-- FUNCTION: sc_trx.tr_proforma()
-- Trigger: tr_proforma

-- DROP TRIGGER IF EXISTS tr_proforma ON sc_trx.proforma;

CREATE OR REPLACE TRIGGER tr_proforma
    AFTER UPDATE 
    ON sc_trx.proforma
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_proforma();


-- DROP FUNCTION IF EXISTS sc_trx.tr_proforma();

CREATE OR REPLACE FUNCTION sc_trx.tr_proforma()
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
BEGIN		

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into proformadtl with new columns
			INSERT INTO sc_tmp.proformadtl
			( idurut, docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.proformadtl 
			WHERE docno = NEW.docno;

			-- Insert into proforma with new columns
			INSERT INTO sc_tmp.proforma
			(
				idurut, docno, docdate, pono, podate, jnsinvoice, cust, address, phone, fax,
				facrisk, shipper, consignee, shippingmark, notifyparty, paymentmethod, bank,
				grosssales, downpayment, netsales, taxbasis, vat, pph22, ttlprice, 
				nmbank, alamatbank, accname, accno, swiftcode,
				description, brand, size, qty, pembayaran, pengiriman,
				expdate, ketentuan, status, inputby, inputdate, rolejob, docnotmp
			)
			SELECT  
				idurut, NEW.docno, docdate, pono, podate, jnsinvoice, cust, address, phone, fax,
				facrisk, shipper, consignee, shippingmar	k, notifyparty, paymentmethod, bank,
				grosssales, downpayment, netsales, taxbasis, vat, pph22, ttlprice, 
				nmbank, alamatbank, accname, accno, swiftcode,
				description, brand, size, qty, pembayaran, pengiriman,
				expdate, ketentuan, status, inputby, inputdate, rolejob, NEW.docno
			FROM sc_trx.proforma
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_proforma()
    OWNER TO postgres;
