-- MASTER DATA --

INSERT INTO sc_mst.menuprg (
    branch, 
    urut, 
    kodemenu, 
    namamenu, 
    parentmenu, 
    parentsub, 
    child, 
    holdmenu, 
    iconmenu, 
    linkmenu, 
    menuposition, 
    chold
) VALUES 
('JTS', 1, 'I.M.B.1', 'MASTER CUSTOMER', 'I.M', 'I.M.B', 'P', false, 'fa-lightbulb-o', 'master/data/customer', 'LEFT', 'NO'),
('JTS', 2, 'I.M.B.2', 'MASTER SUPPLIER', 'I.M', 'I.M.B', 'P', false, 'fa-list-alt', 'master/data/supplier', 'LEFT', 'NO'),
('JTS', 3, 'I.M.B.3', 'MASTER TAX', 'I.M', 'I.M.B', 'P', false, 'fa-calendar', 'master/data/tax', 'LEFT', 'NO'),
('JTS', 4, 'I.M.B.4', 'MASTER CURRENCY', 'I.M', 'I.M.B', 'P', false, 'fa-list-alt', 'master/data/currency', 'LEFT', 'NO'),
('JTS', 5, 'I.M.B.5', 'MASTER COA', 'I.M', 'I.M.B', 'P', false, 'fa-list-alt', 'master/data/coa', 'LEFT', 'NO'),
('JTS', 6, 'I.M.B.6', 'MASTER JOB', 'I.M', 'I.M.B', 'P', false, 'fa-folder', 'master/data/job', 'LEFT', 'NO'),
('JTS', 7, 'I.M.B.7', 'MASTER WAREHOUSE', 'I.M', 'I.M.B', 'P', false, 'fa-external-link', 'master/data/location', 'LEFT', 'NO'),
('JTS', 8, 'I.M.B.8', 'MASTER SUB LOCATION', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/area', 'LEFT', 'NO'),
('JTS', 9, 'I.M.B.9', 'MASTER COST CENTER', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/cc', 'LEFT', 'NO'),

('JTS', 10, 'I.M.B.10', 'MASTER BARANG', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/barang', 'LEFT', 'NO'),
('JTS', 11, 'I.M.B.11', 'MASTER GOLONGAN BARANG', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/golonganbarang', 'LEFT', 'NO'),
('JTS', 12, 'I.M.B.12', 'MASTER JENIS PRODUK', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/jenisproduk', 'LEFT', 'NO'),
('JTS', 13, 'I.M.B.13', 'MASTER KELOMPOK BARANG', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/kelompokbarang', 'LEFT', 'NO'),
('JTS', 14, 'I.M.B.14', 'MASTER PRINCIPAL', 'I.M', 'I.M.B', 'P', false, 'fa-traceability', 'master/data/principal', 'LEFT', 'NO');




-- PEMBELIAN
INSERT INTO sc_mst.menuprg (
    branch, 
    urut, 
    kodemenu, 
    namamenu, 
    parentmenu, 
    parentsub, 
    child, 
    holdmenu, 
    iconmenu, 
    linkmenu, 
    menuposition, 
    chold
) VALUES 
('JTS', 1, 'I.P', 'PEMBELIAN', '0', '0', 'U', false, 'fa-cart-plus', '', 'LEFT', 'NO'),
('JTS', 1, 'I.P.A', 'TRANSAKSI', 'I.P', '0', 'S', false, 'fa-right-left', '', 'LEFT', 'NO'),
('JTS', 1, 'I.P.A.1', 'PERMINTAAN PEMBELIAN(PP)', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/pp', 'LEFT', 'NO'),
('JTS', 2, 'I.P.A.2', 'VOID PP', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/voidpp', 'LEFT', 'NO'),
('JTS', 3, 'I.P.A.3', 'PURCHASE ORDER (PO)', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/po', 'LEFT', 'NO'),
('JTS', 4, 'I.P.A.4', 'VOID PO', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/voidpo', 'LEFT', 'NO'),
('JTS', 5, 'I.P.A.5', 'UANG MUKA PEMBELIAN', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/umb', 'LEFT', 'NO'),
('JTS', 6, 'I.P.A.6', 'PENERIMAAN PEMBELIAN', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/lpb', 'LEFT', 'NO'),
('JTS', 7, 'I.P.A.7', 'RETUR PEMBELIAN', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/returbeli', 'LEFT', 'NO');

--update sc_mst.menuprg set urut=2 where kodemenu='I.P';



--PERSEDIAAN I.Q

INSERT INTO sc_mst.menuprg (
    branch, 
    urut, 
    kodemenu, 
    namamenu, 
    parentmenu, 
    parentsub, 
    child, 
    holdmenu, 
    iconmenu, 
    linkmenu, 
    menuposition, 
    chold
) VALUES 
('JTS', 4, 'I.Q', 'PERSEDIAAN', '0', '0', 'U', false, 'fa-cart-plus', '', 'LEFT', 'NO'),
('JTS', 1, 'I.Q.A', 'TRANSAKSI', 'I.Q', '0', 'S', false, 'fa-right-left', '', 'LEFT', 'NO'),
('JTS', 1, 'I.Q.A.1', 'PERINTAH TRANSFER LOKASI', 'I.Q', 'I.Q.A', 'P', false, 'fa-lightbulb-o', 'persediaan/trans/perintah_transfer', 'LEFT', 'NO'),
('JTS', 2, 'I.Q.A.2', 'TRANSFER ANTAR LOKASI', 'I.Q', 'I.Q.A', 'P', false, 'fa-lightbulb-o', 'persediaan/trans/transfer_lokasi', 'LEFT', 'NO'),
('JTS', 3, 'I.Q.A.3', 'AJUSTMENT STOCK', 'I.Q', 'I.Q.A', 'P', false, 'fa-lightbulb-o', 'persediaan/trans/ajustment_stock', 'LEFT', 'NO'),
('JTS', 4, 'I.Q.A.4', 'AJUSTMENT ITEM (VALUE)', 'I.Q', 'I.Q.A', 'P', false, 'fa-lightbulb-o', 'persediaan/trans/ajustment_item_value', 'LEFT', 'NO'),
('JTS', 5, 'I.Q.A.5', 'PEMAKAIAN BARANG', 'I.Q', 'I.Q.A', 'P', false, 'fa-lightbulb-o', 'persediaan/trans/pmk_brng', 'LEFT', 'NO'),
('JTS', 6, 'I.Q.A.6', 'PENERIMAAN BARANG', 'I.Q', 'I.Q.A', 'P', false, 'fa-lightbulb-o', 'persediaan/trans/pnm_barang', 'LEFT', 'NO');



-- PENJUALAN

--DELETE TERLEBIH DAHULU
DELETE FROM sc_mst.menuprg where kodemenu like '%I.S%'
--SALES
INSERT INTO sc_mst.menuprg (
    branch, urut, kodemenu, namamenu, parentmenu, parentsub, child, holdmenu, iconmenu, linkmenu, menuposition, chold
) VALUES
('JTS', 6, 'I.S', 'PENJUALAN', '', '', 'U', 'false', 'fa-area-chart', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.S.A', 'PRE PENJUALAN', 'I.S', '', 'S', 'false', 'fa-list-alt', '#', 'LEFT', 'NO'),
-- ('JTS', 1, 'I.S.A.1', 'TASK MANAGEMENT', 'I.S', 'I.S.A', 'P', 'false', 'fa-tasks', 'sales/presales', 'LEFT', 'NO'),
('JTS', 2, 'I.S.A.2', 'PRICE PROPOSAL', 'I.S', 'I.S.A', 'P', 'false', 'fa-handshake-o', 'sales/presales/priceproposal', 'LEFT', 'NO'),
('JTS', 3, 'I.S.A.3', 'PROFORMA INVOICE', 'I.S', 'I.S.A', 'P', 'false', 'fa-money', 'sales/presales/performainvoice', 'LEFT', 'NO'),
('JTS', 2, 'I.S.B', 'POST PENJUALAN', 'I.S', '', 'S', 'false', 'fa-handshake-o', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.S.B.1', 'SALES ORDER', 'I.S', 'I.S.B', 'P', 'false', 'fa-file-text', 'sales/postsales/salesorder', 'LEFT', 'NO'),
('JTS', 2, 'I.S.B.2', 'PENJUALAN', 'I.S', 'I.S.B', 'P', 'false', 'fa-file-text', 'sales/postsales/penjualan', 'LEFT', 'NO'),
('JTS', 3, 'I.S.B.3', 'SOI', 'I.S', 'I.S.B', 'P', 'false', 'fa-file-text-o', 'sales/postsales/soi', 'LEFT', 'NO'),
('JTS', 4, 'I.S.B.4', 'SALES ORDER EXTERNAL', 'I.S', 'I.S.B', 'P', 'false', 'fa-file-text', 'sales/postsales/salesorderexternal', 'LEFT', 'NO'),
-- ('JTS', 5, 'I.S.B.5', 'DELIVERY SPEC', 'I.S', 'I.S.B', 'P', 'false', 'fa-list', 'sales/postsales/deliveryspec', 'LEFT', 'NO'),
-- ('JTS', 6, 'I.S.B.6', 'DELIVERY ORDER', 'I.S', 'I.S.B', 'P', 'false', 'fa-truck', 'sales/postsales/deliveryorder', 'LEFT', 'NO')
;





-- TOOLS

--DELETE TERLEBIH DAHULU
DELETE FROM sc_mst.menuprg where kodemenu like '%I.T%'
--TOOLS
INSERT INTO sc_mst.menuprg (
    branch, urut, kodemenu, namamenu, parentmenu, parentsub, child, holdmenu, iconmenu, linkmenu, menuposition, chold
) VALUES
('JTS', 6, 'I.T', 'TOOLS', '', '', 'U', 'false', 'fa-wrench', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.T.A', 'SETTING AWAL', 'I.T', '', 'S', 'false', 'fa-list-alt', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.T.A.1', 'SETTING TANGGAL AWAL', 'I.T', 'I.T.A', 'P', 'false', 'fa-tasks', 'tools/settingawal', 'LEFT', 'NO'),
('JTS', 2, 'I.T.A.2', 'SALDO AWAL HUTANG/PIUTANG', 'I.T', 'I.T.A', 'P', 'false', 'fa-handshake-o', 'tools/settingawal/saldoawalhp', 'LEFT', 'NO'),
('JTS', 3, 'I.T.A.3', 'PROSES SALDO AWAL H/P', 'I.T', 'I.T.A', 'P', 'false', 'fa-money', 'tools/settingawal/prosessaldoawalhp', 'LEFT', 'NO'),
('JTS', 2, 'I.T.B', 'KONFIGURASI', 'I.T', '', 'S', 'false', 'fa-cogs', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.T.B.1', 'KONFIGURASI', 'I.T', 'I.T.B', 'P', 'false', 'fa-file-text', 'tools/konfigurasi', 'LEFT', 'NO'),
('JTS', 2, 'I.T.B.2', 'SETTING', 'I.T', 'I.T.B', 'P', 'false', 'fa-file-text', 'tools/konfigurasi/setting', 'LEFT', 'NO'),
('JTS', 3, 'I.T.B.3', 'BLOCK/UNBLOCK PERIOD', 'I.T', 'I.T.B', 'P', 'false', 'fa-file-text', 'tools/konfigurasi/blockunblockperiod', 'LEFT', 'NO'),
('JTS', 3, 'I.T.C', 'PROSES', 'I.T', '', 'S', 'false', 'fa-refresh', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.T.C.1', 'PROSES TUTUP BULAN', 'I.T', 'I.T.C', 'P', 'false', 'fa-file-text', 'tools/proses/tutupbulan', 'LEFT', 'NO'),

-- ('JTS', 3, 'I.T.B.3', 'SALES ORDER EXTERNAL', 'I.T', 'I.T.B', 'P', 'false', 'fa-file-text', 'sales/postsales/salesorderexternal', 'LEFT', 'NO'),
-- ('JTS', 4, 'I.T.B.4', 'SOI', 'I.T', 'I.T.B', 'P', 'false', 'fa-file-text-o', 'sales/postsales/soi', 'LEFT', 'NO'),
-- ('JTS', 5, 'I.T.B.5', 'DELIVERY SPEC', 'I.T', 'I.T.B', 'P', 'false', 'fa-list', 'sales/postsales/deliveryspec', 'LEFT', 'NO'),
-- ('JTS', 6, 'I.T.B.6', 'DELIVERY ORDER', 'I.T', 'I.T.B', 'P', 'false', 'fa-truck', 'sales/postsales/deliveryorder', 'LEFT', 'NO')
;



-- AR/AP

DELETE FROM sc_mst.menuprg where kodemenu like '%I.L%'
--AR/AP
INSERT INTO sc_mst.menuprg (
    branch, urut, kodemenu, namamenu, parentmenu, parentsub, child, holdmenu, iconmenu, linkmenu, menuposition, chold
) VALUES
('JTS', 6, 'I.L', 'AR/AP', '', '', 'U', 'false', 'fa-book', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.L.A', 'TRANSAKSI', 'I.L', '', 'S', 'false', 'fa-list', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.L.A.1', 'NOTA DEBIT / KREDIT', 'I.L', 'I.L.A', 'P', 'false', 'fa-tasks', 'arap/transaksi/ndk', 'LEFT', 'NO'),
('JTS', 2, 'I.L.B', 'LAPORAN', 'I.L', '', 'S', 'false', 'fa-file-text', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.L.B.1', 'LAP. NOTA DEBIT & KREDIT', 'I.L', 'I.L.B', 'P', 'false', 'fa-tasks', 'arap/report/lapndk', 'LEFT', 'NO'),
-- ('JTS', 2, 'I.L.A.2', 'SALDO AWAL HUTANG/PIUTANG', 'I.L', 'I.L.A', 'P', 'false', 'fa-handshake-o', 'arap/settingawal/saldoawalhp', 'LEFT', 'NO'),
-- ('JTS', 3, 'I.L.A.3', 'PROSES SALDO AWAL H/P', 'I.L', 'I.L.A', 'P', 'false', 'fa-money', 'arap/settingawal/prosessaldoawalhp', 'LEFT', 'NO'),
-- ('JTS', 2, 'I.L.B', 'KONFIGURASI', 'I.L', '', 'S', 'false', 'fa-cogs', '#', 'LEFT', 'NO'),
-- ('JTS', 1, 'I.L.B.1', 'KONFIGURASI', 'I.L', 'I.L.B', 'P', 'false', 'fa-file-text', 'arap/konfigurasi', 'LEFT', 'NO'),
-- ('JTS', 2, 'I.L.B.2', 'SETTING', 'I.L', 'I.L.B', 'P', 'false', 'fa-file-text', 'arap/konfigurasi/setting', 'LEFT', 'NO'),
-- ('JTS', 3, 'I.L.B.3', 'BLOCK/UNBLOCK PERIOD', 'I.L', 'I.L.B', 'P', 'false', 'fa-file-text', 'arap/konfigurasi/blockunblockperiod', 'LEFT', 'NO');



-- PAJAK

DELETE FROM sc_mst.menuprg where kodemenu like '%I.J%'
-- PAJAK
INSERT INTO sc_mst.menuprg (
    branch, urut, kodemenu, namamenu, parentmenu, parentsub, child, holdmenu, iconmenu, linkmenu, menuposition, chold
) VALUES
('JTS', 6, 'I.J', 'PAJAK', '', '', 'U', 'false', 'fa-percent', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.J.A', 'LAPORAN', 'I.J', '', 'S', 'false', 'fa-list', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.J.A.1', 'LAPORAN PAJAK', 'I.J', 'I.J.A', 'P', 'false', 'fa-tasks', 'pajak/transaksi/laporan', 'LEFT', 'NO')



-- KEUANGAN & ACCOUNTING

DELETE FROM sc_mst.menuprg where kodemenu like '%I.K%'
--KEUANGAN & ACCOUNTING
INSERT INTO sc_mst.menuprg (
    branch, urut, kodemenu, namamenu, parentmenu, parentsub, child, holdmenu, iconmenu, linkmenu, menuposition, chold
) VALUES
('JTS', 6, 'I.K', 'KEUANGAN & ACCOUNTING', '', '', 'U', 'false', 'fa-dollar', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.K.A', 'ACCOUNTING', 'I.K', '', 'S', 'false', 'fa-book', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.K.A.1', 'JURNAL UMUM PERKIRAAN', 'I.K', 'I.K.A', 'P', 'false', 'fa-tasks', 'ka/accounting/jup', 'LEFT', 'NO'),
('JTS', 2, 'I.K.B', 'FINANCE', 'I.K', '', 'S', 'false', 'fa-money', '#', 'LEFT', 'NO'),
('JTS', 1, 'I.K.B.1', 'UANG MUKA TITIPAN', 'I.K', 'I.K.B', 'P', 'false', 'fa-tasks', 'ka/finance/umt', 'LEFT', 'NO'),
('JTS', 1, 'I.K.B.2', 'PENERIMAAN KAS/BANK', 'I.K', 'I.K.B', 'P', 'false', 'fa-tasks', 'ka/finance/penerimaankb', 'LEFT', 'NO'),
('JTS', 1, 'I.K.B.3', 'PENGELUARAN KAS/BANK', 'I.K', 'I.K.B', 'P', 'false', 'fa-tasks', 'ka/finance/pengeluarankb', 'LEFT', 'NO'),
-- ('JTS', 2, 'I.K.A.2', 'SALDO AWAL HUTANG/PIUTANG', 'I.K', 'I.K.A', 'P', 'false', 'fa-handshake-o', 'arap/settingawal/saldoawalhp', 'LEFT', 'NO'),
-- ('JTS', 3, 'I.K.A.3', 'PROSES SALDO AWAL H/P', 'I.K', 'I.K.A', 'P', 'false', 'fa-money', 'arap/settingawal/prosessaldoawalhp', 'LEFT', 'NO'),
-- ('JTS', 2, 'I.K.B', 'KONFIGURASI', 'I.K', '', 'S', 'false', 'fa-cogs', '#', 'LEFT', 'NO'),
-- ('JTS', 1, 'I.K.B.1', 'KONFIGURASI', 'I.K', 'I.K.B', 'P', 'false', 'fa-file-text', 'arap/konfigurasi', 'LEFT', 'NO'),
-- ('JTS', 2, 'I.K.B.2', 'SETTING', 'I.K', 'I.K.B', 'P', 'false', 'fa-file-text', 'arap/konfigurasi/setting', 'LEFT', 'NO'),
-- ('JTS', 3, 'I.K.B.3', 'BLOCK/UNBLOCK PERIOD', 'I.K', 'I.K.B', 'P', 'false', 'fa-file-text', 'arap/konfigurasi/blockunblockperiod', 'LEFT', 'NO');





--PRODUKSI I.R



--PRODUKSI I.R
DELETE FROM sc_mst.menuprg
WHERE kodemenu IN (
    'I.R',
    'I.R.A',
    'I.R.A.1',
    'I.R.A.2',
    'I.R.A.3',
    'I.R.A.4',
    'I.R.A.5',
    'I.R.A.6',
    'I.R.A.7',
    'I.R.A.8',
    'I.R.A.9',
    'I.R.A.10'
);
INSERT INTO sc_mst.menuprg (
    branch, 
    urut, 
    kodemenu, 
    namamenu, 
    parentmenu, 
    parentsub, 
    child, 
    holdmenu, 
    iconmenu, 
    linkmenu, 
    menuposition, 
    chold
) VALUES 
('JTS', 7, 'I.R', 'PRODUCTION', '0', '0', 'U', false, 'fa-cart-plus', '', 'LEFT', 'NO'),
('JTS', 1, 'I.R.A', 'TRANSAKSI', 'I.R', '0', 'S', false, 'fa-right-left', '', 'LEFT', 'NO'),
('JTS', 1, 'I.R.A.1', 'STANDART COST', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/standart_cost', 'LEFT', 'NO'),
('JTS', 2, 'I.R.A.2', 'BIAYA STANDART COST', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/biaya_standart', 'LEFT', 'NO'),
('JTS', 3, 'I.R.A.3', 'BILL OF MATERIAL (BOM)', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/bom', 'LEFT', 'NO'),
('JTS', 4, 'I.R.A.4', 'WORK ORDER', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/wo', 'LEFT', 'NO'),
('JTS', 5, 'I.R.A.5', 'WORK ORDER EXECUTION', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/woe', 'LEFT', 'NO'),
('JTS', 6, 'I.R.A.6', 'SETOR ANTAR BAGIAN', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/setorbagian', 'LEFT', 'NO'),
('JTS', 7, 'I.R.A.7', 'TERIMA ANTAR BAGIAN', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/terimabagian', 'LEFT', 'NO'),
('JTS', 8, 'I.R.A.8', 'MATERIAL RELEASE', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/mr', 'LEFT', 'NO'),
('JTS', 9, 'I.R.A.9', 'PENERIMAAN BRG PRODUKSI', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/pnm_produksi', 'LEFT', 'NO'),
('JTS', 10, 'I.R.A.10', 'BIAYA PROD NON MATERIAL', 'I.R', 'I.R.A', 'P', false, 'fa-lightbulb-o', 'production/trans/biaya_produksi_non_material', 'LEFT', 'NO');
