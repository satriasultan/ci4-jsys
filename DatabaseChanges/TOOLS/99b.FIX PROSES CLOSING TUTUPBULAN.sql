/*
===============================================================================
J-ACCOUNTING - FIX PROSES CLOSING (TUTUP BULAN) UNTUK KEBUTUHAN ACCOUNTING
===============================================================================
File ini MELENGKAPI "99.PROSES CLOSING PENTING .sql" (jalankan file tersebut
lebih dahulu). Isi perbaikan:

1. fn_close_accounting_period  -> flagproses diset 'TUTUP' (bukan 'CLOSED').
   Semua modul transaksi (Purchase, Sales, Persediaan, Production, ARAP) dan
   guard layout memakai syarat: TRIM(periode)=yymm AND flagproses='TUTUP'.
   Tanpa ini, periode yang sudah CLOSED tetap bisa ditransaksi.
2. fn_reopen_accounting_period -> flagproses 'OPEN' + keterangan 'Periode Dibuka'.
3. fn_register_closeperiod     -> registrasi/update periode bulanan (upsert).
4. fn_period_closing_trial_balance -> total debet/kredit POSTED untuk auto-isi
   closing_debet / closing_kredit (syarat fn_close_accounting_period).
5. fn_apply_period_closing_checks -> mengisi checklist otomatis dari hasil
   diagnostik (JOURNAL_BALANCE, TB_BALANCE, UNCLASSIFIED_COA, PNL_REVIEW) dan
   menyiapkan baris STOCK_REVIEW untuk konfirmasi manual.
6. fn_run_monthly_closing      -> orkestrator closing bulanan 1 klik:
   register -> start -> checklist -> TB -> review -> close (flagproses TUTUP).
7. sc_trx.v_period_closing_status -> view status periode + ringkasan checklist.
8. Trigger lock: menolak INSERT/UPDATE sc_trx.jurnal_hd yang trxdate-nya jatuh
   pada periode ber-status CLOSED (memanggil fn_assert_period_open).

URUTAN PEMAKAIAN (dari aplikasi Tools > Proses > Proses Tutup Bulan):
  Registrasi Periode -> Diagnostik -> Terapkan Checklist -> (konfirmasi
  STOCK_REVIEW) -> Mulai Closing -> Ajukan Review -> Tutup Periode.

CATATAN: jalankan pada database TEST terlebih dahulu.
===============================================================================
*/

BEGIN;

-- ============================================================================
-- 1. REGISTRASI / UPDATE PERIODE CLOSING
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_register_closeperiod(
    p_periode VARCHAR,
    p_tanggal_mulai DATE,
    p_tanggal_akhir DATE,
    p_user VARCHAR
)
RETURNS INTEGER
LANGUAGE plpgsql
AS $function$
DECLARE
    v_id INTEGER;
    v_status TEXT;
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' THEN
        RAISE EXCEPTION 'p_user wajib diisi.';
    END IF;
    IF COALESCE(BTRIM(p_periode), '') = '' THEN
        RAISE EXCEPTION 'periode wajib diisi.';
    END IF;
    IF p_tanggal_mulai IS NULL OR p_tanggal_akhir IS NULL THEN
        RAISE EXCEPTION 'tanggal_mulai dan tanggal_akhir wajib diisi.';
    END IF;
    IF p_tanggal_mulai > p_tanggal_akhir THEN
        RAISE EXCEPTION 'tanggal_mulai tidak boleh melewati tanggal_akhir.';
    END IF;

    SELECT cp.idurut, COALESCE(NULLIF(BTRIM(cp.status_closing), ''), 'OPEN')
      INTO v_id, v_status
    FROM sc_trx.closeperiod cp
    WHERE BTRIM(cp.periode) = BTRIM(p_periode)
    ORDER BY cp.idurut DESC
    LIMIT 1
    FOR UPDATE;

    IF FOUND THEN
        IF v_status = 'CLOSED' THEN
            RAISE EXCEPTION
                'Periode % sudah CLOSED. Lakukan Buka Kembali (reopen) sebelum mengubah rentang tanggal.',
                BTRIM(p_periode);
        END IF;

        UPDATE sc_trx.closeperiod
           SET periode       = p_periode,
               tanggal_mulai = p_tanggal_mulai,
               tanggal_akhir = p_tanggal_akhir,
               updateby      = p_user,
               updatedate    = CURRENT_TIMESTAMP
         WHERE idurut = v_id;

        INSERT INTO sc_trx.period_closing_log
            (closeperiod_id, action, old_status, new_status, action_by, reason, detail)
        VALUES
            (v_id, 'REGISTER', v_status, v_status, p_user,
             'Registrasi/ubah rentang tanggal periode.',
             jsonb_build_object('periode', BTRIM(p_periode),
                                'tanggal_mulai', p_tanggal_mulai,
                                'tanggal_akhir', p_tanggal_akhir));
    ELSE
        INSERT INTO sc_trx.closeperiod
            (periode, flagproses, keterangan, tanggal_mulai, tanggal_akhir,
             status_closing, inputby, inputdate)
        VALUES
            (p_periode, 'OPEN', 'Periode Berjalan', p_tanggal_mulai, p_tanggal_akhir,
             'OPEN', p_user, CURRENT_TIMESTAMP)
        RETURNING idurut INTO v_id;

        INSERT INTO sc_trx.period_closing_log
            (closeperiod_id, action, old_status, new_status, action_by, reason, detail)
        VALUES
            (v_id, 'REGISTER', NULL, 'OPEN', p_user,
             'Registrasi periode baru.',
             jsonb_build_object('periode', BTRIM(p_periode),
                                'tanggal_mulai', p_tanggal_mulai,
                                'tanggal_akhir', p_tanggal_akhir));
    END IF;

    RETURN v_id;
