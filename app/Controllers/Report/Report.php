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

        /* =========================================================
        OUTSTANDING — logika LAMA (tidak diubah)
        ========================================================= */
        if ($jenis !== 'history') {
            $whereOut   = $where;
            $whereOut[] = "(COALESCE(dtl.qtypo,0) + COALESCE(dtl.qtyvoid,0)) <> dtl.qty";
            $whereOut[] = "TRIM(pp.status) <> 'C'";
            $whereSql   = ' WHERE ' . implode(' AND ', $whereOut);

            $sqlCount = "
                SELECT COUNT(*) AS total
                FROM sc_trx.pp_dtl dtl
                INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
                $whereSql
            ";
            $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
            $total     = (int)($totalRow['total'] ?? 0);
            $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

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
        HISTORY — UNION PP Detail + PO Detail
        ========================================================= */
        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        $unionSql = "
            /* --- Bagian 1: baris PP --- */
            SELECT
                TRIM(dtl.docno)                     AS docno_pp,
                ''                                  AS jurnal,
                TO_CHAR(pp.docdate, 'DD-MM-YYYY')   AS docdate,
                TRIM(pp.inputby)                    AS userid,
                TRIM(dtl.idbarang)                  AS idbarang,
                dtl.qty                             AS qty,
                0                                   AS qty_realisasi,
                TRIM(pp.pemohon)                    AS nama_user,
                TRIM(dtl.nmbarang)                  AS nmbarang,
                TRIM(dtl.unit)                      AS unit,
                dtl.description                     AS spec,
                pp.keterangan                       AS ket_pp_header,
                dtl.description                     AS ket_detail,
                TO_CHAR(pp.estpakai, 'DD-MM-YYYY')  AS tglpakai,
                TRIM(pp.cabang)                     AS job,
                1                                   AS src_order,
                CASE
                    WHEN TRIM(pp.status) = 'C'                                       THEN 'CANCEL'
                    WHEN COALESCE(agg.total_po, 0) >= dtl.qty                        THEN 'FINISH'
                    WHEN COALESCE(agg.total_po, 0) > 0 AND agg.total_po < dtl.qty    THEN 'OUTSTANDING'
                    ELSE                                                                  'OUTSTANDING'
                END                                 AS status_outstanding,
                COALESCE(agg.total_po, 0)           AS total_realisasi
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
            LEFT JOIN (
                SELECT TRIM(docnopp) AS docnopp, SUM(qty) AS total_po
                FROM sc_trx.po_dtl
                GROUP BY TRIM(docnopp)
            ) agg ON agg.docnopp = TRIM(dtl.docno)
            $whereSql

            UNION ALL

            /* --- Bagian 2: baris PO --- */
            SELECT
                TRIM(dtl.docno)                     AS docno_pp,
                TRIM(pod.docno)                     AS jurnal,
                TO_CHAR(pp.docdate, 'DD-MM-YYYY')   AS docdate,
                TRIM(pp.inputby)                    AS userid,
                TRIM(dtl.idbarang)                  AS idbarang,
                0                                   AS qty,
                pod.qty                             AS qty_realisasi,
                TRIM(pp.pemohon)                    AS nama_user,
                TRIM(dtl.nmbarang)                  AS nmbarang,
                TRIM(dtl.unit)                      AS unit,
                dtl.description                     AS spec,
                pp.keterangan                       AS ket_pp_header,
                dtl.description                     AS ket_detail,
                TO_CHAR(pp.estpakai, 'DD-MM-YYYY')  AS tglpakai,
                TRIM(pp.cabang)                     AS job,
                2                                   AS src_order,
                CASE
                    WHEN TRIM(pp.status) = 'C'                                       THEN 'CANCEL'
                    WHEN COALESCE(agg.total_po, 0) >= dtl.qty                        THEN 'FINISH'
                    WHEN COALESCE(agg.total_po, 0) > 0 AND agg.total_po < dtl.qty    THEN 'OUTSTANDING'
                    ELSE                                                                  'OUTSTANDING'
                END                                 AS status_outstanding,
                COALESCE(agg.total_po, 0)           AS total_realisasi
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp       ON TRIM(pp.docno)    = TRIM(dtl.docno)
            INNER JOIN sc_trx.po_dtl pod  ON TRIM(pod.docnopp)  = TRIM(dtl.docno)
            LEFT JOIN (
                SELECT TRIM(docnopp) AS docnopp, SUM(qty) AS total_po
                FROM sc_trx.po_dtl
                GROUP BY TRIM(docnopp)
            ) agg ON agg.docnopp = TRIM(dtl.docno)
            $whereSql
        ";

        /* COUNT */
        $sqlCount  = "SELECT COUNT(*) AS total FROM ($unionSql) x";
        $bindCount = array_merge($bind, $bind);
        $totalRow  = $this->db->query($sqlCount, $bindCount)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* DATA */
        $sqlData = "SELECT * FROM ($unionSql) z
                    ORDER BY docdate DESC, docno_pp, idbarang
                    LIMIT ? OFFSET ?";
        $bindData   = array_merge($bind, $bind);
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

        /* =========================================================
        TENTUKAN QUERY & HEADER
        ========================================================= */
        if ($jenis !== 'history') {
            /* ---- OUTSTANDING (logika lama) ---- */
            $where[]    = "(COALESCE(dtl.qtypo,0) + COALESCE(dtl.qtyvoid,0)) <> dtl.qty";
            $where[]    = "TRIM(pp.status) <> 'C'";
            $whereSql   = ' WHERE ' . implode(' AND ', $where);
            $bindData   = $bind;

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

            $judulLaporan = 'LAPORAN OUTSTANDING PP';
            $headers      = [
                'No. Dokumen','Tanggal','Cabang','Pemohon','ID Barang','Nama Barang',
                'Satuan','Qty','Qty PO','Qty Void','Qty Proses',
                'Keterangan','No. Capex','Keterangan PP'
            ];
        } else {
            /* ---- HISTORY (UNION PP + PO) ---- */
            $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

            $unionSql = "
            SELECT
                TRIM(dtl.docno)                     AS docno_pp,
                ''                                  AS jurnal,
                TO_CHAR(pp.docdate, 'DD-MM-YYYY')   AS docdate,
                TRIM(pp.inputby)                    AS userid,
                TRIM(dtl.idbarang)                  AS idbarang,
                dtl.qty                             AS qty,
                0                                   AS qty_realisasi,
                TRIM(pp.pemohon)                    AS nama_user,
                TRIM(dtl.nmbarang)                  AS nmbarang,
                TRIM(dtl.unit)                      AS unit,
                dtl.description                     AS spec,
                pp.keterangan                       AS ket_pp_header,
                dtl.description                     AS ket_detail,
                TO_CHAR(pp.estpakai, 'DD-MM-YYYY')  AS tglpakai,
                TRIM(pp.cabang)                     AS job,
                1                                   AS src_order,
                CASE
                    WHEN TRIM(pp.status) = 'C'                                 THEN 'CANCEL'
                    WHEN COALESCE(agg.total_po, 0) >= dtl.qty                  THEN 'FINISH'
                    ELSE                                                            'OUTSTANDING'
                END                                 AS status_outstanding,
                COALESCE(agg.total_po, 0)           AS total_realisasi
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
            LEFT JOIN (
                SELECT TRIM(docnopp) AS docnopp,
                    TRIM(idbarang) AS idbarang,
                    SUM(qty) AS total_po
                FROM sc_trx.po_dtl
                GROUP BY TRIM(docnopp), TRIM(idbarang)
            ) agg
                ON  agg.docnopp  = TRIM(dtl.docno)
                AND agg.idbarang = TRIM(dtl.idbarang)
            $whereSql

            UNION ALL

            SELECT
                TRIM(dtl.docno)                     AS docno_pp,
                TRIM(pod.docno)                     AS jurnal,
                TO_CHAR(pp.docdate, 'DD-MM-YYYY')   AS docdate,
                TRIM(pp.inputby)                    AS userid,
                TRIM(dtl.idbarang)                  AS idbarang,
                0                                   AS qty,
                pod.qty                             AS qty_realisasi,
                TRIM(pp.pemohon)                    AS nama_user,
                TRIM(dtl.nmbarang)                  AS nmbarang,
                TRIM(dtl.unit)                      AS unit,
                dtl.description                     AS spec,
                pp.keterangan                       AS ket_pp_header,
                dtl.description                     AS ket_detail,
                TO_CHAR(pp.estpakai, 'DD-MM-YYYY')  AS tglpakai,
                TRIM(pp.cabang)                     AS job,
                2                                   AS src_order,
                CASE
                    WHEN TRIM(pp.status) = 'C'                                 THEN 'CANCEL'
                    WHEN COALESCE(agg.total_po, 0) >= dtl.qty                  THEN 'FINISH'
                    ELSE                                                            'OUTSTANDING'
                END                                 AS status_outstanding,
                COALESCE(agg.total_po, 0)           AS total_realisasi
            FROM sc_trx.pp_dtl dtl
            INNER JOIN sc_trx.pp pp ON TRIM(pp.docno) = TRIM(dtl.docno)
            INNER JOIN sc_trx.po_dtl pod
                ON  TRIM(pod.docnopp)  = TRIM(dtl.docno)
                AND TRIM(pod.idbarang) = TRIM(dtl.idbarang)
            LEFT JOIN (
                SELECT TRIM(docnopp) AS docnopp,
                    TRIM(idbarang) AS idbarang,
                    SUM(qty) AS total_po
                FROM sc_trx.po_dtl
                GROUP BY TRIM(docnopp), TRIM(idbarang)
            ) agg
                ON  agg.docnopp  = TRIM(dtl.docno)
                AND agg.idbarang = TRIM(dtl.idbarang)
            $whereSql
        ";

        $sql      = "SELECT docno_pp, jurnal, docdate, userid, idbarang, qty, qty_realisasi,
                            nama_user, nmbarang, unit, spec, ket_pp_header, ket_detail,
                            tglpakai, job, status_outstanding
                    FROM ($unionSql) z
                    ORDER BY docdate DESC, docno_pp, idbarang, src_order, jurnal";
        $bindData = array_merge($bind, $bind);

        $judulLaporan = 'LAPORAN PP HISTORY';
        $headers = [
            'No. PP','Jurnal','Tanggal','UserID','Kode Barang','Qty',
            'Qty Realisasi','Nama User','Nama Barang','Satuan','Spec',
            'Keterangan PP Header','Keterangan Detail','Tanggal Pakai','Job','Status'
        ];
        }

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bindData);
        if ($query === false) {
            log_message('error', 'Laporan PP query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $colCount = count($headers);
        $lastCol  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum      = 4;
        $rowCount    = 0;
        $sampleWidth = array_fill(0, $colCount, 0);
        $sampleMax   = 100;
        $sampleCount = 0;

        while ($r = $query->getUnbufferedRow('array')) {
            $rowData = array_values($r);
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

        $colLetter = 'A';
        foreach ($headers as $i => $h) {
            $width = max(mb_strlen($h), $sampleWidth[$i]) + 2;
            if ($width > 40) $width = 40;
            if ($width < 8)  $width = 8;
            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colLetter++;
        }

        $sheet->freezePane('A4');

        $namaFile = 'Laporan_PP_' . ucfirst($jenis) . '_' . date('Ymd_His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');

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
        $jenis    = $this->request->getPost('jenisLaporan');   // outstanding_po | history_po
        $tglrange = $this->request->getPost('tglrange');
        $docno    = $this->request->getPost('docno');
        $kdsupp   = $this->request->getPost('kdsupplier');
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
                $where[] = "po.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(pod.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupp)) {
            $where[] = "TRIM(po.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupp));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(po.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        /* =========================================================
        OUTSTANDING PO — logika lama (tidak diubah)
        ========================================================= */
        if ($jenis !== 'history_po') {
            $whereOut   = $where;
            $whereOut[] = "COALESCE(pod.qtylpb,0) < pod.qty";
            $whereOut[] = "TRIM(po.status) <> 'C'";
            $whereSql   = ' WHERE ' . implode(' AND ', $whereOut);

            /* --- COUNT --- */
            $sqlCount = "
                SELECT COUNT(*) AS total
                FROM sc_trx.po_dtl pod
                INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(pod.docno)
                $whereSql
            ";
            $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
            $total     = (int)($totalRow['total'] ?? 0);
            $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

            /* --- DATA (sesuaikan dengan query outspp versi Anda) --- */
            $sqlData = "
                SELECT
                    TRIM(pod.docno)                       AS docno,
                    TO_CHAR(po.docdate, 'DD-MM-YYYY')     AS docdate,
                    TRIM(po.cabang)                       AS cabang,
                    TRIM(po.kdsupplier)                   AS kdsupplier,
                    TRIM(po.nmsupplier)                   AS nmsupplier,
                    TRIM(po.pemohon)                      AS pemohon,
                    TRIM(pod.idbarang)                    AS idbarang,
                    TRIM(pod.nmbarang)                    AS nmbarang,
                    TRIM(pod.unit)                        AS unit,
                    pod.qty                               AS qty,
                    pod.qtybonus                          AS qtybonus,
                    pod.qtylpb                            AS qtylpb,
                    pod.qtyvoid                           AS qtyvoid,
                    (COALESCE(pod.qtylpb,0) + COALESCE(pod.qtyvoid,0)) AS qty_proses,
                    pod.harga                             AS harga,
                    pod.nilai                             AS nilai,
                    pod.multidisc                         AS multidisc,
                    pod.totaldiscount                     AS totaldiscount,
                    pod.nilaipajak                        AS nilaipajak,
                    pod.descriptionpo                     AS descriptionpo,
                    pod.descriptionpp                     AS descriptionpp,
                    TRIM(pod.status)                      AS status_dtl,
                    TRIM(po.status)                       AS status_po,
                    po.keterangan                         AS keterangan_po
                FROM sc_trx.po_dtl pod
                INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(pod.docno)
                $whereSql
                ORDER BY po.docdate DESC, pod.docno, pod.idurut
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
        HISTORY PO — UNION PO Detail + LPB Detail
        ========================================================= */
        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        $unionSql = "
            SELECT
                TRIM(pod.docno)                         AS docno_po,
                ''                                      AS jurnal,
                TO_CHAR(po.docdate, 'DD-MM-YYYY')       AS docdate,
                TRIM(po.kdsupplier)                     AS kdsupplier,
                TRIM(po.nmsupplier)                     AS nmsupplier,
                TRIM(pod.idbarang)                      AS idbarang,
                TRIM(pod.nmbarang)                      AS nmbarang,
                pod.qty                                 AS qty,
                0                                       AS qty_realisasi,
                TRIM(pod.unit)                          AS unit,
                pod.nilai                               AS nilai,
                TRIM(po.cabang)                         AS job,
                TRIM(po.cabang)                         AS nama_job,
                pod.harga                               AS harga_po,
                pod.descriptionpp                       AS ket_item_pp,
                TO_CHAR(po.senddate, 'DD-MM-YYYY')      AS tgl_kirim,
                1                                       AS src_order,
                CASE
                    WHEN TRIM(po.status) = 'C'                                 THEN 'CANCEL'
                    WHEN COALESCE(agg.total_lpb, 0) >= pod.qty                 THEN 'FINISH'
                    ELSE                                                            'OUTSTANDING'
                END                                     AS status_outstanding,
                COALESCE(agg.total_lpb, 0)              AS total_realisasi
            FROM sc_trx.po_dtl pod
            INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(pod.docno)
            LEFT JOIN (
                SELECT TRIM(uniqueid) AS uniqueid, SUM(qty) AS total_lpb
                FROM sc_trx.lpb_dtl
                GROUP BY TRIM(uniqueid)
            ) agg ON agg.uniqueid = TRIM(pod.uniqueid)
            $whereSql

            UNION ALL

            SELECT
                TRIM(pod.docno)                         AS docno_po,
                TRIM(lpb.docno)                         AS jurnal,
                TO_CHAR(po.docdate, 'DD-MM-YYYY')       AS docdate,
                TRIM(po.kdsupplier)                     AS kdsupplier,
                TRIM(po.nmsupplier)                     AS nmsupplier,
                TRIM(pod.idbarang)                      AS idbarang,
                TRIM(pod.nmbarang)                      AS nmbarang,
                0                                       AS qty,
                lpb.qty                                 AS qty_realisasi,
                TRIM(pod.unit)                          AS unit,
                pod.nilai                               AS nilai,
                TRIM(po.cabang)                         AS job,
                TRIM(po.cabang)                         AS nama_job,
                pod.harga                               AS harga_po,
                pod.descriptionpp                       AS ket_item_pp,
                TO_CHAR(po.senddate, 'DD-MM-YYYY')      AS tgl_kirim,
                2                                       AS src_order,
                CASE
                    WHEN TRIM(po.status) = 'C'                                 THEN 'CANCEL'
                    WHEN COALESCE(agg.total_lpb, 0) >= pod.qty                 THEN 'FINISH'
                    ELSE                                                            'OUTSTANDING'
                END                                     AS status_outstanding,
                COALESCE(agg.total_lpb, 0)              AS total_realisasi
            FROM sc_trx.po_dtl pod
            INNER JOIN sc_trx.po po       ON TRIM(po.docno)     = TRIM(pod.docno)
            INNER JOIN sc_trx.lpb_dtl lpb ON TRIM(lpb.uniqueid) = TRIM(pod.uniqueid)
            LEFT JOIN (
                SELECT TRIM(uniqueid) AS uniqueid, SUM(qty) AS total_lpb
                FROM sc_trx.lpb_dtl
                GROUP BY TRIM(uniqueid)
            ) agg ON agg.uniqueid = TRIM(pod.uniqueid)
            $whereSql
        ";

        /* --- COUNT --- */
        $sqlCount  = "SELECT COUNT(*) AS total FROM ($unionSql) x";
        $bindCount = array_merge($bind, $bind);
        $totalRow  = $this->db->query($sqlCount, $bindCount)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* --- DATA --- */
        $sqlData = "SELECT * FROM ($unionSql) z
                    ORDER BY docdate DESC, docno_po, idbarang, src_order, jurnal
                    LIMIT ? OFFSET ?";
        $bindData   = array_merge($bind, $bind);
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
        $jenis    = $this->request->getPost('jenisLaporan');
        $tglrange = $this->request->getPost('tglrange');
        $docno    = $this->request->getPost('docno');
        $kdsupp   = $this->request->getPost('kdsupplier');
        $cabang   = $this->request->getPost('cabang');

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
            $where[] = "TRIM(pod.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupp)) {
            $where[] = "TRIM(po.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupp));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(po.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        /* =========================================================
        TENTUKAN QUERY & HEADER
        ========================================================= */
        if ($jenis !== 'history_po') {
            /* ---- OUTSTANDING PO (logika lama) ---- */
            $where[]    = "COALESCE(pod.qtylpb,0) < pod.qty";
            $where[]    = "TRIM(po.status) <> 'C'";
            $whereSql   = ' WHERE ' . implode(' AND ', $where);
            $bindData   = $bind;

            $sql = "
                SELECT
                    TRIM(pod.docno)                       AS docno,
                    TO_CHAR(po.docdate, 'DD-MM-YYYY')     AS docdate,
                    TRIM(po.cabang)                       AS cabang,
                    TRIM(po.kdsupplier)                   AS kdsupplier,
                    TRIM(po.nmsupplier)                   AS nmsupplier,
                    TRIM(po.pemohon)                      AS pemohon,
                    TRIM(pod.idbarang)                    AS idbarang,
                    TRIM(pod.nmbarang)                    AS nmbarang,
                    TRIM(pod.unit)                        AS unit,
                    pod.qty                               AS qty,
                    pod.qtybonus                          AS qtybonus,
                    pod.qtylpb                            AS qtylpb,
                    pod.qtyvoid                           AS qtyvoid,
                    (COALESCE(pod.qtylpb,0) + COALESCE(pod.qtyvoid,0)) AS qty_proses,
                    pod.harga                             AS harga,
                    pod.nilai                             AS nilai,
                    pod.multidisc                         AS multidisc,
                    pod.totaldiscount                     AS totaldiscount,
                    pod.nilaipajak                        AS nilaipajak,
                    pod.descriptionpo                     AS descriptionpo,
                    pod.descriptionpp                     AS descriptionpp,
                    TRIM(pod.status)                      AS status_dtl,
                    TRIM(po.status)                       AS status_po,
                    po.keterangan                         AS keterangan_po
                FROM sc_trx.po_dtl pod
                INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(pod.docno)
                $whereSql
                ORDER BY po.docdate DESC, pod.docno, pod.idurut
            ";

            $judulLaporan = 'LAPORAN OUTSTANDING PO';
            $headers      = [
                'No. Dokumen','Tanggal','Cabang','Kode Supplier','Nama Supplier','Pemohon',
                'ID Barang','Nama Barang','Satuan','Qty','Qty Bonus','Qty LPB','Qty Void',
                'Qty Proses','Harga','Nilai','Multidisc','Total Discount','Nilai Pajak',
                'Description PO','Description PP','Status Dtl','Status PO','Keterangan PO'
            ];
        } else {
            /* ---- HISTORY PO (UNION PO + LPB) ---- */
            $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

            $unionSql = "
                SELECT
                    TRIM(pod.docno)                         AS docno_po,
                    ''                                      AS jurnal,
                    TO_CHAR(po.docdate, 'DD-MM-YYYY')       AS docdate,
                    TRIM(po.kdsupplier)                     AS kdsupplier,
                    TRIM(po.nmsupplier)                     AS nmsupplier,
                    TRIM(pod.idbarang)                      AS idbarang,
                    TRIM(pod.nmbarang)                      AS nmbarang,
                    pod.qty                                 AS qty,
                    0                                       AS qty_realisasi,
                    TRIM(pod.unit)                          AS unit,
                    pod.nilai                               AS nilai,
                    TRIM(po.cabang)                         AS job,
                    TRIM(po.cabang)                         AS nama_job,
                    pod.harga                               AS harga_po,
                    pod.descriptionpp                       AS ket_item_pp,
                    TO_CHAR(po.senddate, 'DD-MM-YYYY')      AS tgl_kirim,
                    1                                       AS src_order,
                    CASE
                        WHEN TRIM(po.status) = 'C'                                 THEN 'CANCEL'
                        WHEN COALESCE(agg.total_lpb, 0) >= pod.qty                 THEN 'FINISH'
                        ELSE                                                            'OUTSTANDING'
                    END                                     AS status_outstanding,
                    COALESCE(agg.total_lpb, 0)              AS total_realisasi
                FROM sc_trx.po_dtl pod
                INNER JOIN sc_trx.po po ON TRIM(po.docno) = TRIM(pod.docno)
                LEFT JOIN (
                    SELECT TRIM(uniqueid) AS uniqueid, SUM(qty) AS total_lpb
                    FROM sc_trx.lpb_dtl
                    GROUP BY TRIM(uniqueid)
                ) agg ON agg.uniqueid = TRIM(pod.uniqueid)
                $whereSql

                UNION ALL

                SELECT
                    TRIM(pod.docno)                         AS docno_po,
                    TRIM(lpb.docno)                         AS jurnal,
                    TO_CHAR(po.docdate, 'DD-MM-YYYY')       AS docdate,
                    TRIM(po.kdsupplier)                     AS kdsupplier,
                    TRIM(po.nmsupplier)                     AS nmsupplier,
                    TRIM(pod.idbarang)                      AS idbarang,
                    TRIM(pod.nmbarang)                      AS nmbarang,
                    0                                       AS qty,
                    lpb.qty                                 AS qty_realisasi,
                    TRIM(pod.unit)                          AS unit,
                    pod.nilai                               AS nilai,
                    TRIM(po.cabang)                         AS job,
                    TRIM(po.cabang)                         AS nama_job,
                    pod.harga                               AS harga_po,
                    pod.descriptionpp                       AS ket_item_pp,
                    TO_CHAR(po.senddate, 'DD-MM-YYYY')      AS tgl_kirim,
                    2                                       AS src_order,
                    CASE
                        WHEN TRIM(po.status) = 'C'                                 THEN 'CANCEL'
                        WHEN COALESCE(agg.total_lpb, 0) >= pod.qty                 THEN 'FINISH'
                        ELSE                                                            'OUTSTANDING'
                    END                                     AS status_outstanding,
                    COALESCE(agg.total_lpb, 0)              AS total_realisasi
                FROM sc_trx.po_dtl pod
                INNER JOIN sc_trx.po po       ON TRIM(po.docno)     = TRIM(pod.docno)
                INNER JOIN sc_trx.lpb_dtl lpb ON TRIM(lpb.uniqueid) = TRIM(pod.uniqueid)
                LEFT JOIN (
                    SELECT TRIM(uniqueid) AS uniqueid, SUM(qty) AS total_lpb
                    FROM sc_trx.lpb_dtl
                    GROUP BY TRIM(uniqueid)
                ) agg ON agg.uniqueid = TRIM(pod.uniqueid)
                $whereSql
            ";

            $sql      = "SELECT docno_po, jurnal, docdate, kdsupplier, nmsupplier, idbarang,
                                nmbarang, qty, qty_realisasi, unit, nilai, job, nama_job,
                                harga_po, ket_item_pp, tgl_kirim, status_outstanding
                        FROM ($unionSql) z
                        ORDER BY docdate DESC, docno_po, idbarang, src_order, jurnal";
            $bindData = array_merge($bind, $bind);

            $judulLaporan = 'LAPORAN PO HISTORY';
            $headers      = [
                'No. PO','Jurnal','Tanggal','Kode Supplier','Nama Supplier','Kode Barang',
                'Nama Barang','Qty','Qty Realisasi','Satuan','Nilai','Job','Nama Job',
                'Harga PO','Keterangan Item PP','Tgl Kirim','Status'
            ];
        }

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bindData);
        if ($query === false) {
            log_message('error', 'Laporan PO query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $colCount = count($headers);
        $lastCol  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum      = 4;
        $rowCount    = 0;
        $sampleWidth = array_fill(0, $colCount, 0);
        $sampleMax   = 100;
        $sampleCount = 0;

        while ($r = $query->getUnbufferedRow('array')) {
            $rowData = array_values($r);
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

        $colLetter = 'A';
        foreach ($headers as $i => $h) {
            $width = max(mb_strlen($h), $sampleWidth[$i]) + 2;
            if ($width > 40) $width = 40;
            if ($width < 8)  $width = 8;
            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colLetter++;
        }

        $sheet->freezePane('A4');

        $namaFile = 'Laporan_PO_' . ucfirst($jenis) . '_' . date('Ymd_His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

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


    

    /* =========================================================
    LAPORAN SALES ORDER
    ========================================================= */
    public function salesorder()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Sales Order";
        $data['jenisLaporan'] = 'salesorder';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_salesorder', $data);
    }

    /* =========================================================
    PREVIEW DATA SALES ORDER — AJAX pagination
    ========================================================= */
    public function previewLaporanSalesOrder()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdcustomer = $this->request->getPost('kdcustomer');
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
                $where[] = "so.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdcustomer)) {
            $where[] = "TRIM(so.kdcustomer) = ?";
            $bind[]  = trim(strtoupper($kdcustomer));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(so.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT ====== */
        $sqlCount = "
            SELECT COUNT(*) AS total
            FROM sc_trx.salesorder_dtl dtl
            INNER JOIN sc_trx.salesorder so ON TRIM(so.docno) = TRIM(dtl.docno)
            $whereSql
        ";
        $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== DATA HALAMAN INI ====== */
        $sqlData = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(so.docdate, 'DD-MM-YYYY')     AS docdate,

                TRIM(so.kdcustomer)                   AS kdcustomer,
                TRIM(c.nmcustomer)                    AS nmcustomer,
                TRIM(c.alamat_kantor)                 AS alamatcustomer,

                TRIM(kel.namakeldesa)                 AS desa,
                TRIM(kec.namakec)                     AS kecamatan,
                TRIM(kot.namakotakab)                 AS nmkota,

                TRIM(so.kdsalesman)                   AS kdsalesman,
                TRIM(s.nmsalesman)                    AS nmsalesman,

                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                dtl.qty                               AS qty,
                TRIM(dtl.unit)                        AS unit,
                dtl.harga                             AS harga,
                (dtl.qty * dtl.harga)                 AS nilaibruto,
                dtl.multidisc                         AS nilaidisc,
                dtl.nilaipajak                        AS nilaipajak,
                dtl.nilai                             AS nilai,

                TRIM(b.idbranch)                      AS job,
                TRIM(b.nmbranch)                      AS namajob
            FROM sc_trx.salesorder_dtl dtl
            INNER JOIN sc_trx.salesorder so ON TRIM(so.docno) = TRIM(dtl.docno)
            LEFT JOIN sc_mst.customer c     ON TRIM(c.kdcustomer) = TRIM(so.kdcustomer)
            LEFT JOIN sc_mst.salesman s     ON TRIM(s.kdsalesman) = TRIM(so.kdsalesman)
            LEFT JOIN sc_mst.kotakab kot    ON TRIM(kot.kodekotakab) = TRIM(c.kota_kantor)
            LEFT JOIN sc_mst.kec kec        ON TRIM(kec.kodekec) = TRIM(c.kec_kantor)
            LEFT JOIN sc_mst.keldesa kel    ON TRIM(kel.kodekeldesa) = TRIM(c.kel_kantor)
            LEFT JOIN sc_mst.branchjob b    ON TRIM(b.idbranch) = TRIM(so.cabang)
            $whereSql
            ORDER BY so.docdate DESC, dtl.docno, dtl.idurut
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
    DOWNLOAD EXCEL — SALES ORDER
    ========================================================= */
    public function downloadLaporanSalesOrder()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdcustomer = $this->request->getPost('kdcustomer');
        $cabang     = $this->request->getPost('cabang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "so.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdcustomer)) {
            $where[] = "TRIM(so.kdcustomer) = ?";
            $bind[]  = trim(strtoupper($kdcustomer));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(so.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL ====== */
        $sql = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(so.docdate, 'DD-MM-YYYY')     AS docdate,

                TRIM(so.kdcustomer)                   AS kdcustomer,
                TRIM(c.nmcustomer)                    AS nmcustomer,
                TRIM(c.alamat_kantor)                 AS alamatcustomer,

                TRIM(kel.namakeldesa)                 AS desa,
                TRIM(kec.namakec)                     AS kecamatan,
                TRIM(kot.namakotakab)                 AS nmkota,

                TRIM(so.kdsalesman)                   AS kdsalesman,
                TRIM(s.nmsalesman)                    AS nmsalesman,

                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                dtl.qty                               AS qty,
                TRIM(dtl.unit)                        AS unit,
                dtl.harga                             AS harga,
                (dtl.qty * dtl.harga)                 AS nilaibruto,
                dtl.multidisc                         AS nilaidisc,
                dtl.nilaipajak                        AS nilaipajak,
                dtl.nilai                             AS nilai,

                TRIM(b.idbranch)                      AS job,
                TRIM(b.nmbranch)                      AS namajob
            FROM sc_trx.salesorder_dtl dtl
            INNER JOIN sc_trx.salesorder so ON TRIM(so.docno) = TRIM(dtl.docno)
            LEFT JOIN sc_mst.customer c     ON TRIM(c.kdcustomer) = TRIM(so.kdcustomer)
            LEFT JOIN sc_mst.salesman s     ON TRIM(s.kdsalesman) = TRIM(so.kdsalesman)
            LEFT JOIN sc_mst.kotakab kot    ON TRIM(kot.kodekotakab) = TRIM(c.kota_kantor)
            LEFT JOIN sc_mst.kec kec        ON TRIM(kec.kodekec) = TRIM(c.kec_kantor)
            LEFT JOIN sc_mst.keldesa kel    ON TRIM(kel.kodekeldesa) = TRIM(c.kel_kantor)
            LEFT JOIN sc_mst.branchjob b    ON TRIM(b.idbranch) = TRIM(so.cabang)
            $whereSql
            ORDER BY so.docdate DESC, dtl.docno, dtl.idurut
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan SO query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = 'LAPORAN SALES ORDER';

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:U1');   // 21 kolom (A..U)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* --- Header: 21 kolom --- */
        $headers = [
            'No. SO', 'Tanggal', 'Kode Customer', 'Nama Customer', 'Alamat Customer',
            'Desa', 'Kecamatan', 'Kota Customer', 'Kode Salesman', 'Nama Salesman',
            'Kode Barang', 'Nama Barang', 'Qty', 'Satuan', 'Harga',
            'Nilai Bruto', 'Nilai Disc', 'Nilai Pajak', 'Nilai', 'Job', 'Nama Job'
        ]; // 21 kolom ✅
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'U';
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
                $r['docno'], $r['docdate'], $r['kdcustomer'], $r['nmcustomer'], $r['alamatcustomer'],
                $r['desa'], $r['kecamatan'], $r['nmkota'], $r['kdsalesman'], $r['nmsalesman'],
                $r['idbarang'], $r['nmbarang'], $r['qty'], $r['unit'], $r['harga'],
                $r['nilaibruto'], $r['nilaidisc'], $r['nilaipajak'], $r['nilai'],
                $r['job'], $r['namajob']
            ];

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
        // Kolom M..S = Qty, Harga, Nilai Bruto, Nilai Disc, Nilai Pajak, Nilai
        $firstDataRow = 4;
        $lastDataRow  = max(4, $rowNum - 1);
        foreach (['M','O','P','Q','R','S'] as $col) {
            $sheet->getStyle("{$col}{$firstDataRow}:{$col}{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_SalesOrder_' . date('Ymd_His') . '.xlsx';

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
    LAPORAN PENJUALAN
    ========================================================= */
    public function penjualan()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Penjualan";
        $data['jenisLaporan'] = 'penjualan';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_penjualan', $data);
    }

    /* =========================================================
    PREVIEW DATA PENJUALAN — AJAX pagination
    ========================================================= */
    public function previewLaporanPenjualan()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdcustomer = $this->request->getPost('kdcustomer');
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
                $where[] = "pj.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdcustomer)) {
            $where[] = "TRIM(pj.kdcustomer) = ?";
            $bind[]  = trim(strtoupper($kdcustomer));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(pj.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT ====== */
        $sqlCount = "
            SELECT COUNT(*) AS total
            FROM sc_trx.penjualan_dtl dtl
            INNER JOIN sc_trx.penjualan pj ON TRIM(pj.docno) = TRIM(dtl.docno)
            $whereSql
        ";
        $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== DATA HALAMAN INI ====== */
        $sqlData = "
                    SELECT
            TRIM(dtl.docno)                       AS docno,
            TO_CHAR(pj.docdate, 'DD-MM-YYYY')     AS docdate,
            TO_CHAR(pj.docdate + COALESCE(pj.jthtempo,0)::int, 'DD-MM-YYYY') AS tgljt,
            TRIM(pj.currcode)                     AS currcode,
            pj.kurs                               AS kurs,

            TRIM(pj.kdcustomer)                   AS kdcustomer,
            TRIM(c.nmcustomer)                    AS nmcustomer,
            TRIM(c.alamat_kantor)                 AS alamatcustomer,
            TRIM(kot.namakotakab)                 AS nmkota,

            TRIM(pj.kdsalesman)                   AS kdsalesman,
            TRIM(s.nmsalesman)                    AS nmsalesman,

            TRIM(dtl.idbarang)                    AS idbarang,
            TRIM(dtl.nmbarang)                    AS nmbarang,
            dtl.qty                               AS qty,
            0                                     AS qtybonus,
            0                                     AS nilaibonus,
            (dtl.qty * dtl.harga)                 AS nilaibruto,
            dtl.multidisc                         AS nilaidisc,
            dtl.nilaipajak                        AS nilaipajak,

            TRIM(pj.cabang)                       AS job,
            TRIM(b.nmbranch)                      AS namajob,
            TRIM(dtl.unit)                        AS unit,

            ''                                    AS npwp,
            TRIM(mk.nmmarket)                     AS market,
            ''                                    AS jenismarket,
            TRIM(prov.namaprov)                   AS region,

            TRIM(dtl.idgudang)                    AS idgudang,
            ''                                    AS nmgudang,
            TRIM(dtl.idspec)                      AS idspec,
            NULL                                  AS expdate,
            TRIM(dtl.description)                 AS keteranganbarang,
            TRIM(dtl.bomdesc)                     AS specbarang,
            TRIM(dtl.idprincipal)                 AS idprincipal,
            ''                                    AS nmprincipal,
            ''                                    AS golongan,
            ''                                    AS jenisproduk,
            ''                                    AS kelompokbarang,

            TRIM(kel.namakeldesa)                 AS desa,
            TRIM(kec.namakec)                     AS kecamatan,
            TRIM(prov.namaprov)                   AS wilayah,
            TRIM(pj.keterangan)                   AS keteranganpenjualan
        FROM sc_trx.penjualan_dtl dtl
        INNER JOIN sc_trx.penjualan pj ON TRIM(pj.docno) = TRIM(dtl.docno)
        LEFT JOIN sc_mst.customer c    ON TRIM(c.kdcustomer) = TRIM(pj.kdcustomer)
        LEFT JOIN sc_mst.salesman s    ON TRIM(s.kdsalesman) = TRIM(pj.kdsalesman)
        LEFT JOIN sc_mst.branchjob b   ON TRIM(b.idbranch) = TRIM(pj.cabang)
        LEFT JOIN sc_mst.provinsi prov ON TRIM(prov.kodeprov) = TRIM(c.provinsi_kantor)
        LEFT JOIN sc_mst.kotakab kot   ON TRIM(kot.kodekotakab) = TRIM(c.kota_kantor)
        LEFT JOIN sc_mst.kec kec       ON TRIM(kec.kodekec) = TRIM(c.kec_kantor)
        LEFT JOIN sc_mst.keldesa kel   ON TRIM(kel.kodekeldesa) = TRIM(c.kel_kantor)
        LEFT JOIN sc_mst.market mk     ON TRIM(mk.idmarket) = TRIM(c.idmarket)
        $whereSql
        ORDER BY pj.docdate DESC, dtl.docno, dtl.idurut
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
    DOWNLOAD EXCEL — PENJUALAN
    ========================================================= */
    public function downloadLaporanPenjualan()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdcustomer = $this->request->getPost('kdcustomer');
        $cabang     = $this->request->getPost('cabang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "pj.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(dtl.docno) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdcustomer)) {
            $where[] = "TRIM(pj.kdcustomer) = ?";
            $bind[]  = trim(strtoupper($kdcustomer));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(pj.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL ====== */
        $sql = "
            SELECT
                TRIM(dtl.docno)                       AS docno,
                TO_CHAR(pj.docdate, 'DD-MM-YYYY')     AS docdate,
                TO_CHAR(pj.docdate + COALESCE(pj.jthtempo,0)::int, 'DD-MM-YYYY') AS tgljt,
                TRIM(pj.currcode)                     AS currcode,
                pj.kurs                               AS kurs,

                TRIM(pj.kdcustomer)                   AS kdcustomer,
                TRIM(c.nmcustomer)                    AS nmcustomer,
                TRIM(c.alamat_kantor)                 AS alamatcustomer,
                TRIM(kot.namakotakab)                 AS nmkota,

                TRIM(pj.kdsalesman)                   AS kdsalesman,
                TRIM(s.nmsalesman)                    AS nmsalesman,

                TRIM(dtl.idbarang)                    AS idbarang,
                TRIM(dtl.nmbarang)                    AS nmbarang,
                dtl.qty                               AS qty,
                dtl.qtybonus                          AS qtybonus,
                (dtl.qtybonus * dtl.harga)            AS nilaibonus,
                (dtl.qty * dtl.harga)                 AS nilaibruto,
                dtl.multidisc                         AS nilaidisc,
                dtl.nilaipajak                        AS nilaipajak,

                TRIM(pj.cabang)                       AS job,
                TRIM(b.nmbranch)                      AS namajob,
                TRIM(dtl.unit)                        AS unit,

                ''                                    AS npwp,
                TRIM(mk.nmmarket)                     AS market,
                ''                                    AS jenismarket,
                TRIM(prov.namaprov)                   AS region,
                TRIM(dtl.idgudang)                    AS idgudang,
                ''                                    AS nmgudang,
                TRIM(dtl.idspec)                      AS idspec,
                NULL                                  AS expdate,
                TRIM(dtl.description)                 AS keteranganbarang,
                TRIM(dtl.bomdesc)                     AS specbarang,
                TRIM(dtl.idprincipal)                 AS idprincipal,
                ''                                    AS nmprincipal,
                ''                                    AS golongan,
                ''                                    AS jenisproduk,
                ''                                    AS kelompokbarang,
                TRIM(kel.namakeldesa)                 AS desa,
                TRIM(kec.namakec)                     AS kecamatan,
                TRIM(prov.namaprov)                   AS wilayah,
                TRIM(pj.keterangan)                   AS keteranganpenjualan
            FROM sc_trx.penjualan_dtl dtl
            INNER JOIN sc_trx.penjualan pj ON TRIM(pj.docno) = TRIM(dtl.docno)
            LEFT JOIN sc_mst.customer c    ON TRIM(c.kdcustomer) = TRIM(pj.kdcustomer)
            LEFT JOIN sc_mst.salesman s    ON TRIM(s.kdsalesman) = TRIM(pj.kdsalesman)
            LEFT JOIN sc_mst.branchjob b   ON TRIM(b.idbranch) = TRIM(pj.cabang)
            LEFT JOIN sc_mst.provinsi prov ON TRIM(prov.kodeprov) = TRIM(c.provinsi_kantor)
            LEFT JOIN sc_mst.kotakab kot   ON TRIM(kot.kodekotakab) = TRIM(c.kota_kantor)
            LEFT JOIN sc_mst.kec kec       ON TRIM(kec.kodekec) = TRIM(c.kec_kantor)
            LEFT JOIN sc_mst.keldesa kel   ON TRIM(kel.kodekeldesa) = TRIM(c.kel_kantor)
            LEFT JOIN sc_mst.market mk     ON TRIM(mk.idmarket) = TRIM(c.idmarket)
            $whereSql
            ORDER BY pj.docdate DESC, dtl.docno, dtl.idurut
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan Penjualan query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = 'LAPORAN PENJUALAN';

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:AO1');   // 41 kolom (A..AO)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* --- Header: 41 kolom --- */
        $headers = [
            'No Invoice', 'Tanggal', 'Tgl JT', 'Mata Uang', 'Kurs',
            'Kode Customer', 'Nama Customer', 'Alamat Customer', 'Kota Customer',
            'Kode Salesman', 'Nama Salesman',
            'Kode Barang', 'Nama Barang', 'Qty', 'Qty Bonus', 'Nilai Bonus',
            'Nilai Bruto', 'Nilai Disc', 'Nilai Pajak',
            'Kode Job', 'Nama Job', 'Satuan', 'NPWP',
            'Market', 'Jenis Market', 'Region',
            'Kode Gudang', 'Nama Gudang', 'No.Batch/Spec', 'Expired Date',
            'Keterangan Barang', 'Spec Barang', 'Kode Principal', 'Nama Principal',
            'Golongan', 'Jenis Produk', 'Kelompok Barang',
            'Desa', 'Kecamatan', 'Wilayah', 'Keterangan Penjualan'
        ]; // 41 kolom ✅
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'AO';
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
                $r['docno'], $r['docdate'], $r['tgljt'], $r['currcode'], $r['kurs'],
                $r['kdcustomer'], $r['nmcustomer'], $r['alamatcustomer'], $r['nmkota'],
                $r['kdsalesman'], $r['nmsalesman'],
                $r['idbarang'], $r['nmbarang'], $r['qty'], $r['qtybonus'], $r['nilaibonus'],
                $r['nilaibruto'], $r['nilaidisc'], $r['nilaipajak'],
                $r['job'], $r['namajob'], $r['unit'], $r['npwp'],
                $r['market'], $r['jenismarket'], $r['region'],
                $r['idgudang'], $r['nmgudang'], $r['idspec'], $r['expdate'],
                $r['keteranganbarang'], $r['specbarang'], $r['idprincipal'], $r['nmprincipal'],
                $r['golongan'], $r['jenisproduk'], $r['kelompokbarang'],
                $r['desa'], $r['kecamatan'], $r['wilayah'], $r['keteranganpenjualan']
            ]; // 41 kolom ✅

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
        // E=Kurs, N=Qty, O=Qty Bonus, P=Nilai Bonus, Q=Nilai Bruto, R=Nilai Disc, S=Nilai Pajak
        $firstDataRow = 4;
        $lastDataRow  = max(4, $rowNum - 1);
        foreach (['E','N','O','P','Q','R','S'] as $col) {
            $sheet->getStyle("{$col}{$firstDataRow}:{$col}{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_Penjualan_' . date('Ymd_His') . '.xlsx';

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