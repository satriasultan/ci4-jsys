/* ============================================================
   JSYS ERP
   TAHAP 07 - STOCK AVERAGE COST
   TABLE : sc_trx.stkblc_avgcost
   ============================================================

   FINAL REPAIR - COMPATIBLE WITH ACTUAL LEGACY SCHEMA
   ------------------------------------------------------------
   sc_trx.stkblc_avgcost ACTUAL:
       idbarang       VARCHAR(20) / CHAR(20)
       idlocation     VARCHAR(12) / CHAR(12)
       batch          VARCHAR(100) / CHAR(100)
       qty            NUMERIC(18,4)
       total_value    NUMERIC(18,4)
       avg_cost       NUMERIC(18,4)
       unit           VARCHAR(12)
       updated_at     TIMESTAMP

   IMPORTANT:
   ------------------------------------------------------------
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
   3. INDEX
      PK SUDAH MENUTUPI LOOKUP UTAMA:
          idbarang + idlocation + batch
   ============================================================ */

-- Tidak membuat index tambahan yang tidak diperlukan.


/* ============================================================
   4. CORE RECALCULATE

   Fungsi ini menghitung ulang SATU stock group legacy:

       idbarang + idlocation + batch

   BUKAN berdasarkan stock_uniqueid pada tabel avgcost,
   karena kolom tersebut memang tidak ada pada schema aktual.
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

    v_qty_in         NUMERIC(18,6) := 0;
    v_qty_out        NUMERIC(18,6) := 0;

    v_value_in       NUMERIC(18,6) := 0;
    v_value_out      NUMERIC(18,6) := 0;

    v_cost_out       NUMERIC(18,6) := 0;
    v_out_avg        NUMERIC(18,6) := 0;

    v_unit           VARCHAR(12);
    v_found          BOOLEAN := FALSE;
BEGIN

    /* --------------------------------------------------------
       VALIDASI INPUT
       -------------------------------------------------------- */
    IF NULLIF(BTRIM(p_idbarang), '') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 07 GAGAL: idbarang kosong.';
    END IF;

    IF NULLIF(BTRIM(p_idlocation), '') IS NULL THEN
        RAISE EXCEPTION
            'TAHAP 07 GAGAL: idlocation kosong untuk idbarang=%',
            p_idbarang;
    END IF;


    /* --------------------------------------------------------
       HITUNG ULANG SECARA KRONOLOGIS

       docdate = urutan bisnis
       id      = tie breaker tanggal yang sama
       -------------------------------------------------------- */
    FOR r IN
        SELECT
            sb.id,
            sb.uniqueid,
            sb.docdate,
            sb.trxdate,
            sb.type_in_out,
            sb.qty_in,
            sb.qty_out,
            sb.pricelst_in,
            sb.unit,
            sb.docno,
            sb.journal_type
        FROM sc_trx.stkblc sb
        WHERE BTRIM(sb.idbarang::TEXT) = BTRIM(p_idbarang)
          AND BTRIM(sb.idlocation::TEXT) = BTRIM(p_idlocation)
          AND BTRIM(COALESCE(sb.batch::TEXT, ''))
                = BTRIM(COALESCE(p_batch, ''))
          AND COALESCE(BTRIM(sb.grouptype::TEXT), 'STOCK')
                <> 'NON STOCK'
        ORDER BY
            COALESCE(sb.docdate, sb.trxdate::DATE),
            sb.id
    LOOP

        v_found := TRUE;
        v_unit := NULLIF(BTRIM(COALESCE(r.unit::TEXT, '')), '');


        /* ----------------------------------------------------
           IN
           ---------------------------------------------------- */
        IF BTRIM(COALESCE(r.type_in_out::TEXT, '')) = 'IN'
        THEN

            v_qty_in :=
                v_qty_in + COALESCE(r.qty_in, 0);

            v_value_in :=
                v_value_in
                + ROUND(
                    COALESCE(r.qty_in, 0)
                    * COALESCE(r.pricelst_in, 0),
                    6
                  );

            v_qty :=
                v_qty + COALESCE(r.qty_in, 0);

            v_value :=
                v_value
                + ROUND(
                    COALESCE(r.qty_in, 0)
                    * COALESCE(r.pricelst_in, 0),
                    6
                  );

            IF v_qty > 0 THEN
                v_avg_cost :=
                    ROUND(v_value / v_qty, 6);
            ELSE
                v_avg_cost := 0;
            END IF;


        /* ----------------------------------------------------
           OUT
           ---------------------------------------------------- */
        ELSIF BTRIM(COALESCE(r.type_in_out::TEXT, '')) = 'OUT'
        THEN

            /* Average cost SEBELUM transaksi OUT */
            v_out_avg := v_avg_cost;

            v_cost_out :=
                ROUND(
                    COALESCE(r.qty_out, 0) * v_out_avg,
                    2
                );

            v_qty_out :=
                v_qty_out + COALESCE(r.qty_out, 0);

            v_value_out :=
                v_value_out + v_cost_out;

            v_qty :=
                v_qty - COALESCE(r.qty_out, 0);

            v_value :=
                v_value - v_cost_out;

            /* ------------------------------------------------
               NEGATIVE STOCK = INVALID STATE
               ------------------------------------------------ */
            IF v_qty < -0.000001 THEN
                RAISE EXCEPTION
                    'NEGATIVE STOCK: idbarang=%, idlocation=%, batch=%, qty_balance=%, uniqueid=%, docno=%',
                    p_idbarang,
                    p_idlocation,
                    COALESCE(p_batch, ''),
                    v_qty,
                    r.uniqueid,
                    r.docno;
            END IF;

            IF v_value < -0.01 THEN
                RAISE EXCEPTION
                    'NEGATIVE STOCK VALUE: idbarang=%, idlocation=%, batch=%, value_balance=%, uniqueid=%, docno=%',
                    p_idbarang,
                    p_idlocation,
                    COALESCE(p_batch, ''),
                    v_value,
                    r.uniqueid,
                    r.docno;
            END IF;

            /* Hilangkan noise pembulatan */
            IF ABS(v_qty) < 0.000001 THEN
                v_qty := 0;
            END IF;

            IF ABS(v_value) < 0.01 THEN
                v_value := 0;
            END IF;

            IF v_qty > 0 THEN
                v_avg_cost :=
                    ROUND(v_value / v_qty, 6);
            ELSE
                v_avg_cost := 0;
            END IF;

            /* ------------------------------------------------
               TAHAP 07 menetapkan cost OUT.
               TAHAP 06 tidak diubah.
               ------------------------------------------------ */
            UPDATE sc_trx.stkblc
            SET
                unitcost = ROUND(v_out_avg, 6),
                totalcost = ROUND(v_cost_out, 2)
            WHERE id = r.id
              AND
              (
                  unitcost IS DISTINCT FROM ROUND(v_out_avg, 6)
                  OR totalcost IS DISTINCT FROM ROUND(v_cost_out, 2)
              );

        END IF;

    END LOOP;


    /* --------------------------------------------------------
       Tidak ada transaksi stock untuk group ini.
       Hapus current avgcost row jika masih tersisa.
       -------------------------------------------------------- */
    IF NOT v_found THEN

        DELETE FROM sc_trx.stkblc_avgcost
        WHERE BTRIM(idbarang) = BTRIM(p_idbarang)
          AND BTRIM(idlocation) = BTRIM(p_idlocation)
          AND BTRIM(COALESCE(batch, ''))
                = BTRIM(COALESCE(p_batch, ''));

        RETURN;
    END IF;


    /* --------------------------------------------------------
       Simpan current average cost.
       Schema actual hanya mempunyai:
           idbarang
           idlocation
           batch
           qty
           total_value
           avg_cost
           unit
           updated_at
       -------------------------------------------------------- */
    INSERT INTO sc_trx.stkblc_avgcost
    (
        idbarang,
        idlocation,
        batch,
        qty,
        total_value,
        avg_cost,
        unit,
        updated_at
    )
    VALUES
    (
        BTRIM(p_idbarang),
        BTRIM(p_idlocation),
        BTRIM(COALESCE(p_batch, '')),
        ROUND(v_qty, 4),
        ROUND(v_value, 4),
        ROUND(v_avg_cost, 4),
        v_unit,
        CURRENT_TIMESTAMP
    )
    ON CONFLICT (idbarang, idlocation, batch)
    DO UPDATE SET
        qty = EXCLUDED.qty,
        total_value = EXCLUDED.total_value,
        avg_cost = EXCLUDED.avg_cost,
        unit = EXCLUDED.unit,
        updated_at = CURRENT_TIMESTAMP;

