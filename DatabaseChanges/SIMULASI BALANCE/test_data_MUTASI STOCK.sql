BEGIN;

/* ============================================================
   1. VALIDASI AWAL
   ============================================================ */

DO $$
BEGIN
    IF NULLIF(BTRIM('01.01'), '') IS NULL
       OR NULLIF(BTRIM('16401'), '') IS NULL THEN
        RAISE EXCEPTION 'Branch atau warehouse belum diisi';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.mbarang
        WHERE BTRIM(idbarang::TEXT) = '01040201000017'
    ) THEN
        RAISE EXCEPTION 'Barang CRITERION 60 tidak ditemukan';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_trx.transaction_dt
        WHERE source_table = 'SIM_EXCEL_STOCK_202608'
           OR docno IN (
                'OPENST2608-0017',
                'PBL/2608/PA0206','PBL/2608/PA0209',
                'PBL/2608/PA0210','PBL/2608/PA0213',
                'PBL/2608/PA0223','PBL/2608/PA0290',
                'PBL/2608/PA0291','PBL/2608/PA0308',
                'PBL/2608/PA0316','PBL/2608/PA0112',
                'PBL/2608/PA0321','PBL/2608/PA0113',
                'PBL/2608/PA0368','PBL/2608/PA0386',
                'PBL/2608/PA0387','LPI/2608/PA0018',
                'PBL/2608/PA0392','PBL/2608/PA0393',
                'PBL/2608/PA0250','PBL/2608/PA0399',
                'PBL/2608/PA0251','PBL/2608/PA0465',
                'PBL/2608/PA0446','PBL/2608/PA0466',
                'PBL/2608/PA0468','PBL/2608/PA0356',
                'PBL/2608/PA0469','PBL/2608/PA0357',
                'PBL/2608/PA0474','PBL/2608/PA0485',
                'PBL/2608/PA0443','PBL/2608/PA0445',
                'ADR/2608/PA03FA','ADR/2608/PA03FB'
           )
    ) THEN
        RAISE EXCEPTION
            'Data simulasi sudah ada. ROLLBACK simulasi sebelumnya atau bersihkan data simulasi terlebih dahulu.';
    END IF;
END $$;


