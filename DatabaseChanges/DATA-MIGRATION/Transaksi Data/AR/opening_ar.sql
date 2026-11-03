/*
===============================================================================
JSYS ERP - OPENING AR (SALDO AWAL PIUTANG CUSTOMER)
STAGING IMPORT -> VALIDASI -> PROSES -> sc_trx.transaction_dt

Alur menu (I.T.D.6 - UPLOAD SALDO AR):
   1. Upload file Template_opening_ar.xlsx (sheet "template_import")
      -> staging sc_im.import_ar_batch + sc_im.import_ar
   2. Validasi  -> sc_im.fn_validate_import_ar(batch, user)
   3. Proses    -> sc_im.fn_process_import_ar(batch, counter_coa, idbranch,
                                              cabang, user)

Catatan:
- Tidak menghapus / recreate tabel transaksi existing.
- Output transaksi memakai journal_type OPENAR (harus ada di
  sc_mst.journal_type dengan accounting_effect = 'YES').
- Perkiraan dokumen per baris = kode_prk dari file; perkiraan lawan =
  counter_idcoa dari form Proses. Agar routing ini benar, jalankan juga
  sql/opening_ar_accounting_fix.sql.
- Nilai negatif dinormalisasi menjadi ABS(nilai); arah mengikuti D/K.
  Template AR menyimpan nilai asli (DK=D positif, DK=K negatif), sehingga
  posting memakai ABS(nilai) + debet_kredit = kolom dk.
- Baris ERROR tidak memblokir seluruh batch: baris READY tetap diproses dan
  batch berstatus PARTIAL.
- Baris tanpa tanggal saldo memakai Tanggal Saldo dari form upload
  (kolom batch), sehingga file sumber tidak wajib mengisi tanggal.

PERBEDAAN DENGAN FILE DASAR (opening_ar.sql versi awal)
-------------------------------------------------------
1. import_ar.tanggal_saldo boleh NULL + fallback ke tanggal batch.
2. Status batch PARTIAL: satu baris ERROR tidak lagi membatalkan proses.
3. Warning baris (kolom warning_message): customer tidak ada di master,
   mata uang kosong (dipakai IDR), kurs 0 (dipakai 1), umur hari hasil
   hitungan tanggal.
4. Nama customer / nama perkiraan / nama salesman dilengkapi dari master
   bila kolom file kosong.
5. Nomor dokumen per baris (OAR<batch>-<row>) + ref_docno = nomor batch.
   Ini menjaga biaya trigger sc_trx.fn_transaction_dt_sync_hd() tetap O(n);
   satu docno untuk 600+ baris membuat posting menjadi O(n^2).
6. Validasi kode customer memakai master sc_mst.customer (kdcustomer).

URUTAN DEPLOY:
  1) sql/opening_ar.sql                    (file ini)
  2) sql/opening_ar_accounting_fix.sql     (routing accounting OPENAR)
  3) sql/trx_hd_recalculate_perf.sql       (opsional, disarankan)
  4) sql/menu_upload_saldo_awal_ar.sql     (menu I.T.D.6)

Sifat: idempoten (CREATE IF NOT EXISTS / CREATE OR REPLACE / ALTER IF EXISTS).
===============================================================================
*/

BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_im;

-- ---------------------------------------------------------------------------
-- 1. HEADER BATCH IMPORT
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sc_im.import_ar_batch
(
    id                  BIGSERIAL PRIMARY KEY,
    batch_no            VARCHAR(40) NOT NULL UNIQUE,
    filename            VARCHAR(250) DEFAULT '',
    tanggal_saldo       DATE,
    counter_idcoa       VARCHAR(20),
    idbranch            VARCHAR(20) DEFAULT '',
    cabang              VARCHAR(30) DEFAULT '',
    status              VARCHAR(20) NOT NULL DEFAULT 'STAGED',
    total_rows          INTEGER NOT NULL DEFAULT 0,
    ready_rows          INTEGER NOT NULL DEFAULT 0,
    error_rows          INTEGER NOT NULL DEFAULT 0,
    processed_rows      INTEGER NOT NULL DEFAULT 0,
    createdby           VARCHAR(50),
    createddate         TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    validatedby         VARCHAR(50),
    validateddate       TIMESTAMP WITHOUT TIME ZONE,
    processedby         VARCHAR(50),
    processeddate       TIMESTAMP WITHOUT TIME ZONE,
    notes               TEXT
);

-- Upgrade untuk instalasi lama: status PARTIAL (sebagian baris error).
ALTER TABLE sc_im.import_ar_batch DROP CONSTRAINT IF EXISTS ck_import_ar_batch_status;

