/* ini master konfigurasi umum tidak ada sc_mst.konfigurasi_umum  

clue pengambilan transaksi ,

--settingan di sc_mst.mbarang atau master barang yang digunakan
--kolom persediaan;
idgroup -> ada JSA * BRG , membedakan barang dan jasa, jika jasa tidak masuk stok saat lpb tapi masuk hutang dagang (dari currency) & perkiraan jasa pjasa
ppersediaan -> untuk default perkiraan persediaan 
psj  -> untuk default perkiraan surat jalan
pcogs  -> untuk default perkiraan hpp / master barang
phppproduksi   -> untuk default perkiraan hpp produksi
pjasa   -> untuk default perkiraan jasa
pwaste   -> untuk default perkiraan pembuangan



#default hpp perkiraan tidak dari master perbarang









*/


CREATE TABLE sc_mst.konfigurasi_umum (
    id INT PRIMARY KEY DEFAULT 1,
    -- PEMBELIAN
    pp character(10),
    voidpp character(10),
    po character(10),
    voidpo character(10),
    lpb character(10),
    returbeli character(10),
    refundbeli character(10),
    -- PENJUALAN
    salesorder character(10),
    voidso character(10),
    deliveryorder character(10),
    suratjalan character(10),
    penjualan character(10),
    penjualannon character(10),
    returpenjualan character(10),
    retursj character(10),
    refundjual character(10),
    -- PRODUKSI
    workorder character(10),
    workorderexecution character(10),
    materialrelease character(10),
    bpnm character(10), -- BIAYA PROD NON MATERIAL
    penerimaanbarangprod character(10),
    setorantarbagian character(10),
    pmkbarang character(10),
    pnmbarang character(10),
    -- KAS / BANK
    kasmasuk character(10),
    kaskeluar character(10),
    bankmasuk character(10),
    bankkeluar character(10),
    setorangiro character(10),
    pencairangiro character(10),
    tolakangiro character(10),
    buktikaskecil character(10),
    -- FAKTUR PAJAK
    fpm character(10), -- FAKTUR PAJAK MASUKAN
    fpk character(10), -- FAKTUR PAJAK KELUARAN
    bppph character(10), -- BUKTI PUNGUT PPH
    -- LAIN - LAIN
    notadk character(10), -- NOTA DEBIT / KREDIT
    jurnalumump character(10), -- JURNAL UMUM PERKIRAAN
    ptal character(10), -- PERINTAH TRANSF ANT LOKASI
    koreksihargajual character(10), 
    adjusmentstock character(10),

    -- PERKIRAAN
    hpp character(20), -- Harga Pokok Penjualan
    labakurs character(20),
    rugikurs character(20),
    ldtb character(20), -- Laba Ditahan Th Berjalan
    ldtl character(20), -- Laba Ditahan Th Lalu
    pproduksi character(20), -- Perkiraan Produksi
    -- DEFAULT
    idtax character(20),
    ispajak character(6),
    currcode character(10),
    gudang character(20),
    kaskecil character(20),
    pkas character(20),
    ppersediaan character(20),
    psj character(20), -- Perkiraan Surat Jalan
    pselisih character(20), -- Perkiraan Selisih
    gudangretail character(20),
    pmutasimasuk character(20),
    pmutasikeluar character(20),
    prefixnofp character(20), -- PREFIX NO FAKTUR PAJAK
    sembunyilokasi character(6),

    inputby character varying(50) COLLATE pg_catalog."default",
    inputdate timestamp without time zone,
    updateby character varying(50) COLLATE pg_catalog."default",
    updatedate timestamp without time zone,
    
    CONSTRAINT only_one_row CHECK (id = 1)
);

-- Trigger untuk update updated_at di PostgreSQL
-- CREATE OR REPLACE FUNCTION update_updated_at_column()
-- RETURNS TRIGGER AS $$
-- BEGIN
--     NEW.updated_at = CURRENT_TIMESTAMP;
--     RETURN NEW;
-- END;
-- $$ language 'plpgsql';

