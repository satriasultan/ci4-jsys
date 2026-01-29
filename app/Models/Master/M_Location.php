<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Location extends Model
{
    function q_versidb($kodemenu){
        return $this->db->query("select * from sc_mst.version where kodemenu='$kodemenu'");
    }

    function q_trxerror($paramtrxerror){
        return $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null  $paramtrxerror");
    }
    function ins_trxerror($param1error,$param2error,$modul){
        return $this->db->query("
				delete from sc_mst.trxerror where userid is not null and modul='$modul' and userid='$param2error' ;
				insert into sc_mst.trxerror
				(userid,errorcode,nomorakhir1,nomorakhir2,modul) VALUES
				('$param2error',$param1error,'$param2error','','$modul')");
    }
    function q_deltrxerror($paramtrxerror){
        return $this->db->query("delete from sc_mst.trxerror where userid is not null $paramtrxerror");
    }

    function q_karyawan ($param){
        return $this->db->query("select * from (select *,trim(nik) as id from sc_mst.lv_m_karyawan where nik is not null 
                                    and coalesce(resign,'')!='YES') as x where nik is not null $param ");
    }

    var $t_mlocation_view = "(select a.*,c.nmcoa,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,b.nmarea from sc_mst.mlocation a
left outer join sc_mst.marea b on a.idarea_default=b.idarea
left outer join sc_mst.coa c on a.pselisih=c.idcoa ) as x";
    var $t_mlocation_view_column = array('id','idlocation','nmlocation','idarea_default','nmarea','nmcoa');
    var $t_mlocation_view_order = array("nmlocation" => 'asc'); // default order
    private function _get_query_t_mlocation()
    {

        $builder = $this->db->table($this->t_mlocation_view);
        $i = 0;
        foreach ($this->t_mlocation_view_column as $item)
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

                if(count($this->t_mlocation_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_mlocation_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_mlocation_view_order))
        {
            $order = $this->t_mlocation_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_mlocation_view(){
        $builder = $this->_get_query_t_mlocation();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_mlocation_view_count_filtered()
    {
        $builder = $this->_get_query_t_mlocation();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_mlocation_view_count_all()
    {
        $builder = $this->_get_query_t_mlocation();
        return $builder->countAllResults();
    }
    public function get_t_mlocation_view_by_id($id)
    {
        $builder = $this->_get_query_t_mlocation();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_mlocation($param){
        return $this->db->query("select * from sc_mst.mlocation where id is not null $param ");
    }

    // THIS AREA
    var $t_marea_view = "(select b.nmlocation,a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1 from sc_mst.marea a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation) as x";
    var $t_marea_view_column = array('id','idlocation','nmlocation','idarea','nmarea','inputdate1','inputby','idbarcode');
    var $t_marea_view_order = array("idarea" => 'asc'); // default order
    private function _get_query_t_marea()
    {

        $builder = $this->db->table($this->t_marea_view);
        $i = 0;
        foreach ($this->t_marea_view_column as $item)
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

                if(count($this->t_marea_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_marea_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_marea_view_order))
        {
            $order = $this->t_marea_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_marea_view(){
        $builder = $this->_get_query_t_marea();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_marea_view_count_filtered()
    {
        $builder = $this->_get_query_t_marea();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_marea_view_count_all()
    {
        $builder = $this->_get_query_t_marea();
        return $builder->countAllResults();
    }
    public function get_t_marea_view_by_id($id)
    {
        $builder = $this->_get_query_t_marea();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_marea($param){
        return $this->db->query("select * from sc_mst.marea where id is not null $param ");
    }

    function q_marea_tmp($param){
        return $this->db->query("select * from sc_tmp.marea where id is not null $param");
    }

    /* Penambahan Bagian Pada Inventory */
    // var $t_costcenter_view = "sc_mst.costcenter";
    var $t_costcenter_view = "(select a.*,c.nmcoa from sc_mst.costcenter a
            left outer join sc_mst.coa c on a.pbiaya=c.idcoa ) as x";
    var $t_costcenter_view_column = array('idcostcenter','nmcostcenter','nmcoa','chold');
    var $t_costcenter_view_order = array("nmcostcenter" => 'asc'); // default order
    private function _get_query_t_costcenter()
    {

        $builder = $this->db->table($this->t_costcenter_view);
        $i = 0;
        foreach ($this->t_costcenter_view_column as $item)
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

                if(count($this->t_costcenter_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_costcenter_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_costcenter_view_order))
        {
            $order = $this->t_costcenter_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_costcenter_view(){
        $builder = $this->_get_query_t_costcenter();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_costcenter_view_count_filtered()
    {
        $builder = $this->_get_query_t_costcenter();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_costcenter_view_count_all()
    {
        $builder = $this->_get_query_t_costcenter();
        return $builder->countAllResults();
    }
    public function get_t_costcenter_view_by_id($id)
    {
        $builder = $this->_get_query_t_costcenter();
        $builder->where('idcostcenter',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_costcenter($param){
        return $this->db->query("select * from sc_mst.costcenter where idcostcenter is not null $param ");
    }
    /* Penambahan Bagian Pada Inventory*/


}
