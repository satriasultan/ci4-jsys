<?php 

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Tax extends BaseController
{
     public function tax()
    {
        $data['title']="Master Tax";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.3'; $versirelease='I.M.B.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
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
        //auto insert unit
        // $this->m_suppliers->q_autoinsert_unit();
        $kmenu = 'I.M.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/tax/v_list_tax',$data);
    }


    function input_tax(){
        $data['title']="INPUT MASTER TAX";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.3'; $versirelease='I.M.B.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
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
        $data['type']="INPUT";
        $data['nama']=$nama;

        $data['back_url'] = base_url("master/data");
        $data['typeform'] = 'INPUT';

        $kmenu = 'I.M.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/tax/v_input_tax',$data);
    }
    function list_tax(){
        $kmenu = 'I.M.B.3';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));
        $list = $this->m_tax->get_t_tax_mst_view();
        $roleid=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';



        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;

            if ($canView || $canUpdate || $canDelete) {
                $btnActions = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle text-white"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-gear"></i>
                    </button>
                    <div class="dropdown-menu">';
            }
            // Default: Cetak selalu ada
            // if(trim($lm->nmstatus) !== 'BATAL'){

            // }
            if ($canUpdate) {
                $btnActions .= '<a class="dropdown-item text-warning" href="#" onclick="updateMasterTax(\'' . trim($lm->id) . '\');">
                    <i class="fa fa-edit"></i> Update</a>';
            }
            if ($canView) {
                $btnActions .= '<a class="dropdown-item text-info" href="#" onclick="detailMasterTax(\'' . trim($lm->id) . '\');">
                        <i class="fa fa-eye"></i> Detail Data</a>';
            }
            if ($canDelete) {
                $btnActions .= '<a class="dropdown-item text-danger" href="#" onclick="hapusMasterTax(\'' . trim($lm->id) . '\');">
                    <i class="fa fa-trash"></i> Hapus</a>';
            }
            //Default: Detail selalu ada



            $btnActions .= '</div></div>';


            $row[] = $btnActions;
            $row[] = $lm->idtax;
            $row[] = $lm->nmtax;
            $row[] = $lm->chold;


            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_tax->t_tax_mst_view_count_all(),
            "recordsFiltered" => $this->m_tax->t_tax_mst_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function hapus_master_tax()
    {
        $request = service('request');
        $db      = \Config\Database::connect();

        // id yang dikirim dari frontend (PK numeric)
        $id = trim($request->getPost('id'));

        if (empty($id)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter id tidak boleh kosong'
            ]);
        }

        $db->transBegin();

        try {

            /* ===============================
               1️⃣ AMBIL IDTAX DARI MASTER
            =============================== */
            $master = $db->table('sc_mst.tax_mst')
                ->select('idtax')
                ->where('id', $id)
                ->get()
                ->getRowArray();

            if (!$master) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data Master Tax tidak ditemukan'
                ]);
            }

            $idtax = strtoupper(trim($master['idtax']));

            /* ===============================
               2️⃣ HAPUS DETAIL TAX (by idtax)
            =============================== */
            $db->table('sc_mst.tax_dtl')
                ->where('idtax', $idtax)
                ->delete();

            /* ===============================
               3️⃣ HAPUS MASTER TAX (by id)
            =============================== */
            $db->table('sc_mst.tax_mst')
                ->where('id', $id)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Gagal menghapus Master Tax'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Master Tax dan seluruh Detail Tax berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    function update_tax(){
        $id = trim($this->request->getGet('id'));

        $data['title']="UPDATE MASTER TAX";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.3'; $versirelease='I.M.B.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
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
        $data['type']="UPDATE";
        $data['nama']=$nama;

        $data['back_url'] = base_url("master/data");
        $data['typeform'] = 'UPDATE';

        $kmenu = 'I.M.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']=$id;
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/tax/v_input_tax',$data);
    }

    function detail_tax(){
        $id = trim($this->request->getGet('id'));

        $data['title']="UPDATE MASTER TAX";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.3'; $versirelease='I.M.B.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
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
        $data['type']="DETAIL";
        $data['nama']=$nama;

        $data['back_url'] = base_url("master/data");
        $data['typeform'] = 'DETAIL';

        $kmenu = 'I.M.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']=$id;
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/tax/v_input_tax',$data);
    }

    public function save_tax_master()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status'=>false]);
        }

        $db = \Config\Database::connect();

        $id     = $this->request->getPost('id');
        $idtax  = strtoupper(trim($this->request->getPost('idtax')));
        $nmtax  = strtoupper(trim($this->request->getPost('nmtax')));
        $type = strtoupper(trim($this->request->getPost('type')));

        // cek existing by idtax
        $row = $db->table('sc_mst.tax_mst')
            ->where('idtax', $idtax)
            ->where('status','P')
            ->get()
            ->getRow();

        if ($row) {
            // UPDATE (idtax TIDAK diubah)
            $db->table('sc_mst.tax_mst')
                ->where('id', $row->id)
                ->update([
                    'nmtax'      => $nmtax,
                    'createddate' => date('Y-m-d H:i:s')
                ]);

            $newId = $row->id;

        } else {
            // INSERT
            $db->table('sc_mst.tax_mst')->insert([
                'idtax'      => $idtax,
                'nmtax'      => $nmtax,
                'status'     => 'P',
                'createdby' => date('Y-m-d H:i:s')
            ]);

            $newId = $db->insertID();
        }

        return $this->response->setJSON([
            'status' => true,
            'id'     => $newId,
            'idtax'  => $idtax,
            'type'  => $type
        ]);
    }


    public function getMasterTax()
    {
//        if (!$this->request->isAJAX()) {
//            return $this->response->setJSON([
//                'status' => false,
//                'message' => 'Invalid request'
//            ]);
//        }

        $id = $this->request->getGet('var');
        $idtax = $this->request->getGet('idtax');


        $param = " and id='$id'";
        $data = $this->m_tax->q_master_tax_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    /* LIST TAX DETAIL */
    public function list_tax_detail()
    {
        $kmenu = 'I.M.B.3';
        $nama  = trim($this->session->get('nama'));
        $role  = trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role
            ->detail_user_akses($role, $kmenu)
            ->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';

        $list = $this->m_tax->get_t_tax_dtl_view();
        $data = [];
        $no   = $_POST['start'];

        foreach ($list as $lm) {
            $no++;
            $row = [];
            $row[] = $lm->id;
            $row[] = $lm->idtax;
            $row[] = $lm->idgrouptax;
            $row[] = $lm->nmgrouptax;
            $row[] = $lm->kodepajak;
            $row[] = $lm->prk_masukan;
            $row[] = $lm->prk_keluaran;
            $row[] = $lm->prk_fpj_msk;
            $row[] = $lm->prk_fpj_klr;
            $row[] = $lm->prk_fpj_msk_rep;
            $row[] = $lm->prk_fpj_klr_rep;
            $row[] = $lm->rumus_dpp_lain;
            $row[] = $lm->rumus_pajak;
            $row[] = number_format($lm->percentation, 2);
            $row[] = $lm->chold;

            $data[] = $row;
        }

        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->m_tax->t_tax_dtl_view_count_all(),
            "recordsFiltered" => $this->m_tax->t_tax_dtl_view_count_filtered(),
            "data"            => $data
        ];

        echo $this->fiky_encryption->jDatatable($output);
    }

    public function save_tax_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_mst.tax_dtl');

        // ===============================
        // DATA (UPPERCASE)
        // ===============================
        $data = [
            'idtax'           => strtoupper(trim($request->getPost('idtax'))),
            'idgrouptax'      => strtoupper(trim($request->getPost('idgrouptax'))),
            'nmgrouptax'      => strtoupper(trim($request->getPost('nmgrouptax'))),
            'kodepajak'       => strtoupper(trim($request->getPost('kodepajak'))),

            'prk_masukan'     => strtoupper(trim($request->getPost('prk_masukan'))),
            'prk_keluaran'    => strtoupper(trim($request->getPost('prk_keluaran'))),
            'prk_fpj_msk'     => strtoupper(trim($request->getPost('prk_fpj_msk'))),
            'prk_fpj_klr'     => strtoupper(trim($request->getPost('prk_fpj_klr'))),
            'prk_fpj_msk_rep' => strtoupper(trim($request->getPost('prk_fpj_msk_rep'))),
            'prk_fpj_klr_rep' => strtoupper(trim($request->getPost('prk_fpj_klr_rep'))),

            'rumus_dpp_lain'  => strtoupper(trim($request->getPost('rumus_dpp_lain'))),
            'rumus_pajak'     => strtoupper(trim($request->getPost('rumus_pajak'))),

            'percentation'    => (float) $request->getPost('percentation'),
            'status'          => strtoupper($request->getPost('status') ?? 'ACTIVE'),
            'chold'           => strtoupper($request->getPost('chold') ?? 'NO'),
        ];

        // ===============================
        // VALIDASI MINIMAL
        // ===============================
        if (
            empty($data['idtax']) ||
            empty($data['idgrouptax']) ||
            empty($data['kodepajak'])
        ) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Field wajib belum lengkap'
            ]);
        }

        // ===============================
        // CEK DATA EXIST (PK KOMPOSIT)
        // ===============================
        $exists = $builder
            ->where('idtax', $data['idtax'])
            ->where('idgrouptax', $data['idgrouptax'])
            ->get()
            ->getRowArray();

        // ===============================
        // TRANSAKSI
        // ===============================
        $db->transBegin();

        try {

            if ($exists) {
                // ===============================
                // UPDATE (TANPA PRIMARY KEY)
                // ===============================
                $updateData = $data;
                unset($updateData['idtax'], $updateData['idgrouptax']);

                $builder
                    ->where('idtax', $data['idtax'])
                    ->where('idgrouptax', $data['idgrouptax'])
                    ->update($updateData);

                $message = 'Data Tax Detail berhasil diupdate';

            } else {
                // ===============================
                // INSERT
                // ===============================
                $builder->insert($data);
                $message = 'Data Tax Detail berhasil disimpan';
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Gagal menyimpan data'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => $message
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function get_tax_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_mst.tax_dtl')
            ->where('id', $id)
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

    public function delete_tax_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_mst.tax_dtl');

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
                ->whereIn('id', $ids)
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



}