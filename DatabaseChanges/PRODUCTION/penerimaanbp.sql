-- =========================================
-- I.R.A.9 — PENERIMAAN BARANG PRODUKSI
-- SATU FILE: CREATE TABLE + TRIGGER TMP->TRX + TRX->TMP + POSTING transaction_dt (WORECV)
-- Aman dijalankan ulang: blok DROP TABLE hanya dijalankan bila keempat tabel KOSONG.
-- Jalankan: php writable/dbq.php -m -f DatabaseChanges/PRODUCTION/penerimaanbp.sql
-- =========================================

BEGIN;

--DELETE FROM sc_mst.trxtype WHERE jenistrx='I.R.A.9';
INSERT INTO sc_mst.trxtype (kdtrx, jenistrx, uraian)
VALUES
('I','I.R.A.9','DRAFT'),
('E','I.R.A.9','REVISI/EDIT'),
('F','I.R.A.9','FINAL USER'),
('A','I.R.A.9','APPROVE'),
('A2','I.R.A.9','APPROVE 2'),
('A3','I.R.A.9','APPROVE 3'),
('P','I.R.A.9','CETAK/PRINT'),
('O','I.R.A.9','OBSOLATE'),
('C','I.R.A.9','CANCEL'),
('D','I.R.A.9','DELETE')
ON CONFLICT (kdtrx, jenistrx) DO NOTHING;


-- =========================================
-- 2. DROP TABLE - DIJAGA (hanya bila keempat tabel kosong)
-- =========================================
DO $$
DECLARE
    v_tabel TEXT;
    v_jml   BIGINT;
    v_ada   BOOLEAN := FALSE;
BEGIN
    -- Cek isi tabel secara dinamis (aman walau tabel belum ada sama sekali)
    FOREACH v_tabel IN ARRAY ARRAY['sc_tmp.penerimaanbp_mst', 'sc_tmp.penerimaanbp_dtl', 'sc_trx.penerimaanbp_mst', 'sc_trx.penerimaanbp_dtl']
    LOOP
        IF to_regclass(v_tabel) IS NOT NULL THEN
            EXECUTE format('SELECT count(*) FROM %s', v_tabel) INTO v_jml;
            IF v_jml > 0 THEN
                v_ada := TRUE;
                RAISE NOTICE 'DROP TABLE dilewati: % berisi % baris.', v_tabel, v_jml;
            END IF;
        END IF;
    END LOOP;

    IF v_ada THEN
        RAISE NOTICE 'DROP TABLE DILEWATI (tabel berisi data). Tabel dipertahankan; hanya trigger/fungsi yang di-refresh.';
    ELSE
        DROP TABLE IF EXISTS sc_tmp.penerimaanbp_dtl CASCADE;
        DROP TABLE IF EXISTS sc_tmp.penerimaanbp_mst CASCADE;
        DROP TABLE IF EXISTS sc_trx.penerimaanbp_dtl CASCADE;
        DROP TABLE IF EXISTS sc_trx.penerimaanbp_mst CASCADE;
        RAISE NOTICE 'DROP TABLE dijalankan (semua tabel kosong) - tabel dibuat ulang.';
    END IF;
END $$;


