-- Revisi Pengeluaran Kas/Bank:
-- 1) nilaiakhir disimpan hanya di pengeluarankb_dtl (TMP dan TRX).
-- 2) Nilai jurnal penggunaan diambil dari COALESCE(d.nilaiakhir, d.nilai).
-- 3) Tidak menambah/mengubah kolom sc_trx.transaction_dt.
-- 4) Tidak DROP tabel, sehingga data existing tidak dihapus.
BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_tmp;
CREATE SCHEMA IF NOT EXISTS sc_trx;

-- Lepas trigger lama hanya jika tabel sumber sudah ada.
DO $$
BEGIN
    IF to_regclass('sc_tmp.pengeluarankb') IS NOT NULL THEN
        EXECUTE 'DROP TRIGGER IF EXISTS pengeluarankb_finalize ON sc_tmp.pengeluarankb';
    END IF;
    IF to_regclass('sc_trx.pengeluarankb') IS NOT NULL THEN
        EXECUTE 'DROP TRIGGER IF EXISTS pengeluarankb ON sc_trx.pengeluarankb';
    END IF;
END;
$$;

-- Aman untuk data existing: tidak menghapus tabel atau data transaksi.

-- =========================================================
-- HEADER TEMPORARY
-- =========================================================
CREATE TABLE IF NOT EXISTS sc_tmp.pengeluarankb
(
    idurut       SERIAL NOT NULL,
    docno        CHARACTER(30) NOT NULL,
    docdate      CHARACTER(20),
    cabang       CHARACTER(30),
    pemohon      CHARACTER(100),
    kdsupplier   CHARACTER(30),
    nmsupplier   CHARACTER(250),
    alamatsupplier TEXT,
    prkkas       CHARACTER(20),
    np           CHARACTER(20),
    jthtempo     NUMERIC(18,2),
    isinclusive  CHARACTER(6),
    currcode     CHARACTER(3),
    kurs         NUMERIC(18,2),
    dpp          NUMERIC(18,2),
    jumlahpajak  NUMERIC(18,2),
    total        NUMERIC(18,2),
    status       CHARACTER(6),
    keterangan   TEXT,
    balance      NUMERIC(18,2),
    inputby      VARCHAR(50),
    inputdate    TIMESTAMP WITHOUT TIME ZONE,
    updateby     VARCHAR(50),
    updatedate   TIMESTAMP WITHOUT TIME ZONE,
    printby      VARCHAR(50),
    printdate    TIMESTAMP WITHOUT TIME ZONE,
    docnotmp     CHARACTER(30),
    CONSTRAINT pk_tmp_pengeluarankb PRIMARY KEY (idurut, docno)
);

-- =========================================================
-- HEADER TRANSAKSI
-- =========================================================
CREATE TABLE IF NOT EXISTS sc_trx.pengeluarankb
(
    idurut       SERIAL NOT NULL,
    docno        CHARACTER(30) NOT NULL,
    docdate      CHARACTER(20),
    cabang       CHARACTER(30),
    pemohon      CHARACTER(100),
    kdsupplier   CHARACTER(30),
    nmsupplier   CHARACTER(250),
    alamatsupplier TEXT,
    prkkas       CHARACTER(20),
    np           CHARACTER(20),
    jthtempo     NUMERIC(18,2),
    isinclusive  CHARACTER(6),
    currcode     CHARACTER(3),
    kurs         NUMERIC(18,2),
    dpp          NUMERIC(18,2),
    jumlahpajak  NUMERIC(18,2),
    total        NUMERIC(18,2),
    status       CHARACTER(6),
    keterangan   TEXT,
    balance      NUMERIC(18,2),
    inputby      VARCHAR(50),
    inputdate    TIMESTAMP WITHOUT TIME ZONE,
    updateby     VARCHAR(50),
    updatedate   TIMESTAMP WITHOUT TIME ZONE,
    printby      VARCHAR(50),
    printdate    TIMESTAMP WITHOUT TIME ZONE,
    docnotmp     CHARACTER(30),
    CONSTRAINT pk_trx_pengeluarankb PRIMARY KEY (idurut, docno)
);

