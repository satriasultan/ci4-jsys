/* =====================================================================
   J-ACCOUNTING - SIMULASI CLOSING MEI 2026
   Data sumber: Lap_Jurnal_Transaksi_20261025_210514.XLS (49.296 baris)

   TUJUAN:
   1) Import laporan Excel ke staging sc_im.
   2) Validasi COA dan jurnal type.
   3) Insert ke sc_trx.transaction_dt dengan journal_type JVGENL.
   4) Jalankan validasi, proses posting existing, lalu simulasi closing.

   PERINGATAN:
   - JALANKAN DI DATABASE TEST/SIMULASI, BUKAN PRODUKSI.
   - Data Excel adalah output jurnal, bukan transaksi operasional mentah.
   - Selisih pembulatan kecil akan dibuat sebagai baris koreksi eksplisit pada COA 713 (PEMBULATAN), hanya jika selisih setelah normalisasi <= Rp0,03 per jurnal.
   - Script tidak membuat jurnal POSTED secara paksa. Jalankan pipeline posting
     J-Accounting yang ada dan verifikasi hasilnya sebelum closeperiod ditutup.
   - Import ulang tidak menggandakan transaction_dt karena uniqueid deterministik.
   - Script memakai COPY SQL agar tidak error pada SQL Editor; COPY membaca file dari server PostgreSQL.
      Jika file hanya ada di PC client, gunakan fitur Import CSV aplikasi ke tabel staging.
   ===================================================================== */

-- STEP 0: pastikan database TEST yang benar
SELECT current_database() AS database_aktif, current_user AS user_aktif;

-- STEP 1: staging import
CREATE SCHEMA IF NOT EXISTS sc_im;

CREATE TABLE IF NOT EXISTS sc_im.closing_may2026_stage (
    source_row BIGINT PRIMARY KEY,
    docno VARCHAR(30) NOT NULL,
    docdate DATE NOT NULL,
    line_no INTEGER NOT NULL,
    idcoa VARCHAR(20) NOT NULL,
    keterangan TEXT,
    debet NUMERIC(18,2) NOT NULL DEFAULT 0,
    kredit NUMERIC(18,2) NOT NULL DEFAULT 0,
    job_name VARCHAR(250)
);

-- Bersihkan hanya tabel staging simulasi ini agar import bisa diulang.
TRUNCATE TABLE sc_im.closing_may2026_stage;

-- SQL Editor-compatible: COPY (bukan \copy).
-- Catatan: COPY FROM membaca file dari MESIN SERVER PostgreSQL, bukan selalu PC client.
-- Pastikan file CSV berada pada path yang bisa dibaca server dan user PostgreSQL punya izin.
COPY sc_im.closing_may2026_stage
(source_row, docno, docdate, line_no, idcoa, keterangan, debet, kredit, job_name)
FROM 'D:/GITHUB/ci4-jsys/DatabaseChanges/TOOLS/99.PENDUKUNG CLOSING/Simulasi_Closing_Mei2026_transaction_dt.csv'
WITH (FORMAT CSV, HEADER TRUE, ENCODING 'UTF8');
-- STEP 2: validasi staging sebelum insert
SELECT COUNT(*) AS jumlah_baris,
       MIN(docdate) AS tanggal_awal,
       MAX(docdate) AS tanggal_akhir,
       SUM(debet) AS total_debet,
       SUM(kredit) AS total_kredit,
       ROUND(SUM(debet)-SUM(kredit),2) AS selisih
FROM sc_im.closing_may2026_stage;

-- Cek COA yang belum ada pada master. Hasil wajib 0 sebelum insert.
SELECT s.idcoa, COUNT(*) AS jumlah_baris
FROM sc_im.closing_may2026_stage s
LEFT JOIN sc_mst.coa c ON BTRIM(c.idcoa::text) = BTRIM(s.idcoa)
WHERE c.idcoa IS NULL
GROUP BY s.idcoa
ORDER BY s.idcoa;

