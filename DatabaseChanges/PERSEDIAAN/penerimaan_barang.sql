/* ============================================================================
   PENERIMAAN BARANG — pnm_brng
   FULL 1x EXECUTE (FIXED)
   ----------------------------------------------------------------------------
   - TIDAK menyentuh TAHAP 0-33
   - Hanya drop & recreate tabel + trigger + function PNM BRNG
   - journal_type = STKIN
   ============================================================================ */

BEGIN;

/* ============================================================================
   STEP 1 — DROP OBJECT PNM BRNG SAJA
   ============================================================================ */

DROP TRIGGER IF EXISTS tr_tmp_pnm_brng_mst       ON sc_tmp.pnm_brng_mst;
DROP TRIGGER IF EXISTS tr_trx_pnm_brng_mst       ON sc_trx.pnm_brng_mst;
DROP TRIGGER IF EXISTS tr_pnm_brng_dtl_to_transaction ON sc_trx.pnm_brng_dtl;

DROP FUNCTION IF EXISTS sc_tmp.tr_tmp_pnm_brng_mst() CASCADE;
DROP FUNCTION IF EXISTS sc_trx.tr_trx_pnm_brng_mst() CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_pnm_brng_dtl_to_transaction() CASCADE;
DROP FUNCTION IF EXISTS sc_trx.fn_sync_pnm_brng_dtl_to_transaction(TEXT, TEXT) CASCADE;

DROP TABLE IF EXISTS sc_tmp.pnm_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_trx.pnm_brng_dtl CASCADE;
DROP TABLE IF EXISTS sc_tmp.pnm_brng_mst CASCADE;
DROP TABLE IF EXISTS sc_trx.pnm_brng_mst CASCADE;


/* ============================================================================
   STEP 2 — CREATE TABEL TMP
   ============================================================================ */

CREATE TABLE sc_tmp.pnm_brng_mst (
    idurut             BIGSERIAL PRIMARY KEY,
    docno              VARCHAR(30) NOT NULL,
    doctype            VARCHAR(20) DEFAULT 'PNMBRG',
    docdate            VARCHAR(20),
    docref             VARCHAR(30),
    cabang             VARCHAR(30),
    cabang_sent        VARCHAR(30),
    pemohon            VARCHAR(100),
    estpakai           VARCHAR(20),
    idlocation_from    VARCHAR(30),
    idlocation_to      VARCHAR(30),
    idlocation_transit VARCHAR(30),
    status             CHAR(6),
    description        TEXT,
    inputby            VARCHAR(50),
    inputdate          TIMESTAMP WITHOUT TIME ZONE,
    updateby           VARCHAR(50),
    updatedate         TIMESTAMP WITHOUT TIME ZONE,
    printby            VARCHAR(50),
    printdate          TIMESTAMP WITHOUT TIME ZONE,
    docnotmp           VARCHAR(30),
    idcostcenter       VARCHAR(10),
    CONSTRAINT pk_tmp_pnm_brng_mst PRIMARY KEY (docno)
);

CREATE TABLE sc_tmp.pnm_brng_dtl (
    idurut       BIGSERIAL PRIMARY KEY,
    docno        VARCHAR(30) NOT NULL,
    docref       VARCHAR(30),
    doctype      VARCHAR(20) DEFAULT 'PNMBRG',
    idbarang     VARCHAR(20),
    nmbarang     VARCHAR(150),
    unit         VARCHAR(20),
    qtystock     NUMERIC(18,2) DEFAULT 0,
    qty          NUMERIC(18,2) DEFAULT 0,
    description  TEXT,
    status       CHAR(6),
    val          NUMERIC(18,2) DEFAULT 0,
    valsum       NUMERIC(18,2) DEFAULT 0,
    inputby      VARCHAR(50),
    inputdate    TIMESTAMP WITHOUT TIME ZONE,
    updateby     VARCHAR(50),
    updatedate   TIMESTAMP WITHOUT TIME ZONE,
    iduniq       TEXT,
    docnotmp     VARCHAR(30),
    idcostcenter VARCHAR(10),
    batch        VARCHAR(100),
    idlocation   VARCHAR(10),
    idcoa        VARCHAR(20)
);


