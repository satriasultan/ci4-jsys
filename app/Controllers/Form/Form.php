<?php

namespace App\Controllers\Form;

use App\Controllers\BaseController;

class Form extends BaseController
{

    public function index()
    {
        $data['title']="LIST PERANGKAT PRIBADI";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.1'; $versirelease='I.N.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.1'";
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
        // $this->m_form->q_autoinsert_unit();
        $roleid=trim($this->session->get('roleid'));
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_list_perangkat',$data);
    }

    function list_perangkat(){
        $nama=trim($this->session->get('nama'));
        $roleid=trim($this->session->get('roleid'));
        $list = $this->m_form->get_t_perangkat_view();
        $roleid=trim($this->session->get('roleid'));
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $btnActions = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle text-white"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-gear"></i>
                </button>
                <div class="dropdown-menu">';

            // Default: Cetak selalu ada
            // if(trim($lm->nmstatus) !== 'BATAL'){

            // }

            //Default: Detail selalu ada
            $btnActions .= '<a class="dropdown-item" href="#" onclick="detailPerangkat(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-eye"></i> Detail Data</a>';


            // Jika role IT
            if ($roleid==='IT') {
                if (trim($lm->nmstatus) !== 'BATAL'){
                    $btnActions .= '<a class="dropdown-item" href="#" onclick="editPerangkat(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-edit"></i> Update</a>';
                }

                // if(trim($lm->nmstatus) !== 'BATAL' && trim($lm->nmstatus) !== 'OPEN' && trim($lm->nmstatus) !== 'CLOSE' && trim($lm->inputby) == $nama){
                $btnActions .= '<a class="dropdown-item"  
                        href="' . base_url('form/pa/print_perangkat') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                        onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                        <i class="fa fa-print"></i> Cetak</a>';
                // }

                if (trim($lm->nmstatus) !== 'BATAL' && trim($lm->nmstatus) !== 'OPEN' && trim($lm->nmstatus) !== 'CLOSE') {
                    $btnActions .= '<a class="dropdown-item" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-ban"></i> Batalkan Pengajuan</a>';
                }

                if (trim($lm->nmstatus) !== 'OPEN' && trim($lm->nmstatus) !== 'BATAL') {
                    $btnActions .= '<a class="dropdown-item bg-success" href="#" onclick="setToOpen(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-external-link-alt"></i> Set To Open</a>';
                }

                if (trim($lm->nmstatus) !== 'CLOSE' && trim($lm->nmstatus) !== 'BATAL') {
                    $btnActions .= '<a class="dropdown-item bg-danger" href="#" onclick="setToClosed(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-times-circle"></i> Set To Close</a>';
                }
            } elseif (trim($lm->status)==='I') {
                // Non-IT, status I
                $btnActions .= '<a class="dropdown-item" href="#" onclick="editPerangkat(\'' . trim($lm->docno) . '\');"
                    >
                    <i class="fa fa-edit"></i> Update</a>';

                $btnActions .= '<a class="dropdown-item" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-ban"></i> Batalkan Pengajuan</a>';

                $btnActions .= '<a class="dropdown-item"  
                    href="' . base_url('form/pa/print_perangkat') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                    onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                    <i class="fa fa-print"></i> Cetak</a>';
            }

            $btnActions .= '</div></div>';


            $row[] = $btnActions;
            $row[] = '<div class="text-bold">' . $lm->docno . '</div>';
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'CLOSE':
                    $badgeClass = 'badge-danger';
                    break;
                case 'OPEN':
                    $badgeClass = 'badge-success';
                    break;
                case 'BATAL':
                    $badgeClass = 'badge-warning';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';
            $row[] = $lm->nmpemohon;
            $row[] = $lm->instansi;
            $row[] = $lm->jabatan;
            $row[] = $lm->telepon;
            $row[] = $lm->docdate;
            $row[] = $lm->jnsperangkat;
            $row[] = $lm->periodemulai;
            $row[] = $lm->periodeakhir;
            $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
                . htmlspecialchars($lm->tujuan, ENT_QUOTES, 'UTF-8') .
                '</div>';

            // $row[] = $lm->nmstatus;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
            $row[] = $lm->printby;
            $row[] = $lm->printdate;
            $row[] = $lm->printcount;


            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_form->t_perangkat_view_count_all(),
            "recordsFiltered" => $this->m_form->t_perangkat_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input_perangkat(){
        $data['title']="INPUT DATA PERANGKAT PRIBADI";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.1'; $versirelease='I.N.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.1'";
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

        $data['back_url'] = base_url("form/pa");
        $data['typeform'] = 'INPUT';
        $data['docno'] = $nama;
        $data['status'] = '';

        $kmenu = 'I.N.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_perangkat',$data);
    }

