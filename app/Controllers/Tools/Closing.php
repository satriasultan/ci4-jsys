<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;

/**
 * Proses Tutup Bulan (Accounting Closing)
 * ---------------------------------------------------------------------------
 * Menjalankan proses closing pada sc_trx.closeperiod dengan memanfaatkan
 * fungsi-fungsi database yang ada pada
 *   DatabaseChanges/TOOLS/99.PROSES CLOSING PENTING .sql
 *   DatabaseChanges/TOOLS/99b.FIX PROSES CLOSING TUTUPBULAN.sql
 *
 * Tahapan (dipersingkat): Registrasi Periode -> Diagnostik -> Mulai Closing
 *          -> Tutup Periode (+ lock flagproses='TUTUP') -> Buka Kembali /
 *          Jurnal Penutup Tahunan.
 * Approver diisi otomatis dari session login ($nama).
 */
class Closing extends BaseController
{
    private $kodemenu     = 'I.T.C.1';
    private $versirelease = 'I.T.C.1/01';

    /* =====================================================================
     * HALAMAN PROSES TUTUP BULAN
     * ===================================================================== */
    public function index()
    {
        $data['title'] = "Proses Tutup Bulan";
        $dtlbranch     = $this->m_global->q_branch()->getRowArray();
        $branch        = isset($dtlbranch['branch']) ? $dtlbranch['branch'] : '';

        /* CODE UNTUK VERSI */
        $nama         = trim($this->session->get('nama'));
        $kodemenu     = $this->kodemenu;
        $versirelease = $this->versirelease;
        $releasedate  = date('2025-04-12 00:00:00');
        $versidb      = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x            = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x']          = $x['rows'];
        $data['y']          = $x['res'];
        $data['t']          = $x['xn'];
        $data['kodemenu']   = $kodemenu;
        $data['version']    = $versidb;
        /* END CODE UNTUK VERSI */

        $paramerror = " and userid='$nama' and modul='I.T.C.1'";
        $dtlerror   = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err  = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        $errordesc   = isset($dtlerror['description']) ? trim($dtlerror['description']) : '';
        $nomorakhir1 = isset($dtlerror['nomorakhir1']) ? trim($dtlerror['nomorakhir1']) : '';
        $errorcode   = isset($dtlerror['errorcode']) ? trim($dtlerror['errorcode']) : '';

        if ($count_err > 0 and $errordesc <> '') {
            if ($dtlerror['errorcode'] == 0) {
                $data['message'] = "<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message'] = "<div class='alert alert-info'>$errordesc</div>";
            }
        } else {
            if ($errorcode == '0') {
                $data['message'] = "<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message'] = "";
            }
        }

        /* HAK AKSES */
        $role             = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kodemenu)->getRowArray();

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        /* PERIODE CLOSING SELALU MENGIKUTI TANGGAL LOGIN
           (session logindate dari login.php, format d-m-Y -> periode yymm) */
        $login                = $this->closingLoginPeriod();
        $periodeAktif         = $login['periode'];

        $data['periode']      = $periodeAktif;
        $data['periodelogin'] = $login['periode'];
        $data['logindate']    = $login['logindate'];
        $data['tglmulai']     = $login['tanggal_mulai'];
        $data['tglakhir']     = $login['tanggal_akhir'];
        $data['namalogin']    = $nama;
        $data['branch']       = $branch;

        /* PERIODE YANG SEDANG DIPILIH */
        $current          = $this->closingPeriodRow($periodeAktif);
        $data['current']  = $current;
        $idurut           = isset($current['idurut']) ? (int) $current['idurut'] : 0;
        $data['idurut']   = $idurut;

        /* LOG & DAFTAR PERIODE */
        $data['log']       = $idurut > 0 ? $this->closingLog($idurut, 100) : [];
        $data['list']      = $this->closingPeriodList(200);

        /* JURNAL PENUTUP TAHUNAN (sumber sc_trx.jurnal_hd + sc_trx.jurnal_dt) */
        $data['closingjournal'] = $this->closingYearEndJournals(100);

