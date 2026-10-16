-- ============================================================
-- PMK BRNG — REBUILD FINAL PEMAKAIAN BARANG
-- DROP objek modul PMK SAJA.
-- TAHAP 01-33 TIDAK DISENTUH.
--
-- journal_type = 'STKOUT'  (mapping operational)
-- idcoa         = costcenter.pbiaya          (DEBIT)   ← untuk trace
-- counter_idcoa = mbarang.ppersediaan        (KREDIT)  ← untuk trace
-- debet_kredit  = 'D'
-- Nilai diambil dari valsum, fallback avg_cost × qty
-- ============================================================

BEGIN;

-- ============================================================
-- STEP 1 — DROP SEMUA OBJEK MILIK MODUL PMK
-- ============================================================

DROP TRIGGER IF EXISTS tr_tmp_pmk_brng_mst            ON sc_tmp.pmk_brng_mst;
DROP TRIGGER IF EXISTS tr_trx_pmk_brng_mst            ON sc_trx.pmk_brng_mst;
DROP TRIGGER IF EXISTS tr_pmk_brng_dtl_to_transaction ON sc_trx.pmk_brng_dtl;
DROP TRIGGER IF EXISTS tr_tmp_pmk_brng_dtl_plot_coa   ON sc_tmp.pmk_brng_dtl;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_pmk_brng_mst()           CASCADE;
DROP FUNCTION IF EXISTS sc_trx.tr_trx_pmk_brng_mst()           CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_sync_pmk_dtl_to_transaction(TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_pmk_dtl_to_transaction()      CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_resolve_pmk_expense_coa(TEXT, TEXT, TEXT) CASCADE;
DROP FUNCTION IF EXISTS sc_tmp.fn_pmk_dtl_plot_coa()            CASCADE;

DROP TABLE IF EXISTS sc_tmp.pmk_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.pmk_brng_mst CASCADE;
DROP TABLE IF EXISTS sc_trx.pmk_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.pmk_brng_mst CASCADE;

-- ============================================================
-- STEP 2 — CREATE TABLE
-- ============================================================

CREATE TABLE sc_tmp.pmk_brng_mst
(
    idurut              BIGSERIAL,
    docno               CHARACTER(30) NOT NULL,
    doctype             CHARACTER(20) DEFAULT 'pmk_brng',
    docdate             CHARACTER(20),
    docref              CHARACTER(30),
    cabang              CHARACTER(30),
    cabang_sent         CHARACTER(30),
    pemohon             CHARACTER(100),
    estpakai            CHARACTER(20),
    idlocation_from     CHARACTER(30),
    idlocation_to       CHARACTER(30),
    idlocation_transit  CHARACTER(30),
    status              CHARACTER(6),
    description         TEXT,
    inputby             VARCHAR(50),
    inputdate           TIMESTAMP WITHOUT TIME ZONE,
    updateby            VARCHAR(50),
    updatedate          TIMESTAMP WITHOUT TIME ZONE,
    printby             VARCHAR(50),
    printdate           TIMESTAMP WITHOUT TIME ZONE,
    docnotmp            CHARACTER(30),
    idcostcenter        CHARACTER(10),
    nmcostcenter        CHARACTER(100),
    CONSTRAINT pk_tmp_pmk_brng_mst PRIMARY KEY (docno)
);

CREATE TABLE sc_trx.pmk_brng_mst
(
    idurut              BIGINT,
    docno               CHARACTER(30) NOT NULL,
    doctype             CHARACTER(20) DEFAULT 'pmk_brng',
    docdate             CHARACTER(20),
    docref              CHARACTER(30),
    cabang              CHARACTER(30),
    cabang_sent         CHARACTER(30),
    pemohon             CHARACTER(100),
    estpakai            CHARACTER(20),
    idlocation_from     CHARACTER(30),
    idlocation_to       CHARACTER(30),
    idlocation_transit  CHARACTER(30),
    status              CHARACTER(6),
    description         TEXT,
    inputby             VARCHAR(50),
    inputdate           TIMESTAMP WITHOUT TIME ZONE,
    updateby            VARCHAR(50),
    updatedate          TIMESTAMP WITHOUT TIME ZONE,
    printby             VARCHAR(50),
    printdate           TIMESTAMP WITHOUT TIME ZONE,
    docnotmp            CHARACTER(30),
    idcostcenter        CHARACTER(10),
    nmcostcenter        CHARACTER(100),
    CONSTRAINT pk_trx_pmk_brng_mst PRIMARY KEY (docno)
);

CREATE TABLE sc_tmp.pmk_brng_dtl
(
    idurut              BIGSERIAL PRIMARY KEY,
    docno               CHARACTER(30) NOT NULL,
    docref              CHARACTER(30),
    doctype             CHARACTER(20) DEFAULT 'pmk_brng',
    idbarang            CHARACTER(20),
    nmbarang            CHARACTER(150),
    unit                CHARACTER(20),
    qtystock            NUMERIC(18,2),
    qty                 NUMERIC(18,2),
    description         TEXT,
    status              CHARACTER(6),
    val                 NUMERIC(18,2),
    valsum              NUMERIC(18,2),
    inputby             CHARACTER(50),
    inputdate           TIMESTAMP WITHOUT TIME ZONE,
    updateby            CHARACTER(50),
    updatedate          TIMESTAMP WITHOUT TIME ZONE,
    iduniq              TEXT,
    docnotmp            CHARACTER(30),
    idcostcenter        CHARACTER(10),
    nmcostcenter        CHARACTER(100),
    pbiaya              CHARACTER(30),
    batch               CHARACTER(100),
    idlocation          CHARACTER(10),
    idcoa               CHARACTER(20),
    counter_idcoa       CHARACTER(20),
    conversion_factor   NUMERIC(18,2) DEFAULT 1,
    unit_default        CHARACTER(20),
    qty_base            NUMERIC(18,6) DEFAULT 0
);

CREATE TABLE sc_trx.pmk_brng_dtl
(
    idurut              INTEGER,
    docno               CHARACTER(30) NOT NULL,
    docref              CHARACTER(30),
    doctype             CHARACTER(20) DEFAULT 'pmk_brng',
    idbarang            CHARACTER(20),
    nmbarang            CHARACTER(150),
    unit                CHARACTER(20),
    qtystock            NUMERIC(18,2),
    qty                 NUMERIC(18,2),
    description         TEXT,
    status              CHARACTER(6),
    val                 NUMERIC(18,2),
    valsum              NUMERIC(18,2),
    inputby             CHARACTER(50),
    inputdate           TIMESTAMP WITHOUT TIME ZONE,
    updateby            CHARACTER(50),
    updatedate          TIMESTAMP WITHOUT TIME ZONE,
    iduniq              TEXT,
    docnotmp            CHARACTER(30),
    idcostcenter        CHARACTER(10),
    nmcostcenter        CHARACTER(100),
    pbiaya              CHARACTER(30),
    batch               CHARACTER(100),
    idlocation          CHARACTER(10),
    idcoa               CHARACTER(20),
    counter_idcoa       CHARACTER(20),
    conversion_factor   NUMERIC(18,2) DEFAULT 1,
    unit_default        CHARACTER(20),
    qty_base            NUMERIC(18,6) DEFAULT 0
);

-- ============================================================
-- STEP 3 — INDEX
-- ============================================================

CREATE INDEX idx_tmp_pmk_brng_dtl_docno
    ON sc_tmp.pmk_brng_dtl(docno);

CREATE INDEX idx_trx_pmk_brng_dtl_docno
    ON sc_trx.pmk_brng_dtl(docno);

CREATE INDEX idx_trx_pmk_brng_dtl_iduniq
    ON sc_trx.pmk_brng_dtl(iduniq);

CREATE INDEX idx_trx_pmk_brng_dtl_idbarang
    ON sc_trx.pmk_brng_dtl(idbarang);

-- ============================================================
-- STEP 4 — FUNCTION: RESOLVER COA BEBAN
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.fn_resolve_pmk_expense_coa(
    p_idcostcenter TEXT,
    p_idbarang     TEXT,
    p_fallback     TEXT DEFAULT ''
)
RETURNS VARCHAR(20)
LANGUAGE plpgsql
STABLE
AS $$
DECLARE
    v_coa      TEXT;
BEGIN
    IF NULLIF(BTRIM(COALESCE(p_idcostcenter, '')), '') IS NOT NULL THEN
        SELECT NULLIF(BTRIM(cc.pbiaya::TEXT), '')
        INTO v_coa
        FROM sc_mst.costcenter cc
        WHERE BTRIM(cc.idcostcenter::TEXT) = BTRIM(p_idcostcenter)
        LIMIT 1;
    END IF;

    IF v_coa IS NULL THEN
        v_coa := NULLIF(BTRIM(COALESCE(p_fallback, '')), '');
    END IF;

    IF v_coa IS NULL
       AND NULLIF(BTRIM(COALESCE(p_idbarang, '')), '') IS NOT NULL
    THEN
        SELECT NULLIF(BTRIM(to_jsonb(b)->>'pcogs'), '')
        INTO v_coa
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang') = BTRIM(p_idbarang)
        LIMIT 1;
    END IF;

    IF v_coa IS NULL THEN
        SELECT NULLIF(BTRIM(to_jsonb(k)->>'hpp'), '')
        INTO v_coa
        FROM sc_mst.konfigurasi_umum k
        LIMIT 1;
    END IF;

    IF v_coa IS NOT NULL AND NOT EXISTS (
        SELECT 1 FROM sc_mst.coa
        WHERE BTRIM(idcoa::TEXT) = BTRIM(v_coa)
    ) THEN
        RAISE EXCEPTION
            'COA beban % (dari cost center %) tidak ditemukan di sc_mst.coa',
            v_coa, COALESCE(p_idcostcenter, '');
    END IF;

    RETURN v_coa;
END;
$$;

-- ============================================================
-- STEP 5 — TRIGGER PLOT COA DI sc_tmp.pmk_brng_dtl
-- ============================================================

CREATE OR REPLACE FUNCTION sc_tmp.fn_pmk_dtl_plot_coa()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_ppersediaan   TEXT;
    v_pbiaya        TEXT;
    v_nmcostcenter  TEXT;
    v_conv          NUMERIC(18,6);
    v_qty           NUMERIC(18,6);
BEGIN
    IF TG_OP = 'UPDATE'
       AND COALESCE(BTRIM(NEW.idcoa::TEXT), '') <> ''
       AND COALESCE(BTRIM(NEW.counter_idcoa::TEXT), '') <> ''
       AND NEW.idbarang     IS NOT DISTINCT FROM OLD.idbarang
       AND NEW.idcostcenter IS NOT DISTINCT FROM OLD.idcostcenter
    THEN
        NULL;
    ELSE
        IF NULLIF(BTRIM(COALESCE(NEW.idbarang::TEXT, '')), '') IS NOT NULL THEN
            SELECT NULLIF(BTRIM(to_jsonb(b)->>'ppersediaan'), '')
            INTO v_ppersediaan
            FROM sc_mst.mbarang b
            WHERE BTRIM(to_jsonb(b)->>'idbarang') = BTRIM(NEW.idbarang::TEXT)
            LIMIT 1;
        END IF;

        IF NULLIF(BTRIM(COALESCE(NEW.idcostcenter::TEXT, '')), '') IS NOT NULL THEN
            SELECT
                NULLIF(BTRIM(cc.pbiaya::TEXT), ''),
                NULLIF(BTRIM(cc.nmcostcenter::TEXT), '')
            INTO v_pbiaya, v_nmcostcenter
            FROM sc_mst.costcenter cc
            WHERE BTRIM(cc.idcostcenter::TEXT)
                  = BTRIM(NEW.idcostcenter::TEXT)
            LIMIT 1;
        END IF;

        NEW.idcoa         := COALESCE(v_pbiaya, '');        -- DEBIT
        NEW.counter_idcoa := COALESCE(v_ppersediaan, '');   -- KREDIT
        NEW.pbiaya        := COALESCE(v_pbiaya, '');

        IF NULLIF(BTRIM(COALESCE(NEW.nmcostcenter::TEXT, '')), '') IS NULL THEN
            NEW.nmcostcenter := COALESCE(v_nmcostcenter, '');
        END IF;
    END IF;

    IF TG_OP = 'INSERT'
       OR NEW.qty                IS DISTINCT FROM OLD.qty
       OR NEW.conversion_factor  IS DISTINCT FROM OLD.conversion_factor
       OR NEW.qty_base           IS NULL
    THEN
        v_qty  := COALESCE(NEW.qty, 0);
        v_conv := COALESCE(NULLIF(NEW.conversion_factor, 0), 1);
        IF v_conv <= 0 THEN v_conv := 1; END IF;
        NEW.qty_base := ROUND(v_qty * v_conv, 6);
    END IF;

    IF COALESCE(NEW.conversion_factor, 0) <= 0 THEN
        NEW.conversion_factor := 1;
    END IF;

    NEW.qty_base := COALESCE(NEW.qty_base, 0);
    NEW.status   := COALESCE(NULLIF(BTRIM(NEW.status::TEXT), ''), 'E');

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_tmp_pmk_brng_dtl_plot_coa
BEFORE INSERT OR UPDATE
ON sc_tmp.pmk_brng_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.fn_pmk_dtl_plot_coa();

-- ============================================================
-- STEP 6 — SYNC DETAIL -> TRANSACTION_DT
-- ============================================================
-- journal_type  = 'STKOUT'
-- Nilai diambil dari valsum, fallback avg_cost × qty
-- idcoa/counter_idcoa tetap diisi untuk trace

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_pmk_dtl_to_transaction(
    p_docno    TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_mst        sc_trx.pmk_brng_mst%ROWTYPE;
    v_dtl        sc_trx.pmk_brng_dtl%ROWTYPE;
    v_journal    CHAR(6) := 'STKOUT';
    v_type       CHAR(3) := 'OUT';
    v_tx_uid     TEXT;
    v_idbranch   CHAR(20);
    v_idlocation TEXT;
    v_currcode   CHAR(3);
    v_kurs       NUMERIC(18,6);
    v_idtax      VARCHAR(20);
    v_isincl     CHAR(3);
    v_qty        NUMERIC(18,6);
    v_harga      NUMERIC(18,6);
    v_nilai      NUMERIC(18,2);
    v_dpp        NUMERIC(18,2);
    v_pajak      NUMERIC(18,2);
    v_total      NUMERIC(18,2);
    v_bruto      NUMERIC(18,2);
    v_exists     BOOLEAN;
    v_keterangan TEXT;
    v_idcoa      VARCHAR(20);
    v_counter    VARCHAR(20);
BEGIN
    SELECT * INTO v_mst
    FROM sc_trx.pmk_brng_mst
    WHERE BTRIM(docno) = BTRIM(p_docno);

    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_dtl
    FROM sc_trx.pmk_brng_dtl
    WHERE BTRIM(docno) = BTRIM(p_docno)
      AND iduniq = p_uniqueid;

    IF NOT FOUND THEN RETURN; END IF;

    v_idbranch := CASE BTRIM(COALESCE(v_mst.cabang, ''))
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE BTRIM(COALESCE(v_mst.cabang, ''))::CHAR(20)
    END;

    v_idlocation := BTRIM(COALESCE(v_dtl.idlocation, ''));

    v_currcode := 'IDR'::CHAR(3);
    v_kurs     := 1;
    v_idtax    := 'NON';
    v_isincl   := 'NO'::CHAR(3);

    v_qty   := COALESCE(v_dtl.qty, 0);
    v_harga := COALESCE(NULLIF(v_dtl.val, 0), 0);
    v_nilai := COALESCE(NULLIF(v_dtl.valsum, 0), v_harga * v_qty, 0);

    -- ============================================================
    -- FALLBACK: kalau nilai masih 0, ambil dari stkblc_avgcost
    -- Ini WAJIB karena TAHAP 16 membaca value_source = 'NILAI'
    -- ============================================================
    IF COALESCE(v_nilai, 0) <= 0
       AND NULLIF(BTRIM(v_dtl.idbarang::TEXT), '') IS NOT NULL
       AND NULLIF(v_idlocation, '') IS NOT NULL
    THEN
        SELECT ROUND(COALESCE(ac.avg_cost, 0) * v_qty, 2)
        INTO v_nilai
        FROM sc_trx.stkblc_avgcost ac
        WHERE BTRIM(ac.idbarang::TEXT) = BTRIM(v_dtl.idbarang::TEXT)
          AND BTRIM(ac.idlocation::TEXT) = v_idlocation
          AND BTRIM(COALESCE(ac.batch::TEXT, ''))
              = BTRIM(COALESCE(v_dtl.batch::TEXT, ''))
        LIMIT 1;

        v_nilai := COALESCE(v_nilai, 0);

        IF v_nilai > 0 AND v_qty > 0 THEN
            v_harga := ROUND(v_nilai / v_qty, 6);
        END IF;
    END IF;

    v_bruto := v_nilai;
    v_dpp   := v_nilai;
    v_pajak := 0;
    v_total := v_nilai + v_pajak;

    -- ========================================================
    -- idcoa = COA BEBAN (untuk trace)
    -- ========================================================
    v_idcoa := COALESCE(NULLIF(BTRIM(v_dtl.idcoa::TEXT), ''), '');

    IF v_idcoa = '' THEN
        v_idcoa := sc_trx.fn_resolve_pmk_expense_coa(
            v_dtl.idcostcenter::TEXT,
            v_dtl.idbarang::TEXT,
            COALESCE(v_dtl.pbiaya::TEXT, '')
        );
    END IF;

    IF v_idcoa IS NULL THEN
        RAISE EXCEPTION
            'COA beban tidak ter-resolve untuk PMK % line %. cost center=%, barang=%',
            p_docno, v_dtl.idurut,
            COALESCE(v_dtl.idcostcenter::TEXT, ''),
            COALESCE(v_dtl.idbarang::TEXT, '');
    END IF;

    -- ========================================================
    -- counter_idcoa = COA PERSEDIAAN (untuk trace)
    -- ========================================================
    v_counter := COALESCE(NULLIF(BTRIM(v_dtl.counter_idcoa::TEXT), ''), '');

    IF v_counter = '' THEN
        SELECT NULLIF(BTRIM(to_jsonb(b)->>'ppersediaan'), '')
        INTO v_counter
        FROM sc_mst.mbarang b
        WHERE BTRIM(to_jsonb(b)->>'idbarang') = BTRIM(v_dtl.idbarang::TEXT)
        LIMIT 1;
    END IF;

    IF v_counter IS NULL THEN
        RAISE EXCEPTION
            'COA persediaan tidak ditemukan untuk barang %',
            COALESCE(v_dtl.idbarang::TEXT, '');
    END IF;

    v_keterangan := TRIM(
        COALESCE(v_dtl.description, '') ||
        CASE
            WHEN NULLIF(BTRIM(COALESCE(v_dtl.nmcostcenter::TEXT, '')), '') IS NOT NULL
            THEN ' | CC: ' || BTRIM(v_dtl.nmcostcenter::TEXT)
            ELSE ''
        END
    );

    v_tx_uid := MD5(BTRIM(p_docno) || '|' || COALESCE(v_dtl.iduniq, '') || '|pmk');

    SELECT EXISTS (
        SELECT 1 FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(p_docno)
          AND uniqueid = v_tx_uid
    ) INTO v_exists;

    IF v_exists THEN
        UPDATE sc_trx.transaction_dt SET
            journal_type    = v_journal,
            type_in_out     = v_type,
            docdate         = COALESCE(v_mst.docdate::DATE, CURRENT_DATE),
            idbranch        = v_idbranch,
            cabang          = BTRIM(COALESCE(v_mst.cabang, '')),
            idbarang        = BTRIM(COALESCE(v_dtl.idbarang, '')),
            namabarang      = BTRIM(COALESCE(v_dtl.nmbarang, '')),
            idunit          = BTRIM(COALESCE(v_dtl.unit, '')),
            warehouse       = v_idlocation,
            idarea          = v_idlocation,
            bin             = '',
            batch           = BTRIM(COALESCE(v_dtl.batch, '')),
            lotno           = '',
            qty             = v_qty,
            harga           = v_harga,
            bruto           = v_bruto,
            discount        = 0,
            nilai           = v_nilai,
            dpp             = v_dpp,
            pajak           = v_pajak,
            total           = v_total,
            idtax           = v_idtax,
            isinclusive     = v_isincl,
            currcode        = v_currcode,
            kurs            = v_kurs,
            idcoa           = v_idcoa,
            counter_idcoa   = v_counter,
            debet_kredit    = 'D',
            keterangan      = v_keterangan,
            updatedby       = v_mst.updateby,
            updateddate     = CURRENT_TIMESTAMP
        WHERE BTRIM(docno) = BTRIM(p_docno)
          AND uniqueid = v_tx_uid;
    ELSE
        INSERT INTO sc_trx.transaction_dt (
            uniqueid, source_uniqueid,
            docno, doctype, journal_type, line_no, docdate,
            idbranch, cabang, type_in_out,
            ref_docno, ref_doctype,
            idbarang, namabarang, idunit, idarea, warehouse,
            bin, batch, lotno,
            qty, harga, bruto, discount, nilai, dpp, pajak, total,
            idtax, isinclusive, currcode, kurs,
            idcoa, counter_idcoa, debet_kredit,
            keterangan, createdby
        )
        VALUES (
            v_tx_uid,
            COALESCE(v_dtl.iduniq, ''),
            BTRIM(p_docno),
            'PMKBRG',
            v_journal,
            GREATEST(COALESCE(v_dtl.idurut, 1), 1),
            COALESCE(v_mst.docdate::DATE, CURRENT_DATE),
            v_idbranch,
            BTRIM(COALESCE(v_mst.cabang, '')),
            v_type,
            BTRIM(COALESCE(v_mst.docref, '')),
            'PMK',
            BTRIM(COALESCE(v_dtl.idbarang, '')),
            BTRIM(COALESCE(v_dtl.nmbarang, '')),
            BTRIM(COALESCE(v_dtl.unit, '')),
            v_idlocation,
            v_idlocation,
            '', BTRIM(COALESCE(v_dtl.batch, '')), '',
            v_qty, v_harga, v_bruto, 0, v_nilai, v_dpp, v_pajak, v_total,
            v_idtax, v_isincl, v_currcode, v_kurs,
            v_idcoa,
            v_counter,
            'D',
            v_keterangan,
            COALESCE(v_mst.inputby, 'SYSTEM')
        );
    END IF;
END;
$$;

-- ============================================================
-- STEP 7 — TRIGGER: sc_trx.pmk_brng_dtl -> transaction_dt
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.fn_pmk_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        PERFORM sc_trx.fn_sync_pmk_dtl_to_transaction(NEW.docno, NEW.iduniq);
        RETURN NEW;

    ELSIF TG_OP = 'UPDATE' THEN
        PERFORM sc_trx.fn_sync_pmk_dtl_to_transaction(NEW.docno, NEW.iduniq);
        RETURN NEW;

    ELSIF TG_OP = 'DELETE' THEN
        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND uniqueid = MD5(BTRIM(OLD.docno) || '|' || COALESCE(OLD.iduniq, '') || '|pmk');
        RETURN OLD;
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_pmk_brng_dtl_to_transaction
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.pmk_brng_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_pmk_dtl_to_transaction();

-- ============================================================
-- STEP 8 — TRIGGER: sc_tmp.pmk_brng_mst (E -> F, edit, cancel)
-- ============================================================

CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_pmk_brng_mst()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno       TEXT;
    v_inputby     TEXT;
    v_inputdate   TIMESTAMP;
    v_base_docno  TEXT;
    v_new_docno   TEXT;
    v_num         TEXT;
    v_num_int     INTEGER;
    v_doctype     TEXT;
    v_idurut      BIGINT;
    v_lock_key    BIGINT;
BEGIN

    v_doctype := UPPER(TRIM(COALESCE(NEW.doctype, 'PMKBRG')));

    IF OLD.status = 'E' AND NEW.status = 'F'
       AND COALESCE(BTRIM(NEW.docnotmp), '') = ''
    THEN
        v_docno     := TRIM(NEW.docno);
        v_inputby   := NEW.inputby;
        v_inputdate := NEW.inputdate;
        v_idurut    := NEW.idurut;

        IF EXISTS (
            SELECT 1 FROM sc_trx.pmk_brng_mst
            WHERE idurut = v_idurut
              AND TRIM(COALESCE(inputby, '')) = TRIM(COALESCE(v_inputby, ''))
        ) THEN
            DELETE FROM sc_tmp.pmk_brng_mst
            WHERE TRIM(docno) = TRIM(OLD.docno)
              AND idurut = v_idurut;
            RETURN NEW;
        END IF;

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.pmk_brng_mst
                WHERE TRIM(docno) = TRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO Pemakaian Barang tidak valid: %', v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        INSERT INTO sc_trx.pmk_brng_mst (
            idurut, docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to, idlocation_transit,
            status, description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter, nmcostcenter
        )
        SELECT
            idurut, v_docno, v_doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to, idlocation_transit,
            'F', description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter, nmcostcenter
        FROM sc_tmp.pmk_brng_mst
        WHERE TRIM(docno) = TRIM(OLD.docno);

        INSERT INTO sc_trx.pmk_brng_dtl (
            idurut, docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, nmcostcenter, pbiaya,
            batch, idlocation, idcoa, counter_idcoa,
            conversion_factor, unit_default, qty_base
        )
        SELECT
            idurut, v_docno, docref, v_doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, nmcostcenter, pbiaya,
            batch, idlocation, idcoa, counter_idcoa,
            conversion_factor, unit_default, qty_base
        FROM sc_tmp.pmk_brng_dtl
        WHERE TRIM(docno) = TRIM(OLD.docno);

        DELETE FROM sc_tmp.pmk_brng_mst WHERE TRIM(docno) = TRIM(OLD.docno);
        DELETE FROM sc_tmp.pmk_brng_dtl WHERE TRIM(docno) = TRIM(OLD.docno);

    ELSIF OLD.status = 'E' AND NEW.status = 'F'
          AND COALESCE(BTRIM(NEW.docnotmp), '') <> ''
    THEN
        v_inputby := NEW.inputby;

        DELETE FROM sc_trx.pmk_brng_dtl WHERE TRIM(docno) = TRIM(NEW.docnotmp);
        DELETE FROM sc_trx.pmk_brng_mst WHERE TRIM(docno) = TRIM(NEW.docnotmp);

        INSERT INTO sc_trx.pmk_brng_mst (
            idurut, docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to, idlocation_transit,
            status, description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter, nmcostcenter
        )
        SELECT
            idurut, NEW.docnotmp, v_doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to, idlocation_transit,
            'F', description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter, nmcostcenter
        FROM sc_tmp.pmk_brng_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

        INSERT INTO sc_trx.pmk_brng_dtl (
            idurut, docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, nmcostcenter, pbiaya,
            batch, idlocation, idcoa, counter_idcoa,
            conversion_factor, unit_default, qty_base
        )
        SELECT
            idurut, NEW.docnotmp, docref, v_doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, nmcostcenter, pbiaya,
            batch, idlocation, idcoa, counter_idcoa,
            conversion_factor, unit_default, qty_base
        FROM sc_tmp.pmk_brng_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);

        DELETE FROM sc_tmp.pmk_brng_mst WHERE TRIM(docno) = TRIM(NEW.docno);
        DELETE FROM sc_tmp.pmk_brng_dtl WHERE TRIM(docno) = TRIM(NEW.docno);

    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN
        DELETE FROM sc_tmp.pmk_brng_mst WHERE TRIM(docno) = TRIM(NEW.docno);
        DELETE FROM sc_tmp.pmk_brng_dtl WHERE TRIM(docno) = TRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_tmp_pmk_brng_mst
