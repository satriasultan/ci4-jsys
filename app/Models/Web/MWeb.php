<?php

namespace App\Models\Web;

use CodeIgniter\Model;

class MWeb extends Model
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
        $query = $this->db->query("select * from sc_mst.user where username is not null $param ");
        return $query;
    }

    function cek_user_login($username, $password,$hold='NO'){
        $tglskg=date("Y-m-d H:i:s");

        return $this->db->query("select * from sc_mst.user where expdate > '$tglskg' and hold_id='$hold' and username = '$username' 
                            and passwordweb = '".md5(md5(md5(strtoupper($password))))."'");

        //$builder = $this->table('sc_mst.user');
        //$builder->select('*');
        //$builder->where('expdate >',$tglskg);
        //$builder->where('hold_id',$hold);
        //$builder->where('username',$username);
        //$builder->where('passwordweb',md5(md5(md5(strtoupper($password)))));
        //$query = $builder->get();

        //if ($builder->countAll() == 1)
        //{
        //    return $builder->getResultArray();
        //}


    }

    function getUserSession(){
        // $username = $this->session->get('username');
        $query = $this->db->query("select * from sc_mst.user where username is not null and username='$username' ");
        return $query;
    }

    function schedular(){
        $tgl = date('Y-m-d');
        return $this->db->query("select sc_trx.pr_cutirata_tgl('$tgl')");
    }
}
