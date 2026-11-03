<?php

namespace App\Models\Tools;

use CodeIgniter\Model;

class M_PurchaseUpload extends Model
{
    /** Kolom PP yang boleh diisi (kolom yang tidak ada di tabel otomatis dilewati). */
    private $lapPpColumns = [
        'pp', 'jurnal', 'date_', 'username', 'brg', 'qty', 'qty_realisasi', 'lns', 'gdg',
        'nama_user', 'nama_barang', 'unit', 'spec', 'job', 'rem_pp', 'rem_pd',
        'date_pakai', 'docdate', 'estpakai', 'uniqueidpd',
        'status', 'inputby', 'inputdate', 'updateby', 'updatedate',
    ];

    /** Kolom PO yang boleh diisi (mengikuti staging sheet "template_import"). */
    private $lapPoColumns = [
        'no_po', 'keterangan_po', 'kode_supplier', 'nama_supplier', 'kode_barang', 'nama_barang',
        'keterangan_detail', 'qty_po', 'satuan', 'harga_po', 'total_price', 'real_price',
        'ppn_nonppn', 'mata_uang', 'kurs', 'category', 'tahun_po', 'etd', 'docdate', 'senddate',
        'cabang', 'user_pemohon', 'exclude_include', 'nilai_dpp', 'nilai_pajak', 'nilai_konversi',
        'batch', 'etdnotes', 'uniqueidpo',
    ];


    /** @var array<string, array<int, string>> cache kolom tabel */
    private $tableColumns = [];

