<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Models\Tools\M_SaldoAwalAR;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Menu I.T.D.6 - UPLOAD SALDO AR (saldo awal piutang customer).
 *
 * Alur: upload XLSX -> staging sc_im.import_ar -> validasi -> proses
 *       (sc_trx.transaction_dt dengan journal_type OPENAR).
 */
class SaldoAwalAR extends BaseController
{
    private const PATH_AR        = './assets/files/AR/';
    private const URI_AR         = 'tools/proses/uploadSaldoAwalAR';
    private const SHEET          = 'template_import';
    private const TANGGAL_DEFAULT = '2026-10-31';
    private const MAX_DETAIL     = 300;
    private const MAX_PROBLEM    = 50;
    private const INSERT_CHUNK   = 300;

    /** Header template (sheet "template_import", kolom A..Y). */
    private $arHeaders = [
        'tanggal_saldo', 'no_jurnal', 'kode_customer', 'nama_customer',
        'kode_prk', 'nama_prk', 'tanggal_dokumen', 'tanggal_jatuh_tempo',
        'dk', 'keterangan', 'mata_uang', 'kurs', 'nilai',
        'nilai_belum_jt', 'nilai_jt_1', 'nilai_jt_2', 'nilai_jt_3', 'nilai_jt_4',
        'umur_hari', 'kode_job', 'nama_job', 'alamat_customer', 'kota_customer',
        'kode_salesman', 'nama_salesman',
    ];

    /**
     * Alias header (nama ternormalisasi) => kolom staging.
     * Nama kolom mentah tetap diterima supaya template versi lain bisa dipakai.
     */
    private $arHeaderAliases = [
        'tanggalsaldo'    => 'tanggal_saldo',
        'tanggal'         => 'tanggal_saldo',
        'nojurnal'        => 'no_jurnal',
        'nojurnallegacy'  => 'no_jurnal',
        'nomorjurnal'     => 'no_jurnal',
        'kodecustomer'    => 'kode_customer',
        'customer'        => 'kode_customer',
        'kdcustomer'      => 'kode_customer',
        'namacustomer'    => 'nama_customer',
        'namapelanggan'   => 'nama_customer',
        'kodeperkiraan'   => 'kode_prk',
        'kodecoa'         => 'kode_prk',
        'kodeacc'         => 'kode_prk',
        'namaperkiraan'   => 'nama_prk',
        'namacoa'         => 'nama_prk',
        'tanggaldokumen'  => 'tanggal_dokumen',
        'tgldokumen'      => 'tanggal_dokumen',
        'tanggaljatuhtempo' => 'tanggal_jatuh_tempo',
        'jatuhtempo'      => 'tanggal_jatuh_tempo',
        'tgljatuhtempo'   => 'tanggal_jatuh_tempo',
        'dk'              => 'dk',
        'debetkredit'     => 'dk',
        'keterangan'      => 'keterangan',
        'uraian'          => 'keterangan',
        'matauang'        => 'mata_uang',
        'currency'        => 'mata_uang',
        'valuta'          => 'mata_uang',
        'kurs'            => 'kurs',
        'rate'            => 'kurs',
        'nilai'           => 'nilai',
        'amount'          => 'nilai',
        'nilaibelumjt'    => 'nilai_belum_jt',
        'belumjt'         => 'nilai_belum_jt',
        'nilaijt1'        => 'nilai_jt_1',
        'nilaijt2'        => 'nilai_jt_2',
        'nilaijt3'        => 'nilai_jt_3',
        'nilaijt4'        => 'nilai_jt_4',
        'umurhari'        => 'umur_hari',
        'umur'            => 'umur_hari',
        'aging'           => 'umur_hari',
        'kodejob'         => 'kode_job',
        'job'             => 'kode_job',
        'namajob'         => 'nama_job',
        'alamatcustomer'  => 'alamat_customer',
        'alamat'          => 'alamat_customer',
        'kotacustomer'    => 'kota_customer',
        'kota'            => 'kota_customer',
        'kodesalesman'    => 'kode_salesman',
        'salesman'        => 'kode_salesman',
        'namasalesman'    => 'nama_salesman',
    ];

