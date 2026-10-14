--I.R.A.1
--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.Q.A.3';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.R.A.1','DRAFT'),
('E','I.R.A.1','REVISI/EDIT'),
('F','I.R.A.1','FINAL USER'),
('A','I.R.A.1','APPROVE'),
('A2','I.R.A.1','APPROVE 2'),
('A3','I.R.A.1','APPROVE 3'),
('P','I.R.A.1','CETAK/PRINT'),
('O','I.R.A.1','OBSOLATE'),
('C','I.R.A.1','CANCEL'),
('D','I.R.A.1','DELETE');




--drop table sc_tmp.standart_cost_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.standart_cost_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    activedate date,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
	penyesuaian_a character(30) COLLATE pg_catalog."default",
	penyesuaian_b character(30) COLLATE pg_catalog."default",
	dari_bagian	character(30),
	ajustment BOOLEAN default false,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_standart_cost_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.standart_cost_mst
    OWNER to postgres;


--drop table sc_tmp.standart_cost_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.standart_cost_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
	activedate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    description text,
    unit char(20),
    actualcost numeric(18,5),
    lastcost numeric(18,5),
    newcost numeric(18,5),
	currcode char(3),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut bigserial,
	uniqueid text,
    CONSTRAINT pk_tmp_standart_cost_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.standart_cost_dtl
    OWNER to postgres;


/* MST ATAU TRANSAKSI */

--drop table sc_mst.standart_cost_mst;
CREATE TABLE IF NOT EXISTS sc_mst.standart_cost_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    activedate date,
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
	penyesuaian_a character(30) COLLATE pg_catalog."default",
	penyesuaian_b character(30) COLLATE pg_catalog."default",
	dari_bagian	character(30),
	ajustment BOOLEAN default false,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_standart_cost_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.standart_cost_mst
    OWNER to postgres;


--drop table sc_mst.standart_cost_dtl;
CREATE TABLE IF NOT EXISTS sc_mst.standart_cost_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'STD_COST' ,
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
	activedate  date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    description text,
    unit char(20),
    actualcost numeric(18,5),
    lastcost numeric(18,5),
    newcost numeric(18,5),
	currcode char(3),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut INTEGER,
	uniqueid text,
    CONSTRAINT pk_tmp_standart_cost_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.standart_cost_dtl
    OWNER to postgres;


