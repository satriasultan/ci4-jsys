--##############################################################################
-- PENJUALAN (PJO) - POSTGRESQL
-- ONE-SHOT EXECUTION: DROP + CREATE + TRIGGER + JURNAL (TAHAP 01-04)
--
-- PERINGATAN:
--   Script ini MENGHAPUS seluruh data:
--     - sc_tmp.penjualan, sc_tmp.penjualan_dtl
--     - sc_trx.penjualan, sc_trx.penjualan_dtl
--   Jalankan HANYA pada database DEVELOPMENT / hasil backup.
--   WAJIB backup sebelum eksekusi.
--
-- CATATAN PENTING:
--   - Script ini TIDAK meng-update tabel SO (sc_trx.salesorder / _dtl).
--   - Outstanding SO dihitung via view/query terpisah (v_so_outstanding).
--   - Stock movement via transaction_dt (journal_type='DELIVR').
--   - Accounting via transaction_dt (journal_type='SALESX').
--   - Tidak ada panggilan sc_inv.fn_stok_keluar / fn_stok_masuk.
--
-- DEPENDENCY (HARUS SUDAH ADA):
--   - TAHAP 01: sc_trx.fn_stock_uniqueid, sc_trx.fn_stock_key
--   - TAHAP 02: sc_mst.journal_type
--   - TAHAP 03: sc_mst.journal_type_coa
--   - TAHAP 04: sc_trx.transaction_dt, sc_trx.fn_resolve_transaction_coa,
--               sc_trx.fn_get_transaction_tax_accounts
--   - sc_mst.coa, mbarang, currency, konfigurasi_umum, tax_dtl
--   - sc_log.fn_log_transaction, sc_log.fn_get_user_ip
--##############################################################################

BEGIN;

-- ============================================================================
-- 0. DROP OBJECTS
-- ============================================================================
DROP TRIGGER IF EXISTS tr_penjualan_finalize ON sc_tmp.penjualan;
DROP TRIGGER IF EXISTS tr_penjualan          ON sc_trx.penjualan;

DROP TRIGGER IF EXISTS tr_penjualan_dtl_to_transaction ON sc_trx.penjualan_dtl;
DROP TRIGGER IF EXISTS tr_penjualan_dtl_to_stok        ON sc_trx.penjualan_dtl;

DROP FUNCTION IF EXISTS sc_tmp.tr_penjualan_finalize();
DROP FUNCTION IF EXISTS sc_trx.tr_penjualan();
DROP FUNCTION IF EXISTS sc_trx.fn_penjualan_dtl_to_transaction();
DROP FUNCTION IF EXISTS sc_trx.fn_penjualan_dtl_to_stok();
DROP FUNCTION IF EXISTS sc_trx.fn_sync_penjualan_dtl_to_transaction(TEXT, TEXT);
DROP FUNCTION IF EXISTS sc_trx.fn_sync_penjualan_dtl_to_delivr(TEXT, TEXT);
DROP FUNCTION IF EXISTS sc_trx.fn_sync_penjualan_dtl_to_salesx(TEXT, TEXT);

DROP TABLE IF EXISTS sc_tmp.penjualan_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.penjualan     CASCADE;
DROP TABLE IF EXISTS sc_trx.penjualan_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.penjualan     CASCADE;

-- ============================================================================
-- 1. HEADER TEMPORARY
-- ============================================================================
CREATE TABLE sc_tmp.penjualan
(
    idurut              SERIAL NOT NULL,
    idurut_view         INTEGER,
    docno               CHARACTER(30) NOT NULL,
    docnoso             CHARACTER(30),
    docnodo             CHARACTER(30),
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
    jthtempo            NUMERIC(18,4),
    idtax               CHARACTER(20),
    isinclusive         CHARACTER(6),
    isopenprice         CHARACTER(6),
    currcode            CHARACTER(3),
    kurs                NUMERIC(18,4),
    dpp                 NUMERIC(18,4),
    jumlahpajak         NUMERIC(18,4),
    total               NUMERIC(18,4),
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
    doctype             CHARACTER(10) DEFAULT 'SALES',
    CONSTRAINT pk_tmp_penjualan PRIMARY KEY (docno)
);

ALTER TABLE sc_tmp.penjualan OWNER TO postgres;
CREATE INDEX idx_tmp_pjo_view    ON sc_tmp.penjualan (idurut_view);
CREATE INDEX idx_tmp_pjo_docnoso ON sc_tmp.penjualan (docnoso);
CREATE INDEX idx_tmp_pjo_docnodo ON sc_tmp.penjualan (docnodo);

