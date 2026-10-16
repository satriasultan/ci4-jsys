<?php
/* Item.php */


namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Item extends BaseController
{
    public function index()
    {
        $data['title']="MASTER DATA ITEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.10'; $versirelease='I.M.B.10/01'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.10'";
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
        $kmenu = 'I.M.B.10';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $this->m_item->q_autoinsert_unit();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_list',$data);
    }

    function list_mitem(){
        $kmenu = 'I.M.B.10';
        $nama=trim($this->session->get('nama'));
        $list = $this->m_customer->get_t_customer_view();
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canInput = isset($datadtl['dtl_akses']['a_input']) && trim($datadtl['dtl_akses']['a_input']) === 't';


        $list = $this->m_item->get_t_item_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if (($canView || $canUpdate || $canDelete)) {
                $btnActions = '<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle text-white"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-gear"></i>
                    </button>
                    <div class="dropdown-menu">';
            }

            if ($canUpdate) {
                $btnActions .= '<a class="dropdown-item" href="' . base_url('master/item/edit/' . trim($lm->id)) . '"><i class="fa fa-gear"></i> Update</a>';
            }

            if ($canView) {
                $btnActions .= '<a class="dropdown-item" href="' . base_url('master/item/detail/' . trim($lm->id)) . '"><i class="fa fa-eye"></i> Detail</a>';
            }

            if ($canDelete) {
                $btnActions .= '<a class="dropdown-item text-danger" href="#" onclick="deleteItem(\'' . trim($lm->id) . '\',\'' . trim($lm->idbarang) . '\');return false;"><i class="fa fa-trash"></i> Delete</a>';
            }


            $btnActions .= '</div></div>';
            $row[]=$btnActions;

            // $row[] = '<div class="btn-group">
            //                 <button type="button" class="btn btn-info  dropdown-toggle text-white"
            //                     data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-gear"></i>
            //                 </button>
            //                 <div class="dropdown-menu">
            //                     <a class="dropdown-item" href="#" onclick="editItem('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
            //                 </div>
            //             </div>';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->originalno;
            $row[] = $lm->unit;
            $row[] = $lm->nmgolonganbarang;
            $row[] = $lm->nmjenisproduk;
            $row[] = $lm->nmkelompokbarang;
            $row[] = $lm->nmprincipal;
            $row[] = $lm->nmlocation;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            // $row[] = $lm->inputby;
            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_item_view_count_all(),
            "recordsFiltered" => $this->m_item->t_item_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input(){
        $data['title']="INPUT DATA MASTER ITEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.10'; $versirelease='I.M.B.10/01'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.10'";
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
        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_input',$data);
    }

    // =========================================================
    // EDIT — perbaikan: ambil parameter dari segment URI
    // Route: master/item/edit/(:any)
    // =========================================================
    public function edit($id = null)
    {
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.10';
        $versirelease = 'I.M.B.10/01';
        $releasedate = date('2020-04-12 00:00:00');

        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;

        $data['message'] = $this->buildMessage($nama);
        $data['title'] = "EDIT DATA MASTER ITEM";
        $data['type'] = "EDIT";
        $data['nama'] = $nama;
        // FIX: ambil dari segment URI, fallback ke GET
        $data['id'] = $id ?? trim($this->request->getGet('var'));

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_input', $data);
    }

    // =========================================================
    // DETAIL — perbaikan sama
    // =========================================================
    public function detail($id = null)
    {
        $dtlbranch = $this->m_global->q_branch()->getRowArray();
        $branch = $dtlbranch['branch'];

        $nama = trim($this->session->get('nama'));
        $kodemenu = 'I.M.B.10';
        $versirelease = 'I.M.B.10/01';
        $releasedate = date('2020-04-12 00:00:00');

        $versidb = $this->fiky_version->version($kodemenu, $versirelease, $releasedate, $nama);
        $x = $this->fiky_menu->menus($kodemenu, $versirelease, $releasedate);
        $data['x'] = $x['rows'];
        $data['y'] = $x['res'];
        $data['t'] = $x['xn'];
        $data['kodemenu'] = $kodemenu;
        $data['version'] = $versidb;

        $data['message'] = $this->buildMessage($nama);
        $data['title'] = "DETAIL DATA MASTER ITEM";
        $data['type'] = "DETAIL";
        $data['nama'] = $nama;
        $data['id'] = $id ?? trim($this->request->getGet('var'));

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_input', $data);
    }

    // =========================================================
    // Helper: bangun pesan error/sukses (dipakai berulang)
    // =========================================================
    private function buildMessage($nama)
    {
        $paramerror = " and userid='$nama' and modul='I.M.B.10'";
        $dtlerror = $this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err = $this->m_trxerror->q_trxerror($paramerror)->getNumRows();

        $errordesc   = isset($dtlerror['description'])  ? trim($dtlerror['description'])  : '';
        $nomorakhir1 = isset($dtlerror['nomorakhir1'])  ? trim($dtlerror['nomorakhir1'])  : '';
        $errorcode   = isset($dtlerror['errorcode'])    ? trim($dtlerror['errorcode'])    : '';

        if ($count_err > 0 && $errordesc <> '') {
            return ($dtlerror['errorcode'] == 0)
                ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
                : "<div class='alert alert-info'>$errordesc</div>";
        }
        return ($errorcode == '0')
            ? "<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>"
            : "";
    }

    // =========================================================
    // DELETE — perbaikan: cek dependensi + konfirmasi
    // =========================================================
    public function del_item()
    {
        $nama = trim($this->session->get('nama'));
        // FIX: terima dari GET maupun POST
        $id = trim($this->request->getGet('var') ?? $this->request->getPost('var'));

        if ($id === '') {
            return redirect()->to(base_url('master/item'))
                ->with('message', 'ID Barang tidak ditemukan.');
        }

        // Ambil idbarang asli dari tabel master
        $row = $this->db->table('sc_mst.mbarang')
            ->select('idbarang')
            ->where('id', $id)
            ->get()->getRowArray();

        if (!$row) {
            return redirect()->to(base_url('master/item'))
                ->with('message', 'Data tidak ditemukan.');
        }
        $idbarang = trim($row['idbarang']);

        // =====================================================
        // BLOCKING: cek apakah barang sudah dipakai transaksi
        // =====================================================
        $dipakai = $this->db->query("
            SELECT 1 FROM sc_mst.stkgdw WHERE TRIM(idbarang) = ? LIMIT 1
        ", [$idbarang])->getNumRows();

        if ($dipakai > 0) {
            return redirect()->to(base_url('master/item'))
                ->with('message', "Barang [$idbarang] tidak bisa dihapus karena sudah ada di kartu stock.");
        }

        $this->db->transStart();

        // Hapus unit terkait dulu (foreign key)
        $this->db->table('sc_mst.mbarang_unit')
            ->where('idbarang', $idbarang)
            ->delete();

        // Hapus master
        $this->db->table('sc_mst.mbarang')
            ->where('id', $id)
            ->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to(base_url('master/item'))
                ->with('message', 'Gagal menghapus data.');
        }

        // Insert trx error
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.M.B.10');
        $builder_trxerror->delete();
        $builder_trxerror->insert([
            'userid'      => $nama,
            'errorcode'   => 0,
            'nomorakhir1' => $idbarang,
            'nomorakhir2' => '',
            'modul'       => 'I.M.B.10',
        ]);

        return redirect()->to(base_url('master/item'));
    }

    // =========================================================
    // SAVE DATA ITEM — perbaikan: BLOCKING SIMPAN
    // =========================================================
    public function saveDataItem()
    {
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $id   = trim($this->request->getPost('id'));

        // =====================================================
        // Ambil semua input
        // =====================================================
        $idbarang    = strtoupper(trim($this->request->getPost('idbarang')));
        $nmbarang    = strtoupper(trim($this->request->getPost('nmbarang')));
        $idgroup     = trim($this->request->getPost('idgroup'));
        $subunitenable = trim($this->request->getPost('subunitenable'));
        // Ambil dari field utama; kalau kosong (karena disabled), ambil dari hidden backup
        $unit    = trim($this->request->getPost('unit')    ?? '');
        $subunit = trim($this->request->getPost('subunit') ?? '');

        if ($unit === '') {
            $unit = trim($this->request->getPost('unit_hidden') ?? '');
        }
        if ($subunit === '') {
            $subunit = trim($this->request->getPost('subunit_hidden') ?? '');
        }
        $idbarcode   = trim($this->request->getPost('idbarcode'));
        $maks_daystock = str_replace('.', '', trim($this->request->getPost('maks_daystock')));
        $deflocation = trim($this->request->getPost('deflocation'));
        $defarea     = trim($this->request->getPost('defarea'));
        $chold       = trim($this->request->getPost('chold'));

        $kdtax       = trim($this->request->getPost('kdtax'));
        $originalno  = trim($this->request->getPost('originalno'));
        $satuantax   = trim($this->request->getPost('satuantax'));

        $volume  = trim($this->request->getPost('volume'));
        $berat   = trim($this->request->getPost('berat'));
        $gw      = trim($this->request->getPost('gw'));
        $psize   = trim($this->request->getPost('psize'));
        $lsize   = trim($this->request->getPost('lsize'));
        $tsize   = trim($this->request->getPost('tsize'));
        $lokasireff = trim($this->request->getPost('lokasireff'));

        $idgolonganbarang  = trim($this->request->getPost('idgolonganbarang'));
        $idjenisproduk     = trim($this->request->getPost('idjenisproduk'));
        $idkelompokbarang  = trim($this->request->getPost('idkelompokbarang'));
        $idprincipal       = trim($this->request->getPost('idprincipal'));

        $ppersediaan = trim($this->request->getPost('ppersediaan') ?? '');
        $psj         = trim($this->request->getPost('psj') ?? '');
        $salesakun   = trim($this->request->getPost('salesakun') ?? '');
        $pcogs       = trim($this->request->getPost('pcogs') ?? '');
        $phpproduksi = trim($this->request->getPost('phpproduksi') ?? '');
        $pjasa       = trim($this->request->getPost('pjasa') ?? '');
        $pwaste      = trim($this->request->getPost('pwaste') ?? '');
        $grouptype   = trim($this->request->getPost('grouptype') ?? 'STOCK');

        $discontinue = strtoupper(trim($this->request->getPost('discontinue') ?? 'NO'));
        $issn        = strtoupper(trim($this->request->getPost('issn') ?? 'NO'));

        $setminstock = trim($this->request->getPost('setminstock'));
        $minstock    = str_replace('.', '', trim($this->request->getPost('minstock')));
        $description = strtoupper(trim($this->request->getPost('description')));
        $expdate = !$this->request->getPost('expdate') ? null : date('Y-m-d', strtotime($this->request->getPost('expdate')));
        $mfgdate = !$this->request->getPost('mfgdate') ? null : date('Y-m-d', strtotime($this->request->getPost('mfgdate')));

        $inputby   = $nama;
        $inputdate = date('Y-m-d H:i:s');

        // =====================================================
        // 🔒 BLOCKING SIMPAN — VALIDASI WAJIB
        // =====================================================
        $errors = [];

        if ($idbarang === '') {
            $errors[] = 'Item ID wajib diisi.';
        }
        if ($nmbarang === '') {
            $errors[] = 'Item Name wajib diisi.';
        }
        if ($unit === '') {
            $errors[] = 'Unit wajib dipilih.';
        }

        // Cek duplikat untuk INPUT
        if ($type === 'INPUT' && $idbarang !== '') {
            $cek = $this->db->table('sc_mst.mbarang')
                ->where('TRIM(idbarang)', $idbarang)
                ->countAllResults();

            // PostgreSQL pakai TRIM, jadi gunakan query manual
            $cek = $this->db->query(
                "SELECT 1 FROM sc_mst.mbarang WHERE TRIM(idbarang) = ? LIMIT 1",
                [$idbarang]
            )->getNumRows();

            if ($cek > 0) {
                $errors[] = "Item ID [$idbarang] sudah terdaftar.";
            }
        }

        if (!empty($errors)) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => implode(' ', $errors),
            ]);
        }

        // =====================================================
        // PROSES SIMPAN
        // =====================================================
        $builder_mbarang  = $this->db->table('sc_mst.mbarang');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $payload = [
            'nmbarang'         => $nmbarang,
            'idgroup'          => $idgroup,
            'subunitenable'    => $subunitenable,
            'unit'             => $unit,
            'subunit'          => $subunit,
            'idbarcode'        => $idbarcode,
            'maks_daystock'    => $maks_daystock,
            'setminstock'      => $setminstock,
            'minstock'         => $minstock,
            'chold'            => $chold,
            'description'      => $description,
            'expdate'          => $expdate,
            'mfgdate'          => $mfgdate,
            'deflocation'      => $deflocation,
            'defarea'          => $defarea,
            'kdtax'            => $kdtax,
            'originalno'       => $originalno,
            'satuantax'        => $satuantax,
            'volume'           => $volume,
            'berat'            => $berat,
            'gw'               => $gw,
            'psize'            => $psize,
            'lsize'            => $lsize,
            'tsize'            => $tsize,
            'lokasireff'       => $lokasireff,
            'idgolonganbarang' => $idgolonganbarang,
            'idjenisproduk'    => $idjenisproduk,
            'idkelompokbarang' => $idkelompokbarang,
            'idprincipal'      => $idprincipal,
            'ppersediaan'      => $ppersediaan,
            'psj'              => $psj,
            'salesakun'        => $salesakun,
            'pcogs'            => $pcogs,
            'phpproduksi'      => $phpproduksi,
            'pjasa'            => $pjasa,
            'pwaste'           => $pwaste,
            'discontinue'      => $discontinue,
            'issn'             => $issn,
            'inputby'          => $inputby,
            'inputdate'        => $inputdate,
            'grouptype'        => $grouptype,
        ];

        $this->db->transStart();

        if ($type === 'INPUT') {
            $payload['idbarang'] = $idbarang;
            $sukses = $builder_mbarang->insert($payload);
            $modul  = 'I.M.B.10';
        } else {
            // EDIT: idbarang tidak boleh diubah (primary key)
            $builder_mbarang->where('id', $id);
            $sukses = $builder_mbarang->update($payload);
            $modul  = 'I.M.B.10';
        }

        // Insert trx error (untuk info sukses)
