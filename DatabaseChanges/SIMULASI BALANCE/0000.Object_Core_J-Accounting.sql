/* ============================================================================
   JSYS CORE OBJECT REGISTRY V3 + CLEAN TEST TRANSACTION DATA
   ============================================================================

   TUJUAN
   ------
   1. Membuat registry object JSYS Core:
        - TABLE
        - FUNCTION
        - PROCEDURE
        - TRIGGER
   2. Mencatat lokasi object secara jelas:
        - schema
        - table yang ditempati trigger
        - table yang menjadi lokasi/relasi function
   3. Mencatat object_type secara eksplisit.
   4. Mencatat status object:
        TABLE     : ACTIVE / NOT_OBSERVED
        FUNCTION  : ACTIVE / NOT_OBSERVED / UNKNOWN
        PROCEDURE : ACTIVE / NOT_OBSERVED / UNKNOWN
        TRIGGER   : ENABLED_AND_FUNCTION_OBSERVED /
                    ENABLED_BUT_FUNCTION_NOT_OBSERVED / DISABLED
   5. Mencatat penggunaan transaksi:
        PEMBELIAN / PENJUALAN / RETUR / PERSEDIAAN /
        PRODUKSI / JASA / ASSET / ACCOUNTING / TAX /
        FINANCE / LPB / ALL TRANSACTION / CONFIG / REVIEW
   6. Tidak melakukan DROP object.
   7. Menyediakan query clean DATA TEST transaksi.

   CATATAN RUNTIME
   ---------------
   Function/Procedure memakai pg_stat_user_functions.
   Table memakai pg_stat_user_tables.
   Trigger memakai status enable + runtime_calls function trigger.

   Semua counter runtime mengikuti pg_stat dan berlaku sejak statistics reset
   PostgreSQL terakhir. Script TIDAK me-reset statistics.

   CATATAN KLASIFIKASI
   -------------------
   Kolom transaction_usage dan keterangan adalah klasifikasi JSYS Core.
   Object yang tidak dapat diklasifikasikan dari mapping core diberi REVIEW.
   Runtime ACTIVE berarti object benar-benar teramati dipakai sejak stats reset,
   bukan bukti historis absolut.
   ============================================================================ */

BEGIN;

SET LOCAL lock_timeout = '10s';
SET LOCAL statement_timeout = '5min';

/* ============================================================================
   01. REGISTRY TABLE
============================================================================ */
CREATE SCHEMA IF NOT EXISTS sc_mst;

CREATE TABLE IF NOT EXISTS sc_mst.jsys_core_object_registry
(
    id                      BIGSERIAL PRIMARY KEY,

    object_key              TEXT NOT NULL,
    object_type             VARCHAR(20) NOT NULL,

    schema_name             VARCHAR(63) NOT NULL,
    table_name              VARCHAR(63),
    table_location          TEXT NOT NULL DEFAULT '',

    object_name             VARCHAR(255) NOT NULL,
    object_identity         TEXT NOT NULL DEFAULT '',

    jsys_scope              VARCHAR(20) NOT NULL DEFAULT 'REVIEW',
    condition_status        VARCHAR(40) NOT NULL DEFAULT 'UNKNOWN',
    status                  VARCHAR(100) NOT NULL DEFAULT 'UNKNOWN',
    enabled_status          VARCHAR(40) NOT NULL DEFAULT '',

    transaction_usage       VARCHAR(255) NOT NULL DEFAULT 'REVIEW',
    usage_basis             VARCHAR(100) NOT NULL DEFAULT '',

    runtime_calls           BIGINT NOT NULL DEFAULT 0,
    runtime_reads           BIGINT NOT NULL DEFAULT 0,
    runtime_writes          BIGINT NOT NULL DEFAULT 0,
    stats_reset_at          TIMESTAMPTZ,
    track_functions         VARCHAR(20) NOT NULL DEFAULT '',

    function_language       VARCHAR(50),
    trigger_timing_event    TEXT,

    keterangan              TEXT NOT NULL DEFAULT '',
    definition              TEXT NOT NULL DEFAULT '',

    last_scanned_at         TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_jsys_core_object_registry_key
        UNIQUE (object_key)
);

/* Upgrade aman bila registry versi sebelumnya sudah ada. */
ALTER TABLE sc_mst.jsys_core_object_registry
    ALTER COLUMN condition_status TYPE VARCHAR(40);

ALTER TABLE sc_mst.jsys_core_object_registry
    ALTER COLUMN status TYPE VARCHAR(100);

ALTER TABLE sc_mst.jsys_core_object_registry
    ALTER COLUMN enabled_status TYPE VARCHAR(40);

ALTER TABLE sc_mst.jsys_core_object_registry
    ADD COLUMN IF NOT EXISTS transaction_usage VARCHAR(255) NOT NULL DEFAULT 'REVIEW';

ALTER TABLE sc_mst.jsys_core_object_registry
    ADD COLUMN IF NOT EXISTS usage_basis VARCHAR(100) NOT NULL DEFAULT '';

ALTER TABLE sc_mst.jsys_core_object_registry
    ADD COLUMN IF NOT EXISTS runtime_reads BIGINT NOT NULL DEFAULT 0;

ALTER TABLE sc_mst.jsys_core_object_registry
    ADD COLUMN IF NOT EXISTS runtime_writes BIGINT NOT NULL DEFAULT 0;

CREATE INDEX IF NOT EXISTS ix_jsys_core_object_registry_scope
    ON sc_mst.jsys_core_object_registry (jsys_scope, object_type);

CREATE INDEX IF NOT EXISTS ix_jsys_core_object_registry_status
    ON sc_mst.jsys_core_object_registry (condition_status, status);

CREATE INDEX IF NOT EXISTS ix_jsys_core_object_registry_table
    ON sc_mst.jsys_core_object_registry (schema_name, table_name);

CREATE INDEX IF NOT EXISTS ix_jsys_core_object_registry_usage
    ON sc_mst.jsys_core_object_registry (transaction_usage);

COMMENT ON TABLE sc_mst.jsys_core_object_registry IS
'Inventory JSYS Core TABLE/FUNCTION/PROCEDURE/TRIGGER dengan lokasi, status runtime, dan klasifikasi transaksi.';

