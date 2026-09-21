/*
===========================================================================
JSYS ERP
TAHAP 01 - 03 FINAL FIXED
===========================================================================

TUJUAN
1. Rewrite TAHAP 01-03 menjadi satu baseline final yang bersih.
2. Mempertahankan struktur utama dari script existing.
3. Menghapus patch/correction yang sebelumnya diperlukan dengan langsung
   menetapkan nilai final pada master journal_type.
4. Menambahkan journal type accounting yang dibutuhkan untuk:
   - Jurnal umum perkiraan
   - Uang muka titipan
   - Nota debit/kredit
   - Giro
   - Accrual / prepaid (sudah ada, tidak diduplikasi)
   - Foreign exchange adjustment
   - Write-off / provision
   - Payroll posting
5. TAHAP 01 tidak menyentuh accounting.
6. TAHAP 02 hanya mendefinisikan konteks transaksi.
7. TAHAP 03 mendefinisikan role jurnal. COA final untuk transaksi
   operasional di-resolve pada TAHAP 04.
8. Untuk transaksi manual, COA berasal dari input transaksi:
      idcoa          = perkiraan
      counter_idcoa  = perkiraan lawan
   Struktur kolom tersebut akan ditambahkan pada TAHAP 04, bukan di sini.

CATATAN
- Tidak DROP table.
- Tidak DROP function existing.
- Tidak mengubah jurnal existing.
- idcoa pada mapping boleh berupa '' untuk resolver dinamis.
- uniqueid stock berbeda dengan uniqueid transaksi.
=========================================================================== */


/* =======================================================================
   TAHAP 01
   FUNCTION IDENTITAS STOCK
   ======================================================================= */

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


/* TEST TAHAP 01 */
SELECT
    sc_trx.fn_stock_uniqueid(
        'B01','WH01','A01','BRG001','KG','BATCH001','LOT001'
    ) AS stock_uniqueid,
    sc_trx.fn_stock_key(
        'B01','WH01','A01','BRG001','KG','BATCH001','LOT001'
    ) AS stock_key;


/* =======================================================================
   TAHAP 02
   MASTER JOURNAL TYPE
   ======================================================================= */

