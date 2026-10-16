-- ============================================================
-- PO.SQL - ONE RUN INSTALL SCRIPT (FINAL REBUILD)
-- Semua object PO dibuat ulang dalam 1 transaction.
-- Perubahan:
--   - Tambah kolom: batch character(100) di po_dtl (tmp & trx)
--   - uniqueidpo ikut ter-copy di semua INSERT ... SELECT po_dtl
--   - batch ikut ter-copy di semua INSERT ... SELECT po_dtl
--   - Trigger auto-isi uniqueidpo (AFTER INSERT sc_tmp.po_dtl)
-- Jika ada error, PostgreSQL akan melakukan ROLLBACK.
-- ============================================================

BEGIN;

-- ============================================================
-- DROP OBJECT TABLE LAMA
-- ============================================================

DROP TABLE IF EXISTS sc_tmp.po_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.po_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.po CASCADE;
DROP TABLE IF EXISTS sc_trx.po CASCADE;

-- ============================================================
-- TABLE: sc_tmp.po (HEADER TMP)
-- ============================================================
CREATE TABLE sc_tmp.po
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate DATE,
    senddate DATE,
    cabang character(30) COLLATE pg_catalog."default",
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    docnoumb character(30),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    printcount integer,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_po PRIMARY KEY (idurut, docno)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.po
    OWNER to postgres;

-- ============================================================
-- TABLE: sc_trx.po (HEADER TRX)
-- ============================================================
CREATE TABLE sc_trx.po
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate DATE,
    senddate DATE,
    cabang character(30) COLLATE pg_catalog."default",
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    docnoumb character(30),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    printcount integer,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_po PRIMARY KEY (idurut, docno)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.po
    OWNER to postgres;

-- ============================================================
-- TABLE: sc_tmp.po_dtl (DETAIL TMP)
-- ============================================================
CREATE TABLE sc_tmp.po_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    qtybonus NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    descriptionpo TEXT COLLATE pg_catalog."default",
    descriptionpp TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30),
    uniqueid text,
    uniqueidpo text,
    capexno character(30),
    idtax character(20),
    currcode character(3),
    kurs numeric(18,2),
    nilaikonversi numeric(18,2),
    nilaipajak numeric(18,2),
    qtylpb numeric(18,2) DEFAULT 0,
    qtyvoid numeric(18,2) DEFAULT 0,
    idhistory_price text,
    multidisctype char(30),
    totaldiscount numeric(18,2),
    batch character(100)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.po_dtl
    OWNER to postgres;

-- ============================================================
-- TABLE: sc_trx.po_dtl (DETAIL TRX)
-- ============================================================
CREATE TABLE sc_trx.po_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    qtybonus NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    descriptionpo TEXT COLLATE pg_catalog."default",
    descriptionpp TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30),
    uniqueid text,
    uniqueidpo text,
    capexno character(30),
    idtax character(20),
    currcode character(3),
    kurs numeric(18,2),
    nilaikonversi numeric(18,2),
    nilaipajak numeric(18,2),
    qtylpb numeric(18,2) DEFAULT 0,
    qtyvoid numeric(18,2) DEFAULT 0,
    idhistory_price text,
    multidisctype char(30),
    totaldiscount numeric(18,2),
    batch character(100)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.po_dtl
    OWNER to postgres;

-- ============================================================
-- FUNCTION + TRIGGER: AUTO-ISI uniqueidpo DI sc_tmp.po_dtl
-- Hanya mengisi kalau NULL / kosong.
-- ============================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_po_dtl_set_uniqueidpo()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_rand INTEGER;
BEGIN
    IF NEW.uniqueidpo IS NULL OR btrim(NEW.uniqueidpo) = '' THEN
        v_rand := floor(random() * 100000)::int;   -- 0..99999

        UPDATE sc_tmp.po_dtl
        SET uniqueidpo = md5(
                v_rand::text
                || COALESCE(NEW.docno, '')
                || COALESCE(NEW.idbarang, '')
                || COALESCE(NEW.inputdate::text, '')
            )
        WHERE idurut = NEW.idurut;
    END IF;

    RETURN NEW;
END;
$BODY$;

DROP TRIGGER IF EXISTS tr_po_dtl_set_uniqueidpo ON sc_tmp.po_dtl;

CREATE TRIGGER tr_po_dtl_set_uniqueidpo
    AFTER INSERT ON sc_tmp.po_dtl
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_po_dtl_set_uniqueidpo();

-- ============================================================
-- FUNCTION: sc_tmp.tr_po_finalize()
-- ============================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_po_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno      TEXT;
    v_inputby    TEXT;
    v_idurut     INTEGER;
    v_num        TEXT;
    v_num_int    INTEGER;
    v_lock_key   BIGINT;
    v_base_docno TEXT;
    v_new_docno  TEXT;

    v_client_ip  TEXT;