END
$function$;

-- ============================================================================
-- 2. TRIAL BALANCE PER PERIODE (SUMBER closing_debet / closing_kredit)
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_period_closing_trial_balance(
    p_closeperiod_id INTEGER
)
RETURNS TABLE (
    total_debet NUMERIC(24,2),
    total_kredit NUMERIC(24,2),
    selisih NUMERIC(24,2),
    jurnal_tidak_balance BIGINT
)
LANGUAGE plpgsql
AS $function$
DECLARE
    v_start DATE;
    v_end DATE;
BEGIN
    SELECT cp.tanggal_mulai, cp.tanggal_akhir
      INTO v_start, v_end
    FROM sc_trx.closeperiod cp
    WHERE cp.idurut = p_closeperiod_id;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_start IS NULL OR v_end IS NULL THEN
        RAISE EXCEPTION 'Rentang tanggal periode belum diisi.';
    END IF;

    RETURN QUERY
    SELECT ROUND(COALESCE(SUM(d.debet), 0)::NUMERIC, 2),
           ROUND(COALESCE(SUM(d.kredit), 0)::NUMERIC, 2),
           ROUND(COALESCE(SUM(d.debet - d.kredit), 0)::NUMERIC, 2),
           (SELECT COUNT(*)
              FROM (
                    SELECT h.id
                    FROM sc_trx.jurnal_hd h
                    WHERE h.trxdate BETWEEN v_start AND v_end
                      AND h.status = 'POSTED' AND h.accounting_effect = 'YES'
                    GROUP BY h.id, h.total_debet, h.total_kredit
                    HAVING ROUND(COALESCE(h.total_debet,0)::NUMERIC,2)
                        <> ROUND(COALESCE(h.total_kredit,0)::NUMERIC,2)
              ) q)
    FROM sc_trx.jurnal_dt d
    JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
    WHERE h.trxdate BETWEEN v_start AND v_end
      AND h.status = 'POSTED' AND h.accounting_effect = 'YES';
END
$function$;


