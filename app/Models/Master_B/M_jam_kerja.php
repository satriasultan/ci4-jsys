<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_jam_kerja extends Model
{
    function q_jam_kerja(){
        return $this->db->query("select * from sc_mst.jam_kerja order by kdjam_kerja asc");
    }

    function q_cekjam_kerja($kdjam_kerja){
        return $this->db->query("select * from sc_mst.jam_kerja where trim(kdjam_kerja)='$kdjam_kerja'");
    }
}