END;
$$;


/* ============================================================
   5. COMPATIBILITY WRAPPER

   TAHAP 06 menggunakan stkblc.stock_uniqueid.
   Wrapper ini mencari group legacy dari row stkblc tersebut.
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

    /*
       Jika transaction/stock row sudah tidak ada,
       tidak bisa menentukan group dari stock_uniqueid.
       Caller DELETE harus menggunakan helper group trigger
       yang menyimpan OLD dimensions.
    */
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
   6. REBUILD SEMUA AVG COST

   Rebuild mengikuti schema legacy:
       idbarang + idlocation + batch
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
   7. TRIGGER STKBLc -> AVGCOST

   INSERT:
       recalculate group

   DELETE:
       recalculate OLD group

   UPDATE:
       OLD group direcalculate
       NEW group direcalculate

   pg_trigger_depth() > 1 mencegah recursive recalculate
   ketika TAHAP 07 mengubah unitcost / totalcost OUT.
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
            /* Jika group sama, hitung sekali lagi tetap aman;
               trigger depth mencegah recursive costing. */
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
   9. VALIDASI STRUCTURE ACTUAL
   ============================================================ */

SELECT
    ordinal_position,
    column_name,
    data_type,
    character_maximum_length,
    numeric_precision,
    numeric_scale,
    is_nullable
FROM information_schema.columns
WHERE table_schema = 'sc_trx'
  AND table_name = 'stkblc_avgcost'
ORDER BY ordinal_position;


/* ============================================================
   10. CEK PRIMARY KEY ACTUAL
   ============================================================ */

SELECT
    conname,
    pg_get_constraintdef(oid)
FROM pg_constraint
WHERE conrelid = 'sc_trx.stkblc_avgcost'::regclass
  AND contype = 'p';


/* ============================================================
   11. REBUILD MANUAL

   Tidak dijalankan otomatis.

   Setelah instalasi / perbaikan, bila ingin membangun ulang
   seluruh current average cost berdasarkan histori stkblc:

       SELECT sc_trx.fn_recalculate_all_avgcost();

   Jika terdapat negative stock pada histori, function akan
   menghentikan proses dan menunjukkan item/location/batch
   serta transaksi penyebabnya.
   ============================================================ */

/* SELECT sc_trx.fn_recalculate_all_avgcost(); */


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

   OUT
       -> memakai average cost sebelum OUT
       -> cost ditulis ke stkblc.unitcost / totalcost

   NEGATIVE STOCK
       -> ERROR
       -> bukan diubah menjadi avg cost negatif

   ============================================================ */