AFTER UPDATE ON sc_tmp.pmk_brng_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_pmk_brng_mst();

-- ============================================================
-- STEP 9 — TRIGGER: sc_trx.pmk_brng_mst (F -> E / REVISI)
-- ============================================================

CREATE OR REPLACE FUNCTION sc_trx.tr_trx_pmk_brng_mst()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF OLD.status = 'F' AND NEW.status = 'E' THEN

        INSERT INTO sc_tmp.pmk_brng_mst (
            idurut, docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to, idlocation_transit,
            status, description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter, nmcostcenter
        )
        SELECT
            idurut, NEW.updateby, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to, idlocation_transit,
            'E', description, inputby, inputdate, updateby, updatedate,
            printby, printdate, docno, idcostcenter, nmcostcenter
        FROM sc_trx.pmk_brng_mst
        WHERE TRIM(docno) = TRIM(NEW.docno);

        INSERT INTO sc_tmp.pmk_brng_dtl (
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idcostcenter, nmcostcenter, pbiaya,
            batch, idlocation, idcoa, counter_idcoa,
            conversion_factor, unit_default, qty_base
        )
        SELECT
            NEW.updateby, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docno, idurut, idcostcenter, nmcostcenter, pbiaya,
            batch, idlocation, idcoa, counter_idcoa,
            conversion_factor, unit_default, qty_base
        FROM sc_trx.pmk_brng_dtl
        WHERE TRIM(docno) = TRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_trx_pmk_brng_mst
