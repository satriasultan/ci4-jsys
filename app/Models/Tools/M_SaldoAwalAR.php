<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

/**
 * Staging & proses Opening AR (saldo awal piutang customer).
 *
 * Tabel  : sc_im.import_ar_batch, sc_im.import_ar (skema sql/opening_ar.sql)
 * Fungsi : sc_im.fn_validate_import_ar(batch, user)
 *          sc_im.fn_process_import_ar(batch, counter_coa, idbranch, cabang, user)
 */
class M_SaldoAwalAR extends Model
{
    /** Kolom staging sc_im.import_ar yang boleh diisi dari file Excel. */
    private $arColumns = [
        'row_no', 'tanggal_saldo', 'no_jurnal', 'kode_customer', 'nama_customer',
        'kode_prk', 'nama_prk', 'tanggal_dokumen', 'tanggal_jatuh_tempo',
        'dk', 'keterangan', 'mata_uang', 'kurs', 'nilai',
        'nilai_belum_jt', 'nilai_jt_1', 'nilai_jt_2', 'nilai_jt_3', 'nilai_jt_4',
        'umur_hari', 'kode_job', 'nama_job', 'alamat_customer', 'kota_customer',
        'kode_salesman', 'nama_salesman',
    ];

    /** @var array<string, array<int, string>> cache kolom tabel */
    private $tableColumns = [];

    /**
     * Daftar batch opening AR (terbaru di atas) + ringkasan baris.
     *
     * @return array<int, array<string, mixed>>
     */
    public function q_batches(int $limit = 50): array
    {
        $limit = $limit > 0 ? $limit : 50;

        return $this->query_rows(
            "select batch_id, batch_no, filename, tanggal_saldo, status,
                    total_rows, ready_rows, error_rows, processed_rows,
                    warning_rows, nilai_total, counter_idcoa, idbranch, cabang,
                    createdby, createddate, processedby, processeddate, notes
               from sc_im.v_import_ar_monitor
              order by batch_id desc
              limit {$limit}"
        );
    }

    /**
     * Satu batch (array kosong bila tidak ada).
     *
     * @return array<string, mixed>
     */
    public function q_batch(int $batchId): array
    {
        if ($batchId <= 0) {
            return [];
        }

        return $this->query_row(
            'select * from sc_im.import_ar_batch where id = ?',
            [$batchId]
        );
    }

    /**
     * Ringkasan seluruh staging opening AR + transaksi OPENAR yang sudah ada.
     *
     * @return array<string, int|float>
     */
    public function q_summary(): array
    {
        $row = $this->query_row(
            "select count(*)                                                          as baris,
                    count(distinct batch_id)                                          as batch,
                    count(*) filter (where row_status = 'READY')                       as ready,
                    count(*) filter (where row_status = 'ERROR')                       as error,
                    count(*) filter (where row_status = 'PROCESSED')                   as processed,
                    count(*) filter (where coalesce(btrim(warning_message), '') <> '') as warning,
                    coalesce(sum(abs(nilai)) filter (where row_status <> 'PROCESSED'), 0) as nilai_belum
               from sc_im.import_ar"
        );

        $trx = $this->query_row(
            "select count(*)                  as trx,
                    coalesce(sum(debet), 0)   as debet,
                    coalesce(sum(kredit), 0)  as kredit,
                    count(distinct kdcustomer) as customer
               from sc_trx.transaction_dt
              where btrim(journal_type::text) = 'OPENAR'"
        );

        return [
            'baris'       => (int) ($row['baris'] ?? 0),
            'batch'       => (int) ($row['batch'] ?? 0),
            'ready'       => (int) ($row['ready'] ?? 0),
            'error'       => (int) ($row['error'] ?? 0),
            'processed'   => (int) ($row['processed'] ?? 0),
            'warning'     => (int) ($row['warning'] ?? 0),
            'nilai_belum' => (float) ($row['nilai_belum'] ?? 0),
            'trx'         => (int) ($trx['trx'] ?? 0),
            'debet'       => (float) ($trx['debet'] ?? 0),
            'kredit'      => (float) ($trx['kredit'] ?? 0),
            'customer'    => (int) ($trx['customer'] ?? 0),
        ];
    }

    /**
     * Detail staging satu batch.
     *
     * @return array<int, array<string, mixed>>
     */
    public function q_detail(int $batchId, int $limit = 300): array
    {
        if ($batchId <= 0) {
            return [];
        }

        $limit = $limit > 0 ? $limit : 300;

        return $this->query_rows(
            "select id, batch_id, row_no, tanggal_saldo, no_jurnal, kode_customer,
                    nama_customer, kode_prk, nama_prk, tanggal_dokumen,
                    tanggal_jatuh_tempo, dk, mata_uang, kurs, nilai, umur_hari,
                    kode_salesman, row_status, error_message, warning_message,
                    transaction_uniqueid, transaction_id, processeddate
               from sc_im.import_ar
              where batch_id = ?
              order by row_no
              limit {$limit}",
            [$batchId]
        );
    }

