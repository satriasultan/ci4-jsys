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
    CONSTRAINT pk_tmp_workingorder_mst PRIMARY KEY (docno)
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
AS $BODY$

DECLARE

    v_docno        TEXT;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      INTEGER;

BEGIN

    RAISE NOTICE 'TMP WO TRIGGER : %, OLD=%, NEW=%',
        NEW.docno,
        OLD.status,
        NEW.status;

    -- =====================================================
    -- FINAL INSERT
    -- =====================================================
    IF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='F'
       AND COALESCE(TRIM(NEW.docnotmp),'')='' THEN

        v_docno := TRIM(NEW.docno);

        -- =================================================
        -- LOCK DOCNO
        -- =================================================
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        PERFORM pg_advisory_xact_lock(hashtext(v_base_docno));

        v_new_docno := v_docno;

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.workingorder_mst
                WHERE TRIM(docno)=TRIM(v_new_docno)
            );

            v_num := regexp_replace(
                v_new_docno,
                '.*?([0-9]+)$',
                '\1'
            );

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                LPAD(v_num_int::TEXT, LENGTH(v_num), '0');

        END LOOP;

        v_docno := v_new_docno;

        -- =================================================
        -- INSERT MASTER
        -- =================================================
        INSERT INTO sc_trx.workingorder_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            docdatefinish,
            kdcustomer,
            nmcustomer,
            alamatcustomer,
            docref,
            status,
            keterangan,
            noso,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        )
        SELECT

            v_docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            docdatefinish,
            kdcustomer,
            nmcustomer,
            alamatcustomer,
            docref,
            'F',
            keterangan,
            noso,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno)=TRIM(OLD.docno);


        -- =================================================
        -- INSERT BOM HEADER
        -- =================================================
        INSERT INTO sc_trx.workingorder_bom_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            buildfor,
            ttlmaterial,
            ttlcost,
            ttlwip,
            ttlprice,
            buildunit,
            minimumqty,
            status,
            desc_bom,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        )
        SELECT

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idbarang_jadi,
            nmbarang_jadi,
            v_docno,
            buildfor,
            ttlmaterial,
            ttlcost,
            ttlwip,
            ttlprice,
            buildunit,
            minimumqty,
            'F',
            desc_bom,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(OLD.docno);

        -- =================================================
        -- INSERT DETAIL
        -- =================================================
        INSERT INTO sc_trx.workingorder_bom_dtl (

            docno,
            doctype,
            doctype_detail,
            cabang,
            docref,
            pemohon,
            docdate,
            
            idbarang,
            nmbarang,
            description,
            qty,
            unit,
            standartcost,
            totalcost,
            newcost,
            currcode,
            idbagian,
            nmbagian,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        )
        SELECT

            docno,
            doctype,
            doctype_detail,
            cabang,
            v_docno,
            pemohon,
            docdate,
            
            idbarang,
            nmbarang,
            description,
            qty,
            unit,
            COALESCE(standartcost,0),
            COALESCE(totalcost,0),
            COALESCE(newcost,0),
            currcode,
            idbagian,
            nmbagian,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(OLD.docno);

        -- =================================================
        -- CLEAN TMP
        -- =================================================
        DELETE FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(OLD.docno);

        DELETE FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(OLD.docno);

        DELETE FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno)=TRIM(OLD.docno);

    -- =====================================================
    -- FINAL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='F'
       AND COALESCE(TRIM(NEW.docnotmp),'')<>'' THEN

        -- =================================================
        -- DELETE OLD TRX
        -- =================================================
        DELETE FROM sc_trx.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.workingorder_mst
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        -- =================================================
        -- INSERT MASTER
        -- =================================================
        INSERT INTO sc_trx.workingorder_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            docdatefinish,
            kdcustomer,
            nmcustomer,
            alamatcustomer,
            docref,
            status,
            keterangan,
            noso,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        )
        SELECT

            NEW.docnotmp,
            doctype,
            cabang,
            pemohon,
            docdate,
            docdatefinish,
            kdcustomer,
            nmcustomer,
            alamatcustomer,
            docref,
            'F',
            keterangan,
            noso,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =================================================
        -- INSERT BOM HEADER
        -- =================================================
        INSERT INTO sc_trx.workingorder_bom_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            buildfor,
            ttlmaterial,
            ttlcost,
            ttlwip,
            ttlprice,
            buildunit,
            minimumqty,
            status,
            desc_bom,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        )
        SELECT

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idbarang_jadi,
            nmbarang_jadi,
            NEW.docnotmp,
            buildfor,
            ttlmaterial,
            ttlcost,
            ttlwip,
            ttlprice,
            buildunit,
            minimumqty,
            'F',
            desc_bom,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(OLD.docno);

        -- =================================================
        -- INSERT DETAIL
        -- =================================================
        INSERT INTO sc_trx.workingorder_bom_dtl (

            docno,
            doctype,
            doctype_detail,
            cabang,
            docref,
            pemohon,
            docdate,
            
            idbarang,
            nmbarang,
            description,
            qty,
            unit,
            standartcost,
            totalcost,
            newcost,
            currcode,
            idbagian,
            nmbagian,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        )
        SELECT

            docno,
            doctype,
            doctype_detail,
            cabang,
            NEW.docnotmp,
            pemohon,
            docdate,
            
            idbarang,
            nmbarang,
            description,
            qty,
            unit,
            COALESCE(standartcost,0),
            COALESCE(totalcost,0),
            COALESCE(newcost,0),
            currcode,
            idbagian,
            nmbagian,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(NEW.docno);

        -- =================================================
        -- CLEAN TMP
        -- =================================================
        DELETE FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.workingorder_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

    -- =====================================================
    -- CANCEL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='C' THEN

        UPDATE sc_trx.workingorder_mst
        SET status='F'
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        DELETE FROM sc_tmp.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.workingorder_mst
        WHERE TRIM(docref)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
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

