--select * from sc_trx.transaction_dt

--select * from sc_trx.closeperiod
-- ONE-RUN TRANSACTION SCRIPT
BEGIN;

/* ============================================================
   REMOVE LEGACY UNPOST FUNCTION
   ============================================================ */

DROP FUNCTION IF EXISTS sc_trx.sp_unpost_by_doc(
    character varying,
    character varying
);


-- JALANKAN INI DULU
DROP TABLE IF EXISTS sc_trx.lpb_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.lpb CASCADE;
DROP TABLE IF EXISTS sc_tmp.lpb_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.lpb CASCADE;

CREATE TABLE sc_tmp.lpb
(
    idurut          SERIAL NOT NULL,
    docno           CHARACTER(30) NOT NULL,
    docdate         DATE,

    cabang          CHARACTER(30),
    pemohon         CHARACTER(100),

    kdsupplier      CHARACTER(30),
    nmsupplier      CHARACTER(250),
    alamatsupplier  TEXT,

    jthtempo        NUMERIC(18,2),

    biayavol        NUMERIC(18,2),
    biayavol2       NUMERIC(18,2),

    idtax           CHARACTER(20),
    isinclusive     CHARACTER(6),

    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nofaktur        CHARACTER(30),
    nosj            CHARACTER(30),

    dpp             NUMERIC(18,2),
    jumlahpajak     NUMERIC(18,2),
    total           NUMERIC(18,2),

    status          CHARACTER(6),
    keterangan      TEXT,

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    printby         VARCHAR(50),
    printdate       TIMESTAMP WITHOUT TIME ZONE,
    printcount      INTEGER,

    docnotmp        CHARACTER(30),

    -- sudah termasuk ALTER TABLE sebelumnya
    doctype         CHARACTER(10) DEFAULT 'GR',

    CONSTRAINT pk_tmp_lpb
        PRIMARY KEY (docno)
)
TABLESPACE pg_default;

ALTER TABLE sc_tmp.lpb
    OWNER TO postgres;


DROP TABLE IF EXISTS sc_trx.lpb CASCADE;

CREATE TABLE sc_trx.lpb
(
    idurut          SERIAL NOT NULL,
    docno           CHARACTER(30) NOT NULL,
    docdate         DATE,

    cabang          CHARACTER(30),
    pemohon         CHARACTER(100),

    kdsupplier      CHARACTER(30),
    nmsupplier      CHARACTER(250),
    alamatsupplier  TEXT,

    jthtempo        NUMERIC(18,2),

    biayavol        NUMERIC(18,2),
    biayavol2       NUMERIC(18,2),

    idtax           CHARACTER(20),
    isinclusive     CHARACTER(6),

    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nofaktur        CHARACTER(30),
    nosj            CHARACTER(30),

    dpp             NUMERIC(18,2),
    jumlahpajak     NUMERIC(18,2),
    total           NUMERIC(18,2),

    status          CHARACTER(6),
    keterangan      TEXT,

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    printby         VARCHAR(50),
    printdate       TIMESTAMP WITHOUT TIME ZONE,
    printcount      INTEGER,

    docnotmp        CHARACTER(30),

    -- sudah termasuk ALTER TABLE sebelumnya
    doctype         CHARACTER(10) DEFAULT 'GR',

    CONSTRAINT pk_trx_lpb
        PRIMARY KEY (docno)
)
TABLESPACE pg_default;

ALTER TABLE sc_trx.lpb
    OWNER TO postgres;

DROP TABLE IF EXISTS sc_tmp.lpb_dtl CASCADE;

CREATE TABLE sc_tmp.lpb_dtl
(
    idurut          SERIAL PRIMARY KEY,

    docno           CHARACTER(30) NOT NULL,
    docnopo         CHARACTER(30) NOT NULL,

    uniqueid        VARCHAR(64),

    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),

    unit            CHARACTER(20),

    qty             NUMERIC(18,2),
    qtybonus        NUMERIC(18,2),

    harga           NUMERIC(18,2),

    multidisc       NUMERIC(18,2),

    volitem         NUMERIC(18,2),

    biaya           NUMERIC(18,2),
    biaya2          NUMERIC(18,2),

    nilai           NUMERIC(18,2),

    descriptionpo   TEXT,
    descriptionpp   TEXT,

    status          CHARACTER(6),

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    docnotmp        CHARACTER(30),
    capexno         CHARACTER(30),

    -- =========================================
    -- KOLOM TAMBAHAN YANG SEBELUMNYA DITAMBAHKAN ALTER
    -- =========================================

    doctype         CHARACTER(10) DEFAULT 'GR',

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),

    qtyretur        NUMERIC(18,2) DEFAULT 0,
    idhistory_price CHAR(30),
    multidisctype   CHAR(30),
    totaldiscount   NUMERIC(18,2)

)
TABLESPACE pg_default;