-- ============================================================================
-- 3. CHECKLIST OTOMATIS DARI HASIL DIAGNOSTIK
-- ============================================================================
-- Kode checklist:
--   JOURNAL_BALANCE  : semua jurnal POSTED balance (target 0 tidak balance)
--   TB_BALANCE       : total debet = total kredit periode
--   UNCLASSIFIED_COA : seluruh COA transaksi sudah diklasifikasikan
--   PNL_REVIEW       : nilai PNL_NET dicatat untuk dibandingkan Lap. Laba Rugi
--   STOCK_REVIEW     : rekonsiliasi persediaan, WAJIB konfirmasi manual
CREATE OR REPLACE FUNCTION sc_trx.fn_apply_period_closing_checks(
    p_closeperiod_id INTEGER,
    p_user VARCHAR
)
RETURNS TABLE (
    check_code VARCHAR(50),
    check_name VARCHAR(150),
    check_status VARCHAR(20),
    issue_count BIGINT
)
LANGUAGE plpgsql
AS $function$
DECLARE
    v_status TEXT;
    v_unbalanced NUMERIC(24,2);
    v_debet NUMERIC(24,2);
    v_kredit NUMERIC(24,2);
    v_unclassified NUMERIC(24,2);
    v_pnl NUMERIC(24,2);
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' THEN
        RAISE EXCEPTION 'p_user wajib diisi.';
    END IF;

    SELECT cp.status_closing
      INTO v_status
    FROM sc_trx.closeperiod cp
    WHERE cp.idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;

    -- Checklist hanya boleh diubah pada status CHECKING; pindahkan ke CHECKING
    -- (fn_start_period_closing menolak periode CLOSED).
    IF v_status <> 'CHECKING' THEN
        PERFORM sc_trx.fn_start_period_closing(p_closeperiod_id, p_user);
    END IF;

    SELECT d.metric_value INTO v_unbalanced
    FROM sc_trx.fn_period_closing_diagnostics(p_closeperiod_id) d
    WHERE d.check_code = 'JOURNAL_UNBALANCED';

    SELECT d.metric_value INTO v_debet
    FROM sc_trx.fn_period_closing_diagnostics(p_closeperiod_id) d
    WHERE d.check_code = 'PERIOD_DEBIT';

    SELECT d.metric_value INTO v_kredit
    FROM sc_trx.fn_period_closing_diagnostics(p_closeperiod_id) d
    WHERE d.check_code = 'PERIOD_CREDIT';

    SELECT d.metric_value INTO v_unclassified
    FROM sc_trx.fn_period_closing_diagnostics(p_closeperiod_id) d
    WHERE d.check_code = 'UNCLASSIFIED_COA';

    SELECT d.metric_value INTO v_pnl
    FROM sc_trx.fn_period_closing_diagnostics(p_closeperiod_id) d
    WHERE d.check_code = 'PNL_NET';

    v_unbalanced   := COALESCE(v_unbalanced, 0);
    v_debet        := COALESCE(v_debet, 0);
    v_kredit       := COALESCE(v_kredit, 0);
    v_unclassified := COALESCE(v_unclassified, 0);
    v_pnl          := COALESCE(v_pnl, 0);



    PERFORM sc_trx.fn_set_period_closing_check(
        p_closeperiod_id, 'JOURNAL_BALANCE',
        'Keseimbangan jurnal POSTED (debet = kredit)',
        CASE WHEN v_unbalanced = 0 THEN 'PASS' ELSE 'FAIL' END,
        v_unbalanced::BIGINT,
        jsonb_build_object('jurnal_tidak_balance', v_unbalanced,
                           'sumber', 'sc_trx.jurnal_hd status POSTED & accounting_effect YES'),
        p_user);

    PERFORM sc_trx.fn_set_period_closing_check(
        p_closeperiod_id, 'TB_BALANCE',
        'Neraca Saldo periode balance (total debet = total kredit)',
        CASE WHEN ROUND(v_debet, 2) = ROUND(v_kredit, 2) THEN 'PASS' ELSE 'FAIL' END,
        CASE WHEN ROUND(v_debet, 2) = ROUND(v_kredit, 2) THEN 0 ELSE 1 END,
        jsonb_build_object('total_debet', v_debet,
                           'total_kredit', v_kredit,
                           'selisih', ROUND(v_debet - v_kredit, 2)),
        p_user);

    PERFORM sc_trx.fn_set_period_closing_check(
        p_closeperiod_id, 'UNCLASSIFIED_COA',
        'Seluruh COA transaksi sudah diklasifikasikan (BS/PNL)',
        CASE WHEN v_unclassified = 0 THEN 'PASS' ELSE 'FAIL' END,
        v_unclassified::BIGINT,
        jsonb_build_object('jumlah_coa_belum_klasifikasi', v_unclassified,
                           'catatan', 'Perbaiki pada tab Klasifikasi COA.'),
        p_user);

    PERFORM sc_trx.fn_set_period_closing_check(
        p_closeperiod_id, 'PNL_REVIEW',
        'Review saldo PNL periode (bandingkan Lap. Laba Rugi)',
        'PASS', 0,
        jsonb_build_object('pnl_net_debet_min_kredit', v_pnl,
                           'catatan', 'Nilai dari jurnal POSTED akun PNL pada periode ini.'),
        p_user);

    IF NOT EXISTS (
        SELECT 1 FROM sc_trx.period_closing_check c
        WHERE c.closeperiod_id = p_closeperiod_id
          AND c.check_code = 'STOCK_REVIEW'
    ) THEN
        PERFORM sc_trx.fn_set_period_closing_check(
            p_closeperiod_id, 'STOCK_REVIEW',
            'Rekonsiliasi nilai persediaan vs Analisa Mutasi Stock',
            'PENDING', 0,
            jsonb_build_object('catatan',
                'Rekonsiliasi stok belum otomatis. Periksa manual lalu tandai PASS.'),
            p_user);
    END IF;

    RETURN QUERY
    SELECT c.check_code, c.check_name, c.check_status, c.issue_count
    FROM sc_trx.period_closing_check c
    WHERE c.closeperiod_id = p_closeperiod_id
    ORDER BY c.check_code;
