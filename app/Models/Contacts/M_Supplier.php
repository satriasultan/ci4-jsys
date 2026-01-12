<?php

namespace App\Models\Contacts;

use CodeIgniter\Model;

class M_Supplier extends Model
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

    var $t_msupplier_view = "(select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,b.nmgroup from sc_mst.msupplier a 
    left outer join sc_mst.mgroup b on a.idgroup=b.idgroup ) as x";
    var $t_msupplier_view_column = array('id','idsupplier','nmsupplier','addsupplier','ownsupplier','contact1','contact2','npwp','inputdate1','inputby','nmgroup');
    var $t_msupplier_view_order = array("nmsupplier" => 'asc'); // default order
    private function _get_query_t_msupplier()
    {

        $builder = $this->db->table($this->t_msupplier_view);
        $i = 0;
        foreach ($this->t_msupplier_view_column as $item)
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

                if(count($this->t_msupplier_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_msupplier_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_msupplier_view_order))
        {
            $order = $this->t_msupplier_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_msupplier_view(){
        $builder = $this->_get_query_t_msupplier();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_msupplier_view_count_filtered()
    {
        $builder = $this->_get_query_t_msupplier();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_msupplier_view_count_all()
    {
        $builder = $this->_get_query_t_msupplier();
        return $builder->countAllResults();
    }
    public function get_t_msupplier_view_by_id($id)
    {
        $builder = $this->_get_query_t_msupplier();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_msupplier($param){
        return $this->db->query("select * from sc_mst.msupplier where id is not null $param ");
    }

    public function q_tmp_msupplier($param){
        return $this->db->query("select * from sc_tmp.msupplier where id is not null $param ");
    }

//// SUPPLIER GROUP DARI MGROUP
    var $t_mgroup_view = "sc_mst.mgroup";
    var $t_mgroup_view_column = array('id','idgroup','nmgroup','grouptype','chold');
    var $t_mgroup_view_order = array("nmgroup" => 'asc'); // default order
    private function _get_query_t_mgroup()
    {

        $builder = $this->db->table($this->t_mgroup_view);
        $builder->where(array('grouptype ' => 'SUPPLIER'));
        $i = 0;
        foreach ($this->t_mgroup_view_column as $item)
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

                if(count($this->t_mgroup_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_mgroup_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_mgroup_view_order))
        {
            $order = $this->t_mgroup_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_mgroup_view(){
        $builder = $this->_get_query_t_mgroup();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_mgroup_view_count_filtered()
    {
        $builder = $this->_get_query_t_mgroup();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_mgroup_view_count_all()
    {
        $builder = $this->_get_query_t_mgroup();
        return $builder->countAllResults();
    }
    public function get_t_mgroup_view_by_id($id)
    {
        $builder = $this->_get_query_t_mgroup();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_mgroup($param){
        return $this->db->query("select * from sc_mst.mgroup where id is not null $param ");
    }
//// SUPPLIER GROUP DARI MGROUP

}
