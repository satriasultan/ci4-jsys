<?php

namespace App\Models\Stock;

use CodeIgniter\Model;

class M_Bbk extends Model
{

    function q_tmp_item_bbk_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocation ,z.uraian as nmstatus  from sc_tmp.item_bbk_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null $param");
    }
    function q_tmp_item_bbk_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_bbk_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null  $param order by id asc");
    }
    function q_trx_item_bbk_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocation ,z.uraian as nmstatus,d.nmcostcenter  from sc_trx.item_bbk_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.costcenter d on d.idcostcenter=a.idsection
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null $param");
    }

    function q_trx_item_bbk_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa,f.nmcostcenter,g.nmgroup from sc_trx.item_bbk_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.costcenter f on f.idcostcenter=a.idsection 
 left outer join sc_mst.mgroup g on g.idgroup=b.idgroup			   
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null  $param order by id asc");
    }

    var $t_tmp_bbk_dtl_view = "(select a.*,b.nmbarang,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_bbk_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x";
    var $t_tmp_bbk_dtl_view_column = array('docno','docdate1','docref','nmlocationfrom','nmlocationto','inputby');
    var $t_tmp_bbk_dtl_view_order = array("docno" => 'desc',"id" => 'asc'); // default order
    private function _get_query_t_tmp_bbk_dtl()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_tmp_bbk_dtl_view);
        $i = 0;

        //diblockir per warehouse seharusnya
        $builder->where("docno = '$nama'");

        foreach ($this->t_tmp_bbk_dtl_view_column as $item)
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

                if(count($this->t_tmp_bbk_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_bbk_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_bbk_dtl_view_order))
        {
            $order = $this->t_tmp_bbk_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_tmp_bbk_dtl_view(){
        $builder = $this->_get_query_t_tmp_bbk_dtl();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tmp_bbk_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_bbk_dtl();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_bbk_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_tmp_bbk_dtl();
        return $builder->countAllResults();
    }
    public function get_t_tmp_bbk_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_tmp_bbk_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }



/* BBK TRANSACTION */


    var $t_trx_bbk_mst_view = "(select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocation ,z.uraian as nmstatus  from sc_trx.item_bbk_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x";
    var $t_trx_bbk_mst_view_column = array('docno','docdate1','docref','docjns','nmlocation','idlocation','nmstatus','inputby');
    var $t_trx_bbk_mst_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_trx_bbk_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_trx_bbk_mst_view);
        $i = 0;

        //diblockir per warehouse seharusnya
        //$builder->where("docno = '$nama'");

        foreach ($this->t_trx_bbk_mst_view_column as $item)
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

                if(count($this->t_trx_bbk_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_bbk_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_bbk_mst_view_order))
        {
            $order = $this->t_trx_bbk_mst_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_trx_bbk_mst_view(){
        $builder = $this->_get_query_t_trx_bbk_mst();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_bbk_mst_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_bbk_mst();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_bbk_mst_view_count_all()
    {
        $builder = $this->_get_query_t_trx_bbk_mst();
        return $builder->countAllResults();
    }
    public function get_t_trx_bbk_mst_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_bbk_mst();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

/* TRANSACTION BBK DTL */

    var $t_trx_bbk_dtl_view = "(select a.*,b.nmbarang,b.nmgroup,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa,f.nmcostcenter from sc_trx.item_bbk_dtl a
 left outer join sc_mst.tv_mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.costcenter f on a.idsection=f.idcostcenter
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x";
    var $t_trx_bbk_dtl_view_column = array('docno','lasttrxdate1','docref','nmlocation','inputby','nmbarang','nmarea','idsection','idbarang');
    var $t_trx_bbk_dtl_view_order = array("docno" => 'desc',"id" => 'asc'); // default order
    private function _get_query_t_trx_bbk_dtl()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));
        $roleid=trim($this->session->get('roleid'));

        $builder = $this->db->table($this->t_trx_bbk_dtl_view);
        $i = 0;

        if ($roleid === 'TR'){
            $builder->where("idlocation ='$loccode'");
        }

        /* $builder->where("status in ('A','S')");  OPEN ALL STATUS*/
        if($this->request->getPost('status')) {
            $ket = trim($this->request->getPost('status_filter'));
            if ($ket === 'A' or $ket === 'S') {
                $builder->where("status in ('A','S')");
            } else {
                $builder->where("status ='$ket'");
            }

        }
        if($this->request->getPost('idbarang')) {
            $ket1 = trim($this->request->getPost('idbarang'));
            $builder->where("idbarang ='$ket1'");
        }
        if($this->request->getPost('idsection')) {
            $ket2 = trim($this->request->getPost('namasupplier'));
            $builder->where("idsection like '%$ket2%'");
        }
        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("to_char(lasttrxdate,'yyyy-mm-dd') BETWEEN '$tgl1' AND '$tgl2'");
        } else {
            $datey = date('Y-m-d');
            $tgl1= date('Y-m-d',strtotime($datey . "-120 days"));
            $tgl2= date('Y-m-d',strtotime($datey . "+30 days"));
            $builder->where("to_char(lasttrxdate,'yyyy-mm-dd') BETWEEN '$tgl1' AND '$tgl2'");
        }

        //diblockir per warehouse seharusnya
        //$builder->where("docno = '$nama'");

        foreach ($this->t_trx_bbk_dtl_view_column as $item)
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

                if(count($this->t_trx_bbk_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_bbk_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_bbk_dtl_view_order))
        {
            $order = $this->t_trx_bbk_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_trx_bbk_dtl_view(){
        $builder = $this->_get_query_t_trx_bbk_dtl();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_bbk_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_bbk_dtl();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_bbk_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_trx_bbk_dtl();
        return $builder->countAllResults();
    }
    public function get_t_trx_bbk_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_bbk_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }






    /* mbbk VOID*/

    function q_tmp_item_void_bbk_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocation ,z.uraian as nmstatus  from sc_tmp.item_void_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null $param");
    }
    function q_tmp_item_void_bbk_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_void_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null  $param order by id asc");
    }
    function q_trx_item_void_bbk_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocation ,z.uraian as nmstatus  from sc_trx.item_void_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null $param");
    }
    function q_trx_item_void_bbk_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_trx.item_void_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x where docno is not null  $param order by id asc");
    }


    var $t_tmp_void_bbk_dtl_view = "(select a.*,b.nmbarang,c1.nmlocation as nmlocation,d1.nmarea as nmarea, to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_void_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.D.1') as x";
    var $t_tmp_void_bbk_dtl_view_column = array('docno','docdate1','docref','nmlocationfrom','nmlocationto','inputby');
    var $t_tmp_void_bbk_dtl_view_order = array("id" => 'desc'); // default order
    private function _get_query_t_tmp_void_bbk_dtl()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_tmp_void_bbk_dtl_view);
        $i = 0;

        //diblockir per warehouse seharusnya
        //$builder->where("docno = '$nama'");

        foreach ($this->t_tmp_void_bbk_dtl_view_column as $item)
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

                if(count($this->t_tmp_void_bbk_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_void_bbk_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_void_bbk_dtl_view_order))
        {
            $order = $this->t_tmp_void_bbk_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }
    function get_t_tmp_void_bbk_dtl_view(){
        $builder = $this->_get_query_t_tmp_void_bbk_dtl();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }
    function t_tmp_void_bbk_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_void_bbk_dtl();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_void_bbk_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_tmp_void_bbk_dtl();
        return $builder->countAllResults();
    }
    public function get_t_tmp_void_bbk_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_tmp_void_bbk_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    function q_tmp_item_pbl_wacc_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus from sc_tmp.item_pbl_wacc_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x
where docno is not null $param");
    }

    function q_trx_item_pbl_wacc_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus from sc_trx.item_pbl_wacc_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x
where docno is not null $param");
    }

    var $t_trx_item_pbl_wacc_dtl_view = "(select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus from sc_trx.item_pbl_wacc_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x";
    var $t_trx_item_pbl_wacc_dtl_view_column = array('docno','idbarang','trxdate1','senddate1','description');
    var $t_trx_item_pbl_wacc_dtl_view_order = array("trxdate" => 'desc'); // default order
    private function _get_query_t_trx_item_pbl_wacc_dtl()
    {
        $builder = $this->db->table($this->t_trx_item_pbl_wacc_dtl_view);
        $i = 0;

        //$builder->where("status in ('P','O','C','D','X')");

        foreach ($this->t_trx_item_pbl_wacc_dtl_view_column as $Purchaseorder)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($Purchaseorder) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($Purchaseorder) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_trx_item_pbl_wacc_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_item_pbl_wacc_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_item_pbl_wacc_dtl_view_order))
        {
            $order = $this->t_trx_item_pbl_wacc_dtl_view_order;
            foreach ($order as $key => $Purchaseorder){
                $builder->orderBy($key, $Purchaseorder);
            }
        }
        return $builder;
    }


    function get_t_trx_item_pbl_wacc_dtl_view(){
        $builder = $this->_get_query_t_trx_item_pbl_wacc_dtl();
        ////$this->_get_query_t_trx_item_pbl_wacc_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_item_pbl_wacc_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_item_pbl_wacc_dtl();
        ////$this->_get_query_t_trx_item_pbl_wacc_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_item_pbl_wacc_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_trx_item_pbl_wacc_dtl();
        return $builder->countAllResults();
    }
    public function get_t_trx_item_pbl_wacc_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_item_pbl_wacc_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }
    /* LBM WINACC */
}
