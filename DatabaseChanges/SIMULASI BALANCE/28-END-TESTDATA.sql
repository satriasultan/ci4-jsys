
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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
    'B01',
    'CABANG 01',
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

   OUT dari B01
   IN ke B02

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
    'B01',
    'CABANG 01',
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
    'B02',
    'CABANG 02',
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
ROLLBACK;

/* ============================================================
   SELESAI
   ============================================================ */
