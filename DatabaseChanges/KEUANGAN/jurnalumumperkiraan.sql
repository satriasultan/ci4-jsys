
-- JUP - REVISI STRUKTUR DAN INTEGRASI TRANSACTION_DT
-- Tidak DROP tabel; data transaksi existing dipertahankan.
CREATE SCHEMA IF NOT EXISTS sc_tmp;
CREATE SCHEMA IF NOT EXISTS sc_trx;

BEGIN;
CREATE TABLE IF NOT EXISTS sc_tmp.jup
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    balance numeric(18,2),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_jup PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.jup
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.jup
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    cabang character (30) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    balance numeric(18,2),
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_jup PRIMARY KEY (idurut, docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.jup
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.jup_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idcoa CHARACTER(20) COLLATE pg_catalog."default",
    nmcoa CHARACTER(250) COLLATE pg_catalog."default",
    remarks TEXT COLLATE pg_catalog."default",
    dk character(20),
    cabang character (30) COLLATE pg_catalog."default",
    nilai numeric(18,2),
    uniqueid VARCHAR(64),
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.jup_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.jup_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    idcoa CHARACTER(20) COLLATE pg_catalog."default",
    nmcoa CHARACTER(250) COLLATE pg_catalog."default",
    remarks TEXT COLLATE pg_catalog."default",
    dk character(20),
    cabang character (30) COLLATE pg_catalog."default",
    nilai numeric(18,2),
    uniqueid VARCHAR(64),
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.jup_dtl
    OWNER TO postgres;

-- =========================================================
-- PENAMBAHAN KOLOM JUP (ADDITIVE / AMAN UNTUK DATA EXISTING)
-- Header: metadata accounting dan currency.
-- Detail: nilaiakhir sebagai nilai posting, currency dan kurs.
-- =========================================================
ALTER TABLE sc_tmp.jup
    ADD COLUMN IF NOT EXISTS np CHARACTER(20),
    ADD COLUMN IF NOT EXISTS isinclusive CHARACTER(6) DEFAULT 'NO',
    ADD COLUMN IF NOT EXISTS currcode CHARACTER(3) DEFAULT 'IDR',
    ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,6) DEFAULT 1,
    ADD COLUMN IF NOT EXISTS dpp NUMERIC(18,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS jumlahpajak NUMERIC(18,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS total NUMERIC(18,2) DEFAULT 0;

ALTER TABLE sc_trx.jup
    ADD COLUMN IF NOT EXISTS np CHARACTER(20),
    ADD COLUMN IF NOT EXISTS isinclusive CHARACTER(6) DEFAULT 'NO',
    ADD COLUMN IF NOT EXISTS currcode CHARACTER(3) DEFAULT 'IDR',
    ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,6) DEFAULT 1,
    ADD COLUMN IF NOT EXISTS dpp NUMERIC(18,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS jumlahpajak NUMERIC(18,2) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS total NUMERIC(18,2) DEFAULT 0;

ALTER TABLE sc_tmp.jup_dtl
    ADD COLUMN IF NOT EXISTS nilaiakhir NUMERIC(18,2),
    ADD COLUMN IF NOT EXISTS currcode CHARACTER(3) DEFAULT 'IDR',
    ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,6) DEFAULT 1;

ALTER TABLE sc_trx.jup_dtl
    ADD COLUMN IF NOT EXISTS nilaiakhir NUMERIC(18,2),
    ADD COLUMN IF NOT EXISTS currcode CHARACTER(3) DEFAULT 'IDR',
    ADD COLUMN IF NOT EXISTS kurs NUMERIC(18,6) DEFAULT 1;


-- Kolom currency asli pada transaction_dt.
-- currcode/kurs/nilai tetap dipakai untuk nilai posting (IDR).
ALTER TABLE sc_trx.transaction_dt
    ADD COLUMN IF NOT EXISTS currcode_trans CHAR(3),
    ADD COLUMN IF NOT EXISTS kurs_trans NUMERIC(18,6),
    ADD COLUMN IF NOT EXISTS nilai_trans NUMERIC(18,6);

UPDATE sc_trx.transaction_dt
SET currcode_trans = COALESCE(NULLIF(BTRIM(currcode_trans), ''), NULLIF(BTRIM(currcode), ''), 'IDR'),
    kurs_trans = COALESCE(NULLIF(kurs_trans, 0), NULLIF(kurs, 0), 1),
    nilai_trans = COALESCE(nilai_trans, nilai, 0)
WHERE currcode_trans IS NULL OR BTRIM(currcode_trans) = ''
   OR kurs_trans IS NULL OR kurs_trans = 0
   OR nilai_trans IS NULL;






-- FUNCTION: sc_tmp.tr_jup_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_jup_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_jup_finalize()
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
    v_lock_key  BIGINT;
    v_base_docno TEXT;
    v_new_docno  TEXT;
BEGIN
    IF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') = '' THEN

        -- ===============================
        -- NORMALISASI
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;
        -- ambil base docno (tanpa angka belakang)
        -- contoh:
        -- 05M/2601/PA0001 -> 05M/2601/PA
        -- PPB/2601/PT0025 -> PPB/2601/PT
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');

        -- ===============================
        -- ADVISORY LOCK (ANTI RACE CONDITION)
        -- ===============================
        PERFORM pg_advisory_xact_lock(hashtext(v_base_docno));

        -- ===============================
        -- AUTO INCREMENT JIKA SUDAH ADA
        -- ===============================
        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.jup
                WHERE rtrim(docno) = v_new_docno
            );

            -- ambil angka terakhir (dinamis)
            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');
            v_num_int := v_num::INTEGER + 1;

            -- padding mengikuti panjang awal
            v_new_docno := v_base_docno
                        || lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        -- gunakan docno final
        v_docno := v_new_docno;


        -- ===============================
        -- INSERT HEADER
        -- ===============================
        INSERT INTO sc_trx.jup (
            idurut, docno, cabang, docdate, pemohon, balance,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate,
            np, isinclusive, currcode, kurs, dpp, jumlahpajak, total
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, balance,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate,
            np, COALESCE(isinclusive, 'NO'), COALESCE(currcode, 'IDR'),
            COALESCE(NULLIF(kurs, 0), 1), COALESCE(dpp, 0),
            COALESCE(jumlahpajak, 0), COALESCE(total, balance, 0)
        FROM sc_tmp.jup
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.jup_dtl (
            idurut, docno, idcoa, nmcoa, remarks, dk, nilai, nilaiakhir,
            currcode, kurs, uniqueid, cabang, inputby, inputdate,
            status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idcoa, nmcoa, remarks, dk, nilai,
            COALESCE(nilaiakhir, nilai), COALESCE(NULLIF(currcode, ''), 'IDR'),
            COALESCE(NULLIF(kurs, 0), 1), uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.jup_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;


        -- ===============================
        -- CLEANUP TMP
        -- ===============================
        DELETE FROM sc_tmp.jup
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.jup_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.jup WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.jup_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.jup_dtl
        (idurut, docno, idcoa, nmcoa, remarks, dk, nilai, nilaiakhir,
         currcode, kurs, uniqueid, cabang, inputby, inputdate,
         status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, idcoa, nmcoa, remarks, dk, nilai,
            COALESCE(nilaiakhir, nilai), COALESCE(NULLIF(currcode, ''), 'IDR'),
            COALESCE(NULLIF(kurs, 0), 1), uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.jup_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);


        INSERT INTO sc_trx.jup
        (idurut, docno, cabang, docdate, pemohon, balance,
         keterangan, status, inputby, inputdate,
         updateby, updatedate, printby, printdate, docnotmp,
         np, isinclusive, currcode, kurs, dpp, jumlahpajak, total)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, balance,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, docnotmp,
            np, COALESCE(isinclusive, 'NO'), COALESCE(currcode, 'IDR'),
            COALESCE(NULLIF(kurs, 0), 1), COALESCE(dpp, 0),
            COALESCE(jumlahpajak, 0), COALESCE(total, balance, 0)
        FROM sc_tmp.jup
        WHERE rtrim(docno) = rtrim(NEW.docno);

        DELETE FROM sc_tmp.jup WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.jup_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.jup SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.jup SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.jup WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.jup_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



DROP TRIGGER IF EXISTS tr_jup_finalize ON sc_tmp.jup;
CREATE TRIGGER tr_jup_finalize
    AFTER UPDATE ON sc_tmp.jup
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_jup_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_jup();

CREATE OR REPLACE FUNCTION sc_trx.tr_jup()
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
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.jup_dtl
			( idurut, docno, idcoa, nmcoa, remarks, dk, nilai, nilaiakhir,
              currcode, kurs, uniqueid, cabang, inputby, inputdate,
              status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idcoa, nmcoa, remarks, dk, nilai,
            COALESCE(nilaiakhir, nilai), COALESCE(NULLIF(currcode, ''), 'IDR'),
            COALESCE(NULLIF(kurs, 0), 1), uniqueid, cabang,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.jup_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.jup
            (
                idurut, docno, cabang, docdate, pemohon, balance,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, docnotmp,
                np, isinclusive, currcode, kurs, dpp, jumlahpajak, total
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, balance,
            keterangan, status, inputby, inputdate, updateby, updatedate,
            printby, printdate, NEW.docno,
            np, COALESCE(isinclusive, 'NO'), COALESCE(currcode, 'IDR'),
            COALESCE(NULLIF(kurs, 0), 1), COALESCE(dpp, 0),
            COALESCE(jumlahpajak, 0), COALESCE(total, balance, 0)
			FROM sc_trx.jup 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_jup()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_jup()
-- Trigger: tr_jup

-- DROP TRIGGER IF EXISTS tr_jup ON sc_trx.jup;

DROP TRIGGER IF EXISTS tr_jup ON sc_trx.jup;
CREATE TRIGGER tr_jup
    AFTER UPDATE
    ON sc_trx.jup
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_jup();






-- =========================================================
-- NORMALISASI NILAI DETAIL JUP
-- nilaiakhir menjadi nilai posting. Jika UI lama hanya mengirim nilai,
-- nilaiakhir diisi dari nilai agar kompatibel.
-- =========================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_prepare_jup_detail()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP <> 'DELETE' THEN
        NEW.nilaiakhir := COALESCE(NEW.nilaiakhir, NEW.nilai);
        NEW.nilai := NEW.nilaiakhir;
        NEW.currcode := COALESCE(NULLIF(BTRIM(NEW.currcode), ''), 'IDR');
        NEW.kurs := COALESCE(NULLIF(NEW.kurs, 0), 1);

        IF NEW.nilaiakhir IS NULL THEN
            RAISE EXCEPTION 'Nilai JUP wajib diisi. Dokumen %, detail %.',
                NEW.docno, NEW.idurut;
        END IF;

        IF NEW.kurs <= 0 THEN
            RAISE EXCEPTION 'Kurs JUP harus lebih besar dari 0. Dokumen %, detail %.',
                NEW.docno, NEW.idurut;
        END IF;
    END IF;
    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS tr_jup_detail_prepare_tmp ON sc_tmp.jup_dtl;
CREATE TRIGGER tr_jup_detail_prepare_tmp
BEFORE INSERT OR UPDATE ON sc_tmp.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_jup_detail();

DROP TRIGGER IF EXISTS tr_jup_detail_prepare_trx ON sc_trx.jup_dtl;
CREATE TRIGGER tr_jup_detail_prepare_trx
BEFORE INSERT OR UPDATE ON sc_trx.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_jup_detail();


-- =========================================================
-- SYNC JUP KE TRANSACTION_DT
-- Journal type: JVGENL (General Journal)
-- Satu detail JUP = satu baris transaction_dt.
-- idcoa + debet_kredit wajib; counter_idcoa tidak wajib untuk JVGENL.
-- =========================================================
DO $$
BEGIN
    IF to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'sc_trx.transaction_dt belum tersedia.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.journal_type
        WHERE BTRIM(journal_type::TEXT) = 'JVGENL'
    ) THEN
        RAISE EXCEPTION 'Journal type JVGENL belum tersedia di sc_mst.journal_type.';
    END IF;
END;
$$;

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_jup_detail_JVGENL()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno       TEXT;
    v_uniqueid    TEXT;
    v_uid         TEXT;
    v_header      sc_trx.jup%ROWTYPE;
    v_coa         TEXT;
    v_dc          CHAR(1);
    v_nilai       NUMERIC(18,2);
    v_docdate     DATE;
    v_line        INTEGER;
    v_desc        TEXT;
BEGIN
    IF TG_OP = 'DELETE' THEN
        v_docno := BTRIM(OLD.docno);
        v_uniqueid := COALESCE(NULLIF(BTRIM(OLD.uniqueid), ''), 'DETAIL-' || OLD.idurut::TEXT);
        v_uid := MD5(v_docno || '|' || v_uniqueid || '|jup');

        DELETE FROM sc_trx.transaction_dt t
        WHERE t.uniqueid = v_uid
           OR (BTRIM(t.docno) = v_docno
               AND t.source_table = 'sc_trx.jup_dtl'
               AND t.source_line_id = OLD.idurut
               AND t.journal_type = 'JVGENL');
        RETURN OLD;
    END IF;

    v_docno := BTRIM(NEW.docno);
    v_uniqueid := COALESCE(NULLIF(BTRIM(NEW.uniqueid), ''), 'DETAIL-' || NEW.idurut::TEXT);
    v_uid := MD5(v_docno || '|' || v_uniqueid || '|jup');

    SELECT h.* INTO v_header
    FROM sc_trx.jup h
    WHERE BTRIM(h.docno) = v_docno
    ORDER BY h.idurut DESC
    LIMIT 1;

    IF NOT FOUND THEN
        RETURN NEW;
    END IF;

    v_coa := NULLIF(BTRIM(COALESCE(NEW.idcoa, '')), '');
    IF v_coa IS NULL THEN
        RAISE EXCEPTION 'COA JUP kosong. Dokumen %, detail %.', v_docno, NEW.idurut;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = v_coa
    ) THEN
        RAISE EXCEPTION 'COA % tidak ditemukan di sc_mst.coa.', v_coa;
    END IF;

    -- transaction_dt.debet_kredit hanya menerima D atau K.
    v_dc := CASE
        WHEN UPPER(BTRIM(COALESCE(NEW.dk, ''))) = 'D' THEN 'D'
        WHEN UPPER(BTRIM(COALESCE(NEW.dk, ''))) = 'K' THEN 'K'
        ELSE NULL
    END;

    IF v_dc IS NULL THEN
        RAISE EXCEPTION
            'DK JUP harus D atau K. Dokumen %, detail %, nilai DK: %.',
            v_docno, NEW.idurut, COALESCE(NEW.dk, '<NULL>');
    END IF;

    v_nilai := ROUND(COALESCE(NEW.nilaiakhir, NEW.nilai, 0), 2);
    IF v_nilai < 0 THEN
        RAISE EXCEPTION 'Nilai JUP tidak boleh negatif. Dokumen %, detail %.', v_docno, NEW.idurut;
    END IF;

    IF COALESCE(BTRIM(v_header.docdate), '') ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN
        v_docdate := BTRIM(v_header.docdate)::DATE;
    ELSIF COALESCE(BTRIM(v_header.docdate), '') ~ '^[0-9]{2}[-/][0-9]{2}[-/][0-9]{4}$' THEN
        v_docdate := TO_DATE(REPLACE(BTRIM(v_header.docdate), '/', '-'), 'DD-MM-YYYY');
    ELSE
        RAISE EXCEPTION 'Format docdate JUP tidak dikenali: dokumen %, tanggal %.',
            v_docno, v_header.docdate;
    END IF;

    v_line := GREATEST(COALESCE(NEW.idurut, 1), 1);
    v_desc := CONCAT_WS(' | ',
        NULLIF(BTRIM(COALESCE(NEW.remarks, '')), ''),
        NULLIF(BTRIM(COALESCE(v_header.keterangan, '')), '')
    );

    INSERT INTO sc_trx.transaction_dt (
        uniqueid, source_uniqueid, docno, doctype, journal_type, line_no, docdate,
        idbranch, cabang, type_in_out, ref_docno, ref_doctype,
        source_table, source_id, source_line_id,
        kdsupplier, nsupplier, nilai, dpp, pajak, total, idtax, isinclusive,
        currcode, kurs, currcode_trans, kurs_trans, nilai_trans,
        idcoa, counter_idcoa, debet_kredit,
        module, direction, stock_effect, accounting_effect, asset_effect,
        keterangan, createdby, createddate, updatedby, updateddate
    ) VALUES (
        v_uid, v_uniqueid, v_docno, 'JUP', 'JVGENL', v_line, v_docdate,
        BTRIM(COALESCE(v_header.cabang, '')), BTRIM(COALESCE(NEW.cabang, v_header.cabang, '')),
        CASE WHEN v_dc = 'D' THEN 'IN' ELSE 'OUT' END, '', '',
        'sc_trx.jup_dtl', v_header.idurut, NEW.idurut,
        '', '', v_nilai, v_nilai, 0, v_nilai, 'NON',
        COALESCE(NULLIF(UPPER(BTRIM(v_header.isinclusive)), ''), 'NO'),
        'IDR',
        1,
        COALESCE(NULLIF(BTRIM(NEW.currcode), ''), NULLIF(BTRIM(v_header.currcode), ''), 'IDR'),
        COALESCE(NULLIF(NEW.kurs, 0), NULLIF(v_header.kurs, 0), 1),
        COALESCE(NEW.nilaiakhir, NEW.nilai, v_nilai),
        v_coa, '', v_dc,
        'ACCOUNTING', 'INOUT', 'NONE', 'YES', 'NONE',
        v_desc, COALESCE(v_header.inputby, 'SYSTEM'),
        COALESCE(v_header.inputdate, CURRENT_TIMESTAMP),
        v_header.updateby, v_header.updatedate
    )
    ON CONFLICT (uniqueid) DO UPDATE SET
        source_uniqueid = EXCLUDED.source_uniqueid,
        docno = EXCLUDED.docno,
        doctype = EXCLUDED.doctype,
        journal_type = EXCLUDED.journal_type,
        line_no = EXCLUDED.line_no,
        docdate = EXCLUDED.docdate,
        idbranch = EXCLUDED.idbranch,
        cabang = EXCLUDED.cabang,
        type_in_out = EXCLUDED.type_in_out,
        source_table = EXCLUDED.source_table,
        source_id = EXCLUDED.source_id,
        source_line_id = EXCLUDED.source_line_id,
        nilai = EXCLUDED.nilai,
        dpp = EXCLUDED.dpp,
        pajak = EXCLUDED.pajak,
        total = EXCLUDED.total,
        idtax = EXCLUDED.idtax,
        isinclusive = EXCLUDED.isinclusive,
        currcode = EXCLUDED.currcode,
        kurs = EXCLUDED.kurs,
        currcode_trans = EXCLUDED.currcode_trans,
        kurs_trans = EXCLUDED.kurs_trans,
        nilai_trans = EXCLUDED.nilai_trans,
        idcoa = EXCLUDED.idcoa,
        counter_idcoa = EXCLUDED.counter_idcoa,
        debet_kredit = EXCLUDED.debet_kredit,
        module = EXCLUDED.module,
        direction = EXCLUDED.direction,
        stock_effect = EXCLUDED.stock_effect,
        accounting_effect = EXCLUDED.accounting_effect,
        asset_effect = EXCLUDED.asset_effect,
        keterangan = EXCLUDED.keterangan,
        updatedby = EXCLUDED.updatedby,
        updateddate = CURRENT_TIMESTAMP;

    RETURN NEW;
END;
$$;

/* =====================================================================
   PATCH NILAI AKHIR + CURRENCY UNTUK TRANSACTION_DT
   Pola sama dengan Penerimaan KB / Pengeluaran KB:
   - nilaiakhir wajib diisi pada detail TRX.
   - nilai disamakan dengan nilaiakhir sebelum AFTER trigger berjalan.
   - currcode selalu IDR dan kurs selalu 1.
   - Isi fn_sync_jup_detail_JVGENL() tidak perlu diubah.
   ===================================================================== */
CREATE OR REPLACE FUNCTION sc_trx.fn_prepare_jup_detail_transaction_JVGENL()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF NEW.nilaiakhir IS NULL THEN
        RAISE EXCEPTION
            'nilaiakhir wajib diisi pada JUP, dokumen %, detail %.',
            BTRIM(NEW.docno), NEW.idurut;
    END IF;

    -- Simpan nilai posting yang sudah dibulatkan, tetapi jangan timpa
    -- currcode/kurs sumber karena akan disimpan ke kolom *_trans.
    NEW.nilai := ROUND(NEW.nilaiakhir, 2);
    NEW.currcode := COALESCE(NULLIF(BTRIM(NEW.currcode), ''), 'IDR');
    NEW.kurs := COALESCE(NULLIF(NEW.kurs, 0), 1);

    RETURN NEW;
END;
$$;

-- Nama trigger diawali 00 agar berjalan sebelum trigger prepare JUP lama.
DROP TRIGGER IF EXISTS tr_jup_detail_00_prepare_JVGENL
ON sc_trx.jup_dtl;

CREATE TRIGGER tr_jup_detail_00_prepare_JVGENL
BEFORE INSERT OR UPDATE ON sc_trx.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_jup_detail_transaction_JVGENL();

DROP TRIGGER IF EXISTS tr_jup_detail_JVGENL ON sc_trx.jup_dtl;
CREATE TRIGGER tr_jup_detail_JVGENL
AFTER INSERT OR UPDATE OR DELETE ON sc_trx.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_sync_jup_detail_JVGENL();

-- Normalisasi detail TRX existing yang sudah mempunyai nilaiakhir.
-- UPDATE memicu AFTER trigger agar transaction_dt ikut diperbarui.
UPDATE sc_trx.jup_dtl
SET nilai = ROUND(nilaiakhir, 2)
WHERE nilaiakhir IS NOT NULL
  AND nilai IS DISTINCT FROM ROUND(nilaiakhir, 2);

-- Validasi hasil integrasi:
-- SELECT docno, journal_type, line_no, idcoa, debet_kredit, nilai, currcode, kurs
-- FROM sc_trx.transaction_dt
-- WHERE journal_type = 'JVGENL'
--   AND source_table = 'sc_trx.jup_dtl'
-- ORDER BY docdate DESC, docno, line_no;

-- =========================================================
-- REVISI SINKRONISASI TRANSACTION_HD
-- =========================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_transaction_dt_sync_hd()
RETURNS trigger
LANGUAGE plpgsql
AS $function$
DECLARE
    v_docno   TEXT;
    v_doctype TEXT;
    r         RECORD;
BEGIN
    IF TG_OP = 'DELETE' THEN
        v_docno   := BTRIM(OLD.docno);
        v_doctype := BTRIM(OLD.doctype);
    ELSE
        v_docno   := BTRIM(NEW.docno);
        v_doctype := BTRIM(NEW.doctype);
    END IF;

    -- Kunci per dokumen agar rebuild header tidak berjalan bersamaan.
    PERFORM pg_advisory_xact_lock(
        hashtext(COALESCE(v_doctype, '')),
        hashtext(COALESCE(v_docno, ''))
    );

    DELETE FROM sc_trx.transaction_hd
    WHERE BTRIM(docno) = v_docno
      AND BTRIM(doctype) = v_doctype;

    FOR r IN
        SELECT
            d.docno, d.doctype, d.journal_type, d.type_in_out,
            d.idbranch, d.cabang, d.ref_docno, d.ref_doctype, d.docdate,
            MIN(d.module)::VARCHAR(30) AS module,
            MIN(d.direction)::CHAR(5) AS direction,
            MIN(d.stock_effect)::CHAR(5) AS stock_effect,
            MIN(d.accounting_effect)::CHAR(3) AS accounting_effect,
            MIN(d.asset_effect)::CHAR(5) AS asset_effect,
            SUM(COALESCE(d.qty, 0)) AS total_qty,
            SUM(CASE WHEN d.type_in_out = 'IN' THEN COALESCE(d.qty, 0) ELSE 0 END) AS total_qty_in,
            SUM(CASE WHEN d.type_in_out = 'OUT' THEN COALESCE(d.qty, 0) ELSE 0 END) AS total_qty_out,
            SUM(COALESCE(d.bruto, 0)) AS total_bruto,
            SUM(COALESCE(d.discount, 0)) AS total_discount,
            SUM(COALESCE(d.nilai, 0)) AS total_nilai,
            SUM(COALESCE(d.dpp, 0)) AS total_dpp,
            SUM(COALESCE(d.pajak, 0)) AS total_pajak,
            SUM(COALESCE(d.total, 0)) AS total_total,
            SUM(COALESCE(d.debet, 0)) AS total_debet,
            SUM(COALESCE(d.kredit, 0)) AS total_kredit,
            COUNT(*) AS total_line,
            MAX(d.createdby) AS createdby,
            MIN(d.createddate) AS createddate
        FROM sc_trx.transaction_dt d
        WHERE BTRIM(d.docno) = v_docno
          AND BTRIM(d.doctype) = v_doctype
        GROUP BY
            d.docno, d.doctype, d.journal_type, d.type_in_out,
            d.idbranch, d.cabang, d.ref_docno, d.ref_doctype, d.docdate
    LOOP
        INSERT INTO sc_trx.transaction_hd (
            docno, doctype, journal_type, type_in_out, docdate,
            idbranch, cabang, ref_docno, ref_doctype,
            module, direction, stock_effect, accounting_effect, asset_effect,
            total_qty, total_qty_in, total_qty_out,
            total_bruto, total_discount, total_nilai,
            total_dpp, total_pajak, total_total,
            total_debet, total_kredit, balance,
            total_line, createdby, createddate, updateddate
        )
        VALUES (
            r.docno, r.doctype, r.journal_type, r.type_in_out, r.docdate,
            r.idbranch, r.cabang, r.ref_docno, r.ref_doctype,
            COALESCE(r.module, ''),
            COALESCE(r.direction, ''),
            COALESCE(r.stock_effect, 'NONE'),
            COALESCE(r.accounting_effect, 'NO'),
            COALESCE(r.asset_effect, 'NONE'),
            COALESCE(r.total_qty, 0),
            COALESCE(r.total_qty_in, 0),
            COALESCE(r.total_qty_out, 0),
            COALESCE(r.total_bruto, 0),
            COALESCE(r.total_discount, 0),
            COALESCE(r.total_nilai, 0),
            COALESCE(r.total_dpp, 0),
            COALESCE(r.total_pajak, 0),
            COALESCE(r.total_total, 0),
            COALESCE(r.total_debet, 0),
            COALESCE(r.total_kredit, 0),
            ABS(COALESCE(r.total_debet, 0) - COALESCE(r.total_kredit, 0)),
            COALESCE(r.total_line, 0),
            r.createdby,
            COALESCE(r.createddate, CURRENT_TIMESTAMP),
            CURRENT_TIMESTAMP
        )
        ON CONFLICT ON CONSTRAINT uq_transaction_hd
        DO UPDATE SET
            docdate = EXCLUDED.docdate,
            idbranch = EXCLUDED.idbranch,
            cabang = EXCLUDED.cabang,
            module = EXCLUDED.module,
            direction = EXCLUDED.direction,
            stock_effect = EXCLUDED.stock_effect,
            accounting_effect = EXCLUDED.accounting_effect,
            asset_effect = EXCLUDED.asset_effect,
            total_qty = EXCLUDED.total_qty,
            total_qty_in = EXCLUDED.total_qty_in,
            total_qty_out = EXCLUDED.total_qty_out,
            total_bruto = EXCLUDED.total_bruto,
            total_discount = EXCLUDED.total_discount,
            total_nilai = EXCLUDED.total_nilai,
            total_dpp = EXCLUDED.total_dpp,
            total_pajak = EXCLUDED.total_pajak,
            total_total = EXCLUDED.total_total,
            total_debet = EXCLUDED.total_debet,
            total_kredit = EXCLUDED.total_kredit,
            balance = EXCLUDED.balance,
            total_line = EXCLUDED.total_line,
            updateddate = CURRENT_TIMESTAMP;
    END LOOP;

    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    END IF;
    RETURN NEW;
END;
$function$;

COMMIT;


-- ============================================================
-- JUP / JURNAL UMUM PERKIRAAN
-- ONE SHOT PATCH: UNIQUEID + SYNC TRANSACTION_DT
-- Tidak DROP tabel dan tidak menghapus data transaksi.
-- ============================================================

BEGIN;

-- Pastikan dependency tersedia
DO $$
BEGIN
    IF to_regclass('sc_trx.jup') IS NULL
       OR to_regclass('sc_trx.jup_dtl') IS NULL
       OR to_regclass('sc_trx.transaction_dt') IS NULL THEN
        RAISE EXCEPTION 'Tabel JUP atau transaction_dt belum tersedia.';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.journal_type
        WHERE BTRIM(journal_type::TEXT) = 'JVGENL'
    ) THEN
        RAISE EXCEPTION 'Journal type JVGENL belum tersedia di sc_mst.journal_type.';
    END IF;
END;
$$;


-- 1. UniqueID detail: satu ID untuk setiap DOCNO + IDURUT DETAIL.
-- IDURUT detail adalah PK serial sehingga dua detail dalam dokumen
-- yang sama tidak akan menghasilkan UniqueID yang sama.
CREATE OR REPLACE FUNCTION sc_trx.fn_prepare_jup_detail()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP <> 'DELETE' THEN
        NEW.uniqueid := MD5(
            'JUPDTL|' || BTRIM(NEW.docno) || '|' || NEW.idurut::TEXT
        );

        NEW.nilaiakhir := COALESCE(NEW.nilaiakhir, NEW.nilai);
        NEW.nilai := NEW.nilaiakhir;
        NEW.currcode := COALESCE(NULLIF(BTRIM(NEW.currcode), ''), 'IDR');
        NEW.kurs := COALESCE(NULLIF(NEW.kurs, 0), 1);

        IF NEW.nilaiakhir IS NULL THEN
            RAISE EXCEPTION
                'Nilai JUP wajib diisi. Dokumen %, detail %.',
                BTRIM(NEW.docno), NEW.idurut;
        END IF;

        IF NEW.kurs <= 0 THEN
            RAISE EXCEPTION
                'Kurs JUP harus lebih besar dari 0. Dokumen %, detail %.',
                BTRIM(NEW.docno), NEW.idurut;
        END IF;
    END IF;

    RETURN NEW;
END;
$$;


-- 2. Normalisasi khusus JUP TRX: mengikuti ketentuan file sumber
-- currency IDR dan kurs 1.
CREATE OR REPLACE FUNCTION sc_trx.fn_prepare_jup_detail_transaction_JVGENL()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF NEW.nilaiakhir IS NULL THEN
        RAISE EXCEPTION
            'nilaiakhir wajib diisi pada JUP, dokumen %, detail %.',
            BTRIM(NEW.docno), NEW.idurut;
    END IF;

    -- Simpan nilai posting yang sudah dibulatkan, tetapi jangan timpa
    -- currcode/kurs sumber karena akan disimpan ke kolom *_trans.
    NEW.nilai := ROUND(NEW.nilaiakhir, 2);
    NEW.currcode := COALESCE(NULLIF(BTRIM(NEW.currcode), ''), 'IDR');
    NEW.kurs := COALESCE(NULLIF(NEW.kurs, 0), 1);

    RETURN NEW;
END;
$$;


-- 3. Sinkronisasi satu detail JUP = satu transaction_dt.
-- UniqueID transaksi memakai DOCNO + IDURUT HEADER + IDURUT DETAIL.
CREATE OR REPLACE FUNCTION sc_trx.fn_sync_jup_detail_JVGENL()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno       TEXT;
    v_uid         TEXT;
    v_header      sc_trx.jup%ROWTYPE;
    v_coa         TEXT;
    v_dc          CHAR(1);
    v_nilai       NUMERIC(18,2);
    v_docdate     DATE;
    v_line        INTEGER;
    v_desc        TEXT;
BEGIN
    IF TG_OP = 'DELETE' THEN
        DELETE FROM sc_trx.transaction_dt t
        WHERE t.source_table = 'sc_trx.jup_dtl'
          AND t.source_line_id = OLD.idurut
          AND t.journal_type = 'JVGENL';

        RETURN OLD;
    END IF;

    v_docno := BTRIM(NEW.docno);

    -- Ambil header JUP yang sesuai dokumen.
    SELECT h.*
    INTO v_header
    FROM sc_trx.jup h
    WHERE BTRIM(h.docno) = v_docno
    ORDER BY h.idurut DESC
    LIMIT 1;

    -- Detail bisa masuk sebelum header final tersedia.
    IF NOT FOUND THEN
        RETURN NEW;
    END IF;

    -- Identitas transaksi unik per header dan detail.
    v_uid := MD5(
        'JUPTX|' || v_docno || '|' ||
        v_header.idurut::TEXT || '|' ||
        NEW.idurut::TEXT
    );

    -- Cegah satu UID menimpa transaksi milik detail lain.
    IF EXISTS (
        SELECT 1
        FROM sc_trx.transaction_dt t
        WHERE t.uniqueid = v_uid
          AND NOT (
              t.source_table = 'sc_trx.jup_dtl'
              AND t.source_id = v_header.idurut
              AND t.source_line_id = NEW.idurut
              AND t.journal_type = 'JVGENL'
          )
    ) THEN
        RAISE EXCEPTION
            'UniqueID transaction_dt bentrok untuk JUP %, header %, detail %.',
            v_docno, v_header.idurut, NEW.idurut;
    END IF;

    v_coa := NULLIF(BTRIM(COALESCE(NEW.idcoa, '')), '');

    IF v_coa IS NULL THEN
        RAISE EXCEPTION
            'COA JUP kosong. Dokumen %, detail %.',
            v_docno, NEW.idurut;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = v_coa
    ) THEN
        RAISE EXCEPTION 'COA % tidak ditemukan di sc_mst.coa.', v_coa;
    END IF;

    v_dc := CASE
        WHEN UPPER(BTRIM(COALESCE(NEW.dk, ''))) = 'D' THEN 'D'
        WHEN UPPER(BTRIM(COALESCE(NEW.dk, ''))) = 'K' THEN 'K'
        ELSE NULL
    END;

    IF v_dc IS NULL THEN
        RAISE EXCEPTION
            'DK JUP harus D atau K. Dokumen %, detail %, DK %.',
            v_docno, NEW.idurut, COALESCE(NEW.dk, '<NULL>');
    END IF;

    v_nilai := ROUND(COALESCE(NEW.nilaiakhir, NEW.nilai, 0), 2);

    IF v_nilai < 0 THEN
        RAISE EXCEPTION
            'Nilai JUP tidak boleh negatif. Dokumen %, detail %.',
            v_docno, NEW.idurut;
    END IF;

    IF COALESCE(BTRIM(v_header.docdate), '')
       ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN
        v_docdate := BTRIM(v_header.docdate)::DATE;
    ELSIF COALESCE(BTRIM(v_header.docdate), '')
       ~ '^[0-9]{2}[-/][0-9]{2}[-/][0-9]{4}$' THEN
        v_docdate := TO_DATE(
            REPLACE(BTRIM(v_header.docdate), '/', '-'),
            'DD-MM-YYYY'
        );
    ELSE
        RAISE EXCEPTION
            'Format docdate JUP tidak dikenali: dokumen %, tanggal %.',
            v_docno, v_header.docdate;
    END IF;

    v_line := GREATEST(COALESCE(NEW.idurut, 1), 1);

    v_desc := CONCAT_WS(
        ' | ',
        NULLIF(BTRIM(COALESCE(NEW.remarks, '')), ''),
        NULLIF(BTRIM(COALESCE(v_header.keterangan, '')), '')
    );

    -- Hapus UID lama untuk source detail yang sama, jika format UID
    -- sebelumnya berbeda. Detail lain tidak tersentuh.
    DELETE FROM sc_trx.transaction_dt t
    WHERE t.source_table = 'sc_trx.jup_dtl'
      AND t.source_line_id = NEW.idurut
      AND t.journal_type = 'JVGENL'
      AND t.uniqueid <> v_uid;

    INSERT INTO sc_trx.transaction_dt (
        uniqueid, source_uniqueid, docno, doctype, journal_type,
        line_no, docdate, idbranch, cabang, type_in_out,
        ref_docno, ref_doctype,
        source_table, source_id, source_line_id,
        kdsupplier, nsupplier,
        nilai, dpp, pajak, total,
        idtax, isinclusive, currcode, kurs,
        currcode_trans, kurs_trans, nilai_trans,
        idcoa, counter_idcoa, debet_kredit,
        module, direction, stock_effect, accounting_effect, asset_effect,
        keterangan, createdby, createddate, updatedby, updateddate
    )
    VALUES (
        v_uid,
        NEW.uniqueid,
        v_docno,
        'JUP',
        'JVGENL',
        v_line,
        v_docdate,
        BTRIM(COALESCE(v_header.cabang, '')),
        BTRIM(COALESCE(NEW.cabang, v_header.cabang, '')),
        CASE WHEN v_dc = 'D' THEN 'IN' ELSE 'OUT' END,
        '',
        '',
        'sc_trx.jup_dtl',
        v_header.idurut,
        NEW.idurut,
        '',
        '',
        v_nilai,
        v_nilai,
        0,
        v_nilai,
        'NON',
        COALESCE(NULLIF(UPPER(BTRIM(v_header.isinclusive)), ''), 'NO'),
        'IDR',
        1,
        COALESCE(NULLIF(BTRIM(NEW.currcode), ''), NULLIF(BTRIM(v_header.currcode), ''), 'IDR'),
        COALESCE(NULLIF(NEW.kurs, 0), NULLIF(v_header.kurs, 0), 1),
        COALESCE(NEW.nilaiakhir, NEW.nilai, v_nilai),
        v_coa,
        '',
        v_dc,
        'ACCOUNTING',
        'INOUT',
        'NONE',
        'YES',
        'NONE',
        v_desc,
        COALESCE(v_header.inputby, 'SYSTEM'),
        COALESCE(v_header.inputdate, CURRENT_TIMESTAMP),
        v_header.updateby,
        v_header.updatedate
    )
    ON CONFLICT (uniqueid)
    DO UPDATE SET
        source_uniqueid = EXCLUDED.source_uniqueid,
        docno = EXCLUDED.docno,
        doctype = EXCLUDED.doctype,
        journal_type = EXCLUDED.journal_type,
        line_no = EXCLUDED.line_no,
        docdate = EXCLUDED.docdate,
        idbranch = EXCLUDED.idbranch,
        cabang = EXCLUDED.cabang,
        type_in_out = EXCLUDED.type_in_out,
        source_table = EXCLUDED.source_table,
        source_id = EXCLUDED.source_id,
        source_line_id = EXCLUDED.source_line_id,
        nilai = EXCLUDED.nilai,
        dpp = EXCLUDED.dpp,
        pajak = EXCLUDED.pajak,
        total = EXCLUDED.total,
        idtax = EXCLUDED.idtax,
        isinclusive = EXCLUDED.isinclusive,
        currcode = EXCLUDED.currcode,
        kurs = EXCLUDED.kurs,
        currcode_trans = EXCLUDED.currcode_trans,
        kurs_trans = EXCLUDED.kurs_trans,
        nilai_trans = EXCLUDED.nilai_trans,
        idcoa = EXCLUDED.idcoa,
        counter_idcoa = EXCLUDED.counter_idcoa,
        debet_kredit = EXCLUDED.debet_kredit,
        module = EXCLUDED.module,
        direction = EXCLUDED.direction,
        stock_effect = EXCLUDED.stock_effect,
        accounting_effect = EXCLUDED.accounting_effect,
        asset_effect = EXCLUDED.asset_effect,
        keterangan = EXCLUDED.keterangan,
        updatedby = EXCLUDED.updatedby,
        updateddate = CURRENT_TIMESTAMP;

    RETURN NEW;
