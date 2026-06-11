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


    /* BOM Build Of Material  */

    public function bom()
    {
        $data['title']="Build Off Material";
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
        $dtl = $this->m_production->q_tmp_biaya_standart_mst($param);
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

        $list = $this->m_production->get_mst_standart_cost_mst_view();

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
        $data = $this->m_production->q_mst_bom_mst($param);
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

        $row = $this->db->table('sc_mst.bom_mst')
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

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
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

        $builderDetail = $db->table('sc_tmp.bom_dtl');

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
                    'doctype'    => 'bom',
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
                'doctype'    => 'bom',
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



    function list_tmp_bom_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_tmp_bom_dtl_view($docno);
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
            "recordsTotal" => $this->m_production->tmp_bom_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->tmp_bom_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_mst_bom_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_production->get_mst_bom_dtl_view($docno);
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
            "recordsTotal" => $this->m_production->mst_bom_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_production->mst_bom_dtl_view_count_filtered($docno),
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

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
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

                'status'     => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama,

            ];

            // =====================================
            // UPDATE
            // =====================================
            $update = $this->db
                ->table('sc_mst.bom_mst')
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
        $data['title']="Detail Biaya Standart";
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
        $data['dtldata'] = $this->m_production->q_mst_bom_mst($param)->getRowArray();
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




}