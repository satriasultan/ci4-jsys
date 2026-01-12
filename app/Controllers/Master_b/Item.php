<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Item extends BaseController
{
    public function index()
    {
        $data['title']="MASTER DATA ITEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.1'; $versirelease='I.D.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.1'";
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
        $this->m_item->q_autoinsert_unit();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_list',$data);
    }

    function list_mitem(){
        $list = $this->m_item->get_t_item_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="editItem('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <!--div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="'.base_url('master/item/del_item').'?var='.trim($lm->id).'" onclick="return confirm('."'Do you want to delete?..  ".trim($lm->idbarang)."'".')"><i class="fa fa-trash"></i> Delete </a -->
                    </div>
            </div>
             ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->nmgroup;
            $row[] = $lm->unit;
            $row[] = $lm->setminstock1;
            $row[] = '<div class="ratakanan">'.$lm->minstock1.'</div>';
            $row[] = $lm->idbarcode;
            $row[] = $lm->chold;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_item_view_count_all(),
            "recordsFiltered" => $this->m_item->t_item_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input(){
        $data['title']="INPUT DATA MASTER ITEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.1'; $versirelease='I.D.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.1'";
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
        $data['type']="INPUT";
        $data['nama']=$nama;
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_input',$data);
    }

    function edit(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.1'; $versirelease='I.D.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.1'";
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
        $data['title']="EDIT DATA MASTER ITEM";
        $var = trim($this->request->getGet('var'));
        $data['type']="EDIT";
        $data['nama']=$nama;
        $data['id']=$var;
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_input',$data);
    }

    function del_item(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_mst.mbarang');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.A.1');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.D.A.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('master/item'));
    }

    function showDetailItem () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and id='$var'";
        $dtl = $this->m_item->q_item_param($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }
    function saveDataItem(){
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $docno = trim($this->request->getPost('docno'));
        $id = trim($this->request->getPost('id'));
        $idbarang = trim(strtoupper($this->request->getPost('idbarang')));
        $nmbarang = trim(strtoupper($this->request->getPost('nmbarang')));
        $idgroup = trim($this->request->getPost('idgroup'));
        $subunitenable = trim($this->request->getPost('subunitenable'));
        $unit = trim($this->request->getPost('unit'));
        $subunit = trim($this->request->getPost('subunit'));
        $idbarcode = trim($this->request->getPost('idbarcode'));
        $maks_daystock = str_replace('.','',trim($this->request->getPost('maks_daystock')));
        $deflocation = trim($this->request->getPost('deflocation'));
        $defarea = trim($this->request->getPost('defarea'));
        $chold = trim($this->request->getPost('chold'));
        $setminstock = trim($this->request->getPost('setminstock'));
        $minstock = str_replace('.','',trim($this->request->getPost('minstock')));
        $description = trim(strtoupper($this->request->getPost('description')));
        $expdate = !$this->request->getPost('expdate') ? null : date('Y-m-d',strtotime($this->request->getPost('expdate')));
        $mfgdate = !$this->request->getPost('mfgdate') ? null : date('Y-m-d',strtotime($this->request->getPost('mfgdate')));

        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $builder_mbarang = $this->db->table('sc_mst.mbarang');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

            if ($type==='INPUT'){
                $info = array(
                    'idbarang' => $idbarang,
                    'nmbarang' => $nmbarang,
                    'idgroup' => $idgroup,
                    'subunitenable' => $subunitenable,
                    'unit' => $unit,
                    'subunit' => $subunit,
                    'idbarcode' => $idbarcode,
                    'maks_daystock' => $maks_daystock,
                    'setminstock' => $setminstock,
                    'minstock' => $minstock,
                    'chold' => $chold,
                    'description' => $description,
                    'expdate' => $expdate,
                    'mfgdate' => $mfgdate,
                    'deflocation' => $deflocation,
                    'defarea' => $defarea,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );

                if ($builder_mbarang->insert($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.D.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $idbarang,
                        'nomorakhir2' => '',
                        'modul' => 'I.D.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.D.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $paramerror=" and userid='$nama' and modul='I.D.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => false, 'messages' => trim($dtlerror['description']));
                    echo json_encode($getResult);
                }
            } else if ($type==='EDIT'){
                $infoX = array(
                    'nmbarang' => $nmbarang,
                    'idgroup' => $idgroup,
                    'subunitenable' => $subunitenable,
                    'unit' => $unit,
                    'subunit' => $subunit,
                    'idbarcode' => $idbarcode,
                    'maks_daystock' => $maks_daystock,
                    'setminstock' => $setminstock,
                    'minstock' => $minstock,
                    'chold' => $chold,
                    'description' => $description,
                    'expdate' => $expdate,
                    'mfgdate' => $mfgdate,
                    'deflocation' => $deflocation,
                    'defarea' => $defarea,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );
                $builder_mbarang->where('id',$id);
                if ($builder_mbarang->update($infoX)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $idbarang,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $paramerror=" and userid='$nama' and modul='I.D.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => false, 'messages' => trim($dtlerror['description']));
                    echo json_encode($getResult);
            }
        }
    }

    function import(){
        $data['title']="Import Data Item .Xls";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.A.2'; $versirelease='I.D.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.2'";
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
        $kmenu='I.D.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_item->q_item_tmp_param($parameter)->getResult();
        $data['adaisi']=$this->m_item->q_item_tmp_param($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('master/item/v_import',$data);

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $this->db->query("delete from sc_tmp.mbarang where inputby='$nama'");


        $path = "./assets/files/item/";
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
                echo '<a href="'.base_url('/master/item/import').'">BACK</a>';
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
                        //'idlocation' => $loccode,
                        'idbarang' => trim($rowData[0][0]),
                        'nmbarang' => trim($rowData[0][1]),
                        'idgroup' => trim($rowData[0][2]),
                        'unit' => trim($rowData[0][3]),
                        'subunit' => 0,
                        'subunitenable' => 'NO',
                        'sku' => trim($rowData[0][4]),
                        'idbarcode' => trim($rowData[0][4]),
                        'maks_daystock' => trim($rowData[0][5]),
                        'deflocation' => trim($rowData[0][6]),
                        'defarea' => trim($rowData[0][7]),
                        'description' => trim($rowData[0][8]),
                        'chold' => trim($rowData[0][9]),
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                        'setminstock' => trim($rowData[0][10]),
                        'minstock' => trim($rowData[0][11]),
                    );

                    $pm =  " and trim(idbarang)='".trim($rowData[0][0])."'";
                    $cek = $this->m_item->q_item_param($pm)->getNumRows();
                    if($cek <= 0 and trim($rowData[0][0]) !== '') {
                        $builder = $this->db->table("sc_tmp.mbarang");
                        $builder->insert($data);
                    }
                    $no++;
                }
                return redirect()->to(base_url('/master/item/import'));
            }
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.mbarang where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.A.2');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.A.2',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/master/item/import'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.mbarang set status='F' where inputby='$nama'");

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.A.2');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.A.2',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/master/item/import'));
    }


    public function unit()
    {
        $data['title']="UNIT ITEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.3'; $versirelease='I.D.A.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.3'";
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
        return $this->template->render('master/item/v_list_unit',$data);
    }

    function list_unit(){
        $list = $this->m_item->get_t_unit_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="update_unit('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_unit('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';
            $row[] = $lm->idunit;
            $row[] = $lm->parentunit;
            $row[] = $lm->ctype;
            $row[] = $lm->conversion_value;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_unit_view_count_all(),
            "recordsFiltered" => $this->m_item->t_unit_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function saveUnit(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $ctype = strtoupper(trim($dataprocess->ctype));
            $idunit = strtoupper(trim($dataprocess->idunit));
            $parentunit = strtoupper(trim($dataprocess->parentunit));
            $conversion_value = strtoupper(trim($dataprocess->conversion_value));
            $description = strtoupper(trim($dataprocess->description));
            $chold = trim($dataprocess->chold);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.unit');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idunit='$idunit' and parentunit='$parentunit'";
                $check = $this->m_item->q_unit_param($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idunit' => $idunit,
                        'parentunit' => empty($parentunit) ? 0 : $parentunit ,
                        'ctype' => $ctype,
                        'conversion_value' => $conversion_value,
                        'description' => $description,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idunit);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idunit='$idunit' and parentunit='$parentunit'";
                $check = $this->m_item->q_unit_param($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(

                        'conversion_value' => $conversion_value,
                        'description' => $description,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idunit);
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
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$id);
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

    function showing_data_unit($id){
        $data = $this->m_item->get_t_unit_view_by_id($id);
        echo json_encode($data);
    }

    function print_labels()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.4'; $versirelease='I.D.A.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.4'";
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
        return $this->template->render('master/item/v_print_labels',$data);
    }

    function show_showlabels_item(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);

        $title = " Barcode Label Data Item";

        $datajson =  base_url("master/item/api_show_showlabels_item/?enc_idlocation=$enc_idlocation") ;
        $datamrt =  base_url("assets/mrt/showlabels_item.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_showlabels_item(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();
        $param="";
        $datamst = $this->m_item->q_mitem($param);

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


/* METODE POST */
    function show_showlabels_item_post(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $idbarang = $this->request->getPost('idbarang');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();


        if (!empty($idbarang)) {
            $i=0;
            foreach ($idbarang as $dd) {
                if ($i == 0){
                    $idbarangx.=" idbarang='$dd'";
                } else { $idbarangx.=" or idbarang='$dd'"; }
                $i++;
            }
            $idbarangxp=" and (".$idbarangx.")";
        } else {  $idbarangxp=""; }

        $param = "".$idbarangxp;
        $enc_param = $this->fiky_encryption->sealed($param);
        $title = " Barcode Label Data Item";

        $datajson =  base_url("master/item/api_show_showlabels_item_post/?enc_param=$enc_param") ;
        $datamrt =  base_url("assets/mrt/showlabels_item.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_showlabels_item_post(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $param=trim($this->fiky_encryption->unseal($this->request->getGet('enc_param')));
        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_item->q_mitem($param);

        header("Content-Type: text/json");
        return $datajson =  json_encode(
            array(
                'info' => array([
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    /* ITEM GROUP */
    public function itemgroup()
    {
        $data['title']="Item Group ";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.3'; $versirelease='I.D.A.3/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('master/item/v_list_item_group',$data);
    }

    function list_mgroup(){
        $list = $this->m_item->get_t_mgroup_view();
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
            "recordsTotal" => $this->m_item->t_mgroup_view_count_all(),
            "recordsFiltered" => $this->m_item->t_mgroup_view_count_filtered(),
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
                $param = " and idgroup='$idgroup'";
                $check = $this->m_item->q_mgroup($param)->getNumRows();
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
                $param = " and idgroup='$idgroup'";
                $check = $this->m_item->q_mgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                $info = array(
                        'nmgroup' => $nmgroup,
                        'grouptype' => $grouptype,
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
        $data = $this->m_item->get_t_mgroup_view_by_id($id);
        echo json_encode($data);
    }
    /* ITEM GROUP */

}