END;
$$;


-- 4. Pasang trigger. Nama 00 menjaga urutan normalisasi IDR
-- sebelum trigger prepare detail dan sinkronisasi AFTER.
DROP TRIGGER IF EXISTS tr_jup_detail_prepare_tmp
ON sc_tmp.jup_dtl;

CREATE TRIGGER tr_jup_detail_prepare_tmp
BEFORE INSERT OR UPDATE ON sc_tmp.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_jup_detail();


DROP TRIGGER IF EXISTS tr_jup_detail_prepare_trx
ON sc_trx.jup_dtl;

CREATE TRIGGER tr_jup_detail_prepare_trx
BEFORE INSERT OR UPDATE ON sc_trx.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_jup_detail();


DROP TRIGGER IF EXISTS tr_jup_detail_00_prepare_JVGENL
ON sc_trx.jup_dtl;

CREATE TRIGGER tr_jup_detail_00_prepare_JVGENL
BEFORE INSERT OR UPDATE ON sc_trx.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_prepare_jup_detail_transaction_JVGENL();


DROP TRIGGER IF EXISTS tr_jup_detail_JVGENL
ON sc_trx.jup_dtl;

CREATE TRIGGER tr_jup_detail_JVGENL
AFTER INSERT OR UPDATE OR DELETE ON sc_trx.jup_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_sync_jup_detail_JVGENL();


