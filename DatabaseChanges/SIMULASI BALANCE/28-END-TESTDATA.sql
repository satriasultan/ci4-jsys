
/* 
   1 JOURNAL_TYPE
      =
   1 TEST INSERT

   sehingga misalnya:

   PURCHS  -> INSERT
   PURRET  -> INSERT
   GRNREC  -> INSERT
   GRNRET  -> INSERT

   SALESX  -> INSERT
   SALRET  -> INSERT

   STKINX  -> INSERT
   STKOUT  -> INSERT
   ADJINX  -> INSERT
   ADJOUT  -> INSERT

   WOISSU  -> INSERT
   WORETN  -> INSERT
   WORECV  -> INSERT
   WORETR  -> INSERT

   dst...

   Untuk INOUT:
       1 source_uniqueid
       + 2 transaction_dt
       = OUT + IN
*/


/* ============================================================
   TEST DATA sc_trx.transaction_dt
   BERDASARKAN sc_mst.journal_type

   TUJUAN:
   ------------------------------------------------------------
   1. Menguji seluruh GROUP / MODULE journal_type.
   2. INSERT langsung ke sc_trx.transaction_dt.
   3. Menguji arah IN / OUT / INOUT.
   4. Menguji batch + lot.
   5. Menguji source_uniqueid.
   6. Menguji currency dan tax.
   7. Data menggunakan source_uniqueid berbeda.
   8. Bisa dijalankan dengan BEGIN / ROLLBACK
      sehingga tidak merusak data produksi.

   CATATAN:
   ------------------------------------------------------------
   uniqueid           = identitas transaksi detail
   source_uniqueid    = identitas source document

   source_uniqueid TIDAK UNIQUE karena satu source document
   dapat menghasilkan beberapa transaksi.
   ============================================================ */


/* ============================================================
   TEST ALL - FIXED
   ------------------------------------------------------------
   FIXES:
   1. Semua uniqueid dan source_uniqueid test menggunakan MD5().
   2. GRNREC BATCH001 dinaikkan menjadi 200 KG agar seluruh
      transaksi OUT pada batch tersebut tidak menghasilkan negative stock.
   3. REWORK OUT menggunakan BATCH001/LOT001 yang sudah mempunyai stock.
   4. SBISSU/SBRETN menggunakan BATCH001/LOT001 yang sudah mempunyai avg cost.
   5. Blok patch SBISSU lama dihapus; test cukup satu kali dan satu BEGIN/ROLLBACK.
   ============================================================ */

BEGIN;

/* ============================================================
   CLEANUP TEST SEBELUM INSERT
   ------------------------------------------------------------
   Hapus test lama melalui transaction_dt agar trigger reverse
   melakukan cleanup stock/accounting secara normal.

   Legacy test dapat memakai uniqueid plain text, sedangkan versi
   fixed memakai MD5(). Keduanya dibersihkan.
   Penghapusan dilakukan dari id terbesar ke terkecil agar costing
   tidak membaca transaksi lanjutan sebagai stock yang masih ada.
   ============================================================ */

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
                'TX-JOURNAL-UPD-001', MD5('TX-JOURNAL-UPD-001'),
                'TX-JV-PERKIRAAN-001', MD5('TX-JV-PERKIRAAN-001'),
                'TX-JV-PERKIRAAN-002', MD5('TX-JV-PERKIRAAN-002'),
                'TX-NDKAPD-001', MD5('TX-NDKAPD-001'),
                'TX-NDKAPK-001', MD5('TX-NDKAPK-001'),
                'TX-NDKARD-001', MD5('TX-NDKARD-001'),
                'TX-NDKARK-001', MD5('TX-NDKARK-001'),
                'TX-GRNREC-001', MD5('TX-GRNREC-001'),
                'TX-PURRET-001', MD5('TX-PURRET-001'),
                'TX-SALESX-001', MD5('TX-SALESX-001'),
                'TX-SALRET-001', MD5('TX-SALRET-001'),
                'TX-STKINX-001', MD5('TX-STKINX-001'),
                'TX-STKOUT-001', MD5('TX-STKOUT-001'),
                'TX-ADJINX-001', MD5('TX-ADJINX-001'),
                'TX-ADJOUT-001', MD5('TX-ADJOUT-001'),
                'TX-OPENST-001', MD5('TX-OPENST-001'),
                'TX-OPNSTR-001', MD5('TX-OPNSTR-001'),
                'TX-WOISSU-001', MD5('TX-WOISSU-001'),
                'TX-WORETN-001', MD5('TX-WORETN-001'),
                'TX-WORECV-001', MD5('TX-WORECV-001'),
                'TX-WORETR-001', MD5('TX-WORETR-001'),
                'TX-SCRAPP-001', MD5('TX-SCRAPP-001'),
                'TX-SCRREV-001', MD5('TX-SCRREV-001'),
                'TX-REWORK-001-OUT', MD5('TX-REWORK-001-OUT'),
                'TX-REWORK-001-IN', MD5('TX-REWORK-001-IN'),
                'TX-BYPROD-001', MD5('TX-BYPROD-001'),
                'TX-COPROD-001', MD5('TX-COPROD-001'),
                'TX-SBISSU-001', MD5('TX-SBISSU-001'),
                'TX-SBRETN-001', MD5('TX-SBRETN-001'),
                'TX-QHOLDX-001-OUT', MD5('TX-QHOLDX-001-OUT'),
                'TX-QHOLDX-001-IN', MD5('TX-QHOLDX-001-IN'),
                'TX-QRELSX-001-OUT', MD5('TX-QRELSX-001-OUT'),
                'TX-QRELSX-001-IN', MD5('TX-QRELSX-001-IN'),
                'TX-BRNTRF-TEST-OUT', MD5('TX-BRNTRF-TEST-OUT'),
                'TX-BRNTRF-TEST-IN', MD5('TX-BRNTRF-TEST-IN')
            ]::TEXT[]
        )
        ORDER BY id DESC
    LOOP
        DELETE FROM sc_trx.transaction_dt
        WHERE id = r.id;
    END LOOP;
END;
$$;


