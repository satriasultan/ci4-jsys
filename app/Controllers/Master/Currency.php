<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Currency extends BaseController
{
    
    //CURRENCY
    public function currency()
    {
        $data['title']="Currency";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.4'; $versirelease='I.M.B.4/BETA.001'; $releasedate=date('2024-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.4'";
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
        $pterror = " and userid='$nama'";
          //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu = 'I.M.B.4';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/currency/v_list_currency',$data);
    }

    function list_currency()
    {
        $kmenu = 'I.M.B.4';
        $role = trim($this->session->get('roleid'));
        $nama = trim($this->session->get('nama'));
        
        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();
        // $bagian = trim($dataanu['userinfo']['bagian']);

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';


        $list = $this->m_currency->get_t_currency_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $enc_id=$this->fiky_encryption->sealed(trim($lm->id));
            $enc_currname=$this->fiky_encryption->sealed(trim($lm->currname));
            $no++;
            $row = array();
            $row[] = $no;
            
            $btnActions = '';

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
                $btnActions .= '<a class="dropdown-item text-warning"
                    href="' . base_url('master/data/editCurrency/'.$enc_id.'/'.$enc_currname) . '"
                    onclick="return confirm(\'Edit Currency : '.trim($lm->currname).' ?\')">
                    <i class="fa fa-edit"></i> Edit Data
                </a>';
            }

            if ($canView) {
                $btnActions .= '<a class="dropdown-item text-info"
                    href="' . base_url('master/data/detailCurrency/'.$enc_id.'/'.$enc_currname) . '">
                    <i class="fa fa-eye"></i> Detail Data
                </a>';
            }

            if ($canDelete) {
                $btnActions .= '<a class="dropdown-item text-danger"
                    href="' . base_url('master/data/hapusCurrency/'.$enc_id.'/'.$enc_currname) . '"
                    onclick="return confirm(\'Hapus Currency : '.trim($lm->currname).' ?\')">
                    <i class="fa fa-trash"></i> Delete Data
                </a>';
            }

            $btnActions .= '</div></div>';


            $row[] = $btnActions;
            // // //coba
            // $row[] = '
            //     <a class="btn btn-warning btn-sm btn-flat" href='."'". base_url('master/data/editCurrency').'/'.''.$enc_id.'/'.$enc_currname."'".'  title="Editing Currency" onclick="return confirm('."'".'Editing Currency ?  : '.trim($lm->currname)."'".')"><i class="fa fa-gear"></i> </a>
            //     <a class="btn btn-primary btn-sm btn-flat" href='."'". base_url('master/data/detailCurrency').'/'.''.$enc_id.'/'.$enc_currname."'".'  title="View Detail Currency" onclick="return confirm('."'".'View Detail Currency ?  : '.trim($lm->currname)."'".')"><i class="fa fa-eye"></i> </a>
            //     <a class="btn btn-danger btn-sm  btn-flat" href='."'". base_url('master/data/hapusCurrency').'/'.''.$enc_id.'/'.$enc_currname."'".'  title="Hapus Data Currency" onclick="return confirm('."'".'Detail Menu Programs ?  : '.trim($lm->currname)."'".')"><i class="fa fa-trash-o"></i> </a>
            // ';
            // $row[] = $lm->id;
            $row[] = $lm->currcode;
            $row[] = $lm->currname;
            $row[] = $lm->createdby;
            $row[] = $lm->createddate;
            $row[] = $lm->updateby;
            $row[] = $lm->updatedate;
            // $row[] = $lm->alamat;
            // $row[] = $lm->tarif;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_currency->t_currency_view_count_all(),
            "recordsFiltered" => $this->m_currency->t_currency_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function showing_currency(){
        $nama = trim($this->session->get('nama'));
        $id = strtoupper(trim($this->request->getGet('id')));

        $param = '';
        if ($id !== '') {
            $param = " AND id = " . intval($id); // aman karena konversi ke integer
        } else {
            $param = " AND id = 0 ";
        }

        $data = $this->m_currency->q_mstcurrency($param);

        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    public function saveCurrency()
    {
        $json = $this->request->getPost('data');
        $payload = json_decode($json, true);

        if (!$payload || !isset($payload['body'])) {
            return $this->response->setJSON([
                'status' => false,
                'messages' => 'Format data tidak valid.'
            ]);
        }

        $data = $payload['body'];
        $id = isset($data['id']) ? intval($data['id']) : 0;
        $tipe = $data['tipe'];
        $currname = strtoupper(trim($data['currname']));
        $currcode = strtoupper(trim($data['currcode']));
        $createdAt = date('Y-m-d H:i:s');
        $nama_input = $this->session->get('nama');

        $builder = $this->db->table('sc_mst.currency');

        if ($tipe === 'INPUT') {
            // Cek duplikasi
            $cek = $builder->where('currcode', $currcode)->countAllResults();
            if ($cek > 0) {
                return $this->response->setJSON([
                    'status' => false,
                    'messages' => 'Currency sudah terdaftar.'
                ]);
            }

            $data_insert = [
                'currname' => $currname,
                'currcode' => $currcode,
                'createddate' => $createdAt,
                'createdby' => $nama_input
            ];

            $builder->insert($data_insert);
            
            // Get the last inserted ID
            $inserted_id = $this->db->insertID();
            
            // Encrypt ID dan currname untuk redirect
            $enc_id = $this->fiky_encryption->sealed(trim($inserted_id));
            $enc_currname = $this->fiky_encryption->sealed(trim($currname));
            
            return $this->response->setJSON([
                'status' => true,
                'messages' => 'Data berhasil disimpan. Kode Currency: ' . $currcode,
                'redirect_data' => [
                    'enc_id' => $enc_id,
                    'enc_currname' => $enc_currname
                ]
            ]);
            
        } else if ($tipe === 'UPDATE') {
            $data_update = [
                'currname' => $currname,
                'currcode' => $currcode,
                'updatedate' => $createdAt,
                'updateby' => $nama_input
            ];

            $builder->where('id', $id)->update($data_update);
            
            return $this->response->setJSON([
                'status' => true,
                'messages' => 'Data berhasil diupdate. Kode Currency: ' . $currcode
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'messages' => 'Data Gagal Di Proses Ada Kesalahan Data'
            ]);
        }
    }




    public function saveFinalCurrency()
    {
        $json = $this->request->getPost('data');
        $payload = json_decode($json, true);
        $nama = trim($this->session->get('nama'));

        if (!$payload || !isset($payload['body'])) {
            return $this->response->setJSON([
                'status' => false,
                'messages' => 'Format data tidak valid.'
            ]);
        }

        $data = $payload['body'];
        $id   = (int)($data['id'] ?? 0);
        $tipe = $data['tipe'] ?? '';

        if ($tipe !== 'UPDATE' || $id <= 0) {
            return $this->response->setJSON([
                'status' => false,
                'messages' => 'ID atau tipe proses tidak valid.'
            ]);
        }

        $currcode = strtoupper(trim($data['currcode'] ?? ''));

        $data_update = [
            'currname' => strtoupper(trim($data['currname'] ?? '')),
            'currcode' => $currcode,

            'phutang' => $data['phutang'] ?? null,
            'pum' => $data['pum'] ?? null,
            'pbonus' => $data['pbonus'] ?? null,
            'hutangac' => $data['hutangac'] ?? null,
            'hutangbiaya1' => $data['hutangbiaya1'] ?? null,
            'hutangbiaya2' => $data['hutangbiaya2'] ?? null,

            'ppiutang' => $data['ppiutang'] ?? null,
            'pumjual' => $data['pumjual'] ?? null,
            'ppendapatan' => $data['ppendapatan'] ?? null,
            'pretur' => $data['pretur'] ?? null,
            'pdisc' => $data['pdisc'] ?? null,
            'pbonusjual' => $data['pbonusjual'] ?? null,
            'ptunai' => $data['ptunai'] ?? null,
            'piutangac' => $data['piutangac'] ?? null,
            'pendapatanac' => $data['pendapatanac'] ?? null,
            'pps' => $data['pps'] ?? null,

            'updatedate' => date('Y-m-d H:i:s'),
            'updateby' => $nama
        ];

        $this->db->table('sc_mst.currency')
            ->where('id', $id)
            ->update($data_update);

        return $this->response->setJSON([
            'status' => true,
            'messages' => 'Data berhasil diupdate. Kode Currency: ' . $currcode
        ]);
    }


    
    public function input_currency()
    {
        $loccode = trim($this->session->get('loccode'));
        $paramx = " AND idlocation = '$loccode'";
        $dtllocation = $this->m_location->q_mlocation($paramx)->getRowArray();

        $data['title'] = ucfirst("INPUT CUSTOMER: " . $dtllocation['idlocation'] . ' - ' . $dtllocation['nmlocation']);
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        // Versi aplikasi
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.4';
        $versirelease = 'I.M.B.4/BETA.001';
        $releasedate = date('2024-04-12 00:00:00');
        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;

        // Error handler
        $paramerror = " AND userid = '$nama' AND modul = 'I.M.B.4'";
        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        $errordesc = isset($dtlerror['description']) ? trim($dtlerror['description']) : '';
        $nomorakhir1 = isset($dtlerror['nomorakhir1']) ? trim($dtlerror['nomorakhir1']) : '';
        $errorcode = isset($dtlerror['errorcode']) ? trim($dtlerror['errorcode']) : '';

        if ($count_err > 0 && $errordesc !== '') {
            $data['message'] = $dtlerror['errorcode'] == 0
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "<div class='alert alert-info'>$errordesc</div>";
        } else {
            $data['message'] = ($errorcode == '0')
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "";
        }

        $data['back_url'] = base_url("master/data/currency");
        $data['typeform'] = 'INPUT';
        $data['status'] = '';

        // Hak akses
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kodemenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" AND username = '$nama'")->getRowArray();
    
        // Hapus error log lama
        $pterror = " AND userid = '$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        return $this->template->render('master/currency/v_input_currency', $data);
    }


    public function editCurrency($enc_id = null, $enc_currname = null)
    {
        $loccode = trim($this->session->get('loccode'));
        $paramx = " AND idlocation = '$loccode'";
        $dtllocation = $this->m_location->q_mlocation($paramx)->getRowArray();

        $data['title'] = ucfirst("EDIT CUSTOMER: " . $dtllocation['idlocation'] . ' - ' . $dtllocation['nmlocation']);
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        // Versi aplikasi
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.4';
        $versirelease = 'I.M.B.4/BETA.001';
        $releasedate = date('2024-04-12 00:00:00');
        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;

        // Error handler
        $paramerror = " AND userid = '$nama' AND modul = 'I.M.B.4'";
        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        $errordesc = isset($dtlerror['description']) ? trim($dtlerror['description']) : '';
        $nomorakhir1 = isset($dtlerror['nomorakhir1']) ? trim($dtlerror['nomorakhir1']) : '';
        $errorcode = isset($dtlerror['errorcode']) ? trim($dtlerror['errorcode']) : '';

        if ($count_err > 0 && $errordesc !== '') {
            $data['message'] = $dtlerror['errorcode'] == 0
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "<div class='alert alert-info'>$errordesc</div>";
        } else {
            $data['message'] = ($errorcode == '0')
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "";
        }

        $id = trim($this->fiky_encryption->unseal($enc_id));
        $currname = trim($this->fiky_encryption->unseal($enc_currname));
        $data['back_url'] = base_url("master/data/currency");
        $data['typeform'] = 'UPDATE';
        $data['idcurrency'] = $id;
        $data['status'] = '';

        // Ambil data currency berdasarkan id
        $currency = $this->m_currency->q_mstcurrency(" AND id = '$id' ")->getRowArray();
        if (!$currency) {
            return redirect()->to(base_url("master/data/currency"))
                ->with('error', 'Data currency tidak ditemukan.');
        }
        $data['currency'] = $currency;

        // Hak akses
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kodemenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" AND username = '$nama'")->getRowArray();
        if (isset($data['userinfo']['bagian'])) {
            $data['bagian'] = trim($data['userinfo']['bagian']);
        }

        // Hapus error log lama
        $pterror = " AND userid = '$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        return $this->template->render('master/currency/v_input_currency', $data);
    }


    
    public function detailCurrency($enc_id = null, $enc_currname = null)
    {
        $loccode = trim($this->session->get('loccode'));
        $paramx = " AND idlocation = '$loccode'";
        $dtllocation = $this->m_location->q_mlocation($paramx)->getRowArray();

        $data['title'] = ucfirst("DETAIL CUSTOMER: " . $dtllocation['idlocation'] . ' - ' . $dtllocation['nmlocation']);
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        // Versi aplikasi
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.4';
        $versirelease = 'I.M.B.4/BETA.001';
        $releasedate = date('2024-04-12 00:00:00');
        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;

        // Error handler
        $paramerror = " AND userid = '$nama' AND modul = 'I.M.B.4'";
        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        $errordesc = isset($dtlerror['description']) ? trim($dtlerror['description']) : '';
        $nomorakhir1 = isset($dtlerror['nomorakhir1']) ? trim($dtlerror['nomorakhir1']) : '';
        $errorcode = isset($dtlerror['errorcode']) ? trim($dtlerror['errorcode']) : '';

        if ($count_err > 0 && $errordesc !== '') {
            $data['message'] = $dtlerror['errorcode'] == 0
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "<div class='alert alert-info'>$errordesc</div>";
        } else {
            $data['message'] = ($errorcode == '0')
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "";
        }

        $id = trim($this->fiky_encryption->unseal($enc_id));
        $currname = trim($this->fiky_encryption->unseal($enc_currname));
        $data['back_url'] = base_url("master/data/currency");
        $data['typeform'] = 'DETAIL';
        $data['idcurrency'] = $id;
        $data['status'] = '';

        // Ambil data currency berdasarkan id
        $currency = $this->m_currency->q_mstcurrency(" AND id = '$id' ")->getRowArray();
        if (!$currency) {
            return redirect()->to(base_url("master/data/currency"))
                ->with('error', 'Data currency tidak ditemukan.');
        }
        $data['currency'] = $currency;

        // Hak akses
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kodemenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" AND username = '$nama'")->getRowArray();
        if (isset($data['userinfo']['bagian'])) {
            $data['bagian'] = trim($data['userinfo']['bagian']);
        }

        // Hapus error log lama
        $pterror = " AND userid = '$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);

        return $this->template->render('master/currency/v_input_currency', $data);
    }


    public function hapusCurrency($enc_idcurrency = null, $enc_nmcurrency = null)
    {
        $data = [];

        if (!$enc_idcurrency) {
            $data['error'] = 'ID Currency tidak valid.';
            // return $this->template->render('master/currency/v_list_currency', $data);
            return redirect()->to(base_url('master/currency/master/currency'))->with('error', 'ID Currency tidak valid.');
        }
        
        $idcurrency = trim($this->fiky_encryption->unseal($enc_idcurrency));
        
        if (!$idcurrency) {
            $data['error'] = 'ID Currency gagal didekripsi.';
            // return $this->template->render('master/currency/v_list_currency', $data);
            return redirect()->to(base_url('master/currency/master/currency'))->with('error', 'ID Currency gagal didekripsi.');
        }

        // Cek data terlebih dahulu
        $cek = $this->db->table('sc_mst.currency')->where('id', $idcurrency)->get()->getRow();
        if (!$cek) {
            $data['error'] = 'Data Currency tidak ditemukan.';
        } else {
              // Proses hapus
                try {
                    $this->db->table('sc_mst.currency')->where('id', $idcurrency)->delete();
                    return redirect()->to(base_url('master/currency/master/currency'))->with('success', 'Data Currency berhasil dihapus.');
                } catch (\Exception $e) {
                    return redirect()->to(base_url('master/currency/master/currency'))->with('error', 'Gagal menghapus data Currency: ' . $e->getMessage());
                }
        }
    }

    public function insertNewExchange()
    {
        // Ambil data dari session
        $nama = trim($this->session->get('nama'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        // Ambil body request dalam bentuk JSON
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        // Validasi apakah request memiliki key yang benar
        if (!isset($data->key) || $data->key !== '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid Request Key!'
            ]);
        }

        // Set docno sama dengan inputby
        $idcurrency = isset($data->body->idcurrency) ? trim($data->body->idcurrency) : 'No Docno Provided';

        // Data untuk disimpan ke database
        $data_insert = [
            'idcurr' => $idcurrency,
        ];

        // Insert ke database
        $builder = $this->db->table('sc_mst.exchangerate');
        $insert = $builder->insert($data_insert);

        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Supplier successfully created!',
                'idcurrency' => $idcurrency
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create Supplier. Please try again.'
            ]);
        }
    }


    function showing_exchange_rate(){
        $idcurrency = strtoupper(trim($this->request->getGet('idcurrency')));
        $list = $this->m_currency->get_t_exchangerate_view($idcurrency);
        $param=" and idcurrency='$idcurrency'";

        // $datadtl = $this->m_currency->q_trxcapexnew($param);
        // $tampungdtl = $datadtl->getResult();
        // $detail = $tampungdtl[0] ?? null;
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->id;
            // $row[] = '<input class=" " maxlength="50" style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="nmsupplier_' . htmlspecialchars($lm->id, ENT_QUOTES, 'UTF-8') . '" name="nmsupplier_' . htmlspecialchars($lm->id, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->nmsupplier, ENT_QUOTES, 'UTF-8') . '" disabled >';
            // $row[] = '<input type="text" class=" ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;"  id="pricelst_'.$lm->id.'" name="pricelst_'.$lm->id.'" value="'.number_format($lm->pricelst, 2, '.', '').'" disabled >';
            $row[] = '<input type="text" class="" style="margin:0px; background-color:#d6d5d5;width: 100%;" id="exchangedate_'.$lm->id.'" name="exchangedate_'.$lm->id.'" value="'.($lm->exchangedate ? htmlspecialchars(date('d-m-Y H:i:s', strtotime($lm->exchangedate)), ENT_QUOTES, 'UTF-8') : '').'" disabled >';

            
            $row[] = '<input type="text" class=" ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" id="nilai_'.$lm->id.'" name="nilai_'.$lm->id.'" value="'.number_format($lm->nilai, 2, '.', ',').'" disabled >';
            // $row[] = '<input type="text" class=" ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" id="pricelst_'.$lm->idurut.'" name="pricelst_'.$lm->idurut.'" value="'.number_format($lm->pricelst, 2, ',', '.').'" disabled >';

            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_currency->t_exchangerate_view_count_all($idcurrency),
            "recordsFiltered" => $this->m_currency->t_exchangerate_view_count_filtered($idcurrency),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    public function update_exchangerate()
    {
        $nama = trim($this->session->get('nama')); // Mengambil docno dari session
        $request_body = file_get_contents('php://input');
        $data = $this->request->getJSON(); // Mengambil data JSON langsung

        // Ambil data POST (dari frontend)
        $updates = $this->request->getPost('updates');
        $masterData = $this->request->getPost('masterData'); // Ambil data master dari POST

        $response = [];

        if (!empty($updates)) {
            foreach ($updates as $update) {
                $idurut = $update['idurut'] ?? null;
                $nilai = $update['nilai'] ?? 0;

                
                $exchangedate_input = $update['exchangedate'] ?? '';
                $exchangedate = null;

                if ($exchangedate_input && strtotime($exchangedate_input)) {
                    $exchangedate = date('Y-m-d', strtotime($exchangedate_input));
                }
                // $category = $update['category'] ?? '';

                // $description = $update['description'] ?? '';
                // $top = $update['top'] ?? '';

                if (empty($idurut)) {
                    continue; // Skip jika idurut kosong
                }

                // Ambil data supplier sebelumnya dari database
                $builder = $this->db->table('sc_mst.exchangerate');

                // Data yang akan diupdate
                $infoupdate = [
                    'nilai' => $nilai,
                    // 'nmsupplier' => $nmsupplier != '' ? strtoupper($nmsupplier) : $nmsupplier ,
                    'exchangedate' => $exchangedate,
                    'updateby' => $nama,
                    'updatedate' => date('Y-m-d H:i:s')
                ];

                // Update berdasarkan idurut dan docno = nama
                $builder->where('id', $idurut);
                $builder->where('idcurr', $masterData['idcurrency']);

                if ($builder->update($infoupdate)) {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => true,
                        'message' => 'Update successful'
                    ];
                } else {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => false,
                        'message' => 'Update failed'
                    ];
                }
            }

            // Update status di tabel master
            // $builderMaster = $this->db->table('sc_trx.penawaran_capex_mst');
            // $builderMaster->where('docno', $masterData['docno']);
            // $builderMaster->update(['status' => 'E']); // Status untuk master

            // Berikan respons ke frontend
            echo json_encode([
                'status' => 'success',
                'message' => 'Data updated successfully',
                'results' => $response
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No updates provided'
            ]);
        }
    }

    
    public function deleteExchangeRate()
    {
        $id = $this->request->getPost('id'); // Ambil array ID
        $idcurrency = $this->request->getPost('idcurrency'); // Ambil idcurrency yang dikirim dari AJAX

        if (empty($id) || empty($idcurrency)) {
            echo json_encode(['status' => false, 'messages' => 'Missing Parameters']);
            return;
        }

        $builderDtl = $this->db->table('sc_mst.exchangerate');

        // Hapus dari exchangerate(hapus di exchangerate)
        $builderDtl->where('idcurr', $idcurrency)
            ->whereIn('id', $id)
            ->delete();

        echo json_encode(['status' => true, 'messages' => 'Data Deleted Successfully']);
    }

    //END CURRENCY

}