-- =========================================================
-- DETAIL TEMPORARY
-- =========================================================
CREATE TABLE IF NOT EXISTS sc_tmp.pengeluarankb_dtl
(
    idurut      SERIAL PRIMARY KEY,
    docno       CHARACTER(30) NOT NULL,
    nobukti     CHARACTER(30),
    idcoa       CHARACTER(20),
    nmcoa       CHARACTER(250),
    remarks     TEXT,
    dk          CHARACTER(20),
    cabang      CHARACTER(30),
    nilai       NUMERIC(18,2),
    nilaiakhir  NUMERIC(18,2),
    currcode    CHARACTER(3),
    kurs        NUMERIC(18,2),
    uniqueid    VARCHAR(64),
    status      CHARACTER(6),
    inputby     VARCHAR(50),
    inputdate   TIMESTAMP WITHOUT TIME ZONE,
    updateby    VARCHAR(50),
    updatedate  TIMESTAMP WITHOUT TIME ZONE,
    docnotmp    CHARACTER(30),
    jurnalref   VARCHAR(30) DEFAULT 'NO',
    nilai_ref   NUMERIC(18,2) DEFAULT 0
);

-- =========================================================
-- DETAIL TRANSAKSI
-- =========================================================
CREATE TABLE IF NOT EXISTS sc_trx.pengeluarankb_dtl
(
    idurut      SERIAL PRIMARY KEY,
    docno       CHARACTER(30) NOT NULL,
    nobukti     CHARACTER(30),
    idcoa       CHARACTER(20),
    nmcoa       CHARACTER(250),
    remarks     TEXT,
    dk          CHARACTER(20),
    cabang      CHARACTER(30),
    nilai       NUMERIC(18,2),
    nilaiakhir  NUMERIC(18,2),
    currcode    CHARACTER(3),
    kurs        NUMERIC(18,2),
    uniqueid    VARCHAR(64),
    status      CHARACTER(6),
    inputby     VARCHAR(50),
    inputdate   TIMESTAMP WITHOUT TIME ZONE,
    updateby    VARCHAR(50),
    updatedate  TIMESTAMP WITHOUT TIME ZONE,
    docnotmp    CHARACTER(30),
    jurnalref   VARCHAR(30) DEFAULT 'NO',
    nilai_ref   NUMERIC(18,2) DEFAULT 0
);


ALTER TABLE sc_tmp.pengeluarankb ADD COLUMN IF NOT EXISTS nmprkkas CHARACTER(100);
ALTER TABLE sc_trx.pengeluarankb ADD COLUMN IF NOT EXISTS nmprkkas CHARACTER(100);
ALTER TABLE sc_tmp.pengeluarankb_dtl ADD COLUMN IF NOT EXISTS nilaiakhir NUMERIC(18,2);
ALTER TABLE sc_trx.pengeluarankb_dtl ADD COLUMN IF NOT EXISTS nilaiakhir NUMERIC(18,2);
ALTER TABLE sc_tmp.pengeluarankb_dtl ADD COLUMN IF NOT EXISTS currcode CHARACTER(3);
ALTER TABLE sc_tmp.pengeluarankb_dtl ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,2);
ALTER TABLE sc_trx.pengeluarankb_dtl ADD COLUMN IF NOT EXISTS currcode CHARACTER(3);
ALTER TABLE sc_trx.pengeluarankb_dtl ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,2);

-- =========================================================
-- FINALISASI TMP: E -> F
-- =========================================================


CREATE OR REPLACE FUNCTION sc_tmp.pengeluarankb_finalize()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno       TEXT;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_num         TEXT;
    v_num_int     BIGINT;