/* ============================================================
   GROUP 01
   PURCHASE
   ============================================================

   GRNREC
   GOODS RECEIPT
   IN

   Simulasi:
   Barang diterima dari supplier.

   Qty       = 200 KG
   Harga     = 10.000
   Nilai     = 2.000.000
   PPN       = 220.000
   Total     = 2.220.000

   PPN menggunakan INCLUDE = NO
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    source_table,
    source_id,
    source_line_id,
    kdsupplier,
    nsupplier,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-GRNREC-001'),
    MD5('GRN-TEST-001-DTL-001'),
    'GRN-TEST-001',
    'GRN',
    'GRNREC',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'PO-TEST-001',
    'PO',
    'test_grn',
    1001,
    1,
    'SUP001',
    'SUPPLIER TEST',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    200,
    10000,
    2000000,
    0,
    2000000,
    2000000,
    220000,
    2220000,
    'PPN11',
    'NO',
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 01
   PURCHASE RETURN

   PURRET
   OUT

   Barang yang sudah diterima dikembalikan ke supplier.
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    source_table,
    source_id,
    source_line_id,
    kdsupplier,
    nsupplier,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-PURRET-001'),
    MD5('PURRET-TEST-001-DTL-001'),
    'PURRET-TEST-001',
    'PURCHASE_RETURN',
    'PURRET',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'GRN-TEST-001',
    'GRN',
    'test_purchase_return',
    2001,
    1,
    'SUP001',
    'SUPPLIER TEST',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    20,
    10000,
    200000,
    0,
    200000,
    200000,
    22000,
    222000,
    'PPN11',
    'NO',
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 02
   SALES

   SALESX
   OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    source_table,
    source_id,
    source_line_id,
    kdcustomer,
    ncustomer,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-SALESX-001'),
    MD5('SALES-TEST-001-DTL-001'),
    'SALES-TEST-001',
    'SALES',
    'SALESX',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    '',
    '',
    'test_sales',
    3001,
    1,
    'CUS001',
    'CUSTOMER TEST',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    10,
    15000,
    150000,
    0,
    150000,
    150000,
    16500,
    166500,
    'PPN11',
    'NO',
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 02
   SALES RETURN

   SALRET
   IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    source_table,
    source_id,
    source_line_id,
    kdcustomer,
    ncustomer,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-SALRET-001'),
    MD5('SALRET-TEST-001-DTL-001'),
    'SALRET-TEST-001',
    'SALES_RETURN',
    'SALRET',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'SALES-TEST-001',
    'SALES',
    'test_sales_return',
    4001,
    1,
    'CUS001',
    'CUSTOMER TEST',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    2,
    15000,
    30000,
    0,
    30000,
    30000,
    3300,
    33300,
    'PPN11',
    'NO',
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 03
   INVENTORY
   ============================================================

   STKINX
   STOCK IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-STKINX-001'),
    MD5('STKIN-TEST-001-DTL-001'),
    'STKIN-TEST-001',
    'STOCK_ADJUSTMENT',
    'STKINX',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH002',
    'LOT002',
    50,
    10000,
    500000,
    500000,
    500000,
    0,
    500000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   INVENTORY OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-STKOUT-001'),
    MD5('STKOUT-TEST-001-DTL-001'),
    'STKOUT-TEST-001',
    'STOCK_ADJUSTMENT',
    'STKOUT',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH002',
    'LOT002',
    10,
    10000,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   ADJUSTMENT IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-ADJINX-001'),
    MD5('ADJIN-TEST-001-DTL-001'),
    'ADJIN-TEST-001',
    'ADJUSTMENT',
    'ADJINX',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH003',
    'LOT003',
    5,
    10000,
    50000,
    50000,
    50000,
    0,
    50000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   ADJUSTMENT OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-ADJOUT-001'),
    MD5('ADJOUT-TEST-001-DTL-001'),
    'ADJOUT-TEST-001',
    'ADJUSTMENT',
    'ADJOUT',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH003',
    'LOT003',
    3,
    10000,
    30000,
    30000,
    30000,
    0,
    30000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 04
   OPENING STOCK

   OPENST
   IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-OPENST-001'),
    MD5('OPENST-TEST-001-DTL-001'),
    'OPENST-TEST-001',
    'OPENING_STOCK',
    'OPENST',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'BRG002',
    'BARANG OPENING',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCHOPEN',
    'LOTOPEN',
    100,
    9000,
    900000,
    900000,
    900000,
    0,
    900000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   OPENING STOCK REVERSE

   OPNSTR
   OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-OPNSTR-001'),
    MD5('OPNSTR-TEST-001-DTL-001'),
    'OPNSTR-TEST-001',
    'OPENING_STOCK_REV',
    'OPNSTR',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'OPENST-TEST-001',
    'OPENING_STOCK',
    'BRG002',
    'BARANG OPENING',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCHOPEN',
    'LOTOPEN',
    20,
    9000,
    180000,
    180000,
    180000,
    0,
    180000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 05
   MANUFACTURING
   ============================================================

   WOISSU
   MATERIAL ISSUE
   OUT

   Material keluar dari warehouse menuju produksi.
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-WOISSU-001'),
    MD5('WO-TEST-001-MAT-001'),
    'WO-TEST-001',
    'WORK_ORDER',
    'WOISSU',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'WO-TEST-001',
    'WORK_ORDER',
    'BRG001',
    'BAHAN BAKU TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    50,
    10000,
    500000,
    500000,
    500000,
    0,
    500000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   MATERIAL RETURN

   WORETN
   IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-WORETN-001'),
    MD5('WO-TEST-001-RET-001'),
    'WO-TEST-001-RET',
    'WORK_ORDER',
    'WORETN',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'WO-TEST-001',
    'WORK_ORDER',
    'BRG001',
    'BAHAN BAKU TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    5,
    10000,
    50000,
    50000,
    50000,
    0,
    50000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   PRODUCTION RECEIPT

   WORECV
   IN

   Produk jadi masuk ke warehouse.
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-WORECV-001'),
    MD5('WO-TEST-001-RECV-001'),
    'WO-TEST-001-RECV',
    'WORK_ORDER',
    'WORECV',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'WO-TEST-001',
    'WORK_ORDER',
    'FG001',
    'FINISHED GOOD TEST',
    'KG',
    'AREA01',
    'WH01',
    'FG01',
    'BATCHFG001',
    'LOT-FG-001',
    40,
    15000,
    600000,
    600000,
    600000,
    0,
    600000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   PRODUCTION RETURN

   WORETR
   OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-WORETR-001'),
    MD5('WO-TEST-001-RETR-001'),
    'WO-TEST-001-RETR',
    'WORK_ORDER',
    'WORETR',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'WO-TEST-001-RECV',
    'WORK_ORDER',
    'FG001',
    'FINISHED GOOD TEST',
    'KG',
    'AREA01',
    'WH01',
    'FG01',
    'BATCHFG001',
    'LOT-FG-001',
    2,
    15000,
    30000,
    30000,
    30000,
    0,
    30000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 06
   SCRAP

   SCRAPP
   OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-SCRAPP-001'),
    MD5('SCRAP-TEST-001-DTL-001'),
    'SCRAP-TEST-001',
    'SCRAP',
    'SCRAPP',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'WO-TEST-001',
    'WORK_ORDER',
    'BRG001',
    'BAHAN BAKU TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    3,
    10000,
    30000,
    30000,
    30000,
    0,
    30000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   SCRAP REVERSE

   SCRREV
   IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-SCRREV-001'),
    MD5('SCRREV-TEST-001-DTL-001'),
    'SCRREV-TEST-001',
    'SCRAP_REVERSE',
    'SCRREV',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'SCRAP-TEST-001',
    'SCRAP',
    'BRG001',
    'BAHAN BAKU TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    1,
    10000,
    10000,
    10000,
    10000,
    0,
    10000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 07
   REWORK

   REWORK = INOUT

   Karena INOUT:
       LINE 1 = OUT
       LINE 2 = IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-REWORK-001-OUT'),
    MD5('REWORK-TEST-001'),
    'REWORK-TEST-001',
    'REWORK',
    'REWORK',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'WO-TEST-001',
    'WORK_ORDER',
    'BRG001',
    'BARANG REWORK',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    10,
    10000,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-REWORK-001-IN'),
    MD5('REWORK-TEST-001'),
    'REWORK-TEST-001',
    'REWORK',
    'REWORK',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'WO-TEST-001',
    'WORK_ORDER',
    'FG001',
    'HASIL REWORK',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCHRW002',
    'LOT-RW-002',
    8,
    12500,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 08
   BY PRODUCT / CO PRODUCT

   BYPROD
   IN

   COPROD
   IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-BYPROD-001'),
    MD5('BYPROD-TEST-001'),
    'BYPROD-TEST-001',
    'BY_PRODUCT',
    'BYPROD',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'WO-TEST-001',
    'WORK_ORDER',
    'BY001',
    'BY PRODUCT TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCHBY001',
    'LOT-BY-001',
    5,
    5000,
    25000,
    25000,
    25000,
    0,
    25000,
    'IDR',
    1,
    'TEST'
);


INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-COPROD-001'),
    MD5('COPROD-TEST-001'),
    'COPROD-TEST-001',
    'CO_PRODUCT',
    'COPROD',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'WO-TEST-001',
    'WORK_ORDER',
    'CO001',
    'CO PRODUCT TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCHCO001',
    'LOT-CO-001',
    3,
    7000,
    21000,
    21000,
    21000,
    0,
    21000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 09
   SUBCONTRACT

   SBISSU
   OUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    kdsupplier,
    nsupplier,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-SBISSU-001'),
    MD5('SB-TEST-001-ISSU'),
    'SB-TEST-001',
    'SUBCONTRACT',
    'SBISSU',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'PO-SUB-001',
    'PO_SUBCONTRACT',
    'SUP002',
    'SUBCONTRACTOR TEST',
    'BRG001',
    'BAHAN SUBCONTRACT',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    30,
    10000,
    300000,
    300000,
    300000,
    0,
    300000,
    '',
    'NO',
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   SUBCONTRACT RETURN

   SBRETN
   IN
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    kdsupplier,
    nsupplier,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-SBRETN-001'),
    MD5('SB-TEST-001-RETN'),
    'SB-TEST-001-RETN',
    'SUBCONTRACT',
    'SBRETN',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'SB-TEST-001',
    'SUBCONTRACT',
    'SUP002',
    'SUBCONTRACTOR TEST',
    'BRG001',
    'BAHAN SUBCONTRACT',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    5,
    10000,
    50000,
    50000,
    50000,
    0,
    50000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 10
   QUALITY

   QHOLDX
   INOUT

   Untuk INOUT wajib ada OUT dan IN.
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-QHOLDX-001-OUT'),
    MD5('QHOLD-TEST-001'),
    'QHOLD-TEST-001',
    'QUALITY_HOLD',
    'QHOLDX',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'GRN-TEST-001',
    'GRN',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    10,
    10000,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-QHOLDX-001-IN'),
    MD5('QHOLD-TEST-001'),
    'QHOLD-TEST-001',
    'QUALITY_HOLD',
    'QHOLDX',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'GRN-TEST-001',
    'GRN',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH-QC',
    'QC01',
    'BATCH001',
    'LOT001',
    10,
    10000,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   QUALITY RELEASE
   QRELSX
   INOUT
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-QRELSX-001-OUT'),
    MD5('QRELS-TEST-001'),
    'QRELS-TEST-001',
    'QUALITY_RELEASE',
    'QRELSX',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'QHOLD-TEST-001',
    'QUALITY_HOLD',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH-QC',
    'QC01',
    'BATCH001',
    'LOT001',
    10,
    10000,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    nilai,
    dpp,
    pajak,
    total,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-QRELSX-001-IN'),
    MD5('QRELS-TEST-001'),
    'QRELS-TEST-001',
    'QUALITY_RELEASE',
    'QRELSX',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'QHOLD-TEST-001',
    'QUALITY_HOLD',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    10,
    10000,
    100000,
    100000,
    100000,
    0,
    100000,
    'IDR',
    1,
    'TEST'
);


/* ============================================================
   GROUP 11
   BRANCH TRANSFER

   BRNTRF
   INOUT

   OUT dari JTS1
   IN ke JTS2

   Ini sama dengan konsep transfer pada baseline.
   ============================================================ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    nilai,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-BRNTRF-TEST-OUT'),
    MD5('BRNTRF-TEST-001-DTL-001'),
    'BRNTRF-TEST-001',
    'TRANSFER',
    'BRNTRF',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    25,
    10000,
    250000,
    'IDR',
    1,
    'TEST'
);


INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    nilai,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-BRNTRF-TEST-IN'),
    MD5('BRNTRF-TEST-001-DTL-001'),
    'BRNTRF-TEST-001',
    'TRANSFER',
    'BRNTRF',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS2',
    'IN',
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCH001',
    'LOT001',
    25,
    10000,
    250000,
    'IDR',
    1,
    'TEST'
);



/* ============================================================
   TAHAP 28
   END TEST DATA V9 - JVGENL / NDK FULL FIX - PEMBARUAN JURNAL + MANUAL ACCOUNTING V4
   ============================================================

   TUJUAN:
   ------------------------------------------------------------
   1. Menguji UPDATE transaction_dt yang sudah pernah INSERT.
   2. Memastikan reverse transaksi lama + post transaksi baru.
   3. Menguji perubahan ITEM, QTY, HARGA, dan NILAI.
   4. Menguji perubahan stock identity akibat perubahan item.
   5. Memastikan accounting mengikuti nilai jurnal terbaru.
   6. Tetap menggunakan idbranch = JTS.
      Cabang test = JTS1 atau JTS2.
   7. Menguji manual accounting JVGENL (jurnal perkiraan).
   8. Menguji NDKAPD / NDKAPK / NDKARD / NDKARK.
   9. Menguji perkiraan asal, perkiraan lawan, debit/kredit, dan pajak.
   ============================================================ */

