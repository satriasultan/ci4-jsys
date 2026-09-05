
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.returbeli_dtl
DROP TABLE IF EXISTS sc_trx.returbeli_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.returbeli
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
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    complain character(20),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_returbeli PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.returbeli
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.returbeli
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
    -- biayavol numeric(18,2),
    -- biayavol2 numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    currcode character(3),
    kurs numeric(18,2),
    -- nofaktur character(30),
    -- nosj character(30),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    complain character(20),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_returbeli PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.returbeli
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.returbeli_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    -- docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnolpb CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    idgudang CHARACTER(30) COLLATE pg_catalog."default",
    idspec CHARACTER(30) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
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

ALTER TABLE IF EXISTS sc_tmp.returbeli_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.returbeli_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    -- docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnolpb CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    idgudang CHARACTER(30) COLLATE pg_catalog."default",
    idspec CHARACTER(30) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
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

ALTER TABLE IF EXISTS sc_trx.returbeli_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_returbeli_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_returbeli_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_returbeli_finalize()
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
                FROM sc_trx.returbeli
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
        INSERT INTO sc_trx.returbeli (
            idurut, docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo
            ,idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, complain, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo
            ,idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, complain, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.returbeli
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.returbeli_dtl (
            idurut, docno, docnolpb, idbarang, capexno, uniqueid,  nmbarang,
            idgudang, idspec, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, docnolpb, idbarang, capexno, uniqueid,  nmbarang,
            idgudang, idspec, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.returbeli_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        UPDATE sc_trx.lpb_dtl ppd
        SET qtyretur = COALESCE(ppd.qtyretur, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.lpb_dtl
            WHERE rtrim(docno) = rtrim(OLD.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS LPB_DTL -> RTR
        -- ===============================
        UPDATE sc_trx.lpb_dtl ppd
        SET status = CASE 
            WHEN ppd.qty = COALESCE(ppd.qtyretur, 0) THEN 'RTR'
            ELSE 'F'
        END
        FROM sc_tmp.returbeli_dtl t
        WHERE rtrim(t.docno) = rtrim(OLD.docno)
        AND t.inputby = v_inputby
        AND ppd.uniqueid = t.uniqueid;

        -- ===============================
        -- UPDATE STATUS LPB HEADER -> RTR
        -- JIKA SEMUA DETAILNYA QTY DAN QTYRETUR SAMA SEMUA
        -- ===============================
        UPDATE sc_trx.lpb lpb
        SET status = 'RTR'
        WHERE EXISTS (
            SELECT 1 
            FROM sc_tmp.returbeli_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND t.uniqueid IN (
                SELECT uniqueid 
                FROM sc_trx.lpb_dtl 
                WHERE rtrim(docno) = rtrim(lpb.docno)
            )
        )
        AND NOT EXISTS (
            SELECT 1 
            FROM sc_trx.lpb_dtl ppd
            WHERE rtrim(ppd.docno) = rtrim(lpb.docno)
            AND ppd.qty != COALESCE(ppd.qtyretur, 0)
        );

        -- ===============================
        -- LOG: INSERT HEADER RETUR BELI
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',
            'I.P.A.7',
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );


        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.returbeli
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.returbeli_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        UPDATE sc_trx.lpb_dtl ppd
            SET qtyretur = COALESCE(ppd.qtyretur, 0) - pod_lama.qty_retur_lama
            FROM (
                SELECT uniqueid, SUM(qty) as qty_retur_lama
                FROM sc_trx.returbeli_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND inputby = NEW.inputby
                    AND uniqueid IS NOT NULL AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod_lama
            WHERE ppd.uniqueid = pod_lama.uniqueid;

        DELETE FROM sc_trx.returbeli WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.returbeli_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.returbeli_dtl
        (idurut, docno, docnolpb, idbarang, capexno, uniqueid,  nmbarang,
        idgudang, idspec, unit, qty, 
        harga, nilai, descriptionpo, descriptionpp,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, docnolpb, idbarang, capexno, uniqueid,  nmbarang,
            idgudang, idspec, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.returbeli_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.returbeli
        (idurut, docno, cabang, docdate, pemohon, kdsupplier,
        nmsupplier, alamatsupplier, jthtempo
        ,idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, complain, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo
            ,idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, complain, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.returbeli
        WHERE rtrim(docno) = rtrim(NEW.docno);

        UPDATE sc_trx.lpb_dtl ppd
        SET qtyretur = COALESCE(ppd.qtyretur, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.lpb_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;

        -- ===============================
        -- UPDATE STATUS LPB_DTL -> RTR
        -- ===============================
        UPDATE sc_trx.lpb_dtl ppd
        SET status = CASE 
            WHEN ppd.qty = COALESCE(ppd.qtyretur, 0) THEN 'RTR'
            ELSE 'F'
        END
        FROM sc_tmp.returbeli_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
        AND t.inputby = v_inputby
        AND ppd.uniqueid = t.uniqueid;

        -- ===============================
        -- UPDATE STATUS LPB HEADER -> RTR
        -- ===============================
        UPDATE sc_trx.lpb lpb
        SET status = 'RTR'
        WHERE EXISTS (
            SELECT 1 
            FROM sc_tmp.returbeli_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.inputby = v_inputby
            AND t.uniqueid IN (
                SELECT uniqueid 
                FROM sc_trx.lpb_dtl 
                WHERE rtrim(docno) = rtrim(lpb.docno)
            )
        )
        AND NOT EXISTS (
            SELECT 1 
            FROM sc_trx.lpb_dtl ppd
            WHERE rtrim(ppd.docno) = rtrim(lpb.docno)
            AND ppd.qty != COALESCE(ppd.qtyretur, 0)
        );

        -- ===============================
        -- LOG: UPDATE HEADER RETUR BELI
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',
            'I.P.A.7',
            'U',
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.returbeli WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.returbeli_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.returbeli SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.returbeli SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.returbeli WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.returbeli_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_returbeli_finalize
    AFTER UPDATE ON sc_tmp.returbeli
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_returbeli_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_returbeli();

CREATE OR REPLACE FUNCTION sc_trx.tr_returbeli()
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
        -- AMBIL IP
        -- ===============================
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;
        v_client_ip := sc_log.fn_get_user_ip(v_inputby);

        PERFORM pg_advisory_xact_lock(hashtext(NEW.docno));

        -- ===============================
        -- RETUR BELI DIBATALKAN (F -> C)
        -- ===============================
        IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN
            
            -- ===============================
            -- REVERT QTYRETUR DI LPB_DTL
            -- ===============================
            UPDATE sc_trx.lpb_dtl ppd
            SET qtyretur = COALESCE(ppd.qtyretur, 0) - pod.qty_used
            FROM (
                SELECT uniqueid, SUM(qty) as qty_used
                FROM sc_trx.returbeli_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE ppd.uniqueid = pod.uniqueid;

            -- ===============================
            -- UPDATE STATUS LPB_DTL
            -- ===============================
            UPDATE sc_trx.lpb_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtyretur, 0) THEN 'RTR'
                ELSE 'F'
            END
            FROM sc_trx.returbeli_dtl vd
            WHERE ppd.uniqueid = vd.uniqueid
                AND rtrim(vd.docno) = rtrim(NEW.docno);

            -- ===============================
            -- UPDATE STATUS LPB HEADER
            -- ===============================
            UPDATE sc_trx.lpb lpb
                SET status = CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM sc_trx.lpb_dtl ppd
                        WHERE rtrim(ppd.docno) = rtrim(lpb.docno)
                        AND ppd.qty != COALESCE(ppd.qtyretur, 0)  -- ✅ MASIH ADA SISA
                    ) THEN 'F'  -- ✅ MASIH STATUS F
                    ELSE 'RTR'  -- ✅ SEMUA SUDAH DI-RETUR
                END
                WHERE EXISTS (
                    SELECT 1 
                    FROM sc_trx.returbeli_dtl vd
                    WHERE rtrim(vd.docno) = rtrim(NEW.docno)
                    AND vd.uniqueid IN (
                        SELECT uniqueid 
                        FROM sc_trx.lpb_dtl 
                        WHERE rtrim(docno) = rtrim(lpb.docno)
                    )
                );

            -- ===============================
            -- LOG: CANCEL RETUR BELI
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',
                'I.P.A.7',
                'C',
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );
            
        END IF;

        
		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.returbeli_dtl
			( idurut, docno, docnolpb, idbarang, capexno, uniqueid, nmbarang,
            idgudang, idspec, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, docnolpb, idbarang, capexno, uniqueid, nmbarang,
            idgudang, idspec, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.returbeli_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.returbeli
            (
                idurut, docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, jthtempo,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, complain, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo
            ,idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, complain, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.returbeli 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_returbeli()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_returbeli()
-- Trigger: tr_returbeli

-- DROP TRIGGER IF EXISTS tr_returbeli ON sc_trx.returbeli;

CREATE OR REPLACE TRIGGER tr_returbeli
    AFTER UPDATE 
    ON sc_trx.returbeli
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_returbeli();






-- ALTER TABLE sc_tmp.returbeli_dtl
-- ADD COLUMN uniqueid VARCHAR(64)

-- ALTER TABLE sc_trx.returbeli_dtl
-- ADD COLUMN uniqueid VARCHAR(64)


-- Tambahkan kolom di sc_trx.returbeli_dtl
ALTER TABLE sc_trx.returbeli_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2);


-- Tambahkan kolom di sc_tmp.returbeli_dtl
ALTER TABLE sc_tmp.returbeli_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2);





-- =========== TAMBAHAN 24/8/26 ====================
ALTER TABLE sc_tmp.returbeli_dtl
ADD COLUMN capexno character(30)

ALTER TABLE sc_trx.returbeli_dtl
ADD COLUMN capexno character(30)



-- docdate
ALTER TABLE sc_trx.returbeli
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.returbeli
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;



-- printcount
ALTER TABLE sc_tmp.returbeli
ADD COLUMN printcount integer
ALTER TABLE sc_trx.returbeli
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================