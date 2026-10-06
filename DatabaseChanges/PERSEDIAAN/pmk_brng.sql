-- ============================================================
-- PMK BRNG - ONE EXECUTE / CLEAN INSTALL
-- I.Q.A.5
-- ============================================================

BEGIN;

-- HAPUS TRIGGER LAMA
DROP TRIGGER IF EXISTS tr_tmp_pmk_brng_mst ON sc_tmp.pmk_brng_mst;
DROP TRIGGER IF EXISTS tr_trx_pmk_brng_mst ON sc_trx.pmk_brng_mst;

-- HAPUS FUNCTION LAMA
DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_pmk_brng_mst();
DROP FUNCTION IF EXISTS sc_trx.tr_trx_pmk_brng_mst();
DROP FUNCTION IF EXISTS sc_trx.sp_rebuild_pmk(VARCHAR);
DROP FUNCTION IF EXISTS sc_trx.sp_unpost_stk_pmk(VARCHAR);

-- HAPUS TABEL LAMA
DROP TABLE IF EXISTS sc_tmp.pmk_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.pmk_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.pmk_brng_mst CASCADE;
DROP TABLE IF EXISTS sc_trx.pmk_brng_mst CASCADE;

-- ============================================================
-- CREATE TABLE
-- ============================================================

CREATE TABLE sc_tmp.pmk_brng_mst
(
    idurut BIGSERIAL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) DEFAULT 'pmk_brng',
    docdate character(20) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    cabang character(30) COLLATE pg_catalog."default",
    cabang_sent character(30) COLLATE pg_catalog."default",
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
    CONSTRAINT pk_tmp_pmk_brng_mst PRIMARY KEY (docno)
);
ALTER TABLE sc_tmp.pmk_brng_mst OWNER TO postgres;
CREATE TABLE sc_trx.pmk_brng_mst
(
    idurut BIGINT,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) DEFAULT 'pmk_brng',
    docdate character(20) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    cabang character(30) COLLATE pg_catalog."default",
    cabang_sent character(30) COLLATE pg_catalog."default",
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
    CONSTRAINT pk_trx_pmk_brng_mst PRIMARY KEY (docno)
);
ALTER TABLE sc_trx.pmk_brng_mst OWNER TO postgres;
CREATE TABLE sc_tmp.pmk_brng_dtl
(
    idurut BIGSERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docref character(30) COLLATE pg_catalog."default",
    doctype character(20) DEFAULT 'pmk_brng',
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
    docnotmp character(30),
    idcostcenter character(10),
    batch character(100),
    idlocation character(10),
    idcoa character(20)
);
ALTER TABLE sc_tmp.pmk_brng_dtl OWNER TO postgres;
CREATE TABLE sc_trx.pmk_brng_dtl
(
    idurut INTEGER,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    docref character(30) COLLATE pg_catalog."default",
    doctype character(20) DEFAULT 'pmk_brng',
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
    docnotmp character(30),
    idcostcenter character(10),
    batch character(100),
    idlocation character(10),
    idcoa character(20)
);
ALTER TABLE sc_trx.pmk_brng_dtl OWNER TO postgres;

CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_pmk_brng_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno TEXT;
    v_inputby TEXT;
    v_inputdate TIMESTAMP;
    v_base_docno TEXT;
    v_new_docno TEXT;
    v_num TEXT;
    v_num_int INTEGER;
    v_row RECORD;
    v_doctype TEXT;
    v_idurut BIGINT;
    v_lock_key BIGINT;
BEGIN

-- =========================================
-- NORMALISASI
-- =========================================
v_doctype := UPPER(TRIM(COALESCE(NEW.doctype,'PMKBRG')));

