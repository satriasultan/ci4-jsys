
-- JALANKAN INI DULU
DROP TABLE IF EXISTS sc_tmp.voidpo;
DROP TABLE IF EXISTS sc_trx.voidpo;

DROP TABLE IF EXISTS sc_tmp.voidpo_dtl;
DROP TABLE IF EXISTS sc_trx.voidpo_dtl;




CREATE TABLE IF NOT EXISTS sc_tmp.voidpo
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
    CONSTRAINT pk_tmp_voidpo PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.voidpo
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.voidpo
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
    CONSTRAINT pk_trx_voidpo PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.voidpo
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.voidpo_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    -- docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopo CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    -- qtybonus NUMERIC(18,2),
    harga NUMERIC(18,2),
    -- multidisc NUMERIC(18,2),
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

ALTER TABLE IF EXISTS sc_tmp.voidpo_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.voidpo_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    -- docnopp CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docnopo CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    -- qtybonus NUMERIC(18,2),
    harga NUMERIC(18,2),
    -- multidisc NUMERIC(18,2),
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

ALTER TABLE IF EXISTS sc_trx.voidpo_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_voidpo_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_voidpo_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_voidpo_finalize()
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
                FROM sc_trx.voidpo
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
        INSERT INTO sc_trx.voidpo (
            idurut, docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total, syarat,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.voidpo
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.voidpo_dtl (
            idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, docnopo, idbarang, capexno, uniqueid,  nmbarang, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.voidpo_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        UPDATE sc_trx.po_dtl ppd
        SET qtyvoid = COALESCE(ppd.qtyvoid, 0) + pod.qty_used
            -- updateby = v_inputby,
            -- updatedate = CURRENT_TIMESTAMP
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.voidpo_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;


        -- ===============================
        -- UPDATE STATUS PO_DTL -> VP
        -- ===============================
        UPDATE sc_trx.po_dtl p
        SET status = CASE 
            WHEN p.qty = COALESCE(p.qtyvoid, 0) THEN 'VP'
            ELSE 'F'
        END
        FROM sc_tmp.voidpo_dtl t
        WHERE rtrim(t.docno) = rtrim(OLD.docno)
        AND t.inputby = v_inputby
        AND p.uniqueid = t.uniqueid;

        

        -- ===============================
        -- UPDATE STATUS PO HEADER MENJADI VP 
        -- JIKA SEMUA DETAILNYA QTY DAN QTYVOID SAMA SEMUA
        -- ===============================
        UPDATE sc_trx.po po
            SET status = 'VP'
            WHERE po.docno IN (
                SELECT DISTINCT docno 
                FROM sc_tmp.voidpo_dtl 
                WHERE docno = OLD.docno
                AND inputby = v_inputby
            )
            AND NOT EXISTS (
                SELECT 1 
                FROM sc_trx.po_dtl pod
                WHERE pod.docno = po.docno
                AND pod.qty != COALESCE(pod.qtyvoid, 0)
            );

        PERFORM sc_trx.fn_recalculate_po_header(v_docno);

        -- ===============================
        -- LOG: INSERT HEADER VOID PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.4',              -- kode menu untuk VOID PO
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.voidpo
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.voidpo_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        
        -- ===============================
        -- STEP 1: REVERT QTYVOID (KURANGI DENGAN DATA LAMA)
        -- ===============================
        UPDATE sc_trx.po_dtl pod
        SET qtyvoid = COALESCE(pod.qtyvoid, 0) - pod_lama.qty_void_lama
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_void_lama
            FROM sc_trx.voidpo_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = NEW.inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod_lama
        WHERE pod.uniqueid = pod_lama.uniqueid;

        DELETE FROM sc_trx.voidpo WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.voidpo_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.voidpo_dtl
        (idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang, unit, qty, 
        harga, nilai, descriptionpo, descriptionpp,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, docnopo, idbarang, capexno, uniqueid,  nmbarang, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.voidpo_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);
        

        INSERT INTO sc_trx.voidpo
        (idurut, docno, cabang, docdate, pemohon, kdsupplier,
        nmsupplier, alamatsupplier, jthtempo,
        idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total, syarat,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total, syarat,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.voidpo
        WHERE rtrim(docno) = rtrim(NEW.docno);


        -- ===============================
        -- STEP 2: TAMBAH QTYVOID DENGAN DATA BARU
        -- ===============================
        UPDATE sc_trx.po_dtl ppd
        SET qtyvoid = COALESCE(ppd.qtyvoid, 0) + pod.qty_used
        FROM (
            SELECT 
                uniqueid,
                SUM(qty) as qty_used
            FROM sc_tmp.voidpo_dtl
            WHERE rtrim(docno) = rtrim(NEW.docno)
                AND inputby = v_inputby
                AND uniqueid IS NOT NULL
                AND uniqueid <> ''
            GROUP BY uniqueid
        ) pod
        WHERE ppd.uniqueid = pod.uniqueid;


        -- ===============================
        -- UPDATE STATUS PO_DTL -> VP
        -- ===============================
        UPDATE sc_trx.po_dtl p
        SET status = CASE 
            WHEN p.qty = COALESCE(p.qtyvoid, 0) THEN 'VP'
            ELSE 'F'
        END
        FROM sc_tmp.voidpo_dtl t
        WHERE rtrim(t.docno) = rtrim(NEW.docno)
        AND t.inputby = v_inputby
        AND p.uniqueid = t.uniqueid;

        

        -- ===============================
        -- UPDATE STATUS PO HEADER MENJADI VP 
        -- JIKA SEMUA DETAILNYA SUDAH QTY DAN QTYVOID PASTI SAMA
        -- ===============================
        UPDATE sc_trx.po po
            SET status = 'VP'
            WHERE po.docno IN (
                SELECT DISTINCT docno 
                FROM sc_tmp.voidpo_dtl 
                WHERE docno = OLD.docno
                AND inputby = v_inputby
            )
            AND NOT EXISTS (
                SELECT 1 
                FROM sc_trx.po_dtl pod
                WHERE pod.docno = po.docno
                AND pod.qty != COALESCE(pod.qtyvoid, 0)
            );

        PERFORM sc_trx.fn_recalculate_po_header(NEW.docnotmp);

        -- ===============================
        -- LOG: INSERT HEADER VOID PO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.4',              -- kode menu untuk VOID PO
            'U',                    -- action: UPDATE (1 huruf)
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.voidpo WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.voidpo_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.voidpo SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.voidpo SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.voidpo WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.voidpo_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_voidpo_finalize
    AFTER UPDATE ON sc_tmp.voidpo
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_voidpo_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_voidpo();

CREATE OR REPLACE FUNCTION sc_trx.tr_voidpo()
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
        -- VOID PO DIBATALKAN (F -> C) - REVERT QTYVOID
        -- ===============================
        IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN
            
            -- ===============================
            -- REVERT QTYVOID DI PO_DTL
            -- ===============================
            UPDATE sc_trx.po_dtl ppd
                SET qtyvoid = COALESCE(ppd.qtyvoid, 0) - pod.qty_used
                FROM (
                    SELECT 
                        uniqueid,
                        SUM(qty) as qty_used
                    FROM sc_trx.voidpo_dtl
                    WHERE rtrim(docno) = rtrim(NEW.docno)
                        AND uniqueid IS NOT NULL
                        AND uniqueid <> ''
                    GROUP BY uniqueid
                ) pod
                WHERE ppd.uniqueid = pod.uniqueid;

            -- ===============================
            -- UPDATE STATUS PO_DTL BERDASARKAN QTY
            -- ===============================
            UPDATE sc_trx.po_dtl pod
            SET status = CASE 
                WHEN pod.qty = COALESCE(pod.qtyvoid, 0) THEN 'VP'
                ELSE 'F'
            END
            FROM sc_trx.voidpo_dtl vd
            WHERE pod.uniqueid = vd.uniqueid
                AND rtrim(vd.docno) = rtrim(NEW.docno);
            
            -- ===============================
            -- UPDATE STATUS PO HEADER BERDASARKAN QTY
            -- ===============================
            UPDATE sc_trx.po po
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.po_dtl pod
                    WHERE rtrim(pod.docno) = rtrim(po.docno)
                    AND pod.qty != COALESCE(pod.qtyvoid, 0)
                ) THEN 'P'
                ELSE 'VP'
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.voidpo_dtl vd
                WHERE rtrim(vd.docno) = rtrim(NEW.docno)
                AND vd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.po_dtl 
                    WHERE rtrim(docno) = rtrim(po.docno)
                )
            );

            PERFORM sc_trx.fn_recalculate_po_header(NEW.docno);
            
            -- ===============================
            -- LOG: CANCEL HEADER VOID PO
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',                  -- kode module dari menuprg
                'I.P.A.4',              -- kode menu untuk VOID PO
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );
            
        END IF;


		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.voidpo_dtl
			( idurut, docno, docnopo, idbarang, capexno, uniqueid, nmbarang, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, docnopo, idbarang, capexno, uniqueid, nmbarang, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.voidpo_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.voidpo
            (
                idurut, docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, jthtempo,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total, syarat,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total, syarat,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.voidpo 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_voidpo()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_voidpo()
-- Trigger: tr_voidpo

-- DROP TRIGGER IF EXISTS tr_voidpo ON sc_trx.voidpo;

CREATE OR REPLACE TRIGGER tr_voidpo
    AFTER UPDATE 
    ON sc_trx.voidpo
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_voidpo();






-- ALTER TABLE sc_tmp.voidpo_dtl
-- ADD COLUMN uniqueid VARCHAR(64)

-- ALTER TABLE sc_trx.voidpo_dtl
-- ADD COLUMN uniqueid VARCHAR(64)



-- Tambahkan kolom di sc_trx.voidpo_dtl
ALTER TABLE sc_trx.voidpo_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2);


-- Tambahkan kolom di sc_tmp.voidpo_dtl
ALTER TABLE sc_tmp.voidpo_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2);





