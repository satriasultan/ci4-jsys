/*



CATATAN

clue pengambilan setting coa akun transaksi ,
1. sc_mst.coa -> table daftar akun.
2. sc_mst.mbarang -> setingan default coa diantaranya untuk jurnal trasaction_dt

settingan di sc_mst.mbarang atau master barang yang digunakan

idgroup -> ada JSA * BRG , membedakan barang dan jasa, jika jasa tidak masuk stok saat lpb tapi masuk hutang dagang (dari currency) & perkiraan jasa pjasa
ppersediaan -> untuk default perkiraan persediaan 
psj  -> untuk default perkiraan surat jalan
pcogs  -> untuk default perkiraan hpp / master barang
phppproduksi   -> untuk default perkiraan hpp produksi
pjasa   -> untuk default perkiraan jasa
pwaste   -> untuk default perkiraan pembuangan

3. settingan penjualan dan pembelian dari currency, mengikuti default currency dari sc_mst.currency
pembelian 
hutang dst
penjualan
piutang dst

4. jika tidak ada perkiraan setting dari master barang sc_mst.mbarang dan currency maka diambil dari sc_mst.konfigurasi_umum

6. masukkan semua setting untuk penjurnalan sebagai variable di TAHAP 04 


TAHAP 01
Function identitas stock
        ↓
TAHAP 02
Master journal_type
        ↓
TAHAP 03
Master journal_type_coa
        ↓
TAHAP 04
transaction_dt
        ↓
TAHAP 05
transaction_hd
        ↓
TAHAP 06
stkblc
        ↓
TAHAP 07
stkblc_avgcost
        ↓
TAHAP 08
assetblc
        ↓
TAHAP 09
jurnal_hd
        ↓
TAHAP 10
jurnal_dt
        ↓
TAHAP 11
Function reverse stock
        ↓
TAHAP 12
Function post stock
        ↓
TAHAP 13
Function reverse asset
        ↓
TAHAP 14
Function post asset
        ↓
TAHAP 15
Function reverse accounting
        ↓
TAHAP 16
Function post accounting
        ↓
TAHAP 17
Function recalculate transaction_hd
        ↓
TAHAP 18
Trigger transaction_dt
        ↓
TAHAP 19
Trigger / validasi source_uniqueid
        ↓
TAHAP 20
Test Purchase
        ↓
TAHAP 21
Test Return
        ↓
TAHAP 22
Test Update
        ↓
TAHAP 23
Test Cancel / Reverse
        ↓
TAHAP 24
Test Barang + Batch + Lot sama
        ↓
TAHAP 25
Test Multibranch
        ↓
TAHAP 26
Test Transfer INOUT
        ↓
TAHAP 27
Test Production

                         SOURCE DOCUMENT
                              │
                              ▼
                    ┌────────────────────┐
                    │ TRANSACTION_DT    │
                    │                    │
                    │      │
                    └─────────┬──────────┘
                              │
                            TRIGGER
                              │
            ┌─────────────────┼─────────────────┐
            │                 │                 │
            ▼                 ▼                 ▼
       ACCOUNTING           STOCK             ASSET
            │                 │                 │
            ▼                 ▼                 ▼
       jurnal_hd/dt          stkblc           sc_trx.assetblc
            │                 │                 │
            └─────────────────┼─────────────────┘
                              │
                              ▼
                    RECALCULATE SUMMARY
                              │
                              ▼
                    TRANSACTION_HD
*/


/* ============================================================
   TAHAP 01
   FUNCTION IDENTITAS STOCK

   stock_uniqueid:
   identitas kombinasi stock

   Kombinasi:
   idbranch
   warehouse
   bin
   idbarang
   idunit
   batch
   lotno

   uniqueid TIDAK dibuat di sini.
   uniqueid berasal dari transaksi masing-masing.
   ============================================================ */


CREATE OR REPLACE FUNCTION sc_trx.fn_stock_uniqueid(
    p_idbranch   TEXT,
    p_warehouse  TEXT,
    p_bin        TEXT,
    p_idbarang   TEXT,
    p_idunit     TEXT,
    p_batch      TEXT,
    p_lotno      TEXT
)
RETURNS TEXT
LANGUAGE SQL
IMMUTABLE
AS $$
    SELECT concat_ws('|',
        COALESCE(TRIM(p_idbranch), ''),
        COALESCE(TRIM(p_warehouse), ''),
        COALESCE(TRIM(p_bin), ''),
        COALESCE(TRIM(p_idbarang), ''),
        COALESCE(TRIM(p_idunit), ''),
        COALESCE(TRIM(p_batch), ''),
        COALESCE(TRIM(p_lotno), '')
    );
$$;


