<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Ess_cuti extends BaseController
{
    function index(){


        $data['title']="Absensi, Izin & Cuti";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.2'; $versirelease='I.E.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='LEMBUR'";
        $dtlerror=$this->m_ess_cuti->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_ess_cuti->q_trxerror($paramerror)->getNumRows();
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
        $kmenu='I.E.A.2';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_ess_cuti->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        $paramfilter = " and (nik ='$niksession' or nikatasan1='$niksession' or nikatasan2='$niksession' or nikatasan3='$niksession')";

        $daterange = $this->request->getPost('daterangefilter');
        $daterangefilterResume = $this->request->getPost('daterangefilterResume');
        $plant = $this->request->getPost('plantfilter');
        $id_gol = $this->request->getPost('id_gol_filter');
        $idtypecuti = $this->request->getPost('idtypecutifilter');
        $id_dept = $this->request->getPost('id_dept_filter');
        $nikfilter = $this->request->getPost('nikfilter');
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
            $ptgl = " and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        } else {
            $datey = date('Y-m-d');
            $date1 = date('Y-m-d',strtotime($datey . "-20 days"));
            $date2 = date('Y-m-d',strtotime($datey . "+30 days"));
            $ptgl = " and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        }

        if (!empty($daterangefilterResume)) {
            $tglresume = explode(' - ',$daterangefilterResume); $tgl1resume = $tglresume[0]; $tgl2resume = $tglresume[1];
            $date1resume = date('Y-m-d',strtotime($tgl1resume));
            $date2resume = date('Y-m-d',strtotime($tgl2resume));
            $ptglresume = " and to_char(b.tglawal::date,'yyyy-mm-dd') between '$date1resume' and '$date2resume'";
        } else {
            $dateyresume = date('Y-m-d');
            $date1resume = date('Y-m-d',strtotime($dateyresume . "-7 days"));
            $date2resume = date('Y-m-d',strtotime($dateyresume . "+30 days"));
            $ptglresume = " and to_char(b.tglawal::date,'yyyy-mm-dd') between '$date1resume' and '$date2resume'";
        }
        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($id_gol)) { $idgol=" and id_gol='$id_gol'"; } else {  $idgol=""; }
        //type cuti
        if (!empty($idtypecuti)) {
            $i=0;
            foreach ($idtypecuti as $dd) {
                if ($i == 0){
                    $idtypecutipx.=" idtypecuti='$dd'";
                } else { $idtypecutipx.=" or idtypecuti='$dd'"; }
                $i++;
            }
            $idtypecutip=" and (".$idtypecutipx.")";
        } else {  $idtypecutip=""; }
        //dept
        if (!empty($id_dept)) {
            $i=0;
            foreach ($id_dept as $dd) {
                if ($i == 0){
                    $id_deptpx.=" id_dept='$dd'";
                } else { $id_deptpx.=" or id_dept='$dd'"; }
                $i++;
            }
            $id_deptp=" and (".$id_deptpx.")";
        } else {  $id_deptp=""; }
        //nik
        if (!empty($nikfilter)) {
            $i=0;
            foreach ($nikfilter as $dd) {
                if ($i == 0){
                    $nikfiltepx.=" nik='$dd'";
                } else { $nikfiltepx.=" or nik='$dd'"; }
                $i++;
            }
            $nikfiltep=" and (".$nikfiltepx.")";
        } else {  $nikfiltep=""; }

        $statusapprove = trim($this->request->getPost('statusapprove'));
        if (!empty($statusapprove)) {
            $pstatusapprove = " and trim(statusapprove)='$statusapprove'";
        } else {
            $pstatusapprove = "";
        }

        $parameter = $ptgl.$pplan.$idgol.$idtypecutip.$id_deptp.$nikfiltep.$paramfilter.$pstatusapprove;
        ///$parameter = " and to_char(tgl_kerja,'mmYYYY') = '$tgl'";
        $data['list_abscut']=$this->m_ess_cuti->q_abscut($parameter)->getResult();
        $data['dtlkary'] = $dtlkary;
        $data['niksession'] = $niksession;
        $data['tglresume1'] = $date1resume; $data['tglresume2'] = $date2resume;
        $data['list_abscut_resume']=$this->m_ess_cuti->q_abscut_nik_resume($paramfilter,$ptglresume)->getResult();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_ess_cuti->q_deltrxerror($paramerror);;
        return $this->template->render('trans/ess_cuti/v_list',$data);

    }

    //////////////////////////////////////// LEMBUR NEW///////////////////////

    function input_new(){
        $data['title']="ABSENSI CUTI IZIN SAKIT";
        $data['type']="INPUT";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.2'; $versirelease='I.E.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.E.A.2'";
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
        return $this->template->render('trans/ess_cuti/v_input_ess_cuti',$data);

    }

    function list_mtypecuti(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idtypecuti='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idtypecuti='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (coalesce(chold,'')!='YES' and idtypecuti like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (coalesce(chold,'')!='YES' and nmtypecuti like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) ";
        //$param="";
        $getResult = $this->m_ess_cuti->q_mtypecuti($param)->getResult();
        $count = $this->m_ess_cuti->q_mtypecuti($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function edit(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $data['title'] = ' DATA ABSENSI';
        $data['type']="EDIT";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.2'; $versirelease='I.E.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['docno'] =$docno;
        $data['nama'] = $nama;

        return $this->template->render('trans/ess_cuti/v_input_ess_cuti',$data);

    }

    function detail(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $data['title'] = 'DATA ABSENSI';
        $data['type']="DETAIL";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.2'; $versirelease='I.E.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['docno'] =$docno;
        $data['nama'] = $nama;

        return $this->template->render('trans/ess_cuti/v_input_ess_cuti',$data);

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
        $paramfilter = " and (nik ='$niksession' or nikatasan1='$niksession' or nikatasan2='$niksession' or nikatasan3='$niksession') $paramvar";


        $param=" and (upper(nmlengkap) like '%$search%' $paramfilter) or ( nik like '%$search%' $paramfilter ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_ess_cuti->q_karyawan($param)->getResult();
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

    function showDetailNik () {
        $nama = $this->session->get('nama');
        $docno = $this->request->getGet('docno');
        $nik = $this->request->getGet('nik');


        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.E.A.2';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        //$dtlkary = $this->m_ess_cuti->list_karyawan_param(" and nik='$niksession'")->getRowArray();


        //$paramfilter = " and nik='".trim($dtlkary['plant'])."' and id_gol in ('SUP','OP')";
        $paramfilter = "";

        $param = " and nik='$nik'".$paramfilter;
        $dtl = $this->m_ess_cuti->q_karyawan($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function showDetailAbscut () {
        $nama = $this->session->get('nama');
        $docno = trim($this->request->getGet('docno'));
        $nik = trim($this->request->getGet('nik'));

        $param = " and docno='$docno'";
        $dtl = $this->m_ess_cuti->q_abscut($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function saveAbscut(){
        $nama = trim($this->session->get('nama'));
        $nik = $this->request->getPost('nik');
        $docno = trim($this->request->getPost('docno'));
        $tgl = date('Y-m-d',strtotime($this->request->getPost('tgl')));
        $idtypecuti = $this->request->getPost('idtypecuti');
        $durasimenit = $this->request->getPost('durasimenit');
        $keterangan = strtoupper($this->request->getPost('keterangan'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        $type = trim($this->request->getPost('type'));
        $builder = $this->db->table('sc_trx.abscut');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');


        $path = "./assets/files/abscut/imagetrans";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $file = $this->request->getFile('attachment');
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'cimage' => [
                    'uploaded[attachment]',
                    'mime_in[attachment,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[attachment,4096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 450 pixel</br>
					* Tinggi Max 550 pixel</br>					
					');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = $file->getRandomName();

                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                    // ->convert(IMAGETYPE_PNG)
                    ->resize(450, 550, true, 'height')
                    ->save($path . '/' . $final_file_name);
                $gambar=$final_file_name;
            }

            if ($type === 'INPUT') {
                $infox = array(
                    'docno' => $nama,
                    'nik' => $nik,
                    'docdate' => $inputdate,
                    'tglawal' => date('Y-m-d', strtotime($tgl)),
                    'tglakhir' => date('Y-m-d', strtotime($tgl)),
                    'idtypecuti' => $idtypecuti,
                    'durasimenit' => $durasimenit,
                    'status' => 'I',
                    'statusapprove' => 'A',
                    'description' => $keterangan,
                    'attachment' => $gambar,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );
                $cek = $this->m_ess_cuti->q_abscut(" and nik='$nik' and idtypecuti='$idtypecuti' and tglawal='$tgl' and status!='C'")->getNumRows();
                if ($cek > 0) {
                    $output = array(
                        'status' => false,
                        'messages' => 'Data sudah ada, Harap di cek ulang yach!!',
                    );
                    echo json_encode($output);
                } else {

                    if ($builder->insert($infox)) {
                        $paramerror = " and userid='$nama' and modul='ABSCUT'";
                        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                        $output = array(
                            'status' => true,
                            'messages' => trim($dtlerror['description']) . '
    DOKUMEN: ' . $dtlerror['nomorakhir1'],
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

            } else if ($type === 'EDIT') {

                $infox = array(
                    'tglawal' => date('Y-m-d', strtotime($tgl)),
                    'tglakhir' => date('Y-m-d', strtotime($tgl)),
                    'idtypecuti' => $idtypecuti,
                    'durasimenit' => $durasimenit,
                    'status' => 'P',
                    'description' => $keterangan,
                    'attachment' => $gambar,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder->where('docno', $docno);
                if ($builder->update($infox)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid', $nama);
                    $builder_trxerror->where('modul', 'ABSCUT');
                    $builder_trxerror->delete('sc_mst.trxerror');
                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $docno,
                        'nomorakhir2' => '',
                        'modul' => 'ABSCUT',
                    );
                    $builder_trxerror->insert( $infotrxerror);
                    // SHOW getResult TRXERROR
                    $paramerror = " and userid='$nama' and modul='ABSCUT'";
                    $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $output = array(
                        'status' => true,
                        'messages' => trim($dtlerror['description']) . '
    DOKUMEN: ' . $dtlerror['nomorakhir1'],
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

        } else {
            if ($type === 'INPUT') {
                $infox = array(
                    'docno' => $nama,
                    'nik' => $nik,
                    'docdate' => $inputdate,
                    'tglawal' => date('Y-m-d', strtotime($tgl)),
                    'tglakhir' => date('Y-m-d', strtotime($tgl)),
                    'idtypecuti' => $idtypecuti,
                    'durasimenit' => $durasimenit,
                    'status' => 'I',
                    'statusapprove' => 'A',
                    'description' => $keterangan,
                    ////'attachment' => '',
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,

                );


                $cek = $this->m_ess_cuti->q_abscut(" and nik='$nik' and idtypecuti='$idtypecuti' and tglawal='$tgl' and status!='C'")->getNumRows();

                if ($cek > 0) {
                    $output = array(
                        'status' => false,
                        'messages' => 'Data sudah ada, Harap di cek ulang yach!!',
                    );
                    echo json_encode($output);
                } else {

                    if ($builder->insert($infox)) {

                        $paramerror = " and userid='$nama' and modul='ABSCUT'";
                        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                        $output = array(
                            'status' => true,
                            'messages' => trim($dtlerror['description']) . '
    DOKUMEN: ' . $dtlerror['nomorakhir1'],
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

            } else if ($type === 'EDIT') {

                $infox = array(
                    'tglawal' => date('Y-m-d', strtotime($tgl)),
                    'tglakhir' => date('Y-m-d', strtotime($tgl)),
                    'idtypecuti' => $idtypecuti,
                    'durasimenit' => $durasimenit,
                    'status' => 'P',
                    'description' => $keterangan,
                    ////'attachment' => '',
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder->where('docno', $docno);
                if ($builder->update( $infox)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid', $nama);
                    $builder_trxerror->where('modul', 'ABSCUT');
                    $builder_trxerror->delete();
                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $docno,
                        'nomorakhir2' => '',
                        'modul' => 'ABSCUT',
                    );
                    $builder_trxerror->insert($infotrxerror);
                    // SHOW getResult TRXERROR
                    $paramerror = " and userid='$nama' and modul='ABSCUT'";
                    $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $output = array(
                        'status' => true,
                        'messages' => trim($dtlerror['description']) . '
    DOKUMEN: ' . $dtlerror['nomorakhir1'],
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
    }

    function hps_abscut($docno){

        $nama = trim($this->session->get('nama'));
        $builder = $this->db->table('sc_trx.abscut');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $info=array(
            'canceldate'=>date('Y-m-d H:i:s'),
            'cancelby'=>trim($this->session->get('nama')),
            'status'=>'C',
            'statusapprove'=>'C',
        );

        $builder->where('docno',$docno);
        if($builder->update($info)) {
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','ABSCUT');
            $builder_trxerror->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'ABSCUT',
            );
            $builder_trxerror->insert($infotrxerror);
        } else {
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','ABSCUT');
            $builder_trxerror->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 2,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'ABSCUT',
            );
            $builder_trxerror->insert($infotrxerror);
        }


        return redirect()->to(base_url("trans/ess_cuti"));
    }

    function setujui_abscut($docno){

        $nama = trim($this->session->get('nama'));
        $nik = trim($this->session->get('nik'));
        $read = $this->m_ess_cuti->q_abscut(" and docno='$docno'")->getRowArray();
        //$readNik = $this->m_ess_cuti->list_karyawan_param(" and nik='$nik'")->getRowArray();

        //ATASAN 1 , ATASAN 2 & ATASAN 3

        if (trim($read['nikatasan1']) !== '' and trim($read['nikatasan2']) !== '' and trim($read['nikatasan3']) !== '') {
            if (trim($read['statusapprove']) === 'A' and trim($read['nikatasan1']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'A1',
                );
            } else if (trim($read['statusapprove']) === 'A1' and trim($read['nikatasan2']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'A2',
                );
            } else if (trim($read['statusapprove']) === 'A2' and trim($read['nikatasan3']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'P',
                );
            }
        } else if (trim($read['nikatasan1']) !== '' and trim($read['nikatasan2']) !== '' and trim($read['nikatasan3']) === '') {
            if (trim($read['statusapprove']) === 'A' and trim($read['nikatasan1']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'A1',
                );
            } else if (trim($read['statusapprove']) === 'A1' and trim($read['nikatasan2']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'P',
                );
            } else if (trim($read['statusapprove']) === 'A2' and trim($read['nikatasan3']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'P',
                );
            }
        } else if (trim($read['nikatasan1']) !== '' and trim($read['nikatasan2']) === '' and trim($read['nikatasan3']) !== '') {
            if (trim($read['statusapprove']) === 'A' and trim($read['nikatasan1']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'P',
                );
            } else if (trim($read['statusapprove']) === 'A1' and trim($read['nikatasan2']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'P',
                );
            } else if (trim($read['statusapprove']) === 'A2' and trim($read['nikatasan3']) === $nik) {
                $info = array(
                    'approvedate' => date('Y-m-d H:i:s'),
                    'approveby' => trim($this->session->get('nama')),
                    'statusapprove' => 'P',
                );
            }
        }





        $this->db->where('docno',$docno);
        if($this->db->update('sc_trx.abscut',$info)) {
            $this->db->where('userid',$nama);
            $this->db->where('modul','ABSCUT');
            $this->db->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'ABSCUT',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
        } else {
            $this->db->where('userid',$nama);
            $this->db->where('modul','ABSCUT');
            $this->db->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 2,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'ABSCUT',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
        }


        return redirect()->to(base_url("trans/ess_cuti"));
    }

    function downloadExcel(){
        $daterange = $this->request->getPost('daterange');
        $plant = $this->request->getPost('plant');
        $id_gol = $this->request->getPost('id_gol');
        $idtypecuti = $this->request->getPost('idtypecutidownload');
        $id_dept = $this->request->getPost('id_dept_download');
        $nikdownload = $this->request->getPost('nikdownload');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($id_gol)) { $idgol=" and id_gol='$id_gol'"; } else {  $idgol=""; }
        if (!empty($idtypecuti)) { $idtypecutip=" and idtypecuti='$idtypecuti'"; } else {  $idtypecutip=""; }
        if (!empty($id_dept)) { $id_deptp=" and id_dept='$id_dept'"; } else {  $id_deptp=""; }
        if (!empty($nikdownload)) { $nikdownloadp=" and nik='$nikdownload'"; } else {  $nikdownloadp=""; }

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.E.A.2';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_ess_cuti->list_karyawan_param(" and nik='$niksession'")->getRowArray();

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

        $parameter = " and status='P' and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'".$pplan.$idgol.$idtypecutip.$id_deptp.$nikdownloadp.$paramfilter;
        $datane=$this->m_ess_cuti->q_abscut($parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('DOKUMEN','NIK','NAMA LENGKAP','DEPARTMENT','SUB DEPARTMEN/SECTION','COSTCENTER','PLANT','GROUP',
            'JENIS','TANGGAL','DURASI(MENIT)','KETERANGAN','INPUTBY'));
        $this->fiky_excel->set_column(array('docno1','nik1','nmlengkap','nmdept','nmsubdept','nmcostcenter','locaname','nmgroupgol',
            'nmtypecuti1','tglawal2','durasimenit','description','inputby1'));
        $this->fiky_excel->set_width(array(16,10,30,30,30,30,7,10,
            20,15,10,30,20));
        $this->fiky_excel->exportXlsx('Abscut '.$plant.' '.$id_gol.' '.$idtypecuti.' '.$tgl1.' sd '.$tgl2);
    }




    function importabscut(){
        $data['title']="LIST ABSENSI IMPORT";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.A.2'; $versirelease='I.A.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='ABSCUT'";
        $dtlerror=$this->m_ess_cuti->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_ess_cuti->q_trxerror($paramerror)->getNumRows();
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
        $kmenu='I.A.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " ";
        $data['list_detail']=$this->m_ess_cuti->q_abscut_tmp($parameter)->getResult();
        $data['adaisi']=$this->m_ess_cuti->q_abscut_tmp($parameter)->getNumRows();

        return $this->template->render('trans/ess_cuti/v_list_import_abscut',$data);

        $paramerror=" and userid='$nama'";
        $this->m_ess_cuti->q_deltrxerror($paramerror);;

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $this->db->query("delete from sc_tmp.abscut where docno='$nama'");


        $path = "./assets/files/abscut/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        if ($this->request->getPost('save')) {
            $fileName = $_FILES['import']['name'];
            unlink($path.$fileName);

            $config['upload_path'] = $path;
            $config['file_name'] = $fileName;
            $config['allowed_types'] = 'xls|xlsx';
            $config['max_size']		= 10000;
            $this->upload->initialize($config);

            if(! $this->upload->do_upload('import') )
                $this->upload->display_errors();

            echo $inputFileName = $path.trim($fileName);

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
            //$JUDUL[0][0].'</br>';
            $no=1;
            for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);

                //  Insert row data array into your database of choice here
                $data = array(
                    'docno' => $nama,
                    'nik' => $nik = trim($rowData[0][0]),
                    'docdate' => date("Y-m-d H:i:s"),
                    'idtypecuti' => $idtype = trim($rowData[0][2]),
                    'tglawal' => $tglawal = date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][3])))),
                    'tglakhir' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][3])))),
                    'durasimenit'=>empty($rowData[0][4]) ? NULL : trim($rowData[0][4]),
                    'description'=>trim($rowData[0][5]),
                    'inputby'=>$nama,
                    'inputdate'=>date("Y-m-d H:i:s"),
                    'status' => 'I',
                );

                $pm =  " and nik='$nik' and idtypecuti='$idtype' and tglawal='$tglawal' ";
                $cek = $this->m_ess_cuti->q_abscut($pm)->getNumRows();
                if($cek <= 0) {
                    $this->db->insert("sc_tmp.abscut",$data);
                }
                //trim($rowData[0][1]).'   nik:'.trim($rowData[0][0]);
                $no++;
            }

            return redirect()->to(base_url('trans/ess_cuti/importabscut'));
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("delete from sc_tmp.abscut where docno='$nama'");
        $this->db->where('userid',$nama);
        $this->db->where('modul','ABSCUT');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'ABSCUT',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/ess_cuti/importabscut'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("update sc_tmp.abscut set status='F' where docno='$nama'");
        $this->db->where('userid',$nama);
        $this->db->where('modul','ABSCUT');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'LEMBUR',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/ess_cuti'));
    }


    /* Master absen Kriteria */
    function mkriteria(){
        $data['title']="MASTER KRITERIA ABSENSI";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.F.14'; $versirelease='I.M.F.14/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='ABSCUT'";
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

        return $this->template->render('trans/ess_cuti/v_mkriteria',$data);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
    }

    function list_mkriteria(){

        $list = $this->m_ess_cuti->get_t_mtypecuti_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-default" href="javascript:void(0)" title="Detail" onclick="update_mkriteria('."'".trim($lm->idtypecuti)."'".')"><i class="fa fa-bars"></i> </a>
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="update_mkriteria('."'".trim($lm->idtypecuti)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_mkriteria('."'".trim($lm->idtypecuti)."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';

            $row[] = $lm->idtypecuti;
            $row[] = $lm->nmtypecuti;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_ess_cuti->t_mtypecuti_view_count_all(),
            "recordsFiltered" => $this->m_ess_cuti->t_mtypecuti_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function saveEntryKriteria(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $idtypecuti = strtoupper(trim($dataprocess->idtypecuti));
            $nmtypecuti = strtoupper(trim($dataprocess->nmtypecuti));
            $description = strtoupper(trim($dataprocess->description));
            $chold = strtoupper(trim($dataprocess->chold));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);

            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idtypecuti = '$idtypecuti'";
                $check = $this->m_ess_cuti->q_mtypecuti($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idtypecuti' => $idtypecuti,
                        'nmtypecuti' => $nmtypecuti,
                        'description' => $description,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($this->db->insert('sc_mst.mtypecuti', $info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idtypecuti);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $info = array(
                    'nmtypecuti' => $nmtypecuti,
                    'description' => $description,
                    'chold' => $chold,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );

                $this->db->where('idtypecuti',$idtypecuti);
                if ($this->db->update('sc_mst.mtypecuti', $info)) {
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idtypecuti);
                    echo json_encode($getResult);
                }
            } else if ($type==='DELETE') {

                $this->db->where('idtypecuti',$idtypecuti);
                if ($this->db->delete('sc_mst.mtypecuti')) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idtypecuti);
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }
            }


        } else {
            $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($getResult);
        }
    }

    function showing_data_kriteria($id){
        $data = $this->m_ess_cuti->get_t_mtypecuti_view_by_id($id);
        echo json_encode($data);
    }

}