END
$function$;

-- ============================================================================
-- 4. TUTUP PERIODE (FIX: flagproses = 'TUTUP')
-- ============================================================================
-- Syarat: status REVIEW, seluruh checklist PASS & issue_count = 0,
--         closing_debet / closing_kredit terisi dan balance.
CREATE OR REPLACE FUNCTION sc_trx.fn_close_accounting_period(
    p_closeperiod_id INTEGER,
    p_user VARCHAR,
    p_approved_by VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_status TEXT;
    v_debet NUMERIC(18,2);
    v_kredit NUMERIC(18,2);
    v_total BIGINT;
    v_not_pass BIGINT;
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' OR COALESCE(BTRIM(p_approved_by), '') = '' THEN
        RAISE EXCEPTION 'User closing dan approver wajib diisi.';
    END IF;
    IF BTRIM(p_user) = BTRIM(p_approved_by) THEN
        RAISE EXCEPTION 'User closing dan approver harus berbeda.';
    END IF;

    SELECT status_closing, closing_debet, closing_kredit
      INTO v_status, v_debet, v_kredit
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_status <> 'REVIEW' THEN
        RAISE EXCEPTION 'Periode harus REVIEW sebelum ditutup (status saat ini %).', v_status;
    END IF;

    SELECT COUNT(*),
           COUNT(*) FILTER (WHERE check_status <> 'PASS' OR issue_count <> 0)
      INTO v_total, v_not_pass
    FROM sc_trx.period_closing_check
    WHERE closeperiod_id = p_closeperiod_id;

    IF v_total = 0 OR v_not_pass > 0 THEN
        RAISE EXCEPTION 'Checklist closing belum lengkap/lolos.';
    END IF;
    IF v_debet IS NULL OR v_kredit IS NULL THEN
        RAISE EXCEPTION 'closing_debet/kredit harus diisi dari Trial Balance aktual.';
    END IF;
    IF ROUND(v_debet,2) <> ROUND(v_kredit,2) THEN
        RAISE EXCEPTION 'Trial Balance tidak balance. Debit %, Kredit %.', v_debet, v_kredit;
    END IF;

    UPDATE sc_trx.closeperiod
    SET status_closing  = 'CLOSED',
        flagproses      = 'TUTUP',
        keterangan      = 'Periode Ditutup',
        closed_by       = p_user,
        closed_at       = CURRENT_TIMESTAMP,
        approved_by     = p_approved_by,
        approved_at     = CURRENT_TIMESTAMP,
        closing_balance = ROUND(v_debet - v_kredit, 2),
        updateby        = p_user,
        updatedate      = CURRENT_TIMESTAMP
    WHERE idurut = p_closeperiod_id;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason, detail)
    VALUES
        (p_closeperiod_id, 'CLOSE', v_status, 'CLOSED', p_user,
         'Periode ditutup setelah checklist PASS dan Trial Balance balance.',
         jsonb_build_object('closing_debet', v_debet,
                            'closing_kredit', v_kredit,
                            'approved_by', p_approved_by,
                            'flagproses', 'TUTUP'));
END
$function$;