/* ============================================================
   STOCK KEY

   Digunakan untuk pencarian/index yang lebih cepat.
   ============================================================ */

CREATE OR REPLACE FUNCTION sc_trx.fn_stock_key(
    p_idbranch   TEXT,
    p_warehouse  TEXT,
    p_bin        TEXT,
    p_idbarang   TEXT,
    p_idunit     TEXT,
    p_batch      TEXT,
    p_lotno      TEXT
)
RETURNS CHAR(32)
LANGUAGE SQL
IMMUTABLE
AS $$
    SELECT md5(
        sc_trx.fn_stock_uniqueid(
            p_idbranch,
            p_warehouse,
            p_bin,
            p_idbarang,
            p_idunit,
            p_batch,
            p_lotno
        )
    );
$$;


/* ============================================================
   TEST
   ============================================================ */

SELECT
    sc_trx.fn_stock_uniqueid(
        'B01',
        'WH01',
        'A01',
        'BRG001',
        'KG',
        'BATCH001',
        'LOT001'
    ) AS stock_uniqueid,

    sc_trx.fn_stock_key(
        'B01',
        'WH01',
        'A01',
        'BRG001',
        'KG',
        'BATCH001',
        'LOT001'
    ) AS stock_key;


/* ============================================================
   TAHAP 02
   MASTER JOURNAL TYPE
   ============================================================ */

CREATE TABLE IF NOT EXISTS sc_mst.journal_type (
    journal_type CHAR(6) PRIMARY KEY,
    journal_name VARCHAR(100) NOT NULL,
    direction CHAR(5) NOT NULL,
    module VARCHAR(30) NOT NULL,
    stock_effect CHAR(5) NOT NULL DEFAULT 'NONE',
    accounting_effect CHAR(3) NOT NULL DEFAULT 'YES',
    asset_effect CHAR(5) NOT NULL DEFAULT 'NONE',
    description VARCHAR(250),

    CONSTRAINT ck_journal_type_format
        CHECK (
            LENGTH(TRIM(journal_type)) = 6
            AND journal_type = UPPER(journal_type)
            AND journal_type !~ '[[:space:]]'
        ),

    CONSTRAINT ck_journal_direction
        CHECK (
            direction IN ('IN','OUT','INOUT')
        ),

    CONSTRAINT ck_journal_stock_effect
        CHECK (
            stock_effect IN ('IN','OUT','INOUT','NONE')
        ),

    CONSTRAINT ck_journal_accounting_effect
        CHECK (
            accounting_effect IN ('YES','NO')
        ),

    CONSTRAINT ck_journal_asset_effect
        CHECK (
            asset_effect IN ('IN','OUT','INOUT','NONE')
        )
);


/* ============================================================
   PURCHASE
   ============================================================ */

INSERT INTO sc_mst.journal_type
(journal_type, journal_name, direction, module, stock_effect, accounting_effect, asset_effect, description)
VALUES
('PURCHS', 'PURCHASE', 'IN', 'PURCHASE', 'IN', 'YES', 'NONE', 'PEMBELIAN BARANG'),
('PURRET', 'PURCHASE RETURN', 'OUT', 'PURCHASE', 'OUT', 'YES', 'NONE', 'RETUR PEMBELIAN'),
('GRNREC', 'GOODS RECEIPT', 'IN', 'PURCHASE', 'IN', 'YES', 'NONE', 'PENERIMAAN BARANG DARI SUPPLIER'),
('GRNRET', 'GOODS RECEIPT RETURN', 'OUT', 'PURCHASE', 'OUT', 'YES', 'NONE', 'RETUR PENERIMAAN BARANG')


/* ============================================================
   SALES
   ============================================================ */

,('SALESX', 'SALES', 'OUT', 'SALES', 'OUT', 'YES', 'NONE', 'PENJUALAN BARANG'),
 ('SALRET', 'SALES RETURN', 'IN', 'SALES', 'IN', 'YES', 'NONE', 'RETUR PENJUALAN'),
 ('DELIVR', 'DELIVERY', 'OUT', 'SALES', 'OUT', 'YES', 'NONE', 'PENGIRIMAN BARANG KE CUSTOMER'),
 ('DELRET', 'DELIVERY RETURN', 'IN', 'SALES', 'IN', 'YES', 'NONE', 'PENGEMBALIAN BARANG DELIVERY')


/* ============================================================
   INVENTORY
   ============================================================ */

,('STKINX', 'STOCK IN', 'IN', 'INVENTORY', 'IN', 'YES', 'NONE', 'STOK MASUK UMUM'),
 ('STKOUT', 'STOCK OUT', 'OUT', 'INVENTORY', 'OUT', 'YES', 'NONE', 'STOK KELUAR UMUM')


