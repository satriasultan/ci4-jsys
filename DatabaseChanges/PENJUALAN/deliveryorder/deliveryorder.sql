--##############################################################################
-- DELIVERY ORDER (DO) - POSTGRESQL
-- ONE-SHOT EXECUTION: DROP TABLE + CREATE ULANG + DRAFT/FINAL + TRIGGER
--
-- PERINGATAN:
--   Script ini MENGHAPUS seluruh data:
--     - sc_tmp.deliveryorder, sc_tmp.deliveryorder_dtl
--     - sc_trx.deliveryorder, sc_trx.deliveryorder_dtl
--   Jalankan HANYA pada database DEVELOPMENT / hasil backup.
--   WAJIB backup sebelum eksekusi.
--
-- CATATAN PENTING:
--   - Script ini TIDAK meng-update tabel SO (sc_trx.salesorder / _dtl).
--   - Outstanding SO dihitung via view sc_trx.v_so_outstanding.
--   - sc_trx.deliveryorder menjadi HISTORI LENGKAP semua DO:
--       D = Draft, F = Final, P = Printed, E = sedang direvisi, C = Cancel.
--   - Data sc_trx TIDAK PERNAH dihapus saat revisi atau cancel.
--
-- MEKANISME STATUS:
--   D = Draft   : dokumen resmi, stok BELUM tersedia.
--   F = Final   : siap kirim, stok dipotong.
--   P = Printed : sudah dicetak (setelah F).
--   C = Cancel  : dibatalkan (data tetap untuk histori).
--   E = Entry   : draft sementara di sc_tmp / sedang direvisi dari sc_trx.
--
-- ALUR:
--   E -> D  : Simpan Draft         (normal: INSERT baru; revisi: UPDATE existing)
--   E -> F  : Simpan Final         (normal: INSERT baru; revisi: UPDATE existing)
--   E -> C  : Batal input          (hapus tmp saja)
--   D -> F  : Finalize Draft       (UPDATE status 'F', potong stok)
--   D -> E  : Revisi Draft         (UPDATE status 'E' di sc_trx, COPY ke sc_tmp)
--   D -> C  : Cancel Draft         (UPDATE status 'C' di sc_trx)
--   F -> E  : Revisi Final         (UPDATE status 'E' di sc_trx, reversal stok, COPY ke sc_tmp)
--   F -> C  : Cancel Final         (UPDATE status 'C' di sc_trx, reversal stok)
--##############################################################################

BEGIN;

-- ============================================================================
-- 0. DROP OBJECTS
-- ============================================================================
DROP TRIGGER IF EXISTS tr_deliveryorder_finalize ON sc_tmp.deliveryorder;
DROP TRIGGER IF EXISTS tr_deliveryorder          ON sc_trx.deliveryorder;

DROP FUNCTION IF EXISTS sc_tmp.tr_deliveryorder_finalize();
DROP FUNCTION IF EXISTS sc_trx.tr_deliveryorder();

DROP VIEW  IF EXISTS sc_trx.v_so_outstanding;

DROP TABLE IF EXISTS sc_tmp.deliveryorder_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.deliveryorder     CASCADE;
DROP TABLE IF EXISTS sc_trx.deliveryorder_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.deliveryorder     CASCADE;

-- ============================================================================
-- 1. HEADER TEMPORARY
-- ============================================================================
CREATE TABLE sc_tmp.deliveryorder
(
    idurut              SERIAL NOT NULL,
    idurut_view         INTEGER,
    docno               CHARACTER(30) NOT NULL,
    docnoso             CHARACTER(30),
    idrev               CHARACTER(30),
    docdate             DATE,
    cabang              CHARACTER(30),
    pemohon             CHARACTER(100),
    kdcustomer          CHARACTER(30),
    nmcustomer          CHARACTER(250),
    alamatcustomer      TEXT,
    gradecustomer       CHARACTER(30),
    kdcustomerdeliv     CHARACTER(30),
    nmcustomerdeliv     CHARACTER(250),
    alamatcustomerdeliv TEXT,
    kdsalesman          CHARACTER(10),
    jthtempo            NUMERIC(18,2),
    idtax               CHARACTER(20),
    isinclusive         CHARACTER(6),
    isopenprice         CHARACTER(6),
    currcode            CHARACTER(3),
    kurs                NUMERIC(18,2),
    dpp                 NUMERIC(18,2),
    jumlahpajak         NUMERIC(18,2),
    total               NUMERIC(18,2),
    carabayar           CHARACTER(30),
    status              CHARACTER(6),
    keterangan          TEXT,
    inputby             VARCHAR(50),
    inputdate           TIMESTAMP,
    updateby            VARCHAR(50),
    updatedate          TIMESTAMP,
    printby             VARCHAR(50),
    printdate           TIMESTAMP,
    printcount          INTEGER DEFAULT 0,
    docnotmp            CHARACTER(30),
    CONSTRAINT pk_tmp_deliveryorder PRIMARY KEY (docno)
);