-- ============================================================================
-- 2. HEADER TRANSAKSI
-- ============================================================================
CREATE TABLE sc_trx.penjualan
(
    idurut              SERIAL NOT NULL,
    idurut_view         INTEGER,
    docno               CHARACTER(30) NOT NULL,
    docnoso             CHARACTER(30),
    docnodo             CHARACTER(30),
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
    jthtempo            NUMERIC(18,4),
    idtax               CHARACTER(20),
    isinclusive         CHARACTER(6),
    isopenprice         CHARACTER(6),
    currcode            CHARACTER(3),
    kurs                NUMERIC(18,4),
    dpp                 NUMERIC(18,4),
    jumlahpajak         NUMERIC(18,4),
    total               NUMERIC(18,4),
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
    doctype             CHARACTER(10) DEFAULT 'SALES',
    CONSTRAINT pk_trx_penjualan PRIMARY KEY (docno)
);

ALTER TABLE sc_trx.penjualan OWNER TO postgres;
CREATE INDEX idx_trx_pjo_view    ON sc_trx.penjualan (idurut_view);
CREATE INDEX idx_trx_pjo_docnoso ON sc_trx.penjualan (docnoso);
CREATE INDEX idx_trx_pjo_docnodo ON sc_trx.penjualan (docnodo);

-- ============================================================================
-- 3. DETAIL TEMPORARY
-- ============================================================================
CREATE TABLE sc_tmp.penjualan_dtl
(
    idurut          SERIAL PRIMARY KEY,
    idurut_view     INTEGER,
    docno           CHARACTER(30) NOT NULL,

    docnoso         CHARACTER(30),
    idrev           CHARACTER(30),
    idurutso        INTEGER,
    uniqueidso      VARCHAR(64),

    docnodo         CHARACTER(30),
    idurutdo        INTEGER,
    uniqueiddo      VARCHAR(64),

    uniqueid        VARCHAR(64) NOT NULL,

    idbarangref     CHARACTER(20),
    nmbarangref     CHARACTER(150),
    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),
    unit            CHARACTER(20),

    qtyref          NUMERIC(18,4),
    hargasatuanref  NUMERIC(18,4),

    qty             NUMERIC(18,4),
    harga           NUMERIC(18,4),
    hargasatuan     NUMERIC(18,4),
    multidisc       NUMERIC(18,4),
    nilai           NUMERIC(18,4),

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,4),
    nilaikonversi   NUMERIC(18,4),
    nilaipajak      NUMERIC(18,4),
    qtyretur        NUMERIC(18,4) DEFAULT 0,

    stokakhir       NUMERIC(18,4),

    bomdesc         TEXT,
    description     TEXT,
    etd             DATE,
    eta             DATE,
    status          CHARACTER(6),
    inputby         VARCHAR(50),
    inputdate       TIMESTAMP,
    updateby        VARCHAR(50),
    updatedate      TIMESTAMP,
    doctype         CHARACTER(10) DEFAULT 'SALES',
    docnotmp        CHARACTER(30)
);

ALTER TABLE sc_tmp.penjualan_dtl OWNER TO postgres;
CREATE INDEX idx_tmp_pjo_dtl_view    ON sc_tmp.penjualan_dtl (docno, idurut_view);
CREATE INDEX idx_tmp_pjo_dtl_uid     ON sc_tmp.penjualan_dtl (uniqueid);
CREATE INDEX idx_tmp_pjo_dtl_uidso   ON sc_tmp.penjualan_dtl (uniqueidso);
CREATE INDEX idx_tmp_pjo_dtl_uiddo   ON sc_tmp.penjualan_dtl (uniqueiddo);
CREATE INDEX idx_tmp_pjo_dtl_docnoso ON sc_tmp.penjualan_dtl (docnoso);
CREATE INDEX idx_tmp_pjo_dtl_docnodo ON sc_tmp.penjualan_dtl (docnodo);

