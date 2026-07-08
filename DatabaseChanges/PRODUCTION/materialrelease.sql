--I.R.A.8
--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.R.A.8';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.R.A.8','DRAFT'),
('E','I.R.A.8','REVISI/EDIT'),
('F','I.R.A.8','FINAL USER'),
('A','I.R.A.8','APPROVE'),
('A2','I.R.A.8','APPROVE 2'),
('A3','I.R.A.8','APPROVE 3'),
('P','I.R.A.8','CETAK/PRINT'),
('O','I.R.A.8','OBSOLATE'),
('C','I.R.A.8','CANCEL'),
('D','I.R.A.8','DELETE');




--drop table sc_tmp.materialrelease_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.materialrelease_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'BOM' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idlocation character(12) COLLATE pg_catalog."default",
    nmlocation character(50) COLLATE pg_catalog."default",
    tabno character(50) COLLATE pg_catalog."default",
    woeno character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi TEXT,
    buildfor numeric(18,2),
    buildunit character(30),
    batchno character(50),
    bagian character(30) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_materialrelease_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.materialrelease_mst
    OWNER to postgres;


/** 'MATERIAL, COST, WIP' **/
--drop table sc_tmp.materialrelease_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.materialrelease_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'BOM' ,
    doctype_detail character(20) default 'MATERIAL' ,
    cabang character (30 ) COLLATE pg_catalog."default",
    docref character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    idbarang_bom character(50) COLLATE pg_catalog."default",
    nmbarang_bom text,
    description text,
	qty numeric(18,2),
    unit char(20),
	spec char(100),
    issub character(6),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut bigserial,
	uniqueid text,
    CONSTRAINT pk_tmp_materialrelease_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.materialrelease_dtl
    OWNER to postgres;


/* MST ATAU TRANSAKSI */

--drop table sc_trx.materialrelease_mst;
CREATE TABLE IF NOT EXISTS sc_trx.materialrelease_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'BOM' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idlocation character(12) COLLATE pg_catalog."default",
    nmlocation character(50) COLLATE pg_catalog."default",
    tabno character(50) COLLATE pg_catalog."default",
    woeno character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi TEXT,
    buildfor numeric(18,2),
    buildunit character(30),
    batchno character(50),
    bagian character(30) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_materialrelease_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.materialrelease_mst
    OWNER to postgres;


