
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.jup_dtl
DROP TABLE IF EXISTS sc_trx.jup_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.jup
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    balance numeric(18,2),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_jup PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.jup
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.jup
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    balance numeric(18,2),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_jup PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.jup
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.jup_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idcoa CHARACTER(20) COLLATE pg_catalog."default",
    nmcoa CHARACTER(250) COLLATE pg_catalog."default",
    remarks TEXT COLLATE pg_catalog."default",
    dk character(20),
    cabang character (30) COLLATE pg_catalog."default",
    nilai numeric(18,2),
    uniqueid VARCHAR(64),
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.jup_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.jup_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idcoa CHARACTER(20) COLLATE pg_catalog."default",
    nmcoa CHARACTER(250) COLLATE pg_catalog."default",
    remarks TEXT COLLATE pg_catalog."default",
    dk character(20),
    cabang character (30) COLLATE pg_catalog."default",
    nilai numeric(18,2),
    uniqueid VARCHAR(64),
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.jup_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_jup_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_jup_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_jup_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_idurut    INTEGER;
    v_prefix    TEXT;
    v_num       TEXT;
    v_num_int   INTEGER;
    v_lock_key  BIGINT;
    v_base_docno TEXT;
    v_new_docno  TEXT;
BEGIN
    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        -- ===============================
        -- NORMALISASI
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;
        -- ambil base docno (tanpa angka belakang)
        -- contoh:
        -- 05M/2601/PA0001 -> 05M/2601/PA
        -- PPB/2601/PT0025 -> PPB/2601/PT
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        -- ===============================
        -- ADVISORY LOCK (ANTI RACE CONDITION)
        -- ===============================
        PERFORM pg_advisory_xact_lock(hashtext(v_base_docno));

        -- ===============================
        -- AUTO INCREMENT JIKA SUDAH ADA
        -- ===============================
        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.jup
                WHERE rtrim(docno) = v_new_docno
            );

            -- ambil angka terakhir (dinamis)
            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');
            v_num_int := v_num::INTEGER + 1;

            -- padding mengikuti panjang awal
            v_new_docno := v_base_docno
                        || lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        -- gunakan docno final
        v_docno := v_new_docno;


        -- ===============================
        -- INSERT HEADER
        -- ===============================
        INSERT INTO sc_trx.jup (
            idurut, docno, cabang, docdate, pemohon, balance,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, balance,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate
        FROM sc_tmp.jup
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.jup_dtl (
            idurut, docno, idcoa, nmcoa, remarks, dk, nilai, uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idcoa, nmcoa, remarks, dk, nilai, uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.jup_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;


        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.jup
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.jup_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.jup WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.jup_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.jup_dtl
        (idurut, docno, idcoa, nmcoa, remarks, dk, nilai, uniqueid, cabang,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, idcoa, nmcoa, remarks, dk, nilai, uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.jup_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);


        INSERT INTO sc_trx.jup
        (idurut, docno, cabang, docdate, pemohon, balance,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, balance,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, docnotmp
        FROM sc_tmp.jup
        WHERE rtrim(docno) = rtrim(NEW.docno);

        DELETE FROM sc_tmp.jup WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.jup_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.jup SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.jup SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.jup WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.jup_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_jup_finalize
    AFTER UPDATE ON sc_tmp.jup
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_jup_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_jup();

CREATE OR REPLACE FUNCTION sc_trx.tr_jup()
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
BEGIN		

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.jup_dtl
			( idurut, docno, idcoa, nmcoa, remarks, dk, nilai, uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idcoa, nmcoa, remarks, dk, nilai, uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.jup_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.jup
            (
                idurut, docno, cabang, docdate,
                pemohon, balance,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, balance,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, NEW.docno
			FROM sc_trx.jup 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_jup()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_jup()
-- Trigger: tr_jup

-- DROP TRIGGER IF EXISTS tr_jup ON sc_trx.jup;

CREATE OR REPLACE TRIGGER tr_jup
    AFTER UPDATE 
    ON sc_trx.jup
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_jup();




