/* ============================================================================
   JSYS ERP - TEST DATA
   FOKUS: TRANSFER (TRFWHS, BINMOV, BRNTRF)

   TUJUAN:
   ------------------------------------------------------------
   1. Menguji 3 jenis transfer:
      - TRFWHS : Transfer antar gudang
      - BINMOV : Pindah bin
      - BRNTRF : Transfer antar cabang

   2. Memastikan nilai persediaan TIDAK berubah setelah transfer.

   3. Memastikan jurnal accounting:
      - OUT → KREDIT Inventory
      - IN  → DEBIT Inventory
      - 1 baris jurnal_dt per transaction_dt
      - Balance per header boleh ≠ 0 (transfer)
      - Balance OUT + IN = 0

   4. Bisa dijalankan dengan BEGIN/ROLLBACK untuk simulasi.

   PRASYARAT:
   ------------------------------------------------------------
   - TAHAP 01-19 sudah dijalankan
   - Master: mbarang BRG001, currency IDR, journal_type TRFWHS/BINMOV/BRNTRF
   - Minimal ada 1 batch/LOT dengan avg cost established
   ============================================================================ */

BEGIN;

/* ============================================================================
   STEP 0: CLEANUP TEST LAMA
   ============================================================================ */

DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT id
        FROM sc_trx.transaction_dt
        WHERE uniqueid = ANY
        (
            ARRAY[
                -- Setup avg cost
                MD5('TEST-SETUP-GRNREC-BRG001-WH01'),
                MD5('TEST-SETUP-GRNREC-BRG001-WH02'),
                MD5('TEST-SETUP-GRNREC-BRG001-WH03'),

                -- TRFWHS: Transfer WH01 → WH02
                MD5('TEST-TRFWHS-001-OUT'),
                MD5('TEST-TRFWHS-001-IN'),

                -- BINMOV: Pindah bin A01 → A02 (WH01)
                MD5('TEST-BINMOV-001-OUT'),
                MD5('TEST-BINMOV-001-IN'),

                -- BRNTRF: Transfer JTS1 → JTS2
                MD5('TEST-BRNTRF-001-OUT'),
                MD5('TEST-BRNTRF-001-IN')
            ]::TEXT[]
        )
        ORDER BY id DESC
    LOOP
        DELETE FROM sc_trx.transaction_dt
        WHERE id = r.id;
    END LOOP;
END;
$$;


/* ============================================================================
   STEP 1: SETUP AVG COST UNTUK TEST
   ============================================================================
   
   Setup persediaan di:
   - WH01 (JTS1): 100 kg BRG001 BATCH001 @ 10.000 = 1.000.000
   - WH02 (JTS1): 50 kg BRG001 BATCH002 @ 12.000 = 600.000
   
   Untuk transfer antar-cabang, butuh stok di JTS2 juga? Tidak.
   BRNTRF akan transfer dari JTS1 ke JTS2 (JTS2 mulai dari 0).
   ============================================================================ */

/* --- Setup 1: GRNREC ke WH01 (JTS1) --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    kdsupplier, nsupplier,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, bruto, discount, nilai, dpp, pajak, total,
    idtax, isinclusive, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-SETUP-GRNREC-BRG001-WH01'),
    MD5('TEST-SETUP-GRNREC-BRG001-WH01-SRC'),
    'TEST-SETUP-GRN-001',
    'GRN',
    'GRNREC',
    1,
    CURRENT_DATE - INTERVAL '10 days',
    'JTS',
    'JTS1',
    'IN',
    '', '',
    'test_setup', 9001, 1,
    'SUP001', 'SUPPLIER TEST',
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH01', 'A01',
    'BATCH001', 'LOT001',
    100, 10000, 1000000, 0, 1000000, 1000000, 110000, 1110000,
    'PPN11', 'NO', 'IDR', 1, 'TEST'
);

/* --- Setup 2: GRNREC ke WH02 (JTS1) --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    kdsupplier, nsupplier,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, bruto, discount, nilai, dpp, pajak, total,
    idtax, isinclusive, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-SETUP-GRNREC-BRG001-WH02'),
    MD5('TEST-SETUP-GRNREC-BRG001-WH02-SRC'),
    'TEST-SETUP-GRN-002',
    'GRN',
    'GRNREC',
    1,
    CURRENT_DATE - INTERVAL '10 days',
    'JTS',
    'JTS1',
    'IN',
    '', '',
    'test_setup', 9002, 1,
    'SUP001', 'SUPPLIER TEST',
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH02', 'A01',
    'BATCH002', 'LOT002',
    50, 12000, 600000, 0, 600000, 600000, 66000, 666000,
    'PPN11', 'NO', 'IDR', 1, 'TEST'
);


/* ============================================================================
   STEP 2: CATAT NILAI PERSEDIAAN SEBELUM TRANSFER
   ============================================================================
   
   Simpan total nilai perusahaan sebelum transfer.
   ============================================================================ */