/* ============================================================================
   02. REFRESH REGISTRY
============================================================================ */
TRUNCATE TABLE sc_mst.jsys_core_object_registry RESTART IDENTITY;

WITH
/* --------------------------------------------------------------------------
   CORE TABLE MAPPING
   Table yang memang menjadi bagian langsung dari flow JSYS Core.
--------------------------------------------------------------------------- */
known_tables AS
(
    SELECT *
    FROM
    (
        VALUES
            ('sc_mst','coa',                'CORE','ACCOUNTING / COA',
             'Chart of Accounts untuk seluruh jurnal accounting'),
            ('sc_mst','journal_type',       'CORE','ALL TRANSACTION / CONFIG',
             'Master routing tipe transaksi dan arah stock/accounting/asset'),
            ('sc_mst','journal_type_coa',   'CORE','ACCOUNTING / CONFIG',
             'Mapping role jurnal ke COA dan sumber nilai'),
            ('sc_mst','mbarang',             'CORE','PEMBELIAN / PENJUALAN / PERSEDIAAN / PRODUKSI / JASA',
             'Master barang, akun persediaan/HPP/jasa/waste dan tax default'),
            ('sc_mst','currency',            'CORE','PEMBELIAN / PENJUALAN / FINANCE',
             'Default currency dan akun AP/AR/pendapatan/pajak/finance'),
            ('sc_mst','grouptax',             'CORE','TAX',
             'Group tax'),
            ('sc_mst','tax_mst',              'CORE','TAX',
             'Master pajak'),
            ('sc_mst','tax_dtl',              'CORE','TAX / PEMBELIAN / PENJUALAN',
             'Detail tax dan COA pajak masukan/keluaran'),
            ('sc_mst','konfigurasi_umum',    'CORE','ALL TRANSACTION / CONFIG',
             'Fallback konfigurasi COA dan parameter transaksi umum'),

            ('sc_trx','transaction_hd',       'CORE','ALL TRANSACTION',
             'Header transaksi sumber JSYS'),
            ('sc_trx','transaction_dt',       'CORE','ALL TRANSACTION',
             'Detail transaksi sumber dan source of truth routing'),
            ('sc_trx','stkblc',               'CORE','PEMBELIAN / GRN / PENJUALAN / RETUR / PERSEDIAAN / PRODUKSI / TRANSFER',
             'Ledger mutasi stock'),
            ('sc_trx','stkblc_avgcost',       'CORE','PERSEDIAAN / HPP / PRODUKSI',
             'Average cost dan valuasi stock'),
            ('sc_trx','assetblc',              'CORE','ASSET',
             'Ledger pergerakan fixed asset'),
            ('sc_trx','jurnal_hd',             'CORE','ACCOUNTING / PEMBELIAN / PENJUALAN / TAX / FINANCE / ASSET',
             'Header jurnal accounting'),
            ('sc_trx','jurnal_dt',             'CORE','ACCOUNTING / PEMBELIAN / PENJUALAN / TAX / FINANCE / ASSET',
             'Detail jurnal accounting'),

            ('sc_tmp','lpb_dtl',               'CORE','LPB / PEMBELIAN / GRN',
             'Staging detail LPB yang difinalisasi menjadi stock/accounting')
    ) AS x(schema_name, table_name, known_scope, transaction_usage, known_note)
),