// Insert trx error (untuk info sukses)
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', $modul);
        $builder_trxerror->delete();
        $builder_trxerror->insert([
            'userid'      => $nama,
            'errorcode'   => $sukses ? 0 : 1,
            'nomorakhir1' => $idbarang,
            'nomorakhir2' => '',
            'modul'       => $modul,
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false || !$sukses) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Gagal menyimpan data. Silakan coba lagi.',
            ]);
        }

        return $this->response->setJSON([
            'status'   => true,
            'messages' => 'Sukses Di Proses Nomor: ' . $idbarang,
        ]);
    }

    function import(){
        $data['title']="Import Data Item ";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.A.2'; $versirelease='I.D.A.2/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc   $nomorakhir1</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.D.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_item->q_item_tmp_param($parameter)->getResult();
        $data['adaisi']=$this->m_item->q_item_tmp_param($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('master/item/v_import',$data);

    }

    function proses_upload() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $this->db->query("delete from sc_tmp.mbarang where inputby='$nama'");


        $path = "./assets/files/item/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        if ($this->request->getPost('save')) {
            $file = $this->request->getFile('import');
            $fileName = $file->getName();
            if (file_exists($path.$fileName)){
                unlink($path.$fileName);
            }
            $validateFile = $this->validate([
                'import' => [
                    'uploaded[import]',
                    'ext_in[import,xls,xlsx,excel]',
                    'max_size[import,10096]',
                ]
            ]);
            $file->move($path, $fileName);
            $inputFileName = $path.trim($fileName);
            if (!$validateFile){
                echo 'INVALID FAILED FILE';
                echo '<a href="'.base_url('/master/item/import').'">BACK</a>';
            } else {
                //  Read your Excel workbook
                try {
                    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                    $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                } catch(Exception $e) {
                    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }

                //  Get worksheet dimensions
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                //  Loop through each row of the worksheet in turn
                $JUDUL = $sheet->rangeToArray('A' . '1' . ':' . $highestColumn . '1',
                    NULL,
                    TRUE,
                    FALSE);
                //$JUDUL[0][0].'</br>';
                $no=1;
                for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                        NULL,
                        TRUE,
                        FALSE);

                    //  Insert row data array into your database of choice here
                    $data = array(
                        //'idlocation' => $loccode,
                        'idbarang' => trim($rowData[0][0]),
                        'nmbarang' => trim($rowData[0][1]),
                        'idgroup' => trim($rowData[0][2]),
                        'unit' => trim($rowData[0][3]),
                        'subunit' => 0,
                        'subunitenable' => 'NO',
                        'sku' => trim($rowData[0][4]),
                        'idbarcode' => trim($rowData[0][4]),
                        'maks_daystock' => trim($rowData[0][5]),
                        'deflocation' => trim($rowData[0][6]),
                        'defarea' => trim($rowData[0][7]),
                        'description' => trim($rowData[0][8]),
                        'chold' => trim($rowData[0][9]),
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                        'setminstock' => trim($rowData[0][10]),
                        'minstock' => trim($rowData[0][11]),
                    );

                    //$pm =  " and trim(idbarang)='".trim($rowData[0][0])."'";
                    //$cek = $this->m_item->q_item_param($pm)->getNumRows();
                    //if($cek <= 0 and trim($rowData[0][0]) !== '') {
                        $builder = $this->db->table("sc_tmp.mbarang");
                        $builder->insert($data);
                    //}
                    $no++;
                }
                return redirect()->to(base_url('/master/item/import'));
            }
        }
    }

    function clear_tmp(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.mbarang where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.A.2');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.A.2',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/master/item/import'));
    }

    function final_data(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.mbarang set status='F' where inputby='$nama'");

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.A.2');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.A.2',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/master/item/import'));
    }


    public function unit()
    {
        $data['title']="UNIT ITEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.3'; $versirelease='I.D.A.3/01'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.3'";
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
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_list_unit',$data);
    }

    function list_unit(){
        $list = $this->m_item->get_t_unit_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="update_unit('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_unit('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';
            $row[] = $lm->idunit;
            $row[] = $lm->parentunit;
            $row[] = $lm->ctype;
            $row[] = $lm->conversion_value;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_unit_view_count_all(),
            "recordsFiltered" => $this->m_item->t_unit_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    // =========================================================
    // SHOW DETAIL ITEM — untuk mengisi form EDIT / DETAIL
    // Route: master/item/showDetailItem/(:any)
    // =========================================================
    public function showDetailItem($id = null)
    {
        $id = $id ?? trim($this->request->getGet('var'));

        if ($id === '' || $id === null) {
            return $this->response->setJSON([
                'dataTables' => [
                    'status' => false,
                    'total_count' => 0,
                    'items' => [],
                    'incomplete_getResults' => false,
                ]
            ]);
        }

        // =================== HEADER ITEM ===================
        $param = " and id='" . $this->db->escapeString($id) . "'";
        $dtl = $this->m_item->q_item_param($param);
        $items = $dtl->getResult();

        // Trim semua string (buang trailing space char(12))
        foreach ($items as $it) {
            foreach ($it as $k => $v) {
                if (is_string($v)) {
                    $it->$k = trim($v);
                }
            }
        }

        // =================== UNITS ===================
        $units = [];
        if (!empty($items)) {
            $idbarang = trim($items[0]->idbarang);
            $units = $this->db->table('sc_mst.mbarang_unit')
                ->select('idbarang, idunit, idunit_tax, basic_value, conv_value, cdefault')
                ->where('idbarang', $idbarang)
                ->orderBy('idunit', 'ASC')
                ->get()->getResultArray();

            // Trim setiap field
            foreach ($units as &$u) {
                $u['idbarang']   = trim($u['idbarang']);
                $u['idunit']     = trim($u['idunit']);
                $u['idunit_tax'] = trim($u['idunit_tax'] ?? '');
                $u['cdefault']   = trim($u['cdefault'] ?? 'NO');
            }
            unset($u);
        }

        // =================== RESPONSE ===================
        return $this->response->setJSON([
            'dataTables' => [
                'status' => true,
                'total_count' => count($items),
                'items' => $items,
                'incomplete_getResults' => false,
            ],
            'units' => $units,
            'status' => true,
        ]);
    }

    function saveUnit(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $ctype = strtoupper(trim($dataprocess->ctype));
            $idunit = strtoupper(trim($dataprocess->idunit));
            $parentunit = strtoupper(trim($dataprocess->parentunit));
            $conversion_value = strtoupper(trim($dataprocess->conversion_value));
            $description = strtoupper(trim($dataprocess->description));
            $chold = trim($dataprocess->chold);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.unit');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idunit='$idunit' and parentunit='$parentunit'";
                $check = $this->m_item->q_unit_param($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idunit' => $idunit,
                        'parentunit' => empty($parentunit) ? 0 : $parentunit ,
                        'ctype' => $ctype,
                        'conversion_value' => $conversion_value,
                        'description' => $description,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idunit);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idunit='$idunit' and parentunit='$parentunit'";
                $check = $this->m_item->q_unit_param($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(

                        'conversion_value' => $conversion_value,
                        'description' => $description,
                        'chold' => $chold,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idunit);
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

    function showing_data_unit($id){
        $data = $this->m_item->get_t_unit_view_by_id($id);
        echo json_encode($data);
    }

    function print_labels()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.4'; $versirelease='I.D.A.4/01'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.4'";
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
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_print_labels',$data);
    }

    function list_item_spec(){
        $branch = trim($this->session->get('branch'));
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idunit='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idunit='$varGet'";
        } else {
            $paramglobal2= "";
        }

        //$parameterx = " and coalesce(chold,'NO')!='YES'";
        $parameterx = "";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idbarang like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) or (nmbarang like '%$search%' $paramglobal1 $paramglobal2 $parameterx )  or (idspec like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) order by nmbarang asc";
        $getResult = $this->m_item->q_label_barang($param)->getResult();
        $count = $this->m_item->q_label_barang($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function show_showlabels_item(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);

        $title = " Barcode Label Data Item";

        $datajson =  base_url("master/item/api_show_showlabels_item/?enc_idlocation=$enc_idlocation") ;
        //$datamrt =  base_url("assets/mrt/showlabels_item.mrt") ;
        $datamrt =  base_url("assets/mrt/showlabels_item_new_zebra.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_showlabels_item(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();
        $param="";
        $datamst = $this->m_item->q_mitem($param);

        header("Content-Type: text/json");
        return json_encode(
            array(
                'info' => array([
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
            ), JSON_PRETTY_PRINT);
    }


/* METODE POST */
    function show_showlabels_item_post(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $idbarang = $this->request->getPost('idbarang');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();


        if (!empty($idbarang)) {
            $i=0;
            foreach ($idbarang as $dd) {
                if ($i == 0){
                    $idbarangx.=" idbarang='$dd'";
                } else { $idbarangx.=" or idbarang='$dd'"; }
                $i++;
            }
            $idbarangxp=" and (".$idbarangx.")";
        } else {  $idbarangxp=""; }

        $param = "".$idbarangxp;
        $enc_param = $this->fiky_encryption->sealed($param);
        $title = " Barcode Label Data Item";

        $datajson =  base_url("master/item/api_show_showlabels_item_post/?enc_param=$enc_param") ;
        $datamrt =  base_url("assets/mrt/showlabels_item_new_zebra.mrt") ;
        //$datamrt =  base_url("assets/mrt/showlabels_item.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_showlabels_item_post(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $param=trim($this->fiky_encryption->unseal($this->request->getGet('enc_param')));
        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_item->q_label_barang($param);

        header("Content-Type: text/json");
        return $datajson =  json_encode(
            array(
                'info' => array([
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
            ), JSON_PRETTY_PRINT);
    }
    /* PENGEMBANGAN */
    function printFromListPrinter(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $idbarang = $this->request->getPost('idbarang');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();

        $idbarangxp=" and trim(idbarang)||trim(batch) in (select trim(idfromx) from sc_tmp.print_label_item_temporary 
        where docno='$nama')";

        $param = " ".$idbarangxp;
        $enc_param = $this->fiky_encryption->sealed($param);
        $title = " Barcode Label Data Item";

        $datajson =  base_url("master/item/api_show_showlabels_item_post/?enc_param=$enc_param") ;
        $datamrt =  base_url("assets/mrt/showlabels_item_new_zebra.mrt") ;
        //$datamrt =  base_url("assets/mrt/showlabels_item.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function printFromListPrinterRackMode(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $idbarang = $this->request->getPost('idbarang');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();

        $idbarangxp=" and trim(idbarang)||'$-$'||trim(batch) in (select trim(idfromx) from sc_tmp.print_label_item_temporary 
        where docno='$nama')";

        $param = " ".$idbarangxp;
        $enc_param = $this->fiky_encryption->sealed($param);
        $title = " Barcode Label Data Item";

        $datajson =  base_url("master/item/api_show_showlabels_item_post/?enc_param=$enc_param") ;
        $datamrt =  base_url("assets/mrt/showlabels_item_backup_rack.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }
    /* PENGEMBANGAN */






    function list_label_item_temporary(){
        $list = $this->m_item->get_query_label_item_temporary();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
/*            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="update_unit('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_unit('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';*/
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->unit;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_item_temporary_count_all(),
            "recordsFiltered" => $this->m_item->t_item_temporary_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function insertOnchangeTmp(){
        $idfromx = trim($this->request->getGet('var'));
        $nama = trim($this->session->get('nama'));
        $isx = explode("$-$", $idfromx);
        //$idbarangx = trim($isx[0]);
        //$batch = trim($isx[1]);

        $param = " and idspec='$idfromx'";
        $dtl = $this->m_item->q_label_barang($param)->getRowArray();
        $idbarang = $dtl['idbarang'];
        $nmbarang = $dtl['nmbarang'];
        $batch = $dtl['batch'];
        $unit = $dtl['unit'];

        $info = array(
            'docno' => $nama,
            'idbarang' => $idbarang,
            'nmbarang' => $nmbarang,
            'batch' => $batch,
            'unit' => $unit,
            'idfromx' => $idfromx,

        );

        $builder = $this->db->table('sc_tmp.print_label_item_temporary');
        if ($builder->insert($info)) {
            $result = array('status' => true, 'messages' => 'Insert Successfully');
            echo json_encode($result);
            //return redirect()->to(base_url('trans/tms/input_annualy'));
        }
    }

    function clearTemporaryPrinter(){
        $idfromx = trim($this->request->getGet('var'));
        $nama = trim($this->session->get('nama'));
        $builder = $this->db->table('sc_tmp.print_label_item_temporary');

        $builder->where('docno' , $nama);
        if ($builder->delete()) {
            $result = array('status' => true, 'messages' => 'Clear Successfully');
            echo json_encode($result);
            return redirect()->to(base_url('master/item/print_labels'));
        }
    }

    /* ITEM GROUP */
    public function itemgroup()
    {
        $data['title']="Item Group ";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.A.3'; $versirelease='I.D.A.3/01'; $releasedate=date('2022-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']="";
        $message=$this->session->getFlashdata('message');
        if (!empty($message)) {
            $data['message']="<div class='alert alert-info'> $message </div>";
        } else {
            $data['message']='';
        }
        return $this->template->render('master/item/v_list_item_group',$data);
    }

    function list_mgroup(){
        $list = $this->m_item->get_t_mgroup_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Update" onclick="update_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Delete" onclick="delete_mgroup('."'".trim($lm->id)."'".')"><i class="fa fa-trash"></i> </a>';
            $row[] = $lm->idgroup;
            $row[] = $lm->nmgroup;
            $row[] = $lm->description;
            $row[] = $lm->chold;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_mgroup_view_count_all(),
            "recordsFiltered" => $this->m_item->t_mgroup_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function saveMgroup(){
        $nama = trim($this->session->get('nama'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $idgroup = strtoupper(trim($dataprocess->idgroup));
            $nmgroup = strtoupper(trim($dataprocess->nmgroup));
            $grouptype = strtoupper(trim($dataprocess->grouptype));
            $chold = strtoupper(trim($dataprocess->chold));
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_mst.mgroup');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $param = " and idgroup='$idgroup'";
                $check = $this->m_item->q_mgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $getResult = array('status' => false, 'messages' => 'Oops the code is already in use');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        'idgroup' => $idgroup,
                        'nmgroup' => $nmgroup,
                        'grouptype' => $grouptype,
                        'chold' => $chold,
                        'description' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $param = " and idgroup='$idgroup'";
                $check = $this->m_item->q_mgroup($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                $info = array(
                        'nmgroup' => $nmgroup,
                        'grouptype' => $grouptype,
                        'chold' => $chold,
                        'description' => $description,
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                    );
                    $builder->where('id',$id);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idgroup);
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

                $builder = $this->db->table('sc_mst.mgroup');
                $builder->where('id' , $id);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$idgroup);
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

    function showing_data_mgroup($id){
        $data = $this->m_item->get_t_mgroup_view_by_id($id);
        echo json_encode($data);
    }
    /* ITEM GROUP */

    public function batch()
    {

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        //$loccode=trim($this->session->get('loccode'));
        $loccode=trim($this->request->getGet('idlocation'));
        $kodemenu='I.D.A.5'; $versirelease='I.D.A.5/01'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.A.5'";
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
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $nmlocation = $dtllocation['nmlocation'] ?? '';
        $data['dtllocation']= $dtllocation;
        $data['title']=ucfirst("CARI STOCK DI : <b>".$nmlocation)."</b>";
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/item/v_list_batch',$data);
    }

    function list_batch(){
        $list = $this->m_item->get_t_balance_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $lm->id;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmgroup;


            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_item->t_balance_view_count_all(),
            "recordsFiltered" => $this->m_item->t_balance_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function delete_batch_dtl()
    {
        //$loccode = $this->request->getPost('idlocation_get_table');
        $id[] = $this->request->getPost('id');

        $builder = $this->db->table('sc_mst.stkgdw');
        $builder->whereIn('id', $id[0]);
        $builder->delete();
        echo json_encode(array(
            'id' => $id,
            'messages' => 'Data Sukses Di Hapus ID: ',
            'status' => true,
        ),JSON_PRETTY_PRINT);
    }



    public function saveBarangUnit()
    {
        // ==========================================
        // SESSION USER
        // ==========================================

        $nama = trim(
            (string) $this->session->get('nama')
        );


        // ==========================================
        // POST DATA
        // ==========================================

        $idbarang = strtoupper(
            trim(
                (string) $this->request->getPost('idbarang')
            )
        );

        $idunit = strtoupper(
            trim(
                (string) $this->request->getPost('idunit')
            )
        );
        $idunit_tax = strtoupper(
            trim(
                (string) $this->request->getPost('idunit_tax')
            )
        );
        $basic_value = trim(
            (string) $this->request->getPost('basic_value')
        );

        $conv_value = trim(
            (string) $this->request->getPost('conv_value')
        );

        $cdefault = strtoupper(
            trim(
                (string) $this->request->getPost('cdefault')
            )
        );


        // ==========================================
        // VALIDASI
        // ==========================================

        if ($idbarang === '') {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Barang tidak ditemukan.'
            ]);
        }


        if ($idunit === '') {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit wajib dipilih.'
            ]);
        }


        // ==========================================
        // DEFAULT VALUE
        // ==========================================

        if ($basic_value === '') {
            $basic_value = '1.00';
        }

        if ($conv_value === '') {
            $conv_value = '1.00';
        }

        if ($cdefault === '') {
            $cdefault = 'NO';
        }


        // ==========================================
        // FORMAT NUMBER
        // 1,000.00 -> 1000.00
        // ==========================================

        $basic_value = str_replace(',', '', $basic_value);
        $conv_value  = str_replace(',', '', $conv_value);


        // ==========================================
        // VALIDASI ANGKA
        // ==========================================

        if (
            !is_numeric($basic_value) ||
            !is_numeric($conv_value)
        ) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Basic Value dan Conversion Value harus berupa angka.'
            ]);
        }


        // ==========================================
        // VALIDASI DEFAULT
        // ==========================================

        if (!in_array($cdefault, ['YES', 'NO'], true)) {

            $cdefault = 'NO';
        }


        // ==========================================
        // BUILDER
        // ==========================================

        $builder = $this->db->table(
            'sc_mst.mbarang_unit'
        );


        // ==========================================
        // CEK DUPLIKASI
        // PRIMARY KEY:
        // idbarang + idunit
        // ==========================================

        $exists = $builder
            ->where('idbarang', $idbarang)
            ->where('idunit', $idunit)
            ->countAllResults();


        if ($exists > 0) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Satuan sudah terdaftar untuk barang ini.'
            ]);
        }


        // ==========================================
        // TRANSACTION
        // ==========================================

        $this->db->transStart();


        // ==========================================
        // JIKA DEFAULT = YES
        // RESET DEFAULT UNIT LAIN
        // ==========================================

        if ($cdefault === 'YES') {

            $this->db
                ->table('sc_mst.mbarang_unit')
                ->where('idbarang', $idbarang)
                ->update([
                    'cdefault' => 'NO'
                ]);
        }


        // ==========================================
        // INSERT SATUAN
        // ==========================================

        $builder->insert([

            'idbarang'    => $idbarang,

            'idunit'      => $idunit,

            'idunit_tax'  => $idunit_tax,

            'basic_value' => $basic_value,

            'conv_value'  => $conv_value,

            'inputdate'   => date('Y-m-d H:i:s'),

            'inputby'     => $nama,

            'cdefault'    => $cdefault,

            'chold'       => 'NO'

        ]);


        // ==========================================
        // COMPLETE TRANSACTION
        // ==========================================

        $this->db->transComplete();


        if ($this->db->transStatus() === false) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan satuan barang.'
            ]);
        }


        return $this->response->setJSON([
            'success' => true,
            'message' => 'Satuan barang berhasil ditambahkan.'
        ]);
    }

    public function barangUnitList()
    {
        /* paksa untuk masuk ke barang unit list secara default */
        $this->db->query("insert into sc_mst.mbarang_unit
(idbarang,idunit,idunit_tax,basic_value,conv_value,cdefault,chold)
(select idbarang,trim(unit),'',1,1,'YES','NO' from sc_mst.mbarang where trim(idbarang)||trim(unit)
not in (select trim(idbarang)||trim(idunit) from sc_mst.mbarang_unit));");


        $idbarang = strtoupper(
            trim(
                (string) $this->request->getGet('idbarang')
            )
        );


        if ($idbarang === '') {

            return $this->response->setJSON([
                'data' => []
            ]);
        }


        $builder = $this->db
            ->table('sc_mst.mbarang_unit');


        $data = $builder
            ->select('
            idbarang,
            idunit,
            idunit_tax,
            basic_value,
            conv_value,
            cdefault
        ')
            ->where('idbarang', $idbarang)
            ->orderBy('idunit', 'ASC')
            ->get()
            ->getResultArray();


        $result = [];

        $no = 1;


        foreach ($data as $row) {

            $result[] = [

                'no' => $no++,

                'idbarang' => trim($row['idbarang']),

                'idunit' => trim($row['idunit']),

                'idunit_tax' => trim(
                    $row['idunit_tax'] ?? ''
                ),

                'basic_value' => number_format(
                    (float) ($row['basic_value'] ?? 0),
                    2,
                    '.',
                    ','
                ),

                'conv_value' => number_format(
                    (float) ($row['conv_value'] ?? 0),
                    2,
                    '.',
                    ','
                ),

                'cdefault' => trim(
                    $row['cdefault'] ?? 'NO'
                )

            ];

        }


        return $this->response->setJSON([
            'data' => $result
        ]);
    }

    public function deleteBarangUnit()
    {
        // ==========================================
        // SESSION USER
        // ==========================================

        $nama = trim(
            (string) $this->session->get('nama')
        );


        // ==========================================
        // AMBIL DATA POST
        // ==========================================

        $idbarang = trim(
            (string) $this->request->getPost('idbarang')
        );

        $idunit = strtoupper(
            trim(
                (string) $this->request->getPost('idunit')
            )
        );


        // ==========================================
        // VALIDASI
        // ==========================================

        if ($idbarang === '' || $idunit === '') {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID Barang dan Unit wajib diisi.'
            ]);
        }


        // ==========================================
        // DATABASE
        // ==========================================

        $builder = $this->db->table(
            'sc_mst.mbarang_unit'
        );


        // ==========================================
        // CEK DATA
        // ==========================================

        $exists = $builder
            ->where('idbarang', $idbarang)
            ->where('idunit', $idunit)
            ->countAllResults();


        if ($exists <= 0) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data Unit tidak ditemukan.'
            ]);
        }


        // ==========================================
        // DELETE
        // ==========================================

        $builder
            ->where('idbarang', $idbarang)
            ->where('idunit', $idunit)
            ->delete();


        // ==========================================
        // RESPONSE
        // ==========================================

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Unit berhasil dihapus.'
        ]);
    }

}