/* ============================================================
   2. INSERT DATA EXCEL KE TRANSACTION_DT
   Nilai transaksi dipertahankan sesuai Excel.
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type,
    line_no, docdate, idbranch, cabang, type_in_out,
    source_table, source_line_id, idbarang, namabarang,
    idunit, warehouse, qty, harga, bruto, nilai, dpp,
    pajak, total, idtax, isinclusive, currcode, kurs,
    keterangan, createdby, createddate
)
SELECT
    'SIM2608-CRITERION60-' || LPAD(d.line_no::TEXT, 3, '0'),
    'SIM-EXCEL-202608-' || LPAD(d.line_no::TEXT, 3, '0'),
    d.docno,
    d.doctype,
    d.journal_type,
    d.line_no,
    d.docdate,
    '01.01',
    'PT JATIM TAMAN STEEL MFG',
    d.type_in_out,
    'SIM_EXCEL_STOCK_202608',
    d.line_no,
    '01040201000017',
    'CRITERION 60',
    'KG',
    '16401',
    d.qty,
    CASE
        WHEN d.qty <> 0 THEN ROUND(d.nilai / d.qty, 2)
        ELSE 0
    END,
    d.nilai,
    d.nilai,
    d.nilai,
    0,
    d.nilai,
    'NON',
    'NO',
    'IDR',
    1,
    d.keterangan,
    'SIMULASI',
    CURRENT_TIMESTAMP
FROM (
    VALUES
    (1, DATE '2026-08-01', 'OPENST2608-0017', 'OPENING',
     'OPENST', 'IN', 43450::NUMERIC, 749607658.18::NUMERIC, 'SALDO AWAL'),

    (2, DATE '2026-08-01', 'PBL/2608/PA0206', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.8'),

    (3, DATE '2026-08-01', 'PBL/2608/PA0209', 'STOCK',
     'STKOUT', 'OUT', 275::NUMERIC, 4744352.28::NUMERIC, 'Pemakaian Ladle Service LD No.5'),

    (4, DATE '2026-08-03', 'PBL/2608/PA0210', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.11'),

    (5, DATE '2026-08-03', 'PBL/2608/PA0213', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.2'),

    (6, DATE '2026-08-03', 'PBL/2608/PA0223', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.6'),

    (7, DATE '2026-08-05', 'PBL/2608/PA0290', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.30'),

    (8, DATE '2026-08-06', 'PBL/2608/PA0291', 'STOCK',
     'STKOUT', 'OUT', 225::NUMERIC, 3881742.77::NUMERIC, 'Pemakaian Ladle Service LD No.1'),

    (9, DATE '2026-08-06', 'PBL/2608/PA0308', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.3'),

    (10, DATE '2026-08-07', 'PBL/2608/PA0316', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.13'),

    (11, DATE '2026-08-08', 'PBL/2608/PA0112', 'STOCK',
     'STKOUT', 'OUT', 125::NUMERIC, 2156523.76::NUMERIC, 'Pemakaian Ladle Proses Tgl 02 s/d 08 Agustus 2026'),

    (12, DATE '2026-08-08', 'PBL/2608/PA0321', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.7'),

    (13, DATE '2026-08-08', 'PBL/2608/PA0113', 'STOCK',
     'STKOUT', 'OUT', 850::NUMERIC, 14664361.59::NUMERIC, 'Pemakaian Tundish Proses Tgl 03 s/d 08 Agustus 2026'),

    (14, DATE '2026-08-10', 'PBL/2608/PA0368', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.8'),

    (15, DATE '2026-08-11', 'PBL/2608/PA0386', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4313047.53::NUMERIC, 'Pemakaian Ladle Service LD No.11'),

    (16, DATE '2026-08-12', 'PBL/2608/PA0387', 'STOCK',
     'STKOUT', 'OUT', 300::NUMERIC, 5175657.03::NUMERIC, 'Pemakaian Ladle Service LD No.2'),

    (17, DATE '2026-08-14', 'LPI/2608/PA0018', 'RECEIPT',
     'GRNREC', 'IN', 24000::NUMERIC, 412178400.00::NUMERIC, 'VESUVIUS MALAYSIA SDN BHD'),

    (18, DATE '2026-08-14', 'PBL/2608/PA0392', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.5'),

    (19, DATE '2026-08-14', 'PBL/2608/PA0393', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.6'),

    (20, DATE '2026-08-15', 'PBL/2608/PA0250', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Proses Tgl 10 s/d15 Agustus 2026'),

    (21, DATE '2026-08-15', 'PBL/2608/PA0399', 'STOCK',
     'STKOUT', 'OUT', 275::NUMERIC, 4736194.07::NUMERIC, 'Pemakaian Ladle Service LD No.1'),

    (22, DATE '2026-08-15', 'PBL/2608/PA0251', 'STOCK',
     'STKOUT', 'OUT', 325::NUMERIC, 5597320.27::NUMERIC, 'Pemakaian Tundish Proses Tgl 10 s/d 15 Agustus 2026'),

    (23, DATE '2026-08-19', 'PBL/2608/PA0465', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.7'),

    (24, DATE '2026-08-19', 'PBL/2608/PA0446', 'STOCK',
     'STKOUT', 'OUT', 275::NUMERIC, 4736194.07::NUMERIC, 'Pemakaian Ladle Service LD No.30'),

    (25, DATE '2026-08-20', 'PBL/2608/PA0466', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.3'),

    (26, DATE '2026-08-21', 'PBL/2608/PA0468', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.8'),

    (27, DATE '2026-08-22', 'PBL/2608/PA0356', 'STOCK',
     'STKOUT', 'OUT', 25::NUMERIC, 430563.10::NUMERIC, 'Pemakaian Ladle Proses Tgl 18 s/d 22 Agustus 2026'),

    (28, DATE '2026-08-22', 'PBL/2608/PA0469', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.11'),

    (29, DATE '2026-08-22', 'PBL/2608/PA0357', 'STOCK',
     'STKOUT', 'OUT', 800::NUMERIC, 13778019.12::NUMERIC, 'Pemakaian Tundish Proses Tgl 18 s/d 22 Agustus 2026'),

    (30, DATE '2026-08-26', 'PBL/2608/PA0474', 'STOCK',
     'STKOUT', 'OUT', 250::NUMERIC, 4305630.98::NUMERIC, 'Pemakaian Ladle Service LD No.13'),

    (31, DATE '2026-08-27', 'PBL/2608/PA0485', 'STOCK',
     'STKOUT', 'OUT', 1300::NUMERIC, 22389281.07::NUMERIC, 'Pemakaian Cover Tundish No.16'),

    (32, DATE '2026-08-29', 'PBL/2608/PA0443', 'STOCK',
     'STKOUT', 'OUT', 100::NUMERIC, 1722252.39::NUMERIC, 'Pemakaian Ladle Proses Tgl 26 s/ 29 Agustus 2026'),

    (33, DATE '2026-08-29', 'PBL/2608/PA0445', 'STOCK',
     'STKOUT', 'OUT', 625::NUMERIC, 10764077.44::NUMERIC, 'Pemakaian Tundish Proses Tgl 26 s/d29 Agustus 2026'),

    (34, DATE '2026-08-30', 'ADR/2608/PA03FA', 'ADJUSTMENT',
     'ADJOUT', 'OUT', 0.10::NUMERIC, 1722.25::NUMERIC, 'JTS 1 - PEROLEHAN DARI BIAYA IMPOR, LC ATAU PENYESUAIAN HARGA'),

    (35, DATE '2026-08-31', 'ADR/2608/PA03FB', 'ADJUSTMENT',
     'ADJINX', 'IN', 0.10::NUMERIC, 3078840.06::NUMERIC, 'JTS 1 - PEROLEHAN DARI BIAYA IMPOR, LC ATAU PENYESUAIAN HARGA')
) AS d(line_no, docdate, docno, doctype, journal_type, type_in_out, qty, nilai, keterangan)
ORDER BY d.docdate, d.line_no;


/* ============================================================
   3. VALIDASI HASIL INSERT
   Target: 35 baris
   ============================================================ */