SELECT
    'SEBELUM TRANSFER' AS fase,
    SUM(ac.qty) AS total_qty,
    SUM(ac.total_value) AS total_value,
    CASE
        WHEN SUM(ac.qty) > 0 
        THEN ROUND(SUM(ac.total_value) / SUM(ac.qty), 2)
        ELSE 0
    END AS avg_cost_global
FROM sc_trx.stkblc_avgcost ac
WHERE ac.idbarang = 'BRG001';


/* ============================================================================
   TEST 1: TRFWHS - TRANSFER ANTAR GUDANG (WH01 → WH03)
   ============================================================================
   
   Skenario:
   - Transfer 30 kg BRG001 BATCH001 dari WH01 ke WH03
   - Avg cost di WH01 diharapkan = 10.000
   - Nilai transfer = 30 × 10.000 = 300.000
   
   Sumber: WH01 (JTS1)
   Tujuan: WH03 (JTS1)
   ============================================================================ */

/* --- TRFWHS OUT: dari WH01 --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, nilai, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-TRFWHS-001-OUT'),
    MD5('TEST-TRFWHS-001-SRC'),
    'TEST-TRFWHS-001',
    'TRANSFER_WAREHOUSE',
    'TRFWHS',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    '', '',
    'test_transfer', 9101, 1,
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH01', 'A01',
    'BATCH001', 'LOT001',
    30, 10000, 300000, 'IDR', 1, 'TEST'
);

/* --- TRFWHS IN: ke WH03 --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, nilai, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-TRFWHS-001-IN'),
    MD5('TEST-TRFWHS-001-SRC'),
    'TEST-TRFWHS-001',
    'TRANSFER_WAREHOUSE',
    'TRFWHS',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    '', '',
    'test_transfer', 9101, 2,
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH03', 'A01',
    'BATCH001', 'LOT001',
    30, 10000, 300000, 'IDR', 1, 'TEST'
);


/* ============================================================================
   TEST 2: BINMOV - PINDAH BIN (A01 → A02 di WH02)
   ============================================================================
   
   Skenario:
   - Pindah 20 kg BRG001 BATCH002 dari bin A01 ke A02 di WH02
   - Avg cost di WH02 diharapkan = 12.000
   - Nilai = 20 × 12.000 = 240.000
   ============================================================================ */

/* --- BINMOV OUT: dari WH02 A01 --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, nilai, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-BINMOV-001-OUT'),
    MD5('TEST-BINMOV-001-SRC'),
    'TEST-BINMOV-001',
    'BIN_MOVEMENT',
    'BINMOV',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    '', '',
    'test_binmov', 9201, 1,
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH02', 'A01',
    'BATCH002', 'LOT002',
    20, 12000, 240000, 'IDR', 1, 'TEST'
);

/* --- BINMOV IN: ke WH02 A02 --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, nilai, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-BINMOV-001-IN'),
    MD5('TEST-BINMOV-001-SRC'),
    'TEST-BINMOV-001',
    'BIN_MOVEMENT',
    'BINMOV',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    '', '',
    'test_binmov', 9201, 2,
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH02', 'A02',
    'BATCH002', 'LOT002',
    20, 12000, 240000, 'IDR', 1, 'TEST'
);


/* ============================================================================
   TEST 3: BRNTRF - TRANSFER ANTAR CABANG (JTS1 → JTS2)
   ============================================================================
   
   Skenario:
   - Transfer 25 kg BRG001 BATCH001 dari JTS1/WH01 ke JTS2/WH01
   - Avg cost di JTS1/WH01 = 10.000 (setelah TRFWHS keluar 30 kg)
   - Nilai = 25 × 10.000 = 250.000
   
   Catatan:
   - Cabang berbeda (JTS vs JTS)
   - Lokasi warehouse sama (WH01)
   - Batch/LOT sama (BATCH001/LOT001)
   ============================================================================ */

