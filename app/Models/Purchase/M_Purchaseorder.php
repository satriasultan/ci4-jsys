<?php

namespace App\Models\Purchase;

use CodeIgniter\Model;

class M_Purchaseorder extends Model
{

    var $t_Purchaseorder_view = "(select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
 c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,coalesce(a.setminstock,'NO') as setminstock1,round(coalesce(a.minstock,0)) as minstock1
 from sc_mst.mbarang a 
 left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
 left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
 left outer join sc_mst.marea d on a.defarea=d.idarea
) as x";
    var $t_Purchaseorder_view_column = array('idbarang','nmbarang','expdate1','chold','nmgroup','unit','subunit');
    var $t_Purchaseorder_view_order = array("idbarang" => 'asc'); // default order
    private function _get_query_t_Purchaseorder()
    {
        $builder = $this->db->table($this->t_Purchaseorder_view);
        $i = 0;

        foreach ($this->t_Purchaseorder_view_column as $Purchaseorder)
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

                if(count($this->t_Purchaseorder_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_Purchaseorder_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_Purchaseorder_view_order))
        {
            $order = $this->t_Purchaseorder_view_order;
            foreach ($order as $key => $Purchaseorder){
                $builder->orderBy($key, $Purchaseorder);
            }
        }
        return $builder;
    }


    function get_t_Purchaseorder_view(){
        $builder = $this->_get_query_t_Purchaseorder();
        ////$this->_get_query_t_Purchaseorder();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_Purchaseorder_view_count_filtered()
    {
        $builder = $this->_get_query_t_Purchaseorder();
        ////$this->_get_query_t_Purchaseorder();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_Purchaseorder_view_count_all()
    {
        $builder = $this->_get_query_t_Purchaseorder();
        return $builder->countAllResults();
    }
    public function get_t_Purchaseorder_view_by_id($id)
    {
        $builder = $this->_get_query_t_Purchaseorder();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mPurchaseorder(){
        return $this->db->query("select * from (select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
 c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,trim(a.idbarang) as idbarang1
 from sc_mst.mbarang a 
 left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
 left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
 left outer join sc_mst.marea d on a.defarea=d.idarea
) as x where idbarang is not null and coalesce(chold,'NO') !='YES' $param");
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


    function q_tmp_item_po_param($param){
        return $this->db->query("select * from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus from sc_tmp.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x
where docno is not null $param");
    }

    function q_trx_item_po_param($param){
        return $this->db->query("select * from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus from sc_trx.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x
where docno is not null $param");
    }
    /* UNIT Purchaseorder */

    var $t_trx_item_po_dtl_view = "(select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus,
(coalesce(onhandpo,0)- coalesce(onhandtranspo,0)) as sisapo, coalesce(onhanddirty,0) as kotor
from sc_trx.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x";
    var $t_trx_item_po_dtl_view_column = array('docno','idbarang','trxdate1','senddate1','description','namasupplier','namabarangx');
    var $t_trx_item_po_dtl_view_order = array("trxdate" => 'desc'); // default order
    private function _get_query_t_trx_item_po_dtl()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();

        $builder = $this->db->table($this->t_trx_item_po_dtl_view);
        $i = 0;
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
        if($this->request->getPost('namasupplier')) {
            $ket2 = trim($this->request->getPost('namasupplier'));
            $builder->where("namasupplier like '%$ket2%'");
        }
        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("to_char(trxdate,'yyyy-mm-dd') BETWEEN '$tgl1' AND '$tgl2'");
        } else {
            $datey = date('Y-m-d');
            $tgl1= date('Y-m-d',strtotime($datey . "-60 days"));
            $tgl2= date('Y-m-d',strtotime($datey . "-1 days"));
            $builder->where("to_char(trxdate,'yyyy-mm-dd') BETWEEN '$tgl1' AND '$tgl2'");
        }


        foreach ($this->t_trx_item_po_dtl_view_column as $Purchaseorder)
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

                if(count($this->t_trx_item_po_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_item_po_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_item_po_dtl_view_order))
        {
            $order = $this->t_trx_item_po_dtl_view_order;
            foreach ($order as $key => $Purchaseorder){
                $builder->orderBy($key, $Purchaseorder);
            }
        }
        return $builder;
    }


    function get_t_trx_item_po_dtl_view(){
        $builder = $this->_get_query_t_trx_item_po_dtl();
        ////$this->_get_query_t_trx_item_po_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_item_po_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_item_po_dtl();
        ////$this->_get_query_t_trx_item_po_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_item_po_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_trx_item_po_dtl();
        return $builder->countAllResults();
    }
    public function get_t_trx_item_po_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_item_po_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    // UNTUK PARAMETER FINAL
    var $t_trx_item_po_final_dtl_view = "(select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus from sc_trx.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x";

    var $t_trx_item_po_final_dtl_view_column = array('docno','idbarang','trxdate1','senddate1','description','namasupplier','namabarangx');
    var $t_trx_item_po_final_dtl_view_order = array("trxdate" => 'desc'); // default order
    private function _get_query_t_trx_item_po_final_dtl()
    {
        $builder = $this->db->table($this->t_trx_item_po_final_dtl_view);
        $i = 0;

        $builder->where("status in ('P','O','C','D','X')");

        foreach ($this->t_trx_item_po_final_dtl_view_column as $Purchaseorder)
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

                if(count($this->t_trx_item_po_final_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_item_po_final_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_item_po_final_dtl_view_order))
        {
            $order = $this->t_trx_item_po_final_dtl_view_order;
            foreach ($order as $key => $Purchaseorder){
                $builder->orderBy($key, $Purchaseorder);
            }
        }
        return $builder;
    }


    function get_t_trx_item_po_final_dtl_view(){
        $builder = $this->_get_query_t_trx_item_po_final_dtl();
        ////$this->_get_query_t_trx_item_po_final_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_trx_item_po_final_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_item_po_final_dtl();
        ////$this->_get_query_t_trx_item_po_final_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_item_po_final_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_trx_item_po_final_dtl();
        return $builder->countAllResults();
    }
    public function get_t_trx_item_po_final_dtl_view_by_id($id)
    {
        $builder = $this->_get_query_t_trx_item_po_final_dtl();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_outstanding_po($id){
        return $this->db->query("select * from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus,
(coalesce(onhandpo,0)- coalesce(onhandtranspo,0)) as sisapo, coalesce(onhanddirty,0) as kotor
from sc_trx.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x");
    }

}