-- ============================================================================
-- 4. DETAIL TRANSAKSI
-- ============================================================================
CREATE TABLE sc_trx.penjualan_dtl
(
    idurut          SERIAL PRIMARY KEY,
    idurut_view     INTEGER,
    docno           CHARACTER(30) NOT NULL,
    docnoso         CHARACTER(30),
    idrev           CHARACTER(30),
    idurutso        INTEGER,
    uniqueidso      VARCHAR(64),
    docnodo         CHARACTER(30),
    idurutdo        INTEGER,
    uniqueiddo      VARCHAR(64),
    uniqueid        VARCHAR(64) NOT NULL,
    idbarangref     CHARACTER(20),
    nmbarangref     CHARACTER(150),
    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),
    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),
    unit            CHARACTER(20),
    qtyref          NUMERIC(18,4),
    hargasatuanref  NUMERIC(18,4),
    qty             NUMERIC(18,4),
    harga           NUMERIC(18,4),
    hargasatuan     NUMERIC(18,4),
    multidisc       NUMERIC(18,4),
    nilai           NUMERIC(18,4),
    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,4),
    nilaikonversi   NUMERIC(18,4),
    nilaipajak      NUMERIC(18,4),
    qtyretur        NUMERIC(18,4) DEFAULT 0,
    stokakhir       NUMERIC(18,4),
    bomdesc         TEXT,
    description     TEXT,
    etd             DATE,
    eta             DATE,
    status          CHARACTER(6),
    inputby         VARCHAR(50),
    inputdate       TIMESTAMP,
    updateby        VARCHAR(50),
    updatedate      TIMESTAMP,
    doctype         CHARACTER(10) DEFAULT 'SALES',
    docnotmp        CHARACTER(30)
);

ALTER TABLE sc_trx.penjualan_dtl OWNER TO postgres;
CREATE INDEX idx_trx_pjo_dtl_view    ON sc_trx.penjualan_dtl (docno, idurut_view);
CREATE INDEX idx_trx_pjo_dtl_uid     ON sc_trx.penjualan_dtl (uniqueid);
CREATE INDEX idx_trx_pjo_dtl_uidso   ON sc_trx.penjualan_dtl (uniqueidso);
CREATE INDEX idx_trx_pjo_dtl_uiddo   ON sc_trx.penjualan_dtl (uniqueiddo);
CREATE INDEX idx_trx_pjo_dtl_docnoso ON sc_trx.penjualan_dtl (docnoso);
CREATE INDEX idx_trx_pjo_dtl_docnodo ON sc_trx.penjualan_dtl (docnodo);

