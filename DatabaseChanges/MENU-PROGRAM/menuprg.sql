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
('JTS', 3, 'I.P.A.3', 'PURCHASE ORDER (PO)', 'I.P', 'I.P.A', 'P', false, 'fa-lightbulb-o', 'purchase/trans/po', 'LEFT', 'NO');

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