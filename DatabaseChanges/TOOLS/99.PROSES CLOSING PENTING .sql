/*
===============================================================================
J-ACCOUNTING - PATCH ACCOUNTING CLOSING (POSTGRESQL)
===============================================================================
Tujuan:
1. Mempertahankan sc_trx.closeperiod dan skema jurnal_hd/jurnal_dt yang sudah ada.
2. Mengadaptasi kontrol terhadap laporan lama: Jurnal Transaksi, Buku Besar Detail,
   Neraca Saldo, Laba Rugi, serta rekonsiliasi persediaan.
3. Memisahkan closing bulanan (validasi, rekonsiliasi, checklist, lock) dari
   closing tahunan (jurnal penutup P&L).
4. Mengambil sumber buku besar hanya dari jurnal POSTED dan accounting_effect='YES'.
5. Menyiapkan jurnal penutup tahunan berstatus DRAFT, tanpa mengubah jurnal lama.
6. Mencegah jurnal penutup ganda untuk periode yang sama.
7. Menyediakan panduan pemakaian per fungsi dan tahap proses.

PENTING:
- Script mengisi klasifikasi awal otomatis dari struktur COA: awalan 1-3 = BS,
  awalan 4-7 = PNL; akun awalan 8 dibiarkan untuk pemeriksaan manual.
- Periksa kembali akun khusus dan akun induk/detail sebelum closing produksi.
- Akun lawan laba/rugi (retained earnings / laba-rugi tahun berjalan) harus
  ditentukan secara eksplisit saat menjalankan fungsi.
- Jurnal penutup hanya dibuat sebagai DRAFT agar proses posting tetap memakai
  mekanisme posting J-Accounting yang sudah ada.
- Jalankan seluruh script ini sekaligus di database TEST terlebih dahulu.
===============================================================================
*/

BEGIN;

-- ============================================================================
-- PANDUAN ADAPTASI LAPORAN EXCEL LEGACY
-- ============================================================================
-- Jurnal Transaksi: sumber audit transaksi per nomor jurnal/COA.
-- Buku Besar Detail: telusuri saldo awal dan mutasi per COA.
-- Neraca Saldo: cocokkan saldo awal, mutasi debit/kredit, dan saldo akhir.
-- Laba Rugi: cocokkan kelompok P&L untuk Bulan Berjalan dan Tahun Berjalan.
-- Analisa Mutasi Stock: rekonsiliasi kuantitas/nilai stok terhadap akun persediaan.
--
-- Excel merupakan laporan pembanding, bukan sumber transaksi SQL. Semua angka
-- SQL diambil dari jurnal POSTED dengan accounting_effect='YES'. Rekonsiliasi
-- stok belum dibuat otomatis karena skema tabel stock ledger aktual belum
-- tersedia di file yang diperiksa.
--
-- URUTAN: install di TEST -> siapkan periode -> diagnostik -> rekonsiliasi laporan
-- -> checklist PASS -> review/approval -> close bulanan. Jurnal penutup P&L hanya
-- untuk closing tahunan dan harus direview/posting melalui workflow existing.

CREATE SCHEMA IF NOT EXISTS sc_trx;

-- Fallback agar script dapat berjalan pada database yang belum memiliki header.
-- Jika tabel sudah ada, struktur dan data existing tidak dihapus.
CREATE TABLE IF NOT EXISTS sc_trx.closeperiod (
    idurut SERIAL PRIMARY KEY,
    periode CHARACTER(20),
    flagproses CHARACTER(6),
    keterangan TEXT,
    inputby VARCHAR(50),
    inputdate TIMESTAMP WITHOUT TIME ZONE,
    updateby VARCHAR(50),
    updatedate TIMESTAMP WITHOUT TIME ZONE,
    printby VARCHAR(50),
    printdate TIMESTAMP WITHOUT TIME ZONE,
    docnotmp CHARACTER(30)
);

-- A. Lengkapi header periode existing. Kolom lama dipertahankan.
ALTER TABLE sc_trx.closeperiod
    ADD COLUMN IF NOT EXISTS tanggal_mulai DATE,
    ADD COLUMN IF NOT EXISTS tanggal_akhir DATE,
    ADD COLUMN IF NOT EXISTS status_closing VARCHAR(20) NOT NULL DEFAULT 'OPEN',
    ADD COLUMN IF NOT EXISTS closed_by VARCHAR(50),
    ADD COLUMN IF NOT EXISTS closed_at TIMESTAMP WITHOUT TIME ZONE,
    ADD COLUMN IF NOT EXISTS approved_by VARCHAR(50),
    ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP WITHOUT TIME ZONE,
    ADD COLUMN IF NOT EXISTS reopened_by VARCHAR(50),
    ADD COLUMN IF NOT EXISTS reopened_at TIMESTAMP WITHOUT TIME ZONE,
    ADD COLUMN IF NOT EXISTS reopen_reason TEXT,
    ADD COLUMN IF NOT EXISTS closing_debet NUMERIC(18,2),
    ADD COLUMN IF NOT EXISTS closing_kredit NUMERIC(18,2),
    ADD COLUMN IF NOT EXISTS closing_balance NUMERIC(18,2);

UPDATE sc_trx.closeperiod
SET status_closing = 'OPEN'
WHERE status_closing IS NULL OR BTRIM(status_closing) = '';

