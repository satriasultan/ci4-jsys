<?php
/**
 * Created by PhpStorm.
 * User: FIKY-PC
 * Date: 11/04/2019
 * Time: 15:41
 */

namespace App\Libraries;

use App\Models\Globals\M_Gobals;

#[\AllowDynamicProperties]
class Fiky_version
{

    protected $_CI;

    public function __construct()
    {
        //parent::__construct();
        //$this->session = \Config\Services::session();
        $this->db = \Config\Database::connect('default');
        $this->fiky_encryption = new Fiky_encryption();
        $this->m_global = new M_Gobals();
    }

    function coba(){
        return 'TEST';
        /**
         * P1 : KODEMENU
         * P2 : NAMA VERSI
         * P3 : SESSION
         */
    }
    function version($p1,$p2,$p3,$p4){
        
        
        $this->fiky_encryption->first_template_license();
        $this->fiky_encryption->onExpireMacLock();
        $this->fiky_encryption->onExpireLc();
        /* CODE UNTUK VERSI */
        //$kodemenu='I.P.F.4';
        $kodemenu=$p1;
        //$versirelease='I.P.F.4/RELEASE.401';
        $versirelease=$p2;
        //$userid=$this->session->userdata('nama');
        $vrdb = $this->m_global->q_versidb($kodemenu)->getNumRows();
        if ($vrdb<=0){
            $this->m_global->insert_version($kodemenu);
            $vdb=$this->m_global->q_versidb($kodemenu)->getRowArray();
        } else {
            $vdb=$this->m_global->q_versidb($kodemenu)->getRowArray();
        }

        $vmn=$this->m_global->q_menuprg($kodemenu)->getRowArray();
        $versidb=$vdb['vrelease'];
        if($versidb<>$versirelease){
            $infoversiold= array (
                'vreleaseold'   => $versidb,
                'vdateold'      => $vdb['vdate'],
                'vauthorold'    => $vdb['vauthor'],
                'vketeranganold'=> $vdb['vketerangan'],
            );
            $this->m_global->q_update_version($kodemenu,$infoversiold);

            $infoversi= array (
                'vrelease'   => $versirelease,
                'vdate'      => date('2017-07-10 11:18:00'),
                'vauthor'    => 'FIKY',
                'vketerangan'=> 'PENAMBAHAN VERSION RELEASE',
                'update_date' => $p3,
                'update_by'   => $p4,
            );
            $this->m_global->q_update_version($kodemenu,$infoversi);
        }
        $vdb=$this->m_global->q_versidb($kodemenu)->getRowArray();
        $versidb=$vdb['vrelease'];
        $data['version']=$versidb;
        /* END CODE UNTUK VERSI */
        return $versidb;
    }


}
