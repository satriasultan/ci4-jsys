
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.suratjalan_dtl
DROP TABLE IF EXISTS sc_trx.suratjalan_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.suratjalan
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate DATE,
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
    printcount integer,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_suratjalan PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.suratjalan
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.suratjalan
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate DATE,
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
    printcount integer,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_suratjalan PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.suratjalan
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.suratjalan_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnodo CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
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
    idtax character(20),
    currcode character(3),
    kurs numeric(18,2),
    nilaikonversi numeric(18,2),
    nilaipajak numeric(18,2),
    qtypenjualan numeric(18,2) DEFAULT 0,
    bomdesc TEXT COLLATE pg_catalog."default",
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.suratjalan_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.suratjalan_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnodo CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
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
    idtax character(20),
    currcode character(3),
    kurs numeric(18,2),
    nilaikonversi numeric(18,2),
    nilaipajak numeric(18,2),
    qtypenjualan numeric(18,2) DEFAULT 0,
    bomdesc TEXT COLLATE pg_catalog."default",
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.suratjalan_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_suratjalan_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_suratjalan_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_suratjalan_finalize()
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
                FROM sc_trx.suratjalan
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
        INSERT INTO sc_trx.suratjalan (
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
        FROM sc_tmp.suratjalan
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.suratjalan_dtl (
            idurut, docno, docnodo, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, docnodo, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.suratjalan_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        UPDATE sc_trx.deliveryorder_dtl ppd
        SET qtysj = COALESCE(ppd.qtysj, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.suratjalan_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS SO_DTL BERDASARKAN QTYSJ
        -- ===============================
        UPDATE sc_trx.deliveryorder_dtl sod
        SET status = CASE 
            WHEN sod.qty = COALESCE(sod.qtysj, 0) THEN 'SJ'
            ELSE 'F'
        END
        FROM sc_tmp.suratjalan_dtl t
        WHERE rtrim(t.docno) = rtrim(OLD.docno)
        AND t.inputby = v_inputby
        AND sod.uniqueid = t.uniqueid;
        
        -- ===============================
        -- UPDATE STATUS SJ HEADER MENJADI 'SJ' 
        -- JIKA ADA DETAIL YANG QTYSJ > 0
        -- ===============================
        UPDATE sc_trx.deliveryorder so
        SET status = 'SJ'
        WHERE so.docno IN (
            SELECT DISTINCT t.docnodo
            FROM sc_tmp.suratjalan_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND t.docnodo IS NOT NULL
            AND t.docnodo <> ''
        );

        -- ===============================
        -- LOG: INSERT HEADER SJ
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.S',                  -- kode module dari menuprg
            'I.S.B.3',              -- kode menu untuk SJ
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.suratjalan
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.suratjalan_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- SJCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        -- ===============================
        -- STEP 1: REVERT QTYSJ (KURANGI DENGAN DATA LAMA)
        -- ===============================
        UPDATE sc_trx.deliveryorder_dtl sod
        SET qtysj = COALESCE(sod.qtysj, 0) - pjo_lama.qty_pjo_lama
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_pjo_lama
            FROM sc_trx.suratjalan_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pjo_lama
        WHERE sod.uniqueid = pjo_lama.uniqueid;

        DELETE FROM sc_trx.suratjalan WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.suratjalan_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.suratjalan_dtl
        (idurut, docno, docnodo, idbarang, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, unit, qty, 
        harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, kurs, idtax, currcode,
        bomdesc, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, docnodo, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.suratjalan_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.suratjalan
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
        FROM sc_tmp.suratjalan
        WHERE rtrim(docno) = rtrim(NEW.docno);


        UPDATE sc_trx.deliveryorder_dtl ppd
        SET qtysj = COALESCE(ppd.qtysj, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.suratjalan_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;


        -- ===============================
        -- UPDATE STATUS SO_DTL BERDASARKAN QTYSJ
        -- ===============================
        UPDATE sc_trx.deliveryorder_dtl sod
        SET status = CASE 
            WHEN sod.qty = COALESCE(sod.qtysj, 0) THEN 'SJ'
            ELSE 'F'
        END
        FROM sc_tmp.suratjalan_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
        AND t.inputby = v_inputby
        AND sod.uniqueid = t.uniqueid;


        -- ===============================
        -- UPDATE STATUS SO HEADER MENJADI 'SJ' 
        -- JIKA ADA DETAIL YANG QTYSJ > 0
        -- ===============================
        UPDATE sc_trx.deliveryorder so
        SET status = 'SJ'
        WHERE so.docno IN (
            SELECT DISTINCT t.docnodo
            FROM sc_tmp.suratjalan_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.docnodo IS NOT NULL
            AND t.docnodo <> ''
        );
        

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.S',                  -- kode module dari menuprg
            'I.S.B.3',              -- kode menu untuk SJ
            'U',                    -- action: UPDATE (1 huruf)
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.suratjalan WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.suratjalan_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.suratjalan SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.suratjalan SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.suratjalan WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.suratjalan_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_suratjalan_finalize
    AFTER UPDATE ON sc_tmp.suratjalan
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_suratjalan_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_suratjalan();

CREATE OR REPLACE FUNCTION sc_trx.tr_suratjalan()
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
            -- REVERT QTYSJ DI SO_DTL
            -- ===============================
            UPDATE sc_trx.deliveryorder_dtl sod
            SET qtysj = COALESCE(sod.qtysj, 0) - pod.qty_used
            FROM (
                SELECT 
                    uniqueid,
                    SUM(qty) as qty_used
                FROM sc_trx.suratjalan_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL
                    AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE sod.uniqueid = pod.uniqueid;


            -- ===============================
            -- UPDATE STATUS SJ HEADER 
            -- 'SJ' JIKA MASIH ADA QTYSJ, 'P' JIKA TIDAK ADA QTYSJ
            -- ===============================
            UPDATE sc_trx.deliveryorder so
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.deliveryorder_dtl sod
                    WHERE rtrim(sod.docno) = rtrim(so.docno)
                    AND COALESCE(sod.qtysj, 0) > 0
                ) THEN 'SJ'   -- masih ada qtysj
                ELSE 'P'      -- tidak ada qtysj
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.suratjalan_dtl pd
                WHERE rtrim(pd.docno) = rtrim(NEW.docno)
                AND pd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.deliveryorder_dtl 
                    WHERE rtrim(docno) = rtrim(so.docno)
                )
            );

            -- ===============================
            -- UPDATE STATUS SO_DTL BERDASARKAN QTYSJ
            -- ===============================
            UPDATE sc_trx.deliveryorder_dtl sod
            SET status = CASE 
                WHEN sod.qty = COALESCE(sod.qtysj, 0) THEN 'SJ'
                ELSE 'F'
            END
            FROM sc_trx.suratjalan_dtl pd
            WHERE sod.uniqueid = pd.uniqueid
                AND rtrim(pd.docno) = rtrim(NEW.docno);


            -- ===============================
            -- LOG: INSERT HEADER SJ
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.S',                  -- kode module dari menuprg
                'I.S.B.3',              -- kode menu untuk SJ
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );

        END IF;

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.suratjalan_dtl
			( idurut, docno, docnodo, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, docnodo, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.suratjalan_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.suratjalan
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
			FROM sc_trx.suratjalan 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_suratjalan()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_suratjalan()
-- Trigger: tr_suratjalan

-- DROP TRIGGER IF EXISTS tr_suratjalan ON sc_trx.suratjalan;

CREATE OR REPLACE TRIGGER tr_suratjalan
    AFTER UPDATE 
    ON sc_trx.suratjalan
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_suratjalan();






