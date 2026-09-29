BEGIN;

-- =========================================================
-- FUNCTION: sc_trx.soi()
-- =========================================================

CREATE OR REPLACE FUNCTION sc_trx.soi()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE
    vr_nomor     CHAR(15);
    vr_cekprefix CHAR(15);
    vr_nowprefix CHAR(15);
    vr_id_dtl    NUMERIC;
    vr_lastdoc   NUMERIC(18);
BEGIN

    -- =====================================================
    -- F -> E
    -- Copy Sales Order ke temporary untuk proses edit SOI
    -- =====================================================

    IF OLD.status = 'F'
       AND NEW.status = 'E'
    THEN

        -- =================================================
        -- INSERT DETAIL
        -- =================================================

        INSERT INTO sc_tmp.salesorderdtl
        (
            idurut,
            docno,
            idbarang,
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
            NEW.docno,
            idbarang,
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
            etd,
            usdmt,
            amount,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate,
            NEW.docno
        FROM sc_trx.salesorderdtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- =================================================
        -- INSERT HEADER
        -- =================================================

        INSERT INTO sc_tmp.salesorder
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
            rolejob,
            docnotmp
        )
        SELECT
            idurut,
            NEW.docno,
            docdate,
            cust,
            po,
            pocust,
            revno,
            description,
            status,
            inputby,
            inputdate,
            rolejob,
            NEW.docno
        FROM sc_trx.salesorder
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;


-- =========================================================
-- OWNER FUNCTION
-- =========================================================

ALTER FUNCTION sc_trx.soi()
    OWNER TO postgres;


-- =========================================================
-- TRIGGER
-- =========================================================

DROP TRIGGER IF EXISTS soi
ON sc_trx.salesorder;


CREATE TRIGGER soi
    AFTER UPDATE
    ON sc_trx.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.soi();


COMMIT;