/* --------------------------------------------------------------------------
   CORE FUNCTION MAPPING
--------------------------------------------------------------------------- */
known_functions AS
(
    SELECT *
    FROM
    (
        VALUES
            ('fn_stock_uniqueid',                  'CORE','ALL TRANSACTION / PERSEDIAAN',
             'Membuat unique identity transaksi stock'),
            ('fn_stock_key',                       'CORE','PERSEDIAAN / STOCK',
             'Membuat stock key barang/lokasi/batch/lot'),
            ('fn_prepare_transaction_dt',         'CORE','ALL TRANSACTION',
             'Prepare, normalisasi, routing dan default nilai transaction_dt'),
            ('fn_validate_transaction_type',      'CORE','ALL TRANSACTION',
             'Validasi journal_type dan routing transaksi'),
            ('fn_get_transaction_tax_accounts',   'CORE','TAX / PEMBELIAN / PENJUALAN',
             'Resolve akun pajak dari tax master/detail'),
            ('fn_resolve_transaction_coa',        'CORE','ALL TRANSACTION / ACCOUNTING',
             'Resolve primary COA transaction_dt'),
            ('fn_post_stkblc_from_transaction',   'CORE','PEMBELIAN / GRN / PENJUALAN / RETUR / PERSEDIAAN / PRODUKSI',
             'Membentuk stock ledger dari transaction_dt'),
            ('fn_delete_stkblc_from_transaction', 'CORE','PEMBELIAN / GRN / PENJUALAN / RETUR / PERSEDIAAN / PRODUKSI',
             'Menghapus stock ledger berdasarkan source transaksi'),
            ('fn_transaction_stock',               'CORE','PEMBELIAN / GRN / PENJUALAN / RETUR / PERSEDIAAN / PRODUKSI',
             'Trigger function stock transaction'),
            ('fn_recalculate_avgcost',             'CORE','PERSEDIAAN / HPP / PRODUKSI',
             'Recalculate average cost stock'),
            ('fn_recalculate_all_avgcost',         'CORE','PERSEDIAAN / HPP / PRODUKSI',
             'Recalculate seluruh average cost'),
            ('fn_stkblc_avgcost_trigger',          'CORE','PERSEDIAAN / HPP / PRODUKSI',
             'Trigger function perubahan stkblc ke avgcost'),
            ('fn_asset_uniqueid',                  'CORE','ASSET',
             'Membuat unique identity transaksi asset'),
            ('fn_asset_key',                       'CORE','ASSET',
             'Membuat asset key'),
            ('fn_transaction_asset',               'CORE','ASSET',
             'Posting/deleting asset movement'),
            ('fn_journal_uniqueid',                'CORE','ACCOUNTING',
             'Membuat unique identity jurnal'),
            ('fn_validate_jurnal_coa',             'CORE','ACCOUNTING',
             'Validasi COA jurnal detail'),
            ('fn_validate_jurnal_detail_status',  'CORE','ACCOUNTING',
             'Melindungi jurnal detail sesuai status header'),
            ('fn_sync_jurnal_hd',                  'CORE','ACCOUNTING',
             'Sinkronisasi jurnal detail ke jurnal header'),
            ('fn_resolve_accounting_coa',          'CORE','ACCOUNTING / PEMBELIAN / PENJUALAN / TAX / FINANCE / PRODUKSI',
             'Resolver COA accounting final berdasarkan role dan master'),
            ('fn_post_accounting_transaction',    'CORE','ACCOUNTING / PEMBELIAN / PENJUALAN / TAX / FINANCE / PRODUKSI',
             'Posting transaction_dt menjadi jurnal accounting'),
            ('fn_reverse_accounting_transaction', 'CORE','ACCOUNTING / PEMBELIAN / PENJUALAN / TAX / FINANCE / PRODUKSI',
             'Reverse accounting transaction/journal'),
            ('fn_recalculate_transaction_hd',     'CORE','ALL TRANSACTION',
             'Recalculate summary transaction_hd'),
            ('fn_transaction_accounting',          'CORE','ACCOUNTING / ALL TRANSACTION',
             'Trigger function accounting transaction final'),

            ('fn_apply_transaction_coa',           'REVIEW','ACCOUNTING / ALL TRANSACTION',
             'Legacy candidate - review runtime usage'),
            ('fn_resolve_journal_type_coa',        'REVIEW','ACCOUNTING / CONFIG',
             'Legacy/old resolver - review runtime usage'),
            ('fn_resolve_role_coa',                'REVIEW','ACCOUNTING / CONFIG',
             'Legacy role resolver - review runtime usage'),
            ('fn_validate_posting_configuration',  'REVIEW','ACCOUNTING / CONFIG',
             'Legacy validation helper - review runtime usage'),
            ('fn_get_tax_accounts',                'REVIEW','TAX',
             'Legacy tax resolver - review runtime usage'),
            ('fn_post_stock_transaction',          'REVIEW','PERSEDIAAN / PEMBELIAN / PENJUALAN / PRODUKSI',
             'Legacy stock posting'),
            ('fn_reverse_stock_transaction',       'REVIEW','PERSEDIAAN / RETUR / PEMBELIAN / PENJUALAN',
             'Legacy stock reverse'),
            ('fn_post_asset_transaction',           'REVIEW','ASSET',
             'Legacy asset posting'),
            ('fn_reverse_asset_transaction',        'REVIEW','ASSET',
             'Legacy asset reverse'),
            ('fn_post_assetblc_from_transaction',   'REVIEW','ASSET',
             'Legacy asset ledger posting'),
            ('fn_delete_assetblc_from_transaction','REVIEW','ASSET',
             'Legacy asset ledger delete'),
            ('fn_recalculate_jurnal_hd',            'REVIEW','ACCOUNTING',
             'Legacy journal header recalculation'),
            ('fn_validate_journal_balance',         'REVIEW','ACCOUNTING',
             'Legacy journal balance validation'),
            ('fn_post_journal',                    'REVIEW','ACCOUNTING',
             'Legacy direct journal posting'),
            ('fn_reverse_journal',                 'REVIEW','ACCOUNTING',
             'Legacy direct journal reverse'),
            ('fn_cancel_journal',                  'REVIEW','ACCOUNTING',
             'Legacy direct journal cancellation'),
            ('fn_transaction_dt_process',           'REVIEW','ALL TRANSACTION',
             'Legacy transaction processor'),
            ('fn_validate_source_uniqueid',         'REVIEW','ALL TRANSACTION',
             'Legacy source identity validation'),
            ('fn_validate_transaction_type_stage19','REVIEW','ALL TRANSACTION',
             'Legacy validation stage'),
            ('fn_validate_transaction_coa_stage19','REVIEW','ACCOUNTING',
             'Legacy validation stage COA'),
            ('fn_transaction_to_stkblc',            'REVIEW','PERSEDIAAN / STOCK',
             'Legacy stock bridge'),
            ('fn_transaction_delete_stkblc',        'REVIEW','PERSEDIAAN / STOCK',
             'Legacy stock delete bridge')
    ) AS x(function_name, known_scope, transaction_usage, known_note)
),

/* --------------------------------------------------------------------------
   CORE TRIGGER MAPPING
--------------------------------------------------------------------------- */
known_triggers AS
(
    SELECT *
    FROM
    (
        VALUES
            ('trg_01_transaction_prepare',        'CORE','ALL TRANSACTION',
             'Prepare transaction_dt'),
            ('trg_02_transaction_type',           'CORE','ALL TRANSACTION',
             'Validate journal_type/type transaksi'),
            ('trg_transaction_stock',             'CORE','PEMBELIAN / GRN / PENJUALAN / RETUR / PERSEDIAAN / PRODUKSI',
             'Build/remove stock ledger'),
            ('trg_stkblc_avgcost',                'CORE','PERSEDIAAN / HPP / PRODUKSI',
             'Recalculate average cost'),
            ('trg_transaction_asset',             'CORE','ASSET',
             'Build/remove asset ledger'),
            ('trg_validate_jurnal_coa',           'CORE','ACCOUNTING',
             'Validate journal detail COA'),
            ('trg_validate_jurnal_detail_status', 'CORE','ACCOUNTING',
             'Protect posted/reversed journal detail'),
            ('trg_sync_jurnal_hd',               'CORE','ACCOUNTING',
             'Sync journal header totals'),
            ('trg_zz_transaction_accounting',    'CORE','ACCOUNTING / ALL TRANSACTION',
             'Final accounting posting/reversal trigger'),
            ('tr_lpb_finalize',                   'CORE','LPB / PEMBELIAN / GRN',
             'LPB finalize staging trigger'),

            ('trg_01_transaction_coa',            'REVIEW','ACCOUNTING / ALL TRANSACTION',
             'Legacy transaction COA trigger'),
            ('trg_validate_transaction_type',     'REVIEW','ALL TRANSACTION',
             'Legacy transaction type trigger'),
            ('trg_01_transaction_source',         'REVIEW','ALL TRANSACTION',
             'Legacy transaction source trigger'),
            ('trg_03_transaction_coa',            'REVIEW','ACCOUNTING / ALL TRANSACTION',
             'Legacy transaction COA trigger'),
            ('trg_transaction_accounting',        'REVIEW','ACCOUNTING / ALL TRANSACTION',
             'Legacy accounting trigger candidate')
    ) AS x(trigger_name, known_scope, transaction_usage, known_note)
),

