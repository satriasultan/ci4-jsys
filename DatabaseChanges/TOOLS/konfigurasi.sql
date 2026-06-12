CREATE TABLE konfigurasi_umum (
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