/* ============================================================
   ADJUSTMENT
   ============================================================ */

,('ADJINX', 'STOCK ADJUSTMENT IN', 'IN', 'INVENTORY', 'IN', 'YES', 'NONE', 'PENYESUAIAN STOK MASUK'),
 ('ADJOUT', 'STOCK ADJUSTMENT OUT', 'OUT', 'INVENTORY', 'OUT', 'YES', 'NONE', 'PENYESUAIAN STOK KELUAR')


/* ============================================================
   OPENING STOCK
   ============================================================ */

,('OPENST', 'OPENING STOCK', 'IN', 'OPENING', 'IN', 'YES', 'NONE', 'SALDO AWAL PERSEDIAAN'),
 ('OPNSTR', 'OPENING STOCK REVERSAL', 'OUT', 'OPENING', 'OUT', 'YES', 'NONE', 'PEMBALIKAN SALDO AWAL PERSEDIAAN')


/* ============================================================
   TRANSFER
   ============================================================ */

,('TRFWHS', 'WAREHOUSE TRANSFER', 'INOUT', 'INVENTORY', 'INOUT', 'YES', 'NONE', 'TRANSFER ANTAR GUDANG'),
 ('BINMOV', 'BIN MOVEMENT', 'INOUT', 'INVENTORY', 'INOUT', 'YES', 'NONE', 'PERPINDAHAN LOKASI BIN'),
 ('BRNTRF', 'BRANCH TRANSFER', 'INOUT', 'INVENTORY', 'INOUT', 'YES', 'NONE', 'TRANSFER ANTAR CABANG')


/* ============================================================
   MANUFACTURING
   ============================================================ */

,('WOISSU', 'WORK ORDER MATERIAL ISSUE', 'OUT', 'MANUFACTURING', 'OUT', 'YES', 'NONE', 'PEMAKAIAN BAHAN PRODUKSI'),
 ('WORETN', 'WORK ORDER MATERIAL RETURN', 'IN', 'MANUFACTURING', 'IN', 'YES', 'NONE', 'PENGEMBALIAN BAHAN PRODUKSI'),
 ('WORECV', 'WORK ORDER PRODUCTION RECEIPT', 'IN', 'MANUFACTURING', 'IN', 'YES', 'NONE', 'PENERIMAAN BARANG JADI PRODUKSI'),
 ('WORETR', 'WORK ORDER PRODUCTION RETURN', 'OUT', 'MANUFACTURING', 'OUT', 'YES', 'NONE', 'PENGEMBALIAN BARANG HASIL PRODUKSI'),
 ('WOADJX', 'WORK ORDER ADJUSTMENT', 'INOUT', 'MANUFACTURING', 'INOUT', 'YES', 'NONE', 'PENYESUAIAN TRANSAKSI PRODUKSI')


/* ============================================================
   WIP
   ============================================================ */

,('WIPINX', 'WIP IN', 'IN', 'MANUFACTURING', 'NONE', 'YES', 'NONE', 'PENAMBAHAN NILAI WIP'),
 ('WIPOUT', 'WIP OUT', 'OUT', 'MANUFACTURING', 'NONE', 'YES', 'NONE', 'PENGURANGAN NILAI WIP'),
 ('WIPMOV', 'WIP MOVEMENT', 'INOUT', 'MANUFACTURING', 'NONE', 'YES', 'NONE', 'PERPINDAHAN NILAI WIP')


/* ============================================================
   SCRAP
   ============================================================ */

,('SCRAPP', 'PRODUCTION SCRAP', 'OUT', 'MANUFACTURING', 'OUT', 'YES', 'NONE', 'SCRAP HASIL PRODUKSI'),
 ('SCRREV', 'SCRAP REVERSAL', 'IN', 'MANUFACTURING', 'IN', 'YES', 'NONE', 'PEMBATALAN SCRAP PRODUKSI'),
 ('WASTEX', 'PRODUCTION WASTE', 'OUT', 'MANUFACTURING', 'OUT', 'YES', 'NONE', 'WASTE HASIL PRODUKSI')


/* ============================================================
   REWORK
   ============================================================ */

,('REWORK', 'REWORK', 'INOUT', 'MANUFACTURING', 'INOUT', 'YES', 'NONE', 'PROSES REWORK PRODUKSI'),
 ('RWISSU', 'REWORK ISSUE', 'OUT', 'MANUFACTURING', 'OUT', 'YES', 'NONE', 'PEMAKAIAN MATERIAL REWORK'),
 ('RWRECV', 'REWORK RECEIPT', 'IN', 'MANUFACTURING', 'IN', 'YES', 'NONE', 'PENERIMAAN HASIL REWORK')