ALTER TABLE sc_tmp.deliveryorder OWNER TO postgres;
CREATE INDEX idx_tmp_do_view ON sc_tmp.deliveryorder (idurut_view);

-- ============================================================================
-- 2. HEADER TRANSAKSI
-- ============================================================================
CREATE TABLE sc_trx.deliveryorder
(
    idurut              SERIAL NOT NULL,
    idurut_view         INTEGER,
    docno               CHARACTER(30) NOT NULL,
    docnoso             CHARACTER(30),
    idrev               CHARACTER(30),
    docdate             DATE,
    cabang              CHARACTER(30),
    pemohon             CHARACTER(100),
    kdcustomer          CHARACTER(30),
    nmcustomer          CHARACTER(250),
    alamatcustomer      TEXT,
    gradecustomer       CHARACTER(30),
    kdcustomerdeliv     CHARACTER(30),
    nmcustomerdeliv     CHARACTER(250),
    alamatcustomerdeliv TEXT,
    kdsalesman          CHARACTER(10),
    jthtempo            NUMERIC(18,2),
    idtax               CHARACTER(20),
    isinclusive         CHARACTER(6),
    isopenprice         CHARACTER(6),
    currcode            CHARACTER(3),
    kurs                NUMERIC(18,2),
    dpp                 NUMERIC(18,2),
    jumlahpajak         NUMERIC(18,2),
    total               NUMERIC(18,2),
    carabayar           CHARACTER(30),
    status              CHARACTER(6),
    keterangan          TEXT,
    inputby             VARCHAR(50),
    inputdate           TIMESTAMP,
    updateby            VARCHAR(50),
    updatedate          TIMESTAMP,
    printby             VARCHAR(50),
    printdate           TIMESTAMP,
    printcount          INTEGER DEFAULT 0,
    docnotmp            CHARACTER(30),
    CONSTRAINT pk_trx_deliveryorder PRIMARY KEY (docno)
);

ALTER TABLE sc_trx.deliveryorder OWNER TO postgres;
CREATE INDEX idx_trx_do_view ON sc_trx.deliveryorder (idurut_view);

-- ============================================================================
-- 3. DETAIL TEMPORARY
-- ============================================================================
CREATE TABLE sc_tmp.deliveryorder_dtl
(
    idurut          SERIAL PRIMARY KEY,
    idurut_view     INTEGER,
    docno           CHARACTER(30) NOT NULL,
    docnoso         CHARACTER(30),
    idrev           CHARACTER(30),
    idurutso        INTEGER,

    uniqueid        VARCHAR(64) NOT NULL,
    uniqueidso      VARCHAR(64),

    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),
    idbarangso      CHARACTER(20),
    nmbarangso      CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),
    unit            CHARACTER(20),

    qty             NUMERIC(18,2),
    qtyso           NUMERIC(18,2),

    harga           NUMERIC(18,2),
    hargasatuan     NUMERIC(18,2),
    multidisc       NUMERIC(18,2),
    nilai           NUMERIC(18,2),

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),
    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),
    qtysj           NUMERIC(18,2) DEFAULT 0,

    stokakhir       NUMERIC(18,2),

    bomdesc         TEXT,
    description     TEXT,
    status          CHARACTER(6),
    inputby         VARCHAR(50),
    inputdate       TIMESTAMP,
    updateby        VARCHAR(50),
    updatedate      TIMESTAMP,
    docnotmp        CHARACTER(30)
);

