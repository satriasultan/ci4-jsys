/* ============================================================================
   JSYS ERP — TEST INSERT MODUL ADJUSTMENT STOCK
   ----------------------------------------------------------------------------
   TEST 1 : ADJINX  — Penyesuaian masuk (D)
   TEST 2 : ADJOUT  — Penyesuaian keluar (K)
   TEST 3 : Multi-line dalam 1 dokumen (IN + OUT)
   ----------------------------------------------------------------------------
   Semua test data pakai prefix 'TEST-ADJ%'.
   Tidak menyentuh trigger TAHAP 01-19.
   ============================================================================ */

BEGIN;

/* ============================================================================
   STEP 0 — BERSIHKAN DATA TEST LAMA
   ============================================================================ */

DELETE FROM sc_trx.transaction_dt
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_trx.transaction_hd
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_trx.stkblc
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang) = '01040201000017'
  AND BTRIM(idlocation) = '16401'
  AND BTRIM(batch) = 'BATCH01';

DELETE FROM sc_trx.jurnal_dt
WHERE jurnal_id IN (
    SELECT id FROM sc_trx.jurnal_hd
    WHERE BTRIM(docno) LIKE 'TEST-ADJ%'
);

DELETE FROM sc_trx.jurnal_hd
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_trx.ajustment_stock_dtl
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_trx.ajustment_stock_mst
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_tmp.ajustment_stock_dtl
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';

DELETE FROM sc_tmp.ajustment_stock_mst
WHERE BTRIM(docno) LIKE 'TEST-ADJ%';


/* ============================================================================
   TEST 1 — ADJINX : Penyesuaian Masuk (D)
   ----------------------------------------------------------------------------
   Barang  : 01040201000017 / CRITERION 60
   Lokasi  : 16401
   Batch   : BATCH01
   Qty     : 100 KG
   Nilai   : 100,000,000
   COA     : D 115401 Persediaan
             K 611001 Beban Selisih
   ============================================================================ */

INSERT INTO sc_tmp.ajustment_stock_mst (
    docno, doctype, docdate, docref, cabang, pemohon,
    status, description,
    currency, kurs, isinclusive, idtax,
    inputby, inputdate
) VALUES (
    'TEST-ADJ/001',
    'AJUSTMENT_STOCK',
    CURRENT_DATE::TEXT,
    'TEST-REF-001',
    'JTS1',
    'TESTER',
    'E',
    'TEST ADJUSTMENT IN — PENYESUAIAN MASUK',
    'IDR', 1, 'NO', 'NON',
    'TESTER', CURRENT_TIMESTAMP
);


INSERT INTO sc_tmp.ajustment_stock_dtl (
    docno, docref, doctype,
    idbarang, nmbarang, unit,
    qtystock, qty,
    val, valqty, valsum,
    dk,
    currency,
    idlocation, batch,
    description, status,
    inputby, inputdate,
    iduniq,
    idcoa, counter_idcoa
) VALUES (
    'TEST-ADJ/001',
    'TEST-REF-001',
    'AJUSTMENT_STOCK',
    '01040201000017',
    'CRITERION 60',
    'KG',
    0,
    100,
    1000000, 1000000, 100000000,
    'D',
    'IDR',
    '16401', 'BATCH01',
    'TEST ADJUSTMENT IN',
    'E',
    'TESTER', CURRENT_TIMESTAMP,
    'UID-TEST-001',
    '115401', '611001'
);


/* Finalize TEST 1 → E ke F */
UPDATE sc_tmp.ajustment_stock_mst
SET status = 'F',
    printby = NULL,
    printdate = NULL
WHERE BTRIM(docno) = 'TEST-ADJ/001';


/* ============================================================================
   CEK HASIL TEST 1
   ============================================================================ */

SELECT '=== TEST 1 : transaction_dt ===' AS bagian;

SELECT
    id, uniqueid,
    docno, doctype, journal_type, line_no, docdate,
    idbranch, cabang, type_in_out,
    idbarang, namabarang, idunit,
    warehouse, batch,
    qty, harga, bruto, nilai, dpp, pajak, total,
    idcoa, counter_idcoa, debet_kredit,
    idtax, currcode, kurs,
    module, direction,
    stock_effect, accounting_effect, asset_effect,
    stock_uniqueid, stock_key,
    keterangan, createdby
