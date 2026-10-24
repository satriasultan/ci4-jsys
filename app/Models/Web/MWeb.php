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


    public function cek_user_login($username, $password, $hold = 'NO')
    {
        $tglskg = date('Y-m-d H:i:s');

        // Pertahankan algoritma password lama untuk kompatibilitas.
        // Migrasi ke password_hash/password_verify dilakukan terpisah.
        $passwordHash = md5(
            md5(
                md5(strtoupper($password))
            )
        );

        return $this->db->table('sc_mst.user')
            ->where('expdate >', $tglskg)
            ->where('hold_id', $hold)
            ->where('username', $username)
            ->where('passwordweb', $passwordHash)
            ->get();
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
