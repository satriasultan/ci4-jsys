<?php

namespace App\Controllers\Stock;

use App\Controllers\BaseController;

class Balance extends BaseController
{
    public function index()
    {

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.C.1'; $versirelease='I.D.C.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.1'";
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
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/balance/v_list',$data);
    }

    function list_balance(){
        $list = $this->m_balance->get_t_balance_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmgroup;
            $row[] = $lm->idbarang;

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_balance_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_balance_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function allocation_map(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.2'; $versirelease='I.D.C.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.2'";
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
        return $this->template->render('stock/balance/v_alocation_map',$data);
    }

    /* Api Sti Test Trial */

    function show_sti_allocation_map(){
        $nama = trim($this->session->get('nama'));
        $docno=trim($this->request->getGet('docno'));

        $title = " Form Alocation Map ";
        $datajson =  base_url("stock/balance/api_sti_allocation_map/?var=$docno") ;
        $datamrt =  base_url("assets/mrt/form_allocation_map.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_sti_allocation_map(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $varGet=trim($this->request->getGet('var'));

        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= "";
        } else {
            $paramglobal1= "";
        }

        $parameterx = " and idlocation='$loccode' and sisa > 0";

        $param=" and (idbarang = '$varGet' $paramglobal1 $parameterx ) order by idbarang asc";
        $param_row=" and (idbarang = '$varGet') order by idbarang asc";

        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_balance->q_item_param($param_row);
        $datadtl = $this->m_balance->q_stkgdw($param);
        $this->db->query("select sc_trx.pr_t_v_allocation_map('$nama','$loccode','$varGet');");
        $datadtlnew = $this->m_balance->q_t_v_allocation_map(" and docno='$nama'");


        header("Content-Type: text/json");
        return json_encode(
            array(

                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
                'dtlnew' => $datadtlnew->getResult(),
            ), JSON_PRETTY_PRINT);
    }






    /* End Api Sti Test Trial */

    function apiAlocationMap(){
        $branch = $this->session->get('branch');
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        $pglobal= trim($this->request->getPost('_paramglobal_'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= "";
        } else {
            $paramglobal1= "";
        }

        $parameterx = " and idlocation='$loccode' and sisa > 0";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idbarang = '$pglobal' $paramglobal1 $parameterx ) order by idbarang asc";
        $param_row=" and (idbarang = '$pglobal') order by idbarang asc";
        $getResult = $this->m_balance->q_stkgdw($param)->getResult();
        $getRow = $this->m_item->q_item_param($param_row)->getRow();
        $count = $this->m_balance->q_stkgdw($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'dataitems' => $getRow,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function import_stock(){
        $data['title']="Import Opening Stock ";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.C.5'; $versirelease='I.D.C.5/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.5'";
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
        $kmenu='I.D.C.5';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_balance->q_openingstock_tmp_param($parameter)->getResult();
        $data['adaisi']=$this->m_balance->q_openingstock_tmp_param($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('stock/balance/v_import',$data);

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $this->db->query("delete from sc_tmp.import_opening_stock where inputby='$nama'");

        $path = "./assets/files/item/openingstock/";
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
                echo '<a href="'.base_url('/stock/balance/import_stock').'">BACK</a>';
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
                        'idlocation' => trim($rowData[0][2]),
                        'idarea' => trim($rowData[0][3]),
                        'qty' => trim($rowData[0][4]),
                        'unit' => trim($rowData[0][5]),
                        'trxdate' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][6])))),
                        'description' => trim($rowData[0][7]),
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                    );

                    //$pm =  " and trim(idbarang)='".trim($rowData[0][0])."'";
                    //$cek = $this->m_item->q_item_param($pm)->getNumRows();
                    if(trim($rowData[0][0]) != '') {
                        $builder = $this->db->table("sc_tmp.import_opening_stock");
                        $builder->insert($data);
                    }
                    $no++;
                }
                return redirect()->to(base_url('/stock/balance/import_stock'));
            }
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.import_opening_stock where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.A.2');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.C.5',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/stock/balance/import_stock'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $param = " and (nmbarang in ('UNIDENTIFIED') or nmlocation in ('UNIDENTIFIED') or nmarea in ('UNIDENTIFIED'))";
        $cek = $this->m_balance->q_openingstock_tmp_param($param)->getNumRows();
        //$cek2 = $this->m_balance->q_openingstock_tmp_param($param)->getNumRows();

        if ($cek > 0) {
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.D.C.5');
            $builder_trxerror->delete();
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 2,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.D.C.5',
            );
            $builder_trxerror->insert($infotrxerror);
        } else {
            $this->db->query("update sc_tmp.import_opening_stock set status='F' where inputby='$nama'");
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.D.C.5');
            $builder_trxerror->delete();
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.D.C.5',
            );
            $builder_trxerror->insert($infotrxerror);
        }

        return redirect()->to(base_url('/stock/balance/import_stock'));
    }

    function moving(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.3'; $versirelease='I.D.C.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.3'";
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
        return $this->template->render('stock/balance/v_moving',$data);
    }

    function list_moving(){
        $list = $this->m_balance->get_t_item_move_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (trim($lm->status)==='P') {
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="'.base_url('stock/balance/cancelmoveitem').'?var='.trim($lm->docno).'" onclick="return confirm('."'Do you wan cancel this transactional ?..  ".trim($lm->docno)."'".')"><i class="fa fa-close"></i> Cancel </a>
                    </div>
            </div>
             ';
            }
            if (trim($lm->status)==='C') {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                     
                    </div>
            </div>
             ';
            }
            $row[] = $lm->docno;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->onhandmove;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmareamove;
            $row[] = $lm->nmstatus;
            $row[] = $lm->idbarang;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_item_move_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_item_move_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function cancelmoveitem(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_trx.item_move');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $info = array('status' => 'C');
        $builder->where('docno',$id);
        $builder->update($info);
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.C.3');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.D.C.3',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('stock/balance/moving'));
    }

    function input_moving(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.3'; $versirelease='I.D.C.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.3'";
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
        return $this->template->render('stock/balance/v_input_moving',$data);
    }

    function list_item(){
        $branch = trim($this->session->get('branch'));
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idunit='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idunit='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $parameterx = " and coalesce(chold,'NO')!='YES'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idbarang like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) or (nmbarang like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) order by nmbarang asc";
        $getResult = $this->m_balance->q_item_param($param)->getResult();
        $count = $this->m_balance->q_item_param($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_area(){
        $branch = trim($this->session->get('branch'));
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idunit='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idunit='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $parameterx = " and coalesce(idlocation,'')='$loccode'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idarea like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) or (nmarea like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) order by nmarea asc";
        $getResult = $this->m_location->q_marea($param)->getResult();
        $count = $this->m_location->q_marea($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }


    function list_balance_moving(){
        $list = $this->m_balance->get_t_balance_moving_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
                <a class="btn btn-md btn-primary" href="#"  title="Move" onclick="moving_stock('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
             ';
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmgroup;
            $row[] = $lm->idbarang;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_balance_moving_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_balance_moving_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_detail_moving($id){
        $param = " and id='$id'";
        $data = $this->m_balance->q_balance_moving($param)->getRow();
        echo (json_encode($data,JSON_PRETTY_PRINT));
    }

    function saveMovingStock(){
        $nama = trim($this->session->get('nama'));

            $id = trim($this->request->getPost('id'));
            $idlocationold = strtoupper(trim($this->request->getPost('idlocationold')));
            $idareaold = strtoupper(trim($this->request->getPost('idareaold')));
            $batch = strtoupper(trim($this->request->getPost('batch')));
            $idbarangmove = strtoupper(trim($this->request->getPost('idbarangmove')));
            $nmbarangmove = strtoupper(trim($this->request->getPost('nmbarangmove')));
            $qty = strtoupper(trim($this->request->getPost('qty')));
            $unit = strtoupper(trim($this->request->getPost('unit')));
            $nmarea = strtoupper(trim($this->request->getPost('nmarea')));
            $qtymove = strtoupper(trim($this->request->getPost('qtymove')));
            $unitmove = strtoupper(trim($this->request->getPost('unitmove')));
            $trxdate = strtoupper(trim($this->request->getPost('trxdate')));
            $idareamove = strtoupper(trim($this->request->getPost('idareamove')));
            $description = strtoupper(trim($this->request->getPost('description')));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($this->request->getPost('type'));
            $builder = $this->db->table('sc_trx.item_move');
            // CHECK INPUT TYPE
             if ($type==='UPDATE') {
                 if ($idareaold === $idareamove) {
                     $getResult = array('status' => false, 'messages' => 'Data Fail Must Be Different Area Move!!!');
                     echo json_encode($getResult);
                 } else {
                     $info = array(
                         'docno' => $inputby,
                         'docref' => 'MVSTK',
                         'idlocation' => $idlocationold,
                         'idarea' => $idareaold,
                         'batch' => $batch,
                         'idbarang' => $idbarangmove,
                         'onhand' => $qty,
                         'idlocationmove' => $idlocationold,
                         'idareamove' => $idareamove,
                         'onhandmove' => $qtymove,
                         'unit' => $unit,
                         'lasttrxdate' => date('Y-m-d',strtotime($trxdate)),
                         'subunit' => '',
                         'description' => $description,
                         'status' => '',
                         'inputby' => $inputby,
                         'inputdate' => $inputdate,
                     );
                     if ((int)$qtymove <= (int)$qty) {
                         if ($builder->insert($info)) {
                             $paramerror=" and userid='$nama' and modul='I.D.C.3'";
                             $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();

                             $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtlerror['nomorakhir1']);
                             echo json_encode($getResult);
                         } else {
                             $getResult = array('status' => false, 'messages' => 'Data Fail Or Item Must Be Low or Equal Than Older, Try Again');
                             echo json_encode($getResult);
                         }
                     } else {
                         $getResult = array('status' => false, 'messages' => 'Data Fail Or Item Must Be Low or Equal Than Older Or Area Coulnot, Try Again');
                         echo json_encode($getResult);
                     }
                 }


            } else if ($type==='DELETE') {

//                $builder->where('id' , $id);
//                if ($builder->delete()) {
//                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idlocation);
//                    echo json_encode($getResult);
//                } else {
//                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
//                    echo json_encode($getResult);
//                }
            }
        
    }

    function transfers(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.6'; $versirelease='I.D.C.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.6'";
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
        $dtl = $this->m_balance->q_tmp_item_transfers_mst($param);

        if ($dtl->getNumRows()>0) {
            $title = "PERINGATAN !!!";
            $urlclear = base_url('stock/balance/clearEntryTransfers');
            $urlnext = base_url('stock/balance/input_stock_transfers');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/balance/v_transfers',$data);
    }

    function list_transfers(){
        $list = $this->m_balance->get_t_trx_transfers_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (trim($lm->status)==='P') {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href='."'". base_url('stock/balance/update_stock_transfers').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Update Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Update </a>
                      <a class="dropdown-item" href='."'". base_url('stock/balance/cancel_stock_transfers').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Batalkan Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Cancel </a>
                      <div class="dropdown-divider"></div>
                      
                      <a class="dropdown-item" href='."'". base_url('stock/balance/detail_stock_transfers').'/'.'?docno='.bin2hex(trim($lm->docno))."'".' onclick="return confirm('."'".'Detail Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-bars"></i> Detail </a>
                    </div>
            </div>
             ';
            } else {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href='."'". base_url('stock/balance/detail_stock_transfers').'/'.'?docno='.bin2hex(trim($lm->docno))."'".' onclick="return confirm('."'".'Detail Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-bars"></i> Detail </a>
                    </div>
            </div>
             ';
            }

            $row[] = $lm->docno;
            $row[] = $lm->docref;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->onhandtrans;
            $row[] = $lm->unit;
            $row[] = $lm->lasttrxdate1;
            $row[] = $lm->nmlocationfrom;
            $row[] = $lm->nmlocationto;
            $row[] = $lm->nmstatus;
            $row[] = $lm->inputby;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_trx_transfers_dtl_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_trx_transfers_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function input_stock_transfers()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.6'; $versirelease='I.D.C.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.6'";
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
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_balance->q_tmp_item_transfers_mst($param)->getNumRows();

        if (!empty($nama) and $dtl<=0) {
            $info = array (
                'docno' => $nama,
                'docref' => '',
                'idlocation' => $loccode,
                'docdate' => date('Y-m-d'),
                'status' => 'I',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.item_transfers_mst');
            $builder->insert($info);
        } else if (!empty($nama) and $dtl>0) {
            //lanjutkan
        } else {
            return redirect()->to(base_url('stock/balance/transfers'));
        }
        $data['typeform'] = 'INPUT';
        $data['dtldata'] = $this->m_balance->q_tmp_item_transfers_mst($param)->getRowArray();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/balance/v_input_stock_transfers',$data);
    }
    function update_stock_transfers()
    {
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $param = " and docno='$docno' and coalesce(holdby,'')!='$nama' and coalesce(holdrow,'')='YES'";
        $dtl = $this->m_balance->q_trx_item_transfers_mst($param)->getNumRows();

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder = $this->db->table('sc_trx.item_transfers_mst');
        if ($dtl>0){
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.D.C.6');
            $this->db->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'I.D.C.6',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('stock/balance/transfers'));
        } else {
            $info = array(

                'docnotmp' => $docno,
                'holdby' => $nama,
                'holdrow' => 'YES',
                'status' => 'E',
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),
            );
            $builder->where('docno',$docno);
            if ($builder->update( $info)) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/input_stock_transfers'));
            }
        }
    }
    function cancel_stock_transfers()
    {
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $param = " and docno='$docno' and coalesce(holdby,'')!='$nama' and coalesce(holdrow,'')='YES'";
        $dtl = $this->m_balance->q_trx_item_transfers_mst($param)->getNumRows();

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder = $this->db->table('sc_trx.item_transfers_mst');
        if ($dtl>0){
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.D.C.6');
            $this->db->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'I.D.C.6',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('stock/balance/transfers'));
        } else {
            $info = array(

                'docnotmp' => '',
                'holdby' => '',
                'holdrow' => '',
                'status' => 'C',
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),
            );
            $builder->where('docno',$docno);
            if ($builder->update( $info)) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/transfers'));
            }
        }
    }
    function detail_stock_transfers()
    {
        $docno = trim(hex2bin($this->request->getGet('docno')));
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.6'; $versirelease='I.D.C.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.6'";
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
        $param = " and coalesce(docno,'')='$docno'";
        $data['dtlmst'] = $this->m_balance->q_trx_item_transfers_mst($param)->getRowArray();
        $data['listdtl'] = $this->m_balance->q_trx_item_transfers_dtl($param)->getResult();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/balance/v_detail_stock_transfers',$data);
    }
    function list_tmp_transfers_dtl(){
        $nama=trim($this->session->get('nama'));
        $mst = $this->m_balance->q_tmp_item_transfers_mst(" and docno='$nama'")->getRowArray();
        $status = trim($mst['status']);
        $list = $this->m_balance->get_t_tmp_transfers_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if ($status === 'E') {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="edit_transfers('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                    </div>
            </div>
             ';
            } else {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="edit_transfers('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_transfers('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';
            }


            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->onhandtrans;
            $row[] = $lm->unit;
            $row[] = $lm->nmlocationto;
            $row[] = $lm->description;
            $row[] = $lm->idbarang;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_tmp_transfers_dtl_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_tmp_transfers_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_transfers_mst(){
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$nama'";
        $data = $this->m_balance->q_tmp_item_transfers_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_transfers_dtl($id){
        $data = $this->m_balance->q_tmp_item_transfers_dtl(" and id='$id'")->getRow();
        echo json_encode($data);
    }

    function showing_stkgdw($id){
        $data = $this->m_balance->q_stkgdw(" and id='$id'")->getRow();
        echo json_encode($data);
    }


    function verifikasi_mst_stock_transfers(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $idlocation = trim($this->request->getPost('idlocation'));
        $idlocationtrans = trim($this->request->getPost('idlocationtrans'));
        $docdate = $this->request->getPost('docdate');
        $docref = $this->request->getPost('docref');
        $description = $this->request->getPost('description');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $dtl = $this->m_location->q_mlocation(" and idlocation='$idlocationtrans'")->getRowArray();

        if (!empty($idlocationtrans) and ($idlocation!==$idlocationtrans)) {
            $info = array (
                'docref' => $docref,
                'idlocation' => $loccode,
                'idlocationtrans' => $idlocationtrans,
                'idareatrans' => trim($dtl['idarea_default']),
                'description' => $description,
                'docdate' => date('Y-m-d',strtotime($docdate)),
                'status' => 'E',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.item_transfers_mst');
            $builder->where('docno',$nama);
            $builder->update($info);


            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.D.C.6');
            $builder_trxerror->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.D.C.6',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('stock/balance/input_stock_transfers'));
        } else {

            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.D.C.6');
            $builder_trxerror->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.D.C.6',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('stock/balance/input_stock_transfers'));
        }


    }
    function clearEntryTransfers()
    {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_balance->q_tmp_item_transfers_mst($param);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_transfers_mst');
        $builder_dtl = $this->db->table('sc_tmp.item_transfers_dtl');
        $buildertx = $this->db->table('sc_trx.item_transfers_mst');
        $builderdtltx = $this->db->table('sc_trx.item_transfers_dtl');
        //PROSES INPUT
        if ($status==='I') {
            $builder->where('docno', $nama);
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno', $nama);
                $builder_dtl->delete();

                $builder->where('docno', $nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/transfers'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else if ($status==='E')
            {   $builder->where('docno',$nama);
                if ($builder_dtl->update(array('status' => 'F'))) {
                    $builder_dtl->where('docno',$nama);
                    $builder_dtl->delete();

                    $builder->where('docno',$nama);
                    $builder->delete();

                    $buildertx->where('docno',trim($dtl->getRowArray()['docnotmp']));
                    $buildertx->update(array(
                        'status' => 'P',
                        'holdrow' => '',
                        'holdby' => '',
                    ));
                    $builderdtltx->where('docno',trim($dtl->getRowArray()['docnotmp']));
                    $builderdtltx->update(array(
                        'status' => 'P',
                        'holdrow' => '',
                        'holdby' => '',
                    ));
                    $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                    echo json_encode($result);
                    return redirect()->to(base_url('stock/balance/transfers'));
                } else {
                    $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                    echo json_encode($result);
                }
        } else {
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno',$nama);
                $builder_dtl->delete();

                $builder->where('docno',$nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/transfers'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        }
    }

    function saveDetailTransfer(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            $idlocationfrom = trim($dataprocess->idlocationfrom);
            $idlocationto = trim($dataprocess->idlocationto);
            $idbarangmove = trim($dataprocess->idbarangmove);
            $nmbarangmove = trim($dataprocess->nmbarangmove);
            $qtyonhand = trim($dataprocess->qtyonhand);
            $unitonhand = trim($dataprocess->unitonhand);
            $idareafrom = trim($dataprocess->idareafrom);
            $nmareafrom = trim($dataprocess->nmareafrom);
            $idareato = trim($dataprocess->idareato);
            $nmareato = trim($dataprocess->nmareato);
            $qtytransfers = trim($dataprocess->qtytransfers);
            $unittransfers = trim($dataprocess->unittransfers);
            $description = strtoupper(trim($dataprocess->description));
            $batch = trim($dataprocess->batch);

            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            $builder = $this->db->table('sc_tmp.item_transfers_dtl');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $parammst = " and docno='$nama'";
                $param = " and docno='$nama' and idbarang='$idbarangmove' and idlocation='$idlocationfrom' and idarea='$idareafrom' and batch='$batch'";
                $dtlmst = $this->m_balance->q_tmp_item_transfers_mst($parammst)->getRowArray();
                $check = $this->m_balance->q_tmp_item_transfers_dtl($param)->getNumRows();
                //check data ganda
                if ($check > 0 or ($qtytransfers>$qtyonhand) or ($qtytransfers<0) ) {
                    $getResult = array('status' => false, 'messages' => 'Oops ready in use, use edit or qty not acceptable');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        //'parentunit' => empty($parentunit) ? 0 : $parentunit ,
                        'id' => 0,
                        'docno' => $nama,
                        'docref' => $dtlmst['docref'],
                        'idlocation' => $idlocationfrom,
                        'idarea' => $idareafrom,
                        'idbarang' => $idbarangmove,
                        'onhand' => $qtyonhand,
                        'idlocationtrans' => $dtlmst['idlocationtrans'],
                        'idareatrans' => $dtlmst['idareatrans'],
                        'onhandtrans' => $qtytransfers,
                        'unit' => $unittransfers,
                        'subunit' => '',
                        'lasttrxdate' => date('Y-m-d H:i:s',strtotime($dtlmst['docdate'])),
                        'description' => $description,
                        'status' => 'I',
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                        'batch' => $batch,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: ');
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idbarang='$idbarangmove' and idlocation='$idlocationfrom' and idarea='$idareafrom' and batch='$batch'";
                $dtl = $this->m_balance->q_tmp_item_transfers_dtl($param)->getRowArray();
                if ($qtytransfers<=0 or (($dtl['sisa'] + $dtl['onhandtrans']) - $qtytransfers )<0) {
                    $getResult = array('status' => false, 'messages' => 'Oops ready in use, use edit or qty not acceptable');
                    echo json_encode($getResult);
                } else {
                    $infoupdate = array(
                        'onhandtrans' => $qtytransfers,
                        'description' => $description,
                        'status' => 'E',
                    );
                    $builder->where('docno',$nama);
                    $builder->where('id',$id);
                    if ($builder->update($infoupdate)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: ');
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='DELETE') {
                $iupdate = array(
                    'status' => 'C',
                );
                $builder->where('docno',$nama);
                $builder->where('id',$id);
                if ($builder->update($iupdate)) {
                    $builder->where('docno',$nama);
                    $builder->where('id',$id);
                    $builder->delete();

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

    function finalEntryTransfers(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_balance->q_tmp_item_transfers_mst($param);
        $cek = $this->m_balance->q_tmp_item_transfers_dtl($param);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_transfers_mst');


        if ($status==='E' and $cek->getNumRows() > 0)
        {
            $info = array(
                'status' => 'F'
            );
            $builder->where('docno',$nama);
            $builder->update($info);
            return redirect()->to(base_url('/stock/balance/transfers'));
        } else {
            return redirect()->to(base_url('/stock/balance/input_stock_transfers'));
        }

    }

    function list_balance_transfers(){
        $list = $this->m_balance->get_t_balance_moving_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = '
                <a class="btn btn-md btn-primary" href="#"  title="Add Item" onclick="add_transfers('."'".trim($lm->id)."'".')"><i class="fa fa-plus"></i> </a>
             ';

            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmgroup;
            $row[] = $lm->idbarang;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_balance_moving_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_balance_moving_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    /* START AJUSTMENT ITEMS BATAL  DIIKUTKAN TRASNSAKSI IN & OUT */
    function ajustment(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.4'; $versirelease='I.D.C.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.4'";
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
        $dtl = $this->m_balance->q_tmp_item_ajustment_mst($param);

        if ($dtl->getNumRows()>0) {
            $title = "PERINGATAN !!!";
            $urlclear = base_url('stock/balance/clearEntryAjustment');
            $urlnext = base_url('stock/balance/input_stock_ajustment');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/balance/v_ajustment',$data);
    }
    function list_ajustment(){
        $list = $this->m_balance->get_t_trx_transfers_mst_view();
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
                      <a class="dropdown-item" href='."'". base_url('stock/balance/update_stock_transfers').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Update Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Update </a>
                      <a class="dropdown-item" href='."'". base_url('stock/balance/cancel_stock_transfers').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Batalkan Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Cancel </a>
                      <div class="dropdown-divider"></div>
                      
                      <a class="dropdown-item" href="#" onclick="detail_trxtransfers('."'".trim($lm->docno)."'".');"><i class="fa fa-bars"></i> Detail </a>
                    </div>
            </div>
             ';
            $row[] = $lm->docno;
            $row[] = $lm->docref;
            $row[] = $lm->docdate1;
            $row[] = $lm->nmlocationfrom;
            $row[] = $lm->nmlocationto;
            $row[] = $lm->nmstatus;
            $row[] = $lm->inputby;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_trx_transfers_mst_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_trx_transfers_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_ajustment_mst(){
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$nama'";
        $data = $this->m_balance->q_tmp_item_ajustment_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_ajustment_dtl($id){
        $data = $this->m_balance->q_tmp_item_ajustment_dtl(" and id='$id'")->getRow();
        echo json_encode($data);
    }
    function input_stock_ajustment()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.4'; $versirelease='I.D.C.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.4'";
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
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_balance->q_tmp_item_ajustment_mst($param)->getNumRows();

        if (!empty($nama) and $dtl<=0) {
            $info = array (
                'docno' => $nama,
                'docref' => '',
                'idlocation' => $loccode,
                'docdate' => date('Y-m-d'),
                'status' => 'I',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.item_ajustment_mst');
            $builder->insert($info);
        } else if (!empty($nama) and $dtl>0) {
            //lanjutkan
        } else {
            return redirect()->to(base_url('stock/balance/transfers'));
        }
        $data['typeform'] = 'INPUT';
        $data['dtldata'] = $this->m_balance->q_tmp_item_ajustment_mst($param)->getRowArray();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/balance/v_input_stock_ajustment',$data);
    }
    function verifikasi_mst_stock_ajustment(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $docdate = $this->request->getPost('docdate');
        $docref = $this->request->getPost('docref');
        $doctype = $this->request->getPost('doctype');
        $description = $this->request->getPost('description');

        $dtl = $this->m_location->q_mlocation(" and idlocation='$idlocation'")->getRowArray();

        if (!empty($doctype)) {
            $info = array (
                'doctype' => $doctype,
                'docref' => $docref,
                'idlocation' => $loccode,
                'description' => $description,
                'docdate' => date('Y-m-d',strtotime($docdate)),
                'status' => 'E',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.item_ajustment_mst');
            $builder->where('docno',$nama);
            $builder->update($info);
        }
        return redirect()->to(base_url('stock/balance/input_stock_ajustment'));

    }
    function showing_item_ajustments_mst(){
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$nama'";
        $data = $this->m_balance->q_tmp_item_ajustment_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function clearEntryAjustment()
    {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_balance->q_tmp_item_ajustment_mst($param);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_ajustment_mst');
        $builder_dtl = $this->db->table('sc_tmp.item_ajustment_dtl');
        $buildertx = $this->db->table('sc_trx.item_ajustment_mst');
        $builderdtltx = $this->db->table('sc_trx.item_ajustment_dtl');
        //PROSES INPUT
        if ($status === 'I') {
            $builder->where('docno', $nama);
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno', $nama);
                $builder_dtl->delete();

                $builder->where('docno', $nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/ajustment'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else if ($status === 'E') {
            $builder->where('docno', $nama);
            if ($builder_dtl->update(array('status' => 'C'))) {
                $builder_dtl->where('docno', $nama);
                $builder_dtl->delete();

                $builder->where('docno', $nama);
                $builder->delete();

                $buildertx->where('docno', trim($dtl->getRowArray()['docnotmp']));
                $buildertx->update(array(
                    'status' => 'P',
                    'holdrow' => '',
                    'holdby' => '',
                ));
                $builderdtltx->where('docno', trim($dtl->getRowArray()['docnotmp']));
                $builderdtltx->update(array(
                    'status' => 'P',
                    'holdrow' => '',
                    'holdby' => '',
                ));
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/ajustment'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno', $nama);
                $builder_dtl->delete();

                $builder->where('docno', $nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/balance/ajustment'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        }
    }
    function list_balance_ajustments(){
        $list = $this->m_balance->get_t_balance_moving_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = '
            <a class="btn btn-md btn-primary" href="#"  title="Add Item" onclick="add_ajustments('."'".trim($lm->id)."'".')"><i class="fa fa-plus"></i> </a>
         ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->idarea;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_balance_moving_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_balance_moving_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function saveDetailajustment(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            $idlocationfrom = trim($dataprocess->idlocationfrom);
            $idbarangmove = trim($dataprocess->idbarangmove);
            $nmbarangmove = trim($dataprocess->nmbarangmove);
            $qtyonhand = trim($dataprocess->qtyonhand);
            $unitonhand = trim($dataprocess->unitonhand);
            $idareafrom = trim($dataprocess->idareafrom);
            $nmareafrom = trim($dataprocess->nmareafrom);
            $qtyajustment = trim($dataprocess->qtyajustment);
            $unitajustment = trim($dataprocess->unitajustment);
            $description = trim($dataprocess->description);

            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            $builder = $this->db->table('sc_tmp.item_ajustment_dtl');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $parammst = " and docno='$nama'";
                $param = " and docno='$nama' and idbarang='$idbarangmove' and idlocation='$idlocationfrom' and idarea='$idareafrom'";
                $dtlmst = $this->m_balance->q_tmp_item_ajustment_mst($parammst)->getRowArray();
                $check = $this->m_balance->q_tmp_item_ajustment_dtl($param)->getNumRows();
                //check data ganda
                if ($check > 0 or ($qtyajustment>$qtyonhand) or ($qtyajustment<0) ) {
                    $getResult = array('status' => false, 'messages' => 'Oops ready in use, use edit or qty not acceptable');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        //'parentunit' => empty($parentunit) ? 0 : $parentunit ,
                        'id' => 0,
                        'docno' => $nama,
                        'docref' => $dtlmst['docref'],
                        'doctype' => $dtlmst['doctype'],
                        'idlocation' => $idlocationfrom,
                        'idarea' => $idareafrom,
                        'idbarang' => $idbarangmove,
                        'onhand' => $qtyonhand,
                        'onhandtrans' => $qtyajustment,
                        'unit' => $unitajustment,
                        'subunit' => '',
                        'lasttrxdate' => date('Y-m-d H:i:s',strtotime($dtlmst['docdate'])),
                        'description' => $description,
                        'status' => 'I',
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: ');
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idbarang='$idbarangmove' and idlocation='$idlocationfrom' and idarea='$idareafrom'";
                $dtl = $this->m_balance->q_tmp_item_ajustment_dtl($param)->getRowArray();
                if ($qtyajustment<=0 or (($dtl['sisa'] + $dtl['onhandtrans']) - $qtyajustment )<0) {
                    $getResult = array('status' => false, 'messages' => 'Oops ready in use, use edit or qty not acceptable');
                    echo json_encode($getResult);
                } else {
                    $infoupdate = array(
                        'onhandtrans' => $qtyajustment,
                        'description' => $description,
                        'status' => 'E',
                    );
                    $builder->where('docno',$nama);
                    $builder->where('id',$id);
                    if ($builder->update($infoupdate)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: ');
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='DELETE') {
                $iupdate = array(
                    'status' => 'C',
                );
                $builder->where('docno',$nama);
                $builder->where('id',$id);
                if ($builder->update($iupdate)) {
                    $builder->where('docno',$nama);
                    $builder->where('id',$id);
                    $builder->delete();

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
    function finalEntryAjustment(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_balance->q_tmp_item_ajustment_mst($param);
        $cek = $this->m_balance->q_tmp_item_ajustment_dtl($param);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_ajustment_mst');
        if ($status==='E' and $cek->getNumRows() > 0)
        {
            $info = array(
                'status' => 'F'
            );
            $builder->where('docno',$nama);
            $builder->update($info);
            return redirect()->to(base_url('/stock/balance/ajustment'));
        } else {
            return redirect()->to(base_url('/stock/balance/input_stock_ajustment'));
        }
    }
    function list_tmp_ajustments_dtl(){
        $list = $this->m_balance->get_t_tmp_ajustment_dtl_view();
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
                      <a class="dropdown-item" href="#" onclick="edit_ajustments('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_ajustments('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->doctype;
            $row[] = $lm->onhandtrans;
            $row[] = $lm->unit;
            $row[] = $lm->nmareafrom;
            $row[] = $lm->description;
            $row[] = $lm->nmstatus;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_tmp_ajustment_dtl_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_tmp_ajustment_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    /* END AJUSTMENT AJUSTMENT ITEMS BATAL  DIIKUTKAN TRASNSAKSI IN & OUT*/

    /* IMPORT BALANCE WINACC START */
    function import_balance_winacc(){
        $data['title']="Import Laporan Stock Gudang Winacc";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.C.7'; $versirelease='I.D.C.7/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.7'";
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
        $kmenu='I.D.C.7';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_balance->q_balance_winacc_tmp_param($parameter)->getResult();
        $data['adaisi']=$this->m_balance->q_balance_winacc_tmp_param($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('stock/balance/v_import_balance_winacc',$data);
    }

    function proses_balance_winacc_upload() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $dtlloc = $this->m_location->q_mlocation(" and idlocation='$loccode'")->getRowArray();
        $this->db->query("delete from sc_tmp.item_balance_winacc where inputby='$nama'");

        $path = "./assets/files/item/balancewinacc/";
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
                echo '<a href="'.base_url('/stock/balance/import_balance_winacc').'">BACK</a>';
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
                        'docno' => trim($rowData[0][6]),
                        'docref' => trim($rowData[0][14]),
                        'doctype' => trim($rowData[0][13]),
                        'idlocation' => trim($rowData[0][0]),
                        'idarea' => trim($dtlloc['idarea_default']),
                        'idbarang' => trim($rowData[0][2]),
                        'qtyd' => trim($rowData[0][8]),
                        'vald' => trim($rowData[0][9]),
                        'qtyk' => trim($rowData[0][10]),
                        'valk' => trim($rowData[0][11]),
                        'unit' => trim($rowData[0][4]),
                        'trxdate' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][5])))),
                        'description' => trim($rowData[0][7]),
                        'namabarangx' => trim($rowData[0][3]),
                        'docjns' => trim($rowData[0][13]),
                        'pclnama' => trim($rowData[0][12]),
                        'uniqueidobd' => trim($rowData[0][7]),
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                    );

                    //$pm =  " and trim(idbarang)='".trim($rowData[0][0])."'";
                    //$cek = $this->m_item->q_item_param($pm)->getNumRows();
                    if(trim($rowData[0][0]) != '') {
                        $builder = $this->db->table("sc_tmp.item_balance_winacc");
                        $builder->insert($data);
                    }
                    $no++;
                }
                return redirect()->to(base_url('/stock/balance/import_balance_winacc'));
            }
        }
    }

    function clear_balance_winacc_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.item_balance_winacc where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.C.7');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.C.7',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/stock/balance/import_balance_winacc'));
    }

    function final_balance_winacc_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $param = " and (nmbarang in ('UNIDENTIFIED') or nmlocation in ('UNIDENTIFIED') or nmarea in ('UNIDENTIFIED'))";
        $cek = $this->m_balance->q_balance_winacc_tmp_param($param)->getNumRows();
        //$cek2 = $this->m_balance->q_openingstock_tmp_param($param)->getNumRows();

        if ($cek > 0) {
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.D.C.7');
            $builder_trxerror->delete();
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 2,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.D.C.7',
            );
            $builder_trxerror->insert($infotrxerror);
        } else {
            $this->db->query("update sc_tmp.import_opening_stock set status='F' where inputby='$nama'");
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.D.C.7');
            $builder_trxerror->delete();
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.D.C.7',
            );
            $builder_trxerror->insert($infotrxerror);
        }

        return redirect()->to(base_url('/stock/balance/import_balance_winacc'));
    }
    /* IMPORT BALANCE WINACC END */

    /* DAILY STOCK OPNAME  */
    function stockopname(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.8'; $versirelease='I.D.C.8/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.3'";
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
        return $this->template->render('stock/balance/v_stockopname',$data);
    }

    function list_StockOpname(){
        $list = $this->m_balance->get_t_item_stock_opname_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (trim($lm->status)==='P') {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-warning dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="'.base_url('stock/balance/cancelstockopname').'?var='.trim($lm->docno).'" onclick="return confirm('."'Do you wan cancel this transactional ?..  ".trim($lm->docno)."'".')"><i class="fa fa-close"></i> Cancel </a>
                    </div>
            </div>
             ';
            }
            if (trim($lm->status)==='C') {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                     
                    </div>
            </div>
             ';
            }
            $row[] = $lm->docno;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->lasttrxdate1;
            $row[] = $lm->onhand;
            $row[] = $lm->onhandso;
            $row[] = $lm->selisih;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            //$row[] = $lm->nmarea_posisi;
            if ( in_array(trim($lm->status),array('C','D','T'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if ( in_array(trim($lm->status),array('I','A'))) {
                $row[] = '<span class="badge bg-info" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_item_stock_opname_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_item_stock_opname_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function cancelstockopname(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_trx.item_stock_opname');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $info = array('status' => 'C');
        $builder->where('docno',$id);
        $builder->update($info);
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.C.8');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.D.C.8',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('stock/balance/stockopname'));
    }

    function input_stockopname(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.C.8'; $versirelease='I.D.C.8/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.C.8'";
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
        return $this->template->render('stock/balance/v_input_stock_opname',$data);
    }

    function list_balance_stock_opname(){
        $list = $this->m_balance->get_t_balance_so_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
                <a class="btn btn-md btn-primary" href="#"  title="Stock Opname" onclick="so_stock('."'".trim($lm->id)."'".')"><i class="fa fa-plus"></i> Buat SO</a>
             ';
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmgroup;
            $row[] = $lm->idbarang;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_balance_so_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_balance_so_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_detail_so($id){
        $param = " and id='$id'";
        $data = $this->m_balance->q_balance_so($param)->getRow();
        echo (json_encode($data,JSON_PRETTY_PRINT));
    }

    function saveStockOpname(){
        $nama = trim($this->session->get('nama'));

        $id = trim($this->request->getPost('id'));
        $idlocationold = strtoupper(trim($this->request->getPost('idlocationold')));
        $idareaold = strtoupper(trim($this->request->getPost('idareaold')));
        $batch = strtoupper(trim($this->request->getPost('batch')));
        $idbarangmove = strtoupper(trim($this->request->getPost('idbarangmove')));
        $nmbarangmove = strtoupper(trim($this->request->getPost('nmbarangmove')));
        $qty = strtoupper(trim($this->request->getPost('qty')));
        $unit = strtoupper(trim($this->request->getPost('unit')));
        $nmarea = strtoupper(trim($this->request->getPost('nmarea')));
        $qtyso = strtoupper(trim($this->request->getPost('qtyso')));
        $unitso = strtoupper(trim($this->request->getPost('unitso')));
        $trxdate = strtoupper(trim($this->request->getPost('trxdate')));
        $idareaso = strtoupper(trim($this->request->getPost('idareaso')));
        $idbarangso = strtoupper(trim($this->request->getPost('idbarangso')));
        $description = strtoupper(trim($this->request->getPost('description')));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        $type = trim($this->request->getPost('type'));
        $builder = $this->db->table('sc_trx.item_stock_opname');
        // CHECK INPUT TYPE
        if ($type==='UPDATE') {
                $info = array(
                    'docno' => $inputby,
                    'docref' => 'SO',
                    'doctype' => 'IN',
                    'idlocation' => $idlocationold,
                    'idarea' => $idareaold,
                    'batch' => $batch,
                    'idbarang' => $idbarangmove,
                    'onhand' => (int)$qty,
                    'onhandso' => (int)$qtyso,
                    'selisih' => (int)$qty-(int)$qtyso,
                    'unit' => $unit,
                    'lasttrxdate' => date('Y-m-d',strtotime($trxdate)),
                    'subunit' => '',
                    'description' => $description,
                    'status' => '',
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                    /* ranah peletakan dan so*/
                    'batchso' => '',
                    'idlocationso' => $idlocationold,
                    'idareaso' => $idareaso,
                    'idbarangso' => $idbarangso,
                );
                if ($builder->insert($info)) {
                    $paramerror=" and userid='$nama' and modul='I.D.C.8'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();

                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtlerror['nomorakhir1']);
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail Or Item Must Be Low or Equal Than Older, Try Again');
                    echo json_encode($getResult);
                }



        } else if ($type==='DELETE') {

//                $builder->where('id' , $id);
//                if ($builder->delete()) {
//                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idlocation);
//                    echo json_encode($getResult);
//                } else {
//                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
//                    echo json_encode($getResult);
//                }
        }

    }
    /* DAILY STOCK OPNAME  */


}