CREATE TABLE IF NOT EXISTS sc_mst.journal_type (
    journal_type       CHAR(6) PRIMARY KEY,
    journal_name       VARCHAR(100) NOT NULL,
    direction          CHAR(5) NOT NULL,
    module             VARCHAR(30) NOT NULL,
    stock_effect       CHAR(5) NOT NULL DEFAULT 'NONE',
    accounting_effect  CHAR(3) NOT NULL DEFAULT 'YES',
    asset_effect       CHAR(5) NOT NULL DEFAULT 'NONE',
    description        VARCHAR(250),

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


/*
---------------------------------------------------------------------------
MASTER FINAL
---------------------------------------------------------------------------
Policy penting:
PURCHS  = dokumen pembelian, tidak posting
PURRET  = dokumen retur pembelian, tidak posting
GRNREC  = penerimaan fisik + accounting
GRNRET  = retur penerimaan fisik + accounting

DELIVR  = pengiriman fisik, stock saja
SALESX  = faktur penjualan, accounting saja
DELRET  = pengembalian fisik, stock saja
SALRET  = faktur retur penjualan, accounting saja

Manual accounting:
JVGENL / UMTITP / NDK* / GIRO* / FX* / write-off / provision / payroll
tidak masuk stock dan COA dipilih pada transaksi.
*/

BEGIN;

INSERT INTO sc_mst.journal_type
(
    journal_type,
    journal_name,
    direction,
    module,
    stock_effect,
    accounting_effect,
    asset_effect,
    description
)
VALUES

/* PURCHASE */
('PURCHS','PURCHASE','IN','PURCHASE','NONE','NO','NONE',
 'DOKUMEN PEMBELIAN / PURCHASE ORDER TANPA POSTING STOCK DAN ACCOUNTING'),

('PURRET','PURCHASE RETURN','OUT','PURCHASE','NONE','NO','NONE',
 'DOKUMEN RETUR PEMBELIAN TANPA POSTING; RETUR FISIK MENGGUNAKAN GRNRET'),

('GRNREC','GOODS RECEIPT','IN','PURCHASE','IN','YES','NONE',
 'PENERIMAAN BARANG DARI SUPPLIER; STOCK DAN ACCOUNTING'),

('GRNRET','GOODS RECEIPT RETURN','OUT','PURCHASE','OUT','YES','NONE',
 'RETUR FISIK PENERIMAAN BARANG KE SUPPLIER; STOCK DAN ACCOUNTING'),

/* SALES */
('SALESX','SALES','OUT','SALES','NONE','YES','NONE',
 'FAKTUR PENJUALAN; ACCOUNTING, COGS, TAX DAN AR'),

('SALRET','SALES RETURN','IN','SALES','NONE','YES','NONE',
 'FAKTUR RETUR PENJUALAN; ACCOUNTING DAN PEMBALIKAN COGS'),

('DELIVR','DELIVERY','OUT','SALES','OUT','NO','NONE',
 'PENGIRIMAN BARANG KE CUSTOMER; STOCK SAJA'),

('DELRET','DELIVERY RETURN','IN','SALES','IN','NO','NONE',
 'PENGEMBALIAN BARANG DELIVERY; STOCK SAJA'),

/* INVENTORY */
('STKINX','STOCK IN','IN','INVENTORY','IN','YES','NONE',
 'STOCK MASUK UMUM'),

('STKOUT','STOCK OUT','OUT','INVENTORY','OUT','YES','NONE',
 'STOCK KELUAR UMUM'),

/* ADJUSTMENT */
('ADJINX','STOCK ADJUSTMENT IN','IN','INVENTORY','IN','YES','NONE',
 'PENYESUAIAN STOCK MASUK'),

('ADJOUT','STOCK ADJUSTMENT OUT','OUT','INVENTORY','OUT','YES','NONE',
 'PENYESUAIAN STOCK KELUAR'),

/* OPENING STOCK */
('OPENST','OPENING STOCK','IN','OPENING','IN','YES','NONE',
 'SALDO AWAL PERSEDIAAN'),

('OPNSTR','OPENING STOCK REVERSAL','OUT','OPENING','OUT','YES','NONE',
 'PEMBALIKAN SALDO AWAL PERSEDIAAN'),

/* TRANSFER */
('TRFWHS','WAREHOUSE TRANSFER','INOUT','INVENTORY','INOUT','YES','NONE',
 'TRANSFER ANTAR GUDANG'),

('BINMOV','BIN MOVEMENT','INOUT','INVENTORY','INOUT','YES','NONE',
 'PERPINDAHAN LOKASI BIN'),

('BRNTRF','BRANCH TRANSFER','INOUT','INVENTORY','INOUT','YES','NONE',
 'TRANSFER ANTAR CABANG'),

/* MANUFACTURING */
('WOISSU','WORK ORDER MATERIAL ISSUE','OUT','MANUFACTURING','OUT','YES','NONE',
 'PEMAKAIAN BAHAN PRODUKSI'),

('WORETN','WORK ORDER MATERIAL RETURN','IN','MANUFACTURING','IN','YES','NONE',
 'PENGEMBALIAN BAHAN PRODUKSI'),

('WORECV','WORK ORDER PRODUCTION RECEIPT','IN','MANUFACTURING','IN','YES','NONE',
 'PENERIMAAN BARANG JADI PRODUKSI'),

('WORETR','WORK ORDER PRODUCTION RETURN','OUT','MANUFACTURING','OUT','YES','NONE',
 'PENGEMBALIAN BARANG HASIL PRODUKSI'),

('WOADJX','WORK ORDER ADJUSTMENT','INOUT','MANUFACTURING','INOUT','YES','NONE',
 'PENYESUAIAN TRANSAKSI PRODUKSI'),

/* WIP */
('WIPINX','WIP IN','IN','MANUFACTURING','NONE','YES','NONE',
 'PENAMBAHAN NILAI WIP'),

('WIPOUT','WIP OUT','OUT','MANUFACTURING','NONE','YES','NONE',
 'PENGURANGAN NILAI WIP'),

('WIPMOV','WIP MOVEMENT','INOUT','MANUFACTURING','NONE','YES','NONE',
 'PERPINDAHAN NILAI WIP'),

/* SCRAP / WASTE */
('SCRAPP','PRODUCTION SCRAP','OUT','MANUFACTURING','OUT','YES','NONE',
 'SCRAP HASIL PRODUKSI'),

('SCRREV','SCRAP REVERSAL','IN','MANUFACTURING','IN','YES','NONE',
 'PEMBATALAN SCRAP PRODUKSI'),

('WASTEX','PRODUCTION WASTE','OUT','MANUFACTURING','OUT','YES','NONE',
 'WASTE HASIL PRODUKSI'),

/* REWORK */
('REWORK','REWORK','INOUT','MANUFACTURING','INOUT','YES','NONE',
 'PROSES REWORK PRODUKSI'),

('RWISSU','REWORK ISSUE','OUT','MANUFACTURING','OUT','YES','NONE',
 'PEMAKAIAN MATERIAL REWORK'),

('RWRECV','REWORK RECEIPT','IN','MANUFACTURING','IN','YES','NONE',
 'PENERIMAAN HASIL REWORK'),

/* BY / CO PRODUCT */
('BYPROD','BY PRODUCT RECEIPT','IN','MANUFACTURING','IN','YES','NONE',
 'PENERIMAAN BY PRODUCT'),

('COPROD','CO PRODUCT RECEIPT','IN','MANUFACTURING','IN','YES','NONE',
 'PENERIMAAN CO PRODUCT'),

('BYRETN','BY PRODUCT RETURN','OUT','MANUFACTURING','OUT','YES','NONE',
 'PENGEMBALIAN BY PRODUCT'),

/* SUBCONTRACT */
('SBISSU','SUBCONTRACT ISSUE','OUT','SUBCONTRACT','OUT','YES','NONE',
 'PENGIRIMAN MATERIAL KE SUBCONTRACTOR'),

('SBRETN','SUBCONTRACT RETURN','IN','SUBCONTRACT','IN','YES','NONE',
 'PENGEMBALIAN MATERIAL DARI SUBCONTRACTOR'),

('SBRECV','SUBCONTRACT RECEIPT','IN','SUBCONTRACT','IN','YES','NONE',
 'PENERIMAAN HASIL SUBCONTRACT'),

/* QUALITY */
('QHOLDX','QUALITY HOLD','INOUT','QUALITY','INOUT','NO','NONE',
 'HOLD BARANG KARENA QUALITY'),

('QRELSX','QUALITY RELEASE','INOUT','QUALITY','INOUT','NO','NONE',
 'RELEASE BARANG QUALITY'),

('QREJCT','QUALITY REJECT','INOUT','QUALITY','INOUT','YES','NONE',
 'REJECT BARANG KARENA QUALITY'),

/* PAYMENT / RECEIPT */
('PAYMNT','PAYMENT','OUT','FINANCE','NONE','YES','NONE',
 'PEMBAYARAN SUPPLIER ATAU HUTANG'),

('PAYREV','PAYMENT REVERSAL','IN','FINANCE','NONE','YES','NONE',
 'PEMBALIKAN PEMBAYARAN'),

('RECVPT','RECEIPT','IN','FINANCE','NONE','YES','NONE',
 'PENERIMAAN CUSTOMER ATAU PIUTANG'),

('RECREV','RECEIPT REVERSAL','OUT','FINANCE','NONE','YES','NONE',
 'PEMBALIKAN PENERIMAAN'),

/* CASH */
('CASHIN','CASH IN','IN','FINANCE','NONE','YES','NONE',
 'KAS MASUK'),

('CASHOT','CASH OUT','OUT','FINANCE','NONE','YES','NONE',
 'KAS KELUAR'),

('CASHAD','CASH ADJUSTMENT','INOUT','FINANCE','NONE','YES','NONE',
 'PENYESUAIAN SALDO KAS'),

/* BANK */
('BANKIN','BANK IN','IN','FINANCE','NONE','YES','NONE',
 'BANK MASUK'),

('BANKOT','BANK OUT','OUT','FINANCE','NONE','YES','NONE',
 'BANK KELUAR'),

('BANKTR','BANK TRANSFER','INOUT','FINANCE','NONE','YES','NONE',
 'TRANSFER ANTAR REKENING BANK'),

('BANKCH','BANK CHARGE','OUT','FINANCE','NONE','YES','NONE',
 'BIAYA ADMINISTRASI BANK'),

/* GIRO */
('GIROIN','GIRO IN','IN','FINANCE','NONE','YES','NONE',
 'PENERIMAAN GIRO MASUK; ACCOUNT DAN COUNTER ACCOUNT DARI TRANSAKSI'),

('GIROUT','GIRO OUT','OUT','FINANCE','NONE','YES','NONE',
 'PENERBITAN GIRO KELUAR; ACCOUNT DAN COUNTER ACCOUNT DARI TRANSAKSI'),

/* NON STOCK */
('EXPENS','EXPENSE','OUT','FINANCE','NONE','YES','NONE',
 'TRANSAKSI BIAYA NON STOCK'),

('EXPREV','EXPENSE REVERSAL','IN','FINANCE','NONE','YES','NONE',
 'PEMBALIKAN TRANSAKSI BIAYA'),

('OTHINC','OTHER INCOME','IN','FINANCE','NONE','YES','NONE',
 'PENDAPATAN LAIN-LAIN'),

('OTHEXP','OTHER EXPENSE','OUT','FINANCE','NONE','YES','NONE',
 'BIAYA LAIN-LAIN'),

/* TAX */
('TAXINX','INPUT TAX','IN','TAX','NONE','YES','NONE',
 'PAJAK MASUKAN'),

('TAXOUT','OUTPUT TAX','OUT','TAX','NONE','YES','NONE',
 'PAJAK KELUAR'),

('TAXADJ','TAX ADJUSTMENT','INOUT','TAX','NONE','YES','NONE',
 'PENYESUAIAN PAJAK'),

/* LANDED COST */
('LANDCS','LANDED COST','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA LANDED COST IMPORT NON STOCK'),

('FREIGH','FREIGHT COST','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA FREIGHT'),

('INSURE','IMPORT INSURANCE','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA INSURANCE IMPORT'),

('CUSTOM','CUSTOM DUTY','OUT','PURCHASE','NONE','YES','NONE',
 'BEA MASUK IMPORT'),

('HANDLC','HANDLING COST','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA HANDLING'),

('PORTCH','PORT CHARGE','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA PELABUHAN'),

('TRUCKC','TRUCKING COST','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA TRUCKING'),

('DEMURR','DEMURRAGE COST','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA DEMURRAGE'),

('FORWRD','FORWARDER COST','OUT','PURCHASE','NONE','YES','NONE',
 'BIAYA FORWARDER'),

/* COSTING */
('COSTAD','COST ADJUSTMENT','INOUT','COSTING','INOUT','YES','NONE',
 'PENYESUAIAN NILAI COST'),

('COSTRV','COST REVERSAL','INOUT','COSTING','INOUT','YES','NONE',
 'PEMBALIKAN NILAI COST'),

/* FIXED ASSET */
('ASSETI','ASSET ACQUISITION','IN','ASSET','NONE','YES','IN',
 'PEROLEHAN ASET'),

('ASSETD','ASSET DISPOSAL','OUT','ASSET','NONE','YES','OUT',
 'PELEPASAN ASET'),

('DEPREC','DEPRECIATION','OUT','ASSET','NONE','YES','OUT',
 'PENYUSUTAN ASET'),

('ASSETX','ASSET ADJUSTMENT','INOUT','ASSET','NONE','YES','INOUT',
 'PENYESUAIAN ASET'),

/* OPENING BALANCE */
('OPENGL','OPENING GENERAL LEDGER','IN','OPENING','NONE','YES','NONE',
 'SALDO AWAL GENERAL LEDGER'),

('OPENAP','OPENING ACCOUNT PAYABLE','IN','OPENING','NONE','YES','NONE',
 'SALDO AWAL HUTANG SUPPLIER'),

('OPENAR','OPENING ACCOUNT RECEIVABLE','IN','OPENING','NONE','YES','NONE',
 'SALDO AWAL PIUTANG CUSTOMER'),

('OPNCAS','OPENING CASH','IN','OPENING','NONE','YES','NONE',
 'SALDO AWAL KAS'),

('OPNBNK','OPENING BANK','IN','OPENING','NONE','YES','NONE',
 'SALDO AWAL BANK'),

('OPNAST','OPENING FIXED ASSET','IN','OPENING','NONE','YES','IN',
 'SALDO AWAL ASET TETAP'),

('OPNWIP','OPENING WIP','IN','OPENING','NONE','YES','NONE',
 'SALDO AWAL WIP'),

/* ACCRUAL / PREPAID - SUDAH ADA, TIDAK DIDUPLIKASI */
('ACCRUA','ACCRUAL','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENCATATAN ACCRUAL'),

('ACCRRV','ACCRUAL REVERSAL','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PEMBALIKAN ACCRUAL'),

('PREPAI','PREPAID PAYMENT','IN','ACCOUNTING','NONE','YES','NONE',
 'PEMBAYARAN BIAYA DIBAYAR DIMUKA'),

('PREEXP','PREPAID EXPENSE','OUT','ACCOUNTING','NONE','YES','NONE',
 'PENGAKUAN BIAYA PREPAID'),

/* ACCOUNTING ADJUSTMENT */
('ADJGLX','GENERAL LEDGER ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENYESUAIAN GENERAL LEDGER'),

('ADJAPX','ACCOUNT PAYABLE ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENYESUAIAN HUTANG'),

('ADJARX','ACCOUNT RECEIVABLE ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENYESUAIAN PIUTANG'),

('ADJCSH','CASH ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENYESUAIAN SALDO KAS'),

('ADJBKN','BANK ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENYESUAIAN SALDO BANK'),

/* GENERAL JOURNAL */
('JVGENL','GENERAL JOURNAL','INOUT','ACCOUNTING','NONE','YES','NONE',
 'JURNAL UMUM PERKIRAAN; COA ASAL DAN COA LAWAN DARI INPUT TRANSAKSI'),

('JVREVS','JOURNAL REVERSAL','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PEMBALIKAN JURNAL; DIBUAT DARI JURNAL ASAL'),

/* UANG MUKA / TITIPAN */
('UMTITP','ADVANCE / DEPOSIT HOLDING','INOUT','ACCOUNTING','NONE','YES','NONE',
 'UANG MUKA TITIPAN; PERKIRAAN DAN PERKIRAAN LAWAN DARI INPUT TRANSAKSI'),

/* NOTA DEBIT / KREDIT */
('NDKAPD','SUPPLIER DEBIT NOTE','INOUT','ACCOUNTING','NONE','YES','NONE',
 'NOTA DEBIT SUPPLIER / AP; COA DARI INPUT DAN TAX DARI TRANSAKSI'),

('NDKAPK','SUPPLIER CREDIT NOTE','INOUT','ACCOUNTING','NONE','YES','NONE',
 'NOTA KREDIT SUPPLIER / AP; COA DARI INPUT DAN TAX DARI TRANSAKSI'),

('NDKARD','CUSTOMER DEBIT NOTE','INOUT','ACCOUNTING','NONE','YES','NONE',
 'NOTA DEBIT CUSTOMER / AR; COA DARI INPUT DAN TAX DARI TRANSAKSI'),

('NDKARK','CUSTOMER CREDIT NOTE','INOUT','ACCOUNTING','NONE','YES','NONE',
 'NOTA KREDIT CUSTOMER / AR; COA DARI INPUT DAN TAX DARI TRANSAKSI'),

/* FOREIGN EXCHANGE */
('FXREAL','REALIZED FX ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'LABA/RUGI KURS REALISASI SAAT SETTLEMENT'),

('FXUNRL','UNREALIZED FX ADJUSTMENT','INOUT','ACCOUNTING','NONE','YES','NONE',
 'REVALUASI SALDO VALUTA ASING / LABA RUGI KURS BELUM TERREALISASI'),

/* RECEIVABLE / PAYABLE WRITE OFF */
('ARWOFF','AR WRITE OFF','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENGHAPUSAN PIUTANG; COA DAN LAWAN DITENTUKAN SAAT INPUT'),

('APWOFF','AP WRITE OFF','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENGHAPUSAN HUTANG; COA DAN LAWAN DITENTUKAN SAAT INPUT'),

/* BAD DEBT */
('BADPRV','BAD DEBT PROVISION','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENYISIHAN PIUTANG TAK TERTAGIH'),

('BADREV','BAD DEBT PROVISION REVERSAL','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PEMBALIKAN PENYISIHAN PIUTANG TAK TERTAGIH'),

/* DEFERRED / UNEARNED */
('UNEARN','UNEARNED REVENUE','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENDAPATAN DITERIMA DIMUKA / DEFERRED REVENUE'),

('UNEREL','UNEARNED REVENUE RELEASE','INOUT','ACCOUNTING','NONE','YES','NONE',
 'PENGAKUAN PENDAPATAN DARI DEFERRED REVENUE'),

/* PAYROLL */
('PAYROL','PAYROLL POSTING','INOUT','ACCOUNTING','NONE','YES','NONE',
 'POSTING PAYROLL: GAJI, TUNJANGAN DAN KEWAJIBAN TERKAIT'),

/* PERIOD */
('OPNPER','PERIOD OPENING','IN','ACCOUNTING','NONE','YES','NONE',
 'PEMBUKAAN PERIODE ACCOUNTING'),

('CLSPER','PERIOD CLOSING','OUT','ACCOUNTING','NONE','YES','NONE',
 'PENUTUPAN PERIODE ACCOUNTING')

ON CONFLICT (journal_type) DO UPDATE
SET
    journal_name      = EXCLUDED.journal_name,
    direction         = EXCLUDED.direction,
    module            = EXCLUDED.module,
    stock_effect      = EXCLUDED.stock_effect,
    accounting_effect = EXCLUDED.accounting_effect,
    asset_effect      = EXCLUDED.asset_effect,
    description       = EXCLUDED.description;

COMMIT;


/* VALIDASI FINAL TAHAP 02 */
SELECT
    TRIM(journal_type::TEXT) AS journal_type,
    journal_name,
    direction,
    module,
    stock_effect,
    accounting_effect,
    asset_effect,
    description
FROM sc_mst.journal_type
ORDER BY module, journal_type;


/* =======================================================================
   TAHAP 03
   MASTER MAPPING JOURNAL TYPE -> COA / ROLE
   ======================================================================= */

CREATE TABLE IF NOT EXISTS sc_mst.journal_type_coa (
    id              BIGSERIAL PRIMARY KEY,
    journal_type    CHAR(6) NOT NULL,
    account_role    VARCHAR(30) NOT NULL,
    idcoa           VARCHAR(20) NOT NULL DEFAULT '',
    debit_credit    CHAR(1) NOT NULL,
    value_source    VARCHAR(10) NOT NULL,
    seq             INTEGER NOT NULL DEFAULT 1,
    active          CHAR(3) NOT NULL DEFAULT 'YES',
    description     VARCHAR(250),

    createdby       VARCHAR(50),
    createddate     TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updatedby       VARCHAR(50),
    updateddate     TIMESTAMP WITHOUT TIME ZONE,

    CONSTRAINT fk_journal_type_coa_type
        FOREIGN KEY (journal_type)
        REFERENCES sc_mst.journal_type(journal_type),

    CONSTRAINT ck_journal_type_coa_dc
        CHECK (debit_credit IN ('D','K')),

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
        CHECK (active IN ('YES','NO'))
);


CREATE INDEX IF NOT EXISTS idx_journal_type_coa_type
ON sc_mst.journal_type_coa(journal_type);

CREATE INDEX IF NOT EXISTS idx_journal_type_coa_role
ON sc_mst.journal_type_coa(journal_type, account_role);

CREATE INDEX IF NOT EXISTS idx_journal_type_coa_active
ON sc_mst.journal_type_coa(journal_type, active);


/*
---------------------------------------------------------------------------
HELPER COMMENT
---------------------------------------------------------------------------
idcoa:
- '' = COA di-resolve dinamis pada TAHAP 04.
- Nilai COA tidak di-hard-code untuk transaksi yang bergantung pada
  mbarang / currency / konfigurasi / tax.

Untuk transaksi manual:
- ACCOUNT
- COUNTER_ACCOUNT
- TAX

COA aktual berasal dari transaction_dt:
- idcoa
- counter_idcoa
- idtax

Debet/kredit final transaksi manual akan mengikuti field transaksi
(debet_kredit) pada TAHAP 04.
--------------------------------------------------------------------------- */


/*
---------------------------------------------------------------------------
UPSERT MAPPING
---------------------------------------------------------------------------
Metode:
1. UPDATE mapping existing berdasarkan (journal_type, seq)
2. INSERT bila mapping belum ada

Tidak menggunakan ON CONFLICT (journal_type,seq), sehingga script tetap
dapat berjalan pada database lama yang belum memiliki unique index tersebut.
--------------------------------------------------------------------------- */

DO $$
DECLARE
    r RECORD;
BEGIN

    /* ================================================================
       ACCOUNTING FROM PURCHASE
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('GRNREC','INVENTORY','','D','DPP',1,
             'GRNREC - Persediaan/Jasa dari mbarang'),
            ('GRNREC','TAX','','D','PAJAK',2,
             'GRNREC - Pajak Masukan dari tax_dtl.prk_masukan'),
            ('GRNREC','AP','','K','TOTAL',3,
             'GRNREC - Hutang Supplier dari currency.phutang'),

            ('GRNRET','AP','','D','TOTAL',1,
             'GRNRET - Hutang Supplier dari currency.phutang'),
            ('GRNRET','INVENTORY','','K','DPP',2,
             'GRNRET - Persediaan/Jasa dari mbarang'),
            ('GRNRET','TAX','','K','PAJAK',3,
             'GRNRET - Pajak Masukan dari tax_dtl.prk_masukan'),

            ('LANDCS','INVENTORY','','D','NILAI',1,
             'LANDCS - Nilai biaya ke persediaan'),
            ('LANDCS','AP','','K','TOTAL',2,
             'LANDCS - Hutang biaya / supplier'),

            ('FREIGH','INVENTORY','','D','NILAI',1,
             'FREIGH - Nilai biaya ke persediaan'),
            ('FREIGH','AP','','K','TOTAL',2,
             'FREIGH - Hutang biaya / supplier'),

            ('INSURE','INVENTORY','','D','NILAI',1,
             'INSURE - Nilai biaya ke persediaan'),
            ('INSURE','AP','','K','TOTAL',2,
             'INSURE - Hutang biaya / supplier'),

            ('CUSTOM','INVENTORY','','D','NILAI',1,
             'CUSTOM - Nilai biaya ke persediaan'),
            ('CUSTOM','AP','','K','TOTAL',2,
             'CUSTOM - Hutang biaya / supplier'),

            ('HANDLC','INVENTORY','','D','NILAI',1,
             'HANDLC - Nilai biaya ke persediaan'),
            ('HANDLC','AP','','K','TOTAL',2,
             'HANDLC - Hutang biaya / supplier'),

            ('PORTCH','INVENTORY','','D','NILAI',1,
             'PORTCH - Nilai biaya ke persediaan'),
            ('PORTCH','AP','','K','TOTAL',2,
             'PORTCH - Hutang biaya / supplier'),

            ('TRUCKC','INVENTORY','','D','NILAI',1,
             'TRUCKC - Nilai biaya ke persediaan'),
            ('TRUCKC','AP','','K','TOTAL',2,
             'TRUCKC - Hutang biaya / supplier'),

            ('DEMURR','INVENTORY','','D','NILAI',1,
             'DEMURR - Nilai biaya ke persediaan'),
            ('DEMURR','AP','','K','TOTAL',2,
             'DEMURR - Hutang biaya / supplier'),

            ('FORWRD','INVENTORY','','D','NILAI',1,
             'FORWRD - Nilai biaya ke persediaan'),
            ('FORWRD','AP','','K','TOTAL',2,
             'FORWRD - Hutang biaya / supplier')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP

        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       SALES
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('SALESX','AR','','D','TOTAL',1,
             'SALESX - Piutang dari currency.ppiutang'),
            ('SALESX','SALES','','K','DPP',2,
             'SALESX - Pendapatan dari currency.ppendapatan'),
            ('SALESX','TAX','','K','PAJAK',3,
             'SALESX - Pajak Keluaran dari tax_dtl.prk_keluaran'),
            ('SALESX','COGS','','D','COST',4,
             'SALESX - HPP dari stok/avgcost'),
            ('SALESX','INVENTORY','','K','COST',5,
             'SALESX - Pengurangan persediaan berdasarkan cost'),

            ('SALRET','SALES_RETURN','','D','DPP',1,
             'SALRET - Retur penjualan'),
            ('SALRET','TAX','','D','PAJAK',2,
             'SALRET - Pajak Keluaran yang direverse / disesuaikan'),
            ('SALRET','AR','','K','TOTAL',3,
             'SALRET - Piutang dari currency.ppiutang'),
            ('SALRET','INVENTORY','','D','COST',4,
             'SALRET - Persediaan berdasarkan cost'),
            ('SALRET','COGS','','K','COST',5,
             'SALRET - Pembalikan HPP')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP

        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       DELIVERY
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('DELIVR','COGS','','D','COST',1,
             'DELIVR - HPP berdasarkan cost'),
            ('DELIVR','INVENTORY','','K','COST',2,
             'DELIVR - Pengurangan persediaan berdasarkan cost'),

            ('DELRET','INVENTORY','','D','COST',1,
             'DELRET - Persediaan berdasarkan cost'),
            ('DELRET','COGS','','K','COST',2,
             'DELRET - Pembalikan HPP')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       INVENTORY
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('STKINX','INVENTORY','','D','NILAI',1,
             'STKINX - Nilai persediaan'),
            ('STKINX','ADJUSTMENT','','K','NILAI',2,
             'STKINX - Lawan penyesuaian stock'),

            ('STKOUT','ADJUSTMENT','','D','NILAI',1,
             'STKOUT - Lawan penyesuaian stock'),
            ('STKOUT','INVENTORY','','K','NILAI',2,
             'STKOUT - Nilai persediaan'),

            ('ADJINX','INVENTORY','','D','NILAI',1,
             'ADJINX - Nilai persediaan'),
            ('ADJINX','ADJUSTMENT','','K','NILAI',2,
             'ADJINX - Akun selisih'),

            ('ADJOUT','ADJUSTMENT','','D','NILAI',1,
             'ADJOUT - Akun selisih'),
            ('ADJOUT','INVENTORY','','K','NILAI',2,
             'ADJOUT - Nilai persediaan'),

            ('OPENST','INVENTORY','','D','NILAI',1,
             'OPENST - Saldo awal persediaan'),
            ('OPENST','ADJUSTMENT','','K','NILAI',2,
             'OPENST - Akun pembuka'),

            ('OPNSTR','ADJUSTMENT','','D','NILAI',1,
             'OPNSTR - Akun pembuka'),
            ('OPNSTR','INVENTORY','','K','NILAI',2,
             'OPNSTR - Pembalikan persediaan'),

            ('TRFWHS','INVENTORY','','D','NILAI',1,
             'TRFWHS - Persediaan tujuan'),
            ('TRFWHS','INVENTORY','','K','NILAI',2,
             'TRFWHS - Persediaan asal'),

            ('BINMOV','INVENTORY','','D','NILAI',1,
             'BINMOV - Perpindahan lokasi persediaan'),
            ('BINMOV','INVENTORY','','K','NILAI',2,
             'BINMOV - Perpindahan lokasi persediaan'),

            ('BRNTRF','INVENTORY','','D','NILAI',1,
             'BRNTRF - Persediaan cabang tujuan'),
            ('BRNTRF','INVENTORY','','K','NILAI',2,
             'BRNTRF - Persediaan cabang asal')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       MANUFACTURING / WIP / SCRAP / REWORK / BYPRODUCT / SUBCONTRACT
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('WOISSU','WIP','','D','COST',1,'WOISSU - WIP'),
            ('WOISSU','INVENTORY','','K','COST',2,'WOISSU - Persediaan'),

            ('WORETN','INVENTORY','','D','COST',1,'WORETN - Persediaan'),
            ('WORETN','WIP','','K','COST',2,'WORETN - WIP'),

            ('WORECV','INVENTORY','','D','COST',1,'WORECV - Barang jadi'),
            ('WORECV','WIP','','K','COST',2,'WORECV - WIP'),

            ('WORETR','WIP','','D','COST',1,'WORETR - WIP'),
            ('WORETR','INVENTORY','','K','COST',2,'WORETR - Persediaan'),

            ('WOADJX','WIP','','D','NILAI',1,'WOADJX - WIP'),
            ('WOADJX','INVENTORY','','K','NILAI',2,'WOADJX - Persediaan'),

            ('WIPINX','WIP','','D','NILAI',1,'WIPINX - WIP'),
            ('WIPINX','INVENTORY','','K','NILAI',2,'WIPINX - Persediaan'),

            ('WIPOUT','INVENTORY','','D','NILAI',1,'WIPOUT - Persediaan'),
            ('WIPOUT','WIP','','K','NILAI',2,'WIPOUT - WIP'),

            ('WIPMOV','WIP','','D','NILAI',1,'WIPMOV - WIP'),
            ('WIPMOV','WIP','','K','NILAI',2,'WIPMOV - WIP'),

            ('SCRAPP','WASTE','','D','COST',1,'SCRAPP - Waste/Scrap'),
            ('SCRAPP','INVENTORY','','K','COST',2,'SCRAPP - Persediaan'),

            ('SCRREV','INVENTORY','','D','COST',1,'SCRREV - Persediaan'),
            ('SCRREV','WASTE','','K','COST',2,'SCRREV - Waste/Scrap'),

            ('WASTEX','WASTE','','D','COST',1,'WASTEX - Waste'),
            ('WASTEX','INVENTORY','','K','COST',2,'WASTEX - Persediaan'),

            ('REWORK','WIP','','D','NILAI',1,'REWORK - WIP'),
            ('REWORK','INVENTORY','','K','NILAI',2,'REWORK - Persediaan'),

            ('RWISSU','WIP','','D','COST',1,'RWISSU - WIP'),
            ('RWISSU','INVENTORY','','K','COST',2,'RWISSU - Persediaan'),

            ('RWRECV','INVENTORY','','D','COST',1,'RWRECV - Persediaan'),
            ('RWRECV','WIP','','K','COST',2,'RWRECV - WIP'),

            ('BYPROD','INVENTORY','','D','COST',1,'BYPROD - By Product'),
            ('BYPROD','WIP','','K','COST',2,'BYPROD - WIP'),

            ('COPROD','INVENTORY','','D','COST',1,'COPROD - Co Product'),
            ('COPROD','WIP','','K','COST',2,'COPROD - WIP'),

            ('BYRETN','WIP','','D','COST',1,'BYRETN - WIP'),
            ('BYRETN','INVENTORY','','K','COST',2,'BYRETN - Persediaan'),

            ('SBISSU','WIP','','D','COST',1,'SBISSU - WIP subcontract'),
            ('SBISSU','INVENTORY','','K','COST',2,'SBISSU - Persediaan'),

            ('SBRETN','INVENTORY','','D','COST',1,'SBRETN - Persediaan'),
            ('SBRETN','WIP','','K','COST',2,'SBRETN - WIP'),

            ('SBRECV','INVENTORY','','D','COST',1,'SBRECV - Hasil subcontract'),
            ('SBRECV','WIP','','K','COST',2,'SBRECV - WIP'),

            ('QREJCT','WASTE','','D','COST',1,'QREJCT - Waste/Reject'),
            ('QREJCT','INVENTORY','','K','COST',2,'QREJCT - Persediaan')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       FINANCE
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('PAYMNT','AP','','D','TOTAL',1,'PAYMNT - Hutang/AP'),
            ('PAYMNT','CASH','','K','TOTAL',2,'PAYMNT - Kas/Bank'),

            ('PAYREV','CASH','','D','TOTAL',1,'PAYREV - Kas/Bank'),
            ('PAYREV','AP','','K','TOTAL',2,'PAYREV - Hutang/AP'),

            ('RECVPT','CASH','','D','TOTAL',1,'RECVPT - Kas/Bank'),
            ('RECVPT','AR','','K','TOTAL',2,'RECVPT - Piutang/AR'),

            ('RECREV','AR','','D','TOTAL',1,'RECREV - Piutang/AR'),
            ('RECREV','CASH','','K','TOTAL',2,'RECREV - Kas/Bank'),

            ('CASHIN','CASH','','D','TOTAL',1,'CASHIN - Kas'),
            ('CASHIN','AR','','K','TOTAL',2,'CASHIN - Piutang / lawan'),

            ('CASHOT','AP','','D','TOTAL',1,'CASHOT - Hutang / lawan'),
            ('CASHOT','CASH','','K','TOTAL',2,'CASHOT - Kas'),

            ('CASHAD','CASH','','D','TOTAL',1,'CASHAD - Kas'),
            ('CASHAD','ADJUSTMENT','','K','TOTAL',2,'CASHAD - Akun penyesuaian'),

            ('BANKIN','CASH','','D','TOTAL',1,'BANKIN - Bank/Cash'),
            ('BANKIN','AR','','K','TOTAL',2,'BANKIN - Piutang / lawan'),

            ('BANKOT','AP','','D','TOTAL',1,'BANKOT - Hutang / lawan'),
            ('BANKOT','CASH','','K','TOTAL',2,'BANKOT - Bank/Cash'),

            ('BANKTR','CASH','','D','TOTAL',1,'BANKTR - Bank tujuan'),
            ('BANKTR','CASH','','K','TOTAL',2,'BANKTR - Bank asal'),

            ('BANKCH','COGS','','D','NILAI',1,'BANKCH - Biaya bank'),
            ('BANKCH','CASH','','K','TOTAL',2,'BANKCH - Bank'),

            ('EXPENS','EXPENSE','','D','NILAI',1,'EXPENS - Biaya'),
            ('EXPENS','CASH','','K','TOTAL',2,'EXPENS - Kas/Bank'),

            ('EXPREV','CASH','','D','TOTAL',1,'EXPREV - Kas/Bank'),
            ('EXPREV','EXPENSE','','K','NILAI',2,'EXPREV - Pembalikan biaya'),

            ('OTHINC','CASH','','D','TOTAL',1,'OTHINC - Kas/Bank'),
            ('OTHINC','SALES','','K','NILAI',2,'OTHINC - Pendapatan lain'),

            ('OTHEXP','COGS','','D','NILAI',1,'OTHEXP - Biaya lain'),
            ('OTHEXP','CASH','','K','TOTAL',2,'OTHEXP - Kas/Bank')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       TAX
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('TAXINX','TAX','','D','PAJAK',1,
             'TAXINX - Pajak masukan dari tax_dtl.prk_masukan'),
            ('TAXINX','AP','','K','PAJAK',2,
             'TAXINX - Lawan pajak masukan'),

            ('TAXOUT','AR','','D','PAJAK',1,
             'TAXOUT - Pajak keluaran'),
            ('TAXOUT','TAX','','K','PAJAK',2,
             'TAXOUT - Lawan pajak keluaran'),

            ('TAXADJ','TAX','','D','PAJAK',1,
             'TAXADJ - Pajak penyesuaian'),
            ('TAXADJ','TAX','','K','PAJAK',2,
             'TAXADJ - Lawan pajak penyesuaian')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       COSTING
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('COSTAD','COGS','','D','NILAI',1,'COSTAD - HPP/COGS adjustment'),
            ('COSTAD','INVENTORY','','K','NILAI',2,'COSTAD - Persediaan'),

            ('COSTRV','INVENTORY','','D','NILAI',1,'COSTRV - Persediaan'),
            ('COSTRV','COGS','','K','NILAI',2,'COSTRV - HPP/COGS reversal')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       FIXED ASSET
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('ASSETI','ASSET','','D','NILAI',1,'ASSETI - Aset'),
            ('ASSETI','AP','','K','NILAI',2,'ASSETI - Hutang / lawan'),

            ('ASSETD','ASSET','','D','NILAI',1,'ASSETD - Nilai aset'),
            ('ASSETD','AP','','K','NILAI',2,'ASSETD - Lawan pelepasan'),

            ('DEPREC','EXPENSE','','D','NILAI',1,'DEPREC - Beban penyusutan'),
            ('DEPREC','ASSET','','K','NILAI',2,'DEPREC - Akumulasi / aset'),

            ('ASSETX','ASSET','','D','NILAI',1,'ASSETX - Penyesuaian aset'),
            ('ASSETX','ADJUSTMENT','','K','NILAI',2,'ASSETX - Lawan penyesuaian'),

            ('OPNAST','ASSET','','D','NILAI',1,'OPNAST - Saldo awal aset'),
            ('OPNAST','ADJUSTMENT','','K','NILAI',2,'OPNAST - Akun pembuka')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       OPENING / ACCRUAL / ACCOUNTING ADJUSTMENT / PERIOD
       ================================================================ */

    FOR r IN
        SELECT * FROM (
            VALUES
            ('OPENGL','ADJUSTMENT','','D','NILAI',1,'OPENGL - Opening GL'),
            ('OPENGL','ADJUSTMENT','','K','NILAI',2,'OPENGL - Opening GL counterpart'),

            ('OPENAP','ADJUSTMENT','','D','NILAI',1,'OPENAP - Opening AP'),
            ('OPENAP','ADJUSTMENT','','K','NILAI',2,'OPENAP - Opening AP counterpart'),

            ('OPENAR','ADJUSTMENT','','D','NILAI',1,'OPENAR - Opening AR'),
            ('OPENAR','ADJUSTMENT','','K','NILAI',2,'OPENAR - Opening AR counterpart'),

            ('OPNCAS','ADJUSTMENT','','D','NILAI',1,'OPNCAS - Opening cash'),
            ('OPNCAS','ADJUSTMENT','','K','NILAI',2,'OPNCAS - Opening cash counterpart'),

            ('OPNBNK','ADJUSTMENT','','D','NILAI',1,'OPNBNK - Opening bank'),
            ('OPNBNK','ADJUSTMENT','','K','NILAI',2,'OPNBNK - Opening bank counterpart'),

            ('OPNWIP','ADJUSTMENT','','D','NILAI',1,'OPNWIP - Opening WIP'),
            ('OPNWIP','ADJUSTMENT','','K','NILAI',2,'OPNWIP - Opening WIP counterpart'),

            ('ACCRUA','EXPENSE','','D','NILAI',1,'ACCRUA - Beban accrual'),
            ('ACCRUA','AP','','K','NILAI',2,'ACCRUA - Hutang accrual'),

            ('ACCRRV','AP','','D','NILAI',1,'ACCRRV - Pembalikan hutang accrual'),
            ('ACCRRV','EXPENSE','','K','NILAI',2,'ACCRRV - Pembalikan beban accrual'),

            ('PREPAI','PREPAID','','D','NILAI',1,'PREPAI - Biaya dibayar dimuka'),
            ('PREPAI','CASH','','K','TOTAL',2,'PREPAI - Kas/Bank'),

            ('PREEXP','EXPENSE','','D','NILAI',1,'PREEXP - Pengakuan biaya'),
            ('PREEXP','PREPAID','','K','NILAI',2,'PREEXP - Prepaid'),

            ('ADJGLX','ADJUSTMENT','','D','NILAI',1,'ADJGLX - Penyesuaian GL'),
            ('ADJGLX','ADJUSTMENT','','K','NILAI',2,'ADJGLX - Counter'),

            ('ADJAPX','ADJUSTMENT','','D','NILAI',1,'ADJAPX - Penyesuaian AP'),
            ('ADJAPX','AP','','K','NILAI',2,'ADJAPX - Hutang'),

            ('ADJARX','AR','','D','NILAI',1,'ADJARX - Piutang'),
            ('ADJARX','ADJUSTMENT','','K','NILAI',2,'ADJARX - Penyesuaian'),

            ('ADJCSH','CASH','','D','NILAI',1,'ADJCSH - Kas'),
            ('ADJCSH','ADJUSTMENT','','K','NILAI',2,'ADJCSH - Penyesuaian'),

            ('ADJBKN','CASH','','D','NILAI',1,'ADJBKN - Bank/Cash'),
            ('ADJBKN','ADJUSTMENT','','K','NILAI',2,'ADJBKN - Penyesuaian'),

            ('OPNPER','ADJUSTMENT','','D','NILAI',1,'OPNPER - Opening period'),
            ('OPNPER','ADJUSTMENT','','K','NILAI',2,'OPNPER - Opening period counterpart'),

            ('CLSPER','ADJUSTMENT','','D','NILAI',1,'CLSPER - Closing period'),
            ('CLSPER','ADJUSTMENT','','K','NILAI',2,'CLSPER - Closing period counterpart')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;


    /* ================================================================
       MANUAL ACCOUNTING
       ================================================================

       ACCOUNT          = Perkiraan input
       COUNTER_ACCOUNT  = Perkiraan lawan input
       TAX              = Pajak dari tax_dtl

       Orientasi master:
           ACCOUNT          -> D
           COUNTER_ACCOUNT  -> K
           TAX              -> K

       TAHAP 04 akan menggunakan field debet_kredit transaksi untuk
       membalik orientasi tersebut bila user memilih K.

       value_source:
           ACCOUNT         -> TOTAL
           COUNTER_ACCOUNT -> DPP
           TAX             -> PAJAK
       */

    FOR r IN
        SELECT * FROM (
            VALUES

            /* GENERAL JOURNAL */
            ('JVGENL','ACCOUNT','','D','TOTAL',1,
             'JVGENL - Perkiraan asal dari input transaksi'),
            ('JVGENL','COUNTER_ACCOUNT','','K','DPP',2,
             'JVGENL - Perkiraan lawan dari input transaksi'),
            ('JVGENL','TAX','','K','PAJAK',3,
             'JVGENL - Pajak dari tax_dtl bila ada'),

            /* UANG MUKA TITIPAN */
            ('UMTITP','ACCOUNT','','D','TOTAL',1,
             'UMTITP - Perkiraan utama dari input transaksi'),
            ('UMTITP','COUNTER_ACCOUNT','','K','DPP',2,
             'UMTITP - Perkiraan lawan dari input transaksi'),
            ('UMTITP','TAX','','K','PAJAK',3,
             'UMTITP - Pajak dari tax_dtl bila ada'),

            /* NOTA DEBIT/KREDIT SUPPLIER */
            ('NDKAPD','ACCOUNT','','D','TOTAL',1,
             'NDKAPD - Perkiraan utama Nota Debit Supplier'),
            ('NDKAPD','COUNTER_ACCOUNT','','K','DPP',2,
             'NDKAPD - Perkiraan lawan Nota Debit Supplier'),
            ('NDKAPD','TAX','','K','PAJAK',3,
             'NDKAPD - Pajak dari tax_dtl'),

            ('NDKAPK','ACCOUNT','','K','TOTAL',1,
             'NDKAPK - Perkiraan utama Nota Kredit Supplier'),
            ('NDKAPK','COUNTER_ACCOUNT','','D','DPP',2,
             'NDKAPK - Perkiraan lawan Nota Kredit Supplier'),
            ('NDKAPK','TAX','','D','PAJAK',3,
             'NDKAPK - Pajak dari tax_dtl'),

            /* NOTA DEBIT/KREDIT CUSTOMER */
            ('NDKARD','ACCOUNT','','D','TOTAL',1,
             'NDKARD - Perkiraan utama Nota Debit Customer'),
            ('NDKARD','COUNTER_ACCOUNT','','K','DPP',2,
             'NDKARD - Perkiraan lawan Nota Debit Customer'),
            ('NDKARD','TAX','','K','PAJAK',3,
             'NDKARD - Pajak dari tax_dtl'),

            ('NDKARK','ACCOUNT','','K','TOTAL',1,
             'NDKARK - Perkiraan utama Nota Kredit Customer'),
            ('NDKARK','COUNTER_ACCOUNT','','D','DPP',2,
             'NDKARK - Perkiraan lawan Nota Kredit Customer'),
            ('NDKARK','TAX','','D','PAJAK',3,
             'NDKARK - Pajak dari tax_dtl'),

            /* GIRO */
            ('GIROIN','ACCOUNT','','D','TOTAL',1,
             'GIROIN - Akun giro / bank dari input'),
            ('GIROIN','COUNTER_ACCOUNT','','K','DPP',2,
             'GIROIN - Akun lawan dari input'),
            ('GIROIN','TAX','','K','PAJAK',3,
             'GIROIN - Pajak bila ada'),

            ('GIROUT','ACCOUNT','','K','TOTAL',1,
             'GIROUT - Akun giro / bank dari input'),
            ('GIROUT','COUNTER_ACCOUNT','','D','DPP',2,
             'GIROUT - Akun lawan dari input'),
            ('GIROUT','TAX','','D','PAJAK',3,
             'GIROUT - Pajak bila ada'),

            /* FX */
            ('FXREAL','ACCOUNT','','D','TOTAL',1,
             'FXREAL - Akun utama laba/rugi kurs'),
            ('FXREAL','COUNTER_ACCOUNT','','K','DPP',2,
             'FXREAL - Akun lawan laba/rugi kurs'),

            ('FXUNRL','ACCOUNT','','D','TOTAL',1,
             'FXUNRL - Akun utama revaluasi kurs'),
            ('FXUNRL','COUNTER_ACCOUNT','','K','DPP',2,
             'FXUNRL - Akun lawan revaluasi kurs'),

            /* WRITE OFF */
            ('ARWOFF','ACCOUNT','','D','TOTAL',1,
             'ARWOFF - Akun utama penghapusan piutang'),
            ('ARWOFF','COUNTER_ACCOUNT','','K','DPP',2,
             'ARWOFF - Akun lawan penghapusan piutang'),

            ('APWOFF','ACCOUNT','','D','TOTAL',1,
             'APWOFF - Akun utama penghapusan hutang'),
            ('APWOFF','COUNTER_ACCOUNT','','K','DPP',2,
             'APWOFF - Akun lawan penghapusan hutang'),

            /* BAD DEBT */
            ('BADPRV','ACCOUNT','','D','TOTAL',1,
             'BADPRV - Beban penyisihan'),
            ('BADPRV','COUNTER_ACCOUNT','','K','DPP',2,
             'BADPRV - Akun lawan penyisihan'),

            ('BADREV','ACCOUNT','','D','TOTAL',1,
             'BADREV - Akun pembalikan penyisihan'),
            ('BADREV','COUNTER_ACCOUNT','','K','DPP',2,
             'BADREV - Akun lawan pembalikan penyisihan'),

            /* UNEARNED REVENUE */
            ('UNEARN','ACCOUNT','','D','TOTAL',1,
             'UNEARN - Akun penyesuaian/defer'),
            ('UNEARN','COUNTER_ACCOUNT','','K','DPP',2,
             'UNEARN - Akun deferred revenue'),

            ('UNEREL','ACCOUNT','','D','TOTAL',1,
             'UNEREL - Akun pengakuan revenue'),
            ('UNEREL','COUNTER_ACCOUNT','','K','DPP',2,
             'UNEREL - Akun deferred revenue'),

            /* PAYROLL */
            ('PAYROL','ACCOUNT','','D','TOTAL',1,
             'PAYROL - Akun utama payroll'),
            ('PAYROL','COUNTER_ACCOUNT','','K','DPP',2,
             'PAYROL - Akun lawan payroll'),
            ('PAYROL','TAX','','K','PAJAK',3,
             'PAYROL - Pajak payroll bila ada')
        ) AS x(
            journal_type, account_role, idcoa, debit_credit,
            value_source, seq, description
        )
    LOOP
        UPDATE sc_mst.journal_type_coa
        SET
            account_role = r.account_role,
            idcoa        = r.idcoa,
            debit_credit = r.debit_credit,
            value_source = r.value_source,
            active       = 'YES',
            description  = r.description,
            updatedby    = 'SYSTEM',
            updateddate  = CURRENT_TIMESTAMP
        WHERE TRIM(journal_type::TEXT) = r.journal_type
          AND seq = r.seq;

        IF NOT FOUND THEN
            INSERT INTO sc_mst.journal_type_coa
            (
                journal_type, account_role, idcoa, debit_credit,
                value_source, seq, active, description,
                createdby, createddate
            )
            VALUES
            (
                r.journal_type, r.account_role, r.idcoa, r.debit_credit,
                r.value_source, r.seq, 'YES', r.description,
                'SYSTEM', CURRENT_TIMESTAMP
            );
        END IF;
    END LOOP;

END;
$$;


/*
===========================================================================
VALIDASI DUPLICATE (journal_type, seq)
===========================================================================

Tidak langsung DROP atau DELETE data lama.
Hanya memberikan status agar database lama tidak rusak diam-diam.
*/

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
        RAISE NOTICE
            'WARNING TAHAP 03: duplicate (journal_type, seq) ditemukan: %',
            v_dup;
        RAISE NOTICE
            'Unique index tidak dipaksa dibuat. Rapikan duplicate mapping setelah review.';
    ELSE

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
            ON sc_mst.journal_type_coa(journal_type, seq);
        END IF;

    END IF;

END;
$$;


/* =======================================================================
   VALIDASI KHUSUS JOURNAL TYPE UTAMA
   ======================================================================= */

DO $$
BEGIN

    /* PURCHASE */
    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='PURCHS'
          AND stock_effect='NONE'
          AND accounting_effect='NO'
    ) THEN
        RAISE EXCEPTION 'PURCHS tidak sesuai policy final';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='PURRET'
          AND stock_effect='NONE'
          AND accounting_effect='NO'
    ) THEN
        RAISE EXCEPTION 'PURRET tidak sesuai policy final';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='GRNREC'
          AND stock_effect='IN'
          AND accounting_effect='YES'
    ) THEN
        RAISE EXCEPTION 'GRNREC tidak sesuai policy final';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='GRNRET'
          AND stock_effect='OUT'
          AND accounting_effect='YES'
    ) THEN
        RAISE EXCEPTION 'GRNRET tidak sesuai policy final';
    END IF;

    /* SALES */
    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='DELIVR'
          AND stock_effect='OUT'
          AND accounting_effect='NO'
    ) THEN
        RAISE EXCEPTION 'DELIVR tidak sesuai policy final';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='SALESX'
          AND stock_effect='NONE'
          AND accounting_effect='YES'
    ) THEN
        RAISE EXCEPTION 'SALESX tidak sesuai policy final';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='DELRET'
          AND stock_effect='IN'
          AND accounting_effect='NO'
    ) THEN
        RAISE EXCEPTION 'DELRET tidak sesuai policy final';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT)='SALRET'
          AND stock_effect='NONE'
          AND accounting_effect='YES'
    ) THEN
        RAISE EXCEPTION 'SALRET tidak sesuai policy final';
    END IF;

    /* MANUAL ACCOUNTING */
    IF (
        SELECT COUNT(*)
        FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT) IN
            ('JVGENL','UMTITP','NDKAPD','NDKAPK',
             'NDKARD','NDKARK','GIROIN','GIROUT',
             'FXREAL','FXUNRL','ARWOFF','APWOFF',
             'BADPRV','BADREV','UNEARN','UNEREL','PAYROL')
    ) <> 17 THEN
        RAISE EXCEPTION 'Journal type manual accounting belum lengkap';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_mst.journal_type
        WHERE TRIM(journal_type::TEXT) IN
            ('JVGENL','UMTITP','NDKAPD','NDKAPK',
             'NDKARD','NDKARK','GIROIN','GIROUT',
             'FXREAL','FXUNRL','ARWOFF','APWOFF',
             'BADPRV','BADREV','UNEARN','UNEREL','PAYROL')
          AND (
                stock_effect <> 'NONE'
                OR accounting_effect <> 'YES'
                OR asset_effect <> 'NONE'
              )
    ) THEN
        RAISE EXCEPTION
            'Journal type manual accounting memiliki effect yang tidak sesuai';
    END IF;

    /* GRNREC mapping */
    IF (
        SELECT COUNT(*)
        FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT)='GRNREC'
          AND active='YES'
    ) <> 3 THEN
        RAISE EXCEPTION
            'GRNREC harus memiliki 3 mapping aktif';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT)='GRNREC'
          AND seq=1
          AND account_role='INVENTORY'
          AND debit_credit='D'
          AND value_source='DPP'
          AND active='YES'
    ) THEN
        RAISE EXCEPTION 'GRNREC seq 1 tidak valid';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT)='GRNREC'
          AND seq=2
          AND account_role='TAX'
          AND debit_credit='D'
          AND value_source='PAJAK'
          AND active='YES'
    ) THEN
        RAISE EXCEPTION 'GRNREC seq 2 tidak valid';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.journal_type_coa
        WHERE TRIM(journal_type::TEXT)='GRNREC'
          AND seq=3
          AND account_role='AP'
          AND debit_credit='K'
          AND value_source='TOTAL'
          AND active='YES'
    ) THEN
        RAISE EXCEPTION 'GRNREC seq 3 tidak valid';
    END IF;