SELECT
    COUNT(*) AS jumlah_baris,
    COUNT(DISTINCT docno) AS jumlah_docno,
    SUM(CASE WHEN type_in_out = 'IN' THEN qty ELSE 0 END) AS total_qty_in,
    SUM(CASE WHEN type_in_out = 'OUT' THEN qty ELSE 0 END) AS total_qty_out,
    SUM(CASE WHEN type_in_out = 'IN' THEN nilai ELSE 0 END) AS total_nilai_in_excel,
    SUM(CASE WHEN type_in_out = 'OUT' THEN nilai ELSE 0 END) AS total_nilai_out_excel
FROM sc_trx.transaction_dt
WHERE source_table = 'SIM_EXCEL_STOCK_202608';


/* Tampilkan data dengan urutan sumber Excel */

SELECT
    line_no,
    docdate,
    docno,
    journal_type,
    type_in_out,
    qty,
    harga,
    nilai,
    idbarang,
    warehouse,
    idbranch
FROM sc_trx.transaction_dt
WHERE source_table = 'SIM_EXCEL_STOCK_202608'
ORDER BY docdate, line_no;


/* ============================================================
   4. CEK APAKAH TRANSACTION_DT MEMBENTUK STKBLC
   ============================================================ */

SELECT
    t.line_no,
    t.docdate,
    t.docno,
    t.journal_type,
    t.type_in_out,
    t.qty AS qty_excel,
    t.harga AS harga_excel,
    t.nilai AS nilai_excel,
    s.id AS stkblc_id,
    s.qty_in,
    s.qty_out,
    s.pricelst_in,
    s.pricelst_out,
    s.unitcost,
    s.totalcost AS nilai_stkblc,
    CASE
        WHEN s.id IS NULL THEN 'STKBLC BELUM TERBENTUK'
        WHEN t.type_in_out = 'IN'
             AND COALESCE(s.qty_in, 0) <> t.qty
            THEN 'QTY IN BERBEDA'
        WHEN t.type_in_out = 'OUT'
             AND COALESCE(s.qty_out, 0) <> t.qty
            THEN 'QTY OUT BERBEDA'
        ELSE 'PERIKSA NILAI DAN COSTING'
    END AS hasil_pemeriksaan
FROM sc_trx.transaction_dt t
LEFT JOIN sc_trx.stkblc s
    ON BTRIM(s.docno::TEXT) = BTRIM(t.docno::TEXT)
   AND BTRIM(s.idbarang::TEXT) = BTRIM(t.idbarang::TEXT)
   AND BTRIM(s.idlocation::TEXT) = BTRIM(t.warehouse::TEXT)
WHERE t.source_table = 'SIM_EXCEL_STOCK_202608'
ORDER BY t.docdate, t.line_no, s.id;


/* ============================================================
   5. JALANKAN RECALCULATION AVERAGE COST
   Pastikan data STKBLC sudah terbentuk sebelum menjalankan ini.
   ============================================================ */

SELECT sc_trx.fn_recalculate_avgcost_group(
    '01040201000017',
    '16401',
    ''
);


/* ============================================================
   6. HASIL AKHIR AVERAGE COST
   ============================================================ */

SELECT
    idbarang,
    idlocation,
    batch,
    qty,
    total_value,
    avg_cost
FROM sc_trx.stkblc_avgcost
WHERE BTRIM(idbarang::TEXT) = '01040201000017'
  AND BTRIM(idlocation::TEXT) = '16401'
  AND BTRIM(COALESCE(batch, '')) = '';


/* ============================================================
   7. SELESAI TEST
   ============================================================ */
select * from sc_trx.stkblc
/*
Periksa seluruh hasil sebelum memutuskan.

ROLLBACK; -- batalkan seluruh perubahan simulasi
commit;
Jangan COMMIT sebelum memastikan trigger, STKBLC,
dan hasil average cost sudah sesuai.
*/