--drop table sc_trx.materialrelease_dtl;
CREATE TABLE IF NOT EXISTS sc_trx.materialrelease_dtl
(
	docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'BOM' ,
    doctype_detail character(20) default 'MATERIAL' ,
    cabang character (30 ) COLLATE pg_catalog."default",
    docref character (30 ) COLLATE pg_catalog."default",        
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    idbarang_bom character(50) COLLATE pg_catalog."default",
    nmbarang_bom text,
    description text,
	qty numeric(18,2),
    unit char(20),
	spec char(100),
    issub character(6),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut integer,
	uniqueid text,
    CONSTRAINT pk_tmp_materialrelease_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.materialrelease_dtl
    OWNER to postgres;


-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_materialrelease_mst
ON sc_tmp.materialrelease_mst;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_materialrelease_mst();

-- =========================================
-- FUNCTION TMP -> TRX
-- =========================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_materialrelease_mst()
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

    RAISE NOTICE 'TMP BOM TRIGGER : %, OLD=%, NEW=%',
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
                FROM sc_trx.materialrelease_mst
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
        INSERT INTO sc_trx.materialrelease_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idlocation,
            nmlocation,
            tabno,
            woeno,
            wono,
            bomno,
            desc_bom,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            status,
            keterangan,
            batchno,
            buildfor,
            buildunit,
            bagian,
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
            idlocation,
            nmlocation,
            tabno,
            woeno,
            wono,
            bomno,
            desc_bom,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            'F',
            keterangan,
            batchno,
            buildfor,
            buildunit,
            bagian,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno)=TRIM(OLD.docno);

        -- =================================================
        -- INSERT DETAIL
        -- =================================================
        INSERT INTO sc_trx.materialrelease_dtl (

            docno,
            doctype,
            doctype_detail,
            cabang,
            pemohon,
            docdate,
            docref,
            idbarang,
            nmbarang,
            idbarang_bom,
            nmbarang_bom,
            description,
            qty,
            unit,
            spec,
            issub,
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

            v_docno,
            doctype,
            doctype_detail,
            cabang,
            pemohon,
            docdate,
            docref,
            idbarang,
            nmbarang,
            idbarang_bom,
            nmbarang_bom,
            description,
            qty,
            unit,
            spec,
            issub,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(OLD.docno);

        -- =================================================
        -- CLEAN TMP
        -- =================================================
        DELETE FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(OLD.docno);

        DELETE FROM sc_tmp.materialrelease_mst
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
        DELETE FROM sc_trx.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.materialrelease_mst
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        -- =================================================
        -- INSERT MASTER
        -- =================================================
        INSERT INTO sc_trx.materialrelease_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idlocation,
            nmlocation,
            tabno,
            woeno,
            wono,
            bomno,
            desc_bom,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            status,
            keterangan,
            batchno,
            buildfor,
            buildunit,
            bagian,
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
            idlocation,
            nmlocation,
            tabno,
            woeno,
            wono,
            bomno,
            desc_bom,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            'F',
            keterangan,
            batchno,
            buildfor,
            buildunit,
            bagian,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =================================================
        -- INSERT DETAIL
        -- =================================================
        INSERT INTO sc_trx.materialrelease_dtl (

            docno,
            doctype,
            doctype_detail,
            cabang,
            pemohon,
            docdate,
            docref,
            idbarang,
            nmbarang,
            idbarang_bom,
            nmbarang_bom,
            description,
            qty,
            unit,
            spec,
            issub,
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

            NEW.docnotmp,
            doctype,
            doctype_detail,
            cabang,
            pemohon,
            docdate,
            docref,
            idbarang,
            nmbarang,
            idbarang_bom,
            nmbarang_bom,
            description,
            qty,
            unit,
            spec,
            issub,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =================================================
        -- CLEAN TMP
        -- =================================================
        DELETE FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

    -- =====================================================
    -- CANCEL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='C' THEN

        UPDATE sc_trx.materialrelease_mst
        SET status='F'
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        DELETE FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_tmp_materialrelease_mst
AFTER UPDATE
ON sc_tmp.materialrelease_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_materialrelease_mst();



-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_mst_materialrelease_mst
ON sc_trx.materialrelease_mst;

DROP FUNCTION IF EXISTS sc_trx.tr_mst_materialrelease_mst();

-- =========================================
-- FUNCTION TRX -> TMP
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.tr_mst_materialrelease_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$

BEGIN

    RAISE NOTICE 'TRX BOM TRIGGER : %, OLD=%, NEW=%',
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
        INSERT INTO sc_tmp.materialrelease_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            idlocation,
            nmlocation,
            tabno,
            woeno,
            wono,
            bomno,
            desc_bom,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            status,
            keterangan,
            batchno,
            buildfor,
            buildunit,
            bagian,
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
            idlocation,
            nmlocation,
            tabno,
            woeno,
            wono,
            bomno,
            desc_bom,
            idbarang_jadi,
            nmbarang_jadi,
            docref,
            'E',
            keterangan,
            batchno,
            buildfor,
            buildunit,
            bagian,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docno

        FROM sc_trx.materialrelease_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =================================================
        -- INSERT TMP DETAIL
        -- =================================================
        INSERT INTO sc_tmp.materialrelease_dtl (

            docno,
            doctype,
            doctype_detail,
            cabang,
            pemohon,
            docdate,
            docref,
            idbarang,
            nmbarang,
            idbarang_bom,
            nmbarang_bom,
            description,
            qty,
            unit,
            spec,
            issub,
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
            pemohon,
            docdate,
            docref,
            idbarang,
            nmbarang,
            idbarang_bom,
            nmbarang_bom,
            description,
            qty,
            unit,
            spec,
            issub,
            'E',
            inputby,
            inputdate,
            updateby,
            updatedate,
            docno,
            uniqueid

        FROM sc_trx.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_mst_materialrelease_mst
AFTER UPDATE
ON sc_trx.materialrelease_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_mst_materialrelease_mst();