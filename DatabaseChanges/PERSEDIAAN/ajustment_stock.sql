/* ============================================================================
   JSYS ERP — MODUL ADJUSTMENT STOCK (SELF-CONTAINED)
   ----------------------------------------------------------------------------
   PRINSIP:
   - TIDAK menghapus / mengubah objek milik TAHAP 01-19.
   - TIDAK DROP TABLE.
   - TIDAK DROP TRIGGER di sc_trx.transaction_dt.
   - Modul ini HANYA:
       * CREATE TABLE IF NOT EXISTS (tabel miliknya)
       * ALTER TABLE ... ADD COLUMN IF NOT EXISTS
       * CREATE OR REPLACE FUNCTION (miliknya sendiri)
       * CREATE OR REPLACE TRIGGER (di tabel miliknya sendiri)
   - Modul ini menulis ke sc_trx.transaction_dt dan membiarkan
     TAHAP 04 / 06 / 08 / 17 / 18 bekerja otomatis.
   ============================================================================ */


/* ============================================================================
   STEP 1 — TABEL sc_tmp.ajustment_stock_mst
   ============================================================================ */

CREATE TABLE IF NOT EXISTS sc_tmp.ajustment_stock_mst (
    docno              VARCHAR(30)  NOT NULL,
    doctype            VARCHAR(20)  NOT NULL DEFAULT 'AJUSTMENT_STOCK',
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
    currency           VARCHAR(20) DEFAULT 'IDR',
    kurs               NUMERIC(18,6) DEFAULT 1,
    isinclusive        CHAR(3) DEFAULT 'NO',
    idtax              VARCHAR(20) DEFAULT 'NON',
    inputby            VARCHAR(50),
    inputdate          TIMESTAMP WITHOUT TIME ZONE,
    updateby           VARCHAR(50),
    updatedate         TIMESTAMP WITHOUT TIME ZONE,
    printby            VARCHAR(50),
    printdate          TIMESTAMP WITHOUT TIME ZONE,
    docnotmp           VARCHAR(30),
    CONSTRAINT pk_tmp_ajustment_stock_mst PRIMARY KEY (docno)
);


/* ============================================================================
   STEP 2 — TABEL sc_tmp.ajustment_stock_dtl
   ============================================================================ */

CREATE TABLE IF NOT EXISTS sc_tmp.ajustment_stock_dtl (
    idurut       BIGSERIAL PRIMARY KEY,
    docno        VARCHAR(30) NOT NULL,
    docref       VARCHAR(30),
    doctype      VARCHAR(20) DEFAULT 'AJUSTMENT_STOCK',
    idbarang     VARCHAR(20),
    nmbarang     VARCHAR(150),
    unit         VARCHAR(20),
    qtystock     NUMERIC(18,2) DEFAULT 0,
    qty          NUMERIC(18,2) DEFAULT 0,
    val          NUMERIC(18,2) DEFAULT 0,
    valqty       NUMERIC(18,2) DEFAULT 0,
    valsum       NUMERIC(18,2) DEFAULT 0,
    dk           CHAR(1) DEFAULT 'D',
    currency     VARCHAR(20) DEFAULT 'IDR',
    idlocation   VARCHAR(30),
    batch        VARCHAR(50),
    description  TEXT,
    status       CHAR(6),
    idtax        VARCHAR(20) DEFAULT 'NON',
    inputby      VARCHAR(50),
    inputdate    TIMESTAMP WITHOUT TIME ZONE,
    updateby     VARCHAR(50),
    updatedate   TIMESTAMP WITHOUT TIME ZONE,
    iduniq       TEXT,
    docnotmp     VARCHAR(30)
);


/* ============================================================================
   STEP 3 — TABEL sc_trx.ajustment_stock_mst
   ============================================================================ */

CREATE TABLE IF NOT EXISTS sc_trx.ajustment_stock_mst (
    docno              VARCHAR(30)  NOT NULL,
    doctype            VARCHAR(20)  NOT NULL DEFAULT 'AJUSTMENT_STOCK',
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
    currency           VARCHAR(20) DEFAULT 'IDR',
    kurs               NUMERIC(18,6) DEFAULT 1,
    isinclusive        CHAR(3) DEFAULT 'NO',
    idtax              VARCHAR(20) DEFAULT 'NON',
    inputby            VARCHAR(50),
    inputdate          TIMESTAMP WITHOUT TIME ZONE,
    updateby           VARCHAR(50),
    updatedate         TIMESTAMP WITHOUT TIME ZONE,
    printby            VARCHAR(50),
    printdate          TIMESTAMP WITHOUT TIME ZONE,
    docnotmp           VARCHAR(30),
    CONSTRAINT pk_trx_ajustment_stock_mst PRIMARY KEY (docno)
);


