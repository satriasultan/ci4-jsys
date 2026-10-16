/* ============================================================
   JSYS ERP
   TAHAP 07 - STOCK AVERAGE COST
   TABLE : sc_trx.stkblc_avgcost

   FINAL - TRANSFER SAFE
   ------------------------------------------------------------
   
   PRINSIP:
   1. TAHAP 06 sc_trx.stkblc TIDAK DIUBAH.
   2. Tidak menambahkan stock_uniqueid ke stkblc_avgcost.
   3. Tidak menambahkan idbranch / warehouse / bin / lotno
      ke stkblc_avgcost.
   4. Tidak DROP tabel existing.
   5. Compatible dengan trigger TAHAP 06 yang menggunakan
      stkblc.stock_uniqueid.
   6. Legacy avgcost mengikuti key:
          idbarang + idlocation + batch
   7. IN  : tambah qty + nilai stock.
   8. OUT : memakai average cost sebelum OUT.
   9. Cost OUT ditulis ke stkblc.unitcost / totalcost.
   10. INSERT / UPDATE / DELETE pada stkblc otomatis recalculate.
   11. Jika histori menghasilkan negative stock, proses dihentikan.
   
   TRANSFER SAFE:
   12. IN transfer (TRFWHS/BINMOV/BRNTRF) menggunakan
       totalcost dari OUT pasangan (bukan pricelst_in × qty).
   13. Nilai IN transfer = Nilai OUT transfer.
   14. Total nilai persediaan perusahaan TIDAK berubah.
   
   LOCK SAFE:
   15. Menggunakan session_replication_role = 'replica'
       (bukan ALTER TABLE DISABLE TRIGGER) untuk mencegah
       recursive trigger. Tidak butuh lock eksklusif.
   ============================================================ */


/* ============================================================
   1. DEPENDENCY
   ============================================================ */

DO $$
BEGIN
    IF to_regclass('sc_trx.stkblc') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 07 GAGAL: sc_trx.stkblc tidak ditemukan.';
    END IF;
END;
$$;


/* ============================================================
   2. CREATE TABLE JIKA BELUM ADA
      MENGIKUTI SCHEMA LEGACY YANG AKTUAL
   ============================================================ */

CREATE TABLE IF NOT EXISTS sc_trx.stkblc_avgcost
(
    idbarang      VARCHAR(20) NOT NULL,
    idlocation    VARCHAR(12) NOT NULL,
    batch         VARCHAR(100) NOT NULL DEFAULT '',

    qty           NUMERIC(18,4) NOT NULL DEFAULT 0,
    total_value   NUMERIC(18,4) NOT NULL DEFAULT 0,
    avg_cost      NUMERIC(18,4) NOT NULL DEFAULT 0,

    unit          VARCHAR(12),
    updated_at    TIMESTAMP WITHOUT TIME ZONE
                  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_sc_trx_stkblc_avgcost
        PRIMARY KEY (idbarang, idlocation, batch)
);


/* ============================================================
   3. CORE RECALCULATE — fn_recalculate_avgcost_group
   ============================================================
   
   Fungsi ini menghitung ulang SATU stock group legacy:
       idbarang + idlocation + batch
   
   TRANSFER SAFE:
   - Untuk IN transfer, gunakan totalcost yang tersimpan di
     stkblc (yang sudah dihitung TAHAP 06 dari OUT pasangan).
   - Untuk OUT transfer, tetap pakai avg cost saat itu.
   - Untuk non-transfer, gunakan pricelst_in × qty_in (perilaku lama).
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_avgcost_group
(
    p_idbarang   TEXT,
    p_idlocation TEXT,
    p_batch      TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r RECORD;
    v_qty            NUMERIC(18,6) := 0;
    v_value          NUMERIC(18,6) := 0;
    v_avg_cost       NUMERIC(18,6) := 0;
    v_cost_out       NUMERIC(18,6) := 0;
    v_out_avg        NUMERIC(18,6) := 0;
    v_unit           VARCHAR(12);
    v_found          BOOLEAN := FALSE;

    /* Guard untuk restore session_replication_role */
    v_old_replica    TEXT;
