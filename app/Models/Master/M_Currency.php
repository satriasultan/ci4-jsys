<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Currency extends Model
{

    
var $t_currency_view = "(select a.* from sc_mst.currency a) as x";

var $t_currency_view_column = array('id','currcode','currname');
var $t_currency_view_order = array("id" => 'desc'); // Default order

private function _get_query_t_currency()
{
    $builder = $this->db->table($this->t_currency_view);

    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_currency_view_column as $Bank) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_currency_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_currency_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_currency_view_order)) {
        foreach ($this->t_currency_view_order as $key => $Bank) {
            $builder->orderBy($key, $Bank);
        }
    }

    return $builder;
}


    function get_t_currency_view(){
        $builder = $this->_get_query_t_currency();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_currency_view_count_filtered()
    {
        $builder = $this->_get_query_t_currency();
        ////$this->_get_query_t_currency();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_currency_view_count_all()
    {
        $builder = $this->_get_query_t_currency();
        return $builder->countAllResults();
    }
    public function get_t_currency_view_by_id($id,)
    {
        $builder = $this->_get_query_t_currency();
        $builder->where('idsteelgrade',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mstcurrency($param){
        return $this->db->query("SELECT * FROM (SELECT a.* FROM sc_mst.currency a) AS x WHERE id IS NOT NULL $param");
    }




    var $t_exchangerate_view = "sc_mst.exchangerate";
    var $t_exchangerate_view_column = array('nilai','exchangedate');
    var $t_exchangerate_view_order = array("id" => 'desc'); // default order
    private function _get_query_t_exchangerate($param)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_exchangerate_view . ' AS x');  // Menambahkan alias 'x' untuk tabel utama
        
        $builder->join('sc_mst.currency AS c', 'c.id = x.idcurr', 'left');

        // Memilih kolom yang dibutuhkan
        $builder->select("x.*, c.id AS idcurrparam, c.currcode, c.currname");

        // Menentukan urutan
        $builder->orderBy('x.id', 'desc');  // Pastikan urutan berdasarkan 'x.idurut'

        // Melakukan where dengan bind parameter untuk keamanan
        $builder->where("x.idcurr", $param);  // Menghindari SQL injection
        $i = 0;
        foreach ($this->t_exchangerate_view_column as $mrp)
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

                if(count($this->t_exchangerate_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_exchangerate_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_exchangerate_view_order))
        {
            $order = $this->t_exchangerate_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_exchangerate_view($param){
        $builder = $this->_get_query_t_exchangerate($param);
        ////$this->_get_query_t_exchangerate();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_exchangerate_view_count_filtered($param)
    {
        $builder = $this->_get_query_t_exchangerate($param);
        ////$this->_get_query_t_exchangerate();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_exchangerate_view_count_all($param)
    {
        $builder = $this->_get_query_t_exchangerate($param);
        return $builder->countAllResults();
    }
    public function get_t_exchangerate_view_by_id($id)
    {
        $builder = $this->_get_query_t_exchangerate($param);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }




}