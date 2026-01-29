<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Job extends Model
{

    protected $table = 'sc_mst.branchjob';
    protected $primaryKey = 'idbranch';
    protected $returnType = 'object';

    function getTreeData()
    {
        return $this->select("
                TRIM(idbranch)   AS id,
                COALESCE(TRIM(induk), '0') AS pid,
                TRIM(nmbranch)   AS name,
            ")
            ->where('status', 'F')
            ->orderBy('idbranch', 'ASC')
            ->findAll();
    }

    public function getById($idbranch)
    {
        return $this->where('idbranch', $idbranch)->first();
    }

}