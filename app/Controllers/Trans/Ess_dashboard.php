<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Ess_dashboard extends BaseController
{
    function index(){
        $data['title']="Dashboard Panel";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.6'; $versirelease='I.E.A.6/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='LEMBUR'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }


        $data['nama']=$nama;
        /* END APPROVE ATASAN */

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.E.A.6';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_global->q_lv_m_karyawan(" and nik='$niksession'")->getRowArray();

        $paramfilter = " and (nik ='$niksession' or nikatasan1='$niksession' or nikatasan2='$niksession' or nikatasan3='$niksession')";
        $daterange = $this->request->getPost('daterangefilter');


        if (!empty($daterange)) {
            $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
            $date1 = date('Y-m-d',strtotime($tgl1));
            $date2 = date('Y-m-d',strtotime($tgl2));
            $ptgl = " and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        } else {
            $datey = date('Y-m-d');
            $date1 = date('Y-m-d',strtotime($datey . "-20 days"));
            $date2 = date('Y-m-d',strtotime($datey . "+20 days"));
            $ptgl = " and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        }

        ///$parameter = " and to_char(tgl_kerja,'mmYYYY') = '$tgl'";
        $yearNow = date('Y');
        $data['list_libur'] = $this->m_ess_dashboard->q_mst_libur(" and to_char(tgl_libur,'yyyy')='$yearNow' ")->getResult();
        $data['dtlkary'] = $dtlkary;
        $data['niksession'] = $niksession;
        $paramfilter = " and ((nikatasan1='$niksession' and asstatus='A') or (nikatasan2='$niksession'  and asstatus='A1') or (nikatasan3='$niksession' and asstatus='A2'))";
        $data['count_approval'] = $this->m_ess_persetujuan->q_approval_system_karyawan(" and asstatus not in ('P','C','D') ".$paramfilter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);;
        return $this->template->render('trans/ess_dashboard/v_ess_dashboard',$data);

    }
}