-- 5. Sinkron ulang seluruh detail JUP TRX.
-- Update hanya baris yang UID-nya belum sesuai.
-- Trigger AFTER akan melakukan upsert transaction_dt.
UPDATE sc_trx.jup_dtl d
SET uniqueid = MD5(
    'JUPDTL|' || BTRIM(d.docno) || '|' || d.idurut::TEXT
);


-- 6. Validasi: tidak boleh ada detail JUP TRX yang tidak punya
-- pasangan transaction_dt.
DO $$
DECLARE
    v_detail INTEGER;
    v_synced INTEGER;
    v_missing INTEGER;
BEGIN
    SELECT COUNT(*)
    INTO v_detail
    FROM sc_trx.jup_dtl d
    WHERE EXISTS (
        SELECT 1
        FROM sc_trx.jup h
        WHERE BTRIM(h.docno) = BTRIM(d.docno)
    );

    SELECT COUNT(*)
    INTO v_synced
    FROM sc_trx.transaction_dt t
    WHERE t.source_table = 'sc_trx.jup_dtl'
      AND t.journal_type = 'JVGENL';

    SELECT COUNT(*)
    INTO v_missing
    FROM sc_trx.jup_dtl d
    WHERE EXISTS (
        SELECT 1
        FROM sc_trx.jup h
        WHERE BTRIM(h.docno) = BTRIM(d.docno)
    )
    AND NOT EXISTS (
        SELECT 1
        FROM sc_trx.transaction_dt t
        WHERE t.source_table = 'sc_trx.jup_dtl'
          AND t.source_line_id = d.idurut
          AND t.journal_type = 'JVGENL'
    );

    RAISE NOTICE 'JUP detail eligible : %', v_detail;
    RAISE NOTICE 'Transaction JUP     : %', v_synced;
    RAISE NOTICE 'Belum tersinkron    : %', v_missing;

    IF v_missing > 0 THEN
        RAISE EXCEPTION
            'Ada % detail JUP belum masuk transaction_dt. Periksa validasi COA/tanggal/header.',
            v_missing;
    END IF;
