-- =====================================================================
-- I.Q.A.2 — TRANSFER ANTAR LOKASI (TRFWHS) — FULL REBUILD
-- DOCTYPE = 'TRFWHS'
-- =====================================================================
-- Fitur:
--   - Race-Condition Safe (docno)
--   - Batch/Lot + Unit Conversion + Unit Default
--   - Sync ke transaction_dt dengan iduniq
--   - Primary Key (docno, iduniq)
--   - ✅ FIX: Auto-fill val dari sc_trx.stkblc_avgcost (SELALU)
--   - ✅ FIX: User TIDAK bisa override val (nilai selalu dari avg cost asal)
--   - ✅ FIX: idcoa TIDAK di-set (auto-resolve TAHAP 04)
--   - ✅ FIX: counter_idcoa, debet_kredit TIDAK di-set (bukan manual)
--   - Cabang & idbranch SEMUA dari kolom `cabang`
--   - ✅ FIX: valsum = qty_base × val (bukan qty × val)
-- =====================================================================

BEGIN;

-- =====================================================================
-- 0. DROP SEMUA OBJECT (RERUN-SAFE)
-- =====================================================================

DROP TRIGGER IF EXISTS trg_zy_consume_reservation ON sc_trx.transaction_dt;
DROP TRIGGER IF EXISTS trg_zz_consume_reservation ON sc_trx.transaction_dt;

DROP TRIGGER IF EXISTS tr_tmp_transfer_location_mst ON sc_tmp.transfer_location_mst;
DROP TRIGGER IF EXISTS zz_tmp_transfer_location_consume_res ON sc_tmp.transfer_location_mst;
DROP TRIGGER IF EXISTS tr_trx_transfer_location_mst ON sc_trx.transfer_location_mst;

