-- ============================================================
-- PP.SQL - ONE RUN INSTALL SCRIPT
-- Semua object PP dibuat ulang dalam 1 transaction.
-- Jika ada error, PostgreSQL akan melakukan ROLLBACK.
-- ============================================================

BEGIN;

-- ============================================================
-- DROP OBJECT TABLE LAMA
-- ============================================================

DROP TABLE IF EXISTS sc_trx.pp_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.pp_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.pp CASCADE;
DROP TABLE IF EXISTS sc_tmp.pp CASCADE;

CREATE TABLE sc_tmp.pp
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate DATE,
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    estpakai DATE,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    printcount INTEGER,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_pp PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.pp
    OWNER to postgres;





CREATE TABLE sc_trx.pp
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate DATE,
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    estpakai DATE,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    printcount INTEGER,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_pp PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.pp
    OWNER to postgres;




CREATE TABLE sc_tmp.pp_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30),
    uniqueid VARCHAR(64),
    capexno CHARACTER(30),
    idtax CHARACTER(20),
    currcode CHARACTER(3),
    kurs NUMERIC(18,2),
    nilaikonversi NUMERIC(18,2),
    nilaipajak NUMERIC(18,2),
    qtypo NUMERIC(18,2) DEFAULT 0,
    qtyvoid NUMERIC(18,2) DEFAULT 0
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.pp_dtl
    OWNER TO postgres;



CREATE TABLE sc_trx.pp_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30),
    uniqueid VARCHAR(64),
    capexno CHARACTER(30),
    idtax CHARACTER(20),
    currcode CHARACTER(3),
    kurs NUMERIC(18,2),
    nilaikonversi NUMERIC(18,2),
    nilaipajak NUMERIC(18,2),
    qtypo NUMERIC(18,2) DEFAULT 0,
    qtyvoid NUMERIC(18,2) DEFAULT 0
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.pp_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_pp_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_pp_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_pp_finalize()
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
    
    v_client_ip TEXT;
    v_uniqueid  VARCHAR(64);
BEGIN

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

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
        /* ========================================================
        CEK APAKAH SUDAH PERNAH DIFINALKAN
        ======================================================== */

        IF EXISTS (
            SELECT 1
            FROM sc_trx.pp
            WHERE idurut = v_idurut
                AND TRIM(COALESCE(inputby, '')) =
                    TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.pp
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
                FROM sc_trx.pp
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
        INSERT INTO sc_trx.pp (
            idurut, docno, cabang, docdate, pemohon, estpakai,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, estpakai,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.pp
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.pp_dtl (
            idurut, docno, idbarang, capexno, uniqueid, nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idbarang, capexno, uniqueid, nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.pp_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;


        -- ===============================
        -- LOG: INSERT HEADER PP
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.1',              -- kode menu untuk PP
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );
        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.pp
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        DELETE FROM sc_tmp.pp_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.pp WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.pp_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.pp_dtl
        (idurut, docno, idbarang, capexno, uniqueid, nmbarang, unit, qty, description,
         inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, idbarang, capexno, uniqueid, nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.pp_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.pp
        (idurut, docno, cabang, docdate, pemohon, estpakai,
         keterangan, status, inputby, inputdate,
         updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, estpakai,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.pp
        WHERE rtrim(docno) = rtrim(NEW.docno);

         -- ===============================
        -- LOG: INSERT HEADER PP
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.1',              -- kode menu untuk PP
            'U',                    -- action: UPDATE (1 huruf)
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.pp WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.pp_dtl WHERE rtrim(docno) = rtrim(NEW.docno);


    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.pp SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.pp SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.pp WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.pp_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE OR REPLACE TRIGGER tr_pp_finalize
    AFTER UPDATE ON sc_tmp.pp
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_pp_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_pp();

CREATE OR REPLACE FUNCTION sc_trx.tr_pp()
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
    v_docno     TEXT;
    v_client_ip TEXT;
    v_inputby   TEXT;
BEGIN		
        -- ===============================
        -- AMBIL IP DARI sc_log.useronline
        -- ===============================
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;

        v_client_ip := sc_log.fn_get_user_ip(v_inputby);

        
        IF (OLD.STATUS='F' AND NEW.STATUS='C') THEN
            -- ===============================
            -- LOG: INSERT HEADER PP
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',                  -- kode module dari menuprg
                'I.P.A.1',              -- kode menu untuk PP
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );

        END IF;


		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.pp_dtl
			( idurut, docno, idbarang, capexno, uniqueid,nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idbarang, capexno, uniqueid,nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.pp_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.pp
            (
                idurut, docno, cabang, docdate, pemohon, estpakai,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, estpakai,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.pp 
			WHERE docno = NEW.docno;


            -- -- ===============================
            -- -- LOG: INSERT HEADER PP
            -- -- ===============================
            -- PERFORM sc_log.fn_log_transaction(
            --     NEW.docno,
            --     NULL,
            --     'I.P',                  -- kode module dari menuprg
            --     'I.P.A.1',              -- kode menu untuk PP
            --     'E',                    -- action: UPDATE (1 huruf)
            --     COALESCE(NEW.updateby, NEW.inputby),
            --     v_client_ip,
            --     COALESCE(NEW.updateby, NEW.inputby)
            -- );

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_pp()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_pp()
-- Trigger: tr_pp

-- DROP TRIGGER IF EXISTS tr_pp ON sc_trx.pp;

CREATE OR REPLACE TRIGGER tr_pp
    AFTER UPDATE 
    ON sc_trx.pp
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_pp();

-- ============================================================
-- SELESAI
-- ============================================================

COMMIT;