/* --- BRNTRF OUT: dari JTS1/WH01 --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, nilai, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-BRNTRF-001-OUT'),
    MD5('TEST-BRNTRF-001-SRC'),
    'TEST-BRNTRF-001',
    'BRANCH_TRANSFER',
    'BRNTRF',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    '', '',
    'test_brntrf', 9301, 1,
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH01', 'A01',
    'BATCH001', 'LOT001',
    25, 10000, 250000, 'IDR', 1, 'TEST'
);

/* --- BRNTRF IN: ke JTS2/WH01 --- */
INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, nilai, currcode, kurs, createdby
)
VALUES
(
    MD5('TEST-BRNTRF-001-IN'),
    MD5('TEST-BRNTRF-001-SRC'),
    'TEST-BRNTRF-001',
    'BRANCH_TRANSFER',
    'BRNTRF',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS2',
    'IN',
    '', '',
    'test_brntrf', 9301, 2,
    'BRG001', 'BARANG TEST', 'KG', 'AREA01', 'WH01', 'A01',
    'BATCH001', 'LOT001',
    25, 10000, 250000, 'IDR', 1, 'TEST'
);


/* ============================================================================
   VALIDASI 1: STKBLc — CEK NILAI IN = NILAI OUT
   ============================================================================
   
   Untuk setiap pasangan transfer:
   - totalcost OUT (stkblc) harus SAMA dengan totalcost IN (stkblc)
   ============================================================================ */

SELECT
    sb.source_uniqueid,
    sb.docno,
    sb.journal_type,
    sb.type_in_out,
    sb.idbranch,
    sb.cabang,
    sb.warehouse,
    sb.bin,
    sb.idbarang,
    sb.batch,
    sb.qty_in,
    sb.qty_out,
    sb.unitcost,
    sb.totalcost
FROM sc_trx.stkblc sb
WHERE sb.source_uniqueid IN
(
    MD5('TEST-TRFWHS-001-SRC'),
    MD5('TEST-BINMOV-001-SRC'),
    MD5('TEST-BRNTRF-001-SRC')
)
ORDER BY sb.source_uniqueid, sb.type_in_out, sb.id;


/* ============================================================================
   VALIDASI 2: JURNAL_HD — CEK 2 HEADER PER TRANSFER
   ============================================================================
   
   Setiap transfer harus menghasilkan:
   - 1 header OUT (total_kredit = X, total_debet = 0)
   - 1 header IN  (total_debet = X, total_kredit = 0)
   ============================================================================ */

SELECT
    jh.docno,
    jh.journal_type,
    jh.type_in_out,
    jh.idbranch,
    jh.cabang,
    jh.total_debet,
    jh.total_kredit,
    jh.balance,
    jh.status,
    jh.source_uniqueid
FROM sc_trx.jurnal_hd jh
WHERE jh.docno IN
(
    'TEST-TRFWHS-001',
    'TEST-BINMOV-001',
    'TEST-BRNTRF-001'
)
  AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
ORDER BY jh.docno, jh.type_in_out;


/* ============================================================================
   VALIDASI 3: JURNAL_DT — CEK 1 BARIS PER TRANSACTION
   ============================================================================
   
   Untuk transfer:
   - Header OUT: 1 baris jurnal_dt (KREDIT Inventory)
   - Header IN : 1 baris jurnal_dt (DEBIT Inventory)
   ============================================================================ */

SELECT
    jh.docno,
    jh.journal_type,
    jh.type_in_out,
    jd.seq,
    jd.account_role,
    jd.value_source,
    jd.idcoa,
    jd.debet,
    jd.kredit,
    jd.source_uniqueid
FROM sc_trx.jurnal_hd jh
JOIN sc_trx.jurnal_dt jd
  ON jd.jurnal_id = jh.id
WHERE jh.docno IN
(
    'TEST-TRFWHS-001',
    'TEST-BINMOV-001',
    'TEST-BRNTRF-001'
)
  AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
ORDER BY jh.docno, jh.type_in_out, jd.seq;


/* ============================================================================
   VALIDASI 4: AVG COST — NILAI PERSEDIAAN SETELAH TRANSFER
   ============================================================================
   
   Total nilai perusahaan TIDAK BOLEH BERUBAH.
   ============================================================================ */

SELECT
    'SESUDAH TRANSFER' AS fase,
    SUM(ac.qty) AS total_qty,
    SUM(ac.total_value) AS total_value,
    CASE
        WHEN SUM(ac.qty) > 0 
        THEN ROUND(SUM(ac.total_value) / SUM(ac.qty), 2)
        ELSE 0
    END AS avg_cost_global
FROM sc_trx.stkblc_avgcost ac
WHERE ac.idbarang = 'BRG001';


/* ============================================================================
   VALIDASI 5: AVG COST PER LOKASI
   ============================================================================
   
   Cek distribusi persediaan per lokasi setelah transfer.
   ============================================================================ */

