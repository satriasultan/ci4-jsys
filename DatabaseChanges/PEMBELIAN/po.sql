
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.po_dtl
DROP TABLE IF EXISTS sc_trx.po_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.po
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_po PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.po
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.po
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdsupplier character(30) COLLATE pg_catalog."default",
    nmsupplier character(250) COLLATE pg_catalog."default",
    alamatsupplier TEXT,
    alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_po PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.po
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.po_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    qtybonus NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    descriptionpo TEXT COLLATE pg_catalog."default",
    descriptionpp TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.po_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.po_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    qtybonus NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    descriptionpo TEXT COLLATE pg_catalog."default",
    descriptionpp TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.po_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_po_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_po_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_po_finalize()
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
                FROM sc_trx.po
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
        INSERT INTO sc_trx.po (
            idurut, docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, docnoumb, 
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, docnoumb, 
            jumlahpajak, total, syarat,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.po
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.po_dtl (
            idurut, docno, docnopp, idbarang, capexno, uniqueid,  nmbarang, unit, qty, qtybonus, 
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, docnopp, idbarang, capexno, uniqueid,  nmbarang, unit, qty, qtybonus, 
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.po_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.po_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS PP_DTL BERDASARKAN QTYPO
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET status = CASE 
            WHEN ppd.qty = COALESCE(ppd.qtypo, 0) THEN 'PO'
            ELSE 'F'
        END
        FROM sc_tmp.po_dtl t
        WHERE rtrim(t.docno) = rtrim(OLD.docno)
        AND t.inputby = v_inputby
        AND ppd.uniqueid = t.uniqueid;
        
        -- ===============================
        -- UPDATE STATUS PP HEADER MENJADI 'PO' 
        -- JIKA ADA DETAIL YANG QTYPO > 0
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = 'PO'
        WHERE pp.docno IN (
            SELECT DISTINCT t.docnopp
            FROM sc_tmp.po_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND t.docnopp IS NOT NULL
            AND t.docnopp <> ''
        );


        -- ===============================
        -- LOG: INSERT HEADER PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.3',              -- kode menu untuk PO
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.po
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.po_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        -- ===============================
        -- STEP 1: REVERT QTYPO (KURANGI DENGAN DATA LAMA)
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) - pod_lama.qty_po_lama
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_po_lama
            FROM sc_trx.po_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod_lama
        WHERE ppd.uniqueid = pod_lama.uniqueid;

        DELETE FROM sc_trx.po WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.po_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.po_dtl
        (idurut, docno, docnopp, idbarang, capexno, uniqueid,  nmbarang, unit, qty, qtybonus, 
        harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
        descriptionpo, descriptionpp,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, docnopp, idbarang, capexno, uniqueid,  nmbarang, unit, qty, qtybonus, 
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.po_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.po
        (idurut, docno, cabang, docdate, senddate, pemohon, kdsupplier,
        nmsupplier, alamatsupplier, alamatkirim, jthtempo,
        idtax,isinclusive, currcode, kurs, dpp, docnoumb, 
        jumlahpajak, total, syarat,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, docnoumb, 
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.po
        WHERE rtrim(docno) = rtrim(NEW.docno);

        UPDATE sc_trx.pp_dtl ppd
        SET qtypo = COALESCE(ppd.qtypo, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.po_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;


        -- ===============================
        -- UPDATE STATUS PP_DTL BERDASARKAN QTYPO
        -- ===============================
        UPDATE sc_trx.pp_dtl ppd
        SET status = CASE 
            WHEN ppd.qty = COALESCE(ppd.qtypo, 0) THEN 'PO'
            ELSE 'F'
        END
        FROM sc_tmp.po_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
        AND t.inputby = v_inputby
        AND ppd.uniqueid = t.uniqueid;


        -- ===============================
        -- UPDATE STATUS PP HEADER MENJADI 'PO' 
        -- JIKA ADA DETAIL YANG QTYPO > 0
        -- ===============================
        UPDATE sc_trx.pp pp
        SET status = 'PO'
        WHERE pp.docno IN (
            SELECT DISTINCT t.docnopp
            FROM sc_tmp.po_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.docnopp IS NOT NULL
            AND t.docnopp <> ''
        );

        

        -- ===============================
        -- LOG: UPDATE HEADER PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.3',              -- kode menu untuk PO
            'U',                    -- action: UPDATE (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );

        DELETE FROM sc_tmp.po WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.po_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.po SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.po SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.po WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.po_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_po_finalize
    AFTER UPDATE ON sc_tmp.po
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_po_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_po();

CREATE OR REPLACE FUNCTION sc_trx.tr_po()
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
        -- PO DIBATALKAN (F -> C) - REVERT QTYPO
        -- ===============================
        IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN
            
            -- ===============================
            -- REVERT QTYPO DI PP_DTL
            -- ===============================
            UPDATE sc_trx.pp_dtl ppd
            SET qtypo = COALESCE(ppd.qtypo, 0) - pod.qty_used
            FROM (
                SELECT 
                    uniqueid,
                    SUM(qty) as qty_used
                FROM sc_trx.po_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL
                    AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE ppd.uniqueid = pod.uniqueid;
            
            -- ===============================
            -- UPDATE STATUS PP HEADER 
            -- 'PO' JIKA MASIH ADA QTYPO, 'P' JIKA TIDAK ADA QTYPO
            -- ===============================
            UPDATE sc_trx.pp pp
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.pp_dtl ppd
                    WHERE rtrim(ppd.docno) = rtrim(pp.docno)
                    AND COALESCE(ppd.qtypo, 0) > 0
                ) THEN 'PO'   -- masih ada qtypo
                ELSE 'P'      -- tidak ada qtypo
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.po_dtl pd
                WHERE rtrim(pd.docno) = rtrim(NEW.docno)
                AND pd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.pp_dtl 
                    WHERE rtrim(docno) = rtrim(pp.docno)
                )
            );

            -- ===============================
            -- UPDATE STATUS PP_DTL BERDASARKAN QTYPO
            -- ===============================
            UPDATE sc_trx.pp_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtypo, 0) THEN 'PO'
                ELSE 'F'
            END
            FROM sc_trx.po_dtl pd
            WHERE ppd.uniqueid = pd.uniqueid
                AND rtrim(pd.docno) = rtrim(NEW.docno);
            
            -- ===============================
            -- LOG: INSERT HEADER PO
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',                  -- kode module dari menuprg
                'I.P.A.3',              -- kode menu untuk PO
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );
            
        END IF;

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.po_dtl
			( idurut, docno, docnopp, idbarang, capexno, uniqueid, nmbarang, unit, qty, qtybonus, 
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, docnopp, idbarang, capexno, uniqueid, nmbarang, unit, qty, qtybonus, 
            harga, multidisc, nilai, nilaipajak, nilaikonversi, currcode, idtax, kurs,
            descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.po_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.po
            (
                idurut, docno, cabang, docdate, senddate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, alamatkirim, jthtempo,
                idtax,isinclusive, currcode, kurs, dpp, docnoumb, 
                jumlahpajak, total, syarat,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, senddate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, alamatkirim, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, docnoumb, 
            jumlahpajak, total, syarat,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.po 
			WHERE docno = NEW.docno;

            -- -- -- ===============================
            -- -- LOG: INSERT HEADER PO
            -- -- ===============================
            -- PERFORM sc_log.fn_log_transaction(
            --     NEW.docno,
            --     NULL,
            --     'I.P',                  -- kode module dari menuprg
            --     'I.P.A.3',              -- kode menu untuk PO
            --     'E',                    -- action: UPDATE (1 huruf)
            --     COALESCE(NEW.updateby, NEW.inputby),
            --     v_client_ip,
            --     COALESCE(NEW.updateby, NEW.inputby)
            -- );

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_po()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_po()
-- Trigger: tr_po

-- DROP TRIGGER IF EXISTS tr_po ON sc_trx.po;

CREATE OR REPLACE TRIGGER tr_po
    AFTER UPDATE 
    ON sc_trx.po
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_po();






ALTER TABLE sc_tmp.po_dtl
ADD COLUMN uniqueid VARCHAR(64)

ALTER TABLE sc_trx.po_dtl
ADD COLUMN uniqueid VARCHAR(64)





ALTER TABLE sc_tmp.po
ADD COLUMN docnoumb character(30)

ALTER TABLE sc_trx.po
ADD COLUMN docnoumb character(30)





-- Tambahkan kolom di sc_trx.po_dtl
ALTER TABLE sc_trx.po_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtylpb numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtyvoid numeric(18,2) DEFAULT 0;

-- Tambahkan kolom di sc_tmp.po_dtl
ALTER TABLE sc_tmp.po_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtylpb numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtyvoid numeric(18,2) DEFAULT 0;



-- =========== TAMBAHAN 24/8/26 ====================
ALTER TABLE sc_tmp.po_dtl
ADD COLUMN capexno character(30)

ALTER TABLE sc_trx.po_dtl
ADD COLUMN capexno character(30)



-- docdate
ALTER TABLE sc_trx.po
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.po
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;


-- senddate 
ALTER TABLE sc_trx.po
ALTER COLUMN senddate TYPE DATE
USING TRIM(senddate)::DATE;
ALTER TABLE sc_tmp.po
ALTER COLUMN senddate TYPE DATE
USING TRIM(senddate)::DATE;

-- printcount
ALTER TABLE sc_tmp.po
ADD COLUMN printcount integer
ALTER TABLE sc_trx.po
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================