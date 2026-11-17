/*
===============================================================================
JSYS ERP - PATCH lpb.sql
  - Precision NUMERIC(18,4) untuk qty/harga/nilai/pajak/diskon
  - Kurs -> NUMERIC(18,6)
  - Tambah kolom paymenttype CHAR(20), polokal CHAR(20)
    di header & detail (tmp & trx) mengacu pada po.sql

Target : PostgreSQL / schema sc_tmp & sc_trx
Safety :
  - Satu transaksi, error rollback seluruhnya.
  - Non-destruktif: ALTER COLUMN TYPE + ADD COLUMN IF NOT EXISTS.
  - View yang bergantung pada kolom target otomatis di-DROP & CREATE ulang.
  - Function lpb.sql TIDAK di-recompile (hanya INSERT...SELECT, otomatis cast).
  - Rerun-safe: kolom yang sudah sesuai di-skip.
===============================================================================
*/

BEGIN;

SET LOCAL lock_timeout = '30s';
SET LOCAL statement_timeout = '0';

SELECT pg_advisory_xact_lock(hashtext('JSYS_PATCH_LPB_PRECISION_AND_COLS'));

-------------------------------------------------------------------------------
-- 1. DEPENDENCY CHECK
-------------------------------------------------------------------------------
DO $$
DECLARE
    v_table TEXT;
BEGIN
    FOREACH v_table IN ARRAY ARRAY[
        'sc_tmp.lpb','sc_trx.lpb',
        'sc_tmp.lpb_dtl','sc_trx.lpb_dtl'
    ]
    LOOP
        IF to_regclass(v_table) IS NULL THEN
            RAISE EXCEPTION
                'PATCH DIBATALKAN: tabel % tidak ditemukan. Jalankan lpb.sql dulu.',
                v_table;
        END IF;
    END LOOP;
END;
$$;

-------------------------------------------------------------------------------
-- 2. ADD KOLOM BARU (paymenttype, polokal) -- SEBELUM ALTER TIPE
--    Agar view yang SELECT * otomatis ikut, urutan: add kolom dulu,
--    baru drop view, alter tipe, restore view.
-------------------------------------------------------------------------------
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT * FROM (VALUES
            ('sc_tmp','lpb'), ('sc_trx','lpb'),
            ('sc_tmp','lpb_dtl'), ('sc_trx','lpb_dtl')
        ) AS x(schema_name, table_name)
    LOOP
        EXECUTE format(
            'ALTER TABLE %I.%I
             ADD COLUMN IF NOT EXISTS paymenttype CHAR(20),
             ADD COLUMN IF NOT EXISTS polokal     CHAR(20)',
            r.schema_name, r.table_name
        );
        RAISE NOTICE 'Kolom paymenttype/polokal siap: %.%',
            r.schema_name, r.table_name;
    END LOOP;
END;
$$;

