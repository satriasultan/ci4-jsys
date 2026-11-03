<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class M_StockUpload extends Model
{
    /** Kolom staging yang boleh diisi (mengikuti sheet "template_stock_persediaan"). */
    private $openingColumns = [
        'tanggal_saldo', 'idbranch', 'cabang', 'kode_gudang', 'nama_gudang',
        'kode_barang', 'nama_barang', 'satuan', 'batch_spec',
        'qty_saldo', 'nilai_saldo', 'harga_rata_rata', 'keterangan',
        'status_validasi', 'uniqueidopening', 'inputby', 'inputdate',
    ];

    /** @var array<string, array<int, string>> cache kolom tabel */
    private $tableColumns = [];

    /**
     * Daftar baris staging opening stock (terbaru di atas).
     */
    public function q_stock_opening(string $param = '', int $limit = 500)
    {
        $limit = $limit > 0 ? $limit : 500;

        $query = $this->db->query("select * from sc_im.stock_persediaan_opening
                                where id is not null $param
                                order by id desc
                                limit $limit");

        if ($query === false) {
            throw new \RuntimeException($this->db_error_message());
        }

        return $query;
    }

    /**
     * Ringkasan staging + ringkasan buku besar persediaan.
     *
     * @return array<string, int|float>
     */
    public function q_stock_summary(): array
    {
        $row = $this->query_row("select count(*)                                                      as baris,
                                        count(*) filter (where upper(btrim(status_validasi)) = 'DRAFT')    as draft,
                                        count(*) filter (where upper(btrim(status_validasi)) = 'VALID')    as valid,
                                        count(*) filter (where upper(btrim(status_validasi)) = 'POSTED')   as posted,
                                        count(*) filter (where validasi_catatan is not null)               as bermasalah,
                                        coalesce(sum(qty_saldo) filter (where upper(btrim(status_validasi)) <> 'POSTED'), 0)   as qty,
                                        coalesce(sum(nilai_saldo) filter (where upper(btrim(status_validasi)) <> 'POSTED'), 0) as nilai,
                                        count(distinct btrim(kode_gudang))                        as gudang,
                                        count(distinct btrim(kode_barang))                        as barang
                                   from sc_im.stock_persediaan_opening");

        $trx = $this->query_row("select count(*)                                                              as stkblc,
                                        count(*) filter (where upper(btrim(ctype)) = 'SLD')                   as stkblc_sld,
                                        count(*) filter (where upper(btrim(ctype)) = 'SLD'
                                                           and upper(btrim(hist)) = 'SALDO AWAL')             as stkblc_opening,
                                        count(distinct btrim(idlocation))                                     as gudang
                                   from sc_trx.stkblc");

        return [
            'baris'          => (int) ($row['baris'] ?? 0),
            'draft'          => (int) ($row['draft'] ?? 0),
            'valid'          => (int) ($row['valid'] ?? 0),
            'posted'         => (int) ($row['posted'] ?? 0),
            'bermasalah'     => (int) ($row['bermasalah'] ?? 0),
            'qty'            => (float) ($row['qty'] ?? 0),
            'nilai'          => (float) ($row['nilai'] ?? 0),
            'gudang'         => (int) ($row['gudang'] ?? 0),
            'barang'         => (int) ($row['barang'] ?? 0),
            'stkblc'         => (int) ($trx['stkblc'] ?? 0),
            'stkblc_sld'     => (int) ($trx['stkblc_sld'] ?? 0),
            'stkblc_opening' => (int) ($trx['stkblc_opening'] ?? 0),
            'stkblc_gudang'  => (int) ($trx['gudang'] ?? 0),
        ];
    }

    /**
     * Simpan satu baris staging opening stock.
     */
    public function ins_stock_persediaan_opening(array $data): bool
    {
        $result = $this->ins_stock_persediaan_opening_batch([$data]);

        return $result['inserted'] > 0;
    }

    /**
     * Simpan banyak baris staging sekaligus (satu statement).
     * Baris dengan uniqueidopening yang sudah ada dilewati (upload ulang idempoten).
     *
     * @return array{inserted: int, skipped: int}
     */
    public function ins_stock_persediaan_opening_batch(array $records): array
    {
        return $this->ins_batch('stock_persediaan_opening', $records, $this->openingColumns);
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
     * Kolom sc_im.stock_persediaan_opening (dipakai untuk validasi kolom template).
     *
     * @return array<int, string>
     */
    public function available_stock_opening_columns(): array
    {
        return $this->available_columns('stock_persediaan_opening');
    }

    /**
     * Jalankan sc_im.fn_validasi_stock_persediaan_opening().
     *
     * @return array{total: int, valid: int, draft: int, posted: int, bermasalah: int,
     *               qty: float, nilai: float, gudang: int, barang: int, elapsed_ms: int}
     */
    public function exe_validasi_stock_opening(): array
    {
        $this->require_function('sc_im.fn_validasi_stock_persediaan_opening()');

        $started = microtime(true);

        $this->db->transStrict(false);
        $this->db->transBegin();

        try {
            $row = $this->query_row('select * from sc_im.fn_validasi_stock_persediaan_opening()');

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Validasi dibatalkan (rollback) oleh database.');
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        return [
            'total'      => (int) ($row['r_total'] ?? 0),
            'valid'      => (int) ($row['r_valid'] ?? 0),
            'draft'      => (int) ($row['r_draft'] ?? 0),
            'posted'     => (int) ($row['r_posted'] ?? 0),
            'bermasalah' => (int) ($row['r_bermasalah'] ?? 0),
            'qty'        => (float) ($row['r_qty'] ?? 0),
            'nilai'      => (float) ($row['r_nilai'] ?? 0),
            'gudang'     => (int) ($row['r_gudang'] ?? 0),
            'barang'     => (int) ($row['r_barang'] ?? 0),
            'elapsed_ms' => (int) round((microtime(true) - $started) * 1000),
        ];
    }

    /**
     * Jalankan sc_im.fn_posting_stock_persediaan_opening(username).
     * Baris VALID diposting ke sc_trx.stkblc lalu ditandai POSTED.
     *
     * @return array{calon: int, inserted: int, skipped: int, posted: int, pending: int, elapsed_ms: int}
     */
    public function exe_posting_stock_opening(string $username = 'SYSTEM'): array
    {
        $this->require_function('sc_im.fn_posting_stock_persediaan_opening(character varying)');

        $started = microtime(true);

        $this->db->transStrict(false);
        $this->db->transBegin();

        try {
            $row = $this->query_row(
                'select * from sc_im.fn_posting_stock_persediaan_opening(?)',
                [$username === '' ? 'SYSTEM' : $username]
            );

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Posting dibatalkan (rollback) oleh database.');
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        return [
            'calon'      => (int) ($row['r_calon'] ?? 0),
            'inserted'   => (int) ($row['r_inserted'] ?? 0),
            'skipped'    => (int) ($row['r_skipped'] ?? 0),
            'posted'     => (int) ($row['r_posted'] ?? 0),
            'pending'    => (int) ($row['r_pending'] ?? 0),
            'elapsed_ms' => (int) round((microtime(true) - $started) * 1000),
        ];
    }

    /**
     * Pastikan fungsi database tersedia sebelum dipanggil.
     */
    private function require_function(string $signature): void
    {
        $row = $this->query_row('select to_regprocedure(?) as fn', [$signature]);

        if (($row['fn'] ?? null) === null) {
            throw new \RuntimeException('Fungsi ' . $signature
                . ' tidak ditemukan. Jalankan sql/stock_persediaan_opening.sql terlebih dahulu.');
        }
    }

    /**
     * Jalankan query yang mengembalikan satu baris.
     *
     * Driver Postgre mengembalikan FALSE (bukan exception) bila DBDebug = false
     * dan query gagal; memanggil getRowArray() pada nilai FALSE hanya memunculkan
     * "Call to a member function getRowArray() on bool" tanpa sebabnya.
     * Helper ini mengangkat pesan error database yang sebenarnya.
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
     * Jalankan query yang mengembalikan banyak baris (lihat catatan query_row()).
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

    /**
     * Insert banyak baris dalam satu statement; baris duplikat (uniqueidopening) dilewati.
     *
     * @param  array<int, array<string, mixed>> $records
     * @return array{inserted: int, skipped: int}
     */
    private function ins_batch(string $table, array $records, array $allowed): array
    {
        $result = ['inserted' => 0, 'skipped' => 0];

        if ($records === []) {
            return $result;
        }

        // Hanya kolom yang ada di tabel yang dikirim, supaya template & skema bisa berbeda.
        $columns = array_values(array_intersect($allowed, $this->available_columns($table)));

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

        $sql = 'insert into sc_im.' . $table
            . ' (' . implode(', ', $columns) . ') values '
            . implode(', ', $placeholders)
            . ' on conflict do nothing';

        // Insert massal bisa gagal (constraint/tipe kolom); jangan diam-diam
        // melaporkan "0 baris masuk" tanpa pesan penyebabnya.
        if ($this->db->query($sql, $binds) === false) {
            throw new \RuntimeException($this->db_error_message());
        }

        $result['inserted'] = (int) $this->db->affectedRows();
        $result['skipped']  = count($records) - $result['inserted'];

        return $result;
    }
}
