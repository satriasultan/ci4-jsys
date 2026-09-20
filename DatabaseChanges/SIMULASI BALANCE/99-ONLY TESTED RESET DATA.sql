/*
============================================================================
JSYS ERP
RESET TOTAL DATA TRANSAKSI - V2
============================================================================

MASALAH V1:
    ERROR:
    Jurnal detail 194 tidak dapat dihapus.
    Status header = REVERSED.

PENYEBAB:
    trg_validate_jurnal_detail_status pada sc_trx.jurnal_dt
    memang melarang DELETE jika jurnal_hd bukan DRAFT.

SOLUSI:
    Untuk RESET TOTAL, seluruh USER TRIGGER pada tabel transaksi
    sementara DISABLE.
    Foreign-key/internal trigger tetap aktif.

    Setelah seluruh data transaksi dihapus,
    USER TRIGGER di-ENABLE kembali.

TIDAK DIHAPUS:
    - sc_mst.*
    - master COA
    - master barang
    - currency
    - journal_type
    - journal_type_coa
    - tax
    - konfigurasi

DIHAPUS:
    - jurnal_dt
    - jurnal_hd
    - assetblc
    - stkblc_avgcost
    - stkblc
    - transaction_hd
    - transaction_dt

CATATAN:
    Ini RESET PERMANEN.
    Jalankan COMMIT jika ingin menghapus permanen.
    Untuk simulasi, ganti COMMIT menjadi ROLLBACK.
============================================================================
*/

BEGIN;


/* ============================================================================
   1. DISABLE USER TRIGGER
   ----------------------------------------------------------------------------
   Tujuan:
       - tidak menjalankan validasi DRAFT/POSTED/REVERSED
       - tidak menjalankan auto reverse/post
       - tidak menjalankan sync header
       - tidak menjalankan stock/asset/accounting trigger

   INTERNAL FK TRIGGER tetap aktif.
   ============================================================================ */

ALTER TABLE sc_trx.jurnal_dt
    DISABLE TRIGGER USER;

ALTER TABLE sc_trx.jurnal_hd
    DISABLE TRIGGER USER;

ALTER TABLE sc_trx.assetblc
    DISABLE TRIGGER USER;

ALTER TABLE sc_trx.stkblc_avgcost
    DISABLE TRIGGER USER;

ALTER TABLE sc_trx.stkblc
    DISABLE TRIGGER USER;

ALTER TABLE sc_trx.transaction_hd
    DISABLE TRIGGER USER;

ALTER TABLE sc_trx.transaction_dt
    DISABLE TRIGGER USER;


/* ============================================================================
   2. HAPUS JOURNAL DETAIL
   ----------------------------------------------------------------------------
   Harus lebih dahulu karena FK jurnal_dt -> jurnal_hd.
   ============================================================================ */

DELETE FROM sc_trx.jurnal_dt;


/* ============================================================================
   3. HAPUS JOURNAL HEADER
   ============================================================================ */

DELETE FROM sc_trx.jurnal_hd;


/* ============================================================================
   4. HAPUS ASSET
   ============================================================================ */

DELETE FROM sc_trx.assetblc;


/* ============================================================================
   5. HAPUS AVG COST
   ============================================================================ */

DELETE FROM sc_trx.stkblc_avgcost;


/* ============================================================================
   6. HAPUS STOCK LEDGER
   ============================================================================ */

DELETE FROM sc_trx.stkblc;


/* ============================================================================
   7. HAPUS TRANSACTION HEADER
   ============================================================================ */

DELETE FROM sc_trx.transaction_hd;


/* ============================================================================
   8. HAPUS TRANSACTION DETAIL
   ============================================================================ */

DELETE FROM sc_trx.transaction_dt;


/* ============================================================================
   9. RESET SEQUENCE ID
   ----------------------------------------------------------------------------
   Aman dilakukan hanya untuk sequence yang memang terkait tabel core.
   Tidak menyentuh sequence master.
   ============================================================================ */

DO $$
DECLARE
    r RECORD;
