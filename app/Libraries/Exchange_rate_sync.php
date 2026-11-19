<?php

namespace App\Libraries;

use App\Models\Master\M_Currency;

/**
 * Sinkronisasi exchange rate otomatis (cron / spark command).
 *
 * Alur :
 *  1. Ambil kurs dari API untuk currency target (default EUR, AUD, SGD, USD, JPY -> IDR).
 *  2. Validasi response API : HTTP, JSON, nilai kurs, quote currency, dan tanggal kurs.
 *  3. Bandingkan tanggal kurs API dengan data terakhir di sc_mst.exchangerate.
 *  4. Simpan HANYA jika tanggal kurs API lebih baru (tanggal sama tidak dioverwrite).
 *  5. Setiap kegagalan ditulis ke writable/logs/exchange_rate_sync.log sehingga
 *     penjadwalan per jam berikutnya otomatis menjadi proses retry (idempotent).
 *
 * Catatan : exchangedate selalu disimpan dengan jam 00:00:00 agar satu tanggal
 * hanya mewakili satu kurs (padanan exchangedate::date pada PostgreSQL).
 */
class Exchange_rate_sync
{
    public const DEFAULT_CURRENCIES = ['EUR', 'AUD', 'SGD', 'USD', 'JPY'];
    public const LOG_FILE = 'exchange_rate_sync.log';

    /** @var M_Currency|null */
    protected $m_currency;

    /** @var \CodeIgniter\Database\BaseConnection */
    protected $db;

    public function __construct(?M_Currency $m_currency = null)
    {
        $this->m_currency = $m_currency;
        $this->db = \Config\Database::connect();
    }

    protected function model(): M_Currency
    {
        if ($this->m_currency === null) {
            $this->m_currency = new M_Currency();
        }

        return $this->m_currency;
    }

