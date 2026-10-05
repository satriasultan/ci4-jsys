
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.penjualan_dtl
DROP TABLE IF EXISTS sc_trx.penjualan_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.penjualan
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    gradecustomer character(30),
    kdcustomerdeliv character(30) COLLATE pg_catalog."default",
    nmcustomerdeliv character(250) COLLATE pg_catalog."default",
    alamatcustomerdeliv TEXT,
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
    carabayar character(30),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    doctype CHARACTER(10) DEFAULT 'SALES',
    CONSTRAINT pk_tmp_penjualan PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.penjualan
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.penjualan
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    gradecustomer character(30),
    kdcustomerdeliv character(30) COLLATE pg_catalog."default",
    nmcustomerdeliv character(250) COLLATE pg_catalog."default",
    alamatcustomerdeliv TEXT,
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
    carabayar character(30),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    doctype CHARACTER(10) DEFAULT 'SALES',
    CONSTRAINT pk_trx_penjualan PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.penjualan
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.penjualan_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnoso CHARACTER(30),
    docnosj CHARACTER(30),
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
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    doctype CHARACTER(10) DEFAULT 'SALES',
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.penjualan_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.penjualan_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnoso CHARACTER(30),
    docnosj CHARACTER(30),
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
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    doctype CHARACTER(10) DEFAULT 'SALES',
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.penjualan_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_penjualan_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_penjualan_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_penjualan_finalize()
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
    v_inputby := NEW.inputby;
    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        -- ===============================
        -- NORMALISASI
        v_docno := rtrim(NEW.docno);
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
                FROM sc_trx.penjualan
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
        INSERT INTO sc_trx.penjualan (
            idurut, docno, cabang, docdate, pemohon, 
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer,nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, 
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer,nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.penjualan
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.penjualan_dtl (
            idurut, docno, docnoso, docnosj, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, docnoso, docnosj, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.penjualan_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        UPDATE sc_trx.salesorder_dtl ppd
        SET qtypenjualan = COALESCE(ppd.qtypenjualan, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.penjualan_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS SO_DTL BERDASARKAN QTYPJO
        -- ===============================
        UPDATE sc_trx.salesorder_dtl sod
        SET status = CASE 
            WHEN sod.qty = COALESCE(sod.qtypenjualan, 0) THEN 'PJO'
            ELSE 'F'
        END
        FROM sc_tmp.penjualan_dtl t
        WHERE rtrim(t.docno) = rtrim(OLD.docno)
        AND t.inputby = v_inputby
        AND sod.uniqueid = t.uniqueid;
        
        -- ===============================
        -- UPDATE STATUS PJO HEADER MENJADI 'PJO' 
        -- JIKA ADA DETAIL YANG QTYPJO > 0
        -- ===============================
        UPDATE sc_trx.salesorder so
        SET status = 'PJO'
        WHERE so.docno IN (
            SELECT DISTINCT t.docnoso
            FROM sc_tmp.penjualan_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND t.docnoso IS NOT NULL
            AND t.docnoso <> ''
        );

        -- ===============================
        -- LOG: INSERT HEADER PJO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.S',                  -- kode module dari menuprg
            'I.S.B.4',              -- kode menu untuk PJO
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.penjualan
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.penjualan_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        -- ===============================
        -- STEP 1: REVERT QTYPJO (KURANGI DENGAN DATA LAMA)
        -- ===============================
        UPDATE sc_trx.salesorder_dtl sod
        SET qtypenjualan = COALESCE(sod.qtypenjualan, 0) - pjo_lama.qty_pjo_lama
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_pjo_lama
            FROM sc_trx.penjualan_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pjo_lama
        WHERE sod.uniqueid = pjo_lama.uniqueid;

        DELETE FROM sc_trx.penjualan WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.penjualan_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.penjualan
        (idurut, docno, cabang, docdate, pemohon, 
        kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
        kdcustomer,nmcustomer, alamatcustomer, jthtempo,
        isopenprice, kdsalesman, gradecustomer,
        idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, 
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer,nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.penjualan
        WHERE rtrim(docno) = rtrim(NEW.docno);
        
        INSERT INTO sc_trx.penjualan_dtl
        (idurut, docno, docnoso, docnosj, idbarang, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, unit, qty, 
        harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
        bomdesc, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, docnoso, docnosj, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.penjualan_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);


        -- DELETE PENJUALAN DT YANG SUDAH TIDAK ADA DI PENJUALAN TMP
        DELETE FROM sc_trx.penjualan_dtl td
        WHERE rtrim(td.docno) = rtrim(NEW.docnotmp)
        AND td.doctype IN ('SALES', 'SALESX')
        AND NOT EXISTS (
            SELECT 1
            FROM sc_tmp.penjualan_dtl d
            WHERE rtrim(d.docno) = rtrim(NEW.docno)
                AND d.uniqueid = td.uniqueid
        );


        UPDATE sc_trx.salesorder_dtl ppd
        SET qtypenjualan = COALESCE(ppd.qtypenjualan, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.penjualan_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;


        -- ===============================
        -- UPDATE STATUS SO_DTL BERDASARKAN QTYPJO
        -- ===============================
        UPDATE sc_trx.salesorder_dtl sod
        SET status = CASE 
            WHEN sod.qty = COALESCE(sod.qtypenjualan, 0) THEN 'PJO'
            ELSE 'F'
        END
        FROM sc_tmp.penjualan_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
        AND t.inputby = v_inputby
        AND sod.uniqueid = t.uniqueid;


        -- ===============================
        -- UPDATE STATUS SO HEADER MENJADI 'PJO' 
        -- JIKA ADA DETAIL YANG QTYPJO > 0
        -- ===============================
        UPDATE sc_trx.salesorder so
        SET status = 'PJO'
        WHERE so.docno IN (
            SELECT DISTINCT t.docnoso
            FROM sc_tmp.penjualan_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.docnoso IS NOT NULL
            AND t.docnoso <> ''
        );


        

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.S',                  -- kode module dari menuprg
            'I.S.B.4',              -- kode menu untuk PJO
            'U',                    -- action: UPDATE (1 huruf)
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        

        DELETE FROM sc_tmp.penjualan WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.penjualan_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.penjualan SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.penjualan SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.penjualan WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.penjualan_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_penjualan_finalize
    AFTER UPDATE ON sc_tmp.penjualan
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_penjualan_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_penjualan();

CREATE OR REPLACE FUNCTION sc_trx.tr_penjualan()
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

        IF (OLD.STATUS='F' AND NEW.STATUS='C') THEN

            -- ===============================
            -- REVERT QTYPJO DI SO_DTL
            -- ===============================
            UPDATE sc_trx.salesorder_dtl sod
            SET qtypenjualan = COALESCE(sod.qtypenjualan, 0) - pod.qty_used
            FROM (
                SELECT 
                    uniqueid,
                    SUM(qty) as qty_used
                FROM sc_trx.penjualan_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL
                    AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE sod.uniqueid = pod.uniqueid;


            -- ===============================
            -- UPDATE STATUS PJO HEADER 
            -- 'PJO' JIKA MASIH ADA QTYPJO, 'P' JIKA TIDAK ADA QTYPJO
            -- ===============================
            UPDATE sc_trx.salesorder so
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.salesorder_dtl sod
                    WHERE rtrim(sod.docno) = rtrim(so.docno)
                    AND COALESCE(sod.qtypenjualan, 0) > 0
                ) THEN 'PJO'   -- masih ada qtypenjualan
                ELSE 'P'      -- tidak ada qtypenjualan
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.penjualan_dtl pd
                WHERE rtrim(pd.docno) = rtrim(NEW.docno)
                AND pd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.salesorder_dtl 
                    WHERE rtrim(docno) = rtrim(so.docno)
                )
            );

            -- ===============================
            -- UPDATE STATUS SO_DTL BERDASARKAN QTYPJO
            -- ===============================
            UPDATE sc_trx.salesorder_dtl sod
            SET status = CASE 
                WHEN sod.qty = COALESCE(sod.qtypenjualan, 0) THEN 'PJO'
                ELSE 'F'
            END
            FROM sc_trx.penjualan_dtl pd
            WHERE sod.uniqueid = pd.uniqueid
                AND rtrim(pd.docno) = rtrim(NEW.docno);


            -- ===============================
            -- LOG: INSERT HEADER PJO
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.S',                  -- kode module dari menuprg
                'I.S.B.4',              -- kode menu untuk PJO
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );

        END IF;

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.penjualan_dtl
			( idurut, docno, docnoso, docnosj, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, docnoso, docnosj, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.penjualan_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.penjualan
            (
                idurut, docno, cabang, docdate, pemohon, 
                kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
                kdcustomer,nmcustomer, alamatcustomer, jthtempo,
                isopenprice, kdsalesman, gradecustomer,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, 
            kdcustomerdeliv, nmcustomerdeliv, alamatcustomerdeliv, carabayar,
            kdcustomer,nmcustomer, alamatcustomer, jthtempo,
            isopenprice, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.penjualan 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_penjualan()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_penjualan()
-- Trigger: tr_penjualan

-- DROP TRIGGER IF EXISTS tr_penjualan ON sc_trx.penjualan;

CREATE OR REPLACE TRIGGER tr_penjualan
    AFTER UPDATE 
    ON sc_trx.penjualan
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_penjualan();






-- ALTER TABLE sc_tmp.penjualan_dtl
-- ADD COLUMN uniqueid VARCHAR(64)

-- ALTER TABLE sc_trx.penjualan_dtl
-- ADD COLUMN uniqueid VARCHAR(64)



-- Tambahkan kolom di sc_trx.penjualan_dtl
ALTER TABLE sc_trx.penjualan_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtyretur numeric(18,2) DEFAULT 0;

-- Tambahkan kolom di sc_tmp.penjualan_dtl
ALTER TABLE sc_tmp.penjualan_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtyretur numeric(18,2) DEFAULT 0;



-- =========== TAMBAHAN 24/8/26 ====================
-- docdate
ALTER TABLE sc_trx.penjualan
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.penjualan
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;


-- printcount
ALTER TABLE sc_tmp.penjualan
ADD COLUMN printcount integer
ALTER TABLE sc_trx.penjualan
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================


ALTER TABLE sc_tmp.penjualan_dtl 
    ALTER COLUMN docnoso DROP NOT NULL;

ALTER TABLE sc_trx.penjualan_dtl 
    ALTER COLUMN docnoso DROP NOT NULL;