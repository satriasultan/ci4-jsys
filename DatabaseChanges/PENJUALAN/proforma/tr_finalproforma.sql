CREATE TRIGGER tr_proforma_finalize
    AFTER UPDATE ON sc_tmp.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_proforma_finalize();


CREATE OR REPLACE FUNCTION sc_tmp.tr_proforma_finalize()
RETURNS trigger AS $$
DECLARE
    v_docno TEXT;
    v_inputby TEXT;
    v_idurut INTEGER;
    v_prefix TEXT;
    v_num TEXT;
    v_num_int INTEGER;
BEGIN
    -- Finalisasi dari status 'E' ke 'F'
    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN
        v_docno   := rtrim(NEW.docno); -- hapus spasi kanan
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        -- Pastikan tidak duplicate, kalau ada → increment nomor terakhir
        LOOP
            EXIT WHEN NOT EXISTS (SELECT 1 FROM sc_trx.proforma WHERE docno = v_docno);

            -- Ambil prefix & angka terakhir pakai regex
            -- Ambil prefix (semua sebelum nomor urut)
            v_prefix := substring(v_docno from '^(.*?/)[0-9]{4}/');
            -- Ambil nomor urut 4 digit
            v_num    := substring(v_docno from '/([0-9]{4})/');

            -- Ambil suffix (semua setelah nomor urut)
            v_suffix := substring(v_docno from '/[0-9]{4}(/.*)$');

            -- Convert ke integer & increment
            v_num_int := v_num::INTEGER + 1;

            -- Gabungkan kembali
            v_docno := v_prefix || lpad(v_num_int::TEXT, 4, '0') || v_suffix;
        END LOOP;

        -- Pindahkan header
        INSERT INTO sc_trx.proforma (
            idurut, docno, docdate, pono, podate,jnsinvoice, cust, address, phone, fax,
            facrisk, shipper, consignee, shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,ttlprice,
            nmbank, alamatbank, accno, accname, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, inputby, inputdate, 
            updateby, updatedate, printby, printdate, rolejob
        )
        SELECT
            idurut, v_docno, docdate, pono, podate,jnsinvoice, cust, address, phone, fax,
            facrisk, shipper, consignee, shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,ttlprice,
            nmbank, alamatbank, accno, accname, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, 'F', inputby, inputdate, 
            updateby, updatedate, printby, printdate, rolejob
        FROM sc_tmp.proforma
        WHERE docno = OLD.docno AND inputby = v_inputby AND idurut = v_idurut;

        -- Pindahkan detail
        INSERT INTO sc_trx.proformadtl (
            idurut, docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.proformadtl
        WHERE docno = OLD.docno AND inputby = v_inputby;

        -- Hapus dari tmp
        DELETE FROM sc_tmp.proforma WHERE docno = OLD.docno AND inputby = v_inputby AND idurut = v_idurut;
        DELETE FROM sc_tmp.proformadtl WHERE docno = OLD.docno AND inputby = v_inputby;
    
    
    -- END IF;
    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'F' AND COALESCE(NEW.docnotmp, '') != '') THEN
        DELETE FROM sc_trx.proforma WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.proformadtl WHERE docno = NEW.docnotmp;

        -- Insert into proformadtl with new columns
        INSERT INTO sc_trx.proformadtl
        ( idurut, docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT  idurut,  NEW.docnotmp, idbarang,nmbarang, unit, qty, price, exchange, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.proformadtl 
        WHERE docno = NEW.docno;

        -- Insert into proforma with new columns
        INSERT INTO sc_trx.proforma
            (idurut, docno, docdate, pono, podate,jnsinvoice, cust, address, phone, fax,
            facrisk, shipper, consignee, shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,ttlprice,
            nmbank, alamatbank, accno, accname, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, rolejob, docnotmp)
        SELECT 
            idurut, NEW.docnotmp, docdate, pono, podate,jnsinvoice, cust, address, phone, fax,
            facrisk, shipper, consignee, shippingmark, notifyparty, paymentmethod, bank,
            grosssales, downpayment, netsales, taxbasis, vat, pph22,ttlprice,
            nmbank, alamatbank, accno, accname, swiftcode,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, rolejob, docnotmp
        FROM sc_tmp.proforma
        WHERE docno = NEW.docno;

        DELETE FROM sc_tmp.proforma WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.proformadtl WHERE docno = NEW.docno;

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.proforma SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.proforma SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.proforma WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.proformadtl WHERE docno = NEW.docno;
    
    END IF;


    RETURN NEW; -- karena AFTER UPDATE
END;
$$ LANGUAGE plpgsql;