/* ============================================================================
   STEP 3 — CREATE TABEL TRX
   ============================================================================ */

CREATE TABLE sc_trx.pnm_brng_mst (
    idurut             BIGINT,
    docno              VARCHAR(30) NOT NULL,
    doctype            VARCHAR(20) DEFAULT 'PNMBRG',
    docdate            VARCHAR(20),
    docref             VARCHAR(30),
    cabang             VARCHAR(30),
    cabang_sent        VARCHAR(30),
    pemohon            VARCHAR(100),
    estpakai           VARCHAR(20),
    idlocation_from    VARCHAR(30),
    idlocation_to      VARCHAR(30),
    idlocation_transit VARCHAR(30),
    status             CHAR(6),
    description        TEXT,
    inputby            VARCHAR(50),
    inputdate          TIMESTAMP WITHOUT TIME ZONE,
    updateby           VARCHAR(50),
    updatedate         TIMESTAMP WITHOUT TIME ZONE,
    printby            VARCHAR(50),
    printdate          TIMESTAMP WITHOUT TIME ZONE,
    docnotmp           VARCHAR(30),
    idcostcenter       VARCHAR(10),
    CONSTRAINT pk_trx_pnm_brng_mst PRIMARY KEY (docno)
);

CREATE TABLE sc_trx.pnm_brng_dtl (
    idurut       INTEGER,
    docno        VARCHAR(30) NOT NULL,
    docref       VARCHAR(30),
    doctype      VARCHAR(20) DEFAULT 'PNMBRG',
    idbarang     VARCHAR(20),
    nmbarang     VARCHAR(150),
    unit         VARCHAR(20),
    qtystock     NUMERIC(18,2) DEFAULT 0,
    qty          NUMERIC(18,2) DEFAULT 0,
    description  TEXT,
    status       CHAR(6),
    val          NUMERIC(18,2) DEFAULT 0,
    valsum       NUMERIC(18,2) DEFAULT 0,
    inputby      VARCHAR(50),
    inputdate    TIMESTAMP WITHOUT TIME ZONE,
    updateby     VARCHAR(50),
    updatedate   TIMESTAMP WITHOUT TIME ZONE,
    iduniq       TEXT,
    docnotmp     VARCHAR(30),
    idcostcenter VARCHAR(10),
    batch        VARCHAR(100),
    idlocation   VARCHAR(10),
    idcoa        VARCHAR(20)
);


