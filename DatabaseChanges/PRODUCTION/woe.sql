
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.woe
DROP TABLE IF EXISTS sc_trx.woe


--I.R.A.5
--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.R.A.5';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.R.A.5','DRAFT'),
('E','I.R.A.5','REVISI/EDIT'),
('F','I.R.A.5','FINAL USER'),
('A','I.R.A.5','APPROVE'),
('A2','I.R.A.5','APPROVE 2'),
('A3','I.R.A.5','APPROVE 3'),
('P','I.R.A.5','CETAK/PRINT'),
('O','I.R.A.5','OBSOLATE'),
('C','I.R.A.5','CANCEL'),
('D','I.R.A.5','DELETE');



CREATE TABLE IF NOT EXISTS sc_tmp.woe
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    bagian character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi text,
    buildfor numeric(18,2),
    batchno character(50),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_woe PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.woe
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.woe
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    bagian character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi text,
    buildfor numeric(18,2),
    batchno character(50),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_woe PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.woe
    OWNER to postgres;







-- FUNCTION: sc_tmp.tr_woe_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_woe_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_woe_finalize()
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
                FROM sc_trx.woe
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
        INSERT INTO sc_trx.woe (
            idurut, docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor,batchno,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor,batchno,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate
        FROM sc_tmp.woe
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.woe
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;


    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.woe WHERE docno = NEW.docnotmp;


        INSERT INTO sc_trx.woe
        (idurut, docno, cabang, docdate, pemohon, bagian,
        wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
        buildfor,batchno,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor,batchno,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, docnotmp
        FROM sc_tmp.woe
        WHERE rtrim(docno) = rtrim(NEW.docno);

        DELETE FROM sc_tmp.woe WHERE rtrim(docno) = rtrim(NEW.docno);
        
    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.woe SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.woe SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.woe WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_woe_finalize
    AFTER UPDATE ON sc_tmp.woe
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_woe_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_woe();

CREATE OR REPLACE FUNCTION sc_trx.tr_woe()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE 
	vr_nomor char(15); 
	vr_cekprefix char(15);
	vr_nowprefix char(15);
	vr_lastdoc NUMERIC(18);
BEGIN		

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.woe
            (
            idurut, docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor,batchno,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor,batchno,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, NEW.docno
			FROM sc_trx.woe 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_woe()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_woe()
-- Trigger: tr_woe

-- DROP TRIGGER IF EXISTS tr_woe ON sc_trx.woe;

CREATE OR REPLACE TRIGGER tr_woe
    AFTER UPDATE 
    ON sc_trx.woe
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_woe();




