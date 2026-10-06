<?php
/*
 * UNTUK PERKIRAAN COA NDK LEVEL 5
 *
 *
 *
 *
 * */

namespace App\Controllers\Arap;

use App\Controllers\BaseController;

class Arap extends BaseController
{

    public function ndk()
    {
        $data['title']="Nota Debit/Kredit";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.1'; $versirelease='I.L.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.L.A.1'";
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
        $dtl = $this->m_arap->q_ndk_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('arap/transaksi/clearEntryNDK');
            $urlnext = base_url('arap/transaksi/addNDK');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.L.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('arap/v_list_ndk',$data);
    }

    function detailNDK()
    {
        /* Penambahan Squence */
        $data['title']="Detail Nota Debit / Kredit";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('arap/transaksi/ndk'));
        }
        $kodemenu='I.L.A.1'; $versirelease='I.L.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.L.A.1'";
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
        $data['dtldata'] = $this->m_arap->q_ndk_master($param)->getRowArray();
        return $this->template->render('arap/v_detail_ndk',$data);
    }

    function list_ndk(){
        $list = $this->m_arap->get_t_front_ndk_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.L.A.1';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_rendkrt']) && trim($datadtl['dtl_akses']['a_rendkrt']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);
            $status = trim($lm->status);

            $updateBtn = '';
            $detailBtn = '';
            // $printBtn  = '';
            // $approveBtn  = '';
            // $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if (
                $canUpdate &&
                !in_array(strtoupper(trim($status)), ['C', 'CANCELED'])
            ) {
                $updateBtn = '
    <a class="dropdown-item bg-warning" 
        href="' . base_url('arap/transaksi/updateNDK') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
        onclick="return confirm(\'Update This Nota Debit / Kredit : ' . $docno . '\')">
        <i class="fa fa-edit"></i> Update Nota Debit / Kredit 
    </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('arap/transaksi/detailNDK') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Nota Debit / Kredit : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Nota Debit / Kredit 
                </a>';
            }



            $menuContent = '';

            // if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                // if ($canView) {
                //     $menuContent .= $detailBtn;
            //         $menuContent .= $printBtn;
            //     }

            // } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                // if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
            //     if ($canApprove)   $menuContent .= $approveBtn;
            //     if ($canApprove)   $menuContent .= $disapproveBtn;
            // }

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
            
            // $row[] = $lm->kdsupplier;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsplr;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            // $row[] = $lm->docnohp;
            $row[] = $lm->currcode;
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTemndk = $date->format('d-m-Y');

            } else {
                $jatuhTemndk = '';
            }

            $row[] = $jatuhTemndk;
            $row[] = $lm->nmsalesman;
            $row[] = $lm->dk;
            // $row[] = $lm->nilai;
            $row[] = '<div class="ratakanan">'. number_format($lm->total, 2, '.', ',') . '</div>';
            $row[] = $lm->keterangan;
            $row[] = $lm->nmbranch;
            // =========================
// STATUS
// =========================
            // =========================