    /**
     * Baris bermasalah (ERROR atau ada warning) pada satu batch.
     *
     * @return array<int, array<string, mixed>>
     */
    public function q_problems(int $batchId, int $limit = 50): array
    {
        if ($batchId <= 0) {
            return [];
        }

        $limit = $limit > 0 ? $limit : 50;

        return $this->query_rows(
            "select row_no, no_jurnal, kode_customer, kode_prk, dk, mata_uang,
                    nilai, row_status, error_message, warning_message
               from sc_im.import_ar
              where batch_id = ?
                and (row_status = 'ERROR'
                     or coalesce(btrim(warning_message), '') <> '')
              order by case when row_status = 'ERROR' then 0 else 1 end, row_no
              limit {$limit}",
            [$batchId]
        );
    }

    /**
     * Pilihan cabang (sc_mst.branch) untuk form Proses.
     *
     * @return array<int, array<string, mixed>>
     */
    public function q_branch_options(): array
    {
        return $this->query_rows(
            "select btrim(branch::text) as idbranch,
                    btrim(branchname::text) as cabang,
                    coalesce(btrim(cdefault::text), '') as cdefault
               from sc_mst.branch
              order by case when btrim(coalesce(cdefault::text, '')) = 'YES' then 0 else 1 end,
                       branch"
        );
    }

    /**
     * Daftar COA untuk bantuan pemilihan counter (perkiraan lawan).
     *
     * Dibatasi pada akun yang lazim dipakai sebagai akun lawan saldo awal
     * (kewajiban / ekuitas / nama mengandung "saldo") agar datalist tetap ringan.
     * Kode COA lain tetap bisa diketik manual dan divalidasi oleh fungsi database.
     *
     * @return array<int, array<string, mixed>>
     */
    public function q_coa_options(): array
    {
        return $this->query_rows(
            "select btrim(idcoa::text) as idcoa,
                    btrim(nmcoa::text) as nmcoa
               from sc_mst.coa
              where idcoa like '2%'
                 or idcoa like '3%'
                 or nmcoa ilike '%saldo%'
                 or nmcoa ilike '%ekuitas%'
                 or nmcoa ilike '%modal%'
              order by idcoa
              limit 500"
        );
    }


    /**
     * Buat header batch baru.
     *
     * @param array<string, mixed> $data batch_no, filename, tanggal_saldo, createdby
     */
    public function ins_batch(array $data): int
    {
        $row = $this->query_row(
            "insert into sc_im.import_ar_batch
                 (batch_no, filename, tanggal_saldo, createdby, status)
             values (?, ?, ?, ?, 'STAGED')
             returning id",
            [
                $data['batch_no'] ?? ('OPENAR-' . date('YmdHis')),
                $data['filename'] ?? '',
                $data['tanggal_saldo'] ?? null,
                $data['createdby'] ?? 'SYSTEM',
            ]
        );

        return (int) ($row['id'] ?? 0);
    }

    /**
     * Insert banyak baris staging sekaligus (satu statement).
     * Baris dengan (batch_id, row_no) yang sudah ada dilewati.
     *
     * @param  array<int, array<string, mixed>> $records
     * @return array{inserted: int, skipped: int}
     */
    public function ins_import_ar_batch(int $batchId, array $records): array
    {
        $result = ['inserted' => 0, 'skipped' => 0];

        if ($batchId <= 0 || $records === []) {
            return $result;
        }

        foreach ($records as $index => $record) {
            $records[$index]['batch_id'] = $batchId;
        }

        $columns = array_merge(['batch_id'], $this->arColumns);
        $columns = array_values(array_intersect($columns, $this->available_columns('import_ar')));

        if ($columns === []) {
            $result['skipped'] = count($records);

            return $result;
        }

        $tuple        = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $placeholders = [];
        $binds        = [];

        foreach ($records as $record) {
            $placeholders[] = $tuple;

            foreach ($columns as $column) {
                $value   = $record[$column] ?? null;
                $binds[] = $value === null ? null : (string) $value;
            }
        }

        $sql = 'insert into sc_im.import_ar ('
            . implode(', ', $columns) . ') values '
            . implode(', ', $placeholders)
            . ' on conflict do nothing';

        if ($this->db->query($sql, $binds) === false) {
            throw new \RuntimeException($this->db_error_message());
        }

        $result['inserted'] = (int) $this->db->affectedRows();
        $result['skipped']  = count($records) - $result['inserted'];

        return $result;
    }


