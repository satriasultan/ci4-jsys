<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Absensi extends BaseController
{
    function filter(){
        $data['title']='Tarikan Data Mesin Absensi'; //aksesconvert absensi
        $kmenu='I.T.B.6';
        if($this->uri->getSegment(3)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(3)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_kanwil']=$this->m_absensi->q_kanwil()->getResult();
        return $this->template->render('trans/absensi/v_filterabsensi',$data);
    }

    function ajax_tglakhir_ci($kdcabang){
        $data = $this->m_absensi->tglakhir_ci($kdcabang)->getRowArray();
        echo json_encode($data);
    }

    function filter_lembur(){
        $data['title']='Filter Data Lembur';
        return $this->template->render('trans/absensi/v_filterlembur',$data);
    }

    function filter_koreksi(){
        if($this->uri->getSegment(3)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
        } elseif ($this->uri->getSegment(3)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }

        $kmenu='I.T.B.7'; //aksesupdate koreksi absensi
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['title']='Filter Koreksi Data Absensi';
        $data['list_karyawan']=$this->m_absensi->q_karyawan()->getResult();
        $data['list_regu']=$this->m_absensi->q_regu()->getResult();
        $data['list_dept']=$this->m_absensi->q_department()->getResult();

        return $this->template->render('trans/absensi/v_filterkoreksi',$data);
    }

    function filter_detail(){

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.9'; $versirelease='I.T.B.9/RELEASE.401'; $releasedate=date('2021-04-12 00:00:00');
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

        if($count_err>0 and $errordesc<>'') {
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        } else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }

        return $this->template->render('trans/absensi/v_filterdtlabsensi',$data);
    }

    function filter_input(){
        $data['title']='DETAIL SIMPAN TRANSREADY';
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.10'; $versirelease='I.T.B.10/RELEASE.401'; $releasedate=date('2021-04-12 00:00:00');
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

        } else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        $data['akses']=$this->m_global->list_aksespermenu($nama,$kodemenu)->getRowArray();
        $data['list_karyawan']=$this->m_absensi->q_karyawan()->getResult();
        $data['list_kanwil']=$this->m_absensi->q_mstgudang("")->getResult();
        return $this->template->render('trans/absensi/v_filtertarik',$data);

    }

    function ajax_tglakhir_tr($kdcabang){
        $data = $this->m_absensi->tglakhir_tr($kdcabang)->getRowArray();
        echo json_encode($data);
    }

    public function index()
    {
        $tanggal=$this->request->getPost('tgl');
        $kdcabang=$this->request->getPost('kdcabang');
        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';

        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        if (empty($tanggal))
        {
            return redirect()->to(base_url('trans/absensi/filter'));
        }
        $data['title']="DATA MESIN ABSEN $tgla hingga $tglb Untuk Wilayah $kdcabang";
        $data['tgl1']=$tgla;
        $data['tgl2']=$tglb;
        $data['kdcabang']=$kdcabang;
        $data['message']='';
        if($kdcabang=='MJKCNI')
        {
            $data['ttldata']=$this->m_absensi->ttldata_cni($tgl1,$tgl2)->getRowArray();
            $data['list_absen']=$this->m_absensi->show_user_cni($tgl1,$tgl2)->getResult();
        } else 	if($kdcabang=='SBYCNI')
        {
            $data['ttldata']=$this->m_absensi->ttldata_cnisby($tgl1,$tgl2)->getRowArray();
            $data['list_absen']=$this->m_absensi->show_user_cnisby($tgl1,$tgl2)->getResult();
        } else { return redirect()->to(base_url('trans/absensi/filter')); }

        return $this->template->render('trans/absensi/v_absensi',$data);
    }

    public function lembur()
    {
        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';
        //echo date('m-d-Y H:i:s',strtotime($tgl1)).'<br>';
        //echo date('m-d-Y H:i:s',strtotime($tgl2));
        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter_lembur'));
        }

        $data['ttldata']=$this->m_absensi->ttldata_lembur($tgl1,$tgl2)->getRowArray();
        $data['title']="DATA MESIN ABSEN $tgla hingga $tglb";
        $data['tgl1']=$tgla;
        $data['tgl2']=$tglb;
        $data['message']='';
        $data['list_lembur']=$this->m_absensi->show_user_lembur($tgl1,$tgl2)->getResult();
        return $this->template->render('trans/absensi/v_lembur',$data);
    }

    public function absen_mesin()
    {
        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=$tgl[0].' 00:00:00';
        $tgl2=$tgl[1].' 23:59:59';

        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter'));
        }
        $data['ttldata']=$this->m_absensi->ttldata($tgl1,$tgl2)->getRowArray();
        $data['title']="DATA MESIN ABSEN $tgl1 hingga $tgl2";
        $data['tgl1']=$tgl1;
        $data['tgl2']=$tgl2;
        $data['message']='';
        $data['list_absen']=$this->m_absensi->show_user($tgl1,$tgl2)->getResult();
        return $this->template->render('trans/absensi/v_absensi',$data);
    }



    function tarik_data(){
        $tgla=$this->request->getPost('tgl1');
        $tglb=$this->request->getPost('tgl2');
        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        $tglawal=date('Y-m-d',strtotime($tgla));
        $tglakhir=date('Y-m-d',strtotime($tglb));
        $kdcabang=$this->request->getPost('kdcabang');

        if($kdcabang=='MJKCNI'){
            $datane=$this->m_absensi->show_user_cni($tgl1,$tgl2)->getResult();
        } else if($kdcabang=='SBYCNI'){
            $datane=$this->m_absensi->show_user_cnisby($tgl1,$tgl2)->getResult();
        } else { return redirect()->to(base_url('trans/absensi/filter')); }



        foreach ($datane as $dta){
            $badgenumber=($dta->Badgenumber);
            $userid=($dta->USERID);
            $checktime=($dta->CHECKTIME);
            $this->db->query("delete from sc_tmp.checkinout where userid='$userid' and badgenumber='$badgenumber' and checktime='$checktime'");
            $cek_fingerid=$this->m_absensi->q_cek_fingerid($badgenumber)->getNumRows();
            if($cek_fingerid>0){
                $info=array(
                    'userid'=>$dta->USERID,
                    'badgenumber'=>$dta->Badgenumber,
                    'nama'=>$dta->Name,
                    'checktime'=>$dta->CHECKTIME,
                    'inputan'=>'M',
                    'inputby'=>$this->session->get('nik')
                );
                $this->m_absensi->simpan($info);
            }


        }

        //$txt='select sc_tmp.update_badgenumber('.chr(39).$tglawal.chr(39).','.chr(39).$tglakhir.chr(39).')';
        //$this->db->query($txt);
        echo json_encode(array("status" => TRUE));
    }

    function tarik_data_lembur(){
        $tgla=$this->request->getPost('tgl1');
        $tglb=$this->request->getPost('tgl2');
        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        //$tgl1='17-01-2016';
        //$tgl2='18-01-2016';
        $datane=$this->m_absensi->show_user_lembur($tgl1,$tgl2)->getResult();

        foreach ($datane as $dta){
            $info=array(
                'userid'=>$dta->USERID,
                'badgenumber'=>$dta->Badgenumber,
                'nama'=>$dta->Name,
                'checktime'=>$dta->CHECKTIME,
                'inputan'=>'M',
                'inputby'=>$this->session->get('nik')
            );
            $this->m_absensi->simpan_lembur($info);
        }
        echo json_encode(array("status" => TRUE));
    }

    function input_data(){
        $tanggal=$this->request->getPost('tgl');
        $kdcabang=$this->request->getPost('kdcabang');
        $nama = trim($this->session->get('nama'));
        $tgl=explode(' - ',$tanggal);
        $tglawal1=$tgl[0];
        $tglakhir1=$tgl[1];
        $tglawal=date('Y-m-d',strtotime($tglawal1));
        $tglakhir=date('Y-m-d',strtotime($tglakhir1));

        //memasukkan absensi dari finger ke checkinout
        $this->db->query("select sc_tmp.pr_mc_checkinout('$tglawal', '$tglakhir')");
        //tarik transready
        $this->db->query("delete from sc_trx.transready where tgl between '$tglawal' and '$tglakhir' and trim(nik) in (select trim(nik) from sc_mst.t_karyawan where plant='$kdcabang')");
        $txt='select sc_tmp.pr_generate_transready('.chr(39).$tglawal.chr(39).','.chr(39).$tglakhir.chr(39).','.chr(39).$kdcabang.chr(39).')';
        $this->db->query($txt);

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','LEMBUR');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $kdcabang.' s/d '.$tglakhir,
            'nomorakhir2' => '',
            'modul' => 'LEMBUR',
        );
        $builder_trxerror->insert($infotrxerror);
        // SHOW getResult TRXERROR

        return redirect()->to(base_url('trans/absensi/filter_input'));
    }

    function input_data_new(){
        $tanggal=$this->request->getPost('tgl');

        $tgl=explode(' - ',$tanggal);
        $tglawal1=$tgl[0];
        $tglakhir1=$tgl[1];
        $tglawal=date('Y-m-d',strtotime($tglawal1));
        $tglakhir=date('Y-m-d',strtotime($tglakhir1));
        //echo $tglawal.'|'.$tglakhir;

        //$tglb=$this->request->getPost('tgl2');
        //$tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        //$tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        //$tgl1='17-01-2016';
        //$tgl2='18-01-2016';
        $datane=$this->m_absensi->q_loopingjadwal_new($tglawal,$tglakhir)->getResult();
        //$datane2=$this->m_absensi->q_showjadwal($tgl)->getResult();

        foreach ($datane as $dta){

            $nik=$dta->nik;
            $tgl1=$dta->tgl_min_masuk;
            $tgl2=$dta->tgl_max_pulang;
            $cari_absen=$this->m_absensi->q_caricheckinout($nik,$tgl1,$tgl2)->getRowArray();
            if (empty($cari_absen['tgl_min']) and empty($cari_absen['tgl_max'])) {
                $jam_min=NULL;
                $jam_max=NULL;
            } else {
                $jam_min=$cari_absen['tgl_min'];
                $jam_max=$cari_absen['tgl_max'];
            }
            $info_absen=array(
                //'userid'=>$dta->userid,
                //'badgenumber'=>$dta->badgenumber,
                //'checktime'=>$dta->checktime,
                'nik'=>$dta->nik,
                'kdjamkerja'=>$dta->kdjamkerja,
                'tgl'=>$dta->tgl,
                //'kdregu'=>$dta->kdregu,
                'shiftke'=>$dta->shiftke,
                //'shift'=>$dta->shift,
                'jam_masuk'=>$dta->jam_masuk,
                'jam_masuk_min'=>$dta->jam_masuk_min,
                //'jam_masuk_max'=>$dta->jam_masuk_max,
                'jam_pulang'=>$dta->jam_pulang,
                //'jam_pulang_min'=>$dta->jam_pulang_min,
                'jam_pulang_min'=>$dta->jam_pulang_min,
                'jam_pulang_max'=>$dta->jam_pulang_max,
                'kdhari_masuk'=>$dta->kdharimasuk,
                'kdhari_pulang'=>$dta->kdharipulang,
                'jam_masuk_absen'=>$jam_min,
                'jam_pulang_absen'=>$jam_max,
            );

            /*
            echo $nik.'|';
            echo $jam_min.'|';
            echo $jam_max.'|';
            echo $tgl1.'|';
            echo $tgl2.'<br>';
            */
            $builder_transready = $this->db->table('sc_trx.transready');
            $builder_transready->insert('sc_trx.transready',$info_absen);


            //echo $status;
            //$this->db->insert('sc_trx.transready',$info_absen);
            //$this->db->insert('sc_trx.transready',$status);

        }
        //echo 'sukses';
        return redirect()->to(base_url('trans/absensi/filter_input/rep_succes'));
    }




    function koreksiabsensi(){

        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=$tgl[0];
        $tgl2=$tgl[1];

        //$nama=trim($this->request->getPost('karyawan'));
        $nik=trim($this->request->getPost('karyawan'));
        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter_koreksi'));
        } else {
            /*$data['title']="KOREKSI DATA ABSEN KARYAWAN";
            //echo $nama;
            //$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->getResult();

            $data['list_absen']=$this->m_absensi->q_transready_koreksi($nik,$tgl1,$tgl2)->getResult();
            $data['list_jam']=$this->m_absensi->q_jamkerja()->getResult();
            $data['nik']=$nik;
            return $this->template->render('trans/absensi/v_koreksiabsensi',$data);*/
            $this->lihat_koreksi_kar($nik,$tgl1,$tgl2);
        }

    }

    function koreksiabsensi_dept(){

        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=$tgl[0];
        $tgl2=$tgl[1];

        //$nama=trim($this->request->getPost('karyawan'));
        $kddept=trim($this->request->getPost('kddept'));
        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter_koreksi'));
        } else {
            /*$data['title']="KOREKSI DATA ABSEN KARYAWAN";
            //echo $nama;
            //$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->getResult();

            $data['list_absen']=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2)->getResult();
            $data['list_jam']=$this->m_absensi->q_jamkerja()->getResult();
            $data['kddept']=$kddept;
            return $this->template->render('trans/absensi/v_koreksiabsensi_dept',$data);*/
            $this->lihat_koreksi($kddept,$tgl1,$tgl2);
        }

    }

    function lihat_koreksi($kddept,$tgl1,$tgl2){
        $data['title']="KOREKSI DATA ABSEN KARYAWAN";
        $data['list_absen']=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2)->getResult();
        $data['list_jam']=$this->m_absensi->q_jamkerja()->getResult();
        $data['kddept']=$kddept;
        $data['tgl1']=$tgl1;
        $data['tgl2']=$tgl2;
        return $this->template->render('trans/absensi/v_koreksiabsensi_dept',$data);

    }

    function lihat_koreksi_kar($nik,$tgl1,$tgl2){

        $data['title']="KOREKSI DATA ABSEN KARYAWAN";
        //echo $nama;
        //$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->getResult();

        $data['list_absen']=$this->m_absensi->q_transready_koreksi($nik,$tgl1,$tgl2)->getResult();
        $data['list_jam']=$this->m_absensi->q_jamkerja()->getResult();
        $data['nik']=$nik;
        $data['tgl1']=$tgl1;
        $data['tgl2']=$tgl2;
        return $this->template->render('trans/absensi/v_koreksiabsensi',$data);

    }

    function show_edit($id,$kddept,$tgl1,$tgl2){

        $data['title']="KOREKSI DATA ABSEN KARYAWAN";
        //echo $nama;
        //$data['list_absen']=$this->m_absensi->q_absensi_kor($tgl1,$tgl2,$nik)->getResult();
        $data['kddept']=$kddept;
        $data['tgl1']=$tgl1;
        $data['tgl2']=$tgl2;
        $data['ld']=$this->m_absensi->q_show_edit($id)->getRowArray();
        $data['list_jam']=$this->m_absensi->q_jamkerja()->getResult();
        return $this->template->render('trans/absensi/v_editkoresiabsensi_dept',$data);

    }


    function input_absensi(){
        $nik=$this->request->getPost('nik');
        $tgl=$this->request->getPost('tanggal1');
        $kdjamkerja=$this->request->getPost('kdjamkerja');
        //$editby=$this->request->getPost('editby');
        $jam_masuk=str_replace("_",0,$this->request->getPost('jam_masuk'));
        $jam_pulang=str_replace("_",0,$this->request->getPost('jam_pulang'));
        //$checktime=$tgl.' '.$jam;
        $info_absen=array(
            'nik'=>$nik,
            'tgl'=>$tgl,
            'kdjamkerja'=>$kdjamkerja,
            'jam_masuk_absen'=>$jam_masuk,
            'jam_pulang_absen'=>$jam_pulang,
            //'input_by'=>$this->session->get('nik'),
            //'input_date'=>date('Y-m-d H:i:s'),
            'editan'=>'T',
        );
        $cek=$this->m_absensi->cek_absenexist($nik,$tgl,$kdjamkerja)->getNumRows();
        if ($cek>0){
            return redirect()->to(base_url('trans/absensi/filter_koreksi/exist'));
        } else {

            $builder_transready = $this->db->table('sc_trx.transready');
            $builder_transready->insert($info_absen);
            return redirect()->to(base_url('trans/absensi/filter_koreksi/rep_succes'));
        }



    }


    function detailabsensi(){
        $tanggal=$this->request->getPost('tgl');

        $tgl=explode(' - ',$tanggal);
        $ketsts1=$this->request->getPost('ketsts');
        if (!empty($ketsts1)) {
            $ketsts="and x.docref='$ketsts1'";
        } else {
            $ketsts='';
        }
        $tglawal=date('Y-m-d',strtotime($tgl[0]));
        $tglakhir=date('Y-m-d',strtotime($tgl[1]));
        $kanwil=trim($this->request->getPost('kanwil'));

        if (empty($tanggal) or empty($kanwil)){
            return redirect()->to(base_url('trans/absensi/filter_detail'));
        } else {
            $data['title']="DETAIL DATA ABSEN PER WILAYAH";
            $data['list_absen']=$this->m_absensi->q_transready($kanwil,$tglawal,$tglakhir,$ketsts)->getResult();
            $data['list_kary']=$this->m_absensi->q_karyawan()->getResult();
            return $this->template->render('trans/absensi/v_dtlabsensi',$data);
        }

    }

    function detailabsensi_regu(){
        $tanggal=$this->request->getPost('tgl');

        $tgl=explode(' - ',$tanggal);
        $ketsts1=$this->request->getPost('ketsts');
        if (!empty($ketsts1)) {
            $ketsts="and x.docref='$ketsts1'";
        } else {
            $ketsts='';
        }
        $tglawal=date('Y-m-d',strtotime($tgl[0]));
        $tglakhir=date('Y-m-d',strtotime($tgl[1]));
        $kdregu=trim($this->request->getPost('kdregu'));
        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter_detail'));
        } else {
            $data['title']="DETAIL DATA ABSEN KARYAWAN";
            //echo $nama;
            $data['list_absen']=$this->m_absensi->q_transready_regu($kdregu,$tglawal,$tglakhir,$ketsts)->getResult();

            return $this->template->render('trans/absensi/v_dtlabsensi',$data);
        }
        //echo $ketsts1;

    }

    function detailabsensi_dept(){
        $tanggal=$this->request->getPost('tgl');

        $tgl=explode(' - ',$tanggal);
        $ketsts1=$this->request->getPost('ketsts');
        if (!empty($ketsts1)) {
            $ketsts="and x.docref='$ketsts1'";
        } else {
            $ketsts='';
        }

        $tglawal=date('Y-m-d',strtotime($tgl[0]));
        $tglakhir=date('Y-m-d',strtotime($tgl[1]));
        $kddept=trim($this->request->getPost('kddept'));
        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter_detail'));
        } else {
            $data['title']="DETAIL DATA ABSEN KARYAWAN";
            //echo $nama;
            $data['list_absen']=$this->m_absensi->q_transready_dept($kddept,$tglawal,$tglakhir,$ketsts)->getResult();

            return $this->template->render('trans/absensi/v_dtlabsensi',$data);
        }
        //echo $ketsts1;

    }

    function edit_absensi_old(){
        $id=$this->request->getPost('id');
        $tgl=$this->request->getPost('tanggal');
        //$editby=$this->request->getPost('editby');
        $jam_masuk=str_replace("_",0,$this->request->getPost('jam_masuk'));
        $jam_pulang=str_replace("_",0,$this->request->getPost('jam_pulang'));
        $checktime=$tgl.' '.$jam;
        $this->db->query("update sc_tmp.checkinout set checktime='$checktime',editan='T' where id='$id'");
        return redirect()->to(base_url('trans/absensi/filter_koreksi/add_succes'));


    }

    function edit_absensi(){
        $id=$this->request->getPost('id');
        $tgl=$this->request->getPost('tanggal');
        $nik=$this->request->getPost('nik');
        $tgl1=$this->request->getPost('tgl1');
        $tgl2=$this->request->getPost('tgl2');
        //$editby=$this->request->getPost('editby');
        $jam_masuk=str_replace("_",0,$this->request->getPost('jam_masuk'));
        $jam_pulang=str_replace("_",0,$this->request->getPost('jam_pulang'));
        $checktime=$tgl.' '.$jam;
        $this->db->query("update sc_trx.transready set jam_masuk_absen='$jam_masuk',jam_pulang_absen='$jam_pulang',editan='T' where id='$id'");
        //return redirect()->to(base_url('trans/absensi/filter_koreksi/rep_succes');
        return redirect()->to(base_url("trans/absensi/lihat_koreksi_kar/$nik/$tgl1/$tgl2"));


    }

    function edit_absensi_dept(){
        $id=$this->request->getPost('id');
        $tgl=$this->request->getPost('tanggal');
        $kddept=$this->request->getPost('kddept');
        $tgl1=$this->request->getPost('tgl1');
        $tgl2=$this->request->getPost('tgl2');
        //$editby=$this->request->getPost('editby');
        $jam_masuk=str_replace("_",0,$this->request->getPost('jam_masuk'));
        $jam_pulang=str_replace("_",0,$this->request->getPost('jam_pulang'));
        $checktime=$tgl.' '.$jam;
        $this->db->query("update sc_trx.transready set jam_masuk_absen='$jam_masuk',jam_pulang_absen='$jam_pulang',editan='T' where id='$id'");
        return redirect()->to(base_url("trans/absensi/lihat_koreksi/$kddept/$tgl1/$tgl2"));


    }

    function hapus_absensi($id){
        $builder = $this->db->table('sc_tmp.checkinout');
        $builder->where('id',$id);
        $builder->delete();
        return redirect()->to(base_url('trans/absensi/filter_koreksi/rep_succes'));

    }

    public function ajax_edit($id)
    {
        $data = $this->m_absensi->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $data = array(
            'kdkepegawaian' => strtoupper($this->request->getPost('kdkepegawaian')),
            'nmkepegawaian' => strtoupper($this->request->getPost('nmkepegawaian')),
            'input_date' => date('d-m-Y H:i:s'),
            'input_by' => $this->session->get('nik'),
        );
        $insert = $this->m_absensi->save($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $data = array(
            'kdkepegawaian' => strtoupper($this->request->getPost('kdkepegawaian')),
            'nmkepegawaian' => strtoupper($this->request->getPost('nmkepegawaian')),
            'update_date' => date('d-m-Y H:i:s'),
            'update_by' => $this->session->get('nik'),
        );
        $this->m_absensi->update(array('kdkepegawaian' => $this->request->getPost('kdkepegawaian')), $data);
        echo json_encode(array("status" => TRUE));

        $data['message']='Update succes';
    }

    public function ajax_delete($id)
    {
        $this->m_absensi->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    function show_mdb(){
        $tgl1='2015-04-01';
        $tgl2='2015-04-30';
        $cek=$this->m_absensi->show_user_lembur($tgl1,$tgl2)->getResult();
        foreach ($cek as $ck){

            echo $ck->USERID.$ck->CHECKTIME;

        }

    }



    function delete_mdb(){
        $tgl1='2015-04-01';
        $tgl2='2015-04-30';
        $cek=$this->m_absensi->del_user_lembur($tgl1,$tgl2)->getResult();
        foreach ($cek as $ck){

            echo $ck->USERID.$ck->CHECKTIME;

        }

    }

    /* excel report absensi*/
    function pr_report_absensi(){
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $nama=trim($this->session->get('nama'));
        $thnbln=$thn.$bln;

        $paramerror=" and nomorakhir1='$thnbln' and userid='$nama' and modul='PR_ABSEN_R' and errorcode='0'";
        $rowtrx=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

        if ($rowtrx>=1) {
            echo json_encode(array("status" => TRUE));

        } else {
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','PR_ABSEN_R');
            $builder_trxerror->delete();

            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $thn.$bln,
                'nomorakhir2' => '',
                'modul' => 'PR_ABSEN_R',
            );
            $builder_trxerror->insert($infotrxerror);

            $this->m_absensi->q_prreportabsensi($bln,$thn,$nama);
            echo json_encode(array("status" => TRUE));

        }

    }

    function report_absensi($bln,$thn){
        $nama = trim($this->session->get('nama'));
        $tglskr= date("Ym");
        $tglinp=$thn.$bln;
        //$info=$this->m_absensi->excel_reportabsensi($bln,$thn);
        $this->m_absensi->q_prreportabsensi($bln,$thn,$nama);

        $this->fiky_excel->mergeCells('A1:F1');
        $this->fiky_excel->setCellValue ( "A1", "ABSENSI KARYAWAN PERIODE ".$tglinp );
        $this->fiky_excel->setCellValue ( "A2", "NIK" );
        $this->fiky_excel->setCellValue ( "B2", "NAMA LENGKAP" );
        $this->fiky_excel->setCellValue ( "C2", "DEPARTEMENT" );
        $this->fiky_excel->setCellValue ( "D2", "SECTION" );
        $this->fiky_excel->setCellValue ( "E2", "POSITION" );
        $this->fiky_excel->setCellValue ( "F2", "REGU" );
        //TGL 1
        $this->fiky_excel->mergeCells('G1:I1');
        $this->fiky_excel->setCellValue ( "G1", "TGL 1");
        $this->fiky_excel->setCellValue ( "G2", "IN" );
        $this->fiky_excel->setCellValue ( "H2", "OUT" );
        $this->fiky_excel->setCellValue ( "I2", "KET" );
        //TGL 2
        $this->fiky_excel->mergeCells('J1:L1');
        $this->fiky_excel->setCellValue ( "J1", "TGL 2");
        $this->fiky_excel->setCellValue ( "J2", "IN" );
        $this->fiky_excel->setCellValue ( "K2", "OUT" );
        $this->fiky_excel->setCellValue ( "L2", "KET" );
        //TGL 3
        $this->fiky_excel->mergeCells('M1:O1');
        $this->fiky_excel->setCellValue ( "M1", "TGL 3");
        $this->fiky_excel->setCellValue ( "M2", "IN" );
        $this->fiky_excel->setCellValue ( "N2", "OUT" );
        $this->fiky_excel->setCellValue ( "O2", "KET" );
        //TGL 4
        $this->fiky_excel->mergeCells('P1:R1');
        $this->fiky_excel->setCellValue ( "P1", "TGL 4");
        $this->fiky_excel->setCellValue ( "P2", "IN" );
        $this->fiky_excel->setCellValue ( "Q2", "OUT" );
        $this->fiky_excel->setCellValue ( "R2", "KET" );
        //TGL 5
        $this->fiky_excel->mergeCells('S1:U1');
        $this->fiky_excel->setCellValue ( "S1", "TGL 5");
        $this->fiky_excel->setCellValue ( "S2", "IN" );
        $this->fiky_excel->setCellValue ( "T2", "OUT" );
        $this->fiky_excel->setCellValue ( "U2", "KET" );
        //TGL 6
        $this->fiky_excel->mergeCells('V1:X1');
        $this->fiky_excel->setCellValue ( "V1", "TGL 6");
        $this->fiky_excel->setCellValue ( "V2", "IN" );
        $this->fiky_excel->setCellValue ( "W2", "OUT" );
        $this->fiky_excel->setCellValue ( "X2", "KET" );
        //TGL 6
        $this->fiky_excel->mergeCells('V1:X1');
        $this->fiky_excel->setCellValue ( "V1", "TGL 6");
        $this->fiky_excel->setCellValue ( "V2", "IN" );
        $this->fiky_excel->setCellValue ( "W2", "OUT" );
        $this->fiky_excel->setCellValue ( "X2", "KET" );
        //TGL 7
        $this->fiky_excel->mergeCells('Y1:AA1');
        $this->fiky_excel->setCellValue ( "Y1", "TGL 7");
        $this->fiky_excel->setCellValue ( "Y2", "IN" );
        $this->fiky_excel->setCellValue ( "Z2", "OUT" );
        $this->fiky_excel->setCellValue ( "AA2", "KET" );
        //TGL 8
        $this->fiky_excel->mergeCells('AB1:AD1');
        $this->fiky_excel->setCellValue ( "AB1", "TGL 8");
        $this->fiky_excel->setCellValue ( "AB2", "IN" );
        $this->fiky_excel->setCellValue ( "AC2", "OUT" );
        $this->fiky_excel->setCellValue ( "AD2", "KET" );
        //TGL 9
        $this->fiky_excel->mergeCells('AE1:AG1');
        $this->fiky_excel->setCellValue ( "AE1", "TGL 9");
        $this->fiky_excel->setCellValue ( "AE2", "IN" );
        $this->fiky_excel->setCellValue ( "AF2", "OUT" );
        $this->fiky_excel->setCellValue ( "AG2", "KET" );
        //TGL 10
        $this->fiky_excel->mergeCells('AH1:AJ1');
        $this->fiky_excel->setCellValue ( "AH1", "TGL 10");
        $this->fiky_excel->setCellValue ( "AH2", "IN" );
        $this->fiky_excel->setCellValue ( "AI2", "OUT" );
        $this->fiky_excel->setCellValue ( "AJ2", "KET" );
        //TGL 11
        $this->fiky_excel->mergeCells('AK1:AM1');
        $this->fiky_excel->setCellValue ( "AK1", "TGL 11");
        $this->fiky_excel->setCellValue ( "AK2", "IN" );
        $this->fiky_excel->setCellValue ( "AL2", "OUT" );
        $this->fiky_excel->setCellValue ( "AM2", "KET" );
        //TGL 12
        $this->fiky_excel->mergeCells('AN1:AP1');
        $this->fiky_excel->setCellValue ( "AN1", "TGL 12");
        $this->fiky_excel->setCellValue ( "AN2", "IN" );
        $this->fiky_excel->setCellValue ( "AO2", "OUT" );
        $this->fiky_excel->setCellValue ( "AP2", "KET" );
        //TGL 13
        $this->fiky_excel->mergeCells('AQ1:AS1');
        $this->fiky_excel->setCellValue ( "AQ1", "TGL 13");
        $this->fiky_excel->setCellValue ( "AQ2", "IN" );
        $this->fiky_excel->setCellValue ( "AR2", "OUT" );
        $this->fiky_excel->setCellValue ( "AS2", "KET" );
        //TGL 14
        $this->fiky_excel->mergeCells('AT1:AV1');
        $this->fiky_excel->setCellValue ( "AT1", "TGL 14");
        $this->fiky_excel->setCellValue ( "AT2", "IN" );
        $this->fiky_excel->setCellValue ( "AU2", "OUT" );
        $this->fiky_excel->setCellValue ( "AV2", "KET" );
        //TGL 15
        $this->fiky_excel->mergeCells('AW1:AY1');
        $this->fiky_excel->setCellValue ( "AW1", "TGL 15");
        $this->fiky_excel->setCellValue ( "AW2", "IN" );
        $this->fiky_excel->setCellValue ( "AX2", "OUT" );
        $this->fiky_excel->setCellValue ( "AY2", "KET" );
        //TGL 16
        $this->fiky_excel->mergeCells('AZ1:BB1');
        $this->fiky_excel->setCellValue ( "AZ1", "TGL 16");
        $this->fiky_excel->setCellValue ( "AZ2", "IN" );
        $this->fiky_excel->setCellValue ( "BA2", "OUT" );
        $this->fiky_excel->setCellValue ( "BB2", "KET" );
        //TGL 17
        $this->fiky_excel->mergeCells('BC1:BE1');
        $this->fiky_excel->setCellValue ( "BC1", "TGL 17");
        $this->fiky_excel->setCellValue ( "BC2", "IN" );
        $this->fiky_excel->setCellValue ( "BD2", "OUT" );
        $this->fiky_excel->setCellValue ( "BE2", "KET" );
        //TGL 18
        $this->fiky_excel->mergeCells('BF1:BH1');
        $this->fiky_excel->setCellValue ( "BF1", "TGL 18");
        $this->fiky_excel->setCellValue ( "BF2", "IN" );
        $this->fiky_excel->setCellValue ( "BG2", "OUT" );
        $this->fiky_excel->setCellValue ( "BH2", "KET" );
        //TGL 19
        $this->fiky_excel->mergeCells('BI1:BK1');
        $this->fiky_excel->setCellValue ( "BI1", "TGL 19");
        $this->fiky_excel->setCellValue ( "BI2", "IN" );
        $this->fiky_excel->setCellValue ( "BJ2", "OUT" );
        $this->fiky_excel->setCellValue ( "BK2", "KET" );
        //TGL 20
        $this->fiky_excel->mergeCells('BL1:BN1');
        $this->fiky_excel->setCellValue ( "BL1", "TGL 20");
        $this->fiky_excel->setCellValue ( "BL2", "IN" );
        $this->fiky_excel->setCellValue ( "BM2", "OUT" );
        $this->fiky_excel->setCellValue ( "BN2", "KET" );
        //TGL 21
        $this->fiky_excel->mergeCells('BO1:BQ1');
        $this->fiky_excel->setCellValue ( "BO1", "TGL 21");
        $this->fiky_excel->setCellValue ( "BO2", "IN" );
        $this->fiky_excel->setCellValue ( "BP2", "OUT" );
        $this->fiky_excel->setCellValue ( "BQ2", "KET" );
        //TGL 22
        $this->fiky_excel->mergeCells('BR1:BT1');
        $this->fiky_excel->setCellValue ( "BR1", "TGL 22");
        $this->fiky_excel->setCellValue ( "BR2", "IN" );
        $this->fiky_excel->setCellValue ( "BS2", "OUT" );
        $this->fiky_excel->setCellValue ( "BT2", "KET" );
        //TGL 23
        $this->fiky_excel->mergeCells('BU1:BW1');
        $this->fiky_excel->setCellValue ( "BU1", "TGL 23");
        $this->fiky_excel->setCellValue ( "BU2", "IN" );
        $this->fiky_excel->setCellValue ( "BV2", "OUT" );
        $this->fiky_excel->setCellValue ( "BW2", "KET" );
        //TGL 24
        $this->fiky_excel->mergeCells('BX1:BZ1');
        $this->fiky_excel->setCellValue ( "BX1", "TGL 24");
        $this->fiky_excel->setCellValue ( "BX2", "IN" );
        $this->fiky_excel->setCellValue ( "BY2", "OUT" );
        $this->fiky_excel->setCellValue ( "BZ2", "KET" );
        //TGL 25
        $this->fiky_excel->mergeCells('CA1:CC1');
        $this->fiky_excel->setCellValue ( "CA1", "TGL 25");
        $this->fiky_excel->setCellValue ( "CA2", "IN" );
        $this->fiky_excel->setCellValue ( "CB2", "OUT" );
        $this->fiky_excel->setCellValue ( "CC2", "KET" );
        //TGL 26
        $this->fiky_excel->mergeCells('CD1:CF1');
        $this->fiky_excel->setCellValue ( "CD1", "TGL 26");
        $this->fiky_excel->setCellValue ( "CD2", "IN" );
        $this->fiky_excel->setCellValue ( "CE2", "OUT" );
        $this->fiky_excel->setCellValue ( "CF2", "KET" );
        //TGL 27
        $this->fiky_excel->mergeCells('CG1:CI1');
        $this->fiky_excel->setCellValue ( "CG1", "TGL 27");
        $this->fiky_excel->setCellValue ( "CG2", "IN" );
        $this->fiky_excel->setCellValue ( "CH2", "OUT" );
        $this->fiky_excel->setCellValue ( "CI2", "KET" );
        //TGL 28
        $this->fiky_excel->mergeCells('CJ1:CL1');
        $this->fiky_excel->setCellValue ( "CJ1", "TGL 28");
        $this->fiky_excel->setCellValue ( "CJ2", "IN" );
        $this->fiky_excel->setCellValue ( "CK2", "OUT" );
        $this->fiky_excel->setCellValue ( "CL2", "KET" );
        //TGL 29
        $this->fiky_excel->mergeCells('CM1:CO1');
        $this->fiky_excel->setCellValue ( "CM1", "TGL 29");
        $this->fiky_excel->setCellValue ( "CM2", "IN" );
        $this->fiky_excel->setCellValue ( "CN2", "OUT" );
        $this->fiky_excel->setCellValue ( "CO2", "KET" );
        //TGL 30
        $this->fiky_excel->mergeCells('CP1:CR1');
        $this->fiky_excel->setCellValue ( "CP1", "TGL 30");
        $this->fiky_excel->setCellValue ( "CP2", "IN" );
        $this->fiky_excel->setCellValue ( "CQ2", "OUT" );
        $this->fiky_excel->setCellValue ( "CR2", "KET" );
        //TGL 31
        $this->fiky_excel->mergeCells('CS1:CU1');
        $this->fiky_excel->setCellValue ( "CS1", "TGL 31");
        $this->fiky_excel->setCellValue ( "CS2", "IN" );
        $this->fiky_excel->setCellValue ( "CT2", "OUT" );
        $this->fiky_excel->setCellValue ( "CU2", "KET" );

        //SAKIT,IJIN,CUTI,ALPHA,DT,PC,CUTIPG
        $this->fiky_excel->mergeCells('CV1:DB1');
        $this->fiky_excel->setCellValue ( "CV1", "TOTAL ABSENSI PERIODE ".$tglinp );
        $this->fiky_excel->setCellValue ( "CV2", "SAKIT" );
        $this->fiky_excel->setCellValue ( "CW2", "IJIN" );
        $this->fiky_excel->setCellValue ( "CX2", "CUTI" );
        $this->fiky_excel->setCellValue ( "CY2", "ALPHA" );
        $this->fiky_excel->setCellValue ( "CZ2", "DT" );
        $this->fiky_excel->setCellValue ( "DA2", "PA" );
        $this->fiky_excel->setCellValue ( "DB2", "CUTI_PG" );

        $this->fiky_excel->start_at(3);
        $info=$this->m_absensi->excel_reportabsensi_user($bln,$thn,$nama);
        $this->fiky_excel->set_query($info);
        $this->fiky_excel->set_column(
            array('nik','nmlengkap','nmdept','nmsubdept','nmjabatan','kdregu',
                'tgl_in_1','tgl_out_1','tgl1',
                'tgl_in_2','tgl_out_2','tgl2',
                'tgl_in_3','tgl_out_3','tgl3',
                'tgl_in_4','tgl_out_4','tgl4',
                'tgl_in_5','tgl_out_5','tgl5',
                'tgl_in_6','tgl_out_6','tgl6',
                'tgl_in_7','tgl_out_7','tgl7',
                'tgl_in_8','tgl_out_8','tgl8',
                'tgl_in_9','tgl_out_9','tgl9',
                'tgl_in_10','tgl_out_10','tgl10',
                'tgl_in_11','tgl_out_11','tgl11',
                'tgl_in_12','tgl_out_12','tgl12',
                'tgl_in_13','tgl_out_13','tgl13',
                'tgl_in_14','tgl_out_14','tgl14',
                'tgl_in_15','tgl_out_15','tgl15',
                'tgl_in_16','tgl_out_16','tgl16',
                'tgl_in_17','tgl_out_17','tgl17',
                'tgl_in_18','tgl_out_18','tgl18',
                'tgl_in_19','tgl_out_19','tgl19',
                'tgl_in_20','tgl_out_20','tgl20',
                'tgl_in_21','tgl_out_21','tgl21',
                'tgl_in_22','tgl_out_22','tgl22',
                'tgl_in_23','tgl_out_23','tgl23',
                'tgl_in_24','tgl_out_24','tgl24',
                'tgl_in_25','tgl_out_25','tgl25',
                'tgl_in_26','tgl_out_26','tgl26',
                'tgl_in_27','tgl_out_27','tgl27',
                'tgl_in_28','tgl_out_28','tgl28',
                'tgl_in_29','tgl_out_29','tgl29',
                'tgl_in_30','tgl_out_30','tgl30',
                'tgl_in_31','tgl_out_31','tgl31',
                'sakit','ijin','cuti','alpha','dt','pa','cuti_pg'
            )
        );
        $this->fiky_excel->set_width(
            array(12,20,20,20,20,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                10,10,10,
                4,4,4,4,4,4,4
            )
        );

        $this->fiky_excel->exportXlsx("REPORT ABSENSI BULAN $bln TAHUN $thn");
    }


    function report_absensi_pernik(){
        $nik=$this->request->getPost('nik');
        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=date('Y-m-d',strtotime($tgl[0]));
        $tgl2=date('Y-m-d',strtotime($tgl[1]));
        $info=$this->m_absensi->q_transready_koreksi($nik,$tgl1,$tgl2);
        $this->excel_generator->set_query($info);
        $this->excel_generator->set_header(array('NIK','NAMALENGKAP','TANGGAL','JAM_MASUK','JAM_PULANG','KETERANGAN'

        ));
        $this->excel_generator->set_column(array('nik','nmlengkap','tgl','jam_masuk_absen','jam_pulang_absen','ketsts'
        ));
        $this->excel_generator->set_width(array(12,20,20,10,10,20
        ));


        $this->excel_generator->exportTo2007("REPORT ABSENSI");


    }

    function report_absensi_perdept(){
        $kddept=$this->request->getPost('kddept');
        $tanggal=$this->request->getPost('tgl');
        $tgl=explode(' - ',$tanggal);
        $tgl1=date('Y-m-d',strtotime($tgl[0]));
        $tgl2=date('Y-m-d',strtotime($tgl[1]));
        $info=$this->m_absensi->q_transready_koreksidept($kddept,$tgl1,$tgl2);
        $this->excel_generator->set_query($info);
        $this->excel_generator->set_header(array('NIK','REGU','NAMALENGKAP','BAGIAN','TANGGAL','JAM KERJA','JAM_MASUK','JAM_PULANG','KETERANGAN',
            'CUTI','IZIN','TERLAMBAT','PULANG AWAL','DINAS','CUTIBERSAMA',
            'Dok.CUTI','Dok.IZIN','Dok.TERLAMBAT','Dok.PULANG AWAL','Dok.DINAS','Dok CUTIBERSAMA'

        ));
        $this->excel_generator->set_column(array('nik','kdregu','nmlengkap','nmdept','tgl','jam_kerja','jam_masuk_absen','jam_pulang_absen','ketsts',
            'ketercuti','keterijin','ketertelat','keterpulangawal','keterdinas','ketcutibersama',
            'nodokcuti','nodokijin','nodoktelat','nodokpulangawal','nodokdinas','nodokcb'
        ));
        $this->excel_generator->set_width(array(12,20,20,10,10,10,20,20,20,
            20,20,20,20,20,20,
            20,20,20,20,20,20
        ));


        $this->excel_generator->exportTo2007("REPORT ABSENSI".' '.$kddept.' '.$tgl1.' '.$tgl2);


    }


    function uang_makan(){
        $data['title']="Laporan Absensi Uang Makan";
        $data['fingerprintwil']=$this->m_absensi->q_idfinger()->getResult();
        if($this->uri->getSegment(3)=="exist_data")
            $data['message']="<div class='alert alert-danger'>Data Sudah Ada</div>";
        else if($this->uri->getSegment(3)=="add_success")
            $data['message']="<div class='alert alert-success'>Input Data Sukses</div>";
        else if($this->uri->getSegment(3)=="fp_success")
            $data['message']="<div class='alert alert-success'>Download Data Sukses</div>";
        return $this->template->render('trans/absensi/v_absen_um',$data);
    }

    function list_um(){
        $data['title']="Laporan Absensi Uang Makan";
        $data['fingerprintwil']=$this->m_absensi->q_idfinger()->getResult();
        $branch=$this->request->getPost('branch');
        $tgl=explode(' - ',$this->request->getPost('tgl'));
        if (empty($tgl) or empty($branch)) { return redirect()->to(base_url('trans/absensi/uang_makan')); }
        $awal=$tgl[0];
        $akhir=$tgl[1];


        $data['tgl']=$this->request->getPost('tgl');
        $data['branch']=$this->request->getPost('branch');
        $data['list_um']=$this->m_absensi->q_absensi_um($branch,$awal,$akhir)->getResult();
        return $this->template->render('trans/absensi/v_absen_um_list',$data);
    }


    /* CONTROLLER ABSENSI MOBILE */
    function filter_absen_mobile(){

        $data['title']='Tarikan Data Mesin Absensi Mobile'; //aksesconvert absensi
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.19'; $versirelease='I.T.B.19/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
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
        if($this->uri->getSegment(3)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil Di Simpan </div>";
        } elseif ($this->uri->getSegment(3)=="exist"){
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada </div>";
        }
        else {
            $data['message']='';
        }
        $nama=$this->session->get('nik');
        $data['akses']=$this->m_global->list_aksespermenu($nama,$kodemenu)->getRowArray();
        $data['list_kanwil']=$this->m_absensi->q_kanwil()->getResult();
        //$data['dtl_opt']=$this->m_absensi->q_dblink_option()->getRowArray();
        return $this->template->render('trans/absensi/v_filter_absen_mobile',$data);
    }

    function save_option(){
        $dblink_id = $this->request->getPost('dblink_id');
        $branch = $this->request->getPost('branch');
        $idbu = $this->request->getPost('idbu');
        $c_hostaddr = base64_encode(trim($this->request->getPost('c_hostaddr')));
        $c_dbname = base64_encode(trim($this->request->getPost('c_dbname')));
        $c_userpg = base64_encode(trim($this->request->getPost('c_userpg')));
        $c_passpg = base64_encode(trim($this->request->getPost('c_passpg')));
        $doctype = $this->request->getPost('doctype');
        $erptype = $this->request->getPost('erptype');
        $description = $this->request->getPost('description');
        $inputdate = date('Y-m-d H:i:s');
        $inputby = $this->session->get('nama');

        $info = array(
            'idbu' => $idbu,
            'c_hostaddr' => $c_hostaddr,
            'c_dbname' => $c_dbname,
            'c_userpg' => $c_userpg,
            'c_passpg' => $c_passpg,
            'doctype' => $doctype,
            'erptype' => $erptype,
            'description' => $description,
            'inputdate' => $inputdate,
            'inputby' => $inputby,
        );

        $this->db->cache_delete('trans', 'absensi');
        $people = array('FIKY');
        if ( in_array(trim($this->session->get('nama')),$people)) {
            $builder = $this->db->table("sc_mst.option_dblink");
            $builder->where('branch','SBYNSA');
            $builder->where('dblink_id','CRMDEV');
            $builder->update($info);
        }

        return redirect()->to(base_url('trans/absensi/filter_absen_mobile/rep_succes'));
    }

    function ajax_tglakhir_mobile_cabang($kdcabang){
        $dtl_opt=$this->m_absensi->q_dblink_option()->getRowArray();
        $host = $dtl_opt['c_hostaddr']; $dbname = $dtl_opt['c_dbname']; $userpg = $dtl_opt['c_userpg']; $passpg = $dtl_opt['c_passpg'];
        $data = $this->m_absensi->tglakhir_mobile($kdcabang,$host,$dbname,$userpg,$passpg)->getRowArray();
        echo json_encode($data);
    }

    function show_mobile_attendance(){
        //$this->db->cache_delete('trans', 'absensi');

        $tanggal=$this->request->getPost('tgl');
        $kdcabang=$this->request->getPost('kdcabang');
        $maplikasi=$this->request->getPost('maplikasi');

        $tgl=explode(' - ',$tanggal);
        $tgla=$tgl[0].' 00:00:00';
        $tglb=$tgl[1].' 23:59:59';

        //echo date('m-d-Y H:i:s',strtotime($tgl1)).'<br>';
        //echo date('m-d-Y H:i:s',strtotime($tgl2));
        $tgl1=date('Y-m-d H:i:s',strtotime($tgla));
        $tgl2=date('Y-m-d H:i:s',strtotime($tglb));
        if (empty($tanggal)){
            return redirect()->to(base_url('trans/absensi/filter_absen_mobile'));
        }
        $data['title']="DATA MESIN ABSEN $tgla hingga $tglb Untuk Wilayah $kdcabang";
        $data['tgl1']=$tgla;
        $data['tgl2']=$tglb;
        $data['kdcabang']=$kdcabang;
        $data['message']='';
        $data['maplikasi']=$maplikasi;

        $dtl_opt=$this->m_absensi->q_dblink_option()->getRowArray();
        $host = base64_decode($dtl_opt['c_hostaddr']); $dbname = base64_decode($dtl_opt['c_dbname']);
        $userpg = base64_decode($dtl_opt['c_userpg']); $passpg = base64_decode($dtl_opt['c_passpg']);
        //$dtl_getResult = $this->m_absensi->read_dblink_join_karyawan($host,$dbname,$userpg,$passpg)->getResult();

        if ($maplikasi==='MCRM') {
            $data['ttldata']=$this->m_absensi->read_dblink_join_karyawan($host,$dbname,$userpg,$passpg,$tgl1,$tgl2,$kdcabang)->getNumRows();
            $data['list_absen']=$this->m_absensi->read_dblink_join_karyawan($host,$dbname,$userpg,$passpg,$tgl1,$tgl2,$kdcabang)->getResult();
        } else if ($maplikasi==='MABS') {
            $dbname='CRMABSENT';
            $data['ttldata']=$this->m_absensi->read_dblink_join_karyawan_mabs($host,$dbname,$userpg,$passpg,$tgl1,$tgl2,$kdcabang)->getNumRows();
            $data['list_absen']=$this->m_absensi->read_dblink_join_karyawan_mabs($host,$dbname,$userpg,$passpg,$tgl1,$tgl2,$kdcabang)->getResult();
        }


        return $this->template->render('trans/absensi/v_absensi_mobile',$data);

//        foreach ($dtl_getResult as $dt) {
//            echo $dt->nik.'   '.$dt->checktime;
//            echo '</br>';
//        }
//        echo $host.$dbname.$userpg.$passpg;

    }

    function tarik_data_mobile(){
        $this->db->cache_delete('trans', 'absensi');

        $tgla=$this->request->getPost('tgl1');
        $tglb=$this->request->getPost('tgl2');
        $tgl1=date('m-d-Y H:i:s',strtotime($tgla));
        $tgl2=date('m-d-Y H:i:s',strtotime($tglb));
        $tglawal=date('Y-m-d H:i:s',strtotime($tgla));
        $tglakhir=date('Y-m-d H:i:s',strtotime($tglb));
        $kdcabang=$this->request->getPost('kdcabang');
        $maplikasi=$this->request->getPost('maplikasi');

        $dtl_opt=$this->m_absensi->q_dblink_option()->getRowArray();
        $host = base64_decode($dtl_opt['c_hostaddr']); $dbname = base64_decode($dtl_opt['c_dbname']);
        $userpg = base64_decode($dtl_opt['c_userpg']); $passpg = base64_decode($dtl_opt['c_passpg']);
        if ($maplikasi==='MCRM') {
            $datane = $this->m_absensi->read_dblink_join_karyawan($host,$dbname,$userpg,$passpg,$tglawal,$tglakhir,$kdcabang)->getResult();
        } else if ($maplikasi==='MABS') {
            $dbname='CRMABSENT';
            $datane = $this->m_absensi->read_dblink_join_karyawan_mabs($host,$dbname,$userpg,$passpg,$tglawal,$tglakhir,$kdcabang)->getResult();
        }


        foreach ($datane as $dta){
            $badgenumber=trim($dta->idabsen);
            $userid=trim($dta->userid);
            $checktime=trim($dta->checktime);
            $this->db->query("delete from sc_tmp.checkinout where userid='$userid' and badgenumber='$badgenumber' and checktime='$checktime'");
            $cek_fingerid=$this->m_absensi->q_cek_fingerid($badgenumber)->getNumRows();
            if($cek_fingerid>0){
                $builder_trxerror = $this->db->table('sc_tmp.checkinout');
                $info=array(
                    'userid'=>trim($dta->userid),
                    'badgenumber'=>$badgenumber,
                    'nama'=>trim($dta->usersname),
                    'checktime'=>$checktime,
                    'inputan'=>'S',
                    'inputby'=>$this->session->get('nik')
                );
                $builder_trxerror->insert($info);
            }

        }
        echo json_encode(array("status" => TRUE));
    }

}
