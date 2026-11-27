
-- JALANKAN INI DULU

DROP TABLE IF EXISTS sc_tmp.salesorder_dtl
DROP TABLE IF EXISTS sc_trx.salesorder_dtl




CREATE TABLE IF NOT EXISTS sc_tmp.salesorder
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    delivdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    gradecustomer character(30),
    kdsalesman character(10),
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    isopenprice character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    pocust character(30),
    nodp character(30),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_tmp_salesorder PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.salesorder
    OWNER to postgres;





CREATE TABLE IF NOT EXISTS sc_trx.salesorder
(
    idurut serial NOT NULL,
    docno character(30) COLLATE pg_catalog."default" NOT NULL,
    docdate character(20) COLLATE pg_catalog."default",
    delivdate character(20) COLLATE pg_catalog."default",
    cabang character (30 ) COLLATE pg_catalog."default",    
    pemohon character(100) COLLATE pg_catalog."default",
    kdcustomer character(30) COLLATE pg_catalog."default",
    nmcustomer character(250) COLLATE pg_catalog."default",
    alamatcustomer TEXT,
    gradecustomer character(30),
    kdsalesman character(10),
    -- alamatkirim TEXT,
    jthtempo numeric(18,2),
    idtax character(20),
    isinclusive character(6),
    isopenprice character(6),
    currcode character(3),
    kurs numeric(18,2),
    dpp numeric(18,2),
    jumlahpajak numeric(18,2),
    total numeric(18,2),
    -- syarat TEXT,
    pocust character(30),
    nodp character(30),
    status character(6) COLLATE pg_catalog."default",
    keterangan TEXT,
    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    printby character varying(50) COLLATE pg_catalog."default",
    printdate timestamp without time zone,
    docnotmp character(30) COLLATE pg_catalog."default",
    CONSTRAINT pk_trx_salesorder PRIMARY KEY (docno)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.salesorder
    OWNER to postgres;



CREATE TABLE IF NOT EXISTS sc_tmp.salesorder_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    idprincipal CHARACTER(20) COLLATE pg_catalog."default",
    idgudang CHARACTER(30) COLLATE pg_catalog."default",
    idspec CHARACTER(30) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    bomdesc TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.salesorder_dtl
    OWNER TO postgres;




CREATE TABLE IF NOT EXISTS sc_trx.salesorder_dtl
(
    idurut SERIAL PRIMARY KEY,
    docno CHARACTER(30) COLLATE pg_catalog."default" NOT NULL,
    uniqueid VARCHAR(64),
    idbarang CHARACTER(20) COLLATE pg_catalog."default",
    nmbarang CHARACTER(150) COLLATE pg_catalog."default",
    idprincipal CHARACTER(20) COLLATE pg_catalog."default",
    idgudang CHARACTER(30) COLLATE pg_catalog."default",
    idspec CHARACTER(30) COLLATE pg_catalog."default",
    unit CHARACTER(20) COLLATE pg_catalog."default",
    qty NUMERIC(18,2),
    harga NUMERIC(18,2),
    multidisc NUMERIC(18,2),
    nilai NUMERIC(18,2),
    bomdesc TEXT COLLATE pg_catalog."default",
    status CHARACTER(6) COLLATE pg_catalog."default",
    inputby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby CHARACTER VARYING(50) COLLATE pg_catalog."default",
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp character(30)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.salesorder_dtl
    OWNER TO postgres;





-- FUNCTION: sc_tmp.tr_salesorder_finalize()

-- DROP FUNCTION IF EXISTS sc_tmp.tr_salesorder_finalize();
CREATE OR REPLACE FUNCTION sc_tmp.tr_salesorder_finalize()
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

    v_client_ip TEXT;
    v_uniqueid  VARCHAR(64);
BEGIN

    -- ===============================
    -- AMBIL IP DARI sc_log.useronline
    -- ===============================
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);
    
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
                FROM sc_trx.salesorder
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
        INSERT INTO sc_trx.salesorder (
            idurut, docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.salesorder
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            AND idurut = v_idurut;

        -- ===============================
        -- INSERT DETAIL
        -- ===============================
        INSERT INTO sc_trx.salesorder_dtl (
            idurut, docno, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        )
        SELECT
            idurut, v_docno, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate
        FROM sc_tmp.salesorder_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

        -- ===============================
        -- LOG: INSERT HEADER SO
        -- ===============================
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.S',                  -- kode module dari menuprg
            'I.S.B.1',              -- kode menu untuk SO
            'I',                    -- action: INPUT (1 huruf)
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- -- ===============================
        -- -- CLEANUP TMP
        -- -- ===============================
        DELETE FROM sc_tmp.salesorder
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby
            
            AND idurut = v_idurut;

        DELETE FROM sc_tmp.salesorder_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
            AND inputby = v_inputby;

    -- ===============================
    -- DOCNOTMP FLOW (TETAP)
    -- ===============================
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.salesorder WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.salesorder_dtl WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.salesorder_dtl
        (idurut, docno, idbarang, uniqueid,  nmbarang,
        idprincipal, idgudang, idspec, unit, qty, 
        harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
        kurs, idtax, currcode,
        bomdesc, multidisc,
        inputby, inputdate, status, updateby, updatedate, docnotmp)
        SELECT
            idurut, NEW.docnotmp, idbarang, uniqueid,  nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp
        FROM sc_tmp.salesorder_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno);

                -- ===============================
        -- UPDATE STATUS PP MENJADI 'PO'
        -- ===============================
        -- UPDATE sc_trx.pp p
        -- SET status = 'PO'
        -- WHERE p.docno IN (
        --     SELECT DISTINCT docnopp
        --     FROM sc_tmp.salesorder_dtl
        --     WHERE rtrim(docno) = rtrim(NEW.docno)
        --         AND docnopp IS NOT NULL
        --         AND docnopp <> ''
        -- );


        INSERT INTO sc_trx.salesorder
        (idurut, docno, cabang, docdate, pemohon, kdcustomer,
        nmcustomer, alamatcustomer, jthtempo,
        delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
        idtax,isinclusive, currcode, kurs, dpp, 
        jumlahpajak, total,
        keterangan, status, inputby, inputdate,
        updateby, updatedate, printby, printdate, printcount, docnotmp)
        SELECT
            idurut, NEW.docnotmp, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.salesorder
        WHERE rtrim(docno) = rtrim(NEW.docno);

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.S',                  -- kode module dari menuprg
            'I.S.B.1',              -- kode menu untuk PP
            'U',                    -- action: UPDATE (1 huruf)
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.salesorder WHERE rtrim(docno) = rtrim(NEW.docno);
        DELETE FROM sc_tmp.salesorder_dtl WHERE rtrim(docno) = rtrim(NEW.docno);

    ELSEIF (OLD.STATUS = 'E' AND NEW.STATUS = 'C') THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> '' AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.salesorder SET status = 'P' WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.salesorder SET status = 'F' WHERE docno = NEW.docnotmp;
        END IF;

            
        DELETE FROM sc_tmp.salesorder WHERE docno = NEW.docno;
        DELETE FROM sc_tmp.salesorder_dtl WHERE docno = NEW.docno;
    
    END IF;

    RETURN NEW;
END;
$BODY$;



CREATE TRIGGER tr_salesorder_finalize
    AFTER UPDATE ON sc_tmp.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_salesorder_finalize();







-- DROP FUNCTION IF EXISTS sc_trx.tr_salesorder();

CREATE OR REPLACE FUNCTION sc_trx.tr_salesorder()
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
    v_docno     TEXT;
    v_client_ip TEXT;
    v_inputby   TEXT;
BEGIN		

        -- ===============================
        -- AMBIL IP DARI sc_log.useronline
        -- ===============================
        v_docno := rtrim(NEW.docno);
        v_inputby := NEW.inputby;

        v_client_ip := sc_log.fn_get_user_ip(v_inputby);

        IF (OLD.STATUS='F' AND NEW.STATUS='C') THEN
            -- ===============================
            -- LOG: INSERT HEADER SO
            -- ===============================
            PERFORM sc_log.fn_log_transaction(
                NEW.docno,
                NULL,
                'I.S',                  -- kode module dari menuprg
                'I.S.B.1',              -- kode menu untuk SO
                'C',                    -- action: UPDATE (1 huruf)
                COALESCE(NEW.updateby, NEW.inputby),
                v_client_ip,
                COALESCE(NEW.updateby, NEW.inputby)
            );

        END IF;

		IF (OLD.STATUS='F' AND NEW.STATUS='E') THEN
			-- Insert into pp_dtl with new columns
			INSERT INTO sc_tmp.salesorder_dtl
			( idurut, docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, docnotmp)
			SELECT idurut, NEW.docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty, 
            harga, nilai, nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate, NEW.docno
			FROM sc_trx.salesorder_dtl 
			WHERE docno = NEW.docno;

			-- Insert into pp with new columns
			INSERT INTO sc_tmp.salesorder
            (
                idurut, docno, cabang, docdate, pemohon, kdcustomer,
                nmcustomer, alamatcustomer, jthtempo,
                delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
                idtax,isinclusive, currcode, kurs, dpp, 
                jumlahpajak, total,
                keterangan, status, inputby, inputdate, updateby, updatedate,
                printby, printdate, printcount, docnotmp
            )
			SELECT  idurut, NEW.docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax,isinclusive, currcode, kurs, dpp, 
            jumlahpajak, total,
            keterangan, status , inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
			FROM sc_trx.salesorder 
			WHERE docno = NEW.docno;

		END IF;	
			
		RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_salesorder()
    OWNER TO postgres;


    

-- FUNCTION: sc_trx.tr_salesorder()
-- Trigger: tr_salesorder

-- DROP TRIGGER IF EXISTS tr_salesorder ON sc_trx.salesorder;

CREATE OR REPLACE TRIGGER tr_salesorder
    AFTER UPDATE 
    ON sc_trx.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_salesorder();






-- Tambahkan kolom di sc_trx.salesorder_dtl
ALTER TABLE sc_trx.salesorder_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtypenjualan numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtyretur numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtydo numeric(18,2) DEFAULT 0;

-- Tambahkan kolom di sc_tmp.salesorder_dtl
ALTER TABLE sc_tmp.salesorder_dtl 
ADD COLUMN idtax character(20),
ADD COLUMN currcode character(3),
ADD COLUMN kurs numeric(18,2),
ADD COLUMN nilaikonversi numeric(18,2),
ADD COLUMN nilaipajak numeric(18,2),
ADD COLUMN IF NOT EXISTS qtypenjualan numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtyretur numeric(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS qtydo numeric(18,2) DEFAULT 0;





-- ALTER TABLE sc_tmp.salesorder_dtl
-- ADD COLUMN uniqueid VARCHAR(64)

-- ALTER TABLE sc_trx.salesorder_dtl
-- ADD COLUMN uniqueid VARCHAR(64)




-- =========== TAMBAHAN 24/8/26 ====================
-- docdate
ALTER TABLE sc_trx.salesorder
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;
ALTER TABLE sc_tmp.salesorder
ALTER COLUMN docdate TYPE DATE
USING TRIM(docdate)::DATE;


-- delivdate 
ALTER TABLE sc_trx.salesorder
ALTER COLUMN delivdate TYPE DATE
USING TRIM(delivdate)::DATE;
ALTER TABLE sc_tmp.salesorder
ALTER COLUMN delivdate TYPE DATE
USING TRIM(delivdate)::DATE;

-- printcount
ALTER TABLE sc_tmp.salesorder
ADD COLUMN printcount integer
ALTER TABLE sc_trx.salesorder
ADD COLUMN printcount integer



--####################################################### PATCHING 02 POSTGRESQL ###################################################################


--####################################################### PATCHING 02 POSTGRESQL ###################################################################



-- Tambahkan kolom di sc_trx.salesorder_dtl
ALTER TABLE sc_trx.salesorder_dtl 
ADD COLUMN IF NOT EXISTS etd date,
ADD COLUMN IF NOT EXISTS eta date,
ADD COLUMN IF NOT EXISTS description text;

-- Tambahkan kolom di sc_tmp.salesorder_dtl
ALTER TABLE sc_tmp.salesorder_dtl 
ADD COLUMN IF NOT EXISTS etd date,
ADD COLUMN IF NOT EXISTS eta date,
ADD COLUMN IF NOT EXISTS description text;

/* ============================================================================
   PATCH SALES ORDER - PostgreSQL
   Tujuan:
   - Memperbaiki function/trigger Sales Order tanpa DROP TABLE.
   - Mencegah bentrok PRIMARY KEY idurut pada tabel detail.
   - Memperkuat auto-number dokumen dengan advisory transaction lock.
   - Mengubah seluruh kolom numerik Sales Order menjadi NUMERIC(18,4).
   - Mempertahankan alur finalisasi, DOCNOTMP, revisi, cancel, dan logging.

   PENTING:
   - Jalankan terlebih dahulu pada database DEVELOPMENT / hasil backup.
   - Script ini mengasumsikan kolom-kolom yang digunakan sudah tersedia,
     sesuai struktur dan ALTER TABLE pada script sumber.
   - Script tidak membuat/mengubah/menghapus tabel dan tidak menghapus data.
   ============================================================================ */

BEGIN;

/* --------------------------------------------------------------------------
   PATCH NUMERIC SALES ORDER
   Seluruh kolom numerik pada header dan detail sc_tmp/sc_trx menjadi
   NUMERIC(18,4). Data lama tetap dipertahankan; nilai dibulatkan ke 4
   angka desimal oleh PostgreSQL saat konversi tipe.
   -------------------------------------------------------------------------- */

-- HEADER TEMPORARY
ALTER TABLE sc_tmp.salesorder
    ALTER COLUMN jthtempo    TYPE NUMERIC(18,4) USING jthtempo::NUMERIC(18,4),
    ALTER COLUMN kurs        TYPE NUMERIC(18,4) USING kurs::NUMERIC(18,4),
    ALTER COLUMN dpp         TYPE NUMERIC(18,4) USING dpp::NUMERIC(18,4),
    ALTER COLUMN jumlahpajak TYPE NUMERIC(18,4) USING jumlahpajak::NUMERIC(18,4),
    ALTER COLUMN total       TYPE NUMERIC(18,4) USING total::NUMERIC(18,4);

-- HEADER TRANSAKSI
ALTER TABLE sc_trx.salesorder
    ALTER COLUMN jthtempo    TYPE NUMERIC(18,4) USING jthtempo::NUMERIC(18,4),
    ALTER COLUMN kurs        TYPE NUMERIC(18,4) USING kurs::NUMERIC(18,4),
    ALTER COLUMN dpp         TYPE NUMERIC(18,4) USING dpp::NUMERIC(18,4),
    ALTER COLUMN jumlahpajak TYPE NUMERIC(18,4) USING jumlahpajak::NUMERIC(18,4),
    ALTER COLUMN total       TYPE NUMERIC(18,4) USING total::NUMERIC(18,4);

-- DETAIL TEMPORARY
ALTER TABLE sc_tmp.salesorder_dtl
    ALTER COLUMN qty           TYPE NUMERIC(18,4) USING qty::NUMERIC(18,4),
    ALTER COLUMN harga         TYPE NUMERIC(18,4) USING harga::NUMERIC(18,4),
    ALTER COLUMN multidisc     TYPE NUMERIC(18,4) USING multidisc::NUMERIC(18,4),
    ALTER COLUMN nilai         TYPE NUMERIC(18,4) USING nilai::NUMERIC(18,4),
    ALTER COLUMN kurs          TYPE NUMERIC(18,4) USING kurs::NUMERIC(18,4),
    ALTER COLUMN nilaikonversi TYPE NUMERIC(18,4) USING nilaikonversi::NUMERIC(18,4),
    ALTER COLUMN nilaipajak    TYPE NUMERIC(18,4) USING nilaipajak::NUMERIC(18,4),
    ALTER COLUMN qtypenjualan  TYPE NUMERIC(18,4) USING qtypenjualan::NUMERIC(18,4),
    ALTER COLUMN qtyretur      TYPE NUMERIC(18,4) USING qtyretur::NUMERIC(18,4),
    ALTER COLUMN qtydo         TYPE NUMERIC(18,4) USING qtydo::NUMERIC(18,4);

-- DETAIL TRANSAKSI
ALTER TABLE sc_trx.salesorder_dtl
    ALTER COLUMN qty           TYPE NUMERIC(18,4) USING qty::NUMERIC(18,4),
    ALTER COLUMN harga         TYPE NUMERIC(18,4) USING harga::NUMERIC(18,4),
    ALTER COLUMN multidisc     TYPE NUMERIC(18,4) USING multidisc::NUMERIC(18,4),
    ALTER COLUMN nilai         TYPE NUMERIC(18,4) USING nilai::NUMERIC(18,4),
    ALTER COLUMN kurs          TYPE NUMERIC(18,4) USING kurs::NUMERIC(18,4),
    ALTER COLUMN nilaikonversi TYPE NUMERIC(18,4) USING nilaikonversi::NUMERIC(18,4),
    ALTER COLUMN nilaipajak    TYPE NUMERIC(18,4) USING nilaipajak::NUMERIC(18,4),
    ALTER COLUMN qtypenjualan  TYPE NUMERIC(18,4) USING qtypenjualan::NUMERIC(18,4),
    ALTER COLUMN qtyretur      TYPE NUMERIC(18,4) USING qtyretur::NUMERIC(18,4),
    ALTER COLUMN qtydo         TYPE NUMERIC(18,4) USING qtydo::NUMERIC(18,4);


/* --------------------------------------------------------------------------
   1. FUNCTION: sc_tmp.tr_salesorder_finalize()
   Trigger: AFTER UPDATE ON sc_tmp.salesorder
   -------------------------------------------------------------------------- */
CREATE OR REPLACE FUNCTION sc_tmp.tr_salesorder_finalize()
RETURNS trigger
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_idurut      INTEGER;
    v_num         TEXT;
    v_num_int     BIGINT;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_client_ip   TEXT;
    v_docnotmp    TEXT;
BEGIN
    -- Trigger hanya memproses transisi status yang memang ditangani.
    IF NOT (
        (OLD.status = 'E' AND NEW.status = 'F') OR
        (OLD.status = 'E' AND NEW.status = 'C')
    ) THEN
        RETURN NEW;
    END IF;

    v_inputby   := NEW.inputby;
    v_idurut    := NEW.idurut;
    v_docno     := BTRIM(NEW.docno);
    v_docnotmp  := NULLIF(BTRIM(NEW.docnotmp), '');
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    /* ========================= NORMAL FINALIZE E -> F ==================== */
    IF OLD.status = 'E' AND NEW.status = 'F' AND v_docnotmp IS NULL THEN

        IF v_docno IS NULL OR v_docno = '' THEN
            RAISE EXCEPTION 'Nomor Sales Order tidak boleh kosong';
        END IF;

        -- Nomor dokumen harus memiliki angka di bagian paling belakang.
        IF v_docno !~ '[0-9]+$' THEN
            RAISE EXCEPTION
                'Format nomor Sales Order tidak valid (harus diakhiri angka): %',
                v_docno;
        END IF;

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_num := substring(v_docno FROM '([0-9]+)$');

        -- Kunci berdasarkan prefix agar dua proses finalisasi prefix sama
        -- tidak menghasilkan nomor dokumen yang sama secara bersamaan.
        PERFORM pg_advisory_xact_lock(hashtextextended(v_base_docno, 0));

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.salesorder so
                WHERE BTRIM(so.docno) = v_new_docno
            );

            v_num := substring(v_new_docno FROM '([0-9]+)$');
            IF v_num IS NULL OR v_num = '' THEN
                RAISE EXCEPTION 'Tidak dapat membaca nomor urut dari dokumen: %', v_new_docno;
            END IF;

            v_num_int := v_num::BIGINT + 1;
            v_new_docno := v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');

            IF length(v_new_docno) > 30 THEN
                RAISE EXCEPTION 'Nomor Sales Order melebihi 30 karakter: %', v_new_docno;
            END IF;
        END LOOP;

        v_docno := v_new_docno;

        -- Insert header. idurut header tetap mengikuti perilaku script awal.
        INSERT INTO sc_trx.salesorder (
            idurut, docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        )
        SELECT
            idurut, v_docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, 'F', inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount
        FROM sc_tmp.salesorder
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        IF NOT FOUND THEN
            RAISE EXCEPTION
                'Header temporary SO tidak ditemukan. docno=%, inputby=%, idurut=%',
                OLD.docno, v_inputby, v_idurut;
        END IF;

        -- Jangan menyalin idurut detail. SERIAL trx membuat ID baru
        -- sehingga tidak terjadi duplicate primary key.
        INSERT INTO sc_trx.salesorder_dtl (
            docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty,
            harga, nilai, nilaikonversi, nilaipajak,
            qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate,
            etd, eta, description
        )
        SELECT
            v_docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty,
            harga, nilai, nilaikonversi, nilaipajak,
            qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate,
            etd, eta, description
        FROM sc_tmp.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.S',
            'I.S.B.1',
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        DELETE FROM sc_tmp.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.salesorder
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    /* ========================= DOCNOTMP FLOW E -> F ====================== */
    ELSIF OLD.status = 'E' AND NEW.status = 'F' AND v_docnotmp IS NOT NULL THEN

        -- Hapus detail lebih dahulu untuk menghindari masalah FK bila ada.
        DELETE FROM sc_trx.salesorder_dtl
        WHERE BTRIM(docno) = v_docnotmp;

        DELETE FROM sc_trx.salesorder
        WHERE BTRIM(docno) = v_docnotmp;

        -- Header tujuan dibuat sebelum detail.
        INSERT INTO sc_trx.salesorder (
            idurut, docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        )
        SELECT
            idurut, v_docnotmp, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate,
            updateby, updatedate, printby, printdate, printcount, docnotmp
        FROM sc_tmp.salesorder
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Header temporary SO DOCNOTMP tidak ditemukan: %', NEW.docno;
        END IF;

        -- ID detail dibuat baru oleh sequence sc_trx.
        INSERT INTO sc_trx.salesorder_dtl (
            docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty,
            harga, nilai, nilaikonversi, nilaipajak,
            qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate,
            docnotmp, etd, eta, description
        )
        SELECT
            v_docnotmp, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty,
            harga, nilai, nilaikonversi, nilaipajak,
            qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate,
            docnotmp, etd, eta, description
        FROM sc_tmp.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.S',
            'I.S.B.1',
            'U',
            COALESCE(NEW.updateby, NEW.inputby),
            v_client_ip,
            COALESCE(NEW.updateby, NEW.inputby)
        );

        DELETE FROM sc_tmp.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.salesorder
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

    /* ================================ CANCEL E -> C ===================== */
    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN

        IF NEW.printby IS NOT NULL
           AND BTRIM(NEW.printby) <> ''
           AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.salesorder
            SET status = 'P'
            WHERE BTRIM(docno) = v_docnotmp;
        ELSE
            UPDATE sc_trx.salesorder
            SET status = 'F'
            WHERE BTRIM(docno) = v_docnotmp;
        END IF;

        DELETE FROM sc_tmp.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.salesorder
        WHERE BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$BODY$;


