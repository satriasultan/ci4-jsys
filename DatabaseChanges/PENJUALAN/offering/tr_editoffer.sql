-- FUNCTION: sc_trx.tr_offering()
-- Trigger: tr_offering

-- DROP TRIGGER IF EXISTS tr_offering ON sc_trx.offering;

CREATE OR REPLACE TRIGGER tr_offering
    AFTER UPDATE 
    ON sc_trx.offering
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_offering();


-- DROP FUNCTION IF EXISTS sc_trx.tr_offering();

CREATE OR REPLACE FUNCTION sc_trx.tr_offering()
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
			-- Insert into offeringdtl with new columns
			INSERT INTO sc_tmp.offeringdtl
			( idurut, docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idbarang,nmbarang, unit, qty, price, exchange, usdmt, description,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.offeringdtl 
			WHERE docno = NEW.docno;

			-- Insert into offering with new columns
			INSERT INTO sc_tmp.offering
            (
                idurut, docno, cust, address, docdate, phone, fax, up,
                description, brand, size, qty, pembayaran, pengiriman,
                expdate, ketentuan, status, inputby, inputdate, rolejob, docnotmp
            )
			SELECT  idurut, NEW.docno, cust, address, docdate, phone, fax, up,
            description, brand, size, qty, pembayaran, pengiriman,
            expdate, ketentuan, status , inputby, inputdate, rolejob, NEW.docno
			FROM sc_trx.offering 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_offering()
    OWNER TO postgres;
