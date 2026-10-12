/* ============================================================
   MASTER CUSTOMER IMPORT
   Target : sc_mst.customer
   Fix    : tidak pakai ON CONFLICT (anti error 42P10)
   ============================================================ */

CREATE SCHEMA IF NOT EXISTS sc_im;

DROP TABLE IF EXISTS sc_im.mcustomer_import CASCADE;

/* ============================================================
   1. STAGING TABLE (semua TEXT, sesuai header CSV)
   ============================================================ */
CREATE TABLE sc_im.mcustomer_import
(
    kode                    TEXT,   -- 01 Kode
    nama                    TEXT,   -- 02 Nama
    cp                      TEXT,   -- 03 CP
    phone                   TEXT,   -- 04 Phone
    fax                     TEXT,   -- 05 Fax
    alamat                  TEXT,   -- 06 Alamat
    kode_post               TEXT,   -- 07 Kode Post
    npwp                    TEXT,   -- 08 NPWP
    keterangan              TEXT,   -- 09 Keterangan
    max_plafon              TEXT,   -- 10 Max Plafon
    kota                    TEXT,   -- 11 Kota
    market                  TEXT,   -- 12 Market
    blacklist               TEXT,   -- 13 BlackList
    nama_npwp               TEXT,   -- 14 Nama NPWP
    alamat_npwp             TEXT,   -- 15 Alamat NPWP
    kota_npwp               TEXT,   -- 16 Kota NPWP
    email                   TEXT,   -- 17 Email
    jabatan                 TEXT,   -- 18 Jabatan
    npkp                    TEXT,   -- 19 NPKP
    grade                   TEXT,   -- 20 Grade
    kode_retur              TEXT,   -- 21 Kode Retur
    salesman                TEXT,   -- 22 Salesman
    kolektor                TEXT,   -- 23 Kolektor
    bill_to                 TEXT,   -- 24 Bill To
    send_to                 TEXT,   -- 25 Send To
    status_fpj              TEXT,   -- 26 Status FPJ
    periode_jatuh_tempo     TEXT,   -- 27 Periode Jatuh Tempo
    jenis_id_pembeli        TEXT,   -- 28 Jenis ID Pembeli
    id_tku_pembeli          TEXT,   -- 29 ID TKU Pembeli
    nama_psa                TEXT,   -- 30 Nama PSA
    email_psa               TEXT,   -- 31 Email PSA
    telp_psa                TEXT,   -- 32 Telp PSA
    no_ijin_psa             TEXT,   -- 33 No.Ijin PSA
    nama_apoteker           TEXT,   -- 34 Nama Apoteker
    kode_stra_apoteker      TEXT,   -- 35 Kode STRA Apoteker
    no_sipa_sika_apoteker   TEXT,   -- 36 No.SIPA/SIKA Apoteker
    telp_apoteker           TEXT,   -- 37 Telp Apoteker
    email_apoteker          TEXT,   -- 38 Email Apoteker
    alamat_apoteker         TEXT,   -- 39 Alamat Apoteker
    masa_berlaku_sipa       TEXT,   -- 40 Masa Berlaku No.SIPA/SIKA
    masa_berlaku_psa        TEXT    -- 41 Masa Berlaku PSA
);

ALTER TABLE sc_im.mcustomer_import OWNER TO postgres;

/* ============================================================
   2. BERSIHKAN STAGING
   ============================================================ */
TRUNCATE TABLE sc_im.mcustomer_import;

/* ============================================================
   3. COPY CSV KE STAGING
   ============================================================ */
COPY sc_im.mcustomer_import
(
    kode, nama, cp, phone, fax, alamat, kode_post, npwp, keterangan,
    max_plafon, kota, market, blacklist, nama_npwp, alamat_npwp,
    kota_npwp, email, jabatan, npkp, grade, kode_retur, salesman,
    kolektor, bill_to, send_to, status_fpj, periode_jatuh_tempo,
    jenis_id_pembeli, id_tku_pembeli, nama_psa, email_psa, telp_psa,
    no_ijin_psa, nama_apoteker, kode_stra_apoteker,
    no_sipa_sika_apoteker, telp_apoteker, email_apoteker,
    alamat_apoteker, masa_berlaku_sipa, masa_berlaku_psa
)
FROM 'D:\GITHUB\ci4-jsys\DatabaseChanges\DATA-MIGRATION\master_customer.csv'
WITH
(
    FORMAT CSV,
    HEADER TRUE,
    DELIMITER ';',
    ENCODING 'WIN1252'
);

