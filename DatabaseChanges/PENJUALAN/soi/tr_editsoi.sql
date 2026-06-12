-- FUNCTION: sc_trx.soi()
-- Trigger: soi

-- DROP TRIGGER IF EXISTS soi ON sc_trx.salesorder;

CREATE OR REPLACE TRIGGER soi
    AFTER UPDATE 
    ON sc_trx.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.soi();


-- DROP FUNCTION IF EXISTS sc_trx.soi();

CREATE OR REPLACE FUNCTION sc_trx.soi()
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
			-- Insert into salesorderdtl with new columns
			INSERT INTO sc_tmp.salesorderdtl
			( idurut, docno, idbarang,nmbarang, unit, qty, price, exchange, 
			grade, size, cutlength, totaldelivery, balanceorder, specno,
			etd, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idbarang,nmbarang, unit, qty, price, exchange, 
			grade, size, cutlength, totaldelivery, balanceorder, specno,
			etd, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.salesorderdtl 
			WHERE docno = NEW.docno;

			-- Insert into salesorder with new columns
			INSERT INTO sc_tmp.salesorder
			(
				idurut, docno, docdate, cust, po, pocust, revno,
				description, status, inputby, inputdate, rolejob, docnotmp
			)
			SELECT  
				idurut, NEW.docno, docdate, cust, po, pocust, revno,
				description, status, inputby, inputdate, rolejob, NEW.docno
			FROM sc_trx.salesorder
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.soi()
    OWNER TO postgres;
