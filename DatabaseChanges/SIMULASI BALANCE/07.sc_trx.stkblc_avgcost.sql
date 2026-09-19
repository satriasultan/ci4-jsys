/* ============================================================
   JSYS ERP
   TAHAP 07 - STOCK AVERAGE COST
   TABLE : sc_trx.stkblc_avgcost
   ============================================================

   FUNGSI TAHAP 07:
   - stkblc       = histori pergerakan stock
   - stkblc_avgcost = current balance + average cost
   - IN  menambah qty dan value
   - OUT memakai average cost sebelum transaksi OUT
   - Cost OUT ditulis kembali ke stkblc.unitcost / totalcost
   - INSERT / UPDATE / DELETE pada stkblc otomatis recalculate

   VERSI INI AMAN DI-RERUN TANPA MENGHAPUS DATA.
   YANG DISEDERHANAKAN:
   - helper fn_get_stock_uniqueid_from_transaction
   - dependency ulang ke function TAHAP 01
   - query test / dokumentasi runtime yang tidak diperlukan

   CATATAN:
   - Tidak membuat jurnal GL.
   - Tidak membuat assetblc.
   - Tidak mengubah alur TAHAP 06 transaction_dt -> stkblc.
   ============================================================ */


/* ============================================================
   1. DEPENDENCY
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.stkblc') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 06 belum tersedia: sc_trx.stkblc tidak ditemukan';
    END IF;
END;
$$;


/* ============================================================
   2. CREATE TABLE BILA BELUM ADA
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.stkblc_avgcost') IS NULL THEN

        CREATE TABLE sc_trx.stkblc_avgcost
        (
            id BIGSERIAL NOT NULL,

            stock_uniqueid TEXT NOT NULL,
            stock_key CHAR(32) NOT NULL,

            idbranch CHAR(20) NOT NULL DEFAULT '',
            cabang CHAR(30) NOT NULL DEFAULT '',
            idarea CHAR(20) NOT NULL DEFAULT '',
            warehouse VARCHAR(50) NOT NULL DEFAULT '',
            bin VARCHAR(50) NOT NULL DEFAULT '',

            idbarang CHAR(20) NOT NULL,
            idunit VARCHAR(10) NOT NULL DEFAULT '',
            batch CHAR(100) NOT NULL DEFAULT '',
            lotno VARCHAR(100) NOT NULL DEFAULT '',

            last_uniqueid TEXT NOT NULL DEFAULT '',
            last_source_uniqueid TEXT NOT NULL DEFAULT '',
            last_docno VARCHAR(50) NOT NULL DEFAULT '',
            last_doctype VARCHAR(20) NOT NULL DEFAULT '',
            last_journal_type CHAR(6) NOT NULL DEFAULT '',
            last_type_in_out CHAR(3) NOT NULL DEFAULT '',
            last_docdate DATE,

            qty_in NUMERIC(18,6) NOT NULL DEFAULT 0,
            qty_out NUMERIC(18,6) NOT NULL DEFAULT 0,
            qty_balance NUMERIC(18,6) NOT NULL DEFAULT 0,

            value_in NUMERIC(18,2) NOT NULL DEFAULT 0,
            value_out NUMERIC(18,2) NOT NULL DEFAULT 0,
            value_balance NUMERIC(18,2) NOT NULL DEFAULT 0,

            avgcost NUMERIC(18,6) NOT NULL DEFAULT 0,

            currcode CHAR(3) NOT NULL DEFAULT 'IDR',
            kurs NUMERIC(18,6) NOT NULL DEFAULT 1,

            updatedby VARCHAR(50),
            updateddate TIMESTAMP WITHOUT TIME ZONE
                NOT NULL DEFAULT CURRENT_TIMESTAMP,

            CONSTRAINT pk_stkblc_avgcost
                PRIMARY KEY (id),

            CONSTRAINT uq_stkblc_avgcost_stock
                UNIQUE (stock_uniqueid),

            CONSTRAINT chk_stkblc_avgcost_stock_key
                CHECK (length(stock_key) = 32),

            CONSTRAINT chk_stkblc_avgcost_qty
                CHECK
                (
                    qty_in >= 0
                    AND qty_out >= 0
                    AND qty_balance >= 0
                ),

            CONSTRAINT chk_stkblc_avgcost_value
                CHECK
                (
                    value_in >= 0
                    AND value_out >= 0
                    AND value_balance >= 0
                ),

            CONSTRAINT chk_stkblc_avgcost_avg
                CHECK (avgcost >= 0),

            CONSTRAINT chk_stkblc_avgcost_kurs
                CHECK (kurs > 0),

            CONSTRAINT chk_stkblc_avgcost_last_type
                CHECK
                (
                    last_type_in_out = ''
                    OR last_type_in_out IN ('IN','OUT')
                )
        );

    END IF;
END;
$$;


/* ============================================================
   3. PASTIKAN UNIQUE STOCK IDENTITY
   ============================================================ */