BEGIN
    -- =====================================================
    -- FINALISASI BARU: E -> F
    -- =====================================================
    IF OLD.status = 'E'
       AND NEW.status = 'F'
       AND NULLIF(BTRIM(NEW.docnotmp), '') IS NULL THEN

        v_docno := BTRIM(NEW.docno);
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_num := substring(v_docno FROM '([0-9]+)$');

        IF v_num IS NULL THEN
            RAISE EXCEPTION
                'Nomor dokumen % harus diakhiri angka', v_docno;
        END IF;

        PERFORM pg_advisory_xact_lock(
            hashtext(v_base_docno)::BIGINT
        );

        v_new_docno := v_docno;

        WHILE EXISTS (
            SELECT 1
            FROM sc_trx.pengeluarankb h
            WHERE BTRIM(h.docno) = v_new_docno
        ) LOOP
            v_num := substring(v_new_docno FROM '([0-9]+)$');
            v_num_int := v_num::BIGINT + 1;

            v_new_docno := v_base_docno ||
                lpad(v_num_int::TEXT, length(v_num), '0');

            IF length(v_new_docno) > 30 THEN
                RAISE EXCEPTION
                    'Nomor dokumen melebihi 30 karakter: %',
                    v_new_docno;
            END IF;
        END LOOP;

        -- HEADER FINAL
        INSERT INTO sc_trx.pengeluarankb (
            idurut, docno, docdate, cabang, pemohon,
            kdsupplier, nmsupplier, alamatsupplier,
            prkkas, np, jthtempo, isinclusive, currcode, kurs,
            dpp, jumlahpajak, total, status, keterangan, balance,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, nmprkkas
        )
        SELECT
            h.idurut, v_new_docno, h.docdate, h.cabang, h.pemohon,
            h.kdsupplier, h.nmsupplier, h.alamatsupplier,
            h.prkkas, h.np, h.jthtempo, h.isinclusive, h.currcode,
            h.kurs, h.dpp, h.jumlahpajak, h.total, 'F',
            h.keterangan, h.balance, h.inputby, h.inputdate,
            h.updateby, h.updatedate, h.printby, h.printdate,
            h.docnotmp, h.nmprkkas
        FROM sc_tmp.pengeluarankb h
        WHERE h.idurut = OLD.idurut
          AND BTRIM(h.docno) = BTRIM(OLD.docno);

        -- DETAIL FINAL
        INSERT INTO sc_trx.pengeluarankb_dtl (
            docno, nobukti, idcoa, nmcoa, remarks, dk, cabang,
            nilai, nilaiakhir, currcode, kurs, uniqueid, status, inputby, inputdate,
            updateby, updatedate, docnotmp, jurnalref, nilai_ref
        )
        SELECT
            v_new_docno, d.nobukti, d.idcoa, d.nmcoa,
            d.remarks, d.dk, d.cabang, d.nilai,
            COALESCE(d.nilaiakhir, d.nilai), d.currcode, d.kurs, d.uniqueid,
            'F', d.inputby, d.inputdate, d.updateby,
            d.updatedate, d.docnotmp, d.jurnalref,
            COALESCE(d.nilai_ref, 0)
        FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
          AND COALESCE(BTRIM(d.inputby), '') =
              COALESCE(BTRIM(OLD.inputby), '');

        -- =================================================
        -- JURNAL HD PENGGUNAAN
        -- Hanya detail dengan jurnalref = YES
        -- =================================================
        INSERT INTO sc_trx.jurnal_hd_penggunaan (
            docno,
            idunique,
            nobukti,
            docref,
            noperkiraan,
            namaperkiraan,
            keterangan,
            dk,
            costprofitcenter,
            nilai,
            status,
            inputby,
            inputdate
        )
        SELECT
            v_new_docno,
            d.uniqueid,
            d.nobukti,
            v_new_docno,
            d.idcoa,
            d.nmcoa,
            d.remarks,
            d.dk,
            d.cabang,
            COALESCE(d.nilaiakhir, d.nilai),
            'P',
            d.inputby,
            d.inputdate
        FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
          AND COALESCE(BTRIM(d.inputby), '') =
              COALESCE(BTRIM(OLD.inputby), '')
          AND UPPER(BTRIM(COALESCE(d.jurnalref, 'NO'))) = 'YES';

        -- HAPUS DETAIL TEMPORARY
        DELETE FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
          AND COALESCE(BTRIM(d.inputby), '') =
              COALESCE(BTRIM(OLD.inputby), '');

        -- HAPUS HEADER TEMPORARY
        DELETE FROM sc_tmp.pengeluarankb h
        WHERE h.idurut = OLD.idurut
          AND BTRIM(h.docno) = BTRIM(OLD.docno);

    -- =====================================================
    -- REVISI TRANSAKSI LAMA: E -> F
    -- =====================================================
    ELSIF OLD.status = 'E'
       AND NEW.status = 'F'
       AND NULLIF(BTRIM(NEW.docnotmp), '') IS NOT NULL THEN

        -- Hapus detail, jurnal, dan header lama.
        DELETE FROM sc_trx.jurnal_hd_penggunaan
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_trx.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_trx.pengeluarankb
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        -- HEADER REVISI
        INSERT INTO sc_trx.pengeluarankb (
            idurut, docno, docdate, cabang, pemohon,
            kdsupplier, nmsupplier, alamatsupplier,
            prkkas, np, jthtempo, isinclusive, currcode, kurs,
            dpp, jumlahpajak, total, status, keterangan, balance,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, nmprkkas
        )
        SELECT
            h.idurut, NEW.docnotmp, h.docdate, h.cabang, h.pemohon,
            h.kdsupplier, h.nmsupplier, h.alamatsupplier,
            h.prkkas, h.np, h.jthtempo, h.isinclusive, h.currcode,
            h.kurs, h.dpp, h.jumlahpajak, h.total, 'F',
            h.keterangan, h.balance, h.inputby, h.inputdate,
            h.updateby, h.updatedate, h.printby, h.printdate,
            h.docnotmp, h.nmprkkas
        FROM sc_tmp.pengeluarankb h
        WHERE h.idurut = NEW.idurut
          AND BTRIM(h.docno) = BTRIM(NEW.docno);

        -- DETAIL REVISI
        INSERT INTO sc_trx.pengeluarankb_dtl (
            docno, nobukti, idcoa, nmcoa, remarks, dk, cabang,
            nilai, nilaiakhir, currcode, kurs, uniqueid, status, inputby, inputdate,
            updateby, updatedate, docnotmp, jurnalref, nilai_ref
        )
        SELECT
            NEW.docnotmp, d.nobukti, d.idcoa, d.nmcoa,
            d.remarks, d.dk, d.cabang, d.nilai,
            COALESCE(d.nilaiakhir, d.nilai), d.currcode, d.kurs, d.uniqueid,
            'F', d.inputby, d.inputdate, d.updateby,
            d.updatedate, d.docnotmp, d.jurnalref,
            COALESCE(d.nilai_ref, 0)
        FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno)
          AND COALESCE(BTRIM(d.inputby), '') =
              COALESCE(BTRIM(NEW.inputby), '');

        -- JURNAL REVISI
        INSERT INTO sc_trx.jurnal_hd_penggunaan (
            docno,
            idunique,
            nobukti,
            docref,
            noperkiraan,
            namaperkiraan,
            keterangan,
            dk,
            costprofitcenter,
            nilai,
            status,
            inputby,
            inputdate
        )
        SELECT
            NEW.docnotmp,
            d.uniqueid,
            d.nobukti,
            NEW.docnotmp,
            d.idcoa,
            d.nmcoa,
            d.remarks,
            d.dk,
            d.cabang,
            COALESCE(d.nilaiakhir, d.nilai),
            'P',
            d.inputby,
            d.inputdate
        FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno)
          AND COALESCE(BTRIM(d.inputby), '') =
              COALESCE(BTRIM(NEW.inputby), '')
          AND UPPER(BTRIM(COALESCE(d.jurnalref, 'NO'))) = 'YES';

        -- HAPUS TEMPORARY REVISI
        DELETE FROM sc_tmp.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno)
          AND COALESCE(BTRIM(inputby), '') =
              COALESCE(BTRIM(NEW.inputby), '');

        DELETE FROM sc_tmp.pengeluarankb
        WHERE idurut = NEW.idurut
          AND BTRIM(docno) = BTRIM(NEW.docno);

    -- =====================================================
    -- PEMBATALAN / PENYELESAIAN: E -> C
    -- =====================================================
    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN

        UPDATE sc_trx.pengeluarankb
        SET status = CASE
            WHEN NULLIF(BTRIM(NEW.printby), '') IS NOT NULL
                 AND NEW.printdate IS NOT NULL THEN 'P'
            ELSE 'F'
        END
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_tmp.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno)
          AND COALESCE(BTRIM(inputby), '') =
              COALESCE(BTRIM(NEW.inputby), '');

        DELETE FROM sc_tmp.pengeluarankb
        WHERE idurut = NEW.idurut
          AND BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$$;