/* ------------------------------------------------------------
   INSERT JURNAL AWAL
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    ref_docno,
    ref_doctype,
    source_table,
    source_id,
    source_line_id,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    createdby
)
VALUES
(
    MD5('TX-JOURNAL-UPD-001'),
    MD5('JOURNAL-UPD-TEST-001-DTL-001'),
    'JOURNAL-UPD-TEST-001',
    'JOURNAL_UPDATE',
    'GRNREC',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'PO-UPD-TEST-001',
    'PO',
    'test_journal_update',
    9801,
    1,
    'BRG001',
    'BARANG TEST',
    'KG',
    'AREA01',
    'WH01',
    'A01',
    'BATCHUPD001',
    'LOT-UPD-001',
    15,
    10000,
    150000,
    0,
    150000,
    150000,
    16500,
    166500,
    'PPN11',
    'NO',
    'IDR',
    1,
    'TEST'
);

/* ------------------------------------------------------------
   SNAPSHOT SEBELUM UPDATE
   ------------------------------------------------------------ */

SELECT
    uniqueid,
    docno,
    journal_type,
    idbranch,
    cabang,
    idbarang,
    namabarang,
    batch,
    lotno,
    qty,
    harga,
    nilai,
    pajak,
    total
FROM sc_trx.transaction_dt
WHERE uniqueid = MD5('TX-JOURNAL-UPD-001');