ALTER TABLE sc_tmp.deliveryorder_dtl OWNER TO postgres;
CREATE INDEX idx_tmp_do_dtl_view    ON sc_tmp.deliveryorder_dtl (docno, idurut_view);
CREATE INDEX idx_tmp_do_dtl_uid     ON sc_tmp.deliveryorder_dtl (uniqueid);
CREATE INDEX idx_tmp_do_dtl_uidso   ON sc_tmp.deliveryorder_dtl (uniqueidso);
CREATE INDEX idx_tmp_do_dtl_docnoso ON sc_tmp.deliveryorder_dtl (docnoso);

-- ============================================================================
-- 4. DETAIL TRANSAKSI
-- ============================================================================
CREATE TABLE sc_trx.deliveryorder_dtl
(
    idurut          SERIAL PRIMARY KEY,
    idurut_view     INTEGER,
    docno           CHARACTER(30) NOT NULL,
    docnoso         CHARACTER(30),
    idrev           CHARACTER(30),
    idurutso        INTEGER,

    uniqueid        VARCHAR(64) NOT NULL,
    uniqueidso      VARCHAR(64),

    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),
    idbarangso      CHARACTER(20),
    nmbarangso      CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),
    unit            CHARACTER(20),

    qty             NUMERIC(18,2),
    qtyso           NUMERIC(18,2),

    harga           NUMERIC(18,2),
    hargasatuan     NUMERIC(18,2),
    multidisc       NUMERIC(18,2),
    nilai           NUMERIC(18,2),

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),
    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),
    qtysj           NUMERIC(18,2) DEFAULT 0,

    stokakhir       NUMERIC(18,2),

    bomdesc         TEXT,
    description     TEXT,
    status          CHARACTER(6),
    inputby         VARCHAR(50),
    inputdate       TIMESTAMP,
    updateby        VARCHAR(50),
    updatedate      TIMESTAMP,
    docnotmp        CHARACTER(30)
);

ALTER TABLE sc_trx.deliveryorder_dtl OWNER TO postgres;
CREATE INDEX idx_trx_do_dtl_view    ON sc_trx.deliveryorder_dtl (docno, idurut_view);
CREATE INDEX idx_trx_do_dtl_uid     ON sc_trx.deliveryorder_dtl (uniqueid);
CREATE INDEX idx_trx_do_dtl_uidso   ON sc_trx.deliveryorder_dtl (uniqueidso);
CREATE INDEX idx_trx_do_dtl_docnoso ON sc_trx.deliveryorder_dtl (docnoso);

-- ============================================================================
-- 5. FUNCTION: sc_tmp.tr_deliveryorder_finalize()
--    AFTER UPDATE ON sc_tmp.deliveryorder
--
--    E -> D : Simpan Draft   (normal: INSERT; revisi: UPDATE existing sc_trx)
--    E -> F : Simpan Final   (normal: INSERT; revisi: UPDATE existing sc_trx)
--    E -> C : Batal input    (hapus tmp saja)
--
--    CATATAN: TIDAK ada update ke sc_trx.salesorder / _dtl.
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_deliveryorder_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_idurut      INTEGER;
    v_num         TEXT;
    v_num_int     BIGINT;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_client_ip   TEXT;
    v_docnotmp    TEXT;
    v_new_view    INTEGER;
    v_new_status  CHAR(6);