DO $patch$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.closeperiod'::regclass
          AND conname = 'ck_closeperiod_status_closing'
    ) THEN
        ALTER TABLE sc_trx.closeperiod
        ADD CONSTRAINT ck_closeperiod_status_closing
        CHECK (status_closing IN ('OPEN','CHECKING','REVIEW','CLOSED','REOPENED'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conrelid = 'sc_trx.closeperiod'::regclass
          AND conname = 'ck_closeperiod_date_range'
    ) THEN
        ALTER TABLE sc_trx.closeperiod
        ADD CONSTRAINT ck_closeperiod_date_range
        CHECK (tanggal_mulai IS NULL OR tanggal_akhir IS NULL
               OR tanggal_mulai <= tanggal_akhir);
    END IF;
END
$patch$;

CREATE INDEX IF NOT EXISTS ix_closeperiod_date_range
    ON sc_trx.closeperiod (tanggal_mulai, tanggal_akhir);

CREATE INDEX IF NOT EXISTS ix_closeperiod_status
    ON sc_trx.closeperiod (status_closing);

-- B. Klasifikasi COA khusus closing.
-- account_class:
--   PNL = akun nominal pendapatan/beban yang ditutup akhir tahun.
--   BS  = akun neraca yang saldonya tidak dinolkan oleh jurnal penutup P&L.
-- Isikan hanya untuk COA yang ditemukan pada jurnal POSTED di periode yang akan ditutup.
CREATE TABLE IF NOT EXISTS sc_trx.period_closing_coa_rule (
    idcoa VARCHAR(20) PRIMARY KEY,
    account_class VARCHAR(10) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    description TEXT,
    created_by VARCHAR(50),
    created_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(50),
    updated_at TIMESTAMP WITHOUT TIME ZONE,
    CONSTRAINT ck_period_closing_coa_class
        CHECK (account_class IN ('PNL','BS'))
);

-- B1. Klasifikasi otomatis berdasarkan struktur master COA J-Accounting.
-- Berdasarkan master COA yang diberikan:
--   Awalan 1, 2, 3 = AKTIVA, PASIVA, MODAL       -> BS
--   Awalan 4, 5, 6, 7 = PENDAPATAN / BIAYA       -> PNL
--   Awalan 8 = PERKIRAAN AYAT SILANG / LAIN-LAIN -> tidak diklasifikasikan
-- Hanya akun level detail (level >= 4) yang dimasukkan; akun induk tidak dipakai
-- sebagai akun jurnal. COA yang sudah diklasifikasikan manual tidak ditimpa.
-- Kolom yang dipakai sesuai master COA: idcoa, nmcoa, level.
INSERT INTO sc_trx.period_closing_coa_rule
    (idcoa, account_class, active, description, created_by)
SELECT
    BTRIM(c.idcoa::TEXT),
    CASE
        WHEN LEFT(BTRIM(c.idcoa::TEXT), 1) IN ('1','2','3') THEN 'BS'
        WHEN LEFT(BTRIM(c.idcoa::TEXT), 1) IN ('4','5','6','7') THEN 'PNL'
    END,
    TRUE,
    BTRIM(c.nmcoa::TEXT),
    'SYSTEM'
FROM sc_mst.coa c
WHERE BTRIM(c.idcoa::TEXT) ~ '^[1-7][0-9]*$'
  AND COALESCE(c.level::INTEGER, 0) >= 4
ON CONFLICT (idcoa) DO NOTHING;

-- C. Hasil checklist dan audit log.
CREATE TABLE IF NOT EXISTS sc_trx.period_closing_check (
    id BIGSERIAL PRIMARY KEY,
    closeperiod_id INTEGER NOT NULL
        REFERENCES sc_trx.closeperiod(idurut) ON DELETE CASCADE,
    check_code VARCHAR(50) NOT NULL,
    check_name VARCHAR(150) NOT NULL,
    check_status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    issue_count BIGINT NOT NULL DEFAULT 0,
    issue_detail JSONB,
    checked_by VARCHAR(50),
    checked_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT ck_period_closing_check_status
        CHECK (check_status IN ('PENDING','PASS','WARNING','FAIL')),
    CONSTRAINT uq_period_closing_check_code UNIQUE (closeperiod_id, check_code),
    CONSTRAINT ck_period_closing_check_issue_count CHECK (issue_count >= 0)
);

CREATE TABLE IF NOT EXISTS sc_trx.period_closing_log (
    id BIGSERIAL PRIMARY KEY,
    closeperiod_id INTEGER NOT NULL REFERENCES sc_trx.closeperiod(idurut),
    action VARCHAR(30) NOT NULL,
    old_status VARCHAR(20),
    new_status VARCHAR(20) NOT NULL,
    action_by VARCHAR(50) NOT NULL,
    action_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reason TEXT,
    detail JSONB
);

CREATE INDEX IF NOT EXISTS ix_period_closing_check_header
    ON sc_trx.period_closing_check(closeperiod_id);

CREATE INDEX IF NOT EXISTS ix_period_closing_log_header
    ON sc_trx.period_closing_log(closeperiod_id, action_at);

-- D. Validasi rentang tanggal tidak tumpang tindih.
CREATE OR REPLACE FUNCTION sc_trx.fn_validate_closeperiod_range()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $function$
BEGIN
    -- Perubahan status/audit tidak perlu memvalidasi ulang rentang tanggal.
    IF TG_OP = 'UPDATE'
       AND NEW.tanggal_mulai IS NOT DISTINCT FROM OLD.tanggal_mulai
       AND NEW.tanggal_akhir IS NOT DISTINCT FROM OLD.tanggal_akhir THEN
        RETURN NEW;
    END IF;

    IF NEW.tanggal_mulai IS NULL OR NEW.tanggal_akhir IS NULL THEN
        RETURN NEW;
    END IF;

    IF NEW.tanggal_mulai > NEW.tanggal_akhir THEN
        RAISE EXCEPTION 'tanggal_mulai tidak boleh melewati tanggal_akhir.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM sc_trx.closeperiod cp
        WHERE cp.idurut <> COALESCE(NEW.idurut, -1)
          AND cp.tanggal_mulai IS NOT NULL
          AND cp.tanggal_akhir IS NOT NULL
          AND daterange(cp.tanggal_mulai, cp.tanggal_akhir, '[]')
              && daterange(NEW.tanggal_mulai, NEW.tanggal_akhir, '[]')
    ) THEN
        RAISE EXCEPTION 'Rentang tanggal closing tumpang tindih dengan periode lain.';
    END IF;

    RETURN NEW;
END
$function$;

DROP TRIGGER IF EXISTS trg_validate_closeperiod_range ON sc_trx.closeperiod;
CREATE TRIGGER trg_validate_closeperiod_range
BEFORE INSERT OR UPDATE
ON sc_trx.closeperiod
FOR EACH ROW EXECUTE FUNCTION sc_trx.fn_validate_closeperiod_range();