    /**
     * Jalankan sc_im.fn_validate_import_ar(batch, user).
     *
     * @return array{total_rows: int, ready_rows: int, error_rows: int, warning_rows: int, elapsed_ms: int}
     */
    public function exe_validate(int $batchId, string $username = 'SYSTEM'): array
    {
        $this->require_function('sc_im.fn_validate_import_ar(bigint,character varying)');

        $started = microtime(true);

        $this->db->transStrict(false);
        $this->db->transBegin();

        try {
            $row = $this->query_row(
                'select * from sc_im.fn_validate_import_ar(?, ?)',
                [$batchId, $username === '' ? 'SYSTEM' : $username]
            );

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Validasi dibatalkan (rollback) oleh database.');
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        return [
            'total_rows'   => (int) ($row['total_rows'] ?? 0),
            'ready_rows'   => (int) ($row['ready_rows'] ?? 0),
            'error_rows'   => (int) ($row['error_rows'] ?? 0),
            'warning_rows' => (int) ($row['warning_rows'] ?? 0),
            'elapsed_ms'   => (int) round((microtime(true) - $started) * 1000),
        ];
    }


    /**
     * Jalankan sc_im.fn_process_import_ar(batch, counter, idbranch, cabang, user).
     *
     * @return array<string, mixed>
     */
    public function exe_process(
        int $batchId,
        string $counterIdcoa,
        string $idbranch = '',
        string $cabang = '',
        string $username = 'SYSTEM'
    ): array {
        $this->require_function('sc_im.fn_process_import_ar(bigint,character varying,character varying,character varying,character varying)');

        $started = microtime(true);

        $this->db->transStrict(false);
        $this->db->transBegin();

        try {
            $row = $this->query_row(
                'select * from sc_im.fn_process_import_ar(?, ?, ?, ?, ?)',
                [$batchId, $counterIdcoa, $idbranch, $cabang, $username === '' ? 'SYSTEM' : $username]
            );

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Proses dibatalkan (rollback) oleh database.');
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        return [
            'batch_id'       => (int) ($row['batch_id'] ?? $batchId),
            'status'         => (string) ($row['status'] ?? ''),
            'total_rows'     => (int) ($row['total_rows'] ?? 0),
            'ready_rows'     => (int) ($row['ready_rows'] ?? 0),
            'error_rows'     => (int) ($row['error_rows'] ?? 0),
            'processed_rows' => (int) ($row['processed_rows'] ?? 0),
            'message'        => (string) ($row['message'] ?? ''),
            'elapsed_ms'     => (int) round((microtime(true) - $started) * 1000),
        ];
    }


    /**
     * Kolom yang benar-benar ada pada sc_im.<tabel>.
     *
     * @return array<int, string>
     */
    public function available_columns(string $table): array
    {
        $table = strtolower(preg_replace('/[^a-z0-9_]/i', '', $table));

        if ($table === '') {
            return [];
        }

        if (! isset($this->tableColumns[$table])) {
            $rows = $this->query_rows(
                "select column_name
                   from information_schema.columns
                  where table_schema = 'sc_im'
                    and table_name = ?
                  order by ordinal_position",
                [$table]
            );

            $this->tableColumns[$table] = array_map(
                static fn ($row) => strtolower((string) ($row['column_name'] ?? '')),
                $rows
            );
        }

        return $this->tableColumns[$table];
    }

    /**
     * Pastikan fungsi database tersedia sebelum dipanggil.
     */
    private function require_function(string $signature): void
    {
        $row = $this->query_row('select to_regprocedure(?) as fn', [$signature]);

        if (($row['fn'] ?? null) === null) {
            throw new \RuntimeException('Fungsi ' . $signature
                . ' tidak ditemukan. Jalankan sql/opening_ar.sql terlebih dahulu.');
        }
    }

    /**
     * Query satu baris; mengangkat pesan error database yang sebenarnya.
     *
     * @param  array<int, mixed> $binds
     * @return array<string, mixed>
     */
    private function query_row(string $sql, array $binds = []): array
    {
        $query = $this->db->query($sql, $binds);

        if ($query === false) {
            throw new \RuntimeException($this->db_error_message());
        }

        $row = $query->getRowArray();

        return is_array($row) ? $row : [];
    }

    /**
     * Query banyak baris; mengangkat pesan error database yang sebenarnya.
     *
     * @param  array<int, mixed> $binds
     * @return array<int, array<string, mixed>>
     */
    private function query_rows(string $sql, array $binds = []): array
    {
        $query = $this->db->query($sql, $binds);

        if ($query === false) {
            throw new \RuntimeException($this->db_error_message());
        }

        $rows = $query->getResultArray();

        return is_array($rows) ? $rows : [];
    }

    /**
     * Pesan error database terakhir dalam satu baris (siap ditampilkan).
     */
    private function db_error_message(): string
    {
        $error   = $this->db->error();
        $message = trim(preg_replace('/\s+/', ' ', (string) ($error['message'] ?? '')));

        return $message !== '' ? $message : 'Query database gagal dijalankan (pesan driver kosong).';
    }
}