END;
$$;

COMMIT;

-- Verifikasi hasil:
-- SELECT d.docno, d.idurut AS detail_id, d.uniqueid AS detail_uid,
--        t.uniqueid AS transaction_uid, t.source_id, t.source_line_id,
--        t.idcoa, t.debet_kredit, t.nilai
-- FROM sc_trx.jup_dtl d
-- LEFT JOIN sc_trx.transaction_dt t
--   ON t.source_table = 'sc_trx.jup_dtl'
--  AND t.source_line_id = d.idurut
--  AND t.journal_type = 'JVGENL'
-- ORDER BY d.docno, d.idurut;

-- ============================================================
-- FINAL FIX: RECALCULATE TRANSACTION_HD
-- Penyebab: GROUP BY cabang membuat beberapa baris dengan
-- kombinasi uq_transaction_hd yang sama.
-- Perbaikan: GROUP BY hanya kolom kunci unik; cabang diagregasi.
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_transaction_hd(
    p_docno TEXT DEFAULT NULL
)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_docno TEXT;
BEGIN
    -- Rebuild penuh tetap didukung.
    IF p_docno IS NULL THEN
        DELETE FROM sc_trx.transaction_hd;

        FOR v_docno IN
            SELECT DISTINCT BTRIM(t.docno)
            FROM sc_trx.transaction_dt t
            WHERE NULLIF(BTRIM(t.docno), '') IS NOT NULL
        LOOP
            PERFORM sc_trx.fn_recalculate_transaction_hd(v_docno);
        END LOOP;

        RETURN;
    END IF;

    -- Serialisasi proses rebuild untuk dokumen yang sama.
    PERFORM pg_advisory_xact_lock(
        hashtext('transaction_hd'),
        hashtext(BTRIM(p_docno))
    );

    -- Hapus header dokumen ini saja, lalu bangun kembali.
    DELETE FROM sc_trx.transaction_hd h
    WHERE BTRIM(h.docno) = BTRIM(p_docno);

    INSERT INTO sc_trx.transaction_hd
    (
        docno,
        doctype,
        journal_type,
        type_in_out,
        docdate,
        idbranch,
        cabang,
        ref_docno,
        ref_doctype,
        module,
        direction,
        stock_effect,
        accounting_effect,
        asset_effect,
        total_qty,
        total_qty_in,
        total_qty_out,
        total_bruto,
        total_discount,
        total_nilai,
        total_dpp,
        total_pajak,
        total_total,
        total_debet,
        total_kredit,
        balance,
        total_line
    )
    SELECT
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        MIN(t.docdate),
        t.idbranch,
        MAX(COALESCE(t.cabang, '')),
        t.ref_docno,
        t.ref_doctype,
        MAX(COALESCE(t.module, '')),
        MAX(COALESCE(t.direction, '')),
        MAX(COALESCE(t.stock_effect, 'NONE')),
        MAX(COALESCE(t.accounting_effect, 'NO')),
        MAX(COALESCE(t.asset_effect, 'NONE')),
        COALESCE(SUM(t.qty), 0),
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out = 'IN'
                    THEN COALESCE(t.qty, 0)
                    ELSE 0
                END
            ),
            0
        ),
        COALESCE(
            SUM(
                CASE
                    WHEN t.type_in_out = 'OUT'
                    THEN COALESCE(t.qty, 0)
                    ELSE 0
                END
            ),
            0
        ),
        COALESCE(SUM(t.bruto), 0),
        COALESCE(SUM(t.discount), 0),
        COALESCE(SUM(t.nilai), 0),
        COALESCE(SUM(t.dpp), 0),
        COALESCE(SUM(t.pajak), 0),
        COALESCE(SUM(t.total), 0),
        COALESCE(SUM(t.debet), 0),
        COALESCE(SUM(t.kredit), 0),
        ABS(
            ROUND(
                COALESCE(SUM(t.debet), 0)
                - COALESCE(SUM(t.kredit), 0),
                2
            )
        ),
        COUNT(*)
    FROM sc_trx.transaction_dt t
    WHERE BTRIM(t.docno) = BTRIM(p_docno)
    GROUP BY
        t.docno,
        t.doctype,
        t.journal_type,
        t.type_in_out,
        t.idbranch,
        t.ref_docno,
        t.ref_doctype;

END;
$function$;

-- Verifikasi definisi fungsi setelah patch:
-- SELECT pg_get_functiondef(
--     'sc_trx.fn_recalculate_transaction_hd(text)'::regprocedure
-- );

-- Verifikasi header untuk dokumen yang sebelumnya bermasalah:
-- SELECT *
-- FROM sc_trx.transaction_hd
-- WHERE BTRIM(docno) = 'JUP/2611/PA0001'
--   AND BTRIM(doctype) = 'JUP'
--   AND journal_type = 'JVGENL';