ALTER TABLE sc_tmp.lpb_dtl
    OWNER TO postgres;



DROP TABLE IF EXISTS sc_trx.lpb_dtl CASCADE;

CREATE TABLE sc_trx.lpb_dtl
(
    idurut          SERIAL PRIMARY KEY,

    docno           CHARACTER(30) NOT NULL,
    docnopo         CHARACTER(30) NOT NULL,

    uniqueid        VARCHAR(64),

    idbarang        CHARACTER(20),
    nmbarang        CHARACTER(150),

    idprincipal     CHARACTER(20),
    idgudang        CHARACTER(30),
    idspec          CHARACTER(30),

    unit            CHARACTER(20),

    qty             NUMERIC(18,2),
    qtybonus        NUMERIC(18,2),

    harga           NUMERIC(18,2),

    multidisc       NUMERIC(18,2),

    volitem         NUMERIC(18,2),

    biaya           NUMERIC(18,2),
    biaya2          NUMERIC(18,2),

    nilai           NUMERIC(18,2),

    descriptionpo   TEXT,
    descriptionpp   TEXT,

    status          CHARACTER(6),

    inputby         VARCHAR(50),
    inputdate       TIMESTAMP WITHOUT TIME ZONE,

    updateby        VARCHAR(50),
    updatedate      TIMESTAMP WITHOUT TIME ZONE,

    docnotmp        CHARACTER(30),
    capexno         CHARACTER(30),

    -- =========================================
    -- KOLOM TAMBAHAN YANG SEBELUMNYA DITAMBAHKAN ALTER
    -- =========================================

    doctype         CHARACTER(10) DEFAULT 'GR',

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),

    qtyretur        NUMERIC(18,2) DEFAULT 0,
    idhistory_price CHAR(30),
    multidisctype   CHAR(30),
    totaldiscount   NUMERIC(18,2)

)
TABLESPACE pg_default;

ALTER TABLE sc_trx.lpb_dtl
    OWNER TO postgres;
	
	
	
	
	
	
/* FUNCTION STORE PROCEDURE TRIGGER */

