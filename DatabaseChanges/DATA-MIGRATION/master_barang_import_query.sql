CREATE SCHEMA IF NOT EXISTS sc_im;

DROP TABLE IF EXISTS sc_im.mbarang_import CASCADE;

CREATE TABLE sc_im.mbarang_import
(
    kode                 TEXT,
    nama                 TEXT,
    qty_minimum          TEXT,
    prk_persediaan       TEXT,
    satuan               TEXT,
    keterangan           TEXT,
    discontinue          TEXT,
    create_by            TEXT,
    terakhir_simpan      TEXT,
    group_               TEXT,
    berat                TEXT,
    panjang              TEXT,
    lebar                TEXT,
    tinggi               TEXT,
    jenis_barang         TEXT,
    golongan             TEXT,
    jenis_produk         TEXT,
    kelompok_barang      TEXT,
    launching_date       TEXT,
    gudang_default       TEXT,
    spec                 TEXT,
    principal            TEXT,
    batas_expired_date   TEXT,
    fast_moving          TEXT,
    with_serial_no       TEXT,
    prk_surat_jalan      TEXT,
    volume               TEXT,
    lokasi_reff          TEXT,
    satuan_dinkes        TEXT,
    konversi_dinkes      TEXT,
    job                  TEXT,
    approval             TEXT,
    tgl_approved         TEXT,
    approved_by          TEXT,
    prk_revenue          TEXT,
    prk_hpp              TEXT,
    prk_produksi         TEXT,
    kategori_barang      TEXT,
    gross_weight         TEXT,
    kode_barang_tax      TEXT,
    satuan_tax           TEXT
);

ALTER TABLE sc_im.mbarang_import
    OWNER TO postgres;
/* ============================================================
   2. BERSIHKAN STAGING
   ============================================================ */

TRUNCATE TABLE sc_im.mbarang_import;

/* ============================================================
   3. COPY CSV KE STAGING

   CSV asli menggunakan:
   - delimiter   ;
   - encoding    Windows-1252
   - header      TRUE

   COPY membaca file dari filesystem SERVER PostgreSQL.
   ============================================================ */

COPY sc_im.mbarang_import
(
    kode,
    nama,
    qty_minimum,
    prk_persediaan,
    satuan,
    keterangan,
    discontinue,
    create_by,
    terakhir_simpan,
    group_,
    berat,
    panjang,
    lebar,
    tinggi,
    jenis_barang,
    golongan,
    jenis_produk,
    kelompok_barang,
    launching_date,
    gudang_default,
    spec,
    principal,
    batas_expired_date,
    fast_moving,
    with_serial_no,
    prk_surat_jalan,
    volume,
    lokasi_reff,
    satuan_dinkes,
    konversi_dinkes,
    job,
    approval,
    tgl_approved,
    approved_by,
    prk_revenue,
    prk_hpp,
    prk_produksi,
    kategori_barang,
    gross_weight,
    kode_barang_tax,
    satuan_tax
)
FROM 'D:\GITHUB\ci4-jsys\DatabaseChanges\DATA-MIGRATION\master_barang.csv'
WITH
(
    FORMAT CSV,
    HEADER TRUE,
    DELIMITER ';',
    ENCODING 'WIN1252'
);

/* ============================================================
   4. INSERT KE SC_MST.MBARANG
   untuk pengosongan barang
   truncate sc_mst.mbarang;
   ============================================================ */

