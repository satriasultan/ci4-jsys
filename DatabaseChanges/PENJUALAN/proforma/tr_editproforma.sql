BEGIN;

-- =========================================================
-- FUNCTION: sc_trx.tr_proforma()
-- =========================================================

CREATE OR REPLACE FUNCTION sc_trx.tr_proforma()
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
    -- Copy data transaksi ke temporary untuk proses edit
    -- =====================================================

    IF OLD.status = 'F'
       AND NEW.status = 'E'
    THEN

        -- =================================================
        -- INSERT DETAIL KE TEMP
        -- =================================================

        INSERT INTO sc_tmp.proformadtl
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
            usdmt,
            amount,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate,
            NEW.docno
        FROM sc_trx.proformadtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- =================================================
        -- INSERT HEADER KE TEMP
        -- =================================================

        INSERT INTO sc_tmp.proforma
        (
            idurut,
            docno,
            docdate,
            pono,
            podate,
            jnsinvoice,
            cust,
            address,
            phone,
            fax,
            facrisk,
            shipper,
            consignee,
            shippingmark,
            notifyparty,
            paymentmethod,
            bank,
            grosssales,
            downpayment,
            netsales,
            taxbasis,
            vat,
            pph22,
            ttlprice,
            nmbank,
            alamatbank,
            accname,
            accno,
            swiftcode,
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
            rolejob,
            docnotmp
        )
        SELECT
            idurut,
            NEW.docno,
            docdate,
            pono,
            podate,
            jnsinvoice,
            cust,
            address,
            phone,
            fax,
            facrisk,
            shipper,
            consignee,
            shippingmark,
            notifyparty,
            paymentmethod,
            bank,
            grosssales,
            downpayment,
            netsales,
            taxbasis,
            vat,
            pph22,
            ttlprice,
            nmbank,
            alamatbank,
            accname,
            accno,
            swiftcode,
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
            rolejob,
            NEW.docno
        FROM sc_trx.proforma
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;


-- =========================================================
-- OWNER FUNCTION
-- =========================================================

ALTER FUNCTION sc_trx.tr_proforma()
    OWNER TO postgres;


-- =========================================================
-- TRIGGER
-- =========================================================

DROP TRIGGER IF EXISTS tr_proforma
ON sc_trx.proforma;


CREATE TRIGGER tr_proforma
    AFTER UPDATE
    ON sc_trx.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_proforma();


COMMIT;