
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.salesorder_dtl
DROP TABLE IF EXISTS sc_trx.salesorder_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.salesorder
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    delivdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    gradecustomer character(30),
    kdsalesman character(10),
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    isopenprice character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    pocust character(30),
    nodp character(30),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_salesorder PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.salesorder
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.salesorder
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    delivdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    gradecustomer character(30),
    kdsalesman character(10),
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    isopenprice character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    pocust character(30),
    nodp character(30),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_salesorder PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.salesorder
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.salesorder_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    idprincipal CHARACTER(20) COLLATE pg_catalog."default",
    idgudang CHARACTER(30) COLLATE pg_catalog."default",
    idspec CHARACTER(30) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    bomdesc TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.salesorder_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.salesorder_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    idprincipal CHARACTER(20) COLLATE pg_catalog."default",
    idgudang CHARACTER(30) COLLATE pg_catalog."default",
    idspec CHARACTER(30) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    bomdesc TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.salesorder_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_salesorder_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_salesorder_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_salesorder_finalize()
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
                FROM sc_trx.salesorder
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
        INSERT INTO sc_trx.salesorder (
            idurut, docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate
        FROM sc_tmp.salesorder
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.salesorder_dtl (
            idurut, docno, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.salesorder_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        -- UPDATE sc_trx.pp p
        --     SET status = 'PO'
        --     WHERE p.docno IN (
        --         SELECT DISTINCT docnopp
        --         FROM sc_tmp.salesorder_dtl
        --         WHERE rtrim(docno) = rtrim(OLD.docno)
        --         AND inputby = v_inputby
        --         AND docnopp IS NOT NULL
        --         AND docnopp <> ''
        -- );

        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.salesorder
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.salesorder_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.salesorder WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.salesorder_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.salesorder_dtl
        (idurut, docno, idbarang, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, unit, qty, 
        harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
        bomdesc, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.salesorder_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

                -- ===============================
        -- UPDATE STATUS PP MENJADI 'PO'
        -- ===============================
        -- UPDATE sc_trx.pp p
        -- SET status = 'PO'
        -- WHERE p.docno IN (
        --     SELECT DISTINCT docnopp
        --     FROM sc_tmp.salesorder_dtl
        --     WHERE rtrim(docno) = rtrim(NEW.docno)
        --         AND docnopp IS NOT NULL
        --         AND docnopp <> ''
        -- );


        INSERT INTO sc_trx.salesorder
        (idurut, docno, cabang, docdate, pemohon, kdcustomer,
        nmcustomer, alamatcustomer, jthtempo,
        delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
        idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, docnotmp
        FROM sc_tmp.salesorder
        WHERE rtrim(docno) = rtrim(NEW.docno);

        DELETE FROM sc_tmp.salesorder WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.salesorder_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.salesorder SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.salesorder SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.salesorder WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.salesorder_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_salesorder_finalize
    AFTER UPDATE ON sc_tmp.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_salesorder_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_salesorder();

CREATE OR REPLACE FUNCTION sc_trx.tr_salesorder()
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
			INSERT INTO sc_tmp.salesorder_dtl
			( idurut, docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.salesorder_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.salesorder
            (
                idurut, docno, cabang, docdate, pemohon, kdcustomer,
                nmcustomer, alamatcustomer, jthtempo,
                delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, NEW.docno
			FROM sc_trx.salesorder 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_salesorder()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_salesorder()
-- Trigger: tr_salesorder

-- DROP TRIGGER IF EXISTS tr_salesorder ON sc_trx.salesorder;

CREATE OR REPLACE TRIGGER tr_salesorder
    AFTER UPDATE 
    ON sc_trx.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_salesorder();






-- Tambahkan kolom di sc_trx.salesorder_dtl
ALTER TABLE sc_trx.salesorder_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtypenjualan numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtyretur numeric(18,2) DEFAULT 0;

-- Tambahkan kolom di sc_tmp.salesorder_dtl
ALTER TABLE sc_tmp.salesorder_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtypenjualan numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtyretur numeric(18,2) DEFAULT 0;





-- ALTER TABLE sc_tmp.salesorder_dtl
-- ADD COLUMN uniqueid VARCHAR(64)

-- ALTER TABLE sc_trx.salesorder_dtl
-- ADD COLUMN uniqueid VARCHAR(64)