CREATE TRIGGER pengeluarankb_finalize
AFTER UPDATE OF status ON sc_tmp.pengeluarankb
FOR EACH ROW
WHEN (OLD.status IS DISTINCT FROM NEW.status)
EXECUTE FUNCTION sc_tmp.pengeluarankb_finalize();

-- =========================================================
-- EDIT TRANSAKSI: F -> E, salin header/detail ke TMP
-- =========================================================
CREATE OR REPLACE FUNCTION sc_trx.pengeluarankb()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF OLD.status = 'F' AND NEW.status = 'E' THEN

        -- Hindari duplikasi draft untuk dokumen yang sama.
        DELETE FROM sc_tmp.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.pengeluarankb
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        INSERT INTO sc_tmp.pengeluarankb (
            idurut, docno, docdate, cabang, pemohon,
            kdsupplier, nmsupplier, alamatsupplier,
            prkkas, np, jthtempo, isinclusive, currcode, kurs,
            dpp, jumlahpajak, total, status, keterangan, balance,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,nmprkkas
        )
        SELECT
            h.idurut, h.docno, h.docdate, h.cabang, h.pemohon,
            h.kdsupplier, h.nmsupplier, h.alamatsupplier,
            h.prkkas, h.np, h.jthtempo, h.isinclusive, h.currcode, h.kurs,
            h.dpp, h.jumlahpajak, h.total, 'E', h.keterangan, h.balance,
            h.inputby, h.inputdate, h.updateby, h.updatedate,
            h.printby, h.printdate, h.docno,h.nmprkkas
        FROM sc_trx.pengeluarankb h
        WHERE h.idurut = NEW.idurut
          AND BTRIM(h.docno) = BTRIM(NEW.docno);

        INSERT INTO sc_tmp.pengeluarankb_dtl (
            docno, nobukti, idcoa, nmcoa, remarks, dk, cabang,
            nilai, nilaiakhir, currcode, kurs, uniqueid, status, inputby, inputdate,
            updateby, updatedate, docnotmp, jurnalref, nilai_ref
        )
        SELECT
            d.docno, d.nobukti, d.idcoa, d.nmcoa, d.remarks, d.dk, d.cabang,
            d.nilai, COALESCE(d.nilaiakhir, d.nilai), d.currcode, d.kurs, d.uniqueid, d.status, d.inputby, d.inputdate,
            d.updateby, d.updatedate, NEW.docno, d.jurnalref, d.nilai_ref
        FROM sc_trx.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno);

    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER pengeluarankb