-- =====================================================
-- 🔥 NORMAL FINAL
-- =====================================================
IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

    v_docno := TRIM(NEW.docno);
    v_inputby := NEW.inputby;
    v_inputdate := NEW.inputdate;
    v_idurut := NEW.idurut;

    /* ========================================================
    CEK APAKAH SUDAH PERNAH DIFINALKAN
    ======================================================== */

    IF EXISTS (
        SELECT 1
        FROM sc_trx.pmk_brng_mst
        WHERE idurut = v_idurut
            AND TRIM(COALESCE(inputby, '')) =
                TRIM(COALESCE(v_inputby, ''))
    ) THEN

        DELETE FROM sc_tmp.pmk_brng_mst
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
            FROM sc_trx.pmk_brng_mst
            WHERE TRIM(docno) = TRIM(v_new_docno)
        );

        v_num := regexp_replace(
            v_new_docno,
            '.*?([0-9]+)$',
            '\1'
        );

        IF COALESCE(v_num, '') = '' THEN

            RAISE EXCEPTION
                'Format DOCNO Pemakaian Barang tidak valid: %',
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
    -- VALIDASI STOCK
    -- ===============================
    FOR v_row IN
        SELECT d.idbarang, d.idlocation, COALESCE(d.batch,'') batch,
               d.qty, COALESCE(a.qty,0) stock
        FROM sc_tmp.pmk_brng_dtl d
        LEFT JOIN sc_trx.stkblc_avgcost a
          ON TRIM(a.idbarang)=TRIM(d.idbarang)
         AND TRIM(a.idlocation)=TRIM(d.idlocation)
         AND TRIM(a.batch)=TRIM(COALESCE(d.batch,''))
        WHERE TRIM(d.docno)=TRIM(OLD.docno)
    LOOP
        IF v_row.qty > v_row.stock THEN
            RAISE EXCEPTION 'Stock tidak cukup: %', v_row.idbarang;
        END IF;
    END LOOP;

    -- ===============================
    -- INSERT HEADER
    -- ===============================
    INSERT INTO sc_trx.pmk_brng_mst
    (
        idurut, docno, doctype, docdate, docref, cabang, cabang_sent, pemohon,
        estpakai, idlocation_from, idlocation_to, idlocation_transit, status,
        description, inputby, inputdate, updateby, updatedate, printby, printdate, docnotmp
    )
    SELECT
        idurut, v_docno, v_doctype, docdate, docref, cabang, cabang_sent, pemohon,
        estpakai, idlocation_from, idlocation_to, idlocation_transit, 'F',
        description, inputby, inputdate, updateby, updatedate, printby, printdate, docnotmp
    FROM sc_tmp.pmk_brng_mst
    WHERE TRIM(docno)=TRIM(OLD.docno);

    -- ===============================
    -- INSERT DETAIL
    -- ===============================
    INSERT INTO sc_trx.pmk_brng_dtl
    (
        idurut, docno, docref, doctype, idbarang, nmbarang, unit, qtystock, qty,
        description, status, val, valsum, inputby, inputdate, updateby, updatedate,
        iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
    )
    SELECT
        idurut, v_docno, docref, v_doctype, idbarang, nmbarang, unit, qtystock, qty,
        description, 'F', val, valsum, inputby, inputdate, updateby, updatedate,
        iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
    FROM sc_tmp.pmk_brng_dtl
    WHERE TRIM(docno)=TRIM(OLD.docno);

    -- ===============================
    -- 🔥 REPOST UNIVERSAL
    -- ===============================
    PERFORM sc_trx.sp_repost_universal(
        v_docno,
        v_doctype,
        v_inputby
    );

    -- ===============================
    -- CLEANUP TMP
    -- ===============================
    DELETE FROM sc_tmp.pmk_brng_mst WHERE TRIM(docno)=TRIM(OLD.docno);
    DELETE FROM sc_tmp.pmk_brng_dtl WHERE TRIM(docno)=TRIM(OLD.docno);

