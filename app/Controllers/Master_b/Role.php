<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Role extends BaseController
{
    function index(){
        $data['title']='Master Role Access';
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.13'; $versirelease='I.M.A.13/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.M.A.13'";
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
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/role/v_mrole',$data);
    }


    function list_mrole(){
        $list = $this->m_role->get_t_mrole_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $cek = $this->m_role->cek_mst_akses($lm->roleid)->getNumRows();

            $no++;
            $row = array();

            $row[] = $no;
            if ($cek>0) {
                $row[] = '
              <a class="btn btn-sm btn-warning" href='."'". base_url('master/role/access_permission').'/'.'?id='.$this->fiky_encryption->sealed(trim($lm->id))."'".'  title="Access Permission" onclick="return confirm('."'".'Accessing To Menu Programs ?  : '.trim($lm->rolename)."'".')"><i class="fa fa-key"></i> </a>
              <a class="btn btn-sm btn-primary" href='."'". base_url('master/role/list_access_permission').'/'.'?id='.$this->fiky_encryption->sealed(trim($lm->id))."'".'  title="Detail Permission" onclick="return confirm('."'".'Detail Menu Programs ?  : '.trim($lm->rolename)."'".')"><i class="fa fa-bars"></i> </a>
              ';
            } else {
                $row[] = '
              <a class="btn btn-sm btn-warning" href='."'". base_url('master/role/access_permission').'/'.'?id='.$this->fiky_encryption->sealed(trim($lm->id))."'".'  title="Access Permission" onclick="return confirm('."'".'Accessing To Menu Programs ?  : '.trim($lm->rolename)."'".')"><i class="fa fa-key"></i> </a>
              <a class="btn btn-sm btn-primary" href='."'". base_url('master/role/list_access_permission').'/'.'?id='.$this->fiky_encryption->sealed(trim($lm->id))."'".'  title="Detail Permission" onclick="return confirm('."'".'Detail Menu Programs ?  : '.trim($lm->rolename)."'".')"><i class="fa fa-bars"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete Role" onclick="delete_role('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a>';
            }


            $row[] = $lm->roleid;
            $row[] = $lm->rolename;
            $row[] = $lm->chold;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_role->t_mrole_view_count_all(),
            "recordsFiltered" => $this->m_role->t_mrole_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function saveEntry(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $roleid = strtoupper(trim($dataprocess->roleid));
            $rolename = strtoupper(trim($dataprocess->rolename));
            $chold = trim($dataprocess->chold);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.role');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and roleid='$roleid'";
                $check = $this->m_role->q_rolemaster($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'roleid' => $roleid,
                        'rolename' => $rolename,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        //$paramerror=" and userid='$nama' and modul='I.M.A.13'";
                        //$dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$roleid);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='DELETE') {

                $builder->where('id' , $id);
                if ($builder->delete()) {
                    //$paramerror=" and userid='$nama' and modul='I.M.A.13'";
                    //$dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$roleid);
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

    function showing_data($id){
        $data = $this->m_role->get_t_mrole_view_by_id($id);
        echo json_encode($data);
    }

    function access_permission(){
        $id = trim($this->fiky_encryption->unseal($this->request->getGet('id')));
        $param = " and id='$id'";
        $extract_role = $this->m_role->q_rolemaster($param)->getRowArray();

        $data['dtl'] = $extract_role;
        $data['title']='Role Access Permissions';
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.13'; $versirelease='I.M.A.13/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.M.A.13'";
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

        /*s*/
        $data['title1']="MENU PROGRAM";
        $data['title2']="MENU PROGRAM ROLE PERMISSION ".$extract_role['rolename'];
        $data['roleid']=$extract_role['roleid'];
        $data['list_menu_child']=$this->m_role->q_childmenu($extract_role['roleid'])->getResult();
        $data['list_menu_user']=$this->m_role->q_childmenu_usertmp($extract_role['roleid'])->getResult();
        $data['cek_user']=$this->m_role->cek_tmp_akses($extract_role['roleid'])->getNumRows();
        /*s*/
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        return $this->template->render('master/role/v_access_permissions.php',$data);

    }

    function tambah_menu(){
        $nama = trim($this->session->get('nama'));
        $lb = $this->request->getPost('centang');
        $id = $this->request->getPost('id');
        $roleid = $this->request->getPost('roleid');

        if(empty($lb)){
            //INSERT TRX ERROR
            $trx_build = $this->db->table('sc_mst.trxerror');
            $trx_build->where('userid',$nama);
            $trx_build->where('modul','I.M.A.13');
            $trx_build->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.M.A.13',
            );
            $trx_build->insert($infotrxerror);
            return redirect()->to(base_url("master/role"));
        }
        foreach($lb as $index => $temp){
            $kdmenu=trim($lb[$index]);
            $info[$index]['roleid']=$roleid;
            $info[$index]['kodemenu']=$kdmenu;
            $info[$index]['chold']='f';
            $info[$index]['a_view']='t';
            $info[$index]['a_input']='t';
            $info[$index]['a_update']='t';
            $info[$index]['a_delete']='t';
            $info[$index]['a_approve1']='t';
            $info[$index]['a_approve2']='t';
            $info[$index]['a_approve3']='t';
            $info[$index]['a_filter']='t';
            $info[$index]['a_report']='t';
        }
        $insert = $this->m_role->add_kdmnu_tmp($info);
        $enc_id = $this->fiky_encryption->sealed($id);
        //INSERT TRX ERROR

        $trx_build = $this->db->table('sc_mst.trxerror');
        $trx_build->where('userid',$nama);
        $trx_build->where('modul','I.M.A.13');
        $trx_build->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nama,
            'nomorakhir2' => '',
            'modul' => 'I.M.A.13',
        );
        $trx_build->insert($infotrxerror);
        return redirect()->to(base_url("master/role/access_permission/?id=".$enc_id));
    }

    function kurangi_menu(){
        $lb=$this->request->getPost('centang');
        $id = $this->request->getPost('id');
        $roleid = $this->request->getPost('roleid');
        $nama = trim($this->session->get('nama'));
        if(empty($lb)){
            //INSERT TRX ERROR
            $trx_build = $this->db->table('sc_mst.trxerror');
            $trx_build->where('userid',$nama);
            $trx_build->where('modul','I.M.A.13');
            $trx_build->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.M.A.13',
            );
            $trx_build->insert($infotrxerror);
            return redirect()->to(base_url("master/role"));

        }
        foreach($lb as $index => $temp){
            $kdmenu=trim($lb[$index]);
            $info[$index]['roleid']=$roleid;
            $builder1 = $this->db->table('sc_mst.role_permissions');
            $builder1->delete(array('kodemenu' => $kdmenu,'roleid' => $roleid));

            $builder2 = $this->db->table('sc_tmp.role_permissions');
            $builder2->delete(array('kodemenu' => $kdmenu,'roleid' => $roleid));
        }
        $enc_id = $this->fiky_encryption->sealed($id);
        //INSERT TRX ERROR

        $trx_build = $this->db->table('sc_mst.trxerror');
        $trx_build->where('userid',$nama);
        $trx_build->where('modul','I.M.A.13');
        $trx_build->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nama,
            'nomorakhir2' => '',
            'modul' => 'I.M.A.13',
        );
        $trx_build->insert($infotrxerror);
        return redirect()->to(base_url("master/role/access_permission/?id=".$enc_id));
    }

    function add_menugrid($enc_roleid){
        $nama = trim($this->session->get('nama'));
        $roleid = trim($this->fiky_encryption->unseal($enc_roleid));
        $this->m_role->fl_akses($roleid);
        $this->m_role->deltmp_akses($roleid);
        $param = " and roleid='$roleid'";
        $extract_role = $this->m_role->q_rolemaster($param)->getRowArray();
        $enc_id = $this->fiky_encryption->sealed($extract_role['id']);

        //INSERT TRX ERROR
        $trx_build = $this->db->table('sc_mst.trxerror');
        $trx_build->where('userid',$nama);
        $trx_build->where('modul','I.M.A.13');
        $trx_build->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nama,
            'nomorakhir2' => '',
            'modul' => 'I.M.A.13',
        );
        $trx_build->insert($infotrxerror);
        return redirect()->to(base_url("master/role"));
    }

    /* LIST AKSES PROGRAMS */

    function list_access_permission(){
        $id = trim($this->fiky_encryption->unseal($this->request->getGet('id')));
        $param = " and id = '$id'";
        $extract_role = $this->m_role->q_rolemaster($param)->getRowArray();


        $data['title']="HAK AKSES PERMISSION ROLE <strong><b>".$extract_role['rolename']."</strong></b>";
        $data['list_akses']=$this->m_role->list_akses_permission($extract_role['roleid'])->getResult();
        $data['roleid']=$extract_role['roleid'];
        $data['rolename']=$extract_role['rolename'];

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.13'; $versirelease='I.M.A.13/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.M.A.13'";
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
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/role/v_list_access_permission',$data);

    }

    function edit_akses($enc_roleid,$enc_kodemenu){
        $nama = trim($this->session->get('nama'));
        $roleid = trim($this->fiky_encryption->unseal($enc_roleid));
        $kdmenu = trim($this->fiky_encryption->unseal($enc_kodemenu));
        $param = " and roleid = '$roleid'";
        $extract_role = $this->m_role->q_rolemaster($param)->getRowArray();

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.13'; $versirelease='I.M.A.13/RELEASE.401'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        if (empty($roleid)){
            //INSERT TRX ERROR
            $trx_build = $this->db->table('sc_mst.trxerror');
            $trx_build->where('userid',$nama);
            $trx_build->where('modul','I.M.A.13');
            $trx_build->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 2,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.M.A.13',
            );
            $trx_build->insert($infotrxerror);
            return redirect()->to(base_url("master/role"));
        } else {

            $data['title']='Akses Permission Programs & Module ';
            $data['rolename']=$extract_role['rolename'];
            $data['enc_id']=$this->fiky_encryption->sealed($extract_role['id']);
            $data['akses']=$this->m_role->detail_user_akses($roleid,$kdmenu)->getRowArray();
            return $this->template->render('master/role/v_edit_access_permissions',$data);
        }
    }

    function hps_akses($enc_roleid,$enc_kodemenu){
        $nama = trim($this->session->get('nama'));
        $roleid = trim($this->fiky_encryption->unseal($enc_roleid));
        $kodemenu = trim($this->fiky_encryption->unseal($enc_kodemenu));
        $param = " and roleid = '$roleid'";
        $extract_role = $this->m_role->q_rolemaster($param)->getRowArray();


        $builder_role = $this->db->table('sc_mst.role_permissions');
        $builder_role->where('roleid',$roleid);
        $builder_role->where('kodemenu',$kodemenu);
        $builder_role->delete();


        //TRXERROR
        $trx_build = $this->db->table('sc_mst.trxerror');
        $trx_build->where('userid',$nama);
        $trx_build->where('modul','I.M.A.13');
        $trx_build->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 2,
            'nomorakhir1' => $nama,
            'nomorakhir2' => '',
            'modul' => 'I.M.A.13',
        );
        $trx_build->insert($infotrxerror);

        $enc_id = $this->fiky_encryption->sealed($extract_role['id']);
        return redirect()->to(base_url("master/role/list_access_permission/?id=".$enc_id));
    }


    function save_akses(){
        $tipe=trim($this->request->getPost('tipe'));
        $roleid=trim($this->request->getPost('roleid'));
        $kodemenu=trim($this->request->getPost('menu'));
        $chold=$this->request->getPost('chold');
        $a_view=$this->request->getPost('a_view');
        $a_input=$this->request->getPost('a_input');
        $a_update=$this->request->getPost('a_update');
        $a_delete=$this->request->getPost('a_delete');
        $a_approve1=$this->request->getPost('a_approve1');
        $a_approve2=$this->request->getPost('a_approve2');
        $a_approve3=$this->request->getPost('a_approve3');
        $a_filter=$this->request->getPost('a_filter');
        $a_report=$this->request->getPost('a_report');
        if ($tipe==='edit'){
            $info_update=array(
                'roleid'=>$roleid,
                'kodemenu'=>$kodemenu,
                'chold'=>$chold,
                'a_view'=>$a_view,
                'a_input'=>$a_input,
                'a_update'=>$a_update,
                'a_delete'=>$a_delete,
                'a_approve1'=>$a_approve1,
                'a_approve2'=>$a_approve2,
                'a_approve3'=>$a_approve3,
                'a_filter'=>$a_filter,
                'a_report'=>$a_report
            );
            $this->m_role->update_akses($roleid,$kodemenu,$info_update);
            $enc_roleid = $this->fiky_encryption->sealed($roleid);
            $enc_kodemenu = $this->fiky_encryption->sealed($kodemenu);
            return redirect()->to(base_url("master/role/edit_akses/$enc_roleid/$enc_kodemenu"));
        }
    }

}
