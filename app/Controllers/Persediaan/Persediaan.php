<?php
/*
Controller Persediaan.js */

namespace App\Controllers\Persediaan;

use App\Controllers\BaseController;

class Persediaan extends BaseController
{

    public function perintah_transfer()
    {
        $data['title']="SPK Transfers Lokasi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.1'; $versirelease='I.Q.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.1'";
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
        $dtl = $this->m_persediaan->q_tmp_transfer_spk_mst($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('persediaan/trans/clearSpkTransfers');
            $urlnext = base_url('persediaan/trans/addSPKtransfers');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.Q.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('/persediaan/transfer/v_spk_transfer',$data);
    }

    function addSPKtransfers(){
        /* Penambahan Squence */
        $data['title']="Input Perintah Transfer";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.1'; $versirelease='I.Q.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.Q.A.1'";
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
        $data['mst'] = $this->m_persediaan->q_tmp_transfer_spk_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_persediaan->q_tmp_transfer_spk_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/transfer/v_add_spk_transfers',$data);
    }


    public function saveSPKTransferDetail()
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

        $builderHeader = $db->table('sc_tmp.transfer_spk_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        // =========================
        // INSERT HEADER JIKA BELUM ADA
        // =========================
        if ($exists == 0) {

//docno character(30) COLLATE pg_catalog."default" NOT NULL,
//doctype character(20) default 'SPK_TRANSFERS' ,
//docdate character(20) COLLATE pg_catalog."default",
//cabang character (30 ) COLLATE pg_catalog."default",
//pemohon character(100) COLLATE pg_catalog."default",
//estpakai character(20) COLLATE pg_catalog."default",
//idlocation_from character(30),
//idlocation_to character(30),
//idlocation_transit character(30),
//status character(6) COLLATE pg_catalog."default",
//keterangan TEXT,
//inputby character varying(50) COLLATE pg_catalog."default",
//inputdate timestamp without time zone,
//updateby character varying(50) COLLATE pg_catalog."default",
//updatedate timestamp without time zone,
//printby character varying(50) COLLATE pg_catalog."default",
//printdate timestamp without time zone,
//docnotmp character(30) COLLATE pg_catalog."default",

            $builderHeader->insert([
                'docno'      => $docno,
                'doctype'    => 'SPK_TRANSFERS',
                'docdate'    => trim($this->request->getPost('docdate')),
                'cabang'     => trim($this->request->getPost('cabang')),
                'cabang_sent'     => trim($this->request->getPost('cabang_sent')),
                'pemohon'    => strtoupper(trim($this->request->getPost('pemohon'))),
                'estpakai'   => $this->request->getPost('estpakai'),
                'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
                'idlocation_to'    => strtoupper(trim($this->request->getPost('idlocation_to'))),
                'idlocation_transit'    => strtoupper(trim($this->request->getPost('idlocation_transit'))),
                'status'     => 'E',
                'keterangan' => strtoupper(trim($this->request->getPost('keterangan'))),
                'inputby'    => $nama,
                'inputdate'  => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        // =========================
        // AMBIL DATA DETAIL
        // =========================
        $uniqueid = $this->request->getPost('iduniq');
        $idbarang    = trim($this->request->getPost('idbarang'));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $qtystock    = trim($this->request->getPost('qtystock'));
        $qty         = trim($this->request->getPost('qty'));
        $description = strtoupper(trim($this->request->getPost('description')));

        $builderDetail = $db->table('sc_tmp.transfer_spk_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('qty', $qty)
            ->where('inputby', $nama)
            ->where('description', $description);

        // jika mode update → jangan bandingkan dengan dirinya sendiri
        if ($idurut) {
            $builderDuplicate->where('iduniq !=', $uniqueid);
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
            $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('iduniq', $uniqueid)
                ->update([
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
            $rawUnique = $idbarang
                . '|' . $docno
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
                'iduniq'    => $uniqueid
            ]);
        }

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload
        ]);
    }

    public function getBranchInfoSPKTransfer()
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
        $prefix = trim($konfigurasiUmum[0]['ptal']) ?? '';
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

    public function getNextSuffixSPKTransfer()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.transfer_spk_mst')
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

    function showing_spk_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby='$nama'";
        $data = $this->m_persediaan->q_tmp_transfer_spk_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_tmp_spk_transfer_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_tmp_transfer_spk_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    // UNTUK TRX
    function showing_spk_mst_trx(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_persediaan->q_trx_transfer_spk_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_trx_spk_transfer_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_trx_transfer_spk_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



    function list_tmp_spk_transfers_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_tmp_transfer_spk_dtl_view($docno);
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
            "recordsTotal" => $this->m_persediaan->tmp_transfer_spk_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_persediaan->tmp_transfer_spk_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearSpkTransfers()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_persediaan->q_tmp_transfer_spk_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('persediaan/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.transfer_spk_mst');
        $builder_dtl = $this->db->table('sc_tmp.transfer_spk_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }

    }

    public function deleteSPKTransferDetail()
    {
        $ids = $this->request->getPost('ids');

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_tmp.transfer_spk_dtl');

        try {

            $db->transBegin(); // START TRANSACTION

            $builder->whereIn('idurut', $ids)->delete();

            // Cek apakah semua benar-benar terhapus
            if ($db->affectedRows() !== count($ids)) {
                throw new \Exception('Sebagian data gagal dihapus');
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            $db->transCommit(); // COMMIT jika sukses semua

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semua data berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback(); // ROLLBACK jika ada error

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }


    function finalSpkTransfers(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_persediaan->q_tmp_transfer_spk_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_persediaan->q_tmp_transfer_spk_dtl($paramdtl);
        $cek2 = $this->m_persediaan->q_tmp_transfer_spk_dtl($paramdtl2);


        $builder = $this->db->table(' sc_tmp.transfer_spk_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.Q.A.1');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.Q.A.1',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/addSPKtransfers'));
        } else {
            // Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
//

            // Update data header dulu sebelum set status F
            $updateHeader = [
//                'docdate'      => $docdateph,
//                'pemohon'       => $pemohon,
                'keterangan'        => $keterangan,
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
                $paramerror=" and userid='$nama' and modul='I.Q.A.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/persediaan/trans/perintah_transfer'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.Q.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/persediaan/trans/addSPKtransfers'));
            }



        }

    }

    /*TRXXXXXXXXXXXXXXXXXXXXXXXX*/


    function list_trx_spk_transfers_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_trx_transfer_spk_dtl_view($docno);
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
            "recordsTotal" => $this->m_persediaan->trx_transfer_spk_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_persediaan->trx_transfer_spk_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_spk_transfers(){
        $list = $this->m_persediaan->get_t_trx_transfer_spk_mst_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.Q.A.1';
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

            $status    = strtoupper(trim($lm->status));
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
                    href="' . base_url('persediaan/trans/updateSPKTransfers') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This SPK Transfers : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('persediaan/trans/detailSpkTransfers') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail SPK : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('persediaan/trans/show_pp') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print SPK : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Print 
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
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->idlocation_from;
            $row[] = $lm->idlocation_to;
            $row[] = $lm->idlocation_transit;
            $row[] = $lm->keterangan;
            $row[] = $lm->nmstatus;


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->t_trx_transfer_spk_mst_view_count_all(),
            "recordsFiltered" => $this->m_persediaan->t_trx_transfer_spk_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function updateSPKTransfers()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_transfer_spk_mst($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }

        if ($status === 'F' || $status === 'P') {

            $info = array(
                'status' => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama,
            );
            $builder = $this->db->table('sc_trx.transfer_spk_mst');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('persediaan/trans/addSPKtransfers'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }
    }
    function detailSpkTransfers()
    {
        /* Penambahan Squence */
        $data['title']="Detail SPK Transfer";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }
        $kodemenu='I.Q.A.1'; $versirelease='I.Q.A.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.1'";
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
        $data['dtldata'] = $this->m_persediaan->q_trx_transfer_spk_mst($param)->getRowArray();
        return $this->template->render('persediaan/transfer/v_detail_spk_transfers',$data);
    }


    /* TRANSFER LOKASI +++++++++++++++++++++++++++++++++++++++++++++++++*/
    function transfer_lokasi()
    {
        //I.Q.A.2
        $data['title']="Transfer Antar Lokasi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.2'; $versirelease='I.Q.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.2'";
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
        $dtl = $this->m_persediaan->q_tmp_transfer_location_mst($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('persediaan/trans/clearTransferLokasi');
            $urlnext = base_url('persediaan/trans/addTransferLokasi');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.Q.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/transferlocation/v_transfer_location',$data);


    }

    function list_trx_transfer_location(){
        $list = $this->m_persediaan->get_trx_transfer_location_mst_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.Q.A.1';
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

            $status    = strtoupper(trim($lm->status));
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
                    href="' . base_url('persediaan/trans/updateTransfersLocation') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update Transfers Location : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('persediaan/trans/detailTransfersLocation') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Transfer Location : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('persediaan/trans/showPrint') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Transfer Lokasi : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Print 
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
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $row[] = $lm->idlocation_from;
            $row[] = $lm->idlocation_to;
//            $row[] = $lm->idlocation_transit;
            $row[] = $lm->description;

            // =========================
            // STATUS BADGE
            // =========================
            $nmstatus   = trim((string) ($lm->nmstatus ?? ''));
            $statusText = $nmstatus !== '' ? $nmstatus : $status;
            $statusUp   = strtoupper($statusText);

            if (in_array($status, array('C', 'X'), true) || strpos($statusUp, 'CANCEL') !== false
                || strpos($statusUp, 'BATAL') !== false || strpos($statusUp, 'DITARIK') !== false) {
                $badgeClass = 'bg-danger';
            } elseif ($status === 'P' || strpos($statusUp, 'CETAK') !== false || strpos($statusUp, 'PRINT') !== false) {
                $badgeClass = 'bg-success';
            } elseif ($status === 'F' || strpos($statusUp, 'FINAL') !== false || strpos($statusUp, 'APPROV') !== false) {
                $badgeClass = 'bg-info';
            } elseif ($status === 'E' || strpos($statusUp, 'REVIS') !== false || strpos($statusUp, 'EDIT') !== false) {
                $badgeClass = 'bg-warning text-dark';
            } else {
                // 'I' / INPUT / OPEN / DRAFT / lainnya
                $badgeClass = 'bg-secondary';
            }

            $row[] = '<div class="text-center"><span class="badge badge-status ' . $badgeClass . '">'
                . htmlspecialchars($statusText) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->trx_transfer_location_mst_view_count_all(),
            "recordsFiltered" => $this->m_persediaan->trx_transfer_location_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function addTransferLokasi(){
        /* Penambahan Squence */
        $data['title']="Add Transfer Lokasi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.2'; $versirelease='I.Q.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.Q.A.2'";
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
        $data['mst'] = $this->m_persediaan->q_tmp_transfer_location_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_persediaan->q_tmp_transfer_location_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/transferlocation/v_add_transfer_location',$data);
    }


    public function saveTransferLocationDetail()
    {
        $nama  = trim($this->session->get('nama'));
        $docno = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        // ================================================
        // HEADER — check & insert jika belum ada
        // ================================================
        $builderHeader = $db->table('sc_tmp.transfer_location_mst');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        if ($exists == 0) {
            $builderHeader->insert([
                'docno'         => $docno,
                'doctype'       => 'TRFWHS',
                'docref'        => trim($this->request->getPost('docref')),
                'docdate'       => trim($this->request->getPost('docdate')),
                'cabang'        => trim($this->request->getPost('cabang')),
                'cabang_sent'   => trim($this->request->getPost('cabang_sent')),
                'pemohon'       => strtoupper(trim($this->request->getPost('pemohon'))),
                'estpakai'      => $this->request->getPost('estpakai'),
                'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
                'idlocation_to'      => strtoupper(trim($this->request->getPost('idlocation_to'))),
                // idlocation_transit tidak digunakan pada form input (field disembunyikan)
                'idlocation_transit' => strtoupper(trim((string) $this->request->getPost('idlocation_transit'))),
                'status'        => 'E',
                'description'   => strtoupper(trim($this->request->getPost('description'))),
                'inputby'       => $nama,
                'inputdate'     => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        } else {

            // ================================================
            // UPDATE HEADER (DRAFT) SAAT SIMPAN DETAIL
            // Hanya kolom yang benar-benar dikirim form yang di-update,
            // supaya keterangan/pemohon/tujuan tidak hilang saat simpan item.
            // ================================================
            $headerUpdate = [];

            if ($this->request->getPost('description') !== null) {
                $headerUpdate['description'] = strtoupper(trim((string) $this->request->getPost('description')));
            }
            if ($this->request->getPost('pemohon') !== null) {
                $headerUpdate['pemohon'] = strtoupper(trim((string) $this->request->getPost('pemohon')));
            }
            if ($this->request->getPost('docref') !== null) {
                $headerUpdate['docref'] = trim((string) $this->request->getPost('docref'));
            }
            if ($this->request->getPost('docdate') !== null) {
                $headerUpdate['docdate'] = trim((string) $this->request->getPost('docdate'));
            }
            if ($this->request->getPost('cabang') !== null) {
                $headerUpdate['cabang'] = trim((string) $this->request->getPost('cabang'));
            }
            if ($this->request->getPost('cabang_sent') !== null) {
                $headerUpdate['cabang_sent'] = trim((string) $this->request->getPost('cabang_sent'));
            }

            // location hanya di-update bila terisi (jangan mengosongkan header)
            $locFrom = strtoupper(trim((string) $this->request->getPost('idlocation_from')));
            if ($locFrom !== '') {
                $headerUpdate['idlocation_from'] = $locFrom;
            }

            $locTo = strtoupper(trim((string) $this->request->getPost('idlocation_to')));
            if ($locTo !== '') {
                $headerUpdate['idlocation_to'] = $locTo;
            }

            $locTransit = strtoupper(trim((string) $this->request->getPost('idlocation_transit')));
            if ($locTransit !== '') {
                $headerUpdate['idlocation_transit'] = $locTransit;
            }

            if (!empty($headerUpdate)) {
                $headerUpdate['updateby']   = $nama;
                $headerUpdate['updatedate'] = date('Y-m-d H:i:s');

                $updateHeader = $db->table('sc_tmp.transfer_location_mst')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update($headerUpdate);

                if ($updateHeader === false) {
                    $error = $db->error();
                    $db->transRollback();

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $error['message']
                    ]);
                }
            }
        }

        // ================================================
        // DETAIL — AMBIL SEMUA FIELD
        // ================================================
        $uniqueid          = $this->request->getPost('iduniq');
        $idbarang          = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang          = strtoupper(trim($this->request->getPost('nmbarang')));
        $batch             = strtoupper(trim($this->request->getPost('batch') ?? ''));
        $lotno             = strtoupper(trim($this->request->getPost('lotno') ?? ''));

        $unit              = strtoupper(trim($this->request->getPost('unit')));
        $unit_default      = strtoupper(trim($this->request->getPost('unit_default') ?? $unit));
        $conversion_factor = (float) ($this->request->getPost('conversion_factor') ?? 1);

        if ($conversion_factor <= 0) {
            $conversion_factor = 1;
        }

        $qty               = (float) $this->request->getPost('qty');
        $qtystock          = (float) $this->request->getPost('qtystock');
        $val               = (float) ($this->request->getPost('val') ?? 0);

        // hitung qty_base = qty * conversion_factor
        $qty_base = $qty * $conversion_factor;

        $description = strtoupper(trim($this->request->getPost('description')));

        // ================================================
        // VALIDASI DASAR
        // ================================================
        if (!$idbarang) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        if ($qty <= 0) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Qty harus lebih dari 0'
            ]);
        }

        // ================================================
        // 🔥 BLOCKING: QTY > STOCK TIDAK BOLEH
        // ================================================
        if ($qtystock <= 0) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stock tidak tersedia untuk item ini'
            ]);
        }

        if ($qty_base > $qtystock) {
            $db->transRollback();

            $qtyStockDisplay = number_format($qtystock, 2, ',', '.');
            $qtyBaseDisplay  = number_format($qty_base, 2, ',', '.');

            return $this->response->setJSON([
                'success' => false,
                'message' =>
                    'Qty melebihi stock yang tersedia. ' .
                    'Qty (dalam satuan dasar) = ' . $qtyBaseDisplay .
                    ', Stock tersedia = ' . $qtyStockDisplay .
                    ' ' . $unit_default
            ]);
        }

        // ================================================
        // INSERT / UPDATE
        // ================================================
        $builderDetail = $db->table('sc_tmp.transfer_location_dtl');

        $dataDetail = [
            'docno'             => $docno,
            'doctype'           => 'TRFWHS',
            'idbarang'          => $idbarang,
            'nmbarang'          => $nmbarang,
            'unit'              => $unit,
            'unit_default'      => $unit_default,
            'conversion_factor' => $conversion_factor,
            'qty'               => $qty,
            'qty_base'          => $qty_base,
            'qtystock'          => $qtystock,
            'batch'             => $batch,
            'lotno'             => $lotno,
            'val'               => $val,
            'description'       => $description,
        ];

