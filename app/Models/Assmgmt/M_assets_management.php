<?php

namespace App\Models\Assmgmt;

use CodeIgniter\Model;

class M_assets_management extends Model
{
    function list_mgroup($param){
        return $this->db->query("select * from sc_mst.mgroup where grouptype='ASSETS' and coalesce(chold,'NO')!='YES' $param");
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

}