/* ============================================================================
   STEP 4 — TRIGGER FINAL: sc_tmp.pnm_brng_mst (E → F)
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_pnm_brng_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $$
DECLARE
    v_docno      TEXT;
    v_inputby    TEXT;
    v_inputdate  TIMESTAMP;
    v_num        TEXT;
    v_num_int    INTEGER;
    v_lock_key   BIGINT;
    v_base_docno TEXT;
    v_new_docno  TEXT;
    v_doctype    TEXT;
BEGIN

    v_doctype := UPPER(TRIM(COALESCE(NEW.doctype, 'PNMBRG')));

    /* =====================================================================
       FLOW 1: FINAL BARU (E → F, docnotmp kosong)
       ===================================================================== */
    IF OLD.status = 'E' AND NEW.status = 'F'
       AND COALESCE(BTRIM(NEW.docnotmp), '') = ''
    THEN

        v_docno     := BTRIM(NEW.docno);
        v_inputby   := NEW.inputby;
        v_inputdate := NEW.inputdate;

        v_base_docno := regexp_replace(v_docno, '[0-9]+$', '');
        v_lock_key   := hashtext(v_base_docno);

        PERFORM pg_advisory_xact_lock(v_lock_key);

        v_new_docno := v_docno;

        LOOP
            EXIT WHEN NOT EXISTS (
                SELECT 1 FROM sc_trx.pnm_brng_mst
                WHERE BTRIM(docno) = BTRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO PNM BRG tidak valid: %', v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        /* Insert header */
        INSERT INTO sc_trx.pnm_brng_mst (
            idurut, docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, status, description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        )
        SELECT
            idurut, v_docno, v_doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, 'F', description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        FROM sc_tmp.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND inputdate IS NOT DISTINCT FROM v_inputdate;

        /* Insert detail — trigger dtl yang sync ke transaction_dt */
        INSERT INTO sc_trx.pnm_brng_dtl (
            idurut, docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
        )
        SELECT
            idurut, v_docno, docref, v_doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
        FROM sc_tmp.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        /* Cleanup tmp */
        DELETE FROM sc_tmp.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND inputdate IS NOT DISTINCT FROM v_inputdate;

        DELETE FROM sc_tmp.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

    /* =====================================================================
       FLOW 2: REVISI (E → F, docnotmp ada)
       ===================================================================== */
    ELSIF OLD.status = 'E' AND NEW.status = 'F'
          AND COALESCE(BTRIM(NEW.docnotmp), '') <> ''
    THEN

        v_inputby := NEW.inputby;

        /* Hapus dulu — trigger DELETE akan hapus transaction_dt */
        DELETE FROM sc_trx.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_trx.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        /* Insert header */
        INSERT INTO sc_trx.pnm_brng_mst (
            idurut, docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, status, description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        )
        SELECT
            idurut, NEW.docnotmp, v_doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, 'F', description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        FROM sc_tmp.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        /* Insert detail */
        INSERT INTO sc_trx.pnm_brng_dtl (
            idurut, docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
        )
        SELECT
            idurut, NEW.docnotmp, docref, v_doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'F', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idcostcenter, batch, idlocation, idcoa
        FROM sc_tmp.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        /* Cleanup tmp */
        DELETE FROM sc_tmp.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

    /* =====================================================================
       FLOW 3: CANCEL (E → C)
       ===================================================================== */
    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN

        IF NEW.printby IS NOT NULL
           AND BTRIM(NEW.printby) <> ''
           AND NEW.printdate IS NOT NULL
        THEN
            UPDATE sc_trx.pnm_brng_mst
            SET status = 'P'
            WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);
        ELSE
            UPDATE sc_trx.pnm_brng_mst
            SET status = 'F'
            WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);
        END IF;

        DELETE FROM sc_tmp.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_tmp_pnm_brng_mst
AFTER UPDATE ON sc_tmp.pnm_brng_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_pnm_brng_mst();


/* ============================================================================
   STEP 5 — TRIGGER: sc_trx.pnm_brng_mst (F → E / REVISI)
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.tr_trx_pnm_brng_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN

    IF OLD.status = 'F' AND NEW.status = 'E' THEN

        /* Copy header ke tmp */
        INSERT INTO sc_tmp.pnm_brng_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, status, description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp, idcostcenter
        )
        SELECT
            docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, 'E', description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docno, idcostcenter
        FROM sc_trx.pnm_brng_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        /* Copy detail ke tmp */
        INSERT INTO sc_tmp.pnm_brng_dtl (
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, status, val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idcostcenter, batch, idlocation, idcoa
        )
        SELECT
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, qty, description, 'E', val, valsum,
            inputby, inputdate, updateby, updatedate,
            iduniq, docno, idurut, idcostcenter, batch, idlocation, idcoa
        FROM sc_trx.pnm_brng_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_trx_pnm_brng_mst
AFTER UPDATE ON sc_trx.pnm_brng_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_trx_pnm_brng_mst();