BEGIN

    IF NULLIF(BTRIM(p_idbarang), '') IS NULL THEN
        RAISE EXCEPTION 'TAHAP 07: idbarang kosong.';
    END IF;

    IF NULLIF(BTRIM(p_idlocation), '') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 07: idlocation kosong untuk idbarang=%',
            p_idbarang;
    END IF;

    /* =========================================================
       LOCK SAFE: Pakai session_replication_role = 'replica'
       untuk mematikan trigger sementara DI SESSION INI.
       Tidak butuh lock eksklusif seperti ALTER TABLE.
       ========================================================= */
    v_old_replica := current_setting('session_replication_role', true);

    IF v_old_replica IS DISTINCT FROM 'replica' THEN
        PERFORM set_config('session_replication_role', 'replica', true);
    END IF;

    /* =========================================================
       LOOP HITUNG KRONOLOGIS
       ========================================================= */
    FOR r IN
        SELECT
            sb.id,
            sb.uniqueid,
            sb.docdate,
            sb.trxdate,
            sb.journal_type,      -- ✅ untuk deteksi transfer
            sb.type_in_out,
            sb.qty_in,
            sb.qty_out,
            sb.pricelst_in,
            sb.totalcost,          -- ✅ untuk IN transfer
            sb.unit,
            sb.docno
        FROM sc_trx.stkblc sb
        WHERE BTRIM(sb.idbarang::TEXT)   = BTRIM(p_idbarang)
          AND BTRIM(sb.idlocation::TEXT) = BTRIM(p_idlocation)
          AND BTRIM(COALESCE(sb.batch::TEXT, ''))
                = BTRIM(COALESCE(p_batch, ''))
          AND COALESCE(BTRIM(sb.grouptype::TEXT), 'STOCK') <> 'NON STOCK'
        ORDER BY
            COALESCE(sb.docdate, sb.trxdate::DATE),
            sb.id
    LOOP
        v_found := TRUE;
        v_unit  := NULLIF(BTRIM(COALESCE(r.unit::TEXT, '')), '');

        /* ====================================================
           IN
           ==================================================== */
        IF BTRIM(COALESCE(r.type_in_out::TEXT, '')) = 'IN' THEN

            v_qty := v_qty + COALESCE(r.qty_in, 0);

            /* --------------------------------------------------
               TRANSFER SAFE:
               - IN transfer  → pakai totalcost dari stkblc
                                (sudah dihitung TAHAP 06 dari
                                OUT pasangan)
               - IN non-transfer → pakai pricelst_in × qty
               -------------------------------------------------- */
            IF TRIM(COALESCE(r.journal_type::TEXT, '')) IN
               ('TRFWHS','BINMOV','BRNTRF')
            THEN
                v_value := v_value + COALESCE(r.totalcost, 0);
            ELSE
                v_value := v_value + ROUND(
                    COALESCE(r.qty_in, 0) * COALESCE(r.pricelst_in, 0),
                    6
                );
            END IF;

            IF v_qty > 0 THEN
                v_avg_cost := ROUND(v_value / v_qty, 6);
            ELSE
                v_avg_cost := 0;
            END IF;

        /* ====================================================
           OUT
           ==================================================== */
        ELSIF BTRIM(COALESCE(r.type_in_out::TEXT, '')) = 'OUT' THEN

            /* Avg cost SEBELUM OUT */
            v_out_avg := v_avg_cost;

            /* Cost OUT = qty × avg cost saat itu */
            v_cost_out := ROUND(
                COALESCE(r.qty_out, 0) * v_out_avg,
                2
            );

            v_qty   := v_qty   - COALESCE(r.qty_out, 0);
            v_value := v_value - v_cost_out;

            /* ------------------------------------------
               NEGATIVE STOCK = INVALID
               ------------------------------------------ */
            IF v_qty < -0.000001 THEN
                PERFORM set_config(
                    'session_replication_role',
                    COALESCE(v_old_replica, 'origin'),
                    true
                );
                RAISE EXCEPTION
                    'NEGATIVE STOCK: idbarang=%, idlocation=%, batch=%, qty_balance=%, docno=%',
                    p_idbarang, p_idlocation, COALESCE(p_batch, ''),
                    v_qty, r.docno;
            END IF;

            IF v_value < -0.01 THEN
                PERFORM set_config(
                    'session_replication_role',
                    COALESCE(v_old_replica, 'origin'),
                    true
                );
                RAISE EXCEPTION
                    'NEGATIVE STOCK VALUE: idbarang=%, idlocation=%, batch=%, value_balance=%, docno=%',
                    p_idbarang, p_idlocation, COALESCE(p_batch, ''),
                    v_value, r.docno;
            END IF;

            /* Noise cleanup */
            IF ABS(v_qty)   < 0.000001 THEN v_qty   := 0; END IF;
            IF ABS(v_value) < 0.01     THEN v_value := 0; END IF;

            IF v_qty > 0 THEN
                v_avg_cost := ROUND(v_value / v_qty, 6);
            ELSE
                v_avg_cost := 0;
            END IF;

            /* ------------------------------------------
               Update unitcost & totalcost OUT
               (trigger dimatikan oleh replica)
               ------------------------------------------ */
            UPDATE sc_trx.stkblc
            SET
                unitcost  = ROUND(v_out_avg, 6),
                totalcost = ROUND(v_cost_out, 2)
            WHERE id = r.id
              AND (
                  unitcost  IS DISTINCT FROM ROUND(v_out_avg, 6)
                  OR totalcost IS DISTINCT FROM ROUND(v_cost_out, 2)
              );

        END IF;
    END LOOP;

    /* =========================================================
       RESTORE session_replication_role
       ========================================================= */
    IF v_old_replica IS NOT NULL THEN
        PERFORM set_config('session_replication_role', v_old_replica, true);
    END IF;

    /* --------------------------------------------------------
       Group kosong → hapus avgcost
       -------------------------------------------------------- */
    IF NOT v_found THEN
        DELETE FROM sc_trx.stkblc_avgcost
        WHERE BTRIM(idbarang)   = BTRIM(p_idbarang)
          AND BTRIM(idlocation) = BTRIM(p_idlocation)
          AND BTRIM(COALESCE(batch, '')) = BTRIM(COALESCE(p_batch, ''));
        RETURN;
    END IF;

    /* --------------------------------------------------------
       Simpan avg cost akhir
       -------------------------------------------------------- */
    INSERT INTO sc_trx.stkblc_avgcost
        (idbarang, idlocation, batch,
         qty, total_value, avg_cost, unit, updated_at)
    VALUES
        (BTRIM(p_idbarang),
         BTRIM(p_idlocation),
         BTRIM(COALESCE(p_batch, '')),
         ROUND(v_qty, 4),
         ROUND(v_value, 4),
         ROUND(v_avg_cost, 4),
         v_unit,
         CURRENT_TIMESTAMP)
    ON CONFLICT (idbarang, idlocation, batch)
    DO UPDATE SET
        qty         = EXCLUDED.qty,
        total_value = EXCLUDED.total_value,
        avg_cost    = EXCLUDED.avg_cost,
        unit        = EXCLUDED.unit,
        updated_at  = CURRENT_TIMESTAMP;

