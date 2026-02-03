<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Tax extends Model
{

//sc_mst.tax_mst
    var $t_tax_mst_view = "sc_mst.tax_mst";
    var $t_tax_mst_view_column = array('idtax','nmtax','chold');
    var $t_tax_mst_view_order = array("chold" => 'asc'); // default order
    private function _get_query_t_tax_mst()
    {

        $builder = $this->db->table($this->t_tax_mst_view);
        $i = 0;
        foreach ($this->t_tax_mst_view_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_tax_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tax_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tax_mst_view_order))
        {
            $order = $this->t_tax_mst_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_tax_mst_view(){
        $builder = $this->_get_query_t_tax_mst();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tax_mst_view_count_filtered()
    {
        $builder = $this->_get_query_t_tax_mst();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tax_mst_view_count_all()
    {
        $builder = $this->_get_query_t_tax_mst();
        return $builder->countAllResults();
    }
    public function get_t_tax_mst_view_by_id($id)
    {
        $builder = $this->_get_query_t_tax_mst();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_master_tax_mst($param){
        return $this->db->query("select * from sc_mst.tax_mst where id is not null $param");
    }


    /* GET TAX DETAIL  **************************************************************************************************/
    var $t_tax_dtl_view = "sc_mst.tax_dtl";
    var $t_tax_dtl_view_column = array('id','idtax','idgrouptax','nmgrouptax','kodepajak','chold');
    var $t_tax_dtl_view_order = array("chold" => 'asc'); // default order
    private function _get_query_t_tax_dtl()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();

        $builder = $this->db->table($this->t_tax_dtl_view);

        if ($this->request->getPost('id')) {

            $id = trim($this->request->getPost('id'));

            $builder->where("idtax = (
        SELECT idtax 
        FROM sc_mst.tax_mst 
        WHERE id = ".$this->db->escape($id)."
        LIMIT 1
    )");
        } else {
            $id = trim($this->request->getPost('id'));
            $builder->where("idtax='".$id."'");
        }



        $i = 0;
        foreach ($this->t_tax_dtl_view_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_tax_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tax_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tax_dtl_view_order))
        {
            $order = $this->t_tax_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_tax_dtl_view(){
        $builder = $this->_get_query_t_tax_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tax_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_tax_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tax_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_tax_dtl();
        return $builder->countAllResults();
    }
    public function get_t_tax_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_tax_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }



}