INSERT INTO sc_mst.mbarang
(
    idbarang,
    nmbarang,
    idgroup,
    idsubgroup,
    idtype,
    grade,
    lsize,
    deflocation,
    defarea,
    description,
    unit,
    subunit,
    subunitenable,
    lastprice,
    onhand,
    allocated,
    uninvoiced,
    tmpalloca,
    lastrxdate,
    lastrxdoc,
    idbarcode,
    sku,
    expdate,
    batch,
    mfgdate,
    maks_daystock,
    chold,
    inputby,
    inputdate,
    status,
    setminstock,
    minstock,
    defaultcurrency,
    kdtax,
    originalno,
    satuantax,
    volume,
    berat,
    gw,
    psize,
    tsize,
    lokasireff,
    idgolonganbarang,
    idjenisproduk,
    idkelompokbarang,
    idprincipal,
    ppersediaan,
    psj,
    salesakun,
    pcogs,
    phpproduksi,
    pjasa,
    pwaste,
    discontinue,
    issn,
    grouptype,
    actualcost,
    lastcost
)
SELECT
    /* 01 */ TRIM(kode),
    /* 02 */ LEFT(TRIM(nama), 150),
    /* 03 */ NULLIF(TRIM(golongan), ''),
    /* 04 */ NULLIF(TRIM(kelompok_barang), ''),
    /* 05 */ NULLIF(TRIM(jenis_produk), ''),
    /* 06 */ LEFT(NULLIF(TRIM(spec), ''), 20),
    /* 07 */ COALESCE(NULLIF(TRIM(lebar), '')::numeric, 0),
    /* 08 */ NULLIF(TRIM(gudang_default), ''),
    /* 09 */ NULLIF(TRIM(lokasi_reff), ''),
    /* 10 */ NULLIF(TRIM(keterangan), ''),
    /* 11 */ NULLIF(TRIM(satuan), ''),
    /* 12 */ NULLIF(TRIM(satuan_dinkes), ''),
    /* 13 */ CASE
                 WHEN NULLIF(TRIM(satuan_dinkes), '') IS NULL THEN 'NO'
                 ELSE 'YES'
             END,
    /* 14 */ 0,
    /* 15 */ 0,
    /* 16 */ 0,
    /* 17 */ 0,
    /* 18 */ 0,
    /* 19 */ NULL,
    /* 20 */ NULL,
    /* 21 */ NULL,
    /* 22 */ NULL,
    /* 23 */ CASE
                 WHEN TRIM(batas_expired_date) ~ '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}'
                 THEN SUBSTRING(
                         TRIM(batas_expired_date)
                         FROM '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}'
                      )::date
                 ELSE NULL
             END,
    /* 24 */ NULL,
    /* 25 */ NULL,
    /* 26 */ NULL,
    /* 27 */ 'NO',
    /* 28 */ NULLIF(TRIM(create_by), ''),
    /* 29 */ CASE
                 WHEN TRIM(terakhir_simpan) ~ '^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4} [0-9]{1,2}[.][0-9]{2}$'
                 THEN TO_TIMESTAMP(TRIM(terakhir_simpan), 'DD/MM/YYYY HH24.MI')
                 ELSE CURRENT_TIMESTAMP
             END,
    /* 30 */ 'F',
    /* 31 */ CASE
                 WHEN COALESCE(NULLIF(TRIM(qty_minimum), '')::numeric, 0) > 0
                 THEN 'YES'
                 ELSE 'NO'
             END,
    /* 32 */ COALESCE(NULLIF(TRIM(qty_minimum), '')::numeric, 0),
    /* 33 */ 'IDR',
    /* 34 */ NULLIF(TRIM(kode_barang_tax), ''),
    /* 35 */ NULL,
    /* 36 */ NULLIF(TRIM(satuan_tax), ''),
    /* 37 */ COALESCE(NULLIF(TRIM(volume), '')::numeric, 0),
    /* 38 */ COALESCE(NULLIF(TRIM(berat), '')::numeric, 0),
    /* 39 */ COALESCE(NULLIF(TRIM(gross_weight), '')::numeric, 0),
    /* 40 */ COALESCE(NULLIF(TRIM(panjang), '')::numeric, 0),
    /* 41 */ COALESCE(NULLIF(TRIM(tinggi), '')::numeric, 0),
    /* 42 */ NULLIF(TRIM(lokasi_reff), ''),
    /* 43 */ NULLIF(TRIM(golongan), ''),
    /* 44 */ NULLIF(TRIM(jenis_produk), ''),
    /* 45 */ NULLIF(TRIM(kelompok_barang), ''),
    /* 46 */ NULLIF(TRIM(principal), ''),
    /* 47 */ NULLIF(TRIM(prk_persediaan), ''),
    /* 48 */ NULLIF(TRIM(prk_surat_jalan), ''),
    /* 49 */ NULLIF(TRIM(prk_revenue), ''),
    /* 50 */ NULLIF(TRIM(prk_hpp), ''),
    /* 51 */ NULLIF(TRIM(prk_produksi), ''),
    /* 52 */ NULL,
    /* 53 */ NULL,
    /* 54 */     CASE
        WHEN UPPER(TRIM(discontinue)) = 'DISCONTINUE'
            THEN 'YES'
        ELSE 'NO'
    END,
    /* 55 */ NULLIF(TRIM(with_serial_no), ''),
    /* 56 */ CASE
                 WHEN UPPER(TRIM(group_)) = 'JASA' THEN 'JASA'
                 ELSE 'STOCK'
             END,
    /* 57 */ 0,
    /* 58 */ 0
