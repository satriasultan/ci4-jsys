-- ============================================================
-- MIGRASI ROLE, USER, ROLE_PERMISSIONS
-- Target: PostgreSQL
-- Password semua user: 123  (hash: md5(md5(md5('123'))))
-- Idempotent: aman dijalankan ulang. Blok 0 (CLEANUP) menghapus data
-- migrasi lama terlebih dahulu, lalu di-insert ulang, sehingga hasilnya
-- selalu sama. ON CONFLICT DO NOTHING tetap ada sebagai pengaman.
-- ============================================================



BEGIN;

ALTER TABLE sc_mst.role
ALTER COLUMN rolename TYPE CHARACTER(50);

-- ------------------------------------------------------------
-- 0. CLEANUP DATA MIGRASI SEBELUMNYA
--    Supaya file ini bisa dijalankan ulang dari kondisi bersih
--    (hapus dulu -> insert ulang), sehingga hasilnya selalu sama.
--    URUTAN WAJIB: role_permissions -> user -> role
--    Cakupan aman (data lama di luar migrasi TIDAK tersentuh):
--      * role_permissions : hanya roleid daftar migrasi
--                           (role IT/AD/MG/ACC/PP dibiarkan)
--      * user             : hanya yang inputby = 'MIGRATION'
--      * role             : hanya yang inputby = 'MIGRATION'
--                           (PCH sudah ada sebelum migrasi, tidak dihapus)
-- ------------------------------------------------------------
DELETE FROM sc_mst.role_permissions
WHERE roleid IN ('ADMNEW','ADMTEK','ADM2','ADMPRD','ADMLTD','GDG','RO','INPD','PJL','KSR','MKT','MNTPRJ','PRD','SPVPRD','VPRO','SUBIT','PCHUM','PCH');

DELETE FROM sc_mst.user WHERE inputby = 'MIGRATION';

DELETE FROM sc_mst.role WHERE inputby = 'MIGRATION';

-- ------------------------------------------------------------
-- 1. ROLE
-- ------------------------------------------------------------
INSERT INTO sc_mst.role (roleid, rolename, chold, inputby, inputdate)
SELECT v.roleid, v.rolename, v.chold, v.inputby, v.inputdate
FROM (VALUES
  ('ADMNEW','ADMINISTRATOR_NEW','NO','MIGRATION',NOW()),
  ('ADMTEK','ADMINISTRATOR_TEKNISI','NO','MIGRATION',NOW()),
  ('ADM2','ADMINISTRATOR2','NO','MIGRATION',NOW()),
  ('ADMPRD','ADMINISTRATOR PRODUKSI','NO','MIGRATION',NOW()),
  ('ADMLTD','ADMINISTRATOR_LIMITED','NO','MIGRATION',NOW()),
  ('GDG','GUDANG','NO','MIGRATION',NOW()),
  ('RO','READ ONLY','NO','MIGRATION',NOW()),
  ('INPD','INPUT DATA','NO','MIGRATION',NOW()),
  ('PJL','PENJUALAN','NO','MIGRATION',NOW()),
  ('KSR','KASIR','NO','MIGRATION',NOW()),
  ('MKT','MARKETING MANAGER','NO','MIGRATION',NOW()),
  ('MNTPRJ','MONITORING PROJECT','NO','MIGRATION',NOW()),
  ('PRD','PRODUKSI','NO','MIGRATION',NOW()),
  ('SPVPRD','SUPERVISOR PRODUKSI','NO','MIGRATION',NOW()),
  ('VPRO','VISUAL PRO','NO','MIGRATION',NOW()),
  ('SUBIT','SUB ADMIN IT','NO','MIGRATION',NOW()),
  ('PCH','PEMBELIAN','NO','MIGRATION',NOW()),
  ('PCHUM','PEMBELIAN UMUM','NO','MIGRATION',NOW())
) AS v(roleid, rolename, chold, inputby, inputdate)
WHERE NOT EXISTS (
  SELECT 1 FROM sc_mst.role r WHERE r.roleid = v.roleid
);

-- PCH (PEMBELIAN) kini ikut di-insert di atas (hanya bila roleid belum ada).
-- Bila role sudah ada sebelumnya (mis. rolename 'PURCHASING'), rolename existing
-- TIDAK diubah oleh blok ini.

