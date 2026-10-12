BEGIN;

CREATE SCHEMA IF NOT EXISTS sc_tmp;
CREATE SCHEMA IF NOT EXISTS sc_trx;

-- Hapus trigger sebelum mengganti function
DROP TRIGGER IF EXISTS pengeluarankb_finalize ON sc_tmp.pengeluarankb;
DROP TRIGGER IF EXISTS pengeluarankb ON sc_trx.pengeluarankb;

-- Hapus tabel lama sesuai urutan detail lalu header
DROP TABLE IF EXISTS sc_tmp.pengeluarankb_dtl;
DROP TABLE IF EXISTS sc_trx.pengeluarankb_dtl;
DROP TABLE IF EXISTS sc_tmp.pengeluarankb;
DROP TABLE IF EXISTS sc_trx.pengeluarankb;

-- =========================================================
-- HEADER TEMPORARY
-- =========================================================
CREATE TABLE sc_tmp.pengeluarankb
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
CREATE TABLE sc_trx.pengeluarankb
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
CREATE TABLE sc_tmp.pengeluarankb_dtl
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
CREATE TABLE sc_trx.pengeluarankb_dtl
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


alter table sc_tmp.pengeluarankb add column nmprkkas character(100);
alter table sc_trx.pengeluarankb add column nmprkkas character(100);

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
    IF OLD.status = 'E' AND NEW.status = 'F'
       AND NULLIF(BTRIM(NEW.docnotmp), '') IS NULL THEN

        v_docno := BTRIM(NEW.docno);

        -- Pisahkan prefix dan angka di belakang nomor dokumen.
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_num := substring(v_docno FROM '([0-9]+)$');

        IF v_num IS NULL THEN
            RAISE EXCEPTION 'Nomor dokumen % harus diakhiri angka', v_docno;
        END IF;

        -- Kunci berdasarkan prefix agar nomor tidak bentrok bersamaan.
        PERFORM pg_advisory_xact_lock(hashtext(v_base_docno)::BIGINT);

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
                RAISE EXCEPTION 'Nomor dokumen melebihi 30 karakter: %', v_new_docno;
            END IF;
        END LOOP;

        -- Header final
        INSERT INTO sc_trx.pengeluarankb (
            idurut, docno, docdate, cabang, pemohon,
            kdsupplier, nmsupplier, alamatsupplier,
            prkkas, np, jthtempo, isinclusive, currcode, kurs,
            dpp, jumlahpajak, total, status, keterangan, balance,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,nmprkkas
        )
        SELECT
            h.idurut, v_new_docno, h.docdate, h.cabang, h.pemohon,
            h.kdsupplier, h.nmsupplier, h.alamatsupplier,
            h.prkkas, h.np, h.jthtempo, h.isinclusive, h.currcode, h.kurs,
            h.dpp, h.jumlahpajak, h.total, 'F', h.keterangan, h.balance,
            h.inputby, h.inputdate, h.updateby, h.updatedate,
            h.printby, h.printdate, h.docnotmp,h.nmprkkas
        FROM sc_tmp.pengeluarankb h
        WHERE h.idurut = OLD.idurut
          AND BTRIM(h.docno) = BTRIM(OLD.docno);

        -- Detail final; idurut dibuat baru agar tidak bentrok PK antar dokumen.
        INSERT INTO sc_trx.pengeluarankb_dtl (
            docno, nobukti, idcoa, nmcoa, remarks, dk, cabang,
            nilai, uniqueid, status, inputby, inputdate,
            updateby, updatedate, docnotmp, jurnalref, nilai_ref
        )
        SELECT
            v_new_docno, d.nobukti, d.idcoa, d.nmcoa, d.remarks, d.dk, d.cabang,
            d.nilai, d.uniqueid, 'F', d.inputby, d.inputdate,
            d.updateby, d.updatedate, d.docnotmp, d.jurnalref, d.nilai_ref
        FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
          AND COALESCE(BTRIM(d.inputby), '') = COALESCE(BTRIM(OLD.inputby), '')
          AND BTRIM(COALESCE(d.status, '')) = 'F';

        -- Jurnal dibuat untuk detail yang jurnalref = YES.
        IF EXISTS (
            SELECT 1
            FROM sc_tmp.pengeluarankb_dtl d
            WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
              AND COALESCE(BTRIM(d.inputby), '') = COALESCE(BTRIM(OLD.inputby), '')
              AND BTRIM(COALESCE(d.status, '')) = 'F'
              AND UPPER(BTRIM(COALESCE(d.jurnalref, 'NO'))) = 'YES'
        ) THEN
            DELETE FROM sc_trx.jurnal_hd_penggunaan j
            WHERE BTRIM(j.docno) = v_new_docno;

            INSERT INTO sc_trx.jurnal_hd_penggunaan
            (
                docno, idunique, nobukti, docref,
                noperkiraan, namaperkiraan, keterangan, dk,
                costprofitcenter, nilai, status, inputby, inputdate
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
                d.nilai,
                'P',
                d.inputby,
                d.inputdate
            FROM sc_tmp.pengeluarankb_dtl d
            WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
              AND COALESCE(BTRIM(d.inputby), '') = COALESCE(BTRIM(OLD.inputby), '')
              AND BTRIM(COALESCE(d.status, '')) = 'F'
              AND UPPER(BTRIM(COALESCE(d.jurnalref, 'NO'))) = 'YES';
        END IF;

        -- Hapus data temporary setelah semua proses berhasil.
        DELETE FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(OLD.docno)
          AND COALESCE(BTRIM(d.inputby), '') = COALESCE(BTRIM(OLD.inputby), '');

        DELETE FROM sc_tmp.pengeluarankb h
        WHERE h.idurut = OLD.idurut
          AND BTRIM(h.docno) = BTRIM(OLD.docno);

    ELSIF OLD.status = 'E' AND NEW.status = 'F'
       AND NULLIF(BTRIM(NEW.docnotmp), '') IS NOT NULL THEN

        -- Revisi transaksi yang sudah ada: ganti detail/header pada docnotmp.
        DELETE FROM sc_trx.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_trx.pengeluarankb
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        INSERT INTO sc_trx.pengeluarankb (
            idurut, docno, docdate, cabang, pemohon,
            kdsupplier, nmsupplier, alamatsupplier,
            prkkas, np, jthtempo, isinclusive, currcode, kurs,
            dpp, jumlahpajak, total, status, keterangan, balance,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,nmprkkas
        )
        SELECT
            h.idurut, NEW.docnotmp, h.docdate, h.cabang, h.pemohon,
            h.kdsupplier, h.nmsupplier, h.alamatsupplier,
            h.prkkas, h.np, h.jthtempo, h.isinclusive, h.currcode, h.kurs,
            h.dpp, h.jumlahpajak, h.total, 'F', h.keterangan, h.balance,
            h.inputby, h.inputdate, h.updateby, h.updatedate,
            h.printby, h.printdate, h.docnotmp,h.nmprkkas
        FROM sc_tmp.pengeluarankb h
        WHERE h.idurut = NEW.idurut
          AND BTRIM(h.docno) = BTRIM(NEW.docno);

        INSERT INTO sc_trx.pengeluarankb_dtl (
            docno, nobukti, idcoa, nmcoa, remarks, dk, cabang,
            nilai, uniqueid, status, inputby, inputdate,
            updateby, updatedate, docnotmp, jurnalref, nilai_ref
        )
        SELECT
            NEW.docnotmp, d.nobukti, d.idcoa, d.nmcoa, d.remarks, d.dk, d.cabang,
            d.nilai, d.uniqueid, 'F', d.inputby, d.inputdate,
            d.updateby, d.updatedate, d.docnotmp, d.jurnalref, d.nilai_ref
        FROM sc_tmp.pengeluarankb_dtl d
        WHERE BTRIM(d.docno) = BTRIM(NEW.docno)
          AND BTRIM(COALESCE(d.status, '')) = 'F';

        DELETE FROM sc_tmp.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.pengeluarankb
        WHERE idurut = NEW.idurut
          AND BTRIM(docno) = BTRIM(NEW.docno);

    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN

        UPDATE sc_trx.pengeluarankb
        SET status = CASE
            WHEN NULLIF(BTRIM(NEW.printby), '') IS NOT NULL
                 AND NEW.printdate IS NOT NULL THEN 'P'
            ELSE 'F'
        END
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_tmp.pengeluarankb_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

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
            nilai, uniqueid, status, inputby, inputdate,
            updateby, updatedate, docnotmp, jurnalref, nilai_ref
        )
        SELECT
            d.docno, d.nobukti, d.idcoa, d.nmcoa, d.remarks, d.dk, d.cabang,
            d.nilai, d.uniqueid, d.status, d.inputby, d.inputdate,
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

COMMIT;
