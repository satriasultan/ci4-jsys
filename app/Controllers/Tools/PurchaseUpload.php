<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Models\Tools\M_PurchaseUpload;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PurchaseUpload extends BaseController
{
    private const PATH_PP = './assets/files/Purchaseorder/PP/';
    private const PATH_PO = './assets/files/Purchaseorder/PO/';
    private const MAX_LIST = 500;
    private const MAX_ERROR_DETAIL = 20;
    private const INSERT_CHUNK = 500;

    /** URL kanonik, mengikuti link menu I.T.D.1 / I.T.D.2 di sc_mst.menuprg. */
    private const URI_PP = 'tools/proses/uploadpp';
    private const URI_PO = 'tools/proses/uploadpo';

    /** Nama sheet sumber data pada file Template_Import_Outstanding_PP.xlsx. */
    private const PP_SHEET = 'template_import_pp';

    /** Header template PP sesuai sheet "template_import_pp" (kolom A..Q). */
    private $ppTemplateHeaders = [
        'No. PP', 'Jurnal', 'Tanggal', 'UserID', 'Kode Barang', 'Qty', 'Qty Realisasi',
        'Nama User', 'Nama Barang', 'Satuan', 'Spec', 'Keterangan PP Header',
        'Keterangan Detail', 'Tanggal Pakai', 'Job', 'Tanggal Dokumen PP (docdate)',
        'Estimasi Tanggal Pakai (estpakai)',
    ];

    /**
     * Alias header PP (nama header ternormalisasi) => kolom sc_im.lap_pp_win.
     * Nama kolom mentah tetap diterima agar template versi lama masih bisa dipakai.
     */
    private $ppHeaderAliases = [
        'nopp'                         => 'pp',
        'pp'                           => 'pp',
        'jurnal'                       => 'jurnal',
        'tanggal'                      => 'date_',
        'date'                         => 'date_',
        'userid'                       => 'username',
        'username'                     => 'username',
        'kodebarang'                   => 'brg',
        'brg'                          => 'brg',
        'qty'                          => 'qty',
        'qtyrealisasi'                 => 'qty_realisasi',
        'namauser'                     => 'nama_user',
        'namabarang'                   => 'nama_barang',
        'satuan'                       => 'unit',
        'unit'                         => 'unit',
        'spec'                         => 'spec',
        'keteranganppheader'           => 'rem_pp',
        'rempp'                        => 'rem_pp',
        'keterangandetail'             => 'rem_pd',
        'rempd'                        => 'rem_pd',
        'tanggalpakai'                 => 'date_pakai',
        'datepakai'                    => 'date_pakai',
        'job'                          => 'job',
        'tanggaldokumenppdocdate'      => 'docdate',
        'tanggaldokumenpp'             => 'docdate',
        'docdate'                      => 'docdate',
        'estimasitanggalpakaiestpakai' => 'estpakai',
        'estimasitanggalpakai'         => 'estpakai',
        'estpakai'                     => 'estpakai',
        'lns'                          => 'lns',
        'gdg'                          => 'gdg',
        'uniqueidpd'                   => 'uniqueidpd',
    ];

    /** Kolom PP yang dibaca dari file (posisi kolom mengikuti header, bukan urutan ini). */
    private $ppColumns = [
        'pp', 'jurnal', 'date_', 'username', 'brg', 'qty', 'qty_realisasi',
        'nama_user', 'nama_barang', 'unit', 'spec', 'rem_pp', 'rem_pd',
        'date_pakai', 'job', 'docdate', 'estpakai', 'lns', 'gdg', 'uniqueidpd',
    ];

    /** Header PP yang wajib ada pada file upload. */
    private $ppRequiredHeaders = [
        'pp', 'jurnal', 'date_', 'username', 'brg', 'qty', 'nama_user',
        'nama_barang', 'unit', 'rem_pp', 'rem_pd', 'date_pakai', 'job',
    ];

    /** Nilai PP yang wajib terisi pada setiap baris data. */
    private $ppRequired = ['pp', 'date_', 'brg'];

    /** Kolom PP bertipe NOT NULL: nilai kosong dikirim sebagai '' (bukan null). */
    private $ppNotNull = ['pp', 'jurnal', 'date_', 'brg', 'rem_pp', 'rem_pd', 'uniqueidpd'];

    /** Nama sheet sumber data pada file Template_Import_Outstanding_PO.xlsx. */
    private const PO_SHEET = 'template_import';

    /** Header template PO sesuai sheet "template_import" (kolom A..AB). */
    private $poTemplateHeaders = [
        'No. PO', 'Keterangan PO', 'Kode Supplier', 'Nama Supplier', 'Kode Barang', 'Nama Barang',
        'Keterangan Detail', 'Qty PO', 'Satuan', 'Harga PO', 'Total Price', 'Real Price / Nilai',
        'PPN / NON PPN', 'Mata Uang', 'Kurs', 'Category', 'Tahun PO', 'ETD / Tanggal Kirim',
        'Tanggal PO (docdate)', 'Tanggal Kirim Header (senddate)', 'Cabang (PA JTS1, PB JTS2)',
        'User Pemohon', 'Exclude/Include', 'Nilai DPP', 'Nilai Pajak',
        'Nilai Konversi Ke IDR (jika menggunakan kurs)', 'Batch', 'ETD Notes',
    ];

    /**
     * Alias header PO (nama header ternormalisasi) => kolom sc_im.lap_po_win.
     * Nama kolom mentah tetap diterima agar template versi lain masih bisa dipakai.
     */
    private $poHeaderAliases = [
        'nopo'                                 => 'no_po',
        'keteranganpo'                         => 'keterangan_po',
        'kodesupplier'                         => 'kode_supplier',
        'namasupplier'                         => 'nama_supplier',
        'kodebarang'                           => 'kode_barang',
        'namabarang'                           => 'nama_barang',
        'keterangandetail'                     => 'keterangan_detail',
        'qtypo'                                => 'qty_po',
        'qty'                                  => 'qty_po',
        'satuan'                               => 'satuan',
        'unit'                                 => 'satuan',
        'hargapo'                              => 'harga_po',
        'harga'                                => 'harga_po',
        'totalprice'                           => 'total_price',
        'realpricenilai'                       => 'real_price',
        'realprice'                            => 'real_price',
        'nilai'                                => 'real_price',
        'ppnnonppn'                            => 'ppn_nonppn',
        'ppn'                                  => 'ppn_nonppn',
        'matauang'                             => 'mata_uang',
        'currency'                             => 'mata_uang',
        'kurs'                                 => 'kurs',
        'category'                             => 'category',
        'tahunpo'                              => 'tahun_po',
        'tahun'                                => 'tahun_po',
        'etdtanggalkirim'                      => 'etd',
        'etd'                                  => 'etd',
        'tanggalkirim'                         => 'etd',
        'tanggalpodocdate'                     => 'docdate',
        'tanggalpo'                            => 'docdate',
        'docdate'                              => 'docdate',
        'tanggalkirimheadersenddate'           => 'senddate',
        'senddate'                             => 'senddate',
        'cabangpajts1pbjts2'                   => 'cabang',
        'cabang'                               => 'cabang',
        'userpemohon'                          => 'user_pemohon',
        'pemohon'                              => 'user_pemohon',
        'excludeinclude'                       => 'exclude_include',
        'nilaidpp'                             => 'nilai_dpp',
        'dpp'                                  => 'nilai_dpp',
        'nilaipajak'                           => 'nilai_pajak',
        'pajak'                                => 'nilai_pajak',
        'nilaikonversikeidrjikamenggunakankurs' => 'nilai_konversi',
        'nilaikonversi'                        => 'nilai_konversi',
        'batch'                                => 'batch',
        'etdnotes'                             => 'etdnotes',
        'notes'                                => 'etdnotes',
        'uniqueidpo'                           => 'uniqueidpo',
    ];

    /** Kolom template PO (tanpa id dan imported_at). */
    private $poColumns = [
        'no_po', 'keterangan_po', 'kode_supplier', 'nama_supplier', 'kode_barang', 'nama_barang',
        'keterangan_detail', 'qty_po', 'satuan', 'harga_po', 'total_price', 'real_price',
        'ppn_nonppn', 'mata_uang', 'kurs', 'category', 'tahun_po', 'etd', 'docdate', 'senddate',
        'cabang', 'user_pemohon', 'exclude_include', 'nilai_dpp', 'nilai_pajak', 'nilai_konversi',
        'batch', 'etdnotes', 'uniqueidpo',
    ];

    /** Header PO yang wajib ada pada file upload. */
    private $poRequiredHeaders = [
        'no_po', 'kode_supplier', 'nama_supplier', 'kode_barang', 'nama_barang', 'qty_po',
        'satuan', 'harga_po', 'total_price', 'ppn_nonppn', 'mata_uang', 'kurs', 'category',
        'tahun_po', 'etd', 'docdate', 'senddate', 'cabang', 'user_pemohon', 'exclude_include',
    ];

    /** Nilai PO yang wajib terisi pada setiap baris data. */
    private $poRequired = ['no_po', 'kode_barang', 'qty_po'];

    /** Kolom PO bertipe NOT NULL: nilai kosong dikirim sebagai '' (bukan null). */
    private $poNotNull = ['no_po', 'kode_barang'];

    /** Kolom PO bertipe angka: nilai kosong dikirim sebagai null dan divalidasi numerik. */
    private $poNumeric = [
        'qty_po', 'harga_po', 'total_price', 'real_price',
        'kurs', 'tahun_po', 'nilai_dpp', 'nilai_pajak', 'nilai_konversi',
    ];

    /** [nama kolom => wajib diisi] */
    private $ppDateColumns = ['date_' => true, 'date_pakai' => false, 'docdate' => false, 'estpakai' => false];
    private $poDateColumns = ['etd' => false, 'docdate' => false, 'senddate' => false];


    /** @var M_PurchaseUpload|null */
    private $m_purchaseupload;

    public function index()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        $data = $this->pageData('I.T.D.1', 'I.T.D.1/01', 'Upload PP / PO / Saldo Awal Persediaan');

        return $this->template->render('tools/import/v_index', $data);
    }

    public function uploadpp()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getGet('template') !== null) {
            return $this->downloadTemplate('pp');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $this->handleUpload('pp');

            return redirect()->to(base_url(self::URI_PP));
        }

        $data = $this->pageData('I.T.D.1', 'I.T.D.1/01', 'Upload PP (Permintaan Pembelian)');
        $data['list_detail']    = $this->model()->q_lap_pp_win('', self::MAX_LIST)->getResult();
        $data['header_columns'] = $this->ppTemplateHeaders;
        $data['sheet_name']     = self::PP_SHEET;
        $data['upload_url']     = base_url(self::URI_PP);
        $data['template_url']   = base_url(self::URI_PP . '?template=1');
        $data['import_url']     = base_url(self::URI_PP . '/import');
        $data['sync_summary']   = $this->model()->q_pp_sync_summary();

        return $this->template->render('tools/import/v_pp_upload', $data);
    }

    /**
     * Tombol halaman upload PP: jalankan CALL sc_im.sp_import_lap_pp_to_trx().
     */
    public function importpptotrx()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url(self::URI_PP));
        }

        try {
            $result = $this->model()->exe_import_pp_to_trx();

            $this->setFlash('success',
                'Prosedur <b>sc_im.sp_import_lap_pp_to_trx()</b> selesai dalam <b>' . $result['elapsed_ms'] . ' ms</b>. '
                . 'Header PP baru: <b>' . $result['header'] . '</b>, detail PP baru: <b>' . $result['detail'] . '</b>. '
                . 'Total sekarang: sc_trx.pp = <b>' . $result['header_total'] . '</b>, sc_trx.pp_dtl = <b>'
                . $result['detail_total'] . '</b>.');
        } catch (\Throwable $e) {
            $this->setFlash('danger',
                'Eksekusi <b>sc_im.sp_import_lap_pp_to_trx()</b> gagal: <b>'
                . esc($this->cleanDbMessage($e->getMessage())) . '</b>');
        }

        return redirect()->to(base_url(self::URI_PP));
    }

    public function uploadpo()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getGet('template') !== null) {
            return $this->downloadTemplate('po');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $this->handleUpload('po');

            return redirect()->to(base_url(self::URI_PO));
        }

        $data = $this->pageData('I.T.D.2', 'I.T.D.2/01', 'Upload PO (Purchase Order)');
        $data['list_detail']    = $this->model()->q_lap_po_win('', self::MAX_LIST)->getResult();
        $data['header_columns'] = $this->poTemplateHeaders;
        $data['sheet_name']     = self::PO_SHEET;
        $data['upload_url']     = base_url(self::URI_PO);
        $data['template_url']   = base_url(self::URI_PO . '?template=1');
        $data['import_url']     = base_url(self::URI_PO . '/import');
        $data['sync_summary']   = $this->model()->q_po_sync_summary();

        return $this->template->render('tools/import/v_po_upload', $data);
    }

    /**
     * Tombol halaman upload PO: jalankan CALL sc_im.sp_import_template_po().
     */
    public function importpototrx()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url(self::URI_PO));
        }

        try {
            $result = $this->model()->exe_import_po_to_trx();

            $this->setFlash('success',
                'Prosedur <b>sc_im.sp_import_template_po()</b> selesai dalam <b>' . $result['elapsed_ms'] . ' ms</b>. '
                . 'Header PO baru: <b>' . $result['header'] . '</b>, detail PO baru: <b>' . $result['detail'] . '</b>. '
                . 'Total sekarang: sc_trx.po = <b>' . $result['header_total'] . '</b>, sc_trx.po_dtl = <b>'
                . $result['detail_total'] . '</b>.');
        } catch (\Throwable $e) {
            $this->setFlash('danger',
                'Eksekusi <b>sc_im.sp_import_template_po()</b> gagal: <b>'
                . esc($this->cleanDbMessage($e->getMessage())) . '</b>');
        }

        return redirect()->to(base_url(self::URI_PO));
    }

    private function model(): M_PurchaseUpload
    {
        if ($this->m_purchaseupload === null) {
            $this->m_purchaseupload = new M_PurchaseUpload();
        }

        return $this->m_purchaseupload;
    }

    /**
     * Data standar header halaman (breadcrumb, versi menu, hak akses).
     */
    private function pageData(string $kodemenu, string $versirelease, string $title): array
    {
        $nama        = trim((string) $this->session->get('nama'));
        $role        = trim((string) $this->session->get('roleid'));
        $releasedate = date('Y-m-d H:i:s');

        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x       = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);

        return [
            'title'     => $title,
            'x'         => $x['rows'],
            'y'         => $x['res'],
            't'         => $x['xn'],
            'kodemenu'  => $kodemenu,
            'version'   => $versidb,
            'dtl_akses' => $this->m_role->detail_user_akses($role, $kodemenu)->getRowArray(),
        ];
    }

    /**
     * Proses upload file XLS/XLSX. Hasil proses dikirim lewat flashdata.
     */
    private function handleUpload(string $type): void
    {
        $isPp = $type === 'pp';

        if ($isPp) {
            // Sheet outstanding PP berisi +/- 12.000 baris: longgarkan batas waktu & memori.
            @set_time_limit(300);
            @ini_set('memory_limit', '512M');
        } else {
            // File PO outstanding juga bisa berisi ribuan baris.
            @set_time_limit(300);
            @ini_set('memory_limit', '512M');
        }

        $file = $this->request->getFile('import');
        if ($file === null || ! $file->isValid()) {
            $this->setFlash('danger', 'File belum dipilih atau gagal di-upload. Silahkan coba lagi.');

            return;
        }

        if (! $this->validate([
            'import' => 'uploaded[import]|ext_in[import,xls,xlsx]|max_size[import,10240]',
        ])) {
            $this->setFlash('danger', 'Validasi file gagal: ' . implode(' ', $this->validator->getErrors()));

            return;
        }

        $dir = $isPp ? self::PATH_PP : self::PATH_PO;
        if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir)) {
            $this->setFlash('danger', 'Folder upload tidak dapat dibuat: ' . esc($dir));

            return;
        }

        $clientName = $file->getClientName();
        $safeName   = date('YmdHis') . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $clientName);

        try {
            $file->move($dir, $safeName);
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'File gagal disimpan: ' . esc($e->getMessage()));

            return;
        }

        $fullPath = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

        try {
            [$sheetName, $rows] = $this->readSpreadsheet($fullPath, $isPp ? self::PP_SHEET : self::PO_SHEET);
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'File tidak dapat dibaca: ' . esc($e->getMessage()));

            return;
        }

        $columns  = $isPp ? $this->ppColumns : $this->poColumns;
        $required = $isPp ? $this->ppRequired : $this->poRequired;
        $dates    = $isPp ? $this->ppDateColumns : $this->poDateColumns;
        $notNull  = $isPp ? $this->ppNotNull : $this->poNotNull;
        $aliases  = $isPp ? $this->ppHeaderAliases : $this->poHeaderAliases;
        $mustHave = $isPp ? $this->ppRequiredHeaders : $this->poRequiredHeaders;

        // Template outstanding PP/PO memakai nama header ramah pengguna (bukan nama kolom).
        $headerOffset = $this->locateHeaderRow($rows, $aliases, $mustHave);
        $headerMap    = $this->mapHeaderAliases($rows[$headerOffset] ?? [], $aliases);
        $missing      = array_values(array_diff($mustHave, array_keys($headerMap)));

        if ($missing !== []) {
            $this->setFlash('danger', 'Header template tidak sesuai pada sheet <b>' . esc($sheetName)
                . '</b>. Kolom berikut tidak ditemukan pada baris header (baris ' . ($headerOffset + 1) . '): <b>'
                . implode(', ', array_map('esc', $missing)) . '</b>');

            return;
        }

        $insertMethod = $isPp ? 'ins_lap_pp_win_batch' : 'ins_lap_po_win_batch';
        $nama         = trim((string) $this->session->get('nama'));
        $now          = date('Y-m-d H:i:s');

        $total     = 0;
        $inserted  = 0;
        $duplicate = 0;
        $invalid   = 0;
        $errors    = [];
        $records   = [];

        $this->db->transBegin();

        try {
            foreach (array_slice($rows, $headerOffset + 1) as $index => $row) {
                $excelRow = $index + $headerOffset + 2;
                $values   = [];
                $isEmpty  = true;

                foreach ($columns as $column) {
                    $position = $headerMap[$column] ?? null;

                    // Kolom opsional (mis. qty_realisasi/docdate/estpakai) boleh tidak ada di file.
                    $value = $position === null ? '' : $this->normalizeValue($row[$position] ?? null);

                    $values[$column] = $value;

                    if ($value !== '') {
                        $isEmpty = false;
                    }
                }

                if ($isEmpty) {
                    continue;
                }

                $total++;
                $rowErrors = [];

                foreach ($dates as $column => $isRequired) {
                    [$valid, $dateValue] = $this->normalizeDate($values[$column] ?? '', $isRequired);
                    if (! $valid) {
                        $rowErrors[] = "kolom {$column} bukan tanggal yang valid";
                    }
                    $values[$column] = $dateValue;
                }

                foreach ($required as $column) {
                    if (($values[$column] ?? null) === null || $values[$column] === '') {
                        $rowErrors[] = "kolom {$column} wajib diisi";
                    }
                }

                if (! $isPp) {
                    foreach ($this->poNumeric as $column) {
                        $value = $values[$column] ?? '';

                        if ($value === '') {
                            continue;
                        }

                        $number = $this->normalizeNumber($value);

                        if ($number === null) {
                            $rowErrors[] = "kolom {$column} bukan angka";
                            continue;
                        }

                        // tahun_po bertipe integer pada sc_im.lap_po_win.
                        $values[$column] = $column === 'tahun_po'
                            ? (string) (int) (float) $number
                            : $number;
                    }
                }

                if ($rowErrors !== []) {
                    $invalid++;
                    if (count($errors) < self::MAX_ERROR_DETAIL) {
                        $errors[] = 'Baris ' . $excelRow . ': ' . implode(', ', $rowErrors);
                    }
                    continue;
                }

                $record = $this->buildRecord($values, $columns, $notNull, $isPp);
                $record['status']    = 'I';
                $record['inputby']   = $nama;
                $record['inputdate'] = $now;

                $records[] = $record;

                if (count($records) >= self::INSERT_CHUNK) {
                    [$saved, $skipped] = $this->insertRecords($insertMethod, $records);
                    $inserted += $saved;
                    $duplicate += $skipped;
                    $records = [];
                }
            }

            if ($records !== []) {
                [$saved, $skipped] = $this->insertRecords($insertMethod, $records);
                $inserted += $saved;
                $duplicate += $skipped;
                $records = [];
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            $this->setFlash('danger', 'Proses upload gagal: ' . esc($e->getMessage()));

            return;
        }

        $lines = [
            'Upload ' . strtoupper($type) . ' dari file <b>' . esc($clientName) . '</b>'
                . ' (sheet <b>' . esc($sheetName) . '</b>) selesai.',
            'Total baris data: <b>' . $total . '</b>, berhasil disimpan: <b>' . $inserted
                . '</b>, duplikat/dilewati: <b>' . $duplicate . '</b>, tidak valid: <b>' . $invalid . '</b>.',
        ];

        if ($total === 0) {
            $lines[] = 'Tidak ada baris data yang dibaca. Pastikan baris 1 adalah header dan data dimulai baris 2.';
        }

        if ($isPp) {
            $notStored = array_values(array_diff(
                ['qty_realisasi', 'docdate', 'estpakai', 'lns', 'gdg'],
                $this->model()->available_lap_pp_columns()
            ));

            if ($notStored !== []) {
                $lines[] = 'Catatan: kolom <b>' . implode(', ', array_map('esc', $notStored))
                    . '</b> belum ada pada tabel sc_im.lap_pp_win sehingga nilainya tidak disimpan.';
            }
        }

        if ($errors !== []) {
            $lines[] = '<b>Detail baris tidak valid:</b>';
            foreach ($errors as $error) {
                $lines[] = esc($error);
            }
        }

        $this->setFlash(($invalid > 0 || $total === 0) ? 'warning' : 'success', implode('<br>', $lines));
    }

    /**
     * Baca satu sheet (default: sheet pertama) sebagai array baris x kolom (nilai apa adanya).
     *
     * @return array{0: string, 1: array} [nama sheet, baris]
     */
    private function readSpreadsheet(string $filePath, ?string $preferredSheet = null): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);

        $sheetName = null;

        if ($preferredSheet !== null && method_exists($reader, 'setLoadSheetsOnly')) {
            $available = method_exists($reader, 'listWorksheetNames')
                ? $reader->listWorksheetNames($filePath)
                : [];

            // Baca hanya sheet yang diminta supaya file besar (sheet petunjuk) tidak ikut dimuat.
            if ($available === [] || in_array($preferredSheet, $available, true)) {
                $reader->setLoadSheetsOnly([$preferredSheet]);
                $sheetName = $preferredSheet;
            }
        }

        $spreadsheet = $reader->load($filePath);
        $worksheet   = $sheetName === null ? null : $spreadsheet->getSheetByName($sheetName);

        if ($worksheet === null) {
            $worksheet = $spreadsheet->getSheet(0);
            $sheetName = $worksheet->getTitle();
        }

        $rows = $worksheet->toArray(null, true, false, false);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $reader);

        return [$sheetName, $rows];
    }

    /**
     * Petakan header template ke posisi kolom memakai daftar alias.
     */
    private function mapHeaderAliases(array $headerRow, array $aliases): array
    {
        $map = [];

        foreach ($headerRow as $position => $label) {
            $column = $aliases[$this->normalizeHeader($label)] ?? null;

            if ($column !== null && ! array_key_exists($column, $map)) {
                $map[$column] = $position;
            }
        }

        return $map;
    }

    /**
     * Cari baris header: baris (maks. 10 baris awal) dengan jumlah header wajib terbanyak.
     */
    private function locateHeaderRow(array $rows, array $aliases, array $required): int
    {
        $best      = 0;
        $bestScore = 0;

        foreach (array_slice($rows, 0, 10) as $index => $row) {
            $found = array_intersect($required, array_keys($this->mapHeaderAliases($row, $aliases)));
            $score = count($found);

            if ($score > $bestScore) {
                $best      = $index;
                $bestScore = $score;
            }
        }

        return $best;
    }

    /**
     * Susun satu baris tabel dari nilai template.
     */
    private function buildRecord(array $values, array $columns, array $notNull, bool $isPp): array
    {
        $record = [];

        foreach ($columns as $column) {
            $value = $values[$column] ?? '';

            // Kolom NOT NULL tetap dikirim sebagai string kosong agar insert tidak gagal.
            $record[$column] = ($value === '' || $value === null) && ! in_array($column, $notNull, true)
                ? null
                : (string) $value;
        }

        if ($isPp && ($record['uniqueidpd'] ?? '') === '') {
            // Kunci unik detail PP dibuat dari kolom kunci agar upload ulang bersifat idempoten.
            $record['uniqueidpd'] = md5(implode('|', [
                (string) $record['pp'],
                (string) $record['jurnal'],
                (string) $record['date_'],
                (string) $record['brg'],
                (string) $record['rem_pp'],
                (string) $record['rem_pd'],
            ]));
        }

        if (! $isPp) {
            // Kunci idempotensi staging PO, memakai rumus yang sama dengan
            // sc_im.sp_import_template_po(): md5(no_po|kode_barang|keterangan_detail|batch|etd).
            $record['uniqueidpo'] = md5(implode('|', [
                (string) ($record['no_po'] ?? ''),
                (string) ($record['kode_barang'] ?? ''),
                (string) ($record['keterangan_detail'] ?? ''),
                (string) ($record['batch'] ?? ''),
                (string) ($record['etd'] ?? ''),
            ]));
        }

        return $record;
    }

    /**
     * Simpan satu potongan baris sekaligus.
     *
     * @return array{0: int, 1: int} [tersimpan, dilewati]
     */
    private function insertRecords(string $insertMethod, array $records): array
    {
        $result = $this->model()->{$insertMethod}($records);

        return [(int) ($result['inserted'] ?? 0), (int) ($result['skipped'] ?? 0)];
    }

    private function normalizeHeader($value): string
    {
        $value = strtolower(trim((string) $value));

        return preg_replace('/[^a-z0-9]+/', '', $value);
    }

    /**
     * Normalisasi nilai sel menjadi string (angka bulat tidak memakai desimal).
     */
    private function normalizeValue($value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_float($value)) {
            if (is_finite($value) && floor($value) === $value && abs($value) < PHP_INT_MAX) {
                return (string) (int) $value;
            }

            return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');
        }

        if (is_int($value)) {
            return (string) $value;
        }

        return trim((string) $value);
    }

    /**
     * Ubah nilai sel menjadi tanggal Y-m-d.
     *
     * @return array{0: bool, 1: string|null} [valid, nilai]
     */
    private function normalizeDate(string $value, bool $required): array
    {
        $value = trim($value);

        if ($value === '') {
            return [$required ? false : true, null];
        }

        // Excel serial date
        if (is_numeric($value) && (float) $value > 0 && (float) $value < 100000) {
            try {
                return [true, Date::excelToDateTimeObject((float) $value)->format('Y-m-d')];
            } catch (\Throwable $e) {
                // lanjut ke percobaan format teks
            }
        }

        $clean = trim(preg_replace('/\s+/', ' ', $value));

        foreach (['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd.m.Y', 'd M Y', 'd F Y', 'Ymd'] as $format) {
            $date = \DateTime::createFromFormat('!' . $format, $clean);
            $last = \DateTime::getLastErrors();

            if ($date !== false && ($last === false || ($last['warning_count'] === 0 && $last['error_count'] === 0))) {
                return [true, $date->format('Y-m-d')];
            }
        }

        $timestamp = strtotime($clean);
        if ($timestamp !== false) {
            return [true, date('Y-m-d', $timestamp)];
        }

        return [false, null];
    }

    /**
     * Ubah teks angka (termasuk format ribuan Indonesia) menjadi string numerik.
     * Mengembalikan null bila nilai tidak dapat dibaca sebagai angka.
     */
    private function normalizeNumber(string $value): ?string
    {
        $value = str_replace([' ', "\u{00A0}"], '', trim($value));

        if ($value === '') {
            return null;
        }

        // 1.234.567,89 (format Indonesia) => 1234567.89
        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        } elseif (substr_count($value, ',') === 1 && strpos($value, '.') === false) {
            // 1,5 (koma desimal) => 1.5
            $value = str_replace(',', '.', $value);
        } else {
            // 16,800.00 (format Inggris) => 16800.00
            $value = str_replace(',', '', $value);
        }

        return is_numeric($value) ? $value : null;
    }



    /**
     * Kirim template XLSX (hanya baris header) ke browser.
     */
    private function downloadTemplate(string $type)
    {
        $columns     = $type === 'pp' ? $this->ppTemplateHeaders : $this->poTemplateHeaders;
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($type === 'pp' ? self::PP_SHEET : self::PO_SHEET);
        $sheet->fromArray($columns, null, 'A1');


        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9E1F2');
        $sheet->freezePane('A2');

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'template_' . $type . '_');
        if ($tempFile === false) {
            throw new \RuntimeException('Tidak dapat membuat file template.');
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        @unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_upload_' . $type . '.xlsx"')
            ->setBody($content);
    }

    private function setFlash(string $type, string $message): void
    {
        $this->session->setFlashdata('message', '<div class="alert alert-' . $type . '">' . $message . '</div>');
    }

    /**
     * Rapatkan pesan error database menjadi satu baris pendek agar nyaman di-flash.
     */
    private function cleanDbMessage(string $message): string
    {
        $message = trim((string) preg_replace('/\s+/', ' ', strip_tags($message)));

        return strlen($message) > 400 ? substr($message, 0, 400) . '...' : $message;
    }
}

