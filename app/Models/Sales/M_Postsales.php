<?php

namespace App\Models\Sales;

use CodeIgniter\Model;

class M_Postsales extends Model
{



    //  ============================================== Sales Order ====================================================


    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_salesorder_view = "sc_trx.salesorder";
    var $t_salesorder_view_column = array('docno','docref','description');
    var $t_salesorder_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_salesorder()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_salesorder_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_salesorder_view_column as $mrp)
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

                if(count($this->t_salesorder_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo salesorderst column 0
                $builder->orderBy($this->t_salesorder_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_salesorder_view_order))
        {
            $order = $this->t_salesorder_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_salesorder_view(){
        $builder = $this->_get_query_t_salesorder();
        ////$this->_get_query_t_salesorder();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_salesorder_view_count_filtered()
    {
        $builder = $this->_get_query_t_salesorder();
        ////$this->_get_query_t_salesorder();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_salesorder_view_count_all()
    {
        $builder = $this->_get_query_t_salesorder();
        return $builder->countAllResults();
    }
    public function get_t_salesorder_view_by_id($id)
    {
        $builder = $this->_get_query_t_salesorder();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_salesorder_dtl_view = "sc_trx.salesorder_dtl";
    var $t_salesorder_dtl_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_salesorder_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_salesorder_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_salesorder_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_salesorder_dtl_view_column as $mrp)
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

                if(count($this->t_salesorder_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo salesorderst column 0
                $builder->orderBy($this->t_salesorder_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_salesorder_dtl_view_order))
        {
            $order = $this->t_salesorder_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_salesorder_dtl_view($docnoParam){
        $builder = $this->_get_query_t_salesorder_dtl($docnoParam);
        ////$this->_get_query_t_salesorder_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_salesorder_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_salesorder_dtl($docnoParam);
        ////$this->_get_query_t_salesorder_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_salesorder_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_salesorder_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_salesorder_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_salesorder_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_salesorder_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.salesorder where docno is not null $param");
    }

    public function q_salesorder_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.salesorder_dtl where docno is not null $param order by idurut desc");
    }


    public function q_salesorder_master($param)
    {
        return $this->db->query("select * from sc_trx.salesorder where docno is not null $param");
    }

    public function q_salesorder_dtl($param)
    {
        return $this->db->query("select * from sc_trx.salesorder_dtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_salesorder_dtl_temp_view = "sc_tmp.salesorder_dtl";
    var $t_salesorder_dtl_temp_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_salesorder_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_salesorder_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_salesorder_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_salesorder_dtl_temp_view_column as $mrp)
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

                if(count($this->t_salesorder_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo salesorderst column 0
                $builder->orderBy($this->t_salesorder_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_salesorder_dtl_temp_view_order))
        {
            $order = $this->t_salesorder_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_salesorder_dtl_temp_view($docno){
        $builder = $this->_get_query_t_salesorder_dtl_temp($docno);
        ////$this->_get_query_t_salesorder_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_salesorder_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_salesorder_dtl_temp($docno);
        ////$this->_get_query_t_salesorder_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_salesorder_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_salesorder_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_salesorder_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_salesorder_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_salesorder_view = "sc_trx.salesorder";
    var $t_front_salesorder_view = "(select a.*, 
    c.alamat_kantor as alamatcust,
    c.nmcustomer as nmcust,
    b.nmbranch,
    c.nmcustomer,
    s.nmsalesman,
    d.namakotakab AS nmkota,
    z.uraian as status_desc
    from sc_trx.salesorder a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.customer c on a.kdcustomer=c.kdcustomer
    left outer join sc_mst.salesman s on a.kdsalesman=s.kdsalesman
    left outer join sc_mst.kotakab d on c.kota_kantor=d.kodekotakab
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.S.B.1') as x";
    var $t_front_salesorder_view_column = array('docno','docdate','status_desc','kdcustomer','nmcust','alamatcust','nmkota','currcode','jthtempo','keterangan','nmbranch');
    var $t_front_salesorder_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_salesorder()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_salesorder_view);
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
        foreach ($this->t_front_salesorder_view_column as $mrpgroup)
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

                if(count($this->t_front_salesorder_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo salesorderst column 0
                $builder->orderBy($this->t_front_salesorder_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_salesorder_view_order))
        {
            $order = $this->t_front_salesorder_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_salesorder_view(){
        $builder = $this->_get_query_front_salesorder();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_salesorder_view_count_filtered()
    {
        $builder = $this->_get_query_front_salesorder();
        ////$this->_get_query_t_salesorder();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_salesorder_view_count_all()
    {
        $builder = $this->_get_query_front_salesorder();
        return $builder->countAllResults();
    }
    public function get_t_front_salesorder_view_by_id($id)
    {
        $builder = $this->_get_query_front_salesorder();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }







    
    var $t_front_salesorder_apprv_view = "(select a.*, 
    c.alamat_kantor as alamatcust,
    c.nmcustomer as nmcust,
    b.nmbranch,
    c.nmcustomer,
    s.nmsalesman,
    d.namakotakab AS nmkota,
    z.uraian as status_desc
    from sc_trx.salesorder a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.customer c on a.kdcustomer=c.kdcustomer
    left outer join sc_mst.salesman s on a.kdsalesman=s.kdsalesman
    left outer join sc_mst.kotakab d on c.kota_kantor=d.kodekotakab
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.S.B.1'
    where z.uraian != 'APPROVED' AND z.uraian != 'CANCEL') as x";
    var $t_front_salesorder_apprv_view_column = array('docno','docdate','status_desc','kdcustomer','nmcust','alamatcust','nmkota','currcode','jthtempo','keterangan','nmbranch');
    var $t_front_salesorder_apprv_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_salesorder_apprv()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_salesorder_apprv_view);
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
        foreach ($this->t_front_salesorder_apprv_view_column as $mrpgroup)
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

                if(count($this->t_front_salesorder_apprv_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo salesorder_apprvst column 0
                $builder->orderBy($this->t_front_salesorder_apprv_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_salesorder_apprv_view_order))
        {
            $order = $this->t_front_salesorder_apprv_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_salesorder_apprv_view(){
        $builder = $this->_get_query_front_salesorder_apprv();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_salesorder_apprv_view_count_filtered()
    {
        $builder = $this->_get_query_front_salesorder_apprv();
        ////$this->_get_query_t_salesorder_apprv();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_salesorder_apprv_view_count_all()
    {
        $builder = $this->_get_query_front_salesorder_apprv();
        return $builder->countAllResults();
    }
    public function get_t_front_salesorder_apprv_view_by_id($id)
    {
        $builder = $this->_get_query_front_salesorder_apprv();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }










    
    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_penjualan_view = "sc_trx.penjualan";
    var $t_penjualan_view_column = array('docno','docref','description');
    var $t_penjualan_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_penjualan()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_penjualan_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_penjualan_view_column as $mrp)
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

                if(count($this->t_penjualan_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo penjualanst column 0
                $builder->orderBy($this->t_penjualan_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_penjualan_view_order))
        {
            $order = $this->t_penjualan_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_penjualan_view(){
        $builder = $this->_get_query_t_penjualan();
        ////$this->_get_query_t_penjualan();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_penjualan_view_count_filtered()
    {
        $builder = $this->_get_query_t_penjualan();
        ////$this->_get_query_t_penjualan();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_penjualan_view_count_all()
    {
        $builder = $this->_get_query_t_penjualan();
        return $builder->countAllResults();
    }
    public function get_t_penjualan_view_by_id($id)
    {
        $builder = $this->_get_query_t_penjualan();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_penjualan_dtl_view = "sc_trx.penjualan_dtl";
    var $t_penjualan_dtl_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_penjualan_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_penjualan_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_penjualan_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_penjualan_dtl_view_column as $mrp)
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

                if(count($this->t_penjualan_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo penjualanst column 0
                $builder->orderBy($this->t_penjualan_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_penjualan_dtl_view_order))
        {
            $order = $this->t_penjualan_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_penjualan_dtl_view($docnoParam){
        $builder = $this->_get_query_t_penjualan_dtl($docnoParam);
        ////$this->_get_query_t_penjualan_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_penjualan_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_penjualan_dtl($docnoParam);
        ////$this->_get_query_t_penjualan_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_penjualan_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_penjualan_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_penjualan_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_penjualan_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_penjualan_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.penjualan where docno is not null $param");
    }

    public function q_penjualan_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.penjualan_dtl where docno is not null $param order by idurut desc");
    }


    public function q_penjualan_master($param)
    {
        return $this->db->query("select * from sc_trx.penjualan where docno is not null $param");
    }

    public function q_penjualan_dtl($param)
    {
        return $this->db->query("select * from sc_trx.penjualan_dtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_penjualan_dtl_temp_view = "sc_tmp.penjualan_dtl";
    var $t_penjualan_dtl_temp_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_penjualan_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_penjualan_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_penjualan_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_penjualan_dtl_temp_view_column as $mrp)
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

                if(count($this->t_penjualan_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo penjualanst column 0
                $builder->orderBy($this->t_penjualan_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_penjualan_dtl_temp_view_order))
        {
            $order = $this->t_penjualan_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_penjualan_dtl_temp_view($docno){
        $builder = $this->_get_query_t_penjualan_dtl_temp($docno);
        ////$this->_get_query_t_penjualan_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_penjualan_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_penjualan_dtl_temp($docno);
        ////$this->_get_query_t_penjualan_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_penjualan_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_penjualan_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_penjualan_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_penjualan_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_penjualan_view = "sc_trx.penjualan";
    var $t_front_penjualan_view = "(select a.*, 
    c.kdcustomer as kdcust,
    c.nmcustomer as nmcust,
    c.alamat_kantor as alamatcust,
    l.kdcustomer as kdcustdeliv,
    l.nmcustomer as nmcustdeliv,
    l.alamat_kantor as alamatcustdeliv,
    b.nmbranch,
    s.nmsalesman,
    d.namakotakab AS nmkota,
    k.namakotakab AS nmkotadeliv,
    z.uraian as status_desc
    from sc_trx.penjualan a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.customer c on a.kdcustomer=c.kdcustomer
    left outer join sc_mst.customer l on a.kdcustomerdeliv=l.kdcustomer
    left outer join sc_mst.salesman s on a.kdsalesman=s.kdsalesman
    left outer join sc_mst.kotakab d on c.kota_kantor=d.kodekotakab
    left outer join sc_mst.kotakab k on l.kota_kantor=k.kodekotakab
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.S.B.1') as x";
    var $t_front_penjualan_view_column = array('docno','docdate','status_desc','kdcustomer','nmcust','alamatcust','nmkota','currcode','jthtempo','keterangan','nmbranch');
    var $t_front_penjualan_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_penjualan()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_penjualan_view);
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
        foreach ($this->t_front_penjualan_view_column as $mrpgroup)
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

                if(count($this->t_front_penjualan_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo penjualanst column 0
                $builder->orderBy($this->t_front_penjualan_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_penjualan_view_order))
        {
            $order = $this->t_front_penjualan_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_penjualan_view(){
        $builder = $this->_get_query_front_penjualan();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_penjualan_view_count_filtered()
    {
        $builder = $this->_get_query_front_penjualan();
        ////$this->_get_query_t_penjualan();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_penjualan_view_count_all()
    {
        $builder = $this->_get_query_front_penjualan();
        return $builder->countAllResults();
    }
    public function get_t_front_penjualan_view_by_id($id)
    {
        $builder = $this->_get_query_front_penjualan();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }







    
    var $t_front_penjualan_apprv_view = "(select a.*, 
    c.alamat_kantor as alamatcust,
    c.nmcustomer as nmcust,
    b.nmbranch,
    c.nmcustomer,
    s.nmsalesman,
    d.namakotakab AS nmkota,
    z.uraian as status_desc
    from sc_trx.penjualan a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.customer c on a.kdcustomer=c.kdcustomer
    left outer join sc_mst.salesman s on a.kdsalesman=s.kdsalesman
    left outer join sc_mst.kotakab d on c.kota_kantor=d.kodekotakab
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.S.B.1'
    where z.uraian != 'APPROVED' AND z.uraian != 'CANCEL') as x";
    var $t_front_penjualan_apprv_view_column = array('docno','docdate','status_desc','kdcustomer','nmcust','alamatcust','nmkota','currcode','jthtempo','keterangan','nmbranch');
    var $t_front_penjualan_apprv_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_penjualan_apprv()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_penjualan_apprv_view);
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
        foreach ($this->t_front_penjualan_apprv_view_column as $mrpgroup)
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

                if(count($this->t_front_penjualan_apprv_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo penjualan_apprvst column 0
                $builder->orderBy($this->t_front_penjualan_apprv_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_penjualan_apprv_view_order))
        {
            $order = $this->t_front_penjualan_apprv_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_penjualan_apprv_view(){
        $builder = $this->_get_query_front_penjualan_apprv();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_penjualan_apprv_view_count_filtered()
    {
        $builder = $this->_get_query_front_penjualan_apprv();
        ////$this->_get_query_t_penjualan_apprv();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_penjualan_apprv_view_count_all()
    {
        $builder = $this->_get_query_front_penjualan_apprv();
        return $builder->countAllResults();
    }
    public function get_t_front_penjualan_apprv_view_by_id($id)
    {
        $builder = $this->_get_query_front_penjualan_apprv();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }





















    //OFFERING
    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_salesorderexternal_view = "sc_trx.salesorderexternal";
    var $t_salesorderexternal_view_column = array('docno','docref','description');
    var $t_salesorderexternal_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_salesorderexternal()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_salesorderexternal_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_salesorderexternal_view_column as $mrp)
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

                if(count($this->t_salesorderexternal_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_salesorderexternal_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_salesorderexternal_view_order))
        {
            $order = $this->t_salesorderexternal_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_salesorderexternal_view(){
        $builder = $this->_get_query_t_salesorderexternal();
        ////$this->_get_query_t_salesorderexternal();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_salesorderexternal_view_count_filtered()
    {
        $builder = $this->_get_query_t_salesorderexternal();
        ////$this->_get_query_t_salesorderexternal();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_salesorderexternal_view_count_all()
    {
        $builder = $this->_get_query_t_salesorderexternal();
        return $builder->countAllResults();
    }
    public function get_t_salesorderexternal_view_by_id($id)
    {
        $builder = $this->_get_query_t_salesorderexternal();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_salesorderexternal_dtl_view = "sc_trx.salesorderexternaldtl";
    var $t_salesorderexternal_dtl_view_column = array('docno','docref','description');
    var $t_salesorderexternal_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_salesorderexternal_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_salesorderexternal_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_salesorderexternal_dtl_view_column as $mrp)
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

                if(count($this->t_salesorderexternal_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_salesorderexternal_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_salesorderexternal_dtl_view_order))
        {
            $order = $this->t_salesorderexternal_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_salesorderexternal_dtl_view($docnoParam){
        $builder = $this->_get_query_t_salesorderexternal_dtl($docnoParam);
        ////$this->_get_query_t_salesorderexternal_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_salesorderexternal_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_salesorderexternal_dtl($docnoParam);
        ////$this->_get_query_t_salesorderexternal_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_salesorderexternal_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_salesorderexternal_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_salesorderexternal_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_salesorderexternal_dtl($docnoParam);
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

    public function q_salesorderexternal_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.salesorderexternal where docno is not null $param");
    }

    public function q_salesorderexternal_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.salesorderexternaldtl where docno is not null $param order by idurut desc");
    }


    public function q_salesorderexternal_master($param)
    {
        return $this->db->query("select * from sc_trx.salesorderexternal where docno is not null $param");
    }

    public function q_salesorderexternal_dtl($param)
    {
        return $this->db->query("select * from sc_trx.salesorderexternaldtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_salesorderexternal_dtl_temp_view = "sc_tmp.salesorderexternaldtl";
    var $t_salesorderexternal_dtl_temp_view_column = array('docno','docref','description');
    var $t_salesorderexternal_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_salesorderexternal_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_salesorderexternal_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_salesorderexternal_dtl_temp_view_column as $mrp)
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

                if(count($this->t_salesorderexternal_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_salesorderexternal_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_salesorderexternal_dtl_temp_view_order))
        {
            $order = $this->t_salesorderexternal_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_salesorderexternal_dtl_temp_view($docno){
        $builder = $this->_get_query_t_salesorderexternal_dtl_temp($docno);
        ////$this->_get_query_t_salesorderexternal_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_salesorderexternal_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_salesorderexternal_dtl_temp($docno);
        ////$this->_get_query_t_salesorderexternal_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_salesorderexternal_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_salesorderexternal_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_salesorderexternal_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_salesorderexternal_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_salesorderexternal_view = "sc_trx.salesorderexternal";
    var $t_front_salesorderexternal_view = "(select b.nama_customer as nmcust,
                                b.docno as docnocust,
                                b.alamat_kantor,
                                b.alamat_pengiriman,
                                b.alamat_penagihan,
                                b.email,
                                b.kontak_pic,
                                b.jabatanpic,
                                b.telepon,
                                b.fax,

                                a.inputby as salesorderexternal_inputby,
                                a.status as salesorderexternal_status,
                                trim(a.status) as trimmed_status,
                                a.* 
                        from sc_trx.salesorderexternal a
                        left outer join sc_mst.customer b on a.cust = b.nama_customer
                        ) as x";

    var $t_front_salesorderexternal_view_column = array('docno','cust','description');
    var $t_front_salesorderexternal_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_salesorderexternal()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_salesorderexternal_view);
        $builder->join(
            "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
            FROM sc_mst.trxtype 
            WHERE jenistrx = 'I.S.B.1' 
            ORDER BY kdtrx, uraian DESC) AS trx", 
            "COALESCE(x.salesorderexternal_status, '') = COALESCE(trx.kdtrx, '')", 
            "left"
        );
        $builder->select("x.*, trx.uraian AS status_desc");
        $builder->where('salesorderexternal_inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_salesorderexternal_view_column as $mrpgroup)
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

                if(count($this->t_front_salesorderexternal_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_salesorderexternal_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_salesorderexternal_view_order))
        {
            $order = $this->t_front_salesorderexternal_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_salesorderexternal_view(){
        $builder = $this->_get_query_front_salesorderexternal();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_salesorderexternal_view_count_filtered()
    {
        $builder = $this->_get_query_front_salesorderexternal();
        ////$this->_get_query_t_salesorderexternal();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_salesorderexternal_view_count_all()
    {
        $builder = $this->_get_query_front_salesorderexternal();
        return $builder->countAllResults();
    }
    public function get_t_front_salesorderexternal_view_by_id($id)
    {
        $builder = $this->_get_query_front_salesorderexternal();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function getRolePO($jobcode, $codemenu)
    {
        return $this->db->query("
            SELECT prefix,infix, suffix 
            FROM sc_mst.rolepo 
            WHERE TRIM(jobcode) = ? AND TRIM(codemenu) = ?
        ", [$jobcode, $codemenu])->getRowArray();
    }

    public function getRate(string $currCode, string $docDate)
    {
        $sql = "
            SELECT e.nilai
            FROM sc_mst.exchangerate e
            JOIN sc_mst.currency c ON e.idcurr = c.id
            WHERE c.id = ?                  -- kalau pakai ID currency
            AND e.exchangedate <= ?
            ORDER BY e.exchangedate DESC
            LIMIT 1
        ";

        $query = $this->db->query($sql, [$currCode, $docDate]);
        $row   = $query->getRow();
        return $row ? $row->nilai : null;
    }























    //OFFERING
    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_soi_view = "sc_trx.soi";
    var $t_soi_view_column = array('docno','docref','description');
    var $t_soi_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_soi()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_soi_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_soi_view_column as $mrp)
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

                if(count($this->t_soi_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_soi_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_soi_view_order))
        {
            $order = $this->t_soi_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_soi_view(){
        $builder = $this->_get_query_t_soi();
        ////$this->_get_query_t_soi();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_soi_view_count_filtered()
    {
        $builder = $this->_get_query_t_soi();
        ////$this->_get_query_t_soi();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_soi_view_count_all()
    {
        $builder = $this->_get_query_t_soi();
        return $builder->countAllResults();
    }
    public function get_t_soi_view_by_id($id)
    {
        $builder = $this->_get_query_t_soi();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_soi_dtl_view = "sc_trx.soidtl";
    var $t_soi_dtl_view_column = array('docno','docref','description');
    var $t_soi_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_soi_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_soi_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_soi_dtl_view_column as $mrp)
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

                if(count($this->t_soi_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_soi_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_soi_dtl_view_order))
        {
            $order = $this->t_soi_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_soi_dtl_view($docnoParam){
        $builder = $this->_get_query_t_soi_dtl($docnoParam);
        ////$this->_get_query_t_soi_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_soi_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_soi_dtl($docnoParam);
        ////$this->_get_query_t_soi_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_soi_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_soi_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_soi_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_soi_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    public function q_soi_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.soi where docno is not null $param");
    }

    public function q_soi_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.soidtl where docno is not null $param order by idurut desc");
    }


    public function q_soi_master($param)
    {
        return $this->db->query("select * from sc_trx.soi where docno is not null $param");
    }

    public function q_soi_dtl($param)
    {
        return $this->db->query("select * from sc_trx.soidtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_soi_dtl_temp_view = "sc_tmp.soidtl";
    var $t_soi_dtl_temp_view_column = array('docno','docref','description');
    var $t_soi_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_soi_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_soi_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_soi_dtl_temp_view_column as $mrp)
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

                if(count($this->t_soi_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_soi_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_soi_dtl_temp_view_order))
        {
            $order = $this->t_soi_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_soi_dtl_temp_view($docno){
        $builder = $this->_get_query_t_soi_dtl_temp($docno);
        ////$this->_get_query_t_soi_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_soi_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_soi_dtl_temp($docno);
        ////$this->_get_query_t_soi_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_soi_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_soi_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_soi_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_soi_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_soi_view = "sc_trx.soi";
    var $t_front_soi_view = "(select b.nama_customer as nmcust,
                                b.docno as docnocust,
                                b.alamat_kantor,
                                b.alamat_pengiriman,
                                b.alamat_penagihan,
                                b.email,
                                b.kontak_pic,
                                b.jabatanpic,
                                b.telepon,
                                b.fax,

                                a.inputby as soi_inputby,
                                a.status as soi_status,
                                trim(a.status) as trimmed_status,
                                a.* 
                        from sc_trx.soi a
                        left outer join sc_mst.customer b on a.cust = b.nama_customer
                        ) as x";

    var $t_front_soi_view_column = array('docno','cust','description');
    var $t_front_soi_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_soi()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_soi_view);
        $builder->join(
            "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
            FROM sc_mst.trxtype 
            WHERE jenistrx = 'I.S.B.2' 
            ORDER BY kdtrx, uraian DESC) AS trx", 
            "COALESCE(x.soi_status, '') = COALESCE(trx.kdtrx, '')", 
            "left"
        );
        $builder->select("x.*, trx.uraian AS status_desc");
        $builder->where('soi_inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_soi_view_column as $mrpgroup)
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

                if(count($this->t_front_soi_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_soi_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_soi_view_order))
        {
            $order = $this->t_front_soi_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_soi_view(){
        $builder = $this->_get_query_front_soi();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_soi_view_count_filtered()
    {
        $builder = $this->_get_query_front_soi();
        ////$this->_get_query_t_soi();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_soi_view_count_all()
    {
        $builder = $this->_get_query_front_soi();
        return $builder->countAllResults();
    }
    public function get_t_front_soi_view_by_id($id)
    {
        $builder = $this->_get_query_front_soi();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function getRolePOSOI($jobcode, $codemenu)
    {
        return $this->db->query("
            SELECT prefix,infix, suffix 
            FROM sc_mst.rolepo 
            WHERE TRIM(jobcode) = ? AND TRIM(codemenu) = ?
        ", [$jobcode, $codemenu])->getRowArray();
    }

    public function getRateSOI(string $currCode, string $docDate)
    {
        $sql = "
            SELECT e.nilai
            FROM sc_mst.exchangerate e
            JOIN sc_mst.currency c ON e.idcurr = c.id
            WHERE c.id = ?                  -- kalau pakai ID currency
            AND e.exchangedate <= ?
            ORDER BY e.exchangedate DESC
            LIMIT 1
        ";

        $query = $this->db->query($sql, [$currCode, $docDate]);
        $row   = $query->getRow();
        return $row ? $row->nilai : null;
    }

    public function q_view_print_soi_dtl($param,$nama)
    {
        return $this->db->query("select * from sc_trx.view_print_soidtl where docno is not null and printby='$nama' $param order by inputdate asc,idurut asc");
    }





}