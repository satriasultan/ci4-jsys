/*
===============================================================================
JSYS ERP - PATCH PRECISION FOR po.sql
Target : PostgreSQL / schema sc_tmp & sc_trx
Purpose:
  - qty, harga, nilai, dpp, pajak, total, diskon, konversi, qtylpb/qtyvoid
    -> NUMERIC(18,4)
  - kurs -> NUMERIC(18,6)  (exchange rate harus 6 desimal)
  - jthtempo -> NUMERIC(18,4)
Safety:
  - Satu transaksi; error rollback seluruh patch.
  - Non-destruktif: hanya ALTER COLUMN TYPE.
  - View yang bergantung pada kolom target otomatis di-DROP dan
    di-CREATE ulang di dalam transaksi yang sama.
  - Tidak mengubah function/trigger po.sql (hanya tipe kolom).
  - Rerun-safe: kolom yang sudah 18,4 di-skip.
===============================================================================
*/

BEGIN;

SET LOCAL lock_timeout = '30s';
SET LOCAL statement_timeout = '0';

SELECT pg_advisory_xact_lock(hashtext('JSYS_PATCH_PO_PRECISION'));

-------------------------------------------------------------------------------
-- 1. DEPENDENCY CHECK
-------------------------------------------------------------------------------
DO $$
DECLARE
    v_table TEXT;
BEGIN
    FOREACH v_table IN ARRAY ARRAY[
        'sc_tmp.po',
        'sc_trx.po',
        'sc_tmp.po_dtl',
        'sc_trx.po_dtl'
    ]
    LOOP
        IF to_regclass(v_table) IS NULL THEN
            RAISE EXCEPTION
                'PATCH DIBATALKAN: tabel % tidak ditemukan. Jalankan po.sql dulu.',
                v_table;
        END IF;
    END LOOP;
END;
$$;

-------------------------------------------------------------------------------
-- 2. DROP VIEW YANG BERGANTUNG (AUTO-DETECT) LALU ALTER COLUMN LALU RE-CREATE
-------------------------------------------------------------------------------
DO $$
DECLARE
    -- Daftar kolom target: (schema, table, column, tipe_target)
    r RECORD;

    -- Simpan view yang bergantung
    v_view_schema TEXT;
    v_view_name   TEXT;
    v_view_def    TEXT;
    v_view_names  TEXT[] := ARRAY[]::TEXT[];
    v_view_defs   TEXT[] := ARRAY[]::TEXT[];

    v_type        TEXT;
    v_scale       INTEGER;
    v_count       INTEGER := 0;
    v_target_type TEXT;
