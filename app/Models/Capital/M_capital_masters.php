<?php

namespace App\Models\Capital;

use CodeIgniter\Model;

class M_capital_masters extends Model
{
    var $t_mgroup_view = "sc_mst.mgroup";
    var $t_mgroup_view_column = array('id','idgroup','nmgroup','grouptype','chold');
    var $t_mgroup_view_order = array("nmgroup" => 'asc'); // default order
    private function _get_query_t_mgroup()
    {

        $builder = $this->db->table($this->t_mgroup_view);
        $builder->where(array('grouptype ' => 'CIT'));
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
/* SUB GROUP */
    var $t_msubgroup_view = "(select a.*,b.nmgroup from sc_mst.msubgroup a
left outer join sc_mst.mgroup b on a.idgroup=b.idgroup and a.grouptype=b.grouptype) as x";
    var $t_msubgroup_view_column = array('nmsubgroup','idgroup','nmgroup','grouptype','chold');
    var $t_msubgroup_view_order = array("nmsubgroup" => 'asc'); // default order
    private function _get_query_t_msubgroup()
    {

        $builder = $this->db->table($this->t_msubgroup_view);
        $builder->where(array('grouptype ' => 'CIT'));
        $i = 0;
        foreach ($this->t_msubgroup_view_column as $item)
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

                if(count($this->t_msubgroup_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_msubgroup_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_msubgroup_view_order))
        {
            $order = $this->t_msubgroup_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_msubgroup_view(){
        $builder = $this->_get_query_t_msubgroup();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_msubgroup_view_count_filtered()
    {
        $builder = $this->_get_query_t_msubgroup();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_msubgroup_view_count_all()
    {
        $builder = $this->_get_query_t_msubgroup();
        return $builder->countAllResults();
    }
    public function get_t_msubgroup_view_by_id($id)
    {
        $builder = $this->_get_query_t_msubgroup();
        $builder->where('idserial',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_msubgroup($param){
        return $this->db->query("select * from (select a.*,b.nmgroup from sc_mst.msubgroup a
left outer join sc_mst.mgroup b on a.idgroup=b.idgroup and a.grouptype=b.grouptype) as x where idsubgroup is not null $param ");
    }





    function list_menu_sub($param){
        return $this->db->query("select kodemenu,urut,namamenu,parentmenu,								
								case holdmenu when 't' then 'DI HOLD' else 'TIDAK DI HOLD' end as holdmenu ,linkmenu 
								from sc_mst.menuprg where child='S' ");
    }

    function list_menu_submenu($param){
        return $this->db->query("select kodemenu,urut,namamenu,parentmenu,parentsub,								
								case holdmenu when 't' then 'DI HOLD' else 'TIDAK DI HOLD' end as holdmenu ,linkmenu 
								from sc_mst.menuprg where child='P' ");
    }


    function list_menu_opt_utama(){
        return $this->db->query("select * from sc_mst.menuprg where child='U'");
    }

    function list_menu_opt_sub(){
        return $this->db->query("select * from sc_mst.menuprg where child='S'");
    }

    /* CAPITAL AUTHORIZATION */
    var $capital_authorization_view = "(select a.*,b.nmdept from sc_mst.capital_authorization a
left outer join sc_mst.departmen b  on a.kddept=b.kddept) as x";
    var $capital_authorization_view_column = array('id','username','nmdept','description','chold');
    var $capital_authorization_view_order = array("nmdept" => 'asc'); // default order
    private function _get_query_capital_authorization()
    {

        $builder = $this->db->table($this->capital_authorization_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        $i = 0;
        foreach ($this->capital_authorization_view_column as $item)
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

                if(count($this->capital_authorization_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->capital_authorization_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->capital_authorization_view_order))
        {
            $order = $this->capital_authorization_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_capital_authorization_view(){
        $builder = $this->_get_query_capital_authorization();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function capital_authorization_view_count_filtered()
    {
        $builder = $this->_get_query_capital_authorization();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function capital_authorization_view_count_all()
    {
        $builder = $this->_get_query_capital_authorization();
        return $builder->countAllResults();
    }
    public function get_capital_authorization_view_by_id($id)
    {
        $builder = $this->_get_query_capital_authorization();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_capital_authorization($param){
        return $this->db->query("select * from 
(select a.*,b.nmdept from sc_mst.capital_authorization a
left outer join sc_mst.departmen b  on a.kddept=b.kddept) as x where id is not null $param ");
    }
    /* CAPITAL AUTHORIZATION */

    public function q_lvljabatan($param){
        return $this->db->query("select * from 
(select *,round(pricemax) as pricemax1,kdlvl as id from sc_mst.lvljabatan) as x
where kdlvl is not null $param ");
    }

    public function q_capital_level_plafond($param){
        return $this->db->query("select *,round(plafond) as pricemax1 from sc_mst.capital_level_plafond where kdlvl is not null $param ");
    }

}