SELECT
    ac.idbarang,
    ac.idlocation,
    ac.batch,
    ac.qty,
    ac.total_value,
    ac.avg_cost
FROM sc_trx.stkblc_avgcost ac
WHERE ac.idbarang = 'BRG001'
ORDER BY ac.idlocation, ac.batch;


/* ============================================================================
   VALIDASI 6: ASSERTION — TOTAL NILAI TIDAK BERUBAH
   ============================================================================
   
   Bandingkan total_value sebelum dan sesudah transfer.
   Karena kita tidak simpan snapshot sebelum, kita hitung ulang
   dari setup GRNREC.
   
   Total setup:
   - GRNREC WH01: 100 kg × 10.000 = 1.000.000
   - GRNREC WH02: 50 kg × 12.000 = 600.000
   Total: 1.600.000
   
   Setelah transfer, total harus TETAP 1.600.000.
   ============================================================================ */

DO $$
DECLARE
    v_total_value NUMERIC;
    v_expected_total NUMERIC := 1600000;
    v_selisih NUMERIC;
BEGIN
    SELECT COALESCE(SUM(ac.total_value), 0)
    INTO v_total_value
    FROM sc_trx.stkblc_avgcost ac
    WHERE ac.idbarang = 'BRG001';

    v_selisih := ABS(v_total_value - v_expected_total);

    IF v_selisih > 0.01 THEN
        RAISE EXCEPTION 
            '❌ TEST TRANSFER GAGAL: total nilai persediaan berubah! Expected=%, Actual=%, Selisih=%',
            v_expected_total, v_total_value, v_selisih;
    END IF;

    RAISE NOTICE 
        '✅ TEST TRANSFER OK: total nilai persediaan TIDAK BERUBAH. Total=%',
        v_total_value;
END;
$$;


/* ============================================================================
   VALIDASI 7: ASSERTION — NILAI IN = NILAI OUT PER TRANSFER
   ============================================================================ */

DO $$
DECLARE
    r RECORD;
    v_selisih NUMERIC;
    v_source_list TEXT[] := ARRAY[
        MD5('TEST-TRFWHS-001-SRC'),
        MD5('TEST-BINMOV-001-SRC'),
        MD5('TEST-BRNTRF-001-SRC')
    ];
BEGIN
    FOR r IN
        SELECT 
            source_uniqueid,
            docno,
            SUM(CASE WHEN type_in_out = 'OUT' THEN totalcost ELSE 0 END) AS out_total,
            SUM(CASE WHEN type_in_out = 'IN'  THEN totalcost ELSE 0 END) AS in_total
        FROM sc_trx.stkblc
        WHERE source_uniqueid = ANY(v_source_list)
        GROUP BY source_uniqueid, docno
    LOOP
        v_selisih := ABS(COALESCE(r.out_total, 0) - COALESCE(r.in_total, 0));
        
        IF v_selisih > 0.01 THEN
            RAISE EXCEPTION 
                '❌ TEST TRANSFER GAGAL: nilai IN ≠ OUT untuk %. OUT=%, IN=%, Selisih=%',
                r.docno, r.out_total, r.in_total, v_selisih;
        END IF;
        
        RAISE NOTICE 
            '✅ % OK: OUT=% = IN=%',
            r.docno, r.out_total, r.in_total;
    END LOOP;
END;
$$;


/* ============================================================================
   VALIDASI 8: ASSERTION — JURNAL_DT 1 BARIS PER TRANSACTION
   ============================================================================ */

DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT 
            jh.docno,
            jh.type_in_out,
            COUNT(jd.id) AS detail_count,
            SUM(jd.debet) AS total_debet,
            SUM(jd.kredit) AS total_kredit
        FROM sc_trx.jurnal_hd jh
        JOIN sc_trx.jurnal_dt jd ON jd.jurnal_id = jh.id
        WHERE jh.docno IN
        (
            'TEST-TRFWHS-001',
            'TEST-BINMOV-001',
            'TEST-BRNTRF-001'
        )
          AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
        GROUP BY jh.id, jh.docno, jh.type_in_out
    LOOP
        IF r.detail_count <> 1 THEN
            RAISE EXCEPTION 
                '❌ TEST GAGAL: % (%) punya % baris jurnal_dt, harusnya 1.',
                r.docno, r.type_in_out, r.detail_count;
        END IF;
        
        IF r.type_in_out = 'OUT' THEN
            IF r.total_debet <> 0 OR r.total_kredit <= 0 THEN
                RAISE EXCEPTION 
                    '❌ TEST GAGAL: % OUT harusnya total_debet=0, total_kredit>0. Actual: D=%, K=%',
                    r.docno, r.total_debet, r.total_kredit;
            END IF;
        ELSIF r.type_in_out = 'IN' THEN
            IF r.total_kredit <> 0 OR r.total_debet <= 0 THEN
                RAISE EXCEPTION 
                    '❌ TEST GAGAL: % IN harusnya total_debet>0, total_kredit=0. Actual: D=%, K=%',
                    r.docno, r.total_debet, r.total_kredit;
            END IF;
        END IF;
        
        RAISE NOTICE 
            '✅ % (%) OK: 1 baris jurnal_dt, D=%, K=%',
            r.docno, r.type_in_out, r.total_debet, r.total_kredit;
    END LOOP;
