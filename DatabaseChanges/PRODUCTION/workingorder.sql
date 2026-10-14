--I.R.A.4
--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.R.A.4';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.R.A.4','DRAFT'),
('E','I.R.A.4','REVISI/EDIT'),
('F','I.R.A.4','FINAL USER'),
('A','I.R.A.4','APPROVE'),
('A2','I.R.A.4','APPROVE 2'),
('A3','I.R.A.4','APPROVE 3'),
('P','I.R.A.4','CETAK/PRINT'),
('O','I.R.A.4','OBSOLATE'),
('C','I.R.A.4','CANCEL'),
('D','I.R.A.4','DELETE');




--drop table sc_tmp.workingorder_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.workingorder_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    idurut bigserial,
    doctype character(20) default 'WO',
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    docdatefinish date,
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
	noso character(30),
    inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_workingorder_mst PRIMARY KEY (docno, idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.workingorder_mst
    OWNER to postgres;

--drop table sc_tmp.workingorder_bom_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.workingorder_bom_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'WO',
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi text,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    buildfor numeric(18,2),
    ttlmaterial numeric(18,2),
    ttlcost numeric(18,2),
    ttlwip numeric(18,2),
    ttlprice numeric(18,2),
    buildunit character(30),
	minimumqty numeric(18,2),
    desc_bom TEXT,
    inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_workingorder_bom_mst PRIMARY KEY (docno,docref)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.workingorder_bom_mst
    OWNER to postgres;


/** 'MATERIAL, COST, WIP' **/
--drop table sc_tmp.workingorder_bom_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.workingorder_bom_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'WO',
    doctype_detail character(20) default 'MATERIAL' ,
    cabang character (30 ) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    description text,
	qty numeric(18,2),
    unit char(20),
    standartcost numeric(18,2),
    totalcost numeric(18,2),
    newcost numeric(18,2),
	currcode char(3),
	idbagian char(20),
	nmbagian char(100),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut bigserial,
	uniqueid text,
    CONSTRAINT pk_tmp_workingorder_bom_dtl PRIMARY KEY (docno,uniqueid,idurut,docref)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.workingorder_bom_dtl
    OWNER to postgres;


/* MST ATAU TRANSAKSI */

--drop table sc_trx.workingorder_mst;
CREATE TABLE IF NOT EXISTS sc_trx.workingorder_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    idurut bigserial,
    doctype character(20) default 'WO' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    docdatefinish date,
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    noso character(30),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_workingorder_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.workingorder_mst
    OWNER to postgres;


--drop table sc_trx.workingorder_bom_mst;
CREATE TABLE IF NOT EXISTS sc_trx.workingorder_bom_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'WO' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi text,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    buildfor numeric(18,2),
    ttlmaterial numeric(18,2),
    ttlcost numeric(18,2),
    ttlwip numeric(18,2),
    ttlprice numeric(18,2),
    buildunit character(30),
	minimumqty numeric(18,2),
    desc_bom TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_workingorder_bom_mst PRIMARY KEY (docno, docref)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.workingorder_bom_mst
    OWNER to postgres;


--drop table sc_trx.workingorder_bom_dtl;
CREATE TABLE IF NOT EXISTS sc_trx.workingorder_bom_dtl
(
	docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'WO' ,
    doctype_detail character(20) default 'MATERIAL' ,
    cabang character (30 ) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    description text,
	qty numeric(18,2),
    unit char(20),
    standartcost numeric(18,2),
    totalcost numeric(18,2),
    newcost numeric(18,2),
	currcode char(3),
	idbagian char(20),
	nmbagian char(100),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut integer,
	uniqueid text,
    CONSTRAINT pk_tmp_workingorder_bom_dtl PRIMARY KEY (docno,uniqueid,idurut,docref)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.workingorder_bom_dtl
    OWNER to postgres;


-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_workingorder_mst
ON sc_tmp.workingorder_mst;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_workingorder_mst();

-- =========================================
-- FUNCTION TMP -> TRX
-- =========================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_workingorder_mst()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno        TEXT;
    v_inputby      TEXT;
    v_idurut       INTEGER;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      INTEGER;
    v_lock_key     BIGINT;
    v_client_ip    TEXT;

BEGIN

    -- ===============================
    -- GUARD: hanya proses kalau status berubah
    -- ===============================
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

    -- =====================================================
    -- FINAL INSERT (tmp kosong / docnotmp kosong)
    -- =====================================================
    IF UPPER(TRIM(OLD.status)) = 'E'
       AND UPPER(TRIM(NEW.status)) = 'F'
       AND COALESCE(TRIM(NEW.docnotmp), '') = '' THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        -- ================================================
        -- DETEKSI DUPLIKAT (cegah double insert)
        -- ================================================
        IF EXISTS (
            SELECT 1
            FROM sc_trx.workingorder_mst
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) = TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.workingorder_bom_dtl
            WHERE TRIM(docref) = TRIM(OLD.docno)
              AND inputby = v_inputby;

            DELETE FROM sc_tmp.workingorder_bom_mst
            WHERE TRIM(docref) = TRIM(OLD.docno)
              AND inputby = v_inputby;

            DELETE FROM sc_tmp.workingorder_mst
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND inputby = v_inputby
              AND idurut = v_idurut;

            RETURN NEW;
        END IF;

        -- ================================================
        -- GENERATE DOCNO (advisory lock + loop increment)
        -- ================================================
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.workingorder_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO Working Order tidak valid: %',
                    v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        -- ================================================
        -- INSERT HEADER MASTER (dengan idurut)
        -- ================================================
        INSERT INTO sc_trx.workingorder_mst (
            idurut, docno, doctype, cabang, pemohon, docdate, docdatefinish,
            kdcustomer, nmcustomer, alamatcustomer, docref,
            status, keterangan, noso,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            idurut, v_docno, doctype, cabang, pemohon, docdate, docdatefinish,
            kdcustomer, nmcustomer, alamatcustomer, docref,
            'F', keterangan, noso,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        -- ================================================
        -- INSERT BOM HEADER
        -- ================================================
        INSERT INTO sc_trx.workingorder_bom_mst (
            docno, doctype, cabang, pemohon, docdate,
            idbarang_jadi, nmbarang_jadi, docref,
            buildfor, ttlmaterial, ttlcost, ttlwip, ttlprice,
            buildunit, minimumqty, status, desc_bom,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate,
            idbarang_jadi, nmbarang_jadi, v_docno,
            buildfor, ttlmaterial, ttlcost, ttlwip, ttlprice,
            buildunit, minimumqty, 'F', desc_bom,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- INSERT BOM DETAIL
        -- ================================================
        INSERT INTO sc_trx.workingorder_bom_dtl (
            docno, doctype, doctype_detail, cabang, docref,
            pemohon, docdate,
            idbarang, nmbarang, description, qty, unit,
            standartcost, totalcost, newcost, currcode,
            idbagian, nmbagian, status,
            inputby, inputdate, updateby, updatedate,
            docnotmp, idurut, uniqueid
        )
        SELECT
            docno, doctype, doctype_detail, cabang, v_docno,
            pemohon, docdate,
            idbarang, nmbarang, description, qty, unit,
            COALESCE(standartcost, 0),
            COALESCE(totalcost, 0),
            COALESCE(newcost, 0),
            currcode,
            idbagian, nmbagian, 'F',
            inputby, inputdate, updateby, updatedate,
            docnotmp, idurut, uniqueid
        FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- LOG: INSERT HEADER WO
        -- ================================================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.4',
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.workingorder_mst
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

        -- ================================================
        -- DELETE OLD TRX
        -- ================================================
        DELETE FROM sc_trx.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.workingorder_mst
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        -- ================================================
        -- INSERT HEADER MASTER (pakai docnotmp, dengan idurut)
        -- ================================================
        INSERT INTO sc_trx.workingorder_mst (
            idurut, docno, doctype, cabang, pemohon, docdate, docdatefinish,
            kdcustomer, nmcustomer, alamatcustomer, docref,
            status, keterangan, noso,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            idurut, NEW.docnotmp, doctype, cabang, pemohon, docdate, docdatefinish,
            kdcustomer, nmcustomer, alamatcustomer, docref,
            'F', keterangan, noso,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- INSERT BOM HEADER
        -- ================================================
        INSERT INTO sc_trx.workingorder_bom_mst (
            docno, doctype, cabang, pemohon, docdate,
            idbarang_jadi, nmbarang_jadi, docref,
            buildfor, ttlmaterial, ttlcost, ttlwip, ttlprice,
            buildunit, minimumqty, status, desc_bom,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate,
            idbarang_jadi, nmbarang_jadi, NEW.docnotmp,
            buildfor, ttlmaterial, ttlcost, ttlwip, ttlprice,
            buildunit, minimumqty, 'F', desc_bom,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- INSERT BOM DETAIL
        -- ================================================
        INSERT INTO sc_trx.workingorder_bom_dtl (
            docno, doctype, doctype_detail, cabang, docref,
            pemohon, docdate,
            idbarang, nmbarang, description, qty, unit,
            standartcost, totalcost, newcost, currcode,
            idbagian, nmbagian, status,
            inputby, inputdate, updateby, updatedate,
            docnotmp, idurut, uniqueid
        )
        SELECT
            docno, doctype, doctype_detail, cabang, NEW.docnotmp,
            pemohon, docdate,
            idbarang, nmbarang, description, qty, unit,
            COALESCE(standartcost, 0),
            COALESCE(totalcost, 0),
            COALESCE(newcost, 0),
            currcode,
            idbagian, nmbagian, 'F',
            inputby, inputdate, updateby, updatedate,
            docnotmp, idurut, uniqueid
        FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- LOG: UPDATE HEADER WO
        -- ================================================
        PERFORM sc_log.fn_log_transaction(
            NEW.docnotmp::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.4',
            'U',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

    -- =====================================================
    -- CANCEL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
        AND UPPER(TRIM(NEW.status)) = 'C' THEN

        -- ================================================
        -- KEMBALIKAN STATUS TRX KE 'F'
        -- ================================================
        IF COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN
            UPDATE sc_trx.workingorder_mst
            SET status = 'F'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);
        END IF;

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(NEW.docno);

        DELETE FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(NEW.docno);

        DELETE FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_tmp.tr_tmp_workingorder_mst()
    OWNER TO postgres;

-- =========================================
-- TRIGGER
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_workingorder_mst
ON sc_tmp.workingorder_mst;

CREATE TRIGGER tr_tmp_workingorder_mst
AFTER UPDATE
ON sc_tmp.workingorder_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_workingorder_mst();



-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_mst_workingorder_mst
ON sc_trx.workingorder_mst;

DROP FUNCTION IF EXISTS sc_trx.tr_mst_workingorder_mst();

-- =========================================
-- FUNCTION TRX -> TMP
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.tr_mst_workingorder_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$

DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_client_ip TEXT;

BEGIN

    -- ===============================
    -- GUARD: hanya proses kalau status berubah
    -- ===============================
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_docno    := RTRIM(NEW.docno);
    v_inputby  := NEW.inputby;
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ===============================
    -- ADVISORY LOCK (CEGAH RACE CONDITION)
    -- ===============================
    PERFORM pg_advisory_xact_lock(hashtext(NEW.docno));

    -- =====================================================
    -- WO DIBATALKAN (F -> C)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN

        -- ===============================
        -- LOG: CANCEL WO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.R',                  -- kode module (sesuaikan)
            'I.R.A.4',              -- kode menu WO (sesuaikan)
            'C',                    -- action: CANCEL
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

    END IF;

    -- =====================================================
    -- WO DIEDIT (F -> E)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'E') THEN

        -- =================================================
        -- INSERT TMP MASTER
        -- =================================================
        INSERT INTO sc_tmp.workingorder_mst (
            docno, doctype, cabang, pemohon, docdate, docdatefinish,
            kdcustomer, nmcustomer, alamatcustomer, docref,
            status, keterangan, noso,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate, docdatefinish,
            kdcustomer, nmcustomer, alamatcustomer, docref,
            'E', keterangan, noso,
            inputby, inputdate, updateby, updatedate, docno
        FROM sc_trx.workingorder_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

        -- =================================================
        -- INSERT TMP BOM MASTER
        -- =================================================
        INSERT INTO sc_tmp.workingorder_bom_mst (
            docno, doctype, cabang, pemohon, docdate,
            idbarang_jadi, nmbarang_jadi, docref,
            buildfor, ttlmaterial, ttlcost, ttlwip, ttlprice,
            buildunit, minimumqty, status, desc_bom,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate,
            idbarang_jadi, nmbarang_jadi, docref,
            buildfor, ttlmaterial, ttlcost, ttlwip, ttlprice,
            buildunit, minimumqty, 'E', desc_bom,
            inputby, inputdate, updateby, updatedate, docno
        FROM sc_trx.workingorder_bom_mst
        WHERE TRIM(docref) = TRIM(NEW.docno);

        -- =================================================
        -- INSERT TMP BOM DETAIL
        -- =================================================
        INSERT INTO sc_tmp.workingorder_bom_dtl (
            docno, doctype, doctype_detail, cabang, docref,
            pemohon, docdate,
            idbarang, nmbarang, description, qty, unit,
            standartcost, totalcost, newcost, currcode,
            idbagian, nmbagian, status,
            inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            docno, doctype, doctype_detail, cabang, docref,
            pemohon, docdate,
            idbarang, nmbarang, description, qty, unit,
            standartcost, totalcost, newcost, currcode,
            idbagian, nmbagian, 'E',
            inputby, inputdate, updateby, updatedate,
            docno, uniqueid
        FROM sc_trx.workingorder_bom_dtl
        WHERE TRIM(docref) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_mst_workingorder_mst()
    OWNER TO postgres;