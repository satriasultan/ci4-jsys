<?php

namespace App\Models\Purchase;

use CodeIgniter\Model;

class M_Purchase extends Model
{
    //PP

    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_pp_view = "sc_trx.pp";
    var $t_pp_view_column = array('docno','docref','description');
    var $t_pp_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_pp()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_pp_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_pp_view_column as $mrp)
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

                if(count($this->t_pp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_pp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_pp_view_order))
        {
            $order = $this->t_pp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_pp_view(){
        $builder = $this->_get_query_t_pp();
        ////$this->_get_query_t_pp();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_pp_view_count_filtered()
    {
        $builder = $this->_get_query_t_pp();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_pp_view_count_all()
    {
        $builder = $this->_get_query_t_pp();
        return $builder->countAllResults();
    }
    public function get_t_pp_view_by_id($id)
    {
        $builder = $this->_get_query_t_pp();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_pp_dtl_view = "sc_trx.pp_dtl";
    var $t_pp_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $t_pp_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_pp_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_pp_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_pp_dtl_view_column as $mrp)
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

                if(count($this->t_pp_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_pp_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_pp_dtl_view_order))
        {
            $order = $this->t_pp_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_pp_dtl_view($docnoParam){
        $builder = $this->_get_query_t_pp_dtl($docnoParam);
        ////$this->_get_query_t_pp_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_pp_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_pp_dtl($docnoParam);
        ////$this->_get_query_t_pp_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_pp_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_pp_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_pp_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_pp_dtl($docnoParam);
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

    public function q_pp_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.pp where docno is not null $param");
    }

    public function q_pp_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.pp_dtl where docno is not null $param order by idurut desc");
    }


    public function q_pp_master($param)
    {
        return $this->db->query("select * from sc_trx.pp where docno is not null $param");
    }

    public function q_pp_dtl($param)
    {
        return $this->db->query("select * from sc_trx.pp_dtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_pp_dtl_temp_view = "sc_tmp.pp_dtl";
    var $t_pp_dtl_temp_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $t_pp_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_pp_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_pp_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_pp_dtl_temp_view_column as $mrp)
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

                if(count($this->t_pp_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_pp_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_pp_dtl_temp_view_order))
        {
            $order = $this->t_pp_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_pp_dtl_temp_view($docno){
        $builder = $this->_get_query_t_pp_dtl_temp($docno);
        ////$this->_get_query_t_pp_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_pp_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_pp_dtl_temp($docno);
        ////$this->_get_query_t_pp_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_pp_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_pp_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_pp_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_pp_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_pp_view = "sc_trx.pp";
    var $t_front_pp_view = "(select a.*, 
    b.nmbranch,
    z.uraian as status_desc
    from sc_trx.pp a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.P.A.1') as x";
    var $t_front_pp_view_column = array('docno','cust','description');
    var $t_front_pp_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_pp()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_pp_view);
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
        foreach ($this->t_front_pp_view_column as $mrpgroup)
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

                if(count($this->t_front_pp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_pp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_pp_view_order))
        {
            $order = $this->t_front_pp_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_pp_view(){
        $builder = $this->_get_query_front_pp();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_pp_view_count_filtered()
    {
        $builder = $this->_get_query_front_pp();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_pp_view_count_all()
    {
        $builder = $this->_get_query_front_pp();
        return $builder->countAllResults();
    }
    public function get_t_front_pp_view_by_id($id)
    {
        $builder = $this->_get_query_front_pp();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }












    // =============================== VOID PP ============================================================




    //PP

    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_voidpp_view = "sc_trx.voidpp";
    var $t_voidpp_view_column = array('docno','docref','description');
    var $t_voidpp_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_voidpp()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_voidpp_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_voidpp_view_column as $mrp)
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

                if(count($this->t_voidpp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_voidpp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_voidpp_view_order))
        {
            $order = $this->t_voidpp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_voidpp_view(){
        $builder = $this->_get_query_t_voidpp();
        ////$this->_get_query_t_voidpp();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_voidpp_view_count_filtered()
    {
        $builder = $this->_get_query_t_voidpp();
        ////$this->_get_query_t_voidpp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_voidpp_view_count_all()
    {
        $builder = $this->_get_query_t_voidpp();
        return $builder->countAllResults();
    }
    public function get_t_voidpp_view_by_id($id)
    {
        $builder = $this->_get_query_t_voidpp();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_voidpp_dtl_view = "sc_trx.voidpp_dtl";
    var $t_voidpp_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $t_voidpp_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_voidpp_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_voidpp_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_voidpp_dtl_view_column as $mrp)
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

                if(count($this->t_voidpp_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_voidpp_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_voidpp_dtl_view_order))
        {
            $order = $this->t_voidpp_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_voidpp_dtl_view($docnoParam){
        $builder = $this->_get_query_t_voidpp_dtl($docnoParam);
        ////$this->_get_query_t_voidpp_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_voidpp_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_voidpp_dtl($docnoParam);
        ////$this->_get_query_t_voidpp_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_voidpp_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_voidpp_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_voidpp_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_voidpp_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_voidpp_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.voidpp where docno is not null $param");
    }

    public function q_voidpp_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.voidpp_dtl where docno is not null $param order by idurut desc");
    }


    public function q_voidpp_master($param)
    {
        return $this->db->query("select * from sc_trx.voidpp where docno is not null $param");
    }

    public function q_voidpp_dtl($param)
    {
        return $this->db->query("select * from sc_trx.voidpp_dtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_voidpp_dtl_temp_view = "sc_tmp.voidpp_dtl";
    var $t_voidpp_dtl_temp_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $t_voidpp_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_voidpp_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_voidpp_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_voidpp_dtl_temp_view_column as $mrp)
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

                if(count($this->t_voidpp_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_voidpp_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_voidpp_dtl_temp_view_order))
        {
            $order = $this->t_voidpp_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_voidpp_dtl_temp_view($docno){
        $builder = $this->_get_query_t_voidpp_dtl_temp($docno);
        ////$this->_get_query_t_voidpp_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_voidpp_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_voidpp_dtl_temp($docno);
        ////$this->_get_query_t_voidpp_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_voidpp_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_voidpp_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_voidpp_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_voidpp_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_voidpp_view = "sc_trx.voidpp";
    var $t_front_voidpp_view = "(select a.*, 
    b.nmbranch,
    z.uraian as status_desc
    from sc_trx.voidpp a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.P.A.1') as x";
    var $t_front_voidpp_view_column = array('docno','cust','description');
    var $t_front_voidpp_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_voidpp()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_voidpp_view);
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
        foreach ($this->t_front_voidpp_view_column as $mrpgroup)
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

                if(count($this->t_front_voidpp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_voidpp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_voidpp_view_order))
        {
            $order = $this->t_front_voidpp_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_voidpp_view(){
        $builder = $this->_get_query_front_voidpp();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_voidpp_view_count_filtered()
    {
        $builder = $this->_get_query_front_voidpp();
        ////$this->_get_query_t_voidpp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_voidpp_view_count_all()
    {
        $builder = $this->_get_query_front_voidpp();
        return $builder->countAllResults();
    }
    public function get_t_front_voidpp_view_by_id($id)
    {
        $builder = $this->_get_query_front_voidpp();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }





}