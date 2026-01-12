<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Mutpromot extends BaseController
{
    function index(){
        $data['title']="MODUL MUTASI KARYAWAN";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nik'));
        $kodemenu='I.T.C.4'; $versirelease='I.T.C.4/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;

        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.C.4'";
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
        $data['showUnfinish'] = '' ;
        /*
        if ($dtl->getNumRows()>0) {
            if (trim($dtl->getRowArray()['status'])==='I') {
                $title = "PERINGATAN !!!";
                $urlclear = base_url('trans/mutpromot/clearEntry');
                $urlnext = base_url('trans/mutpromot/input');
                $body = " Ada Entry Data Yang Belum Terselesaikan";
                $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
            } else if (trim($dtl->getRowArray()['status'])==='E') {
                $title = "PERINGATAN !!!";
                $urlclear = base_url('trans/mutpromot/clearEntry');
                $urlnext = base_url('trans/mutpromot/edit');
                $body = " Ada Entry Data Yang Belum Terselesaikan";
                $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
            }
        } else { $data['showUnfinish'] = '' ; }
        */
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('trans/mutpromot/v_mutpromot',$data);

    }

    function list_his_mutpromot(){
        //AHC ADMIN HC // ML SUPER ADMIN IRGA
        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $list = $this->m_mutpromot->get_t_hismutpromot_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            $row[] = $no;
            //if ((trim($lm->status)==='I' and $role !== 'AHC') or (trim($lm->status)==='I' and $role !== 'ML')) {
            if (trim($lm->status)==='A' ) {
                $row[] = '
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
        <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
        <li role="presentation"><a role="menuitem" title="Detail" href="#" onclick="detailMutasi(' . "'" . trim($lm->docno) . "'" . ');"><i class="fa fa-bars"></i> Detail </a></li>
        <li role="presentation"><a role="menuitem" title="Ubah Data Mutasi" onclick="editMutasi(' . "'" . trim($lm->docno) . "'" . ');"><i class="fa fa-gear"></i> Update Data </a></li>
        <li role="presentation"><a role="menuitem" href="' . site_url('trans/mutpromot/cancel') . '/' . $enc_docno . '"  onclick="return confirm(' . "'Apakah Anda Yakin Cancel Ini?  " . trim($lm->docno) . "'" . ')"><i class="fa fa-close"></i> Cancel </a></li>
    </ul>
</div>
               ';
            } else {
                $row[] = '
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
        <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
        <li role="presentation"><a role="menuitem" title="Detail" href="#" onclick="detailMutasi(' . "'" . trim($lm->docno) . "'" . ');"><i class="fa fa-bars"></i> Detail </a></li>
        <li role="presentation"><a role="menuitem" title="Reprint" target="_blank" href='."'". base_url('trans/mutpromot/cetak').'/'.'?enc_docno='.$this->fiky_encryption->sealed(trim($lm->docno))."'".'><i class="fa fa-print"></i> Reprint </a></li>
        <li role="presentation"><a role="menuitem" title="Print SK Input" onclick="beforePrint(' . "'" . trim($lm->docno) . "'" . ');" ><i class="fa fa-print"></i> Print & SK Update </a></li>
    </ul>
</div>
               ';
            }

            $row[] = $lm->docno;
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->docdate;
            $row[] = $lm->doctype;
            if (trim($lm->status)==='A'){
                $row[] = '<span class="label label-primary" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->status)==='P') {
                $row[] = '<span class="label label-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->status)==='C') {
                $row[] = '<span class="label label-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="label label-default" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->nmnewdept;
            $row[] = $lm->nmolddept;
            $row[] = $lm->nosk;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_mutpromot->t_hismutpromot_view_count_all(),
            "recordsFiltered" => $this->m_mutpromot->t_hismutpromot_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    /* LIST KARYAWAN */
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
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and nik='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and nik='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.T.C.4';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_mutpromot->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        if ($role_access_filter==='t') {
            //$people = array("100061", "5301");
            //if (in_array($niksession, $people)) {
            //$paramfilter = " and trim(id_dept)='".trim($dtlkary['id_dept'])."' ";
            $paramfilter = "";
        } else {
            $paramfilter = "";
        }

        $param="  and ( coalesce(resign,'')!='YES' and nmlengkap like '%$search%' $paramglobal1 $paramglobal2 $paramfilter ) or ( coalesce(statuskepegawaian,'')!='KO' and nik like '%$search%'  $paramglobal1 $paramglobal2 $paramfilter ) order by nmlengkap asc";
        //$param="";
        // $result = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $result = $this->m_global->q_lv_m_karyawan($param)->getResult();
        $count = $this->m_global->q_lv_m_karyawan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $result,
                'incomplete_results' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function input(){
        $data['title']="INPUT MUTASI & PROMOSI";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nik'));
        $kodemenu='I.T.C.4'; $versirelease='I.T.C.4/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.T.C.4'";
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
        $data['type']="INPUT";
        $data['nama']=$nama;
        $data['docno'] ='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('trans/mutpromot/v_input_mutpromot',$data);
    }

    function editMutasi(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.C.4'; $versirelease='I.T.C.4/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';

        $data['title'] = "Edit Mutasi Karyawan";
        $data['type']="EDIT";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        return $this->template->render('trans/mutpromot/v_input_mutpromot',$data);
    }

    function detailMutasi(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.C.4'; $versirelease='I.T.C.4/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';

        $data['title'] = "Detail Mutasi Karyawan";
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        return $this->template->render('trans/mutpromot/v_input_mutpromot',$data);
    }

    function showOn(){
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_mutpromot->read_his_mutasipromosi($param);
        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_results' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveEntry(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        $builder = $this->db->table('sc_his.mutasipromosi');

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO'){
            $dataprocess = $data->body;
            $docno = trim($dataprocess->nik);
            $type = trim($dataprocess->type);

            $nik = strtoupper(trim($dataprocess->nik));
            $evaluation = strtoupper(trim($dataprocess->evaluation));
            $sdate = trim($dataprocess->startdate);
            $tgl=explode(' - ',$sdate);
            $startdate= date('Y-m-d',strtotime($tgl[0]));
            $enddate= date('Y-m-d',strtotime($tgl[1]));

            $doctype = strtoupper(trim($dataprocess->doctype));
            $kdregu = strtoupper(trim($dataprocess->kdregu));
            $description = strtoupper(trim($dataprocess->description));
            $newdept = strtoupper(trim($dataprocess->newdept));
            $newsubdept = strtoupper(trim($dataprocess->newsubdept));
            $newjabatan = strtoupper(trim($dataprocess->newjabatan));
            $newlvljabatan = strtoupper(trim($dataprocess->newlvljabatan));
            $newplant = strtoupper(trim($dataprocess->newplant));

            if ($type==='INPUT') {
                $info = array(
                    'docno' => $nama,
                    'nik' => $nik,
                    'docdate' => date('Y-m-d H:i:s'),
                    'docname' => 'MUTASI',
                    'evaluation' => $evaluation,
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'doctype' => $doctype,
                    'kdregu' => $kdregu,
                    'description' => $description,
                    'newdept' => $newdept,
                    'newsubdept' => $newsubdept,
                    'newjabatan' => $newjabatan,
                    'newlvljabatan' => $newlvljabatan,
                    'newplant' => $newplant,
                    'holdby' => '',
                    'holdrow' => 'NO',
                    'status' => 'I',
                    'inputby' => $nama,
                    'inputdate' => date('Y-m-d H:i:s'),

                );
                if ($builder->insert($info)) {

                    $paramerror=" and userid='$nama' and modul='I.T.C.4'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();

                    $result = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.$dtlerror['nomorakhir1']);
                    echo json_encode($result);
                } else {
                    $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($result);
                }
            } else if ($type==='EDIT') {
                $info = array(
                    'nik' => $nik,
                    'docdate' => date('Y-m-d H:i:s'),
                    'evaluation' => $evaluation,
                    'startdate' => $startdate,
                    'enddate' => $enddate,
                    'doctype' => $doctype,
                    'kdregu' => $kdregu,
                    'description' => $description,
                    'newdept' => $newdept,
                    'newsubdept' => $newsubdept,
                    'newjabatan' => $newjabatan,
                    'newlvljabatan' => $newlvljabatan,
                    'newplant' => $newplant,
                    'updateby' => $nama,
                    'updatedate' => date('Y-m-d H:i:s'),
                );
                $builder->where('docno',$docno);
                if ($builder->update($info)) {

                    $paramerror=" and userid='$nama' and modul='I.T.C.4'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();

                    $result = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.$dtlerror['nomorakhir1']);
                    echo json_encode($result);
                } else {
                    $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($result);
                }
            }


        } else {
            $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($result);
        }
    }

    function cancel(){
        $enc_docno = $this->uri->segment(4);
        $docno = $this->fiky_encryption->unseal($enc_docno);
        $nama = trim($this->session->get('nama'));

        $info = array(
            'cancelby' => $nama,
            'canceldate' => date('Y-m-d H:i:s'),
            'status' => 'C'
        );
        $this->db->where('docno',$docno);
        $this->db->update('sc_his.mutasipromosi',$info);


        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.C.4');
        $this->db->delete('sc_mst.trxerror');
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $docno,
            'nomorakhir2' => '',
            'modul' => 'I.T.C.5',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/mutpromot'));
    }


    function beforeCetak(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $docno = trim($dataprocess->docno);
            $nosk = trim($dataprocess->nosk);
            $dtlcc1 = $this->m_mutpromot->q_jabatan_position(" and kdjabatan='".trim($dataprocess->cc1)."'")->getRowArray();
            $dtlcc2 = $this->m_mutpromot->q_jabatan_position(" and kdjabatan='".trim($dataprocess->cc2)."'")->getRowArray();
            $dtlcc3 = $this->m_mutpromot->q_jabatan_position(" and kdjabatan='".trim($dataprocess->cc3)."'")->getRowArray();
            $dtlcc4 = $this->m_mutpromot->q_jabatan_position(" and kdjabatan='".trim($dataprocess->cc4)."'")->getRowArray();
            $cc1 = trim($dtlcc1['nmjabatan']);
            $cc2 = trim($dtlcc2['nmjabatan']);
            $cc3 = trim($dtlcc3['nmjabatan']);
            $cc4 = trim($dtlcc4['nmjabatan']);

            $info = array(
                'nosk' => strtoupper($nosk),
                'cc1' => $cc1,
                'cc2' => $cc2,
                'cc3' => $cc3,
                'cc4' => $cc4,
            );
            $this->db->where('docno',$docno);
            $this->db->update("sc_his.mutasipromosi",$info);

            $result = array('status' => true, 'messages' => 'Data Sukses Di Proses','enc_docno' => trim($this->fiky_encryption->sealed($docno)));
            echo json_encode($result);
        } else {
            $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
            echo json_encode($result);
        }
    }

    function cetak(){
        $nama = trim($this->session->get('nama'));
        $enc_docno = $this->request->getGet('enc_docno');
        $docno=trim($this->fiky_encryption->unseal($enc_docno));

        $title = " Form Cetak Surat Mutasi & Promosi ";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_mutpromot->read_his_mutasipromosi($param);


        $datajson =  base_url("trans/mutpromot/api_cetak/?enc_docno=$enc_docno") ;
        if (trim($dtl->getRowArray()['doctype'])==='MUTASI') {
            $datamrt =  base_url("assets/mrt/rpt_mutasi.mrt") ;
        } else {
            $datamrt =  base_url("assets/mrt/rpt_mutasi.mrt") ;
        }


        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_cetak(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docno')));
        $data['title']='SURAT KEPUTUSAN MUTASI';

        $datamst = $this->m_global->q_master_branch()->getResult();
        $param=" and docno='$docno'";
        $dtl = $this->m_mutpromot->read_his_mutasipromosi($param);
        header("Content-Type: text/json");
        return json_encode(
            array(
                'master' => $datamst,
                'detail' => $dtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    /* RANAH PERSETUJUAN MUTASI */
    function persetujuanmutasi(){
        $data['title']="MODUL PERSETUJUAN MUTASI KARYAWAN";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $niksession=trim($this->session->get('nik'));
        $kodemenu='I.T.C.5'; $versirelease='I.T.C.5/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.T.C.5'";
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
        $data['showUnfinish'] = '' ;
        /* role akses */
        $kmenu=$kodemenu;
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $dtlkary = $this->m_role->q_karyawan(" and trim(nik)='$niksession'")->getRowArray();
        $data['deptsession'] = trim($dtlkary['id_dept']);
        $data['role'] = $role;
        return $this->template->render('trans/mutpromot/v_persetujuan_mutpromot',$data);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

    }


    function list_persetujuanmutasi(){
        //AHC ADMIN HC // ML SUPER ADMIN IRGA
        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $list = $this->m_mutpromot->get_t_hismutpromot_approval_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            $row[] = $no;
            //if ((trim($lm->status)==='I' and $role !== 'AHC') or (trim($lm->status)==='I' and $role !== 'ML')) {
            $row[] = '
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
        <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
        <li role="presentation"><a role="menuitem" title="Detail" href="#" onclick="detailMutasi('."'".trim($lm->docno)."'".');"><i class="fa fa-bars"></i> Detail </a></li>
        <li role="presentation"><a role="menuitem" href="'.site_url('trans/mutpromot/approveDept').'/'.$enc_docno.'"  onclick="return confirm('."'Apakah Anda Yakin Setujui Ini?  ".trim($lm->docno)."'".')"><i class="fa fa-check-square-o"></i> Setujui </a></li>
        <li role="presentation"><a role="menuitem" href="'.site_url('trans/mutpromot/tolakDept').'/'.$enc_docno.'"  onclick="return confirm('."'Apakah Anda Yakin Cancel Ini?  ".trim($lm->docno)."'".')"><i class="fa fa-close"></i> Tolak </a></li>
    </ul>
</div>
               ';

            $row[] = $lm->docno;
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->docdate;
            $row[] = $lm->doctype;
            if (trim($lm->status)==='A'){
                $row[] = '<span class="label label-primary" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->status)==='P') {
                $row[] = '<span class="label label-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if (trim($lm->status)==='C') {
                $row[] = '<span class="label label-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="label label-default" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->nmnewdept;
            $row[] = $lm->nmolddept;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_mutpromot->t_hismutpromot_approval_view_count_all(),
            "recordsFiltered" => $this->m_mutpromot->t_hismutpromot_approval_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function approveDept(){
        $enc_docno = $this->uri->segment(4);
        $docno = $this->fiky_encryption->unseal($enc_docno);
        $nama = trim($this->session->get('nama'));

        $paramx = " and trim(docno)='$docno'";
        $cek = $this->m_mutpromot->read_his_mutasipromosi($paramx)->getRowArray();

        if(trim($cek['status'])==='A') {
            $info = array(
                'approveby' => $nama,
                'approvedate' => date('Y-m-d H:i:s'),
                'status' => 'A1'
            );
        } else if (trim($cek['status'])==='A1') {
            $info = array(
                'approveby' => $nama,
                'approvedate' => date('Y-m-d H:i:s'),
                'status' => 'A2'
            );
        }

        $this->db->where('docno',$docno);
        $this->db->update('sc_his.mutasipromosi',$info);


        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.C.5');
        $this->db->delete('sc_mst.trxerror');
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $docno,
            'nomorakhir2' => '',
            'modul' => 'I.T.C.5',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/mutpromot/persetujuanmutasi'));
    }

    function tolakDept(){
        $enc_docno = $this->uri->segment(4);
        $docno = $this->fiky_encryption->unseal($enc_docno);
        $nama = trim($this->session->get('nama'));

        $info = array(
            'cancelby' => $nama,
            'canceldate' => date('Y-m-d H:i:s'),
            'status' => 'C'
        );
        $this->db->where('docno',$docno);
        $this->db->update('sc_his.mutasipromosi',$info);


        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.C.5');
        $this->db->delete('sc_mst.trxerror');
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $docno,
            'nomorakhir2' => '',
            'modul' => 'I.T.C.5',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/mutpromot/persetujuanmutasi'));
    }
}