BEGIN
    IF NOT (
        (OLD.status = 'E' AND NEW.status = 'D') OR
        (OLD.status = 'E' AND NEW.status = 'F') OR
        (OLD.status = 'E' AND NEW.status = 'C')
    ) THEN
        RETURN NEW;
    END IF;

    v_inputby   := NEW.inputby;
    v_idurut    := NEW.idurut;
    v_docno     := BTRIM(NEW.docno);
    v_docnotmp  := NULLIF(BTRIM(NEW.docnotmp), '');
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- E -> C (batal input di tmp)
    IF OLD.status = 'E' AND NEW.status = 'C' THEN
        DELETE FROM sc_tmp.deliveryorder_dtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.deliveryorder     WHERE BTRIM(docno) = BTRIM(NEW.docno);
        RETURN NEW;
    END IF;

    v_new_status := CASE WHEN NEW.status = 'D' THEN 'D'::CHAR(6)
                         ELSE 'F'::CHAR(6) END;

    -- ============================ PATH A: NORMAL (INSERT BARU) ===========
    IF v_docnotmp IS NULL THEN

        IF v_docno IS NULL OR v_docno = '' THEN
            RAISE EXCEPTION 'Nomor Delivery Order tidak boleh kosong';
        END IF;

        IF v_docno !~ '[0-9]+$' THEN
            RAISE EXCEPTION
                'Format nomor Delivery Order tidak valid (harus diakhiri angka): %',
                v_docno;
        END IF;

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_num        := substring(v_docno FROM '([0-9]+)$');

        PERFORM pg_advisory_xact_lock(hashtextextended(v_base_docno, 0));

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.deliveryorder dx
                WHERE BTRIM(dx.docno) = v_new_docno
            );

            v_num := substring(v_new_docno FROM '([0-9]+)$');
            IF v_num IS NULL OR v_num = '' THEN
                RAISE EXCEPTION 'Tidak dapat membaca nomor urut dari dokumen: %', v_new_docno;
            END IF;

            v_num_int   := v_num::BIGINT + 1;
            v_new_docno := v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');

            IF length(v_new_docno) > 30 THEN
                RAISE EXCEPTION 'Nomor Delivery Order melebihi 30 karakter: %', v_new_docno;
            END IF;
        END LOOP;

        v_docno := v_new_docno;

        SELECT COALESCE(MAX(idurut_view), 0) + 1 INTO v_new_view
        FROM sc_trx.deliveryorder;

        -- INSERT HEADER (status D/F)
        INSERT INTO sc_trx.deliveryorder (
            idurut_view, docno, docnoso, idrev, cabang, docdate, pemohon,
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate,
            printcount, docnotmp
        )
        SELECT
            v_new_view, v_docno, docnoso, idrev, cabang, docdate, pemohon,
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, v_new_status, inputby, inputdate,
            updateby, updatedate, printby, printdate,
            COALESCE(printcount, 0), docnotmp
        FROM sc_tmp.deliveryorder
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header temporary DO tidak ditemukan. docno=%, inputby=%, idurut=%',
                OLD.docno, v_inputby, v_idurut;
        END IF;

        -- INSERT DETAIL
        INSERT INTO sc_trx.deliveryorder_dtl (
            idurut_view, docno, docnoso, idrev, idurutso,
            uniqueid, uniqueidso,
            idbarang, nmbarang, idbarangso, nmbarangso,
            idprincipal, idgudang, idspec, unit,
            qty, qtyso,
            harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtysj,
            stokakhir,
            bomdesc, description,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(idurut_view, idurut), idurut)::INTEGER,
            v_docno, docnoso, idrev, idurutso,
            uniqueid, uniqueidso,
            idbarang, nmbarang, idbarangso, nmbarangso,
            idprincipal, idgudang, idspec, unit,
            qty, qtyso,
            harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtysj,
            stokakhir,
            bomdesc, description,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.deliveryorder_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        -- (TIDAK ada update ke sc_trx.salesorder / _dtl)
        -- Potong stok gudang via fungsi inventory (jika ada, aktifkan):
        -- IF v_new_status = 'F' THEN
        --     PERFORM sc_inv.fn_stok_keluar(...);
        -- END IF;

        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30), NULL, 'I.S', 'I.S.B.2',
            CASE WHEN v_new_status = 'D' THEN 'D' ELSE 'I' END,
            v_inputby, v_client_ip, v_inputby
        );

        DELETE FROM sc_tmp.deliveryorder_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno) AND inputby = v_inputby;

        DELETE FROM sc_tmp.deliveryorder
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- ============================ PATH B: REVISI (UPDATE EXISTING) =======
    ELSE

        -- docnotmp = docno asli di sc_trx. Kita UPDATE, bukan hapus.

        -- 1) Update HEADER di sc_trx (status D/F)
        UPDATE sc_trx.deliveryorder h
        SET
            cabang              = t.cabang,
            docdate             = t.docdate,
            pemohon             = t.pemohon,
            kdcustomerdeliv     = t.kdcustomerdeliv,
            nmcustomerdeliv     = t.nmcustomerdeliv,
            alamatcustomerdeliv = t.alamatcustomerdeliv,
            carabayar           = t.carabayar,
            kdcustomer          = t.kdcustomer,
            nmcustomer          = t.nmcustomer,
            alamatcustomer      = t.alamatcustomer,
            jthtempo            = t.jthtempo,
            isopenprice         = t.isopenprice,
            kdsalesman          = t.kdsalesman,
            gradecustomer       = t.gradecustomer,
            idtax               = t.idtax,
            isinclusive         = t.isinclusive,
            currcode            = t.currcode,
            kurs                = t.kurs,
            dpp                 = t.dpp,
            jumlahpajak         = t.jumlahpajak,
            total               = t.total,
            keterangan          = t.keterangan,
            status              = v_new_status,
            updateby            = t.updateby,
            updatedate          = t.updatedate,
            printby             = t.printby,
            printdate           = t.printdate,
            printcount          = COALESCE(t.printcount, 0)
        FROM sc_tmp.deliveryorder t
        WHERE BTRIM(h.docno) = BTRIM(v_docnotmp)
          AND BTRIM(t.docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Header transaksi DO tidak ditemukan untuk update: %', v_docnotmp;
        END IF;

        -- 2) Hapus detail lama di sc_trx
        DELETE FROM sc_trx.deliveryorder_dtl
        WHERE BTRIM(docno) = BTRIM(v_docnotmp);

        -- 3) Insert ulang detail dari sc_tmp
        INSERT INTO sc_trx.deliveryorder_dtl (
            idurut_view, docno, docnoso, idrev, idurutso,
            uniqueid, uniqueidso,
            idbarang, nmbarang, idbarangso, nmbarangso,
            idprincipal, idgudang, idspec, unit,
            qty, qtyso,
            harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtysj,
            stokakhir,
            bomdesc, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(idurut_view, idurut), idurut)::INTEGER,
            v_docnotmp, docnoso, idrev, idurutso,
            uniqueid, uniqueidso,
            idbarang, nmbarang, idbarangso, nmbarangso,
            idprincipal, idgudang, idspec, unit,
            qty, qtyso,
            harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtysj,
            stokakhir,
            bomdesc, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.deliveryorder_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        -- (TIDAK ada update ke sc_trx.salesorder / _dtl)
        -- Potong stok gudang via fungsi inventory (jika ada, aktifkan):
        -- IF v_new_status = 'F' THEN
        --     PERFORM sc_inv.fn_stok_keluar(...);
        -- END IF;

        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.2',
            CASE WHEN v_new_status = 'D' THEN 'D' ELSE 'U' END,
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.deliveryorder_dtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.deliveryorder     WHERE BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$BODY$;