-- ============================================================================
-- 5. FUNCTION: sc_tmp.tr_penjualan_finalize()
--    AFTER UPDATE ON sc_tmp.penjualan
--    TANPA update ke sc_trx.salesorder / salesorder_dtl.
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_penjualan_finalize()
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
BEGIN
    IF NOT (
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

    -- ===================== NORMAL FINALIZE E -> F ========================
    IF OLD.status = 'E' AND NEW.status = 'F' AND v_docnotmp IS NULL THEN

        IF v_docno IS NULL OR v_docno = '' THEN
            RAISE EXCEPTION 'Nomor Penjualan tidak boleh kosong';
        END IF;

        IF v_docno !~ '[0-9]+$' THEN
            RAISE EXCEPTION
                'Format nomor Penjualan tidak valid (harus diakhiri angka): %',
                v_docno;
        END IF;

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_num        := substring(v_docno FROM '([0-9]+)$');

        PERFORM pg_advisory_xact_lock(hashtextextended(v_base_docno, 0));

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.penjualan pj
                WHERE BTRIM(pj.docno) = v_new_docno
            );

            v_num := substring(v_new_docno FROM '([0-9]+)$');
            IF v_num IS NULL OR v_num = '' THEN
                RAISE EXCEPTION 'Tidak dapat membaca nomor urut dari dokumen: %', v_new_docno;
            END IF;

            v_num_int   := v_num::BIGINT + 1;
            v_new_docno := v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');

            IF length(v_new_docno) > 30 THEN
                RAISE EXCEPTION 'Nomor Penjualan melebihi 30 karakter: %', v_new_docno;
            END IF;
        END LOOP;

        v_docno := v_new_docno;

        SELECT COALESCE(MAX(idurut_view), 0) + 1 INTO v_new_view
        FROM sc_trx.penjualan;

        -- Insert HEADER
        INSERT INTO sc_trx.penjualan (
            idurut_view, docno, docnoso, docnodo, idrev, cabang, docdate,
            pemohon, kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            carabayar, kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total, keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount,
            docnotmp, doctype
        )
        SELECT
            v_new_view, v_docno, docnoso, docnodo, idrev, cabang, docdate,
            pemohon, kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            carabayar, kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total, keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate,
            COALESCE(printcount, 0), docnotmp,
            COALESCE(doctype, 'SALES')
        FROM sc_tmp.penjualan
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header temporary PJO tidak ditemukan. docno=%, inputby=%, idurut=%',
                OLD.docno, v_inputby, v_idurut;
        END IF;

        -- Insert DETAIL
        INSERT INTO sc_trx.penjualan_dtl (
            idurut_view, docno,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            uniqueid,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtyretur,
            stokakhir, bomdesc, description, etd, eta,
            inputby, inputdate, status, updateby, updatedate,
            doctype, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(idurut_view, idurut), idurut)::INTEGER,
            v_docno,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            uniqueid,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, COALESCE(qtyretur, 0),
            stokakhir, bomdesc, description, etd, eta,
            inputby, inputdate, status, updateby, updatedate,
            COALESCE(doctype, 'SALES'), docnotmp
        FROM sc_tmp.penjualan_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        -- *** TIDAK ADA UPDATE KE sc_trx.salesorder / salesorder_dtl ***
        -- Outstanding SO dihitung via view/query terpisah.

        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30), NULL, 'I.S', 'I.S.B.4', 'I',
            v_inputby, v_client_ip, v_inputby
        );

        DELETE FROM sc_tmp.penjualan_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno) AND inputby = v_inputby;

        DELETE FROM sc_tmp.penjualan
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- ===================== DOCNOTMP FLOW E -> F =========================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND v_docnotmp IS NOT NULL THEN

        -- Hapus transaction_dt lama (DELIVR + SALESX)
        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = v_docnotmp
          AND journal_type IN ('DELIVR', 'SALESX');

        DELETE FROM sc_trx.penjualan_dtl WHERE BTRIM(docno) = v_docnotmp;
        DELETE FROM sc_trx.penjualan     WHERE BTRIM(docno) = v_docnotmp;

        SELECT COALESCE(MAX(idurut_view), 0) + 1 INTO v_new_view
        FROM sc_trx.penjualan;

        INSERT INTO sc_trx.penjualan (
            idurut_view, docno, docnoso, docnodo, idrev, cabang, docdate,
            pemohon, kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            carabayar, kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total, keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount,
            docnotmp, doctype
        )
        SELECT
            v_new_view, v_docnotmp, docnoso, docnodo, idrev, cabang, docdate,
            pemohon, kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            carabayar, kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total, keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate,
            COALESCE(printcount, 0), docnotmp,
            COALESCE(doctype, 'SALES')
        FROM sc_tmp.penjualan
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Header temporary PJO DOCNOTMP tidak ditemukan: %', NEW.docno;
        END IF;

        INSERT INTO sc_trx.penjualan_dtl (
            idurut_view, docno,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            uniqueid,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtyretur,
            stokakhir, bomdesc, description, etd, eta,
            inputby, inputdate, status, updateby, updatedate,
            doctype, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(idurut_view, idurut), idurut)::INTEGER,
            v_docnotmp,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            uniqueid,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, COALESCE(qtyretur, 0),
            stokakhir, bomdesc, description, etd, eta,
            inputby, inputdate, status, updateby, updatedate,
            COALESCE(doctype, 'SALES'), docnotmp
        FROM sc_tmp.penjualan_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        -- *** TIDAK ADA UPDATE KE sc_trx.salesorder / salesorder_dtl ***

        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.4', 'U',
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.penjualan_dtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.penjualan     WHERE BTRIM(docno) = BTRIM(NEW.docno);

    -- ============================ CANCEL E -> C =========================
    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN

        IF NEW.printby IS NOT NULL
           AND BTRIM(NEW.printby) <> ''
           AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.penjualan SET status = 'P' WHERE BTRIM(docno) = v_docnotmp;
        ELSE
            UPDATE sc_trx.penjualan SET status = 'F' WHERE BTRIM(docno) = v_docnotmp;
        END IF;

        DELETE FROM sc_tmp.penjualan_dtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.penjualan     WHERE BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$BODY$;