    /**
     * Jalankan sinkronisasi.
     *
     * @param array $currencies Kode currency target (kode API, mis. JPY untuk data YEN).
     * @param array $options    createdby, force, dry_run.
     *
     * @return array{ok:bool,message:string,success:int,planned:int,skipped:int,failed:int,details:array}
     */
    public function sync(array $currencies = self::DEFAULT_CURRENCIES, array $options = []): array
    {
        $dryRun    = ! empty($options['dry_run']);
        $force     = ! empty($options['force']);
        $createdBy = isset($options['createdby']) && trim((string) $options['createdby']) !== ''
            ? substr(trim((string) $options['createdby']), 0, 20)
            : 'SYSTEM';

        $setting = $this->model()->getExchangeRateSetting();
        $mode    = strtolower(trim((string) ($setting['mode'] ?? 'manual')));
        $targets = $this->normalizeCurrencyList($currencies);

        $result = [
            'ok'      => true,
            'message' => '',
            'success' => 0,
            'planned' => 0,
            'skipped' => 0,
            'failed'  => 0,
            'details' => [],
        ];

        $this->writeLog('RUN start | mode=' . $mode . ' | target=' . implode(',', $targets) . ($dryRun ? ' | DRY-RUN' : ''));

        if ($mode !== 'auto' && ! $force) {
            $result['ok']      = false;
            $result['message'] = 'Mode pengambilan exchange rate masih MANUAL. Aktifkan mode OTOMATIS atau jalankan dengan opsi --force.';
            $this->writeLog('RUN abort | ' . $result['message']);

            return $result;
        }

        if ($targets === []) {
            $result['ok']      = false;
            $result['message'] = 'Daftar currency target kosong.';
            $this->writeLog('RUN abort | ' . $result['message']);

            return $result;
        }

        // Currency dasar (IDR) tidak perlu diambil kursnya.
        $baseCurrency = strtoupper(trim((string) ($setting['base_currency'] ?? '')));
        if ($baseCurrency !== '') {
            $targets = array_values(array_filter($targets, static function ($code) use ($baseCurrency) {
                return $code !== $baseCurrency;
            }));
        }

        $rows = $this->resolveCurrencyRows($targets, $setting);
        if ($rows === []) {
            $result['ok']      = false;
            $result['message'] = 'Tidak ada currency target yang cocok dengan data sc_mst.currency (target: ' . implode(',', $targets) . ').';
            $this->writeLog('RUN abort | ' . $result['message']);

            return $result;
        }

        $now = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            $idcurr   = (int) $row['id'];
            $currcode = strtoupper(trim((string) $row['currcode']));
            $api      = $this->fetchRate($currcode, $setting);

            $detail = [
                'currcode' => $currcode,
                'api_code' => $api['from'],
                'status'   => '',
                'message'  => '',
                'date'     => '',
                'nilai'    => '',
            ];

            // 1. Validasi response API (HTTP / JSON / rate).
            if (! $api['ok'] || $api['rate'] === null) {
                $message = $api['error'] !== '' ? $api['error'] : 'Gagal mengambil exchange rate dari API.';

                $detail['status']    = 'FAILED';
                $detail['message']   = $message;
                $result['failed']++;
                $result['details'][] = $detail;

                $this->writeLog('FAILED | ' . $currcode . ' (' . $api['from'] . '->' . $api['to'] . ') | ' . $message . ' | url=' . $api['url']);
                log_message('error', 'ExchangeRateSync: ' . $currcode . ' gagal - ' . $message);

                continue;
            }

            // 2. Validasi tanggal kurs.
            if ($api['rate_date'] === null) {
                $message = 'Tanggal kurs tidak valid / tidak tersedia pada response API.';

                $detail['status']    = 'FAILED';
                $detail['message']   = $message;
                $result['failed']++;
                $result['details'][] = $detail;

                $this->writeLog('FAILED | ' . $currcode . ' | ' . $message . ' | url=' . $api['url']);
                log_message('error', 'ExchangeRateSync: ' . $currcode . ' gagal - ' . $message);

                continue;
            }

            $rateDate        = $api['rate_date'];
            $nilai           = number_format((float) $api['rate'], 4, '.', '');
            $detail['date']  = $rateDate;
            $detail['nilai'] = $nilai;

            // 3. Tanggal kurs sudah ada => jangan overwrite.
            $existing = $this->getExchangeRateByDate($idcurr, $rateDate);
            if ($existing) {
                $message = 'Tanggal kurs ' . $rateDate . ' sudah ada (nilai: '
                    . number_format((float) $existing['nilai'], 4, '.', '')
                    . '). Data tidak dioverwrite.';

                $detail['status']    = 'SKIPPED';
                $detail['message']   = $message;
                $result['skipped']++;
                $result['details'][] = $detail;
                $this->writeLog('SKIPPED | ' . $currcode . ' | ' . $message);

                continue;
            }

            // 4. Hanya simpan jika tanggal kurs API lebih baru dari data terakhir.
            $latest = $this->getLatestExchangeRate($idcurr);
            if ($latest) {
                $latestDate = $this->toDateOnly($latest['exchangedate']);

                if ($latestDate !== null && $latestDate >= $rateDate) {
                    $message = 'Tanggal kurs API (' . $rateDate . ') tidak lebih baru dari data terakhir ('
                        . $latestDate . '). Data tidak disimpan.';

                    $detail['status']    = 'SKIPPED';
                    $detail['message']   = $message;
                    $result['skipped']++;
                    $result['details'][] = $detail;
                    $this->writeLog('SKIPPED | ' . $currcode . ' | ' . $message);

                    continue;
                }
            }

            if ($dryRun) {
                $detail['status']    = 'WOULD-INSERT';
                $detail['message']   = 'Akan disimpan: ' . $rateDate . ' 00:00:00 = ' . $nilai;
                $result['planned']++;
                $result['details'][] = $detail;
                $this->writeLog('DRY-RUN | ' . $currcode . ' | ' . $detail['message']);

                continue;
            }

            // 5. Insert data kurs baru (exchangedate selalu jam 00:00:00).
            $insert = $this->db->table('sc_mst.exchangerate')->insert([
                'idcurr'       => $idcurr,
                'exchangedate' => $rateDate . ' 00:00:00',
                'nilai'        => $nilai,
                'createdby'    => $createdBy,
                'createddate'  => $now,
            ]);

            if ($insert) {
                $detail['status']    = 'SUCCESS';
                $detail['message']   = 'Disimpan: ' . $rateDate . ' 00:00:00 = ' . $nilai;
                $result['success']++;
                $result['details'][] = $detail;
                $this->writeLog('SUCCESS | ' . $currcode . ' | ' . $detail['message']);
            } else {
                $error = $this->db->error();

                $detail['status']    = 'FAILED';
                $detail['message']   = 'Insert gagal: ' . ($error['message'] ?? 'unknown error');
                $result['failed']++;
                $result['details'][] = $detail;
                $this->writeLog('FAILED | ' . $currcode . ' | ' . $detail['message']);
                log_message('error', 'ExchangeRateSync: ' . $currcode . ' insert gagal - ' . $detail['message']);
            }
        }

        $result['ok'] = $result['failed'] === 0;

        if ($dryRun) {
            $result['message'] = 'DRY-RUN selesai. Akan disimpan: ' . $result['planned']
                . ', Dilewati: ' . $result['skipped']
                . ', Gagal: ' . $result['failed'] . '.';
        } else {
            $result['message'] = 'Sinkronisasi selesai. Berhasil: ' . $result['success']
                . ', Dilewati: ' . $result['skipped']
                . ', Gagal: ' . $result['failed'] . '.';
        }

