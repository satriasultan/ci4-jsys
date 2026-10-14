-- =========================================
-- I.R.A.10 — BIAYA PRODUKSI NON MATERIAL (BPNM)
-- =========================================

--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.R.A.10';
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian)
VALUES
('I','I.R.A.10','DRAFT'),
('E','I.R.A.10','REVISI/EDIT'),
('F','I.R.A.10','FINAL USER'),
('A','I.R.A.10','APPROVE'),
('A2','I.R.A.10','APPROVE 2'),
('A3','I.R.A.10','APPROVE 3'),
('P','I.R.A.10','CETAK/PRINT'),
('O','I.R.A.10','OBSOLATE'),
('C','I.R.A.10','CANCEL'),
('D','I.R.A.10','DELETE');


-- =========================================
-- DROP TABLE (urutan: DTL dulu, baru MST)
-- =========================================
DROP TABLE IF EXISTS sc_tmp.bpnm_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.bpnm_mst CASCADE;
DROP TABLE IF EXISTS sc_trx.bpnm_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.bpnm_mst CASCADE;


-- =========================================
-- TMP (DRAFT)
-- =========================================
CREATE TABLE IF NOT EXISTS sc_tmp.bpnm_mst
(
    docno           character(30) NOT NULL,
    doctype         character(20) DEFAULT 'BOM',
    idurut          bigserial,
    cabang          character(30),
    pemohon         character(100),
    docdate         date,
    idlocation      character(12),
    nmlocation      character(50),
    tabno           character(50),
    woeno           character(30),
    wono            character(30),
    bomno           character(30),
    desc_bom        TEXT,
    idbarang_jadi   character(50),
    nmbarang_jadi   TEXT,
    buildfor        numeric(18,2),
    ttlprice        numeric(18,2),
    buildunit       character(30),
    batchno         character(50),
    bagian          character(30),
    docref          character(30),
    status          character(6),
    keterangan      TEXT,
    inputby         varchar(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    CONSTRAINT pk_tmp_bpnm_mst PRIMARY KEY (docno, idurut)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.bpnm_mst OWNER TO postgres;


CREATE TABLE IF NOT EXISTS sc_tmp.bpnm_dtl
(
    docno           character(30) NOT NULL,
    doctype         character(20) DEFAULT 'BOM',
    doctype_detail  character(20) DEFAULT 'MATERIAL',
    cabang          character(30),
    docref          character(30),
    pemohon         character(100),
    docdate         date,
    idbarang        character(50),
    nmbarang        TEXT,
    idbarang_bom    character(50),
    nmbarang_bom    TEXT,
    description     TEXT,
    qty             numeric(18,2),
    unit            char(20),
    nilai           numeric(18,2),
    harga           numeric(18,2),
    spec            char(100),
    issub           character(6),
    status          character(6),
    inputby         character(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    idurut          bigserial,
    uniqueid        TEXT,
    CONSTRAINT pk_tmp_bpnm_dtl PRIMARY KEY (docno, uniqueid, idurut)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.bpnm_dtl OWNER TO postgres;


-- =========================================
-- TRX (FINAL)
-- =========================================
CREATE TABLE IF NOT EXISTS sc_trx.bpnm_mst
(
    docno           character(30) NOT NULL,
    doctype         character(20) DEFAULT 'BOM',
    idurut          bigint,                       -- bigint, bukan bigserial
    cabang          character(30),
    pemohon         character(100),
    docdate         date,
    idlocation      character(12),
    nmlocation      character(50),
    tabno           character(50),
    woeno           character(30),
    wono            character(30),
    bomno           character(30),
    desc_bom        TEXT,
    idbarang_jadi   character(50),
    nmbarang_jadi   TEXT,
    buildfor        numeric(18,2),
    ttlprice        numeric(18,2),
    buildunit       character(30),
    batchno         character(50),
    bagian          character(30),
    docref          character(30),
    status          character(6),
    keterangan      TEXT,
    inputby         varchar(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    CONSTRAINT pk_trx_bpnm_mst PRIMARY KEY (docno)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.bpnm_mst OWNER TO postgres;


CREATE TABLE IF NOT EXISTS sc_trx.bpnm_dtl
(
    docno           character(30) NOT NULL,
    doctype         character(20) DEFAULT 'BOM',
    doctype_detail  character(20) DEFAULT 'MATERIAL',
    cabang          character(30),
    docref          character(30),
    pemohon         character(100),
    docdate         date,
    idbarang        character(50),
    nmbarang        TEXT,
    idbarang_bom    character(50),
    nmbarang_bom    TEXT,
    description     TEXT,
    qty             numeric(18,2),
    unit            char(20),
    nilai           numeric(18,2),
    harga           numeric(18,2),
    spec            char(100),
    issub           character(6),
    status          character(6),
    inputby         character(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    idurut          bigint,                       -- bigint, bukan bigserial
    uniqueid        TEXT,
    CONSTRAINT pk_trx_bpnm_dtl PRIMARY KEY (docno, uniqueid, idurut)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.bpnm_dtl OWNER TO postgres;


-- =========================================
-- TRIGGER & FUNCTION: TMP -> TRX
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_bpnm_mst
ON sc_tmp.bpnm_mst;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_bpnm_mst();

CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_bpnm_mst()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno        TEXT;
    v_inputby      TEXT;
    v_idurut       BIGINT;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      INTEGER;
    v_lock_key     BIGINT;
    v_client_ip    TEXT;

BEGIN

    -- GUARD: hanya proses kalau status berubah
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- AMBIL IP
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

    -- =====================================================
    -- FINAL INSERT (docnotmp kosong)
    -- =====================================================
    IF UPPER(TRIM(OLD.status)) = 'E'
       AND UPPER(TRIM(NEW.status)) = 'F'
       AND COALESCE(TRIM(NEW.docnotmp), '') = '' THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        -- DETEKSI DUPLIKAT (cegah double insert)
        IF EXISTS (
            SELECT 1
            FROM sc_trx.bpnm_mst
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) = TRIM(COALESCE(v_inputby, ''))
        ) THEN
            DELETE FROM sc_tmp.bpnm_dtl
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND inputby = v_inputby;

            DELETE FROM sc_tmp.bpnm_mst
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND inputby = v_inputby
              AND idurut = v_idurut;

            RETURN NEW;
        END IF;

        -- GENERATE DOCNO (advisory lock + loop increment)
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.bpnm_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO BPNM tidak valid: %',
                    v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        -- INSERT HEADER (dengan idurut)
        INSERT INTO sc_trx.bpnm_mst (
            idurut, docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, status, keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            idurut, v_docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, 'F', keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.bpnm_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        -- INSERT DETAIL (dengan idurut)
        INSERT INTO sc_trx.bpnm_dtl (
            idurut, docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            status, inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            idurut, v_docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            'F', inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        FROM sc_tmp.bpnm_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        -- LOG: INSERT
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.10',
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- CLEANUP TMP
        DELETE FROM sc_tmp.bpnm_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.bpnm_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- =====================================================
    -- FINAL EDIT (docnotmp terisi)
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
          AND UPPER(TRIM(NEW.status)) = 'F'
          AND COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN

        v_inputby := NEW.inputby;

        -- DELETE OLD TRX
        DELETE FROM sc_trx.bpnm_dtl
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.bpnm_mst
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        -- INSERT HEADER (pakai docnotmp, dengan idurut)
        INSERT INTO sc_trx.bpnm_mst (
            idurut, docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, status, keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            idurut, NEW.docnotmp, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, 'F', keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.bpnm_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- INSERT DETAIL
        INSERT INTO sc_trx.bpnm_dtl (
            idurut, docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            status, inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            idurut, NEW.docnotmp, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            'F', inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        FROM sc_tmp.bpnm_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- LOG: UPDATE
        PERFORM sc_log.fn_log_transaction(
            NEW.docnotmp::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.10',
            'U',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- CLEANUP TMP
        DELETE FROM sc_tmp.bpnm_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.bpnm_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

    -- =====================================================
    -- CANCEL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
        AND UPPER(TRIM(NEW.status)) = 'C' THEN

        IF COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN
            UPDATE sc_trx.bpnm_mst
            SET status = 'F'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);
        END IF;

        DELETE FROM sc_tmp.bpnm_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);

        DELETE FROM sc_tmp.bpnm_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_tmp.tr_tmp_bpnm_mst() OWNER TO postgres;


DROP TRIGGER IF EXISTS tr_tmp_bpnm_mst
ON sc_tmp.bpnm_mst;

CREATE TRIGGER tr_tmp_bpnm_mst
AFTER UPDATE
ON sc_tmp.bpnm_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_bpnm_mst();


-- =========================================
-- TRIGGER & FUNCTION: TRX -> TMP
-- =========================================
DROP TRIGGER IF EXISTS tr_mst_bpnm_mst
ON sc_trx.bpnm_mst;

DROP FUNCTION IF EXISTS sc_trx.tr_mst_bpnm_mst();

CREATE OR REPLACE FUNCTION sc_trx.tr_mst_bpnm_mst()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_client_ip TEXT;

BEGIN

    -- GUARD: hanya proses kalau status berubah
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- AMBIL IP
    v_docno     := RTRIM(NEW.docno);
    v_inputby   := COALESCE(NEW.updateby, NEW.inputby);
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ADVISORY LOCK
    PERFORM pg_advisory_xact_lock(hashtext(v_docno));

    -- =====================================================
    -- DIBATALKAN (F -> C)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.R',
            'I.R.A.10',
            'C',
            v_inputby,
            v_client_ip,
            v_inputby
        );

    END IF;

    -- =====================================================
    -- DIEDIT (F -> E)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'E') THEN

        -- INSERT TMP MASTER
        INSERT INTO sc_tmp.bpnm_mst (
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, status, keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, 'E', keterangan,
            inputby, inputdate, updateby, updatedate, v_docno
        FROM sc_trx.bpnm_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

        -- INSERT TMP DETAIL
        INSERT INTO sc_tmp.bpnm_dtl (
            docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            status, inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            'E', inputby, inputdate, updateby, updatedate,
            docno, uniqueid
        FROM sc_trx.bpnm_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_mst_bpnm_mst() OWNER TO postgres;


DROP TRIGGER IF EXISTS tr_mst_bpnm_mst
ON sc_trx.bpnm_mst;

CREATE TRIGGER tr_mst_bpnm_mst
AFTER UPDATE
ON sc_trx.bpnm_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_mst_bpnm_mst();