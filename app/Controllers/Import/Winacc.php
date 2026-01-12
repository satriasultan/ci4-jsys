<?php

namespace App\Controllers\Import;

use App\Controllers\BaseController;

class Winacc extends BaseController
{
    function index(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.F.1'; $versirelease='I.D.F.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.F.1'";
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

    function pp(){
        $data['title']="Import PP Dari Winacc";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.F.1'; $versirelease='I.D.F.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.F.1'";
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
        $kmenu='I.D.F.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        /*parameter isi tanggal range aja biar tidak terlalu berat*/
        //$parameter = " and trim(username)='fiky'";
        $parameter = " ";
        $data['list_detail']=$this->m_winacc->q_im_lap_pp_win($parameter)->getResult();
        $data['adaisi']=$this->m_winacc->q_im_lap_pp_win($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('import/winacc/v_import_pp',$data);

    }

    function proses_upload_pp() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        //$this->db->query("delete from sc_tmp.item_lbm_wacc_dtl where inputby='$nama'");
        $dtlloc = $this->m_location->q_mlocation(" and idlocation='$loccode'")->getRowArray();

        $path = "./assets/files/Purchaseorder/PP/";
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
                echo '<a href="'.base_url('/import/winacc/pp').'">BACK</a>';
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
                //echo $JUDUL[0][0].'</br>';
                if (trim($JUDUL[0][0])==='PP') {
                    $no=1;
                    for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                            NULL,
                            TRUE,
                            FALSE);

                        //  Insert row data array into your database of choice here
                        $data = array(
                            //'idlocation' => $loccode,
                            'pp' => trim($rowData[0][0]),
                            'jurnal' => trim($rowData[0][1]),
                            'date_' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][17])))),
                            'username' => trim($rowData[0][3]),
                            'brg' => trim($rowData[0][4]),
                            'qty' => trim($rowData[0][5]),
                            'lns' => trim($rowData[0][6]),
                            'gdg' => trim($rowData[0][7]),
                            'nama_user' => trim($rowData[0][8]),
                            'nama_barang' => trim($rowData[0][9]),
                            'unit' => trim($rowData[0][10]),
                            'spec' => trim($rowData[0][11]),
                            'job' => trim($rowData[0][12]),
                            'rem_pp' => trim($rowData[0][13]),
                            'rem_pd' => trim($rowData[0][14]),
                            'date_pakai' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][18])))),
                            'uniqueidpd' => trim($rowData[0][16]),
                            'inputby'=>$nama,
                            'inputdate'=>date("Y-m-d H:i:s"),
                            'status' => 'I',
                        );
                        $parameter = " and pp='".trim($rowData[0][0])."' and jurnal='".trim($rowData[0][1])."' and uniqueidpd='".trim($rowData[0][16])."'";
                        //$parameter2 = " and brg='".trim($rowData[0][4])."' and qty='".trim($rowData[0][5])."'";
                        //$parameter3 = " and rem_pp='".trim($rowData[0][13])."' and rem_pd='".trim($rowData[0][14]);
                        $checking = $this->m_winacc->q_im_lap_pp_win($parameter)->getNumRows();
                        if (trim($rowData[0][0]) !== '' and $checking<=0) {
                            $builder = $this->db->table("sc_im.lap_pp_win");
                            $builder->insert($data);
                        }
                        $no++;
                    }
                    return redirect()->to(base_url('/import/winacc/pp'));
                } else {
                    echo 'INVALID FAILED FILE';
                    echo '<a href="'.base_url('/import/winacc/pp').'">BACK</a>';
                }


            }
        }
    }


    function po(){
        $data['title']="Import PO Dari Winacc";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.F.2'; $versirelease='I.D.F.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.F.2'";
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
        $kmenu='I.D.F.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        /*parameter isi tanggal range aja biar tidak terlalu berat*/
        //$parameter = " and trim(username)='fiky'";
        $parameter = " ";
        $data['list_detail']=$this->m_winacc->q_im_lap_po_win($parameter)->getResult();
        $data['adaisi']=$this->m_winacc->q_im_lap_po_win($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('import/winacc/v_import_po',$data);

    }

    function proses_upload_po() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $dtlloc = $this->m_location->q_mlocation(" and idlocation='$loccode'")->getRowArray();

        $path = "./assets/files/Purchaseorder/PO/";
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
                echo '<a href="'.base_url('/import/winacc/po').'">BACK</a>';
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
                //echo $JUDUL[0][0].'</br>';
                if (trim($JUDUL[0][0])==='OBL') {
                    $no=1;
                    for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                            NULL,
                            TRUE,
                            FALSE);

                        //  Insert row data array into your database of choice here
                        $data = array(
                            'obl' => trim($rowData[0][0]),
                            'jurnal' => trim($rowData[0][1]),
                            'date_' =>  date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][18])))),
                            'sub' => trim($rowData[0][3]),
                            'brg' => trim($rowData[0][4]),
                            'gdgobl' => trim($rowData[0][5]),
                            'qty' => trim($rowData[0][6]),
                            'lns' => trim($rowData[0][7]),
                            'val' => trim($rowData[0][8]),
                            'job' => trim($rowData[0][9]),
                            'unit' => trim($rowData[0][10]),
                            'uniqueidobd' => trim($rowData[0][11]),
                            'nama_supplier' => trim($rowData[0][12]),
                            'nama_barang' => trim($rowData[0][13]),
                            'jobname' => trim($rowData[0][14]),
                            'priceobl' => trim($rowData[0][15]),
                            'rempd' => trim($rowData[0][16]),
                            'tglkirim' =>  date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][19])))),
                            'inputby'=>$nama,
                            'inputdate'=>date("Y-m-d H:i:s"),
                            'status' => 'I',
                        );
                        $parameter = " and obl='".trim($rowData[0][0])."' and jurnal='".trim($rowData[0][1])."' and uniqueidobd='".trim($rowData[0][16])."'";
                        //$parameter2 = " and brg='".trim($rowData[0][4])."' and qty='".trim($rowData[0][5])."'";
                        //$parameter3 = " and rem_pp='".trim($rowData[0][13])."' and rem_pd='".trim($rowData[0][14]);
                        $checking = $this->m_winacc->q_im_lap_po_win($parameter)->getNumRows();
                        if (trim($rowData[0][0]) !== '' and $checking<=0) {
                            $builder = $this->db->table("sc_im.lap_po_win");
                            $builder->insert($data);
                        }
                        $no++;
                    }
                    return redirect()->to(base_url('/import/winacc/po'));
                } else {
                    echo 'INVALID FAILED FILE';
                    echo '<a href="'.base_url('/import/winacc/po').'">BACK</a>';
                }

            }
        }
    }

    function penerimaan(){
        $data['title']="Import Laporan Penerimaan Barang Dari Winacc";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.F.3'; $versirelease='I.D.F.3/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.F.3'";
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
        $kmenu='I.D.F.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        /*parameter isi tanggal range aja biar tidak terlalu berat*/
        //$parameter = " and trim(username)='fiky'";
        $parameter = " ";
        $data['list_detail']=$this->m_winacc->q_im_lap_penerimaan_win($parameter)->getResult();
        $data['adaisi']=$this->m_winacc->q_im_lap_penerimaan_win($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('import/winacc/v_import_penerimaan_win',$data);

    }

    function proses_upload_penerimaan() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $dtlloc = $this->m_location->q_mlocation(" and idlocation='$loccode'")->getRowArray();

        $path = "./assets/files/Purchaseorder/PENERIMAAN/";
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
                echo '<a href="'.base_url('/import/winacc/penerimaan').'">BACK</a>';
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
                //echo $JUDUL[0][0].'</br>';
                if (trim($JUDUL[0][0])==='TBL') {
                    $no=1;
                    for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                            NULL,
                            TRUE,
                            FALSE);

                        //  Insert row data array into your database of choice here
                        $data = array(
                            'tbl' => trim($rowData[0][0]),
                            'date_' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][38])))),
                            'duedate' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][39])))),
                            'cur' => trim($rowData[0][3]),
                            'kurs' => trim($rowData[0][4]),
                            'sub' => trim($rowData[0][5]),
                            'subname' => trim($rowData[0][6]),
                            'slsm' => trim($rowData[0][7]),
                            'slsmname' => trim($rowData[0][8]),
                            'brg' => trim($rowData[0][9]),
                            'brgname' => trim($rowData[0][10]),
                            'qty' => trim($rowData[0][11]),
                            'qtybonus' => trim($rowData[0][12]),
                            'valbonus' => trim($rowData[0][13]),
                            'valwithdiscval' => trim($rowData[0][14]),
                            'discval' => trim($rowData[0][15]),
                            'ppnval' => trim($rowData[0][16]),
                            'job' => trim($rowData[0][17]),
                            'jobname' => trim($rowData[0][18]),
                            'unit' => trim($rowData[0][19]),
                            'uniqueid' => trim($rowData[0][20]),
                            'posisistock' => trim($rowData[0][21]),
                            'nofaktur' => trim($rowData[0][22]),
                            'biaya' => trim($rowData[0][23]),
                            'biaya2' => trim($rowData[0][24]),
                            'disc1' => trim($rowData[0][25]),
                            'disc2' => trim($rowData[0][26]),
                            'pcl' => trim($rowData[0][27]),
                            'pclname' => trim($rowData[0][28]),
                            'rem' => trim($rowData[0][29]),
                            'npwp' => trim($rowData[0][30]),
                            'nobatch' => trim($rowData[0][31]),
                            'expired_date' => trim($rowData[0][32]),
                            'kadar' => trim($rowData[0][33]),
                            'date_lulus' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][40])))),
                            'penanggungjw' => trim($rowData[0][35]),
                            'nomor' => trim($rowData[0][36]),
                            'nomor_po' => trim($rowData[0][37]),
                            'inputby'=>$nama,
                            'inputdate'=>date("Y-m-d H:i:s"),
                            'status' => 'I',
                        );
                        $parameter = " and tbl='".trim($rowData[0][0])."' and nomor_po='".trim($rowData[0][1])."' and uniqueid='".trim($rowData[0][16])."' and nomor='".trim($rowData[0][16])."'";
                        //$parameter2 = " and brg='".trim($rowData[0][4])."' and qty='".trim($rowData[0][5])."'";
                        //$parameter3 = " and rem_pp='".trim($rowData[0][13])."' and rem_pd='".trim($rowData[0][14]);
                        $checking = $this->m_winacc->q_im_lap_penerimaan_win($parameter)->getNumRows();
                        if (trim($rowData[0][0]) !== '' and $checking<=0) {
                            $builder = $this->db->table("sc_im.lap_penerimaan_win");
                            $builder->insert($data);
                        }
                        $no++;
                    }
                    return redirect()->to(base_url('/import/winacc/penerimaan'));
                } else {
                    echo 'INVALID FAILED FILE';
                    echo '<a href="'.base_url('/import/winacc/penerimaan').'">BACK</a>';
                }

            }
        }
    }


    function syncronize(){
        $data['title']="SINKRONISASI DATA & GENERATE LAPORAN";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.F.4'; $versirelease='I.D.F.4/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.F.4'";
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
        $kmenu='I.D.F.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        /*parameter isi tanggal range aja biar tidak terlalu berat*/
        //$parameter = " and trim(username)='fiky'";


        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('import/winacc/v_syncronize_laporan',$data);

    }

}