-- =====================================================
-- 🔥 REVISI FLOW
-- =====================================================
ELSIF OLD.status='E' AND NEW.status='F' AND COALESCE(NEW.docnotmp,'')<>'' THEN

    v_inputby := NEW.inputby;

    -- ===============================
    -- VALIDASI STOCK (REVISI)
    -- ===============================
    FOR v_row IN
        SELECT d.idbarang, d.idlocation, COALESCE(d.batch,'') batch,
               d.qty,
               COALESCE(a.qty,0) + COALESCE(old.qty,0) stock
        FROM sc_tmp.pmk_brng_dtl d
        LEFT JOIN sc_trx.stkblc_avgcost a
          ON TRIM(a.idbarang)=TRIM(d.idbarang)
         AND TRIM(a.idlocation)=TRIM(d.idlocation)
         AND TRIM(a.batch)=TRIM(COALESCE(d.batch,''))

        LEFT JOIN sc_trx.pmk_brng_dtl old
          ON TRIM(old.docno)=TRIM(NEW.docnotmp)
         AND TRIM(old.idbarang)=TRIM(d.idbarang)

        WHERE TRIM(d.docno)=TRIM(NEW.docno)
    LOOP
        IF v_row.qty > v_row.stock THEN
            RAISE EXCEPTION 'Stock tidak cukup (revisi): %', v_row.idbarang;
        END IF;
    END LOOP;

    -- ===============================
    -- INSERT DETAIL
    -- ===============================
    INSERT INTO sc_trx.pmk_brng_dtl
    (
        idurut, docno, docref, doctype, idbarang, nmbarang, unit, qtystock, qty,
        description, status, val, valsum, inputby, inputdate, updateby, updatedate,
        iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
    )
    SELECT
        idurut, NEW.docnotmp, docref, v_doctype, idbarang, nmbarang, unit, qtystock, qty,
        description, 'F', val, valsum, inputby, inputdate, updateby, updatedate,
        iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
    FROM sc_tmp.pmk_brng_dtl
    WHERE TRIM(docno)=TRIM(NEW.docno);

    -- ===============================
    -- INSERT HEADER
    -- ===============================
    INSERT INTO sc_trx.pmk_brng_mst
    (
        idurut, docno, doctype, docdate, docref, cabang, cabang_sent, pemohon,
        estpakai, idlocation_from, idlocation_to, idlocation_transit, status,
        description, inputby, inputdate, updateby, updatedate, printby, printdate, docnotmp
    )
    SELECT
        idurut, NEW.docnotmp, v_doctype, docdate, docref, cabang, cabang_sent, pemohon,
        estpakai, idlocation_from, idlocation_to, idlocation_transit, 'F',
        description, inputby, inputdate, updateby, updatedate, printby,
        printdate, docnotmp
    FROM sc_tmp.pmk_brng_mst
    WHERE TRIM(docno)=TRIM(NEW.docno);

    -- ===============================
    -- 🔥 REPOST UNIVERSAL
    -- ===============================
    PERFORM sc_trx.sp_repost_universal(
        NEW.docnotmp,
        v_doctype,
        v_inputby
    );

    -- ===============================
    -- CLEANUP TMP
    -- ===============================
    DELETE FROM sc_tmp.pmk_brng_mst WHERE TRIM(docno)=TRIM(NEW.docno);
    DELETE FROM sc_tmp.pmk_brng_dtl WHERE TRIM(docno)=TRIM(NEW.docno);

END IF;

RETURN NEW;
END;
$$;





-- DROP FUNCTION IF EXISTS sc_trx.tr_trx_pmk_brng_mst();

CREATE OR REPLACE FUNCTION sc_trx.tr_trx_pmk_brng_mst()
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
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_tmp.pmk_brng_dtl 
		(docno,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docnotmp,idurut,idcostcenter,batch,idlocation,idcoa)
        (SELECT new.updateby,docref,doctype,idbarang,nmbarang,unit,qtystock,qty,description,'F' AS status,val,valsum,inputby,inputdate,updateby,updatedate,iduniq,docno as docnotmp ,idurut,idcostcenter,batch,idlocation,idcoa
		FROM sc_trx.pmk_brng_dtl WHERE trim(docno) =  trim(new.docno));
		
        -- ===============================
        INSERT INTO sc_tmp.pmk_brng_mst (
            idurut, docno, doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit, status,
            description, inputby, inputdate, updateby, updatedate, printby, printdate, docnotmp
        )
        (SELECT idurut, new.updateby, doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit, 'E',
            description, inputby, inputdate, updateby, updatedate, printby, printdate, docno
         FROM sc_trx.pmk_brng_mst
         WHERE trim(docno) = trim(new.docno));



		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_trx_pmk_brng_mst()
    OWNER TO postgres;




    