-- E. API guard untuk dipanggil dari trigger/fungsi transaksi, posting, dan reverse.
CREATE OR REPLACE FUNCTION sc_trx.fn_assert_period_open(p_tanggal DATE)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_periode TEXT;
BEGIN
    IF p_tanggal IS NULL THEN
        RAISE EXCEPTION 'Tanggal transaksi wajib diisi.';
    END IF;

    SELECT BTRIM(cp.periode::TEXT)
      INTO v_periode
    FROM sc_trx.closeperiod cp
    WHERE cp.tanggal_mulai IS NOT NULL
      AND cp.tanggal_akhir IS NOT NULL
      AND p_tanggal BETWEEN cp.tanggal_mulai AND cp.tanggal_akhir
      AND cp.status_closing = 'CLOSED'
    LIMIT 1;

    IF FOUND THEN
        RAISE EXCEPTION 'Periode % sudah CLOSED; transaksi/posting/reverse ditolak.',
            COALESCE(v_periode, '(tanpa nama)');
    END IF;
END
$function$;

-- F. Mulai proses pemeriksaan periode.
CREATE OR REPLACE FUNCTION sc_trx.fn_start_period_closing(
    p_closeperiod_id INTEGER,
    p_user VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_status TEXT;
    v_start DATE;
    v_end DATE;
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' THEN
        RAISE EXCEPTION 'p_user wajib diisi.';
    END IF;

    SELECT status_closing, tanggal_mulai, tanggal_akhir
      INTO v_status, v_start, v_end
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_start IS NULL OR v_end IS NULL THEN
        RAISE EXCEPTION 'tanggal_mulai dan tanggal_akhir wajib diisi.';
    END IF;
    IF v_status = 'CLOSED' THEN
        RAISE EXCEPTION 'Periode sudah CLOSED.';
    END IF;

    UPDATE sc_trx.closeperiod
       SET status_closing = 'CHECKING',
           updateby = p_user,
           updatedate = CURRENT_TIMESTAMP
     WHERE idurut = p_closeperiod_id;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason)
    VALUES
        (p_closeperiod_id, 'START_CHECK', v_status, 'CHECKING', p_user,
         'Memulai pemeriksaan closing.');
END
$function$;

-- G. Simpan checklist. Status CHECKING wajib sebelum checklist diubah.
CREATE OR REPLACE FUNCTION sc_trx.fn_set_period_closing_check(
    p_closeperiod_id INTEGER,
    p_check_code VARCHAR,
    p_check_name VARCHAR,
    p_check_status VARCHAR,
    p_issue_count BIGINT DEFAULT 0,
    p_issue_detail JSONB DEFAULT NULL,
    p_user VARCHAR DEFAULT NULL
)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_status TEXT;
BEGIN
    SELECT status_closing INTO v_status
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_status <> 'CHECKING' THEN
        RAISE EXCEPTION 'Checklist hanya boleh diubah pada status CHECKING.';
    END IF;
    IF COALESCE(BTRIM(p_check_code), '') = ''
       OR COALESCE(BTRIM(p_check_name), '') = '' THEN
        RAISE EXCEPTION 'check_code dan check_name wajib diisi.';
    END IF;
    IF p_check_status IS NULL
       OR p_check_status NOT IN ('PENDING','PASS','WARNING','FAIL') THEN
        RAISE EXCEPTION 'check_status tidak valid.';
    END IF;
    IF p_issue_count IS NULL OR p_issue_count < 0 THEN
        RAISE EXCEPTION 'issue_count tidak boleh negatif/NULL.';
    END IF;

    INSERT INTO sc_trx.period_closing_check
        (closeperiod_id, check_code, check_name, check_status,
         issue_count, issue_detail, checked_by, checked_at)
    VALUES
        (p_closeperiod_id, BTRIM(p_check_code), BTRIM(p_check_name), p_check_status,
         p_issue_count, p_issue_detail, NULLIF(BTRIM(p_user), ''), CURRENT_TIMESTAMP)
    ON CONFLICT (closeperiod_id, check_code)
    DO UPDATE SET
        check_name = EXCLUDED.check_name,
        check_status = EXCLUDED.check_status,
        issue_count = EXCLUDED.issue_count,
        issue_detail = EXCLUDED.issue_detail,
        checked_by = EXCLUDED.checked_by,
        checked_at = CURRENT_TIMESTAMP;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason, detail)
    VALUES
        (p_closeperiod_id, 'UPDATE_CHECK', v_status, v_status,
         COALESCE(NULLIF(BTRIM(p_user), ''), CURRENT_USER),
         'Checklist diperbarui.',
         jsonb_build_object('check_code', p_check_code,
                            'check_status', p_check_status,
                            'issue_count', p_issue_count));
END
$function$;


-- ============================================================================
-- DIAGNOSTIK CLOSING PERIODE (READ ONLY)
-- ============================================================================
-- Cara pakai:
-- SELECT * FROM sc_trx.fn_period_closing_diagnostics(<ID_CLOSEPERIOD>);
-- JOURNAL_UNBALANCED harus 0. Total mutasi debit dan kredit harus sama.
-- PNL_NET adalah net debit-kredit akun PNL, bukan otomatis laba bersih bertanda
-- sama dengan laporan Excel; verifikasi tanda/kelompok pada Lap. Laba Rugi.
CREATE OR REPLACE FUNCTION sc_trx.fn_period_closing_diagnostics(
    p_closeperiod_id INTEGER
)
RETURNS TABLE (
    check_code VARCHAR(40),
    metric_name VARCHAR(120),
    metric_value NUMERIC(24,2),
    detail TEXT
)
LANGUAGE plpgsql
AS $function$
DECLARE
    v_start DATE;
    v_end DATE;
