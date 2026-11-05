<?php
/* Purchase.php  */

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
        $kodemenu='I.P.A.1'; $versirelease='I.P.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */
        $logindate = trim($this->session->get('logindate'));
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
        $kodemenu='I.P.A.1'; $versirelease='I.P.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status    = strtoupper(trim($lm->status_desc));
            $docno     = trim($lm->docno);
            $docnoHex  = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {

                $updateBtn = '
                    <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updatePP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This PP : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update Permintaan Pembelian 
                    </a>';
            }

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-undo"></i> Batalkan Permintaan Pembelian</a>';
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

            if ($canPrint && (trim($status) == 'APPROVED' || trim($status) == 'CETAK/PRINT')) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_pp') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Preview PP : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Preview Permintaan Pembelian 
                    </a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }

            // =========================
            // RULE STATUS
            // =========================
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
                    $badgeClass = 'badge-cetak';
                    break;
                case 'VOID PP':
                    $badgeClass = 'badge-danger';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success';
                    break;
                case 'CANCELED':
                    $badgeClass = 'badge-danger ';
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


    function list_pp_apprv(){
        $list = $this->m_purchase->get_t_front_pp_apprv_view();
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
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status    = strtoupper(trim($lm->status_desc));
            $docno     = trim($lm->docno);
            $docnoHex  = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $approveBtn  = '';
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
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

            if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
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
                // if ($canUpdate) $menuContent .= $updateBtn;
                // if ($canPrint)  $menuContent .= $printBtn;
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
                    $badgeClass = 'badge-cetak';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success';
                    break;
                case 'CANCELED':
                    $badgeClass = 'badge-danger ';
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
            "recordsTotal" => $this->m_purchase->t_front_pp_apprv_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_pp_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    public function updateStatusPP()
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
        $builder = $db->table('sc_trx.pp');
        $builder->where('docno', $docno);
        $info = array('status' => $status);
        $update = $builder->update($info);

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
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
                'I.P',             // kode module dari menuprg
                'I.P.A.1'          // kode menu untuk PP
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }

    function clearEntryPP()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_pp_master_temp($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('purchase/trans/pp'));
        }
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
        $kodemenu='I.P.A.1'; $versirelease='I.P.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/pp'));
        }
        // =================================

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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['pp']) ?? '';
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

    public function getNextSuffixPP()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

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
        $nama       = trim($this->session->get('nama'));
        $logindate  = trim($this->session->get('logindate'));

        // ==========================================
        // VALIDASI INPUT AWAL CABANG / JOB
        // ==========================================
        $cabang = trim((string) $this->request->getPost('cabang'));

        // Cabang wajib dipilih, termasuk untuk user PT JATIM TAMAN STEEL
        if ($cabang === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang / Job wajib dipilih.'
            ]);
        }

        if (!$this->ppIsAllowedInitialBranch($cabang)) {
            $sessionBranchLabel = trim((string) $this->session->get('nmbranchjob'));

            if ($sessionBranchLabel === '') {
                $sessionBranchLabel = trim((string) $this->session->get('idbranchjob'));
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang / Job harus sesuai dengan cabang user login ('
                    . $sessionBranchLabel . ').'
            ]);
        }

        $docno = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut'); // 🔹 untuk update

        /* String To Time */
        $cek_logindate = date('Ym', strtotime($this->session->get('logindate')));
        $cek_docdate   = date('Ym', strtotime($this->request->getPost('docdate')));

        // ==========================================
        // PERIODE HARUS SAMA
        // ==========================================
        if ($cek_logindate != $cek_docdate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Periode tanggal dokumen harus sama dengan periode login. Periode aktif: '
                    . date('m-Y', strtotime($this->session->get('logindate')))
            ]);
        }

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
                'docdate'    => date('Y-m-d', strtotime($this->request->getPost('docdate'))),
                'cabang'     => $cabang,
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
        $capexno     = strtoupper($this->request->getPost('capexno'));
        $unit        = strtoupper($this->request->getPost('unit'));
        $qty         = $this->request->getPost('qty');
        $batch       = strtoupper(trim($this->request->getPost('batch') ?? '')); // 🔹 NEW
        $description = strtoupper($this->request->getPost('description'));

        $builderDetail = $db->table('sc_tmp.pp_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('capexno', $capexno)
            ->where('unit', $unit)
            ->where('batch', $batch)        // 🔹 NEW
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
                'capexno'     => $capexno,
                'unit'        => $unit,
                'batch'       => $batch,        // 🔹 NEW
                'qty'         => $qty,
                'qtypo'       => 0,
                'description' => $description,
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
            ]);

        } else {

            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $nmbarang
                . '|' . $docno
                . '|' . $nama
                . '|' . $inputdate;

            $uniqueid = hash('sha256', $rawUnique);

            // 🔹 INSERT
            $builderDetail->insert([
                'docno'       => $docno,
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'capexno'     => $capexno,
                'unit'        => $unit,
                'batch'       => $batch,        // 🔹 NEW
                'qty'         => $qty,
                'qtypo'       => 0,
                'description' => $description,
                'status'      => 'F',
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

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/pp'));
        }

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
        $param = " and docno='$docno' and inputby='$nama'";
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
                'message' => 'Data PP Detail berhasil dihapus'
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
            $row[] = $lm->batch;
            $row[] = $lm->capexno;
            /*status pp detail tidak usah dimunculkan dulu */
//            $row[] = trim($lm->status) == 'VP' ?
//            '<div class="text-center"><span style="font-size:12px" class="badge badge-danger w-100">' . 'Void PP' . '</span></div>' :
//            '<div class="text-center"><span style="font-size:12px" class="badge badge-primary w-100">' . 'Final User' . '</span></div>'  ;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'
                . number_format((float) $lm->qty, 2, '.', ',')
                . '</div>';
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
            $row[] = $lm->batch;
            $row[] = $lm->capexno;

            $row[] = $lm->qty;
            $row[] = $lm->unit;

            $row[] = $lm->description;
            $row[] = trim($lm->status) == 'VP' ?
                '<div class="text-center"><span style="font-size:12px" class="badge badge-danger w-100">' . 'Void PP' . '</span></div>' :
                '<div class="text-center"><span style="font-size:12px" class="badge badge-primary w-100">' . 'Final User' . '</span></div>'  ;
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


    public function finalEntryPP()
    {
        // ==========================================
        // SESSION
        // ==========================================

        $nama = trim(
            (string) $this->session->get('nama')
        );

        $logindate = trim(
            (string) $this->session->get('logindate')
        );


        // ==========================================
        // AMBIL DOCDATE
        // ==========================================

        $docdate = trim(
            (string) $this->request->getPost('docdate')
        );


        // ==========================================
        // VALIDASI TANGGAL
        // ==========================================

        if ($logindate === '' || $docdate === '') {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Tanggal login dan tanggal dokumen wajib diisi.'
                );
        }


        // ==========================================
        // STRING TO TIME
        // ==========================================

        $cek_logindate = date(
            'Ym',
            strtotime($logindate)
        );

        $cek_docdate = date(
            'Ym',
            strtotime($docdate)
        );


        // ==========================================
        // PERIODE HARUS SAMA
        // ==========================================

        if ($cek_logindate != $cek_docdate) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Periode tanggal dokumen harus sama dengan periode login. Periode aktif: '
                    . date(
                        'm-Y',
                        strtotime($logindate)
                    )
                );
        }


        // ==========================================
        // PARAMETER QUERY
        // ==========================================

        $param = " AND COALESCE(inputby,'')='$nama'";

        $paramdtl = "
        AND COALESCE(inputby, '') = '$nama'
        AND (
            COALESCE(unit, '') = ''
            OR qty = '0.00'
            OR qty = '0'
            OR COALESCE(nmbarang, '') = ''
            OR COALESCE(description, '') = ''
        )
    ";

        $paramdtl2 = "
        AND COALESCE(inputby, '') = '$nama'
    ";


        // ==========================================
        // AMBIL HEADER
        // ==========================================

        $header = $this->m_purchase->q_pp_master_temp(
            $param
        );

        $headerRow = $header->getRowArray();

        $status = trim(
            (string) ($headerRow['status'] ?? '')
        );


        // ==========================================
        // VALIDASI INPUT AWAL CABANG / JOB
        // ==========================================
        $headerCabang = trim(
            (string) ($headerRow['cabang'] ?? '')
        );

        // Dokumen yang sudah ada header tetap wajib memiliki cabang
        if ($headerRow && $headerCabang === '') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Cabang / Job pada dokumen belum diisi. '
                        . 'Silakan hapus draft PP lalu input ulang.'
                );
        }

        if (
            $headerCabang !== ''
            && !$this->ppIsAllowedInitialBranch($headerCabang)
        ) {
            $sessionBranchLabel = trim((string) $this->session->get('nmbranchjob'));

            if ($sessionBranchLabel === '') {
                $sessionBranchLabel = trim((string) $this->session->get('idbranchjob'));
            }

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Cabang / Job pada dokumen tidak sesuai dengan cabang user login ('
                        . $sessionBranchLabel . ').'
                );
        }


        // ==========================================
        // CEK DETAIL
        // ==========================================

        $cek = $this->m_purchase->q_pp_dtl_temp(
            $paramdtl
        );

        $cek2 = $this->m_purchase->q_pp_dtl_temp(
            $paramdtl2
        );


        // ==========================================
        // BUILDER
        // ==========================================

        $builder = $this->db->table(
            'sc_tmp.pp'
        );


        // ==========================================
        // INSERT TRX ERROR
        // ==========================================

        $builder_trxerror = $this->db->table(
            'sc_mst.trxerror'
        );


        // HAPUS ERROR LAMA

        $builder_trxerror
            ->where('userid', $nama)
            ->where('modul', 'I.P.A.1')
            ->delete();


        // ==========================================
        // VALIDASI DETAIL
        // ==========================================

        if (
            ($status === 'E' && $cek->getNumRows() > 0)
            || ($cek2->getNumRows() <= 0)
        ) {

            $infotrxerror = [

                'userid' => $nama,

                'errorcode' => 3,

                'nomorakhir1' => $cek->getNumRows(),

                'nomorakhir2' => $cek2->getNumRows(),

                'modul' => 'I.P.A.1'

            ];


            $builder_trxerror->insert(
                $infotrxerror
            );


            return redirect()->to(
                base_url('/purchase/trans/addPP')
            );
        }


        // ==========================================
        // AMBIL DATA POST
        // ==========================================

        $pemohon = strtoupper(
            trim(
                (string) $this->request->getPost('pemohon')
            )
        );

        $keterangan = strtoupper(
            trim(
                (string) $this->request->getPost('keterangan')
            )
        );

        $estpakai = trim(
            (string) $this->request->getPost('estpakai')
        );


        // ==========================================
        // CONVERT ESTPAKAI
        // ==========================================

        $estpakaiph = null;

        if (!empty($estpakai)) {

            $estpakaiph = date(
                'Y-m-d',
                strtotime($estpakai)
            );
        }


        // ==========================================
        // CONVERT DOCDATE
        // ==========================================

        $docdateph = date(
            'Y-m-d',
            strtotime($docdate)
        );


        // ==========================================
        // UPDATE HEADER
        // ==========================================

        $updateHeader = [

            'docdate' => $docdateph,

            'pemohon' => $pemohon,

            'keterangan' => $keterangan,

            'estpakai' => $estpakaiph

        ];


        $builder
            ->where('inputby', $nama)
            ->update($updateHeader);


        // ==========================================
        // UPDATE STATUS FINAL
        // ==========================================

        $info = [

            'status' => 'F'

        ];


        $builder
            ->where('inputby', $nama);


        if ($builder->update($info)) {

            return redirect()->to(
                base_url('/purchase/trans/pp')
            );
        }


        // ==========================================
        // JIKA UPDATE GAGAL
        // ==========================================

        $infotrxerror = [

            'userid' => $nama,

            'errorcode' => 3,

            'nomorakhir1' => $cek->getNumRows(),

            'nomorakhir2' => $cek2->getNumRows(),

            'modul' => 'I.P.A.1'

        ];


        $builder_trxerror->insert(
            $infotrxerror
        );


        return redirect()->to(
            base_url('/purchase/trans/addPP')
        );
    }


    function show_pp(){
        $module = 'I.P';
        $menu = 'I.P.A.1';
        $table = 'sc_trx.pp';
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/pp'));
        }
