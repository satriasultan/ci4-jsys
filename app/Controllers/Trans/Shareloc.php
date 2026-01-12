<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Shareloc extends BaseController
{
    function index(){
        $data['title']="List Share Lokasi";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.C.2'; $versirelease='I.A.C.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.A.C.2'";
        $dtlerror=$this->m_shareloc->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_shareloc->q_trxerror($paramerror)->getNumRows();
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
        $kmenu='I.A.C.2';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_shareloc->list_karyawan_param(" and nik='$niksession'")->getRowArray();

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


        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($id_gol)) { $idgol=" and id_gol='$id_gol'"; } else {  $idgol=""; }
        if (!empty($id_dept_filter)) { $dept=" and id_dept='$id_dept_filter'"; } else {  $dept=""; }
        if (!empty($costcenter_filter)) { $costcenter=" and costcenter='$costcenter_filter'"; } else {  $costcenter=""; }



        $parameter = $ptgl.$pplan.$idgol.$dept.$costcenter.$paramfilter;
        ///$parameter = " and to_char(tgl_kerja,'mmYYYY') = '$tgl'";
        $data['list_lembur']=$this->m_shareloc->q_lembur($parameter)->getResult();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_shareloc->q_deltrxerror($paramerror);
        return $this->template->render('trans/shareloc/v_list',$data);
    }


    function report(){


        $data['title']="Report List Share Lokasi";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.C.2'; $versirelease='I.A.C.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.A.C.2'";
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
        $kmenu='I.A.C.2';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $data['list_shareloc'] = $this->m_shareloc->q_attendancestatus()->getResult();
        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/shareloc/v_list',$data);
    }

    function list_shareloc(){
        //AHC ADMIN HC // ML SUPER ADMIN IRGA
        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $list = $this->m_shareloc->get_t_his_shareloc_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            $row[] = $no;
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->nmsubdept;
            $row[] = $lm->attenddate1;
            $row[] = $lm->attend;
            if (trim($lm->attendstatus)==='0'){
                $row[] = '<span class="label label-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->attendstatus)==='1') {
                $row[] = '<span class="label label-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->attendstatus)==='2') {
                $row[] = '<span class="label label-warning" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="label label-primary" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_shareloc->t_his_shareloc_view_count_all(),
            "recordsFiltered" => $this->m_shareloc->t_his_shareloc_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function import(){

        $data['title']="Import Data Karyawan Shareloc";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.C.1'; $versirelease='I.A.C.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.A.C.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
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
        $kmenu='I.A.C.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and docno='$nama'";
        $data['akses']=$this->m_akses->list_aksespermenu($nama,$kmenu)->getRowArray();
        $data['list_shareloc']=$this->m_shareloc->q_shareloc_tmp($parameter)->getResult();
        $data['adaisi']=$this->m_shareloc->q_shareloc_tmp($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);;
        return $this->template->render('trans/shareloc/v_list_import',$data);
    }

    function proses_xls_shareloc() {
        $nama = $this->session->get('nama');
        $this->db->query("delete from sc_tmp.shareloc where docno='$nama'");
        $path = "./assets/files/shareloc/";
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
            $this->db->where('docno',$nama);
            $this->db->delete('sc_tmp.shareloc');

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
            $JUDUL[0][0].'</br>';
            $no=1;
            for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                //  Insert row data array into your database of choice here
                $data = array(
                    'docno'=>$nama,
                    'docname'=> 'SHARELOC',
                    'nik'=>trim($rowData[0][0]),
                    'docdate'=>date("Y-m-d H:i:s"),
                    'attend'=>strtoupper(trim($rowData[0][5])),
                    'attenddate'=>date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][4])))),
                    'attendstatus'=>trim($rowData[0][6]),
                    'status'=>'I',
                    'uploadby'=>$nama,
                    'uploaddate'=>date("Y-m-d H:i:s")
                );
                $varnik = trim($rowData[0][0]);
                $vartgl_kerja = date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][4]))));
                $var_attend = strtoupper(trim($rowData[0][4]));
                //$parametercek = " and nik='$varnik' and to_char(attenddate,'yyyy-mm-dd')='$vartgl_kerja' and attend in ('$var_attend')";
                //$cek = $this->m_shareloc->q_shareloc_his($parametercek)->getNumRows();
                if (!empty($rowData[0][0])) {
                    $this->db->insert("sc_tmp.shareloc",$data);
                    /* 3 */
                    $this->db->where('userid',$nama);
                    $this->db->where('modul','SHARELOC');
                    $this->db->delete('sc_mst.trxerror');
                    /* error handling */
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => '',
                        'nomorakhir2' => '',
                        'modul' => 'SHARELOC',
                    );
                    $this->db->insert('sc_mst.trxerror',$infotrxerror);
                } else {
                    $this->db->where('userid',$nama);
                    $this->db->where('modul','SHARELOC');
                    $this->db->delete('sc_mst.trxerror');
                    /* error handling */
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 7,
                        'nomorakhir1' => $varnik.'*'.$vartgl_kerja,
                        'nomorakhir2' => '',
                        'modul' => 'SHARELOC',
                    );
                    $this->db->insert('sc_mst.trxerror',$infotrxerror);
                }
                $no++;
            }

            return redirect()->to(base_url('trans/shareloc/import'));
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("delete from sc_tmp.shareloc where docno='$nama'");
        $this->db->where('userid',$nama);
        $this->db->where('modul','SHARELOC');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'SHARELOC',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/shareloc/import'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("update sc_tmp.shareloc set status='F' where docno='$nama'");
        $this->db->where('userid',$nama);
        $this->db->where('modul','SHARELOC');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'SHARELOC',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/shareloc/report'));
    }


    // REPORTING ABSEN ONLINE
    function report_absen_online(){
        $data['title']="Report List Share Lokasi";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.C.3'; $versirelease='I.A.C.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.A.C.3'";
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
        $kmenu='I.A.C.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $data['list_shareloc'] = $this->m_shareloc->q_attendancestatus()->getResult();
        return $this->template->render('trans/shareloc/v_list_onlineabsen',$data);

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);

    }

    function list_absen_online(){
        //AHC ADMIN HC // ML SUPER ADMIN IRGA
        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $list = $this->m_shareloc->get_t_tmp_onlineabsen_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            $row[] = $no;
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->nmsubdept;
            $row[] = $lm->checktime1;
            if (! empty($lm->imageblob)) {
                $row[] = 					//'test';
                    '<div class="pull-left image">
 					<a href="javascript:void(0)" onclick="show_image('."'".trim($lm->imageblob)."'".')"><img height="100px" width="100px" alt="User Image" class="img-box" src="'.$lm->imageblob.'"></a>
 				</div>';
            } else {
                $row[] =
                    '<div class="pull-left image">
 					<img height="100px" width="100px" alt="User Image" src="'.site_url('/assets/img/user.png').'">
 				</div>';
            }

            if (trim($lm->ctype)==='OUT'){
                $row[] = '<span class="label label-danger" style="font-size: 100%">'.$lm->ctype.'</span>';
            } else if (trim($lm->ctype)==='IN') {
                $row[] = '<span class="label label-success" style="font-size: 100%">'.$lm->ctype.'</span>';
            } else if (trim($lm->ctype)==='R-OUT') {
                $row[] = '<span class="label label-warning" style="font-size: 100%">'.$lm->ctype.'</span>';
            } else {
                $row[] = '<span class="label label-info" style="font-size: 100%">'.$lm->ctype.'</span>';
            }

            $row[] = $lm->alamat;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_shareloc->t_tmp_onlineabsen_view_count_all(),
            "recordsFiltered" => $this->m_shareloc->t_tmp_onlineabsen_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function downloadExcel(){
        $daterange = $this->request->getPost('daterangeDownload');
        $id_dept = $this->request->getPost('id_dept_download');
        $nikdownload = $this->request->getPost('nik_download');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];

        if (!empty($id_dept)) { $id_deptp=" and id_dept='$id_dept'"; } else {  $id_deptp=""; }
        if (!empty($nikdownload)) { $nikdownloadp=" and nik='$nikdownload'"; } else {  $nikdownloadp=""; }

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.A.C.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];

        $parameter = " and to_char(checktime::date,'yyyy-mm-dd') between '$date1' and '$date2'".$id_deptp.$nikdownloadp;
        $datane=$this->m_shareloc->q_tmponline_absen($parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('NIK','NAMA LENGKAP','DEPARTMENT','SUB DEPARTMEN/SECTION','CHECKTIME','TYPE','COORDINAT',
            'ALAMAT COORDINAT','DESA/DUSUN','KECAMATAN','KOTA/KABUPATEN','PROVINSI','NEGARA','KODEPOS'));
        $this->fiky_excel->set_column(array('nik','nmlengkap','nmdept','nmsubdept','checktime1','ctype','coordinat',
            'alamat','desa','kota','kabupaten','provinsi','negara','kodepos'));
        $this->fiky_excel->set_width(array(16,10,30,30,30,10,10,
            60,20,20,20,20,20,20));
        $this->fiky_excel->exportXlsx('Absensi Online '.$tgl1.' sd '.$tgl2);
    }


    /// LAPORAN HASIL SC_TMP.CHECKIOUT
    // REPORTING ABSEN ONLINE
    function report_checkinout(){
        $data['title']="LAPORAN PRESENSI DASAR DARI MESIN FINGERPRINT";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.A.3'; $versirelease='I.A.A.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.A.A.3'";
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
        $kmenu='I.A.A.3';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $data['list_shareloc'] = $this->m_shareloc->q_attendancestatus()->getResult();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/shareloc/v_list_checkinout',$data);

    }

    function list_report_checkinout(){
        //AHC ADMIN HC // ML SUPER ADMIN IRGA
        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $list = $this->m_shareloc->get_t_tmp_checkinout_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->id));
            $row = array();
            $row[] = $no;
            $row[] = $lm->userid;
            $row[] = $lm->badgenumber;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->nmsubdept;
            $row[] = $lm->checktime1;

            if (trim($lm->ctype)==='OUT'){
                $row[] = '<span class="label label-danger" style="font-size: 100%">'.$lm->ctype.'</span>';
            } else if (trim($lm->ctype)==='IN') {
                $row[] = '<span class="label label-success" style="font-size: 100%">'.$lm->ctype.'</span>';
            } else if (trim($lm->ctype)==='R-OUT') {
                $row[] = '<span class="label label-warning" style="font-size: 100%">'.$lm->ctype.'</span>';
            } else {
                $row[] = '<span class="label label-info" style="font-size: 100%">'.$lm->ctype.'</span>';
            }

            $row[] = $lm->statusid;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_shareloc->t_tmp_checkinout_view_count_all(),
            "recordsFiltered" => $this->m_shareloc->t_tmp_checkinout_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

}