    /**
     * Daftar data PP hasil upload (terbaru di atas).
     */
    public function q_lap_pp_win(string $param = '', int $limit = 500)
    {
        $limit = $limit > 0 ? $limit : 500;

        return $this->db->query("select * from sc_im.lap_pp_win
                                where pp is not null $param
                                order by id desc
                                limit $limit");
    }

    /**
     * Daftar data PO hasil upload (terbaru di atas).
     */
    public function q_lap_po_win(string $param = '', int $limit = 500)
    {
        $limit = $limit > 0 ? $limit : 500;

        return $this->db->query("select * from sc_im.lap_po_win
                                where no_po is not null $param
                                order by id desc
                                limit $limit");
    }

    /**
     * Simpan satu baris PP. Baris yang sudah ada (sesuai primary key) dilewati.
     */
    public function ins_lap_pp_win(array $data): bool
    {
        $result = $this->ins_lap_pp_win_batch([$data]);

        return $result['inserted'] > 0;
    }

    /**
     * Simpan banyak baris PP sekaligus (satu statement).
     *
     * @return array{inserted: int, skipped: int}
     */
    public function ins_lap_pp_win_batch(array $records): array
    {
        return $this->ins_batch('lap_pp_win', $records, $this->lapPpColumns);
    }

    /**
     * Simpan satu baris PO. Baris yang sudah ada (sesuai primary key) dilewati.
     */
    public function ins_lap_po_win(array $data): bool
    {
        $result = $this->ins_lap_po_win_batch([$data]);

        return $result['inserted'] > 0;
    }

    /**
     * Simpan banyak baris PO sekaligus (satu statement).
     *
     * @return array{inserted: int, skipped: int}
     */
    public function ins_lap_po_win_batch(array $records): array
    {
        return $this->ins_batch('lap_po_win', $records, $this->lapPoColumns);
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
            $rows = $this->db->query(
                "select column_name
                   from information_schema.columns
                  where table_schema = 'sc_im'
                    and table_name = ?
                  order by ordinal_position",
                [$table]
            )->getResultArray();

            $this->tableColumns[$table] = array_map(
                static fn ($row) => strtolower((string) ($row['column_name'] ?? '')),
                $rows
            );
        }

        return $this->tableColumns[$table];
    }

    /**
     * Kolom sc_im.lap_pp_win (dipakai untuk validasi kolom template PP).
     *
     * @return array<int, string>
     */
    public function available_lap_pp_columns(): array
    {
        return $this->available_columns('lap_pp_win');
    }

    /**
     * Kolom sc_im.lap_po_win (dipakai untuk validasi kolom template PO).
     *
     * @return array<int, string>
     */
    public function available_lap_po_columns(): array
    {
        return $this->available_columns('lap_po_win');
    }

    /**
     * Jumlah baris sc_trx.pp dan sc_trx.pp_dtl saat ini.
     *
     * @return array{pp: int, pp_dtl: int}
     */
    public function q_pp_trx_counts(): array
    {
        $row = $this->db->query("select (select count(*) from sc_trx.pp)     as pp,
                                        (select count(*) from sc_trx.pp_dtl) as pp_dtl")->getRowArray();

        return [
            'pp'     => (int) ($row['pp'] ?? 0),
            'pp_dtl' => (int) ($row['pp_dtl'] ?? 0),
        ];
    }

    /**
     * Ringkasan kesiapan data PP yang akan dipindahkan ke transaksi.
     *
     * @return array<string, int>
     */
    public function q_pp_sync_summary(): array
    {
        $row = $this->db->query("with src as (
                                    select distinct btrim(pp) as docno
                                      from sc_im.lap_pp_win
                                     where nullif(btrim(pp), '') is not null
                                       and (upper(btrim(pp)) like '%PA%' or upper(btrim(pp)) like '%PB%')
                                 )
                                 select (select count(*) from sc_im.lap_pp_win) as baris_pp,
                                        (select count(*) from src)              as total_pp,
                                        (select count(*) from src s
                                          where exists (select 1
                                                          from sc_trx.pp p
                                                         where btrim(p.docno) = s.docno)) as sudah_pp,
                                        (select count(*) from sc_trx.pp)     as trx_pp,
                                        (select count(*) from sc_trx.pp_dtl) as trx_pp_dtl")->getRowArray();

        return [
            'baris_pp'   => (int) ($row['baris_pp'] ?? 0),
            'total_pp'   => (int) ($row['total_pp'] ?? 0),
            'sudah_pp'   => (int) ($row['sudah_pp'] ?? 0),
            'belum_pp'   => max((int) ($row['total_pp'] ?? 0) - (int) ($row['sudah_pp'] ?? 0), 0),
            'trx_pp'     => (int) ($row['trx_pp'] ?? 0),
            'trx_pp_dtl' => (int) ($row['trx_pp_dtl'] ?? 0),
        ];
    }

    /**
     * Jalankan prosedur sc_im.sp_import_lap_pp_to_trx().
     *
     * Prosedur memindahkan PP dari sc_im.lap_pp_win ke sc_trx.pp / sc_trx.pp_dtl.
     * Aman dijalankan berulang: dokumen yang sudah ada tidak diduplikasi.
     *
     * @return array{header: int, detail: int, header_total: int, detail_total: int, elapsed_ms: int}
     */
    public function exe_import_pp_to_trx(): array
    {
        $exists = $this->db->query(
            "select to_regprocedure('sc_im.sp_import_lap_pp_to_trx()') as fn"
        )->getRowArray();

        if (($exists['fn'] ?? null) === null) {
            throw new \RuntimeException('Prosedur sc_im.sp_import_lap_pp_to_trx() tidak ditemukan pada database.');
        }

        $before  = $this->q_pp_trx_counts();
        $started = microtime(true);

        // transStrict(false): kegagalan query tidak "menempel" pada transaksi berikutnya.
        $this->db->transStrict(false);
        $this->db->transStart();

        try {
            $this->db->query('CALL sc_im.sp_import_lap_pp_to_trx()');

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi dibatalkan (rollback) oleh database.');
            }

            $this->db->transComplete();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        $after = $this->q_pp_trx_counts();

        return [
            'header'       => max($after['pp'] - $before['pp'], 0),
            'detail'       => max($after['pp_dtl'] - $before['pp_dtl'], 0),
            'header_total' => $after['pp'],
            'detail_total' => $after['pp_dtl'],
            'elapsed_ms'   => (int) round((microtime(true) - $started) * 1000),
        ];
    }

    /**
     * Jumlah baris sc_trx.po dan sc_trx.po_dtl saat ini.
     *
     * @return array{po: int, po_dtl: int}
     */
    public function q_po_trx_counts(): array
    {
        $row = $this->db->query("select (select count(*) from sc_trx.po)     as po,
                                        (select count(*) from sc_trx.po_dtl) as po_dtl")->getRowArray();

        return [
            'po'     => (int) ($row['po'] ?? 0),
            'po_dtl' => (int) ($row['po_dtl'] ?? 0),
        ];
    }

    /**
     * Ringkasan kesiapan data PO yang akan dipindahkan ke transaksi.
     *
     * @return array<string, int>
     */
    public function q_po_sync_summary(): array
    {
        $row = $this->db->query("with src as (
                                    select distinct btrim(no_po) as docno
                                      from sc_im.lap_po_win
                                     where nullif(btrim(no_po), '') is not null
                                 )
                                 select (select count(*) from sc_im.lap_po_win) as baris_po,
                                        (select count(*) from src)              as total_po,
                                        (select count(*) from src s
                                          where exists (select 1
                                                          from sc_trx.po p
                                                         where btrim(p.docno) = s.docno)) as sudah_po,
                                        (select count(*) from sc_trx.po)     as trx_po,
                                        (select count(*) from sc_trx.po_dtl) as trx_po_dtl")->getRowArray();

        return [
            'baris_po'   => (int) ($row['baris_po'] ?? 0),
            'total_po'   => (int) ($row['total_po'] ?? 0),
            'sudah_po'   => (int) ($row['sudah_po'] ?? 0),
            'belum_po'   => max((int) ($row['total_po'] ?? 0) - (int) ($row['sudah_po'] ?? 0), 0),
            'trx_po'     => (int) ($row['trx_po'] ?? 0),
            'trx_po_dtl' => (int) ($row['trx_po_dtl'] ?? 0),
        ];
    }

    /**
     * Jalankan prosedur sc_im.sp_import_template_po().
     *
     * Prosedur memindahkan PO dari sc_im.lap_po_win ke sc_trx.po / sc_trx.po_dtl.
     * Aman dijalankan berulang: dokumen yang sudah ada tidak diduplikasi.
     *
     * @return array{header: int, detail: int, header_total: int, detail_total: int, elapsed_ms: int}
     */
    public function exe_import_po_to_trx(): array
    {
        $exists = $this->db->query(
            "select to_regprocedure('sc_im.sp_import_template_po()') as fn"
        )->getRowArray();

        if (($exists['fn'] ?? null) === null) {
            throw new \RuntimeException('Prosedur sc_im.sp_import_template_po() tidak ditemukan pada database.');
        }

        $before  = $this->q_po_trx_counts();
        $started = microtime(true);

        // transStrict(false): kegagalan query tidak "menempel" pada transaksi berikutnya.
        $this->db->transStrict(false);
        $this->db->transStart();

        try {
            $this->db->query('CALL sc_im.sp_import_template_po()');

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi dibatalkan (rollback) oleh database.');
            }

            $this->db->transComplete();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }

        $after = $this->q_po_trx_counts();

        return [
            'header'       => max($after['po'] - $before['po'], 0),
            'detail'       => max($after['po_dtl'] - $before['po_dtl'], 0),
            'header_total' => $after['po'],
            'detail_total' => $after['po_dtl'],
            'elapsed_ms'   => (int) round((microtime(true) - $started) * 1000),
        ];
    }


    /**
     * Insert banyak baris dalam satu statement, baris duplikat dilewati.
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

        $tuple = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';

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

        $this->db->query($sql, $binds);

        $result['inserted'] = (int) $this->db->affectedRows();
        $result['skipped']  = count($records) - $result['inserted'];

        return $result;
    }
}