FROM sc_trx.transaction_dt
WHERE BTRIM(docno) = 'TEST-ADJ/001'
ORDER BY id;


SELECT '=== TEST 1 : transaction_hd ===' AS bagian;

SELECT
    id, docno, doctype, journal_type, type_in_out, docdate,
    idbranch, cabang,
    module, direction,
    stock_effect, accounting_effect, asset_effect,
    total_qty, total_qty_in, total_qty_out,
    total_bruto, total_nilai, total_dpp, total_pajak, total_total,
    total_debet, total_kredit, balance,
    total_line,
    createdby, createddate
FROM sc_trx.transaction_hd
WHERE BTRIM(docno) = 'TEST-ADJ/001'
ORDER BY id;


SELECT '=== TEST 1 : stkblc ===' AS bagian;

SELECT
    id, uniqueid, docno, journal_type, type_in_out,
    idbarang, idlocation, batch,
    qty_in, qty_out,
    unitcost, totalcost,
    stock_uniqueid, stock_key
FROM sc_trx.stkblc
WHERE BTRIM(docno) = 'TEST-ADJ/001'
ORDER BY id;


SELECT '=== TEST 1 : stkblc_avgcost ===' AS bagian;

SELECT
    idbarang, idlocation, batch,
    qty, total_value, avg_cost, updated_at
FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang) = '01040201000017'
  AND BTRIM(idlocation) = '16401'
  AND BTRIM(batch) = 'BATCH01';


SELECT '=== TEST 1 : jurnal_hd + jurnal_dt ===' AS bagian;

SELECT
    jh.id AS jurnal_id,
    jh.uniqueid,
    jh.source_uniqueid,
    jh.docno, jh.journal_type,
    jh.status,
    jh.total_debet, jh.total_kredit, jh.balance,
    jd.seq,
    jd.account_role,
    jd.idcoa,
    jd.debet, jd.kredit,
    jd.keterangan
FROM sc_trx.jurnal_hd jh
LEFT JOIN sc_trx.jurnal_dt jd ON jd.jurnal_id = jh.id
WHERE BTRIM(jh.docno) = 'TEST-ADJ/001'
ORDER BY jh.id, jd.seq;


COMMIT;


/* ============================================================================
   TEST 2 — ADJOUT : Penyesuaian Keluar (K)
   ----------------------------------------------------------------------------
   Barang  : 01040201000017 / CRITERION 60
   Lokasi  : 16401
   Batch   : BATCH01
   Qty     : 50 KG
   Nilai   : 50,000,000
   COA     : D 611001 Beban Selisih
             K 115401 Persediaan
   ============================================================================ */

BEGIN;

INSERT INTO sc_tmp.ajustment_stock_mst (
    docno, doctype, docdate, docref, cabang, pemohon,
    status, description,
    currency, kurs, isinclusive, idtax,
    inputby, inputdate
) VALUES (
    'TEST-ADJ/002',
    'AJUSTMENT_STOCK',
    CURRENT_DATE::TEXT,
    'TEST-REF-002',
    'JTS1',
    'TESTER',
    'E',
    'TEST ADJUSTMENT OUT — PENYESUAIAN KELUAR',
    'IDR', 1, 'NO', 'NON',
    'TESTER', CURRENT_TIMESTAMP
);


INSERT INTO sc_tmp.ajustment_stock_dtl (
    docno, docref, doctype,
    idbarang, nmbarang, unit,
    qtystock, qty,
    val, valqty, valsum,
    dk,
    currency,
    idlocation, batch,
    description, status,
    inputby, inputdate,
    iduniq,
    idcoa, counter_idcoa
) VALUES (
    'TEST-ADJ/002',
    'TEST-REF-002',
    'AJUSTMENT_STOCK',
    '01040201000017',
    'CRITERION 60',
    'KG',
    100,
    50,
    1000000, 1000000, 50000000,
    'K',
    'IDR',
    '16401', 'BATCH01',
    'TEST ADJUSTMENT OUT',
    'E',
    'TESTER', CURRENT_TIMESTAMP,
    'UID-TEST-002',
    '611001', '115401'
);


UPDATE sc_tmp.ajustment_stock_mst
SET status = 'F',
    printby = NULL,
    printdate = NULL
WHERE BTRIM(docno) = 'TEST-ADJ/002';