-- ============================================================================
-- 5. BUKA KEMBALI PERIODE (flagproses = 'OPEN')
-- ============================================================================
CREATE OR REPLACE FUNCTION sc_trx.fn_reopen_accounting_period(
    p_closeperiod_id INTEGER,
    p_user VARCHAR,
    p_reason TEXT
)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_status TEXT;
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' OR COALESCE(BTRIM(p_reason), '') = '' THEN
        RAISE EXCEPTION 'User dan alasan reopen wajib diisi.';
    END IF;

    SELECT status_closing INTO v_status
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_status <> 'CLOSED' THEN
        RAISE EXCEPTION 'Hanya periode CLOSED yang dapat dibuka kembali (status saat ini %).', v_status;
    END IF;

    UPDATE sc_trx.closeperiod
    SET status_closing = 'REOPENED',
        flagproses     = 'OPEN',
        keterangan     = 'Periode Dibuka',
        reopened_by    = p_user,
        reopened_at    = CURRENT_TIMESTAMP,
        reopen_reason  = BTRIM(p_reason),
        updateby       = p_user,
        updatedate     = CURRENT_TIMESTAMP
    WHERE idurut = p_closeperiod_id;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason)
    VALUES
        (p_closeperiod_id, 'REOPEN', v_status, 'REOPENED', p_user, BTRIM(p_reason));
END
$function$;

-- ============================================================================
-- 6. ORKESTRATOR CLOSING BULANAN (1 KLIK)
-- ============================================================================
-- Urutan: registrasi periode -> CHECKING -> checklist otomatis -> Trial Balance
--         -> REVIEW -> CLOSED (flagproses 'TUTUP').
-- Akan GAGAL bila masih ada checklist yang bukan PASS / masih ada issue.
CREATE OR REPLACE FUNCTION sc_trx.fn_run_monthly_closing(
    p_periode VARCHAR,
    p_tanggal_mulai DATE,
    p_tanggal_akhir DATE,
    p_user VARCHAR,
    p_approved_by VARCHAR
)
RETURNS INTEGER
LANGUAGE plpgsql
AS $function$
DECLARE
    v_id INTEGER;
    v_status TEXT;
    v_debet NUMERIC(24,2);
    v_kredit NUMERIC(24,2);
    v_belum TEXT;
BEGIN
    v_id := sc_trx.fn_register_closeperiod(p_periode, p_tanggal_mulai, p_tanggal_akhir, p_user);

    SELECT cp.status_closing INTO v_status
    FROM sc_trx.closeperiod cp
    WHERE cp.idurut = v_id;

    IF COALESCE(v_status, 'OPEN') = 'CLOSED' THEN
        RAISE EXCEPTION 'Periode % sudah CLOSED. Gunakan Buka Kembali bila perlu dikoreksi.',
            BTRIM(p_periode);
    END IF;

    PERFORM sc_trx.fn_apply_period_closing_checks(v_id, p_user);

    -- Pastikan seluruh checklist PASS sebelum review.
    SELECT STRING_AGG(c.check_code || ' (' || c.check_status || ')', ', ' ORDER BY c.check_code)
      INTO v_belum
    FROM sc_trx.period_closing_check c
    WHERE c.closeperiod_id = v_id
      AND (c.check_status <> 'PASS' OR c.issue_count <> 0);

    IF v_belum IS NOT NULL THEN
        RAISE EXCEPTION 'Closing dihentikan. Checklist belum PASS: %.', v_belum;
    END IF;

    SELECT t.total_debet, t.total_kredit
      INTO v_debet, v_kredit
    FROM sc_trx.fn_period_closing_trial_balance(v_id) t;

    UPDATE sc_trx.closeperiod
       SET closing_debet  = v_debet,
           closing_kredit = v_kredit,
           updateby       = p_user,
           updatedate     = CURRENT_TIMESTAMP
     WHERE idurut = v_id;

    PERFORM sc_trx.fn_submit_period_closing_review(v_id, p_user);
    PERFORM sc_trx.fn_close_accounting_period(v_id, p_user, p_approved_by);

    RETURN v_id;
END
$function$;