        $this->writeLog('RUN end   | success=' . $result['success']
            . ' planned=' . $result['planned']
            . ' skipped=' . $result['skipped']
            . ' failed=' . $result['failed']
            . ' | ' . $result['message']);

        return $result;
    }

    /**
     * Ambil kurs dari API sesuai kode currency + setting.
     *
     * @return array{ok:bool,rate:float|null,from:string,to:string,url:string,date:string,rate_date:string|null,error:string}
     */
    public function fetchRate(string $currcode, array $setting): array
    {
        $from = strtoupper(trim($currcode));
        $to   = strtoupper(trim((string) ($setting['base_currency'] ?? 'IDR')));

        $alias = isset($setting['code_alias']) && is_array($setting['code_alias']) ? $setting['code_alias'] : [];
        if (isset($alias[$from]) && trim((string) $alias[$from]) !== '') {
            $from = strtoupper(trim((string) $alias[$from]));
        }

        $url = str_replace(['{FROM}', '{TO}'], [$from, $to], (string) ($setting['api_url'] ?? ''));

        $response = $this->httpGetJson($url);
        if (! $response['ok']) {
            return [
                'ok'        => false,
                'rate'      => null,
                'from'      => $from,
                'to'        => $to,
                'url'       => $url,
                'date'      => '',
                'rate_date' => null,
                'error'     => $response['error'],
            ];
        }

        $json  = $response['json'];
        $error = '';
        $rate  = null;

        // Validasi mata uang tujuan pada response (format v2: quote, v1: base).
        $quote = isset($json['quote']) ? strtoupper(trim((string) $json['quote'])) : '';
        if ($quote !== '' && $quote !== $to) {
            $error = 'Response API tidak sesuai: quote ' . $quote . ' (diharapkan ' . $to . ').';
        } else {
            $rate = $this->extractRateFromJson($json, $to);

            if ($rate === null) {
                $error = 'Rate tidak ditemukan pada response API.';
            } elseif ($rate <= 0) {
                $error = 'Nilai rate tidak valid: ' . $rate . '.';
                $rate  = null;
            }
        }

        return [
            'ok'        => $error === '',
            'rate'      => $rate,
            'from'      => $from,
            'to'        => $to,
            'url'       => $url,
            'date'      => isset($json['date']) ? (string) $json['date'] : '',
            'rate_date' => $this->extractRateDate($json),
            'error'     => $error,
        ];
    }

    /**
     * Ambil tanggal kurs dari response API (Y-m-d). Tanggal masa depan ditolak.
     */
    public function extractRateDate($json): ?string
    {
        if (! is_array($json)) {
            return null;
        }

        $candidates = [];
        foreach (['date', 'time_last_update_utc', 'time_last_update_unix'] as $key) {
            if (isset($json[$key]) && $json[$key] !== '') {
                $candidates[] = $json[$key];
            }
        }

        $today = date('Y-m-d');

        foreach ($candidates as $value) {
            if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                $timestamp = (int) $value;
            } else {
                $timestamp = strtotime((string) $value);
            }

            if ($timestamp === false || $timestamp <= 0) {
                continue;
            }

            $date = date('Y-m-d', $timestamp);
            if ($date > $today) {
                continue;
            }

            return $date;
        }

        return null;
    }

    /**
     * Mendukung format response v2 (rate) dan v1 (rates.{TO}).
     *
     * @return float|null
     */
    public function extractRateFromJson($json, $to)
    {
        if (! is_array($json)) {
            return null;
        }

        if (isset($json['rate']) && is_numeric($json['rate'])) {
            return (float) $json['rate'];
        }

        if (isset($json['rates']) && is_array($json['rates'])) {
            if (isset($json['rates'][$to]) && is_numeric($json['rates'][$to])) {
                return (float) $json['rates'][$to];
            }

            foreach ($json['rates'] as $value) {
                if (is_numeric($value)) {
                    return (float) $value;
                }
            }
        }

        return null;
    }

    public function httpGetJson($url)
    {
        if (! function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'Extension cURL tidak tersedia pada server.', 'http' => 0, 'json' => null];
        }

        $request = function ($verifyPeer) use ($url) {
            $ch      = curl_init($url);
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT      => 'JAccounting-ExchangeRate/1.0',
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ];

            if ($verifyPeer) {
                $caBundle = $this->resolveCaBundle();
                if ($caBundle !== null) {
                    $options[CURLOPT_CAINFO] = $caBundle;
                }
            } else {
                $options[CURLOPT_SSL_VERIFYPEER] = false;
                $options[CURLOPT_SSL_VERIFYHOST] = 0;
            }

            curl_setopt_array($ch, $options);

            $body  = curl_exec($ch);
            $http  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            return ['body' => $body, 'http' => $http, 'error' => $error];
        };

        $result = $request(true);

        /* Fallback : CA bundle lokal belum terkonfigurasi pada server. */
        if ($result['body'] === false && stripos($result['error'], 'ssl') !== false) {
            $result = $request(false);
        }

        if ($result['body'] === false) {
            return [
                'ok'    => false,
                'error' => $result['error'] !== '' ? $result['error'] : 'Gagal mengakses API.',
                'http'  => $result['http'],
                'json'  => null,
            ];
        }

        if ($result['http'] < 200 || $result['http'] >= 300) {
            return [
                'ok'    => false,
                'error' => 'API mengembalikan HTTP ' . $result['http'] . '.',
                'http'  => $result['http'],
                'json'  => json_decode($result['body'], true),
            ];
        }

        $json = json_decode($result['body'], true);
        if (! is_array($json)) {
            return [
                'ok'    => false,
                'error' => 'Response API bukan JSON yang valid.',
                'http'  => $result['http'],
                'json'  => null,
            ];
        }

        return ['ok' => true, 'error' => '', 'http' => $result['http'], 'json' => $json];
    }

    public function resolveCaBundle(): ?string
    {
        $candidates = [
            ini_get('curl.cainfo'),
            ini_get('openssl.cafile'),
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/usr/local/share/ca-certificates/cacert.pem',
            'C:\\xampp\\apache\\bin\\curl-ca-bundle.crt',
            'D:\\XAMPP85\\apache\\bin\\curl-ca-bundle.crt',
        ];

        foreach ($candidates as $path) {
            if (! empty($path) && is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Cari data kurs untuk satu currency pada tanggal tertentu (00:00:00 - 23:59:59).
     */
    protected function getExchangeRateByDate(int $idcurr, string $dateOnly): ?array
    {
        $row = $this->db->table('sc_mst.exchangerate')
            ->select('id, nilai, exchangedate')
            ->where('idcurr', $idcurr)
            ->where('exchangedate >=', $dateOnly . ' 00:00:00')
            ->where('exchangedate <', date('Y-m-d 00:00:00', strtotime($dateOnly . ' +1 day')))
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Data kurs terakhir untuk satu currency.
     */
    protected function getLatestExchangeRate(int $idcurr): ?array
    {
        $row = $this->db->table('sc_mst.exchangerate')
            ->select('id, nilai, exchangedate')
            ->where('idcurr', $idcurr)
            ->orderBy('exchangedate', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Mapping kode currency di sc_mst.currency ke kode API (mendukung alias YEN -> JPY).
     *
     * @return array<int, array{id:mixed, currcode:string, api_code:string}>
     */
    protected function resolveCurrencyRows(array $targets, array $setting): array
    {
        $alias = isset($setting['code_alias']) && is_array($setting['code_alias']) ? $setting['code_alias'] : [];

        $rows = $this->db->table('sc_mst.currency')
            ->select('id, currcode')
            ->orderBy('currcode', 'ASC')
            ->get()
            ->getResultArray();

        $selected = [];

        foreach ($rows as $row) {
            $code = strtoupper(trim((string) $row['currcode']));
            if ($code === '') {
                continue;
            }

            $apiCode = $code;
            if (isset($alias[$code]) && trim((string) $alias[$code]) !== '') {
                $apiCode = strtoupper(trim((string) $alias[$code]));
            }

            if (! in_array($code, $targets, true) && ! in_array($apiCode, $targets, true)) {
                continue;
            }

            $selected[] = [
                'id'       => $row['id'],
                'currcode' => $code,
                'api_code' => $apiCode,
            ];
        }

        return $selected;
    }

    /**
     * @return string[]
     */
    protected function normalizeCurrencyList(array $currencies): array
    {
        $result = [];

        foreach ($currencies as $code) {
            $code = strtoupper(trim((string) $code));
            if ($code !== '' && ! in_array($code, $result, true)) {
                $result[] = $code;
            }
        }

        return $result;
    }

    protected function toDateOnly($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp === false ? null : date('Y-m-d', $timestamp);
    }

    /**
     * Tulis log sinkronisasi (success / skipped / failed) ke writable/logs.
     */
    public function writeLog(string $message): void
    {
        $dir = WRITEPATH . 'logs';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents(
            $dir . DIRECTORY_SEPARATOR . self::LOG_FILE,
            '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}