EXCEPTION
    WHEN OTHERS THEN
        /* Safety: restore session_replication_role */
        IF v_old_replica IS NOT NULL THEN
            PERFORM set_config(
                'session_replication_role',
                v_old_replica,
                true
            );
        ELSE
            PERFORM set_config(
                'session_replication_role',
                'origin',
                true
            );
        END IF;
        RAISE;
END;
$$;


/* ============================================================
   4. COMPATIBILITY WRAPPER
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_avgcost
(
    p_stock_uniqueid TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    v_idbarang   TEXT;
    v_idlocation TEXT;
    v_batch      TEXT;
BEGIN

    SELECT
        BTRIM(sb.idbarang::TEXT),
        BTRIM(sb.idlocation::TEXT),
        BTRIM(COALESCE(sb.batch::TEXT, ''))
    INTO
        v_idbarang,
        v_idlocation,
        v_batch
    FROM sc_trx.stkblc sb
    WHERE sb.stock_uniqueid = p_stock_uniqueid
    ORDER BY sb.id
    LIMIT 1;

    IF v_idbarang IS NULL THEN
        RETURN;
    END IF;

    PERFORM sc_trx.fn_recalculate_avgcost_group(
        v_idbarang,
        v_idlocation,
        v_batch
    );
END;
$$;


/* ============================================================
   5. REBUILD SEMUA AVG COST
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_recalculate_all_avgcost()
RETURNS VOID
LANGUAGE plpgsql
AS $$
DECLARE
    r RECORD;
BEGIN

    FOR r IN
        SELECT DISTINCT
            BTRIM(sb.idbarang::TEXT) AS idbarang,
            BTRIM(sb.idlocation::TEXT) AS idlocation,
            BTRIM(COALESCE(sb.batch::TEXT, '')) AS batch
        FROM sc_trx.stkblc sb
        WHERE COALESCE(BTRIM(sb.grouptype::TEXT), 'STOCK')
                <> 'NON STOCK'
        ORDER BY
            BTRIM(sb.idbarang::TEXT),
            BTRIM(sb.idlocation::TEXT),
            BTRIM(COALESCE(sb.batch::TEXT, ''))
    LOOP

        PERFORM sc_trx.fn_recalculate_avgcost_group(
            r.idbarang,
            r.idlocation,
            r.batch
        );

    END LOOP;
END;
$$;


/* ============================================================
   6. TRIGGER STKBLc -> AVGCOST
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_stkblc_avgcost_trigger()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN

    /* Hindari recursive trigger dari UPDATE cost OUT */
    IF pg_trigger_depth() > 1 THEN
        IF TG_OP = 'DELETE' THEN
            RETURN OLD;
        END IF;
        RETURN NEW;
    END IF;


    IF TG_OP = 'INSERT' THEN

        IF COALESCE(BTRIM(NEW.grouptype::TEXT), 'STOCK')
               <> 'NON STOCK'
        THEN
            PERFORM sc_trx.fn_recalculate_avgcost_group(
                BTRIM(NEW.idbarang::TEXT),
                BTRIM(NEW.idlocation::TEXT),
                BTRIM(COALESCE(NEW.batch::TEXT, ''))
            );
        END IF;

        RETURN NEW;


    ELSIF TG_OP = 'DELETE' THEN

        IF COALESCE(BTRIM(OLD.grouptype::TEXT), 'STOCK')
               <> 'NON STOCK'
        THEN
            PERFORM sc_trx.fn_recalculate_avgcost_group(
                BTRIM(OLD.idbarang::TEXT),
                BTRIM(OLD.idlocation::TEXT),
                BTRIM(COALESCE(OLD.batch::TEXT, ''))
            );
        END IF;

        RETURN OLD;


    ELSIF TG_OP = 'UPDATE' THEN

        /* OLD group */
        IF COALESCE(BTRIM(OLD.grouptype::TEXT), 'STOCK')
               <> 'NON STOCK'
        THEN
            PERFORM sc_trx.fn_recalculate_avgcost_group(
                BTRIM(OLD.idbarang::TEXT),
                BTRIM(OLD.idlocation::TEXT),
                BTRIM(COALESCE(OLD.batch::TEXT, ''))
            );
        END IF;

        /* NEW group */
        IF COALESCE(BTRIM(NEW.grouptype::TEXT), 'STOCK')
               <> 'NON STOCK'
        THEN
            PERFORM sc_trx.fn_recalculate_avgcost_group(
                BTRIM(NEW.idbarang::TEXT),
                BTRIM(NEW.idlocation::TEXT),
                BTRIM(COALESCE(NEW.batch::TEXT, ''))
            );
        END IF;

        RETURN NEW;
    END IF;

    RETURN NEW;
