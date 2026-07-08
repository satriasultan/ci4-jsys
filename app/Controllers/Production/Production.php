<?php
/*
 * select * from sc_mst.trxtype;
--DELETE FROM sc_mst.trxtype WHERE JENISTRX='I.R.A.1';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
values
('I','I.R.A.1','DRAFT'),
('E','I.R.A.1','REVISION/EDITING'),
('F','I.R.A.1','FINAL USER'),
('A','I.R.A.1','APPROVED'),
('D','I.R.A.1','DISAPPROVED'),
('P','I.R.A.1','CETAK/PRINT'),
('O','I.R.A.1','FINAL TRANSACTION');
 *
 * */

namespace App\Controllers\Production;

use App\Controllers\BaseController;

class Production extends BaseController
{

    public function index()
    {
        echo 'pg_Exception';
    }
    public function standart_cost()
    {
        $data['title']="Standart Cost Production";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_standart_cost_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clearStandartCostTmp');
            $urlnext = base_url('production/trans/add_standart_cost');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/standart_cost/v_standart_cost',$data);
    }

    function list_standart_cost_mst(){

        $list = $this->m_production->get_mst_standart_cost_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.1';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/updateStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Standard Cost : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detailStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Standard Cost : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Standard Cost : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->cabang;
            $row[] = $lm->pemohon;

            $row[] = $lm->docdate;
            $row[] = $lm->activedate;

            $row[] = $lm->docref;

            $row[] = $lm->description;

            $row[] = '<div class="text-center">'.$statusBadge.'</div>';

            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;

            $row[] = $lm->updateby;
            $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->mst_standart_cost_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->mst_standart_cost_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_standart_cost(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_standart_cost_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_standart_cost_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/standart_cost/v_add_standart_cost',$data);
    }

    function showing_tmp_standart_cost_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_standart_cost_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_mst_standart_cost_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_mst_standart_cost_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_standart_cost_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_mst.standart_cost_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_standart_cost_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.standart_cost_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'STDCOST',
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'activedate'     => trim($this->request->getPost('activedate')),
                'penyesuaian_a'    => trim($this->request->getPost('penyesuaian_a')),
                'penyesuaian_b'   => trim($this->request->getPost('penyesuaian_b')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'dari_bagian'       => trim($this->request->getPost('dari_bagian')),
                'ajustment'       => 'false',


                'status'     => 'E',
                'description' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

        $idbarang    = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit    = strtoupper(trim($this->request->getPost('unit')));
        $actualcost    = strtoupper(trim($this->request->getPost('actualcost')));
        $lastcost    = strtoupper(trim($this->request->getPost('lastcost')));
        $newcost    = strtoupper(trim($this->request->getPost('newcost')));
        $description_detail    = strtoupper(trim($this->request->getPost('description_detail')));



        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.standart_cost_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('description', $description_detail);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama dengan keterangan yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('idurut', $idurut)
                ->update([
                    'doctype'    => 'STDCOST',
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'unit'        => $unit,
                    'docdate'    => trim($this->request->getPost('docdate')),
                    'activedate'     => trim($this->request->getPost('activedate')),
                    'newcost'        => $newcost,
                    'description' => $description_detail,
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

            if (!$updateDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {

            $inputdate = date('Y-m-d H:i:s');

            $rawUnique = $idbarang . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $insertDetail = $builderDetail->insert([
                'docno'       => $docno,
                'doctype'    => 'STDCOST',
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'docdate'    => trim($this->request->getPost('docdate')),
                'activedate'     => trim($this->request->getPost('activedate')),
                //'batch'         => $batch,
                //'qty'         => $qty,
                'newcost'        => $newcost,
                'description' => $description_detail,
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s'),
                'uniqueid'  => $uniqueid
            ]);

            if (!$insertDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clearStandartCostTmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_standart_cost_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.standart_cost_mst');
        $builder_dtl = $this->db->table('sc_tmp.standart_cost_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/standart_cost'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/standart_cost'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/standart_cost'));
        }

    }



    function list_tmp_standart_cost_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_standart_cost_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_standart_cost_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_standart_cost_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_mst_standart_cost_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_mst_standart_cost_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->mst_standart_cost_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->mst_standart_cost_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_standart_cost(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_standart_cost_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_standart_cost_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_standart_cost_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.standart_cost_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.1');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.1',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_standart_cost'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'description'        => $keterangan,
//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/standart_cost'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/standart_cost'));
            }



        }

    }

    public function get_standart_cost_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_standart_cost_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function updateStandartCost()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/standart_cost')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.standart_cost_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/standart_cost')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_mst.standart_cost_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_standart_cost')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/standart_cost')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/standart_cost')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detailStandartCost()
    {
        /* Penambahan Squence */
        $data['title']="Detail Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/standart_cost'));
        }
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_mst_standart_cost_mst($param)->getRowArray();
        return $this->template->render('production/standart_cost/v_detail_standart_cost',$data);
    }

    public function cancelStandartCost()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/standart_cost')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.standart_cost_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/standart_cost')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

            'status'     => 'C',
            'updatedate' => date('Y-m-d H:i:s'),
            'updateby'   => $nama,

        ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.standart_cost_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.standart_cost_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/standart_cost')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/standart_cost')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/standart_cost')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }


/* BIAYA STANDART PRODUKSI  I.R.A.2*/
    public function biaya_standart()
    {
        $data['title']="Biaya Standart Production";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_biaya_standart_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clearBiayaStandartTmp');
            $urlnext = base_url('production/trans/add_biaya_standart');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/biaya_standart/v_biaya_standart',$data);
    }

    function list_biaya_standart_mst(){

        $list = $this->m_production->get_mst_biaya_standart_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.2';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/updateStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Standard Cost : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detailStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Standard Cost : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Standard Cost : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->cabang;
            $row[] = $lm->pemohon;

            $row[] = $lm->docdate;
            $row[] = $lm->activedate;

            $row[] = $lm->docref;

            $row[] = $lm->description;

            $row[] = '<div class="text-center">'.$statusBadge.'</div>';

            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;

            $row[] = $lm->updateby;
            $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->mst_biaya_standart_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->mst_biaya_standart_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_biaya_standart(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_biaya_standart_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_biaya_standart_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/biaya_standart/v_add_biaya_standart',$data);
    }

    function showing_tmp_biaya_standart_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_biaya_standart_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_mst_biaya_standart_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_mst_biaya_standart_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_biaya_standart_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_mst.biaya_standart_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_biaya_standart_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.biaya_standart_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'BIAYA_STANDART',
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'activedate'     => trim($this->request->getPost('activedate')),
                'penyesuaian_a'    => trim($this->request->getPost('penyesuaian_a')),
                'penyesuaian_b'   => trim($this->request->getPost('penyesuaian_b')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'dari_bagian'       => trim($this->request->getPost('dari_bagian')),
                'ajustment'       => 'false',


                'status'     => 'E',
                'description' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

        $idbarang    = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit    = strtoupper(trim($this->request->getPost('unit')));
        $actualcost    = strtoupper(trim($this->request->getPost('actualcost')));
        $lastcost    = strtoupper(trim($this->request->getPost('lastcost')));
        $newcost    = strtoupper(trim($this->request->getPost('newcost')));
        $description_detail    = strtoupper(trim($this->request->getPost('description_detail')));



        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.biaya_standart_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('description', $description_detail);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama dengan keterangan yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('idurut', $idurut)
                ->update([
                    'doctype'    => 'BIAYA_STANDART',
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'unit'        => $unit,
                    'docdate'    => trim($this->request->getPost('docdate')),
                    'activedate'     => trim($this->request->getPost('activedate')),
                    'newcost'        => $newcost,
                    'description' => $description_detail,
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

            if (!$updateDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {

            $inputdate = date('Y-m-d H:i:s');

            $rawUnique = $idbarang . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $insertDetail = $builderDetail->insert([
                'docno'       => $docno,
                'doctype'    => 'BIAYA_STANDART',
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'docdate'    => trim($this->request->getPost('docdate')),
                'activedate'     => trim($this->request->getPost('activedate')),
                //'batch'         => $batch,
                //'qty'         => $qty,
                'newcost'        => $newcost,
                'description' => $description_detail,
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s'),
                'uniqueid'  => $uniqueid
            ]);

            if (!$insertDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clearBiayaStandartTmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_biaya_standart_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.biaya_standart_mst');
        $builder_dtl = $this->db->table('sc_tmp.biaya_standart_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/biaya_standart'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/biaya_standart'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/biaya_standart'));
        }

    }



    function list_tmp_biaya_standart_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_biaya_standart_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_biaya_standart_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_biaya_standart_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_mst_biaya_standart_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_mst_biaya_standart_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->mst_biaya_standart_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->mst_biaya_standart_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_biaya_standart(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_biaya_standart_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_biaya_standart_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_biaya_standart_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.biaya_standart_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.2');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_biaya_standart'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'description'        => $keterangan,
//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/biaya_standart'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.2',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/biaya_standart'));
            }



        }

    }

    public function get_biaya_standart_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_biaya_standart_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function updateBiayaStandart()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/biaya_standart')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.biaya_standart_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/biaya_standart')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_mst.biaya_standart_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_biaya_standart')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/biaya_standart')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/biaya_standart')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detailBiayaStandart()
    {
        /* Penambahan Squence */
        $data['title']="Detail Biaya Standart";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/biaya_standart'));
        }
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_mst_biaya_standart_mst($param)->getRowArray();
        return $this->template->render('production/biaya_standart/v_detail_biaya_standart',$data);
    }

    public function cancelBiayaStandart()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/biaya_standart')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.biaya_standart_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/biaya_standart')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

                'status'     => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.biaya_standart_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.biaya_standart_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/biaya_standart')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/biaya_standart')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/biaya_standart')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }


    /* BOM Bill Of Material  */

    public function bom()
    {
        $data['title']="Bill Of Material";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_bom_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clear_bom_Tmp');
            $urlnext = base_url('production/trans/add_bom');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/bom/v_bom',$data);
    }

    function list_bom_mst(){

        $list = $this->m_production->get_trx_bom_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.3';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_bom_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Bill of Material : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detail_bom_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Bill of Material : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelBOM') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Bill of Material : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;
            
            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;
            
            
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            // $row[] = $lm->inputby;
            // $row[] = $lm->inputdate;

            // $row[] = $lm->updateby;
            // $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->trx_bom_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->trx_bom_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_bom(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_bom_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_bom_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/bom/v_add_bom',$data);
    }

    function showing_tmp_bom_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_bom_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_mst_bom_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_trx_bom_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_bom_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.bom_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_bom_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        if (!$doctype_detail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tipe Detail tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.bom_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'bom',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                'minimumqty'   => trim($this->request->getPost('minimumqty')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                // 'dari_bagian'       => trim($this->request->getPost('dari_bagian')),
                'inactive'       => 'false',


                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

        if ($doctype_detail == 'MATERIAL') {

            $idbarang    = strtoupper(trim($this->request->getPost('idbarangMaterial')));
            $nmbarang    = strtoupper(trim($this->request->getPost('nmbarangmaterial')));
            $unit        = strtoupper(trim($this->request->getPost('unitMaterial')));
            $qty         = $this->request->getPost('qtymaterial');
            $standartcost= $this->request->getPost('standartcostmaterial');
            $totalcost   = $this->request->getPost('totalcostmaterial');
            $description = strtoupper(trim($this->request->getPost('description_detail_material')));

        } elseif ($doctype_detail == 'COST') {

            $idbarang    = strtoupper(trim($this->request->getPost('idbarangCost')));
            $nmbarang    = strtoupper(trim($this->request->getPost('nmbarangcost')));
            $unit        = strtoupper(trim($this->request->getPost('unitCost')));
            $qty         = $this->request->getPost('qtycost');
            $standartcost= $this->request->getPost('standartcostcost');
            $totalcost   = $this->request->getPost('totalcostcost');
            $description = strtoupper(trim($this->request->getPost('description_detail_cost')));

        } elseif ($doctype_detail == 'WIP') {

            $idbarang    = strtoupper(trim($this->request->getPost('idbarangWip')));
            $nmbarang    = strtoupper(trim($this->request->getPost('nmbarangwip')));
            $unit        = strtoupper(trim($this->request->getPost('unitWip')));
            $qty         = $this->request->getPost('qtywip');
            $standartcost= $this->request->getPost('standartcostwip');
            $totalcost   = $this->request->getPost('totalcostwip');
            $description = strtoupper(trim($this->request->getPost('description_detail_wip')));

        } else {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tipe detail tidak valid'
            ]);

        }


        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.bom_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('doctype_detail', $doctype_detail)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('description', $description);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama dengan keterangan yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('idurut', $idurut)
                ->update([
                    'doctype_detail' => $doctype_detail,
                    'idbarang'       => $idbarang,
                    'nmbarang'       => $nmbarang,
                    'unit'           => $unit,
                    'docdate'        => trim($this->request->getPost('docdate')),
                    'qty'            => $qty,
                    'standartcost'   => $standartcost,
                    'totalcost'      => $totalcost,
                    'description'    => $description,
                    'updateby'       => $nama,
                    'updatedate'     => date('Y-m-d H:i:s')
                ]);

            if (!$updateDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {

            $inputdate = date('Y-m-d H:i:s');

            $rawUnique = $doctype_detail . '|' . $idbarang . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $insertDetail = $builderDetail->insert([
                'docno'          => $docno,
                'doctype_detail' => $doctype_detail,
                'idbarang'       => $idbarang,
                'nmbarang'       => $nmbarang,
                'unit'           => $unit,
                'docdate'        => trim($this->request->getPost('docdate')),
                'qty'            => $qty,
                'standartcost'   => $standartcost,
                'totalcost'      => $totalcost,
                'description'    => $description,
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s'),
                'uniqueid'       => $uniqueid
            ]);
        }

        $totals = $db->query("
            SELECT
                COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'MATERIAL' THEN totalcost ELSE 0 END),0) AS ttlmaterial,
                COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'COST' THEN totalcost ELSE 0 END),0) AS ttlcost,
                COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'WIP' THEN totalcost ELSE 0 END),0) AS ttlwip
            FROM sc_tmp.bom_dtl
            WHERE docno = ?
        ", [$docno])->getRowArray();

        $ttlmaterial = (float)$totals['ttlmaterial'];
        $ttlcost     = (float)$totals['ttlcost'];
        $ttlwip      = (float)$totals['ttlwip'];
        $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

        $db->table('sc_tmp.bom_mst')
            ->where('docno', $docno)
            ->update([
                'ttlmaterial' => $ttlmaterial,
                'ttlcost'     => $ttlcost,
                'ttlwip'      => $ttlwip,
                'ttlprice'    => $ttlprice,
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clear_bom_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_bom_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.bom_mst');
        $builder_dtl = $this->db->table('sc_tmp.bom_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/bom'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/bom'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/bom'));
        }

    }


    //MATERIAL
    function list_tmp_bom_material_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_bom_material_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_bom_material_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_bom_material_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_bom_material_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_bom_material_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_bom_material_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_bom_material_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    //COST
    function list_tmp_bom_cost_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_bom_cost_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_bom_cost_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_bom_cost_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_bom_cost_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_bom_cost_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_bom_cost_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_bom_cost_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    //WIP
    function list_tmp_bom_wip_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_bom_wip_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_bom_wip_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_bom_wip_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_bom_wip_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_bom_wip_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_bom_wip_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_bom_wip_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_bom(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_bom_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_bom_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_bom_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.bom_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.3');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_bom'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'description'        => $keterangan,
//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.3'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/bom'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.3',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/bom'));
            }



        }

    }

    public function get_bom_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_bom_dtl(" and idurut='$id'");

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,   
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        ]);
    }

    
    public function delete_bom_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.bom_dtl');
        $nama = trim(session()->get('nama'));

        // ambil ids (bisa array atau single)
        $ids = $request->getPost('ids');

        // normalisasi: pastikan array
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // Ambil docno dari salah satu detail yang akan dihapus
            $firstId = $ids[0];

            $row = $db->table('sc_tmp.bom_dtl')
                ->select('docno')
                ->where('idurut', $firstId)
                ->get()
                ->getRow();

            if (!$row) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $docno = $row->docno;

            $builder
                ->whereIn('idurut', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            $totals = $db->query("
                SELECT
                    COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'MATERIAL' THEN totalcost ELSE 0 END),0) AS ttlmaterial,
                    COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'COST' THEN totalcost ELSE 0 END),0) AS ttlcost,
                    COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'WIP' THEN totalcost ELSE 0 END),0) AS ttlwip
                FROM sc_tmp.bom_dtl
                WHERE docno = ?
            ", [$docno])->getRowArray();

            $ttlmaterial = (float)$totals['ttlmaterial'];
            $ttlcost     = (float)$totals['ttlcost'];
            $ttlwip      = (float)$totals['ttlwip'];
            $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

            $db->table('sc_tmp.bom_mst')
                ->where('docno', $docno)
                ->update([
                    'ttlmaterial' => $ttlmaterial,
                    'ttlcost'     => $ttlcost,
                    'ttlwip'      => $ttlwip,
                    'ttlprice'    => $ttlprice,
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data BOM Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_bom_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/bom')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_trx.bom_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/bom')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_trx.bom_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_bom')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/bom')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/bom')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detail_bom_()
    {
        /* Penambahan Squence */
        $data['title']="Detail Bill Of Material";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/bom'));
        }
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_trx_bom_mst($param)->getRowArray();
        return $this->template->render('production/bom/v_detail_bom',$data);
    }

    public function cancel_bom_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/bom')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.bom_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/bom')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

                'status'     => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.bom_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.bom_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/bom')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/bom')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/bom')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }



    
    /* Working Order  */

    public function workingorder()
    {
        $data['title']="Working Order";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.4'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_workingorder_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clear_workingorder_Tmp');
            $urlnext = base_url('production/trans/add_workingorder');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.4';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/workingorder/v_workingorder',$data);
    }

    function list_workingorder_mst(){

        $list = $this->m_production->get_trx_workingorder_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.4';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_workingorder_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Working Order : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detail_workingorder_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Working Order : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelWO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Working Order : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = $lm->nmcustomer;

            $row[] = $lm->alamatcust;
            
            $row[] = $lm->nmkota;
            $row[] = $lm->noso;
            $row[] = $lm->docdatefinish;
            
            
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            // $row[] = $lm->inputby;
            // $row[] = $lm->inputdate;

            // $row[] = $lm->updateby;
            // $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->trx_workingorder_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->trx_workingorder_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_workingorder(){

        $data['title']="Input Working Order";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.4'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_workingorder_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_workingorder_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/workingorder/v_add_workingorder',$data);
    }

    function showing_tmp_workingorder_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_workingorder_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_trx_workingorder_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_trx_workingorder_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_workingorder_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.workingorder_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_workingorder_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docnoWo  = trim($this->request->getPost('docno'));
        $docnoBom = trim($this->request->getPost('docnobom'));
        // $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        
        if (!$docnoWo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        // if (!$doctype_detail) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Tipe Detail tidak ditemukan'
        //     ]);
        // }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.workingorder_mst');

        $exists = $builderHeader
            ->where('docno', $docnoWo)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $kdcustomer   = trim($this->request->getPost('kdcustomer'));
            $nmcustomer   = trim($this->request->getPost('nmcustomer'));
            $alamatcustomer   = trim($this->request->getPost('alamatcustomer'));

            $insertHeader = $builderHeader->insert([
                'docno'      => $docnoWo,
                'doctype'    => 'workingorder',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'docdatefinish'    => trim($this->request->getPost('docdatefinish')),
                'kdcustomer'     => strtoupper($kdcustomer),
                'nmcustomer'     => strtoupper($nmcustomer),
                'alamatcustomer' => strtoupper($alamatcustomer),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'noso' => strtoupper(trim($this->request->getPost('noso'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. CEK BOM SUDAH DIPILIH ATAU BELUM
        |--------------------------------------------------------------------------
        */
        $cekBom = $db->table('sc_tmp.workingorder_bom_mst')
            ->where('docno', $docnoBom)
            ->where('inputby', $nama)
            ->where('docref', $docnoWo)
            ->countAllResults();

        if ($cekBom > 0) {

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'BOM sudah dipilih'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. AMBIL BOM MST
        |--------------------------------------------------------------------------
        */
        $bomMst = $db->table('sc_trx.bom_mst')
            ->where('docno', $docnoBom)
            ->get()
            ->getRowArray();

        if (!$bomMst) {

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'BOM tidak ditemukan'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. INSERT WORKINGORDER_BOM_MST
        |--------------------------------------------------------------------------
        */
        $insertBomMst = $bomMst;

        unset($insertBomMst['id']);
        unset($insertBomMst['id']);
        unset($insertBomMst['dari_bagian']);
        unset($insertBomMst['inactive']);
        
        $insertBomMst['docref']    = $docnoWo;
        $insertBomMst['desc_bom']    = $insertBomMst['keterangan'];
        $insertBomMst['inputby']   = $nama;
        $insertBomMst['inputdate'] = date('Y-m-d H:i:s');
        unset($insertBomMst['keterangan']);

        $db->table('sc_tmp.workingorder_bom_mst')
            ->insert($insertBomMst);

        /*
        |--------------------------------------------------------------------------
        | 5. AMBIL BOM DETAIL
        |--------------------------------------------------------------------------
        */
        $bomDtl = $db->table('sc_trx.bom_dtl')
            ->where('docno', $docnoBom)
            ->get()
            ->getResultArray();

        /*
        |--------------------------------------------------------------------------
        | 6. INSERT WORKINGORDER_BOM_DTL
        |--------------------------------------------------------------------------
        */
        foreach ($bomDtl as $row) {

            unset($row['id']);

            $row['docref']    = $docnoWo;
            $row['inputby']   = $nama;
            $row['inputdate'] = date('Y-m-d H:i:s');

            $db->table('sc_tmp.workingorder_bom_dtl')
                ->insert($row);
        }


        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clear_workingorder_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_workingorder_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.workingorder_mst');
        $builder_dtl = $this->db->table('sc_tmp.workingorder_bom_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/workingorder'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/workingorder'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/workingorder'));
        }

    }

    //BOM
    function list_tmp_workingorder_bom_mst(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_workingorder_bom_mst_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            // $row[] = $lm->idurut;
            $row[] = '
            <div class="btn-group">

                <button type="button"
                        class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$lm->docno.'">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button"
                        class="btn btn-success btn-sm btn-save-update d-none"
                        data-id="'.$lm->docno.'">
                    <i class="fa fa-check"></i>
                </button>

                &nbsp
                <button type="button"
                        class="btn btn-secondary btn-sm btn-cancel-update d-none"
                        data-id="'.$lm->docno.'">
                    <i class="fa fa-times"></i>
                </button>
                <button type="button"
                        class="btn btn-danger btn-sm btn-delete"
                        data-id="'.$lm->docno.'">
                    <i class="fa fa-trash"></i>
                </button>
                

            </div>';
            $row[] = $lm->docno;
            $row[] = $lm->desc_bom;
            $row[] = $lm->idbarang_jadi;
            $row[] = $lm->nmbarang_jadi;
            // $row[] = '<div class="ratakanan">'. number_format($lm->buildfor, 2, '.', ',') . '</div>';
            $row[] = '
            <input type="text"
                class="form-control form-control-sm jtsseparator ratakanan buildfor-input"
                data-id="'.$lm->docno.'"
                value="'.number_format($lm->buildfor, 2, '.', ',').'"
                disabled>';
            $row[] = $lm->buildunit;
            // $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->ttlcost, 2, '.', ',') . '</div>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_workingorder_bom_mst_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_workingorder_bom_mst_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_workingorder_bom_mst(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_workingorder_bom_mst_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            // $row[] = $lm->idurut;
            
            //item
            $row[] = $lm->docno;
            $row[] = $lm->desc_bom;
            $row[] = $lm->idbarang_jadi;
            $row[] = $lm->nmbarang_jadi;
            $row[] = '<div class="ratakanan">'. number_format($lm->buildfor, 2, '.', ',') . '</div>';
            $row[] = $lm->buildunit;
            // $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->ttlcost, 2, '.', ',') . '</div>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_workingorder_bom_mst_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_workingorder_bom_mst_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    //MATERIAL
    function list_tmp_workingorder_material_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_workingorder_material_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->docref;
            $row[] = $lm->docno;
            
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_workingorder_material_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_workingorder_material_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_workingorder_material_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_workingorder_material_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->docref;
            $row[] = $lm->docno;
            
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_workingorder_material_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_workingorder_material_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    //COST
    function list_tmp_workingorder_cost_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_workingorder_cost_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->docref;
            $row[] = $lm->docno;
            
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_workingorder_cost_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_workingorder_cost_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_workingorder_cost_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_workingorder_cost_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->docref;
            $row[] = $lm->docno;
            
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_workingorder_cost_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_workingorder_cost_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    //WIP
    function list_tmp_workingorder_wip_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_workingorder_wip_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->docref;
            $row[] = $lm->docno;
            
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_workingorder_wip_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_workingorder_wip_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_workingorder_wip_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_workingorder_wip_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->docref;
            $row[] = $lm->docno;
            
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_workingorder_wip_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_workingorder_wip_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_workingorder(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_workingorder_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_workingorder_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_workingorder_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.workingorder_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.4');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.4',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_workingorder'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $kdcustomer = strtoupper(trim($this->request->getPost('kdcustomer')));
            $nmcustomer = strtoupper(trim($this->request->getPost('nmcustomer')));
            $alamatcustomer = strtoupper(trim($this->request->getPost('alamatcustomer')));
            $noso = strtoupper(trim($this->request->getPost('noso')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'noso'              => $noso,
                'keterangan'        => $keterangan,
                'kdcustomer'        => $kdcustomer,
                'nmcustomer'        => $nmcustomer,
                'alamatcustomer'    => $alamatcustomer,
//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.4'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/workingorder'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.4',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/workingorder'));
            }



        }

    }

    public function get_workingorder_bom_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_workingorder_bom_dtl(" and idurut='$id'");

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,   
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        ]);
    }

    public function save_update_workingorder_bom()
    {
        $db = \Config\Database::connect();

        $nama = trim($this->session->get('nama'));

        $docno = trim($this->request->getPost('docno'));

        $buildforBaru = str_replace(',', '', $this->request->getPost('buildfor'));

        $db->transBegin();

        try{

            $mst = $db->query("
                SELECT *
                FROM sc_tmp.workingorder_bom_mst
                WHERE TRIM(docno)=?
                AND TRIM(inputby)=?
            ",[$docno,$nama])->getRow();

            if(!$mst){
                throw new \Exception('Data BOM tidak ditemukan');
            }

            $buildforLama = (float)$mst->buildfor;

            if($buildforLama <= 0){
                throw new \Exception('Build For lama tidak valid');
            }

            /*
            update mst dulu
            */

            $db->table('sc_tmp.workingorder_bom_mst')
                ->where('docno',$docno)
                ->where('inputby',$nama)
                ->update([
                    'buildfor'=>$buildforBaru,
                    'updatedate'=>date('Y-m-d H:i:s'),
                    'updateby'=>$nama
                ]);

            /*
            recalc detail
            */

            $detail = $db->query("
                SELECT *
                FROM sc_tmp.workingorder_bom_dtl
                WHERE TRIM(docno)=?
                AND TRIM(inputby)=?
            ",[$docno,$nama])->getResult();

            foreach($detail as $d){

                $factor = $d->qty / $buildforLama;

                $qtyBaru = $factor * $buildforBaru;

                $totalCostBaru = $qtyBaru * $d->standartcost;

                $db->table('sc_tmp.workingorder_bom_dtl')
                    ->where('uniqueid',$d->uniqueid)
                    ->update([
                        'qty'=>$qtyBaru,
                        'totalcost'=>$totalCostBaru,
                        'updateby'=>$nama,
                        'updatedate'=>date('Y-m-d H:i:s')
                    ]);

            }

            /*
            update total mst
            */

            $ttl = $db->query("
                SELECT COALESCE(SUM(totalcost),0) ttl
                FROM sc_tmp.workingorder_bom_dtl
                WHERE TRIM(docno)=?
                AND TRIM(inputby)=?
            ",[$docno,$nama])->getRow();

            $db->table('sc_tmp.workingorder_bom_mst')
                ->where('docno',$docno)
                ->where('inputby',$nama)
                ->update([
                    'ttlcost'=>$ttl->ttl
                ]);

            if($db->transStatus() === FALSE){
                throw new \Exception('Gagal update');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'=>true
            ]);

        }catch(\Exception $e){

            $db->transRollback();

            return $this->response->setJSON([
                'status'=>false,
                'message'=>$e->getMessage()
            ]);

        }
    }

    
    public function delete_workingorder_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.workingorder_bom_mst');
        $nama = trim(session()->get('nama'));

        // ambil ids (bisa array atau single)
        $ids = $request->getPost('ids');

        // normalisasi: pastikan array
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // Ambil docno dari salah satu detail yang akan dihapus
            $firstId = $ids[0];

            $row = $db->table('sc_tmp.workingorder_bom_mst')
                ->select('docno')
                ->where('docno', $firstId)
                ->where('inputby', $nama)
                ->get()
                ->getRow();

            if (!$row) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $docno = $row->docno;

            $builder->whereIn('docno', $ids)
            ->where('inputby', $nama)
            ->delete();

            $row = $db->table('sc_tmp.workingorder_bom_dtl')
                ->select('docno')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data WO Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_workingorder_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/workingorder')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_trx.workingorder_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/workingorder')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_trx.workingorder_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_workingorder')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/workingorder')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/workingorder')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detail_workingorder_()
    {
        /* Penambahan Squence */
        $data['title']="Detail Bill Of Material";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/workingorder'));
        }
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.4'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_trx_workingorder_mst($param)->getRowArray();
        return $this->template->render('production/workingorder/v_detail_workingorder',$data);
    }

    public function cancel_workingorder_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/workingorder')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.workingorder_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/workingorder')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

                'status'     => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.workingorder_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.workingorder_bom_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/workingorder')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/workingorder')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/workingorder')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }



    // ======================= WORK ORDER EXECUTION


    
     public function woe()
    {
        $data['title']="Work Order Execution";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.5'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_woe_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clearEntryWOE');
            $urlnext = base_url('production/trans/addWOE');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.5';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/woe/v_list_woe',$data);
    }

    function detailWOE()
    {
        /* Penambahan Squence */
        $data['title']="Detail Work Order Execution";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/woe'));
        }
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.5'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_woe_master($param)->getRowArray();
        return $this->template->render('production/woe/v_detail_woe',$data);
    }

    function list_woe(){
        $list = $this->m_production->get_t_front_woe_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.R.A.5';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $approveBtn  = '';
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('production/trans/updateWOE') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Work Order Execution : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Work Order Execution 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('production/trans/detailWOE') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Work Order Execution : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Work Order Execution 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('production/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Work Order Execution : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Work Order Execution 
                </a>';
            }


            // if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
            //         $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
            //             <i class="fa fa-check-circle"></i> Approve</a>';
            // }

            // if (trim($status) == 'APPROVED') {
            //     $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
            //         <i class="fa fa-times-circle"></i> Disapprove</a>';
            // }


            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                    $menuContent .= $printBtn;
                }

            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canApprove)   $menuContent .= $approveBtn;
                if ($canApprove)   $menuContent .= $disapproveBtn;
            }

            // =========================
            // Final Dropdown (jangan tampil kalau kosong)
            // =========================
            if ($menuContent !== '') {

                $dropdownMenu = '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu">
                            ' . $menuContent . '
                        </div>
                    </div>';

            } else {

                // Tidak punya hak akses apapun
                $dropdownMenu = '';
            }

            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
            $status = $lm->status_desc ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'REVISION/EDITING':
                    $badgeClass = 'badge-warning';
                    break;
                case 'FINAL USER':
                    $badgeClass = 'badge-info';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $row[] = $lm->bagian;
            $row[] = $lm->idbarang_jadi;
            $row[] = $lm->batchno;
            // $row[] = $lm->buildfor;
            $row[] = '<div class="ratakanan">'. number_format($lm->buildfor, 0, '.', ',') . '</div>';
            $row[] = $lm->keterangan;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->t_front_woe_view_count_all(),
            "recordsFiltered" => $this->m_production->t_front_woe_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function clearEntryWOE()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_woe_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.woe');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('production/trans/woe'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/woe'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('production/trans/woe'));
        }

    }

    function addWOE()
    {
        /* Penambahan Squence */
        $data['title']="Input Work Order Execution";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.5'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_woe_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_woe_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/woe/v_add_woe',$data);
    }


   public function getBranchInfoWOE()
    {
        $idbranch = trim($this->request->getGet('idbranch'));

        $row = $this->db->table('sc_mst.branchjob')
            ->select('nmbranch')
            ->where('idbranch', $idbranch)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang tidak ditemukan'
            ]);
        }

        // mapping nmbranch → kode suffix
        $map = [
            'PT JATIM TAMAN STEEL MFG' => 'PT',
            'PLANT I'                 => 'PA',
            'PLANT II'                => 'PB',
        ];

        $kodeSuffix = $map[trim($row['nmbranch'])] ?? '';

        if ($kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mapping cabang belum diset'
            ]);
        }

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix
        ]);
    }

    public function getNextSuffixWOE()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.woe')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }

    public function initWOEHeader()
    {
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper($this->request->getPost('docno'));
        $docdate    = $this->request->getPost('docdate');
        $cabang     = $this->request->getPost('cabang');
        $pemohon    = strtoupper($this->request->getPost('pemohon'));
        // $estpakai   = $this->request->getPost('estpakai');
        // $keterangan = strtoupper($this->request->getPost('keterangan'));

        if (!$docno || !$docdate || !$cabang) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Header belum lengkap'
            ]);
        }

        $builder = $this->db->table('sc_tmp.woe');
        $exists = $builder->where('docno', $docno)->countAllResults();

        // HEADER SUDAH ADA → TIDAK PERLU RELOAD
        if ($exists > 0) {
            return $this->response->setJSON([
                'success' => true,
                'reload'  => false
            ]);
        }

        // HEADER BARU → INSERT
        $builder->insert([
            'docno'      => $docno,
            'docdate'    => $docdate,
            'cabang'     => $cabang,
            'pemohon'    => $pemohon,
            // 'estpakai'   => $estpakai,
            'status'     => 'E',
            // 'keterangan' => $keterangan,
            'inputby'    => $nama,
            'inputdate'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'reload'  => true   // ⬅ PENTING
        ]);
    }



    public function saveWOEDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $docnopo = strtoupper(trim($this->request->getPost('docnopo')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya
        
        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno || !$docnopo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno atau Docno PP tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.woe');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST
        
        if ($exists == 0) {
            $isinclusive = strtoupper(trim(
                $this->request->getPost('isinclusive') 
                ?? $dataprocess->isinclusive 
                ?? 'NO'
            ));

            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                // 'senddate'   => date('Y-m-d', strtotime(trim($this->request->getPost('senddate')))),
                'jthtempo'     => $this->request->getPost('jthtempo'),
                'isinclusive'     => $isinclusive,
                
                'kdsupplier'    => strtoupper($this->request->getPost('kdsupplier')),
                'alamatsupplier'    => strtoupper($this->request->getPost('alamatsupplier')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                'idtax'    => strtoupper($this->request->getPost('idtax')),
                'biayavol'    => ($this->request->getPost('biayavol')),
                'biayavol2'    => ($this->request->getPost('biayavol2')),
                'nosj'    => strtoupper($this->request->getPost('nosj')),
                'nofaktur'    => strtoupper($this->request->getPost('nofaktur')),
                'currcode'    => strtoupper($this->request->getPost('currcode')),
                'kurs'    => ($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.woe_dtl');
        $insertCount = 0;
        $message = '';

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan apa adanya
            // =====================================================
            // MODE EDIT - UPDATE DATA
            // =====================================================
            $qty         = $this->request->getPost('qty');
            $qtybonus    = $this->request->getPost('qtybonus') ?: 0;
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            $volitem   = $this->request->getPost('volitem') ?: 0;
            $biaya   = $this->request->getPost('biaya') ?: 0;
            $biaya2   = $this->request->getPost('biaya2') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $descriptionpo = strtoupper($this->request->getPost('descriptionpo'));
            $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang = strtoupper($this->request->getPost('idgudang'));
            $idspec = strtoupper($this->request->getPost('idspec'));

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                'qtybonus'     => $qtybonus,
                'harga'        => $harga,
                'multidisc'    => $multidisc,
                'nilai'        => $nilai,
                'volitem'      => $volitem,

                'biaya'      => $biaya,
                'biaya2'      => $biaya2,
                'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                'idspec'      => $idspec,

                'descriptionpo' => $descriptionpo,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);



            
            
            $message = 'Data berhasil diupdate';
            
        } else {
            // =====================================================
            // MODE ADD - INSERT DATA DARI PP
            // =====================================================
            $poDetails = $db->query("
                SELECT 
                    docno,
                    idbarang,
                    uniqueid,
                    nmbarang,
                    unit,
                    qty,
                    qtybonus,
                    multidisc,
                    harga,
                    nilai,
                    descriptionpo,
                    descriptionpp
                FROM sc_trx.po_dtl
                WHERE TRIM(docno) = ?
            ", [$docnopo])->getResult();

            if (empty($poDetails)) {
                $db->transRollback();   
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PO tidak ditemukan'
                ]);
            }

            foreach ($poDetails as $row) {
                // CEK APAKAH ITEM SUDAH ADA DI TMP
                // $duplicate = $builderDetail
                //     ->where('docno', $docno)
                //     ->where('docnopp', $docnopp)
                //     ->where('idbarang', $row->idbarang)
                //     ->where('inputby', $nama)
                //     ->countAllResults();

                $duplicate = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnopo'       => $docnopo,
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $row->qty,
                        'qtybonus'      => 0, // Default 0 untuk new insert
                        'harga'         => $row->harga, // Default 0 untuk new insert
                        'multidisc'     => 0, // Default 0 untuk new insert
                        'volitem'     => 0, // Default 0 untuk new insert
                        'biaya'     => 0, // Default 0 untuk new insert
                        'biaya2'     => 0, // Default 0 untuk new insert
                        'nilai'         => $row->nilai, // Default 0 untuk new insert
                        'descriptionpp' => $row->descriptionpp,
                        'descriptionpo' => $row->descriptionpo,
                        'inputby'       => $nama,
                        'inputdate'     => date('Y-m-d H:i:s')
                    ]);

                    $insertCount++;
                }
            }
            
            $message = $insertCount > 0 
                        ? "$insertCount item berhasil ditambahkan"
                        : "Semua item sudah ada sebelumnya";
        }

        $woeHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
        $idtax = $woeHeader['idtax'] ?? '';
        
        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.woe_dtl');
        $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();
        
        $dpp = $totalDpp['total_dpp'] ?? 0;
        
        // Hitung jumlah pajak berdasarkan idtax
        $jumlahPajak = 0;
        
        if (!empty($idtax) && trim($idtax) !== 'NON'  && $dpp > 0) {
            // Ambil detail tax dari sc_mst.tax_dtl
            $builderTaxDtl = $db->table('sc_mst.tax_dtl');
            $taxDetails = $builderTaxDtl->select('percentation')
                ->where('idtax', $idtax)
                ->get()
                ->getResultArray();
            
            foreach ($taxDetails as $tax) {
                $persentase = $tax['percentation'] ?? 0;
                $jumlahPajak += $dpp * ($persentase / 100);
            }
        }
        
        // Hitung total (DPP + Jumlah Pajak)
        $total = $dpp + $jumlahPajak;
        
        // Update header WOE
        $builderHeader->where('docno', $docno)->update([
            'dpp' => number_format($dpp, 2, '.', ''),
            'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
            'updateby' => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusWOE()
    {
        $docno = $this->request->getPost('docno');
        $status = $this->request->getPost('status');
        if (!$docno || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_trx.woe');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function updateWOE()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_production->q_woe_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.woe');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('production/trans/addWOE'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('production/trans/woe'));
        }
    }

    function showing_woetrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_production->q_woe_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_woetemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_woe_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_woe_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_production->q_woe_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    function finalEntryWOE() {
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getPost('docno'));
        
        // Hapus data error sebelumnya
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.5');
        $builder_trxerror->delete();

        if ($docno === '') {
            // Insert error jika docno kosong
            $infotrxerror = [
                'userid' => $nama,
                'errorcode' => 3,
                'modul' => 'I.R.A.5',
            ];
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/production/trans/addWOE'));
        }
        
        // Ambil semua data dari POST
        $cabang = trim($this->request->getPost('cabang'));
        $docdate = trim($this->request->getPost('docdate'));
        $pemohon = trim($this->request->getPost('pemohon'));
        $bagian = strtoupper($this->request->getPost('bagian'));
        $wono = strtoupper($this->request->getPost('wono'));
        $bomno = strtoupper($this->request->getPost('bomno'));
        $idbarang_jadi = strtoupper($this->request->getPost('idbarang_jadi'));
        $nmbarang_jadi = strtoupper($this->request->getPost('nmbarang_jadi'));
        $desc_bom = strtoupper($this->request->getPost('desc_bom'));
        $batchno = strtoupper($this->request->getPost('batchno'));
        $keterangan = strtoupper($this->request->getPost('keterangan'));
        
        // Bersihkan format angka
        $buildfor_clean = $this->cleanNumber($this->request->getPost('buildfor'));
        // $dpp_clean = $this->cleanNumber($this->request->getPost('dpp'));
        // $jumlahpajak_clean = $this->cleanNumber($this->request->getPost('jumlahpajak'));
        // $total_clean = $this->cleanNumber($this->request->getPost('total'));
        
        // Convert date
        $docdateph = !empty($docdate) ? date('Y-m-d', strtotime(str_replace('-', '/', $docdate))) : null;
        
        // **CEK APAKAH DATA SUDAH ADA**
        $existingData = $this->db->table('sc_tmp.woe')
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->get()
            ->getRowArray();
        
        $builder = $this->db->table('sc_tmp.woe');
        
        if ($existingData) {
            // **UPDATE DATA YANG SUDAH ADA**
            $updateHeader = [
                'docdate' => $docdateph,
                'pemohon' => $pemohon,
                'bagian' => $bagian,
                'wono' => $wono,
                'bomno' => $bomno,
                'desc_bom' => strtoupper($desc_bom),
                'idbarang_jadi' => strtoupper($idbarang_jadi),
                'nmbarang_jadi' => strtoupper($nmbarang_jadi),
                'batchno' => $batchno,
                'buildfor' => $buildfor_clean,
                'keterangan' => strtoupper($keterangan), // Hanya 1 kali
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),
            ];
            
            $builder->where('inputby', $nama)
                    ->where('docno', $docno)
                    ->update($updateHeader);
        } else {
            // **INSERT DATA BARU**
            $insertHeader = [
                'docno' => $docno,
                'cabang' => $cabang,
                'docdate' => $docdateph,
                'pemohon' => $pemohon,
                'bagian' => $bagian,
                'wono' => $wono,
                'bomno' => $bomno,
                'desc_bom' => strtoupper($desc_bom),
                'idbarang_jadi' => strtoupper($idbarang_jadi),
                'nmbarang_jadi' => strtoupper($nmbarang_jadi),
                'batchno' => $batchno,
                'buildfor' => $buildfor_clean,
                'keterangan' => strtoupper($keterangan),
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
                'status' => 'E' // Status awal E
            ];
            
            $builder->insert($insertHeader);
        }
        
        // **UPDATE STATUS MENJADI F** (untuk trigger)
        $updateStatus = $builder
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->update(['status' => 'F']);
        
        if ($updateStatus) {
            // Berhasil update status
            $paramerror=" and userid='$nama' and modul='I.R.A.5'";
            $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
            $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

            // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

            return redirect()->to(base_url('/production/trans/woe'));
        } else {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                // 'nomorakhir1' => $cek->getNumRows(),
                // 'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.5',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/production/trans/addWOE'));
        }
    }


    private function cleanNumber($value) {
        if (empty($value)) return 0;
        return str_replace(',', '', $value);
    }

    function show_woe(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.woe');

       $builder = $builder
            ->where('docno', $docno)
            ->update([
                'status'=> 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s')
            ]);

        
        $enc_docno = $this->fiky_encryption->sealed($docno);
        
        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Report Void Permintaan Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("production/trans/api_woe/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_woe.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_woe(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docno')));
        //$docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        // $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        // $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

       // $ddate = explode(' - ',$docdate);
       // $tgl1 = date('Y-m-d',strtotime($ddate[0]));
       // $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($docno) or $docno==='') {
            $param_brg = "";
        } else {
            $param_brg = " and docno='$docno'";
        }

        // //idgroup
        // if (!empty($idgroup)) {
        //     $param_group=" and idgroup='$idgroup'";
        // } else {  $param_group=""; }


        $databranch = $this->m_global->q_master_branch();
        $param=" and docno='$docno'";
        $datamst = $this->m_production->q_woe_master($param);
        $datadtl = $this->m_production->q_woe_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;        
        if ($detail) {

            $tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
        
            // Tambahkan properti baru isPindah
            $detail->isPindah = false; // Default value
            if ($tujuan === 'pindah') {
                $detail->isPindah = true;
            }

             // Tambahkan properti baru isPembuangan
             $detail->isPembuangan = false; // Default value
             if ($tujuan === 'pembuangan') {
                 $detail->isPembuangan = true;
             }

            // Tambahkan properti baru isPinjam
            $detail->isPinjam = false; // Default value
            if ($tujuan === 'pinjam') {
                $detail->isPinjam = true;
            }

            $isreturn = isset($detail->isreturn) ? trim($detail->isreturn) : '';
             // Tambahkan properti baru iskembali
             $detail->iskembali = false; // Default value
             if ($isreturn === 'kembali') {
                 $detail->iskembali = true;
             }

             $detail->istidakkembali = false; // Default value
             if ($isreturn === 'tidak_kembali') {
                 $detail->istidakkembali = true;
             }

             $jenisbarang = isset($detail->jenisbarang) ? trim($detail->jenisbarang) : '';
              // Tambahkan properti baru isAset
              $detail->isAset = false; // Default value
              if ($jenisbarang === 'aset') {
                  $detail->isAset = true;
              }

              // Tambahkan properti baru isPersediaan
              $detail->isPersediaan = false; // Default value
              if ($jenisbarang === 'persediaan') {
                  $detail->isPersediaan = true;
              }

              // Tambahkan properti baru isLainlain
              $detail->isLainlain = false; // Default value
              if ($jenisbarang === 'lainlain') {
                  $detail->isLainlain = true;
              }
        }

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    //'date1' => date('d-m-Y',strtotime($tgl1)),
                    //'date2' => date('d-m-Y',strtotime($tgl2)),
                    'date1' => date('d-m-Y'),
                    'date2' => date('d-m-Y'),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,
                    'param' => $param,

                    ]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }








    /* MATERIAL RELEASE  */

    public function materialrelease()
    {
        $data['title']="Material Release";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.8'; $versirelease='I.R.A.8/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.8'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_materialrelease_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clear_materialrelease_Tmp');
            $urlnext = base_url('production/trans/add_materialrelease');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.8';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/materialrelease/v_materialrelease',$data);
    }

    function list_materialrelease_mst(){

        $list = $this->m_production->get_trx_materialrelease_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.8';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_materialrelease_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Material Release : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detail_materialrelease_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Material Release : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelMaterial Release') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Material Release : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;
            
            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;
            
            
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            // $row[] = $lm->inputby;
            // $row[] = $lm->inputdate;

            // $row[] = $lm->updateby;
            // $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->trx_materialrelease_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->trx_materialrelease_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_materialrelease(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.8'; $versirelease='I.R.A.8/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.8'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_materialrelease_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_materialrelease_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/materialrelease/v_add_materialrelease',$data);
    }

    function showing_tmp_materialrelease_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_materialrelease_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_trx_materialrelease_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_trx_materialrelease_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_materialrelease_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.materialrelease_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_materialrelease_from_wo()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        // if (!$doctype_detail) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Tipe Detail tidak ditemukan'
        //     ]);
        // }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.materialrelease_mst');
        $builderDetail = $db->table('sc_tmp.materialrelease_dtl');
        $oldWoeno  = trim($this->request->getPost('old_woeno'));
        $woeno = trim($this->request->getPost('woeno'));
        $wono  = trim($this->request->getPost('wono'));

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'materialrelease',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'woeno'     => trim($this->request->getPost('woeno')),
                'wono'     => trim($this->request->getPost('wono')),
                'bomno'     => trim($this->request->getPost('bomno')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'nmbarang_jadi'     => trim($this->request->getPost('nmbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                'idlocation'    => trim($this->request->getPost('idlocation')),
                'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        } else {

            $updateHeader = $builderHeader
                ->where('docno',$docno)
                ->where('inputby',$nama)
                ->update([

                    'woeno'        => trim($this->request->getPost('woeno')),
                    'wono'         => trim($this->request->getPost('wono')),
                    'bomno'        => trim($this->request->getPost('bomno')),
                    'idbarang_jadi'=> trim($this->request->getPost('idbarang_jadi')),
                    'nmbarang_jadi'=> trim($this->request->getPost('nmbarang_jadi')),
                    'buildfor'     => trim($this->request->getPost('buildfor')),
                    'buildunit'    => trim($this->request->getPost('buildunit')),
                    'batchno'      => trim($this->request->getPost('batchno')),
                    'bagian'       => trim($this->request->getPost('bagian')),
                    'idlocation'   => trim($this->request->getPost('idlocation')),
                    'nmlocation'   => trim($this->request->getPost('nmlocation')),
                    'keterangan'   => strtoupper(trim($this->request->getPost('keterangan'))),
                    'updateby'     => $nama,
                    'updatedate'   => date('Y-m-d H:i:s')
                ]);

            if (!$updateHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }

        
        // =========================
        // HAPUS DETAIL GENERATE WOE LAMA
        // =========================
        
        if (!empty($oldWoeno)) {
            $delete = $builderDetail
                ->where('docno', $docno)
                ->where('docref', $oldWoeno)
                ->delete();

            if (!$delete) {

                $error = $db->error();

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }
        
        // =========================
        // AMBIL DATA DETAIL
        // =========================

        $wono = trim($this->request->getPost('wono'));

        $list = $db->query("
            SELECT *
            FROM sc_trx.workingorder_bom_dtl
            WHERE trim(docref)=trim(?)
        ",[$wono])->getResult();

        if(empty($list)) {
            $db->transRollback();

            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Detail WO tidak ditemukan.'
            ]);
        }

        foreach($list as $row){

            $rawUnique = $row->idbarang.'|'.$docno.'|'.microtime(true);

            $insert = $builderDetail->insert([

                'docno'=>$docno,
                'docref'=>trim($this->request->getPost('woeno')),
                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'=>$row->idbarang,
                'nmbarang'=>$row->nmbarang,
                'unit'=>$row->unit,
                'qty'=>$row->qty,
                'description'=>$row->description,
                'issub'=> 'F',
                // 'spec'=>$row->spec,

                'inputby'=>$nama,
                'inputdate'=>date('Y-m-d H:i:s'),

                'uniqueid'=>hash('sha256',$rawUnique)

            ]);

            if (!$insert) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }


    public function save_materialrelease_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        // if (!$doctype_detail) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Tipe Detail tidak ditemukan'
        //     ]);
        // }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.materialrelease_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'materialrelease',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'woeno'     => trim($this->request->getPost('woeno')),
                'wono'     => trim($this->request->getPost('wono')),
                'bomno'     => trim($this->request->getPost('bomno')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'nmbarang_jadi'     => trim($this->request->getPost('nmbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                'idlocation'    => trim($this->request->getPost('idlocation')),
                'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

        // $idbarang    = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $qty         = $this->request->getPost('qty');
        $issub = $this->request->getPost('issub') == 'T' ? 'T' : 'F';
        // $issub         = $this->request->getPost('issub');
        // $spec = $this->request->getPost('spec');
        $description = strtoupper(trim($this->request->getPost('description')));

        $idbarang     = null;
        // $nmbarang     = null;

        $idbarangBom  = null;
        // $nmbarangBom  = null;

        if($issub == 'T'){

            $idbarang    = trim($this->request->getPost('idbarang'));
            // $nmbarang    = trim($this->request->getPost('nmbarang'));

        }
        else{

            $idbarangBom = trim($this->request->getPost('idbarang_bom'));
            // $nmbarangBom = trim($this->request->getPost('nmbarang'));

        }


        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.materialrelease_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            // ->where('doctype_detail', $doctype_detail)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('description', $description);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama dengan keterangan yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('idurut', $idurut)
                ->update([
                    // 'doctype_detail' => $doctype_detail,
                    'idbarang'       => $idbarang,
                    'nmbarang'       => $nmbarang,
                    'idbarang_bom'       => $idbarangBom,
                    // 'nmbarang_bom'       => $nmbarangBom,
                    'unit'           => $unit,
                    'docdate'        => trim($this->request->getPost('docdate')),
                    'qty'            => $qty,
                    'issub'            => $issub,
                    'description'    => $description,
                    'updateby'       => $nama,
                    'updatedate'     => date('Y-m-d H:i:s')
                ]);

            if (!$updateDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {

            $inputdate = date('Y-m-d H:i:s');

            $itemUnique = ($issub == 'T') ? $idbarang : $idbarangBom;
            $rawUnique = $itemUnique . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $insertDetail = $builderDetail->insert([
                'docno'          => $docno,
                // 'doctype_detail' => $doctype_detail,
                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'       => $idbarang,
                'nmbarang'       => $nmbarang,
                'idbarang_bom'       => $idbarangBom,
                // 'nmbarang_bom'       => $nmbarangBom,
                'unit'           => $unit,
                'docdate'        => trim($this->request->getPost('docdate')),
                'qty'            => $qty,
                'issub'            => $issub,
                // 'standartcost'   => $standartcost,
                // 'totalcost'      => $totalcost,
                'description'    => $description,
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s'),
                'uniqueid'       => $uniqueid
            ]);

            if (!$insertDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }


        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clear_materialrelease_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_materialrelease_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.materialrelease_mst');
        $builder_dtl = $this->db->table('sc_tmp.materialrelease_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/materialrelease'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/materialrelease'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/materialrelease'));
        }

    }


    //MATERIAL
    function list_tmp_materialrelease_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_materialrelease_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            // $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 2, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_materialrelease_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_materialrelease_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_materialrelease_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_materialrelease_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_materialrelease_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_materialrelease_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_materialrelease(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_materialrelease_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_materialrelease_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_materialrelease_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.materialrelease_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.8');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.8',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_materialrelease'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $batchno = strtoupper(trim($this->request->getPost('batchno')));
            // $idlocation = strtoupper(trim($this->request->getPost('idlocation')));
            // $nmlocation = strtoupper(trim($this->request->getPost('nmlocation')));
            $bagian = strtoupper(trim($this->request->getPost('bagian')));
            $tabno = strtoupper(trim($this->request->getPost('tabno')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'keterangan'       => $keterangan,
                'batchno'           => $batchno,
                'bagian'           => $bagian,
                // 'idlocation'           => $idlocation,
                // 'nmlocation'           => $nmlocation,
                'tabno'           => $tabno,

//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.8'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/materialrelease'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.8',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/materialrelease'));
            }



        }

    }

    public function get_materialrelease_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_materialrelease_dtl(" and idurut='$id'");

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,   
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        ]);
    }

    
    public function delete_materialrelease_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.materialrelease_dtl');
        $nama = trim(session()->get('nama'));

        // ambil ids (bisa array atau single)
        $ids = $request->getPost('ids');

        // normalisasi: pastikan array
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // Ambil docno dari salah satu detail yang akan dihapus
            $firstId = $ids[0];

            $row = $db->table('sc_tmp.materialrelease_dtl')
                ->select('docno')
                ->where('idurut', $firstId)
                ->get()
                ->getRow();

            if (!$row) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $docno = $row->docno;

            $builder
                ->whereIn('idurut', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            // $totals = $db->query("
            //     SELECT
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'MATERIAL' THEN totalcost ELSE 0 END),0) AS ttlmaterial,
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'COST' THEN totalcost ELSE 0 END),0) AS ttlcost,
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'WIP' THEN totalcost ELSE 0 END),0) AS ttlwip
            //     FROM sc_tmp.materialrelease_dtl
            //     WHERE docno = ?
            // ", [$docno])->getRowArray();

            // $ttlmaterial = (float)$totals['ttlmaterial'];
            // $ttlcost     = (float)$totals['ttlcost'];
            // $ttlwip      = (float)$totals['ttlwip'];
            // $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

            // $db->table('sc_tmp.materialrelease_mst')
            //     ->where('docno', $docno)
            //     ->update([
            //         'ttlmaterial' => $ttlmaterial,
            //         'ttlcost'     => $ttlcost,
            //         'ttlwip'      => $ttlwip,
            //         'ttlprice'    => $ttlprice,
            //         'updateby'    => $nama,
            //         'updatedate'  => date('Y-m-d H:i:s')
            //     ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data Material Release Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_materialrelease_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/materialrelease')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_trx.materialrelease_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_trx.materialrelease_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_materialrelease')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/materialrelease')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detail_materialrelease_()
    {
        /* Penambahan Squence */
        $data['title']="Detail Material Release";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/materialrelease'));
        }
        $kodemenu='I.R.A.8'; $versirelease='I.R.A.8/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.8'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_trx_materialrelease_mst($param)->getRowArray();
        return $this->template->render('production/materialrelease/v_detail_materialrelease',$data);
    }

    public function cancel_materialrelease_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/materialrelease')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.materialrelease_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

                'status'     => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.materialrelease_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.materialrelease_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/materialrelease')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/materialrelease')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }

















    /* PENERIMAAN BARANG PRODUKSI  */

    public function penerimaanbp()
    {
        $data['title']="Penerimaan Barang Produksi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.9'; $versirelease='I.R.A.9/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.9'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_penerimaanbp_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clear_penerimaanbp_Tmp');
            $urlnext = base_url('production/trans/add_penerimaanbp');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.9';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/penerimaanbp/v_penerimaanbp',$data);
    }

    function list_penerimaanbp_mst(){

        $list = $this->m_production->get_trx_penerimaanbp_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.9';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_penerimaanbp_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Penerimaan Barang Produksi : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detail_penerimaanbp_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Penerimaan Barang Produksi : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelPenerimaan Barang Produksi') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Penerimaan Barang Produksi : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;
            
            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;
            
            
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            // $row[] = $lm->inputby;
            // $row[] = $lm->inputdate;

            // $row[] = $lm->updateby;
            // $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->trx_penerimaanbp_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->trx_penerimaanbp_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_penerimaanbp(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.9'; $versirelease='I.R.A.9/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.9'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_penerimaanbp_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_penerimaanbp_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/penerimaanbp/v_add_penerimaanbp',$data);
    }

    function showing_tmp_penerimaanbp_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_penerimaanbp_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_trx_penerimaanbp_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_trx_penerimaanbp_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_penerimaanbp_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.penerimaanbp_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_penerimaanbp_from_wo()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        $docdate = trim($this->request->getPost('docdate'));
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        if (!$docdate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal Dokumen belum diisi'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.penerimaanbp_mst');
        $builderDetail = $db->table('sc_tmp.penerimaanbp_dtl');
        $oldWoeno  = trim($this->request->getPost('old_woeno'));
        $woeno = trim($this->request->getPost('woeno'));
        $wono  = trim($this->request->getPost('wono'));

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'penerimaanbp',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'woeno'     => trim($this->request->getPost('woeno')),
                'wono'     => trim($this->request->getPost('wono')),
                'bomno'     => trim($this->request->getPost('bomno')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'nmbarang_jadi'     => trim($this->request->getPost('nmbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                // 'idlocation'    => trim($this->request->getPost('idlocation')),
                // 'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        } else {

            $updateHeader = $builderHeader
                ->where('docno',$docno)
                ->where('inputby',$nama)
                ->update([

                    'woeno'        => trim($this->request->getPost('woeno')),
                    'wono'         => trim($this->request->getPost('wono')),
                    'bomno'        => trim($this->request->getPost('bomno')),
                    'idbarang_jadi'=> trim($this->request->getPost('idbarang_jadi')),
                    'nmbarang_jadi'=> trim($this->request->getPost('nmbarang_jadi')),
                    'buildfor'     => trim($this->request->getPost('buildfor')),
                    'buildunit'    => trim($this->request->getPost('buildunit')),
                    'batchno'      => trim($this->request->getPost('batchno')),
                    'bagian'       => trim($this->request->getPost('bagian')),
                    // 'idlocation'   => trim($this->request->getPost('idlocation')),
                    // 'nmlocation'   => trim($this->request->getPost('nmlocation')),
                    'keterangan'   => strtoupper(trim($this->request->getPost('keterangan'))),
                    'updateby'     => $nama,
                    'updatedate'   => date('Y-m-d H:i:s')
                ]);

            if (!$updateHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }

        
        // =========================
        // HAPUS DETAIL GENERATE WOE LAMA
        // =========================
        
        if (!empty($oldWoeno)) {
            $delete = $builderDetail
                ->where('docno', $docno)
                ->where('docref', $oldWoeno)
                ->delete();

            if (!$delete) {

                $error = $db->error();

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }
        
        // =========================
        // AMBIL DATA DETAIL
        // =========================

        $wono = trim($this->request->getPost('wono'));

        // $list = $db->query("
        //     SELECT *
        //     FROM sc_trx.workingorder_bom_dtl
        //     WHERE trim(docref)=trim(?)
        // ",[$wono])->getResult();

        $list = $db->query("
            SELECT
                w.*,
                COALESCE(sc.newcost,0) AS harga
            FROM sc_trx.workingorder_bom_dtl w

            LEFT JOIN (
                SELECT
                    a.idbarang,
                    a.newcost,
                    ROW_NUMBER() OVER(
                        PARTITION BY a.idbarang
                        ORDER BY a.activedate DESC
                    ) rn
                FROM sc_mst.standart_cost_dtl a
                WHERE a.status='F'
                AND a.activedate <= ?
            ) sc
                ON TRIM(sc.idbarang)=TRIM(w.idbarang)
                AND sc.rn=1

            WHERE TRIM(w.docref)=TRIM(?) and TRIM(w.doctype_detail) = 'MATERIAL'
        ", [
            $docdate,
            $wono
        ])->getResult();

        if(empty($list)) {
            $db->transRollback();

            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Detail WO tidak ditemukan.'
            ]);
        }

        foreach($list as $row){

            $rawUnique = $row->idbarang.'|'.$docno.'|'.microtime(true);
            $harga = $row->harga;
            $qty   = $row->qty;
            $nilai = $qty * $harga;

            $insert = $builderDetail->insert([

                'docno'=>$docno,
                'docref'=>trim($this->request->getPost('woeno')),
                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'=>$row->idbarang,
                'nmbarang'=>$row->nmbarang,
                'unit'=>$row->unit,
                'qty'=>$row->qty,
                'description'=>$row->description,
                'issub'=> 'F',
                'harga' => $harga,
                'nilai' => $nilai,
                // 'spec'=>$row->spec,

                'inputby'=>$nama,
                'inputdate'=>date('Y-m-d H:i:s'),

                'uniqueid'=>hash('sha256',$rawUnique)

            ]);

            if (!$insert) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        }

        $total = $db->query("
            SELECT COALESCE(SUM(nilai),0) AS ttlprice
            FROM sc_tmp.penerimaanbp_dtl
            WHERE docno = ?
        ", [$docno])->getRow();

        $db->table('sc_tmp.penerimaanbp_mst')
            ->where('docno', $docno)
            ->update([
                'ttlprice'   => $total->ttlprice,
                'updateby'   => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }


    public function save_penerimaanbp_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        // if (!$doctype_detail) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Tipe Detail tidak ditemukan'
        //     ]);
        // }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.penerimaanbp_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'penerimaanbp',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'woeno'     => trim($this->request->getPost('woeno')),
                'wono'     => trim($this->request->getPost('wono')),
                'bomno'     => trim($this->request->getPost('bomno')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'nmbarang_jadi'     => trim($this->request->getPost('nmbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                // 'idlocation'    => trim($this->request->getPost('idlocation')),
                // 'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

        // $idbarang    = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $qty         = $this->request->getPost('qty');
        $harga         = $this->request->getPost('harga');
        $nilai         = $this->request->getPost('nilai');
        $issub = $this->request->getPost('issub') == 'T' ? 'T' : 'F';
        // $issub         = $this->request->getPost('issub');
        // $spec = $this->request->getPost('spec');
        $description = strtoupper(trim($this->request->getPost('description')));

        $idbarang     = null;
        // $nmbarang     = null;

        // $idbarangBom  = null;
        // $nmbarangBom  = null;

        if($issub == 'T'){

            $idbarang    = trim($this->request->getPost('idbarang'));
            // $nmbarang    = trim($this->request->getPost('nmbarang'));

        }
        else{

            $idbarang = trim($this->request->getPost('idbarang_bom'));
            // $nmbarangBom = trim($this->request->getPost('nmbarang'));

        }


        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.penerimaanbp_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            // ->where('doctype_detail', $doctype_detail)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('description', $description);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama dengan keterangan yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('idurut', $idurut)
                ->update([
                    // 'doctype_detail' => $doctype_detail,
                    'cabang'=>trim($this->request->getPost('cabang')),
                    'idbarang'       => $idbarang,
                    'nmbarang'       => $nmbarang,
                    // 'idbarang_bom'       => $idbarangBom,
                    // 'nmbarang_bom'       => $nmbarangBom,
                    'unit'           => $unit,
                    'docdate'        => trim($this->request->getPost('docdate')),
                    'qty'            => $qty,
                    'harga'            => $harga,
                    'nilai'            => $nilai,
                    'issub'            => $issub,
                    'description'    => $description,
                    'updateby'       => $nama,
                    'updatedate'     => date('Y-m-d H:i:s')
                ]);

            if (!$updateDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {

            $inputdate = date('Y-m-d H:i:s');

            $itemUnique = $idbarang;
            $rawUnique = $itemUnique . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $insertDetail = $builderDetail->insert([
                'docno'          => $docno,
                // 'doctype_detail' => $doctype_detail,
                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'       => $idbarang,
                'nmbarang'       => $nmbarang,
                // 'idbarang_bom'       => $idbarangBom,
                // 'nmbarang_bom'       => $nmbarangBom,
                'unit'           => $unit,
                'docdate'        => trim($this->request->getPost('docdate')),
                'qty'            => $qty,
                'issub'            => $issub,
                'harga'            => $harga,
                'nilai'            => $nilai,
                // 'standartcost'   => $standartcost,
                // 'totalcost'      => $totalcost,
                'description'    => $description,
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s'),
                'uniqueid'       => $uniqueid
            ]);

            if (!$insertDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }


        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clear_penerimaanbp_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_penerimaanbp_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.penerimaanbp_mst');
        $builder_dtl = $this->db->table('sc_tmp.penerimaanbp_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/penerimaanbp'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/penerimaanbp'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/penerimaanbp'));
        }

    }


    //MATERIAL
    function list_tmp_penerimaanbp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_penerimaanbp_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_penerimaanbp_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_penerimaanbp_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_penerimaanbp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_penerimaanbp_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_penerimaanbp_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_penerimaanbp_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_penerimaanbp(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_penerimaanbp_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_penerimaanbp_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_penerimaanbp_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.penerimaanbp_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.9');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.9',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_penerimaanbp'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $batchno = strtoupper(trim($this->request->getPost('batchno')));
            // $idlocation = strtoupper(trim($this->request->getPost('idlocation')));
            // $nmlocation = strtoupper(trim($this->request->getPost('nmlocation')));
            $bagian = strtoupper(trim($this->request->getPost('bagian')));
            $tabno = strtoupper(trim($this->request->getPost('tabno')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'keterangan'       => $keterangan,
                'batchno'           => $batchno,
                'bagian'           => $bagian,
                // 'idlocation'           => $idlocation,
                // 'nmlocation'           => $nmlocation,
                'tabno'           => $tabno,

//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.9'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/penerimaanbp'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.9',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/penerimaanbp'));
            }



        }

    }

    public function get_penerimaanbp_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_penerimaanbp_dtl(" and idurut='$id'");

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,   
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        ]);
    }

    
    public function delete_penerimaanbp_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.penerimaanbp_dtl');
        $nama = trim(session()->get('nama'));

        // ambil ids (bisa array atau single)
        $ids = $request->getPost('ids');

        // normalisasi: pastikan array
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // Ambil docno dari salah satu detail yang akan dihapus
            $firstId = $ids[0];

            $row = $db->table('sc_tmp.penerimaanbp_dtl')
                ->select('docno')
                ->where('idurut', $firstId)
                ->get()
                ->getRow();

            if (!$row) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $docno = $row->docno;

            $builder
                ->whereIn('idurut', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            // $totals = $db->query("
            //     SELECT
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'MATERIAL' THEN totalcost ELSE 0 END),0) AS ttlmaterial,
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'COST' THEN totalcost ELSE 0 END),0) AS ttlcost,
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'WIP' THEN totalcost ELSE 0 END),0) AS ttlwip
            //     FROM sc_tmp.penerimaanbp_dtl
            //     WHERE docno = ?
            // ", [$docno])->getRowArray();

            // $ttlmaterial = (float)$totals['ttlmaterial'];
            // $ttlcost     = (float)$totals['ttlcost'];
            // $ttlwip      = (float)$totals['ttlwip'];
            // $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

            // $db->table('sc_tmp.penerimaanbp_mst')
            //     ->where('docno', $docno)
            //     ->update([
            //         'ttlmaterial' => $ttlmaterial,
            //         'ttlcost'     => $ttlcost,
            //         'ttlwip'      => $ttlwip,
            //         'ttlprice'    => $ttlprice,
            //         'updateby'    => $nama,
            //         'updatedate'  => date('Y-m-d H:i:s')
            //     ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data Penerimaan Barang Produksi Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_penerimaanbp_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_trx.penerimaanbp_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_trx.penerimaanbp_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_penerimaanbp')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/penerimaanbp')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detail_penerimaanbp_()
    {
        /* Penambahan Squence */
        $data['title']="Detail Penerimaan Barang Produksi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/penerimaanbp'));
        }
        $kodemenu='I.R.A.9'; $versirelease='I.R.A.9/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.9'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_trx_penerimaanbp_mst($param)->getRowArray();
        return $this->template->render('production/penerimaanbp/v_detail_penerimaanbp',$data);
    }

    public function cancel_penerimaanbp_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.penerimaanbp_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

                'status'     => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.penerimaanbp_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.penerimaanbp_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/penerimaanbp')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/penerimaanbp')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }












    

    /* BIAYA PRODUKSI NON MATERIAL  */

    public function biaya_produksi_non_material()
    {
        $data['title']="Biaya Produksi Non Material";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.10'; $versirelease='I.R.A.10/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.10'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_bpnm_mst($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('production/trans/clear_bpnm_Tmp');
            $urlnext = base_url('production/trans/add_bpnm');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.R.A.10';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/production/bpnm/v_bpnm',$data);
    }

    function list_bpnm_mst(){

        $list = $this->m_production->get_trx_bpnm_mst_view();

        $data = array();
        $no   = $_POST['start'];

        $kmenu = 'I.R.A.10';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $canUpdate =
            isset($datadtl['dtl_akses']['a_update']) &&
            trim($datadtl['dtl_akses']['a_update']) === 't';

        $canPrint =
            isset($datadtl['dtl_akses']['a_report']) &&
            trim($datadtl['dtl_akses']['a_report']) === 't';

        $canView =
            isset($datadtl['dtl_akses']['a_view']) &&
            trim($datadtl['dtl_akses']['a_view']) === 't';

        $canInput =
            isset($datadtl['dtl_akses']['a_input']) &&
            trim($datadtl['dtl_akses']['a_input']) === 't';

        $canDelete =
            isset($datadtl['dtl_akses']['a_delete']) &&
            trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {

            $no++;

            $row = array();

            $status   = strtoupper(trim($lm->status));
            $docno    = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate) {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_bpnm_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Update Biaya Produksi Non Material : ' . $docno . '\')">

                    <i class="fa fa-edit"></i> Update

                </a>';
            }

            // =====================================
            // DETAIL
            // =====================================
            if ($canView) {

                $detailBtn = '
                <a class="dropdown-item"
                   style="background-color:#3badf6;"
                   href="' . base_url('production/trans/detail_bpnm_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'View Detail Biaya Produksi Non Material : ' . $docno . '\')">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete) {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancelBiaya Produksi Non Material') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return confirm(\'Cancel Biaya Produksi Non Material : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            // =====================================
            // RULE STATUS
            // =====================================
            $menuContent = '';

            if ($status === 'P' || $status === 'C') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete) $menuContent .= $deleteBtn;
            }

            // =====================================
            // DROPDOWN
            // =====================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button class="btn btn-primary btn-sm dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                        <i class="fa fa-bars"></i>

                    </button>

                    <div class="dropdown-menu">
                        ' . $menuContent . '
                    </div>

                </div>';

            } else {

                $dropdownMenu = '';
            }

            // =====================================
            // STATUS BADGE
            // =====================================
            $badge = 'secondary';

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger';
                    break;

                case 'E':
                    $badge = 'primary';
                    break;

                case 'F':
                    $badge = 'success';
                    break;

                case 'P':
                    $badge = 'warning';
                    break;
            }

            $statusLabel =
                isset($lm->nmstatus)
                    ? $lm->nmstatus
                    : $status;

            $statusBadge =
                '<span class="badge bg-' . $badge . ' text-dark w-100"
                    style="font-size:14px;display:block;padding:6px 8px;">'

                . $statusLabel .

                '</span>';

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;
            
            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;
            
            
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            // $row[] = $lm->inputby;
            // $row[] = $lm->inputdate;

            // $row[] = $lm->updateby;
            // $row[] = $lm->updatedate;

            $data[] = $row;
        }

        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" =>
                $this->m_production
                    ->trx_bpnm_mst_view_count_all(),

            "recordsFiltered" =>
                $this->m_production
                    ->trx_bpnm_mst_view_count_filtered(),

            "data" => $data,
        );

        echo $this->fiky_encryption->jDatatable($output);
    }

    function add_bpnm(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.10'; $versirelease='I.R.A.10/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.R.A.10'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_production->q_tmp_bpnm_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_bpnm_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/bpnm/v_add_bpnm',$data);
    }

    function showing_tmp_bpnm_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_tmp_bpnm_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_trx_bpnm_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_production->q_trx_bpnm_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getNextSuffix_bpnm_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.bpnm_mst')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }


    public function save_bpnm_from_wo()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        $docdate = trim($this->request->getPost('docdate'));
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        if (!$docdate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal Dokumen belum diisi'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.bpnm_mst');
        $builderDetail = $db->table('sc_tmp.bpnm_dtl');
        $oldWoeno  = trim($this->request->getPost('old_woeno'));
        $woeno = trim($this->request->getPost('woeno'));
        $wono  = trim($this->request->getPost('wono'));

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'bpnm',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'woeno'     => trim($this->request->getPost('woeno')),
                'wono'     => trim($this->request->getPost('wono')),
                'bomno'     => trim($this->request->getPost('bomno')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'nmbarang_jadi'     => trim($this->request->getPost('nmbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                // 'idlocation'    => trim($this->request->getPost('idlocation')),
                // 'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        } else {

            $updateHeader = $builderHeader
                ->where('docno',$docno)
                ->where('inputby',$nama)
                ->update([

                    'woeno'        => trim($this->request->getPost('woeno')),
                    'wono'         => trim($this->request->getPost('wono')),
                    'bomno'        => trim($this->request->getPost('bomno')),
                    'idbarang_jadi'=> trim($this->request->getPost('idbarang_jadi')),
                    'nmbarang_jadi'=> trim($this->request->getPost('nmbarang_jadi')),
                    'buildfor'     => trim($this->request->getPost('buildfor')),
                    'buildunit'    => trim($this->request->getPost('buildunit')),
                    'batchno'      => trim($this->request->getPost('batchno')),
                    'bagian'       => trim($this->request->getPost('bagian')),
                    // 'idlocation'   => trim($this->request->getPost('idlocation')),
                    // 'nmlocation'   => trim($this->request->getPost('nmlocation')),
                    'keterangan'   => strtoupper(trim($this->request->getPost('keterangan'))),
                    'updateby'     => $nama,
                    'updatedate'   => date('Y-m-d H:i:s')
                ]);

            if (!$updateHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }

        
        // =========================
        // HAPUS DETAIL GENERATE WOE LAMA
        // =========================
        
        if (!empty($oldWoeno)) {
            $delete = $builderDetail
                ->where('docno', $docno)
                ->where('docref', $oldWoeno)
                ->delete();

            if (!$delete) {

                $error = $db->error();

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }
        
        // =========================
        // AMBIL DATA DETAIL
        // =========================

        $wono = trim($this->request->getPost('wono'));

        // $list = $db->query("
        //     SELECT *
        //     FROM sc_trx.workingorder_bom_dtl
        //     WHERE trim(docref)=trim(?)
        // ",[$wono])->getResult();

        $list = $db->query("
            SELECT
                w.*,
                COALESCE(sc.newcost,0) AS harga
            FROM sc_trx.workingorder_bom_dtl w

            LEFT JOIN (
                SELECT
                    a.idbarang,
                    a.newcost,
                    ROW_NUMBER() OVER(
                        PARTITION BY a.idbarang
                        ORDER BY a.activedate DESC
                    ) rn
                FROM sc_mst.standart_cost_dtl a
                WHERE a.status='F'
                AND a.activedate <= ?
            ) sc
                ON TRIM(sc.idbarang)=TRIM(w.idbarang)
                AND sc.rn=1

            WHERE TRIM(w.docref)=TRIM(?) and TRIM(w.doctype_detail) = 'COST'
        ", [
            $docdate,
            $wono
        ])->getResult();

        if(empty($list)) {
            $db->transRollback();

            return $this->response->setJSON([
                'success'=>false,
                'message'=>'Detail WO tidak ditemukan.'
            ]);
        }

        foreach($list as $row){

            $rawUnique = $row->idbarang.'|'.$docno.'|'.microtime(true);
            $harga = $row->harga;
            $qty   = $row->qty;
            $nilai = $qty * $harga;

            $insert = $builderDetail->insert([

                'docno'=>$docno,
                'docref'=>trim($this->request->getPost('woeno')),
                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'=>$row->idbarang,
                'nmbarang'=>$row->nmbarang,
                'unit'=>$row->unit,
                'qty'=>$row->qty,
                'description'=>$row->description,
                'issub'=> 'F',
                'harga' => $harga,
                'nilai' => $nilai,
                // 'spec'=>$row->spec,

                'inputby'=>$nama,
                'inputdate'=>date('Y-m-d H:i:s'),

                'uniqueid'=>hash('sha256',$rawUnique)

            ]);

            if (!$insert) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        }

        $total = $db->query("
            SELECT COALESCE(SUM(nilai),0) AS ttlprice
            FROM sc_tmp.bpnm_dtl
            WHERE docno = ?
        ", [$docno])->getRow();

        $db->table('sc_tmp.bpnm_mst')
            ->where('docno', $docno)
            ->update([
                'ttlprice'   => $total->ttlprice,
                'updateby'   => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }


    public function save_bpnm_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        // $doctype_detail = strtoupper(trim($this->request->getPost('doctype_detail')));
        
        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }


        // if (!$doctype_detail) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Tipe Detail tidak ditemukan'
        //     ]);
        // }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.bpnm_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $insertHeader = $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'bpnm',
                'cabang'     => trim($this->request->getPost('cabang')),
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'woeno'     => trim($this->request->getPost('woeno')),
                'wono'     => trim($this->request->getPost('wono')),
                'bomno'     => trim($this->request->getPost('bomno')),
                'idbarang_jadi'     => trim($this->request->getPost('idbarang_jadi')),
                'nmbarang_jadi'     => trim($this->request->getPost('nmbarang_jadi')),
                'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                'buildunit'    => trim($this->request->getPost('buildunit')),
                // 'idlocation'    => trim($this->request->getPost('idlocation')),
                // 'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            if (!$insertHeader) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

        // $idbarang    = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $qty         = $this->request->getPost('qty');
        $harga         = $this->request->getPost('harga');
        $nilai         = $this->request->getPost('nilai');
        $issub = $this->request->getPost('issub') == 'T' ? 'T' : 'F';
        // $issub         = $this->request->getPost('issub');
        // $spec = $this->request->getPost('spec');
        $description = strtoupper(trim($this->request->getPost('description')));

        $idbarang     = null;
        // $nmbarang     = null;

        // $idbarangBom  = null;
        // $nmbarangBom  = null;

        if($issub == 'T'){

            $idbarang    = trim($this->request->getPost('idbarang'));
            // $nmbarang    = trim($this->request->getPost('nmbarang'));

        }
        else{

            $idbarang = trim($this->request->getPost('idbarang_bom'));
            // $nmbarangBom = trim($this->request->getPost('nmbarang'));

        }


        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.bpnm_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            // ->where('doctype_detail', $doctype_detail)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('description', $description);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama dengan keterangan yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('idurut', $idurut)
                ->update([
                    // 'doctype_detail' => $doctype_detail,
                    'cabang'=>trim($this->request->getPost('cabang')),
                    'idbarang'       => $idbarang,
                    'nmbarang'       => $nmbarang,
                    // 'idbarang_bom'       => $idbarangBom,
                    // 'nmbarang_bom'       => $nmbarangBom,
                    'unit'           => $unit,
                    'docdate'        => trim($this->request->getPost('docdate')),
                    'qty'            => $qty,
                    'harga'            => $harga,
                    'nilai'            => $nilai,
                    'issub'            => $issub,
                    'description'    => $description,
                    'updateby'       => $nama,
                    'updatedate'     => date('Y-m-d H:i:s')
                ]);

            if (!$updateDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {

            $inputdate = date('Y-m-d H:i:s');

            $itemUnique = $idbarang;
            $rawUnique = $itemUnique . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $insertDetail = $builderDetail->insert([
                'docno'          => $docno,
                // 'doctype_detail' => $doctype_detail,
                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'       => $idbarang,
                'nmbarang'       => $nmbarang,
                // 'idbarang_bom'       => $idbarangBom,
                // 'nmbarang_bom'       => $nmbarangBom,
                'unit'           => $unit,
                'docdate'        => trim($this->request->getPost('docdate')),
                'qty'            => $qty,
                'issub'            => $issub,
                'harga'            => $harga,
                'nilai'            => $nilai,
                // 'standartcost'   => $standartcost,
                // 'totalcost'      => $totalcost,
                'description'    => $description,
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s'),
                'uniqueid'       => $uniqueid
            ]);

            if (!$insertDetail) {
                $error = $db->error();
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }
        }

        $total = $db->query("
            SELECT COALESCE(SUM(nilai),0) AS ttlprice
            FROM sc_tmp.bpnm_dtl
            WHERE docno = ?
        ", [$docno])->getRow();

        $db->table('sc_tmp.bpnm_mst')
            ->where('docno', $docno)
            ->update([
                'ttlprice'   => $total->ttlprice,
                'updateby'   => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction gagal'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }



    function clear_bpnm_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_bpnm_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('production/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.bpnm_mst');
        $builder_dtl = $this->db->table('sc_tmp.bpnm_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();

            return redirect()->to(base_url('production/trans/biaya_produksi_non_material'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {

                $builder->where('inputby',$nama);
                $builder->delete();
                $builder_dtl->where('inputby',$nama);
                $builder_dtl->delete();

                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('production/trans/biaya_produksi_non_material'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('production/trans/biaya_produksi_non_material'));
        }

    }


    //MATERIAL
    function list_tmp_bpnm_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_bpnm_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->tmp_bpnm_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_bpnm_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_bpnm_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_trx_bpnm_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_production->trx_bpnm_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->trx_bpnm_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_input_bpnm(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_production->q_tmp_bpnm_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_production->q_tmp_bpnm_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_bpnm_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.bpnm_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.R.A.10');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.R.A.10',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_bpnm'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $batchno = strtoupper(trim($this->request->getPost('batchno')));
            // $idlocation = strtoupper(trim($this->request->getPost('idlocation')));
            // $nmlocation = strtoupper(trim($this->request->getPost('nmlocation')));
            $bagian = strtoupper(trim($this->request->getPost('bagian')));
            $tabno = strtoupper(trim($this->request->getPost('tabno')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'keterangan'       => $keterangan,
                'batchno'           => $batchno,
                'bagian'           => $bagian,
                // 'idlocation'           => $idlocation,
                // 'nmlocation'           => $nmlocation,
                'tabno'           => $tabno,

//                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.R.A.10'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/production/trans/biaya_produksi_non_material'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.R.A.10',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/production/trans/biaya_produksi_non_material'));
            }



        }

    }

    public function get_bpnm_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_bpnm_dtl(" and idurut='$id'");

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,   
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        ]);
    }

    
    public function delete_bpnm_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.bpnm_dtl');
        $nama = trim(session()->get('nama'));

        // ambil ids (bisa array atau single)
        $ids = $request->getPost('ids');

        // normalisasi: pastikan array
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // Ambil docno dari salah satu detail yang akan dihapus
            $firstId = $ids[0];

            $row = $db->table('sc_tmp.bpnm_dtl')
                ->select('docno')
                ->where('idurut', $firstId)
                ->get()
                ->getRow();

            if (!$row) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $docno = $row->docno;

            $builder
                ->whereIn('idurut', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            // $totals = $db->query("
            //     SELECT
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'MATERIAL' THEN totalcost ELSE 0 END),0) AS ttlmaterial,
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'COST' THEN totalcost ELSE 0 END),0) AS ttlcost,
            //         COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'WIP' THEN totalcost ELSE 0 END),0) AS ttlwip
            //     FROM sc_tmp.bpnm_dtl
            //     WHERE docno = ?
            // ", [$docno])->getRowArray();

            // $ttlmaterial = (float)$totals['ttlmaterial'];
            // $ttlcost     = (float)$totals['ttlcost'];
            // $ttlwip      = (float)$totals['ttlwip'];
            // $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

            // $db->table('sc_tmp.bpnm_mst')
            //     ->where('docno', $docno)
            //     ->update([
            //         'ttlmaterial' => $ttlmaterial,
            //         'ttlcost'     => $ttlcost,
            //         'ttlwip'      => $ttlwip,
            //         'ttlprice'    => $ttlprice,
            //         'updateby'    => $nama,
            //         'updatedate'  => date('Y-m-d H:i:s')
            //     ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data Biaya Produksi Non Material Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_bpnm_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_trx.bpnm_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {

            $info = [

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_trx.bpnm_mst')
                ->where('docno', trim($docno))
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            if ($update) {

                return redirect()->to(
                    base_url('production/trans/add_bpnm')
                )->with(
                    'success',
                    'Document berhasil dibuka untuk edit'
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'error',
                'Gagal update document'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/biaya_produksi_non_material')
        )->with(
            'warning',
            'Document sedang diproses user lain'
        );
    }

    function detail_bpnm_()
    {
        /* Penambahan Squence */
        $data['title']="Detail Biaya Produksi Non Material";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('production/trans/biaya_produksi_non_material'));
        }
        $kodemenu='I.R.A.10'; $versirelease='I.R.A.10/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.R.A.10'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_trx_bpnm_mst($param)->getRowArray();
        return $this->template->render('production/bpnm/v_detail_bpnm',$data);
    }

    public function cancel_bpnm_()
    {
        $nama = trim(session()->get('nama'));

        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        $docno = hex2bin($id);

        // =====================================
        // GET DATA
        // =====================================
        $dtl = $this->db
            ->table('sc_mst.bpnm_mst')
            ->where('docno', trim($docno))
            ->get()
            ->getRowArray();

        // =====================================
        // VALIDASI DATA
        // =====================================
        if (!$dtl) {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'error',
                'Document tidak ditemukan'
            );
        }

        // =====================================
        // STATUS
        // =====================================
        $status = strtoupper(trim($dtl['status']));

        // =====================================
        // VALIDASI STATUS
        // =====================================
        if ($status === 'F' || $status === 'P') {
            $info = [

                'status'     => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

// =====================================
// START TRANSACTION
// =====================================
            $this->db->transStart();

// =====================================
// UPDATE MASTER
// =====================================
            $this->db
                ->table('sc_mst.bpnm_mst')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// UPDATE DETAIL
// =====================================
            $this->db
                ->table('sc_mst.bpnm_dtl')
                ->where('docno', trim($docno))
                ->update($info);

// =====================================
// COMMIT
// =====================================
            $this->db->transComplete();

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/biaya_produksi_non_material')
                )->with(
                    'error',
                    'Gagal cancel document'
                );
            }

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'success',
                'Document berhasil dicancel'
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/biaya_produksi_non_material')
        )->with(
            'warning',
            'Document tidak bisa dicancel'
        );
    }

}