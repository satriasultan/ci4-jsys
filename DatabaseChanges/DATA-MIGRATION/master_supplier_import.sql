/* ============================================================
   MASTER SUPPLIER IMPORT — FINAL (dengan TRUNCATE)
   Target : sc_mst.mstsupplier
   ============================================================ */

CREATE SCHEMA IF NOT EXISTS sc_im;

DROP TABLE IF EXISTS sc_im.msupplier_import CASCADE;

/* ============================================================
   1. STAGING TABLE
   ============================================================ */
CREATE TABLE sc_im.msupplier_import
(
    kode                    TEXT,
    nama                    TEXT,
    cp                      TEXT,
    phone                   TEXT,
    fax                     TEXT,
    alamat                  TEXT,
    kode_post               TEXT,
    npwp                    TEXT,
    keterangan              TEXT,
    kota                    TEXT,
    market                  TEXT,
    email                   TEXT,
    jabatan                 TEXT,
    npkp                    TEXT,
    periode_jatuh_tempo     TEXT
);

ALTER TABLE sc_im.msupplier_import OWNER TO postgres;

/* ============================================================
   2. BERSIHKAN STAGING
   ============================================================ */
TRUNCATE TABLE sc_im.msupplier_import;

/* ============================================================
   3. COPY CSV KE STAGING
   ============================================================ */
COPY sc_im.msupplier_import
(
    kode, nama, cp, phone, fax, alamat, kode_post, npwp, keterangan,
    kota, market, email, jabatan, npkp, periode_jatuh_tempo
)
FROM 'D:\GITHUB\ci4-jsys\DatabaseChanges\DATA-MIGRATION\master_supplier.csv'
WITH
(
    FORMAT CSV,
    HEADER TRUE,
    DELIMITER ';',
    ENCODING 'WIN1252'
);

/* ============================================================
   4. HAPUS SEMUA DATA SUPPLIER LAMA  ⬅️ NEW
   ============================================================ */
TRUNCATE TABLE sc_mst.mstsupplier RESTART IDENTITY CASCADE;

/* ============================================================
   5. INSERT DATA BARU
   ============================================================ */
INSERT INTO sc_mst.mstsupplier
(
    kdsupplier,
    nmsupplier,
    alamat,
    idkota,
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
    interngroup,
    blacklist,
    status,
    createdby,
    createddate,
    idprovinsi,
    chold
)
SELECT
    LEFT(TRIM(i.kode), 30),
    LEFT(TRIM(i.nama), 250),
    NULLIF(TRIM(i.alamat), ''),
    LEFT(NULLIF(TRIM(i.kota), ''), 20),
    LEFT(NULLIF(TRIM(i.phone), ''), 20),
    LEFT(NULLIF(TRIM(i.fax), ''), 30),
    LEFT(NULLIF(TRIM(i.email), ''), 150),
    LEFT(NULLIF(TRIM(i.npwp), ''), 30),
    LEFT(NULLIF(TRIM(i.npkp), ''), 30),
    COALESCE(NULLIF(TRIM(i.periode_jatuh_tempo), '')::numeric, 30),   -- jthtempo
    0,                                                                 -- plafon
    LEFT(NULLIF(TRIM(i.market), ''), 10),
    LEFT(NULLIF(TRIM(i.cp), ''), 150),
    LEFT(NULLIF(TRIM(i.jabatan), ''), 100),
    NULLIF(TRIM(i.keterangan), ''),
    'N',                       -- interngroup: Non Internal
    'NO',                      -- blacklist
    'A',                       -- status: Active
    'MIGRATION',               -- createdby
    CURRENT_TIMESTAMP,         -- createddate
    NULL,                      -- idprovinsi
    'NO'                       -- chold ✅ default 'NO'
FROM sc_im.msupplier_import i
WHERE NULLIF(TRIM(i.kode), '') IS NOT NULL;


update  sc_mst.mstsupplier SET status='F';
update  sc_mst.customer SET status='F';


/* ============================================================
   6. CEK HASIL
   ============================================================ */
-- SELECT COUNT(*) FROM sc_mst.mstsupplier;
-- SELECT kdsupplier, nmsupplier, jthtempo, chold
-- FROM sc_mst.mstsupplier
-- ORDER BY kdsupplier
-- LIMIT 20;