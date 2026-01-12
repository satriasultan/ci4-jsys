<?php

namespace App\Controllers\Report;

use App\Controllers\BaseController;

class Presensi extends BaseController
{
    public function index()
    {
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
        return $this->template->render('report/presensi/v_filterdtlabsensi',$data);
    }

    function showupPresensi()
    {
        $periode = trim($this->request->getPost('periode'));
        $id_dept_filter = trim($this->request->getPost('id_dept_filter'));
        $plantfilter = trim($this->request->getPost('plantfilter'));
        $id_gol_filter = trim($this->request->getPost('id_gol_filter'));
        $nikfilter = trim($this->request->getPost('nikfilter'));

        $p = explode('-',$periode);
        $bln =$p[0];
        $thn =$p[1];

        if (empty($periode)) {
            return redirect()->to(base_url("report/presensi"));
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
        $data['bln'] = $bln; $data['thn'] = $thn;
        $data['title'] = " Laporan Detail Presensi Per Periode ".$bln. ' / '.$thn;
        $this->m_absensi->q_prreportabsensi($bln,$thn,$nama);

        $filter = '';

        $data['listpresensi'] = $this->m_absensi->excel_reportabsensi_user_filter(trim($bln),trim($thn),trim($nama),$filter)->getResult();
        return $this->template->render('report/presensi/v_showupPresensi',$data);

    }

    function downloadPresensi(){

        $bln = trim($this->request->getGet('bln'));
        $thn = trim($this->request->getGet('thn'));
        $nama = trim($this->session->get('nama'));
        $tglskr= date("Ym");
        $tglinp=$thn.$bln;
        //$info=$this->m_absensi->excel_reportabsensi($bln,$thn);
        //$this->m_absensi->q_prreportabsensi($bln,$thn,$nama);


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