-- ------------------------------------------------------------
-- 2. USER
-- passwordweb = md5(md5(md5('123')))
-- branch: 01 = PT JTS, 01.01 = PLANT I, 01.02 = PLANT II
-- iduser TIDAK di-insert -> diisi otomatis dari sequence
--   sc_mst.user_iduser_seq (NOT NULL, sama seperti input dari aplikasi).
-- CATATAN DATA (username = nik, kunci unik, ON CONFLICT DO NOTHING):
--   * username diisi sama dengan kolom nik (login memakai NIK).
--   * PERHATIAN — ada NIK GANDA yang akan ter-skip (baris ke-2):
--       05480  : ADMIN_IT (ADMNEW) vs WAWAN (ADMTEK)  -> ADMIN_IT masuk
--       100285 : ADM_IMPORT (PCH) vs ADM_PURC (PCH)   -> ADM_IMPORT masuk
--       05025  : DHEWANTA (branch 01) vs ADMIN_PPIC (branch 01.01) -> DHEWANTA masuk
--     Bila ke-3 akun tersebut tetap harus ada, beri NIK/usernya nilai yang berbeda.
--   * 'WAWAN' sudah ada di DB dengan inputby='FIKY' (bukan hasil migrasi).
-- ------------------------------------------------------------
INSERT INTO sc_mst.user
(branch, nik, username, passwordweb, level_id, level_akses, expdate, hold_id,
 inputdate, inputby, editdate, editby, lastlogin, image, loccode, roleid, lang, kddept)