SELECT '=== TEST 2 : transaction_dt ===' AS bagian;

SELECT
    id, uniqueid,
    docno, journal_type, type_in_out,
    idbarang, warehouse, batch,
    qty, nilai, total,
    idcoa, counter_idcoa, debet_kredit,
    module, direction, stock_effect, accounting_effect,
    stock_uniqueid, stock_key
FROM sc_trx.transaction_dt
WHERE BTRIM(docno) = 'TEST-ADJ/002'
ORDER BY id;


SELECT '=== TEST 2 : stkblc ===' AS bagian;

SELECT
    id, uniqueid, docno, journal_type, type_in_out,
    idbarang, idlocation, batch,
    qty_in, qty_out,
    unitcost, totalcost,
    stock_uniqueid, stock_key
FROM sc_trx.stkblc
WHERE BTRIM(docno) = 'TEST-ADJ/002'
ORDER BY id;


SELECT '=== TEST 2 : stkblc_avgcost ===' AS bagian;

SELECT
    idbarang, idlocation, batch,
    qty, total_value, avg_cost, updated_at
FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang) = '01040201000017'
  AND BTRIM(idlocation) = '16401'
  AND BTRIM(batch) = 'BATCH01';


SELECT '=== TEST 2 : jurnal_hd + jurnal_dt ===' AS bagian;

SELECT
    jh.id AS jurnal_id,
    jh.uniqueid,
    jh.docno, jh.journal_type,
    jh.status,
    jh.total_debet, jh.total_kredit, jh.balance,
    jd.seq, jd.account_role, jd.idcoa,
    jd.debet, jd.kredit
FROM sc_trx.jurnal_hd jh
LEFT JOIN sc_trx.jurnal_dt jd ON jd.jurnal_id = jh.id
WHERE BTRIM(jh.docno) = 'TEST-ADJ/002'
ORDER BY jh.id, jd.seq;


COMMIT;


/* ============================================================================
   TEST 3 — MULTI-LINE : 1 DOKUMEN BERISI IN + OUT
   ----------------------------------------------------------------------------
   Detail 1 : IN  50 KG @ 1,000,000 = 50,000,000
   Detail 2 : OUT 20 KG @ 1,000,000 = 20,000,000
   ============================================================================ */

BEGIN;

INSERT INTO sc_tmp.ajustment_stock_mst (
    docno, doctype, docdate, docref, cabang, pemohon,
    status, description,
    currency, kurs, isinclusive, idtax,
    inputby, inputdate
) VALUES (
    'TEST-ADJ/003',
    'AJUSTMENT_STOCK',
    CURRENT_DATE::TEXT,
    'TEST-REF-003',
    'JTS1',
    'TESTER',
    'E',
    'TEST ADJUSTMENT MULTI-LINE',
    'IDR', 1, 'NO', 'NON',
    'TESTER', CURRENT_TIMESTAMP
);


INSERT INTO sc_tmp.ajustment_stock_dtl (
    docno, docref, doctype,
    idbarang, nmbarang, unit,
    qtystock, qty,
    val, valqty, valsum,
    dk, currency,
    idlocation, batch,
    description, status,
    inputby, inputdate,
    iduniq,
    idcoa, counter_idcoa
) VALUES
(
    'TEST-ADJ/003', 'TEST-REF-003', 'AJUSTMENT_STOCK',
    '01040201000017', 'CRITERION 60', 'KG',
    0, 50,
    1000000, 1000000, 50000000,
    'D', 'IDR',
    '16401', 'BATCH01',
    'MULTI-LINE IN', 'E',
    'TESTER', CURRENT_TIMESTAMP,
    'UID-TEST-003-A',
    '115401', '611001'
),
(
    'TEST-ADJ/003', 'TEST-REF-003', 'AJUSTMENT_STOCK',
    '01040201000017', 'CRITERION 60', 'KG',
    50, 20,
    1000000, 1000000, 20000000,
    'K', 'IDR',
    '16401', 'BATCH01',
    'MULTI-LINE OUT', 'E',
    'TESTER', CURRENT_TIMESTAMP,
    'UID-TEST-003-B',
    '611001', '115401'
);


UPDATE sc_tmp.ajustment_stock_mst
SET status = 'F',
    printby = NULL,
    printdate = NULL
WHERE BTRIM(docno) = 'TEST-ADJ/003';