-- =========== TAMBAHAN 24/8/26 ====================
ALTER TABLE sc_tmp.voidpo_dtl
ADD COLUMN capexno character(30)

ALTER TABLE sc_trx.voidpo_dtl
ADD COLUMN capexno character(30)



-- docdate
ALTER TABLE sc_trx.voidpo
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.voidpo
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;


-- printcount
ALTER TABLE sc_tmp.voidpo
ADD COLUMN printcount integer
ALTER TABLE sc_trx.voidpo
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================






-- Fungsi untuk recalculate header PO
CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_po_header(
    p_docno TEXT
)
RETURNS VOID AS $$
DECLARE
    v_idtax TEXT;
    v_dpp NUMERIC(18,2);
    v_jumlahpajak NUMERIC(18,2);
    v_total NUMERIC(18,2);
    v_tax_percent NUMERIC(5,2);
BEGIN
    -- Ambil idtax dari header
    SELECT idtax INTO v_idtax
    FROM sc_trx.po
    WHERE rtrim(docno) = rtrim(p_docno);
    
    -- Hitung total DPP dari detail
    SELECT COALESCE(SUM(nilai), 0) INTO v_dpp
    FROM sc_trx.po_dtl
    WHERE rtrim(docno) = rtrim(p_docno);
    
    -- Hitung pajak
    v_jumlahpajak := 0;
    IF v_idtax IS NOT NULL AND TRIM(v_idtax) != 'NON' AND v_dpp > 0 THEN
        FOR v_tax_percent IN 
            SELECT percentation 
            FROM sc_mst.tax_dtl 
            WHERE idtax = v_idtax
        LOOP
            v_jumlahpajak := v_jumlahpajak + (v_dpp * v_tax_percent / 100);
        END LOOP;
    END IF;
    
    -- Hitung total
    v_total := v_dpp + v_jumlahpajak;
    
    -- Update header
    UPDATE sc_trx.po
    SET 
        dpp = v_dpp,
        jumlahpajak = v_jumlahpajak,
        total = v_total,
        updateby = CURRENT_USER,
        updatedate = CURRENT_TIMESTAMP
    WHERE rtrim(docno) = rtrim(p_docno);
    
END;
$$ LANGUAGE plpgsql;