<?php
/* Finance.php */

namespace App\Controllers\Finance;

use App\Controllers\BaseController;

class Finance extends BaseController
{

    // =================================== PO ===========================================


    public function jup()
    {
        $data['title']="Jurnal Umum Perkiraan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.A.1'; $versirelease='I.K.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.A.1'";
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

        // Pesan gagal/sukses dari proses update JUP (tombol Update di daftar JUP)
        $flashUpdate = $this->session->getFlashdata('jup_update_error');
        if (!empty($flashUpdate)) {
            $data['message'] .= "<div class='alert alert-danger'>" . esc($flashUpdate) . "</div>";
        }

        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_finance->q_jup_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('ka/accounting/clearEntryJUP');
            $urlnext = base_url('ka/accounting/addJUP');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.K.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/accounting/v_list_jup',$data);
    }

    function detailJUP()
    {
        /* Penambahan Squence */
        $data['title']="Detail Jurnal Umum Perkiraan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('ka/accounting/jup'));
        }
        $kodemenu='I.K.A.1'; $versirelease='I.K.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.A.1'";
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
        $data['dtldata'] = $this->m_finance->q_jup_master($param)->getRowArray();
        return $this->template->render('finance/accounting/v_detail_jup',$data);
    }

    function list_jup(){
        $list = $this->m_finance->get_t_front_jup_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.K.A.1';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);


            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            // $approveBtn  = '';
            // $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('ka/accounting/updateJUP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Jurnal Umum Perkiraan : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Jurnal Umum Perkiraan 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('ka/accounting/detailJUP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Jurnal Umum Perkiraan : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Jurnal Umum Perkiraan 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('ka/accounting/show_jup') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Preview Jurnal Umum Perkiraan : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Preview Jurnal Umum Perkiraan 
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
                // if ($canApprove)   $menuContent .= $approveBtn;
                // if ($canApprove)   $menuContent .= $disapproveBtn;
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

            // $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            // $row[] = $lm->kdsupplier;
            // $row[] = $lm->nmsupplier;
            // $row[] = $lm->alamatsupplier;
            // $row[] = $lm->nmkota;
            // $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            // $docdate  = trim($lm->docdate);
            // $jthtempo = (int) $lm->jthtempo;

            // if (!empty($docdate)) {

            //     $date = new \DateTime(trim($lm->docdate));
            //     $date->modify("+{$jthtempo} days");

            //     $jatuhTempo = $date->format('d/m/Y');

            // } else {
            //     $jatuhTempo = '';
            // }

            // $row[] = $jatuhTempo;

            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_front_jup_view_count_all(),
            "recordsFiltered" => $this->m_finance->t_front_jup_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



    function clearEntryJUP()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_finance->q_jup_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('ka/finance/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.jup');
        $builder_dtl = $this->db->table('sc_tmp.jup_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('ka/accounting/jup'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('ka/accounting/jup'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('ka/accounting/jup'));
        }

    }

    function addJUP()
    {
        /* Penambahan Squence */
        $data['title']="Input Jurnal Umum Perkiraan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.A.1'; $versirelease='I.K.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.K.A.1'";
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

        // Pesan gagal simpan final karena balance tidak nol
        $flashBalance = $this->session->getFlashdata('jup_balance_error');
        if (!empty($flashBalance)) {
            $data['message'] .= "<div class='alert alert-danger'>" . esc($flashBalance) . "</div>";
        }

        // Pesan gagal simpan final karena detail belum lengkap / belum ada
        $flashFinal = $this->session->getFlashdata('jup_final_error');
        if (!empty($flashFinal)) {
            $data['message'] .= "<div class='alert alert-warning'>" . esc($flashFinal) . "</div>";
        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_finance->q_jup_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_finance->q_jup_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/accounting/v_add_jup',$data);
    }


    public function getBranchInfoJUP()
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

    public function getNextSuffixJUP()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.jup')
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

    public function initJUPHeader()
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

        $builder = $this->db->table('sc_tmp.jup');
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



    public function saveJUPDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        // ID detail dari hidden field #idurut pada modal (formJUPDetail).
        $idurutRaw = trim((string) ($this->request->getPost('idurut') ?? ''));

        $idurut = null;

        if ($idurutRaw !== '') {
            if (!ctype_digit($idurutRaw) || (int) $idurutRaw <= 0) {
                throw new \RuntimeException('ID detail tidak valid');
            }

            $idurut = (int) $idurutRaw;
        }

        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.jup');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST

        if ($exists == 0) {

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                // 'senddate'   => date('Y-m-d', strtotime(trim($this->request->getPost('senddate')))),
                // 'jthtempo'     => $this->request->getPost('jthtempo'),
                // 'isinclusive'     => $isinclusive,

                // 'kdsupplier'    => strtoupper($this->request->getPost('kdsupplier')),
                // 'alamatsupplier'    => strtoupper($this->request->getPost('alamatsupplier')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                // 'idtax'    => strtoupper($this->request->getPost('idtax')),
                // 'currcode'    => strtoupper($this->request->getPost('currcode')),
                // 'kurs'    => strtoupper($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.jup_dtl');
        $insertCount = 0;
        $message = '';

        // -----------------------------------------------------
        // Cabang header sebagai default Cost Center detail.
        // uq_transaction_hd tidak memuat kolom cabang, sehingga
        // transaction_dt dengan arah (D/K) sama tetapi cabang berbeda
        // akan bentrok saat simpan final. Cost Center kosong ('')
        // selalu dianggap sama dengan cabang header.
        // -----------------------------------------------------
        $headerRow = $builderHeader
            ->select('cabang')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        $cabangHeader = strtoupper(trim((string) ($headerRow['cabang'] ?? '')));

        $idcoa    = $this->request->getPost('idcoa');
        $nmcoa    = strtoupper($this->request->getPost('nmcoa'));
        $nilai       = $this->request->getPost('nilai') ?: 0;
        $remarks = strtoupper($this->request->getPost('remarks'));
        $dk = strtoupper($this->request->getPost('dk'));
        $cabangdtl = strtoupper(trim((string) $this->request->getPost('cabangdtl')));

        if ($cabangdtl === '') {
            $cabangdtl = $cabangHeader;
        }

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan
            // 🔹 UPDATE
            $builderDetail->where('idurut', $idurut)->update([
                'idcoa'    => $idcoa,
                'nmcoa'    => $nmcoa,
                'dk'        => $dk,
                'nilai'        => $nilai,
                'cabang'        => $cabangdtl,
                'remarks' => $remarks,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);

            // // Update header PO
            // $builderHeader->where('docno', $docno)->update([
            //     // 'dpp' => number_format($dpp, 2, '.', ''),
            //     // 'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
            //     'balance' => number_format($balance, 2, '.', ''),
            //     'updateby' => $nama,
            //     'updatedate' => date('Y-m-d H:i:s')
            // ]);

            $message = 'Data berhasil diupdate';

        } else {
            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $nmcoa
                . '|' . $docno
                . '|' . $nama
                . '|' . $inputdate;

            $uniqueid  = hash('sha256', $rawUnique);


            // 🔹 INSERT
            $builderDetail->insert([
                'docno'       => $docno,
                'idcoa'    => $idcoa,
                'nmcoa'    => $nmcoa,
                'dk'         => $dk,
                'cabang'         => $cabangdtl,
                'remarks' => $remarks,
                'nilai' => $nilai,
                'status'      => 'F',
                'inputby'     => $nama,
                'inputdate'   => date('Y-m-d H:i:s'),
                'uniqueid'    => $uniqueid
            ]);
        }


        // =====================================================
        // HITUNG ULANG BALANCE HEADER
        // =====================================================

        $getBalance = $db->table('sc_tmp.jup_dtl')
            ->select("
                SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('DEBIT','D','DR') THEN nilai ELSE 0 END) AS total_debit,
                SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('KREDIT','K','CREDIT','CR') THEN nilai ELSE 0 END) AS total_kredit
            ")
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $totalDebit  = ($getBalance['total_debit'] ?? 0);
        $totalKredit = ($getBalance['total_kredit'] ?? 0);

        // balance = debit - kredit
        $balance = $totalDebit - $totalKredit;

        // update header
        $builderHeader->where('docno', $docno)->update([
            'balance'    => $balance,
            'updateby'   => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ]);
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



    /**
     * Buka dokumen JUP yang sudah final / cetak ke halaman input untuk direvisi.
     *
     * Alur yang dipakai (sesuai trigger database sc_trx.tr_jup()):
     *   status sc_trx.jup diubah F -> E, lalu trigger menyalin header + detail
     *   ke sc_tmp.jup / sc_tmp.jup_dtl dengan docnotmp = docno.
     *   Dengan docnotmp terisi, "Simpan Final Data" berikutnya memperbarui
     *   dokumen yang sama (tidak membuat nomor dokumen baru).
     */
    function updateJUP()
    {
        $nama = trim((string) $this->session->get('nama'));
        $urlBack = base_url('ka/accounting/jup');

        if ($nama === '') {
            return redirect()->to($urlBack);
        }

        // id / docno dikirim dalam bentuk hex (lihat tombol Update di list JUP)
        $idHex = trim((string) $this->request->getGet('id'));
        if ($idHex === '') {
            $idHex = trim((string) $this->request->getGet('docno'));
        }

        $docno = '';
        if ($idHex !== '' && ctype_xdigit($idHex) && strlen($idHex) % 2 === 0) {
            $docno = trim((string) hex2bin($idHex));
        }

        if ($docno === '') {
            $this->session->setFlashdata('jup_update_error',
                'Dokumen JUP tidak valid. Silakan pilih data dari daftar JUP.');
            return redirect()->to($urlBack);
        }

        $db = \Config\Database::connect();

        try {
            $header = $db->table('sc_trx.jup')
                ->select('docno, status')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            if (!$header) {
                $this->session->setFlashdata('jup_update_error',
                    'Dokumen JUP ' . esc($docno) . ' tidak ditemukan.');
                return redirect()->to($urlBack);
            }

            $status = strtoupper(trim((string) $header['status']));

            if (!in_array($status, ['F', 'P'], true)) {
                $this->session->setFlashdata('jup_update_error',
                    'Dokumen JUP ' . esc($docno) . ' berstatus ' . esc($status) . ' sehingga tidak dapat diupdate. '
                    . 'Hanya dokumen FINAL USER (F) atau CETAK/PRINT (P) yang dapat diupdate.');
                return redirect()->to($urlBack);
            }

            // Satu dokumen hanya boleh diedit satu user pada satu waktu karena
            // trigger menyalin dokumen ke tabel temporary (sc_tmp.jup).
            $draft = $db->table('sc_tmp.jup')
                ->select('inputby')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            if ($draft && trim((string) $draft['inputby']) !== $nama) {
                $this->session->setFlashdata('jup_update_error',
                    'Dokumen JUP ' . esc($docno) . ' sedang diedit oleh user lain ('
                    . esc(trim((string) $draft['inputby'])) . ').');
                return redirect()->to($urlBack);
            }

            $now = date('Y-m-d H:i:s');

            $db->transBegin();

            // Aplikasi hanya mendukung satu draft JUP per user, jadi draft lama
            // (milik user ini) dan sisa draft dokumen ini dibersihkan dulu.
            $db->table('sc_tmp.jup_dtl')->where('inputby', $nama)->orWhere('docno', $docno)->delete();
            $db->table('sc_tmp.jup')->where('inputby', $nama)->orWhere('docno', $docno)->delete();

            // Status P (cetak) dinormalkan ke F dulu, sebab trigger tr_jup()
            // hanya menyalin dokumen ke temporary saat transisi F -> E.
            if ($status === 'P') {
                $db->table('sc_trx.jup')
                    ->where('docno', $docno)
                    ->where('status', 'P')
                    ->update([
                        'status'     => 'F',
                        'updateby'   => $nama,
                        'updatedate' => $now,
                    ]);
            }

            // F -> E : trigger sc_trx.tr_jup() menyalin header + detail ke
            // sc_tmp.jup / sc_tmp.jup_dtl beserta docnotmp = docno.
            $db->table('sc_trx.jup')
                ->where('docno', $docno)
                ->where('status', 'F')
                ->update([
                    'status'     => 'E',
                    'updateby'   => $nama,
                    'updatedate' => $now,
                ]);

            $tempHeader = $db->table('sc_tmp.jup')->where('docno', $docno)->countAllResults();

            if ((int) $tempHeader === 0) {
                $db->transRollback();
                $this->session->setFlashdata('jup_update_error',
                    'Dokumen JUP ' . esc($docno) . ' gagal dibuka untuk diupdate. Silakan hubungi administrator.');
                return redirect()->to($urlBack);
            }

            // Hasil salinan trigger memakai inputby pembuat dokumen; alihkan ke
            // user yang melakukan update agar halaman input (addJUP) menampilkannya.
            $db->table('sc_tmp.jup')
                ->where('docno', $docno)
                ->update([
                    'inputby'    => $nama,
                    'updateby'   => $nama,
                    'updatedate' => $now,
                ]);

            $db->table('sc_tmp.jup_dtl')
                ->where('docno', $docno)
                ->update([
                    'inputby'    => $nama,
                    'updateby'   => $nama,
                    'updatedate' => $now,
                ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                $this->session->setFlashdata('jup_update_error',
                    'Dokumen JUP ' . esc($docno) . ' gagal dibuka untuk diupdate.');
                return redirect()->to($urlBack);
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'updateJUP gagal buka dokumen {docno}: {pesan}', [
                'docno' => $docno,
                'pesan' => $e->getMessage(),
            ]);

            $this->session->setFlashdata('jup_update_error',
                'Dokumen JUP ' . esc($docno) . ' gagal dibuka untuk diupdate: ' . esc($e->getMessage()));
            return redirect()->to($urlBack);
        }

        // Draft sudah tersalin ke sc_tmp.jup / sc_tmp.jup_dtl.
        return redirect()->to(base_url('ka/accounting/addJUP'));
    }

    function showing_juptrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_finance->q_jup_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_juptemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_finance->q_jup_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_jup_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_finance->q_jup_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_jup_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.jup_dtl')
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

    public function delete_jup_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.jup_dtl');

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

            // docno yang terpengaruh (dipakai untuk hitung ulang balance header)
            $docnoList = array_values(array_unique(array_column(
                $db->table('sc_tmp.jup_dtl')->select('docno')->whereIn('idurut', $ids)->get()->getResultArray(),
                'docno'
            )));

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

            // HITUNG ULANG BALANCE HEADER (balance = total debit - total kredit)
            foreach ($docnoList as $docnoDtl) {
                $docnoDtl = trim((string) $docnoDtl);

                if ($docnoDtl === '') {
                    continue;
                }

                $balanceDtl = $this->getJUPTempBalance($docnoDtl);

                $db->table('sc_tmp.jup')
                    ->where('docno', $docnoDtl)
                    ->update([
                        'balance'    => $balanceDtl['balance'],
                        'updateby'   => trim((string) $this->session->get('nama')),
                        'updatedate' => date('Y-m-d H:i:s')
                    ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data JUP Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_jup_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_jup_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idcoa;
            $row[] = $lm->nmcoa;
            $row[] = $lm->remarks;
            $row[] = $lm->dk;
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_jup_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_finance->t_jup_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_jup_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_jup_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->idcoa;
            $row[] = $lm->nmcoa;
            $row[] = $lm->remarks;
            $row[] = $lm->dk;
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $data[] = $row;

        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_jup_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_finance->t_jup_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    /**
     * Total debit, total kredit dan balance (debit - kredit) dari detail JUP temporary.
     */
    private function getJUPTempBalance($docno)
    {
        $docno = trim((string) $docno);

        if ($docno === '') {
            return [
                'total_debit'  => 0.0,
                'total_kredit' => 0.0,
                'balance'      => 0.0,
            ];
        }

        $row = $this->db->table('sc_tmp.jup_dtl')
            ->select("
                COALESCE(SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('DEBIT','D','DR') THEN nilai ELSE 0 END),0) AS total_debit,
                COALESCE(SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('KREDIT','K','CREDIT','CR') THEN nilai ELSE 0 END),0) AS total_kredit
            ")
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $totalDebit  = (float) ($row['total_debit'] ?? 0);
        $totalKredit = (float) ($row['total_kredit'] ?? 0);

        return [
            'total_debit'  => $totalDebit,
            'total_kredit' => $totalKredit,
            'balance'      => $totalDebit - $totalKredit,
        ];
    }

    function finalEntryJUP(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(dk, '') = ''  OR nilai = '0.00' OR COALESCE(nmcoa, '') = '' OR COALESCE(remarks, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_finance->q_jup_master_temp($param);
        $headerRow = $header->getRowArray();

        if (!$headerRow) {
            $this->session->setFlashdata('jup_balance_error', 'Data header JUP tidak ditemukan. Silakan ulangi input JUP.');
            return redirect()->to(base_url('/ka/accounting/addJUP'));
        }

        $status = trim($headerRow['status'] ?? '');
        $cek = $this->m_finance->q_jup_dtl_temp($paramdtl);
        $cek2 = $this->m_finance->q_jup_dtl_temp($paramdtl2);

        // VALIDASI BALANCE: total debit harus sama dengan total kredit
        $jupBalance = $this->getJUPTempBalance($headerRow['docno'] ?? '');


        $builder = $this->db->table('sc_tmp.jup');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        // =====================================================
        // TOLAK SIMPAN FINAL JIKA TOTAL DEBIT <> TOTAL KREDIT
        // (balance harus 0, tidak boleh hanya mengandalkan validasi client)
        // =====================================================
        if (abs($jupBalance['balance']) > 0.005)
        {
            $this->session->setFlashdata('jup_balance_error',
                'Total Debit (' . number_format($jupBalance['total_debit'], 2, '.', ',') . ') ' .
                'dengan Total Kredit (' . number_format($jupBalance['total_kredit'], 2, '.', ',') . ') tidak sama. ' .
                'Balance harus 0 sebelum data disimpan (balance saat ini: ' .
                number_format($jupBalance['balance'], 2, '.', ',') . ').');

            return redirect()->to(base_url('/ka/accounting/addJUP'));
        }

        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.K.A.1');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.K.A.1',
            );
            $builder_trxerror->insert($infotrxerror);

            // Beri pesan yang jelas agar tombol "Simpan Final Data" tidak
            // terkesan tidak berfungsi (penyebab penolakan selalu ditampilkan)
            $this->session->setFlashdata('jup_final_error',
                $cek->getNumRows() > 0
                    ? 'Simpan final ditolak: ada ' . $cek->getNumRows() . ' baris detail yang belum lengkap (Debit/Kredit, Nilai, Nama Perkiraan, atau Keterangan masih kosong).'
                    : 'Simpan final ditolak: belum ada detail perkiraan yang diinput.');

            return redirect()->to(base_url('/ka/accounting/addJUP'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            $keterangan   = trim($this->request->getPost('keterangan'));

            // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                // 'senddate'       => $senddateph,
                // 'jthtempo'       => $jthtempo,
                // 'kdsupplier'     => strtoupper($kdsupplier),
                // 'alamatsupplier' => strtoupper($alamatsupplier),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                // 'currcode'       => $currcode,
                // 'kurs'           => $kurs_clean,
                // 'isinclusive'    => strtoupper($isinclusive),
                // 'idtax'          => strtoupper($idtax),
                // 'syarat'         => strtoupper($syarat)
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);

            // -------------------------------------------------
            // UPDATE status F memicu trigger sc_tmp.tr_jup_finalize().
            // Jika trigger gagal (mis. bentrok uq_transaction_hd),
            // tampilkan pesan yang bisa dibaca user, bukan error page.
            // -------------------------------------------------
            try {
                $simpanFinal = $builder->update($info);
            } catch (\Throwable $e) {
                log_message('error', 'finalEntryJUP gagal simpan final: {pesan}', [
                    'pesan' => $e->getMessage(),
                ]);

                $this->session->setFlashdata('jup_final_error', $this->buildJUPFinalErrorMessage($e));

                return redirect()->to(base_url('/ka/accounting/addJUP'));
            }

            if ($simpanFinal) {
                $paramerror=" and userid='$nama' and modul='I.K.A.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/ka/accounting/jup'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.K.A.1',
                );
                $builder_trxerror->insert($infotrxerror);

                $this->session->setFlashdata('jup_final_error',
                    'Simpan final gagal disimpan. Periksa kembali detail perkiraan (Debit/Kredit, Nilai, Nama Perkiraan, Keterangan, dan Cost Center) lalu coba lagi.');

                return redirect()->to(base_url('/ka/accounting/addJUP'));
            }



        }

    }

    /**
     * Pesan ramah untuk kegagalan simpan final JUP (exception dari trigger DB).
     */
    private function buildJUPFinalErrorMessage(\Throwable $e)
    {
        $raw = (string) $e->getMessage();

        // Bentrok unique key header transaksi:
        // UNIQUE (docno, doctype, journal_type, type_in_out, idbranch, ref_docno, ref_doctype)
        // -> terjadi bila ada lebih dari satu baris arah (D/K) yang sama
        //    dengan cabang/Cost Center berbeda pada satu dokumen.
        if (stripos($raw, 'uq_transaction_hd') !== false) {
            return 'Simpan final gagal: kunci header transaksi bentrok (uq_transaction_hd). '
                . 'Penyebab umumnya ada lebih dari satu baris detail dengan arah (Debit/Kredit) '
                . 'dan cabang sama tetapi Cost Center berbeda, sehingga header hanya mendukung '
                . 'satu baris per arah. Samakan Cost Center pada baris detail tersebut, lalu simpan ulang.';
        }

        // COA / nilai / DK tidak valid (validasi di sc_trx.fn_sync_jup_detail_jvgenl)
        if (preg_match('/COA .* tidak ditemukan|COA JUP kosong|DK JUP harus|Nilai JUP tidak boleh negatif/i', $raw)) {
            return 'Simpan final ditolak: ' . trim(preg_replace('/\s+/', ' ', $raw));
        }

        $singkat = trim(preg_replace('/\s+/', ' ', $raw));

        if (function_exists('mb_substr')) {
            $singkat = mb_substr($singkat, 0, 300);
        } else {
            $singkat = substr($singkat, 0, 300);
        }

        return 'Simpan final gagal diproses oleh database: ' . $singkat;
    }

    public function finalEntryPO_DP()
    {
        $nama = trim($this->session->get('nama'));

        $this->db->transStart();

        /*
        ==========================
        VALIDASI DATA PO
        ==========================
        */

        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' 
                    AND (COALESCE(unit, '') = ''  
                    OR qty = '0.00' 
                    OR qty = '0' 
                    OR COALESCE(nmbarang, '') = '' 
                    OR COALESCE(descriptionpo, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_finance->q_po_master_temp($param)->getRowArray();
        $cek = $this->m_finance->q_po_dtl_temp($paramdtl);
        $cek2 = $this->m_finance->q_po_dtl_temp($paramdtl2);

        if(!$header){
            return redirect()->to(base_url('/ka/finance/addPO'));
        }

        $status = trim($header['status']);

        /*
        ==========================
        TRX ERROR CLEAN
        ==========================
        */

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama)
            ->where('modul','I.K.A.1')
            ->delete();

        if (($status === 'E' && $cek->getNumRows() > 0) || ($cek2->getNumRows() <= 0))
        {
            $builder_trxerror->insert([
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.K.A.1',
            ]);

            return redirect()->to(base_url('/ka/finance/addPO'));
        }

        /*
        ==========================
        AMBIL POST DATA
        ==========================
        */

        $docdate   = trim($this->request->getPost('docdate'));
        $senddate  = trim($this->request->getPost('senddate'));
        $jthtempo  = trim($this->request->getPost('jthtempo'));
        $kdsupplier = trim($this->request->getPost('kdsupplier'));
        $alamatsupplier = trim($this->request->getPost('alamatsupplier'));
        $alamatkirim = trim($this->request->getPost('alamatkirim'));
        $keterangan = trim($this->request->getPost('keterangan'));
        $currcode = trim($this->request->getPost('currcode'));
        $idtax = trim($this->request->getPost('idtax'));
        $syarat = trim($this->request->getPost('syarat'));
        $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';
        $dpp_clean = $this->cleanNumber($this->request->getPost('dpp'));
        $jumlahpajak_clean = $this->cleanNumber($this->request->getPost('jumlahpajak'));
        $total_clean = $this->cleanNumber($this->request->getPost('total'));

        /*
        ==========================
        FORMAT KURS
        ==========================
        */

        $kurs = trim($this->request->getPost('kurs'));
        $kurs_clean = !empty($kurs) ? str_replace(',', '', $kurs) : 0;

        /*
        ==========================
        FORMAT DATE
        ==========================
        */

        $docdateph = !empty($docdate) ? date('Y-m-d', strtotime(str_replace('-', '/', $docdate))) : null;
        $senddateph = !empty($senddate) ? date('Y-m-d', strtotime(str_replace('-', '/', $senddate))) : null;

        /*
        ==========================
        UPDATE HEADER TMP PO
        ==========================
        */

        $this->db->table('sc_tmp.po')
            ->where('inputby',$nama)
            ->update([
                'docdate' => $docdateph,
                'senddate' => $senddateph,
                'jthtempo' => $jthtempo,
                'kdsupplier' => strtoupper($kdsupplier),
                'alamatsupplier' => strtoupper($alamatsupplier),
                'alamatkirim' => strtoupper($alamatkirim),
                'keterangan' => strtoupper($keterangan),
                'currcode' => $currcode,
                'kurs' => $kurs_clean,
                'dpp' => $dpp_clean,
                'jumlahpajak' => $jumlahpajak_clean,
                'total' => $total_clean,
                'isinclusive' => strtoupper($isinclusive),
                'idtax' => strtoupper($idtax),
                'syarat' => strtoupper($syarat)
            ]);

        /*
        ==========================
        AMBIL HEADER LAGI
        ==========================
        */

        $header = $this->db->table('sc_tmp.po')
            ->where('inputby',$nama)
            ->get()
            ->getRowArray();

        $idurutPO = $header['idurut'];

        $this->db->table('sc_tmp.po')
            ->where('inputby',$nama)
            ->update(['status'=>'F']);

        /* ambil PO final */

        $trxPO = $this->db->table('sc_trx.po')
            ->where('idurut',$idurutPO)
            ->get()
            ->getRowArray();

        $docnoPOFinal = $trxPO['docno'];

        /* ambil suffix */

        /*
        ==========================
        GENERATE DOCNO UMB
        ==========================
        */

        $prefix = 'UMK';
        $infix = date('ym', strtotime($this->session->get('logindate')));
        // $kodeSuffix = 'PT';
        $parts = explode('/',$docnoPOFinal);
        $suffixPart = trim($parts[2]);
        $kodeSuffix = preg_replace('/[0-9]/','',$suffixPart);

        $like = $prefix.'/'.$infix.'/'.$kodeSuffix;

        $sql = "
            SELECT docno FROM sc_trx.umb WHERE docno LIKE '$like%'
            UNION ALL
            SELECT docno FROM sc_tmp.umb WHERE docno LIKE '$like%'
            ORDER BY docno DESC
            LIMIT 1
        ";

        $row = $this->db->query($sql)->getRowArray();

        if($row){
            $parts = explode('/',$row['docno']);
            $last = preg_replace('/[^0-9]/','',$parts[2]);
            $next = str_pad(((int)$last)+1,4,'0',STR_PAD_LEFT);
        }else{
            $next = '0001';
        }

        $docnoUMB = $prefix.'/'.$infix.'/'.$kodeSuffix.$next;

        /*
        ==========================
        INSERT TMP UMB
        ==========================
        */


        $this->db->table('sc_tmp.umb')->insert([
            'docno'          => $docnoUMB,
            'docdate'        => $header['docdate'],
            'cabang'         => $header['cabang'],
            'jthtempo'       => $header['jthtempo'],
            'kdsupplier'     => $header['kdsupplier'],
            'nmsupplier'     => $header['nmsupplier'],
            'alamatsupplier' => $header['alamatsupplier'],
            'currcode'       => $header['currcode'],
            'kurs'           => $header['kurs'],
            'idtax'          => $header['idtax'],
            'isinclusive'    => $header['isinclusive'],
            'dpp'            => $header['dpp'],
            'jumlahpajak'    => $header['jumlahpajak'],
            'total'          => $header['total'],
            'keterangan'     => $header['keterangan'],
            'dk'             => 'KREDIT',
            'status'         => 'E',
            'inputby'        => $nama,
            'inputdate'      => date('Y-m-d H:i:s')
        ]);

        /*
        ==========================
        VALIDASI INSERT TMP
        ==========================
        */

        $cekTmp = $this->db->table('sc_tmp.umb')
            ->where('docno', $docnoUMB)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if(!$cekTmp){
            $this->db->transRollback();
            throw new \RuntimeException('Insert TMP UMB gagal.');
        }

        /*
        ==========================
        FINALIZE TMP (E → F)
        Trigger akan:
        - insert ke sc_trx.umb
        - delete sc_tmp.umb
        ==========================
        */

        $this->db->table('sc_tmp.umb')
            ->where('docno', $docnoUMB)
            ->where('inputby', $nama)
            ->update([
                'status' => 'F'
            ]);

        /*
        ==========================
        AMBIL DOCNO FINAL DI TRX
        (docno bisa berubah karena trigger)
        ==========================
        */

        $trxUMB = $this->db->table('sc_trx.umb')
            ->where('inputby', $nama)
            ->orderBy('inputdate','DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if(!$trxUMB){
            $this->db->transRollback();
            throw new \RuntimeException('Finalize UMB gagal, data tidak masuk trx.');
        }

        $docnoUMBFinal = $trxUMB['docno'];

        /*
        ==========================
        UBAH TRX → EDIT MODE (F → E)
        Trigger akan insert kembali ke TMP
        ==========================
        */

        $this->db->table('sc_trx.umb')
            ->where('docno', $docnoUMBFinal)
            ->update([
                'status' => 'E'
            ]);

        /*
        ==========================
        LINK PO → UMB
        ==========================
        */

        $this->db->table('sc_trx.po')
            ->where('docno',$docnoPOFinal)
            ->update([
                'docnoumb' => $docnoUMBFinal
            ]);

        $this->db->transComplete();

        /*
        ==========================
        REDIRECT
        ==========================
        */

        return redirect()->to(
            base_url('/ka/finance/addUMB')
        );
    }


    function show_jup(){
        $module = 'Jurnal Umum Perkiraan';
        $table = 'sc_trx.jup';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.jup');

        //    $builder = $builder
        //         ->where('docno', $docno)
        //         ->update([
        //             'status'=> 'P',
        //             'printby' => $nama,
        //             'printdate' => date('Y-m-d H:i:s')
        //         ]);


        $enc_docno = $this->fiky_encryption->sealed($docno);

        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Bukti Jurnal";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("ka/accounting/api_jup/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_jup.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno);
    }

    function api_jup(){
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
        $datamst = $this->m_finance->q_jup_master($param);
        $datadtl = $this->m_finance->q_jup_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {


            // // 🔹 Ambil nmsupplier berdasarkan kdsupplier
            // $kdsupplier = trim($detail->kdsupplier);

            // $supplier = $this->db->query("
            //     SELECT nmsupplier
            //     FROM sc_mst.mstsupplier
            //     WHERE TRIM(kdsupplier) = ?
            //     LIMIT 1
            // ", [$kdsupplier])->getRow();

            // // 🔹 Set ke object detail
            // $detail->nmsupplierdata = $supplier->nmsupplier ?? '';
            // $nilai = $detail->total; // dari database

            // $data['total'] = $nilai;
            // $data['total_terbilang'] = strtoupper($this->terbilang($nilai));
            // $detail->terbilang = $data['total_terbilang'];


            // $currcode = trim($detail->currcode);

            // $currency = $this->db->query("
            //     SELECT currname
            //     FROM sc_mst.currency
            //     WHERE TRIM(currcode) = ?
            //     LIMIT 1
            // ", [$currcode])->getRow();

            // $cleanedCurrname = $currency->currname ?? '';
            // $cleanedCurrname = str_replace('(LUAR NEGERI)', '', $cleanedCurrname);
            // $cleanedCurrname = trim($cleanedCurrname);

            // $detail->currname = $cleanedCurrname;



            $detail->namauser = $nama;

        }

        $detailRows = $datadtl->getResultArray();

        // minimum row yang ingin ditampilkan
        $minRow = 20;

        // hitung kekurangan
        $kurang = $minRow - count($detailRows);

        // =========================
        // DATA ASLI = dummy 0
        // =========================
        foreach ($detailRows as &$row) {
            $row['dummy'] = 0;
        }

        // =========================
        // TAMBAHKAN ROW KOSONG
        // =========================
        if ($kurang > 0) {

            for ($i = 0; $i < $kurang; $i++) {

                $detailRows[] = [

                    // penanda row kosong
                    'dummy'        => 1,

                    // field existing
                    'accno'        => '',
                    'accname'      => '',
                    'keterangan'   => '',
                    'debet'        => '',
                    'kredit'       => '',
                    'nilai'        => ''

                ];
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
                'detail' => $detailRows,
            ), JSON_PRETTY_PRINT);
    }


    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam",
            "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";

        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10) . " puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai / 1000000000) . " milyar" . $this->penyebut($nilai % 1000000000);
        }

        return $temp;
    }

    function terbilang($nilai) {
        $nilai = floatval($nilai);

        $integer = floor($nilai);
        $decimal = $nilai - $integer;

        $hasil = trim($this->penyebut($integer));

        // Handle decimal (koma)
        if ($decimal > 0) {
            $decimalStr = substr(strstr(number_format($nilai, 2, '.', ''), '.'), 1);
            $angka = ["0"=>"nol","1"=>"satu","2"=>"dua","3"=>"tiga","4"=>"empat","5"=>"lima","6"=>"enam","7"=>"tujuh","8"=>"delapan","9"=>"sembilan"];

            $hasil .= " koma";

            for ($i = 0; $i < strlen($decimalStr); $i++) {
                $hasil .= " " . $angka[$decimalStr[$i]];
            }
        }

        return strtoupper($hasil);
    }


    // =================================== UMB ===========================================


    public function umt()
    {
        $data['title']="Uang Muka Titipan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.B.1'; $versirelease='I.K.B.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.B.1'";
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
        $dtl = $this->m_finance->q_umt_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('ka/finance/clearEntryUMT');
            $urlnext = base_url('ka/finance/addUMT');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.K.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/finance/v_list_umt',$data);
    }

    function detailUMT()
    {
        /* Penambahan Squence */
        $data['title']="Detail Uang Muka Titipan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('ka/finance/umt'));
        }
        $kodemenu='I.K.B.1'; $versirelease='I.K.B.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.B.1'";
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
        $data['dtldata'] = $this->m_finance->q_umt_master($param)->getRowArray();
        return $this->template->render('finance/finance/v_detail_umt',$data);
    }

    function list_umt(){
        $list = $this->m_finance->get_t_front_umt_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.K.B.1';
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
                    href="' . base_url('ka/finance/updateUMT') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Uang Muka Titipan : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Uang Muka Titipan 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('ka/finance/detailUMT') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Uang Muka Titipan : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Uang Muka Titipan 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('ka/finance/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Uang Muka Titipan : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Uang Muka Titipan 
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

            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsplr;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
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
            $row[] = $lm->dk;
            $row[] = '<div class="ratakanan">'. number_format($lm->dpp, 0, '.', ',') . '</div>';
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_front_umt_view_count_all(),
            "recordsFiltered" => $this->m_finance->t_front_umt_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function clearEntryUMT()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_finance->q_umt_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('ka/finance/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.umt');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('ka/finance/umt'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('ka/finance/umt'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('ka/finance/umt'));
        }

    }

    function addUMT()
    {
        /* Penambahan Squence */
        $data['title']="Input Uang Muka Titipan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.B.1'; $versirelease='I.K.B.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.K.B.1'";
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
        $data['mst'] = $this->m_finance->q_umt_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_finance->q_umt_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/finance/v_add_umt',$data);
    }


    public function getBranchInfoUMT()
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

    public function getNextSuffixUMT()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.umt')
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

    public function initUMTHeader()
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

        $builder = $this->db->table('sc_tmp.umt');
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



    public function saveUMTDetail()
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
        $builderHeader = $db->table('sc_tmp.umt');

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

        $builderDetail = $db->table('sc_tmp.umt_dtl');
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

        $umtHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
        $idtax = $umtHeader['idtax'] ?? '';

        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.umt_dtl');
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

        // Update header UMT
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


    public function updateStatusUMT()
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
        $builder = $db->table('sc_trx.umt');
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



    function updateUMT()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_finance->q_umt_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.umt');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('ka/finance/addUMT'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('ka/finance/umt'));
        }
    }

    function showing_umttrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_finance->q_umt_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_umttemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_finance->q_umt_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_umt_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_finance->q_umt_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    function finalEntryUMT() {
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getPost('docno'));

        // Hapus data error sebelumnya
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.K.B.1');
        $builder_trxerror->delete();

        if ($docno === '') {
            // Insert error jika docno kosong
            $infotrxerror = [
                'userid' => $nama,
                'errorcode' => 3,
                'modul' => 'I.K.B.1',
            ];
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/ka/finance/addUMT'));
        }

        // Ambil semua data dari POST
        $cabang = trim($this->request->getPost('cabang'));
        $docdate = trim($this->request->getPost('docdate'));
        $dk = strtoupper($this->request->getPost('dk'));
        $prkarap = strtoupper($this->request->getPost('prkarap'));
        $prkkas = strtoupper($this->request->getPost('prkkas'));
        $kdsupplier = strtoupper($this->request->getPost('kdsupplier'));
        $alamatsupplier = strtoupper($this->request->getPost('alamatsupplier'));
        $jthtempo = strtoupper($this->request->getPost('jthtempo'));
        $keterangan = strtoupper($this->request->getPost('keterangan'));
        $currcode = strtoupper($this->request->getPost('currcode'));
        $idtax = strtoupper($this->request->getPost('idtax'));
        $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';

        // Bersihkan format angka
        $kurs_clean = $this->cleanNumber($this->request->getPost('kurs'));
        $dpp_clean = $this->cleanNumber($this->request->getPost('dpp'));
        $jumlahpajak_clean = $this->cleanNumber($this->request->getPost('jumlahpajak'));
        $total_clean = $this->cleanNumber($this->request->getPost('total'));

        // Convert date
        $docdateph = !empty($docdate) ? date('Y-m-d', strtotime(str_replace('-', '/', $docdate))) : null;

        // **CEK APAKAH DATA SUDAH ADA**
        $existingData = $this->db->table('sc_tmp.umt')
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $builder = $this->db->table('sc_tmp.umt');

        if ($existingData) {
            // **UPDATE DATA YANG SUDAH ADA**
            $updateHeader = [
                'docdate' => $docdateph,
                'dk' => $dk,
                'prkarap' => $prkarap,
                'prkkas' => $prkkas,
                'jthtempo' => $jthtempo,
                'kdsupplier' => strtoupper($kdsupplier),
                'alamatsupplier' => strtoupper($alamatsupplier),
                'currcode' => $currcode,
                'kurs' => $kurs_clean,
                'dpp' => $dpp_clean,
                'jumlahpajak' => $jumlahpajak_clean,
                'total' => $total_clean,
                'isinclusive' => $isinclusive,
                'idtax' => strtoupper($idtax),
                'keterangan' => strtoupper($keterangan) // Hanya 1 kali
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
                'dk' => $dk,
                'prkarap' => $prkarap,
                'prkkas' => $prkkas,
                'jthtempo' => $jthtempo,
                'kdsupplier' => strtoupper($kdsupplier),
                'alamatsupplier' => strtoupper($alamatsupplier),
                'currcode' => $currcode,
                'kurs' => $kurs_clean,
                'dpp' => $dpp_clean,
                'jumlahpajak' => $jumlahpajak_clean,
                'total' => $total_clean,
                'isinclusive' => $isinclusive,
                'idtax' => strtoupper($idtax),
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
            $paramerror=" and userid='$nama' and modul='I.K.B.1'";
            $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
            $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

            // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

            return redirect()->to(base_url('/ka/finance/umt'));
        } else {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                // 'nomorakhir1' => $cek->getNumRows(),
                // 'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.K.B.1',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/ka/finance/addUMT'));
        }
    }


    private function cleanNumber($value) {
        if (empty($value)) return 0;
        return str_replace(',', '', $value);
    }

    function show_umt(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.umt');

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
        $datajson =  base_url("ka/finance/api_umt/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_umt.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_umt(){
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
        $datamst = $this->m_finance->q_umt_master($param);
        $datadtl = $this->m_finance->q_umt_dtl($param);
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


    // ============== PENERIMAAN KAS BANK ====================================

    public function penerimaankb()
    {
        $data['title']="Penerimaan Kas/Bank";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.B.2'; $versirelease='I.K.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.B.2'";
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
        $dtl = $this->m_finance->q_penerimaankb_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('ka/finance/clearEntryPenerimaanKB');
            $urlnext = base_url('ka/finance/addPenerimaanKB');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.K.B.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/finance/v_list_penerimaankb',$data);
    }

    function detailPenerimaanKB()
    {
        /* Penambahan Squence */
        $data['title']="Detail Penerimaan Kas/Bank";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('ka/finance/penerimaankb'));
        }
        $kodemenu='I.K.B.2'; $versirelease='I.K.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.B.2'";
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
        $data['dtldata'] = $this->m_finance->q_penerimaankb_master($param)->getRowArray();
        return $this->template->render('finance/finance/v_detail_penerimaankb',$data);
    }

    function list_penerimaankb(){
        $list = $this->m_finance->get_t_front_penerimaankb_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.K.B.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);


            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            // $approveBtn  = '';
            // $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('ka/finance/updatePenerimaanKB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Penerimaan Kas/Bank : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Penerimaan Kas/Bank 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('ka/finance/detailPenerimaanKB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Penerimaan Kas/Bank : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Penerimaan Kas/Bank 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('ka/finance/show_penerimaankb') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Preview Penerimaan Kas/Bank : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Preview Penerimaan Kas/Bank 
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
                // if ($canApprove)   $menuContent .= $approveBtn;
                // if ($canApprove)   $menuContent .= $disapproveBtn;
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

            $row[] = $lm->kdcustomer;
            $row[] = $lm->nmcustomernew;
            $row[] = $lm->alamatcustomer;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            // $docdate  = trim($lm->docdate);
            // $jthtempo = (int) $lm->jthtempo;

            // if (!empty($docdate)) {

            //     $date = new \DateTime(trim($lm->docdate));
            //     $date->modify("+{$jthtempo} days");

            //     $jatuhTempo = $date->format('d/m/Y');

            // } else {
            //     $jatuhTempo = '';
            // }

            // $row[] = $jatuhTempo;
            $row[] = $lm->np;
            $row[] = $lm->prkkas;
            $row[] = $lm->nmcoa;
            $row[] = '<div class="ratakanan">'. number_format($lm->dpp, 2, '.', ',') . '</div>';

            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_front_penerimaankb_view_count_all(),
            "recordsFiltered" => $this->m_finance->t_front_penerimaankb_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



    function clearEntryPenerimaanKB()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_finance->q_penerimaankb_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('ka/finance/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.penerimaankb');
        $builder_dtl = $this->db->table('sc_tmp.penerimaankb_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('ka/finance/penerimaankb'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('ka/finance/penerimaankb'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('ka/finance/penerimaankb'));
        }

    }

    function addPenerimaanKB()
    {
        /* Penambahan Squence */
        $data['title']="Input Penerimaan Kas/Bank";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.B.2'; $versirelease='I.K.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.K.B.2'";
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
        $data['mst'] = $this->m_finance->q_penerimaankb_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_finance->q_penerimaankb_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/finance/v_add_penerimaankb',$data);
    }


    public function getBranchInfoPenerimaanKB()
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

        // =========================================================
        // NOMOR BUKTI TERAKHIR (AUTO SUFFIX) UNTUK CABANG INI
        // Prefix default No. Bukti Penerimaan Kas/Bank = BKM
        // =========================================================
        $prefix   = 'BKM';
        $lastInfo = $this->getLastSuffixPenerimaanKB($prefix, $infix, $kodeSuffix);

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'prefix'       => $prefix,
            'last_docno'   => $lastInfo['last_docno'],
            'last_number'  => $lastInfo['last_number'],
            'last_suffix'  => $lastInfo['last_suffix'],
            'next_suffix'  => $lastInfo['next_suffix']
        ]);
    }

    public function getNextSuffixPenerimaanKB()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $lastInfo = $this->getLastSuffixPenerimaanKB($prefix, $infix, $kodeSuffix);

        return $this->response->setJSON([
            'success'     => true,
            'suffix'      => $lastInfo['next_suffix'],
            'last_docno'  => $lastInfo['last_docno'],
            'last_suffix' => $lastInfo['last_suffix']
        ]);
    }

    /**
     * Ambil nomor bukti terakhir sekaligus nomor berikutnya (auto suffix)
     * dari sc_trx.penerimaankb untuk prefix / infix / kode suffix tertentu.
     *
     * Contoh: prefix=BKM, infix=2611, kodeSuffix=PA
     *   last_docno  = BKM/2611/PA0001
     *   last_number = 1
     *   last_suffix = PA0001
     *   next_suffix = PA0002
     */
    private function getLastSuffixPenerimaanKB(string $prefix, string $infix, string $kodeSuffix): array
    {
        $sql = "SELECT docno,
                       CAST(SUBSTRING(split_part(TRIM(docno), '/', 3) FROM 3) AS INTEGER) AS nomor
                FROM sc_trx.penerimaankb
                WHERE split_part(TRIM(docno), '/', 1) = ?
                  AND split_part(TRIM(docno), '/', 2) = ?
                  AND UPPER(split_part(TRIM(docno), '/', 3)) LIKE ?
                  AND SUBSTRING(split_part(TRIM(docno), '/', 3) FROM 3) ~ '^[0-9]+$'
                ORDER BY nomor DESC
                LIMIT 1";

        $row = $this->db->query($sql, [$prefix, $infix, $kodeSuffix . '%'])->getRowArray();

        $lastNo = $row ? (int) $row['nomor'] : 0;

        return [
            'last_docno'  => $row ? trim((string) $row['docno']) : '',
            'last_number' => $lastNo,
            'last_suffix' => $lastNo > 0
                ? $kodeSuffix . str_pad((string) $lastNo, 4, '0', STR_PAD_LEFT)
                : '',
            'next_suffix' => $kodeSuffix . str_pad((string) ($lastNo + 1), 4, '0', STR_PAD_LEFT)
        ];
    }

    public function initPenerimaanKBHeader()
    {
        $nama = trim((string) $this->session->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User login tidak ditemukan.'
            ]);
        }

        // =========================================================
        // AMBIL DATA HEADER DARI POST
        // =========================================================
        $docno          = strtoupper(trim((string) $this->request->getPost('docno')));
        $docdate        = trim((string) $this->request->getPost('docdate'));
        $cabang         = trim((string) $this->request->getPost('cabang'));
        $pemohon        = $nama;
        $kdcustomer     = strtoupper(trim((string) $this->request->getPost('kdcustomer')));
        $nmcustomer     = strtoupper(trim((string) $this->request->getPost('nmcustomer')));
        $alamatcustomer = strtoupper(trim((string) $this->request->getPost('alamatcustomer')));
        $prkkas         = trim((string) $this->request->getPost('prkkas'));
        $np             = trim((string) $this->request->getPost('np'));
        $currcode       = strtoupper(trim((string) $this->request->getPost('currcode')));
        $kurs           = $this->request->getPost('kurs');
        $keterangan     = trim((string) $this->request->getPost('keterangan'));

        // Nilai diterima dikirim lewat field #total (konsep Pengeluaran KB)
        // atau dpp. Keduanya diterima.
        $totalPosted = trim((string) $this->request->getPost('total'));
        $dpp         = trim((string) $this->request->getPost('dpp'));

        if ($dpp === '' && $totalPosted !== '') {
            $dpp = $totalPosted;
        }

        $toNumber = static function ($value) {
            $value = str_replace(',', '', trim((string) ($value ?? '')));

            return $value === '' ? 0 : (float) $value;
        };

        // =========================================================
        // VALIDASI HEADER MINIMAL
        // =========================================================
        if ($docno === '' || $docdate === '' || $cabang === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor dokumen, tanggal, dan cabang wajib diisi.'
            ]);
        }

        if ($kdcustomer === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Customer wajib dipilih.'
            ]);
        }

        if ($prkkas === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Perkiraan Kas/Bank (prkkas) wajib diisi.'
            ]);
        }

        // =========================================================
        // CEK DOCNO SUDAH DIGUNAKAN DI TRANSAKSI
        // =========================================================
        $docnoTrx = $this->db
            ->table('sc_trx.penerimaankb')
            ->where('docno', $docno)
            ->countAllResults();

        if ($docnoTrx > 0) {
            return $this->response->setJSON([
                'success' => false,
                'reload'  => false,
                'message' => 'Nomor dokumen ' . $docno
                    . ' sudah digunakan. Ubah No. Bukti (suffix) terlebih dahulu.'
            ]);
        }

        // =========================================================
        // CEK / SIMPAN HEADER TEMPORARY
        // =========================================================
        $builder = $this->db->table('sc_tmp.penerimaankb');

        $existing = $builder
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->where('status', 'E')
            ->get()
            ->getRowArray();

        $data = [
            'docdate'        => $this->penerimaanKBDateDisplay($docdate) ?: date('d-m-Y'),
            'cabang'         => $cabang,
            'pemohon'        => $pemohon,
            'kdcustomer'     => $kdcustomer,
            'nmcustomer'     => $nmcustomer,
            'alamatcustomer' => $alamatcustomer,
            'prkkas'         => $prkkas,
            'np'             => $np,
            'currcode'       => $currcode !== '' ? $currcode : 'IDR',
            'kurs'           => $toNumber($kurs),
            'dpp'            => $toNumber($dpp),
            'keterangan'     => $keterangan,
            'inputdate'      => date('Y-m-d H:i:s')
        ];

        try {
            if ($existing) {
                // =====================================================
                // UPDATE HEADER TEMPORARY YANG SUDAH ADA
                // =====================================================
                $idurut = $existing['idurut'];

                $updated = $this->db
                    ->table('sc_tmp.penerimaankb')
                    ->where('idurut', $idurut)
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update($data);

                if (!$updated && $this->db->error()['code'] != 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal memperbarui header temporary.'
                    ]);
                }

                // total & balance dihitung dari SELURUH baris detail
                $this->recalcPenerimaanKBBalance($docno, $nama);

                return $this->response->setJSON([
                    'success' => true,
                    'reload'  => false,
                    'message' => 'Header temporary berhasil diperbarui.',
                    'data'    => [
                        'idurut' => $idurut,
                        'docno'  => $docno
                    ]
                ]);
            }

            // =========================================================
            // INSERT HEADER TEMPORARY BARU
            // =========================================================
            $data['docno']   = $docno;
            $data['status']  = 'E';
            $data['inputby'] = $nama;

            $inserted = $this->db
                ->table('sc_tmp.penerimaankb')
                ->insert($data);

            if (!$inserted) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan header temporary.'
                ]);
            }

            $idurut = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'reload'  => false,
                'message' => 'Header temporary berhasil disimpan.',
                'data'    => [
                    'idurut' => $idurut,
                    'docno'  => $docno
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'initPenerimaanKBHeader: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan header temporary.'
            ]);
        }
    }

    /**
     * Hitung ulang total & balance header Penerimaan Kas/Bank.
     *
     * Kondisi disamakan dengan Pengeluaran Kas/Bank: SELURUH baris detail
     * ikut dihitung (tanpa filter status), karena trigger finalisasi
     * (sc_tmp.penerimaankb_finalize) memindahkan semua detail ke sc_trx
     * dan trigger BANKIN membuat jurnal dari semua detail tersebut.
     *
     *   total   = SUM(kredit) - SUM(debit)
     *   balance = dpp + SUM(debit) - SUM(kredit)   => 0 bila sudah balance
     */
    private function recalcPenerimaanKBBalance($docno, $nama = '')
    {
        $docno = trim((string) $docno);

        if ($docno === '') {
            return null;
        }

        $getBalance = $this->db->table('sc_tmp.penerimaankb_dtl')
            ->select("
                COALESCE(SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('DEBIT','D') THEN nilai ELSE 0 END),0) AS total_debit,
                COALESCE(SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('KREDIT','K','CREDIT','CR') THEN nilai ELSE 0 END),0) AS total_kredit
            ")
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $totalDebit  = (float) ($getBalance['total_debit'] ?? 0);
        $totalKredit = (float) ($getBalance['total_kredit'] ?? 0);

        $headerRow = $this->db->table('sc_tmp.penerimaankb')
            ->select('dpp')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        if (!$headerRow) {
            return null;
        }

        $dpp = (float) ($headerRow['dpp'] ?? 0);

        $updateHeader = [
            'total'      => $totalKredit - $totalDebit,
            'balance'    => $dpp + $totalDebit - $totalKredit,
            'updatedate' => date('Y-m-d H:i:s')
        ];

        if ($nama !== '') {
            $updateHeader['updateby'] = $nama;
        }

        $this->db->table('sc_tmp.penerimaankb')
            ->where('docno', $docno)
            ->update($updateHeader);

        return [
            'total'       => $updateHeader['total'],
            'balance'     => $updateHeader['balance'],
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit
        ];
    }

    /**
     * Normalisasi tanggal ke format tampilan dd-mm-yyyy.
     * Menerima input dd-mm-yyyy maupun yyyy-mm-dd.
     * Format ini juga diterima oleh trigger jurnal BANKIN.
     */
    private function penerimaanKBDateDisplay($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        // yyyy-mm-dd
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $m)) {
            return sprintf('%02d-%02d-%04d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        // dd-mm-yyyy / dd/mm/yyyy
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $value, $m)) {
            return sprintf('%02d-%02d-%04d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }

        $ts = strtotime($value);

        return $ts ? date('d-m-Y', $ts) : $value;
    }

    public function getPenjualanCust()
    {
        $db   = $this->db;
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper(trim($this->request->getPost('docno')));
        $cabang     = strtoupper(trim($this->request->getPost('cabang')));
        $docdate    = trim($this->request->getPost('docdate'));
        $kdcustomer = strtoupper(trim($this->request->getPost('kdcustomer')));
        $alamatcustomer = strtoupper(trim($this->request->getPost('alamatcustomer')));
        $currcode = strtoupper(trim($this->request->getPost('currcode')));
        $kurs = strtoupper(trim($this->request->getPost('kurs')));

        if (empty($docno) || empty($cabang) || empty($kdcustomer) || empty($currcode)) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data header belum lengkap'
            ]);
        }

        $db->transStart();
        $reload = false;

        // =====================================================
        // INSERT HEADER JIKA BELUM ADA
        // =====================================================

        $cekHeader = $db->table('sc_tmp.penerimaankb')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        if ($cekHeader == 0) {

            $db->table('sc_tmp.penerimaankb')->insert([
                'docno'     => $docno,
                'cabang'    => $cabang,
                'kdcustomer'    => $kdcustomer,
                'alamatcustomer'    => $alamatcustomer,
                'currcode'    => $currcode,
                'kurs'    => $kurs,
                'docdate'   => $this->penerimaanKBDateDisplay($docdate) ?: date('d-m-Y'),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);
            $reload = true;
        }

        // =====================================================
        // UPDATE HEADER CUSTOMER
        // Catatan penting:
        //   Detail TIDAK dihapus dan TIDAK diisi di sini.
        //   Penarikan piutang customer dilakukan lewat tombol
        //   "Tarik Piutang Customer" (Finance::tarikPiutangCustomer),
        //   mengikuti pola Pengeluaran Kas/Bank (tombol Tarik).
        // =====================================================

        $updateHeader = [
            'kdcustomer'     => $kdcustomer,
            'alamatcustomer' => $alamatcustomer,
            'currcode'       => $currcode,
            'updateby'       => $nama,
            'updatedate'     => date('Y-m-d H:i:s')
        ];

        if ($kurs !== '') {
            $updateHeader['kurs'] = $kurs;
        }

        if ($docdate !== '') {
            $updateHeader['docdate'] = $this->penerimaanKBDateDisplay($docdate);
        }

        $db->table('sc_tmp.penerimaankb')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->update($updateHeader);

        // total & balance dihitung dari SELURUH detail (kondisi Pengeluaran KB)
        $this->recalcPenerimaanKBBalance($docno, $nama);

        $db->transComplete();

        if ($db->transStatus() === false) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses data'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'total'   => 0
        ]);
    }


    public function savePenerimaanKBDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        // $docnopp = strtoupper(trim($this->request->getPost('docnopp')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya

        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        // =====================================================
        // PARSER ANGKA (aman untuk format "1,000.00")
        // =====================================================
        $toNumber = static function ($value) {
            $value = str_replace(',', '', trim((string) ($value ?? '')));

            return $value === '' ? 0 : (float) $value;
        };

        // Nilai diterima: field #total (konsep Pengeluaran KB) atau dpp
        $dppPosted = trim((string) $this->request->getPost('dpp'));

        if ($dppPosted === '') {
            $dppPosted = trim((string) $this->request->getPost('total'));
        }

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.penerimaankb');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST

        if ($exists == 0) {

            $docdatePost = trim((string) $this->request->getPost('docdate'));

            $builderHeader->insert([
                'docno'          => $docno,
                'cabang'         => strtoupper(trim((string) $this->request->getPost('cabang'))),
                'docdate'        => $this->penerimaanKBDateDisplay($docdatePost) ?: date('d-m-Y'),
                'dpp'            => $toNumber($dppPosted),
                'jumlahpajak'    => $toNumber($this->request->getPost('ppn')),
                'total'          => $toNumber($this->request->getPost('total')),
                'prkkas'         => strtoupper(trim((string) $this->request->getPost('prkkas'))),
                'kdcustomer'     => strtoupper(trim((string) $this->request->getPost('kdcustomer'))),
                'nmcustomer'     => strtoupper(trim((string) $this->request->getPost('nmcustomer'))),
                'alamatcustomer' => strtoupper(trim((string) $this->request->getPost('alamatcustomer'))),
                'currcode'       => strtoupper(trim((string) $this->request->getPost('currcode'))),
                'kurs'           => $toNumber($this->request->getPost('kurs')),
                'keterangan'     => strtoupper(trim((string) $this->request->getPost('keterangan'))),
                'status'         => 'E',
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.penerimaankb_dtl');
        $insertCount = 0;
        $message = '';

        // =====================================================
        // IDENTITAS DETAIL
        // =====================================================
        $idcoa     = trim((string) $this->request->getPost('idcoa'));
        $nmcoa     = strtoupper(trim((string) $this->request->getPost('nmcoa')));
        $nobukti   = strtoupper(trim((string) $this->request->getPost('nobukti')));
        $remarks   = strtoupper(trim((string) $this->request->getPost('remarks')));
        $dk        = strtoupper(trim((string) $this->request->getPost('dk')));
        $cabangdtl = strtoupper(trim((string) $this->request->getPost('cabangdtl')));
        $nilai     = $toNumber($this->request->getPost('nilai'));

        // Normalisasi DK ke 'D' / 'K'
        $dk = in_array($dk, ['D', 'DEBIT', 'DR'], true) ? 'D' : 'K';

        // =====================================================
        // MATA UANG / KURS HEADER + NILAI AKHIR
        // Kondisi sama dengan Pengeluaran Kas/Bank:
        //   nilaiakhir = nilai x kurs (dipakai trigger jurnal BANKIN)
        // =====================================================
        $headerCurr = $db->table('sc_tmp.penerimaankb')
            ->select('currcode, kurs')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $currcode = strtoupper(trim((string) ($headerCurr['currcode'] ?? '')));
        $currcode = $currcode !== '' ? $currcode : 'IDR';

        $kurs = $toNumber($headerCurr['kurs'] ?? 0);

        if ($kurs <= 0) {
            $kurs = 1;
        }

        $nilaiakhir = round($nilai * $kurs, 2);

        $detailData = [
            'nobukti'    => $nobukti,
            'idcoa'      => $idcoa,
            'nmcoa'      => $nmcoa,
            'dk'         => $dk,
            'nilai'      => $nilai,
            'nilaiakhir' => $nilaiakhir,
            'currcode'   => $currcode,
            'kurs'       => $kurs,
            'cabang'     => $cabangdtl,
            'remarks'    => $remarks,
            'jurnalref'  => 'YES',
            'nilai_ref'  => $nilaiakhir,
            'updateby'   => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ];

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan
            // 🔹 UPDATE
            $builderDetail->where('idurut', $idurut)->update($detailData);

            $message = 'Data berhasil diupdate';

            // Total & balance header dihitung ulang di akhir method
            // (recalcPenerimaanKBBalance) memakai SELURUH baris detail.

        } else {
            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $nmcoa
                . '|' . $docno
                . '|' . $nama
                . '|' . $inputdate;

            $uniqueid  = hash('sha256', $rawUnique);


            // 🔹 INSERT
            $detailData['docno']     = $docno;
            $detailData['inputby']   = $nama;
            $detailData['inputdate'] = $inputdate;
            $detailData['uniqueid']  = $uniqueid;

            $builderDetail->insert($detailData);

            $message = 'Data berhasil disimpan';
        }


        // =====================================================
        // HITUNG ULANG TOTAL & BALANCE HEADER
        // Memakai SELURUH baris detail (kondisi sama dengan
        // Pengeluaran Kas/Bank).
        // =====================================================
        $this->recalcPenerimaanKBBalance($docno, $nama);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }




    function updatePenerimaanKB()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_finance->q_penerimaankb_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.penerimaankb');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('ka/finance/addPenerimaanKB'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('ka/finance/penerimaankb'));
        }
    }

    function showing_penerimaankbtrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_finance->q_penerimaankb_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_penerimaankbtemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_finance->q_penerimaankb_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_penerimaankb_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_finance->q_penerimaankb_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_penerimaankb_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.penerimaankb_dtl')
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

    public function delete_penerimaankb_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();

        // Ambil IDs
        $ids = $request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'success' => false,
                'message' => 'Parameter IDs tidak boleh kosong.'
            ]);
        }

        // Normalisasi menjadi array
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        // Bersihkan dan validasi IDs
        $ids = array_values(array_unique(array_filter(
            array_map(
                static fn($id) => trim((string) $id),
                $ids
            ),
            static fn($id) => $id !== ''
        )));

        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'success' => false,
                'message' => 'Tidak ada ID detail yang valid.'
            ]);
        }

        $nama = trim((string) session()->get('nama'));

        try {
            $db->transBegin();

            /*
             * Identifier baris detail Penerimaan Kas/Bank = idurut
             * (sesuai checkbox pada tabel detail), uniqueid tetap
             * didukung agar kompatibel dengan pemanggil lama.
             */
            $numericIds = array_values(array_filter(
                $ids,
                static fn($id) => ctype_digit((string) $id)
            ));

            $detailQuery = $db->table('sc_tmp.penerimaankb_dtl')
                ->select('docno, idurut, uniqueid')
                ->groupStart();

            if (!empty($numericIds)) {
                $detailQuery
                    ->whereIn('idurut', $numericIds)
                    ->orWhereIn('uniqueid', $ids);
            } else {
                $detailQuery->whereIn('uniqueid', $ids);
            }

            $detailRows = $detailQuery->groupEnd()->get()->getResultArray();

            if (empty($detailRows)) {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'success' => false,
                    'message' => 'Data detail Penerimaan Kas Bank tidak ditemukan.'
                ]);
            }

            $deleteIds = array_values(array_unique(array_map(
                static fn($row) => (string) $row['idurut'],
                $detailRows
            )));

            $affectedDocnos = array_values(array_unique(array_filter(array_map(
                static fn($row) => trim((string) ($row['docno'] ?? '')),
                $detailRows
            ))));

            $db->table('sc_tmp.penerimaankb_dtl')
                ->whereIn('idurut', $deleteIds)
                ->delete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Gagal menghapus detail Penerimaan Kas Bank.'
                );
            }

            // Hitung ulang total & balance header untuk setiap dokumen yang terpengaruh
            // (memakai SELURUH baris detail - kondisi sama dengan Pengeluaran Kas/Bank)
            foreach ($affectedDocnos as $docnoAffected) {
                $this->recalcPenerimaanKBBalance($docnoAffected, $nama);
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Gagal menghitung ulang balance Penerimaan Kas Bank.'
                );
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'success' => true,
                'message' => 'Data Penerimaan Kas Bank Detail berhasil dihapus.'
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'delete_penerimaankb_detail: ' . $e->getMessage()
            );

            return $this->response->setJSON([
                'status'  => false,
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus detail Penerimaan Kas Bank.'
            ]);
        }
    }

    public function update_status_penerimaankb_dtl()
    {
        $nama   = trim(session()->get('nama'));
        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $db = db_connect();

        try {

            $db->transBegin();

            // ambil docno berdasarkan idurut
            $detail = $db->table('sc_tmp.penerimaankb_dtl')
                ->select('docno')
                ->where('idurut', $id)
                ->get()
                ->getRowArray();

            if (!$detail) {
                throw new \Exception('Detail tidak ditemukan');
            }

            $docno = trim($detail['docno']);

            // update status detail
            $db->table('sc_tmp.penerimaankb_dtl')
                ->where('idurut', $id)
                ->update([
                    'status' => $status ?: null
                ]);

            // hitung ulang total & balance header
            // (memakai SELURUH baris detail - kondisi sama dengan Pengeluaran Kas/Bank)
            $this->recalcPenerimaanKBBalance($docno, $nama);

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal update data');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_penerimaankb_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_penerimaankb_dtl_temp_view($docno);
        $data = array();

        foreach ($list as $lm) {
            $row = array();

            // 0. Checkbox (identifier baris = idurut)
            $row[] = [
                'id'       => $lm->idurut,
                'uniqueid' => $lm->uniqueid,
                'checked'  => trim((string) $lm->status) === 'F' ? 'checked' : ''
            ];

            // 1-6. Kolom tampilan (konsep sama dengan Pengeluaran Kas/Bank)
            $row[] = $lm->nobukti;
            $row[] = trim((string) $lm->idcoa) . '  -  ' . trim((string) $lm->nmcoa);
            $row[] = $lm->remarks;
            $row[] = strtoupper(trim((string) $lm->dk));
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">' . number_format((float) $lm->nilai, 2, '.', ',') . '</div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_penerimaankb_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_finance->t_penerimaankb_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_penerimaankb_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_penerimaankb_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $checked = trim($lm->status) == 'F' ? 'checked' : '';

            $row[] = [
                'id' => $lm->idurut,
                'checked' => $checked
            ];



            $row[] = $lm->nobukti;
            //item
            $row[] = $lm->idcoa;
            $row[] = $lm->nmcoa;
            $row[] = $lm->remarks;
            $row[] = $lm->dk;
            $row[] = trim($lm->status) == 'F'
                ? '<span class="badge bg-success">DIPILIH</span>'
                : '<span class="badge bg-secondary text-dark">BELUM DIPILIH</span>';
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $data[] = $row;

        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_penerimaankb_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_finance->t_penerimaankb_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryPenerimaanKB(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' 
        AND (COALESCE(dk, '') = ''  
        OR nilai = '0.00' 
        OR COALESCE(nmcoa, '') = ''
        OR COALESCE(remarks, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_finance->q_penerimaankb_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_finance->q_penerimaankb_dtl_temp($paramdtl);
        $cek2 = $this->m_finance->q_penerimaankb_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.penerimaankb');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.K.B.2');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.K.B.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/ka/finance/addPenerimaanKB'));
        } else {
            $keterangan = trim((string) $this->request->getPost('keterangan'));
            $prkkas     = strtoupper(trim((string) $this->request->getPost('prkkas')));
            $dpp        = trim((string) $this->request->getPost('dpp'));

            // #prkkas dapat terkunci (disabled) pada UI setelah header tersimpan,
            // sehingga diambil dari header temporary bila tidak terkirim.
            if ($prkkas === '') {
                $rowPrkkas = $this->db->table('sc_tmp.penerimaankb')
                    ->select('prkkas')
                    ->where('inputby', $nama)
                    ->get()
                    ->getRowArray();

                $prkkas = strtoupper(trim((string) ($rowPrkkas['prkkas'] ?? '')));
            }

            // Field #total (konsep Pengeluaran KB) dipakai bila dpp kosong
            if ($dpp === '') {
                $dpp = trim((string) $this->request->getPost('total'));
            }

            // =====================================================
            // VALIDASI: Perkiraan Kas/Bank (prkkas) WAJIB diisi.
            // Trigger jurnal BANKIN pada sc_trx.penerimaankb_dtl akan
            // menolak detail bila header.prkkas kosong.
            // =====================================================
            if ($prkkas === '') {
                $builder_trxerror->insert([
                    'userid'      => $nama,
                    'errorcode'   => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul'       => 'I.K.B.2',
                ]);

                return redirect()->to(base_url('/ka/finance/addPenerimaanKB'));
            }

            $dpp_clean = (float) str_replace(',', '', $dpp);

            // 1) Update header dulu sebelum set status F
            $updateHeader = [
                'prkkas'     => $prkkas,
                'dpp'        => $dpp_clean,
                'keterangan' => strtoupper($keterangan),
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            // 2) Hitung total & balance dari SELURUH baris detail
            //    (kondisi sama dengan Pengeluaran Kas/Bank)
            $headerTmp = $this->db->table('sc_tmp.penerimaankb')
                ->select('docno')
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if ($headerTmp) {
                $this->recalcPenerimaanKBBalance(trim((string) $headerTmp['docno']), $nama);
            }

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.K.B.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/ka/finance/penerimaankb'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.K.B.2',
                );
                $builder_trxerror->insert($infotrxerror);

                // Catat penyebab kegagalan (mis. periode CLOSED pada modul closing)
                $dbError = $this->db->error();
                log_message(
                    'error',
                    'finalEntryPenerimaanKB gagal finalisasi: '
                    . ($dbError['message'] ?? 'penyebab tidak diketahui')
                );

                return redirect()->to(base_url('/ka/finance/addPenerimaanKB'));
            }



        }

    }



    /**
     * Laporan Jurnal Transaksi Penerimaan Kas/Bank (read only).
     * Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
     * nama customer diambil dari dokumen sc_trx.penerimaankb.
     */
    public function laporan_jurnal_transaksi_penerimaankb()
    {
        try {

            // =================================================
            // GET DOCNO
            // =================================================

            $docno = trim(
                $this->request->getPost('docno')
            );


            // =================================================
            // VALIDASI
            // =================================================

            if ($docno === '') {

                return $this->response
                    ->setJSON([

                        'status'   => false,

                        'messages' => 'Doc No Penerimaan Kas/Bank tidak ditemukan',

                        'data'     => []

                    ]);

            }


            // =================================================
            // PARAMETER QUERY
            // =================================================

            $params = "

            AND TRIM(jh.docno) = " .
                $this->db->escape($docno);


            // =================================================
            // GET DATA
            // =================================================

            $query = $this->m_finance
                ->q_laporan_jurnal_transaksi_penerimaankb($params);


            $data = $query->getResultArray();


            // =================================================
            // RESPONSE
            // =================================================

            return $this->response
                ->setJSON([

                    'status'   => true,

                    'messages' => 'Data berhasil diambil',

                    'data'     => $data

                ]);

        }
        catch (\Throwable $e) {

            log_message(
                'error',
                'laporan_jurnal_transaksi_penerimaankb: '
                . $e->getMessage()
            );


            return $this->response
                ->setJSON([

                    'status'   => false,

                    'messages' => $e->getMessage(),

                    'data'     => []

                ]);

        }

    }

    /**
     * Laporan Jurnal Transaksi Jurnal Umum Perkiraan (JUP) (read only).
     * Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
     * keterangan diambil dari dokumen asal sc_trx.jup.
     */
    public function laporan_jurnal_transaksi_jup()
    {
        try {

            // =================================================
            // GET DOCNO
            // =================================================

            $docno = trim(
                $this->request->getPost('docno')
            );


            // =================================================
            // VALIDASI
            // =================================================

            if ($docno === '') {

                return $this->response
                    ->setJSON([

                        'status'   => false,

                        'messages' => 'Doc No Jurnal Umum Perkiraan tidak ditemukan',

                        'data'     => []

                    ]);

            }


            // =================================================
            // PARAMETER QUERY
            // =================================================

            $params = "

            AND TRIM(jh.docno) = " .
                $this->db->escape($docno);


            // =================================================
            // GET DATA
            // =================================================

            $query = $this->m_finance
                ->q_laporan_jurnal_transaksi_jup($params);


            $data = $query->getResultArray();


            // =================================================
            // RESPONSE
            // =================================================

            return $this->response
                ->setJSON([

                    'status'   => true,

                    'messages' => 'Data berhasil diambil',

                    'data'     => $data

                ]);

        }
        catch (\Throwable $e) {

            log_message(
                'error',
                'laporan_jurnal_transaksi_jup: '
                . $e->getMessage()
            );


            return $this->response
                ->setJSON([

                    'status'   => false,

                    'messages' => $e->getMessage(),

                    'data'     => []

                ]);

        }

    }

    function show_penerimaankb(){
        $module = 'Penerimaan Kas/Bank';
        $table = 'sc_trx.penerimaankb';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.penerimaankb');

        //    $builder = $builder
        //         ->where('docno', $docno)
        //         ->update([
        //             'status'=> 'P',
        //             'printby' => $nama,
        //             'printdate' => date('Y-m-d H:i:s')
        //         ]);


        $enc_docno = $this->fiky_encryption->sealed($docno);

        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Bukti Jurnal";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("ka/finance/api_penerimaankb/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_penerimaankb.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno);
    }

    function api_penerimaankb(){
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
        $datamst = $this->m_finance->q_penerimaankb_master($param);
        $datadtl = $this->m_finance->q_penerimaankb_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {


            // // 🔹 Ambil nmcustomer berdasarkan kdcustomer
            $kdcustomer = trim($detail->kdcustomer);

            $customer = $this->db->query("
                SELECT nmcustomer 
                FROM sc_mst.customer 
                WHERE TRIM(kdcustomer) = ?
                LIMIT 1
            ", [$kdcustomer])->getRow();

            // 🔹 Set ke object detail
            $detail->nmcustomerdata = $customer->nmcustomer ?? '';
            $nilai = $detail->total; // dari database

            $data['total'] = $nilai;
            $data['total_terbilang'] = strtoupper($this->terbilang($nilai));
            $detail->terbilang = $data['total_terbilang'];


            $currcode = trim($detail->currcode);

            $currency = $this->db->query("
                SELECT currname 
                FROM sc_mst.currency 
                WHERE TRIM(currcode) = ?
                LIMIT 1
            ", [$currcode])->getRow();

            $cleanedCurrname = $currency->currname ?? '';
            $cleanedCurrname = str_replace('(LUAR NEGERI)', '', $cleanedCurrname);
            $cleanedCurrname = trim($cleanedCurrname);

            $detail->currname = $cleanedCurrname;

            $prkkas = trim($detail->prkkas);
            $coa = $this->db->query("
                SELECT nmcoa 
                FROM sc_mst.coa 
                WHERE TRIM(idcoa) = ?
                LIMIT 1
            ", [$prkkas])->getRow();

            if ($coa !== null) {
                $cleanedPrkkas = trim($coa->nmcoa);
            } else {
                $cleanedPrkkas = '';
            }

            $detail->perkkasname = $cleanedPrkkas;


            $detail->namauser = $nama;

        }

        $detailRows = $datadtl->getResultArray();

        // minimum row yang ingin ditampilkan
        $minRow = 20;

        // hitung kekurangan
        $kurang = $minRow - count($detailRows);

        // =========================
        // DATA ASLI = dummy 0
        // =========================
        foreach ($detailRows as &$row) {
            $row['dummy'] = 0;
        }

        // =========================
        // TAMBAHKAN ROW KOSONG
        // =========================
        if ($kurang > 0) {

            for ($i = 0; $i < $kurang; $i++) {

                $detailRows[] = [

                    // penanda row kosong
                    'dummy'        => 1,

                    // field existing
                    'idcoa'        => '',
                    'nobukti'      => '',
                    'remarks'   => '',
                    'dk'        => '',
                    'kredit'       => '',
                    'nilai'        => ''

                ];
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
                'detail' => $detailRows,
            ), JSON_PRETTY_PRINT);
    }





    // ============== PENGELUARAN KAS BANK ====================================

    public function pengeluarankb()
    {
        $data['title']="Pengeluaran Kas/Bank";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.B.3'; $versirelease='I.K.B.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.B.3'";
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
        $dtl = $this->m_finance->q_pengeluarankb_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('ka/finance/clearfinalPengeluaranKB');
            $urlnext = base_url('ka/finance/addPengeluaranKB');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.K.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/finance/v_list_pengeluarankb',$data);
    }

    function detailPengeluaranKB()
    {
        /* Penambahan Squence */
        $data['title']="Detail Pengeluaran Kas/Bank";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        }
        $kodemenu='I.K.B.3'; $versirelease='I.K.B.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.K.B.3'";
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
        $data['dtldata'] = $this->m_finance->q_pengeluarankb_master($param)->getRowArray();
        return $this->template->render('finance/finance/v_detail_pengeluarankb',$data);
    }

    public function list_pengeluarankb()
    {
        $list = $this->m_finance->get_t_front_pengeluarankb_view();
        $data = array();

        $no = (int) ($_POST['start'] ?? 0);

        $kmenu = 'I.K.B.3';
        $nama  = trim($this->session->get('nama'));
        $role  = trim($this->session->get('roleid'));

        $akses = $this->m_role
            ->detail_user_akses($role, $kmenu)
            ->getRowArray();

        $canUpdate = isset($akses['a_update'])
            && trim($akses['a_update']) === 't';

        $canPrint = isset($akses['a_report'])
            && trim($akses['a_report']) === 't';

        $canView = isset($akses['a_view'])
            && trim($akses['a_view']) === 't';

        foreach ($list as $lm) {
            $no++;

            $row = array();

            $status = strtoupper(trim(
                $lm->status_desc ?? $lm->status ?? ''
            ));

            $docno = trim($lm->docno ?? '');
            $docnoHex = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn = '';
            $dropdownMenu = '';

            // =====================================
            // TOMBOL BERDASARKAN HAK AKSES
            // =====================================
            if (
                $canUpdate &&
                $status !== 'REVISION/EDITING' &&
                $status !== 'APPROVED' &&
                $status !== 'CETAK/PRINT'
            ) {
                $updateBtn =
                    '<a class="dropdown-item bg-warning"
                    href="' . base_url('ka/finance/updatePengeluaranKB')
                    . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                    onclick="return confirm('
                    . htmlspecialchars(
                        json_encode(
                            'Update Pengeluaran Kas/Bank: ' . $docno
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    )
                    . ')">
                    <i class="fa fa-edit"></i>
                    Update Pengeluaran Kas/Bank
                </a>';
            }

            if ($canView) {
                $detailBtn =
                    '<a class="dropdown-item"
                    style="background-color:#3badf6;"
                    href="' . base_url('ka/finance/detailPengeluaranKB')
                    . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">
                    <i class="fa fa-eye"></i>
                    Detail Pengeluaran Kas/Bank
                </a>';
            }

            if ($canPrint) {
                $printBtn =
                    '<a class="dropdown-item"
                    style="background-color:#00ff8e;"
                    href="' . base_url('ka/finance/show_pengeluarankb')
                    . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">
                    <i class="fa fa-print"></i>
                    Preview Pengeluaran Kas/Bank
                </a>';
            }

            // =====================================
            // MENU DROPDOWN
            // =====================================
            if ($status === 'CETAK/PRINT') {
                if ($canView) {
                    $dropdownMenu .= $detailBtn;
                }

                if ($canPrint) {
                    $dropdownMenu .= $printBtn;
                }
            } else {
                if ($canUpdate) {
                    $dropdownMenu .= $updateBtn;
                }

                if ($canPrint) {
                    $dropdownMenu .= $printBtn;
                }

                if ($canView) {
                    $dropdownMenu .= $detailBtn;
                }
            }

            if ($dropdownMenu !== '') {
                $dropdownMenu =
                    '<div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="dropdown-menu">'
                    . $dropdownMenu .
                    '</div>
                </div>';
            }

            // =====================================
            // BADGE STATUS
            // =====================================
            switch ($status) {
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
                    $badgeClass = 'badge-success';
                    break;

                default:
                    $badgeClass = 'badge-primary';
                    break;
            }

            // =====================================
            // DATA ROW
            // Urutan harus sama dengan header HTML
            // =====================================
            $row[] = $no;
            $row[] = $dropdownMenu;
            $row[] = $lm->docno ?? '';
            $row[] = !empty($lm->docdate)
                ? date('d-m-Y', strtotime(trim($lm->docdate)))
                : '';

            $row[] = $lm->kdsupplier ?? '';
            $row[] = $lm->nmsuppliernew ?? '';
            $row[] = $lm->alamatsupplier ?? '';
            $row[] = $lm->nmkota ?? '';
            $row[] = $lm->currcode ?? '';
            $row[] = $lm->kurs ?? '';
            $row[] = $lm->prkkas ?? '';
            $row[] = $lm->nmcoa ?? '';
            $row[] = $lm->total ?? '';
            $row[] = $lm->keterangan ?? '';
            $row[] = $lm->nmbranch ?? '';

            $row[] =
                '<div class="text-center">
                <span class="badge ' . $badgeClass . ' w-100">'
                . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') .
                '</span>
            </div>';

            $data[] = $row;
        }

        // =====================================
        // RESPONS DATATABLES
        // =====================================
        $output = array(
            'draw' => (int) ($_POST['draw'] ?? 0),
            'recordsTotal' =>
                $this->m_finance->t_front_pengeluarankb_view_count_all(),
            'recordsFiltered' =>
                $this->m_finance->t_front_pengeluarankb_view_count_filtered(),
            'data' => $data
        );

        echo $this->fiky_encryption->jDatatable($output);
    }



    function clearEntryPengeluaranKB()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_finance->q_pengeluarankb_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('ka/finance/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.pengeluarankb');
        $builder_dtl = $this->db->table('sc_tmp.pengeluarankb_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('ka/finance/pengeluarankb'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        }

    }

    function addPengeluaranKB()
    {
        /* Penambahan Squence */
        $data['title']="Input Pengeluaran Kas/Bank";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.B.3'; $versirelease='I.K.B.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.K.B.3'";
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
        $data['mst'] = $this->m_finance->q_pengeluarankb_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_finance->q_pengeluarankb_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('finance/finance/v_add_pengeluarankb',$data);
    }


    public function getBranchInfoPengeluaranKB()
    {
        // ==========================================
        // AMBIL ID BRANCH
        // ==========================================
        $idbranch = trim((string) $this->request->getGet('idbranch'));

        // ==========================================
        // VALIDASI
        // ==========================================
        if ($idbranch === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Branch wajib dipilih.'
            ]);
        }

        // ==========================================
        // AMBIL DATA BRANCH
        // ==========================================
        $row = $this->db
            ->table('sc_mst.branchjob')
            ->select('idbranch, nmbranch')
            ->where('idbranch', $idbranch)
            ->get()
            ->getRowArray();

        // ==========================================
        // CEK BRANCH
        // ==========================================
        if (!$row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang tidak ditemukan.'
            ]);
        }

        // ==========================================
        // MAPPING NAMA BRANCH
        // ==========================================
        $map = [
            'PT JATIM TAMAN STEEL MFG' => 'PT',
            'PLANT I'                  => 'PA',
            'PLANT II'                 => 'PB',
        ];

        $nmbranch   = trim((string) $row['nmbranch']);
        $kodeSuffix = $map[$nmbranch] ?? '';

        // ==========================================
        // CEK MAPPING
        // ==========================================
        if ($kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mapping cabang belum diset.'
            ]);
        }

        // ==========================================
        // AMBIL KONFIGURASI
        // ==========================================
        // $konfigurasi = $this->db
        //     ->table('sc_mst.konfigurasi')
        //     ->get()
        //     ->getResultArray();

        // ==========================================
        // AMBIL KONFIGURASI UMUM
        // ==========================================
        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray();

        // ==========================================
        // LOGIN DATE
        // ==========================================
        $logindate = trim((string) $this->session->get('logindate'));

        // ==========================================
        // FORMAT INFIX
        // ==========================================
        $infix = '';

        if ($logindate !== '') {
            $infix = date('ym', strtotime($logindate));
        }

        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['kaskeluar']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        // ==========================================
        // RESPONSE
        // ==========================================
        return $this->response->setJSON([
            'success' => true,

            // BRANCH
            'idbranch'    => trim($row['idbranch']),
            'nmbranch'    => $nmbranch,
            'kode_suffix' => $kodeSuffix,
            'infix'     => $infix,

            // PERIODE
            'logindate' => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
            // KONFIGURASI
            // 'konfigurasi' => $konfigurasi,
        ]);
    }

    public function getNextSuffixPengeluaranKB()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.pengeluarankb')
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

    public function initPengeluaranKBHeader()
    {
        $nama = trim((string) $this->session->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User login tidak ditemukan.'
            ]);
        }

        // =========================================================
        // AMBIL DATA HEADER DARI POST
        // =========================================================
        $docno         = strtoupper(trim((string) $this->request->getPost('docno')));
        $docdate       = trim((string) $this->request->getPost('docdate'));
        $cabang        = trim((string) $this->request->getPost('cabang'));
        $pemohon       = $nama;
        $kdsupplier    = trim((string) $this->request->getPost('kdsupplier'));
        $nmsupplier    = trim((string) $this->request->getPost('nmsupplier'));
        $alamatsupplier = trim((string) $this->request->getPost('alamatsupplier'));
        $prkkas        = trim((string) $this->request->getPost('prkkas'));
        $np            = trim((string) $this->request->getPost('np'));
        $jthtempo      = $this->request->getPost('jthtempo');
        $isinclusive   = trim((string) $this->request->getPost('isinclusive'));
        $currcode      = strtoupper(trim((string) $this->request->getPost('currcode')));
        $kurs          = $this->request->getPost('kurs');
        $dpp           = $this->request->getPost('dpp');
        $jumlahpajak   = $this->request->getPost('jumlahpajak');
        $total         = $this->request->getPost('total');
        $keterangan    = trim((string) $this->request->getPost('keterangan'));
        $balance       = $this->request->getPost('balance');
        $docnotmp      = trim((string) $this->request->getPost('docnotmp'));

        // =========================================================
        // VALIDASI HEADER MINIMAL
        // =========================================================
        if ($docno === '' || $docdate === '' || $cabang === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor dokumen, tanggal, dan cabang wajib diisi.'
            ]);
        }
        // =========================================================
        // CEK DOCNO SUDAH DIGUNAKAN DI TRANSAKSI DETAIL
        // =========================================================
        $docnoTrx = $this->db
            ->table('sc_trx.pengeluarankb')
            ->where('docno', $docno)
            ->countAllResults();

        if ($docnoTrx > 0) {
            return $this->response->setJSON([
                'success' => false,
                'reload'  => false,
                'message' => 'Nomor dokumen ' . $docno .
                    ' sudah digunakan pada transaksi detail. Input header ditolak.'
            ]);
        }
        // =========================================================
        // KONVERSI ANGKA
        // =========================================================
        $toNumber = static function ($value, $default = 0) {
            if ($value === null || trim((string) $value) === '') {
                return $default;
            }

            return (float) str_replace(',', '', (string) $value);
        };

        $jthtempo    = $toNumber($jthtempo);
        $kurs        = $toNumber($kurs, 1);
        $dpp         = $toNumber($dpp);
        $jumlahpajak = $toNumber($jumlahpajak);
        $total       = $toNumber($total);
        $balance     = $toNumber($balance);

        // =========================================================