/* --------------------------------------------------------------------------
   TABLE OBJECTS
--------------------------------------------------------------------------- */
table_rows AS
(
    SELECT
        'TABLE'::TEXT AS object_type,
        n.nspname::VARCHAR(63) AS schema_name,
        c.relname::VARCHAR(63) AS table_name,
        format('%I.%I', n.nspname, c.relname)::TEXT AS table_location,
        c.relname::VARCHAR(255) AS object_name,
        format('%I.%I', n.nspname, c.relname)::TEXT AS object_identity,
        format('%I.%I', n.nspname, c.relname)::TEXT AS object_key,

        COALESCE(kt.known_scope, 'REVIEW')::VARCHAR(20) AS jsys_scope,

        CASE
            WHEN COALESCE(st.seq_scan,0) > 0
              OR COALESCE(st.idx_scan,0) > 0
              OR COALESCE(st.n_tup_ins,0) > 0
              OR COALESCE(st.n_tup_upd,0) > 0
              OR COALESCE(st.n_tup_del,0) > 0
                THEN 'ACTIVE'
            ELSE 'NOT_OBSERVED'
        END::VARCHAR(40) AS condition_status,

        CASE
            WHEN COALESCE(st.seq_scan,0) > 0
              OR COALESCE(st.idx_scan,0) > 0
              OR COALESCE(st.n_tup_ins,0) > 0
              OR COALESCE(st.n_tup_upd,0) > 0
              OR COALESCE(st.n_tup_del,0) > 0
                THEN 'RUNTIME_ACTIVITY_OBSERVED'
            ELSE 'NO_RUNTIME_ACTIVITY_OBSERVED_SINCE_STATS_RESET'
        END::VARCHAR(100) AS status,

        ''::VARCHAR(40) AS enabled_status,
        COALESCE(kt.transaction_usage, 'REVIEW')::VARCHAR(255) AS transaction_usage,
        CASE
            WHEN kt.table_name IS NOT NULL THEN 'KNOWN_CORE_MAPPING'
            ELSE 'CATALOG_DISCOVERY'
        END::VARCHAR(100) AS usage_basis,

        0::BIGINT AS runtime_calls,
        (
            COALESCE(st.seq_scan,0) + COALESCE(st.idx_scan,0)
        )::BIGINT AS runtime_reads,
        (
            COALESCE(st.n_tup_ins,0) + COALESCE(st.n_tup_upd,0) + COALESCE(st.n_tup_del,0)
        )::BIGINT AS runtime_writes,
        db.stats_reset,
        current_setting('track_functions', true)::VARCHAR(20) AS track_functions,

        NULL::VARCHAR(50) AS function_language,
        NULL::TEXT AS trigger_timing_event,

        COALESCE(
            kt.known_note,
            NULLIF(obj_description(c.oid, 'pg_class'), ''),
            'Table ditemukan dari PostgreSQL catalog; klasifikasi transaksi perlu review.'
        )::TEXT AS keterangan,
        format('TABLE %I.%I', n.nspname, c.relname)::TEXT AS definition
    FROM pg_class c
    JOIN pg_namespace n
      ON n.oid = c.relnamespace
    LEFT JOIN pg_stat_user_tables st
      ON st.relid = c.oid
    LEFT JOIN pg_stat_database db
      ON db.datname = current_database()
    LEFT JOIN known_tables kt
      ON kt.schema_name = n.nspname
     AND kt.table_name = c.relname
    WHERE n.nspname IN ('sc_mst','sc_trx','sc_tmp')
      AND c.relkind IN ('r','p')
),