FROM sc_im.mbarang_import
WHERE NULLIF(TRIM(kode), '') IS NOT NULL
ON CONFLICT (idbarang)
DO UPDATE SET
    nmbarang          = EXCLUDED.nmbarang,
    idgroup           = EXCLUDED.idgroup,
    idsubgroup        = EXCLUDED.idsubgroup,
    idtype            = EXCLUDED.idtype,
    grade             = EXCLUDED.grade,
    lsize             = EXCLUDED.lsize,
    deflocation       = EXCLUDED.deflocation,
    defarea           = EXCLUDED.defarea,
    description       = EXCLUDED.description,
    unit              = EXCLUDED.unit,
    subunit           = EXCLUDED.subunit,
    subunitenable     = EXCLUDED.subunitenable,
    setminstock       = EXCLUDED.setminstock,
    minstock          = EXCLUDED.minstock,
    defaultcurrency   = EXCLUDED.defaultcurrency,
    kdtax             = EXCLUDED.kdtax,
    satuantax         = EXCLUDED.satuantax,
    volume            = EXCLUDED.volume,
    berat             = EXCLUDED.berat,
    gw                = EXCLUDED.gw,
    psize             = EXCLUDED.psize,
    tsize             = EXCLUDED.tsize,
    lokasireff        = EXCLUDED.lokasireff,
    idgolonganbarang  = EXCLUDED.idgolonganbarang,
    idjenisproduk     = EXCLUDED.idjenisproduk,
    idkelompokbarang  = EXCLUDED.idkelompokbarang,
    idprincipal       = EXCLUDED.idprincipal,
    ppersediaan       = EXCLUDED.ppersediaan,
    psj               = EXCLUDED.psj,
    salesakun         = EXCLUDED.salesakun,
    pcogs             = EXCLUDED.pcogs,
    phpproduksi       = EXCLUDED.phpproduksi,
    discontinue       = EXCLUDED.discontinue,
    issn              = EXCLUDED.issn,
    grouptype         = EXCLUDED.grouptype;


/* ============================================================
   5. INSERT DEFAULT UNIT KE SC_MST.MBARANG_UNIT

   Setiap barang yang mempunyai Satuan akan dibuatkan:
   basic_value = 1
   conv_value  = 1
   cdefault    = YES
   chold       = NO
   ============================================================ */

INSERT INTO sc_mst.mbarang_unit
(
    idbarang,
    idunit,
    basic_value,
    conv_value,
    inputdate,
    inputby,
    cdefault,
    chold
)
SELECT DISTINCT
    TRIM(kode),
    TRIM(satuan),
    1,
    1,
    CURRENT_TIMESTAMP,
    NULLIF(TRIM(create_by), ''),
    'YES',
    'NO'
FROM sc_im.mbarang_import
WHERE NULLIF(TRIM(kode), '') IS NOT NULL
  AND NULLIF(TRIM(satuan), '') IS NOT NULL
ON CONFLICT (idbarang, idunit) DO NOTHING;


