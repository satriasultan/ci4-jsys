<?php

namespace App\Controllers\Capital;

use App\Controllers\BaseController;

class Masters extends BaseController
{
    public function index()
    {
        $data['title']="Otoritas system";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.2'; $versirelease='I.T.A.2/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/master/v_authorisasi_setting',$data);
    }

    function list_capital_authorization(){
        $list = $this->m_capital_masters->get_capital_authorization_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
           /* $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="update_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a>';
           */
            $row[] = $lm->username;
            $row[] = $lm->nmdept;
            $row[] = $lm->level_1;
            $row[] = $lm->level_2;
            $row[] = $lm->level_3;
            $row[] = $lm->level_4;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_masters->capital_authorization_view_count_all(),
            "recordsFiltered" => $this->m_capital_masters->capital_authorization_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function save_capital_authorization(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $username = trim($dataprocess->username);
            $kddept = trim($dataprocess->kddept);
            $level_1 = trim($dataprocess->level_1);
            $level_2 = trim($dataprocess->level_2);
            $level_3 = trim($dataprocess->level_3);
            $level_4 = trim($dataprocess->level_4);
            $description = trim($dataprocess->description);
            $id = trim($dataprocess->id);

            $chold = strtoupper(trim($dataprocess->chold));
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.capital_authorization');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and username='$username'";
                $check = $this->m_capital_masters->q_capital_authorization($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'username' => $username,
                        'kddept' => $kddept,
                        'kodemenu' => 'I.T.A.2',
                        'grouptype' => 'CIT',
                        'level_1' => $level_1,
                        'level_2' => $level_2,
                        'level_3' => $level_3,
                        'level_4' => $level_4,
                        'chold' => $chold,
                        'description' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$username);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and username='$username'";
                $check = $this->m_capital_masters->q_capital_authorization($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(

                        'level_1' => $level_1,
                        'level_2' => $level_2,
                        'level_3' => $level_3,
                        'level_4' => $level_4,
                        'chold' => $chold,
                        'description' => $description,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$username);
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

                $builder = $this->db->table('sc_mst.capital_authorization');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$username);
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

    public function group()
    {
        $data['title']="Master Pengajuan IT Kategori";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/master/v_grouping',$data);
    }

    function list_mgroup(){
        $list = $this->m_capital_masters->get_t_mgroup_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="update_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a>';
            $row[] = $lm->idgroup;
            $row[] = $lm->nmgroup;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_masters->t_mgroup_view_count_all(),
            "recordsFiltered" => $this->m_capital_masters->t_mgroup_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveGrouping(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $nmgroup = strtoupper(trim($dataprocess->nmgroup));
            $grouptype = strtoupper(trim($dataprocess->grouptype));
            $chold = strtoupper(trim($dataprocess->chold));
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.mgroup');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and grouptype='CIT' and idgroup='$idgroup'";
                $check = $this->m_capital_masters->q_mgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idgroup' => $idgroup,
                        'nmgroup' => $nmgroup,
                        'grouptype' => $grouptype,
                        'chold' => $chold,
                        'description' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and grouptype='CIT' and idgroup='$idgroup'";
                $check = $this->m_capital_masters->q_mgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'nmgroup' => $nmgroup,
                        'chold' => $chold,
                        'description' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
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

                $builder = $this->db->table('sc_mst.mgroup');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idgroup);
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

    function showing_data_mgroup($id){
        $data = $this->m_capital_masters->get_t_mgroup_view_by_id($id);
        echo json_encode($data);
    }
    /* MSUBGROUP */
    function list_msubgroup(){
        $list = $this->m_capital_masters->get_t_msubgroup_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="update_msubgroup('."'".trim($lm->idserial)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_msubgroup('."'".trim($lm->idserial)."'".')"><i class="fa fa-trash"></i> </a>';
            $row[] = $lm->idsubgroup;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->nmgroup;
            $row[] = $lm->keterangan;
            $row[] = $lm->grouphold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_masters->t_msubgroup_view_count_all(),
            "recordsFiltered" => $this->m_capital_masters->t_msubgroup_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveSubGrouping(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $idsubgroup = strtoupper(trim($dataprocess->idsubgroup));
            $nmsubgroup = strtoupper(trim($dataprocess->nmsubgroup));
            $grouptype = strtoupper(trim($dataprocess->grouptype));
            $chold = strtoupper(trim($dataprocess->chold));
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.msubgroup');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and grouptype='CIT' and idgroup='$idgroup' and idsubgroup='$idsubgroup'";
                $check = $this->m_capital_masters->q_msubgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idgroup' => $idgroup,
                        'idsubgroup' => $idsubgroup,
                        'nmsubgroup' => $nmsubgroup,
                        'grouptype' => 'CIT',
                        'grouphold' => $chold,
                        'keterangan' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $builder = $this->db->table('sc_mst.msubgroup');
                $param = " and grouptype='CIT' and idgroup='$idgroup' and idsubgroup='$idsubgroup'";
                $check = $this->m_capital_masters->q_msubgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(

                        'nmsubgroup' => $nmsubgroup,
                        'grouptype' => 'CIT',
                        'grouphold' => $chold,
                        'keterangan' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    $builder->where('idserial',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
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

                $builder = $this->db->table('sc_mst.msubgroup');
                $builder->where('idserial' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idgroup);
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

    function showing_data_msubgroup($id){
        $data = $this->m_capital_masters->get_t_msubgroup_view_by_id($id);
        echo json_encode($data);
    }

    /* MTYPE MASTER TYPE  MTYPE*/
    function list_mtype(){
        $list = $this->m_capital_masters->get_t_msubgroup_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="update_msubgroup('."'".trim($lm->idserial)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_msubgroup('."'".trim($lm->idserial)."'".')"><i class="fa fa-trash"></i> </a>';
            $row[] = $lm->idsubgroup;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->nmgroup;
            $row[] = $lm->keterangan;
            $row[] = $lm->grouphold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_masters->t_msubgroup_view_count_all(),
            "recordsFiltered" => $this->m_capital_masters->t_msubgroup_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveMtype(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $idsubgroup = strtoupper(trim($dataprocess->idsubgroup));
            $nmsubgroup = strtoupper(trim($dataprocess->nmsubgroup));
            $grouptype = strtoupper(trim($dataprocess->grouptype));
            $chold = strtoupper(trim($dataprocess->chold));
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.msubgroup');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and grouptype='CIT' and idgroup='$idgroup' and idsubgroup='$idsubgroup'";
                $check = $this->m_capital_masters->q_msubgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idgroup' => $idgroup,
                        'idsubgroup' => $idsubgroup,
                        'nmsubgroup' => $nmsubgroup,
                        'grouptype' => 'CIT',
                        'grouphold' => $chold,
                        'keterangan' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $builder = $this->db->table('sc_mst.msubgroup');
                $param = " and grouptype='CIT' and idgroup='$idgroup' and idsubgroup='$idsubgroup'";
                $check = $this->m_capital_masters->q_msubgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(

                        'nmsubgroup' => $nmsubgroup,
                        'grouptype' => 'CIT',
                        'grouphold' => $chold,
                        'keterangan' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    $builder->where('idserial',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
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

                $builder = $this->db->table('sc_mst.msubgroup');
                $builder->where('idserial' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idgroup);
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

    function showing_data_mtype($id){
        $data = $this->m_capital_masters->get_t_msubgroup_view_by_id($id);
        echo json_encode($data);
    }


    function showing_data_kdlvl($id){
        $param = " and kdlvl='$id'";
        $data = $this->m_capital_masters->q_lvljabatan($param)->getRow();
        echo json_encode($data);
    }

    function showing_data_kdlvl_msubgroup(){
        $kdlvl = $this->request->getGet('kdlvl');
        $idsubgroup = $this->request->getGet('idsubgroup');
        $param = " and kdlvl='$kdlvl' and idsubgroup='$idsubgroup'";
        $data = $this->m_capital_masters->q_capital_level_plafond($param)->getRow();
        echo json_encode($data);
    }

}
