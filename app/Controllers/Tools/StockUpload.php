<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Models\Tools\M_StockUpload;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockUpload extends BaseController
{
    private const PATH_STOCK = './assets/files/Stock/';
    private const MAX_LIST = 500;
    private const MAX_ERROR_DETAIL = 20;
    private const INSERT_CHUNK = 500;

    /** URL kanonik halaman upload saldo awal persediaan (nama menu I.T.D.3). */
    private const URI_STOCK = 'tools/proses/uploadStockAwal';

    /** Nama sheet sumber data pada file Template_Import_Stock_Persediaan.xlsx. */
    private const STOCK_SHEET = 'template_stock_persediaan';

    /** Nilai bawaan kolom tanggal saldo bila sel tanggal dikosongkan. */
    private const TANGGAL_DEFAULT = '2026-10-31';

    /**
     * Header template saldo awal persediaan (kolom A..N).
     * Mengikuti nama kolom pada Template_Import_Stock_Persediaan.xlsx
     * (sheet "template_stock_persediaan"). Header versi ramah pengguna seperti
     * "Tanggal Saldo" / "Cabang (idbranch)" tetap diterima lewat daftar alias.
     */
    private $stockTemplateHeaders = [
        'tanggal_saldo', 'idbranch', 'cabang', 'kode_gudang', 'nama_gudang',
        'kode_barang', 'nama_barang', 'satuan', 'batch_spec',
        'qty_saldo', 'nilai_saldo', 'harga_rata_rata', 'keterangan', 'status_validasi',
    ];

    /**
     * Alias header (nama header ternormalisasi) => kolom sc_im.stock_persediaan_opening.
     * Nama kolom mentah tetap diterima agar template versi lain masih bisa dipakai.
     */
    private $stockHeaderAliases = [
        'tanggalsaldo'  => 'tanggal_saldo',
        'tanggal'       => 'tanggal_saldo',
        'idbranch'      => 'idbranch',
        'cabang'        => 'idbranch',
        'cabangidbranch'=> 'idbranch',
        'kodecabang'    => 'idbranch',
        'namacabang'    => 'cabang',
        'kodegudang'    => 'kode_gudang',
        'gudang'        => 'kode_gudang',
        'namagudang'    => 'nama_gudang',
        'kodebarang'    => 'kode_barang',
        'barang'        => 'kode_barang',
        'namabarang'    => 'nama_barang',
        'satuan'        => 'satuan',
        'unit'          => 'satuan',
        'batchspec'     => 'batch_spec',
        'batch'         => 'batch_spec',
        'spec'          => 'batch_spec',
        'qtysaldo'      => 'qty_saldo',
        'qty'           => 'qty_saldo',
        'nilaisaldo'    => 'nilai_saldo',
        'nilai'         => 'nilai_saldo',
        'hargaratarata' => 'harga_rata_rata',
        'harga'         => 'harga_rata_rata',
        'keterangan'    => 'keterangan',
        'catatan'       => 'keterangan',
        'statusvalidasi'=> 'status_validasi',
        'status'        => 'status_validasi',
    ];

    /** Kolom staging yang dibaca dari file (posisi mengikuti header, bukan urutan ini). */
    private $stockColumns = [
        'tanggal_saldo', 'idbranch', 'cabang', 'kode_gudang', 'nama_gudang',
        'kode_barang', 'nama_barang', 'satuan', 'batch_spec',
        'qty_saldo', 'nilai_saldo', 'harga_rata_rata', 'keterangan', 'status_validasi',
    ];

    /** Header yang wajib ada pada file upload. */
    private $stockRequiredHeaders = [
        'tanggal_saldo', 'idbranch', 'kode_gudang', 'kode_barang', 'qty_saldo',
    ];

    /** Nilai yang wajib terisi pada setiap baris data. */
    private $stockRequired = [
        'idbranch', 'kode_gudang', 'kode_barang', 'qty_saldo',
    ];

    /** Kolom angka (format ribuan Indonesia ikut dinormalisasi). */
    private $stockNumeric = ['qty_saldo', 'nilai_saldo', 'harga_rata_rata'];

    /** Kolom tanggal: nama kolom => wajib diisi (kosong berarti TANGGAL_DEFAULT). */
    private $stockDateColumns = ['tanggal_saldo' => false];

    /** Kolom NOT NULL: nilai kosong dikirim sebagai '' (bukan null). */
    private $stockNotNull = [
        'tanggal_saldo', 'idbranch', 'kode_gudang', 'kode_barang',
        'qty_saldo', 'nilai_saldo', 'status_validasi',
    ];

    /** @var M_StockUpload|null */
    private $m_stockupload;

    public function uploadstock()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getGet('template') !== null) {
            return $this->downloadTemplate();
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $this->handleUpload();

            return redirect()->to(base_url($this->pageUri()));
        }

        $data = $this->pageData('I.T.D.3', 'I.T.D.3/01', 'Upload Saldo Awal Persediaan');

        $model = $this->model();
        $uri   = $this->pageUri();

        $data['list_detail']    = $model->q_stock_opening('', self::MAX_LIST)->getResult();
        $data['header_columns'] = $this->stockTemplateHeaders;
        $data['sheet_name']     = self::STOCK_SHEET;
        $data['upload_url']     = base_url($uri);
        $data['template_url']   = base_url($uri . '?template=1');
        $data['import_url']     = base_url($uri . '/import');
        $data['validasi_url']   = base_url($uri . '/validasi');
        $data['sync_summary']   = $model->q_stock_summary();

        return $this->template->render('tools/import/v_stock_upload', $data);
    }

    /**
     * Tombol "Validasi Staging": jalankan sc_im.fn_validasi_stock_persediaan_opening().
     */
    public function validasistock()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url($this->pageUri()));
        }

        // Validasi memproses seluruh staging sekaligus; beri waktu lebih dari
        // default php.ini agar upload besar (ribuan baris) tidak terpotong.
        set_time_limit(1200);

        try {
            $result = $this->model()->exe_validasi_stock_opening();

            $this->setFlash('success',
                'Validasi <b>sc_im.fn_validasi_stock_persediaan_opening()</b> selesai dalam <b>'
                . $result['elapsed_ms'] . ' ms</b>. '
                . 'Total baris: <b>' . $result['total'] . '</b>, VALID: <b>' . $result['valid'] . '</b>, '
                . 'DRAFT: <b>' . $result['draft'] . '</b>, POSTED: <b>' . $result['posted'] . '</b>, '
                . 'bermasalah: <b>' . $result['bermasalah'] . '</b>. '
                . 'Qty: <b>' . number_format($result['qty'], 4, ',', '.') . '</b>, '
                . 'nilai: <b>' . number_format($result['nilai'], 2, ',', '.') . '</b>.');
        } catch (\Throwable $e) {
            $this->setFlash('danger',
                'Validasi gagal: <b>' . esc($this->cleanDbMessage($e->getMessage())) . '</b>');
        }

        return redirect()->to(base_url($this->pageUri()));
    }

    /**
     * Tombol "Import ke Transaksi": jalankan sc_im.fn_posting_stock_persediaan_opening().
     * Baris VALID diposting ke sc_trx.stkblc lalu ditandai POSTED.
     */
    public function importstocktotrx()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url($this->pageUri()));
        }

        $nama = trim((string) $this->session->get('nama'));

        // Posting menyentuh ribuan baris sc_trx.stkblc beserta trigger avgcost,
        // jadi butuh waktu jauh lebih lama daripada batas default php.ini.
        set_time_limit(1200);

        try {
            $result = $this->model()->exe_posting_stock_opening($nama);

            $this->setFlash('success',
                'Prosedur <b>sc_im.fn_posting_stock_persediaan_opening()</b> selesai dalam <b>'
                . $result['elapsed_ms'] . ' ms</b>. '
                . 'Baris VALID: <b>' . $result['calon'] . '</b>, diposting ke <code>sc_trx.stkblc</code>: <b>'
                . $result['inserted'] . '</b>, dilewati: <b>' . $result['skipped'] . '</b>. '
                . 'Total baris berstatus POSTED: <b>' . $result['posted'] . '</b>, '
                . 'masih VALID (belum terposting): <b>' . $result['pending'] . '</b>.');
        } catch (\Throwable $e) {
            $this->setFlash('danger',
                'Eksekusi <b>sc_im.fn_posting_stock_persediaan_opening()</b> gagal: <b>'
                . esc($this->cleanDbMessage($e->getMessage())) . '</b>');
        }

        return redirect()->to(base_url($this->pageUri()));
    }

    /**
     * URL halaman yang sedang dipakai pengguna.
     *
     * Menu I.T.D.3 memakai jalur lama 'tools/proses/uploadStockAwal', sedangkan
     * grup route '/tools/import' memakai 'tools/import/uploadstock'. Keduanya
     * menuju controller yang sama; redirect & form action harus mengikuti jalur
     * asal supaya pengguna tidak terlempar ke halaman lain.
     */
    private function pageUri(): string
    {
        $path = strtolower(trim((string) $this->request->getPath(), '/'));

        if (strpos($path, 'tools/import') !== false) {
            return 'tools/import/uploadstock';
        }

        return self::URI_STOCK;
    }

    private function model(): M_StockUpload
    {
        if ($this->m_stockupload === null) {
            $this->m_stockupload = new M_StockUpload();
        }

        return $this->m_stockupload;
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
     * Proses upload file XLS/XLSX saldo awal persediaan. Hasil proses dikirim lewat flashdata.
     */
    private function handleUpload(): void
    {
        // File saldo awal bisa berisi ribuan baris: longgarkan batas waktu & memori.
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

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

        $dir = self::PATH_STOCK;
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
            [$sheetName, $rows] = $this->readSpreadsheet($fullPath, self::STOCK_SHEET);
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'File tidak dapat dibaca: ' . esc($e->getMessage()));

            return;
        }

        // Template saldo awal memakai nama header ramah pengguna (bukan nama kolom).
        $headerOffset = $this->locateHeaderRow($rows, $this->stockHeaderAliases, $this->stockRequiredHeaders);
        $headerMap    = $this->mapHeaderAliases($rows[$headerOffset] ?? [], $this->stockHeaderAliases);
        $missing      = array_values(array_diff($this->stockRequiredHeaders, array_keys($headerMap)));

        if ($missing !== []) {
            $this->setFlash('danger', 'Header template tidak sesuai pada sheet <b>' . esc($sheetName)
                . '</b>. Kolom berikut tidak ditemukan pada baris header (baris ' . ($headerOffset + 1) . '): <b>'
                . implode(', ', array_map('esc', $missing)) . '</b>');

            return;
        }

        $nama = trim((string) $this->session->get('nama'));
        $now  = date('Y-m-d H:i:s');

        $total        = 0;
        $inserted     = 0;
        $duplicate    = 0;
        $merged       = 0;
        $invalid      = 0;
        $rekonsiliasi = 0;
        $errors       = [];
        $duplicateNotes = [];
        $seenKeys     = [];
        $mergedKeys   = [];
        $records      = [];

        $this->db->transBegin();


        try {
            foreach (array_slice($rows, $headerOffset + 1) as $index => $row) {
                $excelRow = $index + $headerOffset + 2;
                $values   = [];
                $numericCells = [];
                $isEmpty  = true;

                foreach ($this->stockColumns as $column) {
                    $position = $headerMap[$column] ?? null;
                    $raw      = $position === null ? null : ($row[$position] ?? null);

                    if ($position !== null && (is_int($raw) || is_float($raw))) {
                        // Sel angka Excel: titik selalu desimal. Tanpa penanda ini, nilai
                        // seperti 1.001 / 26.854 / 314.562 akan salah dibaca sebagai 1001 /
                        // 26854 / 314562 oleh penafsir pemisah ribuan.
                        $value                = $this->normalizeNumericCell($raw);
                        $numericCells[$column] = true;
                    } else {
                        // Kolom opsional (mis. nama gudang / batch / harga) boleh tidak ada di file.
                        $value = $position === null ? '' : $this->normalizeValue($raw);
                    }

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

                // Baris bertanda "JANGAN IMPORT" (mis. sheet perlu_rekonsiliasi) tidak boleh masuk staging.
                if (stripos((string) ($values['status_validasi'] ?? ''), 'JANGAN') !== false) {
                    $rekonsiliasi++;

                    if (count($errors) < self::MAX_ERROR_DETAIL) {
                        $errors[] = 'Baris ' . $excelRow . ': status "'
                            . trim((string) $values['status_validasi'])
                            . '" menandakan baris belum boleh diimport - dilewati.';
                    }

                    continue;
                }

                foreach ($this->stockDateColumns as $column => $isRequired) {
                    [$valid, $dateValue] = $this->normalizeDate($values[$column] ?? '', $isRequired);
                    if (! $valid) {
                        $rowErrors[] = "kolom {$column} bukan tanggal yang valid";
                    }
                    $values[$column] = $dateValue;
                }

                foreach ($this->stockRequired as $column) {
                    if (($values[$column] ?? null) === null || $values[$column] === '') {
                        $rowErrors[] = "kolom {$column} wajib diisi";
                    }
                }

                foreach ($this->stockNumeric as $column) {
                    $value = $values[$column] ?? '';

                    if ($value === '') {
                        continue;
                    }

                    // Sel Excel yang sudah berupa angka tidak perlu ditafsirkan ulang.
                    if (isset($numericCells[$column]) && is_numeric($value)) {
                        $values[$column] = (string) $value;
                        continue;
                    }

                    $number = $this->normalizeNumber($value);

                    if ($number === null) {
                        $rowErrors[] = "kolom {$column} bukan angka";
                        continue;
                    }

                    $values[$column] = $number;
                }

                // ck_stock_persediaan_opening_qty_nonzero: qty_saldo tidak boleh 0.
                if (($values['qty_saldo'] ?? '') !== '' && (float) $values['qty_saldo'] === 0.0) {
                    $rowErrors[] = 'kolom qty_saldo tidak boleh 0';
                }

                if (($values['tanggal_saldo'] ?? null) === null || $values['tanggal_saldo'] === '') {
                    $values['tanggal_saldo'] = self::TANGGAL_DEFAULT;
                }

                if ($rowErrors !== []) {
                    $invalid++;
                    if (count($errors) < self::MAX_ERROR_DETAIL) {
                        $errors[] = 'Baris ' . $excelRow . ': ' . implode(', ', $rowErrors);
                    }
                    continue;
                }

                $record = $this->buildRecord($values);

                // Kunci bisnis (uniqueidopening) harus unik. Bila template memecah satu
                // kode menjadi beberapa baris (mis. baris pembulatan qty), qty & nilai
                // dijumlahkan agar tidak ada saldo yang hilang.
                $key = (string) ($record['uniqueidopening'] ?? '');

                if ($key !== '' && isset($seenKeys[$key])) {
                    $merged++;
                    $mergedKeys[$key] = true;

                    if (count($duplicateNotes) < self::MAX_ERROR_DETAIL) {
                        $duplicateNotes[] = 'Baris ' . $excelRow . ': digabung ke baris ' . $seenKeys[$key]
                            . ' (tanggal|idbranch|kode_gudang|kode_barang|satuan|batch_spec sama).';
                    }

                    $records[$key]['qty_saldo']   = $this->sumDecimal($records[$key]['qty_saldo'], $record['qty_saldo']);
                    $records[$key]['nilai_saldo'] = $this->sumDecimal($records[$key]['nilai_saldo'], $record['nilai_saldo']);

                    continue;
                }

                $seenKeys[$key] = $excelRow;

                $record['status_validasi'] = 'DRAFT';
                $record['inputby']         = $nama;
                $record['inputdate']       = $now;

                $records[$key] = $record;
            }

            // Rapikan hasil penggabungan: harga rata-rata dihitung ulang dan baris yang
            // qty-nya menjadi 0 tidak boleh disimpan (ck_stock_persediaan_opening_qty_nonzero).
            foreach ($records as $key => $record) {
                if ((float) $record['qty_saldo'] === 0.0) {
                    $invalid++;
                    unset($records[$key]);

                    if (count($errors) < self::MAX_ERROR_DETAIL) {
                        $errors[] = 'Baris ' . ($seenKeys[$key] ?? '-')
                            . ': qty_saldo menjadi 0 setelah penggabungan - dilewati.';
                    }

                    continue;
                }

                if (isset($mergedKeys[$key])) {
                    $record['harga_rata_rata'] = $this->divideDecimal($record['nilai_saldo'], $record['qty_saldo']);
                    $records[$key]             = $record;
                }
            }

            // Simpan per potongan agar satu statement tidak terlalu besar.
            foreach (array_chunk(array_values($records), self::INSERT_CHUNK) as $chunk) {
                [$saved, $skipped] = $this->insertRecords($chunk);
                $inserted += $saved;
                $duplicate += $skipped;
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            $this->setFlash('danger', 'Proses upload gagal: ' . esc($this->cleanDbMessage($e->getMessage())));

            return;
        }


        $lines = [
            'Upload saldo awal persediaan dari file <b>' . esc($clientName) . '</b>'
                . ' (sheet <b>' . esc($sheetName) . '</b>) selesai.',
            'Total baris data: <b>' . $total . '</b>, berhasil disimpan ke '
                . '<code>sc_im.stock_persediaan_opening</code>: <b>' . $inserted
                . '</b>, digabung (kunci saldo sama): <b>' . $merged
                . '</b>, duplikat di staging: <b>' . $duplicate
                . '</b>, tidak valid: <b>' . $invalid
                . '</b>, ditandai JANGAN IMPORT: <b>' . $rekonsiliasi . '</b>.',
            'Langkah berikutnya: tekan <b>Validasi Staging</b>, lalu <b>Import ke Transaksi</b> agar baris '
                . 'berstatus VALID terposting ke <code>sc_trx.stkblc</code>.',
        ];

        if ($total === 0) {
            $lines[] = 'Tidak ada baris data yang dibaca. Pastikan baris header berada di baris 1 dan data dimulai baris 2.';
        }

        if ($duplicateNotes !== []) {
            $lines[] = '<b>Detail baris duplikat:</b>';
            foreach ($duplicateNotes as $note) {
                $lines[] = esc($note);
            }
        }

        if ($errors !== []) {
            $lines[] = '<b>Detail baris tidak valid:</b>';
            foreach ($errors as $error) {
                $lines[] = esc($error);
            }
        }

        $this->setFlash(($invalid > 0 || $rekonsiliasi > 0 || $total === 0) ? 'warning' : 'success', implode('<br>', $lines));
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

            // Baca hanya sheet yang diminta supaya sheet petunjuk tidak ikut dimuat.
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
     * Petakan header template ke posisi kolom.
     *
     * Dua tahap:
     *   1. Header yang sudah memakai nama kolom staging (template standar,
     *      mis. "idbranch" / "cabang" / "batch_spec") dipetakan apa adanya.
     *      Tahap ini yang membuat kolom "cabang" (nama cabang) pada template
     *      resmi tidak tertukar dengan "idbranch".
     *   2. Kolom yang belum terpetakan dilengkapi lewat daftar alias
     *      (mis. "Cabang (idbranch)" -> idbranch).
     */
    private function mapHeaderAliases(array $headerRow, array $aliases): array
    {
        $map     = [];
        $columns = [];

        foreach ($this->stockColumns as $column) {
            $columns[$this->normalizeHeader($column)] = $column;
        }

        // Tahap 1: nama kolom staging.
        foreach ($headerRow as $position => $label) {
            $column = $columns[$this->normalizeHeader($label)] ?? null;

            if ($column !== null && ! array_key_exists($column, $map)) {
                $map[$column] = $position;
            }
        }

        // Tahap 2: alias header ramah pengguna.
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
     * Susun satu baris staging dari nilai template.
     * Kunci idempotensi harus sama dengan sc_im.stock_persediaan_opening.uniqueidopening:
     * md5(tanggal_saldo|idbranch|kode_gudang|kode_barang|satuan|batch_spec)
     */
    private function buildRecord(array $values): array
    {
        $record = [];

        foreach ($this->stockColumns as $column) {
            $value = $values[$column] ?? '';

            if ($value === '' || $value === null) {
                $notNull = in_array($column, $this->stockNotNull, true);

                $record[$column] = match (true) {
                    // Kolom numerik NOT NULL (qty_saldo, nilai_saldo) dikirim '0', bukan ''.
                    $notNull && in_array($column, $this->stockNumeric, true) => '0',
                    // Kolom teks NOT NULL tetap dikirim '' agar insert tidak gagal.
                    $notNull                                                => '',
                    default                                                 => null,
                };

                continue;
            }

            $record[$column] = (string) $value;
        }

        $record['uniqueidopening'] = md5(implode('|', [
            (string) ($record['tanggal_saldo'] ?? ''),
            trim((string) ($record['idbranch'] ?? '')),
            trim((string) ($record['kode_gudang'] ?? '')),
            trim((string) ($record['kode_barang'] ?? '')),
            trim((string) ($record['satuan'] ?? '')),
            trim((string) ($record['batch_spec'] ?? '')),
        ]));

        return $record;
    }

    /**
     * Simpan satu potongan baris sekaligus.
     *
     * @return array{0: int, 1: int} [tersimpan, dilewati]
     */
    private function insertRecords(array $records): array
    {
        $result = $this->model()->ins_stock_persediaan_opening_batch($records);

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
     * Normalisasi sel angka Excel menjadi string numerik tanpa menebak pemisah ribuan.
     * 1.001 (sel angka) tetap 1.001, bukan 1001.
     */
    private function normalizeNumericCell($value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }

        $number = (float) $value;

        if (! is_finite($number)) {
            return '';
        }

        if (floor($number) === $number && abs($number) < PHP_INT_MAX) {
            return (string) (int) $number;
        }

        $text = rtrim(rtrim(number_format($number, 10, '.', ''), '0'), '.');

        return $text === '' || $text === '-' ? '0' : $text;
    }

    /**
     * Jumlahkan dua nilai desimal sebagai string tanpa kehilangan presisi.
     * Dipakai saat baris template yang memakai kunci saldo sama digabung.
     */
    private function sumDecimal($left, $right): string
    {
        $left  = ($left === null || $left === '') ? '0' : (string) $left;
        $right = ($right === null || $right === '') ? '0' : (string) $right;

        if (function_exists('bcadd')) {
            return bcadd($left, $right, 6);
        }

        return number_format((float) $left + (float) $right, 6, '.', '');
    }

    /**
     * Bagi dua nilai desimal sebagai string (hasil pembulatan 6 desimal).
     */
    private function divideDecimal($numerator, $denominator): ?string
    {
        $numerator   = ($numerator === null || $numerator === '') ? '0' : (string) $numerator;
        $denominator = ($denominator === null || $denominator === '') ? '0' : (string) $denominator;

        if ((float) $denominator === 0.0) {
            return null;
        }

        if (function_exists('bcdiv')) {
            return bcdiv($numerator, $denominator, 6);
        }

        return number_format((float) $numerator / (float) $denominator, 6, '.', '');
    }

    /**
     * Kirim template XLSX saldo awal persediaan (baris header + 1 baris contoh).
     */
    private function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(self::STOCK_SHEET);
        $sheet->fromArray($this->stockTemplateHeaders, null, 'A1');

        // Baris contoh membantu pengguna melihat format tanggal, qty dan nilai saldo.
        $sheet->fromArray([
            self::TANGGAL_DEFAULT, 'JTS', 'PT JATIM TAMAN STEEL MFG', 'G001', 'GUDANG IT',
            '010104A0000002', 'LAPTOP', 'UNIT', '',
            10, 1000000, 100000, 'Saldo awal contoh - hapus baris ini', 'DRAFT',
        ], null, 'A2');

        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9E1F2');
        $sheet->freezePane('A2');

        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'template_stock_');
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
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_stock_persediaan.xlsx"')
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

