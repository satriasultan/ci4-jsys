-- =============================================================================
-- PATCH NUMERIC(18,2) -> NUMERIC(18,4) — MODUL PENJUALAN
-- =============================================================================
-- Cakupan:
--   sc_tmp & sc_trx : salesorder, deliveryorder, suratjalan, penjualan
--   sc_tmp & sc_trx : salesorder_dtl, deliveryorder_dtl, suratjalan_dtl, penjualan_dtl
--
-- Catatan:
--   * Script ini TIDAK dijalankan otomatis oleh aplikasi.
--   * Aman dijalankan ulang: kolom yang sudah scale >= 4 dilewati.
--   * Jalankan manual, contoh:
--       php writable/dbq.php -m -f DatabaseChanges/PENJUALAN/PATCH_sales_numeric_18_4.sql
-- =============================================================================
BEGIN;

DO $$
DECLARE
    r record;
BEGIN
    FOR r IN
        SELECT * FROM (VALUES
            -- ===== HEADER (sc_tmp + sc_trx) =====
            ('sc_tmp','salesorder','jthtempo'),
            ('sc_tmp','salesorder','kurs'),
            ('sc_tmp','salesorder','dpp'),
            ('sc_tmp','salesorder','jumlahpajak'),
            ('sc_tmp','salesorder','total'),
            ('sc_trx','salesorder','jthtempo'),
            ('sc_trx','salesorder','kurs'),
            ('sc_trx','salesorder','dpp'),
            ('sc_trx','salesorder','jumlahpajak'),
            ('sc_trx','salesorder','total'),

            ('sc_tmp','deliveryorder','jthtempo'),
            ('sc_tmp','deliveryorder','kurs'),
            ('sc_tmp','deliveryorder','dpp'),
            ('sc_tmp','deliveryorder','jumlahpajak'),
            ('sc_tmp','deliveryorder','total'),
            ('sc_trx','deliveryorder','jthtempo'),
            ('sc_trx','deliveryorder','kurs'),
            ('sc_trx','deliveryorder','dpp'),
            ('sc_trx','deliveryorder','jumlahpajak'),
            ('sc_trx','deliveryorder','total'),

            ('sc_tmp','suratjalan','jthtempo'),
            ('sc_tmp','suratjalan','kurs'),
            ('sc_tmp','suratjalan','dpp'),
            ('sc_tmp','suratjalan','jumlahpajak'),
            ('sc_tmp','suratjalan','total'),
            ('sc_trx','suratjalan','jthtempo'),
            ('sc_trx','suratjalan','kurs'),
            ('sc_trx','suratjalan','dpp'),
            ('sc_trx','suratjalan','jumlahpajak'),
            ('sc_trx','suratjalan','total'),

            ('sc_tmp','penjualan','jthtempo'),
            ('sc_tmp','penjualan','kurs'),
            ('sc_tmp','penjualan','dpp'),
            ('sc_tmp','penjualan','jumlahpajak'),
            ('sc_tmp','penjualan','total'),
            ('sc_trx','penjualan','jthtempo'),
            ('sc_trx','penjualan','kurs'),
            ('sc_trx','penjualan','dpp'),
            ('sc_trx','penjualan','jumlahpajak'),
            ('sc_trx','penjualan','total')
        ) AS x(sch, tbl, col)

        UNION ALL

        -- ===== DETAIL (sc_tmp + sc_trx) =====
        SELECT s.sch, t.tbl, trim(c.col)
        FROM (VALUES ('sc_tmp'), ('sc_trx')) AS s(sch)
        CROSS JOIN (VALUES
            ('salesorder_dtl',    'qty,harga,multidisc,nilai,kurs,nilaikonversi,nilaipajak,qtypenjualan,qtyretur,qtydo'),
            ('deliveryorder_dtl', 'qty,harga,multidisc,nilai,kurs,nilaikonversi,nilaipajak,qtysj'),
            ('suratjalan_dtl',    'qty,harga,multidisc,nilai,kurs,nilaikonversi,nilaipajak,qtypenjualan'),
            ('penjualan_dtl',     'qty,harga,multidisc,nilai,kurs,nilaikonversi,nilaipajak,qtyretur')
        ) AS t(tbl, cols)
        CROSS JOIN LATERAL unnest(string_to_array(t.cols, ',')) AS c(col)
    LOOP
        IF EXISTS (
            SELECT 1
            FROM information_schema.columns c
            WHERE c.table_schema = r.sch
              AND c.table_name   = r.tbl
              AND c.column_name  = r.col
              AND c.data_type    = 'numeric'
              AND COALESCE(c.numeric_scale, 0) < 4
        ) THEN
            EXECUTE format(
                'ALTER TABLE %I.%I ALTER COLUMN %I TYPE NUMERIC(18,4) USING %I::NUMERIC(18,4)',
                r.sch, r.tbl, r.col, r.col
            );
            RAISE NOTICE 'ALTER %.%.% -> NUMERIC(18,4)', r.sch, r.tbl, r.col;
        END IF;
    END LOOP;
END $$;

COMMIT;

-- =============================================================================
-- VERIFIKASI (read-only)
-- Kolom di bawah harus menunjukkan numeric_scale = 4.
-- =============================================================================
SELECT table_schema, table_name, column_name, numeric_precision, numeric_scale
FROM information_schema.columns
WHERE table_schema IN ('sc_tmp', 'sc_trx')
  AND table_name IN (
        'salesorder', 'deliveryorder', 'suratjalan', 'penjualan',
        'salesorder_dtl', 'deliveryorder_dtl', 'suratjalan_dtl', 'penjualan_dtl'
  )
  AND data_type = 'numeric'
ORDER BY table_schema, table_name, ordinal_position;

