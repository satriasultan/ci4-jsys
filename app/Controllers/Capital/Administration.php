<?php

namespace App\Controllers\Capital;

use App\Controllers\BaseController;

class Administration extends BaseController
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

    public function admin_transaction()
    {
        $data['title']="Admin Transaction";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.1'; $versirelease='I.T.B.1/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/administration/v_pengajuan',$data);
    }

    function showing_data_pengajuan($id){
        $param = " and id='$id'";
        $data = $this->m_capital_administration->q_capital_pengajuan($param)->getRow();
        echo json_encode($data);
    }
    function list_karyawan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $kddept = $this->session->get('kddept');
        $roleid = $this->session->get('roleid');

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

        if (!empty($roleid==='IT')) {
            $param_userid = " ";
        } else {
            $param_userid = " and id_dept='$kddept'";
        }


        $param=" and (coalesce(resign,'NO')!='YES' and upper(nmlengkap) like '%$search%' $paramglobal $paramfilter $param_userid) or (coalesce(resign,'NO')!='YES' and nik like '%$search%' $paramglobal $paramfilter $param_userid) order by nmlengkap asc";
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
               <!--a class="btn btn-md btn-success" href="javascript:void(0)" title="Minta Persetujuan" onclick="persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-check"></i> </a--->
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
            $row[] = $lm->desc_appv_lv2;

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

    function save_capital_pengajuan(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            //$nmlengkap = strtoupper(trim($dataprocess->nmlengkap));
            $kebutuhanjenis = trim($dataprocess->kebutuhanjenis);
            $noasset = strtoupper(trim($dataprocess->noasset));
            $nik = trim($dataprocess->nik);
            $idgroup = trim($dataprocess->idgroup);
            $idsubgroup = trim($dataprocess->idsubgroup);
            $kdlvl = trim($dataprocess->kdlvl);
            $docdate= trim($dataprocess->docdate);
            $estprice_kdlvl = trim($dataprocess->estprice_kdlvl);
            $spec = strtoupper(trim($dataprocess->spec));
            //$kddept = strtoupper(trim($dataprocess->kddept));
            $estvendor = strtoupper(trim($dataprocess->estvendor));
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            $builder = $this->db->table('sc_trx.capital_permintaan');
            $dtl = $this->m_capital_administration->q_karyawan(" and nik='$nik'")->getRowArray();
            $dtlpricemax = $this->m_capital_masters->q_capital_level_plafond(" and kdlvl='$kdlvl' and idsubgroup='$idsubgroup'")->getRowArray();

            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $info = array(
                    'kebutuhanjenis' => $kebutuhanjenis,
                    'noasset' => $noasset,
                    'nik' => $nik,
                    'nmlengkap' => $dtl['nmlengkap'],
                    'kddept' => $dtl['id_dept'],
                    'idgroup' => $idgroup,
                    'idsubgroup' => $idsubgroup,
                    'docdate' => date('Y-m-d',strtotime($docdate)),
                    'kdlvl' => $kdlvl,
                    'estprice_kdlvl' => empty($dtlpricemax['pricemax1'])? 0 : str_replace('.','',$dtlpricemax['pricemax1']),
                    //'estprice_kdlvl' => empty($estprice_kdlvl)? 0 : str_replace('.','',$estprice_kdlvl),
                    'spec' => $spec,
                    'estvendor' => $estvendor,
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
                        'nik' => $nik,
                        'nmlengkap' => $dtl['nmlengkap'],
                        'kddept' => $dtl['id_dept'],
                        'idgroup' => $idgroup,
                        'idsubgroup' => $idsubgroup,
                        'docdate' => date('Y-m-d',strtotime($docdate)),
                        'kdlvl' => $kdlvl,
                        'estprice_kdlvl' => empty($estprice_kdlvl)? 0 : str_replace('.','',$estprice_kdlvl),
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

    function tolak_persetujuan_mngr(){
        $nama=trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $desc_appv_lv2 = strtoupper(trim($this->request->getGet('rs')));

        $param = " and id='$id'";
        $builder = $this->db->table('sc_trx.capital_permintaan');
        $info = array(
            'status' => 'T',
            'cancelby' => $nama,
            'canceldate' => date('Y-m-d H:i:s'),
            'desc_appv_lv2' => $desc_appv_lv2,
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
        $data['title']="Daftar Keseluruhan Pengajuan Untuk IT";
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
            if (trim($lm->status)==='I') {
                $row[] = '
               <!--a class="btn btn-md btn-primary" href="javascript:void(0)" title="Ubah Persetujuan Pengajuan" onclick="update_persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
               <a class="btn btn-md btn-success" href="javascript:void(0)" title="Disetujui Ke Budget" onclick="persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-check"></i> </a-->
               
               <a class="btn btn-md btn-warning" href="javascript:void(0)" title="Review Pengajuan" onclick="review_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-image"></i> </a>
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
            //$row[] = $lm->budgeting;
            //$row[] = 'Rp '.number_format(round($lm->vbudgeting),0,",",".");;
            $row[] = $lm->kebutuhanjenis;
            $row[] = $lm->noasset;

            if ($lm->cimage<>'') {
                $row[] =
                    '<div class="pull-left image">
                    <a href="'.base_url('/assets/img/capitalpengajuan').'/'.trim($lm->cimage).'" target="_blank"> <img height="100px" width="100px" alt="User Image" class="img-box" src="'.base_url('/assets/img/capitalpengajuan').'/'.trim($lm->cimage).'"></a>
                </div>';
            } else {
                $row[] =
                    '<div class="pull-left image">
                    <img height="100px" width="100px" alt="User Image" src="'.base_url('/assets/img/user.png').'">
                </div>';
            }

            $row[] = $lm->nmgroup;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->spec;
            $row[] = $lm->nmlvljabatan;
            $row[] = 'Rp '.number_format(round($lm->estprice_kdlvl),0,",",".");;
            $row[] = $lm->estvendor;
            $row[] = $lm->description;
            $row[] = $lm->review;
            $row[] = $lm->desc_appv_lv2;

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
            $kebutuhanjenis = strtoupper(trim($dataprocess->kebutuhanjenis));
            $noassetit = strtoupper(trim($dataprocess->noassetit));
            $nik = trim($dataprocess->nik);
            $docdate = trim($dataprocess->docdate);
            $idgroup = trim($dataprocess->idgroup);
            $idsubgroup = trim($dataprocess->idsubgroup);
            $kdlvl = strtoupper(trim($dataprocess->kdlvl));
            $estprice_kdlvl = trim($dataprocess->estprice_kdlvl);
            $budgeting = trim($dataprocess->budgeting);
            $vbudgeting = trim($dataprocess->vbudgeting);
            $idcostcenter = trim($dataprocess->idcostcenter);
            $datevrealisasi = trim($dataprocess->datevrealisasi);
            $spec = strtoupper(trim($dataprocess->spec));
            $estvendor = strtoupper(trim($dataprocess->estvendor));
            //$vendor_ref1 = strtoupper(trim($dataprocess->vendor_ref1));
            //$pricev_ref1 = trim($dataprocess->pricev_ref1);
            //$vendor_ref2 = strtoupper(trim($dataprocess->vendor_ref2));
            //$pricev_ref2 = trim($dataprocess->pricev_ref2);
            //$vendor_ref3 = strtoupper(trim($dataprocess->vendor_ref3));
            //$pricev_ref3 = trim($dataprocess->pricev_ref3);
            $description = strtoupper(trim($dataprocess->keterangan));

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
                    'estprice_kdlvl' => empty($estprice_kdlvl)? 0 : str_replace('.','',$estprice_kdlvl),
                    'budgeting' => $budgeting,
                    'vbudgeting' => empty($vbudgeting)? 0 : str_replace('.','',$vbudgeting),
                    'idcostcenter' => $idcostcenter,
                    'daterealization' => $datevrealisasi,
                    'spec' => $spec,
                    'estvendor' => $estvendor,
                    //'vendor_ref1' => $vendor_ref1,
                    //'pricev_ref1' => empty($pricev_ref1)? 0 : str_replace('.','',$pricev_ref1),
                    //'vendor_ref2' => $vendor_ref2,
                    //'pricev_ref2' => empty($pricev_ref2)? 0 : str_replace('.','',$pricev_ref2),
                    //'vendor_ref3' => $vendor_ref3,
                    //'pricev_ref3' => empty($pricev_ref3)? 0 : str_replace('.','',$pricev_ref3),
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
                        'noasset' => $noassetit,
                        'nik' => $dtl['nik'],
                        'nmlengkap' => $dtl['nmlengkap'],
                        'kddept' => $dtl['id_dept'],
                        'idgroup' => $idgroup,
                        'idsubgroup' => $idsubgroup,
                        'docdate' => date('Y-m-d',strtotime($docdate)),
                        'kdlvl' => $kdlvl,
                        'estprice_kdlvl' => empty($estprice_kdlvl)? 0 : str_replace('.','',$estprice_kdlvl),
                        'budgeting' => $budgeting,
                        'vbudgeting' => empty($vbudgeting)? 0 : str_replace('.','',$vbudgeting),
                        'idcostcenter' => $idcostcenter,
                        'daterealization' => $datevrealisasi,
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


    function print_pengajuan_dept(){
        $nama = trim($this->session->get('nama'));
        $kddept=trim($this->request->getPost('kddept_filter'));

        $title = " Form Pengajuan Budget IT";
        if (!empty($kddept)) {
            $param = " and coalesce(docno,'')='$kddept'";
        } else {
            $param = "";
        }

        ///$dtl = $this->m_capital_administration->q_capital_pengajuan($param)->getRowArray();

        $datajson =  base_url("capital/administration/api_print_capital_pengajuan/?kddept=$kddept") ;
        $datamrt =  base_url("assets/mrt/form_pengajuan_budget_processing_it.mrt") ;
        //$datamrt =  base_url("assets/mrt/form_pengajuan_budget.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);

    }

    function print_pengajuan_dept_get(){
        $nama = trim($this->session->get('nama'));
        $kddept=trim($this->session->get('kddept'));;

        $title = " Form Pengajuan Budget IT";
        if (!empty($kddept)) {
            $param = " and coalesce(docno,'')='$kddept'";
        } else {
            $param = "";
        }

        ///$dtl = $this->m_capital_administration->q_capital_pengajuan($param)->getRowArray();

        $datajson =  base_url("capital/administration/api_print_capital_pengajuan/?kddept=$kddept") ;
        $datamrt =  base_url("assets/mrt/form_pengajuan_budget.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);

    }

    function api_print_capital_pengajuan(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $kddept=trim($this->request->getGet('kddept'));

        if (!empty($kddept)) {
            $pdept = " and kddept='$kddept'";
            $param = " and status not in ('C','D','T') and coalesce(kddept,'')='$kddept'";
        } else {
            $pdept = " and kddept='UNKNOWN'";
            $param = " and status not in ('C','D','T')";
        }
        //$docno=trim($this->request->getGet('enc_docno'));

        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_capital_administration->q_departmen($pdept);
        $datadtl = $this->m_capital_administration->q_capital_pengajuan($param);

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
                //'tglcetak' => date('d-m-Y H:i:s'),
            ), JSON_PRETTY_PRINT);
    }


    function saveReview(){
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $id = trim($this->request->getPost('id'));
        $cimage = $this->request->getPost('cimage');
        $cbase64 = $this->request->getPost('cimage_base64');
        $review = strtoupper(trim($this->request->getPost('review')));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $builder_capital_permintaan = $this->db->table('sc_trx.capital_permintaan');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        if (empty($cbase64)) {
            if ($type==='REVIEW'){
                $info = array(
                    'review' => $review,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder_capital_permintaan->where('id',$id);
                if ($builder_capital_permintaan->update($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.B.3');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.B.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }
        } else {
            $content = file_get_contents($cbase64);
            //$file = $this->request->getFile('cimage');
            $path = "./assets/img/capitalpengajuan";
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            }

            $filename = trim(trim($id)).'.png';

            //$final_file_name = trim(trim($id)).'.png';
            $path = FCPATH.'assets/img/capitalpengajuan';
            if(file_exists($path.$filename)){
                unlink($path.$filename);
            }
            $isifile = file_put_contents($path.'/'.trim(trim($id)).'.png', $content);

            if ($type==='REVIEW'){
                $info = array(
                    'review' => $review,
                    'cimage' => $filename,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder_capital_permintaan->where('id',$id);
                if ($builder_capital_permintaan->update($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.B.3');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.B.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }
        }

/*
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'cimage' => [
                    'uploaded[cimage]',
                    'mime_in[cimage,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[cimage,4096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 550 pixel</br>
					* Tinggi Max 450 pixel</br>					
					');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = trim(trim($id)).'.png';
                $path = FCPATH.'assets/img/capitalpengajuan';
                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                    // ->convert(IMAGETYPE_PNG)
                    ->resize(550, 450, true, 'height')
                    ->save($path . '/' . trim(trim($id)).'.png');
                $gambar=$final_file_name;
            }

            if ($type==='REVIEW'){
                $info = array(
                    'review' => $review,
                    'cimage' => $gambar,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder_capital_permintaan->where('id',$id);
                if ($builder_capital_permintaan->update($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.B.3');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.B.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }

        } else {
            if ($type==='REVIEW'){
                $info = array(
                    'review' => $review,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder_capital_permintaan->where('id',$id);
                if ($builder_capital_permintaan->update($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.B.3');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.B.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }


        }*/
    }




    function jasa_pembayaran()
    {
        $data['title']="Jasa dan Pembayaran IT";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.4'; $versirelease='I.T.B.4/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('capital/administration/v_jasa_pembayaran',$data);
    }


    function save_jasa_pembayaran(){
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $id = trim($this->request->getPost('id'));
        $idgroup = trim($this->request->getPost('idgroup'));
        $idsubgroup = trim($this->request->getPost('idsubgroup'));
        $noassetit = trim($this->request->getPost('noassetit'));
        $nmpembayaran = trim($this->request->getPost('nmpembayaran'));
        $nmpengguna = trim($this->request->getPost('nmpengguna'));
        $renewal = trim($this->request->getPost('renewal'));
        $ppn = trim($this->request->getPost('ppn'));
        $jnsppn = trim($this->request->getPost('jnsppn'));
        $ttlnetto = trim($this->request->getPost('ttlnetto'));
        $duedate_in = trim($this->request->getPost('duedate_in'));
        $duedate_next = trim($this->request->getPost('duedate_next'));
        $nmsupplier = trim($this->request->getPost('nmsupplier'));
        $description = trim($this->request->getPost('description'));

        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $builder_jasa_pembayaran = $this->db->table('sc_mst.jasa_pembayaran');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        if ($type === 'INPUT') {
            $info = array(
                'docdate' => date('Y-m-d'),
                'idgroup' => $idgroup,
                'idsubgroup' => $idsubgroup,
                'noassetit' => strtoupper($noassetit),
                'nmpembayaran' => strtoupper($nmpembayaran),
                'nmpengguna' => strtoupper($nmpengguna),
                'renewal' => $renewal,
                'ppn' => $ppn,
                'jnsppn' => $jnsppn,
                'ttlnetto' => str_replace(',','.',str_replace('.','',$ttlnetto)),
                'duedate_in' => date('Y-m-d',strtotime($duedate_in)),
                'duedate_next' => date('Y-m-d',strtotime($duedate_next)),
                'nmsupplier' => strtoupper($nmsupplier),
                'description' => strtoupper($description),
                'input_date' => $inputdate,
                'input_by' => $inputby,
                'status' => 'INPUT',
            );
            if ($builder_jasa_pembayaran->insert($info)) {
                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','I.T.B.4');
                $builder_trxerror->delete();
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $nama,
                    'nomorakhir2' => '',
                    'modul' => 'I.T.B.4',
                );
                $builder_trxerror->insert($infotrxerror);
                $paramerror=" and userid='$nama' and modul='I.T.B.4'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                echo json_encode($getResult);
            } else {
                $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                echo json_encode($getResult);
            }
        }

    }

    function list_jasa_pembayaran(){
        $list = $this->m_capital_administration->get_jasa_pembayaran_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $lm->id;
            if (trim($lm->status)==='INPUT') {
                $row[] = '
               <a class="btn btn-md btn-primary" href="javascript:void(0)" title="Input Detail Pembayaran" onclick="detail_pembayaran('."'".trim($lm->id)."'".')"><i class="fa fa-bars"></i> </a>
               <!--a class="btn btn-md btn-success" href="javascript:void(0)" title="Minta Persetujuan" onclick="persetujuan_pengajuan('."'".trim($lm->id)."'".')"><i class="fa fa-check"></i> </a--->
               <a class="btn btn-md btn-danger" href="javascript:void(0)" title="Batalkan Pembayaran" onclick="delete_pembayaran('."'".trim($lm->id)."'".')"><i class="fa fa-close"></i> </a>';
            } else {
                $row[] = '
               <a class="btn btn-md btn-primary" href="javascript:void(0)" title="Input Detail Pembayaran" onclick="detail_pembayaran('."'".trim($lm->id)."'".')"><i class="fa fa-bars"></i> </a>';
            }
            $row[] = $lm->nmpembayaran;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->nmpengguna;
            $row[] = $lm->nmsupplier;


            $row[] = $lm->renewal;
            $row[] = $lm->duedate_next;
//            $row[] = $lm->selisih_hari;
            if ( $lm->selisih_hari <= 25 ) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->selisih_hari.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->selisih_hari.'</span>';
            }
            $row[] = 'Rp '.number_format(round($lm->ttlnetto),0,",",".");;
            if ( in_array(trim($lm->status),array('CLOSE'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->status.'</span>';
            } else if ( in_array(trim($lm->status),array('I','A'))) {
                $row[] = '<span class="badge bg-info" style="font-size: 100%">'.$lm->status.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->status.'</span>';
            }
            /*$row[] = date('d-m-Y H:i:s',strtotime($lm->lastpayment));*/
            $row[] = $lm->description;

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_administration->jasa_pembayaran_view_count_all(),
            "recordsFiltered" => $this->m_capital_administration->jasa_pembayaran_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_jasa_pembayaran($id){
        $param = " and id='$id'";
        $data = $this->m_capital_administration->q_jasa_pembayaran($param)->getRow();
        echo json_encode($data);
    }
    function showing_jasa_pembayaran_detail($id){
        $param = " and id='$id'";
        $data = $this->m_capital_administration->q_jasa_pembayaran_detail($param)->getRow();
        echo json_encode($data);
    }
    function detail_jasa_pembayaran()
    {
        $var = trim($this->request->getGet('q'));
        $data['title']="Detail Pelunasan Pembayaran/History Pembayaran IT -> ".$var;
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.4'; $versirelease='I.T.B.4/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='$kodemenu'";
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
        $data['id'] = $var;
        $data['mst'] = $this->m_capital_administration->q_jasa_pembayaran(" and id=$var")->getRowArray();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('capital/administration/v_detail_jasa_pembayaran',$data);

    }

    function save_jasa_pembayaran_detail(){
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $id = trim($this->request->getPost('id'));
        $idrev = trim($this->request->getPost('idrev'));
        $idinvoice = trim($this->request->getPost('idinvoice'));
        $idftax = trim($this->request->getPost('idftax'));
        $nmpembayaran = trim($this->request->getPost('nmpembayaran'));
        $nmsupplier = trim($this->request->getPost('nmsupplier'));
        $renewal = trim($this->request->getPost('renewal'));
        $ppn = trim($this->request->getPost('ppn'));
        $jnsppn = trim($this->request->getPost('jnsppn'));
        $ttlnetto = trim($this->request->getPost('ttlnetto'));
        $invoice_date = trim($this->request->getPost('invoice_date'));
        $attdir = trim($this->request->getPost('attdir'));
        $attfile = trim($this->request->getPost('attfile'));
        $description = trim($this->request->getPost('description'));
        $status = trim($this->request->getPost('status'));
        $docdate = trim($this->request->getPost('docdate'));
        $duedate_next = trim($this->request->getPost('duedate_next'));
        $duedate_in = trim($this->request->getPost('duedate_in'));
        $duedate_renewal = trim($this->request->getPost('duedate_renewal'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        $builder_jasa_pembayaran_detail = $this->db->table('sc_trx.jasa_pembayaran_detail');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        if ($type === 'INPUT') {
            $info = array(
                'idrev' => $idrev,
                'ttlnetto' => str_replace(',','.',str_replace('.','',$ttlnetto)),
                'idinvoice' => strtoupper($idinvoice),
                'idftax' => strtoupper($idftax),
                'docdate' => date('Y-m-d',strtotime($docdate)),
                'renewal' => strtoupper($renewal),
                'ppn' => strtoupper($ppn),
                'jnsppn' => strtoupper($jnsppn),
                'description' => strtoupper($description),
                'status' => $status,
                'invoice_date' => date('Y-m-d',strtotime($invoice_date)),
                'attdir' => strtoupper($attdir),
                'attfile' => strtoupper($attfile),
                'duedate_next' => date('Y-m-d',strtotime($duedate_renewal)),
                'duedate_in' => date('Y-m-d',strtotime($duedate_next)),
                'input_date' => $inputdate,
                'input_by' => $inputby,
            );
            if ($builder_jasa_pembayaran_detail->insert($info)) {
                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','I.T.B.4');
                $builder_trxerror->delete();
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $nama,
                    'nomorakhir2' => '',
                    'modul' => 'I.T.B.4',
                );
                $builder_trxerror->insert($infotrxerror);
                $paramerror=" and userid='$nama' and modul='I.T.B.4'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                echo json_encode($getResult);
            } else {
                $getResult = array('status' => false, 'messages' => 'Data sama, kembar');
                echo json_encode($getResult);
            }
        }

    }


    function list_jasa_pembayaran_detail(){
        $list = $this->m_capital_administration->get_jasa_pembayaran_detail_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $lm->id;

            $row[] = '
               <a class="btn btn-md btn-danger" href="javascript:void(0)" title="Hapus History Pembayaran" onclick="delete_pembayaran_detail('."'".trim($lm->id)."'".')"><i class="fa fa-close"></i> </a>';
            $row[] = $lm->nmpembayaran;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->noassetit;
            $row[] = $lm->idinvoice;
            $row[] = $lm->idftax;
            $row[] = $lm->renewal;
            $row[] = $lm->invoice_date1;
            $row[] = $lm->docdate1;
            $row[] = $lm->ppn;
            $row[] = $lm->jnsppn;
            $row[] = 'Rp '.number_format(round($lm->ttlnetto),0,",",".");;
            if ( in_array(trim($lm->status),array('C','D','T'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->status.'</span>';
            } else if ( in_array(trim($lm->status),array('I','A'))) {
                $row[] = '<span class="badge bg-info" style="font-size: 100%">'.$lm->status.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->status.'</span>';
            }
            $row[] = $lm->duedate_next1;

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_capital_administration->jasa_pembayaran_detail_view_count_all(),
            "recordsFiltered" => $this->m_capital_administration->jasa_pembayaran_detail_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function delete_pembayaran(){
         $id = trim($this->request->getGet('q'));

        $builder = $this->db->table('sc_mst.jasa_pembayaran');
        $builder->where('id',$id);
        if ($builder->delete()) {
            return redirect()->to(base_url('capital/administration/jasa_pembayaran'));
        } else {
            return redirect()->to(base_url('capital/administration/jasa_pembayaran/Error'));
        }
    }
    function delete_detail_pembayaran($id){

        $param = " and id='$id'";
        $dtl = $this->m_capital_administration->q_jasa_pembayaran_detail($param)->getRowArray();
        $idrev = trim($dtl['idrev']);
        $builder = $this->db->table('sc_trx.jasa_pembayaran_detail');
        $builder->where('id',$id);
        if ($builder->delete()) {
            return redirect()->to(base_url('capital/administration/detail_jasa_pembayaran?q='.$idrev));
        } else {
            return redirect()->to(base_url('capital/administration/detail_jasa_pembayaran?q='.$idrev));
        }
    }
}