END;
$$;


/* ============================================================
   7. TRIGGER
   ============================================================ */

DROP TRIGGER IF EXISTS trg_stkblc_avgcost
ON sc_trx.stkblc;

CREATE TRIGGER trg_stkblc_avgcost
AFTER INSERT OR UPDATE OR DELETE
ON sc_trx.stkblc
FOR EACH ROW
EXECUTE FUNCTION sc_trx.fn_stkblc_avgcost_trigger();


/* ============================================================
   8. VERIFIKASI
   ============================================================ */

DO $$
DECLARE
    v_table_ok   BOOLEAN;
    v_trig_ok    BOOLEAN;
    v_func_ok    INTEGER;
BEGIN

    SELECT EXISTS (
        SELECT 1 FROM information_schema.tables
        WHERE table_schema = 'sc_trx'
          AND table_name   = 'stkblc_avgcost'
    ) INTO v_table_ok;

    SELECT EXISTS (
        SELECT 1 FROM pg_trigger
        WHERE tgrelid = 'sc_trx.stkblc'::regclass
          AND tgname   = 'trg_stkblc_avgcost'
          AND NOT tgisinternal
    ) INTO v_trig_ok;

    SELECT COUNT(*)
    INTO v_func_ok
    FROM pg_proc p
    JOIN pg_namespace n ON n.oid = p.pronamespace
    WHERE n.nspname = 'sc_trx'
      AND p.proname IN (
          'fn_recalculate_avgcost_group',
          'fn_recalculate_avgcost',
          'fn_recalculate_all_avgcost',
          'fn_stkblc_avgcost_trigger'
      );

    RAISE NOTICE '============================================================';
    RAISE NOTICE 'TAHAP 07 INSTALL : OK';
    RAISE NOTICE '  Table    : %', v_table_ok;
    RAISE NOTICE '  Trigger  : %', v_trig_ok;
    RAISE NOTICE '  Function : % (harus 4)', v_func_ok;
    RAISE NOTICE '============================================================';

    IF NOT v_table_ok THEN
        RAISE EXCEPTION 'Table stkblc_avgcost tidak ada.';
    END IF;

    IF NOT v_trig_ok THEN
        RAISE EXCEPTION 'Trigger trg_stkblc_avgcost tidak aktif.';
    END IF;

    IF v_func_ok <> 4 THEN
        RAISE EXCEPTION 'Function terpasang hanya %, harus 4', v_func_ok;
    END IF;