-- FUNCTION: sc_tmp.tr_lpb_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_lpb_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_lpb_finalize()
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
            FROM sc_trx.lpb
            WHERE idurut = v_idurut
                AND TRIM(COALESCE(inputby, '')) =
                    TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.lpb
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

        -- gunakan docno final
        v_docno := v_new_docno;


        -- ===============================
        -- INSERT HEADER
        -- ===============================
        INSERT INTO sc_trx.lpb (
            idurut, docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.lpb
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.lpb_dtl (
            idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
        )
        SELECT
            idurut, v_docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
        FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;


        UPDATE sc_trx.po_dtl ppd
        SET qtylpb = COALESCE(ppd.qtylpb, 0) + pod.qty_used
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

        UPDATE sc_trx.po_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtylpb, 0) THEN 'LPB'
                ELSE 'F'
            END
            FROM sc_tmp.lpb_dtl t
            WHERE rtrim(t.docno) = rtrim(OLD.docno)
            AND t.inputby = v_inputby
            AND ppd.uniqueid = t.uniqueid;

        UPDATE sc_trx.po po
            SET status = 'LPB'
            WHERE po.docno IN (
                SELECT DISTINCT t.docnopo
                FROM sc_tmp.lpb_dtl t
                WHERE rtrim(t.docno) = rtrim(OLD.docno)
                AND t.inputby = v_inputby
                AND t.docnopo IS NOT NULL
                AND t.docnopo <> ''
            );
        


        -- ===============================
        -- LOG: INSERT HEADER LPB
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.6',              -- kode menu untuk LPB
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );



        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.lpb
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        UPDATE sc_trx.po_dtl ppd
            SET qtylpb = COALESCE(ppd.qtylpb, 0) - pod_lama.qty_lpb_lama
            FROM (
                SELECT uniqueid, SUM(qty) as qty_lpb_lama
                FROM sc_trx.lpb_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND inputby = NEW.inputby
                    AND uniqueid IS NOT NULL AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod_lama
            WHERE ppd.uniqueid = pod_lama.uniqueid;

        DELETE FROM sc_trx.lpb WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.lpb_dtl WHERE docno = NEW.docnotmp;
        
        
        INSERT INTO sc_trx.lpb
        (idurut, docno, cabang, docdate, pemohon, kdsupplier,
        nmsupplier, alamatsupplier, jthtempo,
        biayavol, biayavol2, nosj, nofaktur,
        idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.lpb
        WHERE rtrim(docno) = rtrim(NEW.docno);

        INSERT INTO sc_trx.lpb_dtl
        (idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
        harga, nilai, descriptionpo, descriptionpp, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount)
        SELECT
            idurut, NEW.docnotmp, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
        FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

        UPDATE sc_trx.po_dtl ppd
        SET qtylpb = COALESCE(ppd.qtylpb, 0) + pod.qty_used
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

        
        UPDATE sc_trx.po_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtylpb, 0) THEN 'LPB'
                ELSE 'F'
            END
            FROM sc_tmp.lpb_dtl t
            WHERE rtrim(t.docno) = rtrim(NEW.docno)
            AND t.inputby = v_inputby
            AND ppd.uniqueid = t.uniqueid;

        UPDATE sc_trx.po po
            SET status = 'LPB'
            WHERE po.docno IN (
                SELECT DISTINCT t.docnopo
                FROM sc_tmp.lpb_dtl t
                WHERE rtrim(t.docno) = rtrim(NEW.docno)
                AND t.inputby = v_inputby
                AND t.docnopo IS NOT NULL
                AND t.docnopo <> ''
            );

        -- ===============================
        -- LOG: INSERT HEADER LPB
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.P',                  -- kode module dari menuprg
            'I.P.A.6',              -- kode menu untuk LPB
            'U',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );


        DELETE FROM sc_tmp.lpb WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.lpb_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.lpb SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.lpb SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.lpb WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.lpb_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE OR REPLACE TRIGGER tr_lpb_finalize
    AFTER UPDATE ON sc_tmp.lpb
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_lpb_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_lpb();

