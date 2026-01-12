<?php

namespace App\Controllers\Absenmesin;

use App\Controllers\BaseController;
require FCPATH . 'vendor/autoload.php';
use TADPHP\TAD;
use TADPHP\TADFactory;

class Absenmesin extends BaseController
{

    function userinfo(){
        $data['title']='TARIK DATA USERINFO '; //aksesconvert absenMesin
        $kmenu='I.T.B.6';
        if($this->uri->getSegment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(4)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();
        return $this->template->render('absenmesin/v_userinfo',$data);
    }

    function tarik_userinfo() {
        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';

        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        $tglawal=date('Y-m-d',strtotime($tgla));
        $tglakhir=date('Y-m-d',strtotime($tglb));
        $kdcabang=trim($this->request->getPost('kdcabang'));
        $comberan = 'TRUE';
        if(($kdcabang=='NBISMG') and ($this->load->database('NBIDMK',TRUE)->initialize())){
            $datane=$this->m_absenMesin->show_userinfo_smg($tgl1,$tgl2)-> getResult();
        } else if($kdcabang=='NBISBY'   and ($this->load->database('NBISBY',TRUE)->initialize())){
            $datane=$this->m_absenMesin->show_userinfo_sby($tgl1,$tgl2)-> getResult();
        } else if($kdcabang=='NBIJKT'   and ($this->load->database('NBIJKT',TRUE)->initialize())) {
            $datane = $this->m_absenMesin->show_userinfo_jkt($tgl1, $tgl2)-> getResult();
        } else if($kdcabang=='NBIDMK'   and ($this->load->database('NBIDMK',TRUE)->initialize())) {
            $datane = $this->m_absenMesin->show_userinfo_dmk($tgl1, $tgl2)-> getResult();
        } else {
            $comberan = 'FALSE';
        }

        if ($comberan =='TRUE') {
            foreach ($datane as $dta){
                $paramxr = "and userid='".$dta->USERID."' and badgenumber ='".$dta->Badgenumber."'";
                $xr = $this->m_absenMesin->cek_tmp_userinfo($paramxr)->getNumRows();
                if ( $xr <=0 ) {
                    $info=array(
                        'userid'=>"$dta->USERID",
                        'badgenumber'=>"$dta->Badgenumber",
                        't_name'=>"$dta->Name",
                        'gender'=> "$dta->Gender",
                        'cardno'=>"$dta->CardNo",
                    );
                    $this->m_absenMesin->simpan_userinfo($info);
                }
            }
            echo 'TRUE';
        } else { echo 'FALSE'; }

    }

    function userabsen(){
        $data['title']='Tarik User Info'; //aksesconvert absenMesin
        $kmenu='I.T.B.6';
        if($this->uri->getSegment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(4)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();

        // $datane = $this->m_absenMesin->show_user_dmk('01-01-2019 01:01:01', '01-30-2019 01:01:01')->getResult();
        return $this->template->render('absenmesin/v_userabsen',$data);
    }

    function tarik_userabsen(){
        $tanggal=$this->request->getPost('tgl');
        $kdcabang=$this->request->getPost('kdcabang');
        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';

        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        $tglawal=date('Y-m-d',strtotime($tgla));
        $tglakhir=date('Y-m-d',strtotime($tglb));
        $kdcabang=$this->request->getPost('kdcabang');
        $comberan = 'TRUE';
        if(($kdcabang=='NBISMG') and ($this->load->database('NBIDMK',TRUE)->initialize())){
            $datane=$this->m_absenMesin->show_user_smg($tgl1,$tgl2);
        } else if($kdcabang=='NBISBY'   and ($this->load->database('NBISBY',TRUE)->initialize())){
            $datane=$this->m_absenMesin->show_user_sby($tgl1,$tgl2);
        } else if($kdcabang=='NBIJKT'   and ($this->load->database('NBIJKT',TRUE)->initialize())) {
            $datane = $this->m_absenMesin->show_user_jkt($tgl1, $tgl2);
        } else if($kdcabang=='NBIDMK'   and ($this->load->database('NBIDMK',TRUE)->initialize())) {
            $datane = $this->m_absenMesin->show_user_dmk($tgl1, $tgl2);
        } else {
            $comberan = 'FALSE';
        }

        if ($comberan =='TRUE') {
            $datane = $datane -> getResult();
            $this->db->query("delete from sc_tmp.checkinout where to_char(checktime,'yyyy-mm-dd') between '$tglawal' and '$tglakhir'");
            foreach ($datane as $dta){
                $info=array(
                    'userid'=>"$dta->USERID",
                    'badgenumber'=>"$dta->Badgenumber",
                    'nama'=>"$dta->Name",
                    'checktime'=> date('Y-m-d H:i:s',strtotime(trim($dta->CHECKTIME))),
                    'inputan'=>'M',
                    'inputby'=>$this->session->get('nik'),
                );
                $this->m_absenMesin->simpan($info);
            }
            $txt='select sc_tmp.update_badgenumber('.chr(39).$tglawal.chr(39).','.chr(39).$tglakhir.chr(39).')';
            $this->db->query($txt);
            echo 'TRUE';
        } else { echo 'FALSE'; }

        //echo $tglawal;
        //echo $tglakhir;
        // echo json_encode(array("status" => TRUE));
    }

    function xtest(){
        for ($i = 1; $i <= 10; $i++):
            sleep(1);
            echo "$i\n";
            ob_flush(); flush();
        endfor;

    }

    function filter(){
        $data['title']='Tarikan Data Mesin absenMesin'; //aksesconvert absenMesin
        $kmenu='I.T.B.6';
        if($this->uri->getSegment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(4)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();
        return $this->template->render('absenmesin/v_filterabsenMesin',$data);
    }

    function ajax_tglakhir_ci($kdcabang){
        $data = $this->m_absenMesin->tglakhir_ci($kdcabang)->getRowArray();
        echo json_encode($data);
    }

    function ajax_tglakhir_tr($kdcabang){
        $data = $this->m_absenMesin->tglakhir_tr($kdcabang)->getRowArray();
        echo json_encode($data);
    }

    function filter_lembur(){
        $data['title']='Filter Data Lembur';
        return $this->template->render('trans/absenMesin/v_filterlembur',$data);
    }

    function filter_koreksi(){
        if($this->uri->getSegment(4)=="rep_succes"){
            $data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
        } elseif ($this->uri->getSegment(4)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }

        $kmenu='I.T.B.7'; //aksesupdate koreksi absenMesin
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['title']='Filter Koreksi Data absenMesin';
        $data['list_karyawan']=$this->m_absenMesin->q_karyawan()->getResult();
        $data['list_regu']=$this->m_absenMesin->q_regu()->getResult();
        $data['list_dept']=$this->m_absenMesin->q_department()->getResult();

        return $this->template->render('trans/absenMesin/v_filterkoreksi',$data);
    }

    function filter_detail(){
        $kmenu='I.T.B.9'; //aksesview dan akses download absenMesin
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['title']='Filter Detail Data absenMesin';
        $data['list_karyawan']=$this->m_absenMesin->q_karyawan()->getResult();
        $data['list_regu']=$this->m_absenMesin->q_regu()->getResult();
        $data['list_dept']=$this->m_absenMesin->q_department()->getResult();
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();
        $data['list_trxabsen']=$this->m_absenMesin->q_trxabsen()->getResult();
        return $this->template->render('trans/absenMesin/v_filterdtlabsenMesin',$data);
    }

    function filter_input(){
        if($this->uri->getSegment(4)=="kode_failed")
            $data['message']="<div class='alert alert-warning'>absenMesin Gagal Disimpan</div>";
        else if($this->uri->getSegment(4)=="rep_succes")
            $data['message']="<div class='alert alert-success'>absenMesin Sukses Disimpan </div>";
        else
            $data['message']='';
        $data['title']='DETAIL SIMPAN TRANSREADY';
        $kmenu='I.T.B.10'; //akses view transready
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_karyawan']=$this->m_absenMesin->q_karyawan()->getResult();
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();
        //$tglakhir1=$this->m_absenMesin->tglakhir_tr()->getRowArray();
        //$tglakhir=$tglakhir1['tglakhir'];
        //$data['tglakhir']=$tglakhir;
        return $this->template->render('trans/absenMesin/v_filtertarik',$data);
    }

    public function index()
    {

        /*   for ($x = 0; $x <= 1000; $x++) {
               echo "The number is: $x <br>";
           }*/


        $tanggal=$this->request->getPost('tgl');
        $kdcabang=$this->request->getPost('kdcabang');
        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';

        //echo date('m-d-Y H:i:s',strtotime($tgl1)).'<br>';
        //echo date('m-d-Y H:i:s',strtotime($tgl2));
        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        if (empty($tanggal)){
            //return redirect()->to(base_url('trans/absenMesin/filter');
            echo "Tanggal Kosong ";
        }
        $data['title']="DATA MESIN ABSEN $tgla hingga $tglb Untuk Wilayah $kdcabang";
        $data['tgl1']=$tgla;
        $data['tgl2']=$tglb;
        $data['kdcabang']=$kdcabang;
        $data['message']='';
        if($kdcabang=='NBISMG'){
            $data['ttldata']=$this->m_absenMesin->ttldata_smg($tgl1,$tgl2)->getRowArray();
            $data['list_absen']=$this->m_absenMesin->show_user_smg($tgl1,$tgl2)->getResult();
        } else if($kdcabang=='NBISBY'){
            $data['ttldata']=$this->m_absenMesin->ttldata_sby($tgl1,$tgl2)->getRowArray();
            $data['list_absen']=$this->m_absenMesin->show_user_sby($tgl1,$tgl2)->getResult();
        } else if($kdcabang=='NBIJKT'){
            $data['ttldata']=$this->m_absenMesin->ttldata_jkt($tgl1,$tgl2)->getRowArray();
            $data['list_absen']=$this->m_absenMesin->show_user_jkt($tgl1,$tgl2)->getResult();
        } else if($kdcabang=='NBIDMK'){
            $data['ttldata']=$this->m_absenMesin->ttldata_dmk($tgl1,$tgl2)->getRowArray();
            $data['list_absen']=$this->m_absenMesin->show_user_dmk($tgl1,$tgl2)->getResult();
        } else {
            echo "Halo";
            //return redirect()->to(base_url('trans/absenMesin/filter');
        }

        exit;
        //return $this->template->render('trans/absenMesin/v_absenMesin',$data);
    }


    function testConnFinger(){
        $this->fikyattd->connectAttd("10.0.15.1", 0);
        $this->fikyattd->connect();
        if ($this->fikyattd->connect()){
            //connection successfull
            echo 'CONNECT';
            $this->fikyattd->disconnect();
        } else {
            //connection fail
            echo 'DISCONNECT';
        }
    }

    function getInformationAttendance(){
        $data['title'] = "Get Attendance Information";
        $data['getConString'] = $this->fikyattd->connectAttd("10.0.15.1", 4370);
        $data['zk'] = $this->fikyattd;
        $this->load->view('absenmesin/absenmesin/v_getAttendanceInformation',$data);
    }

    function getFinger(){
        $getConString = $this->fikyattd->connectAttd("192.168.15.222", 4370);
        //new $getConString;
        $zk = $this->fikyattd;
        $this->db->truncate('sc_tmp.mc_userinfo');
        $this->db->truncate('sc_tmp.mc_checkinout');
        if ($zk->connect()){
            $user = $zk->getUser();
            while( list($uid, $get) = each($user) ) {
                if ($get[2] == LEVEL_ADMIN)
                    $role = 'ADMIN';
                elseif($get[2] == LEVEL_USER)
                    $role = 'USER';
                else
                    $role = 'Unknown';

                /*       echo 'UID: '.$uid;
                         echo 'ID: '.$get[0];
                         echo 'Name: '.$get[1];
                         echo 'Role: '.$role;
                         echo 'Password: '.$get[3];*/

                $userinfo = array (
                    'userid' => $uid,
                    'badgenumber' => $get[0],
                    't_name' => $get[1],
                    't_role' => $role,
                    't_password' => $get[3]
                );
                $builder = $this->db->table('sc_tmp.mc_userinfo');
                $builder->insert($userinfo);
            }

            $attendance = $zk->getAttendance();
            while( list($idx, $attendancedata) = each($attendance) ) {
                /*            echo 'Index: '.$idx;
                            echo 'ID: '.$attendancedata[0];
                            echo 'Status: '.$attendancedata[1];
                            echo 'Date: '.date("d-m-Y", strtotime($attendancedata[2]));
                            echo 'Time: '.date("H:i:s", strtotime($attendancedata[2]));*/

                $useratt = array (
                    'userid' => $idx,
                    'badgenumber' => $attendancedata[0],
                    'status' => $attendancedata[1],
                    'checkinout' => date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))
                );
                $builderx = $this->db->table('sc_tmp.mc_checkinout');
                $builderx->insert($useratt);
            }

            $zk->getTime();
            $zk->enableDevice();
            $zk->disconnect();
            echo 'SUCCESS';
        } else {
            //connection fail
            echo 'DISCONNECT';
        }

    }
    /* TARIK MESIN USERINFO */
    function mc_userinfo(){
        $data['title']='TARIK DATA USERINFO DIRECT MESIN ABSENSI'; //aksesconvert absenMesin
        $kmenu='I.T.B.6';
        if($this->uri->getSegment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(4)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();


        return $this->template->render('absenmesin/v_tarik_mc_userinfo',$data);
    }

    function save_mc_userinfo(){
        //$kdcabang = $this->fiky_encryption->dekript(trim($this->uri->getSegment(4))));

        $kdcabang=trim($this->request->getPost('kdcabang'));
        $param = " and kdcabang='$kdcabang' and ctype='MC' and chold='NO'";
        $set = $this->m_absenMesin->q_setup_attendance($param)-> getRowArray();

        $getConString = $this->fikyattd->connectAttd($set['ipaddress'], $set['port']);
        //new $getConString;
        $zk = $this->fikyattd;
        $this->db->truncate('sc_tmp.mc_userinfo');
        if ($zk->connect()) {
            $user = $zk->getUser();
            while( list($uid, $get) = each($user) ) {
                if ($get[2] == LEVEL_ADMIN)
                    $role = 'ADMIN';
                elseif($get[2] == LEVEL_USER)
                    $role = 'USER';
                else
                    $role = 'Unknown';

                /*       echo 'UID: '.$uid;
                         echo 'ID: '.$get[0];
                         echo 'Name: '.$get[1];
                         echo 'Role: '.$role;
                         echo 'Password: '.$get[3];*/

                $userinfo = array (
                    'userid' => $uid,
                    'badgenumber' => $get[0],
                    't_name' => $get[1],
                    't_role' => $role,
                    't_password' => $get[3]
                );
                $this->db->insert('sc_tmp.mc_userinfo',$userinfo);
            }
            $zk->getTime();
            $zk->enableDevice();
            $zk->disconnect();
            echo 'TRUE';
        } else {
            echo 'FALSE';
        }

    }

    /* TARIK MESIN ABSEN */
    function mc_userabsen()
    {
        $nama=$this->session->get('nik');
        $data['title']="Tarik Data Absensi Mesin";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nik'));
        $kodemenu='I.T.B.6'; $versirelease='I.T.B.6/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        if($this->uri->getSegment(3)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(3)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=trim($this->session->get('nama'));

        $param = '';
        $data['list_machine']=$this->m_absenMesin->q_set_connection($param)->getResult();
        return $this->template->render('absenmesin/v_tarik_mc_userabsen',$data);
    }

    /* TARIK MESIN ABSEN */

    function automaticaly_getfinger(){
        //read data finger with id =
        $id = 1;
        $param = " and id='$id'";
        $set = $this->m_absenMesin->q_setup_attendance($param)->getRowArray();

        if (trim($set['read_method'])==='RATE_TAD')
        {   $this->db->query("delete from sc_tmp.mc_checkinout;");
            $options = [
                'ip' => trim($set['ipaddress']),
                'com_key' => trim($set['com_key']),
                //'com_key' => 111825,
                'soap_port' => trim($set['port']),
            ];
            $tad = new TADFactory($options);
            $con = $tad->get_instance();
            if ($con->is_alive()) {
                $data = $con->get_att_log()->to_array();
                //$data = $con->get_user_info()->to_array();
                foreach ($data["Row"] as $key => $log) {
                    $useratt = array(

                        'userid' => $log['PIN'],
                        'badgenumber' => $log['PIN'],
                        'status' => ($log['Status']==='1') ? 'OUT' : 'IN',
                        'checkinout' => $log['DateTime'],
                    );
                    $this->db->insert('sc_tmp.mc_checkinout', $useratt);
                }



                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        } else {
            $getConString = $this->fikyattd->connectAttd($set['ipaddress'], $set['port']);
            //new $getConString;
            $zk = $this->fikyattd;
            $this->db->truncate('sc_tmp.mc_checkinout');
            if ($zk->connect()) {
                $attendance = $zk->getAttendance();
                while( list($idx, $attendancedata) = each($attendance) ) {

                    $useratt = array (
                        'userid' => $idx,
                        'badgenumber' => $attendancedata[0],
                        'status' => $attendancedata[1],
                        'checkinout' => date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))
                    );
                    $this->db->insert('sc_tmp.mc_checkinout',$useratt);
                }

                $zk->getTime();
                $zk->enableDevice();
                $zk->disconnect();
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }
    }

    function save_mc_userabsen(){
        //$kdcabang = $this->fiky_encryption->dekript(trim($this->uri->getSegment(4))));
        $daterange = trim($this->request->getPost('daterange'));
        $da = explode(' - ',$daterange);
        $tglawal = date('Y-m-d',strtotime($da[0]));
        $tglakhir = date('Y-m-d',strtotime($da[1]));
        $id=trim($this->request->getPost('id'));
        $param = " and id='$id'";

        $set = $this->m_absenMesin->q_setup_attendance($param)->getRowArray();


        if (trim($set['read_method'])==='RATE_TAD')
        {   //$this->db->query("delete from sc_tmp.mc_checkinout;");
            $options = [
                'ip' => trim($set['ipaddress']),
                'com_key' => trim($set['com_key']),
                //'com_key' => 111825,
                'soap_port' => trim($set['port']),
            ];
            $tad = new TADFactory($options);
            $con = $tad->get_instance();
            if ($con->is_alive()) {
                $att_logs = $con->get_att_log();
                // Now, you want filter the resulset to get att logs between '2014-01-10' and '2014-03-20'.
                $filtered_att_logs = $att_logs->filter_by_date(
                    ['start' => $tglawal,'end' => $tglakhir]
                );
                $data = $filtered_att_logs->to_array();
                //$data = $con->get_att_log()->to_array();
                //$data = $con->get_user_info()->to_array();
                foreach ($data["Row"] as $key => $log) {
                    $useratt = array(
                        'userid' => $log['PIN'],
                        'badgenumber' => $log['PIN'],
                        'status' => ($log['Status']==='1') ? 'OUT' : 'IN',
                        'checkinout' => $log['DateTime'],
                    );
                    //tarik manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$log['PIN']."' and checkinout='".$log['DateTime']."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }
                echo json_encode(array("status" => TRUE,'user' =>$useratt));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        } else {
            $getConString = $this->fikyattd->connectAttd($set['ipaddress'], $set['port']);
            //new $getConString;
            $zk = $this->fikyattd;
            $this->db->truncate('sc_tmp.mc_checkinout');
            if ($zk->connect()) {
                $attendance = $zk->getAttendance();
                while( list($idx, $attendancedata) = each($attendance) ) {

                    $useratt = array (
                        'userid' => $idx,
                        'badgenumber' => $attendancedata[0],
                        'status' => $attendancedata[1],
                        'checkinout' => date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))
                    );
                    $this->db->insert('sc_tmp.mc_checkinout',$useratt);
                }

                $zk->getTime();
                $zk->enableDevice();
                $zk->disconnect();
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }

    }

    function mc_vAttendance(){
        $data['title'] = 'CHECKINOUT';
        $data['message'] = '';

        $kdcabang = $this->request->getPost('kdcabang');
        $tanggal = $this->request->getPost('tgl');


        if (empty($tanggal) or $tanggal==''){

            $x = date('Y-m-d');
            $param ="and to_char(checktime,'yyyy-mm-dd') = '$x'";
        } else {
            $tgl=explode(' - ',$tanggal);
            $tglawal = $tgl[0];
            $tglakhir = $tgl[1];
            $param = " and kdcabang='$kdcabang' and   (to_char(checktime,'dd-mm-yyyy') between '$tglawal' and '$tglakhir' )";
        }
        $limit = " limit 10000";
        $data['list_kanwil']=$this->m_absenMesin->q_kanwil()->getResult();
        $data['list_absen'] = $this->m_absenMesin->q_show_checkinout($param,$limit)->getResult();
        return $this->template->render('absenmesin/vAttendance',$data);
    }

    function set_mConnection(){

        $data['title']="Setting Koneksi Mesin";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.F.11'; $versirelease='I.M.F.11/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.F.11'";
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

        $param = " ";
        $data['list_set'] = $this->m_absenMesin->q_set_connection($param)->getResult();
        return $this->template->render('absenmesin/vSetConnection',$data);
    }

    function saveFingerprint(){
        $id = $this->request->getPost('id');
        $branch = $this->request->getPost('branch');
        $cabang = $this->request->getPost('cabang');
        $machinename = $this->request->getPost('machinename');
        $ftype = $this->request->getPost('ftype');
        $ipaddress = $this->request->getPost('ipaddress');
        $com_key = $this->request->getPost('com_key');
        $soap_port = $this->request->getPost('soap_port');
        $read_method = $this->request->getPost('read_method');
        $fpassword = $this->request->getPost('fpassword');
        $inputby = $this->session->get('nama');
        $inputdate = date('Y-m-d H:i:s');

        $chold = $this->request->getPost('chold');

        $info = array (
            'branch' => $branch,
            'cabang' => $cabang,
            'machinename' => $machinename,
            'ftype' => $ftype,
            'ipaddress' => $ipaddress,
            'com_key' => $com_key,
            'soap_port' => $soap_port,
            'read_method' => $read_method,
            'fpassword' => $fpassword,
            'inputby' => $inputby,
            'inputdate' => $inputdate,
            'chold' => $chold,

        );

        $builder = $this->db->table('sc_mst.fingerprint');
        $builder->where('id',$id);
        $builder->update($info);
        return redirect()->to(base_url('absenmesin/set_mConnection'));

    }

    function testKoneksiMesin(){
        $id=trim($this->uri->getSegment(3));
        $param = " and id='$id' ";

       $xz = $this->m_absenMesin->q_set_connection($param )->getRowArray();

        if (trim($xz['read_method'])==='RATE_TAD') {
            $options = [
                'ip' => trim($xz['ipaddress']),
                'com_key' => trim($xz['com_key']),
                //'com_key' => 111825,
                'soap_port' => trim($xz['port']),
            ];
            $tad = new TADFactory($options);
            $con = $tad->get_instance();
            if ($con->is_alive()) {
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        } else {
            $this->fikyattd->connectAttd($xz['ipaddress'], $xz['port']);
            $this->fikyattd->connect();
            if ($this->fikyattd->connect()){
                //connection successfull
                $this->fikyattd->disconnect();
                echo json_encode(array("status" => TRUE));
            } else {
                //connection fail
                $this->fikyattd->disconnect();
                echo json_encode(array("status" => FALSE));

            }
        }

    }


    public function tadLibraryTest()
    {
        $options = [
            'ip' => '10.0.15.1',
            'com_key' => 0,
            //'com_key' => 111825,
            'soap_port' => 80
        ];
        $tad = new TADFactory($options);
        $con = $tad->get_instance();
        if ($con->is_alive()) {
            $data = $con->get_att_log()->to_array();
            //$data = $con->get_user_info()->to_array();
            foreach ($data["Row"] as $key => $log) {
                var_dump($log);
                //echo $log['PIN'];
                //echo $log['DateTime'];
                //echo $log['Verified'];
                //echo $log['Status'];
                echo "<br/>";
                echo "<br/>";
            }
        } else {
            echo "can't connect to machine";
        }

    }

    
}