/* --------------------------------------------------------------------------
   TRIGGER OBJECTS
--------------------------------------------------------------------------- */
trigger_rows AS
(
    SELECT
        'TRIGGER'::TEXT AS object_type,
        n.nspname::VARCHAR(63) AS schema_name,
        c.relname::VARCHAR(63) AS table_name,
        format('%I.%I', n.nspname, c.relname)::TEXT AS table_location,
        t.tgname::VARCHAR(255) AS object_name,
        format('%I.%I.%I', n.nspname, c.relname, t.tgname)::TEXT AS object_identity,
        format('%I.%I.%I', n.nspname, c.relname, t.tgname)::TEXT AS object_key,

        COALESCE(
            ktg.known_scope,
            CASE
                WHEN EXISTS
                (
                    SELECT 1
                    FROM known_tables kt
                    WHERE kt.schema_name = n.nspname
                      AND kt.table_name = c.relname
                      AND kt.known_scope = 'CORE'
                ) THEN 'CORE'
                ELSE 'REVIEW'
            END
        )::VARCHAR(20) AS jsys_scope,

        CASE
            WHEN t.tgenabled = 'D' THEN 'INACTIVE'
            WHEN current_setting('track_functions', true) = 'none' THEN 'UNKNOWN_RUNTIME'
            WHEN COALESCE(sf.calls,0) > 0 THEN 'ACTIVE'
            ELSE 'NOT_OBSERVED'
        END::VARCHAR(40) AS condition_status,

        CASE
            WHEN t.tgenabled = 'D' THEN 'DISABLED'
            WHEN current_setting('track_functions', true) = 'none' THEN 'ENABLED_RUNTIME_STATS_UNAVAILABLE'
            WHEN COALESCE(sf.calls,0) > 0 THEN 'ENABLED_AND_FUNCTION_OBSERVED'
            ELSE 'ENABLED_BUT_FUNCTION_NOT_OBSERVED'
        END::VARCHAR(100) AS status,

        CASE
            WHEN t.tgenabled = 'D' THEN 'DISABLED'
            WHEN t.tgenabled = 'O' THEN 'ENABLED'
            WHEN t.tgenabled = 'A' THEN 'ENABLED_ALWAYS'
            WHEN t.tgenabled = 'R' THEN 'ENABLED_REPLICA'
            ELSE 'UNKNOWN'
        END::VARCHAR(40) AS enabled_status,

        COALESCE(
            ktg.transaction_usage,
            CASE
                WHEN c.relname IN ('transaction_dt','transaction_hd') THEN 'ALL TRANSACTION'
                WHEN c.relname = 'stkblc' THEN 'PERSEDIAAN / PEMBELIAN / PENJUALAN / RETUR / PRODUKSI'
                WHEN c.relname = 'stkblc_avgcost' THEN 'PERSEDIAAN / HPP / PRODUKSI'
                WHEN c.relname = 'assetblc' THEN 'ASSET'
                WHEN c.relname IN ('jurnal_hd','jurnal_dt') THEN 'ACCOUNTING'
                WHEN c.relname = 'lpb_dtl' THEN 'LPB / PEMBELIAN / GRN'
                ELSE 'REVIEW'
            END
        )::VARCHAR(255) AS transaction_usage,

        CASE
            WHEN ktg.trigger_name IS NOT NULL THEN 'KNOWN_TRIGGER_MAPPING'
            WHEN EXISTS
            (
                SELECT 1
                FROM known_tables kt
                WHERE kt.schema_name = n.nspname
                  AND kt.table_name = c.relname
                  AND kt.known_scope = 'CORE'
            ) THEN 'CORE_TABLE_TRIGGER'
            ELSE 'CATALOG_DISCOVERY'
        END::VARCHAR(100) AS usage_basis,

        COALESCE(sf.calls,0)::BIGINT AS runtime_calls,
        0::BIGINT AS runtime_reads,
        0::BIGINT AS runtime_writes,
        db.stats_reset,
        current_setting('track_functions', true)::VARCHAR(20) AS track_functions,
        l.lanname::VARCHAR(50) AS function_language,
        pg_get_triggerdef(t.oid, true)::TEXT AS trigger_timing_event,

        COALESCE(
            ktg.known_note,
            NULLIF(obj_description(t.oid, 'pg_trigger'), ''),
            format('Trigger pada %I.%I; function trigger = %s. Review bila belum diketahui.',
                   n.nspname, c.relname, p.oid::regprocedure::TEXT)
        )::TEXT AS keterangan,
        pg_get_triggerdef(t.oid, true)::TEXT AS definition

    FROM pg_trigger t
    JOIN pg_class c
      ON c.oid = t.tgrelid
    JOIN pg_namespace n
      ON n.oid = c.relnamespace
    JOIN pg_proc p
      ON p.oid = t.tgfoid
    JOIN pg_language l
      ON l.oid = p.prolang
    LEFT JOIN pg_stat_user_functions sf
      ON sf.funcid = p.oid
    LEFT JOIN pg_stat_database db
      ON db.datname = current_database()
    LEFT JOIN known_triggers ktg
      ON ktg.trigger_name = t.tgname
    WHERE n.nspname IN ('sc_mst','sc_trx','sc_tmp')
      AND NOT t.tgisinternal
),

/* --------------------------------------------------------------------------
   FUNCTION / PROCEDURE OBJECTS
--------------------------------------------------------------------------- */
function_rows AS
(
    SELECT
        CASE WHEN p.prokind = 'p' THEN 'PROCEDURE' ELSE 'FUNCTION' END::TEXT AS object_type,
        n.nspname::VARCHAR(63) AS schema_name,
        NULL::VARCHAR(63) AS table_name,

        COALESCE(
            (
                SELECT string_agg(
                    format('%I.%I', tn.nspname, tc.relname),
                    ', ' ORDER BY tn.nspname, tc.relname
                )
                FROM pg_trigger tr
                JOIN pg_class tc
                  ON tc.oid = tr.tgrelid
                JOIN pg_namespace tn
                  ON tn.oid = tc.relnamespace
                WHERE tr.tgfoid = p.oid
                  AND NOT tr.tgisinternal
            ),
            'DIRECT / API / SQL'
        )::TEXT AS table_location,

        p.proname::VARCHAR(255) AS object_name,
        p.oid::regprocedure::TEXT AS object_identity,
        format('%I.%I(%s)', n.nspname, p.proname,
               pg_get_function_identity_arguments(p.oid))::TEXT AS object_key,

        COALESCE(
            kf.known_scope,
            CASE
                WHEN EXISTS
                (
                    SELECT 1
                    FROM pg_trigger tr
                    JOIN pg_class tc
                      ON tc.oid = tr.tgrelid
                    JOIN pg_namespace tn
                      ON tn.oid = tc.relnamespace
                    WHERE tr.tgfoid = p.oid
                      AND NOT tr.tgisinternal
                      AND EXISTS
                      (
                          SELECT 1
                          FROM known_tables kt
                          WHERE kt.schema_name = tn.nspname
                            AND kt.table_name = tc.relname
                            AND kt.known_scope = 'CORE'
                      )
                ) THEN 'CORE'
                ELSE 'REVIEW'
            END
        )::VARCHAR(20) AS jsys_scope,

        CASE
            WHEN current_setting('track_functions', true) = 'none' THEN 'UNKNOWN_RUNTIME'
            WHEN COALESCE(sf.calls,0) > 0 THEN 'ACTIVE'
            ELSE 'NOT_OBSERVED'
        END::VARCHAR(40) AS condition_status,

        CASE
            WHEN current_setting('track_functions', true) = 'none' THEN 'RUNTIME_STATS_UNAVAILABLE'
            WHEN COALESCE(sf.calls,0) > 0 THEN 'CALLED'
            ELSE 'NOT_OBSERVED_SINCE_STATS_RESET'
        END::VARCHAR(100) AS status,

        ''::VARCHAR(40) AS enabled_status,

        COALESCE(
            kf.transaction_usage,
            CASE
                WHEN p.proname ILIKE '%tax%' THEN 'TAX'
                WHEN p.proname ILIKE '%asset%' THEN 'ASSET'
                WHEN p.proname ILIKE '%stock%'
                  OR p.proname ILIKE '%stkblc%'
                  OR p.proname ILIKE '%avgcost%' THEN 'PERSEDIAAN / HPP / PRODUKSI'
                WHEN p.proname ILIKE '%jurnal%'
                  OR p.proname ILIKE '%journal%'
                  OR p.proname ILIKE '%accounting%'
                  OR p.proname ILIKE '%coa%' THEN 'ACCOUNTING'
                WHEN p.proname ILIKE '%lpb%' THEN 'LPB / PEMBELIAN / GRN'
                WHEN p.proname ILIKE '%transaction%' THEN 'ALL TRANSACTION'
                ELSE 'REVIEW'
            END
        )::VARCHAR(255) AS transaction_usage,

        CASE
            WHEN kf.function_name IS NOT NULL THEN 'KNOWN_FUNCTION_MAPPING'
            WHEN EXISTS
            (
                SELECT 1
                FROM pg_trigger tr
                JOIN pg_class tc
                  ON tc.oid = tr.tgrelid
                JOIN pg_namespace tn
                  ON tn.oid = tc.relnamespace
                WHERE tr.tgfoid = p.oid
                  AND NOT tr.tgisinternal
            ) THEN 'TRIGGER_FUNCTION_REFERENCE'
            ELSE 'CATALOG_DISCOVERY'
        END::VARCHAR(100) AS usage_basis,

        COALESCE(sf.calls,0)::BIGINT AS runtime_calls,
        0::BIGINT AS runtime_reads,
        0::BIGINT AS runtime_writes,
        db.stats_reset,
        current_setting('track_functions', true)::VARCHAR(20) AS track_functions,
        l.lanname::VARCHAR(50) AS function_language,
        NULL::TEXT AS trigger_timing_event,

        COALESCE(
            kf.known_note,
            NULLIF(obj_description(p.oid, 'pg_proc'), ''),
            'Function/Procedure ditemukan dari PostgreSQL catalog; klasifikasi transaksi perlu review.'
        )::TEXT AS keterangan,
        pg_get_functiondef(p.oid)::TEXT AS definition

    FROM pg_proc p
    JOIN pg_namespace n
      ON n.oid = p.pronamespace
    JOIN pg_language l
      ON l.oid = p.prolang
    LEFT JOIN pg_stat_user_functions sf
      ON sf.funcid = p.oid
    LEFT JOIN pg_stat_database db
      ON db.datname = current_database()
    LEFT JOIN known_functions kf
      ON kf.function_name = p.proname
    WHERE n.nspname IN ('sc_mst','sc_trx','sc_tmp')
      AND p.prokind IN ('f','p')
),