-- Cek nomor jurnal yang tidak balance; investigasi sebelum final close.
SELECT docno, COUNT(*) AS jumlah_baris,
       SUM(debet) AS total_debet,
       SUM(kredit) AS total_kredit,
       ROUND(SUM(debet)-SUM(kredit),2) AS selisih
FROM sc_im.closing_may2026_stage
GROUP BY docno
HAVING ROUND(SUM(debet)-SUM(kredit),2) <> 0
ORDER BY docno;

-- Cek panjang docno dan rentang tanggal.
SELECT COUNT(*) FILTER (WHERE LENGTH(docno) > 20) AS docno_lebih_20,
       COUNT(*) FILTER (WHERE docdate < DATE '2026-05-01' OR docdate > DATE '2026-05-31') AS tanggal_di_luar_periode,
       COUNT(*) FILTER (WHERE debet <> 0 AND kredit <> 0) AS debit_dan_kredit_sama_sama_terisi
FROM sc_im.closing_may2026_stage;

-- STEP 3: siapkan closeperiod Mei 2026 (hanya buat jika belum ada).
-- Periksa hasil SELECT sebelum meneruskan. Jangan memakai ID periode dari tebakan.
INSERT INTO sc_trx.closeperiod
    (periode, tanggal_mulai, tanggal_akhir, status_closing, inputby, inputdate, keterangan)
SELECT '2026-05-SIM', DATE '2026-05-01', DATE '2026-05-31', 'OPEN',
       CURRENT_USER, CURRENT_TIMESTAMP, 'SIMULASI closing berdasarkan laporan legacy Mei 2026'
WHERE NOT EXISTS (
    SELECT 1 FROM sc_trx.closeperiod
    WHERE periode = '2026-05-SIM'
);

SELECT idurut, periode, tanggal_mulai, tanggal_akhir, status_closing
FROM sc_trx.closeperiod
WHERE periode = '2026-05-SIM';

-- STEP 4: insert ke transaction_dt.
-- Selisih setelah normalisasi maksimal Rp0,03 per jurnal akan diseimbangkan dengan
-- baris koreksi eksplisit pada COA 713 (PEMBULATAN). Pastikan COA 713 memang akun
-- pembulatan resmi pada database TEST sebelum menjalankan skrip ini.
DO $validate$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM sc_mst.journal_type WHERE BTRIM(journal_type) = 'JVGENL') THEN
        RAISE EXCEPTION 'Journal type JVGENL tidak ditemukan di sc_mst.journal_type.';
    END IF;
    IF EXISTS (
        SELECT 1 FROM sc_im.closing_may2026_stage s
        LEFT JOIN sc_mst.coa c ON BTRIM(c.idcoa::text) = BTRIM(s.idcoa)
        WHERE c.idcoa IS NULL
    ) THEN
        RAISE EXCEPTION 'Ada COA dari Excel yang belum ada di sc_mst.coa. Insert dibatalkan.';
    END IF;
    IF EXISTS (SELECT 1 FROM sc_im.closing_may2026_stage WHERE LENGTH(docno) > 20) THEN
        RAISE EXCEPTION 'Ada docno lebih dari 20 karakter. Insert dibatalkan.';
    END IF;
    IF EXISTS (SELECT 1 FROM sc_im.closing_may2026_stage WHERE docdate < DATE '2026-05-01' OR docdate > DATE '2026-05-31') THEN
        RAISE EXCEPTION 'Ada tanggal di luar Mei 2026. Insert dibatalkan.';
    END IF;
    IF EXISTS (
        SELECT 1
        FROM sc_im.closing_may2026_stage
        WHERE debet <> 0 AND kredit <> 0
    ) THEN
        RAISE EXCEPTION 'Ada baris dengan debit dan kredit sama-sama terisi. Insert dibatalkan.';
    END IF;
    -- COA 713 harus ada dan merupakan akun PEMBULATAN yang disetujui.
    IF NOT EXISTS (
        SELECT 1 FROM sc_mst.coa c
        WHERE BTRIM(c.idcoa::text) = '713'
    ) THEN
        RAISE EXCEPTION 'COA 713 tidak ditemukan di sc_mst.coa. Insert dibatalkan.';
    END IF;
    -- Selisih di atas Rp0,03 per dokumen bukan dianggap pembulatan otomatis.
    IF EXISTS (
        SELECT 1
        FROM sc_im.closing_may2026_stage
        GROUP BY docno
        HAVING ABS(ROUND(
            SUM(GREATEST(debet, 0) + GREATEST(-kredit, 0)) -
            SUM(GREATEST(kredit, 0) + GREATEST(-debet, 0)), 2
        )) > 0.03
    ) THEN
        RAISE EXCEPTION 'Ada selisih jurnal lebih dari Rp0,03 setelah normalisasi. Insert dibatalkan; periksa data sumber.';
    END IF;
