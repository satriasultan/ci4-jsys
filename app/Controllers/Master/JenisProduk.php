<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class JenisProduk extends BaseController{

    function jenisproduk(){
        $data['title']="Master Jenis Produk";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.12'; $versirelease='I.M.B.12/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']="";
        $message=$this->session->getFlashdata('message');
        if (!empty($message)) {
            $data['message']="<div class='alert alert-info'> $message </div>";
        } else {
            $data['message']='';
        }
        $kmenu = 'I.M.B.12';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();    
        return $this->template->render('master/jenisproduk/v_jenisproduk',$data);
    }

    function list_jenisproduk(){
        $kmenu = 'I.M.B.12';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        // $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';

        $list = $this->m_jnsprdk->get_t_jenisproduk_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] =
                ($canUpdate ? '
                    <a class="btn btn-sm btn-warning"
                        href="javascript:void(0)"
                        title="Update Jenis Produk"
                        onclick="update_jenisproduk(\''.trim($lm->idjenisproduk).'\')">
                        <i class="fa fa-gear"></i>
                    </a>' : '')
                .
                ($canDelete ? '
                    <a class="btn btn-sm btn-danger"
                        href="javascript:void(0)"
                        title="Delete Jenis Produk"
                        onclick="delete_jenisproduk(\''.trim($lm->idjenisproduk).'\')">
                        <i class="fa fa-trash"></i>
                    </a>' : '');
            $row[] = $lm->idjenisproduk;
            $row[] = $lm->nmjenisproduk;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_jnsprdk->t_jenisproduk_view_count_all(),
            "recordsFiltered" => $this->m_jnsprdk->t_jenisproduk_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveJenisProduk(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $idjenisproduk = strtoupper(trim($dataprocess->idjenisproduk));
            $nmjenisproduk = strtoupper(trim($dataprocess->nmjenisproduk));
            $chold = trim($dataprocess->chold);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.jenisproduk');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idjenisproduk='$idjenisproduk'";
                $check = $this->m_jnsprdk->q_jenisproduk($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idjenisproduk' => $idjenisproduk,
                        'nmjenisproduk' => $nmjenisproduk,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idjenisproduk);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idjenisproduk='$idjenisproduk'";
                $check = $this->m_jnsprdk->q_jenisproduk($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'nmjenisproduk' => $nmjenisproduk,
                        'chold' => $chold,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('idjenisproduk',$idjenisproduk);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idjenisproduk);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                } else {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                }

            } else if ($type==='DELETE') {
                $builder = $this->db->table('sc_mst.jenisproduk');
                $builder->where('idjenisproduk' , $idjenisproduk);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idjenisproduk);
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

    function showing_data_jenisproduk(){
        $id = $this->uri->getSegment(4);
        $data = $this->m_jnsprdk->q_jenisproduk(" and idjenisproduk = '$id'")->getRow();
        echo json_encode($data);
    }

}