BEGIN

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        -- ===============================
        -- NORMALISASI
        v_docno   := rtrim(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        /* ========================================================
           CEK APAKAH SUDAH PERNAH DIFINALKAN
        ======================================================== */
        IF EXISTS (
            SELECT 1
            FROM sc_trx.po
            WHERE idurut = v_idurut
                AND TRIM(COALESCE(inputby, '')) = TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.po
            WHERE TRIM(docno) = TRIM(OLD.docno)
                AND idurut = v_idurut;

            RETURN NEW;
        END IF;

        /* ========================================================
           GENERATE DOCNO
        ======================================================== */
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.po
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(
                v_new_docno,
                '.*?([0-9]+)$',
                '\1'
            );

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO PO tidak valid: %',
                    v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(
                    v_num_int::TEXT,
                    length(v_num),
                    '0'
                );

        END LOOP;

        v_docno := v_new_docno;

        -- ===============================
        -- INSERT HEADER
        -- ===============================
        INSERT INTO sc_trx.po (
            idurut, docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax, isinclusive, currcode, kurs, dpp, docnoumb,
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax, isinclusive, currcode, kurs, dpp, docnoumb,
            jumlahpajak, total, syarat,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.po
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL (uniqueidpo & batch IKUT)
        -- ===============================
        INSERT INTO sc_trx.po_dtl (
            idurut, docno, docnopp, idbarang, capexno, uniqueid, uniqueidpo,
            nmbarang, unit, qty, qtybonus,
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate,
            idhistory_price, multidisctype, totaldiscount, batch
        )
        SELECT
            idurut, v_docno, docnopp, idbarang, capexno, uniqueid, uniqueidpo,
            nmbarang, unit, qty, qtybonus,
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate,
            idhistory_price, multidisctype, totaldiscount, batch
        FROM sc_tmp.po_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        -- ===============================
        -- UPDATE QTYPO DI PP_DTL (TAMBAH)
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) + pod.qty_used
        FROM (
            SELECT
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.po_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP_DTL BERDASARKAN QTYPO
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET status = CASE
            WHEN ppd.qty = COALESCE(ppd.qtypo, 0) THEN 'PO'
            ELSE 'F'
        END
        FROM sc_tmp.po_dtl t
        WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND ppd.uniqueid = t.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP HEADER MENJADI 'PO'
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = 'PO'
        WHERE pp.docno IN (
            SELECT DISTINCT t.docnopp
            FROM sc_tmp.po_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
                AND t.inputby = v_inputby
                AND t.docnopp IS NOT NULL
                AND t.docnopp <> ''
        );

        -- ===============================
        -- LOG: INSERT HEADER PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',
            'I.P.A.3',
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.po
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.po_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        -- ===============================
        -- STEP 1: REVERT QTYPO (KURANGI DENGAN DATA LAMA)
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) - pod_lama.qty_po_lama
        FROM (
            SELECT
                uniqueid,
                SUM(qty) as qty_po_lama
            FROM sc_trx.po_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod_lama
        WHERE ppd.uniqueid = pod_lama.uniqueid;

        DELETE FROM sc_trx.po     WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.po_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.po_dtl
        (idurut, docno, docnopp, idbarang, capexno, uniqueid, uniqueidpo,
         nmbarang, unit, qty, qtybonus,
         harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
         descriptionpo, descriptionpp,
         inputby, inputdate, status, updateby, updatedate, docnotmp,
         idhistory_price, multidisctype, totaldiscount, batch)
        SELECT
            idurut, NEW.docnotmp, docnopp, idbarang, capexno, uniqueid, uniqueidpo,
            nmbarang, unit, qty, qtybonus,
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp,
            idhistory_price, multidisctype, totaldiscount, batch
        FROM sc_tmp.po_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.po
        (idurut, docno, cabang, docdate, senddate, pemohon, kdsupplier,
         nmsupplier, alamatsupplier, alamatkirim, jthtempo,
         idtax, isinclusive, currcode, kurs, dpp, docnoumb,
         jumlahpajak, total, syarat,
         keterangan, status, inputby, inputdate,
         updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax, isinclusive, currcode, kurs, dpp, docnoumb,
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.po
        WHERE rtrim(docno) = rtrim(NEW.docno);

        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) + pod.qty_used
        FROM (
            SELECT
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.po_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP_DTL BERDASARKAN QTYPO
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET status = CASE
            WHEN ppd.qty = COALESCE(ppd.qtypo, 0) THEN 'PO'
            ELSE 'F'
        END
        FROM sc_tmp.po_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.inputby = v_inputby
            AND ppd.uniqueid = t.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP HEADER MENJADI 'PO'
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = 'PO'
        WHERE pp.docno IN (
            SELECT DISTINCT t.docnopp
            FROM sc_tmp.po_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
                AND t.docnopp IS NOT NULL
                AND t.docnopp <> ''
        );

        -- ===============================
        -- LOG: UPDATE HEADER PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',
            'I.P.A.3',
            'U',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        DELETE FROM sc_tmp.po     WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.po_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN

        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.po SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.po SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

        DELETE FROM sc_tmp.po     WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.po_dtl WHERE docno = NEW.docno;

    END IF;

    RETURN NEW;
END;
$BODY$;

CREATE OR REPLACE TRIGGER tr_po_finalize
    AFTER UPDATE ON sc_tmp.po
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_po_finalize();

-- ============================================================
-- FUNCTION: sc_trx.tr_po()
-- ============================================================
CREATE OR REPLACE FUNCTION sc_trx.tr_po()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno      TEXT;
    v_client_ip  TEXT;
    v_inputby    TEXT;
BEGIN

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_docno   := rtrim(NEW.docno);
    v_inputby := NEW.inputby;

    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ===============================
    -- ADVISORY LOCK (CEGAH RACE CONDITION)
    -- ===============================
    PERFORM pg_advisory_xact_lock(hashtext(NEW.docno));

    -- ===============================
    -- PO DIBATALKAN (F -> C) - REVERT QTYPO
    -- ===============================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN

        -- ===============================
        -- REVERT QTYPO DI PP_DTL
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) - pod.qty_used
        FROM (
            SELECT
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_trx.po_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP HEADER
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = CASE
            WHEN EXISTS (
                SELECT 1
                FROM sc_trx.pp_dtl ppd
                WHERE rtrim(ppd.docno) = rtrim(pp.docno)
                    AND COALESCE(ppd.qtypo, 0) > 0
            ) THEN 'PO'
            ELSE 'P'
        END
        WHERE EXISTS (
            SELECT 1
            FROM sc_trx.po_dtl pd
            WHERE rtrim(pd.docno) = rtrim(NEW.docno)
                AND pd.uniqueid IN (
                    SELECT uniqueid
                    FROM sc_trx.pp_dtl
                    WHERE rtrim(docno) = rtrim(pp.docno)
                )
        );

        -- ===============================
        -- UPDATE STATUS PP_DTL BERDASARKAN QTYPO
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET status = CASE
            WHEN ppd.qty = COALESCE(ppd.qtypo, 0) THEN 'PO'
            ELSE 'F'
        END
        FROM sc_trx.po_dtl pd
        WHERE ppd.uniqueid = pd.uniqueid
            AND rtrim(pd.docno) = rtrim(NEW.docno);

        -- ===============================
        -- LOG: CANCEL HEADER PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',
            'I.P.A.3',
            'C',
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

    END IF;

    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'E') THEN
        -- Insert into po_dtl (uniqueidpo & batch IKUT)
        INSERT INTO sc_tmp.po_dtl
        (idurut, docno, docnopp, idbarang, capexno, uniqueid, uniqueidpo,
         nmbarang, unit, qty, qtybonus,
         harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
         descriptionpo, descriptionpp,
         inputby, inputdate, status, updateby, updatedate, docnotmp,
         idhistory_price, multidisctype, totaldiscount, batch)
        SELECT
            idurut, NEW.docno, docnopp, idbarang, capexno, uniqueid, uniqueidpo,
            nmbarang, unit, qty, qtybonus,
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, NEW.docno,
            idhistory_price, multidisctype, totaldiscount, batch
        FROM sc_trx.po_dtl
        WHERE docno = NEW.docno;

        -- Insert into po
        INSERT INTO sc_tmp.po
        (
            idurut, docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax, isinclusive, currcode, kurs, dpp, docnoumb,
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, docnotmp
        )
        SELECT
            idurut, NEW.docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax, isinclusive, currcode, kurs, dpp, docnoumb,
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
        FROM sc_trx.po
        WHERE docno = NEW.docno;
    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_po()
    OWNER TO postgres;

-- ============================================================
-- TRIGGER: tr_po
-- ============================================================
CREATE OR REPLACE TRIGGER tr_po
    AFTER UPDATE
    ON sc_trx.po
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_po();

-- ============================================================
-- SELESAI
-- ============================================================
COMMIT;

-- ============================================================
-- VERIFIKASI
-- ============================================================
SELECT table_schema, column_name, data_type
FROM information_schema.columns
WHERE table_schema IN ('sc_tmp','sc_trx') AND table_name = 'po_dtl'
  AND column_name = 'batch'
ORDER BY table_schema;

SELECT event_object_schema, event_object_table, trigger_name
FROM information_schema.triggers
WHERE trigger_name IN ('tr_po','tr_po_finalize','tr_po_dtl_set_uniqueidpo')
ORDER BY event_object_schema, trigger_name;