END
$validate$;

BEGIN;
INSERT INTO sc_trx.transaction_dt
    (uniqueid, source_uniqueid, docno, doctype, journal_type, line_no, docdate,
     idbranch, cabang, type_in_out, ref_docno, ref_doctype, source_table,
     source_id, source_line_id, qty, harga, bruto, discount, nilai, dpp, pajak,
     total, idtax, isinclusive, currcode, kurs, idcoa, counter_idcoa,
     debet_kredit, debet, kredit, module, direction, stock_effect,
     accounting_effect, asset_effect, keterangan, createdby, createddate)
SELECT
    MD5('SIM-CLOSING-MAY2026|' || s.source_row::text) AS uniqueid,
    MD5('LEGACY-XLS-MAY2026|' || s.source_row::text) AS source_uniqueid,
    s.docno,
    'GENERAL_JOURNAL' AS doctype,
    'JVGENL' AS journal_type,
    s.line_no,
    s.docdate,
    '' AS idbranch,
    COALESCE(NULLIF(s.job_name,''),'SIMULASI') AS cabang,
    'IN' AS type_in_out, -- compatibility value JVGENL; bukan arah stock
    '' AS ref_docno,
    '' AS ref_doctype,
    'sc_im.closing_may2026_stage' AS source_table,
    s.source_row AS source_id,
    s.line_no AS source_line_id,
    0, 0,
    (GREATEST(s.debet, 0) + GREATEST(-s.kredit, 0) +
     GREATEST(s.kredit, 0) + GREATEST(-s.debet, 0)),
    0,
    (GREATEST(s.debet, 0) + GREATEST(-s.kredit, 0) +
     GREATEST(s.kredit, 0) + GREATEST(-s.debet, 0)),
    0, 0,
    (GREATEST(s.debet, 0) + GREATEST(-s.kredit, 0) +
     GREATEST(s.kredit, 0) + GREATEST(-s.debet, 0)),
    'NON', 'NO', 'IDR', 1,
    s.idcoa,
    '',
    CASE
        WHEN s.debet < 0 OR s.kredit > 0 THEN 'K'
        ELSE 'D'
    END,
    GREATEST(s.debet, 0) + GREATEST(-s.kredit, 0),
    GREATEST(s.kredit, 0) + GREATEST(-s.debet, 0),
    'ACCOUNTING', 'INOUT', 'NONE', 'YES', 'NONE',
    s.keterangan,
    CURRENT_USER,
    CURRENT_TIMESTAMP
FROM sc_im.closing_may2026_stage s
ON CONFLICT (uniqueid) DO NOTHING;

