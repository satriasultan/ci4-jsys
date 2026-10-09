--I.Q.A.2

--drop table sc_tmp.transfer_location_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.transfer_location_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'SPK_TRANSFERS' ,
    docdate character(20) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    cabang_sent character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    estpakai character(20) COLLATE pg_catalog."default",
	idlocation_from character(30),
	idlocation_to character(30),
	idlocation_transit character(30),
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_transfer_location_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.transfer_location_mst
    OWNER to postgres;


--drop table sc_trx.transfer_location_mst;
CREATE TABLE IF NOT EXISTS sc_trx.transfer_location_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
	doctype character(20) default 'SPK_TRANSFERS' ,
    docdate character(20) COLLATE pg_catalog."default",
	docref character(30) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
	cabang_sent character (30 ) COLLATE pg_catalog."default",  
    pemohon character(100) COLLATE pg_catalog."default",
    estpakai character(20) COLLATE pg_catalog."default",
	idlocation_from character(30),
	idlocation_to character(30),
	idlocation_transit character(30),
    status character(6) COLLATE pg_catalog."default",
    description TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_transfer_location_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.transfer_location_mst
    OWNER to postgres;



--drop table sc_tmp.transfer_location_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.transfer_location_dtl
(
    idurut BIGSERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
	docref character(30) COLLATE pg_catalog."default",
	doctype character(20) default 'SPK_TRANSFERS' ,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qtystock NUMERIC(18,2),
    qty NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
	val numeric(18,2),
	valsum numeric(18,2),
    inputby character(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
	iduniq text,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.transfer_location_dtl
    OWNER TO postgres;


--drop table sc_trx.transfer_location_dtl;
CREATE TABLE IF NOT EXISTS sc_trx.transfer_location_dtl
(
    idurut INTEGER,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
	docref character(30) COLLATE pg_catalog."default",
	doctype character(20) default 'SPK_TRANSFERS' ,
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
	qtystock NUMERIC(18,2),
    qty NUMERIC(18,2),
    description TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
	val numeric(18,2),
	valsum numeric(18,2),
    inputby character(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
	iduniq text,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.transfer_location_dtl
    OWNER TO postgres;






-- FUNCTION: sc_tmp.tr_tmp_transfer_location_mst()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_transfer_location_mst();
CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_transfer_location_mst()
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
	v_inputdate timestamp without time zone;
BEGIN
    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        -- ===============================
        -- NORMALISASI
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;
        v_inputdate  := NEW.inputdate;
        --v_idurut  := NEW.idurut;
        -- ambil base docno (tanpa angka belakang)
        -- contoh:
        -- 05M/2601/PA0001 -> 05M/2601/PA
        -- PPB/2601/PT0025 -> PPB/2601/PT
        /* ========================================================
        CEK APAKAH SUDAH PERNAH DIFINALKAN
        ======================================================== */

        IF EXISTS (
            SELECT 1
            FROM sc_trx.transfer_location_mst
            WHERE idurut = v_idurut
                AND TRIM(COALESCE(inputby, '')) =
                    TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.transfer_location_mst
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

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.transfer_location_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(
                v_new_docno,
                '.*?([0-9]+)$',
                '\1'
            );

            IF COALESCE(v_num, '') = '' THEN

                RAISE EXCEPTION
                    'Format DOCNO PP tidak valid: %',
                    v_new_docno;

            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(
                    v_num_int::TEXT,
                    length(v_num),
                    '0'
                );

        END LOOP;

        v_docno := v_new_docno;


        -- ===============================
        -- INSERT HEADER
		

        -- ===============================
        INSERT INTO sc_trx.transfer_location_mst (
            docno,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,status,description,inputby,inputdate,updateby,updatedate,printby,printdate,docnotmp
        )
        (SELECT v_docno,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,'F',description,inputby,inputdate,updateby,updatedate,printby,printdate,docnotmp FROM sc_tmp.transfer_location_mst
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby
          AND inputdate = v_inputdate);

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.transfer_location_dtl 
		(docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut)
        (SELECT v_docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,'F' AS status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut
		FROM sc_tmp.transfer_location_dtl WHERE rtrim(docno) = rtrim(OLD.docno) AND inputby = v_inputby);

        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.transfer_location_mst
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby
          AND inputdate = v_inputdate;

        DELETE FROM sc_tmp.transfer_location_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.transfer_location_mst WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.transfer_location_dtl WHERE docno = NEW.docnotmp;

        -- ===============================
        INSERT INTO sc_trx.transfer_location_mst (
            docno,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,status,description,inputby,inputdate,updateby,updatedate,printby,printdate,docnotmp
        )
        (SELECT new.docnotmp,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,'F',description,inputby,inputdate,updateby,updatedate,printby,printdate,docnotmp FROM sc_tmp.transfer_location_mst
        WHERE trim(docno) = trim(NEW.docnotmp));

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.transfer_location_dtl 
		(docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut)
        (SELECT NEW.docnotmp,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,'F' AS status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut
		FROM sc_tmp.transfer_location_dtl WHERE rtrim(docno) = rtrim(NEW.docno));
		

        DELETE FROM sc_tmp.transfer_location_mst WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.transfer_location_dtl WHERE rtrim(docno) = rtrim(NEW.docno);


    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.transfer_location_mst SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.transfer_location_mst SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.transfer_location_mst WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.transfer_location_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_tmp_transfer_location_mst
    AFTER UPDATE ON sc_tmp.transfer_location_mst
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_tmp_transfer_location_mst();




-- DROP FUNCTION IF EXISTS sc_trx.tr_trx_transfer_location_mst();

CREATE OR REPLACE FUNCTION sc_trx.tr_trx_transfer_location_mst()
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
	v_inputdate timestamp without time zone;
BEGIN		

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
        -- ===============================
        INSERT INTO sc_tmp.transfer_location_mst (
            docno,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,status,description,inputby,inputdate,updateby,updatedate,printby,printdate,docnotmp
        )
        (SELECT docno,doctype,docdate,docref,cabang,cabang_sent,pemohon,estpakai,idlocation_from,idlocation_to,idlocation_transit,'E',description,inputby,inputdate,updateby,updatedate,printby,printdate,docno as docnotmp FROM sc_trx.transfer_location_mst
        WHERE trim(docno) = new.docno);

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_tmp.transfer_location_dtl 
		(docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut)
        (SELECT docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,'F' AS status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docno as docnotmp ,idurut
		FROM sc_trx.transfer_location_dtl WHERE trim(docno) =  trim(new.docno));

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_trx_transfer_location_mst()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_trx_transfer_location_mst()
-- Trigger: tr_trx_transfer_location_mst

-- DROP TRIGGER IF EXISTS tr_trx_transfer_location_mst ON sc_trx.transfer_location_mst;

CREATE OR REPLACE TRIGGER tr_trx_transfer_location_mst
    AFTER UPDATE 
    ON sc_trx.transfer_location_mst
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_trx_transfer_location_mst();






CREATE OR REPLACE FUNCTION sc_trx.fn_sync_transfer_location_dtl_to_transaction(
    p_docno TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_hdr        sc_trx.transfer_location_mst%ROWTYPE;
    v_dtl        sc_trx.transfer_location_dtl%ROWTYPE;
    v_journal    CHAR(6);
    v_idbranch   CHAR(20);
    v_qty        NUMERIC(18,2);
    v_val        NUMERIC(18,2);
    v_from       TEXT;
    v_to         TEXT;
    v_transit    TEXT;
    v_src_uid    TEXT;
    v_cab_from   TEXT;
    v_cab_to     TEXT;
    v_uid        TEXT;
    v_type       CHAR(3);
    v_loc        TEXT;
    v_cabang     TEXT;
    v_step       TEXT;    -- label step: 'OUT-FROM', 'IN-TRANSIT', 'OUT-TRANSIT', 'IN-TO'
BEGIN
    -- ============================================
    -- 1. Ambil header
    -- ============================================
    SELECT * INTO v_hdr
    FROM sc_trx.transfer_location_mst
    WHERE rtrim(docno) = rtrim(p_docno);
    IF NOT FOUND THEN RETURN; END IF;

    -- ============================================
    -- 2. Ambil detail
    -- ============================================
    SELECT * INTO v_dtl
    FROM sc_trx.transfer_location_dtl
    WHERE rtrim(docno) = rtrim(p_docno)
      AND iduniq = p_uniqueid;
    IF NOT FOUND THEN RETURN; END IF;

    -- ============================================
    -- 3. Siapkan nilai
    -- ============================================
    v_journal := CASE
        WHEN rtrim(v_hdr.cabang) = rtrim(v_hdr.cabang_sent) THEN 'TRFWHS'::CHAR(6)
        ELSE 'BRNTRF'::CHAR(6)
    END;

    v_idbranch := CASE rtrim(v_hdr.cabang)
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE rtrim(v_hdr.cabang)::CHAR(20)
    END;

    v_qty      := COALESCE(v_dtl.qty, 0);
    v_val      := COALESCE(v_dtl.val, 0);
    v_src_uid  := COALESCE(v_dtl.iduniq, '');

    v_from     := rtrim(COALESCE(v_hdr.idlocation_from, ''));
    v_to       := rtrim(COALESCE(v_hdr.idlocation_to, ''));
    v_transit  := rtrim(COALESCE(v_hdr.idlocation_transit, ''));

    v_cab_from := rtrim(COALESCE(v_hdr.cabang, ''));
    v_cab_to   := rtrim(COALESCE(v_hdr.cabang_sent, ''));

    -- Kalau from == to → tidak ada perpindahan, skip
    IF v_from = v_to AND v_from = v_transit THEN
        RETURN;
    END IF;

    -- Transit dianggap aktif kalau beda dari from & to
    IF v_transit = v_from OR v_transit = v_to THEN
        v_transit := '';
    END IF;

    -- ============================================
    -- 4. Loop step: FROM → [TRANSIT] → TO
    -- ============================================
    FOR v_step IN
        SELECT unnest(
            CASE
                WHEN v_transit = '' THEN ARRAY['OUT-FROM','IN-TO']
                ELSE ARRAY['OUT-FROM','IN-TRANSIT','OUT-TRANSIT','IN-TO']
            END
        )
    LOOP
        -- Tentukan type_in_out
        IF v_step LIKE 'OUT%' THEN
            v_type := 'OUT';
        ELSE
            v_type := 'IN';
        END IF;

        -- Tentukan lokasi
        v_loc := CASE v_step
            WHEN 'OUT-FROM'    THEN v_from
            WHEN 'IN-TRANSIT'  THEN v_transit
            WHEN 'OUT-TRANSIT' THEN v_transit
            WHEN 'IN-TO'       THEN v_to
        END;

        -- Tentukan cabang (asal untuk OUT, tujuan untuk IN)
        v_cabang := CASE
            WHEN v_type = 'OUT' THEN v_cab_from
            ELSE v_cab_to
        END;

        -- Generate uniqueid
        v_uid := md5(rtrim(p_docno) || '|' || v_step || '|' || v_src_uid);

        -- ============================================
        -- 5. Upsert
        -- ============================================
        INSERT INTO sc_trx.transaction_dt (
            uniqueid, source_uniqueid, docno, doctype, journal_type,
            line_no, docdate, idbranch, cabang, type_in_out,
            ref_docno, ref_doctype,
            idbarang, namabarang, idunit, idarea, warehouse,
            bin, batch, lotno,
            qty, harga, bruto, discount, nilai, dpp, pajak, total,
            createdby
        )
        VALUES (
            v_uid, v_src_uid, rtrim(p_docno), 'SPK_TRANSFERS', v_journal,
            v_dtl.idurut, v_hdr.docdate, v_idbranch, v_cabang, v_type,
            rtrim(v_dtl.docref), 'TRF',
            rtrim(v_dtl.idbarang), rtrim(v_dtl.nmbarang), rtrim(v_dtl.unit),
            v_loc, v_loc,
            '', '', '',
            v_qty, 0, v_val, 0, v_val, 0, 0, v_val,
            rtrim(v_hdr.inputby)
        )
        ON CONFLICT (uniqueid) DO UPDATE
        SET
            qty         = EXCLUDED.qty,
            nilai       = EXCLUDED.nilai,
            total       = EXCLUDED.total,
            warehouse   = EXCLUDED.warehouse,
            idarea      = EXCLUDED.idarea,
            cabang      = EXCLUDED.cabang,
            updateddate = CURRENT_TIMESTAMP;
    END LOOP;
END;
$$;



CREATE OR REPLACE FUNCTION sc_trx.fn_transfer_location_dtl_to_transaction()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
    PERFORM sc_trx.fn_sync_transfer_location_dtl_to_transaction(NEW.docno, NEW.iduniq);
    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_transfer_location_dtl_to_transaction
AFTER INSERT OR UPDATE ON sc_trx.transfer_location_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_transfer_location_dtl_to_transaction();