VALUES
-- branch 01 (PT JATIM TAMAN STEEL MFG)
('01','05616','05616',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','05361','05361',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05690','05690',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05480','05480',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMNEW','english',NULL),
('01','05568','05568', md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','300325','300325', md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','100285','100285',md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','100313','100313',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','100285','100285',  md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05628','05628',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05656','05656',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05516','05516',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADM2',  'english',NULL),
('01','05657','05657',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','05699','05699',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','06666','06666',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','04883','04883',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05517','05517',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','05370','05370',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','04903','04903',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMTEK','english',NULL),
('01','05481','05481',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','05607','05607',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADM2',  'english',NULL),
('01','04905','04905',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05498','05498',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05177','05177',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05621','05621',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','100284','100284',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05025','05025',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05582','05582',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
('01','04551','04551',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05076','05076',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05476','05476',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','04787','04787',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PRD',   'english',NULL),
('01','05495','05495',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMTEK','english',NULL),
('01','04938','04938',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05024','05024',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05240','05240',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADM2',  'english',NULL),
('01','05515','05515',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05611','05611',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','04761','04761',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05672','05672', md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05170','05170',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05023','05023',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05724','05724',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','100335','100335',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05172','05172',  md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','04564','04564',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05661','05661',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADM2',  'english',NULL),
('01','05122','05122',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','04881','04881',  md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','200108','200108',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05685','05685',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05655','05655',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05098','05098',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','05584','05584',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','100118','100118',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05218','05218',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','04817','04817',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
('01','05623','05623',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
('01','05526','05526',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','200362','200362',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','04836','04836',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','04698','04698',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05057','05057',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PJL',   'english',NULL),
('01','04911','04911',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05193','05193',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
('01','05452','05452',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','04764','04764',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','200169','200169',    md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','04778','04778',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05114','05114',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05121','05121',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PJL',   'english',NULL),
('01','05068','05068',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
('01','05456','05456',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05491','05491',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05489','05489',  md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','100322','100322',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCH',   'english',NULL),
('01','05619','05619',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','04769','04769',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05490','05490',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','04064','04064',  md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05362','05362',   md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05359','05359',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05101','05101',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','04157','04157',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','05084','05084',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
('01','00000','00000',         md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'VPRO',  'english',NULL),
('01','04145','04145',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'PCHUM', 'english',NULL),
('01','05480','05480',      md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMTEK','english',NULL),
('01','05405','05405',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMLTD','english',NULL),
('01','100134','100134', md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'GDG',   'english',NULL),
('01','05406','05406',     md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL),
('01','05698','05698',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMTEK','english',NULL),
('01','100347','100347',       md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'ADMPRD','english',NULL),
-- branch 01.01 (PLANT I)
('01.01','05025','05025', md5(md5(md5('123'))),NULL,NULL,'2055-10-22 00:00:00','NO',NOW(),'MIGRATION',NULL,NULL,NULL,NULL,NULL,'SPVPRD','english',NULL)
ON CONFLICT (username) DO NOTHING;

-- ------------------------------------------------------------
-- 3. ROLE_PERMISSIONS
-- Hanya pasangan role x kodemenu yang punya akses (a_view='t') yang di-insert.
-- Nilai diambil dari matrix; kode menu tanpa padanan => tidak di-insert.
-- Mapping matrix:
--   Hak Pakai -> a_view, Tambah -> a_input, Koreksi -> a_update,
--   Cetak -> a_report, Hapus -> a_delete, Approval -> a_approve1
--   a_approve2 = a_approve3 = 'f'; a_filter mengikuti a_view
-- Hanya baris dengan a_view = 't' (Hak Pakai dicentang) yang di-insert.
-- Menu dengan a_view = 'f' TIDAK dibuatkan baris (dianggap tanpa akses).
-- ------------------------------------------------------------
INSERT INTO sc_mst.role_permissions
(roleid, kodemenu, chold, a_view, a_input, a_update, a_delete,
 a_approve1, a_approve2, a_approve3, a_filter, a_report)
VALUES
-- ===== ADMINISTRATOR_NEW (ADMNEW) =====
('ADMNEW','I.M.A.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.A.13','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.6','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.7','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.8','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.9','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.10','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.11','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.12','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.13','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.M.B.14','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.6','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.P.A.7','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.Q.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.Q.A.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.Q.A.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.Q.A.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.Q.A.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.Q.A.6','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.8','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.9','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.R.A.10','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.L.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.L.B.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.A.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.A.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.B.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.B.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.B.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.T.C.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.K.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.K.B.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.K.B.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.K.B.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.S.B.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.S.B.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.S.B.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.S.B.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.A.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.A.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.A.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.6','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.B.7','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.C.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.C.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.D.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.D.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.D.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.E.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.E.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.E.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.E.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.E.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.F.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.F.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.F.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.F.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.F.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.F.6','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.G.1','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.G.2','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.G.3','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.G.4','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.G.5','f','t','t','t','t','t','f','f','f','t'),
('ADMNEW','I.H.H.1','f','t','t','t','t','t','f','f','f','t'),

-- ===== ADMINISTRATOR_TEKNISI (ADMTEK) =====
-- Sumber: Matrix Winaccounting(perubahan).csv, kolom ke-2 "ADMINISTRATOR_TEKNISI".
-- Hanya menu yang ADA di sc_mst.menuprg (child='P') yang di-insert -> 48 menu.
-- Pola flag: Hak Pakai->a_view, Tambah->a_input, Koreksi->a_update,
--   Hapus->a_delete, Approval->a_approve1, Cetak->a_report, a_filter=a_view.
('ADMTEK','I.M.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.M.A.13','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.M.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.M.B.9','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.P.A.7','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.S.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.S.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.A.1','f','t','f','f','f','f','f','f','t','f'),
('ADMTEK','I.H.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.B.5','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.B.7','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('ADMTEK','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),
('ADMTEK','I.H.F.1','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.F.2','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.F.3','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.F.4','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.F.5','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.F.6','f','t','t','t','t','t','f','f','t','t'),
('ADMTEK','I.H.G.3','f','t','t','t','t','t','f','f','t','t'),

-- ===== ADMINISTRATOR2 (ADM2) =====
-- Sumber: Matrix Winaccounting(perubahan).csv, kolom "ADMINISTRATOR2".
-- Hanya menu yang ADA di sc_mst.menuprg (child='P') yang di-insert -> 84 menu.
-- Pola flag: Hak Pakai->a_view, Tambah->a_input, Koreksi->a_update,
--   Hapus->a_delete, Approval->a_approve1, Cetak->a_report, a_filter=a_view.
('ADM2','I.M.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.7','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.9','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.11','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.12','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.13','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.14','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.P.A.7','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.L.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.L.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.T.C.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.K.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.K.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.K.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.K.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.S.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.S.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.B.7','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.C.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.C.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.D.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.D.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.D.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.E.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.E.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.E.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.E.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.E.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.F.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.F.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.F.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.F.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.F.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.F.6','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.G.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.G.2','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.G.3','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.G.4','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.G.5','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.H.H.1','f','t','t','t','t','t','f','f','t','t'),
('ADM2','I.M.B.8','f','t','t','t','t','t','f','f','t','t'),

-- ===== ADMINISTRATOR PRODUKSI (ADMPRD) =====
-- Sumber: Matrix Winaccounting(perubahan).csv, kolom ke-4 "ADMINISTRATOR PRODUKSI".
-- Hanya menu yang ADA di sc_mst.menuprg (child='P') yang di-insert -> 45 menu.
-- Pola flag: Hak Pakai->a_view, Tambah->a_input, Koreksi->a_update,
--   Hapus->a_delete, Approval->a_approve1, Cetak->a_report, a_filter=a_view.
('ADMPRD','I.M.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.7','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.9','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.11','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.12','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.13','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.M.B.8','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.P.A.4','f','t','f','f','f','f','f','f','t','f'),
('ADMPRD','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.T.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.T.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.T.C.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.C.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.D.1','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.D.2','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.D.3','f','t','t','t','t','t','f','f','t','t'),
('ADMPRD','I.H.H.1','f','t','t','t','t','t','f','f','t','t'),

-- ===== ADMINISTRATOR_LIMITED (ADMLTD) =====
-- Sumber: Matrix Winaccounting(perubahan).csv, kolom "ADMINISTRATOR_LIMITED".
-- Hanya menu yang ADA di sc_mst.menuprg (child='P') yang di-insert -> 83 menu.
-- Pola flag: Hak Pakai->a_view, Tambah->a_input, Koreksi->a_update,
--   Hapus->a_delete, Approval->a_approve1, Cetak->a_report, a_filter=a_view.
('ADMLTD','I.M.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.7','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.9','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.11','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.12','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.13','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.14','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.P.A.7','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.L.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.L.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.T.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.T.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.T.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.T.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.T.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.T.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.K.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.K.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.K.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.K.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.S.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.S.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.A.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.A.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.A.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.B.7','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.C.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.C.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.D.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.D.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.D.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.E.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.E.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.E.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.E.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.E.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.F.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.F.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.F.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.F.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.F.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.F.6','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.G.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.G.2','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.G.3','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.G.4','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.G.5','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.H.H.1','f','t','t','t','t','t','f','f','t','t'),
('ADMLTD','I.M.B.8','f','t','t','t','t','t','f','f','t','t'),

-- ===== PEMBELIAN (PCH) =====
-- Sumber: Matrix Winaccounting(perubahan).csv, kolom ke-6 "PEMBELIAN".
-- Hanya menu yang ADA di sc_mst.menuprg (child='P') yang di-insert -> 21 menu.
-- Baris CSV yang tidak punya padanan menu di menuprg TIDAK di-insert, mis.:
--   Full Search Authorization, Calculator, Password *, Import *,
--   Lap. Retur Jual, Lap. Penerimaan Barang, Daftar PO Belum di Approved,
--   serta varian (XLS)/(Rekap)/(dg Nilai) dari menu yang sudah terwakili.
-- Pola flag (bit): Hak Pakai->a_view, Tambah->a_input, Koreksi->a_update,
--   Hapus->a_delete, Approval->a_approve1, Cetak->a_report, a_filter=a_view.
('PCH','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.M.B.10','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.P.A.4','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.P.A.5','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.P.A.7','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.H.A.1','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.A.3','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.B.1','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.B.2','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.H.B.3','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.B.5','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('PCH','I.H.B.7','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.E.4','f','t','f','f','f','f','f','f','t','f'),
('PCH','I.H.E.5','f','t','f','f','f','f','f','f','t','f'),

-- ===== GUDANG (GDG) =====
-- Sumber: Matrix Winaccounting(perubahan).csv, kolom ke-7 "GUDANG".
-- Hanya menu yang ADA di sc_mst.menuprg (child='P') yang di-insert -> 18 menu.
-- Baris CSV lain tanpa padanan menu di menuprg (fitur/password/import,
-- varian (XLS)/(Rekap), dan laporan yang belum ada menunya) tidak di-insert.
-- Pola flag (bit): Hak Pakai->a_view, Tambah->a_input, Koreksi->a_update,
--   Hapus->a_delete, Approval->a_approve1, Cetak->a_report, a_filter=a_view.
('GDG','I.M.B.10','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.P.A.3','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.S.B.4','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.H.B.1','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.H.B.2','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.H.B.3','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.H.B.5','f','t','t','t','t','t','f','f','t','t'),
('GDG','I.H.C.2','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('GDG','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),

-- ------------------------------------------------------------
-- 3a-2. ROLE_PERMISSIONS role lanjutan
-- Sumber: Matrix Winaccounting(perubahan).csv
-- Pemetaan kolom matrix -> kolom tabel:
--   Hak Pakai -> a_view    Tambah -> a_input   Koreksi -> a_update
--   Cetak     -> a_report  Hapus  -> a_delete  Approval -> a_approve1
-- a_approve2 = a_approve3 = 'f' ; a_filter = a_view
-- Hanya baris a_view='t' yang di-insert.
-- Kode menu baru yang tidak punya padanan di matrix => tidak di-insert
-- (I.M.A.4, I.M.C.1, I.J.A.1, I.S.A.2, I.S.A.3, I.S.B.5, I.S.B.6).
-- ------------------------------------------------------------
-- ===== READ ONLY (RO) =====
('RO','I.P.A.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.P.A.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.P.A.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.P.A.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.P.A.5','f','t','f','f','f','f','f','f','t','t'),
('RO','I.P.A.6','f','t','f','f','f','f','f','f','t','t'),
('RO','I.P.A.7','f','t','f','f','f','f','f','f','t','t'),
('RO','I.Q.A.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.Q.A.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.Q.A.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.Q.A.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.Q.A.5','f','t','f','f','f','f','f','f','t','t'),
('RO','I.Q.A.6','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.5','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.8','f','t','f','f','f','f','f','f','t','t'),
('RO','I.R.A.9','f','t','f','f','f','f','f','f','t','f'),
('RO','I.R.A.10','f','t','f','f','f','f','f','f','t','t'),
('RO','I.L.A.1','f','t','f','f','f','f','f','f','t','f'),
('RO','I.K.A.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.K.B.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.K.B.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.K.B.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.S.B.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.S.B.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.S.B.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.S.B.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.B.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.B.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.B.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.B.6','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.C.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.C.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.D.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.D.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.D.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.E.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.E.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.E.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.E.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.E.5','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.F.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.F.2','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.F.3','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.F.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.F.5','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.F.6','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.G.1','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.G.4','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.G.5','f','t','f','f','f','f','f','f','t','t'),
('RO','I.H.H.1','f','t','f','f','f','f','f','f','t','t'),

-- ===== INPUT DATA (INPD) =====
('INPD','I.M.B.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.4','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.6','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.7','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.8','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.9','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.11','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.12','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.13','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.M.B.14','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.4','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.5','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.P.A.7','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.L.B.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.T.A.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.T.A.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.T.A.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.S.B.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.S.B.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.A.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.A.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.A.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.B.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.B.4','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.C.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.C.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.D.1','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.D.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.D.3','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.G.2','f','t','t','t','t','t','f','f','t','t'),
('INPD','I.H.H.1','f','t','t','t','t','t','f','f','t','t'),

-- ===== PENJUALAN (PJL) =====
('PJL','I.M.B.9','f','t','f','f','f','f','f','f','t','f'),
('PJL','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('PJL','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('PJL','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('PJL','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('PJL','I.S.B.3','f','t','t','t','t','t','f','f','t','t'),
('PJL','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('PJL','I.H.B.1','f','t','f','f','f','f','f','f','t','f'),
('PJL','I.H.C.2','f','t','f','f','f','f','f','f','t','f'),
('PJL','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('PJL','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),

-- ===== KASIR (KSR) =====
('KSR','I.L.A.1','f','t','t','t','t','t','f','f','t','t'),
('KSR','I.T.C.1','f','t','f','f','f','f','f','f','t','f'),
('KSR','I.K.B.1','f','t','t','t','t','t','f','f','t','t'),
('KSR','I.K.B.2','f','t','t','t','t','t','f','f','t','t'),
('KSR','I.K.B.3','f','t','t','t','t','t','f','f','t','t'),
('KSR','I.H.B.3','f','t','f','f','f','f','f','f','t','f'),
('KSR','I.H.B.6','f','t','f','f','f','f','f','f','t','f'),
('KSR','I.H.E.1','f','t','f','f','f','f','f','f','t','t'),
('KSR','I.H.E.2','f','t','f','f','f','f','f','f','t','t'),
('KSR','I.H.E.3','f','t','f','f','f','f','f','f','t','t'),
('KSR','I.H.E.4','f','t','f','f','f','f','f','f','t','t'),
('KSR','I.H.E.5','f','t','f','f','f','f','f','f','t','t'),
('KSR','I.H.G.1','f','t','t','t','f','t','f','f','t','t'),

-- ===== MARKETING MANAGER (MKT) =====
('MKT','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('MKT','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('MKT','I.P.A.4','f','t','t','t','f','f','f','f','t','t'),
('MKT','I.P.A.6','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('MKT','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('MKT','I.H.A.1','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.A.2','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.C.2','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.F.1','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.F.2','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.F.3','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.F.4','f','t','f','f','f','f','f','f','t','f'),
('MKT','I.H.F.6','f','t','f','f','f','f','f','f','t','f'),

-- ===== MONITORING PROJECT (MNTPRJ) =====
('MNTPRJ','I.H.B.3','f','t','f','f','f','f','f','f','t','f'),
('MNTPRJ','I.H.B.4','f','t','f','f','f','f','f','f','t','f'),
('MNTPRJ','I.H.B.7','f','t','f','f','f','f','f','f','t','f'),

-- ===== PRODUKSI (PRD) =====
('PRD','I.M.B.10','f','t','f','f','f','f','f','f','t','f'),
('PRD','I.R.A.4','f','t','t','t','t','f','f','f','t','t'),
('PRD','I.R.A.5','f','t','t','t','t','f','f','f','t','t'),
('PRD','I.R.A.8','f','t','t','t','t','f','f','f','t','t'),
('PRD','I.R.A.9','f','t','t','t','t','f','f','f','t','t'),
('PRD','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('PRD','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),

-- ===== SUPERVISOR PRODUKSI (SPVPRD) =====
('SPVPRD','I.M.B.2','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.M.B.10','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.M.B.14','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.P.A.4','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.H.A.1','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.H.B.1','f','t','t','t','t','f','f','f','t','t'),
('SPVPRD','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('SPVPRD','I.H.D.1','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),
('SPVPRD','I.H.H.1','f','t','f','f','f','f','f','f','t','f'),

-- ===== VISUAL PRO (VPRO) =====
('VPRO','I.M.A.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.A.13','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.6','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.7','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.8','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.9','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.10','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.11','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.12','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.13','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.M.B.14','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.6','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.P.A.7','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.Q.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.Q.A.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.Q.A.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.Q.A.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.Q.A.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.Q.A.6','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.8','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.9','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.R.A.10','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.L.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.L.B.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.A.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.A.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.B.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.B.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.B.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.T.C.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.K.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.K.B.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.K.B.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.K.B.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.S.B.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.S.B.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.S.B.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.S.B.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.A.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.A.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.A.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.B.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.B.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.B.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.B.6','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.B.7','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.C.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.C.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.D.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.D.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.D.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.E.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.E.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.E.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.E.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.E.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.F.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.F.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.F.3','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.F.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.F.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.F.6','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.G.1','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.G.2','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.G.4','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.G.5','f','t','t','t','t','t','f','f','t','t'),
('VPRO','I.H.H.1','f','t','t','t','t','t','f','f','t','t'),

-- ===== SUB ADMIN IT (SUBIT) =====
('SUBIT','I.P.A.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.P.A.2','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.P.A.3','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.P.A.4','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.P.A.5','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.P.A.6','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.P.A.7','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.Q.A.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.Q.A.2','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.Q.A.3','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.Q.A.4','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.Q.A.5','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.Q.A.6','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.T.B.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.T.B.2','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.T.B.3','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.T.C.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.B.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.B.3','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.B.4','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.B.6','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.B.7','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.C.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.C.2','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.D.1','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.D.2','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.D.3','f','t','f','f','f','f','f','f','t','f'),
('SUBIT','I.H.H.1','f','t','f','f','f','f','f','f','t','f'),

-- ===== PEMBELIAN UMUM (PCHUM) =====
('PCHUM','I.M.B.10','f','t','f','f','f','f','f','f','t','f'),
('PCHUM','I.P.A.1','f','t','t','t','t','t','f','f','t','t'),
('PCHUM','I.P.A.2','f','t','t','t','t','t','f','f','t','t'),
('PCHUM','I.H.A.1','f','t','f','f','f','f','f','f','t','f'),
('PCHUM','I.H.B.1','f','t','f','f','f','f','f','f','t','f'),
('PCHUM','I.H.B.2','f','t','t','t','t','t','f','f','t','t'),
('PCHUM','I.H.B.3','f','t','f','f','f','f','f','f','t','f'),
('PCHUM','I.H.B.5','f','t','t','t','t','t','f','f','t','t'),
('PCHUM','I.H.D.3','f','t','f','f','f','f','f','f','t','f')

ON CONFLICT (roleid, kodemenu) DO NOTHING;

-- ------------------------------------------------------------
-- 3b. ROLE_PERMISSIONS default 'f' untuk semua role x kodemenu
--     *** DINONAKTIFKAN ***
--     Aturan baru: baris dengan a_view='f' tidak di-insert, sehingga
--     blok ini justru akan membuat kembali semua baris 'f' tersebut.
-- ------------------------------------------------------------
-- INSERT INTO sc_mst.role_permissions
-- (roleid, kodemenu, chold, a_view, a_input, a_update, a_delete,
--  a_approve1, a_approve2, a_approve3, a_filter, a_report)
-- SELECT r.roleid, m.kodemenu, 'f', 'f', 'f', 'f', 'f', 'f', 'f', 'f', 'f', 'f'
-- FROM sc_mst.role r
-- CROSS JOIN (SELECT DISTINCT kodemenu FROM sc_mst.menuprg
--             WHERE kodemenu ~ '^I\.[A-Z]\.[A-Z]\.[0-9]+') m
-- WHERE r.roleid IN ('ADMNEW','ADMTEK','ADM2','ADMPRD','ADMLTD','GDG','RO','INPD','PJL','KSR','MKT','MNTPRJ','PRD','SPVPRD','VPRO','SUBIT','PCHUM','PCH')
-- ON CONFLICT (roleid, kodemenu) DO NOTHING;

-- (Pembersihan baris a_view <> 't' dari versi lama sudah ditangani oleh
--  blok 0 CLEANUP di awal file, sehingga tidak perlu blok terpisah.)

-- ------------------------------------------------------------
-- 4. VERIFIKASI
-- ------------------------------------------------------------
DO $$
DECLARE
  v_role INT;
  v_user INT;
  v_perm INT;
  v_hash TEXT;
BEGIN
  SELECT COUNT(*) INTO v_role FROM sc_mst.role WHERE inputby = 'MIGRATION';
  SELECT COUNT(*) INTO v_user FROM sc_mst.user WHERE inputby = 'MIGRATION';
  SELECT COUNT(*) INTO v_perm FROM sc_mst.role_permissions
    WHERE roleid IN ('ADMNEW','ADMTEK','ADM2','ADMPRD','ADMLTD','GDG','RO','INPD','PJL','KSR','MKT','MNTPRJ','PRD','SPVPRD','VPRO','SUBIT','PCHUM','PCH');
  SELECT md5(md5(md5('123'))) INTO v_hash;

  RAISE NOTICE '==============================================';
  RAISE NOTICE 'MIGRASI BERHASIL';
  RAISE NOTICE 'Role baru        : %', v_role;
  RAISE NOTICE 'User baru        : %', v_user;
  RAISE NOTICE 'Role permission  : %', v_perm;
  RAISE NOTICE 'Hash password 123: %', v_hash;
  RAISE NOTICE '==============================================';
END $$;

COMMIT;

-- Verifikasi manual (opsional, jalankan setelah commit):
-- SELECT username, nik, branch, roleid FROM sc_mst.user WHERE inputby='MIGRATION' ORDER BY username;
-- SELECT md5(md5(md5('123'))) AS hash_123;