<?php
/* persediaan\m_persediaan.php */
namespace App\Models\Persediaan;

use CodeIgniter\Model;

class M_Persediaan extends Model
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

    public function q_tmp_transfer_spk_mst($param)
    {
        return $this->db->query("select * from sc_tmp.transfer_spk_mst where docno is not null $param");
    }

    public function q_tmp_transfer_spk_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.transfer_spk_dtl where docno is not null $param order by idurut desc");
    }


    public function q_trx_transfer_spk_mst($param)
    {
        return $this->db->query("select * from sc_trx.transfer_spk_mst where docno is not null $param");
    }

    public function q_trx_transfer_spk_dtl($param)
    {
        return $this->db->query("select * from sc_trx.transfer_spk_dtl where docno is not null $param order by idurut desc");
    }


    public function delete_tmp_spk_transfer_dtl($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_tmp.transfer_spk_dtl'); // langsung sebut table

        if (is_array($ids)) {
            return $builder->whereIn('idurut', $ids)->delete();
        }

        return $builder->where('idurut', $ids)->delete();
    }


    //WO TEMP
    /* WO DETAIL */
    var $tmp_transfer_spk_dtl_view = "sc_tmp.transfer_spk_dtl";
    var $tmp_transfer_spk_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $tmp_transfer_spk_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_transfer_spk_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_transfer_spk_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->tmp_transfer_spk_dtl_view_column as $mrp)
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

                if(count($this->tmp_transfer_spk_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_transfer_spk_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_transfer_spk_dtl_view_order))
        {
            $order = $this->tmp_transfer_spk_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_transfer_spk_dtl_view($docno){
        $builder = $this->_get_query_tmp_transfer_spk_dtl($docno);
        ////$this->_get_query_tmp_transfer_spk_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_transfer_spk_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_transfer_spk_dtl($docno);
        ////$this->_get_query_tmp_transfer_spk_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_transfer_spk_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_transfer_spk_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_transfer_spk_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_transfer_spk_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* UNTUK LIST DEPAN */
    // var $t_trx_transfer_spk_mst_view = "sc_trx.pp";
    var $t_trx_transfer_spk_mst_view = "(select A.*,z.uraian as nmstatus,kdtrx,z.jenistrx,z.uraian as nmstatus from sc_trx.transfer_spk_mst a 
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.Q.A.1' and trim(a.status)=trim(z.kdtrx)) as x";
    var $t_trx_transfer_spk_mst_view_column = array('docno','idlocation_from','idlocation_to');
    var $t_trx_transfer_spk_mst_view_order = array('inputdate' => 'desc'); // default order
    private function _get_t_trx_transfer_spk()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_trx_transfer_spk_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_trx_transfer_spk_mst_view_column as $mrpgroup)
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

                if(count($this->t_trx_transfer_spk_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_transfer_spk_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_transfer_spk_mst_view_order))
        {
            $order = $this->t_trx_transfer_spk_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_trx_transfer_spk_mst_view(){
        $builder = $this->_get_t_trx_transfer_spk();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_transfer_spk_mst_view_count_filtered()
    {
        $builder = $this->_get_t_trx_transfer_spk();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_transfer_spk_mst_view_count_all()
    {
        $builder = $this->_get_t_trx_transfer_spk();
        return $builder->countAllResults();
    }
    public function get_t_trx_transfer_spk_mst_view_by_id($id)
    {
        $builder = $this->_get_t_trx_transfer_spk();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* WO DETAIL */
    var $trx_transfer_spk_dtl_view = "sc_trx.transfer_spk_dtl";
    var $trx_transfer_spk_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $trx_transfer_spk_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_transfer_spk_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_transfer_spk_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->trx_transfer_spk_dtl_view_column as $mrp)
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

                if(count($this->trx_transfer_spk_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_transfer_spk_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_transfer_spk_dtl_view_order))
        {
            $order = $this->trx_transfer_spk_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_transfer_spk_dtl_view($docno){
        $builder = $this->_get_query_trx_transfer_spk_dtl($docno);
        ////$this->_get_query_trx_transfer_spk_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_transfer_spk_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_transfer_spk_dtl($docno);
        ////$this->_get_query_trx_transfer_spk_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_transfer_spk_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_transfer_spk_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_transfer_spk_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_transfer_spk_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    /* WO DETAIL */
    var $tmp_transfer_location_dtl_view = "sc_tmp.transfer_location_dtl";

    var $tmp_transfer_location_dtl_view_column = array(
        'iduniq',
        'idurut',
        'idbarang',
        'nmbarang',
        'unit',
        'unit_default',   /* BARU */
        'qty',
        'qty_base',       /* BARU */
        'description'
    );

    var $tmp_transfer_location_dtl_view_order = array("idurut" => 'desc');


    private function _get_query_tmp_transfer_location_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode = trim($this->session->get('loccode'));
        $nama    = trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_transfer_location_dtl_view);

        /* SELECT EKSPLISIT — tambahkan iduniq */
        $builder->select(
            'idurut, iduniq, docno, idbarang, nmbarang, '
            . 'unit, unit_default, conversion_factor, '
            . 'qty, qty_base, batch, lotno, description'
        );

        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");

        foreach ($this->tmp_transfer_location_dtl_view_column as $mrp)
        {
            if (!empty($_POST['search']['value']))
            {
                if ($i === 0)
                {
                    $builder->groupStart();
                    $builder->like(
                        "upper(cast(" . strtoupper($mrp) . " as varchar))",
                        strtoupper($_POST['search']['value'])
                    );
                }
                else
                {
                    $builder->orLike(
                        "upper(cast(" . strtoupper($mrp) . " as varchar))",
                        strtoupper($_POST['search']['value'])
                    );
                }

                if (count($this->tmp_transfer_location_dtl_view_column) - 1 == $i)
                    $builder->groupEnd();
            }
            $i++;
        }

        if (isset($_POST['order']))
        {
            if ($_POST['order']['0']['column'] != 0)
            {
                $col_index = (int) $_POST['order']['0']['column'] - 1;

                if (isset($this->tmp_transfer_location_dtl_view_column[$col_index]))
                {
                    $builder->orderBy(
                        $this->tmp_transfer_location_dtl_view_column[$col_index],
                        $_POST['order']['0']['dir']
                    );
                }
            }
        }
        else if (isset($this->tmp_transfer_location_dtl_view_order))
        {
            $order = $this->tmp_transfer_location_dtl_view_order;
            foreach ($order as $key => $mrp) {
                $builder->orderBy($key, $mrp);
            }
        }

        return $builder;
    }


    function get_tmp_transfer_location_dtl_view($docno)
    {
        $builder = $this->_get_query_tmp_transfer_location_dtl($docno);

        if (isset($_POST['length']) && $_POST['length'] != -1)
            $builder->limit((int) $_POST['length'], (int) $_POST['start']);

        $query = $builder->get();

        /* GUARD: query gagal → jangan crash */
        if ($query === false) {
            log_message('error',
                '[get_tmp_transfer_location_dtl_view] Error: '
                . json_encode($this->db->error()));
            return [];
        }

        return $query->getResult();
    }


    function tmp_transfer_location_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_transfer_location_dtl($docno);

        $query = $builder->get();

        if ($query === false) {
            log_message('error',
                '[tmp_transfer_location_dtl_view_count_filtered] Error: '
                . json_encode($this->db->error()));
            return 0;
        }

        return $query->getNumRows();
    }


    public function tmp_transfer_location_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_transfer_location_dtl($docno);
        return (int) $builder->countAllResults();
    }


    public function get_tmp_transfer_location_dtl_view_by_id($id, $docno)
    {
        $builder = $this->_get_query_tmp_transfer_location_dtl($docno);
        // $builder->where('idmrpgroup', $id);

        $query = $builder->get();

        if ($query === false) {
            return null;
        }

        return $query->getRow();
    }


    var $trx_transfer_location_mst_view = "(select A.*,z.uraian as nmstatus,kdtrx,z.jenistrx,z.uraian as nmstatus from sc_trx.transfer_location_mst a 
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.Q.A.1' and trim(a.status)=trim(z.kdtrx)) as x";
    var $trx_transfer_location_mst_view_column = array('docno','idlocation_from','idlocation_to');
    var $trx_transfer_location_mst_view_order = array('inputdate' => 'desc'); // default order
    private function _get_trx_transfer_location_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_transfer_location_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_transfer_location_mst_view_column as $mrpgroup)
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

                if(count($this->trx_transfer_location_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_transfer_location_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_transfer_location_mst_view_order))
        {
            $order = $this->trx_transfer_location_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_transfer_location_mst_view(){
        $builder = $this->_get_trx_transfer_location_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_transfer_location_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_transfer_location_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_transfer_location_mst_view_count_all()
    {
        $builder = $this->_get_trx_transfer_location_mst();
        return $builder->countAllResults();
    }
    public function get_trx_transfer_location_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_transfer_location_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    // =============================== VOID PP ============================================================




    public function q_tmp_transfer_location_mst($param)
    {
        return $this->db->query("select * from sc_tmp.transfer_location_mst where docno is not null $param");
    }

    public function q_tmp_transfer_location_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.transfer_location_dtl where docno is not null $param order by idurut desc");
    }


    public function q_trx_transfer_location_mst($param)
    {
        return $this->db->query("select * from sc_trx.transfer_location_mst where docno is not null $param");
    }

    public function q_trx_transfer_location_dtl($param)
    {
        return $this->db->query("select * from sc_trx.transfer_location_dtl where docno is not null $param order by idurut desc");
    }









    /* ******************** AJUSTMENT STOCK *******************               */



    var $tmp_ajustment_stock_dtl_view = "sc_tmp.ajustment_stock_dtl";
    var $tmp_ajustment_stock_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $tmp_ajustment_stock_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_ajustment_stock_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_ajustment_stock_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->tmp_ajustment_stock_dtl_view_column as $mrp)
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

                if(count($this->tmp_ajustment_stock_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_ajustment_stock_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_ajustment_stock_dtl_view_order))
        {
            $order = $this->tmp_ajustment_stock_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_ajustment_stock_dtl_view($docno){
        $builder = $this->_get_query_tmp_ajustment_stock_dtl($docno);
        ////$this->_get_query_tmp_ajustment_stock_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_ajustment_stock_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_ajustment_stock_dtl($docno);
        ////$this->_get_query_tmp_ajustment_stock_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_ajustment_stock_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_ajustment_stock_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_ajustment_stock_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_ajustment_stock_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



////////////////////UNTUK TRX //////////////////////////////////////////////

    var $trx_ajustment_stock_dtl_view = "sc_trx.ajustment_stock_dtl";
    var $trx_ajustment_stock_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $trx_ajustment_stock_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_ajustment_stock_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_ajustment_stock_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->trx_ajustment_stock_dtl_view_column as $mrp)
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

                if(count($this->trx_ajustment_stock_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_ajustment_stock_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_ajustment_stock_dtl_view_order))
        {
            $order = $this->trx_ajustment_stock_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_trx_ajustment_stock_dtl_view($docno){
        $builder = $this->_get_query_trx_ajustment_stock_dtl($docno);
        ////$this->_get_query_trx_ajustment_stock_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_ajustment_stock_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_ajustment_stock_dtl($docno);
        ////$this->_get_query_trx_ajustment_stock_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_ajustment_stock_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_ajustment_stock_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_trx_ajustment_stock_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_trx_ajustment_stock_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    ////////////////////UNTUK TRX //////////////////////////////////////////////

    var $trx_ajustment_stock_mst_view = "(select A.*,z.uraian as nmstatus,kdtrx,z.jenistrx,z.uraian as nmstatus from sc_trx.ajustment_stock_mst a 
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.Q.A.3' and trim(a.status)=trim(z.kdtrx)) as x";
    var $trx_ajustment_stock_mst_view_column = array('docno','idlocation_from','idlocation_to');
    var $trx_ajustment_stock_mst_view_order = array('inputdate' => 'desc'); // default order
    private function _get_trx_ajustment_stock_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_ajustment_stock_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_ajustment_stock_mst_view_column as $mrpgroup)
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

                if(count($this->trx_ajustment_stock_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->trx_ajustment_stock_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_ajustment_stock_mst_view_order))
        {
            $order = $this->trx_ajustment_stock_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_ajustment_stock_mst_view(){
        $builder = $this->_get_trx_ajustment_stock_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_ajustment_stock_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_ajustment_stock_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_ajustment_stock_mst_view_count_all()
    {
        $builder = $this->_get_trx_ajustment_stock_mst();
        return $builder->countAllResults();
    }
    public function get_trx_ajustment_stock_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_ajustment_stock_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    // =============================== VOID PP ============================================================




    public function q_tmp_ajustment_stock_mst($param)
    {
        return $this->db->query("select * from sc_tmp.ajustment_stock_mst where docno is not null $param");
    }

    public function q_tmp_ajustment_stock_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.ajustment_stock_dtl where docno is not null $param order by idurut desc");
    }


    public function q_trx_ajustment_stock_mst($param)
    {
        return $this->db->query("select * from sc_trx.ajustment_stock_mst where docno is not null $param");
    }

    public function q_trx_ajustment_stock_dtl($param)
    {
        return $this->db->query("select * from sc_trx.ajustment_stock_dtl where docno is not null $param order by idurut desc");
    }




    /* *********************************************************8888 PEMAKAIAN BARANG ******************************************************************* */



    var $tmp_pnm_brng_dtl_view = "sc_tmp.pnm_brng_dtl";
    var $tmp_pnm_brng_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $tmp_pnm_brng_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_pnm_brng_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_pnm_brng_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->tmp_pnm_brng_dtl_view_column as $mrp)
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

                if(count($this->tmp_pnm_brng_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_pnm_brng_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_pnm_brng_dtl_view_order))
        {
            $order = $this->tmp_pnm_brng_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_pnm_brng_dtl_view($docno){
        $builder = $this->_get_query_tmp_pnm_brng_dtl($docno);
        ////$this->_get_query_tmp_pnm_brng_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_pnm_brng_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_pnm_brng_dtl($docno);
        ////$this->_get_query_tmp_pnm_brng_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_pnm_brng_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_pnm_brng_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_pnm_brng_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_pnm_brng_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    var $trx_pnm_brng_mst_view = "(select A.*,z.uraian as nmstatus,kdtrx,z.jenistrx,
    trim(coalesce(b.nmbranch,'')) as nmbranch,
    trim(coalesce(c.nmcostcenter,'')) as nmcostcenter,
    coalesce((select sum(d.valsum) from sc_trx.pnm_brng_dtl d where trim(d.docno)=trim(a.docno)),0) as total
from sc_trx.pnm_brng_mst a
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.Q.A.1' and trim(a.status)=trim(z.kdtrx)
left outer join sc_mst.branchjob b on trim(b.idbranch)=trim(a.cabang)
left outer join sc_mst.costcenter c on trim(c.idcostcenter)=trim(a.idcostcenter)) as x";
    var $trx_pnm_brng_mst_view_column = array('docno','docdate','cabang','idcostcenter','docref','total','nmstatus','nmbranch','nmcostcenter');
    var $trx_pnm_brng_mst_view_order = array('inputdate' => 'desc'); // default order
    private function _get_trx_pnm_brng_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_pnm_brng_mst_view);


        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_pnm_brng_mst_view_column as $mrpgroup)
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

                if(count($this->trx_pnm_brng_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            /* urutan kolom DataTables -> kolom view.
               Kolom No (0), Action (1) & Status (8) tidak diurutkan
               (sama seperti LPB; kolom status pada view tidak unik). */
            $orderMap = array(
                2 => 'docno',
                3 => 'docdate',
                4 => 'cabang',
                5 => 'idcostcenter',
                6 => 'docref',
                7 => 'total',
            );

            $orderCol = (int) $_POST['order']['0']['column'];

            if (isset($orderMap[$orderCol])) {
                $builder->orderBy($orderMap[$orderCol], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_pnm_brng_mst_view_order))
        {
            $order = $this->trx_pnm_brng_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_pnm_brng_mst_view(){
        $builder = $this->_get_trx_pnm_brng_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_pnm_brng_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_pnm_brng_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_pnm_brng_mst_view_count_all()
    {
        $builder = $this->_get_trx_pnm_brng_mst();
        return $builder->countAllResults();
    }
    public function get_trx_pnm_brng_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_pnm_brng_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    // =============================== VOID PP ============================================================




    public function q_tmp_pnm_brng_mst($param)
    {
        return $this->db->query("select * from sc_tmp.pnm_brng_mst where docno is not null $param");
    }

    public function q_tmp_pnm_brng_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.pnm_brng_dtl where docno is not null $param order by idurut desc");
    }


    public function q_trx_pnm_brng_mst($param)
    {
        return $this->db->query("select * from sc_trx.pnm_brng_mst where docno is not null $param");
    }

    public function q_trx_pnm_brng_dtl($param)
    {
        return $this->db->query("select * from sc_trx.pnm_brng_dtl where docno is not null $param order by idurut desc");
    }

    /**
     * Header halaman DETAIL Penerimaan Barang (read only).
     * Melengkapi nama cabang, cost center, pemohon, status, dan total nilai.
     */
    public function q_detail_pnm_brng_mst($docno)
    {
        $sql = "
            SELECT h.*,
                   TRIM(COALESCE(b.nmbranch, ''))    AS nmbranch,
                   TRIM(COALESCE(c.nmcostcenter, '')) AS nmcostcenter,
                   COALESCE(NULLIF(TRIM(u.nmlengkap), ''), TRIM(COALESCE(h.pemohon, ''))) AS nmpemohon,
                   TRIM(COALESCE(z.uraian, ''))      AS nmstatus,
                   COALESCE((SELECT SUM(x.valsum)
                               FROM sc_trx.pnm_brng_dtl x
                              WHERE TRIM(x.docno) = TRIM(h.docno)), 0) AS total
              FROM sc_trx.pnm_brng_mst h
              LEFT JOIN sc_mst.branchjob b  ON TRIM(b.idbranch)      = TRIM(h.cabang)
              LEFT JOIN sc_mst.costcenter c ON TRIM(c.idcostcenter)  = TRIM(h.idcostcenter)
              LEFT JOIN sc_mst.user u       ON TRIM(u.username)      = TRIM(h.pemohon)
              LEFT JOIN sc_mst.trxtype z    ON TRIM(COALESCE(z.jenistrx, '')) = 'I.Q.A.1'
                                           AND TRIM(h.status) = TRIM(z.kdtrx)
             WHERE TRIM(h.docno) = TRIM(?)
             LIMIT 1
        ";

        return $this->db->query($sql, [$docno]);
    }

    /**
     * Detail barang halaman DETAIL Penerimaan Barang (read only) + nama gudang.
     */
    public function q_detail_pnm_brng_dtl($docno)
    {
        $sql = "
            SELECT d.*,
                   TRIM(COALESCE(l.nmlocation, '')) AS nmlocation
              FROM sc_trx.pnm_brng_dtl d
              LEFT JOIN sc_mst.mlocation l ON TRIM(l.idlocation) = TRIM(d.idlocation)
             WHERE TRIM(d.docno) = TRIM(?)
             ORDER BY d.idurut
        ";

        return $this->db->query($sql, [$docno]);
    }

    /**
     * Cek kelengkapan COA untuk transaksi Pemakaian Barang (I.Q.A.6).
     * Dipakai sebelum Simpan Final Data, supaya trigger sc_tmp.tr_tmp_pnm_brng_mst()
     * tidak melempar error "COA persediaan / COA biaya belum diisi".
     *  - COA persediaan : sc_mst.mbarang.ppersediaan
     *  - COA biaya      : sc_mst.costcenter.pbiaya
     */
    public function q_cek_coa_pnm_brng($nama)
    {
        $sql = "
            SELECT 'BARANG' AS jenis,
                   COALESCE(BTRIM(d.idbarang), '') AS kode,
                   COALESCE(BTRIM(m.nmbarang), '') AS nama
              FROM sc_tmp.pnm_brng_dtl d
              LEFT JOIN sc_mst.mbarang m ON BTRIM(m.idbarang) = BTRIM(d.idbarang)
             WHERE COALESCE(d.inputby, '') = ?
               AND TRIM(COALESCE(m.ppersediaan, '')) = ''
            UNION
            SELECT 'COSTCENTER' AS jenis,
                   COALESCE(BTRIM(COALESCE(NULLIF(BTRIM(d.idcostcenter), ''), h.idcostcenter)), '') AS kode,
                   '' AS nama
              FROM sc_tmp.pnm_brng_dtl d
              JOIN sc_tmp.pnm_brng_mst h
                ON BTRIM(h.docno) = BTRIM(d.docno)
               AND COALESCE(h.inputby, '') = COALESCE(d.inputby, '')
              LEFT JOIN sc_mst.costcenter cc
                ON BTRIM(cc.idcostcenter) = BTRIM(COALESCE(NULLIF(BTRIM(d.idcostcenter), ''), h.idcostcenter))
             WHERE COALESCE(d.inputby, '') = ?
               AND TRIM(COALESCE(cc.pbiaya, '')) = ''
            ORDER BY 1, 2
        ";
        return $this->db->query($sql, [$nama, $nama]);
    }

    /**
     * Cek kelengkapan COA untuk 1 item Pemakaian Barang (dipakai saat simpan detail).
     * Logika mengikuti trigger sc_trx.fn_sync_pnm_brng_dtl_to_transaction():
     *  - COA persediaan : sc_mst.mbarang.ppersediaan   (barang tidak ada di master = error)
     *  - COA biaya      : sc_mst.costcenter.pbiaya     (cost center tidak ada / kosong = error)
     */
    public function q_cek_coa_item_pnm_brng($idbarang, $idcostcenter)
    {
        $sql = "
            SELECT 'BARANG' AS jenis,
                   BTRIM(?::text) AS kode,
                   COALESCE(BTRIM(m.nmbarang), '') AS nama
              FROM (SELECT 1) x
              LEFT JOIN sc_mst.mbarang m ON BTRIM(m.idbarang) = BTRIM(?::text)
             WHERE TRIM(COALESCE(m.ppersediaan, '')) = ''
            UNION
            SELECT 'COSTCENTER' AS jenis,
                   BTRIM(?::text) AS kode,
                   '' AS nama
              FROM (SELECT 1) y
              LEFT JOIN sc_mst.costcenter cc ON BTRIM(cc.idcostcenter) = BTRIM(?::text)
             WHERE TRIM(COALESCE(cc.pbiaya, '')) = ''
            ORDER BY 1, 2
        ";
        return $this->db->query($sql, [$idbarang, $idbarang, $idcostcenter, $idcostcenter]);
    }

    public function q_tmp_pnm_brng_dtl_summary($param)
    {
        return $this->db->query("select sum(qty) as total_qty , sum(valsum) as valsum  from sc_tmp.pnm_brng_dtl where docno is not null $param;");
    }


    /* *********************************************************8888 PEMAKAIAN BARANG ******************************************************************* */



    var $tmp_pmk_brng_dtl_view = "sc_tmp.pmk_brng_dtl";
    var $tmp_pmk_brng_dtl_view_column = array('idurut','idbarang','nmbarang','unit','qty','description');
    var $tmp_pmk_brng_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_pmk_brng_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_pmk_brng_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        // $builder->where("docno = '$docno'");
        $builder->where("inputby = '$nama'");
        foreach ($this->tmp_pmk_brng_dtl_view_column as $mrp)
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

                if(count($this->tmp_pmk_brng_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->tmp_pmk_brng_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->tmp_pmk_brng_dtl_view_order))
        {
            $order = $this->tmp_pmk_brng_dtl_view_order;
            foreach ($order as $key => $mrp){
                $builder->orderBy($key, $mrp);
            }
        }
        return $builder;
    }


    function get_tmp_pmk_brng_dtl_view($docno){
        $builder = $this->_get_query_tmp_pmk_brng_dtl($docno);
        ////$this->_get_query_tmp_pmk_brng_dtl($docno);
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function tmp_pmk_brng_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_tmp_pmk_brng_dtl($docno);
        ////$this->_get_query_tmp_pmk_brng_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function tmp_pmk_brng_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_tmp_pmk_brng_dtl($docno);
        return $builder->countAllResults();
    }
    public function get_tmp_pmk_brng_dtl_view_by_id($id,$docno)
    {
        $builder = $this->_get_query_tmp_pmk_brng_dtl($docno);
        //$builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* Kolom A.* ditulis eksplisit: sebelumnya `A.*` + alias nmcostcenter dari
       join sc_mst.costcenter menghasilkan dua kolom bernama nmcostcenter pada
       subquery, sehingga ORDER BY / pencarian DataTables error
       ("kolom referensi nmcostcenter ambigu"). Nilai cost center header
       dipakai lebih dulu, join hanya sebagai fallback. */
    var $trx_pmk_brng_mst_view = "(select a.idurut, a.docno, a.doctype, a.docdate, a.docref,
    a.cabang, a.cabang_sent, a.pemohon, a.estpakai,
    a.idlocation_from, a.idlocation_to, a.idlocation_transit,
    a.status, a.description, a.inputby, a.inputdate, a.updateby, a.updatedate,
    a.printby, a.printdate, a.docnotmp, a.idcostcenter,
    z.uraian as nmstatus,kdtrx,z.jenistrx,
    trim(coalesce(b.nmbranch,'')) as nmbranch,
    trim(coalesce(nullif(trim(a.nmcostcenter),''), c.nmcostcenter, '')) as nmcostcenter,
    coalesce((select sum(d.valsum) from sc_trx.pmk_brng_dtl d where trim(d.docno)=trim(a.docno)),0) as total
 from sc_trx.pmk_brng_mst a
 left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.Q.A.1' and trim(a.status)=trim(z.kdtrx)
 left outer join sc_mst.branchjob b on trim(b.idbranch)=trim(a.cabang)
 left outer join sc_mst.costcenter c on trim(c.idcostcenter)=trim(a.idcostcenter)) as x";
    var $trx_pmk_brng_mst_view_column = array('docno','docdate','cabang','idcostcenter','docref','total','nmstatus','nmbranch','nmcostcenter');
    var $trx_pmk_brng_mst_view_order = array('inputdate' => 'desc'); // default order
    private function _get_trx_pmk_brng_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_pmk_brng_mst_view);


        $i = 0;
        $searchValue = isset($_POST['search']['value']) ? (string) $_POST['search']['value'] : '';

        //$builder->where("docno = '$nama'");
        foreach ($this->trx_pmk_brng_mst_view_column as $mrpgroup)
        {
            if($searchValue !== '') // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($mrpgroup) . " as varchar))", strtoupper($searchValue));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($mrpgroup) . " as varchar))", strtoupper($searchValue));
                }

                if(count($this->trx_pmk_brng_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            /* urutan kolom DataTables -> kolom view.
               Kolom No (0), Action (1) & Status (8) tidak diurutkan
               (sama seperti LPB / PNM; kolom status pada view tidak unik). */
            $orderMap = array(
                2 => 'docno',
                3 => 'docdate',
                4 => 'cabang',
                5 => 'idcostcenter',
                6 => 'docref',
                7 => 'total',
            );

            $orderCol = (int) $_POST['order']['0']['column'];

            if (isset($orderMap[$orderCol])) {
                $builder->orderBy($orderMap[$orderCol], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->trx_pmk_brng_mst_view_order))
        {
            $order = $this->trx_pmk_brng_mst_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_trx_pmk_brng_mst_view(){
        $builder = $this->_get_trx_pmk_brng_mst();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function trx_pmk_brng_mst_view_count_filtered()
    {
        $builder = $this->_get_trx_pmk_brng_mst();
        ////$this->_get_query_t_pp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function trx_pmk_brng_mst_view_count_all()
    {
        $builder = $this->_get_trx_pmk_brng_mst();
        return $builder->countAllResults();
    }
    public function get_trx_pmk_brng_mst_view_by_id($id)
    {
        $builder = $this->_get_trx_pmk_brng_mst();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    public function q_tmp_pmk_brng_mst($param)
    {
        return $this->db->query("select * from sc_tmp.pmk_brng_mst where docno is not null $param");
    }

    public function q_tmp_pmk_brng_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.pmk_brng_dtl where docno is not null $param order by idurut desc");
    }


    public function q_trx_pmk_brng_mst($param)
    {
        return $this->db->query("select * from sc_trx.pmk_brng_mst where docno is not null $param");
    }

    public function q_trx_pmk_brng_dtl($param)
    {
        return $this->db->query("select * from sc_trx.pmk_brng_dtl where docno is not null $param order by idurut desc");
    }

    /* ============================================================
       STOK TERSEDIA PMK (READ ONLY)
       Sumber: sc_trx.stkblc (stock ledger) -> SUM(qty_in) - SUM(qty_out)
       Harga rata-rata: sc_trx.stkblc_avgcost
       ============================================================ */

    /**
     * Stok on-hand untuk kombinasi barang + gudang (+ batch).
     * Bila $batch kosong, stok dijumlahkan untuk seluruh batch gudang tsb.
     */
    public function q_stock_onhand_pmk($idlocation, $idbarang, $batch = '')
    {
        $sql = "SELECT COALESCE(SUM(COALESCE(sb.qty_in, 0)) - SUM(COALESCE(sb.qty_out, 0)), 0) AS stok
                  FROM sc_trx.stkblc sb
                 WHERE TRIM(sb.idbarang) = TRIM(?)
                   AND TRIM(COALESCE(sb.idlocation, '')) = TRIM(?)";

        $binds = array($idbarang, $idlocation);

        if (trim((string) $batch) !== '') {
            $sql .= " AND TRIM(COALESCE(sb.batch, '')) = TRIM(?)";
            $binds[] = $batch;
        }

        $row = $this->db->query($sql, $binds)->getRowArray();

        return (float) ($row['stok'] ?? 0);
    }

    /**
     * Qty yang sudah "dibooking" pada draft (sc_tmp.pmk_brng_dtl) milik
     * user yang sedang login, untuk barang + gudang (+ batch) tertentu.
     *
     * Dipakai menghitung STOK TERSEDIA di grid input:
     *   stok tersedia = stok fisik (sc_trx.stkblc) - qty draft (tabel ini)
     *
     * @param string $inputby        user pemilik draft (session nama)
     * @param string $idlocation     gudang
     * @param string $idbarang       barang
     * @param string $batch          batch ('' = semua batch)
     * @param string $excludeIdurut  idurut baris yang sedang diedit (dikecualikan)
     *
     * @return float
     */
    public function q_stock_booked_pmk($inputby, $idlocation, $idbarang, $batch = '', $excludeIdurut = '')
    {
        $sql = "SELECT COALESCE(SUM(COALESCE(d.qty, 0)), 0) AS qty
                  FROM sc_tmp.pmk_brng_dtl d
                 WHERE TRIM(COALESCE(d.inputby, '')) = TRIM(?)
                   AND TRIM(d.idbarang) = TRIM(?)
                   AND TRIM(COALESCE(d.idlocation, '')) = TRIM(?)";

        $binds = array($inputby, $idbarang, $idlocation);

        if (trim((string) $batch) !== '') {
            $sql .= " AND TRIM(COALESCE(d.batch, '')) = TRIM(?)";
            $binds[] = $batch;
        }

        if (trim((string) $excludeIdurut) !== '') {
            $sql .= " AND COALESCE(d.idurut, 0) <> ?";
            $binds[] = (int) $excludeIdurut;
        }

        $row = $this->db->query($sql, $binds)->getRowArray();

        return (float) ($row['qty'] ?? 0);
    }

    /**
     * Stok tersedia (buffered) = stok fisik - qty draft user.
     * Hasil tidak pernah negatif.
     */
    public function q_stock_tersedia_pmk($inputby, $idlocation, $idbarang, $batch = '', $excludeIdurut = '')
    {
        $fisik  = $this->q_stock_onhand_pmk($idlocation, $idbarang, $batch);
        $booked = $this->q_stock_booked_pmk($inputby, $idlocation, $idbarang, $batch, $excludeIdurut);

        $tersedia = $fisik - $booked;

        return $tersedia > 0 ? $tersedia : 0;
    }

    /**
     * Harga rata-rata (average cost) barang + gudang (+ batch).
     * Bila batch kosong -> rata-rata berbobot dari seluruh batch.
     * Fallback: lastprice master barang.
     */
    public function q_avgcost_pmk($idlocation, $idbarang, $batch = '')
    {
        $sql = "SELECT
                    CASE WHEN SUM(COALESCE(ac.qty, 0)) > 0
                         THEN SUM(COALESCE(ac.total_value, 0)) / SUM(COALESCE(ac.qty, 0))
                         ELSE 0
                    END AS avgcost
                  FROM sc_trx.stkblc_avgcost ac
                 WHERE TRIM(ac.idbarang) = TRIM(?)
                   AND TRIM(COALESCE(ac.idlocation, '')) = TRIM(?)";

        $binds = array($idbarang, $idlocation);

        if (trim((string) $batch) !== '') {
            $sql .= " AND TRIM(COALESCE(ac.batch, '')) = TRIM(?)";
            $binds[] = $batch;
        }

        $row = $this->db->query($sql, $binds)->getRowArray();
        $avg = (float) ($row['avgcost'] ?? 0);

        if ($avg <= 0) {
            $brg = $this->db->query(
                "SELECT COALESCE(lastprice, 0) AS lastprice
                   FROM sc_mst.mbarang
                  WHERE TRIM(idbarang) = TRIM(?)
                  LIMIT 1",
                array($idbarang)
            )->getRowArray();

            $avg = (float) ($brg['lastprice'] ?? 0);
        }

        return $avg;
    }

    /* ============================================================
       PEMAKAIAN BARANG - DETAIL TRANSAKSI FINAL (READ ONLY)
       Sumber: sc_trx.pmk_brng_dtl, difilter per docno
       ============================================================ */
    var $trx_pmk_brng_dtl_view = "sc_trx.pmk_brng_dtl";
    var $trx_pmk_brng_dtl_view_column = array('idurut','idbarang','nmbarang','idlocation','batch','unit','qty','description');
    var $trx_pmk_brng_dtl_view_order = array("idurut" => 'asc'); // default order

    private function _get_query_trx_pmk_brng_dtl($docno)
    {
        $builder = $this->db->table($this->trx_pmk_brng_dtl_view . ' AS x');

        $builder->join('sc_mst.mlocation AS ml', 'ml.idlocation = x.idlocation', 'left');

        $builder->select("x.idurut, x.docno, x.idbarang, x.nmbarang, x.unit, x.qty, x.batch,
                          x.idlocation, ml.nmlocation, x.description, x.idcostcenter, x.idcoa, x.counter_idcoa");

        // filter dokumen : hanya baris milik docno yang dibuka
        $builder->where("trim(x.docno)", trim($docno));

        $builder->orderBy('x.idurut', 'asc');

        if (isset($_POST['search']['value']) && $_POST['search']['value']) // if datatable send POST for search
        {
            $i = 0;
            foreach ($this->trx_pmk_brng_dtl_view_column as $mrp)
            {
                if ($i === 0) // first loop
                {
                    $builder->groupStart(); // open bracket
                    $builder->like("upper(cast(x." . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(x." . strtoupper($mrp) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if (count($this->trx_pmk_brng_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket

                $i++;
            }
        }

        if (isset($_POST['order'])) // here order processing
        {
            $colIdx = (int) $_POST['order']['0']['column'];
            if ($colIdx != 0 && isset($this->trx_pmk_brng_dtl_view_column[$colIdx])) {
                $dir = strtoupper($_POST['order']['0']['dir']) === 'DESC' ? 'DESC' : 'ASC';
                $builder->orderBy('x.' . $this->trx_pmk_brng_dtl_view_column[$colIdx], $dir);
            }
        }

        return $builder;
    }

    function get_trx_pmk_brng_dtl_view($docno)
    {
        $builder = $this->_get_query_trx_pmk_brng_dtl($docno);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $builder->limit($_POST['length'], $_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    function trx_pmk_brng_dtl_view_count_filtered($docno)
    {
        $builder = $this->_get_query_trx_pmk_brng_dtl($docno);
        $query = $builder->get();
        return $query->getNumRows();
    }

    public function trx_pmk_brng_dtl_view_count_all($docno)
    {
        $builder = $this->_get_query_trx_pmk_brng_dtl($docno);
        return $builder->countAllResults();
    }

    /* ============================================================
       LAPORAN JURNAL TRANSAKSI PEMAKAIAN BARANG (READ ONLY)
       Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
       cost center diambil dari dokumen asal sc_trx.pmk_brng_mst.
       ============================================================ */
    function q_laporan_jurnal_transaksi_pmk($params = '')
    {
        return $this->db->query("
        WITH data_jurnal AS (

            SELECT
                jd.id,
                jd.jurnal_id,

                TRIM(jd.idcoa) AS idcoa,
                TRIM(coa.nmcoa) AS nmcoa,

                jd.debet,
                jd.kredit,

                TRIM(jd.ref_docno) AS ref_docno,
                TRIM(jd.ref_doctype) AS ref_doctype,

                TRIM(jh.docno) AS docno,
                TRIM(jh.doctype) AS doctype,

                jh.trxdate,

                TRIM(pmk.idcostcenter) AS idcostcenter,
                TRIM(cc.nmcostcenter) AS nmcostcenter

            FROM sc_trx.jurnal_dt jd

            INNER JOIN sc_trx.jurnal_hd jh
                ON jh.id = jd.jurnal_id

            LEFT JOIN sc_mst.coa coa
                ON TRIM(coa.idcoa) = TRIM(jd.idcoa)

            LEFT JOIN sc_trx.pmk_brng_mst pmk
                ON TRIM(pmk.docno) = COALESCE(
                    NULLIF(TRIM(jd.ref_docno), ''),
                    NULLIF(TRIM(jh.docno), '')
                )

            LEFT JOIN sc_mst.costcenter cc
                ON TRIM(cc.idcostcenter) = TRIM(pmk.idcostcenter)

            WHERE 1=1
            AND jh.status = 'POSTED'
            AND jd.status = 'POSTED'
            $params
        )

        /* ============================================
           DETAIL JURNAL
        ============================================ */

        SELECT
            id,
            jurnal_id,
            idcoa,
            nmcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype,
            docno,
            doctype,
            trxdate,
            idcostcenter,
            nmcostcenter,

            0 AS urutan

        FROM data_jurnal


        UNION ALL


        /* ============================================
           TOTAL
        ============================================ */

        SELECT
            NULL::BIGINT,
            NULL::BIGINT,

            NULL::VARCHAR,
            'TOTAL'::VARCHAR,

            COALESCE(SUM(debet), 0),
            COALESCE(SUM(kredit), 0),

            NULL::VARCHAR,
            NULL::VARCHAR,

            NULL::VARCHAR,
            NULL::VARCHAR,

            NULL::DATE,

            NULL::VARCHAR,
            NULL::VARCHAR,

            1 AS urutan

        FROM data_jurnal


        ORDER BY
            urutan,
            trxdate DESC NULLS LAST,
            jurnal_id,
            id
        ");
    }

    /* ============================================================
       LAPORAN JURNAL TRANSAKSI PEMAKAIAN BARANG (PNM) - READ ONLY
       Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
       cost center diambil dari dokumen asal sc_trx.pnm_brng_mst.
       ============================================================ */
    function q_laporan_jurnal_transaksi_pnm($params = '')
    {
        return $this->db->query("
        WITH data_jurnal AS (

            SELECT
                jd.id,
                jd.jurnal_id,

                TRIM(jd.idcoa) AS idcoa,
                TRIM(coa.nmcoa) AS nmcoa,

                jd.debet,
                jd.kredit,

                TRIM(jd.ref_docno) AS ref_docno,
                TRIM(jd.ref_doctype) AS ref_doctype,

                TRIM(jh.docno) AS docno,
                TRIM(jh.doctype) AS doctype,

                jh.trxdate,

                TRIM(pnm.idcostcenter) AS idcostcenter,
                TRIM(cc.nmcostcenter) AS nmcostcenter

            FROM sc_trx.jurnal_dt jd

            INNER JOIN sc_trx.jurnal_hd jh
                ON jh.id = jd.jurnal_id

            LEFT JOIN sc_mst.coa coa
                ON TRIM(coa.idcoa) = TRIM(jd.idcoa)

            LEFT JOIN sc_trx.pnm_brng_mst pnm
                ON TRIM(pnm.docno) = COALESCE(
                    NULLIF(TRIM(jd.ref_docno), ''),
                    NULLIF(TRIM(jh.docno), '')
                )

            LEFT JOIN sc_mst.costcenter cc
                ON TRIM(cc.idcostcenter) = TRIM(pnm.idcostcenter)

            WHERE 1=1
            AND jh.status = 'POSTED'
            AND jd.status = 'POSTED'
            $params
        )

        SELECT
            id,
            jurnal_id,
            idcoa,
            nmcoa,
            debet,
            kredit,
            ref_docno,
            ref_doctype,
            docno,
            doctype,
            trxdate,
            idcostcenter,
            nmcostcenter,

            0 AS urutan

        FROM data_jurnal

        UNION ALL

        SELECT
            NULL::BIGINT,
            NULL::BIGINT,

            NULL::VARCHAR,
            'TOTAL'::VARCHAR,

            COALESCE(SUM(debet), 0),
            COALESCE(SUM(kredit), 0),

            NULL::VARCHAR,
            NULL::VARCHAR,

            NULL::VARCHAR,
            NULL::VARCHAR,

            NULL::DATE,

            NULL::VARCHAR,
            NULL::VARCHAR,

            1 AS urutan

        FROM data_jurnal

        ORDER BY
            urutan,
            trxdate DESC NULLS LAST,
            jurnal_id,
            id
        ");
    }




    public function q_cek_periode($periode)
    {
        $sql = "SELECT idurut, periode, flagproses, keterangan
                    FROM sc_trx.closeperiod
                    WHERE TRIM(periode) = ?
                    ORDER BY idurut DESC
                    LIMIT 1";
        return $this->db->query($sql, [$periode]);
    }

    /**
     * Periode closing terakhir yang benar-benar TUTUP (sc_trx.closeperiod).
     * Dipakai modul Penerimaan Barang (I.Q.A.6) untuk memvalidasi tombol Update
     * berdasarkan tanggal transaksi (docdate).
     *
     * @return \CodeIgniter\Database\BaseResult
     */
    public function q_periode_closing_last()
    {
        $sql = "
            SELECT idurut,
                   BTRIM(periode::TEXT)                AS periode,
                   tanggal_mulai,
                   tanggal_akhir,
                   TRIM(COALESCE(status_closing, ''))  AS status_closing,
                   TRIM(COALESCE(flagproses, ''))      AS flagproses,
                   TRIM(COALESCE(keterangan, ''))      AS keterangan
              FROM sc_trx.closeperiod
             WHERE UPPER(TRIM(COALESCE(flagproses, ''))) = 'TUTUP'
             ORDER BY tanggal_akhir DESC NULLS LAST, periode DESC
             LIMIT 1
        ";

        return $this->db->query($sql);
    }

    /**
     * Apakah tanggal dokumen ($docdate) sudah masuk periode yang DITUTUP?
     * TRUE  = docdate <= akhir periode closing terakhir yang TUTUP.
     *
     * @param string     $docdate  tanggal dokumen (dd-mm-yyyy / yyyy-mm-dd)
     * @param array|null $closing  baris hasil q_periode_closing_last()
     */
    public function isPeriodeClosed($docdate, $closing = null)
    {
        if ($closing === null) {
            $closing = $this->q_periode_closing_last()->getRowArray();
        }

        if (empty($closing)) {
            return false;
        }

        $doc = $this->_pnmParseDate($docdate);

        if ($doc === null) {
            return false;
        }

        $end = $this->_pnmParseDate($closing['tanggal_akhir'] ?? '');

        /* fallback: tanggal_akhir kosong -> pakai akhir bulan dari periode (yymm) */
        if ($end === null
            && preg_match('/^(\d{2})(\d{2})$/', trim((string) ($closing['periode'] ?? '')), $m)
        ) {
            $end = new \DateTime(sprintf('20%s-%s-01', $m[1], $m[2]));
            $end->modify('last day of this month');
        }

        if ($end === null) {
            return false;
        }

        return $doc->format('Y-m-d') <= $end->format('Y-m-d');
    }

    /**
     * Parser tanggal sederhana (aman untuk 'dd-mm-yyyy' & 'yyyy-mm-dd').
     */
    private function _pnmParseDate($value)
    {
        $value = substr(trim((string) $value), 0, 10);

        if ($value === '') {
            return null;
        }

        foreach (['d-m-Y', 'Y-m-d', 'd/m/Y'] as $format) {
            $dt = \DateTime::createFromFormat('!' . $format, $value);

            if ($dt !== false && $dt->format($format) === $value) {
                return $dt;
            }
        }

        $ts = strtotime($value);

        return $ts !== false ? (new \DateTime())->setTimestamp($ts) : null;
    }




}