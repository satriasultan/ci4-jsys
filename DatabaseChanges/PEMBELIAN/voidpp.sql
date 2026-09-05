drop table sc_trx.voidpp;
drop table sc_tmp.voidpp;

drop table sc_trx.voidpp_dtl;
drop table sc_tmp.voidpp_dtl;

CREATE TABLE IF NOT EXISTS sc_tmp.voidpp
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_voidpp PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.voidpp
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.voidpp
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_voidpp PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.voidpp
    OWNER to postgres;




CREATE TABLE IF NOT EXISTS sc_tmp.voidpp_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
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
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.voidpp_dtl
    OWNER TO postgres;



CREATE TABLE IF NOT EXISTS sc_trx.voidpp_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
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
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.voidpp_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_voidpp_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_voidpp_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_voidpp_finalize()
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
                FROM sc_trx.voidpp
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
        INSERT INTO sc_trx.voidpp (
            idurut, docno, cabang, docdate, pemohon,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.voidpp
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.voidpp_dtl (
            idurut, docno, docnopp, idbarang, uniqueid,  nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, docnopp, idbarang, uniqueid,  nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.voidpp_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;


        UPDATE sc_trx.pp_dtl ppd
        SET qtyvoid = COALESCE(ppd.qtyvoid, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.voidpp_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP_DTL -> VP
        -- ===============================
        UPDATE sc_trx.pp_dtl p
        SET status = CASE 
            WHEN p.qty = COALESCE(p.qtyvoid, 0) THEN 'VP'
            ELSE 'F'
        END
        FROM sc_tmp.voidpp_dtl t
        WHERE t.docno = OLD.docno
        AND t.inputby = v_inputby
        AND p.uniqueid = t.uniqueid;


        -- ===============================
        -- UPDATE STATUS PP HEADER -> VP
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = 'VP'
        WHERE pp.docno IN (
            SELECT DISTINCT docnopp 
            FROM sc_tmp.voidpp_dtl 
            WHERE docno = OLD.docno
            AND inputby = v_inputby
        )
        AND NOT EXISTS (
            SELECT 1 
            FROM sc_trx.pp_dtl ppd
            WHERE ppd.docno = pp.docno
            AND ppd.qty != COALESCE(ppd.qtyvoid, 0)
        );


        -- ===============================
        -- LOG: INSERT HEADER VOID PP
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.2',              -- kode menu untuk VOID PP
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );


        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.voidpp
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        DELETE FROM sc_tmp.voidpp_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        -- ===============================
        -- STEP 1: REVERT QTYVOID (KURANGI DENGAN DATA LAMA)
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET qtyvoid = COALESCE(ppd.qtyvoid, 0) - COALESCE(pod_lama.qty_void_lama, 0)
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_void_lama
            FROM sc_trx.voidpp_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod_lama
        WHERE ppd.uniqueid = pod_lama.uniqueid;


        -- =================================
        -- STEP 2 : DELETE TRX LAMA
        -- =================================
        DELETE FROM sc_trx.voidpp WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.voidpp_dtl WHERE docno = NEW.docnotmp;

        -- =================================
        -- STEP 3 : INSERT TRX BARU
        -- =================================

        INSERT INTO sc_trx.voidpp_dtl
        (idurut, docno, docnopp, idbarang, uniqueid,  nmbarang, unit, qty, description,
         inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, docnopp, idbarang, uniqueid,  nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.voidpp_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);


        INSERT INTO sc_trx.voidpp
        (idurut, docno, cabang, docdate, pemohon,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.voidpp
        WHERE rtrim(docno) = rtrim(NEW.docno);


        -- ===============================
        -- STEP 4: TAMBAH QTYVOID DENGAN DATA BARU
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET qtyvoid = COALESCE(ppd.qtyvoid, 0) + pod_baru.qty_used
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.voidpp_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod_baru
        WHERE ppd.uniqueid = pod_baru.uniqueid;


        -- =================================
        -- STEP 5 : UPDATE STATUS
        -- =================================

        -- ===============================
        -- UPDATE STATUS PP HEADER -> VP
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = 'VP'
        WHERE pp.docno IN (
            SELECT DISTINCT docnopp 
            FROM sc_tmp.voidpp_dtl 
            WHERE docno = OLD.docno
            AND inputby = v_inputby
        )
        AND NOT EXISTS (
            SELECT 1 
            FROM sc_trx.pp_dtl ppd
            WHERE ppd.docno = pp.docno
            AND ppd.qty != COALESCE(ppd.qtyvoid, 0)
        );


        -- ===============================
        -- UPDATE STATUS PP_DTL -> VP
        -- ===============================
        UPDATE sc_trx.pp_dtl p
        SET status = CASE 
            WHEN p.qty = COALESCE(p.qtyvoid, 0) THEN 'VP'
            ELSE 'F'
        END
        FROM sc_tmp.voidpp_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
        AND t.inputby = v_inputby
        AND p.uniqueid = t.uniqueid;

        -- ===============================
        -- LOG: INSERT HEADER VOID PP
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.2',              -- kode menu untuk VOID PP
            'U',                    -- action: UPDATE (1 huruf)
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.voidpp WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.voidpp_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.voidpp SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.voidpp SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.voidpp WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.voidpp_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_voidpp_finalize
    AFTER UPDATE ON sc_tmp.voidpp
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_voidpp_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_voidpp();

CREATE OR REPLACE FUNCTION sc_trx.tr_voidpp()
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

    v_total_po  NUMERIC(18,2);
    v_total_void NUMERIC(18,2);
    v_rec       RECORD;
BEGIN		

        -- ===============================
        -- AMBIL IP DARI sc_log.useronline
        -- ===============================
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;

        v_client_ip := sc_log.fn_get_user_ip(v_inputby);
        -- ===============================
        -- ADVISORY LOCK (CEGAH RACE CONDITION)
        -- ===============================
        PERFORM pg_advisory_xact_lock(hashtext(NEW.docno));
        -- ===============================
        -- VOID PP DIBATALKAN (F -> C) - REVERT QTYVOID
        -- ===============================
        IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN
            
            -- ===============================
            -- REVERT QTYVOID DI PP_DTL
            -- ===============================
            UPDATE sc_trx.pp_dtl ppd
            SET qtyvoid = COALESCE(ppd.qtyvoid, 0) - pod.qty_used
            FROM (
                SELECT 
                    uniqueid,
                    SUM(qty) as qty_used
                FROM sc_trx.voidpp_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL
                    AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE ppd.uniqueid = pod.uniqueid;

            -- ===============================
            -- UPDATE STATUS PP_DTL BERDASARKAN QTY
            -- ===============================
            UPDATE sc_trx.pp_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtyvoid, 0) THEN 'VP'
                ELSE 'F'
            END
            FROM sc_trx.voidpp_dtl vd
            WHERE ppd.uniqueid = vd.uniqueid
                AND rtrim(vd.docno) = rtrim(NEW.docno);
            
            -- ===============================
            -- UPDATE STATUS PP HEADER BERDASARKAN QTY
            -- ===============================
            UPDATE sc_trx.pp pp
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.pp_dtl ppd
                    WHERE rtrim(ppd.docno) = rtrim(pp.docno)
                    AND ppd.qty != COALESCE(ppd.qtyvoid, 0)
                ) THEN 'P'
                ELSE 'VP'
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.voidpp_dtl vd
                WHERE rtrim(vd.docno) = rtrim(NEW.docno)
                AND vd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.pp_dtl 
                    WHERE rtrim(docno) = rtrim(pp.docno)
                )
            );
            
            -- ===============================
            -- LOG: INSERT HEADER VOID PP
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',                  -- kode module dari menuprg
                'I.P.A.2',              -- kode menu untuk VOID PP
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );
            
        END IF;

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.voidpp_dtl
			( idurut, docno, docnopp, idbarang, uniqueid, nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, docnopp, idbarang, uniqueid, nmbarang, unit, qty, description,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.voidpp_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.voidpp
            (
                idurut, docno, cabang, docdate, pemohon,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.voidpp 
			WHERE docno = NEW.docno;


            -- ===============================
            -- LOG: INSERT HEADER VOID PP
            -- ===============================
            -- PERFORM sc_log.fn_log_transaction(
            --     NEW.docno,
            --     NULL,
            --     'I.P',                  -- kode module dari menuprg
            --     'I.P.A.2',              -- kode menu untuk VOID PP
            --     'E',                    -- action: UPDATE (1 huruf)
            --     COALESCE(NEW.updateby, NEW.inputby),
            --     v_client_ip,
            --     COALESCE(NEW.updateby, NEW.inputby)
            -- );

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_voidpp()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_voidpp()
-- Trigger: tr_voidpp

-- DROP TRIGGER IF EXISTS tr_voidpp ON sc_trx.voidpp;

CREATE OR REPLACE TRIGGER tr_voidpp
    AFTER UPDATE 
    ON sc_trx.voidpp
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_voidpp();


    

ALTER TABLE sc_tmp.voidpp_dtl
ADD COLUMN uniqueid VARCHAR(64);

ALTER TABLE sc_trx.voidpp_dtl
ADD COLUMN uniqueid VARCHAR(64);



-- =========== TAMBAHAN 24/8/26 ====================
ALTER TABLE sc_tmp.voidpp_dtl
ADD COLUMN capexno character(30)

ALTER TABLE sc_trx.voidpp_dtl
ADD COLUMN capexno character(30)



-- docdate
ALTER TABLE sc_trx.voidpp
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.voidpp
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;


-- printcount
ALTER TABLE sc_tmp.voidpp
ADD COLUMN printcount integer
ALTER TABLE sc_trx.voidpp
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================