//       $builder = $builder
//            ->where('docno', $docno)
//            ->update([
//                'status'=> 'P',
//                'printby' => $nama,
//                'printdate' => date('Y-m-d H:i:s')
//            ]);


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

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
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
            $detail->namauser = $nama;

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
        $kodemenu='I.P.A.2'; $versirelease='I.P.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
        $kodemenu='I.P.A.2'; $versirelease='I.P.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
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
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER') {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updateVoidPP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This VoidPP : ' . $docno . '\')">
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

            if($canPrint && (trim($status) == 'FINAL USER' || trim($status) == 'CETAK/PRINT')){
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_voidpp') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Preview PP : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Preview Permintaan Pembelian 
                    </a>';
            }


            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-undo"></i> Batalkan Void PP</a>';
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
                case 'CANCELED':
                    $badgeClass = 'badge-danger ';
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('purchase/trans/voidpp'));
        }
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
        $kodemenu='I.P.A.2'; $versirelease='I.P.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/voidpp'));
        }
        // =================================

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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays


        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['voidpp']) ?? '';
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

    public function getNextSuffixVoidPP()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

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
        $idurut = $this->request->getPost('idurut'); // Untuk mode edit

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
                'docdate'   => date('Y-m-d', strtotime($this->request->getPost('docdate'))),
                'cabang'    => $this->request->getPost('cabang'),
                'pemohon'   => strtoupper($this->request->getPost('pemohon')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.voidpp_dtl');
        $insertCount = 0;
        $message = '';

        // =====================================================
        // CEK MODE: ADD atau EDIT
        // =====================================================
        if (!empty($idurut)) {
            // =====================================================
            // MODE EDIT - UPDATE QTY SAJA
            // =====================================================
            $uniqueid = $this->request->getPost('uniqueid');
            $qtyVoid = $this->request->getPost('qty'); // Qty yang akan divoid

            // Ambil data asli dari PP_DTL untuk validasi
            $ppDetail = $db->query("
            SELECT 
                qty,
                qtypo,
                qtyvoid,
                (qty - (qtypo + qtyvoid)) as sisa_qty
            FROM sc_trx.pp_dtl
            WHERE TRIM(uniqueid) = ?
        ", [$uniqueid])->getRow();

            if (!$ppDetail) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PP tidak ditemukan'
                ]);
            }

            // Validasi: qty void tidak boleh melebihi sisa qty
            if ($qtyVoid > $ppDetail->sisa_qty) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Qty void (' . $qtyVoid . ') melebihi sisa qty (' . $ppDetail->sisa_qty . ')'
                ]);
            }

            // Update di sc_tmp.voidpp_dtl
            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'        => $qtyVoid,
                'updateby'   => $nama,
                'updatedate' => date('Y-m-d H:i:s')
            ]);

            $message = 'Data void berhasil diupdate';

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
                capexno,
                qty,
                qtypo,
                qtyvoid,
                description
            FROM sc_trx.pp_dtl
            WHERE TRIM(docno) = ?
            AND TRIM(COALESCE(status,'')) <> 'VP'
        ", [$docnopp])->getResult();

            if (empty($ppDetails)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PP tidak ditemukan'
                ]);
            }

            foreach ($ppDetails as $row) {
                // $sisaQty = $row->qty - ($row->qtypo + $row->qtyvoid);
                // =====================================================
                // CEK APAKAH ITEM INI SEDANG DIEDIT
                // Cari di sc_tmp.voidpp_dtl dengan docno yang sama dan uniqueid yang sama
                // =====================================================
                $qtyVoidSaatIni = 0;

                $existingVoid = $db->query("
                SELECT qty 
                FROM sc_tmp.voidpp_dtl 
                WHERE
                uniqueid = ?
            ", [$row->uniqueid])->getRow();

                if ($existingVoid) {
                    $qtyVoidSaatIni = $existingVoid->qty;
                }

                // Hitung sisa qty dengan mengeluarkan qty void yang sedang diedit
                $sisaQty = $row->qty - ($row->qtypo + $row->qtyvoid - $qtyVoidSaatIni);

                // Jika sisa quantity <= 0, skip item ini
                if ($sisaQty <= 0) {
                    continue;
                }

                // Cek apakah item sudah ada di tmp
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
                        'capexno'     => $row->capexno,
                        'qty'         => $sisaQty, // Default ambil semua sisa
                        'description' => $row->description,
                        'inputby'     => $nama,
                        'inputdate'   => date('Y-m-d H:i:s')
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


    public function updateStatusVoidPP()
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
        $builder = $db->table('sc_trx.voidpp');
        $builder->where('docno', $docno);
        $info = array('status' => $status);
        $update = $builder->update($info);

        // Map status ke action (1 huruf)
        $action = '';
        switch ($status) {
            case 'C': $action = 'C'; break;  // CANCEL
            default:
                $action = 'U';  // default update
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // C / U (1 huruf)
                'I.P',             // kode module dari menuprg
                'I.P.A.2'          // kode menu untuk VOID PP
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update status'
            ]);
        }
    }


    function updateVoidPP()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_voidpp_master($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/voidpp'));
        }

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
        $param = " and docno='$docno' and inputby='$nama'";
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
            $row[] = $lm->capexno;
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
            $row[] = $lm->capexno;
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
                $docdateph = date('Y-m-d', strtotime($docdate));
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
        $module = 'I.P';
        $menu = 'I.P.A.2';
        $table = 'sc_trx.voidpp';
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/voidpp'));
        }
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

        $title = " Report Void Permintaan Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_voidpp/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_voidpp.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
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
            $detail->namauser = $nama;

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
        $kodemenu='I.P.A.3'; $versirelease='I.P.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
        $kodemenu='I.P.A.3'; $versirelease='I.P.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
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

            if ($canUpdate && trim($lm->inputby) == $nama &&  !in_array(trim($status), ['REVISION/EDITING', 'APPROVED','DITARIK LPB'])) {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updatePO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This PO : ' . $docno . '\')">
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

            //if($canPrint && trim($status) == 'APPROVED' && (empty($lm->printby) && empty($lm->printdate))){
            if (
                $canPrint &&
                in_array(trim($status), ['APPROVED', 'CETAK/PRINT'])
            ) {
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Preview PO : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print/Preview PO 
                </a>';
            }


            if (trim($status) &&  !in_array(trim($status), ['REVISION/EDITING', 'APPROVED','DITARIK LPB'])) {
                //if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }

            if ($canUpdate && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
//                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
//                    <i class="fa fa-undo"></i> Batalkan Purchase Order</a>';
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
                // if ($canApprove)   $menuContent .= $approveBtn;
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
                case 'DITARIK LPB':
                    $badgeClass = 'badge-cetak ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }


            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->senddate))
            );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d-m-Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;

            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


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


    public function list_po_apprv()
    {
        $list = $this->m_purchase->get_t_front_po_apprv_view();

        $data = array();

        $no = $_POST['start'];

        $kmenu = 'I.P.A.3';

        $nama = trim($this->session->get('nama'));
        $role = trim($this->session->get('roleid'));

        // =====================================================
        // HAK AKSES
        // =====================================================
        $datadtl['dtl_akses'] =
            $this->m_role
                ->detail_user_akses($role, $kmenu)
                ->getRowArray();

        $dataanu['userinfo'] =
            $this->m_user
                ->getUser(" and username='$nama'")
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

        $canApprove =
            isset($datadtl['dtl_akses']['a_approve1']) &&
            trim($datadtl['dtl_akses']['a_approve1']) === 't';


        // =====================================================
        // LOOP DATA
        // =====================================================
        foreach ($list as $lm) {

            // =================================================
            // STATUS
            // =================================================
            $status = strtoupper(trim(
                $lm->status_desc ?? $lm->status ?? ''
            ));


            // =================================================
            // HANYA FINAL USER
            // STATUS LAIN TIDAK DITAMPILKAN
            // =================================================
            if ($status !== 'FINAL USER') {
                continue;
            }


            // =================================================
            // NOMOR
            // =================================================
            $no++;

            $row = array();


            // =================================================
            // DOCNO
            // =================================================
            $docno = trim($lm->docno);

            $docnoHex = bin2hex($docno);


            // =================================================
            // RESET BUTTON
            // =================================================
            $updateBtn     = '';
            $detailBtn     = '';
            $printBtn      = '';
            $approveBtn    = '';
            $disapproveBtn = '';


            // =================================================
            // UPDATE
            // =================================================
            if (
                $canUpdate &&
                $status !== 'REVISION/EDITING' &&
                $status !== 'APPROVED'
            ) {

                $updateBtn = '
                <a
                    class="dropdown-item bg-warning"
                    href="' .
                    base_url('purchase/trans/updatePO') .
                    '/?id=' . $docnoHex .
                    '&docno=' . $docnoHex . '"
                    onclick="return confirm(\'Update This PO : ' .
                    htmlspecialchars($docno, ENT_QUOTES) .
                    '\')"
                >
                    <i class="fa fa-edit"></i>
                    Update PO
                </a>
            ';
            }


            // =================================================
            // DETAIL
            // =================================================
            if ($canView) {

                $detailBtn = '
                <a
                    class="dropdown-item"
                    style="background-color:#3badf6;"
                    href="' .
                    base_url('purchase/trans/detailPO') .
                    '/?id=' . $docnoHex .
                    '&docno=' . $docnoHex . '"
                    onclick="return confirm(\'View Detail PO : ' .
                    htmlspecialchars($docno, ENT_QUOTES) .
                    '\')"
                >
                    <i class="fa fa-eye"></i>
                    Detail PO
                </a>
            ';
            }


            // =================================================
            // PRINT
            // =================================================



            // =================================================
            // APPROVE
            // =================================================
            if (
                $canApprove &&
                $status !== 'APPROVED' &&
                $status !== 'REVISION/EDITING'
            ) {

                $approveBtn = '
                <a
                    class="dropdown-item bg-success"
                    href="#"
                    onclick="setToApproved(\'' .
                    htmlspecialchars($docno, ENT_QUOTES) .
                    '\'); return false;"
                >
                    <i class="fa fa-check-circle"></i>
                    Approve
                </a>
            ';
            }


            // =================================================
            // DISAPPROVE
            // =================================================
            if (
                $canApprove &&
                $status === 'APPROVED'
            ) {

                $disapproveBtn = '
                <a
                    class="dropdown-item bg-danger"
                    href="#"
                    onclick="setToDisapproved(\'' .
                    htmlspecialchars($docno, ENT_QUOTES) .
                    '\'); return false;"
                >
                    <i class="fa fa-times-circle"></i>
                    Disapprove
                </a>
            ';
            }


            // =================================================
            // MENU
            // =================================================
            $menuContent = '';

            // Karena hanya FINAL USER yang boleh masuk,
            // langsung gunakan hak akses yang sama.
            // if ($canUpdate) {
            //     $menuContent .= $updateBtn;
            // }

            if ($canView) {
                $menuContent .= $detailBtn;
            }

            // if ($canPrint) {
            //     $menuContent .= $printBtn;
            // }

            if ($canApprove) {
                $menuContent .= $approveBtn;
                $menuContent .= $disapproveBtn;
            }


            // =================================================
            // DROPDOWN
            // =================================================
            if ($menuContent !== '') {

                $dropdownMenu = '
                <div class="dropdown">

                    <button
                        class="btn btn-primary btn-sm dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="fa fa-bars"></i>
                    </button>

                    <div class="dropdown-menu">

                        ' . $menuContent . '

                    </div>

                </div>
            ';

            } else {

                $dropdownMenu = '';
            }


            // =================================================
            // COLUMN
            // =================================================
            $row[] = $no;

            $row[] = $dropdownMenu;

            $row[] = $lm->docno;


            // =================================================
            // DOC DATE
            // =================================================
            $row[] = !empty($lm->docdate)
                ? date(
                    'd-m-Y',
                    strtotime(trim($lm->docdate))
                )
                : '';


            // =================================================
            // JATUH TEMPO
            // =================================================
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime($docdate);

                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d-m-Y');

            } else {

                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;


            // =================================================
            // SEND DATE
            // =================================================
            $row[] = !empty($lm->senddate)
                ? date(
                    'd-m-Y',
                    strtotime(trim($lm->senddate))
                )
                : '';


            // =================================================
            // KETERANGAN
            // =================================================
            $row[] = $lm->keterangan;


            // =================================================
            // SUPPLIER
            // =================================================
            $row[] = $lm->kdsupplier;

            $row[] = $lm->nmsupplier;


            // =================================================
            // STATUS BADGE
            // =================================================
            $badgeClass = 'badge-secondary';

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

                case 'APPROVED':
                    $badgeClass = 'badge-primary';
                    break;

                default:
                    $badgeClass = 'badge-secondary';
                    break;
            }


            $row[] = '
            <div class="text-center">

                <span
                    style="font-size:12px"
                    class="badge ' .
                $badgeClass .
                ' w-100"
                >
                    ' .
                htmlspecialchars(
                    $status,
                    ENT_QUOTES,
                    'UTF-8'
                ) .
                '
                </span>

            </div>
        ';


            // =================================================
            // PUSH ROW
            // =================================================
            $data[] = $row;
        }


        // =====================================================
        // DATATABLE RESPONSE
        // =====================================================
        $output = array(

            "draw" =>
                $_POST['draw'],

            "recordsTotal" =>
                $this->m_purchase
                    ->t_front_po_apprv_view_count_all(),

            "recordsFiltered" =>
                $this->m_purchase
                    ->t_front_po_apprv_view_count_filtered(),

            "data" =>
                $data,
        );


        echo $this->fiky_encryption
            ->jDatatable($output);
    }

    function clearEntryPO()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_po_master_temp($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('purchase/trans/po'));
        }
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
        $kodemenu='I.P.A.3'; $versirelease='I.P.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/po'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_po_master_temp($param)->getRowArray();

        /* Jumlah detail temp milik user → dipakai untuk mengunci header PO */
        $paramdtlcount = " and coalesce(inputby,'$nama')='$nama'";
        $data['detailcount'] = $this->m_purchase->q_po_dtl_temp($paramdtlcount)->getNumRows();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/po/v_add_po',$data);
    }


    public function getBranchInfoPO()
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
        $prefix = trim($konfigurasiUmum[0]['po']) ?? '';
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

    public function getNextSuffixPO()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

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
        $nama            = trim($this->session->get('nama'));
        $docno           = strtoupper(trim($this->request->getPost('docno')));
        $docnopp         = strtoupper(trim($this->request->getPost('docnopp')));
        $idurut          = $this->request->getPost('idurut');
        $multidisc       = $this->request->getPost('multidisc');
        $totaldiscount   = $this->request->getPost('totaldiscount');
        $multidisctype   = $this->request->getPost('multidisctype');
        $idhistory_price = $this->request->getPost('idhistory_price');
        $capexno         = trim(strtoupper($this->request->getPost('capexno')));
        $batch           = trim($this->request->getPost('batch'));

// =============================================
// 🔹 ETD & ETD NOTES
// =============================================
        $etd      = trim((string) $this->request->getPost('etd'));
        $etdnotes = trim(strtoupper((string) $this->request->getPost('etdnotes')));

// Convert tanggal ETD dari dd-mm-yyyy → Y-m-d (pakai DateTime biar presisi)
        $etdDate = null;
        if (!empty($etd)) {
            $dt = \DateTime::createFromFormat('d-m-Y', $etd);
            if ($dt && $dt->format('d-m-Y') === $etd) {
                $etdDate = $dt->format('Y-m-d');
            } else {
                log_message('error', '[savePODetail] Format ETD tidak valid: ' . $etd);
            }
        }

        if ($etdnotes === null) {
            $etdnotes = '';
        }