// CEK HEADER TEMP MILIK USER
// =========================================================
        $builder = $this->db->table('sc_tmp.pengeluarankb');

        $existing = $builder
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->where('status', 'E')
            ->get()
            ->getRowArray();

// =========================================================
// DATA HEADER
// =========================================================
        $data = [
            'docdate'        => $docdate,
            'cabang'         => $cabang,
            'pemohon'        => $pemohon,
            'kdsupplier'     => $kdsupplier,
            'nmsupplier'     => $nmsupplier,
            'alamatsupplier' => $alamatsupplier,
            'prkkas'         => $prkkas,
            'np'             => $np,
            'jthtempo'       => $jthtempo,
            'isinclusive'    => $isinclusive,
            'currcode'       => $currcode !== '' ? $currcode : 'IDR',
            'kurs'           => $kurs,
            'dpp'            => $dpp,
            'jumlahpajak'    => $jumlahpajak,
            'total'          => $total,
            'keterangan'     => $keterangan,
            'balance'        => $balance,
            'inputdate'      => date('Y-m-d H:i:s'),
            'docnotmp'       => $docnotmp !== '' ? $docnotmp : null
        ];

        try {
            if ($existing) {
                // =====================================================
                // UPDATE HEADER TEMPORARY YANG SUDAH ADA
                // =====================================================
                $idurut = $existing['idurut'];

                $updated = $this->db
                    ->table('sc_tmp.pengeluarankb')
                    ->where('idurut', $idurut)
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update($data);

                if (!$updated && $this->db->error()['code'] != 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal memperbarui header temporary.'
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'reload'  => false,
                    'message' => 'Header temporary berhasil diperbarui.',
                    'data'    => [
                        'idurut' => $idurut,
                        'docno'  => $docno
                    ]
                ]);
            }

            // =========================================================
            // INSERT HEADER TEMPORARY BARU
            // =========================================================
            $data['docno']     = $docno;
            $data['status']    = 'E';
            $data['inputby']   = $nama;

            $inserted = $this->db
                ->table('sc_tmp.pengeluarankb')
                ->insert($data);

            if (!$inserted) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan header temporary.'
                ]);
            }

            $idurut = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'reload'  => true,
                'message' => 'Header temporary berhasil disimpan.',
                'data'    => [
                    'idurut' => $idurut,
                    'docno'  => $docno
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'initPengeluaranKBHeader: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan header.'
            ]);
        }
    }

    /* TANDA TERIMA LOAD LPB */

    public function tarikPenerimaanBarangSupplier()
    {
        $docno      = trim($this->request->getPost('docno'));
        $kdsupplier = trim($this->request->getPost('kdsupplier'));
        $nama = trim(
            (string) $this->session->get('nama')
        );

        if ($docno == '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Nomor Tanda Terima belum tersedia.'
            ]);
        }

        if ($kdsupplier == '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Supplier belum dipilih.'
            ]);
        }

        /*
         * Relasi:
         *
         * jurnal_dt.source_uniqueid
         *          =
         * transaction_dt.uniqueid
         *
         * Yang diambil hanya jurnal_dt.kredit.
         *
         * Summary:
         * 1 LPB + 1 COA = 1 baris
         *
         * Keterangan diambil dari transaction_dt.namabarang
         * dan digabung menggunakan " / ".
         */


        $sql = "SELECT
    TRIM(jd.ref_docno)   AS nobukti,
    TRIM(jd.idcoa)       AS noperkiraan,
    TRIM(td.kdsupplier)  AS kdsupplier,
    TRIM(td.nsupplier)   AS nmsupplier,
    STRING_AGG(
        DISTINCT NULLIF(TRIM(td.namabarang), ''),
        ' / '
        ORDER BY NULLIF(TRIM(td.namabarang), '')
    ) AS keterangan,
    SUM(COALESCE(jd.kredit, 0))
      - COALESCE(MAX(penggunaan.total_digunakan), 0) AS nilai