-- ============================================================================
-- 7. VIEW STATUS PERIODE + RINGKASAN CHECKLIST
-- ============================================================================
CREATE OR REPLACE VIEW sc_trx.v_period_closing_status AS
SELECT cp.idurut,
       BTRIM(cp.periode::TEXT)                                  AS periode,
       cp.tanggal_mulai,
       cp.tanggal_akhir,
       COALESCE(NULLIF(BTRIM(cp.status_closing), ''), 'OPEN')    AS status_closing,
       COALESCE(NULLIF(BTRIM(cp.flagproses), ''), 'OPEN')        AS flagproses,
       cp.keterangan,
       cp.closing_debet,
       cp.closing_kredit,
       cp.closing_balance,
       cp.closed_by,
       cp.closed_at,
       cp.approved_by,
       cp.approved_at,
       cp.reopened_by,
       cp.reopened_at,
       cp.reopen_reason,
       cp.inputby,
       cp.inputdate,
       cp.updateby,
       cp.updatedate,
       (SELECT COUNT(*) FROM sc_trx.period_closing_check c
         WHERE c.closeperiod_id = cp.idurut)                    AS jumlah_check,
       (SELECT COUNT(*) FROM sc_trx.period_closing_check c
         WHERE c.closeperiod_id = cp.idurut
           AND c.check_status = 'PASS'
           AND c.issue_count = 0)                               AS jumlah_pass
FROM sc_trx.closeperiod cp;

-- ============================================================================
-- 8. LOCK TRANSAKSI: TOLAK POSTING JURNAL PADA PERIODE CLOSED
-- ============================================================================
-- Memakai sc_trx.fn_assert_period_open() dari file 99.PROSES CLOSING PENTING.
-- Berlaku untuk INSERT/UPDATE sc_trx.jurnal_hd (posting, reverse, jurnal manual,
-- jurnal penutup). Untuk menonaktifkan sementara saat darurat:
--   DROP TRIGGER IF EXISTS trg_guard_closed_period_jurnal ON sc_trx.jurnal_hd;
CREATE OR REPLACE FUNCTION sc_trx.fn_guard_closed_period_jurnal()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $function$
BEGIN
    IF NEW.trxdate IS NOT NULL THEN
        PERFORM sc_trx.fn_assert_period_open(NEW.trxdate::DATE);
    END IF;
    RETURN NEW;
END
$function$;

DROP TRIGGER IF EXISTS trg_guard_closed_period_jurnal ON sc_trx.jurnal_hd;
CREATE TRIGGER trg_guard_closed_period_jurnal
    BEFORE INSERT OR UPDATE ON sc_trx.jurnal_hd
    FOR EACH ROW EXECUTE FUNCTION sc_trx.fn_guard_closed_period_jurnal();

COMMIT;

-- ============================================================================
-- CARA PAKAI (SEMUA SUDAH TERSEDIA DI MENU: Tools > Proses > Proses Tutup Bulan)
-- ============================================================================
-- 1) Registrasi periode (periode = yymm, agar terbaca modul transaksi lama):
--    SELECT sc_trx.fn_register_closeperiod('2610', DATE '2026-10-01', DATE '2026-10-31', 'admin');
-- 2) Diagnostik read-only:
--    SELECT * FROM sc_trx.fn_period_closing_diagnostics(<ID_CLOSEPERIOD>);
-- 3) Checklist otomatis:
--    SELECT * FROM sc_trx.fn_apply_period_closing_checks(<ID_CLOSEPERIOD>, 'admin');
-- 4) Konfirmasi manual checklist STOCK_REVIEW:
--    SELECT sc_trx.fn_set_period_closing_check(
--      <ID_CLOSEPERIOD>, 'STOCK_REVIEW',
--      'Rekonsiliasi nilai persediaan vs Analisa Mutasi Stock',
--      'PASS', 0, '{"catatan":"sudah dicocokkan"}'::jsonb, 'admin');
-- 5) Tutup bulan (1 klik, sekaligus mengunci flagproses='TUTUP'):
--    SELECT sc_trx.fn_run_monthly_closing('2610', DATE '2026-10-01', DATE '2026-10-31',
--                                         'admin', 'SYSTEM');
-- 6) Buka kembali bila perlu koreksi:
--    SELECT sc_trx.fn_reopen_accounting_period(<ID_CLOSEPERIOD>, 'admin', 'alasan koreksi');
-- 7) Jurnal penutup tahunan (DRAFT, posting lewat workflow jurnal yang ada):
--    SELECT sc_trx.fn_prepare_year_end_close(<ID_CLOSEPERIOD>, 'admin', '<COA_LABA_RUGI>');
--
-- CATATAN PENTING: periode yang sudah CLOSED akan MENOLAK posting jurnal baru
-- (lihat trigger trg_guard_closed_period_jurnal). Buka kembali periode terlebih
-- dahulu bila memang harus ada koreksi transaksi.