// 🔹 COALESCE batch — jangan boleh NULL
        if ($batch === null || $batch === '') {
            $batch = '';
        }

        if (!$docno || !$docnopp) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno atau Docno PP tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        // =====================================================
        // ADVISORY LOCK
        // =====================================================
        $db->query("SELECT pg_advisory_xact_lock(hashtext(?))", [$docno]);

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.po');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        if ($exists == 0) {
            $isinclusive = strtoupper(trim(
                $this->request->getPost('isinclusive')
                ?? $dataprocess->isinclusive
                ?? 'NO'
            ));

            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $builderHeader->insert([
                'docno'          => $docno,
                'cabang'         => $this->request->getPost('cabang'),
                'docdate'        => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                'senddate'       => date('Y-m-d', strtotime(trim($this->request->getPost('senddate')))),
                'jthtempo'       => $this->request->getPost('jthtempo'),
                'isinclusive'    => $isinclusive,
                'kdsupplier'     => strtoupper($this->request->getPost('kdsupplier')),
                'nmsupplier'     => strtoupper($this->request->getPost('nmsupplier')),
                'alamatsupplier' => strtoupper($this->request->getPost('alamatsupplier')),
                'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                'idtax'          => strtoupper($this->request->getPost('idtax')),
                'currcode'       => strtoupper($this->request->getPost('currcode')),
                'kurs'           => strtoupper($this->request->getPost('kurs')),
                'keterangan'     => strtoupper($this->request->getPost('keterangan')),
                'status'         => 'E',
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.po_dtl');
        $insertCount   = 0;
        $message       = '';

        // =====================================================
        // 🔹 AMBIL SENDDATE DARI HEADER PO (untuk default ETD)
        // =====================================================
        $senddateDefault = null;

        $headerPO = $builderHeader
            ->select('senddate')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if ($headerPO && !empty($headerPO['senddate'])) {
            $senddateDefault = $headerPO['senddate'];
        }

        // =====================================================
        // CEK MODE: EDIT / ADD
        // =====================================================
        if (!empty($idurut)) {

            // =====================================================
            // MODE EDIT - UPDATE DATA
            // =====================================================
            $uniqueid      = $this->request->getPost('uniqueid');
            $uniqueidpo    = $this->request->getPost('uniqueidpo');
            $qty           = $this->request->getPost('qty');
            $qtybonus      = $this->request->getPost('qtybonus') ?: 0;
            $harga         = $this->request->getPost('harga') ?: 0;
            $multidisc     = $this->request->getPost('multidisc') ?: 0;
            $nilai         = $this->request->getPost('nilai') ?: 0;
            $descriptionpo = strtoupper($this->request->getPost('descriptionpo'));

            if (empty($uniqueidpo)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'uniqueidpo wajib dikirim saat mode edit'
                ]);
            }

            // =====================================================
            // VALIDASI QTY TERHADAP SISA QTY PP
            // =====================================================
            $ppDetail = $db->query("
            SELECT
                ppd.qty,
                ppd.qtypo,
                ppd.qtyvoid,
                COALESCE(p1.qty, p2.qty, p3.qty,
                         COALESCE(ppd.qtypo, 0) + COALESCE(ppd.qtyvoid, 0),
                         0) AS qty_pengurang
            FROM sc_trx.pp_dtl ppd
            LEFT JOIN LATERAL (
                SELECT SUM(qty) AS qty
                FROM sc_tmp.po_dtl
                WHERE uniqueid = ppd.uniqueid
                  AND rtrim(uniqueidpo) != rtrim(?)
            ) p1 ON TRUE
            LEFT JOIN LATERAL (
                SELECT SUM(qty) AS qty
                FROM sc_trx.po_dtl
                WHERE uniqueid = ppd.uniqueid
            ) p2 ON TRUE
            LEFT JOIN LATERAL (
                SELECT SUM(qty) AS qty
                FROM sc_trx.voidpp_dtl
                WHERE uniqueid = ppd.uniqueid
            ) p3 ON TRUE
            WHERE TRIM(ppd.uniqueid) = TRIM(?)
            LIMIT 1
        ", [$uniqueidpo, $uniqueid])->getRow();

            if (!$ppDetail) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PP tidak ditemukan'
                ]);
            }

            $sisaTersedia = $ppDetail->qty - $ppDetail->qty_pengurang;

            if ($qty > $sisaTersedia) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Qty (' . $qty . ') melebihi sisa qty PP yang tersedia (' . $sisaTersedia . ')'
                ]);
            }

            // Ambil kurs dari header PO
            $poHeader = $builderHeader->select('kurs, idtax')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()->getRowArray();
            $kurs  = $poHeader['kurs'] ?? 0;
            $idtax = $poHeader['idtax'] ?? '';

            $nilaikonversi = $nilai * $kurs;

            $nilaipajak = 0;
            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                $taxDetails = $db->table('sc_mst.tax_dtl')
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                $totalPersentase = 0;
                foreach ($taxDetails as $tax) {
                    $totalPersentase += $tax['percentation'] ?? 0;
                }

                $nilaipajak = $nilai * $totalPersentase / 100;
            } else {
                $nilaipajak = $nilai;
            }

            // =====================================================
            // UPDATE DETAIL — pakai uniqueidpo, batch pakai COALESCE
            // =====================================================
            // =============================================
            // 🔹 ETD: kalau kosong → pakai senddate header
            // =============================================
            $etdFinal = $etdDate;
            if (empty($etdFinal) && !empty($senddateDefault)) {
                $etdFinal = $senddateDefault;
            }

            $builderDetail
                ->where('uniqueidpo', $uniqueidpo)
                ->where('docno', $docno)
                ->update([
                    'qty'             => $qty,
                    'qtybonus'        => $qtybonus,
                    'harga'           => $harga,
                    'multidisc'       => $multidisc,
                    'nilai'           => $nilai,
                    'nilaikonversi'   => $nilaikonversi,
                    'nilaipajak'      => $nilaipajak,
                    'kurs'            => strtoupper($this->request->getPost('kurs')),
                    'idtax'           => strtoupper($this->request->getPost('idtax')),
                    'currcode'        => strtoupper($this->request->getPost('currcode')),
                    'totaldiscount'   => $this->request->getPost('totaldiscount'),
                    'multidisctype'   => $this->request->getPost('multidisctype'),
                    'idhistory_price' => $this->request->getPost('idhistory_price'),
                    'descriptionpo'   => $descriptionpo,
                    'capexno'         => $capexno,
                    'etd'             => $etdFinal,      // 🔹 fallback ke senddate kalau kosong
                    'etdnotes'        => $etdnotes,
                    'updateby'        => $nama,
                    'updatedate'      => date('Y-m-d H:i:s')
                ]);

            $message = 'Data berhasil diupdate';

        } else {

            // =====================================================
            // MODE ADD - INSERT DATA DARI PP
            // Konsep:
            //   - qty insert = SISA (tidak boleh melebihi)
            //   - selama sisa > 0 → INSERT BARIS BARU
            //   - uniqueidpo di-generate otomatis oleh trigger AFTER INSERT
            //   - baris lama JANGAN di-update
            //   - cegah double insert dalam 1x request via tracking array
            // =====================================================
            $ppDetails = $db->query("
        SELECT
            ppd.docno,
            ppd.idbarang,
            ppd.uniqueid,
            ppd.nmbarang,
            ppd.unit,
            ppd.qty,
            ppd.capexno,
            ppd.qtypo,
            ppd.qtyvoid,
            ppd.description,
            COALESCE(ppd.batch, '') AS batch,

            COALESCE(p1.qty, p2.qty, p3.qty,
                     COALESCE(ppd.qtypo, 0) + COALESCE(ppd.qtyvoid, 0),
                     0) AS qty_pengurang

        FROM sc_trx.pp_dtl ppd

        LEFT JOIN LATERAL (
            SELECT SUM(qty) AS qty
            FROM sc_tmp.po_dtl
            WHERE uniqueid = ppd.uniqueid
            -- 🔹 tidak mengecualikan docno
        ) p1 ON TRUE

        LEFT JOIN LATERAL (
            SELECT SUM(qty) AS qty
            FROM sc_trx.po_dtl
            WHERE uniqueid = ppd.uniqueid
        ) p2 ON TRUE

        LEFT JOIN LATERAL (
            SELECT SUM(qty) AS qty
            FROM sc_trx.voidpp_dtl
            WHERE uniqueid = ppd.uniqueid
        ) p3 ON TRUE

        WHERE TRIM(ppd.docno) = TRIM(?)
          AND TRIM(COALESCE(ppd.status,'')) <> 'VP'

        ORDER BY ppd.idurut
    ", [$docnopp])->getResult();

            if (empty($ppDetails)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PP tidak ditemukan'
                ]);
            }

            // 🔹 Tracking per request — cegah double insert untuk sisa yang sama
            $insertedKeys = [];

            foreach ($ppDetails as $row) {

                // =====================================================
                // HITUNG SISA = qty_pp − qty_pengurang
                // =====================================================
                $sisaQty = (float)$row->qty - (float)$row->qty_pengurang;

                // Skip kalau sisa <= 0
                if ($sisaQty <= 0) {
                    continue;
                }

                // =====================================================
                // CEGAH DOUBLE INSERT DALAM 1x REQUEST
                // =====================================================
                $key = $row->uniqueid . '|' . $sisaQty;

                if (isset($insertedKeys[$key])) {
                    continue;
                }

                // =====================================================
                // BATCH COALESCE
                // =====================================================
                $batchFromPP = $row->batch ?? '';
                if ($batchFromPP === null) {
                    $batchFromPP = '';
                }

                // =====================================================
                // INSERT BARIS BARU
                // — uniqueidpo di-generate otomatis oleh trigger
                // =====================================================
                // =============================================
                // 🔹 ETD: kalau user tidak isi → pakai senddate
                // =============================================
                $etdFinal = $etdDate;
                if (empty($etdFinal) && !empty($senddateDefault)) {
                    $etdFinal = $senddateDefault;
                }

                $payload = [
                    'docno'           => $docno,
                    'docnopp'         => $docnopp,
                    'idbarang'        => $row->idbarang,
                    'uniqueid'        => $row->uniqueid,
                    'nmbarang'        => $row->nmbarang,
                    'unit'            => $row->unit,
                    'capexno'         => $row->capexno,
                    'qty'             => $sisaQty,
                    'batch'           => $batchFromPP,
                    'kurs'            => strtoupper($this->request->getPost('kurs')),
                    'idtax'           => strtoupper($this->request->getPost('idtax')),
                    'currcode'        => strtoupper($this->request->getPost('currcode')),
                    'totaldiscount'   => $this->request->getPost('totaldiscount'),
                    'multidisctype'   => $this->request->getPost('multidisctype'),
                    'idhistory_price' => $this->request->getPost('idhistory_price'),
                    'qtybonus'        => 0,
                    'harga'           => 0,
                    'multidisc'       => 0,
                    'nilai'           => 0,
                    'descriptionpp'   => $row->description,
                    'descriptionpo'   => $row->description,
                    'etd'             => $etdFinal,      // 🔹 default = senddate kalau kosong
                    'etdnotes'        => $etdnotes,
                    'inputby'         => $nama,
                    'inputdate'       => date('Y-m-d H:i:s')
                ];

                $ok = $builderDetail->insert($payload);

                if (!$ok) {
                    log_message('error', '[savePODetail][ADD] INSERT FAILED: ' . json_encode($db->error()));
                    log_message('error', '[savePODetail][ADD] Payload: ' . json_encode($payload));
                } else {
                    $insertedKeys[$key] = true;
                    $insertCount++;
                }
            }

            $message = $insertCount > 0
                ? "$insertCount item berhasil ditambahkan"
                : "Tidak ada sisa untuk ditambahkan";
        }

        // =====================================================
        // HITUNG ULANG DPP & TOTAL HEADER
        // =====================================================
        $poHeader = $builderHeader->select('idtax')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()->getRowArray();
        $idtax = $poHeader['idtax'] ?? '';

        $totalDpp = $db->table('sc_tmp.po_dtl')
            ->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        $dpp = $totalDpp['total_dpp'] ?? 0;

        $jumlahPajak = 0;
        if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {
            $taxDetails = $db->table('sc_mst.tax_dtl')
                ->select('percentation')
                ->where('idtax', $idtax)
                ->get()
                ->getResultArray();

            foreach ($taxDetails as $tax) {
                $persentase = $tax['percentation'] ?? 0;
                $jumlahPajak += $dpp * ($persentase / 100);
            }
        }

        $total = $dpp + $jumlahPajak;

        $builderHeader->where('docno', $docno)
            ->where('inputby', $nama)
            ->update([
                'dpp'         => number_format($dpp, 2, '.', ''),
                'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                'total'       => number_format($total, 2, '.', ''),
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
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
        $info = array('status' => $status);
        $update = $builder->update($info);

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
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
                'I.P',             // kode module dari menuprg
                'I.P.A.3'          // kode menu untuk PO
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update status'
            ]);
        }
    }



    function updatePO()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_po_master($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/po'));
        }

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
        $param = " and docno='$docno' and inputby='$nama'";
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
            ->select("
            idurut,
            TRIM(docno) AS docno,
            TRIM(docnopp) AS docnopp,
            TRIM(idbarang) AS idbarang,
            TRIM(nmbarang) AS nmbarang,
            TRIM(unit) AS unit,
            qty,
            qtybonus,
            harga,
            multidisc,
            nilai,
            descriptionpo,
            descriptionpp,
            TRIM(status) AS status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            TRIM(docnotmp) AS docnotmp,
            uniqueid,
            uniqueidpo,
            TRIM(idtax) AS idtax,
            TRIM(currcode) AS currcode,
            kurs,
            nilaikonversi,
            nilaipajak,
            qtylpb,
            qtyvoid,
            TRIM(capexno) AS capexno,
            TRIM(idhistory_price) AS idhistory_price,
            TRIM(multidisctype) AS multidisctype,
            totaldiscount,
            TRIM(batch) AS batch,               -- 🔹 NEW
            etd,                                  -- 🔹 NEW
            TRIM(etdnotes) AS etdnotes           -- 🔹 NEW
        ", false)
            ->where('uniqueidpo', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status'  => false,
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
            // 1. AMBIL docno DARI SALAH SATU DETAIL SEBELUM DIHAPUS
            $docno = '';
            $firstDetail = $builder
                ->select('docno')
                ->whereIn('uniqueidpo', $ids)
                ->get()
                ->getRowArray();

            if ($firstDetail) {
                $docno = $firstDetail['docno'];
            }

            $builder
                ->whereIn('uniqueidpo', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // 2. RECALCULATE HEADER
            if (!empty($docno)) {
                $this->recalculateHeaderPO($docno);
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

    private function recalculateHeaderPO($docno)
    {
        $db = \Config\Database::connect();
        $builderHeader = $db->table('sc_tmp.po');

        // Ambil idtax dari header
        $poHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
        $idtax = $poHeader['idtax'] ?? '';

        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.po_dtl');
        $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $dpp = $totalDpp['total_dpp'] ?? 0;

        // Hitung jumlah pajak
        $jumlahPajak = 0;
        if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {
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

        // Hitung total
        $total = $dpp + $jumlahPajak;

        // Update header
        $builderHeader->where('docno', $docno)->update([
            'dpp' => number_format($dpp, 2, '.', ''),
            'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
            'updateby' => session('nama'),
            'updatedate' => date('Y-m-d H:i:s')
        ]);
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
            $row[] = $lm->uniqueidpo;
//        $row[] = $lm->uniqueid;
            //$row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopp;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->capexno;

            // =========================================
            // 🔹 ETD (menggantikan Multi Disc)
            // =========================================
            $etdFormatted = !empty($lm->etd)
                ? date('d-m-Y', strtotime($lm->etd))
                : '';
            $row[] = $etdFormatted;

            // =========================================
            // 🔹 ETD NOTES (menggantikan Total Disc)
            // =========================================
            $row[] = $lm->etdnotes;

            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            /*$row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 2, '.', ',') . '</div>';*/
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 2, '.', ',') . '</div>';

            // Multi Disc & Total Disc tetap di-comment
//        $multidisctype = trim($lm->multidisctype ?? '');
//        $multidisc = (float) ($lm->multidisc ?? 0);
//        $row[] = '<div class="ratakanan">' .
//            number_format($multidisc, 2, '.', ',') .
//            (
//            $multidisc > 0 &&
//            in_array($multidisctype, ['PERCENT', '%'])
//                ? ' %'
//                : ''
//            ) .
//            '</div>';
//        $row[] = '<div class="ratakanan">'. number_format($lm->totaldiscount, 2, '.', ',') . '</div>';

            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
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
            $row[] = $lm->batch;
            $row[] = $lm->capexno;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
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

        // =====================================================
        // CEK QTY DAN HARGA DETAIL PO
        // =====================================================

        $cekQtyHargaKosong = $this->db
            ->table('sc_tmp.po_dtl')
            ->where('inputby', $nama)
            ->groupStart()
            ->where('qty IS NULL', null, false)
            ->orWhere('qty <=', 0)
            ->orWhere('harga IS NULL', null, false)
            ->orWhere('harga <=', 0)
            ->groupEnd()
            ->countAllResults();



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
            $kdsupplier = trim($this->request->getPost('kdsupplier'));
            $nmsupplier = trim($this->request->getPost('nmsupplier'));
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
                $docdateph = date('Y-m-d', strtotime($docdate));
            }

            $senddateph = null;
            if (!empty($senddate)) {
                $senddateph = date('Y-m-d', strtotime($senddate));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                'senddate'       => $senddateph,
                'jthtempo'       => $jthtempo,
                'kdsupplier'     => strtoupper($kdsupplier),
                'nmsupplier'     => strtoupper($nmsupplier),
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


            // =====================================================
// VALIDASI QTY DAN HARGA
// =====================================================

            if ($cekQtyHargaKosong > 0) {

                $this->session->setFlashdata(
                    'error',
                    'Tidak dapat Final Entry. Masih terdapat ' .
                    $cekQtyHargaKosong .
                    ' item dengan Quantity atau Harga kosong / 0.'
                );

                return redirect()->to(
                    base_url('/purchase/trans/addPO')
                );

            } else {

                if ($builder->update($info)) {

                    $paramerror = " and userid='$nama' and modul='I.P.A.3'";

                    $dtlerror = $this->m_trxerror
                        ->q_trxerror($paramerror)
                        ->getRowArray();

                    $count_err = $this->m_trxerror
                        ->q_trxerror($paramerror)
                        ->getNumRows();

                    return redirect()->to(
                        base_url('/purchase/trans/po')
                    );

                } else {

                    $infotrxerror = array(
                        'userid'       => $nama,
                        'errorcode'    => 3,
                        'nomorakhir1'  => $cek->getNumRows(),
                        'nomorakhir2'  => $cek2->getNumRows(),
                        'modul'        => 'I.P.A.3',
                    );

                    $builder_trxerror->insert($infotrxerror);

                    return redirect()->to(
                        base_url('/purchase/trans/addPO')
                    );
                }
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

        $header = $this->m_purchase->q_po_master_temp($param)->getRowArray();
        $cek = $this->m_purchase->q_po_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_po_dtl_temp($paramdtl2);

        if(!$header){
            return redirect()->to(base_url('/purchase/trans/addPO'));
        }

        $status = trim($header['status']);

        /*
        ==========================
        TRX ERROR CLEAN
        ==========================
        */

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama)
            ->where('modul','I.P.A.3')
            ->delete();

        if (($status === 'E' && $cek->getNumRows() > 0) || ($cek2->getNumRows() <= 0))
        {
            $builder_trxerror->insert([
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.3',
            ]);

            return redirect()->to(base_url('/purchase/trans/addPO'));
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
            base_url('/purchase/trans/addUMB')
        );
    }


    function show_po(){
        $module = 'I.P';
        $menu = 'I.P.A.3';
        $table = 'sc_trx.po';
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/po'));
        }
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

        $title = " Report Purchase Order";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_po/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_po.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
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



            $detail->namauser = $nama;

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









    // =================================== PO ===========================================


    public function voidpo()
    {
        $data['title']="Void PO";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.4'; $versirelease='I.P.A.4/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.4'";
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
        $dtl = $this->m_purchase->q_voidpo_master_temp($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('purchase/trans/clearEntryVoidPO');
            $urlnext = base_url('purchase/trans/addVoidPO');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.4';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/voidpo/v_list_voidpo',$data);
    }

    function detailVoidPO()
    {
        /* Penambahan Squence */
        $data['title']="Detail Void PO";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }
        $kodemenu='I.P.A.4'; $versirelease='I.P.A.4/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.4'";
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
        $data['dtldata'] = $this->m_purchase->q_voidpo_master($param)->getRowArray();
        return $this->template->render('purchase/voidpo/v_detail_voidpo',$data);
    }

    function list_voidpo(){
        $list = $this->m_purchase->get_t_front_voidpo_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.4';
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
                    href="' . base_url('purchase/trans/updateVoidPO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Void PO : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Void PO 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailVoidPO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Void PO : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Void PO 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Void PO : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Void PO 
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


            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd-m-Y',
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

            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_voidpo_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_voidpo_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_voidpo_apprv(){
        $list = $this->m_purchase->get_t_front_voidpo_apprv_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.4';
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
                    href="' . base_url('purchase/trans/updateVoidPO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Void PO : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Void PO 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailVoidPO') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Void PO : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Void PO 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_voidpo') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Void PO : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Void PO 
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
                'd-m-Y',
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
                'd-m-Y',
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
            "recordsTotal" => $this->m_purchase->t_front_voidpo_apprv_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_voidpo_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryVoidPO()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_voidpo_master_temp($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('purchase/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.voidpo');
        $builder_dtl = $this->db->table('sc_tmp.voidpo_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('purchase/trans/voidpo'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/voidpo'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }

    }

    function addVoidPO()
    {
        /* Penambahan Squence */
        $data['title']="Input VoidPO";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.4'; $versirelease='I.P.A.4/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.4'";
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
        $data['mst'] = $this->m_purchase->q_voidpo_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_voidpo_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/voidpo/v_add_voidpo',$data);
    }


    public function getBranchInfoVoidPO()
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
        $prefix = trim($konfigurasiUmum[0]['voidpo']) ?? '';
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

    public function getNextSuffixVoidPO()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.voidpo')
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

    public function initVoidPOHeader()
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

        $builder = $this->db->table('sc_tmp.voidpo');
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



    public function saveVoidPODetail()
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

        $builderPO = $db->table('sc_trx.po');
        $poData = $builderPO
            ->select('currcode, kurs, idtax, isinclusive')
            ->where('docno', $docnopo)
            ->get()
            ->getRowArray();

        // Jika data PO tidak ditemukan, beri response error
        if (!$poData) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => "Data PO dengan nomor {$docnopo} tidak ditemukan"
            ]);
        }

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.voidpo');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST

        if ($exists == 0) {
            $currcode   = $poData['currcode'] ?? '';
            $kurs       = $poData['kurs'] ?? 0;
            $idtax      = $poData['idtax'] ?? '';
            $isinclusive = strtoupper(trim($poData['isinclusive'] ?? 'NO'));
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
                'idtax'         => strtoupper($idtax),
                'currcode'      => strtoupper($currcode),
                'kurs'          => $kurs ?? 1,                                     // ← Dari PO
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.voidpo_dtl');
        $insertCount = 0;
        $message = '';

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan apa adanya
            // =====================================================
            // MODE EDIT - UPDATE DATA
            // =====================================================
            $qty         = $this->request->getPost('qty');
            // $qtybonus    = $this->request->getPost('qtybonus') ?: 0;
            $harga       = $this->request->getPost('harga') ?: 0;
            // $multidisc   = $this->request->getPost('multidisc') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            // $descriptionpo = strtoupper($this->request->getPost('descriptionpo'));

            // =====================================================
            // VALIDASI QTY TERHADAP SISA QTY PO (untuk VOID PO)
            // =====================================================
            $poDetail = $db->query("
                SELECT
                    qty,
                    COALESCE(qtylpb, 0)  AS qtylpb,
                    COALESCE(qtyvoid, 0) AS qtyvoid,
                    (qty - COALESCE(qtylpb, 0) - COALESCE(qtyvoid, 0)) AS sisa_qty
                FROM sc_trx.po_dtl
                WHERE TRIM(uniqueid) = ?
            ", [$uniqueid])->getRow();

            if (!$poDetail) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PO tidak ditemukan'
                ]);
            }

            // Sisa yang bisa di-void = sisa_qty dari PO
            // (qtyvoid sudah realtime dari trigger Void PO final)
            $sisaTersedia = $poDetail->sisa_qty;

            if ($qty > $sisaTersedia) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Qty void (' . $qty . ') melebihi sisa qty PO yang bisa di-void (' . $sisaTersedia . ')'
                ]);
            }


            // Ambil kurs dari header PO
            $voidpoHeader = $builderHeader->select('kurs, idtax')
                ->where('inputby', $nama)
                ->where('docno', $docno)
                ->get()->getRowArray();
            $kurs = $voidpoHeader['kurs'] ?? 0;
            $idtax = $voidpoHeader['idtax'] ?? '';

            // Hitung nilaikonversi = nilai * kurs
            $nilaikonversi = $nilai * $kurs;

            // Hitung nilaipajak berdasarkan idtax
            $nilaipajak = 0;
            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                // Ambil detail tax dari sc_mst.tax_dtl
                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                $totalPersentase = 0;
                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $totalPersentase += $persentase;
                }

                // Hitung nilaipajak = nilai + (nilai * totalPersentase / 100)
                // $nilaipajak = $nilai + ($nilai * $totalPersentase / 100);
                $nilaipajak = $nilai * $totalPersentase / 100;
            } else {
                // Jika NON pajak, nilaipajak sama dengan nilai
                $nilaipajak = $nilai;
            }

            $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)->update([
                    'qty'          => $qty,
                    // 'qtybonus'     => $qtybonus,
                    'harga'        => $harga,
                    // 'multidisc'    => $multidisc,
                    'nilai'        => $nilai,
                    'nilaikonversi' => $nilaikonversi,
                    'nilaipajak'   => $nilaipajak,
                    // 'descriptionpo' => $descriptionpo,
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
                    *
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
                $qtyVoidSaatIni = 0;

                $existingVoid = $db->query("
                    SELECT qty 
                    FROM sc_tmp.voidpo_dtl 
                    WHERE
                    uniqueid = ?
                ", [$row->uniqueid])->getRow();

                if ($existingVoid) {
                    $qtyVoidSaatIni = $existingVoid->qty;
                }

                $sisaQty = $row->qty - ($row->qtylpb + $row->qtyvoid - $qtyVoidSaatIni);


                // Jika sisa quantity <= 0, skip item ini
                if ($sisaQty <= 0) {
                    continue; // Lewati item ini
                }

                $duplicate = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnopo'       => $docnopo,
                        'idbarang'      => $row->idbarang,
                        'capexno'      => $row->capexno,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $sisaQty,
                        'harga'         => $row->harga, // Default 0 untuk new insert
                        'nilai'         => $row->nilai, // Default 0 untuk new insert
                        'nilaipajak'    => $row->nilaipajak,
                        'nilaikonversi' => $row->nilaikonversi,
                        'currcode'         => $row->currcode,
                        'kurs'          => $row->kurs,
                        'idtax'         => $row->idtax,
                        // 'qtybonus'      => 0, // Default 0 untuk new insert
                        // 'multidisc'     => 0, // Default 0 untuk new insert
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

        $voidpoHeader = $builderHeader->select('idtax')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()->getRowArray();
        $idtax = $voidpoHeader['idtax'] ?? '';

        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.voidpo_dtl');
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

        // Update header Void PO
        $builderHeader->where('docno', $docno)->where('inputby', $nama)->update([
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


    public function updateStatusVoidPO()
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
        $builder = $db->table('sc_trx.voidpo');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        // Map status ke action (1 huruf)
        $action = '';
        switch ($status) {
            case 'C': $action = 'C'; break;  // CANCEL
            default:
                $action = 'U';  // default update
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // C / U (1 huruf)
                'I.P',             // kode module dari menuprg
                'I.P.A.4'          // kode menu untuk VOID PP
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update status'
            ]);
        }
    }



    function updateVoidPO()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_voidpo_master($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.voidpo');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addVoidPO'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }
    }

    function showing_voidpotrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_voidpo_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_voidpotemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby='$nama'";
        $data = $this->m_purchase->q_voidpo_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_voidpo_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_voidpo_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_voidpo_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.voidpo_dtl')
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

    public function delete_voidpo_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.voidpo_dtl');
        $nama    = trim($this->session->get('nama'));

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

            // ======================================
            // AMBIL DOCNO DARI DETAIL
            // ======================================
            $rows = $builder
                ->select('docno')
                ->whereIn('idurut', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // karena Anda yakin semua docno sama
            $docno = $rows[0]['docno'];

            // OPTIONAL VALIDATION (lebih aman)
            foreach ($rows as $r) {
                if ($r['docno'] !== $docno) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Docno tidak konsisten'
                    ]);
                }
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
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

            // ======================================
            // RECALCULATE HEADER
            // ======================================
            $builderHeader = $db->table('sc_tmp.voidpo');

            $voidpoHeader = $builderHeader
                ->select('idtax')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $idtax = $voidpoHeader['idtax'] ?? '';

            $builderTotalDpp = $db->table('sc_tmp.voidpo_dtl');
            $totalDpp = $builderTotalDpp
                ->select('COALESCE(SUM(nilai), 0) as total_dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $dpp = $totalDpp['total_dpp'] ?? 0;

            $jumlahPajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {

                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $jumlahPajak += $dpp * ($persentase / 100);
                }
            }

            $total = $dpp + $jumlahPajak;

            $builderHeader
                ->where('docno', $docno)
                ->update([
                    'dpp'         => number_format($dpp, 2, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                    'total'       => number_format($total, 2, '.', ''),
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

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

    function list_tmp_voidpo_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_voidpo_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->capexno;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '% </div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            // $row[] = $lm->descriptionpo;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_voidpo_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_voidpo_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_voidpo_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_voidpo_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->capexno;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            // $row[] = $lm->descriptionpo;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;

        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_voidpo_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_voidpo_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryVoidPO(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR COALESCE(descriptionpo, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_purchase->q_voidpo_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_purchase->q_voidpo_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_voidpo_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.voidpo');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.4');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.4',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/purchase/trans/addVoidPO'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdsupplier   = trim($this->request->getPost('kdsupplier'));
            $alamatsupplier   = trim($this->request->getPost('alamatsupplier'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $currcode   = trim($this->request->getPost('currcode'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            $idtax   = trim($this->request->getPost('idtax'));
            // $syarat   = trim($this->request->getPost('syarat'));
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
                $docdateph = date('Y-m-d', strtotime($docdate));
            }

            // $senddateph = null;
            // if (!empty($senddate)) {
            //     $senddateph = date('Y-m-d', strtotime(str_replace('-', '/', $senddate)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                // 'senddate'       => $senddateph,
                'jthtempo'       => $jthtempo,
                'kdsupplier'     => strtoupper($kdsupplier),
                'alamatsupplier' => strtoupper($alamatsupplier),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                'currcode'       => $currcode,
                'kurs'           => $kurs_clean,
                'isinclusive'    => strtoupper($isinclusive),
                'idtax'          => strtoupper($idtax)
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
                $paramerror=" and userid='$nama' and modul='I.P.A.4'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/purchase/trans/voidpo'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.P.A.4',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/purchase/trans/addVoidPO'));
            }



        }

    }


    function show_voidpo(){
        $module = 'I.P';
        $menu = 'I.P.A.4';
        $table = 'sc_trx.voidpo';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.voidpo');
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/voidpo'));
        }
        // $builder = $builder
        //     ->where('docno', $docno)
        //     ->update([
        //         'status'=> 'P',
        //         'printby' => $nama,
        //         'printdate' => date('Y-m-d H:i:s')
        //     ]);


        $enc_docno = $this->fiky_encryption->sealed($docno);

        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Report Void Purchase Order";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_voidpo/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_voidpo.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_voidpo(){
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
        $datamst = $this->m_purchase->q_voidpo_master($param);
        $datadtl = $this->m_purchase->q_voidpo_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {

            $detail->namauser = $nama;
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




    // =================================== UMB ===========================================


    public function umb()
    {
        $data['title']="Uang Muka Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.5'; $versirelease='I.P.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.5'";
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
        $dtl = $this->m_purchase->q_umb_master_temp($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('purchase/trans/clearEntryUMB');
            $urlnext = base_url('purchase/trans/addUMB');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.5';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/umb/v_list_umb',$data);
    }

    function detailUMB()
    {
        /* Penambahan Squence */
        $data['title']="Detail Uang Muka Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/umb'));
        }
        $kodemenu='I.P.A.5'; $versirelease='I.P.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.5'";
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
        $data['dtldata'] = $this->m_purchase->q_umb_master($param)->getRowArray();
        return $this->template->render('purchase/umb/v_detail_umb',$data);
    }

    function list_umb(){
        $list = $this->m_purchase->get_t_front_umb_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.5';
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
                    href="' . base_url('purchase/trans/updateUMB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Uang Muka Pembelian : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Uang Muka Pembelian 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailUMB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Uang Muka Pembelian : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Uang Muka Pembelian 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Uang Muka Pembelian : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Uang Muka Pembelian 
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

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd-m-Y',
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
            "recordsTotal" => $this->m_purchase->t_front_umb_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_umb_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function clearEntryUMB()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_umb_master_temp($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('purchase/trans/po'));
        }
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('purchase/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.umb');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('purchase/trans/umb'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/umb'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('purchase/trans/umb'));
        }

    }

    function addUMB()
    {
        /* Penambahan Squence */
        $data['title']="Input Uang Muka Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.5'; $versirelease='I.P.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.5'";
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
        $data['mst'] = $this->m_purchase->q_umb_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/umb'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_umb_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/umb/v_add_umb',$data);
    }


    public function getBranchInfoUMB()
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
            ->getResultArray();

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));
        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['umb']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate' => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax
        ]);
    }

    public function getNextSuffixUMB()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.umb')
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

    public function initUMBHeader()
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

        $builder = $this->db->table('sc_tmp.umb');
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



    public function saveUMBDetail()
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
        $builderHeader = $db->table('sc_tmp.umb');

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

        $builderDetail = $db->table('sc_tmp.umb_dtl');
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

        $umbHeader = $builderHeader->select('idtax')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()->getRowArray();
        $idtax = $umbHeader['idtax'] ?? '';

        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.umb_dtl');
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

        // Update header UMB
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


    public function updateStatusUMB()
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
        $builder = $db->table('sc_trx.umb');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        // Map status ke action (1 huruf)
        $action = '';
        switch ($status) {
            case 'C': $action = 'C'; break;  // CANCEL
            default:
                $action = 'U';  // default update
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // C / U (1 huruf)
                'I.P',             // kode module dari menuprg
                'I.P.A.5'          // kode menu untuk VOID PP
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update status'
            ]);
        }
    }



    function updateUMB()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_umb_master($param)->getRowArray();
        $status = trim($dtl['status']);


        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/umb'));
        }

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.umb');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addUMB'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/umb'));
        }
    }

    function showing_umbtrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_umb_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_umbtemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby = '$nama'";
        $data = $this->m_purchase->q_umb_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_umb_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_umb_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    function finalEntryUMB() {
        $nama = trim($this->session->get('nama'));
        $docno = trim($this->request->getPost('docno'));

        // Hapus data error sebelumnya
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.5');
        $builder_trxerror->delete();

        if ($docno === '') {
            // Insert error jika docno kosong
            $infotrxerror = [
                'userid' => $nama,
                'errorcode' => 3,
                'modul' => 'I.P.A.5',
            ];
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/purchase/trans/addUMB'));
        }

        // Ambil semua data dari POST
        $cabang = trim($this->request->getPost('cabang'));
        $docdate = trim($this->request->getPost('docdate'));
        $dk = strtoupper($this->request->getPost('dk'));
        $prkarap = strtoupper($this->request->getPost('prkarap'));
        $kdsupplier = strtoupper($this->request->getPost('kdsupplier'));
        $alamatsupplier = strtoupper($this->request->getPost('alamatsupplier'));
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
        $docdateph = !empty($docdate) ? date('Y-m-d', strtotime($docdate)) : null;

        // **CEK APAKAH DATA SUDAH ADA**
        $existingData = $this->db->table('sc_tmp.umb')
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $builder = $this->db->table('sc_tmp.umb');

        if ($existingData) {
            // **UPDATE DATA YANG SUDAH ADA**
            $updateHeader = [
                'docdate' => $docdateph,
                'dk' => $dk,
                'prkarap' => $prkarap,
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
            $paramerror=" and userid='$nama' and modul='I.P.A.5'";
            $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
            $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

            // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

            return redirect()->to(base_url('/purchase/trans/umb'));
        } else {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                // 'nomorakhir1' => $cek->getNumRows(),
                // 'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.5',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/purchase/trans/addUMB'));
        }
    }


    private function cleanNumber($value) {
        if (empty($value)) return 0;
        return str_replace(',', '', $value);
    }

    function show_umb(){
        $module = 'I.P';
        $menu = 'I.P.A.5';
        $table = 'sc_trx.umb';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.umb');
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/po'));
        }
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

        $title = " Report Uang Muka Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_umb/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_umb.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_umb(){
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
        $datamst = $this->m_purchase->q_umb_master($param);
        $datadtl = $this->m_purchase->q_umb_dtl($param);
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








    // =================================== LPB ===========================================


    public function lpb()
    {
        $data['title']="Penerimaan Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.6'; $versirelease='I.P.A.6/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.6'";
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
        $dtl = $this->m_purchase->q_lpb_master_temp($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('purchase/trans/clearEntryLPB');
            $urlnext = base_url('purchase/trans/addLPB');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.6';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/lpb/v_list_lpb',$data);
    }

    function detailLPB()
    {
        /* Penambahan Squence */
        $data['title']="Detail Penerimaan Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/lpb'));
        }
        $kodemenu='I.P.A.6'; $versirelease='I.P.A.6/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.6'";
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
        $data['dtldata'] = $this->m_purchase->q_lpb_master($param)->getRowArray();
        return $this->template->render('purchase/lpb/v_detail_lpb',$data);
    }

    function list_lpb(){
        $list = $this->m_purchase->get_t_front_lpb_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.6';
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

            if ($canUpdate  && trim($lm->inputby) == $nama && $status == "FINAL USER") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updateLPB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Penerimaan Pembelian : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Penerimaan Pembelian 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailLPB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Penerimaan Pembelian : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Penerimaan Pembelian 
                </a>';
            }

            if($canPrint && ($status == "FINAL USER" || $status == "CETAK/PRINT")){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_lpb') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Preview Penerimaan Pembelian : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Preview Penerimaan Pembelian 
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


            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd-m-Y',
            //     strtotime(trim($lm->senddate))
            // );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d-m-Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;

            $row[] = $lm->keterangan;
            $row[] = $lm->nofaktur;
            $row[] = $lm->nosj;

            $row[] = $lm->nmbranch;
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_lpb_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_lpb_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_lpb_apprv(){
        $list = $this->m_purchase->get_t_front_lpb_apprv_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.6';
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
                    href="' . base_url('purchase/trans/updateLPB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This LPB : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update LPB 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailLPB') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail LPB : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail LPB 
                </a>';
            }

            if($canPrint && $status != "REVISION/EDITING"){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_lpb') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print LPB : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print LPB 
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
                'd-m-Y',
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
                'd-m-Y',
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
            "recordsTotal" => $this->m_purchase->t_front_lpb_apprv_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_lpb_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryLPB()
    {
        $nama = trim($this->session->get('nama'));

        $param = " AND COALESCE(inputby,'') = '$nama'";

        $dtl = $this->m_purchase->q_lpb_master_temp($param);

        // =====================================================
        // JIKA TIDAK ADA DATA / ROW LANGSUNG KEMBALI KE LPB
        // =====================================================
        if (!$dtl || $dtl->getNumRows() == 0) {
            return redirect()->to(base_url('purchase/trans/lpb'));
        }

        $row = $dtl->getRowArray();

        // JIKA DOCNO ATAU DATA TIDAK ADA
        if (empty($row) || empty(trim($row['docno'] ?? ''))) {
            return redirect()->to(base_url('purchase/trans/lpb'));
        }

        $status = trim($row['status'] ?? '');

        $builder = $this->db->table('sc_tmp.lpb');
        $builder_dtl = $this->db->table('sc_tmp.lpb_dtl');

        if ($status === 'I') {

            $builder
                ->where('inputby', $nama)
                ->delete();

            $builder_dtl
                ->where('inputby', $nama)
                ->delete();

            return redirect()->to(base_url('purchase/trans/lpb'));

        } elseif ($status === 'E') {

            $builder->where('inputby', $nama);

            if ($builder->update([
                'status' => 'C'
            ])) {

                return redirect()->to(base_url('purchase/trans/lpb'));

            } else {

                return redirect()->to(base_url('purchase/trans/lpb'));

            }

        }

        return redirect()->to(base_url('purchase/trans/lpb'));
    }

    function addLPB()
    {
        /* Penambahan Squence */
        $data['title']="Input Penerimaan Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.6'; $versirelease='I.P.A.6/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.6'";
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
        $data['mst'] = $this->m_purchase->q_lpb_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/lpb'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_lpb_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/lpb/v_add_lpb',$data);
    }


    public function getBranchInfoLPB()
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
        $prefix = trim($konfigurasiUmum[0]['lpb']) ?? '';
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

    public function getNextSuffixLPB()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.lpb')
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

    public function initLPBHeader()
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

        $builder = $this->db->table('sc_tmp.lpb');
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
            'status'     => 'E',
            'inputby'    => $nama,
            'inputdate'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'reload'  => true   // ⬅ PENTING
        ]);
    }



    public function saveLPBDetail()
    {
        $nama    = trim($this->session->get('nama'));
        $docno   = strtoupper(trim($this->request->getPost('docno') ?? ''));
        $docnopo = strtoupper(trim($this->request->getPost('docnopo') ?? ''));
        $idurut  = $this->request->getPost('idurut');

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $modeEdit = !empty($idurut);

        $poData = null;
        if (!$modeEdit) {
            $poData = $db->table('sc_trx.po')
                ->select('currcode, kurs, idtax, isinclusive')
                ->where('docno', $docnopo)
                ->get()
                ->getRowArray();

            if (!$poData) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Data PO dengan nomor {$docnopo} tidak ditemukan"
                ]);
            }
        }

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.lpb');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        if ($exists == 0) {
            $currcode    = $poData['currcode']   ?? '';
            $kurs        = $poData['kurs']       ?? 0;
            $idtax       = $poData['idtax']      ?? '';
            $isinclusive = strtoupper(trim($poData['isinclusive'] ?? 'NO'));
            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $db->table('sc_tmp.lpb')->insert([
                'docno'          => $docno,
                'cabang'         => $this->request->getPost('cabang'),
                'docdate'        => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                'jthtempo'       => $this->request->getPost('jthtempo'),
                'isinclusive'    => $isinclusive,
                'kdsupplier'     => strtoupper($this->request->getPost('kdsupplier')),
                'nmsupplier'     => strtoupper($this->request->getPost('nmsupplier')),
                'alamatsupplier' => strtoupper($this->request->getPost('alamatsupplier')),
                'idtax'          => strtoupper($idtax),
                'biayavol'       => $this->request->getPost('biayavol')  ?? 0,
                'biayavol2'      => $this->request->getPost('biayavol2') ?? 0,
                'nosj'           => strtoupper($this->request->getPost('nosj')),
                'nofaktur'       => strtoupper($this->request->getPost('nofaktur')),
                'currcode'       => strtoupper($currcode),
                'kurs'           => $kurs ?: 1,
                'keterangan'     => strtoupper($this->request->getPost('keterangan')),
                'pemohon'        => strtoupper($this->request->getPost('pemohon') ?? $nama),
                'status'         => 'E',
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $insertCount = 0;
        $message     = '';

        // =====================================================
        // MODE EDIT
        // =====================================================
        if ($modeEdit) {

            $idurutEdit = (int) $idurut;

            if ($idurutEdit <= 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID detail (idurut) tidak valid'
                ]);
            }

            // =====================================================
            // AMBIL BARIS DETAIL YANG AKAN DIUBAH
            // Hanya baris milik dokumen ini & milik user login
            // =====================================================
            $detailRow = $db->table('sc_tmp.lpb_dtl')
                ->select('uniqueid')
                ->where('idurut', $idurutEdit)
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if (!$detailRow) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data detail tidak ditemukan pada dokumen ' . $docno
                ]);
            }

            // 🔧 FIX: uniqueid diambil dari database (di-TRIM),
            //         tidak pernah ditempel apa adanya ke dalam string SQL
            $uniqueid = trim((string) ($detailRow['uniqueid'] ?? ''));

            if ($uniqueid === '') {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Uniqueid PO pada detail ini kosong, hapus lalu input ulang detail'
                ]);
            }

            // ---- Input post
            $qty       = (float) ($this->request->getPost('qty')       ?? 0);
            $harga     = (float) ($this->request->getPost('harga')     ?? 0);
            $qtybonus  = (float) ($this->request->getPost('qtybonus')  ?? 0);
            $multidisc = (float) ($this->request->getPost('multidisc') ?? 0);
            $volitem   = (float) ($this->request->getPost('volitem')   ?? 0);
            $biaya     = (float) ($this->request->getPost('biaya')     ?? 0);
            $biaya2    = (float) ($this->request->getPost('biaya2')    ?? 0);

            $descriptionpo = strtoupper($this->request->getPost('descriptionpo') ?? '');
            $idprincipal   = strtoupper(trim($this->request->getPost('idprincipal') ?? ''));
            $idgudang      = strtoupper($this->request->getPost('idgudang') ?? '');

            // 🔧 FIX: input batch (sebelumnya idspec)
            $batchPost = strtoupper(trim($this->request->getPost('batch') ?? ''));

            // 🔧 nilai = (qty * harga) - diskon
            $nilaiAwal = $qty * $harga;
            $diskon    = $nilaiAwal * $multidisc / 100;
            $nilai     = $nilaiAwal - $diskon;

            // =====================================================
            // VALIDASI QTY TERHADAP SISA QTY PO
            // 🔧 FIX: uniqueid dikirim sebagai bind (?), bukan string
            //         yang ditempel ke SQL (penyebab error PostgreSQL
            //         "trailing junk after numeric literal")
            // =====================================================
            $poDetail = $db->query("
            SELECT
                qty,
                COALESCE(qtylpb, 0)  AS qtylpb,
                COALESCE(qtyvoid, 0) AS qtyvoid,
                COALESCE(batch, '')  AS batch_po,
                (qty - COALESCE(qtylpb, 0) - COALESCE(qtyvoid, 0)) AS sisa_qty
            FROM sc_trx.po_dtl
            WHERE TRIM(uniqueid) = ?
        ", [$uniqueid])->getRow();

            if (!$poDetail) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PO tidak ditemukan'
                ]);
            }

            if ($qty > $poDetail->sisa_qty) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Qty Penerimaan (' . $qty . ') melebihi sisa qty PO yang bisa diterima (' . $poDetail->sisa_qty . ')'
                ]);
            }

            // ---- Ambil header tmp
            $h = $db->table('sc_tmp.lpb')
                ->select('kurs, idtax, currcode')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            $kurs     = (float) ($h['kurs']     ?? 1);
            $idtax    = $h['idtax']    ?? 'NON';
            $currcode = $h['currcode'] ?? 'IDR';

            if ($kurs <= 0) $kurs = 1;

            $nilaikonversi = $nilai * $kurs;

            // 🔧 nilaipajak untuk NON = 0
            $nilaipajak = 0;
            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                $taxDetails = $db->table('sc_mst.tax_dtl')
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
                $nilaipajak  = $nilai * $totalPersen / 100;
            }

            // 🔧 FIX: batch langsung dari POST; kalau kosong fallback ke batch PO
            $batchFinal = $batchPost !== '' ? $batchPost : ($poDetail->batch_po ?? '');

            // =====================================================
            // UPDATE DETAIL — hanya 1 baris pada dokumen ini
            // (idurut = primary key, docno & inputby sebagai pengunci)
            // Tidak ada tabel lain yang diubah di mode edit
            // =====================================================
            $db->table('sc_tmp.lpb_dtl')
                ->where('idurut', $idurutEdit)
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->update([
                    'qty'           => $qty,
                    'qtybonus'      => $qtybonus,
                    'harga'         => $harga,
                    'multidisc'     => $multidisc,
                    'nilai'         => $nilai,
                    'nilaikonversi' => $nilaikonversi,
                    'nilaipajak'    => $nilaipajak,
                    'idtax'         => $idtax,
                    'kurs'          => $kurs,
                    'currcode'      => $currcode,
                    'volitem'       => $volitem,
                    'biaya'         => $biaya,
                    'biaya2'        => $biaya2,
                    'idprincipal'   => $idprincipal,
                    'idgudang'      => $idgudang,
                    'batch'         => $batchFinal,   // 🔧 batch, bukan idspec
                    'descriptionpo' => $descriptionpo,
                    'updateby'      => $nama,
                    'updatedate'    => date('Y-m-d H:i:s')
                ]);

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Detail gagal diupdate (data sudah berubah), silakan reload halaman'
                ]);
            }

            $message = 'Data berhasil diupdate';

        } else {

            // =====================================================
            // MODE ADD
            // =====================================================
            if (empty($docnopo)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Docno PO kosong'
                ]);
            }

            $sql = "
    SELECT
        idbarang, uniqueid, uniqueidpo, nmbarang, capexno, unit,
        qty, qtylpb, qtyvoid, harga, idtax, currcode, kurs,
        descriptionpp, descriptionpo, batch, etd, etdnotes
    FROM sc_trx.po_dtl
    WHERE TRIM(docno) = ?
