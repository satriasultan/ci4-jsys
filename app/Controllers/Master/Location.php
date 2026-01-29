<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Location extends BaseController
{
    public function index()
    {
        $data['title']="Master Data Warehouse";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.7'; $versirelease='I.M.B.7/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        $kmenu = 'I.M.B.7';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        return $this->template->render('master/location/v_location',$data);
    }

    function list_mlocation(){
        $list = $this->m_location->get_t_mlocation_view();
        $kmenu = 'I.M.B.7';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        // $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';


        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $btnUpdate = '';
            $btnDelete = '';

            if ($canUpdate) {
                $btnUpdate = '
                    <a class="btn btn-sm btn-warning" 
                        href="javascript:void(0)" 
                        title="Update Location" 
                        onclick="update_mlocation(\''.trim($lm->id).'\')">
                        <i class="fa fa-gear"></i>
                    </a>';
            }

            if ($canDelete) {
                $btnDelete = '
                    <a class="btn btn-sm btn-danger" 
                        href="javascript:void(0)" 
                        title="Delete Location" 
                        onclick="delete_mlocation(\''.trim($lm->id).'\')">
                        <i class="fa fa-trash"></i>
                    </a>';
            }

            $row[] = $btnUpdate . ' ' . $btnDelete;
            $row[] = $lm->idlocation;
            $row[] = $lm->nmlocation;
            $row[] = $lm->idarea_default;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmcoa;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_location->t_mlocation_view_count_all(),
            "recordsFiltered" => $this->m_location->t_mlocation_view_count_filtered(),
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
            $idlocation = strtoupper(trim($dataprocess->idlocation));
            $idarea_default = strtoupper(trim($dataprocess->idarea_default));
            $nmlocation = strtoupper(trim($dataprocess->nmlocation));
            $pselisih = $dataprocess->pselisih;
            $chold = trim($dataprocess->chold);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.mlocation');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idlocation='$idlocation'";
                $check = $this->m_location->q_mlocation($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idlocation' => $idlocation,
                        'idarea_default' => $idarea_default,
                        'nmlocation' => $nmlocation,
                        'chold' => $chold,
                        'idbarcode' => $idlocation,
                        'pselisih' => $pselisih,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        //$paramerror=" and userid='$nama' and modul='I.M.A.13'";
                        //$dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idlocation);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idlocation='$idlocation'";
                $check = $this->m_location->q_mlocation($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'idbarcode' => $idlocation,
                        'idarea_default' => $idarea_default,
                        'nmlocation' => $nmlocation,
                        'pselisih' => $pselisih,
                        'chold' => $chold,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idlocation);
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

                $builder = $this->db->table('sc_mst.mlocation');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    //$paramerror=" and userid='$nama' and modul='I.M.A.13'";
                    //$dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idlocation);
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

    function showing_data(){
        $id = $this->uri->getSegment(4);
        $data = $this->m_location->q_mlocation(" and id=$id")->getRow();
        echo json_encode($data);
    }

    //area construction
    public function area()
    {
        $data['title']="Master Data Position Area";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.2'; $versirelease='I.M.B.2/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        $kmenu = 'I.M.B.8';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        return $this->template->render('master/location/v_area',$data);
    }

    function list_marea(){
        $kmenu = 'I.M.B.8';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        // $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';


        $list = $this->m_location->get_t_marea_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;

            $btnUpdate = '';
            $btnDelete = '';

            if ($canUpdate) {
                $btnUpdate = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Update Area" 
                onclick="update_marea('."'".trim($lm->id)."'".')">
                <i class="fa fa-gear"></i> </a>';
            }

            if ($canDelete) {
                $btnDelete = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete Area" 
                onclick="delete_marea('."'".trim($lm->id)."'".')">
                <i class="fa fa-trash"></i> </a>';
            }
            $row[] = $btnUpdate . ' ' . $btnDelete;
            $row[] = $lm->nmlocation;
            $row[] = $lm->idarea;
            $row[] = $lm->nmarea;
            $row[] = $lm->idbarcode;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_location->t_marea_view_count_all(),
            "recordsFiltered" => $this->m_location->t_marea_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function saveEntryArea(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idlocation = strtoupper(trim($dataprocess->idlocation));
            $idarea = strtoupper(trim($dataprocess->idarea));
            $nmarea = strtoupper(trim($dataprocess->nmarea));
            $chold = trim($dataprocess->chold);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.marea');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idlocation='$idlocation' and idarea='$idarea'";
                $check = $this->m_location->q_marea($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idlocation' => $idlocation,
                        'idarea' => $idarea,
                        'nmarea' => $nmarea,
                        'chold' => $chold,
                        'idbarcode' => $idarea,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        //$paramerror=" and userid='$nama' and modul='I.M.A.13'";
                        //$dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$nmarea);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idlocation='$idlocation' and idarea='$idarea'";
                $check = $this->m_location->q_marea($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        //'idbarcode' => $idlocation,
                        'nmarea' => $nmarea,
                        'chold' => $chold,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$nmarea);
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

                $builder = $this->db->table('sc_mst.marea');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idlocation);
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

    function showing_data_area(){
        $id = $this->uri->getSegment(4);
        $data = $this->m_location->q_marea(" and id = $id")->getRow();
        echo json_encode($data);
    }

    function import_area(){
        $data['title']="Import Position Area ";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.M.B.3'; $versirelease='I.M.B.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
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
        $kmenu='I.M.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama' and idlocation='$loccode'";
        $data['list_detail']=$this->m_location->q_marea_tmp($parameter)->getResult();
        $data['adaisi']=$this->m_location->q_marea_tmp($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('master/location/v_import_area',$data);

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $this->db->query("delete from sc_tmp.marea where inputby='$nama'");


        $path = "./assets/files/positionarea/";
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
                echo '<a href="'.base_url('/master/location/import_area').'">BACK</a>';
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
                        'idlocation' => $loccode,
                        'idarea' => trim($rowData[0][0]),
                        'nmarea' => trim($rowData[0][1]),
                        'idbarcode' => $idtype = trim($rowData[0][2]),
                        'idcomplex' => $idtype = trim($rowData[0][2]),
                        'chold' => 'NO',
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                    );

                    $pm =  " and trim(nmarea)='".trim($rowData[0][1])."'";
                    $cek = $this->m_location->q_marea($pm)->getNumRows();
                    if($cek <= 0 and trim($rowData[0][0]) != '') {
                        $builder = $this->db->table("sc_tmp.marea");
                        $builder->insert($data);
                    }
                    $no++;
                }
                return redirect()->to(base_url('/master/location/import_area'));
            }
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.marea where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.B.3');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.M.B.3',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/master/location/import_area'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.marea set status='F' where inputby='$nama'");

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.B.3');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.M.B.3',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/master/location/import_area'));
    }

    function showlabels()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.4'; $versirelease='I.M.B.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.4'";
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
        return $this->template->render('master/location/v_showlabels_area',$data);
    }

    function show_showlabels_area(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);

        $title = " Barcode Label Area/Rack/Position";

        $datajson =  base_url("master/location/api_show_showlabels_area/?enc_idlocation=$enc_idlocation") ;
        $datamrt =  base_url("assets/mrt/showlabels_area.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_showlabels_area(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();
        $param=" and idlocation='$idlocation'";
        $datamst = $this->m_location->q_marea($param);

        header("Content-Type: text/json");
        return json_encode(
            array(
                'info' => array([
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    // PARTIAL AREA ITEM
    function show_showlabels_area_partial(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $idarea = $this->request->getPost('idarea');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);

        if (!empty($idarea)) {
            $i=0;
            foreach ($idarea as $dd) {
                if ($i == 0){
                    $idareax.=" idarea='$dd'";
                } else { $idareax.=" or idarea='$dd'"; }
                $i++;
            }
            $idareaxp=" and (".$idareax.")";
        } else {  $idareaxp=""; }


        $param = "".$idareaxp;
        $enc_param = $this->fiky_encryption->sealed($param);


        $title = " Barcode Label Area/Rack/Position";
        $datajson =  base_url("master/location/api_show_showlabels_area_partial/?enc_param=$enc_param") ;
        $datamrt =  base_url("assets/mrt/showlabels_area.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_showlabels_area_partial(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $param=trim($this->fiky_encryption->unseal($this->request->getGet('enc_param')));
        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_location->q_marea($param);

        header("Content-Type: text/json");
        return json_encode(
            array(
                'info' => array([
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    function cc(){
        $data['title']="Master Cost Center";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.9'; $versirelease='I.M.B.9/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        $kmenu = 'I.M.B.9';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();    
        return $this->template->render('master/location/v_costcenter',$data);
    }

    function list_costcenter(){
        $kmenu = 'I.M.B.9';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        // $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';

        $list = $this->m_location->get_t_costcenter_view();
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
                        title="Update Bagian"
                        onclick="update_costcenter(\''.trim($lm->idcostcenter).'\')">
                        <i class="fa fa-gear"></i>
                    </a>' : '')
                .
                ($canDelete ? '
                    <a class="btn btn-sm btn-danger"
                        href="javascript:void(0)"
                        title="Delete Bagian"
                        onclick="delete_costcenter(\''.trim($lm->idcostcenter).'\')">
                        <i class="fa fa-trash"></i>
                    </a>' : '');
            $row[] = $lm->idcostcenter;
            $row[] = $lm->nmcostcenter;
            $row[] = $lm->nmcoa;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_location->t_costcenter_view_count_all(),
            "recordsFiltered" => $this->m_location->t_costcenter_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveCostCenter(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $idcostcenter = strtoupper(trim($dataprocess->idcostcenter));
            $nmcostcenter = strtoupper(trim($dataprocess->nmcostcenter));
            $chold = trim($dataprocess->chold);
            $pbiaya = ($dataprocess->pbiaya);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.costcenter');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idcostcenter='$idcostcenter'";
                $check = $this->m_location->q_costcenter($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idcostcenter' => $idcostcenter,
                        'nmcostcenter' => $nmcostcenter,
                        'chold' => $chold,
                        'pbiaya' => $pbiaya,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idcostcenter);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idcostcenter='$idcostcenter'";
                $check = $this->m_location->q_costcenter($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'nmcostcenter' => $nmcostcenter,
                        'chold' => $chold,
                        'pbiaya' => $pbiaya,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('idcostcenter',$idcostcenter);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idcostcenter);
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
                $builder = $this->db->table('sc_mst.costcenter');
                $builder->where('idcostcenter' , $idcostcenter);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idcostcenter);
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

    function showing_data_costcenter(){
        $id = $this->uri->getSegment(4);
        $data = $this->m_location->q_costcenter(" and idcostcenter = '$id'")->getRow();
        echo json_encode($data);
    }
}