// STATUS
// =========================
            $status = strtoupper(trim($lm->status));

            if ($status === 'C') {

                $statusHtml = '
        <div class="text-center">
            <span style="font-size:12px"
                  class="badge badge-danger w-100">
                CANCEL
            </span>
        </div>';

            } elseif ($status === 'F') {

                $statusHtml = '
        <div class="text-center">
            <span style="font-size:12px"
                  class="badge badge-primary w-100">
                FINAL USER
            </span>
        </div>';

            } else {

                $statusHtml = '
        <div class="text-center">
            <span style="font-size:12px"
                  class="badge badge-secondary w-100">
                ' . htmlspecialchars(trim($lm->status ?? '')) . '
            </span>
        </div>';
            }

            $row[] = $statusHtml;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_arap->t_front_ndk_view_count_all(),
            "recordsFiltered" => $this->m_arap->t_front_ndk_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    
    function clearEntryNDK()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_arap->q_ndk_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('arap/transaksi/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.ndk');
        // $builder_dtl = $this->db->table('sc_tmp.ndk_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('arap/transaksi/ndk'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('arap/transaksi/ndk'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('arap/transaksi/ndk'));
        }

    }

    function addNDK()
    {
        /* Penambahan Squence */
        $data['title']="Input Debit / Kredit";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.1'; $versirelease='I.L.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.L.A.1'";
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
        $data['mst'] = $this->m_arap->q_ndk_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));


        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_arap->q_ndk_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('arap/v_add_ndk',$data);
    }


   public function getBranchInfoNDK()
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

    public function getNextSuffixNDK()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.ndk')
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

    public function initNDKHeader()
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

        $builder = $this->db->table('sc_tmp.ndk');
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



    public function saveNDKDetail()
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
        $builderHeader = $db->table('sc_tmp.ndk');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari Nota Debit / KreditST
        
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

        $builderDetail = $db->table('sc_tmp.ndk_dtl');
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
            $descriptionndk = strtoupper($this->request->getPost('descriptionndk'));


            // Ambil kurs dari header Nota Debit / Kredit
            $ndkHeader = $builderHeader->select('kurs, idtax')->where('docno', $docno)->get()->getRowArray();
            $kurs = $ndkHeader['kurs'] ?? 0;
            $idtax = $ndkHeader['idtax'] ?? '';
            
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
                $nilaipajak = $nilai + ($nilai * $totalPersentase / 100);
            } else {
                // Jika NON pajak, nilaipajak sama dengan nilai
                $nilaipajak = $nilai;
            }

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                'qtybonus'     => $qtybonus,
                'harga'        => $harga,
                'multidisc'    => $multidisc,
                'nilai'        => $nilai,
                'nilaikonversi' => $nilaikonversi,  // Tambahkan ini
                'nilaipajak'   => $nilaipajak,      // Tambahkan ini
                'descriptionndk' => $descriptionndk,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);
            // $builderDetail->where('uniqueid', $uniqueid)->update([
            //     'qty'          => $qty,
            //     'qtybonus'     => $qtybonus,
            //     'harga'        => $harga,
            //     'multidisc'    => $multidisc,
            //     'nilai'        => $nilai,
            //     'descriptionndk' => $descriptionndk,
            //     'updateby'     => $nama,
            //     'updatedate'   => date('Y-m-d H:i:s')
            // ]);



            $ndkHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
            $idtax = $ndkHeader['idtax'] ?? '';
            
            // Hitung total DPP (sum nilai dari ndk_dtl)
            $builderTotalDpp = $db->table('sc_tmp.ndk_dtl');
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
            
            // Update header Nota Debit / Kredit
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
                    qtyndk,
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
                // CEK APAKAH ITEM SUDAH ADA DI TMP
                // $duplicate = $builderDetail
                //     ->where('docno', $docno)
                //     ->where('docnopp', $docnopp)
                //     ->where('idbarang', $row->idbarang)
                //     ->where('inputby', $nama)
                //     ->countAllResults();

                $sisaQty = $row->qty - ($row->qtyndk + $row->qtyvoid);
                
                // Jika sisa quantity <= 0, skip item ini
                if ($sisaQty <= 0) {
                    continue; // Lewati item ini
                }

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
                        'qty'           => $sisaQty,
                        'kurs'          => strtoupper($this->request->getPost('kurs')),
                        'idtax'         => strtoupper($this->request->getPost('idtax')),
                        'currcode'      => strtoupper($this->request->getPost('currcode')),
                        'qtybonus'      => 0, // Default 0 untuk new insert
                        'harga'         => 0, // Default 0 untuk new insert
                        'multidisc'     => 0, // Default 0 untuk new insert
                        'nilai'         => 0, // Default 0 untuk new insert
                        'descriptionpp' => $row->description,
                        'descriptionndk' => $row->description,
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


    public function updateStatusNDK()
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
        $builder = $db->table('sc_trx.ndk');
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



    function updateNDK()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_arap->q_ndk_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.ndk');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('arap/transaksi/addNDK'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('arap/transaksi/ndk'));
        }
    }

    function showing_ndktrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_arap->q_ndk_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_ndktemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_arap->q_ndk_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_ndk_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_arap->q_ndk_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_ndk_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.ndk_dtl')
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

    public function delete_ndk_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.ndk_dtl');

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
                'message' => 'Data Nota Debit / Kredit Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_ndk_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari Nota Debit / KreditST
        $list = $this->m_arap->get_t_ndk_dtl_temp_view($docno);
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 2, '.', ',') . '% </div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->descriptionndk;
            $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_arap->t_ndk_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_arap->t_ndk_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_ndk_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari Nota Debit / KreditST
        $list = $this->m_arap->get_t_ndk_dtl_view($docno);
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
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 2, '.', ',') . '</div>';
            $row[] = $lm->descriptionndk;
            $row[] = $lm->descriptionpp;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_arap->t_ndk_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_arap->t_ndk_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    public function finalEntryNDK()
    {
        $nama = trim($this->session->get('nama'));

        // =========================================================
        // AMBIL POST
        // =========================================================
//        $docno = strtoupper(trim(
//            $this->request->getPost('docno')
//        ));

        // =========================================================
// AMBIL POST DOCNO
// DOCNO = PREFIX / INFIX / SUFFIX
// =========================================================

        $prefix = strtoupper(trim(
            $this->request->getPost('prefix')
        ));

        $infix = strtoupper(trim(
            $this->request->getPost('infix')
        ));

        $suffix = strtoupper(trim(
            $this->request->getPost('suffix')
        ));

        $docno = '';

        if ($prefix !== '' && $infix !== '' && $suffix !== '') {

            $docno =
                $prefix . '/' .
                $infix . '/' .
                $suffix;

        } else {
            $builderTrxError = $this->db->table('sc_mst.trxerror');


            // =========================================================
            // HAPUS ERROR SEBELUMNYA
            // =========================================================
            $builderTrxError
                ->where('userid', $nama)
                ->where('modul', 'I.L.A.1')
                ->delete();
            $builderTrxError->insert([
                'userid'      => $nama,
                'errorcode'   => 3,
                'nomorakhir1' => 0,
                'nomorakhir2' => 0,
                'modul'       => 'I.L.A.1',
            ]);

            return redirect()
                ->to(base_url('/arap/transaksi/addNDK'))
                ->with(
                    'error',
                    'Prefix, Infix, dan Suffix wajib diisi.'
                );
        }

        $cabang = strtoupper(trim(
            $this->request->getPost('cabang')
        ));

        $idtax = strtoupper(trim(
            $this->request->getPost('idtax')
        ));

        $nmsupplier= strtoupper(trim(
            $this->request->getPost('nmsupplier')
        ));

        $isinclusive = $this->request->getPost('isinclusive')
            ? 'YES'
            : 'NO';


        // =========================================================
        // BUILDER
        // =========================================================
        $builderHeader = $this->db->table('sc_tmp.ndk');
        $builderTrxError = $this->db->table('sc_mst.trxerror');


        // =========================================================
        // HAPUS ERROR SEBELUMNYA
        // =========================================================
        $builderTrxError
            ->where('userid', $nama)
            ->where('modul', 'I.L.A.1')
            ->delete();


        // =========================================================
        // VALIDASI DOCNO
        // =========================================================
        if ($docno === '') {

            $builderTrxError->insert([
                'userid'      => $nama,
                'errorcode'   => 3,
                'nomorakhir1' => 0,
                'nomorakhir2' => 0,
                'modul'       => 'I.L.A.1',
            ]);

            return redirect()->to(
                base_url('/arap/transaksi/addNDK')
            );
        }


        // =========================================================
        // AMBIL NILAI
        // =========================================================
        $kurs = trim(
            $this->request->getPost('kurs')
        );

        $dpp = trim(
            $this->request->getPost('dpp')
        );

        $total = trim(
            $this->request->getPost('total')
        );


        // =========================================================
        // NORMALISASI ANGKA
        // =========================================================
        $kursClean = (float) str_replace(
            ',',
            '',
            $kurs ?: '0'
        );

        $dppClean = (float) str_replace(
            ',',
            '',
            $dpp ?: '0'
        );

        $totalClean = (float) str_replace(
            ',',
            '',
            $total ?: '0'
        );


        // =========================================================
        // VALIDASI DPP
        // TIDAK BOLEH 0 / NEGATIF
        // =========================================================
        if ($dppClean <= 0) {

            $builderTrxError->insert([
                'userid'      => $nama,
                'errorcode'   => 3,
                'nomorakhir1' => 0,
                'nomorakhir2' => 0,
                'modul'       => 'I.L.A.1',
            ]);

            return redirect()
                ->to(base_url('/arap/transaksi/addNDK'))
                ->with(
                    'error',
                    'Nilai transaksi harus lebih besar dari 0.'
                );
        }


        // =========================================================
        // KURS
        // IDR = 1
        // =========================================================
        if ($kursClean <= 0) {
            $kursClean = 1;
        }


        // =========================================================
        // TOTAL
        // =========================================================
        if ($totalClean <= 0) {

            $totalClean = $dppClean;
        }


        // =========================================================
        // JUMLAH PAJAK
        // =========================================================
        $jumlahPajak = $totalClean - $dppClean;

        if ($jumlahPajak < 0) {
            $jumlahPajak = 0;
        }


        // =========================================================
        // TANGGAL
        // FORMAT FORM : DD-MM-YYYY
        // =========================================================
        $docdate = trim(
            $this->request->getPost('docdate')
        );

        $docdateph = null;

        if ($docdate !== '') {

            $dateObj = \DateTime::createFromFormat(
                'd-m-Y',
                $docdate
            );

            if ($dateObj !== false) {

                $docdateph = $dateObj->format('Y-m-d');

            } else {

                $builderTrxError->insert([
                    'userid'      => $nama,
                    'errorcode'   => 3,
                    'nomorakhir1' => 0,
                    'nomorakhir2' => 0,
                    'modul'       => 'I.L.A.1',
                ]);

                return redirect()
                    ->to(base_url('/arap/transaksi/addNDK'))
                    ->with(
                        'error',
                        'Format tanggal tidak valid.'
                    );
            }
        }


        // =========================================================
        // DATA HEADER
        // =========================================================
        $data = [

            'docno'          => $docno,

            'cabang'         => $cabang,

            'docdate'        => $docdateph,

            'jthtempo' => (float) str_replace(
                ',',
                '',
                $this->request->getPost('jthtempo') ?: '0'
            ),
            'isinclusive'    => $isinclusive,

            'kdsalesman'     => strtoupper(
                trim(
                    $this->request->getPost('kdsalesman')
                )
            ),

            'kdsupplier'     => strtoupper(
                trim(
                    $this->request->getPost('kdsupplier')
                )
            ),

            'nmsupplier'     => strtoupper(
                trim(
                    $this->request->getPost('nmsupplier')
                )
            ),

            'alamatsupplier' => strtoupper(
                trim(
                    $this->request->getPost('alamatsupplier')
                )
            ),

            'idtax'          => $idtax,

            'currcode'       => strtoupper(
                trim(
                    $this->request->getPost('currcode')
                )
            ),

            'kurs'           => $kursClean,

            'dpp'            => $dppClean,

            'jumlahpajak'    => $jumlahPajak,

            'total'          => $totalClean,

            'nilai'          => $totalClean * $kursClean,

            'dk'             => strtoupper(
                trim(
                    $this->request->getPost('dk')
                )
            ),

            'perkiraanarap'  => strtoupper(
                trim(
                    $this->request->getPost('perkiraanarap')
                )
            ),

            'perkiraanlawan' => strtoupper(
                trim(
                    $this->request->getPost('perkiraanlawan')
                )
            ),

            'keterangan'     => strtoupper(
                trim(
                    $this->request->getPost('keterangan')
                )
            ),

            'status'         => 'E'
        ];


        // =========================================================
        // CEK DATA HEADER
        // =========================================================
        $cekData = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();


        // =========================================================
        // MULAI TRANSACTION
        // =========================================================
        $this->db->transBegin();


        try {

            // =====================================================
            // UPDATE EXISTING
            // =====================================================
            if ($cekData) {

                $data['updateby'] = $nama;
                $data['updatedate'] = date(
                    'Y-m-d H:i:s'
                );


                $updateHeader = $builderHeader
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update($data);


                if (!$updateHeader) {

                    throw new \Exception(
                        'Gagal update header NDK.'
                    );
                }

            }

            // =====================================================
            // INSERT NEW
            // =====================================================
            else {

                $data['inputby'] = $nama;

                $data['inputdate'] = date(
                    'Y-m-d H:i:s'
                );


                $insertHeader = $builderHeader
                    ->insert($data);


                if (!$insertHeader) {

                    throw new \Exception(
                        'Gagal insert header NDK.'
                    );
                }
            }


            // =====================================================
            // FINAL STATUS
            // =====================================================
            $updateStatus = $builderHeader
                ->where('inputby', $nama)
                ->update([
                    'status' => 'F'
                ]);


            if (!$updateStatus) {

                throw new \Exception(
                    'Gagal mengubah status NDK menjadi F.'
                );
            }


            // =====================================================
            // CEK TRANSACTION
            // =====================================================
            if ($this->db->transStatus() === false) {

                throw new \Exception(
                    'Database transaction gagal.'
                );
            }


            // =====================================================
            // COMMIT
            // =====================================================
            $this->db->transCommit();


            // =====================================================
            // SUCCESS
            // =====================================================
            return redirect()->to(
                base_url('/arap/transaksi/ndk')
            );


        } catch (\Throwable $e) {

            // =====================================================
            // ROLLBACK
            // =====================================================
            $this->db->transRollback();


            // =====================================================
            // LOG ERROR
            // =====================================================
            log_message(
                'error',
                'FINAL ENTRY NDK ERROR: ' .
                $e->getMessage()
            );


            // =====================================================
            // SIMPAN ERROR TRANSAKSI
            // =====================================================
            $builderTrxError->insert([
                'userid'      => $nama,
                'errorcode'   => 3,
                'nomorakhir1' => 0,
                'nomorakhir2' => 0,
                'modul'       => 'I.L.A.1',
            ]);


            return redirect()
                ->to(base_url('/arap/transaksi/addNDK'))
                ->with(
                    'error',
                    'Gagal menyimpan transaksi NDK.'
                );
        }
    }
    
    public function finalEntryNDK_DP()
    {
        $nama = trim($this->session->get('nama'));

        $this->db->transStart();

        /*
        ==========================
        VALIDASI DATA Nota Debit / Kredit
        ==========================
        */

        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' 
                    AND (COALESCE(unit, '') = ''  
                    OR qty = '0.00' 
                    OR qty = '0' 
                    OR COALESCE(nmbarang, '') = '' 
                    OR COALESCE(descriptionndk, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_arap->q_ndk_master_temp($param)->getRowArray();
        $cek = $this->m_arap->q_ndk_dtl_temp($paramdtl);
        $cek2 = $this->m_arap->q_ndk_dtl_temp($paramdtl2);

        if(!$header){
            return redirect()->to(base_url('/arap/transaksi/addNDK'));
        }

        $status = trim($header['status']);

        /*
        ==========================
        TRX ERROR CLEAN
        ==========================
        */

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama)
                        ->where('modul','I.L.A.1')
                        ->delete();

        if (($status === 'E' && $cek->getNumRows() > 0) || ($cek2->getNumRows() <= 0))
        {
            $builder_trxerror->insert([
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.L.A.1',
            ]);

            return redirect()->to(base_url('/arap/transaksi/addNDK'));
        }

        /*
        ==========================
        AMBIL Nota Debit / KreditST DATA
        ==========================
        */

        $docdate   = trim($this->request->getPost('docdate'));
        $senddate  = trim($this->request->getPost('senddate'));
        $jthtempo = $this->cleanNumber($this->request->getPost('jthtempo'));
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

        $kurs     = $this->cleanNumber($this->request->getPost('kurs'));
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
        UPDATE HEADER TMP Nota Debit / Kredit
        ==========================
        */

        $this->db->table('sc_tmp.ndk')
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

        $header = $this->db->table('sc_tmp.ndk')
            ->where('inputby',$nama)
            ->get()
            ->getRowArray();

        $idurutNDK = $header['idurut'];

        $this->db->table('sc_tmp.ndk')
            ->where('inputby',$nama)
            ->update(['status'=>'F']);

        /* ambil Nota Debit / Kredit final */

        $trxNDK = $this->db->table('sc_trx.ndk')
            ->where('idurut',$idurutNDK)
            ->get()
            ->getRowArray();

        $docnoNDKFinal = $trxNDK['docno'];

        /* ambil suffix */

        /*
        ==========================
        GENERATE DOCNO UMB
        ==========================
        */
        
        $prefix = 'UMK';
        $infix = date('ym', strtotime($this->session->get('logindate')));
        // $kodeSuffix = 'PT';
        $parts = explode('/',$docnoNDKFinal);
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
        LINK Nota Debit / Kredit → UMB
        ==========================
        */

        $this->db->table('sc_trx.ndk')
            ->where('docno',$docnoNDKFinal)
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
            base_url('/arap/transaksi/addUMB')
        );
    }


    function show_ndk(){
        $module = 'NDK';
        $table = 'sc_trx.ndk';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.ndk');

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

        $title = " Rendkrt Purchase Order";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("arap/transaksi/api_ndk/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/rendkrt_ndk.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/rendkrt_pp_non_header.mrt") ;
        // }

        return $this->fiky_rendkrt->render($datajson,$datamrt,$title,$nama,$module,$table,$docno);
    }

    function api_ndk(){
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
        $datamst = $this->m_arap->q_ndk_master($param);
        $datadtl = $this->m_arap->q_ndk_dtl($param);
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



    public function lapndk()
    {
        $data['title']="Lap. Nota Debit/Kredit";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.2'; $versirelease='I.L.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.L.A.2'";
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
        // $param = " and coalesce(inputby,'')='$nama'";
        // $dtl = $this->m_arap->q_ndk_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        // if ($dtl->getNumRows()>0) {
        //     $title = "WARNING !!!";
        //     $urlclear = base_url('arap/transaksi/clearEntryNDK');
        //     $urlnext = base_url('arap/transaksi/addNDK');
        //     $body = " Entry not finished found....!!!";
        //     $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        // } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.L.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('arap/v_list_lapndk',$data);
    }




    function list_lapndk(){
        $list = $this->m_arap->get_t_front_ndk_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.L.A.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        // $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        // $canPrint = isset($datadtl['dtl_akses']['a_rendkrt']) && trim($datadtl['dtl_akses']['a_rendkrt']) === 't';
        // $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';
        $total_debit = 0;
        $total_kredit = 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();

            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            


            $row[] = $lm->docno;
            
            // $row[] = $lm->kdsupplier;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            // $row[] = $lm->alamatsupplier;
            $row[] = $lm->keterangan;
            // $row[] = $lm->nmkota;
            // $row[] = $lm->docnohp;
            if(trim($lm->dk) == 'DEBIT'){
                $total_debit += $lm->total;
                $row[] = '<div class="ratakanan">'. number_format($lm->total, 2, '.', ',') . '</div>';
                $row[] = '<div class="ratakanan">' . '0'. '</div>';
            } else {
                $total_kredit += $lm->total;
                $row[] = '<div class="ratakanan">' . '0'. '</div>';
                $row[] = '<div class="ratakanan">'. number_format($lm->total, 2, '.', ',') . '</div>';
            }

            $row[] = $lm->nmsalesman;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_arap->t_front_ndk_view_count_all(),
            "recordsFiltered" => $this->m_arap->t_front_ndk_view_count_filtered(),
            "data" => $data,
            "total_debit" => '<div class="ratakanan">'. number_format($total_debit,2,'.',',') . '</div>',
            "total_kredit" => '<div class="ratakanan">'. number_format($total_kredit,2,'.',',') . '</div>'
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function laporan_jurnal_transaksi_ndk()
    {
        $docno = trim($this->request->getPost('docno'));

        if ($docno === '') {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Doc No NDK tidak ditemukan',
                'data'     => []
            ]);
        }

        $params = $this->db->escape($docno);

        $params = "
        AND (
            TRIM(jd.ref_docno) = {$params}
            OR TRIM(jh.docno) = {$params}
        )
    ";

        $query = $this->m_arap->q_laporan_jurnal_transaksi_ndk($params);

        return $this->response->setJSON([
            'status'   => true,
            'messages' => 'OK',
            'data'     => $query->getResultArray()
        ]);
    }


    public function cancelNDK()
    {
        $docno = trim(
            $this->request->getPost('docno')
        );

        if ($docno === '') {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno NDK tidak boleh kosong.'
            ]);
        }


        $db = \Config\Database::connect();


        try {

            $db->transBegin();


            // =====================================================
            // CEK DOKUMEN NDK
            // =====================================================

            $ndk = $db->table('sc_trx.ndk')
                ->where('docno', $docno)
                ->get()
                ->getRow();


            if (!$ndk) {

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dokumen NDK tidak ditemukan.'
                ]);
            }


            // =====================================================
            // UPDATE STATUS NDK = C
            // =====================================================

            $db->table('sc_trx.ndk')
                ->where('docno', $docno)
                ->update([
                    'status' => 'C'
                ]);


            // =====================================================
            // HAPUS transaction_dt DETAIL
            // =====================================================

            $db->table('sc_trx.transaction_dt')
                ->where('docno', $docno)
                ->delete();


            // =====================================================
            // CEK TRANSACTION
            // =====================================================

            if ($db->transStatus() === false) {

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cancel NDK gagal.'
                ]);
            }


            $db->transCommit();


            return $this->response->setJSON([
                'success' => true,
                'message' => 'Dokumen NDK berhasil dibatalkan.',
                'docno'   => $docno
            ]);


        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Cancel NDK Error: ' .
                $e->getMessage()
            );


            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // ============== Tanda Terima ====================================

    public function tterima()
    {
        $data['title'] = "Tanda Terima Supplier";

        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        /* CODE UNTUK VERSI */
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.L.A.2';
        $versirelease = 'I.L.A.2/01';
        $releasedate = date('2025-04-12 00:00:00');

        $versidb = $this->fiky_version->version(
            $kodemenu,
            $versirelease,
            $releasedate,
            $nama
        );

        $x = $this->fiky_menu->menus(
            $kodemenu,
            $versirelease,
            $releasedate
        );

        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;
        /* END CODE UNTUK VERSI */

        /* CEK ERROR TRANSAKSI */
        $paramerror = " and userid='$nama' and modul='I.L.A.2'";

        $dtlerror = $this->m_trxerror
            ->q_trxerror($paramerror)
            ->getRowArray();

        $count_err = $this->m_trxerror
            ->q_trxerror($paramerror)
            ->getNumRows();

        $errordesc = isset($dtlerror['description'])
            ? trim($dtlerror['description'])
            : '';

        $nomorakhir1 = isset($dtlerror['nomorakhir1'])
            ? trim($dtlerror['nomorakhir1'])
            : '';

        $errorcode = isset($dtlerror['errorcode'])
            ? trim($dtlerror['errorcode'])
            : '';

        if ($count_err > 0 && $errordesc != '') {
            if ($errorcode == 0) {
                $data['message'] = "
                <div class='alert alert-info'>
                    DATA SUCCESSFULLY PROCESSED $nomorakhir1
                </div>";
            } else {
                $data['message'] = "
                <div class='alert alert-info'>
                    $errordesc
                </div>";
            }
        } else {
            if ($errorcode == '0') {
                $data['message'] = "
                <div class='alert alert-info'>
                    DATA SUCCESSFULLY PROCESSED $nomorakhir1
                </div>";
            } else {
                $data['message'] = "";
            }
        }

        /* ITEM ENTRY MASTER CHECK */
        $param = " and coalesce(inputby,'')='$nama'";

        // TODO: Ganti dengan query master temporary Tanda Terima Supplier
        // Query master temporary Tanda Terima Supplier
        $dtl = $this->m_arap->q_tterima_master_temp($param);

        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows() > 0) {
            $title = "WARNING !!!";

            $urlclear = base_url('arap/transaksi/clearEntryTterima');
            $urlnext  = base_url('arap/transaksi/addTterima');

            $body = "Entry not finished found....!!!";

            $data['showUnfinish'] = $this->m_trxerror->unfinish(
                $nama,
                $urlclear,
                $urlnext,
                $title,
                $body
            );
        } else {
            $data['showUnfinish'] = '';
        }

        /* HAK AKSES MENU */
        $kmenu = 'I.L.A.2';
        $role = trim($this->session->get('roleid'));

        $data['dtl_akses'] = $this->m_role
            ->detail_user_akses($role, $kmenu)
            ->getRowArray();

        /* HAPUS ERROR TRANSAKSI USER */
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        return $this->template->render(
            '/arap/tterima/v_list_tterima',
            $data
        );
    }


    public function addTterima()
    {
        /* Penambahan Squence */
        $data['title']="Nota Tanda Terima";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.L.A.2'; $versirelease='I.L.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.L.A.2'";
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

        // =====================================================
        // AMBIL TANDA TERIMA TEMP USER
        // HANYA YANG STATUS = E
        // =====================================================
        $builderTmp = $this->db
            ->table('sc_tmp.tterima_hd')
            ->where('TRIM(inputby)', $nama)
            ->where('TRIM(status)', 'E')
            ->orderBy('inputdate', 'DESC')
            ->limit(1);

        $data['dtldata'] = $builderTmp
            ->get()
            ->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_purchase->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('arap/trans/tterima'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_purchase->q_po_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('arap/tterima/v_add_tterima',$data);
    }
    /**
     * DataTables list Tanda Terima Supplier
     */
    /**
     * DataTables list Tanda Terima Supplier
     */
    public function listTterimaTrx()
    {
        $list = $this->m_arap->get_t_tterima_hd();

        $data = array();

        $no = (int) ($_POST['start'] ?? 0);


        /*
         * ============================================================
         * ACCESS
         * ============================================================
         */

        $kmenu = 'I.L.A.2';

        $role = trim(
            $this->session->get('roleid')
        );

        $dtlAkses = $this->m_role
            ->detail_user_akses(
                $role,
                $kmenu
            )
            ->getRowArray();


        $canUpdate =
            isset($dtlAkses['a_update']) &&
            trim($dtlAkses['a_update']) === 't';

        $canView =
            isset($dtlAkses['a_view']) &&
            trim($dtlAkses['a_view']) === 't';


        /*
         * ============================================================
         * HELPER STATUS CHECKLIST DOKUMEN
         * ============================================================
         */

        $docStatusIcon = function ($value, $label) {

            $value = strtoupper(
                trim(
                    (string) $value
                )
            );

            $checked = in_array(
                $value,
                array(
                    '1',
                    'Y',
                    'YES',
                    'TRUE',
                    'T',
                    'OK',
                    'ADA'
                ),
                true
            );

            if ($checked) {

                return '
                <span
                    class="d-inline-block me-1"
                    title="' .
                    htmlspecialchars(
                        $label,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                    '"
                >
                    <i
                        class="fa fa-check-circle text-success"
                        style="font-size:18px;"
                    ></i>
                </span>
            ';

            }

            return '
            <span
                class="d-inline-block me-1"
                title="' .
                htmlspecialchars(
                    $label . ' belum dicentang',
                    ENT_QUOTES,
                    'UTF-8'
                ) .
                '"
            >
                <i
                    class="fa fa-circle-o text-muted"
                    style="font-size:18px;"
                ></i>
            </span>
        ';
        };


        /*
         * ============================================================
         * LOOP DATA
         * ============================================================
         */

        foreach ($list as $lm) {

            $no++;

            $row = array();


            /*
             * ========================================================
             * DATA DASAR
             * ========================================================
             */

            $docno = trim(
                $lm->docno ?? ''
            );

            $status = strtoupper(
                trim(
                    $lm->status ?? ''
                )
            );


            /*
             * ========================================================
             * ACTION
             * ========================================================
             */

            $menuContent = '';


            /*
             * EDIT
             */

            if ($canUpdate) {

                $menuContent .= '
    <a
        class="dropdown-item bg-warning"
        href="' .
                    base_url(
                        'arap/transaksi/updateTterima'
                    ) .
                    '?id=' .
                    bin2hex($docno) .
                    '"
    >
        <i class="fa fa-edit"></i>
        Edit Tanda Terima
    </a>
';
            }


            /*
             * VIEW
             */

            if ($canView) {

                $menuContent .= '
                <a
                    class="dropdown-item"
                    href="' .
                    base_url(
                        'arap/transaksi/addTterima'
                    ) .
                    '?docno=' .
                    urlencode($docno) .
                    '"
                >
                    <i class="fa fa-eye"></i>
                    Detail Tanda Terima
                </a>
            ';
            }


            /*
             * TRACEABILITY
             */

            $docnoJs = htmlspecialchars(
                $docno,
                ENT_QUOTES,
                'UTF-8'
            );

            $menuContent .= '
            <a
                class="dropdown-item bg-info text-white"
                href="#"
                onclick="openTterimaTraceabilityByDocno(\'' .
                $docnoJs .
                '\'); return false;"
            >
                <i class="fa fa-check-double"></i>
                Traceability Dokumen
            </a>
        ';


            /*
             * DROPDOWN
             */

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
                    ' .
                $menuContent .
                '
                </div>

            </div>
        ';


            /*
             * ========================================================
             * STATUS DOKUMEN TRANSAKSI
             * ========================================================
             *
             * Status E/F/C tetap ditampilkan hanya jika dibutuhkan
             * untuk informasi transaksi.
             *
             * Kolom UI sekarang menggunakan "Status Dokumen"
             * sebagai checklist dokumen.
             *
             * ========================================================
             */


            /*
             * ========================================================
             * STATUS CHECKLIST DOKUMEN
             * ========================================================
             *
             * Tidak mandatory.
             *
             * Hanya menunjukkan dokumen mana yang sudah dicentang.
             *
             * Invoice
             * Faktur Pajak
             * Surat Jalan
             * Penerimaan
             * Bea Import
             * Dokumen
             *
             * ========================================================
             */

            $statusDokumen = '

            <div
                class="d-flex align-items-center justify-content-center"
                style="white-space: nowrap;"
            >

                ' .
                $docStatusIcon(
                    $lm->cekinvoice ?? '',
                    'Invoice'
                ) .

                $docStatusIcon(
                    $lm->cekfakturpajak ?? '',
                    'Faktur Pajak'
                ) .

                $docStatusIcon(
                    $lm->ceksj ?? '',
                    'Surat Jalan'
                ) .

                $docStatusIcon(
                    $lm->cekpenerimaan ?? '',
                    'Penerimaan'
                ) .

                $docStatusIcon(
                    $lm->cekbeaimport ?? '',
                    'Bea Import'
                ) .

                $docStatusIcon(
                    $lm->cekdokumen ?? '',
                    'Dokumen'
                ) .

                '</div>
        ';


            /*
             * ========================================================
             * ROW
             * ========================================================
             *
             * HARUS SAMA DENGAN <thead>:
             *
             * 0 No.
             * 1 Action
             * 2 Document
             * 3 Tanggal
             * 4 Nama Supplier
             * 5 Currency
             * 6 Nilai
             * 7 Keterangan
             * 8 Status Dokumen
             *
             * ========================================================
             */

            $row[] = $no;

            $row[] = $dropdownMenu;

            $row[] = htmlspecialchars(
                $docno,
                ENT_QUOTES,
                'UTF-8'
            );

            $row[] = htmlspecialchars(
                !empty($lm->docdate)
                    ? date('d-m-Y', strtotime($lm->docdate))
                    : '',
                ENT_QUOTES,
                'UTF-8'
            );

            $row[] = htmlspecialchars(
                trim($lm->nmsupplier ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );

            $row[] = htmlspecialchars(
                trim($lm->currcode ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );

            $row[] = number_format(
                (float) ($lm->total ?? 0),
                2,
                '.',
                ','
            );

            $row[] = htmlspecialchars(
                trim($lm->keterangan ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );

            $row[] = $statusDokumen;


            /*
             * ========================================================
             * PUSH ROW
             * ========================================================
             */

            $data[] = $row;
        }


        /*
         * ============================================================
         * DATATABLE RESPONSE
         * ============================================================
         */

        $output = array(

            "draw" =>
                (int) ($_POST['draw'] ?? 0),

            "recordsTotal" =>
                $this->m_arap
                    ->t_tterima_hd_count_all(),

            "recordsFiltered" =>
                $this->m_arap
                    ->t_tterima_hd_count_filtered(),

            "data" =>
                $data
        );


        /*
         * ============================================================
         * ENCRYPT DATATABLE
         * ============================================================
         */

        echo $this->fiky_encryption
            ->jDatatable($output);
    }

    // ============================================================
// LIST DETAIL TANDA TERIMA
// Owner : nama session
// Header harus status E
// ============================================================
    public function listTterimaDetail()
    {
        $db      = db_connect();
        $request = $this->request;
        $session = session();

        $nama = trim(
            (string) $session->get('nama')
        );

        if ($nama === '') {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Session nama tidak ditemukan.'
                ]);
        }

        $docno = trim(
            (string) $request->getPost('docno')
        );

        if ($docno === '') {
            return $this->response->setJSON([
                'success' => true,
                'data'    => []
            ]);
        }

        $data = $db->table('sc_tmp.tterima_dt')
            ->select('
            idurut,
            docno,
            idunique,
            nobukti,
            docref,
            noperkiraan,
            namaperkiraan,
            keterangan,
            TRIM(dk) AS dk,
            costprofitcenter,
            nilai,
            status,
            inputby,
            inputdate,
            updateby,
            updatedate,
            docnotmp
        ')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->where('status', 'E')
            ->orderBy('idurut', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'status'  => true,
            'data'    => $data
        ]);
    }

    // ============================================================
// SAVE / UPDATE DETAIL TANDA TERIMA
// INSERT : sc_tmp.tterima_dt
// UPDATE : sc_tmp.tterima_dt
// Owner  : nama session
// Header : status E
// ============================================================
    public function saveTterimaDetail()
    {
        $db      = db_connect();
        $request = $this->request;
        $session = session();

        $transStarted = false;

        try {

            // =====================================================
            // USER
            // =====================================================
            $nama = trim(
                (string) $session->get('nama')
            );

            if ($nama === '') {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Session nama tidak ditemukan. Silakan login kembali.'
                    ]);
            }

            // =====================================================
            // ID HEADER
            // =====================================================
            $idurutHeader = trim(
                (string) $request->getPost('idurut')
            );

            if (
                $idurutHeader === '' ||
                !ctype_digit($idurutHeader)
            ) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'ID header Tanda Terima tidak valid.'
                    ]);
            }

            $idurutHeader = (int) $idurutHeader;

            // =====================================================
            // ID DETAIL / ID UNIQUE
            //
            // idurut_detail = SERIAL numeric
            // idunique      = MD5(docno.inputdate.inputby)
            //
            // uniqueid tetap diterima untuk kompatibilitas,
            // tetapi TIDAK lagi dianggap selalu numeric.
            // =====================================================
            $idurutDetailRaw = trim(
                (string) $request->getPost('idurut_detail')
            );

            $iduniquePosted = trim(
                (string) (
                $request->getPost('idunique')
                    ?: $request->getPost('uniqueid')
                )
            );

            $idurutDetail = 0;

            if ($idurutDetailRaw !== '') {

                if (!ctype_digit($idurutDetailRaw)) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'success' => false,
                            'message' => 'ID detail tidak valid.'
                        ]);
                }

                $idurutDetail = (int) $idurutDetailRaw;

            } elseif (
                $iduniquePosted !== '' &&
                preg_match('/^[a-f0-9]{32}$/i', $iduniquePosted)
            ) {

                // idunique MD5 akan dipakai sebagai pencarian UPDATE.
                // Nilai numeric idurut detail akan diambil dari database.

            } elseif ($iduniquePosted !== '') {

                // Kompatibilitas dengan frontend lama yang mengirim
                // uniqueid sebagai numeric ID detail.
                if (ctype_digit($iduniquePosted)) {
                    $idurutDetail = (int) $iduniquePosted;
                } else {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'success' => false,
                            'message' => 'ID detail / ID unique tidak valid.'
                        ]);
                }
            }

            // =====================================================
            // PERKIRAAN
            // =====================================================
            $perkiraan = strtoupper(
                trim((string) $request->getPost('perkiraan'))
            );

            if ($perkiraan === '') {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Perkiraan wajib diisi.'
                    ]);
            }

            // =====================================================
            // KETERANGAN
            // =====================================================
            $keterangan = strtoupper(
                trim((string) $request->getPost('keterangan_dtl'))
            );

            if ($keterangan === '') {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Keterangan detail wajib diisi.'
                    ]);
            }

            // =====================================================
            // DK
            // =====================================================
            $dk = strtoupper(
                trim((string) $request->getPost('dk'))
            );

            if (!in_array($dk, ['D', 'K'], true)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Debit / Kredit tidak valid. Nilai harus D atau K.'
                    ]);
            }

            // =====================================================
            // NILAI
            // =====================================================
            $nilaiRaw = trim(
                (string) $request->getPost('nilai')
            );

            $nilaiRaw = str_replace(
                ',',
                '',
                $nilaiRaw
            );

            $nilaiRaw = preg_replace(
                '/[^0-9.\-]/',
                '',
                $nilaiRaw
            );

            if (
                $nilaiRaw === '' ||
                $nilaiRaw === '-' ||
                !is_numeric($nilaiRaw)
            ) {
                $nilai = 0;
            } else {
                $nilai = (float) $nilaiRaw;
            }

            if ($nilai < 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Nilai tidak boleh negatif.'
                    ]);
            }

            // =====================================================
            // STRING LAIN
            // =====================================================
            $nobukti = strtoupper(
                trim((string) $request->getPost('nobukti'))
            );

            $namaperkiraan = strtoupper(
                trim((string) $request->getPost('namaperkiraan'))
            );

            $costprofitcenter = strtoupper(
                trim((string) $request->getPost('costcenter'))
            );

            // =====================================================
            // TRANSACTION
            // =====================================================
            $db->transBegin();
            $transStarted = true;

            // =====================================================
            // CEK HEADER
            // =====================================================
            $header = $db->table('sc_tmp.tterima_hd')
                ->select(
                    'idurut, docno, cabang, kdsupplier, nmsupplier, status'
                )
                ->where('idurut', $idurutHeader)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->get()
                ->getRowArray();

            if (!$header) {

                $db->transRollback();
                $transStarted = false;

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Header Tanda Terima tidak ditemukan atau bukan milik user.'
                    ]);
            }

            // =====================================================
            // CEK CABANG
            // =====================================================
            $cabang = trim(
                (string) ($header['cabang'] ?? '')
            );

            if ($cabang === '') {

                $db->transRollback();
                $transStarted = false;

                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Cabang pada header belum diisi.'
                    ]);
            }

            // =====================================================
            // CEK SUPPLIER
            // =====================================================
            $kdsupplier = trim(
                (string) ($header['kdsupplier'] ?? '')
            );

            if ($kdsupplier === '') {

                $db->transRollback();
                $transStarted = false;

                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Supplier pada header belum diisi.'
                    ]);
            }

            // =====================================================
            // DOCNO DARI DATABASE
            // =====================================================
            $docno = trim(
                (string) ($header['docno'] ?? '')
            );

            if ($docno === '') {

                $db->transRollback();
                $transStarted = false;

                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Nomor Tanda Terima tidak ditemukan.'
                    ]);
            }

            // =====================================================
            // JIKA ID UNIQUE MD5 DIKIRIM, CARI DETAIL
            // =====================================================
            if (
                $idurutDetail <= 0 &&
                $iduniquePosted !== '' &&
                preg_match('/^[a-f0-9]{32}$/i', $iduniquePosted)
            ) {

                $existingByUnique = $db->table('sc_tmp.tterima_dt')
                    ->select('idurut, idunique, inputdate')
                    ->where('idunique', strtolower($iduniquePosted))
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->get()
                    ->getRowArray();

                if ($existingByUnique) {
                    $idurutDetail = (int) $existingByUnique['idurut'];
                }
            }

            // =====================================================
            // UPDATE
            // =====================================================
            if ($idurutDetail > 0) {

                // -----------------------------------------------
                // CEK DETAIL MILIK USER
                // -----------------------------------------------
                $existing = $db->table('sc_tmp.tterima_dt')
                    ->select(
                        'idurut, docno, idunique, inputdate, inputby, status'
                    )
                    ->where('idurut', $idurutDetail)
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->get()
                    ->getRowArray();

                if (!$existing) {

                    $db->transRollback();
                    $transStarted = false;

                    return $this->response
                        ->setStatusCode(404)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Detail tidak ditemukan atau bukan milik user.'
                        ]);
                }

                // Pertahankan idunique yang sudah dibuat saat INSERT.
                $idunique = trim(
                    (string) ($existing['idunique'] ?? '')
                );

                // Untuk data lama yang belum memiliki idunique,
                // buat berdasarkan data asli row tersebut.
                if ($idunique === '') {

                    $existingInputDate = trim(
                        (string) ($existing['inputdate'] ?? '')
                    );

                    if ($existingInputDate === '') {
                        $existingInputDate = date('Y-m-d H:i:s.u');
                    }

                    $idunique = md5(
                        $docno .
                        '.' .
                        $existingInputDate .
                        '.' .
                        $nama
                    );
                }

                $updateData = [
                    'idunique'          => $idunique,
                    'nobukti'           => $nobukti,
                    'noperkiraan'       => $perkiraan,
                    'namaperkiraan'     => $namaperkiraan,
                    'keterangan'        => $keterangan,
                    'dk'                => $dk,
                    'costprofitcenter'  => $costprofitcenter,
                    'nilai'             => $nilai,
                    'updateby'          => $nama,
                    'updatedate'        => date('Y-m-d H:i:s')
                ];

                $updated = $db->table('sc_tmp.tterima_dt')
                    ->where('idurut', $idurutDetail)
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update($updateData);

                if (!$updated) {

                    $db->transRollback();
                    $transStarted = false;

                    return $this->response
                        ->setStatusCode(500)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Gagal mengupdate detail Tanda Terima.'
                        ]);
                }

                $detailId = $idurutDetail;
                $mode     = 'UPDATE';

            } else {

                // =================================================
                // INSERT
                // =================================================

                // Gunakan inputdate yang sama untuk pembentukan idunique.
                $inputDate = date('Y-m-d H:i:s.u');

                $idunique = md5(
                    $docno .
                    '.' .
                    $inputDate .
                    '.' .
                    $nama
                );

                $detailData = [
                    'docno'             => $docno,
                    'idunique'          => $idunique,
                    'nobukti'           => $nobukti,
                    'docref'            => '',
                    'noperkiraan'       => $perkiraan,
                    'namaperkiraan'     => $namaperkiraan,
                    'keterangan'        => $keterangan,
                    'dk'                => $dk,
                    'costprofitcenter'  => $costprofitcenter,
                    'nilai'             => $nilai,
                    'status'            => 'E',
                    'inputby'           => $nama,
                    'inputdate'         => $inputDate,
                    'docnotmp'          => $docno
                ];

                $inserted = $db->table(
                    'sc_tmp.tterima_dt'
                )->insert($detailData);

                if (!$inserted) {

                    $db->transRollback();
                    $transStarted = false;

                    return $this->response
                        ->setStatusCode(500)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Gagal menambahkan detail Tanda Terima.'
                        ]);
                }

                $detailId = $db->insertID();
                $mode     = 'INSERT';
            }

            // =====================================================
            // TRANSACTION STATUS
            // =====================================================
            if ($db->transStatus() === false) {

                $db->transRollback();
                $transStarted = false;

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Gagal menyimpan detail Tanda Terima.'
                    ]);
            }

            // =====================================================
            // COMMIT
            // =====================================================
            $db->transCommit();
            $transStarted = false;

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' => $mode === 'UPDATE'
                        ? 'Detail Tanda Terima berhasil diupdate.'
                        : 'Detail Tanda Terima berhasil ditambahkan.',
                    'data' => [
                        'idurut_header'     => $idurutHeader,
                        'idurut_detail'     => $detailId,
                        'idunique'          => $idunique,
                        'docno'             => $docno,
                        'nobukti'           => $nobukti,
                        'noperkiraan'       => $perkiraan,
                        'namaperkiraan'     => $namaperkiraan,
                        'keterangan'        => $keterangan,
                        'dk'                => $dk,
                        'costprofitcenter'  => $costprofitcenter,
                        'nilai'             => $nilai,
                        'mode'              => $mode
                    ]
                ]);

        } catch (\Throwable $e) {

            if ($transStarted) {
                $db->transRollback();
            }

            log_message(
                'error',
                'saveTterimaDetail error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
        }
    }
    // ============================================================