";

            $query = $db->query($sql, [$docnopo]);

// ======================================
// VALIDASI QUERY PO DETAIL
// ======================================
            if ($query === false) {

                $error = $db->error();

                log_message('error', 'SAVE LPB DETAIL - QUERY PO FAILED');
                log_message('error', 'DOCNO PO: ' . $docnopo);
                log_message('error', 'SQL ERROR: ' . json_encode($error));
                log_message('error', 'LAST QUERY: ' . $db->getLastQuery());

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membaca detail PO: '
                        . ($error['message'] ?? 'Database query error')
                ]);
            }

            $poDetails = $query->getResult();

            if (empty($poDetails)) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'PO tidak ditemukan atau tidak memiliki detail'
                ]);
            }

            $headerTmp = $db->table('sc_tmp.lpb')
                ->select('idtax, currcode, kurs')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            $idtaxHeader    = $headerTmp['idtax']    ?? ($poData['idtax']    ?? 'NON');
            $currcodeHeader = $headerTmp['currcode'] ?? ($poData['currcode'] ?? 'IDR');
            $kursHeader     = (float) ($headerTmp['kurs'] ?? ($poData['kurs'] ?? 1));
            if ($kursHeader <= 0) $kursHeader = 1;

            // 🔧 FIX: input batch dari POST (bukan idspec)
            $batchPost = strtoupper(trim($this->request->getPost('batch') ?? ''));

            foreach ($poDetails as $row) {

                $sisaQty = (float) $row->qty
                    - (float) ($row->qtylpb  ?? 0)
                    - (float) ($row->qtyvoid ?? 0);

                if ($sisaQty <= 0) {
                    continue;
                }

                $duplicate = $db->table('sc_tmp.lpb_dtl')
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate > 0) {
                    continue;
                }

                $barang = $db->query("
                SELECT deflocation FROM sc_mst.mbarang
                WHERE TRIM(idbarang) = ? LIMIT 1
            ", [trim($row->idbarang)])->getRow();

                $harga      = (float) ($row->harga ?? 0);
                $nilai      = $sisaQty * $harga;

                $kursRow    = (float) ($row->kurs ?? $kursHeader);
                if ($kursRow <= 0) $kursRow = 1;

                $nilaikonversi = $nilai * $kursRow;

                $idtaxRow = trim($row->idtax ?? '') ?: $idtaxHeader;
                $nilaipajak = 0;
                if (!empty($idtaxRow) && trim($idtaxRow) !== 'NON' && $nilai > 0) {
                    $taxDetails = $db->table('sc_mst.tax_dtl')
                        ->select('percentation')
                        ->where('idtax', $idtaxRow)
                        ->get()
                        ->getResultArray();

                    $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
                    $nilaipajak  = $nilai * $totalPersen / 100;
                }

                // 🔧 FIX: batch dari POST kalau diisi, kalau tidak pakai batch PO
                $batchFinal = $batchPost !== ''
                    ? $batchPost
                    : ($row->batch ?? '');

                $db->table('sc_tmp.lpb_dtl')->insert([
                    'docno'         => $docno,
                    'docnopo'       => $docnopo,
                    'idbarang'      => $row->idbarang,
                    'uniqueid'      => $row->uniqueid,
                    'uniqueidpo'    => $row->uniqueidpo,
                    'nmbarang'      => $row->nmbarang,
                    'capexno'       => $row->capexno,
                    'idgudang'      => $barang->deflocation ?? '',
                    'unit'          => $row->unit,
                    'qty'           => $sisaQty,
                    'harga'         => $harga,
                    'nilai'         => $nilai,
                    'nilaipajak'    => $nilaipajak,
                    'nilaikonversi' => $nilaikonversi,
                    'currcode'      => $row->currcode   ?? $currcodeHeader,
                    'kurs'          => $kursRow,
                    'idtax'         => $idtaxRow,
                    'descriptionpp' => $row->descriptionpp,
                    'descriptionpo' => $row->descriptionpo,
                    'idprincipal'   => strtoupper(trim($this->request->getPost('idprincipal') ?? '')),
                    'batch'         => $batchFinal,
                    'etd'           => $row->etd      ?? null,
                    'etdnotes'      => $row->etdnotes ?? '',
                    'inputby'       => $nama,
                    'inputdate'     => date('Y-m-d H:i:s')
                ]);

                $insertCount++;
            }

            $message = $insertCount > 0
                ? "$insertCount item berhasil ditambahkan"
                : "Semua item sudah ada sebelumnya";
        }

        // =====================================================
        // HITUNG TOTAL HEADER
        // =====================================================
        $lpbHeader = $builderHeader
            ->select('idtax')
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        $idtax = $lpbHeader['idtax'] ?? '';

        $totalDpp = $db->table('sc_tmp.lpb_dtl')
            ->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        $dpp = (float) ($totalDpp['total_dpp'] ?? 0);

        $jumlahPajak = 0;
        if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {
            $taxDetails = $db->table('sc_mst.tax_dtl')
                ->select('percentation')
                ->where('idtax', $idtax)
                ->get()
                ->getResultArray();

            foreach ($taxDetails as $tax) {
                $persentase   = (float) ($tax['percentation'] ?? 0);
                $jumlahPajak += $dpp * ($persentase / 100);
            }
        }

        $total = $dpp + $jumlahPajak;

        $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->update([
                'dpp'         => number_format($dpp, 2, '.', ''),
                'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                'total'       => number_format($total, 2, '.', ''),
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusLPB()
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
        $builder = $db->table('sc_trx.lpb');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        // Map status ke action (1 huruf)
        $action = '';
        switch ($status) {
            case 'C': $action = 'C'; break;  // CANCEL
            default:
                $action = 'U';  // default update
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // C / U (1 huruf)
                'I.P',             // kode module dari menuprg
                'I.P.A.6'          // kode menu untuk VOID PP
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update status'
            ]);
        }
    }



    function updateLPB()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_lpb_master($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/lpb'));
        }

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.lpb');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addLPB'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/lpb'));
        }
    }

    function showing_lpbtrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_lpb_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_lpbtemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby = '$nama'";
        $data = $this->m_purchase->q_lpb_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_lpb_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_lpb_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_lpb_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.lpb_dtl')
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

    public function delete_document_lpb()
    {
        try {
            // =====================================================
            // USER LOGIN
            // =====================================================
            $nama = trim($this->session->get('nama') ?? '');

            // =====================================================
            // DOCNO
            // =====================================================
            $docno = trim($this->request->getPost('docno') ?? '');


            // =====================================================
            // VALIDASI DOCNO
            // =====================================================
            if ($docno === '') {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Document Number LPB tidak ditemukan.'
                ]);

            }


            // =====================================================
            // VALIDASI USER
            // =====================================================
            if ($nama === '') {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User login tidak ditemukan.'
                ]);

            }


            // =====================================================
            // PANGGIL POSTGRESQL FUNCTION
            // SELECT sc_trx.sp_delete_lpb('DOCNO', 'USERNAME');
            // =====================================================
            $query = $this->db->query(
                "SELECT sc_trx.sp_delete_lpb(?, ?) AS result",
                [
                    $docno,
                    $nama
                ]
            );


            $result = $query->getRowArray();


            // =====================================================
            // VALIDASI RESULT DATABASE
            // =====================================================
            if (!$result || empty($result['result'])) {

                throw new \Exception(
                    'Tidak ada response dari proses penghapusan LPB.'
                );

            }


            // =====================================================
            // POSTGRESQL JSONB RESULT
            // =====================================================
            $response = json_decode(
                $result['result'],
                true
            );


            if (!$response) {

                throw new \Exception(
                    'Response database tidak valid.'
                );

            }


            // =====================================================
            // JIKA FUNCTION MENGEMBALIKAN GAGAL
            // =====================================================
            if (
                !isset($response['success'])
                || $response['success'] !== true
            ) {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $response['message']
                        ?? 'Gagal menghapus LPB.'
                ]);

            }


            // =====================================================
            // SUCCESS
            // =====================================================
            return $this->response->setJSON([
                'success'      => true,
                'message'      => $response['message']
                    ?? 'LPB berhasil dihapus.',
                'docno'        => $response['docno']
                    ?? $docno,
                'total_detail' => $response['total_detail']
                    ?? 0
            ]);


        } catch (\Throwable $e) {

            // =====================================================
            // ERROR / EXCEPTION
            // =====================================================
            log_message(
                'error',
                'DELETE LPB ERROR [' . $docno . '] : ' . $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' => 'Gagal menghapus LPB: '
                        . $e->getMessage()

                ]);

        }

    }

    public function delete_lpb_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.lpb_dtl');
        $nama    = trim($this->session->get('nama'));

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

            // ======================================
            // AMBIL DOCNO DARI DETAIL
            // ======================================
            $rows = $builder
                ->select('docno')
                ->whereIn('idurut', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // karena Anda yakin semua docno sama
            $docno = $rows[0]['docno'];

            // OPTIONAL VALIDATION (lebih aman)
            foreach ($rows as $r) {
                if ($r['docno'] !== $docno) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Docno tidak konsisten'
                    ]);
                }
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
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

            // ======================================
            // RECALCULATE HEADER
            // ======================================
            $builderHeader = $db->table('sc_tmp.lpb');

            $lpbHeader = $builderHeader
                ->select('idtax')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $idtax = $lpbHeader['idtax'] ?? '';

            $builderTotalDpp = $db->table('sc_tmp.lpb_dtl');
            $totalDpp = $builderTotalDpp
                ->select('COALESCE(SUM(nilai), 0) as total_dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $dpp = $totalDpp['total_dpp'] ?? 0;

            $jumlahPajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {

                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $jumlahPajak += $dpp * ($persentase / 100);
                }
            }

            $total = $dpp + $jumlahPajak;

            $builderHeader
                ->where('docno', $docno)
                ->update([
                    'dpp'         => number_format($dpp, 2, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                    'total'       => number_format($total, 2, '.', ''),
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

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

    function list_tmp_lpb_dtl(){
        $docno = trim($this->request->getPost('docno'));
        $list = $this->m_purchase->get_t_lpb_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->capexno;
//            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;

            // 🔧 batch (sebelumnya idspec)
            $row[] = $lm->batch;

            // 🔧 ETD & ETD Notes
            $row[] = !empty($lm->etd) ? date('d-m-Y', strtotime($lm->etd)) : '';
            $row[] = $lm->etdnotes;

            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 2, '.', ',') . '% </div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->descriptionpo;
            $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_lpb_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_lpb_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_lpb_dtl(){
        $docno = trim($this->request->getPost('docno'));
        $list = $this->m_purchase->get_t_lpb_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->capexno;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;

            // 🔧 batch (sebelumnya idspec)
            $row[] = $lm->batch;

            // 🔧 ETD & ETD Notes
            $row[] = !empty($lm->etd) ? date('d-m-Y', strtotime($lm->etd)) : '';
            $row[] = $lm->etdnotes;

            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 2, '.', ',') . '</div>';
//            $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->descriptionpo;
            $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_lpb_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_lpb_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryLPB(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR COALESCE(descriptionpo, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_purchase->q_lpb_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_purchase->q_lpb_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_lpb_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.lpb');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.6');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.6',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/purchase/trans/addLPB'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            // $jthtempo   = trim($this->request->getPost('jthtempo'));
            // $kdsupplier   = trim($this->request->getPost('kdsupplier'));
            // $alamatsupplier   = trim($this->request->getPost('alamatsupplier'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            // $currcode   = trim($this->request->getPost('currcode'));
            $nosj   = trim($this->request->getPost('nosj'));
            $nofaktur   = trim($this->request->getPost('nofaktur'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            // $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            // $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';



            // **BERSIHKAN FORMAT KURS**
            // $kurs = trim($this->request->getPost('kurs'));
            // $kurs_clean = 0;
            // if (!empty($kurs)) {
            //     $kurs_clean = str_replace(',', '', $kurs);
            //     // $kurs_clean = str_replace('.', '.', $kurs_clean);
            //     // $kurs_clean = floatval($kurs_clean);
            // }

            // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime($docdate));
            }

            // $senddateph = null;
            // if (!empty($senddate)) {
            //     $senddateph = date('Y-m-d', strtotime(str_replace('-', '/', $senddate)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                // 'senddate'       => $senddateph,
                // 'jthtempo'       => $jthtempo,
                // 'kdsupplier'     => strtoupper($kdsupplier),
                // 'alamatsupplier' => strtoupper($alamatsupplier),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'nofaktur'     => strtoupper($nofaktur),
                'nosj'     => strtoupper($nosj),
                'keterangan'     => strtoupper($keterangan),
                // 'currcode'       => $currcode,
                // 'kurs'           => $kurs_clean,
                // 'isinclusive'    => strtoupper($isinclusive),
                // 'idtax'          => strtoupper($idtax),
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.P.A.6'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/purchase/trans/lpb'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.P.A.6',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/purchase/trans/addLPB'));
            }



        }

    }


    function show_lpb(){
        $module = 'I.P';
        $menu = 'I.P.A.6';
        $table = 'sc_trx.lpb';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.lpb');
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/lpb'));
        }

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

        $title = " Report Penerimaan Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_lpb/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_lpb.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_lpb(){
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
        $datamst = $this->m_purchase->q_lpb_master($param);
        $datadtl = $this->m_purchase->q_lpb_dtl($param);
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



            // 🔹 Ambil nmsupplier berdasarkan currcode
            $currcode = trim($detail->currcode);

            $curr = $this->db->query("
                SELECT currname 
                FROM sc_mst.currency 
                WHERE TRIM(currcode) = ?
                LIMIT 1
            ", [$currcode])->getRow();

            // 🔹 Set ke object detail
            $detail->currname = $curr->currname ?? '';


            $jthtempo = !empty($detail->jthtempo) ? (int) $detail->jthtempo : 0;

            if (!empty(trim($detail->docdate)) && $jthtempo > 0) {
                $date = new \DateTime(trim($detail->docdate));
                $date->modify("+{$jthtempo} days");
                $jatuhTempo = $date->format('Y-m-d'); // Format database: 2026-04-13
            } else if (!empty(trim($detail->docdate))) {
                $jatuhTempo = trim($detail->docdate); // Langsung pakai docdate asli
            } else {
                $jatuhTempo = '';
            }

            $detail->jatuhtempodate = $jatuhTempo;
            $detail->namauser = $nama;


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






    // =================================== RETUR ===========================================


    public function returbeli()
    {
        $data['title']="Retur Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.7'; $versirelease='I.P.A.7/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.7'";
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
        $dtl = $this->m_purchase->q_returbeli_master_temp($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('purchase/trans/clearEntryReturBeli');
            $urlnext = base_url('purchase/trans/addReturBeli');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.P.A.7';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/returbeli/v_list_returbeli',$data);
    }

    function detailReturBeli()
    {
        /* Penambahan Squence */
        $data['title']="Detail Retur Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('purchase/trans/returbeli'));
        }
        $kodemenu='I.P.A.7'; $versirelease='I.P.A.7/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.P.A.7'";
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
        $data['dtldata'] = $this->m_purchase->q_returbeli_master($param)->getRowArray();
        return $this->template->render('purchase/returbeli/v_detail_returbeli',$data);
    }

    function list_returbeli(){
        $list = $this->m_purchase->get_t_front_returbeli_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.7';
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

            if ($canUpdate && trim($lm->inputby) == $nama && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('purchase/trans/updateReturBeli') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Retur Pembelian : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Retur Pembelian 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailReturBeli') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Retur Pembelian : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Retur Pembelian 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Retur Pembelian : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Retur Pembelian 
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


            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd-m-Y',
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

            $row[] = $lm->keterangan;
            // $row[] = $lm->nofaktur;
            // $row[] = $lm->nosj;

            $row[] = $lm->nmbranch;
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_front_returbeli_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_returbeli_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_returbeli_apprv(){
        $list = $this->m_purchase->get_t_front_returbeli_apprv_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.P.A.7';
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
                    href="' . base_url('purchase/trans/updateReturBeli') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This ReturBeli : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update ReturBeli 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('purchase/trans/detailReturBeli') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail ReturBeli : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail ReturBeli 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('purchase/trans/show_returbeli') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print ReturBeli : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print ReturBeli 
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
                'd-m-Y',
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
                'd-m-Y',
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
            "recordsTotal" => $this->m_purchase->t_front_returbeli_apprv_view_count_all(),
            "recordsFiltered" => $this->m_purchase->t_front_returbeli_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryReturBeli()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_purchase->q_returbeli_master_temp($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('purchase/trans/returbeli'));
        }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.returbeli');
        $builder_dtl = $this->db->table('sc_tmp.returbeli_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('purchase/trans/returbeli'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('purchase/trans/returbeli'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('purchase/trans/returbeli'));
        }

    }

    function addReturBeli()
    {
        /* Penambahan Squence */
        $data['title']="Input Retur Pembelian";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.P.A.7'; $versirelease='I.P.A.7/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.P.A.7'";
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
        $data['mst'] = $this->m_purchase->q_returbeli_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/returbeli'));
        }
        // =================================


        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_returbeli_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('purchase/returbeli/v_add_returbeli',$data);
    }


    public function getBranchInfoReturBeli()
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
        $prefix = trim($konfigurasiUmum[0]['returbeli']) ?? '';
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

    public function getNextSuffixReturBeli()
    {
        $prefix      = ($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $prefix = str_pad(trim($prefix), 3, ' ');

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.returbeli')
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

    public function initReturBeliHeader()
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

        $builder = $this->db->table('sc_tmp.returbeli');
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



    public function saveReturBeliDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $docnolpb = strtoupper(trim($this->request->getPost('docnolpb')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya

        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno || !$docnolpb) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno atau Docno LPB tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderLPB = $db->table('sc_trx.lpb');
        $lpbData = $builderLPB
            ->select('currcode, kurs, idtax, isinclusive')
            ->where('docno', $docnolpb)
            ->get()
            ->getRowArray();

        // Jika data PO tidak ditemukan, beri response error
        if (!$lpbData) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => "Data PO dengan nomor {$docnopo} tidak ditemukan"
            ]);
        }


        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.returbeli');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST

        if ($exists == 0) {
            $currcode   = trim($lpbData['currcode']) ?? '';
            $kurs       = trim($lpbData['kurs']) ?? 0;
            $idtax      = trim($lpbData['idtax']) ?? '';
            $isinclusive = strtoupper(trim($poData['isinclusive'] ?? 'NO'));
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
                'idtax'         => strtoupper($idtax),                        // ← Dari PO
                // 'biayavol'    => ($this->request->getPost('biayavol')),
                // 'biayavol2'    => ($this->request->getPost('biayavol2')),
                // 'nosj'    => strtoupper($this->request->getPost('nosj')),
                // 'nofaktur'    => strtoupper($this->request->getPost('nofaktur')),
                'currcode'      => strtoupper($currcode),                     // ← Dari PO
                'kurs'          => $kurs ?? 1,                                     // ← Dari PO
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'complain'    => strtoupper($this->request->getPost('complain')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.returbeli_dtl');
        $insertCount = 0;
        $message = '';

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid'); // HAPUS strtoupper, biarkan apa adanya
            // =====================================================
            // MODE EDIT - UPDATE DATA
            // =====================================================
            $qty         = $this->request->getPost('qty');
            // $qtybonus    = $this->request->getPost('qtybonus') ?: 0;
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            // $volitem   = $this->request->getPost('volitem') ?: 0;
            // $biaya   = $this->request->getPost('biaya') ?: 0;
            // $biaya2   = $this->request->getPost('biaya2') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $descriptionpo = strtoupper($this->request->getPost('descriptionpo'));
            // $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang = strtoupper($this->request->getPost('idgudang'));
            $idspec = strtoupper($this->request->getPost('idspec'));

            // =====================================================
            // VALIDASI QTY TERHADAP SISA QTY PO (untuk LPB)
            // =====================================================
            $lpbDetail = $db->query("
                SELECT
                    qty,
                    COALESCE(qtyretur, 0)  AS qtyretur,
                    (qty - COALESCE(qtyretur, 0)) AS sisa_qty
                FROM sc_trx.lpb_dtl
                WHERE TRIM(uniqueid) = ?
            ", [$uniqueid])->getRow();

            if (!$lpbDetail) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PO tidak ditemukan'
                ]);
            }

            if ($qty > $lpbDetail->sisa_qty) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Qty Retur (' . $qty . ') melebihi sisa qty Penerimaan yang bisa diretur (' . $lpbDetail->sisa_qty . ')'
                ]);
            }

            $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)->update([
                    'qty'          => $qty,
                    // 'qtybonus'     => $qtybonus,
                    'harga'        => $harga,
                    'multidisc'    => $multidisc,
                    'nilai'        => $nilai,
                    // 'volitem'      => $volitem,

                    // 'biaya'      => $biaya,
                    // 'biaya2'      => $biaya2,
                    // 'idprincipal'      => $idprincipal,
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
            $lpbDetails = $db->query("
                SELECT 
                    docno,
                    idbarang,
                    uniqueid,
                    nmbarang,
                    capexno,
                    unit,
                    qty,
                    qtyretur,
                    idgudang,
                    idspec,
                    multidisc,
                    harga,
                    nilai,
                    descriptionpo,
                    descriptionpp
                FROM sc_trx.lpb_dtl
                WHERE TRIM(docno) = ?
            ", [$docnolpb])->getResult();

            if (empty($lpbDetails)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data PO tidak ditemukan'
                ]);
            }

            foreach ($lpbDetails as $row) {
                // CEK APAKAH ITEM SUDAH ADA DI TMP
                // $duplicate = $builderDetail
                //     ->where('docno', $docno)
                //     ->where('docnopp', $docnopp)
                //     ->where('idbarang', $row->idbarang)
                //     ->where('inputby', $nama)
                //     ->countAllResults();

                $qtyReturSaatIni = 0;

                $existingRetur = $db->query("
                    SELECT qty 
                    FROM sc_tmp.returbeli_dtl 
                    WHERE uniqueid = ?
                ", [$row->uniqueid])->getRow();

                if ($existingRetur) {
                    $qtyReturSaatIni = $existingRetur->qty;
                }

                $sisaQty = $row->qty - ($row->qtyretur - $qtyReturSaatIni);

                $duplicate = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnolpb'       => $docnolpb,
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'capexno'      => $row->capexno,
                        'unit'          => $row->unit,
                        'qty'           => $row->qty,
                        'idgudang'           => $row->idgudang,
                        'idspec'           => $row->idspec,
                        // 'qtybonus'      => 0, // Default 0 untuk new insert
                        'harga'         => $row->harga, // Default 0 untuk new insert
                        'multidisc'     => 0, // Default 0 untuk new insert
                        // 'volitem'     => 0, // Default 0 untuk new insert
                        // 'biaya'     => 0, // Default 0 untuk new insert
                        // 'biaya2'     => 0, // Default 0 untuk new insert
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

        $returbeliHeader = $builderHeader->select('idtax')
            ->where('inputby', $nama)
            ->where('docno', $docno)->get()->getRowArray();
        $idtax = $returbeliHeader['idtax'] ?? '';

        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.returbeli_dtl');
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

        // Update header ReturBeli
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


    public function updateStatusReturBeli()
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
        $builder = $db->table('sc_trx.returbeli');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        // Map status ke action (1 huruf)
        $action = '';
        switch ($status) {
            case 'C': $action = 'C'; break;  // CANCEL
            default:
                $action = 'U';  // default update
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // C / U (1 huruf)
                'I.P',             // kode module dari menuprg
                'I.P.A.7'          // kode menu untuk VOID PP
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal update status'
            ]);
        }
    }



    function updateReturBeli()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_purchase->q_returbeli_master($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/returbeli'));
        }

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.returbeli');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('purchase/trans/addReturBeli'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('purchase/trans/returbeli'));
        }
    }

    function showing_returbelitrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_returbeli_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_returbelitemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_purchase->q_returbeli_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_returbeli_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_purchase->q_returbeli_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_returbeli_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.returbeli_dtl')
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

    public function delete_returbeli_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.returbeli_dtl');
        $nama    = trim($this->session->get('nama'));

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

            // ======================================
            // AMBIL DOCNO DARI DETAIL
            // ======================================
            $rows = $builder
                ->select('docno')
                ->whereIn('idurut', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // karena Anda yakin semua docno sama
            $docno = $rows[0]['docno'];

            // OPTIONAL VALIDATION (lebih aman)
            foreach ($rows as $r) {
                if ($r['docno'] !== $docno) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Docno tidak konsisten'
                    ]);
                }
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
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

            // ======================================
            // RECALCULATE HEADER
            // ======================================
            $builderHeader = $db->table('sc_tmp.returbeli');

            $returbeliHeader = $builderHeader
                ->select('idtax')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $idtax = $returbeliHeader['idtax'] ?? '';

            $builderTotalDpp = $db->table('sc_tmp.returbeli_dtl');
            $totalDpp = $builderTotalDpp
                ->select('COALESCE(SUM(nilai), 0) as total_dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $dpp = $totalDpp['total_dpp'] ?? 0;

            $jumlahPajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {

                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $jumlahPajak += $dpp * ($persentase / 100);
                }
            }

            $total = $dpp + $jumlahPajak;

            $builderHeader
                ->where('docno', $docno)
                ->update([
                    'dpp'         => number_format($dpp, 2, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                    'total'       => number_format($total, 2, '.', ''),
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

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

    function list_tmp_returbeli_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_returbeli_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnolpb;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->capexno;
            // $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '% </div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            // $row[] = $lm->descriptionpo;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_returbeli_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_returbeli_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_returbeli_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_purchase->get_t_returbeli_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnolpb;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->capexno;
            // $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            // $row[] = $lm->descriptionpo;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;

        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_purchase->t_returbeli_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_purchase->t_returbeli_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryReturBeli(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR COALESCE(descriptionpo, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_purchase->q_returbeli_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_purchase->q_returbeli_dtl_temp($paramdtl);
        $cek2 = $this->m_purchase->q_returbeli_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.returbeli');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.P.A.7');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.P.A.7',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/purchase/trans/addReturBeli'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdsupplier   = trim($this->request->getPost('kdsupplier'));
            $alamatsupplier   = trim($this->request->getPost('alamatsupplier'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $currcode   = trim($this->request->getPost('currcode'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $complain   = trim($this->request->getPost('complain'));
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
                $docdateph = date('Y-m-d', strtotime($docdate));
            }

            // $senddateph = null;
            // if (!empty($senddate)) {
            //     $senddateph = date('Y-m-d', strtotime(str_replace('-', '/', $senddate)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                // 'senddate'       => $senddateph,
                'jthtempo'       => $jthtempo,
                'kdsupplier'     => strtoupper($kdsupplier),
                'alamatsupplier' => strtoupper($alamatsupplier),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                'complain'     => strtoupper($complain),
                'currcode'       => $currcode,
                'kurs'           => $kurs_clean,
                'isinclusive'    => strtoupper($isinclusive),
                'idtax'          => strtoupper($idtax),
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.P.A.7'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/purchase/trans/returbeli'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.P.A.7',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/purchase/trans/addReturBeli'));
            }



        }

    }


    function show_returbeli(){
        $module = 'I.P';
        $menu = 'I.P.A.7';
        $table = 'sc_trx.returbeli';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.returbeli');
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('purchase/trans/lpb'));
        }
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

        $title = " Report Retur Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("purchase/trans/api_returbeli/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_returbeli.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_returbeli(){
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
        $datamst = $this->m_purchase->q_returbeli_master($param);
        $datadtl = $this->m_purchase->q_returbeli_dtl($param);
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

    public function historyHargaPO()
    {
        $idbarang = trim($this->request->getGet('idbarang'));

        if ($idbarang === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Barang tidak boleh kosong',
                'data' => []
            ]);
        }

        try {
            $sql = "
                SELECT
                    TRIM(d.idbarang) AS idbarang,
                    TRIM(d.nmbarang) AS nmbarang,
                    TRIM(h.docno) AS docno,
                    h.docdate,
                    TRIM(h.kdsupplier) AS kdsupplier,
                    TRIM(s.nmsupplier) AS supplier,
                    TRIM(h.currcode) AS currcode,
                    COALESCE(h.kurs, 1) AS kurs,
                    COALESCE(d.qty, 0) AS qty,
                    COALESCE(d.harga, 0) AS harga,
                    COALESCE(d.nilai, 0) AS nilai,
                    TRIM(d.docnopo) AS docnopo
                FROM sc_trx.lpb_dtl d
                INNER JOIN sc_trx.lpb h ON TRIM(h.docno) = TRIM(d.docno)
                LEFT JOIN sc_mst.mstsupplier s ON TRIM(s.kdsupplier) = TRIM(h.kdsupplier)
                WHERE TRIM(d.idbarang) = ?
                    AND COALESCE(d.harga, 0) > 0
                    AND TRIM(COALESCE(h.status, '')) IN ('F', 'P')
                ORDER BY h.docdate DESC, h.docno DESC, d.idurut DESC
                LIMIT 50
            ";

            $rows = $this->db->query($sql, [$idbarang])->getResultArray();
            $data = [];

            foreach ($rows as $row) {
                $data[] = [
                    'idbarang' => trim($row['idbarang'] ?? ''),
                    'nmbarang' => trim($row['nmbarang'] ?? ''),
                    'docno' => trim($row['docno'] ?? ''),
                    'docnopo' => trim($row['docnopo'] ?? ''),
                    'docdate' => !empty($row['docdate']) ? date('d-m-Y', strtotime($row['docdate'])) : '-',
                    'kdsupplier' => trim($row['kdsupplier'] ?? ''),
                    'supplier' => trim($row['supplier'] ?? '-'),
                    'currcode' => trim($row['currcode'] ?? 'IDR'),
                    'kurs' => (float)($row['kurs'] ?? 1),
                    'qty' => (float)($row['qty'] ?? 0),
                    'harga' => (float)($row['harga'] ?? 0),
                    'nilai' => (float)($row['nilai'] ?? 0)
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'History harga penerimaan barang berhasil diambil',
                'data' => $data
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'historyHargaPO ERROR : ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil history harga penerimaan',
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
    }



    public function laporan_jurnal_transaksi_lpb()
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

                        'messages' => 'Doc No LPB tidak ditemukan',

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

            $query = $this->m_purchase
                ->q_laporan_jurnal_transaksi($params);


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
                'laporan_jurnal_transaksi_lpb: '
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
     * Cek total onhand 1 item di 1 branch
     * URL: purchase/trans/see_stock_itemperbranch?idbranch=xxx&idbarang=yyy
     */
    public function see_stock_itemperbranch()
    {
        // =============================
        // AMBIL PARAMETER
        // =============================
        $idbranch = $this->request->getGet('idbranch')
            ?? $this->request->getPost('idbranch');

        $idbarang = $this->request->getGet('idbarang')
            ?? $this->request->getPost('idbarang');

        $batch = $this->request->getGet('batch')
            ?? $this->request->getPost('batch');
        $batch = trim((string) ($batch ?? ''));

        // =============================
        // VALIDASI PARAMETER
        // =============================
        if (empty($idbranch) || empty($idbarang)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter idbranch dan idbarang wajib diisi.',
                'data'    => null
            ]);
        }

        // =============================
        // ESCAPE PARAMETER
        // =============================
        $idbranchEsc = $this->db->escape(trim((string) $idbranch));
        $idbarangEsc = $this->db->escape(trim((string) $idbarang));

        // 🔹 Filter batch — SELALU diterapkan
        // batch kosong  → hanya baris tanpa batch
        // batch terisi  → hanya baris dengan batch tsb
        $batchEsc    = $this->db->escape($batch);
        $batchFilter = " AND TRIM(COALESCE(sg.batch, '')) = TRIM({$batchEsc}) ";

        // =============================
        // QUERY
        // =============================
        $sql = "
        SELECT
            ml.idbranchjob,
            ml.nmbranchjob,
            sg.idbarang,
            COUNT(DISTINCT sg.idlocation) AS total_gudang,
            COUNT(DISTINCT sg.batch)      AS total_batch,
            SUM(COALESCE(sg.onhand, 0))   AS total_onhand
        FROM sc_mst.stkgdw sg
        INNER JOIN sc_mst.mlocation ml
                ON TRIM(ml.idlocation) = TRIM(sg.idlocation)
        WHERE TRIM(ml.idbranchjob) = TRIM({$idbranchEsc})
          AND TRIM(sg.idbarang)    = TRIM({$idbarangEsc})
          {$batchFilter}
        GROUP BY
            ml.idbranchjob,
            ml.nmbranchjob,
            sg.idbarang
    ";

        $query = $this->db->query($sql);

        // =============================
        // GUARD: query gagal
        // =============================
        if ($query === false) {
            log_message('error', 'see_stock_itemperbranch gagal: ' . print_r($this->db->error(), true));
            log_message('error', 'SQL: ' . $sql);
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Query gagal. Hubungi administrator.',
                'data'    => null
            ]);
        }

        $result = $query->getRow();

        // =============================
        // HANDLE JIKA DATA TIDAK ADA
        // =============================
        if (!$result) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Tidak ada stock untuk item ini di branch tersebut.',
                'data'    => [
                    'idbranchjob'  => $idbranch,
                    'nmbranchjob'  => null,
                    'idbarang'     => $idbarang,
                    'batch'        => $batch,
                    'total_gudang' => 0,
                    'total_batch'  => 0,
                    'total_onhand' => 0
                ]
            ]);
        }

        // =============================
        // FORMAT RESPONSE
        // =============================
        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'idbranchjob'  => trim($result->idbranchjob),
                'nmbranchjob'  => trim((string) $result->nmbranchjob),
                'idbarang'     => trim($result->idbarang),
                'batch'        => $batch,
                'total_gudang' => (int)   $result->total_gudang,
                'total_batch'  => (int)   $result->total_batch,
                'total_onhand' => (float) $result->total_onhand
            ]
        ]);
    }

    // ==========================================================
    // VALIDASI INPUT AWAL CABANG / JOB (KHUSUS ADDPP)
    // ==========================================================

    private function ppSessionBranchName()
    {
        return strtoupper(trim((string) $this->session->get('nmbranchjob')));
    }

    private function ppCanChooseAnyBranch()
    {
        // Normalisasi agar variasi penulisan (PT / PT. / spasi ganda) tetap dikenali
        $branchKey = preg_replace(
            '/[^A-Z0-9]/',
            '',
            $this->ppSessionBranchName()
        );

        return $branchKey !== ''
            && strpos($branchKey, 'PTJATIMTAMANSTEEL') === 0;
    }

    private function ppCanonicalBranchName($branchName)
    {
        $key = preg_replace(
            '/[^A-Z0-9]/',
            '',
            strtoupper(trim((string) $branchName))
        );

        $aliases = [
            'PLANI'             => 'PLANTI',
            'PLAN1'             => 'PLANTI',
            'PLANT1'            => 'PLANTI',
            'PLANII'            => 'PLANTII',
            'PLAN2'             => 'PLANTII',
            'PLANT2'            => 'PLANTII',
            'PTJATIMTAMANSTEEL' => 'PTJATIMTAMANSTEELMFG',
        ];

        return $aliases[$key] ?? $key;
    }

    private function ppIsAllowedInitialBranch($branchId)
    {
        $branchId          = trim((string) $branchId);
        $sessionBranchId   = trim((string) $this->session->get('idbranchjob'));
        $sessionBranchName = $this->ppSessionBranchName();

        // Cabang wajib dipilih, termasuk untuk user PT JATIM TAMAN STEEL
        if ($branchId === '') {
            return false;
        }

        // PT JATIM TAMAN STEEL bebas memilih cabang / job
        if ($this->ppCanChooseAnyBranch()) {
            return true;
        }

        // Session lama belum memiliki data cabang -> jangan kunci user
        if ($sessionBranchId === '' && $sessionBranchName === '') {
            return true;
        }

        if ($sessionBranchId !== '' && $branchId === $sessionBranchId) {
            return true;
        }

        // Fallback pencocokan nama (PLAN I / PLAN 1 / PLANT I dianggap sama)
        if ($sessionBranchName !== '') {
            $row = $this->db->table('sc_mst.branchjob')
                ->select('nmbranch')
                ->where('idbranch', $branchId)
                ->get()
                ->getRowArray();

            if (
                $row
                && $this->ppCanonicalBranchName($row['nmbranch'] ?? '')
                    === $this->ppCanonicalBranchName($sessionBranchName)
            ) {
                return true;
            }
        }

        return false;
    }

}