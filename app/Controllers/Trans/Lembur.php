<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Lembur extends BaseController
{
    function index(){

        $data['title']="List Lembur Karyawan";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.3'; $versirelease='I.T.B.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $this->db->query("insert into sc_his.detail_gaji
            (nik,description,status,gajipokok,tjlembur)
            (select nik,'GP','P',0 as gajipokok,0 as tjlembur from sc_mst.t_karyawan where resign!='YES' and nik not in (select nik from sc_his.detail_gaji));
        ");

        $paramerror=" and userid='$nama' and modul='LEMBUR'";
        $dtlerror=$this->m_lembur->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_lembur->q_trxerror($paramerror)->getNumRows();
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
        $kmenu='I.T.B.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_lembur->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        if ($role_access_filter==='t') {
            $people = array("100061", "5301");
            if (in_array($niksession, $people)) {
                $paramfilter = " and trim(plant)='".trim($dtlkary['plant'])."' and trim(id_gol) in ('SUP','OP')";
            } else {
                $paramfilter = " and trim(statuskepegawaian)='".trim($dtlkary['statuskepegawaian'])."'";
            }


        } else {
            $paramfilter = "";
        }


        $daterange = $this->request->getPost('daterangefilter');
        $plant = $this->request->getPost('plantfilter');
        $id_gol = $this->request->getPost('id_gol_filter');
        $id_dept_filter = $this->request->getPost('id_dept_filter');
        $costcenter_filter = $this->request->getPost('costcenter_filter');
        $daterangefilterResume = $this->request->getPost('daterangefilterResume');

        $tab = $this->request->getPost('tab');
        if (empty($tab) or $tab==='1') {
            $data['tab_active_1']='active';
        } else {
            $data['tab_active_2']='active';
        }

        if (!empty($daterange)) {
            $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
            $date1 = date('Y-m-d',strtotime($tgl1));
            $date2 = date('Y-m-d',strtotime($tgl2));
            $ptgl = " and to_char(tgl_kerja::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        } else {
            $datey = date('Y-m-d');
            $date1 = date('Y-m-d',strtotime($datey . "-5 days"));
            $date2 = date('Y-m-d',strtotime($datey));
            $ptgl = " and to_char(tgl_kerja::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        }
        if (!empty($daterangefilterResume)) {
            $tglresume = explode(' - ',$daterangefilterResume); $tgl1resume = $tglresume[0]; $tgl2resume = $tglresume[1];
            $date1resume = date('Y-m-d',strtotime($tgl1resume));
            $date2resume = date('Y-m-d',strtotime($tgl2resume));
            $ptglresume = " and to_char(a.tgl_kerja::date,'yyyy-mm-dd') between '$date1resume' and '$date2resume'";
        } else {
            $dateyresume = date('Y-m-d');
            $date1resume = date('Y-m-d',strtotime($dateyresume . "-7 days"));
            $date2resume = date('Y-m-d',strtotime($dateyresume . "+30 days"));
            $ptglresume = " and to_char(a.tgl_kerja::date,'yyyy-mm-dd') between '$date1resume' and '$date2resume'";
        }

        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($id_gol)) { $idgol=" and id_gol='$id_gol'"; } else {  $idgol=""; }
        if (!empty($id_dept_filter)) { $dept=" and id_dept='$id_dept_filter'"; } else {  $dept=""; }
        if (!empty($costcenter_filter)) { $costcenter=" and costcenter='$costcenter_filter'"; } else {  $costcenter=""; }



        $parameter = $ptgl.$pplan.$idgol.$dept.$costcenter.$paramfilter;
        ///$parameter = " and to_char(tgl_kerja,'mmYYYY') = '$tgl'";
        $data['list_lembur']=$this->m_lembur->q_lembur($parameter)->getResult();
        $data['tglresume1'] = $date1resume; $data['tglresume2'] = $date2resume;
        $data['list_lembur_resume']=$this->m_lembur->q_lembur_resume('',$ptglresume)->getResult();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_lembur->q_deltrxerror($paramerror);

        return $this->template->render('trans/lembur/v_list',$data);

    }
    function karyawan(){
        //$data['title']="List Master Riwayat Keluarga";

        $data['title']="List Karyawan";
        $userinfo=$this->m_global->q_user_check()->getRowArray();
        $userhr=$this->m_global->list_aksesperdep()->getNumRows();
        $level_akses=strtoupper(trim($userinfo['level_akses']));

        if($userhr>0 or $level_akses=='A'){
            $data['list_karyawan']=$this->m_global->list_karyawan()->getResult();
        } else{
            $data['list_karyawan']=$this->m_global->list_akses_alone()->getResult();
        }
        $data['list_lembur']=$this->m_lembur->list_lembur()->getResult();
        $data['list_trxtype']=$this->m_lembur->list_trxtype()->getResult();
        //$data['list_lk2']=$this->m_lembur->list_karyawan_index($nik)->getRowArray();
        //$data['list_lk']=$this->m_lembur->list_karyawan_index()->getResult();
        return $this->template->render('trans/lembur/v_list_karyawan',$data);
    }


    function proses_input($nik){
        $data['title']="List Karyawan";
        $data['list_lk']=$this->m_lembur->list_karyawan_index($nik)->getResult();
        $data['list_lembur']=$this->m_lembur->list_lembur()->getResult();
        $data['list_trxtype']=$this->m_lembur->list_trxtype()->getResult();
        return $this->template->render('trans/lembur/v_input',$data);
    }


    function add_lembur(){
        //$nik1=explode('|',);
        $lintashari=$this->request->getPost('lintashari');
        $tgllintas=$this->request->getPost('tgllin');

        $nik=$this->request->getPost('nik');
        $nodok=$this->session->get('nama');
        $kddept=$this->request->getPost('department');
        $kdsubdept=$this->request->getPost('subdepartment');
        $kdjabatan=$this->request->getPost('jabatan');
        $kdlvljabatan=$this->request->getPost('kdlvl');
        $atasan=$this->request->getPost('atasan');
        $kdlembur=$this->request->getPost('kdlembur');
        $tgl_kerja1=$this->request->getPost('tgl_kerja');


        if ($tgl_kerja1==''){
            $tgl_kerja=NULL;
        } else {
            $tgl_kerja=$tgl_kerja1;
        }


        /*$durasi1=$this->request->getPost('durasi');
        if ($durasi1==''){
            $durasi=NULL;
        } else {
            $durasi=$durasi1;
        }*/
        $jam_awal=str_replace("_","",$this->request->getPost('jam_awal'));
        $jam_selesai=str_replace("_","",$this->request->getPost('jam_selesai'));
        $durasi_istirahat=str_replace("_","",$this->request->getPost('durasi_istirahat'));
        //$durasi=$jam_selesai-$jam_awal;
        //$tgl_dok=$this->request->getPost('tgl_dok');
        $tgl_dok=$tgl_kerja;
        $kdtrx=$this->request->getPost('kdtrx');
        $keterangan=$this->request->getPost('keterangan');
        $status=$this->request->getPost('status');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $jenis_lembur=$this->request->getPost('jenis_lembur');

        if($lintashari=='t'){
            $jenis_lembur='D2';
            $jam_awal1=trim(date("Y-m-d",strtotime($tgl_kerja)).' '.$jam_awal);
            $jam_selesai1=trim(date("Y-m-d",strtotime($tgllintas)).' '.$jam_selesai);

        } else{
            $jam_awal1=trim(date("Y-m-d",strtotime($tgl_kerja)).' '.$jam_awal);
            $jam_selesai1=trim(date("Y-m-d",strtotime($tgl_kerja)).' '.$jam_selesai);
        }

        //echo $jam_awal1.'<br>';
        //echo $jam_selesai1.'<br>';

        //echo $gaji_bpjs;
        $info=array(
            'nik'=>$nik,
            'nodok'=>strtoupper($nodok),
            'kddept'=>strtoupper($kddept),
            'kdsubdept'=>strtoupper($kdsubdept),
            //'durasi'=>$durasi,
            'durasi_istirahat'=>$durasi_istirahat,
            'kdjabatan'=>$kdjabatan,
            'kdlvljabatan'=>strtoupper($kdlvljabatan),
            'kdjabatan'=>strtoupper($kdjabatan),
            'nmatasan'=>$atasan,
            'tgl_dok'=>$tgl_dok,
            'kdlembur'=>$kdlembur,
            'tgl_kerja'=>$tgl_kerja,
            'tgl_jam_mulai'=>$jam_awal1,
            'tgl_jam_selesai'=>$jam_selesai1,
            'kdtrx'=>strtoupper($kdtrx),
            'jenis_lembur'=>strtoupper($jenis_lembur),
            'keterangan'=>strtoupper($keterangan),
            'status'=>strtoupper($status),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        //echo $durasi;
        $cek=$this->m_lembur->q_cekdouble($nik,$tgl_kerja,$jam_awal1)->getNumRows();
        if ($cek>0) {
            return redirect()->to(base_url("trans/lembur/index/lembur_failed"));
        } else {
            $nama = $this->session->get('nama');
            $this->db->insert('sc_tmp.lembur',$info);
            $dtl_push = $this->m_global->q_lv_mkaryawan(" and nik='$nik'")->getRowArray();
            $paramerror=" and userid='$nama' and modul='LEMBUR'";
            $dtlerror=$this->m_lembur->q_trxerror($paramerror)->getRowArray();
            if ($this->fiky_notification_push->onePushVapeApprovalHrms($nik,trim($dtl_push['nik_atasan']),trim($dtlerror['nomorakhir1']))){
                return redirect()->to(base_url("trans/lembur/index/rep_succes/$nik"));
            }

        }

    }



    function edit_lembur(){
        //$nik1=explode('|',);
        $lintashari=$this->request->getPost('lintashari');
        $tgllintas=$this->request->getPost('tgllin');


        $nik=$this->request->getPost('nik');
        $nodok=$this->request->getPost('nodok');
        $kddept=$this->request->getPost('department');
        $kdsubdept=$this->request->getPost('subdepartment');
        $kdjabatan=$this->request->getPost('jabatan');
        $kdlvljabatan=$this->request->getPost('kdlvl');
        $atasan=$this->request->getPost('atasan');
        $kdlembur=$this->request->getPost('kdlembur');
        $tgl_kerja1=$this->request->getPost('tgl_kerja');
        if ($tgl_kerja1==''){
            $tgl_kerja=NULL;
        } else {
            $tgl_kerja=$tgl_kerja1;
        }
        /*$durasi1=$this->request->getPost('durasi');
        if ($durasi1==''){
            $durasi=NULL;
        } else {
            $durasi=$durasi1;
        }*/
        $jam_awal=str_replace("_","",$this->request->getPost('jam_awal'));
        $jam_selesai=str_replace("_","",$this->request->getPost('jam_selesai'));
        $durasi_istirahat=str_replace("_","",$this->request->getPost('durasi_istirahat'));
        $kdtrx=$this->request->getPost('kdtrx');
        $tgl_dok=$this->request->getPost('tgl_dok');
        $keterangan=$this->request->getPost('keterangan');
        $status=$this->request->getPost('status');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $jenis_lembur=$this->request->getPost('jenis_lembur');
        //$no_urut=$this->request->getPost('no_urut');

        if($lintashari=='t'){
            $jenis_lembur='D2';
            $jam_awal1=trim(date("Y-m-d",strtotime($tgl_kerja)).' '.$jam_awal);
            $jam_selesai1=trim(date("Y-m-d",strtotime($tgllintas)).' '.$jam_selesai);

        } else{
            //$jenis_lembur='D1';
            $jam_awal1=trim(date("Y-m-d",strtotime($tgl_kerja)).' '.$jam_awal);
            $jam_selesai1=trim(date("Y-m-d",strtotime($tgl_kerja)).' '.$jam_selesai);
        }


        $info=array(
            //'nodok'=>strtoupper($nodok),

            'durasi_istirahat'=>$durasi_istirahat,
            'kdlembur'=>$kdlembur,
            'tgl_kerja'=>$tgl_kerja,
            'tgl_jam_mulai'=>$jam_awal1,
            'tgl_jam_selesai'=>$jam_selesai1,
            'kdtrx'=>strtoupper($kdtrx),
            'keterangan'=>strtoupper($keterangan),
            'jenis_lembur'=>strtoupper($jenis_lembur),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        //$this->db->where('custcode',$kode);

        $cekxsdf=$this->m_lembur->q_cekdouble2($nik,$jam_awal1,$jam_selesai1)->getNumRows();


        //if ($cek>0 or $cek2>0) {
        if ($cekxsdf>0) {
            return redirect()->to(base_url("trans/lembur/index/lembur_failed"));
        } else {
            $this->db->where('nodok',$nodok);
            $this->db->update('sc_trx.lembur',$info);
            $this->db->query("update sc_trx.lembur set status='U' where nodok='$nodok'");
            return redirect()->to(base_url("trans/lembur/index/$nodok/upsuccess"));
        }


        //echo $inputby;
    }

    function hps_lembur($nodok){
        $nama = trim($this->session->get('nama'));
        $cek=$this->m_lembur->cek_dokumen3($nodok)->getRowArray();
        $cek2=$this->m_lembur->cek_dokumen2($nodok)->getNumRows();
        //$tgl_closing1=$this->m_lembur->tgl_closing()->getRowArray();
        //$tgl_closing=$tgl_closing1['value1'];
        $tgl_dok=$cek['tgl_dok'];
            $info=array(
                'cancel_date'=>date('Y-m-d H:i:s'),
                'cancel_by'=>$this->session->get('nama'),
                'status'=>'C',
            );

            $builder = $this->db->table('sc_trx.lembur');
            $builder->where('nodok',$nodok);
            $builder->update($info);

            //INSERT TRX ERROR
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','LEMBUR');
            $builder_trxerror->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nodok,
                'nomorakhir2' => '',
                'modul' => 'LEMBUR',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url("trans/lembur"));

    }

    function approval($nik,$nodok){
        $nik=$this->request->getPost('nik');
        $nodok=$this->request->getPost('nodok');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $cek=$this->m_lembur->cek_dokumen($nodok)->getNumRows();
        $cek2=$this->m_lembur->cek_dokumen2($nodok)->getNumRows();
        if ($cek>0) {
            return redirect()->to(base_url("trans/lembur/index/kode_failed"));
        } else if ($cek2>0){
            return redirect()->to(base_url("trans/lembur/index/kode_failed"));
        } else {
            $this->m_lembur->tr_app($nodok,$inputby,$tgl_input);
            return redirect()->to(base_url("trans/lembur/index/app_succes"));
        }
        //echo $cek2;
    }

    function cancel($nik,$nodok){
        $nik=$this->request->getPost('nik');
        $nodok=$this->request->getPost('nodok');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $cek=$this->m_lembur->cek_dokumen($nodok)->getNumRows();
        $cek2=$this->m_lembur->cek_dokumen2($nodok)->getNumRows();
        if ($cek>0) {
            return redirect()->to(base_url("trans/lembur/index/kode_failed"));
        } else if ($cek2>0){
            return redirect()->to(base_url("trans/lembur/index/kode_failed"));
        } else {
            $this->m_lembur->tr_cancel($nodok,$inputby,$tgl_input);
            return redirect()->to(base_url("trans/lembur/index/cancel_succes"));
        }

    }


    //////////////////////////////////////// LEMBUR NEW///////////////////////
    ///
    ///
    function input_new(){
        $data['title']="INPUT NEW LEMBUR KARYAWAN";
        $data['type']="INPUT";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.3'; $versirelease='I.T.B.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.T.B.3'";
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

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('trans/lembur/v_input_new_lembur',$data);

    }

    function edit(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $data['title'] = 'EDIT DATA LEMBUR';
        $data['type']="EDIT";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.3'; $versirelease='I.T.B.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['docno'] =$docno;
        $data['nama'] = $nama;

        return $this->template->render('trans/lembur/v_input_new_lembur',$data);

    }

    function detail(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $data['title'] = 'DETAIL DATA LEMBUR';
        $data['type']="DETAIL";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.3'; $versirelease='I.T.B.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['docno'] =$docno;
        $data['nama'] = $nama;

        return $this->template->render('trans/lembur/v_input_new_lembur',$data);

    }

    function list_karyawan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_param_global_');
        $page = intval($page);
        $limit = $perpage * $page;

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.T.B.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_lembur->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        if ($role_access_filter==='t') {
            $people = array("100061", "5301");
            if (in_array($niksession, $people)) {
                $paramfilter = " and plant='".trim($dtlkary['plant'])."' and id_gol in ('SUP','OP')";
            } else {
                $paramfilter = " and statuskepegawaian='".trim($dtlkary['statuskepegawaian'])."'";
            }


        } else {
            $paramfilter = "";
        }

        $param=" and (nmlengkap like '%$search%' $paramglobal $paramfilter) or ( nik like '%$search%' $paramglobal $paramfilter ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_lembur->q_karyawan($param)->getResult();
        $count = $this->m_lembur->q_karyawan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'paramfilter' => $paramfilter,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function showDetailNik () {
        $nama = $this->session->get('nama');
        $docno = $this->request->getGet('docno');
        $nik = $this->request->getGet('nik');


        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.T.B.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_lembur->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        if ($role_access_filter==='t') {
            $people = array("100061", "5301"); //yeni & selvi
            if (in_array($niksession, $people)) {
                $paramfilter = " and plant='".trim($dtlkary['plant'])."' and id_gol in ('SUP','OP')";
            } else {
                $paramfilter = " and statuskepegawaian='".trim($dtlkary['statuskepegawaian'])."'";
            }


        } else {
            $paramfilter = "";
        }


        $param = " and nik='$nik'".$paramfilter;
        $dtl = $this->m_lembur->q_karyawan($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function showDetailLembur () {
        $nama = $this->session->get('nama');
        $docno = $this->request->getGet('docno');
        $nik = $this->request->getGet('nik');

        $param = " and nodok='$docno'";
        $dtl = $this->m_lembur->q_lembur($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function saveLembur(){
        $nama = trim($this->session->get('nama'));
        $nik = $this->request->getPost('nik');
        $docno = $this->request->getPost('docno');
        $tgl_kerja = date('Y-m-d',strtotime($this->request->getPost('tgl_kerja')));
        $jenis_lembur = $this->request->getPost('jenis_lembur');
        $kdlembur = trim($this->request->getPost('kdlembur'));
        $durasijam = $this->request->getPost('durasijam');
        $id_gol = $this->request->getPost('id_gol');
        $keterangan = strtoupper($this->request->getPost('keterangan'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        $type = $this->request->getPost('type');

        $builder_tmp = $this->db->table('sc_tmp.lembur');
        $builder_trx = $this->db->table('sc_trx.lembur');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');


        if ($type==='INPUT'){
            $pram = " and nik='$nik' and tgl_kerja='$tgl_kerja' and status='P'";
            $pramtmp = " and nik='$nik' and tgl_kerja='$tgl_kerja'";
            $cek1 = $this->m_lembur->q_lembur($pram)->getNumRows();
            $cek2 = $this->m_lembur->q_cek_tmplembur($pramtmp)->getNumRows();
            if ($cek1>0 or $cek2>0) {
                $output = array(
                    'status' => false,
                    'messages' => 'Sudah Tersedia Inputan Dengan Nik & Tanggal Yang Sama/Clear Import Data Lembur',
                );
                echo json_encode($output);
            } else {
                $infox = array(
                    'nodok' => $nama ,
                    'nik' => $nik ,
                    'tgl_dok' => date('Y-m-d'),
                    'tgl_kerja' => $tgl_kerja,
                    'jenis_lembur' => $jenis_lembur,
                    'durasijam' => str_replace(',','.',$durasijam),
                    'kdlembur' => $kdlembur,
                    'status' => 'I',
                    'id_gol' => $id_gol,
                    'keterangan' => $keterangan,
                    'input_by' => $inputby,
                    'input_date' => $inputdate,

                );


                if($builder_tmp->insert($infox)) {
                    $finput = array(
                        'status' => 'F' ,
                    );
                    $builder_tmp->where('nodok',$nama);
                    $builder_tmp->where('nik',$nik);
                    $builder_tmp->update($finput);

                    $paramerror=" and userid='$nama' and modul='LEMBUR'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $output = array(
                        'status' => true,
                        'messages' => trim($dtlerror['description']).'
DOKUMEN: '.$dtlerror['nomorakhir1'],
                    );
                    echo json_encode($output);
                } else {
                    $output = array(
                        'status' => false,
                        'messages' => 'Fail Data No Response',
                    );
                    echo json_encode($output);
                }
            }
        } else if ($type==='EDIT') {

            $infox = array(
                'tgl_kerja' => $tgl_kerja,
                'jenis_lembur' => $jenis_lembur,
                'durasijam' => str_replace(',','.',$durasijam),
                'kdlembur' => $kdlembur,
                'id_gol' => $id_gol,
                'keterangan' => $keterangan,
                'update_by' => $inputby,
                'update_date' => $inputdate,
            );

            $builder_trx->where('nodok',$docno);
            if($builder_trx->update($infox)) {
                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','LEMBUR');
                $builder_trxerror->delete();
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $docno,
                    'nomorakhir2' => '',
                    'modul' => 'LEMBUR',
                );
                $builder_trxerror->insert($infotrxerror);
                // SHOW getResult TRXERROR
                $paramerror=" and userid='$nama' and modul='LEMBUR'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $output = array(
                    'status' => true,
                    'messages' => trim($dtlerror['description']).'
DOKUMEN: '.$dtlerror['nomorakhir1'],
                );
                echo json_encode($output);
            } else {
                $output = array(
                    'status' => false,
                    'messages' => 'Fail Data No Response',
                );
                echo json_encode($output);
            }
        }
    }

    function import_lembur(){

        $data['title']="List Lembur Karyawan";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.27'; $versirelease='I.T.B.27/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='LEMBUR'";
        $dtlerror=$this->m_lembur->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_lembur->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc   $nomorakhir1</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.T.B.27';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and nodok='$nama'";
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_lembur']=$this->m_lembur->q_lembur_tmp($parameter)->getResult();
        $data['adaisi']=$this->m_lembur->q_lembur_tmp($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_lembur->q_deltrxerror($paramerror);;
        return $this->template->render('trans/lembur/v_list_import',$data);

    }

    function proses_xls_lembur() {
        $nama = $this->session->get('nama');
        $this->db->query("delete from sc_tmp.lembur where nodok='$nama'");
        $builder_tmp = $this->db->table('sc_tmp.lembur');
        $builder_trx = $this->db->table('sc_trx.lembur');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $path = "./assets/files/lembur/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        $file = $this->request->getFile('import');
        $fileName = $file->getName();
        if (file_exists($path.$fileName)){
            unlink($path.$fileName);
        }
        $validateFile = $this->validate([
            'import' => [
                'uploaded[import]',
                'ext_in[import,xls,xlsx,excel]',
                'max_size[import,10096]',
            ]
        ]);
        $file->move($path, $fileName);
        $inputFileName = $path.trim($fileName);
        if (!$validateFile){
            echo 'INVALID FAILED FILE';
            echo '<a href="'.base_url('/trans/lembur/import_lembur').'">BACK</a>';
        } else {
            $builder_tmp->where('nodok',$nama);
            $builder_tmp->delete();
            //  Read your Excel workbook
            try {
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }

            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            //  Loop through each row of the worksheet in turn
            $JUDUL = $sheet->rangeToArray('A' . '1' . ':' . $highestColumn . '1',
                NULL,
                TRUE,
                FALSE);
            $JUDUL[0][0].'</br>';
            $no=1;
            for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                //  Insert row data array into your database of choice here
                $data = array(
                    'nodok'=>$nama,
                    'nik'=>trim($rowData[0][0]),
                    'tgl_dok'=>date('Y-m-d'),
                    'tgl_kerja'=>date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][2])))),
                    'kdlembur'=>strtoupper(trim($rowData[0][3])),
                    'durasijam'=>trim($rowData[0][4]),
                    'keterangan'=>trim($rowData[0][5]),
                    'status'=>'I',
                    'input_by'=>$nama,
                    'input_date'=>date("Y-m-d H:i:s")
                );
                $varnik = trim($rowData[0][0]);
                $vartgl_kerja = date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][2]))));
                $parametercek = " and nik='$varnik' and to_char(tgl_kerja,'yyyy-mm-dd')='$vartgl_kerja' and status not in ('C','D')";
                $cek = $this->m_lembur->q_cek_double_upload_lembur($parametercek)->getNumRows();
                if ((!empty($rowData[0][0]) or (trim($rowData[0][0]) !== '')) and $cek == 0) {
                    $builder_tmp->insert($data);
                    /* 3 */
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','LEMBUR');
                    $builder_trxerror->delete();
                    /* error handling */
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => '',
                        'nomorakhir2' => '',
                        'modul' => 'LEMBUR',
                    );
                    $builder_trxerror->insert($infotrxerror);
                } else {
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','LEMBUR');
                    $builder_trxerror->delete();
                    /* error handling */
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 8,
                        'nomorakhir1' => $varnik.'*'.$vartgl_kerja,
                        'nomorakhir2' => '',
                        'modul' => 'LEMBUR',
                    );
                    $builder_trxerror->insert($infotrxerror);
                    return redirect()->to(base_url('trans/lembur/import_lembur'));
                }
                $no++;
            }
            return redirect()->to(base_url('trans/lembur/import_lembur'));
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.lembur where nodok='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','LEMBUR');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'LEMBUR',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('trans/lembur/import_lembur'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.lembur set status='F' where nodok='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','LEMBUR');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'LEMBUR',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('trans/lembur'));
    }

    function downloadExcel(){
        $daterange = $this->request->getPost('daterange');
        $plant = $this->request->getPost('plant');
        $id_gol = $this->request->getPost('id_gol');
        $id_dept_download = $this->request->getPost('id_dept_download');
        $costcenter_download = $this->request->getPost('costcenter_download');



        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($id_gol)) { $idgol=" and id_gol='$id_gol'"; } else {  $idgol=""; }
        if (!empty($id_dept_download)) { $dept=" and id_dept='$id_dept_download'"; } else {  $dept=""; }
        if (!empty($costcenter_download)) { $costcenter=" and costcenter='$costcenter_download'"; } else {  $costcenter=""; }

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.T.B.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_lembur->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        if ($role_access_filter==='t') {
            $people = array("100061", "5301"); //yeni & selvi
            if (in_array($niksession, $people)) {
                $paramfilter = " and plant='".trim($dtlkary['plant'])."' and id_gol in ('SUP','OP')";
            } else {
                $paramfilter = " and statuskepegawaian='".trim($dtlkary['statuskepegawaian'])."'";
            }


        } else {
            $paramfilter = "";
        }

        $parameter = " and status='P' and to_char(tgl_kerja::date,'yyyy-mm-dd') between '$date1' and '$date2'".$pplan.$idgol.$dept.$costcenter.$paramfilter;
        $datane=$this->m_lembur->q_lembur($parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('DOKUMEN','NIK','NAMA LENGKAP','DEPARTMENT','SUB DEPARTMEN/SECTION','COSTCENTER','PLANT','GROUP','STATUS',
            'JENIS','TGL LEMBUR','JAM(MATI)','JAM(HIDUP)','TJLEMBUR','TOTAL','KETERANGAN','INPUTBY'));
        $this->fiky_excel->set_column(array('nodok1','nik1','nmlengkap','nmdept','nmsubdept','nmcostcenter','locaname','nmgroupgol','nmkepegawaian',
            'kdlembur','tgl_kerja2','durasijam','durasihidup','tjlembur','nominal','keterangan','input_by'));
        $this->fiky_excel->set_width(array(16,10,30,30,30,30,7,10,7,
            20,15,10,10,10,10,50,20));
        $this->fiky_excel->exportXlsx('Lembur '.$plant.' '.$id_gol.' '.$tgl1.' sd '.$tgl2);
    }

    function downloadResumeExcel(){
        $daterange = $this->request->getPost('daterangeExcelResume');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        $parameter = " and to_char(a.tgl_kerja::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        $datane=$this->m_lembur->q_lembur_resume('',$parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('DEPARTMENT','JUMLAH DOKUMEN','TOTAL JAM'));
        $this->fiky_excel->set_column(array('nmdept','jml_lembur','ttljam'));
        $this->fiky_excel->set_width(array(30,30,30));
        $this->fiky_excel->exportXlsx('Resume Lembur Department'.$tgl1.' sd '.$tgl2);
    }


    function immgp(){

        $nama=$this->session->get('nama');
        $data['title']="List Gp";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.28'; $versirelease='I.T.B.28/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='LEMBUR'";
        $dtlerror=$this->m_lembur->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_lembur->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc   $nomorakhir1</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.T.B.28';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " ";
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_detail']=$this->m_lembur->q_detail_gaji($parameter)->getResult();
        $data['adaisi']=$this->m_lembur->q_detail_gaji($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_lembur->q_deltrxerror($paramerror);

        if ($role === 'ML') { //hak akses untuk superadmin irga
            return $this->template->render('trans/lembur/v_list_import_gp',$data);
        } else {
            return $this->template->render('trans/lembur/v_not_found',$data);
        }



    }

    function proses_komponen_gp() {
        $nama = $this->session->get('nama');
        $this->db->query("delete from sc_tmp.lembur where nodok='$nama'");
        $path = "./assets/files/gp/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $this->db->query("insert into sc_his.detail_gaji
            (nik,description,status,gajipokok,tjlembur)
            (select nik,'GP','P',0 as gajipokok,0 as tjlembur from sc_mst.t_karyawan where resign!='YES' and nik not in (select nik from sc_his.detail_gaji));
        ");

        if ($this->request->getPost('save')) {
            $file = $this->request->getFile('import');
            $fileName = $file->getName();
            if (file_exists($path . $fileName)) {
                unlink($path . $fileName);
            }
            $validateFile = $this->validate([
                'import' => [
                    'uploaded[import]',
                    'ext_in[import,xls,xlsx,excel]',
                    'max_size[import,10096]',
                ]
            ]);
            $file->move($path, $fileName);
            $inputFileName = $path . trim($fileName);
            if (!$validateFile) {
                echo 'INVALID FAILED FILE';
                echo '<a href="' . base_url('/trans/lembur/immgp') . '">BACK</a>';
            } else {

                //  Read your Excel workbook
                try {
                    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                    $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                } catch (Exception $e) {
                    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                }

                //  Get worksheet dimensions
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                //  Loop through each row of the worksheet in turn
                $JUDUL = $sheet->rangeToArray('A' . '1' . ':' . $highestColumn . '1',
                    NULL,
                    TRUE,
                    FALSE);
                $JUDUL[0][0] . '</br>';
                $no = 1;
                for ($row = 2; $row <= $highestRow; $row++) {                //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                        NULL,
                        TRUE,
                        FALSE);

                    //  Insert row data array into your database of choice here
                    $data = array(
                        'gajipokok' => trim($rowData[0][1]),
                        'updateby' => $nama,
                        'updatedate' => date("Y-m-d H:i:s"),
                        'status' => 'U',
                    );

                    $builder_dtlgaji = $this->db->table("sc_his.detail_gaji");
                    $builder_dtlgaji->where("nik", trim($rowData[0][0]));
                    $builder_dtlgaji->update($data);
                    //trim($rowData[0][1]).'   nik:'.trim($rowData[0][0]);
                    $no++;
                }

                return redirect()->to(base_url('trans/lembur/immgp'));
            }
        }
    }

    function clear_tmp_gp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.lembur where nodok='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','LEMBUR');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'LEMBUR',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('trans/lembur/import_lembur'));
    }

    function final_data_gp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.lembur set status='F' where nodok='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','LEMBUR');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'LEMBUR',
        );
        $builder_trxerror->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/lembur'));
    }

    function recalculate(){
        $daterange = $this->request->getPost('daterangerecalculate');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));
        $parameter = " and status='P' and to_char(tgl_kerja::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        $this->db->query("update sc_trx.lembur set status='U' where nodok is not null $parameter");

        return redirect()->to(base_url('trans/lembur'));

    }

}