-------------------------------------------------------------------------------
-- 3. DROP VIEW BERGANTUNG -> ALTER TIPE -> RESTORE VIEW
-------------------------------------------------------------------------------
DO $$
DECLARE
    r RECORD;

    v_view_schema TEXT;
    v_view_name   TEXT;
    v_view_def    TEXT;
    v_view_names  TEXT[] := ARRAY[]::TEXT[];
    v_view_defs   TEXT[] := ARRAY[]::TEXT[];

    v_type        TEXT;
    v_scale       INTEGER;
    v_count       INTEGER := 0;
    v_target_type TEXT;
    v_target_scale INTEGER;
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
              (tnsp.nspname = 'sc_tmp' AND tbl.relname = 'lpb'
                  AND a.attname IN ('jthtempo','biayavol','biayavol2','kurs',
                                    'dpp','jumlahpajak','total'))
              OR
              (tnsp.nspname = 'sc_trx' AND tbl.relname = 'lpb'
                  AND a.attname IN ('jthtempo','biayavol','biayavol2','kurs',
                                    'dpp','jumlahpajak','total'))
              OR
              (tnsp.nspname = 'sc_tmp' AND tbl.relname = 'lpb_dtl'
                  AND a.attname IN ('qty','qtybonus','qtyretur',
                                    'harga','multidisc','volitem','biaya','biaya2',
                                    'nilai','nilaikonversi','nilaipajak',
                                    'totaldiscount','kurs'))
              OR
              (tnsp.nspname = 'sc_trx' AND tbl.relname = 'lpb_dtl'
                  AND a.attname IN ('qty','qtybonus','qtyretur',
                                    'harga','multidisc','volitem','biaya','biaya2',
                                    'nilai','nilaikonversi','nilaipajak',
                                    'totaldiscount','kurs'))
          )
    LOOP
        v_view_def := pg_get_viewdef(
            format('%I.%I', r.view_schema, r.view_name)::regclass, true
        );
        v_view_names := array_append(v_view_names,
            format('%I.%I', r.view_schema, r.view_name));
        v_view_defs := array_append(v_view_defs, v_view_def);

        RAISE NOTICE 'DROP view sementara: %',
            format('%I.%I', r.view_schema, r.view_name);
        EXECUTE format('DROP VIEW IF EXISTS %I.%I',
            r.view_schema, r.view_name);
    END LOOP;

    -- ============================================================
    -- FASE B: ALTER kolom ke tipe target
    -- ============================================================
    FOR r IN
        SELECT * FROM (VALUES
            -- HEADER TMP
            ('sc_tmp','lpb','jthtempo',      'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb','biayavol',      'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb','biayavol2',     'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb','kurs',          'NUMERIC(18,6)', 6),
            ('sc_tmp','lpb','dpp',           'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb','jumlahpajak',   'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb','total',         'NUMERIC(18,4)', 4),

            -- HEADER TRX
            ('sc_trx','lpb','jthtempo',      'NUMERIC(18,4)', 4),
            ('sc_trx','lpb','biayavol',      'NUMERIC(18,4)', 4),
            ('sc_trx','lpb','biayavol2',     'NUMERIC(18,4)', 4),
            ('sc_trx','lpb','kurs',          'NUMERIC(18,6)', 6),
            ('sc_trx','lpb','dpp',           'NUMERIC(18,4)', 4),
            ('sc_trx','lpb','jumlahpajak',   'NUMERIC(18,4)', 4),
            ('sc_trx','lpb','total',         'NUMERIC(18,4)', 4),

            -- DETAIL TMP
            ('sc_tmp','lpb_dtl','qty',           'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','qtybonus',      'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','qtyretur',      'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','harga',         'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','multidisc',     'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','volitem',       'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','biaya',         'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','biaya2',        'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','nilai',         'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','nilaikonversi', 'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','nilaipajak',    'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','totaldiscount', 'NUMERIC(18,4)', 4),
            ('sc_tmp','lpb_dtl','kurs',          'NUMERIC(18,6)', 6),

            -- DETAIL TRX
            ('sc_trx','lpb_dtl','qty',           'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','qtybonus',      'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','qtyretur',      'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','harga',         'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','multidisc',     'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','volitem',       'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','biaya',         'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','biaya2',        'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','nilai',         'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','nilaikonversi', 'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','nilaipajak',    'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','totaldiscount', 'NUMERIC(18,4)', 4),
            ('sc_trx','lpb_dtl','kurs',          'NUMERIC(18,6)', 6)
        ) AS x(schema_name, table_name, column_name, target_type, target_scale)
    LOOP
        v_target_type  := r.target_type;
        v_target_scale := r.target_scale;

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

        IF v_scale = v_target_scale THEN
            RAISE NOTICE 'Skip (sudah %): %.%.%',
                v_target_type, r.schema_name, r.table_name, r.column_name;
            CONTINUE;
        END IF;

        EXECUTE format(
            'ALTER TABLE %I.%I ALTER COLUMN %I TYPE %s USING %I::%s',
            r.schema_name, r.table_name, r.column_name,
            v_target_type, r.column_name, v_target_type
        );

        v_count := v_count + 1;
        RAISE NOTICE 'Altered: %.%.% -> %',
            r.schema_name, r.table_name, r.column_name, v_target_type;
    END LOOP;

    -- ============================================================
    -- FASE C: Restore semua view
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
-- 4. VALIDASI TIPE KOLOM
-------------------------------------------------------------------------------
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT * FROM (VALUES
            ('sc_tmp','lpb','jthtempo',      18, 4),
            ('sc_tmp','lpb','biayavol',      18, 4),
            ('sc_tmp','lpb','biayavol2',     18, 4),
            ('sc_tmp','lpb','kurs',          18, 6),
            ('sc_tmp','lpb','dpp',           18, 4),
            ('sc_tmp','lpb','jumlahpajak',   18, 4),
            ('sc_tmp','lpb','total',         18, 4),
            ('sc_trx','lpb','jthtempo',      18, 4),
            ('sc_trx','lpb','biayavol',      18, 4),
            ('sc_trx','lpb','biayavol2',     18, 4),
            ('sc_trx','lpb','kurs',          18, 6),
            ('sc_trx','lpb','dpp',           18, 4),
            ('sc_trx','lpb','jumlahpajak',   18, 4),
            ('sc_trx','lpb','total',         18, 4),

            ('sc_tmp','lpb_dtl','qty',           18, 4),
            ('sc_tmp','lpb_dtl','qtybonus',      18, 4),
            ('sc_tmp','lpb_dtl','qtyretur',      18, 4),
            ('sc_tmp','lpb_dtl','harga',         18, 4),
            ('sc_tmp','lpb_dtl','multidisc',     18, 4),
            ('sc_tmp','lpb_dtl','volitem',       18, 4),
            ('sc_tmp','lpb_dtl','biaya',         18, 4),
            ('sc_tmp','lpb_dtl','biaya2',        18, 4),
            ('sc_tmp','lpb_dtl','nilai',         18, 4),
            ('sc_tmp','lpb_dtl','nilaikonversi', 18, 4),
            ('sc_tmp','lpb_dtl','nilaipajak',    18, 4),
            ('sc_tmp','lpb_dtl','totaldiscount', 18, 4),
            ('sc_tmp','lpb_dtl','kurs',          18, 6),

            ('sc_trx','lpb_dtl','qty',           18, 4),
            ('sc_trx','lpb_dtl','qtybonus',      18, 4),
            ('sc_trx','lpb_dtl','qtyretur',      18, 4),
            ('sc_trx','lpb_dtl','harga',         18, 4),
            ('sc_trx','lpb_dtl','multidisc',     18, 4),
            ('sc_trx','lpb_dtl','volitem',       18, 4),
            ('sc_trx','lpb_dtl','biaya',         18, 4),
            ('sc_trx','lpb_dtl','biaya2',        18, 4),
            ('sc_trx','lpb_dtl','nilai',         18, 4),
            ('sc_trx','lpb_dtl','nilaikonversi', 18, 4),
            ('sc_trx','lpb_dtl','nilaipajak',    18, 4),
            ('sc_trx','lpb_dtl','totaldiscount', 18, 4),
            ('sc_trx','lpb_dtl','kurs',          18, 6)
        ) AS x(schema_name, table_name, column_name, exp_precision, exp_scale)
    LOOP
        IF EXISTS (
            SELECT 1 FROM information_schema.columns c
            WHERE c.table_schema = r.schema_name
              AND c.table_name   = r.table_name
              AND c.column_name  = r.column_name
              AND (
                  c.data_type <> 'numeric'
                  OR c.numeric_precision <> r.exp_precision
                  OR c.numeric_scale     <> r.exp_scale
              )
        ) THEN
            RAISE EXCEPTION
                'Validasi gagal: %.%.% bukan NUMERIC(%,%)',
                r.schema_name, r.table_name, r.column_name,
                r.exp_precision, r.exp_scale;
        END IF;
    END LOOP;

    RAISE NOTICE 'Validasi tipe kolom: LULUS';
