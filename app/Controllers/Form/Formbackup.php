<?php

namespace App\Controllers\Form;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Formbackup extends BaseController
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
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('form/v_list_perangkat',$data);
    }

   function list_perangkat(){
        $list = $this->m_form->get_t_perangkat_view();
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
                                <a class="dropdown-item" href="#" onclick="editPerangkat('."'".trim($lm->docno)."'".');"><i class="fa fa-gear"></i> Update</a>
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
            $row[] = $lm->nmpemohon;
            $row[] = $lm->instansi;
            $row[] = $lm->telepon;
            $row[] = $lm->docdate;
            $row[] = $lm->jnsakses;
            $row[] = $lm->tujuan;

            // $row[] = $lm->nmstatus;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
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
        $data['type']="EDIT";
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

        $param = " and id='$var'";
        $dtl = $this->m_form->q_item_param($param);

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
            $docdate        = date('Y-m-d', strtotime(trim($dataprocess->docdate)));
            $telepon        = trim($dataprocess->telepon);
            $idsticker      = strtoupper(trim($dataprocess->idsticker));
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
                    'docdate'       => $docdate,
                    'telepon'     => $telepon,
                    'idsticker'      => $idsticker,
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
                    'inputdate'     => $inputdate
                );
                if ($builder->insert($info)) {
                    $inx = array('status' => 'F');
                    $builder->where('docno',$nama);
                    $builder->update($inx);

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
                $check = $this->m_form->q_trxinternet($param)->getNumRows();
                //check data ganda
                if ($check > 0) {
                    $info = array(
                        // 'docno'         => $docno,
                    'docno'         => $docno,
                    'nmpemohon'     => $nmpemohon,
                    // 'deptpic'       => $deptpic,
                    'instansi'       => $instansi,
                    'docdate'       => $docdate,
                    'telepon'     => $telepon,
                    'idsticker'      => $idsticker,
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
        $list = $this->m_form->get_t_internet_view();
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
                                <a class="dropdown-item" href="#" onclick="editInternet('."'".trim($lm->docno)."'".');"><i class="fa fa-gear"></i> Update </a>
                                <a class="dropdown-item" href="#" onclick="deleteInternet('."'".trim($lm->docno)."'".');" style="background-color: red; color: white;"><i class="fa fa-trash"></i> Delete </a>
                                <a class="dropdown-item" href="#" onclick="printInternet('."'".trim($lm->docno)."'".');"><i class="fa fa-print"></i> Print </a>
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
            $row[] = $lm->nmpemohon;
            $row[] = $lm->jabatan;
            $row[] = $lm->departmen;
            $row[] = $lm->docdate;
            $row[] = $lm->jnsakses;
            $row[] = $lm->keperluan;

            // $row[] = $lm->nmstatus;
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
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
        $data['title']="INPUT PERMINTAAN AKSES JARINGAN INTERNET";
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
        $data['title']="EDIT DATA MASTER ITEM";
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

            $jnsakses = trim($dataprocess->jnsakses);
            $emailField = ($dataprocess->emailField) ? strtolower($dataprocess->emailField) : null;
            $otherField = ($dataprocess->otherField) ? strtoupper($dataprocess->otherField) : null;
            $keperluan = strtoupper($dataprocess->keperluan);
            $sifatakses = trim($dataprocess->sifatakses);
            $waktuakses = trim($dataprocess->waktuakses);
            $tertentuField = ($dataprocess->tertentuField) ? strtoupper($dataprocess->tertentuField) : null;
            $otherWaktuField =  ($dataprocess->otherWaktuField) ? strtoupper($dataprocess->otherWaktuField) : null;
            $expdate = date('Y-m-d',strtotime($dataprocess->expdate));

            
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
            //isi qrcode jika di scan
            $codeContents = base_url()."/form/pa/verifikasi_internet?var=";
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

                    'jnsakses'      => $jnsakses,
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
                    'linkdefault'   => $codeContents,

                    'status'        => 'I',
                    'inputby'       => $inputby,
                    'inputdate'     => $inputdate
                );
                if ($builder->insert($info)) {
                    $inx = array('status' => 'F');
                    $builder->where('docno',$nama);
                    $builder->update($inx);

                    // Ambil ulang berdasarkan inputby + inputdate + status terbaru
                    // $capexRow = $builder
                    //     ->orderBy('docno', 'DESC') // pakai id jika ada, atau docdate
                    //     ->get()
                    //     ->getRowArray();
                    // if ($capexRow) {
                    //     $docnoFinal = trim($capexRow['docno']);

                    //     // Jika file upload valid, baru simpan & update
                    //     if ($ext) {
                    //         $filedoc = $docnoFinal . '_lampiran_penawaran.' . $ext;
                    //         $fileDest = $uploadPath . '/' . $filedoc;

                    //         if (!move_uploaded_file($_FILES['filedoc']['tmp_name'], $fileDest)) {
                    //             echo json_encode(['status' => false, 'messages' => 'Gagal menyimpan file upload.']);
                    //             return;
                    //         }
                    //         $ale = array('filedoc' => $filedoc);
                    //         $builder->where('TRIM(docno)', $docnoFinal);
                    //         $builder->update($ale);
                    //     }
                    // }

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

                        'jnsakses'      => $jnsakses,
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

    /* PRINT SECTION*/

    function print_internet(){
        $nama = trim($this->session->get('nama'));
        $docno=trim($this->request->getGet('var'));
        /* FORM IZIN INTERNET CETAK */
        $title = " Form izin akses internet";
        if (!empty($docno)) {
            $param = " and coalesce(docno,'')='$docno'";
        } else {
            $param = "";
        }

        $datajson =  base_url("form/pa/api_print_internet/?docno=$docno") ;
        $datamrt =  base_url("assets/mrt/form_permintaan_akses_jaringan_system.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);

    }

    function api_print_internet(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim($this->request->getGet('docno'));

        if (!empty($docno)) {
            $pdept = " and docno='$docno'";

        } else {
            $pdept = " and docno='UNKNOWN'";
        }

        $databranch = $this->m_global->q_master_branch();
        //$datamst = '';
        $datadtl = $this->m_form->q_trxinternet($pdept);

        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                //'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
                //'tglcetak' => date('d-m-Y H:i:s'),
            ), JSON_PRETTY_PRINT);
    }

    function verifikasi_internet(){
        $var = trim($this->request->getGet('var'));
        $datx = date('Y-m-d H:i:s');
        $nama = trim($this->session->get('nama'));
        $tmp['_ini_stylenya']=$this->default_style->_getCss();
        $tmp['_ini_jsnya']=$this->default_style->_getJs();
        //$param = " and docno_encrypt='$var' and coalesce(status,'')!='C' and coalesce(trim(attname),'') != ''";
        $param = " and docnoencrypt='$var'";
        $check = $this->m_form->q_trxinternet($param)->getNumRows();
        $tmp['dtl'] = $this->m_form->q_trxinternet($param)->getRowArray();
        //if ($check<=0 and trim($tmp['dtl']['status'])!=='P'){
        // abaikan sementara
        if (trim($tmp['dtl']['status'])!=='P'){
            throw PageNotFoundException::forPageNotFound();
        } else {
            //return $this->template->render('master/document/v_document_embed',$tmp);
            return view('master/document/v_verifikasi_internet',$tmp);
        }

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







}
