<?php

namespace App\Models\Sales;

use CodeIgniter\Model;

class M_Sales extends Model
{

var $t_Task_view = "(select a.* from sc_trx.tasksales a) as x";

var $t_Task_view_column = array('id');
var $t_Task_view_order = array("id" => 'desc'); // Default order

private function _get_query_t_Task($nama = null, $role = null, $bagian = null)
{
    $builder = $this->db->table($this->t_Task_view);

    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_Task_view_column as $Capex) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Capex) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Capex) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_Task_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_Task_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_Task_view_order)) {
        foreach ($this->t_Task_view_order as $key => $Capex) {
            $builder->orderBy($key, $Capex);
        }
    }

    return $builder;
}


    function get_t_Task_view($nama = null, $role = null, $bagian = null){
        $builder = $this->_get_query_t_Task($nama, $role, $bagian);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    function get_list_task_board($nama = null, $role = null, $bagian = null)
    {
        $builder = $this->db->table($this->t_Task_view);

        // Tambahkan filter jika perlu (misalnya berdasarkan bagian atau role)
        // $builder->where('assignee', $nama); // contoh jika hanya ambil yang ditugaskan ke user

        $builder->orderBy('inputdate', 'desc');
        $query = $builder->get();
        return $query->getResult();
    }


    function t_Task_view_count_filtered($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_Task($nama, $role, $bagian);
        ////$this->_get_query_t_Task($nama, $role, $bagian);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_Task_view_count_all($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_Task($nama, $role, $bagian);
        return $builder->countAllResults();
    }
    public function get_t_Task_view_by_id($id,$nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_Task($nama, $role, $bagian);
        $builder->where('idsteelgrade',$id);
        $query = $builder->get();
        return $query->getRow();
    }
    

var $t_customer_view = "(select a.* from sc_mst.customer a) as x";

var $t_customer_view_column = array('docno, kodecust, nama_customer');
var $t_customer_view_order = array("created_at" => 'desc'); // Default order

private function _get_query_t_customer($nama = null, $role = null, $bagian = null)
{
    $builder = $this->db->table($this->t_customer_view);

    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_customer_view_column as $Cust) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Cust) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Cust) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_customer_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_customer_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_customer_view_order)) {
        foreach ($this->t_customer_view_order as $key => $Cust) {
            $builder->orderBy($key, $Cust);
        }
    }

    return $builder;
}


    function get_t_customer_view($nama = null, $role = null, $bagian = null){
        $builder = $this->_get_query_t_customer($nama, $role, $bagian);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_customer_view_count_filtered($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_customer($nama, $role, $bagian);
        ////$this->_get_query_t_customer($nama, $role, $bagian);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_customer_view_count_all($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_customer($nama, $role, $bagian);
        return $builder->countAllResults();
    }
    public function get_t_customer_view_by_id($id,$nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_customer($nama, $role, $bagian);
        $builder->where('idsteelgrade',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mstcustomer($param){
        return $this->db->query("SELECT * FROM (SELECT a.* FROM sc_mst.customer a) AS x WHERE docno IS NOT NULL $param");
    }



    
var $t_bank_view = "(select a.* from sc_mst.banks a) as x";

var $t_bank_view_column = array('id');
var $t_bank_view_order = array("created_at" => 'desc'); // Default order

private function _get_query_t_bank($nama = null, $role = null, $bagian = null)
{
    $builder = $this->db->table($this->t_bank_view);

    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_bank_view_column as $Bank) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_bank_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_bank_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_bank_view_order)) {
        foreach ($this->t_bank_view_order as $key => $Bank) {
            $builder->orderBy($key, $Bank);
        }
    }

    return $builder;
}


    function get_t_bank_view($nama = null, $role = null, $bagian = null){
        $builder = $this->_get_query_t_bank($nama, $role, $bagian);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_bank_view_count_filtered($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_bank($nama, $role, $bagian);
        ////$this->_get_query_t_bank($nama, $role, $bagian);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_bank_view_count_all($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_bank($nama, $role, $bagian);
        return $builder->countAllResults();
    }
    public function get_t_bank_view_by_id($id,$nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_bank($nama, $role, $bagian);
        $builder->where('idsteelgrade',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mstbank($param){
        return $this->db->query("SELECT * FROM (SELECT a.* FROM sc_mst.banks a) AS x WHERE id IS NOT NULL $param");
    }
    



    
var $t_tax_view = "(select a.* from sc_mst.tax a) as x";

var $t_tax_view_column = array('idtax, nmtax, jnstax, kodetax');
var $t_tax_view_order = array("created_at" => 'desc'); // Default order

private function _get_query_t_tax($nama = null, $role = null, $bagian = null)
{
    $builder = $this->db->table($this->t_tax_view);

    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_tax_view_column as $Bank) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_tax_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_tax_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_tax_view_order)) {
        foreach ($this->t_tax_view_order as $key => $Bank) {
            $builder->orderBy($key, $Bank);
        }
    }

    return $builder;
}


    function get_t_tax_view($nama = null, $role = null, $bagian = null){
        $builder = $this->_get_query_t_tax($nama, $role, $bagian);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tax_view_count_filtered($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_tax($nama, $role, $bagian);
        ////$this->_get_query_t_tax($nama, $role, $bagian);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tax_view_count_all($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_tax($nama, $role, $bagian);
        return $builder->countAllResults();
    }
    public function get_t_tax_view_by_id($id,$nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_tax($nama, $role, $bagian);
        $builder->where('idsteelgrade',$id);    
        $query = $builder->get();
        return $query->getRow();
    }

    function q_msttax($param){
        return $this->db->query("SELECT * FROM (SELECT a.* FROM sc_mst.tax a) AS x WHERE idtax IS NOT NULL $param");
    }



    
var $t_currency_view = "(select a.* from sc_mst.currency a) as x";

var $t_currency_view_column = array('id');
var $t_currency_view_order = array("id" => 'desc'); // Default order

private function _get_query_t_currency($nama = null, $role = null, $bagian = null)
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


    function get_t_currency_view($nama = null, $role = null, $bagian = null){
        $builder = $this->_get_query_t_currency($nama, $role, $bagian);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_currency_view_count_filtered($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_currency($nama, $role, $bagian);
        ////$this->_get_query_t_currency($nama, $role, $bagian);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_currency_view_count_all($nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_currency($nama, $role, $bagian);
        return $builder->countAllResults();
    }
    public function get_t_currency_view_by_id($id,$nama, $role, $bagian)
    {
        $builder = $this->_get_query_t_currency($nama, $role, $bagian);
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





    
    
var $t_expedisi_view = "(select a.* from sc_mst.expedisi a) as x";

var $t_expedisi_view_column = array('idexpedisi');
var $t_expedisi_view_order = array("idexpedisi" => 'desc'); // Default order

private function _get_query_t_expedisi()
{
    $builder = $this->db->table($this->t_expedisi_view);

    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_expedisi_view_column as $Bank) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_expedisi_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_expedisi_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_expedisi_view_order)) {
        foreach ($this->t_expedisi_view_order as $key => $Bank) {
            $builder->orderBy($key, $Bank);
        }
    }

    return $builder;
}


    function get_t_expedisi_view(){
        $builder = $this->_get_query_t_expedisi();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_expedisi_view_count_filtered()
    {
        $builder = $this->_get_query_t_expedisi();
        ////$this->_get_query_t_expedisi();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_expedisi_view_count_all()
    {
        $builder = $this->_get_query_t_expedisi();
        return $builder->countAllResults();
    }
    public function get_t_expedisi_view_by_id($id,)
    {
        $builder = $this->_get_query_t_expedisi();
        $builder->where('idsteelgrade',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mstexpedisi($param){
        return $this->db->query("SELECT * FROM (SELECT a.* FROM sc_mst.expedisi a) AS x WHERE idexpedisi IS NOT NULL $param");
    }



     
var $t_expedisidtl_view = "(select a.* from sc_mst.expedisidtl a) as x";

var $t_expedisidtl_view_column = array('idexpedisidtl');
var $t_expedisidtl_view_order = array("idexpedisidtl" => 'desc'); // Default order

private function _get_query_t_expedisidtl($param)
{
    $builder = $this->db->table($this->t_expedisidtl_view);

    $builder->join('sc_mst.expedisi AS c', 'c.idexpedisi = x.idexpedisi', 'left');

    // Memilih kolom yang dibutuhkan
    $builder->select("x.*, c.idexpedisi AS idexpedisiparam, c.nmexpedisi");

    $builder->where('x.idexpedisi',$param);
    $this->request = \Config\Services::request();
    // ✅ Pencarian (search) di DataTables
    $i = 0;
    foreach ($this->t_expedisidtl_view_column as $Bank) {
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            if ($i === 0) {
                $builder->groupStart(); // Mulai grup pencarian
                $builder->like("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            } else {
                $builder->orLike("upper(cast(" . strtoupper($Bank) . " as varchar))", strtoupper($_POST['search']['value']));
            }

            if (count($this->t_expedisidtl_view_column) - 1 == $i) {
                $builder->groupEnd(); // Akhiri grup pencarian
            }
        }
        $i++;
    }

    // ✅ Sorting di DataTables
    if (isset($_POST['order'])) {
        if ($_POST['order']['0']['column'] != 0) {
            $builder->orderBy($this->t_expedisidtl_view_column[$_POST['order']['0']['column'] - 1], $_POST['order']['0']['dir']);
        }
    } elseif (isset($this->t_expedisidtl_view_order)) {
        foreach ($this->t_expedisidtl_view_order as $key => $Bank) {
            $builder->orderBy($key, $Bank);
        }
    }

    return $builder;
}


    function get_t_expedisidtl_view($param){
        $builder = $this->_get_query_t_expedisidtl($param);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_expedisidtl_view_count_filtered($param)
    {
        $builder = $this->_get_query_t_expedisidtl($param);
        ////$this->_get_query_t_expedisidtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_expedisidtl_view_count_all($param)
    {
        $builder = $this->_get_query_t_expedisidtl($param);
        return $builder->countAllResults();
    }
    public function get_t_expedisidtl_view_by_id($id,$param)
    {
        $builder = $this->_get_query_t_expedisidtl($param);
        $builder->where('idsteelgrade',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mstexpedisidtl($param){
        return $this->db->query("SELECT * FROM (SELECT a.* FROM sc_mst.expedisidtl a) AS x WHERE idexpedisidtl IS NOT NULL $param");
    }







    var $t_item_view = "(select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
        c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,coalesce(a.setminstock,'NO') as setminstock1,round(coalesce(a.minstock,0)) as minstock1
        from sc_mst.mbarang a 
        left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
        left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
        left outer join sc_mst.marea d on a.defarea=d.idarea
    ) as x";
    var $t_item_view_column = array('idbarang','nmbarang','expdate1','chold','nmgroup','unit','subunit');
    var $t_item_view_order = array("idbarang" => 'asc'); // default order
    private function _get_query_t_item()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $builder = $this->db->table($this->t_item_view);
        $i = 0;

        // if($this->request->getPost('idgroup')) {
        //     $ket1 = trim($this->request->getPost('idgroup'));
        //     $builder->where("idgroup ='$ket1'");
        // }


        $builder->where("tipebarang='SALES'");
        foreach ($this->t_item_view_column as $item)
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

                if(count($this->t_item_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_item_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_item_view_order))
        {
            $order = $this->t_item_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_item_view(){
        $builder = $this->_get_query_t_item();
        ////$this->_get_query_t_item();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_item_view_count_filtered()
    {
        $builder = $this->_get_query_t_item();
        ////$this->_get_query_t_item();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_item_view_count_all()
    {
        $builder = $this->_get_query_t_item();
        return $builder->countAllResults();
    }
    public function get_t_item_view_by_id($id)
    {
        $builder = $this->_get_query_t_item();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    function q_item_param($param){
        return $this->db->query("select * from (select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
            c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,coalesce(a.setminstock,'NO') as setminstock1,round(coalesce(a.minstock,0)) as minstock1
            from sc_mst.mbarang a 
            left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
            left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
            left outer join sc_mst.marea d on a.defarea=d.idarea
        ) as x where id is not null $param");
    }

    function q_autoinsert_unit(){
        return $this->db->query("insert into sc_mst.unit
(idunit,parentunit,ctype,conversion_value,chold,inputby,inputdate)
(select unit as idunit,0 as parentunit,'UNIT' as ctype,0,'NO','SYSTEM' as inputby,to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp 
from sc_mst.mbarang where unit not in (select idunit from sc_mst.unit where ctype='UNIT')
group by idunit);");
    }
    
}
