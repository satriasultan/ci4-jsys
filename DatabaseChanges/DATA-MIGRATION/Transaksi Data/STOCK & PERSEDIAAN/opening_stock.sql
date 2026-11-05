-- Tabel staging saldo awal persediaan (belum mem-posting ke sc_trx.transaction_dt)
-- PostgreSQL
CREATE SCHEMA IF NOT EXISTS sc_im;

-- Table: sc_im.stock_persediaan_opening

-- Tidak DROP TABLE agar data staging yang sudah di-import tetap aman.

CREATE TABLE IF NOT EXISTS sc_im.stock_persediaan_opening
(
    id bigserial,
    tanggal_saldo date NOT NULL DEFAULT '2026-10-31'::date,
    idbranch character varying(20) COLLATE pg_catalog."default" NOT NULL,
    cabang character varying(50) COLLATE pg_catalog."default",
    kode_gudang character varying(20) COLLATE pg_catalog."default" NOT NULL,
    nama_gudang character varying(150) COLLATE pg_catalog."default",
    kode_barang character varying(50) COLLATE pg_catalog."default" NOT NULL,
    nama_barang character varying(250) COLLATE pg_catalog."default",
    satuan character varying(20) COLLATE pg_catalog."default",
    batch_spec character varying(150) COLLATE pg_catalog."default",
    qty_saldo numeric(20,4) NOT NULL DEFAULT 0,
    nilai_saldo numeric(20,2) NOT NULL DEFAULT 0,
    harga_rata_rata numeric(20,6),
    keterangan text COLLATE pg_catalog."default",
    status_validasi character varying(30) COLLATE pg_catalog."default" NOT NULL DEFAULT 'DRAFT'::character varying,
    validasi_catatan text COLLATE pg_catalog."default",
    uniqueidopening character varying(40) COLLATE pg_catalog."default",
    inputdate timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    inputby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    CONSTRAINT stock_persediaan_opening_pkey PRIMARY KEY (id),
    CONSTRAINT ck_stock_persediaan_opening_qty_nonzero CHECK (qty_saldo <> 0::numeric),
    CONSTRAINT ck_stock_persediaan_opening_status CHECK (status_validasi::text = ANY (ARRAY['DRAFT'::character varying, 'VALID'::character varying, 'POSTED'::character varying, 'CANCELLED'::character varying]::text[]))
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_im.stock_persediaan_opening
    OWNER to postgres;
-- Index: ix_stock_persediaan_opening_barang

-- DROP INDEX IF EXISTS sc_im.ix_stock_persediaan_opening_barang;

CREATE INDEX IF NOT EXISTS ix_stock_persediaan_opening_barang
    ON sc_im.stock_persediaan_opening USING btree
    (kode_barang COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: ix_stock_persediaan_opening_item

-- DROP INDEX IF EXISTS sc_im.ix_stock_persediaan_opening_item;

CREATE INDEX IF NOT EXISTS ix_stock_persediaan_opening_item
    ON sc_im.stock_persediaan_opening USING btree
    (kode_barang COLLATE pg_catalog."default" ASC NULLS LAST, satuan COLLATE pg_catalog."default" ASC NULLS LAST, batch_spec COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: ix_stock_persediaan_opening_period

-- DROP INDEX IF EXISTS sc_im.ix_stock_persediaan_opening_period;

CREATE INDEX IF NOT EXISTS ix_stock_persediaan_opening_period
    ON sc_im.stock_persediaan_opening USING btree
    (tanggal_saldo ASC NULLS LAST, idbranch COLLATE pg_catalog."default" ASC NULLS LAST, kode_gudang COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: ix_stock_persediaan_opening_period_location

-- DROP INDEX IF EXISTS sc_im.ix_stock_persediaan_opening_period_location;

CREATE INDEX IF NOT EXISTS ix_stock_persediaan_opening_period_location
    ON sc_im.stock_persediaan_opening USING btree
    (tanggal_saldo ASC NULLS LAST, idbranch COLLATE pg_catalog."default" ASC NULLS LAST, kode_gudang COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: ix_stock_persediaan_opening_status

-- DROP INDEX IF EXISTS sc_im.ix_stock_persediaan_opening_status;

CREATE INDEX IF NOT EXISTS ix_stock_persediaan_opening_status
    ON sc_im.stock_persediaan_opening USING btree
    (status_validasi COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;
-- Index: ux_stock_persediaan_opening_uniqueid

-- DROP INDEX IF EXISTS sc_im.ux_stock_persediaan_opening_uniqueid;

CREATE UNIQUE INDEX IF NOT EXISTS ux_stock_persediaan_opening_uniqueid
    ON sc_im.stock_persediaan_opening USING btree
    (uniqueidopening COLLATE pg_catalog."default" ASC NULLS LAST)
    TABLESPACE pg_default;


/* =============================================================================
   VALIDATOR STAGING OPENING STOCK
   Fungsi ini dipanggil aplikasi:
       SELECT sc_im.fn_validasi_stock_persediaan_opening();

   Return:
       INTEGER = jumlah baris berstatus VALID setelah validasi.

   Catatan:
   - Validasi dilakukan pada staging sc_im.stock_persediaan_opening.
   - Tidak melakukan posting ke sc_trx.transaction_dt.
   - Baris POSTED/CANCELLED tidak diubah.
   - Validasi master barang/gudang/cabang sengaja tidak mengasumsikan
     nama tabel/kolom master yang belum tersedia pada file ini.
   ============================================================================= */

CREATE OR REPLACE FUNCTION sc_im.fn_validasi_stock_persediaan_opening()
RETURNS INTEGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_valid_count INTEGER := 0;
BEGIN
    IF to_regclass('sc_im.stock_persediaan_opening') IS NULL THEN
        RAISE EXCEPTION 'Tabel sc_im.stock_persediaan_opening tidak ditemukan.';
    END IF;

    /* Pastikan setiap baris mempunyai unique ID untuk proses import/posting. */
    UPDATE sc_im.stock_persediaan_opening
       SET uniqueidopening = md5(
               concat_ws('|',
                   COALESCE(tanggal_saldo::TEXT, ''),
                   COALESCE(idbranch, ''),
                   COALESCE(kode_gudang, ''),
                   COALESCE(kode_barang, ''),
                   COALESCE(satuan, ''),
                   COALESCE(batch_spec, ''),
                   COALESCE(id::TEXT, '')
               )
           )
     WHERE NULLIF(BTRIM(uniqueidopening), '') IS NULL
       AND status_validasi NOT IN ('POSTED', 'CANCELLED');

    /* Hitung validasi tiap baris. Duplikasi didefinisikan berdasarkan
       tanggal + cabang + gudang + barang + satuan + batch. */
    WITH duplicate_rows AS
    (
        SELECT id
          FROM
          (
              SELECT id,
                     COUNT(*) OVER (
                         PARTITION BY tanggal_saldo,
                                      BTRIM(COALESCE(idbranch, '')),
                                      BTRIM(COALESCE(kode_gudang, '')),
                                      BTRIM(COALESCE(kode_barang, '')),
                                      BTRIM(COALESCE(satuan, '')),
                                      BTRIM(COALESCE(batch_spec, ''))
                     ) AS duplicate_count
                FROM sc_im.stock_persediaan_opening
               WHERE status_validasi NOT IN ('POSTED', 'CANCELLED')
          ) d
         WHERE d.duplicate_count > 1
    ),
    validation AS
    (
        SELECT s.id,
               NULLIF(CONCAT_WS('; ',
                   CASE WHEN s.tanggal_saldo IS NULL
                        THEN 'Tanggal saldo wajib diisi' END,
                   CASE WHEN NULLIF(BTRIM(COALESCE(s.idbranch, '')), '') IS NULL
                        THEN 'Cabang/idbranch wajib diisi' END,
                   CASE WHEN NULLIF(BTRIM(COALESCE(s.kode_gudang, '')), '') IS NULL
                        THEN 'Kode gudang wajib diisi' END,
                   CASE WHEN NULLIF(BTRIM(COALESCE(s.kode_barang, '')), '') IS NULL
                        THEN 'Kode barang wajib diisi' END,
                   CASE WHEN NULLIF(BTRIM(COALESCE(s.satuan, '')), '') IS NULL
                        THEN 'Satuan wajib diisi' END,
                   CASE WHEN COALESCE(s.qty_saldo, 0) <= 0
                        THEN 'Qty saldo harus lebih besar dari 0' END,
                   CASE WHEN COALESCE(s.nilai_saldo, 0) <= 0
                        THEN 'Nilai saldo harus lebih besar dari 0' END,
                   CASE WHEN dr.id IS NOT NULL
                        THEN 'Duplikasi kombinasi tanggal, cabang, gudang, barang, satuan, dan batch' END
               ), '') AS catatan
          FROM sc_im.stock_persediaan_opening s
          LEFT JOIN duplicate_rows dr ON dr.id = s.id
         WHERE s.status_validasi NOT IN ('POSTED', 'CANCELLED')
    )
    UPDATE sc_im.stock_persediaan_opening s
       SET status_validasi = CASE
                                 WHEN v.catatan IS NULL THEN 'VALID'
                                 ELSE 'DRAFT'
                             END,
           validasi_catatan = v.catatan,
           harga_rata_rata = CASE
                                 WHEN COALESCE(s.qty_saldo, 0) > 0
                                  AND COALESCE(s.nilai_saldo, 0) > 0
                                  AND COALESCE(s.harga_rata_rata, 0) <= 0
                                 THEN ROUND(s.nilai_saldo / s.qty_saldo, 6)
                                 ELSE s.harga_rata_rata
                             END,
           updatedate = CURRENT_TIMESTAMP
      FROM validation v
     WHERE s.id = v.id;

    SELECT COUNT(*)::INTEGER
      INTO v_valid_count
      FROM sc_im.stock_persediaan_opening
     WHERE status_validasi = 'VALID';

    RETURN v_valid_count;
END;
$$;

COMMENT ON FUNCTION sc_im.fn_validasi_stock_persediaan_opening() IS
'Validasi staging saldo awal persediaan; mengembalikan jumlah baris VALID dan tidak melakukan posting transaksi.';


/*
===============================================================================
JSYS ERP - PATCH OPENING STOCK (OPENST) UNTUK sc_trx.transaction_dt
===============================================================================

Tujuan:
1. Memastikan OPENST menjadi transaksi IN.
2. Memastikan OPENST menghasilkan stock_effect IN dan accounting_effect YES.
3. Validasi atribut wajib sebelum transaksi masuk ke stock ledger.
4. Memastikan COA Inventory dan Adjustment tersedia.
5. Memastikan mapping accounting OPENST menggunakan:
      seq 1 : INVENTORY   D  NILAI
      seq 2 : ADJUSTMENT  K  NILAI
6. Tidak membuat trigger stock baru.
   Trigger stock existing TAHAP 06 tetap memproses transaction_dt -> stkblc.
7. Average cost tetap diproses oleh mekanisme existing TAHAP 07.
8. Aman dijalankan ulang; tidak DROP TABLE / DROP FUNCTION / DELETE data.

Tanggal saldo yang digunakan saat INSERT OPENST:
31-10-2026 (saldo akhir Oktober 2026).

Urutan trigger:
trg_01_transaction_prepare
trg_02_transaction_type
trg_03_opening_stock_validate  <-- patch ini
AFTER trg_transaction_stock   <-- trigger existing TAHAP 06

DEPENDENCY:
- TAHAP 02 : sc_mst.journal_type
- TAHAP 03 : sc_mst.journal_type_coa
- TAHAP 04 : sc_trx.transaction_dt + fn_prepare_transaction_dt
- TAHAP 06 : fn_transaction_stock / stkblc
- TAHAP 07 : stkblc_avgcost
- TAHAP 16 : fn_post_accounting_transaction
===============================================================================
*/

BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_trx;


/* =============================================================================
   1. PASTIKAN MASTER JOURNAL TYPE OPENST
   ============================================================================= */

INSERT INTO sc_mst.journal_type
(
    journal_type,
    journal_name,
    direction,
    module,
    stock_effect,
    accounting_effect,
    asset_effect,
    description
)
VALUES
(
    'OPENST',
    'OPENING STOCK',
    'IN',
    'OPENING',
    'IN',
    'YES',
    'NONE',
    'SALDO AWAL PERSEDIAAN'
)
ON CONFLICT (journal_type)
DO UPDATE SET
    journal_name      = EXCLUDED.journal_name,
    direction         = EXCLUDED.direction,
    module            = EXCLUDED.module,
    stock_effect      = EXCLUDED.stock_effect,
    accounting_effect = EXCLUDED.accounting_effect,
    asset_effect      = EXCLUDED.asset_effect,
    description       = EXCLUDED.description;


/* =============================================================================
   2. PASTIKAN MAPPING ACCOUNTING OPENST
   ============================================================================= */

DO $$
DECLARE
    v_count INTEGER;
BEGIN
    /* INVENTORY - DEBIT */
    UPDATE sc_mst.journal_type_coa
    SET
        account_role = 'INVENTORY',
        idcoa        = '',
        debit_credit = 'D',
        value_source = 'NILAI',
        active       = 'YES',
        description  = 'OPENST - Saldo awal persediaan (debit)',
        updatedby    = 'SYSTEM',
        updateddate  = CURRENT_TIMESTAMP
    WHERE BTRIM(journal_type::TEXT) = 'OPENST'
      AND seq = 1;

    GET DIAGNOSTICS v_count = ROW_COUNT;

    IF v_count = 0 THEN
        INSERT INTO sc_mst.journal_type_coa
        (
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, active, description,
            createdby, createddate
        )
        VALUES
        (
            'OPENST', 'INVENTORY', '', 'D',
            'NILAI', 1, 'YES',
            'OPENST - Saldo awal persediaan (debit)',
            'SYSTEM', CURRENT_TIMESTAMP
        );
    END IF;


    /* ADJUSTMENT - CREDIT */
    UPDATE sc_mst.journal_type_coa
    SET
        account_role = 'ADJUSTMENT',
        idcoa        = '',
        debit_credit = 'K',
        value_source = 'NILAI',
        active       = 'YES',
        description  = 'OPENST - Akun pembuka saldo awal (kredit)',
        updatedby    = 'SYSTEM',
        updateddate  = CURRENT_TIMESTAMP
    WHERE BTRIM(journal_type::TEXT) = 'OPENST'
      AND seq = 2;

    GET DIAGNOSTICS v_count = ROW_COUNT;

    IF v_count = 0 THEN
        INSERT INTO sc_mst.journal_type_coa
        (
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, active, description,
            createdby, createddate
        )
        VALUES
        (
            'OPENST', 'ADJUSTMENT', '', 'K',
            'NILAI', 2, 'YES',
            'OPENST - Akun pembuka saldo awal (kredit)',
            'SYSTEM', CURRENT_TIMESTAMP
        );
    END IF;
END;
$$;


/* =============================================================================
   3. VALIDATOR OPENST
   Trigger ini berjalan SETELAH trg_01_transaction_prepare dan
   trg_02_transaction_type (urutan alfabetis nama trigger).
   ============================================================================= */

CREATE OR REPLACE FUNCTION sc_trx.fn_validate_opening_stock_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_adjustment_coa TEXT;
BEGIN
    /* Tidak mengubah transaksi selain OPENST. */
    IF BTRIM(COALESCE(NEW.journal_type::TEXT, '')) <> 'OPENST' THEN
        RETURN NEW;
    END IF;


    /* -------------------------------------------------------------------------
       A. ROUTING HARUS SESUAI MASTER OPENST
       ------------------------------------------------------------------------- */

    IF NEW.type_in_out <> 'IN' THEN
        RAISE EXCEPTION
            'OPENST wajib type_in_out=IN. uniqueid=%',
            NEW.uniqueid;
    END IF;

    IF COALESCE(BTRIM(NEW.module), '') <> 'OPENING'
       OR COALESCE(BTRIM(NEW.direction), '') <> 'IN'
       OR COALESCE(BTRIM(NEW.stock_effect), '') <> 'IN'
       OR COALESCE(BTRIM(NEW.accounting_effect), '') <> 'YES'
       OR COALESCE(BTRIM(NEW.asset_effect), '') <> 'NONE'
    THEN
        RAISE EXCEPTION
            'Routing OPENST tidak valid. module=%, direction=%, stock_effect=%, accounting_effect=%, asset_effect=%, uniqueid=%',
            NEW.module,
            NEW.direction,
            NEW.stock_effect,
            NEW.accounting_effect,
            NEW.asset_effect,
            NEW.uniqueid;
    END IF;


    /* -------------------------------------------------------------------------
       B. ATRIBUT STOCK WAJIB
       ------------------------------------------------------------------------- */

    IF NULLIF(BTRIM(COALESCE(NEW.idbranch, '')), '') IS NULL THEN
        RAISE EXCEPTION 'OPENST: idbranch wajib diisi. uniqueid=%', NEW.uniqueid;
    END IF;

    IF NULLIF(BTRIM(COALESCE(NEW.warehouse, '')), '') IS NULL THEN
        RAISE EXCEPTION 'OPENST: warehouse/idlocation wajib diisi. uniqueid=%', NEW.uniqueid;
    END IF;

    IF NULLIF(BTRIM(COALESCE(NEW.idbarang, '')), '') IS NULL THEN
        RAISE EXCEPTION 'OPENST: idbarang wajib diisi. uniqueid=%', NEW.uniqueid;
    END IF;

    IF NULLIF(BTRIM(COALESCE(NEW.idunit, '')), '') IS NULL THEN
        RAISE EXCEPTION 'OPENST: idunit wajib diisi. uniqueid=%', NEW.uniqueid;
    END IF;

    IF COALESCE(NEW.qty, 0) <= 0 THEN
        RAISE EXCEPTION
            'OPENST: qty saldo harus lebih besar dari nol. uniqueid=%, qty=%',
            NEW.uniqueid,
            NEW.qty;
    END IF;

    IF COALESCE(NEW.nilai, 0) <= 0 THEN
        RAISE EXCEPTION
            'OPENST: nilai saldo harus lebih besar dari nol agar jurnal pembuka terbentuk. uniqueid=%, nilai=%',
            NEW.uniqueid,
            NEW.nilai;
    END IF;


    /* -------------------------------------------------------------------------
       C. PRIMARY COA INVENTORY SUDAH DIRESOLVE OLEH TAHAP 04
       ------------------------------------------------------------------------- */

    IF NULLIF(BTRIM(COALESCE(NEW.idcoa, '')), '') IS NULL THEN
        RAISE EXCEPTION
            'OPENST: COA persediaan tidak ditemukan. Periksa mbarang.ppersediaan atau konfigurasi_umum.ppersediaan. idbarang=%, uniqueid=%',
            NEW.idbarang,
            NEW.uniqueid;
    END IF;

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = BTRIM(NEW.idcoa::TEXT)
    ) THEN
        RAISE EXCEPTION
            'OPENST: COA persediaan % tidak terdaftar pada sc_mst.coa. uniqueid=%',
            NEW.idcoa,
            NEW.uniqueid;
    END IF;


    /* -------------------------------------------------------------------------
       D. COA LAWAN ADJUSTMENT DIAMBIL DARI konfigurasi_umum.pselisih
       Sesuai resolver TAHAP 16.
       ------------------------------------------------------------------------- */

    SELECT NULLIF(BTRIM(to_jsonb(k)->>'pselisih'), '')
    INTO v_adjustment_coa
    FROM sc_mst.konfigurasi_umum k
    LIMIT 1;

    IF v_adjustment_coa IS NULL THEN
        RAISE EXCEPTION
            'OPENST: konfigurasi_umum.pselisih belum diisi untuk COA ADJUSTMENT.';
    END IF;

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = BTRIM(v_adjustment_coa)
    ) THEN
        RAISE EXCEPTION
            'OPENST: COA ADJUSTMENT % dari konfigurasi_umum.pselisih tidak terdaftar pada sc_mst.coa.',
            v_adjustment_coa;
    END IF;


    /* -------------------------------------------------------------------------
       E. HARGA SATUAN
       Stock ledger TAHAP 06 menggunakan nilai sebagai total cost.
       Harga diselaraskan bila belum diisi oleh proses import.
       ------------------------------------------------------------------------- */

    IF COALESCE(NEW.harga, 0) <= 0 THEN
        NEW.harga := ROUND(NEW.nilai / NULLIF(NEW.qty, 0), 6);
    END IF;

    NEW.bruto := COALESCE(NULLIF(NEW.bruto, 0), NEW.nilai);
    NEW.dpp   := COALESCE(NULLIF(NEW.dpp, 0), NEW.nilai);
    NEW.total := COALESCE(NULLIF(NEW.total, 0), NEW.nilai);

    NEW.idtax   := COALESCE(NULLIF(BTRIM(NEW.idtax), ''), 'NON');
    NEW.currcode := COALESCE(NULLIF(BTRIM(NEW.currcode), ''), 'IDR');
    NEW.kurs     := COALESCE(NULLIF(NEW.kurs, 0), 1);

    RETURN NEW;
END;
$$;


/* =============================================================================
   4. PASANG VALIDATOR SETELAH PREPARE + TYPE VALIDATOR
   ============================================================================= */

CREATE OR REPLACE TRIGGER trg_03_opening_stock_validate
BEFORE INSERT OR UPDATE
ON sc_trx.transaction_dt
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_validate_opening_stock_transaction();


/* =============================================================================
   5. DEPENDENCY CHECK + VERIFIKASI
   ============================================================================= */

DO $$
BEGIN
    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'sc_trx.transaction_dt tidak ditemukan.';
    END IF;

    IF to_regprocedure('sc_trx.fn_prepare_transaction_dt()') IS NULL THEN
        RAISE EXCEPTION 'fn_prepare_transaction_dt() TAHAP 04 belum tersedia.';
    END IF;

    IF to_regprocedure('sc_trx.fn_transaction_stock()') IS NULL THEN
        RAISE EXCEPTION 'fn_transaction_stock() TAHAP 06 belum tersedia.';
    END IF;

    IF to_regprocedure('sc_trx.fn_post_accounting_transaction(text)') IS NULL THEN
        RAISE NOTICE
            'fn_post_accounting_transaction(text) TAHAP 16 belum tersedia. Stock dapat diproses, tetapi posting jurnal belum dapat dijalankan.';
    END IF;
END;
$$;


SELECT
    TRIM(jt.journal_type::TEXT) AS journal_type,
    jt.journal_name,
    jt.direction,
    jt.module,
    jt.stock_effect,
    jt.accounting_effect,
    jt.asset_effect
FROM sc_mst.journal_type jt
WHERE TRIM(jt.journal_type::TEXT) = 'OPENST';


SELECT
    TRIM(journal_type::TEXT) AS journal_type,
    seq,
    account_role,
    idcoa,
    debit_credit,
    value_source,
    active,
    description
FROM sc_mst.journal_type_coa
WHERE TRIM(journal_type::TEXT) = 'OPENST'
ORDER BY seq;


SELECT
    tgname,
    tgenabled,
    pg_get_triggerdef(oid) AS trigger_definition
FROM pg_trigger
WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
  AND NOT tgisinternal
  AND tgname IN
  (
      'trg_01_transaction_prepare',
      'trg_02_transaction_type',
      'trg_03_opening_stock_validate',
      'trg_transaction_stock'
  )
ORDER BY tgname;



/* Verifikasi fungsi validasi staging */
SELECT to_regprocedure('sc_im.fn_validasi_stock_persediaan_opening()') AS fungsi_validasi_stock_opening;


-- FUNCTION: sc_im.fn_posting_stock_persediaan_opening(character varying)

-- DROP FUNCTION IF EXISTS sc_im.fn_posting_stock_persediaan_opening(character varying);

CREATE OR REPLACE FUNCTION sc_im.fn_posting_stock_persediaan_opening(
	p_inputby character varying DEFAULT 'SYSTEM'::character varying)
    RETURNS TABLE(r_calon bigint, r_inserted bigint, r_skipped bigint, r_posted bigint, r_pending bigint) 
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
    ROWS 1000

AS $BODY$

DECLARE
    v_inputby  VARCHAR(20);
    v_calon    BIGINT := 0;
    v_inserted BIGINT := 0;
BEGIN
    v_inputby := left(coalesce(nullif(btrim(p_inputby), ''), 'SYSTEM'), 20);

    SELECT count(*)::bigint INTO v_calon
      FROM sc_im.stock_persediaan_opening o
     WHERE upper(btrim(o.status_validasi)) = 'VALID';

    /* 1. Posting baris VALID yang dokumennya belum ada di buku besar.
          Satu baris staging = satu dokumen 'SA' || YYMMDD || id (6 digit). */
    WITH src AS (
        SELECT o.id,
               left(btrim(o.kode_gudang), 12)                            AS idlocation,
               left(btrim(coalesce(m.idarea_default::text, '')), 30)     AS idarea,
               left(coalesce(o.batch_spec, ''), 100)                     AS batch,
               left(btrim(o.kode_barang), 20)                            AS idbarang,
               o.tanggal_saldo                                           AS tanggal,
               left('SA' || to_char(o.tanggal_saldo, 'YYMMDD')
                    || lpad(o.id::text, 6, '0'), 20)                     AS docno,
               greatest(coalesce(o.qty_saldo, 0), 0)                     AS qty_in,
               greatest(-coalesce(o.qty_saldo, 0), 0)                    AS qty_out,
               CASE WHEN coalesce(o.qty_saldo, 0) <> 0
                    THEN abs(o.nilai_saldo / o.qty_saldo)
                    ELSE 0 END                                           AS harga,
               abs(coalesce(o.nilai_saldo, 0))                           AS nilai,
               left(btrim(coalesce(o.idbranch, '')), 20)                 AS idbranch,
               left(btrim(coalesce(o.cabang, '')), 30)                   AS cabang,
               left(btrim(o.kode_gudang), 50)                            AS warehouse,
               left(coalesce(o.satuan, ''), 10)                          AS idunit,
               left(coalesce(o.satuan, ''), 12)                          AS unit,
               coalesce(nullif(btrim(o.keterangan), ''), o.nama_barang)  AS description,
               coalesce(o.keterangan, '')                                AS keterangan,
               coalesce(nullif(btrim(o.uniqueidopening), ''),
                        md5(o.id::text))                                 AS kunci
          FROM sc_im.stock_persediaan_opening o
          LEFT JOIN sc_mst.mlocation m
                 ON btrim(m.idlocation::text) = btrim(o.kode_gudang)
         WHERE upper(btrim(o.status_validasi)) = 'VALID'
           AND coalesce(o.qty_saldo, 0) <> 0
           /* Qty harus terwakili pada NUMERIC(18,2); bila dibulatkan menjadi 0,00
              baris tidak diposting (lihat blok penanganan setelah INSERT). */
           AND round(abs(coalesce(o.qty_saldo, 0)), 2) > 0
           AND NOT EXISTS (
                 SELECT 1
                   FROM sc_trx.stkblc b
                  WHERE btrim(b.docno) = left('SA' || to_char(o.tanggal_saldo, 'YYMMDD')
                                              || lpad(o.id::text, 6, '0'), 20)
                    AND btrim(b.idbarang::text) = left(btrim(o.kode_barang), 20))
    )
    INSERT INTO sc_trx.stkblc (
        idlocation, idarea, batch, idbarang, trxdate, docdate, doctype, docno, docref,
        hist, ctype, qty_in, qty_out, qty_sld,
        pricelst_in, pricelst_out, pricelst_sld,
        unitcost, totalcost,
        type_in_out, journal_type,
        idbranch, cabang, warehouse, idunit, unit, subunit,
        picby, created_by, createdby, is_posted,
        description, keterangan,
        uniqueid, stock_uniqueid, source_uniqueid,
        source_table, source_id, source_line_id)
    SELECT s.idlocation,
           s.idarea,
           s.batch,
           s.idbarang,
           s.tanggal::timestamp,
           s.tanggal,
           'IN',
           s.docno,
           '',
           'SALDO AWAL',
           'SLD',
           s.qty_in,
           s.qty_out,
           coalesce((SELECT sum(coalesce(p.qty_in, 0) - coalesce(p.qty_out, 0))
                       FROM sc_trx.stkblc p
                      WHERE p.idlocation = s.idlocation
                        AND p.idarea     = s.idarea
                        AND p.batch      = s.batch
                        AND p.idbarang   = s.idbarang
                        AND (p.trxdate, p.doctype, p.docno, p.docref)
                            < (s.tanggal::timestamp, 'IN', s.docno, '')), 0)
             + s.qty_in - s.qty_out,
           CASE WHEN s.qty_in > 0 THEN s.harga ELSE 0 END,
           CASE WHEN s.qty_out > 0 THEN s.harga ELSE 0 END,
           0,
           s.harga,
           s.nilai,
           CASE WHEN s.qty_in > 0 THEN 'IN' ELSE 'OUT' END,
           'OPENST',
           s.idbranch,
           s.cabang,
           s.warehouse,
           s.idunit,
           s.unit,
           '',
           v_inputby,
           v_inputby,
           v_inputby,
           false,
           s.description,
           s.keterangan,
           'OPN' || s.kunci,
           'OPN' || s.kunci,
           s.kunci,
           'sc_im.stock_persediaan_opening',
           s.id,
           s.id
      FROM src s;

    GET DIAGNOSTICS v_inserted = ROW_COUNT;

    /* Baris VALID yang qty-nya tidak terwakili pada presisi sc_trx.stkblc
       (NUMERIC 18,2) tidak ikut diposting. Kembalikan ke DRAFT + catatan agar
       baris tersebut terlihat pada layar validasi dan tidak terus dihitung
       sebagai "masih VALID (belum terposting)". */
    UPDATE sc_im.stock_persediaan_opening o
       SET status_validasi  = 'DRAFT',
           validasi_catatan = concat_ws('; ', o.validasi_catatan,
                                        'qty_saldo di bawah 0,005 (dibulatkan 0,00 pada sc_trx.stkblc.qty_in/qty_out)'),
           updatedate       = CURRENT_TIMESTAMP,
           updateby         = v_inputby
     WHERE upper(btrim(o.status_validasi)) = 'VALID'
       AND coalesce(o.qty_saldo, 0) <> 0
       AND round(abs(o.qty_saldo), 2) = 0;

    /* Baris VALID yang dokumennya sudah ada di stkblc tetap ditandai POSTED,
       supaya staging tidak terus menampilkan baris "siap posting" yang sebenarnya
       sudah masuk buku besar (mis. upload ulang setelah staging dikosongkan). */
    UPDATE sc_im.stock_persediaan_opening o
       SET status_validasi = 'POSTED',
           updatedate      = CURRENT_TIMESTAMP,
           updateby        = v_inputby
     WHERE upper(btrim(o.status_validasi)) = 'VALID'
       AND EXISTS (
             SELECT 1
               FROM sc_trx.stkblc b
              WHERE btrim(b.docno) = 'SA' || to_char(o.tanggal_saldo, 'YYMMDD')
                                     || lpad(o.id::text, 6, '0')
                AND btrim(b.idbarang::text) = left(btrim(o.kode_barang), 20));

    RETURN QUERY
    SELECT v_calon,
           v_inserted,
           greatest(v_calon - v_inserted, 0)::bigint,
           (SELECT count(*)::bigint FROM sc_im.stock_persediaan_opening x
             WHERE upper(btrim(x.status_validasi)) = 'POSTED'),
           (SELECT count(*)::bigint FROM sc_im.stock_persediaan_opening x
             WHERE upper(btrim(x.status_validasi)) = 'VALID');
END;
$BODY$;

ALTER FUNCTION sc_im.fn_posting_stock_persediaan_opening(character varying)
    OWNER TO postgres;


COMMIT;



