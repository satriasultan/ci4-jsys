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
        $kodemenu='I.M.B.4'; $versirelease='I.M.B.4/01'; $releasedate=date('2024-04-12 00:00:00');
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
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle text-white"
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

        $dataloc_id = isset($dtllocation['idlocation']) ? trim($dtllocation['idlocation']) : '';
        $dataloc_nm = isset($dtllocation['nmlocation']) ? trim($dtllocation['nmlocation']) : '';
        $data['title'] = ucfirst("INPUT CURRENCY: " . $dataloc_id . ' - ' . $dataloc_nm);
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        // Versi aplikasi
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.4';
        $versirelease = 'I.M.B.4/01';
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

        $dataloc_id = isset($dtllocation['idlocation']) ? trim($dtllocation['idlocation']) : '';
        $dataloc_nm = isset($dtllocation['nmlocation']) ? trim($dtllocation['nmlocation']) : '';
        $data['title'] = ucfirst("EDIT CURRENCY: " . $dataloc_id . ' - ' . $dataloc_nm);
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        // Versi aplikasi
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.4';
        $versirelease = 'I.M.B.4/01';
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
        $data['exchange_setting'] = $this->m_currency->getExchangeRateSetting();

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

        $dataloc_id = isset($dtllocation['idlocation']) ? trim($dtllocation['idlocation']) : '';
        $dataloc_nm = isset($dtllocation['nmlocation']) ? trim($dtllocation['nmlocation']) : '';
        $data['title'] = ucfirst("DETAIL CURRENCY: " . $dataloc_id . ' - ' . $dataloc_nm);
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        // Versi aplikasi
        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.4';
        $versirelease = 'I.M.B.4/01';
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
        $data['exchange_setting'] = $this->m_currency->getExchangeRateSetting();

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

        // Set idcurrency dari modal popup exchange rate
        $idcurrency = isset($data->body->idcurrency) ? trim((string) $data->body->idcurrency) : '';

        if ($idcurrency === '') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Currency is not selected.'
            ]);
        }

        /* Exchange date dari modal popup : format DD-MM-YYYY.
           Kolom timestamp tetap diisi jam 00:00:00 agar satu tanggal
           hanya mewakili satu kurs (padanan exchangedate::date). */
        $exchangedate_input = isset($data->body->exchangedate) ? trim((string) $data->body->exchangedate) : '';
        if ($exchangedate_input !== '' && strtotime($exchangedate_input) !== false) {
            $exchangedate = date('Y-m-d 00:00:00', strtotime($exchangedate_input));
        } else {
            $exchangedate = date('Y-m-d 00:00:00'); // default : tanggal hari ini, jam selalu 00:00:00
        }

        // Nilai exchange rate (NUMERIC 18,4) => selalu 4 digit desimal
        $nilai_input = isset($data->body->nilai) ? str_replace(',', '', trim((string) $data->body->nilai)) : '';
        $nilai = ($nilai_input === '' || !is_numeric($nilai_input)) ? 0 : (float) $nilai_input;

        // Proteksi : tanggal hanya boleh satu data & nilai tidak boleh sama dengan kurs terakhir
        $validasi = $this->_validateExchangeRate($idcurrency, $exchangedate, $nilai);
        if (!$validasi['ok']) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $validasi['message']
            ]);
        }

        // Data untuk disimpan ke database
        $data_insert = [
            'idcurr'       => $idcurrency,
            'exchangedate' => $exchangedate,
            'nilai'        => number_format($nilai, 4, '.', ''),
            'createdby'    => substr($inputby, 0, 20), // kolom character(20)
            'createddate'  => $inputdate,
        ];

        // Insert ke database
        $builder = $this->db->table('sc_mst.exchangerate');
        $insert = $builder->insert($data_insert);

        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Exchange rate successfully created!',
                'idcurrency' => $idcurrency
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create exchange rate. Please try again.'
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
            /* Exchange date & nilai ditampilkan sebagai text (read only).
               Input / update data dilakukan melalui modal popup "Exchange Rate"
               (lihat currency.js : openExchangeRateModal()).
               Jam selalu 00:00:00 sehingga yang ditampilkan hanya tanggalnya. */
            $exchangedate_display = $lm->exchangedate ? date('d-m-Y', strtotime($lm->exchangedate)) : '';

            $row[] = '<span class="exchange-date">' . htmlspecialchars($exchangedate_display, ENT_QUOTES, 'UTF-8') . '</span>';


            /* Tampilkan nilai exchange rate sesuai presisi kolom (NUMERIC 18,4).
               Format angka polos: titik = pemisah desimal, TANPA pemisah ribuan/koma.
               Selalu tampil tepat 4 digit di belakang koma (contoh: 17000.0000). */
            $nilai_display = number_format((float) $lm->nilai, 4, '.', '');

            $row[] = '<span class="exchange-nilai">' . $nilai_display . '</span>';

            /* Tombol Update : membuka modal popup berisi Exchange Date & Price */
            $row[] = '<button type="button" class="btn btn-sm btn-primary btn-edit-exchange"'
                . ' data-id="' . htmlspecialchars($lm->id, ENT_QUOTES, 'UTF-8') . '"'
                . ' data-exchangedate="' . htmlspecialchars($exchangedate_display, ENT_QUOTES, 'UTF-8') . '"'
                . ' data-nilai="' . $nilai_display . '"'
                . ' title="Update Exchange Rate"><i class="fa fa-pencil"></i></button>';
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
                    // kolom timestamp => disimpan sebagai tanggal + jam 00:00:00 (satu tanggal = satu kurs)
                    $exchangedate = date('Y-m-d 00:00:00', strtotime($exchangedate_input));
                }
                // $category = $update['category'] ?? '';

                // $description = $update['description'] ?? '';
                // $top = $update['top'] ?? '';

                if (empty($idurut)) {
                    continue; // Skip jika idurut kosong
                }

                $idcurrency = isset($masterData['idcurrency']) ? trim((string) $masterData['idcurrency']) : '';
                if ($idcurrency === '') {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => false,
                        'message' => 'Currency is not selected.'
                    ];
                    continue;
                }

                // Proteksi : tanggal tidak boleh bentrok dengan data lain
                // dan nilai tidak boleh sama dengan kurs terakhir
                $validasi = $this->_validateExchangeRate($idcurrency, $exchangedate, $nilai, $idurut);
                if (!$validasi['ok']) {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => false,
                        'message' => $validasi['message']
                    ];
                    continue;
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

                // Update berdasarkan idurut dan idcurr
                $builder->where('id', $idurut);
                $builder->where('idcurr', $idcurrency);

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

            // Berikan respons ke frontend (error jika ada baris yang ditolak proteksi)
            $failed = array_values(array_filter($response, static function ($row) {
                return empty($row['status']);
            }));

            echo json_encode([
                'status' => empty($failed) ? 'success' : 'error',
                'message' => empty($failed) ? 'Data updated successfully' : $failed[0]['message'],
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

    /* =========================================================
     * PROTEKSI EXCHANGE DATE & NILAI
     * 1. Satu currency hanya boleh punya satu data per tanggal.
     *    Rentang tanggal dihitung dari 00:00:00 s/d 23:59:59
     *    (padanan exchangedate::date pada PostgreSQL), jadi jam
     *    berapa pun dianggap tanggal yang sama.
     * 2. Nilai yang sama dengan exchange rate terakhir tidak boleh
     *    diinput lagi karena masih memakai kurs terakhir.
     * ========================================================= */
    private function _getExchangeRateByDate($idcurr, $dateOnly, $excludeId = null)
    {
        $builder = $this->db->table('sc_mst.exchangerate')
            ->select('id, nilai, exchangedate')
            ->where('idcurr', $idcurr)
            ->where('exchangedate >=', $dateOnly . ' 00:00:00')
            ->where('exchangedate <', date('Y-m-d 00:00:00', strtotime($dateOnly . ' +1 day')));

        if ($excludeId !== null && $excludeId !== '') {
            $builder->where('id <>', $excludeId);
        }

        return $builder->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
    }

    private function _getLastExchangeRate($idcurr, $excludeId = null)
    {
        $builder = $this->db->table('sc_mst.exchangerate')
            ->select('id, nilai, exchangedate')
            ->where('idcurr', $idcurr);

        if ($excludeId !== null && $excludeId !== '') {
            $builder->where('id <>', $excludeId);
        }

        return $builder->orderBy('exchangedate', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    private function _validateExchangeRate($idcurr, $exchangedate, $nilai, $excludeId = null)
    {
        $nilaiBaru = number_format((float) $nilai, 4, '.', '');
        $timestamp = ($exchangedate !== null && $exchangedate !== '')
            ? strtotime((string) $exchangedate)
            : false;

        // Tanggal asli baris yang sedang diupdate (jika ada)
        $currentDateOnly = null;
        if ($excludeId !== null && $excludeId !== '') {
            $current = $this->db->table('sc_mst.exchangerate')
                ->select('exchangedate')
                ->where('id', $excludeId)
                ->where('idcurr', $idcurr)
                ->get()
                ->getRowArray();

            if ($current && !empty($current['exchangedate'])) {
                $currentDateOnly = date('Y-m-d', strtotime((string) $current['exchangedate']));
            }
        }

        // 1. Proteksi tanggal : tidak boleh ada 2 data untuk tanggal yang sama
        if ($timestamp !== false) {
            $dateOnly = date('Y-m-d', $timestamp);

            // Saat update dan tanggal tidak berubah, proteksi tanggal dilewati
            if ($dateOnly !== $currentDateOnly) {
                $existing = $this->_getExchangeRateByDate($idcurr, $dateOnly, $excludeId);

                if ($existing) {
                    return [
                        'ok'      => false,
                        'message' => 'Exchange date ' . date('d-m-Y', $timestamp) . ' sudah terisi (nilai: '
                            . number_format((float) $existing['nilai'], 4, '.', '') . '). '
                            . 'Satu tanggal hanya boleh satu data kurs, silakan gunakan Update.',
                    ];
                }
            }
        }

        // 2. Proteksi nilai : nilai sama dengan kurs terakhir tidak perlu disimpan
        $last = $this->_getLastExchangeRate($idcurr, $excludeId);
        if ($last && number_format((float) $last['nilai'], 4, '.', '') === $nilaiBaru) {
            return [
                'ok'      => false,
                'message' => 'Nilai kurs ' . $nilaiBaru . ' sama dengan exchange rate terakhir ('
                    . date('d-m-Y', strtotime((string) $last['exchangedate'])) . '). '
                    . 'Tidak bisa disimpan karena masih memakai exchange rate terakhir.',
            ];
        }

        return ['ok' => true, 'message' => ''];
    }

    /* =========================================================
     * PENGATURAN & SINKRONISASI EXCHANGE RATE OTOMATIS
     * ========================================================= */
    public function getExchangeSetting()
    {
        return $this->response->setJSON([
            'status'  => true,
            'setting' => $this->m_currency->getExchangeRateSetting(),
        ]);
    }

    public function saveExchangeSetting()
    {
        $mode = strtolower(trim((string) $this->request->getPost('mode')));
        if (!in_array($mode, ['auto', 'manual'], true)) {
            $mode = 'manual';
        }

        $apiUrl = $this->_normalizeExchangeApiUrl((string) $this->request->getPost('api_url'));
        if ($apiUrl === '') {
            $apiUrl = 'https://api.frankfurter.dev/v2/rate/{FROM}/{TO}';
        }
        if (!preg_match('#^https?://#i', $apiUrl)) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'API URL harus diawali dengan http:// atau https://.',
            ]);
        }
        if (strpos($apiUrl, '{FROM}') === false) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'API URL wajib mengandung placeholder {FROM} untuk kode currency (contoh: https://api.frankfurter.dev/v2/rate/{FROM}/{TO}).',
            ]);
        }

        $baseCurrency = strtoupper(trim((string) $this->request->getPost('base_currency')));
        if ($baseCurrency === '') {
            $baseCurrency = 'IDR';
        }

        $aliasInput = $this->request->getPost('code_alias');
        $codeAlias = [];
        if (is_array($aliasInput)) {
            foreach ($aliasInput as $key => $value) {
                $key = strtoupper(trim((string) $key));
                $value = strtoupper(trim((string) $value));
                if ($key !== '' && $value !== '') {
                    $codeAlias[$key] = $value;
                }
            }
        }

        $nama = trim($this->session->get('nama'));
        $saved = $this->m_currency->saveExchangeRateSetting([
            'mode'          => $mode,
            'api_url'       => $apiUrl,
            'base_currency' => $baseCurrency,
            'code_alias'    => $codeAlias,
            'updatedby'     => $nama,
        ]);

        return $this->response->setJSON([
            'status'   => $saved,
            'messages' => $saved ? 'Pengaturan exchange rate berhasil disimpan.' : 'Gagal menyimpan pengaturan exchange rate.',
            'setting'  => $this->m_currency->getExchangeRateSetting(),
        ]);
    }

    public function testExchangeApi()
    {
        $currcode = strtoupper(trim((string) $this->request->getPost('currcode')));
        if ($currcode === '') {
            $currcode = 'USD';
        }

        $setting = $this->m_currency->getExchangeRateSetting();

        $apiUrl = $this->_normalizeExchangeApiUrl((string) $this->request->getPost('api_url'));
        if ($apiUrl !== '' && preg_match('#^https?://#i', $apiUrl)) {
            $setting['api_url'] = $apiUrl;
        }

        $baseCurrency = strtoupper(trim((string) $this->request->getPost('base_currency')));
        if ($baseCurrency !== '') {
            $setting['base_currency'] = $baseCurrency;
        }

        $aliasInput = $this->request->getPost('code_alias');
        if (is_array($aliasInput)) {
            $codeAlias = [];
            foreach ($aliasInput as $key => $value) {
                $key = strtoupper(trim((string) $key));
                $value = strtoupper(trim((string) $value));
                if ($key !== '' && $value !== '') {
                    $codeAlias[$key] = $value;
                }
            }
            $setting['code_alias'] = $codeAlias;
        }

        $result = $this->_fetchExchangeRateFromApi($currcode, $setting);

        if (!$result['ok'] || $result['rate'] === null) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => $result['error'] !== '' ? $result['error'] : 'Gagal mengambil exchange rate dari API.',
                'url'      => $result['url'],
            ]);
        }

        return $this->response->setJSON([
            'status'   => true,
            'messages' => 'API berhasil diakses.',
            'rate'     => $result['rate'],
            'nilai'    => number_format((float) $result['rate'], 4, '.', ''),
            'from'     => $result['from'],
            'to'       => $result['to'],
            'date'     => $result['date'],
            'url'      => $result['url'],
        ]);
    }


    public function fetchExchangeRate()
    {
        $idcurrency = (int) $this->request->getPost('idcurrency');
        if ($idcurrency <= 0) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Currency tidak valid.',
            ]);
        }

        $setting = $this->m_currency->getExchangeRateSetting();
        if (strtolower((string) $setting['mode']) !== 'auto') {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Mode pengambilan exchange rate masih MANUAL. Aktifkan mode OTOMATIS di menu Pengaturan.',
            ]);
        }

        $currency = $this->m_currency->q_mstcurrency(' AND id = ' . $idcurrency)->getRowArray();
        if (!$currency) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Data currency tidak ditemukan.',
            ]);
        }

        $currcode = trim($currency['currcode']);
        $result = $this->_fetchExchangeRateFromApi($currcode, $setting);

        if (!$result['ok'] || $result['rate'] === null) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => $result['error'] !== '' ? $result['error'] : 'Gagal mengambil exchange rate dari API.',
                'url'      => $result['url'],
            ]);
        }

        $nama = trim($this->session->get('nama'));
        $now = date('Y-m-d H:i:s');
        $nilai_rate = number_format((float) $result['rate'], 4, '.', '');

        /* MODE OTOMATIS :
           - Exchange date mengikuti tanggal kurs dari response API dan selalu jam 00:00:00.
           - Satu tanggal hanya boleh satu data kurs => tanggal yang sudah ada tidak dioverwrite
             (perubahan nilai pada tanggal yang sama dilakukan lewat modal Update). */
        $rateDate = $this->_resolveExchangeDate(isset($result['date']) ? $result['date'] : '');
        $exchangedate = $rateDate . ' 00:00:00';

        $existing = $this->_getExchangeRateByDate($idcurrency, $rateDate);
        if ($existing !== null) {
            $nilai_tersimpan = number_format((float) $existing['nilai'], 4, '.', '');

            return $this->response->setJSON([
                'status'          => false,
                'messages'        => 'Exchange date ' . date('d-m-Y', strtotime($rateDate))
                    . ' sudah terisi (kurs tersimpan: ' . $nilai_tersimpan
                    . ', kurs API: ' . $nilai_rate . '). Data tidak dioverwrite.',
                'nilai'           => $nilai_rate,
                'nilai_tersimpan' => $nilai_tersimpan,
                'from'            => $result['from'],
                'to'              => $result['to'],
                'idcurrency'      => $idcurrency,
            ]);
        }

        $insert = $this->db->table('sc_mst.exchangerate')->insert([
            'idcurr'       => $idcurrency,
            'exchangedate' => $exchangedate,
            'nilai'        => $nilai_rate,
            'createdby'    => substr($nama, 0, 20),
            'createddate'  => $now,
        ]);

        if (!$insert) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Gagal menyimpan exchange rate ke database.',
            ]);
        }

        return $this->response->setJSON([
            'status'     => true,
            'messages'   => 'Exchange rate ' . $result['from'] . ' -> ' . $result['to'] . ' ('
                . number_format((float) $result['rate'], 4, '.', '') . ') tanggal '
                . date('d-m-Y', strtotime($rateDate)) . ' berhasil disimpan.',
            'nilai'      => number_format((float) $result['rate'], 4, '.', ''),
            'from'       => $result['from'],
            'to'         => $result['to'],
            'date'       => $rateDate,
            'idcurrency' => $idcurrency,
        ]);
    }

    public function syncAllExchangeRate()
    {
        $setting = $this->m_currency->getExchangeRateSetting();
        if (strtolower((string) $setting['mode']) !== 'auto') {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Mode pengambilan exchange rate masih MANUAL. Aktifkan mode OTOMATIS terlebih dahulu.',
            ]);
        }

        $list = $this->m_currency->q_mstcurrency('')->getResultArray();
        $nama = trim($this->session->get('nama'));
        $now = date('Y-m-d H:i:s');
        $success = 0;
        $skipped = 0;
        $failed = 0;
        $details = [];

        foreach ($list as $row) {
            $currcode = strtoupper(trim($row['currcode']));
            $idcurr = (int) $row['id'];

            if ($currcode === strtoupper((string) $setting['base_currency'])) {
                /* Tampilkan juga kurs terakhir yang tersimpan (biasanya 1.0000) */
                $lastBase = $this->_getLastExchangeRate($idcurr);
                $message = 'Base currency, dilewati.';

                if ($lastBase) {
                    $message .= ' Kurs terakhir: ' . number_format((float) $lastBase['nilai'], 4, '.', '')
                        . ' (' . date('d-m-Y', strtotime((string) $lastBase['exchangedate'])) . ').';
                }

                $details[] = ['currcode' => $currcode, 'status' => 'SKIP', 'message' => $message];
                continue;
            }

            $result = $this->_fetchExchangeRateFromApi($currcode, $setting);
            if (!$result['ok'] || $result['rate'] === null) {
                $failed++;
                $details[] = ['currcode' => $currcode, 'status' => 'FAILED', 'message' => $result['error']];
                continue;
            }

            $nilai_rate = number_format((float) $result['rate'], 4, '.', '');

            /* MODE OTOMATIS :
               - Exchange date mengikuti tanggal kurs dari response API, selalu jam 00:00:00
                 (satu tanggal hanya satu kurs).
               - Tanggal yang sudah ada dilewati (tidak dioverwrite). */
            $rateDate = $this->_resolveExchangeDate(isset($result['date']) ? $result['date'] : '');

            $existing = $this->_getExchangeRateByDate($idcurr, $rateDate);
            if ($existing !== null) {
                $skipped++;
                $nilai_tersimpan = number_format((float) $existing['nilai'], 4, '.', '');

                $details[] = [
                    'currcode'        => $currcode,
                    'status'          => 'SKIP',
                    'message'         => 'Exchange date ' . date('d-m-Y', strtotime($rateDate))
                        . ' sudah ada (kurs tersimpan: ' . $nilai_tersimpan
                        . ', kurs API: ' . $nilai_rate . '), data tidak dioverwrite.',
                    'nilai_tersimpan' => $nilai_tersimpan,
                    'nilai_api'       => $nilai_rate,
                ];
                continue;
            }

            $insert = $this->db->table('sc_mst.exchangerate')->insert([
                'idcurr'       => $idcurr,
                'exchangedate' => $rateDate . ' 00:00:00',
                'nilai'        => $nilai_rate,
                'createdby'    => substr($nama, 0, 20),
                'createddate'  => $now,
            ]);

            if ($insert) {
                $success++;
                $details[] = [
                    'currcode' => $currcode,
                    'status'   => 'SUCCESS',
                    'message'  => date('d-m-Y', strtotime($rateDate)) . ' 00:00:00 = ' . $nilai_rate,
                ];
            } else {
                $failed++;
                $details[] = ['currcode' => $currcode, 'status' => 'FAILED', 'message' => 'Insert gagal.'];
            }
        }

        return $this->response->setJSON([
            'status'   => true,
            'messages' => 'Sync selesai. Berhasil: ' . $success . ', Dilewati: ' . $skipped . ', Gagal: ' . $failed . '.',
            'success'  => $success,
            'skipped'  => $skipped,
            'failed'   => $failed,
            'details'  => $details,
        ]);
    }


    /* Tentukan tanggal kurs (Y-m-d) dari response API.
       Jam tidak pernah dipakai : exchange date selalu disimpan 00:00:00.
       Tanggal kosong / tidak valid / tanggal masa depan => pakai tanggal hari ini. */
    private function _resolveExchangeDate($apiDate)
    {
        $value = trim((string) $apiDate);

        if ($value !== '') {
            $timestamp = strtotime($value);

            if ($timestamp !== false) {
                $dateOnly = date('Y-m-d', $timestamp);

                if ($dateOnly <= date('Y-m-d')) {
                    return $dateOnly;
                }
            }
        }

        return date('Y-m-d');
    }

    /* Normalisasi URL API : contoh statis (mis. .../rate/USD/IDR) diubah
       menjadi template .../rate/{FROM}/{TO} agar bisa dipakai semua currency. */
    private function _normalizeExchangeApiUrl($apiUrl)
    {
        $apiUrl = trim((string) $apiUrl);
        if ($apiUrl === '') {
            return '';
        }

        $apiUrl = preg_replace('#([?&]from=)[A-Za-z]{3}#i', '${1}{FROM}', $apiUrl, 1);
        $apiUrl = preg_replace('#([?&]to=)[A-Za-z]{3}#i', '${1}{TO}', $apiUrl, 1);

        if (strpos($apiUrl, '{FROM}') === false
            && preg_match('#/rate/[A-Za-z]{3}/[A-Za-z]{3}#', $apiUrl)) {
            $apiUrl = preg_replace('#/rate/[A-Za-z]{3}/[A-Za-z]{3}#', '/rate/{FROM}/{TO}', $apiUrl, 1);
        }

        return $apiUrl;
    }

    /* Ambil exchange rate dari API sesuai kode currency + setting. */
    private function _fetchExchangeRateFromApi($currcode, array $setting)
    {
        $from = strtoupper(trim((string) $currcode));
        $to = strtoupper(trim((string) $setting['base_currency']));

        if (isset($setting['code_alias'][$from]) && trim((string) $setting['code_alias'][$from]) !== '') {
            $from = strtoupper(trim((string) $setting['code_alias'][$from]));
        }

        $url = str_replace(['{FROM}', '{TO}'], [$from, $to], (string) $setting['api_url']);

        $response = $this->_httpGetJson($url);
        if (!$response['ok']) {
            return [
                'ok'    => false,
                'rate'  => null,
                'from'  => $from,
                'to'    => $to,
                'url'   => $url,
                'date'  => '',
                'error' => $response['error'],
            ];
        }

        $rate = $this->_extractRateFromJson($response['json'], $to);

        return [
            'ok'    => $rate !== null,
            'rate'  => $rate,
            'from'  => $from,
            'to'    => $to,
            'url'   => $url,
            'date'  => isset($response['json']['date']) ? (string) $response['json']['date'] : '',
            'error' => $rate === null ? 'Rate tidak ditemukan pada response API.' : '',
        ];
    }

    /* Mendukung format response v2 (rate) dan v1 (rates.{TO}). */
    private function _extractRateFromJson($json, $to)
    {
        if (!is_array($json)) {
            return null;
        }

        if (isset($json['rate']) && is_numeric($json['rate'])) {
            return (float) $json['rate'];
        }

        if (isset($json['rates']) && is_array($json['rates'])) {
            if (isset($json['rates'][$to]) && is_numeric($json['rates'][$to])) {
                return (float) $json['rates'][$to];
            }
            foreach ($json['rates'] as $value) {
                if (is_numeric($value)) {
                    return (float) $value;
                }
            }
        }

        return null;
    }


    private function _httpGetJson($url)
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'error' => 'Extension cURL tidak tersedia pada server.', 'http' => 0, 'json' => null];
        }

        $request = function ($verifyPeer) use ($url) {
            $ch = curl_init($url);
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT      => 'JAccounting-ExchangeRate/1.0',
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ];

            if ($verifyPeer) {
                $caBundle = $this->_resolveCaBundle();
                if ($caBundle !== null) {
                    $options[CURLOPT_CAINFO] = $caBundle;
                }
            } else {
                $options[CURLOPT_SSL_VERIFYPEER] = false;
                $options[CURLOPT_SSL_VERIFYHOST] = 0;
            }

            curl_setopt_array($ch, $options);

            $body = curl_exec($ch);
            $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            return ['body' => $body, 'http' => $http, 'error' => $error];
        };

        $result = $request(true);

        /* Fallback : CA bundle lokal belum terkonfigurasi pada server. */
        if ($result['body'] === false && stripos($result['error'], 'ssl') !== false) {
            $result = $request(false);
        }

        if ($result['body'] === false) {
            return [
                'ok'    => false,
                'error' => $result['error'] !== '' ? $result['error'] : 'Gagal mengakses API.',
                'http'  => $result['http'],
                'json'  => null,
            ];
        }

        if ($result['http'] < 200 || $result['http'] >= 300) {
            return [
                'ok'    => false,
                'error' => 'API mengembalikan HTTP ' . $result['http'] . '.',
                'http'  => $result['http'],
                'json'  => json_decode($result['body'], true),
            ];
        }

        $json = json_decode($result['body'], true);
        if (!is_array($json)) {
            return [
                'ok'    => false,
                'error' => 'Response API bukan JSON yang valid.',
                'http'  => $result['http'],
                'json'  => null,
            ];
        }

        return ['ok' => true, 'error' => '', 'http' => $result['http'], 'json' => $json];
    }

    private function _resolveCaBundle()
    {
        $candidates = [
            ini_get('curl.cainfo'),
            ini_get('openssl.cafile'),
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/usr/local/share/ca-certificates/cacert.pem',
            'C:\\xampp\\apache\\bin\\curl-ca-bundle.crt',
            'D:\\XAMPP85\\apache\\bin\\curl-ca-bundle.crt',
        ];

        foreach ($candidates as $path) {
            if (!empty($path) && is_file($path)) {
                return $path;
            }
        }

        return null;
    }


    //END CURRENCY

}