/* ------------------------------------------------------------
   UPDATE JURNAL SEBELUMNYA
   ------------------------------------------------------------

   Perubahan:
   BRG001 -> BRG002
   Qty    : 15 -> 18
   Harga  : 10.000 -> 12.500
   Nilai  : 150.000 -> 225.000
   PPN    : 16.500 -> 24.750
   Total  : 166.500 -> 249.750

   Batch dan lot tetap agar fokus pengujian
   perubahan ITEM + VALUE pada jurnal yang sama.
   ------------------------------------------------------------ */

UPDATE sc_trx.transaction_dt
SET
    idbarang  = 'BRG002',
    namabarang = 'BARANG OPENING',
    qty       = 18,
    harga     = 12500,
    bruto     = 225000,
    discount  = 0,
    nilai     = 225000,
    dpp       = 225000,
    pajak     = 24750,
    total     = 249750,
    idtax     = 'PPN11',
    isinclusive = 'NO',
    currcode  = 'IDR',
    kurs      = 1
WHERE uniqueid = MD5('TX-JOURNAL-UPD-001');

/* ------------------------------------------------------------
   SNAPSHOT SESUDAH UPDATE
   ------------------------------------------------------------ */

SELECT
    uniqueid,
    docno,
    journal_type,
    idbranch,
    cabang,
    idbarang,
    namabarang,
    batch,
    lotno,
    qty,
    harga,
    nilai,
    pajak,
    total
FROM sc_trx.transaction_dt
WHERE uniqueid = MD5('TX-JOURNAL-UPD-001');

/* ------------------------------------------------------------
   VALIDASI HASIL UPDATE
   ------------------------------------------------------------ */

SELECT
    td.uniqueid,
    td.source_uniqueid,
    td.docno,
    td.journal_type,
    td.idbranch,
    td.cabang,
    td.idbarang,
    td.namabarang,
    td.qty,
    td.harga,
    td.nilai,
    td.pajak,
    td.total,
    jh.uniqueid AS jurnal_uniqueid,
    jh.total_debet,
    jh.total_kredit,
    jh.status AS jurnal_status
FROM sc_trx.transaction_dt td
LEFT JOIN sc_trx.jurnal_hd jh
       ON jh.source_uniqueid = td.uniqueid
      OR jh.uniqueid = td.source_uniqueid
WHERE td.uniqueid = MD5('TX-JOURNAL-UPD-001');

/* ============================================================
   SAMPLE JURNAL PERKIRAAN / GENERAL JOURNAL
   ============================================================

   JOURNAL TYPE : JVGENL
   DOCUMENT     : JV-PERKIRAAN-001
   CABANG       : JTS1

   ATURAN:
       Satu docno JVGENL dapat memiliki beberapa line.
       Setiap line:
           idcoa         = perkiraan yang diinput
           debet_kredit  = D/K
           counter_idcoa = TIDAK WAJIB

       Semua line:
           docno + doctype + journal_type + idbranch
       -> SATU jurnal_hd

       Setiap line
       -> SATU jurnal_dt

   Contoh:
       722103     D  84.627.273
       213201     K  84.627.273

   TOTAL:
       DEBET  = 84.627.273
       KREDIT = 84.627.273
       BALANCE = 0
   ============================================================ */


/* ------------------------------------------------------------
   PREFLIGHT COA JVGENL
   ------------------------------------------------------------ */

DO $$
BEGIN

    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = '722103'
    )
    THEN
        RAISE EXCEPTION
            'TEST JVGENL GAGAL: COA 722103 tidak ditemukan.';
    END IF;


    IF NOT EXISTS
    (
        SELECT 1
        FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::TEXT) = '213201'
    )
    THEN
        RAISE EXCEPTION
            'TEST JVGENL GAGAL: COA 213201 tidak ditemukan.';
    END IF;

END;
$$;


