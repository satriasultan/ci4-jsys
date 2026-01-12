<?php

namespace App\Controllers\Report;

use App\Controllers\BaseController;

class Manpower extends BaseController
{
    public function index()
    {
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.3'; $versirelease='I.L.A.3/RELEASE.401'; $releasedate=date('2021-04-12 00:00:00');
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
        $data['title'] = " Laporan Man Power Position ";
        $data['manpower'] = $this->m_manpower->q_manpower($param = null)->getResult();
        return $this->template->render('report/manpower/v_manpowerPosition',$data);
    }

    function downloadManpowerPosition(){

        $nama = trim($this->session->get('nama'));
        $tglskr= date("Ym");
        $tglinp=$thn.$bln;
        //$info=$this->m_absensi->excel_reportabsensi($bln,$thn);
        //$this->m_absensi->q_prreportabsensi($bln,$thn,$nama);


        $this->fiky_excel->mergeCells('A1:C1');
        $this->fiky_excel->setCellValue ( "A1", "STRUCTURE MAN POWER POSITION" );
        $this->fiky_excel->setCellValue ( "A2", "DEPARTMENT" );
        $this->fiky_excel->setCellValue ( "B2", "SECTION" );
        $this->fiky_excel->setCellValue ( "C2", "POSITION" );
        $this->fiky_excel->mergeCells('D1:L1');
        $this->fiky_excel->setCellValue ( "D1", "STATUS TETAP");
        $this->fiky_excel->setCellValue ( "D2", "PRESDIR" );
        $this->fiky_excel->setCellValue ( "E2", "VICE" );
        $this->fiky_excel->setCellValue ( "F2", "DIR" );
        $this->fiky_excel->setCellValue ( "G2", "GM" );
        $this->fiky_excel->setCellValue ( "H2", "MGR" );
        $this->fiky_excel->setCellValue ( "I2", "AM" );
        $this->fiky_excel->setCellValue ( "J2", "SPV" );
        $this->fiky_excel->setCellValue ( "K2", "STAFF" );
        $this->fiky_excel->setCellValue ( "L2", "OPR" );
        //TSDF
        $this->fiky_excel->mergeCells('M1:Q1');
        $this->fiky_excel->setCellValue ( "M1", "PKWT");
        $this->fiky_excel->setCellValue ( "M2", "MGR" );
        $this->fiky_excel->setCellValue ( "N2", "AM" );
        $this->fiky_excel->setCellValue ( "O2", "SPV" );
        $this->fiky_excel->setCellValue ( "P2", "STAFF" );
        $this->fiky_excel->setCellValue ( "Q2", "OPR" );
        //VIDI
        $this->fiky_excel->mergeCells('R1:R2');
        $this->fiky_excel->setCellValue ( "R1", "OUTSOURCE");
        //VIDI
        $this->fiky_excel->mergeCells('S1:S2');
        $this->fiky_excel->setCellValue ( "S1", "GRANDTOTAL");

        $this->fiky_excel->start_at(3);
        $info=$this->m_manpower->q_manpower($param = null);
        $this->fiky_excel->set_query($info);
        $this->fiky_excel->set_column(
            array('nmdept','nmsubdept','nmjabatan','presdir','vp','dir','gm','mng','am','spv','staff','opr',
                'pkwt_mng','pkwt_am','pkwt_spv','pkwt_staff','pkwt_opr','ttl_os','grandtotal'
            )
        );
        $this->fiky_excel->set_width(
            array(20,20,20,10,10,10, 10,10,10,10,10,10,
                10,10,10,10,10,10,10,
            )
        );

        $this->fiky_excel->exportXlsx("REPORT MAN POWER JTS _LAST");
    }


    function detail(){
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.2'; $versirelease='I.L.A.2/RELEASE.401'; $releasedate=date('2021-04-12 00:00:00');
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
        $data['title'] = " Laporan Detail Presensi Transready Per Periode ";
        return $this->template->render('report/presensi/v_detailpresensi',$data);
    }


    function showupPresensiTransready()
    {
        $daterange = trim($this->request->getPost('daterangefilter'));
        $id_dept = trim($this->request->getPost('id_dept_filter'));
        $plantfilter = trim($this->request->getPost('plantfilter'));
        $id_gol_filter = trim($this->request->getPost('id_gol_filter'));
        $nikfilter = $this->request->getPost('nikfilter');


        $tgl = explode(' - ',$daterange);
        $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
        if (!empty($plantfilter)) { $pplan=" and plant='$plantfilter'"; } else {  $pplan=""; }
        if (!empty($id_gol_filter)) { $idgol=" and id_gol='$id_gol_filter'"; } else {  $idgol=""; }
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
        if (empty($daterange)) {
            return redirect()->to(base_url("report/presensi/detail"));
        }


        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.1'; $versirelease='I.L.A.1/RELEASE.401'; $releasedate=date('2021-04-12 00:00:00');
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

        $data['title'] = " Laporan Detail Presensi Per Periode ";
        $filter = " and to_char(tgl::date,'dd-mm-yyyy') between '$tgl1' and '$tgl2'".$pplan.$idgol.$id_deptp.$nikfiltep;
        $data['filter'] = bin2hex($filter);
        $data['listpresensi'] = $this->m_absensi->report_presensi_transready($filter)->getResult();
        return $this->template->render('report/presensi/v_showupPresensiTransready',$data);

    }

    function downloadPresensiDetail()
    {
        $var = hex2bin($this->request->getGet('var'));
        $parameter = $var;
        $datane=$this->m_absensi->report_presensi_transready($parameter);
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array('NIK','NAMA KARYAWAN','DEPARTMENT','POSITION','TANGGAL',
        'JAM MASUK','JAM PULANG','CHECK IN','CHECK OUT','JENIS ABSEN','DOKUMEN'));
        $this->fiky_excel->set_column(array('nik','nmlengkap','nmdept','nmjabatan','tgl','jam_masuk','jam_pulang','jam_masuk_absen',
            'jam_pulang_absen','nmtypecuti','docabscut'));
        $this->fiky_excel->set_width(array(16,30,30,30,30,30,30,30,
            20,20,10));
        $this->fiky_excel->exportXlsx('Download Presensi Karyawan');
    }


}
