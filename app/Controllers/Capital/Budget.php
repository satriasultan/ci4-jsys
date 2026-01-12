<?php

namespace App\Controllers\Capital;

use App\Controllers\BaseController;

class Budget extends BaseController
{
    public function index()
    {
        $data['title']="Budget Register";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.C.1'; $versirelease='I.T.C.1/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/budget/v_budgeting',$data);
    }

    function showing_data_pengajuan_budget($id){
        $param = " and id='$id'";
        $data = $this->m_capital_administration->q_capital_pengajuan($param)->getRow();
        echo json_encode($data);
    }

    function list_karyawan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_param_global_');
        $varGet = $this->request->getVar('var');
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($varGet)) {
            $paramfilter = " and nik='$varGet'";
        } else {
            $paramfilter = " ";
        }



        $param=" and (upper(nmlengkap) like '%$search%' $paramglobal $paramfilter) or ( nik like '%$search%' $paramglobal $paramfilter ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_capital_administration->q_karyawan($param)->getResult();
        $count = $this->m_capital_administration->q_karyawan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'paramfilter' => $paramfilter,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_pengajuan(){
        $list = $this->m_capital_administration->get_capital_pengajuan_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (trim($lm->status)==='I') {
                $row[] = '
               <a class="btn btn-md btn-primary" href="javascript:void(0)" title="Ubah Pengajuan" onclick="update_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
               <a class="btn btn-md btn-success" href="javascript:void(0)" title="Minta Persetujuan" onclick="persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-check"></i> </a>
               <a class="btn btn-md btn-danger" href="javascript:void(0)" title="Batalkan Pengajuan" onclick="delete_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-close"></i> </a>';
            } else if (trim($lm->status)==='A') {
                $row[] = '
               <a class="btn btn-md btn-danger" href="javascript:void(0)" title="Batalkan Pengajuan" onclick="delete_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-close"></i> </a>';
            } else {
                $row[] = '';
            }


            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->docdate1;
            if ( in_array(trim($lm->status),array('C','D'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if ( in_array(trim($lm->status),array('I','A'))) {
                $row[] = '<span class="badge bg-info" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->kebutuhanjenis;
            $row[] = $lm->noasset;
            $row[] = $lm->nmgroup;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->spec;
            $row[] = $lm->nmlvljabatan;
            $row[] = 'Rp '.number_format(round($lm->estprice_kdlvl),0,",",".");;
            $row[] = $lm->estvendor;
            $row[] = $lm->description;

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_administration->capital_pengajuan_view_count_all(),
            "recordsFiltered" => $this->m_capital_administration->capital_pengajuan_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function save_capital_pengajuan_budget(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = strtoupper(trim($dataprocess->type));
            $id = strtoupper(trim($dataprocess->id));
            $kebutuhanjenis = strtoupper(trim($dataprocess->kebutuhanjenis));
            $noasset = strtoupper(trim($dataprocess->noasset));
            $nmasset = strtoupper(trim($dataprocess->nmasset));
            $nik = strtoupper(trim($dataprocess->nik));
            $kddept = strtoupper(trim($dataprocess->kddept));
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $idsubgroup = strtoupper(trim($dataprocess->idsubgroup));
            $kdlvl = strtoupper(trim($dataprocess->kdlvl));
            $estprice_kdlvl = strtoupper(trim($dataprocess->estprice_kdlvl));
            $budgeting = strtoupper(trim($dataprocess->budgeting));
            $vbudgeting = strtoupper(trim($dataprocess->vbudgeting));
            $docdate = strtoupper(trim($dataprocess->docdate));
            $daterealization = strtoupper(trim($dataprocess->daterealization));
            $idcostcenter = strtoupper(trim($dataprocess->idcostcenter));
            $spec = strtoupper(trim($dataprocess->spec));
            $estvendor = strtoupper(trim($dataprocess->estvendor));
            $btype = strtoupper(trim($dataprocess->btype));
            $bsubtype = strtoupper(trim($dataprocess->bsubtype));
            $acctype = strtoupper(trim($dataprocess->acctype));
            $docno = strtoupper(trim($dataprocess->docno));
            $description = strtoupper(trim($dataprocess->description));

            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            $builder = $this->db->table('sc_trx.capital_budget');
            $dtl = $this->m_capital_administration->q_karyawan(" and nik='$nik'")->getRowArray();

            // CHECK INPUT TYPE
            if ($type==='INPUT' and ($docno===$nama)) {
                $info = array(
                    'type' => $type,
                    'id' => $id,
                    'kebutuhanjenis' => $kebutuhanjenis,
                    'noasset' => $noasset,
                    'nmasset' => $nmasset,
                    'nik' => $nik,
                    'nmlengkap' => $dtl['nmlengkap'],
                    'kddept' => $dtl['id_dept'],
                    'idgroup' => $idgroup,
                    'idsubgroup' => $idsubgroup,
                    'kdlvl' => $kdlvl,
                    'estprice_kdlvl' => $estprice_kdlvl,
                    'budgeting' => $budgeting,
                    'vbudgeting' => $vbudgeting,
                    'docdate' => $docdate,
                    'daterealization' => $daterealization,
                    'idcostcenter' => $idcostcenter,
                    'spec' => $spec,
                    'estvendor' => $estvendor,
                    'btype' => $btype,
                    'bsubtype' => $bsubtype,
                    'acctype' => $acctype,
                    'docno' => $docno,
                    'description' => $description,

                    'status' => 'I',
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                );
                if ($builder->insert($info)) {
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtl['nmlengkap']);
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and id='$id'";
                $check = $this->m_capital_administration->q_capital_pengajuan($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'kebutuhanjenis' => $kebutuhanjenis,
                        'noasset' => $noasset,
                        'nik' => $dtl['nik'],
                        'nmlengkap' => $dtl['nmlengkap'],
                        'kddept' => $dtl['id_dept'],
                        'idgroup' => $idgroup,
                        'idsubgroup' => $idsubgroup,
                        'docdate' => date('Y-m-d',strtotime($docdate)),
                        'kdlvl' => $kdlvl,
                        'estprice_kdlvl' => str_replace('.','',$estprice_kdlvl),
                        'spec' => $spec,
                        'estvendor' => $estvendor,
                        'description' => $description,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtl['nmlengkap']);
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

                $builder = $this->db->table('sc_trx.capital_permintaan');
                $builder->where('id' , $id);
                $info = array(
                    'status' => 'C',
                    'cancelby' => $inputby,
                    'canceldate' => $inputdate,
                );
                if ($builder->update($info)) {
                    $getResult = array('status' => true, 'messages' => 'Data Canceled'.' Code: '.$dtl['nmlengkap']);
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

    function permintaan_persetujuan_mngr($id){

        $param = " and id='$id'";
        $builder = $this->db->table('sc_trx.capital_permintaan');
        $info = array(
            'status' => 'A',
        );
        $builder->where('id',$id);
        if ($builder->update($info)) {
            $getResult = array('status' => true, 'messages' => 'Permintaan Pengajuan Dilakukan');
            echo json_encode($getResult);
        } else {
            $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
            echo json_encode($getResult);
        }
    }


    public function approvals_admin()
    {
        $data['title']="Persetujuan Manager";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));
        $kodemenu='I.T.B.2'; $versirelease='I.T.B.2/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/administration/v_pengajuan_approval_manager',$data);
    }
    function list_pengajuan_approvals_admin(){
        $list = $this->m_capital_administration->get_capital_pengajuan_level1_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (trim($lm->status)==='A') {
                $row[] = '
               <a class="btn btn-md btn-success" href="javascript:void(0)" title="Disetujui" onclick="persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-check"></i> </a>
               <a class="btn btn-md btn-danger" href="javascript:void(0)" title="Dibatalkan" onclick="tolak_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-close"></i> </a>';
            } else {
                $row[] = '';
            }

            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->docdate1;
            if ( in_array(trim($lm->status),array('C','D','T'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if ( in_array(trim($lm->status),array('I','A'))) {
                $row[] = '<span class="badge bg-info" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->kebutuhanjenis;
            $row[] = $lm->noasset;
            $row[] = $lm->nmgroup;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->spec;
            $row[] = $lm->nmlvljabatan;
            $row[] = 'Rp '.number_format(round($lm->estprice_kdlvl),0,",",".");;
            $row[] = $lm->estvendor;
            $row[] = $lm->description;

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_administration->capital_pengajuan_level1_view_count_all(),
            "recordsFiltered" => $this->m_capital_administration->capital_pengajuan_level1_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function persetujuan_mngr($id){
        $nama=trim($this->session->get('nama'));
        $param = " and id='$id'";
        $builder = $this->db->table('sc_trx.capital_permintaan');
        $info = array(
            'status' => 'B',
            'approveby' => $nama,
            'approvedate' => date('Y-m-d H:i:s'),
        );
        $builder->where('id',$id);
        if ($builder->update($info)) {
            $getResult = array('status' => true, 'messages' => 'Permintaan Pengajuan Dilakukan');
            echo json_encode($getResult);
        } else {
            $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
            echo json_encode($getResult);
        }
    }

    function tolak_persetujuan_mngr($id){
        $nama=trim($this->session->get('nama'));
        $param = " and id='$id'";
        $builder = $this->db->table('sc_trx.capital_permintaan');
        $info = array(
            'status' => 'C',
            'cancelby' => $nama,
            'canceldate' => date('Y-m-d H:i:s'),
        );
        $builder->where('id',$id);
        if ($builder->update($info)) {
            $getResult = array('status' => true, 'messages' => 'Permintaan Pengajuan Ditolak');
            echo json_encode($getResult);
        } else {
            $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
            echo json_encode($getResult);
        }
    }

    /* ADMIN TRANSACTION */
    public function input_transaction()
    {
        $data['title']="Daftar Keseluruhan Pengajuan Yang Disetujui Manager";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));
        $kodemenu='I.T.B.3'; $versirelease='I.T.B.3/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/administration/v_pengajuan_budgeting_process',$data);
    }

    function list_pengajuan_all(){
        $list = $this->m_capital_administration->get_capital_pengajuan_all_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (trim($lm->status)==='B') {
                $row[] = '
               <a class="btn btn-md btn-success" href="javascript:void(0)" title="Disetujui Ke Budget" onclick="persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-check"></i> </a>
               <a class="btn btn-md btn-danger" href="javascript:void(0)" title="Ditolak Pengajuan" onclick="tolak_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-close"></i> </a>';
            } else {
                $row[] = '';
            }

            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->docdate1;
            if ( in_array(trim($lm->status),array('C','D','T'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else if ( in_array(trim($lm->status),array('I','A'))) {
                $row[] = '<span class="badge bg-info" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->kebutuhanjenis;
            $row[] = $lm->noasset;
            $row[] = $lm->nmgroup;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->spec;
            $row[] = $lm->nmlvljabatan;
            $row[] = 'Rp '.number_format(round($lm->estprice_kdlvl),0,",",".");;
            $row[] = $lm->estvendor;
            $row[] = $lm->description;

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_administration->capital_pengajuan_all_view_count_all(),
            "recordsFiltered" => $this->m_capital_administration->capital_pengajuan_all_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function save_capital_it_input_transaction(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            $kebutuhanjenis = trim($dataprocess->kebutuhanjenis);
            $noassetit = trim($dataprocess->noassetit);
            $nik = trim($dataprocess->nik);
            $docdate = trim($dataprocess->docdate);
            $idgroup = trim($dataprocess->idgroup);
            $idsubgroup = trim($dataprocess->idsubgroup);
            $kdlvl = trim($dataprocess->kdlvl);
            $estprice_kdlvl = trim($dataprocess->estprice_kdlvl);
            $budgeting = trim($dataprocess->budgeting);
            $vbudgeting = trim($dataprocess->vbudgeting);
            $idcostcenter = trim($dataprocess->idcostcenter);
            $datevrealisasi = trim($dataprocess->datevrealisasi);
            $spec = trim($dataprocess->spec);
            $estvendor = trim($dataprocess->estvendor);
            $vendor_ref1 = trim($dataprocess->vendor_ref1);
            $pricev_ref1 = trim($dataprocess->pricev_ref1);
            $vendor_ref2 = trim($dataprocess->vendor_ref2);
            $pricev_ref2 = trim($dataprocess->pricev_ref2);
            $vendor_ref3 = trim($dataprocess->vendor_ref3);
            $pricev_ref3 = trim($dataprocess->pricev_ref3);
            $description = trim($dataprocess->keterangan);

            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $builder = $this->db->table('sc_trx.capital_permintaan');
            $dtl = $this->m_capital_administration->q_karyawan(" and nik='$nik'")->getRowArray();
            // CHECK INPUT TYPE
            if ($type==='INPUT' and !empty($dtl['nik'])) {
                $info = array(
                    'kebutuhanjenis' => $kebutuhanjenis,
                    'noasset' => $noassetit,
                    'nik' => $dtl['nik'],
                    'nmlengkap' => $dtl['nmlengkap'],
                    'kddept' => $dtl['id_dept'],
                    'idgroup' => $idgroup,
                    'idsubgroup' => $idsubgroup,
                    'docdate' => date('Y-m-d',strtotime($docdate)),
                    'kdlvl' => $kdlvl,
                    'estprice_kdlvl' => str_replace('.','',$estprice_kdlvl),
                    'budgeting' => $budgeting,
                    'vbudgeting' => $vbudgeting,
                    'idcostcenter' => $idcostcenter,
                    'daterealization' => $datevrealisasi,
                    'spec' => $spec,
                    'estvendor' => $estvendor,
                    'vendor_ref1' => $vendor_ref1,
                    'pricev_ref1' => str_replace('.','',$pricev_ref1),
                    'vendor_ref2' => $vendor_ref2,
                    'pricev_ref2' => str_replace('.','',$pricev_ref2),
                    'vendor_ref3' => $vendor_ref3,
                    'pricev_ref3' => str_replace('.','',$pricev_ref3),
                    'description' => $description,
                    'status' => 'B',
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,

                );
                if ($builder->insert($info)) {
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtl['nmlengkap']);
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and id='$id'";
                $check = $this->m_capital_administration->q_capital_pengajuan($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        'kebutuhanjenis' => $kebutuhanjenis,
                        'noasset' => $noassetit,
                        'kddept' => $dtl['id_dept'],
                        'docdate' => date('Y-m-d',strtotime($docdate)),
                        'kdlvl' => $kdlvl,
                        'estprice_kdlvl' => str_replace('.','',$estprice_kdlvl),
                        'spec' => $spec,
                        'estvendor' => $estvendor,
                        'description' => $description,
                        'updateby' => $inputby,
                        'updatedate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtl['nmlengkap']);
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

                $builder = $this->db->table('sc_trx.capital_permintaan');
                $builder->where('id' , $id);
                $info = array(
                    'status' => 'C',
                    'cancelby' => $inputby,
                    'canceldate' => $inputdate,
                );
                if ($builder->update($info)) {
                    $getResult = array('status' => true, 'messages' => 'Data Canceled'.' Code: '.$dtl['nmlengkap']);
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



}