/* --------------------------------------------------------------------------
   2. FUNCTION: sc_trx.tr_salesorder()
   Trigger: AFTER UPDATE ON sc_trx.salesorder
   -------------------------------------------------------------------------- */
CREATE OR REPLACE FUNCTION sc_trx.tr_salesorder()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE
AS $BODY$
DECLARE
    v_client_ip TEXT;
    v_inputby   TEXT;
BEGIN
    -- Hindari kerja tambahan bila status tidak berubah.
    IF OLD.status IS NOT DISTINCT FROM NEW.status THEN
        RETURN NEW;
    END IF;

    v_inputby := COALESCE(NEW.updateby, NEW.inputby);
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    /* ================================ F -> C ============================ */
    IF OLD.status = 'F' AND NEW.status = 'C' THEN
        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.S',
            'I.S.B.1',
            'C',
            v_inputby,
            v_client_ip,
            v_inputby
        );
    END IF;

    /* ================================ F -> E ============================ */
    IF OLD.status = 'F' AND NEW.status = 'E' THEN

        -- Bersihkan draft lama untuk dokumen ini agar tidak terjadi
        -- duplicate idurut pada primary key detail temporary.
        DELETE FROM sc_tmp.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        -- idurut detail tidak disalin; gunakan SERIAL temporary yang baru.
        INSERT INTO sc_tmp.salesorder_dtl (
            docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty,
            harga, nilai, nilaikonversi, nilaipajak,
            qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate,
            docnotmp, etd, eta, description
        )
        SELECT
            NEW.docno, idbarang, uniqueid, nmbarang,
            idprincipal, idgudang, idspec, unit, qty,
            harga, nilai, nilaikonversi, nilaipajak,
            qtypenjualan, qtyretur, qtydo,
            kurs, idtax, currcode,
            bomdesc, multidisc,
            inputby, inputdate, status, updateby, updatedate,
            NEW.docno, etd, eta, description
        FROM sc_trx.salesorder_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        -- Upsert draft header by docno. Preserve existing temporary
        -- primary-key rule and refresh it from the transaction header.
        INSERT INTO sc_tmp.salesorder (
            idurut, docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, docnotmp
        )
        SELECT
            idurut, NEW.docno, cabang, docdate, pemohon, kdcustomer,
            nmcustomer, alamatcustomer, jthtempo,
            delivdate, isopenprice, pocust, nodp, kdsalesman, gradecustomer,
            idtax, isinclusive, currcode, kurs, dpp,
            jumlahpajak, total,
            keterangan, status, inputby, inputdate, updateby, updatedate,
            printby, printdate, printcount, NEW.docno
        FROM sc_trx.salesorder
        WHERE BTRIM(docno) = BTRIM(NEW.docno)
        ON CONFLICT (docno) DO UPDATE SET
            idurut       = EXCLUDED.idurut,
            cabang       = EXCLUDED.cabang,
            docdate      = EXCLUDED.docdate,
            pemohon      = EXCLUDED.pemohon,
            kdcustomer   = EXCLUDED.kdcustomer,
            nmcustomer   = EXCLUDED.nmcustomer,
            alamatcustomer = EXCLUDED.alamatcustomer,
            jthtempo     = EXCLUDED.jthtempo,
            delivdate    = EXCLUDED.delivdate,
            isopenprice  = EXCLUDED.isopenprice,
            pocust       = EXCLUDED.pocust,
            nodp         = EXCLUDED.nodp,
            kdsalesman   = EXCLUDED.kdsalesman,
            gradecustomer = EXCLUDED.gradecustomer,
            idtax        = EXCLUDED.idtax,
            isinclusive  = EXCLUDED.isinclusive,
            currcode     = EXCLUDED.currcode,
            kurs         = EXCLUDED.kurs,
            dpp          = EXCLUDED.dpp,
            jumlahpajak  = EXCLUDED.jumlahpajak,
            total        = EXCLUDED.total,
            keterangan   = EXCLUDED.keterangan,
            status       = EXCLUDED.status,
            inputby      = EXCLUDED.inputby,
            inputdate    = EXCLUDED.inputdate,
            updateby     = EXCLUDED.updateby,
            updatedate   = EXCLUDED.updatedate,
            printby      = EXCLUDED.printby,
            printdate    = EXCLUDED.printdate,
            printcount   = EXCLUDED.printcount,
            docnotmp     = EXCLUDED.docnotmp;
    END IF;

    RETURN NEW;
