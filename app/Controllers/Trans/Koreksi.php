<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Koreksi extends BaseController
{
    function index(){
        $data['title']="INPUT KOREKSI";
        return $this->template->render('trans/koreksi/v_koreksi',$data);
    }

    function koreksicuti()
    {
        $data['title']="Koreksi & Penyesuaian Data Cuti Karyawan";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.14'; $versirelease='I.T.B.14/ALPHA.001'; $releasedate=date('2019-04-12 00:00:00');
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

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kodemenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_role->list_karyawan_param(" and nik='$niksession'")->getRowArray();

        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_koreksi->q_koreksicuti_tmp_mst($param);
        if ($dtl->getNumRows()>0) {
            if (trim($dtl->getRowArray()['status'])==='I' or trim($dtl->getRowArray()['status'])==='') {
                $title = "PERINGATAN !!!";
                $urlclear = base_url('trans/koreksi/clearEntry_Koreksi_Cuti');
                $urlnext = base_url('trans/koreksi/input_koreksicuti');
                $body = " Ada Entry Data Yang Belum Terselesaikan";
                $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
            } else if (trim($dtl->getRowArray()['status'])==='E') {
                $title = "PERINGATAN !!!";
                $urlclear = base_url('trans/koreksi/clearEntry_Koreksi_Cuti');
                $urlnext = base_url('trans/koreksi/input_koreksicuti');
                $body = " Ada Entry Data Yang Belum Terselesaikan";
                $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
            }
        } else { $data['showUnfinish'] = '' ; }

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('trans/koreksi/v_koreksicuti',$data);
    }

    function list_koreksi_cuti(){
        $list = $this->m_koreksi->get_koreksi_cuti_mst_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            $row[] = $no;
            //$row[] = trim($lm->id);
            //if ((trim($lm->status)==='I' and $role !== 'AHC') or (trim($lm->status)==='I' and $role !== 'ML')) {
            $row[] = '
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
        <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
        <li role="presentation"><a role="menuitem" title="Detail" href="#" onclick="detail_koreksi_cuti('."'".trim($lm->docno)."'".');"><i class="fa fa-gear"></i> Detail</a></li>
        <li role="presentation"><a role="menuitem" title="Batalkan" href="#" onclick="cancel_koreksi_cuti('."'".trim($lm->docno)."'".');"><i class="fa fa-trash"></i> Batalkan</a></li>
    </ul>
</div>
               ';
            $row[] = $lm->docno;
            $row[] = $lm->docref;
            $row[] = $lm->docdate1;
            $row[] = $lm->doctype1;
            $row[] = $lm->description;
            if (trim($lm->status) === 'P') {
                $row[] = '<td><span class="label label-success">' . $lm->nmstatus . '</span></td>';
            } else if (trim($lm->status) === 'C') {
                $row[] = '<td><span class="label label-danger">' . $lm->nmstatus . '</span></td>';
            }

            $row[] = $lm->inputby;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_koreksi->koreksi_cuti_mst_view_count_all(),
            "recordsFiltered" => $this->m_koreksi->koreksi_cuti_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function input_koreksicuti(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.14'; $versirelease='I.T.B.14/ALPHA.001'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.T.B.14'";
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

        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_koreksi->q_koreksicuti_tmp_mst($param);
        $val = $dtl->getRowArray();
        $data['dtl']=$val;
        if (trim($val['status'])==='E') {
            $data['type']="EDIT";
            $data['title']=$data['type']." EDIT KOREKSI CUTI";
        } else {
            $data['type']="INPUT";
            $data['title']=$data['type']." INPUT KOREKSI CUTI";
        }
        $cek = $dtl->getNumRows();
        if (! empty($nama) and $cek<=0) {
            $build = $this->db->table('sc_tmp.koreksi_cuti_mst');
            $info = array(
                'branch' => $branch,
                'docno' => $nama,
                'docdate' =>  date('Y-m-d'),
                'status' => '',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $build->insert($info);
        }else if (! empty($nama) and $dtl>0) {
            //lanjutkan
        } else {
            return redirect()->to(base_url('trans/koreksi/koreksicuti'));
        }
        return $this->template->render('trans/koreksi/v_input_koreksicuti',$data);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
    }

    function saveKoreksiCutiTmpMst(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        $builder = $this->db->table('sc_tmp.koreksi_cuti_mst');
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $docref = strtoupper(trim($dataprocess->docref));
            $docdate = date('Y-m-d',strtotime($dataprocess->docdate));
            $doctype = trim($dataprocess->doctype);
            $qty = trim($dataprocess->qty);
            //$daterange = $dataprocess->daterange;
            //$tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);

            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $info = array(
                    'docref' => $docref,
                    'docdate' => $docdate,
                    'doctype' => $doctype,
                    'description' => $description,
                    'qty' => $qty,
                    'status' => 'I',
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );
                $builder->where('docno', $nama);
                if ($builder->update($info)) {
                    $result = array('status' => true, 'messages' => 'Data processing');
                    echo json_encode($result);
                } else {
                    $result = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($result);
                }

            } else if ($type==='UPDATE') {


            } else if ($type==='DELETE') {

            }


        } else {
            $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($result);
        }
    }

    function saveKoreksiCutiTmpDtl(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        $builder = $this->db->table('sc_tmp.koreksi_cuti_dtl');
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $dtlmst = $this->m_koreksi->q_koreksicuti_tmp_mst(" and docno='".$nama."'")->getRowArray();
            $dtlitem = $this->m_global->q_lv_m_karyawan("  and nik='".trim($dataprocess->nik."'"))->getRowArray();

            $type = trim($dataprocess->type);
            $docno = $nama;
            $nik = trim($dtlitem['nik']);
            $branch = trim($dtlmst['branch']);
            $docdate = trim($dtlmst['docdate']);
            $docref = trim($dtlmst['docref']);
            $doctype = trim($dtlmst['doctype']);
            $qty = trim($dtlmst['qty']);
            $description = trim($dtlmst['description']);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            // CHECK INPUT TYPE
            if ($type==='ADDING_ITEM') {
                $info = array(
                    'id' => 0,
                    'branch' => $branch,
                    'docno' => $nama,
                    'nik' => $nik,
                    'docref' => $docref,
                    'doctype' => $doctype,
                    'qty' => $qty,
                    'docdate' => $docdate,
                    'status' => 'I',
                    'description' => $description,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );
                if ($builder->insert($info)) {
                    $result = array('status' => true, 'messages' => 'Data processing');
                    echo json_encode($result);
                } else {
                    $result = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($result);
                }

            } else if ($type==='UPDATE_ITEM') {
                //none to update
                $result = array('status' => false, 'messages' => 'Data Fail, Try Again');
                echo json_encode($result);
            } else if ($type==='DELETE_ITEM') {
                $builder = $this->db->table('sc_tmp.koreksi_cuti_dtl');
                $builder->where('id',trim($dataprocess->id));
                $builder->where('docno',$nama);
                if ($builder->delete()) {
                    $result = array('status' => true, 'messages' => 'Data processing');
                    echo json_encode($result);
                } else {
                    $result = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($result);
                }
            }


        } else {
            $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($result);
        }
    }


    function showOn_Koreksi_Cuti(){
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_koreksi->q_koreksicuti_tmp_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_results' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showOn_Koreksi_Cuti_Trx(){
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_koreksi->q_koreksicuti_trx_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_results' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function clearEntry_Koreksi_Cuti(){
        $nama = trim($this->session->get('nama'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_koreksi->q_koreksicuti_tmp_mst($param);
        $status = trim($dtl->getRowArray()['status']);
        $docnotmp = trim($dtl->getRowArray()['docnotmp']);
        if ($status==='I' or $status===''){
            $builder_mst = $this->db->table('sc_tmp.koreksi_cuti_mst');
            $builder_dtl = $this->db->table('sc_tmp.koreksi_cuti_dtl');
            $builder_mst->where('docno',$nama);
            if ($builder_mst->delete()) {
                $builder_dtl->where('docno',$nama);
                $builder_dtl->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('trans/koreksi/koreksicuti'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else if ($status==='E'){
            $this->db->query("update sc_tmp.koreksi_cuti_mst set status='C' where docno='$nama'");
            $info = array(
                'status' => 'A',
            );
            $builder_tx_mst = $this->db->table('sc_trx.koreksi_cuti_mst');
            $builder_tx_mst->where('docno',$docnotmp);
            if ($builder_tx_mst->update($info)) {
                $this->db->query("delete from sc_tmp.koreksi_cuti_mst where docno='$nama'");
                $this->db->query("delete from sc_tmp.koreksi_cuti_dtl where docno='$nama'");
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('trans/koreksi/koreksicuti'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        }
    }

    function list_tmp_koreksi_cuti_dtl(){
        $list = $this->m_koreksi->get_t_tmp_koreksi_cuti_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            //$row[] = $no;
            $row[] = trim($lm->id);
            //if ((trim($lm->status)==='I' and $role !== 'AHC') or (trim($lm->status)==='I' and $role !== 'ML')) {
            $row[] = '
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
        <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
        <!--li role="presentation"><a role="menuitem" title="Ubah Detail" href="#" onclick="update_itemtrans_dtl('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Ubah Detail</a></li-->
        <li role="presentation"><a role="menuitem" title="Batalkan Input" href="#" onclick="delete_itemtrans_dtl('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Hapus </a></li>
    </ul>
</div>
               ';
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->nmsubdept;
            $row[] = $lm->nmjabatan;
            $row[] = $lm->doctype1;
            $row[] = $lm->qty;
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_koreksi->t_tmp_koreksi_cuti_dtl_view_count_all(),
            "recordsFiltered" => $this->m_koreksi->t_tmp_koreksi_cuti_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_koreksi_cuti_dtl(){
        $list = $this->m_koreksi->get_t_trx_koreksi_cuti_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $enc_docno = $this->fiky_encryption->sealed(trim($lm->docno));
            $row = array();
            //$row[] = $no;
            $row[] = trim($lm->id);
            //if ((trim($lm->status)==='I' and $role !== 'AHC') or (trim($lm->status)==='I' and $role !== 'ML')) {
            $row[] = '
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
        <span class="caret"></span></button>
</div>';
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->nmsubdept;
            $row[] = $lm->nmjabatan;
            $row[] = $lm->doctype1;
            $row[] = $lm->qty;
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_koreksi->t_trx_koreksi_cuti_dtl_view_count_all(),
            "recordsFiltered" => $this->m_koreksi->t_trx_koreksi_cuti_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }




    ////

    function showing_koreksi_cuti_dtl($id){
        $data = $this->m_koreksi->get_t_tmp_itemtrans_dtl_view_by_id($id);
        echo json_encode($data);
    }

    function finishInputKoreksiCuti() {
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $countzero = $this->m_koreksi->q_koreksicuti_tmp_dtl(" and qty=0 and docno='".$nama."'")->getNumRows();
            if ($countzero > 0){
                $result = array('status' => false, 'messages' => 'Jumlah Detail Tidak Boleh 0/Kosong');
                echo json_encode($result);
            } else {
                $infomst = array(
                    'status' => 'F'
                );
                $build = $this->db->table('sc_tmp.koreksi_cuti_mst');
                $build->where('docno',$nama);
                $build->update($infomst);


                $paramerror=" and userid='$nama' and modul='I.T.B.14'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $result = array('status' => true, 'messages' => 'Data Sukses Di Proses,Nomor '.$dtlerror['nomorakhir1']);
                echo json_encode($result);
            }
        } else {
            $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($result);
        }
    }
    function cancelKoreksiCuti(){
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno'));
        $info = array(
            'status' => 'C',
            'updatedate' => date('Y-m-d H:i:s'),
            'updateby' => $nama,
            'docnotmp' => $docno,
        );
        $build = $this->db->table('sc_trx.koreksi_cuti_mst');
        $build->where('docno', $docno);
        $build->update($info);
        return redirect()->to(base_url('trans/koreksi/koreksicuti'));
    }

    function detailKoreksiCuti(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.14'; $versirelease='I.T.B.14/ALPHA.001'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';

        $data['title'] = "Detail Koreksi Cuti";
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        return $this->template->render('trans/koreksi/v_input_koreksicuti',$data);
    }


    function kcutibersama(){
        $data['title']="KOREKSI CUTI BERSAMA KARYAWAN";

        if($this->uri->segment(4)=="kode_failed")
            $data['message']="<div class='alert alert-warning'>No Dokumen Sudah Di Approve Atau Dibatalkan</div>";
        else if($this->uri->segment(4)=="rep_succes"){
            $nik=$this->uri->segment(5);
            $data['message']="<div class='alert alert-success'>Cuti NIK : <b>$nik</b> Sukses Disimpan </div>";
        }
        else if($this->uri->segment(4)=="del_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-success'>Dokumen Dengan Nomor <b>$nodok</b> Sukses Dihapus </div>";
        }
        else if($this->uri->segment(4)=="app_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-success'>Dokumen Dengan Nomor <b>$nodok</b> Sukses Di Approval</div>";
        }
        else if($this->uri->segment(4)=="cancel_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-danger'>Dokumen Dengan Nomor <b>$nodok</b> Telah Dibatalkan</div>";
        }
        else if($this->uri->segment(4)=="edit_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-danger'>Data dengan Nomor: <b>$nodok</b> Berhasil Diubah</div>";
        }
        else if($this->uri->segment(4)=="nohakcuti")
            $data['message']="<div class='alert alert-warning'>Anda Tidak Memilik Hak Cuti</div>";
        else
            $data['message']='';
        $data['list_kcb']=$this->m_koreksi->list_kcb()->getResult();
        return $this->template->render('trans/koreksi/v_koreksicutibersama',$data);
    }

    function inputkcb(){
        $data['title']="INPUT KOREKSI";
        $data['list_karyawan']=$this->m_koreksi->list_karyawan()->getResult();
        $data['listcb']=$this->m_koreksi->list_cb()->getResult();
        $data['listcb2']=$this->m_koreksi->list_cb()->getRowArray();
        return $this->template->render('trans/koreksi/v_inputkcbersama',$data);
    }

    function savekcb(){
        $nik=$this->input->post('nik');
        $nodok='';
        $status='I';
        $tgldok=$this->input->post('tgl_dok');
        $docref=$this->input->post('docref');
        $tgl_awal=$this->input->post('tgl_awal');
        $tgl_akhir=$this->input->post('tgl_akhir');
        $jumlahcuti=$this->input->post('jumlahcuti');
        $tglinput=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $doctype=$this->input->post('doctype');
        $keterangan=$this->input->post('keterangan');
        $info=array(
            'nodok'=>$nodok,
            'nik'=>$nik,
            'status'=>$status,
            'tgl_dok'=>$tgldok,
            'docref'=>$docref,
            'tgl_awal'=>$tgl_awal,
            'tgl_akhir'=>$tgl_akhir,
            'jumlahcuti'=>$jumlahcuti,
            'input_date'=>$tglinput,
            'input_by'=>$inputby,
            'doctype'=>$doctype,
            'keterangan'=>$keterangan

        );

        $this->db->insert('sc_tmp.koreksicb',$info);
        redirect("trans/koreksi/kcutibersama");

    }

    function hapuskcb(){
        $cek=$this->m_koreksi->cek_kcb($nodok=null)->getNumRows();

        if ($cek==0) {
            redirect("trans/cuti_karyawan/cutibersama/index/kode_failed");
        } else {

            $this->db->query("delete from sc_trx.kcutibersama where nodok='$nodok' and status='I'");
            redirect("trans/cuti_karyawan/cutibersama/del_succes/$nodok");
        }

    }

    //view otoritas
    function viewotokcb($nodok){
        $data['title']='SAVE FINAL KOREKSI';
        $data['listcb']=$this->m_koreksi->list_cb()->getResult();
        $data['list_karyawan']=$this->m_koreksi->list_karyawan()->getResult();
        $data['dtl']=$this->m_koreksi->cek_kcb($nodok)->getRowArray();
        return $this->template->render('trans/koreksi/v_otoinputkcbersama',$data);
    }



    //otoritas koresi
    function otokcb(){
        $nik=$this->input->post('nik');
        $nodok=$this->input->post('nodok');
        $status='P';
        $tgldok=$this->input->post('tgl_dok');
        $docref=$this->input->post('docref');
        $tgl_awal=$this->input->post('tgl_awal');
        $tgl_akhir=$this->input->post('tgl_akhir');
        $jumlahcuti=$this->input->post('jumlahcuti');
        $tglinput=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $keterangan=$this->input->post('keterangan');
        $info=array(
            //'nodok'=>$nodok,
            'nik'=>$nik,
            'status'=>$status,
            'tgl_dok'=>$tgldok,
            'docref'=>$docref,
            'tgl_awal'=>$tgl_awal,
            'tgl_akhir'=>$tgl_akhir,
            'jumlahcuti'=>$jumlahcuti,
            'input_date'=>$tglinput,
            'input_by'=>$inputby,
            'keterangan'=>$keterangan

        );
        $this->db->where('nodok',$nodok);
        $this->db->update('sc_trx.koreksicb',$info);
        redirect("trans/koreksi/kcutibersama/index/app_succes/$nodok");
        /*$cek=$this->m_koreksi->cek_kcb($nodok)->getRowArray();

            if (trim($cek['status'])=='I') {

                    $this->db->where('nodok',$nodok);
                    $this->db->update('sc_trx.koreksicb',$info);
                    redirect("trans/koreksi/kcutibersama/index/app_succes/$nodok");
            }
            else {
                    redirect("trans/koreksi/kcutibersama/kode_failed");
            }
            */
    }


    function koreksi_khusus(){
        $data['title']="KOREKSI CUTI KONDISI KHUSUS";

        if($this->uri->segment(4)=="kode_failed")
            $data['message']="<div class='alert alert-warning'>No Dokumen Sudah Di Approve Atau Dibatalkan</div>";
        else if($this->uri->segment(4)=="rep_succes"){
            $nik=$this->uri->segment(5);
            $data['message']="<div class='alert alert-success'>Cuti NIK : <b>$nik</b> Sukses Disimpan </div>";
        }
        else if($this->uri->segment(4)=="del_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-success'>Dokumen Dengan Nomor <b>$nodok</b> Sukses Dihapus </div>";
        }
        else if($this->uri->segment(4)=="app_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-success'>Dokumen Dengan Nomor <b>$nodok</b> Sukses Di Approval</div>";
        }
        else if($this->uri->segment(4)=="cancel_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-danger'>Dokumen Dengan Nomor <b>$nodok</b> Telah Dibatalkan</div>";
        }
        else if($this->uri->segment(4)=="edit_succes"){
            $nodok=$this->uri->segment(5);
            $data['message']="<div class='alert alert-danger'>Data dengan Nomor: <b>$nodok</b> Berhasil Diubah</div>";
        }
        else if($this->uri->segment(4)=="nohakcuti")
            $data['message']="<div class='alert alert-warning'>Anda Tidak Memilik Hak Cuti</div>";
        else
            $data['message']='';
        $data['list_kkstmp']=$this->m_koreksi->list_kkstmp()->getResult();
        $data['list_kksfinal']=$this->m_koreksi->list_kks()->getResult();

        return $this->template->render('trans/koreksi/v_koreksikondisikhusus',$data);
    }

    function edit_koreksick(){
        $data['title']='EDIT KOREKSI CUTI KONDISI KHUSUS';
        $nodok=$this->uri->segment(4);
        $nik=$this->uri->segment(5);
        $jumlahcuti=$this->uri->segment(6);
        $data['listcb']=$this->m_koreksi->list_cb()->getResult();
        $data['list_karyawan']=$this->m_koreksi->list_karyawan()->getResult();
        $data['dtl']=$this->m_koreksi->cek_kks($nodok,$nik,$jumlahcuti)->getRowArray();
        return $this->template->render('trans/koreksi/v_editkoreksikhusus',$data);
    }


    function koreksi_khususdtl(){

        $nik=$this->input->post('kdkaryawan');
        $tahun=$this->input->post('tahunlek');
        $nikht=$this->input->post('htgkry');
        $data['title']='List Balance';
        $data['listkaryawan']=$this->m_koreksi->list_karyawan_index_2()->getResult();
        $data['listblc']=$this->m_koreksi->q_cutiblcdtl($nik,$tahun)->getResult();
        $data['tahun']=$tahun;
        $this->m_cuti_karyawan->q_proc_htg($nikht);
        return $this->template->render('trans/koreksi/v_koreksikondisikhususdtl',$data);

    }
    function inputkoreksikhusus(){
        $data['title']="INPUT KOREKSI KONDISI KHUSUS";
        $data['list_karyawan']=$this->m_koreksi->list_karyawan()->getResult();
        $data['listcb']=$this->m_koreksi->list_cb()->getResult();
        $data['listcb2']=$this->m_koreksi->list_cb()->getRowArray();
        return $this->template->render('trans/koreksi/v_inputkoreksikhusus',$data);
    }

    function save_finalkck(){
        $nodok=$this->uri->segment(4);
        $nik=$this->uri->segment(5);
        $tgl_dok=str_replace('%20',' ',$this->uri->segment(6));
        $jumlahcuti=$this->uri->segment(7);



        $info=array(
            'status'=>'P',
        );
        $this->db->where('nodok',$nodok);
        $this->db->where('nik',$nik);
        $this->db->where('tgl_dok',$tgl_dok);
        $this->db->where('jumlahcuti',$jumlahcuti);
        $this->db->update('sc_tmp.koreksicb',$info);
        $this->db->query("delete from sc_tmp.koreksicb 
							where nodok='$nodok' and nik='$nik' and tgl_dok='$tgl_dok' and jumlahcuti='$jumlahcuti' and doctype='X' ");
        redirect("trans/koreksi/koreksi_khusus");

    }

    function save_inputckk(){
        $nik=$this->input->post('nik');
        $nodok=$this->input->post('nodoksmt');;
        $status='I';
        $tgldok=$this->input->post('tgl_dok');
        $docref=$this->input->post('docref');
        $tgl_awal=$this->input->post('tgl_awal');
        $tgl_akhir=$this->input->post('tgl_akhir');
        $jumlahcuti=$this->input->post('jumlahcuti');
        $tglinput=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $doctype=$this->input->post('doctype');
        $keterangan=$this->input->post('keterangan');


        $info=array(
            'nodok'=>$nodok,
            'nik'=>$nik,
            'status'=>$status,
            'tgl_dok'=>$tgldok,
            'docref'=>$docref,
            'tgl_awal'=>$tgl_awal,
            'tgl_akhir'=>$tgl_akhir,
            'jumlahcuti'=>$jumlahcuti,
            'input_date'=>$tglinput,
            'input_by'=>$inputby,
            'doctype'=>$doctype,
            'keterangan'=>$keterangan

        );

        $this->db->insert('sc_tmp.koreksicb',$info);
        redirect("trans/koreksi/koreksi_khusus");

    }

    function save_editckk(){
        $nik=$this->input->post('nik');
        $nodok=$this->input->post('nodoksmt');;
        //$status='I';
        $tgldok=$this->input->post('tgl_dok');
        $docref=$this->input->post('docref');
        $tgl_awal=$this->input->post('tgl_awal');
        $tgl_akhir=$this->input->post('tgl_akhir');
        $jumlahcuti=$this->input->post('jumlahcuti');
        $tglinput=$this->input->post('tgl');
        $inputby=$this->input->post('inputby');
        $doctype=$this->input->post('doctype');
        $keterangan=$this->input->post('keterangan');


        $info=array(
            'nodok'=>$nodok,
            'nik'=>$nik,
            //'status'=>$status,
            'tgl_dok'=>$tgldok,
            'docref'=>$docref,
            //'tgl_awal'=>$tgl_awal,
            //'tgl_akhir'=>$tgl_akhir,
            'jumlahcuti'=>$jumlahcuti,
            'input_date'=>$tglinput,
            'input_by'=>$inputby,
            'doctype'=>$doctype,
            'keterangan'=>$keterangan

        );
        $this->db->where('nodok',$nodok);
        $this->db->where('status','I');
        $this->db->where('jumlahcuti',$jumlahcuti);
        $this->db->update('sc_tmp.koreksicb',$info);
        redirect("trans/koreksi/koreksi_khusus");

    }

    function hps_kck(){
        $nodok=$this->uri->segment(4);
        $nik=$this->uri->segment(5);
        $tgl_dok=str_replace('%20',' ',$this->uri->segment(6));
        $jumlahcuti=$this->uri->segment(7);
        $cek=$this->m_koreksi->cek_kks($nodok,$nik,$jumlahcuti)->getNumRows();

        if ($cek=0) {
            redirect("trans/koreksi/koreksi_khusus/kode_failed");
        } else {

            $this->db->query("delete from sc_tmp.koreksicb 
				where nik='$nik' and nodok='$nodok' and tgl_dok='$tgl_dok' and jumlahcuti='$jumlahcuti' and doctype='X'");
            redirect("trans/koreksi/koreksi_khusus/del_succes/$nodok");
        }

    }


}
