<?php

namespace App\Models\Sales;

/**
 * Delivery Order (DO) data access.
 *
 * Hanya menangani sc_tmp.deliveryorder(_dtl) dan pembacaan sc_trx.salesorder(_dtl).
 * Tidak ada DDL / perubahan skema di sini.
 */
class DeliveryOrderModel
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /* ------------------------------------------------------------------
     * HELPERS
     * ------------------------------------------------------------------ */

    public function num($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $clean = str_replace([' ', ','], ['', '.'], (string) $value);
        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    public function flag($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        $v = strtolower(trim((string) $value));
        return in_array($v, ['1', 'true', 't', 'yes', 'y'], true) ? 'true' : 'false';
    }

    protected function tmpHeader()
    {
        return $this->db->table('sc_tmp.deliveryorder');
    }

    protected function tmpDetail()
    {
        return $this->db->table('sc_tmp.deliveryorder_dtl');
    }

    /**
     * Batasi panjang nilai agar sesuai kolom CHARACTER(n) (hindari
     * "value too long for type character(n)" dari PostgreSQL).
     */
    protected function fit(array $data, array $limits): array
    {
        foreach ($limits as $key => $len) {
            if (array_key_exists($key, $data) && $data[$key] !== null) {
                $data[$key] = substr((string) $data[$key], 0, $len);
            }
        }
        return $data;
    }

    /* ------------------------------------------------------------------
     * READ
     * ------------------------------------------------------------------ */

    public function getHeader(string $docno, string $source = 'sc_tmp'): ?array
    {
        $docno = trim($docno);
        if ($docno === '') {
            return null;
        }
        $row = $this->db->table($source . '.deliveryorder')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        if (!$row && $source === 'sc_tmp') {
            $row = $this->db->table('sc_trx.deliveryorder')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();
        }
        return $row ?: null;
    }

    public function getDetail(string $docno, string $source = 'sc_tmp'): array
    {
        $docno = trim($docno);
        if ($docno === '') {
            return [];
        }
        return $this->db->table($source . '.deliveryorder_dtl')
            ->where('docno', $docno)
            ->orderBy('idurut_view', 'ASC')
            ->orderBy('idurut', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function maxIdurut(string $docno): int
    {
        $row = $this->tmpDetail()->selectMax('idurut', 'mx')->where('docno', trim($docno))->get()->getRowArray();
        return (int) ($row['mx'] ?? 0);
    }

    public function maxView(string $docno): int
    {
        $row = $this->tmpDetail()->selectMax('idurut_view', 'mx')->where('docno', trim($docno))->get()->getRowArray();
        return (int) ($row['mx'] ?? 0);
    }

    public function getRow(string $docno, int $idurut): ?array
    {
        $row = $this->tmpDetail()
            ->where('docno', trim($docno))
            ->where('idurut', $idurut)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    /* ------------------------------------------------------------------
     * HEADER
     * ------------------------------------------------------------------ */

    public function saveHeader(array $data, string $nama): ?array
    {
        $docno = strtoupper(trim((string) ($data['docno'] ?? '')));
        if ($docno === '') {
            return null;
        }

        $docdate = trim((string) ($data['docdate'] ?? ''));
        if ($docdate !== '') {
            $ts = strtotime(str_replace('/', '-', $docdate));
            $docdate = $ts ? date('Y-m-d', $ts) : date('Y-m-d');
        } else {
            $docdate = date('Y-m-d');
        }

        $payload = [
            'docdate'              => $docdate,
            'cabang'               => strtoupper(trim((string) ($data['cabang'] ?? ''))),
            'pemohon'              => strtoupper(trim((string) ($data['pemohon'] ?? ''))),
            'kdcustomer'           => strtoupper(trim((string) ($data['kdcustomer'] ?? ''))),
            'nmcustomer'           => strtoupper(trim((string) ($data['nmcustomer'] ?? ''))),
            'alamatcustomer'       => strtoupper(trim((string) ($data['alamatcustomer'] ?? ''))),
            'gradecustomer'        => strtoupper(trim((string) ($data['gradecustomer'] ?? ''))),
            'kdcustomerdeliv'      => strtoupper(trim((string) ($data['kdcustomerdeliv'] ?? ''))),
            'nmcustomerdeliv'      => strtoupper(trim((string) ($data['nmcustomerdeliv'] ?? ''))),
            'alamatcustomerdeliv'  => strtoupper(trim((string) ($data['alamatcustomerdeliv'] ?? ''))),
            'kdsalesman'           => strtoupper(trim((string) ($data['kdsalesman'] ?? ''))),
            'jthtempo'             => $this->num($data['jthtempo'] ?? 0),
            'idtax'                => strtoupper(trim((string) ($data['idtax'] ?? ''))),
            'isinclusive'          => $this->flag($data['isinclusive'] ?? 'false'),
            'isopenprice'          => $this->flag($data['isopenprice'] ?? 'false'),
            'currcode'             => strtoupper(trim((string) ($data['currcode'] ?? ''))),
            'kurs'                 => $this->num($data['kurs'] ?? 1),
            'carabayar'            => strtoupper(trim((string) ($data['carabayar'] ?? ''))),
            'keterangan'           => strtoupper(trim((string) ($data['keterangan'] ?? ''))),
            'docnoso'              => strtoupper(trim((string) ($data['docnoso'] ?? ''))),
            'idrev'                => strtoupper(trim((string) ($data['idrev'] ?? ''))),
            'updateby'             => $nama,
            'updatedate'           => date('Y-m-d H:i:s'),
        ];

        $payload = $this->fit($payload, [
            'cabang' => 30, 'pemohon' => 100, 'kdcustomer' => 30, 'nmcustomer' => 250,
            'gradecustomer' => 30, 'kdcustomerdeliv' => 30, 'nmcustomerdeliv' => 250,
            'kdsalesman' => 10, 'idtax' => 20, 'currcode' => 3, 'carabayar' => 30,
            'docnoso' => 30, 'idrev' => 30,
        ]);

        $exists = $this->tmpHeader()->where('docno', $docno)->countAllResults();
        if ($exists > 0) {
            $this->tmpHeader()->where('docno', $docno)->update($payload);
        } else {
            $payload['docno']     = $docno;
            $payload['status']    = 'E';
            $payload['inputby']   = $nama;
            $payload['inputdate'] = date('Y-m-d H:i:s');
            $this->tmpHeader()->insert($payload);
        }

        return $this->getHeader($docno);
    }

    public function recalcTotals(string $docno): ?array
    {
        $docno = trim($docno);
        if ($docno === '') {
            return null;
        }
        $row = $this->db->query(
            "SELECT COALESCE(SUM(nilai),0) AS dpp,
                    COALESCE(SUM(nilaipajak),0) AS pajak
               FROM sc_tmp.deliveryorder_dtl
              WHERE docno = ?",
            [$docno]
        )->getRowArray();

        $dpp   = (float) ($row['dpp'] ?? 0);
        $pajak = (float) ($row['pajak'] ?? 0);

        $this->tmpHeader()->where('docno', $docno)->update([
            'dpp'         => $dpp,
            'jumlahpajak' => $pajak,
            'total'       => $dpp + $pajak,
        ]);

        return $this->getHeader($docno);
    }


    /* ------------------------------------------------------------------
     * DETAIL
     * ------------------------------------------------------------------ */

    public function saveDetail(array $data, string $nama): ?array
    {
        $docno = strtoupper(trim((string) ($data['docno'] ?? '')));
        if ($docno === '') {
            return null;
        }

        $qty        = $this->num($data['qty'] ?? 0);
        $harga      = $this->num($data['harga'] ?? 0);
        $multidisc  = $this->num($data['multidisc'] ?? 0);
        $hargasatuan = $this->num($data['hargasatuan'] ?? $harga);
        $kurs       = $this->num($data['kurs'] ?? 1);
        $nilai      = $qty * $harga * (1 - ($multidisc / 100));

        // Stok akhir WAJIB dihitung ulang di server saat insert/update,
        // berdasarkan gudang + idbarang + spec (bukan nilai kiriman client).
        $idbrgStok  = strtoupper(trim((string) ($data['idbarang'] ?? '')));
        $gudangStok = strtoupper(trim((string) ($data['idgudang'] ?? '')));
        $specStok   = strtoupper(trim((string) ($data['idspec'] ?? '')));
        $docdateStok = $this->getDocDate($docno);

        $payload = [
            'docno'        => $docno,
            'docnoso'      => strtoupper(trim((string) ($data['docnoso'] ?? ''))),
            'idrev'        => strtoupper(trim((string) ($data['idrev'] ?? ''))),
            'idurutso'     => (int) ($data['idurutso'] ?? 0),
            'uniqueidso'   => trim((string) ($data['uniqueidso'] ?? '')),
            'idbarangso'   => strtoupper(trim((string) ($data['idbarangso'] ?? ''))),
            'nmbarangso'   => strtoupper(trim((string) ($data['nmbarangso'] ?? ''))),
            'idbarang'     => strtoupper(trim((string) ($data['idbarang'] ?? ''))),
            'nmbarang'     => strtoupper(trim((string) ($data['nmbarang'] ?? ''))),
            'idgudang'     => strtoupper(trim((string) ($data['idgudang'] ?? ''))),
            'idspec'       => strtoupper(trim((string) ($data['idspec'] ?? ''))),
            'unit'         => strtoupper(trim((string) ($data['unit'] ?? ''))),
            'qty'          => $qty,
            'qtyso'        => $this->num($data['qtyso'] ?? 0),
            'harga'        => $harga,
            'hargasatuan'  => $hargasatuan,
            'multidisc'    => $multidisc,
            'nilai'        => $nilai,
            'nilaikonversi' => $nilai * ($kurs > 0 ? $kurs : 1),
            'idtax'        => strtoupper(trim((string) ($data['idtax'] ?? ''))),
            'currcode'     => strtoupper(trim((string) ($data['currcode'] ?? ''))),
            'kurs'         => $kurs,
            'stokakhir'    => $this->stokAkhir($idbrgStok, $gudangStok, $specStok, $docdateStok),
            'bomdesc'      => (string) ($data['bomdesc'] ?? ''),
            'description'  => strtoupper(trim((string) ($data['description'] ?? ''))),
            'status'       => 'E',
            'updateby'     => $nama,
            'updatedate'   => date('Y-m-d H:i:s'),
        ];

        $payload = $this->fit($payload, [
            'docno' => 30, 'docnoso' => 30, 'idrev' => 30, 'uniqueidso' => 64,
            'idbarangso' => 20, 'nmbarangso' => 150, 'idbarang' => 20, 'nmbarang' => 150,
            'idgudang' => 30, 'idspec' => 30, 'unit' => 20, 'idtax' => 20, 'currcode' => 3,
        ]);

        $idurut = (int) ($data['idurut'] ?? 0);

        if ($idurut > 0) {
            $this->tmpDetail()->where('docno', $docno)->where('idurut', $idurut)->update($payload);
        } else {
            $payload['idurut_view'] = $this->maxView($docno) + 1;
            $payload['uniqueid']    = trim((string) ($data['uniqueid'] ?? ''));
            if ($payload['uniqueid'] === '') {
                $payload['uniqueid'] = md5(uniqid('', true) . random_bytes(8));
            }
            $payload['inputby']   = $nama;
            $payload['inputdate'] = date('Y-m-d H:i:s');
            $this->tmpDetail()->insert($payload);
            $idurut = (int) $this->db->insertID();
            if ($idurut <= 0) {
                $idurut = $this->maxIdurut($docno);
            }
        }

        $this->recalcTotals($docno);
        return $this->getRow($docno, $idurut);
    }

    public function deleteDetail(string $docno, int $idurut): bool
    {
        $docno = trim($docno);
        if ($docno === '' || $idurut <= 0) {
            return false;
        }
        $deleted = $this->tmpDetail()->where('docno', $docno)->where('idurut', $idurut)->delete();
        $this->recalcTotals($docno);
        return (bool) $deleted;
    }

    public function swapView(string $docno, int $idurutView, string $direction): bool
    {
        $docno = trim($docno);
        if ($docno === '' || $idurutView <= 0) {
            return false;
        }

        $current = $this->tmpDetail()
            ->where('docno', $docno)
            ->where('idurut_view', $idurutView)
            ->get()
            ->getRowArray();

        if (!$current) {
            return false;
        }

        $builder = $this->tmpDetail()->where('docno', $docno);
        if (strtolower($direction) === 'up') {
            $neighbor = $builder->where('idurut_view <', $idurutView)
                ->orderBy('idurut_view', 'DESC')->limit(1)->get()->getRowArray();
        } else {
            $neighbor = $builder->where('idurut_view >', $idurutView)
                ->orderBy('idurut_view', 'ASC')->limit(1)->get()->getRowArray();
        }

        if (!$neighbor) {
            return false;
        }

        $this->db->transStart();
        $this->tmpDetail()->where('docno', $docno)->where('idurut', $current['idurut'])
            ->update(['idurut_view' => $neighbor['idurut_view']]);
        $this->tmpDetail()->where('docno', $docno)->where('idurut', $neighbor['idurut'])
            ->update(['idurut_view' => $current['idurut_view']]);
        $this->db->transComplete();

        return $this->db->transStatus();
    }


    /* ------------------------------------------------------------------
     * STOCK & SALES ORDER
     * ------------------------------------------------------------------ */

    /**
     * Normalisasi tanggal (Y-m-d / d-m-Y / d/m/Y) menjadi Y-m-d.
     */
    public function normalDate($value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return substr($value, 0, 10);
        }
        $ts = strtotime(str_replace('/', '-', $value));
        return $ts ? date('Y-m-d', $ts) : '';
    }

    /**
     * Tanggal dokumen DO (sc_tmp didahulukan, fallback sc_trx) milik user.
     * Dipakai sebagai batas perhitungan stok akhir (stok posisi tanggal DO).
     */
    public function getDocDate(string $docno, string $nama = ''): string
    {
        $docno = strtoupper(trim($docno));
        if ($docno === '') {
            return '';
        }

        $sql    = "SELECT docdate FROM sc_tmp.deliveryorder WHERE TRIM(docno) = ?";
        $params = [$docno];
        if (trim($nama) !== '') {
            $sql     .= " AND TRIM(inputby) = ?";
            $params[] = trim($nama);
        }
        $row = $this->db->query($sql . ' LIMIT 1', $params)->getRowArray();

        if (!$row) {
            $row = $this->db->query(
                "SELECT docdate FROM sc_trx.deliveryorder WHERE TRIM(docno) = ? LIMIT 1",
                [$docno]
            )->getRowArray();
        }

        return $row ? $this->normalDate($row['docdate'] ?? '') : '';
    }

    /**
     * Stok akhir dari sc_trx.stkblc untuk gudang + barang + batch/spec.
     * Bila $docdate diisi, hanya mutasi s/d tanggal tersebut yang dihitung
     * (stok posisi tanggal dokumen DO).
     */
    public function stokAkhir(string $idbarang, string $idgudang = '', string $idspec = '', string $docdate = ''): float
    {
        $idbarang = trim($idbarang);
        $idgudang = trim($idgudang);
        $idspec   = trim($idspec);
        if (strtoupper($idspec) === 'NULL') {
            $idspec = '';
        }
        if ($idbarang === '' || $idgudang === '') {
            return 0.0;
        }

        // Sumber sama dengan validasi final penjualan: sc_trx.stkblc.
        $sql    = "SELECT (COALESCE(SUM(qty_in),0) - COALESCE(SUM(qty_out),0)) AS stok
                     FROM sc_trx.stkblc
                    WHERE TRIM(idbarang) = ?
                      AND (TRIM(idlocation) = ? OR TRIM(warehouse) = ?)
                      AND COALESCE(NULLIF(TRIM(batch), ''), '') = ?";
        $params = [$idbarang, $idgudang, $idgudang, $idspec];

        $docdate = $this->normalDate($docdate);
        if ($docdate !== '') {
            $sql     .= " AND trxdate::date <= ?::date";
            $params[] = $docdate;
        }

        $row = $this->db->query($sql, $params)->getRowArray();

        return (float) ($row['stok'] ?? 0);
    }
    /**
     * Stok akhir semua baris detail DO dalam satu query agregat.
     *
     * @param array  $rows    Baris sc_tmp.deliveryorder_dtl
     * @param string $docdate Tanggal DO (Y-m-d); kosong = stok terkini
     *
     * @return array [idurut => ['idurut','stokakhir','qty','kosong','kurang','cukup']]
     */
    public function stokForRows(array $rows, string $docdate = ''): array
    {
        $docdate = $this->normalDate($docdate);

        $result       = [];
        $keyOf        = [];
        $idbarangList = [];

        foreach ($rows as $r) {
            $idurut = (int) ($r['idurut'] ?? 0);
            if ($idurut <= 0) {
                continue;
            }

            $idbarang = strtoupper(trim((string) ($r['idbarang'] ?? '')));
            $idgudang = strtoupper(trim((string) ($r['idgudang'] ?? '')));
            $idspec   = trim((string) ($r['idspec'] ?? ''));
            if (strtoupper($idspec) === 'NULL') {
                $idspec = '';
            }
            $idspec = strtoupper($idspec);

            if ($idbarang !== '' && $idgudang !== '') {
                $keyOf[$idurut]          = $idbarang . '|' . $idspec . '|' . $idgudang;
                $idbarangList[$idbarang] = $idbarang;
            }

            $result[$idurut] = [
                'idurut'    => $idurut,
                'stokakhir' => 0.0,
                'qty'       => $this->num($r['qty'] ?? 0),
            ];
        }

        if (!empty($idbarangList)) {
            $place  = implode(',', array_fill(0, count($idbarangList), '?'));
            $params = array_values($idbarangList);

            $sql = "SELECT TRIM(idbarang)                          AS idbarang,
                           TRIM(idlocation)                        AS idlocation,
                           TRIM(COALESCE(warehouse, ''))           AS warehouse,
                           COALESCE(NULLIF(TRIM(batch), ''), '')   AS batch,
                           (COALESCE(SUM(qty_in),0) - COALESCE(SUM(qty_out),0)) AS stok
                      FROM sc_trx.stkblc
                     WHERE TRIM(idbarang) IN ({$place})";

            if ($docdate !== '') {
                $sql     .= " AND trxdate::date <= ?::date";
                $params[] = $docdate;
            }

            $sql .= " GROUP BY 1,2,3,4";

            $byLoc = [];
            $byWh  = [];
            foreach ($this->db->query($sql, $params)->getResultArray() as $s) {
                $base = strtoupper(trim((string) ($s['idbarang'] ?? '')))
                    . '|' . strtoupper(trim((string) ($s['batch'] ?? '')));
                $loc  = strtoupper(trim((string) ($s['idlocation'] ?? '')));
                $wh   = strtoupper(trim((string) ($s['warehouse'] ?? '')));
                $stok = $this->num($s['stok'] ?? 0);

                if ($loc !== '') {
                    $byLoc[$base . '|' . $loc] = ($byLoc[$base . '|' . $loc] ?? 0.0) + $stok;
                }
                if ($wh !== '') {
                    $byWh[$base . '|' . $wh] = ($byWh[$base . '|' . $wh] ?? 0.0) + $stok;
                }
            }

            foreach ($result as $idurut => $info) {
                $key = $keyOf[$idurut] ?? '';
                if ($key === '') {
                    continue;
                }
                $result[$idurut]['stokakhir'] = $byLoc[$key] ?? $byWh[$key] ?? 0.0;
            }
        }

        foreach ($result as $idurut => $info) {
            $stok = (float) $info['stokakhir'];
            $qty  = (float) $info['qty'];

            $result[$idurut]['kosong'] = ($stok <= 0);
            $result[$idurut]['kurang'] = ($stok <= 0) || ($qty > $stok);
            $result[$idurut]['cukup']  = !$result[$idurut]['kurang'];
        }

        return $result;
    }



    /**
     * Informasi header SO yang kolomnya sama dengan header DO.
     * Dipakai untuk auto-fill kurs / keterangan / field lain saat Tarik SO.
     */
    public function getSOInfo(string $docnoso): ?array
    {
        $docnoso = strtoupper(trim($docnoso));
        if ($docnoso === '') {
            return null;
        }

        $row = $this->db->query(
            "SELECT trim(docno)              AS docnoso,
                    docdate,
                    trim(cabang)             AS cabang,
                    trim(kdcustomer)         AS kdcustomer,
                    COALESCE(nmcustomer, '') AS nmcustomer,
                    COALESCE(alamatcustomer, '') AS alamatcustomer,
                    COALESCE(pemohon, '')    AS pemohon,
                    trim(kdsalesman)         AS kdsalesman,
                    COALESCE(jthtempo, 0)    AS jthtempo,
                    trim(idtax)              AS idtax,
                    isinclusive,
                    isopenprice,
                    trim(currcode)           AS currcode,
                    COALESCE(kurs, 1)        AS kurs,
                    COALESCE(keterangan, '') AS keterangan
               FROM sc_trx.salesorder
              WHERE trim(docno) = ?",
            [$docnoso]
        )->getRowArray();

        return $row ?: null;
    }

    /**
     * Kurs terakhir yang berlaku pada / sebelum docdate.
     * Jika tidak ada kurs pada rentang tersebut, dipakai kurs terakhir
     * yang tercatat untuk mata uang tersebut.
     */
    public function kursByDocdate(string $currcode, string $docdate = ''): float
    {
        $currcode = strtoupper(trim($currcode));
        if ($currcode === '') {
            return 0.0;
        }

        $docdate = trim($docdate);
        if ($docdate !== '') {
            $ts = strtotime(str_replace('/', '-', $docdate));
            $docdate = $ts ? date('Y-m-d', $ts) : '';
        }

        $sql = "SELECT e.nilai
                  FROM sc_mst.exchangerate e
                  JOIN sc_mst.currency c ON c.id = e.idcurr
                 WHERE upper(trim(c.currcode)) = ?";

        $params = [$currcode];

        if ($docdate !== '') {
            $sql .= " AND e.exchangedate::date <= ?::date";
            $params[] = $docdate;
        }

        $sql .= " ORDER BY e.exchangedate DESC, e.id DESC LIMIT 1";

        $row = $this->db->query($sql, $params)->getRowArray();

        if (!$row && $docdate !== '') {
            // Tidak ada kurs pada / sebelum docdate → pakai kurs terakhir.
            $row = $this->db->query(
                "SELECT e.nilai
                   FROM sc_mst.exchangerate e
                   JOIN sc_mst.currency c ON c.id = e.idcurr
                  WHERE upper(trim(c.currcode)) = ?
                  ORDER BY e.exchangedate DESC, e.id DESC LIMIT 1",
                [$currcode]
            )->getRowArray();
        }

        return (float) ($row['nilai'] ?? 0);
    }

    /**
     * Subquery agregat qty SO yang sudah dipakai DO di sc_trx.
     *
     * Dipakai menghitung outstanding SO: draft (status 'D') ikut dihitung supaya
     * baris SO tidak bisa ditarik dua kali ke DO lain. DO batal ('C') dan DO
     * yang sedang direvisi ('E') diabaikan.
     *
     * @return string Subquery dengan kolom uid (uniqueid baris SO) & qty_used
     */
    private function sqlSoUsedQty(): string
    {
        return "(SELECT TRIM(d.uniqueidso) AS uid,
                        SUM(COALESCE(d.qty, 0)) AS qty_used
                   FROM sc_trx.deliveryorder_dtl d
                   JOIN sc_trx.deliveryorder h ON h.docno = d.docno
                  WHERE COALESCE(TRIM(d.uniqueidso), '') <> ''
                    AND h.status NOT IN ('C', 'E')
                  GROUP BY TRIM(d.uniqueidso))";
    }

    /**
     * Daftar SO outstanding untuk modal Tarik SO.
     *
     * Outstanding = qty SO - qtydo - qty yang sudah masuk DO di sc_trx
     * (termasuk DO draft), jadi:
     *   GREATEST(qty - COALESCE(qtydo,0) - COALESCE(qty_used,0), 0) > 0
     * Hanya SO yang masih punya minimal satu baris outstanding yang ditampilkan,
     * lengkap dengan total sisa qty & sisa nilai (qty * harga * (1 - multidisc/100)).
     */
    public function listSO(string $kdcustomer = '', string $search = '', int $limit = 50): array
    {
        $kdcustomer = trim($kdcustomer);
        $search     = trim($search);
        $limit      = $limit > 0 ? $limit : 50;
        $used       = $this->sqlSoUsedQty();

        $sql = "SELECT a.docno, a.docdate, a.kdcustomer, a.nmcustomer,
                       a.total, a.status, a.currcode, a.kurs,
                       COALESCE(o.sisa_qty, 0)   AS sisa_qty,
                       COALESCE(o.sisa_nilai, 0) AS sisa_nilai,
                       COALESCE(o.jml_baris, 0)  AS jml_baris
                  FROM sc_trx.salesorder a
                  JOIN (SELECT z.docno,
                               SUM(z.sisa) AS sisa_qty,
                               SUM(z.sisa
                                   * z.harga
                                   * (1 - COALESCE(z.multidisc, 0) / 100)) AS sisa_nilai,
                               COUNT(*) AS jml_baris
                          FROM (SELECT trim(sd.docno) AS docno,
                                       GREATEST(COALESCE(sd.qty, 0) - COALESCE(sd.qtydo, 0)
                                                - COALESCE(u.qty_used, 0), 0) AS sisa,
                                       COALESCE(sd.harga, 0)     AS harga,
                                       COALESCE(sd.multidisc, 0) AS multidisc
                                  FROM sc_trx.salesorder_dtl sd
                                  LEFT JOIN {$used} u ON u.uid = trim(sd.uniqueid)) z
                         WHERE z.sisa > 0
                         GROUP BY z.docno) o
                    ON o.docno = trim(a.docno)
                 WHERE upper(trim(a.status)) NOT IN ('C')
                   AND (? = '' OR trim(a.kdcustomer) = ?)
                   AND (? = '' OR a.docno ILIKE ? OR a.nmcustomer ILIKE ?)
                 ORDER BY a.docdate DESC, a.docno DESC
                 LIMIT {$limit}";

        $rows = $this->db->query($sql, [
            $kdcustomer, $kdcustomer,
            $search, '%' . $search . '%', '%' . $search . '%',
        ])->getResultArray();

        foreach ($rows as &$r) {
            $r['sisa_qty']   = $this->num($r['sisa_qty'] ?? 0);
            $r['sisa_nilai'] = round((float) ($r['sisa_nilai'] ?? 0), 2);
            $r['jml_baris']  = (int) ($r['jml_baris'] ?? 0);
        }
        unset($r);

        return $rows;
    }


    public function pullSO(string $docno, string $docnoso, string $nama, array $uniqueids = []): array
    {
        $docno   = strtoupper(trim($docno));
        $docnoso = strtoupper(trim($docnoso));

        if ($docno === '' || $docnoso === '') {
            return ['success' => false, 'message' => 'Nomor DO / SO tidak valid'];
        }

        $so = $this->db->table('sc_trx.salesorder')->where('docno', $docnoso)->get()->getRowArray();
        if (!$so) {
            return ['success' => false, 'message' => 'SO ' . $docnoso . ' tidak ditemukan'];
        }

        // Bersihkan daftar uniqueid detail SO yang dipilih (boleh kosong = tarik semua).
        $picks = [];
        foreach ($uniqueids as $v) {
            $v = trim((string) $v);
            if ($v !== '') {
                $picks[$v] = $v;
            }
        }
        $picks = array_values($picks);

        // Sisa qty per baris SO = qty - qtydo - qty yang sudah masuk DO di sc_trx
        // (termasuk DO draft), supaya baris yang sudah ditarik tidak tertarik 2x.
        $used = $this->sqlSoUsedQty();

        $sql    = "SELECT sd.*, COALESCE(u.qty_used, 0) AS qty_used
                     FROM sc_trx.salesorder_dtl sd
                     LEFT JOIN {$used} u ON u.uid = trim(sd.uniqueid)
                    WHERE trim(sd.docno) = ?
                      AND GREATEST(COALESCE(sd.qty, 0) - COALESCE(sd.qtydo, 0)
                                   - COALESCE(u.qty_used, 0), 0) > 0";
        $params = [$docnoso];

        if (!empty($picks)) {
            $ph     = implode(',', array_fill(0, count($picks), '?'));
            $sql   .= " AND trim(sd.uniqueid) IN ({$ph})";
            $params = array_merge($params, $picks);
        }

        $sql .= " ORDER BY sd.idurut ASC";

        $details = $this->db->query($sql, $params)->getResultArray();

        if (empty($details)) {
            return [
                'success' => false,
                'message' => !empty($picks)
                    ? 'Detail SO yang dipilih tidak tersedia / sudah terpenuhi (full DO)'
                    : 'Semua item SO sudah terpenuhi (full DO)',
            ];
        }

        // Satu DO hanya untuk satu SO.
        $lain = $this->db->query(
            "SELECT trim(docnoso) AS docnoso
               FROM sc_tmp.deliveryorder_dtl
              WHERE trim(docno) = ?
                AND trim(docnoso) <> ''
                AND trim(docnoso) <> ?
              LIMIT 1",
            [$docno, $docnoso]
        )->getRowArray();

        if ($lain) {
            return [
                'success' => false,
                'message' => 'Satu DO hanya boleh berisi satu SO. DO ini sudah berisi SO '
                    . trim((string) $lain['docnoso']) . '.',
            ];
        }

        // Kurs header: rate terakhir pada/sebelum docdate SO (fallback kurs terakhir).
        $kursRate = $this->kursByDocdate((string) ($so['currcode'] ?? ''), (string) ($so['docdate'] ?? ''));
        if ($kursRate <= 0) {
            $kursRate = $this->num($so['kurs'] ?? 1);
        }

        $this->db->transStart();

        if (!$this->tmpHeader()->where('docno', $docno)->countAllResults()) {
            $this->tmpHeader()->insert([
                'docno'     => $docno,
                'docdate'   => date('Y-m-d'),
                'cabang'    => trim((string) ($so['cabang'] ?? '')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->tmpHeader()->where('docno', $docno)->update([
            'docnoso'             => $docnoso,
            'idrev'               => $docnoso,
            'kdcustomer'          => strtoupper(trim((string) ($so['kdcustomer'] ?? ''))),
            'nmcustomer'          => strtoupper(trim((string) ($so['nmcustomer'] ?? ''))),
            'alamatcustomer'      => strtoupper(trim((string) ($so['alamatcustomer'] ?? ''))),
            'gradecustomer'       => strtoupper(trim((string) ($so['gradecustomer'] ?? ''))),
            'kdcustomerdeliv'     => strtoupper(trim((string) ($so['kdcustomer'] ?? ''))),
            'nmcustomerdeliv'     => strtoupper(trim((string) ($so['nmcustomer'] ?? ''))),
            'alamatcustomerdeliv' => strtoupper(trim((string) ($so['alamatcustomer'] ?? ''))),
            'kdsalesman'          => strtoupper(trim((string) ($so['kdsalesman'] ?? ''))),
            'jthtempo'            => $this->num($so['jthtempo'] ?? 0),
            'idtax'               => strtoupper(trim((string) ($so['idtax'] ?? ''))),
            'isinclusive'         => $this->flag($so['isinclusive'] ?? 'false'),
            'isopenprice'         => $this->flag($so['isopenprice'] ?? 'false'),
            'currcode'            => strtoupper(trim((string) ($so['currcode'] ?? ''))),
            'kurs'                => $kursRate,
            // Keterangan & pemohon ikut SO (kolom yang sama pada header DO).
            'pemohon'             => substr(strtoupper(trim((string) ($so['pemohon'] ?? ''))), 0, 100),
            'keterangan'          => strtoupper(trim((string) ($so['keterangan'] ?? ''))),
            'updateby'            => $nama,
            'updatedate'          => date('Y-m-d H:i:s'),
        ]);

        $inserted = 0;
        $maxview  = $this->maxView($docno);
        // Stok akhir mengikuti tanggal DO (header sc_tmp milik user).
        $docdateStok = $this->getDocDate($docno);


        foreach ($details as $d) {
            $uniqueidso = trim((string) ($d['uniqueid'] ?? ''));
            $ada = $this->tmpDetail()
                ->where('docno', $docno)
                ->where('uniqueidso', $uniqueidso)
                ->countAllResults();
            if ($ada > 0) {
                continue;
            }

            // Sisa yang boleh ditarik: qty SO - qtydo - qty DO yang sudah tersimpan.
            $qtyso = $this->num($d['qty'] ?? 0)
                   - $this->num($d['qtydo'] ?? 0)
                   - $this->num($d['qty_used'] ?? 0);
            if ($qtyso <= 0) {
                continue;
            }

            $harga  = $this->num($d['harga'] ?? 0);
            $disc   = $this->num($d['multidisc'] ?? 0);
            $idbrg  = strtoupper(trim((string) ($d['idbarang'] ?? '')));
            $idbrg  = substr($idbrg, 0, 20);
            $gudang = strtoupper(trim((string) ($d['idgudang'] ?? '')));

            $idtaxRow  = trim((string) ($d['idtax'] ?? ''));
            $currRow   = trim((string) ($d['currcode'] ?? ''));
            $kursRow   = $this->num($d['kurs'] ?? 0);
            $nilaiRow  = $qtyso * $harga * (1 - ($disc / 100));

            if ($idtaxRow === '') { $idtaxRow = trim((string) ($so['idtax'] ?? '')); }
            if ($currRow === '')  { $currRow  = trim((string) ($so['currcode'] ?? '')); }
            if ($kursRow <= 0)    { $kursRow  = $kursRate; }

            $maxview++;

            $this->tmpDetail()->insert([
                'idurut_view'   => $maxview,
                'docno'         => $docno,
                'docnoso'       => $docnoso,
                'idrev'         => $docnoso,
                'idurutso'      => (int) ($d['idurut'] ?? 0),
                'uniqueid'      => md5(uniqid('', true) . random_bytes(8)),
                'uniqueidso'    => $uniqueidso,
                'idbarang'      => $idbrg,
                'nmbarang'      => strtoupper(trim((string) ($d['nmbarang'] ?? ''))),
                'idbarangso'    => $idbrg,
                'nmbarangso'    => strtoupper(trim((string) ($d['nmbarang'] ?? ''))),
                'idprincipal'   => substr(trim((string) ($d['idprincipal'] ?? '')), 0, 20),
                'idgudang'      => substr($gudang, 0, 30),
                'idspec'        => substr(trim((string) ($d['idspec'] ?? '')), 0, 30),
                'unit'          => strtoupper(trim((string) ($d['unit'] ?? ''))),
                'qty'           => $qtyso,
                'qtyso'         => $qtyso,
                'harga'         => $harga,
                'hargasatuan'   => $harga,
                'multidisc'     => $disc,
                'nilai'         => $nilaiRow,
                'idtax'         => $idtaxRow,
                'currcode'      => $currRow,
                'kurs'          => $kursRow,
                'nilaikonversi' => $nilaiRow * ($kursRow > 0 ? $kursRow : 1),
                'nilaipajak'    => $this->num($d['nilaipajak'] ?? 0),
                'qtysj'         => 0,
                'stokakhir'     => $this->stokAkhir($idbrg, $gudang, (string) ($d['idspec'] ?? ''), $docdateStok),
                'bomdesc'       => (string) ($d['bomdesc'] ?? ''),
                'description'   => strtoupper(trim((string) ($d['description'] ?? ''))),
                'status'        => 'E',
                'inputby'       => $nama,
                'inputdate'     => date('Y-m-d H:i:s'),
            ]);
            $inserted++;
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menarik SO, transaksi dibatalkan'];
        }

        $this->recalcTotals($docno);

        return [
            'success'  => true,
            'inserted' => $inserted,
            'header'   => $this->getHeader($docno),
            'message'  => $inserted > 0
                ? $inserted . ' detail SO berhasil ditarik'
                : 'Detail yang dipilih sudah ada di DO ini',
        ];
    }

    /**
     * Detail SO (parent-child pada modal Tarik SO) + flag sudah ditarik ke DO.
     */
    public function listSODetail(string $docnoso, string $docno = ''): array
    {
        $docnoso = strtoupper(trim($docnoso));
        $docno   = strtoupper(trim($docno));

        if ($docnoso === '') {
            return [];
        }

        // Qty per baris SO yang sudah dipakai DO di sc_trx (termasuk draft).
        $used = $this->sqlSoUsedQty();

        $rows = $this->db->query(
            "SELECT sd.idurut,
                    trim(sd.uniqueid) AS uniqueid,
                    sd.idbarang,
                    sd.nmbarang,
                    COALESCE(sd.idspec, '')       AS idspec,
                    COALESCE(sd.unit, '')         AS unit,
                    COALESCE(sd.qty, 0)           AS qty,
                    COALESCE(sd.qtydo, 0)         AS qtydo,
                    COALESCE(u.qty_used, 0)       AS qtyused,
                    GREATEST(COALESCE(sd.qty, 0) - COALESCE(sd.qtydo, 0)
                             - COALESCE(u.qty_used, 0), 0) AS qtysisa,
                    COALESCE(sd.harga, 0)     AS harga,
                    COALESCE(sd.multidisc, 0) AS multidisc
               FROM sc_trx.salesorder_dtl sd
               LEFT JOIN {$used} u ON u.uid = trim(sd.uniqueid)
              WHERE trim(sd.docno) = ?
              ORDER BY sd.idurut ASC",
            [$docnoso]
        )->getResultArray();

        if (empty($rows)) {
            return [];
        }

        $pulled = [];
        if ($docno !== '') {
            $tmp = $this->db->query(
                "SELECT trim(uniqueidso) AS uid
                   FROM sc_tmp.deliveryorder_dtl
                  WHERE trim(docno) = ?",
                [$docno]
            )->getResultArray();

            foreach ($tmp as $t) {
                $uid = trim((string) ($t['uid'] ?? ''));
                if ($uid !== '') {
                    $pulled[$uid] = true;
                }
            }
        }

        foreach ($rows as &$r) {
            $r['uniqueid'] = trim((string) ($r['uniqueid'] ?? ''));
            $r['qty']      = $this->num($r['qty'] ?? 0);
            $r['qtydo']    = $this->num($r['qtydo'] ?? 0);
            $r['qtyused']  = $this->num($r['qtyused'] ?? 0);
            $r['qtysisa']  = $this->num($r['qtysisa'] ?? 0);
            $r['harga']    = $this->num($r['harga'] ?? 0);
            $r['sudah']    = isset($pulled[$r['uniqueid']]) ? 1 : 0;
        }
        unset($r);

        return $rows;
    }
}

