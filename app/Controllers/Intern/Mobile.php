<?php

namespace App\Controllers\Intern;

use App\Controllers\BaseController;

class Mobile extends BaseController
{

    public function index()
    {
        echo json_encode(ARRAY('Geolocation' => 'True','Wilayah'=>'True')) ;
    }

    function downloadMst(){
        $startDate=$this->request->getPost('startDate');
        $endDate=$this->request->getPost('endDate');
        $userId=trim($this->request->getPost('userId'));

        $param="";
        //$paramuser=" and trim(username)='$userId' and trim(branch)='$branch'";
        $paramuser=" and trim(username)='$userId'";
        $paramin=" and fc_entrydate between '$startDate' and '$endDate'";
        $user = $this->m_mobile->q_mst_user($paramuser)->getResult();
        $mbarang = $this->m_mobile->q_mst_mbarang($param)->getResult();
        $marea = $this->m_mobile->q_mst_marea($param)->getResult();
        $costcenter = $this->m_mobile->q_mst_costcenter($param)->getResult();


        header("Content-Type: text/json");
        echo json_encode(
            array(
                'success' => true,
                'message' => 'Data Sukses',
                'body' => array(
                    'user' => $user,
                    'mbarang' => $mbarang,
                    'marea' => $marea,
                    'costcenter' => $costcenter,
                )
            )
            , JSON_PRETTY_PRINT );
    }

}
