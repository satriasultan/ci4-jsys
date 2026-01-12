<?php

namespace App\Models\Intern;

use CodeIgniter\Model;

class M_Mobile extends Model
{

    function q_mst_user($param){
        return $this->db->query("select coalesce(trim(branch 		     ::text),'') as branch 	,     
                                            coalesce(trim(nik              ::text),'') as nik        ,  
                                            coalesce(trim(username         ::text),'') as username   ,  
                                            coalesce(trim(passwordweb      ::text),'') as passwordweb,  
                                            coalesce(trim(roleid         ::text),'') as level_id   ,  
                                            coalesce(trim(roleid      ::text),'') as level_akses,  
                                            coalesce(trim(roleid      ::text),'') as divisi,  
                                            coalesce(trim(expdate          ::text),'') as expdate    ,  
                                            coalesce(trim(hold_id          ::text),'') as hold_id    ,  
                                            coalesce(trim(inputdate        ::text),'') as inputdate  ,  
                                            coalesce(trim(inputby          ::text),'') as inputby    ,  
                                            coalesce(trim(editdate         ::text),'') as editdate   ,  
                                            coalesce(trim(editby           ::text),'') as editby     ,  
                                            coalesce(trim(lastlogin        ::text),'') as lastlogin  ,  
                                            coalesce(trim(image            ::text),'') as image      ,  
                                            coalesce(trim(loccode          ::text),'') as loccode    ,  
                                            coalesce(trim(loccode          ::text),'') as location    ,  
                                            coalesce(trim(roleid           ::text),'') as roleid     ,  
                                            coalesce(trim(lang             ::text),'') as lang       ,  
                                            coalesce(trim(iduser           ::text),'') as iduser     
                                            from sc_mst.user where username is not null $param");
    }


    function q_mst_mbarang($param){
        return $this->db->query("select coalesce(trim(idbarang	   ::text),'') as idbarang	   ,
coalesce(trim(nmbarang     ::text),'') as nmbarang     ,
coalesce(trim(idgroup      ::text),'') as idgroup      ,
coalesce(trim(idsubgroup   ::text),'') as idsubgroup   ,
coalesce(trim(idtype       ::text),'') as idtype       ,
coalesce(trim(grade        ::text),'') as grade        ,
coalesce(trim(lsize        ::text),'') as lsize        ,
coalesce(trim(deflocation  ::text),'') as deflocation  ,
coalesce(trim(defarea      ::text),'') as defarea      ,
coalesce(trim(description  ::text),'') as description  ,
coalesce(trim(unit         ::text),'') as unit         ,
coalesce(trim(subunit      ::text),'') as subunit      ,
coalesce(trim(subunitenable::text),'') as subunitenable,
coalesce(trim(lastprice    ::text),'0') as lastprice    ,
coalesce(trim(onhand       ::text),'0') as onhand       ,
coalesce(trim(allocated    ::text),'0') as allocated    ,
coalesce(trim(uninvoiced   ::text),'0') as uninvoiced   ,
coalesce(trim(tmpalloca    ::text),'0') as tmpalloca    ,
coalesce(trim(lastrxdate   ::text),'') as lastrxdate   ,
coalesce(trim(lastrxdoc    ::text),'') as lastrxdoc    ,
coalesce(trim(idbarcode    ::text),'') as idbarcode    ,
coalesce(trim(sku          ::text),'') as sku          ,
coalesce(trim(expdate      ::text),'') as expdate      ,
coalesce(trim(batch        ::text),'') as batch        ,
coalesce(trim(mfgdate      ::text),'') as mfgdate      ,
coalesce(trim(maks_daystock::text),'0') as maks_daystock,
coalesce(trim(chold        ::text),'') as chold        ,
coalesce(trim(inputby      ::text),'') as inputby      ,
coalesce(trim(inputdate    ::text),'') as inputdate    ,
coalesce(trim(status       ::text),'') as status       ,
coalesce(trim(setminstock  ::text),'') as setminstock  ,
coalesce(trim(minstock     ::text),'0') as minstock from sc_mst.mbarang where idbarang is not null $param");
    }

    function q_mst_marea($param){
        return $this->db->query("select 
coalesce(trim(idlocation::text),'') as idlocation,
coalesce(trim(idarea::text),'') as idarea,
coalesce(trim(nmarea::text),'') as nmarea,
coalesce(trim(chold::text),'') as chold,
coalesce(trim(status::text),'') as status,
coalesce(trim(description::text),'') as description,
coalesce(trim(idcomplex::text),'') as idcomplex,
coalesce(trim(idbarcode::text),'') as idbarcode,
coalesce(trim(idtype::text),'') as idtype,
coalesce(trim(inputdate::text),'') as inputdate,
coalesce(trim(inputby::text),'') as inputby,
coalesce(trim(updatedate::text),'') as updatedate,
coalesce(trim(updateby::text),'') as updateby from sc_mst.marea where idarea is not null $param
        ");
    }

    function q_mst_costcenter($param){
        return $this->db->query("select 
coalesce(trim(idcostcenter::text),'') as idcostcenter,
coalesce(trim(nmcostcenter::text),'') as nmcostcenter
from sc_mst.costcenter where idcostcenter is not null $param
        ");
    }
}