all_objects AS
(
    SELECT * FROM table_rows
    UNION ALL
    SELECT * FROM trigger_rows
    UNION ALL
    SELECT * FROM function_rows
)
INSERT INTO sc_mst.jsys_core_object_registry
(
    object_key,
    object_type,
    schema_name,
    table_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    enabled_status,
    transaction_usage,
    usage_basis,
    runtime_calls,
    runtime_reads,
    runtime_writes,
    stats_reset_at,
    track_functions,
    function_language,
    trigger_timing_event,
    keterangan,
    definition,
    last_scanned_at
)
SELECT
    object_key,
    object_type,
    schema_name,
    table_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    enabled_status,
    transaction_usage,
    usage_basis,
    runtime_calls,
    runtime_reads,
    runtime_writes,
    stats_reset,
    track_functions,
    function_language,
    trigger_timing_event,
    keterangan,
    definition,
    CURRENT_TIMESTAMP
FROM all_objects
ORDER BY schema_name, object_type, table_location, object_name, object_identity;

/* ============================================================================
   03. REPORT SEMUA OBJECT JSYS CORE / REVIEW
============================================================================ */
SELECT
    object_type,
    schema_name,
    table_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    enabled_status,
    transaction_usage,
    usage_basis,
    runtime_calls,
    runtime_reads,
    runtime_writes,
    stats_reset_at,
    function_language,
    trigger_timing_event,
    keterangan
FROM sc_mst.jsys_core_object_registry
ORDER BY
    CASE jsys_scope WHEN 'CORE' THEN 1 ELSE 2 END,
    schema_name,
    CASE object_type WHEN 'TABLE' THEN 1 WHEN 'TRIGGER' THEN 2 WHEN 'FUNCTION' THEN 3 WHEN 'PROCEDURE' THEN 4 ELSE 9 END,
    table_location,
    object_name,
    object_identity;

/* ============================================================================
   04. REPORT TABLE SAJA
============================================================================ */
SELECT
    schema_name,
    table_name,
    table_location,
    jsys_scope,
    condition_status,
    status,
    transaction_usage,
    usage_basis,
    runtime_reads,
    runtime_writes,
    keterangan
FROM sc_mst.jsys_core_object_registry
WHERE object_type = 'TABLE'
ORDER BY
    CASE jsys_scope WHEN 'CORE' THEN 1 ELSE 2 END,
    schema_name,
    table_name;

/* ============================================================================
   05. REPORT TRIGGER SAJA
============================================================================ */
SELECT
    schema_name,
    table_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    enabled_status,
    transaction_usage,
    usage_basis,
    runtime_calls,
    keterangan
FROM sc_mst.jsys_core_object_registry
WHERE object_type = 'TRIGGER'
ORDER BY
    CASE jsys_scope WHEN 'CORE' THEN 1 ELSE 2 END,
    schema_name,
    table_location,
    object_name;

/* ============================================================================
   06. REPORT FUNCTION / PROCEDURE SAJA
============================================================================ */
SELECT
    object_type,
    schema_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    transaction_usage,
    usage_basis,
    runtime_calls,
    function_language,
    keterangan
FROM sc_mst.jsys_core_object_registry
WHERE object_type IN ('FUNCTION','PROCEDURE')
ORDER BY
    CASE jsys_scope WHEN 'CORE' THEN 1 ELSE 2 END,
    schema_name,
    object_type,
    object_name,
    object_identity;

/* ============================================================================
   07. REPORT YANG BENAR-BENAR TERAMATI DIPAKAI
============================================================================ */
SELECT
    object_type,
    schema_name,
    table_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    enabled_status,
    transaction_usage,
    runtime_calls,
    runtime_reads,
    runtime_writes,
    keterangan