/* ------------------------------------------------------------
   LINE 1 - DEBET
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    source_table,
    source_id,
    source_line_id,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    idcoa,
    counter_idcoa,
    debet_kredit,
    createdby
)
VALUES
(
    MD5('TX-JV-PERKIRAAN-001'),
    MD5('JV-PERKIRAAN-TEST-001'),
    'JV-PERKIRAAN-001',
    'GENERAL_JOURNAL',
    'JVGENL',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'test_journal_perkiraan',
    9901,
    1,
    '',
    'JURNAL UMUM TEST - DEBET',
    '',
    '',
    '',
    '',
    '',
    '',
    1,
    84627273,
    84627273,
    0,
    84627273,
    84627273,
    0,
    84627273,
    'NON',
    'NO',
    'IDR',
    1,
    '722103',
    '',
    'D',
    'TEST'
);


/* ------------------------------------------------------------
   LINE 2 - KREDIT
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid,
    source_uniqueid,
    docno,
    doctype,
    journal_type,
    line_no,
    docdate,
    idbranch,
    cabang,
    type_in_out,
    source_table,
    source_id,
    source_line_id,
    idbarang,
    namabarang,
    idunit,
    idarea,
    warehouse,
    bin,
    batch,
    lotno,
    qty,
    harga,
    bruto,
    discount,
    nilai,
    dpp,
    pajak,
    total,
    idtax,
    isinclusive,
    currcode,
    kurs,
    idcoa,
    counter_idcoa,
    debet_kredit,
    createdby
)
VALUES
(
    MD5('TX-JV-PERKIRAAN-002'),
    MD5('JV-PERKIRAAN-TEST-001'),
    'JV-PERKIRAAN-001',
    'GENERAL_JOURNAL',
    'JVGENL',
    2,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'test_journal_perkiraan',
    9901,
    2,
    '',
    'JURNAL UMUM TEST - KREDIT',
    '',
    '',
    '',
    '',
    '',
    '',
    1,
    84627273,
    84627273,
    0,
    84627273,
    84627273,
    0,
    84627273,
    'NON',
    'NO',
    'IDR',
    1,
    '213201',
    '',
    'K',
    'TEST'
);


/* ------------------------------------------------------------
   VALIDASI JAVGENL DOCUMENT + DETAIL
   ------------------------------------------------------------ */

SELECT
    td.docno,
    td.line_no,
    td.idcoa,
    td.counter_idcoa,
    td.debet_kredit,
    td.nilai,
    jh.id AS jurnal_id,
    jh.status AS jurnal_status,
    jh.total_debet,
    jh.total_kredit,
    jh.balance,
    jd.seq AS jurnal_dt_seq,
    jd.idcoa AS jurnal_dt_coa,
    jd.debet,
    jd.kredit,
    jd.ref_docno,
    jd.ref_doctype
FROM sc_trx.transaction_dt td
LEFT JOIN sc_trx.jurnal_hd jh
       ON BTRIM(jh.docno::TEXT) = BTRIM(td.docno::TEXT)
      AND BTRIM(jh.doctype::TEXT) = BTRIM(td.doctype::TEXT)
      AND BTRIM(jh.journal_type::TEXT) = BTRIM(td.journal_type::TEXT)
      AND BTRIM(jh.idbranch::TEXT) = BTRIM(td.idbranch::TEXT)
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
      AND jh.status = 'POSTED'
LEFT JOIN sc_trx.jurnal_dt jd
       ON jd.jurnal_id = jh.id
      AND jd.source_uniqueid = td.uniqueid
WHERE td.docno = 'JV-PERKIRAAN-001'
  AND BTRIM(td.journal_type::TEXT) = 'JVGENL'
ORDER BY td.line_no;


/* ------------------------------------------------------------
   EXPECTED:
       2 transaction_dt
       1 jurnal_hd POSTED
       2 jurnal_dt
       jurnal_dt.ref_docno = JV-PERKIRAAN-001
       total_debet = total_kredit
       balance = 0
   ------------------------------------------------------------ */


/* ============================================================
   SAMPLE NOTA DEBIT / KREDIT SUPPLIER & CUSTOMER
   ============================================================

   Masing-masing menguji mapping manual:

       NDKAPD  = Nota Debit Supplier
       NDKAPK  = Nota Kredit Supplier
       NDKARD  = Nota Debit Customer
       NDKARK  = Nota Kredit Customer

   Nilai test:
       DPP     = 100.000
       PPN 11% = 11.000
       TOTAL   = 111.000

   Supplier menggunakan JTS1.
   Customer menggunakan JTS2.

   ACCOUNT / COUNTER_ACCOUNT menggunakan COA master:
       Supplier:
          ACCOUNT         = currency.IDR.phutang
          COUNTER_ACCOUNT = mbarang.BRG001.ppersediaan

       Customer:
          ACCOUNT         = currency.IDR.ppiutang
          COUNTER_ACCOUNT = currency.IDR.ppendapatan
   ============================================================ */


/* ------------------------------------------------------------
   PREFLIGHT COA MANUAL NDK
   ------------------------------------------------------------
   COA manual diambil mengikuti resolver accounting yang sama
   dengan TAHAP 16, sehingga fallback master/config tetap konsisten.

   Supplier:
       ACCOUNT         = AP / HUTANG
       COUNTER_ACCOUNT = INVENTORY

   Customer:
       ACCOUNT         = AR / PIUTANG
       COUNTER_ACCOUNT = SALES / INCOME
   ------------------------------------------------------------ */

DO $$
DECLARE
    v_supplier_account TEXT;
    v_supplier_counter TEXT;
    v_customer_account TEXT;
    v_customer_counter TEXT;
