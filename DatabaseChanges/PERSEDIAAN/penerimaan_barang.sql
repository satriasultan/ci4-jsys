-- PNM BRNG - FULL 1x EXECUTE
-- WARNING: script drops existing PNM BRNG tables and their data.
BEGIN;

DROP TRIGGER IF EXISTS tr_tmp_pnm_brng_mst ON sc_tmp.pnm_brng_mst;
DROP TRIGGER IF EXISTS tr_trx_pnm_brng_mst ON sc_trx.pnm_brng_mst;
DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_pnm_brng_mst();
DROP FUNCTION IF EXISTS sc_trx.tr_trx_pnm_brng_mst();
DROP TABLE IF EXISTS sc_tmp.pnm_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.pnm_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.pnm_brng_mst CASCADE;
DROP TABLE IF EXISTS sc_trx.pnm_brng_mst CASCADE;


--drop table sc_tmp.pnm_brng_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.pnm_brng_mst
(
    idurut BIGSERIAL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'pnm_brng' ,
    docdate character(20) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    cabang_sent character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    estpakai character(20) COLLATE pg_catalog."default",
	idlocation_from character(30),
	idlocation_to character(30),
	idlocation_transit character(30),
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_pnm_brng_mst PRIMARY KEY (docno)
)
;

ALTER TABLE IF EXISTS sc_tmp.pnm_brng_mst
    OWNER to postgres;


--drop table sc_trx.pnm_brng_mst;
CREATE TABLE IF NOT EXISTS sc_trx.pnm_brng_mst
(
    idurut BIGINT,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
	doctype character(20) default 'pnm_brng' ,
    docdate character(20) COLLATE pg_catalog."default",
	docref character(30) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
	cabang_sent character (30 ) COLLATE pg_catalog."default",  
    pemohon character(100) COLLATE pg_catalog."default",
    estpakai character(20) COLLATE pg_catalog."default",
	idlocation_from character(30),
	idlocation_to character(30),
	idlocation_transit character(30),
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_pnm_brng_mst PRIMARY KEY (docno)
)
;

ALTER TABLE IF EXISTS sc_trx.pnm_brng_mst
    OWNER to postgres;



