CASE MASUK DAN KELUAR GUDANG , CONTOH GUDANG KECIL - EAF  
DB: 10.0.0.4:5430 DB_Wms_Scrap

--#lihat stock akhir
select a.onhand,a.allocated,a.tmpalloca,a.onhand - (coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)) as sisastock,b.nmbarang,a.* 
from sc_mst.stkgdw a left outer join sc_mst.mbarang b on a.idbarang=b.idbarang  where a.idlocation='16901' and a.idbarang='01040100000002';

--#history transaksi masuk & keluar
select qty_in,qty_out,qty_sld,b.nmbarang,a.* from sc_trx.stkblc a left outer join sc_mst.mbarang b on a.idbarang=b.idbarang  
where a.idlocation='16901' and a.idbarang='01040100000002' order by   a.idlocation DESC, a.idarea DESC, a.batch DESC, a.idbarang DESC, a.idsort DESC,a.trxdate DESC;




--#tambah stock
-- TAMBAH STOCK BUSHELING - IN (penambahan di qty in otomatis hitung)
INSERT INTO sc_trx.stkblc (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, qty_in, qty_out, qty_sld, hist, ctype, pricelst_in, pricelst_out, pricelst_sld, picby, unit, subunit, description) 
VALUES 
('16901', '16901.0000', '', '01040100000002', '2026-09-10 00:00:00', 'IN',  'PAF/2504/PA00ZZ', 'PLANT I', 99, 0, 0, 'TESTED IN', 'SCRAP', 0.00, 0.00, 0.00, '', 'KG', '', 'SALDO AWAL');

--#kurang stock (penambahan di qty out otomatis hitung)
INSERT INTO sc_trx.stkblc (idlocation, idarea, batch, idbarang, trxdate, doctype, docno, docref, qty_in, qty_out, qty_sld, hist, ctype, pricelst_in, pricelst_out, pricelst_sld, picby, unit, subunit, description) 
VALUES 
('16901', '16901.0000', '', '01040100000002', '2026-09-10 00:00:00', 'OUT', 'PAF/2504/PA00ZZ', 'PLANT I', 0, 99, 0, 'TESTED OUT', 'SCRAP', 0.00, 0.00, 0.00, '', 'KG', '', 'J34067');