DROP TRIGGER IF EXISTS trg_tmp_transfer_location_dtl_calc ON sc_tmp.transfer_location_dtl;
DROP TRIGGER IF EXISTS trg_tmp_transfer_location_reserve ON sc_tmp.transfer_location_dtl;
DROP TRIGGER IF EXISTS trg_tmp_transfer_location_release ON sc_tmp.transfer_location_dtl;
DROP TRIGGER IF EXISTS trg_trx_transfer_location_dtl_calc ON sc_trx.transfer_location_dtl;
DROP TRIGGER IF EXISTS tr_transfer_location_dtl_to_transaction ON sc_trx.transfer_location_dtl;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_transfer_location_consume_res() CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.fn_consume_transfer_reservations(TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.fn_release_transfer_reservations(TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.fn_tmp_transfer_location_reserve() CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.fn_tmp_transfer_location_release() CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.fn_tmp_transfer_location_dtl_calc() CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_transfer_location_mst() CASCADE;

DROP FUNCTION IF EXISTS sc_trx.fn_trx_transfer_location_dtl_calc() CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_sync_transfer_location_dtl_to_transaction(TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_transfer_location_dtl_to_transaction() CASCADE;
DROP FUNCTION IF EXISTS sc_trx.tr_trx_transfer_location_mst() CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_sync_transfer_location_doc(TEXT) CASCADE;

-- =====================================================================
-- 1. TABLE HEADER — TMP
-- =====================================================================
DROP TABLE IF EXISTS sc_tmp.transfer_location_mst CASCADE;

CREATE TABLE sc_tmp.transfer_location_mst
(
    docno              VARCHAR(30)   NOT NULL,
    doctype            VARCHAR(20)   DEFAULT 'TRFWHS',
    docdate            VARCHAR(20),
    docref             VARCHAR(30),
    cabang             VARCHAR(30),
    cabang_sent        VARCHAR(30),
    pemohon            VARCHAR(100),
    estpakai           VARCHAR(20),
    idlocation_from    VARCHAR(30),
    idlocation_to      VARCHAR(30),
    idlocation_transit VARCHAR(30),
    status             VARCHAR(6),
    description        TEXT,
    inputby            VARCHAR(50)   NOT NULL,
    inputdate          TIMESTAMP WITHOUT TIME ZONE,
    updateby           VARCHAR(50),
    updatedate         TIMESTAMP WITHOUT TIME ZONE,
    printby            VARCHAR(50),
    printdate          TIMESTAMP WITHOUT TIME ZONE,
    docnotmp           VARCHAR(30),

    CONSTRAINT pk_tmp_transfer_location_mst PRIMARY KEY (docno, inputby)
);

-- =====================================================================
-- 2. TABLE HEADER — TRX
-- =====================================================================
DROP TABLE IF EXISTS sc_trx.transfer_location_mst CASCADE;

CREATE TABLE sc_trx.transfer_location_mst
(
    docno              VARCHAR(30)   NOT NULL,
    doctype            VARCHAR(20)   DEFAULT 'TRFWHS',
    docdate            VARCHAR(20),
    docref             VARCHAR(30),
    cabang             VARCHAR(30),
    cabang_sent        VARCHAR(30),
    pemohon            VARCHAR(100),
    estpakai           VARCHAR(20),
    idlocation_from    VARCHAR(30),
    idlocation_to      VARCHAR(30),
    idlocation_transit VARCHAR(30),
    status             VARCHAR(6),
    description        TEXT,
    inputby            VARCHAR(50),
    inputdate          TIMESTAMP WITHOUT TIME ZONE,
    updateby           VARCHAR(50),
    updatedate         TIMESTAMP WITHOUT TIME ZONE,
    printby            VARCHAR(50),
    printdate          TIMESTAMP WITHOUT TIME ZONE,
    docnotmp           VARCHAR(30),

    CONSTRAINT pk_trx_transfer_location_mst PRIMARY KEY (docno)
);

-- =====================================================================
-- 3. TABLE DETAIL — TMP
-- =====================================================================
DROP TABLE IF EXISTS sc_tmp.transfer_location_dtl CASCADE;

CREATE TABLE sc_tmp.transfer_location_dtl
(
    idurut            BIGSERIAL       NOT NULL,
    docno             VARCHAR(30)     NOT NULL,
    iduniq            TEXT            NOT NULL,
    docref            VARCHAR(30),
    doctype           VARCHAR(20)     DEFAULT 'TRFWHS',

    idbarang          VARCHAR(20),
    nmbarang          VARCHAR(150),

    unit              VARCHAR(20),
    unit_default      VARCHAR(20),
    conversion_factor NUMERIC(18,6)   DEFAULT 1,
    qty               NUMERIC(18,2),
    qty_base          NUMERIC(18,2),
    qtystock          NUMERIC(18,2),

    batch             VARCHAR(100),
    lotno             VARCHAR(100),

    /* ----------------------------------------------------------
       ✅ val dan valsum adalah READ-ONLY dari sistem
       User TIDAK boleh input nilai ini.
       Trigger akan SELALU override dari avg cost gudang asal.
       ---------------------------------------------------------- */
    val               NUMERIC(18,2),
    valsum            NUMERIC(18,2),

    description       TEXT,
    status            VARCHAR(6),

    inputby           VARCHAR(50)     NOT NULL,
    inputdate         TIMESTAMP WITHOUT TIME ZONE,
    updateby          VARCHAR(50),
    updatedate        TIMESTAMP WITHOUT TIME ZONE,

    docnotmp          VARCHAR(30),

    CONSTRAINT pk_tmp_transfer_location_dtl PRIMARY KEY (docno, iduniq)
);

CREATE INDEX idx_tmp_tl_dtl_docno_inputby ON sc_tmp.transfer_location_dtl(docno, inputby);
CREATE INDEX idx_tmp_tl_dtl_barang ON sc_tmp.transfer_location_dtl(idbarang, batch, lotno);
CREATE INDEX idx_tmp_tl_dtl_iduniq ON sc_tmp.transfer_location_dtl(iduniq);

-- =====================================================================
-- 4. TABLE DETAIL — TRX
-- =====================================================================
DROP TABLE IF EXISTS sc_trx.transfer_location_dtl CASCADE;

CREATE TABLE sc_trx.transfer_location_dtl
(
    idurut            BIGSERIAL       NOT NULL,
    docno             VARCHAR(30)     NOT NULL,
    iduniq            TEXT            NOT NULL,
    docref            VARCHAR(30),
    doctype           VARCHAR(20)     DEFAULT 'TRFWHS',

    idbarang          VARCHAR(20),
    nmbarang          VARCHAR(150),

    unit              VARCHAR(20),
    unit_default      VARCHAR(20),
    conversion_factor NUMERIC(18,6)   DEFAULT 1,
    qty               NUMERIC(18,2),
    qty_base          NUMERIC(18,2),
    qtystock          NUMERIC(18,2),

    batch             VARCHAR(100),
    lotno             VARCHAR(100),

    val               NUMERIC(18,2),
    valsum            NUMERIC(18,2),

    description       TEXT,
    status            VARCHAR(6),

    inputby           VARCHAR(50),
    inputdate         TIMESTAMP WITHOUT TIME ZONE,
    updateby          VARCHAR(50),
    updatedate        TIMESTAMP WITHOUT TIME ZONE,

    docnotmp          VARCHAR(30),

    CONSTRAINT pk_trx_transfer_location_dtl PRIMARY KEY (docno, iduniq)
);

CREATE INDEX idx_trx_tl_dtl_docno ON sc_trx.transfer_location_dtl(docno);
CREATE INDEX idx_trx_tl_dtl_barang ON sc_trx.transfer_location_dtl(idbarang, batch, lotno);
CREATE INDEX idx_trx_tl_dtl_iduniq ON sc_trx.transfer_location_dtl(iduniq);

-- =====================================================================
-- 5. FUNCTION CALC — TMP
-- ✅ FIX: val SELALU dari avg cost asal (user TIDAK bisa override)
-- ✅ FIX: valsum = qty_base × val
-- =====================================================================
CREATE OR REPLACE FUNCTION sc_tmp.fn_tmp_transfer_location_dtl_calc()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_hdr            sc_tmp.transfer_location_mst%ROWTYPE;
    v_idlocation_from TEXT;
    v_avg_cost       NUMERIC(18,6);
    v_stk_unitcost   NUMERIC(18,6);
BEGIN

    /* 1. AUTO iduniq */
    IF NEW.iduniq IS NULL OR BTRIM(NEW.iduniq) = '' THEN
        NEW.iduniq := md5(
            'TRFWHS-TMP|'
            || COALESCE(rtrim(NEW.docno), '')     || '|'
            || COALESCE(NEW.inputby, '')          || '|'
            || COALESCE(rtrim(NEW.idbarang), '')  || '|'
            || COALESCE(rtrim(NEW.batch), '')     || '|'
            || COALESCE(rtrim(NEW.lotno), '')     || '|'
            || COALESCE(rtrim(NEW.unit), '')      || '|'
            || COALESCE(NEW.idurut::TEXT, '')
        );
    END IF;

    /* 2. Default conversion_factor & unit_default */
    IF NEW.conversion_factor IS NULL OR NEW.conversion_factor <= 0 THEN
        NEW.conversion_factor := 1;
    END IF;

    IF NEW.unit_default IS NULL OR BTRIM(NEW.unit_default) = '' THEN
        NEW.unit_default := NEW.unit;
    END IF;

    /* 3. Hitung qty_base */
    IF NEW.qty IS NOT NULL THEN
        NEW.qty_base := ROUND(NEW.qty * NEW.conversion_factor, 2);
    END IF;

    /* ================================================================
       4. ✅ FIX UTAMA: AUTO-FILL val DARI AVG COST GUDANG ASAL
       
       SEBELUM: IF COALESCE(NEW.val, 0) <= 0 THEN ...
       SESUDAH: SELALU override, TIDAK ada IF
       
       Ini memastikan user TIDAK BISA mengisi val manual di form.
       Nilai transfer selalu dari avg cost gudang asal.
       ================================================================ */
    
    SELECT * INTO v_hdr
    FROM sc_tmp.transfer_location_mst
    WHERE rtrim(docno) = rtrim(NEW.docno)
      AND inputby      = NEW.inputby;

    IF FOUND THEN
        v_idlocation_from := rtrim(COALESCE(v_hdr.idlocation_from, ''));

        IF v_idlocation_from <> '' 
           AND NEW.idbarang IS NOT NULL 
           AND BTRIM(NEW.idbarang) <> ''
        THEN
            /* Coba ambil dari stkblc_avgcost */
            SELECT ac.avg_cost
            INTO v_avg_cost
            FROM sc_trx.stkblc_avgcost ac
            WHERE BTRIM(ac.idbarang::TEXT)   = BTRIM(NEW.idbarang::TEXT)
              AND BTRIM(ac.idlocation::TEXT) = BTRIM(v_idlocation_from)
              AND BTRIM(COALESCE(ac.batch::TEXT,'')) = BTRIM(COALESCE(NEW.batch::TEXT,''))
            LIMIT 1;

            IF v_avg_cost IS NULL OR v_avg_cost <= 0 THEN
                /* Fallback: ambil dari stkblc unitcost */
                SELECT sb.unitcost
                INTO v_stk_unitcost
                FROM sc_trx.stkblc sb
                WHERE BTRIM(sb.idbarang::TEXT)   = BTRIM(NEW.idbarang::TEXT)
                  AND BTRIM(sb.idlocation::TEXT) = BTRIM(v_idlocation_from)
                  AND BTRIM(COALESCE(sb.batch::TEXT,'')) = BTRIM(COALESCE(NEW.batch::TEXT,''))
                  AND sb.type_in_out = 'IN'
                  AND sb.unitcost > 0
                ORDER BY sb.docdate DESC, sb.id DESC
                LIMIT 1;

                v_avg_cost := COALESCE(v_stk_unitcost, 0);
            END IF;

            /* ✅ SELALU override NEW.val dengan avg cost asal */
            NEW.val := ROUND(COALESCE(v_avg_cost, 0), 2);
        ELSE
            /* Tidak ada info gudang/barang → set 0 */
            NEW.val := 0;
        END IF;
    ELSE
        /* Tidak ada header → set 0 */
        NEW.val := 0;
    END IF;

    /* 5. ✅ Hitung valsum = qty_base × val */
    IF NEW.qty_base IS NOT NULL AND NEW.val IS NOT NULL THEN
        NEW.valsum := ROUND(NEW.qty_base * NEW.val, 2);
    ELSE
        NEW.valsum := 0;
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_tmp_transfer_location_dtl_calc
BEFORE INSERT OR UPDATE ON sc_tmp.transfer_location_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.fn_tmp_transfer_location_dtl_calc();

-- =====================================================================
-- 6. FUNCTION CALC — TRX
-- ✅ FIX: val SELALU dari avg cost asal
-- =====================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_trx_transfer_location_dtl_calc()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_hdr            sc_trx.transfer_location_mst%ROWTYPE;
    v_idlocation_from TEXT;
    v_avg_cost       NUMERIC(18,6);
    v_stk_unitcost   NUMERIC(18,6);
BEGIN

    IF NEW.iduniq IS NULL OR BTRIM(NEW.iduniq) = '' THEN
        NEW.iduniq := md5(
            'TRFWHS-TRX|'
            || COALESCE(rtrim(NEW.docno), '')     || '|'
            || COALESCE(rtrim(NEW.idbarang), '')  || '|'
            || COALESCE(rtrim(NEW.batch), '')     || '|'
            || COALESCE(rtrim(NEW.lotno), '')     || '|'
            || COALESCE(rtrim(NEW.unit), '')      || '|'
            || COALESCE(NEW.idurut::TEXT, '')
        );
    END IF;

    IF NEW.conversion_factor IS NULL OR NEW.conversion_factor <= 0 THEN
        NEW.conversion_factor := 1;
    END IF;

    IF NEW.unit_default IS NULL OR BTRIM(NEW.unit_default) = '' THEN
        NEW.unit_default := NEW.unit;
    END IF;

    IF NEW.qty IS NOT NULL THEN
        NEW.qty_base := ROUND(NEW.qty * NEW.conversion_factor, 2);
    END IF;

    /* ✅ SELALU override val dari avg cost gudang asal */
    SELECT * INTO v_hdr
    FROM sc_trx.transfer_location_mst
    WHERE rtrim(docno) = rtrim(NEW.docno);

    IF FOUND THEN
        v_idlocation_from := rtrim(COALESCE(v_hdr.idlocation_from, ''));

        IF v_idlocation_from <> '' 
           AND NEW.idbarang IS NOT NULL 
           AND BTRIM(NEW.idbarang) <> ''
        THEN
            SELECT ac.avg_cost
            INTO v_avg_cost
            FROM sc_trx.stkblc_avgcost ac
            WHERE BTRIM(ac.idbarang::TEXT)   = BTRIM(NEW.idbarang::TEXT)
              AND BTRIM(ac.idlocation::TEXT) = BTRIM(v_idlocation_from)
              AND BTRIM(COALESCE(ac.batch::TEXT,'')) = BTRIM(COALESCE(NEW.batch::TEXT,''))
            LIMIT 1;

            IF v_avg_cost IS NULL OR v_avg_cost <= 0 THEN
                SELECT sb.unitcost
                INTO v_stk_unitcost
                FROM sc_trx.stkblc sb
                WHERE BTRIM(sb.idbarang::TEXT)   = BTRIM(NEW.idbarang::TEXT)
                  AND BTRIM(sb.idlocation::TEXT) = BTRIM(v_idlocation_from)
                  AND BTRIM(COALESCE(sb.batch::TEXT,'')) = BTRIM(COALESCE(NEW.batch::TEXT,''))
                  AND sb.type_in_out = 'IN'
                  AND sb.unitcost > 0
                ORDER BY sb.docdate DESC, sb.id DESC
                LIMIT 1;

                v_avg_cost := COALESCE(v_stk_unitcost, 0);
            END IF;

            NEW.val := ROUND(COALESCE(v_avg_cost, 0), 2);
        ELSE
            NEW.val := 0;
        END IF;
    ELSE
        NEW.val := 0;
    END IF;

    /* ✅ Hitung valsum = qty_base × val */
    IF NEW.qty_base IS NOT NULL AND NEW.val IS NOT NULL THEN
        NEW.valsum := ROUND(NEW.qty_base * NEW.val, 2);
    ELSE
        NEW.valsum := 0;
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_trx_transfer_location_dtl_calc
BEFORE INSERT OR UPDATE ON sc_trx.transfer_location_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_trx_transfer_location_dtl_calc();

-- =====================================================================
-- 7. TRIGGER TMP → TRX
-- =====================================================================
CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_transfer_location_mst()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $BODY$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_lock_key    BIGINT;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_num         TEXT;
    v_num_int     INTEGER;
BEGIN
    IF NOT (OLD.status = 'E' AND NEW.status = 'F') THEN
        RETURN NEW;
    END IF;

    v_docno     := rtrim(NEW.docno);
    v_inputby   := NEW.inputby;

    IF COALESCE(NEW.docnotmp, '') = '' THEN

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.transfer_location_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION 'Format DOCNO tidak valid: %', v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno := v_base_docno ||
                lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        INSERT INTO sc_trx.transfer_location_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit,
            status, description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp
        )
        SELECT v_docno, 'TRFWHS', docdate, docref, cabang, cabang_sent, pemohon,
               estpakai, idlocation_from, idlocation_to, idlocation_transit,
               'F', description, inputby, inputdate, updateby, updatedate,
               printby, printdate, docnotmp
        FROM sc_tmp.transfer_location_mst
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

        INSERT INTO sc_trx.transfer_location_dtl (
            docno, iduniq, docref, doctype, idbarang, nmbarang,
            unit, unit_default, conversion_factor, qty, qty_base, qtystock,
            batch, lotno,
            description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            docnotmp
        )
        SELECT v_docno, iduniq, docref, 'TRFWHS', idbarang, nmbarang,
               unit, unit_default, conversion_factor, qty, qty_base, qtystock,
               batch, lotno,
               description, 'F', val, valsum,
               inputby, inputdate, updateby, updatedate,
               docnotmp
        FROM sc_tmp.transfer_location_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.transfer_location_dtl
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.transfer_location_mst
        WHERE rtrim(docno) = rtrim(OLD.docno)
          AND inputby = v_inputby;

    ELSIF COALESCE(NEW.docnotmp, '') <> '' THEN

        DELETE FROM sc_trx.transfer_location_dtl WHERE docno = NEW.docnotmp;
        DELETE FROM sc_trx.transfer_location_mst WHERE docno = NEW.docnotmp;

        INSERT INTO sc_trx.transfer_location_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit,
            status, description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp
        )
        SELECT NEW.docnotmp, 'TRFWHS', docdate, docref, cabang, cabang_sent, pemohon,
               estpakai, idlocation_from, idlocation_to, idlocation_transit,
               'F', description, inputby, inputdate, updateby, updatedate,
               printby, printdate, docnotmp
        FROM sc_tmp.transfer_location_mst
        WHERE TRIM(docno) = TRIM(NEW.docnotmp)
          AND inputby = v_inputby;

        INSERT INTO sc_trx.transfer_location_dtl (
            docno, iduniq, docref, doctype, idbarang, nmbarang,
            unit, unit_default, conversion_factor, qty, qty_base, qtystock,
            batch, lotno,
            description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            docnotmp
        )
        SELECT NEW.docnotmp, iduniq, docref, 'TRFWHS', idbarang, nmbarang,
               unit, unit_default, conversion_factor, qty, qty_base, qtystock,
               batch, lotno,
               description, 'F', val, valsum,
               inputby, inputdate, updateby, updatedate,
               docnotmp
        FROM sc_tmp.transfer_location_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.transfer_location_dtl
        WHERE rtrim(docno) = rtrim(NEW.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.transfer_location_mst
        WHERE rtrim(docno) = rtrim(NEW.docno)
          AND inputby = v_inputby;
    END IF;

    IF OLD.status = 'E' AND NEW.status = 'C' THEN
        IF NEW.printby IS NOT NULL AND NEW.printby <> ''
           AND NEW.printdate IS NOT NULL THEN
            UPDATE sc_trx.transfer_location_mst
            SET status = 'P'
            WHERE docno = NEW.docnotmp;
        ELSE
            UPDATE sc_trx.transfer_location_mst
            SET status = 'F'
            WHERE docno = NEW.docnotmp;
        END IF;

        DELETE FROM sc_tmp.transfer_location_dtl
        WHERE docno = NEW.docno AND inputby = v_inputby;

        DELETE FROM sc_tmp.transfer_location_mst
        WHERE docno = NEW.docno AND inputby = v_inputby;
    END IF;

    RETURN NEW;
END;
$BODY$;

CREATE TRIGGER tr_tmp_transfer_location_mst
AFTER UPDATE ON sc_tmp.transfer_location_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_transfer_location_mst();

-- =====================================================================
-- 8. TRIGGER TRX → TMP (edit)
-- =====================================================================
CREATE OR REPLACE FUNCTION sc_trx.tr_trx_transfer_location_mst()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $BODY$
BEGIN
    IF (OLD.status = 'F' AND NEW.status = 'E') THEN

        INSERT INTO sc_tmp.transfer_location_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent, pemohon,
            estpakai, idlocation_from, idlocation_to, idlocation_transit,
            status, description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp
        )
        SELECT docno, 'TRFWHS', docdate, docref, cabang, cabang_sent, pemohon,
               estpakai, idlocation_from, idlocation_to, idlocation_transit,
               'E', description, inputby, inputdate, updateby, updatedate,
               printby, printdate, docno
        FROM sc_trx.transfer_location_mst
        WHERE TRIM(docno) = NEW.docno;

        INSERT INTO sc_tmp.transfer_location_dtl (
            docno, iduniq, docref, doctype, idbarang, nmbarang,
            unit, unit_default, conversion_factor, qty, qty_base, qtystock,
            batch, lotno,
            description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            docnotmp
        )
        SELECT docno, iduniq, docref, 'TRFWHS', idbarang, nmbarang,
               unit, unit_default, conversion_factor, qty, qty_base, qtystock,
               batch, lotno,
               description, 'F', val, valsum,
               inputby, inputdate, updateby, updatedate,
               docno
        FROM sc_trx.transfer_location_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$BODY$;

CREATE TRIGGER tr_trx_transfer_location_mst
AFTER UPDATE ON sc_trx.transfer_location_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_trx_transfer_location_mst();

-- =====================================================================
-- 9. FUNCTION SYNC — DETAIL → TRANSACTION_DT
-- ✅ FIX: v_valsum = v_val × v_qty_base
-- ✅ FIX: idcoa/counter_idcoa/debet_kredit TIDAK di-set
-- =====================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_sync_transfer_location_dtl_to_transaction(
    p_docno    TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_hdr             sc_trx.transfer_location_mst%ROWTYPE;
    v_dtl             sc_trx.transfer_location_dtl%ROWTYPE;
    v_journal         CHAR(6);
    v_idbranch        CHAR(20);
    v_qty_base        NUMERIC(18,2);
    v_val             NUMERIC(18,2);
    v_valsum          NUMERIC(18,2);
    v_from            TEXT;
    v_to              TEXT;
    v_src_uid         TEXT;
    v_uid             TEXT;
    v_type            CHAR(3);
    v_loc             TEXT;
    v_cabang          TEXT;
    v_step            TEXT;
    v_docdate         DATE;
    v_ref_docno       TEXT;
    v_idbarang        TEXT;
    v_nmbarang        TEXT;
    v_unit_default    TEXT;
    v_batch           TEXT;
    v_lotno           TEXT;
    v_keterangan      TEXT;
BEGIN
    SELECT * INTO v_hdr
    FROM sc_trx.transfer_location_mst
    WHERE rtrim(docno) = rtrim(p_docno);
    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_dtl
    FROM sc_trx.transfer_location_dtl
    WHERE rtrim(docno) = rtrim(p_docno)
      AND iduniq = p_uniqueid;
    IF NOT FOUND THEN RETURN; END IF;

    v_docdate :=
        CASE
            WHEN v_hdr.docdate IS NULL OR BTRIM(v_hdr.docdate::TEXT) = ''
            THEN CURRENT_DATE
            ELSE v_hdr.docdate::DATE
        END;

    v_ref_docno := COALESCE(rtrim(v_dtl.docref), '');
    v_idbarang  := COALESCE(rtrim(v_dtl.idbarang), '');
    v_nmbarang  := COALESCE(rtrim(v_dtl.nmbarang), '');
    v_batch     := COALESCE(rtrim(v_dtl.batch), '');
    v_lotno     := COALESCE(rtrim(v_dtl.lotno), '');
    v_keterangan := COALESCE(rtrim(v_dtl.description), '');

    v_unit_default := COALESCE(
        NULLIF(rtrim(v_dtl.unit_default), ''),
        NULLIF(rtrim(v_dtl.unit), ''),
        ''
    );

    /* ✅ Journal type: TRFWHS jika cabang sama, BRNTRF jika beda */
    v_journal := CASE
        WHEN rtrim(COALESCE(v_hdr.cabang,'')) = rtrim(COALESCE(v_hdr.cabang_sent,''))
            THEN 'TRFWHS'::CHAR(6)
        ELSE 'BRNTRF'::CHAR(6)
    END;

    v_idbranch := CASE rtrim(COALESCE(v_hdr.cabang,''))
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE COALESCE(NULLIF(rtrim(v_hdr.cabang), ''), 'JTS')::CHAR(20)
    END;

    v_cabang := COALESCE(rtrim(v_hdr.cabang), '');

    /* ✅ Hitung v_valsum dari v_val × v_qty_base */
    v_qty_base := COALESCE(v_dtl.qty_base, v_dtl.qty, 0);
    v_val      := COALESCE(v_dtl.val, 0);
    v_valsum   := ROUND(v_val * v_qty_base, 2);

    v_src_uid  := COALESCE(v_dtl.iduniq, '');

    v_from := rtrim(COALESCE(v_hdr.idlocation_from, ''));
    v_to   := rtrim(COALESCE(v_hdr.idlocation_to, ''));

    IF v_from = '' OR v_to = '' THEN RETURN; END IF;
    IF v_from = v_to THEN RETURN; END IF;

    FOR v_step IN SELECT unnest(ARRAY['OUT-FROM', 'IN-TO'])
    LOOP
        v_type := CASE WHEN v_step = 'OUT-FROM' THEN 'OUT' ELSE 'IN' END;

        v_loc := CASE v_step
            WHEN 'OUT-FROM' THEN v_from
            WHEN 'IN-TO'    THEN v_to
        END;

        v_uid := v_type || '-' || v_src_uid;

        /* ==============================================================
           ✅ FIX: idcoa, counter_idcoa, debet_kredit, debet, kredit
                    TIDAK di-set → biar TAHAP 04 auto-resolve
           
           INSERT hanya field yang benar-benar relevan.
           ============================================================== */
        INSERT INTO sc_trx.transaction_dt (
            uniqueid, source_uniqueid, iduniq, docno, doctype, journal_type,
            line_no, docdate, idbranch, cabang, type_in_out,
            ref_docno, ref_doctype,
            idbarang, namabarang, idunit, idarea, warehouse,
            bin, batch, lotno,
            qty, harga, bruto, discount, nilai, dpp, pajak, total,

            source_table,
            source_id,
            source_line_id,
            kdcustomer, ncustomer,
            kdsupplier, nsupplier,
            idtax, isinclusive,
            currcode, kurs,
            keterangan,

            createdby
        )
        VALUES (
            v_uid, v_src_uid, v_src_uid, rtrim(p_docno), 'TRFWHS', v_journal,
            v_dtl.idurut, v_docdate, v_idbranch, v_cabang, v_type,
            v_ref_docno, 'TRF',
            v_idbarang, v_nmbarang, v_unit_default, '',
            v_loc,
            '', v_batch, v_lotno,
            v_qty_base, v_val, v_valsum, 0, v_valsum, 0, 0, v_valsum,

            'sc_trx.transfer_location_dtl',
            v_dtl.idurut,
            v_dtl.idurut,
            '', '',
            '', '',
            'NON', 'NO',
            'IDR', 1,
            v_keterangan,

            COALESCE(rtrim(v_hdr.inputby), '')
        )
        ON CONFLICT (uniqueid) DO UPDATE
        SET
            qty         = EXCLUDED.qty,
            harga       = EXCLUDED.harga,
            bruto       = EXCLUDED.bruto,
            nilai       = EXCLUDED.nilai,
            total       = EXCLUDED.total,
            idunit      = EXCLUDED.idunit,
            warehouse   = EXCLUDED.warehouse,
            idarea      = EXCLUDED.idarea,
            cabang      = EXCLUDED.cabang,
            idbranch    = EXCLUDED.idbranch,
            batch       = EXCLUDED.batch,
            lotno       = EXCLUDED.lotno,
            iduniq      = EXCLUDED.iduniq,

            source_table   = EXCLUDED.source_table,
            source_id      = EXCLUDED.source_id,
            source_line_id = EXCLUDED.source_line_id,
            idtax          = EXCLUDED.idtax,
            isinclusive    = EXCLUDED.isinclusive,
            currcode       = EXCLUDED.currcode,
            kurs           = EXCLUDED.kurs,
            keterangan     = EXCLUDED.keterangan,

            updateddate = CURRENT_TIMESTAMP;
    END LOOP;

END;
$$;

-- =====================================================================
-- 10. TRIGGER FUNCTION — INSERT / UPDATE / DELETE
-- =====================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_transfer_location_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno   TEXT;
    v_iduniq  TEXT;
BEGIN
    IF TG_OP = 'DELETE' THEN
        v_docno  := rtrim(OLD.docno);
        v_iduniq := COALESCE(OLD.iduniq, '');

        IF v_iduniq <> '' THEN
            DELETE FROM sc_trx.transaction_dt
            WHERE iduniq = v_iduniq
              AND rtrim(docno) = v_docno;
        ELSE
            DELETE FROM sc_trx.transaction_dt
            WHERE rtrim(docno) = v_docno
              AND source_uniqueid = v_iduniq;
        END IF;

        RETURN OLD;
    ELSE
        PERFORM sc_trx.fn_sync_transfer_location_dtl_to_transaction(
            NEW.docno,
            NEW.iduniq
        );
        RETURN NEW;
    END IF;
END;
$$;

CREATE TRIGGER tr_transfer_location_dtl_to_transaction
AFTER INSERT OR UPDATE OR DELETE ON sc_trx.transfer_location_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_transfer_location_dtl_to_transaction();

-- =====================================================================
-- 11. VERIFIKASI
-- =====================================================================
DO $$
DECLARE
    v_tmp_pk     TEXT;
    v_trx_pk     TEXT;
    v_trg_count  INTEGER;
BEGIN

    SELECT string_agg(kcu.column_name, ', ' ORDER BY kcu.ordinal_position)
    INTO v_tmp_pk
    FROM information_schema.table_constraints tc
    JOIN information_schema.key_column_usage kcu
      ON kcu.constraint_name = tc.constraint_name
     AND kcu.table_schema    = tc.table_schema
    WHERE tc.table_schema   = 'sc_tmp'
      AND tc.table_name     = 'transfer_location_dtl'
      AND tc.constraint_type = 'PRIMARY KEY';

    SELECT string_agg(kcu.column_name, ', ' ORDER BY kcu.ordinal_position)
    INTO v_trx_pk
    FROM information_schema.table_constraints tc
    JOIN information_schema.key_column_usage kcu
      ON kcu.constraint_name = tc.constraint_name
     AND kcu.table_schema    = tc.table_schema
    WHERE tc.table_schema   = 'sc_trx'
      AND tc.table_name     = 'transfer_location_dtl'
      AND tc.constraint_type = 'PRIMARY KEY';

    SELECT COUNT(*)
    INTO v_trg_count
    FROM pg_trigger
    WHERE NOT tgisinternal
      AND tgname IN (
          'trg_tmp_transfer_location_dtl_calc',
          'trg_trx_transfer_location_dtl_calc',
          'tr_tmp_transfer_location_mst',
          'tr_trx_transfer_location_mst',
          'tr_transfer_location_dtl_to_transaction'
      );

    RAISE NOTICE '============================================================';
    RAISE NOTICE 'TRANSFER LOCATION REBUILD : OK';
    RAISE NOTICE 'PK sc_tmp.transfer_location_dtl = %', v_tmp_pk;
    RAISE NOTICE 'PK sc_trx.transfer_location_dtl = %', v_trx_pk;
    RAISE NOTICE 'Trigger terpasang = %', v_trg_count;
    RAISE NOTICE '============================================================';
    RAISE NOTICE '✅ FIX APPLIED:';
    RAISE NOTICE '  1. val SELALU dari avg cost gudang asal';
    RAISE NOTICE '     (user TIDAK bisa override)';
    RAISE NOTICE '  2. valsum = qty_base × val (bukan qty × val)';
    RAISE NOTICE '  3. idcoa TIDAK di-set → auto-resolve TAHAP 04';
    RAISE NOTICE '  4. counter_idcoa, debet_kredit TIDAK di-set';
    RAISE NOTICE '     (bukan manual accounting)';
    RAISE NOTICE '============================================================';

    IF v_tmp_pk IS DISTINCT FROM 'docno, iduniq' THEN
        RAISE EXCEPTION 'PK sc_tmp.transfer_location_dtl TIDAK sesuai: %', v_tmp_pk;
    END IF;

    IF v_trx_pk IS DISTINCT FROM 'docno, iduniq' THEN
        RAISE EXCEPTION 'PK sc_trx.transfer_location_dtl TIDAK sesuai: %', v_trx_pk;
    END IF;

    IF v_trg_count <> 5 THEN
        RAISE EXCEPTION 'Trigger terpasang hanya %, harus 5', v_trg_count;
    END IF;

END;
$$;

COMMIT;