    /** Header yang wajib ada pada file upload. */
    private $arRequiredHeaders = [
        'no_jurnal', 'kode_customer', 'kode_prk', 'nilai', 'dk',
    ];

    /** Nilai yang wajib terisi pada setiap baris data. */
    private $arRequired = [
        'no_jurnal', 'kode_customer', 'kode_prk', 'nilai', 'dk',
    ];

    /** Kolom angka. */
    private $arNumeric = [
        'kurs', 'nilai', 'nilai_belum_jt',
        'nilai_jt_1', 'nilai_jt_2', 'nilai_jt_3', 'nilai_jt_4', 'umur_hari',
    ];

    /** Kolom tanggal. */
    private $arDates = ['tanggal_saldo', 'tanggal_dokumen', 'tanggal_jatuh_tempo'];

    /** @var M_SaldoAwalAR|null */
    private $m_saldoawalar;

    /**
     * Halaman menu I.T.D.6: form upload + daftar batch + detail staging.
     */
    public function uploadar()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getGet('template') !== null) {
            return $this->downloadTemplate();
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $batchId = $this->handleUpload();

            return redirect()->to(base_url($this->pageUri() . ($batchId > 0 ? '?batch=' . $batchId : '')));
        }

        $data  = $this->pageData('I.T.D.6', 'I.T.D.6/01', 'Upload Saldo Awal Piutang (AR)');
        $model = $this->model();
        $uri   = $this->pageUri();

        $batches = $model->q_batches(50);
        $batchId = (int) ($this->request->getGet('batch') ?? 0);

        if ($batchId <= 0 && $batches !== []) {
            $batchId = (int) ($batches[0]['batch_id'] ?? 0);
        }

        $data['list_batches']      = $batches;
        $data['selected_batch_id'] = $batchId;
        $data['selected_batch']    = $model->q_batch($batchId);
        $data['list_detail']       = $model->q_detail($batchId, self::MAX_DETAIL);
        $data['list_problems']     = $model->q_problems($batchId, self::MAX_PROBLEM);
        $data['summary']           = $model->q_summary();
        $data['branch_options']    = $model->q_branch_options();
        $data['coa_options']       = $model->q_coa_options();
        $data['header_columns']    = $this->arHeaders;
        $data['sheet_name']        = self::SHEET;
        $data['tanggal_default']   = self::TANGGAL_DEFAULT;
        $data['detail_limit']      = self::MAX_DETAIL;
        $data['problem_limit']     = self::MAX_PROBLEM;
        $data['upload_url']        = base_url($uri);
        $data['template_url']      = base_url($uri . '?template=1');
        $data['validasi_url']      = base_url($uri . '/validasi');
        $data['proses_url']        = base_url($uri . '/proses');

        return $this->template->render('tools/import/v_ar_upload', $data);
    }

    /**
     * Tombol "Validasi Staging": sc_im.fn_validate_import_ar(batch, user).
     */
    public function validasiar()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url($this->pageUri()));
        }

        $batchId = (int) $this->request->getPost('batch_id');
        $nama    = trim((string) $this->session->get('nama'));

        if ($batchId <= 0) {
            $this->setFlash('danger', 'Batch belum dipilih.');

            return redirect()->to(base_url($this->pageUri()));
        }

        // Validasi memproses seluruh baris staging sekaligus.
        set_time_limit(1200);

        try {
            $result = $this->model()->exe_validate($batchId, $nama);

            $this->setFlash('success',
                'Validasi batch <b>#' . $batchId . '</b> selesai dalam <b>'
                . $result['elapsed_ms'] . ' ms</b>. '
                . 'Total baris: <b>' . $result['total_rows'] . '</b>, READY: <b>' . $result['ready_rows'] . '</b>, '
                . 'ERROR: <b>' . $result['error_rows'] . '</b>, warning: <b>' . $result['warning_rows'] . '</b>. '
                . 'Baris ERROR tidak menghalangi proses; baris READY tetap dapat diposting.');
        } catch (\Throwable $e) {
            $this->setFlash('danger',
                'Validasi gagal: <b>' . esc($this->cleanDbMessage($e->getMessage())) . '</b>');
        }

        return redirect()->to(base_url($this->pageUri() . '?batch=' . $batchId));
    }


    /**
     * Tombol "Proses": sc_im.fn_process_import_ar(batch, counter COA, ...).
     */
    public function prosesar()
    {
        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        }

        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url($this->pageUri()));
        }

        $batchId  = (int) $this->request->getPost('batch_id');
        $counter  = strtoupper(trim((string) $this->request->getPost('counter_idcoa')));
        $idbranch = trim((string) $this->request->getPost('idbranch'));
        $cabang   = trim((string) $this->request->getPost('cabang'));
        $nama     = trim((string) $this->session->get('nama'));

        if ($batchId <= 0) {
            $this->setFlash('danger', 'Batch belum dipilih.');

            return redirect()->to(base_url($this->pageUri()));
        }

        if ($counter === '') {
            $this->setFlash('danger', 'Counter COA (perkiraan lawan) wajib diisi.');

            return redirect()->to(base_url($this->pageUri() . '?batch=' . $batchId));
        }

        // Posting seluruh baris READY: longgarkan batas waktu.
        set_time_limit(1200);

        try {
            $result = $this->model()->exe_process($batchId, $counter, $idbranch, $cabang, $nama);

            $type = $result['status'] === 'PROCESSED' ? 'success'
                : ($result['processed_rows'] > 0 ? 'warning' : 'danger');

            $this->setFlash($type,
                'Proses batch <b>#' . $batchId . '</b> selesai dalam <b>' . $result['elapsed_ms'] . ' ms</b>. '
                . 'Status: <b>' . esc($result['status']) . '</b>. '
                . 'Total: <b>' . $result['total_rows'] . '</b>, terposting: <b>' . $result['processed_rows'] . '</b>, '
                . 'ERROR: <b>' . $result['error_rows'] . '</b>, sisa READY: <b>' . $result['ready_rows'] . '</b>. '
                . esc($result['message']));
        } catch (\Throwable $e) {
            $this->setFlash('danger',
                'Proses gagal: <b>' . esc($this->cleanDbMessage($e->getMessage())) . '</b>');
        }

        return redirect()->to(base_url($this->pageUri() . '?batch=' . $batchId));
    }

    /**
     * Proses upload file XLS/XLSX menjadi satu batch staging baru.
     *
     * @return int id batch (0 bila gagal)
     */
    private function handleUpload(): int
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $file = $this->request->getFile('import');

        if ($file === null || ! $file->isValid()) {
            $this->setFlash('danger', 'File belum dipilih atau gagal di-upload. Silahkan coba lagi.');

            return 0;
        }

        if (! $this->validate([
            'import' => 'uploaded[import]|ext_in[import,xls,xlsx]|max_size[import,10240]',
        ])) {
            $this->setFlash('danger', 'Validasi file gagal: ' . implode(' ', $this->validator->getErrors()));

            return 0;
        }

        $tanggal = $this->normalizeBatchDate((string) $this->request->getPost('tanggal_saldo'));
        $dir     = self::PATH_AR;

        if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir)) {
            $this->setFlash('danger', 'Folder upload tidak dapat dibuat: ' . esc($dir));

            return 0;
        }

        $clientName = $file->getClientName();
        $safeName   = date('YmdHis') . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $clientName);

        try {
            $file->move($dir, $safeName);
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'File gagal disimpan: ' . esc($e->getMessage()));

            return 0;
        }

        $fullPath = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

        try {
            [$sheetName, $rows] = $this->readSpreadsheet($fullPath, self::SHEET);
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'File tidak dapat dibaca: ' . esc($e->getMessage()));

            return 0;
        }

        $headerOffset = $this->locateHeaderRow($rows, $this->arHeaderAliases, $this->arRequiredHeaders);
        $headerMap    = $this->mapHeaderAliases($rows[$headerOffset] ?? [], $this->arHeaderAliases);
        $missing      = array_values(array_diff($this->arRequiredHeaders, array_keys($headerMap)));

        if ($missing !== []) {
            $this->setFlash('danger', 'Header template tidak sesuai pada sheet <b>' . esc($sheetName)
                . '</b>. Kolom berikut tidak ditemukan pada baris header (baris ' . ($headerOffset + 1) . '): <b>'
                . implode(', ', array_map('esc', $missing)) . '</b>');

            return 0;
        }


        $nama    = trim((string) $this->session->get('nama'));
        $records = [];
        $total   = 0;
        $skipped = 0;
        $notes   = [];
        $rowNo   = 0;

        foreach (array_slice($rows, $headerOffset + 1) as $index => $row) {
            $excelRow = $index + $headerOffset + 2;
            $values   = [];
            $isEmpty  = true;
            $numeric  = [];

            foreach ($this->arHeaders as $column) {
                $position = $headerMap[$column] ?? null;
                $raw      = $position === null ? null : ($row[$position] ?? null);

                if ($position !== null && (is_int($raw) || is_float($raw))) {
                    $values[$column]  = $this->normalizeNumericCell($raw);
                    $numeric[$column] = true;
                } else {
                    $values[$column] = $position === null ? '' : $this->normalizeValue($raw);
                }

                if ($values[$column] !== '') {
                    $isEmpty = false;
                }
            }

            if ($isEmpty) {
                continue;
            }

            $total++;
            $rowNo++;

            $record = $this->buildRecord($values, $numeric, $excelRow, $tanggal, $notes);

            if ($record === null) {
                $skipped++;

                continue;
            }

            $records[$rowNo] = $record;
        }

        if ($records === []) {
            $this->setFlash('warning', 'Tidak ada baris data yang dibaca dari sheet <b>' . esc($sheetName)
                . '</b>. Pastikan baris header berada di baris 1 dan data dimulai baris 2.');

            return 0;
        }

        try {
            $batchId = $this->model()->ins_batch([
                'batch_no'      => 'OPENAR-' . date('YmdHis') . '-'
                                   . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'filename'      => $clientName,
                'tanggal_saldo' => $tanggal,
                'createdby'     => $nama,
            ]);

            if ($batchId <= 0) {
                throw new \RuntimeException('Batch staging tidak terbentuk (id kosong).');
            }

            $inserted = $this->insertRecords($batchId, $records);
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'Gagal menyimpan staging: <b>'
                . esc($this->cleanDbMessage($e->getMessage())) . '</b>');

            return 0;
        }

        // Validasi otomatis supaya pengguna langsung melihat baris ERROR / warning.
        $validasi = null;

        try {
            $validasi = $this->model()->exe_validate($batchId, $nama);
        } catch (\Throwable $e) {
            $notes[] = 'Validasi otomatis gagal: ' . $this->cleanDbMessage($e->getMessage());
        }

        $lines = [
            'Upload sheet <b>' . esc($sheetName) . '</b> selesai ke batch <b>#' . $batchId . '</b>. '
                . 'Baris data dibaca: <b>' . $total . '</b>, tersimpan ke '
                . '<code>sc_im.import_ar</code>: <b>' . $inserted['inserted'] . '</b>, '
                . 'dilewati: <b>' . ($inserted['skipped'] + $skipped) . '</b>.',
        ];

        if ($validasi !== null) {
            $lines[] = 'Hasil validasi otomatis: READY <b>' . $validasi['ready_rows']
                . '</b>, ERROR <b>' . $validasi['error_rows']
                . '</b>, warning <b>' . $validasi['warning_rows'] . '</b>.';
        }

        $lines[] = 'Langkah berikutnya: periksa baris ERROR/warning, lalu tekan '
            . '<b>Proses</b> dan isi Counter COA (perkiraan lawan).';

        foreach (array_slice($notes, 0, 20) as $note) {
            $lines[] = esc($note);
        }

        $this->setFlash(
            ($validasi !== null && $validasi['error_rows'] > 0) ? 'warning' : 'success',
            implode('<br>', $lines)
        );

        return $batchId;
    }


    /**
     * Susun satu baris staging dari nilai template.
     *
     * @param array<string, mixed> $values  nilai per kolom
     * @param array<string, bool>  $numeric kolom yang berasal dari sel angka Excel
     * @param array<int, string>   $notes   kumpulan catatan untuk flashdata (by ref)
     * @return array<string, mixed>|null
     */
    private function buildRecord(array $values, array $numeric, int $excelRow, string $tanggalBatch, array &$notes): ?array
    {
        $record = [];

        // Tanggal kosong -> null; tanggal saldo memakai tanggal batch.
        foreach ($this->arDates as $column) {
            [$valid, $date] = $this->normalizeDate((string) ($values[$column] ?? ''), false);

            if (! $valid && count($notes) < 20) {
                $notes[] = 'Baris ' . $excelRow . ': kolom ' . $column
                    . ' bukan tanggal yang valid ("' . (string) ($values[$column] ?? '') . '").';
            }

            $record[$column] = $date;
        }

        if ($record['tanggal_saldo'] === null) {
            $record['tanggal_saldo'] = $tanggalBatch;
        }

        foreach ($this->arHeaders as $column) {
            if (in_array($column, $this->arDates, true)) {
                continue;
            }

            $value = (string) ($values[$column] ?? '');

            if (! in_array($column, $this->arNumeric, true)) {
                if ($column === 'dk' || $column === 'mata_uang') {
                    $value = strtoupper(trim($value));
                }

                $record[$column] = $value;
                continue;
            }

            if ($value === '') {
                $record[$column] = '0';
                continue;
            }

            if (isset($numeric[$column]) && is_numeric($value)) {
                $record[$column] = $column === 'umur_hari' ? (string) (int) $value : $value;
                continue;
            }

            $number = $this->normalizeNumber($value);

            if ($number === null) {
                if (count($notes) < 20) {
                    $notes[] = 'Baris ' . $excelRow . ': kolom ' . $column
                        . ' bukan angka ("' . $value . '") - dianggap 0.';
                }

                $record[$column] = '0';
                continue;
            }

            $record[$column] = $column === 'umur_hari' ? (string) (int) $number : $number;
        }

        // Baris tanpa isi pada kolom wajib dianggap bukan data.
        foreach ($this->arRequired as $column) {
            if (trim((string) ($record[$column] ?? '')) !== '') {
                return $record;
            }
        }

        if (count($notes) < 20) {
            $notes[] = 'Baris ' . $excelRow . ': dilewati karena kolom wajib ('
                . implode(', ', $this->arRequired) . ') tidak terisi.';
        }

        return null;
    }

    /**
     * Simpan baris staging per potongan (key array = nomor baris).
     *
     * @param  array<int, array<string, mixed>> $records
     * @return array{inserted: int, skipped: int}
     */
    private function insertRecords(int $batchId, array $records): array
    {
        $result = ['inserted' => 0, 'skipped' => 0];

        foreach (array_chunk($records, self::INSERT_CHUNK, true) as $chunk) {
            $chunkRecords = [];

            foreach ($chunk as $rowNo => $record) {
                $chunkRecords[] = array_merge($record, ['row_no' => (int) $rowNo]);
            }

            $partial = $this->model()->ins_import_ar_batch($batchId, $chunkRecords);

            $result['inserted'] += (int) ($partial['inserted'] ?? 0);
            $result['skipped']  += (int) ($partial['skipped'] ?? 0);
        }

        return $result;
    }


    /**
     * Baca satu sheet (default: sheet pertama) sebagai array baris x kolom.
     *
     * @return array{0: string, 1: array<int, array<int, mixed>>} [nama sheet, baris]
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
     * Cari baris header: baris (maks. 10 baris awal) dengan kolom wajib terbanyak.
     *
     * @param array<int, array<int, mixed>> $rows
     * @param array<string, string>         $aliases
     * @param array<int, string>            $required
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
     * Petakan header template ke posisi kolom.
     * Tahap 1: nama kolom staging apa adanya. Tahap 2: alias ramah pengguna.
     *
     * @param array<int, mixed>     $headerRow
     * @param array<string, string> $aliases
     * @return array<string, int>
     */
    private function mapHeaderAliases(array $headerRow, array $aliases): array
    {
        $map     = [];
        $columns = [];

        foreach ($this->arHeaders as $column) {
            $columns[$this->normalizeHeader($column)] = $column;
        }

        foreach ($headerRow as $position => $label) {
            $column = $columns[$this->normalizeHeader($label)] ?? null;

            if ($column !== null && ! array_key_exists($column, $map)) {
                $map[$column] = $position;
            }
        }

        foreach ($headerRow as $position => $label) {
            $column = $aliases[$this->normalizeHeader($label)] ?? null;

            if ($column !== null && ! array_key_exists($column, $map)) {
                $map[$column] = $position;
            }
        }

        return $map;
    }

    private function normalizeHeader($value): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(trim((string) $value)));
    }

    /**
     * Normalisasi nilai sel menjadi string (angka bulat tanpa desimal).
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

        return trim((string) preg_replace('/\s+/', ' ', (string) $value));
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
     */
    private function normalizeNumber(string $value): ?string
    {
        $value = str_replace([' ', "\u{00A0}"], '', trim($value));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            // 1.234.567,89 => 1234567.89
            $value = str_replace(['.', ','], ['', '.'], $value);
        } elseif (substr_count($value, ',') === 1 && strpos($value, '.') === false) {
            // 1,5 => 1.5
            $value = str_replace(',', '.', $value);
        } else {
            // 16,800.00 => 16800.00
            $value = str_replace(',', '', $value);
        }

        return is_numeric($value) ? $value : null;
    }

    /**
     * Normalisasi sel angka Excel (tanpa menebak pemisah ribuan).
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
     * Tanggal saldo batch dari form (fallback ke TANGGAL_DEFAULT).
     */
    private function normalizeBatchDate(string $value): string
    {
        [$valid, $date] = $this->normalizeDate($value, false);

        return ($valid && $date !== null) ? $date : self::TANGGAL_DEFAULT;
    }


    /**
     * Unduh template XLSX (header + 1 baris contoh) pada sheet "template_import".
     */
    private function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(self::SHEET);

        $sheet->fromArray($this->arHeaders, null, 'A1');
        $sheet->fromArray([
            self::TANGGAL_DEFAULT, 'NDP/2610/PT0015', 'C-B0016', 'BPK BAHAR',
            '114005', 'PIUTANG LAIN-LAIN', '2026-10-09', '2026-11-08',
            'D', 'Saldo awal contoh - hapus baris ini', 'IDR', 1, 1000000,
            1000000, 0, 0, 0, 0, 8, '01', 'PT JATIM TAMAN STEEL MFG',
            'ALAMAT CUSTOMER', 'SIDOARJO', 'MKT', 'MARKETING',
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

        $tempFile = tempnam(sys_get_temp_dir(), 'template_ar_');

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
            ->setHeader('Content-Disposition', 'attachment; filename="Template_opening_ar.xlsx"')
            ->setBody($content);
    }

    /**
     * URL halaman upload yang sedang dipakai (menu I.T.D.6 atau grup import).
     */
    private function pageUri(): string
    {
        $path = strtolower(trim((string) $this->request->getPath(), '/'));

        if (strpos($path, 'tools/import') !== false) {
            return 'tools/import/uploadar';
        }

        return self::URI_AR;
    }

    private function model(): M_SaldoAwalAR
    {
        if ($this->m_saldoawalar === null) {
            $this->m_saldoawalar = new M_SaldoAwalAR();
        }

        return $this->m_saldoawalar;
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

    private function setFlash(string $type, string $message): void
    {
        $this->session->setFlashdata('message', '<div class="alert alert-' . $type . '">' . $message . '</div>');
    }

    /**
     * Rapatkan pesan error database menjadi satu baris pendek.
     */
    private function cleanDbMessage(string $message): string
    {
        $message = trim((string) preg_replace('/\s+/', ' ', strip_tags($message)));

        return strlen($message) > 400 ? substr($message, 0, 400) . '...' : $message;
    }
}