END;
$$;


/* =======================================================================
   FINAL SAFETY CHECK - JOURNAL TYPE CODE MUST BE EXACTLY 6 CHARACTERS
   ======================================================================= */

DO $$
DECLARE
    v_bad TEXT;
BEGIN
    SELECT STRING_AGG(
               TRIM(journal_type::TEXT) || '(' ||
               LENGTH(TRIM(journal_type::TEXT))::TEXT || ')',
               ', '
           )
    INTO v_bad
    FROM sc_mst.journal_type
    WHERE LENGTH(TRIM(journal_type::TEXT)) <> 6;

    IF v_bad IS NOT NULL THEN
        RAISE EXCEPTION
            'TAHAP 02 GAGAL: journal_type harus tepat 6 karakter: %',
            v_bad;
    END IF;
END;
$$;


/* =======================================================================
   REPORT TAHAP 02
   ======================================================================= */

SELECT
    TRIM(journal_type::TEXT) AS journal_type,
    journal_name,
    direction,
    module,
    stock_effect,
    accounting_effect,
    asset_effect,
    description
FROM sc_mst.journal_type
ORDER BY
    module,
    TRIM(journal_type::TEXT);


/* =======================================================================
   REPORT TAHAP 03
   ======================================================================= */

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
WHERE active = 'YES'
ORDER BY
    TRIM(journal_type::TEXT),
    seq,
    id;