/* ============================================================
   BY / CO PRODUCT
   ============================================================ */

,('BYPROD', 'BY PRODUCT RECEIPT', 'IN', 'MANUFACTURING', 'IN', 'YES', 'NONE', 'PENERIMAAN BY PRODUCT'),
 ('COPROD', 'CO PRODUCT RECEIPT', 'IN', 'MANUFACTURING', 'IN', 'YES', 'NONE', 'PENERIMAAN CO PRODUCT'),
 ('BYRETN', 'BY PRODUCT RETURN', 'OUT', 'MANUFACTURING', 'OUT', 'YES', 'NONE', 'PENGEMBALIAN BY PRODUCT')


/* ============================================================
   SUBCONTRACT
   ============================================================ */

,('SBISSU', 'SUBCONTRACT ISSUE', 'OUT', 'SUBCONTRACT', 'OUT', 'YES', 'NONE', 'PENGIRIMAN MATERIAL KE SUBCONTRACTOR'),
 ('SBRETN', 'SUBCONTRACT RETURN', 'IN', 'SUBCONTRACT', 'IN', 'YES', 'NONE', 'PENGEMBALIAN MATERIAL DARI SUBCONTRACTOR'),
 ('SBRECV', 'SUBCONTRACT RECEIPT', 'IN', 'SUBCONTRACT', 'IN', 'YES', 'NONE', 'PENERIMAAN HASIL SUBCONTRACT')


/* ============================================================
   QUALITY
   ============================================================ */

,('QHOLDX', 'QUALITY HOLD', 'INOUT', 'QUALITY', 'INOUT', 'NO', 'NONE', 'HOLD BARANG KARENA QUALITY'),
 ('QRELSX', 'QUALITY RELEASE', 'INOUT', 'QUALITY', 'INOUT', 'NO', 'NONE', 'RELEASE BARANG QUALITY'),
 ('QREJCT', 'QUALITY REJECT', 'INOUT', 'QUALITY', 'INOUT', 'YES', 'NONE', 'REJECT BARANG KARENA QUALITY')


/* ============================================================
   PAYMENT / RECEIPT
   ============================================================ */

,('PAYMNT', 'PAYMENT', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'PEMBAYARAN SUPPLIER ATAU HUTANG'),
 ('PAYREV', 'PAYMENT REVERSAL', 'IN', 'FINANCE', 'NONE', 'YES', 'NONE', 'PEMBALIKAN PEMBAYARAN'),
 ('RECVPT', 'RECEIPT', 'IN', 'FINANCE', 'NONE', 'YES', 'NONE', 'PENERIMAAN CUSTOMER ATAU PIUTANG'),
 ('RECREV', 'RECEIPT REVERSAL', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'PEMBALIKAN PENERIMAAN')


/* ============================================================
   CASH
   ============================================================ */

