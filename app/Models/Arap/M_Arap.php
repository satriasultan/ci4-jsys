<?php

namespace App\Models\Arap;

use CodeIgniter\Model;

class M_Arap extends Model
{
    //PP

    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_ndk_view = "sc_trx.ndk";
    var $t_ndk_view_column = array('docno','docref','description');
    var $t_ndk_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_ndk()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_ndk_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_ndk_view_column as $mrp)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_ndk_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_ndk_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_ndk_view_order))
        {
            $order = $this->t_ndk_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_ndk_view(){
        $builder = $this->_get_query_t_ndk();
        ////$this->_get_query_t_ndk();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_ndk_view_count_filtered()
    {
        $builder = $this->_get_query_t_ndk();
        ////$this->_get_query_t_ndk();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_ndk_view_count_all()
    {
        $builder = $this->_get_query_t_ndk();
        return $builder->countAllResults();
    }
    public function get_t_ndk_view_by_id($id)
    {
        $builder = $this->_get_query_t_ndk();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_ndk_dtl_view = "sc_trx.ndk_dtl";
    var $t_ndk_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $t_ndk_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_ndk_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_ndk_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_ndk_dtl_view_column as $mrp)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_ndk_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_ndk_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_ndk_dtl_view_order))
        {
            $order = $this->t_ndk_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_ndk_dtl_view($docnoParam){
        $builder = $this->_get_query_t_ndk_dtl($docnoParam);
        ////$this->_get_query_t_ndk_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_ndk_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_ndk_dtl($docnoParam);
        ////$this->_get_query_t_ndk_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_ndk_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_ndk_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_ndk_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_ndk_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function get_suppliers()
    {
        $builder = $this->db->table('sc_mst.msupplier'); // Tentukan tabel
        $builder->select('idsupplier, nmsupplier'); // Tentukan kolom yang diambil
        $query = $builder->get(); // Ambil data
        return $query->getResult(); // Kembalikan hasilnya
    }

    public function q_ndk_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.ndk where docno is not null $param");
    }

    public function q_ndk_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.ndk_dtl where docno is not null $param order by idurut desc");
    }


    public function q_ndk_master($param)
    {
        return $this->db->query("select * from sc_trx.ndk where docno is not null $param");
    }

    public function q_ndk_dtl($param)
    {
        return $this->db->query("select * from sc_trx.ndk_dtl where docno is not null $param order by idurut desc");
    }


    
    public function q_konfigurasi_umum($param)
    {
        return $this->db->query("select * from sc_mst.konfigurasi_umum where id is not null $param");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_ndk_dtl_temp_view = "sc_tmp.ndk_dtl";
    var $t_ndk_dtl_temp_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $t_ndk_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_ndk_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_ndk_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_ndk_dtl_temp_view_column as $mrp)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_ndk_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_ndk_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_ndk_dtl_temp_view_order))
        {
            $order = $this->t_ndk_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_ndk_dtl_temp_view($docno){
        $builder = $this->_get_query_t_ndk_dtl_temp($docno);
        ////$this->_get_query_t_ndk_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_ndk_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_ndk_dtl_temp($docno);
        ////$this->_get_query_t_ndk_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_ndk_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_ndk_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_ndk_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_ndk_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_ndk_view = "sc_trx.ndk";
    var $t_front_ndk_view = "(select a.*,
        c.alamat as alamatsplr,
        c.nama as nmsplr,
        d.namakotakab AS nmkota,
        b.nmbranch as nmbranch,
        m.nmsalesman as nmsalesman,
        c.tipe
    from sc_trx.ndk a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join (
        select
            trim(kdsupplier) as kode,
            trim(nmsupplier) as nama,
            trim(alamat) as alamat,
            trim(idkota) as idkota,
            'SUPPLIER' as tipe
        from sc_mst.mstsupplier

        union all

        select
            trim(kdcustomer) as kode,
            trim(nmcustomer) as nama,
            trim(alamat_kantor) as alamat,
            trim(kota_kantor) as idkota,
            'CUSTOMER' as tipe
        from sc_mst.customer

    ) c
        on trim(a.kdsupplier) = trim(c.kode)
    left outer join sc_mst.salesman m on a.kdsalesman=m.kdsalesman
    left outer join sc_mst.kotakab d on c.idkota=d.kodekotakab) as x";
    var $t_front_ndk_view_column = array('docno','docdate','currcode','keterangan');
    var $t_front_ndk_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_ndk()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_ndk_view);
        // $builder->join(
        //     "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
        //     FROM sc_mst.trxtype 
        //     WHERE jenistrx = 'I.P.A.2' 
        //     ORDER BY kdtrx, uraian DESC) AS trx", 
        //     "COALESCE(x.status, '') = COALESCE(trx.kdtrx, '')", 
        //     "left"
        // );
        $builder->select("x.*");
        
        $tglrange = $this->request->getPost('tglrange');
        if (!empty($tglrange)) {
            $dates = explode(' - ', $tglrange);
            if (count($dates) == 2) {
                $start = \DateTime::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end   = \DateTime::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                $builder->where("docdate BETWEEN '{$start}' AND '{$end}'");
            }
        }

        
        // $builder->where('inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_ndk_view_column as $mrpgroup)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($mrpgroup) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($mrpgroup) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_front_ndk_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_ndk_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_ndk_view_order))
        {
            $order = $this->t_front_ndk_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_ndk_view(){
        $builder = $this->_get_query_front_ndk();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_ndk_view_count_filtered()
    {
        $builder = $this->_get_query_front_ndk();
        ////$this->_get_query_t_ndk();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_ndk_view_count_all()
    {
        $builder = $this->_get_query_front_ndk();
        return $builder->countAllResults();
    }
    public function get_t_front_ndk_view_by_id($id)
    {
        $builder = $this->_get_query_front_ndk();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_laporan_jurnal_transaksi_ndk($params = '')
    {
        return $this->db->query("
                        WITH data_jurnal AS (
                            SELECT
                                jd.id,
                                jd.jurnal_id,
                                TRIM(jd.idcoa) AS idcoa,
                                TRIM(coa.nmcoa) AS nmcoa,
                                jd.debet,
                                jd.kredit,
                                TRIM(jd.ref_docno) AS ref_docno,
                                TRIM(jd.ref_doctype) AS ref_doctype,
                                TRIM(jh.docno) AS docno,
                                TRIM(jh.doctype) AS doctype,
                                jh.trxdate,
                                TRIM(ndk.kdsupplier) AS kdsupplier,
                                TRIM(ndk.nmsupplier) AS nmsupplier,
                                TRIM(jd.status) AS status
                            FROM sc_trx.jurnal_dt jd
                        
                            INNER JOIN sc_trx.jurnal_hd jh
                                ON jh.id = jd.jurnal_id
                        
                            LEFT JOIN sc_mst.coa coa
                                ON TRIM(coa.idcoa) = TRIM(jd.idcoa)
                        
                            LEFT JOIN sc_trx.ndk ndk
                                ON TRIM(ndk.docno) = TRIM(jd.ref_docno)
                                AND TRIM(jd.ref_doctype) = 'NDK'
                        
                            WHERE 1=1
                                AND TRIM(jd.status) = 'POSTED'
                        
                                $params
                        )
                        
                        SELECT
                            id,
                            jurnal_id,
                            idcoa,
                            nmcoa,
                            debet,
                            kredit,
                            ref_docno,
                            ref_doctype,
                            docno,
                            doctype,
                            trxdate,
                            kdsupplier,
                            nmsupplier,
                            status,
                            0 AS urutan
                        FROM data_jurnal
                        
                        UNION ALL
                        
                        SELECT
                            NULL::BIGINT AS id,
                            NULL::BIGINT AS jurnal_id,
                            NULL::VARCHAR AS idcoa,
                            'TOTAL'::VARCHAR AS nmcoa,
                            COALESCE(SUM(debet), 0) AS debet,
                            COALESCE(SUM(kredit), 0) AS kredit,
                            NULL::VARCHAR AS ref_docno,
                            NULL::VARCHAR AS ref_doctype,
                            NULL::VARCHAR AS docno,
                            NULL::VARCHAR AS doctype,
                            NULL::DATE AS trxdate,
                            NULL::VARCHAR AS kdsupplier,
                            NULL::VARCHAR AS nmsupplier,
                            'POSTED'::VARCHAR AS status,
                            1 AS urutan
                        FROM data_jurnal
                        
                        ORDER BY
                            urutan,
                            trxdate DESC NULLS LAST,
                            jurnal_id,
                            id
                        ");
    }



    /**
     * Query dasar daftar Tanda Terima Supplier
     */
    private function builderTterima(array $filter = [])
    {
        $builder = $this->db->table('sc_trx.tterima_hd h');

        $builder->select("
        h.idurut,
        h.docno,
        h.docdate,
        h.status,
        h.kdsupplier,
        h.nmsupplier,
        h.kotasupplier,
        h.noinvoice,
        h.tglinvoice,
        h.nosj,
        h.tglsj,
        h.noaju,
        h.nobl,
        h.noawb,
        h.noinvoicebea,
        h.tglinvoicebea,
        h.nobkrev,
        h.nofakturpajak,
        h.senddate,
        h.currcode,
        h.idtax,
        h.kurs,
        h.jthtempo,
        h.tgljthtempo,
        h.isinclusive,
        h.dpp,
        h.jumlahpajak,
        h.total,
        h.beaimport,
        h.ppnimport,
        h.pphimport,
        h.biayaangkut,
        h.biayaasuransi,
        h.biayalain,
        h.totalestimasi,
        h.cekinvoice,
        h.ceksj,
        h.cekpenerimaan,
        h.cekfakturpajak,
        h.cekbeaimport,
        h.cekdokumen,
        h.keterangan,
        h.balance,
        h.coabank,
        h.nmcoabank,
        h.inputby,
        h.inputdate,
        h.updateby,
        h.updatedate
    ", false);

        // =========================
        // FILTER STATUS
        // =========================
        if (
            !empty($filter['status']) &&
            strtoupper(trim($filter['status'])) !== 'ALL'
        ) {
            $builder->where(
                'TRIM(h.status)',
                trim($filter['status'])
            );
        }

        // =========================
        // FILTER SUPPLIER
        // =========================
        if (!empty($filter['kdsupplier'])) {
            $builder->like(
                'h.kdsupplier',
                trim($filter['kdsupplier'])
            );
        }

        // =========================
        // FILTER INVOICE
        // =========================
        if (!empty($filter['noinvoice'])) {
            $builder->like(
                'h.noinvoice',
                trim($filter['noinvoice'])
            );
        }

        // =========================
        // GLOBAL SEARCH DATATABLE
        // =========================
        if (!empty($filter['search'])) {

            $search = trim($filter['search']);

            $builder->groupStart()
                ->like('h.docno', $search)
                ->orLike('h.kdsupplier', $search)
                ->orLike('h.nmsupplier', $search)
                ->orLike('h.noinvoice', $search)
                ->orLike('h.nosj', $search)
                ->orLike('h.noaju', $search)
                ->orLike('h.nobl', $search)
                ->orLike('h.noawb', $search)
                ->orLike('h.nobkrev', $search)
                ->orLike('h.nofakturpajak', $search)
                ->orLike('h.keterangan', $search)
                ->groupEnd();
        }

        // =========================
        // FILTER TANGGAL
        // =========================
        if (
            !empty($filter['tglawal']) &&
            !empty($filter['tglakhir'])
        ) {

            $builder->where(
                "TO_DATE(TRIM(h.docdate), 'DD-MM-YYYY') >= TO_DATE(" .
                $this->db->escape($filter['tglawal']) .
                ", 'DD-MM-YYYY')",
                null,
                false
            );

            $builder->where(
                "TO_DATE(TRIM(h.docdate), 'DD-MM-YYYY') <= TO_DATE(" .
                $this->db->escape($filter['tglakhir']) .
                ", 'DD-MM-YYYY')",
                null,
                false
            );
        }

        return $builder;
    }

    /**
     * Total seluruh data
     */
    public function countTterimaAll(): int
    {
        return $this->db->table('sc_trx.tterima_hd')
            ->countAllResults();
    }

    /**
     * Total data setelah filter
     */
    public function countTterimaFiltered(array $filter = []): int
    {
        return $this->builderTterima($filter)->countAllResults();
    }

    /**
     * Data transaksi untuk DataTables
     */
    public function getTterimaList(
        array $filter = [],
        int $start = 0,
        int $length = 10,
        string $orderColumn = 'docdate',
        string $orderDir = 'DESC'
    ): array {
        $allowedColumns = [
            'docno'       => 'h.docno',
            'docdate'     => 'h.docdate',
            'status'      => 'h.status',
            'kdsupplier'  => 'h.kdsupplier',
            'nmsupplier'  => 'h.nmsupplier',
            'noinvoice'   => 'h.noinvoice',
            'tglinvoice'  => 'h.tglinvoice',
            'nosj'        => 'h.nosj',
            'tglsj'       => 'h.tglsj',
            'currcode'    => 'h.currcode',
            'kurs'        => 'h.kurs',
            'dpp'         => 'h.dpp',
            'jumlahpajak' => 'h.jumlahpajak',
            'total'       => 'h.total',
            'balance'     => 'h.balance',
            'keterangan'  => 'h.keterangan',
        ];

        $orderBy = $allowedColumns[$orderColumn] ?? 'h.docdate';
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        return $this->builderTterima($filter)
            ->orderBy($orderBy, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan header dan detail Tanda Terima ke tabel temporary.
     *
     * Header: sc_tmp.tterima_hd
     * Detail: sc_tmp.tterima_dt
     */
    public function saveTterimaDraft(array $header, array $details): array
    {
        $db = $this->db;
        $db->transBegin();

        try {
            $docno = trim($header['docno'] ?? '');

            if ($docno === '') {
                throw new \RuntimeException(
                    'Nomor dokumen belum tersedia.'
                );
            }

            $existing = $db->table('sc_tmp.tterima_hd')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $headerData = [
                'docno'         => $docno,
                'docdate'       => $header['docdate'] ?? null,
                'cabang'        => $header['cabang'] ?? '',
                'kdsupplier'    => $header['kdsupplier'] ?? '',
                'nmsupplier'    => $header['nmsupplier'] ?? '',
                'alamatsupplier'=> $header['alamatsupplier'] ?? '',
                'kotasupplier'  => $header['kotasupplier'] ?? '',
                'noinvoice'     => $header['noinvoice'] ?? '',
                'tglinvoice'    => $header['tglinvoice'] ?? null,
                'nosj'          => $header['nosj'] ?? '',
                'tglsj'         => $header['tglsj'] ?? null,
                'currcode'      => $header['currcode'] ?? '',
                'kurs'          => $this->toNumber($header['kurs'] ?? 1),
                'dpp'           => $this->toNumber($header['dpp'] ?? 0),
                'jumlahpajak'   => $this->toNumber($header['jumlahpajak'] ?? 0),
                'total'         => $this->toNumber($header['total'] ?? 0),
                'balance'       => $this->toNumber($header['balance'] ?? 0),
                'keterangan'    => $header['keterangan'] ?? '',
            ];

            if ($existing) {
                if (trim($existing['status']) !== 'I') {
                    throw new \RuntimeException(
                        'Dokumen bukan berstatus draft dan tidak dapat diubah.'
                    );
                }

                $db->table('sc_tmp.tterima_hd')
                    ->where('docno', $docno)
                    ->update($headerData);
            } else {
                $headerData['status'] = 'I';
                $db->table('sc_tmp.tterima_hd')->insert($headerData);
            }

            // Replace detail temporary untuk dokumen ini
            $db->table('sc_tmp.tterima_dt')
                ->where('docno', $docno)
                ->delete();

            foreach ($details as $item) {
                $db->table('sc_tmp.tterima_dt')->insert([
                    'docno'            => $docno,
                    'nobukti'          => $item['nobukti'] ?? '',
                    'docref'           => $item['docref'] ?? '',
                    'noperkiraan'      => $item['noperkiraan'] ?? '',
                    'namaperkiraan'    => $item['namaperkiraan'] ?? '',
                    'keterangan'       => $item['keterangan'] ?? '',
                    'dk'               => $item['dk'] ?? '',
                    'costprofitcenter' => $item['costprofitcenter'] ?? '',
                    'nilai'            => $this->toNumber($item['nilai'] ?? 0),
                    'status'           => 'I',
                ]);
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan transaksi database.');
            }

            $db->transCommit();

            return [
                'status' => true,
                'docno'  => $docno,
                'message'=> 'Draft berhasil disimpan.',
            ];
        } catch (\Throwable $e) {
            $db->transRollback();

            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Konversi angka format Indonesia.
     */
    private function toNumber($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0;
    }

    public function getTterimaTempDetail(string $docno): array
    {
        return $this->db->table('sc_tmp.tterima_dt')
            ->where('docno', trim($docno))
            ->orderBy('idurut', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function deleteTterimaTempDetail(string $docno, int $idurut): bool
    {
        return $this->db->table('sc_tmp.tterima_dt')
            ->where('docno', trim($docno))
            ->where('idurut', $idurut)
            ->delete();
    }

    public function q_tterima_master_temp($param)
    {
        $builder = $this->db->table('sc_tmp.tterima_hd');

        $builder->select('*');

        if (!empty($param['docno'])) {
            $builder->where('docno', $param['docno']);
        }

        if (!empty($param['docnotmp'])) {
            $builder->where('docnotmp', $param['docnotmp']);
        }

        return $builder->get();
    }

    public function saveTterimaHeader(
        array $data,
              $idurut = null,
              $docno = null
    )
    {
        $db = $this->db;

        $builder = $db->table('sc_tmp.tterima_hd');


        // =====================================================
        // UPDATE
        // =====================================================
        if (!empty($idurut)) {

            $builder
                ->where(
                    'idurut',
                    (int) $idurut
                )
                ->where(
                    'docno',
                    $docno
                )
                ->update($data);

            return [
                'idurut' => $idurut,
                'docno'  => $docno,
                'mode'   => 'UPDATE'
            ];
        }


        // =====================================================
        // INSERT
        // =====================================================
        $builder->insert($data);

        $newId =
            $db->insertID();

        return [
            'idurut' => $newId,
            'docno'  => $data['docno'],
            'mode'   => 'INSERT'
        ];
    }


    // =========================================================
    // GET HEADER TANDA TERIMA
    // =========================================================
    public function getTterimaHeader(
        $idurut,
        $docno
    )
    {
        return $this->db
            ->table('sc_tmp.tterima_hd')
            ->where('idurut', $idurut)
            ->where('docno', $docno)
            ->get()
            ->getRowArray();
    }

    /* TRX */

    /* UNTUK LIST DEPAN TANDA TERIMA */
    /* TRX TANDA TERIMA */

    var $t_tterima_hd = "sc_trx.tterima_hd";

    var $t_tterima_hd_column = array(
        'docno',
        'docdate',
        'status',
        'kdsupplier',
        'nmsupplier',
        'noinvoice',
        'tglinvoice',
        'nosj',
        'tglsj',
        'currcode',
        'kurs',
        'dpp',
        'jumlahpajak',
        'total',
        'balance',
        'keterangan'
    );

    var $t_tterima_hd_order = array("docdate" => 'desc'); // default order


    private function _get_query_t_tterima_hd()
    {
        $this->session = \Config\Services::session();

        $loccode = trim($this->session->get('loccode'));
        $nama    = trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_tterima_hd);

        $i = 0;

        /*
         * SEARCH DATATABLE
         */
        foreach ($this->t_tterima_hd_column as $mrp)
        {
            if ($_POST['search']['value'])
            {
                if ($i === 0)
                {
                    $builder->groupStart();

                    $builder->like(
                        "upper(cast(" . strtoupper($mrp) . " as varchar))",
                        strtoupper($_POST['search']['value'])
                    );
                }
                else
                {
                    $builder->orLike(
                        "upper(cast(" . strtoupper($mrp) . " as varchar))",
                        strtoupper($_POST['search']['value'])
                    );
                }

                if (count($this->t_tterima_hd_column) - 1 == $i)
                {
                    $builder->groupEnd();
                }
            }

            $i++;
        }


        /*
         * ORDER DATATABLE
         */
        if (isset($_POST['order']))
        {
            /*
             * Column 0 = No
             * Column 1 = Action
             *
             * Data tabel dimulai dari column 2.
             */
            if ($_POST['order']['0']['column'] != 0 &&
                $_POST['order']['0']['column'] != 1)
            {
                $column = $_POST['order']['0']['column'] - 2;

                if (isset($this->t_tterima_hd_column[$column]))
                {
                    $builder->orderBy(
                        $this->t_tterima_hd_column[$column],
                        $_POST['order']['0']['dir']
                    );
                }
            }
        }
        else if (isset($this->t_tterima_hd_order))
        {
            $order = $this->t_tterima_hd_order;

            foreach ($order as $key => $mrp)
            {
                $builder->orderBy($key, $mrp);
            }
        }

        return $builder;
    }


    function get_t_tterima_hd()
    {
        $builder = $this->_get_query_t_tterima_hd();

        if ($_POST['length'] != -1)
        {
            $builder->limit(
                $_POST['length'],
                $_POST['start']
            );
        }

        $query = $builder->get();

        return $query->getResult();
    }


    function t_tterima_hd_count_filtered()
    {
        $builder = $this->_get_query_t_tterima_hd();

        $query = $builder->get();

        return $query->getNumRows();
    }


    public function t_tterima_hd_count_all()
    {
        $builder = $this->_get_query_t_tterima_hd();

        return $builder->countAllResults();
    }


    public function get_t_tterima_hd_by_id($id)
    {
        $builder = $this->_get_query_t_tterima_hd();

        $builder->where('docno', $id);

        $query = $builder->get();

        return $query->getRow();
    }


    public function q_tterima_master($param = '')
    {
        $sql = "
        SELECT
            *
        FROM sc_trx.tterima_hd
        WHERE 1=1
        $param
    ";

        return $this->db->query($sql);
    }
}