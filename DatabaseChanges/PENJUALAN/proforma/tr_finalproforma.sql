BEGIN;

-- =========================================================
-- FUNCTION: sc_tmp.tr_proforma_finalize()
-- =========================================================

CREATE OR REPLACE FUNCTION sc_tmp.tr_proforma_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_idurut    INTEGER;
    v_prefix    TEXT;
    v_num       TEXT;
    v_suffix    TEXT;
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
        -- CEK DOCNO DUPLICATE
        -- =================================================

        LOOP

            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.proforma
                WHERE RTRIM(docno) = RTRIM(v_docno)
            );


            -- =============================================
            -- PREFIX
            -- Contoh:
            -- PF/2609/0001/ABC
            -- Prefix = PF/2609/
            -- =============================================

            v_prefix := substring(
                v_docno
                FROM '^(.*?/)[0-9]{4}/'
            );


            -- =============================================
            -- NOMOR 4 DIGIT
            -- =============================================

            v_num := substring(
                v_docno
                FROM '/([0-9]{4})/'
            );


            -- =============================================
            -- SUFFIX
            -- =============================================

            v_suffix := substring(
                v_docno
                FROM '/[0-9]{4}(/.*)$'
            );


            -- =============================================
            -- VALIDASI FORMAT DOCNO
            -- =============================================

            IF COALESCE(v_prefix, '') = ''
               OR COALESCE(v_num, '') = ''
            THEN

                RAISE EXCEPTION
                    'Format DOCNO PROFORMA tidak valid: %',
                    v_docno;

            END IF;


            -- =============================================
            -- INCREMENT NOMOR
            -- =============================================

            v_num_int := v_num::INTEGER + 1;


            -- =============================================
            -- GABUNG KEMBALI
            -- =============================================

            v_docno :=
                v_prefix
                ||
                LPAD(
                    v_num_int::TEXT,
                    4,
                    '0'
                )
                ||
                COALESCE(v_suffix, '');

        END LOOP;


        -- =================================================
        -- INSERT HEADER KE TRANSAKSI
        -- =================================================

        INSERT INTO sc_trx.proforma
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
            accno,
            accname,
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
            accno,
            accname,
            swiftcode,
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
        FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;


        -- =================================================
        -- INSERT DETAIL KE TRANSAKSI
        -- =================================================

        INSERT INTO sc_trx.proformadtl
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
            amount,
            description,
            inputby,
            inputdate,
            status,
            updateby,
            updatedate
        FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby;


        -- =================================================
        -- CLEANUP TEMP
        -- =================================================

        DELETE FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;


        DELETE FROM sc_tmp.proformadtl
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

        DELETE FROM sc_trx.proforma
        WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);


        DELETE FROM sc_trx.proformadtl
        WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);


        -- ================================================
        -- INSERT DETAIL
        -- ================================================

        INSERT INTO sc_trx.proformadtl
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
            NEW.docnotmp,
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
        FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- ================================================
        -- INSERT HEADER
        -- ================================================

        INSERT INTO sc_trx.proforma
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
            accno,
            accname,
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
            accno,
            accname,
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
            updateby,
            updatedate,
            printby,
            printdate,
            rolejob,
            docnotmp
        FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        -- ================================================
        -- CLEANUP TEMP
        -- ================================================

        DELETE FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        DELETE FROM sc_tmp.proformadtl
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

            UPDATE sc_trx.proforma
            SET status = 'P'
            WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);

        ELSE

            UPDATE sc_trx.proforma
            SET status = 'F'
            WHERE RTRIM(docno) = RTRIM(NEW.docnotmp);

        END IF;


        -- ================================================
        -- CLEANUP TEMP
        -- ================================================

        DELETE FROM sc_tmp.proforma
        WHERE RTRIM(docno) = RTRIM(NEW.docno);


        DELETE FROM sc_tmp.proformadtl
        WHERE RTRIM(docno) = RTRIM(NEW.docno);

    END IF;


    RETURN NEW;

END;
$BODY$;


-- =========================================================
-- OWNER FUNCTION
-- =========================================================

ALTER FUNCTION sc_tmp.tr_proforma_finalize()
    OWNER TO postgres;


-- =========================================================
-- TRIGGER
-- =========================================================

DROP TRIGGER IF EXISTS tr_proforma_finalize
ON sc_tmp.proforma;


CREATE TRIGGER tr_proforma_finalize
    AFTER UPDATE
    ON sc_tmp.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_proforma_finalize();


COMMIT;