BEGIN
    /* Supplier ACCOUNT */
    v_supplier_account :=
        sc_trx.fn_resolve_accounting_coa(
            'AP',
            NULL::CHAR(20),
            'IDR'::CHAR(3),
            NULL::VARCHAR(20)
        );

    IF NULLIF(BTRIM(COALESCE(v_supplier_account,'')), '') IS NULL THEN
        SELECT jd.idcoa::TEXT
          INTO v_supplier_account
          FROM sc_trx.jurnal_dt jd
         WHERE jd.source_uniqueid = MD5('TX-GRNREC-001')
           AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('AP','HUTANG')
           AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
         ORDER BY jd.id
         LIMIT 1;
    END IF;

    /* Supplier COUNTER_ACCOUNT */
    v_supplier_counter :=
        sc_trx.fn_resolve_accounting_coa(
            'INVENTORY',
            'BRG001'::CHAR(20),
            'IDR'::CHAR(3),
            NULL::VARCHAR(20)
        );

    IF NULLIF(BTRIM(COALESCE(v_supplier_counter,'')), '') IS NULL THEN
        SELECT t2.idcoa::TEXT
          INTO v_supplier_counter
          FROM sc_trx.transaction_dt t2
         WHERE t2.uniqueid = MD5('TX-GRNREC-001')
           AND NULLIF(BTRIM(t2.idcoa::TEXT),'') IS NOT NULL
         LIMIT 1;
    END IF;

    IF NULLIF(BTRIM(COALESCE(v_supplier_counter,'')), '') IS NULL THEN
        SELECT jd.idcoa::TEXT
          INTO v_supplier_counter
          FROM sc_trx.jurnal_dt jd
         WHERE jd.source_uniqueid = MD5('TX-GRNREC-001')
           AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('INVENTORY','STOCK')
           AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
         ORDER BY jd.id
         LIMIT 1;
    END IF;

    /* Customer ACCOUNT */
    v_customer_account :=
        sc_trx.fn_resolve_accounting_coa(
            'AR',
            NULL::CHAR(20),
            'IDR'::CHAR(3),
            NULL::VARCHAR(20)
        );

    IF NULLIF(BTRIM(COALESCE(v_customer_account,'')), '') IS NULL THEN
        SELECT jd.idcoa::TEXT
          INTO v_customer_account
          FROM sc_trx.jurnal_dt jd
         WHERE jd.source_uniqueid = MD5('TX-SALESX-001')
           AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('AR','PIUTANG')
           AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
         ORDER BY jd.id
         LIMIT 1;
    END IF;

    /* Customer COUNTER_ACCOUNT */
    v_customer_counter :=
        sc_trx.fn_resolve_accounting_coa(
            'SALES',
            NULL::CHAR(20),
            'IDR'::CHAR(3),
            NULL::VARCHAR(20)
        );

    IF NULLIF(BTRIM(COALESCE(v_customer_counter,'')), '') IS NULL THEN
        SELECT t2.idcoa::TEXT
          INTO v_customer_counter
          FROM sc_trx.transaction_dt t2
         WHERE t2.uniqueid = MD5('TX-SALESX-001')
           AND NULLIF(BTRIM(t2.idcoa::TEXT),'') IS NOT NULL
         LIMIT 1;
    END IF;

    IF NULLIF(BTRIM(COALESCE(v_customer_counter,'')), '') IS NULL THEN
        SELECT jd.idcoa::TEXT
          INTO v_customer_counter
          FROM sc_trx.jurnal_dt jd
         WHERE jd.source_uniqueid = MD5('TX-SALESX-001')
           AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('SALES','INCOME')
           AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
         ORDER BY jd.id
         LIMIT 1;
    END IF;

    /* Pastikan keempat COA benar-benar ada di master COA */
    IF NULLIF(BTRIM(COALESCE(v_supplier_account,'')), '') IS NULL
       OR NOT EXISTS (
            SELECT 1 FROM sc_mst.coa c
             WHERE BTRIM(c.idcoa::TEXT)=BTRIM(v_supplier_account)
       )
    THEN
        RAISE EXCEPTION 'TEST NDK GAGAL: COA ACCOUNT supplier tidak dapat di-resolve / tidak ada di sc_mst.coa: %', v_supplier_account;
    END IF;

    IF NULLIF(BTRIM(COALESCE(v_supplier_counter,'')), '') IS NULL
       OR NOT EXISTS (
            SELECT 1 FROM sc_mst.coa c
             WHERE BTRIM(c.idcoa::TEXT)=BTRIM(v_supplier_counter)
       )
    THEN
        RAISE EXCEPTION 'TEST NDK GAGAL: COA counter supplier tidak dapat di-resolve / tidak ada di sc_mst.coa: %', v_supplier_counter;
    END IF;

    IF NULLIF(BTRIM(COALESCE(v_customer_account,'')), '') IS NULL
       OR NOT EXISTS (
            SELECT 1 FROM sc_mst.coa c
             WHERE BTRIM(c.idcoa::TEXT)=BTRIM(v_customer_account)
       )
    THEN
        RAISE EXCEPTION 'TEST NDK GAGAL: COA ACCOUNT customer tidak dapat di-resolve / tidak ada di sc_mst.coa: %', v_customer_account;
    END IF;

    IF NULLIF(BTRIM(COALESCE(v_customer_counter,'')), '') IS NULL
       OR NOT EXISTS (
            SELECT 1 FROM sc_mst.coa c
             WHERE BTRIM(c.idcoa::TEXT)=BTRIM(v_customer_counter)
       )
    THEN
        RAISE EXCEPTION 'TEST NDK GAGAL: COA counter customer tidak dapat di-resolve / tidak ada di sc_mst.coa: %', v_customer_counter;
    END IF;

    RAISE NOTICE
        'PREFLIGHT NDK OK : supplier AP=% / counter=% ; customer AR=% / counter=%',
        v_supplier_account,
        v_supplier_counter,
        v_customer_account,
        v_customer_counter;
END $$;


/* ------------------------------------------------------------
   NDKAPD - NOTA DEBIT SUPPLIER
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    kdsupplier, nsupplier,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, bruto, discount, nilai, dpp, pajak, total,
    idtax, isinclusive, currcode, kurs,
    idcoa, counter_idcoa, debet_kredit,
    createdby
)
VALUES
(
    MD5('TX-NDKAPD-001'),
    MD5('NDKAPD-TEST-001-DTL-001'),
    'NDKAPD-TEST-001',
    'NOTA_DEBIT_SUP',
    'NDKAPD',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'IN',
    'GRN-TEST-001',
    'GRN',
    'test_ndk_supplier',
    9911,
    1,
    'SUP001',
    'SUPPLIER TEST',
    'BRG001',
    'BARANG TEST',
    'KG', 'AREA01', 'WH01', 'A01', 'BATCHNDK001', 'LOT-NDK-001',
    10, 10000, 100000, 0, 100000, 100000, 11000, 111000,
    'PPN11', 'NO', 'IDR', 1,
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('AP', NULL::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-GRNREC-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('AP','HUTANG')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('INVENTORY', 'BRG001'::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT t2.idcoa::TEXT
           FROM sc_trx.transaction_dt t2
          WHERE t2.uniqueid = MD5('TX-GRNREC-001')
            AND NULLIF(BTRIM(t2.idcoa::TEXT),'') IS NOT NULL
          LIMIT 1),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-GRNREC-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('INVENTORY','STOCK')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    'D',
    'TEST'
);


/* ------------------------------------------------------------
   NDKAPK - NOTA KREDIT SUPPLIER
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    kdsupplier, nsupplier,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, bruto, discount, nilai, dpp, pajak, total,
    idtax, isinclusive, currcode, kurs,
    idcoa, counter_idcoa, debet_kredit,
    createdby
)
VALUES
(
    MD5('TX-NDKAPK-001'),
    MD5('NDKAPK-TEST-001-DTL-001'),
    'NDKAPK-TEST-001',
    'NOTA_KREDIT_SUP',
    'NDKAPK',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS1',
    'OUT',
    'GRN-TEST-001',
    'GRN',
    'test_ndk_supplier',
    9912,
    1,
    'SUP001',
    'SUPPLIER TEST',
    'BRG001',
    'BARANG TEST',
    'KG', 'AREA01', 'WH01', 'A01', 'BATCHNDK002', 'LOT-NDK-002',
    10, 10000, 100000, 0, 100000, 100000, 11000, 111000,
    'PPN11', 'NO', 'IDR', 1,
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('AP', NULL::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-GRNREC-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('AP','HUTANG')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('INVENTORY', 'BRG001'::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT t2.idcoa::TEXT
           FROM sc_trx.transaction_dt t2
          WHERE t2.uniqueid = MD5('TX-GRNREC-001')
            AND NULLIF(BTRIM(t2.idcoa::TEXT),'') IS NOT NULL
          LIMIT 1),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-GRNREC-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('INVENTORY','STOCK')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    'D',
    'TEST'
);


/* ------------------------------------------------------------
   NDKARD - NOTA DEBIT CUSTOMER
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    kdcustomer, ncustomer,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, bruto, discount, nilai, dpp, pajak, total,
    idtax, isinclusive, currcode, kurs,
    idcoa, counter_idcoa, debet_kredit,
    createdby
)
VALUES
(
    MD5('TX-NDKARD-001'),
    MD5('NDKARD-TEST-001-DTL-001'),
    'NDKARD-TEST-001',
    'NOTA_DEBIT_CUS',
    'NDKARD',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS2',
    'IN',
    'SALES-TEST-001',
    'SALES',
    'test_ndk_customer',
    9921,
    1,
    'CUS001',
    'CUSTOMER TEST',
    'BRG001',
    'BARANG TEST',
    'KG', 'AREA01', 'WH01', 'A01', 'BATCHNDK003', 'LOT-NDK-003',
    10, 10000, 100000, 0, 100000, 100000, 11000, 111000,
    'PPN11', 'NO', 'IDR', 1,
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('AR', NULL::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-SALESX-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('AR','PIUTANG')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('SALES', NULL::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT t2.idcoa::TEXT
           FROM sc_trx.transaction_dt t2
          WHERE t2.uniqueid = MD5('TX-SALESX-001')
            AND NULLIF(BTRIM(t2.idcoa::TEXT),'') IS NOT NULL
          LIMIT 1),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-SALESX-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('SALES','INCOME')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    'D',
    'TEST'
);


/* ------------------------------------------------------------
   NDKARK - NOTA KREDIT CUSTOMER
   ------------------------------------------------------------ */