/* ============================================================================
   STEP 4 — TABEL sc_trx.ajustment_stock_dtl
   ============================================================================ */

CREATE TABLE IF NOT EXISTS sc_trx.ajustment_stock_dtl (
    idurut       INTEGER,
    docno        VARCHAR(30) NOT NULL,
    docref       VARCHAR(30),
    doctype      VARCHAR(20) DEFAULT 'AJUSTMENT_STOCK',
    idbarang     VARCHAR(20),
    nmbarang     VARCHAR(150),
    unit         VARCHAR(20),
    qtystock     NUMERIC(18,2) DEFAULT 0,
    qty          NUMERIC(18,2) DEFAULT 0,
    val          NUMERIC(18,2) DEFAULT 0,
    valqty       NUMERIC(18,2) DEFAULT 0,
    valsum       NUMERIC(18,2) DEFAULT 0,
    dk           CHAR(1) DEFAULT 'D',
    currency     VARCHAR(20) DEFAULT 'IDR',
    idlocation   VARCHAR(30),
    batch        VARCHAR(50),
    description  TEXT,
    status       CHAR(6),
    idtax        VARCHAR(20) DEFAULT 'NON',
    inputby      VARCHAR(50),
    inputdate    TIMESTAMP WITHOUT TIME ZONE,
    updateby     VARCHAR(50),
    updatedate   TIMESTAMP WITHOUT TIME ZONE,
    iduniq       TEXT,
    docnotmp     VARCHAR(30)
);


/* ============================================================================
   STEP 5 — TAMBAH KOLOM COA (AMAN, TANPA DROP)
   ============================================================================ */

ALTER TABLE sc_tmp.ajustment_stock_dtl
    ADD COLUMN IF NOT EXISTS idcoa VARCHAR(20) DEFAULT '';

ALTER TABLE sc_tmp.ajustment_stock_dtl
    ADD COLUMN IF NOT EXISTS counter_idcoa VARCHAR(20) DEFAULT '';

ALTER TABLE sc_trx.ajustment_stock_dtl
    ADD COLUMN IF NOT EXISTS idcoa VARCHAR(20) DEFAULT '';

ALTER TABLE sc_trx.ajustment_stock_dtl
    ADD COLUMN IF NOT EXISTS counter_idcoa VARCHAR(20) DEFAULT '';


