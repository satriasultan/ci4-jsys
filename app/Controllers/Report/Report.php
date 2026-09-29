<?php

namespace App\Controllers\Report;

use App\Controllers\BaseController;

class Report extends BaseController
{
    /* =========================================================
    LAPORAN OUTSTANDING PP
    ========================================================= */
    public function outspp()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Outstanding PP";
        $data['jenisLaporan'] = 'outstanding';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_outspp', $data);
    }

    /* =========================================================
    LAPORAN PP HISTORY
    ========================================================= */
    public function pphistory()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan PP History";
        $data['jenisLaporan'] = 'history';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_pphistory', $data);
    }

    /* =========================================================
    COMMON DATA — dipakai kedua view
    ========================================================= */
    private function commonData()
    {
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $data['branch'] = $dtlbranch['branch'];

        $nama      = trim($this->session->get('nama'));
        $kodemenu  = 'I.H.B';  // parent menu report pembelian
        $versirelease = 'I.H.B/01';
        $releasedate  = date('2025-04-12 00:00:00');

        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x       = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);

        $data['x']         = $x['rows'];
        $data['y']         = $x['res'];
        $data['t']         = $x['xn'];
        $data['kodemenu']  = $kodemenu;
        $data['version']   = $versidb;
        $data['nama']      = $nama;

        /* error handling */
        $paramerror = " and userid='$nama' and modul='I.H.B'";
        $dtlerror   = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err  = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();

        $errordesc   = isset($dtlerror['description'])  ? trim($dtlerror['description'])  : '';
        $nomorakhir1 = isset($dtlerror['nomorakhir1'])  ? trim($dtlerror['nomorakhir1'])  : '';
        $errorcode   = isset($dtlerror['errorcode'])    ? trim($dtlerror['errorcode'])    : '';

        if ($count_err > 0 && $errordesc <> '') {
            if ($dtlerror['errorcode'] == 0) {
                $data['message'] = "<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message'] = "<div class='alert alert-info'>$errordesc</div>";
            }
        } else {
            $data['message'] = ($errorcode == '0')
                ? "<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>"
                : "";
        }

        /* akses */
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kodemenu)->getRowArray();

        return $data;
    }


    /* =========================================================
    PREVIEW DATA PP — AJAX, server-side pagination
    ========================================================= */
    public function previewLaporanPP()
    {
        $jenis    = $this->request->getPost('jenisLaporan');
        $tglrange = $this->request->getPost('tglrange');
        $docno    = $this->request->getPost('docno');
        $idbarang = $this->request->getPost('idbarang');
        $cabang   = $this->request->getPost('cabang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $offset = ($page - 1) * $perpage;

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "pp.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($idbarang)) {
            $where[] = "TRIM(dtl.idbarang) = ?";
            $bind[]  = trim(strtoupper($idbarang));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(pp.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }
        if ($jenis === 'outstanding') {
            $where[] = "(COALESCE(dtl.qtypo,0) + COALESCE(dtl.qtyvoid,0)) <> dtl.qty";
            $where[] = "TRIM(pp.status) <> 'C'";
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT TOTAL ====== */
        $sqlCount = "
            SELECT COUNT(*) AS total
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
            $whereSql
        ";
        $totalRow = $this->db->query($sqlCount, $bind)->getRowArray();
        $total    = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== AMBIL DATA HALAMAN INI ====== */
        $sqlData = "
            SELECT
                TRIM(dtl.docno)                      AS docno,
                TO_CHAR(pp.docdate, 'DD-MM-YYYY')    AS docdate,
                TRIM(pp.cabang)                      AS cabang,
                TRIM(pp.pemohon)                     AS pemohon,
                TRIM(dtl.idbarang)                   AS idbarang,
                TRIM(dtl.nmbarang)                   AS nmbarang,
                TRIM(dtl.unit)                       AS unit,
                dtl.qty                              AS qty,
                dtl.qtypo                            AS qtypo,
                dtl.qtyvoid                          AS qtyvoid,
                (COALESCE(dtl.qtypo,0) + COALESCE(dtl.qtyvoid,0)) AS qty_proses,
                dtl.description                      AS description,
                TRIM(dtl.capexno)                    AS capexno,
                pp.keterangan                        AS keterangan_pp
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY pp.docdate DESC, dtl.docno, dtl.idurut
            LIMIT ? OFFSET ?
        ";
        $bindData   = $bind;
        $bindData[] = $perpage;
        $bindData[] = $offset;

        $rows = $this->db->query($sqlData, $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }
    /* =========================================================
    DOWNLOAD EXCEL — dipakai kedua laporan
    ========================================================= */
    public function downloadLaporanPP()
    {
        $jenis    = $this->request->getPost('jenisLaporan');
        $tglrange = $this->request->getPost('tglrange');
        $docno    = $this->request->getPost('docno');
        $idbarang = $this->request->getPost('idbarang');
        $cabang   = $this->request->getPost('cabang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "pp.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($idbarang)) {
            $where[] = "TRIM(dtl.idbarang) = ?";
            $bind[]  = trim(strtoupper($idbarang));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(pp.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        if ($jenis === 'outstanding') {
            $where[] = "(COALESCE(dtl.qtypo,0) + COALESCE(dtl.qtyvoid,0)) <> dtl.qty";
            $where[] = "TRIM(pp.status) <> 'C'";
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL — PostgreSQL pakai TO_CHAR ====== */
        $sql = "
            SELECT
                TRIM(dtl.docno)                      AS docno,
                TO_CHAR(pp.docdate, 'DD-MM-YYYY')    AS docdate,
                TRIM(pp.cabang)                      AS cabang,
                TRIM(pp.pemohon)                     AS pemohon,
                TRIM(dtl.idbarang)                   AS idbarang,
                TRIM(dtl.nmbarang)                   AS nmbarang,
                TRIM(dtl.unit)                       AS unit,
                dtl.qty                              AS qty,
                dtl.qtypo                            AS qtypo,
                dtl.qtyvoid                          AS qtyvoid,
                (COALESCE(dtl.qtypo,0) + COALESCE(dtl.qtyvoid,0)) AS qty_proses,
                dtl.description                      AS description,
                TRIM(dtl.capexno)                    AS capexno,
                pp.keterangan                        AS keterangan_pp
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY pp.docdate DESC, dtl.docno, dtl.idurut
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan PP query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = ($jenis === 'outstanding')
            ? 'LAPORAN OUTSTANDING PP'
            : 'LAPORAN PP HISTORY';

        // Judul
        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Header kolom (14 kolom, tanpa status)
        $headers = [
            'No. Dokumen', 'Tanggal', 'Cabang', 'Pemohon',
            'ID Barang', 'Nama Barang', 'Satuan',
            'Qty', 'Qty PO', 'Qty Void', 'Qty Proses',
            'Keterangan', 'No. Capex', 'Keterangan PP'
        ];
        $sheet->fromArray($headers, null, 'A3');

        // Style header
        $sheet->getStyle('A3:N3')->getFont()->setBold(true);
        $sheet->getStyle('A3:N3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        /* ====== TULIS DATA ROW PER ROW ====== */
        $rowNum    = 4;
        $rowCount  = 0;

        // Untuk auto-width sampling
        $sampleWidth = array_fill(0, count($headers), 0);
        $sampleMax   = 100;   // ambil 100 row pertama buat hitung lebar
        $sampleCount = 0;

        // Siapkan row buffer
        $buffer = [];

        while ($r = $query->getUnbufferedRow('array')) {
            $rowData = [
                $r['docno'], $r['docdate'], $r['cabang'], $r['pemohon'],
                $r['idbarang'], $r['nmbarang'], $r['unit'],
                $r['qty'], $r['qtypo'], $r['qtyvoid'], $r['qty_proses'],
                $r['description'], $r['capexno'], $r['keterangan_pp']
            ];

            // Tulis ke sheet
            $sheet->fromArray($rowData, null, 'A' . $rowNum);

            // Sampling untuk auto-width
            if ($sampleCount < $sampleMax) {
                foreach ($rowData as $i => $val) {
                    $len = mb_strlen((string)$val);
                    if ($len > $sampleWidth[$i]) $sampleWidth[$i] = $len;
                }
                $sampleCount++;
            }

            $rowNum++;
            $rowCount++;
        }

        /* ====== TIDAK ADA DATA ====== */
        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells('A4:N4');
        }

        /* ====== AUTO-WIDTH "CERDAS" DARI SAMPEL ====== */
        // Ambil max dari header & sampel 100 row pertama
        $colLetter = 'A';
        foreach ($headers as $i => $h) {
            $lenHeader = mb_strlen($h);
            $lenSample = $sampleWidth[$i];

            // Max lebar: header atau sampel, + 2 padding
            $width = max($lenHeader, $lenSample) + 2;

            // Batasi max 40 karakter biar tidak kolom raksasa
            if ($width > 40) $width = 40;
            // Minimal 8 karakter
            if ($width < 8) $width = 8;

            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colLetter++;
        }

        /* ====== FREEZE PANE (opsional) ====== */
        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_PP_' . ucfirst($jenis) . '_' . date('Ymd_His') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');

        // Bebaskan memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }





    // ========================== PO SECTION =====================================



    /* =========================================================
    LAPORAN OUTSTANDING PO
    ========================================================= */
    public function outspo()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Outstanding PO";
        $data['jenisLaporan'] = 'outstanding_po';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_outspo', $data);
    }

    /* =========================================================
    LAPORAN PO HISTORY
    ========================================================= */
    public function pohistory()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan PO History";
        $data['jenisLaporan'] = 'history_po';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_pohistory', $data);
    }

    /* =========================================================
   PREVIEW DATA PO — AJAX, server-side pagination
   ========================================================= */
    public function previewLaporanPO()
    {
        $jenis      = $this->request->getPost('jenisLaporan');   // 'outstanding_po' | 'history_po'
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $offset = ($page - 1) * $perpage;

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "po.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(po.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(po.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }
        if ($jenis === 'outstanding_po') {
            $where[] = "(COALESCE(dtl.qtylpb,0) + COALESCE(dtl.qtyvoid,0)) <> dtl.qty";
            $where[] = "TRIM(po.status) <> 'C'";
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT ====== */
        $sqlCount = "
            SELECT COUNT(*) AS total
            FROM sc_trx.po_dtl dtl
            INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(dtl.docno)
            $whereSql
        ";
        $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== DATA HALAMAN INI ====== */
        $sqlData = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(po.docdate, 'DD-MM-YYYY')     AS docdate,
                TRIM(po.cabang)                       AS cabang,
                TRIM(po.kdsupplier)                   AS kdsupplier,
                TRIM(po.nmsupplier)                   AS nmsupplier,
                TRIM(po.pemohon)                      AS pemohon,
                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                TRIM(dtl.unit)                        AS unit,
                dtl.qty                               AS qty,
                dtl.qtybonus                          AS qtybonus,
                dtl.qtylpb                            AS qtylpb,
                dtl.qtyvoid                           AS qtyvoid,
                (COALESCE(dtl.qtylpb,0) + COALESCE(dtl.qtyvoid,0)) AS qty_proses,
                dtl.harga                             AS harga,
                dtl.nilai                             AS nilai,
                dtl.multidisc                         AS multidisc,
                dtl.totaldiscount                     AS totaldiscount,
                dtl.nilaipajak                        AS nilaipajak,
                dtl.descriptionpo                     AS descriptionpo,
                dtl.descriptionpp                     AS descriptionpp,
                po.keterangan                         AS keterangan_po
            FROM sc_trx.po_dtl dtl
            INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY po.docdate DESC, dtl.docno, dtl.idurut
            LIMIT ? OFFSET ?
        ";
        $bindData   = $bind;
        $bindData[] = $perpage;
        $bindData[] = $offset;

        $rows = $this->db->query($sqlData, $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }


    /* =========================================================
    DOWNLOAD EXCEL — PO (Outstanding & History)
    ========================================================= */
    public function downloadLaporanPO()
    {
        $jenis      = $this->request->getPost('jenisLaporan');
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "po.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(po.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(po.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }
        if ($jenis === 'outstanding_po') {
            $where[] = "(COALESCE(dtl.qtylpb,0) + COALESCE(dtl.qtyvoid,0)) <> dtl.qty";
            $where[] = "TRIM(po.status) <> 'C'";
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL ====== */
        $sql = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(po.docdate, 'DD-MM-YYYY')     AS docdate,
                TRIM(po.cabang)                       AS cabang,
                TRIM(po.kdsupplier)                   AS kdsupplier,
                TRIM(po.nmsupplier)                   AS nmsupplier,
                TRIM(po.pemohon)                      AS pemohon,
                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                TRIM(dtl.unit)                        AS unit,
                dtl.qty                               AS qty,
                dtl.qtybonus                          AS qtybonus,
                dtl.qtylpb                            AS qtylpb,
                dtl.qtyvoid                           AS qtyvoid,
                (COALESCE(dtl.qtylpb,0) + COALESCE(dtl.qtyvoid,0)) AS qty_proses,
                dtl.harga                             AS harga,
                dtl.nilai                             AS nilai,
                dtl.multidisc                         AS multidisc,
                dtl.totaldiscount                     AS totaldiscount,
                dtl.nilaipajak                        AS nilaipajak,
                dtl.descriptionpo                     AS descriptionpo,
                dtl.descriptionpp                     AS descriptionpp,
                TRIM(dtl.status)                      AS status_dtl,
                TRIM(po.status)                       AS status_po,
                po.keterangan                         AS keterangan_po
            FROM sc_trx.po_dtl dtl
            INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY po.docdate DESC, dtl.docno, dtl.idurut
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan PO query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = ($jenis === 'outstanding_po')
            ? 'LAPORAN OUTSTANDING PO'
            : 'LAPORAN PO HISTORY';

        /* --- Judul (24 kolom: A..X) --- */
        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:X1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* --- Header: 24 kolom --- */
        $headers = [
            'No. Dokumen', 'Tanggal', 'Cabang',
            'Kode Supplier', 'Nama Supplier', 'Pemohon',
            'ID Barang', 'Nama Barang', 'Satuan',
            'Qty', 'Qty Bonus', 'Qty LPB', 'Qty Void', 'Qty Proses',
            'Harga Satuan', 'Jumlah', 'Discount', 'PPN', 'Total',
            'Keterangan PO', 'Keterangan PP',
            'Status Detail', 'Status PO', 'Keterangan'
        ];
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'X'; // 24 kolom
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum      = 4;
        $rowCount    = 0;
        $sampleWidth = array_fill(0, count($headers), 0);
        $sampleMax   = 100;
        $sampleCount = 0;

        while ($r = $query->getUnbufferedRow('array')) {
            $rowData = [
                $r['docno'], $r['docdate'], $r['cabang'],
                $r['kdsupplier'], $r['nmsupplier'], $r['pemohon'],
                $r['idbarang'], $r['nmbarang'], $r['unit'],
                $r['qty'], $r['qtybonus'], $r['qtylpb'], $r['qtyvoid'], $r['qty_proses'],
                $r['harga'], $r['nilai'], $r['multidisc'], $r['totaldiscount'], $r['nilaipajak'],
                $r['descriptionpo'], $r['descriptionpp'],
                $r['status_dtl'], $r['status_po'], $r['keterangan_po']
            ];  // 24 kolom ✅ — SAMA dengan $headers

            $sheet->fromArray($rowData, null, 'A' . $rowNum);

            if ($sampleCount < $sampleMax) {
                foreach ($rowData as $i => $val) {
                    $len = mb_strlen((string)$val);
                    if ($len > $sampleWidth[$i]) $sampleWidth[$i] = $len;
                }
                $sampleCount++;
            }

            $rowNum++;
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A4:{$lastCol}4");
        }

        /* ====== AUTO-WIDTH ====== */
        $colLetter = 'A';
        foreach ($headers as $i => $h) {
            $width = max(mb_strlen($h), $sampleWidth[$i]) + 2;
            if ($width > 40) $width = 40;
            if ($width < 8)  $width = 8;
            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colLetter++;
        }

        /* ====== FORMAT ANGKA ====== */
        // Kolom J..S = Qty..Total (10 kolom angka)
        $firstDataRow = 4;
        $lastDataRow  = max(4, $rowNum - 1);
        foreach (['J','K','L','M','N','O','P','Q','R','S'] as $col) {
            $sheet->getStyle("{$col}{$firstDataRow}:{$col}{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_PO_' . ucfirst(str_replace('_po','',$jenis)) . '_' . date('Ymd_His') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }





    /* =========================================================
   LAPORAN VOID PO
   ========================================================= */
    public function voidpo()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Void PO";
        $data['jenisLaporan'] = 'void_po';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_voidpo', $data);
    }

    /* =========================================================
    PREVIEW DATA VOID PO — AJAX pagination
    ========================================================= */
    public function previewLaporanVoidPO()
    {
        $jenis      = $this->request->getPost('jenisLaporan');   // 'void_po'
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $offset = ($page - 1) * $perpage;

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "vp.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(vp.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(vp.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        // WAJIB: status bukan C
        $where[] = "TRIM(vp.status) <> 'C'";

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT ====== */
        $sqlCount = "
            SELECT COUNT(*) AS total
            FROM sc_trx.voidpo_dtl dtl
            INNER JOIN sc_trx.voidpo vp ON TRIM(vp.docno) = TRIM(dtl.docno)
            $whereSql
        ";
        $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== DATA HALAMAN INI ====== */
        $sqlData = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(vp.docdate, 'DD-MM-YYYY')     AS docdate,
                TRIM(vp.cabang)                       AS cabang,
                TRIM(vp.kdsupplier)                   AS kdsupplier,
                TRIM(vp.nmsupplier)                   AS nmsupplier,
                TRIM(dtl.docnopo)                     AS docnopo,
                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                TRIM(dtl.unit)                        AS unit,
                dtl.qty                               AS qty,
                dtl.harga                             AS harga,
                dtl.nilai                             AS nilai,
                dtl.descriptionpo                     AS descriptionpo,
                dtl.descriptionpp                     AS descriptionpp,
                TRIM(dtl.capexno)                     AS capexno,
                vp.keterangan                         AS keterangan_vp,
                vp.currcode                           AS currcode
            FROM sc_trx.voidpo_dtl dtl
            INNER JOIN sc_trx.voidpo vp ON TRIM(vp.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY vp.docdate DESC, dtl.docno, dtl.idurut
            LIMIT ? OFFSET ?
        ";
        $bindData   = $bind;
        $bindData[] = $perpage;
        $bindData[] = $offset;

        $rows = $this->db->query($sqlData, $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }

    /* =========================================================
    DOWNLOAD EXCEL — VOID PO
    ========================================================= */
    public function downloadLaporanVoidPO()
    {
        $jenis      = $this->request->getPost('jenisLaporan');
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "vp.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(vp.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(vp.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        // WAJIB: status bukan C
        $where[] = "TRIM(vp.status) <> 'C'";

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL ====== */
        $sql = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(vp.docdate, 'DD-MM-YYYY')     AS docdate,
                TRIM(vp.cabang)                       AS cabang,
                TRIM(vp.kdsupplier)                   AS kdsupplier,
                TRIM(vp.nmsupplier)                   AS nmsupplier,
                TRIM(dtl.docnopo)                     AS docnopo,
                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                TRIM(dtl.unit)                        AS unit,
                dtl.qty                               AS qty,
                dtl.harga                             AS harga,
                dtl.nilai                             AS nilai,
                dtl.descriptionpo                     AS descriptionpo,
                dtl.descriptionpp                     AS descriptionpp,
                TRIM(dtl.capexno)                     AS capexno,
                
                vp.keterangan                         AS keterangan_vp,
                vp.currcode                           AS currcode
            FROM sc_trx.voidpo_dtl dtl
            INNER JOIN sc_trx.voidpo vp ON TRIM(vp.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY vp.docdate DESC, dtl.docno, dtl.idurut
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan VoidPO query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = 'LAPORAN VOID PO';

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:R1');   // 18 kolom (A..R)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* --- Header: 18 kolom --- */
        $headers = [
            'No. VPO', 'Tanggal', 'Cabang',
            'Kode Supplier', 'Nama Supplier', 'No. PO',
            'ID Barang', 'Nama Barang', 'Satuan',
            'Qty', 'Harga', 'Jumlah',
            'Keterangan PO', 'Keterangan PP',
            'No. Capex', 'Keterangan', 'Currency'
        ];
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'R'; // 18 kolom
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum      = 4;
        $rowCount    = 0;
        $sampleWidth = array_fill(0, count($headers), 0);
        $sampleMax   = 100;
        $sampleCount = 0;

        while ($r = $query->getUnbufferedRow('array')) {
            $rowData = [
                $r['docno'],
                $r['docdate'],
                $r['cabang'],
                $r['kdsupplier'],
                $r['nmsupplier'],
                $r['docnopo'],
                $r['idbarang'],
                $r['nmbarang'],
                $r['unit'],
                $r['qty'],
                $r['harga'],
                $r['nilai'],
                $r['descriptionpo'],
                $r['descriptionpp'],
                $r['capexno'],
                $r['keterangan_vp'],
                $r['currcode']
            ];  // 18 kolom ✅

            $sheet->fromArray($rowData, null, 'A' . $rowNum);

            if ($sampleCount < $sampleMax) {
                foreach ($rowData as $i => $val) {
                    $len = mb_strlen((string)$val);
                    if ($len > $sampleWidth[$i]) $sampleWidth[$i] = $len;
                }
                $sampleCount++;
            }

            $rowNum++;
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A4:{$lastCol}4");
        }

        /* ====== AUTO-WIDTH ====== */
        $colLetter = 'A';
        foreach ($headers as $i => $h) {
            $width = max(mb_strlen($h), $sampleWidth[$i]) + 2;
            if ($width > 40) $width = 40;
            if ($width < 8)  $width = 8;
            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colLetter++;
        }

        /* ====== FORMAT ANGKA ====== */
        // Kolom J, K, L = Qty, Harga, Jumlah
        $firstDataRow = 4;
        $lastDataRow  = max(4, $rowNum - 1);
        foreach (['J','K','L'] as $col) {
            $sheet->getStyle("{$col}{$firstDataRow}:{$col}{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_VoidPO_' . date('Ymd_His') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }




    /* =========================================================
    LAPORAN LPB
    ========================================================= */
    public function lpb()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Penerimaan Barang (LPB)";
        $data['jenisLaporan'] = 'lpb';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_lpb', $data);
    }

    /* =========================================================
    PREVIEW DATA LPB — AJAX pagination
    ========================================================= */
    public function previewLaporanLPB()
    {
        $jenis      = $this->request->getPost('jenisLaporan');   // 'lpb'
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $offset = ($page - 1) * $perpage;

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "lp.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(lp.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(lp.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        // WAJIB: status bukan C
        $where[] = "TRIM(lp.status) <> 'C'";

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT ====== */
        $sqlCount = "
            SELECT COUNT(*) AS total
            FROM sc_trx.lpb_dtl dtl
            INNER JOIN sc_trx.lpb lp ON TRIM(lp.docno) = TRIM(dtl.docno)
            $whereSql
        ";
        $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== DATA HALAMAN INI ====== */
        $sqlData = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(lp.docdate, 'DD-MM-YYYY')     AS docdate,
                TRIM(lp.cabang)                       AS cabang,
                TRIM(lp.kdsupplier)                   AS kdsupplier,
                TRIM(lp.nmsupplier)                   AS nmsupplier,
                TRIM(lp.alamatsupplier)               AS alamatsupplier,
                TRIM(lp.pemohon)                      AS pemohon,
                TRIM(dtl.docnopo)                     AS docnopo,
                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                TRIM(dtl.unit)                        AS unit,
                dtl.qty                               AS qty,
                dtl.qtybonus                          AS qtybonus,
                dtl.harga                             AS harga,
                dtl.multidisc                         AS multidisc,
                dtl.totaldiscount                     AS totaldiscount,
                dtl.nilai                             AS nilai,
                dtl.nilaipajak                        AS nilaipajak,
                dtl.nilaikonversi                     AS nilaikonversi,
                dtl.descriptionpo                     AS descriptionpo,
                dtl.descriptionpp                     AS descriptionpp,
                TRIM(dtl.capexno)                     AS capexno,
                dtl.currcode                          AS currcode,
                dtl.kurs                              AS kurs,
                lp.nofaktur                           AS nofaktur,
                lp.nosj                               AS nosj,
                lp.keterangan                         AS keterangan_lpb
            FROM sc_trx.lpb_dtl dtl
            INNER JOIN sc_trx.lpb lp ON TRIM(lp.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY lp.docdate DESC, dtl.docno, dtl.idurut
            LIMIT ? OFFSET ?
        ";
        $bindData   = $bind;
        $bindData[] = $perpage;
        $bindData[] = $offset;

        $rows = $this->db->query($sqlData, $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }

    /* =========================================================
    DOWNLOAD EXCEL — LPB
    ========================================================= */
    public function downloadLaporanLPB()
    {
        $jenis      = $this->request->getPost('jenisLaporan');
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "lp.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(lp.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(lp.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        // WAJIB: status bukan C
        $where[] = "TRIM(lp.status) <> 'C'";

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL ====== */
        $sql = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(lp.docdate, 'DD-MM-YYYY')     AS docdate,
                TRIM(lp.cabang)                       AS cabang,
                TRIM(lp.kdsupplier)                   AS kdsupplier,
                TRIM(lp.nmsupplier)                   AS nmsupplier,
                TRIM(lp.alamatsupplier)               AS alamatsupplier,
                TRIM(lp.inputby)                      AS pemohon,
                TRIM(dtl.docnopo)                     AS docnopo,
                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                TRIM(dtl.unit)                        AS unit,
                dtl.qty                               AS qty,
                dtl.qtybonus                          AS qtybonus,
                dtl.harga                             AS harga,
                dtl.multidisc                         AS multidisc,
                dtl.totaldiscount                     AS totaldiscount,
                dtl.nilai                             AS nilai,
                dtl.nilaipajak                        AS nilaipajak,
                dtl.nilaikonversi                     AS nilaikonversi,
                dtl.descriptionpo                     AS descriptionpo,
                dtl.descriptionpp                     AS descriptionpp,
                TRIM(dtl.capexno)                     AS capexno,
                dtl.currcode                          AS currcode,
                dtl.kurs                              AS kurs,
                lp.nofaktur                           AS nofaktur,
                lp.nosj                               AS nosj,
                lp.keterangan                         AS keterangan_lpb
            FROM sc_trx.lpb_dtl dtl
            INNER JOIN sc_trx.lpb lp ON TRIM(lp.docno) = TRIM(dtl.docno)
            $whereSql
            ORDER BY lp.docdate DESC, dtl.docno, dtl.idurut
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan LPB query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = 'LAPORAN PENERIMAAN BARANG (LPB)';

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:Z1');   // 26 kolom (A..Z)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* --- Header: 26 kolom --- */
        $headers = [
            'No. LPB', 'Tanggal', 'Cabang',
            'Kode Supplier', 'Nama Supplier', 'Alamat Supplier', 'Pemohon',
            'No. PO', 'ID Barang', 'Nama Barang', 'Satuan',
            'Qty', 'Qty Bonus', 'Harga', 'Disc', 'Total Disc', 'Jumlah',
            'PPN', 'Nilai Konversi', 'Kurs',
            'Keterangan PO', 'Keterangan PP', 'No. Capex',
            'No. Faktur', 'No. SJ', 'Keterangan LPB'
        ];  // 26 kolom ✅
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'Z'; // 26 kolom
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum      = 4;
        $rowCount    = 0;
        $sampleWidth = array_fill(0, count($headers), 0);
        $sampleMax   = 100;
        $sampleCount = 0;

        while ($r = $query->getUnbufferedRow('array')) {
            $rowData = [
                $r['docno'], $r['docdate'], $r['cabang'],
                $r['kdsupplier'], $r['nmsupplier'], $r['alamatsupplier'], $r['pemohon'],
                $r['docnopo'], $r['idbarang'], $r['nmbarang'], $r['unit'],
                $r['qty'], $r['qtybonus'], $r['harga'],
                $r['multidisc'], $r['totaldiscount'], $r['nilai'],
                $r['nilaipajak'], $r['nilaikonversi'], $r['kurs'],
                $r['descriptionpo'], $r['descriptionpp'], $r['capexno'],
                $r['nofaktur'], $r['nosj'], $r['keterangan_lpb']
            ];  // 26 kolom ✅ — MATCH dengan header

            $sheet->fromArray($rowData, null, 'A' . $rowNum);

            if ($sampleCount < $sampleMax) {
                foreach ($rowData as $i => $val) {
                    $len = mb_strlen((string)$val);
                    if ($len > $sampleWidth[$i]) $sampleWidth[$i] = $len;
                }
                $sampleCount++;
            }

            $rowNum++;
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A4:{$lastCol}4");
        }

        /* ====== AUTO-WIDTH ====== */
        $colLetter = 'A';
        foreach ($headers as $i => $h) {
            $width = max(mb_strlen($h), $sampleWidth[$i]) + 2;
            if ($width > 40) $width = 40;
            if ($width < 8)  $width = 8;
            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colLetter++;
        }

        /* ====== FORMAT ANGKA ====== */
        // Kolom L..T = Qty, Qty Bonus, Harga, Disc, Total Disc, Jumlah, PPN, Nilai Konversi, Kurs
        $firstDataRow = 4;
        $lastDataRow  = max(4, $rowNum - 1);
        foreach (['L','M','N','O','P','Q','R','S','T'] as $col) {
            $sheet->getStyle("{$col}{$firstDataRow}:{$col}{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_LPB_' . date('Ymd_His') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }
}