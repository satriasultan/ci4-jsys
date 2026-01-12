<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Role extends Model
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

    var $t_mrole_view = "(select *,to_char(inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1 from sc_mst.role ) as x";
    var $t_mrole_view_column = array('roleid','rolename','inputdate1','inputby');
    var $t_mrole_view_order = array("roleid" => 'asc'); // default order
    private function _get_query_t_mrole()
    {

        $builder = $this->db->table($this->t_mrole_view);
        $i = 0;
        foreach ($this->t_mrole_view_column as $item)
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

                if(count($this->t_mrole_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_mrole_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_mrole_view_order))
        {
            $order = $this->t_mrole_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_mrole_view(){
        $builder = $this->_get_query_t_mrole();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_mrole_view_count_filtered()
    {
        $builder = $this->_get_query_t_mrole();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_mrole_view_count_all()
    {
        $builder = $this->_get_query_t_mrole();
        return $builder->countAllResults();
    }
    public function get_t_mrole_view_by_id($id)
    {
        $builder = $this->_get_query_t_mrole();
        $builder->where('id',(int)trim($id));
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_rolemaster($param){
        return $this->db->query("select * from sc_mst.role where id is not null $param ");
    }

//    ROLE PERMISSION ACCESSING
    function q_childmenu($roleid){
        return $this->db->query("select a.*,trim(trim(c.namamenu)||'/'||trim(b.namamenu)||'/'||trim(a.namamenu)) as menunya from sc_mst.menuprg a left outer join 
            sc_mst.menuprg b on a.parentsub=b.kodemenu and b.child='S' and b.chold='NO' left outer join 
            sc_mst.menuprg c on a.parentmenu=c.kodemenu and c.child='U' and c.chold='NO' 
            where a.child='P' and a.kodemenu not in (select trim(kodemenu) as kodemenu from (
            select trim(kodemenu) as kodemenu,roleid from sc_mst.role_permissions 
            union all
            select trim(kodemenu) as kodemenu,roleid from sc_tmp.role_permissions ) as x
            where x.roleid='$roleid') order by menunya");
    }

    function q_childmenu_usertmp($roleid){
        return $this->db->query("select * from (
            select a.roleid,a.kodemenu,b.menunya from sc_mst.role_permissions a left outer join (
            select a.*,trim(trim(c.namamenu)||'/'||trim(b.namamenu)||'/'||trim(a.namamenu)) as menunya from sc_mst.menuprg a left outer join 
            sc_mst.menuprg b on a.parentsub=b.kodemenu and b.child='S' and b.chold='NO' left outer join 
            sc_mst.menuprg c on a.parentmenu=c.kodemenu and c.child='U' and c.chold='NO'
            where a.child='P' 
            order by namamenu asc) b on a.kodemenu=b.kodemenu
            union all
            select a.roleid,a.kodemenu,b.menunya from sc_tmp.role_permissions a left outer join (
            select a.*,trim(trim(c.namamenu)||'/'||trim(b.namamenu)||'/'||trim(a.namamenu)) as menunya from sc_mst.menuprg a left outer join 
            sc_mst.menuprg b on a.parentsub=b.kodemenu and b.child='S' and b.chold='NO' left outer join 
            sc_mst.menuprg c on a.parentmenu=c.kodemenu and c.child='U' and c.chold='NO' 
            where a.child='P' 
            order by namamenu asc) b on a.kodemenu=b.kodemenu) as x
            where x.roleid='$roleid'
            group by roleid,kodemenu,menunya
            order by menunya");
    }

    function cek_mst_akses($roleid){
        return $this->db->query("select * from sc_mst.role_permissions where roleid='$roleid'");
    }

    function cek_tmp_akses($roleid){
        return $this->db->query("select * from sc_tmp.role_permissions where roleid='$roleid'");
    }

    function add_kdmnu_tmp($data = array()){
        $builder = $this->db->table('sc_tmp.role_permissions');
        $insert = $builder->insertBatch($data);
        return $insert?true:false;
    }

    function remove_kdmnu_tmp($data = array()){
        $builder = $this->db->table('sc_tmp.role_permissions');
        $delete = $builder->delete('sc_mst.role_permissions',$data);
        return $delete?true:false;
    }

    function fl_akses($roleid){
        return $this->db->query("insert into sc_mst.role_permissions(
								select * from sc_tmp.role_permissions where roleid='$roleid')
								");
    }
    function deltmp_akses($roleid){
        return $this->db->query("delete from sc_tmp.role_permissions where roleid='$roleid'");
    }

    /* LIST AKSES USER PERMISSIONS NYA */
    function list_akses_permission($roleid){
        return $this->db->query("select roleid,a.kodemenu,b.namamenu,
                                    case when a.chold='t' then 'YES' else 'NO' end as chold, 
                                    case when a.a_view='t' then 'YES' else 'NO' end as a_view, 
                                    case when a.a_input='t' then 'YES' else 'NO' end as a_input, 
                                    case when a.a_update='t' then 'YES' else 'NO' end as a_update, 
                                    case when a.a_delete='t' then 'YES' else 'NO' end as a_delete, 
                                    case when a.a_approve1='t' then 'YES' else 'NO' end as a_approve1, 
                                    case when a.a_approve2='t' then 'YES' else 'NO' end as a_approve2, 
                                    case when a.a_approve3='t' then 'YES' else 'NO' end as a_approve3, 
                                    case when a.a_filter='t' then 'YES' else 'NO' end as a_filter, 
                                    case when a.a_report='t' then 'YES' else 'NO' end as a_report
                                from sc_mst.role_permissions a
                                left outer join sc_mst.menuprg b on a.kodemenu=b.kodemenu and b.chold='NO'
                                where roleid='$roleid' ");
    }

    function detail_user_akses($roleid,$kodemenu){
        return $this->db->query("select a.*,b.namamenu from sc_mst.role_permissions a
								left outer join sc_mst.menuprg b on a.kodemenu=b.kodemenu
								where roleid='$roleid' and a.kodemenu='$kodemenu'");
    }

    function update_akses($roleid,$kodemenu,$info_update){
        $builder = $this->db->table('sc_mst.role_permissions');
        $builder->where('roleid',$roleid);
        $builder->where('kodemenu',$kodemenu);
        $builder->update($info_update);
    }

    function list_karyawan_param($param){
        return $this->db->query("select * from sc_mst.t_karyawan where coalesce(resign,'')='NO' $param");

    }

}