/* ============================================================================
   STEP 6 — SYNC DETAIL PNM → TRANSACTION_DT
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_pnm_brng_dtl_to_transaction(
    p_docno    TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_hdr       sc_trx.pnm_brng_mst%ROWTYPE;
    v_dtl       sc_trx.pnm_brng_dtl%ROWTYPE;
    v_idbranch  CHAR(20);
    v_tx_uid    TEXT;
    v_qty       NUMERIC(18,2);
    v_val       NUMERIC(18,2);
    v_total     NUMERIC(18,2);
    v_coa_persediaan TEXT;
    v_coa_costcenter TEXT;
    v_exists    BOOLEAN;
BEGIN

    SELECT * INTO v_hdr
    FROM sc_trx.pnm_brng_mst
    WHERE BTRIM(docno) = BTRIM(p_docno);

    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_dtl
    FROM sc_trx.pnm_brng_dtl
    WHERE BTRIM(docno) = BTRIM(p_docno)
      AND iduniq = p_uniqueid;

    IF NOT FOUND THEN RETURN; END IF;

    /* Branch */
    v_idbranch := CASE BTRIM(COALESCE(v_hdr.cabang, ''))
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE BTRIM(COALESCE(v_hdr.cabang, ''))::CHAR(20)
    END;

    /* Nilai */
    v_qty   := COALESCE(v_dtl.qty, 0);
    v_val   := COALESCE(v_dtl.val, 0);
    v_total := COALESCE(NULLIF(v_dtl.valsum, 0), v_val * v_qty, 0);

    /* COA persediaan dari master barang */
    SELECT NULLIF(BTRIM(COALESCE(m.ppersediaan, '')), '')
    INTO v_coa_persediaan
    FROM sc_mst.mbarang m
    WHERE BTRIM(m.idbarang) = BTRIM(v_dtl.idbarang)
    LIMIT 1;

    /* COA biaya dari cost center */
    SELECT NULLIF(BTRIM(COALESCE(cc.pbiaya, '')), '')
    INTO v_coa_costcenter
    FROM sc_mst.costcenter cc
    WHERE BTRIM(cc.idcostcenter) = BTRIM(COALESCE(v_dtl.idcostcenter, v_hdr.idcostcenter))
    LIMIT 1;

    IF COALESCE(v_coa_persediaan, '') = '' THEN
        RAISE EXCEPTION
            'COA persediaan belum diisi pada master barang: %',
            BTRIM(v_dtl.idbarang);
    END IF;

    IF COALESCE(v_coa_costcenter, '') = '' THEN
        RAISE EXCEPTION
            'COA biaya belum diisi pada master cost center: %',
            BTRIM(COALESCE(v_dtl.idcostcenter, v_hdr.idcostcenter));
    END IF;

    v_tx_uid := MD5(BTRIM(p_docno) || '|' || COALESCE(v_dtl.iduniq, '') || '|pnm');

    /* Cek existing */
    SELECT EXISTS (
        SELECT 1 FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(p_docno)
          AND uniqueid = v_tx_uid
    ) INTO v_exists;

    IF v_exists THEN
        UPDATE sc_trx.transaction_dt SET
            journal_type    = 'STKIN',
            type_in_out     = 'IN',
            docdate         = COALESCE(v_hdr.docdate::DATE, CURRENT_DATE),
            idbranch        = v_idbranch,
            cabang          = BTRIM(COALESCE(v_hdr.cabang, '')),
            idbarang        = BTRIM(COALESCE(v_dtl.idbarang, '')),
            namabarang      = BTRIM(COALESCE(v_dtl.nmbarang, '')),
            idunit          = BTRIM(COALESCE(v_dtl.unit, '')),
            warehouse       = BTRIM(COALESCE(v_dtl.idlocation, '')),
            idarea          = BTRIM(COALESCE(v_dtl.idlocation, '')),
            bin             = '',
            batch           = BTRIM(COALESCE(v_dtl.batch, '')),
            lotno           = '',
            qty             = v_qty,
            harga           = v_val,
            bruto           = v_total,
            discount        = 0,
            nilai           = v_total,
            dpp             = v_total,
            pajak           = 0,
            total           = v_total,
            idtax           = 'NON',
            currcode        = 'IDR',
            kurs            = 1,
            idcoa           = v_coa_persediaan,
            counter_idcoa   = v_coa_costcenter,
            debet_kredit    = 'D',
            keterangan      = COALESCE(v_dtl.description, ''),
            updatedby       = v_hdr.updateby,
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
            'PNMBRG',
            'STKIN',
            GREATEST(COALESCE(v_dtl.idurut, 1), 1),
            COALESCE(v_hdr.docdate::DATE, CURRENT_DATE),
            v_idbranch,
            BTRIM(COALESCE(v_hdr.cabang, '')),
            'IN',
            BTRIM(COALESCE(v_dtl.docref, '')),
            'PNM',
            BTRIM(COALESCE(v_dtl.idbarang, '')),
            BTRIM(COALESCE(v_dtl.nmbarang, '')),
            BTRIM(COALESCE(v_dtl.unit, '')),
            BTRIM(COALESCE(v_dtl.idlocation, '')),
            BTRIM(COALESCE(v_dtl.idlocation, '')),
            '',
            BTRIM(COALESCE(v_dtl.batch, '')),
            '',
            v_qty, v_val, v_total, 0, v_total, v_total, 0, v_total,
            'NON', 'NO', 'IDR', 1,
            v_coa_persediaan, v_coa_costcenter, 'D',
            COALESCE(v_dtl.description, ''),
            COALESCE(v_hdr.inputby, 'SYSTEM')
        );
    END IF;

