<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Item extends Model
{

    var $t_item_view = "(select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
 c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,coalesce(a.setminstock,'NO') as setminstock1,round(coalesce(a.minstock,0)) as minstock1
 from sc_mst.mbarang a 
 left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
 left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
 left outer join sc_mst.marea d on a.defarea=d.idarea
) as x";
    var $t_item_view_column = array('idbarang','nmbarang','expdate1','chold','nmgroup','unit','subunit');
    var $t_item_view_order = array("idbarang" => 'asc'); // default order
    private function _get_query_t_item()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $builder = $this->db->table($this->t_item_view);
        $i = 0;

        if($this->request->getPost('idgroup')) {
            $ket1 = trim($this->request->getPost('idgroup'));
            $builder->where("idgroup ='$ket1'");
        }

        foreach ($this->t_item_view_column as $item)
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

                if(count($this->t_item_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_item_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_item_view_order))
        {
            $order = $this->t_item_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_item_view(){
        $builder = $this->_get_query_t_item();
        ////$this->_get_query_t_item();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_item_view_count_filtered()
    {
        $builder = $this->_get_query_t_item();
        ////$this->_get_query_t_item();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_item_view_count_all()
    {
        $builder = $this->_get_query_t_item();
        return $builder->countAllResults();
    }
    public function get_t_item_view_by_id($id)
    {
        $builder = $this->_get_query_t_item();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_mitem($param){
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

    function q_item_param($param){
        return $this->db->query("select * from (select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
 c.nmlocation,d.nmarea,b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,coalesce(a.setminstock,'NO') as setminstock1,round(coalesce(a.minstock,0)) as minstock1
 from sc_mst.mbarang a 
 left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
 left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
 left outer join sc_mst.marea d on a.defarea=d.idarea
) as x where id is not null $param");
    }

    function q_item_tmp_param($param){
        return $this->db->query("select * from (select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,
case when c.nmlocation is null then 'UNIDENTIFIED'
else c.nmlocation end as nmlocation,
case when d.nmarea is null then 'UNIDENTIFIED'
else d.nmarea end as nmarea,
b.nmgroup,to_char(a.mfgdate,'dd-mm-yyyy hh24:mi:ss') as mfgdate1,coalesce(a.setminstock,'NO') as setminstock1,round(coalesce(a.minstock,0)) as minstock1
 from sc_tmp.mbarang a 
 left outer join sc_mst.mgroup b on a.idgroup=b.idgroup
 left outer join sc_mst.mlocation c on a.deflocation=c.idlocation
 left outer join sc_mst.marea d on a.defarea=d.idarea
) as x where id is not null $param");
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


    ////////////////
    //// SUPPLIER GROUP DARI MGROUP
    var $t_mgroup_view = "sc_mst.mgroup";
    var $t_mgroup_view_column = array('id','idgroup','nmgroup','grouptype','chold');
    var $t_mgroup_view_order = array("nmgroup" => 'asc'); // default order
    private function _get_query_t_mgroup()
    {

        $builder = $this->db->table($this->t_mgroup_view);
        $builder->where(array('grouptype ' => 'BRG'));
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