AFTER UPDATE OF status ON sc_trx.pengeluarankb
FOR EACH ROW
WHEN (OLD.status IS DISTINCT FROM NEW.status)
EXECUTE FUNCTION sc_trx.pengeluarankb();



-- =========================================================
-- PATCH: sinkronisasi Pengeluaran Kas/Bank ke transaction_dt
-- journal_type = BANKOT
-- nilaiakhir TIDAK ditambahkan sebagai kolom transaction_dt.
-- transaction_dt.nilai, dpp, dan total menggunakan pengeluarankb_dtl.nilaiakhir.
-- Jika nilaiakhir NULL, gunakan nilai sebagai fallback.
-- =========================================================

DO $$
BEGIN
    IF to_regclass('sc_trx.pengeluarankb') IS NULL
       OR to_regclass('sc_trx.pengeluarankb_dtl') IS NULL THEN
        RAISE EXCEPTION 'Tabel sc_trx.pengeluarankb dan/atau sc_trx.pengeluarankb_dtl belum tersedia.';
    END IF;

    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'sc_trx.transaction_dt belum tersedia. Jalankan tahap pembuatan transaction_dt terlebih dahulu.';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE BTRIM(journal_type::TEXT) = 'BANKOT'
    ) THEN
        RAISE EXCEPTION 'journal_type BANKOT belum tersedia di sc_mst.journal_type.';
    END IF;
END;
$$;

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_pengeluarankb_detail_BANKOT()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno       TEXT;
    v_uniqueid    TEXT;
    v_uid         TEXT;
    v_header      sc_trx.pengeluarankb%ROWTYPE;
    v_coa_detail  TEXT;
    v_coa_bank    TEXT;
    v_dc          CHAR(1);
    v_nilai       NUMERIC(18,2);
    v_docdate     DATE;
    v_line        INTEGER;
    v_desc        TEXT;