// DELETE DETAIL TANDA TERIMA
// Owner : nama session
// Status : E
// ============================================================
    public function deleteTterimaDetail()
    {
        $db      = db_connect();
        $request = $this->request;
        $session = session();

        try {

            // =====================================================
            // USER DARI SESSION
            // =====================================================
            $nama = trim(
                (string) $session->get('nama')
            );

            if ($nama === '') {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Session nama tidak ditemukan.'
                    ]);
            }

            // =====================================================
            // DOCNO
            // =====================================================
            $docno = trim(
                (string) $request->getPost('docno')
            );

            if ($docno === '') {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Docno Tanda Terima tidak valid.'
                    ]);
            }

            // =====================================================
            // ID DETAIL
            // idurut yang dikirim frontend = ID DETAIL
            // =====================================================
            $idurutDetail = trim(
                (string) $request->getPost('idurut')
            );

            if (
                $idurutDetail === '' ||
                !ctype_digit($idurutDetail) ||
                (int) $idurutDetail <= 0
            ) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'ID detail tidak valid.'
                    ]);
            }

            $idurutDetail = (int) $idurutDetail;

            // =====================================================
            // CEK HEADER
            // Berdasarkan:
            // docno + inputby + status E
            // =====================================================
            $header = $db->table('sc_tmp.tterima_hd')
                ->select('docno, inputby, status')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->get()
                ->getRowArray();

            if (!$header) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Header Tanda Terima tidak ditemukan atau bukan milik user.'
                    ]);
            }

            // =====================================================
            // CEK DETAIL
            // WHERE:
            // docno
            // idurut
            // inputby = nama
            // status = E
            // =====================================================
            $detail = $db->table('sc_tmp.tterima_dt')
                ->select('idurut, docno, inputby, status')
                ->where('docno', $docno)
                ->where('idurut', $idurutDetail)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->get()
                ->getRowArray();

            if (!$detail) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Detail Tanda Terima tidak ditemukan atau bukan milik user.'
                    ]);
            }

            // =====================================================
            // DELETE
            // =====================================================
            $deleted = $db->table('sc_tmp.tterima_dt')
                ->where('docno', $docno)
                ->where('idurut', $idurutDetail)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->delete();

            if (!$deleted) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Gagal menghapus detail Tanda Terima.'
                    ]);
            }

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' => 'Detail Tanda Terima berhasil dihapus.',
                    'data' => [
                        'docno'         => $docno,
                        'idurut_detail' => $idurutDetail,
                        'inputby'       => $nama
                    ]
                ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'deleteTterimaDetail error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
        }
    }