-- ============================================================================
-- 6. FUNCTION: sc_trx.tr_deliveryorder()
--    AFTER UPDATE ON sc_trx.deliveryorder
--
--    D -> F : Finalize Draft    (UPDATE status 'F', potong stok)
--    D -> C : Cancel Draft      (UPDATE status 'C', data tetap)
--    F -> C : Cancel Final      (UPDATE status 'C', reversal stok, data tetap)
--    D -> E : Revisi Draft      (UPDATE status 'E', COPY ke sc_tmp, data tetap)
--    F -> E : Revisi Final      (UPDATE status 'E', reversal stok, COPY ke sc_tmp, data tetap)
--
--    CATATAN: TIDAK ada update ke sc_trx.salesorder / _dtl.
--    Data sc_trx TIDAK PERNAH dihapus, hanya berubah status.
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.tr_deliveryorder()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE
AS $BODY$
DECLARE
    v_client_ip TEXT;
    v_inputby   TEXT;
BEGIN
    IF OLD.status IS NOT DISTINCT FROM NEW.status THEN
        RETURN NEW;
    END IF;

    v_inputby   := COALESCE(NEW.updateby, NEW.inputby);
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ============================ D -> F (Finalize Draft) ===============
    IF OLD.status = 'D' AND NEW.status = 'F' THEN

        -- Potong stok gudang via fungsi inventory (jika ada, aktifkan):
        -- PERFORM sc_inv.fn_stok_keluar(...);

        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.2', 'U',
            v_inputby, v_client_ip, v_inputby
        );
        RETURN NEW;
    END IF;

    -- ============================ D -> C (Cancel Draft) =================
    IF OLD.status = 'D' AND NEW.status = 'C' THEN
        -- Draft tidak pernah potong stok. Data sc_trx TETAP (status C).
        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.2', 'C',
            v_inputby, v_client_ip, v_inputby
        );
        RETURN NEW;
    END IF;

    -- ============================ F -> C (Cancel Final) =================
    IF OLD.status = 'F' AND NEW.status = 'C' THEN

        -- Kembalikan stok gudang via fungsi inventory (reversal):
        -- PERFORM sc_inv.fn_stok_masuk(...);

        -- Data sc_trx TETAP (status C), tidak dihapus.
        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.2', 'C',
            v_inputby, v_client_ip, v_inputby
        );
        RETURN NEW;
    END IF;

    -- ============================ D -> E atau F -> E (REVISI) ===========
    -- PENTING: JANGAN hapus sc_trx. Cukup:
    --   1) Reversal stok (jika dari F)
    --   2) Salin data ke sc_tmp sebagai draft baru
    --   3) Status sc_trx di-set 'E' oleh UPDATE (yang memicu trigger ini)
    IF NEW.status = 'E' AND OLD.status IN ('D', 'F') THEN

        -- Reversal stok HANYA jika dari FINAL (F):
        -- IF OLD.status = 'F' THEN
        --     PERFORM sc_inv.fn_stok_masuk(...);
        -- END IF;

        -- Bersihkan draft lama di tmp (jika ada sisa)
        DELETE FROM sc_tmp.deliveryorder_dtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.deliveryorder     WHERE BTRIM(docno) = BTRIM(NEW.docno);

        -- Insert HEADER ke tmp (copy dari sc_trx yang SUDAH ada)
        INSERT INTO sc_tmp.deliveryorder (
            idurut_view, docno, docnoso, idrev, cabang, docdate, pemohon,
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, docnotmp
        )
        SELECT
            COALESCE(h.idurut_view, h.idurut),
            h.docno, h.docnoso, h.idrev, h.cabang, h.docdate, h.pemohon,
            h.kdcustomerdeliv, h.nmcustomerdeliv, h.alamatcustomerdeliv, h.carabayar,
            h.kdcustomer, h.nmcustomer, h.alamatcustomer, h.jthtempo,
            h.isopenprice, h.kdsalesman, h.gradecustomer,
            h.idtax, h.isinclusive, h.currcode, h.kurs, h.dpp,
            h.jumlahpajak, h.total,
            h.keterangan, 'E', h.inputby, h.inputdate, h.updateby, h.updatedate,
            h.printby, h.printdate, COALESCE(h.printcount, 0),
            h.docno  -- docnotmp = docno asli (penanda revisi)
        FROM sc_trx.deliveryorder h
        WHERE BTRIM(h.docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header transaksi DO tidak ditemukan saat revisi ->E: %',
                NEW.docno;
        END IF;

        -- Insert DETAIL ke tmp (idurut_view 1..N)
        INSERT INTO sc_tmp.deliveryorder_dtl (
            idurut_view, docno, docnoso, idrev, idurutso,
            uniqueid, uniqueidso,
            idbarang, nmbarang, idbarangso, nmbarangso,
            idprincipal, idgudang, idspec, unit,
            qty, qtyso,
            harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtysj,
            stokakhir,
            bomdesc, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (
                ORDER BY COALESCE(d.idurut_view, d.idurut), d.idurut
            )::INTEGER,
            NEW.docno, d.docnoso, d.idrev, d.idurutso,
            d.uniqueid, d.uniqueidso,
            d.idbarang, d.nmbarang, d.idbarangso, d.nmbarangso,
            d.idprincipal, d.idgudang, d.idspec, d.unit,
            d.qty, d.qtyso,
            d.harga, d.hargasatuan, d.multidisc, d.nilai,
            d.idtax, d.currcode, d.kurs, d.nilaikonversi, d.nilaipajak, d.qtysj,
            d.stokakhir,
            d.bomdesc, d.description,
            d.inputby, d.inputdate, COALESCE(d.status, 'F'),
            d.updateby, d.updatedate,
            d.docno  -- docnotmp = docno asli
        FROM sc_trx.deliveryorder_dtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno);

        -- TIDAK ADA DELETE FROM sc_trx.* -- data tetap ada dengan status 'E'.

        RETURN NEW;
    END IF;

    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_trx.tr_deliveryorder() OWNER TO postgres;

-- ============================================================================
-- 7. VIEW OUTSTANDING SO
--    - sc_tmp SELALU prioritas (jika docno ada di tmp)
--    - sc_trx dihitung HANYA jika docno tidak ada di tmp DAN status F/P
--    - Draft D & E & C diabaikan
-- ============================================================================
CREATE OR REPLACE VIEW sc_trx.v_so_outstanding AS
WITH do_source AS (
    -- 1) sc_tmp SELALU dipakai (prioritas)
    SELECT
        BTRIM(docno)  AS docno,
        uniqueidso,
        qty
    FROM sc_tmp.deliveryorder_dtl
    WHERE uniqueidso IS NOT NULL
      AND uniqueidso <> ''

    UNION ALL

    -- 2) sc_trx HANYA jika docno tidak ada di sc_tmp DAN status F/P
    SELECT
        BTRIM(trx.docno)  AS docno,
        trx.uniqueidso,
        trx.qty
    FROM sc_trx.deliveryorder_dtl trx
    JOIN sc_trx.deliveryorder h
         ON BTRIM(h.docno) = BTRIM(trx.docno)
    WHERE trx.uniqueidso IS NOT NULL
      AND trx.uniqueidso <> ''
      AND h.status IN ('F', 'P')
      AND NOT EXISTS (
          SELECT 1
          FROM sc_tmp.deliveryorder_dtl t
          WHERE BTRIM(t.docno) = BTRIM(trx.docno)
      )
),
do_sum AS (
    SELECT uniqueidso, SUM(qty) AS qty_do
    FROM do_source
    GROUP BY uniqueidso
)
SELECT
    sod.docno                     AS docnoso,
    sod.idurut                    AS idurutso,
    sod.uniqueid                  AS uniqueidso,
    sod.idbarang,
    sod.nmbarang,
    sod.unit,
    sod.idgudang,
    sod.qty                       AS qty_so,
    sod.harga,
    sod.nilai,
    sod.nilaikonversi,
    sod.nilaipajak,
    COALESCE(ds.qty_do, 0)        AS qty_do,
    sod.qty - COALESCE(ds.qty_do, 0) AS qty_outstanding
