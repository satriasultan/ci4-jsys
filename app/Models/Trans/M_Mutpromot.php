<?php
namespace App\Models\Trans;

use App\Models\Master\M_Role;
use CodeIgniter\Model;

class M_Mutpromot extends Model
{
    function list_karyawan_param($param){
        return $this->db->query("select * from sc_mst.t_karyawan where coalesce(resign,'')='NO' $param");

    }

    function read_his_mutasipromosi ($param) {
        return $this->db->query("select * from (select a.*,
to_char(a.startdate,'dd-mm-yyyy') as startdate2,
to_char(a.enddate,'dd-mm-yyyy') as enddate2,
to_char(a.docdate,'dd-mm-yyyy HH24:mi:ss') as docdate2,
b.nmlengkap,b.nmdept,b.id_dept,b.nmjabatan,b.nmsubdept,b.nmgrade,b.desc_domisili,b.locaname,coalesce(b.nohp1,'') as nohp1,coalesce(b.email_self,'') as email,
z.uraian as nmstatus,
c1.nmdept as nmnewdept,
c2.nmsubdept as nmnewsubdept,
c3.nmjabatan as nmnewjabatan,
c4.nmlvljabatan as nmnewlvljabatan,
c5.nmdivision as nmnewdivision,
c6.locaname as nmnewplant,
d1.nmdept as nmolddept,
d2.nmsubdept as nmoldsubdept,
d3.nmjabatan as nmoldjabatan,
d4.nmlvljabatan as nmoldlvljabatan,
d5.nmdivision as nmolddivision,
d6.locaname as nmoldplant
from sc_his.mutasipromosi a
left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
left outer join sc_mst.departmen c1 on a.newdept=c1.kddept
left outer join sc_mst.subdepartmen c2 on a.newsubdept=c2.kdsubdept
left outer join sc_mst.jabatan c3 on a.newjabatan=c3.kdjabatan
left outer join sc_mst.lvljabatan c4 on a.newlvljabatan=c4.kdlvl
left outer join sc_mst.division c5 on a.newdivision=c5.iddivision 
left outer join sc_mst.mgudang c6 on a.newplant=c6.loccode
left outer join sc_mst.departmen d1 on a.olddept=d1.kddept
left outer join sc_mst.subdepartmen d2 on a.oldsubdept=d2.kdsubdept
left outer join sc_mst.jabatan d3 on a.oldjabatan=d3.kdjabatan
left outer join sc_mst.lvljabatan d4 on a.oldlvljabatan=d4.kdlvl
left outer join sc_mst.division d5 on a.olddivision=d5.iddivision 
left outer join sc_mst.mgudang d6 on a.oldplant=d6.loccode
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.T.C.4') as x where coalesce(docno,'') !='' $param
 ");
    }


    var $t_hismutpromot_view = "(select a.*,
to_char(a.startdate,'dd-mm-yyyy') as startdate2,
to_char(a.enddate,'dd-mm-yyyy') as enddate2,
to_char(a.docdate,'dd-mm-yyyy HH24:mi:ss') as docdate2,
b.nmlengkap,b.nmdept,b.id_dept,b.nmjabatan,b.nmsubdept,b.nmgrade,b.desc_domisili,b.locaname,coalesce(b.nohp1,'') as nohp1,coalesce(b.email_self,'') as email,
z.uraian as nmstatus,
c1.nmdept as nmnewdept,
c2.nmsubdept as nmnewsubdept,
c3.nmjabatan as nmnewjabatan,
c4.nmlvljabatan as nmnewlvljabatan,
c5.nmdivision as nmnewdivision,
c6.locaname as nmnewplant,
d1.nmdept as nmolddept,
d2.nmsubdept as nmoldsubdept,
d3.nmjabatan as nmoldjabatan,
d4.nmlvljabatan as nmoldlvljabatan,
d5.nmdivision as nmolddivision,
d6.locaname as nmoldplant
from sc_his.mutasipromosi a
left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
left outer join sc_mst.departmen c1 on a.newdept=c1.kddept
left outer join sc_mst.subdepartmen c2 on a.newsubdept=c2.kdsubdept
left outer join sc_mst.jabatan c3 on a.newjabatan=c3.kdjabatan
left outer join sc_mst.lvljabatan c4 on a.newlvljabatan=c4.kdlvl
left outer join sc_mst.division c5 on a.newdivision=c5.iddivision 
left outer join sc_mst.mgudang c6 on a.newplant=c6.loccode
left outer join sc_mst.departmen d1 on a.olddept=d1.kddept
left outer join sc_mst.subdepartmen d2 on a.oldsubdept=d2.kdsubdept
left outer join sc_mst.jabatan d3 on a.oldjabatan=d3.kdjabatan
left outer join sc_mst.lvljabatan d4 on a.oldlvljabatan=d4.kdlvl
left outer join sc_mst.division d5 on a.olddivision=d5.iddivision 
left outer join sc_mst.mgudang d6 on a.oldplant=d6.loccode
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.T.C.4') as x";
    var $t_hismutpromot_view_column = array('docno','docdate','docname','nik','nmlengkap','doctype','kdregu','evaluation','nmstatus','nmnewjabatan','nmoldjabatan');
    var $t_hismutpromot_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_hismutpromot()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->m_role = new M_Role();
        $builder = $this->db->table($this->t_hismutpromot_view);

        /* untuk filtering view*/

        $kmenu='I.T.C.4';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        if ($role_access_filter==='t') {

            //$builder->where(" olddept = " , "('".trim($dtlkary['id_dept'])."') ");
        }

        /* end untuk filtering view */
        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("docdate BETWEEN '$tgl1' AND '$tgl2'");
        }