BEGIN
    SELECT tanggal_mulai, tanggal_akhir
      INTO v_start, v_end
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_start IS NULL OR v_end IS NULL OR v_start > v_end THEN
        RAISE EXCEPTION 'Rentang tanggal closing belum valid.';
    END IF;

    RETURN QUERY
    SELECT 'JOURNAL_UNBALANCED'::VARCHAR(40),
           'Jumlah jurnal POSTED yang tidak balance'::VARCHAR(120),
           COUNT(*)::NUMERIC(24,2),
           'Target 0; validasi dilakukan per header jurnal.'
    FROM (
        SELECT h.id
        FROM sc_trx.jurnal_hd h
        WHERE h.trxdate::DATE BETWEEN v_start AND v_end
          AND h.status = 'POSTED' AND h.accounting_effect = 'YES'
        GROUP BY h.id, h.total_debet, h.total_kredit
        HAVING ROUND(COALESCE(h.total_debet,0)::NUMERIC,2)
            <> ROUND(COALESCE(h.total_kredit,0)::NUMERIC,2)
    ) q;

    RETURN QUERY
    SELECT 'PERIOD_DEBIT'::VARCHAR(40), 'Total debit mutasi'::VARCHAR(120),
           ROUND(COALESCE(SUM(d.debet),0)::NUMERIC,2),
           'Bandingkan dengan total mutasi debit Neraca Saldo.'
    FROM sc_trx.jurnal_dt d
    JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
    WHERE h.trxdate::DATE BETWEEN v_start AND v_end
      AND h.status = 'POSTED' AND h.accounting_effect = 'YES';

    RETURN QUERY
    SELECT 'PERIOD_CREDIT'::VARCHAR(40), 'Total kredit mutasi'::VARCHAR(120),
           ROUND(COALESCE(SUM(d.kredit),0)::NUMERIC,2),
           'Bandingkan dengan total mutasi kredit Neraca Saldo.'
    FROM sc_trx.jurnal_dt d
    JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
    WHERE h.trxdate::DATE BETWEEN v_start AND v_end
      AND h.status = 'POSTED' AND h.accounting_effect = 'YES';

    RETURN QUERY
    SELECT 'PERIOD_NET'::VARCHAR(40), 'Selisih debit - kredit periode'::VARCHAR(120),
           ROUND(COALESCE(SUM(d.debet - d.kredit),0)::NUMERIC,2),
           'Target 0 secara total; tidak menggantikan pemeriksaan per jurnal.'
    FROM sc_trx.jurnal_dt d
    JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
    WHERE h.trxdate::DATE BETWEEN v_start AND v_end
      AND h.status = 'POSTED' AND h.accounting_effect = 'YES';

    RETURN QUERY
    SELECT 'UNCLASSIFIED_COA'::VARCHAR(40),
           'Jumlah COA belum diklasifikasikan'::VARCHAR(120),
           COUNT(DISTINCT BTRIM(d.idcoa::TEXT))::NUMERIC(24,2),
           'Harus 0 sebelum jurnal closing tahunan.'
    FROM sc_trx.jurnal_dt d
    JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
    LEFT JOIN sc_trx.period_closing_coa_rule r
      ON r.idcoa = BTRIM(d.idcoa::TEXT) AND r.active = TRUE
    WHERE h.trxdate::DATE BETWEEN v_start AND v_end
      AND h.status = 'POSTED' AND h.accounting_effect = 'YES'
      AND r.idcoa IS NULL;

    RETURN QUERY
    SELECT 'PNL_NET'::VARCHAR(40), 'Saldo neto akun PNL (debit - kredit)'::VARCHAR(120),
           ROUND(COALESCE(SUM(d.debet - d.kredit),0)::NUMERIC,2),
           'Cocokkan hasil dengan Lap. Laba Rugi dan tanda saldo COA.'
    FROM sc_trx.jurnal_dt d
    JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
    JOIN sc_trx.period_closing_coa_rule r
      ON r.idcoa = BTRIM(d.idcoa::TEXT)
     AND r.active = TRUE AND r.account_class = 'PNL'
    WHERE h.trxdate::DATE BETWEEN v_start AND v_end
      AND h.status = 'POSTED' AND h.accounting_effect = 'YES';
END
$function$;

-- View mutasi per tanggal dan COA. Tidak memasukkan saldo awal karena saldo
-- awal harus diambil dari mekanisme carry-forward resmi / laporan Neraca Saldo.
CREATE OR REPLACE VIEW sc_trx.v_period_closing_coa_movement AS
SELECT h.trxdate::DATE AS trxdate,
       BTRIM(d.idcoa::TEXT) AS idcoa,
       COUNT(*)::BIGINT AS jumlah_baris,
       ROUND(COALESCE(SUM(d.debet),0)::NUMERIC,2) AS total_debet,
       ROUND(COALESCE(SUM(d.kredit),0)::NUMERIC,2) AS total_kredit,
       ROUND(COALESCE(SUM(d.debet-d.kredit),0)::NUMERIC,2) AS saldo_net_debet_kredit
FROM sc_trx.jurnal_hd h
JOIN sc_trx.jurnal_dt d ON d.jurnal_id = h.id
WHERE h.status = 'POSTED' AND h.accounting_effect = 'YES'
GROUP BY h.trxdate::DATE, BTRIM(d.idcoa::TEXT);