/* ============================================================================
   STEP 6 — FUNCTION SYNC DETAIL ADJUSTMENT → transaction_dt
   ----------------------------------------------------------------------------
   Fungsi ini HANYA menulis ke transaction_dt.
   Trigger TAHAP 04 (fn_prepare_transaction_dt) akan otomatis:
       - mengisi module, direction, stock_effect, accounting_effect,
         asset_effect, stock_uniqueid, stock_key
       - resolve COA
   Trigger TAHAP 06 akan posting ke stkblc.
   Trigger TAHAP 08 akan posting ke assetblc.
   Trigger TAHAP 18 akan posting ke jurnal_hd / jurnal_dt.
   TAHAP 17 akan recalculate transaction_hd.
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_sync_ajustment_stock_dtl_to_transaction(
    p_docno    TEXT,
    p_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_adj        sc_trx.ajustment_stock_mst%ROWTYPE;
    v_dtl        sc_trx.ajustment_stock_dtl%ROWTYPE;
    v_journal    CHAR(6);
    v_type       CHAR(3);
    v_tx_uid     TEXT;
    v_idbranch   CHAR(20);
    v_currcode   CHAR(3);
    v_kurs       NUMERIC(18,6);
    v_idtax      VARCHAR(20);
    v_isincl     CHAR(3);
    v_dk         CHAR(1);
    v_qty        NUMERIC(18,6);
    v_harga      NUMERIC(18,6);
    v_nilai      NUMERIC(18,2);
    v_dpp        NUMERIC(18,2);
    v_pajak      NUMERIC(18,2);
    v_total      NUMERIC(18,2);
    v_bruto      NUMERIC(18,2);
    v_exists     BOOLEAN;
    v_idcoa      VARCHAR(20);
    v_counter    VARCHAR(20);
BEGIN
    SELECT * INTO v_adj
    FROM sc_trx.ajustment_stock_mst
    WHERE BTRIM(docno) = BTRIM(p_docno);

    IF NOT FOUND THEN RETURN; END IF;

    SELECT * INTO v_dtl
    FROM sc_trx.ajustment_stock_dtl
    WHERE BTRIM(docno) = BTRIM(p_docno)
      AND iduniq = p_uniqueid;

    IF NOT FOUND THEN RETURN; END IF;

    v_dk := UPPER(BTRIM(COALESCE(v_dtl.dk, 'D')));

    IF v_dk = 'K' THEN
        v_journal := 'ADJOUT'::CHAR(6);
        v_type    := 'OUT'::CHAR(3);
    ELSE
        v_journal := 'ADJINX'::CHAR(6);
        v_type    := 'IN'::CHAR(3);
    END IF;

    v_idbranch := CASE BTRIM(COALESCE(v_adj.cabang, ''))
        WHEN 'JTS1' THEN 'JTS'::CHAR(20)
        WHEN 'JTS2' THEN 'JTS'::CHAR(20)
        ELSE BTRIM(COALESCE(v_adj.cabang, ''))::CHAR(20)
    END;

    v_currcode := UPPER(COALESCE(NULLIF(BTRIM(v_adj.currency),''), 'IDR'))::CHAR(3);
    v_kurs     := COALESCE(NULLIF(v_adj.kurs, 0), 1);
    v_idtax    := COALESCE(NULLIF(BTRIM(v_adj.idtax),''), 'NON');
    v_isincl   := UPPER(COALESCE(NULLIF(BTRIM(v_adj.isinclusive),''), 'NO'))::CHAR(3);

    v_qty   := COALESCE(v_dtl.qty, 0);
    v_harga := COALESCE(NULLIF(v_dtl.valqty, 0), NULLIF(v_dtl.val, 0), 0);
    v_nilai := COALESCE(NULLIF(v_dtl.valsum, 0), v_harga * v_qty, 0);
    v_bruto := v_nilai;
    v_dpp   := v_nilai;
    v_pajak := 0;
    v_total := v_nilai + v_pajak;

    v_idcoa   := COALESCE(NULLIF(BTRIM(v_dtl.idcoa), ''), '');
    v_counter := COALESCE(NULLIF(BTRIM(v_dtl.counter_idcoa), ''), '');

    v_tx_uid := MD5(BTRIM(p_docno) || '|' || COALESCE(v_dtl.iduniq, '') || '|adj');

    SELECT EXISTS (
        SELECT 1 FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(p_docno)
          AND uniqueid = v_tx_uid
    ) INTO v_exists;

    IF v_exists THEN
        UPDATE sc_trx.transaction_dt SET
            journal_type    = v_journal,
            type_in_out     = v_type,
            docdate         = COALESCE(v_adj.docdate::DATE, CURRENT_DATE),
            idbranch        = v_idbranch,
            cabang          = BTRIM(COALESCE(v_adj.cabang, '')),
            idbarang        = BTRIM(COALESCE(v_dtl.idbarang, '')),
            namabarang      = BTRIM(COALESCE(v_dtl.nmbarang, '')),
            idunit          = BTRIM(COALESCE(v_dtl.unit, '')),
            warehouse       = BTRIM(COALESCE(v_dtl.idlocation, '')),
            idarea          = BTRIM(COALESCE(v_dtl.idlocation, '')),
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
            debet_kredit    = v_dk,
            keterangan      = COALESCE(v_dtl.description, ''),
            updatedby       = v_adj.updateby,
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
            'AJUSTMENT_STOCK',
            v_journal,
            GREATEST(COALESCE(v_dtl.idurut, 1), 1),
            COALESCE(v_adj.docdate::DATE, CURRENT_DATE),
            v_idbranch,
            BTRIM(COALESCE(v_adj.cabang, '')),
            v_type,
            BTRIM(COALESCE(v_adj.docref, '')),
            'ADJ',
            BTRIM(COALESCE(v_dtl.idbarang, '')),
            BTRIM(COALESCE(v_dtl.nmbarang, '')),
            BTRIM(COALESCE(v_dtl.unit, '')),
            BTRIM(COALESCE(v_dtl.idlocation, '')),
            BTRIM(COALESCE(v_dtl.idlocation, '')),
            '', BTRIM(COALESCE(v_dtl.batch, '')), '',
            v_qty, v_harga, v_bruto, 0, v_nilai, v_dpp, v_pajak, v_total,
            v_idtax, v_isincl, v_currcode, v_kurs,
            v_idcoa,
            v_counter,
            v_dk,
            COALESCE(v_dtl.description, ''),
            COALESCE(v_adj.inputby, 'SYSTEM')
        );
    END IF;
END;
$$;


/* ============================================================================
   STEP 7 — TRIGGER sc_trx.ajustment_stock_dtl → transaction_dt
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_ajustment_stock_dtl_to_transaction()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        PERFORM sc_trx.fn_sync_ajustment_stock_dtl_to_transaction(
            NEW.docno, NEW.iduniq
        );
        RETURN NEW;

    ELSIF TG_OP = 'UPDATE' THEN
        PERFORM sc_trx.fn_sync_ajustment_stock_dtl_to_transaction(
            NEW.docno, NEW.iduniq
        );
        RETURN NEW;

    ELSIF TG_OP = 'DELETE' THEN
        DELETE FROM sc_trx.transaction_dt
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND uniqueid = MD5(
                BTRIM(OLD.docno) || '|' ||
                COALESCE(OLD.iduniq, '') || '|adj'
              );
        RETURN OLD;
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS tr_ajustment_stock_dtl_to_transaction
ON sc_trx.ajustment_stock_dtl;

CREATE TRIGGER tr_ajustment_stock_dtl_to_transaction
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.ajustment_stock_dtl
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_ajustment_stock_dtl_to_transaction();


/* ============================================================================
   STEP 8 — TRIGGER sc_tmp.ajustment_stock_mst (E → F)
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_tmp.tr_tmp_ajustment_stock_mst()
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
BEGIN
    /* FLOW 1: FINAL BARU */
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
                SELECT 1 FROM sc_trx.ajustment_stock_mst
                WHERE BTRIM(docno) = BTRIM(v_new_docno)
            );

            v_num := regexp_replace(v_new_docno, '.*?([0-9]+)$', '\1');

            IF COALESCE(v_num, '') = '' THEN
                RAISE EXCEPTION
                    'Format DOCNO ADJUSTMENT tidak valid: %', v_new_docno;
            END IF;

            v_num_int := v_num::INTEGER + 1;

            v_new_docno :=
                v_base_docno || lpad(v_num_int::TEXT, length(v_num), '0');
        END LOOP;

        v_docno := v_new_docno;

        INSERT INTO sc_trx.ajustment_stock_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, status, description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,
            currency, kurs, isinclusive, idtax
        )
        SELECT
            v_docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, 'F', description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,
            COALESCE(currency, 'IDR'), COALESCE(kurs, 1),
            COALESCE(isinclusive, 'NO'), COALESCE(idtax, 'NON')
        FROM sc_tmp.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND inputdate IS NOT DISTINCT FROM v_inputdate;

        INSERT INTO sc_trx.ajustment_stock_dtl (
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, dk, currency, valqty, val, valsum,
            idlocation, batch, qty, description, status,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idtax,
            idcoa, counter_idcoa
        )
        SELECT
            v_docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, dk, currency, valqty, val, valsum,
            idlocation, batch, qty, description, 'F',
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, COALESCE(idtax, 'NON'),
            COALESCE(idcoa, ''), COALESCE(counter_idcoa, '')
        FROM sc_tmp.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

        DELETE FROM sc_tmp.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby
          AND inputdate IS NOT DISTINCT FROM v_inputdate;

        DELETE FROM sc_tmp.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(OLD.docno)
          AND inputby = v_inputby;

    /* FLOW 2: FINAL EDIT */
    ELSIF OLD.status = 'E' AND NEW.status = 'F'
          AND COALESCE(BTRIM(NEW.docnotmp), '') <> ''
    THEN
        DELETE FROM sc_trx.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        DELETE FROM sc_trx.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);

        INSERT INTO sc_trx.ajustment_stock_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, status, description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,
            currency, kurs, isinclusive, idtax
        )
        SELECT
            NEW.docnotmp, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, 'F', description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,
            COALESCE(currency, 'IDR'), COALESCE(kurs, 1),
            COALESCE(isinclusive, 'NO'), COALESCE(idtax, 'NON')
        FROM sc_tmp.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        INSERT INTO sc_trx.ajustment_stock_dtl (
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, dk, currency, valqty, val, valsum,
            idlocation, batch, qty, description, status,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idtax,
            idcoa, counter_idcoa
        )
        SELECT
            NEW.docnotmp, docref, doctype, idbarang, nmbarang, unit,
            qtystock, dk, currency, valqty, val, valsum,
            idlocation, batch, qty, description, 'F',
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, COALESCE(idtax, 'NON'),
            COALESCE(idcoa, ''), COALESCE(counter_idcoa, '')
        FROM sc_tmp.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

    /* FLOW 3: CANCEL */
    ELSIF OLD.status = 'E' AND NEW.status = 'C' THEN
        IF NEW.printby IS NOT NULL
           AND BTRIM(NEW.printby) <> ''
           AND NEW.printdate IS NOT NULL
        THEN
            UPDATE sc_trx.ajustment_stock_mst
            SET status = 'P'
            WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);
        ELSE
            UPDATE sc_trx.ajustment_stock_mst
            SET status = 'F'
            WHERE BTRIM(docno) = BTRIM(NEW.docnotmp);
        END IF;

        DELETE FROM sc_tmp.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        DELETE FROM sc_tmp.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS tr_tmp_ajustment_stock_mst