        $builder->where(array(" status !=" => 'I'));
        ///$builder->where(array('resign <>' => 'YES','trim(nik) =' => $nik));

        $i = 0;

        foreach ($this->t_hismutpromot_view_column as $item)
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

                if(count($this->t_hismutpromot_view_column) - 1 == $i) //last loop
                    $builder->orLike(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_hismutpromot_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_hismutpromot_view_order))
        {
            $order = $this->t_hismutpromot_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_hismutpromot_view(){
        $builder = $this->_get_query_t_hismutpromot();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_hismutpromot_view_count_filtered()
    {
        $builder = $this->_get_query_t_hismutpromot();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_hismutpromot_view_count_all()
    {
        $builder = $this->_get_query_t_hismutpromot();
        return $builder->countAllResults();
    }
    public function get_t_hismutpromot_view_by_id($id)
    {
        $builder = $this->_get_query_t_hismutpromot();
        $builder->where('nodok',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* FOR APPROVAL */
    private function _get_query_t_hismutpromot_approval()
    {
        /* untuk filtering view */
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->m_role = new M_Role();

        $kmenu='I.T.C.5';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->list_karyawan_param(" and nik='$niksession'")->getRowArray();
        $builder = $this->db->table($this->t_hismutpromot_view);
        if ($role_access_filter==='t') {
            //$builder->where(" newdept in ('".trim($dtlkary['id_dept'])."') ","", FALSE);
        }
        /* end untuk filtering view */

        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("docdate BETWEEN '$tgl1' AND '$tgl2'");

        }
        if ($role==='DR'){
            $builder->where(" status in ('A1') ");
        } else if ($role==='AHC'){
            $builder->where(" status in ('A') ");
        } else {
            $builder->where(" status in ('A') ");
        }
        $i = 0;

        foreach ($this->t_hismutpromot_view_column as $item)
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

                if(count($this->t_hismutpromot_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_hismutpromot_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_hismutpromot_view_order))
        {
            $order = $this->t_hismutpromot_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }

        return $builder;

    }


    function get_t_hismutpromot_approval_view(){
        $builder = $this->_get_query_t_hismutpromot_approval();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_hismutpromot_approval_view_count_filtered()
    {
        $builder = $this->_get_query_t_hismutpromot_approval();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_hismutpromot_approval_view_count_all()
    {
        $builder = $this->_get_query_t_hismutpromot_approval();
        return $builder->countAllResults();
    }
    public function get_t_hismutpromot_approval_view_by_id($id)
    {
        $builder = $this->_get_query_t_hismutpromot_approval();
        $builder->where('docno',$id);
        $query = $builder->get();
        return $query->getRow();
    }
    function q_jabatan_position($param){
        return $this->db->query("select * from sc_mst.jabatan where kdjabatan is not null $param");
    }
}