-- ============================================================================
-- 6. FUNCTION: sc_trx.tr_penjualan()
--    AFTER UPDATE ON sc_trx.penjualan
--    TANPA update ke sc_trx.salesorder / salesorder_dtl.
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.tr_penjualan()
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

    -- ============================ F -> C ================================
    IF OLD.status = 'F' AND NEW.status = 'C' THEN

        -- *** TIDAK ADA UPDATE KE sc_trx.salesorder / salesorder_dtl ***

        -- Hapus transaction_dt (DELIVR + SALESX)
        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(NEW.docno)
          AND journal_type IN ('DELIVR', 'SALESX');

        PERFORM sc_log.fn_log_transaction(
            NEW.docno, NULL, 'I.S', 'I.S.B.4', 'C',
            v_inputby, v_client_ip, v_inputby
        );
    END IF;

    -- ============================ F -> E ================================
    IF OLD.status = 'F' AND NEW.status = 'E' THEN

        -- Hapus transaction_dt lama (DELIVR + SALESX)
        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(NEW.docno)
          AND journal_type IN ('DELIVR', 'SALESX');

        DELETE FROM sc_tmp.penjualan_dtl WHERE BTRIM(docno) = BTRIM(NEW.docno);
        DELETE FROM sc_tmp.penjualan     WHERE BTRIM(docno) = BTRIM(NEW.docno);

        INSERT INTO sc_tmp.penjualan (
            idurut_view, docno, docnoso, docnodo, idrev, cabang, docdate,
            pemohon, kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv,
            carabayar, kdcustomer, nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total, keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount,
            docnotmp, doctype
        )
        SELECT
            COALESCE(h.idurut_view, h.idurut),
            h.docno, h.docnoso, h.docnodo, h.idrev, h.cabang, h.docdate,
            h.pemohon, h.kdcustomerdeliv, h.nmcustomerdeliv, h.alamatcustomerdeliv,
            h.carabayar, h.kdcustomer, h.nmcustomer, h.alamatcustomer, h.jthtempo,
            h.isopenprice, h.kdsalesman, h.gradecustomer,
            h.idtax, h.isinclusive, h.currcode, h.kurs, h.dpp,
            h.jumlahpajak, h.total, h.keterangan, h.status, h.inputby, h.inputdate,
            h.updateby, h.updatedate, h.printby, h.printdate,
            COALESCE(h.printcount, 0), h.docnotmp,
            COALESCE(h.doctype, 'SALES')
        FROM sc_trx.penjualan h
        WHERE BTRIM(h.docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header transaksi PJO tidak ditemukan saat revisi F->E: %',
                NEW.docno;
        END IF;

        INSERT INTO sc_tmp.penjualan_dtl (
            idurut_view, docno,
            docnoso, idrev, idurutso, uniqueidso,
            docnodo, idurutdo, uniqueiddo,
            uniqueid,
            idbarangref, nmbarangref, idbarang, nmbarang,
            idprincipal, idgudang, idspec, unit,
            qtyref, hargasatuanref,
            qty, harga, hargasatuan, multidisc, nilai,
            idtax, currcode, kurs, nilaikonversi, nilaipajak, qtyretur,
            stokakhir, bomdesc, description, etd, eta,
            inputby, inputdate, status, updateby, updatedate,
            doctype, docnotmp
        )
        SELECT
            ROW_NUMBER() OVER (ORDER BY COALESCE(d.idurut_view, d.idurut), d.idurut)::INTEGER,
            NEW.docno,
            d.docnoso, d.idrev, d.idurutso, d.uniqueidso,
            d.docnodo, d.idurutdo, d.uniqueiddo,
            d.uniqueid,
            d.idbarangref, d.nmbarangref, d.idbarang, d.nmbarang,
            d.idprincipal, d.idgudang, d.idspec, d.unit,
            d.qtyref, d.hargasatuanref,
            d.qty, d.harga, d.hargasatuan, d.multidisc, d.nilai,
            d.idtax, d.currcode, d.kurs, d.nilaikonversi, d.nilaipajak,
            COALESCE(d.qtyretur, 0),
            d.stokakhir, d.bomdesc, d.description, d.etd, d.eta,
            d.inputby, d.inputdate, COALESCE(d.status, 'F'),
            d.updateby, d.updatedate,
            COALESCE(d.doctype, 'SALES'),
            COALESCE(NULLIF(BTRIM(d.docnotmp), ''), NEW.docno)
        FROM sc_trx.penjualan_dtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_trx.tr_penjualan() OWNER TO postgres;

-- ============================================================================
-- 7. FUNCTION: sc_trx.fn_sync_penjualan_dtl_to_delivr()
--    Insert/update ke transaction_dt untuk journal_type='DELIVR' (STOCK)
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_sync_penjualan_dtl_to_delivr(
    p_docno TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_pjo         sc_trx.penjualan%ROWTYPE;
    v_dtl         sc_trx.penjualan_dtl%ROWTYPE;
    v_jt          sc_mst.journal_type%ROWTYPE;
    v_tx_uid      TEXT;
    v_stock_uid   TEXT;
    v_stock_key   CHAR(32);
    v_idbranch    CHAR(20);
    v_nilai       NUMERIC(18,6);
    v_dpp         NUMERIC(18,6);
    v_pajak       NUMERIC(18,6);
    v_bruto       NUMERIC(18,6);
BEGIN
    SELECT * INTO v_pjo
    FROM sc_trx.penjualan
    WHERE BTRIM(docno) = BTRIM(p_docno);
    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_dtl
    FROM sc_trx.penjualan_dtl
    WHERE BTRIM(docno) = BTRIM(p_docno)
      AND uniqueid = p_uniqueid;
    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_jt
    FROM sc_mst.journal_type
    WHERE journal_type = 'DELIVR';

    v_idbranch := CASE rtrim(v_pjo.cabang)
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE rtrim(v_pjo.cabang)::CHAR(20)
    END;

    -- Stock identity (TAHAP 01)
    v_stock_uid := sc_trx.fn_stock_uniqueid(
        v_idbranch, v_dtl.idgudang, '', v_dtl.idbarang,
        v_dtl.unit, v_dtl.idspec, ''
    );
    v_stock_key := sc_trx.fn_stock_key(
        v_idbranch, v_dtl.idgudang, '', v_dtl.idbarang,
        v_dtl.unit, v_dtl.idspec, ''
    );

    v_dpp   := COALESCE(v_dtl.nilaikonversi, 0);
    v_pajak := COALESCE(v_dtl.nilaipajak, 0);
    v_nilai := v_dpp + v_pajak;
    v_bruto := v_nilai;

    v_tx_uid := md5(BTRIM(p_docno) || '|DELIVR|' || COALESCE(v_dtl.uniqueid, ''));

    INSERT INTO sc_trx.transaction_dt (
        uniqueid, source_uniqueid, docno, doctype, journal_type,
        line_no, docdate, idbranch, cabang, type_in_out,
        ref_docno, ref_doctype,
        kdcustomer, ncustomer,
        idbarang, namabarang, idunit, idarea, warehouse,
        bin, batch, lotno,
        qty, harga, bruto, discount, nilai, dpp, pajak, total,
        idtax, isinclusive, currcode, kurs,
        currcode_trans, kurs_trans, nilai_trans,
        module, direction, stock_effect, accounting_effect, asset_effect,
        stock_uniqueid, stock_key,
        createdby
    )
    VALUES (
        v_tx_uid, COALESCE(v_dtl.uniqueid, ''),
        rtrim(p_docno), 'SALES', 'DELIVR',
        v_dtl.idurut, v_pjo.docdate, v_idbranch, rtrim(v_pjo.cabang), 'OUT',
        COALESCE(rtrim(v_dtl.docnoso), rtrim(v_dtl.docnodo)), 'PJO',
        rtrim(v_pjo.kdcustomer), rtrim(v_pjo.nmcustomer),
        rtrim(v_dtl.idbarang), rtrim(v_dtl.nmbarang), rtrim(v_dtl.unit),
        rtrim(v_dtl.idgudang), rtrim(v_dtl.idgudang),
        '', rtrim(v_dtl.idspec), '',
        v_dtl.qty, v_dtl.harga,
        v_bruto,
        COALESCE(v_dtl.multidisc, 0),
        v_nilai, v_dpp, v_pajak, v_nilai,
        rtrim(v_dtl.idtax), rtrim(v_pjo.isinclusive),
        rtrim(v_pjo.currcode), v_pjo.kurs,
        rtrim(v_pjo.currcode), v_pjo.kurs, COALESCE(v_dtl.nilai, 0),
        TRIM(v_jt.module), TRIM(v_jt.direction),
        TRIM(v_jt.stock_effect), TRIM(v_jt.accounting_effect),
        TRIM(v_jt.asset_effect),
        v_stock_uid, v_stock_key,
        rtrim(v_pjo.inputby)
    )
    ON CONFLICT (uniqueid) DO UPDATE
    SET
        qty             = EXCLUDED.qty,
        harga           = EXCLUDED.harga,
        bruto           = EXCLUDED.bruto,
        discount        = EXCLUDED.discount,
        nilai           = EXCLUDED.nilai,
        dpp             = EXCLUDED.dpp,
        pajak           = EXCLUDED.pajak,
        total           = EXCLUDED.total,
        stock_uniqueid  = EXCLUDED.stock_uniqueid,
        stock_key       = EXCLUDED.stock_key,
        updatedby       = EXCLUDED.createdby,
        updateddate     = CURRENT_TIMESTAMP;
END;
$$;

-- ============================================================================
-- 8. FUNCTION: sc_trx.fn_sync_penjualan_dtl_to_salesx()
--    Insert/update ke transaction_dt untuk journal_type='SALESX' (ACCOUNTING)
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_sync_penjualan_dtl_to_salesx(
    p_docno TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_pjo         sc_trx.penjualan%ROWTYPE;
    v_dtl         sc_trx.penjualan_dtl%ROWTYPE;
    v_jt          sc_mst.journal_type%ROWTYPE;
    v_tx_uid      TEXT;
    v_idbranch    CHAR(20);
    v_idcoa_ar    TEXT;
    v_nilai       NUMERIC(18,6);
    v_dpp         NUMERIC(18,6);
    v_pajak       NUMERIC(18,6);
    v_bruto       NUMERIC(18,6);
BEGIN
    SELECT * INTO v_pjo
    FROM sc_trx.penjualan
    WHERE BTRIM(docno) = BTRIM(p_docno);
    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_dtl
    FROM sc_trx.penjualan_dtl
    WHERE BTRIM(docno) = BTRIM(p_docno)
      AND uniqueid = p_uniqueid;
    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_jt
    FROM sc_mst.journal_type
    WHERE journal_type = 'SALESX';

    v_idbranch := CASE rtrim(v_pjo.cabang)
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE rtrim(v_pjo.cabang)::CHAR(20)
    END;

    -- Resolve primary COA (AR) via TAHAP 04
    v_idcoa_ar := sc_trx.fn_resolve_transaction_coa(
        'SALESX',
        rtrim(v_dtl.idbarang),
        rtrim(v_pjo.currcode),
        rtrim(v_dtl.idtax),
        'OUT',
        NULL
    );

    v_dpp   := COALESCE(v_dtl.nilaikonversi, 0);
    v_pajak := COALESCE(v_dtl.nilaipajak, 0);
    v_nilai := v_dpp + v_pajak;
    v_bruto := v_nilai;

    v_tx_uid := md5(BTRIM(p_docno) || '|SALESX|' || COALESCE(v_dtl.uniqueid, ''));

    INSERT INTO sc_trx.transaction_dt (
        uniqueid, source_uniqueid, docno, doctype, journal_type,
        line_no, docdate, idbranch, cabang, type_in_out,
        ref_docno, ref_doctype,
        kdcustomer, ncustomer,
        idbarang, namabarang, idunit, idarea, warehouse,
        bin, batch, lotno,
        qty, harga, bruto, discount, nilai, dpp, pajak, total,
        idtax, isinclusive, currcode, kurs,
        currcode_trans, kurs_trans, nilai_trans,
        idcoa, counter_idcoa, debet_kredit,
        module, direction, stock_effect, accounting_effect, asset_effect,
        createdby
    )
    VALUES (
        v_tx_uid, COALESCE(v_dtl.uniqueid, ''),
        rtrim(p_docno), 'SALES', 'SALESX',
        v_dtl.idurut, v_pjo.docdate, v_idbranch, rtrim(v_pjo.cabang), 'OUT',
        COALESCE(rtrim(v_dtl.docnoso), rtrim(v_dtl.docnodo)), 'PJO',
        rtrim(v_pjo.kdcustomer), rtrim(v_pjo.nmcustomer),
        rtrim(v_dtl.idbarang), rtrim(v_dtl.nmbarang), rtrim(v_dtl.unit),
        rtrim(v_dtl.idgudang), rtrim(v_dtl.idgudang),
        '', rtrim(v_dtl.idspec), '',
        v_dtl.qty, v_dtl.harga,
        v_bruto,
        COALESCE(v_dtl.multidisc, 0),
        v_nilai, v_dpp, v_pajak, v_nilai,
        rtrim(v_dtl.idtax), rtrim(v_pjo.isinclusive),
        rtrim(v_pjo.currcode), v_pjo.kurs,
        rtrim(v_pjo.currcode), v_pjo.kurs, COALESCE(v_dtl.nilai, 0),
        COALESCE(v_idcoa_ar, ''), '', 'D',
        TRIM(v_jt.module), TRIM(v_jt.direction),
        TRIM(v_jt.stock_effect), TRIM(v_jt.accounting_effect),
        TRIM(v_jt.asset_effect),
        rtrim(v_pjo.inputby)
    )
    ON CONFLICT (uniqueid) DO UPDATE
    SET
        qty             = EXCLUDED.qty,
        harga           = EXCLUDED.harga,
        bruto           = EXCLUDED.bruto,
        discount        = EXCLUDED.discount,
        nilai           = EXCLUDED.nilai,
        dpp             = EXCLUDED.dpp,
        pajak           = EXCLUDED.pajak,
        total           = EXCLUDED.total,
        idcoa           = EXCLUDED.idcoa,
        updatedby       = EXCLUDED.createdby,
        updateddate     = CURRENT_TIMESTAMP;
END;
$$;

-- ============================================================================
-- 9. TRIGGER: jurnal (AFTER INSERT/UPDATE ON sc_trx.penjualan_dtl)
--    Memanggil 2 fungsi: DELIVR (stock) + SALESX (accounting)
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_penjualan_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP IN ('INSERT', 'UPDATE') THEN
        -- 1) Stock movement (DELIVR)
        PERFORM sc_trx.fn_sync_penjualan_dtl_to_delivr(NEW.docno, NEW.uniqueid);
        -- 2) Accounting (SALESX)
        PERFORM sc_trx.fn_sync_penjualan_dtl_to_salesx(NEW.docno, NEW.uniqueid);
        RETURN NEW;
    END IF;
    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_penjualan_dtl_to_transaction
AFTER INSERT OR UPDATE
ON sc_trx.penjualan_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_penjualan_dtl_to_transaction();

-- ============================================================================
-- 10. PASANG TRIGGER UTAMA
-- ============================================================================
CREATE TRIGGER tr_penjualan_finalize
    AFTER UPDATE ON sc_tmp.penjualan
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_penjualan_finalize();

CREATE TRIGGER tr_penjualan
    AFTER UPDATE ON sc_trx.penjualan
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_penjualan();

-- ============================================================================
-- 11. VIEW OUTSTANDING SO (query terpisah, pengganti update SO)
-- ============================================================================
-- COMPATIBILITY PATCH:
-- View versi sebelumnya menggunakan nama kolom qty_do.
-- Rename secara eksplisit sebelum CREATE OR REPLACE VIEW.
-- DO block aman dijalankan berulang dan tidak error jika view/kolom belum ada.
DO $$
BEGIN
    IF to_regclass('sc_trx.v_so_outstanding') IS NOT NULL
       AND EXISTS (
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'sc_trx'
              AND table_name = 'v_so_outstanding'
              AND column_name = 'qty_do'
       )
       AND NOT EXISTS (
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'sc_trx'
              AND table_name = 'v_so_outstanding'
              AND column_name = 'qty_pjo'
       )
    THEN
        ALTER VIEW sc_trx.v_so_outstanding
            RENAME COLUMN qty_do TO qty_pjo;
    END IF;
END;
$$;

CREATE OR REPLACE VIEW sc_trx.v_so_outstanding AS
WITH pjo_source AS (
    -- PJO final/print
    SELECT
        pjo.uniqueidso,
        pjo.qty
    FROM sc_trx.penjualan_dtl pjo
    JOIN sc_trx.penjualan h
         ON BTRIM(h.docno) = BTRIM(pjo.docno)
    WHERE BTRIM(h.status) IN ('F', 'P')
      AND pjo.uniqueidso IS NOT NULL
      AND pjo.uniqueidso <> ''

    UNION ALL

    -- PJO draft di sc_tmp (prioritas jika sedang diedit)
    SELECT
        t.uniqueidso,
        t.qty
    FROM sc_tmp.penjualan_dtl t
    WHERE t.uniqueidso IS NOT NULL
      AND t.uniqueidso <> ''
),
pjo_sum AS (
    SELECT uniqueidso, SUM(qty) AS qty_pjo
    FROM pjo_source
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
    COALESCE(ps.qty_pjo, 0)       AS qty_pjo,
    sod.qty - COALESCE(ps.qty_pjo, 0) AS qty_outstanding
FROM sc_trx.salesorder_dtl sod
LEFT JOIN pjo_sum ps ON ps.uniqueidso = sod.uniqueid
WHERE sod.status IN ('F', 'DO', 'P', 'PJO');

COMMIT;

-- ============================================================================
-- 12. VERIFIKASI (jalankan manual setelah COMMIT)
-- ============================================================================
-- -- Cek trigger:
-- SELECT n.nspname AS schema_name, c.relname AS table_name,
--        t.tgname AS trigger_name, pg_get_triggerdef(t.oid) AS trigger_definition
-- FROM pg_trigger t
-- JOIN pg_class c ON c.oid = t.tgrelid
-- JOIN pg_namespace n ON n.oid = c.relnamespace
-- WHERE NOT t.tgisinternal
--   AND n.nspname IN ('sc_tmp','sc_trx')
--   AND c.relname IN ('penjualan','penjualan_dtl')
-- ORDER BY n.nspname, c.relname, t.tgname;
--
-- -- Cek jurnal per dokumen PJO (harus ada 2 baris per detail):
-- SELECT docno, journal_type, stock_effect, accounting_effect,
--        idcoa, qty, nilai
-- FROM sc_trx.transaction_dt
-- WHERE BTRIM(docno) = 'PJO/2601/PA0001'
-- ORDER BY line_no, journal_type;
--
-- -- Cek outstanding SO:
-- SELECT * FROM sc_trx.v_so_outstanding
-- WHERE docnoso = 'SO/2601/PA0001'
--   AND qty_outstanding > 0
-- ORDER BY idurutso;
--
-- -- Contoh query tampilan:
-- SELECT * FROM sc_trx.penjualan     ORDER BY idurut_view, idurut;
-- SELECT * FROM sc_trx.penjualan_dtl WHERE docno = ? ORDER BY idurut_view, idurut;