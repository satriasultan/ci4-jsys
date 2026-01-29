<?php 

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Customer extends BaseController
{
     public function customer()
    {
        $data['title']="LIST CUSTOMER";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.1'; $versirelease='I.M.B.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.1'";
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
        // $this->m_customer->q_autoinsert_unit();
        $kmenu = 'I.M.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/customer/v_list_customer',$data);
    }

    function list_customer(){
        $kmenu = 'I.M.B.1';
        $nama=trim($this->session->get('nama'));
        $list = $this->m_customer->get_t_customer_view();
        $role=trim($this->session->get('roleid'));

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

            //Default: Detail selalu ada

            if ($canUpdate) {
                $btnActions .= '<a class="dropdown-item text-warning href="#" onclick="editCustomer(\'' . trim($lm->id) . '\');">
                    <i class="fa fa-edit"></i> Update</a>';
            }
            if ($canView) {
                $btnActions .= '<a class="dropdown-item text-info" href="#" onclick="detailCustomer(\'' . trim($lm->id) . '\');">
                        <i class="fa fa-eye"></i> Detail Data</a>';
            }
            if ($canDelete && trim($lm->chold) != 'YES') {
                $btnActions .= '<a class="dropdown-item text-danger" href="#" onclick="hapusCustomer(\'' . trim($lm->id) . '\');">
                    <i class="fa fa-trash"></i> Hapus</a>';
            }



            $btnActions .= '</div></div>';


            $row[] = $btnActions;
            $row[] = $lm->kdcustomer;
            $row[] = $lm->nmcustomer;
            $row[] = $lm->alamat_kantor;
            $row[] = $lm->namakotakab;
            $row[] = $lm->namaprov;
            $row[] = $lm->nmmarket;
            $row[] = $lm->npwp;
            $row[] = number_format((float)$lm->plafon, 2, '.', ',');
            $row[] = number_format((float)$lm->jthtempo, 2, '.', ',');
            $row[] = $lm->phone;
            $row[] = $lm->cp;
            // $row[] = $lm->createdby;
            // $row[] = $lm->createddate;
            // $row[] = $lm->updateby;
            // $row[] = $lm->updatedate;


            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_customer->t_customer_view_count_all(),
            "recordsFiltered" => $this->m_customer->t_customer_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input_customer(){
        $data['title']="INPUT DATA CUSTOMER";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.1'; $versirelease='I.M.B.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.1'";
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

        $kmenu = 'I.M.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/customer/v_input_customer',$data);
    }

    function edit_customer(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.1'; $versirelease='I.M.B.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.1'";
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
        $data['title']="EDIT DATA CUSTOMER";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $idParam = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("master/data");



        $kmenu = 'I.M.B.1';
        $role = trim($this->session->get('roleid'));
        $data['idParam'] = $idParam;
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/customer/v_input_customer',$data);
    }

    function detail_customer(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.1'; $versirelease='I.M.B.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.1'";
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
        $data['title']="DETAIL DATA CUSTOMER";
        $var = trim($this->request->getGet('var'));
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['id']=$var;

        $idParam = $var;
        $data['typeform'] = 'DETAIL';
        $data['back_url'] = base_url("master/data");

        $data['idParam'] = $idParam;
        $kmenu = 'I.M.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/customer/v_input_customer',$data);
    }

    public function hapus_customer()
    {
        $id   = $this->request->getPost('id');
        $nama = trim($this->session->get('nama'));

        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID customer tidak valid'
            ]);
        }

        $builder = $this->db->table('sc_mst.customer');

        // cek data
        $customer = $builder
            ->select('id, kdcustomer')
            ->where('id', $id)
            ->where('status !=', 'D')
            ->get()
            ->getRowArray();

        if (!$customer) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data customer tidak ditemukan atau sudah dihapus'
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
            'message' => 'Data customer ' . trim($customer['kdcustomer']) . ' berhasil dihapus'
        ]);
    }




    function del_customer(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_mst.customer');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.B.1');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.M.B.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('master/data'));
    }

    function showDetailCustomer () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and id='$var'";
        $dtl = $this->m_customer->q_mstcustomer($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }


    function saveDataCustomer(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            $kdcustomer          = strtoupper(trim($dataprocess->kdcustomer));
            $nmcustomer      = strtoupper(trim($dataprocess->nmcustomer));
            $chold = $dataprocess->chold;

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
            $builder = $this->db->table('sc_mst.customer');
            if ($type === 'INPUT') {
                $info = [
                    'kdcustomer' => $kdcustomer,
                    'nmcustomer' => $nmcustomer,
                    'chold' => $chold,

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
                    // $customerRow = $builder
                    //     ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                    //     ->get()
                    //     ->getRowArray();
                    // if ($customerRow) {
                    //     $docnoFinal   = trim($customerRow['docno']);
                    //     $url = array(
                    //         'barcodelink' => base_url('master/data/getDataFormCustomer/' . $this->fiky_encryption->sealed($docnoFinal)),
                    //     );
                    //     $builder->where('TRIM(docno)', $docnoFinal);
                    //     $builder->update($url);

                    // }

                    // $paramerror=" and userid='$nama' and modul='I.M.B.1'";
                    // $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.trim($kdcustomer));
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and id='$id'";
                $check = $this->m_customer->q_mstcustomer($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                $info = [
                    'nmcustomer' => $nmcustomer,
                    'chold' => $chold,
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

                $builder = $this->db->table('sc_mst.customer');
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