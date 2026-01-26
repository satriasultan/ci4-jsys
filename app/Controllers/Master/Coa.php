<?php 

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Coa extends BaseController
{
     public function coa()
    {
        $data['title']="LIST COA";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.5'; $versirelease='I.M.B.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.5'";
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
        // $this->m_coa->q_autoinsert_unit();
        $roleid=trim($this->session->get('roleid'));
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/coa/v_list_coa',$data);
    }

    public function js_vtree_query()
    {
        $rows  = $this->m_coa->getTreeData();

        $tree = [];

        foreach ($rows as $r) {
            $tree[] = [
                'id'   => $r->id,
                'pid'  => $r->pid,
                'name' => trim($r->id) . ' | ' . trim($r->name),
                'open' => ($r->pid === '0'), // auto open root
                // opsional
                // 'chkDisabled' => ($r->isDetail === 'false')
            ];
        }

        return $this->response->setJSON($tree);
    }

    public function get_coa_detail()
    {
        $idcoa = $this->request->getPost('idcoa');
        
        $data['coa'] = $this->m_coa->getById($idcoa);
        
        $roleid = trim($this->session->get('roleid'));
        $kodemenu = 'I.M.B.5';
        $perm = $this->m_role->detail_user_akses($roleid, $kodemenu)->getRowArray();


        // fallback jika tidak ada record
        $data['perm'] = [
            'input'  => isset($perm['a_input'])  && trim($perm['a_input'])  === 't',
            'update' => isset($perm['a_update']) && trim($perm['a_update']) === 't',
            'delete' => isset($perm['a_delete']) && trim($perm['a_delete']) === 't',
        ];


        return view('master/coa/coa_form', $data);
    }


    public function saveCOA()
    {
        $type  = $this->request->getPost('type');
        $idcoa = $this->request->getPost('idcoa');
        $nama  = trim($this->session->get('nama'));

        $builder = $this->db->table('sc_mst.coa');

        if ($type === 'INPUT') {

            $idcoa = trim((string) $idcoa);

            $exists = $builder
            ->where("TRIM(idcoa) =", $idcoa)
            ->where("COALESCE(TRIM(status), 'F') = 'F'", null, false)
            ->countAllResults();

            if ($exists > 0) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ID COA sudah ada'
                ]);
            }

            $data = [
                'idcoa'        => $idcoa,
                'nmcoa'        => strtoupper($this->request->getPost('nmcoa')),
                'nm2coa'       => strtoupper($this->request->getPost('nm2coa')),
                'induk'        => $this->request->getPost('induk'),
                'level'        => $this->request->getPost('level'),
                'isdetail'     => $this->request->getPost('isdetail'),
                'groupcoa'     => strtoupper($this->request->getPost('groupcoa')),
                'idcur'        => strtoupper($this->request->getPost('idcur')),
                'jenis'        => strtoupper($this->request->getPost('jenis')),
                'status'       => 'F',
                'createdby'    => $nama,
                'createddate'  => date('Y-m-d H:i:s')
            ];

            $builder->insert($data);
        }

        if ($type === 'EDIT') {

            $data = [
                'nmcoa'        => strtoupper($this->request->getPost('nmcoa')),
                'nm2coa'       => strtoupper($this->request->getPost('nm2coa')),
                // 'induk'        => $this->request->getPost('induk'),
                // 'level'        => $this->request->getPost('level'),
                'isdetail'     => $this->request->getPost('isdetail'),
                'groupcoa'     => strtoupper($this->request->getPost('groupcoa')),
                'idcur'        => strtoupper($this->request->getPost('idcur')),
                'jenis'        => strtoupper($this->request->getPost('jenis')),
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
            ];

            $builder->where('idcoa', $idcoa)->update($data);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data COA berhasil disimpan'
        ]);
    }


    // public function delete_coa()
    // {
    //     $idcoa = $this->request->getPost('idcoa');
    //     $nama  = trim($this->session->get('nama'));

    //     if (!$idcoa) {
    //         return $this->response->setJSON([
    //             'status'  => 'error',
    //             'message' => 'ID COA tidak valid'
    //         ]);
    //     }

    //     $builder = $this->db->table('sc_mst.coa');

    //     $builder->where('idcoa', $idcoa)->update([
    //         'status'      => 'C',
    //         'updateby'   => $nama,
    //         'updatedate' => date('Y-m-d H:i:s')
    //     ]);

    //     return $this->response->setJSON([
    //         'status'  => 'success',
    //         'message' => 'COA berhasil dihapus'
    //     ]);
    // }

    public function delete_coa()
    {
        $idcoa = $this->request->getPost('idcoa');
        $nama  = trim($this->session->get('nama'));

        if (!$idcoa) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ID COA tidak valid'
            ]);
        }

        // Cek apakah COA memiliki child (untuk keamanan)
        $builder = $this->db->table('sc_mst.coa');
        
        // Cek apakah ada child records
        $hasChild = $builder->where('induk', $idcoa)->countAllResults();
        
        if ($hasChild > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'COA tidak dapat dihapus karena masih memiliki child COA'
            ]);
        }

        // Hard delete menggunakan delete()
        $deleted = $builder->where('idcoa', $idcoa)->delete();

        if ($deleted) {
            // Optional: Log aktivitas
            // $this->logActivity($nama, 'DELETE_COA', "Menghapus COA: $idcoa");
            
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'COA berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus COA'
            ]);
        }
    }


    function input_coa(){
        $data['title']="INPUT DATA COA";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.5'; $versirelease='I.M.B.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.5'";
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

        $kmenu = 'I.M.B.5';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/coa/v_input_coa',$data);
    }

    function edit_coa(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.5'; $versirelease='I.M.B.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.5'";
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
        $data['title']="EDIT DATA COA";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $idParam = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("master/data");



        $kmenu = 'I.M.B.5';
        $role = trim($this->session->get('roleid'));
        $data['idParam'] = $idParam;
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/coa/v_input_coa',$data);
    }

    function detail_coa(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.5'; $versirelease='I.M.B.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.5'";
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
        $data['title']="DETAIL DATA COA";
        $var = trim($this->request->getGet('var'));
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['id']=$var;

        $idParam = $var;
        $data['typeform'] = 'DETAIL';
        $data['back_url'] = base_url("master/data");

        $data['idParam'] = $idParam;
        $kmenu = 'I.M.B.5';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/coa/v_input_coa',$data);
    }

    public function hapus_coa()
    {
        $id   = $this->request->getPost('id');
        $nama = trim($this->session->get('nama'));

        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID coa tidak valid'
            ]);
        }

        $builder = $this->db->table('sc_mst.coa');

        // cek data
        $coa = $builder
            ->select('id, kdcoa')
            ->where('id', $id)
            ->where('status !=', 'D')
            ->get()
            ->getRowArray();

        if (!$coa) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data coa tidak ditemukan atau sudah dihapus'
            ]);
        }

        // soft delete
        $builder->where('id', $id);
        $builder->update([
            'status'      => 'D',
            'updateby'   => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data coa ' . trim($coa['kdcoa']) . ' berhasil dihapus'
        ]);
    }




    function del_coa(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_mst.coa');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.B.5');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.M.B.5',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('master/data'));
    }

    function showDetailCoa () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and id='$var'";
        $dtl = $this->m_coa->q_mstcoa($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }


    function saveDataCoa(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            $kdcoa          = strtoupper(trim($dataprocess->kdcoa));
            $nmcoa      = strtoupper(trim($dataprocess->nmcoa));

            $provinsi_kantor = $dataprocess->provinsi_kantor;
            $kota_kantor = $dataprocess->kota_kantor;
            $kec_kantor = $dataprocess->kec_kantor;
            $kel_kantor = $dataprocess->kel_kantor;
            $alamat_kantor = isset($dataprocess->alamat_kantor) ? strtoupper(trim($dataprocess->alamat_kantor)) : null;
            $kodepos_kantor = $dataprocess->kodepos_kantor;

            $provinsi_pengiriman = $dataprocess->provinsi_pengiriman;
            $kota_pengiriman = $dataprocess->kota_pengiriman;
            $kec_pengiriman = $dataprocess->kec_pengiriman;
            $kel_pengiriman = $dataprocess->kel_pengiriman;
            $alamat_pengiriman = isset($dataprocess->alamat_pengiriman) ? strtoupper(trim($dataprocess->alamat_pengiriman)) : null;
            $kodepos_pengiriman = $dataprocess->kodepos_pengiriman;

            $provinsi_penagihan = $dataprocess->provinsi_penagihan;
            $kota_penagihan = $dataprocess->kota_penagihan;
            $kec_penagihan = $dataprocess->kec_penagihan;
            $kel_penagihan = $dataprocess->kel_penagihan;
            $alamat_penagihan = isset($dataprocess->alamat_penagihan) ? strtoupper(trim($dataprocess->alamat_penagihan)) : null;
            $kodepos_penagihan = $dataprocess->kodepos_penagihan;




            $phone   = isset($dataprocess->phone) ? trim($dataprocess->phone) : null;
            $email   = isset($dataprocess->email) ? strtoupper(trim($dataprocess->email)) : null;
            $fax     = isset($dataprocess->fax) ? strtoupper(trim($dataprocess->fax)) : null;

            $npwp    = isset($dataprocess->npwp) ? strtoupper(trim($dataprocess->npwp)) : null;
            $npkp    = isset($dataprocess->npkp) ? strtoupper(trim($dataprocess->npkp)) : null;

            $plafon  = isset($dataprocess->plafon) ? trim($dataprocess->plafon) : null;
            $jthtempo= isset($dataprocess->jthtempo) ? trim($dataprocess->jthtempo) : null;

            $idmarket= isset($dataprocess->idmarket) ? strtoupper(trim($dataprocess->idmarket)) : null;
            $cp      = isset($dataprocess->cp) ? strtoupper(trim($dataprocess->cp)) : null;
            $jabatan = isset($dataprocess->jabatan) ? strtoupper(trim($dataprocess->jabatan)) : null;
            $keterangan = isset($dataprocess->keterangan) ? strtoupper(trim($dataprocess->keterangan)) : null;
            $coa       = isset($dataprocess->coa) ? strtoupper(trim($dataprocess->coa)) : 'NO';
            $blacklist = isset($dataprocess->blacklist) ? strtoupper(trim($dataprocess->blacklist)) : 'NO';
            
            
            $namanpwp   = isset($dataprocess->namanpwp) ? strtoupper(trim($dataprocess->namanpwp)) : null;
            $alamatnpwp= isset($dataprocess->alamatnpwp) ? strtoupper(trim($dataprocess->alamatnpwp)) : null;
            $idkotanpwp= isset($dataprocess->idkotanpwp) ? strtoupper(trim($dataprocess->idkotanpwp)) : null;
            
            $idcoretax = isset($dataprocess->idcoretax) ? strtoupper(trim($dataprocess->idcoretax)) : null;
            $idtku     = isset($dataprocess->idtku) ? strtoupper(trim($dataprocess->idtku)) : null;
            $docnopembeli = isset($dataprocess->docnopembeli) ? strtoupper(trim($dataprocess->docnopembeli)) : null;
            
            
            $grade       = strtoupper(trim($dataprocess->grade));
            $salesman       = strtoupper(trim($dataprocess->salesman));
            $kolektor       = strtoupper(trim($dataprocess->kolektor));
            $koderetur       = isset($dataprocess->koderetur) ? trim($dataprocess->koderetur) : null;
            $isfaktur  = isset($dataprocess->isfaktur) ? strtoupper(trim($dataprocess->isfaktur)) : 'NO';
            
            $harifakturin = isset($dataprocess->harifakturin) && is_array($dataprocess->harifakturin)
                ? strtoupper(trim(implode(',', $dataprocess->harifakturin)))
                : null;

            $haripenagihan = isset($dataprocess->haripenagihan) && is_array($dataprocess->haripenagihan)
                ? strtoupper(trim(implode(',', $dataprocess->haripenagihan)))
                : null;

            $haripembayaran = isset($dataprocess->haripembayaran) && is_array($dataprocess->haripembayaran)
                ? strtoupper(trim(implode(',', $dataprocess->haripembayaran)))
                : null;


            $defaultfor = isset($dataprocess->defaultfor)
                ? strtoupper(trim($dataprocess->defaultfor))
                : 'NONE';



            // $interngroup           = ($dataprocess->interngroup);
            // $blacklist    = ($dataprocess->blacklist);

            $createdby = $nama;
            $createddate = date('Y-m-d H:i:s');
            $builder = $this->db->table('sc_mst.coa');
            if ($type === 'INPUT') {
                $info = [
                    'kdcoa' => $kdcoa,
                    'nmcoa' => $nmcoa,

                    'provinsi_kantor' => $provinsi_kantor,
                    'kota_kantor'     => $kota_kantor,
                    'kec_kantor'      => $kec_kantor,
                    'kel_kantor'      => $kel_kantor,
                    'alamat_kantor'   => $alamat_kantor,
                    'kodepos_kantor'  => $kodepos_kantor,

                    'provinsi_pengiriman' => $provinsi_pengiriman,
                    'kota_pengiriman'     => $kota_pengiriman,
                    'kec_pengiriman'      => $kec_pengiriman,
                    'kel_pengiriman'      => $kel_pengiriman,
                    'alamat_pengiriman'   => $alamat_pengiriman,
                    'kodepos_pengiriman'  => $kodepos_pengiriman,

                    'provinsi_penagihan' => $provinsi_penagihan,
                    'kota_penagihan'     => $kota_penagihan,
                    'kec_penagihan'      => $kec_penagihan,
                    'kel_penagihan'      => $kel_penagihan,
                    'alamat_penagihan'   => $alamat_penagihan,
                    'kodepos_penagihan'  => $kodepos_penagihan,


                    'phone'      => $phone,
                    'email'      => $email,
                    'fax'        => $fax,
                    'npwp'       => $npwp,
                    'npkp'       => $npkp,
                    'plafon'     => $plafon,
                    'jthtempo'   => $jthtempo,
                    'idmarket'   => $idmarket,

                    'cp'         => $cp,
                    'jabatan'    => $jabatan,
                    'keterangan' => $keterangan,
                    'coa'        => $coa,
                    'blacklist'  => $blacklist,

                    'namanpwp'   => $namanpwp,
                    'alamatnpwp' => $alamatnpwp,
                    'idkotanpwp' => $idkotanpwp,
                    'idcoretax'  => $idcoretax,
                    'idtku'      => $idtku,
                    'docnopembeli'=> $docnopembeli,

                    'grade'     => $grade,
                    'salesman'  => $salesman,
                    'kolektor'  => $kolektor,

                    'koderetur' => $koderetur,
                    'isfaktur'   => $isfaktur,

                    'harifakturin'   => $harifakturin,
                    'haripenagihan'  => $haripenagihan,
                    'haripembayaran' => $haripembayaran,

                    'defaultfor' => $defaultfor,
                    'status'     => 'F',
                    'createdby'  => $createdby,
                    'createddate'=> $createddate
                ];
                if ($builder->insert($info)) {
                    // $inx = array('status' => 'F');
                    // $builder->where('docno',$nama);
                    // $builder->update($inx);
                    // $coaRow = $builder
                    //     ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                    //     ->get()
                    //     ->getRowArray();
                    // if ($coaRow) {
                    //     $docnoFinal   = trim($coaRow['docno']);
                    //     $url = array(
                    //         'barcodelink' => base_url('master/data/getDataFormCoa/' . $this->fiky_encryption->sealed($docnoFinal)),
                    //     );
                    //     $builder->where('TRIM(docno)', $docnoFinal);
                    //     $builder->update($url);

                    // }

                    // $paramerror=" and userid='$nama' and modul='I.M.B.5'";
                    // $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.trim($kdcoa));
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and id='$id'";
                $check = $this->m_coa->q_mstcoa($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                $info = [
                    'nmcoa' => $nmcoa,
                    'provinsi_kantor' => $provinsi_kantor,
                    'kota_kantor'     => $kota_kantor,
                    'kec_kantor'      => $kec_kantor,
                    'kel_kantor'      => $kel_kantor,
                    'alamat_kantor'   => $alamat_kantor,
                    'kodepos_kantor'  => $kodepos_kantor,

                    'provinsi_pengiriman' => $provinsi_pengiriman,
                    'kota_pengiriman'     => $kota_pengiriman,
                    'kec_pengiriman'      => $kec_pengiriman,
                    'kel_pengiriman'      => $kel_pengiriman,
                    'alamat_pengiriman'   => $alamat_pengiriman,
                    'kodepos_pengiriman'  => $kodepos_pengiriman,

                    'provinsi_penagihan' => $provinsi_penagihan,
                    'kota_penagihan'     => $kota_penagihan,
                    'kec_penagihan'      => $kec_penagihan,
                    'kel_penagihan'      => $kel_penagihan,
                    'alamat_penagihan'   => $alamat_penagihan,
                    'kodepos_penagihan'  => $kodepos_penagihan,


                    'phone'      => $phone,
                    'email'      => $email,
                    'fax'        => $fax,
                    'npwp'       => $npwp,
                    'npkp'       => $npkp,
                    'plafon'     => $plafon,
                    'jthtempo'   => $jthtempo,
                    'idmarket'   => $idmarket,

                    'cp'         => $cp,
                    'jabatan'    => $jabatan,
                    'keterangan' => $keterangan,
                    'coa'        => $coa,
                    'blacklist'  => $blacklist,

                    'namanpwp'   => $namanpwp,
                    'alamatnpwp' => $alamatnpwp,
                    'idkotanpwp' => $idkotanpwp,
                    'idcoretax'  => $idcoretax,
                    'idtku'      => $idtku,
                    'docnopembeli'=> $docnopembeli,

                    'grade'     => $grade,
                    'salesman'  => $salesman,
                    'kolektor'  => $kolektor,

                    'koderetur' => $koderetur,
                    'isfaktur'   => $isfaktur,

                    'harifakturin'   => $harifakturin,
                    'haripenagihan'  => $haripenagihan,
                    'haripembayaran' => $haripembayaran,

                    'defaultfor' => $defaultfor,

                    'updateby'  => $createdby,
                    'updatedate'=> $createddate
                ];


                    // if ($status !== null) {
                    //     $info['status'] = $status;
                    // }

                    $builder->where('id', $id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$id);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                } else {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                }

            } else if ($type==='DELETE') {

                $builder = $this->db->table('sc_mst.coa');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$id);
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }
            }


        } else {
            $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($getResult);
        }
    }

}
?>