END;
$BODY$;

ALTER FUNCTION sc_trx.tr_salesorder() OWNER TO postgres;


/* --------------------------------------------------------------------------
   3. Pasang ulang trigger dengan nama yang sama.
   Tidak mengubah tabel atau data transaksi.
   -------------------------------------------------------------------------- */
DROP TRIGGER IF EXISTS tr_salesorder_finalize ON sc_tmp.salesorder;
CREATE TRIGGER tr_salesorder_finalize
    AFTER UPDATE ON sc_tmp.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_tmp.tr_salesorder_finalize();

DROP TRIGGER IF EXISTS tr_salesorder ON sc_trx.salesorder;
CREATE TRIGGER tr_salesorder
    AFTER UPDATE ON sc_trx.salesorder
    FOR EACH ROW
    EXECUTE FUNCTION sc_trx.tr_salesorder();

/* Sinkronisasi sequence detail. Script lama menyalin idurut secara eksplisit,
   sehingga sequence SERIAL mungkin tertinggal dari MAX(idurut) saat ini. */
DO $SYNC_SEQUENCE$
BEGIN
    PERFORM setval(
        pg_get_serial_sequence('sc_tmp.salesorder_dtl', 'idurut')::regclass,
        COALESCE((SELECT MAX(idurut) FROM sc_tmp.salesorder_dtl), 1),
        (SELECT MAX(idurut) IS NOT NULL FROM sc_tmp.salesorder_dtl)
    );

    PERFORM setval(
        pg_get_serial_sequence('sc_trx.salesorder_dtl', 'idurut')::regclass,
        COALESCE((SELECT MAX(idurut) FROM sc_trx.salesorder_dtl), 1),
        (SELECT MAX(idurut) IS NOT NULL FROM sc_trx.salesorder_dtl)
    );