INSERT INTO sc_trx.transaction_dt
(
    uniqueid, source_uniqueid, docno, doctype, journal_type, line_no,
    docdate, idbranch, cabang, type_in_out,
    ref_docno, ref_doctype,
    source_table, source_id, source_line_id,
    kdcustomer, ncustomer,
    idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno,
    qty, harga, bruto, discount, nilai, dpp, pajak, total,
    idtax, isinclusive, currcode, kurs,
    idcoa, counter_idcoa, debet_kredit,
    createdby
)
VALUES
(
    MD5('TX-NDKARK-001'),
    MD5('NDKARK-TEST-001-DTL-001'),
    'NDKARK-TEST-001',
    'NOTA_KREDIT_CUS',
    'NDKARK',
    1,
    CURRENT_DATE,
    'JTS',
    'JTS2',
    'OUT',
    'SALES-TEST-001',
    'SALES',
    'test_ndk_customer',
    9922,
    1,
    'CUS001',
    'CUSTOMER TEST',
    'BRG001',
    'BARANG TEST',
    'KG', 'AREA01', 'WH01', 'A01', 'BATCHNDK004', 'LOT-NDK-004',
    10, 10000, 100000, 0, 100000, 100000, 11000, 111000,
    'PPN11', 'NO', 'IDR', 1,
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('AR', NULL::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-SALESX-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('AR','PIUTANG')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    COALESCE(
        sc_trx.fn_resolve_accounting_coa('SALES', NULL::CHAR(20), 'IDR'::CHAR(3), NULL::VARCHAR(20)),
        (SELECT t2.idcoa::TEXT
           FROM sc_trx.transaction_dt t2
          WHERE t2.uniqueid = MD5('TX-SALESX-001')
            AND NULLIF(BTRIM(t2.idcoa::TEXT),'') IS NOT NULL
          LIMIT 1),
        (SELECT jd.idcoa::TEXT
           FROM sc_trx.jurnal_dt jd
          WHERE jd.source_uniqueid = MD5('TX-SALESX-001')
            AND UPPER(TRIM(COALESCE(jd.account_role,''))) IN ('SALES','INCOME')
            AND NULLIF(BTRIM(jd.idcoa::TEXT),'') IS NOT NULL
          ORDER BY jd.id
          LIMIT 1)
    ),
    'D',
    'TEST'
);


/* ============================================================
   VALIDASI SAMPLE MANUAL ACCOUNTING / NDK
   ============================================================ */

SELECT
    td.docno,
    td.journal_type,
    td.idbranch,
    td.cabang,
    td.idcoa AS perkiraan_utama,
    td.counter_idcoa AS perkiraan_lawan,
    td.debet_kredit,
    td.idtax,
    td.dpp,
    td.pajak,
    td.total,
    jh.uniqueid AS jurnal_uniqueid,
    jh.total_debet,
    jh.total_kredit,
    jh.balance,
    jh.status
FROM sc_trx.transaction_dt td
LEFT JOIN sc_trx.jurnal_hd jh
       ON jh.source_uniqueid = td.uniqueid
WHERE td.uniqueid IN
(
    MD5('TX-JV-PERKIRAAN-001'),
    MD5('TX-NDKAPD-001'),
    MD5('TX-NDKAPK-001'),
    MD5('TX-NDKARD-001'),
    MD5('TX-NDKARK-001')
)
ORDER BY td.journal_type, td.docno;


/* ============================================================
   DETAIL HASIL POSTING MANUAL

   Memastikan hasil generator benar-benar membentuk:
       - perkiraan utama
       - perkiraan lawan
       - pajak
       - debet
       - kredit
   ============================================================ */

SELECT
    jh.docno,
    jh.journal_type,
    jd.seq,
    jd.account_role,
    jd.value_source,
    jd.idcoa,
    jd.debet,
    jd.kredit,
    jd.source_uniqueid,
    jd.keterangan
FROM sc_trx.jurnal_hd jh
JOIN sc_trx.jurnal_dt jd
  ON jd.jurnal_id = jh.id
WHERE jh.source_uniqueid IN
(
    MD5('TX-JV-PERKIRAAN-001'),
    MD5('TX-NDKAPD-001'),
    MD5('TX-NDKAPK-001'),
    MD5('TX-NDKARD-001'),
    MD5('TX-NDKARK-001')
)
ORDER BY jh.docno, jd.seq, jd.id;


/* ============================================================
   VALIDASI HASIL INSERT
   ============================================================ */

SELECT uniqueid, source_uniqueid, docno, doctype, journal_type, line_no, docdate, idbranch, cabang, type_in_out, ref_docno, ref_doctype, idbarang, namabarang, idunit, idarea, warehouse, bin, batch, lotno, qty, harga, bruto, discount, nilai, dpp, pajak, total, idtax, isinclusive, currcode, kurs, debet, kredit, createdby, createddate
FROM sc_trx.transaction_dt
WHERE createdby = 'TEST'
  AND docno LIKE '%-TEST-%'
ORDER BY id;


/* ============================================================
   VALIDASI JOURNAL TYPE YANG SUDAH DITES
   ============================================================ */

SELECT journal_type, COUNT(*) AS jumlah_detail, SUM(CASE WHEN type_in_out = 'IN' THEN qty ELSE 0 END) AS qty_in, SUM(CASE WHEN type_in_out = 'OUT' THEN qty ELSE 0 END) AS qty_out, SUM(nilai) AS total_nilai
FROM sc_trx.transaction_dt
WHERE createdby = 'TEST'
  AND docno LIKE '%-TEST-%'
GROUP BY journal_type
ORDER BY journal_type;


/* ============================================================
   VALIDASI STOCK IDENTITY

   Kombinasi:
       idbranch
       warehouse
       bin
       idbarang
       idunit
       batch
       lotno

   Barang + batch + lot yang sama tetap boleh mempunyai
   banyak transaction uniqueid.
   ============================================================ */

SELECT uniqueid, journal_type, type_in_out, idbranch, warehouse, bin, idbarang, idunit, batch, lotno, qty, nilai
FROM sc_trx.transaction_dt
WHERE createdby = 'TEST'
  AND docno LIKE '%-TEST-%'
ORDER BY idbarang, batch, lotno, docdate, id;


/* ============================================================
   JIKA SEMUA TEST HANYA SIMULASI:
   gunakan ROLLBACK.

   Jika ingin menyimpan test:
   ganti ROLLBACK menjadi COMMIT.
   ============================================================ */
--COMMIT

/* ============================================================
   FINAL ASSERTION TAHAP 28
   ============================================================ */

/* JVGENL harus menjadi satu header POSTED dengan dua detail. */
DO $$
DECLARE
    v_hd_count INTEGER;
    v_dt_count INTEGER;
    v_balance NUMERIC;
BEGIN

    SELECT COUNT(*)
    INTO v_hd_count
    FROM sc_trx.jurnal_hd
    WHERE BTRIM(docno::TEXT) = 'JV-PERKIRAAN-001'
      AND BTRIM(journal_type::TEXT) = 'JVGENL'
      AND BTRIM(idbranch::TEXT) = 'JTS'
      AND status = 'POSTED'
      AND uniqueid NOT LIKE 'JRNL-REV-%';


    SELECT COUNT(*), COALESCE(SUM(debet) - SUM(kredit), 0)
    INTO v_dt_count, v_balance
    FROM sc_trx.jurnal_dt jd
    JOIN sc_trx.jurnal_hd jh
      ON jh.id = jd.jurnal_id
    WHERE BTRIM(jh.docno::TEXT) = 'JV-PERKIRAAN-001'
      AND BTRIM(jh.journal_type::TEXT) = 'JVGENL'
      AND BTRIM(jh.idbranch::TEXT) = 'JTS'
      AND jh.status = 'POSTED'
      AND jh.uniqueid NOT LIKE 'JRNL-REV-%';


    IF v_hd_count <> 1 THEN
        RAISE EXCEPTION
            'TEST JVGENL GAGAL: expected 1 POSTED jurnal_hd, actual %',
            v_hd_count;
    END IF;


    IF v_dt_count <> 2 THEN
        RAISE EXCEPTION
            'TEST JVGENL GAGAL: expected 2 jurnal_dt, actual %',
            v_dt_count;
    END IF;


    IF ABS(v_balance) > 0.01 THEN
        RAISE EXCEPTION
            'TEST JVGENL GAGAL: detail tidak balance, balance=%',
            v_balance;
    END IF;


    IF EXISTS
    (
        SELECT 1
        FROM sc_trx.jurnal_dt jd
        JOIN sc_trx.jurnal_hd jh
          ON jh.id = jd.jurnal_id
        WHERE BTRIM(jh.docno::TEXT) = 'JV-PERKIRAAN-001'
          AND BTRIM(jh.journal_type::TEXT) = 'JVGENL'
          AND jh.status = 'POSTED'
          AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
          AND BTRIM(COALESCE(jd.ref_docno, '')) <> 'JV-PERKIRAAN-001'
    )
    THEN
        RAISE EXCEPTION
            'TEST JVGENL GAGAL: jurnal_dt.ref_docno tidak sama dengan docno.';
    END IF;


    RAISE NOTICE
        'TEST JVGENL OK: 1 jurnal_hd, 2 jurnal_dt, balance=0, ref_docno benar.';

END;
$$;


/* NDK harus mempunyai detail dan ref_docno = docno NDK. */
DO $$
DECLARE
    r RECORD;
BEGIN

    FOR r IN
        SELECT
            jh.docno,
            COUNT(jd.id) AS detail_count,
            COUNT(*) FILTER (
                WHERE BTRIM(COALESCE(jd.ref_docno,'')) <> BTRIM(jh.docno::TEXT)
            ) AS wrong_ref
        FROM sc_trx.jurnal_hd jh
        LEFT JOIN sc_trx.jurnal_dt jd
          ON jd.jurnal_id = jh.id
        WHERE BTRIM(jh.docno::TEXT) IN
        (
            'NDKAPD-TEST-001',
            'NDKAPK-TEST-001',
            'NDKARD-TEST-001',
            'NDKARK-TEST-001'
        )
          AND jh.status = 'POSTED'
          AND jh.uniqueid NOT LIKE 'JRNL-REV-%'
        GROUP BY jh.id, jh.docno
        ORDER BY jh.docno
    LOOP

        IF r.detail_count = 0 THEN
            RAISE EXCEPTION
                'TEST NDK GAGAL: % tidak mempunyai jurnal_dt.',
                r.docno;
        END IF;


        IF r.wrong_ref > 0 THEN
            RAISE EXCEPTION
                'TEST NDK GAGAL: % mempunyai % jurnal_dt dengan ref_docno salah.',
                r.docno,
                r.wrong_ref;
        END IF;

    END LOOP;


    RAISE NOTICE
        'TEST NDK OK: detail jurnal dan ref_docno seluruh NDK benar.';

END;
$$;


COMMIT;

/* ============================================================
   SELESAI
   ============================================================ */
