--I.R.A.8
BEGIN;
--materialrelease.sql
-- Pastikan schema utama tersedia. Fungsi sc_log tetap harus sudah ada.
CREATE SCHEMA IF NOT EXISTS sc_mst;
CREATE SCHEMA IF NOT EXISTS sc_tmp;
CREATE SCHEMA IF NOT EXISTS sc_trx;

-- Reset definisi status khusus Material Release agar script bisa dijalankan ulang.
DELETE FROM sc_mst.trxtype WHERE jenistrx = 'I.R.A.8';

INSERT INTO sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.R.A.8','DRAFT'),
('E','I.R.A.8','REVISI/EDIT'),
('F','I.R.A.8','FINAL USER'),
('A','I.R.A.8','APPROVE'),
('A2','I.R.A.8','APPROVE 2'),
('A3','I.R.A.8','APPROVE 3'),
('P','I.R.A.8','CETAK/PRINT'),
('O','I.R.A.8','OBSOLATE'),
('C','I.R.A.8','CANCEL'),
('D','I.R.A.8','DELETE');




--drop table sc_tmp.materialrelease_mst;
CREATE TABLE IF NOT EXISTS sc_tmp.materialrelease_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    idurut bigserial,
    doctype character(20) default 'BOM' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idlocation character(12) COLLATE pg_catalog."default",
    nmlocation character(50) COLLATE pg_catalog."default",
    tabno character(50) COLLATE pg_catalog."default",
    woeno character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi TEXT,
    buildfor numeric(18,2),
    buildunit character(30),
    batchno character(50),
    bagian character(30) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_materialrelease_mst PRIMARY KEY (docno, idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.materialrelease_mst
    OWNER to postgres;


/** 'MATERIAL, COST, WIP' **/
--drop table sc_tmp.materialrelease_dtl;
CREATE TABLE IF NOT EXISTS sc_tmp.materialrelease_dtl
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    idurut bigserial,
    doctype character(20) default 'BOM' ,
    doctype_detail character(20) default 'MATERIAL' ,
    cabang character (30 ) COLLATE pg_catalog."default",
    docref character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    idbarang_bom character(50) COLLATE pg_catalog."default",
    nmbarang_bom text,
    description text,
	qty numeric(18,2),
    unit char(20),
	spec char(100),
    issub character(6),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    uniqueid text,
    CONSTRAINT pk_tmp_materialrelease_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.materialrelease_dtl
    OWNER to postgres;


/* MST ATAU TRANSAKSI */

--drop table sc_trx.materialrelease_mst;
CREATE TABLE IF NOT EXISTS sc_trx.materialrelease_mst
(
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'BOM' ,
	cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idlocation character(12) COLLATE pg_catalog."default",
    nmlocation character(50) COLLATE pg_catalog."default",
    tabno character(50) COLLATE pg_catalog."default",
    woeno character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi TEXT,
    buildfor numeric(18,2),
    buildunit character(30),
    batchno character(50),
    bagian character(30) COLLATE pg_catalog."default",
    docref character(30) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_materialrelease_mst PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.materialrelease_mst
    OWNER to postgres;


--drop table sc_trx.materialrelease_dtl;
CREATE TABLE IF NOT EXISTS sc_trx.materialrelease_dtl
(
	docno character(30) COLLATE pg_catalog."default" NOT NULL,
    doctype character(20) default 'BOM' ,
    doctype_detail character(20) default 'MATERIAL' ,
    cabang character (30 ) COLLATE pg_catalog."default",
    docref character (30 ) COLLATE pg_catalog."default",        
    pemohon character(100) COLLATE pg_catalog."default",
    docdate date,
    idbarang character(50) COLLATE pg_catalog."default",
    nmbarang text,
    idbarang_bom character(50) COLLATE pg_catalog."default",
    nmbarang_bom text,
    description text,
	qty numeric(18,2),
    unit char(20),
	spec char(100),
    issub character(6),
	status character(6) COLLATE pg_catalog."default",
	inputby character(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
	idurut integer,
	uniqueid text,
    CONSTRAINT pk_tmp_materialrelease_dtl PRIMARY KEY (docno,uniqueid,idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.materialrelease_dtl
    OWNER to postgres;


-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_materialrelease_mst
ON sc_tmp.materialrelease_mst;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_materialrelease_mst();

-- =========================================
-- FUNCTION TMP -> TRX
-- =========================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_materialrelease_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno        TEXT;
    v_inputby      TEXT;
    v_idurut       BIGINT;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      BIGINT;
    v_lock_key     BIGINT;
    v_client_ip    TEXT;
BEGIN

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

    -- ===============================
    -- FINAL INSERT (tmp kosong)
    -- ===============================
    IF UPPER(TRIM(OLD.status)) = 'E' AND UPPER(TRIM(NEW.status)) = 'F' AND COALESCE(TRIM(NEW.docnotmp), '') = '' THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        -- ================================================
        -- GENERATE DOCNO
        -- ================================================
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.materialrelease_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            IF v_new_docno !~ '[0-9]+$' THEN
                RAISE EXCEPTION 'Format DOCNO Material Release harus diakhiri angka: %', v_new_docno;
            END IF;

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\\1');
            v_num_int := v_num::BIGINT + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(
                    v_num_int::TEXT,
                    GREATEST(length(v_num), length(v_num_int::TEXT)),
                    '0'
                );
        END LOOP;

        v_docno := v_new_docno;

        -- ================================================
        -- INSERT HEADER
        -- ================================================
        INSERT INTO sc_trx.materialrelease_mst (
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            docref, status, keterangan, batchno, buildfor, buildunit,
            bagian, inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            v_docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            docref, 'F', keterangan, batchno, buildfor, buildunit,
            bagian, inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut  = v_idurut;

        -- ================================================
        -- INSERT DETAIL
        -- ================================================
        INSERT INTO sc_trx.materialrelease_dtl (
            docno, doctype, doctype_detail, cabang, pemohon, docdate,
            docref, idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, spec, issub, status,
            inputby, inputdate, updateby, updatedate, docnotmp,
            idurut, uniqueid
        )
        SELECT
            v_docno, doctype, doctype_detail, cabang, pemohon, docdate,
            docref, idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, spec, issub, 'F',
            inputby, inputdate, updateby, updatedate, docnotmp,
            idurut, uniqueid
        FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- LOG: INSERT HEADER
        -- ================================================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.R',                 
            'I.R.A.8',             
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- ================================================
    -- FINAL EDIT (docnotmp terisi)
    -- ================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
          AND UPPER(TRIM(NEW.status)) = 'F'
          AND COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN

        v_inputby := NEW.inputby;

        -- DELETE OLD TRX
        DELETE FROM sc_trx.materialrelease_dtl
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.materialrelease_mst
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        -- INSERT MASTER (pakai docnotmp = docno lama)
        INSERT INTO sc_trx.materialrelease_mst (
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            docref, status, keterangan, batchno, buildfor, buildunit,
            bagian, inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            NEW.docnotmp, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            docref, 'F', keterangan, batchno, buildfor, buildunit,
            bagian, inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- INSERT DETAIL
        INSERT INTO sc_trx.materialrelease_dtl (
            docno, doctype, doctype_detail, cabang, pemohon, docdate,
            docref, idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, spec, issub, status,
            inputby, inputdate, updateby, updatedate, docnotmp,
            idurut, uniqueid
        )
        SELECT
            NEW.docnotmp, doctype, doctype_detail, cabang, pemohon, docdate,
            docref, idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, spec, issub, 'F',
            inputby, inputdate, updateby, updatedate, docnotmp,
            idurut, uniqueid
        FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- LOG: UPDATE HEADER
        -- ================================================
        PERFORM sc_log.fn_log_transaction(
            NEW.docnotmp::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.8',
            'U',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- CLEANUP TMP
        DELETE FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

    -- ================================================
    -- CANCEL EDIT
    -- ================================================
    ELSIF UPPER(TRIM(OLD.status))='E'
        AND UPPER(TRIM(NEW.status))='C' THEN

        UPDATE sc_trx.materialrelease_mst
        SET status='F'
        WHERE TRIM(docno)=TRIM(NEW.docnotmp);

        DELETE FROM sc_tmp.materialrelease_dtl
        WHERE TRIM(docno)=TRIM(NEW.docno);

        DELETE FROM sc_tmp.materialrelease_mst
        WHERE TRIM(docno)=TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_tmp_materialrelease_mst
AFTER UPDATE
ON sc_tmp.materialrelease_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_materialrelease_mst();



-- =========================================
-- DROP TRIGGER & FUNCTION
-- =========================================
DROP TRIGGER IF EXISTS tr_mst_materialrelease_mst
ON sc_trx.materialrelease_mst;

DROP FUNCTION IF EXISTS sc_trx.tr_mst_materialrelease_mst();

-- =========================================
-- FUNCTION TRX -> TMP
-- =========================================
CREATE OR REPLACE FUNCTION sc_trx.tr_mst_materialrelease_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$

DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_client_ip TEXT;

BEGIN

    -- ===============================
    -- GUARD: hanya proses kalau status berubah
    -- ===============================
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- ===============================
    -- AMBIL IP
    -- ===============================
    v_docno     := RTRIM(NEW.docno);
    v_inputby   := COALESCE(NEW.updateby, NEW.inputby);
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ===============================
    -- ADVISORY LOCK
    -- ===============================
    PERFORM pg_advisory_xact_lock(hashtext(v_docno));

    -- =====================================================
    -- MATERIAL RELEASE DIBATALKAN (F -> C)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.R',
            'I.R.A.8',
            'C',
            v_inputby,
            v_client_ip,
            v_inputby
        );

    END IF;

    -- =====================================================
    -- MATERIAL RELEASE DIEDIT (F -> E)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'E') THEN

        -- INSERT TMP MASTER
        INSERT INTO sc_tmp.materialrelease_mst (
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            docref, status, keterangan, batchno, buildfor, buildunit,
            bagian, inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            docref, 'E', keterangan, batchno, buildfor, buildunit,
            bagian, inputby, inputdate, updateby, updatedate, v_docno
        FROM sc_trx.materialrelease_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

        -- INSERT TMP DETAIL
        INSERT INTO sc_tmp.materialrelease_dtl (
            docno, doctype, doctype_detail, cabang, pemohon, docdate,
            docref, idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, spec, issub, status,
            inputby, inputdate, updateby, updatedate, docnotmp,
            uniqueid
        )
        SELECT
            docno, doctype, doctype_detail, cabang, pemohon, docdate,
            docref, idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, spec, issub, 'E',
            inputby, inputdate, updateby, updatedate, docno,
            uniqueid
        FROM sc_trx.materialrelease_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_mst_materialrelease_mst()
    OWNER TO postgres;

-- =========================================
-- TRIGGER
-- =========================================
CREATE TRIGGER tr_mst_materialrelease_mst
AFTER UPDATE
ON sc_trx.materialrelease_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_mst_materialrelease_mst();

COMMIT;