FROM sc_trx.jurnal_dt jd
INNER JOIN sc_trx.jurnal_hd jh
    ON jh.id = jd.jurnal_id
INNER JOIN sc_trx.transaction_dt td
    ON TRIM(td.uniqueid) = TRIM(jd.source_uniqueid)
LEFT JOIN (
    SELECT
        TRIM(nobukti)     AS nobukti,
        TRIM(noperkiraan) AS noperkiraan,
        SUM(COALESCE(nilai, 0)) AS total_digunakan
    FROM sc_trx.jurnal_hd_penggunaan
    GROUP BY
        TRIM(nobukti),
        TRIM(noperkiraan)
) penggunaan
    ON  penggunaan.nobukti     = TRIM(jd.ref_docno)
    AND penggunaan.noperkiraan = TRIM(jd.idcoa)
WHERE TRIM(td.journal_type) = 'GRNREC'
  AND TRIM(td.kdsupplier)   = ?
  AND COALESCE(jd.kredit, 0) > 0
  AND TRIM(jh.status)       = 'POSTED'
  AND TRIM(jd.status)       = 'POSTED'
GROUP BY
    TRIM(jd.ref_docno),
    TRIM(jd.idcoa),
    TRIM(td.kdsupplier),
    TRIM(td.nsupplier),
    penggunaan.total_digunakan