--drop table sc_tmp.pnm_brng_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.pnm_brng_dtl
(
    idurut BIGSERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
	docref character(30) COLLATE pg_catalog."default",
	doctype character(20) default 'pnm_brng' ,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qtystock NUMERIC(18,2),
    qty NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
	val numeric(18,2),
	valsum numeric(18,2),
    inputby character(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
	iduniq text,
    docnotmp character(30)
);

ALTER TABLE IF EXISTS sc_tmp.pnm_brng_dtl
    OWNER TO postgres;


--drop table sc_trx.pnm_brng_dtl;
CREATE TABLE IF NOT EXISTS sc_trx.pnm_brng_dtl
(
    idurut INTEGER,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
	docref character(30) COLLATE pg_catalog."default",
	doctype character(20) default 'pnm_brng' ,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
	qtystock NUMERIC(18,2),
    qty NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
	val numeric(18,2),
	valsum numeric(18,2),
    inputby character(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
	iduniq text,
    docnotmp character(30)
);

ALTER TABLE IF EXISTS sc_trx.pnm_brng_dtl
    OWNER TO postgres;



alter table sc_tmp.pnm_brng_mst add column idcostcenter character(10);
alter table sc_trx.pnm_brng_mst add column idcostcenter character(10); 


alter table sc_tmp.pnm_brng_dtl add column idcostcenter character(10), add column batch character(100);
alter table sc_trx.pnm_brng_dtl add column idcostcenter character(10), add column batch character(100);


alter table sc_tmp.pnm_brng_dtl add column idlocation character(10), add column idcoa character(20);
alter table sc_trx.pnm_brng_dtl add column idlocation character(10), add column idcoa character(20);


CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_pnm_brng_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_base_docno TEXT;
    v_new_docno  TEXT;
    v_num       TEXT;
    v_num_int   INTEGER;
    v_inputdate TIMESTAMP;
    v_doctype   TEXT;
    v_idurut    BIGINT;
    v_lock_key  BIGINT;
BEGIN

    -- =========================================
    -- NORMALISASI
    -- =========================================
    v_doctype := UPPER(TRIM(COALESCE(NEW.doctype,'PNM')));

    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        v_docno := TRIM(NEW.docno);
        v_idurut := NEW.idurut;
        v_inputby := NEW.inputby;
        v_inputdate := NEW.inputdate;

        /* ========================================================
        CEK APAKAH SUDAH PERNAH DIFINALKAN
        ======================================================== */

        IF EXISTS (
            SELECT 1
            FROM sc_trx.pnm_brng_mst
            WHERE idurut = v_idurut
                AND TRIM(COALESCE(inputby, '')) =
                    TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.pnm_brng_mst
            WHERE TRIM(docno) = TRIM(OLD.docno)
                AND idurut = v_idurut;

            RETURN NEW;
        END IF;


        /* ========================================================
        GENERATE DOCNO
        ======================================================== */

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        v_lock_key := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.pnm_brng_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(
                v_new_docno,
                '.*?([0-9]+)$',
                '\1'
            );

            IF COALESCE(v_num, '') = '' THEN

                RAISE EXCEPTION
                    'Format DOCNO PP tidak valid: %',
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
        INSERT INTO sc_trx.pnm_brng_mst
        SELECT 
            v_docno, v_doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit,
            'F', description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        FROM sc_tmp.pnm_brng_mst
        WHERE TRIM(docno)=TRIM(OLD.docno)
          AND inputby = v_inputby
          AND inputdate = v_inputdate;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.pnm_brng_dtl
        SELECT 
            v_docno, docref, v_doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idcostcenter,
            batch, idlocation, idcoa
        FROM sc_tmp.pnm_brng_dtl
        WHERE TRIM(docno)=TRIM(OLD.docno)
          AND inputby = v_inputby;

        -- ===============================
        -- 🔥 REPOST UNIVERSAL
        -- ===============================
        PERFORM sc_trx.sp_repost_universal(
            v_docno,
            v_doctype,
            v_inputby
        );

        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.pnm_brng_mst
        WHERE TRIM(docno)=TRIM(OLD.docno)
          AND inputby = v_inputby
          AND inputdate = v_inputdate;

        DELETE FROM sc_tmp.pnm_brng_dtl
        WHERE TRIM(docno)=TRIM(OLD.docno)
          AND inputby = v_inputby;

    -- =========================================
    -- REVISI FLOW
    -- =========================================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        v_inputby := NEW.inputby;

        DELETE FROM sc_trx.pnm_brng_mst WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.pnm_brng_dtl WHERE docno = NEW.docnotmp;

        -- INSERT DETAIL
        INSERT INTO sc_trx.pnm_brng_dtl
        SELECT 
            NEW.docnotmp, docref, v_doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idcostcenter,
            batch, idlocation, idcoa
        FROM sc_tmp.pnm_brng_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- INSERT HEADER
        INSERT INTO sc_trx.pnm_brng_mst
        SELECT 
            NEW.docnotmp, v_doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit,
            'F', description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        FROM sc_tmp.pnm_brng_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

        -- 🔥 REPOST UNIVERSAL
        PERFORM sc_trx.sp_repost_universal(
            NEW.docnotmp,
            v_doctype,
            v_inputby
        );

        -- CLEANUP
        DELETE FROM sc_tmp.pnm_brng_mst WHERE TRIM(docno)=TRIM(NEW.docno);
        DELETE FROM sc_tmp.pnm_brng_dtl WHERE TRIM(docno)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;
END;
$BODY$;

-- FUNCTION: sc_tmp.tr_tmp_pnm_brng_mst()
-- Trigger: tr_tmp_pnm_brng_mst

-- DROP TRIGGER IF EXISTS tr_tmp_pnm_brng_mst ON sc_tmp.pnm_brng_mst;

CREATE OR REPLACE TRIGGER tr_tmp_pnm_brng_mst
    AFTER UPDATE 
    ON sc_tmp.pnm_brng_mst
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_tmp_pnm_brng_mst();




-- DROP FUNCTION IF EXISTS sc_trx.tr_trx_pnm_brng_mst();

CREATE OR REPLACE FUNCTION sc_trx.tr_trx_pnm_brng_mst()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE 
	vr_nomor char(15); 
	vr_cekprefix char(15);
	vr_nowprefix char(15);  
	vr_id_dtl numeric;
	vr_lastdoc NUMERIC(18);
	v_inputdate timestamp without time zone;
BEGIN		

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
		        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_tmp.pnm_brng_dtl 
		(docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut,idcostcenter,batch,idlocation,idcoa)
        (SELECT new.updateby,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,'F' AS status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docno as docnotmp ,idurut,idcostcenter,batch,idlocation,idcoa
		FROM sc_trx.pnm_brng_dtl WHERE trim(docno) =  trim(new.docno));
		
        -- ===============================
        INSERT INTO sc_tmp.pnm_brng_mst (
            docno,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,status,description,inputby,inputdate,updateby,updatedate,printby,printdate,docnotmp,idcostcenter
        )
        (SELECT new.updateby,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,'E',description,inputby,inputdate,updateby,updatedate,printby,printdate,docno as docnotmp,idcostcenter FROM sc_trx.pnm_brng_mst
        WHERE trim(docno) = trim(new.docno));



		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_trx_pnm_brng_mst()
    OWNER TO postgres;


-- FUNCTION: sc_trx.tr_trx_pnm_brng_mst()
-- Trigger: tr_trx_pnm_brng_mst

-- DROP TRIGGER IF EXISTS tr_trx_pnm_brng_mst ON sc_trx.pnm_brng_mst;

CREATE OR REPLACE TRIGGER tr_trx_pnm_brng_mst
    AFTER UPDATE 
    ON sc_trx.pnm_brng_mst
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_trx_pnm_brng_mst();

COMMIT;