BEGIN

    RAISE NOTICE 'TRX WO TRIGGER : %, OLD=%, NEW=%',
        NEW.docno,
        OLD.status,
        NEW.status;

    -- =====================================================
    -- EDIT
    -- =====================================================
    IF UPPER(TRIM(OLD.status))='F'
       AND UPPER(TRIM(NEW.status))='E' THEN

        -- =================================================
        -- INSERT TMP MASTER
        -- =================================================
        INSERT INTO sc_tmp.workingorder_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            docdatefinish,
            kdcustomer,
            nmcustomer,
            alamatcustomer,
            docref,
            status,
            keterangan,
            noso,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        )
        SELECT

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            docdatefinish,
            kdcustomer,
            nmcustomer,
            alamatcustomer,
            docref,
            'E',
            keterangan,
            noso,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docno

        FROM sc_trx.workingorder_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =================================================
        -- INSERT TMP BOM MASTER
        -- =================================================
        INSERT INTO sc_tmp.workingorder_bom_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            buildfor,
            ttlmaterial,
            ttlcost,
            ttlwip,
            ttlprice,
            buildunit,
            minimumqty,
            status,
            desc_bom,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        )
        SELECT

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            buildfor,
            ttlmaterial,
            ttlcost,
            ttlwip,
            ttlprice,
            buildunit,
            minimumqty,
            'E',
            desc_bom,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docref

        FROM sc_trx.workingorder_bom_mst
        WHERE TRIM(docref)=TRIM(NEW.docno);

        -- =================================================
        -- INSERT TMP BOM DETAIL
        -- =================================================
        INSERT INTO sc_tmp.workingorder_bom_dtl (

            docno,
            doctype,
            doctype_detail,
            cabang,
            docref,
            pemohon,
            docdate,
            
            idbarang,
            nmbarang,
            description,
            qty,
            unit,
            standartcost,
            totalcost,
            newcost,
            currcode,
            idbagian,
            nmbagian,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            uniqueid

        )
        SELECT

            docno,
            doctype,
            doctype_detail,
            cabang,
            docref,
            pemohon,
            docdate,
            
            idbarang,
            nmbarang,
            description,
            qty,
            unit,
            standartcost,
            totalcost,
            newcost,
            currcode,
            idbagian,
            nmbagian,
            'E',
            inputby,
            inputdate,
            updateby,
            updatedate,
            docref,
            uniqueid

        FROM sc_trx.workingorder_bom_dtl
        WHERE TRIM(docref)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_mst_workingorder_mst
AFTER UPDATE
ON sc_trx.workingorder_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_mst_workingorder_mst();