SELECT '=== TEST 3 : transaction_dt ===' AS bagian;

SELECT
    id, uniqueid,
    docno, journal_type, line_no, type_in_out,
    idbarang, warehouse, batch,
    qty, nilai, total,
    idcoa, counter_idcoa, debet_kredit,
    module, direction, stock_effect, accounting_effect,
    stock_uniqueid, stock_key
FROM sc_trx.transaction_dt
WHERE BTRIM(docno) = 'TEST-ADJ/003'
ORDER BY id;


SELECT '=== TEST 3 : transaction_hd (harus 2 baris: IN + OUT) ===' AS bagian;

SELECT
    id, docno, journal_type, type_in_out,
    total_qty, total_qty_in, total_qty_out,
    total_nilai, total_total,
    total_debet, total_kredit, balance,
    total_line
FROM sc_trx.transaction_hd
WHERE BTRIM(docno) = 'TEST-ADJ/003'
ORDER BY type_in_out;


SELECT '=== TEST 3 : stkblc ===' AS bagian;

SELECT
    id, uniqueid, docno, journal_type, type_in_out,
    idbarang, idlocation, batch,
    qty_in, qty_out,
    unitcost, totalcost
FROM sc_trx.stkblc
WHERE BTRIM(docno) = 'TEST-ADJ/003'
ORDER BY id;


SELECT '=== TEST 3 : stkblc_avgcost ===' AS bagian;

SELECT
    idbarang, idlocation, batch,
    qty, total_value, avg_cost, updated_at
FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang) = '01040201000017'
  AND BTRIM(idlocation) = '16401'
  AND BTRIM(batch) = 'BATCH01';


SELECT '=== TEST 3 : jurnal_hd + jurnal_dt ===' AS bagian;

SELECT
    jh.id AS jurnal_id,
    jh.uniqueid,
    jh.docno, jh.journal_type, jh.type_in_out,
    jh.status,
    jh.total_debet, jh.total_kredit, jh.balance,
    jd.seq, jd.account_role, jd.idcoa,
    jd.debet, jd.kredit
FROM sc_trx.jurnal_hd jh
LEFT JOIN sc_trx.jurnal_dt jd ON jd.jurnal_id = jh.id
WHERE BTRIM(jh.docno) = 'TEST-ADJ/003'
ORDER BY jh.id, jd.seq;


COMMIT;


/* ============================================================================
   RINGKASAN AKHIR — CEK SEMUA TEST
   ============================================================================ */

SELECT '=== RINGKASAN SEMUA DATA TEST ===' AS bagian;

SELECT
    docno, doctype, journal_type, type_in_out,
    total_qty, total_nilai,
    total_debet, total_kredit, balance,
    total_line,
    stock_effect, accounting_effect
FROM sc_trx.transaction_hd
WHERE BTRIM(docno) LIKE 'TEST-ADJ%'
ORDER BY docno, type_in_out;


SELECT '=== RINGKASAN STOCK MOVEMENT ===' AS bagian;

SELECT
    docno, type_in_out, idbarang, idlocation, batch,
    qty_in, qty_out,
    unitcost, totalcost
FROM sc_trx.stkblc
WHERE BTRIM(docno) LIKE 'TEST-ADJ%'
ORDER BY docno, id;


SELECT '=== RINGKASAN POSISI STOK AKHIR ===' AS bagian;

SELECT
    idbarang, idlocation, batch,
    qty, total_value, avg_cost, updated_at
FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang) = '01040201000017'
  AND BTRIM(idlocation) = '16401'
  AND BTRIM(batch) = 'BATCH01';


SELECT '=== RINGKASAN JURNAL ===' AS bagian;

SELECT
    jh.docno, jh.journal_type, jh.type_in_out,
    jh.status,
    jh.total_debet, jh.total_kredit, jh.balance,
    COUNT(jd.id) AS jml_detail
FROM sc_trx.jurnal_hd jh
LEFT JOIN sc_trx.jurnal_dt jd ON jd.jurnal_id = jh.id
WHERE BTRIM(jh.docno) LIKE 'TEST-ADJ%'
GROUP BY
    jh.id, jh.docno, jh.journal_type, jh.type_in_out,
    jh.status, jh.total_debet, jh.total_kredit, jh.balance
ORDER BY jh.docno, jh.type_in_out;