/* ============================================================
   4. HAPUS DATA LAMA (opsional, komentar kalau tidak ingin wipe)
   ============================================================ */
-- DELETE FROM sc_mst.customer;

/* ============================================================
   5. UPDATE DATA YANG SUDAH ADA (match by kdcustomer)
   ============================================================ */
UPDATE sc_mst.customer c
SET
    nmcustomer           = LEFT(TRIM(i.nama), 200),

    alamat1              = NULLIF(TRIM(i.alamat), ''),
    alamat_kantor        = NULLIF(TRIM(i.alamat), ''),
    kodepos_kantor       = LEFT(NULLIF(TRIM(i.kode_post), ''), 10),
    kota_kantor          = LEFT(NULLIF(TRIM(i.kota), ''), 100),

    alamat_pengiriman    = NULLIF(TRIM(i.send_to), ''),
    kodepos_pengiriman   = LEFT(NULLIF(TRIM(i.kode_post), ''), 10),
    kota_pengiriman      = LEFT(NULLIF(TRIM(i.kota), ''), 100),

    alamat_penagihan     = NULLIF(TRIM(i.bill_to), ''),
    kodepos_penagihan    = LEFT(NULLIF(TRIM(i.kode_post), ''), 10),
    kota_penagihan       = LEFT(NULLIF(TRIM(i.kota), ''), 100),

    phone                = LEFT(NULLIF(TRIM(i.phone), ''), 20),
    fax                  = LEFT(NULLIF(TRIM(i.fax), ''), 30),
    email                = LEFT(NULLIF(TRIM(i.email), ''), 150),
    npwp                 = LEFT(NULLIF(TRIM(i.npwp), ''), 30),
    npkp                 = LEFT(NULLIF(TRIM(i.npkp), ''), 30),
    jthtempo             = COALESCE(NULLIF(TRIM(i.periode_jatuh_tempo), '')::numeric, 30),
    plafon               = COALESCE(NULLIF(TRIM(i.max_plafon), '')::numeric, 0),
    idmarket             = LEFT(NULLIF(TRIM(i.market), ''), 10),
    cp                   = LEFT(NULLIF(TRIM(i.cp), ''), 150),
    jabatan              = LEFT(NULLIF(TRIM(i.jabatan), ''), 100),
    keterangan           = NULLIF(TRIM(i.keterangan), ''),
    blacklist            = CASE
                              WHEN UPPER(TRIM(i.blacklist)) LIKE 'YES%' THEN 'YES'
                              ELSE 'NO'
                           END,
    namanpwp             = LEFT(NULLIF(TRIM(i.nama_npwp), ''), 50),
    alamatnpwp           = NULLIF(TRIM(i.alamat_npwp), ''),
    idkotanpwp           = LEFT(NULLIF(TRIM(i.kota_npwp), ''), 20),
    idtku                = LEFT(NULLIF(TRIM(i.id_tku_pembeli), ''), 100),
    docnopembeli         = LEFT(NULLIF(TRIM(i.jenis_id_pembeli), ''), 100),
    grade                = LEFT(NULLIF(TRIM(i.grade), ''), 30),
    salesman             = LEFT(NULLIF(TRIM(i.salesman), ''), 30),
    kolektor             = LEFT(NULLIF(TRIM(i.kolektor), ''), 30),
    koderetur            = LEFT(NULLIF(TRIM(i.kode_retur), ''), 20),
    defaultfor           = LEFT(NULLIF(TRIM(i.status_fpj), ''), 30),
    updateby             = 'MIGRATION',
    updatedate           = CURRENT_TIMESTAMP
FROM sc_im.mcustomer_import i
WHERE c.kdcustomer = LEFT(TRIM(i.kode), 20);

