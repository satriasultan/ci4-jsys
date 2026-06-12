<?php


namespace App\Controllers\Tools;

use App\Controllers\BaseController;

class Tools extends BaseController
{

    // =================================== SAHP ===========================================

    public function index()
    {
        $data['title']="Setting Tanggal Awal";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.A.1'";
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
        
        $logindate = trim($this->session->get('logindate'));
        $kmenu = 'I.T.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        
        $builderTglAwal = $this->db->table('sc_trx.tglawal');
        $dataTglAwal = $builderTglAwal
        ->where('flagproses', 'PROSES')
        ->orderBy('idurut', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();
    
        // Kirim data ke view
        $data['tglawal'] = isset($dataTglAwal['tglawal']) ? $dataTglAwal['tglawal'] : '';
        
        return $this->template->render('tools/settingawal/v_list_sta',$data);
    }

    public function processTglAwal()
    {
        $nama = trim($this->session->get('nama'));
        $tglawal = trim($this->request->getPost('tglawal'));
        
        // Validasi input
        if (empty($tglawal)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal awal tidak boleh kosong'
            ]);
        }
        
        // // Validasi format tanggal (asumsi format d-m-Y)
        // $dt = DateTime::createFromFormat('d-m-Y', $tglawal);
        // if (!$dt) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Format tanggal tidak valid. Gunakan format DD-MM-YYYY'
        //     ]);
        // }
        
        $db = $this->db;
        $db->transStart();
        