END;
$$;

-------------------------------------------------------------------------------
-- 5. VALIDASI KOLOM BARU (paymenttype, polokal) DI 4 TABEL
-------------------------------------------------------------------------------
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT * FROM (VALUES
            ('sc_tmp','lpb'),
            ('sc_trx','lpb'),
            ('sc_tmp','lpb_dtl'),
            ('sc_trx','lpb_dtl')
        ) AS x(schema_name, table_name)
    LOOP
        IF NOT EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = r.schema_name
              AND table_name   = r.table_name
              AND column_name  = 'paymenttype'
              AND data_type    = 'character'
              AND character_maximum_length = 20
        ) THEN
            RAISE EXCEPTION
                'Validasi gagal: %.%.paymenttype bukan CHAR(20)',
                r.schema_name, r.table_name;
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_schema = r.schema_name
              AND table_name   = r.table_name
              AND column_name  = 'polokal'
              AND data_type    = 'character'
              AND character_maximum_length = 20
        ) THEN
            RAISE EXCEPTION
                'Validasi gagal: %.%.polokal bukan CHAR(20)',
                r.schema_name, r.table_name;
        END IF;
    END LOOP;

    RAISE NOTICE 'Validasi kolom paymenttype/polokal: LULUS';
END;
$$;

-------------------------------------------------------------------------------
-- 6. RESULT
-------------------------------------------------------------------------------
DO $$
BEGIN
    RAISE NOTICE '=============================================================';
    RAISE NOTICE 'PATCH lpb.sql BERHASIL';
    RAISE NOTICE 'qty/harga/nilai/dpp/pajak/total/biaya/diskon : 4 dec';
    RAISE NOTICE 'kurs                                          : 6 dec';
    RAISE NOTICE 'jthtempo                                      : 4 dec';
    RAISE NOTICE 'paymenttype, polokal CHAR(20)                 : ditambahkan';
    RAISE NOTICE '  -> sc_tmp.lpb, sc_trx.lpb';
    RAISE NOTICE '  -> sc_tmp.lpb_dtl, sc_trx.lpb_dtl';
    RAISE NOTICE '=============================================================';
END;
$$;

COMMIT;