ALTER TABLE sc_mst.mbarang
ADD COLUMN IF NOT EXISTS actualcost NUMERIC(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS lastcost NUMERIC(18,2) DEFAULT 0;


-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_standart_cost_mst
ON sc_tmp.standart_cost_mst;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_standart_cost_mst();

-- =========================================
-- FUNCTION
-- =========================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_standart_cost_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$

DECLARE

    v_docno        TEXT;
    v_inputby      TEXT;
    v_inputdate    TIMESTAMP;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      INTEGER;
    v_doctype      TEXT;

BEGIN

    v_doctype := TRIM(COALESCE(NEW.doctype,'STD_COST'));

    RAISE NOTICE 'STANDARD COST TRIGGER: %, OLD=%, NEW=%',
        NEW.docno,
        OLD.status,
        NEW.status;

    -- =====================================
    -- FINAL NORMAL
    -- =====================================
    IF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='F'
       AND COALESCE(TRIM(NEW.docnotmp),'')='' THEN

        v_docno      := TRIM(NEW.docno);
        v_inputby    := NEW.inputby;
        v_inputdate  := NEW.inputdate;

        -- =====================================
        -- DOCNO LOCK
        -- =====================================
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        PERFORM pg_advisory_xact_lock(hashtext(v_base_docno));

        v_new_docno := v_docno;

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_mst.standart_cost_mst
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

        -- =====================================
        -- INSERT HEADER
        -- =====================================
        INSERT INTO sc_mst.standart_cost_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            activedate,
            docref,
            status,
            description,
            penyesuaian_a,
            penyesuaian_b,
            dari_bagian,
            ajustment,
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
            activedate,
            docref,
            'F',
            description,
            penyesuaian_a,
            penyesuaian_b,
            dari_bagian,
            ajustment,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.standart_cost_mst

        WHERE TRIM(docno)=TRIM(OLD.docno)
          AND inputby=v_inputby;

        -- =====================================
        -- INSERT DETAIL
        -- =====================================
        INSERT INTO sc_mst.standart_cost_dtl (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            activedate,
            idbarang,
            nmbarang,
            description,
            unit,
            actualcost,
            lastcost,
            newcost,
            currcode,
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
            cabang,
            pemohon,
            docdate,
            activedate,
            idbarang,
            nmbarang,
            description,
            unit,

            COALESCE(actualcost,0),
            COALESCE(lastcost,0),
            COALESCE(newcost,0),

            currcode,

            'F',

            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        FROM sc_tmp.standart_cost_dtl

        WHERE TRIM(docno)=TRIM(OLD.docno)
          AND inputby=v_inputby;

        -- =====================================
        -- UPDATE MASTER COST
        -- =====================================
        UPDATE sc_mst.mbarang mb
        SET
            actualcost = d.newcost,
            lastcost   = d.lastcost
        FROM sc_mst.standart_cost_dtl d
        WHERE d.docno=v_docno
          AND d.idbarang=mb.idbarang;

        -- =====================================
        -- CLEAN TMP
        -- =====================================
        DELETE FROM sc_tmp.standart_cost_mst
        WHERE TRIM(docno)=TRIM(OLD.docno);

        DELETE FROM sc_tmp.standart_cost_dtl
        WHERE TRIM(docno)=TRIM(OLD.docno);

    -- =====================================
    -- FINAL EDIT
    -- =====================================
    ELSIF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='F'
       AND COALESCE(TRIM(NEW.docnotmp),'')<>'' THEN

        -- =====================================
        -- DELETE OLD
        -- =====================================
        DELETE FROM sc_mst.standart_cost_mst
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        DELETE FROM sc_mst.standart_cost_dtl
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        -- =====================================
        -- INSERT HEADER
        -- =====================================
        INSERT INTO sc_mst.standart_cost_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            activedate,
            docref,
            status,
            description,
            penyesuaian_a,
            penyesuaian_b,
            dari_bagian,
            ajustment,
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
            activedate,
            docref,
            'F',
            description,
            penyesuaian_a,
            penyesuaian_b,
            dari_bagian,
            ajustment,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp

        FROM sc_tmp.standart_cost_mst

        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =====================================
        -- INSERT DETAIL
        -- =====================================
        INSERT INTO sc_mst.standart_cost_dtl (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            activedate,
            idbarang,
            nmbarang,
            description,
            unit,
            actualcost,
            lastcost,
            newcost,
            currcode,
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
            cabang,
            pemohon,
            docdate,
            activedate,
            idbarang,
            nmbarang,
            description,
            unit,

            COALESCE(actualcost,0),
            COALESCE(lastcost,0),
            COALESCE(newcost,0),

            currcode,

            'F',

            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp,
            idurut,
            uniqueid

        FROM sc_tmp.standart_cost_dtl

        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =====================================
        -- UPDATE MASTER COST
        -- =====================================
        UPDATE sc_mst.mbarang mb
        SET
            actualcost = d.newcost,
            lastcost   = d.lastcost
        FROM sc_mst.standart_cost_dtl d
        WHERE d.docno=NEW.docnotmp
          AND d.idbarang=mb.idbarang;

        -- =====================================
        -- CLEAN TMP
        -- =====================================
        DELETE FROM sc_tmp.standart_cost_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.standart_cost_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

    -- =====================================
    -- CANCEL EDIT
    -- =====================================
    ELSIF UPPER(TRIM(OLD.status))='E'
       AND UPPER(TRIM(NEW.status))='C' THEN

        UPDATE sc_mst.standart_cost_mst
        SET status='F'
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        DELETE FROM sc_tmp.standart_cost_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.standart_cost_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_tmp_standart_cost_mst
AFTER UPDATE
ON sc_tmp.standart_cost_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_standart_cost_mst();





-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_mst_standart_cost_mst
ON sc_mst.standart_cost_mst;

DROP FUNCTION IF EXISTS sc_mst.tr_mst_standart_cost_mst();

-- =========================================
-- FUNCTION
-- =========================================
CREATE OR REPLACE FUNCTION sc_mst.tr_mst_standart_cost_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$

DECLARE

    v_inputdate TIMESTAMP;

BEGIN

    RAISE NOTICE 'MST STANDARD COST TRIGGER: %, OLD=%, NEW=%',
        NEW.docno,
        OLD.status,
        NEW.status;

    -- =====================================
    -- EDIT -> TMP
    -- =====================================
    IF UPPER(TRIM(OLD.status))='F'
       AND UPPER(TRIM(NEW.status))='E' THEN

        -- =====================================
        -- INSERT HEADER TMP
        -- =====================================
        INSERT INTO sc_tmp.standart_cost_mst (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            activedate,
            docref,
            status,
            description,
            penyesuaian_a,
            penyesuaian_b,
            dari_bagian,
            ajustment,
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
            activedate,
            docref,

            'E',

            description,
            penyesuaian_a,
            penyesuaian_b,
            dari_bagian,
            ajustment,
            inputby,
            inputdate,
            updateby,
            updatedate,

            docno AS docnotmp

        FROM sc_mst.standart_cost_mst

        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- =====================================
        -- INSERT DETAIL TMP
        -- =====================================
        INSERT INTO sc_tmp.standart_cost_dtl (

            docno,
            doctype,
            cabang,
            pemohon,
            docdate,
            activedate,
            idbarang,
            nmbarang,
            description,
            unit,
            actualcost,
            lastcost,
            newcost,
            currcode,
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
            cabang,
            pemohon,
            docdate,
            activedate,
            idbarang,
            nmbarang,
            description,
            unit,

            COALESCE(actualcost,0),
            COALESCE(lastcost,0),
            COALESCE(newcost,0),

            currcode,

            'E',

            inputby,
            inputdate,
            updateby,
            updatedate,

            docno AS docnotmp,

            idurut,
            uniqueid

        FROM sc_mst.standart_cost_dtl

        WHERE TRIM(docno)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_mst_standart_cost_mst
AFTER UPDATE
ON sc_mst.standart_cost_mst
FOR EACH ROW
EXECUTE FUNCTION sc_mst.tr_mst_standart_cost_mst();