BEGIN

    FOR r IN
        SELECT
            n.nspname AS schema_name,
            c.relname AS sequence_name
        FROM pg_class c
        JOIN pg_namespace n
          ON n.oid = c.relnamespace
        JOIN pg_depend d
          ON d.objid = c.oid
         AND d.deptype = 'a'
        JOIN pg_class t
          ON t.oid = d.refobjid
        JOIN pg_namespace nt
          ON nt.oid = t.relnamespace
        WHERE c.relkind = 'S'
          AND nt.nspname = 'sc_trx'
          AND t.relname IN
          (
              'transaction_dt',
              'transaction_hd',
              'jurnal_hd',
              'jurnal_dt',
              'stkblc',
              'stkblc_avgcost',
              'assetblc'
          )
        ORDER BY n.nspname, c.relname
    LOOP

        EXECUTE format(
            'ALTER SEQUENCE %I.%I RESTART WITH 1',
            r.schema_name,
            r.sequence_name
        );

    END LOOP;

END;
$$;


/* ============================================================================
   10. ENABLE USER TRIGGER KEMBALI
   ============================================================================ */

ALTER TABLE sc_trx.jurnal_dt
    ENABLE TRIGGER USER;

ALTER TABLE sc_trx.jurnal_hd
    ENABLE TRIGGER USER;

ALTER TABLE sc_trx.assetblc
    ENABLE TRIGGER USER;

ALTER TABLE sc_trx.stkblc_avgcost
    ENABLE TRIGGER USER;

ALTER TABLE sc_trx.stkblc
    ENABLE TRIGGER USER;

ALTER TABLE sc_trx.transaction_hd
    ENABLE TRIGGER USER;

ALTER TABLE sc_trx.transaction_dt
    ENABLE TRIGGER USER;


/* ============================================================================
   11. VERIFIKASI
   ============================================================================ */

SELECT
    'jurnal_dt' AS table_name,
    COUNT(*) AS jumlah_data
FROM sc_trx.jurnal_dt

UNION ALL

SELECT
    'jurnal_hd',
    COUNT(*)
FROM sc_trx.jurnal_hd

UNION ALL

SELECT
    'assetblc',
    COUNT(*)
FROM sc_trx.assetblc

UNION ALL

SELECT
    'stkblc_avgcost',
    COUNT(*)
FROM sc_trx.stkblc_avgcost

UNION ALL

SELECT
    'stkblc',
    COUNT(*)
FROM sc_trx.stkblc

UNION ALL

SELECT
    'transaction_hd',
    COUNT(*)
FROM sc_trx.transaction_hd

UNION ALL

SELECT
    'transaction_dt',
    COUNT(*)
FROM sc_trx.transaction_dt

ORDER BY table_name;


/* ============================================================================
   12. VALIDASI SEMUA = 0
   ============================================================================ */

DO $$
DECLARE
    v_total BIGINT;
BEGIN

    SELECT
          (SELECT COUNT(*) FROM sc_trx.jurnal_dt)
        + (SELECT COUNT(*) FROM sc_trx.jurnal_hd)
        + (SELECT COUNT(*) FROM sc_trx.assetblc)
        + (SELECT COUNT(*) FROM sc_trx.stkblc_avgcost)
        + (SELECT COUNT(*) FROM sc_trx.stkblc)
        + (SELECT COUNT(*) FROM sc_trx.transaction_hd)
        + (SELECT COUNT(*) FROM sc_trx.transaction_dt)
    INTO v_total;

    IF v_total <> 0 THEN

        RAISE EXCEPTION
            'RESET GAGAL: masih ada % row pada core transaction table.',
            v_total;

    END IF;

    RAISE NOTICE '============================================================';
    RAISE NOTICE 'RESET TOTAL DATA TRANSAKSI : OK';
    RAISE NOTICE 'jurnal_dt       = 0';
    RAISE NOTICE 'jurnal_hd       = 0';
    RAISE NOTICE 'assetblc        = 0';
    RAISE NOTICE 'stkblc_avgcost  = 0';
    RAISE NOTICE 'stkblc          = 0';
    RAISE NOTICE 'transaction_hd  = 0';
    RAISE NOTICE 'transaction_dt  = 0';
    RAISE NOTICE 'USER TRIGGER    = ENABLED KEMBALI';
    RAISE NOTICE 'sc_mst          = TIDAK DISENTUH';
    RAISE NOTICE '============================================================';

END;
$$;


/* ============================================================================
   PERMANEN
   ============================================================================ */

COMMIT;


/*
============================================================================
UNTUK TEST SAJA:
    ubah COMMIT menjadi ROLLBACK.

PENTING:
    Script ini memang melewati logic reverse/post trigger karena seluruh
    data transaksi akan dihapus. Jangan gunakan script ini untuk penghapusan
    satu transaksi individual.
============================================================================
*/