    function edit_perangkat(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.1'; $versirelease='I.N.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.1'";
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
        $data['title']="EDIT DATA FORM IZIN MEMBAWA PERANGKAT PRIBADI";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("form/pa");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.formperangkat');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.formperangkat.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.1\'', 'left')
            ->where('TRIM(sc_trx.formperangkat.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';
        $kmenu = 'I.N.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_perangkat',$data);
    }

    function detail_perangkat(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.1'; $versirelease='I.N.A.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.1'";
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
        $data['title']="DETAIL DATA FORM IZIN MEMBAWA PERANGKAT PRIBADI";
        $var = trim($this->request->getGet('var'));
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'DETAIL';
        $data['back_url'] = base_url("form/pa");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.formperangkat');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.formperangkat.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.1\'', 'left')
            ->where('TRIM(sc_trx.formperangkat.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';
        $kmenu = 'I.N.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_perangkat',$data);
    }

    function del_perangkat(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_trx.formperangkat');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.N.A.1');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.N.A.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('form/pa'));
    }

    function showDetailPerangkat () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and docno='$var'";
        $dtl = $this->m_form->q_trxperangkat($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }



    function saveDataPerangkat(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $docno          = trim($dataprocess->docno);
            $nmpemohon      = strtoupper(trim($dataprocess->nmpemohon));
            // $deptpic     = trim($dataprocess->deptpic);
            $instansi       = strtoupper(trim($dataprocess->instansi));
            $jabatan       = strtoupper(trim($dataprocess->jabatan));
            $docdate        = date('Y-m-d', strtotime(trim($dataprocess->docdate)));
            $telepon        = trim($dataprocess->telepon);
            // $idsticker      = strtoupper(trim($dataprocess->idsticker));
            $jnsperangkat   = strtoupper(trim($dataprocess->jnsperangkat));
            $otherField     = ($dataprocess->otherField) ? strtoupper(trim($dataprocess->otherField)) : null;
            $idperangkat    = strtoupper(trim($dataprocess->idperangkat));
            $merk           = strtoupper(trim($dataprocess->merk));
            $dtllain        = strtoupper(trim($dataprocess->dtllain));
            $tujuan         = strtoupper(trim($dataprocess->tujuan));
            $periodemulai   = date('Y-m-d', strtotime(trim($dataprocess->periodemulai)));
            $periodeakhir   = date('Y-m-d', strtotime(trim($dataprocess->periodeakhir)));
            $lokasi         = strtoupper(trim($dataprocess->lokasi));
            $picjts         = strtoupper(trim($dataprocess->picjts));
            $aksesinternet  = strtoupper(trim($dataprocess->aksesinternet));
            $ipmac          = strtoupper(trim($dataprocess->ipmac));
            $wifi           = strtoupper(trim($dataprocess->wifi));
            $tujuanakses    = ($dataprocess->tujuanakses) ? strtoupper(trim($dataprocess->tujuanakses)) : null;
            $description    = strtoupper(trim($dataprocess->description));

            $ijinmasuk           = strtoupper(trim($dataprocess->ijinmasuk));
            $alasantidakmasuk           = strtoupper(trim($dataprocess->alasantidakmasuk));
            $ijinkeluar           = strtoupper(trim($dataprocess->ijinkeluar));
            $alasantidakkeluar           = strtoupper(trim($dataprocess->alasantidakkeluar));


            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_trx.formperangkat');
            if ($type === 'INPUT') {
                $info = array(
                    'docno'         => $docno,
                    'nmpemohon'     => $nmpemohon,
                    // 'deptpic'       => $deptpic,
                    'instansi'       => $instansi,
                    'jabatan'       => $jabatan,
                    'docdate'       => $docdate,
                    'telepon'     => $telepon,
                    // 'idsticker'      => $idsticker,
                    'jnsperangkat'      => $jnsperangkat,
                    'otherfield'    => $otherField,
                    'idperangkat'    => $idperangkat,
                    'merk'    => $merk,
                    'dtllain'    => $dtllain,
                    'tujuan'     => $tujuan,
                    'periodemulai'    => $periodemulai,
                    'periodeakhir' => $periodeakhir,
                    'lokasi'=> $lokasi,
                    'picjts'       => $picjts,
                    'aksesinternet'=> $aksesinternet,
                    'ipmac'       => $ipmac,
                    'wifi'         => $wifi,
                    'tujuanakses'    => $tujuanakses,
                    'description'   => $description,
                    'status'        => 'I',
                    'inputby'       => $inputby,
                    'inputdate'     => $inputdate,

                    'ijinmasuk' => $ijinmasuk === 'YA'
                        ? true
                        : ($ijinmasuk === 'TIDAK' ? false : null),
                    'alasantidakmasuk '  =>  $alasantidakmasuk,

                    'ijinkeluar' => $ijinkeluar === 'YA'
                        ? true
                        : ($ijinkeluar === 'TIDAK' ? false : null),
                    // 'ijinkeluar'  =>  $ijinkeluar,
                    'alasantidakkeluar '  =>  $alasantidakkeluar,

                );
                if ($builder->insert($info)) {
                    $inx = array('status' => 'F');
                    $builder->where('docno',$nama);
                    $builder->update($inx);
                    $perangkatRow = $builder
                        ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                        ->get()
                        ->getRowArray();
                    if ($perangkatRow) {
                        $docnoFinal   = trim($perangkatRow['docno']);
                        $url = array(
                            'barcodelink' => base_url('form/pa/getDataFormPerangkat/' . $this->fiky_encryption->sealed($docnoFinal)),
                        );
                        $builder->where('TRIM(docno)', $docnoFinal);
                        $builder->update($url);

                    }

                    $paramerror=" and userid='$nama' and modul='I.N.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtlerror['nomorakhir1']);
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and docno='$docno'";
                $check = $this->m_form->q_trxperangkat($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    // $status = null;

                    // if ($ijinmasuk !== null) {
                    //     // $status = ($ijinmasuk === 'YA') ? 'M' : 'TM';
                    // } elseif ($ijinkeluar !== null) {
                    //     // $status = ($ijinkeluar === 'YA') ? 'K' : 'TK';
                    // }

                    $info = array(
                        // 'docno'         => $docno,
                        'docno'         => $docno,
                        'nmpemohon'     => $nmpemohon,
                        // 'deptpic'       => $deptpic,
                        'instansi'       => $instansi,
                        'jabatan'       => $jabatan,
                        'docdate'       => $docdate,
                        'telepon'     => $telepon,
                        // 'idsticker'      => $this->fiky_encryption->sealed($docno),
                        'jnsperangkat'      => $jnsperangkat,
                        'otherfield'    => $otherField,
                        'idperangkat'    => $idperangkat,
                        'merk'    => $merk,
                        'dtllain'    => $dtllain,
                        'tujuan'     => $tujuan,
                        'periodemulai'    => $periodemulai,
                        'periodeakhir' => $periodeakhir,
                        'lokasi'=> $lokasi,
                        'picjts'       => $picjts,
                        'aksesinternet'=> $aksesinternet,
                        'ipmac'       => $ipmac,
                        'wifi'         => $wifi,
                        'tujuanakses'    => $tujuanakses,
                        'description'   => $description,
                        'updateby'       => $inputby,
                        'updatedate'     => $inputdate,
                        // 'barcodelink'   => ,

                        'ijinmasuk' => $ijinmasuk === 'YA'
                            ? true
                            : ($ijinmasuk === 'TIDAK' ? false : null),
                        'alasantidakmasuk '  =>  $alasantidakmasuk,

                        'ijinkeluar' => $ijinkeluar === 'YA'
                            ? true
                            : ($ijinkeluar === 'TIDAK' ? false : null),
                        'alasantidakkeluar '  =>  $alasantidakkeluar,
                        // Hanya update status kalau ada nilainya
                        // 'filedoc' => $filedoc,
                        // 'typecapex' =>  trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $typecapex : null ,
                        // 'accno' => trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $accno : null
                    );


                    // if ($status !== null) {
                    //     $info['status'] = $status;
                    // }

                    $builder->where('docno', $docno);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$docno);
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

                $builder = $this->db->table('sc_trx.formperangkat');
                $builder->where('docno' , $docno);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$docno);
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



    public function updateIzinMasuk()
    {
        $docno = trim($this->request->getPost('docno'));
        $ijinmasuk = $this->request->getPost('ijinmasuk') === 'true' ? true : false;
        $alasan = $this->request->getPost('alasantidakmasuk');

        $status = $ijinmasuk ? 'M' : 'TM'; // M = masuk, TM = tidak masuk

        // Enkripsi parameter
        $enc_param = $this->fiky_encryption->sealed($docno);
        $link      =  $ijinmasuk ? base_url("form/pa/getDataFormPerangkat?enc_param={$enc_param}") : null;

        // Update data
        $this->db->table('sc_trx.formperangkat')
            ->where('TRIM(docno)', $docno, false) // false biar TRIM tidak di-escape
            ->update([
                'ijinmasuk'        => $ijinmasuk,
                'alasantidakmasuk' => $ijinmasuk ? null : strtoupper(trim($alasan)),
                'status'           => $status,
                'barcodelink'      => $link
            ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function updateIzinKeluar()
    {
        $docno = trim($this->request->getPost('docno'));
        $ijinkeluar = $this->request->getPost('ijinkeluar') === 'true' ? true : false;
        $alasan = $this->request->getPost('alasantidakkeluar');

        $status = $ijinkeluar ? 'K' : 'TK'; // K = keluar, TK = tidak keluar

        $this->db->table('sc_trx.formperangkat')
            ->where('trim(docno)', trim($docno))
            ->update([
                'ijinkeluar' => $ijinkeluar,
                'alasantidakkeluar' => $ijinkeluar ? null : strtoupper(trim($alasan)),
                'status' => $status
            ]);

        return $this->response->setJSON(['success' => true]);
    }

    // public function getDataFormPerangkat()
    // {
    //     $param = $this->request->getGet('enc_param');
    //     $docno = trim($this->fiky_encryption->unseal($param));

    //     // Lanjutkan query atau proses berdasarkan $docno...
    // }




    public function getDataFormPerangkat($encryptedDocno)
    {
        $docno = trim($this->fiky_encryption->unseal($encryptedDocno));

        // $model = new FormPerangkatModel();
        // $param = " and docno='$docno'";
        $builder = $this->db->table('sc_trx.formperangkat a');

        $builder->select('a.*, z.uraian as nmstatus');
        $builder->join('sc_mst.trxtype z', "a.status = z.kdtrx AND z.jenistrx = 'I.N.A.1'", 'left');
        $builder->where('a.docno', $docno);

        $data['form'] = $builder->get()->getRow();
        // $data['form'] = $this->m_from->where('docno', $docno)->first();

        if (!$data['form']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        // Tentukan badgeClass
        $badgeClass = '';
        switch (trim($data['form']->nmstatus ?? '')) {
            case 'OPEN':
                $badgeClass = 'badge-success';
                break;
            case 'CLOSE':
                $badgeClass = 'badge-danger';
                break;
            default:
                $badgeClass = 'badge-secondary';
        }

        $data['badgeClass'] = $badgeClass;
        $data['status'] = $data['form']->nmstatus ?? 'Tidak diketahui';

        return view('form/form_perangkat_view', $data);
    }



    public function updateStatus()
    {
        $docno = $this->request->getPost('docno');
        $status = $this->request->getPost('status');
        $barcodelink = base_url('form/pa/getDataFormPerangkat/' . $this->fiky_encryption->sealed($docno));
        if (!$docno || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_trx.formperangkat');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status, 'barcodelink'=>$barcodelink);
        $update = $builder->update($info);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function print_perangkat(){
        $nama = trim($this->session->get('nama'));
        $enc_docno = $this->request->getGet('id');
        $docno = hex2bin(trim($this->request->getGet('id')));

        $title = " Cetak Form Perangkat Pribadi ";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_form->q_trxperangkat($param)->getRowArray();

        if (in_array(trim($dtl['status']),array('CN'))) {
            //INSERT TRX ERROR
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid', $nama);
            $builder_trxerror->where('modul', 'I.N.A.1');
            $builder_trxerror->delete();

            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => 0,
                'modul' => 'I.N.A.1',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('form/pa'));
        } else {
            $datajson =  base_url("form/pa/api_print_perangkat/?enc_docno=$enc_docno") ;
            // if (trim($dtl['kodeform'])==='JTS-08-16A') {
            //     $datamrt =  base_url("assets/mrt/capex_a_baru.mrt") ;
            // } else {
            $datamrt =  base_url("assets/mrt/form_membawa_perangkat_it.mrt") ;
            // }

            return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
        }

    }

    function api_print_perangkat(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim(hex2bin($this->request->getGet('enc_docno')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $param=" and docno='$docno'";
        $databranch = $this->m_global->q_master_branch();
        //$datamst = $this->m_bbm->q_trx_item_bbm_mst($param);
        $datadtl = $this->m_form->q_trxperangkat($param);
        $tampungdtl = $datadtl->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {

            $status = isset($detail->status) ? trim($detail->status) : '';
            if ($status) {
                $detail->status = $status;
            }

            $nmstatus = isset($detail->nmstatus) ? trim($detail->nmstatus) : '';
            if ($nmstatus) {
                $detail->nmstatus = $nmstatus;
            }

            if($status){
                $detail->docnoEncrypt = $this->fiky_encryption->sealed(trim($detail->docno));
            }
            // Pastikan jsnperangkat ada dan di-trim
            $jsnperangkat = isset($detail->jsnperangkat) ? trim($detail->jsnperangkat) : '';
            // Tambahkan properti baru isLaptop
            $detail->isLaptop = false; // Default value
            if ($jsnperangkat === 'LAPTOP') {
                $detail->isLaptop = true;
            }

            // Tambahkan properti baru isDesktop
            $detail->isDesktop = false; // Default value
            if ($jsnperangkat === 'DESKTOP') {
                $detail->isDesktop = true;
            }

            // Tambahkan properti baru isTablet
            $detail->isTablet = false; // Default value
            if ($jsnperangkat === 'TABLET') {
                $detail->isTablet = true;
            }

            // Tambahkan properti baru isPrinter
            $detail->isPrinter = false; // Default value
            if ($jsnperangkat === 'PRINTER') {
                $detail->isPrinter = true;
            }

            // Tambahkan properti baru isScanner
            $detail->isScanner = false; // Default value
            if ($jsnperangkat === 'SCANNER') {
                $detail->isScanner = true;
            }

            // Tambahkan properti baru isOther
            $detail->isOther = false; // Default value
            if ($jsnperangkat === 'OTHER') {
                $detail->isOther = true;
            }

            $detail->otherField = isset($detail->otherField) ? trim($detail->otherField) : '';
            $detail->merk = isset($detail->merk) ? trim($detail->merk) : '';
            $detail->idperangkat = isset($detail->idperangkat) ? trim($detail->idperangkat) : '';
            $detail->dtllain = isset($detail->dtllain) ? trim($detail->dtllain) : '';
            $detail->tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
            $detail->periodemulai = isset($detail->periodemulai) ? trim($detail->periodemulai) : '';
            $detail->periodeakhir = isset($detail->periodeakhir) ? trim($detail->periodeakhir) : '';

            // Pastikan lokasi ada dan di-trim
            $lokasi = isset($detail->lokasi) ? trim($detail->lokasi) : '';
            $detail->isP1 = false; // Default value
            if ($lokasi === 'P1') {
                $detail->isP1 = true;
            }

            $detail->isP2 = false; // Default value
            if ($lokasi === 'P2') {
                $detail->isP2 = true;
            }

            $detail->picjts = isset($detail->picjts) ? trim($detail->picjts) : '';

            // Pastikan aksesinternet ada dan di-trim
            $aksesinternet = isset($detail->aksesinternet) ? trim($detail->aksesinternet) : '';
            $detail->isInternet = false; // Default value
            if ($aksesinternet === 'YA') {
                $detail->isInternet = true;
            }

            $detail->isNoInternet = false; // Default value
            if ($aksesinternet === 'TIDAK') {
                $detail->isNoInternet = true;
            }


            $detail->tujuanakses = isset($detail->tujuanakses) ? trim($detail->tujuanakses) : '';
            $detail->ipmac = isset($detail->ipmac) ? trim($detail->ipmac) : '';
            $detail->wifi = isset($detail->wifi) ? trim($detail->wifi) : '';
            $detail->description = isset($detail->description) ? trim($detail->description) : '';

            // Pastikan ijinmasuk ada dan di-trim
            $ijinmasuk = isset($detail->ijinmasuk) ? trim($detail->ijinmasuk) : null;

            $detail->isMasuk = false;
            $detail->isTidakMasuk = false;

            if ($ijinmasuk === 't') {
                $detail->isMasuk = true;
            } elseif ($ijinmasuk === 'f') {
                $detail->isTidakMasuk = true;
            }



            $detail->alasantidakmasuk = isset($detail->alasantidakmasuk) ? trim($detail->alasantidakmasuk) : '';


            // Pastikan ijinkeluar ada dan di-trim
            $ijinkeluar = isset($detail->ijinkeluar) ? trim($detail->ijinkeluar) : null;

            $detail->isKeluar = false;
            $detail->isTidakKeluar = false;

            if ($ijinkeluar === 't') {
                $detail->isKeluar = true;
            } elseif ($ijinkeluar === 'f') {
                $detail->isTidakKeluar = true;
            }



            $detail->alasantidakkeluar = isset($detail->alasantidakkeluar) ? trim($detail->alasantidakkeluar) : '';
            $detail->idsticker = isset($detail->idsticker) ? trim($detail->idsticker) : '';
            $detail->barcodelink = isset($detail->barcodelink) ? trim($detail->barcodelink) : '';
            $detail->docno = isset($detail->docno) ? trim($detail->docno) : '';
            $detail->nmpemohon = isset($detail->nmpemohon) ? trim($detail->nmpemohon) : '';

            $detail->instansi = isset($detail->instansi) ? trim($detail->instansi) : '';
            $detail->jabatan = isset($detail->jabatan) ? trim($detail->jabatan) : '';
            $detail->docdate = isset($detail->docdate) ? trim($detail->docdate) : '';
            $detail->telepon = isset($detail->telepon) ? trim($detail->telepon) : '';


        }

        // Ambil status CAPEX berdasarkan docno
        $builderx = $this->db->table("sc_trx.formperangkat");
        $perangkatCek = $builderx->select('status,printcount')->where('docno', $docno)->get()->getRow();

        // Cek apakah status bukan 'FC'
        if ($perangkatCek) {
            $currentCount = is_numeric($perangkatCek->printcount) ? (int)$perangkatCek->printcount : 0;


            $nfo = array(
                // 'status' => 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s'),
                'printcount' => $currentCount + 1,
                // 'updated_at' => date('Y-m-d H:i:s')

            );

            // Update status menjadi 'P' jika bukan 'FC'
            $builderx->where('docno', $docno)->update($nfo);
        }

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                //'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }



    // ======================================================== ======================================================== IZIN INTERNET ======================================================== ========================================================







    public function internet()
    {
        $data['title']="LIST PERMINTAAN AKSES JARINGAN INTERNET";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.2'; $versirelease='I.N.A.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.2'";
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
        return $this->template->render('form/v_list_internet',$data);
    }

    function list_internet(){
        $nama=trim($this->session->get('nama'));
        $roleid=$this->session->get('roleid');
        $list = $this->m_form->get_t_internet_view();
        $roleid=trim($this->session->get('roleid'));
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;

            $btnActions = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle text-white"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-gear"></i>
                </button>
                <div class="dropdown-menu">';

            // Jika role bukan IT dan status = I → Update + Cetak
            if ($roleid!=='IT' && trim($lm->status)==='I') {
                $btnActions .= '<a class="dropdown-item" href="#" onclick="editInternet(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-edit"></i> Update</a>
                    <a class="dropdown-item" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                                <i class="fa fa-ban"></i> Batalkan Pengajuan
                            </a>';
                $btnActions .= '<a class="dropdown-item"  
                    href="' . base_url('form/pa/print_internet') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                    onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                    <i class="fa fa-print"></i> Cetak</a>';
            }

            // Tombol Cetak (selalu ada)

            // Tombol Detail (selalu ada)
            $btnActions .= '<a class="dropdown-item" href="#" onclick="detailInternet(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-eye"></i> Detail Data</a>';


            // Jika role IT → tambahkan kontrol status
            if ($roleid==='IT') {
                if (trim($lm->nmstatus) !== 'BATAL'){
                    $btnActions .= '<a class="dropdown-item" href="#" onclick="editInternet(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-edit"></i> Update</a>';
                }

                // if (trim($lm->nmstatus) !== 'DIIZINKAN' && trim($lm->nmstatus) !== 'BATAL' && trim($lm->nmstatus) !== 'TIDAK DIIZINKAN' && trim($lm->inputby) == $nama){
                $btnActions .= '<a class="dropdown-item"  
                        href="' . base_url('form/pa/print_internet') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                        onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                        <i class="fa fa-print"></i> Cetak</a>';
                // }


                if (trim($lm->nmstatus) !== 'DIIZINKAN' && trim($lm->nmstatus) !== 'BATAL' && trim($lm->nmstatus) !== 'TIDAK DIIZINKAN'){
                    $btnActions .= '<a class="dropdown-item" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                                <i class="fa fa-ban"></i> Batalkan Pengajuan
                            </a>';
                }

                if (trim($lm->nmstatus) !== 'DIIZINKAN' && trim($lm->nmstatus) !== 'BATAL') {
                    $btnActions .= '<a class="dropdown-item bg-success" href="#" onclick="setToDiizinkan(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Set To Diizinkan</a>';
                }
                if (trim($lm->nmstatus) !== 'TIDAK DIIZINKAN' && trim($lm->nmstatus) !== 'BATAL') {
                    $btnActions .= '<a class="dropdown-item bg-danger" href="#" onclick="setToTidakDiizinkan(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-times-circle"></i> Set To Tidak Diizinkan</a>';
                }
            }
            $btnActions .= '</div></div>';

            $row[] = $btnActions;

            $row[] = '<div class="text-bold">' . $lm->docno . '</div>';
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'TIDAK DIIZINKAN':
                    $badgeClass = 'badge-danger';
                    break;
                case 'DIIZINKAN':
                    $badgeClass = 'badge-success';
                    break;
                case 'BATAL':
                    $badgeClass = 'badge-warning';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';
            $row[] = $lm->nmpemohon;
            $row[] = $lm->jabatan;
            $row[] = $lm->departmen;
            // $row[] = $lm->nmlvljabatan;
            // $row[] = $lm->nmdept;
            $row[] = $lm->docdate;
            $row[] = $lm->expdate;
            // $row[] = $lm->jnsakses;
            $aksesList = [];

            if ($lm->aksesuser === true || $lm->aksesuser === 't') {
                $aksesList[] = 'User';
            }
            if ($lm->aksesinternet === true || $lm->aksesinternet === 't') {
                $aksesList[] = 'Internet';
            }
            if ($lm->aksesemail === true || $lm->aksesemail === 't') {
                $aksesList[] = 'Email';
            }
            if ($lm->aksesapp === true || $lm->aksesapp === 't') {
                $aksesList[] = 'Aplikasi';
            }
            if ($lm->aksesother === true || $lm->aksesother === 't') {
                $aksesList[] = 'Lain-lain';
            }

            $row[] = implode(', ', $aksesList);
            $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
                . htmlspecialchars($lm->keperluan, ENT_QUOTES, 'UTF-8') .
                '</div>';

            // $row[] = $lm->nmstatus;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;

            $row[] = $lm->printby;
            $row[] = $lm->printdate;
            $row[] = $lm->printcount;
            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_form->t_internet_view_count_all(),
            "recordsFiltered" => $this->m_form->t_internet_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input_internet(){
        $data['title']="INPUT PERMINTAAN AKSES JARINGAN & SYSTEM";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.2'; $versirelease='I.N.A.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.2'";
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
        $data['back_url'] = base_url("form/pa/internet");
        $data['typeform'] = 'INPUT';
        $data['docno'] = $nama;
        $data['status'] = '';
        $kmenu = 'I.N.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_internet',$data);
    }

    function edit_internet(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.2'; $versirelease='I.N.A.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.2'";
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
        $data['title']="EDIT DATA PERMOHONAN AKSES SYSTEM & JARINGAN";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("form/pa/internet");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.forminternet');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.forminternet.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.2\'', 'left')
            ->where('TRIM(sc_trx.forminternet.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';

        $kmenu = 'I.N.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_internet',$data);
    }

    function detail_internet(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.2'; $versirelease='I.N.A.2/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.2'";
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
        $data['title']="DETAIL DATA PERMOHONAN AKSES SYSTEM & JARINGAN";
        $var = trim($this->request->getGet('var'));
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'DETAIL';
        $data['back_url'] = base_url("form/pa/internet");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.forminternet');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.forminternet.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.2\'', 'left')
            ->where('TRIM(sc_trx.forminternet.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';

        $kmenu = 'I.N.A.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_internet',$data);
    }

    function del_internet(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_trx.forminternet');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.N.A.2');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.N.A.2',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('form/pa/internet'));
    }

    function showDetailInternet () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and docno='$var'";
        $dtl = $this->m_form->q_trxinternet($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }


    function saveDataInternet(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $docno = trim($dataprocess->docno);
            $nmpemohon = strtoupper($dataprocess->nmpemohon);

            $deptpic = trim($dataprocess->departemenpic);
            $jabatan = strtoupper($dataprocess->jabatan);
            $docdate = date('Y-m-d',strtotime($dataprocess->docdate));
            $departmen = strtoupper($dataprocess->departmen);
            $osticket = strtoupper($dataprocess->osticket);

            // $jnsakses = trim($dataprocess->jnsakses);
            $aksesUser = (($dataprocess->aksesUser));
            $aksesInternet = (($dataprocess->aksesInternet));
            $aksesEmail = (($dataprocess->aksesEmail));
            $aksesApp = (($dataprocess->aksesApp));
            $aksesOther = (($dataprocess->aksesOther));

            $appField = ($dataprocess->appField) ? strtoupper($dataprocess->appField) : null;
            $emailField = ($dataprocess->emailField) ? strtolower($dataprocess->emailField) : null;
            $otherField = ($dataprocess->otherField) ? strtoupper($dataprocess->otherField) : null;
            $keperluan = strtoupper($dataprocess->keperluan);
            $sifatakses = trim($dataprocess->sifatakses);
            $waktuakses = trim($dataprocess->waktuakses);
            $tertentuField = ($dataprocess->tertentuField) ? strtoupper($dataprocess->tertentuField) : null;
            $otherWaktuField =  ($dataprocess->otherWaktuField) ? strtoupper($dataprocess->otherWaktuField) : null;
            $expdate = null; // Default value untuk RUTIN

            if ($sifatakses === 'RUTIN') {
                $expdate = null; // atau $expdate = '';
            } else {
                $expdate = ($dataprocess->expdate && trim($dataprocess->expdate) != '')
                    ? date('Y-m-d', strtotime(trim($dataprocess->expdate)))
                    : null;
            }
            $perangkatregist = strtoupper($dataprocess->perangkatregist);
            $picakun = trim($dataprocess->picakun);
            $nmpic = ($dataprocess->nmpic) ? strtoupper($dataprocess->nmpic) : null;
            $jabatanpic = ($dataprocess->jabatanpic) ? strtoupper($dataprocess->jabatanpic) : null;
            $departemenpic = ($dataprocess->departemenpic) ? strtoupper($dataprocess->departemenpic) : null;
            $description = strtoupper($dataprocess->jabatanpic);


            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_trx.forminternet');

            /*
            // Pastikan nilai kosong diubah menjadi 0
            // $ttlprice = empty( $ttlprice) ? 0 :  $ttlprice;
            // $qtyasset = empty($qtyasset) ? 0 : $qtyasset;
            // $valbudget = empty($valbudget) ? 0 : $valbudget;
            // $valprice = empty($valprice) ? 0 : $valprice;
            // $valremainbudget = empty($valremainbudget) ? 0 : $valremainbudget;
            // // CHECK INPUT TYPE

            // $uploadPath = FCPATH . 'uploads/capex/lampiran_capex';
            // $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            // $maxSize = 2 * 1024 * 1024; // 2MB
            // $filedoc = null;
            // $ext = null;

            // // Cek file upload valid (tapi belum disimpan)
            // if (isset($_FILES['filedoc']) && $_FILES['filedoc']['error'] === UPLOAD_ERR_OK) {
            //     $ext = strtolower(pathinfo($_FILES['filedoc']['name'], PATHINFO_EXTENSION));

            //     if (!in_array($ext, $allowedExtensions)) {
            //         echo json_encode(['status' => false, 'messages' => 'Tipe file tidak valid. Hanya PDF, JPG, JPEG, PNG.']);
            //         return;
            //     }

            //     if ($_FILES['filedoc']['size'] > $maxSize) {
            //         echo json_encode(['status' => false, 'messages' => 'Ukuran file maksimal 2MB.']);
            //         return;
            //     }

            //     if (!is_dir($uploadPath)) {
            //         mkdir($uploadPath, 0755, true);
            //     }
            // }*/
            if ($type === 'INPUT') {
                $info = array(
                    'docno'         => $docno,
                    'nmpemohon'     => $nmpemohon,
                    // 'deptpic'       => $deptpic,
                    'jabatan'       => $jabatan,
                    'docdate'       => $docdate,
                    'departmen'     => $departmen,
                    'osticket'      => $osticket,

                    // 'jnsakses'      => $jnsakses,
                    'aksesuser'      => $aksesUser,
                    'aksesinternet'      => $aksesInternet,
                    'aksesemail'      => $aksesEmail,
                    'aksesapp'      => $aksesApp,
                    'aksesother'      => $aksesOther,

                    'appfield'    => $appField,
                    'emailfield'    => $emailField,
                    'otherfield'    => $otherField,
                    'keperluan'     => $keperluan,
                    'sifatakses'    => $sifatakses,
                    'waktuakses'    => $waktuakses,
                    'tertentufield' => $tertentuField,
                    'otherwaktufield'=> $otherWaktuField,
                    'expdate'       => $expdate,

                    'perangkatregist'=> $perangkatregist,
                    'picakun'       => $picakun,
                    'nmpic'         => $nmpic,
                    'jabatanpic'    => $jabatanpic,
                    'departemenpic' => $departemenpic,
                    'description'   => $description,

                    'status'        => 'I',
                    'inputby'       => $inputby,
                    'inputdate'     => $inputdate
                );
                if ($builder->insert($info)) {
                    $inx = array('status' => 'F');
                    $builder->where('docno',$nama);
                    $builder->update($inx);

                    $internetRow = $builder
                        ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                        ->get()
                        ->getRowArray();
                    if ($internetRow) {
                        $docnoFinal   = trim($internetRow['docno']);
                        $url = array(
                            'barcodelink' => base_url('form/pa/getDataFormInternet/' . $this->fiky_encryption->sealed($docnoFinal)),
                        );
                        $builder->where('TRIM(docno)', $docnoFinal);
                        $builder->update($url);

                    }

                    $paramerror=" and userid='$nama' and modul='I.N.A.2'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtlerror['nomorakhir1']);
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and docno='$docno'";
                $check = $this->m_form->q_trxinternet($param)->getNumRows();
                //check data ganda
                if ($check > 0) {

                    // $filedoc = null;
                    // $ext = null;
                    // $uploadPath = FCPATH . 'uploads/capex/lampiran_capex';
                    // $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                    // $maxSize = 2 * 1024 * 1024;

                    // // Cek & hapus file lama jika ada upload baru
                    // if (isset($_FILES['filedoc']) && $_FILES['filedoc']['error'] === UPLOAD_ERR_OK) {
                    //     $ext = strtolower(pathinfo($_FILES['filedoc']['name'], PATHINFO_EXTENSION));

                    //     if (!in_array($ext, $allowedExtensions)) {
                    //         echo json_encode(['status' => false, 'messages' => 'Tipe file tidak valid. Hanya PDF, JPG, JPEG, PNG.']);
                    //         return;
                    //     }

                    //     if ($_FILES['filedoc']['size'] > $maxSize) {
                    //         echo json_encode(['status' => false, 'messages' => 'Ukuran file maksimal 2MB.']);
                    //         return;
                    //     }

                    //     if (!is_dir($uploadPath)) {
                    //         mkdir($uploadPath, 0755, true);
                    //     }

                    //     // Ambil data lama untuk unlink
                    //     $old = $this->m_form->q_trxcapex("AND docno='$docno'")->getRowArray();
                    //     if (!empty($old['filedoc']) && file_exists($uploadPath . '/' . $old['filedoc'])) {
                    //         unlink($uploadPath . '/' . $old['filedoc']);
                    //     }

                    //     // Simpan file baru
                    //     $filedoc = $docno . '_lampiran_penawaran.' . $ext;
                    //     $fileDest = $uploadPath . '/' . $filedoc;
                    //     if (!move_uploaded_file($_FILES['filedoc']['tmp_name'], $fileDest)) {
                    //         echo json_encode(['status' => false, 'messages' => 'Gagal menyimpan file upload.']);
                    //         return;
                    //     }
                    // }
                    $info = array(
                        // 'docno'         => $docno,
                        'nmpemohon'     => $nmpemohon,
                        'deptpic'       => $deptpic,
                        'jabatan'       => $jabatan,
                        'docdate'       => $docdate,
                        'departmen'     => $departmen,
                        'osticket'      => $osticket,

                        // 'jnsakses'      => $jnsakses,
                        'aksesuser'      => $aksesUser,
                        'aksesinternet'      => $aksesInternet,
                        'aksesemail'      => $aksesEmail,
                        'aksesapp'      => $aksesApp,
                        'aksesother'      => $aksesOther,


                        'appfield'    => $appField,
                        'emailfield'    => $emailField,
                        'otherfield'    => $otherField,
                        'keperluan'     => $keperluan,
                        'sifatakses'    => $sifatakses,
                        'waktuakses'    => $waktuakses,
                        'tertentufield' => $tertentuField,
                        'otherwaktufield'=> $otherWaktuField,
                        'expdate'       => $expdate,

                        'perangkatregist'=> $perangkatregist,
                        'picakun'       => $picakun,
                        'nmpic'         => $nmpic,
                        'jabatanpic'    => $jabatanpic,
                        'departemenpic' => $departemenpic,
                        'description'   => $description,

                        'status'        => 'I',
                        'updateby'       => $inputby,
                        'updatedate'     => $inputdate
                        // 'filedoc' => $filedoc,
                        // 'typecapex' =>  trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $typecapex : null ,
                        // 'accno' => trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $accno : null
                    );

                    $builder->where('docno', $docno);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$docno);
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

                $builder = $this->db->table('sc_trx.forminternet');
                $builder->where('docno' , $docno);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$docno);
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



    public function updateStatusInternet()
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
        $builder = $db->table('sc_trx.forminternet');
        $builder->where('docno', $docno);
        $update = $builder->update(['status' => $status]);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    public function getDataFormInternet($encryptedDocno)
    {
        $docno = trim($this->fiky_encryption->unseal($encryptedDocno));

        // $model = new FormPerangkatModel();
        // $param = " and docno='$docno'";
        $builder = $this->db->table('sc_trx.forminternet a');

        $builder->select('a.*, z.uraian as nmstatus');
        $builder->join('sc_mst.trxtype z', "a.status = z.kdtrx AND z.jenistrx = 'I.N.A.2'", 'left');
        $builder->where('a.docno', $docno);

        $data['form'] = $builder->get()->getRow();
        // $data['form'] = $this->m_from->where('docno', $docno)->first();

        if (!$data['form']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        // Tentukan badgeClass
        $badgeClass = '';
        switch (trim($data['form']->nmstatus ?? '')) {
            case 'DIIZINKAN':
                $badgeClass = 'badge-success';
                break;
            case 'TIDAK DIIZINKAN':
                $badgeClass = 'badge-danger';
                break;
            default:
                $badgeClass = 'badge-secondary';
        }

        $data['badgeClass'] = $badgeClass;
        $data['status'] = $data['form']->nmstatus ?? 'Tidak diketahui';

        return view('form/form_internet_view', $data);
    }



    function print_internet(){
        $nama = trim($this->session->get('nama'));
        $enc_docno = $this->request->getGet('id');
        $docno = hex2bin(trim($this->request->getGet('id')));

        $title = " Cetak Form Izin Akses Internet ";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_form->q_trxinternet($param)->getRowArray();

        if (in_array(trim($dtl['status']),array('CN'))) {
            //INSERT TRX ERROR
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid', $nama);
            $builder_trxerror->where('modul', 'I.N.A.2');
            $builder_trxerror->delete();

            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => 0,
                'modul' => 'I.N.A.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('form/pa/internet'));
        } else {
            $datajson =  base_url("form/pa/api_print_internet/?enc_docno=$enc_docno") ;
            // if (trim($dtl['kodeform'])==='JTS-08-16A') {
            //     $datamrt =  base_url("assets/mrt/capex_a_baru.mrt") ;
            // } else {
            $datamrt =  base_url("assets/mrt/form_permintaan_akses_jaringan_system.mrt") ;
            // }

            return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
        }

    }

    function api_print_internet(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim(hex2bin($this->request->getGet('enc_docno')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $param=" and docno='$docno'";
        $databranch = $this->m_global->q_master_branch();
        //$datamst = $this->m_bbm->q_trx_item_bbm_mst($param);
        $datadtl = $this->m_form->q_trxinternet($param);
        $tampungdtl = $datadtl->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {


            $detail->docno = isset($detail->docno) ? trim($detail->docno) : '';
            $detail->nmpemohon = isset($detail->nmpemohon) ? trim($detail->nmpemohon) : '';

            $detail->departmen = isset($detail->departmen) ? trim($detail->departmen) : '';
            $detail->jabatan = isset($detail->jabatan) ? trim($detail->jabatan) : '';

            $detail->docdate = isset($detail->docdate) ? trim($detail->docdate) : '';
            $detail->osticket = isset($detail->osticket) ? trim($detail->osticket) : '';


            $status = isset($detail->status) ? trim($detail->status) : '';
            if ($status) {
                $detail->status = $status;
            }

            $nmstatus = isset($detail->nmstatus) ? trim($detail->nmstatus) : '';
            if ($nmstatus) {
                $detail->nmstatus = $nmstatus;
            }

            if($status){
                $detail->docnoEncrypt = $this->fiky_encryption->sealed(trim($detail->docno));
            }
            // Pastikan jnsakses ada dan di-trim
            // $jnsakses = isset($detail->jnsakses) ? trim($detail->jnsakses) : '';
            // // Tambahkan properti baru isUser
            // $detail->isUser = false; // Default value
            // if ($jnsakses === 'USER') {
            //     $detail->isUser = true;
            // }

            // // Tambahkan properti baru isInternet
            // $detail->isInternet = false; // Default value
            // if ($jnsakses === 'INTERNET') {
            //     $detail->isInternet = true;
            // }

            // // Tambahkan properti baru isEmail
            // $detail->isEmail = false; // Default value
            // if ($jnsakses === 'EMAIL') {
            //     $detail->isEmail = true;
            // }

            // // Tambahkan properti baru isAplikasi
            // $detail->isAplikasi = false; // Default value
            // if ($jnsakses === 'APLIKASI') {
            //     $detail->isAplikasi = true;
            // }

            // // Tambahkan properti baru isOther
            // $detail->isOther = false; // Default value
            // if ($jnsakses === 'OTHER') {
            //     $detail->isOther = true;
            // }


            // aksesUser
            $aksesUser = isset($detail->aksesuser) ? trim($detail->aksesuser) : 'f';
            $detail->isUser = ($aksesUser === 't');

            // aksesInternet
            $aksesInternet = isset($detail->aksesinternet) ? trim($detail->aksesinternet) : 'f';
            $detail->isInternet = ($aksesInternet === 't');

            // aksesEmail
            $aksesEmail = isset($detail->aksesemail) ? trim($detail->aksesemail) : 'f';
            $detail->isEmail = ($aksesEmail === 't');

            // aksesApp
            $aksesApp = isset($detail->aksesapp) ? trim($detail->aksesapp) : 'f';
            $detail->isAplikasi = ($aksesApp === 't');

            // aksesOther
            $aksesOther = isset($detail->aksesother) ? trim($detail->aksesother) : 'f';
            $detail->isOther = ($aksesOther === 't');


            $detail->emailfield = isset($detail->emailfield) ? trim($detail->emailfield) : '';
            $detail->appfield = isset($detail->appfield) ? trim($detail->appfield) : '';
            $detail->otherfield = isset($detail->otherfield) ? trim($detail->otherfield) : '';

            $detail->keperluan = isset($detail->keperluan) ? trim($detail->keperluan) : '';

            // Pastikan sifatakses ada dan di-trim
            $sifatakses = isset($detail->sifatakses) ? trim($detail->sifatakses) : '';
            $detail->isRutin = false; // Default value
            if ($sifatakses === 'RUTIN') {
                $detail->isRutin = true;
            }

            $detail->isSementara = false; // Default value
            if ($sifatakses === 'SEMENTARA') {
                $detail->isSementara = true;
            }


            // Pastikan waktuakses ada dan di-trim
            $waktuakses = isset($detail->waktuakses) ? trim($detail->waktuakses) : '';
            $detail->isHariKerja = false; // Default value
            if ($waktuakses === 'KERJA') {
                $detail->isHariKerja = true;
            }

            $detail->isWaktuTertentu = false; // Default value
            if ($waktuakses === 'TERTENTU') {
                $detail->isWaktuTertentu = true;
            }


            $detail->isOtherWaktu = false; // Default value
            if ($waktuakses === 'OTHER') {
                $detail->isOtherWaktu = true;
            }
            $detail->tertentufield = isset($detail->tertentufield) ? trim($detail->tertentufield) : '';
            $detail->otherwaktufield = isset($detail->otherwaktufield) ? trim($detail->otherwaktufield) : '';
            $detail->expdate = isset($detail->expdate) ? trim($detail->expdate) : '';
            $detail->perangkatregist = isset($detail->perangkatregist) ? trim($detail->perangkatregist) : '';

            // Pastikan picakun ada dan di-trim
            $picakun = isset($detail->picakun) ? trim($detail->picakun) : '';
            $detail->isPemohon = false; // Default value
            if ($picakun === 'PEMOHON') {
                $detail->isPemohon = true;
            }

            $detail->isKaryawanLain = false; // Default value
            if ($picakun === 'OTHER') {
                $detail->isKaryawanLain = true;
            }

            $detail->nmpic = isset($detail->nmpic) ? trim($detail->nmpic) : '';
            $detail->jabatanpic = isset($detail->jabatanpic) ? trim($detail->jabatanpic) : '';
            $detail->departemenpic = isset($detail->departemenpic) ? trim($detail->departemenpic) : '';
            $detail->description = isset($detail->description) ? trim($detail->description) : '';

        }

        // Ambil status CAPEX berdasarkan docno
        $builderx = $this->db->table("sc_trx.forminternet");
        $internetCek = $builderx->select('status,printcount')->where('docno', $docno)->get()->getRow();

        // Cek apakah status bukan 'FC'
        if ($internetCek) {
            $currentCount = is_numeric($internetCek->printcount) ? (int)$internetCek->printcount : 0;


            $nfo = array(
                // 'status' => 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s'),
                'printcount' => $currentCount + 1,
                // 'updated_at' => date('Y-m-d H:i:s')

            );

            // Update status menjadi 'P' jika bukan 'FC'
            $builderx->where('docno', $docno)->update($nfo);
        }

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                //'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }




















    //PERANGKAT KELUAR



    public function laptop()
    {
        $data['title']="LIST PERANGKAT KELUAR";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.3'; $versirelease='I.N.A.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.3'";
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
        // $this->m_form->q_autoinsert_unit();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_list_perangkat_keluar',$data);
    }

    function list_laptop(){
        $nama=trim($this->session->get('nama'));

        $roleid=$this->session->get('roleid');
        $list = $this->m_form->get_t_perangkatkeluar_view();
        $roleid=trim($this->session->get('roleid'));
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            if ($roleid !== 'IT') {
                // Role bukan IT
                if (trim($lm->status) === 'I') {
                    // Status I → Update + Cetak
                    $btnActions = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle text-white"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-gear"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" 
                                    onclick="editPerangkat(\'' . trim($lm->docno) . '\');" >
                                    <i class="fa fa-edit"></i> Update
                                </a>
                                <a class="dropdown-item" href="#" 
                                    onclick="detailPerangkat(\'' . trim($lm->docno) . '\');" >
                                    <i class="fa fa-eye"></i> Detail Data
                                </a>
                                <a class="dropdown-item"  
                                    href="' . base_url('form/pa/print_perangkatkeluar') .
                        '/?id=' . bin2hex(trim($lm->docno)) .
                        '&docno=' . bin2hex(trim($lm->docno)) . '" 
                                    onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                                    <i class="fa fa-print"></i> Cetak
                                </a>
                                <a class="dropdown-item" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                                    <i class="fa fa-ban"></i> Batalkan Pengajuan
                                </a>';

                    $btnActions .= '
                            </div>
                        </div>';
                } else {
                    // Status bukan I → hanya Cetak
                    $btnActions = '
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle text-white"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-gear"></i>
                                </button>
                                <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" 
                                    onclick="detailPerangkat(\'' . trim($lm->docno) . '\');" >
                                    <i class="fa fa-eye"></i> Detail Data
                                </a>';

                    // if((trim($lm->nmstatus) !== 'BATAL')){
                    //     $btnActions .= '
                    //                 <a class="dropdown-item"
                    //                     href="' . base_url('form/pa/print_perangkatkeluar') .
                    //                         '/?id=' . bin2hex(trim($lm->docno)) .
                    //                         '&docno=' . bin2hex(trim($lm->docno)) . '"
                    //                     onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                    //                     <i class="fa fa-print"></i> Cetak
                    //                 </a>';
                    // }

                    // if ((trim($lm->nmstatus) !== 'BATAL') && (trim($lm->nmstatus) !== 'OPEN') && (trim($lm->nmstatus) !== 'CLOSE')) {
                    //     $btnActions .= '
                    //             <a class="dropdown-item" href="#" onclick="cancelPerangkat(\'' . trim($lm->docno) . '\');">
                    //                 <i class="fa fa-ban"></i> Batalkan Pengajuan
                    //             </a>';
                    // }

                    $btnActions .= '
                            </div>
                        </div>';
                }
            } else {
                // Role IT
                $btnActions = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle text-white"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-gear"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" 
                                    onclick="detailPerangkat(\'' . trim($lm->docno) . '\');" >
                                    <i class="fa fa-eye"></i> Detail Data
                            </a>
                            <a class="dropdown-item"  
                                    href="' . base_url('form/pa/print_perangkatkeluar') .
                    '/?id=' . bin2hex(trim($lm->docno)) .
                    '&docno=' . bin2hex(trim($lm->docno)) . '" 
                                    onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                                    <i class="fa fa-print"></i> Cetak
                                </a>
                            ';

                // if(){
                //     $btnActions .= '<a class="dropdown-item"
                //                     href="' . base_url('form/pa/print_perangkatkeluar') .
                //                         '/?id=' . bin2hex(trim($lm->docno)) .
                //                         '&docno=' . bin2hex(trim($lm->docno)) . '"
                //                     onclick="return confirm(\'Print This Form Data : ' . trim($lm->docno) . '\')">
                //                     <i class="fa fa-print"></i> Cetak
                //                 </a>';


                // }
                if((trim($lm->nmstatus) !== 'BATAL')){
                    $btnActions .= ' <a class="dropdown-item" href="#" 
                                    onclick="editPerangkat(\'' . trim($lm->docno) . '\');" >
                                    <i class="fa fa-edit"></i> Update
                                </a>';
                }

                // Tambahan Batalkan Pengajuan
                if ((trim($lm->nmstatus) !== 'BATAL')  && (trim($lm->nmstatus) !== 'OPEN') && (trim($lm->nmstatus) !== 'CLOSE'))  {
                    $btnActions .= '
                            <a class="dropdown-item" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                                <i class="fa fa-ban"></i> Batalkan Pengajuan
                            </a>';
                }

                // Tambahan khusus IT
                if (trim($lm->nmstatus) !== 'OPEN' && (trim($lm->nmstatus) !== 'BATAL')) {
                    $btnActions .= '
                            <a class="dropdown-item bg-success" href="#" onclick="setToOpen(\'' . trim($lm->docno) . '\');">
                                <i class="fa fa-external-link-alt"></i> Set To Open
                            </a>';
                }
                if (trim($lm->nmstatus) !== 'CLOSE' && (trim($lm->nmstatus) !== 'BATAL')) {
                    $btnActions .= '
                            <a class="dropdown-item bg-danger" href="#" onclick="setToClosed(\'' . trim($lm->docno) . '\');">
                                <i class="fa fa-times-circle"></i> Set To Close
                            </a>';
                }

                $btnActions .= '
                        </div>
                    </div>';
            }



            $row[] = $btnActions;
            $row[] = '<div class="text-bold">' . $lm->docno . '</div>';
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'CLOSE':
                    $badgeClass = 'badge-danger';
                    break;
                case 'OPEN':
                    $badgeClass = 'badge-success';
                    break;
                case 'BATAL':
                    $badgeClass = 'badge-warning';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';
            $row[] = $lm->nmpemohon;
            // $row[] = $lm->nmdept;
            // $row[] = $lm->nmlvljabatan;
            $row[] = $lm->departmen;
            $row[] = $lm->jabatan;
            // jnsform
            $jnsform = strtoupper(trim($lm->jnsform));
            if ($jnsform === 'BARU') {
                $row[] = '<span class="w-100 badge bg-success">BARU</span>';
            } elseif ($jnsform === 'PERPANJANGAN') {
                $row[] = '<span class="w-100 badge bg-info text-dark">PERPANJANGAN</span>';
            }

            // docdate
            $row[] = $lm->docdate;

            // jnsperangkat
            $row[] = $lm->jnsperangkat;

            // periode
            $periode = strtoupper(trim($lm->periode));
            if ($periode === 'RUTIN') {
                $row[] = '<span class="w-100 badge bg-primary text-dark">RUTIN</span>';
            } elseif ($periode === 'TERTENTU') {
                $row[] = '<span class="w-100 badge bg-dark">TERTENTU</span>';
            }

            $row[] = $lm->periodemulai;
            $row[] = $lm->periodeakhir;

            $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
                . htmlspecialchars($lm->keperluan, ENT_QUOTES, 'UTF-8') .
                '</div>';
            $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
                . htmlspecialchars($lm->tujuan, ENT_QUOTES, 'UTF-8') .
                '</div>';
            // $row[] = $lm->nmstatus;
            // $row[] = $lm->tujuan;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
            $row[] = $lm->printby;
            $row[] = $lm->printdate;
            $row[] = $lm->printcount;
            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_form->t_perangkatkeluar_view_count_all(),
            "recordsFiltered" => $this->m_form->t_perangkatkeluar_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }


    function input_perangkatkeluar(){
        $data['title']="INPUT DATA PERANGKAT KELUAR PERUSAHAAN";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.3'; $versirelease='I.N.A.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.3'";
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

        $data['back_url'] = base_url("form/pa/laptop");
        $data['typeform'] = 'INPUT';
        $data['docno'] = $nama;
        $data['status'] = '';

        $kmenu = 'I.N.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_perangkat_keluar',$data);
    }

    function edit_perangkatkeluar(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.3'; $versirelease='I.N.A.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.3'";
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
        $data['title']="EDIT DATA FORM IZIN MEMBAWA PERANGKAT PRIBADI";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("form/pa/laptop");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.formperangkatkeluar');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.formperangkatkeluar.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.3\'', 'left')
            ->where('TRIM(sc_trx.formperangkatkeluar.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';
        $kmenu = 'I.N.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_perangkat_keluar',$data);
    }


    function detail_perangkatkeluar(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.3'; $versirelease='I.N.A.3/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.3'";
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
        $data['title']="DETAIL DATA FORM IZIN MEMBAWA PERANGKAT PRIBADI";
        $var = trim($this->request->getGet('var'));
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'DETAIL';
        $data['back_url'] = base_url("form/pa/laptop");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.formperangkatkeluar');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.formperangkatkeluar.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.3\'', 'left')
            ->where('TRIM(sc_trx.formperangkatkeluar.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';
        $kmenu = 'I.N.A.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_perangkat_keluar',$data);
    }

    function del_perangkatkeluar(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getGet('var'));
        $builder = $this->db->table('sc_trx.formperangkatkeluar');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $builder->where('id',$id);
        $builder->delete();
        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.N.A.3');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $id,
            'nomorakhir2' => '',
            'modul' => 'I.N.A.3',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('form/pa'));
    }

    function showDetailPerangkatKeluar () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and docno='$var'";
        $dtl = $this->m_form->q_trxperangkatkeluar($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }



    function saveDataPerangkatKeluar(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $docno          = trim($dataprocess->docno);
            $nmpemohon      = strtoupper(trim($dataprocess->nmpemohon));
            // $deptpic     = trim($dataprocess->deptpic);
            $departmen       = strtoupper(trim($dataprocess->departmen));
            $jabatan       = strtoupper(trim($dataprocess->jabatan));
            $docdate        = date('Y-m-d', strtotime(trim($dataprocess->docdate)));
            $osticket        = trim($dataprocess->osticket);
            $jnsform        = trim($dataprocess->jnsform);

            // $idsticker      = strtoupper(trim($dataprocess->idsticker));
            $jnsperangkat   = strtoupper(trim($dataprocess->jnsperangkat));
            $otherField     = ($dataprocess->otherField) ? strtoupper(trim($dataprocess->otherField)) : null;
            $merk    = strtoupper(trim($dataprocess->merk));
            $noasset           = strtoupper(trim($dataprocess->noasset));
            $qty        = strtoupper(trim($dataprocess->qty));
            $keperluan         = strtoupper(trim($dataprocess->keperluan));
            $tujuan         = strtoupper(trim($dataprocess->tujuan));
            // $periode         = strtoupper(trim($dataprocess->periode));
            // $periodemulai   = ($dataprocess->periodemulai) ? date('Y-m-d', strtotime(trim($dataprocess->periodemulai))) : null;
            // $periodeakhir   = ($dataprocess->periodeakhir) ? date('Y-m-d', strtotime(trim($dataprocess->periodeakhir))) : null;
            $docdate        = date('Y-m-d', strtotime(trim($dataprocess->docdate)));
            $periode        = strtoupper(trim($dataprocess->periode));

            if ($periode === 'RUTIN') {
                $periodemulai = $docdate;
                $periodeakhir = date('Y-m-d', strtotime($docdate . ' +6 months'));
            } else {
                $periodemulai = ($dataprocess->periodemulai)
                    ? date('Y-m-d', strtotime(trim($dataprocess->periodemulai)))
                    : null;
                $periodeakhir = ($dataprocess->periodeakhir)
                    ? date('Y-m-d', strtotime(trim($dataprocess->periodeakhir)))
                    : null;
            }
            $picakun         = strtoupper(trim($dataprocess->picakun));
            $nmpic         = strtoupper(trim($dataprocess->nmpic));
            $jabatanpic  = strtoupper(trim($dataprocess->jabatanpic));
            $departemenpic          = strtoupper(trim($dataprocess->departemenpic));

            $tglpemeriksaan = !empty($dataprocess->tglpemeriksaan)
                ? date('Y-m-d', strtotime(trim($dataprocess->tglpemeriksaan)))
                : null;            $dataperangkat           = strtoupper(trim($dataprocess->dataperangkat));
            $enkripsidata           = strtoupper(trim($dataprocess->enkripsidata));
            $kondisi           = strtoupper(trim($dataprocess->kondisi));
            $tglpenyerahan = !empty($dataprocess->tglpenyerahan)
                ? date('Y-m-d', strtotime(trim($dataprocess->tglpenyerahan)))
                : null;

            $tglpengembalian = !empty($dataprocess->tglpengembalian)
                ? date('Y-m-d', strtotime(trim($dataprocess->tglpengembalian)))
                : null;
            $pembaruanizin           = strtoupper(trim($dataprocess->pembaruanizin));


            $alasanterlambat    = ($dataprocess->alasanterlambat) ? strtoupper(trim($dataprocess->alasanterlambat)) : null;
            $keteranganlain           = strtoupper(trim($dataprocess->keteranganlain));
            // $description    = strtoupper(trim($dataprocess->description));

            // $ijinmasuk           = strtoupper(trim($dataprocess->ijinmasuk));
            // $alasantidakmasuk           = strtoupper(trim($dataprocess->alasantidakmasuk));
            // $ijinkeluar           = strtoupper(trim($dataprocess->ijinkeluar));


            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_trx.formperangkatkeluar');
            if ($type === 'INPUT') {
                $info = array(
                    'docno'         => $docno,
                    'nmpemohon'     => $nmpemohon,
                    // 'deptpic'       => $deptpic,
                    'departmen'       => $departmen,
                    'jabatan'       => $jabatan,
                    'docdate'       => $docdate,
                    'jnsform'       => $jnsform,
                    'osticket'     => $osticket,
                    // 'idsticker'      => $idsticker,
                    'jnsperangkat'      => $jnsperangkat,
                    'otherfield'    => $otherField,
                    'merk'    => $merk,
                    'noasset'    => $noasset,
                    'qty'    => $qty,
                    'keperluan'     => $keperluan,
                    'tujuan'     => $tujuan,
                    'periode'     => $periode,
                    'periodemulai'    => $periodemulai,
                    'periodeakhir' => $periodeakhir,
                    'picakun'=> $picakun,
                    'nmpic'       => $nmpic,
                    'jabatanpic'=> $jabatanpic,
                    'departemenpic'       => $departemenpic,
                    'tglpemeriksaan'         => $tglpemeriksaan,
                    'dataperangkat'    => $dataperangkat,
                    'enkripsidata'    => $enkripsidata,
                    'kondisi'    => $kondisi,
                    'tglpenyerahan'    => $tglpenyerahan,
                    'tglpengembalian'    => $tglpengembalian,
                    'pembaruanizin'    => $pembaruanizin,
                    'alasanterlambat'    => $alasanterlambat,
                    'keteranganlain'    => $keteranganlain,

                    // 'description'   => $description,
                    'status'        => 'I',
                    'inputby'       => $inputby,
                    'inputdate'     => $inputdate,

                    // 'ijinmasuk' => $ijinmasuk === 'YA'
                    //     ? true
                    //     : ($ijinmasuk === 'TIDAK' ? false : null),
                    // 'alasantidakmasuk '  =>  $alasantidakmasuk,

                    // 'ijinkeluar' => $ijinkeluar === 'YA'
                    //     ? true
                    //     : ($ijinkeluar === 'TIDAK' ? false : null),
                    // // 'ijinkeluar'  =>  $ijinkeluar,
                    // 'alasantidakkeluar '  =>  $alasantidakkeluar,

                );
                if ($builder->insert($info)) {
                    $inx = array('status' => 'F');
                    $builder->where('docno',$nama);
                    $builder->update($inx);
                    $perangkatRow = $builder
                        ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                        ->get()
                        ->getRowArray();
                    if ($perangkatRow) {
                        $docnoFinal   = trim($perangkatRow['docno']);
                        $url = array(
                            'barcodelink' => base_url('form/pa/getDataFormPerangkatKeluar/' . $this->fiky_encryption->sealed($docnoFinal)),
                        );
                        $builder->where('TRIM(docno)', $docnoFinal);
                        $builder->update($url);

                    }

                    $paramerror=" and userid='$nama' and modul='I.N.A.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtlerror['nomorakhir1']);
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and docno='$docno'";
                $check = $this->m_form->q_trxperangkatkeluar($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    // $status = null;

                    // if ($ijinmasuk !== null) {
                    //     // $status = ($ijinmasuk === 'YA') ? 'M' : 'TM';
                    // } elseif ($ijinkeluar !== null) {
                    //     // $status = ($ijinkeluar === 'YA') ? 'K' : 'TK';
                    // }

                    $info = array(
                        // 'docno'         => $docno,
                        'docno'         => $docno,
                        'nmpemohon'     => $nmpemohon,
                        // 'deptpic'       => $deptpic,
                        'departmen'       => $departmen,
                        'jabatan'       => $jabatan,
                        'docdate'       => $docdate,
                        'jnsform'       => $jnsform,
                        'osticket'     => $osticket,
                        // 'idsticker'      => $idsticker,
                        'jnsperangkat'      => $jnsperangkat,
                        'otherfield'    => $otherField,
                        'merk'    => $merk,
                        'noasset'    => $noasset,
                        'qty'    => $qty,
                        'keperluan'     => $keperluan,
                        'tujuan'     => $tujuan,
                        'periode'     => $periode,
                        'periodemulai'    => $periodemulai,
                        'periodeakhir' => $periodeakhir,
                        'picakun'=> $picakun,
                        'nmpic'       => $nmpic,
                        'jabatanpic'=> $jabatanpic,
                        'departemenpic'       => $departemenpic,

                        'tglpemeriksaan'         => $tglpemeriksaan,
                        'dataperangkat'    => $dataperangkat,
                        'enkripsidata'    => $enkripsidata,
                        'kondisi'    => $kondisi,
                        'tglpenyerahan'    => $tglpenyerahan,

                        'tglpengembalian'    => $tglpengembalian,
                        'pembaruanizin'    => $pembaruanizin,
                        'alasanterlambat'    => $alasanterlambat,
                        'keteranganlain'    => $keteranganlain,
                        // 'description'   => $description,
                        'updateby'       => $inputby,
                        'updatedate'     => $inputdate,
                        // 'barcodelink'   => ,

                        // 'ijinmasuk' => $ijinmasuk === 'YA'
                        //     ? true
                        //     : ($ijinmasuk === 'TIDAK' ? false : null),
                        // 'alasantidakmasuk '  =>  $alasantidakmasuk,

                        // 'ijinkeluar' => $ijinkeluar === 'YA'
                        //     ? true
                        //     : ($ijinkeluar === 'TIDAK' ? false : null),
                        // 'alasantidakkeluar '  =>  $alasantidakkeluar,
                        // Hanya update status kalau ada nilainya
                        // 'filedoc' => $filedoc,
                        // 'typecapex' =>  trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $typecapex : null ,
                        // 'accno' => trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $accno : null
                    );


                    // if ($status !== null) {
                    //     $info['status'] = $status;
                    // }

                    $builder->where('docno', $docno);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$docno);
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

                $builder = $this->db->table('sc_trx.formperangkatkeluar');
                $builder->where('docno' , $docno);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$docno);
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



    public function updateIzinMasukPK()
    {
        $docno = trim($this->request->getPost('docno'));
        $ijinmasuk = $this->request->getPost('ijinmasuk') === 'true' ? true : false;
        $alasan = $this->request->getPost('alasantidakmasuk');

        $status = $ijinmasuk ? 'M' : 'TM'; // M = masuk, TM = tidak masuk

        // Enkripsi parameter
        $enc_param = $this->fiky_encryption->sealed($docno);
        $link      =  $ijinmasuk ? base_url("form/pa/getDataFormPerangkat?enc_param={$enc_param}") : null;

        // Update data
        $this->db->table('sc_trx.formperangkat')
            ->where('TRIM(docno)', $docno, false) // false biar TRIM tidak di-escape
            ->update([
                'ijinmasuk'        => $ijinmasuk,
                'alasantidakmasuk' => $ijinmasuk ? null : strtoupper(trim($alasan)),
                'status'           => $status,
                'barcodelink'      => $link
            ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function updateIzinKeluarPK()
    {
        $docno = trim($this->request->getPost('docno'));
        $ijinkeluar = $this->request->getPost('ijinkeluar') === 'true' ? true : false;
        $alasan = $this->request->getPost('alasantidakkeluar');

        $status = $ijinkeluar ? 'K' : 'TK'; // K = keluar, TK = tidak keluar

        $this->db->table('sc_trx.formperangkat')
            ->where('trim(docno)', trim($docno))
            ->update([
                'ijinkeluar' => $ijinkeluar,
                'alasantidakkeluar' => $ijinkeluar ? null : strtoupper(trim($alasan)),
                'status' => $status
            ]);

        return $this->response->setJSON(['success' => true]);
    }

    // public function getDataFormPerangkat()
    // {
    //     $param = $this->request->getGet('enc_param');
    //     $docno = trim($this->fiky_encryption->unseal($param));

    //     // Lanjutkan query atau proses berdasarkan $docno...
    // }




    public function getDataFormPerangkatKeluar($encryptedDocno)
    {
        $docno = trim($this->fiky_encryption->unseal($encryptedDocno));

        // $model = new FormPerangkatModel();
        // $param = " and docno='$docno'";
        $builder = $this->db->table('sc_trx.formperangkatkeluar a');

        $builder->select('a.*, z.uraian as nmstatus');
        $builder->join('sc_mst.trxtype z', "a.status = z.kdtrx AND z.jenistrx = 'I.N.A.3'", 'left');
        $builder->where('a.docno', $docno);

        $data['form'] = $builder->get()->getRow();
        // $data['form'] = $this->m_from->where('docno', $docno)->first();

        if (!$data['form']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data tidak ditemukan");
        }

        // Tentukan badgeClass
        $badgeClass = '';
        switch (trim($data['form']->nmstatus ?? '')) {
            case 'OPEN':
                $badgeClass = 'badge-success';
                break;
            case 'CLOSE':
                $badgeClass = 'badge-danger';
                break;
            default:
                $badgeClass = 'badge-secondary';
        }

        $data['badgeClass'] = $badgeClass;
        $data['status'] = $data['form']->nmstatus ?? 'Tidak diketahui';

        return view('form/form_perangkatkeluar_view', $data);
    }



    public function updateStatusPK()
    {
        $docno = $this->request->getPost('docno');
        $status = $this->request->getPost('status');
        $barcodelink = base_url('form/pa/getDataFormPerangkatKeluar/' . $this->fiky_encryption->sealed($docno));
        if (!$docno || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_trx.formperangkatkeluar');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status, 'barcodelink'=>$barcodelink);
        $update = $builder->update($info);

        if ($update) {

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function print_perangkatkeluar(){
        $nama = trim($this->session->get('nama'));
        $enc_docno = $this->request->getGet('id');
        $docno = hex2bin(trim($this->request->getGet('id')));

        $title = " Cetak Form Perangkat IT Keluar Perusahaan ";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_form->q_trxperangkatkeluar($param)->getRowArray();

        if (in_array(trim($dtl['status']),array('CN'))) {
            //INSERT TRX ERROR
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid', $nama);
            $builder_trxerror->where('modul', 'I.N.A.3');
            $builder_trxerror->delete();

            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => 0,
                'modul' => 'I.N.A.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('form/pa/laptop'));
        } else {
            $datajson =  base_url("form/pa/api_print_perangkatkeluar/?enc_docno=$enc_docno") ;
            // if (trim($dtl['kodeform'])==='JTS-08-16A') {
            //     $datamrt =  base_url("assets/mrt/capex_a_baru.mrt") ;
            // } else {
            $datamrt =  base_url("assets/mrt/form_membawa_perangkat_it_keluar.mrt") ;
            // }

            return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
        }

    }

    function api_print_perangkatkeluar(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim(hex2bin($this->request->getGet('enc_docno')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $param=" and docno='$docno'";
        $databranch = $this->m_global->q_master_branch();
        //$datamst = $this->m_bbm->q_trx_item_bbm_mst($param);
        $datadtl = $this->m_form->q_trxperangkatkeluar($param);
        $tampungdtl = $datadtl->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {

            $detail->idsticker = isset($detail->idsticker) ? trim($detail->idsticker) : '';
            $detail->osticket = isset($detail->osticket) ? trim($detail->osticket) : '';
            $detail->barcodelink = isset($detail->barcodelink) ? trim($detail->barcodelink) : '';
            $detail->docno = isset($detail->docno) ? trim($detail->docno) : '';
            $detail->nmpemohon = isset($detail->nmpemohon) ? trim($detail->nmpemohon) : '';
            $detail->departmen = isset($detail->departmen) ? trim($detail->departmen) : '';
            $detail->jabatan = isset($detail->jabatan) ? trim($detail->jabatan) : '';

            // Ambil nama departemen
            if (!empty($detail->departmen)) {
                $dept = $this->db->table('sc_mst.departmen')
                    ->select('nmdept')
                    ->where('TRIM(kddept)', trim($detail->departmen))
                    ->get()
                    ->getRow();
                $detail->nmdepartmen = $dept ? trim($dept->nmdept) : '';
            } else {
                $detail->nmdepartmen = '';
            }

            // Ambil nama jabatan
            if (!empty($detail->jabatan)) {
                $jab = $this->db->table('sc_mst.lvljabatan')
                    ->select('nmlvljabatan')
                    ->where('TRIM(kdlvl)', trim($detail->jabatan))
                    ->get()
                    ->getRow();
                $detail->nmjabatan = $jab ? trim($jab->nmlvljabatan) : '';
            } else {
                $detail->nmjabatan = '';
            }

            $detail->docdate = isset($detail->docdate) ? trim($detail->docdate) : '';
            $detail->jnsform = isset($detail->jnsform) ? trim($detail->jnsform) : '';
            $jnsform = isset($detail->jnsform) ? trim($detail->jnsform) : '';
            $detail->isBaru = false; // Default value
            if ($jnsform === 'BARU') {
                $detail->isBaru = true;
            }

            $detail->isPerpanjangan = false; // Default value
            if ($jnsform === 'PERPANJANGAN') {
                $detail->isPerpanjangan = true;
            }

            $status = isset($detail->status) ? trim($detail->status) : '';
            if ($status) {
                $detail->status = $status;
            }

            $nmstatus = isset($detail->nmstatus) ? trim($detail->nmstatus) : '';
            if ($nmstatus) {
                $detail->nmstatus = $nmstatus;
            }

            if($status){
                $detail->docnoEncrypt = $this->fiky_encryption->sealed(trim($detail->docno));
            }
            // Pastikan jsnperangkat ada dan di-trim
            $jsnperangkat = isset($detail->jsnperangkat) ? trim($detail->jsnperangkat) : '';
            // Tambahkan properti baru isLaptop
            $detail->isLaptop = false; // Default value
            if ($jsnperangkat === 'LAPTOP') {
                $detail->isLaptop = true;
            }

            // Tambahkan properti baru isDesktop
            $detail->isDesktop = false; // Default value
            if ($jsnperangkat === 'DESKTOP') {
                $detail->isDesktop = true;
            }

            // Tambahkan properti baru isTablet
            $detail->isTablet = false; // Default value
            if ($jsnperangkat === 'TABLET') {
                $detail->isTablet = true;
            }

            // Tambahkan properti baru isPrinter
            $detail->isPrinter = false; // Default value
            if ($jsnperangkat === 'PRINTER') {
                $detail->isPrinter = true;
            }

            // Tambahkan properti baru isScanner
            $detail->isScanner = false; // Default value
            if ($jsnperangkat === 'SCANNER') {
                $detail->isScanner = true;
            }

            // Tambahkan properti baru isOther
            $detail->isOther = false; // Default value
            if ($jsnperangkat === 'OTHER') {
                $detail->isOther = true;
            }

            $detail->otherField = isset($detail->otherfield) ? trim($detail->otherfield) : '';
            $detail->merk = isset($detail->merk) ? trim($detail->merk) : '';
            $detail->noasset = isset($detail->noasset) ? trim($detail->noasset) : '';
            $detail->qty = isset($detail->qty) ? trim($detail->qty) : '';
            $detail->keperluan = isset($detail->keperluan) ? trim($detail->keperluan) : '';
            $detail->tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
            $detail->periode = isset($detail->periode) ? trim($detail->periode) : '';
            $periode = isset($detail->periode) ? trim($detail->periode) : '';
            $detail->isRutin = false; // Default value
            if ($periode === 'RUTIN') {
                $detail->isRutin = true;
            }

            $detail->isTertentu = false; // Default value
            if ($periode === 'TERTENTU') {
                $detail->isTertentu = true;
            }
            $detail->periodemulai = isset($detail->periodemulai) ? trim($detail->periodemulai) : '';
            $detail->periodeakhir = isset($detail->periodeakhir) ? trim($detail->periodeakhir) : '';

            // Pastikan picakun ada dan di-trim
            $picakun = isset($detail->picakun) ? trim($detail->picakun) : '';
            $detail->isPemohon = false; // Default value
            if ($picakun === 'PEMOHON') {
                $detail->isPemohon = true;
            }

            $detail->isKaryawanLain = false; // Default value
            if ($picakun === 'OTHER') {
                $detail->isKaryawanLain = true;
            }

            $detail->nmpic = isset($detail->nmpic) ? trim($detail->nmpic) : '';
            $detail->jabatanpic = isset($detail->jabatanpic) ? trim($detail->jabatanpic) : '';
            $detail->departemenpic = isset($detail->departemenpic) ? trim($detail->departemenpic) : '';



            $detail->tglpemeriksaan = isset($detail->tglpemeriksaan) ? trim($detail->tglpemeriksaan) : '';
            $detail->dataperangkat = isset($detail->dataperangkat) ? trim($detail->dataperangkat) : '';
            $detail->enkripsidata = isset($detail->enkripsidata) ? trim($detail->enkripsidata) : '';
            $enkripsidata = isset($detail->enkripsidata) ? trim($detail->enkripsidata) : '';
            $detail->isEnkripsi = false;
            $detail->isTidakEnkripsi = false;
            if ($enkripsidata === 't') {
                $detail->isEnkripsi = true;
            } elseif ($enkripsidata === 'f') {
                $detail->isTidakEnkripsi = true;
            }
            $detail->kondisi = isset($detail->kondisi) ? trim($detail->kondisi) : '';
            $detail->tglpenyerahan = isset($detail->tglpenyerahan) ? trim($detail->tglpenyerahan) : '';

            $detail->tglpengembalian = isset($detail->tglpengembalian) ? trim($detail->tglpengembalian) : '';
            $detail->pembaruanizin = isset($detail->pembaruanizin) ? trim($detail->pembaruanizin) : '';
            $pembaruanizin = isset($detail->pembaruanizin) ? trim($detail->pembaruanizin) : '';
            $detail->isPembaruan = false; // Default value
            if ($pembaruanizin === 'YA') {
                $detail->isPembaruan = true;
            }

            $detail->isTidakPembaruan = false; // Default value
            if ($pembaruanizin === 'TIDAK') {
                $detail->isTidakPembaruan = true;
            }
            $detail->alasanterlambat = isset($detail->alasanterlambat) ? trim($detail->periodeakhir) : '';
            $detail->keteranganlain = isset($detail->keteranganlain) ? trim($detail->keteranganlain) : '';



        }

        // Ambil status CAPEX berdasarkan docno
        $builderx = $this->db->table("sc_trx.formperangkatkeluar");
        $perangkatCek = $builderx->select('status,printcount')->where('docno', $docno)->get()->getRow();

        // Cek apakah status bukan 'FC'
        if ($perangkatCek) {
            $currentCount = is_numeric($perangkatCek->printcount) ? (int)$perangkatCek->printcount : 0;


            $nfo = array(
                // 'status' => 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s'),
                'printcount' => $currentCount + 1,
                // 'updated_at' => date('Y-m-d H:i:s')

            );

            // Update status menjadi 'P' jika bukan 'FC'
            $builderx->where('docno', $docno)->update($nfo);
        }

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                //'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }




    /* FORM PERMINTAAN HARDWARE & SOFTWARE*/

    /* FORM PERMINTAAN HARDWARE & SOFTWARE*/

    public function pengadaan_hwsw()
    {
        $data['title']="LIST PERMINTAAN HARDWARE SOFTWARE";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.10'; $versirelease='I.N.A.10/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.10'";
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
        return $this->template->render('form/v_list_pengadaan_hwsw',$data);
    }

    function list_pengadaan_hwsw(){
        $list = $this->m_form->get_t_pengadaan_hwsw_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<div class="btn-group">
                            <button type="button" class="btn btn-info  dropdown-toggle text-white"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-gear"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="editPengadaanHwSw('."'".trim($lm->docno)."'".');"><i class="fa fa-edit"></i> Edit</a>
                                <a class="dropdown-item"  
                                    href="' . base_url('form/pa/print_pengadaan_hwsw') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                                    onclick="return confirm(' . "'" . 'Print This Form Data : ' . trim($lm->docno) . "'" . ')">
                                    <i class="fa fa-print"></i> Cetak
                                </a>
                            </div>
                        </div>';
            $row[] = $lm->docno;
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'OUTSTANDING PURCHASING':
                    $badgeClass = 'badge-warning';
                    break;
                case 'FA CONTROLLER':
                    $badgeClass = 'badge-primary';
                    break;
                case 'FINAL USER':
                    $badgeClass = 'badge-info';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-orange ';
                    break;
                case 'CANCEL':
                    $badgeClass = 'badge-danger';
                    break;
                case 'FINAL CAPEX':
                    $badgeClass = 'badge-success';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';
            $row[] = $lm->namapic;
            $row[] = $lm->departemen;
            $row[] = $lm->nmbarang;
            $row[] = $lm->jnsbarang;
            $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
                . htmlspecialchars($lm->tujuan, ENT_QUOTES, 'UTF-8') .
                '</div>';
            $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
                . htmlspecialchars($lm->spesifikasi, ENT_QUOTES, 'UTF-8') .
                '</div>';
            $row[] = $lm->inputdate;
            // $row[] = $lm->inputdate1;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_form->t_pengadaan_hwsw_view_count_all(),
            "recordsFiltered" => $this->m_form->t_pengadaan_hwsw_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function input_pengadaan_hwsw(){
        $data['title']="INPUT DATA PERANGKAT KELUAR PERUSAHAAN";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.10'; $versirelease='I.N.A.10/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.10'";
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

        $data['back_url'] = base_url("form/pa/pengadaan_hwsw");
        $data['typeform'] = 'INPUT';
        $data['docno'] = $nama;
        $data['status'] = '';

        $kmenu = 'I.N.A.10';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();


        $data['id']='';
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_pengadaan_hwsw',$data);
    }

    function edit_pengadaan_hwsw(){

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.10'; $versirelease='I.N.A.10/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.10'";
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
        $data['title']="EDIT DATA FORM PENGADAAN HARDWARE DAN SOFTWARE";
        $var = trim($this->request->getGet('var'));
        $data['type']="UPDATE";
        $data['nama']=$nama;
        $data['id']=$var;

        $docno = hex2bin($this->request->getGet('id'));
        $data['typeform'] = 'UPDATE';
        $data['back_url'] = base_url("form/pa/pengadaan_hwsw");

        $data['docno'] = $docno;
        $builder = $this->db->table('sc_trx.formrequest_it');
        $builder->select('trx.uraian as status_description') // Ambil uraian dari trxtype
        ->join('sc_mst.trxtype as trx', 'TRIM(sc_trx.formrequest_it.status) = TRIM(trx.kdtrx) AND trx.jenistrx = \'I.N.A.10\'', 'left')
            ->where('TRIM(sc_trx.formrequest_it.docno)', trim($docno));

        $result = $builder->get();
        $row = $result->getRow();

        $data['status'] = $row ? $row->status_description : '';
        $kmenu = 'I.N.A.10';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();



        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_input_pengadaan_hwsw',$data);
    }


    function showDetailReq () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $var = $this->request->getGet('var');
        $nik = $this->request->getGet('nik');

        $param = " and docno='$var'";
        $dtl = $this->m_form->q_trxpengadaan_hwsw($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }


    function saveDataPengadaanHwsw(){
        $nama = trim($this->session->get('nama'));
        // $request_body = file_get_contents('php://input');
        // $data = json_decode($request_body);
        $data = json_decode($this->request->getPost('data'));

        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $docno          = trim($dataprocess->docno);
            $namapic      = strtoupper(trim($dataprocess->namapic));
            // $deptpic     = trim($dataprocess->deptpic);
            $departemen       = strtoupper(trim($dataprocess->departemen));
            $nmbarang       = strtoupper(trim($dataprocess->nmbarang));
            $docdate        = date('Y-m-d', strtotime(trim($dataprocess->docdate)));
            // $osticket        = trim($dataprocess->osticket);
            // $jnsform        = trim($dataprocess->jnsform);

            $jumlah      = strtoupper(trim($dataprocess->jumlah));
            $estimasi   = strtoupper(trim($dataprocess->estimasi));
            // $otherField     = ($dataprocess->otherField) ? strtoupper(trim($dataprocess->otherField)) : null;
            $jnsbarang    = strtoupper(trim($dataprocess->jnsbarang));
            $tujuan           = strtoupper(trim($dataprocess->tujuan));
            $frekuensi        = strtoupper(trim($dataprocess->frekuensi));
            $jnspengadaan         = strtoupper(trim($dataprocess->jnspengadaan));
            // $tujuan         = strtoupper(trim($dataprocess->tujuan));
            $klasifikasi         = strtoupper(trim($dataprocess->klasifikasi));
            // $periodemulai   = ($dataprocess->periodemulai) ? date('Y-m-d', strtotime(trim($dataprocess->periodemulai))) : null;
            // $periodeakhir   = ($dataprocess->periodeakhir) ? date('Y-m-d', strtotime(trim($dataprocess->periodeakhir))) : null;
            $spesifikasi         = strtoupper(trim($dataprocess->spesifikasi));
            $lampiran         = strtoupper(trim($dataprocess->lampiran));
            $setuju  = strtoupper(trim($dataprocess->setuju));
            $alasanreject          = strtoupper(trim($dataprocess->alasanreject));

            // $tglpemeriksaan   = date('Y-m-d', strtotime(trim($dataprocess->tglpemeriksaan)));
            // $dataperangkat           = strtoupper(trim($dataprocess->dataperangkat));
            // $enkripsidata           = strtoupper(trim($dataprocess->enkripsidata));
            // $kondisi           = strtoupper(trim($dataprocess->kondisi));
            // $tglpenyerahan   = date('Y-m-d', strtotime(trim($dataprocess->tglpenyerahan)));

            // $tglpengembalian   = date('Y-m-d', strtotime(trim($dataprocess->tglpengembalian)));
            // $pembaruanizin           = strtoupper(trim($dataprocess->pembaruanizin));


            // $alasanterlambat    = ($dataprocess->alasanterlambat) ? strtoupper(trim($dataprocess->alasanterlambat)) : null;
            // $keteranganlain           = strtoupper(trim($dataprocess->keteranganlain));
            // $description    = strtoupper(trim($dataprocess->description));

            // $ijinmasuk           = strtoupper(trim($dataprocess->ijinmasuk));
            // $alasantidakmasuk           = strtoupper(trim($dataprocess->alasantidakmasuk));
            // $ijinkeluar           = strtoupper(trim($dataprocess->ijinkeluar));


            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $type = trim($dataprocess->type);
            $builder = $this->db->table('sc_trx.formrequest_it');
            if ($type === 'INPUT') {
                $info = array(
                    'docno'         => $docno,
                    'namapic'     => $namapic,
                    // 'deptpic'       => $deptpic,
                    'departemen'       => $departemen,
                    'nmbarang'       => $nmbarang,
                    'docdate'       => $docdate,
                    'jumlah'       => $jumlah,
                    'estimasi'     => $estimasi,
                    // 'idsticker'      => $idsticker,
                    'jnsbarang'      => $jnsbarang,
                    // 'otherfield'    => $otherField,
                    'tujuan'    => $tujuan,
                    'frekuensi'    => $frekuensi,
                    'jnspengadaan'    => $jnspengadaan,
                    'klasifikasi'     => $klasifikasi,
                    'spesifikasi'     => $spesifikasi,
                    'lampiran'     => $lampiran,
                    // 'urlfoto'    => $urlfoto,
                    'alasanreject' => $alasanreject,
                    'setuju'        => $setuju,
                    // 'description'   => $description,
                    'status'        => 'I',
                    'inputby'       => $inputby,
                    'inputdate'     => $inputdate,

                    // 'ijinmasuk' => $ijinmasuk === 'YA'
                    //     ? true
                    //     : ($ijinmasuk === 'TIDAK' ? false : null),
                    // 'alasantidakmasuk '  =>  $alasantidakmasuk,

                    // 'ijinkeluar' => $ijinkeluar === 'YA'
                    //     ? true
                    //     : ($ijinkeluar === 'TIDAK' ? false : null),
                    // // 'ijinkeluar'  =>  $ijinkeluar,
                    // 'alasantidakkeluar '  =>  $alasantidakkeluar,

                );
                if ($builder->insert($info)) {
                    $inx = array('status' => 'F');
                    $builder->where('docno',$nama);
                    $builder->update($inx);
                    $perangkatRow = $builder
                        ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                        ->get()
                        ->getRowArray();
                    if ($perangkatRow) {
                        $docnoFinal   = trim($perangkatRow['docno']);
                        $url = array(
                            'barcodelink' => base_url('form/pa/getDataFormRequestIT/' . $this->fiky_encryption->sealed($docnoFinal)),
                        );
                        $builder->where('TRIM(docno)', $docnoFinal);
                        $builder->update($url);

                    }

                    $paramerror=" and userid='$nama' and modul='I.N.A.10'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$dtlerror['nomorakhir1']);
                    echo json_encode($getResult);

                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }

            } else if ($type==='UPDATE') {
                $param = " and docno='$docno'";
                $check = $this->m_form->q_trxpengadaan_hwsw($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    // $status = null;

                    // if ($ijinmasuk !== null) {
                    //     // $status = ($ijinmasuk === 'YA') ? 'M' : 'TM';
                    // } elseif ($ijinkeluar !== null) {
                    //     // $status = ($ijinkeluar === 'YA') ? 'K' : 'TK';
                    // }

                    $info = array(
                        // 'docno'         => $docno,
                        'docno'         => $docno,
                        'namapic'     => $namapic,
                        // 'deptpic'       => $deptpic,
                        'departemen'       => $departemen,
                        'nmbarang'       => $nmbarang,
                        'docdate'       => $docdate,
                        'jumlah'       => $jumlah,
                        'estimasi'     => $estimasi,
                        // 'idsticker'      => $idsticker,
                        'jnsbarang'      => $jnsbarang,
                        // 'otherfield'    => $otherField,
                        'tujuan'    => $tujuan,
                        'setuju'        => $setuju,
                        'frekuensi'    => $frekuensi,
                        'jnspengadaan'    => $jnspengadaan,
                        'klasifikasi'     => $klasifikasi,
                        'spesifikasi'     => $spesifikasi,
                        'lampiran'     => $lampiran,
                        // 'urlfoto'    => $urlfoto,
                        'alasanreject' => $alasanreject,
                        // 'description'   => $description,
                        'updateby'       => $inputby,
                        'updatedate'     => $inputdate,
                        // 'barcodelink'   => ,

                        // 'ijinmasuk' => $ijinmasuk === 'YA'
                        //     ? true
                        //     : ($ijinmasuk === 'TIDAK' ? false : null),
                        // 'alasantidakmasuk '  =>  $alasantidakmasuk,

                        // 'ijinkeluar' => $ijinkeluar === 'YA'
                        //     ? true
                        //     : ($ijinkeluar === 'TIDAK' ? false : null),
                        // 'alasantidakkeluar '  =>  $alasantidakkeluar,
                        // Hanya update status kalau ada nilainya
                        // 'filedoc' => $filedoc,
                        // 'typecapex' =>  trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $typecapex : null ,
                        // 'accno' => trim($dataanu['userinfo']['bagian']) == 'IT'||  trim($dataanu['userinfo']['bagian']) == 'FA' ? $accno : null
                    );


                    // if ($status !== null) {
                    //     $info['status'] = $status;
                    // }

                    $builder->where('docno', $docno);
                    if ($builder->update($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$docno);
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

                $builder = $this->db->table('sc_trx.formrequest_it');
                $builder->where('docno' , $docno);
                if ($builder->delete()) {
                    $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: '.$docno);
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


    function print_pengadaan_hwsw(){
        $nama = trim($this->session->get('nama'));
        $enc_docno = $this->request->getGet('id');
        $docno = hex2bin(trim($this->request->getGet('id')));

        $title = " Cetak Form Pengadaan Hardware & Software";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_form->q_trxpengadaan_hwsw($param)->getRowArray();

        if (in_array(trim($dtl['status']),array('CN'))) {
            //INSERT TRX ERROR
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid', $nama);
            $builder_trxerror->where('modul', 'I.N.A.10');
            $builder_trxerror->delete();

            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => 0,
                'modul' => 'I.N.A.10',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('form/pa/pengadaan_hwsw'));
        } else {
            $datajson =  base_url("form/pa/api_print_pengadaan_hwsw/?enc_docno=$enc_docno") ;
            // if (trim($dtl['kodeform'])==='JTS-08-16A') {
            //     $datamrt =  base_url("assets/mrt/capex_a_baru.mrt") ;
            // } else {
            $datamrt =  base_url("assets/mrt/pengadaan_hwsw.mrt") ;
            // }

            return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
        }

    }

    function api_print_pengadaan_hwsw(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim(hex2bin($this->request->getGet('enc_docno')));
        //$docno=trim($this->request->getGet('enc_docno'));

        $param=" and docno='$docno'";
        $databranch = $this->m_global->q_master_branch();
        //$datamst = $this->m_bbm->q_trx_item_bbm_mst($param);
        $datadtl = $this->m_form->q_trxpengadaan_hwsw($param);
        $tampungdtl = $datadtl->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {

            $detail->estimasi = isset($detail->estimasi) ? trim($detail->estimasi) : '';
            $detail->jumlah = isset($detail->jumlah) ? trim($detail->jumlah) : '';
            $detail->barcodelink = isset($detail->barcodelink) ? trim($detail->barcodelink) : '';
            $detail->docno = isset($detail->docno) ? trim($detail->docno) : '';
            $detail->namapic = isset($detail->namapic) ? trim($detail->namapic) : '';
            $detail->departemen = isset($detail->departemen) ? trim($detail->departemen) : '';
            $detail->nmbarang = isset($detail->nmbarang) ? trim($detail->nmbarang) : '';
            $detail->docdate = isset($detail->docdate) ? trim($detail->docdate) : '';
            $detail->jnsbarang = isset($detail->jnsbarang) ? trim($detail->jnsbarang) : '';
            $jnsbarang = isset($detail->jnsbarang) ? trim($detail->jnsbarang) : '';
            $detail->isHardware = false; // Default value
            if ($jnsbarang === 'HARDWARE') {
                $detail->isHardware = true;
            }

            $detail->isSoftware = false; // Default value
            if ($jnsbarang === 'SOFTWARE') {
                $detail->isSoftware = true;
            }

            $detail->isSparepart = false; // Default value
            if ($jnsbarang === 'SPAREPART') {
                $detail->isSparepart = true;
            }

            $detail->isConsumable = false; // Default value
            if ($jnsbarang === 'CONSUMABLE') {
                $detail->isConsumable = true;
            }

            $status = isset($detail->status) ? trim($detail->status) : '';
            if ($status) {
                $detail->status = $status;
            }

            $nmstatus = isset($detail->nmstatus) ? trim($detail->nmstatus) : '';
            if ($nmstatus) {
                $detail->nmstatus = $nmstatus;
            }

            if($status){
                $detail->docnoEncrypt = $this->fiky_encryption->sealed(trim($detail->docno));
            }


            $detail->tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
            $detail->frekuensi = isset($detail->frekuensi) ? trim($detail->frekuensi) : '';
            $detail->spesifikasi = isset($detail->spesifikasi) ? trim($detail->spesifikasi) : '';
            // $detail->lampiran = isset($detail->lampiran) ? trim($detail->lampiran) : '';


            // Pastikan jnspengadaan ada dan di-trim
            $jnspengadaan = isset($detail->jnspengadaan) ? trim($detail->jnspengadaan) : '';
            // Tambahkan properti baru isBaru
            $detail->isBaru = false; // Default value
            if ($jnspengadaan === 'BARU') {
                $detail->isBaru = true;
            }

            // Tambahkan properti baru isPenggantian
            $detail->isPenggantian = false; // Default value
            if ($jnspengadaan === 'PENGGANTIAN') {
                $detail->isPenggantian = true;
            }

            // Pastikan klasifikasi ada dan di-trim
            $klasifikasi = isset($detail->klasifikasi) ? trim($detail->klasifikasi) : '';
            // Tambahkan properti baru isKlasifikasiPeripheral
            $detail->isKlasifikasiPeripheral = false; // Default value
            if ($klasifikasi === 'PERIPHERAL') {
                $detail->isKlasifikasiPeripheral = true;
            }

            // Tambahkan properti baru isKlasifikasiSpare
            $detail->isKlasifikasiSpare = false; // Default value
            if ($klasifikasi === 'SPAREPART') {
                $detail->isKlasifikasiSpare = true;
            }

            // Tambahkan properti baru isKlasifikasiConsumable
            $detail->isKlasifikasiConsumable = false; // Default value
            if ($klasifikasi === 'CONSUMABLE') {
                $detail->isKlasifikasiConsumable = true;
            }


            // Pastikan lampiran ada dan di-trim
            $lampiran = isset($detail->lampiran) ? trim($detail->lampiran) : '';
            // Tambahkan properti baru isBooklet
            $detail->isBooklet = false; // Default value
            if ($lampiran === 'BOOKLET') {
                $detail->isBooklet = true;
            }

            // Tambahkan properti baru isFoto
            $detail->isFoto = false; // Default value
            if ($lampiran === 'FOTO') {
                $detail->isFoto = true;
            }

            // Tambahkan properti baru isOther
            $detail->isOther = false; // Default value
            if ($lampiran === 'OTHERS') {
                $detail->isOther = true;
            }


            $detail->setuju = isset($detail->setuju) ? trim($detail->setuju) : '';
            $setuju = isset($detail->setuju) ? trim($detail->setuju) : '';
            $detail->isSetuju = false; // Default value
            if ($setuju === 'YA') {
                $detail->isSetuju = true;
            }

            $detail->isTidakSetuju = false; // Default value
            if ($setuju === 'TIDAK') {
                $detail->isTidakSetuju = true;
            }
            $detail->alasanreject = isset($detail->alasanreject) ? trim($detail->alasanreject) : '';


        }

        // Ambil status CAPEX berdasarkan docno
        $builderx = $this->db->table("sc_trx.formrequest_it");
        $perangkatCek = $builderx->select('status,printcount')->where('docno', $docno)->get()->getRow();

        // Cek apakah status bukan 'FC'
        if ($perangkatCek) {
            $currentCount = is_numeric($perangkatCek->printcount) ? (int)$perangkatCek->printcount : 0;


            $nfo = array(
                // 'status' => 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s'),
                'printcount' => $currentCount + 1,
                // 'updated_at' => date('Y-m-d H:i:s')

            );

            // Update status menjadi 'P' jika bukan 'FC'
            $builderx->where('docno', $docno)->update($nfo);
        }

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                //'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    /* PRINT SECTION*/

    function print_pa_dept(){
        $nama = trim($this->session->get('nama'));
        $kddept=trim($this->request->getPost('kddept_filter'));

        $title = " Form Pengajuan Budget IT";
        if (!empty($kddept)) {
            $param = " and coalesce(docno,'')='$kddept'";
        } else {
            $param = "";
        }

        ///$dtl = $this->m_form->q_capital_pa($param)->getRowArray();

        $datajson =  base_url("capital/administration/api_print_capital_pa/?kddept=$kddept") ;
        $datamrt =  base_url("assets/mrt/form_pa_budget_processing_it.mrt") ;
        //$datamrt =  base_url("assets/mrt/form_pa_budget.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);

    }

    function print_pa_dept_get(){
        $nama = trim($this->session->get('nama'));
        $kddept=trim($this->session->get('kddept'));;

        $title = " Form Pengajuan Budget IT";
        if (!empty($kddept)) {
            $param = " and coalesce(docno,'')='$kddept'";
        } else {
            $param = "";
        }

        ///$dtl = $this->m_form->q_capital_pa($param)->getRowArray();

        $datajson =  base_url("capital/administration/api_print_capital_pa/?kddept=$kddept") ;
        $datamrt =  base_url("assets/mrt/form_pa_budget.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);

    }

    function api_print_capital_pa(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $kddept=trim($this->request->getGet('kddept'));

        if (!empty($kddept)) {
            $pdept = " and kddept='$kddept'";
            $param = " and status not in ('C','D','T') and coalesce(kddept,'')='$kddept'";
        } else {
            $pdept = " and kddept='UNKNOWN'";
            $param = " and status not in ('C','D','T')";
        }
        //$docno=trim($this->request->getGet('enc_docno'));

        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_form->q_departmen($pdept);
        $datadtl = $this->m_form->q_capital_pa($param);

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
                //'tglcetak' => date('d-m-Y H:i:s'),
            ), JSON_PRETTY_PRINT);
    }


    function saveReview(){
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $id = trim($this->request->getPost('id'));
        $cimage = $this->request->getPost('cimage');
        $cbase64 = $this->request->getPost('cimage_base64');
        $review = strtoupper(trim($this->request->getPost('review')));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $builder_capital_permintaan = $this->db->table('sc_trx.capital_permintaan');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        if (empty($cbase64)) {
            if ($type==='REVIEW'){
                $info = array(
                    'review' => $review,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder_capital_permintaan->where('id',$id);
                if ($builder_capital_permintaan->update($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.B.3');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.B.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }
        } else {
            $content = file_get_contents($cbase64);
            //$file = $this->request->getFile('cimage');
            $path = "./assets/img/capitalpa";
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            }

            $filename = trim(trim($id)).'.png';

            //$final_file_name = trim(trim($id)).'.png';
            $path = FCPATH.'assets/img/capitalpa';
            if(file_exists($path.$filename)){
                unlink($path.$filename);
            }
            $isifile = file_put_contents($path.'/'.trim(trim($id)).'.png', $content);

            if ($type==='REVIEW'){
                $info = array(
                    'review' => $review,
                    'cimage' => $filename,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );

                $builder_capital_permintaan->where('id',$id);
                if ($builder_capital_permintaan->update($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.B.3');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.B.3'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }
        }

    }

    /* PRINT LABEL UNTUK BARCODE LINK */

    function print_labels_form()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.N.A.11'; $versirelease='I.N.A.11/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.N.A.11'";
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
        //auto insert unitD:\XAMPP\htdocs\ci4_elite_starter\app\Views\form\v_print_labels_form.php
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_print_labels_form',$data);
    }


    function list_label_form_temporary(){
        $list = $this->m_form->get_query_print_label_form_temporary();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lm->docno;
            $row[] = $lm->ctype;
            $row[] = $lm->cname;
            $row[] = $lm->idbarcode;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_form->print_label_form_temporary_count_all(),
            "recordsFiltered" => $this->m_form->print_label_form_temporary_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function getDataFormLabel(){
        $nama = trim($this->session->get('nama'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $this->db->query("
delete from sc_tmp.print_label_form_temporary where inputby='$inputby';
insert into sc_tmp.print_label_form_temporary
(docno,cmodul,cgroup,ctype,cname,idbarcode,unit,subunit,inputby,inputdate,startdate,enddate)
select docno,cmodul,cgroup,ctype,cname,barcodelink,unit,subunit,inputby,inputdate::timestamp without time zone,startdate,enddate from (
select docno,'I.N.A.1' as cmodul,'IT' as cgroup,'FORM' as ctype,'IJIN PERANGKAT IT PRIBADI' as cname,barcodelink,'UNIT' as unit, '' as subunit, '$inputby' as inputby, '$inputdate' as inputdate,periodemulai as startdate,periodeakhir as enddate from sc_trx.formperangkat where docno is not null and status='O'
union all
select docno,'I.N.A.3' as cmodul,'IT' as cgroup,'FORM' as ctype,'IJIN PERANGKAT IT KELUAR' as cname,barcodelink,'UNIT' as unit, '' as subunit, '$inputby' as inputby, '$inputdate' as inputdate,periodemulai as startdate,periodeakhir as enddate from sc_trx.formperangkatkeluar where docno is not null  and status='O'
) as x

");

//        $builder = $this->db->table('sc_tmp.print_label_item_temporary');
//        if ($builder->insert($info)) {
        $result = array('status' => true, 'messages' => 'Insert Successfully');
        echo json_encode($result);

        //return redirect()->to(base_url('trans/tms/input_annualy'));
//        }
    }

    function clearTemporaryPrinter(){
        $idfromx = trim($this->request->getGet('var'));
        $nama = trim($this->session->get('nama'));
        $builder = $this->db->table('sc_tmp.print_label_item_temporary');

        $builder->where('docno' , $nama);
        if ($builder->delete()) {
            $result = array('status' => true, 'messages' => 'Clear Successfully');
            echo json_encode($result);
            return redirect()->to(base_url('form/pa/print_labels_form'));
        }
    }

    function printFromListPrinter(){
        $nama = trim($this->session->get('nama'));
        $idlocation = $this->request->getPost('idlocation');
        $idbarang = $this->request->getPost('idbarang');
        $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        $databranch = $this->m_global->q_master_branch();

        $idbarangxp=" and inputby='$nama'";

        $param = " ".$idbarangxp;
        $enc_param = $this->fiky_encryption->sealed($param);
        $title = " Barcode Label Data Item";

        $datajson =  base_url("form/pa/api_show_form_labels/?enc_param=$enc_param") ;
        $datamrt =  base_url("assets/mrt/label_perangkat_pribadi_v75.mrt") ;
        //$datamrt =  base_url("assets/mrt/showlabels_item.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_show_form_labels(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $param=trim($this->fiky_encryption->unseal($this->request->getGet('enc_param')));
        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_form->q_labeltemporary($param);

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




}
