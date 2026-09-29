BEGIN;

-- =========================================================
-- FUNCTION: sc_tmp.tr_soi_finalize()
-- =========================================================

CREATE OR REPLACE FUNCTION sc_tmp.tr_soi_finalize()
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
    -- FINALISASI E -> F
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
                FROM sc_trx.soi
                WHERE RTRIM(docno) = RTRIM(v_docno)
            );


            -- Ambil prefix
            v_prefix := regexp_replace(
                v_docno,
                '[0-9]{6}$',
                ''
            );


            -- Ambil nomor 6 digit terakhir
            v_num := substring(
                v_docno
                FROM '([0-9]{6})$'
            );


            -- Validasi format
            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO SOI tidak valid: %',
                    v_docno;
            END IF;


            -- Increment
            v_num_int := v_num::INTEGER + 1;


            v_docno :=
                v_prefix ||
                LPAD(
                    v_num_int::TEXT,
                    6,
                    '0'
                );

        END LOOP;


        -- =================================================
        -- INSERT HEADER SOI
        -- =================================================

        INSERT INTO sc_trx.soi
        (
            idurut,
            docno,
            docdate,
            cust,
            po,
            pocust,
            revno,
            description,
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
            docdate,
            cust,
            po,
            pocust,
            revno,
            description,
            'F',
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob
        FROM sc_tmp.soi
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;


        -- =================================================
        -- INSERT DETAIL SOI
        -- =================================================

        INSERT INTO sc_trx.soidtl
        (
            idurut,
            docno,
            idbarang,
            cust,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            grade,
            size,
            cutlength,
            totaldelivery,
            balanceorder,
            specno,
            ordernumbermsr,
            etd,
            usdmt,
            amount,
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
            cust,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            grade,
            size,
            cutlength,
            totaldelivery,
            balanceorder,
            specno,
            ordernumbermsr,
            etd,
            usdmt,
            amount,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate
        FROM sc_tmp.soidtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby;


        -- =================================================
        -- CLEANUP TEMP
        -- =================================================

        DELETE FROM sc_tmp.soi
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;


        DELETE FROM sc_tmp.soidtl
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
        -- DELETE DATA LAMA
        -- ================================================

        DELETE FROM sc_trx.soi
        WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);


        DELETE FROM sc_trx.soidtl
        WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);


        -- ================================================
        -- INSERT DETAIL DENGAN DOCNOTMP
        -- ================================================

        INSERT INTO sc_trx.soidtl
        (
            idurut,
            docno,
            idbarang,
            cust,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            grade,
            size,
            cutlength,
            totaldelivery,
            balanceorder,
            specno,
            ordernumbermsr,
            etd,
            usdmt,
            amount,
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
            cust,
            nmbarang,
            unit,
            qty,
            price,
            exchange,
            grade,
            size,
            cutlength,
            totaldelivery,
            balanceorder,
            specno,
            ordernumbermsr,
            etd,
            usdmt,
            amount,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate,
            docnotmp
        FROM sc_tmp.soidtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- ================================================
        -- INSERT HEADER DENGAN DOCNOTMP
        -- ================================================

        INSERT INTO sc_trx.soi
        (
            idurut,
            docno,
            docdate,
            cust,
            po,
            pocust,
            revno,
            description,
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
            docdate,
            cust,
            po,
            pocust,
            revno,
            description,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob,
            docnotmp
        FROM sc_tmp.soi
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- ================================================
        -- CLEANUP TEMP
        -- ================================================

        DELETE FROM sc_tmp.soi
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        DELETE FROM sc_tmp.soidtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


    -- =====================================================
    -- CANCEL E -> C
    -- =====================================================

    ELSIF OLD.status = 'E'
          AND NEW.status = 'C'
    THEN

        IF NEW.printby IS NOT NULL
           AND NEW.printby <> ''
           AND NEW.printdate IS NOT NULL
        THEN

            UPDATE sc_trx.soi
            SET status = 'P'
            WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);

        ELSE

            UPDATE sc_trx.soi
            SET status = 'F'
            WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);

        END IF;


        -- ================================================
        -- CLEANUP TEMP
        -- ================================================

        DELETE FROM sc_tmp.soi
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        DELETE FROM sc_tmp.soidtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;


-- =========================================================
-- OWNER FUNCTION
-- =========================================================

ALTER FUNCTION sc_tmp.tr_soi_finalize()
    OWNER TO postgres;


-- =========================================================
-- TRIGGER
-- =========================================================

DROP TRIGGER IF EXISTS tr_soi_finalize
ON sc_tmp.soi;


CREATE TRIGGER tr_soi_finalize
    AFTER UPDATE
    ON sc_tmp.soi
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_soi_finalize();


COMMIT;