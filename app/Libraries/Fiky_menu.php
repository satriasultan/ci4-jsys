<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 2:48 PM
 *  * Last Modified: 7/7/20, 2:48 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */

namespace App\Libraries;

use App\Models\Globals\M_Gobals;

class Fiky_menu
{
    function menus($p1,$p2,$p3){
        $mu = new M_Gobals();
        /* CODE UNTUK VERSI */
        //$kodemenu='I.P.F.4';
        $kodemenu=$p1;
        //$versirelease='I.P.F.4/RELEASE.401';
        $versirelease=$p2;
        $userid=$p3;
        $vmn=$mu->q_menuprg($kodemenu)->getRowArray();
        $vmext=$mu->q_menuprg_ext($kodemenu)->getResult();
        $data['rows'] = $vmn;
        $data['res'] = $vmext;
        $data['xn'] = 'Author: '.MYAUTHOR.' : ';
        /* END CODE UNTUK VERSI */
        return $data;
    }

    function version($p1,$p2,$p3,$p4){
        $mu = new M_Gobals();
        $kodemenu=$p1;
        //$versirelease='I.P.F.4/RELEASE.401';
        $versirelease=$p2;
        //$userid=$this->_CI->session->userdata('nama');
        $param = " and kodemenu='$kodemenu'";
        $vrdb = count($mu->q_versidb($param)->getResultArray());
        if ($vrdb<=0){
            $mu->insert_version($kodemenu);
            $vdb=$mu->q_versidb($param)->getRowArray();
        } else {
            $vdb=$mu->q_versidb($param)->getRowArray();
        }

        $vmn=$mu->q_menuprg($kodemenu)->getRowArray();
        $versidb=$vdb['vrelease'];
        if($versidb<>$versirelease){
            $infoversiold= array (
                'vreleaseold'   => $versidb,
                'vdateold'      => $vdb['vdate'],
                'vauthorold'    => $vdb['vauthor'],
                'vketeranganold'=> $vdb['vketerangan'],
            );
            $mu->q_update_version($kodemenu,$infoversiold);

            $infoversi= array (
                'vrelease'   => $versirelease,
                'vdate'      => date('2017-07-10 11:18:00'),
                'vauthor'    => 'FIKY',
                'vketerangan'=> 'PENAMBAHAN VERSION RELEASE',
                'update_date' => $p3,
                'update_by'   => $p4,
            );
            $mu->q_update_version($kodemenu,$infoversi);
        }
        $vdb=$mu->q_versidb($param)->getRowArray();
        $versidb=$vdb['vrelease'];
        $data['version']=$versidb;
        /* END CODE UNTUK VERSI */
        return $versidb;
    }

    function imcoming(){
        $str = '';
        $str .= '
        
        ';
    }
}