-- CREATE TRIGGER update_konfigurasi_updated_at 
--     BEFORE UPDATE ON konfigurasi_umum 
--     FOR EACH ROW 
--     EXECUTE FUNCTION update_updated_at_column();



ALTER TABLE sc_mst.konfigurasi_umum
    ADD COLUMN IF NOT EXISTS transferantarlokasi character(10),
    ADD COLUMN IF NOT EXISTS buktikaskecil2 character(10),
    ADD COLUMN IF NOT EXISTS bahasa character varying(20),
    ADD COLUMN IF NOT EXISTS securemode boolean DEFAULT false,
    ADD COLUMN IF NOT EXISTS hargatermasukpajak boolean DEFAULT false;
	
	
	INSERT INTO sc_mst.konfigurasi_umum
(
    id,

    -- PEMBELIAN
    pp,
    voidpp,
    po,
    voidpo,
    lpb,
    returbeli,
    refundbeli,

    -- PENJUALAN
    salesorder,
    voidso,
    deliveryorder,
    suratjalan,
    penjualan,
    penjualannon,
    returpenjualan,
    retursj,
    refundjual,

    -- PRODUKSI
    workorder,
    workorderexecution,
    materialrelease,
    bpnm,
    penerimaanbarangprod,
    setorantarbagian,
    pmkbarang,
    pnmbarang,

    -- KAS / BANK
    kasmasuk,
    kaskeluar,
    bankmasuk,
    bankkeluar,
    setorangiro,
    pencairangiro,
    tolakangiro,
    buktikaskecil,
    buktikaskecil2,

    -- FAKTUR PAJAK
    fpm,
    fpk,
    bppph,

    -- LAIN-LAIN
    notadk,
    jurnalumump,
    ptal,
    transferantarlokasi,
    koreksihargajual,
    adjusmentstock,

    -- PERKIRAAN
    hpp,
    labakurs,
    rugikurs,
    ldtb,
    ldtl,
    pproduksi,

    -- DEFAULT
    idtax,
    ispajak,
    currcode,
    gudang,
    kaskecil,
    pkas,
    ppersediaan,
    psj,
    pselisih,
    gudangretail,
    pmutasimasuk,
    pmutasikeluar,
    prefixnofp,
    sembunyilokasi,

    -- USER CONFIG
    bahasa,
    securemode,
    hargatermasukpajak,

    -- AUDIT
    inputby,
    inputdate,
    updateby,
    updatedate
)
VALUES
(
    1,

    -- PEMBELIAN
    'PPB',
    'VPP',
    'POB',
    'VPO',
    'LPB',
    'RBL',
    'RFD',

    -- PENJUALAN
    'SOJ',
    'VSO',
    'DOR',
    'SJL',
    'INV',
    'INV',
    'RJN',
    'RSJ',
    'RFK',

    -- PRODUKSI
    'WO',
    'WOE',
    'PMR',
    'BNM',
    'PRO',
    'TAB',
    'PBL',
    'TBR',

    -- KAS / BANK
    'BKM',
    'BKK',
    'BBM',
    'BBK',
    'STG',
    'CGR',
    'TLG',
    'BPK',
    'BPM',

    -- FAKTUR PAJAK
    'FPM',
    'FPK',
    'BPH',

    -- LAIN-LAIN
    'NTD',
    'JUP',
    'OTB',
    'TRL',
    'HJL',
    'JBR',

    -- PERKIRAAN
    '5511',
    '722103',
    '722101',
    '331201',
    '331101',
    '115201',

    -- DEFAULT
    'PPN11',
    'N',
    'IDR',
    '16101',
    '1111.02',
    '1111.01',
    '115401',
    '115501',
    '5511.1',
    '16101',
    NULL,
    NULL,
    NULL,
    'Y',

    -- USER CONFIG
    'Indonesia',
    TRUE,
    FALSE,

    -- AUDIT
    'SYSTEM',
    CURRENT_TIMESTAMP,
    'SYSTEM',
    CURRENT_TIMESTAMP
)
ON CONFLICT (id)
DO UPDATE SET

    -- PEMBELIAN
    pp                    = EXCLUDED.pp,
    voidpp                = EXCLUDED.voidpp,
    po                    = EXCLUDED.po,
    voidpo                = EXCLUDED.voidpo,
    lpb                   = EXCLUDED.lpb,
    returbeli             = EXCLUDED.returbeli,
    refundbeli            = EXCLUDED.refundbeli,

    -- PENJUALAN
    salesorder            = EXCLUDED.salesorder,
    voidso                = EXCLUDED.voidso,
    deliveryorder         = EXCLUDED.deliveryorder,
    suratjalan            = EXCLUDED.suratjalan,
    penjualan             = EXCLUDED.penjualan,
    penjualannon          = EXCLUDED.penjualannon,
    returpenjualan        = EXCLUDED.returpenjualan,
    retursj                = EXCLUDED.retursj,
    refundjual            = EXCLUDED.refundjual,

    -- PRODUKSI
    workorder             = EXCLUDED.workorder,
    workorderexecution    = EXCLUDED.workorderexecution,
    materialrelease       = EXCLUDED.materialrelease,
    bpnm                  = EXCLUDED.bpnm,
    penerimaanbarangprod  = EXCLUDED.penerimaanbarangprod,
    setorantarbagian      = EXCLUDED.setorantarbagian,
    pmkbarang             = EXCLUDED.pmkbarang,
    pnmbarang             = EXCLUDED.pnmbarang,

    -- KAS / BANK
    kasmasuk              = EXCLUDED.kasmasuk,
    kaskeluar             = EXCLUDED.kaskeluar,
    bankmasuk             = EXCLUDED.bankmasuk,
    bankkeluar             = EXCLUDED.bankkeluar,
    setorangiro           = EXCLUDED.setorangiro,
    pencairangiro         = EXCLUDED.pencairangiro,
    tolakangiro           = EXCLUDED.tolakangiro,
    buktikaskecil         = EXCLUDED.buktikaskecil,
    buktikaskecil2        = EXCLUDED.buktikaskecil2,

    -- FAKTUR PAJAK
    fpm                   = EXCLUDED.fpm,
    fpk                   = EXCLUDED.fpk,
    bppph                 = EXCLUDED.bppph,

    -- LAIN-LAIN
    notadk                = EXCLUDED.notadk,
    jurnalumump           = EXCLUDED.jurnalumump,
    ptal                  = EXCLUDED.ptal,
    transferantarlokasi   = EXCLUDED.transferantarlokasi,
    koreksihargajual      = EXCLUDED.koreksihargajual,
    adjusmentstock        = EXCLUDED.adjusmentstock,

    -- PERKIRAAN
    hpp                   = EXCLUDED.hpp,
    labakurs              = EXCLUDED.labakurs,
    rugikurs              = EXCLUDED.rugikurs,
    ldtb                  = EXCLUDED.ldtb,
    ldtl                  = EXCLUDED.ldtl,
    pproduksi             = EXCLUDED.pproduksi,

    -- DEFAULT
    idtax                 = EXCLUDED.idtax,
    ispajak               = EXCLUDED.ispajak,
    currcode              = EXCLUDED.currcode,
    gudang                = EXCLUDED.gudang,
    kaskecil              = EXCLUDED.kaskecil,
    pkas                  = EXCLUDED.pkas,
    ppersediaan           = EXCLUDED.ppersediaan,
    psj                   = EXCLUDED.psj,
    pselisih              = EXCLUDED.pselisih,
    gudangretail          = EXCLUDED.gudangretail,
    pmutasimasuk          = EXCLUDED.pmutasimasuk,
    pmutasikeluar         = EXCLUDED.pmutasikeluar,
    prefixnofp            = EXCLUDED.prefixnofp,
    sembunyilokasi        = EXCLUDED.sembunyilokasi,

    -- USER CONFIG
    bahasa                = EXCLUDED.bahasa,
    securemode            = EXCLUDED.securemode,
    hargatermasukpajak    = EXCLUDED.hargatermasukpajak,

    -- AUDIT
    updateby              = EXCLUDED.updateby,
    updatedate            = CURRENT_TIMESTAMP;