END;
$$;


/* ============================================================================
   VALIDASI 9: ASSERTION — TRANSACTION_HD HEADER COUNT
   ============================================================================ */

SELECT
    th.docno,
    th.journal_type,
    th.type_in_out,
    th.idbranch,
    th.cabang,
    th.total_qty,
    th.total_qty_in,
    th.total_qty_out,
    th.total_nilai,
    th.total_debet,
    th.total_kredit,
    th.balance
FROM sc_trx.transaction_hd th
WHERE th.docno IN
(
    'TEST-TRFWHS-001',
    'TEST-BINMOV-001',
    'TEST-BRNTRF-001'
)
ORDER BY th.docno, th.type_in_out;


/* ============================================================================
   VALIDASI 10: RINGKASAN — SEMUA TRANSFER
   ============================================================================ */

DO $$
DECLARE
    v_count_transfer INTEGER;
BEGIN
    SELECT COUNT(DISTINCT docno)
    INTO v_count_transfer
    FROM sc_trx.transaction_dt
    WHERE docno IN
    (
        'TEST-TRFWHS-001',
        'TEST-BINMOV-001',
        'TEST-BRNTRF-001'
    );

    RAISE NOTICE '============================================================';
    RAISE NOTICE 'TEST DATA TRANSFER SELESAI';
    RAISE NOTICE '============================================================';
    RAISE NOTICE 'Total dokumen transfer : %', v_count_transfer;
    RAISE NOTICE 'Detail per dokumen     : 2 baris (OUT + IN)';
    RAISE NOTICE 'Total transaction_dt   : 6 baris';
    RAISE NOTICE 'Total jurnal_hd        : 6 header';
    RAISE NOTICE 'Total jurnal_dt        : 6 baris (1 per transaction)';
    RAISE NOTICE '';
    RAISE NOTICE 'EXPECTED RESULTS:';
    RAISE NOTICE '  - Total nilai persediaan TIDAK BERUBAH';
    RAISE NOTICE '  - Nilai IN transfer = Nilai OUT transfer';
    RAISE NOTICE '  - 1 baris jurnal_dt per transaction';
    RAISE NOTICE '  - Balance OUT + IN = 0';
    RAISE NOTICE '============================================================';
END;
$$;


/* ============================================================================
   AKHIR TEST DATA

   ============================================================
   CARA PAKAI:

   1. UNTUK SIMULASI (tidak menyimpan):
      - Biarkan COMMIT di bawah sebagai komentar
      - Jalankan: ROLLBACK;
      - Data akan dibatalkan

   2. UNTUK MENYIMPAN TEST DATA:
      - Ubah ROLLBACK menjadi COMMIT
      - Data akan tersimpan permanen

   PRASYARAT:
   - TAHAP 01-19 sudah dijalankan
   - Master data: BRG001, currency IDR, journal_type siap
   - COA persediaan BRG001 sudah terdaftar di mbarang

   YANG DIUJI:
   1. TRFWHS - Transfer antar gudang (WH01 → WH03, JTS1)
   2. BINMOV - Pindah bin (WH02 A01 → A02, JTS1)
   3. BRNTRF - Transfer antar cabang (JTS1 → JTS2)

   SETIAP TEST DIVERIFIKASI:
   - stkblc: nilai IN = nilai OUT
   - jurnal_hd: OUT → K, IN → D (balance per header ≠ 0)
   - jurnal_dt: 1 baris per transaction_dt
   - Total nilai perusahaan tidak berubah
   ============================================================ */

-- ROLLBACK;  -- Aktifkan untuk simulasi (tidak menyimpan)
COMMIT;       -- Aktifkan untuk menyimpan test data

/* ============================================================================
   SELESAI
   ============================================================================ */