END;
$$;


/* ============================================================
   9. REBUILD MANUAL (TIDAK DIJALANKAN OTOMATIS)

   Setelah instalasi / perbaikan, bila ingin membangun ulang
   seluruh current average cost berdasarkan histori stkblc:

       SELECT sc_trx.fn_recalculate_all_avgcost();

   Jika terdapat negative stock pada histori, function akan
   menghentikan proses dan menunjukkan item/location/batch
   serta transaksi penyebabnya.
   ============================================================ */

-- SELECT sc_trx.fn_recalculate_all_avgcost();


/* ============================================================
   TAHAP 07 SELESAI
   ============================================================

   transaction_dt
          |
          v
       stkblc
          |
          v
   fn_stkblc_avgcost_trigger
          |
          v
   idbarang + idlocation + batch
          |
          v
   stkblc_avgcost

   INSERT / UPDATE / DELETE stkblc
       -> recalculation otomatis

   IN
       -> transfer     : pakai totalcost OUT pasangan
       -> non-transfer : pakai pricelst_in × qty

   OUT
       -> memakai average cost sebelum OUT
       -> cost ditulis ke stkblc.unitcost / totalcost

   NEGATIVE STOCK
       -> ERROR
       -> bukan diubah menjadi avg cost negatif

   ============================================================ */