AFTER UPDATE ON sc_trx.pmk_brng_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_trx_pmk_brng_mst();

-- ============================================================
-- STEP 10 — VERIFIKASI
-- ============================================================

DO $$
DECLARE
    v_ok INTEGER := 0;
    r RECORD;
BEGIN
    IF to_regprocedure('sc_trx.fn_resolve_pmk_expense_coa(text,text,text)') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.fn_sync_pmk_dtl_to_transaction(text,text)') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.fn_pmk_dtl_to_transaction()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_tmp.fn_pmk_dtl_plot_coa()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_tmp.tr_tmp_pmk_brng_mst()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.tr_trx_pmk_brng_mst()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    RAISE NOTICE '=========================================================';
    RAISE NOTICE 'MODUL PMK BRNG : REBUILD SELESAI';
    RAISE NOTICE '  Fungsi modul terpasang : % / 6', v_ok;
    RAISE NOTICE '';
    RAISE NOTICE '  journal_type = STKOUT (operational)';
    RAISE NOTICE '  idcoa        = costcenter.pbiaya         (trace)';
    RAISE NOTICE '  counter_idcoa= mbarang.ppersediaan       (trace)';
    RAISE NOTICE '  nilai        = valsum / avg_cost × qty';
    RAISE NOTICE '  Jurnal hasil = mengikuti mapping STKOUT di journal_type_coa';
    RAISE NOTICE '';
    RAISE NOTICE '  Trigger milik TAHAP 01-33 di transaction_dt';
    RAISE NOTICE '  (TIDAK disentuh):';
    FOR r IN
        SELECT tgname FROM pg_trigger
        WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
          AND NOT tgisinternal
        ORDER BY tgname
    LOOP
        RAISE NOTICE '    %', r.tgname;
    END LOOP;
    RAISE NOTICE '=========================================================';
END;
$$;

COMMIT;