CREATE OR REPLACE FUNCTION sc_trx.tr_lpb()
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
            -- REVERT QTYLPB
            UPDATE sc_trx.po_dtl ppd
            SET qtylpb = COALESCE(ppd.qtylpb, 0) - pod.qty_used
            FROM (
                SELECT uniqueid, SUM(qty) as qty_used
                FROM sc_trx.lpb_dtl
                WHERE rtrim(docno) = rtrim(NEW.docno)
                    AND uniqueid IS NOT NULL AND uniqueid <> ''
                GROUP BY uniqueid
            ) pod
            WHERE ppd.uniqueid = pod.uniqueid;

            -- UPDATE STATUS PO_DTL
            UPDATE sc_trx.po_dtl ppd
            SET status = CASE 
                WHEN ppd.qty = COALESCE(ppd.qtylpb, 0) THEN 'LPB'
                ELSE 'F'
            END
            FROM sc_trx.lpb_dtl vd
            WHERE ppd.uniqueid = vd.uniqueid
                AND rtrim(vd.docno) = rtrim(NEW.docno);

            -- UPDATE STATUS PO HEADER
            UPDATE sc_trx.po po
            SET status = CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM sc_trx.po_dtl ppd
                    WHERE rtrim(ppd.docno) = rtrim(po.docno)
                    AND COALESCE(ppd.qtylpb, 0) > 0
                ) THEN 'LPB'
                ELSE 'P'
            END
            WHERE EXISTS (
                SELECT 1 
                FROM sc_trx.lpb_dtl vd
                WHERE rtrim(vd.docno) = rtrim(NEW.docno)
                AND vd.uniqueid IN (
                    SELECT uniqueid 
                    FROM sc_trx.po_dtl 
                    WHERE rtrim(docno) = rtrim(po.docno)
                )
            );

            -- LOG: CANCEL LPB
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.P',
                'I.P.A.6',
                'C',
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );
        END IF;


		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.lpb_dtl
			( idurut, docno, docnopo, idbarang, capexno, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount)
			SELECT idurut, NEW.docno, docnopo, idbarang, capexno, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur,idhistory_price,multidisctype,totaldiscount
			FROM sc_trx.lpb_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.lpb
            (
                idurut, docno, cabang, docdate, pemohon, kdsupplier,
                nmsupplier, alamatsupplier, jthtempo,
                biayavol, biayavol2, nosj, nofaktur,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdsupplier,
            nmsupplier, alamatsupplier, jthtempo,
            biayavol, biayavol2, nosj, nofaktur,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.lpb 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_lpb()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_lpb()
-- Trigger: tr_lpb

-- DROP TRIGGER IF EXISTS tr_lpb ON sc_trx.lpb;

CREATE OR REPLACE TRIGGER tr_lpb
    AFTER UPDATE 
    ON sc_trx.lpb
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_lpb();


/* ============================================================
   TRANSACTION_DT SYNC
   ============================================================

   SOURCE OF TRUTH:
       sc_trx.lpb_dtl

   MAPPING:
       lpb_dtl.uniqueid
           -> transaction_dt.source_uniqueid

       md5(docno + journal_type + source_uniqueid)
           -> transaction_dt.uniqueid

   RULE:
       INSERT lpb_dtl -> INSERT transaction_dt
       DELETE lpb_dtl -> DELETE transaction_dt

   Dengan pola ini:
       - finalize normal       -> otomatis membuat transaction_dt
       - edit LPB              -> delete lama + insert baru
                                -> transaction_dt ikut delete/rebuild
       - delete LPB            -> transaction_dt ikut terhapus
       - tidak perlu sinkronisasi manual di finalize
   ============================================================ */

DROP TRIGGER IF EXISTS tr_lpb_dtl_transaction_dt
ON sc_trx.lpb_dtl;

CREATE OR REPLACE FUNCTION sc_trx.tr_lpb_dtl_transaction_dt()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_tx_uniqueid TEXT;
    v_header      sc_trx.lpb%ROWTYPE;
BEGIN

    /* ========================================================
       DELETE
       ======================================================== */
    IF TG_OP = 'DELETE' THEN

        IF NULLIF(BTRIM(OLD.uniqueid), '') IS NOT NULL THEN

            DELETE FROM sc_trx.transaction_dt
            WHERE uniqueid = md5(
                'LPB|GRNREC|' ||
                BTRIM(OLD.docno) || '|' ||
                BTRIM(OLD.uniqueid)
            );

            /* Fallback untuk data lama yang mungkin masih
               menggunakan source_uniqueid langsung. */
            DELETE FROM sc_trx.transaction_dt
            WHERE BTRIM(source_uniqueid) = BTRIM(OLD.uniqueid)
              AND BTRIM(docno) = BTRIM(OLD.docno)
              AND BTRIM(doctype) = 'GR'
              AND BTRIM(journal_type) = 'GRNREC';

        END IF;

        RETURN OLD;
    END IF;


    /* ========================================================
       INSERT
       ======================================================== */

    IF NULLIF(BTRIM(NEW.uniqueid), '') IS NULL THEN
        RAISE EXCEPTION
            'LPB detail % tidak mempunyai uniqueid PO. Transaction_dt tidak dapat dibuat.',
            BTRIM(NEW.docno);
    END IF;


    v_docno := BTRIM(NEW.docno);

    SELECT h.*
    INTO v_header
    FROM sc_trx.lpb h
    WHERE BTRIM(h.docno) = v_docno
    LIMIT 1;

    IF NOT FOUND THEN
        RAISE EXCEPTION
            'Header LPB % tidak ditemukan saat membuat transaction_dt.',
            v_docno;
    END IF;


    v_tx_uniqueid := md5(
        'LPB|GRNREC|' ||
        v_docno || '|' ||
        BTRIM(NEW.uniqueid)
    );


    /* ========================================================
       UPSERT-SAFE DALAM KONTEKS DETAIL
       Jika trigger dipanggil ulang untuk detail yang sama,
       hapus transaction_dt yang sama terlebih dahulu.
       ======================================================== */

    DELETE FROM sc_trx.transaction_dt
    WHERE uniqueid = v_tx_uniqueid;


    -- ========================================================
    -- HANYA SINKRONISASI KE TRANSACTION_DT
    -- TIDAK ADA POST / UNPOST JURNAL
    -- ========================================================
    INSERT INTO sc_trx.transaction_dt
    (
        uniqueid,
        source_uniqueid,

        docno,
        doctype,
        journal_type,
        line_no,
        docdate,

        idbranch,
        cabang,
        type_in_out,

        ref_docno,
        ref_doctype,

        source_table,
        source_id,
        source_line_id,

        kdsupplier,
        nsupplier,

        idbarang,
        namabarang,
        idunit,

        idarea,
        warehouse,
        bin,

        batch,
        lotno,

        qty,
        harga,
        bruto,
        discount,
        nilai,
        dpp,
        pajak,
        total,

        idtax,
        isinclusive,

        currcode,
        kurs,

        keterangan,
        createdby,
        createddate
    )
    VALUES
    (
        v_tx_uniqueid,
        BTRIM(NEW.uniqueid),

        v_docno,
        'GR',
        'GRNREC',
        GREATEST(COALESCE(NEW.idurut, 1), 1),
        v_header.docdate,

        COALESCE(BTRIM(v_header.cabang), ''),
        COALESCE(BTRIM(v_header.cabang), ''),
        'IN',

        COALESCE(BTRIM(NEW.docnopo), ''),
        'PO',

        'sc_trx.lpb_dtl',
        NEW.idurut,
        NEW.idurut,

        COALESCE(BTRIM(v_header.kdsupplier), ''),
        COALESCE(BTRIM(v_header.nmsupplier), ''),

        COALESCE(BTRIM(NEW.idbarang), ''),
        COALESCE(BTRIM(NEW.nmbarang), ''),
        COALESCE(BTRIM(NEW.unit), ''),

        '',
        COALESCE(BTRIM(NEW.idgudang), ''),

        '',

        '',
        '',

        COALESCE(NEW.qty, 0),
        COALESCE(NEW.harga, 0),
        COALESCE(NEW.harga, 0) * COALESCE(NEW.qty, 0),
        COALESCE(NEW.totaldiscount, 0),

        /* NILAI = nilai DPP dasar */
        COALESCE(
            NULLIF(NEW.nilaikonversi, 0),
            COALESCE(NEW.nilai, 0)
        ),

        /* DPP */
        COALESCE(
            NULLIF(NEW.nilaikonversi, 0),
            COALESCE(NEW.nilai, 0)
        ),

        /* PAJAK */
        COALESCE(NEW.nilaipajak, 0),

        /* TOTAL = DPP + PAJAK */
        COALESCE(
            NULLIF(NEW.nilaikonversi, 0),
            COALESCE(NEW.nilai, 0)
        ) + COALESCE(NEW.nilaipajak, 0),

        COALESCE(NULLIF(BTRIM(NEW.idtax), ''), NULLIF(BTRIM(v_header.idtax), ''), 'NON'),
        COALESCE(NULLIF(BTRIM(v_header.isinclusive), ''), 'NO'),

        COALESCE(NULLIF(BTRIM(NEW.currcode), ''), NULLIF(BTRIM(v_header.currcode), ''), 'IDR'),
        COALESCE(NULLIF(NEW.kurs, 0), NULLIF(v_header.kurs, 0), 1),

        COALESCE(
            NULLIF(BTRIM(NEW.descriptionpp), ''),
            NULLIF(BTRIM(NEW.descriptionpo), ''),
            'LPB ' || v_docno
        ),

        COALESCE(BTRIM(NEW.inputby), BTRIM(v_header.inputby), ''),
        COALESCE(NEW.inputdate, v_header.inputdate, CURRENT_TIMESTAMP)
    );


    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_trx.tr_lpb_dtl_transaction_dt()
    OWNER TO postgres;


CREATE OR REPLACE TRIGGER tr_lpb_dtl_transaction_dt
    AFTER INSERT OR DELETE
    ON sc_trx.lpb_dtl
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_lpb_dtl_transaction_dt();





-- =========== TAMBAHAN 24/8/26 ====================
-- Kolom capexno dan printcount sudah dimasukkan langsung ke CREATE TABLE
-- agar seluruh script dapat dijalankan satu kali tanpa ALTER berulang.

-- docdate sudah bertipe DATE sejak CREATE TABLE.
-- Tidak diperlukan ALTER COLUMN TYPE lagi.

-- printcount sudah dimasukkan langsung ke CREATE TABLE.

-- ==================== END OFTAMBAHAN 24/8/26  ====================


--TAMBAHN MULTIDISCOUNT----
-- Kolom idhistory_price, multidisctype, dan totaldiscount sudah
-- dimasukkan langsung ke CREATE TABLE agar tidak terjadi duplicate
-- column / missing semicolon saat script dijalankan satu kali.

--,idhistory_price,multidisctype,totaldiscount







/* DELETE LPB ALL */

-- ============================================================
-- FUNCTION : sc_trx.sp_delete_lpb
-- PURPOSE  : Delete LPB dan reverse PO serta inventory; tanpa proses jurnal
-- ============================================================

/* DELETE LPB ALL */

-- ============================================================
-- FUNCTION : sc_trx.sp_delete_lpb
-- PURPOSE  : Delete LPB dan reverse PO serta inventory; tanpa proses jurnal
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.sp_delete_lpb(
    p_docno TEXT,
    p_user  TEXT
)
RETURNS JSONB
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno        TEXT;
    v_client_ip    TEXT;
    v_total_detail INTEGER;
    v_deleted_stk  INTEGER;
BEGIN

    -- =========================================================
    -- VALIDASI DOCNO
    -- =========================================================

    v_docno := TRIM(p_docno);

    IF COALESCE(v_docno, '') = '' THEN
        RAISE EXCEPTION 'DOCNO LPB tidak boleh kosong';
    END IF;


    -- =========================================================
    -- LOCK DOCUMENT
    -- Mencegah delete bersamaan
    -- =========================================================

    PERFORM pg_advisory_xact_lock(hashtext(v_docno));


    -- =========================================================
    -- VALIDASI LPB
    -- =========================================================

    IF NOT EXISTS (
        SELECT 1
        FROM sc_trx.lpb
        WHERE TRIM(docno) = v_docno
    ) THEN

        RETURN jsonb_build_object(
            'success', false,
            'message', 'Dokumen LPB tidak ditemukan',
            'docno', v_docno
        );

    END IF;


    -- =========================================================
    -- HITUNG DETAIL
    -- =========================================================

    SELECT COUNT(*)
    INTO v_total_detail
    FROM sc_trx.lpb_dtl
    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- GET CLIENT IP
    -- =========================================================

    v_client_ip := sc_log.fn_get_user_ip(p_user);


    -- =========================================================
    -- 1. REVERSE QTY LPB KE PO DETAIL
    -- =========================================================

    UPDATE sc_trx.po_dtl ppd

    SET qtylpb = GREATEST(
        0,
        COALESCE(ppd.qtylpb, 0)
        -
        COALESCE(x.qty_used, 0)
    )

    FROM (

        SELECT
            TRIM(uniqueid) AS uniqueid,
            SUM(COALESCE(qty, 0)) AS qty_used

        FROM sc_trx.lpb_dtl

        WHERE TRIM(docno) = v_docno

          AND uniqueid IS NOT NULL
          AND TRIM(uniqueid) <> ''

        GROUP BY TRIM(uniqueid)

    ) x

    WHERE TRIM(ppd.uniqueid) = x.uniqueid;


    -- =========================================================
    -- 2. UPDATE STATUS PO DETAIL
    -- =========================================================

    UPDATE sc_trx.po_dtl ppd

    SET status = CASE

        WHEN COALESCE(ppd.qtylpb, 0) <= 0
            THEN 'F'

        WHEN COALESCE(ppd.qtylpb, 0)
             >= COALESCE(ppd.qty, 0)
            THEN 'LPB'

        ELSE 'F'

    END

    WHERE TRIM(ppd.uniqueid) IN (

        SELECT DISTINCT TRIM(uniqueid)

        FROM sc_trx.lpb_dtl

        WHERE TRIM(docno) = v_docno

          AND uniqueid IS NOT NULL
          AND TRIM(uniqueid) <> ''

    );


    -- =========================================================
    -- 3. UPDATE STATUS PO HEADER
    -- =========================================================

    UPDATE sc_trx.po po

    SET status = CASE

        WHEN EXISTS (

            SELECT 1

            FROM sc_trx.po_dtl pd

            WHERE TRIM(pd.docno) = TRIM(po.docno)

              AND COALESCE(pd.qtylpb, 0) > 0

        )

        THEN 'LPB'

        ELSE 'P'

    END

    WHERE TRIM(po.docno) IN (

        SELECT DISTINCT TRIM(docnopo)

        FROM sc_trx.lpb_dtl

        WHERE TRIM(docno) = v_docno

          AND docnopo IS NOT NULL
          AND TRIM(docnopo) <> ''

    );


    -- =========================================================
    -- 4. DELETE TRANSACTION_DT
    -- Source identity berasal dari LPB detail.
    -- Detail trigger juga menghapus saat lpb_dtl dihapus.
    -- Bagian ini membersihkan orphan/legacy transaction_dt.
    -- =========================================================

    DELETE FROM sc_trx.transaction_dt td
    WHERE BTRIM(td.docno) = v_docno
      AND BTRIM(td.doctype) = 'GR'
      AND BTRIM(td.journal_type) = 'GRNREC';


    -- =========================================================
    -- 5. JOURNAL
    -- =========================================================
    -- TIDAK ADA POST / UNPOST / DELETE JURNAL DI MODUL LPB.
    -- Journal diproses oleh accounting engine secara terpisah.
    -- =========================================================


    -- =========================================================
    -- 5. DELETE INVENTORY TRANSACTION
    --
    -- Trigger stkblc seharusnya otomatis:
    --   - Update stkgdw
    --   - Recalculate avg cost
    -- =========================================================

    DELETE FROM sc_trx.stkblc

    WHERE TRIM(docno) = v_docno

      AND TRIM(doctype) = 'GR';

    GET DIAGNOSTICS v_deleted_stk = ROW_COUNT;


    -- =========================================================
    -- 6. DELETE TEMP LPB DETAIL
    -- =========================================================

    DELETE FROM sc_tmp.lpb_dtl

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 7. DELETE TEMP LPB HEADER
    -- =========================================================

    DELETE FROM sc_tmp.lpb

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 8. DELETE LPB DETAIL
    -- =========================================================

    DELETE FROM sc_trx.lpb_dtl

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 9. DELETE LPB HEADER
    -- =========================================================

    DELETE FROM sc_trx.lpb

    WHERE TRIM(docno) = v_docno;


    -- =========================================================
    -- 10. AUDIT LOG
    -- =========================================================

    PERFORM sc_log.fn_log_transaction(
        v_docno::CHAR(30),
        NULL,
        'I.P',
        'I.P.A.6',
        'D',
        p_user,
        v_client_ip,
        p_user
    );


    -- =========================================================
    -- SUCCESS
    -- =========================================================

    RETURN jsonb_build_object(

        'success', true,

        'message',
        'LPB berhasil dihapus dan seluruh transaksi terkait telah direverse',

        'docno', v_docno,

        'total_detail', v_total_detail,

        'total_stock_deleted', v_deleted_stk

    );


EXCEPTION

    WHEN OTHERS THEN

        RAISE EXCEPTION
            'Gagal menghapus LPB % : %',
            COALESCE(v_docno, p_docno),
            SQLERRM;

END;

$$;


-- ============================================================
-- CONTOH
-- ============================================================

-- SELECT sc_trx.sp_delete_lpb(
--     'LPB/2609/PA0001',
--     'USERNAME'
-- );
-- Contoh:
-- SELECT sc_trx.sp_delete_lpb('LPB/2609/PA0001', 'USERNAME');

/* ============================================================
   VALIDATION
   ============================================================

   1. Setiap LPB detail final wajib mempunyai transaction_dt.
   2. Tidak boleh ada transaction_dt orphan untuk LPB/GRNREC.
   3. Delete lpb_dtl otomatis menghapus transaction_dt.
   4. Insert lpb_dtl otomatis membuat transaction_dt.
   ============================================================ */

-- Cek orphan transaction_dt untuk LPB
-- SELECT td.*
-- FROM sc_trx.transaction_dt td
-- WHERE BTRIM(td.doctype) = 'GR'
--   AND BTRIM(td.journal_type) = 'GRNREC'
--   AND NOT EXISTS (
--       SELECT 1
--       FROM sc_trx.lpb_dtl d
--       WHERE BTRIM(d.docno) = BTRIM(td.docno)
--         AND BTRIM(d.uniqueid) = BTRIM(td.source_uniqueid)
--   );

-- Cek jumlah source dan transaction_dt per LPB
-- SELECT
--     d.docno,
--     COUNT(*) AS total_lpb_detail,
--     COUNT(td.uniqueid) AS total_transaction_dt
-- FROM sc_trx.lpb_dtl d
-- LEFT JOIN sc_trx.transaction_dt td
--   ON BTRIM(td.docno) = BTRIM(d.docno)
--  AND BTRIM(td.source_uniqueid) = BTRIM(d.uniqueid)
--  AND BTRIM(td.doctype) = 'GR'
--  AND BTRIM(td.journal_type) = 'GRNREC'
-- GROUP BY d.docno
-- ORDER BY d.docno;


COMMIT;
