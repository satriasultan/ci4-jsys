DO $$
DECLARE
    r RECORD;
    v_type TEXT;
    v_count INTEGER := 0;
    v_view_defs TEXT[] := ARRAY[]::TEXT[];
    v_view_names TEXT[] := ARRAY[]::TEXT[];
    v_def TEXT;
BEGIN
    -- ============================================================
    -- FASE A: Simpan definisi semua view yang bergantung pada kolom target
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
          AND tnsp.nspname = 'sc_trx'
          AND tbl.relname IN (
              'transaction_dt','transaction_hd','stkblc','stkblc_avgcost',
              'assetblc','jurnal_hd','jurnal_dt'
          )
          AND a.attname IN (
              'qty','harga','bruto','discount','nilai','dpp','pajak','total',
              'debet','kredit','nilai_trans',
              'total_qty','total_qty_in','total_qty_out','total_bruto',
              'total_discount','total_nilai','total_dpp','total_pajak',
              'total_total','total_debet','total_kredit','balance',
              'qty_in','qty_out','unitcost','totalcost',
              'pricelst_in','pricelst_out','pricelst_sld',
              'tax','disc','biaya',
              'total_value','avg_cost','value_in','value_out'
          )
          AND c.relkind = 'v'
    LOOP
        v_def := pg_get_viewdef(format('%I.%I', r.view_schema, r.view_name)::regclass, true);
        v_view_defs := array_append(v_view_defs, v_def);
        v_view_names := array_append(v_view_names, format('%I.%I', r.view_schema, r.view_name));
        RAISE NOTICE 'Akan DROP view sementara: %', format('%I.%I', r.view_schema, r.view_name);
        EXECUTE format('DROP VIEW IF EXISTS %I.%I', r.view_schema, r.view_name);
    END LOOP;

    -- ============================================================
    -- FASE B: Ubah tipe kolom (kode asli Anda)
    -- ============================================================
    FOR r IN
        SELECT *
        FROM (VALUES
            ('transaction_dt', 'qty'),
            ('transaction_dt', 'harga'),
            ('transaction_dt', 'bruto'),
            ('transaction_dt', 'discount'),
            ('transaction_dt', 'nilai'),
            ('transaction_dt', 'dpp'),
            ('transaction_dt', 'pajak'),
            ('transaction_dt', 'total'),
            ('transaction_dt', 'debet'),
            ('transaction_dt', 'kredit'),
            ('transaction_dt', 'nilai_trans'),

            ('transaction_hd', 'total_qty'),
            ('transaction_hd', 'total_qty_in'),
            ('transaction_hd', 'total_qty_out'),
            ('transaction_hd', 'total_bruto'),
            ('transaction_hd', 'total_discount'),
            ('transaction_hd', 'total_nilai'),
            ('transaction_hd', 'total_dpp'),
            ('transaction_hd', 'total_pajak'),
            ('transaction_hd', 'total_total'),
            ('transaction_hd', 'total_debet'),
            ('transaction_hd', 'total_kredit'),
            ('transaction_hd', 'balance'),

            ('stkblc', 'qty_in'),
            ('stkblc', 'qty_out'),
            ('stkblc', 'unitcost'),
            ('stkblc', 'totalcost'),
            ('stkblc', 'pricelst_in'),
            ('stkblc', 'pricelst_out'),
            ('stkblc', 'pricelst_sld'),
            ('stkblc', 'tax'),
            ('stkblc', 'disc'),
            ('stkblc', 'biaya'),

            ('stkblc_avgcost', 'qty'),
            ('stkblc_avgcost', 'total_value'),
            ('stkblc_avgcost', 'avg_cost'),

            ('assetblc', 'qty'),
            ('assetblc', 'value_in'),
            ('assetblc', 'value_out'),

            ('jurnal_hd', 'total_debet'),
            ('jurnal_hd', 'total_kredit'),
            ('jurnal_hd', 'balance'),

            ('jurnal_dt', 'debet'),
            ('jurnal_dt', 'kredit')
        ) AS x(table_name, column_name)
    LOOP
        SELECT c.data_type
        INTO v_type
        FROM information_schema.columns c
        WHERE c.table_schema = 'sc_trx'
          AND c.table_name = r.table_name
          AND c.column_name = r.column_name;

        IF v_type IS NOT NULL THEN
            IF v_type <> 'numeric' THEN
                RAISE EXCEPTION
                    'Kolom sc_trx.%.% ditemukan tetapi tipe datanya %, bukan numeric.',
                    r.table_name, r.column_name, v_type;
            END IF;

            EXECUTE format(
                'ALTER TABLE sc_trx.%I ALTER COLUMN %I TYPE NUMERIC(18,4) USING %I::NUMERIC(18,4)',
                r.table_name,
                r.column_name,
                r.column_name
            );

            v_count := v_count + 1;
        END IF;
    END LOOP;

    -- Legacy table opsional
    IF to_regclass('sc_trx.jurnal_hd_penggunaan') IS NOT NULL THEN
        SELECT c.data_type
        INTO v_type
        FROM information_schema.columns c
        WHERE c.table_schema = 'sc_trx'
          AND c.table_name = 'jurnal_hd_penggunaan'
          AND c.column_name = 'nilai';

        IF v_type = 'numeric' THEN
            ALTER TABLE sc_trx.jurnal_hd_penggunaan
                ALTER COLUMN nilai TYPE NUMERIC(18,4)
                USING nilai::NUMERIC(18,4);
            v_count := v_count + 1;
        ELSIF v_type IS NOT NULL THEN
            RAISE EXCEPTION
                'Kolom sc_trx.jurnal_hd_penggunaan.nilai bukan numeric.';
        END IF;
    END IF;

    -- ============================================================
    -- FASE C: Pulihkan semua view yang sebelumnya di-DROP
    -- ============================================================
    FOR i IN 1 .. array_length(v_view_defs, 1) LOOP
        EXECUTE format('CREATE OR REPLACE VIEW %s AS %s', v_view_names[i], v_view_defs[i]);
        RAISE NOTICE 'View dipulihkan: %', v_view_names[i];
    END LOOP;

    RAISE NOTICE 'Kolom numeric yang diproses: %, view dipulihkan: %',
        v_count, COALESCE(array_length(v_view_defs, 1), 0);
END;
$$;