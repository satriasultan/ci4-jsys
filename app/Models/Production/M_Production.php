<?php

namespace App\Models\Production;

use CodeIgniter\Model;

class M_Production extends Model
{
    //STANDART COST



    var $mst_standart_cost_mst_view = "(select A.*,z.uraian as nmstatus,kdtrx,z.jenistrx,z.uraian as nmstatus from sc_mst.standart_cost_mst a 
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.1' and trim(a.status)=trim(z.kdtrx)) as x";
    var $mst_standart_cost_mst_view_column = array('docno','docdate','activatedate');
    var $mst_standart_cost_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_mst_standart_cost_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->mst_standart_cost_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->mst_standart_cost_mst_view_column as $mrpgroup)
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

                if(count($this->mst_standart_cost_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->mst_standart_cost_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->mst_standart_cost_mst_view_order))
        {
            $order = $this->mst_standart_cost_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_mst_standart_cost_mst_view(){
        $builder = $this->_get_mst_standart_cost_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function mst_standart_cost_mst_view_count_filtered()
    {
        $builder = $this->_get_mst_standart_cost_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function mst_standart_cost_mst_view_count_all()
    {
        $builder = $this->_get_mst_standart_cost_mst();
        return $builder->countAllResults();
    }
    public function get_mst_standart_cost_mst_view_by_id($id)
    {
        $builder = $this->_get_mst_standart_cost_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_mst_standart_cost_mst($param)
    {
        return $this->db->query("select * from sc_mst.standart_cost_mst where docno is not null $param");
    }
    function q_mst_standart_cost_dtl($param)
    {
        return $this->db->query("select * from sc_mst.standart_cost_dtl where docno is not null $param");
    }

    function q_tmp_standart_cost_mst($param)
    {
        return $this->db->query("select * from sc_tmp.standart_cost_mst where docno is not null $param");
    }

    function q_tmp_standart_cost_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.standart_cost_dtl where docno is not null $param");
    }

    /* TEMPORARY STANDART COST */

    var $tmp_standart_cost_dtl_view = "sc_tmp.standart_cost_dtl";
    var $tmp_standart_cost_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_standart_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_standart_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_standart_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->tmp_standart_cost_dtl_view_column as $mrp)
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

                if(count($this->tmp_standart_cost_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_standart_cost_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_standart_cost_dtl_view_order))
        {
            $order = $this->tmp_standart_cost_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_standart_cost_dtl_view($docno){
        $builder = $this->_get_query_tmp_standart_cost_dtl($docno);
        ////$this->_get_query_tmp_standart_cost_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_standart_cost_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_standart_cost_dtl($docno);
        ////$this->_get_query_tmp_standart_cost_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_standart_cost_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_standart_cost_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_standart_cost_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_standart_cost_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* STANDART COST MST TEMPORARY STANDART COST */

    var $mst_standart_cost_dtl_view = "sc_mst.standart_cost_dtl";
    var $mst_standart_cost_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $mst_standart_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_mst_standart_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->mst_standart_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->mst_standart_cost_dtl_view_column as $mrp)
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

                if(count($this->mst_standart_cost_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->mst_standart_cost_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->mst_standart_cost_dtl_view_order))
        {
            $order = $this->mst_standart_cost_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_mst_standart_cost_dtl_view($docno){
        $builder = $this->_get_query_mst_standart_cost_dtl($docno);
        ////$this->_get_query_mst_standart_cost_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function mst_standart_cost_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_mst_standart_cost_dtl($docno);
        ////$this->_get_query_mst_standart_cost_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function mst_standart_cost_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_mst_standart_cost_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_mst_standart_cost_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_mst_standart_cost_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


/* BIAYA STANDART */

    var $mst_biaya_standart_mst_view = "(select A.*,z.uraian as nmstatus,kdtrx,z.jenistrx,z.uraian as nmstatus from sc_mst.biaya_standart_mst a 
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.1' and trim(a.status)=trim(z.kdtrx)) as x";
    var $mst_biaya_standart_mst_view_column = array('docno','docdate','activatedate');
    var $mst_biaya_standart_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_mst_biaya_standart_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->mst_biaya_standart_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->mst_biaya_standart_mst_view_column as $mrpgroup)
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

                if(count($this->mst_biaya_standart_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->mst_biaya_standart_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->mst_biaya_standart_mst_view_order))
        {
            $order = $this->mst_biaya_standart_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_mst_biaya_standart_mst_view(){
        $builder = $this->_get_mst_biaya_standart_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function mst_biaya_standart_mst_view_count_filtered()
    {
        $builder = $this->_get_mst_biaya_standart_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function mst_biaya_standart_mst_view_count_all()
    {
        $builder = $this->_get_mst_biaya_standart_mst();
        return $builder->countAllResults();
    }
    public function get_mst_biaya_standart_mst_view_by_id($id)
    {
        $builder = $this->_get_mst_biaya_standart_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_mst_biaya_standart_mst($param)
    {
        return $this->db->query("select * from sc_mst.biaya_standart_mst where docno is not null $param");
    }
    function q_mst_biaya_standart_dtl($param)
    {
        return $this->db->query("select * from sc_mst.biaya_standart_dtl where docno is not null $param");
    }

    function q_tmp_biaya_standart_mst($param)
    {
        return $this->db->query("select * from sc_tmp.biaya_standart_mst where docno is not null $param");
    }

    function q_tmp_biaya_standart_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.biaya_standart_dtl where docno is not null $param");
    }

    /* TEMPORARY STANDART COST */

    var $tmp_biaya_standart_dtl_view = "sc_tmp.biaya_standart_dtl";
    var $tmp_biaya_standart_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_biaya_standart_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_biaya_standart_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_biaya_standart_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->tmp_biaya_standart_dtl_view_column as $mrp)
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

                if(count($this->tmp_biaya_standart_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_biaya_standart_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_biaya_standart_dtl_view_order))
        {
            $order = $this->tmp_biaya_standart_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_biaya_standart_dtl_view($docno){
        $builder = $this->_get_query_tmp_biaya_standart_dtl($docno);
        ////$this->_get_query_tmp_biaya_standart_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_biaya_standart_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_biaya_standart_dtl($docno);
        ////$this->_get_query_tmp_biaya_standart_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_biaya_standart_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_biaya_standart_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_biaya_standart_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_biaya_standart_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* STANDART COST MST TEMPORARY STANDART COST */

    var $mst_biaya_standart_dtl_view = "sc_mst.biaya_standart_dtl";
    var $mst_biaya_standart_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $mst_biaya_standart_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_mst_biaya_standart_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->mst_biaya_standart_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->mst_biaya_standart_dtl_view_column as $mrp)
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

                if(count($this->mst_biaya_standart_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->mst_biaya_standart_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->mst_biaya_standart_dtl_view_order))
        {
            $order = $this->mst_biaya_standart_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_mst_biaya_standart_dtl_view($docno){
        $builder = $this->_get_query_mst_biaya_standart_dtl($docno);
        ////$this->_get_query_mst_biaya_standart_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function mst_biaya_standart_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_mst_biaya_standart_dtl($docno);
        ////$this->_get_query_mst_biaya_standart_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function mst_biaya_standart_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_mst_biaya_standart_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_mst_biaya_standart_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_mst_biaya_standart_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    /******************************************************* BOM BUILD OF MATERIAL  ****************************************************************************************/

    var $trx_bom_mst_view = "(select A.*,
    z.uraian as nmstatus,kdtrx,
    z.jenistrx,
    b.nmbranch,
    n.nmbarang,
    z.uraian as nmstatus 
    from sc_trx.bom_mst a 
    left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.3' and 
    trim(a.status)=trim(z.kdtrx)
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.mbarang n on a.idbarang_jadi=n.idbarang
    ) as x";
    var $trx_bom_mst_view_column = array('docno','docdate','activatedate');
    var $trx_bom_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_trx_bom_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_bom_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_bom_mst_view_column as $mrpgroup)
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

                if(count($this->trx_bom_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_bom_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_bom_mst_view_order))
        {
            $order = $this->trx_bom_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_bom_mst_view(){
        $builder = $this->_get_trx_bom_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_bom_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_bom_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_bom_mst_view_count_all()
    {
        $builder = $this->_get_trx_bom_mst();
        return $builder->countAllResults();
    }
    public function get_trx_bom_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_bom_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_trx_bom_mst($param)
    {
        return $this->db->query("select * from sc_trx.bom_mst where docno is not null $param");
    }
    function q_trx_bom_dtl($param)
    {
        return $this->db->query("select * from sc_trx.bom_dtl where docno is not null $param");
    }

    function q_tmp_bom_mst($param)
    {
        return $this->db->query("select * from sc_tmp.bom_mst where docno is not null $param");
    }

    function q_tmp_bom_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.bom_dtl where docno is not null $param");
    }


    //MATERIAL


    var $tmp_bom_material_dtl_view = "sc_tmp.bom_dtl";
    var $tmp_bom_material_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_bom_material_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_bom_material_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_bom_material_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->tmp_bom_material_dtl_view_column as $mrp)
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

                if(count($this->tmp_bom_material_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_bom_material_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_bom_material_dtl_view_order))
        {
            $order = $this->tmp_bom_material_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_bom_material_dtl_view($docno){
        $builder = $this->_get_query_tmp_bom_material_dtl($docno);
        ////$this->_get_query_tmp_bom_material_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_bom_material_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_bom_material_dtl($docno);
        ////$this->_get_query_tmp_bom_material_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_bom_material_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_bom_material_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_bom_material_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_bom_material_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_bom_material_dtl_view = "sc_trx.bom_dtl";
    var $trx_bom_material_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_bom_material_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_bom_material_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_bom_material_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->trx_bom_material_dtl_view_column as $mrp)
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

                if(count($this->trx_bom_material_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_bom_material_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_bom_material_dtl_view_order))
        {
            $order = $this->trx_bom_material_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_bom_material_dtl_view($docno){
        $builder = $this->_get_query_trx_bom_material_dtl($docno);
        ////$this->_get_query_trx_bom_material_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_bom_material_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_bom_material_dtl($docno);
        ////$this->_get_query_trx_bom_material_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_bom_material_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_bom_material_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_bom_material_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_bom_material_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //COST


    var $tmp_bom_cost_dtl_view = "sc_tmp.bom_dtl";
    var $tmp_bom_cost_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_bom_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_bom_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_bom_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'COST'");
        foreach ($this->tmp_bom_cost_dtl_view_column as $mrp)
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

                if(count($this->tmp_bom_cost_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_bom_cost_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_bom_cost_dtl_view_order))
        {
            $order = $this->tmp_bom_cost_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_bom_cost_dtl_view($docno){
        $builder = $this->_get_query_tmp_bom_cost_dtl($docno);
        ////$this->_get_query_tmp_bom_cost_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_bom_cost_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_bom_cost_dtl($docno);
        ////$this->_get_query_tmp_bom_cost_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_bom_cost_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_bom_cost_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_bom_cost_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_bom_cost_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_bom_cost_dtl_view = "sc_trx.bom_dtl";
    var $trx_bom_cost_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_bom_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_bom_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_bom_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'COST'");
        foreach ($this->trx_bom_cost_dtl_view_column as $mrp)
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

                if(count($this->trx_bom_cost_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_bom_cost_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_bom_cost_dtl_view_order))
        {
            $order = $this->trx_bom_cost_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_bom_cost_dtl_view($docno){
        $builder = $this->_get_query_trx_bom_cost_dtl($docno);
        ////$this->_get_query_trx_bom_cost_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_bom_cost_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_bom_cost_dtl($docno);
        ////$this->_get_query_trx_bom_cost_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_bom_cost_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_bom_cost_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_bom_cost_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_bom_cost_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //WIP


    var $tmp_bom_wip_dtl_view = "sc_tmp.bom_dtl";
    var $tmp_bom_wip_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_bom_wip_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_bom_wip_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_bom_wip_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'WIP'");
        foreach ($this->tmp_bom_wip_dtl_view_column as $mrp)
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

                if(count($this->tmp_bom_wip_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_bom_wip_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_bom_wip_dtl_view_order))
        {
            $order = $this->tmp_bom_wip_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_bom_wip_dtl_view($docno){
        $builder = $this->_get_query_tmp_bom_wip_dtl($docno);
        ////$this->_get_query_tmp_bom_wip_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_bom_wip_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_bom_wip_dtl($docno);
        ////$this->_get_query_tmp_bom_wip_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_bom_wip_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_bom_wip_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_bom_wip_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_bom_wip_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_bom_wip_dtl_view = "sc_trx.bom_dtl";
    var $trx_bom_wip_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_bom_wip_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_bom_wip_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_bom_wip_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'WIP'");
        foreach ($this->trx_bom_wip_dtl_view_column as $mrp)
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

                if(count($this->trx_bom_wip_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_bom_wip_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_bom_wip_dtl_view_order))
        {
            $order = $this->trx_bom_wip_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_bom_wip_dtl_view($docno){
        $builder = $this->_get_query_trx_bom_wip_dtl($docno);
        ////$this->_get_query_trx_bom_wip_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_bom_wip_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_bom_wip_dtl($docno);
        ////$this->_get_query_trx_bom_wip_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_bom_wip_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_bom_wip_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_bom_wip_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_bom_wip_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }






    

    /******************************************************* WO WORKING ORDER  ****************************************************************************************/

    var $trx_workingorder_mst_view = "(select A.*,
    z.uraian as nmstatus,kdtrx,
    z.jenistrx,
    b.nmbranch,
    c.nmcustomer,
    c.alamat_kantor as alamatcust,
    d.namakotakab AS nmkota,
    z.uraian as nmstatus 
    from sc_trx.workingorder_mst a 
    left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.4' and 
    trim(a.status)=trim(z.kdtrx)
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.customer c on a.kdcustomer=c.kdcustomer
    left outer join sc_mst.kotakab d on c.kota_kantor=d.kodekotakab
    ) as x";
    var $trx_workingorder_mst_view_column = array('docno','docdate','activatedate');
    var $trx_workingorder_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_trx_workingorder_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_workingorder_mst_view_column as $mrpgroup)
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

                if(count($this->trx_workingorder_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_workingorder_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_workingorder_mst_view_order))
        {
            $order = $this->trx_workingorder_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_workingorder_mst_view(){
        $builder = $this->_get_trx_workingorder_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_workingorder_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_workingorder_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_workingorder_mst_view_count_all()
    {
        $builder = $this->_get_trx_workingorder_mst();
        return $builder->countAllResults();
    }
    public function get_trx_workingorder_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_workingorder_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_trx_workingorder_mst($param)
    {
        return $this->db->query("select * from sc_trx.workingorder_mst where docno is not null $param");
    }
    function q_trx_workingorder_bom_dtl($param)
    {
        return $this->db->query("select * from sc_trx.workingorder_bom_dtl where docno is not null $param");
    }

    function q_tmp_workingorder_mst($param)
    {
        return $this->db->query("select * from sc_tmp.workingorder_mst where docno is not null $param");
    }

    function q_tmp_workingorder_bom_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.workingorder_bom_dtl where docno is not null $param");
    }


    //BOM WO

    

    var $tmp_workingorder_bom_mst_view = "sc_tmp.workingorder_bom_mst";
    var $tmp_workingorder_bom_mst_view_column = array('urut','kdcustomer','nmcustomer','unit','actualcost','lastcost','newcost');
    var $tmp_workingorder_bom_mst_view_order = array("docno" => 'desc'); // default order
    private function _get_query_tmp_workingorder_bom_mst($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_workingorder_bom_mst_view);
        $builder->orderBy('docno');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->tmp_workingorder_bom_mst_view_column as $mrp)
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

                if(count($this->tmp_workingorder_bom_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_workingorder_bom_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_workingorder_bom_mst_view_order))
        {
            $order = $this->tmp_workingorder_bom_mst_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_workingorder_bom_mst_view($docno){
        $builder = $this->_get_query_tmp_workingorder_bom_mst($docno);
        ////$this->_get_query_tmp_workingorder_bom_mst($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_workingorder_bom_mst_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_bom_mst($docno);
        ////$this->_get_query_tmp_workingorder_bom_mst($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_workingorder_bom_mst_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_bom_mst($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_workingorder_bom_mst_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_workingorder_bom_mst($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    var $trx_workingorder_bom_mst_view = "sc_trx.workingorder_bom_mst";
    var $trx_workingorder_bom_mst_view_column = array('urut','kdcustomer','nmcustomer','unit','actualcost','lastcost','newcost');
    var $trx_workingorder_bom_mst_view_order = array("docno" => 'desc'); // default order
    private function _get_query_trx_workingorder_bom_mst($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_bom_mst_view);
        $builder->orderBy('docno');

        $i = 0;

        $builder->where("docref = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->trx_workingorder_bom_mst_view_column as $mrp)
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

                if(count($this->trx_workingorder_bom_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_workingorder_bom_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_workingorder_bom_mst_view_order))
        {
            $order = $this->trx_workingorder_bom_mst_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_workingorder_bom_mst_view($docno){
        $builder = $this->_get_query_trx_workingorder_bom_mst($docno);
        ////$this->_get_query_trx_workingorder_bom_mst($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_workingorder_bom_mst_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_workingorder_bom_mst($docno);
        ////$this->_get_query_trx_workingorder_bom_mst($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_workingorder_bom_mst_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_workingorder_bom_mst($docno);
        return $builder->countAllResults();
    }
    public function get_trx_workingorder_bom_mst_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_workingorder_bom_mst($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    //MATERIAL


    var $tmp_workingorder_material_dtl_view = "sc_tmp.workingorder_bom_dtl";
    var $tmp_workingorder_material_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_workingorder_material_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_workingorder_material_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_workingorder_material_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->tmp_workingorder_material_dtl_view_column as $mrp)
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

                if(count($this->tmp_workingorder_material_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_workingorder_material_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_workingorder_material_dtl_view_order))
        {
            $order = $this->tmp_workingorder_material_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_workingorder_material_dtl_view($docno){
        $builder = $this->_get_query_tmp_workingorder_material_dtl($docno);
        ////$this->_get_query_tmp_workingorder_material_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_workingorder_material_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_material_dtl($docno);
        ////$this->_get_query_tmp_workingorder_material_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_workingorder_material_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_material_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_workingorder_material_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_workingorder_material_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_workingorder_material_dtl_view = "sc_trx.workingorder_bom_dtl";
    var $trx_workingorder_material_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_workingorder_material_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_workingorder_material_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_material_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->trx_workingorder_material_dtl_view_column as $mrp)
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

                if(count($this->trx_workingorder_material_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_workingorder_material_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_workingorder_material_dtl_view_order))
        {
            $order = $this->trx_workingorder_material_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_workingorder_material_dtl_view($docno){
        $builder = $this->_get_query_trx_workingorder_material_dtl($docno);
        ////$this->_get_query_trx_workingorder_material_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_workingorder_material_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_workingorder_material_dtl($docno);
        ////$this->_get_query_trx_workingorder_material_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_workingorder_material_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_workingorder_material_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_workingorder_material_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_workingorder_material_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //COST


    var $tmp_workingorder_cost_dtl_view = "sc_tmp.workingorder_bom_dtl";
    var $tmp_workingorder_cost_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_workingorder_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_workingorder_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_workingorder_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'COST'");
        foreach ($this->tmp_workingorder_cost_dtl_view_column as $mrp)
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

                if(count($this->tmp_workingorder_cost_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_workingorder_cost_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_workingorder_cost_dtl_view_order))
        {
            $order = $this->tmp_workingorder_cost_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_workingorder_cost_dtl_view($docno){
        $builder = $this->_get_query_tmp_workingorder_cost_dtl($docno);
        ////$this->_get_query_tmp_workingorder_cost_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_workingorder_cost_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_cost_dtl($docno);
        ////$this->_get_query_tmp_workingorder_cost_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_workingorder_cost_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_cost_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_workingorder_cost_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_workingorder_cost_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_workingorder_cost_dtl_view = "sc_trx.workingorder_bom_dtl";
    var $trx_workingorder_cost_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_workingorder_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_workingorder_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'COST'");
        foreach ($this->trx_workingorder_cost_dtl_view_column as $mrp)
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

                if(count($this->trx_workingorder_cost_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_workingorder_cost_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_workingorder_cost_dtl_view_order))
        {
            $order = $this->trx_workingorder_cost_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_workingorder_cost_dtl_view($docno){
        $builder = $this->_get_query_trx_workingorder_cost_dtl($docno);
        ////$this->_get_query_trx_workingorder_cost_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_workingorder_cost_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_workingorder_cost_dtl($docno);
        ////$this->_get_query_trx_workingorder_cost_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_workingorder_cost_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_workingorder_cost_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_workingorder_cost_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_workingorder_cost_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    //WIP


    var $tmp_workingorder_wip_dtl_view = "sc_tmp.workingorder_bom_dtl";
    var $tmp_workingorder_wip_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_workingorder_wip_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_workingorder_wip_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_workingorder_wip_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'WIP'");
        foreach ($this->tmp_workingorder_wip_dtl_view_column as $mrp)
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

                if(count($this->tmp_workingorder_wip_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_workingorder_wip_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_workingorder_wip_dtl_view_order))
        {
            $order = $this->tmp_workingorder_wip_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_workingorder_wip_dtl_view($docno){
        $builder = $this->_get_query_tmp_workingorder_wip_dtl($docno);
        ////$this->_get_query_tmp_workingorder_wip_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_workingorder_wip_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_wip_dtl($docno);
        ////$this->_get_query_tmp_workingorder_wip_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_workingorder_wip_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_workingorder_wip_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_workingorder_wip_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_workingorder_wip_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_workingorder_wip_dtl_view = "sc_trx.workingorder_bom_dtl";
    var $trx_workingorder_wip_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_workingorder_wip_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_workingorder_wip_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_wip_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        $builder->where("doctype_detail = 'WIP'");
        foreach ($this->trx_workingorder_wip_dtl_view_column as $mrp)
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

                if(count($this->trx_workingorder_wip_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_workingorder_wip_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_workingorder_wip_dtl_view_order))
        {
            $order = $this->trx_workingorder_wip_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_workingorder_wip_dtl_view($docno){
        $builder = $this->_get_query_trx_workingorder_wip_dtl($docno);
        ////$this->_get_query_trx_workingorder_wip_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_workingorder_wip_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_workingorder_wip_dtl($docno);
        ////$this->_get_query_trx_workingorder_wip_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_workingorder_wip_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_workingorder_wip_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_workingorder_wip_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_workingorder_wip_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }






    // ================================== WORK ORDER EXECUTION ==================================================
    
    
    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_woe_view = "sc_trx.woe";
    var $t_woe_view_column = array('docno','docref','description');
    var $t_woe_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_woe()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_woe_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_woe_view_column as $mrp)
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

                if(count($this->t_woe_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo woest column 0
                $builder->orderBy($this->t_woe_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_woe_view_order))
        {
            $order = $this->t_woe_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_woe_view(){
        $builder = $this->_get_query_t_woe();
        ////$this->_get_query_t_woe();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_woe_view_count_filtered()
    {
        $builder = $this->_get_query_t_woe();
        ////$this->_get_query_t_woe();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_woe_view_count_all()
    {
        $builder = $this->_get_query_t_woe();
        return $builder->countAllResults();
    }
    public function get_t_woe_view_by_id($id)
    {
        $builder = $this->_get_query_t_woe();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_woe_dtl_view = "sc_trx.woe_dtl";
    var $t_woe_dtl_view_column = array('idurut','wono','bomno','idbarang_jadi','nmbarang_jadi','buildfor','descriptionpp');
    var $t_woe_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_woe_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_woe_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_woe_dtl_view_column as $mrp)
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

                if(count($this->t_woe_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo woest column 0
                $builder->orderBy($this->t_woe_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_woe_dtl_view_order))
        {
            $order = $this->t_woe_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_woe_dtl_view($docnoParam){
        $builder = $this->_get_query_t_woe_dtl($docnoParam);
        ////$this->_get_query_t_woe_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_woe_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_woe_dtl($docnoParam);
        ////$this->_get_query_t_woe_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_woe_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_woe_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_woe_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_woe_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_woe_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.woe where docno is not null $param");
    }

    public function q_woe_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.woe_dtl where docno is not null $param order by idurut desc");
    }


    public function q_woe_master($param)
    {
        return $this->db->query("select * from sc_trx.woe where docno is not null $param");
    }

    public function q_woe_dtl($param)
    {
        return $this->db->query("select * from sc_trx.woe_dtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_woe_dtl_temp_view = "sc_tmp.woe_dtl";
    var $t_woe_dtl_temp_view_column = array('idurut','docnopo','idbarang','nmbarang','unit','qty','descriptionpo','descriptionpp');
    var $t_woe_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_woe_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_woe_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_woe_dtl_temp_view_column as $mrp)
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

                if(count($this->t_woe_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo woest column 0
                $builder->orderBy($this->t_woe_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_woe_dtl_temp_view_order))
        {
            $order = $this->t_woe_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_woe_dtl_temp_view($docno){
        $builder = $this->_get_query_t_woe_dtl_temp($docno);
        ////$this->_get_query_t_woe_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_woe_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_woe_dtl_temp($docno);
        ////$this->_get_query_t_woe_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_woe_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_woe_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_woe_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_woe_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_woe_view = "sc_trx.woe";
    var $t_front_woe_view = "(select a.*, 
    b.nmbranch,
    z.uraian as status_desc
    from sc_trx.woe a 
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.R.A.5') as x";
    var $t_front_woe_view_column = array('docno','docdate','bagian','wono','bomno','idbarang_jadi','nmbarang_jadi','buildfor','keterangan','nmbranch');
    var $t_front_woe_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_woe()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_woe_view);
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
        foreach ($this->t_front_woe_view_column as $mrpgroup)
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

                if(count($this->t_front_woe_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo woest column 0
                $builder->orderBy($this->t_front_woe_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_woe_view_order))
        {
            $order = $this->t_front_woe_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_woe_view(){
        $builder = $this->_get_query_front_woe();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_woe_view_count_filtered()
    {
        $builder = $this->_get_query_front_woe();
        ////$this->_get_query_t_woe();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_woe_view_count_all()
    {
        $builder = $this->_get_query_front_woe();
        return $builder->countAllResults();
    }
    public function get_t_front_woe_view_by_id($id)
    {
        $builder = $this->_get_query_front_woe();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }












    /******************************************************* MATERIAL RELEASE  ****************************************************************************************/

    var $trx_materialrelease_mst_view = "(select A.*,
    z.uraian as nmstatus,kdtrx,
    z.jenistrx,
    b.nmbranch,
    n.nmbarang,
    z.uraian as nmstatus 
    from sc_trx.materialrelease_mst a 
    left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.8' and 
    trim(a.status)=trim(z.kdtrx)
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.mbarang n on a.idbarang_jadi=n.idbarang
    ) as x";
    var $trx_materialrelease_mst_view_column = array('docno','docdate','activatedate');
    var $trx_materialrelease_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_trx_materialrelease_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_materialrelease_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_materialrelease_mst_view_column as $mrpgroup)
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

                if(count($this->trx_materialrelease_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_materialrelease_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_materialrelease_mst_view_order))
        {
            $order = $this->trx_materialrelease_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_materialrelease_mst_view(){
        $builder = $this->_get_trx_materialrelease_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_materialrelease_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_materialrelease_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_materialrelease_mst_view_count_all()
    {
        $builder = $this->_get_trx_materialrelease_mst();
        return $builder->countAllResults();
    }
    public function get_trx_materialrelease_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_materialrelease_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_trx_materialrelease_mst($param)
    {
        return $this->db->query("select * from sc_trx.materialrelease_mst where docno is not null $param");
    }
    function q_trx_materialrelease_dtl($param)
    {
        return $this->db->query("select * from sc_trx.materialrelease_dtl where docno is not null $param");
    }

    function q_tmp_materialrelease_mst($param)
    {
        return $this->db->query("select * from sc_tmp.materialrelease_mst where docno is not null $param");
    }

    function q_tmp_materialrelease_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.materialrelease_dtl where docno is not null $param");
    }


    //MATERIAL


    var $tmp_materialrelease_dtl_view = "sc_tmp.materialrelease_dtl";
    var $tmp_materialrelease_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_materialrelease_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_materialrelease_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_materialrelease_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->tmp_materialrelease_dtl_view_column as $mrp)
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

                if(count($this->tmp_materialrelease_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_materialrelease_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_materialrelease_dtl_view_order))
        {
            $order = $this->tmp_materialrelease_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_materialrelease_dtl_view($docno){
        $builder = $this->_get_query_tmp_materialrelease_dtl($docno);
        ////$this->_get_query_tmp_materialrelease_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_materialrelease_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_materialrelease_dtl($docno);
        ////$this->_get_query_tmp_materialrelease_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_materialrelease_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_materialrelease_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_materialrelease_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_materialrelease_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_materialrelease_dtl_view = "sc_trx.materialrelease_dtl";
    var $trx_materialrelease_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_materialrelease_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_materialrelease_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_materialrelease_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->trx_materialrelease_dtl_view_column as $mrp)
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

                if(count($this->trx_materialrelease_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_materialrelease_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_materialrelease_dtl_view_order))
        {
            $order = $this->trx_materialrelease_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_materialrelease_dtl_view($docno){
        $builder = $this->_get_query_trx_materialrelease_dtl($docno);
        ////$this->_get_query_trx_materialrelease_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_materialrelease_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_materialrelease_dtl($docno);
        ////$this->_get_query_trx_materialrelease_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_materialrelease_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_materialrelease_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_materialrelease_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_materialrelease_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }









    /******************************************************* PENERIMAAN BARANG PRODUKSI  ****************************************************************************************/

    var $trx_penerimaanbp_mst_view = "(select A.*,
    z.uraian as nmstatus,kdtrx,
    z.jenistrx,
    b.nmbranch,
    n.nmbarang,
    z.uraian as nmstatus 
    from sc_trx.penerimaanbp_mst a 
    left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.8' and 
    trim(a.status)=trim(z.kdtrx)
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.mbarang n on a.idbarang_jadi=n.idbarang
    ) as x";
    var $trx_penerimaanbp_mst_view_column = array('docno','docdate','activatedate');
    var $trx_penerimaanbp_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_trx_penerimaanbp_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_penerimaanbp_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_penerimaanbp_mst_view_column as $mrpgroup)
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

                if(count($this->trx_penerimaanbp_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_penerimaanbp_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_penerimaanbp_mst_view_order))
        {
            $order = $this->trx_penerimaanbp_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_penerimaanbp_mst_view(){
        $builder = $this->_get_trx_penerimaanbp_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_penerimaanbp_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_penerimaanbp_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_penerimaanbp_mst_view_count_all()
    {
        $builder = $this->_get_trx_penerimaanbp_mst();
        return $builder->countAllResults();
    }
    public function get_trx_penerimaanbp_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_penerimaanbp_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_trx_penerimaanbp_mst($param)
    {
        return $this->db->query("select * from sc_trx.penerimaanbp_mst where docno is not null $param");
    }
    function q_trx_penerimaanbp_dtl($param)
    {
        return $this->db->query("select * from sc_trx.penerimaanbp_dtl where docno is not null $param");
    }

    function q_tmp_penerimaanbp_mst($param)
    {
        return $this->db->query("select * from sc_tmp.penerimaanbp_mst where docno is not null $param");
    }

    function q_tmp_penerimaanbp_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.penerimaanbp_dtl where docno is not null $param");
    }


    //MATERIAL


    var $tmp_penerimaanbp_dtl_view = "sc_tmp.penerimaanbp_dtl";
    var $tmp_penerimaanbp_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_penerimaanbp_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_penerimaanbp_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_penerimaanbp_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->tmp_penerimaanbp_dtl_view_column as $mrp)
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

                if(count($this->tmp_penerimaanbp_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_penerimaanbp_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_penerimaanbp_dtl_view_order))
        {
            $order = $this->tmp_penerimaanbp_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_penerimaanbp_dtl_view($docno){
        $builder = $this->_get_query_tmp_penerimaanbp_dtl($docno);
        ////$this->_get_query_tmp_penerimaanbp_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_penerimaanbp_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_penerimaanbp_dtl($docno);
        ////$this->_get_query_tmp_penerimaanbp_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_penerimaanbp_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_penerimaanbp_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_penerimaanbp_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_penerimaanbp_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_penerimaanbp_dtl_view = "sc_trx.penerimaanbp_dtl";
    var $trx_penerimaanbp_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_penerimaanbp_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_penerimaanbp_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_penerimaanbp_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->trx_penerimaanbp_dtl_view_column as $mrp)
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

                if(count($this->trx_penerimaanbp_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_penerimaanbp_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_penerimaanbp_dtl_view_order))
        {
            $order = $this->trx_penerimaanbp_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_penerimaanbp_dtl_view($docno){
        $builder = $this->_get_query_trx_penerimaanbp_dtl($docno);
        ////$this->_get_query_trx_penerimaanbp_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_penerimaanbp_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_penerimaanbp_dtl($docno);
        ////$this->_get_query_trx_penerimaanbp_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_penerimaanbp_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_penerimaanbp_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_penerimaanbp_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_penerimaanbp_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }






    

    /******************************************************* BIAYA PRODUKSI NON MATERIAL  ****************************************************************************************/

    var $trx_bpnm_mst_view = "(select A.*,
    z.uraian as nmstatus,kdtrx,
    z.jenistrx,
    b.nmbranch,
    n.nmbarang,
    z.uraian as nmstatus 
    from sc_trx.bpnm_mst a 
    left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.8' and 
    trim(a.status)=trim(z.kdtrx)
    left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    left outer join sc_mst.mbarang n on a.idbarang_jadi=n.idbarang
    ) as x";
    var $trx_bpnm_mst_view_column = array('docno','docdate','activatedate');
    var $trx_bpnm_mst_view_order = array('docno' => 'desc'); // default order
    private function _get_trx_bpnm_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_bpnm_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_bpnm_mst_view_column as $mrpgroup)
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

                if(count($this->trx_bpnm_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_bpnm_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_bpnm_mst_view_order))
        {
            $order = $this->trx_bpnm_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_bpnm_mst_view(){
        $builder = $this->_get_trx_bpnm_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_bpnm_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_bpnm_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_bpnm_mst_view_count_all()
    {
        $builder = $this->_get_trx_bpnm_mst();
        return $builder->countAllResults();
    }
    public function get_trx_bpnm_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_bpnm_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_trx_bpnm_mst($param)
    {
        return $this->db->query("select * from sc_trx.bpnm_mst where docno is not null $param");
    }
    function q_trx_bpnm_dtl($param)
    {
        return $this->db->query("select * from sc_trx.bpnm_dtl where docno is not null $param");
    }

    function q_tmp_bpnm_mst($param)
    {
        return $this->db->query("select * from sc_tmp.bpnm_mst where docno is not null $param");
    }

    function q_tmp_bpnm_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.bpnm_dtl where docno is not null $param");
    }


    //MATERIAL


    var $tmp_bpnm_dtl_view = "sc_tmp.bpnm_dtl";
    var $tmp_bpnm_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $tmp_bpnm_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_bpnm_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_bpnm_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->tmp_bpnm_dtl_view_column as $mrp)
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

                if(count($this->tmp_bpnm_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_bpnm_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_bpnm_dtl_view_order))
        {
            $order = $this->tmp_bpnm_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_bpnm_dtl_view($docno){
        $builder = $this->_get_query_tmp_bpnm_dtl($docno);
        ////$this->_get_query_tmp_bpnm_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_bpnm_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_bpnm_dtl($docno);
        ////$this->_get_query_tmp_bpnm_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_bpnm_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_bpnm_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_bpnm_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_bpnm_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $trx_bpnm_dtl_view = "sc_trx.bpnm_dtl";
    var $trx_bpnm_dtl_view_column = array('urut','idbarang','nmbarang','unit','actualcost','lastcost','newcost');
    var $trx_bpnm_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_bpnm_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_bpnm_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        // $builder->where("doctype_detail = 'MATERIAL'");
        foreach ($this->trx_bpnm_dtl_view_column as $mrp)
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

                if(count($this->trx_bpnm_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_bpnm_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_bpnm_dtl_view_order))
        {
            $order = $this->trx_bpnm_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_bpnm_dtl_view($docno){
        $builder = $this->_get_query_trx_bpnm_dtl($docno);
        ////$this->_get_query_trx_bpnm_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_bpnm_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_bpnm_dtl($docno);
        ////$this->_get_query_trx_bpnm_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_bpnm_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_bpnm_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_bpnm_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_bpnm_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }
}