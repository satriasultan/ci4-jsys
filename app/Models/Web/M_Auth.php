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


class M_Auth extends Model
{
    protected $table      = 'pb';
    protected $returnType = 'array';


    public function getPb($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['a_1' => $id]);
        }
    }

    public function getResult(){
        $query = $this->db->query('select * from public.pb');
        return $query;
    }

    function getUser($param){
        $query = $this->db->query("select a.*,b.rolename,c.nmlocation,d.nmdept from sc_mst.user a
        left outer join sc_mst.role b on a.roleid=b.roleid
        left outer join sc_mst.mlocation c on a.loccode=c.idlocation
        left outer join sc_mst.departmen d on a.kddept=d.kddept 
        where username is not null $param ");
        return $query;
    }

    function getUserSession(){
        $this->session = \Config\Services::session();
        $username = trim($this->session->get('username'));
        $query = $this->db->query("select * from sc_mst.user where username is not null and username='$username' ");
        return $query;
    }

    function user_profile(){ //awas user profile untuk template bukan disini!!!!
        $this->session = \Config\Services::session();
        $nik=$this->session->get('nik');
        $username=$this->session->get('nama');
        return $this->db->query("select * from sc_mst.user where nik='$nik' and username='$username'");
    }

    function user_online(){
        $this->session = \Config\Services::session();
        $user=trim($this->session->get('nik'));
        $username=trim($this->session->get('nama'));
        $this->db->query("update sc_mst.user a set image=coalesce(b.cimage,'admin.jpg') from sc_mst.t_karyawan b
	    where a.nik=b.nik and trim(a.username)='$username'");

        return $this->db->query("select username as userid,ip as ip_address,username,nik,image from (
select a.tgl,a.ip,a.statuslogin,b.* from sc_log.useronline a
left outer join sc_mst.user b on a.username=b.username) as x
where username ='$username'");
    }

    function q_user_last_login(){
        return $this->db->query("select tgl,ip,username from (
select max(tgl) as tgl,max(ip) as ip,b.username
from sc_log.log_time a
left outer join sc_mst.user b on a.nik=b.username
where a.nik is not null group by b.username) as x
order by tgl desc
limit 5");

    }
}