        if ($idurut) {
            // ================================================
            // UPDATE
            // ================================================
            $dataDetail['updateby']   = $nama;
            $dataDetail['updatedate'] = date('Y-m-d H:i:s');

            $updated = $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('iduniq', $uniqueid)
                ->update($dataDetail);

            if (!$updated) {
                $error = $db->error();
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $error['message']
                ]);
            }

        } else {
            // ================================================
            // INSERT
            // ================================================
            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $idbarang . '|' . $batch . '|' . $lotno . '|' . $docno . '|' . $inputdate;
            $uniqueid  = hash('sha256', $rawUnique);

            $dataDetail['inputby']   = $nama;
            $dataDetail['inputdate'] = $inputdate;
            $dataDetail['iduniq']    = $uniqueid;

            $inserted = $builderDetail->insert($dataDetail);

            if (!$inserted) {
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
    function showing_transfer_location_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby='$nama'";
        $data = $this->m_persediaan->q_tmp_transfer_location_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_tmp_transfer_location_dtl()
    {
        $id = $this->request->getGet('id');
        $inputby = trim($this->session->get('nama'));
        $data = $this->m_persediaan
            ->q_tmp_transfer_location_dtl(" and iduniq='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    // UNTUK TRX
    function showing_transfer_mst_trx(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_persediaan->q_trx_transfer_location_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_trx_transfer_location_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_trx_transfer_location_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function finalTransferLocation(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_persediaan->q_tmp_transfer_location_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_persediaan->q_tmp_transfer_location_mst($paramdtl);
        $cek2 = $this->m_persediaan->q_tmp_transfer_location_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.transfer_location_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.Q.A.1');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.Q.A.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/addTransferLokasi'));
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
                $paramerror=" and userid='$nama' and modul='I.Q.A.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/persediaan/trans/transfer_lokasi'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.Q.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/persediaan/trans/addTransferLokasi'));
            }



        }

    }

    public function list_tmp_transfer_location_dtl()
    {
        $docno = trim($this->request->getPost('docno'));

        try {
            $list = $this->m_persediaan
                ->get_tmp_transfer_location_dtl_view($docno);

            if (!is_array($list)) {
                $list = [];
            }
        } catch (\Throwable $e) {
            log_message('error',
                '[list_tmp_transfer_location_dtl] ' . $e->getMessage());

            echo $this->fiky_encryption->jDatatable([
                'draw'            => (int) ($_POST['draw'] ?? 0),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage(),
            ]);
            return;
        }

        $data = [];
        $no   = (int) ($_POST['start'] ?? 0);

        foreach ($list as $lm) {

            $no++;

            $qty     = (float) ($lm->qty      ?? 0);
            $qtyBase = (float) ($lm->qty_base ?? $qty);
            $unit    = trim($lm->unit         ?? '');
            $unitDef = trim($lm->unit_default ?? $unit);

            $fmtQty  = number_format($qty,     0, ',', '.');
            $fmtBase = number_format($qtyBase, 0, ',', '.');

            $row = [];

            /* 0 — iduniq (hidden) */
            $row[] = $lm->iduniq;

            /* 1 — ID Barang (kiri) */
            $row[] = htmlspecialchars($lm->idbarang ?? '');

            /* 2 — Nama Barang (kiri) */
            $row[] = htmlspecialchars($lm->nmbarang ?? '');

            /* 3 — Satuan (TENGAH) */
            $row[] = '<div class="ratatengah">'
                . htmlspecialchars($unit)
                . '</div>';

            /* 4 — Qty Input (KANAN) */
            $row[] = '<div class="ratakanan">'
                . $fmtQty . ' ' . htmlspecialchars($unit)
                . '</div>';

            /* 5 — Unit Default (TENGAH) */
            $row[] = '<div class="ratatengah">'
                . '<span class="badge-unit-default">'
                . htmlspecialchars($unitDef)
                . '</span>'
                . '</div>';

            /* 6 — Qty Base (KANAN) */
            $row[] = '<div class="ratakanan qty-base">'
                . $fmtBase . ' ' . htmlspecialchars($unitDef)
                . '</div>';

            /* 7 — Keterangan (kiri) */
            $row[] = htmlspecialchars($lm->description ?? '');

            $data[] = $row;
        }

        $recordsTotal    = (int) $this->m_persediaan
            ->tmp_transfer_location_dtl_view_count_all($docno);
        $recordsFiltered = (int) $this->m_persediaan
            ->tmp_transfer_location_dtl_view_count_filtered($docno);

        $output = [
            "draw"            => (int) ($_POST['draw'] ?? 0),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data,
        ];

        echo $this->fiky_encryption->jDatatable($output);
    }

    function updateTransfersLocation()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_transfer_location_mst($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
        }


        if ($status === 'F' || $status === 'P') {

            $info = array(
                'status' => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama,
            );
            $builder = $this->db->table('sc_trx.transfer_location_mst');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('persediaan/trans/addTransferLokasi'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
        }
    }
    function detailTransfersLocation()
    {
        /* Penambahan Squence */
        $data['title']="Detail Transfer Lokasi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }
        $kodemenu='I.Q.A.2'; $versirelease='I.Q.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.2'";
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
        $data['dtldata'] = $this->m_persediaan->q_trx_transfer_location_mst($param)->getRowArray();
        return $this->template->render('persediaan/transfer/v_detail_transfer_location',$data);
    }

    public function getBranchInfoStockTransfers()
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
        $prefix = trim($konfigurasiUmum[0]['transferantarlokasi']) ?? '';
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

    public function getNextSuffixStockTransfers()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.transfer_location_mst')
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


    /* ============================================================
   DELETE DETAIL TRANSFER LOKASI
   Route: deleteTransferLokasiDetail
   ============================================================ */
    public function deleteTransferLokasiDetail()
    {
        $ids = $this->request->getPost('ids');

        if (!$ids || !is_array($ids) || count($ids) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid / tidak ada yang dipilih'
            ]);
        }

        /* Sanitasi */
        $cleanIds = [];
        foreach ($ids as $id) {
            $v = trim((string) $id);
            if ($v !== '') {
                $cleanIds[] = $v;
            }
        }

        if (count($cleanIds) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID tidak valid'
            ]);
        }

        /* -------------------------------------------------
           ✅ TABLE YANG BENAR: sc_tmp.transfer_location_dtl
           ------------------------------------------------- */
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.transfer_location_dtl');

        try {

            $db->transBegin();

            /* Hapus berdasarkan iduniq */
            $builder->whereIn('iduniq', $cleanIds)->delete();

            $affected = $db->affectedRows();

            if ($affected !== count($cleanIds)) {
                throw new \Exception(
                    'Sebagian data tidak ditemukan. '
                    . 'Diminta: ' . count($cleanIds)
                    . ', Terhapus: ' . $affected
                );
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success'  => true,
                'message'  => $affected . ' data berhasil dihapus',
                'affected' => $affected
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message('error',
                '[deleteTransferLokasiDetail] ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }
    /* ============================================================
   CLEAR TRANSFER LOKASI
   - Kembalikan status sc_trx.transfer_location_mst ke 'F'
     untuk dokumen milik user yang masih tertinggal di status
     'E' (mis. Edit dibatalkan sebelum finalize).
   - Hapus TMP header + detail milik user yang login
     (status apa pun: I, E, F, P, C) supaya entry yang
     menggantung benar-benar hilang dari menu transfer_lokasi.
   ============================================================ */

    function clearTransferLokasi()
    {
        $nama = trim($this->session->get('nama'));

        // -------------------------------------------------
        // Validasi session
        // -------------------------------------------------
        if (empty($nama)) {
            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
        }

        // -------------------------------------------------
        // Ambil semua data TMP header milik user
        // -------------------------------------------------
        $param = " AND COALESCE(inputby,'') = " . $this->db->escape($nama);
        $dtl   = $this->m_persediaan->q_tmp_transfer_location_mst($param);
        $rows  = $dtl ? $dtl->getResultArray() : [];

        // Kumpulkan docno milik user (untuk hapus detail)
        $docnos = [];
        foreach ($rows as $row) {
            $docno = trim((string) ($row['docno'] ?? ''));
            if ($docno !== '') {
                $docnos[] = $docno;
            }
        }

        // -------------------------------------------------
        // Koneksi DB & transaksi
        // -------------------------------------------------
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');

            // =================================================
            // KEMBALIKAN STATUS TRX KE 'F'
            // Saat user menekan Edit, updateTransfersLocation()
            // mengubah status sc_trx.transfer_location_mst
            // dari 'F'/'P' menjadi 'E'. Jika entry edit dibatalkan
            // (Clear), status dokumen harus kembali ke 'F'.
            // =================================================
            $totalReset = 0;

            // 1) Reset berdasarkan docno yang masih ada di TMP
            if (count($docnos) > 0) {
                $placeholders = implode(',', array_fill(0, count($docnos), '?'));
                $bind = array_merge([$now, $nama], $docnos);

                $db->query(
                    "UPDATE sc_trx.transfer_location_mst
                        SET status = 'F', updatedate = ?, updateby = ?
                      WHERE status = 'E'
                        AND trim(docno) IN ($placeholders)",
                    $bind
                );

                $totalReset += $db->affectedRows();
            }

            // 2) Reset sisa dokumen milik user yang masih tertinggal
            //    di status 'E' (mis. Edit lalu langsung Clear tanpa
            //    menyimpan detail sehingga TMP header tak terbentuk)
            $db->query(
                "UPDATE sc_trx.transfer_location_mst
                    SET status = 'F', updatedate = ?, updateby = ?
                  WHERE status = 'E'
                    AND updateby = ?",
                [$now, $nama, $nama]
            );

            $totalReset += $db->affectedRows();

            // =================================================
            // HAPUS TMP HEADER + DETAIL MILIK USER
            // (berlaku untuk semua status: I, E, F, P, C)
            // =================================================
            if (count($rows) > 0) {

                // 1. Hapus detail berdasarkan pemilik entry
                $db->table('sc_tmp.transfer_location_dtl')
                    ->where('inputby', $nama)
                    ->delete();

                // 1b. Hapus detail lain dengan docno yang sama (jaga-jaga)
                if (count($docnos) > 0) {
                    $db->table('sc_tmp.transfer_location_dtl')
                        ->whereIn('docno', $docnos)
                        ->delete();
                }

                // 2. Hapus header
                $db->table('sc_tmp.transfer_location_mst')
                    ->where('inputby', $nama)
                    ->delete();
            }

            // 3. Cek status transaksi
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal memproses clear Transfer Lokasi');
            }

            $db->transCommit();

            $countTmp = count($rows);
            $msg = array();
            if ($countTmp > 0) {
                $msg[] = $countTmp . ' entry TMP dihapus';
            }
            if ($totalReset > 0) {
                $msg[] = $totalReset . ' dokumen dikembalikan ke status F';
            }

            $pesan = count($msg) > 0
                ? 'Clear berhasil. ' . implode(', ', $msg) . '.'
                : 'Tidak ada entry menggantung untuk di-clear.';

            log_message('info',
                '[clearTransferLokasi] User=' . $nama .
                ' reset=' . $totalReset .
                ' clearedTmp=' . $countTmp);

            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'))
                ->with('success', $pesan);

        } catch (\Throwable $e) {

            // Rollback transaksi
            $db->transRollback();

            log_message('error',
                '[clearTransferLokasi] User=' . $nama .
                ' error=' . $e->getMessage());

            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'))
                ->with('error', 'Clear gagal: ' . $e->getMessage());
        }
    }



    /* AJUSTMENT STOCK */
    function ajustment_stock()
    {
        //I.Q.A.3
        $data['title']="Ajustment Stock";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.3'; $versirelease='I.Q.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.3'";
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
        $dtl = $this->m_persediaan->q_tmp_ajustment_stock_mst($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('persediaan/trans/clear_ajustment_stock');
            $urlnext = base_url('persediaan/trans/add_ajustment_stock_mst');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.Q.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/ajustment_stock/v_ajustment_stock',$data);


    }

    function list_trx_ajustment_stock_mst(){
        $list = $this->m_persediaan->get_trx_ajustment_stock_mst_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.Q.A.3';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint  = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView   = isset($datadtl['dtl_akses']['a_view'])   && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput  = isset($datadtl['dtl_akses']['a_input'])  && trim($datadtl['dtl_akses']['a_input']) === 't';
        $canDelete  = isset($datadtl['dtl_akses']['a_delete'])  && trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status    = strtoupper(trim($lm->status));
            $docno     = trim($lm->docno);
            $docnoHex  = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $deleteBtn = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) !== 'DITARIK PO'
            ) {

                $updateBtn = '
                    <a class="dropdown-item bg-warning" 
                    href="' . base_url('persediaan/trans/updateAjustmentStock') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update Transfers Location : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('persediaan/trans/detailAjustmentStock') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Transfer Location : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('persediaan/trans/showPrintAjustmentStock') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Transfer Lokasi : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Print 
                    </a>';
            }

            if ($canDelete) {
                $deleteBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#FF7C7CD6;" 
                    href="' . base_url('persediaan/trans/cancelAjustmentStock') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Batal Transaksi : ' . $docno . '\')">
                        <i class="fa fa-trash"></i> Cancel 
                    </a>';
            }

            // =========================
            // RULE STATUS
            // =========================
            $menuContent = '';

            if ($status === 'P' or $status === 'C') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                }

            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canDelete)   $menuContent .= $deleteBtn;
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
            $row[] = $lm->docref;
            $row[] = $lm->cabang;
            $row[] = date(
                'd-m-Y',
                strtotime(trim($lm->docdate))
            );
            $status = strtolower(trim($lm->status));
            $statusLabel = $lm->nmstatus;
            $badge = 'secondary'; // default grey

            switch ($status) {

                case 'C':
                case 'D':
                    $badge = 'danger'; // merah
                    break;

                case 'I':
                    $badge = 'default'; // abu default
                    break;

                case 'E':
                    $badge = 'primary'; // biru
                    break;

                case 'f':
                    $badge = 'success'; // hijau
                    break;

                case 'A':
                case 'A1':
                case 'A2':
                case 'A3':
                    $badge = 'primary'; // biru
                    break;

                case 'P':
                    $badge = 'success'; // hijau
                    break;
            }

            $statusBadge = '<span class="badge bg-'.$badge.' text-dark w-100" style="font-size:14px;display:block;padding:6px 8px;">'.$statusLabel.'</span>';
            $row[] = $lm->description;
            $row[] = $lm->inputby;
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->trx_ajustment_stock_mst_view_count_all(),
            "recordsFiltered" => $this->m_persediaan->trx_ajustment_stock_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    /**
     * Ambil harga persediaan (avg_cost) untuk auto-fill di adjustment stock.
     *
     * Route: GET/POST /persediaan/trans/getHargaPersediaan
     *
     * Parameter:
     *   - idbarang
     *   - idlocation
     *   - batch
     *
     * Response:
     *   { "success": true, "valqty": 12345.67, "avg_cost": 12345.67, "qty": 100 }
     */
    public function getHargaPersediaan()
    {
        $idbarang   = trim($this->request->getPost('idbarang')   ?? $this->request->getGet('idbarang')   ?? '');
        $idlocation = trim($this->request->getPost('idlocation') ?? $this->request->getGet('idlocation') ?? '');
        $batch      = trim($this->request->getPost('batch')      ?? $this->request->getGet('batch')      ?? '');

        if ($idbarang === '' || $idlocation === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'idbarang dan idlocation wajib diisi'
            ]);
        }

        // Ambil avg_cost dari stkblc_avgcost
        $row = $this->db->query("
        SELECT 
            ac.avg_cost,
            ac.qty,
            ac.total_value
        FROM sc_trx.stkblc_avgcost ac
        WHERE TRIM(ac.idbarang::TEXT)   = ?
          AND TRIM(ac.idlocation::TEXT) = ?
          AND TRIM(COALESCE(ac.batch::TEXT, '')) = ?
        LIMIT 1
    ", [$idbarang, $idlocation, $batch])->getRowArray();

        if (!$row) {
            // Fallback: ambil dari stkblc.unitcost terakhir (IN)
            $row2 = $this->db->query("
            SELECT 
                sb.unitcost AS avg_cost,
                0 AS qty,
                0 AS total_value
            FROM sc_trx.stkblc sb
            WHERE TRIM(sb.idbarang::TEXT)   = ?
              AND TRIM(sb.idlocation::TEXT) = ?
              AND TRIM(COALESCE(sb.batch::TEXT, '')) = ?
              AND sb.type_in_out = 'IN'
              AND sb.unitcost > 0
            ORDER BY sb.docdate DESC, sb.id DESC
            LIMIT 1
        ", [$idbarang, $idlocation, $batch])->getRowArray();

            if ($row2) {
                return $this->response->setJSON([
                    'success'  => true,
                    'valqty'   => (float) $row2['avg_cost'],
                    'avg_cost' => (float) $row2['avg_cost'],
                    'qty'      => 0,
                    'source'   => 'STKBLC_FALLBACK',
                ]);
            }

            return $this->response->setJSON([
                'success'  => true,
                'valqty'   => 0,
                'avg_cost' => 0,
                'qty'      => 0,
                'source'   => 'NOT_FOUND',
            ]);
        }

        return $this->response->setJSON([
            'success'  => true,
            'valqty'   => (float) $row['avg_cost'],
            'avg_cost' => (float) $row['avg_cost'],
            'qty'      => (float) $row['qty'],
            'total_value' => (float) $row['total_value'],
            'source'   => 'AVGCOST',
        ]);
    }
    function add_ajustment_stock_mst(){
        /* Penambahan Squence */
        $data['title']="Add Ajustment Stock";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.3'; $versirelease='I.Q.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.Q.A.3'";
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
        $data['mst'] = $this->m_persediaan->q_tmp_ajustment_stock_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_persediaan->q_tmp_ajustment_stock_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/ajustment_stock/v_add_ajustment_stock',$data);
    }


    public function save_ajustment_stock_detail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $idurut = $this->request->getPost('idurut');
        $iduniq = trim($this->request->getPost('iduniq'));   // ✅ tambah iduniq

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        try {

            $builderHeader = $db->table('sc_tmp.ajustment_stock_mst');

            $exists = $builderHeader
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->countAllResults();

            $reload = false;

            // ============================================================
            // INSERT HEADER JIKA BELUM ADA
            // ============================================================
            if ($exists == 0) {

                $insertHeader = $builderHeader->insert([
                    'docno'          => $docno,
                    'doctype'        => 'AJUSTMENT_STOCK',
                    'docref'         => trim($this->request->getPost('docref')),
                    'docdate'        => trim($this->request->getPost('docdate')),
                    'cabang'         => trim($this->request->getPost('cabang')),
                    // 'cabang_sent' => trim($this->request->getPost('cabang_sent')),
                    'pemohon'        => strtoupper(trim($this->request->getPost('pemohon'))),
                    // 'estpakai'    => $this->request->getPost('estpakai'),
                    // 'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
                    // 'idlocation_to'      => strtoupper(trim($this->request->getPost('idlocation_to'))),
                    // 'idlocation_transit' => strtoupper(trim($this->request->getPost('idlocation_transit'))),

                    // ✅ Tambah field yang sebelumnya hilang
                    'currency'       => strtoupper(trim($this->request->getPost('currency'))) ?: 'IDR',
                    'kurs'           => floatval($this->request->getPost('kurs')) ?: 1,
                    'isinclusive'    => strtoupper(trim($this->request->getPost('isinclusive'))) ?: 'NO',
                    'idtax'          => strtoupper(trim($this->request->getPost('idtax'))) ?: 'NON',

                    'status'         => 'E',
                    'description'    => strtoupper(trim($this->request->getPost('keterangan'))),
                    'inputby'        => $nama,
                    'inputdate'      => date('Y-m-d H:i:s'),
                ]);

                if (!$insertHeader) {
                    $error = $db->error();
                    $db->transRollback();

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $error['message'] ?? 'Gagal insert header'
                    ]);
                }

                $reload = true;
            }

            // ============================================================
            // CEK DUPLIKAT DETAIL
            // ============================================================
            $builderDetail = $db->table('sc_tmp.ajustment_stock_dtl');

            if ($iduniq) {
                // ✅ Pakai iduniq (TEXT), BUKAN idurut (BIGINT)
                $cekDtl = $builderDetail
                    ->where('docno', $docno)
                    ->where('iduniq', $iduniq)
                    ->countAllResults();

                $isUpdate = ($cekDtl > 0);
            } else {
                $isUpdate = false;
            }

            // ============================================================
            // DATA DETAIL
            // ============================================================
            $dataDtl = [
                'docno'        => $docno,
                'doctype'      => 'AJUSTMENT_STOCK',
                'docref'       => trim($this->request->getPost('docref')),
                'idbarang'     => strtoupper(trim($this->request->getPost('idbarang'))),
                'nmbarang'     => strtoupper(trim($this->request->getPost('nmbarang'))),
                'unit'         => strtoupper(trim($this->request->getPost('unit'))),
                'qtystock'     => floatval($this->request->getPost('qtystock')),
                'qty'          => floatval($this->request->getPost('qty')),
                'dk'           => strtoupper(trim($this->request->getPost('dk'))),          // D / K
                'currency'     => strtoupper(trim($this->request->getPost('currency'))) ?: 'IDR',
                'valqty'       => floatval($this->request->getPost('valqty')),               // harga satuan
                'idlocation'   => strtoupper(trim($this->request->getPost('idlocation_dtl'))), // ✅ fix mapping
                'batch'        => strtoupper(trim($this->request->getPost('batch'))),
                'description'  => strtoupper(trim($this->request->getPost('description'))),
                'val'          => floatval($this->request->getPost('val')),                  // nilai total
                'valsum'       => floatval($this->request->getPost('valsum')),               // nilai + tax
                'status'       => 'E',
                'inputby'      => $nama,
                'inputdate'    => date('Y-m-d H:i:s'),
                'iduniq'       => $iduniq ?: md5($docno . '|' . microtime(true)),
            ];

            // ============================================================
            // UPDATE atau INSERT DETAIL
            // ============================================================
            if ($isUpdate) {

                // UPDATE by iduniq
                $update = $builderDetail
                    ->where('docno', $docno)
                    ->where('iduniq', $iduniq)
                    ->update($dataDtl);

                if (!$update) {
                    $error = $db->error();
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $error['message'] ?? 'Gagal update detail'
                    ]);
                }

            } else {

                // INSERT baru
                $insertDtl = $builderDetail->insert($dataDtl);

                if (!$insertDtl) {
                    $error = $db->error();
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $error['message'] ?? 'Gagal insert detail'
                    ]);
                }
            }

            // ============================================================
            // COMMIT
            // ============================================================
            $db->transComplete();

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaksi gagal'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $isUpdate
                    ? 'Data detail berhasil diupdate'
                    : 'Data detail berhasil disimpan',
                'reload'  => $reload,
                'iduniq'  => $dataDtl['iduniq'],
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ]);
        }
    }


    function showing_ajustment_stock_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby='$nama'";
        $data = $this->m_persediaan->q_tmp_ajustment_stock_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_tmp_ajustment_stock_dtl()
    {
        $id = $this->request->getGet('id');
        $nama=trim($this->session->get('nama'));
        $data = $this->m_persediaan
            ->q_tmp_ajustment_stock_dtl(" and inputby='$nama' and iduniq='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    // UNTUK TRX
    function showing_ajustment_stock_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_persediaan->q_trx_ajustment_stock_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_trx_ajustment_stock_mst_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_trx_ajustment_stock_mst_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function final_ajustment_stock_mst(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_persediaan->q_tmp_ajustment_stock_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_persediaan->q_tmp_ajustment_stock_mst($paramdtl);
        $cek2 = $this->m_persediaan->q_tmp_ajustment_stock_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.ajustment_stock_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.Q.A.1');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.Q.A.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/addTransferLokasi'));
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
                $paramerror=" and userid='$nama' and modul='I.Q.A.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/persediaan/trans/ajustment_stock'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.Q.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/persediaan/trans/addTransferLokasi'));
            }



        }

    }

    function list_tmp_ajustment_stock_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_tmp_ajustment_stock_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->iduniq;
            //item
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idlocation;
            $row[] = $lm->dk;
            $row[] = $lm->batch;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->valqty, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->get_tmp_ajustment_stock_dtl_view($docno),
            "recordsFiltered" => $this->m_persediaan->tmp_ajustment_stock_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function updateAjustmentStock()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_ajustment_stock_mst($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {

            $info = array(
                'status' => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama,
            );
            $builder = $this->db->table('sc_trx.ajustment_stock_mst');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('persediaan/trans/add_ajustment_stock_mst'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        }
    }

    function cancelAjustmentStock()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_ajustment_stock_mst($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        }

        if ($status === 'F' or $status === 'P') {

            $info = array(
                'status' => 'C',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama,
            );
            $builder = $this->db->table('sc_trx.ajustment_stock_mst');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        }
    }

    function detailAjustmentStock()
    {
        /* Penambahan Squence */
        $data['title']="Detail Ajustment Stock";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }
        $kodemenu='I.Q.A.3'; $versirelease='I.Q.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.3'";
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
        $data['dtldata'] = $this->m_persediaan->q_trx_ajustment_stock_mst($param)->getRowArray();
        return $this->template->render('persediaan/ajustment_stock/v_detail_ajustment_stock',$data);
    }

    public function getBranch_ajustment_stock()
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
        $prefix = trim($konfigurasiUmum[0]['adjusmentstock']) ?? '';
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

    public function getNextSuffix_ajustment_stock()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.ajustment_stock_mst')
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


    function clear_ajustment_stock()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_persediaan->q_tmp_ajustment_stock_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('persediaan/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.ajustment_stock_mst');
        $builder_dtl = $this->db->table('sc_tmp.transfer_location_dtl_mst');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('persediaan/trans/ajustment_stock'));
        }

    }

    public function deleteAjustmentStockDetail()
    {
        $ids = $this->request->getPost('ids');

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ]);
        }

        $ids = array_values(array_filter(array_map('trim', $ids)));

        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_tmp.ajustment_stock_dtl');

        try {
            $db->transBegin();

            // ✅ Pakai iduniq, BUKAN idurut
            $existing = $builder
                ->select('iduniq, docno')
                ->whereIn('iduniq', $ids)
                ->get()
                ->getResultArray();

            if (empty($existing)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            $foundIds = array_column($existing, 'iduniq');

            $builder->whereIn('iduniq', $foundIds)->delete();
            $deleted = $db->affectedRows();

            if ($deleted === 0) {
                throw new \Exception('Tidak ada data yang terhapus');
            }

            $docnos = array_unique(array_column($existing, 'docno'));

            foreach ($docnos as $docno) {
                $sisa = $db->table('sc_tmp.ajustment_stock_dtl')
                    ->where('docno', $docno)
                    ->countAllResults();

                if ($sisa === 0) {
                    $db->table('sc_tmp.ajustment_stock_mst')
                        ->where('docno', $docno)
                        ->delete();
                }
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => $deleted . ' data berhasil dihapus',
                'deleted' => $deleted
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ]);
        }
    }


    function list_trx_ajustment_stock_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_trx_ajustment_stock_dtl_view($docno);
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
            $row[] = $lm->idlocation;
            $row[] = $lm->dk;
            $row[] = $lm->batch;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->valqty, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->trx_ajustment_stock_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_persediaan->trx_ajustment_stock_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function ajustment_item_value()
    {
        echo 'UNDER CONSTUCTION';
        //I.Q.A.4
    }





    /* PEMAKAIAN BARANG  */
    function pmk_brng()
    {
        //I.Q.A.5
        $data['title']="Pemakaian Barang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.5'; $versirelease='I.Q.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.5'";
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
        $dtl = $this->m_persediaan->q_tmp_pmk_brng_mst($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('persediaan/trans/clear_pmk_brng');
            $urlnext = base_url('persediaan/trans/add_pmk_brng_mst');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.Q.A.5';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/pmk_brng/v_pmk_brng',$data);


    }

    function list_trx_pmk_brng_mst(){
        $list = $this->m_persediaan->get_trx_pmk_brng_mst_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.Q.A.5';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint  = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView   = isset($datadtl['dtl_akses']['a_view'])   && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput  = isset($datadtl['dtl_akses']['a_input'])  && trim($datadtl['dtl_akses']['a_input']) === 't';

            /* ====== PERIODE CLOSING TERAKHIR (untuk disable tombol Update) ====== */
            $closingPeriode = $this->m_persediaan->q_periode_closing_last()->getRowArray();
            $closingLabel   = '' ;

            if (!empty($closingPeriode)) {
                $closingLabel = trim((string) ($closingPeriode['periode'] ?? ''));

                if ($closingLabel === '') {
                    $closingLabel = trim((string) ($closingPeriode['tanggal_akhir'] ?? ''));
                }
            }

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status    = strtoupper(trim($lm->status));
            $docno     = trim($lm->docno);
            $docnoHex  = bin2hex($docno);

            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $jurnalBtn = '';
            $clearBtn  = '';

            /* docdate transaksi masuk periode yang sudah ditutup? */
            $isClosed = $this->m_persediaan->isPeriodeClosed(trim((string) $lm->docdate), $closingPeriode);

            // =========================
            // Build button by access
            // =========================

            /* TUGAS 3: tombol Update HANYA untuk transaksi FINAL ('F' / 'P').
               Status 'E' (Entry/Draft) tidak boleh di-update (hidden).
               Dokumen lama tanpa pemohon (kosong) tetap boleh di-update
               selama user punya hak a_update. */
            $pemohon       = trim((string) $lm->pemohon);
            $isRowOwner    = ($pemohon === '' || $pemohon === $nama);

            if ($canUpdate && in_array($status, array('F', 'P'), true) && $isRowOwner && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) !== 'DITARIK '
            ) {

                if ($isClosed) {

                    /* docdate sudah masuk periode closing -> Update tidak dapat diklik */
                    $closedTitle = 'Tidak dapat diupdate, periode sudah ditutup.';

                    if ($closingLabel !== '') {
                        $closedTitle = 'Tidak dapat diupdate, periode ' . $closingLabel . ' sudah ditutup.';
                    }

                    $closedTitleEsc = htmlspecialchars($closedTitle, ENT_QUOTES, 'UTF-8');

                    $updateBtn = '
                        <span class="dropdown-item bg-warning disabled pmk-item-closed"
                              role="button"
                              aria-disabled="true"
                              title="' . $closedTitleEsc . '"
                              style="cursor:not-allowed;pointer-events:auto;opacity:.65;">
                            <i class="fa fa-edit"></i> Update
                            <small style="font-weight:700;">(Periode Tutup)</small>
                        </span>';
                } else {

                    $updateBtn = '
                        <a class="dropdown-item bg-warning"
                        href="' . base_url('persediaan/trans/updatePmkBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                        onclick="return guardPeriodeTutup(event) && confirm(\'Update Pemakaian Barang : ' . $docno . '\')">
                            <i class="fa fa-edit"></i> Update
                        </a>';
                }
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item pmk-act-detail"
                       style="background-color:#3badf6;"
                       href="' . base_url('persediaan/trans/detail_pmk_brng_mst') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">
                        <i class="fa fa-eye"></i> Detail Pemakaian Barang
                    </a>';

                /* tombol jurnal: dibuka oleh modal #modalLaporanJurnalPmk
                   (handler .pmk-act-jurnal pada pmk_brng_list.js) */
                $jurnalBtn = '
                    <a class="dropdown-item pmk-act-jurnal"
                       style="background-color:#e7f0ff;"
                       href="#" data-docno="' . htmlspecialchars($docno, ENT_QUOTES, 'UTF-8') . '">
                        <i class="fa fa-book"></i> Laporan Jurnal Transaksi
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item"
                    style="background-color:#00ff8e;"
                    href="' . base_url('persediaan/trans/showPrintPmkBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Pemakaian Barang : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Print
                    </a>';
            }

            /* TUGAS 4: tombol Clear (finalisasi E -> F) hanya untuk status 'E'.
               Konfirmasi ditampilkan sebelum proses clear dijalankan.
               URL tanpa trailing slash agar tidak terkena redirect 301. */
            if ($canUpdate && $status === 'E') {
                $clearBtn = '
                    <a class="dropdown-item pmk-act-clear"
                       style="background-color:#28a745;color:#ffffff;"
                       href="' . base_url('persediaan/trans/clear_pmk_brng') . '?docno=' . $docnoHex . '&id=' . $docnoHex . '"
                       onclick="return confirm(\'Yakin ingin mem-finalisasi transaksi ini? Data tidak dapat diubah setelahnya.\')">
                        <i class="fa fa-check-circle"></i> Clear / Finalisasi
                    </a>';
            }

            // =========================
            // RULE STATUS (urutan: Detail -> Jurnal -> Clear -> Update -> Print)
            // =========================
            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                if ($canView) {
                    $menuContent .= $detailBtn;
                    $menuContent .= $jurnalBtn;
                }

            } else {

                if ($canView)   $menuContent .= $detailBtn;
                if ($canView)   $menuContent .= $jurnalBtn;
                if ($canUpdate) $menuContent .= $clearBtn;
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
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

            // =========================
            // STATUS BADGE
            // =========================
            $nmstatus   = trim((string) ($lm->nmstatus ?? ''));
            $statusText = $nmstatus !== '' ? $nmstatus : $status;

            $badgeMap = array(
                'F' => 'badge-info',       // FINAL USER
                'O' => 'badge-primary',    // FINAL TRANSACTION
                'P' => 'badge-success',    // CETAK/PRINT
                'A' => 'badge-primary',    // APPROVED
                'D' => 'badge-danger',     // DISAPPROVED
                'E' => 'badge-warning',    // REVISION/EDITING
                'I' => 'badge-secondary',  // DRAFT
            );
            $badgeClass = isset($badgeMap[$status]) ? $badgeMap[$status] : 'badge-secondary';
            $statusBadge = '<div class="text-center"><span style="font-size:12px" class="badge '
                . $badgeClass . ' w-100">'
                . htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') . '</span></div>';

            $cabangText = trim((string) $lm->nmbranch) !== '' ? trim((string) $lm->nmbranch) : trim((string) $lm->cabang);
            $bagianText = trim((string) $lm->nmcostcenter) !== '' ? trim((string) $lm->nmcostcenter) : trim((string) $lm->idcostcenter);

            // =========================
            // ROW DATA (urutan harus sama dengan <th> view)
            // No | Action | Docno | Tanggal | Cabang | Bagian/CC | Referensi | Total | Status
            // =========================
            $row[] = $no;
            $row[] = $dropdownMenu;
            $row[] = htmlspecialchars($docno, ENT_QUOTES, 'UTF-8');
            $row[] = date('d-m-Y', strtotime(trim((string) $lm->docdate)));
            $row[] = htmlspecialchars($cabangText, ENT_QUOTES, 'UTF-8');
            $row[] = htmlspecialchars($bagianText, ENT_QUOTES, 'UTF-8');
            $row[] = htmlspecialchars(trim((string) $lm->docref), ENT_QUOTES, 'UTF-8');
            $row[] = number_format((float) $lm->total, 2, '.', ',');
            $row[] = $statusBadge;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->trx_pmk_brng_mst_view_count_all(),
            "recordsFiltered" => $this->m_persediaan->trx_pmk_brng_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function add_pmk_brng_mst(){
        /* Penambahan Squence */
        $data['title']="Input Pemakaian Barang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.5'; $versirelease='I.Q.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.Q.A.5'";
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
        $data['mst'] = $this->m_persediaan->q_tmp_pmk_brng_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));


        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_persediaan->q_tmp_pmk_brng_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/pmk_brng/v_add_pmk_brg',$data);
    }


    public function save_pmk_brng_detail()
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

        $builderHeader = $db->table('sc_tmp.pmk_brng_mst');

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
                'doctype'    => 'PMKBRG',
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'cabang'     => trim($this->request->getPost('cabang')),
//                'cabang_sent'=> trim($this->request->getPost('cabang_sent')),
                'pemohon'    => strtoupper(trim($this->request->getPost('pemohon'))),
                'estpakai'   => $this->request->getPost('estpakai'),
                'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
                'idcostcenter'    => strtoupper(trim($this->request->getPost('idcostcenter'))),
