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
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
            if ($canUpdate  && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/updateStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Standard Cost : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detailStandartCost') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

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
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Standard Cost : ' . $docno . '\')">

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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                case 'D':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-primary';
                    break;

                case 'F':
                    $badgeClass = 'badge-success';
                    break;

                case 'P':
                    $badgeClass = 'badge-warning';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->cabang;
            $row[] = $lm->pemohon;

            $row[] = $lm->docdate1;
            $row[] = $lm->activedate1;

            $row[] = $lm->docref;

            $row[] = $lm->description;


            $row[] = $lm->inputby;
            $row[] = $lm->inputdate1;
            $row[] = $lm->updatedate1;

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/standart_cost'));
        }
        // =================================

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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby = '$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_mst_standart_cost_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_standart_cost()
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['scp']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
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


    // =========================================================
    // tanggal form (dd-mm-yyyy) -> tanggal database (Y-m-d)
    // =========================================================
    private function stdCostDateToDb($val)
    {
        $val = trim((string) $val);
        if ($val === '') {
            return null;
        }

        $dt = \DateTime::createFromFormat('d-m-Y', $val);
        if ($dt && $dt->format('d-m-Y') === $val) {
            return $dt->format('Y-m-d');
        }

        $ts = strtotime($val); // sudah ISO (Y-m-d) / format lain yang dimengerti
        return $ts ? date('Y-m-d', $ts) : $val;
    }

    public function save_standart_cost_mst()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        $cabang = strtoupper(trim($this->request->getPost('cabang')));

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
                'docdate'    => $this->stdCostDateToDb($this->request->getPost('docdate')),
                'activedate' => $this->stdCostDateToDb($this->request->getPost('activedate')),
                'cabang'     => $cabang,
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

        } else {

            // header sudah ada: sinkronkan cabang / tanggal / ref dari form
            $builderHeader
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->update([
                    'cabang'     => $cabang,
                    'docref'     => trim($this->request->getPost('docref')),
                    'docdate'    => $this->stdCostDateToDb($this->request->getPost('docdate')),
                    'activedate' => $this->stdCostDateToDb($this->request->getPost('activedate')),
                    'pemohon'    => trim($this->request->getPost('pemohon')),
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s'),
                ]);
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================
        $uniqueid = $this->request->getPost('uniqueid');

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
            ->where('inputby', $nama)
            // ->where('description', $description_detail)
        ;

        if ($uniqueid) {
            $builderDuplicate->where('uniqueid !=', $uniqueid);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)
                ->update([
                    'doctype'    => 'STDCOST',
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'unit'        => $unit,
                    'docdate'    => $this->stdCostDateToDb($this->request->getPost('docdate')),
                    'activedate' => $this->stdCostDateToDb($this->request->getPost('activedate')),
                    'cabang'     => $cabang,
                    'newcost'        => $newcost,
                    'actualcost'        => $actualcost,
                    'lastcost'        => $lastcost,
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
                'docdate'    => $this->stdCostDateToDb($this->request->getPost('docdate')),
                'activedate' => $this->stdCostDateToDb($this->request->getPost('activedate')),
                'cabang'     => $cabang,
                //'batch'         => $batch,
                //'qty'         => $qty,
                'newcost'        => $newcost,
                'actualcost'        => $actualcost,
                'lastcost'        => $lastcost,
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
        $nama = trim($this->session->get('nama'));

        $param = " AND COALESCE(inputby,'') = '$nama'";

        $dtl = $this->m_production->q_tmp_standart_cost_mst($param);

        // =====================================================
        // JIKA TIDAK ADA DATA / ROW LANGSUNG KEMBALI KE LIST
        // =====================================================
        if (!$dtl || $dtl->getNumRows() == 0) {
            return redirect()->to(base_url('production/trans/standart_cost'));
        }

        $row = $dtl->getRowArray();

        // JIKA DOCNO ATAU DATA TIDAK ADA
        // ... existing code ...

        if (!empty(trim($_POST['f_docno'] ?? ''))) {
            $builder->like('trim(upper(docno))', strtoupper(trim($_POST['f_docno'])));
        }

        if (!empty(trim($_POST['f_cabang'] ?? ''))) {
            $builder->like('trim(upper(cabang))', strtoupper(trim($_POST['f_cabang'])));
        }

        if (!empty(trim($_POST['f_pemohon'] ?? ''))) {
            $builder->like('trim(upper(pemohon))', strtoupper(trim($_POST['f_pemohon'])));
        }

        // ... existing code ...

        $status = trim($row['status'] ?? '');

        $builder     = $this->db->table('sc_tmp.standart_cost_mst');
        $builder_dtl = $this->db->table('sc_tmp.standart_cost_dtl');

        if ($status === 'I') {

            $builder
                ->where('inputby', $nama)
                ->delete();

            $builder_dtl
                ->where('inputby', $nama)
                ->delete();

            return redirect()->to(base_url('production/trans/standart_cost'));

        } elseif ($status === 'E') {

            $builder->where('inputby', $nama);

            if ($builder->update([
                'status' => 'C'
            ])) {

                $builder_dtl
                    ->where('inputby', $nama)
                    ->delete();

                return redirect()->to(base_url('production/trans/standart_cost'));

            } else {

                return redirect()->to(base_url('production/trans/standart_cost'));

            }

        }

        return redirect()->to(base_url('production/trans/standart_cost'));
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
            $row[] = $lm->uniqueid;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 4, '.', ',') . '</div>';
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

    /* =========================================================
     * Grid detail Standart Cost (JSON, tanpa protokol DataTables)
     * Dipakai oleh inline grid ala pmk_brng (assets/pagejs/production/
     * standart_cost/standart_cost_grid.js).
     * Urutan: idurut ASC (baris lama di atas, baris baru di bawah).
     * ========================================================= */
    public function list_standart_cost_dtl_grid()
    {
        $nama  = trim((string) $this->session->get('nama'));
        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama)
               . " order by idurut asc";

        $rows  = $this->m_production->q_tmp_standart_cost_dtl($param)->getResultArray();
        $items = array();

        foreach ($rows as $r) {
            $items[] = array(
                'uniqueid'    => trim((string) ($r['uniqueid'] ?? '')),
                'idurut'      => trim((string) ($r['idurut'] ?? '')),
                'idbarang'    => trim((string) ($r['idbarang'] ?? '')),
                'nmbarang'    => trim((string) ($r['nmbarang'] ?? '')),
                'unit'        => trim((string) ($r['unit'] ?? '')),
                'actualcost'  => (float) ($r['actualcost'] ?? 0),
                'lastcost'    => (float) ($r['lastcost'] ?? 0),
                'newcost'     => (float) ($r['newcost'] ?? 0),
                'description' => trim((string) ($r['description'] ?? '')),
            );
        }

        return $this->response->setJSON(array(
            'status' => true,
            'total'  => count($items),
            'items'  => $items,
        ));
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
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 4, '.', ',') . '</div>';
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


        $builder = $this->db->table('sc_tmp.standart_cost_mst');

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

            return redirect()->to(base_url('/production/trans/standart_cost'));
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

        $data = $this->m_production->q_tmp_standart_cost_dtl(" and uniqueid='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function delete_standart_cost_dtl()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $nama    = trim(session()->get('nama'));

        // ambil ids (bisa array atau single) — ini uniqueid/idurut detail
        $ids = $request->getPost('ids');

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

            // =========================
            // 1. AMBIL DOCNO DARI DETAIL YANG AKAN DIHAPUS
            // =========================
            $detailRows = $db->table('sc_tmp.standart_cost_dtl')
                ->select('docno, uniqueid')
                ->whereIn('uniqueid', $ids)
                ->where('inputby', $nama)
                ->get()
                ->getResult();

            if (empty($detailRows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            // kumpulkan docno unik
            $docnoList = array_unique(array_column($detailRows, 'docno'));

            // =========================
            // 2. HAPUS DETAIL YANG DIPILIH SAJA
            // =========================
            $db->table('sc_tmp.standart_cost_dtl')
                ->whereIn('uniqueid', $ids)
                ->where('inputby', $nama)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Gagal menghapus detail'
                ]);
            }

            // =========================
            // 3. CEK APAKAH MASIH ADA DETAIL LAIN DI DOCNO TERSEBUT
            // =========================
            foreach ($docnoList as $docno) {

                $sisa = $db->table('sc_tmp.standart_cost_dtl')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->countAllResults();

                // kalau tidak ada sisa detail → hapus header
                if ($sisa == 0) {
                    $db->table('sc_tmp.standart_cost_mst')
                        ->where('docno', $docno)
                        ->where('inputby', $nama)
                        ->delete();
                }
            }

            $db->transCommit();
            // setelah transCommit, sebelum return
            $sisaDetail = $db->table('sc_tmp.standart_cost_dtl')
                ->where('inputby', $nama)
                ->countAllResults();

            return $this->response->setJSON([
                'status'      => true,
                'message'     => 'Data berhasil dihapus',
                'sisa_detail' => $sisaDetail
            ]);


        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateStandartCost()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/standart_cost'));
        }
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
        $kodemenu='I.R.A.1'; $versirelease='I.R.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_mst_standart_cost_mst($param)->getRowArray();
        return $this->template->render('production/standart_cost/v_detail_standart_cost',$data);
    }

    public function cancelStandartCost()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/standart_cost'));
        }
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
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/standart_cost')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat dicancel.')
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

            $this->db->transStart();

            try {
                $this->db
                    ->table('sc_mst.standart_cost_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/standart_cost'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
                }
                if ($this->prod_hitungStatus('sc_mst.standart_cost_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/standart_cost'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.'));
                }

                $this->db
                    ->table('sc_mst.standart_cost_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return redirect()->to(base_url('production/trans/standart_cost'))->with('prod_msg', $this->prod_msg('error', $this->prod_pesan_db($e)));
            }

            if ($this->db->transStatus() === false) {
                return redirect()->to(base_url('production/trans/standart_cost'))->with('prod_msg', $this->prod_msg('error', 'Gagal cancel document.'));
            }

            return redirect()->to(base_url('production/trans/standart_cost'))->with('prod_msg', $this->prod_msg('success', 'Standart Cost ' . trim($docno) . ' berhasil dicancel.'));
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
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
            if ($canUpdate && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/updateBiayaStandart') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Biaya Standart Produksi : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detailBiayaStandart') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

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
                   href="' . base_url('production/trans/cancelBiayaStandart') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Biaya Standart Produksi : ' . $docno . '\')">

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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-warning';
                    break;

                case 'F':
                    $badgeClass = 'badge-info';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = $lm->cabang;
            $row[] = $lm->pemohon;

            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->activedate;

            $row[] = $lm->docref;

            $row[] = $lm->description;


            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/biaya_standart'));
        }
        // =================================s

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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_mst_biaya_standart_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_biaya_standart()
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['bs']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
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
        $cabang = strtoupper(trim($this->request->getPost('cabang')));

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
                'cabang'     => $cabang,
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
        $uniqueid = $this->request->getPost('uniqueid');
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
            ->where('inputby', $nama)
            ->where('description', $description_detail);

        if ($uniqueid) {
            $builderDuplicate->where('uniqueid !=', $uniqueid);
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
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)
                ->update([
                    'doctype'    => 'BIAYA_STANDART',
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'unit'        => $unit,
                    'docdate'    => trim($this->request->getPost('docdate')),
                    'activedate'     => trim($this->request->getPost('activedate')),
                    'newcost'        => $newcost,
                    'actualcost'        => $actualcost,
                    'lastcost'        => $lastcost,
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
                'actualcost'        => $actualcost,
                'lastcost'        => $lastcost,
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/biaya_standart'));
        }
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
            $row[] = $lm->uniqueid;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 4, '.', ',') . '</div>';
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

    /* =========================================================
     * Grid detail Biaya Standart (JSON, tanpa protokol DataTables)
     * Dipakai oleh inline grid ala pmk_brng
     * (assets/pagejs/production/biaya_standart/biaya_standart_grid.js).
     * Urutan: idurut ASC (baris lama di atas, baris baru di bawah).
     * ========================================================= */
    public function list_biaya_standart_dtl_grid()
    {
        $nama  = trim((string) $this->session->get('nama'));
        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama)
               . " order by idurut asc";

        $rows  = $this->m_production->q_tmp_biaya_standart_dtl($param)->getResultArray();
        $items = array();

        foreach ($rows as $r) {
            $items[] = array(
                'uniqueid'    => trim((string) ($r['uniqueid'] ?? '')),
                'idurut'      => trim((string) ($r['idurut'] ?? '')),
                'idbarang'    => trim((string) ($r['idbarang'] ?? '')),
                'nmbarang'    => trim((string) ($r['nmbarang'] ?? '')),
                'unit'        => trim((string) ($r['unit'] ?? '')),
                'actualcost'  => (float) ($r['actualcost'] ?? 0),
                'lastcost'    => (float) ($r['lastcost'] ?? 0),
                'newcost'     => (float) ($r['newcost'] ?? 0),
                'description' => trim((string) ($r['description'] ?? '')),
            );
        }

        return $this->response->setJSON(array(
            'status' => true,
            'total'  => count($items),
            'items'  => $items,
        ));
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
            $row[] = '<div class="ratakanan">'. number_format($lm->actualcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->lastcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->newcost, 4, '.', ',') . '</div>';
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


        $builder = $this->db->table('sc_tmp.biaya_standart_mst');

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

            return redirect()->to(base_url('/production/trans/biaya_standart'));
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

        $data = $this->m_production->q_tmp_biaya_standart_dtl(" and uniqueid='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    public function delete_biaya_standart_dtl()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $nama    = trim(session()->get('nama'));

        // ambil ids (bisa array atau single) — ini uniqueid/idurut detail
        $ids = $request->getPost('ids');

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

            // =========================
            // 1. AMBIL DOCNO DARI DETAIL YANG AKAN DIHAPUS
            // =========================
            $detailRows = $db->table('sc_tmp.biaya_standart_dtl')
                ->select('docno, uniqueid')
                ->whereIn('uniqueid', $ids)
                ->where('inputby', $nama)
                ->get()
                ->getResult();

            if (empty($detailRows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            // kumpulkan docno unik
            $docnoList = array_unique(array_column($detailRows, 'docno'));

            // =========================
            // 2. HAPUS DETAIL YANG DIPILIH SAJA
            // =========================
            $db->table('sc_tmp.biaya_standart_dtl')
                ->whereIn('uniqueid', $ids)
                ->where('inputby', $nama)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Gagal menghapus detail'
                ]);
            }

            // =========================
            // 3. CEK APAKAH MASIH ADA DETAIL LAIN DI DOCNO TERSEBUT
            // =========================
            foreach ($docnoList as $docno) {

                $sisa = $db->table('sc_tmp.biaya_standart_dtl')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->countAllResults();

                // kalau tidak ada sisa detail → hapus header
                if ($sisa == 0) {
                    $db->table('sc_tmp.biaya_standart_mst')
                        ->where('docno', $docno)
                        ->where('inputby', $nama)
                        ->delete();
                }
            }

            $db->transCommit();
            // setelah transCommit, sebelum return
            $sisaDetail = $db->table('sc_tmp.biaya_standart_dtl')
                ->where('inputby', $nama)
                ->countAllResults();

            return $this->response->setJSON([
                'status'      => true,
                'message'     => 'Data berhasil dihapus',
                'sisa_detail' => $sisaDetail
            ]);


        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateBiayaStandart()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/biaya_standart'));
        }
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
        $kodemenu='I.R.A.2'; $versirelease='I.R.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_mst_biaya_standart_mst($param)->getRowArray();
        return $this->template->render('production/biaya_standart/v_detail_biaya_standart',$data);
    }

    public function cancelBiayaStandart()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/biaya_standart'));
        }
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
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/biaya_standart')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat dicancel.')
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

            $this->db->transStart();

            try {
                $this->db
                    ->table('sc_mst.biaya_standart_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/biaya_standart'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
                }
                if ($this->prod_hitungStatus('sc_mst.biaya_standart_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/biaya_standart'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.'));
                }

                $this->db
                    ->table('sc_mst.biaya_standart_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return redirect()->to(base_url('production/trans/biaya_standart'))->with('prod_msg', $this->prod_msg('error', $this->prod_pesan_db($e)));
            }

            if ($this->db->transStatus() === false) {
                return redirect()->to(base_url('production/trans/biaya_standart'))->with('prod_msg', $this->prod_msg('error', 'Gagal cancel document.'));
            }

            return redirect()->to(base_url('production/trans/biaya_standart'))->with('prod_msg', $this->prod_msg('success', 'Biaya Standart ' . trim($docno) . ' berhasil dicancel.'));
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
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
        // Ringkasan kartu dashboard (total / released / draft / komponen)
        $data['summary'] = $this->m_production->q_summary_bom();


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

        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';


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
            $disapproveBtn = '';
            $approveBtn = '';


            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_bom_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Bill of Material : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detail_bom_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete  && trim($lm->inputby) == $nama && $status=='F') {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancel_bom_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Bill of Material : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            if (trim($status) == 'A' && $canApprove) {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToUnreleased(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Unrelease</a>';
            }

            if (trim($status) == 'F' && $canApprove) {
                $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToReleased(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-check-circle"></i> Release</a>';
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
                if ($canApprove)   $menuContent .= $disapproveBtn;
                if ($canApprove)   $menuContent .= $approveBtn;

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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-warning';
                    break;

                case 'F':
                    $badgeClass = 'badge-info';
                    break;

                case 'A':
                    $badgeClass = 'badge-success';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;

            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;


            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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

    public function updateStatusBOM()
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

        // =====================================================
        // VALIDASI APPROVE (A): BOM harus punya detail/komponen
        // =====================================================
        if ($status === 'A') {
            $jmlDtl = $db->table('sc_trx.bom_dtl')
                ->where('docno', $docno)
                ->countAllResults();

            if ($jmlDtl <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'BOM tidak dapat di-approve karena detail/komponen masih kosong.'
                ]);
            }
        }

        // =====================================================
        // VALIDASI UNRELEASE (F):
        // BOM tidak boleh di-unrelease bila masih dipakai/ditarik
        // oleh Working Order, baik draft (sc_tmp) maupun trx (sc_trx)
        // =====================================================
        if ($status === 'F') {

            // 1) Sudah ditarik WO (transaksi) — WO batal (C) dikecualikan
            $refTrx = $db->query("
                SELECT DISTINCT TRIM(b.docref) AS docref
                FROM sc_trx.workingorder_bom_mst b
                LEFT JOIN sc_trx.workingorder_mst w
                       ON TRIM(w.docno) = TRIM(b.docref)
                WHERE TRIM(b.docno) = ?
                  AND COALESCE(UPPER(TRIM(w.status)), '') <> 'C'
                ORDER BY 1
            ", [$docno])->getResultArray();

            if (!empty($refTrx)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'BOM sudah ditarik WO nomor '
                        . implode(', ', array_column($refTrx, 'docref'))
                        . '. BOM tidak dapat di-unrelease.'
                ]);
            }

            // 2) Sedang diproses tarik WO (draft user di sc_tmp)
            $refTmp = $db->query("
                SELECT TRIM(b.docref) AS docref, TRIM(b.inputby) AS inputby
                FROM sc_tmp.workingorder_bom_mst b
                WHERE TRIM(b.docno) = ?
                ORDER BY b.inputdate DESC NULLS LAST
                LIMIT 1
            ", [$docno])->getRowArray();

            if ($refTmp) {
                $message = 'BOM sedang diproses tarik WO oleh ' . trim($refTmp['inputby']);

                if (trim($refTmp['docref']) !== '') {
                    $message .= ' (WO ' . trim($refTmp['docref']) . ')';
                }

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message . '. BOM tidak dapat di-unrelease.'
                ]);
            }
        }

        $builder = $db->table('sc_trx.bom_mst');
        $builder->where('docno', $docno);
        $info = array('status' => $status);
        $update = $builder->update($info);

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            // case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // A / R / C (1 huruf)
                'I.R',             // kode module dari menuprg
                'I.R.A.3'          // kode menu untuk BOM
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }

    function add_bom(){

        $data['title']="Input Standart Cost";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/bom'));
        }
        // =================================s

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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_trx_bom_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_bom()
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['bom']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
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
            $uniqueid = $this->request->getPost('uniqueidMaterial');
            $idbarang    = strtoupper(trim($this->request->getPost('idbarangMaterial')));
            $nmbarang    = strtoupper(trim($this->request->getPost('nmbarangmaterial')));
            $unit        = strtoupper(trim($this->request->getPost('unitMaterial')));
            $qty         = $this->request->getPost('qtymaterial');
            $standartcost= $this->request->getPost('standartcostmaterial');
            $totalcost   = $this->request->getPost('totalcostmaterial');
            $description = strtoupper(trim($this->request->getPost('description_detail_material')));

        } elseif ($doctype_detail == 'COST') {
            $uniqueid = $this->request->getPost('uniqueidCost');
            $idbarang    = strtoupper(trim($this->request->getPost('idbarangCost')));
            $nmbarang    = strtoupper(trim($this->request->getPost('nmbarangcost')));
            $unit        = strtoupper(trim($this->request->getPost('unitCost')));
            $qty         = $this->request->getPost('qtycost');
            $standartcost= $this->request->getPost('standartcostcost');
            $totalcost   = $this->request->getPost('totalcostcost');
            $description = strtoupper(trim($this->request->getPost('description_detail_cost')));

        } elseif ($doctype_detail == 'WIP') {
            $uniqueid = $this->request->getPost('uniqueidWip');
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
            ->where('inputby', $nama)
            // ->where('description', $description)
        ;

        if ($uniqueid) {
            $builderDuplicate->where('uniqueid !=', $uniqueid);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak boleh menginputkan item yang sama'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)
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
                COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'COST'     THEN totalcost ELSE 0 END),0) AS ttlcost,
                COALESCE(SUM(CASE WHEN TRIM(doctype_detail) = 'WIP'      THEN totalcost ELSE 0 END),0) AS ttlwip
            FROM sc_tmp.bom_dtl
            WHERE docno = ? AND inputby = ?
        ", [$docno, $nama])->getRowArray();

        $ttlmaterial = (float)$totals['ttlmaterial'];
        $ttlcost     = (float)$totals['ttlcost'];
        $ttlwip      = (float)$totals['ttlwip'];
        $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

        $db->table('sc_tmp.bom_mst')
            ->where('docno', $docno)
            ->where('inputby', $nama)
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/bom'));
        }
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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

        $header = $this->m_production->q_tmp_bom_mst($param)->getRowArray();
        $status = trim($header['status']);
        $docno  = trim($header['docno']);
        $cek = $this->m_production->q_tmp_bom_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_bom_mst($paramdtl2);


        $builder = $this->db->table('sc_tmp.bom_mst');

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

            return redirect()->to(base_url('/production/trans/bom'));
        } else {
            // Ambil dari request POST
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $idbarang_jadi = strtoupper(trim($this->request->getPost('idbarang_jadi')));
            $buildfor_raw   = $this->request->getPost('buildfor');
            $minimumqty_raw = $this->request->getPost('minimumqty');

            // buang pemisah ribuan (koma atau titik tergantung format), lalu pastikan desimal pakai titik
            $buildfor   = str_replace(',', '', (string) $buildfor_raw);   // "23,687.7078" -> "23687.7078"
            $minimumqty = str_replace(',', '', (string) $minimumqty_raw);

            // validasi kalau bukan angka, fallback 0
            $buildfor   = is_numeric($buildfor)   ? $buildfor   : 0;
            $minimumqty = is_numeric($minimumqty) ? $minimumqty : 0;

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'keterangan' => $keterangan,
                'idbarang_jadi' => $idbarang_jadi,
                'buildfor' => $buildfor,
                'minimumqty' => $minimumqty
            ];

            $builder->where('TRIM(docno)', $docno)
                ->where('TRIM(inputby)', $nama)
                ->where('TRIM(status)', 'E')
                ->update($updateHeader);

            $info = array('status' => 'F', 'updatedate' => date('Y-m-d H:i:s'), 'updateby' => $nama);

            $this->db->table('sc_tmp.bom_mst')
                ->where('TRIM(docno)', $docno)
                ->where('TRIM(inputby)', $nama)
                ->where('TRIM(status)', 'E')
                ->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                return redirect()->to(base_url('/production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
            }

            if ($this->prod_hitungStatus('sc_trx.bom_mst', $docno, null, 'F') > 0) {
                return redirect()->to(base_url('/production/trans/bom'))->with('prod_msg', $this->prod_msg('success', 'BOM ' . $docno . ' berhasil di-final.'));
            }

            return redirect()->to(base_url('/production/trans/bom'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.'));
        }

    }

    /* =========================================================
     * Grid detail BOM (JSON) — dipakai 3 tab: MATERIAL / COST / WIP
     * Dipakai oleh inline grid ala pmk_brng
     * (assets/pagejs/production/bom/bom_grid.js).
     * Satu tabel sc_tmp.bom_dtl, dibedakan oleh doctype_detail.
     * ========================================================= */
    public function list_bom_dtl_grid()
    {
        $nama    = trim((string) $this->session->get('nama'));
        $doctype = strtoupper(trim((string) $this->request->getGet('doctype')));

        if (!in_array($doctype, ['MATERIAL', 'COST', 'WIP'], true)) {
            return $this->response->setJSON(array(
                'status'  => false,
                'message' => 'Tipe detail tidak valid',
                'total'   => 0,
                'items'   => array(),
            ));
        }

        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama)
               . " and upper(trim(coalesce(doctype_detail,''))) = " . $this->db->escape($doctype)
               . " order by idurut asc";

        $list  = $this->m_production->q_tmp_bom_dtl($param)->getResultArray();
        $items = array();

        foreach ($list as $lm) {
            $qty  = (float) ($lm['qty'] ?? 0);
            $cost = (float) ($lm['standartcost'] ?? 0);

            $items[] = array(
                'uniqueid'    => trim((string) ($lm['uniqueid'] ?? '')),
                'idurut'      => trim((string) ($lm['idurut'] ?? '')),
                'doctype'     => $doctype,
                'idbarang'    => trim((string) ($lm['idbarang'] ?? '')),
                'nmbarang'    => trim((string) ($lm['nmbarang'] ?? '')),
                'qty'         => $qty,
                'unit'        => trim((string) ($lm['unit'] ?? '')),
                'standartcost' => $cost,
                'totalcost'   => (float) ($lm['totalcost'] ?? ($qty * $cost)),
                'description' => trim((string) ($lm['description'] ?? '')),
            );
        }

        return $this->response->setJSON(array(
            'status' => true,
            'total'  => count($items),
            'items'  => $items,
        ));
    }

    public function get_bom_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_bom_dtl(" and idurut=" . $this->db->escape($id));

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
                WHERE docno = ? AND inputby = ?
            ", [$docno, $nama])->getRowArray();

            $ttlmaterial = (float)$totals['ttlmaterial'];
            $ttlcost     = (float)$totals['ttlcost'];
            $ttlwip      = (float)$totals['ttlwip'];
            $ttlprice    = $ttlmaterial + $ttlcost + $ttlwip;

            $db->table('sc_tmp.bom_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/bom'));
        }
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
            return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Document tidak ditemukan.'));
        }

        // VALIDASI PEMILIK DOKUMEN
        if (trim($dtl['inputby']) !== $nama) {
            return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diubah.'));
        }

        $status = strtoupper(trim($dtl['status']));

        if ($status === 'F' || $status === 'P') {

            $info = ['status' => 'E', 'updatedate' => date('Y-m-d H:i:s'), 'updateby' => $nama];

            // UPDATE (pengaman status : hanya F/P yang boleh dibuka edit)
            $this->db
                ->table('sc_trx.bom_mst')
                ->where('docno', trim($docno))
                ->whereIn('status', ['F', 'P'])
                ->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
            }

            if ($this->prod_hitungStatus('sc_trx.bom_mst', $docno, null, 'E') > 0) {
                return redirect()->to(base_url('production/trans/add_bom'))->with('prod_msg', $this->prod_msg('success', 'BOM ' . trim($docno) . ' berhasil dibuka untuk edit.'));
            }

            return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.'));
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
        $kodemenu='I.R.A.3'; $versirelease='I.R.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/bom'));
        }
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
            return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Document tidak ditemukan.'));
        }

        // VALIDASI PEMILIK DOKUMEN
        if (trim($dtl['inputby']) !== $nama) {
            return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat dicancel.'));
        }

        $status = strtoupper(trim($dtl['status']));

        if ($status === 'F' || $status === 'P') {
            $info = ['status' => 'C', 'updatedate' => date('Y-m-d H:i:s'), 'updateby' => $nama];

            $this->db->transStart();

            try {
                $this->db
                    ->table('sc_trx.bom_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
                }
                if ($this->prod_hitungStatus('sc_trx.bom_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.'));
                }

                $this->db
                    ->table('sc_trx.bom_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', $this->prod_pesan_db($e)));
            }

            if ($this->db->transStatus() === false) {
                return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('error', 'Gagal cancel document.'));
            }

            return redirect()->to(base_url('production/trans/bom'))->with('prod_msg', $this->prod_msg('success', 'BOM ' . trim($docno) . ' berhasil dicancel.'));
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
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
        // Ringkasan kartu dashboard (total / finish / unfinish / final)
        $data['summary'] = $this->m_production->q_summary_wo();


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

        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';


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
            $disapproveBtn = '';
            $approveBtn = '';


            // =====================================
            // UPDATE
            // =====================================
            if ($canUpdate  && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_workingorder_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Working Order : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detail_workingorder_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }

            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete  && trim($lm->inputby) == $nama && $status=='F') {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancel_workingorder_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Working Order : ' . $docno . '\')">

                    <i class="fa fa-trash"></i> Cancel

                </a>';
            }

            if (trim($status) == 'A' && $canApprove) {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToProgress(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Progress</a>';
            }

            if (trim($status) == 'F' && $canApprove) {
                $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToCompleted(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-check-circle"></i> Complete</a>';
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
                if ($canApprove)   $menuContent .= $disapproveBtn;
                if ($canApprove)   $menuContent .= $approveBtn;
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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-warning';
                    break;

                case 'F':
                    $badgeClass = 'badge-info';
                    break;
                case 'A':
                    $badgeClass = 'badge-success';
                    break;
                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->nmcustomer;

            $row[] = $lm->alamatcust;

            $row[] = $lm->nmkota;
            $row[] = $lm->noso;
            $row[] = $lm->docdatefinish;


            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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


    public function updateStatusWO()
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

        // =====================================================
        // VALIDASI APPROVE (A): WO harus sudah punya BOM detail
        // =====================================================
        if ($status === 'A') {
            $jmlDtl = $db->table('sc_trx.workingorder_bom_dtl')
                ->where('docref', $docno)
                ->countAllResults();

            if ($jmlDtl <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Working Order tidak dapat di-approve karena BOM detail masih kosong.'
                ]);
            }
        }

        $builder = $db->table('sc_trx.workingorder_mst');
        $builder->where('docno', $docno);
        $info = array('status' => $status);
        $update = $builder->update($info);

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            // case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // A / R / C (1 huruf)
                'I.R',             // kode module dari menuprg
                'I.R.A.4'          // kode menu untuk BOM
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }

    function add_workingorder(){

        $data['title']="Input Working Order";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/workingorder'));
        }
        // =================================s

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_production->q_tmp_workingorder_mst($param)->getRowArray();
        $data['has_bom_history'] = false;
        if (!empty($data['dtldata']['docno'])) {
            $data['has_bom_history'] = $this->prod_wo_bom_history_exists(trim((string) $data['dtldata']['docno']));
        }
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('production/workingorder/v_add_workingorder',$data);
    }

    function showing_tmp_workingorder_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_trx_workingorder_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_workingorder()
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

        // mapping nmbranch → kode suffix (normalisasi: spasi/titik/angka/romawi)
        $branchKey = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $row['nmbranch']));

        $map = [
            'PTJATIMTAMANSTEELMFG' => 'PT',
            'PTJATIMTAMANSTEEL'    => 'PT',
            'JATIMTAMANSTEELMFG'   => 'PT',
            'JATIMTAMANSTEEL'      => 'PT',
            'PLANTI'               => 'PA',
            'PLANT1'               => 'PA',
            'PLAN1'                => 'PA',
            'PLANTII'              => 'PB',
            'PLANT2'               => 'PB',
            'PLAN2'                => 'PB',
        ];

        $kodeSuffix = strtoupper(trim($map[$branchKey] ?? ''));

        if ($kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mapping cabang belum diset'
            ]);
        }

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['workorder']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
    }

    public function getNextSuffix_workingorder_mst()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.workingorder_mst')
            ->select('docno')
            ->where("UPPER(docno) LIKE " . $this->db->escape(strtoupper($like) . '%'))
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
        $docnoWo  = strtoupper(trim((string) $this->request->getPost('docno')));
        $docnoBom = strtoupper(trim((string) $this->request->getPost('docnobom')));

        // Resolve cabang: terima idbranch (01.02) atau nama (PLANT II / PLANT 2)
        // lalu simpan sebagai idbranch. Tidak ada fallback ke PT.
        $cabangPost     = trim((string) $this->request->getPost('cabang'));
        $cabangResolved = $this->prod_resolve_branch_id($cabangPost);
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
                'cabang'     => $cabangResolved,
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'docdatefinish' => !empty(trim($this->request->getPost('docdatefinish') ?? ''))
                    ? date('Y-m-d', strtotime($this->request->getPost('docdatefinish')))
                    : null,
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

        // =====================================================
        // VALIDASI R1 : BOM harus berstatus APPROVE (A)
        // agar boleh ditarik ke Working Order.
        // =====================================================
        if (strtoupper(trim($bomMst['status'])) !== 'A') {

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'BOM belum di-approve (status bukan A). Tidak dapat ditarik ke WO.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. INSERT WORKINGORDER_BOM_MST
        |--------------------------------------------------------------------------
        */
        // -----------------------------------------------------
        // PENGUAT: nmbarang_jadi sering kosong karena bom_mst
        // tidak selalu menyimpannya. Ambil dari master barang.
        // -----------------------------------------------------
        $nmJadi = trim((string) ($bomMst['nmbarang_jadi'] ?? ''));

        if ($nmJadi === '') {
            $rowBarang = $db->query("
                SELECT TRIM(nmbarang) AS nmbarang
                  FROM sc_mst.mbarang
                 WHERE TRIM(idbarang) = TRIM(?)
                 LIMIT 1
            ", [trim((string) ($bomMst['idbarang_jadi'] ?? ''))])->getRowArray();

            $nmJadi = trim((string) ($rowBarang['nmbarang'] ?? ''));
        }

        if ($nmJadi === '') {
            $nmJadi = trim((string) ($bomMst['idbarang_jadi'] ?? ''));
        }

        $insertBomMst = $bomMst;

        unset($insertBomMst['id']);
        unset($insertBomMst['idurut']);
        unset($insertBomMst['dari_bagian']);
        unset($insertBomMst['inactive']);

        $insertBomMst['docref']    = $docnoWo;
        $insertBomMst['desc_bom']    = $insertBomMst['keterangan'];
        $insertBomMst['nmbarang_jadi'] = $nmJadi;
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

        // -----------------------------------------------------
        // CATAT BOM BARU PADA LOG (HANYA MODE EDIT WO)
        // -----------------------------------------------------
        if ($this->prod_bom_log_ready()) {
            $woTmp = $db->query("
                SELECT TRIM(docno) AS docno,
                       TRIM(COALESCE(docnotmp, '')) AS docnotmp,
                       TRIM(status) AS status
                  FROM sc_tmp.workingorder_mst
                 WHERE TRIM(docno) = TRIM(?)
                   AND TRIM(inputby) = TRIM(?)
                 LIMIT 1
            ", [$docnoWo, $nama])->getRowArray();

            if ($woTmp
                && trim((string) ($woTmp['docnotmp'] ?? '')) !== ''
                && strtoupper(trim((string) ($woTmp['status'] ?? ''))) === 'E') {

                $pending = $this->prod_wo_bom_log_pending(trim((string) $woTmp['docnotmp']), $nama);

                if ($pending) {
                    $db->table('sc_log.workingorder_bom_change')
                        ->where('id', $pending['id'])
                        ->update([
                            'bomno_baru'     => $docnoBom,
                            'idbarang_baru'  => $bomMst['idbarang_jadi'] ?? null,
                            'nmbarang_baru'  => $nmJadi,
                            'buildfor_baru'  => $bomMst['buildfor'] ?? null,
                            'buildunit_baru' => $bomMst['buildunit'] ?? null,
                            'desc_bom_baru'  => $insertBomMst['desc_bom'] ?? null,
                        ]);
                }
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



    function clear_workingorder_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_workingorder_mst($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/workingorder'));
        }
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

                // Batalkan log perubahan BOM yang masih PENDING
                if ($this->prod_bom_log_ready()) {
                    try {
                        $this->db->table('sc_log.workingorder_bom_change')
                            ->where('status', 'PENDING')
                            ->where('inputby', $nama)
                            ->update([
                                'status'    => 'CANCELLED',
                                'applyby'   => $nama,
                                'applydate' => date('Y-m-d H:i:s'),
                            ]);
                    } catch (\Throwable $e) {
                        // jangan gagalkan proses cancel WO
                    }
                }

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
            // $row[] = '<div class="ratakanan">'. number_format($lm->buildfor, 4, '.', ',') . '</div>';
            $row[] = '
            <input type="text"
                class="form-control form-control-sm jaccseparator ratakanan buildfor-input"
                data-id="'.$lm->docno.'"
                value="'.number_format($lm->buildfor, 4, '.', ',').'"
                disabled>';
            $row[] = $lm->buildunit;
            // $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->ttlprice, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->buildfor, 4, '.', ',') . '</div>';
            $row[] = $lm->buildunit;
            // $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->ttlprice, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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

        $header = $this->m_production->q_tmp_workingorder_mst($param)->getRowArray();
        $status = trim($header['status']);
        $docno  = trim($header['docno']);
        $cek = $this->m_production->q_tmp_workingorder_mst($paramdtl);
        $cek2 = $this->m_production->q_tmp_workingorder_mst($paramdtl2);


        $builder = $this->db->table('sc_tmp.workingorder_mst');

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

            return redirect()->to(base_url('/production/trans/workingorder'));
        } else {
            // Ambil dari request POST
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $kdcustomer = strtoupper(trim($this->request->getPost('kdcustomer')));
            $nmcustomer = strtoupper(trim($this->request->getPost('nmcustomer')));
            $alamatcustomer = strtoupper(trim($this->request->getPost('alamatcustomer')));
            $noso = strtoupper(trim($this->request->getPost('noso')));

            $db = $this->db;
            $db->transBegin();

            try {
                // Kunci per WO (hindari final ganda / bentrok cancel)
                $db->query("SELECT pg_advisory_xact_lock(hashtext(?))", [$docno]);

                // Penguat nmbarang_jadi (tmp) sebelum trigger memindahkan BOM
                $this->prod_heal_wo_bom_nmbarang($docno, $nama);

                // -------------------------------------------------
                // RESOLVE LOG PERUBAHAN BOM (SEBELUM STATUS F)
                // -------------------------------------------------
                $log     = null;
                $newBom  = '';
                $logNoop = false;

                if ($this->prod_bom_log_ready()) {
                    $log = $this->prod_wo_bom_log_pending($docno, $nama);
                }

                if ($log) {
                    $oldRef = trim((string) ($log['bomno_referensi'] ?? ''));
                    if ($oldRef === '') {
                        $oldRef = trim((string) ($log['bomno_lama'] ?? ''));
                    }

                    $newBom = trim((string) ($log['bomno_baru'] ?? ''));

                    $tmpBoms = $db->query("
                        SELECT TRIM(docno) AS docno
                          FROM sc_tmp.workingorder_bom_mst
                         WHERE TRIM(docref) = TRIM(?)
                           AND TRIM(inputby) = TRIM(?)
                    ", [$docno, $nama])->getResultArray();

                    $adaLama  = false;
                    $kandidat = [];

                    foreach ($tmpBoms as $tb) {
                        $bn = trim((string) ($tb['docno'] ?? ''));
                        if ($bn === '') {
                            continue;
                        }

                        if ($bn === trim((string) ($log['bomno_lama'] ?? '')) || $bn === $oldRef) {
                            $adaLama = true;
                        } else {
                            $kandidat[] = $bn;
                        }
                    }

                    if ($newBom === '') {
                        $kandidat = array_values(array_unique($kandidat));

                        if (count($kandidat) === 1) {
                            $newBom = $kandidat[0];
                        } elseif ($adaLama) {
                            $newBom  = trim((string) ($log['bomno_lama'] ?? ''));
                            $logNoop = true;
                        } else {
                            throw new \Exception('BOM baru belum dipilih. Tarik BOM pengganti sebelum Simpan Final.');
                        }
                    }

                    if ($newBom === '') {
                        throw new \Exception('BOM baru belum dipilih. Tarik BOM pengganti sebelum Simpan Final.');
                    }

                    $adaBaru = false;
                    foreach ($tmpBoms as $tb) {
                        if (trim((string) ($tb['docno'] ?? '')) === $newBom) {
                            $adaBaru = true;
                            break;
                        }
                    }

                    if (!$logNoop && !$adaBaru) {
                        throw new \Exception('BOM baru ' . $newBom . ' tidak ditemukan pada draft WO. Silakan tarik ulang BOM.');
                    }

                    if (trim((string) ($log['jenis'] ?? '')) === 'GANTI_BOM'
                        && $oldRef !== ''
                        && $newBom === trim((string) ($log['bomno_lama'] ?? ''))
                        && $oldRef === trim((string) ($log['bomno_lama'] ?? ''))) {
                        $logNoop = true;
                    }
                }

                // Update data header dulu sebelum set status F
                $updateHeader = [
                    'noso'              => $noso,
                    'keterangan'        => $keterangan,
                    'kdcustomer'        => $kdcustomer,
                    'nmcustomer'        => $nmcustomer,
                    'alamatcustomer'    => $alamatcustomer,
                ];

                $headerUpdated = $builder->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update($updateHeader);

                if ($headerUpdated === false) {
                    throw new \Exception($this->prod_errorDb());
                }

                $info = array('status' => 'F', 'updatedate' => date('Y-m-d H:i:s'), 'updateby' => $nama);

                $statusUpdated = $this->db->table('sc_tmp.workingorder_mst')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update($info);

                if ($statusUpdated === false) {
                    throw new \Exception($this->prod_errorDb());
                }

                // -------------------------------------------------
                // TERAPKAN HEADER TURUNAN (SETELAH TRIGGER GANTI BOM)
                // -------------------------------------------------
                if ($log) {
                    if ($logNoop) {
                        $this->prod_wo_bom_log_mark($log['id'], 'CANCELLED', $nama);
                    } else {
                        $oldRef2 = trim((string) ($log['bomno_referensi'] ?? ''));
                        if ($oldRef2 === '') {
                            $oldRef2 = trim((string) ($log['bomno_lama'] ?? ''));
                        }

                        if (trim((string) ($log['bomno_baru'] ?? '')) === '' && $newBom !== '') {
                            $db->table('sc_log.workingorder_bom_change')
                                ->where('id', $log['id'])
                                ->update(['bomno_baru' => $newBom]);
                        }

                        $affected = ['woe' => 0, 'mr' => 0, 'pbp' => 0, 'bpnm' => 0, 'total' => 0];

                        if ($oldRef2 !== '') {
                            $affected = $this->prod_apply_wo_bom_downstream($docno, $oldRef2, $newBom, $nama);
                        }

                        $this->prod_wo_bom_log_mark($log['id'], 'APPLIED', $nama, $affected);
                    }
                }

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaksi gagal');
                }

                $db->transCommit();

                if ($this->prod_hitungStatus('sc_trx.workingorder_mst', $docno, null, 'F') > 0) {
                    return redirect()->to(base_url('/production/trans/workingorder'))->with('prod_msg', $this->prod_msg('success', 'Working Order ' . $docno . ' berhasil di-final.'));
                }

                return redirect()->to(base_url('/production/trans/workingorder'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.'));

            } catch (\Throwable $e) {
                $db->transRollback();
                return redirect()->to(base_url('/production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $e->getMessage()));
            }
        }

    }

    public function get_workingorder_bom_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_workingorder_bom_dtl(" and idurut=" . $this->db->escape($id));

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

    /* Preview dampak ganti BOM / konfirmasi final WO (read-only). */
    public function preview_workingorder_bom_change()
    {
        $nama  = trim((string) $this->session->get('nama'));
        $ids   = $this->request->getPost('ids');
        $docno = trim((string) $this->request->getPost('docno'));
        $docref = trim((string) $this->request->getPost('docref'));

        if (!is_array($ids)) {
            $ids = ($ids === null || $ids === '') ? [] : [$ids];
        }

        $isEdit  = false;
        $wono    = '';
        $bomno   = '';
        $bomRow  = null;
        $woRow   = null;

        if (!empty($ids)) {
            $id = trim((string) $ids[0]);

            $bomRow = $this->db->query("
                SELECT TRIM(docno) AS docno, TRIM(docref) AS docref,
                       idbarang_jadi, nmbarang_jadi, buildfor, buildunit, desc_bom
                  FROM sc_tmp.workingorder_bom_mst
                 WHERE TRIM(docno) = TRIM(?)
                   AND TRIM(inputby) = TRIM(?)
                   " . ($docref !== '' ? "AND TRIM(docref) = TRIM(?)" : "") . "
                 LIMIT 1
            ", $docref !== '' ? [$id, $nama, $docref] : [$id, $nama])->getRowArray();

            if (!$bomRow) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data BOM tidak ditemukan.'
                ]);
            }

            $wono  = trim((string) $bomRow['docref']);
            $bomno = trim((string) $bomRow['docno']);

            $woRow = $this->db->query("
                SELECT TRIM(docno) AS docno,
                       TRIM(COALESCE(docnotmp, '')) AS docnotmp,
                       TRIM(status) AS status
                  FROM sc_tmp.workingorder_mst
                 WHERE TRIM(docno) = TRIM(?)
                   AND TRIM(inputby) = TRIM(?)
                 LIMIT 1
            ", [$wono, $nama])->getRowArray();
        } elseif ($docno !== '') {
            $woRow = $this->db->query("
                SELECT TRIM(docno) AS docno,
                       TRIM(COALESCE(docnotmp, '')) AS docnotmp,
                       TRIM(status) AS status
                  FROM sc_tmp.workingorder_mst
                 WHERE TRIM(docno) = TRIM(?)
                   AND TRIM(inputby) = TRIM(?)
                 LIMIT 1
            ", [$docno, $nama])->getRowArray();

            if (!$woRow) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Draft WO tidak ditemukan.'
                ]);
            }

            $wono = trim((string) $woRow['docno']);
        } else {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter tidak lengkap.'
            ]);
        }

        $isEdit = $woRow
            && trim((string) ($woRow['docnotmp'] ?? '')) !== ''
            && strtoupper(trim((string) ($woRow['status'] ?? ''))) === 'E';

        $pending  = $isEdit ? $this->prod_wo_bom_log_pending($wono, $nama) : null;
        $bomnoRef = '';

        if ($pending) {
            $bomnoRef = trim((string) ($pending['bomno_referensi'] ?? ''));
            if ($bomnoRef === '') {
                $bomnoRef = trim((string) ($pending['bomno_lama'] ?? ''));
            }
        }

        if ($bomnoRef === '') {
            $bomnoRef = $bomno; // preview hapus: BOM yang akan dihapus
        }

        $drafts  = $isEdit ? $this->prod_wo_bom_active_draft_rows($wono) : [];
        $impacts = ($isEdit && $bomnoRef !== '')
            ? $this->prod_wo_bom_impact_rows($wono, $bomnoRef)
            : [];

        $hasPbpFinal = false;
        $hasPrinted  = false;

        foreach ($impacts as $r) {
            $st = strtoupper(trim((string) ($r['status'] ?? '')));

            if (trim((string) ($r['modul'] ?? '')) === 'PENERIMAAN BP'
                && in_array($st, ['F', 'P', 'A'], true)) {
                $hasPbpFinal = true;
            }

            if ($st === 'P') {
                $hasPrinted = true;
            }
        }

        // BOM baru mungkin belum tercatat di log (mis. BOM ditarik lebih dulu,
        // baru BOM lama dihapus). Infer dari BOM yang ada di draft.
        $bomnoBaru = $pending ? trim((string) ($pending['bomno_baru'] ?? '')) : '';

        if ($isEdit && $pending && $bomnoBaru === '' && $bomnoRef !== '') {
            $tmpBoms = $this->db->query("
                SELECT TRIM(docno) AS docno
                  FROM sc_tmp.workingorder_bom_mst
                 WHERE TRIM(docref) = TRIM(?)
                   AND TRIM(inputby) = TRIM(?)
            ", [$wono, $nama])->getResultArray();

            $kandidat = [];
            foreach ($tmpBoms as $tb) {
                $bn = trim((string) ($tb['docno'] ?? ''));
                if ($bn === '') {
                    continue;
                }

                if ($bn !== trim((string) ($pending['bomno_lama'] ?? '')) && $bn !== $bomnoRef) {
                    $kandidat[] = $bn;
                }
            }

            $kandidat = array_values(array_unique($kandidat));
            if (count($kandidat) === 1) {
                $bomnoBaru = $kandidat[0];
            }
        }

        return $this->response->setJSON([
            'status'       => true,
            'log_ready'    => $this->prod_bom_log_ready(),
            'is_edit'      => $isEdit,
            'wono'         => $wono,
            'bomno_lama'   => $bomnoRef,
            'bomno_baru'   => $bomnoBaru,
            'has_pending'  => (bool) $pending,
            'drafts'       => $drafts,
            'draft_count'  => count($drafts),
            'impacts'      => $impacts,
            'impact_count' => count($impacts),
            'has_pbp_final'=> $hasPbpFinal,
            'has_printed'  => $hasPrinted,
        ]);
    }

    /* Riwayat BOM sebuah WO (read-only). */
    public function list_workingorder_bom_history()
    {
        $role  = trim((string) $this->session->get('roleid'));
        $docno = trim((string) $this->request->getPost('docno'));

        $akses   = $this->m_role->detail_user_akses($role, 'I.R.A.4')->getRowArray();
        $canView = isset($akses['a_view']) && trim((string) $akses['a_view']) === 't';

        if (!$canView) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Anda tidak memiliki akses melihat riwayat BOM.'
            ]);
        }

        if ($docno === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Nomor WO tidak boleh kosong.'
            ]);
        }

        if (!$this->prod_bom_log_ready()) {
            return $this->response->setJSON([
                'status'  => true,
                'docno'   => $docno,
                'items'   => [],
                'boms'    => [],
                'current' => [],
                'message' => 'Fitur riwayat BOM belum aktif (tabel log belum tersedia).'
            ]);
        }

        $items   = $this->m_production->q_wo_bom_history($docno)->getResultArray();
        $current = $this->m_production->q_wo_current_bom($docno)->getResultArray();

        $currentBoms = [];
        foreach ($current as $c) {
            $b = trim((string) ($c['bomno'] ?? ''));
            if ($b !== '') {
                $currentBoms[$b] = true;
            }
        }

        // BOM yang pernah dipakai = hanya log APPLIED + BOM aktif.
        $used = [];
        foreach (array_reverse($items) as $r) {
            if (strtoupper(trim((string) ($r['status'] ?? ''))) !== 'APPLIED') {
                continue;
            }

            foreach ([$r['bomno_referensi'] ?? '', $r['bomno_baru'] ?? ''] as $b) {
                $b = trim((string) $b);
                if ($b !== '') {
                    $used[$b] = true;
                }
            }
        }

        foreach (array_keys($currentBoms) as $b) {
            $used[$b] = true;
        }

        $boms = [];
        foreach (array_keys($used) as $b) {
            $master = $this->db->query("
                SELECT TRIM(bm.docno) AS bomno,
                       bm.idbarang_jadi,
                       COALESCE(NULLIF(TRIM(bm.nmbarang_jadi), ''), TRIM(mb.nmbarang)) AS nmbarang_jadi,
                       bm.keterangan AS desc_bom
                  FROM sc_trx.bom_mst bm
                  LEFT JOIN sc_mst.mbarang mb
                         ON TRIM(mb.idbarang) = TRIM(bm.idbarang_jadi)
                 WHERE TRIM(bm.docno) = TRIM(?)
                 LIMIT 1
            ", [$b])->getRowArray();

            $boms[] = [
                'bomno'         => $b,
                'idbarang_jadi' => $master['idbarang_jadi'] ?? null,
                'nmbarang_jadi' => $master['nmbarang_jadi'] ?? null,
                'desc_bom'      => $master['desc_bom'] ?? null,
                'aktif'         => isset($currentBoms[$b]),
            ];
        }

        return $this->response->setJSON([
            'status'  => true,
            'docno'   => $docno,
            'items'   => $items,
            'boms'    => $boms,
            'current' => $current,
        ]);
    }

    public function save_update_workingorder_bom()
    {
        $db = \Config\Database::connect();

        $nama = trim($this->session->get('nama'));

        $docno = trim($this->request->getPost('docno'));

        $docref = trim((string) $this->request->getPost('docref'));

        $buildforBaru = str_replace(',', '', $this->request->getPost('buildfor'));

        $db->transBegin();

        try{

            $sqlMst  = "SELECT *
                        FROM sc_tmp.workingorder_bom_mst
                        WHERE TRIM(docno)=?
                        AND TRIM(inputby)=?";
            $bindMst = [$docno, $nama];

            if ($docref !== '') {
                $sqlMst .= " AND TRIM(docref)=TRIM(?)";
                $bindMst[] = $docref;
            }

            $mst = $db->query($sqlMst . " LIMIT 1", $bindMst)->getRow();

            if(!$mst){
                throw new \Exception('Data BOM tidak ditemukan');
            }

            $buildforLama = (float)$mst->buildfor;

            if($buildforLama <= 0){
                throw new \Exception('Build For lama tidak valid');
            }

            $wono     = trim((string) ($mst->docref ?? ''));
            $docnotmp = trim((string) ($mst->docnotmp ?? ''));

            // BOM yang baru ditarik saat edit belum tentu punya docnotmp.
            // Ambil WO dari log PENDING (jika ada) agar buildfor tetap tercatat.
            if ($docnotmp === '' && $wono !== '' && $this->prod_bom_log_ready()) {
                $pendingWo = $this->prod_wo_bom_log_pending($wono, $nama);
                if ($pendingWo && trim((string) ($pendingWo['jenis'] ?? '')) === 'GANTI_BOM') {
                    $docnotmp = $wono;
                }
            }

            $ket      = strtoupper(trim((string) $this->request->getPost('keterangan')));

            if ($ket === '') {
                $ket = 'UBAH BUILD FOR';
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

            if ($docnotmp !== ''
                && abs($buildforLama - (float) $buildforBaru) > 0.0001
                && $this->prod_bom_log_ready()
                && $this->prod_wo_has_downstream($wono) > 0) {
                $pending = $this->prod_wo_bom_log_pending($docnotmp, $nama);

                if ($pending) {
                    $pendingNew = trim((string) ($pending['bomno_baru'] ?? ''));

                    if ($pendingNew === '' || $pendingNew === $docno) {
                        $db->table('sc_log.workingorder_bom_change')
                            ->where('id', $pending['id'])
                            ->update([
                                'bomno_baru'     => $docno,
                                'buildfor_baru'  => $buildforBaru,
                                'buildunit_baru' => $mst->buildunit ?? null,
                            ]);
                    }
                } else {
                    // Referensi = BOM yang masih direferensikan dokumen turunan
                    // (BOM final di trx). Jika hanya satu, pakai itu.
                    $referensi = $docno;
                    $trxBoms = $this->db->query("
                        SELECT TRIM(docno) AS docno
                          FROM sc_trx.workingorder_bom_mst
                         WHERE TRIM(docref) = TRIM(?)
                         ORDER BY docno
                    ", [$wono])->getResultArray();

                    if (count($trxBoms) === 1) {
                        $referensi = trim((string) ($trxBoms[0]['docno'] ?? ''));
                    }

                    $db->table('sc_log.workingorder_bom_change')->insert([
                        'docno'           => $docnotmp,
                        'jenis'           => 'UBAH_BUILDFOR',
                        'bomno_lama'      => $docno,
                        'bomno_baru'      => $docno,
                        'bomno_referensi' => $referensi,
                        'idbarang_lama'   => $mst->idbarang_jadi ?? null,
                        'idbarang_baru'   => $mst->idbarang_jadi ?? null,
                        'nmbarang_baru'   => $mst->nmbarang_jadi ?? null,
                        'buildfor_lama'   => $buildforLama,
                        'buildfor_baru'   => $buildforBaru,
                        'buildunit_baru'  => $mst->buildunit ?? null,
                        'desc_bom_baru'   => $mst->desc_bom ?? null,
                        'keterangan'      => $ket,
                        'status'          => 'PENDING',
                        'inputby'         => $nama,
                        'inputdate'       => date('Y-m-d H:i:s'),
                    ]);
                }
            }

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

        $keterangan = strtoupper(trim((string) $request->getPost('keterangan')));
        if ($keterangan === '') {
            $keterangan = 'GANTI BOM';
        }

        $db->transBegin();

        try {

            // Ambil BOM yang akan dihapus (sekaligus validasi kepemilikan)
            $docref = trim((string) $request->getPost('docref'));
            $ph   = implode(',', array_fill(0, count($ids), '?'));
            $bind = array_merge(
                [$nama],
                array_map(function ($v) {
                    return trim((string) $v);
                }, $ids)
            );

            $sqlRows = "
                SELECT TRIM(docno) AS docno, TRIM(docref) AS docref,
                       idbarang_jadi, nmbarang_jadi, buildfor, buildunit, desc_bom
                  FROM sc_tmp.workingorder_bom_mst
                 WHERE TRIM(inputby) = TRIM(?)
                   AND TRIM(docno) IN ({$ph})";

            if ($docref !== '') {
                $sqlRows .= " AND TRIM(docref) = TRIM(?)";
                $bind[] = $docref;
            }

            $rows = $db->query($sqlRows, $bind)->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $wono   = trim((string) $rows[0]['docref']);
            $bomIds = array_map(function ($r) {
                return trim((string) $r['docno']);
            }, $rows);

            // Status WO draft (mode edit bila docnotmp terisi)
            $wo = $db->query("
                SELECT TRIM(docno) AS docno,
                       TRIM(COALESCE(docnotmp, '')) AS docnotmp,
                       TRIM(status) AS status
                  FROM sc_tmp.workingorder_mst
                 WHERE TRIM(docno) = TRIM(?)
                   AND TRIM(inputby) = TRIM(?)
                 LIMIT 1
            ", [$wono, $nama])->getRowArray();

            $isEdit = $wo
                && trim((string) ($wo['docnotmp'] ?? '')) !== ''
                && strtoupper(trim((string) ($wo['status'] ?? ''))) === 'E';

            if ($isEdit && $this->prod_bom_log_ready()) {

                // Blokir jika masih ada draft transaksi turunan
                $drafts = $this->prod_wo_bom_active_draft_rows($wono);
                if (!empty($drafts)) {
                    $pesan = [];
                    foreach ($drafts as $d) {
                        $pesan[] = trim((string) $d['modul']) . ' ' . trim((string) $d['docno']) .
                                   ' (' . trim((string) $d['inputby']) . ')';
                    }

                    $db->transRollback();
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Tidak dapat mengganti BOM. Masih ada draft transaksi turunan yang belum final: ' .
                                     implode(', ', $pesan) .
                                     '. Batalkan atau final-kan draft tersebut terlebih dahulu.'
                    ]);
                }

                // Rantai log: pending sebelumnya menjadi SUPERSEDED
                $pending      = $this->prod_wo_bom_log_pending($wono, $nama);
                $rootBom      = '';
                $rootIdBarang = null;
                $rootBuildfor = null;

                if ($pending) {
                    $rootBom = trim((string) ($pending['bomno_referensi'] ?? ''));
                    if ($rootBom === '') {
                        $rootBom = trim((string) ($pending['bomno_lama'] ?? ''));
                    }
                    $rootIdBarang = $pending['idbarang_lama'] ?? null;
                    $rootBuildfor = $pending['buildfor_lama'] ?? null;

                    $db->table('sc_log.workingorder_bom_change')
                        ->where('id', $pending['id'])
                        ->update(['status' => 'SUPERSEDED']);
                }

                foreach ($rows as $r) {
                    $bom = trim((string) $r['docno']);

                    if ($rootBom === '') {
                        $rootBom      = $bom;
                        $rootIdBarang = $r['idbarang_jadi'] ?? null;
                        $rootBuildfor = $r['buildfor'] ?? null;
                    }

                    $db->table('sc_log.workingorder_bom_change')->insert([
                        'docno'           => $wono,
                        'jenis'           => 'GANTI_BOM',
                        'bomno_lama'      => $bom,
                        'bomno_baru'      => null,
                        'bomno_referensi' => $rootBom,
                        'idbarang_lama'   => $rootIdBarang,
                        'buildfor_lama'   => $rootBuildfor,
                        'keterangan'      => $keterangan,
                        'status'          => 'PENDING',
                        'inputby'         => $nama,
                        'inputdate'       => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Hapus header BOM
            $builder->whereIn('docno', $bomIds)
                ->where('inputby', $nama)
                ->delete();

            // Hapus detail BOM per BOM (bukan hanya ids[0])
            foreach ($bomIds as $bom) {
                $db->table('sc_tmp.workingorder_bom_dtl')
                    ->where('docno', $bom)
                    ->where('inputby', $nama)
                    ->delete();
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/workingorder'));
        }
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
            return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Document tidak ditemukan.'));
        }

        // VALIDASI PEMILIK DOKUMEN
        if (trim($dtl['inputby']) !== $nama) {
            return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diubah.'));
        }

        $status = strtoupper(trim($dtl['status']));

        if ($status === 'F' || $status === 'P') {

            $info = ['status' => 'E', 'updatedate' => date('Y-m-d H:i:s'), 'updateby' => $nama];

            // UPDATE (pengaman status : hanya F/P yang boleh dibuka edit)
            $this->db
                ->table('sc_trx.workingorder_mst')
                ->where('docno', trim($docno))
                ->whereIn('status', ['F', 'P'])
                ->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
            }

            if ($this->prod_hitungStatus('sc_trx.workingorder_mst', $docno, null, 'E') > 0) {
                return redirect()->to(base_url('production/trans/add_workingorder'))->with('prod_msg', $this->prod_msg('success', 'Working Order ' . trim($docno) . ' berhasil dibuka untuk edit.'));
            }

            return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.'));
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
        $kodemenu='I.R.A.4'; $versirelease='I.R.A.4/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_production->q_trx_workingorder_mst($param)->getRowArray();
        $data['has_bom_history'] = $this->prod_wo_bom_history_exists(trim((string) $decoded_docno));
        return $this->template->render('production/workingorder/v_detail_workingorder',$data);
    }

    public function cancel_workingorder_()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/workingorder'));
        }
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
            return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Document tidak ditemukan.'));
        }

        // VALIDASI PEMILIK DOKUMEN
        if (trim($dtl['inputby']) !== $nama) {
            return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat dicancel.'));
        }

        $status = strtoupper(trim($dtl['status']));

        if ($status === 'F' || $status === 'P') {
            $info = ['status' => 'C', 'updatedate' => date('Y-m-d H:i:s'), 'updateby' => $nama];

            $this->db->transStart();

            try {
                $this->db
                    ->table('sc_trx.workingorder_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Proses gagal : ' . $errDb));
                }
                if ($this->prod_hitungStatus('sc_trx.workingorder_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();
                    return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.'));
                }

                $this->db
                    ->table('sc_trx.workingorder_bom_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', $this->prod_pesan_db($e)));
            }

            if ($this->db->transStatus() === false) {
                return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('error', 'Gagal cancel document.'));
            }

            return redirect()->to(base_url('production/trans/workingorder'))->with('prod_msg', $this->prod_msg('success', 'Working Order ' . trim($docno) . ' berhasil dicancel.'));
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
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
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
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Work Order Execution : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Work Order Execution 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('production/trans/detailWOE') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">
                    <i class="fa fa-eye"></i> Detail Work Order Execution 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('production/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Work Order Execution : ' . $docno . '\')">
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

                // selain status tersebut â†’ tampilkan sesuai hak akses
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
                'd-m-Y',
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


            $row[] = $lm->bagian;
            $row[] = $lm->idbarang_jadi;
            $row[] = $lm->batchno;
            // $row[] = $lm->buildfor;
            $row[] = '<div class="ratakanan">'. number_format($lm->buildfor, 4, '.', ',') . '</div>';
            $row[] = $lm->keterangan;

            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/woe'));
        }
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
        $kodemenu='I.R.A.5'; $versirelease='I.R.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/woe'));
        }
        // =================================s

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

        // mapping nmbranch â†’ kode suffix
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['workorderexecution']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
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

        // HEADER SUDAH ADA â†’ TIDAK PERLU RELOAD
        if ($exists > 0) {
            return $this->response->setJSON([
                'success' => true,
                'reload'  => false
            ]);
        }

        // HEADER BARU â†’ INSERT
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
            'reload'  => true   // â¬… PENTING
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

            $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)
                ->update([
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
                    ->where('inputby', $nama)
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

        $woeHeader = $builderHeader->select('idtax')
            ->where('inputby', $nama)
            ->where('docno', $docno)->get()->getRowArray();
        $idtax = $woeHeader['idtax'] ?? '';

        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.woe_dtl');
        $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->where('inputby', $nama)
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
        $builderHeader->where('docno', $docno)
            ->where('inputby', $nama)
            ->update([
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/woe'));
        }

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

        $param = " and docno=" . $this->db->escape($docno);
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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $docdate = date('Y-m-d', strtotime(trim($this->request->getPost('docdate'))));
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
            $param_brg = " and docno=" . $this->db->escape($docno);
        }

        // //idgroup
        // if (!empty($idgroup)) {
        //     $param_group=" and idgroup='$idgroup'";
        // } else {  $param_group=""; }


        $databranch = $this->m_global->q_master_branch();
        $param=" and docno=" . $this->db->escape($docno);
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
        $kodemenu='I.R.A.8'; $versirelease='I.R.A.8/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* Flash message dari proses FINAL / CANCEL (Sprint 1) */
        $flash = session()->getFlashdata('prod_msg');
        $data['message'] = $this->prod_msg_html($flash) . $data['message'];

        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_materialrelease_mst($param);
        $logindate = trim($this->session->get('logindate'));

        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
            if ($canUpdate && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_materialrelease_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Material Release : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detail_materialrelease_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete  && trim($lm->inputby) == $nama && $status=='F') {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancel_materialrelease_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Material Release : ' . $docno . '\')">

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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-warning';
                    break;

                case 'F':
                    $badgeClass = 'badge-info';
                    break;

                case 'A':
                    $badgeClass = 'badge-success';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;

            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;


            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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
        $kodemenu='I.R.A.8'; $versirelease='I.R.A.8/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/materialrelease'));
        }
        // =================================s

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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_trx_materialrelease_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_materialrelease()
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

        // mapping nmbranch â†’ kode suffix
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['materialrelease']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
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

        // =========================
        // VALIDASI WO
        // Hanya WO status FINAL (F) yang boleh ditarik.
        // WO yang sudah COMPLETED (A) tidak boleh ditarik lagi.
        // =========================
        $wono = trim($this->request->getPost('wono'));

        $pesanWo = $this->prod_cek_wo($wono);

        if ($pesanWo !== '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => $pesanWo
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.materialrelease_mst');
        $builderDetail = $db->table('sc_tmp.materialrelease_dtl');
        $oldWoeno  = trim($this->request->getPost('old_woeno'));
        $woeno = trim($this->request->getPost('woeno'));

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        // WO yang tersimpan di dokumen (untuk deteksi ganti WO)
        $wonoLama = '';
        if ($exists > 0) {
            $rowLama = $this->m_production->q_tmp_materialrelease_doc($docno, $nama)->getRowArray();
            if ($rowLama) {
                $wonoLama = trim($rowLama['wono']);
            }
        }

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
                'woeno'     => $woeno,
                'wono'     => $wono,
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

                    'woeno'        => $woeno,
                    'wono'         => $wono,
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
        // Kalau WO berubah -> bersihkan SEMUA detail dokumen ini supaya
        // 1 dokumen hanya berisi 1 WO (menjaga akurasi costing & jurnal).
        // =========================
        // =========================
        // BERSIHKAN DETAIL DOKUMEN INI SEBELUM GENERATE ULANG DARI WO
        // (idempoten: tarik ulang / double-click tidak menghasilkan baris ganda)
        // =========================
        $delete = $builderDetail
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->delete();

        if (!$delete) {

            $error = $db->error();

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => $error['message']
            ]);
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================

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

            // uniqueid deterministik -> aman diulang, tidak menghasilkan baris ganda
            $rawUnique = $docno . '|' . trim($row->idbarang) . '|' . trim($row->doctype_detail) . '|' . $row->idurut;

            $insert = $builderDetail->insert([

                'docno'=>$docno,
                'docref'=>$woeno,
                'doctype_detail'=> $row->doctype_detail,
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
                'tabno'      => trim($this->request->getPost('tabno')),
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

            // =========================
            // UPDATE HEADER (draft) SAAT SIMPAN DETAIL
            // Kolom mengikuti yang di-update di final_input_materialrelease
            // supaya keterangan/batchno/bagian/tabno tidak hilang
            // =========================
            $updateHeader = $db->table('sc_tmp.materialrelease_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->update([
                    'keterangan' => strtoupper(trim((string) $this->request->getPost('keterangan'))),
                    'batchno'    => trim((string) $this->request->getPost('batchno')),
                    'bagian'     => trim((string) $this->request->getPost('bagian')),
                    'tabno'      => trim((string) $this->request->getPost('tabno')),
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
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
        // AMBIL DATA DETAIL
        // =========================
        $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan apa adanya

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


        if (!$idbarang && !$idbarangBom) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.materialrelease_dtl');
        $spec = strtoupper(trim($this->request->getPost('spec')));

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            // ->where('doctype_detail', $doctype_detail)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('inputby', $nama)
            ->where('description', $description);

        if ($uniqueid) {
            $builderDuplicate->where('uniqueid !=', $uniqueid);
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
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)
                ->update([
                    // 'doctype_detail' => $doctype_detail,
                    'idbarang' => $idbarang ?? $idbarangBom,                    'nmbarang'       => $nmbarang,
                    'idbarang_bom'       => $idbarangBom,
                    // 'nmbarang_bom'       => $nmbarangBom,
                    'unit'           => $unit,
                    'docdate'        => trim($this->request->getPost('docdate')),
                    'qty'            => $qty,
                    'issub'            => $issub,
                    'description'    => $description,
                    'spec'           => $spec,
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
                'idbarang'       => ($idbarang !== null && $idbarang !== '') ? $idbarang : $idbarangBom,
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
                'spec'           => $spec,
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/materialrelease'));
        }
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

        // idlocation header (gudang terpilih) untuk cek stok per barang
        $idlocation = '';

        $hdr = null;
        if ($docno !== '') {
            $hdr = $this->db->table('sc_tmp.materialrelease_mst')
                ->select('idlocation')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();
        }

        // Datatable detail tidak mengirim docno -> ambil header draft milik user
        // (mengikuti filter model: inputby = session nama)
        if (!$hdr) {
            $nama = trim((string) $this->session->get('nama'));

            $hdr = $this->db->table('sc_tmp.materialrelease_mst')
                ->select('idlocation')
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->orderBy('idurut', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if (!$hdr) {
                $hdr = $this->db->table('sc_tmp.materialrelease_mst')
                    ->select('idlocation')
                    ->where('inputby', $nama)
                    ->orderBy('idurut', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
            }
        }

        if ($hdr) {
            $idlocation = trim((string) $hdr['idlocation']);
        }

        // preload saldo stok dari sc_trx.stkblc (presisi 4 desimal).
        // Catatan: sc_mst.stkgdw.onhand bertipe numeric(18,2) sehingga qty
        // di bawah 0,005 terbaca 0,00 dan membuat kolom Stok / validasi MR salah.
        //   - per (idbarang + batch)
        //   - per idbarang (total semua batch, dipakai bila spec/batch kosong)
        $stockMap = array();
        $stockItemMap = array();
        if ($idlocation !== '') {
            $rows = $this->db->query("
                SELECT TRIM(idbarang) AS idbarang,
                       TRIM(COALESCE(batch,'')) AS batch,
                       COALESCE(SUM(COALESCE(qty_in,0) - COALESCE(qty_out,0)), 0) AS stock
                FROM sc_trx.stkblc
                WHERE TRIM(idlocation) = ?
                  AND COALESCE(grouptype, 'STOCK') = 'STOCK'
                GROUP BY TRIM(idbarang), TRIM(COALESCE(batch,''))
            ", [$idlocation])->getResultArray();
            foreach ($rows as $r) {
                $idb = trim((string) $r['idbarang']);
                $btc = trim((string) $r['batch']);
                $stk = (float) $r['stock'];
                $stockMap[$idb . '|' . $btc] = $stk;
                if (!isset($stockItemMap[$idb])) {
                    $stockItemMap[$idb] = 0;
                }
                $stockItemMap[$idb] += $stk;
            }
        }

        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->uniqueid;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';

            // KOLOM STOK (hanya baris MATERIAL yang dibandingkan dengan stok gudang)
            $isMaterial = strtoupper(trim((string) $lm->doctype_detail)) === 'MATERIAL';
            if ($isMaterial) {
                $qty   = (float) $lm->qty;
                $idb   = trim((string) $lm->idbarang);
                $btc   = trim((string) $lm->spec);
                if ($btc !== '') {
                    // spec tidak selalu berupa batch: bila tidak ada batch yang sama,
                    // pakai total stok item agar tidak salah terbaca 0
                    $stock = (float) ($stockMap[$idb . '|' . $btc] ?? $stockItemMap[$idb] ?? 0);
                } else {
                    $stock = (float) ($stockItemMap[$idb] ?? 0);
                }
                $over  = ($qty > $stock);
                $row[] = '<div class="ratakanan" data-stock="' . $stock . '" data-qty="' . $qty . '" data-over="' . ($over ? '1' : '0') . '">'
                    . number_format($stock, 4, '.', ',')
                    . ($over ? ' <i class="fa fa-exclamation-triangle text-warning" title="Qty melebihi stok gudang"></i>' : '')
                    . '</div>';
            } else {
                $row[] = '<div class="ratakanan" data-over="0">-</div>';
            }

            $row[] = $lm->unit;
            // $row[] = '<div class="ratakanan">'. number_format($lm->standartcost, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->totalcost, 4, '.', ',') . '</div>';
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

    /* =========================================================
     * Grid detail Material Release (JSON, tanpa protokol DataTables)
     * Dipakai oleh inline grid ala pmk_brng
     * (assets/pagejs/production/materialrelease/materialrelease_grid.js).
     * Mengembalikan juga kolom Stok (sc_trx.stkblc, presisi 4 desimal).
     * ========================================================= */
    public function list_materialrelease_dtl_grid()
    {
        $nama  = trim((string) $this->session->get('nama'));
        $docno = trim((string) $this->request->getGet('docno'));

        /* idlocation header (gudang terpilih) → dasar hitung stok */
        $hdr = null;

        if ($docno !== '') {
            $hdr = $this->db->table('sc_tmp.materialrelease_mst')
                ->select('idlocation')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();
        }

        if (!$hdr) {
            $hdr = $this->db->table('sc_tmp.materialrelease_mst')
                ->select('idlocation')
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->orderBy('idurut', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
        }

        if (!$hdr) {
            $hdr = $this->db->table('sc_tmp.materialrelease_mst')
                ->select('idlocation')
                ->where('inputby', $nama)
                ->orderBy('idurut', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
        }

        $idlocation = $hdr ? trim((string) $hdr['idlocation']) : '';

        /* preload saldo stok dari sc_trx.stkblc (presisi 4 desimal) */
        $stockMap     = array();
        $stockItemMap = array();

        if ($idlocation !== '') {
            $rows = $this->db->query("
                SELECT TRIM(idbarang) AS idbarang,
                       TRIM(COALESCE(batch,'')) AS batch,
                       COALESCE(SUM(COALESCE(qty_in,0) - COALESCE(qty_out,0)), 0) AS stock
                FROM sc_trx.stkblc
                WHERE TRIM(idlocation) = ?
                  AND COALESCE(grouptype, 'STOCK') = 'STOCK'
                GROUP BY TRIM(idbarang), TRIM(COALESCE(batch,''))
            ", [$idlocation])->getResultArray();

            foreach ($rows as $r) {
                $idb = trim((string) $r['idbarang']);
                $btc = trim((string) $r['batch']);
                $stk = (float) $r['stock'];

                $stockMap[$idb . '|' . $btc] = $stk;

                if (!isset($stockItemMap[$idb])) {
                    $stockItemMap[$idb] = 0;
                }

                $stockItemMap[$idb] += $stk;
            }
        }

        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama)
               . " order by idurut asc";

        $list  = $this->m_production->q_tmp_materialrelease_dtl($param)->getResultArray();
        $items = array();

        foreach ($list as $lm) {
            $issub = (trim((string) ($lm['issub'] ?? '')) === 'T') ? 'T' : 'F';

            $idb = trim((string) ($lm['idbarang'] ?? ''));
            $idbBom = trim((string) ($lm['idbarang_bom'] ?? ''));

            /* baris BOM lama bisa punya idbarang NULL (insert versi lama hanya
               mengisi idbarang_bom) → pakai idbarang_bom agar tetap tampil */
            if ($idb === '') {
                $idb = $idbBom;
            }

            $btc = trim((string) ($lm['spec'] ?? ''));
            $qty = (float) ($lm['qty'] ?? 0);

            if ($btc !== '') {
                $stock = (float) ($stockMap[$idb . '|' . $btc] ?? $stockItemMap[$idb] ?? 0);
            } else {
                $stock = (float) ($stockItemMap[$idb] ?? 0);
            }

            $items[] = array(
                'uniqueid'       => trim((string) ($lm['uniqueid'] ?? '')),
                'idurut'         => trim((string) ($lm['idurut'] ?? '')),
                'issub'          => $issub,
                'idbarang'       => $idb,
                'idbarang_bom'   => $idbBom,
                'nmbarang'       => trim((string) ($lm['nmbarang'] ?? '')),
                'spec'           => $btc,
                'qty'            => $qty,
                'unit'           => trim((string) ($lm['unit'] ?? '')),
                'description'    => trim((string) ($lm['description'] ?? '')),
                'doctype_detail' => trim((string) ($lm['doctype_detail'] ?? '')),
                'stock'          => $stock,
                'idlocation'     => $idlocation,
            );
        }

        return $this->response->setJSON(array(
            'status' => true,
            'total'  => count($items),
            'items'  => $items,
        ));
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
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

    /* ============================================================
       SPRINT 1 - HELPER PENGAMAN PRODUCTION
       Dipakai modul Material Release (modul lain menyusul).
       Semua method di bawah ini TAMBAHAN, tidak mengubah yang lama.
       ============================================================ */

    /* Susun data flash message. $type : success | error | warning */
    private function prod_msg($type, $text)
    {
        return ['type' => $type, 'text' => $text];
    }

    /* Render flash message menjadi alert (kelas sama seperti $message existing). */
    private function prod_msg_html($flash)
    {
        if (empty($flash) || empty($flash['text'])) {
            return '';
        }

        $cls = 'alert-info';
        if ($flash['type'] === 'success') {
            $cls = 'alert-success';
        } elseif ($flash['type'] === 'error') {
            $cls = 'alert-danger';
        } elseif ($flash['type'] === 'warning') {
            $cls = 'alert-warning';
        }

        return "<div class='alert " . $cls . "'>" . $flash['text'] . "</div>";
    }

    /* Cek WO yang akan ditarik.
       Aturan : hanya WO status 'F' yang boleh ditarik.
       Return : '' kalau boleh, selain itu pesan error. */
    private function prod_cek_wo($wono)
    {
        $wono = trim($wono);

        if ($wono === '') {
            return 'Nomor WO belum dipilih.';
        }

        $dtl = $this->m_production->q_wo_status($wono)->getRowArray();

        if (!$dtl) {
            return 'WO ' . $wono . ' tidak ditemukan.';
        }

        $status = strtoupper(trim($dtl['status']));

        if ($status === 'A') {
            return 'WO ' . $wono . ' sudah COMPLETED, tidak dapat ditarik lagi.';
        }

        if ($status !== 'F') {
            return 'WO ' . $wono . ' berstatus ' . $status . '. Hanya WO status FINAL (F) yang dapat ditarik.';
        }

        return '';
    }

    /* Resolve cabang dari idbranch ATAU nama cabang (PLANT I/II, PT ...).
       Return idbranch bila ketemu; selain itu kembalikan nilai asli (trim). */
    private function prod_resolve_branch_id($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        // 1) sudah berupa idbranch
        $row = $this->db->table('sc_mst.branchjob')
            ->select('idbranch')
            ->where('idbranch', $value)
            ->get()
            ->getRowArray();

        if ($row) {
            return trim((string) $row['idbranch']);
        }

        // 2) berupa nama cabang → normalisasi
        $aliases = [
            'PTJATIMTAMANSTEELMFG' => 'PTJATIMTAMANSTEELMFG',
            'PTJATIMTAMANSTEEL'    => 'PTJATIMTAMANSTEELMFG',
            'JATIMTAMANSTEELMFG'   => 'PTJATIMTAMANSTEELMFG',
            'JATIMTAMANSTEEL'      => 'PTJATIMTAMANSTEELMFG',
            'PLANTI'               => 'PLANTI',
            'PLANT1'               => 'PLANTI',
            'PLAN1'                => 'PLANTI',
            'PLANTII'              => 'PLANTII',
            'PLANT2'               => 'PLANTII',
            'PLAN2'                => 'PLANTII',
        ];

        $want = strtoupper(preg_replace('/[^A-Z0-9]/', '', $value));
        $want = $aliases[$want] ?? $want;

        $rows = $this->db->table('sc_mst.branchjob')
            ->select('idbranch, nmbranch')
            ->get()
            ->getResultArray();

        foreach ($rows as $r) {
            $key = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $r['nmbranch']));
            $key = $aliases[$key] ?? $key;

            if ($key !== '' && $key === $want) {
                return trim((string) $r['idbranch']);
            }
        }

        return $value;
    }

    /* Cek kunci WO untuk dokumen turunan.
       Aturan : kalau WO sudah 'A' (completed) maka dokumen turunan terkunci.
       Return : '' kalau boleh diubah, selain itu pesan error. */
    private function prod_cek_wo_lock($wono)
    {
        $wono = trim($wono);

        if ($wono === '') {
            return '';
        }

        $dtl = $this->m_production->q_wo_status($wono)->getRowArray();

        if ($dtl && strtoupper(trim($dtl['status'])) === 'A') {
            return 'WO ' . $wono . ' sudah COMPLETED. Dokumen ini terkunci dan tidak dapat diubah.';
        }

        return '';
    }

    /* Format tanggal input untuk kolom list produksi. */
    private function prod_fmt_inputdate($value, $format = 'd-m-Y')
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $ts = strtotime($value);

        return $ts ? date($format, $ts) : $value;
    }

    /* ============================================================
       LOG / RIWAYAT PERUBAHAN BOM WORKING ORDER
       ============================================================ */

    /* Cek ketersediaan tabel log. Jika script DDL belum dijalankan,
       modul WO tetap berjalan seperti biasa (fitur log non-aktif). */
    private function prod_bom_log_ready()
    {
        static $ready = null;

        if ($ready !== null) {
            return $ready;
        }

        try {
            $row = $this->db->query("
                SELECT to_regclass('sc_log.workingorder_bom_change') AS tbl
            ")->getRowArray();

            $ready = !empty($row['tbl']);
        } catch (\Throwable $e) {
            $ready = false;
        }

        return $ready;
    }

    /* Apakah WO sudah punya riwayat perubahan BOM. */
    private function prod_wo_bom_history_exists($wono)
    {
        if (!$this->prod_bom_log_ready()) {
            return false;
        }

        try {
            $row = $this->m_production->q_wo_bom_history_exists($wono)->getRowArray();
            return ((int) ($row['jml'] ?? 0)) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /* Log PENDING terakhir untuk sebuah WO (opsional milik user). */
    private function prod_wo_bom_log_pending($wono, $inputby = null)
    {
        if (!$this->prod_bom_log_ready()) {
            return null;
        }

        try {
            $row = $this->m_production->q_wo_bom_pending($wono, $inputby)->getRowArray();
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* Tandai log selesai / batal / digantikan. */
    private function prod_wo_bom_log_mark($id, $status, $nama, $affected = null)
    {
        if (!$this->prod_bom_log_ready() || empty($id)) {
            return;
        }

        $data = [
            'status'    => strtoupper(trim((string) $status)),
            'applyby'   => $nama,
            'applydate' => date('Y-m-d H:i:s'),
        ];

        if (is_array($affected)) {
            $data['affected_count'] = (int) ($affected['total'] ?? 0);
            $data['affected_list']  = json_encode($affected);
        }

        $this->db->table('sc_log.workingorder_bom_change')
            ->where('id', $id)
            ->update($data);
    }

    /* Draft turunan aktif (E) — untuk blokir ganti BOM. */
    private function prod_wo_bom_active_draft_rows($wono)
    {
        try {
            return $this->m_production->q_wo_bom_active_draft($wono)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /* Dokumen turunan yang header-nya akan mengikuti BOM baru. */
    private function prod_wo_bom_impact_rows($wono, $bomno)
    {
        try {
            return $this->m_production->q_wo_bom_impact($wono, $bomno)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /* Jumlah dokumen turunan aktif (trx / tmp) untuk sebuah WO. */
    private function prod_wo_has_downstream($wono)
    {
        try {
            $row = $this->m_production->q_wo_has_downstream($wono)->getRowArray();
            return (int) ($row['total'] ?? 0);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /* Penguat: pastikan nmbarang_jadi di tmp WO BOM tidak kosong
       sebelum trigger memindahkannya ke trx. */
    private function prod_heal_wo_bom_nmbarang($wono, $nama)
    {
        try {
            $this->db->query("
                UPDATE sc_tmp.workingorder_bom_mst w
                SET nmbarang_jadi = COALESCE(
                        NULLIF(TRIM(w.nmbarang_jadi), ''),
                        (SELECT NULLIF(TRIM(m.nmbarang), '')
                           FROM sc_mst.mbarang m
                          WHERE TRIM(m.idbarang) = TRIM(w.idbarang_jadi)
                          LIMIT 1),
                        TRIM(w.idbarang_jadi)
                    )
                WHERE TRIM(w.docref) = TRIM(?)
                  AND TRIM(w.inputby) = TRIM(?)
                  AND COALESCE(TRIM(w.nmbarang_jadi), '') = ''
            ", [$wono, $nama]);
        } catch (\Throwable $e) {
            // Penguat bersifat opsional; jangan gagalkan final WO.
        }
    }

    /* Update header satu tabel turunan (hanya jika status bukan C/E). */
    private function prod_update_downstream_header($table, array $set, $wono, $bomnoLama, $withUnit = false)
    {
        $countRow = $this->db->query("
            SELECT COUNT(*) AS jml
              FROM {$table}
             WHERE TRIM(wono) = TRIM(?)
               AND TRIM(COALESCE(bomno, '')) = TRIM(?)
               AND COALESCE(TRIM(status), '') NOT IN ('C', 'E')
        ", [$wono, $bomnoLama])->getRowArray();

        $count = (int) ($countRow['jml'] ?? 0);
        if ($count <= 0) {
            return 0;
        }

        $cols = ['bomno', 'desc_bom', 'idbarang_jadi', 'nmbarang_jadi', 'buildfor'];
        if ($withUnit) {
            $cols[] = 'buildunit';
        }

        $setSql = implode(', ', array_map(function ($c) {
            return $c . ' = ?';
        }, $cols));

        $bind = [];
        foreach ($cols as $c) {
            $bind[] = $set[$c] ?? null;
        }
        $bind[] = $wono;
        $bind[] = $bomnoLama;

        $ok = $this->db->query("
            UPDATE {$table}
               SET {$setSql}
             WHERE TRIM(wono) = TRIM(?)
               AND TRIM(COALESCE(bomno, '')) = TRIM(?)
               AND COALESCE(TRIM(status), '') NOT IN ('C', 'E')
        ", $bind);

        if ($ok === false) {
            throw new \Exception($this->prod_errorDb());
        }

        return $count;
    }

    /* Terapkan perubahan header ke seluruh dokumen turunan. */
    private function prod_apply_wo_bom_downstream($wono, $bomnoLama, $bomnoBaru, $nama)
    {
        $wono      = trim((string) $wono);
        $bomnoLama = trim((string) $bomnoLama);
        $bomnoBaru = trim((string) $bomnoBaru);

        $result = ['woe' => 0, 'mr' => 0, 'pbp' => 0, 'bpnm' => 0, 'total' => 0];

        if ($wono === '' || $bomnoLama === '' || $bomnoBaru === '') {
            return $result;
        }

        $bom = $this->db->query("
            SELECT TRIM(docno) AS bomno, idbarang_jadi, nmbarang_jadi,
                   buildfor, buildunit, desc_bom
              FROM sc_trx.workingorder_bom_mst
             WHERE TRIM(docref) = TRIM(?)
               AND TRIM(docno) = TRIM(?)
             LIMIT 1
        ", [$wono, $bomnoBaru])->getRowArray();

        if (!$bom) {
            throw new \Exception('BOM baru ' . $bomnoBaru . ' tidak ditemukan pada WO ' . $wono . '.');
        }

        $nmJadi = trim((string) ($bom['nmbarang_jadi'] ?? ''));

        if ($nmJadi === '') {
            $rowBarang = $this->db->query("
                SELECT TRIM(nmbarang) AS nmbarang
                  FROM sc_mst.mbarang
                 WHERE TRIM(idbarang) = TRIM(?)
                 LIMIT 1
            ", [trim((string) ($bom['idbarang_jadi'] ?? ''))])->getRowArray();

            $nmJadi = trim((string) ($rowBarang['nmbarang'] ?? ''));
        }

        if ($nmJadi === '') {
            $nmJadi = trim((string) ($bom['idbarang_jadi'] ?? ''));
        }

        $set = [
            'bomno'         => $bomnoBaru,
            'desc_bom'      => $bom['desc_bom'] ?? null,
            'idbarang_jadi' => $bom['idbarang_jadi'] ?? null,
            'nmbarang_jadi' => $nmJadi,
            'buildfor'      => $bom['buildfor'] ?? null,
        ];

        $result['woe']  = $this->prod_update_downstream_header('sc_trx.woe', $set, $wono, $bomnoLama, false);
        $result['mr']   = $this->prod_update_downstream_header('sc_trx.materialrelease_mst', $set + ['buildunit' => $bom['buildunit'] ?? null], $wono, $bomnoLama, true);
        $result['pbp']  = $this->prod_update_downstream_header('sc_trx.penerimaanbp_mst', $set + ['buildunit' => $bom['buildunit'] ?? null], $wono, $bomnoLama, true);
        $result['bpnm'] = $this->prod_update_downstream_header('sc_trx.bpnm_mst', $set + ['buildunit' => $bom['buildunit'] ?? null], $wono, $bomnoLama, true);

        $result['total'] = $result['woe'] + $result['mr'] + $result['pbp'] + $result['bpnm'];

        return $result;
    }

    /* Ambil docno draft milik user (authoritative, tidak percaya input client).
       Return : array ['docno'=>..., 'row'=>...] atau null kalau tidak ada. */
    private function prod_ambil_draft($table, $docno, $inputby)
    {
        $sql = "SELECT * FROM " . $table . "
                    WHERE coalesce(inputby,'') = ?
                      AND (? = '' OR trim(docno) = trim(?))
                    ORDER BY inputdate DESC
                    LIMIT 1";

        $row = $this->db->query($sql, [$inputby, $docno, $docno])->getRowArray();

        if (!$row) {
            return null;
        }

        return ['docno' => trim($row['docno']), 'row' => $row];
    }

    /* Verifikasi hasil update lewat READ-BACK.
       Dipakai menggantikan $this->db->affectedRows() yang tidak aman pada
       driver Postgre CI4 ini (bila query gagal, resultID=false sehingga
       pg_affected_rows() melempar TypeError dan error asli tertutup).
       Return : jumlah baris yang cocok dengan status yang diharapkan. */
    /* Verifikasi hasil update lewat READ-BACK (read-back murni).
       Menggantikan $this->db->affectedRows() yang tidak aman pada driver
       Postgre CI4 ini (bila query gagal, resultID=false sehingga
       pg_affected_rows() melempar TypeError dan error asli tertutup). */
    private function prod_hitungStatus($table, $docno, $inputby, $status)
    {
        $builder = $this->db->table($table)->where('status', $status);

        if ($docno !== null && $docno !== '') {
            $builder->where('docno', trim($docno));
        }

        if ($inputby !== null && $inputby !== '') {
            $builder->where('inputby', $inputby);
        }

        return $builder->countAllResults();
    }

    /* Ambil pesan error terakhir dari koneksi Postgre.
       WAJIB dipanggil SEGERA setelah query yang gagal (sebelum query lain),
       karena setelah transaksi abort, query berikutnya menutupi error asli. */
    private function prod_errorDb()
    {
        try {
            if (!empty($this->db->connID)) {
                return trim((string) pg_last_error($this->db->connID));
            }
        } catch (\Throwable $x) {
            return '';
        }

        return '';
    }
    /* Terjemahkan error database menjadi pesan yang ramah untuk user. */
    private function prod_pesan_db($e)
    {
        $pesan = $e->getMessage();

        if (stripos($pesan, 'NEGATIVE STOCK') !== false) {
            return 'Stok tidak mencukupi. ' . $pesan;
        }

        if (stripos($pesan, 'tidak balance') !== false) {
            return 'Jurnal tidak balance, proses dibatalkan. ' . $pesan;
        }

        if (stripos($pesan, 'duplicate key') !== false) {
            return 'Data sudah ada (duplikat). Silakan muat ulang halaman.';
        }

        if (stripos($pesan, 'foreign key') !== false) {
            return 'Data terkait tidak ditemukan. Periksa kembali pilihan Anda.';
        }

        return 'Proses gagal : ' . $pesan;
    }


    function final_input_materialrelease(){
        $nama = trim($this->session->get('nama'));
        $url_list = base_url('/production/trans/materialrelease');

        // docno dari form (opsional). Docno authoritative tetap diambil dari draft di DB.
        $docno = strtoupper(trim($this->request->getPost('docno')));

        // =====================================
        // AMBIL DRAFT MILIK USER
        // =====================================
        $draft = $this->prod_ambil_draft('sc_tmp.materialrelease_mst', $docno, $nama);

        if ($draft === null) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Draft Material Release tidak ditemukan. Silakan input ulang.')
            );
        }

        $docno = $draft['docno'];

        // =====================================
        // VALIDASI STATUS DRAFT
        // =====================================
        $status = strtoupper(trim($draft['row']['status']));

        if ($status !== 'E') {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('warning', 'Dokumen ' . $docno . ' tidak dapat di-final karena statusnya ' . $status . '.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO (WO sudah A -> terkunci)
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($draft['row']['wono']);

        if ($pesanWo !== '') {
            return redirect()->to($url_list)->with('prod_msg', $this->prod_msg('error', $pesanWo));
        }

        // =====================================
        // VALIDASI DETAIL
        // =====================================
        $stat = $this->m_production->q_tmp_materialrelease_dtl_stat($docno, $nama)->getRowArray();

        if (!$stat || (int) $stat['jml'] <= 0) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Detail Material Release masih kosong. Tidak dapat di-final.')
            );
        }

        if ((int) $stat['qtyinvalid'] > 0) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Masih ada ' . $stat['qtyinvalid'] . ' baris detail dengan QTY 0 / tidak valid.')
            );
        }

        // =====================================
        // UPDATE HEADER + SET STATUS FINAL
        // =====================================
        $keterangan = trim($this->request->getPost('keterangan'));
        $batchno    = strtoupper(trim($this->request->getPost('batchno')));
        $bagian     = strtoupper(trim($this->request->getPost('bagian')));
        $tabno      = strtoupper(trim($this->request->getPost('tabno')));

        $builder = $this->db->table('sc_tmp.materialrelease_mst');

        $this->db->transStart();

        try {
            // update data header dulu (tanpa status)
            $builder->where('docno', $docno);
            $builder->where('inputby', $nama);
            $builder->update([
                'keterangan' => $keterangan,
                'batchno'    => $batchno,
                'bagian'     => $bagian,
                'tabno'      => $tabno,
                'updateby'   => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ]);

            // set FINAL dengan pengaman status (mencegah double final)
            $builder->where('docno', $docno);
            $builder->where('inputby', $nama);
            $builder->where('status', 'E');
            $builder->update([
                'status'     => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby'   => $nama
            ]);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                $this->db->transRollback();

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                );
            }
            $terpengaruh = $this->prod_hitungStatus('sc_trx.materialrelease_mst', $docno, null, 'F');

            if ($terpengaruh <= 0) {
                $this->db->transRollback();

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('warning', 'Dokumen ' . $docno . ' sudah di-final oleh proses lain.')
                );
            }

            $this->db->transComplete();
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', $this->prod_pesan_db($e))
            );
        }

        if ($this->db->transStatus() === false) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Final Material Release gagal diproses.')
            );
        }

        // bersihkan penanda error lama
        $this->db->table('sc_mst.trxerror')
            ->where('userid', $nama)
            ->where('modul', 'I.R.A.8')
            ->delete();

        return redirect()->to($url_list)->with(
            'prod_msg',
            $this->prod_msg('success', 'Material Release ' . $docno . ' berhasil di-final.')
        );
    }

    public function get_materialrelease_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_production->q_tmp_materialrelease_dtl(" and uniqueid='$id'");

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
                ->where('uniqueid', $firstId)
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

            // =========================
            // VALIDASI HEADER : hanya draft (E) yang boleh diubah
            // dan WO belum completed (A)
            // =========================
            $header = $this->m_production->q_tmp_materialrelease_doc($docno, $nama)->getRowArray();

            if (!$header) {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen tidak ditemukan / bukan milik Anda.'
                ]);
            }

            if (strtoupper(trim($header['status'])) !== 'E') {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah di-final, detail tidak dapat diubah.'
                ]);
            }

            $pesanWo = $this->prod_cek_wo_lock($header['wono']);

            if ($pesanWo !== '') {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $pesanWo
                ]);
            }

            $builder
                ->whereIn('uniqueid', $ids)
                ->where('inputby', $nama)
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
                'message' => $this->prod_pesan_db($e)
            ]);
        }
    }

    public function update_materialrelease_()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/materialrelease'));
        }
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
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diubah.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO (WO sudah A -> terkunci)
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($dtl['wono']);

        if ($pesanWo !== '') {

            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'prod_msg',
                $this->prod_msg('error', $pesanWo)
            );
        }

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
            // UPDATE (pengaman status : hanya F/P yang boleh dibuka edit)
            // =====================================
            $this->db
                ->table('sc_trx.materialrelease_mst')
                ->where('docno', trim($docno))
                ->whereIn('status', ['F', 'P'])
                ->update($info);

            // =====================================
            // SUCCESS
            // =====================================
            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                $this->db->transRollback();

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                );
            }
            if ($this->prod_hitungStatus('sc_trx.materialrelease_mst', $docno, null, 'E') > 0) {

                return redirect()->to(
                    base_url('production/trans/add_materialrelease')
                )->with(
                    'prod_msg',
                    $this->prod_msg('success', 'Material Release ' . trim($docno) . ' berhasil dibuka untuk edit.')
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/materialrelease')
            )->with(
                'prod_msg',
                $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.')
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to(
            base_url('production/trans/materialrelease')
        )->with(
            'prod_msg',
            $this->prod_msg('warning', 'Document sedang diproses user lain.')
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
        $kodemenu='I.R.A.8'; $versirelease='I.R.A.8/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
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
        $logindate = trim($this->session->get('logindate'));
        $url_list = base_url('production/trans/materialrelease');

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error', 'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.');
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.')
            );
        }
        // =====================================
        // GET PARAMETER
        // =====================================
        $id = $this->request->getGet('id');

        if (empty($id)) {

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Parameter dokumen tidak lengkap.')
            );
        }

        // =====================================
        // DOCNO
        // =====================================
        if (!ctype_xdigit($id)) {

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Parameter dokumen tidak valid.')
            );
        }

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

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat dicancel.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO (WO sudah A -> terkunci)
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($dtl['wono']);

        if ($pesanWo !== '') {

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', $pesanWo)
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

            try {
// =====================================
// UPDATE MASTER (pengaman status : hanya F/P yang boleh jadi C)
// =====================================
                $this->db
                    ->table('sc_trx.materialrelease_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();

                    return redirect()->to($url_list)->with(
                        'prod_msg',
                        $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                    );
                }
                if ($this->prod_hitungStatus('sc_trx.materialrelease_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();

                    return redirect()->to($url_list)->with(
                        'prod_msg',
                        $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.')
                    );
                }

// =====================================
// UPDATE DETAIL
// =====================================
                $this->db
                    ->table('sc_trx.materialrelease_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

// =====================================
// COMMIT
// =====================================
                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('error', $this->prod_pesan_db($e))
                );
            }

// =====================================
// RESULT
// =====================================
            if ($this->db->transStatus() === false) {

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Gagal cancel document.')
                );
            }

            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('success', 'Material Release ' . trim($docno) . ' berhasil dicancel.')
            );
        }

        // =====================================
        // STATUS INVALID
        // =====================================
        return redirect()->to($url_list)->with(
            'prod_msg',
            $this->prod_msg('warning', 'Document tidak bisa dicancel (status ' . $status . ').')
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
        $kodemenu='I.R.A.9'; $versirelease='I.R.A.9/01'; $releasedate=date('2025-04-12 00:00:00');
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
        /* Flash message dari proses FINAL / CANCEL (Sprint 2) */
        $flash = session()->getFlashdata('prod_msg');
        $data['message'] = $this->prod_msg_html($flash) . $data['message'];

        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_penerimaanbp_mst($param);
        $logindate = trim($this->session->get('logindate'));

        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
            if ($canUpdate  && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_penerimaanbp_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Penerimaan Barang Produksi : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detail_penerimaanbp_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete && trim($lm->inputby) == $nama && $status=='F') {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancel_penerimaanbp_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Penerimaan Barang Produksi : ' . $docno . '\')">

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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-warning';
                    break;

                case 'F':
                    $badgeClass = 'badge-info';
                    break;

                case 'A':
                    $badgeClass = 'badge-success';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;

            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;


            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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
        $kodemenu='I.R.A.9'; $versirelease='I.R.A.9/01'; $releasedate=date('2025-04-12 00:00:00');
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


        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/penerimaanbp'));
        }
        // =================================s

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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_trx_penerimaanbp_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_penerimaanbp()
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

        // mapping nmbranch â†’ kode suffix
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['penerimaanbarangprod']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
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

        // =========================
        // VALIDASI WO (Sprint 2)
        // Hanya WO status FINAL (F) yang boleh ditarik.
        // WO yang sudah COMPLETED (A) tidak boleh ditarik lagi.
        // =========================
        $wono = trim($this->request->getPost('wono'));

        $pesanWo = $this->prod_cek_wo($wono);

        if ($pesanWo !== '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => $pesanWo
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
                // 'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                // 'buildunit'    => trim($this->request->getPost('buildunit')),
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
                    // 'buildfor'     => trim($this->request->getPost('buildfor')),
                    // 'buildunit'    => trim($this->request->getPost('buildunit')),
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

        // =========================
        // BERSIHKAN DETAIL DOKUMEN INI SEBELUM GENERATE ULANG DARI WO
        // (idempoten: tarik ulang / double-click tidak menghasilkan baris ganda)
        // =========================
        $delete = $builderDetail
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->delete();

        if (!$delete) {

            $error = $db->error();

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => $error['message']
            ]);
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
                bm.*,
                COALESCE(NULLIF(TRIM(bm.nmbarang_jadi), ''), TRIM(mb.nmbarang)) AS nmbarang,
                CASE
                    WHEN COALESCE(bm.buildfor, 0) = 0 THEN 0
                    ELSE COALESCE(bm.ttlprice, 0) / bm.buildfor
                END AS harga
            FROM sc_trx.workingorder_bom_mst bm
            LEFT JOIN sc_mst.mbarang mb
                ON TRIM(mb.idbarang) = TRIM(bm.idbarang_jadi)
            WHERE TRIM(bm.docref)=TRIM(?)
        ", [
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

            // uniqueid deterministik -> aman diulang, tidak menghasilkan baris ganda.
            // docno = nomor BOM (komponen PK), jadi aman bila 1 WO punya >1 barang jadi.
            $rawUnique = $docno . '|' . trim($row->idbarang_jadi) . '|MATERIAL|' . trim($row->docno);
            $harga = $row->harga;
            $qty   = $row->buildfor;
            $nilai = $qty * $harga;

            $insert = $builderDetail->insert([

                'docno'=>$docno,
                'docref'=>trim($this->request->getPost('woeno')),
                // Barang JADI tetap ditandai MATERIAL agar tetap ter-posting oleh sync PBP.
                'doctype_detail'=> 'MATERIAL',

                'cabang'=>trim($this->request->getPost('cabang')),
                'idbarang'=>trim($row->idbarang_jadi),
                'nmbarang'=>$row->nmbarang,
                'unit'=>$row->buildunit,
                'qty'=>$row->buildfor,
                'description'=>$row->desc_bom,
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
            WHERE docno = ? and inputby = ?
        ", [$docno, $nama])->getRow();

        $db->table('sc_tmp.penerimaanbp_mst')
            ->where('docno', $docno)
            ->where('inputby', $nama)
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
                // 'buildfor'    => trim($this->request->getPost('buildfor')),
                'batchno'    => trim($this->request->getPost('batchno')),
                // 'buildunit'    => trim($this->request->getPost('buildunit')),
                'idlocation'    => trim($this->request->getPost('idlocation')),
                'nmlocation'    => trim($this->request->getPost('nmlocation')),
                'bagian'    => trim($this->request->getPost('bagian')),
                'pemohon'       => trim($this->request->getPost('pemohon')),
                'tabno'      => trim($this->request->getPost('tabno')),
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

            // =========================
            // UPDATE HEADER (draft) SAAT SIMPAN DETAIL
            // Kolom mengikuti yang di-update di final_input_penerimaanbp
            // supaya keterangan/batchno/bagian/tabno tidak hilang
            // =========================
            $headerUpdate = [
                'keterangan' => strtoupper(trim((string) $this->request->getPost('keterangan'))),
                'batchno'    => trim((string) $this->request->getPost('batchno')),
                'bagian'     => trim((string) $this->request->getPost('bagian')),
                'tabno'      => trim((string) $this->request->getPost('tabno')),
                'updateby'   => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ];

            // idlocation/nmlocation hanya diikutkan bila dikirim
            $postLoc = trim((string) $this->request->getPost('idlocation'));
            if ($postLoc !== '') {
                $headerUpdate['idlocation'] = $postLoc;
                $headerUpdate['nmlocation'] = trim((string) $this->request->getPost('nmlocation'));
            }

            $updateHeader = $db->table('sc_tmp.penerimaanbp_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->update($headerUpdate);

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
        // AMBIL DATA DETAIL
        // =========================
        $uniqueid = $this->request->getPost('uniqueid');
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
            ->where('inputby', $nama)
            ->where('description', $description);

        if ($uniqueid) {
            $builderDuplicate->where('uniqueid !=', $uniqueid);
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
                ->where('uniqueid', $uniqueid)
                ->where('inputby', $nama)
                ->where('docno', $docno)
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
            FROM sc_tmp.penerimaanbp_dtl
            WHERE docno = ? and inputby = ?
        ", [$docno, $nama])->getRow();

        $db->table('sc_tmp.penerimaanbp_mst')
            ->where('docno', $docno)
            ->where('inputby', $nama)
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



    function clear_penerimaanbp_Tmp()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_penerimaanbp_mst($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/penerimaanbp'));
        }
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
            $row[] = $lm->uniqueid;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
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

    /* =========================================================
     * Grid detail Penerimaan Barang Jadi (BP) — JSON
     * Dipakai oleh inline grid ala pmk_brng
     * (assets/pagejs/production/penerimaanbp/penerimaanbp_grid.js).
     * ========================================================= */
    public function list_penerimaanbp_dtl_grid()
    {
        $nama  = trim((string) $this->session->get('nama'));
        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama)
               . " order by idurut asc";

        $list  = $this->m_production->q_tmp_penerimaanbp_dtl($param)->getResultArray();
        $items = array();

        foreach ($list as $lm) {
            $qty   = (float) ($lm['qty'] ?? 0);
            $harga = (float) ($lm['harga'] ?? 0);
            $nilai = (float) ($lm['nilai'] ?? ($qty * $harga));

            $items[] = array(
                'uniqueid'    => trim((string) ($lm['uniqueid'] ?? '')),
                'idurut'      => trim((string) ($lm['idurut'] ?? '')),
                'issub'       => (trim((string) ($lm['issub'] ?? '')) === 'T') ? 'T' : 'F',
                'idbarang'    => trim((string) ($lm['idbarang'] ?? '')),
                'nmbarang'    => trim((string) ($lm['nmbarang'] ?? '')),
                'spec'        => trim((string) ($lm['spec'] ?? '')),
                'qty'         => $qty,
                'unit'        => trim((string) ($lm['unit'] ?? '')),
                'harga'       => $harga,
                'nilai'       => $nilai,
                'description' => trim((string) ($lm['description'] ?? '')),
            );
        }

        return $this->response->setJSON(array(
            'status' => true,
            'total'  => count($items),
            'items'  => $items,
        ));
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
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
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


        // =========================
        // VALIDASI FINAL (Sprint 2)
        // =========================
        $url_list = base_url('/production/trans/penerimaanbp');
        $draft = $this->prod_ambil_draft('sc_tmp.penerimaanbp_mst', '', $nama);

        if ($draft === null) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Draft Penerimaan Barang Produksi tidak ditemukan. Silakan input ulang.')
            );
        }

        $docno = $draft['docno'];

        if (strtoupper(trim($draft['row']['status'])) !== 'E') {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('warning', 'Dokumen ' . $docno . ' tidak dapat di-final karena statusnya ' . strtoupper(trim($draft['row']['status'])) . '.')
            );
        }

        $pesanWo = $this->prod_cek_wo_lock($draft['row']['wono']);

        if ($pesanWo !== '') {
            return redirect()->to($url_list)->with('prod_msg', $this->prod_msg('error', $pesanWo));
        }

        $stat = $this->m_production->q_tmp_penerimaanbp_dtl_stat($docno, $nama)->getRowArray();

        if (!$stat || (int) $stat['jml'] <= 0) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Detail Penerimaan Barang Produksi masih kosong. Tidak dapat di-final.')
            );
        }

        if ((int) $stat['qtyinvalid'] > 0) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Masih ada ' . $stat['qtyinvalid'] . ' baris detail dengan QTY 0 / tidak valid.')
            );
        }
        $builder = $this->db->table('sc_tmp.penerimaanbp_mst');

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

            return redirect()->to(base_url('/production/trans/penerimaanbp'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $batchno = strtoupper(trim($this->request->getPost('batchno')));
            // $idlocation = strtoupper(trim($this->request->getPost('idlocation')));
            // $nmlocation = strtoupper(trim($this->request->getPost('nmlocation')));
            $bagian = strtoupper(trim($this->request->getPost('bagian')));
            $idlocation = strtoupper(trim($this->request->getPost('idlocation')));
            $nmlocation = strtoupper(trim($this->request->getPost('nmlocation')));
            $tabno = strtoupper(trim($this->request->getPost('tabno')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'keterangan'       => $keterangan,
                'batchno'           => $batchno,
                'bagian'           => $bagian,
                'idlocation'           => $idlocation,
                'nmlocation'           => $nmlocation,
                'tabno'           => $tabno,

//                'estpakai' => $estpakaiph,
            ];

            $builder->where('docno', $docno);
            $builder->where('inputby', $nama);
            $builder->where('status', 'E');
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('docno', $docno);
            $builder->where('inputby',$nama);
            $builder->where('status', 'E');
            $builder->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                $this->db->transRollback();

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                );
            }
            if ($this->prod_hitungStatus('sc_trx.penerimaanbp_mst', $docno, null, 'F') > 0) {
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

        $data = $this->m_production->q_tmp_penerimaanbp_dtl(" and uniqueid='$id'");

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
        $nama    = trim(session()->get('nama'));

        // Ambil IDs (bisa array atau single)
        $ids = $request->getPost('ids');

        // Normalisasi: pastikan array
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $ids = array_values(array_filter($ids, function ($id) {
            return is_string($id) && trim($id) !== '';
        }));

        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak valid'
            ]);
        }

        $db->transBegin();

        try {
            // Ambil docno dari detail yang akan dihapus
            $rows = $db->table('sc_tmp.penerimaanbp_dtl')
                ->select('uniqueid, docno')
                ->whereIn('uniqueid', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            // Pastikan seluruh detail berasal dari dokumen yang sama
            $docnos = array_unique(array_column($rows, 'docno'));

            if (count($docnos) !== 1) {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Detail yang dihapus harus berasal dari dokumen yang sama'
                ]);
            }

            $docno = $docnos[0];

            // =========================
            // VALIDASI HEADER : hanya draft (E) yang boleh diubah,
            // dan WO belum completed (A)
            // =========================
            $header = $db->table('sc_tmp.penerimaanbp_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()->getRowArray();

            if (!$header) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen tidak ditemukan / bukan milik Anda.'
                ]);
            }

            if (strtoupper(trim($header['status'])) !== 'E') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah di-final, detail tidak dapat diubah.'
                ]);
            }

            $pesanWo = $this->prod_cek_wo_lock($header['wono']);

            if ($pesanWo !== '') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $pesanWo
                ]);
            }

            // Hapus detail
            $db->table('sc_tmp.penerimaanbp_dtl')
                ->whereIn('uniqueid', $ids)
                ->where('docno', $docno)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data gagal dihapus atau tidak ditemukan'
                ]);
            }

            // Hitung ulang nilai detail yang masih tersisa
            $totals = $db->query("
                SELECT
                    COALESCE(
                        SUM(
                            CASE
                                WHEN TRIM(doctype_detail) = 'MATERIAL'
                                THEN COALESCE(nilai, 0)
                                ELSE 0
                            END
                        ), 0
                    ) AS ttlmaterial,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN TRIM(doctype_detail) = 'COST'
                                THEN COALESCE(nilai, 0)
                                ELSE 0
                            END
                        ), 0
                    ) AS ttlcost,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN TRIM(doctype_detail) = 'WIP'
                                THEN COALESCE(nilai, 0)
                                ELSE 0
                            END
                        ), 0
                    ) AS ttlwip

                FROM sc_tmp.penerimaanbp_dtl
                WHERE docno = ?
            ", [$docno])->getRowArray();

            $ttlmaterial = (float) ($totals['ttlmaterial'] ?? 0);
            $ttlcost    = (float) ($totals['ttlcost'] ?? 0);
            $ttlwip      = (float) ($totals['ttlwip'] ?? 0);

            // Total akhir
            $ttlprice = $ttlmaterial + $ttlcost + $ttlwip;

            // Update header
            $db->table('sc_tmp.penerimaanbp_mst')
                ->where('docno', $docno)
                ->update([
                    'ttlprice'   => $ttlprice,
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
                ]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal memperbarui total header');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data Penerimaan Barang Produksi Detail berhasil dihapus',
                'data'    => [
                    'docno'       => trim($docno),
                    'ttlmaterial' => $ttlmaterial,
                    'ttlcost'    => $ttlcost,
                    'ttlwip'      => $ttlwip,
                    'ttlprice'    => $ttlprice
                ]
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'delete_penerimaanbp_detail: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menghapus detail penerimaan barang'
            ]);
        }
    }


    public function update_penerimaanbp_()
    {
        $nama = trim(session()->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/penerimaanbp'));
        }
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
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diproses.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($dtl['wono']);

        if ($pesanWo !== '') {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'prod_msg',
                $this->prod_msg('error', $pesanWo)
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
            // UPDATE (pengaman status : hanya F/P yang boleh dibuka edit)
            // =====================================
            $this->db
                ->table('sc_trx.penerimaanbp_mst')
                ->where('docno', trim($docno))
                ->whereIn('status', ['F', 'P'])
                ->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {

                return redirect()->to(
                    base_url('production/trans/penerimaanbp')
                )->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                );
            }

            if ($this->prod_hitungStatus('sc_trx.penerimaanbp_mst', $docno, null, 'E') > 0) {

                return redirect()->to(
                    base_url('production/trans/add_penerimaanbp')
                )->with(
                    'prod_msg',
                    $this->prod_msg('success', 'Penerimaan Barang Produksi ' . trim($docno) . ' berhasil dibuka untuk edit.')
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'prod_msg',
                $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.')
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
        $kodemenu='I.R.A.9'; $versirelease='I.R.A.9/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/penerimaanbp'));
        }
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
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diproses.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($dtl['wono']);

        if ($pesanWo !== '') {

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'prod_msg',
                $this->prod_msg('error', $pesanWo)
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

            $this->db->transStart();

            try {
                $this->db
                    ->table('sc_trx.penerimaanbp_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();

                    return redirect()->to(
                        base_url('production/trans/penerimaanbp')
                    )->with(
                        'prod_msg',
                        $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                    );
                }
                if ($this->prod_hitungStatus('sc_trx.penerimaanbp_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();

                    return redirect()->to(
                        base_url('production/trans/penerimaanbp')
                    )->with(
                        'prod_msg',
                        $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.')
                    );
                }

                $this->db
                    ->table('sc_trx.penerimaanbp_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                return redirect()->to(
                    base_url('production/trans/penerimaanbp')
                )->with(
                    'prod_msg',
                    $this->prod_msg('error', $this->prod_pesan_db($e))
                );
            }

            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/penerimaanbp')
                )->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Gagal cancel document.')
                );
            }

            return redirect()->to(
                base_url('production/trans/penerimaanbp')
            )->with(
                'prod_msg',
                $this->prod_msg('success', 'Penerimaan Barang Produksi ' . trim($docno) . ' berhasil dicancel.')
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
        $kodemenu='I.R.A.10'; $versirelease='I.R.A.10/01'; $releasedate=date('2025-04-12 00:00:00');
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
        /* Flash message dari proses FINAL / CANCEL (Sprint 2) */
        $flash = session()->getFlashdata('prod_msg');
        $data['message'] = $this->prod_msg_html($flash) . $data['message'];

        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_production->q_tmp_bpnm_mst($param);
        $logindate = trim($this->session->get('logindate'));

        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
            if ($canUpdate && trim($lm->inputby) == $nama && $status=='F') {

                $updateBtn = '
                <a class="dropdown-item bg-warning"
                   href="' . base_url('production/trans/update_bpnm_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Update Biaya Produksi Non Material : ' . $docno . '\')">

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
                   href="' . base_url('production/trans/detail_bpnm_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">

                    <i class="fa fa-eye"></i> Detail

                </a>';
            }


            // =====================================
            // CANCEL
            // =====================================
            if ($canDelete && trim($lm->inputby) == $nama && $status=='F') {

                $deleteBtn = '
                <a class="dropdown-item"
                   style="background-color:#FF7C7CD6;"
                   href="' . base_url('production/trans/cancel_bpnm_') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                   onclick="return guardPeriodeTutup(event) && confirm(\'Cancel Biaya Produksi Non Material : ' . $docno . '\')">

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
            $badgeClass = 'badge-primary'; // Default

            switch ($status) {

                case 'C':
                    $badgeClass = 'badge-danger';
                    break;

                case 'E':
                    $badgeClass = 'badge-warning';
                    break;

                case 'F':
                    $badgeClass = 'badge-info';
                    break;

                case 'A':
                    $badgeClass = 'badge-success';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }

            $statusLabel = isset($lm->nmstatus)
                ? $lm->nmstatus
                : $status;

            // =====================================
            // ROW DATA
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->idbarang_jadi;

            $row[] = $lm->nmbarang;

            $row[] = $lm->buildfor;
            $row[] = $lm->buildunit;


            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;

            $row[] = $lm->inputby;
            $row[] = $this->prod_fmt_inputdate($lm->inputdate);
            $row[] = $this->prod_fmt_inputdate($lm->updatedate);

            $row[] = '<div class="text-center">'
                . '<span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($statusLabel)
                . '</span>'
                . '</div>';

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
        $kodemenu='I.R.A.10'; $versirelease='I.R.A.10/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/bpnm'));
        }
        // =================================s
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
        $param = " and docno=" . $this->db->escape($docno) . " and inputby='$nama'";
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
        $param = " and docno=" . $this->db->escape($docno);
        $data = $this->m_production->q_trx_bpnm_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function getBranchInfo_bpnm()
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

        // mapping nmbranch â†’ kode suffix
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['bpnm']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
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

        // =========================
        // VALIDASI WO (Sprint 2)
        // Hanya WO status FINAL (F) yang boleh ditarik.
        // WO yang sudah COMPLETED (A) tidak boleh ditarik lagi.
        // =========================
        $wono = trim($this->request->getPost('wono'));

        $pesanWo = $this->prod_cek_wo($wono);

        if ($pesanWo !== '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => $pesanWo
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

        // =========================
        // BERSIHKAN DETAIL DOKUMEN INI SEBELUM GENERATE ULANG DARI WO
        // (idempoten: tarik ulang / double-click tidak menghasilkan baris ganda)
        // =========================
        $delete = $builderDetail
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->delete();

        if (!$delete) {

            $error = $db->error();

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => $error['message']
            ]);
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

            // uniqueid deterministik -> aman diulang, tidak menghasilkan baris ganda
            $rawUnique = $docno . '|' . trim($row->idbarang) . '|' . trim($row->doctype_detail) . '|' . $row->idurut;
            $harga = $row->standartcost;
            $qty   = $row->qty;
            $nilai = $qty * $harga;

            $insert = $builderDetail->insert([

                'docno'=>$docno,
                // Propagasi doctype_detail dari baris WO BOM (pola sama seperti Material Release).
                // Tanpa ini kolom jatuh ke DEFAULT 'MATERIAL', sedangkan filter sync BPNM
                // adalah doctype_detail <> 'MATERIAL' -> dokumen FINAL tapi TIDAK posting.
                'doctype_detail'=> $row->doctype_detail,

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
            WHERE docno = ? and inputby = ?
        ", [$docno, $nama])->getRow();

        $db->table('sc_tmp.bpnm_mst')
            ->where('docno', $docno)
            ->where('inputby', $nama)
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
                'tabno'      => trim($this->request->getPost('tabno')),
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

            // =========================
            // UPDATE HEADER (draft) SAAT SIMPAN DETAIL
            // Kolom mengikuti yang di-update di final_input_bpnm
            // supaya keterangan/batchno/bagian/tabno tidak hilang
            // =========================
            $updateHeader = $db->table('sc_tmp.bpnm_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->update([
                    'keterangan' => strtoupper(trim((string) $this->request->getPost('keterangan'))),
                    'batchno'    => trim((string) $this->request->getPost('batchno')),
                    'bagian'     => trim((string) $this->request->getPost('bagian')),
                    'tabno'      => trim((string) $this->request->getPost('tabno')),
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
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
        // AMBIL DATA DETAIL
        // =========================
        $uniqueid = $this->request->getPost('uniqueid');
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
            ->where('inputby', $nama)
            ->where('description', $description);

        if ($uniqueid) {
            $builderDuplicate->where('uniqueid !=', $uniqueid);
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
                ->where('docno', $docno)
                ->where('uniqueid', $uniqueid)
                ->where('inputby', $nama)
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
                // BPNM = biaya produksi NON MATERIAL. Nilai default kolom 'MATERIAL'
                // membuat baris tidak pernah diposting (filter sync: doctype_detail <> 'MATERIAL').
                // Samakan dengan jalur tarik WO (baris BOM COST) -> 'COST'.
                'doctype_detail' => 'COST',

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
            WHERE docno = ? and inputby = ?
        ", [$docno, $nama])->getRow();

        $db->table('sc_tmp.bpnm_mst')
            ->where('docno', $docno)
            ->where('inputby', $nama)
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('production/trans/biaya_produksi_non_material'));
        }
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
            $row[] = $lm->uniqueid;
            //item
            $row[] = '
            <div class="text-center">
                <input type="checkbox" disabled ' . (trim($lm->issub) === 'T' ? 'checked' : '') . '>
            </div>
            ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->spec;
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
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

    /* =========================================================
     * Grid detail BPNM (JSON, tanpa protokol DataTables)
     * Dipakai oleh inline grid ala pmk_brng
     * (assets/pagejs/production/bpnm/bpnm_grid.js).
     * ========================================================= */
    public function list_bpnm_dtl_grid()
    {
        $nama  = trim((string) $this->session->get('nama'));
        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama)
               . " order by idurut asc";

        $list  = $this->m_production->q_tmp_bpnm_dtl($param)->getResultArray();
        $items = array();

        foreach ($list as $lm) {
            $qty   = (float) ($lm['qty'] ?? 0);
            $harga = (float) ($lm['harga'] ?? 0);
            $nilai = (float) ($lm['nilai'] ?? ($qty * $harga));

            $items[] = array(
                'uniqueid'    => trim((string) ($lm['uniqueid'] ?? '')),
                'idurut'      => trim((string) ($lm['idurut'] ?? '')),
                'issub'       => (trim((string) ($lm['issub'] ?? '')) === 'T') ? 'T' : 'F',
                'idbarang'    => trim((string) ($lm['idbarang'] ?? '')),
                'nmbarang'    => trim((string) ($lm['nmbarang'] ?? '')),
                'spec'        => trim((string) ($lm['spec'] ?? '')),
                'qty'         => $qty,
                'unit'        => trim((string) ($lm['unit'] ?? '')),
                'harga'       => $harga,
                'nilai'       => $nilai,
                'description' => trim((string) ($lm['description'] ?? '')),
            );
        }

        return $this->response->setJSON(array(
            'status' => true,
            'total'  => count($items),
            'items'  => $items,
        ));
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
            $row[] = '<div class="ratakanan" data-export="'.$lm->qty.'">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan" data-export="'.$lm->harga.'">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan" data-export="'.$lm->nilai.'">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
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


        // =========================
        // VALIDASI FINAL (Sprint 2)
        // =========================
        $url_list = base_url('/production/trans/biaya_produksi_non_material');
        $draft = $this->prod_ambil_draft('sc_tmp.bpnm_mst', '', $nama);

        if ($draft === null) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Draft Biaya Produksi Non Material tidak ditemukan. Silakan input ulang.')
            );
        }

        $docno = $draft['docno'];

        if (strtoupper(trim($draft['row']['status'])) !== 'E') {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('warning', 'Dokumen ' . $docno . ' tidak dapat di-final karena statusnya ' . strtoupper(trim($draft['row']['status'])) . '.')
            );
        }

        $pesanWo = $this->prod_cek_wo_lock($draft['row']['wono']);

        if ($pesanWo !== '') {
            return redirect()->to($url_list)->with('prod_msg', $this->prod_msg('error', $pesanWo));
        }

        $stat = $this->m_production->q_tmp_bpnm_dtl_stat($docno, $nama)->getRowArray();

        if (!$stat || (int) $stat['jml'] <= 0) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Detail Biaya Produksi Non Material masih kosong. Tidak dapat di-final.')
            );
        }

        if ((int) $stat['qtyinvalid'] > 0) {
            return redirect()->to($url_list)->with(
                'prod_msg',
                $this->prod_msg('error', 'Masih ada ' . $stat['qtyinvalid'] . ' baris detail dengan QTY 0 / tidak valid.')
            );
        }
        $builder = $this->db->table('sc_tmp.bpnm_mst');

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

            return redirect()->to(base_url('/production/trans/biaya_produksi_non_material'));
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

            $builder->where('docno', $docno);
            $builder->where('inputby', $nama);
            $builder->where('status', 'E');
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('docno', $docno);
            $builder->where('inputby',$nama);
            $builder->where('status', 'E');
            $builder->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {
                $this->db->transRollback();

                return redirect()->to($url_list)->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                );
            }
            if ($this->prod_hitungStatus('sc_trx.bpnm_mst', $docno, null, 'F') > 0) {
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

        $data = $this->m_production->q_tmp_bpnm_dtl(" and uniqueid='$id'");

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
                ->where('uniqueid', $firstId)
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

            // =========================
            // VALIDASI HEADER : hanya draft (E) yang boleh diubah
            // =========================
            $header = $db->table('sc_tmp.bpnm_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()->getRowArray();

            if (!$header) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen tidak ditemukan / bukan milik Anda.'
                ]);
            }

            if (strtoupper(trim($header['status'])) !== 'E') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah di-final, detail tidak dapat diubah.'
                ]);
            }

            $pesanWo = $this->prod_cek_wo_lock($header['wono']);

            if ($pesanWo !== '') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $pesanWo
                ]);
            }

            $builder
                ->whereIn('uniqueid', $ids)
                ->where('inputby', $nama)
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/bpnm'));
        }
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
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diproses.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($dtl['wono']);

        if ($pesanWo !== '') {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'prod_msg',
                $this->prod_msg('error', $pesanWo)
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
            // UPDATE (pengaman status : hanya F/P yang boleh dibuka edit)
            // =====================================
            $this->db
                ->table('sc_trx.bpnm_mst')
                ->where('docno', trim($docno))
                ->whereIn('status', ['F', 'P'])
                ->update($info);

            $errDb = $this->prod_errorDb();

            if ($errDb !== '') {

                return redirect()->to(
                    base_url('production/trans/biaya_produksi_non_material')
                )->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                );
            }

            if ($this->prod_hitungStatus('sc_trx.bpnm_mst', $docno, null, 'E') > 0) {

                return redirect()->to(
                    base_url('production/trans/add_bpnm')
                )->with(
                    'prod_msg',
                    $this->prod_msg('success', 'Biaya Produksi Non Material ' . trim($docno) . ' berhasil dibuka untuk edit.')
                );
            }

            // =====================================
            // FAILED
            // =====================================
            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'prod_msg',
                $this->prod_msg('warning', 'Dokumen berubah oleh proses lain. Silakan muat ulang.')
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
        $kodemenu='I.R.A.10'; $versirelease='I.R.A.10/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $param = " and coalesce(docno,'') = " . $this->db->escape($decoded_docno);
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_production->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('production/trans/bpnm'));
        }
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
                'prod_msg',
                $this->prod_msg('error', 'Document tidak ditemukan.')
            );
        }

        // =====================================
        // VALIDASI PEMILIK DOKUMEN
        // =====================================
        if (trim($dtl['inputby']) !== $nama) {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'prod_msg',
                $this->prod_msg('error', 'Dokumen ini bukan milik Anda, tidak dapat diproses.')
            );
        }

        // =====================================
        // VALIDASI KUNCI WO
        // =====================================
        $pesanWo = $this->prod_cek_wo_lock($dtl['wono']);

        if ($pesanWo !== '') {

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'prod_msg',
                $this->prod_msg('error', $pesanWo)
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

            $this->db->transStart();

            try {
                $this->db
                    ->table('sc_trx.bpnm_mst')
                    ->where('docno', trim($docno))
                    ->whereIn('status', ['F', 'P'])
                    ->update($info);

                $errDb = $this->prod_errorDb();

                if ($errDb !== '') {
                    $this->db->transRollback();

                    return redirect()->to(
                        base_url('production/trans/biaya_produksi_non_material')
                    )->with(
                        'prod_msg',
                        $this->prod_msg('error', 'Proses gagal : ' . $errDb)
                    );
                }
                if ($this->prod_hitungStatus('sc_trx.bpnm_mst', $docno, null, 'C') <= 0) {
                    $this->db->transRollback();

                    return redirect()->to(
                        base_url('production/trans/biaya_produksi_non_material')
                    )->with(
                        'prod_msg',
                        $this->prod_msg('warning', 'Dokumen sudah dicancel / berubah oleh proses lain.')
                    );
                }

                $this->db
                    ->table('sc_trx.bpnm_dtl')
                    ->where('docno', trim($docno))
                    ->update($info);

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                return redirect()->to(
                    base_url('production/trans/biaya_produksi_non_material')
                )->with(
                    'prod_msg',
                    $this->prod_msg('error', $this->prod_pesan_db($e))
                );
            }

            if ($this->db->transStatus() === false) {

                return redirect()->to(
                    base_url('production/trans/biaya_produksi_non_material')
                )->with(
                    'prod_msg',
                    $this->prod_msg('error', 'Gagal cancel document.')
                );
            }

            return redirect()->to(
                base_url('production/trans/biaya_produksi_non_material')
            )->with(
                'prod_msg',
                $this->prod_msg('success', 'Biaya Produksi Non Material ' . trim($docno) . ' berhasil dicancel.')
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


    /* ============================================================
       LAPORAN JURNAL TRANSAKSI (detail Material Release / PBP / BPNM)
       Dipanggil AJAX oleh tombol "Laporan Jurnal Transaksi" di halaman detail.
       Mengembalikan baris jurnal (jurnal_hd + jurnal_dt) yang sudah POSTED.
       ============================================================ */
    public function laporan_jurnal_transaksi()
    {
        try {
            $docno = trim($this->request->getPost('docno'));

            if ($docno === '') {
                return $this->response->setJSON([
                    'status'   => false,
                    'messages' => 'Doc No tidak ditemukan',
                    'data'     => []
                ]);
            }

            $params = "AND TRIM(jh.docno) = " . $this->db->escape($docno);

            $query = $this->m_production->q_laporan_jurnal_transaksi($params);

            return $this->response->setJSON([
                'status'   => true,
                'messages' => 'Data berhasil diambil',
                'data'     => $query->getResultArray()
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'laporan_jurnal_transaksi: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'   => false,
                'messages' => $e->getMessage(),
                'data'     => []
            ]);
        }
    }
}
