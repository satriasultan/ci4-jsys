BEGIN;

-- =========================================================
-- FUNCTION: sc_tmp.tr_offering_finalize()
-- =========================================================

CREATE OR REPLACE FUNCTION sc_tmp.tr_offering_finalize()
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
BEGIN

    -- =====================================================
    -- FINALISASI: E -> F
    -- DOCNOTMP KOSONG
    -- =====================================================

    IF OLD.status = 'E'
       AND NEW.status = 'F'
       AND COALESCE(NEW.docnotmp, '') = ''
    THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;


        -- =================================================
        -- CEK DUPLICATE DOCNO
        -- =================================================

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.offering
                WHERE RTRIM(docno) = RTRIM(v_docno)
            );


            -- Ambil prefix
            v_prefix := regexp_replace(
                v_docno,
                '[0-9]{6}$',
                ''
            );


            -- Ambil 6 digit terakhir
            v_num := substring(
                v_docno
                FROM '([0-9]{6})$'
            );


            -- Kalau format tidak valid
            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO OFFERING tidak valid: %',
                    v_docno;
            END IF;


            v_num_int := v_num::INTEGER + 1;


            v_docno :=
                v_prefix ||
                lpad(
                    v_num_int::TEXT,
                    6,
                    '0'
                );

        END LOOP;


        -- =================================================
        -- INSERT HEADER
        -- =================================================

        INSERT INTO sc_trx.offering
        (
            idurut,
            docno,
            cust,
            address,
            docdate,
            phone,
            fax,
            up,
            description,
            brand,
            size,
            qty,
            pembayaran,
            pengiriman,
            expdate,
            ketentuan,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob
        )
        SELECT
            idurut,
            v_docno,
            cust,
            address,
            docdate,
            phone,
            fax,
            up,
            description,
            brand,
            size,
            qty,
            pembayaran,
            pengiriman,
            expdate,
            ketentuan,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob
        FROM sc_tmp.offering
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;


        -- =================================================
        -- INSERT DETAIL
        -- =================================================

        INSERT INTO sc_trx.offeringdtl
        (
            idurut,
            docno,
            idbarang,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            usdmt,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate
        )
        SELECT
            idurut,
            v_docno,
            idbarang,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            usdmt,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate
        FROM sc_tmp.offeringdtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby;


        -- =================================================
        -- CLEANUP TEMP
        -- =================================================

        DELETE FROM sc_tmp.offering
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;


        DELETE FROM sc_tmp.offeringdtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby;


    -- =====================================================
    -- DOCNOTMP FLOW
    -- =====================================================

    ELSIF OLD.status = 'E'
          AND NEW.status = 'F'
          AND COALESCE(NEW.docnotmp, '') <> ''
    THEN

        -- ================================================
        -- HAPUS DATA LAMA
        -- ================================================

        DELETE FROM sc_trx.offering
        WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);


        DELETE FROM sc_trx.offeringdtl
        WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);


        -- ================================================
        -- INSERT DETAIL
        -- ================================================

        INSERT INTO sc_trx.offeringdtl
        (
            idurut,
            docno,
            idbarang,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            usdmt,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate,
            docnotmp
        )
        SELECT
            idurut,
            NEW.docnotmp,
            idbarang,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            usdmt,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate,
            docnotmp
        FROM sc_tmp.offeringdtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- ================================================
        -- INSERT HEADER
        -- ================================================

        INSERT INTO sc_trx.offering
        (
            idurut,
            docno,
            cust,
            address,
            docdate,
            phone,
            fax,
            up,
            description,
            brand,
            size,
            qty,
            pembayaran,
            pengiriman,
            expdate,
            ketentuan,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob,
            docnotmp
        )
        SELECT
            idurut,
            NEW.docnotmp,
            cust,
            address,
            docdate,
            phone,
            fax,
            up,
            description,
            brand,
            size,
            qty,
            pembayaran,
            pengiriman,
            expdate,
            ketentuan,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob,
            docnotmp
        FROM sc_tmp.offering
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- ================================================
        -- CLEANUP TEMP
        -- ================================================

        DELETE FROM sc_tmp.offering
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        DELETE FROM sc_tmp.offeringdtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


    -- =====================================================
    -- CANCEL / C
    -- =====================================================

    ELSIF OLD.status = 'E'
          AND NEW.status = 'C'
    THEN

        IF NEW.printby IS NOT NULL
           AND NEW.printby <> ''
           AND NEW.printdate IS NOT NULL
        THEN

            UPDATE sc_trx.offering
            SET status = 'P'
            WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);

        ELSE

            UPDATE sc_trx.offering
            SET status = 'F'
            WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);

        END IF;


        -- ================================================
        -- CLEANUP TEMP
        -- ================================================

        DELETE FROM sc_tmp.offering
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        DELETE FROM sc_tmp.offeringdtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;


-- =========================================================
-- OWNER
-- =========================================================

ALTER FUNCTION sc_tmp.tr_offering_finalize()
    OWNER TO postgres;


-- =========================================================
-- TRIGGER
-- =========================================================

DROP TRIGGER IF EXISTS tr_offering_finalize
ON sc_tmp.offering;


CREATE TRIGGER tr_offering_finalize
    AFTER UPDATE
    ON sc_tmp.offering
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_offering_finalize();


COMMIT;