// ================================================================
// TAMBAHKAN METHOD BERIKUT KE App\Controllers\Arap\Arap
// Mekanisme mengikuti referensi PO:
// branch -> kode_suffix + infix + prefix -> getNextSuffix -> docno
// ================================================================

    public function getBranchInfoTterima()
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
        // AMBIL KONFIGURASI UMUM
        // ==========================================
        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray();

        // ==========================================
        // CEK KONFIGURASI
        // ==========================================
        if (empty($konfigurasiUmum)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Konfigurasi umum belum tersedia.'
            ]);
        }

        // ==========================================
        // LOGIN DATE
        // ==========================================
        $logindate = trim((string) $this->session->get('logindate'));

        // ==========================================
        // FORMAT INFIX
        // ==========================================
        $infix = '';

        if ($logindate !== '') {
            $timestamp = strtotime($logindate);

            if ($timestamp !== false) {
                $infix = date('ym', $timestamp);
            }
        }

        // ==========================================
        // KONFIGURASI TT
        // ==========================================
        $config = $konfigurasiUmum[0];

        /*
         * Prefix Tanda Terima
         *
         * Sesuaikan nama field "tterima"
         * dengan kolom yang ada di sc_mst.konfigurasi_umum.
         */
        $prefix = trim((string) ($config['tt'] ?? ''));

        $currcode = trim((string) ($config['currcode'] ?? ''));
        $idtax    = trim((string) ($config['idtax'] ?? ''));

        // ==========================================
        // VALIDASI PREFIX TT
        // ==========================================
        if ($prefix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Prefix Tanda Terima belum diset pada konfigurasi umum.'
            ]);
        }

        // ==========================================
        // RESPONSE
        // ==========================================
        return $this->response->setJSON([
            'success' => true,

            // ======================================
            // BRANCH
            // ======================================
            'idbranch'    => trim((string) $row['idbranch']),
            'nmbranch'    => $nmbranch,
            'kode_suffix' => $kodeSuffix,

            // ======================================
            // NOMOR TANDA TERIMA
            // ======================================
            'prefix'      => $prefix,
            'infix'       => $infix,

            // ======================================
            // PERIODE
            // ======================================
            'logindate'   => $logindate,

            // ======================================
            // DEFAULT TRANSAKSI
            // ======================================
            'currcode'    => $currcode,
            'idtax'       => $idtax,

            // ======================================
            // KONFIGURASI
            // ======================================
            'konfigurasi_umum' => $konfigurasiUmum,
        ]);
    }


    public function getNextSuffixTterima()
    {
        $prefix     = trim((string) $this->request->getGet('prefix'));
        $infix      = trim((string) $this->request->getGet('infix'));
        $kodeSuffix = trim((string) $this->request->getGet('kode_suffix'));

        if ($prefix === '' || $infix === '' || $kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Prefix, infix, dan kode suffix wajib diisi.'
            ]);
        }

        // Mengikuti mekanisme getNextSuffixPO.
        $prefixPadded = str_pad($prefix, 3, ' ');
        $like = $prefixPadded . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db
            ->table('sc_trx.tterima_hd')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', rtrim((string) $row['docno']));
            $lastPart = $parts[2] ?? '';

            // PT0001 / PA0001 / PB0001 -> 0001
            $last = substr($lastPart, 2);
            $next = str_pad(
                ((int) $last) + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }

// ================================================================
// TAMBAHKAN ROUTE DALAM group /arap/transaksi
// ================================================================

    public function saveTterima()
    {
        try {

            $request = $this->request;
            $session = session();

            // =====================================================
            // USER LOGIN
            // NIK dari session menjadi owner temporary.
            // Jangan mengambil NIK dari POST agar tidak bisa dipalsukan.
            // =====================================================
            $nama = trim((string) $session->get('nama'));

            if ($nama === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session NIK tidak ditemukan. Silakan login kembali.'
                ]);
            }


            // =====================================================
            // DATA DASAR
            // =====================================================
            $idurut = trim((string) $request->getPost('idurut'));
            $docno  = trim((string) $request->getPost('docno'));

            $docdate = trim((string) $request->getPost('docdate'));
            $cabang  = trim((string) $request->getPost('cabang'));
            $coabank = trim(
                (string) $request->getPost('coabank')
            );

            $nmcoabank = trim(
                (string) $request->getPost('nmcoabank')
            );

            // =====================================================
            // VALIDASI INPUT AWAL
            // =====================================================
            if ($docno === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nomor Tanda Terima belum terbentuk.'
                ]);
            }

            if ($docdate === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tanggal Tanda Terima belum diisi.'
                ]);
            }

            if ($cabang === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cabang belum dipilih.'
                ]);
            }


            // =====================================================
            // DATA HEADER
            // Field identitas akan diamankan lagi ketika UPDATE
            // =====================================================
            $data = [

                // -------------------------------------------------
                // HEADER
                // -------------------------------------------------
                'docno' => $docno,

                'docdate' =>
                    $this->convertDateToDb($docdate),

                'cabang' => $cabang,

                'pemohon' =>
                    trim((string) $request->getPost('pemohon')),


                // -------------------------------------------------
                // SUPPLIER
                // -------------------------------------------------
                'kdsupplier' =>
                    trim((string) $request->getPost('kdsupplier')),

                'nmsupplier' =>
                    trim((string) $request->getPost('nmsupplier')),

                'kotasupplier' =>
                    trim((string) $request->getPost('kotasupplier')),

                'alamatsupplier' =>
                    trim((string) $request->getPost('alamatsupplier')),

                'alamatkirim' =>
                    trim((string) $request->getPost('alamatkirim')),


                // -------------------------------------------------
                // DOCUMENT
                // -------------------------------------------------
                'noinvoice' =>
                    trim((string) $request->getPost('noinvoice')),

                'tglinvoice' =>
                    $this->convertDateToDb(
                        $request->getPost('tglinvoice')
                    ),

                'nosj' =>
                    trim((string) $request->getPost('nosj')),

                'tglsj' =>
                    $this->convertDateToDb(
                        $request->getPost('tglsj')
                    ),

                'noaju' =>
                    trim((string) $request->getPost('noaju')),

                'nobl' =>
                    trim((string) $request->getPost('nobl')),

                'noawb' =>
                    trim((string) $request->getPost('noawb')),

                'noinvoicebea' =>
                    trim((string) $request->getPost('noinvoicebea')),

                'tglinvoicebea' =>
                    $this->convertDateToDb(
                        $request->getPost('tglinvoicebea')
                    ),

                'nobkrev' =>
                    trim((string) $request->getPost('nobkrev')),

                'nofakturpajak' =>
                    trim((string) $request->getPost('nofakturpajak')),


                // -------------------------------------------------
                // CURRENCY / JATUH TEMPO
                // -------------------------------------------------
                'senddate' =>
                    $this->convertDateToDb(
                        $request->getPost('senddate')
                    ),

                'currcode' =>
                    trim((string) $request->getPost('currcode'))
                        ?: 'IDR',

                'idtax' =>
                    trim((string) $request->getPost('idtax')),

                'kurs' =>
                    $this->dbNumber(
                        $request->getPost('kurs')
                    ),

                'jthtempo' =>
                    $this->dbNumber(
                        $request->getPost('jthtempo')
                    ),

                'tgljthtempo' =>
                    $this->convertDateToDb(
                        $request->getPost('tgljthtempo')
                    ),
                'coabank' =>
                    trim((string) $request->getPost('coabank')),

                'nmcoabank' =>
                    trim((string) $request->getPost('nmcoabank')),

                'isinclusive' =>
                    strtoupper(
                        trim((string) $request->getPost('isinclusive'))
                    ) === 'YES'
                        ? 'YES'
                        : 'NO',


                // -------------------------------------------------
                // VALUE
                // -------------------------------------------------
                'dpp' =>
                    $this->dbNumber(
                        $request->getPost('dpp')
                    ),

                'jumlahpajak' =>
                    $this->dbNumber(
                        $request->getPost('jumlahpajak')
                    ),

                'total' =>
                    $this->dbNumber(
                        $request->getPost('total')
                    ),

                'beaimport' =>
                    $this->dbNumber(
                        $request->getPost('beaimport')
                    ),

                'ppnimport' =>
                    $this->dbNumber(
                        $request->getPost('ppnimport')
                    ),

                'pphimport' =>
                    $this->dbNumber(
                        $request->getPost('pphimport')
                    ),

                'biayaangkut' =>
                    $this->dbNumber(
                        $request->getPost('biayaangkut')
                    ),

                'biayaasuransi' =>
                    $this->dbNumber(
                        $request->getPost('biayaasuransi')
                    ),

                'biayalain' =>
                    $this->dbNumber(
                        $request->getPost('biayalain')
                    ),

                'totalestimasi' =>
                    $this->dbNumber(
                        $request->getPost('totalestimasi')
                    ),


                // -------------------------------------------------
                // CHECKLIST
                // -------------------------------------------------

                'cekinvoice' =>
                    $request->getPost('cekinvoice') === '1',

                'ceksj' =>
                    $request->getPost('ceksj') === '1',

                'cekpenerimaan' =>
                    $request->getPost('cekpenerimaan') === '1',

                'cekfakturpajak' =>
                    $request->getPost('cekfakturpajak') === '1',

                'cekbeaimport' =>
                    $request->getPost('cekbeaimport') === '1',

                'cekdokumen' =>
                    $request->getPost('cekdokumen') === '1',


                // -------------------------------------------------
                // STATUS
                // -------------------------------------------------
                'status' =>
                    trim((string) $request->getPost('status'))
                        ?: 'E',

                'keterangan' =>
                    trim((string) strtoupper($request->getPost('keterangan'))),
            ];


            // =====================================================
            // DATABASE
            // =====================================================
            $db = db_connect();

            $db->transBegin();


            // =====================================================
            // CARI TEMPORARY MILIK USER
            //
            // Tidak hanya percaya idurut dari browser.
            // =====================================================
            $existing = null;


            // -----------------------------------------------------
            // Jika idurut dikirim, cek bahwa memang milik user
            // -----------------------------------------------------
            if ($idurut !== '') {

                $existing = $db->table('sc_tmp.tterima_hd')
                    ->where('idurut', (int) $idurut)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->get()
                    ->getRowArray();
            }


            // -----------------------------------------------------
            // Jika tidak ditemukan, cari berdasarkan inputby
            // -----------------------------------------------------
            if (!$existing) {

                $existing = $db->table('sc_tmp.tterima_hd')
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->orderBy('idurut', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
            }


            // =====================================================
            // UPDATE
            // =====================================================
            if ($existing) {

                $idurut = (int) $existing['idurut'];


                // -------------------------------------------------
                // FIELD YANG SUDAH LOCKED
                //
                // Jangan ambil dari POST ketika UPDATE.
                // -------------------------------------------------
                $data['docno'] =
                    trim((string) $existing['docno']);

                $data['cabang'] =
                    trim((string) $existing['cabang']);

                $data['kdsupplier'] =
                    trim((string) $existing['kdsupplier']);

                $data['nmsupplier'] =
                    trim((string) $existing['nmsupplier']);

                $data['alamatsupplier'] =
                    trim((string) $existing['alamatsupplier']);


                // -------------------------------------------------
                // TANGGAL JATUH TEMPO
                // -------------------------------------------------
                // TIDAK lagi dipertahankan dari database.
                // Nilai POST tgljthtempo harus masuk ke UPDATE.
                //
                // Karena inputby adalah owner NIK dan status masih E,
                // header temporary milik user boleh diperbarui.


                // -------------------------------------------------
                // UPDATE HEADER TEMPORARY
                // -------------------------------------------------
                $db->table('sc_tmp.tterima_hd')
                    ->where('idurut', $idurut)
                    ->where(
                        'docno',
                        trim((string) $existing['docno'])
                    )
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update([
                        ...$data,

                        // Draft selalu tetap E.
                        'status' =>
                            'E',

                        'updateby' =>
                            $nama ?: $nama,

                        'updatedate' =>
                            date('Y-m-d H:i:s')
                    ]);

                $message =
                    'Header Tanda Terima berhasil diperbarui.';
            }


            // =====================================================
            // INSERT
            // =====================================================
            else {

                // -------------------------------------------------
                // PEMILIK TEMPORARY
                // -------------------------------------------------
                // inputby menyimpan NIK sebagai owner temporary.
                $data['inputby'] =
                    $nama;

                $data['inputdate'] =
                    date('Y-m-d H:i:s');


                // -------------------------------------------------
                // DOCUMENT TEMP
                // -------------------------------------------------
                $data['docnotmp'] =
                    $docno;


                // -------------------------------------------------
                // DEFAULT VALUE
                // -------------------------------------------------
                $data['balance'] = 0;
                $data['beaimport'] = 0;
                $data['ppnimport'] = 0;
                $data['pphimport'] = 0;
                $data['biayaangkut'] = 0;
                $data['biayaasuransi'] = 0;
                $data['biayalain'] = 0;
                $data['totalestimasi'] = 0;


                // -------------------------------------------------
                // INSERT
                // -------------------------------------------------
                $db->table('sc_tmp.tterima_hd')
                    ->insert($data);

                $idurut =
                    $db->insertID();


                $message =
                    'Header Tanda Terima berhasil disimpan.';
            }


            // =====================================================
            // TRANSACTION CHECK
            // =====================================================
            if ($db->transStatus() === false) {

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' =>
                        'Gagal menyimpan header Tanda Terima.'
                ]);
            }


            // =====================================================
            // COMMIT
            // =====================================================
            $db->transCommit();


            // =====================================================
            // RESPONSE
            // =====================================================
            return $this->response->setJSON([

                'success' => true,

                'message' =>
                    $message,

                'data' => [

                    'idurut' =>
                        $idurut,

                    'docno' =>
                        $data['docno'],

                    'status' =>
                        'E',

                    'inputby' =>
                        $nama
                ]
            ]);


        } catch (\Throwable $e) {

            log_message(
                'error',
                'saveTterima error: ' .
                $e->getMessage()
            );

            return $this->response->setJSON([

                'success' => false,

                'message' =>
                    'Terjadi kesalahan: ' .
                    $e->getMessage()
            ]);
        }
    }

    private function convertDateToDb($date)
    {
        if (empty($date)) {
            return null;
        }

        $date = trim($date);

        // DD-MM-YYYY
        if (preg_match(
            '/^(\d{2})-(\d{2})-(\d{4})$/',
            $date,
            $m
        )) {

            return $m[3] . '-' .
                $m[2] . '-' .
                $m[1];
        }

        // YYYY-MM-DD
        if (preg_match(
            '/^\d{4}-\d{2}-\d{2}$/',
            $date
        )) {

            return $date;
        }

        return null;
    }

    private function dbNumber($value)
    {
        if ($value === null) {
            return 0;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        // Hapus separator ribuan
        $value = str_replace(',', '', $value);

        // Hilangkan karakter selain angka, minus, dan titik desimal
        $value = preg_replace('/[^0-9.\-]/', '', $value);

        if ($value === '' || $value === '-' || $value === '.') {
            return 0;
        }

        if (!is_numeric($value)) {
            return 0;
        }

        // Kembalikan sebagai numeric string agar aman untuk PostgreSQL numeric
        return $value;
    }




    // ============================================================
// SHOWING TEMP TANDA TERIMA
// HEADER + DETAIL
// ============================================================
    public function showing_tmp_tterima()
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
        // AMBIL HEADER TEMP MILIK USER
        // =========================================================
        $header = $this->db->table('sc_tmp.tterima_hd')
            ->where('TRIM(inputby)', $nama)
            ->where('TRIM(status)', 'E')
            ->orderBy('idurut', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // =========================================================
        // BELUM ADA TEMP
        // = INPUT BARU
        // =========================================================
        if (!$header) {

            return $this->response->setJSON([
                'status'  => true,
                'mode'    => 'INPUT',
                'message' => 'Belum ada Tanda Terima temporary.',
                'data'    => [
                    'header' => null,
                    'detail' => []
                ]
            ]);
        }

        // =========================================================
        // AMBIL DETAIL BERDASARKAN DOCNO
        // =========================================================
        $detail = $this->db->table('sc_tmp.tterima_dt')
            ->where('docno', $header['docno'])
            ->get()
            ->getResultArray();

        // =========================================================
        // RESPONSE
        // =========================================================
        return $this->response->setJSON([
            'status'  => true,
            'mode'    => 'UPDATE',
            'message' => 'Temporary Tanda Terima ditemukan.',
            'data'    => [
                'header' => $header,
                'detail' => $detail
            ]
        ]);
    }


    public function finalTterima()
    {
        try {

            $request = $this->request;
            $session = session();

            // =====================================================
            // USER LOGIN
            // =====================================================
            $nama = trim((string) $session->get('nama'));

            if ($nama === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Session nama tidak ditemukan. Silakan login kembali.'
                ]);
            }


            // =====================================================
            // POST
            // =====================================================
            $idurut = trim(
                (string) $request->getPost('idurut')
            );

            $docno = trim(
                (string) $request->getPost('docno')
            );


            if ($idurut === '' && $docno === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID atau nomor Tanda Terima tidak ditemukan.'
                ]);
            }


            // =====================================================
            // DATABASE
            // =====================================================
            $db = db_connect();

            $db->transBegin();


            // =====================================================
            // CARI HEADER TMP MILIK USER
            // =====================================================
            $builder = $db->table('sc_tmp.tterima_hd')
                ->where('inputby', $nama)
                ->where('status', 'E');


            // Prioritaskan ID apabila dikirim
            if ($idurut !== '') {

                $builder->where(
                    'idurut',
                    (int) $idurut
                );

            } else {

                $builder->where(
                    'docno',
                    $docno
                );
            }


            $header = $builder
                ->limit(1)
                ->get()
                ->getRowArray();


            if (!$header) {

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data Tanda Terima tidak ditemukan atau bukan milik user.'
                ]);
            }


            // =====================================================
            // ID DAN DOCNO OTORITATIF DARI DATABASE
            // =====================================================
            $idurut = (int) $header['idurut'];

            $docno = trim(
                (string) $header['docno']
            );


            // =====================================================
            // UPDATE STATUS E -> F
            // TRIGGER DATABASE AKAN MENJALANKAN FINALISASI
            // =====================================================
            $db->table('sc_tmp.tterima_hd')
                ->where('idurut', $idurut)
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->update([
                    'status' => 'F',
                    'updateby' => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
                ]);


            // =====================================================
            // CEK TRANSACTION
            // =====================================================
            if ($db->transStatus() === false) {

                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal melakukan finalisasi Tanda Terima.'
                ]);
            }


            // =====================================================
            // COMMIT
            // =====================================================
            $db->transCommit();


            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tanda Terima berhasil difinalisasi.',
                'data' => [
                    'idurut' => $idurut,
                    'docno' => $docno,
                    'status' => 'F'
                ]
            ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'finalTterima error: ' . $e->getMessage()
            );

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat finalisasi Tanda Terima.'
            ]);
        }
    }
    public function clearEntryTterima()
    {
        $nama   = trim($this->session->get('nama'));
        $urlBack = base_url('arap/transaksi/tterima');

        if ($nama === '') {
            return redirect()->to($urlBack);
        }

        $db = \Config\Database::connect();

        try {

            $db->transBegin();

            /*
             * --------------------------------------------------------------------------
             * 1. Cari dokumen temporary milik user
             * --------------------------------------------------------------------------
             */
            $tmpRows = $db->table('sc_tmp.tterima_hd')
                ->select('idurut, docno, docnotmp')
                ->where('inputby', $nama)
                ->get()
                ->getResultArray();


            /*
             * --------------------------------------------------------------------------
             * 2. Kembalikan transaksi yang sedang diedit
             * --------------------------------------------------------------------------
             */
            foreach ($tmpRows as $tmp) {

                $docnoTmp = trim($tmp['docno'] ?? '');
                $docnoTrx = trim($tmp['docnotmp'] ?? '');

                // Jika docnotmp kosong, gunakan docno temporary
                if ($docnoTrx === '') {
                    $docnoTrx = $docnoTmp;
                }


                /*
                 * ----------------------------------------------------------------------
                 * Kembalikan header permanent menjadi F
                 * ----------------------------------------------------------------------
                 */
                if ($docnoTrx !== '') {

                    $db->table('sc_trx.tterima_hd')
                        ->where('idurut', $tmp['idurut'])
                        ->where('docno', $docnoTrx)
                        ->where('status', 'E')
                        ->update([
                            'status'     => 'F',
                            'updateby'   => $nama,
                            'updatedate' => date('Y-m-d H:i:s')
                        ]);
                }


                /*
                 * ----------------------------------------------------------------------
                 * Kembalikan detail permanent menjadi F
                 * ----------------------------------------------------------------------
                 */
                if ($docnoTrx !== '') {

                    $db->table('sc_trx.tterima_dt')
                        ->where('idurut', $tmp['idurut'])
                        ->where('docno', $docnoTrx)
                        ->where('status', 'E')
                        ->update([
                            'status'     => 'F',
                            'updateby'   => $nama,
                            'updatedate' => date('Y-m-d H:i:s')
                        ]);
                }


                /*
                 * ----------------------------------------------------------------------
                 * HAPUS DETAIL TEMPORARY
                 *
                 * Gunakan docno yang sama dengan header temporary.
                 * Ini memastikan detail hasil "Tarik Penerimaan" ikut terhapus.
                 * ----------------------------------------------------------------------
                 */
                if ($docnoTmp !== '') {

                    $db->table('sc_tmp.tterima_dt')
                        ->where('docno', $docnoTmp)
                        ->delete();
                }
            }


            /*
             * --------------------------------------------------------------------------
             * 3. Hapus detail temporary milik user sebagai fallback
             * --------------------------------------------------------------------------
             *
             * Jika ada detail temporary yang tidak ditemukan melalui header,
             * tetap bersihkan berdasarkan inputby.
             */
            $db->table('sc_tmp.tterima_dt')
                ->where('inputby', $nama)
                ->delete();


            /*
             * --------------------------------------------------------------------------
             * 4. Hapus header temporary
             * --------------------------------------------------------------------------
             */
            $db->table('sc_tmp.tterima_hd')
                ->where('inputby', $nama)
                ->delete();


            /*
             * --------------------------------------------------------------------------
             * 5. Cek transaksi
             * --------------------------------------------------------------------------
             */
            if ($db->transStatus() === false) {

                $db->transRollback();

                log_message(
                    'error',
                    'clearEntryTterima gagal rollback. User: ' . $nama
                );

                return redirect()
                    ->to($urlBack)
                    ->with(
                        'error',
                        'Gagal melakukan clear Tanda Terima.'
                    );
            }


            /*
             * --------------------------------------------------------------------------
             * 6. Commit
             * --------------------------------------------------------------------------
             */
            $db->transCommit();

            return redirect()
                ->to($urlBack)
                ->with(
                    'success',
                    'Entry Tanda Terima dibatalkan dan data temporary berhasil dihapus.'
                );

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'clearEntryTterima ERROR: ' . $e->getMessage()
            );

            return redirect()
                ->to($urlBack)
                ->with(
                    'error',
                    'Gagal clear Tanda Terima: ' . $e->getMessage()
                );
        }
    }

    public function updateTterima()
    {
        $nama = trim(
            $this->session->get('nama')
        );

        /*
         * ============================================================
         * DOCNO
         * ============================================================
         */

        $docno = hex2bin(
            $this->request->getGet('id')
        );


        /*
         * ============================================================
         * GET HEADER TANDA TERIMA
         * ============================================================
         */

        $param =
            " and coalesce(docno,'')='$docno'";

        $dtl = $this->m_arap
            ->q_tterima_master($param)
            ->getRowArray();


        /*
         * ============================================================
         * JIKA DATA TIDAK DITEMUKAN
         * ============================================================
         */

        if (!$dtl) {

            return redirect()->to(
                base_url('arap/transaksi/tterima')
            );
        }


        /*
         * ============================================================
         * STATUS
         * ============================================================
         */

        $status = trim(
            $dtl['status'] ?? ''
        );


        /*
         * ============================================================
         * LOGIN DATE
         * ============================================================
         */

        $logindate = trim(
            $this->session->get('logindate')
        );


        /*
         * ============================================================
         * GUARD PERIODE TUTUP
         * ============================================================
         */

        $periode = date(
            'ym',
            strtotime($logindate)
        );

        $dtlPeriode = $this->m_purchase
            ->q_cek_periode($periode)
            ->getRowArray();


        if (
            $dtlPeriode &&
            strtoupper(
                trim(
                    $dtlPeriode['flagproses']
                )
            ) === 'TUTUP'
        ) {

            session()->setFlashdata(
                'periode_error',
                'Periode ' .
                $periode .
                ' sudah TUTUP. Tidak dapat melakukan input.'
            );

            return redirect()->to(
                base_url(
                    'arap/transaksi/tterima'
                )
            );
        }


        /*
         * ============================================================
         * STATUS F / P
         * ============================================================
         *
         * F = Final
         * P = Posted
         *
         * Diubah kembali menjadi E
         * agar dapat diedit.
         * ============================================================
         */

        if (
            $status === 'F' ||
            $status === 'P'
        ) {

            $info = array(
                'status' => 'E'
            );


            /*
             * ========================================================
             * UPDATE STATUS
             * ========================================================
             */

            $builder = $this->db
                ->table('sc_trx.tterima_hd');


            $builder
                ->where(
                    'trim(docno)',
                    $docno
                );


            $builder->update(
                $info
            );


            /*
             * ========================================================
             * REDIRECT KE ADD TTERIMA
             * ========================================================
             */

            return redirect()->to(
                base_url(
                    'arap/transaksi/addTterima'
                ) .
                '?docno=' .
                urlencode($docno)
            );


        } else {

            /*
             * ========================================================
             * STATUS BUKAN F / P
             * ========================================================
             */

            return redirect()->to(
                base_url(
                    'arap/transaksi/tterima'
                )
            );
        }
    }

    /* TANDA TERIMA LOAD LPB */

    public function tarikPenerimaanTterima()
    {
        $docno      = trim($this->request->getPost('docno'));
        $kdsupplier = trim($this->request->getPost('kdsupplier'));
        $inputby    = trim($this->request->getPost('inputby'));

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
        $sql = "
            SELECT
                TRIM(jd.ref_docno) AS nobukti,
                TRIM(jd.idcoa) AS noperkiraan,
                TRIM(td.kdsupplier) AS kdsupplier,
                TRIM(td.nsupplier) AS nmsupplier,
                STRING_AGG(
                    DISTINCT NULLIF(TRIM(td.namabarang), ''),
                    ' / '
                    ORDER BY NULLIF(TRIM(td.namabarang), '')
                ) AS keterangan,
                SUM(COALESCE(jd.kredit, 0)) AS nilai
            FROM sc_trx.jurnal_dt jd
            INNER JOIN sc_trx.transaction_dt td
                ON TRIM(td.uniqueid) = TRIM(jd.source_uniqueid)
            WHERE trim(td.journal_type)='GRNREC' and TRIM(td.kdsupplier) = ?
              AND COALESCE(jd.kredit, 0) > 0
            GROUP BY
                TRIM(jd.ref_docno),
                TRIM(jd.idcoa),
                TRIM(td.kdsupplier),
                TRIM(td.nsupplier)
            ORDER BY
                TRIM(jd.ref_docno),
                TRIM(jd.idcoa)
        ";

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

            foreach ($rows as $row) {

                $nobukti     = trim($row['nobukti']);
                $noperkiraan = trim($row['noperkiraan']);
                $nilai       = (float) $row['nilai'];
                $keterangan  = trim($row['keterangan'] ?? '');

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
                    ->table('sc_tmp.tterima_dt')
                    ->where('docno', $docno)
                    ->where('nobukti', $nobukti)
                    ->where('noperkiraan', $noperkiraan)
                    ->countAllResults();

                if ($cek > 0) {
                    $skipped++;
                    continue;
                }

                $this->db
                    ->table('sc_tmp.tterima_dt')
                    ->insert([
                        'docno'            => $docno,
                        'idunique'         => $idunique,
                        'nobukti'          => $nobukti,
                        'docref'           => $nobukti,
                        'noperkiraan'      => $noperkiraan,
                        'namaperkiraan'    => '',
                        'keterangan'       => $keterangan,
                        'dk'               => 'D',
                        'costprofitcenter' => '',
                        'nilai'            => $nilai,
                        'status'           => 'E',
                        'inputby'          => $inputby,
                        'inputdate'        => date('Y-m-d H:i:s')
                    ]);

                $inserted++;
            }

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
                'message'  => 'Penerimaan berhasil ditarik ke detail Tanda Terima.',
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

}