CREATE OR REPLACE FUNCTION sc_trx.sp_rebuild_pmk(
    p_docno VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    -- ===============================
    -- 🔥 UNPOST DULU
    -- ===============================
    PERFORM sc_trx.sp_unpost_stk_pmk(p_docno);

    -- ===============================
    -- 🔥 INSERT KE STKBLC (OUT)
    -- ===============================
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
        qty_out,

        hist,
        ctype,

        pricelst_in,
        pricelst_out,

        currcode,
        currvalue,

        tax,
        disc,
        biaya,

        created_at,
        created_by,

        idgroup,
        grouptype,
        is_posted
    )
    SELECT
        d.idlocation,
        h.cabang,
        COALESCE(d.batch,''),
        d.idbarang,

        h.docdate::date + CURRENT_TIME,

        'PMKBRG',
        h.docno,
        h.docno,

        -- =====================
        -- QTY
        -- =====================
        0 AS qty_in,

        CASE 
            WHEN COALESCE(TRIM(b.grouptype),'STOCK') = 'NON STOCK' THEN 0
            ELSE COALESCE(d.qty,0)
        END AS qty_out,

        -- =====================
        -- HIST
        -- =====================
        'PEMAKAIAN',

        CASE 
            WHEN COALESCE(TRIM(b.grouptype),'STOCK') = 'NON STOCK' THEN 'NON'
            ELSE 'OUT'
        END,

        -- =====================
        -- PRICE
        -- =====================
        0,
        COALESCE(d.val,0),

        -- =====================
        -- CURRENCY
        -- =====================
        'IDR',
        1,

        -- =====================
        -- BIAYA
        -- =====================
        0,0,0,

        NOW(),
        h.inputby,

        b.idgroup,
        COALESCE(TRIM(b.grouptype),'STOCK'),

        FALSE

    FROM sc_trx.pmk_brng_dtl d
    JOIN sc_trx.pmk_brng_mst h 
      ON TRIM(h.docno) = TRIM(d.docno)

    LEFT JOIN sc_mst.mbarang b
      ON TRIM(b.idbarang) = TRIM(d.idbarang)

    WHERE TRIM(d.docno) = TRIM(p_docno);

    -- ===============================
    -- 🔥 POST GL
    -- ===============================
    PERFORM sc_trx.sp_post_gl('SYSTEM');

END;
$$;


CREATE OR REPLACE FUNCTION sc_trx.sp_unpost_stk_pmk(
    p_docno VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
BEGIN

    DELETE FROM sc_trx.jurnal_dt
    WHERE jurnal_id IN (
        SELECT id 
        FROM sc_trx.jurnal_hd
        WHERE TRIM(docno) = TRIM(p_docno)
          AND doctype = 'PMKBRG'
    );

    DELETE FROM sc_trx.jurnal_hd
    WHERE TRIM(docno) = TRIM(p_docno)
      AND doctype = 'PMKBRG';

    DELETE FROM sc_trx.stkblc
    WHERE TRIM(docno) = TRIM(p_docno)
      AND doctype = 'PMKBRG';

END;
$$;


-- ============================================================
-- TRIGGER
-- ============================================================

DROP TRIGGER IF EXISTS tr_tmp_pmk_brng_mst ON sc_tmp.pmk_brng_mst;

CREATE TRIGGER tr_tmp_pmk_brng_mst
    AFTER UPDATE ON sc_tmp.pmk_brng_mst
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_tmp_pmk_brng_mst();

DROP TRIGGER IF EXISTS tr_trx_pmk_brng_mst ON sc_trx.pmk_brng_mst;

CREATE TRIGGER tr_trx_pmk_brng_mst
    AFTER UPDATE ON sc_trx.pmk_brng_mst
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_trx_pmk_brng_mst();

COMMIT;

-- ============================================================
-- SELESAI
-- ============================================================