FROM sc_mst.jsys_core_object_registry
WHERE
       condition_status = 'ACTIVE'
    OR status IN ('ENABLED_AND_FUNCTION_OBSERVED','CALLED','RUNTIME_ACTIVITY_OBSERVED')
ORDER BY
    schema_name,
    object_type,
    table_location,
    object_name;

/* ============================================================================
   08. REPORT BELUM TERAMATI / REVIEW
============================================================================ */
SELECT
    object_type,
    schema_name,
    table_name,
    table_location,
    object_name,
    object_identity,
    jsys_scope,
    condition_status,
    status,
    enabled_status,
    transaction_usage,
    runtime_calls,
    runtime_reads,
    runtime_writes,
    keterangan
FROM sc_mst.jsys_core_object_registry
WHERE condition_status IN ('NOT_OBSERVED','UNKNOWN_RUNTIME','INACTIVE')
ORDER BY
    CASE jsys_scope WHEN 'CORE' THEN 1 ELSE 2 END,
    schema_name,
    object_type,
    table_location,
    object_name;

/* ============================================================================
   09. RINGKASAN BERDASARKAN TRANSAKSI
============================================================================ */
SELECT
    transaction_usage,
    object_type,
    jsys_scope,
    condition_status,
    COUNT(*) AS total_object,
    SUM(runtime_calls) AS total_runtime_calls,
    SUM(runtime_reads) AS total_runtime_reads,
    SUM(runtime_writes) AS total_runtime_writes
FROM sc_mst.jsys_core_object_registry
GROUP BY
    transaction_usage,
    object_type,
    jsys_scope,
    condition_status
ORDER BY
    transaction_usage,
    object_type,
    jsys_scope,
    condition_status;

/* ============================================================================
   10. CLEAN TEST TRANSACTION DATA

   HANYA TEST DATA.
   Tidak menyentuh sc_mst.*.
   Source key = sc_trx.transaction_dt.
============================================================================ */
CREATE TEMP TABLE tmp_clean_transaction
ON COMMIT DROP
AS
SELECT DISTINCT
    BTRIM(uniqueid::TEXT) AS uniqueid,
    BTRIM(COALESCE(docno::TEXT,'')) AS docno,
    BTRIM(COALESCE(source_uniqueid::TEXT,'')) AS source_uniqueid,
    BTRIM(COALESCE(ref_docno::TEXT,'')) AS ref_docno
FROM sc_trx.transaction_dt
WHERE
       UPPER(COALESCE(docno::TEXT,'')) LIKE '%TEST%'
    OR UPPER(COALESCE(ref_docno::TEXT,'')) LIKE '%TEST%'
    OR UPPER(COALESCE(uniqueid::TEXT,'')) LIKE '%TEST%'
    OR UPPER(COALESCE(keterangan::TEXT,'')) = 'TEST';

SELECT
    'TEST TRANSACTION TO CLEAN' AS report,
    COUNT(*) AS line_count,
    COUNT(DISTINCT docno) AS doc_count,
    MIN(docno) AS sample_docno_min,
    MAX(docno) AS sample_docno_max
FROM tmp_clean_transaction;

/* --------------------------------------------------------------------------
   10A. CLEAN JOURNAL DETAIL / HEADER
--------------------------------------------------------------------------- */
UPDATE sc_trx.jurnal_hd jh
SET
    status = 'DRAFT',
    updateddate = CURRENT_TIMESTAMP
