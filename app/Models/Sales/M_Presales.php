<?php

namespace App\Models\Sales;

use CodeIgniter\Model;

class M_Presales extends Model
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
    



    //OFFERING

     /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_offering_view = "sc_trx.offering";
    var $t_offering_view_column = array('docno','docref','description');
    var $t_offering_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_offering()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_offering_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_offering_view_column as $mrp)
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

                if(count($this->t_offering_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_offering_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_offering_view_order))
        {
            $order = $this->t_offering_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_offering_view(){
        $builder = $this->_get_query_t_offering();
        ////$this->_get_query_t_offering();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_offering_view_count_filtered()
    {
        $builder = $this->_get_query_t_offering();
        ////$this->_get_query_t_offering();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_offering_view_count_all()
    {
        $builder = $this->_get_query_t_offering();
        return $builder->countAllResults();
    }
    public function get_t_offering_view_by_id($id)
    {
        $builder = $this->_get_query_t_offering();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_offering_dtl_view = "sc_trx.offeringdtl";
    var $t_offering_dtl_view_column = array('docno','docref','description');
    var $t_offering_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_offering_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_offering_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_offering_dtl_view_column as $mrp)
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

                if(count($this->t_offering_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_offering_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_offering_dtl_view_order))
        {
            $order = $this->t_offering_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_offering_dtl_view($docnoParam){
        $builder = $this->_get_query_t_offering_dtl($docnoParam);
        ////$this->_get_query_t_offering_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_offering_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_offering_dtl($docnoParam);
        ////$this->_get_query_t_offering_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_offering_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_offering_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_offering_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_offering_dtl($docnoParam);
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

    public function q_offering_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.offering where docno is not null $param");
    }

    public function q_offering_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.offeringdtl where docno is not null $param order by idurut desc");
    }


    public function q_offering_master($param)
    {
        return $this->db->query("select * from sc_trx.offering where docno is not null $param");
    }

    public function q_offering_dtl($param)
    {
        return $this->db->query("select * from sc_trx.offeringdtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_offering_dtl_temp_view = "sc_tmp.offeringdtl";
    var $t_offering_dtl_temp_view_column = array('docno','docref','description');
    var $t_offering_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_offering_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_offering_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_offering_dtl_temp_view_column as $mrp)
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

                if(count($this->t_offering_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_offering_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_offering_dtl_temp_view_order))
        {
            $order = $this->t_offering_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_offering_dtl_temp_view($docno){
        $builder = $this->_get_query_t_offering_dtl_temp($docno);
        ////$this->_get_query_t_offering_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_offering_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_offering_dtl_temp($docno);
        ////$this->_get_query_t_offering_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_offering_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_offering_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_offering_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_offering_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_offering_view = "sc_trx.offering";
    var $t_front_offering_view = "(select b.nama_customer as nmcust,
                                b.docno as docnocust,
                                b.alamat_kantor,
                                b.alamat_pengiriman,
                                b.alamat_penagihan,
                                b.email,
                                b.kontak_pic,
                                b.jabatanpic,
                                b.telepon,
                                b.fax,

                                a.inputby as offering_inputby,
                                a.status as offering_status,
                                trim(a.status) as trimmed_status,
                                a.* 
                        from sc_trx.offering a
                        left outer join sc_mst.customer b on a.cust = b.docno
                        ) as x";

    var $t_front_offering_view_column = array('docno','cust','description');
    var $t_front_offering_view_order = array('inputdate' => 'desc'); // default order
    private function _get_query_front_offering()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_offering_view);
        $builder->join(
            "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
            FROM sc_mst.trxtype 
            WHERE jenistrx = 'I.S.A.2' 
            ORDER BY kdtrx, uraian DESC) AS trx", 
            "COALESCE(x.offering_status, '') = COALESCE(trx.kdtrx, '')", 
            "left"
        );
        $builder->select("x.*, trx.uraian AS status_desc");
        $builder->where('offering_inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_offering_view_column as $mrpgroup)
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

                if(count($this->t_front_offering_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_offering_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_offering_view_order))
        {
            $order = $this->t_front_offering_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_offering_view(){
        $builder = $this->_get_query_front_offering();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_offering_view_count_filtered()
    {
        $builder = $this->_get_query_front_offering();
        ////$this->_get_query_t_offering();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_offering_view_count_all()
    {
        $builder = $this->_get_query_front_offering();
        return $builder->countAllResults();
    }
    public function get_t_front_offering_view_by_id($id)
    {
        $builder = $this->_get_query_front_offering();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE
    //PROFORMA INVOICE


    /* UNTUK LIST DEPAN WO*/
    /* TRX WO*/
    var $t_proforma_view = "sc_trx.proforma";
    var $t_proforma_view_column = array('docno','docref','description');
    var $t_proforma_view_order = array("docname" => 'desc'); // default order
    private function _get_query_t_proforma()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_proforma_view);
        $i = 0;

        $builder->where("docno = '$nama'");
        foreach ($this->t_proforma_view_column as $mrp)
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

                if(count($this->t_proforma_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_proforma_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_proforma_view_order))
        {
            $order = $this->t_proforma_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_proforma_view(){
        $builder = $this->_get_query_t_proforma();
        ////$this->_get_query_t_proforma();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_proforma_view_count_filtered()
    {
        $builder = $this->_get_query_t_proforma();
        ////$this->_get_query_t_proforma();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_proforma_view_count_all()
    {
        $builder = $this->_get_query_t_proforma();
        return $builder->countAllResults();
    }
    public function get_t_proforma_view_by_id($id)
    {
        $builder = $this->_get_query_t_proforma();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* TRX MRP DETAIL */
    var $t_proforma_dtl_view = "sc_trx.proformadtl";
    var $t_proforma_dtl_view_column = array('docno','docref','description');
    var $t_proforma_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_proforma_dtl($docnoParam)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_proforma_dtl_view);
        $i = 0;

        $builder->where("docno = '$docnoParam'");
        foreach ($this->t_proforma_dtl_view_column as $mrp)
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

                if(count($this->t_proforma_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_proforma_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_proforma_dtl_view_order))
        {
            $order = $this->t_proforma_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_proforma_dtl_view($docnoParam){
        $builder = $this->_get_query_t_proforma_dtl($docnoParam);
        ////$this->_get_query_t_proforma_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    


    function t_proforma_dtl_view_count_filtered($docnoParam)
    {
        $builder = $this->_get_query_t_proforma_dtl($docnoParam);
        ////$this->_get_query_t_proforma_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_proforma_dtl_view_count_all($docnoParam)
    {
        $builder = $this->_get_query_t_proforma_dtl($docnoParam);
        return $builder->countAllResults();
    }
    public function get_t_proforma_dtl_view_by_id($id,$docnoParam)
    {
        $builder = $this->_get_query_t_proforma_dtl($docnoParam);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    // public function get_suppliers()
    // {
    //     $builder = $this->db->table('sc_mst.msupplier'); // Tentukan tabel
    //     $builder->select('idsupplier, nmsupplier'); // Tentukan kolom yang diambil
    //     $query = $builder->get(); // Ambil data
    //     return $query->getResult(); // Kembalikan hasilnya
    // }

    public function q_proforma_master_temp($param)
    {
        return $this->db->query("select * from sc_tmp.proforma where docno is not null $param");
    }

    public function q_proforma_dtl_temp($param)
    {
        return $this->db->query("select * from sc_tmp.proformadtl where docno is not null $param order by idurut desc");
    }


    public function q_proforma_master($param)
    {
        return $this->db->query("select * from sc_trx.proforma where docno is not null $param");
    }

    public function q_proforma_dtl($param)
    {
        return $this->db->query("select * from sc_trx.proformadtl where docno is not null $param order by idurut desc");
    }


    //WO TEMP
    /* WO DETAIL */
    var $t_proforma_dtl_temp_view = "sc_tmp.proformadtl";
    var $t_proforma_dtl_temp_view_column = array('docno','docref','description');
    var $t_proforma_dtl_temp_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_t_proforma_dtl_temp($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_proforma_dtl_temp_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->t_proforma_dtl_temp_view_column as $mrp)
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

                if(count($this->t_proforma_dtl_temp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_proforma_dtl_temp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_proforma_dtl_temp_view_order))
        {
            $order = $this->t_proforma_dtl_temp_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_t_proforma_dtl_temp_view($docno){
        $builder = $this->_get_query_t_proforma_dtl_temp($docno);
        ////$this->_get_query_t_proforma_dtl_temp($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_proforma_dtl_temp_view_count_filtered($docno)
    {
        $builder = $this->_get_query_t_proforma_dtl_temp($docno);
        ////$this->_get_query_t_proforma_dtl_temp($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_proforma_dtl_temp_view_count_all($docno)
    {
        $builder = $this->_get_query_t_proforma_dtl_temp($docno);
        return $builder->countAllResults();
    }
    public function get_t_proforma_dtl_temp_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_t_proforma_dtl_temp($docno);
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_front_proforma_view = "sc_trx.proforma";
    var $t_front_proforma_view = "(select b.nama_customer as nmcust,
                                b.docno as docnocust,
                                b.alamat_kantor,
                                b.alamat_pengiriman,
                                b.alamat_penagihan,
                                b.email,
                                b.kontak_pic,
                                b.jabatanpic,
                                b.telepon,
                                b.fax,

                                a.inputby as proforma_inputby,
                                a.status as proforma_status,
                                trim(a.status) as trimmed_status,
                                a.* 
                        from sc_trx.proforma a
                        left outer join sc_mst.customer b on a.cust = b.docno
                        ) as x";

    var $t_front_proforma_view_column = array('docno','cust','description');
    var $t_front_proforma_view_order = array("inputdate" => 'desc'); // default order
    private function _get_query_front_proforma()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_proforma_view);
        $builder->join(
            "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
            FROM sc_mst.trxtype 
            WHERE jenistrx = 'I.S.A.3' 
            ORDER BY kdtrx, uraian DESC) AS trx", 
            "COALESCE(x.proforma_status, '') = COALESCE(trx.kdtrx, '')", 
            "left"
        );
        $builder->select("x.*, trx.uraian AS status_desc");
        $builder->where('proforma_inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_proforma_view_column as $mrpgroup)
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

                if(count($this->t_front_proforma_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_proforma_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_proforma_view_order))
        {
            $order = $this->t_front_proforma_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_proforma_view(){
        $builder = $this->_get_query_front_proforma();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_proforma_view_count_filtered()
    {
        $builder = $this->_get_query_front_proforma();
        ////$this->_get_query_t_proforma();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_proforma_view_count_all()
    {
        $builder = $this->_get_query_front_proforma();
        return $builder->countAllResults();
    }
    public function get_t_front_proforma_view_by_id($id)
    {
        $builder = $this->_get_query_front_proforma();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }




    // function q_label_stdUsage($param){
    //     return $this->db->query("select trim(batch) as batch,trim(idbarang) as idbarang,trim(nmbarang) as nmbarang,trim(unit) as unit,idspec as id,idspec from (
    //     select '' as batch,idbarang,nmbarang,unit,trim(idbarang)||'' as idspec from sc_mst.mbarang where idbarang is not null and coalesce(chold,'NO') !='YES'
    //     union all
    //     select a.batch,a.idbarang,b.nmbarang,b.unit,trim(a.idbarang)||trim(a.batch) as idspec from sc_mst.stkgdw a
    //     left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
    //     where coalesce(a.batch,'') !=''
    //     group by a.batch,a.idbarang,b.nmbarang,b.unit) as x
    //     where idbarang is not null $param");
    // }

    public function q_label_stdUsage($param)
    {
        return $this->db->query("select * from sc_mst.standart_usage_mst where docno is not null $param");
    }

    public function getRolePO($jobcode, $codemenu)
    {
        return $this->db->query("
            SELECT prefix,infix, suffix 
            FROM sc_mst.rolepo 
            WHERE TRIM(jobcode) = ? AND TRIM(codemenu) = ?
        ", [$jobcode, $codemenu])->getRowArray();
    }


        public function q_view_print_proforma_dtl($param,$nama)
    {
        return $this->db->query("select * from sc_trx.view_print_proforma_dtl where docno is not null and printby='$nama' $param order by inputdate asc,idurut asc");
    }



}