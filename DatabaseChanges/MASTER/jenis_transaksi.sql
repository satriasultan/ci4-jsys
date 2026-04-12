-- =========================================
-- DROP (optional kalau mau reset)
-- =========================================
DROP TABLE IF EXISTS sc_mst.jenistransaksi CASCADE;

-- =========================================
-- CREATE TABLE
-- =========================================
CREATE TABLE sc_mst.jenistransaksi (
    id SERIAL PRIMARY KEY,

    kodetransaksi VARCHAR(20) UNIQUE,
    namatransaksi VARCHAR(100),
    kategori VARCHAR(50),

    -- FLAG MODUL
    is_pembelian BOOLEAN DEFAULT FALSE,
    is_penjualan BOOLEAN DEFAULT FALSE,
    is_inventory BOOLEAN DEFAULT FALSE,
    is_produksi BOOLEAN DEFAULT FALSE,
    is_finance BOOLEAN DEFAULT FALSE,

    -- ARAH STOK
    stok_in BOOLEAN DEFAULT FALSE,
    stok_out BOOLEAN DEFAULT FALSE,

    -- COA DEFAULT (FALLBACK)
    coa_debet VARCHAR(20),
    coa_kredit VARCHAR(20),

    -- CONTROL
    chold CHAR(4) DEFAULT 'NO',
    status CHAR(1) DEFAULT 'A',

    inputdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    inputby VARCHAR(20),
    updatedate TIMESTAMP,
    updateby VARCHAR(20)
);

-- =========================================
-- INSERT DATA (FULL ERP)
-- =========================================
INSERT INTO sc_mst.jenistransaksi
(kodetransaksi, namatransaksi, kategori,
 is_pembelian, is_penjualan, is_inventory, is_produksi, is_finance,
 stok_in, stok_out,
 coa_debet, coa_kredit,
 inputby)

VALUES

-- =========================
-- PEMBELIAN
-- =========================
('GR','PENERIMAAN BARANG','PEMBELIAN',TRUE,FALSE,FALSE,FALSE,FALSE,TRUE,FALSE,'1.2.1','213101','SYSTEM'),
('AP_INV','INVOICE PEMBELIAN','PEMBELIAN',TRUE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,'511101','213101','SYSTEM'),
('RETUR_BELI','RETUR PEMBELIAN','PEMBELIAN',TRUE,FALSE,FALSE,FALSE,FALSE,FALSE,TRUE,'213101','1.2.1','SYSTEM'),
('AP_PAJAK','PPN MASUKAN','PEMBELIAN',TRUE,FALSE,FALSE,FALSE,FALSE,FALSE,FALSE,'116101','213101','SYSTEM'),

-- =========================
-- PENJUALAN
-- =========================
('SO','PENJUALAN','PENJUALAN',FALSE,TRUE,FALSE,FALSE,FALSE,FALSE,TRUE,'113101','411101','SYSTEM'),
('AR_INV','INVOICE PENJUALAN','PENJUALAN',FALSE,TRUE,FALSE,FALSE,FALSE,FALSE,FALSE,'113101','411101','SYSTEM'),
('RETUR_JUAL','RETUR PENJUALAN','PENJUALAN',FALSE,TRUE,FALSE,FALSE,FALSE,TRUE,FALSE,'411101','113101','SYSTEM'),
('AR_PAJAK','PPN KELUARAN','PENJUALAN',FALSE,TRUE,FALSE,FALSE,FALSE,FALSE,FALSE,'113101','216101','SYSTEM'),

-- =========================
-- INVENTORY
-- =========================
('ADJ_IN','ADJUSTMENT MASUK','INVENTORY',FALSE,FALSE,TRUE,FALSE,FALSE,TRUE,FALSE,'1.2.1','511101','SYSTEM'),
('ADJ_OUT','ADJUSTMENT KELUAR','INVENTORY',FALSE,FALSE,TRUE,FALSE,FALSE,FALSE,TRUE,'511101','1.2.1','SYSTEM'),
('TRANSFER','TRANSFER GUDANG','INVENTORY',FALSE,FALSE,TRUE,FALSE,FALSE,TRUE,TRUE,NULL,NULL,'SYSTEM'),

-- =========================
-- PRODUKSI (BOM)
-- =========================
('BOM_IN','HASIL PRODUKSI','PRODUKSI',FALSE,FALSE,FALSE,TRUE,FALSE,TRUE,FALSE,'1.2.1','1.2.2','SYSTEM'),
('BOM_OUT','PEMAKAIAN BAHAN','PRODUKSI',FALSE,FALSE,FALSE,TRUE,FALSE,FALSE,TRUE,'1.2.2','1.2.1','SYSTEM'),

-- =========================
-- HPP
-- =========================
('HPP','HARGA POKOK PENJUALAN','PRODUKSI',FALSE,FALSE,FALSE,TRUE,FALSE,FALSE,FALSE,'511101','1.2.1','SYSTEM'),

-- =========================
-- FINANCE
-- =========================
('KAS_MASUK','KAS MASUK','FINANCE',FALSE,FALSE,FALSE,FALSE,TRUE,FALSE,FALSE,'111001','113101','SYSTEM'),
('KAS_KELUAR','KAS KELUAR','FINANCE',FALSE,FALSE,FALSE,FALSE,TRUE,FALSE,FALSE,'213101','111001','SYSTEM'),

-- =========================
-- UANG MUKA
-- =========================
('UM_BELI','UANG MUKA PEMBELIAN','FINANCE',TRUE,FALSE,FALSE,FALSE,TRUE,FALSE,FALSE,'117101','111001','SYSTEM'),
('UM_JUAL','UANG MUKA PENJUALAN','FINANCE',FALSE,TRUE,FALSE,FALSE,TRUE,FALSE,FALSE,'111001','218101','SYSTEM');