-- H. Buat DRAFT jurnal penutup dari akun PNL yang:
--    (1) memiliki jurnal POSTED dan accounting_effect=YES pada periode,
--    (2) sudah diklasifikasikan PNL di period_closing_coa_rule,
--    (3) memiliki saldo neto tidak nol.
-- Akun lawan laba/rugi wajib dipilih eksplisit sebagai parameter.
CREATE OR REPLACE FUNCTION sc_trx.fn_prepare_year_end_close(
    p_closeperiod_id INTEGER,
    p_user VARCHAR,
    p_retained_earnings_coa VARCHAR
)
RETURNS BIGINT
LANGUAGE plpgsql
AS $function$
DECLARE
    v_period RECORD;
    v_unclassified TEXT;
    v_journal_id BIGINT;
    v_docno TEXT;
    v_uniqueid TEXT;
    v_total_debit NUMERIC(18,2);
    v_total_credit NUMERIC(18,2);
    v_count BIGINT;
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' THEN
        RAISE EXCEPTION 'p_user wajib diisi.';
    END IF;
    IF COALESCE(BTRIM(p_retained_earnings_coa), '') = '' THEN
        RAISE EXCEPTION 'COA laba/rugi penutup wajib ditentukan.';
    END IF;

    SELECT idurut, periode, tanggal_mulai, tanggal_akhir, status_closing
      INTO v_period
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_period.tanggal_mulai IS NULL OR v_period.tanggal_akhir IS NULL THEN
        RAISE EXCEPTION 'Tanggal periode belum lengkap.';
    END IF;
    IF v_period.status_closing NOT IN ('OPEN','CHECKING','REOPENED') THEN
        RAISE EXCEPTION 'Persiapan jurnal penutup hanya diizinkan saat OPEN/CHECKING/REOPENED.';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.coa c
        WHERE c.idcoa::TEXT = BTRIM(p_retained_earnings_coa)
    ) THEN
        RAISE EXCEPTION 'COA laba/rugi penutup % tidak ditemukan di sc_mst.coa.',
            p_retained_earnings_coa;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM sc_trx.period_closing_coa_rule r
        WHERE r.idcoa = BTRIM(p_retained_earnings_coa)
          AND r.active = TRUE
          AND r.account_class = 'BS'
    ) THEN
        RAISE EXCEPTION
            'COA lawan % harus didaftarkan aktif sebagai kelas BS pada sc_trx.period_closing_coa_rule.',
            p_retained_earnings_coa;
    END IF;

    -- Wajib klasifikasikan semua akun yang tampil pada jurnal POSTED periode ini.
    SELECT STRING_AGG(x.idcoa, ', ' ORDER BY x.idcoa)
      INTO v_unclassified
    FROM (
        SELECT DISTINCT BTRIM(jd.idcoa::TEXT) AS idcoa
        FROM sc_trx.jurnal_dt jd
        JOIN sc_trx.jurnal_hd jh ON jh.id = jd.jurnal_id
        LEFT JOIN sc_trx.period_closing_coa_rule r
          ON r.idcoa = BTRIM(jd.idcoa::TEXT) AND r.active
        WHERE jh.trxdate BETWEEN v_period.tanggal_mulai AND v_period.tanggal_akhir
          AND jh.status = 'POSTED'
          AND jh.accounting_effect = 'YES'
          AND r.idcoa IS NULL
    ) x;

    IF v_unclassified IS NOT NULL THEN
        RAISE EXCEPTION
            'Closing dihentikan. Klasifikasi COA belum diisi untuk akun transaksi berikut: %',
            v_unclassified;
    END IF;

    -- Mencegah jurnal penutup ganda untuk header periode yang sama.
    v_uniqueid := 'CLSPER-' || MD5('CLOSEPERIOD|' || p_closeperiod_id::TEXT);
    IF EXISTS (SELECT 1 FROM sc_trx.jurnal_hd WHERE uniqueid = v_uniqueid) THEN
        RAISE EXCEPTION
            'Jurnal penutup untuk closeperiod id % sudah ada (uniqueid=%).',
            p_closeperiod_id, v_uniqueid;
    END IF;

    -- Hitung nilai total jurnal penutup. Nilai debit/kredit dihitung ulang dari
    -- saldo neto semua akun PNL yang memiliki transaksi POSTED dalam periode.
    SELECT
        COALESCE(SUM(CASE WHEN s.net_balance < 0 THEN -s.net_balance ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN s.net_balance > 0 THEN  s.net_balance ELSE 0 END), 0),
        COUNT(*)
      INTO v_total_debit, v_total_credit, v_count
    FROM (
        SELECT BTRIM(jd.idcoa::TEXT) AS idcoa,
               ROUND(SUM(jd.debet - jd.kredit), 2) AS net_balance
        FROM sc_trx.jurnal_dt jd
        JOIN sc_trx.jurnal_hd jh ON jh.id = jd.jurnal_id
        JOIN sc_trx.period_closing_coa_rule r
          ON r.idcoa = BTRIM(jd.idcoa::TEXT)
         AND r.active = TRUE
         AND r.account_class = 'PNL'
        WHERE jh.trxdate BETWEEN v_period.tanggal_mulai AND v_period.tanggal_akhir
          AND jh.status = 'POSTED'
          AND jh.accounting_effect = 'YES'
        GROUP BY BTRIM(jd.idcoa::TEXT)
        HAVING ROUND(SUM(jd.debet - jd.kredit), 2) <> 0
    ) s;

    IF v_count = 0 THEN
        RAISE EXCEPTION 'Tidak ada saldo akun PNL nonzero untuk ditutup pada periode ini.';
    END IF;

    v_docno := 'CLS-' || LEFT(MD5(p_closeperiod_id::TEXT || v_period.tanggal_akhir::TEXT), 12);

    -- Buat header jurnal DRAFT. Posting tetap dilakukan lewat workflow existing.
    INSERT INTO sc_trx.jurnal_hd
        (uniqueid, source_uniqueid, docno, doctype, journal_type, trxdate,
         type_in_out, ref_docno, ref_doctype, idbranch, cabang, module,
         accounting_effect, currcode, kurs, total_debet, total_kredit, balance,
         status, keterangan, createdby, createddate)
    VALUES
        (v_uniqueid, v_uniqueid, v_docno, 'CLOSING', 'CLSPER',
         v_period.tanggal_akhir, 'OUT', COALESCE(BTRIM(v_period.periode::TEXT), ''),
         'CLOSEPERIOD', '', '', 'ACCOUNTING', 'YES', 'IDR', 1,
         0, 0, 0, 'DRAFT',
         'Jurnal penutup akhir tahun periode ' || COALESCE(BTRIM(v_period.periode::TEXT), ''),
         p_user, CURRENT_TIMESTAMP)
    RETURNING id INTO v_journal_id;

    -- Satu baris pembalik untuk setiap akun PNL yang bersaldo.
    INSERT INTO sc_trx.jurnal_dt
        (jurnal_id, journal_type, source_uniqueid, source_line_no, seq,
         account_role, value_source, idcoa, debet, kredit,
         ref_docno, ref_doctype, status, keterangan, source_description,
         currcode, kurs, createdby, createddate)
    SELECT
        v_journal_id, 'CLSPER', v_uniqueid, ROW_NUMBER() OVER (ORDER BY s.idcoa)::INT,
        ROW_NUMBER() OVER (ORDER BY s.idcoa)::INT,
        'CLOSING_PNL', 'NILAI', s.idcoa,
        CASE WHEN s.net_balance < 0 THEN ABS(s.net_balance) ELSE 0 END,
        CASE WHEN s.net_balance > 0 THEN s.net_balance ELSE 0 END,
        v_docno, 'CLOSING', 'DRAFT',
        'Penutupan saldo akun nominal ' || s.idcoa,
        'Generated from POSTED journals in closing period',
        'IDR', 1, p_user, CURRENT_TIMESTAMP
    FROM (
        SELECT BTRIM(jd.idcoa::TEXT) AS idcoa,
               ROUND(SUM(jd.debet - jd.kredit), 2) AS net_balance
        FROM sc_trx.jurnal_dt jd
        JOIN sc_trx.jurnal_hd jh ON jh.id = jd.jurnal_id
        JOIN sc_trx.period_closing_coa_rule r
          ON r.idcoa = BTRIM(jd.idcoa::TEXT)
         AND r.active = TRUE
         AND r.account_class = 'PNL'
        WHERE jh.trxdate BETWEEN v_period.tanggal_mulai AND v_period.tanggal_akhir
          AND jh.status = 'POSTED'
          AND jh.accounting_effect = 'YES'
        GROUP BY BTRIM(jd.idcoa::TEXT)
        HAVING ROUND(SUM(jd.debet - jd.kredit), 2) <> 0
    ) s;

    -- Counter line menyeimbangkan jurnal penutup.
    IF ROUND(v_total_debit, 2) <> ROUND(v_total_credit, 2) THEN
        INSERT INTO sc_trx.jurnal_dt
            (jurnal_id, journal_type, source_uniqueid, source_line_no, seq,
             account_role, value_source, idcoa, debet, kredit,
             ref_docno, ref_doctype, status, keterangan, source_description,
             currcode, kurs, createdby, createddate)
        VALUES
            (v_journal_id, 'CLSPER', v_uniqueid, NULL,
             (SELECT COALESCE(MAX(seq), 0) + 1 FROM sc_trx.jurnal_dt
              WHERE jurnal_id = v_journal_id),
             'CLOSING_EQUITY', 'NILAI', BTRIM(p_retained_earnings_coa),
             CASE WHEN v_total_credit > v_total_debit THEN v_total_credit - v_total_debit ELSE 0 END,
             CASE WHEN v_total_debit > v_total_credit THEN v_total_debit - v_total_credit ELSE 0 END,
             v_docno, 'CLOSING', 'DRAFT',
             'Lawan jurnal penutup / laba-rugi',
             'Counter line; review saldo laba/rugi sebelum posting',
             'IDR', 1, p_user, CURRENT_TIMESTAMP);
    END IF;

    -- Sync trigger jurnal_dt umumnya mengisi total header; hitung ulang eksplisit
    -- untuk menjamin validasi hasil, tanpa mengubah status DRAFT.
    UPDATE sc_trx.jurnal_hd h
    SET total_debet = x.debet,
        total_kredit = x.kredit,
        balance = ROUND(x.debet - x.kredit, 2),
        updatedby = p_user,
        updateddate = CURRENT_TIMESTAMP
    FROM (
        SELECT COALESCE(SUM(debet),0)::NUMERIC(18,2) AS debet,
               COALESCE(SUM(kredit),0)::NUMERIC(18,2) AS kredit
        FROM sc_trx.jurnal_dt
        WHERE jurnal_id = v_journal_id
    ) x
    WHERE h.id = v_journal_id;

    IF EXISTS (
        SELECT 1 FROM sc_trx.jurnal_hd
        WHERE id = v_journal_id
          AND ROUND(total_debet,2) <> ROUND(total_kredit,2)
    ) THEN
        RAISE EXCEPTION 'Jurnal penutup % tidak balance; transaksi dibatalkan.', v_journal_id;
    END IF;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason, detail)
    VALUES
        (p_closeperiod_id, 'PREPARE_JOURNAL', v_period.status_closing,
         v_period.status_closing, p_user,
         'Jurnal penutup dibuat dalam status DRAFT.',
         jsonb_build_object('jurnal_id', v_journal_id, 'docno', v_docno,
                            'retained_earnings_coa', p_retained_earnings_coa));

    RETURN v_journal_id;
