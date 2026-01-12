<?php

namespace App\Controllers\Stock;

use App\Controllers\BaseController;

class Report extends BaseController
{
    function index()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.D.1'; $versirelease='I.D.D.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.D.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        /* Item Transfer Master Check */
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_bbm->q_tmp_item_bbm_mst($param);

        if ($dtl->getNumRows()>0) {
            $title = "PERINGATAN !!!";
            $urlclear = base_url('stock/bbm/clearEntryBbm');
            $urlnext = base_url('stock/bbm/input_bbm');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbm/v_bbm',$data);
    }


    function sisastockgdg()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        } else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_sisa_stock',$data);
    }

    function show_sisastockgdg(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idgroup = $this->request->getPost('idgroup');
        $formheader = $this->request->getPost('formheader');

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Sisa Stock Per Item ( Gudang )";

        $datajson =  base_url("stock/report/api_sisastockgdg/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;

        if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_sisastockgdg.mrt") ;
        } else {
            $datamrt =  base_url("assets/mrt/report_sisastockgdg_non_header.mrt") ;
        }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_sisastockgdg(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($idbrg) or $idbrg==='') {
            $param_brg = "";
        } else {
            $param_brg = " and idbarang='$idbrg'";
        }

        //idgroup
        if (!empty($idgroup)) {
            $param_group=" and idgroup='$idgroup'";
        } else {  $param_group=""; }

        $param_gdg = " and idlocation='$idlocation' and to_char(trxdate,'yyyy-mm-dd')<='$tgl2'".$param_brg.$param_group;

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        $datadtl = $this->m_stockreport->q_stk_pergudang($param_gdg);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,
                    'param_gdg' => $param_gdg,

                    ]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    function sisastockposition()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_sisa_stock_position',$data);
    }

    function show_sisastockgdg_position(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');
        $idgroup = trim($this->request->getPost('idgroup'));

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idarea = $this->fiky_encryption->sealed($idarea);
        $enc_idgroup = $this->fiky_encryption->sealed($idgroup);

        $title = " Laporan Analisa Stock Per Area";

        $datajson =  base_url("stock/report/api_sisastockgdg_position/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idarea=$enc_idarea&enc_idgroup=$enc_idgroup") ;
        $datamrt =  base_url("assets/mrt/report_sisastockgdg_position.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_sisastockgdg_position(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($idbrg) or $idbrg==='') {
            $param_brg = "";
        } else {
            $param_brg = " and idbarang='$idbrg'";
        }

        //idarea
        if (!empty($idarea)) {
            $param_area=" and idarea='$idarea'";
        } else {  $param_area=""; }

        //idgroup
        if (!empty($idgroup)) {
            $param_group=" and idgroup='$idgroup'";
        } else {  $param_group=""; }

        //$param_gdg = " and idlocation='$idlocation'".$param_brg.$param_area.$param_group;
        $param_gdg = " and idlocation='$idlocation' and to_char(trxdate,'yyyy-mm-dd')<='$tgl2'".$param_brg.$param_group;

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        $datadtl = $this->m_stockreport->q_stk_pergudang_position($param_gdg);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'param_gdg' => $param_gdg,
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    function kstockgdg()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_ktstockgdg',$data);
    }

    function show_kstockgdg(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');
        $idgroup = $this->request->getPost('idgroup');
        $formheader = $this->request->getPost('formheader');

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idarea = $this->fiky_encryption->sealed($idarea);
        $enc_idgroup = $this->fiky_encryption->sealed($idgroup);


        $title = " Report Analisa Stock On Warehouse";

        $datajson =  base_url("stock/report/api_kstockgdg/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;

        if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_kstockgdg.mrt") ;
        } else {
            $datamrt =  base_url("assets/mrt/report_kstockgdg_non_header.mrt") ;
        }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_kstockgdg(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($idbrg) or $idbrg==='') {
            $param_brg = "";
        } else {
            $param_brg = " and idbarang='$idbrg'";
        }
        //idarea
//        if (!empty($idarea)) {
//            $param_area=" and idarea='$idarea'";
//        } else {  $param_area=""; }

        //idgroup
        if (!empty($idgroup)) {
            $param_group=" where c.idgroup='$idgroup'";
        } else {  $param_group=""; }

        /*
        if (!empty($idarea)) {
            $i=0;
            foreach ($idarea as $dd) {
                if ($i == 0){
                    $idareapx.=" idarea='$dd'";
                } else { $idareapx.=" or idarea='$dd'"; }
                $i++;
            }
            $param_area=" and (".$idareapx.")";
        } else {  $param_area=""; }
        */
        $param_gdg = " and idlocation='$idlocation' and to_char(trxdate,'yyyy-mm-dd') between '$tgl1' and '$tgl2'".$param_brg;

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        //$datadtl = $this->m_stockreport->q_kstockgdg_2($param_gdg,$param_group);
        $datadtl = $this->m_stockreport->q_ktstockgdg_3($param_gdg,$param_group,$tgl1,$tgl2,$param);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'idgroup' => $param_group,
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    function lbmbarang()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.4'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_lbm',$data);
    }

    function show_lbmbarang(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idarea = $this->fiky_encryption->sealed($idarea);

        $title = " Laporan Bukti Barang Masuk";

        $datajson =  base_url("stock/report/api_lbmbarang/?enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation") ;
        $datamrt =  base_url("assets/mrt/report_lbm.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_lbmbarang(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        /*
        if (!empty($idarea)) {
            $i=0;
            foreach ($idarea as $dd) {
                if ($i == 0){
                    $idareapx.=" idarea='$dd'";
                } else { $idareapx.=" or idarea='$dd'"; }
                $i++;
            }
            $param_area=" and (".$idareapx.")";
        } else {  $param_area=""; }
        */
        $param_gdg = " and idlocation='$idlocation' and to_char(lasttrxdate,'yyyy-mm-dd') between '$tgl1' and '$tgl2'";

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        $datadtl = $this->m_stockreport->q_lbm($param_gdg);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    function lbkbarang()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.5'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_lbk',$data);
    }

    function show_lbkbarang(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idarea = $this->fiky_encryption->sealed($idarea);

        $title = " Laporan Bukti Barang Keluar";

        $datajson =  base_url("stock/report/api_lbkbarang/?enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation") ;
        $datamrt =  base_url("assets/mrt/report_lbk.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_lbkbarang(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        /*
        if (!empty($idarea)) {
            $i=0;
            foreach ($idarea as $dd) {
                if ($i == 0){
                    $idareapx.=" idarea='$dd'";
                } else { $idareapx.=" or idarea='$dd'"; }
                $i++;
            }
            $param_area=" and (".$idareapx.")";
        } else {  $param_area=""; }
        */
        $param_gdg = " and idlocation='$idlocation' and to_char(lasttrxdate,'yyyy-mm-dd') between '$tgl1' and '$tgl2'";

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        $datadtl = $this->m_stockreport->q_lbk($param_gdg);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    function po_peritem(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.6'; $versirelease='I.R.A.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.6'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_po_peritem',$data);
    }

    function show_po_peritem(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idarea = $this->fiky_encryption->sealed($idarea);

        $title = " Laporan Kedatangan PO Per Item";

        $datajson =  base_url("stock/report/api_po_peritem/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate") ;
        $datamrt =  base_url("assets/mrt/report_po_perbarang.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_po_peritem(){
        $nama = trim($this->session->get('nama'));
        $idlocation = trim($this->session->get('loccode'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($idbrg) or $idbrg==='') {
            $param_brg = "";
        } else {
            $param_brg = " and idbarang='$idbrg'";
        }

        /*
        if (!empty($idarea)) {
            $i=0;
            foreach ($idarea as $dd) {
                if ($i == 0){
                    $idareapx.=" idarea='$dd'";
                } else { $idareapx.=" or idarea='$dd'"; }
                $i++;
            }
            $param_area=" and (".$idareapx.")";
        } else {  $param_area=""; }
        */
        $param_gdg = "  and to_char(trxdate,'yyyy-mm-dd') between '$tgl1' and '$tgl2'".$param_brg;

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        $datadtl = $this->m_stockreport->q_po_peritem($param_gdg);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    /* STOCK OPNAME */
    function laporan_so()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.7'; $versirelease='I.R.A.7/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.7'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/report/v_laporan_stock_opname',$data);
    }

    function show_laporan_so(){
        $nama = trim($this->session->get('nama'));
        $idbarang = $this->request->getPost('idbarang');
        $docdate = $this->request->getPost('docdate');
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');
        $idgroup = $this->request->getPost('idgroup');
        $formheader = $this->request->getPost('formheader');

        $enc_idbarang = $this->fiky_encryption->sealed($idbarang);
        $enc_docdate= $this->fiky_encryption->sealed($docdate);
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $enc_idarea = $this->fiky_encryption->sealed($idarea);
        $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Laporan Stock Opname";

        $datajson =  base_url("stock/report/api_laporan_so/?enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idarea=$enc_idarea&enc_idgroup=$enc_idgroup") ;
        if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_stock_opname.mrt") ;
        } else {
            $datamrt =  base_url("assets/mrt/report_stock_opname_non_header.mrt") ;
        }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_laporan_so(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idbrg=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idbarang')));
        $docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $idarea=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idarea')));
        $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $ddate = explode(' - ',$docdate);
        $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        $tgl2 = date('Y-m-d',strtotime($ddate[1]));


        //idarea
        if (!empty($idarea)) {
            $param_area=" and idarea='$idarea'";
        } else {  $param_area=""; }

        //idgroup
        if (!empty($idgroup)) {
            $param_group=" and idgroup='$idgroup'";
        } else {  $param_group=""; }
        /*
        if (!empty($idarea)) {
            $i=0;
            foreach ($idarea as $dd) {
                if ($i == 0){
                    $idareapx.=" idarea='$dd'";
                } else { $idareapx.=" or idarea='$dd'"; }
                $i++;
            }
            $param_area=" and (".$idareapx.")";
        } else {  $param_area=""; }
        */

        $param_gdg = " and idlocation='$idlocation' and to_char(trxdate,'yyyy-mm-dd') between '$tgl1' and '$tgl2'".$param_area.$param_group;

        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_mlocation($param);
        //$datadtl = $this->m_stockreport->q_stock_opname($param_gdg);
        $datadtl = $this->m_stockreport->q_stock_opname_checklist_print($param_gdg);

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    'date1' => date('d-m-Y',strtotime($tgl1)),
                    'date2' => date('d-m-Y',strtotime($tgl2)),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }

}