BEGIN
    IF TG_OP = 'DELETE' THEN
        v_docno := BTRIM(OLD.docno);
        v_uniqueid := COALESCE(NULLIF(BTRIM(OLD.uniqueid), ''), 'DETAIL-' || OLD.idurut::TEXT);
        v_uid := MD5(v_docno || '|' || v_uniqueid || '|pengeluarankb');

        DELETE FROM sc_trx.transaction_dt t
        WHERE t.uniqueid = v_uid
           OR (BTRIM(t.docno) = v_docno
               AND t.source_table = 'sc_trx.pengeluarankb_dtl'
               AND t.source_line_id = OLD.idurut
               AND t.journal_type = 'BANKOT');
        RETURN OLD;
    END IF;

    v_docno := BTRIM(NEW.docno);
    v_uniqueid := COALESCE(NULLIF(BTRIM(NEW.uniqueid), ''), 'DETAIL-' || NEW.idurut::TEXT);
    v_uid := MD5(v_docno || '|' || v_uniqueid || '|pengeluarankb');

    SELECT h.* INTO v_header
    FROM sc_trx.pengeluarankb h
    WHERE BTRIM(h.docno) = v_docno
    ORDER BY h.idurut DESC
    LIMIT 1;

    -- Header belum tersedia: jangan membuat transaction_dt orphan.
    IF NOT FOUND THEN
        RETURN NEW;
    END IF;

    v_coa_detail := NULLIF(BTRIM(COALESCE(NEW.idcoa, '')), '');
    v_coa_bank := NULLIF(BTRIM(COALESCE(v_header.prkkas, '')), '');

    IF v_coa_detail IS NULL THEN
        RAISE EXCEPTION 'COA detail kosong pada Pengeluaran KB %, detail idurut %.', v_docno, NEW.idurut;
    END IF;
    IF v_coa_bank IS NULL THEN
        RAISE EXCEPTION 'COA bank (prkkas) kosong pada Pengeluaran KB %.', v_docno;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM sc_mst.coa c WHERE BTRIM(c.idcoa::TEXT) = v_coa_detail) THEN
        RAISE EXCEPTION 'COA detail % tidak ditemukan di sc_mst.coa.', v_coa_detail;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM sc_mst.coa c WHERE BTRIM(c.idcoa::TEXT) = v_coa_bank) THEN
        RAISE EXCEPTION 'COA bank % tidak ditemukan di sc_mst.coa.', v_coa_bank;
    END IF;

    v_dc := CASE
        WHEN UPPER(BTRIM(COALESCE(NEW.dk, 'D'))) IN ('K', 'KREDIT', 'CREDIT', 'CR') THEN 'K'
        ELSE 'D'
    END;

    v_nilai := ROUND(COALESCE(NEW.nilaiakhir, NEW.nilai, 0), 2);
    IF v_nilai < 0 THEN
        RAISE EXCEPTION 'Nilai detail tidak boleh negatif: dokumen %, detail %.', v_docno, NEW.idurut;
    END IF;

    IF COALESCE(BTRIM(v_header.docdate), '') ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN
        v_docdate := BTRIM(v_header.docdate)::DATE;
    ELSIF COALESCE(BTRIM(v_header.docdate), '') ~ '^[0-9]{2}[-/][0-9]{2}[-/][0-9]{4}$' THEN
        v_docdate := TO_DATE(REPLACE(BTRIM(v_header.docdate), '/', '-'), 'DD-MM-YYYY');
    ELSE
        RAISE EXCEPTION 'Format docdate tidak dikenali pada Pengeluaran KB %: %', v_docno, v_header.docdate;
    END IF;

    v_line := GREATEST(COALESCE(NEW.idurut, 1), 1);
    v_desc := CONCAT_WS(' | ',
        NULLIF(BTRIM(COALESCE(NEW.remarks, '')), ''),
        NULLIF(BTRIM(COALESCE(NEW.nobukti, '')), ''),
        NULLIF(BTRIM(COALESCE(v_header.keterangan, '')), '')
    );

    INSERT INTO sc_trx.transaction_dt (
        uniqueid, source_uniqueid, docno, doctype, journal_type, line_no, docdate,
        idbranch, cabang, type_in_out, ref_docno, ref_doctype,
        source_table, source_id, source_line_id, kdsupplier, nsupplier,
        nilai, dpp, pajak, total, idtax, isinclusive, currcode, kurs,
        idcoa, counter_idcoa, debet_kredit,
        module, direction, stock_effect, accounting_effect, asset_effect,
        keterangan, createdby, createddate, updatedby, updateddate
    ) VALUES (
        v_uid, v_uniqueid, v_docno, 'PENGELUARANKB', 'BANKOT', v_line, v_docdate,
        BTRIM(COALESCE(v_header.cabang, '')), BTRIM(COALESCE(v_header.cabang, '')), 'OUT', '', '',
        'sc_trx.pengeluarankb_dtl', v_header.idurut, NEW.idurut,
        BTRIM(COALESCE(v_header.kdsupplier, '')), BTRIM(COALESCE(v_header.nmsupplier, '')),
        v_nilai, v_nilai, 0, v_nilai, 'NON',
        COALESCE(NULLIF(UPPER(BTRIM(v_header.isinclusive)), ''), 'NO'),
        COALESCE(NULLIF(BTRIM(NEW.currcode), ''), NULLIF(BTRIM(v_header.currcode), ''), 'IDR'),
         COALESCE(NULLIF(NEW.kurs, 0), NULLIF(v_header.kurs, 0), 1),
        v_coa_detail, v_coa_bank, v_dc,
        'FINANCE', 'OUT', 'NONE', 'YES', 'NONE', v_desc,
        COALESCE(v_header.inputby, 'SYSTEM'), COALESCE(v_header.inputdate, CURRENT_TIMESTAMP),
        v_header.updateby, v_header.updatedate
    )
    ON CONFLICT (uniqueid) DO UPDATE SET
        source_uniqueid = EXCLUDED.source_uniqueid,
        docno = EXCLUDED.docno,
        doctype = EXCLUDED.doctype,
        journal_type = EXCLUDED.journal_type,
        line_no = EXCLUDED.line_no,
        docdate = EXCLUDED.docdate,
        idbranch = EXCLUDED.idbranch,
        cabang = EXCLUDED.cabang,
        type_in_out = EXCLUDED.type_in_out,
        source_table = EXCLUDED.source_table,
        source_id = EXCLUDED.source_id,
        source_line_id = EXCLUDED.source_line_id,
        kdsupplier = EXCLUDED.kdsupplier,
        nsupplier = EXCLUDED.nsupplier,
        nilai = EXCLUDED.nilai,
        dpp = EXCLUDED.dpp,
        pajak = EXCLUDED.pajak,
        total = EXCLUDED.total,
        idtax = EXCLUDED.idtax,
        isinclusive = EXCLUDED.isinclusive,
        currcode = EXCLUDED.currcode,
        kurs = EXCLUDED.kurs,
        idcoa = EXCLUDED.idcoa,
        counter_idcoa = EXCLUDED.counter_idcoa,
        debet_kredit = EXCLUDED.debet_kredit,
        module = EXCLUDED.module,
        direction = EXCLUDED.direction,
        stock_effect = EXCLUDED.stock_effect,
        accounting_effect = EXCLUDED.accounting_effect,
        asset_effect = EXCLUDED.asset_effect,
        keterangan = EXCLUDED.keterangan,
        updatedby = EXCLUDED.updatedby,
        updateddate = CURRENT_TIMESTAMP;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS tr_pengeluarankb_detail_BANKOT ON sc_trx.pengeluarankb_dtl;
CREATE TRIGGER tr_pengeluarankb_detail_BANKOT
AFTER INSERT OR UPDATE OR DELETE ON sc_trx.pengeluarankb_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_sync_pengeluarankb_detail_BANKOT();

-- Tidak menghapus baris transaction_dt existing secara massal.

COMMIT;

-- Validasi:
-- SELECT docno, journal_type, idcoa, counter_idcoa, debet_kredit, nilai, uniqueid
-- FROM sc_trx.transaction_dt
-- WHERE journal_type = 'BANKOT'
--   AND source_table = 'sc_trx.pengeluarankb_dtl'
-- ORDER BY docdate DESC, docno, line_no;