-- Tambahkan koreksi pembulatan eksplisit per docno berdasarkan selisih setelah normalisasi.
-- Jika debit > kredit, koreksi masuk kredit; jika kredit > debit, koreksi masuk debit.
WITH normalized AS (
    SELECT
        s.docno,
        MIN(s.source_row) AS source_row,
        MIN(s.docdate) AS docdate,
        MAX(COALESCE(NULLIF(s.job_name,''),'SIMULASI')) AS cabang,
        MAX(s.line_no) AS max_line_no,
        SUM(GREATEST(s.debet, 0) + GREATEST(-s.kredit, 0)) AS total_debet,
        SUM(GREATEST(s.kredit, 0) + GREATEST(-s.debet, 0)) AS total_kredit
    FROM sc_im.closing_may2026_stage s
    GROUP BY s.docno
), adjustments AS (
    SELECT *, ROUND(total_debet - total_kredit, 2) AS selisih
    FROM normalized
    WHERE ROUND(total_debet - total_kredit, 2) <> 0
)
INSERT INTO sc_trx.transaction_dt
    (uniqueid, source_uniqueid, docno, doctype, journal_type, line_no, docdate,
     idbranch, cabang, type_in_out, ref_docno, ref_doctype, source_table,
     source_id, source_line_id, qty, harga, bruto, discount, nilai, dpp, pajak,
     total, idtax, isinclusive, currcode, kurs, idcoa, counter_idcoa,
     debet_kredit, debet, kredit, module, direction, stock_effect,
     accounting_effect, asset_effect, keterangan, createdby, createddate)
SELECT
    MD5('SIM-CLOSING-MAY2026-ROUNDING|' || a.docno || '|' || a.source_row::text) AS uniqueid,
    MD5('LEGACY-XLS-MAY2026-ROUNDING|' || a.docno || '|' || a.source_row::text) AS source_uniqueid,
    a.docno,
    'GENERAL_JOURNAL',
    'JVGENL',
    a.max_line_no + 1,
    a.docdate,
    '',
    a.cabang,
    'IN',
    '',
    '',
    'sc_im.closing_may2026_stage',
    -ROW_NUMBER() OVER (ORDER BY a.docno),
    a.max_line_no + 1,
    0, 0, ABS(a.selisih), 0, ABS(a.selisih), 0, 0, ABS(a.selisih),
    'NON', 'NO', 'IDR', 1,
    '713', '',
    CASE WHEN a.selisih > 0 THEN 'K' ELSE 'D' END,
    CASE WHEN a.selisih < 0 THEN ABS(a.selisih) ELSE 0 END,
    CASE WHEN a.selisih > 0 THEN ABS(a.selisih) ELSE 0 END,
    'ACCOUNTING', 'INOUT', 'NONE', 'YES', 'NONE',
    'PENYESUAIAN PEMBULATAN SIMULASI CLOSING MEI 2026',
    CURRENT_USER,
    CURRENT_TIMESTAMP
FROM adjustments a
ON CONFLICT (uniqueid) DO NOTHING;
COMMIT;

-- STEP 5: verifikasi hasil insert, termasuk baris koreksi pembulatan yang tercatat eksplisit.
SELECT COUNT(*) AS jumlah_transaction_dt,
       MIN(docdate) AS tanggal_awal,
       MAX(docdate) AS tanggal_akhir,
       SUM(debet) AS total_debet,
       SUM(kredit) AS total_kredit,
       ROUND(SUM(debet)-SUM(kredit),2) AS selisih
FROM sc_trx.transaction_dt
WHERE source_table = 'sc_im.closing_may2026_stage'
  AND docdate BETWEEN DATE '2026-05-01' AND DATE '2026-05-31';

-- Periksa data per dokumen di transaction_dt.
SELECT docno, COUNT(*) AS jumlah_baris,
       SUM(debet) AS total_debet,
       SUM(kredit) AS total_kredit,
       ROUND(SUM(debet)-SUM(kredit),2) AS selisih
FROM sc_trx.transaction_dt
WHERE source_table = 'sc_im.closing_may2026_stage'
  AND docdate BETWEEN DATE '2026-05-01' AND DATE '2026-05-31'
