<?php 

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Suppliers extends BaseController
{
     public function supplier()
    {
        $data['title']="LIST SUPPLIER";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.2'; $versirelease='I.M.B.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.2'";
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
        $kmenu = 'I.M.B.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/supplier/v_list_suppliers',$data);
    }

    function list_suppliers(){
        $kmenu = 'I.M.B.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));
        $list = $this->m_suppliers->get_t_suppliers_view();
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
                $btnActions .= '<a class="dropdown-item text-warning" href="#" onclick="editSupplier(\'' . trim($lm->id) . '\');">
                    <i class="fa fa-edit"></i> Update</a>';
            }
            if ($canView) {
                $btnActions .= '<a class="dropdown-item text-info" href="#" onclick="detailSupplier(\'' . trim($lm->id) . '\');">
                        <i class="fa fa-eye"></i> Detail Data</a>';
            }
            if ($canDelete && trim($lm->chold) != 'YES') {
                $btnActions .= '<a class="dropdown-item text-danger" href="#" onclick="hapusSupplier(\'' . trim($lm->id) . '\');">
                    <i class="fa fa-trash"></i> Hapus</a>';
            }            
            //Default: Detail selalu ada



            $btnActions .= '</div></div>';


            $row[] = $btnActions;
            $row[] = $lm->kdsupplier;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->alamat;
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
            "recordsTotal" => $this->m_suppliers->t_suppliers_view_count_all(),
            "recordsFiltered" => $this->m_suppliers->t_suppliers_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input_suppliers(){
        $data['title']="INPUT DATA SUPPLIER";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.2'; $versirelease='I.M.B.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.2'";
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

        $kmenu = 'I.M.B.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/supplier/v_input_suppliers',$data);
    }

    function edit_suppliers(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.2'; $versirelease='I.M.B.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.2'";
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
        $data['title']="EDIT DATA SUPPLIER";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $idParam = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("master/data");



        $kmenu = 'I.M.B.2';
        $role = trim($this->session->get('roleid'));
        $data['idParam'] = $idParam;
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/supplier/v_input_suppliers',$data);
    }

    function detail_suppliers(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.2'; $versirelease='I.M.B.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.2'";
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
        $data['title']="DETAIL DATA SUPPLIER";
        $var = trim($this->request->getGet('var'));
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['id']=$var;

        $idParam = $var;
        $data['typeform'] = 'DETAIL';
        $data['back_url'] = base_url("master/data");

        $data['idParam'] = $idParam;
        $kmenu = 'I.M.B.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/supplier/v_input_suppliers',$data);
    }

    public function hapus_suppliers()
    {
        $id   = $this->request->getPost('id');
        $nama = trim($this->session->get('nama'));

        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID supplier tidak valid'
            ]);
        }

        $builder = $this->db->table('sc_mst.mstsupplier');

        // cek data
        $supplier = $builder
            ->select('id, kdsupplier')
            ->where('id', $id)
            ->where('status !=', 'D')
            ->get()
            ->getRowArray();

        if (!$supplier) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data supplier tidak ditemukan atau sudah dihapus'
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
            'message' => 'Data supplier ' . trim($supplier['kdsupplier']) . ' berhasil dihapus'
        ]);
    }




    function del_suppliers(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_mst.mstsupplier');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.B.2');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.M.B.2',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('master/data'));
    }

    function showDetailSupplier () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and id='$var'";
        $dtl = $this->m_suppliers->q_mstsuppliers($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }


    function saveDataSupplier(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $type = trim($dataprocess->type);
            $id = trim($dataprocess->id);
            $kdsupplier          = strtoupper(trim($dataprocess->kdsupplier));
            $nmsupplier      = strtoupper(trim($dataprocess->nmsupplier));
            $alamat       = strtoupper(trim($dataprocess->alamat));
            $idprovinsi       = strtoupper(trim($dataprocess->idprovinsi));
            $idkota       = strtoupper(trim($dataprocess->idkota));
            $phone        = trim($dataprocess->phone);
            // $idsticker      = strtoupper(trim($dataprocess->idsticker));
            $fax   = strtoupper(trim($dataprocess->fax));
            $email    = strtoupper(trim($dataprocess->email));
            $npwp           = strtoupper(trim($dataprocess->npwp));
            $npkp        = strtoupper(trim($dataprocess->npkp));
            $jthtempo = $dataprocess->jthtempo;
            $chold = $dataprocess->chold;
            $plafon = $dataprocess->plafon;

            $idmarket         = strtoupper(trim($dataprocess->idmarket));
            $cp         = strtoupper(trim($dataprocess->cp));
            $jabatan  = strtoupper(trim($dataprocess->jabatan));
            $keterangan          = strtoupper(trim($dataprocess->keterangan));
            $interngroup = isset($dataprocess->interngroup)
                ? strtoupper(trim($dataprocess->interngroup))
                : 'NO';

            $blacklist = isset($dataprocess->blacklist)
                ? strtoupper(trim($dataprocess->blacklist))
                : 'NO';

            // $interngroup           = ($dataprocess->interngroup);
            // $blacklist    = ($dataprocess->blacklist);

            $createdby = $nama;
            $createddate = date('Y-m-d H:i:s');
            $builder = $this->db->table('sc_mst.mstsupplier');
            if ($type === 'INPUT') {
                $info = array(
                    'kdsupplier'         => $kdsupplier,
                    'nmsupplier'     => $nmsupplier,
                    'chold' => $chold,
                    // 'deptpic'       => $deptpic,
                    'alamat'       => $alamat,
                    'idprovinsi'       => $idprovinsi,
                    'idkota'       => $idkota,
                    'phone'     => $phone,
                    // 'idsticker'      => $idsticker,
                    'fax'      => $fax,
                    'email'    => $email,
                    'npwp'    => $npwp,
                    'npkp'    => $npkp,

                    'jthtempo'    => $jthtempo,
                    'plafon'    => $plafon,
                    

                    'idmarket'=> $idmarket,
                    'cp'       => $cp,
                    'jabatan'=> $jabatan,
                    'keterangan' => $keterangan,
                    'interngroup' => $interngroup,
                    'blacklist'   => $blacklist,     
                    // 'interngroup' => $interngroup,
                    // 'blacklist'   => $blacklist,
                    'status'        => 'F',
                    'createdby'       => $createdby,
                    'createddate'     => $createddate,

                );
                if ($builder->insert($info)) {
                    // $inx = array('status' => 'F');
                    // $builder->where('docno',$nama);
                    // $builder->update($inx);
                    // $suppliersRow = $builder
                    //     ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                    //     ->get()
                    //     ->getRowArray();
                    // if ($suppliersRow) {
                    //     $docnoFinal   = trim($suppliersRow['docno']);
                    //     $url = array(
                    //         'barcodelink' => base_url('master/data/getDataFormSupplier/' . $this->fiky_encryption->sealed($docnoFinal)),
                    //     );
                    //     $builder->where('TRIM(docno)', $docnoFinal);
                    //     $builder->update($url);

                    // }

                    // $paramerror=" and userid='$nama' and modul='I.M.B.2'";
                    // $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.trim($kdsupplier));
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and id='$id'";
                $check = $this->m_suppliers->q_mstsuppliers($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                    'kdsupplier'         => $kdsupplier,
                    'nmsupplier'     => $nmsupplier,
                    'chold' => $chold,
                    // 'deptpic'       => $deptpic,
                    'alamat'       => $alamat,
                    'idprovinsi'       => $idprovinsi,
                    'idkota'       => $idkota,
                    'phone'     => $phone,
                    // 'idsticker'      => $idsticker,
                    'fax'      => $fax,
                    'email'    => $email,
                    'npwp'    => $npwp,
                    'npkp'    => $npkp,

                    'jthtempo'    => $jthtempo,
                    'plafon'    => $plafon,
                    

                    'idmarket'=> $idmarket,
                    'cp'       => $cp,
                    'jabatan'=> $jabatan,
                    'interngroup' => $interngroup,
                    'blacklist'   => $blacklist,     
                    'keterangan' => $keterangan,
                    // 'interngroup' => $interngroup,
                    // 'blacklist'   => $blacklist,
                    'status'        => 'F',
                    'updateby'       => $createdby,
                    'updatedate'     => $createddate
                    );


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

                $builder = $this->db->table('sc_mst.mstsupplier');
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