WHERE
       BTRIM(COALESCE(jh.source_uniqueid::TEXT,'')) IN
           (SELECT uniqueid FROM tmp_clean_transaction)
    OR BTRIM(COALESCE(jh.docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction)
    OR BTRIM(COALESCE(jh.ref_docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction);

DELETE FROM sc_trx.jurnal_dt jd
WHERE
       BTRIM(COALESCE(jd.source_uniqueid::TEXT,'')) IN
           (SELECT uniqueid FROM tmp_clean_transaction)
    OR BTRIM(COALESCE(jd.ref_docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction);

DELETE FROM sc_trx.jurnal_hd jh
WHERE
       BTRIM(COALESCE(jh.source_uniqueid::TEXT,'')) IN
           (SELECT uniqueid FROM tmp_clean_transaction)
    OR BTRIM(COALESCE(jh.docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction)
    OR BTRIM(COALESCE(jh.ref_docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction);

/* --------------------------------------------------------------------------
   10B. CLEAN TRANSACTION DETAIL

   Accounting final dinonaktifkan sementara agar DELETE tidak membuat
   reversal journal baru. Trigger stock/asset/avgcost tetap aktif.
--------------------------------------------------------------------------- */
DO $$
BEGIN
    IF EXISTS
    (
        SELECT 1
        FROM pg_trigger t
        JOIN pg_class c ON c.oid = t.tgrelid
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE n.nspname = 'sc_trx'
          AND c.relname = 'transaction_dt'
          AND t.tgname = 'trg_zz_transaction_accounting'
          AND NOT t.tgisinternal
    ) THEN
        EXECUTE 'ALTER TABLE sc_trx.transaction_dt DISABLE TRIGGER trg_zz_transaction_accounting';
    END IF;
END;
$$;

DELETE FROM sc_trx.transaction_dt t
WHERE BTRIM(t.uniqueid::TEXT) IN
      (SELECT uniqueid FROM tmp_clean_transaction);

DO $$
BEGIN
    IF EXISTS
    (
        SELECT 1
        FROM pg_trigger t
        JOIN pg_class c ON c.oid = t.tgrelid
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE n.nspname = 'sc_trx'
          AND c.relname = 'transaction_dt'
          AND t.tgname = 'trg_zz_transaction_accounting'
          AND NOT t.tgisinternal
    ) THEN
        EXECUTE 'ALTER TABLE sc_trx.transaction_dt ENABLE TRIGGER trg_zz_transaction_accounting';
    END IF;
END;
$$;

/* --------------------------------------------------------------------------
   10C. CLEAN TRANSACTION HEADER
--------------------------------------------------------------------------- */
DELETE FROM sc_trx.transaction_hd h
WHERE
       BTRIM(COALESCE(h.docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction)
    OR BTRIM(COALESCE(h.ref_docno::TEXT,'')) IN
           (SELECT docno FROM tmp_clean_transaction);

/* --------------------------------------------------------------------------
   10D. CLEAN SISA STOCK / ASSET TEST DATA
--------------------------------------------------------------------------- */
DELETE FROM sc_trx.stkblc s
WHERE
       BTRIM(COALESCE(s.source_uniqueid::TEXT,'')) IN
           (SELECT uniqueid FROM tmp_clean_transaction)
    OR UPPER(COALESCE(s.docno::TEXT,'')) LIKE '%TEST%'
    OR UPPER(COALESCE(s.docref::TEXT,'')) LIKE '%TEST%';

DELETE FROM sc_trx.assetblc a
WHERE
       BTRIM(COALESCE(a.source_uniqueid::TEXT,'')) IN
           (SELECT uniqueid FROM tmp_clean_transaction)
    OR UPPER(COALESCE(a.docno::TEXT,'')) LIKE '%TEST%'
    OR UPPER(COALESCE(a.ref_docno::TEXT,'')) LIKE '%TEST%';

/* Average cost dihitung ulang bila function final tersedia. */
DO $$
BEGIN
    IF to_regprocedure('sc_trx.fn_recalculate_all_avgcost()') IS NOT NULL THEN
        PERFORM sc_trx.fn_recalculate_all_avgcost();
    END IF;
END;
$$;

/* ============================================================================
   11. SC_TMP

   CLEAN otomatis untuk seluruh tabel sc_tmp DIHAPUS.
   Alasannya: struktur sc_tmp dapat berbeda-beda dan menghapus berdasarkan
   kolom yang kebetulan mengandung TEST berisiko membersihkan staging yang
   masih dipakai JSYS. Registry tetap mencatat semua object sc_tmp untuk review.
============================================================================ */
/* ============================================================================
   11B. REPORT SC_TMP YANG TERDAFTAR
============================================================================ */
SELECT
    object_type,
    schema_name,
    table_name,
    table_location,
    object_name,
    jsys_scope,
    condition_status,
    status,
    transaction_usage,
    runtime_calls,
    runtime_reads,
    runtime_writes,
    keterangan
FROM sc_mst.jsys_core_object_registry
WHERE schema_name = 'sc_tmp'
ORDER BY object_type, table_location, object_name;

/* ============================================================================
   12. POST CLEAN CHECK
============================================================================ */
SELECT
    'POST CLEAN CHECK' AS report,
    (SELECT COUNT(*) FROM tmp_clean_transaction) AS originally_marked_test_lines,
    (SELECT COUNT(*)
       FROM sc_trx.transaction_dt t
      WHERE BTRIM(t.uniqueid::TEXT) IN (SELECT uniqueid FROM tmp_clean_transaction))
        AS remaining_transaction_dt,
    (SELECT COUNT(*)
       FROM sc_trx.stkblc s
      WHERE BTRIM(COALESCE(s.source_uniqueid::TEXT,'')) IN (SELECT uniqueid FROM tmp_clean_transaction))
        AS remaining_stkblc,
    (SELECT COUNT(*)
       FROM sc_trx.assetblc a
      WHERE BTRIM(COALESCE(a.source_uniqueid::TEXT,'')) IN (SELECT uniqueid FROM tmp_clean_transaction))
        AS remaining_assetblc,
    (SELECT COUNT(*)
       FROM sc_trx.jurnal_dt jd
      WHERE BTRIM(COALESCE(jd.source_uniqueid::TEXT,'')) IN (SELECT uniqueid FROM tmp_clean_transaction))
        AS remaining_jurnal_dt,
    (SELECT COUNT(*)
       FROM sc_trx.jurnal_hd jh
      WHERE BTRIM(COALESCE(jh.source_uniqueid::TEXT,'')) IN (SELECT uniqueid FROM tmp_clean_transaction))
        AS remaining_jurnal_hd;

/* ============================================================================
   13. FINAL OBJECT SUMMARY
============================================================================ */
SELECT
    object_type,
    jsys_scope,
    condition_status,
    COUNT(*) AS total_object,
    SUM(runtime_calls) AS total_runtime_calls,
    SUM(runtime_reads) AS total_runtime_reads,
    SUM(runtime_writes) AS total_runtime_writes
FROM sc_mst.jsys_core_object_registry
GROUP BY
    object_type,
    jsys_scope,
    condition_status
ORDER BY
    object_type,
    jsys_scope,
    condition_status;
--rollback;
COMMIT;

/* ============================================================================
   QUERY CEPAT SETELAH SCRIPT
============================================================================

   -- 1. Semua table yang terdaftar sebagai JSYS Core:
   SELECT *
   FROM sc_mst.jsys_core_object_registry
   WHERE object_type = 'TABLE'
   ORDER BY jsys_scope, schema_name, table_name;

   -- 2. Semua object untuk PEMBELIAN:
   SELECT *
   FROM sc_mst.jsys_core_object_registry
   WHERE transaction_usage ILIKE '%PEMBELIAN%'
   ORDER BY schema_name, object_type, table_location, object_name;

   -- 3. Semua object untuk PENJUALAN:
   SELECT *
   FROM sc_mst.jsys_core_object_registry
   WHERE transaction_usage ILIKE '%PENJUALAN%'
   ORDER BY schema_name, object_type, table_location, object_name;

   -- 4. Semua object untuk PRODUKSI:
   SELECT *
   FROM sc_mst.jsys_core_object_registry
   WHERE transaction_usage ILIKE '%PRODUKSI%'
   ORDER BY schema_name, object_type, table_location, object_name;

   -- 5. Object yang benar-benar teramati aktif:
   SELECT *
   FROM sc_mst.jsys_core_object_registry
   WHERE condition_status = 'ACTIVE'
      OR status IN ('ENABLED_AND_FUNCTION_OBSERVED','CALLED','RUNTIME_ACTIVITY_OBSERVED')
   ORDER BY schema_name, object_type, table_location, object_name;

   -- 6. Object yang belum teramati:
   SELECT *
   FROM sc_mst.jsys_core_object_registry
   WHERE condition_status IN ('NOT_OBSERVED','UNKNOWN_RUNTIME','INACTIVE')
   ORDER BY jsys_scope, schema_name, object_type, table_location, object_name;

   ============================================================================ */
