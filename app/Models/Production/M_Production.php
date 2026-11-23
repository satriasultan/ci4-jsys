<?php

namespace App\Models\Production;

use CodeIgniter\Model;

class M_Production extends Model
{
    /**
     * Order docno natural:
     * prefix (WO, WO2, WO3, ...) -> infix YYMM -> suffix.
     */
    private function _order_natural_docno($builder, $dir = 'asc')
    {
        $dir = strtolower((string) $dir) === 'desc' ? 'desc' : 'asc';

        $builder->orderBy(
            "COALESCE(NULLIF(substring(trim(split_part(trim(docno), '/', 1)) from '[0-9]+'), '')::int, 0)",
            $dir,
            false
        );
        $builder->orderBy("trim(split_part(trim(docno), '/', 2))", $dir, false);
        $builder->orderBy("trim(split_part(trim(docno), '/', 3))", $dir, false);
        $builder->orderBy("trim(docno)", $dir, false);
    }

    //STANDART COST



    var $mst_standart_cost_mst_view = "(select A.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,to_char(a.activedate,'dd-mm-yyyy') as activedate1,to_char(a.inputdate,'dd-mm-yyyy') as inputdate1,to_char(a.updatedate,'dd-mm-yyyy') as updatedate1,z.uraian as nmstatus,kdtrx,z.jenistrx from sc_mst.standart_cost_mst a 
left outer join sc_mst.trxtype z on trim(coalesce(z.jenistrx,''))='I.R.A.1' and trim(a.status)=trim(z.kdtrx)) as x";
    var $mst_standart_cost_mst_view_column = array('docno','cabang','pemohon','docdate','activedate','docdate1','activedate1','description');
    var $mst_standart_cost_mst_view_order = array('docno' => 'desc'); // default order
    // index kolom grid (#tstandart_cost) => kolom database asli (urut pakai ISO, bukan dd-mm-yyyy)
    var $mst_standart_cost_mst_view_sortable = array(2 => 'docno', 5 => 'docdate', 6 => 'activedate', 11 => 'inputdate', 13 => 'updatedate');
    private function _get_mst_standart_cost_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->mst_standart_cost_mst_view);

        // ================= FILTER MODAL GRID =================
        if (!empty(trim($_POST['f_docno'] ?? ''))) {
            $builder->like('trim(upper(docno))', strtoupper(trim($_POST['f_docno'])));
        }

        if (!empty(trim($_POST['f_cabang'] ?? ''))) {
            $builder->where('trim(upper(cabang))', strtoupper(trim($_POST['f_cabang'])));
        }

        if (!empty(trim($_POST['f_pemohon'] ?? ''))) {
            $builder->like('trim(upper(pemohon))', strtoupper(trim($_POST['f_pemohon'])));
        }

        if (!empty(trim($_POST['f_status'] ?? ''))) {
            $builder->where('trim(status)', strtoupper(trim($_POST['f_status'])));
        }

        // rentang docdate: "dd-mm-yyyy - dd-mm-yyyy"
        if (!empty(trim($_POST['tglrange'] ?? ''))) {
            $dates = explode(' - ', trim($_POST['tglrange']));
            if (count($dates) == 2) {
                $start = \DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                $end   = \DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                if ($start && $end) {
                    $builder->where("docdate BETWEEN '" . $start->format('Y-m-d') . "' AND '" . $end->format('Y-m-d') . "'");
                }
            }
        }
        // ===============================================

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
            $col = $_POST['order']['0']['column'];
            if (isset($this->mst_standart_cost_mst_view_sortable[$col])) {
                $builder->orderBy($this->mst_standart_cost_mst_view_sortable[$col], $_POST['order']['0']['dir']);
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
    var $tmp_standart_cost_dtl_view_column = array('idbarang','nmbarang','unit','actualcost','lastcost','newcost','description');
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
    var $mst_standart_cost_dtl_view_column = array('idurut', 'idbarang','nmbarang','unit','actualcost','lastcost','newcost','description');
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
        $builder->where("docno = '$docno'");
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
    var $tmp_biaya_standart_dtl_view_column = array('idbarang','nmbarang','unit','actualcost','lastcost','newcost','description');
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
    var $mst_biaya_standart_dtl_view_column = array('idbarang','nmbarang','unit','actualcost','lastcost','newcost','description');
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
        $builder->where("docno = '$docno'");
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if ($__col === 2) {
                $this->_order_natural_docno($builder, $__dir);
            } elseif ($__col != 0 && isset($this->trx_bom_mst_view_column[$__col - 1])) {
                $builder->orderBy($this->trx_bom_mst_view_column[$__col - 1], $__dir);
            }
        } else {
            $this->_order_natural_docno($builder, 'asc');
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
    var $tmp_bom_material_dtl_view_column = array('idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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
    var $trx_bom_material_dtl_view_column = array('idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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
        // $builder->where("inputby = '$nama'");
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
    var $tmp_bom_cost_dtl_view_column = array('idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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
    var $trx_bom_cost_dtl_view_column = array('idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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
        // $builder->where("inputby = '$nama'");
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
    var $tmp_bom_wip_dtl_view_column = array('idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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
    var $trx_bom_wip_dtl_view_column = array('idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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
        // $builder->where("inputby = '$nama'");
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
    var $trx_workingorder_mst_view_column = array('docno','docdate');
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if ($__col === 2) {
                $this->_order_natural_docno($builder, $__dir);
            } elseif ($__col != 0 && isset($this->trx_workingorder_mst_view_column[$__col - 1])) {
                $builder->orderBy($this->trx_workingorder_mst_view_column[$__col - 1], $__dir);
            }
        } else {
            $this->_order_natural_docno($builder, 'asc');
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
    var $tmp_workingorder_bom_mst_view_column = array('docno','desc_bom','idbarang_jadi','nmbarang_jadi','buildfor','buildunit','ttlprice');
    var $tmp_workingorder_bom_mst_view_order = array("docno" => 'desc'); // default order
    private function _get_query_tmp_workingorder_bom_mst($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_workingorder_bom_mst_view);
        $builder->orderBy('docno');

        $i = 0;

        // $builder->where("docref = '$docno'");
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
    var $trx_workingorder_bom_mst_view_column = array('docno','desc_bom','idbarang_jadi','nmbarang_jadi','buildfor','buildunit','ttlprice');
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
        // $builder->where("inputby = '$nama'");
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->trx_workingorder_bom_mst_view_column[$__col])) {
                $builder->orderBy($this->trx_workingorder_bom_mst_view_column[$__col], $__dir);
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
    var $tmp_workingorder_material_dtl_view_column = array('docref','docno','idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->tmp_workingorder_material_dtl_view_column[$__col])) {
                $builder->orderBy($this->tmp_workingorder_material_dtl_view_column[$__col], $__dir);
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
    var $trx_workingorder_material_dtl_view_column = array('docref','docno','idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
    var $trx_workingorder_material_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_workingorder_material_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_material_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docref = '$docno'");
        // $builder->where("inputby = '$nama'");
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->trx_workingorder_material_dtl_view_column[$__col])) {
                $builder->orderBy($this->trx_workingorder_material_dtl_view_column[$__col], $__dir);
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
    var $tmp_workingorder_cost_dtl_view_column = array('docref','docno','idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->tmp_workingorder_cost_dtl_view_column[$__col])) {
                $builder->orderBy($this->tmp_workingorder_cost_dtl_view_column[$__col], $__dir);
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
    var $trx_workingorder_cost_dtl_view_column = array('docref','docno','idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
    var $trx_workingorder_cost_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_workingorder_cost_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_cost_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docref = '$docno'");
        // $builder->where("inputby = '$nama'");
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->trx_workingorder_cost_dtl_view_column[$__col])) {
                $builder->orderBy($this->trx_workingorder_cost_dtl_view_column[$__col], $__dir);
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
    var $tmp_workingorder_wip_dtl_view_column = array('docref','docno','idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->tmp_workingorder_wip_dtl_view_column[$__col])) {
                $builder->orderBy($this->tmp_workingorder_wip_dtl_view_column[$__col], $__dir);
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
    var $trx_workingorder_wip_dtl_view_column = array('docref','docno','idbarang','nmbarang','qty','unit','standartcost','totalcost','description');
    var $trx_workingorder_wip_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_workingorder_wip_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_workingorder_wip_dtl_view);
        $builder->orderBy('idurut');

        $i = 0;

        $builder->where("docno = '$docno'");
        // $builder->where("inputby = '$nama'");
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if (isset($this->trx_workingorder_wip_dtl_view_column[$__col])) {
                $builder->orderBy($this->trx_workingorder_wip_dtl_view_column[$__col], $__dir);
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if ($__col === 2) {
                $this->_order_natural_docno($builder, $__dir);
            } elseif ($__col != 0 && isset($this->t_front_woe_view_column[$__col - 1])) {
                $builder->orderBy($this->t_front_woe_view_column[$__col - 1], $__dir);
            }
        } else {
            $this->_order_natural_docno($builder, 'asc');
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if ($__col === 2) {
                $this->_order_natural_docno($builder, $__dir);
            } elseif ($__col != 0 && isset($this->trx_materialrelease_mst_view_column[$__col - 1])) {
                $builder->orderBy($this->trx_materialrelease_mst_view_column[$__col - 1], $__dir);
            }
        } else {
            $this->_order_natural_docno($builder, 'asc');
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
    var $tmp_materialrelease_dtl_view_column = array('issub','idbarang','nmbarang','spec','qty','unit','description');
    var $tmp_materialrelease_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_tmp_materialrelease_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->tmp_materialrelease_dtl_view);

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
            $colIndex = (int) $_POST['order']['0']['column'];
            $orderMap = array(
                1 => 'issub',
                2 => 'idbarang',
                3 => 'nmbarang',
                4 => 'spec',
                5 => 'qty',
                6 => null,      // kolom Stok (dihitung on-the-fly, tidak ikut sorting)
                7 => 'unit',
                8 => 'description'
            );
            if ($colIndex !== 0 && isset($orderMap[$colIndex]) && $orderMap[$colIndex] !== null){
                $builder->orderBy($orderMap[$colIndex], $_POST['order']['0']['dir']);
                $builder->orderBy('COALESCE(updatedate, inputdate)', 'DESC', false);
            } else {
                // default: baris terakhir di-update/insert tampil paling atas
                $builder->orderBy('COALESCE(updatedate, inputdate)', 'DESC', false);
            }
        }
        else
        {
            // default: baris terakhir di-update/insert tampil paling atas
            $builder->orderBy('COALESCE(updatedate, inputdate)', 'DESC', false);
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
    var $trx_materialrelease_dtl_view_column = array('issub','idbarang','nmbarang','spec','qty','unit','description');
    var $trx_materialrelease_dtl_view_order = array("idurut" => 'desc'); // default order
    private function _get_query_trx_materialrelease_dtl($docno)
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->trx_materialrelease_dtl_view);

        $i = 0;

        $builder->where("docno = '$docno'");
        // $builder->where("inputby = '$nama'");
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
            $colIndex = (int) $_POST['order']['0']['column'];
            if ($colIndex !== 0 && isset($this->trx_materialrelease_dtl_view_column[$colIndex-1])){
                $builder->orderBy($this->trx_materialrelease_dtl_view_column[$colIndex-1], $_POST['order']['0']['dir']);
                $builder->orderBy('COALESCE(updatedate, inputdate)', 'DESC', false);
            } else {
                // default: baris terakhir di-update/insert tampil paling atas
                $builder->orderBy('COALESCE(updatedate, inputdate)', 'DESC', false);
            }
        }
        else
        {
            // default: baris terakhir di-update/insert tampil paling atas
            $builder->orderBy('COALESCE(updatedate, inputdate)', 'DESC', false);
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if ($__col === 2) {
                $this->_order_natural_docno($builder, $__dir);
            } elseif ($__col != 0 && isset($this->trx_penerimaanbp_mst_view_column[$__col - 1])) {
                $builder->orderBy($this->trx_penerimaanbp_mst_view_column[$__col - 1], $__dir);
            }
        } else {
            $this->_order_natural_docno($builder, 'asc');
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
    var $tmp_penerimaanbp_dtl_view_column = array('issub','idbarang','nmbarang','spec','qty','unit','harga','nilai','description');
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
    var $trx_penerimaanbp_dtl_view_column = array('issub','idbarang','nmbarang','spec','qty','unit','harga','nilai','description');
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
        // $builder->where("inputby = '$nama'");
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

        if (isset($_POST['order']['0']['column'])) {
            $__col = (int) $_POST['order']['0']['column'];
            $__dir = $_POST['order']['0']['dir'] ?? 'asc';

            if ($__col === 2) {
                $this->_order_natural_docno($builder, $__dir);
            } elseif ($__col != 0 && isset($this->trx_bpnm_mst_view_column[$__col - 1])) {
                $builder->orderBy($this->trx_bpnm_mst_view_column[$__col - 1], $__dir);
            }
        } else {
            $this->_order_natural_docno($builder, 'asc');
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
    var $tmp_bpnm_dtl_view_column = array('issub','idbarang','nmbarang','spec','qty','unit','harga','nilai','description');
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
    var $trx_bpnm_dtl_view_column = array('issub','idbarang','nmbarang','spec','qty','unit','harga','nilai','description');
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
        // $builder->where("inputby = '$nama'");
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



    public function q_cek_periode($periode)
    {
        $sql = "SELECT idurut, periode, flagproses, keterangan
                    FROM sc_trx.closeperiod
                    WHERE TRIM(periode) = ?
                    ORDER BY idurut DESC
                    LIMIT 1";
        return $this->db->query($sql, [$periode]);
    }

    /* ============================================================
       SPRINT 1 - MATERIAL RELEASE (I.R.A.8)
       Helper tambahan untuk validasi & pengaman.
       Tidak mengubah method yang sudah ada di atas.
       ============================================================ */

    /* 1 baris draft material release milik user.
       $docno boleh '' -> ambil draft terbaru milik user tsb. */
    public function q_tmp_materialrelease_doc($docno, $inputby)
    {
        $sql = "SELECT *
                    FROM sc_tmp.materialrelease_mst
                    WHERE coalesce(inputby,'') = ?
                      AND (? = '' OR trim(docno) = trim(?))
                    ORDER BY inputdate DESC
                    LIMIT 1";
        return $this->db->query($sql, [$inputby, $docno, $docno]);
    }

    /* Statistik detail draft : jumlah baris, total qty, jumlah baris qty <= 0 */
    public function q_tmp_materialrelease_dtl_stat($docno, $inputby)
    {
        $sql = "SELECT count(*) as jml,
                       coalesce(sum(coalesce(qty,0)),0) as totalqty,
                       coalesce(sum(case when coalesce(qty,0) <= 0 then 1 else 0 end),0) as qtyinvalid
                    FROM sc_tmp.materialrelease_dtl
                    WHERE trim(docno) = trim(?)
                      AND coalesce(inputby,'') = ?";
        return $this->db->query($sql, [$docno, $inputby]);
    }

    /* Status WO. Dipakai untuk aturan: hanya WO status 'F' yang boleh ditarik. */
    public function q_wo_status($wono)
    {
        $sql = "SELECT trim(status) as status
                    FROM sc_trx.workingorder_mst
                    WHERE trim(docno) = trim(?)
                    LIMIT 1";
        return $this->db->query($sql, [$wono]);
    }

    /* Jumlah dokumen turunan yang masih aktif menarik sebuah WO.
       Dipakai untuk aturan: WO sudah 'A' (completed) -> dokumen turunan terkunci. */
    public function q_wo_downstream($wono)
    {
        $sql = "SELECT
                    (SELECT count(*) FROM sc_trx.woe
                        WHERE trim(wono) = trim(?) and coalesce(status,'') <> 'C') as woe,
                    (SELECT count(*) FROM sc_trx.materialrelease_mst
                        WHERE trim(wono) = trim(?) and coalesce(status,'') <> 'C') as mr,
                    (SELECT count(*) FROM sc_trx.penerimaanbp_mst
                        WHERE trim(wono) = trim(?) and coalesce(status,'') <> 'C') as pbp,
                    (SELECT count(*) FROM sc_trx.bpnm_mst
                        WHERE trim(wono) = trim(?) and coalesce(status,'') <> 'C') as bpnm";
        return $this->db->query($sql, [$wono, $wono, $wono, $wono]);
    }

    /* ============================================================
       LOG / RIWAYAT PERUBAHAN BOM WORKING ORDER
       Method di bawah ini additive & read-only. Tidak mengubah
       method yang sudah ada.
       ============================================================ */

    /* Dokumen turunan yang header-nya akan mengikuti BOM baru. */
    public function q_wo_bom_impact($wono, $bomno)
    {
        $sql = "
            SELECT 'WOE' AS modul,
                   TRIM(m.docno) AS docno,
                   TRIM(m.status) AS status,
                   TRIM(m.bomno) AS bomno,
                   TRIM(m.idbarang_jadi) AS idbarang_jadi,
                   m.nmbarang_jadi AS nmbarang_jadi,
                   m.buildfor AS buildfor,
                   NULL::text AS buildunit,
                   0 AS detail_count,
                   FALSE AS posted
              FROM sc_trx.woe m
             WHERE TRIM(m.wono) = TRIM(?)
               AND TRIM(COALESCE(m.bomno, '')) = TRIM(?)
               AND COALESCE(TRIM(m.status), '') NOT IN ('C', 'E')

            UNION ALL

            SELECT 'MATERIAL RELEASE' AS modul,
                   TRIM(m.docno) AS docno,
                   TRIM(m.status) AS status,
                   TRIM(m.bomno) AS bomno,
                   TRIM(m.idbarang_jadi) AS idbarang_jadi,
                   m.nmbarang_jadi AS nmbarang_jadi,
                   m.buildfor AS buildfor,
                   m.buildunit AS buildunit,
                   (SELECT COUNT(*) FROM sc_trx.materialrelease_dtl d
                     WHERE TRIM(d.docno) = TRIM(m.docno)) AS detail_count,
                   EXISTS (SELECT 1 FROM sc_trx.transaction_dt t
                            WHERE TRIM(t.docno) = TRIM(m.docno)) AS posted
              FROM sc_trx.materialrelease_mst m
             WHERE TRIM(m.wono) = TRIM(?)
               AND TRIM(COALESCE(m.bomno, '')) = TRIM(?)
               AND COALESCE(TRIM(m.status), '') NOT IN ('C', 'E')

            UNION ALL

            SELECT 'PENERIMAAN BP' AS modul,
                   TRIM(m.docno) AS docno,
                   TRIM(m.status) AS status,
                   TRIM(m.bomno) AS bomno,
                   TRIM(m.idbarang_jadi) AS idbarang_jadi,
                   m.nmbarang_jadi AS nmbarang_jadi,
                   m.buildfor AS buildfor,
                   m.buildunit AS buildunit,
                   (SELECT COUNT(*) FROM sc_trx.penerimaanbp_dtl d
                     WHERE TRIM(d.docno) = TRIM(m.docno)) AS detail_count,
                   EXISTS (SELECT 1 FROM sc_trx.transaction_dt t
                            WHERE TRIM(t.docno) = TRIM(m.docno)) AS posted
              FROM sc_trx.penerimaanbp_mst m
             WHERE TRIM(m.wono) = TRIM(?)
               AND TRIM(COALESCE(m.bomno, '')) = TRIM(?)
               AND COALESCE(TRIM(m.status), '') NOT IN ('C', 'E')

            UNION ALL

            SELECT 'BPNM' AS modul,
                   TRIM(m.docno) AS docno,
                   TRIM(m.status) AS status,
                   TRIM(m.bomno) AS bomno,
                   TRIM(m.idbarang_jadi) AS idbarang_jadi,
                   m.nmbarang_jadi AS nmbarang_jadi,
                   m.buildfor AS buildfor,
                   m.buildunit AS buildunit,
                   (SELECT COUNT(*) FROM sc_trx.bpnm_dtl d
                     WHERE TRIM(d.docno) = TRIM(m.docno)) AS detail_count,
                   EXISTS (SELECT 1 FROM sc_trx.transaction_dt t
                            WHERE TRIM(t.docno) = TRIM(m.docno)) AS posted
              FROM sc_trx.bpnm_mst m
             WHERE TRIM(m.wono) = TRIM(?)
               AND TRIM(COALESCE(m.bomno, '')) = TRIM(?)
               AND COALESCE(TRIM(m.status), '') NOT IN ('C', 'E')

             ORDER BY modul, docno
        ";

        return $this->db->query($sql, [$wono, $bomno, $wono, $bomno, $wono, $bomno, $wono, $bomno]);
    }

    /* Draft transaksi turunan (status E) yang masih menggantung. */
    public function q_wo_bom_active_draft($wono)
    {
        $sql = "
            SELECT 'MATERIAL RELEASE' AS modul, TRIM(docno) AS docno, TRIM(inputby) AS inputby
              FROM sc_tmp.materialrelease_mst
             WHERE TRIM(wono) = TRIM(?) AND UPPER(TRIM(status)) = 'E'
            UNION ALL
            SELECT 'PENERIMAAN BP', TRIM(docno), TRIM(inputby)
              FROM sc_tmp.penerimaanbp_mst
             WHERE TRIM(wono) = TRIM(?) AND UPPER(TRIM(status)) = 'E'
            UNION ALL
            SELECT 'BPNM', TRIM(docno), TRIM(inputby)
              FROM sc_tmp.bpnm_mst
             WHERE TRIM(wono) = TRIM(?) AND UPPER(TRIM(status)) = 'E'
            UNION ALL
            SELECT 'WOE', TRIM(docno), TRIM(inputby)
              FROM sc_tmp.woe
             WHERE TRIM(wono) = TRIM(?) AND UPPER(TRIM(status)) = 'E'
             ORDER BY modul, docno
        ";

        return $this->db->query($sql, [$wono, $wono, $wono, $wono]);
    }

    /* Riwayat perubahan BOM sebuah WO (terbaru di atas). */
    public function q_wo_bom_history($wono)
    {
        $sql = "SELECT id,
                       TRIM(docno) AS docno,
                       TRIM(jenis) AS jenis,
                       TRIM(bomno_lama) AS bomno_lama,
                       TRIM(bomno_baru) AS bomno_baru,
                       TRIM(bomno_referensi) AS bomno_referensi,
                       idbarang_lama, idbarang_baru, nmbarang_baru,
                       buildfor_lama, buildfor_baru, buildunit_baru, desc_bom_baru,
                       keterangan,
                       TRIM(status) AS status,
                       affected_count, affected_list,
                       inputby, inputdate, applyby, applydate
                  FROM sc_log.workingorder_bom_change
                 WHERE TRIM(docno) = TRIM(?)
                 ORDER BY inputdate DESC, id DESC";

        return $this->db->query($sql, [$wono]);
    }

    /* BOM yang saat ini menempel pada WO (trx). */
    public function q_wo_current_bom($wono)
    {
        $sql = "SELECT TRIM(docno) AS bomno,
                       idbarang_jadi, nmbarang_jadi,
                       buildfor, buildunit, desc_bom
                  FROM sc_trx.workingorder_bom_mst
                 WHERE TRIM(docref) = TRIM(?)
                 ORDER BY docno";

        return $this->db->query($sql, [$wono]);
    }

    /* Apakah WO sudah punya riwayat perubahan BOM. */
    public function q_wo_bom_history_exists($wono)
    {
        $sql = "SELECT COUNT(*) AS jml
                  FROM sc_log.workingorder_bom_change
                 WHERE TRIM(docno) = TRIM(?)";

        return $this->db->query($sql, [$wono]);
    }

    /* Log PENDING terakhir untuk sebuah WO (opsional milik user). */
    public function q_wo_bom_pending($wono, $inputby = null)
    {
        $sql = "SELECT *
                  FROM sc_log.workingorder_bom_change
                 WHERE TRIM(docno) = TRIM(?)
                   AND UPPER(TRIM(status)) = 'PENDING'";
        $bind = [$wono];

        if ($inputby !== null && trim((string) $inputby) !== '') {
            $sql .= " AND TRIM(inputby) = TRIM(?)";
            $bind[] = $inputby;
        }

        $sql .= " ORDER BY inputdate DESC, id DESC LIMIT 1";

        return $this->db->query($sql, $bind);
    }

    /* Jumlah dokumen turunan aktif (trx maupun draft tmp). */
    public function q_wo_has_downstream($wono)
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM sc_trx.woe
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_trx.materialrelease_mst
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_trx.penerimaanbp_mst
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_trx.bpnm_mst
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_tmp.materialrelease_mst
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_tmp.penerimaanbp_mst
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_tmp.bpnm_mst
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C') +
                    (SELECT COUNT(*) FROM sc_tmp.woe
                        WHERE TRIM(wono) = TRIM(?) AND COALESCE(TRIM(status), '') <> 'C')
                    AS total";

        return $this->db->query($sql, array_fill(0, 8, $wono));
    }

    /* Statistik detail draft Penerimaan Barang Produksi (Sprint 2) */
    public function q_tmp_penerimaanbp_dtl_stat($docno, $inputby)
    {
        $sql = "SELECT count(*) as jml,
                       coalesce(sum(coalesce(qty,0)),0) as totalqty,
                       coalesce(sum(case when coalesce(qty,0) <= 0 then 1 else 0 end),0) as qtyinvalid
                    FROM sc_tmp.penerimaanbp_dtl
                    WHERE trim(docno) = trim(?)
                      AND coalesce(inputby,'') = ?";
        return $this->db->query($sql, [$docno, $inputby]);
    }

    /* Statistik detail draft Biaya Produksi Non Material (Sprint 2) */
    public function q_tmp_bpnm_dtl_stat($docno, $inputby)
    {
        $sql = "SELECT count(*) as jml,
                       coalesce(sum(coalesce(qty,0)),0) as totalqty,
                       coalesce(sum(case when coalesce(qty,0) <= 0 then 1 else 0 end),0) as qtyinvalid
                    FROM sc_tmp.bpnm_dtl
                    WHERE trim(docno) = trim(?)
                      AND coalesce(inputby,'') = ?";
        return $this->db->query($sql, [$docno, $inputby]);
    }


    /* ============================================================
       LAPORAN JURNAL TRANSAKSI (detail Material Release / PBP / BPNM)
       Sumber: sc_trx.jurnal_hd + sc_trx.jurnal_dt (hanya yang POSTED).
       Kolom "Pemohon" diambil dari mst modul produksi terkait (mr/pbp/bpnm).
       ============================================================ */
    public function q_laporan_jurnal_transaksi($params = '')
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
                COALESCE(TRIM(mr.pemohon), TRIM(pbp.pemohon), TRIM(bnm.pemohon), '') AS nmpemohon
            FROM sc_trx.jurnal_dt jd
            INNER JOIN sc_trx.jurnal_hd jh
                ON jh.id = jd.jurnal_id
            LEFT JOIN sc_mst.coa coa
                ON TRIM(coa.idcoa) = TRIM(jd.idcoa)
            LEFT JOIN sc_trx.materialrelease_mst mr
                ON TRIM(mr.docno) = TRIM(jh.docno)
            LEFT JOIN sc_trx.penerimaanbp_mst pbp
                ON TRIM(pbp.docno) = TRIM(jh.docno)
            LEFT JOIN sc_trx.bpnm_mst bnm
                ON TRIM(bnm.docno) = TRIM(jh.docno)
            WHERE 1=1
              AND jh.status = 'POSTED'
              AND jd.status = 'POSTED'
              $params
        )
        SELECT
            id, jurnal_id, idcoa, nmcoa, debet, kredit,
            ref_docno, ref_doctype, docno, doctype, trxdate, nmpemohon,
            0 AS urutan
        FROM data_jurnal

        UNION ALL

        SELECT
            NULL::BIGINT, NULL::BIGINT, NULL::VARCHAR, 'TOTAL'::VARCHAR,
            COALESCE(SUM(debet), 0), COALESCE(SUM(kredit), 0),
            NULL::VARCHAR, NULL::VARCHAR, NULL::VARCHAR, NULL::VARCHAR, NULL::DATE,
            NULL::VARCHAR, 1 AS urutan
        FROM data_jurnal

        ORDER BY urutan, trxdate DESC NULLS LAST, jurnal_id, id
        ");
    }



    /* ============================================================
       RINGKASAN BOM (kartu dashboard di halaman list BOM)
       ============================================================ */
    public function q_summary_bom()
    {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM sc_trx.bom_mst) AS total_bom,
                (SELECT COUNT(*) FROM sc_trx.bom_mst WHERE BTRIM(status) = 'A') AS bom_released,
                (SELECT COUNT(*) FROM sc_trx.bom_mst WHERE BTRIM(status) IN ('I','E')) AS bom_draft,
                (SELECT COUNT(*) FROM sc_trx.bom_dtl) AS total_komponen
        ";
        return $this->db->query($sql)->getRowArray();
    }



    /* ============================================================
       RINGKASAN WORKING ORDER (kartu dashboard di halaman list WO)
       finish = status A (completed), unfinish = selain A/C/D.
       ============================================================ */
    public function q_summary_wo()
    {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM sc_trx.workingorder_mst WHERE BTRIM(status) NOT IN ('C','D')) AS total_wo,
                (SELECT COUNT(*) FROM sc_trx.workingorder_mst WHERE BTRIM(status) = 'A') AS wo_finish,
                (SELECT COUNT(*) FROM sc_trx.workingorder_mst WHERE BTRIM(status) NOT IN ('A','C','D')) AS wo_unfinish,
                (SELECT COUNT(*) FROM sc_trx.workingorder_mst WHERE BTRIM(status) = 'F') AS wo_final
        ";
        return $this->db->query($sql)->getRowArray();
    }


}