ON sc_tmp.ajustment_stock_mst;

CREATE TRIGGER tr_tmp_ajustment_stock_mst
AFTER UPDATE ON sc_tmp.ajustment_stock_mst
FOR EACH ROW
EXECUTE FUNCTION sc_tmp.tr_tmp_ajustment_stock_mst();


/* ============================================================================
   STEP 9 — TRIGGER sc_trx.ajustment_stock_mst (F → E / REVISI)
   ============================================================================ */

CREATE OR REPLACE FUNCTION sc_trx.tr_trx_ajustment_stock_mst()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    IF OLD.status = 'F' AND NEW.status = 'E' THEN
        INSERT INTO sc_tmp.ajustment_stock_mst (
            docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, status, description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docnotmp,
            currency, kurs, isinclusive, idtax
        )
        SELECT
            docno, doctype, docdate, docref, cabang, cabang_sent,
            pemohon, estpakai, idlocation_from, idlocation_to,
            idlocation_transit, 'E', description,
            inputby, inputdate, updateby, updatedate,
            printby, printdate, docno,
            COALESCE(currency, 'IDR'), COALESCE(kurs, 1),
            COALESCE(isinclusive, 'NO'), COALESCE(idtax, 'NON')
        FROM sc_trx.ajustment_stock_mst
        WHERE BTRIM(docno) = BTRIM(NEW.docno);

        INSERT INTO sc_tmp.ajustment_stock_dtl (
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, dk, currency, valqty, val, valsum,
            idlocation, batch, qty, description, status,
            inputby, inputdate, updateby, updatedate,
            iduniq, docnotmp, idurut, idtax,
            idcoa, counter_idcoa
        )
        SELECT
            docno, docref, doctype, idbarang, nmbarang, unit,
            qtystock, dk, currency, valqty, val, valsum,
            idlocation, batch, qty, description, 'E',
            inputby, inputdate, updateby, updatedate,
            iduniq, docno, idurut, COALESCE(idtax, 'NON'),
            COALESCE(idcoa, ''), COALESCE(counter_idcoa, '')
        FROM sc_trx.ajustment_stock_dtl
        WHERE BTRIM(docno) = BTRIM(NEW.docno);
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS tr_trx_ajustment_stock_mst
ON sc_trx.ajustment_stock_mst;