ALTER TABLE sc_im.import_ar_batch
    ADD CONSTRAINT ck_import_ar_batch_status
    CHECK (status IN ('STAGED','VALIDATED','VALIDATION_ERROR',
                      'PROCESSING','PROCESSED','PARTIAL'));

-- ---------------------------------------------------------------------------
-- 2. DETAIL STAGING
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sc_im.import_ar
(
    id                      BIGSERIAL PRIMARY KEY,
    batch_id                BIGINT NOT NULL
                                REFERENCES sc_im.import_ar_batch(id)
                                ON DELETE CASCADE,
    row_no                  INTEGER NOT NULL,

    tanggal_saldo           DATE,
    no_jurnal               VARCHAR(50) NOT NULL DEFAULT '',
    kode_customer           VARCHAR(30) NOT NULL DEFAULT '',
    nama_customer           VARCHAR(250) DEFAULT '',
    kode_prk                VARCHAR(20) NOT NULL DEFAULT '',
    nama_prk                VARCHAR(250) DEFAULT '',
    tanggal_dokumen         DATE,
    tanggal_jatuh_tempo     DATE,
    dk                      CHAR(1) DEFAULT '',
    keterangan              TEXT DEFAULT '',
    mata_uang               VARCHAR(10) DEFAULT '',
    kurs                    NUMERIC(18,6) DEFAULT 1,
    nilai                   NUMERIC(18,2) DEFAULT 0,
    nilai_belum_jt          NUMERIC(18,2) DEFAULT 0,
    nilai_jt_1              NUMERIC(18,2) DEFAULT 0,
    nilai_jt_2              NUMERIC(18,2) DEFAULT 0,
    nilai_jt_3              NUMERIC(18,2) DEFAULT 0,
    nilai_jt_4              NUMERIC(18,2) DEFAULT 0,
    umur_hari               INTEGER DEFAULT 0,
    kode_job                VARCHAR(30) DEFAULT '',
    nama_job                VARCHAR(250) DEFAULT '',
    alamat_customer         TEXT DEFAULT '',
    kota_customer           VARCHAR(150) DEFAULT '',
    kode_salesman           VARCHAR(30) DEFAULT '',
    nama_salesman           VARCHAR(250) DEFAULT '',

    row_status              VARCHAR(20) NOT NULL DEFAULT 'STAGED',
    error_message           TEXT DEFAULT '',
    warning_message         TEXT DEFAULT '',
    transaction_uniqueid    TEXT,
    transaction_id          BIGINT,
    createddate             TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    processeddate           TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT uq_import_ar_batch_row UNIQUE (batch_id, row_no)
);

-- Upgrade untuk instalasi lama.
ALTER TABLE sc_im.import_ar ADD COLUMN IF NOT EXISTS warning_message TEXT DEFAULT '';
ALTER TABLE sc_im.import_ar ADD COLUMN IF NOT EXISTS kode_salesman VARCHAR(30) DEFAULT '';
ALTER TABLE sc_im.import_ar ADD COLUMN IF NOT EXISTS nama_salesman VARCHAR(250) DEFAULT '';

-- tanggal_saldo boleh kosong di staging: divalidasi/diisi dari tanggal batch.
ALTER TABLE sc_im.import_ar ALTER COLUMN tanggal_saldo DROP NOT NULL;
ALTER TABLE sc_im.import_ar ALTER COLUMN tanggal_saldo DROP DEFAULT;

ALTER TABLE sc_im.import_ar DROP CONSTRAINT IF EXISTS ck_import_ar_row_status;

ALTER TABLE sc_im.import_ar
    ADD CONSTRAINT ck_import_ar_row_status
    CHECK (row_status IN ('STAGED','READY','ERROR','PROCESSED'));

CREATE INDEX IF NOT EXISTS idx_import_ar_batch_status
    ON sc_im.import_ar(batch_id, row_status);

CREATE INDEX IF NOT EXISTS idx_import_ar_customer
    ON sc_im.import_ar(kode_customer);

CREATE INDEX IF NOT EXISTS idx_import_ar_docno
    ON sc_im.import_ar(no_jurnal);



-- ---------------------------------------------------------------------------
-- 3. VALIDASI DETAIL STAGING
--    - Baris ERROR tidak memblokir proses (lihat PARTIAL pada fn_process).
--    - Customer tidak ditemukan di master = WARNING, bukan ERROR.
--    - Mata uang kosong -> IDR, kurs kosong/0 -> 1 (dicatat sebagai warning).
-- ---------------------------------------------------------------------------
DROP FUNCTION IF EXISTS sc_im.fn_validate_import_ar(BIGINT, VARCHAR);

