<?php


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
        $kodemenu='I.K.A.1'; $versirelease='I.K.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $kodemenu='I.K.A.1'; $versirelease='I.K.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $data['title']="Input PO";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.K.A.1'; $versirelease='I.K.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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



        $idcoa    = $this->request->getPost('idcoa');
        $nmcoa    = strtoupper($this->request->getPost('nmcoa'));
        $nilai       = $this->request->getPost('nilai') ?: 0;
        $remarks = strtoupper($this->request->getPost('remarks'));
        $dk = strtoupper($this->request->getPost('dk'));
        $cabangdtl = strtoupper($this->request->getPost('cabangdtl'));

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
                SUM(CASE WHEN dk='DEBIT' THEN nilai ELSE 0 END) AS total_debit,
                SUM(CASE WHEN dk='KREDIT' THEN nilai ELSE 0 END) AS total_kredit
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



    function updateJUP()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_finance->q_jup_master($param)->getRowArray();
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
            return redirect()->to(base_url('ka/accounting/addJUP'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('ka/accounting/jup'));
        }
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


    function finalEntryJUP(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(dk, '') = ''  OR nilai = '0.00' OR COALESCE(nmcoa, '') = '' OR COALESCE(remarks, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_finance->q_jup_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_finance->q_jup_dtl_temp($paramdtl);
        $cek2 = $this->m_finance->q_jup_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.jup');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
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
            if ($builder->update($info)) {
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
                return redirect()->to(base_url('/ka/accounting/addJUP'));
            }



        }

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
        $kodemenu='I.K.B.1'; $versirelease='I.K.B.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $kodemenu='I.K.B.1'; $versirelease='I.K.B.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $kodemenu='I.K.B.1'; $versirelease='I.K.B.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $kodemenu='I.K.B.2'; $versirelease='I.K.B.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $kodemenu='I.K.B.2'; $versirelease='I.K.B.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
        $kodemenu='I.K.B.2'; $versirelease='I.K.B.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix
        ]);
    }

    public function getNextSuffixPenerimaanKB()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.penerimaankb')
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

    public function initPenerimaanKBHeader()
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

        $builder = $this->db->table('sc_tmp.penerimaankb');
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

        $db->table('sc_tmp.penerimaankb_dtl')
            ->where('docno', $docno)
            ->delete();

        // =====================================================
        // AMBIL DATA PENJUALAN
        // =====================================================

        $penjualan = $db->query("
            SELECT 
                'PENJUALAN' AS sourcetype,
                trim(p.docno) AS docref,
                p.docdate,
                p.kdcustomer AS kodepartner,

                (
                    p.total -
                    COALESCE(
                        (
                            SELECT SUM(pkbd.nilai)
                            FROM sc_trx.penerimaankb_dtl pkbd
                            WHERE trim(pkbd.nobukti) = trim(p.docno)
                            AND trim(pkbd.status) = 'F'
                        ),
                        0
                    )
                ) AS nilaitotal,
                p.cabang,
                p.keterangan
            FROM sc_trx.penjualan p
            WHERE trim(p.kdcustomer) = ?
            AND trim(coalesce(p.currcode,'')) = ?
            AND trim(coalesce(p.status,'')) = 'F'
            AND (
                p.total -
                COALESCE(
                    (
                        SELECT SUM(pkbd.nilai)
                        FROM sc_trx.penerimaankb_dtl pkbd
                        WHERE trim(pkbd.nobukti) = trim(p.docno)
                        AND trim(pkbd.status) = 'F'
                    ),
                    0
                )
            ) > 0

            UNION ALL

            SELECT 
                'UMT' AS sourcetype,
                trim(u.docno) AS docref,
                u.docdate,
                u.kdsupplier AS kodepartner,
                (
                    u.total -
                    COALESCE(
                        (
                            SELECT SUM(pkbd.nilai)
                            FROM sc_trx.penerimaankb_dtl pkbd
                            WHERE trim(pkbd.nobukti) = trim(u.docno)
                            AND trim(pkbd.status) = 'F'
                        ),
                        0
                    )
                ) AS nilaitotal,
                u.cabang,
                u.keterangan
            FROM sc_trx.umt u
            WHERE trim(u.kdsupplier) = ?
            AND trim(coalesce(u.currcode,'')) = ?
            AND trim(coalesce(u.status,'')) = 'F'
            AND (
                u.total -
                COALESCE(
                    (
                        SELECT SUM(pkbd.nilai)
                        FROM sc_trx.penerimaankb_dtl pkbd
                        WHERE trim(pkbd.nobukti) = trim(u.docno)
                        AND trim(pkbd.status) = 'F'
                    ),
                    0
                )
            ) > 0

            UNION ALL

            SELECT 
                'NDK' AS sourcetype,
                trim(n.docno) AS docref,
                n.docdate,
                n.kdsupplier AS kodepartner,
                (
                    n.total -
                    COALESCE(
                        (
                            SELECT SUM(pkbd.nilai)
                            FROM sc_trx.penerimaankb_dtl pkbd
                            WHERE trim(pkbd.nobukti) = trim(n.docno)
                            AND trim(pkbd.status) = 'F'
                        ),
                        0
                    )
                ) AS nilaitotal,
                n.cabang,
                n.keterangan
            FROM sc_trx.ndk n
            WHERE trim(n.kdsupplier) = ?
            AND trim(coalesce(n.currcode,'')) = ?
            AND trim(coalesce(n.status,'')) = 'F'
            AND (
                n.total -
                COALESCE(
                    (
                        SELECT SUM(pkbd.nilai)
                        FROM sc_trx.penerimaankb_dtl pkbd
                        WHERE trim(pkbd.nobukti) = trim(n.docno)
                        AND trim(pkbd.status) = 'F'
                    ),
                    0
                )
            ) > 0
            ORDER BY docdate ASC

        ", [

            // PENJUALAN
            $kdcustomer,
            $currcode,

            // UMT
            $kdcustomer,
            $currcode,

            // NDK
            $kdcustomer,
            $currcode

        ])->getResult();

        // =====================================================
        // INSERT DETAIL
        // =====================================================

        $currencyData = $db->query("
            SELECT 
                c.currcode,
                c.ppiutang
            FROM sc_mst.currency c
            WHERE trim(c.currcode) = ?
        ", [$currcode])->getRow();

        $idcoa = null;
        $nmcoa = null;

        if (!empty($currencyData) && !empty($currencyData->ppiutang)) {

            $idcoa = trim($currencyData->ppiutang);

            $coaData = $db->query("
                SELECT nmcoa
                FROM sc_mst.coa
                WHERE trim(idcoa) = ?
            ", [$idcoa])->getRow();

            if (!empty($coaData)) {
                $nmcoa = trim($coaData->nmcoa);
            }
        }

        foreach ($penjualan as $row) {

            $rawUnique =
                $docno . '|' .
                $row->docref . '|' .
                microtime(true);

            $uniqueid = hash('sha256', $rawUnique);
            $dk = ($row->sourcetype == 'UMT') ? 'DEBIT' : 'KREDIT';

            $db->table('sc_tmp.penerimaankb_dtl')->insert([

                'docno'        => $docno,
                'nobukti'      => $row->docref,
                'idcoa'        => $idcoa,
                'nmcoa'        => $nmcoa,
                'remarks'      => "(" . $row->docref . ") " . $row->keterangan,
                'nilai'        => $row->nilaitotal ?? 0,
                'dk'           => $dk,
                'cabang'           => $row->cabang,
                // 'status'       => 'F',
                'inputby'      => $nama,
                'inputdate'    => date('Y-m-d H:i:s'),
                'uniqueid'     => $uniqueid

            ]);
        }

        // =====================================================
        // UPDATE HEADER CUSTOMER
        // =====================================================

        $getBalance = $db->table('sc_tmp.penerimaankb_dtl')
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
        

        $db->table('sc_tmp.penerimaankb')
            ->where('docno', $docno)
            ->update([
                'kdcustomer'     => $kdcustomer,
                'total'     => $total,
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
            'total'   => count($penjualan)
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

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                // 'senddate'   => date('Y-m-d', strtotime(trim($this->request->getPost('senddate')))),
                // 'jthtempo'     => $this->request->getPost('jthtempo'),
                // 'isinclusive'     => $isinclusive,
                'perkkas'    => strtoupper($this->request->getPost('perkkas')),
                'kdcustomer'    => strtoupper($this->request->getPost('kdcustomer')),
                'alamatcustomer'    => strtoupper($this->request->getPost('alamatcustomer')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                // 'idtax'    => strtoupper($this->request->getPost('idtax')),
                'currcode'    => strtoupper($this->request->getPost('currcode')),
                'kurs'    => strtoupper($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.penerimaankb_dtl');
        $insertCount = 0;
        $message = '';



        $idcoa    = $this->request->getPost('idcoa');
        $nmcoa    = strtoupper($this->request->getPost('nmcoa'));
        $nilai       = $this->request->getPost('nilai') ?: 0;
        $remarks = strtoupper($this->request->getPost('remarks'));
        $dk = strtoupper($this->request->getPost('dk'));
        $cabangdtl = strtoupper($this->request->getPost('cabangdtl'));

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan 
                // 🔹 UPDATE
            $builderDetail->where('idurut', $idurut)->update([
                'idcoa'    => $idcoa,
                'nmcoa'    => $nmcoa,
                'dk'        => $dk,
                'nilai'        => $nilai,
                // 'status'        => 'F',
                'cabang'        => $cabangdtl,
                'remarks' => $remarks,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);

            $getDetail = $db->table('sc_tmp.penerimaankb_dtl')
                ->select('status')
                ->where('idurut', $idurut)
                ->get()
                ->getRowArray();

            if (trim($getDetail['status'] ?? '') == 'F') {

                $getBalance = $db->table('sc_tmp.penerimaankb_dtl')
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

                $getHeader = $db->table('sc_tmp.penerimaankb')
                    ->select('dpp')
                    ->where('docno', $docno)
                    ->get()
                    ->getRowArray();

                if (!$getHeader) {
                    throw new \Exception('Header tidak ditemukan');
                }

                $dpp = $getHeader['dpp'];

                $total = $totalKredit - $totalDebit;
                $balance = $dpp + $totalDebit - $totalKredit;

                $db->table('sc_tmp.penerimaankb')
                    ->where('docno', $docno)
                    ->update([
                        'total'      => $total,
                        'balance'    => $balance,
                        'updateby'   => $nama,
                        'updatedate' => date('Y-m-d H:i:s')
                    ]);
            }
            
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
                // 'status'      => 'F',
                'inputby'     => $nama,
                'inputdate'   => date('Y-m-d H:i:s'),
                'uniqueid'    => $uniqueid
            ]);
        }


        // =====================================================
        // HITUNG ULANG BALANCE HEADER
        // =====================================================

        // $getBalance = $db->table('sc_tmp.penerimaankb_dtl')
        //     ->select("
        //         SUM(CASE WHEN dk='DEBIT' THEN nilai ELSE 0 END) AS total_debit,
        //         SUM(CASE WHEN dk='KREDIT' THEN nilai ELSE 0 END) AS total_kredit
        //     ")
        //     ->where('docno', $docno)
        //     ->where("TRIM(status) = 'F'", null, false)
        //     ->get()
        //     ->getRowArray();

        // $totalDebit  = ($getBalance['total_debit'] ?? 0);
        // $totalKredit = ($getBalance['total_kredit'] ?? 0);

        // // balance = debit - kredit
        // $balance = $totalDebit - $totalKredit;

        // // update header
        // $builderHeader->where('docno', $docno)->update([
        //     'balance'    => $balance,
        //     'updateby'   => $nama,
        //     'updatedate' => date('Y-m-d H:i:s')
        // ]);
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
        $builder = $db->table('sc_tmp.penerimaankb_dtl');

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
                'message' => 'Data PenerimaanKB Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
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

            // hitung ulang balance dari detail yang status = F
            $getBalance = $db->table('sc_tmp.penerimaankb_dtl')
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

            $getHeader = $db->table('sc_tmp.penerimaankb')
                ->select('dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            if (!$getHeader) {
                throw new \Exception('Header tidak ditemukan');
            }


            // balance = debit - kredit
            $total =  $totalKredit - $totalDebit;
            $balance = $getHeader['dpp'] + $totalDebit - $totalKredit;

            // update header
            $db->table('sc_tmp.penerimaankb')
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


    function list_tmp_penerimaankb_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_penerimaankb_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $row = array();

            $checked = trim($lm->status) == 'F' ? 'checked' : '';

            $row[] = [
                'id' => $lm->idurut,
                'checked' => $checked
            ];

            $row[] = '
            <div class="btn-group">

                <button type="button"
                        class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$lm->idurut.'">
                    <i class="fa fa-edit"></i>
                </button>
                &nbsp
                '.(
                    empty(trim($lm->nobukti))
                    ? '<button type="button"
                            class="btn btn-danger btn-sm btn-delete"
                            data-id="'.$lm->idurut.'">
                        <i class="fa fa-trash"></i>
                    </button>'
                    : ''
                ).'

            </div>';

            $row[] = $lm->nobukti;
            $row[] = $lm->idcoa;
            $row[] = $lm->nmcoa;
            $row[] = $lm->remarks;
            $row[] = $lm->dk;
            $row[] = trim($lm->status) == 'F'
                ? '<span class="badge bg-success">DIPILIH</span>'
                : '<span class="badge bg-secondary text-dark">BELUM DIPILIH</span>';
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">'.number_format($lm->nilai,2,'.',',').'</div>';

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
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            // $docdate   = trim($this->request->getPost('docdate'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $prkkas   = trim($this->request->getPost('prkkas'));
            $dpp   = trim($this->request->getPost('dpp'));
            $balance   = trim($this->request->getPost('balance'));
            $total   = trim($this->request->getPost('total_awal'));

            $dpp = trim($this->request->getPost('dpp'));
            $dpp_clean = 0;
            if (!empty($dpp)) {
                $dpp_clean = str_replace(',', '', $dpp);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }


            $balance = trim($this->request->getPost('balance'));
            $balance_clean = 0;
            if (!empty($balance)) {
                $balance_clean = str_replace(',', '', $balance);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }


            $total = trim($this->request->getPost('total'));
            $total_clean = 0;
            if (!empty($total)) {
                $total_clean = str_replace(',', '', $total);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }
            
             // Convert expdate ke format YYYY-MM-DD
            // $docdateph = null;
            // if (!empty($docdate)) {
            //     $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'docdate'        => $docdateph,
                'prkkas'    => strtoupper($prkkas),
                'dpp'    => $dpp_clean,
                'balance'    => $balance_clean,
                'total'    => $total_clean,
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
                return redirect()->to(base_url('/ka/finance/addPenerimaanKB'));
            }



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
        $kodemenu='I.K.B.3'; $versirelease='I.K.B.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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
            $urlclear = base_url('ka/finance/clearEntryPengeluaranKB');
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
        $kodemenu='I.K.B.3'; $versirelease='I.K.B.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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

    function list_pengeluarankb(){
        $list = $this->m_finance->get_t_front_pengeluarankb_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.K.B.3';
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
                    href="' . base_url('ka/finance/updatePengeluaranKB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Pengeluaran Kas/Bank : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Pengeluaran Kas/Bank 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('ka/finance/detailPengeluaranKB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Pengeluaran Kas/Bank : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Pengeluaran Kas/Bank 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('ka/finance/show_pengeluarankb') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Preview Pengeluaran Kas/Bank : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Preview Pengeluaran Kas/Bank 
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

            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsuppliernew;
            $row[] = $lm->alamatsupplier;
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
            $row[] = $lm->total;

            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_front_pengeluarankb_view_count_all(),
            "recordsFiltered" => $this->m_finance->t_front_pengeluarankb_view_count_filtered(),
            "data" => $data,
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
        $kodemenu='I.K.B.3'; $versirelease='I.K.B.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
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

        $builder = $this->db->table('sc_tmp.pengeluarankb');
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
        // =====================================================

        $pembelian = $db->query("
            SELECT 
                'PEMBELIAN' AS sourcetype,
                trim(p.docno) AS docref,
                p.docdate,
                p.kdsupplier AS kodepartner,
                (
                    p.total -
                    COALESCE(
                        (
                            SELECT SUM(pkbd.nilai)
                            FROM sc_trx.pengeluarankb_dtl pkbd
                            WHERE trim(pkbd.nobukti) = trim(p.docno)
                            AND trim(pkbd.status) = 'F'
                        ),
                        0
                    )
                ) AS nilaitotal,
                p.cabang,
                p.keterangan
            FROM sc_trx.lpb p
            WHERE trim(p.kdsupplier) = ?
            AND trim(coalesce(p.currcode,'')) = ?
            AND trim(coalesce(p.status,'')) = 'F'
            AND (
                p.total -
                COALESCE(
                    (
                        SELECT SUM(pkbd.nilai)
                        FROM sc_trx.pengeluarankb_dtl pkbd
                        WHERE trim(pkbd.nobukti) = trim(p.docno)
                        AND trim(pkbd.status) = 'F'
                    ),
                    0
                )
            ) > 0

            UNION ALL

            SELECT 
                'NDK' AS sourcetype,
                trim(n.docno) AS docref,
                n.docdate,
                n.kdsupplier AS kodepartner,
                (
                    n.total -
                    COALESCE(
                        (
                            SELECT SUM(pkbd.nilai)
                            FROM sc_trx.pengeluarankb_dtl pkbd
                            WHERE trim(pkbd.nobukti) = trim(n.docno)
                            AND trim(pkbd.status) = 'F'
                        ),
                        0
                    )
                ) AS nilaitotal,
                n.cabang,
                n.keterangan
            FROM sc_trx.ndk n
            WHERE trim(n.kdsupplier) = ?
            AND trim(coalesce(n.currcode,'')) = ?
            AND trim(coalesce(n.status,'')) = 'F'
            AND (
                n.total -
                COALESCE(
                    (
                        SELECT SUM(pkbd.nilai)
                        FROM sc_trx.pengeluarankb_dtl pkbd
                        WHERE trim(pkbd.nobukti) = trim(n.docno)
                        AND trim(pkbd.status) = 'F'
                    ),
                    0
                )
            ) > 0

            ORDER BY docdate ASC

        ", [

            // PEMBELIAN
            $kdsupplier,
            $currcode,

            // // UMT
            // $kdsupplier,
            // $currcode,

            // NDK
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
                'uniqueid'     => $uniqueid

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
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.pengeluarankb');

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
                'perkkas'    => strtoupper($this->request->getPost('perkkas')),
                'kdsupplier'    => strtoupper($this->request->getPost('kdsupplier')),
                'alamatsupplier'    => strtoupper($this->request->getPost('alamatsupplier')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                // 'idtax'    => strtoupper($this->request->getPost('idtax')),
                'currcode'    => strtoupper($this->request->getPost('currcode')),
                'kurs'    => strtoupper($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.pengeluarankb_dtl');
        $insertCount = 0;
        $message = '';



        $idcoa    = $this->request->getPost('idcoa');
        $nmcoa    = strtoupper($this->request->getPost('nmcoa'));
        $nilai       = $this->request->getPost('nilai') ?: 0;
        $remarks = strtoupper($this->request->getPost('remarks'));
        $dk = strtoupper($this->request->getPost('dk'));
        $cabangdtl = strtoupper($this->request->getPost('cabangdtl'));

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan 
                // 🔹 UPDATE
            $builderDetail->where('idurut', $idurut)->update([
                'idcoa'    => $idcoa,
                'nmcoa'    => $nmcoa,
                'dk'        => $dk,
                'nilai'        => $nilai,
                // 'status'        => 'F',
                'cabang'        => $cabangdtl,
                'remarks' => $remarks,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);
            
            $getDetail = $db->table('sc_tmp.pengeluarankb_dtl')
                ->select('status')
                ->where('idurut', $idurut)
                ->get()
                ->getRowArray();

            if (trim($getDetail['status'] ?? '') == 'F') {

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

                $dpp = $getHeader['dpp'];

                $total = $totalDebit - $totalKredit;
                $balance = $dpp + $totalKredit - $totalDebit;

                $db->table('sc_tmp.pengeluarankb')
                    ->where('docno', $docno)
                    ->update([
                        'total'      => $total,
                        'balance'    => $balance,
                        'updateby'   => $nama,
                        'updatedate' => date('Y-m-d H:i:s')
                    ]);
            }
            
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
                // 'status'      => 'F',
                'inputby'     => $nama,
                'inputdate'   => date('Y-m-d H:i:s'),
                'uniqueid'    => $uniqueid
            ]);
        }


        // =====================================================
        // HITUNG ULANG BALANCE HEADER
        // =====================================================

        // $getBalance = $db->table('sc_tmp.pengeluarankb_dtl')
        //     ->select("
        //         SUM(CASE WHEN dk='DEBIT' THEN nilai ELSE 0 END) AS total_debit,
        //         SUM(CASE WHEN dk='KREDIT' THEN nilai ELSE 0 END) AS total_kredit
        //     ")
        //     ->where('docno', $docno)
        //     ->where("TRIM(status) = 'F'", null, false)
        //     ->get()
        //     ->getRowArray();

        // $totalDebit  = ($getBalance['total_debit'] ?? 0);
        // $totalKredit = ($getBalance['total_kredit'] ?? 0);

        // // balance = debit - kredit
        // $balance = $totalDebit - $totalKredit;

        // // update header
        // $builderHeader->where('docno', $docno)->update([
        //     'balance'    => $balance,
        //     'updateby'   => $nama,
        //     'updatedate' => date('Y-m-d H:i:s')
        // ]);
        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
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


    function updatePengeluaranKB()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_finance->q_pengeluarankb_master($param)->getRowArray();
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
            return redirect()->to(base_url('ka/finance/addPengeluaranKB'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('ka/finance/pengeluarankb'));
        }
    }

    function showing_pengeluarankbtrx(){
        $nama=trim($this->session->get('nama'));
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

    function showing_pengeluarankbtemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_finance->q_pengeluarankb_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_pengeluarankb_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_finance->q_pengeluarankb_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_pengeluarankb_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.pengeluarankb_dtl')
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

    public function delete_pengeluarankb_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.pengeluarankb_dtl');

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


    function list_tmp_pengeluarankb_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_finance->get_t_pengeluarankb_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $row = array();

            $checked = trim($lm->status) == 'F' ? 'checked' : '';

            $row[] = [
                'id' => $lm->idurut,
                'checked' => $checked
            ];

            $row[] = '
            <div class="btn-group">

                <button type="button"
                        class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$lm->idurut.'">
                    <i class="fa fa-edit"></i>
                </button>
                &nbsp
                '.(
                    empty(trim($lm->nobukti))
                    ? '<button type="button"
                            class="btn btn-danger btn-sm btn-delete"
                            data-id="'.$lm->idurut.'">
                        <i class="fa fa-trash"></i>
                    </button>'
                    : ''
                ).'

            </div>';

            $row[] = $lm->nobukti;
            $row[] = $lm->idcoa;
            $row[] = $lm->nmcoa;
            $row[] = $lm->remarks;
            $row[] = $lm->dk;
            $row[] = trim($lm->status) == 'F'
                ? '<span class="badge bg-success">DIPILIH</span>'
                : '<span class="badge bg-secondary text-dark">BELUM DIPILIH</span>';
            $row[] = $lm->cabang;
            $row[] = '<div class="ratakanan">'.number_format($lm->nilai,2,'.',',').'</div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_finance->t_pengeluarankb_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_finance->t_pengeluarankb_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
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


    function finalEntryPengeluaranKB(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' 
        AND (COALESCE(dk, '') = ''  
        OR nilai = '0.00' 
        OR COALESCE(nmcoa, '') = ''  
        OR COALESCE(remarks, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_finance->q_pengeluarankb_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_finance->q_pengeluarankb_dtl_temp($paramdtl);
        $cek2 = $this->m_finance->q_pengeluarankb_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.pengeluarankb');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.K.B.3');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.K.B.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/ka/finance/addPengeluaranKB'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $prkkas   = trim($this->request->getPost('prkkas'));
            $dpp   = trim($this->request->getPost('dpp'));
            $balance   = trim($this->request->getPost('balance'));
            $total   = trim($this->request->getPost('total_awal'));

            $dpp = trim($this->request->getPost('dpp'));
            $dpp_clean = 0;
            if (!empty($dpp)) {
                $dpp_clean = str_replace(',', '', $dpp);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }


            $balance = trim($this->request->getPost('balance'));
            $balance_clean = 0;
            if (!empty($balance)) {
                $balance_clean = str_replace(',', '', $balance);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }


            $total = trim($this->request->getPost('total'));
            $total_clean = 0;
            if (!empty($total)) {
                $total_clean = str_replace(',', '', $total);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }
            
             // Convert expdate ke format YYYY-MM-DD
            // $docdateph = null;
            // if (!empty($docdate)) {
            //     $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'docdate'        => $docdateph,
                'prkkas'    => strtoupper($prkkas),
                'dpp'    => $dpp_clean,
                'balance'    => $balance_clean,
                'total'    => $total_clean,
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
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.K.B.3'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/ka/finance/pengeluarankb'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.K.B.3',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/ka/finance/addPengeluaranKB'));
            }



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
}