
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.ndk
DROP TABLE IF EXISTS sc_trx.ndk




CREATE TABLE IF NOT EXISTS sc_tmp.ndk
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    dk character(20),
    kdsalesman character(10),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    perkiraanarap character(20),
    perkiraanlawan character(20),
    
    nilai numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_ndk PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.ndk
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.ndk
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    dk character(20),
    kdsalesman character(10),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    perkiraanarap character(20),
    perkiraanlawan character(20),
    
    nilai numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_ndk PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.ndk
    OWNER to postgres;






-- FUNCTION: sc_tmp.tr_ndk_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_ndk_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_ndk_finalize()
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
                FROM sc_trx.ndk
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
        INSERT INTO sc_trx.ndk (
            idurut, docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, kdsalesman, jthtempo,
            isinclusive, dk,perkiraanarap, perkiraanlawan, nilai,
            idtax, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, kdsalesman, jthtempo,
            isinclusive, dk,perkiraanarap, perkiraanlawan, nilai,
            idtax, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate
        FROM sc_tmp.ndk
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;


        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.ndk
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;


    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.ndk WHERE docno = NEW.docnotmp;
        

        INSERT INTO sc_trx.ndk
        (idurut, docno, cabang, docdate, pemohon, kdsupplier,
        nmsupplier, alamatsupplier, kdsalesman, jthtempo,
        isinclusive, dk,perkiraanarap, perkiraanlawan, nilai,
        idtax, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, kdsalesman, jthtempo,
            isinclusive, dk,perkiraanarap, perkiraanlawan, nilai,
            idtax, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, docnotmp
        FROM sc_tmp.ndk
        WHERE rtrim(docno) = rtrim(NEW.docno);

        DELETE FROM sc_tmp.ndk WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.ndk SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.ndk SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.ndk WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_ndk_finalize
    AFTER UPDATE ON sc_tmp.ndk
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_ndk_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_ndk();

CREATE OR REPLACE FUNCTION sc_trx.tr_ndk()
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

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.ndk
            (
                idurut, docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, kdsalesman, jthtempo,
                isinclusive, dk,perkiraanarap, perkiraanlawan, nilai,
                idtax, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, kdsalesman, jthtempo,
            isinclusive, dk,perkiraanarap, perkiraanlawan, nilai,
            idtax, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, NEW.docno
			FROM sc_trx.ndk 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_ndk()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_ndk()
-- Trigger: tr_ndk

-- DROP TRIGGER IF EXISTS tr_ndk ON sc_trx.ndk;

CREATE OR REPLACE TRIGGER tr_ndk
    AFTER UPDATE 
    ON sc_trx.ndk
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_ndk();



