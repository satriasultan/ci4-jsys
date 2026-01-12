<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Abscut extends BaseController
{

    public function index(){
        $data['title']="Absensi, Izin & Cuti";
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.A.1'; $versirelease='I.A.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='LEMBUR'";
        $dtlerror=$this->m_abscut->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_abscut->q_trxerror($paramerror)->getNumRows();
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
        $kmenu='I.A.A.1';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_abscut->list_karyawan_param(" and nik='$niksession'")->getRowArray();

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


        $daterange = $this->request->getPost('daterangefilter');
        $daterangefilterResume = $this->request->getPost('daterangefilterResume');
        $plant = $this->request->getPost('plantfilter');
        $id_gol = $this->request->getPost('id_gol_filter');
        $idtypecuti = $this->request->getPost('idtypecutifilter');
        $id_dept = $this->request->getPost('id_dept_filter');
        $tab = $this->request->getPost('tab');
        if (empty($tab) or $tab==='1') {
            $data['tab_active_1']='active';
        } else {
            $data['tab_active_2']='active';
        }


        $nikfilter = $this->request->getPost('nikfilter');

        if (!empty($daterange)) {
            $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
            $date1 = date('Y-m-d',strtotime($tgl1));
            $date2 = date('Y-m-d',strtotime($tgl2));
            $ptgl = " and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        } else {
            $datey = date('Y-m-d');
            $date1 = date('Y-m-d',strtotime($datey . "-7 days"));
            $date2 = date('Y-m-d',strtotime($datey . "+30 days"));
            $ptgl = " and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        }

        if (!empty($daterangefilterResume)) {
            $tglresume = explode(' - ',$daterangefilterResume); $tgl1resume = $tglresume[0]; $tgl2resume = $tglresume[1];
            $date1resume = date('Y-m-d',strtotime($tgl1resume));
            $date2resume = date('Y-m-d',strtotime($tgl2resume));
            $ptglresume = " and to_char(a.tglawal::date,'yyyy-mm-dd') between '$date1resume' and '$date2resume'";
        } else {
            $dateyresume = date('Y-m-d');
            $date1resume = date('Y-m-d',strtotime($dateyresume . "-7 days"));
            $date2resume = date('Y-m-d',strtotime($dateyresume . "+30 days"));
            $ptglresume = " and to_char(a.tglawal::date,'yyyy-mm-dd') between '$date1resume' and '$date2resume'";
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
        $data['list_abscut']=$this->m_abscut->q_abscut($parameter)->getResult();
        $data['tglresume1'] = $date1resume; $data['tglresume2'] = $date2resume;
        $data['list_abscut_resume']=$this->m_abscut->q_abscut_resume($param_resume_1='',$ptglresume)->getResult();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_abscut->q_deltrxerror($paramerror);
        return $this->template->render('trans/abscut/v_list',$data);



    }

    //////////////////////////////////////// LEMBUR NEW///////////////////////
    ///
    ///
    function input_new(){
        $data['title']="ABSENSI CUTI IZIN SAKIT";
        $data['type']="INPUT";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.A.1'; $versirelease='I.A.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.A.A.1'";
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


        return $this->template->render('trans/abscut/v_input_abscut',$data);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
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
        $getResult = $this->m_abscut->q_mtypecuti($param)->getResult();
        $count = $this->m_abscut->q_mtypecuti($param)->getNumRows();
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
        $kodemenu='I.A.A.1'; $versirelease='I.A.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['docno'] =$docno;
        $data['nama'] = $nama;

        return $this->template->render('trans/abscut/v_input_abscut',$data);

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
        $kodemenu='I.A.A.1'; $versirelease='I.A.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['docno'] =$docno;
        $data['nama'] = $nama;

        return $this->template->render('trans/abscut/v_input_abscut',$data);

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
        $kmenu='I.A.A.1';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_abscut->list_karyawan_param(" and nik='$niksession'")->getRowArray();

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

        $param=" and (upper(nmlengkap) like '%$search%' $paramglobal $paramfilter) or ( nik like '%$search%' $paramglobal $paramfilter ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_abscut->q_karyawan($param)->getResult();
        $count = $this->m_abscut->q_karyawan($param)->getNumRows();
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
        $kmenu='I.A.A.1';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_abscut->list_karyawan_param(" and nik='$niksession'")->getRowArray();

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
        $dtl = $this->m_abscut->q_karyawan($param);

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
        $dtl = $this->m_abscut->q_abscut($param);

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
                    'statusapprove' => 'P',
                    'description' => $keterangan,
                    'attachment' => $gambar,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,

                );


                $cek = $this->m_abscut->q_abscut(" and nik='$nik' and idtypecuti='$idtypecuti' and tglawal='$tgl' and status!='C'")->getNumRows();

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
                    'statusapprove' => 'P',
                    'description' => $keterangan,
                    ////'attachment' => '',
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,

                );
                $cek = $this->m_abscut->q_abscut(" and nik='$nik' and idtypecuti='$idtypecuti' and tglawal='$tgl' and status!='C'")->getNumRows();
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
                if ($builder->update($infox)) {
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
        $builder = $this->db->table('sc_trx.abscut');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $nama = trim($this->session->get('nama'));
        $info=array(
            'canceldate'=>date('Y-m-d H:i:s'),
            'cancelby'=>trim($this->session->get('nama')),
            'status'=>'C',
            'statusapprove' => 'C',
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


        return redirect()->to(base_url("trans/abscut"));
    }

    function setujui_abscut($docno){

        $nama = trim($this->session->get('nama'));
        $nik = trim($this->session->get('nik'));
        $read = $this->m_abscut->q_abscut(" and docno='$docno'")->getRowArray();
        //$readNik = $this->m_ess_cuti->list_karyawan_param(" and nik='$nik'")->getRowArray();
        $builder = $this->db->table('sc_trx.abscut');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        //ATASAN 1 , ATASAN 2 & ATASAN 3

        $info = array(
            'approvedate' => date('Y-m-d H:i:s'),
            'approveby' => trim($this->session->get('nama')),
            'statusapprove' => 'P',
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
            $builder_trxerror->delete();
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

    function downloadExcel(){
        $daterange = $this->request->getPost('daterange');
        $plant = $this->request->getPost('plant');
        $id_gol = $this->request->getPost('id_gol');
        $idtypecuti = $this->request->getPost('idtypecutidownload');
        $id_dept = $this->request->getPost('id_dept_download');
        $nikfilter = $this->request->getPost('nikdownload');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
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

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.A.A.1';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_abscut->list_karyawan_param(" and nik='$niksession'")->getRowArray();

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

        $parameter = " and status='P' and statusapprove='P' and to_char(tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'".$pplan.$idgol.$idtypecutip.$id_deptp.$nikdownloadp.$paramfilter;
        $datane=$this->m_abscut->q_abscut($parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('DOKUMEN','NIK','NAMA LENGKAP','DEPARTMENT','SUB DEPARTMEN/SECTION','COSTCENTER','PLANT','GROUP',
            'JENIS','TANGGAL','DURASI(MENIT)','KETERANGAN','INPUTBY'));
        $this->fiky_excel->set_column(array('docno1','nik1','nmlengkap','nmdept','nmsubdept','nmcostcenter','locaname','nmgroupgol',
            'nmtypecuti1','tglawal2','durasimenit','description','inputby1'));
        $this->fiky_excel->set_width(array(16,10,30,30,30,30,7,10,
            20,15,10,30,20));
        $this->fiky_excel->exportXlsx('Abscut '.$plant.' '.$id_gol.' '.$idtypecuti.' '.$tgl1.' sd '.$tgl2);
    }
    function downloadResumeExcel(){
        $daterange = $this->request->getPost('daterangeExcelResume');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.A.A.1';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_abscut->list_karyawan_param(" and nik='$niksession'")->getRowArray();


        $parameter = " and to_char(a.tglawal::date,'yyyy-mm-dd') between '$date1' and '$date2'";
        $datane=$this->m_abscut->q_abscut_resume('',$parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('DEPARTMENT','CUTI','IZIN','SAKIT','TERLAMBAT','DISPENSASI','ALPHA','COVID'));
        $this->fiky_excel->set_column(array('nmdept','cuti','izin','sakit','terlambat','dispensasi','alpha','sakit_covid'));
        $this->fiky_excel->set_width(array(30,12,12,12,12,12,12,12));
        $this->fiky_excel->exportXlsx('Resume Abscut Department'.$tgl1.' sd '.$tgl2);
    }



    function importabscut(){
        $data['title']="LIST ABSENSI IMPORT".$this->mimes->mimes['xlsx'];
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.A.2'; $versirelease='I.A.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='ABSCUT'";
        $dtlerror=$this->m_abscut->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_abscut->q_trxerror($paramerror)->getNumRows();
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
        $data['list_detail']=$this->m_abscut->q_abscut_tmp($parameter)->getResult();
        $data['adaisi']=$this->m_abscut->q_abscut_tmp($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_abscut->q_deltrxerror($paramerror);
        return $this->template->render('trans/abscut/v_list_import_abscut',$data);

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $this->db->query("delete from sc_tmp.abscut where docno='$nama'");


        $path = "./assets/files/abscut/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        if ($this->request->getPost('save')) {
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
                echo '<a href="'.base_url('/trans/abscut/importabscut').'">BACK</a>';
            } else {
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
                    $cek = $this->m_abscut->q_abscut($pm)->getNumRows();
                    if($cek <= 0 and $nik != '') {
                        $builder = $this->db->table("sc_tmp.abscut");
                        $builder->insert($data);
                    }
                    $no++;
                }
                return redirect()->to(base_url('trans/abscut/importabscut'));
            }
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.abscut where docno='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','ABSCUT');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'ABSCUT',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('trans/abscut/importabscut'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.abscut set status='F' where docno='$nama'");

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','ABSCUT');
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
        return redirect()->to(base_url('trans/abscut'));
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

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('trans/abscut/v_mkriteria',$data);

    }

    function list_mkriteria(){

        $list = $this->m_abscut->get_t_mtypecuti_view();
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
            "recordsTotal" => $this->m_abscut->t_mtypecuti_view_count_all(),
            "recordsFiltered" => $this->m_abscut->t_mtypecuti_view_count_filtered(),
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
            $builder = $this->db->table('sc_mst.mtypecuti');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idtypecuti = '$idtypecuti'";
                $check = $this->m_abscut->q_mtypecuti($param)->getNumRows();
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
                    if ($builder->insert($info)) {
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

                $builder->where('idtypecuti',$idtypecuti);
                if ($builder->update($info)) {
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idtypecuti);
                    echo json_encode($getResult);
                }
            } else if ($type==='DELETE') {

                $builder->where('idtypecuti',$idtypecuti);
                if ($builder->delete()) {
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
        $data = $this->m_abscut->get_t_mtypecuti_view_by_id($id);
        echo json_encode($data);
    }


    function test_download(){
        $parameter = " and status='P' and statusapprove='P'";
        $datane=$this->m_abscut->q_abscut($parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('DOKUMEN','NIK','NAMA LENGKAP','DEPARTMENT','SUB DEPARTMEN/SECTION','COSTCENTER','PLANT','GROUP',
            'JENIS','TANGGAL','DURASI(MENIT)','KETERANGAN','INPUTBY'));
        $this->fiky_excel->set_column(array('docno1','nik1','nmlengkap','nmdept','nmsubdept','nmcostcenter','locaname','nmgroupgol',
            'nmtypecuti1','tglawal2','durasimenit','description','inputby1'));
        $this->fiky_excel->set_width(array(16,10,30,30,30,30,7,10,
            20,15,10,30,20));
        $this->fiky_excel->exportXlsx('Abscut Test');
    }

    // menampilkan cuti balance karyawan awal
    function cutibalance(){
        $data['title']='Sisa Cuti Per Karyawan';
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.11'; $versirelease='I.T.B.11/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $tahune=$this->request->getPost('pilihtahun');
        $depte=$this->request->getPost('lsdept');

        $kmenu=$kodemenu;
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];

        if (empty($tahune) and empty($depte)){
            $tahun=date('Y');
            $deptx='';
        } else if ( empty ($depte)){
            $tahun=$tahune;
            $deptx='';
        } else {
            $tahun=$tahune;
            $deptx=" and trim(id_dept) = '".$depte."'";
        }

        $nama=$this->session->get('nik');
        $userinfo=$this->m_global->q_user_check()->getRowArray();
        $userhr=$this->m_global->list_aksesperdep()->getNumRows();
        $level_akses=strtoupper(trim($userinfo['level_akses']));
        $data['nama']=$nama;
        $niksession=trim($this->session->get('nik'));
        $data['userhr']=$userhr;
        $data['level_akses']=$level_akses;

        $data['tahune']=$tahun;
        $data['kddept']=$depte;
        $data['role']=$role;
        //$data['nmdept']=$nmdept;

        if ($role==='ESS') {
            $paramFromRole = " and (a.nik ='$niksession' or a.nikatasan1='$niksession' or nikatasan2='$niksession' or nikatasan3='$niksession')";
        } else {
            $paramFromRole = '';
        }
        $data['listdepartmen']=$this->m_global->q_departmen("")->getResult();
        $data['listkaryawan']=$this->m_global->q_lv_m_karyawan("")->getResult();
        $data['listblc']=$this->m_abscut->q_cutiblc($tahun,$deptx,$paramFromRole)->getResult();

        return $this->template->render('trans/abscut/v_listblc',$data);
    }
    // menampilkan cuti balance karyawan detail
    function cutibalancedtl(){

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.11'; $versirelease='I.T.B.11/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $nik=$this->request->getPost('kdkaryawan');
        $tahun=trim($this->request->getPost('tahunlek'));
        $nikht=$this->request->getPost('htgkry');

        $kmenu=$kodemenu;
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];


        $data['title']='List Balance';
        $data['listblc']=$this->m_abscut->q_cutiblcdtl($nik,$tahun)->getResult();
        $data['tahun']=$tahun;
        $data['nik']=$nik;
        $data['role']=$role;
        //$this->m_cuti_karyawan->q_proc_htg($nikht);
        return $this->template->render('trans/abscut/v_listblcdtl',$data);

    }

    //hitung all cuti
    function pr_hitungallcuti(){
        $this->m_abscut->q_hitungallcuti();
        return redirect()->to(base_url('trans/abscut/cutibalance'));
    }


    //reporting excel
    public function excel_blc(){
        $tahun=trim($this->uri->getSegment(4));
        $datane=$this->m_abscut->excel_cutiblc($tahun);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('NIK','Nama Lengkap','Tanggal Cuti','Department','Sub Bagian','Jabatan','Sisa Cuti','Deskripsi'
        ));
        $this->fiky_excel->set_column(array('nik','nmlengkap','tanggal','nmdept','nmsubdept','nmjabatan','sisacuti','description'
        ));
        $this->fiky_excel->set_width(array(20,20,20,20,20,20,20,4,40
        ));
        $this->fiky_excel->exportXlsx("Saldo Cuti Per Karyawan ".$tahun);

    }

    public function excel_blc_dtl(){
        $tahun=trim($this->uri->segment(4));
        $nik=trim($this->uri->segment(5));

        $datane=$this->m_cuti_karyawan->excel_cutiblcdtl($nik,$tahun);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('NIK','Nama Lengkap','Dokumen','Tanggal Cuti','In','Out','Sisa','Awal','Akhir'
        ));
        $this->fiky_excel->set_column(array('nik','nmlengkap','no_dokumen','tanggal','in_cuti','out_cuti','sisacuti','tgl_mulai','tgl_selesai'
        ));
        $this->fiky_excel->set_width(array(20,20,20,20,10,10,10,10,10
        ));
        $this->fiky_excel->exportXlsx("Saldo Detail Cuti ".$nik);

    }

}
