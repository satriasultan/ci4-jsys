<?php
namespace App\Models\Trans;

use CodeIgniter\Model;

class M_Shareloc extends Model
{
    function q_shareloc_tmp($param)
    {
        return $this->db->query("select * from (
            select a.*,to_char(a.attenddate,'dd-mm-yyyy') as attenddate1,b.id_dept,b.id_subdept,b.id_jabatan,b.id_grade,b.plant,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.locaname as plant,z.uraian as nmstatus 
            from sc_tmp.shareloc a
            left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
            left outer join sc_mst.trxtype z on a.attendstatus=z.kdtrx and z.jenistrx='I.A.C.2') as x where nik is not null  $param ");
    }

    function q_shareloc_his($param)
    {
        return $this->db->query("select * from (
            select a.*,to_char(a.attenddate,'dd-mm-yyyy') as attenddate1,b.id_dept,b.id_subdept,b.id_jabatan,b.id_grade,b.plant,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.locaname as plant,z.uraian as nmstatus 
            from sc_his.shareloc a
            left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
            left outer join sc_mst.trxtype z on a.attendstatus=z.kdtrx and z.jenistrx='I.A.C.2') as x where nik is not null  $param ");
    }

    var $t_his_shareloc_view = "(
            select a.*,to_char(a.attenddate,'dd-mm-yyyy') as attenddate1,b.id_dept,b.id_subdept,b.id_jabatan,b.id_grade,b.plant,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.locaname as plant,z.uraian as nmstatus 
            from sc_his.shareloc a
            left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
            left outer join sc_mst.trxtype z on a.attendstatus=z.kdtrx and z.jenistrx='I.A.C.2') as x";
    var $t_his_shareloc_view_column = array('nik','nmlengkap','nmdept','attenddate','attend','nmstatus');
    var $t_his_shareloc_view_order = array("attenddate" => 'desc',"nik" => 'asc'); // default order
    private function _get_query_t_his_shareloc()
    {
        /* end untuk filtering view */
        $builder = $this->db->table($this->t_his_shareloc_view);
        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("attenddate BETWEEN '$tgl1' AND '$tgl2'","", FALSE);
        }
        if($this->request->getPost('dept')) {
            $id_dept = trim($this->request->getPost('dept'));
            $builder->where("id_dept = '$id_dept'","", FALSE);
        }
        if($this->request->getPost('attend')) {
            $attend = trim($this->request->getPost('attend'));
            $builder->where("attend = '$attend'","", FALSE);
        }
        if($this->request->getPost('attendstatus')!=='') {
            $status = trim($this->request->getPost('attendstatus'));
            $builder->where("attendstatus = '$status'","", FALSE);
        }
        $i = 0;

        foreach ($this->t_his_shareloc_view_column as $item)
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

                if(count($this->t_his_shareloc_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_his_shareloc_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_his_shareloc_view_order))
        {
            $order = $this->t_his_shareloc_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_his_shareloc_view(){

        $builder = $this->_get_query_t_his_shareloc();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_his_shareloc_view_count_filtered()
    {
        $builder = $this->_get_query_t_his_shareloc();
        $query = $builder->get();
        return $query->getResult();
    }
    public function t_his_shareloc_view_count_all()
    {
        $builder = $this->_get_query_t_his_shareloc();
        return $builder->countAllResults();
    }
    public function get_t_his_shareloc_view_by_id($id)
    {
        $builder = $this->_get_query_t_his_shareloc();
        $builder->where('docno',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_attendancestatus(){
        return $this->db->query("select * from sc_mst.trxtype where jenistrx='I.A.C.2' order by kdtrx asc ");
    }


    //onlineabsen
    var $t_tmp_onlineabsen_view = "(
           select a.*,a.lang||','||a.long as coordinat,to_char(a.checktime,'dd-mm-yyyy HH24:mi:ss') as checktime1,b.id_dept,b.id_subdept,b.id_jabatan,b.id_grade,b.plant,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.locaname as plant
        from sc_tmp.onlineabsen a left outer join sc_mst.lv_m_karyawan_trans b
        on a.nik=b.nik) as x";
    var $t_tmp_onlineabsen_view_column = array('nik','nmlengkap','nmdept','checktime');
    var $t_tmp_onlineabsen_view_order = array("checktime" => 'desc','nmlengkap' => 'asc',"ctype" => 'asc'); // default order
    private function _get_query_t_tmp_onlineabsen()
    {
        /* end untuk filtering view */
        $builder = $this->db->table($this->t_tmp_onlineabsen_view);
        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("checktime BETWEEN '$tgl1' AND '$tgl2'","", FALSE);
        }
        if($this->request->getPost('dept')) {
            $id_dept = trim($this->request->getPost('dept'));
            $builder->where("id_dept = '$id_dept'","", FALSE);
        }
        if($this->request->getPost('nik')) {
            $nik = trim($this->request->getPost('nik'));
            $builder->where("nik = '$nik'","", FALSE);
        }

        $i = 0;

        foreach ($this->t_tmp_onlineabsen_view_column as $item)
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

                if(count($this->t_tmp_onlineabsen_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_onlineabsen_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_onlineabsen_view_order))
        {
            $order = $this->t_tmp_onlineabsen_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_tmp_onlineabsen_view(){
        $builder = $this->_get_query_t_tmp_onlineabsen();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tmp_onlineabsen_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_onlineabsen();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_onlineabsen_view_count_all()
    {
        $builder = $this->_get_query_t_tmp_onlineabsen();
        return $builder->countAllResults();
    }
    public function get_t_tmp_onlineabsen_view_by_id($id)
    {
        $builder = $this->db->table($this->t_tmp_onlineabsen_view);
        $builder->where('nik',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_tmponline_absen($param){
        return $this->db->query("select * from 
(select a.*,a.lang||','||a.long as coordinat,to_char(a.checktime,'dd-mm-yyyy HH24:mi:ss') as checktime1,b.id_dept,b.id_subdept,b.id_jabatan,b.id_grade,b.plant,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.locaname as plant
from sc_tmp.onlineabsen a left outer join sc_mst.lv_m_karyawan_trans b
on a.nik=b.nik) as x where nik is not null $param");
    }


    //// CHECKINOUT
    ///
    ///
    //onlineabsen
    var $t_tmp_checkinout_view = "(select a.*,a.lang||','||a.long as coordinat,to_char(a.checktime,'dd-mm-yyyy HH24:mi:ss') as checktime1,b.id_dept,b.id_subdept,b.id_jabatan,b.id_grade,b.plant,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.locaname as plant,
case when b.nmlengkap is null then 'UNIDENTIFIED' else 'IDENTIFIED' end as statusid from sc_tmp.checkinout a left outer join sc_mst.lv_m_karyawan_trans b
on trim(a.badgenumber)=trim(b.idabsen)) as x";
    var $t_tmp_checkinout_view_column = array('nmlengkap','nmdept','checktime');
    var $t_tmp_checkinout_view_order = array("checktime" => 'desc','nmlengkap' => 'asc'); // default order
    private function _get_query_t_tmp_checkinout()
    {
        /* end untuk filtering view */
        $this->request = \Config\Services::request();
        $builder = $this->db->table($this->t_tmp_checkinout_view);

       if(! empty($this->request->getPost('tglrange'))) {
            $tgl=explode(' - ',trim($this->request->getPost('tglrange')));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("checktime BETWEEN '$tgl1' AND '$tgl2'");
        } else {
            $datey = date('Y-m-d');
            $tgl1= date('Y-m-d',strtotime($datey . "-1 days"));
            $tgl2= date('Y-m-d',strtotime($datey . "+1 days"));
            $builder->where("checktime BETWEEN '$tgl1' AND '$tgl2'");
        }

        if($this->request->getPost('dept')) {
            $id_dept = trim($this->request->getPost('dept'));
            $builder->where("id_dept = '$id_dept'");
        }
        if($this->request->getPost('nik')) {
            $nik = trim($this->request->getPost('nik'));
            $builder->where("badgenumber = '$nik'");
        }
        if($this->request->getPost('statusid')) {
            $statusid = trim($this->request->getPost('statusid'));
            $builder->where("statusid = '$statusid'");
        }


        $i = 0;

        foreach ($this->t_tmp_checkinout_view_column as $item)
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

                if(count($this->t_tmp_checkinout_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_checkinout_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_checkinout_view_order))
        {
            $order = $this->t_tmp_checkinout_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }

        return $builder;
    }


    function get_t_tmp_checkinout_view(){
        $builder = $this->_get_query_t_tmp_checkinout();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tmp_checkinout_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_checkinout();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_checkinout_view_count_all()
    {
        $builder = $this->db->table($this->t_tmp_checkinout_view);
        //return $this->db->count_all_results();
        $query = $builder->countAllResults();
        return $query;
    }
    public function get_t_tmp_checkinout_view_by_id($id)
    {
        $builder = $this->db->table($this->t_tmp_checkinout_view);
        $builder->where('nik',$id);
        $query = $builder->get();
        return $query->getRow();
    }

}
