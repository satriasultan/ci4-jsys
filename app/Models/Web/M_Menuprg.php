<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 9:42 AM
 *  * Last Modified: 7/7/20, 9:42 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


namespace App\Models\Web;
use CodeIgniter\Model;


class M_Menuprg extends Model
{

    function list_menu_sidebar_main($roleid){
        //$roleid = $this->session->get('roleid');
        return $this->db->query("select *,
replace(kodemenu,'.','_') as idkodemenu,
replace(parentmenu,'.','_') as idparentmenu,
replace(parentsub,'.','_') as idparentsub from sc_mst.menuprg where child='U' and kodemenu in 
								(select trim(parentmenu) from sc_mst.menuprg a 
								left outer join sc_mst.role_permissions b on a.kodemenu=b.kodemenu
								where b.roleid='$roleid' 
								group by parentmenu) order by urut asc");
    }

    function list_menu_sidebar_sub($roleid){
        //$roleid = $this->session->get('roleid');
        return $this->db->query("select *,
replace(kodemenu,'.','_') as idkodemenu,
replace(parentmenu,'.','_') as idparentmenu,
replace(parentsub,'.','_') as idparentsub from sc_mst.menuprg 
								where (child='S' or child='P') and kodemenu in (select distinct parentsub from sc_mst.menuprg where child='P' and
								kodemenu in (select kodemenu from sc_mst.role_permissions where roleid='$roleid')) order by urut asc");
    }

    function list_menu_sidebar_submenu($roleid){
        //$roleid = $this->session->get('roleid');
        return $this->db->query("select left(kodemenu,5) as menuurut, cast(right(kodemenu,-6) as integer) as nourut, *,
replace(kodemenu,'.','_') as idkodemenu,
replace(parentmenu,'.','_') as idparentmenu,
replace(parentsub,'.','_') as idparentsub from sc_mst.menuprg where child='P' and
								kodemenu in (select kodemenu from sc_mst.role_permissions where roleid='$roleid')
								order by urut asc");
    }
}