END;
$$;


/* ============================================================================
   STEP 7 — TRIGGER: sc_trx.pnm_brng_dtl → transaction_dt
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_pnm_brng_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        PERFORM sc_trx.fn_sync_pnm_brng_dtl_to_transaction(NEW.docno, NEW.iduniq);
        RETURN NEW;

    ELSIF TG_OP = 'UPDATE' THEN
        PERFORM sc_trx.fn_sync_pnm_brng_dtl_to_transaction(NEW.docno, NEW.iduniq);
        RETURN NEW;

    ELSIF TG_OP = 'DELETE' THEN
        DELETE FROM sc_trx.transaction_dt
        WHERE uniqueid = MD5(BTRIM(OLD.docno) || '|' || COALESCE(OLD.iduniq, '') || '|pnm')
          AND BTRIM(doctype) = 'PNMBRG';
        RETURN OLD;
    END IF;

    RETURN NEW;
END;
$$;

CREATE TRIGGER tr_pnm_brng_dtl_to_transaction
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.pnm_brng_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_pnm_brng_dtl_to_transaction();


/* ============================================================================
   STEP 8 — VERIFIKASI
   ============================================================================ */

DO $$
DECLARE
    v_ok INT := 0;
BEGIN
    IF to_regprocedure('sc_tmp.tr_tmp_pnm_brng_mst()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.tr_trx_pnm_brng_mst()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.fn_sync_pnm_brng_dtl_to_transaction(text,text)') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.fn_pnm_brng_dtl_to_transaction()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    RAISE NOTICE '=========================================================';
    RAISE NOTICE 'PNM BRNG : INSTALLED';
    RAISE NOTICE '  Function terpasang : % / 4', v_ok;
    RAISE NOTICE '  Trigger:';
    RAISE NOTICE '    tr_tmp_pnm_brng_mst';
    RAISE NOTICE '    tr_trx_pnm_brng_mst';
    RAISE NOTICE '    tr_pnm_brng_dtl_to_transaction';
    RAISE NOTICE '=========================================================';
END;
$$;

COMMIT;


/* ============================================================================
   CATATAN
   ----------------------------------------------------------------------------
   1. Script ini HANYA menyentuh:
      - sc_tmp.pnm_brng_mst
      - sc_tmp.pnm_brng_dtl
      - sc_trx.pnm_brng_mst
      - sc_trx.pnm_brng_dtl
      - function & trigger PNM BRNG
   2. TIDAK menyentuh TAHAP 0-33.
   3. journal_type = STKIN (harus ada di sc_mst.journal_type).
   4. Insert ke transaction_dt akan trigger TAHAP 04, 06, 07 (kalau ada).
   5. Pastikan kolom `pbiaya` ada di `sc_mst.costcenter`.
   6. Pastikan `ppersediaan` ada di `sc_mst.mbarang`.
   ============================================================================ */