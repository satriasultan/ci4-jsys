
-- JALANKAN INI DULU
DROP TABLE IF EXISTS sc_trx.lpb;
DROP TABLE IF EXISTS sc_tmp.lpb;
DROP TABLE IF EXISTS sc_tmp.lpb_dtl;
DROP TABLE IF EXISTS sc_trx.lpb_dtl;




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

    -- =========================================
    -- KOLOM YANG SEBELUMNYA DITAMBAHKAN ALTER
    -- =========================================

    doctype         CHARACTER(10) DEFAULT 'GR',

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),

    qtyretur        NUMERIC(18,2) DEFAULT 0

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

    -- =========================================
    -- KOLOM YANG SEBELUMNYA DITAMBAHKAN ALTER
    -- =========================================

    doctype         CHARACTER(10) DEFAULT 'GR',

    idtax           CHARACTER(20),
    currcode        CHARACTER(3),
    kurs            NUMERIC(18,2),

    nilaikonversi   NUMERIC(18,2),
    nilaipajak      NUMERIC(18,2),

    qtyretur        NUMERIC(18,2) DEFAULT 0

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
                FROM sc_trx.lpb
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
            inputby, inputdate, status, updateby, updatedate,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur
        )
        SELECT
            idurut, v_docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur
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
        /* NILAI PERSEDIAAN DAN NILAI COA */
        -- =========================================
        -- UPSERT STKBLC (SOURCE: sc_tmp)
        -- =========================================
        PERFORM sc_trx.sp_unpost_by_doc(v_docno,'GR');
        DELETE FROM sc_trx.stkblc WHERE docno = v_docno and doctype='GR';
        INSERT INTO sc_trx.stkblc (
            idlocation,
            idarea,
            batch,
            idbarang,
            trxdate,
            doctype,
            docno,
            docref,
            qty_in,
            pricelst_in,
            currcode,
            currvalue,
            hist,
            ctype,
            idgroup,
            grouptype,
            is_posted,
            posted_at
        )
        SELECT
            d.idgudang,
            trim(d.idgudang)||'.0000',
            COALESCE(d.idspec,''),
            d.idbarang,

                    CAST(h.docdate AS DATE) + (NOW()::time),
                    'GR',
                    v_docno,
                    d.docnopo,

            -- 🔥 FIX NON STOCK
            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK') = 'NON STOCK' THEN 0
                ELSE (COALESCE(d.qty,0) + COALESCE(d.qtybonus,0))
            END,

                    COALESCE(d.harga,0),

                    h.currcode,
                    COALESCE(h.kurs,1),

                    'LPB',

            -- 🔥 FIX CTYPE
            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK') = 'NON STOCK' THEN 'NON'
                ELSE 'IN'
            END,

            -- 🔥 FIX SOURCE MASTER
            mb.idgroup,
            COALESCE(mb.grouptype,'STOCK'),

                    FALSE,
                    NULL

                FROM sc_tmp.lpb h
                JOIN sc_tmp.lpb_dtl d
                    ON rtrim(d.docno) = rtrim(h.docno)

        LEFT JOIN sc_mst.mbarang mb
            ON mb.idbarang = d.idbarang

        --WHERE rtrim(h.docno) = rtrim(OLD.docno)
        --AND h.inputby = v_inputby

                ON CONFLICT (
            docno,
            idbarang,
            idlocation,
            batch,
            uniqueid
        )
        DO UPDATE SET
            qty_in      = EXCLUDED.qty_in,
            pricelst_in = EXCLUDED.pricelst_in,
            currcode    = EXCLUDED.currcode,
            currvalue   = EXCLUDED.currvalue,
            idgroup     = EXCLUDED.idgroup,
            grouptype   = EXCLUDED.grouptype,
            is_posted   = FALSE,
            posted_at   = NULL;
            
            
        /* END NILAI PERSEDIAAN DAN NILAI COA */	
        PERFORM sc_trx.sp_post_gl(v_inputby);	


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

        INSERT INTO sc_trx.lpb_dtl
        (idurut, docno, docnopo, idbarang, capexno, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
        harga, nilai, descriptionpo, descriptionpp, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur)
        SELECT
            idurut, NEW.docnotmp, docnopo, idbarang, capexno, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur
        FROM sc_tmp.lpb_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

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

        /* NILAI PERSEDIAAN DAN NILAI COA */
        -- =========================================
        -- UPSERT STKBLC (SOURCE: sc_tmp)
        -- =========================================
        -- =========================================
        -- UPSERT STKBLC (DOCNOTMP - sc_tmp)
        -- =========================================
        PERFORM sc_trx.sp_unpost_by_doc(NEW.docnotmp,'GR');
        DELETE FROM sc_trx.stkblc WHERE docno = trim(NEW.docnotmp) and doctype='GR' ;
        INSERT INTO sc_trx.stkblc (
            idlocation,
            idarea,
            batch,
            idbarang,
            trxdate,
            doctype,
            docno,
            docref,
            qty_in,
            pricelst_in,
            currcode,
            currvalue,
            hist,
            ctype,
            idgroup,
            grouptype,
            is_posted,
            posted_at
        )
        SELECT
            d.idgudang,
            trim(d.idgudang)||'.0000',
            COALESCE(d.idspec,''),
            d.idbarang,

            CAST(h.docdate AS DATE) + (NOW()::time),
            'GR',
            d.docnotmp,
            d.docnopo,

            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK') = 'NON STOCK' THEN 0
                ELSE (COALESCE(d.qty,0) + COALESCE(d.qtybonus,0))
            END,

            COALESCE(d.harga,0),

            h.currcode,
            COALESCE(h.kurs,1),

            'LPB',

            CASE 
                WHEN COALESCE(mb.grouptype,'STOCK') = 'NON STOCK' THEN 'NON'
                ELSE 'IN'
            END,

            mb.idgroup,
            COALESCE(mb.grouptype,'STOCK'),

            FALSE,
            NULL

        FROM sc_tmp.lpb h
        JOIN sc_tmp.lpb_dtl d
            ON rtrim(d.docno) = rtrim(h.docno)

        LEFT JOIN sc_mst.mbarang mb
            ON mb.idbarang = d.idbarang

        WHERE trim(h.docno) = trim(new.docno)
        AND trim(h.inputby) = trim(v_inputby)

        ON CONFLICT (docno, idbarang, idlocation, batch)
        DO UPDATE SET
            qty_in = EXCLUDED.qty_in,
            pricelst_in = EXCLUDED.pricelst_in,
            currcode = EXCLUDED.currcode,
            currvalue = EXCLUDED.currvalue,
            idgroup = EXCLUDED.idgroup,
            grouptype = EXCLUDED.grouptype,
            is_posted = FALSE,
            posted_at = NULL;
            
            
        /* END NILAI PERSEDIAAN DAN NILAI COA */
        PERFORM sc_trx.sp_post_gl(v_inputby);

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



CREATE TRIGGER tr_lpb_finalize
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
            inputby, inputdate, status, updateby, updatedate, docnotmp,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur)
			SELECT idurut, NEW.docno, docnopo, idbarang, capexno, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, volitem, biaya, biaya2, unit, qty, 
            harga, nilai, descriptionpo, descriptionpp, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno,idtax,currcode,kurs,nilaikonversi,nilaipajak,qtyretur
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





-- =========== TAMBAHAN 24/8/26 ====================
ALTER TABLE sc_tmp.lpb_dtl
ADD COLUMN capexno character(30)

ALTER TABLE sc_trx.lpb_dtl
ADD COLUMN capexno character(30)



-- docdate
ALTER TABLE sc_trx.lpb
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.lpb
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;



-- printcount
ALTER TABLE sc_tmp.lpb
ADD COLUMN printcount integer
ALTER TABLE sc_trx.lpb
ADD COLUMN printcount integer

-- ==================== END OFTAMBAHAN 24/8/26  ====================