HAVING
    SUM(COALESCE(jd.kredit, 0))
      - COALESCE(MAX(penggunaan.total_digunakan), 0) > 0
ORDER BY
    TRIM(jd.ref_docno),
    TRIM(jd.idcoa); ";

        $rows = $this->db->query($sql, [$kdsupplier])->getResultArray();

        /*
         * Tidak ada data penerimaan
         */
        if (empty($rows)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data penerimaan untuk supplier tersebut tidak ada.',
                'data'    => []
            ]);
        }

        $this->db->transBegin();

        try {

            $inserted = 0;
            $skipped  = 0;

            /*
             * Mata uang / kurs header temporary dipakai untuk menghitung
             * nilaiakhir, sama seperti detail yang diinput manual.
             */
            $headerRow = $this->db->table('sc_tmp.pengeluarankb')
                ->select('currcode, kurs')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            $currcode = strtoupper(trim((string) ($headerRow['currcode'] ?? '')));
            $currcode = $currcode !== '' ? $currcode : 'IDR';

            $kurs = (float) ($headerRow['kurs'] ?? 0);
            if ($kurs <= 0) {
                $kurs = 1;
            }

            $coaCache = [];

            foreach ($rows as $row) {

                $nobukti     = trim($row['nobukti']);
                $noperkiraan = trim($row['noperkiraan']);
                $nilai       = (float) $row['nilai'];
                $keterangan  = trim($row['keterangan'] ?? '');

                if ($nobukti === '' || $noperkiraan === '' || $nilai <= 0) {
                    continue;
                }

                /*
                 * 1 Tanda Terima + 1 LPB + 1 COA
                 */
                $idunique = md5(
                    $docno
                    . '|'
                    . $nobukti
                    . '|'
                    . $noperkiraan
                );

                /*
                 * Jangan insert data yang sama dua kali.
                 */
                $cek = $this->db
                    ->table('sc_tmp.pengeluarankb_dtl')
                    ->where('docno', $docno)
                    ->where('nobukti', $nobukti)
                    ->where('idcoa', $noperkiraan)
                    ->countAllResults();

                if ($cek > 0) {
                    $skipped++;
                    continue;
                }

                /*
                 * Nama perkiraan (nmcoa) untuk tampilan detail.
                 */
                if (!array_key_exists($noperkiraan, $coaCache)) {
                    $coaRow = $this->db->query(
                        "SELECT COALESCE(BTRIM(nmcoa::TEXT), '') AS nmcoa
                           FROM sc_mst.coa
                          WHERE BTRIM(idcoa::TEXT) = ?",
                        [$noperkiraan]
                    )->getRowArray();

                    $coaCache[$noperkiraan] = $coaRow ? trim((string) $coaRow['nmcoa']) : '';
                }

                $nilaiakhir = round($nilai * $kurs, 2);

                $this->db
                    ->table('sc_tmp.pengeluarankb_dtl')
                    ->insert([
                        'docno'      => $docno,
                        'nobukti'    => $nobukti,
                        'idcoa'      => $noperkiraan,
                        'nmcoa'      => $coaCache[$noperkiraan],
                        'remarks'    => '(' . $nobukti . ') ' . $keterangan,
                        'dk'         => 'D',
                        'cabang'     => '',
                        'nilai'      => $nilai,
                        'nilaiakhir' => $nilaiakhir,
                        'currcode'   => $currcode,
                        'kurs'       => $kurs,
                        'uniqueid'   => $idunique,
                        'status'     => 'E',
                        'inputby'    => $nama,
                        'inputdate'  => date('Y-m-d H:i:s'),
                        'jurnalref'  => 'YES',
                        'nilai_ref'  => $nilaiakhir,
                    ]);

                $inserted++;
            }

            /* Hitung ulang total & balance header dari seluruh detail. */
            $this->recalcPengeluaranKBBalance($docno, $nama);

            if ($this->db->transStatus() === false) {
                throw new \Exception(
                    'Gagal menyimpan detail Tanda Terima.'
                );
            }

            $this->db->transCommit();

            /*
             * Semua data sudah pernah ditarik
             */
            if ($inserted == 0) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data penerimaan sudah pernah ditarik.',
                    'data'    => []
                ]);
            }

            return $this->response->setJSON([
                'status'   => true,
                'message'  => 'Penerimaan berhasil ditarik.',
                'inserted' => $inserted,
                'skipped'  => $skipped,
                'data'     => $rows
            ]);

        } catch (\Throwable $e) {

            $this->db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Hitung ulang total & balance header temporary Pengeluaran Kas/Bank
     * dari seluruh baris detail (sc_tmp.pengeluarankb_dtl).
     * Dipakai setelah proses "Tarik Penerimaan Barang Supplier".
     */
    private function recalcPengeluaranKBBalance($docno, $nama = '')
    {
        $docno = trim((string) $docno);

        if ($docno === '') {
            return null;
        }

        $getBalance = $this->db->table('sc_tmp.pengeluarankb_dtl')
            ->select("
                COALESCE(SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('DEBIT','D','DR') THEN nilai ELSE 0 END),0) AS total_debit,
                COALESCE(SUM(CASE WHEN UPPER(TRIM(COALESCE(dk,''))) IN ('KREDIT','K','CREDIT','CR') THEN nilai ELSE 0 END),0) AS total_kredit
            ")
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $totalDebit  = (float) ($getBalance['total_debit'] ?? 0);
        $totalKredit = (float) ($getBalance['total_kredit'] ?? 0);

        $headerRow = $this->db->table('sc_tmp.pengeluarankb')
            ->select('dpp')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        if (!$headerRow) {
            return null;
        }

        $dpp = (float) ($headerRow['dpp'] ?? 0);

        // total = debit - kredit ; balance = dpp + kredit - debit
        $total   = $totalDebit - $totalKredit;
        $balance = $dpp + $totalKredit - $totalDebit;

        $updateHeader = [
            'total'      => $total,
            'balance'    => $balance,
            'updatedate' => date('Y-m-d H:i:s'),
        ];

        if ($nama !== '') {
            $updateHeader['updateby'] = $nama;
        }

        $this->db->table('sc_tmp.pengeluarankb')
            ->where('docno', $docno)
            ->update($updateHeader);

        return [
            'total'        => $total,
            'balance'      => $balance,
            'total_debit'  => $totalDebit,
            'total_kredit' => $totalKredit,
        ];
    }

    /*
     * =====================================================================
     * TARIK PIUTANG CUSTOMER - PENERIMAAN KAS/BANK
     * =====================================================================
     * Konsep sama dengan tombol "Tarik Pengeluaran Kas Bank" pada Pengeluaran
     * Kas/Bank, perbedaannya:
     *   - Pengeluaran KB menarik HUTANG (AP) supplier dari LPB.
     *   - Penerimaan KB menarik PIUTANG (AR) customer dari dokumen penjualan
     *     (input /sales/postsales/penjualan) yang sudah final (status 'F').
     *
     * Sumber angka = dokumen penjualan, karena modul Penjualan tidak menulis
     * jurnal (sc_trx.jurnal_dt / sc_trx.transaction_dt):
     *   sc_trx.penjualan.total
     *   - sc_trx.penerimaankb_dtl (status 'F', dk KREDIT) yang sudah memakai
     *     piutang tersebut pada dokumen Penerimaan Kas/Bank sebelumnya.
     *
     * COA piutang diambil dari sc_mst.currency.ppiutang sesuai currcode
     * dokumen penjualan.
     * Hasil tarik masuk ke sc_tmp.penerimaankb_dtl dengan dk = 'KREDIT'
     * (meng-kredit piutang karena kas/bank bertambah) dan status 'E'
     * sehingga harus dicentang (checked) dulu oleh user.
     */

    /**
     * Ambil baris sisa piutang customer dari dokumen penjualan yang final.
     */
    private function piutangCustomerRows($kdcustomer, $currcode = '')
    {
        $kdcustomer = strtoupper(trim($kdcustomer));
        $currcode   = strtoupper(trim($currcode));

        if ($kdcustomer === '') {
            return [];
        }

        /*
         * Sumber piutang = dokumen penjualan yang sudah final
         * (sc_trx.penjualan, hasil input /sales/postsales/penjualan).
         *
         * Modul Penjualan tidak menulis jurnal (jurnal_dt / transaction_dt),
         * jadi sisa piutang dihitung dari:
         *   sc_trx.penjualan.total
         *   - nilai yang sudah dipakai dokumen Penerimaan Kas/Bank lain
         *     (sc_trx.penerimaankb_dtl dengan status 'F' dan dk KREDIT)
         *
         * COA piutang diambil dari sc_mst.currency.ppiutang sesuai
         * currcode masing-masing dokumen penjualan.
         */
        $sql = "SELECT
                    TRIM(p.docno)                                            AS nobukti,
                    TRIM(COALESCE(cur.ppiutang, ''))                         AS noperkiraan,
                    COALESCE(TRIM(coa.nmcoa), '')                            AS namaperkiraan,
                    TRIM(p.kdcustomer)                                       AS kdcustomer,
                    COALESCE(TRIM(cust.nmcustomer), '')                      AS nmcustomer,
                    COALESCE(TRIM(p.keterangan), '')                         AS keterangan,
                    TRIM(COALESCE(p.cabang, ''))                             AS cabang,
                    COALESCE(p.total, 0) - COALESCE(used.total_digunakan, 0) AS nilai
                FROM sc_trx.penjualan p
                LEFT JOIN sc_mst.currency cur
                       ON TRIM(cur.currcode) = TRIM(COALESCE(p.currcode, ''))
                LEFT JOIN sc_mst.coa coa
                       ON TRIM(coa.idcoa) = TRIM(COALESCE(cur.ppiutang, ''))
                LEFT JOIN sc_mst.customer cust
                       ON TRIM(cust.kdcustomer) = TRIM(p.kdcustomer)
                LEFT JOIN (
                    SELECT
                        TRIM(nobukti)           AS nobukti,
                        SUM(COALESCE(nilai, 0)) AS total_digunakan
                    FROM sc_trx.penerimaankb_dtl
                    WHERE UPPER(TRIM(COALESCE(dk, ''))) IN ('KREDIT', 'K')
                    GROUP BY TRIM(nobukti)
                ) used
                       ON used.nobukti = TRIM(p.docno)
                WHERE TRIM(COALESCE(p.kdcustomer, '')) = ?
                  AND TRIM(COALESCE(p.status, '')) = 'F'
                  AND COALESCE(TRIM(cur.ppiutang), '') <> ''
                  AND ( ? = '' OR TRIM(COALESCE(p.currcode, '')) = ? )
                  AND COALESCE(p.total, 0) - COALESCE(used.total_digunakan, 0) > 0
                ORDER BY TRIM(p.docno)";

        return $this->db->query($sql, [$kdcustomer, $currcode, $currcode])->getResultArray();
    }

    /**
     * Simpan baris piutang hasil tarik ke detail sementara Penerimaan KB.
     * 1 dokumen + 1 bukti (invoice) + 1 COA = 1 baris, tidak dobel.
     */
    private function insertPiutangCustomerDetail($docno, $rows, $nama)
    {
        $inserted = 0;
        $skipped  = 0;

        // =====================================================
        // MATA UANG / KURS HEADER
        // Dipakai untuk nilaiakhir = nilai x kurs, sama seperti
        // detail yang diinput manual pada Pengeluaran Kas/Bank.
        // =====================================================
        $headerRow = $this->db->table('sc_tmp.penerimaankb')
            ->select('currcode, kurs')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $currcode = strtoupper(trim((string) ($headerRow['currcode'] ?? '')));
        $currcode = $currcode !== '' ? $currcode : 'IDR';

        $kurs = (float) ($headerRow['kurs'] ?? 0);

        if ($kurs <= 0) {
            $kurs = 1;
        }

        foreach ($rows as $row) {
            $nobukti    = trim((string) ($row['nobukti'] ?? ''));
            $idcoa      = trim((string) ($row['noperkiraan'] ?? ''));
            $nilai      = (float) ($row['nilai'] ?? 0);
            $keterangan = trim((string) ($row['keterangan'] ?? ''));

            if ($nobukti === '' || $idcoa === '' || $nilai <= 0) {
                continue;
            }

            $cek = $this->db->table('sc_tmp.penerimaankb_dtl')
                ->where('docno', $docno)
                ->where('nobukti', $nobukti)
                ->where('idcoa', $idcoa)
                ->countAllResults();

            if ($cek > 0) {
                $skipped++;
                continue;
            }

            $nilaiakhir = round($nilai * $kurs, 2);

            $this->db->table('sc_tmp.penerimaankb_dtl')->insert([
                'docno'      => $docno,
                'nobukti'    => $nobukti,
                'idcoa'      => $idcoa,
                'nmcoa'      => trim((string) ($row['namaperkiraan'] ?? '')),
                'remarks'    => '(' . $nobukti . ') ' . $keterangan,
                'dk'         => 'K',
                'cabang'     => trim((string) ($row['cabang'] ?? '')),
                'nilai'      => $nilai,
                'nilaiakhir' => $nilaiakhir,
                'currcode'   => $currcode,
                'kurs'       => $kurs,
                'jurnalref'  => 'YES',
                'nilai_ref'  => $nilaiakhir,
                'status'     => 'E',
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s'),
                'uniqueid'   => md5($docno . '|' . $nobukti . '|' . $idcoa),
            ]);

            $inserted++;
        }

        return [$inserted, $skipped];
    }

    /**
     * TARIK PIUTANG CUSTOMER (tombol Tarik pada Penerimaan Kas/Bank).
     */
    public function tarikPiutangCustomer()
    {
        $docno      = strtoupper(trim((string) $this->request->getPost('docno')));
        $kdcustomer = strtoupper(trim((string) $this->request->getPost('kdcustomer')));
        $currcode   = strtoupper(trim((string) $this->request->getPost('currcode')));
        $nama       = trim((string) $this->session->get('nama'));

        if ($docno === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Nomor bukti Penerimaan Kas/Bank belum tersedia.'
            ]);
        }

        if ($kdcustomer === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Customer belum dipilih.'
            ]);
        }

        $rows = $this->piutangCustomerRows($kdcustomer, $currcode);

        if (empty($rows)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Tidak ada sisa piutang customer tersebut (dokumen penjualan belum final atau sudah lunas).',
                'data'    => []
            ]);
        }

        $this->db->transBegin();

        try {
            [$inserted, $skipped] = $this->insertPiutangCustomerDetail($docno, $rows, $nama);

            // Hitung ulang total & balance header dari seluruh detail
            $this->recalcPenerimaanKBBalance($docno, $nama);

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan detail piutang customer.');
            }

            $this->db->transCommit();

            if ($inserted === 0) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data piutang customer sudah pernah ditarik.',
                    'data'    => []
                ]);
            }

            return $this->response->setJSON([
                'status'   => true,
                'message'  => 'Piutang customer berhasil ditarik.',
                'inserted' => $inserted,
                'skipped'  => $skipped,
                'data'     => $rows
            ]);
        } catch (\Throwable $e) {
            $this->db->rollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getPembelianSup()
    {
        $db   = $this->db;
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper(trim($this->request->getPost('docno')));
        $cabang     = strtoupper(trim($this->request->getPost('cabang')));
        $docdate    = trim($this->request->getPost('docdate'));
        $kdsupplier = strtoupper(trim($this->request->getPost('kdsupplier')));
        $alamatsupplier = strtoupper(trim($this->request->getPost('alamatsupplier')));
        $currcode = strtoupper(trim($this->request->getPost('currcode')));
        $kurs = strtoupper(trim($this->request->getPost('kurs')));

        if (empty($docno) || empty($cabang) || empty($kdsupplier) || empty($currcode)) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data header belum lengkap'
            ]);
        }

        $db->transStart();
        $reload = false;

        // =====================================================
        // INSERT HEADER JIKA BELUM ADA
        // =====================================================

        $cekHeader = $db->table('sc_tmp.pengeluarankb')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        if ($cekHeader == 0) {

            $db->table('sc_tmp.pengeluarankb')->insert([
                'docno'     => $docno,
                'cabang'    => $cabang,
                'kdsupplier'    => $kdsupplier,
                'alamatsupplier'    => $alamatsupplier,
                'currcode'    => $currcode,
                'kurs'    => $kurs,
                'docdate'   => date('Y-m-d', strtotime($docdate)),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);
            $reload = true;
        }

        // =====================================================
        // HAPUS DETAIL LAMA
        // =====================================================

        $db->table('sc_tmp.pengeluarankb_dtl')
            ->where('docno', $docno)
            ->delete();

        // =====================================================
        // AMBIL DATA PEMBELIAN
        /*
         *
         * SELECT p.docno, p.nilaikonversi, p.nilaipajak, p.nilaikonversi + p.nilaipajak AS bb, p.keterangan, j.idcoa, j.kredit, 'D' as dk
FROM (
    SELECT TRIM(a.docno) AS docno, SUM(a.nilaikonversi) AS nilaikonversi, SUM(a.nilaipajak) AS nilaipajak, CONCAT_WS(' / ', MAX(TRIM(a.nmbarang)), MAX(TRIM(a.descriptionpo)), MAX(TRIM(b.nmsupplier)), MAX(TRIM(b.keterangan))) AS keterangan
    FROM sc_trx.lpb_dtl a
    LEFT JOIN sc_trx.lpb b ON TRIM(a.docno) = TRIM(b.docno)
    WHERE TRIM(b.kdsupplier) = 'S-1234'
    GROUP BY TRIM(a.docno)
) p
LEFT JOIN (
    SELECT TRIM(ref_docno) AS docno, MAX(idcoa) AS idcoa, MAX(kredit) AS kredit
    FROM sc_trx.jurnal_dt
    WHERE COALESCE(kredit,0) != 0
    AND TRIM(ref_doctype) = 'GR'
    GROUP BY TRIM(ref_docno)
) j ON j.docno = p.docno
ORDER BY p.docno;
*/
        // =====================================================

        $pembelian = $db->query("
    SELECT 
        'PEMBELIAN' AS sourcetype,
        TRIM(p.docno) AS docref,
        p.docdate,
        TRIM(p.kdsupplier) AS kodepartner,
        (
            COALESCE(p.total, 0) -
            COALESCE(
                (
                    SELECT SUM(COALESCE(pkbd.nilai, 0))
                    FROM sc_trx.pengeluarankb_dtl pkbd
                    WHERE TRIM(pkbd.nobukti) = TRIM(p.docno)
                    AND TRIM(COALESCE(pkbd.status, '')) = 'F'
                ),
                0
            )
        ) AS nilaitotal,
        TRIM(p.cabang) AS cabang,
        p.keterangan
    FROM sc_trx.lpb p
    WHERE TRIM(COALESCE(p.kdsupplier, '')) = TRIM(?)
    AND TRIM(COALESCE(p.currcode, '')) = TRIM(?)
    AND TRIM(COALESCE(p.status, '')) = 'F'
    AND (
        COALESCE(p.total, 0) -
        COALESCE(
            (
                SELECT SUM(COALESCE(pkbd.nilai, 0))
                FROM sc_trx.pengeluarankb_dtl pkbd
                WHERE TRIM(pkbd.nobukti) = TRIM(p.docno)
                AND TRIM(COALESCE(pkbd.status, '')) = 'F'
            ),
            0
        )
    ) > 0

    UNION ALL

    SELECT 
        'NDK' AS sourcetype,
        TRIM(n.docno) AS docref,
        n.docdate,
        TRIM(n.kdsupplier) AS kodepartner,
        (
            COALESCE(n.total, 0) -
            COALESCE(
                (
                    SELECT SUM(COALESCE(pkbd.nilai, 0))
                    FROM sc_trx.pengeluarankb_dtl pkbd
                    WHERE TRIM(pkbd.nobukti) = TRIM(n.docno)
                    AND TRIM(COALESCE(pkbd.status, '')) = 'F'
                ),
                0
            )
        ) AS nilaitotal,
        TRIM(n.cabang) AS cabang,
        n.keterangan
    FROM sc_trx.ndk n
    WHERE TRIM(COALESCE(n.kdsupplier, '')) = TRIM(?)
    AND TRIM(COALESCE(n.currcode, '')) = TRIM(?)
    AND TRIM(COALESCE(n.status, '')) = 'F'
    AND (
        COALESCE(n.total, 0) -
        COALESCE(
            (
                SELECT SUM(COALESCE(pkbd.nilai, 0))
                FROM sc_trx.pengeluarankb_dtl pkbd
                WHERE TRIM(pkbd.nobukti) = TRIM(n.docno)
                AND TRIM(COALESCE(pkbd.status, '')) = 'F'
            ),
            0
        )
    ) > 0

    ORDER BY docdate ASC, docref ASC

", [

            // =====================================================
            // PEMBELIAN / LPB
            // =====================================================

            $kdsupplier,
            $currcode,

            // =====================================================
            // NDK
            // =====================================================

            $kdsupplier,
            $currcode

        ])->getResult();

        // =====================================================
        // INSERT DETAIL
        // =====================================================

        $currencyData = $db->query("
            SELECT 
                c.currcode,
                c.phutang
            FROM sc_mst.currency c
            WHERE trim(c.currcode) = ?
        ", [$currcode])->getRow();

        $idcoa = null;
        $nmcoa = null;

        if (!empty($currencyData) && !empty($currencyData->phutang)) {

            $idcoa = trim($currencyData->phutang);

            $coaData = $db->query("
                SELECT nmcoa
                FROM sc_mst.coa
                WHERE trim(idcoa) = ?
            ", [$idcoa])->getRow();

            if (!empty($coaData)) {
                $nmcoa = trim($coaData->nmcoa);
            }
        }

        foreach ($pembelian as $row) {

            $rawUnique =
                $docno . '|' .
                $row->docref . '|' .
                microtime(true);

            $uniqueid = hash('sha256', $rawUnique);
            $dk = 'DEBIT';

            $db->table('sc_tmp.pengeluarankb_dtl')->insert([

                'docno'        => $docno,
                'nobukti'      => $row->docref,
                'idcoa'        => $idcoa,
                'nmcoa'        => $nmcoa,
                'remarks'      => "(" . $docno . ") " . $row->keterangan,
                'nilai'        => $row->nilaitotal ?? 0,
                'dk'           => $dk,
                'cabang'           => $row->cabang,
                // 'status'       => 'F',
                'inputby'      => $nama,
                'inputdate'    => date('Y-m-d H:i:s'),
                'uniqueid'     => $uniqueid,
                'jurnalref'        => 'YES',
                'nilai_ref'    => $row->nilaitotal ?? 0
            ]);
        }

        // =====================================================
        // UPDATE HEADER CUSTOMER
        // =====================================================

        $getBalance = $db->table('sc_tmp.pengeluarankb_dtl')
            ->select("
                SUM(CASE WHEN dk='DEBIT' THEN nilai ELSE 0 END) AS total_debit,
                SUM(CASE WHEN dk='KREDIT' THEN nilai ELSE 0 END) AS total_kredit
            ")
            ->where('docno', $docno)
            ->where("TRIM(status) = 'F'", null, false)
            ->get()
            ->getRowArray();

        $totalDebit  = ($getBalance['total_debit'] ?? 0);
        $totalKredit = ($getBalance['total_kredit'] ?? 0);

        // balance = debit - kredit
        $total = $totalDebit - $totalKredit;


        $db->table('sc_tmp.pengeluarankb')
            ->where('docno', $docno)
            ->update([
                'kdsupplier'     => $kdsupplier,
                'total'     => $total,
                // 'total'     => 0,
                'updateby'        => $nama,
                'updatedate'      => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses data'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'total'   => count($pembelian)
        ]);
    }



    public function savePengeluaranKBDetail()
    {
        $db   = $this->db;
        $post = $this->request->getPost();
        $nama = trim((string) $this->session->get('nama'));
        $now  = date('Y-m-d H:i:s');

        $docno = strtoupper(trim((string) ($post['docno'] ?? '')));

        if ($docno === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No. Bukti tidak boleh kosong'
            ]);
        }

        $toNumber = static function ($value) {
            $value = str_replace(',', '', trim((string) ($value ?? '0')));

            return $value === ''
                ? 0
                : (is_numeric($value) ? (float) $value : null);
        };

        $nilai = $toNumber($post['nilai'] ?? 0);

        if ($nilai === null || $nilai <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nilai detail harus lebih besar dari 0'
            ]);
        }

        // =====================================================
        // IDENTITAS DETAIL DAN VALIDASI
        // =====================================================
        $idurut = trim((string) ($post['idurut_detail'] ?? ''));
        $uniqueid = trim((string) ($post['uniqueid'] ?? ''));

        // Kompatibilitas nama field COA dari frontend.
        $idcoa = trim((string) (
            $post['perkiraan']
            ?? $post['idperkiraan']
            ?? $post['idcoa']
            ?? ''
        ));

        $nobukti = strtoupper(trim((string) ($post['nobukti'] ?? '')));

        $nmcoa = strtoupper(trim((string) (
            $post['nmperkiraan']
            ?? $post['namaperkiraan']
            ?? $post['nmcoa']
            ?? ''
        )));

        $dk = strtoupper(trim((string) ($post['dk'] ?? '')));
        $cabangdtl = trim((string) ($post['costcenter'] ?? ''));
        $remarks = strtoupper(trim((string) ($post['keterangan_dtl'] ?? '')));

        // Mode edit juga dapat dikirim secara eksplisit oleh frontend.
        $mode = strtolower(trim((string) ($post['mode'] ?? '')));
        $isEdit = in_array($mode, ['edit', 'update'], true)
            || $idurut !== ''
            || $uniqueid !== '';

        // Edit tidak boleh berubah menjadi INSERT tanpa sengaja.
        if ($isEdit && $idurut === '' && $uniqueid === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Identitas detail untuk Edit kosong. Data tidak disimpan.'
            ]);
        }

        if ($idcoa === '' || $nmcoa === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Perkiraan dan nama perkiraan tidak boleh kosong'
            ]);
        }

        if ($remarks === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keterangan detail tidak boleh kosong'
            ]);
        }

        if (!in_array($dk, ['D', 'K'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis debit/kredit harus D atau K'
            ]);
        }

        $docnoDetail = trim((string) ($post['docno_detail'] ?? ''));

        if ($docnoDetail !== '' && strtoupper($docnoDetail) !== $docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No. Bukti detail tidak sesuai'
            ]);
        }

        // =====================================================
        // AMBIL CURRENCY DAN KURS DARI HEADER TMP
        // =====================================================
        $header = $db->table('sc_tmp.pengeluarankb')
            ->select('currcode, kurs')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if (!$header) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Header Pengeluaran Kas/Bank tidak ditemukan'
            ]);
        }

        $currcode = strtoupper(trim((string) ($header['currcode'] ?? 'IDR')));
        $currcode = $currcode !== '' ? $currcode : 'IDR';

        $kurs = $toNumber($header['kurs'] ?? 0);

        if ($currcode === 'IDR' && ($kurs === null || $kurs == 0)) {
            $kurs = 1;
        }

        if ($kurs === null || $kurs <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kurs header tidak valid untuk mata uang ' . $currcode
            ]);
        }

        $nilaiakhir = round($nilai * $kurs, 2);

        // =====================================================
        // DATA DETAIL
        // =====================================================
        $detailData = [
            'idcoa'      => $idcoa,
            'nobukti'    => $nobukti,
            'nmcoa'      => $nmcoa,
            'dk'         => $dk,
            'nilai'      => $nilai,
            'nilaiakhir' => $nilaiakhir,
            'currcode'   => $currcode,
            'kurs'       => $kurs,
            'cabang'     => $cabangdtl,
            'remarks'    => $remarks,
            'updateby'   => $nama,
            'updatedate' => $now,
            'jurnalref'        => 'YES',
            'nilai_ref'  => $nilaiakhir
        ];

        $detailTable = $db->table('sc_tmp.pengeluarankb_dtl');

        try {
            $db->transBegin();

            if ($isEdit) {
                // =================================================
                // EDIT: VERIFIKASI DATA TERLEBIH DAHULU
                // =================================================
                $check = $db->table('sc_tmp.pengeluarankb_dtl')
                    ->where('docno', $docno)
                    ->where('inputby', $nama);

                if ($idurut !== '') {
                    $check->where('idurut', $idurut);
                } else {
                    $check->where('uniqueid', $uniqueid);
                }

                $existing = $check->get()->getRowArray();

                if (!$existing) {
                    throw new \RuntimeException(
                        'Detail yang akan diedit tidak ditemukan. ' .
                        'Tidak ada baris baru yang dibuat.'
                    );
                }

                // UPDATE hanya baris yang telah ditemukan.
                $update = $db->table('sc_tmp.pengeluarankb_dtl')
                    ->where('docno', $docno)
                    ->where('inputby', $nama);

                if ($idurut !== '') {
                    $update->where('idurut', $idurut);
                } else {
                    $update->where('uniqueid', $uniqueid);
                }

                if ($update->update($detailData) === false) {
                    $error = $db->error();

                    throw new \RuntimeException(
                        'Gagal memperbarui detail: ' .
                        ($error['message'] ?? 'Penyebab tidak diketahui')
                    );
                }

                $message = 'Detail berhasil diperbarui';

            } else {
                // =================================================
                // INPUT BARU: INSERT HANYA JIKA BUKAN MODE EDIT
                // =================================================
                if ($uniqueid === '') {
                    $uniqueid = hash(
                        'sha256',
                        $docno . '|' . $idcoa . '|' . $nama . '|' . $now
                    );
                }

                $detailData['docno']     = $docno;
                $detailData['inputby']   = $nama;
                $detailData['inputdate'] = $now;
                $detailData['uniqueid']  = $uniqueid;

                if (!$detailTable->insert($detailData)) {
                    $error = $db->error();

                    throw new \RuntimeException(
                        'Gagal menyimpan detail: ' .
                        ($error['message'] ?? 'Penyebab tidak diketahui')
                    );
                }

                $message = 'Detail berhasil ditambahkan';
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success'    => true,
                'reload'     => true,
                'message'    => $message,
                'currcode'   => $currcode,
                'kurs'       => $kurs,
                'nilai'      => $nilai,
                'nilaiakhir' => $nilaiakhir
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'savePengeluaranKBDetail: ' . $e->getMessage()
            );

            return $this->response->setJSON([
                'success' => false,
                'reload'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_status_pengeluarankb_dtl()
    {
        $nama   = trim(session()->get('nama'));
        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $db = db_connect();

        try {

            $db->transBegin();

            // ambil docno berdasarkan idurut
            $detail = $db->table('sc_tmp.pengeluarankb_dtl')
                ->select('docno')
                ->where('idurut', $id)
                ->get()
                ->getRowArray();

            if (!$detail) {
                throw new \Exception('Detail tidak ditemukan');
            }

            $docno = trim($detail['docno']);

            // update status detail
            $db->table('sc_tmp.pengeluarankb_dtl')
                ->where('idurut', $id)
                ->update([
                    'status' => $status ?: null
                ]);

            // hitung ulang balance dari detail yang status = F
            $getBalance = $db->table('sc_tmp.pengeluarankb_dtl')
                ->select("
                    COALESCE(SUM(CASE WHEN dk='DEBIT' THEN nilai ELSE 0 END),0) AS total_debit,
                    COALESCE(SUM(CASE WHEN dk='KREDIT' THEN nilai ELSE 0 END),0) AS total_kredit
                ")
                ->where('docno', $docno)
                ->where("TRIM(status) = 'F'", null, false)
                ->get()
                ->getRowArray();

            $totalDebit  = ($getBalance['total_debit'] ?? 0);
            $totalKredit = ($getBalance['total_kredit'] ?? 0);

            $getHeader = $db->table('sc_tmp.pengeluarankb')
                ->select('dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            if (!$getHeader) {
                throw new \Exception('Header tidak ditemukan');
            }


            // balance = debit - kredit
            $total =   $totalDebit - $totalKredit;
            $balance = $getHeader['dpp'] +  $totalKredit - $totalDebit;

            // update header
            $db->table('sc_tmp.pengeluarankb')
                ->where('docno', $docno)
                ->update([
                    'total'    => $total,
                    'balance'    => $balance,
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
                ]);

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal update data');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }



    public function updatePengeluaranKB()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));

        if ($docno === false || trim($docno) === '') {
            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        }

        $docno = trim($docno);

        $header = $this->db->table('sc_trx.pengeluarankb')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        if (!$header) {
            $header = $this->db->table('sc_tmp.pengeluarankb')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();
        }

        if (!$header) {
            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        }

        $status = strtoupper(trim($header['status'] ?? ''));

        // Periode berdasarkan tanggal login
        $logindate = trim($this->session->get('logindate'));
        $periode = date('ym', strtotime($logindate));

        $dtlPeriode = $this->m_purchase
            ->q_cek_periode($periode)
            ->getRowArray();

        if (
            $dtlPeriode &&
            strtoupper(trim($dtlPeriode['flagproses'] ?? '')) === 'TUTUP'
        ) {
            session()->setFlashdata(
                'periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );

            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        }

        if ($status === 'F' || $status === 'P') {
            $this->db->table('sc_trx.pengeluarankb')
                ->where('docno', $docno)
                ->update(['status' => 'E']);

            return redirect()->to(
                base_url('ka/finance/addPengeluaranKB')
            );
        }

        if ($status === 'E') {
            return redirect()->to(
                base_url('ka/finance/addPengeluaranKB')
            );
        }

        return redirect()->to(base_url('ka/finance/pengeluarankb'));
    }


    public function showing_pengeluarankbtemp()
    {
        $nama = trim((string) $this->session->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'User login tidak ditemukan.',
                'data'    => null
            ]);
        }

        // =========================================================
        // AMBIL HEADER TEMPORARY MILIK USER
        // =========================================================
        $header = $this->db->table('sc_tmp.pengeluarankb')
            ->where('TRIM(inputby)', $nama)
            ->where('TRIM(status)', 'E')
            ->orderBy('idurut', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // =========================================================
        // BELUM ADA TEMPORARY = INPUT BARU
        // =========================================================
        if (!$header) {
            return $this->response->setJSON([
                'status'  => true,
                'mode'    => 'INPUT',
                'message' => 'Belum ada Pengeluaran Kas Bank temporary.',
                'data'    => [
                    'header' => null,
                    'detail' => []
                ]
            ]);
        }

        $docno = trim($header['docno']);

        // =========================================================
        // AMBIL DETAIL BERDASARKAN DOCNO
        // =========================================================
        $detail = $this->db->table('sc_tmp.pengeluarankb_dtl')
            ->where('docno', $docno)
            ->where('TRIM(inputby)', $nama)
            ->get()
            ->getResultArray();

        // =========================================================
        // RESPONSE
        // =========================================================
        return $this->response->setJSON([
            'status'  => true,
            'mode'    => 'UPDATE',
            'message' => 'Temporary Pengeluaran Kas Bank ditemukan.',
            'data'    => [
                'header' => $header,
                'detail' => $detail
            ]
        ]);
    }

    function showing_pengeluarankb_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_finance->q_pengeluarankb_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }

    /**
     * Ambil header Pengeluaran Kas/Bank (read only) dari sc_trx.pengeluarankb.
     * Dipakai oleh halaman Detail Pengeluaran Kas/Bank.
     */
    function showing_pengeluarankbtrx(){
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_finance->q_pengeluarankb_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



    public function get_pengeluarankb_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.pengeluarankb_dtl')
            //->where('idurut', $id)
            ->where('uniqueid', $id)
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

    public function delete_pengeluarankb_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.pengeluarankb_dtl');

        // Ambil IDs (bisa array atau single)
        $ids = $request->getPost('ids');

        // Validasi IDs
        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        // Normalisasi: pastikan array
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {
            $builder
                ->whereIn('uniqueid', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data PengeluaranKB Detail tidak ditemukan'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data PengeluaranKB Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }



    function list_tmp_pengeluarankb_dtl()
    {
        $docno = trim((string) $this->request->getPost('docno'));
        $list  = $this->m_finance->get_t_pengeluarankb_dtl_temp_view($docno);

        $data = [];
        $draw = (int) ($this->request->getPost('draw') ?? 0);

        foreach ($list as $lm) {
            $row = [];

            // 0. Checkbox menggunakan uniqueid
            $row[] = $lm->uniqueid;

            // 1-6. Kolom tampilan yang sudah ada
            $row[] = $lm->nobukti;
            $row[] = ($lm->idcoa ?? '') . '  -  ' . ($lm->nmcoa ?? '');
            $row[] = $lm->remarks ?? '';
            $row[] = $lm->dk ?? '';
            $row[] = $lm->cabang ?? '';
            $row[] = '<div class="ratakanan">'
                . number_format((float) ($lm->nilai ?? 0), 2, '.', ',')
                . '</div>';

            // 7-11. Kolom tambahan untuk proses Edit Detail
            $row[] = $lm->idcoa ?? '';
            $row[] = $lm->nmcoa ?? '';
            $row[] = $lm->nilai_ref ?? $lm->nilai ?? '';
            $row[] = $lm->jurnalref ?? '';
            $row[] = $lm->costcenter ?? $lm->cabang ?? '';

            $data[] = $row;
        }

        $output = [
            'draw'            => $draw,
            'recordsTotal'    => $this->m_finance
                ->t_pengeluarankb_dtl_temp_view_count_all($docno),
            'recordsFiltered' => $this->m_finance
                ->t_pengeluarankb_dtl_temp_view_count_filtered($docno),
            'data'            => $data
        ];

        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_trx_pengeluarankb_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_pengeluarankb_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $checked = trim($lm->status) == 'F' ? 'checked' : '';

            $row[] = [
                'id' => $lm->idurut,
                'checked' => $checked
            ];



            $row[] = $lm->nobukti;
            //item
            $row[] = $lm->idcoa;
            $row[] = $lm->nmcoa;
            $row[] = $lm->remarks;
            $row[] = $lm->dk;
            $row[] = trim($lm->status) == 'F'
                ? '<span class="badge bg-success">DIPILIH</span>'
                : '<span class="badge bg-secondary text-dark">BELUM DIPILIH</span>';
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $data[] = $row;

        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_pengeluarankb_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_finance->t_pengeluarankb_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    /**
     * Laporan Jurnal Transaksi Pengeluaran Kas/Bank (read only).
     * Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
     * nama supplier diambil dari dokumen sc_trx.pengeluarankb.
     */
    public function laporan_jurnal_transaksi_pengeluarankb()
    {
        try {

            // =================================================
            // GET DOCNO
            // =================================================

            $docno = trim(
                $this->request->getPost('docno')
            );


            // =================================================
            // VALIDASI
            // =================================================

            if ($docno === '') {

                return $this->response
                    ->setJSON([

                        'status'   => false,

                        'messages' => 'Doc No Pengeluaran Kas/Bank tidak ditemukan',

                        'data'     => []

                    ]);

            }


            // =================================================
            // PARAMETER QUERY
            // =================================================

            $params = "

            AND TRIM(jh.docno) = " .
                $this->db->escape($docno);


            // =================================================
            // GET DATA
            // =================================================

            $query = $this->m_finance
                ->q_laporan_jurnal_transaksi_pengeluarankb($params);


            $data = $query->getResultArray();


            // =================================================
            // RESPONSE
            // =================================================

            return $this->response
                ->setJSON([

                    'status'   => true,

                    'messages' => 'Data berhasil diambil',

                    'data'     => $data

                ]);

        }
        catch (\Throwable $e) {

            log_message(
                'error',
                'laporan_jurnal_transaksi_pengeluarankb: '
                . $e->getMessage()
            );


            return $this->response
                ->setJSON([

                    'status'   => false,

                    'messages' => $e->getMessage(),

                    'data'     => []

                ]);

        }

    }


    public function finalPengeluaranKB()
    {
        $db = db_connect();
        $nama = trim((string) session()->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session nama tidak ditemukan. Silakan login kembali.'
            ]);
        }

        $idurut = trim((string) $this->request->getPost('idurut'));
        $docno  = trim((string) $this->request->getPost('docno'));

        if ($idurut === '' && $docno === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID atau nomor Pengeluaran Kas Bank tidak ditemukan.'
            ]);
        }

        try {
            $db->transBegin();

            $builder = $db->table('sc_tmp.pengeluarankb')
                ->where('inputby', $nama)
                ->where('status', 'E');

            if ($idurut !== '') {
                $builder->where('idurut', (int) $idurut);
            } else {
                $builder->where('docno', $docno);
            }

            $header = $builder->get()->getRowArray();

            if (!$header) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data Pengeluaran Kas Bank tidak ditemukan atau bukan milik user.'
                ]);
            }

            $idurut = (int) $header['idurut'];
            $docno  = trim((string) $header['docno']);

            $db->table('sc_tmp.pengeluarankb')
                ->where('idurut', $idurut)
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->update([
                    'status'     => 'F',
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
                ]);

            if ($db->transStatus() === false) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal melakukan finalisasi Pengeluaran Kas Bank.'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengeluaran Kas Bank berhasil difinalisasi.',
                'data' => [
                    'idurut' => $idurut,
                    'docno'  => $docno,
                    'status' => 'F'
                ]
            ]);

        } catch (\Throwable $e) {
            if ($db->transStatus() !== false) {
                $db->transRollback();
            }

            log_message('error', 'finalPengeluaranKB error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat finalisasi Pengeluaran Kas Bank.'
            ]);
        }
    }

    public function clearfinalPengeluaranKB()
    {
        $db = db_connect();
        $nama = trim((string) session()->get('nama'));
        $urlBack = base_url('ka/finance/pengeluarankb');

        if ($nama === '') {
            return redirect()->to($urlBack);
        }

        try {
            $db->transBegin();

            $tmpRows = $db->table('sc_tmp.pengeluarankb')
                ->select('idurut, docno, docnotmp')
                ->where('inputby', $nama)
                ->get()
                ->getResultArray();

            foreach ($tmpRows as $tmp) {
                $idurut  = $tmp['idurut'];
                $docnoTmp = trim((string) ($tmp['docno'] ?? ''));
                $docnoTrx = trim((string) ($tmp['docnotmp'] ?? ''));

                if ($docnoTrx === '') {
                    $docnoTrx = $docnoTmp;
                }

                // Kembalikan header permanent menjadi F
                if ($docnoTrx !== '') {
                    $db->table('sc_trx.pengeluarankb')
                        ->where('idurut', $idurut)
                        ->where('docno', $docnoTrx)
                        ->where('status', 'E')
                        ->update([
                            'status'     => 'F',
                            'updateby'   => $nama,
                            'updatedate' => date('Y-m-d H:i:s')
                        ]);

                    // Kembalikan detail permanent menjadi F
                    $db->table('sc_trx.pengeluarankb_dtl')
                        ->where('idurut', $idurut)
                        ->where('docno', $docnoTrx)
                        ->where('status', 'E')
                        ->update([
                            'status'     => 'F',
                            'updateby'   => $nama,
                            'updatedate' => date('Y-m-d H:i:s')
                        ]);
                }

                // Hapus detail temporary
                if ($docnoTmp !== '') {
                    $db->table('sc_tmp.pengeluarankb_dtl')
                        ->where('docno', $docnoTmp)
                        ->delete();
                }
            }

            // Bersihkan detail temporary milik user
            $db->table('sc_tmp.pengeluarankb_dtl')
                ->where('inputby', $nama)
                ->delete();

            // Hapus header temporary milik user
            $db->table('sc_tmp.pengeluarankb')
                ->where('inputby', $nama)
                ->delete();

            if ($db->transStatus() === false) {
                $db->transRollback();

                return redirect()->to($urlBack)
                    ->with('error', 'Gagal membersihkan Pengeluaran Kas Bank.');
            }

            $db->transCommit();

            return redirect()->to($urlBack)
                ->with('success', 'Entry Pengeluaran Kas Bank berhasil dibersihkan.');

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'clearfinalPengeluaranKB error: ' . $e->getMessage());

            return redirect()->to($urlBack)
                ->with('error', 'Gagal membersihkan Pengeluaran Kas Bank.');
        }
    }

    function show_pengeluarankb(){
        $module = 'Pengeluaran Kas/Bank';
        $table = 'sc_trx.pengeluarankb';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.pengeluarankb');

        //    $builder = $builder
        //         ->where('docno', $docno)
        //         ->update([
        //             'status'=> 'P',
        //             'printby' => $nama,
        //             'printdate' => date('Y-m-d H:i:s')
        //         ]);


        $enc_docno = $this->fiky_encryption->sealed($docno);

        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Bukti Jurnal";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("ka/finance/api_pengeluarankb/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_pengeluarankb.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno);
    }

    function api_pengeluarankb(){
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
        $datamst = $this->m_finance->q_pengeluarankb_master($param);
        $datadtl = $this->m_finance->q_pengeluarankb_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {


            // 🔹 Ambil nmsupplier berdasarkan kdsupplier
            $kdsupplier = trim($detail->kdsupplier);

            $supplier = $this->db->query("
                SELECT nmsupplier 
                FROM sc_mst.mstsupplier 
                WHERE TRIM(kdsupplier) = ?
                LIMIT 1
            ", [$kdsupplier])->getRow();

            // 🔹 Set ke object detail
            $detail->nmsupplierdata = $supplier->nmsupplier ?? '';
            $nilai = $detail->total; // dari database

            $data['total'] = $nilai;
            $data['total_terbilang'] = strtoupper($this->terbilang($nilai));
            $detail->terbilang = $data['total_terbilang'];


            $currcode = trim($detail->currcode);

            $currency = $this->db->query("
                SELECT currname 
                FROM sc_mst.currency 
                WHERE TRIM(currcode) = ?
                LIMIT 1
            ", [$currcode])->getRow();

            $cleanedCurrname = $currency->currname ?? '';
            $cleanedCurrname = str_replace('(LUAR NEGERI)', '', $cleanedCurrname);
            $cleanedCurrname = trim($cleanedCurrname);

            $detail->currname = $cleanedCurrname;

            $prkkas = trim($detail->prkkas);
            $coa = $this->db->query("
                SELECT nmcoa 
                FROM sc_mst.coa 
                WHERE TRIM(idcoa) = ?
                LIMIT 1
            ", [$prkkas])->getRow();

            if ($coa !== null) {
                $cleanedPrkkas = trim($coa->nmcoa);
            } else {
                $cleanedPrkkas = '';
            }
            $detail->prkkasname = $cleanedPrkkas;


            $detail->namauser = $nama;

        }

        $detailRows = $datadtl->getResultArray();

        // minimum row yang ingin ditampilkan
        $minRow = 20;

        // hitung kekurangan
        $kurang = $minRow - count($detailRows);

        // =========================
        // DATA ASLI = dummy 0
        // =========================
        foreach ($detailRows as &$row) {
            $row['dummy'] = 0;
        }

        // =========================
        // TAMBAHKAN ROW KOSONG
        // =========================
        if ($kurang > 0) {

            for ($i = 0; $i < $kurang; $i++) {

                $detailRows[] = [

                    // penanda row kosong
                    'dummy'        => 1,

                    // field existing
                    'idcoa'        => '',
                    'nobukti'      => '',
                    'remarks'   => '',
                    'dk'        => '',
                    'kredit'       => '',
                    'nilai'        => ''

                ];
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
                'detail' => $detailRows,
            ), JSON_PRETTY_PRINT);
    }



    public function loadPerSupplier()
    {
        $kdsupplier = $this->request->getGet('kdsupplier');

        if (empty($kdsupplier)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Kode supplier tidak boleh kosong'
            ]);
        }

        // proses/query berdasarkan supplier
        $data = $this->m_finance->loadPerSupplier($kdsupplier);

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }


    /*
     * PATCH untuk App\Controllers\Ka\Finance (sesuaikan namespace sesuai controller).
     * Ganti method importPengeluaranKB(), downloadTemplatePengeluaranKB(),
     * generateTemplateCSV(), prosesImportPengeluaranKB() dan helper readCSV/readExcel/parseDate
     * yang lama dengan blok ini. Method ini hanya menulis staging sc_im; tidak menulis sc_tmp/sc_trx.
     */

    public function importPengeluaranKB()
    {
        $data['title'] = 'Import Pengeluaran Kas/Bank';
        $qBranch = $this->m_global->q_branch();
        $dtlbranch = $qBranch ? $qBranch->getRowArray() : null;
        $nama = trim((string) $this->session->get('nama'));
        $kodemenu = 'I.K.B.3';
        $versirelease = 'I.K.B.3/01';
        $releasedate = date('2025-04-12 00:00:00');

        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;
        $data['message'] = '';

        $db = $this->db;
        $data['recent'] = $db->table('sc_im.pengeluarankb_import_batch')
            ->orderBy('id', 'DESC')->limit(15)->get()->getResultArray();
        $data['previewBatch'] = null;
        $data['previewHeaders'] = [];
        $data['previewDetails'] = [];
        $data['previewSummary'] = null;
        $previewToken = trim((string) session()->getFlashdata('preview_batch_token'));
        if ($previewToken !== '') {
            $batch = $db->table('sc_im.pengeluarankb_import_batch')->where('batch_token', $previewToken)->where('created_by', $nama)->get()->getRowArray();
            if ($batch) {
                $data['previewBatch'] = $batch;
                $data['previewHeaders'] = $db->table('sc_im.pengeluarankb_import_hd')->where('batch_id', $batch['id'])->orderBy('nomor_baris', 'ASC')->get()->getResultArray();
                $data['previewDetails'] = $db->table('sc_im.pengeluarankb_import_dtl')->where('batch_id', $batch['id'])->orderBy('header_id', 'ASC')->orderBy('slot_no', 'ASC')->get()->getResultArray();
                $data['previewSummary'] = ['jumlah_baris'=>(int)$batch['jumlah_baris'], 'jumlah_valid'=>(int)$batch['jumlah_valid'], 'jumlah_gagal'=>(int)$batch['jumlah_gagal']];
            }
        }

        $kmenu = 'I.K.B.3';
        $role = trim((string) $this->session->get('roleid'));
        $qAkses = $this->m_role->detail_user_akses($role, $kmenu);
        $data['dtl_akses'] = $qAkses ? $qAkses->getRowArray() : null;

        return $this->template->render('finance/finance/v_import_pengeluarankb', $data);
    }



    /** Upload hanya membentuk preview di sc_im. Tidak mengubah tabel transaksi. */
    public function prosesImportPengeluaranKB()
    {
        set_time_limit(300); // File export BK bisa berisi ribuan dokumen.
        $nama = trim((string) $this->session->get('nama'));
        $file = $this->request->getFile('file_import');
        $redirect = base_url('ka/finance/importPengeluaranKB');

        if (!$file || !$file->isValid()) return redirect()->to($redirect)->with('error', 'File tidak valid atau tidak ditemukan.');
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['xlsx','xls','csv'], true)) return redirect()->to($redirect)->with('error', 'Format file harus XLSX, XLS, atau CSV.');
        if ($file->getSize() > 10 * 1024 * 1024) return redirect()->to($redirect)->with('error', 'Ukuran file maksimal 10 MB.');

        $uploadPath = WRITEPATH . 'uploads/import_bk/';
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
            return redirect()->to($redirect)->with('error', 'Folder upload tidak dapat dibuat.');
        }
        $safeUser = preg_replace('/[^A-Za-z0-9_-]/', '_', $nama ?: 'user');
        $newName = $safeUser . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        try {
            $file->move($uploadPath, $newName);
            $path = $uploadPath . $newName;
            $rows = $ext === 'csv' ? $this->readCSV($path) : $this->readExcel($path);
        } catch (\Throwable $e) {
            log_message('error', 'Import PengeluaranKB preview: ' . $e->getMessage());
            return redirect()->to($redirect)->with('error', 'Gagal membaca file. Periksa format file dan coba kembali.');
        }

        if (empty($rows)) return redirect()->to($redirect)->with('error', 'File kosong.');

        // Temukan baris header; export BK yang diberikan memiliki baris pertama HEADER/CONTENT,
        // dan nama kolom berada di baris kedua. Tetap mendukung template satu baris header.
        $headerIndex = null;
        foreach (array_slice($rows, 0, 10, true) as $idx => $row) {
            $cells = array_map(fn($v) => strtoupper(trim((string) $v)), $row);
            if (in_array('NOMOR BK', $cells, true) && in_array('TANGGAL BK', $cells, true)) { $headerIndex = $idx; break; }
        }
        if ($headerIndex === null) return redirect()->to($redirect)->with('error', 'Header NOMOR BK dan TANGGAL BK tidak ditemukan. Gunakan template import.');

        $headers = array_map(fn($v) => strtoupper(trim((string) $v)), $rows[$headerIndex]);
        $dataRows = array_slice($rows, $headerIndex + 1);
        $headerMap = [];
        foreach ($headers as $idx => $h) if ($h !== '') $headerMap[$h] = $idx;

        $batchToken = bin2hex(random_bytes(24));
        $now = date('Y-m-d H:i:s');
        $db = $this->db;
        $countValid = 0;
        $countError = 0;
        $qBranch = $this->m_global->q_branch();
        $branchRow = $qBranch ? $qBranch->getRowArray() : null;
        $branch = trim((string) ($branchRow['branch'] ?? ''));

        // Bangun baris RAW (teks persis seperti di Excel); parsing & validasi dilakukan
        // di database oleh sc_im.sp_build_pengeluarankb_import().
        $colMap = [
            'NOMOR BK'=>'nomor_bk', 'TANGGAL BK'=>'tanggal_bk', 'KODE SUPPLIER'=>'kode_supplier',
            'SUPPLIER'=>'supplier', 'DIBAYAR KEPADA'=>'dibayar_kepada', 'ALAMAT SUPPLIER'=>'alamat_supplier',
            'KODE BANK'=>'kode_bank', 'KETERANGAN'=>'keterangan',
            'TOTAL PEMBAYARAN'=>'total_pembayaran', 'CURRENCY'=>'currency',
        ];
        $rawRows = [];
        $lineNo = $headerIndex + 1;
        foreach ($dataRows as $row) {
            $lineNo++;
            $row = is_array($row) ? $row : [];
            if (!array_filter($row, fn($v) => trim((string) $v) !== '')) continue;
            $raw = [];
            foreach ($headerMap as $h => $idx) $raw[$h] = trim((string) ($row[$idx] ?? ''));
            $rec = ['nomor_baris'=>$lineNo];
            foreach ($colMap as $h => $col) $rec[$col] = $raw[$h] ?? '';
            for ($i = 1; $i <= 25; $i++) {
                $rec['coa_'.$i]   = $raw['COA '.$i] ?? '';
                $rec['ket_'.$i]   = $raw['KET '.$i] ?? '';
                $rec['reff_'.$i]  = $raw['REFF '.$i] ?? '';
                $rec['dk_'.$i]    = $raw['DK '.$i] ?? '';
                $rec['nilai_'.$i] = $raw['NILAI '.$i] ?? '';
            }
            $rawRows[] = $rec;
        }
        if (empty($rawRows)) return redirect()->to($redirect)->with('error', 'Tidak ada baris data yang dapat dipreview.');

        $db->transBegin();
        try {
            $db->table('sc_im.pengeluarankb_import_batch')->insert([
                'batch_token'=>$batchToken, 'nama_file'=>basename($file->getName()),
                'status'=>'PREVIEW', 'created_by'=>$nama, 'created_at'=>$now,
                'keterangan'=>'Preview import; belum diposting ke transaksi.'
            ]);
            $batchId = (int) $db->insertID();

            foreach (array_chunk($rawRows, 200) as $chunk) {
                foreach ($chunk as &$rec) $rec['batch_id'] = $batchId;
                unset($rec);
                $db->table('sc_im.pengeluarankb_import_raw')->insertBatch($chunk);
            }

            // Transform RAW -> header/detail + seluruh validasi kolom di sisi database.
            $db->query('CALL sc_im.sp_build_pengeluarankb_import(?)', [$batchId]);

            // TUTUP PERIODE / CLOSING: dokumen yang tanggalnya berada pada periode yang sudah
            // ditutup (sc_trx.closeperiod.flagproses = 'TUTUP') ditandai ERROR (merah) sehingga
            // batch tidak dapat dikonfirmasi/diposting ke transaksi.
            $db->query(
                "UPDATE sc_im.pengeluarankb_import_hd h
                    SET status = 'ERROR',
                        error_message = CONCAT_WS('; ', NULLIF(h.error_message, ''),
                            'Periode ' || TO_CHAR(h.docdate, 'YYMM') || ' sudah TUTUP (closing). Dokumen tidak dapat diproses.')
                   FROM sc_trx.closeperiod cp
                  WHERE h.batch_id = ?
                    AND h.docdate IS NOT NULL
                    AND BTRIM(cp.periode::TEXT) = TO_CHAR(h.docdate, 'YYMM')
                    AND UPPER(BTRIM(cp.flagproses)) = 'TUTUP'",
                [$batchId]
            );
            $countClosing = (int) ($db->query(
                "SELECT COUNT(*)::INT AS c
                   FROM sc_im.pengeluarankb_import_hd
                  WHERE batch_id = ? AND error_message LIKE '%sudah TUTUP (closing)%'",
                [$batchId]
            )->getRowArray()['c'] ?? 0);

            // REPLACE: docno yang sudah ada di transaksi/draft atau staging batch aktif lain
            // ditandai is_replace=1; staging lama dengan docno sama dihapus (tidak ditumpuk).
            $batchDocnos = array_column($db->table('sc_im.pengeluarankb_import_hd')->select('docno')->where('batch_id', $batchId)->get()->getResultArray(), 'docno');
            $replaceSet = [];
            $oldHeaderIds = [];
            foreach (array_chunk($batchDocnos, 500) as $chunk) {
                foreach ($db->table('sc_trx.pengeluarankb')->select('docno')->whereIn('docno', $chunk)->get()->getResultArray() as $r) $replaceSet[$r['docno']] = true;
                foreach ($db->table('sc_tmp.pengeluarankb')->select('docno')->whereIn('docno', $chunk)->get()->getResultArray() as $r) $replaceSet[$r['docno']] = true;
                $oldRows = $db->table('sc_im.pengeluarankb_import_hd h')
                    ->select('h.id, h.docno')
                    ->join('sc_im.pengeluarankb_import_batch b', 'b.id = h.batch_id')
                    ->whereIn('h.docno', $chunk)
                    ->whereIn('b.status', ['PREVIEW', 'CONFIRMED'])
                    ->where('b.id <>', $batchId)
                    ->get()->getResultArray();
                foreach ($oldRows as $r) { $oldHeaderIds[] = (int) $r['id']; $replaceSet[$r['docno']] = true; }
            }
            if ($oldHeaderIds) {
                foreach (array_chunk($oldHeaderIds, 500) as $chunk) {
                    $db->table('sc_im.pengeluarankb_import_dtl')->whereIn('header_id', $chunk)->delete();
                }
                $db->table('sc_im.pengeluarankb_import_hd')->whereIn('id', $oldHeaderIds)->delete();
            }
            if ($replaceSet) {
                foreach (array_chunk(array_keys($replaceSet), 500) as $chunk) {
                    $db->table('sc_im.pengeluarankb_import_hd')->set('is_replace', 1)->where('batch_id', $batchId)->whereIn('docno', $chunk)->update();
                }
            }
            $countReplace = (int) $db->table('sc_im.pengeluarankb_import_hd')->where('batch_id', $batchId)->where('is_replace', 1)->countAllResults();

            // Jumlah valid/error dihitung ulang dari staging agar perubahan akibat penandaan
            // TUTUP PERIODE (closing) di atas ikut terhitung.
            $countRow = $db->query(
                "SELECT COUNT(*)::INT AS jumlah_baris,
                        COUNT(*) FILTER (WHERE status = 'VALID')::INT AS jumlah_valid,
                        COUNT(*) FILTER (WHERE status = 'ERROR')::INT AS jumlah_gagal
                   FROM sc_im.pengeluarankb_import_hd
                  WHERE batch_id = ?",
                [$batchId]
            )->getRowArray();
            $countValid = (int) ($countRow['jumlah_valid'] ?? 0);
            $countError = (int) ($countRow['jumlah_gagal'] ?? 0);

            $totalRows = (int) ($countRow['jumlah_baris'] ?? 0);
            if ($totalRows === 0) throw new \RuntimeException('Tidak ada baris data yang dapat dipreview.');
            $db->table('sc_im.pengeluarankb_import_batch')->where('id', $batchId)->update([
                'jumlah_baris'=>$totalRows, 'jumlah_valid'=>$countValid, 'jumlah_gagal'=>$countError
            ]);
            if ($db->transStatus() === false) throw new \RuntimeException('Database gagal menyimpan preview.');
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Import PengeluaranKB stage failed: '.$e->getMessage());
            return redirect()->to($redirect)->with('error', 'Preview gagal disimpan: '.$e->getMessage());
        }

        if (!empty($countClosing)) {
            return redirect()->to($redirect)->with('preview_batch_token', $batchToken)
                ->with('warning', 'Preview selesai. Valid: '.$countValid.', error: '.$countError.'. '.$countClosing.' dokumen berada pada periode TUTUP (closing) sehingga ditandai INVALID (merah) dan tidak dapat diproses.');
        }

        return redirect()->to($redirect)->with('preview_batch_token', $batchToken)
            ->with('success', 'Preview selesai. Valid: '.$countValid.', error: '.$countError.', replace: '.$countReplace.'. Belum ada transaksi yang diubah.');
    }

    /** Konfirmasi batch: posting staging ke sc_trx.pengeluarankb(_dtl) + jurnal transaction_dt (BANKOT). */
    public function simpanImportPengeluaranKB()
    {
        $nama = trim((string) $this->session->get('nama'));
        $token = trim((string) $this->request->getPost('batch_token'));
        if ($token === '') return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('error', 'Token batch tidak ditemukan.');
        $db = $this->db;
        $batch = $db->table('sc_im.pengeluarankb_import_batch')->where('batch_token', $token)->where('created_by', $nama)->get()->getRowArray();
        if (!$batch || $batch['status'] !== 'PREVIEW') return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('error', 'Batch tidak ditemukan atau sudah diproses.');
        if ((int)$batch['jumlah_gagal'] > 0) return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('error', 'Batch masih memiliki baris ERROR. Perbaiki file lalu upload ulang; batch tidak dikonfirmasi.');
        // Guard periode: dokumen pada periode yang sudah TUTUP (closing) tidak boleh diposting.
        $closedRow = $db->query(
            "SELECT COUNT(*)::INT AS c
               FROM sc_im.pengeluarankb_import_hd h
               JOIN sc_trx.closeperiod cp
                 ON BTRIM(cp.periode::TEXT) = TO_CHAR(h.docdate, 'YYMM')
              WHERE h.batch_id = ?
                AND h.docdate IS NOT NULL
                AND UPPER(BTRIM(cp.flagproses)) = 'TUTUP'",
            [$batch['id']]
        )->getRowArray();
        if ((int)($closedRow['c'] ?? 0) > 0) return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('error', 'Batch memuat dokumen pada periode yang sudah TUTUP (closing). Konfirmasi dibatalkan.');
        // SP validasi ulang + insert header/detail + jurnal, lalu set batch CONFIRMED. Gagal = rollback semua.
        $db->transBegin();
        try {
            $db->query('CALL sc_im.sp_confirm_pengeluarankb_import(?, ?)', [$batch['id'], $nama]);
            if ($db->transStatus() === false) throw new \RuntimeException('Database gagal memposting batch.');
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Import PengeluaranKB confirm failed: '.$e->getMessage());
            return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('error', 'Konfirmasi gagal, tidak ada data disimpan: '.$e->getMessage());
        }
        return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('success', 'Batch dikonfirmasi dan diposting ke Pengeluaran Kas/Bank serta jurnal BANKOT.');
    }

    public function cancelImportPengeluaranKB()
    {
        $nama = trim((string) $this->session->get('nama'));
        $token = trim((string) $this->request->getPost('batch_token'));
        $db = $this->db;
        $db->table('sc_im.pengeluarankb_import_batch')->where('batch_token', $token)->where('created_by', $nama)->where('status', 'PREVIEW')->update([
            'status'=>'CANCELLED', 'cancelled_by'=>$nama, 'cancelled_at'=>date('Y-m-d H:i:s'), 'keterangan'=>'Preview dibatalkan oleh user.'
        ]);
        return redirect()->to(base_url('ka/finance/importPengeluaranKB'))->with('success', 'Preview dibatalkan. Data staging tetap tersedia untuk audit.');
    }

    private function readCSV($filepath): array
    {
        $rows = [];
        if (($handle = fopen($filepath, 'r')) === false) throw new \RuntimeException('File CSV tidak dapat dibuka.');
        $first = fgets($handle);
        $delimiter = (strpos((string)$first, ';') !== false && strpos((string)$first, ',') === false) ? ';' : ',';
        rewind($handle);
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) $rows[] = $data;
        fclose($handle);
        return $rows;
    }

    private function readExcel($filepath): array
    {
        if (class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
            return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        }
        if (class_exists('\\Shuchkin\\SimpleXLSX')) {
            $xlsx = \Shuchkin\SimpleXLSX::parse($filepath);
            if ($xlsx) return $xlsx->rows();
            throw new \RuntimeException('File XLSX tidak dapat dibaca.');
        }
        throw new \RuntimeException('Library PhpSpreadsheet/SimpleXLSX tidak tersedia.');
    }

    private function parseDate($value): ?string
    {
        if ($value instanceof \DateTimeInterface) return $value->format('Y-m-d');
        $value = trim((string)$value);
        if ($value === '') return null;
        if (is_numeric($value) && (float)$value > 0 && (float)$value < 100000 && class_exists('\\PhpOffice\\PhpSpreadsheet\\Shared\\Date')) {
            try { return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        foreach (['d-m-Y','d/m/Y','Y-m-d','d M Y','d F Y','m/d/Y','Y/m/d','d.m.Y'] as $format) {
            $dt = \DateTime::createFromFormat('!'.$format, $value);
            if ($dt && $dt->format($format) === $value) return $dt->format('Y-m-d');
        }
        $ts = strtotime($value);
        return $ts === false ? null : date('Y-m-d', $ts);
    }

    /**
     * Parser angka khusus file import BK. PhpSpreadsheet toArray() mengembalikan
     * angka sebagai string berformat ribuan (mis. "1,186,298,070"), jadi cleanNumber()
     * umum (strip koma saja) tidak cukup bila formatnya Indonesia ("1.186.298.070,50").
     */
    private function parseImportNumber($value): float
    {
        if (is_int($value) || is_float($value)) return (float) $value;
        $v = trim((string) $value);
        if ($v === '') return 0.0;
        $v = str_replace(["\xc2\xa0", ' '], '', $v);
        $negative = str_starts_with($v, '(') && str_ends_with($v, ')');
        $v = trim($v, '()');
        if (str_contains($v, ',') && str_contains($v, '.')) {
            // Pemisah paling kanan diasumsikan desimal.
            if (strrpos($v, ',') > strrpos($v, '.')) $v = str_replace(',', '.', str_replace('.', '', $v));
            else $v = str_replace(',', '', $v);
        } elseif (str_contains($v, ',')) {
            $parts = explode(',', $v);
            $v = count($parts) === 2 && strlen($parts[1]) <= 2 ? $parts[0].'.'.$parts[1] : implode('', $parts);
        } elseif (substr_count($v, '.') > 1) {
            $v = str_replace('.', '', $v);
        } elseif (str_contains($v, '.') && strlen(substr(strrchr($v, '.'), 1)) === 3) {
            $v = str_replace('.', '', $v);
        }
        $v = preg_replace('/[^0-9.\-]/', '', $v);
        $number = is_numeric($v) ? (float) $v : 0.0;
        return $negative ? -$number : $number;
    }

}