//                'idlocation_to'      => strtoupper(trim($this->request->getPost('idlocation_to'))),
//                'idlocation_transit' => strtoupper(trim($this->request->getPost('idlocation_transit'))),
                'status'     => 'E',
                'description' => strtoupper(trim((string) (
                    $this->request->getPost('header_description') ?? $this->request->getPost('description')
                ))),
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
        $uniqueid    = trim((string) $this->request->getPost('iduniq'));
        $idbarang    = trim((string) $this->request->getPost('idbarang'));
        $nmbarang    = strtoupper(trim((string) $this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim((string) $this->request->getPost('unit')));
        $idlocation  = trim((string) $this->request->getPost('idlocation'));
        if ($idlocation === '') {
            $idlocation = trim((string) $this->request->getPost('idlocation_dtl'));
        }
        $idcostcenter = strtoupper(trim((string) $this->request->getPost('idcostcenter')));
        $batch       = trim((string) $this->request->getPost('batch'));
        $qty         = (float) $this->request->getPost('qty');
        $val         = (float) $this->request->getPost('val');
        $valsum      = (float) $this->request->getPost('valsum');
        $qtystock    = (float) $this->request->getPost('qtystock');
        $qtybase     = (float) $this->request->getPost('qty_base');
        $docref      = strtoupper(trim((string) $this->request->getPost('docref')));
        $description = strtoupper(trim((string) $this->request->getPost('description')));

        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        if ($idlocation === '') {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gudang belum dipilih'
            ]);
        }

        if (!($qty > 0)) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Qty harus lebih besar dari 0'
            ]);
        }

        if ($valsum <= 0) {
            $valsum = round($qty * $val, 2);
        }

        // =========================
        // VALIDASI STOK (READ ONLY) - BUFFERED
        // Tidak mengurangi stok: pengurangan dilakukan trigger saat Simpan Final.
        // Qty tidak boleh melebihi stok tersedia, yaitu stok fisik dikurangi
        // qty baris draft LAIN pada kombinasi gudang + barang + batch yang sama.
        // =========================
        $stokOnhand = (float) $this->m_persediaan->q_stock_onhand_pmk($idlocation, $idbarang, $batch);
        $stokLain   = (float) $this->m_persediaan->q_stock_booked_pmk($nama, $idlocation, $idbarang, $batch, $idurut);
        $stokSedia  = $stokOnhand - $stokLain;

        if ($stokSedia < 0) {
            $stokSedia = 0;
        }

        if ($qty > $stokSedia) {
            $db->transRollback();

            return $this->response->setJSON([
                'success'   => false,
                'code'      => 'STOK',
                'message'   => 'Qty melebihi stok tersedia. Stok tersedia: '
                    . number_format($stokSedia, 2, '.', ',')
                    . ' (stok fisik ' . number_format($stokOnhand, 2, '.', ',')
                    . ' - draft lain ' . number_format($stokLain, 2, '.', ',') . ').',
                'stok'          => $stokOnhand,
                'stok_tersedia' => $stokSedia
            ]);
        }

        $builderDetail = $db->table('sc_tmp.pmk_brng_dtl');

        // =========================
        // CEK DUPLIKASI (barang + gudang + satuan + qty)
        // =========================
        // BUGFIX: builder tidak boleh dipakai ulang setelah countAllResults()
        //        karena state where-nya menempel ke query berikutnya.
        $builderDuplicate = $db->table('sc_tmp.pmk_brng_dtl')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->where('idbarang', $idbarang)
            ->where('idlocation', $idlocation)
            ->where('unit', $unit)
            ->where('qty', $qty);

        if ($idurut) {
            $builderDuplicate->where('idurut !=', (int) $idurut);
        }

        $duplicate = $builderDuplicate->countAllResults();

        if ($duplicate > 0) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Baris dengan barang, gudang, satuan dan qty yang sama sudah ada pada dokumen ini.'
            ]);
        }

        // =========================
        // INSERT / UPDATE DETAIL
        // =========================
        if ($idurut) {

            $updateDetail = $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('iduniq', $uniqueid)
                ->update([
                    'doctype'    => 'PMKBRG',
                    'docref'     => $docref,
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'idlocation'    => $idlocation,
                    'idcostcenter'    => $idcostcenter,
                    'unit'        => $unit,
                    'batch'         => $batch,
                    'qtystock'         => $qtystock,
                    'qty'         => $qty,
                    'qty_base'    => $qtybase > 0 ? $qtybase : $qty,
                    'val'         => $val,
                    'valsum'      => $valsum,
                    'description' => $description,
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
                'doctype'    => 'PMKBRG',
                'docno'       => $docno,
                'docref'     => $docref,
                'idlocation'    => $idlocation,
                'idcostcenter'    => $idcostcenter,
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'batch'         => $batch,
                'unit'        => $unit,
                'qtystock'         => $qtystock,
                'qty'         => $qty,
                'qty_base'    => $qtybase > 0 ? $qtybase : $qty,
                'val'         => $val,
                'valsum'      => $valsum,
                'description' => $description,
                'inputby'     => $nama,
                'inputdate'   => $inputdate,
                'iduniq'      => $uniqueid
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
            'success'       => true,
            'reload'        => $reload,
            'docno'         => $docno,
            'stok'          => $stokOnhand,
            'stok_tersedia' => $stokSedia
        ]);
    }


    /**
     * Data detail Pemakaian Barang (draft) untuk grid input cepat.
     * Read-only: hanya SELECT pada sc_tmp.pmk_brng_dtl.
     * Kolom 'stok' = stok tersedia saat ini (sc_trx.stkblc), dipakai grid
     * untuk menampilkan & memvalidasi Qty sebelum Simpan Final.
     */
    public function list_pmk_brng_dtl_grid()
    {
        $nama = trim($this->session->get('nama'));

        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama);
        $rows  = $this->m_persediaan->q_tmp_pmk_brng_dtl($param)->getResultArray();

        /* q_tmp_pmk_brng_dtl memakai order by idurut desc; grid dibaca
           dari baris terlama ke terbaru (baris baru di bawah). */
        $rows = array_reverse($rows);

        $items      = array();
        $totalQty   = 0;
        $totalNilai = 0;

        /* Ambil stok sekali untuk semua barang pada draft (hindari N+1 query).
           Kunci: idbarang|idlocation|batch  -> qty on-hand. */
        $idbarangs = array();

        foreach ($rows as $r) {
            $idb = trim((string) ($r['idbarang'] ?? ''));

            if ($idb !== '') {
                $idbarangs[$idb] = $idb;
            }
        }

        $stokMap   = array();   // idbarang|idlocation|batch => qty
        $stokTotal = array();   // idbarang|idlocation        => qty (semua batch)

        if (count($idbarangs) > 0) {
            $params   = array_values($idbarangs);
            $inMarks  = implode(',', array_fill(0, count($params), '?'));

            $stokRows = $this->db->query(
                "SELECT TRIM(idbarang) AS idbarang,
                        TRIM(COALESCE(idlocation,'')) AS idlocation,
                        TRIM(COALESCE(batch,'')) AS batch,
                        SUM(COALESCE(qty_in,0)) - SUM(COALESCE(qty_out,0)) AS stok
                   FROM sc_trx.stkblc
                  WHERE TRIM(idbarang) IN ($inMarks)
                  GROUP BY TRIM(idbarang), TRIM(COALESCE(idlocation,'')), TRIM(COALESCE(batch,''))",
                $params
            )->getResultArray();

            foreach ($stokRows as $sr) {
                $keyItem = $sr['idbarang'] . '|' . $sr['idlocation'];
                $stok     = (float) $sr['stok'];

                $stokMap[$keyItem . '|' . $sr['batch']] = $stok;
                $stokTotal[$keyItem] = (isset($stokTotal[$keyItem]) ? $stokTotal[$keyItem] : 0) + $stok;
            }
        }

        /* TUGAS 1: qty yang sudah dibooking baris draft lain (per grup),
           agar tampilan "Stok Tersedia" tetap konsisten setelah reload grid. */
        $bookedMap   = array();   // idbarang|idlocation|batch => qty
        $bookedTotal = array();   // idbarang|idlocation        => qty

        foreach ($rows as $rb) {
            $idb = trim((string) ($rb['idbarang'] ?? ''));
            $idl = trim((string) ($rb['idlocation'] ?? ''));
            $bch = trim((string) ($rb['batch'] ?? ''));

            if ($idb === '' || $idl === '') {
                continue;
            }

            $keyItem = $idb . '|' . $idl;
            $qtyRow  = (float) ($rb['qty'] ?? 0);

            $bookedMap[$keyItem . '|' . $bch] = (isset($bookedMap[$keyItem . '|' . $bch])
                ? $bookedMap[$keyItem . '|' . $bch] : 0) + $qtyRow;

            $bookedTotal[$keyItem] = (isset($bookedTotal[$keyItem])
                ? $bookedTotal[$keyItem] : 0) + $qtyRow;
        }

        foreach ($rows as $r) {
            $qty    = (float) ($r['qty'] ?? 0);
            $val    = (float) ($r['val'] ?? 0);
            $valsum = (float) ($r['valsum'] ?? 0);

            $idlocation = trim((string) ($r['idlocation'] ?? ''));
            $idbarang   = trim((string) ($r['idbarang'] ?? ''));
            $batch      = trim((string) ($r['batch'] ?? ''));

            $stokFisik = 0;
            $stok      = 0;
            $stokMax   = 0;

            if ($idlocation !== '' && $idbarang !== '') {
                $keyItem = $idbarang . '|' . $idlocation;

                if ($batch !== '') {
                    $stokFisik = isset($stokMap[$keyItem . '|' . $batch]) ? $stokMap[$keyItem . '|' . $batch] : 0;
                    $booked    = isset($bookedMap[$keyItem . '|' . $batch]) ? $bookedMap[$keyItem . '|' . $batch] : 0;
                } else {
                    $stokFisik = isset($stokTotal[$keyItem]) ? $stokTotal[$keyItem] : 0;
                    $booked    = isset($bookedTotal[$keyItem]) ? $bookedTotal[$keyItem] : 0;
                }

                /* stok tampil = stok fisik - SELURUH qty draft grup
                   (buffering, sesuai rumus pada spesifikasi) */
                $stok = $stokFisik - $booked;

                if ($stok < 0) {
                    $stok = 0;
                }

                /* batas Qty baris ini = fisik - qty draft baris LAIN */
                $bookedLain = $booked - $qty;

                if ($bookedLain < 0) {
                    $bookedLain = 0;
                }

                $stokMax = $stokFisik - $bookedLain;

                if ($stokMax < 0) {
                    $stokMax = 0;
                }
            }

            $items[] = array(
                'idurut'      => trim((string) ($r['idurut'] ?? '')),
                'iduniq'      => trim((string) ($r['iduniq'] ?? '')),
                'idbarang'    => $idbarang,
                'nmbarang'    => trim((string) ($r['nmbarang'] ?? '')),
                'idlocation'  => $idlocation,
                'batch'       => $batch,
                'unit'        => trim((string) ($r['unit'] ?? '')),
                'qty'         => $qty,
                'val'         => $val,
                'valsum'      => $valsum,
                'stok'        => $stok,
                'stok_fisik'  => $stokFisik,
                'stok_max'    => $stokMax,
                'description' => trim((string) ($r['description'] ?? '')),
            );

            $totalQty   += $qty;
            $totalNilai += $valsum;
        }

        return $this->response->setJSON(array(
            'success'     => true,
            'items'       => $items,
            'total_qty'   => $totalQty,
            'total_nilai' => $totalNilai,
        ));
    }

    /**
     * Master gudang untuk dropdown grid detail (read-only).
     * Sumber: sc_mst.mlocation.
     */
    public function list_location_pmk_brng()
    {
        $rows = $this->db->query(
            "SELECT TRIM(idlocation) AS idlocation,
                    TRIM(nmlocation) AS nmlocation,
                    TRIM(COALESCE(idbranchjob,'')) AS idbranchjob
               FROM sc_mst.mlocation
              ORDER BY nmlocation ASC"
        )->getResultArray();

        $branch = trim((string) $this->request->getPost('branch'));
        $items  = $rows;

        /* Prioritaskan gudang milik cabang; bila tidak ada, tampilkan semua */
        if ($branch !== '') {
            $filtered = array_values(array_filter($rows, function ($row) use ($branch) {
                return trim((string) $row['idbranchjob']) === $branch;
            }));

            if (count($filtered) > 0) {
                $items = $filtered;
            }
        }

        return $this->response->setJSON(array(
            'success' => true,
            'items'   => $items,
        ));
    }

    /**
     * Pencarian barang untuk dropdown ID Barang pada grid detail (read-only).
     */
    public function search_item_pmk_brng()
    {
        $term = strtoupper(trim((string) $this->request->getPost('term')));
        $page = (int) $this->request->getPost('page');

        if ($page < 1) {
            $page = 1;
        }

        $limit  = 30;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT TRIM(idbarang) AS idbarang,
                       TRIM(nmbarang) AS nmbarang,
                       TRIM(COALESCE(unit,'')) AS unit,
                       COALESCE(lastprice,0) AS lastprice
                  FROM sc_mst.mbarang
                 WHERE COALESCE(chold,'NO') <> 'YES'";

        if ($term !== '') {
            $esc = $this->db->escapeLikeString($term);
            $sql .= " AND (UPPER(TRIM(idbarang)) LIKE '%$esc%' OR UPPER(TRIM(nmbarang)) LIKE '%$esc%')";
        }

        $sql .= " ORDER BY nmbarang ASC LIMIT " . ($limit + 1) . " OFFSET " . $offset;

        $rows = $this->db->query($sql)->getResultArray();
        $more = count($rows) > $limit;

        if ($more) {
            $rows = array_slice($rows, 0, $limit);
        }

        return $this->response->setJSON(array(
            'success' => true,
            'items'   => $rows,
            'more'    => $more,
        ));
    }

    /**
     * Auto-fill Nama Barang, Satuan, dan Harga pada grid detail (read-only).
     */
    public function get_item_pmk_brng()
    {
        $idbarang = trim((string) $this->request->getPost('idbarang'));

        if ($idbarang === '') {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'ID barang tidak boleh kosong',
            ));
        }

        $item = $this->db->query(
            "SELECT TRIM(idbarang) AS idbarang,
                    TRIM(nmbarang) AS nmbarang,
                    TRIM(COALESCE(unit,'')) AS unit,
                    COALESCE(lastprice,0) AS lastprice
               FROM sc_mst.mbarang
              WHERE TRIM(idbarang) = ?
              LIMIT 1",
            array($idbarang)
        )->getRowArray();

        if (!$item) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Barang tidak ditemukan pada master.',
            ));
        }

        $units = $this->db->query(
            "SELECT TRIM(idunit) AS idunit,
                    TRIM(COALESCE(cdefault,'')) AS cdefault,
                    COALESCE(conv_value,1) AS conv_value
               FROM sc_mst.mbarang_unit
              WHERE TRIM(idbarang) = ?
              ORDER BY TRIM(COALESCE(cdefault,'')) DESC, TRIM(idunit) ASC",
            array($idbarang)
        )->getResultArray();

        return $this->response->setJSON(array(
            'success' => true,
            'data'    => $item,
            'units'   => $units,
        ));
    }

    /**
     * Daftar Batch / Spec berdasarkan ID Barang (read-only).
     * Sumber: sc_mst.batch.
     */
    public function list_batch_pmk_brng()
    {
        $idbarang = trim((string) $this->request->getPost('idbarang'));
        $term     = strtoupper(trim((string) $this->request->getPost('term')));
        $page     = (int) $this->request->getPost('page');

        if ($page < 1) {
            $page = 1;
        }

        if ($idbarang === '') {
            return $this->response->setJSON(array(
                'success' => true,
                'items'   => array(),
                'more'    => false,
                'message' => 'Pilih barang terlebih dahulu',
            ));
        }

        $limit  = 30;
        $offset = ($page - 1) * $limit;

        /* Batch diambil dari stok ledger (sc_trx.stkblc) pada gudang terpilih
           agar hanya batch yang benar-benar memiliki stok yang muncul.
           Bila stok ledger belum berisi batch, fallback ke master sc_mst.batch. */
        $idlocation = trim((string) $this->request->getPost('idlocation'));

        $sql = "SELECT TRIM(batch) AS batch
                  FROM sc_trx.stkblc
                 WHERE TRIM(idbarang) = ?
                   AND COALESCE(TRIM(batch), '') <> ''";

        $binds = array($idbarang);

        if ($idlocation !== '') {
            $sql .= " AND TRIM(COALESCE(idlocation, '')) = ?";
            $binds[] = $idlocation;
        }

        if ($term !== '') {
            $sql .= " AND UPPER(TRIM(batch)) LIKE ?";
            $binds[] = '%' . $this->db->escapeLikeString($term) . '%';
        }

        $sql .= " GROUP BY TRIM(batch)
                 HAVING SUM(COALESCE(qty_in,0)) - SUM(COALESCE(qty_out,0)) > 0
                  ORDER BY TRIM(batch) ASC
                  LIMIT " . ($limit + 1) . " OFFSET " . $offset;

        $rows = $this->db->query($sql, $binds)->getResultArray();

        if (count($rows) === 0) {
            $sqlMaster = "SELECT TRIM(batch) AS batch
                            FROM sc_mst.batch
                           WHERE TRIM(idbarang) = ?
                             AND COALESCE(TRIM(batch), '') <> ''";

            $bindsMaster = array($idbarang);

            if ($term !== '') {
                $sqlMaster .= " AND UPPER(TRIM(batch)) LIKE ?";
                $bindsMaster[] = '%' . $this->db->escapeLikeString($term) . '%';
            }

            $sqlMaster .= " GROUP BY TRIM(batch)
                            ORDER BY TRIM(batch) ASC
                            LIMIT " . ($limit + 1);

            $rows = $this->db->query($sqlMaster, $bindsMaster)->getResultArray();
        }


        $more = count($rows) > $limit;

        if ($more) {
            $rows = array_slice($rows, 0, $limit);
        }

        $items = array();

        foreach ($rows as $row) {
            $batch = trim((string) ($row['batch'] ?? ''));

            if ($batch === '') {
                continue;
            }

            $items[] = array(
                'id'    => $batch,
                'text'  => $batch,
                'batch' => $batch,
            );
        }

        return $this->response->setJSON(array(
            'success' => true,
            'items'   => $items,
            'more'    => $more,
        ));
    }

    /**
     * STOK TERSEDIA untuk PMK (read-only).
     * Sumber on-hand: sc_trx.stkblc, harga rata-rata: sc_trx.stkblc_avgcost.
     */
    public function get_stock_pmk_brng()
    {
        $idbarang   = trim((string) $this->request->getPost('idbarang'));
        $idlocation = trim((string) $this->request->getPost('idlocation'));
        $batch      = trim((string) $this->request->getPost('batch'));
        $idurut     = trim((string) $this->request->getPost('idurut'));
        $nama       = trim((string) $this->session->get('nama'));

        if ($idbarang === '' || $idlocation === '') {
            return $this->response->setJSON(array(
                'success'       => false,
                'message'       => 'Barang dan gudang wajib dipilih',
                'stok'          => 0,
                'stok_fisik'    => 0,
                'stok_tmp'      => 0,
                'stok_tersedia' => 0,
                'avgcost'       => 0,
            ));
        }

        /* ============================================================
           BUFFERING STOK (TUGAS 1)
           stok tersedia = stok fisik gudang (sc_trx.stkblc)
                         - total qty yang sudah dibooking di draft
                           sc_tmp.pmk_brng_dtl (user + gudang + barang + batch)
           'stok_max' = batas Qty baris ini (idurut) = fisik - draft baris LAIN.
           ============================================================ */
        $stokFisik = (float) $this->m_persediaan->q_stock_onhand_pmk($idlocation, $idbarang, $batch);
        $stokTmp   = (float) $this->m_persediaan->q_stock_booked_pmk($nama, $idlocation, $idbarang, $batch);
        $tersedia  = $stokFisik - $stokTmp;

        if ($tersedia < 0) {
            $tersedia = 0;
        }

        $stokMax = null;

        if ($idurut !== '') {
            $stokLain = (float) $this->m_persediaan->q_stock_booked_pmk($nama, $idlocation, $idbarang, $batch, $idurut);
            $stokMax  = $stokFisik - $stokLain;

            if ($stokMax < 0) {
                $stokMax = 0;
            }
        }

        $avgcost = (float) $this->m_persediaan->q_avgcost_pmk($idlocation, $idbarang, $batch);

        return $this->response->setJSON(array(
            'success'       => true,
            'idbarang'      => $idbarang,
            'idlocation'    => $idlocation,
            'batch'         => $batch,
            'stok'          => $stokFisik,      // kompatibilitas pemanggil lama
            'stok_fisik'    => $stokFisik,
            'stok_tmp'      => $stokTmp,
            'stok_tersedia' => $tersedia,
            'stok_max'      => $stokMax,
            'avgcost'       => $avgcost,
        ));
    }

    function showing_pmk_brng_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby='$nama'";
        $data = $this->m_persediaan->q_tmp_pmk_brng_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_tmp_pmk_brng_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_tmp_pmk_brng_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    // UNTUK TRX
    function showing_pmk_brng_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_persediaan->q_trx_pmk_brng_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_trx_pmk_brng_mst_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_trx_pmk_brng_mst_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function final_pmk_barang()
    {
        $db = \Config\Database::connect();
        $nama = trim($this->session->get('nama'));

        // 🔥 WAJIB: ambil docno spesifik (bukan semua inputby)
        $docno = trim($this->request->getPost('docno'));

        if (!$docno) {
            return redirect()->back()->with('error', 'Docno tidak ditemukan');
        }

        try {

            $db->transStart();

            // =========================
            // 🔒 LOCK HEADER (ANTI DOUBLE FINAL)
            // =========================
            $header = $db->query("
            SELECT * 
            FROM sc_tmp.pmk_brng_mst
            WHERE TRIM(docno) = ?
              AND TRIM(inputby) = ?
            FOR UPDATE
        ", [$docno, $nama])->getRowArray();

            if (!$header) {
                throw new \Exception('Data tidak ditemukan');
            }

            if (trim($header['status']) !== 'E') {
                throw new \Exception('Status bukan E (tidak bisa FINAL)');
            }

            // =========================
            // 🔥 VALIDASI STOK (SERVER SIDE, ANTI BYPASS)
            // PMK mengurangi stok -> qty total per barang/gudang/batch
            // tidak boleh melebihi stok tersedia (sc_trx.stkblc).
            // =========================
            $details = $db->query("
                SELECT TRIM(idbarang)    AS idbarang,
                       TRIM(idlocation)  AS idlocation,
                       TRIM(COALESCE(batch,'')) AS batch,
                       SUM(COALESCE(qty,0)) AS qty
                  FROM sc_tmp.pmk_brng_dtl
                 WHERE TRIM(docno) = ?
                   AND TRIM(inputby) = ?
                 GROUP BY TRIM(idbarang), TRIM(idlocation), TRIM(COALESCE(batch,''))
            ", [$docno, $nama])->getResultArray();

            if (count($details) === 0) {
                throw new \Exception('Detail barang belum ada, tidak dapat disimpan final');
            }

            foreach ($details as $d) {
                $stok = (float) $this->m_persediaan->q_stock_onhand_pmk(
                    trim((string) $d['idlocation']),
                    trim((string) $d['idbarang']),
                    trim((string) $d['batch'])
                );

                if ((float) $d['qty'] > $stok) {
                    throw new \Exception(
                        'Stok tidak mencukupi untuk barang ' . trim((string) $d['idbarang'])
                        . ' (gudang ' . trim((string) $d['idlocation']) . '). Qty '
                        . number_format((float) $d['qty'], 2, '.', ',')
                        . ' > stok ' . number_format($stok, 2, '.', ',') . '.'
                    );
                }
            }

            // =========================
            // 🔥 UPDATE HEADER (KETERANGAN)
            // =========================
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));

            $db->table('sc_tmp.pmk_brng_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->update([
                    'description' => $keterangan
                ]);

            // =========================
            // 🔥 FINAL (INI YANG MEMICU TRIGGER)
            // =========================
            $db->table('sc_tmp.pmk_brng_mst')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->update([
                    'status' => 'F',
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateby' => $nama
                ]);

            // =========================
            // 🔥 COMMIT
            // =========================
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal final');
            }

            return redirect()->to(base_url('/persediaan/trans/pmk_brng'))
                ->with('success', 'Data berhasil di FINAL');

        } catch (\Throwable $e) {

            $db->transRollback();

            // simpan ke trxerror (optional)
            $db->table('sc_mst.trxerror')->insert([
                'userid' => $nama,
                'errorcode' => 3,
                'modul' => 'I.Q.A.5'
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function list_tmp_pmk_brng_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_tmp_pmk_brng_dtl_view($docno);
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
            $row[] = $lm->idlocation;
            $row[] = $lm->batch;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. $lm->qty  . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->get_tmp_pmk_brng_dtl_view($docno),
            "recordsFiltered" => $this->m_persediaan->tmp_pmk_brng_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    /* ============================================================
       LIST DETAIL PEMAKAIAN BARANG (TRANSAKSI FINAL)
       Sumber: sc_trx.pmk_brng_dtl difilter per docno
       ============================================================ */
    function list_trx_pmk_brng_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_trx_pmk_brng_dtl_view($docno);
        $data = array();
        $no = $_POST['start'] ?? 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $lm->idurut;
            //item
            $row[] = trim($lm->idbarang);
            $row[] = trim($lm->nmbarang);
            $row[] = trim($lm->nmlocation) != '' ? trim($lm->nmlocation) : trim($lm->idlocation);
            $row[] = trim($lm->batch);
            $row[] = trim($lm->unit);
            $row[] = '<div class="ratakanan">'. number_format((float) $lm->qty, 2, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'] ?? 0,
            "recordsTotal" => $this->m_persediaan->trx_pmk_brng_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_persediaan->trx_pmk_brng_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }




    function updatePmkBrg()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_pmk_brng_mst($param)->getRowArray();

        if (empty($dtl)) {
            session()->setFlashdata('error', 'Transaksi tidak ditemukan.');

            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        }

        $status = strtoupper(trim((string) $dtl['status']));

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        }

        /* ====== GUARD PERIODE CLOSING BERDASARKAN DOCDATE (ANTI BYPASS) ====== */
        $closingPeriode = $this->m_persediaan->q_periode_closing_last()->getRowArray();

        if (!empty($dtl) && $this->m_persediaan->isPeriodeClosed(trim((string) $dtl['docdate']), $closingPeriode)) {
            $closingLabel = trim((string) ($closingPeriode['periode'] ?? ''));

            if ($closingLabel === '') {
                $closingLabel = trim((string) ($closingPeriode['tanggal_akhir'] ?? ''));
            }

            session()->setFlashdata('periode_error',
                'Tanggal transaksi sudah masuk periode TUTUP'
                . ($closingLabel !== '' ? ' (' . $closingLabel . ')' : '')
                . '. Dokumen tidak dapat diupdate.'
            );

            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        }

        /* TUGAS 3: transaksi status 'E' (Entry/Draft) tidak boleh diedit. */
        if ($status === 'E') {
            session()->setFlashdata('error', 'Transaksi berstatus Draft, tidak dapat diedit.');

            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        }

        if ($status === 'F' || $status === 'P') {

            /* F/P -> E : trigger sc_trx.tr_trx_pmk_brng_mst akan menyalin
               dokumen ke sc_tmp sebagai draft revisi.
               Catatan: sc_trx.pmk_brng_mst tidak punya kolom tgl_posting /
               user_posting, jadi dipakai updatedate / updateby. */
            $this->db->query(
                "UPDATE sc_trx.pmk_brng_mst
                    SET status = 'E',
                        updateby = ?,
                        updatedate = ?
                  WHERE TRIM(docno) = ?",
                [$nama, date('Y-m-d H:i:s'), $docno]
            );

            // Redirect ke halaman input Pemakaian Barang
            return redirect()->to(base_url('persediaan/trans/add_pmk_brng_mst'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            session()->setFlashdata('error', 'Status transaksi tidak dapat diupdate.');

            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        }
    }
    function detail_pmk_brng_mst()
    {
        /* Penambahan Squence */
        $data['title']="Detail Pemakaian Barang ";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('persediaan/trans/perintah_transfer'));
        }
        $kodemenu='I.Q.A.5'; $versirelease='I.Q.A.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.5'";
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
        $data['dtldata'] = $this->m_persediaan->q_trx_pmk_brng_mst($param)->getRowArray();

        /* ====== NAMA CABANG & COST CENTER (untuk tampilan detail) ====== */
        $cabang       = isset($data['dtldata']['cabang']) ? trim($data['dtldata']['cabang']) : '';
        $idcostcenter = isset($data['dtldata']['idcostcenter']) ? trim($data['dtldata']['idcostcenter']) : '';

        $dtlbranchjob = $this->m_global->q_branchjob(" and idbranch=" . $this->db->escape($cabang))->getRowArray();
        $dtlcostcntr  = $this->m_global->q_costcenter(" and idcostcenter=" . $this->db->escape($idcostcenter))->getRowArray();

        $data['nmbranch']     = isset($dtlbranchjob['nmbranch']) ? trim($dtlbranchjob['nmbranch']) : $cabang;
        $data['nmcostcenter'] = isset($dtlcostcntr['nmcostcenter']) ? trim($dtlcostcntr['nmcostcenter']) : $idcostcenter;

        return $this->template->render('persediaan/pmk_brng/v_detail_pmk_brng',$data);
    }

    /**
     * Laporan Jurnal Transaksi Pemakaian Barang PNM (read only).
     * Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
     * cost center diambil dari dokumen asal sc_trx.pnm_brng_mst.
     */
    public function laporan_jurnal_transaksi_pnm()
    {
        try {
            $docno = trim($this->request->getPost('docno'));

            if ($docno === '') {
                return $this->response->setJSON([
                    'status'   => false,
                    'messages' => 'Doc No Pemakaian Barang tidak ditemukan',
                    'data'     => []
                ]);
            }

            $params = "
                AND TRIM(jh.docno) = " . $this->db->escape($docno);

            $query = $this->m_persediaan
                ->q_laporan_jurnal_transaksi_pnm($params);

            $data = $query->getResultArray();

            return $this->response->setJSON([
                'status'   => true,
                'messages' => 'Data berhasil diambil',
                'data'     => $data
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'laporan_jurnal_transaksi_pnm: ' . $e->getMessage()
            );

            return $this->response->setJSON([
                'status'   => false,
                'messages' => $e->getMessage(),
                'data'     => []
            ]);
        }
    }

    /**
     * Laporan Jurnal Transaksi Pemakaian Barang (read only).
     * Sumber jurnal: sc_trx.jurnal_hd + sc_trx.jurnal_dt (status POSTED),
     * cost center diambil dari dokumen asal sc_trx.pmk_brng_mst.
     */
    public function laporan_jurnal_transaksi_pmk()
    {
        try {

            // =================================================
            // GET DOCNO
            // =================================================

            $docno = trim($this->request->getPost('docno'));

            // =================================================
            // VALIDASI
            // =================================================

            if ($docno === '') {

                return $this->response->setJSON([

                    'status'   => false,

                    'messages' => 'Doc No Pemakaian Barang tidak ditemukan',

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

            $query = $this->m_persediaan
                ->q_laporan_jurnal_transaksi_pmk($params);

            $data = $query->getResultArray();

            // =================================================
            // RESPONSE
            // =================================================

            return $this->response->setJSON([

                'status'   => true,

                'messages' => 'Data berhasil diambil',

                'data'     => $data

            ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'laporan_jurnal_transaksi_pmk: '
                . $e->getMessage()
            );

            return $this->response->setJSON([

                'status'   => false,

                'messages' => $e->getMessage(),

                'data'     => []

            ]);
        }
    }


    public function getBranch_pmk_brng()
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
        $prefix = trim($konfigurasiUmum[0]['pmkbarang']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = trim((string) $this->session->get('logindate')); // dd-mm-yyyy
        $timeLogin = $logindate !== '' ? strtotime($logindate) : time();
        $infix     = date('ym', $timeLogin ?: time());

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

    /**
     * Penomoran otomatis KHUSUS modul PMK (Pemakaian Barang).
     * Format : {prefix}/{infix}/{kode_suffix}{urut:4}
     * Contoh : PBL/2611/PT0001
     *
     * Urut berikutnya dihitung dari MAX nomor yang sudah ada pada
     * sc_trx.pmk_brng_mst DAN sc_tmp.pmk_brng_mst (draft), agar nomor
     * tidak bentrok ketika draft belum di-final.
     *
     * Read-only (SELECT saja).
     */
    public function getNextSuffix_pmk_brng()
    {
        $prefix     = strtoupper(trim((string) $this->request->getGet('prefix')));
        $infix      = trim((string) $this->request->getGet('infix'));
        $kodeSuffix = strtoupper(trim((string) $this->request->getGet('kode_suffix')));

        /* infix = periode (yy mm) dari tanggal login. Fallback: tanggal hari ini,
           supaya nomor tidak pernah terbentuk dari periode kosong. */
        if ($infix === '') {
            $logindate = trim((string) $this->session->get('logindate'));
            $time      = $logindate !== '' ? strtotime($logindate) : time();
            $infix     = date('ym', $time ?: time());
        }

        if ($prefix === '' || $kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter penomoran belum lengkap',
                'suffix'  => ''
            ]);
        }

        $like    = $prefix . '/' . $infix . '/' . $kodeSuffix;
        $maxUrut = 0;
        $found   = 0;

        foreach (['sc_trx.pmk_brng_mst', 'sc_tmp.pmk_brng_mst'] as $tabel) {

            /* Ambil beberapa nomor terakhir (bukan hanya 1) lalu hitung
               nilai urut terbesar secara numerik, agar penomoran tetap
               benar walau ada docno dengan panjang digit berbeda. */
            $rows = $this->db->query(
                "SELECT TRIM(docno) AS docno
                   FROM {$tabel}
                  WHERE TRIM(docno) LIKE ?
                  ORDER BY TRIM(docno) DESC
                  LIMIT 100",
                array($like . '%')
            )->getResultArray();

            foreach ($rows as $row) {

                $docno = trim((string) ($row['docno'] ?? ''));

                if ($docno === '') {
                    continue;
                }

                $parts = explode('/', $docno);

                if (count($parts) < 3) {
                    continue;
                }

                $last = trim($parts[2]);

                /* buang kode suffix di depan (PT/PA/PB/...) */
                if (strlen($last) > strlen($kodeSuffix)
                    && stripos($last, $kodeSuffix) === 0) {
                    $last = substr($last, strlen($kodeSuffix));
                }

                $urut = (int) preg_replace('/[^0-9]/', '', $last);

                if ($urut > $maxUrut) {
                    $maxUrut = $urut;
                }

                $found++;
            }
        }

        $next = str_pad((string) ($maxUrut + 1), 4, '0', STR_PAD_LEFT);

        return $this->response->setJSON([
            'success' => true,
            'prefix'  => $prefix,
            'infix'   => $infix,
            'suffix'  => $kodeSuffix . $next,
            'docno'   => $prefix . '/' . $infix . '/' . $kodeSuffix . $next,
            'last'    => $maxUrut,
            'rows'    => $found
        ]);
    }

    public function delete_pmk_brng()
    {
        $ids = $this->request->getPost('ids');

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_tmp.pmk_brng_dtl');

        try {

            $db->transBegin(); // START TRANSACTION

            $builder->whereIn('idurut', $ids)->delete();

            // Cek apakah semua benar-benar terhapus
            if ($db->affectedRows() !== count($ids)) {
                throw new \Exception('Sebagian data gagal dihapus');
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            $db->transCommit(); // COMMIT jika sukses semua

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semua data berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback(); // ROLLBACK jika ada error

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }
    /**
     * CLEAR / FINALISASI transaksi Pemakaian Barang.
     *
     * Dipanggil dari dua tempat:
     *  a. Tombol "Clear" pada modal "Entry not finished" (M_Trxerror::unfinish)
     *     -> TANPA parameter docno. Semua draft TMP milik user dibuang dan
     *        dokumen yang sedang diedit (status 'E') dikembalikan ke 'F'.
     *  b. Tombol "Clear / Finalisasi" pada halaman list
     *     (?docno=<hex> atau ?docno=<docno asli>).
     *
     * Alur bisnis PMK (mengikuti pola clearTransferLokasi):
     *  1. Dokumen sudah ada di sc_trx.pmk_brng_mst dengan status 'E'
     *     (sedang diedit / draft revisi)  -> status dikembalikan ke 'F'
     *     dan draft revisi di sc_tmp dibuang.
     *  2. Dokumen masih berupa draft entry baru di sc_tmp.pmk_brng_mst
     *     (belum pernah ke sc_trx)       -> status draft di-set 'F'
     *     sehingga trigger sc_tmp.tr_tmp_pmk_brng_mst menyalin dokumen ke
     *     sc_trx.pmk_brng_mst / sc_trx.pmk_brng_dtl.
     *
     * Catatan stok & jurnal (PENTING):
     *  - Posting stok (sc_trx.stkblc) dan jurnal (sc_trx.jurnal_hd/dt)
     *    TIDAK dilakukan manual di controller. Semua baris detail yang
     *    masuk ke sc_trx.pmk_brng_dtl otomatis diproses oleh trigger
     *    sc_trx.fn_pmk_dtl_to_transaction -> sc_trx.transaction_dt ->
     *    trg_transaction_stock / trg_zz_transaction_accounting.
     *  - Jadi controller tidak boleh insert stok/jurnal sendiri (dokumen
     *    akan ter-posting dua kali).
     *
     * Catatan struktur: sc_trx.pmk_brng_mst tidak memiliki kolom
     * tgl_posting / user_posting, field yang diupdate adalah
     * updateby / updatedate.
     */
    function clear_pmk_brng()
    {
        $db     = \Config\Database::connect();
        $nama   = trim((string) $this->session->get('nama'));
        $isAjax = $this->request->isAJAX();

        if ($nama === '') {

            $pesan = 'Clear gagal: session user tidak ditemukan, silakan login ulang.';

            if ($isAjax) {
                return $this->response->setJSON(array(
                    'status'   => false,
                    'success'  => false,
                    'messages' => $pesan,
                    'message'  => $pesan,
                ));
            }

            return redirect()->to(base_url('persediaan/trans/pmk_brng'))
                ->with('error', $pesan);
        }

        /* docno dari list dikirim dalam bentuk hex (mengikuti tautan lain) */
        $raw = trim((string) ($this->request->getGet('docno') ?? ''));

        if ($raw === '') {
            $raw = trim((string) $this->request->getPost('docno'));
        }

        if ($raw === '') {
            $raw = trim((string) ($this->request->getGet('id') ?? ''));
        }

        if ($raw === '') {
            $raw = trim((string) $this->request->getPost('id'));
        }

        $raw   = trim(urldecode($raw), " \t\n\r\0\x0B\"'");
        $docno = $raw;

        if ($raw !== '' && strpos($raw, '/') === false && ctype_xdigit($raw) && (strlen($raw) % 2 === 0)) {
            $decoded = @hex2bin($raw);

            if ($decoded !== false && trim($decoded) !== '') {
                $docno = trim($decoded);
            }
        }

        /* =========================================================
         * A. TANPA DOCNO — dipanggil dari modal "Entry not finished"
         *    (tombol Clear). Draft TMP milik user dibuang dan status
         *    dokumen yang sedang diedit dikembalikan ke 'F'.
         * ========================================================= */
        if ($docno === '') {

            try {

                $db->transBegin();

                /* A1. Header TMP milik user:
                 *     - entry baru   : inputby = user
                 *     - draft revisi : docnotmp = dokumen berstatus 'E'
                 *                      yang terakhir di-update user
                 *       (inputby draft revisi adalah pembuat dokumen
                 *        asli, bukan user yang melakukan edit)
                 */
                $rows = $db->query(
                    "SELECT TRIM(docno) AS docno,
                            TRIM(COALESCE(docnotmp, '')) AS docnotmp
                       FROM sc_tmp.pmk_brng_mst
                      WHERE BTRIM(COALESCE(inputby, '')) = ?
                         OR BTRIM(COALESCE(docnotmp, '')) IN (
                                SELECT BTRIM(docno)
                                  FROM sc_trx.pmk_brng_mst
                                 WHERE UPPER(BTRIM(status)) = 'E'
                                   AND BTRIM(COALESCE(updateby, '')) = ?
                            )
                      FOR UPDATE",
                    array($nama, $nama)
                )->getResultArray();

                $docnos      = array();   // docno header TMP
                $resetDocnos = array();   // docno dokumen yang dibatalkan editnya

                foreach ($rows as $row) {
                    $rowDocno = trim((string) ($row['docno'] ?? ''));
                    $baseDoc  = trim((string) ($row['docnotmp'] ?? ''));

                    if ($rowDocno !== '') {
                        $docnos[$rowDocno] = $rowDocno;
                    }

                    if ($baseDoc !== '') {
                        $resetDocnos[$baseDoc] = $baseDoc;
                    }
                }

                $now        = date('Y-m-d H:i:s');
                $totalReset = 0;

                /* A2. Batalkan mode edit (E -> F) pada dokumen final.
                 *     Trigger sc_trx.tr_trx_pmk_brng_mst hanya bekerja
                 *     untuk F -> E, jadi detail transaksi tidak berubah.
                 */
                if (count($resetDocnos) > 0) {
                    $placeholders = implode(',', array_fill(0, count($resetDocnos), '?'));

                    $db->query(
                        "UPDATE sc_trx.pmk_brng_mst
                            SET status = 'F',
                                updateby = ?,
                                updatedate = ?
                          WHERE UPPER(BTRIM(status)) = 'E'
                            AND BTRIM(docno) IN ($placeholders)",
                        array_merge(array($nama, $now), array_values($resetDocnos))
                    );

                    $totalReset += $db->affectedRows();
                }

                /* A2b. Sisa dokumen mode edit milik user yang draft TMP-nya
                 *      sudah tidak ada (mis. terhapus manual).
                 */
                $db->query(
                    "UPDATE sc_trx.pmk_brng_mst
                        SET status = 'F',
                            updateby = ?,
                            updatedate = ?
                      WHERE UPPER(BTRIM(status)) = 'E'
                        AND BTRIM(COALESCE(updateby, '')) = ?",
                    array($nama, $now, $nama)
                );

                $totalReset += $db->affectedRows();

                /* A3. Hapus draft TMP milik user (detail dulu, lalu header) */
                $db->query(
                    "DELETE FROM sc_tmp.pmk_brng_dtl
                      WHERE BTRIM(COALESCE(inputby, '')) = ?",
                    array($nama)
                );

                if (count($docnos) > 0) {
                    $placeholders = implode(',', array_fill(0, count($docnos), '?'));
                    $bind         = array_merge(array_values($docnos), array_values($docnos));

                    $db->query(
                        "DELETE FROM sc_tmp.pmk_brng_dtl
                          WHERE BTRIM(docno) IN ($placeholders)
                             OR BTRIM(COALESCE(docnotmp, '')) IN ($placeholders)",
                        $bind
                    );
                }

                $db->query(
                    "DELETE FROM sc_tmp.pmk_brng_mst
                      WHERE BTRIM(COALESCE(inputby, '')) = ?",
                    array($nama)
                );

                if (count($docnos) > 0) {
                    $placeholders = implode(',', array_fill(0, count($docnos), '?'));

                    $db->query(
                        "DELETE FROM sc_tmp.pmk_brng_mst
                          WHERE BTRIM(docno) IN ($placeholders)
                             OR BTRIM(COALESCE(docnotmp, '')) IN ($placeholders)",
                        array_merge(array_values($docnos), array_values($docnos))
                    );
                }

                if ($db->transStatus() === false) {
                    throw new \Exception('Gagal memproses clear Pemakaian Barang.');
                }

                $db->transCommit();

                $countTmp = count($docnos);
                $msg      = array();

                if ($countTmp > 0) {
                    $msg[] = $countTmp . ' draft dihapus';
                }

                if ($totalReset > 0) {
                    $msg[] = $totalReset . ' dokumen dikembalikan ke status F';
                }

                $pesan = count($msg) > 0
                    ? 'Clear berhasil. ' . implode(', ', $msg) . '.'
                    : 'Tidak ada entry menggantung untuk di-clear.';

                log_message('info', '[clear_pmk_brng] user=' . $nama
                    . ' mode=CLEAR_UNFINISHED'
                    . ' draft=' . $countTmp
                    . ' reset=' . $totalReset);

                if ($isAjax) {
                    return $this->response->setJSON(array(
                        'status'   => true,
                        'success'  => true,
                        'mode'     => 'CLEAR_UNFINISHED',
                        'draft'    => $countTmp,
                        'reset'    => $totalReset,
                        'messages' => $pesan,
                        'message'  => $pesan,
                    ));
                }

                return redirect()->to(base_url('persediaan/trans/pmk_brng'))
                    ->with('success', $pesan);

            } catch (\Throwable $e) {

                $db->transRollback();

                log_message('error', '[clear_pmk_brng] user=' . $nama
                    . ' mode=CLEAR_UNFINISHED'
                    . ' error=' . $e->getMessage());

                $pesan = 'Clear gagal: ' . $e->getMessage();

                if ($isAjax) {
                    return $this->response->setJSON(array(
                        'status'   => false,
                        'success'  => false,
                        'messages' => $pesan,
                        'message'  => $pesan,
                    ));
                }

                return redirect()->to(base_url('persediaan/trans/pmk_brng'))
                    ->with('error', $pesan);
            }
        }

        /* =========================================================
         * B. DENGAN DOCNO — finalisasi / clear satu dokumen
         * ========================================================= */
        try {

            $db->transBegin();

            /* =========================================================
             * 1. HEADER sc_trx — dokumen yang sudah pernah Final.
             *    Lock baris agar tidak dobel proses oleh user lain.
             * ========================================================= */
            $trx = $db->query(
                "SELECT TRIM(docno) AS docno, TRIM(status) AS status
                   FROM sc_trx.pmk_brng_mst
                  WHERE BTRIM(docno) = ?
                  FOR UPDATE",
                array($docno)
            )->getRowArray();

            /* =========================================================
             * 2. HEADER sc_tmp — draft entry baru / draft revisi.
             *    Revisi ditandai docnotmp = docno asli.
             * ========================================================= */
            $tmp = $db->query(
                "SELECT TRIM(docno) AS docno,
                        idurut,
                        TRIM(COALESCE(docnotmp, '')) AS docnotmp,
                        TRIM(status) AS status
                   FROM sc_tmp.pmk_brng_mst
                  WHERE BTRIM(docno) = ?
                     OR BTRIM(COALESCE(docnotmp, '')) = ?
                  ORDER BY CASE WHEN BTRIM(COALESCE(docnotmp, '')) = '' THEN 1 ELSE 0 END
                  LIMIT 1
                  FOR UPDATE",
                array($docno, $docno)
            )->getRowArray();

            $now      = date('Y-m-d H:i:s');
            $finalDoc = $docno;
            $mode     = '';

            if ($trx) {

                $status = strtoupper(trim((string) $trx['status']));

                if ($status === 'E') {

                    /* ---------------------------------------------
                     * E -> F : batalkan mode edit (Clear/Finalisasi).
                     * Detail trx tidak dihapus sehingga stok & jurnal
                     * tetap seperti saat dokumen di-finalisasi dulu.
                     * --------------------------------------------- */
                    $db->query(
                        "UPDATE sc_trx.pmk_brng_mst
                            SET status = 'F',
                                updateby = ?,
                                updatedate = ?
                          WHERE BTRIM(docno) = ?
                            AND UPPER(BTRIM(status)) = 'E'",
                        array($nama, $now, $docno)
                    );

                    /* buang draft revisi / draft entry milik dokumen ini */
                    $db->query(
                        "DELETE FROM sc_tmp.pmk_brng_dtl
                          WHERE BTRIM(docno) = ?
                             OR BTRIM(COALESCE(docnotmp, '')) = ?",
                        array($docno, $docno)
                    );
                    $db->query(
                        "DELETE FROM sc_tmp.pmk_brng_mst
                          WHERE BTRIM(docno) = ?
                             OR BTRIM(COALESCE(docnotmp, '')) = ?",
                        array($docno, $docno)
                    );

                    $mode = 'RESTORE_F';

                } elseif ($status === 'F' || $status === 'P') {

                    /* sudah Final: cukup bersihkan sisa draft revisi */
                    $db->query(
                        "DELETE FROM sc_tmp.pmk_brng_dtl
                          WHERE BTRIM(docno) = ?
                             OR BTRIM(COALESCE(docnotmp, '')) = ?",
                        array($docno, $docno)
                    );
                    $db->query(
                        "DELETE FROM sc_tmp.pmk_brng_mst
                          WHERE BTRIM(docno) = ?
                             OR BTRIM(COALESCE(docnotmp, '')) = ?",
                        array($docno, $docno)
                    );

                    $mode = 'ALREADY_F';

                } else {
                    throw new \Exception('Transaksi berstatus ' . $status . ', tidak dapat di-finalisasi.');
                }

            } elseif ($tmp) {

                /* ---------------------------------------------
                 * Dokumen masih draft di sc_tmp (belum pernah masuk
                 * sc_trx). Finalisasi = set status 'F' pada header TMP;
                 * trigger sc_tmp.tr_tmp_pmk_brng_mst menyalin header +
                 * detail ke sc_trx sehingga stok & jurnal ter-posting
                 * otomatis oleh trigger transaksi.
                 * --------------------------------------------- */
                $tmpDocno = trim((string) $tmp['docno']);

                if ($tmpDocno === '') {
                    throw new \Exception('Draft transaksi tidak valid (docno kosong).');
                }

                $tmpStatus = strtoupper(trim((string) $tmp['status']));

                if ($tmpStatus !== 'E') {
                    throw new \Exception(
                        'Draft transaksi ' . $tmpDocno . ' berstatus ' . $tmpStatus
                        . ', tidak dapat di-finalisasi.'
                    );
                }

                $db->query(
                    "UPDATE sc_tmp.pmk_brng_mst
                        SET status = 'F',
                            updateby = ?,
                            updatedate = ?
                      WHERE BTRIM(docno) = ?
                        AND UPPER(BTRIM(status)) = 'E'",
                    array($nama, $now, $tmpDocno)
                );

                /* Trigger hanya menghapus sc_tmp.pmk_brng_mst pada kondisi
                   "sudah ada di sc_trx". Bersihkan sisa detail TMP yang
                   mungkin tertinggal (PK sc_tmp.pmk_brng_mst = docno). */
                $db->query(
                    "DELETE FROM sc_tmp.pmk_brng_dtl
                      WHERE BTRIM(docno) = ?",
                    array($tmpDocno)
                );

                $db->query(
                    "DELETE FROM sc_tmp.pmk_brng_mst
                      WHERE BTRIM(docno) = ?",
                    array($tmpDocno)
                );

                $finalDoc = trim((string) $tmp['docnotmp']) !== ''
                    ? trim((string) $tmp['docnotmp'])
                    : $tmpDocno;

                $mode = 'FINALIZE_DRAFT';

            } else {
                throw new \Exception('Transaksi ' . $docno . ' tidak ditemukan.');
            }

            /* =========================================================
             * 3. VERIFIKASI HASIL
             *    Tidak memakai affectedRows() saja, karena status juga
             *    bisa diubah oleh trigger database.
             * ========================================================= */
            $verify = null;

            /* Trigger sc_tmp.tr_tmp_pmk_brng_mst dapat menomori ulang
               docno draft bila docno tersebut sudah dipakai di sc_trx.
               idurut ikut di-copy ke sc_trx (draft baru maupun revisi),
               jadi cari lewat idurut lebih dulu. */
            if ($mode === 'FINALIZE_DRAFT' && !empty($tmp['idurut'])) {
                $verify = $db->query(
                    "SELECT TRIM(docno) AS docno, TRIM(status) AS status
                       FROM sc_trx.pmk_brng_mst
                      WHERE idurut = ?",
                    array((int) $tmp['idurut'])
                )->getRowArray();

                if ($verify) {
                    $finalDoc = trim((string) $verify['docno']);
                }
            }

            if (!$verify) {
                $verify = $db->query(
                    "SELECT TRIM(docno) AS docno, TRIM(status) AS status
                       FROM sc_trx.pmk_brng_mst
                      WHERE BTRIM(docno) = ?",
                    array($finalDoc)
                )->getRowArray();
            }

            if (!$verify) {
                throw new \Exception('Dokumen ' . $finalDoc . ' tidak ada di tabel transaksi.');
            }

            $statusAfter = strtoupper(trim((string) $verify['status']));

            /* ALREADY_F: dokumen memang sudah final (F) atau sudah
               diposting (P) - hanya sisa draft yang dibersihkan. */
            $statusValid = ($mode === 'ALREADY_F')
                ? in_array($statusAfter, array('F', 'P'), true)
                : ($statusAfter === 'F');

            if (!$statusValid) {
                throw new \Exception('Status dokumen ' . $finalDoc . ' masih ' . $statusAfter . ', finalisasi gagal.');
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal memproses finalisasi Pemakaian Barang.');
            }

            $db->transCommit();

            $pesan = ($mode === 'ALREADY_F')
                ? 'Transaksi ' . $finalDoc . ' sudah berstatus Final.'
                : 'Transaksi berhasil di-finalisasi';

            log_message('info', '[clear_pmk_brng] user=' . $nama
                . ' docno=' . $docno
                . ' mode=' . $mode
                . ' final=' . $finalDoc);

            if ($isAjax) {
                return $this->response->setJSON(array(
                    'status'   => true,
                    'success'  => true,
                    'docno'    => $finalDoc,
                    'mode'     => $mode,
                    'messages' => $pesan,
                    'message'  => $pesan,
                ));
            }

            return redirect()->to(base_url('persediaan/trans/pmk_brng'))
                ->with('success', $pesan);

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message('error', '[clear_pmk_brng] user=' . $nama
                . ' docno=' . $docno
                . ' error=' . $e->getMessage());

            $pesan = 'Finalisasi gagal: ' . $e->getMessage();

            if ($isAjax) {
                return $this->response->setJSON(array(
                    'status'   => false,
                    'success'  => false,
                    'messages' => $pesan,
                    'message'  => $pesan,
                ));
            }

            return redirect()->to(base_url('persediaan/trans/pmk_brng'))
                ->with('error', $pesan);
        }
    }


    /* ************************************************************ PENERIMAAN BARANG  ************************************************************************/
    function pnm_barang()
    {
        //I.Q.A.6
        $data['title']="Penerimaan Barang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.6'; $versirelease='I.Q.A.6/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.Q.A.6'";
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
        $dtl = $this->m_persediaan->q_tmp_pnm_brng_mst($param);
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

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
            $urlclear = base_url('persediaan/trans/clear_pnm_brng');
            $urlnext = base_url('persediaan/trans/add_pnm_brng_mst');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.Q.A.6';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/pnm_brng/v_pnm_brng',$data);


    }

    function list_trx_pnm_brng_mst(){
        $list = $this->m_persediaan->get_trx_pnm_brng_mst_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.Q.A.6';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint  = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView   = isset($datadtl['dtl_akses']['a_view'])   && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput  = isset($datadtl['dtl_akses']['a_input'])  && trim($datadtl['dtl_akses']['a_input']) === 't';

        /* ====== PERIODE CLOSING TERAKHIR (untuk disable tombol Update) ====== */
        $closingPeriode = $this->m_persediaan->q_periode_closing_last()->getRowArray();
        $closingLabel   = '';

        if (!empty($closingPeriode)) {
            $closingLabel = trim((string) ($closingPeriode['periode'] ?? ''));

            if ($closingLabel === '') {
                $closingLabel = trim((string) ($closingPeriode['tanggal_akhir'] ?? ''));
            }
        }

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status    = strtoupper(trim($lm->status));
            $docno     = trim($lm->docno);
            $docnoHex  = bin2hex($docno);

            /* docdate transaksi masuk periode yang sudah ditutup? */
            $isClosed = $this->m_persediaan->isPeriodeClosed(trim((string) $lm->docdate), $closingPeriode);

            $updateBtn = '';
            $detailBtn = '';
            $jurnalBtn = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && trim($lm->pemohon) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) !== 'DITARIK '
            ) {

                if ($isClosed) {

                    /* docdate sudah masuk periode closing -> Update tidak dapat diklik */
                    $closedTitle = 'Tidak dapat diupdate, periode sudah ditutup.';

                    if ($closingLabel !== '') {
                        $closedTitle = 'Tidak dapat diupdate, periode ' . $closingLabel . ' sudah ditutup.';
                    }

                    $closedTitleEsc = htmlspecialchars($closedTitle, ENT_QUOTES, 'UTF-8');

                    $updateBtn = '
                        <span class="dropdown-item bg-warning disabled pnm-item-closed"
                              role="button"
                              aria-disabled="true"
                              title="' . $closedTitleEsc . '"
                              style="cursor:not-allowed;pointer-events:auto;opacity:.65;">
                            <i class="fa fa-edit"></i> Update
                            <small style="font-weight:700;">(Periode Tutup)</small>
                        </span>';
                } else {

                    $updateBtn = '
                        <a class="dropdown-item bg-warning"
                        href="' . base_url('persediaan/trans/updatepnmBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '"
                        onclick="return guardPeriodeTutup(event) && confirm(\'Update Penerimaan Barang : ' . $docno . '\')">
                            <i class="fa fa-edit"></i> Update
                        </a>';
                }
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item pnm-act-detail"
                       style="background-color:#3badf6;"
                       href="' . base_url('persediaan/trans/detail_pnm_brng_mst') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '">
                        <i class="fa fa-eye"></i> Detail Penerimaan Barang
                    </a>';
            }

            if ($canView) {
                /* tombol jurnal: dibuka oleh modal #modalLaporanJurnalPnm
                   (handler .pnm-act-jurnal pada pnm_brng_list.js) */
                $jurnalBtn = '
                    <a class="dropdown-item pnm-act-jurnal"
                       style="background-color:#e7f0ff;"
                       href="#" data-docno="' . htmlspecialchars($docno, ENT_QUOTES, 'UTF-8') . '">
                        <i class="fa fa-book"></i> Laporan Jurnal Transaksi
                    </a>';
            }


            // =========================
            // RULE STATUS
            // =========================
            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                /* LPB-style: Detail + Jurnal */
                if ($canView)  $menuContent .= $detailBtn;
                $menuContent .= $jurnalBtn;

            } else {

                /* urutan: Detail -> Laporan Jurnal -> Update */
                if ($canView)   $menuContent .= $detailBtn;
                $menuContent .= $jurnalBtn;
                if ($canUpdate) $menuContent .= $updateBtn;
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

            // =========================
            // STATUS BADGE
            // =========================
            /* badge Bootstrap, konsisten dengan halaman LPB */
            $badgeMap = array(
                'F' => 'badge-info',       // FINAL USER
                'O' => 'badge-primary',    // FINAL TRANSACTION
                'P' => 'badge-success',    // CETAK/PRINT
                'A' => 'badge-primary',    // APPROVED
                'D' => 'badge-danger',     // DISAPPROVED
                'E' => 'badge-warning',    // REVISION/EDITING
                'I' => 'badge-secondary',  // DRAFT
            );
            $badgeClass = isset($badgeMap[$status]) ? $badgeMap[$status] : 'badge-secondary';
            $statusText = trim((string) $lm->nmstatus) !== '' ? trim($lm->nmstatus) : $status;
            $statusBadge = '<div class="text-center"><span style="font-size:12px" class="badge '
                . $badgeClass . ' w-100">'
                . htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') . '</span></div>';

            $cabangText = trim((string) $lm->nmbranch) !== '' ? trim($lm->nmbranch) : trim((string) $lm->cabang);
            $bagianText = trim((string) $lm->nmcostcenter) !== '' ? trim($lm->nmcostcenter) : trim((string) $lm->idcostcenter);

            // =========================
            // ROW DATA (urutan harus sama dengan <th> view)
            // =========================
            $row[] = $no;
            $row[] = $dropdownMenu;   // Action (kolom ke-2, seperti LPB)
            $row[] = htmlspecialchars($docno, ENT_QUOTES, 'UTF-8');
            $row[] = date('d-m-Y', strtotime(trim($lm->docdate)));
            $row[] = htmlspecialchars($cabangText, ENT_QUOTES, 'UTF-8');
            $row[] = htmlspecialchars($bagianText, ENT_QUOTES, 'UTF-8');
            $row[] = htmlspecialchars(trim((string) $lm->docref), ENT_QUOTES, 'UTF-8');
            $row[] = number_format((float) $lm->total, 2, '.', ',');
            $row[] = $statusBadge;


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->trx_pnm_brng_mst_view_count_all(),
            "recordsFiltered" => $this->m_persediaan->trx_pnm_brng_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function add_pnm_brng_mst(){
        /* Penambahan Squence */
        $data['title']="Input Penerimaan Barang";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.Q.A.6'; $versirelease='I.Q.A.6/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.Q.A.6'";
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

        /* Pesan validasi COA dari proses Simpan Final Data (final_pnm_barang) */
        $flashCoa = session()->getFlashdata('coa_error');
        if (! empty($flashCoa)) {
            $data['message'] = "<div class='alert alert-danger'>" . esc($flashCoa) . "</div>";
        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_persediaan->q_tmp_pnm_brng_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));


        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }
        // =================================

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_persediaan->q_tmp_pnm_brng_mst($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('persediaan/pnm_brng/v_add_pnm_brg',$data);
    }


    /**
     * Simpan detail Pemakaian Barang.
     * Dibungkus try/catch supaya error database (termasuk error COA dari trigger
     * sc_tmp.tr_tmp_pnm_brng_mst / sc_trx.fn_sync_pnm_brng_dtl_to_transaction)
     * tampil sebagai pesan JSON yang bisa dibaca user, bukan halaman error database.
     */
    public function save_pnm_brng_detail()
    {
        try {
            return $this->save_pnm_brng_detail_process();
        } catch (\Throwable $ex) {
            try {
                $this->db->transRollback();
            } catch (\Throwable $e) {
            }

            $pesanDb = trim(explode("\n", $ex->getMessage())[0]);
            $isCoa   = (stripos($pesanDb, 'COA') !== false);

            return $this->response->setJSON([
                'success' => false,
                'code'    => $isCoa ? 'COA' : 'DB',
                'message' => $isCoa
                    ? 'Data tidak dapat disimpan. ' . $pesanDb . '. Lengkapi COA pada master terlebih dahulu.'
                    : 'Data gagal disimpan. ' . $pesanDb,
            ]);
        }
    }

    private function save_pnm_brng_detail_process()
    {
        $nama = trim($this->session->get('nama'));
        $docno  = strtoupper($this->request->getPost('docno'));
        $idurut = $this->request->getPost('idurut');
        $docref = strtoupper(trim((string) $this->request->getPost('docref')));

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Docno tidak ditemukan'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderHeader = $db->table('sc_tmp.pnm_brng_mst');

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
                'doctype'    => 'PNMBRG',
                'docref'     => $docref,
                'docdate'    => trim($this->request->getPost('docdate')),
                'cabang'     => trim($this->request->getPost('cabang')),
//                'cabang_sent'=> trim($this->request->getPost('cabang_sent')),
                'pemohon'    => strtoupper(trim($this->request->getPost('pemohon'))),
                'estpakai'   => $this->request->getPost('estpakai'),
                'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
                'idcostcenter'    => strtoupper(trim($this->request->getPost('idcostcenter'))),
//                'idlocation_to'      => strtoupper(trim($this->request->getPost('idlocation_to'))),
//                'idlocation_transit' => strtoupper(trim($this->request->getPost('idlocation_transit'))),
                'status'     => 'E',
                // Grid input cepat mengirim header_description terpisah dari
                // description detail. Bila tidak dikirim (form modal lama),
                // perilaku tetap sama seperti sebelumnya.
                'description' => strtoupper(trim((string) ($this->request->getPost('header_description') ?? $this->request->getPost('description')))),
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
        $uniqueid = $this->request->getPost('iduniq');
        $idbarang    = trim($this->request->getPost('idbarang'));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $idlocation    = trim($this->request->getPost('idlocation_dtl'));
        $qtystock    = trim($this->request->getPost('qtystock'));
        $batch    = trim($this->request->getPost('batch'));
        $qty         = (float) $this->request->getPost('qty');
        $val         = (float) $this->request->getPost('val');
        $valsum         = (float) $this->request->getPost('valsum');
        $description = strtoupper(trim($this->request->getPost('description')));

        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        // =========================
        // CEK COA MASTER (BARANG & COST CENTER)
        // =========================
        // Validasi sama dengan trigger sc_trx.fn_sync_pnm_brng_dtl_to_transaction():
        // COA persediaan dari master barang, COA biaya dari master cost center.
        // Ditampilkan lebih awal supaya user tahu penyebabnya sejak simpan detail.
        $hdrRow = $db->table('sc_tmp.pnm_brng_mst')
            ->select('idcostcenter')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();
        $idcostcenterHdr = trim((string) ($hdrRow['idcostcenter'] ?? ''));

        $cekCoa = $this->m_persediaan->q_cek_coa_item_pnm_brng($idbarang, $idcostcenterHdr)->getResultArray();
        if (count($cekCoa) > 0) {
            $pesanCoa = array();
            foreach ($cekCoa as $rowCoa) {
                if (strtoupper(trim($rowCoa['jenis'])) === 'BARANG') {
                    $nmCoa = trim($rowCoa['nama']);
                    $pesanCoa[] = 'COA Persediaan belum diisi pada master barang: ' . trim($rowCoa['kode'])
                        . ($nmCoa !== '' ? ' - ' . $nmCoa : '');
                } else {
                    $ccCoa = trim($rowCoa['kode']);
                    $pesanCoa[] = ($ccCoa !== '')
                        ? 'COA Biaya belum diisi pada master cost center: ' . $ccCoa
                        : 'Cost center belum dipilih pada header Pemakaian Barang';
                }
            }

            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'code'    => 'COA',
                'message' => implode('. ', $pesanCoa) . '. Lengkapi COA pada master terlebih dahulu.',
            ]);
        }

        $builderDetail = $db->table('sc_tmp.pnm_brng_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('qty', $qty)
            ->where('inputby', $nama)
            ->where('description', $description);

        if ($idurut) {
            // idurut = primary key detail (bigint), iduniq = hash SHA-256.
            // Perbandingan bigint <> text ditolak PostgreSQL pada proses update,
            // sehingga pengecualian baris yang sedang diedit memakai idurut.
            $builderDuplicate->where('idurut !=', (int) $idurut);
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
                ->where('iduniq', $uniqueid)
                ->update([
                    'doctype'    => 'PNMBRG',
                    'docref'     => $docref,
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'idlocation'    => $idlocation,
                    'unit'        => $unit,
                    'batch'         => $batch,
                    'qty'         => $qty,
                    'val'         => $val,
                    'valsum'         => $valsum,
                    'description' => $description,
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
                'doctype'    => 'PNMBRG',
                'docref'     => $docref,
                'idlocation'    => $idlocation,
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'batch'         => $batch,
                'unit'        => $unit,
                'qty'         => $qty,
                'val'         => $val,
                'valsum'         => $valsum,
                'description' => $description,
                'inputby'     => $nama,
                'inputdate'   => $inputdate,
                'iduniq'      => $uniqueid
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


    function showing_pnm_brng_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno' and inputby='$nama'";
        $data = $this->m_persediaan->q_tmp_pnm_brng_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_tmp_pnm_brng_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_tmp_pnm_brng_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    /**
     * Data detail Penerimaan Barang (draft) untuk grid input cepat.
     * Mengembalikan seluruh kolom yang dibutuhkan grid, termasuk iduniq
     * (dipakai endpoint update save_pnm_brng_detail), plus grand total.
     * Read-only: tidak mengubah struktur maupun isi tabel.
     */
    public function list_pnm_brng_dtl_grid()
    {
        $nama = trim($this->session->get('nama'));

        $param = " and coalesce(inputby,'') = " . $this->db->escape($nama);
        $rows  = $this->m_persediaan->q_tmp_pnm_brng_dtl($param)->getResultArray();
        // q_tmp_pnm_brng_dtl memakai order by idurut desc; grid input lebih
        // mudah dibaca urut dari baris terlama ke terbaru (baris baru di bawah).
        $rows  = array_reverse($rows);

        $items      = array();
        $totalQty   = 0;
        $totalNilai = 0;

        foreach ($rows as $r) {
            $qty    = (float) ($r['qty'] ?? 0);
            $val    = (float) ($r['val'] ?? 0);
            $valsum = (float) ($r['valsum'] ?? 0);

            $items[] = array(
                'idurut'      => trim((string) ($r['idurut'] ?? '')),
                'iduniq'      => trim((string) ($r['iduniq'] ?? '')),
                'idbarang'    => trim((string) ($r['idbarang'] ?? '')),
                'nmbarang'    => trim((string) ($r['nmbarang'] ?? '')),
                'idlocation'  => trim((string) ($r['idlocation'] ?? '')),
                'batch'       => trim((string) ($r['batch'] ?? '')),
                'unit'        => trim((string) ($r['unit'] ?? '')),
                'qty'         => $qty,
                'val'         => $val,
                'valsum'      => $valsum,
                'description' => trim((string) ($r['description'] ?? '')),
            );

            $totalQty   += $qty;
            $totalNilai += $valsum;
        }

        return $this->response->setJSON(array(
            'success'     => true,
            'items'       => $items,
            'total_qty'   => $totalQty,
            'total_nilai' => $totalNilai,
        ));
    }

    /**
     * Master gudang untuk dropdown grid detail (read-only).
     * Hanya memakai SELECT pada sc_mst.mlocation, tidak mengubah data.
     */
    public function list_location_pnm_brng()
    {
        $rows = $this->db->query(
            "SELECT TRIM(idlocation) AS idlocation,
                    TRIM(nmlocation) AS nmlocation,
                    TRIM(COALESCE(idbranchjob,'')) AS idbranchjob
               FROM sc_mst.mlocation
              ORDER BY nmlocation ASC"
        )->getResultArray();

        $branch = trim((string) $this->request->getPost('branch'));
        $items  = $rows;

        // Prioritaskan gudang milik cabang; bila tidak ada, tampilkan semua
        if ($branch !== '') {
            $filtered = array_values(array_filter($rows, function ($row) use ($branch) {
                return trim((string) $row['idbranchjob']) === $branch;
            }));

            if (count($filtered) > 0) {
                $items = $filtered;
            }
        }

        return $this->response->setJSON(array(
            'success' => true,
            'items'   => $items,
        ));
    }

    /**
     * Pencarian barang untuk dropdown ID Barang pada grid detail (read-only).
     * Mengembalikan idbarang, nmbarang, unit default, dan harga terakhir.
     */
    public function search_item_pnm_brng()
    {
        $term = strtoupper(trim((string) $this->request->getPost('term')));
        $page = (int) $this->request->getPost('page');

        if ($page < 1) {
            $page = 1;
        }

        $limit  = 30;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT TRIM(idbarang) AS idbarang,
                       TRIM(nmbarang) AS nmbarang,
                       TRIM(COALESCE(unit,'')) AS unit,
                       COALESCE(lastprice,0) AS lastprice
                  FROM sc_mst.mbarang
                 WHERE COALESCE(chold,'NO') <> 'YES'";

        if ($term !== '') {
            $esc = $this->db->escapeLikeString($term);
            $sql .= " AND (UPPER(TRIM(idbarang)) LIKE '%$esc%' OR UPPER(TRIM(nmbarang)) LIKE '%$esc%')";
        }

        $sql .= " ORDER BY nmbarang ASC LIMIT " . ($limit + 1) . " OFFSET " . $offset;

        $rows = $this->db->query($sql)->getResultArray();
        $more = count($rows) > $limit;

        if ($more) {
            $rows = array_slice($rows, 0, $limit);
        }

        return $this->response->setJSON(array(
            'success' => true,
            'items'   => $rows,
            'more'    => $more,
        ));
    }

    /**
     * Auto-fill Nama Barang, Satuan, dan Harga pada grid detail (read-only).
     */
    public function get_item_pnm_brng()
    {
        $idbarang = trim((string) $this->request->getPost('idbarang'));

        if ($idbarang === '') {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'ID barang tidak boleh kosong',
            ));
        }

        $item = $this->db->query(
            "SELECT TRIM(idbarang) AS idbarang,
                    TRIM(nmbarang) AS nmbarang,
                    TRIM(COALESCE(unit,'')) AS unit,
                    COALESCE(lastprice,0) AS lastprice
               FROM sc_mst.mbarang
              WHERE TRIM(idbarang) = ?
              LIMIT 1",
            array($idbarang)
        )->getRowArray();

        if (!$item) {
            return $this->response->setJSON(array(
                'success' => false,
                'message' => 'Barang tidak ditemukan pada master.',
            ));
        }

        $units = $this->db->query(
            "SELECT TRIM(idunit) AS idunit,
                    TRIM(COALESCE(cdefault,'')) AS cdefault,
                    COALESCE(conv_value,1) AS conv_value
               FROM sc_mst.mbarang_unit
              WHERE TRIM(idbarang) = ?
              ORDER BY TRIM(COALESCE(cdefault,'')) DESC, TRIM(idunit) ASC",
            array($idbarang)
        )->getResultArray();

        return $this->response->setJSON(array(
            'success' => true,
            'data'    => $item,
            'units'   => $units,
        ));
    }

    /**
     * Daftar Batch / Spec berdasarkan ID Barang (read-only).
     * Sumber: sc_mst.batch. Dipakai Select2 kolom Batch/Spec pada grid detail.
     */
    public function list_batch_pnm_brng()
    {
        $idbarang = trim((string) $this->request->getPost('idbarang'));
        $term     = strtoupper(trim((string) $this->request->getPost('term')));
        $page     = (int) $this->request->getPost('page');

        if ($page < 1) {
            $page = 1;
        }

        if ($idbarang === '') {
            return $this->response->setJSON(array(
                'success' => true,
                'items'   => array(),
                'more'    => false,
                'message' => 'Pilih barang terlebih dahulu',
            ));
        }

        $limit  = 30;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT TRIM(batch) AS batch
                  FROM sc_mst.batch
                 WHERE TRIM(idbarang) = ?
                   AND COALESCE(TRIM(batch), '') <> ''";

        $binds = array($idbarang);

        if ($term !== '') {
            $sql .= " AND UPPER(TRIM(batch)) LIKE ?";
            $binds[] = '%' . $this->db->escapeLikeString($term) . '%';
        }

        $sql .= " GROUP BY TRIM(batch)
                  ORDER BY TRIM(batch) ASC
                  LIMIT " . ($limit + 1) . " OFFSET " . $offset;

        $rows = $this->db->query($sql, $binds)->getResultArray();
        $more = count($rows) > $limit;

        if ($more) {
            $rows = array_slice($rows, 0, $limit);
        }

        $items = array();

        foreach ($rows as $row) {
            $batch = trim((string) ($row['batch'] ?? ''));

            if ($batch === '') {
                continue;
            }

            $items[] = array(
                'id'    => $batch,
                'text'  => $batch,
                'batch' => $batch,
            );
        }

        return $this->response->setJSON(array(
            'success' => true,
            'items'   => $items,
            'more'    => $more,
        ));
    }

    // UNTUK TRX
    function showing_pnm_brng_mst(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_persediaan->q_trx_pnm_brng_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_trx_pnm_brng_mst_dtl()
    {
        $id = $this->request->getGet('id');

        $data = $this->m_persediaan
            ->q_trx_pnm_brng_mst_dtl(" and idurut='$id'");

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function get_summary_pnm() {
        $docno = trim($this->request->getPost('docno'));

        // Query sum ke database
        $summary = $this->m_persediaan->q_tmp_pnm_brng_dtl_summary(" and docno='$docno'")->getRowArray();

        echo json_encode([
            'total_qty'   => number_format($summary['total_qty'], 2, '.', ','),
            'total_nilai' => number_format($summary['valsum'], 4, '.', ',')
        ]);
    }

    function final_pnm_barang(){
        $nama = trim($this->session->get('nama'));

        /* ====== GUARD PERIODE TUTUP (defensif: periode bisa ditutup saat input berjalan) ====== */
        $logindate = trim($this->session->get('logindate'));
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat menyimpan final transaksi.'
            );
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }
        // ========================================================================================
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama'";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_persediaan->q_tmp_pnm_brng_mst($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_persediaan->q_tmp_pnm_brng_mst($paramdtl);
        $cek2 = $this->m_persediaan->q_tmp_pnm_brng_mst($paramdtl2);


        $builder = $this->db->table(' sc_tmp.pnm_brng_mst');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.Q.A.6');
        $builder_trxerror->delete();


        if ($status==='E' and $cek->getNumRows() <= 0)
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.Q.A.6',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/persediaan/trans/add_pnm_brg_mst'));
        } else {
            /* GUARD COA: cegah error dari trigger sc_tmp.tr_tmp_pnm_brng_mst()
               (COA persediaan master barang / COA biaya master cost center belum diisi) */
            $dtlCoa = $this->m_persediaan->q_cek_coa_pnm_brng($nama)->getResultArray();
            if (count($dtlCoa) > 0) {
                $listBarang = [];
                $listCc = [];
                foreach ($dtlCoa as $rowCoa) {
                    if (strtoupper(trim($rowCoa['jenis'])) === 'BARANG') {
                        $listBarang[] = trim($rowCoa['kode']);
                    } else {
                        $listCc[] = trim($rowCoa['kode']);
                    }
                }
                $pesan = 'Data belum dapat disimpan. ';
                if (count($listBarang) > 0) {
                    $pesan .= 'COA Persediaan belum diisi pada master barang: ' . implode(', ', $listBarang) . '. ';
                }
                $listCcKode = array_values(array_filter($listCc, function ($v) {
                    return trim((string) $v) !== '';
                }));
                if (count($listCcKode) > 0) {
                    $pesan .= 'COA Biaya belum diisi pada master cost center: ' . implode(', ', $listCcKode) . '. ';
                }
                if (count($listCcKode) < count($listCc)) {
                    $pesan .= 'Cost center belum dipilih pada header Pemakaian Barang. ';
                }
                $pesan .= 'Lengkapi COA pada master terlebih dahulu.';

                session()->setFlashdata('coa_error', $pesan);
                return redirect()->to(base_url('/persediaan/trans/add_pnm_brng_mst'));
            }


// Ambil dari request POST
            //$pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $keterangan = strtoupper(trim($this->request->getPost('keterangan')));
            $docrefPost = strtoupper(trim((string) $this->request->getPost('docref')));

            // docref header (referensi) disimpan bareng keterangan saat Simpan Final.
            // Prioritas nilai dari form; fallback ke docref header yang sudah tersimpan.
            $hdrDocref = $this->m_persediaan->q_tmp_pnm_brng_mst($param)->getRowArray();
            $docrefHdr = strtoupper(trim((string) ($hdrDocref['docref'] ?? '')));
            $docrefFinal = ($docrefPost !== '') ? $docrefPost : $docrefHdr;

            // Sinkronkan docref detail dari docref header supaya ref_docno
            // pada sc_trx.transaction_hd tidak NULL (not-null constraint).
            if ($docrefFinal !== '') {
                $this->db->table('sc_tmp.pnm_brng_dtl')
                    ->where('inputby', $nama)
                    ->where("coalesce(trim(docref), '') = ''")
                    ->set('docref', $docrefFinal)
                    ->update();
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'description'        => $keterangan,
            ];
            if ($docrefFinal !== '') {
                $updateHeader['docref'] = $docrefFinal;
            }

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama
            );
            $builder->where('inputby',$nama);
            try {
                $simpanFinal = $builder->update($info);
            } catch (\Throwable $ex) {
                /* Tampilkan pesan yang mudah dibaca, bukan halaman error database */
                $pesanDb = trim(explode("\n", $ex->getMessage())[0]);
                $pesanDb = preg_replace('/^pg_query\(\):\s*Query failed:\s*/i', '', $pesanDb);
                $pesanDb = preg_replace('/^ERROR:\s*/i', '', $pesanDb);
                session()->setFlashdata('coa_error', 'Data gagal disimpan. ' . $pesanDb);
                return redirect()->to(base_url('/persediaan/trans/add_pnm_brng_mst'));
            }
            if ($simpanFinal) {
                $paramerror=" and userid='$nama' and modul='I.Q.A.6'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/persediaan/trans/pnm_barang'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.Q.A.6',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/persediaan/trans/pnm_barang'));
            }



        }

    }

    /**
     * Cek kelengkapan COA (AJAX) sebelum Simpan Final Data Pemakaian Barang.
     * Mengembalikan daftar barang tanpa COA persediaan dan cost center tanpa COA biaya.
     */
    function check_coa_pnm_brng(){
        $nama = trim($this->session->get('nama'));
        $rows = $this->m_persediaan->q_cek_coa_pnm_brng($nama)->getResultArray();

        $barang = array();
        $costcenter = array();
        foreach ($rows as $row) {
            if (strtoupper(trim($row['jenis'])) === 'BARANG') {
                $barang[] = array(
                    'kode' => trim($row['kode']),
                    'nama' => trim($row['nama']),
                );
            } else {
                $costcenter[] = array(
                    'kode' => trim($row['kode']),
                );
            }
        }

        return $this->response->setJSON(array(
            'status'     => (count($barang) === 0 && count($costcenter) === 0),
            'barang'     => $barang,
            'costcenter' => $costcenter,
        ));
    }


    function list_tmp_pnm_brng_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_tmp_pnm_brng_dtl_view($docno);
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
            $row[] = $lm->idlocation;
            $row[] = $lm->batch;
            $row[] = $lm->unit;
// Menambahkan number_format: (variabel, jumlah desimal, pemisah desimal, pemisah ribuan)
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->val, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->valsum, 4, '.', ',') . '</div>';
            $row[] = $lm->description;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_persediaan->get_tmp_pnm_brng_dtl_view($docno),
            "recordsFiltered" => $this->m_persediaan->tmp_pnm_brng_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function updatepnmBrg()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_pnm_brng_mst($param)->getRowArray();
        $status = trim($dtl['status']);

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_persediaan->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }

        /* ====== GUARD PERIODE CLOSING BERDASARKAN DOCDATE TRANSAKSI ====== */
        $closingPeriode = $this->m_persediaan->q_periode_closing_last()->getRowArray();
        $docdateTrans   = trim((string) ($dtl['docdate'] ?? ''));

        if ($this->m_persediaan->isPeriodeClosed($docdateTrans, $closingPeriode)) {
            $closedLabel = trim((string) ($closingPeriode['periode'] ?? ''));

            session()->setFlashdata('periode_error',
                'Tanggal transaksi ' . $docdateTrans
                . ($closedLabel !== '' ? ' berada pada periode ' . $closedLabel : '')
                . ' yang sudah TUTUP. Tidak dapat melakukan update.'
            );

            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }

        if ($status === 'F' || $status === 'P') {

            $info = array(
                'status' => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama,
            );
            $builder = $this->db->table('sc_trx.pnm_brng_mst');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('persediaan/trans/add_pnm_brng_mst'));
        } else {
            // Jika status bukan 'F', redirect ke halaman list
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }
    }
    function detail_pnm_brng_mst()
    {
        /* Penambahan Squence */
        $data['title'] = "Detail Penerimaan Barang";
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama = trim($this->session->get('nama'));

        $docno = trim((string) $this->request->getGet('docno'));

        if ($docno === '') {
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }

        $kodemenu = 'I.Q.A.6';
        $versirelease = 'I.Q.A.6/01';
        $releasedate = date('2025-04-12 00:00:00');
        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;
        $data['nama'] = $nama;
        /* END CODE UNTUK VERSI */

        $paramerror = " and userid='$nama' and modul='I.Q.A.6'";
        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();

        if (isset($dtlerror['description'])) { $errordesc = trim($dtlerror['description']); } else { $errordesc = ''; }
        if (isset($dtlerror['nomorakhir1'])) { $nomorakhir1 = trim($dtlerror['nomorakhir1']); } else { $nomorakhir1 = ''; }
        if (isset($dtlerror['errorcode'])) { $errorcode = trim($dtlerror['errorcode']); } else { $errorcode = ''; }

        if ($count_err > 0 and $errordesc) {
            if ($dtlerror['errorcode'] == 0) {
                $data['message'] = "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message'] = "<div class='alert alert-info'>$errordesc</div>";
            }
        } else {
            if ($errorcode == '0') {
                $data['message'] = "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message'] = "";
            }
        }

        /* docno dikirim dalam bentuk hex dari halaman list */
        $decoded_docno = (ctype_xdigit($docno) && strlen($docno) % 2 === 0)
            ? (string) hex2bin($docno)
            : $docno;

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        /* HEADER: sc_trx.pnm_brng_mst (+ nama cabang / cost center / pemohon / status / total) */
        $header = $this->m_persediaan->q_detail_pnm_brng_mst($decoded_docno)->getRowArray();

        if (!$header) {
            return redirect()->to(base_url('persediaan/trans/pnm_barang'))
                ->with('error', 'Transaksi Penerimaan Barang tidak ditemukan.');
        }

        /* DETAIL: sc_trx.pnm_brng_dtl (+ nama gudang) */
        $detail = $this->m_persediaan->q_detail_pnm_brng_dtl($decoded_docno)->getResultArray();

        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $header;
        $data['dtldetail'] = $detail;

        return $this->template->render('persediaan/pnm_brng/v_detail_pnm_brng', $data);
    }

    public function getBranch_pnm_brng()
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
        $prefix = trim($konfigurasiUmum[0]['pnmbarang']) ?? '';
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

    public function getNextSuffix_pnm_brng()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.pnm_brng_mst')
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

    public function delete_pnm_brng()
    {
        $ids = $this->request->getPost('ids');

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_tmp.pnm_brng_dtl');

        try {

            $db->transBegin(); // START TRANSACTION

            $builder->whereIn('idurut', $ids)->delete();

            // Cek apakah semua benar-benar terhapus
            if ($db->affectedRows() !== count($ids)) {
                throw new \Exception('Sebagian data gagal dihapus');
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            $db->transCommit(); // COMMIT jika sukses semua

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semua data berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback(); // ROLLBACK jika ada error

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }


    function clear_pnm_brng()
    {
        $url = base_url('persediaan/trans/pnm_barang');
        $nama = trim($this->session->get('nama'));

        if ($nama === '') {
            return redirect()->to($url)
                ->with('error', 'Clear gagal: session nama kosong.');
        }

        $param = " AND COALESCE(TRIM(inputby), '') = "
            . $this->db->escape($nama);

        $dtl = $this->m_persediaan->q_tmp_pnm_brng_mst($param);

        if (!$dtl) {
            return redirect()->to($url)
                ->with('error', 'Clear gagal: query header tidak menghasilkan data.');
        }

        $header = $dtl->getRowArray();

        if (empty($header)) {
            return redirect()->to($url)
                ->with('error', 'Clear gagal: header PNM Barang tidak ditemukan untuk user ' . $nama . '.');
        }

        $status = trim($header['status'] ?? '');
        $docno  = trim($header['docno'] ?? '');

        $builder = $this->db->table('sc_tmp.pnm_brng_mst');
        $builderDtl = $this->db->table('sc_tmp.pnm_brng_dtl');

        if ($status === 'I') {
            $this->db->transBegin();

            try {
                $builderDtl
                    ->where('inputby', $nama)
                    ->delete();

                $error = $this->db->error();

                if (!empty($error['code'])) {
                    throw new \RuntimeException(
                        'Gagal menghapus detail: ' . $error['message']
                    );
                }

                $jumlah = $this->db->affectedRows();

                if ($this->db->transStatus() === false) {
                    throw new \RuntimeException(
                        'Transaksi penghapusan detail gagal.'
                    );
                }

                $this->db->transCommit();

                return redirect()->to($url)->with(
                    'success',
                    "Clear berhasil. Detail terhapus: {$jumlah}. Header tetap dipertahankan."
                );

            } catch (\Throwable $e) {
                $this->db->transRollback();

                log_message('error', 'clear_pnm_brng: ' . $e->getMessage());

                return redirect()->to($url)->with(
                    'error',
                    'Clear gagal menghapus detail: ' . $e->getMessage()
                );
            }
        }

        if ($status === 'E') {
            $builder
                ->where('inputby', $nama)
                ->where('docno', $docno)
                ->update(['status' => 'C']);

            $error = $this->db->error();

            if (!empty($error['code'])) {
                log_message('error', 'clear_pnm_brng update: ' . $error['message']);

                return redirect()->to($url)->with(
                    'error',
                    'Gagal mengubah status menjadi C: ' . $error['message']
                );
            }

            if ($this->db->affectedRows() < 1) {
                return redirect()->to($url)->with(
                    'error',
                    "Clear gagal: header {$docno} tidak berubah. Periksa status dan trigger."
                );
            }

            return redirect()->to($url)
                ->with('success', "Clear berhasil. Dokumen {$docno} dibatalkan.");
        }

        return redirect()->to($url)->with(
            'error',
            "Clear gagal: status '{$status}' tidak didukung."
        );
    }


}