DO $$
DECLARE
    v_has_unique BOOLEAN;
BEGIN
    SELECT EXISTS
    (
        SELECT 1
        FROM pg_index i
        JOIN pg_attribute a
          ON a.attrelid = i.indrelid
         AND a.attnum = ANY(i.indkey)
        WHERE i.indrelid = 'sc_trx.stkblc_avgcost'::regclass
          AND i.indisunique
          AND i.indnkeyatts = 1
          AND a.attname = 'stock_uniqueid'
    )
    INTO v_has_unique;

    IF NOT v_has_unique THEN
        ALTER TABLE sc_trx.stkblc_avgcost
            ADD CONSTRAINT uq_stkblc_avgcost_stock
            UNIQUE (stock_uniqueid);
    END IF;
END;
$$;


/* ============================================================
   4. INDEX MINIMAL
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.idx_stkblc_avgcost_item') IS NULL THEN
        CREATE INDEX idx_stkblc_avgcost_item
        ON sc_trx.stkblc_avgcost(idbarang);
    END IF;

    IF to_regclass('sc_trx.idx_stkblc_avgcost_dimension') IS NULL THEN
        CREATE INDEX idx_stkblc_avgcost_dimension
        ON sc_trx.stkblc_avgcost
        (
            idbranch,
            warehouse,
            bin,
            idbarang,
            idunit,
            batch,
            lotno
        );
    END IF;
END;
$$;


/* ============================================================
   5. RECALCULATE SATU STOCK IDENTITY
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_avgcost
(
    p_stock_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r RECORD;

    v_stock_key CHAR(32);

    v_qty NUMERIC(18,6) := 0;
    v_value NUMERIC(18,2) := 0;
    v_avgcost NUMERIC(18,6) := 0;

    v_qty_in NUMERIC(18,6) := 0;
    v_qty_out NUMERIC(18,6) := 0;

    v_value_in NUMERIC(18,2) := 0;
    v_value_out NUMERIC(18,2) := 0;

    v_cost_out NUMERIC(18,2) := 0;
    v_out_unitcost NUMERIC(18,6) := 0;

    v_last_uniqueid TEXT := '';
    v_last_source_uniqueid TEXT := '';
    v_last_docno VARCHAR(50) := '';
    v_last_doctype VARCHAR(20) := '';
    v_last_journal_type CHAR(6) := '';
    v_last_type_in_out CHAR(3) := '';
    v_last_docdate DATE;

    v_idbranch CHAR(20) := '';
    v_cabang CHAR(30) := '';
    v_idarea CHAR(20) := '';
    v_warehouse VARCHAR(50) := '';
    v_bin VARCHAR(50) := '';
    v_idbarang CHAR(20);
    v_idunit VARCHAR(10) := '';
    v_batch CHAR(100) := '';
    v_lotno VARCHAR(100) := '';
    v_currcode CHAR(3) := 'IDR';
    v_kurs NUMERIC(18,6) := 1;

BEGIN
    /*
       Urutan costing harus konsisten.
       docdate = urutan bisnis.
       id      = tie breaker untuk tanggal yang sama.
    */
    FOR r IN
        SELECT
            sb.id,
            sb.uniqueid,
            sb.source_uniqueid,
            sb.docno,
            sb.doctype,
            sb.journal_type,
            sb.docdate,
            sb.type_in_out,
            sb.idbranch,
            sb.cabang,
            sb.idarea,
            sb.warehouse,
            sb.bin,
            sb.idbarang,
            sb.idunit,
            sb.batch,
            sb.lotno,
            sb.stock_key,
            sb.qty_in,
            sb.qty_out,
            sb.unitcost,
            sb.totalcost,
            sb.currcode,
            sb.kurs
        FROM sc_trx.stkblc sb
        WHERE sb.stock_uniqueid = p_stock_uniqueid
        ORDER BY sb.docdate, sb.id
    LOOP
        /* -----------------------------------------------
           Simpan identity / metadata transaksi terakhir
           ----------------------------------------------- */
        v_stock_key := r.stock_key;

        v_idbranch := COALESCE(r.idbranch, '');
        v_cabang := COALESCE(r.cabang, '');
        v_idarea := COALESCE(r.idarea, '');
        v_warehouse := COALESCE(r.warehouse, '');
        v_bin := COALESCE(r.bin, '');
        v_idbarang := r.idbarang;
        v_idunit := COALESCE(r.idunit, '');
        v_batch := COALESCE(r.batch, '');
        v_lotno := COALESCE(r.lotno, '');
        v_currcode := COALESCE(NULLIF(BTRIM(r.currcode), ''), 'IDR');
        v_kurs := COALESCE(r.kurs, 1);

        v_last_uniqueid := r.uniqueid;
        v_last_source_uniqueid := COALESCE(r.source_uniqueid, '');
        v_last_docno := COALESCE(r.docno, '');
        v_last_doctype := COALESCE(r.doctype, '');
        v_last_journal_type := COALESCE(r.journal_type, '');
        v_last_type_in_out := COALESCE(r.type_in_out, '');
        v_last_docdate := r.docdate;

        /* -----------------------------------------------
           IN = tambah stock dan tambah value
           ----------------------------------------------- */
        IF r.type_in_out = 'IN' THEN

            v_qty_in := v_qty_in + COALESCE(r.qty_in, 0);
            v_value_in := v_value_in + COALESCE(r.totalcost, 0);

            v_qty := v_qty + COALESCE(r.qty_in, 0);
            v_value := v_value + COALESCE(r.totalcost, 0);

            IF v_qty > 0 THEN
                v_avgcost := ROUND(v_value / v_qty, 6);
            ELSE
                v_avgcost := 0;
            END IF;

        /* -----------------------------------------------
           OUT = pakai average cost sebelum transaksi OUT
           ----------------------------------------------- */
        ELSIF r.type_in_out = 'OUT' THEN

            v_out_unitcost := v_avgcost;
            v_cost_out := ROUND(COALESCE(r.qty_out, 0) * v_out_unitcost, 2);

            v_qty_out := v_qty_out + COALESCE(r.qty_out, 0);
            v_value_out := v_value_out + v_cost_out;

            v_qty := v_qty - COALESCE(r.qty_out, 0);
            v_value := v_value - v_cost_out;

            IF ABS(v_qty) < 0.000001 THEN
                v_qty := 0;
            END IF;

            IF ABS(v_value) < 0.01 THEN
                v_value := 0;
            END IF;

            IF v_qty < 0 THEN
                RAISE EXCEPTION
                    'NEGATIVE STOCK: stock_uniqueid=%, qty_balance=%, uniqueid=%',
                    p_stock_uniqueid,
                    v_qty,
                    r.uniqueid;
            END IF;

            IF v_value < 0 THEN
                RAISE EXCEPTION
                    'NEGATIVE STOCK VALUE: stock_uniqueid=%, value_balance=%, uniqueid=%',
                    p_stock_uniqueid,
                    v_value,
                    r.uniqueid;
            END IF;

            IF v_qty > 0 THEN
                v_avgcost := ROUND(v_value / v_qty, 6);
            ELSE
                v_avgcost := 0;
            END IF;

            /*
               TAHAP 06 membuat OUT dengan cost awal = 0.
               TAHAP 07 menetapkan cost OUT sesuai average cost.
            */
            UPDATE sc_trx.stkblc
            SET
                unitcost = ROUND(v_out_unitcost, 6),
                totalcost = v_cost_out
            WHERE id = r.id
              AND
              (
                  unitcost <> ROUND(v_out_unitcost, 6)
                  OR totalcost <> v_cost_out
              );

        END IF;
    END LOOP;

    /* Tidak ada transaksi untuk stock identity ini. */
    IF v_idbarang IS NULL THEN
        DELETE FROM sc_trx.stkblc_avgcost
        WHERE stock_uniqueid = p_stock_uniqueid;

        RETURN;
    END IF;

    /* -----------------------------------------------
       Simpan current balance
       ----------------------------------------------- */
    INSERT INTO sc_trx.stkblc_avgcost
    (
        stock_uniqueid,
        stock_key,
        idbranch,
        cabang,
        idarea,
        warehouse,
        bin,
        idbarang,
        idunit,
        batch,
        lotno,
        last_uniqueid,
        last_source_uniqueid,
        last_docno,
        last_doctype,
        last_journal_type,
        last_type_in_out,
        last_docdate,
        qty_in,
        qty_out,
        qty_balance,
        value_in,
        value_out,
        value_balance,
        avgcost,
        currcode,
        kurs,
        updateddate
    )
    VALUES
    (
        p_stock_uniqueid,
        v_stock_key,
        v_idbranch,
        v_cabang,
        v_idarea,
        v_warehouse,
        v_bin,
        v_idbarang,
        v_idunit,
        v_batch,
        v_lotno,
        v_last_uniqueid,
        v_last_source_uniqueid,
        v_last_docno,
        v_last_doctype,
        v_last_journal_type,
        v_last_type_in_out,
        v_last_docdate,
        v_qty_in,
        v_qty_out,
        v_qty,
        v_value_in,
        v_value_out,
        v_value,
        v_avgcost,
        v_currcode,
        v_kurs,
        CURRENT_TIMESTAMP
    )
    ON CONFLICT (stock_uniqueid)
    DO UPDATE SET
        stock_key = EXCLUDED.stock_key,
        idbranch = EXCLUDED.idbranch,
        cabang = EXCLUDED.cabang,
        idarea = EXCLUDED.idarea,
        warehouse = EXCLUDED.warehouse,
        bin = EXCLUDED.bin,
        idbarang = EXCLUDED.idbarang,
        idunit = EXCLUDED.idunit,
        batch = EXCLUDED.batch,
        lotno = EXCLUDED.lotno,
        last_uniqueid = EXCLUDED.last_uniqueid,
        last_source_uniqueid = EXCLUDED.last_source_uniqueid,
        last_docno = EXCLUDED.last_docno,
        last_doctype = EXCLUDED.last_doctype,
        last_journal_type = EXCLUDED.last_journal_type,
        last_type_in_out = EXCLUDED.last_type_in_out,
        last_docdate = EXCLUDED.last_docdate,
        qty_in = EXCLUDED.qty_in,
        qty_out = EXCLUDED.qty_out,
        qty_balance = EXCLUDED.qty_balance,
        value_in = EXCLUDED.value_in,
        value_out = EXCLUDED.value_out,
        value_balance = EXCLUDED.value_balance,
        avgcost = EXCLUDED.avgcost,
        currcode = EXCLUDED.currcode,
        kurs = EXCLUDED.kurs,
        updateddate = CURRENT_TIMESTAMP;
