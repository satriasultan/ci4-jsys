<?php

namespace App\Controllers\Intern;

use App\Controllers\BaseController;

class WebApprovals extends BaseController
{
    
	function index(){
			ECHO "FORBIDDEN";
	}


    function listWebApprovals(){
        $data['title']="Approval Management System";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.D.1'; $versirelease='I.T.D.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.D.1'";
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
        $kmenu='I.T.D.1';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);

        return $this->template->render('intern/webapprovals/v_list',$data);

    }

    function list_webapprovals(){
        //AHC ADMIN HC // ML SUPER ADMIN IRGA
        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $list = $this->m_webApprovals->get_t_trx_approvals_system_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            $row[] = $no;
            $row[] = $lm->docno;
            $row[] = $lm->doctypename;
            $row[] = $lm->docdate1;
            if (trim($lm->status)==='0'){
                $row[] = '<span class="label label-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->status)==='1') {
                $row[] = '<span class="label label-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->status)==='2') {
                $row[] = '<span class="label label-warning" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="label label-primary" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_webApprovals->t_trx_approvals_system_view_count_all(),
            "recordsFiltered" => $this->m_webApprovals->t_trx_approvals_system_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



}