END;
$SYNC_SEQUENCE$;

COMMIT;

/* --------------------------------------------------------------------------
   4. VERIFIKASI TRIGGER SETELAH PATCH
   Jalankan SELECT ini setelah COMMIT.
   -------------------------------------------------------------------------- */
-- SELECT n.nspname AS schema_name,
--        c.relname AS table_name,
--        t.tgname AS trigger_name,
--        pg_get_triggerdef(t.oid) AS trigger_definition
-- FROM pg_trigger t
-- JOIN pg_class c ON c.oid = t.tgrelid
-- JOIN pg_namespace n ON n.oid = c.relnamespace
-- WHERE NOT t.tgisinternal
--   AND n.nspname IN ('sc_tmp', 'sc_trx')
--   AND c.relname IN ('salesorder', 'salesorder_dtl')
-- ORDER BY n.nspname, c.relname, t.tgname;

/* CATATAN PENTING:
   1. Pastikan kolom printcount tersedia pada kedua tabel header.
   2. Pastikan kolom berikut tersedia pada kedua tabel detail:
      nilaikonversi, nilaipajak, qtypenjualan, qtyretur, qtydo,
      kurs, idtax, currcode, etd, eta, description.
   3. Script sumber menunjukkan kolom-kolom tersebut ditambahkan melalui
      ALTER TABLE. Script patch ini sengaja tidak menjalankan ALTER TABLE.
   4. Bila trigger/function sc_log.fn_get_user_ip atau
      sc_log.fn_log_transaction tidak tersedia, proses akan gagal dan
      transaksi akan rollback secara otomatis.
*/