END;
$$;


/* ============================================================
   6. REBUILD SEMUA STOCK IDENTITY
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_all_avgcost()
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT DISTINCT stock_uniqueid
        FROM sc_trx.stkblc
        WHERE NULLIF(BTRIM(stock_uniqueid), '') IS NOT NULL
        ORDER BY stock_uniqueid
    LOOP
        PERFORM sc_trx.fn_recalculate_avgcost(r.stock_uniqueid);
    END LOOP;
END;
$$;


/* ============================================================
   7. TRIGGER STKBLc -> AVGCOST
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_stkblc_avgcost_trigger()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    /*
       UPDATE unitcost / totalcost di dalam
       fn_recalculate_avgcost juga memicu trigger stkblc.
       Depth > 1 = perubahan internal costing, jangan recalculate lagi.
    */
    IF pg_trigger_depth() > 1 THEN
        IF TG_OP = 'DELETE' THEN
            RETURN OLD;
        END IF;
        RETURN NEW;
    END IF;

    IF TG_OP = 'INSERT' THEN
        PERFORM sc_trx.fn_recalculate_avgcost(NEW.stock_uniqueid);
        RETURN NEW;

    ELSIF TG_OP = 'DELETE' THEN
        PERFORM sc_trx.fn_recalculate_avgcost(OLD.stock_uniqueid);
        RETURN OLD;

    ELSIF TG_OP = 'UPDATE' THEN
        IF OLD.stock_uniqueid IS DISTINCT FROM NEW.stock_uniqueid THEN
            PERFORM sc_trx.fn_recalculate_avgcost(OLD.stock_uniqueid);
        END IF;

        PERFORM sc_trx.fn_recalculate_avgcost(NEW.stock_uniqueid);
        RETURN NEW;
    END IF;

    RETURN NEW;
END;
$$;


/* ============================================================
   8. TRIGGER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_stkblc_avgcost
ON sc_trx.stkblc;

CREATE TRIGGER trg_stkblc_avgcost
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.stkblc
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_stkblc_avgcost_trigger();


/* ============================================================
   9. ONE-TIME REBUILD EXISTING DATA
   ============================================================

   Jalankan manual setelah pemasangan bila avgcost harus
   langsung diisi dari seluruh histori stkblc:

       SELECT sc_trx.fn_recalculate_all_avgcost();

   Tidak dijalankan otomatis agar deployment TAHAP 07
   tidak melakukan rebuild jutaan transaksi tanpa sengaja.
   ============================================================ */