END
$function$;

-- I. Ajukan ke review setelah semua checklist PASS dan issue_count = 0.
CREATE OR REPLACE FUNCTION sc_trx.fn_submit_period_closing_review(
    p_closeperiod_id INTEGER,
    p_user VARCHAR
)
RETURNS VOID
LANGUAGE plpgsql
AS $function$
DECLARE
    v_status TEXT;
    v_total BIGINT;
    v_not_pass BIGINT;
BEGIN
    IF COALESCE(BTRIM(p_user), '') = '' THEN
        RAISE EXCEPTION 'p_user wajib diisi.';
    END IF;

    SELECT status_closing INTO v_status
    FROM sc_trx.closeperiod
    WHERE idurut = p_closeperiod_id
    FOR UPDATE;

    IF NOT FOUND THEN
        RAISE EXCEPTION 'closeperiod id % tidak ditemukan.', p_closeperiod_id;
    END IF;
    IF v_status <> 'CHECKING' THEN
        RAISE EXCEPTION 'Periode harus CHECKING sebelum diajukan ke REVIEW.';
    END IF;

    SELECT COUNT(*),
           COUNT(*) FILTER (WHERE check_status <> 'PASS' OR issue_count <> 0)
      INTO v_total, v_not_pass
    FROM sc_trx.period_closing_check
    WHERE closeperiod_id = p_closeperiod_id;

    IF v_total = 0 OR v_not_pass > 0 THEN
        RAISE EXCEPTION 'Review ditolak: checklist belum lengkap, bukan PASS, atau masih ada issue.';
    END IF;

    UPDATE sc_trx.closeperiod
       SET status_closing = 'REVIEW',
           updateby = p_user,
           updatedate = CURRENT_TIMESTAMP
     WHERE idurut = p_closeperiod_id;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason)
    VALUES
        (p_closeperiod_id, 'SUBMIT_REVIEW', 'CHECKING', 'REVIEW', p_user,
         'Semua checklist PASS; menunggu persetujuan closing.');