GROUP BY docno
HAVING ROUND(SUM(debet)-SUM(kredit),2) <> 0
ORDER BY docno;

/* =====================================================================
   STEP 6: POSTING TRANSACTION_DT
   =====================================================================
   Jangan mengarang INSERT langsung ke transaction_hd/jurnal_hd/jurnal_dt.
   Jalankan fungsi/endpoint posting existing J-Accounting yang biasa memproses
   transaction_dt -> transaction_hd -> jurnal_hd/jurnal_dt. Karena ini data
   laporan legacy, pastikan terlebih dahulu bahwa posting pipeline menerima
   JVGENL multi-line dari satu docno dan tidak menggandakan jurnal.

   Setelah posting, validasi:
   SELECT h.docno, h.status, h.total_debet, h.total_kredit, h.balance
   FROM sc_trx.jurnal_hd h
   WHERE h.trxdate BETWEEN DATE '2026-05-01' AND DATE '2026-05-31'
     AND h.docno IN (SELECT DISTINCT docno FROM sc_im.closing_may2026_stage)
   ORDER BY h.docno;

   STEP 7: klasifikasikan seluruh COA yang ada pada jurnal POSTED ke
   sc_trx.period_closing_coa_rule. Jangan otomatis menebak PNL/BS tanpa verifikasi.

   STEP 8: mulai closing (ganti <ID_CLOSEPERIOD> dengan idurut hasil query di atas):
   SELECT sc_trx.fn_start_period_closing(<ID_CLOSEPERIOD>, CURRENT_USER);

   STEP 9: jalankan audit nyata lalu catat checklist. Contoh checklist; angka 0
   hanya boleh dipakai jika query audit memang membuktikan tidak ada masalah:
   SELECT sc_trx.fn_set_period_closing_check(<ID_CLOSEPERIOD>, 'TB_BALANCE',
     'Trial Balance debit dan kredit seimbang', 'PASS', 0,
     '{"source":"hasil query trial balance aktual"}'::jsonb, CURRENT_USER);
   SELECT sc_trx.fn_set_period_closing_check(<ID_CLOSEPERIOD>, 'JOURNAL_BALANCE',
     'Seluruh jurnal seimbang', 'PASS', 0,
     '{"source":"hasil audit jurnal POSTED"}'::jsonb, CURRENT_USER);
   Ulangi checklist untuk stock, subledger, COA, transaksi pending, dan cut-off.

   STEP 10: setelah semua checklist PASS, isi closing_debet/kredit dari Trial
   Balance aktual, ajukan review, lalu close dengan dua user berbeda:
   UPDATE sc_trx.closeperiod
   SET closing_debet = <TOTAL_DEBET_TB>, closing_kredit = <TOTAL_KREDIT_TB>
   WHERE idurut = <ID_CLOSEPERIOD>;
   SELECT sc_trx.fn_submit_period_closing_review(<ID_CLOSEPERIOD>, CURRENT_USER);
   SELECT sc_trx.fn_close_accounting_period(<ID_CLOSEPERIOD>, '<USER_CLOSING>', '<USER_APPROVER>');

   STEP 11: uji status lock:
   SELECT idurut, periode, status_closing FROM sc_trx.closeperiod
   WHERE idurut = <ID_CLOSEPERIOD>;
   SELECT sc_trx.fn_assert_period_open(DATE '2026-05-15');
   -- Fungsi ini seharusnya menolak setelah CLOSED. Namun lock belum efektif
   -- untuk semua transaksi sampai dipanggil oleh jalur INSERT/UPDATE/DELETE,
   -- posting, reverse, dan transaksi inventory yang relevan.

   STEP 12 (opsional TEST saja): buka kembali dengan alasan yang jelas:
   SELECT sc_trx.fn_reopen_accounting_period(<ID_CLOSEPERIOD>, '<USER_APPROVER>',
     'Simulasi pengujian reopen setelah validasi closing');
   ===================================================================== */
