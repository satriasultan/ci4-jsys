<?php

namespace App\Models\Finance;

use CodeIgniter\Model;

class M_Finance extends Model
{

    /* TRX MRP DETAIL */
    var $t_jup_dtl_view = "sc_trx.jup_dtl";
    var $t_jup_dtl_view_column = array('idurut','idoca','nmcoa','dk','nilai','remarks');
    var $t_jup_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_jup_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_jup_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_jup_dtl_view_column as $mrp)
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

                if(count($this->t_jup_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo jupst column 0
                $builder->orderBy($this->t_jup_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_jup_dtl_view_order))
        {
            $order = $this->t_jup_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_jup_dtl_view($docnoParam){
        $builder = $this->_get_query_t_jup_dtl($docnoParam);
        ////$this->_get_query_t_jup_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_jup_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_jup_dtl($docnoParam);
        ////$this->_get_query_t_jup_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_jup_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_jup_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_jup_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_jup_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //WO TEMP
    /* WO DETAIL */
    var $t_jup_dtl_temp_view = "sc_tmp.jup_dtl";
    var $t_jup_dtl_temp_view_column = array('idurut','idoca','nmcoa','dk','nilai','remarks');
    var $t_jup_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_jup_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_jup_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_jup_dtl_temp_view_column as $mrp)
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

                if(count($this->t_jup_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_jup_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_jup_dtl_temp_view_order))
        {
            $order = $this->t_jup_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_jup_dtl_temp_view($docno){
        $builder = $this->_get_query_t_jup_dtl_temp($docno);
        ////$this->_get_query_t_jup_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_jup_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_jup_dtl_temp($docno);
        ////$this->_get_query_t_jup_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_jup_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_jup_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_jup_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_jup_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_jup_view = "sc_trx.po";
    var $t_front_jup_view = "(select a.*,
    b.nmbranch,
    z.uraian as status_desc
    from sc_trx.jup a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.P.A.3') as x";
    var $t_front_jup_view_column = array('docno','docdate','status_desc','keterangan','nmbranch');
    var $t_front_jup_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_jup()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_jup_view);
        // $builder->join(
        //     "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
        //     FROM sc_mst.trxtype 
        //     WHERE jenistrx = 'I.P.A.2' 
        //     ORDER BY kdtrx, uraian DESC) AS trx", 
        //     "COALESCE(x.status, '') = COALESCE(trx.kdtrx, '')", 
        //     "left"
        // );
        $builder->select("x.*");
        // $builder->where('inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_jup_view_column as $mrpgroup)
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

                if(count($this->t_front_jup_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_jup_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_jup_view_order))
        {
            $order = $this->t_front_jup_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_jup_view(){
        $builder = $this->_get_query_front_jup();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_jup_view_count_filtered()
    {
        $builder = $this->_get_query_front_jup();
        ////$this->_get_query_t_po();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_jup_view_count_all()
    {
        $builder = $this->_get_query_front_jup();
        return $builder->countAllResults();
    }
    public function get_t_front_jup_view_by_id($id)
    {
        $builder = $this->_get_query_front_jup();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    public function q_jup_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.jup where docno is not null $param");
    }

    public function q_jup_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.jup_dtl where docno is not null $param order by idurut desc");
    }


    public function q_jup_master($param)
    {
        return $this->db->query("select * from sc_trx.jup where docno is not null $param");
    }

    public function q_jup_dtl($param)
    {
        return $this->db->query("select * from sc_trx.jup_dtl where docno is not null $param order by idurut desc");
    }


// ================================== UANG MUKA TITIPAN ==================================================
    
    
    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_umt_view = "sc_trx.umt";
    var $t_umt_view_column = array('docno','docref','description');
    var $t_umt_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_umt()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_umt_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_umt_view_column as $mrp)
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

                if(count($this->t_umt_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo umtst column 0
                $builder->orderBy($this->t_umt_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_umt_view_order))
        {
            $order = $this->t_umt_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_umt_view(){
        $builder = $this->_get_query_t_umt();
        ////$this->_get_query_t_umt();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_umt_view_count_filtered()
    {
        $builder = $this->_get_query_t_umt();
        ////$this->_get_query_t_umt();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_umt_view_count_all()
    {
        $builder = $this->_get_query_t_umt();
        return $builder->countAllResults();
    }
    public function get_t_umt_view_by_id($id)
    {
        $builder = $this->_get_query_t_umt();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_umt_dtl_view = "sc_trx.umt_dtl";
    var $t_umt_dtl_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_umt_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_umt_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_umt_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_umt_dtl_view_column as $mrp)
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

                if(count($this->t_umt_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo umtst column 0
                $builder->orderBy($this->t_umt_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_umt_dtl_view_order))
        {
            $order = $this->t_umt_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_umt_dtl_view($docnoParam){
        $builder = $this->_get_query_t_umt_dtl($docnoParam);
        ////$this->_get_query_t_umt_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_umt_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_umt_dtl($docnoParam);
        ////$this->_get_query_t_umt_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_umt_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_umt_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_umt_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_umt_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_umt_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.umt where docno is not null $param");
    }

    public function q_umt_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.umt_dtl where docno is not null $param order by idurut desc");
    }


    public function q_umt_master($param)
    {
        return $this->db->query("select * from sc_trx.umt where docno is not null $param");
    }

    public function q_umt_dtl($param)
    {
        return $this->db->query("select * from sc_trx.umt_dtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_umt_dtl_temp_view = "sc_tmp.umt_dtl";
    var $t_umt_dtl_temp_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_umt_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_umt_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_umt_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_umt_dtl_temp_view_column as $mrp)
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

                if(count($this->t_umt_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo umtst column 0
                $builder->orderBy($this->t_umt_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_umt_dtl_temp_view_order))
        {
            $order = $this->t_umt_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_umt_dtl_temp_view($docno){
        $builder = $this->_get_query_t_umt_dtl_temp($docno);
        ////$this->_get_query_t_umt_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_umt_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_umt_dtl_temp($docno);
        ////$this->_get_query_t_umt_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_umt_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_umt_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_umt_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_umt_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_umt_view = "sc_trx.umt";
    var $t_front_umt_view = "(select a.*, 
    c.alamat as alamatsplr,
    c.nama as nmsplr,
    b.nmbranch,
    c.tipe,
    d.namakotakab AS nmkota,
    z.uraian as status_desc
    from sc_trx.umt a 
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
    left outer join sc_mst.kotakab d on c.idkota=d.kodekotakab
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.P.A.3') as x";
    var $t_front_umt_view_column = array('docno','docdate','status_desc','kdsupplier','nmsplr','alamatsplr','nmkota','currcode','jthtempo','keterangan','nmbranch');
    var $t_front_umt_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_umt()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_umt_view);
        // $builder->join(
        //     "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
        //     FROM sc_mst.trxtype 
        //     WHERE jenistrx = 'I.P.A.2' 
        //     ORDER BY kdtrx, uraian DESC) AS trx", 
        //     "COALESCE(x.status, '') = COALESCE(trx.kdtrx, '')", 
        //     "left"
        // );
        $builder->select("x.*");
        // $builder->where('inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_umt_view_column as $mrpgroup)
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

                if(count($this->t_front_umt_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo umtst column 0
                $builder->orderBy($this->t_front_umt_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_umt_view_order))
        {
            $order = $this->t_front_umt_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_umt_view(){
        $builder = $this->_get_query_front_umt();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_umt_view_count_filtered()
    {
        $builder = $this->_get_query_front_umt();
        ////$this->_get_query_t_umt();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_umt_view_count_all()
    {
        $builder = $this->_get_query_front_umt();
        return $builder->countAllResults();
    }
    public function get_t_front_umt_view_by_id($id)
    {
        $builder = $this->_get_query_front_umt();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    // PENERIMAAN KAS BANK


    /* TRX MRP DETAIL */
    var $t_penerimaankb_dtl_view = "sc_trx.penerimaankb_dtl";
    var $t_penerimaankb_dtl_view_column = array('idurut','idoca','nmcoa','dk','nilai','remarks');
    var $t_penerimaankb_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_penerimaankb_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_penerimaankb_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_penerimaankb_dtl_view_column as $mrp)
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

                if(count($this->t_penerimaankb_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo penerimaankbst column 0
                $builder->orderBy($this->t_penerimaankb_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_penerimaankb_dtl_view_order))
        {
            $order = $this->t_penerimaankb_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_penerimaankb_dtl_view($docnoParam){
        $builder = $this->_get_query_t_penerimaankb_dtl($docnoParam);
        ////$this->_get_query_t_penerimaankb_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_penerimaankb_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_penerimaankb_dtl($docnoParam);
        ////$this->_get_query_t_penerimaankb_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_penerimaankb_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_penerimaankb_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_penerimaankb_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_penerimaankb_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //WO TEMP
    /* WO DETAIL */
    var $t_penerimaankb_dtl_temp_view = "sc_tmp.penerimaankb_dtl";
    var $t_penerimaankb_dtl_temp_view_column = array('idurut','idoca','nmcoa','dk','nilai','remarks');
    var $t_penerimaankb_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_penerimaankb_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_penerimaankb_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_penerimaankb_dtl_temp_view_column as $mrp)
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

                if(count($this->t_penerimaankb_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_penerimaankb_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_penerimaankb_dtl_temp_view_order))
        {
            $order = $this->t_penerimaankb_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_penerimaankb_dtl_temp_view($docno){
        $builder = $this->_get_query_t_penerimaankb_dtl_temp($docno);
        ////$this->_get_query_t_penerimaankb_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_penerimaankb_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_penerimaankb_dtl_temp($docno);
        ////$this->_get_query_t_penerimaankb_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_penerimaankb_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_penerimaankb_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_penerimaankb_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_penerimaankb_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_penerimaankb_view = "sc_trx.po";
    var $t_front_penerimaankb_view = "(select a.*,
    b.nmbranch,
    z.uraian as status_desc,
    c.nmcustomer as nmcustomernew,
    d.namakotakab AS nmkota,
    o.nmcoa as nmcoa
    from sc_trx.penerimaankb a 
    left outer join sc_mst.customer c on a.kdcustomer=c.kdcustomer
    left outer join sc_mst.coa o on a.prkkas=o.idcoa
    left outer join sc_mst.kotakab d on c.kota_kantor=d.kodekotakab
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.P.A.3') as x";
    var $t_front_penerimaankb_view_column = array('docno','docdate','status_desc','keterangan','nmbranch');
    var $t_front_penerimaankb_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_penerimaankb()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_penerimaankb_view);
        // $builder->join(
        //     "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
        //     FROM sc_mst.trxtype 
        //     WHERE jenistrx = 'I.P.A.2' 
        //     ORDER BY kdtrx, uraian DESC) AS trx", 
        //     "COALESCE(x.status, '') = COALESCE(trx.kdtrx, '')", 
        //     "left"
        // );
        $builder->select("x.*");
        // $builder->where('inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_penerimaankb_view_column as $mrpgroup)
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

                if(count($this->t_front_penerimaankb_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_penerimaankb_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_penerimaankb_view_order))
        {
            $order = $this->t_front_penerimaankb_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_penerimaankb_view(){
        $builder = $this->_get_query_front_penerimaankb();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_penerimaankb_view_count_filtered()
    {
        $builder = $this->_get_query_front_penerimaankb();
        ////$this->_get_query_t_po();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_penerimaankb_view_count_all()
    {
        $builder = $this->_get_query_front_penerimaankb();
        return $builder->countAllResults();
    }
    public function get_t_front_penerimaankb_view_by_id($id)
    {
        $builder = $this->_get_query_front_penerimaankb();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    public function q_penerimaankb_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.penerimaankb where docno is not null $param");
    }

    public function q_penerimaankb_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.penerimaankb_dtl where docno is not null $param order by idurut desc");
    }


    public function q_penerimaankb_master($param)
    {
        return $this->db->query("select * from sc_trx.penerimaankb where docno is not null $param");
    }

    public function q_penerimaankb_dtl($param)
    {
        return $this->db->query("select * from sc_trx.penerimaankb_dtl where docno is not null $param order by idurut desc");
    }






    // PENGELUARAN KAS BANK


    /* TRX MRP DETAIL */
    var $t_pengeluarankb_dtl_view = "sc_trx.pengeluarankb_dtl";
    var $t_pengeluarankb_dtl_view_column = array('idurut','idoca','nmcoa','dk','nilai','remarks');
    var $t_pengeluarankb_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_pengeluarankb_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_pengeluarankb_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_pengeluarankb_dtl_view_column as $mrp)
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

                if(count($this->t_pengeluarankb_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo pengeluarankbst column 0
                $builder->orderBy($this->t_pengeluarankb_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_pengeluarankb_dtl_view_order))
        {
            $order = $this->t_pengeluarankb_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_pengeluarankb_dtl_view($docnoParam){
        $builder = $this->_get_query_t_pengeluarankb_dtl($docnoParam);
        ////$this->_get_query_t_pengeluarankb_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_pengeluarankb_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_pengeluarankb_dtl($docnoParam);
        ////$this->_get_query_t_pengeluarankb_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_pengeluarankb_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_pengeluarankb_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_pengeluarankb_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_pengeluarankb_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //WO TEMP
    /* WO DETAIL */
    var $t_pengeluarankb_dtl_temp_view = "sc_tmp.pengeluarankb_dtl";
    var $t_pengeluarankb_dtl_temp_view_column = array('idurut','idoca','nmcoa','dk','nilai','remarks');
    var $t_pengeluarankb_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_pengeluarankb_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_pengeluarankb_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_pengeluarankb_dtl_temp_view_column as $mrp)
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

                if(count($this->t_pengeluarankb_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_pengeluarankb_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_pengeluarankb_dtl_temp_view_order))
        {
            $order = $this->t_pengeluarankb_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_pengeluarankb_dtl_temp_view($docno){
        $builder = $this->_get_query_t_pengeluarankb_dtl_temp($docno);
        ////$this->_get_query_t_pengeluarankb_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_pengeluarankb_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_pengeluarankb_dtl_temp($docno);
        ////$this->_get_query_t_pengeluarankb_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_pengeluarankb_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_pengeluarankb_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_pengeluarankb_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_pengeluarankb_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_pengeluarankb_view = "sc_trx.po";
    var $t_front_pengeluarankb_view = "(select a.*,
    b.nmbranch,
    z.uraian as status_desc,
    c.nmsupplier as nmsuppliernew,
    d.namakotakab AS nmkota,
    o.nmcoa as nmcoa
    from sc_trx.pengeluarankb a 
    left outer join sc_mst.mstsupplier c on a.kdsupplier=c.kdsupplier
    left outer join sc_mst.coa o on a.prkkas=o.idcoa
    left outer join sc_mst.kotakab d on c.idkota=d.kodekotakab
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.P.A.3') as x";
    var $t_front_pengeluarankb_view_column = array('docno','docdate','status_desc','keterangan','nmbranch');
    var $t_front_pengeluarankb_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_pengeluarankb()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_pengeluarankb_view);
        // $builder->join(
        //     "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
        //     FROM sc_mst.trxtype 
        //     WHERE jenistrx = 'I.P.A.2' 
        //     ORDER BY kdtrx, uraian DESC) AS trx", 
        //     "COALESCE(x.status, '') = COALESCE(trx.kdtrx, '')", 
        //     "left"
        // );
        $builder->select("x.*");
        // $builder->where('inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_pengeluarankb_view_column as $mrpgroup)
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

                if(count($this->t_front_pengeluarankb_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_pengeluarankb_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_pengeluarankb_view_order))
        {
            $order = $this->t_front_pengeluarankb_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_pengeluarankb_view(){
        $builder = $this->_get_query_front_pengeluarankb();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_pengeluarankb_view_count_filtered()
    {
        $builder = $this->_get_query_front_pengeluarankb();
        ////$this->_get_query_t_po();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_pengeluarankb_view_count_all()
    {
        $builder = $this->_get_query_front_pengeluarankb();
        return $builder->countAllResults();
    }
    public function get_t_front_pengeluarankb_view_by_id($id)
    {
        $builder = $this->_get_query_front_pengeluarankb();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    public function q_pengeluarankb_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.pengeluarankb where docno is not null $param");
    }

    public function q_pengeluarankb_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.pengeluarankb_dtl where docno is not null $param order by idurut desc");
    }


    public function q_pengeluarankb_master($param)
    {
        return $this->db->query("select * from sc_trx.pengeluarankb where docno is not null $param");
    }

    public function q_pengeluarankb_dtl($param)
    {
        return $this->db->query("select * from sc_trx.pengeluarankb_dtl where docno is not null $param order by idurut desc");
    }

}