<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Coa extends Model
{

    protected $table = 'sc_mst.coa';
    protected $primaryKey = 'idcoa';
    protected $returnType = 'object';

    function getTreeData()
    {
        return $this->select("
                TRIM(idcoa)   AS id,
                COALESCE(TRIM(induk), '0') AS pid,
                TRIM(nmcoa)   AS name,
                CASE 
                    WHEN isdetail = '1' THEN 'true'
                    ELSE 'false'
                END AS isDetail
            ")
            ->where('status', 'F')
            ->orderBy('idcoa', 'ASC')
            ->findAll();
    }

    public function getById($idcoa)
    {
        return $this->where('idcoa', $idcoa)->first();
    }

}