CREATE TRIGGER tr_trx_ajustment_stock_mst
AFTER UPDATE ON sc_trx.ajustment_stock_mst
FOR EACH ROW
EXECUTE FUNCTION sc_trx.tr_trx_ajustment_stock_mst();


/* ============================================================================
   STEP 10 — VERIFIKASI
   ============================================================================ */

DO $$
DECLARE
    v_ok INT := 0;
    r RECORD;
BEGIN
    IF to_regprocedure('sc_trx.fn_sync_ajustment_stock_dtl_to_transaction(text,text)') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.fn_ajustment_stock_dtl_to_transaction()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_tmp.tr_tmp_ajustment_stock_mst()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    IF to_regprocedure('sc_trx.tr_trx_ajustment_stock_mst()') IS NOT NULL
    THEN v_ok := v_ok + 1; END IF;

    RAISE NOTICE '=========================================================';
    RAISE NOTICE 'MODUL ADJUSTMENT STOCK : INSTALLED (SELF-CONTAINED)';
    RAISE NOTICE '  Fungsi modul terpasang : % / 4', v_ok;
    RAISE NOTICE '';
    RAISE NOTICE '  Trigger milik TAHAP 01-19 di transaction_dt';
    RAISE NOTICE '  (TIDAK disentuh, biarkan tetap aktif):';
    FOR r IN
        SELECT tgname FROM pg_trigger
        WHERE tgrelid = 'sc_trx.transaction_dt'::regclass
          AND NOT tgisinternal
        ORDER BY tgname
    LOOP
        RAISE NOTICE '    %', r.tgname;
    END LOOP;
    RAISE NOTICE '=========================================================';

    IF v_ok <> 4 THEN
        RAISE WARNING 'Ada function modul adjustment yang tidak terpasang!';
    END IF;
END;
$$;