-- =========================================
-- TMP (DRAFT)
-- =========================================
CREATE TABLE IF NOT EXISTS sc_tmp.penerimaanbp_mst
(
    docno           character(30) NOT NULL,
    idurut          bigserial,
    doctype         character(20) DEFAULT 'BOM',
    cabang          character(30),
    pemohon         character(100),
    docdate         date,
    idlocation      character(12),
    nmlocation      character(50),
    tabno           character(50),
    woeno           character(30),
    wono            character(30),
    bomno           character(30),
    desc_bom        TEXT,
    idbarang_jadi   character(50),
    nmbarang_jadi   TEXT,
    buildfor        numeric(18,2),
    ttlprice        numeric(18,2),
    buildunit       character(30),
    batchno         character(50),
    bagian          character(30),
    docref          character(30),
    status          character(6),
    keterangan      TEXT,
    inputby         varchar(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    CONSTRAINT pk_tmp_penerimaanbp_mst PRIMARY KEY (docno, idurut)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.penerimaanbp_mst OWNER TO postgres;


CREATE TABLE IF NOT EXISTS sc_tmp.penerimaanbp_dtl
(
    docno           character(30) NOT NULL,
    idurut          bigserial,
    doctype         character(20) DEFAULT 'BOM',
    doctype_detail  character(20) DEFAULT 'MATERIAL',
    cabang          character(30),
    docref          character(30),
    pemohon         character(100),
    docdate         date,
    idbarang        character(50),
    nmbarang        TEXT,
    idbarang_bom    character(50),
    nmbarang_bom    TEXT,
    description     TEXT,
    qty             numeric(18,2),
    unit            char(20),
    nilai           numeric(18,2),
    harga           numeric(18,2),
    spec            char(100),
    issub           character(6),
    status          character(6),
    inputby         character(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    uniqueid        TEXT,
    CONSTRAINT pk_tmp_penerimaanbp_dtl PRIMARY KEY (docno, uniqueid, idurut)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_tmp.penerimaanbp_dtl OWNER TO postgres;


-- =========================================
-- TRX (FINAL)
-- =========================================
CREATE TABLE IF NOT EXISTS sc_trx.penerimaanbp_mst
(
    docno           character(30) NOT NULL,
    idurut          bigint,                       -- bigint, bukan bigserial
    doctype         character(20) DEFAULT 'BOM',
    cabang          character(30),
    pemohon         character(100),
    docdate         date,
    idlocation      character(12),
    nmlocation      character(50),
    tabno           character(50),
    woeno           character(30),
    wono            character(30),
    bomno           character(30),
    desc_bom        TEXT,
    idbarang_jadi   character(50),
    nmbarang_jadi   TEXT,
    buildfor        numeric(18,2),
    ttlprice        numeric(18,2),
    buildunit       character(30),
    batchno         character(50),
    bagian          character(30),
    docref          character(30),
    status          character(6),
    keterangan      TEXT,
    inputby         varchar(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    CONSTRAINT pk_trx_penerimaanbp_mst PRIMARY KEY (docno)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.penerimaanbp_mst OWNER TO postgres;


CREATE TABLE IF NOT EXISTS sc_trx.penerimaanbp_dtl
(
    docno           character(30) NOT NULL,
    idurut          bigint,                       -- bigint, bukan bigserial
    doctype         character(20) DEFAULT 'BOM',
    doctype_detail  character(20) DEFAULT 'MATERIAL',
    cabang          character(30),
    docref          character(30),
    pemohon         character(100),
    docdate         date,
    idbarang        character(50),
    nmbarang        TEXT,
    idbarang_bom    character(50),
    nmbarang_bom    TEXT,
    description     TEXT,
    qty             numeric(18,2),
    unit            char(20),
    nilai           numeric(18,2),
    harga           numeric(18,2),
    spec            char(100),
    issub           character(6),
    status          character(6),
    inputby         character(50),
    inputdate       timestamp without time zone,
    updateby        varchar(50),
    updatedate      timestamp without time zone,
    docnotmp        character(30),
    uniqueid        TEXT,
    CONSTRAINT pk_trx_penerimaanbp_dtl PRIMARY KEY (docno, uniqueid, idurut)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_trx.penerimaanbp_dtl OWNER TO postgres;


-- =========================================
-- TRIGGER & FUNCTION: TMP -> TRX
-- =========================================
DROP TRIGGER IF EXISTS tr_tmp_penerimaanbp_mst
ON sc_tmp.penerimaanbp_mst;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_penerimaanbp_mst();

CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_penerimaanbp_mst()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno        TEXT;
    v_inputby      TEXT;
    v_idurut       BIGINT;
    v_base_docno   TEXT;
    v_new_docno    TEXT;
    v_num          TEXT;
    v_num_int      INTEGER;
    v_lock_key     BIGINT;
    v_client_ip    TEXT;

BEGIN

    -- GUARD: hanya proses kalau status berubah
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- AMBIL IP
    v_client_ip := sc_log.fn_get_user_ip(NEW.inputby);

    -- =====================================================
    -- FINAL INSERT (docnotmp kosong)
    -- =====================================================
    IF UPPER(TRIM(OLD.status)) = 'E'
       AND UPPER(TRIM(NEW.status)) = 'F'
       AND COALESCE(TRIM(NEW.docnotmp), '') = '' THEN

        v_docno   := RTRIM(NEW.docno);
        v_inputby := NEW.inputby;
        v_idurut  := NEW.idurut;

        -- DETEKSI DUPLIKAT (cegah double insert)
        IF EXISTS (
            SELECT 1
            FROM sc_trx.penerimaanbp_mst
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) = TRIM(COALESCE(v_inputby, ''))
        ) THEN
            DELETE FROM sc_tmp.penerimaanbp_dtl
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND inputby = v_inputby;

            DELETE FROM sc_tmp.penerimaanbp_mst
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND inputby = v_inputby
              AND idurut = v_idurut;

            RETURN NEW;
        END IF;

        -- GENERATE DOCNO (advisory lock + loop increment)
        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1
                FROM sc_trx.penerimaanbp_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO Penerimaan BP tidak valid: %',
                    v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno ||
                lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        -- INSERT HEADER (dengan idurut)
        INSERT INTO sc_trx.penerimaanbp_mst (
            idurut, docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, status, keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            idurut, v_docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, 'F', keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

        -- INSERT DETAIL (dengan idurut)
        INSERT INTO sc_trx.penerimaanbp_dtl (
            idurut, docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            status, inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            idurut, v_docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            'F', inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        FROM sc_tmp.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        -- LOG: INSERT
        PERFORM sc_log.fn_log_transaction(
            v_docno::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.9',
            'I',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- CLEANUP TMP
        DELETE FROM sc_tmp.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(OLD.docno)
          AND inputby = v_inputby
          AND idurut = v_idurut;

    -- =====================================================
    -- FINAL EDIT (docnotmp terisi)
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
          AND UPPER(TRIM(NEW.status)) = 'F'
          AND COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN

        v_inputby := NEW.inputby;

        -- DELETE OLD TRX
        DELETE FROM sc_trx.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        DELETE FROM sc_trx.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        -- INSERT HEADER (pakai docnotmp, dengan idurut)
        INSERT INTO sc_trx.penerimaanbp_mst (
            idurut, docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, status, keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            idurut, NEW.docnotmp, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, 'F', keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        FROM sc_tmp.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- INSERT DETAIL
        INSERT INTO sc_trx.penerimaanbp_dtl (
            idurut, docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            status, inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            idurut, NEW.docnotmp, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            'F', inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        FROM sc_tmp.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        -- LOG: UPDATE
        PERFORM sc_log.fn_log_transaction(
            NEW.docnotmp::CHAR(30),
            NULL,
            'I.R',
            'I.R.A.9',
            'U',
            v_inputby,
            v_client_ip,
            v_inputby
        );

        -- CLEANUP TMP
        DELETE FROM sc_tmp.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(NEW.docno)
          AND inputby = v_inputby;

    -- =====================================================
    -- CANCEL EDIT
    -- =====================================================
    ELSIF UPPER(TRIM(OLD.status)) = 'E'
        AND UPPER(TRIM(NEW.status)) = 'C' THEN

        IF COALESCE(TRIM(NEW.docnotmp), '') <> '' THEN
            UPDATE sc_trx.penerimaanbp_mst
            SET status = 'F'
            WHERE TRIM(docno) = TRIM(NEW.docnotmp);
        END IF;

        DELETE FROM sc_tmp.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);

        DELETE FROM sc_tmp.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_tmp.tr_tmp_penerimaanbp_mst() OWNER TO postgres;


DROP TRIGGER IF EXISTS tr_tmp_penerimaanbp_mst
ON sc_tmp.penerimaanbp_mst;

CREATE TRIGGER tr_tmp_penerimaanbp_mst
AFTER UPDATE
ON sc_tmp.penerimaanbp_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_penerimaanbp_mst();


-- =========================================
-- TRIGGER & FUNCTION: TRX -> TMP
-- =========================================
DROP TRIGGER IF EXISTS tr_mst_penerimaanbp_mst
ON sc_trx.penerimaanbp_mst;

DROP FUNCTION IF EXISTS sc_trx.tr_mst_penerimaanbp_mst();

CREATE OR REPLACE FUNCTION sc_trx.tr_mst_penerimaanbp_mst()
RETURNS trigger
LANGUAGE plpgsql
COST 100
VOLATILE NOT LEAKPROOF
AS $BODY$

DECLARE
    v_docno     TEXT;
    v_inputby   TEXT;
    v_client_ip TEXT;

BEGIN

    -- GUARD: hanya proses kalau status berubah
    IF UPPER(TRIM(OLD.status)) = UPPER(TRIM(NEW.status)) THEN
        RETURN NEW;
    END IF;

    -- AMBIL IP
    v_docno     := RTRIM(NEW.docno);
    v_inputby   := COALESCE(NEW.updateby, NEW.inputby);
    v_client_ip := sc_log.fn_get_user_ip(v_inputby);

    -- ADVISORY LOCK
    PERFORM pg_advisory_xact_lock(hashtext(v_docno));

    -- =====================================================
    -- DIBATALKAN (F -> C)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'C') THEN

        PERFORM sc_log.fn_log_transaction(
            NEW.docno,
            NULL,
            'I.R',
            'I.R.A.9',
            'C',
            v_inputby,
            v_client_ip,
            v_inputby
        );

    END IF;

    -- =====================================================
    -- DIEDIT (F -> E)
    -- =====================================================
    IF (OLD.STATUS = 'F' AND NEW.STATUS = 'E') THEN

        -- INSERT TMP MASTER
        INSERT INTO sc_tmp.penerimaanbp_mst (
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, status, keterangan,
            inputby, inputdate, updateby, updatedate, docnotmp
        )
        SELECT
            docno, doctype, cabang, pemohon, docdate,
            idlocation, nmlocation, tabno, woeno, wono, bomno,
            desc_bom, idbarang_jadi, nmbarang_jadi,
            buildfor, ttlprice, buildunit, batchno,
            bagian, docref, 'E', keterangan,
            inputby, inputdate, updateby, updatedate, v_docno
        FROM sc_trx.penerimaanbp_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

        -- INSERT TMP DETAIL
        INSERT INTO sc_tmp.penerimaanbp_dtl (
            docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            status, inputby, inputdate, updateby, updatedate,
            docnotmp, uniqueid
        )
        SELECT
            docno, doctype, doctype_detail, cabang,
            pemohon, docdate, docref,
            idbarang, nmbarang, idbarang_bom, nmbarang_bom,
            description, qty, unit, nilai, harga, spec, issub,
            'E', inputby, inputdate, updateby, updatedate,
            docno, uniqueid
        FROM sc_trx.penerimaanbp_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);

    END IF;

    RETURN NEW;

END;
$BODY$;

ALTER FUNCTION sc_trx.tr_mst_penerimaanbp_mst() OWNER TO postgres;


DROP TRIGGER IF EXISTS tr_mst_penerimaanbp_mst
ON sc_trx.penerimaanbp_mst;

CREATE TRIGGER tr_mst_penerimaanbp_mst
AFTER UPDATE
ON sc_trx.penerimaanbp_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_mst_penerimaanbp_mst();



-- ============================================================
-- I.R.A.9 — PENERIMAAN BP : SYNC KE transaction_dt
-- Self-contained. Tidak menyentuh objek TAHAP 01-19.
-- Tidak DROP TABLE. Aman dijalankan ulang.
-- ============================================================

-- (transaksi tunggal: BEGIN ada di awal file, COMMIT di akhir)

-- ============================================================
-- STEP 1 — FUNGSI SYNC DETAIL PENERIMAAN BP → transaction_dt
-- ============================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_sync_penerimaanbp_dtl_to_transaction(
    p_docno    TEXT,
    p_uniqueid TEXT
) RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_mst        sc_trx.penerimaanbp_mst%ROWTYPE;
    v_dtl        sc_trx.penerimaanbp_dtl%ROWTYPE;
    v_tx_uid     TEXT;
    v_idbranch   CHAR(20);
    v_coa_debet  VARCHAR(20);
    v_coa_kredit VARCHAR(20);
    v_harga      NUMERIC(18,6);
    v_nilai      NUMERIC(18,2);
    v_exists     BOOLEAN;
BEGIN
    -- Ambil header
    SELECT * INTO v_mst
      FROM sc_trx.penerimaanbp_mst
     WHERE BTRIM(docno) = BTRIM(p_docno);
    IF NOT FOUND THEN RETURN; END IF;

    -- Ambil detail
    SELECT * INTO v_dtl
      FROM sc_trx.penerimaanbp_dtl
     WHERE BTRIM(docno) = BTRIM(p_docno)
       AND uniqueid = p_uniqueid;
    IF NOT FOUND THEN RETURN; END IF;

    -- Hanya baris MATERIAL yang posting
    IF UPPER(BTRIM(COALESCE(v_dtl.doctype_detail,'MATERIAL'))) <> 'MATERIAL' THEN
        RETURN;
    END IF;

    -- COA Kredit: WIP dari konfigurasi_umum.pproduksi (balance dengan MR)
    SELECT BTRIM(pproduksi) INTO v_coa_kredit
      FROM sc_mst.konfigurasi_umum
     LIMIT 1;

    -- COA Debet: mbarang.ppersediaan per idbarang detail
    SELECT BTRIM(ppersediaan) INTO v_coa_debet
      FROM sc_mst.mbarang
     WHERE BTRIM(idbarang) = BTRIM(v_dtl.idbarang)
     LIMIT 1;

    -- Harga: pakai dtl.harga; fallback ke standart_cost_dtl kalau issub bukan F
    v_harga := COALESCE(NULLIF(v_dtl.harga,0), NULL);
    IF v_harga IS NULL THEN
        IF COALESCE(UPPER(BTRIM(v_dtl.issub)), 'F') = 'F' THEN
            SELECT standartcost INTO v_harga
              FROM sc_trx.workingorder_bom_dtl
             WHERE BTRIM(docref) = BTRIM(v_mst.wono)
               AND BTRIM(idbarang) = BTRIM(v_dtl.idbarang)
               AND UPPER(BTRIM(doctype_detail)) = 'MATERIAL'
             LIMIT 1;
        ELSE
            SELECT newcost INTO v_harga
              FROM sc_mst.standart_cost_dtl
             WHERE BTRIM(idbarang) = BTRIM(v_dtl.idbarang)
               AND status = 'F'
             ORDER BY activedate DESC
             LIMIT 1;
        END IF;
    END IF;

    v_harga := COALESCE(v_harga, 0);
    v_nilai := COALESCE(NULLIF(v_dtl.nilai,0), v_harga * COALESCE(v_dtl.qty,0), 0);

    v_idbranch := CASE BTRIM(COALESCE(v_mst.cabang,''))
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE BTRIM(COALESCE(v_mst.cabang,''))::CHAR(20)
    END;

    v_tx_uid := MD5(BTRIM(p_docno)||'|'||COALESCE(v_dtl.uniqueid,'')||'|pr');

    -- Cek exist (hindari ON CONFLICT yang bergantung constraint)
    SELECT EXISTS (
        SELECT 1 FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(p_docno)
          AND uniqueid = v_tx_uid
    ) INTO v_exists;

    IF v_exists THEN
        UPDATE sc_trx.transaction_dt SET
            journal_type  = 'WORECV',
            type_in_out   = 'IN',
            docdate       = COALESCE(v_mst.docdate, CURRENT_DATE),
            idbranch      = v_idbranch,
            cabang        = BTRIM(COALESCE(v_mst.cabang,'')),
            idbarang      = BTRIM(COALESCE(v_dtl.idbarang,'')),
            namabarang    = BTRIM(COALESCE(v_dtl.nmbarang,'')),
            idunit        = BTRIM(COALESCE(v_dtl.unit,'')),
            idarea        = BTRIM(COALESCE(v_mst.idlocation,'')),
            warehouse     = BTRIM(COALESCE(v_mst.idlocation,'')),
            bin           = '',
            batch         = BTRIM(COALESCE(v_mst.batchno,'')),
            lotno         = '',
            qty           = COALESCE(v_dtl.qty,0),
            harga         = v_harga,
            bruto         = v_nilai,
            discount      = 0,
            nilai         = v_nilai,
            dpp           = v_nilai,
            pajak         = 0,
            total         = v_nilai,
            idtax         = 'NON',
            isinclusive   = 'NO',
            currcode      = 'IDR',
            kurs          = 1,
            idcoa         = v_coa_debet,
            counter_idcoa = v_coa_kredit,
            debet_kredit  = 'D',
            keterangan    = COALESCE(v_dtl.description,''),
            updatedby     = v_mst.updateby,
            updateddate   = CURRENT_TIMESTAMP
        WHERE BTRIM(docno) = BTRIM(p_docno)
          AND uniqueid = v_tx_uid;
    ELSE
        INSERT INTO sc_trx.transaction_dt (
            uniqueid, source_uniqueid, docno, doctype, journal_type,
            line_no, docdate, idbranch, cabang, type_in_out,
            ref_docno, ref_doctype, idbarang, namabarang, idunit,
            idarea, warehouse, bin, batch, lotno,
            qty, harga, bruto, discount, nilai, dpp, pajak, total,
            idtax, isinclusive, currcode, kurs,
            idcoa, counter_idcoa, debet_kredit,
            keterangan, createdby
        ) VALUES (
            v_tx_uid, COALESCE(v_dtl.uniqueid,''), BTRIM(p_docno),
            'PENERIMAANBP', 'WORECV',
            GREATEST(COALESCE(v_dtl.idurut,1),1),
            COALESCE(v_mst.docdate, CURRENT_DATE),
            v_idbranch, BTRIM(COALESCE(v_mst.cabang,'')), 'IN',
            BTRIM(COALESCE(v_mst.wono,'')), 'PR',
            BTRIM(COALESCE(v_dtl.idbarang,'')),
            BTRIM(COALESCE(v_dtl.nmbarang,'')),
            BTRIM(COALESCE(v_dtl.unit,'')),
            BTRIM(COALESCE(v_mst.idlocation,'')),
            BTRIM(COALESCE(v_mst.idlocation,'')),
            '', BTRIM(COALESCE(v_mst.batchno,'')), '',
            COALESCE(v_dtl.qty,0), v_harga, v_nilai, 0, v_nilai,
            v_nilai, 0, v_nilai, 'NON','NO','IDR',1,
            v_coa_debet, v_coa_kredit, 'D',
            COALESCE(v_dtl.description,''),
            COALESCE(v_mst.inputby,'SYSTEM')
        );
    END IF;
END;
$$;

ALTER FUNCTION sc_trx.fn_sync_penerimaanbp_dtl_to_transaction(TEXT, TEXT)
    OWNER TO postgres;


-- ============================================================
-- STEP 2 — TRIGGER FUNCTION sc_trx.penerimaanbp_dtl
-- ============================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_penerimaanbp_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        PERFORM sc_trx.fn_sync_penerimaanbp_dtl_to_transaction(NEW.docno, NEW.uniqueid);
        RETURN NEW;

    ELSIF TG_OP = 'UPDATE' THEN
        PERFORM sc_trx.fn_sync_penerimaanbp_dtl_to_transaction(NEW.docno, NEW.uniqueid);
        RETURN NEW;

    ELSIF TG_OP = 'DELETE' THEN
        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND uniqueid = MD5(
                BTRIM(OLD.docno) || '|' ||
                COALESCE(OLD.uniqueid, '') || '|pr'
              );
        RETURN OLD;
    END IF;

    RETURN NEW;
END;
$$;

ALTER FUNCTION sc_trx.fn_penerimaanbp_dtl_to_transaction()
    OWNER TO postgres;


-- ============================================================
-- STEP 3 — TRIGGER
-- ============================================================
DROP TRIGGER IF EXISTS tr_penerimaanbp_dtl_to_transaction
ON sc_trx.penerimaanbp_dtl;

CREATE TRIGGER tr_penerimaanbp_dtl_to_transaction
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.penerimaanbp_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_penerimaanbp_dtl_to_transaction();


-- ============================================================
-- STEP 4 — VERIFIKASI
-- ============================================================
DO $$
DECLARE
    v_ok INT := 0;
BEGIN
    IF to_regprocedure('sc_trx.fn_sync_penerimaanbp_dtl_to_transaction(text,text)') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.fn_penerimaanbp_dtl_to_transaction()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF EXISTS (
        SELECT 1 FROM pg_trigger
        WHERE tgname = 'tr_penerimaanbp_dtl_to_transaction'
          AND tgrelid = 'sc_trx.penerimaanbp_dtl'::regclass
    ) THEN v_ok := v_ok + 1; END IF;

    RAISE NOTICE '=========================================================';
    RAISE NOTICE 'MODUL PENERIMAAN BP → transaction_dt : % / 3 terpasang', v_ok;
    RAISE NOTICE '  Journal type : WORECV (IN)';
    RAISE NOTICE '  COA Debet    : mbarang.ppersediaan per idbarang';
    RAISE NOTICE '  COA Kredit   : konfigurasi_umum.pproduksi';
    RAISE NOTICE '  Filter       : doctype_detail = MATERIAL';
    RAISE NOTICE '=========================================================';

    IF v_ok <> 3 THEN
        RAISE WARNING 'Ada objek modul penerimaanbp yang tidak terpasang!';
    END IF;
END;
$$;

COMMIT;