<?php

namespace App\Controllers\Contacts;

use App\Controllers\BaseController;

class Supplier extends BaseController
{
    public function index()
    {
        $data['title']="Master data supplier";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.C.A.1'; $versirelease='I.C.A.1/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('contacs/supplier/v_list_supplier',$data);
    }

    function list_msupplier(){
        $list = $this->m_supplier->get_t_msupplier_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update Supplier" onclick="update_msupplier('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete Supplier" onclick="delete_msupplier('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a>';
            $row[] = $lm->idsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->contact1;
            $row[] = $lm->contact2;
            $row[] = $lm->ownsupplier;
            $row[] = $lm->nmgroup;
            $row[] = $lm->addsupplier;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_supplier->t_msupplier_view_count_all(),
            "recordsFiltered" => $this->m_supplier->t_msupplier_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function input_supplier(){
        $data['title']="Input Data Supplier";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.C.A.1'; $versirelease='I.C.A.1/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('contacs/supplier/v_input_supplier',$data);
    }






    function saveSupplier(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idsupplier = strtoupper(trim($dataprocess->idsupplier));
            $nmsupplier = strtoupper(trim($dataprocess->nmsupplier));
            $contact1 = strtoupper(trim($dataprocess->contact1));
            $contact2 = strtoupper(trim($dataprocess->contact2));
            $ownsupplier = strtoupper(trim($dataprocess->ownsupplier));
            $addsupplier = strtoupper(trim($dataprocess->addsupplier));
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $chold = strtoupper(trim($dataprocess->chold));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.msupplier');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idsupplier='$idsupplier'";
                $check = $this->m_supplier->q_msupplier($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idsupplier' => $idsupplier,
                        'nmsupplier' => $nmsupplier,
                        'contact1' => $contact1,
                        'contact2' => $contact2,
                        'ownsupplier' => $ownsupplier,
                        'addsupplier' => $addsupplier,
                        'idgroup' => $idgroup,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$nmsupplier);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idsupplier='$idsupplier'";
                $check = $this->m_supplier->q_msupplier($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'nmsupplier' => $nmsupplier,
                        'contact1' => $contact1,
                        'contact2' => $contact2,
                        'ownsupplier' => $ownsupplier,
                        'addsupplier' => $addsupplier,
                        'idgroup' => $idgroup,
                        'chold' => $chold,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$nmsupplier);
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

                $builder = $this->db->table('sc_mst.msupplier');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$nmsupplier);
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

    function showing_data_supplier($id){
        $data = $this->m_supplier->get_t_msupplier_view_by_id($id);
        echo json_encode($data);
    }



    public function suppliers_group()
    {
        $data['title']="Master data supplier";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.C.A.3'; $versirelease='I.C.A.3/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('contacs/supplier/v_list_supplier_group',$data);
    }

    function list_mgroup(){
        $list = $this->m_supplier->get_t_mgroup_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update Supplier" onclick="update_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete Supplier" onclick="delete_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a>';
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
            "recordsTotal" => $this->m_supplier->t_mgroup_view_count_all(),
            "recordsFiltered" => $this->m_supplier->t_mgroup_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function saveMgroup(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idsupplier = strtoupper(trim($dataprocess->idsupplier));
            $nmsupplier = strtoupper(trim($dataprocess->nmsupplier));
            $contact1 = strtoupper(trim($dataprocess->contact1));
            $contact2 = strtoupper(trim($dataprocess->contact2));
            $ownsupplier = strtoupper(trim($dataprocess->ownsupplier));
            $addsupplier = strtoupper(trim($dataprocess->addsupplier));
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $chold = strtoupper(trim($dataprocess->chold));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.msupplier');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idsupplier='$idsupplier'";
                $check = $this->m_supplier->q_msupplier($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idsupplier' => $idsupplier,
                        'nmsupplier' => $nmsupplier,
                        'contact1' => $contact1,
                        'contact2' => $contact2,
                        'ownsupplier' => $ownsupplier,
                        'addsupplier' => $addsupplier,
                        'idgroup' => $idgroup,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$nmsupplier);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idsupplier='$idsupplier'";
                $check = $this->m_supplier->q_msupplier($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'nmsupplier' => $nmsupplier,
                        'contact1' => $contact1,
                        'contact2' => $contact2,
                        'ownsupplier' => $ownsupplier,
                        'addsupplier' => $addsupplier,
                        'idgroup' => $idgroup,
                        'chold' => $chold,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$nmsupplier);
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

                $builder = $this->db->table('sc_mst.msupplier');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$nmsupplier);
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
        $data = $this->m_supplier->get_t_msupplier_view_by_id($id);
        echo json_encode($data);
    }





    function import_suppliers(){
        $data['title']="Import Supplier ".$this->mimes->mimes['xlsx'];
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.C.A.2'; $versirelease='I.C.A.2/RELEASE.401'; $releasedate=date('2022-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.C.A.2'";
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
        $kmenu='I.C.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_supplier->q_tmp_msupplier($parameter)->getResult();
        $data['adaisi']=$this->m_supplier->q_tmp_msupplier($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('contacs/supplier/v_import_supplier',$data);

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
                    $cek = $this->m_supplier->q_marea($pm)->getNumRows();
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
}