FROM sc_trx.salesorder_dtl sod
LEFT JOIN do_sum ds ON ds.uniqueidso = sod.uniqueid
WHERE sod.status IN ('F', 'DO', 'P', 'PJO');

-- ============================================================================
-- 8. PASANG TRIGGER
-- ============================================================================
CREATE TRIGGER tr_deliveryorder_finalize
    AFTER UPDATE ON sc_tmp.deliveryorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_deliveryorder_finalize();

CREATE TRIGGER tr_deliveryorder
    AFTER UPDATE ON sc_trx.deliveryorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_deliveryorder();

COMMIT;

-- ============================================================================
-- 9. VERIFIKASI (jalankan manual setelah COMMIT)
-- ============================================================================
-- -- Cek trigger:
-- SELECT n.nspname AS schema_name, c.relname AS table_name,
--        t.tgname AS trigger_name, pg_get_triggerdef(t.oid) AS trigger_definition
-- FROM pg_trigger t
-- JOIN pg_class c ON c.oid = t.tgrelid
-- JOIN pg_namespace n ON n.oid = c.relnamespace
-- WHERE NOT t.tgisinternal
--   AND n.nspname IN ('sc_tmp','sc_trx')
--   AND c.relname IN ('deliveryorder','deliveryorder_dtl')
-- ORDER BY n.nspname, c.relname, t.tgname;
--
-- -- Cek view outstanding:
-- SELECT * FROM sc_trx.v_so_outstanding
-- WHERE qty_outstanding > 0
-- LIMIT 20;
--
-- -- Cek status draft:
-- SELECT docno, status, docdate, kdcustomer, total
-- FROM sc_trx.deliveryorder
-- WHERE status IN ('D','E','C')
-- ORDER BY idurut_view, idurut;