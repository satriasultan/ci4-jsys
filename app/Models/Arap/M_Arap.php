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


}