BEGIN

    -- ============================================================
    -- FASE A: Deteksi & DROP view yang bergantung pada kolom target
    -- ============================================================
    FOR r IN
        SELECT DISTINCT
            n.nspname AS view_schema,
            c.relname AS view_name
        FROM pg_depend d
        JOIN pg_rewrite rw ON rw.oid = d.objid
        JOIN pg_class c ON c.oid = rw.ev_class
        JOIN pg_namespace n ON n.oid = c.relnamespace
        JOIN pg_class tbl ON tbl.oid = d.refobjid
        JOIN pg_namespace tnsp ON tnsp.oid = tbl.relnamespace
        JOIN pg_attribute a ON a.attrelid = tbl.oid AND a.attnum = d.refobjsubid
        WHERE d.classid = 'pg_rewrite'::regclass
          AND d.refclassid = 'pg_class'::regclass
          AND c.relkind = 'v'
          AND (
              (tnsp.nspname = 'sc_tmp' AND tbl.relname IN ('po','po_dtl')
                  AND a.attname IN ('jthtempo','kurs','dpp','jumlahpajak','total'))
              OR
              (tnsp.nspname = 'sc_trx' AND tbl.relname IN ('po','po_dtl')
                  AND a.attname IN ('jthtempo','kurs','dpp','jumlahpajak','total'))
          )
    LOOP
        v_view_def := pg_get_viewdef(
            format('%I.%I', r.view_schema, r.view_name)::regclass, true
        );
        v_view_names := array_append(v_view_names, format('%I.%I', r.view_schema, r.view_name));
        v_view_defs  := array_append(v_view_defs, v_view_def);

        RAISE NOTICE 'DROP view sementara: %', format('%I.%I', r.view_schema, r.view_name);
        EXECUTE format('DROP VIEW IF EXISTS %I.%I', r.view_schema, r.view_name);
    END LOOP;

    -- ============================================================
    -- FASE B: ALTER kolom
    -- ============================================================
    FOR r IN
        SELECT *
        FROM (VALUES
            -- HEADER TMP
            ('sc_tmp', 'po', 'jthtempo',      'NUMERIC(18,4)'),
            ('sc_tmp', 'po', 'kurs',          'NUMERIC(18,6)'),
            ('sc_tmp', 'po', 'dpp',           'NUMERIC(18,4)'),
            ('sc_tmp', 'po', 'jumlahpajak',   'NUMERIC(18,4)'),
            ('sc_tmp', 'po', 'total',         'NUMERIC(18,4)'),

            -- HEADER TRX
            ('sc_trx', 'po', 'jthtempo',      'NUMERIC(18,4)'),
            ('sc_trx', 'po', 'kurs',          'NUMERIC(18,6)'),
            ('sc_trx', 'po', 'dpp',           'NUMERIC(18,4)'),
            ('sc_trx', 'po', 'jumlahpajak',   'NUMERIC(18,4)'),
            ('sc_trx', 'po', 'total',         'NUMERIC(18,4)'),

            -- DETAIL TMP
            ('sc_tmp', 'po_dtl', 'qty',           'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'qtybonus',      'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'harga',         'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'multidisc',     'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'nilai',         'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'kurs',          'NUMERIC(18,6)'),
            ('sc_tmp', 'po_dtl', 'nilaikonversi', 'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'nilaipajak',    'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'qtylpb',        'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'qtyvoid',       'NUMERIC(18,4)'),
            ('sc_tmp', 'po_dtl', 'totaldiscount', 'NUMERIC(18,4)'),

            -- DETAIL TRX
            ('sc_trx', 'po_dtl', 'qty',           'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'qtybonus',      'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'harga',         'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'multidisc',     'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'nilai',         'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'kurs',          'NUMERIC(18,6)'),
            ('sc_trx', 'po_dtl', 'nilaikonversi', 'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'nilaipajak',    'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'qtylpb',        'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'qtyvoid',       'NUMERIC(18,4)'),
            ('sc_trx', 'po_dtl', 'totaldiscount', 'NUMERIC(18,4)')
        ) AS x(schema_name, table_name, column_name, target_type)
    LOOP
        v_target_type := r.target_type;

        SELECT c.data_type, c.numeric_scale
        INTO v_type, v_scale
        FROM information_schema.columns c
        WHERE c.table_schema = r.schema_name
          AND c.table_name   = r.table_name
          AND c.column_name  = r.column_name;

        IF v_type IS NULL THEN
            RAISE NOTICE 'Skip (kolom tidak ada): %.%.%',
                r.schema_name, r.table_name, r.column_name;
            CONTINUE;
        END IF;

        IF v_type <> 'numeric' THEN
            RAISE EXCEPTION
                'Kolom %.%.% bertipe %, bukan numeric.',
                r.schema_name, r.table_name, r.column_name, v_type;
        END IF;

        -- Skip jika sudah sesuai target
        IF (v_target_type = 'NUMERIC(18,4)' AND v_scale = 4)
           OR (v_target_type = 'NUMERIC(18,6)' AND v_scale = 6) THEN
            RAISE NOTICE 'Skip (sudah sesuai): %.%.% = %',
                r.schema_name, r.table_name, r.column_name, v_target_type;
            CONTINUE;
        END IF;

        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN %I TYPE %s USING %I::%s',
            r.schema_name, r.table_name, r.column_name,
            v_target_type,
            r.column_name, v_target_type
        );

        v_count := v_count + 1;
        RAISE NOTICE 'Altered: %.%.% -> %',
            r.schema_name, r.table_name, r.column_name, v_target_type;
    END LOOP;

    -- ============================================================
    -- FASE C: Re-CREATE semua view yang sebelumnya di-DROP
    -- ============================================================
    FOR i IN 1 .. COALESCE(array_length(v_view_defs, 1), 0) LOOP
        EXECUTE format('CREATE OR REPLACE VIEW %s AS %s',
                       v_view_names[i], v_view_defs[i]);
        RAISE NOTICE 'View dipulihkan: %', v_view_names[i];
    END LOOP;

    RAISE NOTICE 'Total kolom diubah: %, view dipulihkan: %',
        v_count, COALESCE(array_length(v_view_defs, 1), 0);
END;
$$;

-------------------------------------------------------------------------------
-- 3. VALIDASI TIPE AKHIR
-------------------------------------------------------------------------------
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT *
        FROM (VALUES
            ('sc_tmp', 'po', 'jthtempo',      18, 4),
            ('sc_tmp', 'po', 'kurs',          18, 6),
            ('sc_tmp', 'po', 'dpp',           18, 4),
            ('sc_tmp', 'po', 'jumlahpajak',   18, 4),
            ('sc_tmp', 'po', 'total',         18, 4),

            ('sc_trx', 'po', 'jthtempo',      18, 4),
            ('sc_trx', 'po', 'kurs',          18, 6),
            ('sc_trx', 'po', 'dpp',           18, 4),
            ('sc_trx', 'po', 'jumlahpajak',   18, 4),
            ('sc_trx', 'po', 'total',         18, 4),

            ('sc_tmp', 'po_dtl', 'qty',           18, 4),
            ('sc_tmp', 'po_dtl', 'qtybonus',      18, 4),
            ('sc_tmp', 'po_dtl', 'harga',         18, 4),
            ('sc_tmp', 'po_dtl', 'multidisc',     18, 4),
            ('sc_tmp', 'po_dtl', 'nilai',         18, 4),
            ('sc_tmp', 'po_dtl', 'kurs',          18, 6),
            ('sc_tmp', 'po_dtl', 'nilaikonversi', 18, 4),
            ('sc_tmp', 'po_dtl', 'nilaipajak',    18, 4),
            ('sc_tmp', 'po_dtl', 'qtylpb',        18, 4),
            ('sc_tmp', 'po_dtl', 'qtyvoid',       18, 4),
            ('sc_tmp', 'po_dtl', 'totaldiscount', 18, 4),

            ('sc_trx', 'po_dtl', 'qty',           18, 4),
            ('sc_trx', 'po_dtl', 'qtybonus',      18, 4),
            ('sc_trx', 'po_dtl', 'harga',         18, 4),
            ('sc_trx', 'po_dtl', 'multidisc',     18, 4),
            ('sc_trx', 'po_dtl', 'nilai',         18, 4),
            ('sc_trx', 'po_dtl', 'kurs',          18, 6),
            ('sc_trx', 'po_dtl', 'nilaikonversi', 18, 4),
            ('sc_trx', 'po_dtl', 'nilaipajak',    18, 4),
            ('sc_trx', 'po_dtl', 'qtylpb',        18, 4),
            ('sc_trx', 'po_dtl', 'qtyvoid',       18, 4),
            ('sc_trx', 'po_dtl', 'totaldiscount', 18, 4)
        ) AS x(schema_name, table_name, column_name, expected_precision, expected_scale)
    LOOP
        IF EXISTS (
            SELECT 1 FROM information_schema.columns c
            WHERE c.table_schema = r.schema_name
              AND c.table_name   = r.table_name
              AND c.column_name  = r.column_name
              AND (
                  c.data_type <> 'numeric'
                  OR c.numeric_precision <> r.expected_precision
                  OR c.numeric_scale     <> r.expected_scale
              )
        ) THEN
            RAISE EXCEPTION
                'Validasi gagal: %.%.% bukan NUMERIC(%,%)',
                r.schema_name, r.table_name, r.column_name,
                r.expected_precision, r.expected_scale;
        END IF;
    END LOOP;

    RAISE NOTICE 'Validasi tipe kolom: LULUS';
END;
$$;

-------------------------------------------------------------------------------
-- 4. RESULT
-------------------------------------------------------------------------------
DO $$
BEGIN
    RAISE NOTICE '=============================================================';
    RAISE NOTICE 'PATCH PRECISION po.sql BERHASIL';
    RAISE NOTICE 'qty / harga / nilai / dpp / pajak / total / diskon : 4 dec';
    RAISE NOTICE 'kurs                                                : 6 dec';
    RAISE NOTICE 'jthtempo                                            : 4 dec';
    RAISE NOTICE '=============================================================';
END;
$$;

COMMIT;

-------------------------------------------------------------------------------
-- 5. POST-RUN INSPECTION (OPSIONAL)
-------------------------------------------------------------------------------
-- SELECT table_schema, table_name, column_name,
--        numeric_precision, numeric_scale
-- FROM information_schema.columns
-- WHERE table_schema IN ('sc_tmp','sc_trx')
--   AND table_name IN ('po','po_dtl')
--   AND data_type = 'numeric'
-- ORDER BY table_schema, table_name, ordinal_position;