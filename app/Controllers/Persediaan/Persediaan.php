<?php
/*
 * select * from sc_mst.trxtype;
--DELETE FROM sc_mst.trxtype WHERE JENISTRX='I.Q.A.1';
insert into sc_mst.trxtype
(kdtrx,jenistrx,uraian)
values
('I','I.Q.A.1','DRAFT'),
('E','I.Q.A.1','REVISION/EDITING'),
('F','I.Q.A.1','FINAL USER'),
('A','I.Q.A.1','APPROVED'),
('D','I.Q.A.1','DISAPPROVED'),
('P','I.Q.A.1','CETAK/PRINT'),
('O','I.Q.A.1','FINAL TRANSACTION');
 *
 * */

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

    function showing_spk_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
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
                    onclick="return confirm(\'Update This SPK Transfers : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('persediaan/trans/detailSpkTransfers') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail PP : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('persediaan/trans/show_pp') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print PP : ' . $docno . '\')">
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
            $row[] = $lm->docdate;
            $row[] = $lm->idlocation_from;
            $row[] = $lm->idlocation_to;
            $row[] = $lm->idlocation_transit;
            $row[] = $lm->nmstatus;
            $row[] = $lm->keterangan;


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
                    onclick="return confirm(\'Update Transfers Location : ' . $docno . '\')">
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
                    onclick="return confirm(\'Print Transfer Lokasi : ' . $docno . '\')">
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
            $row[] = $lm->docdate;
            $row[] = $lm->idlocation_from;
            $row[] = $lm->idlocation_to;
            $row[] = $lm->idlocation_transit;
            $row[] = $lm->nmstatus;
            $row[] = $lm->description;


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

        $builderHeader = $db->table('sc_tmp.transfer_location_mst');

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
                'doctype'    => 'TRANSFER LOCATION',
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'cabang'     => trim($this->request->getPost('cabang')),
                'cabang_sent'=> trim($this->request->getPost('cabang_sent')),
                'pemohon'    => strtoupper(trim($this->request->getPost('pemohon'))),
                'estpakai'   => $this->request->getPost('estpakai'),
                'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
                'idlocation_to'      => strtoupper(trim($this->request->getPost('idlocation_to'))),
                'idlocation_transit' => strtoupper(trim($this->request->getPost('idlocation_transit'))),
                'status'     => 'E',
                'description' => strtoupper(trim($this->request->getPost('description'))),
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
        $idbarang    = trim($this->request->getPost('idbarang'));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $qtystock    = trim($this->request->getPost('qtystock'));
        $qty         = (float) $this->request->getPost('qty');
        $description = strtoupper(trim($this->request->getPost('description')));

        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.transfer_location_dtl');

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
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'unit'        => $unit,
                    'qty'         => $qty,
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
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'qty'         => $qty,
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


    function showing_transfer_location_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
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

        $data = $this->m_persediaan
            ->q_tmp_transfer_location_dtl(" and idurut='$id'");

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

    function list_tmp_transfer_location_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_persediaan->get_tmp_transfer_location_dtl_view($docno);
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
            "recordsTotal" => $this->m_persediaan->get_tmp_transfer_location_dtl_view($docno),
            "recordsFiltered" => $this->m_persediaan->tmp_transfer_location_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function updateTransfersLocation()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_transfer_location_mst($param)->getRowArray();
        $status = trim($dtl['status']);

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

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'        => $logindate,
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


    function clearTransferLokasi()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_persediaan->q_tmp_transfer_location_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('persediaan/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.transfer_location_mst');
        $builder_dtl = $this->db->table('sc_tmp.transfer_location_dtl_mst');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('persediaan/trans/transfer_lokasi'));
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
                    onclick="return confirm(\'Update Transfers Location : ' . $docno . '\')">
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
                    onclick="return confirm(\'Print Transfer Lokasi : ' . $docno . '\')">
                        <i class="fa fa-print"></i> Print 
                    </a>';
            }

            if ($canDelete) {
                $deleteBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#FF7C7CD6;" 
                    href="' . base_url('persediaan/trans/cancelAjustmentStock') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Batal Transaksi : ' . $docno . '\')">
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
            $row[] = $lm->docdate;
            $row[] = $lm->docref;
            $row[] = $lm->cabang;
            $row[] = $lm->docdate;
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
            $row[] = '<div class="text-center">'.$statusBadge.'</div>';
            $row[] = $lm->description;
            $row[] = $lm->inputby;


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

        $builderHeader = $db->table('sc_tmp.ajustment_stock_mst');

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
                'doctype'    => 'AJUSTMENT_STOCK',
                'docref'     => trim($this->request->getPost('docref')),
                'docdate'    => trim($this->request->getPost('docdate')),
                'cabang'     => trim($this->request->getPost('cabang')),
//                'cabang_sent'=> trim($this->request->getPost('cabang_sent')),
                'pemohon'    => strtoupper(trim($this->request->getPost('pemohon'))),
                //'estpakai'   => $this->request->getPost('estpakai'),
                //'idlocation_from'    => strtoupper(trim($this->request->getPost('idlocation_from'))),
//                'idlocation_to'      => strtoupper(trim($this->request->getPost('idlocation_to'))),
//                'idlocation_transit' => strtoupper(trim($this->request->getPost('idlocation_transit'))),
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

        $idlocation_dtl = strtoupper(trim($this->request->getPost('idlocation_dtl')));
        $idbarang = strtoupper(trim($this->request->getPost('idbarang')));
        $batch = strtoupper(trim($this->request->getPost('batch')));
        $nmbarang = strtoupper(trim($this->request->getPost('nmbarang')));
        $qtystock = (float) strtoupper(trim($this->request->getPost('qtystock')));
        $qty = strtoupper(trim($this->request->getPost('qty')));
        $unit = strtoupper(trim($this->request->getPost('unit')));
        $dk = strtoupper(trim($this->request->getPost('dk')));
        $currency = strtoupper(trim($this->request->getPost('currency')));
        $valqty = (float) strtoupper(trim($this->request->getPost('valqty')));
        $description = strtoupper(trim($this->request->getPost('description')));


        if (!$idbarang or !$dk) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih/Debit kredit belum dipilih!'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.ajustment_stock_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('unit', $unit)
            ->where('qty', $qty)
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

                    'idlocation'    => $idlocation_dtl,
                    'idbarang'    => $idbarang,
                    'batch'    => $batch,
                    'nmbarang'    => $nmbarang,
                    'unit'        => $unit,
                    'qtystock'         => $qtystock,
                    'qty'         => $qty,
                    'dk'         => $dk,
                    'currency'         => $currency,
                    'valqty'         => $valqty,
                    'description' => $description,
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s'),
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
                'idlocation'    => $idlocation_dtl,
                'idbarang'    => $idbarang,
                'batch'    => $batch,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'qtystock'         => $qtystock,
                'qty'         => $qty,
                'dk'         => $dk,
                'currency'         => $currency,
                'valqty'         => $valqty,
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


    function showing_ajustment_stock_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
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

        $data = $this->m_persediaan
            ->q_tmp_ajustment_stock_dtl(" and idurut='$id'");

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

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'        => $logindate,
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

        $db = \Config\Database::connect();
        $builder = $db->table('sc_tmp.ajustment_stock_dtl');

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
                trim($status) !== 'DITARIK '
            ) {

                $updateBtn = '
                    <a class="dropdown-item bg-warning" 
                    href="' . base_url('persediaan/trans/updatePmkBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update Pemakaian Barang : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('persediaan/trans/detail_pmk_brng_mst') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Pemakai Barang : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('persediaan/trans/showPrintPmkBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Pemakaian Barang : ' . $docno . '\')">
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
            $row[] = $lm->cabang;
            $row[] = $lm->idcostcenter;
            $row[] = $lm->docdate;

            $row[] = $lm->nmstatus;
            $row[] = $lm->description;
            $row[] = $lm->inputby;


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
                'description' => strtoupper(trim($this->request->getPost('description'))),
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
        $idbarang    = trim($this->request->getPost('idbarang'));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $unit        = strtoupper(trim($this->request->getPost('unit')));
        $idlocation    = trim($this->request->getPost('idlocation_dtl'));
        $qtystock    = trim($this->request->getPost('qtystock'));
        $batch    = trim($this->request->getPost('batch'));
        $qty         = (float) $this->request->getPost('qty');
        $description = strtoupper(trim($this->request->getPost('description')));

        if (!$idbarang) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item belum dipilih'
            ]);
        }

        $builderDetail = $db->table('sc_tmp.pmk_brng_dtl');

        // =========================
        // CEK DUPLIKASI
        // =========================
        $builderDuplicate = $builderDetail
            ->where('docno', $docno)
            ->where('idbarang', $idbarang)
            ->where('nmbarang', $nmbarang)
            ->where('unit', $unit)
            ->where('qtystock', $qty)
            ->where('qty', $qty)
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
                    'doctype'    => 'PMKBRG',
                    'idbarang'    => $idbarang,
                    'nmbarang'    => $nmbarang,
                    'idlocation'    => $idlocation,
                    'idcostcenter'    => strtoupper(trim($this->request->getPost('idcostcenter'))),
                    'unit'        => $unit,
                    'batch'         => $batch,
                    'qtystock'         => $qtystock,
                    'qty'         => $qty,
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
                'idlocation'    => $idlocation,
                'idcostcenter'    => strtoupper(trim($this->request->getPost('idcostcenter'))),
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'batch'         => $batch,
                'unit'        => $unit,
                'qtystock'         => $qtystock,
                'qty'         => $qty,
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


    function showing_pmk_brng_mst_tmp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
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


    function updatePmkBrg()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_persediaan->q_trx_pmk_brng_mst($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {

            $info = array(
                'status' => 'E',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateby' => $nama,
            );
            $builder = $this->db->table('sc_trx.pmk_brng_mst');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('persediaan/trans/add_pmk_brng_mst'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
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
        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_persediaan->q_trx_pmk_brng_mst($param)->getRowArray();
        return $this->template->render('persediaan/pmk_brng/v_detail_pmk_brng',$data);
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

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'        => $logindate,
        ]);
    }

    public function getNextSuffix_pmk_brng()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.pmk_brng_mst')
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
    function clear_pmk_brng()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_persediaan->q_tmp_pmk_brng_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('persediaan/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.pmk_brng_mst');
        $builder_dtl = $this->db->table('sc_tmp.transfer_location_dtl_mst');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('persediaan/trans/pmk_brng'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('persediaan/trans/pmk_brng'));
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
                trim($status) !== 'DITARIK '
            ) {

                $updateBtn = '
                    <a class="dropdown-item bg-warning" 
                    href="' . base_url('persediaan/trans/updatepnmBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update Pemakaian Barang : ' . $docno . '\')">
                        <i class="fa fa-edit"></i> Update 
                    </a>';
            }

            if ($canView) {
                $detailBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('persediaan/trans/detail_pnm_brng_mst') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Pemakai Barang : ' . $docno . '\')">
                        <i class="fa fa-eye"></i> Detail 
                    </a>';
            }

            if ($canPrint) {
                $printBtn = '
                    <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('persediaan/trans/showPrintpnmBrg') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Pemakaian Barang : ' . $docno . '\')">
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
            $row[] = $lm->cabang;
            $row[] = $lm->idcostcenter;
            $row[] = $lm->docdate;

            $row[] = $lm->nmstatus;
            $row[] = $lm->description;
            $row[] = $lm->inputby;


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

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_persediaan->q_tmp_pnm_brng_mst($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

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


    public function save_pnm_brng_detail()
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
                'description' => strtoupper(trim($this->request->getPost('description'))),
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
                    'doctype'    => 'PNMBRG',
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
        $param = " and docno='$docno'";
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
            'total_nilai' => number_format($summary['valsum'], 2, '.', ',')
        ]);
    }

    function final_pnm_barang(){
        $nama = trim($this->session->get('nama'));
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
            $row[] = '<div class="ratakanan">'. number_format($lm->val, 2, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->valsum, 2, '.', ',') . '</div>';
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
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('persediaan/trans/pnm_brng'));
        }
    }
    function detail_pnm_brng_mst()
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
        $data['dtldata'] = $this->m_persediaan->q_trx_pnm_brng_mst($param)->getRowArray();
        return $this->template->render('persediaan/pnm_brng/v_detail_pnm_brng',$data);
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

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'        => $logindate,
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
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_persediaan->q_tmp_pnm_brng_mst($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('persediaan/trans/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.pnm_brng_mst');
        $builder_dtl = $this->db->table('sc_tmp.pnm_brng_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('persediaan/trans/pnm_barang'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('persediaan/trans/pnm_barang'));
        }

    }
}