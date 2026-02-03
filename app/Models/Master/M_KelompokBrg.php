<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_KelompokBrg extends Model
{

    /* Penambahan GolBar Pada Inventory */
    // var $t_kelompokbarang_view = "sc_mst.kelompokbarang";
    var $t_kelompokbarang_view = "(select a.* from sc_mst.kelompokbarang a ) as x";
    var $t_kelompokbarang_view_column = array('idkelompokbarang','nmkelompokbarang','chold');
    var $t_kelompokbarang_view_order = array("nmkelompokbarang" => 'asc'); // default order
    private function _get_query_t_kelompokbarang()
    {

        $builder = $this->db->table($this->t_kelompokbarang_view);
        $i = 0;
        foreach ($this->t_kelompokbarang_view_column as $item)
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

                if(count($this->t_kelompokbarang_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_kelompokbarang_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_kelompokbarang_view_order))
        {
            $order = $this->t_kelompokbarang_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_kelompokbarang_view(){
        $builder = $this->_get_query_t_kelompokbarang();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_kelompokbarang_view_count_filtered()
    {
        $builder = $this->_get_query_t_kelompokbarang();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_kelompokbarang_view_count_all()
    {
        $builder = $this->_get_query_t_kelompokbarang();
        return $builder->countAllResults();
    }
    public function get_t_kelompokbarang_view_by_id($id)
    {
        $builder = $this->_get_query_t_kelompokbarang();
        $builder->where('idkelompokbarang',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_kelompokbarang($param){
        return $this->db->query("select * from sc_mst.kelompokbarang where idkelompokbarang is not null $param ");
    }


}