        return $this->template->render('tools/proses/v_list_ptb', $data);
    }
    /* =====================================================================
     * HELPER: PERIODE DARI TANGGAL LOGIN (yymm) + RENTANG BULAN
     * ===================================================================== */
    private function closingLoginPeriod()
    {
        $logindate = trim($this->session->get('logindate'));

        $dt = \DateTime::createFromFormat('d-m-Y', $logindate);
        if (!$dt) {
            $dt = new \DateTime();
        }

        return [
            'logindate'     => $dt->format('d-m-Y'),
            'periode'       => $dt->format('ym'),
            'tanggal_mulai' => $dt->format('Y-m-01'),
            'tanggal_akhir' => $dt->format('Y-m-t'),
        ];
    }

    private function closingNama()
    {
        return trim($this->session->get('nama'));
    }

    private function closingJson($success, $message, $extra = [])
    {
        return $this->response->setJSON(array_merge([
            'success' => $success,
            'message' => $message,
        ], $extra));
    }

    /* =====================================================================
     * HELPER: DATA PERIODE
     * ===================================================================== */
    private function closingPeriodRow($periode)
    {
        if (trim($periode) === '') {
            return null;
        }

        $row = $this->db->table('sc_trx.closeperiod')
            ->where('TRIM(periode)', trim($periode))
            ->orderBy('idurut', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    private function closingPeriodById($idurut)
    {
        if (empty($idurut)) {
            return null;
        }

        $row = $this->closingRow(
            "SELECT idurut, BTRIM(periode::TEXT) AS periode, tanggal_mulai, tanggal_akhir,
                    COALESCE(NULLIF(BTRIM(status_closing), ''), 'OPEN') AS status_closing,
                    COALESCE(NULLIF(BTRIM(flagproses), ''), 'OPEN') AS flagproses,
                    keterangan, closing_debet, closing_kredit, closing_balance,
                    closed_by, closed_at, approved_by, approved_at,
                    reopened_by, reopened_at, reopen_reason,
                    inputby, inputdate, updateby, updatedate
               FROM sc_trx.v_period_closing_status
              WHERE idurut = ?",
            [(int) $idurut]
        );

        return $row ?: null;
    }

    private function closingLog($idurut, $limit = 100)
    {
        if (empty($idurut)) {
            return [];
        }

        return $this->closingRows(
            "SELECT id, action, old_status, new_status, action_by, action_at, reason, detail
               FROM sc_trx.period_closing_log
              WHERE closeperiod_id = ?
              ORDER BY id DESC
              LIMIT ?",
            [(int) $idurut, (int) $limit]
        );
    }

    private function closingPeriodList($limit = 200)
    {
        return $this->closingRows(
            "SELECT idurut, periode, tanggal_mulai, tanggal_akhir, status_closing, flagproses,
                    keterangan, closing_debet, closing_kredit, closing_balance,
                    closed_by, closed_at, approved_by, approved_at,
                    reopened_by, reopened_at, reopen_reason,
                    inputby, inputdate, updateby, updatedate,
                    jumlah_check, jumlah_pass
               FROM sc_trx.v_period_closing_status
              ORDER BY idurut DESC
              LIMIT ?",
            [(int) $limit]
        );
    }

    /* Daftar jurnal penutup / jurnal yang berkaitan dengan closing.
       Sumber: sc_trx.jurnal_hd (header) + sc_trx.jurnal_dt (baris detail). */
    private function closingYearEndJournals($limit = 100)
    {
        return $this->closingRows(
            "SELECT h.id, h.docno, h.doctype, h.journal_type, h.trxdate, h.status,
                    h.total_debet, h.total_kredit, h.balance, h.keterangan,
                    h.createdby, h.createddate,
                    (SELECT COUNT(*) FROM sc_trx.jurnal_dt d WHERE d.jurnal_id = h.id) AS jumlah_baris
               FROM sc_trx.jurnal_hd h
              WHERE UPPER(COALESCE(BTRIM(h.journal_type::TEXT), '')) IN ('CLSPER', 'CLOSING')
                 OR UPPER(COALESCE(BTRIM(h.doctype::TEXT), '')) = 'CLOSING'
                 OR UPPER(COALESCE(BTRIM(h.ref_doctype::TEXT), '')) = 'CLOSEPERIOD'
                 OR UPPER(COALESCE(h.keterangan::TEXT, '')) LIKE '%CLOSING%'
                 OR UPPER(COALESCE(h.keterangan::TEXT, '')) LIKE '%PENUTUP%'
                 OR UPPER(COALESCE(h.keterangan::TEXT, '')) LIKE '%TUTUP BULAN%'
              ORDER BY h.id DESC
              LIMIT ?",
            [(int) $limit]
        );
    }

    /* =====================================================================
     * HELPER: BERSIHKAN PESAN ERROR DATABASE + RINGKASAN HASIL CLOSING
     * ===================================================================== */
    private function closingCleanError($msg)
    {
        $msg = preg_replace('/^ERROR:\s*/i', '', trim((string) $msg));
        $msg = preg_replace('/\s*CONTEXT:.*$/i', '', $msg);
        $msg = preg_replace('/\s*LINE \d+:.*$/i', '', $msg);
        $msg = preg_replace('/\s+/', ' ', $msg);

        return $msg;
    }

    /* Pesan error database terakhir (ENVIRONMENT production -> DBDebug=false,
       sehingga error query tidak otomatis menjadi exception). */
    private function closingDbError()
    {
        $err = $this->db->error();

        return isset($err['message']) ? trim((string) $err['message']) : '';
    }

    /* Ambil pesan error yang paling informatif antara error database & exception */
    private function closingErrorText(\Throwable $e)
    {
        $dbErr = $this->closingDbError();
        if ($dbErr !== '') {
            return $this->closingCleanError($dbErr);
        }

        return $this->closingCleanError($e->getMessage());
    }

    /* Status closing periode ('' bila periode tidak ditemukan) */
    private function closingQueryStatus($idurut)
    {
        $query = $this->db->query(
            "SELECT COALESCE(NULLIF(BTRIM(status_closing), ''), '') AS status_closing
               FROM sc_trx.closeperiod
              WHERE idurut = ?",
            [(int) $idurut]
        );

        if ($query === false) {
            return '';
        }

        $row = $query->getRowArray();

        return isset($row['status_closing']) ? trim($row['status_closing']) : '';
    }

    /* Eksekusi query & lempar error database yang jelas bila gagal.
       (ENVIRONMENT production => DBDebug=false, query gagal hanya mengembalikan false) */
    private function closingExec($sql, $params = [])
    {
        $query = $this->db->query($sql, $params);

        if ($query === false) {
            $dbErr = $this->closingDbError();
            throw new \Exception($dbErr !== '' ? $this->closingCleanError($dbErr) : 'Query database gagal dieksekusi.');
        }

        return $query;
    }

    /* Mulai transaksi closing dengan status transaksi CI4 yang bersih.
       transStatus bersifat "sticky" pada mode strict (transStrict default true),
       sehingga setelah satu query gagal, transaksi berikutnya dapat ikut
       di-rollback tanpa terlihat. */
    private function closingTransStart()
    {
        try {
            $prop = new \ReflectionProperty($this->db, 'transStatus');
            $prop->setAccessible(true);
            $prop->setValue($this->db, true);
        } catch (\Throwable $e) {
            // abaikan: hanya upaya reset status transaksi
        }

        $this->db->transStrict(false);
        $this->db->transStart();
    }

    /* Selesaikan transaksi; lempar error bila database membatalkannya */
    private function closingTransComplete()
    {
        if ($this->db->transComplete() === false) {
            throw new \Exception('Transaksi dibatalkan (rollback) oleh database.');
        }
    }

    /* Query yang mengembalikan satu baris (array|null) */
    private function closingRow($sql, $params = [])
    {
        $row = $this->closingExec($sql, $params)->getRowArray();

        return $row === null ? null : $row;
    }

    /* Query yang mengembalikan banyak baris */
    private function closingRows($sql, $params = [])
    {
        return $this->closingExec($sql, $params)->getResultArray();
    }

    private function closingSummary($idurut)
    {
        try {
            $row = $this->closingPeriodById($idurut);
            if (!$row) {
                return null;
            }
        } catch (\Throwable $e) {
            // ringkasan hanya informasi tambahan; jangan menggagalkan proses utama
            return null;
        }

        return [
            'idurut'         => (int) $row['idurut'],
            'periode'        => trim($row['periode']),
            'tanggal_mulai'  => $row['tanggal_mulai'],
            'tanggal_akhir'  => $row['tanggal_akhir'],
            'status_closing' => trim($row['status_closing']),
            'flagproses'     => trim($row['flagproses']),
            'closing_debet'  => $row['closing_debet'],
            'closing_kredit' => $row['closing_kredit'],
        ];
    }

    /* =====================================================================
     * 1. PROSES TUTUP BULAN OTOMATIS (1 KLIK)
     * ===================================================================== */
    public function processTutupBulan()
    {
        $nama     = $this->closingNama();
        $login    = $this->closingLoginPeriod();
        $periode  = trim($this->request->getPost('periode'));
        $tglmulai = trim($this->request->getPost('tglmulai'));
        $tglakhir = trim($this->request->getPost('tglakhir'));
        $approver = trim($this->request->getPost('approver'));

        if ($periode === '')  { $periode  = $login['periode']; }
        if ($tglmulai === '') { $tglmulai = $login['tanggal_mulai']; }
        if ($tglakhir === '') { $tglakhir = $login['tanggal_akhir']; }
        /* Approver otomatis dari session login ($nama) */
        if ($approver === '') { $approver = $nama; }

        if ($periode === '' || $tglmulai === '' || $tglakhir === '') {
            return $this->closingJson(false, 'Periode / rentang tanggal closing tidak valid.');
        }

        $db = $this->db;
        $this->closingTransStart();

        try {
            $row = $this->closingRow(
                "SELECT sc_trx.fn_run_monthly_closing(?, ?::date, ?::date, ?, ?) AS idurut",
                [$periode, $tglmulai, $tglakhir, $nama, $approver]
            );

            $this->closingTransComplete();

            $idurut = isset($row['idurut']) ? (int) $row['idurut'] : 0;

            return $this->closingJson(
                true,
                'Tutup bulan periode ' . $periode . ' berhasil. Transaksi periode ini terkunci.',
                ['idurut' => $idurut, 'summary' => $this->closingSummary($idurut)]
            );
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);
            $db->transRollback();
            return $this->closingJson(false, 'Gagal proses tutup bulan: ' . $errText);
        }
    }


    /* =====================================================================
     * 2. REGISTRASI / UPDATE PERIODE CLOSING
     * ===================================================================== */
    public function registerClosingPeriod()
    {
        $nama     = $this->closingNama();
        $login    = $this->closingLoginPeriod();
        $periode  = trim($this->request->getPost('periode'));
        $tglmulai = trim($this->request->getPost('tglmulai'));
        $tglakhir = trim($this->request->getPost('tglakhir'));

        if ($periode === '')  { $periode  = $login['periode']; }
        if ($tglmulai === '') { $tglmulai = $login['tanggal_mulai']; }
        if ($tglakhir === '') { $tglakhir = $login['tanggal_akhir']; }

        if ($periode === '' || $tglmulai === '' || $tglakhir === '') {
            return $this->closingJson(false, 'Periode dan rentang tanggal wajib diisi.');
        }

        $db = $this->db;
        $this->closingTransStart();

        try {
            $row = $this->closingRow(
                "SELECT sc_trx.fn_register_closeperiod(?, ?::date, ?::date, ?) AS idurut",
                [$periode, $tglmulai, $tglakhir, $nama]
            );

            $this->closingTransComplete();

            $idurut = isset($row['idurut']) ? (int) $row['idurut'] : 0;

            return $this->closingJson(
                true,
                'Periode ' . $periode . ' (' . date('d-m-Y', strtotime($tglmulai)) . ' s/d ' . date('d-m-Y', strtotime($tglakhir)) . ') berhasil diregistrasi.',
                ['idurut' => $idurut, 'summary' => $this->closingSummary($idurut)]
            );
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);
            $db->transRollback();

            return $this->closingJson(false, 'Gagal registrasi periode: ' . $errText);
        }
    }

    /* =====================================================================
     * 3. DIAGNOSTIK PERIODE (READ ONLY)
     * ===================================================================== */
    public function getClosingDiagnostics()
    {
        $idurut = (int) $this->request->getPost('idurut');

        if ($idurut <= 0) {
            return $this->closingJson(false, 'Periode belum diregistrasi.');
        }

        try {
            $period = $this->closingPeriodById($idurut);
            if (!$period) {
                return $this->closingJson(false, 'Periode tidak ditemukan.');
            }

            $diagnostics = $this->closingRows(
                "SELECT check_code, metric_name, metric_value, detail
                   FROM sc_trx.fn_period_closing_diagnostics(?)",
                [$idurut]
            );

            $tb = $this->closingRow(
                "SELECT total_debet, total_kredit, selisih, jurnal_tidak_balance
                   FROM sc_trx.fn_period_closing_trial_balance(?)",
                [$idurut]
            );

            $unclassified = [];
            if (!empty($period['tanggal_mulai']) && !empty($period['tanggal_akhir'])) {
                $unclassified = $this->closingRows(
                    "SELECT BTRIM(d.idcoa::TEXT) AS idcoa,
                            COALESCE(BTRIM(c.nmcoa::TEXT), '') AS nmcoa,
                            ROUND(COALESCE(SUM(d.debet),0)::NUMERIC, 2) AS total_debet,
                            ROUND(COALESCE(SUM(d.kredit),0)::NUMERIC, 2) AS total_kredit
                       FROM sc_trx.jurnal_dt d
                       JOIN sc_trx.jurnal_hd h ON h.id = d.jurnal_id
                       LEFT JOIN sc_trx.period_closing_coa_rule r
                              ON r.idcoa = BTRIM(d.idcoa::TEXT) AND r.active = TRUE
                       LEFT JOIN sc_mst.coa c ON BTRIM(c.idcoa::TEXT) = BTRIM(d.idcoa::TEXT)
                      WHERE h.trxdate BETWEEN ?::date AND ?::date
                        AND h.status = 'POSTED' AND h.accounting_effect = 'YES'
                        AND r.idcoa IS NULL
                      GROUP BY BTRIM(d.idcoa::TEXT), BTRIM(c.nmcoa::TEXT)
                      ORDER BY BTRIM(d.idcoa::TEXT)",
                    [$period['tanggal_mulai'], $period['tanggal_akhir']]
                );
            }

            return $this->closingJson(true, 'Diagnostik periode berhasil diambil.', [
                'periode'      => $period,
                'diagnostics'  => $diagnostics,
                'trialbalance' => $tb,
                'unclassified' => $unclassified,
            ]);
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);

            return $this->closingJson(false, 'Gagal mengambil diagnostik: ' . $errText);
        }
    }


    /* =====================================================================
     * 4. MULAI CLOSING (OPEN -> CHECKING)
     * ===================================================================== */
    public function startClosingPeriod()
    {
        return $this->closingStep('start');
    }

    /* =====================================================================
     * 5. TUTUP PERIODE (CHECKING -> REVIEW -> CLOSED, flagproses TUTUP)
     * ===================================================================== */
    public function closeClosingPeriod()
    {
        return $this->closingStep('close');
    }

    /* =====================================================================
     * 6. BUKA KEMBALI PERIODE (CLOSED -> REOPENED)
     * ===================================================================== */
    public function reopenClosingPeriod()
    {
        return $this->closingStep('reopen');
    }


    /* ---------------------------------------------------------------------
     * Eksekutor tahapan closing (start / close / reopen)
     * ------------------------------------------------------------------- */
    private function closingStep($step)
    {
        $nama   = $this->closingNama();
        $idurut = (int) $this->request->getPost('idurut');
        $reason = trim($this->request->getPost('reason'));

        if ($idurut <= 0) {
            return $this->closingJson(false, 'Periode belum diregistrasi.');
        }

        $db = $this->db;
        $this->closingTransStart();

        try {
            switch ($step) {
                case 'start':
                    $this->closingExec("SELECT sc_trx.fn_start_period_closing(?, ?)", [$idurut, $nama]);
                    $message = 'Periode masuk tahap CHECKING. Lanjutkan dengan Tutup Periode.';
                    break;

                case 'close':
                    /* Approver otomatis dari session login ($nama). */
                    $approver = trim($this->request->getPost('approver'));
                    if ($approver === '') { $approver = $nama; }

                    /* Pastikan periode berada pada tahap CHECKING/REVIEW. */
                    $status = $this->closingQueryStatus($idurut);
                    if (!in_array($status, ['CHECKING', 'REVIEW'], true)) {
                        $this->closingExec("SELECT sc_trx.fn_start_period_closing(?, ?)", [$idurut, $nama]);
                    }

                    $tb = $this->closingRow(
                        "SELECT total_debet, total_kredit FROM sc_trx.fn_period_closing_trial_balance(?)",
                        [$idurut]
                    );

                    $this->closingExec(
                        "UPDATE sc_trx.closeperiod
                            SET closing_debet = ?, closing_kredit = ?, updateby = ?, updatedate = CURRENT_TIMESTAMP
                          WHERE idurut = ?",
                        [$tb['total_debet'], $tb['total_kredit'], $nama, $idurut]
                    );

                    if ($this->closingQueryStatus($idurut) !== 'REVIEW') {
                        $this->closingExec("SELECT sc_trx.fn_submit_period_closing_review(?, ?)", [$idurut, $nama]);
                    }

                    $this->closingExec("SELECT sc_trx.fn_close_accounting_period(?, ?, ?)", [$idurut, $nama, $approver]);
                    $message = 'Periode berhasil ditutup (CLOSED). Transaksi periode ini terkunci.';
                    break;

                case 'reopen':
                    if ($reason === '') {
                        throw new \Exception('Alasan buka kembali periode wajib diisi.');
                    }
                    $this->closingExec("SELECT sc_trx.fn_reopen_accounting_period(?, ?, ?)", [$idurut, $nama, $reason]);
                    $message = 'Periode berhasil dibuka kembali (REOPENED). Transaksi periode ini diizinkan lagi.';
                    break;

                default:
                    throw new \Exception('Tahapan closing tidak dikenal.');
            }

            $this->closingTransComplete();

            return $this->closingJson(true, $message, [
                'idurut'  => $idurut,
                'summary' => $this->closingSummary($idurut),
            ]);
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);
            $db->transRollback();

            return $this->closingJson(false, 'Gagal proses: ' . $errText);
        }
    }

    /* =====================================================================
     * 7. JURNAL PENUTUP TAHUNAN (DRAFT CLSPER)
     * ===================================================================== */
    public function prepareYearEndClosing()
    {
        $nama   = $this->closingNama();
        $idurut = (int) $this->request->getPost('idurut');
        $coa    = trim($this->request->getPost('coa'));

        if ($idurut <= 0) {
            return $this->closingJson(false, 'Periode belum diregistrasi.');
        }
        if ($coa === '') {
            return $this->closingJson(false, 'COA laba/rugi (akun lawan jurnal penutup) wajib diisi.');
        }

        $db = $this->db;
        $this->closingTransStart();

        try {
            $row = $this->closingRow(
                "SELECT sc_trx.fn_prepare_year_end_close(?, ?, ?) AS jurnal_id",
                [$idurut, $nama, $coa]
            );

            $this->closingTransComplete();

            $jurnal_id = isset($row['jurnal_id']) ? (int) $row['jurnal_id'] : 0;

            return $this->closingJson(
                true,
                'Jurnal penutup tahunan berhasil dibuat dengan status DRAFT. Periksa detail lalu posting melalui menu jurnal.',
                ['idurut' => $idurut, 'jurnal_id' => $jurnal_id, 'detail' => $this->closingJournalDetail($jurnal_id)]
            );
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);
            $db->transRollback();

            return $this->closingJson(false, 'Gagal membuat jurnal penutup: ' . $errText);
        }
    }

    /* =====================================================================
     * 11. DETAIL JURNAL PENUTUP (HEADER + BARIS)
     * ===================================================================== */
    public function getClosingYearEndJournal()
    {
        $jurnal_id = (int) $this->request->getPost('jurnal_id');

        if ($jurnal_id <= 0) {
            return $this->closingJson(false, 'Jurnal penutup tidak valid.');
        }

        try {
            $detail = $this->closingJournalDetail($jurnal_id);
            if (!$detail) {
                return $this->closingJson(false, 'Jurnal penutup tidak ditemukan.');
            }

            return $this->closingJson(true, 'Detail jurnal penutup berhasil diambil.', ['detail' => $detail]);
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);

            return $this->closingJson(false, 'Gagal mengambil detail jurnal: ' . $errText);
        }
    }

    private function closingJournalDetail($jurnal_id)
    {
        if (empty($jurnal_id)) {
            return null;
        }

        $header = $this->closingRow(
            "SELECT h.id, h.docno, h.doctype, h.journal_type, h.trxdate, h.status,
                    h.total_debet, h.total_kredit, h.balance, h.keterangan,
                    h.createdby, h.createddate
               FROM sc_trx.jurnal_hd h
              WHERE h.id = ?",
            [(int) $jurnal_id]
        );

        if (!$header) {
            return null;
        }

        $lines = $this->closingRows(
            "SELECT d.id, d.seq, d.idcoa, COALESCE(BTRIM(c.nmcoa::TEXT), '') AS nmcoa,
                    d.debet, d.kredit, d.account_role, d.status, d.keterangan
               FROM sc_trx.jurnal_dt d
               LEFT JOIN sc_mst.coa c ON BTRIM(c.idcoa::TEXT) = BTRIM(d.idcoa::TEXT)
              WHERE d.jurnal_id = ?
              ORDER BY d.seq NULLS LAST, d.id",
            [(int) $jurnal_id]
        );

        return ['header' => $header, 'lines' => $lines];
    }

    /* =====================================================================
     * 8. SIMPAN KLASIFIKASI COA CLOSING (BS / PNL)
     *    Dipakai tombol "Klasifikasi Cepat" pada tab Diagnostik.
     * ===================================================================== */
    public function saveClosingCoaRule()
    {
        $nama    = $this->closingNama();
        $idcoa   = trim($this->request->getPost('idcoa'));
        $kelas   = strtoupper(trim($this->request->getPost('account_class')));
        $active  = trim($this->request->getPost('active'));
        $ket     = trim($this->request->getPost('description'));

        if ($idcoa === '') {
            return $this->closingJson(false, 'Kode akun (COA) wajib diisi.');
        }
        if (!in_array($kelas, ['PNL', 'BS'], true)) {
            return $this->closingJson(false, 'Kelas akun harus PNL atau BS.');
        }

        $aktif = ($active === '0' || strtoupper($active) === 'FALSE' || strtoupper($active) === 'N') ? false : true;

        $db = $this->db;
        $this->closingTransStart();

        try {
            $coa = $this->closingRow(
                "SELECT BTRIM(idcoa::TEXT) AS idcoa, COALESCE(BTRIM(nmcoa::TEXT), '') AS nmcoa
                   FROM sc_mst.coa
                  WHERE BTRIM(idcoa::TEXT) = ?",
                [$idcoa]
            );

            if (!$coa) {
                throw new \Exception('COA ' . $idcoa . ' tidak ditemukan pada master COA.');
            }
            if ($ket === '') {
                $ket = $coa['nmcoa'];
            }

            $this->closingExec(
                "INSERT INTO sc_trx.period_closing_coa_rule
                    (idcoa, account_class, active, description, created_by, created_at)
                 VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                 ON CONFLICT (idcoa) DO UPDATE
                    SET account_class = EXCLUDED.account_class,
                        active        = EXCLUDED.active,
                        description   = EXCLUDED.description,
                        updated_by    = ?,
                        updated_at    = CURRENT_TIMESTAMP",
                [$idcoa, $kelas, $aktif ? 'true' : 'false', $ket, $nama, $nama]
            );

            $this->closingTransComplete();

            return $this->closingJson(true, 'Klasifikasi COA ' . $idcoa . ' disimpan sebagai ' . $kelas . '.');
        } catch (\Throwable $e) {
            $errText = $this->closingErrorText($e);
            $db->transRollback();

            return $this->closingJson(false, 'Gagal menyimpan klasifikasi COA: ' . $errText);
        }
    }

}