,('CASHIN', 'CASH IN', 'IN', 'FINANCE', 'NONE', 'YES', 'NONE', 'KAS MASUK'),
 ('CASHOT', 'CASH OUT', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'KAS KELUAR'),
 ('CASHAD', 'CASH ADJUSTMENT', 'INOUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'PENYESUAIAN SALDO KAS')


/* ============================================================
   BANK
   ============================================================ */

,('BANKIN', 'BANK IN', 'IN', 'FINANCE', 'NONE', 'YES', 'NONE', 'BANK MASUK'),
 ('BANKOT', 'BANK OUT', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'BANK KELUAR'),
 ('BANKTR', 'BANK TRANSFER', 'INOUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'TRANSFER ANTAR REKENING BANK'),
 ('BANKCH', 'BANK CHARGE', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'BIAYA ADMINISTRASI BANK')


/* ============================================================
   NON STOCK
   ============================================================ */

,('EXPENS', 'EXPENSE', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'TRANSAKSI BIAYA NON STOCK'),
 ('EXPREV', 'EXPENSE REVERSAL', 'IN', 'FINANCE', 'NONE', 'YES', 'NONE', 'PEMBALIKAN TRANSAKSI BIAYA'),
 ('OTHINC', 'OTHER INCOME', 'IN', 'FINANCE', 'NONE', 'YES', 'NONE', 'PENDAPATAN LAIN-LAIN'),
 ('OTHEXP', 'OTHER EXPENSE', 'OUT', 'FINANCE', 'NONE', 'YES', 'NONE', 'BIAYA LAIN-LAIN')


/* ============================================================
   TAX
   ============================================================ */

,('TAXINX', 'INPUT TAX', 'IN', 'TAX', 'NONE', 'YES', 'NONE', 'PAJAK MASUKAN'),
 ('TAXOUT', 'OUTPUT TAX', 'OUT', 'TAX', 'NONE', 'YES', 'NONE', 'PAJAK KELUAR'),
 ('TAXADJ', 'TAX ADJUSTMENT', 'INOUT', 'TAX', 'NONE', 'YES', 'NONE', 'PENYESUAIAN PAJAK')


/* ============================================================
   LANDED COST
   ============================================================ */

,('LANDCS', 'LANDED COST', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA LANDED COST IMPORT NON STOCK'),
 ('FREIGH', 'FREIGHT COST', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA FREIGHT'),
 ('INSURE', 'IMPORT INSURANCE', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA INSURANCE IMPORT'),
 ('CUSTOM', 'CUSTOM DUTY', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BEA MASUK IMPORT'),
 ('HANDLC', 'HANDLING COST', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA HANDLING'),
 ('PORTCH', 'PORT CHARGE', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA PELABUHAN'),
 ('TRUCKC', 'TRUCKING COST', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA TRUCKING'),
 ('DEMURR', 'DEMURRAGE COST', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA DEMURRAGE'),
 ('FORWRD', 'FORWARDER COST', 'OUT', 'PURCHASE', 'NONE', 'YES', 'NONE', 'BIAYA FORWARDER')


/* ============================================================
   COSTING
   ============================================================ */

,('COSTAD', 'COST ADJUSTMENT', 'INOUT', 'COSTING', 'INOUT', 'YES', 'NONE', 'PENYESUAIAN NILAI COST'),
 ('COSTRV', 'COST REVERSAL', 'INOUT', 'COSTING', 'INOUT', 'YES', 'NONE', 'PEMBALIKAN NILAI COST')


/* ============================================================
   FIXED ASSET
   ============================================================ */

,('ASSETI', 'ASSET ACQUISITION', 'IN', 'ASSET', 'NONE', 'YES', 'IN', 'PEROLEHAN ASET'),
 ('ASSETD', 'ASSET DISPOSAL', 'OUT', 'ASSET', 'NONE', 'YES', 'OUT', 'PELEPASAN ASET'),
 ('DEPREC', 'DEPRECIATION', 'OUT', 'ASSET', 'NONE', 'YES', 'OUT', 'PENYUSUTAN ASET'),
 ('ASSETX', 'ASSET ADJUSTMENT', 'INOUT', 'ASSET', 'NONE', 'YES', 'INOUT', 'PENYESUAIAN ASET')


/* ============================================================
   OPENING BALANCE
   ============================================================ */

,('OPENGL', 'OPENING GENERAL LEDGER', 'IN', 'OPENING', 'NONE', 'YES', 'NONE', 'SALDO AWAL GENERAL LEDGER'),
 ('OPENAP', 'OPENING ACCOUNT PAYABLE', 'IN', 'OPENING', 'NONE', 'YES', 'NONE', 'SALDO AWAL HUTANG SUPPLIER'),
 ('OPENAR', 'OPENING ACCOUNT RECEIVABLE', 'IN', 'OPENING', 'NONE', 'YES', 'NONE', 'SALDO AWAL PIUTANG CUSTOMER'),
 ('OPNCAS', 'OPENING CASH', 'IN', 'OPENING', 'NONE', 'YES', 'NONE', 'SALDO AWAL KAS'),
 ('OPNBNK', 'OPENING BANK', 'IN', 'OPENING', 'NONE', 'YES', 'NONE', 'SALDO AWAL BANK'),
 ('OPNAST', 'OPENING FIXED ASSET', 'IN', 'OPENING', 'NONE', 'YES', 'IN', 'SALDO AWAL ASET TETAP'),
 ('OPNWIP', 'OPENING WIP', 'IN', 'OPENING', 'NONE', 'YES', 'NONE', 'SALDO AWAL WIP')


/* ============================================================
   ACCRUAL / PREPAID
   ============================================================ */

,('ACCRUA', 'ACCRUAL', 'IN', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENCATATAN ACCRUAL'),
 ('ACCRRV', 'ACCRUAL REVERSAL', 'OUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PEMBALIKAN ACCRUAL'),
 ('PREPAI', 'PREPAID PAYMENT', 'IN', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PEMBAYARAN BIAYA DIBAYAR DIMUKA'),
 ('PREEXP', 'PREPAID EXPENSE', 'OUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENGAKUAN BIAYA PREPAID')


/* ============================================================
   ACCOUNTING ADJUSTMENT
   ============================================================ */

,('ADJGLX', 'GENERAL LEDGER ADJUSTMENT', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENYESUAIAN GENERAL LEDGER'),
 ('ADJAPX', 'ACCOUNT PAYABLE ADJUSTMENT', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENYESUAIAN HUTANG'),
 ('ADJARX', 'ACCOUNT RECEIVABLE ADJUSTMENT', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENYESUAIAN PIUTANG'),
 ('ADJCSH', 'CASH ADJUSTMENT', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENYESUAIAN SALDO KAS'),
 ('ADJBKN', 'BANK ADJUSTMENT', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENYESUAIAN SALDO BANK')


/* ============================================================
   GENERAL JOURNAL
   ============================================================ */

,('JVGENL', 'GENERAL JOURNAL', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'JURNAL UMUM MANUAL'),
 ('JVREVS', 'JOURNAL REVERSAL', 'INOUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PEMBALIKAN JURNAL UMUM')


/* ============================================================
   PERIOD
   ============================================================ */

,('OPNPER', 'PERIOD OPENING', 'IN', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PEMBUKAAN PERIODE ACCOUNTING'),
 ('CLSPER', 'PERIOD CLOSING', 'OUT', 'ACCOUNTING', 'NONE', 'YES', 'NONE', 'PENUTUPAN PERIODE ACCOUNTING')


ON CONFLICT (journal_type) DO UPDATE
SET journal_name = EXCLUDED.journal_name,
    direction = EXCLUDED.direction,
    module = EXCLUDED.module,
    stock_effect = EXCLUDED.stock_effect,
    accounting_effect = EXCLUDED.accounting_effect,
    asset_effect = EXCLUDED.asset_effect,
    description = EXCLUDED.description;
	
	/* ============================================================
   TAHAP 02 — POSTING POINT CORRECTION
   JSYS ERP

   TUJUAN:
   Mencegah double posting antara dokumen operasional dan
   titik pengakuan accounting.

   POLICY YANG DIGUNAKAN:

   PURCHASE
       PURCHS  = PO / dokumen pembelian -> NO POSTING
       GRNREC  = penerimaan -> STOCK + ACCOUNTING
       PURRET  = dokumen retur -> NO POSTING
       GRNRET  = retur fisik -> STOCK + ACCOUNTING

   SALES
       DELIVR  = pengiriman fisik -> STOCK SAJA
       SALESX  = faktur penjualan -> ACCOUNTING
       DELRET  = pengembalian fisik -> STOCK SAJA
       SALRET  = faktur retur -> ACCOUNTING

   Catatan:
   Pada GRNREC, accounting mengikuti kebutuhan JSYS:
       BRG -> Persediaan + PPN Masukan + Hutang
       JSA -> Perkiraan Jasa + PPN Masukan + Hutang

   Pada SALESX barang:
       Piutang + Penjualan + PPN Keluaran
       HPP + Persediaan

   ============================================================ */

UPDATE sc_mst.journal_type
SET
    stock_effect = 'NONE',
    accounting_effect = 'NO'
WHERE journal_type IN ('PURCHS', 'PURRET');


UPDATE sc_mst.journal_type
SET
    stock_effect = 'IN',
    accounting_effect = 'YES'
WHERE journal_type = 'GRNREC';


UPDATE sc_mst.journal_type
SET
    stock_effect = 'OUT',
    accounting_effect = 'YES'
WHERE journal_type = 'GRNRET';


UPDATE sc_mst.journal_type
SET
    stock_effect = 'OUT',
    accounting_effect = 'NO'
WHERE journal_type = 'DELIVR';


UPDATE sc_mst.journal_type
SET
    stock_effect = 'NONE',
    accounting_effect = 'YES'
WHERE journal_type = 'SALESX';


UPDATE sc_mst.journal_type
SET
    stock_effect = 'IN',
    accounting_effect = 'NO'
WHERE journal_type = 'DELRET';


UPDATE sc_mst.journal_type
SET
    stock_effect = 'NONE',
    accounting_effect = 'YES'
WHERE journal_type = 'SALRET';
	
/* ============================================================
   TAHAP 03
   MASTER MAPPING JOURNAL TYPE → COA

   account_role:
       STOCK
       AP
       AR
       SALES
       COGS
       TAX
       CASH
       BANK
       EXPENSE
       INCOME
       WIP
       ASSET
       DLL

   debit_credit:
       D = DEBET
       K = KREDIT

   value_source:
       NILAI
       DPP
       PAJAK
       TOTAL
       QTY
       COST

   seq:
       urutan jurnal
   ============================================================ */


CREATE TABLE IF NOT EXISTS sc_mst.journal_type_coa (
    id BIGSERIAL PRIMARY KEY,

    journal_type CHAR(6) NOT NULL,

    account_role VARCHAR(30) NOT NULL,

    idcoa VARCHAR(20) NOT NULL,

    debit_credit CHAR(1) NOT NULL,

    value_source VARCHAR(10) NOT NULL,

    seq INTEGER NOT NULL DEFAULT 1,

    active CHAR(3) NOT NULL DEFAULT 'YES',

    description VARCHAR(250),

    createdby VARCHAR(50),
    createddate TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,

    updatedby VARCHAR(50),
    updateddate TIMESTAMP WITHOUT TIME ZONE,


    CONSTRAINT fk_journal_type_coa_type
        FOREIGN KEY (journal_type)
        REFERENCES sc_mst.journal_type(journal_type),


    CONSTRAINT ck_journal_type_coa_dc
        CHECK (
            debit_credit IN ('D','K')
        ),


    CONSTRAINT ck_journal_type_coa_value_source
        CHECK (
            value_source IN (
                'NILAI',
                'DPP',
                'PAJAK',
                'TOTAL',
                'QTY',
                'COST'
            )
        ),


    CONSTRAINT ck_journal_type_coa_active
        CHECK (
            active IN ('YES','NO')
        )
);


/* ============================================================
   INDEX
   ============================================================ */

CREATE INDEX IF NOT EXISTS idx_journal_type_coa_type
ON sc_mst.journal_type_coa(journal_type);

CREATE INDEX IF NOT EXISTS idx_journal_type_coa_role
ON sc_mst.journal_type_coa(journal_type, account_role);

CREATE INDEX IF NOT EXISTS idx_journal_type_coa_active
ON sc_mst.journal_type_coa(journal_type, active);


/* ============================================================
   SATU JOURNAL TYPE BOLEH MEMPUNYAI BEBERAPA COA
   ============================================================

   seq harus UNIQUE di dalam satu journal_type.
   Jangan memaksakan unique index bila database lama masih
   memiliki duplicate (journal_type, seq).
   ============================================================ */

DO $$
DECLARE
    v_dup TEXT;
BEGIN

    SELECT STRING_AGG(
               TRIM(journal_type::TEXT) || ':SEQ=' || seq::TEXT,
               ', '
           )
    INTO v_dup
    FROM
    (
        SELECT journal_type, seq
        FROM sc_mst.journal_type_coa
        GROUP BY journal_type, seq
        HAVING COUNT(*) > 1
        ORDER BY journal_type, seq
    ) x;

    IF v_dup IS NOT NULL THEN
        RAISE EXCEPTION
            'TAHAP 03 GAGAL: duplicate mapping journal_type + seq ditemukan: %',
            v_dup;
    END IF;

    IF NOT EXISTS
    (
        SELECT 1
        FROM pg_indexes
        WHERE schemaname = 'sc_mst'
          AND tablename = 'journal_type_coa'
          AND indexname = 'uq_journal_type_coa_seq'
    )
    THEN
        CREATE UNIQUE INDEX uq_journal_type_coa_seq
        ON sc_mst.journal_type_coa
        (
            journal_type,
            seq
        );
    END IF;

END
$$;





	
/* ============================================================
   JSYS ERP
   PATCH TAHAP 03 - MAPPING ACCOUNTING GRNREC
   ============================================================

   MASALAH:
       ERROR: Mapping accounting belum tersedia
              untuk journal_type = GRNREC

   ATURAN GRNREC:
       BARANG:
          DEBET   INVENTORY  = DPP / NILAI PEROLEHAN
          DEBET   TAX        = PAJAK
          KREDIT  AP         = TOTAL

       JSA:
          INVENTORY role akan di-resolve oleh
          fn_resolve_accounting_coa() menjadi mbarang.pjasa.

   IMPORTANT:
       idcoa pada journal_type_coa diisi kosong sebagai fallback.
       COA final tetap di-resolve dinamis oleh:
           mbarang.ppersediaan / mbarang.pjasa
           currency.phutang
           tax_dtl.prk_masukan

   Tidak DROP table.
   Tidak mengubah transaction_dt.
   Tidak mengubah jurnal existing.
   ============================================================ */

BEGIN;


/* ============================================================
   1. PASTIKAN JOURNAL TYPE GRNREC ADA
   ============================================================ */

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT) = 'GRNREC'
    ) THEN
        RAISE EXCEPTION
            'Journal type GRNREC tidak ditemukan di sc_mst.journal_type';
    END IF;
END;
$$;


/* ============================================================
   2. AKTIFKAN / PERBAIKI MAPPING GRNREC
   ============================================================ */

UPDATE sc_mst.journal_type_coa
SET
    account_role = 'INVENTORY',
    idcoa        = '',
    debit_credit = 'D',
    value_source = 'DPP',
    active       = 'YES',
    description  = 'GRNREC - Persediaan / Jasa dari mbarang',
    updatedby    = 'SYSTEM',
    updateddate  = CURRENT_TIMESTAMP
WHERE TRIM(journal_type::TEXT) = 'GRNREC'
  AND seq = 1;


UPDATE sc_mst.journal_type_coa
SET
    account_role = 'TAX',
    idcoa        = '',
    debit_credit = 'D',
    value_source = 'PAJAK',
    active       = 'YES',
    description  = 'GRNREC - Pajak Masukan dari tax_dtl.prk_masukan',
    updatedby    = 'SYSTEM',
    updateddate  = CURRENT_TIMESTAMP
WHERE TRIM(journal_type::TEXT) = 'GRNREC'
  AND seq = 2;


UPDATE sc_mst.journal_type_coa
SET
    account_role = 'AP',
    idcoa        = '',
    debit_credit = 'K',
    value_source = 'TOTAL',
    active       = 'YES',
    description  = 'GRNREC - Hutang Supplier dari currency.phutang',
    updatedby    = 'SYSTEM',
    updateddate = CURRENT_TIMESTAMP
WHERE TRIM(journal_type::TEXT) = 'GRNREC'
  AND seq = 3;


/* ============================================================
   3. INSERT MAPPING JIKA BELUM ADA
   ============================================================ */

INSERT INTO sc_mst.journal_type_coa
(
    journal_type,
    account_role,
    idcoa,
    debit_credit,
    value_source,
    seq,
    active,
    description,
    createdby,
    createddate
)
SELECT
    'GRNREC',
    x.account_role,
    '',
    x.debit_credit,
    x.value_source,
    x.seq,
    'YES',
    x.description,
    'SYSTEM',
    CURRENT_TIMESTAMP
FROM
(
    VALUES
        (
            'INVENTORY',
            'D',
            'DPP',
            1,
            'GRNREC - Persediaan / Jasa dari mbarang'
        ),
        (
            'TAX',
            'D',
            'PAJAK',
            2,
            'GRNREC - Pajak Masukan dari tax_dtl.prk_masukan'
        ),
        (
            'AP',
            'K',
            'TOTAL',
            3,
            'GRNREC - Hutang Supplier dari currency.phutang'
        )
) AS x
(
    account_role,
    debit_credit,
    value_source,
    seq,
    description
)
WHERE NOT EXISTS
(
    SELECT 1
    FROM sc_mst.journal_type_coa j
    WHERE TRIM(j.journal_type::TEXT) = 'GRNREC'
      AND j.seq = x.seq
);


/* ============================================================
   4. HASIL MAPPING
   ============================================================ */

SELECT
    id,
    TRIM(journal_type::TEXT) AS journal_type,
    account_role,
    idcoa,
    debit_credit,
    value_source,
    seq,
    active,
    description
FROM sc_mst.journal_type_coa
WHERE TRIM(journal_type::TEXT) = 'GRNREC'
ORDER BY seq, id;


/* ============================================================
   5. VALIDASI MAPPING STATIS GRNREC
   ============================================================

   Resolver COA berada di TAHAP 04, sehingga TAHAP 03 tidak
   boleh bergantung pada function TAHAP 04.
   ============================================================ */

DO $$
DECLARE
    v_count INTEGER;
BEGIN

    SELECT COUNT(*)
    INTO v_count
    FROM sc_mst.journal_type_coa
    WHERE TRIM(journal_type::TEXT) = 'GRNREC'
      AND active = 'YES';

    IF v_count <> 3 THEN
        RAISE EXCEPTION
            'TAHAP 03 GAGAL: mapping GRNREC aktif harus 3 baris, ditemukan %',
            v_count;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT) = 'GRNREC'
          AND seq = 1
          AND account_role = 'INVENTORY'
          AND debit_credit = 'D'
          AND value_source = 'DPP'
          AND active = 'YES'
    ) THEN
        RAISE EXCEPTION 'TAHAP 03 GAGAL: mapping GRNREC seq=1 tidak sesuai';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT) = 'GRNREC'
          AND seq = 2
          AND account_role = 'TAX'
          AND debit_credit = 'D'
          AND value_source = 'PAJAK'
          AND active = 'YES'
    ) THEN
        RAISE EXCEPTION 'TAHAP 03 GAGAL: mapping GRNREC seq=2 tidak sesuai';
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT) = 'GRNREC'
          AND seq = 3
          AND account_role = 'AP'
          AND debit_credit = 'K'
          AND value_source = 'TOTAL'
          AND active = 'YES'
    ) THEN
        RAISE EXCEPTION 'TAHAP 03 GAGAL: mapping GRNREC seq=3 tidak sesuai';
    END IF;

    RAISE NOTICE 'TAHAP 03 OK: mapping GRNREC valid';

END
$$;


/* ============================================================
   SELESAI
   ============================================================ */

COMMIT;