CREATE OR REPLACE FUNCTION sc_im.fn_validate_import_ar
(
    p_batch_id BIGINT,
    p_user     VARCHAR(50) DEFAULT 'SYSTEM'
)
RETURNS TABLE
(
    total_rows    INTEGER,
    ready_rows    INTEGER,
    error_rows    INTEGER,
    warning_rows  INTEGER
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_tanggal_saldo DATE;
BEGIN
    SELECT b.tanggal_saldo
    INTO v_tanggal_saldo
    FROM sc_im.import_ar_batch b
    WHERE b.id = p_batch_id;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Batch Opening AR % tidak ditemukan', p_batch_id;
    END IF;

    -- 3.1 Tanggal saldo baris mengikuti tanggal batch bila baris dikosongkan.
    UPDATE sc_im.import_ar a
    SET tanggal_saldo = v_tanggal_saldo
    WHERE a.batch_id = p_batch_id
      AND a.tanggal_saldo IS NULL
      AND a.row_status <> 'PROCESSED';

    -- 3.2 Lengkapi nama customer dari master (bila kosong).
    UPDATE sc_im.import_ar a
    SET nama_customer = COALESCE(
            NULLIF(BTRIM(a.nama_customer), ''),
            (SELECT BTRIM(c.nmcustomer::TEXT)
             FROM sc_mst.customer c
             WHERE BTRIM(c.kdcustomer::TEXT) = BTRIM(a.kode_customer)
             LIMIT 1))
    WHERE a.batch_id = p_batch_id
      AND a.row_status <> 'PROCESSED'
      AND NULLIF(BTRIM(a.nama_customer), '') IS NULL;

    -- 3.3 Lengkapi nama perkiraan dari master COA (bila kosong).
    UPDATE sc_im.import_ar a
    SET nama_prk = COALESCE(
            NULLIF(BTRIM(a.nama_prk), ''),
            (SELECT BTRIM(c.nmcoa::TEXT)
             FROM sc_mst.coa c
             WHERE BTRIM(c.idcoa::TEXT) = BTRIM(a.kode_prk)
             LIMIT 1))
    WHERE a.batch_id = p_batch_id
      AND a.row_status <> 'PROCESSED'
      AND NULLIF(BTRIM(a.nama_prk), '') IS NULL;

    -- 3.4 Lengkapi nama salesman dari master (bila kode diisi & nama kosong).
    UPDATE sc_im.import_ar a
    SET nama_salesman = COALESCE(
            NULLIF(BTRIM(a.nama_salesman), ''),
            (SELECT BTRIM(s.nmsalesman::TEXT)
             FROM sc_mst.salesman s
             WHERE BTRIM(s.kdsalesman::TEXT) = BTRIM(a.kode_salesman)
             LIMIT 1))
    WHERE a.batch_id = p_batch_id
      AND a.row_status <> 'PROCESSED'
      AND NULLIF(BTRIM(a.kode_salesman), '') IS NOT NULL
      AND NULLIF(BTRIM(a.nama_salesman), '') IS NULL;


    -- 3.5 Validasi utama + warning.
    UPDATE sc_im.import_ar x
    SET row_status        = CASE WHEN e.err = '' THEN 'READY' ELSE 'ERROR' END,
        error_message     = e.err,
        warning_message   = e.warn,
        mata_uang         = e.mata_uang,
        kurs              = e.kurs,
        umur_hari         = e.umur_hari,
        transaction_uniqueid = NULL
    FROM
    (
        SELECT
            a.id,
            COALESCE(NULLIF(BTRIM(a.mata_uang), ''), 'IDR') AS mata_uang,
            CASE
                WHEN a.kurs IS NULL OR a.kurs = 0 THEN 1
                ELSE a.kurs
            END AS kurs,
            CASE
                WHEN COALESCE(a.umur_hari, 0) <> 0 THEN a.umur_hari
                WHEN a.tanggal_saldo IS NOT NULL
                 AND a.tanggal_jatuh_tempo IS NOT NULL
                    THEN (a.tanggal_saldo - a.tanggal_jatuh_tempo)
                ELSE 0
            END AS umur_hari,
            CONCAT_WS('; ',
                CASE WHEN a.tanggal_saldo IS NULL
                     THEN 'Tanggal saldo wajib diisi' END,
                CASE WHEN NULLIF(BTRIM(a.no_jurnal), '') IS NULL
                     THEN 'No.Jurnal wajib diisi' END,
                CASE WHEN NULLIF(BTRIM(a.kode_customer), '') IS NULL
                     THEN 'Kode customer wajib diisi' END,
                CASE WHEN NULLIF(BTRIM(a.kode_prk), '') IS NULL
                     THEN 'Kode perkiraan wajib diisi' END,
                CASE WHEN a.tanggal_dokumen IS NULL
                     THEN 'Tanggal dokumen wajib diisi' END,
                CASE WHEN UPPER(BTRIM(COALESCE(a.dk, ''))) NOT IN ('D', 'K')
                     THEN 'DK harus D atau K' END,
                CASE WHEN LENGTH(BTRIM(COALESCE(a.mata_uang, 'IDR'))) > 3
                     THEN 'Kode mata uang maksimal 3 karakter' END,
                CASE WHEN COALESCE(a.kurs, 0) < 0
                     THEN 'Kurs tidak boleh negatif' END,
                CASE WHEN ROUND(ABS(COALESCE(a.nilai, 0)), 2) = 0
                     THEN 'Nilai tidak boleh 0 atau kosong' END,
                CASE WHEN NOT EXISTS
                     (
                         SELECT 1
                         FROM sc_mst.coa c
                         WHERE btrim(c.idcoa::TEXT) = btrim(a.kode_prk)
                     )
                     THEN 'COA ' || COALESCE(a.kode_prk, '') ||
                          ' tidak ditemukan di sc_mst.coa' END
            ) AS err,
            CONCAT_WS('; ',
                CASE WHEN NULLIF(BTRIM(a.mata_uang), '') IS NULL
                     THEN 'Mata uang kosong, dipakai IDR' END,
                CASE WHEN a.kurs IS NULL OR a.kurs = 0
                     THEN 'Kurs kosong/0, dipakai 1' END,
                CASE WHEN NULLIF(BTRIM(a.kode_customer), '') IS NOT NULL
                      AND NOT EXISTS
                     (
                         SELECT 1 FROM sc_mst.customer c
                         WHERE btrim(c.kdcustomer::TEXT) = btrim(a.kode_customer)
                     )
                     THEN 'Customer ' || btrim(a.kode_customer) ||
                          ' belum ada di master sc_mst.customer' END,
                CASE WHEN COALESCE(a.umur_hari, 0) = 0
                      AND a.tanggal_saldo IS NOT NULL
                      AND a.tanggal_jatuh_tempo IS NOT NULL
                     THEN 'Umur hari dihitung dari tanggal saldo - jatuh tempo' END
            ) AS warn
        FROM sc_im.import_ar a
        WHERE a.batch_id = p_batch_id
          AND a.row_status <> 'PROCESSED'
    ) e
    WHERE x.id = e.id;


    -- 3.6 Ringkasan batch.
    UPDATE sc_im.import_ar_batch b
    SET total_rows     = s.total_rows,
        ready_rows     = s.ready_rows,
        error_rows     = s.error_rows,
        processed_rows = s.processed_rows,
        status = CASE
                    WHEN s.total_rows = 0                THEN 'STAGED'
                    WHEN s.total_rows = s.processed_rows THEN 'PROCESSED'
                    WHEN s.error_rows = 0                THEN 'VALIDATED'
                    WHEN s.processed_rows > 0            THEN 'PARTIAL'
                    ELSE 'VALIDATION_ERROR'
                 END,
        validatedby   = p_user,
        validateddate = CURRENT_TIMESTAMP
    FROM
    (
        SELECT
            COUNT(*)::INTEGER                                         AS total_rows,
            COUNT(*) FILTER (WHERE row_status = 'READY')::INTEGER      AS ready_rows,
            COUNT(*) FILTER (WHERE row_status = 'ERROR')::INTEGER      AS error_rows,
            COUNT(*) FILTER (WHERE row_status = 'PROCESSED')::INTEGER  AS processed_rows
        FROM sc_im.import_ar
        WHERE batch_id = p_batch_id
    ) s
    WHERE b.id = p_batch_id;

    RETURN QUERY
    SELECT b.total_rows,
           b.ready_rows,
           b.error_rows,
           (SELECT COUNT(*)::INTEGER
            FROM sc_im.import_ar a
            WHERE a.batch_id = p_batch_id
              AND NULLIF(BTRIM(a.warning_message), '') IS NOT NULL)
    FROM sc_im.import_ar_batch b
    WHERE b.id = p_batch_id;
END;
$$;



-- ---------------------------------------------------------------------------
-- 4. PROSES STAGING KE TRANSACTION_DT
--    Baris READY diproses; baris ERROR dibiarkan (batch berstatus PARTIAL).
-- ---------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sc_im.fn_process_import_ar
(
    p_batch_id       BIGINT,
    p_counter_idcoa  VARCHAR(20),
    p_idbranch       VARCHAR(20) DEFAULT '',
    p_cabang         VARCHAR(30) DEFAULT '',
    p_user           VARCHAR(50) DEFAULT 'SYSTEM'
)
RETURNS TABLE
(
    batch_id         BIGINT,
    status           VARCHAR(20),
    total_rows       INTEGER,
    ready_rows       INTEGER,
    error_rows       INTEGER,
    processed_rows   INTEGER,
    message          TEXT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_total       INTEGER := 0;
    v_ready       INTEGER := 0;
    v_error       INTEGER := 0;
    v_processed   INTEGER := 0;
    v_counter     VARCHAR(20);
    v_idbranch    VARCHAR(20);
    v_cabang      VARCHAR(30);
    v_docno       VARCHAR(20);
    v_tanggal     DATE;
BEGIN
    SELECT COUNT(*)::INTEGER
    INTO v_total
    FROM sc_im.import_ar
    WHERE import_ar.batch_id = p_batch_id;

    IF v_total = 0 THEN
        RAISE EXCEPTION 'Batch % tidak memiliki detail staging', p_batch_id;
    END IF;

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.journal_type
        WHERE BTRIM(journal_type::TEXT) = 'OPENAR'
          AND accounting_effect = 'YES'
    ) THEN
        RAISE EXCEPTION
            'Journal type OPENAR belum tersedia/aktif pada sc_mst.journal_type';
    END IF;

    -- 4.1 Counter COA (perkiraan lawan) wajib & harus ada di master COA.
    v_counter := NULLIF(BTRIM(COALESCE(p_counter_idcoa, '')), '');

    IF v_counter IS NULL THEN
        UPDATE sc_im.import_ar_batch
        SET status = 'VALIDATION_ERROR',
            notes = 'Proses dibatalkan: Counter COA wajib diisi.'
        WHERE id = p_batch_id;

        RETURN QUERY
        SELECT p_batch_id, 'VALIDATION_ERROR'::VARCHAR(20),
               v_total, 0, v_total, 0,
               'Counter COA wajib diisi pada form Proses.'::TEXT;
        RETURN;
    END IF;

    IF NOT EXISTS
    (
        SELECT 1 FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = v_counter
    ) THEN
        UPDATE sc_im.import_ar_batch
        SET status = 'VALIDATION_ERROR',
            counter_idcoa = v_counter,
            notes = 'Proses dibatalkan: Counter COA tidak ditemukan.'
        WHERE id = p_batch_id;

        RETURN QUERY
        SELECT p_batch_id, 'VALIDATION_ERROR'::VARCHAR(20),
               v_total, 0, v_total, 0,
               ('Counter COA ' || v_counter || ' tidak ditemukan di sc_mst.coa.')::TEXT;
        RETURN;
    END IF;

    -- 4.2 Cabang: pakai input form; bila kosong pakai cabang default.
    v_idbranch := NULLIF(BTRIM(COALESCE(p_idbranch, '')), '');
    v_cabang   := NULLIF(BTRIM(COALESCE(p_cabang, '')), '');

    IF v_idbranch IS NULL OR v_cabang IS NULL THEN
        SELECT BTRIM(b.branch::TEXT), BTRIM(b.branchname::TEXT)
        INTO v_idbranch, v_cabang
        FROM sc_mst.branch b
        WHERE BTRIM(COALESCE(b.cdefault::TEXT, '')) = 'YES'
        LIMIT 1;
    END IF;

    v_idbranch := COALESCE(v_idbranch, '');
    v_cabang   := COALESCE(v_cabang, '');

    -- 4.3 Tanggal saldo batch (fallback: tanggal dokumen paling awal).
    SELECT b.tanggal_saldo
    INTO v_tanggal
    FROM sc_im.import_ar_batch b
    WHERE b.id = p_batch_id;

    IF v_tanggal IS NULL THEN
        SELECT MIN(a.tanggal_dokumen)
        INTO v_tanggal
        FROM sc_im.import_ar a
        WHERE a.batch_id = p_batch_id;
    END IF;

    UPDATE sc_im.import_ar_batch
    SET counter_idcoa = v_counter,
        idbranch      = v_idbranch,
        cabang        = v_cabang,
        tanggal_saldo = COALESCE(tanggal_saldo, v_tanggal)
    WHERE id = p_batch_id;

    -- 4.4 Validasi ulang staging sebelum posting.
    SELECT v.total_rows, v.ready_rows, v.error_rows
    INTO v_total, v_ready, v_error
    FROM sc_im.fn_validate_import_ar(p_batch_id, p_user) v;

    IF v_ready = 0 THEN
        UPDATE sc_im.import_ar_batch
        SET status = 'VALIDATION_ERROR',
            total_rows = v_total,
            ready_rows = 0,
            error_rows = v_error,
            notes = 'Tidak ada baris READY untuk diproses.'
        WHERE id = p_batch_id;

        RETURN QUERY
        SELECT p_batch_id, 'VALIDATION_ERROR'::VARCHAR(20),
               v_total, 0, v_error, 0,
               'Tidak ada baris READY. Perbaiki baris ERROR lalu validasi ulang.'::TEXT;
        RETURN;
    END IF;

    -- 4.5 Nomor dokumen: prefix batch + nomor baris.
    --     Satu baris staging = satu dokumen (OAR<batch>-<row>).
    --     Catatan performa: trigger sc_trx.fn_transaction_dt_sync_hd()
    --     mengagregasi ulang seluruh baris dengan docno yang sama untuk SETIAP
    --     baris. Bila satu batch memakai satu docno, biaya posting menjadi
    --     O(n^2). Dengan docno per baris biaya menjadi O(n).
    --     Referensi batch tetap tersimpan pada ref_docno = v_docno.
    v_docno := 'OAR' || LPAD(p_batch_id::TEXT, 6, '0');

    UPDATE sc_im.import_ar_batch
    SET status = 'PROCESSING',
        notes = NULL,
        processedby = p_user
    WHERE id = p_batch_id;



    /*
      uniqueid stabil berdasarkan batch + nomor baris + data sumber.
      Upload ulang / klik Proses dua kali tidak membuat transaksi duplikat
      (dilindungi ON CONFLICT (uniqueid) DO NOTHING + UNIQUE index).
    */
    INSERT INTO sc_trx.transaction_dt
    (
        uniqueid,
        source_uniqueid,
        docno,
        doctype,
        journal_type,
        line_no,
        docdate,
        idbranch,
        cabang,
        type_in_out,
        ref_docno,
        ref_doctype,
        source_table,
        source_id,
        source_line_id,
        kdcustomer,
        ncustomer,
        qty,
        bruto,
        nilai,
        dpp,
        total,
        idtax,
        isinclusive,
        currcode,
        kurs,
        idcoa,
        counter_idcoa,
        debet_kredit,
        debet,
        kredit,
        keterangan,
        createdby
    )
    SELECT
        'OPENAR-' || MD5(
            a.batch_id::TEXT || '|' || a.row_no::TEXT || '|' ||
            COALESCE(a.no_jurnal, '') || '|' ||
            COALESCE(a.kode_customer, '') || '|' ||
            COALESCE(a.kode_prk, '') || '|' ||
            COALESCE(a.tanggal_saldo::TEXT, '') || '|' ||
            COALESCE(a.nilai::TEXT, '')
        ),
        'OPENAR-' || MD5(
            a.batch_id::TEXT || '|' || a.row_no::TEXT || '|' ||
            COALESCE(a.no_jurnal, '') || '|' ||
            COALESCE(a.kode_customer, '') || '|' ||
            COALESCE(a.kode_prk, '') || '|' ||
            COALESCE(a.tanggal_saldo::TEXT, '') || '|' ||
            COALESCE(a.nilai::TEXT, '')
        ),
        v_docno || '-' || LPAD(a.row_no::TEXT, 6, '0'),   -- docno per baris
        'OPENAR',
        'OPENAR',
        a.row_no,
        COALESCE(a.tanggal_saldo, v_tanggal),      -- docdate = tanggal saldo
        v_idbranch,
        v_cabang,
        'IN',
        v_docno,                                   -- ref_docno = nomor batch
        'OPENAR',                                  -- ref_doctype konstan per batch
        'sc_im.import_ar',
        a.batch_id,
        a.id,
        BTRIM(a.kode_customer),
        COALESCE(a.nama_customer, ''),
        0,
        ROUND(ABS(COALESCE(a.nilai, 0)), 2),
        ROUND(ABS(COALESCE(a.nilai, 0)), 2),
        ROUND(ABS(COALESCE(a.nilai, 0)), 2),
        ROUND(ABS(COALESCE(a.nilai, 0)), 2),
        'NON',
        'NO',
        COALESCE(NULLIF(BTRIM(a.mata_uang), ''), 'IDR'),
        COALESCE(NULLIF(a.kurs, 0), 1),
        BTRIM(a.kode_prk),                         -- perkiraan dokumen (dari file)
        v_counter,                                 -- perkiraan lawan (dari form)
        UPPER(BTRIM(a.dk)),
        CASE WHEN UPPER(BTRIM(a.dk)) = 'D'
             THEN ROUND(ABS(COALESCE(a.nilai, 0)), 2) ELSE 0 END,
        CASE WHEN UPPER(BTRIM(a.dk)) = 'K'
             THEN ROUND(ABS(COALESCE(a.nilai, 0)), 2) ELSE 0 END,
        CONCAT_WS(' | ',
            'No. Jurnal: ' || COALESCE(BTRIM(a.no_jurnal), ''),
            NULLIF(BTRIM(a.keterangan), ''),
            'Saldo awal AR',
            'Tgl dokumen: ' || COALESCE(a.tanggal_dokumen::TEXT, ''),
            'Tgl jatuh tempo: ' || COALESCE(a.tanggal_jatuh_tempo::TEXT, ''),
            'Umur: ' || COALESCE(a.umur_hari, 0)::TEXT || ' hari',
            'Belum JT: ' || COALESCE(a.nilai_belum_jt, 0)::TEXT,
            'JT1: ' || COALESCE(a.nilai_jt_1, 0)::TEXT,
            'JT2: ' || COALESCE(a.nilai_jt_2, 0)::TEXT,
            'JT3: ' || COALESCE(a.nilai_jt_3, 0)::TEXT,
            'JT4: ' || COALESCE(a.nilai_jt_4, 0)::TEXT,
            CASE WHEN NULLIF(BTRIM(a.kode_salesman), '') IS NOT NULL
                 THEN 'Salesman: ' || BTRIM(a.kode_salesman) ||
                      CASE WHEN NULLIF(BTRIM(a.nama_salesman), '') IS NOT NULL
                           THEN ' - ' || BTRIM(a.nama_salesman) ELSE '' END
            END
        ),
        p_user
    FROM sc_im.import_ar a
    WHERE a.batch_id = p_batch_id
      AND a.row_status = 'READY'
    ON CONFLICT (uniqueid) DO NOTHING;



    -- 4.6 Sinkronkan status staging dengan transaksi yang sudah terbentuk.
    UPDATE sc_im.import_ar a
    SET row_status           = 'PROCESSED',
        transaction_uniqueid = t.uniqueid,
        transaction_id       = t.id,
        processeddate        = CURRENT_TIMESTAMP,
        error_message        = ''
    FROM sc_trx.transaction_dt t
    WHERE a.batch_id = p_batch_id
      AND a.row_status = 'READY'
      AND t.source_table = 'sc_im.import_ar'
      AND t.source_id = a.batch_id
      AND t.source_line_id = a.id;

    SELECT COUNT(*)::INTEGER
    INTO v_processed
    FROM sc_im.import_ar
    WHERE import_ar.batch_id = p_batch_id
      AND row_status = 'PROCESSED';

    SELECT COUNT(*)::INTEGER
    INTO v_error
    FROM sc_im.import_ar
    WHERE import_ar.batch_id = p_batch_id
      AND row_status = 'ERROR';

    UPDATE sc_im.import_ar_batch
    SET status = CASE
                    WHEN v_processed = v_total THEN 'PROCESSED'
                    WHEN v_processed > 0       THEN 'PARTIAL'
                    ELSE 'VALIDATION_ERROR'
                 END,
        total_rows     = v_total,
        ready_rows     = GREATEST(v_total - v_processed - v_error, 0),
        error_rows     = v_error,
        processed_rows = v_processed,
        processedby    = p_user,
        processeddate  = CURRENT_TIMESTAMP
    WHERE id = p_batch_id;

    RETURN QUERY
    SELECT p_batch_id,
           CASE
               WHEN v_processed = v_total THEN 'PROCESSED'
               WHEN v_processed > 0       THEN 'PARTIAL'
               ELSE 'VALIDATION_ERROR'
           END::VARCHAR(20),
           v_total,
           GREATEST(v_total - v_processed - v_error, 0),
           v_error,
           v_processed,
           CASE
               WHEN v_processed = v_total
                   THEN 'Opening AR berhasil masuk ke sc_trx.transaction_dt.'
               WHEN v_processed > 0
                   THEN 'Sebagian transaksi terbentuk (' || v_processed ||
                        ' dari ' || v_total || ' baris). ' ||
                        v_error || ' baris masih ERROR dan belum diposting.'
               ELSE 'Belum ada transaksi terbentuk. Periksa status staging.'
           END::TEXT;
END;
$$;


-- ---------------------------------------------------------------------------
-- 5. VIEW MONITORING BATCH
-- ---------------------------------------------------------------------------
CREATE OR REPLACE VIEW sc_im.v_import_ar_monitor AS
SELECT
    b.id AS batch_id,
    b.batch_no,
    b.filename,
    b.tanggal_saldo,
    b.status,
    COUNT(a.id)::INTEGER AS total_rows,
    COUNT(a.id) FILTER (WHERE a.row_status = 'READY')::INTEGER AS ready_rows,
    COUNT(a.id) FILTER (WHERE a.row_status = 'ERROR')::INTEGER AS error_rows,
    COUNT(a.id) FILTER (WHERE a.row_status = 'PROCESSED')::INTEGER AS processed_rows,
    b.counter_idcoa,
    b.createdby,
    b.createddate,
    b.processedby,
    b.processeddate,
    COUNT(a.id) FILTER (WHERE NULLIF(BTRIM(a.warning_message), '') IS NOT NULL)::INTEGER AS warning_rows,
    COALESCE(SUM(ABS(a.nilai)), 0)::NUMERIC(18,2) AS nilai_total,
    b.idbranch,
    b.cabang,
    b.notes
FROM sc_im.import_ar_batch b
LEFT JOIN sc_im.import_ar a ON a.batch_id = b.id
GROUP BY
    b.id, b.batch_no, b.filename, b.tanggal_saldo, b.status,
    b.counter_idcoa, b.createdby, b.createddate,
    b.processedby, b.processeddate, b.idbranch, b.cabang, b.notes;


-- ---------------------------------------------------------------------------
-- 6. VERIFIKASI
-- ---------------------------------------------------------------------------
DO $$
DECLARE
    v_row RECORD;
BEGIN
    RAISE NOTICE '--- Staging Opening AR ---';
    FOR v_row IN
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = 'sc_im'
          AND table_name IN ('import_ar_batch', 'import_ar')
        ORDER BY table_name
    LOOP
        RAISE NOTICE 'tabel sc_im.% OK', v_row.table_name;
    END LOOP;

    RAISE NOTICE 'fungsi validate : %',
        CASE WHEN to_regprocedure('sc_im.fn_validate_import_ar(bigint,character varying)') IS NOT NULL
             THEN 'OK' ELSE 'BELUM' END;
    RAISE NOTICE 'fungsi process  : %',
        CASE WHEN to_regprocedure('sc_im.fn_process_import_ar(bigint,character varying,character varying,character varying,character varying)') IS NOT NULL
             THEN 'OK' ELSE 'BELUM' END;

    RAISE NOTICE 'journal type OPENAR : %',
        COALESCE((SELECT accounting_effect FROM sc_mst.journal_type
                  WHERE BTRIM(journal_type::TEXT) = 'OPENAR' LIMIT 1), 'TIDAK ADA');

    RAISE NOTICE 'kolom customer pada sc_mst.customer : %',
        CASE WHEN EXISTS (SELECT 1 FROM information_schema.columns
                          WHERE table_schema = 'sc_mst'
                            AND table_name = 'customer'
                            AND column_name = 'kdcustomer')
             THEN 'OK' ELSE 'BELUM' END;
END;
$$;

COMMIT;


/*
===============================================================================
CONTOH INTEGRASI APLIKASI (menu I.T.D.6)

1) Buat batch saat upload:
   INSERT INTO sc_im.import_ar_batch
       (batch_no, filename, tanggal_saldo, createdby)
   VALUES
       ('OPENAR-20261031-001', 'Template_opening_ar.xlsx',
        DATE '2026-10-31', 'USER')
   RETURNING id;

2) Bulk insert seluruh baris Excel ke sc_im.import_ar.
   batch_id = id hasil langkah 1.
   row_no   = nomor baris data mulai 1.
   Upload hanya sheet template_import.

3) Preview/validasi:
   SELECT * FROM sc_im.fn_validate_import_ar(<batch_id>, '<user>');

4) Klik Process:
   SELECT *
   FROM sc_im.fn_process_import_ar(
       <batch_id>,
       '<counter_idcoa>',
       '<idbranch>',
       '<cabang>',
       '<user>'
   );

5) Monitor:
   SELECT * FROM sc_im.v_import_ar_monitor
   WHERE batch_id = <batch_id>;

6) Detail error / warning:
   SELECT row_no, no_jurnal, kode_customer, kode_prk, dk, mata_uang,
          row_status, error_message, warning_message
   FROM sc_im.import_ar
   WHERE batch_id = <batch_id>
     AND (row_status = 'ERROR'
          OR NULLIF(BTRIM(warning_message), '') IS NOT NULL)
   ORDER BY row_no;

7) Cek transaksi yang terbentuk:
   SELECT docno, line_no, debet_kredit, idcoa, counter_idcoa, debet, kredit
   FROM sc_trx.transaction_dt
   WHERE journal_type = 'OPENAR'
     AND ref_docno = 'OAR' || LPAD(<batch_id>::TEXT, 6, '0')
   ORDER BY docno, line_no;

CATATAN ACCOUNTING
------------------
Jurnal per baris dibentuk oleh sc_trx.fn_post_accounting_transaction():
   dk = D : Dr  kode_prk        / Cr counter_idcoa
   dk = K : Dr  counter_idcoa   / Cr kode_prk
Mapping tersebut hanya benar bila sql/opening_ar_accounting_fix.sql sudah
dijalankan (role ACCOUNT + COUNTER_ACCOUNT). Tanpa file itu, kedua sisi
jurnal memakai COA konfigurasi umum sehingga Dr = Cr pada COA yang sama.
===============================================================================
*/