        try {
            $builderTglAwal = $db->table('sc_trx.tglawal');
            
            // =====================================================
            // 1. NON-AKTIFKAN DATA YANG SEDANG PROSES
            // =====================================================
            $builderTglAwal
                ->where('flagproses', 'PROSES')
                ->update([
                    'flagproses' => 'NO',
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
                ]);
            
            
            // =====================================================
            // 3. INSERT DATA BARU DENGAN FLAG PROSES
            // =====================================================
            $dataInsert = [
                'tglawal'    => date('Y-m-d', strtotime(trim($tglawal))), // Simpan dalam format Y-m-d
                'flagproses' => 'PROSES',
                'keterangan' => 'Setting Tanggal Awal Transaksi',
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ];
            
            $builderTglAwal->insert($dataInsert);
            
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data');
            }
            
            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Tanggal awal berhasil disimpan',
                // 'tglawal'  => $dt->format('d-m-Y')
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }



    public function saldoawalhp()
    {
        $data['title']="Saldo Awal Hutang/Piutang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.2'; $versirelease='I.T.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.A.2'";
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
        $dtl = $this->m_tools->q_saldoawalhp_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('tools/settingawal/clearEntrySAHP');
            $urlnext = base_url('tools/settingawal/addSAHP');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.T.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('tools/settingawal/v_list_sahp',$data);
    }

    function detailSAHP()
    {
        /* Penambahan Squence */
        $data['title']="Detail SAHP";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('tools/settingawal/saldoawalhp'));
        }
        $kodemenu='I.T.A.2'; $versirelease='I.T.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.A.2'";
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
        $data['dtldata'] = $this->m_tools->q_saldoawalhp_master($param)->getRowArray();
        return $this->template->render('tools/settingawal/v_detail_sahp',$data);
    }

    function list_saldoawalhp(){
        $list = $this->m_tools->get_t_front_saldoawalhp_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.T.A.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_resaldoawalhprt']) && trim($datadtl['dtl_akses']['a_resaldoawalhprt']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            // $printBtn  = '';
            // $approveBtn  = '';
            // $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate) {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('tools/settingawal/updateSAHP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This SAHP : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update SAHP 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('tools/settingawal/detailSAHP') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail SAHP : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail SAHP 
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
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamatsupplier;
            $row[] = $lm->nmkota;
            $row[] = $lm->docnohp;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTemsaldoawalhp = $date->format('d/m/Y');

            } else {
                $jatuhTemsaldoawalhp = '';
            }

            $row[] = $jatuhTemsaldoawalhp;
            $row[] = $lm->currcode;
            $row[] = $lm->idtax;
            $row[] = $lm->jnshp;
            // $row[] = $lm->nilai;
            $row[] = '<div class="ratakanan">'. number_format($lm->total, 2, '.', ',') . '</div>';
            $row[] = $lm->keterangan;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_tools->t_front_saldoawalhp_view_count_all(),
            "recordsFiltered" => $this->m_tools->t_front_saldoawalhp_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    
    function clearEntrySAHP()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_tools->q_saldoawalhp_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('tools/settingawal/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.sahp');
        // $builder_dtl = $this->db->table('sc_tmp.sahp_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('tools/settingawal/saldoawalhp'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('tools/settingawal/saldoawalhp'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('tools/settingawal/saldoawalhp'));
        }

    }

    function addSAHP()
    {
        /* Penambahan Squence */
        $data['title']="Input Saldo Awal Hutang / Piutang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.2'; $versirelease='I.T.A.2/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.T.A.2'";
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
        $data['mst'] = $this->m_tools->q_saldoawalhp_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));


        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_tools->q_saldoawalhp_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('tools/settingawal/v_add_sahp',$data);
    }


   public function getBranchInfoSAHP()
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

    public function getNextSuffixSAHP()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.sahp')
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

    public function initSAHPHeader()
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

        $builder = $this->db->table('sc_tmp.sahp');
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



    public function saveSAHPDetail()
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
        $builderHeader = $db->table('sc_tmp.sahp');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari SAHPST
        
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

        $builderDetail = $db->table('sc_tmp.sahp_dtl');
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
            $descriptionsaldoawalhp = strtoupper($this->request->getPost('descriptionsaldoawalhp'));


            // Ambil kurs dari header SAHP
            $saldoawalhpHeader = $builderHeader->select('kurs, idtax')->where('docno', $docno)->get()->getRowArray();
            $kurs = $saldoawalhpHeader['kurs'] ?? 0;
            $idtax = $saldoawalhpHeader['idtax'] ?? '';
            
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
                'descriptionsaldoawalhp' => $descriptionsaldoawalhp,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);
            // $builderDetail->where('uniqueid', $uniqueid)->update([
            //     'qty'          => $qty,
            //     'qtybonus'     => $qtybonus,
            //     'harga'        => $harga,
            //     'multidisc'    => $multidisc,
            //     'nilai'        => $nilai,
            //     'descriptionsaldoawalhp' => $descriptionsaldoawalhp,
            //     'updateby'     => $nama,
            //     'updatedate'   => date('Y-m-d H:i:s')
            // ]);



            $saldoawalhpHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
            $idtax = $saldoawalhpHeader['idtax'] ?? '';
            
            // Hitung total DPP (sum nilai dari saldoawalhp_dtl)
            $builderTotalDpp = $db->table('sc_tmp.sahp_dtl');
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
            
            // Update header SAHP
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
                    qtysaldoawalhp,
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

                $sisaQty = $row->qty - ($row->qtysaldoawalhp + $row->qtyvoid);
                
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
                        'descriptionsaldoawalhp' => $row->description,
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


    public function updateStatusSAHP()
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
        $builder = $db->table('sc_trx.sahp');
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



    function updateSAHP()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_tools->q_saldoawalhp_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.sahp');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('tools/settingawal/addSAHP'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('tools/settingawal/saldoawalhp'));
        }
    }

    function showing_saldoawalhptrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_tools->q_saldoawalhp_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_saldoawalhptemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_tools->q_saldoawalhp_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_saldoawalhp_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_tools->q_saldoawalhp_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_saldoawalhp_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.sahp_dtl')
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

    public function delete_saldoawalhp_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.sahp_dtl');

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
                'message' => 'Data SAHP Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function list_tmp_saldoawalhp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari SAHPST
        $list = $this->m_tools->get_t_saldoawalhp_dtl_temp_view($docno);
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
            $row[] = $lm->descriptionsaldoawalhp;
            $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_tools->t_saldoawalhp_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_tools->t_saldoawalhp_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_saldoawalhp_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari SAHPST
        $list = $this->m_tools->get_t_saldoawalhp_dtl_view($docno);
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
            $row[] = $lm->descriptionsaldoawalhp;
            $row[] = $lm->descriptionpp;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_tools->t_saldoawalhp_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_tools->t_saldoawalhp_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntrySAHP(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        // $param = " and coalesce(inputby,'')='$nama'";
        $docno  = strtoupper(trim($this->request->getPost('docno')));

        // $header = $this->m_tools->q_saldoawalhp_master_temp($param);
        // $status = trim($header->getRowArray()['status']);
        // $cek = $this->m_tools->q_saldoawalhp_dtl_temp($paramdtl);
        // $cek2 = $this->m_tools->q_saldoawalhp_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.sahp');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.T.A.2');
        $builder_trxerror->delete();


        // if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        if (empty($docno))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.T.A.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/tools/settingawal/addSAHP'));
        } else {
            $db = $this->db;
            $db->transStart();
            $builderHeader = $db->table('sc_tmp.sahp');

            $cekData = $builderHeader
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            $ispajak = strtoupper(trim(
                $this->request->getPost('ispajak') 
                ?? $dataprocess->ispajak 
                ?? 'NO'
            ));

            $ispajak = ($ispajak === 'YES') ? 'YES' : 'NO';

            $kurs = trim($this->request->getPost('kurs'));
            $kurs_clean = 0;
            if (!empty($kurs)) {
                $kurs_clean = str_replace(',', '', $kurs);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }

            $dpp = trim($this->request->getPost('dpp'));
            $dpp_clean = 0;
            if (!empty($dpp)) {
                $dpp_clean = str_replace(',', '', $dpp);
                // $dpp_clean = str_replace('.', '.', $dpp_clean);
                // $dpp_clean = floatval($dpp_clean);
            }

            $total = trim($this->request->getPost('total'));
            $total_clean = 0;
            if (!empty($total)) {
                $total_clean = str_replace(',', '', $total);
                // $total_clean = str_replace('.', '.', $total_clean);
                // $total_clean = floatval($total_clean);
            }

            $docdate   = trim($this->request->getPost('docdate'));
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            $hpdate   = trim($this->request->getPost('hpdate'));
            $hpdateph = null;
            if (!empty($hpdate)) {
                $hpdateph = date('Y-m-d', strtotime(str_replace('-', '/', $hpdate)));
            }

            $data = [
                'docno'     => $docno,
                'docnohp'     => strtoupper($this->request->getPost('docnohp')),
                'cabang'    => $this->request->getPost('cabang'),
                'docdate'   => $docdateph,
                'hpdate'   => $hpdateph,
                // 'senddate'  => date('Y-m-d', strtotime(trim($this->request->getPost('senddate')))),
                'jthtempo'  => $this->request->getPost('jthtempo'),
                'ispajak'   => $ispajak,
                'kdsupplier'=> strtoupper($this->request->getPost('kdsupplier')),
                'alamatsupplier'=> strtoupper($this->request->getPost('alamatsupplier')),
                // 'alamatkirim'=> strtoupper($this->request->getPost('alamatkirim')),
                'idtax'     => strtoupper($this->request->getPost('idtax')),
                'currcode'  => strtoupper($this->request->getPost('currcode')),
                'kurs'      => $kurs_clean,
                'dpp'      => $dpp_clean,
                'total'      => $total_clean,
                'jumlahpajak'=> $total_clean - $dpp_clean,
                'nilai'     => $total_clean * $kurs_clean,
                'jnshp'     => strtoupper($this->request->getPost('jnshp')),
                // 'docnohp'   => strtoupper($this->request->getPost('docnohp')),
                'perkiraan' => strtoupper($this->request->getPost('perkiraan')),
                'perkiraanlawan'=> strtoupper($this->request->getPost('perkiraanlawan')),
                'keterangan'=> strtoupper($this->request->getPost('keterangan')),
                'status'    => 'E'
            ];

            if ($cekData) {
                $data['updateby'] = $nama;
                $data['updatedate'] = date('Y-m-d H:i:s');

                $builderHeader
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update($data);

                $db->transComplete(); 

            } else {
                $data['inputby'] = $nama;
                $data['inputdate'] = date('Y-m-d H:i:s');

                // $builderHeader->insert($data);
                $result = $builderHeader->insert($data);

                $db->transComplete(); 

                // if (!$result) {
                //     print_r($builderHeader->error());
                //     die();
                // }
            }
            
            

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.T.A.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/tools/settingawal/saldoawalhp'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.T.A.2',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/tools/settingawal/addSAHP'));
            }



        }

    }
    
    public function finalEntrySAHP_DP()
    {
        $nama = trim($this->session->get('nama'));

        $this->db->transStart();

        /*
        ==========================
        VALIDASI DATA SAHP
        ==========================
        */

        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' 
                    AND (COALESCE(unit, '') = ''  
                    OR qty = '0.00' 
                    OR qty = '0' 
                    OR COALESCE(nmbarang, '') = '' 
                    OR COALESCE(descriptionsaldoawalhp, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_tools->q_saldoawalhp_master_temp($param)->getRowArray();
        $cek = $this->m_tools->q_saldoawalhp_dtl_temp($paramdtl);
        $cek2 = $this->m_tools->q_saldoawalhp_dtl_temp($paramdtl2);

        if(!$header){
            return redirect()->to(base_url('/tools/settingawal/addSAHP'));
        }

        $status = trim($header['status']);

        /*
        ==========================
        TRX ERROR CLEAN
        ==========================
        */

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama)
                        ->where('modul','I.T.A.2')
                        ->delete();

        if (($status === 'E' && $cek->getNumRows() > 0) || ($cek2->getNumRows() <= 0))
        {
            $builder_trxerror->insert([
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.T.A.2',
            ]);

            return redirect()->to(base_url('/tools/settingawal/addSAHP'));
        }

        /*
        ==========================
        AMBIL SAHPST DATA
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
        UPDATE HEADER TMP SAHP
        ==========================
        */

        $this->db->table('sc_tmp.sahp')
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

        $header = $this->db->table('sc_tmp.sahp')
            ->where('inputby',$nama)
            ->get()
            ->getRowArray();

        $idurutSAHP = $header['idurut'];

        $this->db->table('sc_tmp.sahp')
            ->where('inputby',$nama)
            ->update(['status'=>'F']);

        /* ambil SAHP final */

        $trxSAHP = $this->db->table('sc_trx.sahp')
            ->where('idurut',$idurutSAHP)
            ->get()
            ->getRowArray();

        $docnoSAHPFinal = $trxSAHP['docno'];

        /* ambil suffix */

        /*
        ==========================
        GENERATE DOCNO UMB
        ==========================
        */
        
        $prefix = 'UMK';
        $infix = date('ym', strtotime($this->session->get('logindate')));
        // $kodeSuffix = 'PT';
        $parts = explode('/',$docnoSAHPFinal);
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
        LINK SAHP → UMB
        ==========================
        */

        $this->db->table('sc_trx.sahp')
            ->where('docno',$docnoSAHPFinal)
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
            base_url('/tools/settingawal/addUMB')
        );
    }


    function show_saldoawalhp(){
        $module = 'SAHP';
        $table = 'sc_trx.sahp';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.sahp');

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

        $title = " Resaldoawalhprt Purchase Order";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("tools/settingawal/api_saldoawalhp/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/resaldoawalhprt_saldoawalhp.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/resaldoawalhprt_pp_non_header.mrt") ;
        // }

        return $this->fiky_resaldoawalhprt->render($datajson,$datamrt,$title,$nama,$module,$table,$docno);
    }

    function api_saldoawalhp(){
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
        $datamst = $this->m_tools->q_saldoawalhp_master($param);
        $datadtl = $this->m_tools->q_saldoawalhp_dtl($param);
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


 // ======================================= PROSES SAHP ====================================================================


     public function prosessaldoawalhp()
    {
        $data['title']="Proses Saldo Awal Hutang / Piutang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.3'; $versirelease='I.T.A.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.A.3'";
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
        
        $logindate = trim($this->session->get('logindate'));
        $kmenu = 'I.T.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        
        $builderTglAwal = $this->db->table('sc_trx.prosessahp');
        $dataTglAwal = $builderTglAwal
        ->where('flagproses', 'PROSES')
        ->orderBy('idurut', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();
    
        // Kirim data ke view
        $data['periode'] = isset($dataTglAwal['periode']) ? $dataTglAwal['periode'] : '';
        
        return $this->template->render('tools/settingawal/v_list_prosessahp',$data);
    }

    public function prosesSAHP()
    {
        $nama = trim($this->session->get('nama'));
        // $periode = trim($this->request->getPost('periode'));
        
        // Validasi input
        if (empty($periode)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Periode tidak boleh kosong'
            ]);
        }
        
        // // Validasi format tanggal (asumsi format d-m-Y)
        // $dt = DateTime::createFromFormat('d-m-Y', $periode);
        // if (!$dt) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Format tanggal tidak valid. Gunakan format DD-MM-YYYY'
        //     ]);
        // }
        
        $db = $this->db;
        $db->transStart();
        
        try {
            $builderTglAwal = $db->table('sc_trx.prosessahp');
            
            // =====================================================
            // 1. NON-AKTIFKAN DATA YANG SEDANG PROSES
            // =====================================================
            $builderTglAwal
                ->where('flagproses', 'PROSES')
                ->update([
                    'flagproses' => 'NO',
                    'updateby'   => $nama,
                    'keterangan' => 'Periode Ditutup',
                    'updatedate' => date('Y-m-d H:i:s')
                ]);
            
            
            // =====================================================
            // 3. INSERT DATA BARU DENGAN FLAG PROSES
            // =====================================================
            $dataInsert = [
                // 'periode'    => (trim($periode)), // Simpan dalam format Y-m-d
                'flagproses' => 'PROSES',
                'keterangan' => 'Periode Berjalan',
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ];
            
            $builderTglAwal->insert($dataInsert);
            
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data');
            }
            
            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Tutup Periode berhasil diproses',
                // 'tglawal'  => $dt->format('d-m-Y')
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }


    // ======================================= KONFIGURASI =====================================================================

     public function konfigurasi()
    {
        $data['title']="Konfigurasi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.1'; $versirelease='I.T.B.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.1'";
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
        
        $logindate = trim($this->session->get('logindate'));
        $kmenu = 'I.T.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        
        // $builderTglAwal = $this->db->table('sc_trx.closeperiod');
        // $dataTglAwal = $builderTglAwal
        // ->where('flagproses', 'PROSES')
        // ->orderBy('idurut', 'DESC')
        // ->limit(1)
        // ->get()
        // ->getRowArray();
        
        // Kirim data ke view
        // $data['periode'] = isset($dataTglAwal['periode']) ? $dataTglAwal['periode'] : '';

        $dt = \DateTime::createFromFormat('d-m-Y', $logindate);
        $periode = $dt ? $dt->format('ym') : '';
        $builder = $this->db->table('sc_trx.closeperiod');

        $current = $builder
            ->where('TRIM(periode)', $periode)
            ->orderBy('idurut', 'DESC')
            ->get()
            ->getRowArray();

        $btnLabel = 'Close Period';

        if ($current && trim($current['flagproses']) == 'TUTUP') {
            $btnLabel = 'Open Period';
        }

        $data['periode'] = $periode;
        $data['logindate'] = $logindate;
        $data['btnLabel'] = $btnLabel;
        return $this->template->render('tools/konfigurasi/v_list_konfigurasi',$data);
    }

    public function updateKonfigurasi()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        
        try {
            $nama = trim($this->session->get('nama'));
            
            // Ambil data dari request (POST)
            // Data dikirim sebagai JSON atau form-data
            $rawInput = file_get_contents('php://input');
            $jsonData = json_decode($rawInput, true);
            
            if ($jsonData) {
                // Jika data dikirim sebagai JSON
                $request = $jsonData;
            } else {
                // Jika data dikirim sebagai form-data biasa
                $request = $this->request->getPost();
            }
            
            // Siapkan data untuk update
            $updateData = [];
            
            // Mapping field dari request ke database
            // PEMBELIAN
            $updateData['pp'] = strtoupper($request['pp']) ?? null;
            $updateData['voidpp'] = strtoupper($request['voidpp']) ?? null;
            $updateData['po'] = strtoupper($request['po']) ?? null;
            $updateData['voidpo'] = strtoupper($request['voidpo']) ?? null;
            $updateData['lpb'] = strtoupper($request['lpb']) ?? null;
            $updateData['returbeli'] = strtoupper($request['returbeli']) ?? null;
            $updateData['refundbeli'] = strtoupper($request['refundbeli']) ?? null;
            
            // PENJUALAN
            $updateData['salesorder'] = strtoupper($request['salesorder']) ?? null;
            $updateData['voidso'] = strtoupper($request['voidso']) ?? null;
            $updateData['deliveryorder'] = strtoupper($request['deliveryorder']) ?? null;
            $updateData['suratjalan'] = strtoupper($request['suratjalan']) ?? null;
            $updateData['penjualan'] = strtoupper($request['penjualan']) ?? null;
            $updateData['penjualannon'] = strtoupper($request['penjualannon']) ?? null;
            $updateData['returpenjualan'] = strtoupper($request['returpenjualan']) ?? null;
            $updateData['retursj'] = strtoupper($request['retursj']) ?? null;
            $updateData['refundjual'] = strtoupper($request['refundjual']) ?? null;
            
            // PRODUKSI
            $updateData['workorder'] = strtoupper($request['workorder']) ?? null;
            $updateData['workorderexecution'] = strtoupper($request['workorderexecution']) ?? null;
            $updateData['materialrelease'] = strtoupper($request['materialrelease']) ?? null;
            $updateData['bpnm'] = strtoupper($request['bpnm']) ?? null;
            $updateData['penerimaanbarangprod'] = strtoupper($request['penerimaanbarangprod']) ?? null;
            $updateData['setorantarbagian'] = strtoupper($request['setorantarbagian']) ?? null;
            $updateData['pmkbarang'] = strtoupper($request['pmkbarang']) ?? null;
            $updateData['pnmbarang'] = strtoupper($request['pnmbarang']) ?? null;
            
            // KAS / BANK
            $updateData['kasmasuk'] = strtoupper($request['kasmasuk']) ?? null;
            $updateData['kaskeluar'] = strtoupper($request['kaskeluar']) ?? null;
            $updateData['bankmasuk'] = strtoupper($request['bankmasuk']) ?? null;
            $updateData['bankkeluar'] = strtoupper($request['bankkeluar']) ?? null;
            $updateData['setorangiro'] = strtoupper($request['setorangiro']) ?? null;
            $updateData['pencairangiro'] = strtoupper($request['pencairangiro']) ?? null;
            $updateData['tolakangiro'] = strtoupper($request['tolakangiro']) ?? null;
            $updateData['buktikaskecil'] = strtoupper($request['buktikaskecil']) ?? null;
            
            // FAKTUR PAJAK
            $updateData['fpm'] = strtoupper($request['fpm']) ?? null;
            $updateData['fpk'] = strtoupper($request['fpk']) ?? null;
            $updateData['bppph'] = strtoupper($request['bppph']) ?? null;
            
            // LAIN-LAIN
            $updateData['notadk'] = strtoupper($request['notadk']) ?? null;
            $updateData['jurnalumump'] = strtoupper($request['jurnalumump']) ?? null;
            $updateData['ptal'] = strtoupper($request['ptal']) ?? null;
            $updateData['koreksihargajual'] = strtoupper($request['koreksihargajual']) ?? null;
            $updateData['adjusmentstock'] = strtoupper($request['adjusmentstock']) ?? null;
            
            // PERKIRAAN (tab perkiraan)
            $updateData['hpp'] = strtoupper($request['hpp']) ?? null;
            $updateData['labakurs'] = strtoupper($request['labakurs']) ?? null;
            $updateData['rugikurs'] = strtoupper($request['rugikurs']) ?? null;
            $updateData['ldtb'] = strtoupper($request['ldtb']) ?? null;
            $updateData['ldtl'] = strtoupper($request['ldtl']) ?? null;
            $updateData['pproduksi'] = strtoupper($request['pproduksi']) ?? null;
            
            // DEFAULT (tab default)
            $updateData['idtax'] = strtoupper($request['idtax']) ?? null;
            $updateData['currcode'] = strtoupper($request['currcode']) ?? null;
            $updateData['gudang'] = strtoupper($request['gudang']) ?? null;
            $updateData['kaskecil'] = strtoupper($request['kaskecil']) ?? null;
            $updateData['pkas'] = strtoupper($request['pkas']) ?? null;
            $updateData['ppersediaan'] = strtoupper($request['ppersediaan']) ?? null;
            $updateData['psj'] = strtoupper($request['psj']) ?? null;
            $updateData['pselisih'] = strtoupper($request['pselisih']) ?? null;
            $updateData['gudangretail'] = strtoupper($request['gudangretail']) ?? null;
            $updateData['pmutasimasuk'] = strtoupper($request['pmutasimasuk']) ?? null;
            $updateData['pmutasikeluar'] = strtoupper($request['pmutasikeluar']) ?? null;
            $updateData['prefixnofp'] = strtoupper($request['prefixnofp']) ?? null;
            $updateData['ispajak'] = strtoupper(trim(
                $request['ispajak'] 
                ?? $dataprocess->ispajak 
                ?? 'NO'
            ));

            $updateData['sembunyilokasi'] = strtoupper(trim(
                $request['sembunyilokasi'] 
                ?? $dataprocess->sembunyilokasi 
                ?? 'NO'
            ));
            
            // Audit fields
            $updateData['updateby'] = $nama;
            $updateData['updatedate'] = date('Y-m-d H:i:s');
            
            // Hapus field yang nilainya null atau empty string (opsional)
            // Biarkan saja karena ingin update semua field
            
            // Cek apakah data sudah ada di tabel (hanya 1 row dengan id=1)
            $builder = $db->table('sc_mst.konfigurasi_umum');
            $existingData = $builder->where('id', 1)->get()->getRow();
            
            if ($existingData) {
                // Update existing data
                $builder->where('id', 1);
                $builder->update($updateData);
            } else {
                // Insert new data (pertama kali)
                $updateData['id'] = 1;
                $builder->insert($updateData);
            }
            
            $db->transCommit();
            
            // Ambil data terbaru setelah update
            $updatedConfig = $db->table('sc_mst.konfigurasi_umum')
                                ->where('id', 1)
                                ->get()
                                ->getRowArray();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Konfigurasi berhasil disimpan',
                'data' => $updatedConfig
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    function showing_konfigurasimst(){
        $nama=trim($this->session->get('nama'));
        // $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and id=1";
        $data = $this->m_tools->q_konfigurasi_umum($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    // ======================================= CLOSE PERIOD ====================================================================


     public function blockunblockperiod()
    {
        $data['title']="Block/Unblock Period";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.3'; $versirelease='I.T.B.3/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.3'";
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
        
        $logindate = trim($this->session->get('logindate'));
        $kmenu = 'I.T.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        
        // $builderTglAwal = $this->db->table('sc_trx.closeperiod');
        // $dataTglAwal = $builderTglAwal
        // ->where('flagproses', 'PROSES')
        // ->orderBy('idurut', 'DESC')
        // ->limit(1)
        // ->get()
        // ->getRowArray();
        
        // Kirim data ke view
        // $data['periode'] = isset($dataTglAwal['periode']) ? $dataTglAwal['periode'] : '';

        $dt = \DateTime::createFromFormat('d-m-Y', $logindate);
        $periode = $dt ? $dt->format('ym') : '';
        $builder = $this->db->table('sc_trx.closeperiod');

        $current = $builder
            ->where('TRIM(periode)', $periode)
            ->orderBy('idurut', 'DESC')
            ->get()
            ->getRowArray();

        $btnLabel = 'Close Period';

        if ($current && trim($current['flagproses']) == 'TUTUP') {
            $btnLabel = 'Open Period';
        }

        $data['periode'] = $periode;
        $data['logindate'] = $logindate;
        $data['btnLabel'] = $btnLabel;
        return $this->template->render('tools/konfigurasi/v_list_bup',$data);
    }

    public function processClosePeriod()
    {
        $nama = trim($this->session->get('nama'));
        $logindate = trim($this->session->get('logindate'));

        $dt = \DateTime::createFromFormat('d-m-Y', $logindate);
        $periode = $dt ? $dt->format('ym') : '';

        if (empty($periode)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Periode tidak valid'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        try {

            $builder = $db->table('sc_trx.closeperiod');

            // CEK DATA PERIODE
            $existing = $builder
                ->where('TRIM(periode)', $periode)
                ->orderBy('idurut', 'DESC')
                ->get()
                ->getRowArray();

            if ($existing) {

                // =========================
                // TOGGLE STATUS
                // =========================
                if (trim($existing['flagproses']) == 'TUTUP') {

                    // OPEN
                    $builder->where('idurut', $existing['idurut'])->update([
                        'flagproses' => 'OPEN',
                        'keterangan' => 'Periode Dibuka',
                        'updateby' => $nama,
                        'updatedate' => date('Y-m-d H:i:s')
                    ]);

                    $message = 'Periode berhasil dibuka';

                } else {

                    // TUTUP
                    $builder->where('idurut', $existing['idurut'])->update([
                        'flagproses' => 'TUTUP',
                        'keterangan' => 'Periode Ditutup',
                        'updateby' => $nama,
                        'updatedate' => date('Y-m-d H:i:s')
                    ]);

                    $message = 'Periode berhasil ditutup';
                }

            } else {

                // =========================
                // INSERT BARU → TUTUP
                // =========================
                $builder->insert([
                    'periode' => $periode,
                    'flagproses' => 'TUTUP',
                    'keterangan' => 'Periode Ditutup',
                    'inputby' => $nama,
                    'inputdate' => date('Y-m-d H:i:s')
                ]);

                $message = 'Periode berhasil ditutup (data baru)';
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal proses');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }








     // ======================================= PROSES TUTUP BULAN ====================================================================


     public function tutupbulan()
    {
        $data['title']="Proses Tutup Bulan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.C.1'; $versirelease='I.T.C.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.C.1'";
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
        
        $logindate = trim($this->session->get('logindate'));
        $kmenu = 'I.T.C.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        
        $builderTglAwal = $this->db->table('sc_trx.closeperiod');
        $dataTglAwal = $builderTglAwal
        ->where('flagproses', 'PROSES')
        ->orderBy('idurut', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray();
    
        // Kirim data ke view
        $data['periode'] = isset($dataTglAwal['periode']) ? $dataTglAwal['periode'] : '';
        
        return $this->template->render('tools/proses/v_list_ptb',$data);
    }

    public function processTutupBulan()
    {
        $nama = trim($this->session->get('nama'));
        // $periode = trim($this->request->getPost('periode'));
        
        // Validasi input
        // if (empty($periode)) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Periode tidak boleh kosong'
        //     ]);
        // }
        
        // // Validasi format tanggal (asumsi format d-m-Y)
        // $dt = DateTime::createFromFormat('d-m-Y', $periode);
        // if (!$dt) {
        //     return $this->response->setJSON([
        //         'success' => false,
        //         'message' => 'Format tanggal tidak valid. Gunakan format DD-MM-YYYY'
        //     ]);
        // }
        
        $db = $this->db;
        $db->transStart();
        
        try {
            $builderTglAwal = $db->table('sc_trx.closeperiod');
            
            // =====================================================
            // 1. NON-AKTIFKAN DATA YANG SEDANG PROSES
            // =====================================================
            $builderTglAwal
                ->where('flagproses', 'PROSES')
                ->update([
                    'flagproses' => 'NO',
                    'updateby'   => $nama,
                    'keterangan' => 'Periode Ditutup',
                    'updatedate' => date('Y-m-d H:i:s')
                ]);
            
            
            // =====================================================
            // 3. INSERT DATA BARU DENGAN FLAG PROSES
            // =====================================================
            $dataInsert = [
                // 'periode'    => (trim($periode)), // Simpan dalam format Y-m-d
                'flagproses' => 'PROSES',
                'keterangan' => 'Periode Berjalan',
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ];
            
            $builderTglAwal->insert($dataInsert);
            
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data');
            }
            
            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Tutup Periode berhasil diproses',
                // 'tglawal'  => $dt->format('d-m-Y')
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

}