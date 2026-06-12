CREATE TRIGGER tr_soi_finalize
    AFTER UPDATE ON sc_tmp.soi
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_soi_finalize();


CREATE OR REPLACE FUNCTION sc_tmp.tr_soi_finalize()
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
            EXIT WHEN NOT EXISTS (SELECT 1 FROM sc_trx.soi WHERE docno = v_docno);

            -- Ambil prefix & angka terakhir pakai regex
            v_prefix := regexp_replace(v_docno, '[0-9]{6}$', '');
            v_num    := substring(v_docno from '([0-9]{6})$');

            v_num_int := v_num::INTEGER + 1;
            v_docno := v_prefix || lpad(v_num_int::TEXT, 6, '0');
        END LOOP;

        -- Pindahkan header
        INSERT INTO sc_trx.soi (
            idurut, docno, docdate, cust, po, pocust, revno,
            description, status, inputby, inputdate, 
            updateby, updatedate, printby, printdate, rolejob
        )
        SELECT
            idurut, v_docno, docdate, cust, po, pocust, revno,
            description, 'F', inputby, inputdate, 
            updateby, updatedate, printby, printdate, rolejob
        FROM sc_tmp.soi
        WHERE docno = OLD.docno AND inputby = v_inputby AND idurut = v_idurut;

        -- Pindahkan detail
        INSERT INTO sc_trx.soidtl (
            idurut, docno, idbarang, cust, nmbarang, unit, qty, price, exchange, grade, 
            size, cutlength, totaldelivery, balanceorder, specno, ordernumbermsr, etd, usdmt, 
            amount, description, inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idbarang, cust, nmbarang, unit, qty, price, exchange, grade, 
            size, cutlength, totaldelivery, balanceorder, specno, ordernumbermsr, etd, usdmt, 
            amount, description, inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.soidtl
        WHERE docno = OLD.docno AND inputby = v_inputby;


        -- Hapus dari tmp
        DELETE FROM sc_tmp.soi WHERE docno = OLD.docno AND inputby = v_inputby AND idurut = v_idurut;
        DELETE FROM sc_tmp.soidtl WHERE docno = OLD.docno AND inputby = v_inputby;
    
    
    -- END IF;
    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'F' AND COALESCE(NEW.docnotmp, '') != '') THEN
        DELETE FROM sc_trx.soi WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.soidtl WHERE docno = NEW.docnotmp;

        -- Insert into soidtl with new columns
        INSERT INTO sc_trx.soidtl
        ( idurut, docno, idbarang, cust,nmbarang, unit, qty, price, exchange, 
        grade, size, cutlength, totaldelivery, balanceorder, specno, ordernumbermsr, 
        etd, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT  idurut,  NEW.docnotmp, idbarang, cust,nmbarang, unit, qty, price, exchange, 
        grade, size, cutlength, totaldelivery, balanceorder, specno, ordernumbermsr, 
        etd, usdmt, amount, description,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.soidtl 
        WHERE docno = NEW.docno;

        -- Insert into soi with new columns
        INSERT INTO sc_trx.soi
            (idurut, docno, docdate, cust, po, pocust, revno,
            description, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, rolejob, docnotmp)
        SELECT 
            idurut, NEW.docnotmp, docdate, cust, po, pocust, revno,
            description, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, rolejob, docnotmp
        FROM sc_tmp.soi
        WHERE docno = NEW.docno;

        DELETE FROM sc_tmp.soi WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.soidtl WHERE docno = NEW.docno;

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.soi SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.soi SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.soi WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.soidtl WHERE docno = NEW.docno;
    
    END IF;


    RETURN NEW; -- karena AFTER UPDATE
END;
$$ LANGUAGE plpgsql;