/* ============================================================
   6. INSERT DATA BARU (yang belum ada di customer)
   select * from sc_mst.customer
   ============================================================ */
INSERT INTO sc_mst.customer
(
    kdcustomer,
    nmcustomer,

    alamat1,
    alamat2,

    alamat_kantor,
    kodepos_kantor,
    kota_kantor,

    alamat_pengiriman,
    kodepos_pengiriman,
    kota_pengiriman,

    alamat_penagihan,
    kodepos_penagihan,
    kota_penagihan,

    phone,
    fax,
    email,
    npwp,
    npkp,
    jthtempo,
    plafon,
    idmarket,
    cp,
    jabatan,
    keterangan,
    blacklist,
    namanpwp,
    alamatnpwp,
    idkotanpwp,
    idtku,
    docnopembeli,
    grade,
    salesman,
    kolektor,
    koderetur,

    defaultfor,
    status,
    createdby,
    createddate,

    chold
)
SELECT
    LEFT(TRIM(i.kode), 20),
    LEFT(TRIM(i.nama), 200),

    NULLIF(TRIM(i.alamat), ''),
    NULL,

    NULLIF(TRIM(i.alamat), ''),
    LEFT(NULLIF(TRIM(i.kode_post), ''), 10),
    LEFT(NULLIF(TRIM(i.kota), ''), 100),

    NULLIF(TRIM(i.send_to), ''),
    LEFT(NULLIF(TRIM(i.kode_post), ''), 10),
    LEFT(NULLIF(TRIM(i.kota), ''), 100),

    NULLIF(TRIM(i.bill_to), ''),
    LEFT(NULLIF(TRIM(i.kode_post), ''), 10),
    LEFT(NULLIF(TRIM(i.kota), ''), 100),

    LEFT(NULLIF(TRIM(i.phone), ''), 20),
    LEFT(NULLIF(TRIM(i.fax), ''), 30),
    LEFT(NULLIF(TRIM(i.email), ''), 150),
    LEFT(NULLIF(TRIM(i.npwp), ''), 30),
    LEFT(NULLIF(TRIM(i.npkp), ''), 30),
    COALESCE(NULLIF(TRIM(i.periode_jatuh_tempo), '')::numeric, 30),
    COALESCE(NULLIF(TRIM(i.max_plafon), '')::numeric, 0),
    LEFT(NULLIF(TRIM(i.market), ''), 10),
    LEFT(NULLIF(TRIM(i.cp), ''), 150),
    LEFT(NULLIF(TRIM(i.jabatan), ''), 100),
    NULLIF(TRIM(i.keterangan), ''),
    CASE
        WHEN UPPER(TRIM(i.blacklist)) LIKE 'YES%' THEN 'YES'
        ELSE 'NO'
    END,
    LEFT(NULLIF(TRIM(i.nama_npwp), ''), 50),
    NULLIF(TRIM(i.alamat_npwp), ''),
    LEFT(NULLIF(TRIM(i.kota_npwp), ''), 20),
    LEFT(NULLIF(TRIM(i.id_tku_pembeli), ''), 100),
    LEFT(NULLIF(TRIM(i.jenis_id_pembeli), ''), 100),
    LEFT(NULLIF(TRIM(i.grade), ''), 30),
    LEFT(NULLIF(TRIM(i.salesman), ''), 30),
    LEFT(NULLIF(TRIM(i.kolektor), ''), 30),
    LEFT(NULLIF(TRIM(i.kode_retur), ''), 20),

    LEFT(NULLIF(TRIM(i.status_fpj), ''), 30),
    'A',
    'MIGRATION',
    CURRENT_TIMESTAMP,

    'NO'
FROM sc_im.mcustomer_import i
WHERE NULLIF(TRIM(i.kode), '') IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM sc_mst.customer c
      WHERE c.kdcustomer = LEFT(TRIM(i.kode), 20)
  );

/* ============================================================
   7. (Opsional) Cek hasil
   ============================================================ */
-- SELECT COUNT(*) FROM sc_mst.customer;
-- SELECT kdcustomer, nmcustomer, kota_kantor
-- FROM sc_mst.customer
-- ORDER BY kdcustomer
-- LIMIT 20;