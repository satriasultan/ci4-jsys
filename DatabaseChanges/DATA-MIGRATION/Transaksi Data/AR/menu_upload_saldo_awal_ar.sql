-- =============================================================================
-- MENU I.T.D.6 : UPLOAD SALDO AR (SALDO AWAL PIUTANG CUSTOMER)
-- Target DB  : DB_Jaccounting
-- Skema      : sc_mst
-- Sifat      : idempoten (boleh dijalankan berulang; hanya menyentuh baris
--              kodemenu = 'I.T.D.6')
--
-- Catatan:
--   * sc_mst.menuprg PRIMARY KEY (kodemenu) -> ON CONFLICT (kodemenu).
--   * Sidebar (App\Models\Web\M_Menuprg) hanya menampilkan menu yang punya
--     pasangan di sc_mst.role_permissions. Baris role_permissions di bagian
--     bawah menyalin hak akses dari menu I.T.D.5 (UPLOAD SALDO AP), sehingga
--     role yang sudah boleh membuka upload saldo AP otomatis boleh membuka AR.
--   * Controller: App\Controllers\Tools\SaldoAwalAR
--     Route     : tools/proses/uploadSaldoAwalAR
--   * Jalankan berurutan:
--       1) sql/opening_ar.sql
--       2) sql/opening_ar_accounting_fix.sql
--       3) sql/trx_hd_recalculate_perf.sql   (opsional, disarankan)
--       4) sql/menu_upload_saldo_awal_ar.sql (file ini)
-- =============================================================================

BEGIN;

INSERT INTO sc_mst.menuprg
    (branch, urut, kodemenu, namamenu, parentmenu, parentsub, child,
     holdmenu, iconmenu, linkmenu, menuposition, chold)
VALUES
    ('JTS', 6, 'I.T.D.6', 'UPLOAD SALDO AR', 'I.T', 'I.T.D', 'P',
     false, 'fa-arrow-circle-down', 'tools/proses/uploadSaldoAwalAR', 'LEFT', 'NO')
ON CONFLICT (kodemenu) DO UPDATE
   SET branch       = EXCLUDED.branch,
       urut         = EXCLUDED.urut,
       namamenu     = EXCLUDED.namamenu,
       parentmenu   = EXCLUDED.parentmenu,
       parentsub    = EXCLUDED.parentsub,
       child        = EXCLUDED.child,
       holdmenu     = EXCLUDED.holdmenu,
       iconmenu     = EXCLUDED.iconmenu,
       linkmenu     = EXCLUDED.linkmenu,
       menuposition = EXCLUDED.menuposition,
       chold        = EXCLUDED.chold
 WHERE sc_mst.menuprg.kodemenu = 'I.T.D.6';

-- -----------------------------------------------------------------------------
-- Hak akses: salin dari menu UPLOAD SALDO AP (I.T.D.5) bila belum ada.
-- -----------------------------------------------------------------------------
INSERT INTO sc_mst.role_permissions
    (roleid, kodemenu, chold, a_view, a_input, a_update, a_delete,
     a_approve1, a_approve2, a_approve3, a_filter, a_report)
SELECT DISTINCT
       r.roleid,
       'I.T.D.6',
       r.chold, r.a_view, r.a_input, r.a_update, r.a_delete,
       r.a_approve1, r.a_approve2, r.a_approve3, r.a_filter, r.a_report
FROM sc_mst.role_permissions r
WHERE btrim(r.kodemenu::text) = 'I.T.D.5'
  AND NOT EXISTS
      (SELECT 1
         FROM sc_mst.role_permissions x
        WHERE btrim(x.roleid::text) = btrim(r.roleid::text)
          AND btrim(x.kodemenu::text) = 'I.T.D.6');

-- Bila I.T.D.5 belum ada untuk role apa pun, setidaknya ADMTEK harus punya.
INSERT INTO sc_mst.role_permissions
    (roleid, kodemenu, chold, a_view, a_input, a_update, a_delete,
     a_approve1, a_approve2, a_approve3, a_filter, a_report)
SELECT 'ADMTEK', 'I.T.D.6', 'f', 't', 't', 't', 't', 't', 'f', 'f', 't', 't'
WHERE NOT EXISTS
      (SELECT 1
         FROM sc_mst.role_permissions x
        WHERE btrim(x.roleid::text) = 'ADMTEK'
          AND btrim(x.kodemenu::text) = 'I.T.D.6');

COMMIT;

-- -----------------------------------------------------------------------------
-- Verifikasi
-- -----------------------------------------------------------------------------
DO $$
DECLARE
    v_row RECORD;
BEGIN
    FOR v_row IN
        SELECT kodemenu, namamenu, parentmenu, parentsub, linkmenu
        FROM sc_mst.menuprg
        WHERE btrim(kodemenu::text) = 'I.T.D.6'
    LOOP
        RAISE NOTICE 'menu % | % | % / % | link=%',
            btrim(v_row.kodemenu::text), btrim(v_row.namamenu::text),
            btrim(v_row.parentmenu::text), btrim(v_row.parentsub::text),
            btrim(v_row.linkmenu::text);
    END LOOP;

    RAISE NOTICE 'role_permissions I.T.D.6 : % role',
        (SELECT COUNT(*) FROM sc_mst.role_permissions
          WHERE btrim(kodemenu::text) = 'I.T.D.6');
END;
$$;