END
$function$;

-- J. Tutup periode setelah checklist PASS dan Trial Balance balance.
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
        RAISE EXCEPTION 'Periode harus REVIEW sebelum ditutup.';
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
    SET status_closing = 'CLOSED',
        flagproses = 'CLOSED',
        closed_by = p_user,
        closed_at = CURRENT_TIMESTAMP,
        approved_by = p_approved_by,
        approved_at = CURRENT_TIMESTAMP,
        closing_balance = ROUND(v_debet - v_kredit, 2),
        updateby = p_user,
        updatedate = CURRENT_TIMESTAMP
    WHERE idurut = p_closeperiod_id;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason)
    VALUES
        (p_closeperiod_id, 'CLOSE', v_status, 'CLOSED', p_user,
         'Periode ditutup setelah checklist PASS dan Trial Balance balance.');
END
$function$;

-- J. Reopen periode dengan alasan wajib.
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
        RAISE EXCEPTION 'Hanya periode CLOSED yang dapat dibuka kembali.';
    END IF;

    UPDATE sc_trx.closeperiod
    SET status_closing = 'REOPENED',
        flagproses = 'OPEN',
        reopened_by = p_user,
        reopened_at = CURRENT_TIMESTAMP,
        reopen_reason = BTRIM(p_reason),
        updateby = p_user,
        updatedate = CURRENT_TIMESTAMP
    WHERE idurut = p_closeperiod_id;

    INSERT INTO sc_trx.period_closing_log
        (closeperiod_id, action, old_status, new_status, action_by, reason)
    VALUES
        (p_closeperiod_id, 'REOPEN', 'CLOSED', 'REOPENED', p_user, BTRIM(p_reason));
END
$function$;

COMMIT;

