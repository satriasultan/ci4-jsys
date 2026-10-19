-- ============================================================
-- MLOCATION.SQL - ONE RUN INSTALL SCRIPT
-- Membuat / melengkapi tabel sc_mst.mlocation + insert data
-- + mengisi kolom idbranchjob & nmbranchjob
-- ============================================================

BEGIN;

-- ============================================================
-- 1. CREATE TABLE (kalau belum ada)
-- ============================================================
CREATE TABLE IF NOT EXISTS sc_mst.mlocation
(
    id serial,
    idlocation      character(12)  COLLATE pg_catalog."default" NOT NULL,
    nmlocation      character(60)  COLLATE pg_catalog."default",
    chold           character(5)   COLLATE pg_catalog."default",
    description     text           COLLATE pg_catalog."default",
    idcomplex       character(30)  COLLATE pg_catalog."default",
    idbarcode       character(30)  COLLATE pg_catalog."default",
    inputdate       timestamp without time zone,
    inputby         character(20)  COLLATE pg_catalog."default",
    updatedate      timestamp without time zone,
    updateby        character(20)  COLLATE pg_catalog."default",
    idarea_default  character(30)  COLLATE pg_catalog."default",
    pselisih        character(20),
    pbiaya          character(20),          -- dari ALTER
    idbranchjob     character(20),          -- dari ALTER
    nmbranchjob     character(50),          -- dari ALTER
    CONSTRAINT mlocation_pkey PRIMARY KEY (idlocation)
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS sc_mst.mlocation
    OWNER to postgres;

-- ============================================================
-- 2. ALTER TABLE (aman jika kolom sudah ada)
-- ============================================================
ALTER TABLE sc_mst.mlocation
    ADD COLUMN IF NOT EXISTS pbiaya      character(20),
    ADD COLUMN IF NOT EXISTS idbranchjob character(20),
    ADD COLUMN IF NOT EXISTS nmbranchjob character(50);

-- ============================================================
-- 3. INSERT DATA (idempotent — pakai ON CONFLICT)
-- ============================================================
INSERT INTO sc_mst.mlocation
    (idlocation, nmlocation, chold, pselisih)
VALUES
    ('16101','GUDANG BARANG JADI BILLET','NO',5141),
    ('16102','GUDANG BARANG JADI BESI BETON','NO',5141),
    ('16103','GUDANG BARANG JADI FESI','NO',5141),
    ('16104','GUDANG BARANG JADI FOUNDRY','NO',5141),
    ('16105','GUDANG BARANG JADI LAIN - LAIN','NO',5141),
    ('16201','GUDANG SETENGAH JADI BILLET','NO',5141),
    ('16202','GUDANG SETENGAH JADI FOUNDRY','NO',5141),
    ('16203','GUDANG PENAMPUNGAN SCRAP','NO',5141),
    ('16204','GUDANG TRANSIT ALLOY DAN SUBMAT','NO',5141),
    ('16301','GUDANG BAHAN BAKU FESI','NO',5141),
    ('16302','GUDANG BAHAN BAKU BILLET','NO',5141),
    ('16303','GUDANG BAHAN BAKU FOUNDRY','NO',5141),
    ('16304','GUDANG SCRAP FRESH PLANT 1','NO',5141),
    ('16305','GUDANG SCRAP UNFRESH PLANT 1','NO',5141),
    ('16401','GUDANG BAHAN PENOLONG','NO',5141),
    ('16402','GUDANG BAHAN PENOLONG FESI','NO',5141),
    ('16501','GUDANG BARANG JADI NEW ROLLING ( JTS 2 )','NO',5141),
    ('16502','GUDANG BARANG SETENGAH JADI NEW ROLLING ( JTS 2 )','NO',5141),
    ('16503','GUDANG BAHAN PEMBANTU NEW ROLLING ( JTS 2 )','NO',5141),
    ('16504','GUDANG BARANG SPAREPART NEW ROLLING ( JTS 2 )','NO',5141),
    ('16505','GUDANG BARANG SPAREPART NEW ROLLING ( JTS 1 )','NO',5141),
    ('16506','GUDANG BAHAN BAKU NEW ROLLING ( JTS 2 )','NO',5141),
    ('16507','GUDANG KECIL NEW ROLLING_ELECTRIC ( JTS 2 )','NO',5141),
    ('16508','GUDANG KECIL NEW ROLLING_BAR INSPECTION ( JTS 2 )','NO',5141),
    ('16509','GUDANG KECIL NEW ROLLING MILL (JTS 2)','NO',5141),
    ('16601','GUDANG BARANG DEAD STOCK','NO',5141),
    ('16701','GUDANG P1 -  MEMINJAMKAN PERSEDIAAN KE EKSTERNAL','NO',5141),
    ('16702','GUDANG P1 -  PEMINJAMAN PERSEDIAAN DARI EKSTERNAL','NO',5141),
    ('16703','GUDANG P2 -  MEMINJAMKAN PERSEDIAAN KE EKSTERNAL','NO',5141),
    ('16704','GUDANG P2 -  PEMINJAMAN PERSEDIAAN DARI EKSTERNAL','NO',5141),
    ('16901','GUDANG KECIL - EAF (BARU)','NO',5141),
    ('16902','GUDANG KECIL - LF (BARU)','NO',5141),
    ('16903','GUDANG KECIL - CCM (BARU)','NO',5141),
    ('16904','GUDANG KECIL BI','NO',5141),
    ('16905','GUDANG KECIL REFRACTORIES','NO',5141),
    ('16906','GUDANG KECIL QC 1','NO',5141),
    ('16907','GUDANG KECIL ELECTRIC 1','NO',5141),
    ('16908','GUDANG KECIL LAB CHEMICAL','NO',5141),
    ('16909','GUDANG KECIL CONSUMABLE BIL INSP','NO',5141),
    ('19001','GUDANG TAMPUNGAN KOREKSI STOK OPNAME PLANT 1','NO',5141),
    ('19002','GUDANG TAMPUNGAN KOREKSI STOK OPNAME PLANT 2','NO',5141),
    ('GK-1','GUDANG KECIL - EAF','NO',5141),
    ('GK-2','GUDANG KECIL - LF','NO',5141),
    ('GK-3','GUDANG KECIL - CCM','NO',5141)
ON CONFLICT (idlocation) DO NOTHING;

-- ============================================================
-- 4. ISI KOLOM idbranchjob & nmbranchjob BERDASARKAN POLA
-- ============================================================

-- 4.a. Default untuk semua: kosong dulu
UPDATE sc_mst.mlocation
SET idbranchjob = NULL,
    nmbranchjob = NULL;

-- 4.b. Pattern A: kode berawalan '16' + digit ke-3 = '5' → PLANT II
--      Contoh: 16501–16509 → PLANT II
UPDATE sc_mst.mlocation
SET idbranchjob = '01.02',
    nmbranchjob = 'PLANT II'
WHERE substring(idlocation FROM 1 FOR 3) = '165';

-- 4.c. Pattern B: kode berawalan '16' + digit ke-3 = '7' dan huruf ke-4 = '3'/'4'
--      16703, 16704 → PLANT II
UPDATE sc_mst.mlocation
SET idbranchjob = '01.02',
    nmbranchjob = 'PLANT II'
WHERE idlocation IN ('16703','16704');

-- 4.d. Pattern C: kode berawalan '19' + '002' → PLANT II
--      19002 → PLANT II
UPDATE sc_mst.mlocation
SET idbranchjob = '01.02',
    nmbranchjob = 'PLANT II'
WHERE idlocation = '19002';

-- 4.e. Sisanya (semua yang belum terisi) → PLANT I (default)
UPDATE sc_mst.mlocation
SET idbranchjob = '01.01',
    nmbranchjob = 'PLANT I'
WHERE idbranchjob IS NULL;

-- 4.f. Override: kalau Anda mau set header PT di baris tertentu, sesuaikan di sini.
--      Contoh (opsional): idbranchjob = '01' → PT JATIM TAMAN STEEL MFG
-- UPDATE sc_mst.mlocation
-- SET idbranchjob = '01',
--     nmbranchjob = 'PT JATIM TAMAN STEEL MFG'
-- WHERE idlocation IN ('16101','16102');

COMMIT;

-- ============================================================
-- VERIFIKASI
-- ============================================================
SELECT idlocation, nmlocation, idbranchjob, nmbranchjob
FROM sc_mst.mlocation
ORDER BY idlocation;

-- Hitung per branchjob
SELECT idbranchjob, nmbranchjob, COUNT(*) AS total
FROM sc_mst.mlocation
GROUP BY idbranchjob, nmbranchjob
ORDER BY idbranchjob;