
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.woe
DROP TABLE IF EXISTS sc_trx.woe


--I.R.A.5
--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.R.A.5';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
VALUES
('I','I.R.A.5','DRAFT'),
('E','I.R.A.5','REVISI/EDIT'),
('F','I.R.A.5','FINAL USER'),
('A','I.R.A.5','APPROVE'),
('A2','I.R.A.5','APPROVE 2'),
('A3','I.R.A.5','APPROVE 3'),
('P','I.R.A.5','CETAK/PRINT'),
('O','I.R.A.5','OBSOLATE'),
('C','I.R.A.5','CANCEL'),
('D','I.R.A.5','DELETE');



CREATE TABLE IF NOT EXISTS sc_tmp.woe
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    bagian character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi text,
    buildfor numeric(18,2),
    batchno character(50),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_woe PRIMARY KEY (docno, idurut)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.woe
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.woe
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    -- senddate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    bagian character(30) COLLATE pg_catalog."default",
    wono character(30) COLLATE pg_catalog."default",
    bomno character(30) COLLATE pg_catalog."default",
    desc_bom TEXT,
    idbarang_jadi character(50) COLLATE pg_catalog."default",
    nmbarang_jadi text,
    buildfor numeric(18,2),
    batchno character(50),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_woe PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.woe
    OWNER to postgres;







-- FUNCTION: sc_tmp.tr_woe_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_woe_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_woe_finalize()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno        TEXT;
    v_inputby      TEXT;
    v_idurut       INTEGER;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      INTEGER;
    v_lock_key     BIGINT;
    v_client_ip    TEXT;

BEGIN

    -- ===============================
    -- GUARD: hanya proses kalau status berubah
    -- ===============================
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

    -- =====================================================
    -- FINAL INSERT (tmp kosong / docnotmp kosong)
    -- =====================================================
    IF UPPER(TRIM(OLD.status)) = 'E'
       AND UPPER(TRIM(NEW.status)) = 'F'
       AND COALESCE(TRIM(NEW.docnotmp), '') = '' THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        -- ================================================
        -- DETEKSI DUPLIKAT (cegah double insert)
        -- ================================================
        IF EXISTS (
            SELECT 1
            FROM sc_trx.woe
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) = TRIM(COALESCE(v_inputby, ''))
        ) THEN

            DELETE FROM sc_tmp.woe
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND inputby = v_inputby
              AND idurut = v_idurut;

            RETURN NEW;
        END IF;

        -- ================================================
        -- GENERATE DOCNO (advisory lock + loop increment)
        -- ================================================
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.woe
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO WOE tidak valid: %',
                    v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        -- ================================================
        -- INSERT HEADER
        -- ================================================
        INSERT INTO sc_trx.woe (
            idurut, docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, batchno,
            status, keterangan,
            inputby, inputdate, updateby, updatedate,
            printby, printdate
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, batchno,
            'F', keterangan,
            inputby, inputdate, updateby, updatedate,
            printby, printdate
        FROM sc_tmp.woe
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        -- ================================================
        -- LOG: INSERT HEADER WOE
        -- ================================================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.R',                  -- kode module (sesuaikan)
            'I.R.A.5',              -- kode menu WOE (sesuaikan)
            'I',                    -- action: INPUT
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.woe
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- =====================================================
    -- FINAL EDIT (docnotmp terisi)
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
          AND UPPER(TRIM(NEW.status)) = 'F'
          AND COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN

        v_inputby := NEW.inputby;

        -- ================================================
        -- DELETE OLD TRX
        -- ================================================
        DELETE FROM sc_trx.woe
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        -- ================================================
        -- INSERT HEADER (pakai docnotmp)
        -- ================================================
        INSERT INTO sc_trx.woe (
            idurut, docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, batchno,
            status, keterangan,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp
        )
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, batchno,
            'F', keterangan,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp
        FROM sc_tmp.woe
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- ================================================
        -- LOG: UPDATE HEADER WOE
        -- ================================================
        PERFORM sc_log.fn_log_transaction(
            NEW.docnotmp::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.5',
            'U',                    -- action: UPDATE
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.woe
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

    -- =====================================================
    -- CANCEL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
        AND UPPER(TRIM(NEW.status)) = 'C' THEN

        -- ================================================
        -- KEMBALIKAN STATUS TRX
        -- ================================================
        IF COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN
            IF NEW.printby IS NOT NULL
               AND NEW.printby <> ''
               AND NEW.printdate IS NOT NULL THEN
                UPDATE sc_trx.woe
                SET status = 'P'
                WHERE TRIM(docno) = TRIM(NEW.docnotmp);
            ELSE
                UPDATE sc_trx.woe
                SET status = 'F'
                WHERE TRIM(docno) = TRIM(NEW.docnotmp);
            END IF;
        END IF;

        -- ================================================
        -- CLEANUP TMP
        -- ================================================
        DELETE FROM sc_tmp.woe
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_tmp.tr_woe_finalize()
    OWNER TO postgres;

-- =========================================
-- TRIGGER
-- =========================================
DROP TRIGGER IF EXISTS tr_woe_finalize
ON sc_tmp.woe;

CREATE TRIGGER tr_woe_finalize
AFTER UPDATE
ON sc_tmp.woe
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_woe_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_woe();
CREATE OR REPLACE FUNCTION sc_trx.tr_woe()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
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
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_docno     := RTRIM(NEW.docno);
    v_inputby   := NEW.inputby;
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ===============================
    -- ADVISORY LOCK (CEGAH RACE CONDITION)
    -- ===============================
    PERFORM pg_advisory_xact_lock(hashtext(NEW.docno));

    -- =====================================================
    -- WOE DIBATALKAN (F -> C)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN

        -- ===============================
        -- LOG: CANCEL WOE
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.R',                  -- kode module (sesuaikan)
            'I.R.A.5',              -- kode menu WOE (sesuaikan)
            'C',                    -- action: CANCEL
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

    END IF;

    -- =====================================================
    -- WOE DIEDIT (F -> E)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'E') THEN

        -- =================================================
        -- INSERT TMP MASTER
        -- =================================================
        INSERT INTO sc_tmp.woe (
            idurut, docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, batchno,
            status, keterangan,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp
        )
        SELECT
            idurut, docno, cabang, docdate, pemohon, bagian,
            wono, bomno, desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, batchno,
            'E', keterangan,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docno
        FROM sc_trx.woe
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_woe()
    OWNER TO postgres;

-- =========================================
-- TRIGGER
-- =========================================
DROP TRIGGER IF EXISTS tr_woe
ON sc_trx.woe;

CREATE TRIGGER tr_woe
AFTER UPDATE
ON sc_trx.woe
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_woe();




