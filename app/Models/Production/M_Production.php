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


    function q_mst_standart_cost_dtl($param)
    {
        return $this->db->query("select * from sc_mst.standart_cost_mst where docno is not null $param");
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
    var $tmp_standart_cost_dtl_view_order = array("urut" => 'desc'); // default order
    private function _get_query_tmp_standart_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_standart_cost_dtl_view);
        $builder->orderBy('urut');

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



}