-- ============================================================================
-- PANDUAN TAHAPAN PENGGUNAAN
-- ============================================================================
-- TAHAP 1: Jalankan seluruh file di DATABASE TEST terlebih dahulu.
--
-- TAHAP 2: Daftarkan periode (contoh Mei 2026):
-- INSERT INTO sc_trx.closeperiod
--   (periode, tanggal_mulai, tanggal_akhir, status_closing, inputby, inputdate)
-- VALUES ('2026-05', DATE '2026-05-01', DATE '2026-05-31', 'OPEN',
--         'admin', CURRENT_TIMESTAMP) RETURNING idurut;
--
-- TAHAP 3: Diagnostik read-only:
-- SELECT * FROM sc_trx.fn_period_closing_diagnostics(<ID_CLOSEPERIOD>);
-- SELECT idcoa, SUM(total_debet) debit, SUM(total_kredit) kredit,
--        SUM(saldo_net_debet_kredit) net
-- FROM sc_trx.v_period_closing_coa_movement
-- WHERE trxdate BETWEEN DATE '2026-05-01' AND DATE '2026-05-31'
-- GROUP BY idcoa ORDER BY idcoa;
--
-- TAHAP 4: Rekonsiliasi:
-- 1) Jurnal Transaksi vs Buku Besar Detail berdasarkan nomor jurnal dan COA.
-- 2) Total mutasi dan saldo akhir per COA vs Neraca Saldo.
-- 3) Kelompok pendapatan/beban vs Laba Rugi (Bulan Berjalan/Tahun Berjalan).
-- 4) Nilai akun persediaan vs Analisa Mutasi Stock.
-- Jumlah baris laporan berbeda tidak otomatis berarti salah; cocokkan key dan nilai.
--
-- TAHAP 5: Isi checklist hanya setelah benar-benar diperiksa:
-- SELECT sc_trx.fn_set_period_closing_check(
--   <ID_CLOSEPERIOD>, 'TB_BALANCE', 'Rekonsiliasi Neraca Saldo',
--   'PASS', 0, '{"selisih":0}'::jsonb, 'admin');
-- Gunakan kode terpisah: JOURNAL_BALANCE, TB_BALANCE, PNL_REVIEW, STOCK_REVIEW.
-- Jangan mengisi PASS sebelum angka diverifikasi.
--
-- TAHAP 6: Closing BULANAN:
-- SELECT sc_trx.fn_start_period_closing(<ID_CLOSEPERIOD>, 'admin');
-- Isi closing_debet/kredit dari hasil Neraca Saldo aktual:
-- UPDATE sc_trx.closeperiod SET closing_debet=<DEBET>, closing_kredit=<KREDIT>
-- WHERE idurut=<ID_CLOSEPERIOD>;
-- SELECT sc_trx.fn_submit_period_closing_review(<ID_CLOSEPERIOD>, 'admin');
-- SELECT sc_trx.fn_close_accounting_period(<ID_CLOSEPERIOD>, 'admin', 'approver');
-- Closing bulanan hanya validasi, persetujuan, dan lock; tidak menolkan P&L.
--
-- TAHAP 7: Closing TAHUNAN (proses terpisah):
-- SELECT sc_trx.fn_prepare_year_end_close(
--   <ID_CLOSEPERIOD>, 'admin', '<COA_TUJUAN_LABA_RUGI>');
-- Fungsi membuat jurnal DRAFT. Review detail dan posting melalui workflow existing.
-- Jangan jalankan tiap bulan kecuali kebijakan akuntansi mengharuskan.
--
-- TAHAP 8: Lock transaksi:
-- fn_assert_period_open() harus dipanggil dari trigger/fungsi INSERT/UPDATE/DELETE,
-- posting, reverse, dan proses inventory. Tanpa integrasi, CLOSED belum mengunci
-- seluruh jalur transaksi.
--
-- BATASAN:
-- 1) Rekonsiliasi stok belum otomatis karena skema tabel stok aktual belum diberikan.
-- 2) Klasifikasi awal COA 1-3=BS dan 4-7=PNL wajib ditinjau; kode akun bukan jaminan
--    sempurna untuk akun khusus.
-- 3) SQL ini belum dieksekusi pada database Anda. Uji pada salinan database dahulu.
--
-- ============================================================================
-- PANDUAN PEMAKAIAN (JALANKAN TERPISAH SETELAH PATCH SUKSES)
-- ============================================================================
-- 1) Siapkan periode. Ganti tanggal sesuai fiscal year:
-- INSERT INTO sc_trx.closeperiod
--   (periode, tanggal_mulai, tanggal_akhir, status_closing, inputby, inputdate)
-- VALUES ('FY2026', DATE '2026-01-01', DATE '2026-12-31', 'OPEN',
--         'admin', CURRENT_TIMESTAMP)
-- RETURNING idurut;
--
-- 2) Lihat akun yang benar-benar memiliki jurnal POSTED pada periode:
-- SELECT jd.idcoa, COUNT(*) jumlah_detail,
--        SUM(jd.debet) total_debet, SUM(jd.kredit) total_kredit,
--        SUM(jd.debet-jd.kredit) saldo_net
-- FROM sc_trx.jurnal_dt jd
-- JOIN sc_trx.jurnal_hd jh ON jh.id=jd.jurnal_id
-- WHERE jh.trxdate BETWEEN DATE '2026-01-01' AND DATE '2026-12-31'
--   AND jh.status='POSTED' AND jh.accounting_effect='YES'
-- GROUP BY jd.idcoa ORDER BY jd.idcoa;
--
-- 3) Klasifikasi awal sudah diisi otomatis dari master COA.
-- Periksa hasil klasifikasi dan koreksi akun khusus secara manual:
-- SELECT r.idcoa, c.nmcoa, r.account_class, r.active
-- FROM sc_trx.period_closing_coa_rule r
-- LEFT JOIN sc_mst.coa c ON c.idcoa::TEXT = r.idcoa
-- ORDER BY r.idcoa;
--
-- Contoh koreksi manual jika diperlukan:
-- INSERT INTO sc_trx.period_closing_coa_rule
--     (idcoa, account_class, active, description, created_by)
-- VALUES
--     ('<COA_PENDAPATAN>', 'PNL', TRUE, 'Pendapatan', 'admin'),
--     ('<COA_BEBAN>',      'PNL', TRUE, 'Beban', 'admin'),
--     ('<COA_KAS>',        'BS',  TRUE, 'Kas', 'admin')
-- ON CONFLICT (idcoa) DO UPDATE
-- SET account_class=EXCLUDED.account_class, active=TRUE,
--     description=EXCLUDED.description, updated_by='admin',
--     updated_at=CURRENT_TIMESTAMP;
--
-- 4) Mulai checking dan buat jurnal penutup DRAFT:
-- SELECT sc_trx.fn_start_period_closing(<ID_CLOSEPERIOD>, 'admin');
-- SELECT sc_trx.fn_prepare_year_end_close(
--     <ID_CLOSEPERIOD>, 'admin', '<COA_LABA_RUGI_TAHUN_BERJALAN>');
--
-- Fungsi akan menolak proses jika ada COA transaksi POSTED yang belum diklasifikasikan.
-- Jurnal yang dibuat masih DRAFT. Periksa detailnya sebelum posting melalui proses
-- posting existing J-Accounting. Jangan posting sebelum memastikan akun lawan benar.
--
-- 5) Periksa jurnal penutup:
-- SELECT h.id, h.docno, h.trxdate, h.status, h.total_debet, h.total_kredit, h.balance
-- FROM sc_trx.jurnal_hd h
-- WHERE h.journal_type='CLSPER'
-- ORDER BY h.id DESC;
--
-- SELECT jd.*
-- FROM sc_trx.jurnal_dt jd
-- JOIN sc_trx.jurnal_hd h ON h.id=jd.jurnal_id
-- WHERE h.journal_type='CLSPER'
-- ORDER BY h.id DESC, jd.seq;
--
-- 6) Setelah menjalankan audit query, catat setiap hasil:
-- SELECT sc_trx.fn_set_period_closing_check(
--   <ID_CLOSEPERIOD>, 'TB_BALANCE', 'Trial Balance seimbang',
--   'PASS', 0, '{"debet":1000,"kredit":1000}'::jsonb, 'admin');
-- Ulangi untuk checklist lain. Nilai contoh bukan hasil audit otomatis.
--
-- 7) Isi closing_debet/kredit dari Trial Balance aktual, lalu ajukan review:
-- UPDATE sc_trx.closeperiod
-- SET closing_debet=<TOTAL_DEBET>, closing_kredit=<TOTAL_KREDIT>
-- WHERE idurut=<ID_CLOSEPERIOD>;
-- SELECT sc_trx.fn_submit_period_closing_review(<ID_CLOSEPERIOD>, 'admin');
-- SELECT sc_trx.fn_close_accounting_period(<ID_CLOSEPERIOD>, 'admin', 'approver');
--
-- 8) Penguncian transaksi:
-- fn_assert_period_open() tersedia, tetapi harus dipanggil oleh trigger/fungsi
-- INSERT/UPDATE/DELETE, posting dan reverse setelah kolom tanggal tiap tabel dipetakan.
-- Tanpa integrasi itu, status CLOSED belum memblokir semua jalur transaksi.
--
-- CATATAN KOMPATIBILITAS:
-- Tabel sc_trx.jurnal_hd dan sc_trx.jurnal_dt diasumsikan sudah ada sesuai skema
-- J-Accounting (tahap 09/10). Tabel sc_mst.coa harus memiliki kolom idcoa.
-- Tabel tutupbulan, tglawal, prosessahp, sahp dan konfigurasi_umum tidak diubah.
