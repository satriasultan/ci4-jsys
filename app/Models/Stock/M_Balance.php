<?php

namespace App\Models\Stock;

use CodeIgniter\Model;

class M_Balance extends Model
{

    var $t_balance_view = "(select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,coalesce(b.chold,'NO') AS chold,
c.nmlocation,d.nmarea,f.idgroup,f.nmgroup,trim(a.idbarang)||trim(a.batch) as idspec from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea
left outer join sc_mst.mgroup f on b.idgroup=f.idgroup and f.grouptype='BRG'
) as x";
    var $t_balance_view_column = array('idbarang','nmbarang','batch','unit','idlocation','idarea','nmlocation','nmarea','idspec');
    var $t_balance_view_order = array("nmbarang" => 'asc'); // default order
    private function _get_query_t_balance()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));

        $builder = $this->db->table($this->t_balance_view);
        $i = 0;

        $builder->where("chold != 'YES'");
        $builder->where("idlocation = '$loccode'");
        $builder->where("sisa > 0");
        //jika post kosong maka search table di kosongkan
        if(! $_POST['searchfilter']) {
            $builder->where("idbarang = '1'");
        }

        foreach ($this->t_balance_view_column as $item)
        {
            if($_POST['searchfilter']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['searchfilter']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['searchfilter']));
                }

                if(count($this->t_balance_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_balance_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_balance_view_order))
        {
            $order = $this->t_balance_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_balance_view(){
        $builder = $this->_get_query_t_balance();
        ////$this->_get_query_t_balance();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_balance_view_count_filtered()
    {
        $builder = $this->_get_query_t_balance();
        ////$this->_get_query_t_balance();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_balance_view_count_all()
    {
        $builder = $this->_get_query_t_balance();
        return $builder->countAllResults();
    }
    public function get_t_balance_view_by_id($id)
    {
        $builder = $this->_get_query_t_balance();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function cek_user($nama){
        return $this->db->query("select * from sc_mst.user where username='$nama'");
    }

    function getUser($param){
        $query = $this->db->query("select a.*,b.rolename from sc_mst.user a
        left outer join sc_mst.role b on a.roleid=b.roleid
        where username is not null $param ");
        return $query;
    }

    function q_stkgdw($param){
        return $this->db->query("select * from (select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,
c.nmlocation,d.nmarea from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea) as x
where idbarang is not null $param
");
    }

    function q_stkgdw_batch_selecting($param){
        return $this->db->query("select trim(batch) as id,trim(idlocation) as idlocation, trim(batch) as batch,trim(idbarang) as idbarang from sc_mst.stkgdw
where idbarang is not null $param
group by idlocation ,batch,idbarang 
");
    }

    function q_openingstock_tmp_param($param){
        return $this->db->query("select * from (select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,
case when b.nmbarang is null then 'UNIDENTIFIED'
else b.nmbarang end as nmbarang_asal,
case when c.nmlocation is null then 'UNIDENTIFIED'
else c.nmlocation end as nmlocation,
case when d.nmarea is null then 'UNIDENTIFIED'
else d.nmarea end as nmarea
 from sc_tmp.import_opening_stock a 
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
 left outer join sc_mst.marea d on a.idarea=d.idarea
) as x where idbarang is not null $param");
    }

    /* UNIT ITEM */

    var $t_unit_view = "sc_mst.unit";
    var $t_unit_view_column = array('idunit','parentunit','ctype','chold','description');
    var $t_unit_view_order = array("idunit" => 'asc'); // default order
    private function _get_query_t_unit()
    {
        $builder = $this->db->table($this->t_unit_view);
        $i = 0;

        foreach ($this->t_unit_view_column as $item)
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

                if(count($this->t_unit_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_unit_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_unit_view_order))
        {
            $order = $this->t_unit_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_unit_view(){
        $builder = $this->_get_query_t_unit();
        ////$this->_get_query_t_unit();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_unit_view_count_filtered()
    {
        $builder = $this->_get_query_t_unit();
        ////$this->_get_query_t_unit();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_unit_view_count_all()
    {
        $builder = $this->_get_query_t_unit();
        return $builder->countAllResults();
    }
    public function get_t_unit_view_by_id($id)
    {
        $builder = $this->_get_query_t_unit();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_unit_param($param){
        return $this->db->query("select * from (select *,to_char(inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1
from sc_mst.unit ) as x where id is not null $param");
    }

    function q_autoinsert_unit(){
        return $this->db->query("insert into sc_mst.unit
(idunit,parentunit,ctype,conversion_value,chold,inputby,inputdate)
(select unit as idunit,0 as parentunit,'UNIT' as ctype,0,'NO','SYSTEM' as inputby,to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp 
from sc_mst.mbarang where unit not in (select idunit from sc_mst.unit where ctype='UNIT')
group by idunit);");
    }

    function q_item_param($param){
        return $this->db->query("select * from (select trim(a.idbarang) as id,a.idbarang, a.nmbarang, a.idgroup, a.idsubgroup, a.idtype, a.grade, a.lsize, a.deflocation, a.defarea, a.description, a.unit, a.subunit, a.subunitenable, a.lastprice, a.onhand, a.allocated, a.uninvoiced, a.tmpalloca, a.lastrxdate, a.lastrxdoc, a.idbarcode, a.sku, a.expdate, a.batch, a.mfgdate, a.maks_daystock, a.chold, a.inputby, a.inputdate, a.status,
 to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
 c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1
 from sc_mst.mbarang a 
 left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
 left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
 left outer join sc_mst.marea d on a.defarea=d.idarea
) as x where id is not null $param");
    }


    var $t_balance_moving_view = "(select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,
c.nmlocation,d.nmarea,e.nmgroup,trim(a.idbarang)||trim(a.batch) as idspec from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea
left outer join sc_mst.mgroup e on b.idgroup=e.idgroup
) as x";
    var $t_balance_moving_view_column = array('idbarang','nmbarang','batch','unit','idlocation','idarea','nmlocation','nmarea','idspec');
    var $t_balance_moving_view_order = array("nmbarang" => 'asc'); // default order
    private function _get_query_t_balance_moving()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));

        $builder = $this->db->table($this->t_balance_moving_view);
        $i = 0;

        $builder->where("idlocation = '$loccode'");
        $builder->where("sisa > 0");


        //jika post kosong maka search table di kosongkan
//        if($_POST['idbarang']) {
//            $builder->where("idbarang = '" . $_POST['idbarang'] . "'");
//        } else if ($_POST['idposition']) {
//            $builder->where("idarea = '".$_POST['idposition']."'");
//        } else {
//            $builder->where("idbarang = '1'");
//        }
        //jika post kosong maka search table di kosongkan
        if(! $_POST['searchfilter']) {
            $builder->where("idbarang = '1'");
        }

        foreach ($this->t_balance_moving_view_column as $item)
        {
            if($_POST['searchfilter']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['searchfilter']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['searchfilter']));
                }

                if(count($this->t_balance_moving_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_balance_moving_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_balance_moving_view_order))
        {
            $order = $this->t_balance_moving_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_balance_moving_view(){
        $builder = $this->_get_query_t_balance_moving();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_balance_moving_view_count_filtered()
    {
        $builder = $this->_get_query_t_balance_moving();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_balance_moving_view_count_all()
    {
        $builder = $this->_get_query_t_balance_moving();
        return $builder->countAllResults();
    }
    public function get_t_balance_moving_view_by_id($id)
    {
        $builder = $this->_get_query_t_balance_moving();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_balance_moving($param){
        return $this->db->query("select * from (select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,
c.nmlocation,d.nmarea from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea) as x where id is not null $param");

    }

    function q_item_move($param){
        return $this->db->query("select * from (
select a.*,b1.nmlocation,b2.nmarea,b3.nmbarang,c1.nmlocation as nmlocationmove,c2.nmarea as nmareamove from sc_trx.item_move a
left outer join sc_mst.mlocation b1 on a.idlocation=b1.idlocation
left outer join sc_mst.marea b2 on a.idarea=b2.idarea
left outer join sc_mst.mbarang b3 on a.idbarang=b3.idbarang
left outer join sc_mst.mlocation c1 on a.idlocationmove=c1.idlocation
left outer join sc_mst.marea c2 on a.idareamove=c2.idarea) as x
where id is not null $param");
    }

    /* Balance Moving Item */
    var $t_item_move_view = "(
select a.*,b1.nmlocation,b2.nmarea,b3.nmbarang,c1.nmlocation as nmlocationmove,c2.nmarea as nmareamove,z.uraian as nmstatus from sc_trx.item_move a
left outer join sc_mst.mlocation b1 on a.idlocation=b1.idlocation
left outer join sc_mst.marea b2 on a.idarea=b2.idarea
left outer join sc_mst.mbarang b3 on a.idbarang=b3.idbarang
left outer join sc_mst.mlocation c1 on a.idlocationmove=c1.idlocation
left outer join sc_mst.marea c2 on a.idareamove=c2.idarea
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.3') as x";
    var $t_item_move_view_column = array('docno','idbarang','nmbarang','unit','idlocation','idarea','nmlocation','nmarea','nmlocationmove','nmareamove','nmstatus');
    var $t_item_move_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_item_move()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));

        $builder = $this->db->table($this->t_item_move_view);
        $i = 0;

        $builder->where("idlocation = '$loccode'");
        /* OPEN ALL STATUS */
        if($this->request->getPost('status_filter')) {
            $ket = trim($this->request->getPost('status_filter'));
            if ($ket === 'P') {
                $builder->where("status in ('A')");
            } else if ($ket === 'C') {
                $builder->where("status in ('C')");
            } else {
                $builder->where("status ='$ket'");
            }

        }

        if($this->request->getPost('idbarang')) {
            $ket1 = trim($this->request->getPost('idbarang'));
            $builder->where("idbarang ='$ket1'");
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

        foreach ($this->t_item_move_view_column as $item)
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

                if(count($this->t_item_move_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_item_move_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_item_move_view_order))
        {
            $order = $this->t_item_move_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_item_move_view(){
        $builder = $this->_get_query_t_item_move();
        ////$this->_get_query_t_balance();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_item_move_view_count_filtered()
    {
        $builder = $this->_get_query_t_item_move();
        ////$this->_get_query_t_balance();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_item_move_view_count_all()
    {
        $builder = $this->_get_query_t_item_move();
        return $builder->countAllResults();
    }
    public function get_t_item_move_view_by_id($id)
    {
        $builder = $this->_get_query_t_item_move();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    // INPUT TRANSFER TRANSACTIONAL
    function q_tmp_item_transfers_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d2.nmarea as nmareato,z.uraian as nmstatus  from sc_tmp.item_transfers_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x where docno is not null $param order by docno desc");
    }

    function q_tmp_item_transfers_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d1.nmarea as nmareafrom , d2.nmarea as nmareato,to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_transfers_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea  and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x where docno is not null  $param order by id asc");
    }


    var $t_tmp_transfers_dtl_view = "(select a.*,b.nmbarang,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d1.nmarea as nmareafrom , d2.nmarea as nmareato,to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_transfers_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x";
    var $t_tmp_transfers_dtl_view_column = array('idbarang','nmbarang','unit','idlocation','nmlocationfrom');
    var $t_tmp_transfers_dtl_view_order = array("id" => 'asc'); // default order
    private function _get_query_t_tmp_transfers_dtl()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_tmp_transfers_dtl_view);
        $i = 0;

        $builder->where("docno = '$nama'");

        foreach ($this->t_tmp_transfers_dtl_view_column as $item)
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

                if(count($this->t_tmp_transfers_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_transfers_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_transfers_dtl_view_order))
        {
            $order = $this->t_tmp_transfers_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_tmp_transfers_dtl_view(){
        $builder = $this->_get_query_t_tmp_transfers_dtl();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tmp_transfers_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_transfers_dtl();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_transfers_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_tmp_transfers_dtl();
        return $builder->countAllResults();
    }
    public function get_t_tmp_transfers_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_tmp_transfers_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* BALANCE TRANSACTIONAL */
    var $t_trx_transfers_dtl_view = "(select a.*,b.nmbarang,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d1.nmarea as nmareafrom , d2.nmarea as nmareato,to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_trx.item_transfers_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea  and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x";
    var $t_trx_transfers_dtl_view_column = array('docno','idbarang','nmbarang','unit','idlocation','nmlocationfrom');
    var $t_trx_transfers_dtl_view_order = array("inputdate" => 'desc'); // default order
    private function _get_query_t_trx_transfers_dtl()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_trx_transfers_dtl_view);
        $i = 0;



        foreach ($this->t_trx_transfers_dtl_view_column as $item)
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

                if(count($this->t_trx_transfers_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_transfers_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_transfers_dtl_view_order))
        {
            $order = $this->t_trx_transfers_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_trx_transfers_dtl_view(){
        $builder = $this->_get_query_t_trx_transfers_dtl();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_transfers_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_transfers_dtl();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_transfers_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_trx_transfers_dtl();
        return $builder->countAllResults();
    }
    public function get_t_trx_transfers_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_transfers_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }




    var $t_trx_transfers_mst_view = "(select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d2.nmarea as nmareato,z.uraian as nmstatus  from sc_trx.item_transfers_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x";
    var $t_trx_transfers_mst_view_column = array('docno','docdate1','docref','nmlocationfrom','nmlocationto','inputby');
    var $t_trx_transfers_mst_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_trx_transfers_mst()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_trx_transfers_mst_view);
        $i = 0;

        //diblockir per warehouse seharusnya
        //$builder->where("docno = '$nama'");

        foreach ($this->t_trx_transfers_mst_view_column as $item)
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

                if(count($this->t_trx_transfers_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_transfers_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_transfers_mst_view_order))
        {
            $order = $this->t_trx_transfers_mst_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_trx_transfers_mst_view(){
        $builder = $this->_get_query_t_trx_transfers_mst();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_transfers_mst_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_transfers_mst();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_transfers_mst_view_count_all()
    {
        $builder = $this->_get_query_t_trx_transfers_mst();
        return $builder->countAllResults();
    }
    public function get_t_trx_transfers_mst_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_transfers_mst();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_trx_item_transfers_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d2.nmarea as nmareato,z.uraian as nmstatus  from sc_trx.item_transfers_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x where docno is not null $param order by docno desc");
    }

    function q_trx_item_transfers_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocationfrom,c2.nmlocation as nmlocationto,d1.nmarea as nmareafrom , d2.nmarea as nmareato,to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_trx.item_transfers_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.mlocation c2 on a.idlocationtrans=c2.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.marea d2 on a.idareatrans=d2.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.6') as x where docno is not null  $param order by id asc");
    }

    // AJUSTMENT QUERY
    function q_tmp_item_ajustment_mst($param){
        return $this->db->query("select * from (select a.*,to_char(a.docdate,'dd-mm-yyyy') as docdate1,c1.nmlocation as nmlocationfrom,z.uraian as nmstatus  from sc_tmp.item_ajustment_mst a
left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.4') as x where docno is not null $param order by docno desc");
    }

    function q_tmp_item_ajustment_dtl($param){
        return $this->db->query("select * from (select a.*,b.nmbarang,c1.nmlocation as nmlocationfrom,d1.nmarea as nmareafrom , to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_ajustment_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea  and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.4') as x where docno is not null  $param order by id asc");
    }

    var $t_tmp_ajustment_dtl_view = "(select a.*,b.nmbarang,c1.nmlocation as nmlocationfrom,d1.nmarea as nmareafrom , to_char(a.lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,z.uraian as nmstatus,round(coalesce(e.onhand,0)-(coalesce(e.allocated,0)+coalesce(e.tmpalloca,0)),2) as sisa from sc_tmp.item_ajustment_dtl a
 left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
 left outer join sc_mst.mlocation c1 on a.idlocation=c1.idlocation
 left outer join sc_mst.marea d1 on a.idarea=d1.idarea
 left outer join sc_mst.stkgdw e on a.idbarang=e.idbarang and a.idlocation=e.idlocation and a.idarea=e.idarea and a.batch=e.batch
 left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.4') as x";
    var $t_tmp_ajustment_dtl_view_column = array('idbarang','nmbarang','unit','idlocation','nmlocationfrom');
    var $t_tmp_ajustment_dtl_view_order = array("id" => 'asc'); // default order
    private function _get_query_t_tmp_ajustment_dtl()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_tmp_ajustment_dtl_view);
        $i = 0;

        $builder->where("docno = '$nama'");

        foreach ($this->t_tmp_ajustment_dtl_view_column as $item)
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

                if(count($this->t_tmp_ajustment_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_ajustment_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_ajustment_dtl_view_order))
        {
            $order = $this->t_tmp_ajustment_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_tmp_ajustment_dtl_view(){
        $builder = $this->_get_query_t_tmp_ajustment_dtl();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tmp_ajustment_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_ajustment_dtl();
        ////$this->_get_query_t_balance_moving();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_ajustment_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_tmp_ajustment_dtl();
        return $builder->countAllResults();
    }
    public function get_t_tmp_ajustment_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_tmp_ajustment_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* UNTUK BALANCE AJUSTMENT */
    var $t_balance_ajustment_view = "(select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,
c.nmlocation,d.nmarea,trim(a.idbarang)||trim(a.batch) as idspec from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea) as x";
    var $t_balance_ajustment_view_column = array('idbarang','nmbarang','unit','idlocation','idarea','nmlocation','nmarea','idspec');
    var $t_balance_ajustment_view_order = array("nmbarang" => 'asc'); // default order
    private function _get_query_t_balance_ajustment()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));

        $builder = $this->db->table($this->t_balance_ajustment_view);
        $i = 0;

        $builder->where("idlocation = '$loccode'");
        $builder->where("sisa > 0");


        //jika post kosong maka search table di kosongkan
        if($_POST['idbarang']) {
            $builder->where("idbarang = '" . $_POST['idbarang'] . "'");
        } else if ($_POST['idposition']) {
            $builder->where("idarea = '".$_POST['idposition']."'");
        } else {
            $builder->where("idbarang = '1'");
        }
        foreach ($this->t_balance_ajustment_view_column as $item)
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

                if(count($this->t_balance_ajustment_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_balance_ajustment_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_balance_ajustment_view_order))
        {
            $order = $this->t_balance_ajustment_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_balance_ajustment_view(){
        $builder = $this->_get_query_t_balance_ajustment();
        ////$this->_get_query_t_balance_ajustment();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_balance_ajustment_view_count_filtered()
    {
        $builder = $this->_get_query_t_balance_ajustment();
        ////$this->_get_query_t_balance_ajustment();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_balance_ajustment_view_count_all()
    {
        $builder = $this->_get_query_t_balance_ajustment();
        return $builder->countAllResults();
    }
    public function get_t_balance_ajustment_view_by_id($id)
    {
        $builder = $this->_get_query_t_balance_ajustment();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_t_v_allocation_map($param){
        return $this->db->query("select coalesce(trim(docno::text),'') as docno,
coalesce(trim(idlocation::text),'') as idlocation,
coalesce(trim(idbarang::text),'') as idbarang,
coalesce(trim(defarea::text),'') as defarea,
coalesce(trim(a1::text),'') as a1,
coalesce(trim(a2::text),'') as a2,
coalesce(trim(a3::text),'') as a3,
coalesce(trim(a4::text),'') as a4,
coalesce(trim(a5::text),'') as a5,
coalesce(trim(a6::text),'') as a6,
coalesce(trim(b1::text),'') as b1,
coalesce(trim(b2::text),'') as b2,
coalesce(trim(b3::text),'') as b3,
coalesce(trim(b4::text),'') as b4,
coalesce(trim(b5::text),'') as b5,
coalesce(trim(b6::text),'') as b6,
coalesce(trim(c1::text),'') as c1,
coalesce(trim(c2::text),'') as c2,
coalesce(trim(c3::text),'') as c3,
coalesce(trim(c4::text),'') as c4,
coalesce(trim(c5::text),'') as c5,
coalesce(trim(c6::text),'') as c6,
coalesce(trim(d1::text),'') as d1,
coalesce(trim(d2::text),'') as d2,
coalesce(trim(d3::text),'') as d3,
coalesce(trim(d4::text),'') as d4,
coalesce(trim(d5::text),'') as d5,
coalesce(trim(d6::text),'') as d6,
coalesce(trim(e1::text),'') as e1,
coalesce(trim(e2::text),'') as e2,
coalesce(trim(e3::text),'') as e3,
coalesce(trim(e4::text),'') as e4,
coalesce(trim(e5::text),'') as e5,
coalesce(trim(e6::text),'') as e6,
coalesce(trim(f1::text),'') as f1,
coalesce(trim(f2::text),'') as f2,
coalesce(trim(f3::text),'') as f3,
coalesce(trim(f4::text),'') as f4,
coalesce(trim(f5::text),'') as f5,
coalesce(trim(f6::text),'') as f6,
coalesce(trim(g1::text),'') as g1,
coalesce(trim(g2::text),'') as g2,
coalesce(trim(g3::text),'') as g3,
coalesce(trim(g4::text),'') as g4,
coalesce(trim(g5::text),'') as g5,
coalesce(trim(g6::text),'') as g6,
coalesce(trim(h1::text),'') as h1,
coalesce(trim(h2::text),'') as h2,
coalesce(trim(h3::text),'') as h3,
coalesce(trim(h4::text),'') as h4,
coalesce(trim(h5::text),'') as h5,
coalesce(trim(h6::text),'') as h6,
coalesce(trim(i1::text),'') as i1,
coalesce(trim(i2::text),'') as i2,
coalesce(trim(i3::text),'') as i3,
coalesce(trim(i4::text),'') as i4,
coalesce(trim(i5::text),'') as i5,
coalesce(trim(i6::text),'') as i6,
coalesce(trim(j1::text),'') as j1,
coalesce(trim(j2::text),'') as j2,
coalesce(trim(j3::text),'') as j3,
coalesce(trim(j4::text),'') as j4,
coalesce(trim(j5::text),'') as j5,
coalesce(trim(j6::text),'') as j6,
coalesce(trim(k1::text),'') as k1,
coalesce(trim(k2::text),'') as k2,
coalesce(trim(k3::text),'') as k3,
coalesce(trim(k4::text),'') as k4,
coalesce(trim(k5::text),'') as k5,
coalesce(trim(k6::text),'') as k6,
coalesce(trim(l1::text),'') as l1,
coalesce(trim(l2::text),'') as l2,
coalesce(trim(l3::text),'') as l3,
coalesce(trim(l4::text),'') as l4,
coalesce(trim(l5::text),'') as l5,
coalesce(trim(l6::text),'') as l6,
coalesce(trim(m1::text),'') as m1,
coalesce(trim(m2::text),'') as m2,
coalesce(trim(m3::text),'') as m3,
coalesce(trim(m4::text),'') as m4,
coalesce(trim(m5::text),'') as m5,
coalesce(trim(m6::text),'') as m6,
coalesce(trim(n1::text),'') as n1,
coalesce(trim(n2::text),'') as n2,
coalesce(trim(n3::text),'') as n3,
coalesce(trim(n4::text),'') as n4,
coalesce(trim(n5::text),'') as n5,
coalesce(trim(n6::text),'') as n6,
coalesce(trim(o1::text),'') as o1,
coalesce(trim(o2::text),'') as o2,
coalesce(trim(o3::text),'') as o3,
coalesce(trim(o4::text),'') as o4,
coalesce(trim(o5::text),'') as o5,
coalesce(trim(o6::text),'') as o6,
coalesce(trim(p1::text),'') as p1,
coalesce(trim(p2::text),'') as p2,
coalesce(trim(p3::text),'') as p3,
coalesce(trim(p4::text),'') as p4,
coalesce(trim(p5::text),'') as p5,
coalesce(trim(p6::text),'') as p6,
coalesce(trim(q1::text),'') as q1,
coalesce(trim(q2::text),'') as q2,
coalesce(trim(q3::text),'') as q3,
coalesce(trim(q4::text),'') as q4,
coalesce(trim(q5::text),'') as q5,
coalesce(trim(q6::text),'') as q6,
coalesce(trim(r1::text),'') as r1,
coalesce(trim(r2::text),'') as r2,
coalesce(trim(r3::text),'') as r3,
coalesce(trim(r4::text),'') as r4,
coalesce(trim(r5::text),'') as r5,
coalesce(trim(r6::text),'') as r6,
coalesce(trim(nonident::text),'') as nonident,
coalesce(trim(nonident_idarea::text),'') as nonident_idarea,
coalesce(trim(nonident_nmarea::text),'') as nonident_nmarea from sc_trx.t_v_allocation_map where docno is not null $param");
    }

    /* BALANCE FROM WINACC */
    function q_balance_winacc_tmp_param($param){
        return $this->db->query("select * from sc_tmp.item_balance_winacc where docno is not null $param");
    }

    /* BALANCE FROM WINACC */




    /* STOCK OPNAME */
    /* Balance Moving Item */
    var $t_item_stock_opname_view = "(select a.*,b1.nmlocation,b2.nmarea,b2.description as nmarea_posisi,b3.nmbarang,z.uraian as nmstatus,to_char(lasttrxdate,'dd-mm-yyyy') as lasttrxdate1  from sc_trx.item_stock_opname a
left outer join sc_mst.mlocation b1 on a.idlocation=b1.idlocation
left outer join sc_mst.marea b2 on a.idarea=b2.idarea
left outer join sc_mst.mbarang b3 on a.idbarang=b3.idbarang
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.3') as x";
    var $t_item_stock_opname_view_column = array('docno','idbarang','nmbarang','unit','idlocation','idarea','nmlocation','nmarea','nmarea_posisi','nmstatus');
    var $t_item_stock_opname_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_item_stock_opname()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));

        $builder = $this->db->table($this->t_item_stock_opname_view);
        $i = 0;

        $builder->where("idlocation = '$loccode'");
        //jika post kosong maka search table di kosongkan
//        if(! $_POST['searchfilter']) {
//            $builder->where("idbarang = '1'");
//        }

        foreach ($this->t_item_stock_opname_view_column as $item)
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

                if(count($this->t_item_stock_opname_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_item_stock_opname_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_item_stock_opname_view_order))
        {
            $order = $this->t_item_stock_opname_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_item_stock_opname_view(){
        $builder = $this->_get_query_t_item_stock_opname();
        ////$this->_get_query_t_balance();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_item_stock_opname_view_count_filtered()
    {
        $builder = $this->_get_query_t_item_stock_opname();
        ////$this->_get_query_t_balance();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_item_stock_opname_view_count_all()
    {
        $builder = $this->_get_query_t_item_stock_opname();
        return $builder->countAllResults();
    }
    public function get_t_item_stock_opname_view_by_id($id)
    {
        $builder = $this->_get_query_t_item_stock_opname();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }



    var $t_balance_so_view = "(select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,
c.nmlocation,d.nmarea,e.nmgroup from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea
left outer join sc_mst.mgroup e on b.idgroup=e.idgroup
) as x";
    var $t_balance_so_view_column = array('idbarang','nmbarang','batch','unit','idlocation','idarea','nmlocation','nmarea');
    var $t_balance_so_view_order = array("nmbarang" => 'asc'); // default order
    private function _get_query_t_balance_so()
    {
        $this->session = \Config\Services::session();
        $loccode=trim($this->session->get('loccode'));

        $builder = $this->db->table($this->t_balance_so_view);
        $i = 0;

        $builder->where("idlocation = '$loccode'");
        //$builder->where("sisa > 0");


        //jika post kosong maka search table di kosongkan
//        if($_POST['idbarang']) {
//            $builder->where("idbarang = '" . $_POST['idbarang'] . "'");
//        } else if ($_POST['idposition']) {
//            $builder->where("idarea = '".$_POST['idposition']."'");
//        } else {
//            $builder->where("idbarang = '1'");
//        }
        //jika post kosong maka search table di kosongkan
        if(! $_POST['searchfilter']) {
            $builder->where("idbarang = '1'");
        }

//        if(! $_POST['dateOpname']) {
//            $builder->where("idbarang = '1'");
//        }

        foreach ($this->t_balance_so_view_column as $item)
        {
            if($_POST['searchfilter']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['searchfilter']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['searchfilter']));
                }

                if(count($this->t_balance_so_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }

            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_balance_so_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_balance_so_view_order))
        {
            $order = $this->t_balance_so_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_balance_so_view(){
        $builder = $this->_get_query_t_balance_so();
        ////$this->_get_query_t_balance_moving();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_balance_so_view_count_filtered()
    {
        $builder = $this->_get_query_t_balance_so();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_balance_so_view_count_all()
    {
        $builder = $this->_get_query_t_balance_so();
        return $builder->countAllResults();
    }
    public function get_t_balance_so_view_by_id($id)
    {
        $builder = $this->_get_query_t_balance_so();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

//    function q_balance_so($param){
//        return $this->db->query("select * from (select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,
//c.nmlocation,d.nmarea from sc_mst.stkgdw a
//left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
//left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
//left outer join sc_mst.marea d on a.idarea=d.idarea) as x where id is not null $param");
//
//    }

    function q_balance_so($param,$paramdate){
        return $this->db->query("select * from (select a.*,round(coalesce(a.saldo,0),2) as sisa,b.nmbarang,
c.nmlocation,d.nmarea from (
select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref,max(qty_sld) as saldo  from sc_trx.stkblc a,
(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref from sc_trx.stkblc a,
(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,max(a.docref) as docref from sc_trx.stkblc a,
(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,max(a.docno) as docno from sc_trx.stkblc a,
(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,max(a.doctype) as doctype from sc_trx.stkblc a,
(select idlocation,idarea,batch,idbarang,max(trxdate) as trxdate from sc_trx.stkblc 
where idlocation is not null $param $paramdate
group by idlocation,idarea,batch,idbarang) as b
where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate
group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate) as b
where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype
group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype) as b
where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno
group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno) as b
where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref
group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref) as b
where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref
group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref) as a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea) as x where idbarang is not null ");

    }
    /* STOCK OPNAME */










}
