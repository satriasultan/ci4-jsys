<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Ess_calendar extends BaseController
{
    function index(){
        $data['title']="Panel Personal Calendar";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.8'; $versirelease='I.E.A.8/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.E.A.8'";
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
        $kmenu='I.E.A.8';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $var = hex2bin($this->request->getGet('var'));
        if (!empty($var)) {
            $data['niksession']=$var;
        } else {
            $data['niksession']=$niksession;
        }



        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_global->q_lv_m_karyawan(" and nik='".$data['niksession']."'")->getRowArray();
        $data['dtlkary'] = $dtlkary;
        $paramfilter = " and (nik ='$niksession' or nikatasan1='$niksession' or nikatasan2='$niksession' or nikatasan3='$niksession')";
        $daterange = $this->request->getPost('daterangefilter');

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/ess_calendar/v_ess_calendar',$data);



    }

    function api_postdata(){
        //$nik = trim($this->session->get('nik'));
        $nik = trim($this->request->getGet('var'));
        $qop = $this->m_ess_dashboard->q_transready_calendar("and trim(nik)='$nik'")->getResult();
        foreach($qop as $hos) {
            $ini[] = array(
                'api_postdata' => 'api_postdata',
                'allDay' => 'false',
                'title' => $hos->title,
                'start' => $hos->tgl,
                'end' => '',
                'url' => 'false',
                'backgroundColor' => $hos->colourcode,
                'borderColor' => $hos->colourcode,
            );
        }
        echo json_encode($ini,JSON_PRETTY_PRINT);
    }


    function list_karyawan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $var = $this->request->getGet('var');
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_param_global_');
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($var)) {
            $paramvar = " and nik='$var'";
        } else {
            $paramvar = "";
        }

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.E.A.2';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));

        if ($role==='ML' or $role==='IT') {
            $paramfilter = " and trim(coalesce(resign,'NO')) !='YES' $paramvar";
        } else {
            $paramfilter = " and (nik ='$niksession' or nikatasan1='$niksession' or nikatasan2='$niksession' or nikatasan3='$niksession') $paramvar";
        }



        $param=" and (upper(nmlengkap) like '%$search%' $paramfilter) or ( nik like '%$search%' $paramfilter ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_ess_dashboard->q_karyawan_base64($param)->getResult();
        //$count = $this->m_ess_cuti->q_karyawan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => 1,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'paramfilter' => $paramfilter,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

}