/* =======================================================================
   REPORT MANUAL ACCOUNTING
   ======================================================================= */

SELECT
    TRIM(journal_type::TEXT) AS journal_type,
    journal_name,
    module,
    stock_effect,
    accounting_effect,
    asset_effect,
    description
FROM sc_mst.journal_type
WHERE TRIM(journal_type::TEXT) IN
(
    'JVGENL','UMTITP',
    'NDKAPD','NDKAPK','NDKARD','NDKARK',
    'GIROIN','GIROUT',
    'FXREAL','FXUNRL',
    'ARWOFF','APWOFF',
    'BADPRV','BADREV',
    'UNEARN','UNEREL',
    'PAYROL'
)
ORDER BY TRIM(journal_type::TEXT);


/*
===========================================================================
SELESAI TAHAP 01-03
===========================================================================

TAHAP 04 berikutnya perlu menambahkan:
1. transaction_dt.counter_idcoa
2. transaction_dt.debet_kredit
3. resolver untuk:
      - operational journal  : master/config/tax
      - manual journal       : idcoa/counter_idcoa/idtax
4. perhitungan DPP/PAJAK/TOTAL sesuai inclusive/exclusive
5. validasi bahwa journal type manual tidak menghasilkan stock.

Jangan mengubah TAHAP 06-18 sebelum TAHAP 04 dan TAHAP 16 disesuaikan
dengan struktur manual accounting tersebut.
===========================================================================
*/

