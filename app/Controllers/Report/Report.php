<?php

namespace App\Controllers\Report;

use App\Controllers\BaseController;

class Report extends BaseController
{

    /* =========================================================
    LAPORAN MASTER BARANG
    ========================================================= */
    public function masterbarang()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Master Barang";
        $data['jenisLaporan'] = 'masterbarang';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_mbarang', $data);
    }

    /* =========================================================
    PREVIEW DATA MASTER BARANG — AJAX pagination
    ========================================================= */
    public function previewLaporanMasterBarang()
    {
        $idbarang = $this->request->getPost('idbarang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $offset = ($page - 1) * $perpage;

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($idbarang)) {
            $where[] = "TRIM(idbarang) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($idbarang)) . '%';
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== COUNT ====== */
        $sqlCount = "SELECT COUNT(*) AS total FROM sc_mst.mbarang $whereSql";
        $totalRow  = $this->db->query($sqlCount, $bind)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        /* ====== DATA HALAMAN INI ====== */
        $sqlData = "
            SELECT
                TRIM(idbarang)          AS idbarang,
                TRIM(nmbarang)          AS nmbarang,
                minstock                AS minstock,
                ppersediaan             AS ppersediaan,
                TRIM(unit)              AS unit,
                TRIM(description)       AS description,
                TRIM(discontinue)       AS discontinue,
                TRIM(inputby)           AS inputby,
                inputdate               AS inputdate,
                TRIM(idgroup)           AS idgroup,
                berat                   AS berat,
                lsize                   AS lsize,
                psize                   AS psize,
                tsize                   AS tsize,
                TRIM(idtype)            AS idtype,
                TRIM(idgolonganbarang)  AS idgolonganbarang,
                TRIM(idjenisproduk)     AS idjenisproduk,
                TRIM(idkelompokbarang)  AS idkelompokbarang,
                ''                      AS launchingdate,
                TRIM(deflocation)       AS deflocation,
                TRIM(grade)             AS batch,
                TRIM(idprincipal)       AS idprincipal,
                0                       AS batasexpdate,
                ''                      AS fastmoving,
                ''                      AS withserialno,
                psj                      AS prksuratjalan,
                volume                  AS volume,
                TRIM(lokasireff)        AS lokasireff,
                ''                      AS satuandinkes,
                ''                      AS konversidinkes,
                '01'                    AS job,
                'Approval'              AS approval,
                ''                      AS tglapproved,
                ''                      AS approvedby,
                ''                      AS prkrevenue,
                ''                      AS prkhpp,
                ''                      AS prkproduksi,
                ''                      AS kategoribarang,
                gw                      AS gw,
                TRIM(kdtax)             AS kdtax,
                TRIM(satuantax)         AS satuantax
            FROM sc_mst.mbarang
            $whereSql
            ORDER BY idbarang
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
    DOWNLOAD EXCEL — MASTER BARANG
    ========================================================= */
    public function downloadLaporanMasterBarang()
    {
        $idbarang = $this->request->getPost('idbarang');

        /* ====== BUILD WHERE ====== */
        $where = []; $bind = [];

        if (!empty($idbarang)) {
            $where[] = "TRIM(idbarang) LIKE ?";
            $bind[]  = '%' . trim(strtoupper($idbarang)) . '%';
        }

        $whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

        /* ====== SQL ====== */
        $sql = "
            SELECT
                TRIM(idbarang)          AS idbarang,
                TRIM(nmbarang)          AS nmbarang,
                minstock                AS minstock,
                ppersediaan             AS ppersediaan,
                TRIM(unit)              AS unit,
                TRIM(description)       AS description,
                TRIM(discontinue)       AS discontinue,
                TRIM(inputby)           AS inputby,
                inputdate               AS inputdate,
                TRIM(idgroup)           AS idgroup,
                berat                   AS berat,
                lsize                   AS lsize,
                psize                   AS psize,
                tsize                   AS tsize,
                TRIM(idtype)            AS idtype,
                TRIM(idgolonganbarang)  AS idgolonganbarang,
                TRIM(idjenisproduk)     AS idjenisproduk,
                TRIM(idkelompokbarang)  AS idkelompokbarang,
                ''                      AS launchingdate,
                TRIM(deflocation)       AS deflocation,
                TRIM(grade)             AS batch,
                TRIM(idprincipal)       AS idprincipal,
                0                       AS batasexpdate,
                ''                      AS fastmoving,
                ''                      AS withserialno,
                psj                      AS prksuratjalan,
                volume                  AS volume,
                TRIM(lokasireff)        AS lokasireff,
                ''                      AS satuandinkes,
                ''                      AS konversidinkes,
                '01'                    AS job,
                'Approval'              AS approval,
                ''                      AS tglapproved,
                ''                      AS approvedby,
                ''                      AS prkrevenue,
                ''                      AS prkhpp,
                ''                      AS prkproduksi,
                ''                      AS kategoribarang,
                gw                      AS gw,
                TRIM(kdtax)             AS kdtax,
                TRIM(satuantax)         AS satuantax
            FROM sc_mst.mbarang
            $whereSql
            ORDER BY idbarang
        ";

        /* ====== SETUP ====== */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Laporan Master Barang query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        /* ====== EXCEL ====== */
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $judulLaporan = 'LAPORAN MASTER BARANG';

        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->mergeCells('A1:AO1');   // 41 kolom
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /* --- Header: 41 kolom --- */
        $headers = [
            'Kode', 'Nama', 'Qty Minimum', 'Prk Persediaan', 'Satuan', 'Keterangan',
            'Discontinue', 'Create By', 'Terakhir Simpan', 'Group_',
            'Berat', 'Panjang', 'Lebar', 'Tinggi',
            'Jenis Barang', 'Golongan', 'Jenis Produk', 'Kelompok Barang',
            'Launching Date', 'Gudang Default', 'Spec', 'Principal',
            'Batas Expired Date', 'Fast Moving', 'With Serial No.', 'Prk Surat Jalan',
            'Volume', 'Lokasi Reff', 'Satuan DinKes', 'Konversi Dinkes',
            'JOB', 'Approval', 'Tgl Approved', 'Approved by',
            'Prk Revenue', 'Prk HPP', 'Prk Produksi', 'Kategori Barang',
            'Gross Weight', 'Kode Barang Tax', 'Satuan Tax'
        ]; // 41 kolom
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
                $r['idbarang'], $r['nmbarang'], $r['minstock'], $r['ppersediaan'], $r['unit'], $r['description'],
                $r['discontinue'], $r['inputby'], $r['inputdate'], $r['idgroup'],
                $r['berat'], $r['lsize'], $r['psize'], $r['tsize'],
                $r['idtype'], $r['idgolonganbarang'], $r['idjenisproduk'], $r['idkelompokbarang'],
                $r['launchingdate'], $r['deflocation'], $r['batch'], $r['idprincipal'],
                $r['batasexpdate'], $r['fastmoving'], $r['withserialno'], $r['prksuratjalan'],
                $r['volume'], $r['lokasireff'], $r['satuandinkes'], $r['konversidinkes'],
                $r['job'], $r['approval'], $r['tglapproved'], $r['approvedby'],
                $r['prkrevenue'], $r['prkhpp'], $r['prkproduksi'], $r['kategoribarang'],
                $r['gw'], $r['kdtax'], $r['satuantax']
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
        // C=Qty Min, D=Prk Persediaan, K=Berat, L=Panjang, M=Lebar, N=Tinggi,
        // AA=Volume, AM=Gross Weight
        $firstDataRow = 4;
        $lastDataRow  = max(4, $rowNum - 1);
        foreach (['C','D','K','L','M','N','AA','AM'] as $col) {
            $sheet->getStyle("{$col}{$firstDataRow}:{$col}{$lastDataRow}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        /* ====== OUTPUT ====== */
        $namaFile = 'Laporan_MasterBarang_' . date('Ymd_His') . '.xlsx';

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
    LAPORAN MASTER CUSTOMER
    ========================================================= */
    public function mastercustomer()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Master Customer";
        $data['jenisLaporan'] = 'mastercustomer';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_mcustomer', $data);
    }

    private function buildQueryMasterCustomer()
    {
        $sql = "SELECT
                    TRIM(c.kdcustomer)              AS kdcustomer,
                    TRIM(c.nmcustomer)              AS nmcustomer,
                    TRIM(c.cp)                      AS cp,
                    TRIM(c.phone)                   AS phone,
                    TRIM(c.fax)                     AS fax,
                    TRIM(c.alamat_kantor)           AS alamat,
                    TRIM(c.kodepos_kantor)          AS kodepos,
                    TRIM(c.npwp)                    AS npwp,
                    TRIM(c.keterangan)              AS keterangan,
                    c.plafon                        AS plafon,
                    TRIM(k.namakotakab)             AS nmkota,
                    TRIM(m.nmmarket)                AS nmmarket,
                    TRIM(c.blacklist)               AS blacklist,
                    TRIM(c.namanpwp)                AS namanpwp,
                    TRIM(c.alamatnpwp)              AS alamatnpwp,
                    TRIM(kn.namakotakab)            AS nmkotanpwp,
                    TRIM(c.email)                   AS email,
                    TRIM(c.jabatan)                 AS jabatan,
                    TRIM(c.npkp)                    AS npkp,
                    TRIM(c.grade)                   AS grade,
                    TRIM(c.koderetur)               AS koderetur,
                    TRIM(c.salesman)                AS salesman,
                    TRIM(c.kolektor)                AS kolektor,
                    ''                              AS billto,
                    ''                              AS sendto,
                    TRIM(c.isfaktur)                AS statusfpj,
                    c.jthtempo                      AS jthtempo,
                    TRIM(c.idcoretax)               AS jenisidpembeli,
                    TRIM(c.idtku)                   AS idtkupembeli,
                    '' AS namapsa, '' AS emailpsa, '' AS telppsa, '' AS noijinpsa,
                    '' AS namaapoteker, '' AS kodestra, '' AS nosipa,
                    '' AS telpapoteker, '' AS emailapoteker, '' AS alamatapoteker,
                    '' AS masaberlakusipa, '' AS masaberlakupsa
                FROM sc_mst.customer c
                LEFT JOIN sc_mst.kotakab k  ON TRIM(k.kodekotakab)  = TRIM(c.kota_kantor)
                LEFT JOIN sc_mst.kotakab kn ON TRIM(kn.kodekotakab) = TRIM(c.idkotanpwp)
                LEFT JOIN sc_mst.market  m  ON TRIM(m.idmarket)     = TRIM(c.idmarket)";
        return $sql;
    }

    public function previewLaporanMasterCustomer()
    {
        $kdcustomer = $this->request->getPost('kdcustomer');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $where = []; $bind = [];
        if (!empty($kdcustomer)) {
            $where[] = "(TRIM(c.kdcustomer) ILIKE ? OR TRIM(c.nmcustomer) ILIKE ?)";
            $bind[]  = '%' . trim($kdcustomer) . '%';
            $bind[]  = '%' . trim($kdcustomer) . '%';
        }
        $whereSql = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        $sqlBase = $this->buildQueryMasterCustomer() . $whereSql;
        $offset  = ($page - 1) * $perpage;

        $totalRow = $this->db->query(
            "SELECT COUNT(*) AS total FROM (" . $sqlBase . ") x", $bind
        )->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        $rows = $this->db->query(
            $sqlBase . " ORDER BY kdcustomer LIMIT ? OFFSET ?",
            array_merge($bind, [$perpage, $offset])
        )->getResultArray();

        return $this->response->setJSON([
            'status' => 'ok', 'data' => $rows,
            'page' => $page, 'perpage' => $perpage,
            'total' => $total, 'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanMasterCustomer()
    {
        $kdcustomer = $this->request->getPost('kdcustomer');

        $where = []; $bind = [];
        if (!empty($kdcustomer)) {
            $where[] = "(TRIM(c.kdcustomer) ILIKE ? OR TRIM(c.nmcustomer) ILIKE ?)";
            $bind[]  = '%' . trim($kdcustomer) . '%';
            $bind[]  = '%' . trim($kdcustomer) . '%';
        }
        $whereSql = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
        $sql = $this->buildQueryMasterCustomer() . $whereSql . " ORDER BY kdcustomer";

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) exit('Query gagal.');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN MASTER CUSTOMER');
        $sheet->mergeCells('A1:AO1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'Kode', 'Nama', 'CP', 'Phone', 'Fax', 'Alamat', 'Kode Post', 'NPWP',
            'Keterangan', 'Max Plafon', 'Kota', 'Market', 'BlackList',
            'Nama NPWP', 'Alamat NPWP', 'Kota NPWP', 'Email', 'Jabatan', 'NPKP',
            'Grade', 'Kode Retur', 'Salesman', 'Kolektor', 'Bill To', 'Send To',
            'Status FPJ', 'Periode Jatuh Tempo', 'Jenis ID Pembeli', 'ID TKU Pembeli',
            'Nama PSA', 'Email PSA', 'Telp PSA', 'No.Ijin PSA',
            'Nama Apoteker', 'Kode STRA Apoteker', 'No.SIPA/SIKA Apoteker',
            'Telp Apoteker', 'Email Apoteker', 'Alamat Apoteker',
            'Masa Berlaku No.SIPA/SIKA', 'Masa Berlaku PSA'
        ];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:AO3')->getFont()->setBold(true);
        $sheet->getStyle('A3:AO3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4; $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['kdcustomer'], $r['nmcustomer'], $r['cp'], $r['phone'], $r['fax'],
                $r['alamat'], $r['kodepos'], $r['npwp'], $r['keterangan'], $r['plafon'],
                $r['nmkota'], $r['nmmarket'], $r['blacklist'],
                $r['namanpwp'], $r['alamatnpwp'], $r['nmkotanpwp'],
                $r['email'], $r['jabatan'], $r['npkp'], $r['grade'],
                $r['koderetur'], $r['salesman'], $r['kolektor'],
                $r['billto'], $r['sendto'], $r['statusfpj'], $r['jthtempo'],
                $r['jenisidpembeli'], $r['idtkupembeli'],
                $r['namapsa'], $r['emailpsa'], $r['telppsa'], $r['noijinpsa'],
                $r['namaapoteker'], $r['kodestra'], $r['nosipa'],
                $r['telpapoteker'], $r['emailapoteker'], $r['alamatapoteker'],
                $r['masaberlakusipa'], $r['masaberlakupsa']
            ], null, 'A' . $rowNum);
            $rowNum++; $rowCount++;
        }
        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:AO4');
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }
        // Format plafon sebagai angka
        $firstData = 4; $lastData = max(4, $rowNum - 1);
        $sheet->getStyle("J{$firstData}:J{$lastData}")
            ->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->freezePane('A4');

        $namaFile = 'Laporan_MasterCustomer_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN MASTER SUPPLIER
    ========================================================= */
    public function mastersupplier()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Master Supplier";
        $data['jenisLaporan'] = 'mastersupplier';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_msupplier', $data);
    }

    private function buildQueryMasterSupplier()
    {
        $sql = "SELECT
                    TRIM(s.kdsupplier)              AS kdsupplier,
                    TRIM(s.nmsupplier)              AS nmsupplier,
                    TRIM(s.cp)                      AS cp,
                    TRIM(s.phone)                   AS phone,
                    TRIM(s.fax)                     AS fax,
                    TRIM(s.alamat)                  AS alamat,
                    ''                              AS kodepos,
                    TRIM(s.npwp)                    AS npwp,
                    TRIM(s.keterangan)              AS keterangan,
                    TRIM(k.namakotakab)             AS nmkota,
                    TRIM(m.nmmarket)                AS nmmarket,
                    TRIM(s.email)                   AS email,
                    TRIM(s.jabatan)                 AS jabatan,
                    TRIM(s.npkp)                    AS npkp,
                    s.jthtempo                      AS jthtempo
                FROM sc_mst.mstsupplier s
                LEFT JOIN sc_mst.kotakab k ON TRIM(k.kodekotakab) = TRIM(s.idkota)
                LEFT JOIN sc_mst.market  m ON TRIM(m.idmarket)    = TRIM(s.idmarket)";
        return $sql;
    }

    public function previewLaporanMasterSupplier()
    {
        $kdsupplier = $this->request->getPost('kdsupplier');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $where = []; $bind = [];
        if (!empty($kdsupplier)) {
            $where[] = "(TRIM(s.kdsupplier) ILIKE ? OR TRIM(s.nmsupplier) ILIKE ?)";
            $bind[]  = '%' . trim($kdsupplier) . '%';
            $bind[]  = '%' . trim($kdsupplier) . '%';
        }
        $whereSql = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        $sqlBase = $this->buildQueryMasterSupplier() . $whereSql;
        $offset  = ($page - 1) * $perpage;

        $totalRow = $this->db->query(
            "SELECT COUNT(*) AS total FROM (" . $sqlBase . ") x", $bind
        )->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        $rows = $this->db->query(
            $sqlBase . " ORDER BY kdsupplier LIMIT ? OFFSET ?",
            array_merge($bind, [$perpage, $offset])
        )->getResultArray();

        return $this->response->setJSON([
            'status' => 'ok', 'data' => $rows,
            'page' => $page, 'perpage' => $perpage,
            'total' => $total, 'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanMasterSupplier()
    {
        $kdsupplier = $this->request->getPost('kdsupplier');

        $where = []; $bind = [];
        if (!empty($kdsupplier)) {
            $where[] = "(TRIM(s.kdsupplier) ILIKE ? OR TRIM(s.nmsupplier) ILIKE ?)";
            $bind[]  = '%' . trim($kdsupplier) . '%';
            $bind[]  = '%' . trim($kdsupplier) . '%';
        }
        $whereSql = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
        $sql = $this->buildQueryMasterSupplier() . $whereSql . " ORDER BY kdsupplier";

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) exit('Query gagal.');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN MASTER SUPPLIER');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'Kode', 'Nama', 'CP', 'Phone', 'Fax', 'Alamat', 'Kode Post', 'NPWP',
            'Keterangan', 'Kota', 'Market', 'Email', 'Jabatan', 'NPKP', 'Periode Jatuh Tempo'
        ];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:O3')->getFont()->setBold(true);
        $sheet->getStyle('A3:O3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4; $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['kdsupplier'], $r['nmsupplier'], $r['cp'], $r['phone'], $r['fax'],
                $r['alamat'], $r['kodepos'], $r['npwp'], $r['keterangan'],
                $r['nmkota'], $r['nmmarket'], $r['email'], $r['jabatan'],
                $r['npkp'], $r['jthtempo']
            ], null, 'A' . $rowNum);
            $rowNum++; $rowCount++;
        }
        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:O4');
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }
        $sheet->freezePane('A4');

        $namaFile = 'Laporan_MasterSupplier_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN PO HARIAN
    ========================================================= */
    public function poharian()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan PO Harian";
        $data['jenisLaporan'] = 'poharian';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_poharian', $data);
    }

    private function buildQueryPOHarian()
    {
        $sql = "SELECT
                    TO_CHAR(p.docdate, 'DD/MM/YYYY')     AS docdate,
                    TRIM(d.docno)                        AS docno,
                    TRIM(d.nmbarang)                     AS nmbarang,
                    TRIM(p.nmsupplier)                   AS nmsupplier,
                    d.qty                                AS qty,
                    TRIM(d.unit)                         AS unit,
                    d.harga                              AS harga,
                    TRIM(d.currcode)                     AS currcode,
                    d.kurs                               AS kurs,
                    TRIM(d.descriptionpo)                AS keterangan,
                    TO_CHAR(p.senddate, 'DD/MM/YYYY')    AS senddate
                FROM sc_trx.po_dtl d
                INNER JOIN sc_trx.po p ON TRIM(p.docno) = TRIM(d.docno)";
        return $sql;
    }

    public function previewLaporanPOHarian()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $where = []; $bind = [];

        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "p.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(d.docno) ILIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(p.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(p.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }

        $whereSql = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        $sqlBase = $this->buildQueryPOHarian() . $whereSql;
        $offset  = ($page - 1) * $perpage;

        $totalRow = $this->db->query(
            "SELECT COUNT(*) AS total FROM (" . $sqlBase . ") x", $bind
        )->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        $rows = $this->db->query(
            $sqlBase . " ORDER BY p.docdate DESC, d.docno, d.idurut LIMIT ? OFFSET ?",
            array_merge($bind, [$perpage, $offset])
        )->getResultArray();

        return $this->response->setJSON([
            'status' => 'ok', 'data' => $rows,
            'page' => $page, 'perpage' => $perpage,
            'total' => $total, 'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanPOHarian()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $docno      = $this->request->getPost('docno');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $cabang     = $this->request->getPost('cabang');

        $where = []; $bind = [];
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $where[] = "p.docdate BETWEEN ? AND ?";
                $bind[]  = date('Y-m-d', strtotime($parts[0]));
                $bind[]  = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (!empty($docno)) {
            $where[] = "TRIM(d.docno) ILIKE ?";
            $bind[]  = '%' . trim(strtoupper($docno)) . '%';
        }
        if (!empty($kdsupplier)) {
            $where[] = "TRIM(p.kdsupplier) = ?";
            $bind[]  = trim(strtoupper($kdsupplier));
        }
        if (!empty($cabang)) {
            $where[] = "TRIM(p.cabang) = ?";
            $bind[]  = trim(strtoupper($cabang));
        }
        $whereSql = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        $sql = $this->buildQueryPOHarian() . $whereSql . " ORDER BY p.docdate DESC, d.docno, d.idurut";

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'PO Harian query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN PO HARIAN');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'Tanggal', 'No.PO', 'Nama Barang', 'Nama Supplier',
            'Qty', 'Satuan', 'Harga', 'Mata Uang', 'Kurs',
            'Keterangan', 'Tgl Kirim'
        ];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:K3')->getFont()->setBold(true);
        $sheet->getStyle('A3:K3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4; $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['docdate'], $r['docno'], $r['nmbarang'], $r['nmsupplier'],
                $r['qty'], $r['unit'], $r['harga'], $r['currcode'], $r['kurs'],
                $r['keterangan'], $r['senddate']
            ], null, 'A' . $rowNum);
            $rowNum++; $rowCount++;
        }
        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:K4');
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }
        // Format angka: E=Qty, G=Harga, I=Kurs
        $firstData = 4; $lastData = max(4, $rowNum - 1);
        foreach (['E','G','I'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->freezePane('A4');

        $namaFile = 'Laporan_POHarian_' . date('Ymd_His') . '.xlsx';
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







    /* =========================================================
    LAPORAN ANALISA MUTASI STOCK
    ========================================================= */
    public function analisamutasi()
    {
        $data = $this->commonData();
        $data['title']        = "Analisa Mutasi Stock";
        $data['jenisLaporan'] = 'analisamutasi';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_analisamutasi', $data);
    }

    private function buildQueryAnalisaMutasi($withLimit = false, $perpage = 25, $offset = 0)
    {
        $sql = "WITH parameter AS (
                SELECT
                    ?::date  AS tanggal_awal,
                    ?::date  AS tanggal_akhir,
                    ?        AS cabang,
                    ?        AS idbarang
            ),

            saldo_sebelumnya AS (
                SELECT
                    s.idlocation, s.idarea, s.idbarang, s.warehouse,
                    s.cabang, s.unit, s.subunit, s.idunit, s.idbranch,
                    SUM(COALESCE(s.qty_in, 0)) - SUM(COALESCE(s.qty_out, 0)) AS saldo_qty,
                    SUM(CASE WHEN COALESCE(s.qty_in, 0)  > 0 THEN COALESCE(s.totalcost, 0) ELSE 0 END)
                    -
                    SUM(CASE WHEN COALESCE(s.qty_out, 0) > 0 THEN COALESCE(s.totalcost, 0) ELSE 0 END)
                    AS saldo_cost
                FROM sc_trx.stkblc s
                CROSS JOIN parameter p
                WHERE s.trxdate::date < p.tanggal_awal
                AND (p.cabang   IS NULL OR TRIM(p.cabang)   = '' OR TRIM(s.cabang)   = TRIM(p.cabang))
                AND (p.idbarang IS NULL OR TRIM(p.idbarang) = '' OR TRIM(s.idbarang) = TRIM(p.idbarang))
                GROUP BY
                    s.idlocation, s.idarea, s.idbarang, s.warehouse,
                    s.cabang, s.unit, s.subunit, s.idunit, s.idbranch
            ),

            transaksi AS (
                SELECT
                    s.*,
                    CASE WHEN COALESCE(s.qty_in, 0)  > 0 THEN COALESCE(s.totalcost, 0) ELSE 0 END AS cost_in,
                    CASE WHEN COALESCE(s.qty_out, 0) > 0 THEN COALESCE(s.totalcost, 0) ELSE 0 END AS cost_out
                FROM sc_trx.stkblc s
                CROSS JOIN parameter p
                WHERE s.trxdate::date >= p.tanggal_awal
                AND s.trxdate::date <= p.tanggal_akhir
                AND (p.cabang   IS NULL OR TRIM(p.cabang)   = '' OR TRIM(s.cabang)   = TRIM(p.cabang))
                AND (p.idbarang IS NULL OR TRIM(p.idbarang) = '' OR TRIM(s.idbarang) = TRIM(p.idbarang))
            ),

            stock_list AS (
                SELECT idlocation, idarea, idbarang, warehouse, cabang, unit, subunit, idunit, idbranch
                FROM saldo_sebelumnya
                UNION
                SELECT idlocation, idarea, idbarang, warehouse, cabang, unit, subunit, idunit, idbranch
                FROM transaksi
            ),

            saldo_awal AS (
                SELECT
                    1 AS urut,
                    sl.idlocation, sl.idarea, sl.idbarang, sl.warehouse,
                    sl.cabang, sl.unit, sl.subunit, sl.idunit, sl.idbranch,
                    p.tanggal_awal AS trxdate,
                    p.tanggal_awal AS docdate,
                    'SALDO AWAL'::varchar AS doctype,
                    NULL::varchar AS docno,
                    NULL::varchar AS docref,
                    COALESCE(ss.saldo_qty, 0)  AS qty_in,
                    0::numeric                 AS qty_out,
                    COALESCE(ss.saldo_cost, 0) AS cost_in,
                    0::numeric                 AS cost_out,
                    NULL::numeric AS unitcost,
                    NULL::numeric AS totalcost,
                    'SALDO AWAL'::text AS keterangan,
                    NULL::varchar AS batch
                FROM stock_list sl
                CROSS JOIN parameter p
                LEFT JOIN saldo_sebelumnya ss
                    ON  ss.idlocation = sl.idlocation
                    AND ss.idarea     = sl.idarea
                    AND ss.idbarang   = sl.idbarang
                    AND ss.warehouse  = sl.warehouse
                    AND ss.cabang     = sl.cabang
                    AND ss.unit       = sl.unit
                    AND COALESCE(ss.subunit, '') = COALESCE(sl.subunit, '')
                    AND ss.idunit     = sl.idunit
                    AND ss.idbranch   = sl.idbranch
            ),

            trx AS (
                SELECT
                    2 AS urut,
                    t.idlocation, t.idarea, t.idbarang, t.warehouse,
                    t.cabang, t.unit, t.subunit, t.idunit, t.idbranch,
                    t.trxdate, t.docdate,
                    t.doctype, t.docno, t.docref,
                    COALESCE(t.qty_in, 0)   AS qty_in,
                    COALESCE(t.qty_out, 0)  AS qty_out,
                    COALESCE(t.cost_in, 0)  AS cost_in,
                    COALESCE(t.cost_out, 0) AS cost_out,
                    t.unitcost, t.totalcost,
                    t.keterangan,
                    t.batch
                FROM transaksi t
            ),

            gabung AS (
                SELECT * FROM saldo_awal
                UNION ALL
                SELECT * FROM trx
            ),

            final AS (
                SELECT
                    g.*,
                    SUM(COALESCE(g.qty_in, 0) - COALESCE(g.qty_out, 0)) OVER (
                        PARTITION BY g.idlocation, g.idarea, g.idbarang, g.warehouse,
                                    g.cabang, g.unit, g.subunit, g.idunit, g.idbranch
                        ORDER BY g.urut, g.trxdate, g.docno NULLS FIRST
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    ) AS saldo_qty_akhir,
                    SUM(COALESCE(g.cost_in, 0) - COALESCE(g.cost_out, 0)) OVER (
                        PARTITION BY g.idlocation, g.idarea, g.idbarang, g.warehouse,
                                    g.cabang, g.unit, g.subunit, g.idunit, g.idbranch
                        ORDER BY g.urut, g.trxdate, g.docno NULLS FIRST
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    ) AS saldo_cost_akhir
                FROM gabung g
            )

            SELECT
                TRIM(f.idbarang)                 AS idbarang,
                TRIM(mb.nmbarang)                AS nmbarang,
                TRIM(f.unit)                     AS unit,
                TO_CHAR(f.trxdate, 'DD/MM/YYYY') AS trxdate,
                TRIM(f.docno)                    AS docno,
                TRIM(f.keterangan)               AS keterangan,
                COALESCE(f.qty_in, 0)            AS qty_debet,
                COALESCE(f.cost_in, 0)           AS nilai_debet,
                COALESCE(f.qty_out, 0)           AS qty_kredit,
                COALESCE(f.cost_out, 0)          AS nilai_kredit,
                COALESCE(f.saldo_qty_akhir, 0)   AS saldo_qty_akhir,
                COALESCE(f.saldo_cost_akhir, 0)  AS saldo_cost_akhir,
                TRIM(f.idbranch)                 AS job,
                COALESCE(NULLIF(TRIM(pr.nmprincipal), ''), TRIM(mb.idprincipal), '') AS principal,
                TRIM(mb.ppersediaan)             AS perkiraan,
                TRIM(f.batch)                    AS batch,
                ''                               AS expdate
            FROM final f
            LEFT JOIN sc_mst.mbarang   mb ON TRIM(mb.idbarang)    = TRIM(f.idbarang)
            LEFT JOIN sc_mst.principal pr ON TRIM(pr.idprincipal) = TRIM(mb.idprincipal)
            ORDER BY
                f.idbarang, f.unit, f.urut, f.trxdate, f.docno";  // tempel query di atas
        if ($withLimit) {
            $sql .= " LIMIT ? OFFSET ?";
        }
        return $sql;
    }

    /* =========================================================
    PREVIEW
    ========================================================= */
    public function previewLaporanAnalisaMutasi()
    {
        $tglrange = $this->request->getPost('tglrange');
        $cabang   = $this->request->getPost('cabang');
        $idbarang = $this->request->getPost('idbarang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $tglAwal  = null;
        $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }

        // WAJIB ada tanggal
        if (empty($tglAwal) || empty($tglAkhir)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tanggal wajib diisi.',
            ]);
        }

        $sql = $this->buildQueryAnalisaMutasi();

        // --- HITUNG TOTAL BARIS ---
        $sqlCount = "SELECT COUNT(*) AS total FROM (" . rtrim($sql, ';') . ") x";
        $bindBase = [$tglAwal, $tglAkhir, $cabang ?: null, $idbarang ?: null];

        $totalRow  = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        // --- DATA HALAMAN INI ---
        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);

        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

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
    DOWNLOAD EXCEL
    ========================================================= */
    public function downloadLaporanAnalisaMutasi()
    {
        $tglrange = $this->request->getPost('tglrange');
        $cabang   = $this->request->getPost('cabang');
        $idbarang = $this->request->getPost('idbarang');

        $tglAwal  = null;
        $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }

        if (empty($tglAwal) || empty($tglAkhir)) {
            exit('Tanggal wajib diisi.');
        }

        $sql  = $this->buildQueryAnalisaMutasi();
        $bind = [$tglAwal, $tglAkhir, $cabang ?: null, $idbarang ?: null];

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Analisa Mutasi query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN ANALISA MUTASI STOCK');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'Kode Barang', 'Nama Barang', 'Satuan', 'Tanggal', 'No. Jurnal',
            'Keterangan', 'Qty Debet', 'Nilai Debet', 'Qty Kredit', 'Nilai Kredit',
            'Saldo Qty', 'Saldo Nilai', 'Job', 'Principal', 'Perkiraan'
        ];
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'O';
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum   = 4;
        $rowCount = 0;

        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['idbarang'],
                $r['nmbarang'],
                $r['unit'],
                $r['trxdate'],
                $r['docno'],
                $r['keterangan'],
                $r['qty_debet'],
                $r['nilai_debet'],
                $r['qty_kredit'],
                $r['nilai_kredit'],
                $r['saldo_qty_akhir'],
                $r['saldo_cost_akhir'],
                $r['job'],
                $r['principal'],
                $r['perkiraan'],
            ], null, 'A' . $rowNum);
            $rowNum++;
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A4:{$lastCol}4");
        }

        // auto width
        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        // format angka
        $firstData = 4;
        $lastData  = max(4, $rowNum - 1);
        foreach (['G','H','I','J','K','L'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        $namaFile = 'Laporan_AnalisaMutasiStock_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN KARTU STOCK PER GUDANG
    ========================================================= */
    public function kartustock()
    {
        $data = $this->commonData();
        $data['title']        = "Kartu Stock Per Gudang";
        $data['jenisLaporan'] = 'kartustock';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_kartustock', $data);
    }

    private function buildQueryKartuStock()
    {
        $sql = "WITH parameter AS (
                SELECT
                    ?::date  AS tanggal_awal,
                    ?::date  AS tanggal_akhir,
                    ?        AS cabang,
                    ?        AS idlocation,
                    ?        AS idbarang
            ),

            saldo_sebelumnya AS (
                SELECT
                    s.idlocation, s.idarea, s.idbarang, s.warehouse,
                    s.cabang, s.unit, s.subunit, s.idunit, s.idbranch,
                    SUM(COALESCE(s.qty_in, 0)) - SUM(COALESCE(s.qty_out, 0)) AS saldo_qty
                FROM sc_trx.stkblc s
                CROSS JOIN parameter p
                WHERE s.trxdate::date < p.tanggal_awal
                AND (p.cabang     IS NULL OR TRIM(p.cabang)     = '' OR TRIM(s.cabang)     = TRIM(p.cabang))
                AND (p.idlocation IS NULL OR TRIM(p.idlocation) = '' OR TRIM(s.idlocation) = TRIM(p.idlocation))
                AND (p.idbarang   IS NULL OR TRIM(p.idbarang)   = '' OR TRIM(s.idbarang)   = TRIM(p.idbarang))
                GROUP BY
                    s.idlocation, s.idarea, s.idbarang, s.warehouse,
                    s.cabang, s.unit, s.subunit, s.idunit, s.idbranch
            ),

            transaksi AS (
                SELECT
                    s.*,
                    CASE WHEN COALESCE(s.qty_in, 0)  > 0 THEN COALESCE(s.totalcost, 0) ELSE 0 END AS cost_in,
                    CASE WHEN COALESCE(s.qty_out, 0) > 0 THEN COALESCE(s.totalcost, 0) ELSE 0 END AS cost_out
                FROM sc_trx.stkblc s
                CROSS JOIN parameter p
                WHERE s.trxdate::date >= p.tanggal_awal
                AND s.trxdate::date <= p.tanggal_akhir
                AND (p.cabang     IS NULL OR TRIM(p.cabang)     = '' OR TRIM(s.cabang)     = TRIM(p.cabang))
                AND (p.idlocation IS NULL OR TRIM(p.idlocation) = '' OR TRIM(s.idlocation) = TRIM(p.idlocation))
                AND (p.idbarang   IS NULL OR TRIM(p.idbarang)   = '' OR TRIM(s.idbarang)   = TRIM(p.idbarang))
            ),

            stock_list AS (
                SELECT idlocation, idarea, idbarang, warehouse, cabang, unit, subunit, idunit, idbranch
                FROM saldo_sebelumnya
                UNION
                SELECT idlocation, idarea, idbarang, warehouse, cabang, unit, subunit, idunit, idbranch
                FROM transaksi
            ),

            saldo_awal AS (
                SELECT
                    1 AS urut,
                    sl.idlocation, sl.idarea, sl.idbarang, sl.warehouse,
                    sl.cabang, sl.unit, sl.subunit, sl.idunit, sl.idbranch,
                    p.tanggal_awal AS trxdate,
                    'SALDO AWAL'::varchar AS doctype,
                    NULL::varchar AS docno,
                    COALESCE(ss.saldo_qty, 0) AS qty_in,
                    0::numeric                AS qty_out,
                    'SALDO AWAL'::text        AS keterangan,
                    NULL::varchar             AS batch
                FROM stock_list sl
                CROSS JOIN parameter p
                LEFT JOIN saldo_sebelumnya ss
                    ON  ss.idlocation = sl.idlocation
                    AND ss.idarea     = sl.idarea
                    AND ss.idbarang   = sl.idbarang
                    AND ss.warehouse  = sl.warehouse
                    AND ss.cabang     = sl.cabang
                    AND ss.unit       = sl.unit
                    AND COALESCE(ss.subunit, '') = COALESCE(sl.subunit, '')
                    AND ss.idunit     = sl.idunit
                    AND ss.idbranch   = sl.idbranch
            ),

            trx AS (
                SELECT
                    2 AS urut,
                    t.idlocation, t.idarea, t.idbarang, t.warehouse,
                    t.cabang, t.unit, t.subunit, t.idunit, t.idbranch,
                    t.trxdate,
                    t.doctype, t.docno,
                    COALESCE(t.qty_in, 0)  AS qty_in,
                    COALESCE(t.qty_out, 0) AS qty_out,
                    t.keterangan,
                    t.batch
                FROM transaksi t
            ),

            gabung AS (
                SELECT * FROM saldo_awal
                UNION ALL
                SELECT * FROM trx
            )

            SELECT
                TRIM(f.idlocation)               AS idlocation,
                TRIM(ml.nmlocation)              AS nmgudang,
                TRIM(f.idbarang)                 AS idbarang,
                TRIM(mb.nmbarang)                AS nmbarang,
                TRIM(f.unit)                     AS unit,
                TO_CHAR(f.trxdate, 'DD/MM/YYYY') AS trxdate,
                TRIM(f.docno)                    AS docno,
                TRIM(f.keterangan)               AS keterangan,
                COALESCE(f.qty_in, 0)            AS qty_debet,
                COALESCE(f.qty_out, 0)           AS qty_kredit,
                TRIM(f.idbranch)                 AS job,
                COALESCE(NULLIF(TRIM(pr.nmprincipal), ''), TRIM(mb.idprincipal), '') AS principal,
                TRIM(f.batch)                    AS batch,
                ''                               AS expdate
            FROM gabung f
            LEFT JOIN sc_mst.mbarang   mb ON TRIM(mb.idbarang)    = TRIM(f.idbarang)
            LEFT JOIN sc_mst.principal pr ON TRIM(pr.idprincipal) = TRIM(mb.idprincipal)
            LEFT JOIN sc_mst.mlocation ml ON TRIM(ml.idlocation)  = TRIM(f.idlocation)
            ORDER BY
                f.idlocation, f.idbarang, f.unit, f.urut, f.trxdate, f.docno";

        return rtrim($sql, " \t\n\r\0\x0B;");
    }

    public function previewLaporanKartuStock()
    {
        $tglrange  = $this->request->getPost('tglrange');
        $cabang    = $this->request->getPost('cabang');
        $idlocation= $this->request->getPost('idlocation');
        $idbarang  = $this->request->getPost('idbarang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }

        if (empty($tglAwal) || empty($tglAkhir)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal wajib diisi.']);
        }

        $bindBase = [$tglAwal, $tglAkhir, $cabang ?: null, $idlocation ?: null, $idbarang ?: null];
        $sql = $this->buildQueryKartuStock();

        $sqlCount  = "SELECT COUNT(*) AS total FROM (" . $sql . ") x";
        $totalRow  = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);
        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

        return $this->response->setJSON([
            'status' => 'ok', 'data' => $rows,
            'page' => $page, 'perpage' => $perpage,
            'total' => $total, 'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanKartuStock()
    {
        $tglrange  = $this->request->getPost('tglrange');
        $cabang    = $this->request->getPost('cabang');
        $idlocation= $this->request->getPost('idlocation');
        $idbarang  = $this->request->getPost('idbarang');

        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }

        if (empty($tglAwal) || empty($tglAkhir)) exit('Tanggal wajib diisi.');

        $bind = [$tglAwal, $tglAkhir, $cabang ?: null, $idlocation ?: null, $idbarang ?: null];
        $sql  = $this->buildQueryKartuStock();

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Kartu Stock query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN KARTU STOCK PER GUDANG');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'Kode Gudang', 'Gudang', 'Kode Barang', 'Nama Barang', 'Satuan',
            'Tanggal', 'No. Jurnal', 'Keterangan', 'Qty Debet', 'Qty Kredit',
            'Job', 'Principal', 'No. Batch/Spec', 'Expired Date'
        ];
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'N';
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4;
        $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['idlocation'], $r['nmgudang'], $r['idbarang'], $r['nmbarang'], $r['unit'],
                $r['trxdate'], $r['docno'], $r['keterangan'], $r['qty_debet'], $r['qty_kredit'],
                $r['job'], $r['principal'], $r['batch'], $r['expdate']
            ], null, 'A' . $rowNum);
            $rowNum++;
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A4:{$lastCol}4");
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        $firstData = 4;
        $lastData  = max(4, $rowNum - 1);
        foreach (['I','J'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        $namaFile = 'Laporan_KartuStock_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN POSISI BARANG PER GUDANG
    ========================================================= */
    public function posisibrg()
    {
        $data = $this->commonData();
        $data['title']        = "Posisi Barang Per Gudang";
        $data['jenisLaporan'] = 'posisibrg';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_posisibrg', $data);
    }

    private function buildQueryPosisiBrg()
    {
        $sql = "WITH parameter AS (
                SELECT
                    ?::date  AS tanggal_awal,
                    ?::date  AS tanggal_akhir,
                    ?        AS cabang,
                    ?        AS idlocation,
                    ?        AS idbarang
            ),

            saldo_awal AS (
                SELECT
                    s.idbarang, s.idlocation,
                    s.cabang, s.idbranch,
                    SUM(COALESCE(s.qty_in, 0)) - SUM(COALESCE(s.qty_out, 0)) AS saldo_awal
                FROM sc_trx.stkblc s
                CROSS JOIN parameter p
                WHERE s.trxdate::date < p.tanggal_awal
                AND (p.cabang     IS NULL OR TRIM(p.cabang)     = '' OR TRIM(s.cabang)     = TRIM(p.cabang))
                AND (p.idlocation IS NULL OR TRIM(p.idlocation) = '' OR TRIM(s.idlocation) = TRIM(p.idlocation))
                AND (p.idbarang   IS NULL OR TRIM(p.idbarang)   = '' OR TRIM(s.idbarang)   = TRIM(p.idbarang))
                GROUP BY s.idbarang, s.idlocation, s.cabang, s.idbranch
            ),

            mutasi AS (
                SELECT
                    s.idbarang, s.idlocation,
                    s.cabang, s.idbranch,
                    SUM(COALESCE(s.qty_in, 0))  AS debet,
                    SUM(COALESCE(s.qty_out, 0)) AS kredit
                FROM sc_trx.stkblc s
                CROSS JOIN parameter p
                WHERE s.trxdate::date >= p.tanggal_awal
                AND s.trxdate::date <= p.tanggal_akhir
                AND (p.cabang     IS NULL OR TRIM(p.cabang)     = '' OR TRIM(s.cabang)     = TRIM(p.cabang))
                AND (p.idlocation IS NULL OR TRIM(p.idlocation) = '' OR TRIM(s.idlocation) = TRIM(p.idlocation))
                AND (p.idbarang   IS NULL OR TRIM(p.idbarang)   = '' OR TRIM(s.idbarang)   = TRIM(p.idbarang))
                GROUP BY s.idbarang, s.idlocation, s.cabang, s.idbranch
            ),

            stock_list AS (
                SELECT idbarang, idlocation, cabang, idbranch FROM saldo_awal
                UNION
                SELECT idbarang, idlocation, cabang, idbranch FROM mutasi
            ),

            final AS (
                SELECT
                    sl.idbarang, sl.idlocation,
                    sl.cabang, sl.idbranch,
                    COALESCE(sa.saldo_awal, 0) AS saldo_awal,
                    COALESCE(m.debet, 0)       AS debet,
                    COALESCE(m.kredit, 0)      AS kredit,
                    COALESCE(sa.saldo_awal, 0) + COALESCE(m.debet, 0) - COALESCE(m.kredit, 0) AS saldo_akhir
                FROM stock_list sl
                LEFT JOIN saldo_awal sa
                    ON  sa.idbarang   = sl.idbarang
                    AND sa.idlocation = sl.idlocation
                    AND sa.cabang     = sl.cabang
                    AND sa.idbranch   = sl.idbranch
                LEFT JOIN mutasi m
                    ON  m.idbarang   = sl.idbarang
                    AND m.idlocation = sl.idlocation
                    AND m.cabang     = sl.cabang
                    AND m.idbranch   = sl.idbranch
            )

            SELECT
                TRIM(f.idbarang)                          AS idbarang,
                TRIM(mb.nmbarang)                         AS nmbarang,
                ''                                        AS batch,
                '01/01/0001'                              AS expdate,
                TRIM(f.idlocation)                        AS idlocation,
                TRIM(ml.nmlocation)                       AS nmlocation,
                TRIM(mb.unit)                             AS unit,
                COALESCE(f.saldo_awal, 0)                 AS saldo_awal,
                COALESCE(f.debet, 0)                      AS debet,
                COALESCE(f.kredit, 0)                     AS kredit,
                COALESCE(f.saldo_akhir, 0)                AS saldo_akhir,
                TRIM(f.cabang)                            AS job_kode,
                TRIM(b.nmbranch)                          AS namajob,
                TRIM(mb.idgolonganbarang)                 AS golongan,
                TRIM(mb.idjenisproduk)                    AS jenisproduk,
                TRIM(mb.idkelompokbarang)                 AS kelompok,
                COALESCE(NULLIF(TRIM(pr.nmprincipal), ''), TRIM(mb.idprincipal), '') AS principal,
                TRIM(mb.description)                      AS keteranganbarang,
                TRIM(mb.nmbarang)                         AS bomdesc
            FROM final f
            LEFT JOIN sc_mst.mbarang   mb ON TRIM(mb.idbarang)    = TRIM(f.idbarang)
            LEFT JOIN sc_mst.mlocation ml ON TRIM(ml.idlocation)  = TRIM(f.idlocation)
            LEFT JOIN sc_mst.principal pr ON TRIM(pr.idprincipal) = TRIM(mb.idprincipal)
            LEFT JOIN sc_mst.branchjob b  ON TRIM(b.idbranch)     = TRIM(f.idbranch)
            ORDER BY
                f.idbarang, f.idlocation";

        return rtrim($sql, " \t\n\r\0\x0B;");
    }

    public function previewLaporanPosisiBrg()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $idlocation = $this->request->getPost('idlocation');
        $idbarang   = $this->request->getPost('idbarang');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }

        if (empty($tglAwal) || empty($tglAkhir)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal wajib diisi.']);
        }

        $bindBase = [$tglAwal, $tglAkhir, $cabang ?: null, $idlocation ?: null, $idbarang ?: null];
        $sql = $this->buildQueryPosisiBrg();

        $sqlCount  = "SELECT COUNT(*) AS total FROM (" . $sql . ") x";
        $totalRow  = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);
        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

        return $this->response->setJSON([
            'status' => 'ok', 'data' => $rows,
            'page' => $page, 'perpage' => $perpage,
            'total' => $total, 'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanPosisiBrg()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $idlocation = $this->request->getPost('idlocation');
        $idbarang   = $this->request->getPost('idbarang');

        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }

        if (empty($tglAwal) || empty($tglAkhir)) exit('Tanggal wajib diisi.');

        $bind = [$tglAwal, $tglAkhir, $cabang ?: null, $idlocation ?: null, $idbarang ?: null];
        $sql  = $this->buildQueryPosisiBrg();

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Posisi Barang query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN POSISI BARANG PER GUDANG');
        $sheet->mergeCells('A1:R1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'Kode', 'Nama Barang', 'No.Batch', 'Expired Date',
            'Kode Gudang', 'Nama Gudang', 'Satuan',
            'Saldo Awal', 'Debet', 'Kredit', 'Saldo Akhir',
            'Job', 'Nama Job',
            'Golongan', 'Jenis Produk', 'Kelompok', 'Principal',
            'Keterangan Barang', 'BOM Deskripsi'
        ];
        $sheet->fromArray($headers, null, 'A3');

        $lastCol = 'S';
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A3:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4;
        $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['idbarang'], $r['nmbarang'], $r['batch'], $r['expdate'],
                $r['idlocation'], $r['nmlocation'], $r['unit'],
                $r['saldo_awal'], $r['debet'], $r['kredit'], $r['saldo_akhir'],
                $r['job_kode'], $r['namajob'],
                $r['golongan'], $r['jenisproduk'], $r['kelompok'], $r['principal'],
                $r['keteranganbarang'], $r['bomdesc']
            ], null, 'A' . $rowNum);
            $rowNum++;
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A4:{$lastCol}4");
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        $firstData = 4;
        $lastData  = max(4, $rowNum - 1);
        foreach (['H','I','J','K'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        $namaFile = 'Laporan_PosisiBarangPerGudang_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN POSISI HUTANG
    ========================================================= */
    public function posisihutang()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Posisi Hutang";
        $data['jenisLaporan'] = 'posisihutang';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_posisihutang', $data);
    }

    private function buildQueryPosisiHutang()
    {
        $sql = "WITH parameter AS (
                    SELECT ?::date AS tgl_awal, ?::date AS tgl_akhir,
                        ? AS cabang, ? AS kdprk, ? AS kdsupplier
                ),
                saldo_awal AS (
                    SELECT td.idcoa, td.kdsupplier,
                        SUM(COALESCE(td.debet, 0) - COALESCE(td.kredit, 0)) AS saldo_awal
                    FROM sc_trx.transaction_dt td
                    CROSS JOIN parameter p
                    WHERE td.docdate::date < p.tgl_awal
                    AND td.idcoa LIKE '2%'
                    AND (p.cabang IS NULL OR TRIM(p.cabang) = '' OR TRIM(td.cabang) = TRIM(p.cabang))
                    AND (p.kdprk  IS NULL OR TRIM(p.kdprk)  = '' OR TRIM(td.idcoa)  = TRIM(p.kdprk))
                    AND (p.kdsupplier IS NULL OR TRIM(p.kdsupplier) = '' OR TRIM(td.kdsupplier) = TRIM(p.kdsupplier))
                    GROUP BY td.idcoa, td.kdsupplier
                ),
                mutasi AS (
                    SELECT td.idcoa, td.kdsupplier,
                        SUM(COALESCE(td.debet, 0))  AS debet,
                        SUM(COALESCE(td.kredit, 0)) AS kredit
                    FROM sc_trx.transaction_dt td
                    CROSS JOIN parameter p
                    WHERE td.docdate::date >= p.tgl_awal
                    AND td.docdate::date <= p.tgl_akhir
                    AND td.idcoa LIKE '2%'
                    AND (p.cabang IS NULL OR TRIM(p.cabang) = '' OR TRIM(td.cabang) = TRIM(p.cabang))
                    AND (p.kdprk  IS NULL OR TRIM(p.kdprk)  = '' OR TRIM(td.idcoa)  = TRIM(p.kdprk))
                    AND (p.kdsupplier IS NULL OR TRIM(p.kdsupplier) = '' OR TRIM(td.kdsupplier) = TRIM(p.kdsupplier))
                    GROUP BY td.idcoa, td.kdsupplier
                ),
                stock_list AS (
                    SELECT idcoa, kdsupplier FROM saldo_awal
                    UNION
                    SELECT idcoa, kdsupplier FROM mutasi
                ),
                final AS (
                    SELECT sl.idcoa, sl.kdsupplier,
                        COALESCE(sa.saldo_awal, 0)  AS saldo_awal,
                        COALESCE(m.debet, 0)        AS debet,
                        COALESCE(m.kredit, 0)       AS kredit,
                        COALESCE(sa.saldo_awal, 0) + COALESCE(m.debet, 0) - COALESCE(m.kredit, 0) AS saldo_akhir
                    FROM stock_list sl
                    LEFT JOIN saldo_awal sa ON sa.idcoa = sl.idcoa AND sa.kdsupplier = sl.kdsupplier
                    LEFT JOIN mutasi     m  ON m.idcoa  = sl.idcoa AND m.kdsupplier  = sl.kdsupplier
                )
                SELECT
                    TRIM(f.idcoa)               AS kdprk,
                    TRIM(c.nmcoa)               AS nmprk,
                    TRIM(f.kdsupplier)          AS kdsupplier,
                    TRIM(s.nmsupplier)          AS nmsupplier,
                    COALESCE(f.saldo_awal, 0)   AS saldo_awal,
                    COALESCE(f.debet, 0)        AS debet,
                    COALESCE(f.kredit, 0)       AS kredit,
                    COALESCE(f.saldo_akhir, 0)  AS saldo_akhir
                FROM final f
                LEFT JOIN sc_mst.coa        c ON TRIM(c.idcoa)      = TRIM(f.idcoa)
                LEFT JOIN sc_mst.mstsupplier s ON TRIM(s.kdsupplier) = TRIM(f.kdsupplier)
                ORDER BY f.idcoa, f.kdsupplier";

        return rtrim($sql, " \t\n\r\0\x0B;");
    }

    public function previewLaporanPosisiHutang()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $kdprk      = $this->request->getPost('kdprk');
        $kdsupplier = $this->request->getPost('kdsupplier');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (empty($tglAwal) || empty($tglAkhir)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal wajib diisi.']);
        }

        $bindBase = [$tglAwal, $tglAkhir, $cabang ?: null, $kdprk ?: null, $kdsupplier ?: null];

        $sql = $this->buildQueryPosisiHutang();

        // COUNT
        $sqlCount  = "SELECT COUNT(*) AS total FROM (" . $sql . ") x";
        $totalRow  = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total     = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        // DATA
        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);
        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

        return $this->response->setJSON([
            'status' => 'ok', 'data' => $rows,
            'page' => $page, 'perpage' => $perpage,
            'total' => $total, 'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanPosisiHutang()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $kdprk      = $this->request->getPost('kdprk');
        $kdsupplier = $this->request->getPost('kdsupplier');

        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (empty($tglAwal) || empty($tglAkhir)) exit('Tanggal wajib diisi.');

        $bind = [$tglAwal, $tglAkhir, $cabang ?: null, $kdprk ?: null, $kdsupplier ?: null];
        $sql  = $this->buildQueryPosisiHutang();

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Posisi Hutang query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN POSISI HUTANG');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = ['Kode Prk', 'Nama Prk', 'Kode Supplier', 'Nama Supplier',
                    'Saldo Awal', 'Debet', 'Kredit', 'Saldo Akhir'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:H3')->getFont()->setBold(true);
        $sheet->getStyle('A3:H3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4; $rowCount = 0;
        $lastCoa = null;
        $subtotal = ['sa' => 0, 'd' => 0, 'k' => 0, 'akhir' => 0];

        while ($r = $query->getUnbufferedRow('array')) {

            // Ganti COA → tulis Total COA sebelumnya
            if ($lastCoa !== null && $lastCoa !== $r['kdprk']) {
                $sheet->fromArray(['', '', '', 'TOTAL ' . $lastCoa,
                    $subtotal['sa'], $subtotal['d'], $subtotal['k'], $subtotal['akhir']],
                    null, 'A' . $rowNum);
                $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
                $rowNum++;
                $subtotal = ['sa' => 0, 'd' => 0, 'k' => 0, 'akhir' => 0];
            }

            $sheet->fromArray([
                $r['kdprk'], $r['nmprk'], $r['kdsupplier'], $r['nmsupplier'],
                $r['saldo_awal'], $r['debet'], $r['kredit'], $r['saldo_akhir']
            ], null, 'A' . $rowNum);

            $subtotal['sa']    += (float)$r['saldo_awal'];
            $subtotal['d']     += (float)$r['debet'];
            $subtotal['k']     += (float)$r['kredit'];
            $subtotal['akhir'] += (float)$r['saldo_akhir'];
            $lastCoa = $r['kdprk'];

            $rowNum++; $rowCount++;
        }

        // Total COA terakhir
        if ($lastCoa !== null) {
            $sheet->fromArray(['', '', '', 'TOTAL ' . $lastCoa,
                $subtotal['sa'], $subtotal['d'], $subtotal['k'], $subtotal['akhir']],
                null, 'A' . $rowNum);
            $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
            $rowNum++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:H4');
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        // Format angka: E..H
        $firstData = 4; $lastData = max(4, $rowNum - 1);
        foreach (['E','F','G','H'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->freezePane('A4');

        $namaFile = 'Laporan_PosisiHutang_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN UMUR HUTANG (AP AGING)
    ========================================================= */
    public function umurhutang()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Umur Hutang";
        $data['jenisLaporan'] = 'umurhutang';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_umurhutang', $data);
    }

    private function buildQueryUmurHutang()
    {
        // ============================================
        // TODO: ISI QUERY DARI ATASAN
        // ============================================
        // Kolom output yang diharapkan:
        // docno, kdsupplier, nmsupplier, kdprk, nmprk, docdate, tgljt, dk,
        // keterangan, currcode, kurs, nilai, nilai_belum_jt,
        // nilai_jt_1..4, umur, kodejob, namajob, alamatsupplier, kotasupplier
        //
        // Filter (pakai ? di query):
        // - cabang
        // - kdsupplier
        // - kdprk (COA)
        // - interval (default 30)
        // - konversi ke IDR (lokal)
        // ============================================

        $sql = "SELECT
                    '' AS docno,
                    '' AS kdsupplier,
                    '' AS nmsupplier,
                    '' AS kdprk,
                    '' AS nmprk,
                    NULL AS docdate,
                    NULL AS tgljt,
                    '' AS dk,
                    '' AS keterangan,
                    'IDR' AS currcode,
                    1 AS kurs,
                    0 AS nilai,
                    0 AS nilai_belum_jt,
                    0 AS nilai_jt_1,
                    0 AS nilai_jt_2,
                    0 AS nilai_jt_3,
                    0 AS nilai_jt_4,
                    0 AS umur,
                    '' AS kodejob,
                    '' AS namajob,
                    '' AS alamatsupplier,
                    '' AS kotasupplier
                WHERE 1=0";

        return rtrim($sql, " \t\n\r\0\x0B;");
    }

    public function previewLaporanUmurHutang()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $kdprk      = $this->request->getPost('kdprk');
        $interval   = $this->request->getPost('interval') ?: 30;

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        // ⚠️ SEMENTARA: query placeholder tidak punya ? → bindBase kosong
        // Setelah query asli datang, isi: [$cabang, $kdsupplier, $kdprk, $interval]
        $bindBase = [];

        $sql = $this->buildQueryUmurHutang();

        // COUNT
        $sqlCount = "SELECT COUNT(*) AS total FROM (" . $sql . ") x";
        $totalRow = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total    = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        // DATA
        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);
        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanUmurHutang()
    {
        $cabang     = $this->request->getPost('cabang');
        $kdsupplier = $this->request->getPost('kdsupplier');
        $kdprk      = $this->request->getPost('kdprk');
        $interval   = $this->request->getPost('interval') ?: 30;

        // ⚠️ SEMENTARA: kosong, ikuti bindBase preview
        $bind = [];

        $sql = $this->buildQueryUmurHutang();

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Umur Hutang query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN UMUR HUTANG');
        $sheet->mergeCells('A1:V1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $headers = [
            'No.Jurnal', 'Kode Supplier', 'Nama Supplier', 'Kode Prk', 'Nama Prk',
            'Tanggal', 'Tgl JT', 'DK', 'Keterangan', 'Mata Uang', 'Kurs',
            'Nilai', 'Nilai Belum JT',
            'Nilai JT 1', 'Nilai JT 2', 'Nilai JT 3', 'Nilai JT 4',
            'Umur', 'Kode Job', 'Nama Job', 'Alamat Supplier', 'Kota Supplier'
        ];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:V3')->getFont()->setBold(true);
        $sheet->getStyle('A3:V3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4; $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['docno'], $r['kdsupplier'], $r['nmsupplier'], $r['kdprk'], $r['nmprk'],
                $r['docdate'], $r['tgljt'], $r['dk'], $r['keterangan'],
                $r['currcode'], $r['kurs'],
                $r['nilai'], $r['nilai_belum_jt'],
                $r['nilai_jt_1'], $r['nilai_jt_2'], $r['nilai_jt_3'], $r['nilai_jt_4'],
                $r['umur'], $r['kodejob'], $r['namajob'],
                $r['alamatsupplier'], $r['kotasupplier']
            ], null, 'A' . $rowNum);
            $rowNum++; $rowCount++;
        }
        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:V4');
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        // Format angka: K=Kurs, L=Nilai, M=Belum JT, N-Q=JT1..4, R=Umur
        $firstData = 4; $lastData = max(4, $rowNum - 1);
        foreach (['K','L','M','N','O','P','Q','R'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->freezePane('A4');

        $namaFile = 'Laporan_UmurHutang_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN POSISI PIUTANG (AR POSITION)
    ========================================================= */
    public function posisipiutang()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Posisi Piutang";
        $data['jenisLaporan'] = 'posisipiutang';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_posisipiutang', $data);
    }

    private function buildQueryPosisiPiutang()
    {
        $sql = "WITH parameter AS (
                    SELECT ?::date AS tgl_awal,
                        ?::date AS tgl_akhir,
                        ?       AS cabang,
                        ?       AS kdprk,
                        ?       AS kdcustomer
                ),

                /* ============================================
                SALDO AWAL (sebelum tgl_awal)
                ============================================ */
                saldo_awal AS (
                    SELECT
                        td.idcoa,
                        td.kdcustomer,
                        SUM(COALESCE(td.debet, 0) - COALESCE(td.kredit, 0)) AS saldo_awal
                    FROM sc_trx.transaction_dt td
                    CROSS JOIN parameter p
                    WHERE td.docdate::date < p.tgl_awal
                    AND td.idcoa LIKE '1%'
                    AND (p.cabang     IS NULL OR TRIM(p.cabang)     = '' OR TRIM(td.cabang)     = TRIM(p.cabang))
                    AND (p.kdprk      IS NULL OR TRIM(p.kdprk)      = '' OR TRIM(td.idcoa)      = TRIM(p.kdprk))
                    AND (p.kdcustomer IS NULL OR TRIM(p.kdcustomer) = '' OR TRIM(td.kdcustomer) = TRIM(p.kdcustomer))
                    GROUP BY td.idcoa, td.kdcustomer
                ),

                /* ============================================
                MUTASI PERIODE
                ============================================ */
                mutasi AS (
                    SELECT
                        td.idcoa,
                        td.kdcustomer,
                        SUM(COALESCE(td.debet, 0))  AS debet,
                        SUM(COALESCE(td.kredit, 0)) AS kredit
                    FROM sc_trx.transaction_dt td
                    CROSS JOIN parameter p
                    WHERE td.docdate::date >= p.tgl_awal
                    AND td.docdate::date <= p.tgl_akhir
                    AND td.idcoa LIKE '1%'
                    AND (p.cabang     IS NULL OR TRIM(p.cabang)     = '' OR TRIM(td.cabang)     = TRIM(p.cabang))
                    AND (p.kdprk      IS NULL OR TRIM(p.kdprk)      = '' OR TRIM(td.idcoa)      = TRIM(p.kdprk))
                    AND (p.kdcustomer IS NULL OR TRIM(p.kdcustomer) = '' OR TRIM(td.kdcustomer) = TRIM(p.kdcustomer))
                    GROUP BY td.idcoa, td.kdcustomer
                ),

                /* ============================================
                STOCK LIST
                ============================================ */
                stock_list AS (
                    SELECT idcoa, kdcustomer FROM saldo_awal
                    UNION
                    SELECT idcoa, kdcustomer FROM mutasi
                ),

                /* ============================================
                FINAL
                ============================================ */
                final AS (
                    SELECT
                        sl.idcoa,
                        sl.kdcustomer,
                        COALESCE(sa.saldo_awal, 0)  AS saldo_awal,
                        COALESCE(m.debet, 0)        AS debet,
                        COALESCE(m.kredit, 0)       AS kredit,
                        COALESCE(sa.saldo_awal, 0)
                        + COALESCE(m.debet, 0)
                        - COALESCE(m.kredit, 0)   AS saldo_akhir
                    FROM stock_list sl
                    LEFT JOIN saldo_awal sa
                        ON sa.idcoa = sl.idcoa AND sa.kdcustomer = sl.kdcustomer
                    LEFT JOIN mutasi m
                        ON m.idcoa  = sl.idcoa AND m.kdcustomer  = sl.kdcustomer
                )

                SELECT
                    TRIM(f.idcoa)               AS kdprk,
                    TRIM(c.nmcoa)               AS nmprk,
                    TRIM(f.kdcustomer)          AS kdcustomer,
                    TRIM(cu.nmcustomer)         AS nmcustomer,
                    COALESCE(f.saldo_awal, 0)   AS saldo_awal,
                    COALESCE(f.debet, 0)        AS debet,
                    COALESCE(f.kredit, 0)       AS kredit,
                    COALESCE(f.saldo_akhir, 0)  AS saldo_akhir
                FROM final f
                LEFT JOIN sc_mst.coa      c  ON TRIM(c.idcoa)       = TRIM(f.idcoa)
                LEFT JOIN sc_mst.customer cu ON TRIM(cu.kdcustomer) = TRIM(f.kdcustomer)
                ORDER BY f.idcoa, f.kdcustomer";

        return rtrim($sql, " \t\n\r\0\x0B;");
    }

    public function previewLaporanPosisiPiutang()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $kdprk      = $this->request->getPost('kdprk');
        $kdcustomer = $this->request->getPost('kdcustomer');

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        // ---- Tanggal ----
        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (empty($tglAwal) || empty($tglAkhir)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tanggal wajib diisi.',
            ]);
        }

        $bindBase = [
            $tglAwal,
            $tglAkhir,
            $cabang     ?: null,
            $kdprk      ?: null,
            $kdcustomer ?: null,
        ];

        $sql = $this->buildQueryPosisiPiutang();

        // ---- COUNT ----
        $sqlCount = "SELECT COUNT(*) AS total FROM (" . $sql . ") x";
        $totalRow = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total    = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        // ---- DATA ----
        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);
        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanPosisiPiutang()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $kdprk      = $this->request->getPost('kdprk');
        $kdcustomer = $this->request->getPost('kdcustomer');

        // ---- Tanggal ----
        $tglAwal = $tglAkhir = null;
        if (!empty($tglrange)) {
            $parts = explode(' - ', $tglrange);
            if (count($parts) == 2) {
                $tglAwal  = date('Y-m-d', strtotime($parts[0]));
                $tglAkhir = date('Y-m-d', strtotime($parts[1]));
            }
        }
        if (empty($tglAwal) || empty($tglAkhir)) {
            exit('Tanggal wajib diisi.');
        }

        $bind = [
            $tglAwal,
            $tglAkhir,
            $cabang     ?: null,
            $kdprk      ?: null,
            $kdcustomer ?: null,
        ];

        $sql = $this->buildQueryPosisiPiutang();

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Posisi Piutang query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN POSISI PIUTANG');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Header (No dihilangkan di Excel, nomor urut tidak perlu)
        $headers = [
            'Kode Prk', 'Nama Prk',
            'Kode Customer', 'Nama Customer',
            'Saldo Awal', 'Debet', 'Kredit', 'Saldo Akhir'
        ];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:H3')->getFont()->setBold(true);
        $sheet->getStyle('A3:H3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum   = 4;
        $rowCount = 0;
        $lastCoa  = null;
        $subtotal = ['sa' => 0, 'd' => 0, 'k' => 0, 'akhir' => 0];

        while ($r = $query->getUnbufferedRow('array')) {

            // Ketika COA berubah → tulis TOTAL COA sebelumnya
            if ($lastCoa !== null && $lastCoa !== $r['kdprk']) {
                $sheet->fromArray(
                    ['', '', '', 'TOTAL ' . $lastCoa,
                    $subtotal['sa'], $subtotal['d'], $subtotal['k'], $subtotal['akhir']],
                    null, 'A' . $rowNum
                );
                $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
                $rowNum++;
                $subtotal = ['sa' => 0, 'd' => 0, 'k' => 0, 'akhir' => 0];
            }

            $sheet->fromArray([
                $r['kdprk'],
                $r['nmprk'],
                $r['kdcustomer'],
                $r['nmcustomer'],
                $r['saldo_awal'],
                $r['debet'],
                $r['kredit'],
                $r['saldo_akhir'],
            ], null, 'A' . $rowNum);

            $subtotal['sa']    += (float)$r['saldo_awal'];
            $subtotal['d']     += (float)$r['debet'];
            $subtotal['k']     += (float)$r['kredit'];
            $subtotal['akhir'] += (float)$r['saldo_akhir'];

            $lastCoa = $r['kdprk'];

            $rowNum++;
            $rowCount++;
        }

        // Total COA terakhir
        if ($lastCoa !== null) {
            $sheet->fromArray(
                ['', '', '', 'TOTAL ' . $lastCoa,
                $subtotal['sa'], $subtotal['d'], $subtotal['k'], $subtotal['akhir']],
                null, 'A' . $rowNum
            );
            $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFont()->setBold(true);
            $rowNum++;
        }

        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:H4');
        }

        // Auto-width
        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        // Format angka: E..H
        $firstData = 4;
        $lastData  = max(4, $rowNum - 1);
        foreach (['E','F','G','H'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A4');

        $namaFile = 'Laporan_PosisiPiutang_' . date('Ymd_His') . '.xlsx';
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
    LAPORAN UMUR PIUTANG (AR AGING)
   ========================================================= */
    public function umurpiutang()
    {
        $data = $this->commonData();
        $data['title']        = "Laporan Umur Piutang";
        $data['jenisLaporan'] = 'umurpiutang';
        $data['pagejs']       = 'report/report.js';
        return $this->template->render('report/trans/v_umurpiutang', $data);
    }

    private function buildQueryUmurPiutang()
    {
        // ============================================
        // TODO: ISI QUERY DARI ATASAN
        // ============================================
        // Kolom output yang diharapkan:
        // docno, kdcustomer, nmcustomer, kdprk, nmprk, docdate, tgljt, dk,
        // keterangan, currcode, kurs, nilai, nilai_belum_jt,
        // nilai_jt_1..4, umur, kodejob, namajob,
        // alamatcustomer, kotacustomer, kdsalesman, nmsalesman
        // ============================================

        $sql = "SELECT
                    '' AS docno,
                    '' AS kdcustomer,
                    '' AS nmcustomer,
                    '' AS kdprk,
                    '' AS nmprk,
                    NULL AS docdate,
                    NULL AS tgljt,
                    '' AS dk,
                    '' AS keterangan,
                    'IDR' AS currcode,
                    1 AS kurs,
                    0 AS nilai,
                    0 AS nilai_belum_jt,
                    0 AS nilai_jt_1,
                    0 AS nilai_jt_2,
                    0 AS nilai_jt_3,
                    0 AS nilai_jt_4,
                    0 AS umur,
                    '' AS kodejob,
                    '' AS namajob,
                    '' AS alamatcustomer,
                    '' AS kotacustomer,
                    '' AS kdsalesman,
                    '' AS nmsalesman
                WHERE 1=0";

        return rtrim($sql, " \t\n\r\0\x0B;");
    }

    public function previewLaporanUmurPiutang()
    {
        $tglrange   = $this->request->getPost('tglrange');
        $cabang     = $this->request->getPost('cabang');
        $kdcustomer = $this->request->getPost('kdcustomer');
        $kdprk      = $this->request->getPost('kdprk');
        $interval   = $this->request->getPost('interval') ?: 30;

        $page    = max(1, (int)($this->request->getPost('page') ?? 1));
        $perpage = (int)($this->request->getPost('perpage') ?? 25);
        if ($perpage < 1 || $perpage > 200) $perpage = 25;

        // ⚠️ SEMENTARA: query placeholder tanpa ? → bindBase kosong
        // Setelah query asli datang, isi: [$cabang, $kdcustomer, $kdprk, $interval]
        $bindBase = [];

        $sql = $this->buildQueryUmurPiutang();

        // COUNT
        $sqlCount = "SELECT COUNT(*) AS total FROM (" . $sql . ") x";
        $totalRow = $this->db->query($sqlCount, $bindBase)->getRowArray();
        $total    = (int)($totalRow['total'] ?? 0);
        $totalPage = $perpage > 0 ? (int)ceil($total / $perpage) : 0;

        // DATA
        $offset   = ($page - 1) * $perpage;
        $bindData = array_merge($bindBase, [$perpage, $offset]);
        $rows = $this->db->query($sql . " LIMIT ? OFFSET ?", $bindData)->getResultArray();

        return $this->response->setJSON([
            'status'     => 'ok',
            'data'       => $rows,
            'page'       => $page,
            'perpage'    => $perpage,
            'total'      => $total,
            'total_page' => $totalPage,
        ]);
    }

    public function downloadLaporanUmurPiutang()
    {
        $cabang     = $this->request->getPost('cabang');
        $kdcustomer = $this->request->getPost('kdcustomer');
        $kdprk      = $this->request->getPost('kdprk');
        $interval   = $this->request->getPost('interval') ?: 30;

        // ⚠️ SEMENTARA: kosong
        $bind = [];

        $sql = $this->buildQueryUmurPiutang();

        ini_set('memory_limit', '512M');
        set_time_limit(0);
        if (ob_get_length()) ob_end_clean();

        $query = $this->db->query($sql, $bind);
        if ($query === false) {
            log_message('error', 'Umur Piutang query failed: ' . print_r($this->db->error(), true));
            exit('Query gagal. Hubungi administrator.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'LAPORAN UMUR PIUTANG');
        $sheet->mergeCells('A1:X1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // ⚠️ PIUTANG: header CUSTOMER + Salesman
        $headers = [
            'No.Jurnal', 'Kode Customer', 'Nama Customer', 'Kode Prk', 'Nama Prk',
            'Tanggal', 'Tgl JT', 'DK', 'Keterangan', 'Mata Uang', 'Kurs',
            'Nilai', 'Nilai Belum JT',
            'Nilai JT 1', 'Nilai JT 2', 'Nilai JT 3', 'Nilai JT 4',
            'Umur', 'Kode Job', 'Nama Job',
            'Alamat Customer', 'Kota Customer',
            'Kode Salesman', 'Nama Salesman'
        ];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:X3')->getFont()->setBold(true);
        $sheet->getStyle('A3:X3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF');

        $rowNum = 4; $rowCount = 0;
        while ($r = $query->getUnbufferedRow('array')) {
            $sheet->fromArray([
                $r['docno'], $r['kdcustomer'], $r['nmcustomer'], $r['kdprk'], $r['nmprk'],
                $r['docdate'], $r['tgljt'], $r['dk'], $r['keterangan'],
                $r['currcode'], $r['kurs'],
                $r['nilai'], $r['nilai_belum_jt'],
                $r['nilai_jt_1'], $r['nilai_jt_2'], $r['nilai_jt_3'], $r['nilai_jt_4'],
                $r['umur'], $r['kodejob'], $r['namajob'],
                $r['alamatcustomer'], $r['kotacustomer'],
                $r['kdsalesman'], $r['nmsalesman']
            ], null, 'A' . $rowNum);
            $rowNum++; $rowCount++;
        }
        if ($rowCount === 0) {
            $sheet->setCellValue('A4', 'Tidak ada data.');
            $sheet->mergeCells('A4:X4');
        }

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->getColumnDimension($colLetter)->setWidth(max(12, min(35, mb_strlen($h) + 4)));
            $colLetter++;
        }

        // Format angka: K=Kurs, L=Nilai, M=Belum JT, N-Q=JT1..4, R=Umur
        $firstData = 4; $lastData = max(4, $rowNum - 1);
        foreach (['K','L','M','N','O','P','Q','R'] as $col) {
            $sheet->getStyle("{$col}{$firstData}:{$col}{$lastData}")
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->freezePane('A4');

        $namaFile = 'Laporan_UmurPiutang_' . date('Ymd_His') . '.xlsx';
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