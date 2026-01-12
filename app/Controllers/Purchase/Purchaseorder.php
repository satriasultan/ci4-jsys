<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class Purchaseorder extends BaseController
{
    public function index()
    {
        $data['title']="MASTER DATA Purchaseorder";
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
        $this->m_Purchaseorder->q_autoinsert_unit();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/Purchaseorder/v_list',$data);
    }

    function list_mPurchaseorder(){
        $list = $this->m_Purchaseorder->get_t_Purchaseorder_view();
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
                      <a class="dropdown-Purchaseorder" href="#" onclick="editPurchaseorder('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-Purchaseorder" href="'.base_url('master/Purchaseorder/del_Purchaseorder').'?var='.trim($lm->id).'" onclick="return confirm('."'Do you want to delete?..  ".trim($lm->idbarang)."'".')"><i class="fa fa-trash"></i> Delete </a>
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
            "recordsTotal" => $this->m_Purchaseorder->t_Purchaseorder_view_count_all(),
            "recordsFiltered" => $this->m_Purchaseorder->t_Purchaseorder_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input(){
        $data['title']="INPUT DATA MASTER Purchaseorder";
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
        return $this->template->render('master/Purchaseorder/v_input',$data);
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
        $data['title']="EDIT DATA MASTER Purchaseorder";
        $var = trim($this->request->getGet('var'));
        $data['type']="EDIT";
        $data['nama']=$nama;
        $data['id']=$var;
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/Purchaseorder/v_input',$data);
    }

    function del_Purchaseorder(){
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
        return redirect()->to(base_url('master/Purchaseorder'));
    }

    function showDetailPurchaseorder () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and id='$var'";
        $dtl = $this->m_Purchaseorder->q_Purchaseorder_param($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'Purchaseorders' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }
    function saveDataPurchaseorder(){
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

    function import_purchase(){
        $data['title']="Import Data Purchase Order ";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.B.1'; $versirelease='I.D.B.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.B.1'";
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
        $kmenu='I.D.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_Purchaseorder->q_tmp_item_po_param($parameter)->getResult();
        $data['adaisi']=$this->m_Purchaseorder->q_tmp_item_po_param($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('purchase/purchaseorder/v_import',$data);

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $this->db->query("delete from sc_tmp.item_po_dtl where inputby='$nama'");


        $path = "./assets/files/Purchaseorder/";
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
                echo '<a href="'.base_url('/master/purchaseorder/import_purchase').'">BACK</a>';
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
                        'docno' => trim($rowData[0][0]),
                        'doctype' => 'PO',
                        'trxdate' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][12])))),
                        'idbarang' => trim($rowData[0][2]),
                        'onhandpo' => trim($rowData[0][3]),
                        'val' => trim($rowData[0][4]),
                        'unit' => trim($rowData[0][5]),
                        'uniqueidobd' => trim($rowData[0][6]),
                        'namasupplier' => trim($rowData[0][7]),
                        'namabarangx' => trim($rowData[0][8]),
                        'description' => trim($rowData[0][10]),
                        'senddate' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][13])))),
                        'valrc' => trim($rowData[0][9]),
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                    );

                    $pm =  " and trim(docno)='".trim($rowData[0][0])."' and trim(uniqueidobd)='".trim($rowData[0][5])."'";
                    $cek = $this->m_Purchaseorder->q_tmp_item_po_param($pm)->getNumRows();
                    if($cek <= 0 and trim($rowData[0][0]) != '') {
                        $builder = $this->db->table("sc_tmp.item_po_dtl");
                        if ($builder->insert($data))
                        {
                            $no++;
                        }

                    }

                }
                return redirect()->to(base_url('/purchase/purchaseorder/import_purchase'));
            }
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.item_po_dtl where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.B.1');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.B.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/purchase/purchaseorder/import_purchase'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.item_po_dtl set status='F' where inputby='$nama'");

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.B.1');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.B.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/purchase/purchaseorder/import_purchase'));
    }


    public function listpurchase()
    {
        $data['title']="List Purchasing Order Final ";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.B.2'; $versirelease='I.D.B.2/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('purchase/purchaseorder/v_list_purchaseorder',$data);
    }

    function list_po(){
        $list = $this->m_Purchaseorder->get_t_trx_item_po_final_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;

            $row[] = '<div class="text-wrap width-90">'. $lm->docno .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->trxdate1 .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->idbarang .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->nmbarang .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->unit .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->onhandpo .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->onhandtranspo .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->namasupplier .'</div>';
            $row[] = '<div class="text-wrap width-150">'. $lm->description .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->senddate1 .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->nmstatus .'</div>';

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_Purchaseorder->t_trx_item_po_final_dtl_view_count_all(),
            "recordsFiltered" => $this->m_Purchaseorder->t_trx_item_po_final_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



    /* Purchaseorder GROUP */


    public function outstanding()
    {
        $data['title']="List Outstanding Purchasing Order ";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.B.3'; $versirelease='I.D.B.3/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('purchase/purchaseorder/v_list_outstanding',$data);
    }

    function list_outstanding(){
        $list = $this->m_Purchaseorder->get_t_trx_item_po_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<div class="">
              <a class="btn btn-lg btn-primary" href="javascript:void(0)" title="Update" onclick="update_listpo('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
              <!--a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_listpo('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a> </div-->';
            $row[] = '<div class="text-wrap width-90">'. $lm->docno .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->trxdate1 .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->idbarang .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->nmbarang .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->unit .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->onhandpo .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->onhandtranspo .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->sisapo .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->kotor .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->namasupplier .'</div>';
            $row[] = '<div class="text-wrap width-150">'. $lm->description .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->senddate1 .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->nmstatus .'</div>';

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_Purchaseorder->t_trx_item_po_dtl_view_count_all(),
            "recordsFiltered" => $this->m_Purchaseorder->t_trx_item_po_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function saveListPo(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $docno = strtoupper(trim($dataprocess->docno));
            $idbarang= strtoupper(trim($dataprocess->idbarang));
            $onhandpo = trim($dataprocess->onhandpo);
            $onhandtranspo = trim($dataprocess->onhandtranspo);

            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_trx.item_po_dtl');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                //no input required

            } else if ($type==='UPDATE') {
                $param = " and id='$id' and status in ('A','S')";
                $check = $this->m_Purchaseorder->q_trx_item_po_param($param)->getNumRows();
                //check data ganda
                if ($check > 0 and (int)$onhandtranspo <= (int)$onhandpo) {
                    $info = array(
                        'onhandpo' => $onhandpo,
                        'onhandtranspo' => $onhandtranspo,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$docno);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                } else {
                    $getResult = array('status' => false, 'messages' => 'Oops Po Sudah Final Tidak Dapat Di Ubah!!!');
                    echo json_encode($getResult);
                }

            } else if ($type==='DELETE') {
                $builder = $this->db->table('sc_trx.item_po_mst');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$docno);
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

    function showing_data_po($id){
        $data = $this->m_Purchaseorder->get_t_trx_item_po_dtl_view_by_id($id);
        echo json_encode($data);
    }


    function createpp(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.B.5'; $versirelease='I.D.B.5/BETA.001'; $releasedate=date('2023-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.B.5'";
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
        return $this->template->render('purchase/pp/v_pp',$data);
    }

}
