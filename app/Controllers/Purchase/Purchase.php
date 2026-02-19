<?php


namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

class Purchase extends BaseController
{
    
    public function pp()
    {
        $data['title']="Permintaan Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.1'; $versirelease='I.P.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.1'";
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
        $dtl = $this->m_purchase->q_pp_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('purchase/trans/clearEntryPP');
            $urlnext = base_url('purchase/trans/addPP');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/pp/v_list_pp',$data);
    }

    function detailPP()
    {
        /* Penambahan Squence */
        $data['title']="Detail Permintaan Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/pp'));
        }
        $kodemenu='I.P.A.1'; $versirelease='I.P.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.1'";
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
        $data['dtldata'] = $this->m_purchase->q_pp_master($param)->getRowArray();
        return $this->template->render('purchase/pp/v_detail_pp',$data);
    }

    function list_pp(){
        $list = $this->m_purchase->get_t_front_pp_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.1';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint  = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView   = isset($datadtl['dtl_akses']['a_view'])   && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput  = isset($datadtl['dtl_akses']['a_input'])  && trim($datadtl['dtl_akses']['a_input']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status    = strtoupper(trim($lm->status_desc));
            $docno     = trim($lm->docno);
            $docnoHex  = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) && 
                trim($status) !== 'DITARIK PO'
            ) {

                $updateBtn = '
                    <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updatePP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This PP : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update Permintaan Pembelian 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailPP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail PP : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail Permintaan Pembelian 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_pp') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print PP : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Print Permintaan Pembelian 
                    </a>';
            }

            // =========================
            // RULE STATUS
            // =========================
            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
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
            $row[] = $lm->docdate;
            $row[] = $lm->pemohon;
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
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
                case 'DITARIK PO':
                    $badgeClass = 'badge-success';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-primary ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_pp_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_pp_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryPP()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_pp_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('purchase/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.pp');
        $builder_dtl = $this->db->table('sc_tmp.pp_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('purchase/trans/pp'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/pp'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/pp'));
        }

    }

    function addPP()
    {
        /* Penambahan Squence */
        $data['title']="Input Permintaan Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.1'; $versirelease='I.P.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.1'";
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
        $data['mst'] = $this->m_purchase->q_pp_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_pp_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/pp/v_add_pp',$data);
    }

    function showing_cc($id){
        $data = $this->m_manufacture->q_t_CC(" and idchemicalcomposition='$id'")->getRow();
        echo json_encode($data);
    }


   public function getBranchInfo()
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

    public function getNextSuffixPP()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.pp')
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

    public function initPPHeader()
    {
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper($this->request->getPost('docno'));
        $docdate    = $this->request->getPost('docdate');
        $cabang     = $this->request->getPost('cabang');
        $pemohon    = strtoupper($this->request->getPost('pemohon'));
        $estpakai   = $this->request->getPost('estpakai');
        $keterangan = strtoupper($this->request->getPost('keterangan'));

        if (!$docno || !$docdate || !$cabang) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Header belum lengkap'
            ]);
        }

        $builder = $this->db->table('sc_tmp.pp');
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
            'estpakai'   => $estpakai,
            'status'     => 'E',
            'keterangan' => $keterangan,
            'inputby'    => $nama,
            'inputdate'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'reload'  => true   // ⬅ PENTING
        ]);
    }



    public function savePPDetail()
    {
        $nama=trim($this->session->get('nama'));    
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut     = $this->request->getPost('idurut'); // 🔹 untuk update

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.pp');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

            $builderHeader->insert([
                'docno'      => $docno,
                'docdate'    => $this->request->getPost('docdate'),
                'cabang'     => $this->request->getPost('cabang'),
                'pemohon'    => strtoupper($this->request->getPost('pemohon')),
                'estpakai'   => $this->request->getPost('estpakai'),
                'status'     => 'E',
                'keterangan' => strtoupper($this->request->getPost('keterangan')),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================
        $idbarang    = $this->request->getPost('idbarang');
        $nmbarang    = strtoupper($this->request->getPost('nmbarang'));
        $unit        = strtoupper($this->request->getPost('unit'));
        $qty         = $this->request->getPost('qty');
        $description = strtoupper($this->request->getPost('description'));

        $builderDetail = $db->table('sc_tmp.pp_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('qty', $qty)
            ->where('description', $description);

        // jika mode update → jangan bandingkan dengan dirinya sendiri
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

            // 🔹 UPDATE
            $builderDetail->where('idurut', $idurut)->update([
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'qty'         => $qty,
                'description' => $description,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);

        } else {

            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $nmbarang 
            . '|' . $docno 
            . '|' . $description 
            . '|' . $inputdate;

            $uniqueid  = hash('sha256', $rawUnique);


            // 🔹 INSERT
            $builderDetail->insert([
                'docno'       => $docno,
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'qty'         => $qty,
                'description' => $description,
                'inputby'     => $nama,
                'inputdate'   => date('Y-m-d H:i:s'),
                'uniqueid'    => $uniqueid
            ]);
        }

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }




    function updatePP()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_pp_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.pp');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addPP'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/pp'));
        }
    }

    function showing_pptrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_pp_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_pptemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_pp_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_pp_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_pp_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_pp_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.pp_dtl')
            ->where('idurut', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $row
        ]);
    }

    public function delete_pp_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.pp_dtl');

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

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data Tax Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_pp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_pp_dtl_temp_view($docno);
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
            $row[] = '<div class="ratakanan">'. $lm->qty  . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_pp_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_pp_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_pp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_pp_dtl_view($docno);
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
            $row[] = $lm->qty;
            $row[] = $lm->description;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_pp_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_pp_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryPP(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR COALESCE(description, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_purchase->q_pp_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_purchase->q_pp_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_pp_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.pp');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.1');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.1',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/purchase/trans/addPP'));
        } else {
            // Ambil dari request POST
            $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $estpakai  = trim($this->request->getPost('estpakai'));
            $docdate   = trim($this->request->getPost('docdate'));
            // Convert expdate ke format YYYY-MM-DD
            $estpakaiph = null;
            if (!empty($estpakai)) {
                $estpakaiph = date('Y-m-d', strtotime(str_replace('-', '/', $estpakai)));
            }

             // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'      => $docdateph,
                'pemohon'       => $pemohon,
                'keterangan'        => $keterangan,
                'estpakai' => $estpakaiph,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.P.A.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/purchase/trans/pp'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.P.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/purchase/trans/addPP'));
            }



        }

    }


    function show_pp(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.pp');

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

        $title = " Report Permintaan Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_pp/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_pp.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_pp(){
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
        $datamst = $this->m_purchase->q_pp_master($param);
        $datadtl = $this->m_purchase->q_pp_dtl($param);
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










    // ============================== VOID PP ==========================================
     public function voidpp()
    {
        $data['title']="Void PP";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.2'; $versirelease='I.P.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.2'";
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
        $dtl = $this->m_purchase->q_voidpp_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('purchase/trans/clearEntryVoidPP');
            $urlnext = base_url('purchase/trans/addVoidPP');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/voidpp/v_list_voidpp',$data);
    }

    function detailVoidPP()
    {
        /* Penambahan Squence */
        $data['title']="Detail Void PP";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/voidpp'));
        }
        $kodemenu='I.P.A.2'; $versirelease='I.P.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.2'";
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
        $data['dtldata'] = $this->m_purchase->q_voidpp_master($param)->getRowArray();
        return $this->template->render('purchase/voidpp/v_detail_voidpp',$data);
    }

    function list_voidpp(){
        $list = $this->m_purchase->get_t_front_voidpp_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama) {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updateVoidPP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Void PP : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Void Permintaan Pembelian 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailVoidPP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Void PP : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Void Permintaan Pembelian 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_voidpp') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Void PP : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Void Permintaan Pembelian 
                </a>';
            }


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
            $row[] = $lm->docdate;
            $row[] = $lm->pemohon;
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
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


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_voidpp_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_voidpp_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryVoidPP()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_voidpp_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('purchase/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.voidpp');
        $builder_dtl = $this->db->table('sc_tmp.voidpp_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('purchase/trans/voidpp'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/voidpp'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/voidpp'));
        }

    }

    function addVoidPP()
    {
        /* Penambahan Squence */
        $data['title']="Input Void PP";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.2'; $versirelease='I.P.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.2'";
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
        $data['mst'] = $this->m_purchase->q_voidpp_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_voidpp_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/voidpp/v_add_voidpp',$data);
    }


   public function getBranchInfoVoid()
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
            'PT JATIM TAMAN STEEL MFG' => '00',
            'PLANT I'                 => '00',
            'PLANT II'                => '00',
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

    public function getNextSuffixVoidPP()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.voidpp')
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

    public function initVoidPPHeader()
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

        $builder = $this->db->table('sc_tmp.voidpp');
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



    public function saveVoidPPDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $docnopp = strtoupper(trim($this->request->getPost('docnopp')));

        if (!$docno || !$docnopp) {
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
        $builderHeader = $db->table('sc_tmp.voidpp');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        if ($exists == 0) {
            $builderHeader->insert([
                'docno'     => $docno,
                'docdate'   => date('Y-m-d'),
                'cabang'     => $this->request->getPost('cabang'),
                'pemohon'    => strtoupper($this->request->getPost('pemohon')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        // =====================================================
        // AMBIL DATA DARI sc_trx.pp_dtl
        // =====================================================
        $ppDetails = $db->query("
            SELECT 
                docno,
                idbarang,
                uniqueid,
                nmbarang,
                unit,
                qty,
                description
            FROM sc_trx.pp_dtl
            WHERE TRIM(docno) = ?
        ", [$docnopp])->getResult();

        if (empty($ppDetails)) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data PP tidak ditemukan'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.voidpp_dtl');

        $insertCount = 0;

        foreach ($ppDetails as $row) {

            // =====================================================
            // CEK APAKAH ITEM SUDAH ADA DI TMP
            // =====================================================
            // $duplicate = $builderDetail
            //     ->where('docno', $docno)
            //     ->where('docnopp', $docnopp)
            //     ->where('idbarang', $row->idbarang)
            //     ->where('inputby', $nama)
            //     ->countAllResults();

            $duplicate = $builderDetail
                ->where('uniqueid', $row->uniqueid)
                ->countAllResults();


            if ($duplicate == 0) {

                $builderDetail->insert([
                    'docno'       => $docno,
                    'docnopp'     => $docnopp,
                    'idbarang'    => $row->idbarang,
                    'nmbarang'    => $row->nmbarang,
                    'uniqueid'    => $row->uniqueid,
                    'unit'        => $row->unit,
                    'qty'         => $row->qty,
                    'description' => $row->description,
                    'inputby'     => $nama,
                    'inputdate'   => date('Y-m-d H:i:s')
                ]);

                $insertCount++;
            }
        }

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $insertCount > 0 
                            ? "$insertCount item berhasil ditambahkan"
                            : "Semua item sudah ada sebelumnya"
        ]);
    }





    function updateVoidPP()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_voidpp_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.voidpp');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addVoidPP'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/voidpp'));
        }
    }

    function showing_voidpptrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_voidpp_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_voidpptemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_voidpp_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_voidpp_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_voidpp_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_voidpp_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.voidpp_dtl')
            ->where('idurut', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $row
        ]);
    }

    public function delete_voidpp_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.voidpp_dtl');

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

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data Void PP Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_voidpp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_voidpp_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopp;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. $lm->qty  . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_voidpp_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_voidpp_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_voidpp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_voidpp_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopp;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = $lm->qty;
            $row[] = $lm->description;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_voidpp_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_voidpp_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryVoidPP(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR COALESCE(description, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_purchase->q_voidpp_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_purchase->q_voidpp_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_voidpp_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.voidpp');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.2');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/purchase/trans/addVoidPP'));
        } else {
            // Ambil dari request POST
            $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            // Convert expdate ke format YYYY-MM-DD

             // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'      => $docdateph,
                'pemohon'       => $pemohon
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.P.A.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/purchase/trans/voidpp'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.P.A.2',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/purchase/trans/addVoidPP'));
            }



        }

    }


    function show_voidpp(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.voidpp');

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
        $datajson =  base_url("purchase/trans/api_voidpp/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_voidpp.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_voidpp(){
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
        $datamst = $this->m_purchase->q_voidpp_master($param);
        $datadtl = $this->m_purchase->q_voidpp_dtl($param);
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









    // =================================== PO ===========================================


     public function po()
    {
        $data['title']="Purchase Order (PO)";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.3'; $versirelease='I.P.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.3'";
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
        $dtl = $this->m_purchase->q_po_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('purchase/trans/clearEntryPO');
            $urlnext = base_url('purchase/trans/addPO');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/po/v_list_po',$data);
    }

    function detailPO()
    {
        /* Penambahan Squence */
        $data['title']="Detail PO";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/po'));
        }
        $kodemenu='I.P.A.3'; $versirelease='I.P.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.3'";
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
        $data['dtldata'] = $this->m_purchase->q_po_master($param)->getRowArray();
        return $this->template->render('purchase/po/v_detail_po',$data);
    }

    function list_po(){
        $list = $this->m_purchase->get_t_front_po_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.3';
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
                    href="' . base_url('purchase/trans/updatePO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This PO : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update PO 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailPO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail PO : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail PO 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print PO : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print PO 
                </a>';
            }


            if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                    $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }


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

            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->senddate))
            );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d/m/Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;

            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_po_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_po_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    
    function list_po_apprv(){
        $list = $this->m_purchase->get_t_front_po_apprv_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.3';
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
                    href="' . base_url('purchase/trans/updatePO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This PO : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update PO 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailPO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail PO : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail PO 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print PO : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print PO 
                </a>';
            }


            if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                    $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }


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
            
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;
            
            if (!empty($docdate)) {
                
                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");
                
                $jatuhTempo = $date->format('d/m/Y');
                
                } else {
                    $jatuhTempo = '';
                    }
                    
            $row[] = $jatuhTempo;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->senddate))
            );
            $row[] = $lm->keterangan;
            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
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
            // $row[] = $lm->alamatsupplier;
            // $row[] = $lm->nmkota;

            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_po_apprv_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_po_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryPO()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_po_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('purchase/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.po');
        $builder_dtl = $this->db->table('sc_tmp.po_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('purchase/trans/po'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/po'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/po'));
        }

    }

    function addPO()
    {
        /* Penambahan Squence */
        $data['title']="Input PO";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.3'; $versirelease='I.P.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.3'";
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
        $data['mst'] = $this->m_purchase->q_po_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_po_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/po/v_add_po',$data);
    }


   public function getBranchInfoPO()
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

    public function getNextSuffixPO()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.po')
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

    public function initPOHeader()
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

        $builder = $this->db->table('sc_tmp.po');
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



    public function savePODetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $docnopp = strtoupper(trim($this->request->getPost('docnopp')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya
        
        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno || !$docnopp) {
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
        $builderHeader = $db->table('sc_tmp.po');

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
                'senddate'   => date('Y-m-d', strtotime(trim($this->request->getPost('senddate')))),
                'jthtempo'     => $this->request->getPost('jthtempo'),
                'isinclusive'     => $isinclusive,
                
                'kdsupplier'    => strtoupper($this->request->getPost('kdsupplier')),
                'alamatsupplier'    => strtoupper($this->request->getPost('alamatsupplier')),
                'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                'idtax'    => strtoupper($this->request->getPost('idtax')),
                'currcode'    => strtoupper($this->request->getPost('currcode')),
                'kurs'    => strtoupper($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.po_dtl');
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
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $descriptionpo = strtoupper($this->request->getPost('descriptionpo'));

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                'qtybonus'     => $qtybonus,
                'harga'        => $harga,
                'multidisc'    => $multidisc,
                'nilai'        => $nilai,
                'descriptionpo' => $descriptionpo,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);



            $poHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
            $idtax = $poHeader['idtax'] ?? '';
            
            // Hitung total DPP (sum nilai dari po_dtl)
            $builderTotalDpp = $db->table('sc_tmp.po_dtl');
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
            
            // Update header PO
            $builderHeader->where('docno', $docno)->update([
                'dpp' => number_format($dpp, 2, '.', ''),
                'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                'total' => number_format($total, 2, '.', ''),
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ]);
            
            $message = 'Data berhasil diupdate';
            
        } else {
            // =====================================================
            // MODE ADD - INSERT DATA DARI PP
            // =====================================================
            $ppDetails = $db->query("
                SELECT 
                    docno,
                    idbarang,
                    uniqueid,
                    nmbarang,
                    unit,
                    qty,
                    description
                FROM sc_trx.pp_dtl
                WHERE TRIM(docno) = ?
            ", [$docnopp])->getResult();

            if (empty($ppDetails)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PP tidak ditemukan'
                ]);
            }

            foreach ($ppDetails as $row) {
                // CEK APAKAH ITEM SUDAH ADA DI TMP
                // $duplicate = $builderDetail
                //     ->where('docno', $docno)
                //     ->where('docnopp', $docnopp)
                //     ->where('idbarang', $row->idbarang)
                //     ->where('inputby', $nama)
                //     ->countAllResults();

                $duplicate = $builderDetail
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnopp'       => $docnopp,
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $row->qty,
                        'qtybonus'      => 0, // Default 0 untuk new insert
                        'harga'         => 0, // Default 0 untuk new insert
                        'multidisc'     => 0, // Default 0 untuk new insert
                        'nilai'         => 0, // Default 0 untuk new insert
                        'descriptionpp' => $row->description,
                        'descriptionpo' => $row->description,
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

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusPO()
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
        $builder = $db->table('sc_trx.po');
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



    function updatePO()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_po_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.po');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addPO'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/po'));
        }
    }

    function showing_potrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_po_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_potemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_po_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_po_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_po_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_po_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.po_dtl')
            ->where('idurut', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $row
        ]);
    }

    public function delete_po_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.po_dtl');

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

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data PO Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_po_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_po_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopp;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '% </div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            $row[] = $lm->descriptionpo;
            $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_po_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_po_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_po_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_po_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopp;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            $row[] = $lm->descriptionpo;
            $row[] = $lm->descriptionpp;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_po_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_po_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryPO(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR COALESCE(descriptionpo, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_purchase->q_po_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_purchase->q_po_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_po_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.po');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.3');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/purchase/trans/addPO'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdsupplier   = trim($this->request->getPost('kdsupplier'));
            $alamatsupplier   = trim($this->request->getPost('alamatsupplier'));
            $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $currcode   = trim($this->request->getPost('currcode'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            $idtax   = trim($this->request->getPost('idtax'));
            $syarat   = trim($this->request->getPost('syarat'));
            $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';


            
            // **BERSIHKAN FORMAT KURS**
            $kurs = trim($this->request->getPost('kurs'));
            $kurs_clean = 0;
            if (!empty($kurs)) {
                $kurs_clean = str_replace(',', '', $kurs);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }

             // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            $senddateph = null;
            if (!empty($senddate)) {
                $senddateph = date('Y-m-d', strtotime(str_replace('-', '/', $senddate)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                'senddate'       => $senddateph,
                'jthtempo'       => $jthtempo,
                'kdsupplier'     => strtoupper($kdsupplier),
                'alamatsupplier' => strtoupper($alamatsupplier),
                'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                'currcode'       => $currcode,
                'kurs'           => $kurs_clean,
                'isinclusive'    => strtoupper($isinclusive),
                'idtax'          => strtoupper($idtax),
                'syarat'         => strtoupper($syarat)
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.P.A.3'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/purchase/trans/po'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.P.A.3',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/purchase/trans/addPO'));
            }



        }

    }


    function show_po(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.po');

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
        $datajson =  base_url("purchase/trans/api_po/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_po.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_po(){
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
        $datamst = $this->m_purchase->q_po_master($param);
        $datadtl = $this->m_purchase->q_po_dtl($param);
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

}