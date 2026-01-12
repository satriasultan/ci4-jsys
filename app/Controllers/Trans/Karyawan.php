<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Karyawan extends BaseController
{
    public function index()
    {
        $data['title']='Data Karyawan';

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */
        $data['message']='';
        $roleid = $this->session->get('roleid');
        $data['akses']=$this->m_global->list_akses_role($roleid)->getRowArray();
        return $this->template->render('trans/karyawan/v_karyawan',$data);
    }

    public function ajax_list()
    {
        $list = $this->m_karyawan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        $nama=trim($this->session->get('nama'));
        foreach ($list as $person) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
                    <!--li role="presentation"><a role="menuitem" title="Detail" href="#" onclick="detailNik('."'".trim($person->nik)."'".');"><i class="fa fa-bars"></i> Detail </a></li-->
                    <li role="presentation"><a role="menuitem" title="Ubah Karyawan" onclick="editNik('."'".trim($person->nik)."'".');"><i class="fa fa-gear"></i> Update Data </a></li>
                    <li role="presentation"><a role="menuitem" href="#" title="Detail Atribut Karyawan"  onclick="detailAtributNik('."'".trim($person->nik)."'".');"><i class="fa fa-dashcube"></i> Detail & Upload</a></li>
                    <li role="presentation"><a role="menuitem" href="'.base_url('trans/karyawan/ajax_delete').'/'.trim($person->nik).'" onclick="return confirm('."'Apakah Anda Yakin Menonaktifkan Nik Ini?  ".trim($person->nik)."'".')"><i class="fa fa-close"></i> Resign Karyawan </a></li>
                </ul>
            </div>
             ';
            $row[] = $person->nik;
            $row[] = $person->nmlengkap;

//             if (! empty($person->cimage)) {
//                 $row[] = 					//'test';
//                     '<div class="pull-left image">
// 					<img height="100px" width="100px" alt="User Image" class="img-box" src="'.base_url('/assets/img/profile').'/'.trim($person->cimage).'">
// 				</div>';
//             } else {
//                 $row[] =
//                     '<div class="pull-left image">
// 					<img height="100px" width="100px" alt="User Image" src="'.base_url('/assets/img/user.png').'">
// 				</div>';
//             }

            $row[] = $person->nmdept;
            $row[] = $person->nmsubdept;
            $row[] = $person->nmjabatan;
            $row[] = $person->tglmasukkerja;
            $row[] = $person->nmgroupgol;
            $row[] = $person->locaname;
            //<a class="btn btn-sm btn-success" href="'.base_url('trans/mutprom/index').'/'.trim($person->nik).'" title="Detail"><i class="glyphicon glyphicon-pencil"></i> Mutasi</a>
            //add html for action

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_karyawan->count_all(),
            "recordsFiltered" => $this->m_karyawan->count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function input(){
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $data['message']='';

        $data['title'] = "Input Karyawan Baru";
        $data['type']="INPUT";
        $data['nama']=$nama;
        return $this->template->render('trans/karyawan/v_input_karyawan',$data);
    }

    function showDetailNik () {
        $nama = $this->session->get('nama');
        //parsing biar tidak sama dengan nik
        $docno = $this->request->getGet('docno');
        $nik = $this->request->getGet('nik');

        $param = " and nik='$docno'";
        $dtl = $this->m_karyawan->list_karyawan_param($param);

        $output = array(
            'status' => true,
            'total_count' => $dtl->getNumRows(),
            'items' => $dtl->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    public function edit_karyawan(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';

        $data['title'] = "Edit Karyawan";
        $data['type']="EDIT";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        return $this->template->render('trans/karyawan/v_input_karyawan',$data);
    }

    public function edit_karyawan_resign(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';

        $data['title'] = "Edit Karyawan Resign";
        $data['type']="EDIT_RESIGN";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        return $this->template->render('trans/karyawan/v_input_karyawan_resign',$data);
    }

    public function detail_karyawan(){
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']='';
        $data['title'] = "Detail Karyawan";
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        return $this->template->render('trans/karyawan/v_input_karyawan',$data);
    }

    function saveDataMasterKaryawan(){
        $nama = trim($this->session->get('nama'));
        $type = trim($this->request->getPost('type'));
        $docno = trim($this->request->getPost('docno'));
        $nik = trim($this->request->getPost('nik'));
        $nmlengkap = strtoupper(trim($this->request->getPost('nmlengkap')));
        $callname = strtoupper($this->request->getPost('callname'));
        $alamatlahir = trim($this->request->getPost('alamatlahir'));
        $tgllahir = !$this->request->getPost('tgllahir') ? null : date('Y-m-d',strtotime($this->request->getPost('tgllahir')));

        $jk = trim($this->request->getPost('jk'));
        $kdagama = trim($this->request->getPost('kdagama'));
        $goldarah = strtoupper($this->request->getPost('goldarah'));
        $stswn = trim($this->request->getPost('stswn'));
        $id_division = trim($this->request->getPost('id_division'));
        $id_dept = trim($this->request->getPost('id_dept'));
        $id_subdept = trim($this->request->getPost('id_subdept'));
        $id_jabatan = trim($this->request->getPost('id_jabatan'));
        $id_grade = trim($this->request->getPost('id_grade'));
        $costcenter = trim($this->request->getPost('costcenter'));
        $id_gol = trim($this->request->getPost('id_gol'));
        $plant = trim($this->request->getPost('plant'));
        $statuskepegawaian = trim($this->request->getPost('statuskepegawaian'));
        //$tglmasukkerja = trim($this->request->getPost('tglmasukkerja'));
        $tglmasukkerja = !$this->request->getPost('tglmasukkerja') ? null : date('Y-m-d',strtotime($this->request->getPost('tglmasukkerja')));
        //$tglkeluarkerja = trim($this->request->getPost('tglkeluarkerja'));
        $tglkeluarkerja = !$this->request->getPost('tglkeluarkerja') ? null : date('Y-m-d',strtotime($this->request->getPost('tglkeluarkerja')));
        $id_ktp = trim($this->request->getPost('id_ktp'));
        $id_provktp = trim($this->request->getPost('id_provktp'));
        $id_kotaktp = trim($this->request->getPost('id_kotaktp'));
        $id_kecktp = trim($this->request->getPost('id_kecktp'));
        $id_desaktp = trim($this->request->getPost('id_desaktp'));
        $desc_ktp = strtoupper($this->request->getPost('desc_ktp'));
        $rtktp = trim($this->request->getPost('rtktp'));
        $rwktp = trim($this->request->getPost('rwktp'));
        $id_posktp = trim($this->request->getPost('id_posktp'));

        $checkboxdomisili = $this->request->getPost('checkboxdomisili');
        if ($checkboxdomisili) {
            $id_provdom = $id_provktp;
            $id_kotadom = $id_kotaktp;
            $id_kecdom = $id_kecktp;
            $id_desadom = $id_desaktp;
            $desc_domisili = strtoupper($desc_ktp);
            $rtdom = $rtktp;
            $rwdom = $rwktp;
            $id_posdom = $id_posktp;
        } else {
            $id_provdom = $this->request->getPost('id_provdom');
            $id_kotadom = $this->request->getPost('id_kotadom');
            $id_kecdom = $this->request->getPost('id_kecdom');
            $id_desadom = $this->request->getPost('id_desadom');
            $desc_domisili = strtoupper($this->request->getPost('desc_domisili'));
            $rtdom = $this->request->getPost('rtdom');
            $rwdom = $this->request->getPost('rwdom');
            $id_posdom = $this->request->getPost('id_posdom');
        }

        $noktp = trim($this->request->getPost('noktp'));
        $nokk = trim($this->request->getPost('nokk'));
        $id_npwp = trim($this->request->getPost('id_npwp'));
        $sts_ptkp = trim($this->request->getPost('sts_ptkp'));
        $sts_kawin = trim($this->request->getPost('sts_kawin'));
        $id_jenjang = trim($this->request->getPost('id_jenjang'));
        $pendidikan_terakhir = strtoupper($this->request->getPost('pendidikan_terakhir'));
        $jurusan = strtoupper($this->request->getPost('jurusan'));
        $ipk = $this->request->getPost('ipk');
        $lulus_tahun = trim($this->request->getPost('lulus_tahun'));
        $id_bpjs_kes = trim($this->request->getPost('id_bpjs_kes'));
        $cab_bpjs_kes = trim($this->request->getPost('cab_bpjs_kes'));
        $id_bpjs_naker = trim($this->request->getPost('id_bpjs_naker'));
        $cab_bpjs_naker = trim($this->request->getPost('cab_bpjs_naker'));
        $nohp1 = trim($this->request->getPost('nohp1'));
        $nohp2 = trim($this->request->getPost('nohp2'));
        $email_self = strtoupper($this->request->getPost('email_self'));
        $email_office = strtoupper($this->request->getPost('email_office'));
        $emergency_phone = strtoupper($this->request->getPost('emergency_phone'));
        $emergency_relation = strtoupper($this->request->getPost('emergency_relation'));
        $emergency_name = strtoupper($this->request->getPost('emergency_name'));
        $idabsen = trim($this->request->getPost('idabsen'));
        $type_office = trim($this->request->getPost('type_office'));
        $id_bank = trim($this->request->getPost('id_bank'));
        $id_rekening = trim($this->request->getPost('id_rekening'));
        $nmibukandung = trim($this->request->getPost('nmibukandung'));
        $nmayahkandung = trim($this->request->getPost('nmayahkandung'));
        $alasan_resign = strtoupper($this->request->getPost('alasan_resign'));
        $nikatasan1 = trim($this->request->getPost('nikatasan1'));
        $nikatasan2 = trim($this->request->getPost('nikatasan2'));
        $nikatasan3 = trim($this->request->getPost('nikatasan3'));
        $area_covid = trim($this->request->getPost('area_covid'));
        $statuscovid = trim($this->request->getPost('statuscovid'));
        $typedomisili = trim($this->request->getPost('typedomisili'));
        $transportasi = trim($this->request->getPost('transportasi'));
        $penyakitbawaan = trim($this->request->getPost('penyakitbawaan'));
        $nmkeluargapenyakitbawaan = trim($this->request->getPost('nmkeluargapenyakitbawaan'));
        $nakes = trim($this->request->getPost('nakes'));
        $nmpasangan = trim($this->request->getPost('nmpasangan'));
        $pekerjaanpasangan = trim($this->request->getPost('pekerjaanpasangan'));
        $hubunganpasangan = trim($this->request->getPost('hubunganpasangan'));
        $contactpasangan = trim($this->request->getPost('contactpasangan'));


        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $builder_t_karyawan = $this->db->table('sc_mst.t_karyawan');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $file = $this->request->getFile('cimage');
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'cimage' => [
                    'uploaded[cimage]',
                    'mime_in[cimage,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[cimage,4096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 250 pixel</br>
					* Tinggi Max 250 pixel</br>					
					');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = trim(trim($nik)).'.png';
                $path = FCPATH.'assets/img/profile';
                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                   // ->convert(IMAGETYPE_PNG)
                    ->resize(250, 250, true, 'height')
                    ->save($path . '/' . trim($nik).'.png');
                $gambar=$final_file_name;
            }

            if ($type==='INPUT'){
                $info = array(
                    'branch' => 'JTS',
                    'nik' => $nik,
                    //'nik' => $nama,
                    'nmlengkap' => $nmlengkap,
                    'callname' => $callname,
                    'alamatlahir' => $alamatlahir,
                    'tgllahir' => $tgllahir,
                    'jk' => $jk,
                    'kdagama' => $kdagama,
                    'goldarah' => $goldarah,
                    'stswn' => $stswn,
                    'id_negktp' => $stswn,
                    'id_negdom' => $stswn,
                    'id_division' => $id_division,
                    'id_dept' => $id_dept,
                    'id_subdept' => $id_subdept,
                    'id_jabatan' => $id_jabatan,
                    'id_grade' => $id_grade,
                    'costcenter' => $costcenter,
                    'id_gol' => $id_gol,
                    'plant' => $plant,
                    'loccode' => $plant,
                    'statuskepegawaian' => $statuskepegawaian,
                    'tglmasukkerja' => $tglmasukkerja ? $tglmasukkerja : null,
                    'id_ktp' => $id_ktp,
                    'id_provktp' => $id_provktp,
                    'id_kotaktp' => $id_kotaktp,
                    'id_kecktp' => $id_kecktp,
                    'id_desaktp' => $id_desaktp,
                    'desc_ktp' => $desc_ktp,
                    'id_provdom' => $id_provdom,
                    'id_kotadom' => $id_kotadom,
                    'id_kecdom' => $id_kecdom,
                    'id_desadom' => $id_desadom,
                    'desc_domisili' => $desc_domisili,
                    'nokk' => $nokk,
                    'id_npwp' => $id_npwp,
                    'sts_ptkp' => $sts_ptkp,
                    'sts_kawin' => $sts_kawin,
                    'id_jenjang' => $id_jenjang,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'ipk' => $ipk,
                    'lulus_tahun' => $lulus_tahun,
                    'id_bpjs_kes' => $id_bpjs_kes,
                    'cab_bpjs_kes' => $cab_bpjs_kes,
                    'id_bpjs_naker' => $id_bpjs_naker,
                    'cab_bpjs_naker' => $cab_bpjs_naker,
                    'nohp1' => $nohp1,
                    'nohp2' => $nohp2,
                    'email_self' => $email_self,
                    'email_office' => $email_office,
                    'emergency_phone' => strtoupper($emergency_phone),
                    'emergency_relation' => strtoupper($emergency_relation),
                    'emergency_name' => strtoupper($emergency_name),
                    'idabsen' => $idabsen,
                    'type_office' => $type_office,
                    'id_bank' => $id_bank,
                    'id_rekening' => $id_rekening,
                    'nmibukandung' => $nmibukandung,
                    'nmayahkandung' => $nmayahkandung,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                    'cimage' => $gambar,
                    'nikatasan1' => $nikatasan1,
                    'nikatasan2' => $nikatasan2,
                    'nikatasan3' => $nikatasan3,
                    'area_covid' => $area_covid,
                    'statuscovid' => $statuscovid,
                    'typedomisili' => $typedomisili,
                    'transportasi' => $transportasi,
                    'penyakitbawaan' => $penyakitbawaan,
                    'nmkeluargapenyakitbawaan' => $nmkeluargapenyakitbawaan,
                    'nakes' => $nakes,
                    'rtktp' => $rtktp,
                    'rwktp' => $rwktp,
                    'rtdom' => $rtdom,
                    'rwdom' => $rwdom,
                    'id_posktp' => $id_posktp,
                    'id_posdom' => $id_posdom,
                    'nmpasangan' => $nmpasangan,
                    'pekerjaanpasangan' => $pekerjaanpasangan,
                    'hubunganpasangan' => $hubunganpasangan,
                    'contactpasangan' => $contactpasangan,

                );

                if ($builder_t_karyawan->insert($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            } else if ($type==='EDIT'){
                $infoX = array(
                    'branch' => 'JTS',
                    'nmlengkap' => $nmlengkap,
                    'callname' => $callname,
                    'alamatlahir' => $alamatlahir,
                    'tgllahir' => $tgllahir? $tgllahir : null,
                    'jk' => $jk,
                    'kdagama' => $kdagama,
                    'goldarah' => $goldarah,
                    'stswn' => $stswn,
                    'id_negktp' => $stswn,
                    'id_negdom' => $stswn,
                    'id_division' => $id_division,
                    'id_dept' => $id_dept,
                    'id_subdept' => $id_subdept,
                    'id_jabatan' => $id_jabatan,
                    'id_grade' => $id_grade,
                    'costcenter' => $costcenter,
                    'id_gol' => $id_gol,
                    'plant' => $plant,
                    'loccode' => $plant,
                    'statuskepegawaian' => $statuskepegawaian,
                    'tglmasukkerja' => $tglmasukkerja ? $tglmasukkerja : null,
                    'id_ktp' => $id_ktp,
                    'id_provktp' => $id_provktp,
                    'id_kotaktp' => $id_kotaktp,
                    'id_kecktp' => $id_kecktp,
                    'id_desaktp' => $id_desaktp,
                    'desc_ktp' => $desc_ktp,
                    'id_provdom' => $id_provdom,
                    'id_kotadom' => $id_kotadom,
                    'id_kecdom' => $id_kecdom,
                    'id_desadom' => $id_desadom,
                    'desc_domisili' => $desc_domisili,
                    'nokk' => $nokk,
                    'id_npwp' => $id_npwp,
                    'sts_ptkp' => $sts_ptkp,
                    'sts_kawin' => $sts_kawin,
                    'id_jenjang' => $id_jenjang,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'ipk' => $ipk,
                    'lulus_tahun' => $lulus_tahun,
                    'id_bpjs_kes' => $id_bpjs_kes,
                    'cab_bpjs_kes' => $cab_bpjs_kes,
                    'id_bpjs_naker' => $id_bpjs_naker,
                    'cab_bpjs_naker' => $cab_bpjs_naker,
                    'nohp1' => $nohp1,
                    'nohp2' => $nohp2,
                    'email_self' => $email_self,
                    'email_office' => $email_office,
                    'emergency_phone' => strtoupper($emergency_phone),
                    'emergency_relation' => strtoupper($emergency_relation),
                    'emergency_name' => strtoupper($emergency_name),
                    'idabsen' => $idabsen,
                    'type_office' => $type_office,
                    'id_bank' => $id_bank,
                    'id_rekening' => $id_rekening,
                    'nmibukandung' => $nmibukandung,
                    'nmayahkandung' => $nmayahkandung,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                    'cimage' => $gambar,
                    'nikatasan1' => $nikatasan1,
                    'nikatasan2' => $nikatasan2,
                    'nikatasan3' => $nikatasan3,
                    'area_covid' => $area_covid,
                    'statuscovid' => $statuscovid,
                    'typedomisili' => $typedomisili,
                    'transportasi' => $transportasi,
                    'penyakitbawaan' => $penyakitbawaan,
                    'nmkeluargapenyakitbawaan' => $nmkeluargapenyakitbawaan,
                    'nakes' => $nakes,
                    'rtktp' => $rtktp,
                    'rwktp' => $rwktp,
                    'rtdom' => $rtdom,
                    'rwdom' => $rwdom,
                    'id_posktp' => $id_posktp,
                    'id_posdom' => $id_posdom,
                    'nmpasangan' => $nmpasangan,
                    'pekerjaanpasangan' => $pekerjaanpasangan,
                    'hubunganpasangan' => $hubunganpasangan,
                    'contactpasangan' => $contactpasangan,

                );
                $builder_t_karyawan->where('nik',$nik);
                if ($builder_t_karyawan->update($infoX)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nik,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            } else if ( $type==='EDIT_RESIGN'){
                $infoX = array(
                    'alamatlahir' => $alamatlahir,
                    'tgllahir' => $tgllahir? $tgllahir : null,
                    'jk' => $jk,
                    'kdagama' => $kdagama,
                    'goldarah' => $goldarah,
                    'stswn' => $stswn,
                    'id_negktp' => $stswn,
                    'id_negdom' => $stswn,
                    'id_division' => $id_division,
                    'id_dept' => $id_dept,
                    'id_subdept' => $id_subdept,
                    'id_jabatan' => $id_jabatan,
                    'id_grade' => $id_grade,
                    'costcenter' => $costcenter,
                    'id_gol' => $id_gol,
                    'plant' => $plant,
                    'loccode' => $plant,
                    'statuskepegawaian' => $statuskepegawaian,
                    'tglmasukkerja' => $tglmasukkerja ? $tglmasukkerja : null,
                    'id_ktp' => $id_ktp,
                    'id_provktp' => $id_provktp,
                    'id_kotaktp' => $id_kotaktp,
                    'id_kecktp' => $id_kecktp,
                    'id_desaktp' => $id_desaktp,
                    'desc_ktp' => $desc_ktp,
                    'id_provdom' => $id_provdom,
                    'id_kotadom' => $id_kotadom,
                    'id_kecdom' => $id_kecdom,
                    'id_desadom' => $id_desadom,
                    'desc_domisili' => $desc_domisili,
                    'nokk' => $nokk,
                    'id_npwp' => $id_npwp,
                    'sts_ptkp' => $sts_ptkp,
                    'sts_kawin' => $sts_kawin,
                    'id_jenjang' => $id_jenjang,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'ipk' => $ipk,
                    'lulus_tahun' => $lulus_tahun,
                    'id_bpjs_kes' => $id_bpjs_kes,
                    'cab_bpjs_kes' => $cab_bpjs_kes,
                    'id_bpjs_naker' => $id_bpjs_naker,
                    'cab_bpjs_naker' => $cab_bpjs_naker,
                    'nohp1' => $nohp1,
                    'nohp2' => $nohp2,
                    'email_self' => $email_self,
                    'email_office' => $email_office,
                    'emergency_phone' => strtoupper($emergency_phone),
                    'emergency_relation' => strtoupper($emergency_relation),
                    'emergency_name' => strtoupper($emergency_name),
                    'idabsen' => $idabsen,
                    'type_office' => $type_office,
                    'id_bank' => $id_bank,
                    'id_rekening' => $id_rekening,
                    'nmibukandung' => $nmibukandung,
                    'nmayahkandung' => $nmayahkandung,
                    //'cimage' => $gambar,
                    'nikatasan1' => $nikatasan1,
                    'nikatasan2' => $nikatasan2,
                    'nikatasan3' => $nikatasan3,
                    'area_covid' => $area_covid,
                    'statuscovid' => $statuscovid,
                    'typedomisili' => $typedomisili,
                    'transportasi' => $transportasi,
                    'penyakitbawaan' => $penyakitbawaan,
                    'nmkeluargapenyakitbawaan' => $nmkeluargapenyakitbawaan,
                    'nakes' => $nakes,
                    'rtktp' => $rtktp,
                    'rwktp' => $rwktp,
                    'rtdom' => $rtdom,
                    'rwdom' => $rwdom,
                    'id_posktp' => $id_posktp,
                    'id_posdom' => $id_posdom,
                    'nmpasangan' => $nmpasangan,
                    'pekerjaanpasangan' => $pekerjaanpasangan,
                    'hubunganpasangan' => $hubunganpasangan,
                    'contactpasangan' => $contactpasangan,
                    'tglkeluarkerja' => $tglkeluarkerja ? $tglkeluarkerja : null,
                    'alasan_resign' => strtoupper($alasan_resign),
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );
                $builder_t_karyawan->where('nik',$nik);
                if ($builder_t_karyawan->update($infoX)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete('sc_mst.trxerror');
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nik,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert('sc_mst.trxerror',$infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            }
        } else {
            if ($type==='INPUT'){
                $info = array(
                    'branch' => 'JTS',
                    'nik' => $nik,
                    //'nik' => $nama,
                    'nmlengkap' => $nmlengkap,
                    'callname' => $callname,
                    'alamatlahir' => $alamatlahir,
                    'tgllahir' => $tgllahir,
                    'jk' => $jk,
                    'kdagama' => $kdagama,
                    'goldarah' => $goldarah,
                    'stswn' => $stswn,
                    'id_negktp' => $stswn,
                    'id_negdom' => $stswn,
                    'id_division' => $id_division,
                    'id_dept' => $id_dept,
                    'id_subdept' => $id_subdept,
                    'id_jabatan' => $id_jabatan,
                    'id_grade' => $id_grade,
                    'costcenter' => $costcenter,
                    'id_gol' => $id_gol,
                    'plant' => $plant,
                    'loccode' => $plant,
                    'statuskepegawaian' => $statuskepegawaian,
                    'tglmasukkerja' => $tglmasukkerja ? $tglmasukkerja : null,
                    'id_ktp' => $id_ktp,
                    'id_provktp' => $id_provktp,
                    'id_kotaktp' => $id_kotaktp,
                    'id_kecktp' => $id_kecktp,
                    'id_desaktp' => $id_desaktp,
                    'desc_ktp' => $desc_ktp,
                    'id_provdom' => $id_provdom,
                    'id_kotadom' => $id_kotadom,
                    'id_kecdom' => $id_kecdom,
                    'id_desadom' => $id_desadom,
                    'desc_domisili' => $desc_domisili,
                    'nokk' => $nokk,
                    'id_npwp' => $id_npwp,
                    'sts_ptkp' => $sts_ptkp,
                    'sts_kawin' => $sts_kawin,
                    'id_jenjang' => $id_jenjang,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'ipk' => $ipk,
                    'lulus_tahun' => $lulus_tahun,
                    'id_bpjs_kes' => $id_bpjs_kes,
                    'cab_bpjs_kes' => $cab_bpjs_kes,
                    'id_bpjs_naker' => $id_bpjs_naker,
                    'cab_bpjs_naker' => $cab_bpjs_naker,
                    'nohp1' => $nohp1,
                    'nohp2' => $nohp2,
                    'email_self' => $email_self,
                    'email_office' => $email_office,
                    'emergency_phone' => strtoupper($emergency_phone),
                    'emergency_relation' => strtoupper($emergency_relation),
                    'emergency_name' => strtoupper($emergency_name),
                    'idabsen' => $idabsen,
                    'type_office' => $type_office,
                    'id_bank' => $id_bank,
                    'id_rekening' => $id_rekening,
                    'nmibukandung' => $nmibukandung,
                    'nmayahkandung' => $nmayahkandung,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                    //'cimage' => $gambar,
                    'nikatasan1' => $nikatasan1,
                    'nikatasan2' => $nikatasan2,
                    'nikatasan3' => $nikatasan3,
                    'area_covid' => $area_covid,
                    'statuscovid' => $statuscovid,
                    'typedomisili' => $typedomisili,
                    'transportasi' => $transportasi,
                    'penyakitbawaan' => $penyakitbawaan,
                    'nmkeluargapenyakitbawaan' => $nmkeluargapenyakitbawaan,
                    'nakes' => $nakes,
                    'rtktp' => $rtktp,
                    'rwktp' => $rwktp,
                    'rtdom' => $rtdom,
                    'rwdom' => $rwdom,
                    'id_posktp' => $id_posktp,
                    'id_posdom' => $id_posdom,
                    'nmpasangan' => $nmpasangan,
                    'pekerjaanpasangan' => $pekerjaanpasangan,
                    'hubunganpasangan' => $hubunganpasangan,
                    'contactpasangan' => $contactpasangan,

                );

                if ($builder_t_karyawan->insert($info)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nama,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            } else if ($type==='EDIT'){
                $infoX = array(
                    'branch' => 'JTS',
                    'nmlengkap' => $nmlengkap,
                    'callname' => $callname,
                    'alamatlahir' => $alamatlahir,
                    'tgllahir' => $tgllahir? $tgllahir : null,
                    'jk' => $jk,
                    'kdagama' => $kdagama,
                    'goldarah' => $goldarah,
                    'stswn' => $stswn,
                    'id_negktp' => $stswn,
                    'id_negdom' => $stswn,
                    'id_division' => $id_division,
                    'id_dept' => $id_dept,
                    'id_subdept' => $id_subdept,
                    'id_jabatan' => $id_jabatan,
                    'id_grade' => $id_grade,
                    'costcenter' => $costcenter,
                    'id_gol' => $id_gol,
                    'plant' => $plant,
                    'loccode' => $plant,
                    'statuskepegawaian' => $statuskepegawaian,
                    'tglmasukkerja' => $tglmasukkerja ? $tglmasukkerja : null,
                    'id_ktp' => $id_ktp,
                    'id_provktp' => $id_provktp,
                    'id_kotaktp' => $id_kotaktp,
                    'id_kecktp' => $id_kecktp,
                    'id_desaktp' => $id_desaktp,
                    'desc_ktp' => $desc_ktp,
                    'id_provdom' => $id_provdom,
                    'id_kotadom' => $id_kotadom,
                    'id_kecdom' => $id_kecdom,
                    'id_desadom' => $id_desadom,
                    'desc_domisili' => $desc_domisili,
                    'nokk' => $nokk,
                    'id_npwp' => $id_npwp,
                    'sts_ptkp' => $sts_ptkp,
                    'sts_kawin' => $sts_kawin,
                    'id_jenjang' => $id_jenjang,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'ipk' => $ipk,
                    'lulus_tahun' => $lulus_tahun,
                    'id_bpjs_kes' => $id_bpjs_kes,
                    'cab_bpjs_kes' => $cab_bpjs_kes,
                    'id_bpjs_naker' => $id_bpjs_naker,
                    'cab_bpjs_naker' => $cab_bpjs_naker,
                    'nohp1' => $nohp1,
                    'nohp2' => $nohp2,
                    'email_self' => $email_self,
                    'email_office' => $email_office,
                    'emergency_phone' => strtoupper($emergency_phone),
                    'emergency_relation' => strtoupper($emergency_relation),
                    'emergency_name' => strtoupper($emergency_name),
                    'idabsen' => $idabsen,
                    'type_office' => $type_office,
                    'id_bank' => $id_bank,
                    'id_rekening' => $id_rekening,
                    'nmibukandung' => $nmibukandung,
                    'nmayahkandung' => $nmayahkandung,
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                    //'cimage' => $gambar,
                    'nikatasan1' => $nikatasan1,
                    'nikatasan2' => $nikatasan2,
                    'nikatasan3' => $nikatasan3,
                    'area_covid' => $area_covid,
                    'statuscovid' => $statuscovid,
                    'typedomisili' => $typedomisili,
                    'transportasi' => $transportasi,
                    'penyakitbawaan' => $penyakitbawaan,
                    'nmkeluargapenyakitbawaan' => $nmkeluargapenyakitbawaan,
                    'nakes' => $nakes,
                    'rtktp' => $rtktp,
                    'rwktp' => $rwktp,
                    'rtdom' => $rtdom,
                    'rwdom' => $rwdom,
                    'id_posktp' => $id_posktp,
                    'id_posdom' => $id_posdom,
                    'nmpasangan' => $nmpasangan,
                    'pekerjaanpasangan' => $pekerjaanpasangan,
                    'hubunganpasangan' => $hubunganpasangan,
                    'contactpasangan' => $contactpasangan,

                );
                $builder_t_karyawan->where('nik',$nik);
                if ($builder_t_karyawan->update($infoX)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nik,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
                    $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                    $getResult = array('status' => true, 'messages' => 'Sukses Di Proses'.' Nomor: '.trim($dtlerror['nomorakhir1']));
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data 2');
                    echo json_encode($getResult);
                }
            } else if ( $type==='EDIT_RESIGN'){
                $infoX = array(

                    'alamatlahir' => $alamatlahir,
                    'tgllahir' => $tgllahir? $tgllahir : null,
                    'jk' => $jk,
                    'kdagama' => $kdagama,
                    'goldarah' => $goldarah,
                    'stswn' => $stswn,
                    'id_negktp' => $stswn,
                    'id_negdom' => $stswn,
                    'id_division' => $id_division,
                    'id_dept' => $id_dept,
                    'id_subdept' => $id_subdept,
                    'id_jabatan' => $id_jabatan,
                    'id_grade' => $id_grade,
                    'costcenter' => $costcenter,
                    'id_gol' => $id_gol,
                    'plant' => $plant,
                    'loccode' => $plant,
                    'statuskepegawaian' => $statuskepegawaian,
                    'tglmasukkerja' => $tglmasukkerja ? $tglmasukkerja : null,
                    'id_ktp' => $id_ktp,
                    'id_provktp' => $id_provktp,
                    'id_kotaktp' => $id_kotaktp,
                    'id_kecktp' => $id_kecktp,
                    'id_desaktp' => $id_desaktp,
                    'desc_ktp' => $desc_ktp,
                    'id_provdom' => $id_provdom,
                    'id_kotadom' => $id_kotadom,
                    'id_kecdom' => $id_kecdom,
                    'id_desadom' => $id_desadom,
                    'desc_domisili' => $desc_domisili,
                    'nokk' => $nokk,
                    'id_npwp' => $id_npwp,
                    'sts_ptkp' => $sts_ptkp,
                    'sts_kawin' => $sts_kawin,
                    'id_jenjang' => $id_jenjang,
                    'pendidikan_terakhir' => $pendidikan_terakhir,
                    'jurusan' => $jurusan,
                    'ipk' => $ipk,
                    'lulus_tahun' => $lulus_tahun,
                    'id_bpjs_kes' => $id_bpjs_kes,
                    'cab_bpjs_kes' => $cab_bpjs_kes,
                    'id_bpjs_naker' => $id_bpjs_naker,
                    'cab_bpjs_naker' => $cab_bpjs_naker,
                    'nohp1' => $nohp1,
                    'nohp2' => $nohp2,
                    'email_self' => $email_self,
                    'email_office' => $email_office,
                    'emergency_phone' => strtoupper($emergency_phone),
                    'emergency_relation' => strtoupper($emergency_relation),
                    'emergency_name' => strtoupper($emergency_name),
                    'idabsen' => $idabsen,
                    'type_office' => $type_office,
                    'id_bank' => $id_bank,
                    'id_rekening' => $id_rekening,
                    'nmibukandung' => $nmibukandung,
                    'nmayahkandung' => $nmayahkandung,
                    //'cimage' => $gambar,
                    'nikatasan1' => $nikatasan1,
                    'nikatasan2' => $nikatasan2,
                    'nikatasan3' => $nikatasan3,
                    'area_covid' => $area_covid,
                    'statuscovid' => $statuscovid,
                    'typedomisili' => $typedomisili,
                    'transportasi' => $transportasi,
                    'penyakitbawaan' => $penyakitbawaan,
                    'nmkeluargapenyakitbawaan' => $nmkeluargapenyakitbawaan,
                    'nakes' => $nakes,
                    'rtktp' => $rtktp,
                    'rwktp' => $rwktp,
                    'rtdom' => $rtdom,
                    'rwdom' => $rwdom,
                    'id_posktp' => $id_posktp,
                    'id_posdom' => $id_posdom,
                    'nmpasangan' => $nmpasangan,
                    'pekerjaanpasangan' => $pekerjaanpasangan,
                    'hubunganpasangan' => $hubunganpasangan,
                    'contactpasangan' => $contactpasangan,
                    'tglkeluarkerja' => $tglkeluarkerja ? $tglkeluarkerja : null,
                    'alasan_resign' => strtoupper($alasan_resign),
                    'updateby' => $inputby,
                    'updatedate' => $inputdate,
                );
                $builder_t_karyawan->where('nik',$nik);
                if ($builder_t_karyawan->update($infoX)) {
                    //INSERT TRX ERROR
                    $builder_trxerror->where('userid',$nama);
                    $builder_trxerror->where('modul','I.T.A.1');
                    $builder_trxerror->delete();
                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => $nik,
                        'nomorakhir2' => '',
                        'modul' => 'I.T.A.1',
                    );
                    $builder_trxerror->insert($infotrxerror);

                    $paramerror=" and userid='$nama' and modul='I.T.A.1'";
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


    public function ajax_edit($id)
    {
        $data = $this->m_karyawan->get_by_id($id)->row();
        echo json_encode($data);
    }
    public function detail($id)
    {
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $nik=$this->uri->segment(4);
        $data['message']='';
        $data['nik']=$nik;
        $data['list_opt_agama']=$this->m_agama->q_agama()->getResult();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->getResult();
        $data['list_opt_dept']=$this->m_department->q_department()->getResult();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->getResult();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->getResult();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->getResult();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->getResult();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->getResult();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->getResult();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->getResult();
        $data['list_opt_bank']=$this->m_bank->q_bank()->getResult();
        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getResult();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->getResult();
        $data['list_finger']=$this->m_karyawan->q_finger()->getResult();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->getResult();//

        /*MUTASI*/
        //$data['list_karyawan']=$this->m_mutpromot->list_karyawan()->getResult();
        $data['list_karyawan']=$this->m_mutpromot->list_nik($id)->getResult();
        $data['list_mutasi']=$this->m_mutpromot->get_mutasinik($id)->getResult();
        /*END MUTASI*/
        /*STATUS KEPEGAWAIN*/
        $data['list_lk']=$this->m_stspeg->list_karyawan_index($id)->getRowArray();
        $data['list_kepegawaian']=$this->m_stspeg->list_kepegawaian()->getResult();
        $data['list_stspeg']=$this->m_stspeg->q_stspeg($id)->getResult();
        $data['list_rk']=$this->m_stspeg->q_stspeg($id)->getRowArray();
        /**/
        /*BPJS KARYAWAN*/
        $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->getResult();
        $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->getResult();
        $data['list_bpjskaryawan']=$this->m_bpjs->list_bpjs_karyawan($id)->getResult();
        $data['list_faskes']=$this->m_bpjs->list_faskes()->getResult();
        $data['list_kelas']=$this->m_bpjs->q_trxtype()->getResult();
        $data['list_karyawan_bpjs']=$this->m_bpjs->list_karyawan()->getResult();
        /*ADD RIWAYAT KELUARGA*/
        $data['list_keluarga']=$this->m_riwayat_keluarga->list_keluarga()->getResult();
        $data['list_negara']=$this->m_riwayat_keluarga->list_negara()->getResult();
        $data['list_prov']=$this->m_riwayat_keluarga->list_prov()->getResult();
        $data['list_kotakab']=$this->m_riwayat_keluarga->list_kotakab()->getResult();
        $data['list_jenjang_pendidikan']=$this->m_riwayat_keluarga->list_jenjang_pendidikan()->getResult();
        $data['list_riwayat_keluarga']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->getResult();
        $data['list_rku']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->getRowArray();
        /**/
        /*RIWAYAT KESEHATAN*/

        $data['list_karyawan_kesehatan']=$this->m_riwayat_kesehatan->list_karyawan()->getResult();
        $data['list_lkes']=$this->m_riwayat_kesehatan->list_karyawan_index($nik)->getRowArray();
        $data['list_penyakit']=$this->m_riwayat_kesehatan->list_penyakit()->getResult();
        $data['list_riwayat_kesehatan']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->getResult();
        $data['list_rkes']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->getRowArray();
        //$data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        /**/
        /*RIWAYAT KERJA /PENGALAMAN*/

        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getResult();
        $data['list_rpglm']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getRowArray();
        /**/

        /* PELATIHAN KARYAWAN */
        $data['list_pennf']=$this->m_riwayat_pendidikan_nf->list_karyawan_index($nik)->getRowArray();
        $data['list_keahlian']=$this->m_riwayat_pendidikan_nf->list_keahlian()->getResult();
        $data['list_riwayat_pendidikan_nf']=$this->m_riwayat_pendidikan_nf->q_riwayat_pendidikan_nf($nik)->getResult();
        /**/

        /*PENDIDIKAN */
        $data['list_pendidikan']=$this->m_riwayat_pendidikan->list_pendidikan()->getResult();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->getResult();
        /**/

        /* SP KARYAWAN */
        $lsp = $this->db->query("select * from sc_his.sk_peringatan where nik='$id'");
        $data['list_spkaryawan'] = $lsp->getResult();
        /* END SP KARYAWAN */

        $data['title'] = "Detail Karyawan";
        //$data['dtl'] = $this->m_karyawan->get_dtl_id($id)->getRowArray();
        $data['lp'] = $this->m_karyawan->get_dtl_id($id)->getRowArray();
        return $this->template->render('trans/karyawan/v_detailhrdkary',$data);
    }

    public function detail_resign($id)
    {

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $nik=$this->uri->segment(4);
        $data['message']='';
        $data['nik']=$nik;
        $data['list_opt_agama']=$this->m_agama->q_agama()->getResult();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->getResult();
        $data['list_opt_dept']=$this->m_department->q_department()->getResult();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->getResult();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->getResult();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->getResult();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->getResult();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->getResult();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->getResult();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->getResult();
        $data['list_opt_bank']=$this->m_bank->q_bank()->getResult();
        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getResult();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->getResult();
        $data['list_finger']=$this->m_karyawan->q_finger()->getResult();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->getResult();//
        /// $data['list_wilnom']=$this->m_karyawan->q_wilayah_nominal($p=null)->getResult();// wilayah nominal

        /*MUTASI*/
        //$data['list_karyawan']=$this->m_mutpromot->list_karyawan()->getResult();
        $data['list_karyawan']=$this->m_mutpromot->list_nik($id)->getResult();
        $data['list_mutasi']=$this->m_mutpromot->get_mutasinik($id)->getResult();
        /*END MUTASI*/
        /*STATUS KEPEGAWAIN*/
        $data['list_lk']=$this->m_stspeg->list_karyawan_index($id)->getRowArray();
        $data['list_kepegawaian']=$this->m_stspeg->list_kepegawaian()->getResult();
        $data['list_stspeg']=$this->m_stspeg->q_stspeg($id)->getResult();
        $data['list_rk']=$this->m_stspeg->q_stspeg($id)->getRowArray();
        /**/
        /*BPJS KARYAWAN*/
        $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->getResult();
        $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->getResult();
        $data['list_bpjskaryawan']=$this->m_bpjs->list_bpjs_karyawan($id)->getResult();
        $data['list_faskes']=$this->m_bpjs->list_faskes()->getResult();
        $data['list_kelas']=$this->m_bpjs->q_trxtype()->getResult();
        $data['list_karyawan_bpjs']=$this->m_bpjs->list_karyawan()->getResult();
        /*ADD RIWAYAT KELUARGA*/
        $data['list_keluarga']=$this->m_riwayat_keluarga->list_keluarga()->getResult();
        $data['list_negara']=$this->m_riwayat_keluarga->list_negara()->getResult();
        $data['list_prov']=$this->m_riwayat_keluarga->list_prov()->getResult();
        $data['list_kotakab']=$this->m_riwayat_keluarga->list_kotakab()->getResult();
        $data['list_jenjang_pendidikan']=$this->m_riwayat_keluarga->list_jenjang_pendidikan()->getResult();
        $data['list_riwayat_keluarga']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->getResult();
        $data['list_rku']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->getRowArray();
        /**/
        /*RIWAYAT KESEHATAN*/

        $data['list_karyawan_kesehatan']=$this->m_riwayat_kesehatan->list_karyawan()->getResult();
        $data['list_lkes']=$this->m_riwayat_kesehatan->list_karyawan_index($nik)->getRowArray();
        $data['list_penyakit']=$this->m_riwayat_kesehatan->list_penyakit()->getResult();
        $data['list_riwayat_kesehatan']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->getResult();
        $data['list_rkes']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->getRowArray();
        //$data['akses']=$this->m_global->list_aksespermenu($nama,$kmenu)->getRowArray();
        /**/
        /*RIWAYAT KERJA /PENGALAMAN*/

        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getResult();
        $data['list_rpglm']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getRowArray();
        /**/

        /* PELATIHAN KARYAWAN */
        $data['list_pennf']=$this->m_riwayat_pendidikan_nf->list_karyawan_index($nik)->getRowArray();
        $data['list_keahlian']=$this->m_riwayat_pendidikan_nf->list_keahlian()->getResult();
        $data['list_riwayat_pendidikan_nf']=$this->m_riwayat_pendidikan_nf->q_riwayat_pendidikan_nf($nik)->getResult();
        /**/

        /*PENDIDIKAN */
        $data['list_pendidikan']=$this->m_riwayat_pendidikan->list_pendidikan()->getResult();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->getResult();
        /**/


        $data['title'] = "Detail Karyawan";
        //$data['dtl'] = $this->m_karyawan->get_dtl_id($id)->getRowArray();
        $data['lp'] = $this->m_karyawan->get_dtl_id($id)->getRowArray();
        return $this->template->render('trans/karyawan/v_detailhrdkary_resign',$data);
    }

    public function edit($id){

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */
        $data['message']='';
        $data['list_opt_neg']=$this->m_geo->list_opt_negara()->getResult();
        $data['list_opt_prov']=$this->m_geo->list_opt_prov()->getResult();
        $data['list_opt_kotakab']=$this->m_geo->list_opt_kotakab()->getResult();
        $data['list_opt_kec']=$this->m_geo->list_opt_kec()->getResult();
        //$data['list_opt_keldesa']=$this->m_geo->list_opt_keldesa()->getResult();
        $data['list_opt_agama']=$this->m_agama->q_agama()->getResult();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->getResult();
        $data['list_opt_dept']=$this->m_department->q_department()->getResult();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->getResult();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->getResult();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->getResult();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->getResult();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->getResult();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->getResult();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->getResult();
        $data['list_opt_bank']=$this->m_bank->q_bank()->getResult();
        $data['list_finger']=$this->m_karyawan->q_finger()->getResult();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->getResult();//
        $data['title'] = "Edit Karyawan";
        $data['dtl'] = $this->m_karyawan->get_by_id($id)->getRowArray();
        return $this->template->render('trans/karyawan/v_editkary',$data);
    }

    public function ajax_add()
    {
        $branch=strtoupper($this->request->getPost('branch'));
        $unlimitedktp=$this->request->getPost('ktp_seumurhdp');
        ///if ($unlimitedktp=='f' or $unlimitedktp=='F'){
        $tgl_ktp1=$this->request->getPost('ktpberlaku');
        $nik=trim(strtoupper($this->request->getPost('nik')));
        $tglnpwps=$this->request->getPost('tglnpwp');
        //$besaranptkp=strtoupper($this->request->getPost('besaranptkp'));
        $ktpkeluar=strtoupper($this->request->getPost('ktpdikeluarkan'));
        $tglkeluar=strtoupper($this->request->getPost('tgldikeluarkan'));
        $tgllahir= strtoupper($this->request->getPost('tgllahir'));
        $gp=$this->request->getPost('gajipokok');
        $gbpjs=$this->request->getPost('gajibpjs');
        $gjnaker1=$this->request->getPost('gajinaker');
        $status_ptkp=strtoupper($this->request->getPost('status_ptkp'));
        $kdcabang=strtoupper($this->request->getPost('kdcabang'));
        $besaranptkp1=$this->m_karyawan->q_besaranptkp($status_ptkp)->getRowArray();
        $besaranptkp=$besaranptkp1['besaranpertahun'];
        $dfktp=$this->request->getPost('dfktp');
        $nokk=trim($this->request->getPost('nokk'));
        if($dfktp=='t'){ $noktp=strtoupper($this->request->getPost('noktp2')); } else { $noktp=strtoupper($this->request->getPost('noktp1')); };

        if(empty($tgl_ktp1)){
            $tgl_ktp=null;
        } else {
            $tgl_ktp=$tgl_ktp1;
        }
        if (empty($tgllahir)){
            $tgllahir1=null;
        } else {
            $tgllahir1=$tgllahir;
        }
        if (empty($ktpkeluar)){
            $ktpdikeluarkan=null;
        } else{
            $ktpdikeluarkan=$ktpkeluar;
        }
        if (empty($tglkeluar)){
            $tgldikeluarkan=null;
        } else{
            $tgldikeluarkan=$tglkeluar;
        }
        if (empty($besaranptkp))
        {
            $ptkp=null;
        } else {
            $ptkp=$besaranptkp;
        }
        if (empty($tglnpwps)){
            $tglnpwp=null;
        } else {
            $tglnpwp=$tglnpwps;
        }
        if (empty($gp)){
            $gajipokok=null;
        } else {
            $gajipokok=$gp;
        }
        if (empty($gbpjs)){
            $gajibpjs=null;
        } else {
            $gajibpjs=$gbpjs;
        }
        if (empty($gjnaker1)){
            $gjnaker=null;
        } else {
            $gjnaker=$gjnaker1;
        }



        $data = array(
            'branch'=>$branch,
            'nik' =>$nik,
            'nmlengkap' => strtoupper($this->request->getPost('nmlengkap')),
            'callname' => strtoupper($this->request->getPost('callname')),
            'jk' => strtoupper($this->request->getPost('jk')),
            'neglahir' => strtoupper($this->request->getPost('neglahir')),
            'provlahir' => strtoupper($this->request->getPost('provlahir')),
            'kotalahir' => strtoupper($this->request->getPost('kotalahir')),
            'tgllahir' => $tgllahir1,
            'kd_agama' => strtoupper($this->request->getPost('kd_agama')),
            'stswn' => strtoupper($this->request->getPost('stswn')),
            'stsfisik' => strtoupper($this->request->getPost('stsfisik')),
            'ketfisik' => strtoupper($this->request->getPost('ketfisik')),
            'noktp' => $noktp,
            'tgl_ktp' => $tgl_ktp,
            'ktp_seumurhdp' => strtoupper($this->request->getPost('ktp_seumurhdp')),
            'ktpdikeluarkan' => $ktpdikeluarkan,
            'tgldikeluarkan' => $tgldikeluarkan,
            'status_pernikahan' => strtoupper($this->request->getPost('stastus_pernikahan')),
            'gol_darah' => strtoupper($this->request->getPost('gol_darah')),
            'negktp' => strtoupper($this->request->getPost('negktp')),
            'provktp' => strtoupper($this->request->getPost('provktp')),
            'kotaktp' => strtoupper($this->request->getPost('kotaktp')),
            'kecktp' => strtoupper($this->request->getPost('kecktp')),
            'kelktp' => strtoupper($this->request->getPost('kelktp')),
            'alamatktp' => strtoupper($this->request->getPost('alamatktp')),
            'negtinggal' => strtoupper($this->request->getPost('negtinggal')),
            'provtinggal' => strtoupper($this->request->getPost('provtinggal')),
            'kotatinggal' => strtoupper($this->request->getPost('kotatinggal')),
            'kectinggal' => strtoupper($this->request->getPost('kectinggal')),
            'keltinggal' => strtoupper($this->request->getPost('keltinggal')),
            'alamattinggal' => strtoupper($this->request->getPost('alamattinggal')),
            'nohp1' => strtoupper($this->request->getPost('nohp1')),
            'nohp2' => strtoupper($this->request->getPost('nohp2')),
            'npwp' => strtoupper($this->request->getPost('npwp')),
            'tglnpwp' => $tglnpwp,
            'bag_dept' => strtoupper($this->request->getPost('bag_dept')),
            'subbag_dept' => strtoupper($this->request->getPost('subbag_dept')),
            'jabatan' => strtoupper($this->request->getPost('jabatan')),
            'lvl_jabatan' => strtoupper($this->request->getPost('lvl_jabatan')),
            'grade_golongan' => null,
            'nik_atasan' => strtoupper($this->request->getPost('nik_atasan')),
            'nik_atasan2' => strtoupper($this->request->getPost('nik_atasan2')),
            'status_ptkp' => strtoupper($this->request->getPost('status_ptkp')),
            'besaranptkp' => $besaranptkp,
            'tglmasukkerja' => strtoupper($this->request->getPost('tglmasukkerja')),
            //'tglkeluarkerja' => strtoupper($this->request->getPost('tglkeluarkerja')),
            'masakerja' => strtoupper($this->request->getPost('masakerja')),
            'statuskepegawaian' => strtoupper($this->request->getPost('statuskepegawaian')),
            'grouppenggajian' => strtoupper($this->request->getPost('grouppenggajian')),
            'gajipokok' => $gajipokok,
            'gajibpjs' => $gajibpjs,
            'gajinaker' => $gjnaker,
            'namabank' => strtoupper($this->request->getPost('namabank')),
            'namapemilikrekening' => strtoupper($this->request->getPost('namapemilikrekening')),
            'norek' => strtoupper($this->request->getPost('norek')),
            //'shift' => strtoupper($this->request->getPost('shift')),
            'idabsen' => strtoupper($this->request->getPost('idabsen')),
            'idmesin' => $this->request->getPost('idmesin'),
            'cardnumber' => $this->request->getPost('cardnumber'),
            'email' => strtoupper($this->request->getPost('email')),
            //'bolehcuti' => strtoupper($this->request->getPost('bolehcuti')),
            //'sisacuti' => strtoupper($this->request->getPost('sisacuti')),
            'inputdate'=>date("d-m-Y H:i:s"),
            'inputby'=>$this->session->get('nama'),
            'tjborong'=>$this->request->getPost('borong'),
            'tjshift'=>$this->request->getPost('shift'),
            'tjlembur'=>$this->request->getPost('lembur'),
            'kdcabang'=>$kdcabang,
            'kdregu'=>trim($this->request->getPost('kdregu')),
            'nokk'=>$nokk,

        );
        $cek=$this->m_karyawan->cek_exist($nik);
        if ($cek>0) {
            return redirect()->to(base_url('trans/karyawan/index/exist'));
        } else {
            $insert = $this->m_karyawan->save($data);
            echo json_encode(array("status" => TRUE));
            return redirect()->to(base_url('trans/karyawan/index/success'));
        }

    }

    public function ajax_update()
    {
        $nama = trim($this->session->get('nama'));
        $username = trim($this->session->get('nama'));
        $inputdate = date('Y-m-d H:i:s');
        $branch=strtoupper($this->request->getPost('branch'));
        $unlimitedktp=$this->request->getPost('ktp_seumurhdp');
        if ($unlimitedktp=='f'){
            $tgl_ktp=$this->request->getPost('tgl_ktp');
        } else {
            $tgl_ktp=null;
        }
        $nik=trim(strtoupper($this->request->getPost('nik')));
        $tglnpwps=$this->request->getPost('tglnpwp');
        //$besaranptkp=strtoupper($this->request->getPost('besaranptkp'));
        $ktpkeluar=strtoupper($this->request->getPost('ktpdikeluarkan'));
        $tglkeluar=strtoupper($this->request->getPost('tgldikeluarkan'));
        $tgllahir= strtoupper($this->request->getPost('tgllahir'));
        //$gp=$this->request->getPost('gajipokok'); //disabled by request
        //$gbpjs=$this->request->getPost('gajibpjs');
        //$gjnaker1=$this->request->getPost('gajibpjs');
        $status_ptkp=strtoupper($this->request->getPost('status_ptkp'));
        $besaranptkp1=$this->m_karyawan->q_besaranptkp($status_ptkp)->getRowArray();
        $besaranptkp=$besaranptkp1['besaranpertahun'];
        $kdcabang=$this->request->getPost('kdcabang');
        $nokk=trim($this->request->getPost('nokk'));
        $deviceid=trim($this->request->getPost('deviceid'));
        if (empty($tgllahir)){
            $tgllahir1=null;
        } else {
            $tgllahir1=$tgllahir;
        }
        if (empty($ktpkeluar)){
            $ktpdikeluarkan=null;
        } else{
            $ktpdikeluarkan=$ktpkeluar;
        }
        if (empty($tglkeluar)){
            $tgldikeluarkan=null;
        } else{
            $tgldikeluarkan=$tglkeluar;
        }
        if (empty($besaranptkp))
        {
            $ptkp=null;
        } else {
            $ptkp=$besaranptkp;
        }
        if (empty($tglnpwps)){
            $tglnpwp=null;
        } else {
            $tglnpwp=$tglnpwps;
        }
        if (empty($gp)){
            $gajipokok=null;
        } else {
            $gajipokok=$gp;
        }
        if (empty($gbpjs)){
            $gajibpjs=null;
        } else {
            $gajibpjs=$gbpjs;
        }
        if (empty($gjnaker1)){
            $gjnaker=null;
        } else {
            $gjnaker=$gjnaker1;
        }



        $data = array(
            'branch' =>$branch,
            'nik' =>$nik,
            'nmlengkap' => strtoupper($this->request->getPost('nmlengkap')),
            'callname' => strtoupper($this->request->getPost('callname')),
            'jk' => strtoupper($this->request->getPost('jk')),
            'neglahir' => strtoupper($this->request->getPost('neglahir')),
            'provlahir' => strtoupper($this->request->getPost('provlahir')),
            'kotalahir' => strtoupper($this->request->getPost('kotalahir')),
            'tgllahir' => $tgllahir1,
            'kd_agama' => strtoupper($this->request->getPost('kd_agama')),
            'stswn' => strtoupper($this->request->getPost('stswn')),
            'stsfisik' => strtoupper($this->request->getPost('stsfisik')),
            'ketfisik' => strtoupper($this->request->getPost('ketfisik')),
            'noktp' => strtoupper($this->request->getPost('noktp')),
            //'tgl_ktp' => $tgl_ktp,
            'ktp_seumurhdp' => strtoupper($this->request->getPost('ktp_seumurhdp')),
            'ktpdikeluarkan' => $ktpdikeluarkan,
            'tgldikeluarkan' => $tgldikeluarkan,
            'status_pernikahan' => strtoupper($this->request->getPost('status_pernikahan')),
            'gol_darah' => strtoupper($this->request->getPost('gol_darah')),
            'negktp' => strtoupper($this->request->getPost('negktp')),
            'provktp' => strtoupper($this->request->getPost('provktp')),
            'kotaktp' => strtoupper($this->request->getPost('kotaktp')),
            'kecktp' => strtoupper($this->request->getPost('kecktp')),
            'kelktp' => strtoupper($this->request->getPost('kelktp')),
            'alamatktp' => strtoupper($this->request->getPost('alamatktp')),
            'negtinggal' => strtoupper($this->request->getPost('negtinggal')),
            'provtinggal' => strtoupper($this->request->getPost('provtinggal')),
            'kotatinggal' => strtoupper($this->request->getPost('kotatinggal')),
            'kectinggal' => strtoupper($this->request->getPost('kectinggal')),
            'keltinggal' => strtoupper($this->request->getPost('keltinggal')),
            'alamattinggal' => strtoupper($this->request->getPost('alamattinggal')),
            'nohp1' => strtoupper($this->request->getPost('nohp1')),
            'nohp2' => strtoupper($this->request->getPost('nohp2')),
            'npwp' => strtoupper($this->request->getPost('npwp')),
            'tglnpwp' =>$tglnpwp,
            'bag_dept' => strtoupper($this->request->getPost('dept')),
            'subbag_dept' => strtoupper($this->request->getPost('subbag_dept')),
            'jabatan' => strtoupper($this->request->getPost('jabatan')),
            'lvl_jabatan' => strtoupper($this->request->getPost('lvl_jabatan')),
            //'grade_golongan' => strtoupper($this->request->getPost('grade_golongan')),
            'nik_atasan' => strtoupper($this->request->getPost('nik_atasan')),
            'nik_atasan2' => strtoupper($this->request->getPost('nik_atasan2')),
            'status_ptkp' => strtoupper($this->request->getPost('status_ptkp')),
            'besaranptkp' => $besaranptkp,
            'tglmasukkerja' =>date('Y-m-d',strtotime($this->request->getPost('tglmasukkerja'))),
            //'tglkeluarkerja' => strtoupper($this->request->getPost('tglkeluarkerja')),
            'masakerja' => strtoupper($this->request->getPost('masakerja')),
            ////'statuskepegawaian' => strtoupper($this->request->getPost('statuskepegawaian')),
            'grouppenggajian' => strtoupper($this->request->getPost('grouppenggajian')),
            //'gajipokok' => $gajipokok,
            //'gajibpjs' => $gajibpjs,
            //'gajinaker' => $gjnaker,
            'namabank' => strtoupper($this->request->getPost('namabank')),
            'namapemilikrekening' => strtoupper($this->request->getPost('namapemilikrekening')),
            'norek' => strtoupper($this->request->getPost('norek')),
            //'shift' => strtoupper($this->request->getPost('shift')),
            'idabsen' => strtoupper($this->request->getPost('idabsen')),
            'email' => strtoupper($this->request->getPost('email')),
            //'bolehcuti' => strtoupper($this->request->getPost('bolehcuti')),
            //'sisacuti' => strtoupper($this->request->getPost('sisacuti')),
            /*            'inputdate'=>date("d-m-Y H:i:s"),
                        'inputby'=>$this->session->get('nama'),*/
            'tjborong'=>$this->request->getPost('borong'),
            'tjshift'=>$this->request->getPost('shift'),
            'tjlembur'=>$this->request->getPost('lembur'),
            'kdcabang'=>$kdcabang,
            'nokk'=>$nokk,
            'deviceid'=>$deviceid,
            'updateby' => $username,
            'updatedate' => $inputdate,
        );


        $this->m_karyawan->update(array('nik' => $this->request->getPost('nik')), $data);
        echo json_encode(array("status" => TRUE));
        return redirect()->to(base_url('trans/karyawan/index/upsuccess'));

        ///echo strtoupper($this->request->getPost('dept'));
        ///echo '<BR>';
        ///echo strtoupper($this->request->getPost('subbag_dept'));
        ///echo '<BR>';
        ///echo strtoupper($this->request->getPost('jabatan'));
        ///echo '<BR>';
        ///echo strtoupper($this->request->getPost('lvl_jabatan'));

    }

    public function ajax_delete($id)
    {
        //$this->m_karyawan->delete_by_id($id);
        //echo json_encode(array("status" => TRUE));
        $builder = $this->db->table('sc_mst.t_karyawan');
        $info = array('resign' => 'YES');
        $builder->where('nik',$id);
        $builder->update($info);
        return redirect()->to(base_url('trans/karyawan'));
    }

    public function excel_listkaryawan(){
        $resignFilter = $this->request->getPost('resign_filter');
        $vaksin_filter = $this->request->getPost('vaksin_filter');
        $id_kotaktp = $this->request->getPost('id_kotaktp');
        $id_kotadom = $this->request->getPost('id_kotadom');
        $plant = $this->request->getPost('plant');
        $statuskepegawaian = $this->request->getPost('statuskepegawaian_download');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];

        if ($resignFilter==='ALL') {
            $resign = "";
        } else if ($resignFilter==='YES') {
            $resign = " and resign='YES'";
        } else {
            $resign = " and resign='NO'";
        }
        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($statuskepegawaian)) { $statuskepegawaianx=" and statuskepegawaian='$statuskepegawaian'"; } else {  $statuskepegawaianx=""; }
        if (!empty($vaksin_filter)) {
            if($vaksin_filter==='NO') {
                $area_covid=" and coalesce(area_covid,'BELUM') = 'BELUM'";
            } else {
                $area_covid=" and coalesce(area_covid,'BELUM') != 'BELUM'";
            }
        } else {
            $area_covid="";
        }
        if (!empty($id_kotaktp)) { $kotaktp=" and id_kotaktp='$id_kotaktp'"; } else {  $kotaktp=""; }
        if (!empty($id_kotadom)) { $kotadom=" and id_kotadom='$id_kotadom'"; } else {  $kotadom=""; }

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        $parameter = $resign.$pplan.$area_covid.$kotaktp.$kotadom.$statuskepegawaianx;
        $datane = $this->m_karyawan->list_karyawan_param2(" and left(nik,1)!='A' $parameter");
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array(
            'NIK','NAMA LENGKAP','PANGGILAN','JENIS KELAMIN','WARGA NEGARA','TANGGAL LAHIR','AGAMA','ALAMAT LAHIR','NO KTP',
            'ALAMAT KTP','RT_KTP','RW_KTP','KEL/DESA KTP','KECAMATAN KTP','KOTA/KAB KTP','PROVINSI KTP','KODEPOS KTP','ALAMAT LENGKAP KTP',
            'TYPE DOMISILI','TRANSPORTASI',
            'ALAMAT DOMISILI','RT DOM','RW DOM','KEL/DESA DOMISILI','KECAMATAN DOMISILI','KOTA/KAB DOMISILI','PROVINSI DOMISILI','KODEPOS DOMISILI','ALAMAT LENGKAP DOMISILI',
            'STATUS KAWIN','NPWP','STATUSPTKP','TGL MASUKKERJA','KEPEGAWAIAN','KANTOR','PLANT',
            'DIVISI','DEPARTMEN','SECTION','POSITION','GRADE','GROUP','COSTCENTER','KK','HP1','HP2',
            'EMAIL OFFICE','EMAIL PRIBADI','BANK','NO REKENING','JENJANG','JENJANG DI','JURUSAN','TAHUN LULUS','IPK','NAMA AYAH KANDUNG','NAMA IBU KANDUNG',
            'ID BPJSKES','CABANG BPJSKES','ID BPJSNAKER','CAB BPJS NAKER','ATASAN1','ATASAN2','ATASAN3','SISA CUTI',
            'UMUR KARYAWAN','LAMA BEKERJA','NAMA PASANGAN','HUBUNGAN PASANGAN','HP PASANGAN','PEKERJAAN PASANGAN',
            'PENYAKIT BAWAAN','KELUARGA BERPENYAKIT BAWAAN','NAKES','RESIGN','ALASAN RESIGN','TGL KELUAR','GOLONGAN DARAH',
            'RIWAYAT COVID','STATUS COVID','TGL TERAKHIR TERINDIKASI COVID','DESKRIPSI COVID',
            'VAKSIN COVID','TGL TERAKHIR VAKSIN COVID','DESKIRPSI VAKSIN COVID'
        ));

        $this->fiky_excel->set_column(array(
            'nik','nmlengkap','callname','jk','stswn','tgllahir1','nmagama','alamatlahir','noktp',
            'desc_ktp','rtktp','rwktp','namakeldesaktp','namakecktp','namakotakabktp','namaprovktp','id_posktp','alamatlengkapktp',
            'typedomisili','transportasi',
            'desc_domisili','rtdom','rwdom','namakotakabdom','namakeldesadom','namakecdom','namaprovdom','id_posdom','alamatlengkapdomisili',
            'sts_kawin','nonpwp','sts_ptkp','tglmasukkerja1','nmkepegawaian','type_office','locaname',
            'nmdivision','nmdept','nmsubdept','nmjabatan','nmgrade','nmgroupgol','nmcostcenter','idkk','hp1','hp2',
            'email_office','email_self','nmbank','norekening','nmpendidikan','pendidikan_terakhir','jurusan','lulus_tahun','ipk','nmayahkandung','nmibukandung',
            'id_bpjs_kes','cab_bpjs_kes','id_bpjs_naker','cab_bpjs_naker','nmatasan1','nmatasan2','nmatasan3','sisacuti',
            'umurkaryawan','lama_bekerja','nmpasangan','hubunganpasangan','contactpasangan','pekerjaanpasangan',
            'penyakitbawaan','nmkeluargapenyakitbawaan','nakes','resign','alasan_resign','tglkeluarkerja1','nmgoldarah',
            'pernahcovid','statuscovid','lastdatecovid','lastdescriptioncovid',
            'area_covid','lastdatevaksin','lastdescriptionvaksin'
        ));

        $this->fiky_excel->set_width(array(
            10,20,20,20,20,20,20,20,20,
            50,20,20,20,20,20,20,20,50,
            20,20,
            50,20,20,20,20,20,20,20,50,
            20,20,20,20,20,20,20,
            20,20,20,20,20,20,20,30,30,30,
            20,20,20,50,20,20,20,20,20,20,20,
            20,20,20,20,20,20,20,20,
            20,20,20,20,20,20,
            20,20,20,20,20,20,20,
            20,20,20,20,
            20,20,20,
        ));
        $this->fiky_excel->exportXlsx('Data Karyawan '. date('dmY'));
    }

    public function excel_listkaryawan_sh(){
        $resignFilter = $this->request->getPost('resign_filter');
        $vaksin_filter = $this->request->getPost('vaksin_filter');
        $id_kotaktp = $this->request->getPost('id_kotaktp');
        $id_kotadom = $this->request->getPost('id_kotadom');
        $plant = $this->request->getPost('plant');
        $tgl = explode(' - ',$daterange); $tgl1 = $tgl[0]; $tgl2 = $tgl[1];

        if ($resignFilter==='ALL') {
            $resign = "";
        } else if ($resignFilter==='YES') {
            $resign = " and resign='YES'";
        } else {
            $resign = " and resign='NO'";
        }
        if (!empty($plant)) { $pplan=" and plant='$plant'"; } else {  $pplan=""; }
        if (!empty($vaksin_filter)) {
            if($vaksin_filter==='NO') {
                $area_covid=" and coalesce(area_covid,'BELUM') = 'BELUM'";
            } else {
                $area_covid=" and coalesce(area_covid,'BELUM') != 'BELUM'";
            }
        } else {
            $area_covid="";
        }
        if (!empty($id_kotaktp)) { $kotaktp=" and id_kotaktp='$id_kotaktp'"; } else {  $kotaktp=""; }
        if (!empty($id_kotadom)) { $kotadom=" and id_kotadom='$id_kotadom'"; } else {  $kotadom=""; }

        $date1 = date('Y-m-d',strtotime($tgl1));
        $date2 = date('Y-m-d',strtotime($tgl2));

        $parameter = $resign.$pplan.$area_covid.$kotaktp.$kotadom;

        $datane = $this->m_karyawan->list_karyawan_param2(" and left(nik,1)!='A' $parameter");
        $this->fiky_excel->set_query($datane);
        $this->fiky_excel->set_header(array(
            'NIK','NAMA LENGKAP','JENIS KELAMIN','TANGGAL LAHIR','NO KTP',
            'ALAMAT KTP','RT/RW KTP','KEL/DESA KTP','KECAMATAN KTP','KOTA/KAB KTP','PROVINSI KTP','KODEPOS KTP','ALAMAT LENGKAP KTP',
            'ALAMAT DOMISILI','RT/RW DOMISILI','KEL/DESA DOMISILI','KECAMATAN DOMISILI','KOTA/KAB DOMISILI','PROVINSI DOMISILI','KODEPOS DOMISILI','ALAMAT LENGKAP DOMISILI',
            'STATUS KAWIN','PLANT',
            'DEPARTMEN','SECTION','POSITION','GROUP','HP1','UMUR KARYAWAN','GOLONGAN DARAH',
            'RIWAYAT COVID','STATUS COVID','TGL TERAKHIR TERINDIKASI COVID','DESKRIPSI COVID',
            'VAKSIN COVID','TGL TERAKHIR VAKSIN COVID','DESKIRPSI VAKSIN COVID'

        ));

        $this->fiky_excel->set_column(array(
            'nik','nmlengkap','jk','tgllahir1','noktp',
            'desc_ktp','rtrwktp','namakeldesaktp','namakecktp','namakotakabktp','namaprovktp','id_posktp','alamatlengkapktp',
            'desc_domisili','rtrwdom','namakotakabdom','namakeldesadom','namakecdom','namaprovdom','id_posdom','alamatlengkapdomisili',
            'sts_kawin','locaname',
            'nmdept','nmsubdept','nmjabatan','nmgroupgol','hp1','umurkaryawan','nmgoldarah',
            'pernahcovid','statuscovid','lastdatecovid','lastdescriptioncovid',
            'area_covid','lastdatevaksin','lastdescriptionvaksin'
        ));

        $this->fiky_excel->set_width(array(
            10,20,20,20,20,
            20,20,20,20,20,20,20,20,
            20,20,20,20,20,20,20,20,
            20,20,
            20,20,20,20,20,20,20,
            20,20,20,20,
            20,20,20,

        ));
        $this->fiky_excel->exportXlsx('Data Karyawan '. date('dmY'));
    }


    function up_foto(){
        $data['title']="Profile User";
        $nik=trim($this->request->getPost('nik'));
        $nm=$this->m_karyawan->get_by_id($nik)->getRowArray();
        $nama=$nm['nmlengkap'];
        //setting konfigurasi upload image
        $config['upload_path'] = './assets/img/profile/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']	= '5000';
        $config['max_width']  = '3624';
        $config['max_height']  = '3200';

        $this->upload->initialize($config);
        if(!$this->upload->do_upload('gambar')){
            $gambar="";
            $data['message']="<div class='alert alert-danger'>Gambar Tidak Sesuai</div>";
            echo 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 2000 pixel</br>
					* Tinggi Max 2000 pixel</br>					
					';
        }else{
            //Image Resizing
            $config['source_image'] = $this->upload->upload_path.$this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 480;
            $config['height'] = 640;

            $this->load->library('image_lib', $config);

            if ( ! $this->image_lib->resize()){
                $this->session->set_flashdata('message', $this->image_lib->display_errors('', ''));
            } else {
                $res = $this->upload->data();
                $file_path     = $res['file_path'];
                $file         = $res['full_path'];
                $file_ext     = $res['file_ext'];
                $final_file_name = trim(trim($nik)).$file_ext;
                if(file_exists($file_path.$final_file_name)){
                    unlink($file_path.$final_file_name);
                }
                rename($file,$file_path.$final_file_name);
                //$gambar=$this->upload->file_name;
                $gambar=$final_file_name;
                $info=array('image'=>$gambar);
                //update foto pegawai
                $this->m_karyawan->save_foto($nik,$info);
                $data['message']="<div class='alert alert-success'>Data Berhasil diupdate</div>";
                //return redirect()->to(base_url("hrd/hrd/detail_peg/$nip");
            }

            $x=base_url('assets/img/profile/'.$gambar);
            //echo "<img id='gbr' src='$x' width='100%' height='100%' alt='User Image'>";
            return redirect()->to(base_url("trans/karyawan/detail/$nik"));
        }
        //tampilkan pesan

        //tampilkan data anggota
        //$data['anggota']=$this->m_user->cekId($id)->getRowArray();
        //return $this->template->render('hrd/hrd/view_detail_pegawai',$data);
    }

    function ajax_req_recruitment($ktp){

        $data = $this->m_calonkaryawan->q_linkinputkaryawan($ktp)->getRowArray();
        echo json_encode($data);

    }

    function ajax_cekktpkembar($noktp){

        $data = $this->m_calonkaryawan->q_ajaxktp($noktp)->getNumRows();
        echo json_encode($data);
        //echo 'tae';
    }
    public function excel_karyborong(){

        $datane=$this->m_karyawan->list_karyborong();
        $this->excel_generator->set_query($datane);
        $this->excel_generator->set_header(array('Nik','Nama lengkap','Panggilan','kelamin','Negara lahir','Prov lahir','Kota lahir','Tgllahir','Kode Agama','No ktp',
            'Status Pernikahan','Golongan Darah','Negara','Provinsi','Kota',
            'Kecamatan','Kelurahan','Alamat','No HP1','No HP2','NPWP','TGL NPWP','Bagian','Sub Bagian','Jabatan','Lvl Jabatan','Grade golongan','Atasan1','Atasan2','Status PTKP',
            'Besaran PTKP','Tgl masuk kerja','Tgl keluar kerja','Masakerja','Status kepegawaian','Group penggajian','Gaji pokok','Gaji bpjs','Bank','Nama pemilik rekening','no rekening','Shift','ID absen','Email',
            'Sisa Cuti','id mesin','Card number','Status','Cost center','tj tetap','Gaji tetap','Gaji naker','Borong','Mobile Deviceid','Inputby','Inputdate','Updateby','Updatedate'
        ));



        $this->excel_generator->set_column(array('nik','nmlengkap','callname','jk','neglahir','provlahir','kotalahir','tgllahir','kd_agama','noktp',
            'status_pernikahan','gol_darah','negtinggal','provtinggal','kotatinggal',
            'kectinggal','keltinggal','alamattinggal','nohp1','nohp2','npwp','tglnpwp','bag_dept','subbag_dept','jabatan','lvl_jabatan','grade_golongan','nik_atasan','nik_atasan2','status_ptkp',
            'besaranptkp','tglmasukkerja','tglkeluarkerja','masakerja','statuskepegawaian','grouppenggajian','closegaji','gajibpjs','namabank','namapemilikrekening','norek','tjshift','idabsen','email',
            'sisacuti','idmesin','cardnumber','status','costcenter','tj_tetap','closegaji','closegaji','tjborong','deviceid','inputby','inputdate','updateby','updatedate'
        ));

        $this->excel_generator->set_width(array(10,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,20,20,20,20,
            10,20,20,20,20,20,20,20,20,20,20,20,20,20,
            20,20,20,20,20,20,20,20,7,20,20,20,20,20
        ));
        $this->excel_generator->exportTo2007('Master Karyawan Borong');
    }

    /* MUTASI */
    function simpanmutasi (){
        $type=trim($this->request->getPost('type'));
        $nik=trim($this->request->getPost('newnik'));
        $newkddept=strtoupper(trim($this->request->getPost('newkddept')));
        $newkdsubdept=strtoupper(trim($this->request->getPost('newkdsubdept')));
        $newkdjabatan=strtoupper(trim($this->request->getPost('newkdjabatan')));
        $newkdlevel=strtoupper(trim($this->request->getPost('newkdlevel')));
        $newnikatasan=strtoupper(trim($this->request->getPost('newnikatasan')));
        $newnikatasan2=strtoupper(trim($this->request->getPost('newnikatasan')));
        $nosk=strtoupper(trim($this->request->getPost('nodoksk')));
        $tglsk=strtoupper(trim($this->request->getPost('tglsk')));
        $tglmemo=strtoupper(trim($this->request->getPost('tglmemo')));
        $tglefektif=strtoupper(trim($this->request->getPost('tglefektif')));
        $ket=strtoupper(trim($this->request->getPost('ket')));
        $info=array(
            'nik'=>$nik,
            'newkddept'=>$newkddept,
            'newkdsubdept'=>$newkdsubdept,
            'newkdjabatan'=>$newkdjabatan,
            'newkdlevel'=>$newkdlevel,
            'newnikatasan'=>$newnikatasan,
            'newnikatasan2'=>$newnikatasan2,
            'nodoksk'=>$nosk,
            'tglsk'=>$tglsk,
            'tglmemo'=>$tglmemo,
            'tglefektif'=>$tglefektif,
            'ket'=>$ket,
            'inputdate'=>date('Y-m-d H:i:s'),
            'inputby'=>$this->session->get('nama')
        );

        $this->db->insert('sc_tmp.mutasi',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/success"));

    }

    function deletemutasi($id,$nik){
        $this->db->where('nik',$nik);
        $this->db->where('nodokumen',$id);
        $this->db->delete('sc_mst.mutasi');
        return redirect()->to(base_url("trans/karyawan/detail/$nik/success"));
    }
    function approvemutasi($id,$nik){
        $this->db->query("update sc_mst.mutasi set status='P' where nodokumen='$id' and nik='$nik'");
        return redirect()->to(base_url("trans/karyawan/detail/$nik/success"));
    }

    /*ADD MUTASI & PROMOSI KARYAWAN*/

    function add_stspeg(){
        $nik1=explode('|',$this->request->getPost('nik'));
        $nik=trim($this->request->getPost('nik'));
        $nosk=$this->request->getPost('noskstspeg');
        $kdkepegawaian=$this->request->getPost('kdkepegawaian');
        $tgl_mulai=$this->request->getPost('tgl_mulai');
        $tgl_selesai=$this->request->getPost('tgl_selesai');
        if ($tgl_mulai==''){
            $tgl_mulai=null;
        }
        if ($tgl_selesai==''){
            $tgl_selesai=null;
        }
        $cuti=$this->request->getPost('cuti');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');

        //echo $sub;
        $info=array(
            'nik'=>$nik,
            'nodok'=>$this->session->get('nama'),
            'nosk'=>$nosk,
            'kdkepegawaian'=>strtoupper($kdkepegawaian),
            'tgl_mulai'=>$tgl_mulai,
            'tgl_selesai'=>$tgl_selesai,
            'cuti'=>strtoupper($cuti),
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_tmp.status_kepegawaian',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/success"));
        //echo $inputby;
    }

    function editview_stspeg($nik,$no_urut){
        //echo "test";

        if (empty($no_urut)){
            return redirect()->to(base_url("trans/stspeg/index/$nik"));
        } else {
            $data['title']='EDIT DATA RIWAYAT KELUARGA';
            if($this->uri->segment(5)=="upsuccess"){
                $data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
            }
            else {
                $data['message']='';
            }
            $nik=$this->uri->segment(4);
            $data['nik']=$nik;
            $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->getResult();
            $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->getResult();
            $data['list_bpjskaryawan']=$this->m_bpjs->q_bpjs_karyawan()->getResult();
            $data['list_faskes']=$this->m_bpjs->list_faskes()->getResult();
            $data['list_kelas']=$this->m_bpjs->q_trxtype()->getResult();
            $data['list_karyawan']=$this->m_bpjs->list_karyawan()->getResult();
            $data['list_keluarga']=$this->m_stspeg->list_keluarga()->getResult();
            $data['list_negara']=$this->m_stspeg->list_negara()->getResult();
            $data['list_prov']=$this->m_stspeg->list_prov()->getResult();
            $data['list_tgl_mulai']=$this->m_stspeg->list_tgl_mulai()->getResult();
            $data['list_jenjang_kepegawaian']=$this->m_stspeg->list_jenjang_kepegawaian()->getResult();
            $data['list_rk']=$this->m_stspeg->q_stspeg_edit($nik,$no_urut)->getRowArray();
            return $this->template->render('trans/stspeg/v_edit',$data);
        }
    }

    function detail_stspeg($nik,$no_urut){
        //echo "test";

        if (empty($no_urut)){
            return redirect()->to(base_url("trans/stspeg/index/$nik"));
        } else {
            $data['title']='DETAIL DATA RIWAYAT KELUARGA';
            if($this->uri->segment(5)=="upsuccess"){
                $data['message']="<div class='alert alert-success'>Data Berhasil di update </div>";
            }
            else {
                $data['message']='';
            }
            $nik=$this->uri->segment(4);
            $data['nik']=$nik;
            $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->getResult();
            $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->getResult();
            $data['list_bpjskaryawan']=$this->m_bpjs->q_bpjs_karyawan()->getResult();
            $data['list_faskes']=$this->m_bpjs->list_faskes()->getResult();
            $data['list_kelas']=$this->m_bpjs->q_trxtype()->getResult();
            $data['list_karyawan']=$this->m_bpjs->list_karyawan()->getResult();
            $data['list_keluarga']=$this->m_stspeg->list_keluarga()->getResult();
            $data['list_negara']=$this->m_stspeg->list_negara()->getResult();
            $data['list_prov']=$this->m_stspeg->list_prov()->getResult();
            $data['list_tgl_mulai']=$this->m_stspeg->list_tgl_mulai()->getResult();
            $data['list_jenjang_kepegawaian']=$this->m_stspeg->list_jenjang_kepegawaian()->getResult();
            $data['list_rk']=$this->m_stspeg->q_stspeg_edit($nik,$no_urut)->getRowArray();
            return $this->template->render('trans/stspeg/v_detail',$data);
        }
    }
    function edit_stspeg(){
        //$nik1=explode('|',);
        $nodok=$this->request->getPost('nodok');
        $nik=$this->request->getPost('nik');
        $kdkepegawaian=$this->request->getPost('kdkepegawaian');
        $tgl_selesai=$this->request->getPost('tgl_selesai');

        if ($tgl_selesai==''){
            $tgl_selesai=null;
        }

        $cuti=$this->request->getPost('cuti');
        $tgl_mulai=$this->request->getPost('tgl_mulai');
        if ($tgl_mulai==''){
            $tgl_mulai=null;
        }
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        //$no_urut=$this->request->getPost('no_urut');

        $info=array(
            'kdkepegawaian'=>strtoupper($kdkepegawaian),
            'tgl_mulai'=>($tgl_mulai),
            'tgl_selesai'=>($tgl_selesai),
            'cuti'=>strtoupper($cuti),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        //$this->db->where('custcode',$kode);


        $this->db->where('nodok',$nodok);
        $this->db->where('nik',$nik);
        //$this->db->where('kdkepegawaian',$kdkepegawaian);
        $this->db->update('sc_trx.status_kepegawaian',$info);
        return redirect()->to(base_url("trans/stspeg/index/$nik/rep_succes"));

        //echo $inputby;
    }

    function hps_stspeg($nik,$nodok){
        $this->db->where('nodok',$nodok);
        $this->db->delete('sc_trx.status_kepegawaian');
        return redirect()->to(base_url("trans/stspeg/index/$nik/del_succes"));
    }

    /* ---------------------ADD BPJS KARYAWAN ------------------------*/

    function add_bpjs(){
        $id_bpjs=trim(strtoupper(str_replace(" ","",$this->request->getPost('id_bpjs'))));
        //$nmbpjs=$this->request->getPost('nmbpjs');
        //$kdsubdept=$this->request->getPost('kdsubdept');
        //$subdept=explode('|',$this->request->getPost('kdsubdept'));
        //$sub=$subdept[1];
        //$kode_bpjs1=explode('|',);
        $kode_bpjs=strtoupper($this->request->getPost('kode_bpjs'));
        //$kodekomponen1=explode('|',;
        $kodekomponen=strtoupper($this->request->getPost('kodekomponen'));
        //$kodefaskes1=explode
        $kodefaskes=strtoupper($this->request->getPost('kodefaskes'));
        //$kodefaskes3=explode('|',;
        $kodefaskes2=strtoupper($this->request->getPost('kodefaskes2'));
        $nik=$this->request->getPost('nik');
        $kelas=$this->request->getPost('kelas');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_berlaku=$this->request->getPost('tgl_berlaku');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');


        //echo $sub;
        $info=array(
            'id_bpjs'=>$id_bpjs,
            'kode_bpjs'=>$kode_bpjs,
            'kodekomponen'=>$kodekomponen,
            'kodefaskes'=>$kodefaskes,
            'kodefaskes2'=>$kodefaskes2,
            'nik'=>$nik,
            'kelas'=>strtoupper($kelas),
            'keterangan'=>strtoupper($keterangan),
            'tgl_berlaku'=>$tgl_berlaku,
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $cek=$this->m_bpjs->q_cek_bpjs($kode_bpjs,$nik,$kodekomponen,$id_bpjs)->getNumRows();
        if ($cek>0){
            return redirect()->to(base_url("trans/karyawan/detail/$nik/kode_failed"));
        } else {
            $this->db->insert('sc_trx.bpjs_karyawan',$info);
            return redirect()->to(base_url("trans/karyawan/detail/$nik/success"));
        }

    }

    function edit_bpjs(){
        $id_bpjs=trim(strtoupper(str_replace(" ","",$this->request->getPost('id_bpjs'))));
        //$nmbpjs=$this->request->getPost('nmbpjs');
        //$kdsubdept=$this->request->getPost('kdsubdept');
        //$subdept=explode('|',$this->request->getPost('kdsubdept'));
        //$sub=$subdept[1];
        //$kode_bpjs1=explode('|',);
        $kode_bpjs=strtoupper($this->request->getPost('kode_bpjs'));
        //$kodekomponen1=explode('|',;
        $kodekomponen=strtoupper($this->request->getPost('kodekomponen'));
        //$kodefaskes1=explode
        $kodefaskes=strtoupper($this->request->getPost('kodefaskes'));
        //$kodefaskes3=explode('|',;
        $kodefaskes2=strtoupper($this->request->getPost('kodefaskes2'));
        $nik=$this->request->getPost('nik');
        $kelas=$this->request->getPost('kelas');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_berlaku=$this->request->getPost('tgl_berlaku');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $info=array(
            'kode_bpjs'=>$kode_bpjs,
            'kodekomponen'=>$kodekomponen,
            'kodefaskes'=>$kodefaskes,
            'kodefaskes2'=>$kodefaskes2,
            'nik'=>$nik,
            'kelas'=>strtoupper($kelas),
            'keterangan'=>strtoupper($keterangan),
            'tgl_berlaku'=>$tgl_berlaku,
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        //$this->db->where('custcode',$kode);

        $this->db->where('nik',$nik);
        $this->db->where('kode_bpjs',$kode_bpjs);
        $this->db->where('kodekomponen',$kodekomponen);
        $this->db->where('id_bpjs',$id_bpjs);
        $this->db->update('sc_trx.bpjs_karyawan',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/success"));

    }

    function hps_bpjs($nik,$id_bpjs){

        $this->db->where('id_bpjs',$id_bpjs);
        $this->db->delete('sc_trx.bpjs_karyawan');
        return redirect()->to(base_url("trans/karyawan/detail/$nik/del_success"));
    }


    function karyawan_self(){
        $data['title']='Profile Karyawan';
        $nama=$this->session->get('nama');
        $id=$nama;
        $data['list_opt_neg']=$this->m_geo->list_opt_negara()->getResult();
        $data['list_opt_prov']=$this->m_geo->list_opt_prov()->getResult();
        $data['list_opt_agama']=$this->m_agama->q_agama()->getResult();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->getResult();
        $data['list_opt_dept']=$this->m_department->q_department()->getResult();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->getResult();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->getResult();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->getResult();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->getResult();
        $data['list_chainjobgrade']=$this->m_jabatan->chain_jobgrade()->getResult();//
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->getResult();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->getResult();
        //$data['list_opt_ptkp']=$this->m_karyawan->list_ptkp()->getResult();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->getResult();
        $data['list_opt_bank']=$this->m_bank->q_bank()->getResult();
        $data['list_resignkary']=$this->m_karyawan->list_karyresgn()->getResult();//
        $data['list_borong']=$this->m_karyawan->list_karyborong()->getResult();//
        $data['calon_karyawan']=$this->m_calonkaryawan->q_maxktpcalon()->getResult();//
        $data['list_finger']=$this->m_karyawan->q_finger()->getResult();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->getResult();//
        $data['list_self']=$this->m_karyawan->get_dtl_id($id)->getResult();

        if($this->uri->segment(4)=="exist") {
            $data['message']="<div class='alert alert-warning'>Data Sudah Ada!</div>";
        }
        else if($this->uri->segment(4)=="success"){
            $data['message']="<div class='alert alert-success'>Data Berhasil disimpan </div>";
        }
        else if($this->uri->segment(4)=="upsuccess"){
            $data['message']="<div class='alert alert-success'>Data Berhasil diupdate </div>";
        }
        else if($this->uri->segment(4)=="notacces"){
            $data['message']="<div class='alert alert-success'>Anda tidak Berhak untuk mengakses modul ini</div>";
        }
        else if($this->uri->segment(4)=="del"){
            $data['message']="<div class='alert alert-success'>Hapus Data Sukses</div>";
        }
        else if($this->uri->segment(4)=="del_exist"){
            $data['message']="<div class='alert alert-danger'>Ada data yang terkait, Hapus child data terlebih dahulu</div>";
        }
        else {
            $data['message']='';
        }


        return $this->template->render('trans/karyawan/v_karyawan_self',$data);
    }

    function detail_self($id)
    {
        $nik=$this->uri->segment(4);
        $data['message']='';
        $data['list_opt_agama']=$this->m_agama->q_agama()->getResult();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->getResult();
        $data['list_opt_dept']=$this->m_department->q_department()->getResult();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->getResult();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->getResult();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->getResult();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->getResult();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->getResult();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->getResult();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->getResult();
        $data['list_opt_bank']=$this->m_bank->q_bank()->getResult();
        $data['list_riwayat_keluarga']=$this->m_riwayat_keluarga->q_riwayat_keluarga($nik,$no_urut=null)->getResult();
        $data['list_riwayat_kesehatan']=$this->m_riwayat_kesehatan->q_riwayat_kesehatan($nik)->getResult();
        $data['list_riwayat_pengalaman']=$this->m_riwayat_pengalaman->q_riwayat_pengalaman($nik)->getResult();
        $data['list_riwayat_pendidikan']=$this->m_riwayat_pendidikan->q_riwayat_pendidikan($nik)->getResult();
        $data['list_finger']=$this->m_karyawan->q_finger()->getResult();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->getResult();//

        /*MUTASI*/
        //$data['list_karyawan']=$this->m_mutpromot->list_karyawan()->getResult();
        $data['list_karyawan']=$this->m_mutpromot->list_nik($id)->getResult();
        $data['list_mutasi']=$this->m_mutpromot->get_mutasinik($id)->getResult();
        /*END MUTASI*/
        /*STATUS KEPEGAWAIN*/
        $data['list_lk']=$this->m_stspeg->list_karyawan_index($id)->getRowArray();
        $data['list_kepegawaian']=$this->m_stspeg->list_kepegawaian()->getResult();
        $data['list_stspeg']=$this->m_stspeg->q_stspeg($id)->getResult();
        $data['list_spg']=$this->m_stspeg->q_stspeg($id)->getRowArray();
        /**/
        /*BPJS KARYAWAN*/
        $data['list_bpjs']=$this->m_bpjs->list_jnsbpjs()->getResult();
        $data['list_bpjskomponen']=$this->m_bpjs->list_bpjskomponen()->getResult();
        $data['list_bpjskaryawan']=$this->m_bpjs->list_bpjs_karyawan($id)->getResult();
        $data['list_faskes']=$this->m_bpjs->list_faskes()->getResult();
        $data['list_kelas']=$this->m_bpjs->q_trxtype()->getResult();
        $data['list_karyawan_bpjs']=$this->m_bpjs->list_karyawan()->getResult();
        $data['list_lk']=$this->m_bpjs->list_karyawan_index($id)->getRowArray();


        $data['title'] = "Detail Karyawan";
        //$data['dtl'] = $this->m_karyawan->get_dtl_id($id)->getRowArray();
        $data['lp'] = $this->m_karyawan->get_dtl_id($id)->getRowArray();
        return $this->template->render('trans/karyawan/v_detailhrdkary_self',$data);
    }
    function edit_self($id)
    {
        $data['message']='';
        $data['list_opt_neg']=$this->m_geo->list_opt_negara()->getResult();
        $data['list_opt_prov']=$this->m_geo->list_opt_prov()->getResult();
        $data['list_opt_kotakab']=$this->m_geo->list_opt_kotakab()->getResult();
        $data['list_opt_kec']=$this->m_geo->list_opt_kec()->getResult();
        //$data['list_opt_keldesa']=$this->m_geo->list_opt_keldesa()->getResult();
        $data['list_opt_agama']=$this->m_agama->q_agama()->getResult();
        $data['list_opt_nikah']=$this->m_nikah->q_nikah()->getResult();
        $data['list_opt_dept']=$this->m_department->q_department()->getResult();
        $data['list_opt_subdept']=$this->m_department->q_subdepartment()->getResult();
        $data['list_opt_jabt']=$this->m_jabatan->q_jabatan()->getResult();
        $data['list_opt_lvljabt']=$this->m_jabatan->q_lvljabatan()->getResult();
        $data['list_opt_goljabt']=$this->m_jabatan->q_jobgrade()->getResult();
        $data['list_opt_atasan']=$this->m_karyawan->list_karyawan()->getResult();
        $data['list_opt_ptkp']=$this->m_bpjs->list_ptkp()->getResult();
        $data['list_opt_grp_gaji']=$this->m_group_penggajian->q_group_penggajian()->getResult();
        $data['list_opt_bank']=$this->m_bank->q_bank()->getResult();
        $data['list_finger']=$this->m_karyawan->q_finger()->getResult();//
        $data['list_kanwil']=$this->m_karyawan->q_kanwil()->getResult();//
        $data['title'] = "Edit Karyawan";
        $data['dtl'] = $this->m_karyawan->get_by_id($id)->getRowArray();
        return $this->template->render('trans/karyawan/v_editkary_self',$data);
    }

    function update_self_person(){
        $nik=trim($this->request->getPost('nik'));
        $nohp1=trim($this->request->getPost('nohp1'));
        $nohp2=trim($this->request->getPost('nohp2'));
        $email=trim($this->request->getPost('email'));
        $info=array(
            'nohp1'=>$nohp1,
            'nohp2'=>$nohp2,
            'email'=>$email
        );
        $this->db->where('nik',$nik);
        $this->db->update('sc_mst.karyawan',$info);
        return redirect()->to(base_url("trans/karyawan/karyawan_self/upsuccess"));
    }

    function add_riwayat_keluarga(){
        $nik1=explode('|',$this->request->getPost('nik'));
        $nik=$nik1[0];
        $kdkeluarga=$this->request->getPost('kdkeluarga');
        $nama=$this->request->getPost('nama');
        $kelamin=$this->request->getPost('kelamin');
        $kodenegara=$this->request->getPost('kodenegara');
        $kodeprov=$this->request->getPost('kodeprov');
        $kodekotakab=$this->request->getPost('kodekotakab');
        $tgl_lahir=$this->request->getPost('tgl_lahir');
        $kdjenjang_pendidikan=$this->request->getPost('kdjenjang_pendidikan');
        $pekerjaan=$this->request->getPost('pekerjaan');
        $status_hidup=$this->request->getPost('status_hidup');
        $status_tanggungan=$this->request->getPost('status_tanggungan');
        $npwp_tgl1=$this->request->getPost('npwp_tgl');
        if ($npwp_tgl1==''){
            $npwp_tgl=NULL;
        } else {
            $npwp_tgl=$npwp_tgl1;
        }
        $id_bpjs=trim(strtoupper(str_replace(" ","",$this->request->getPost('id_bpjs'))));
        $no_npwp1=str_replace("_","",$this->request->getPost('no_npwp'));
        if ($no_npwp1==''){
            $no_npwp=NULL;
        } else {
            $no_npwp=$no_npwp1;
        }
        $kode_bpjs1=explode('|',$this->request->getPost('kode_bpjs'));
        $kode_bpjs=$kode_bpjs1[0];
        $kodekomponen1=explode('|',$this->request->getPost('kodekomponen'));
        $kodekomponen=$kodekomponen1[0];
        $kodefaskes1=explode('|',$this->request->getPost('kodefaskes'));
        $kodefaskes=$kodefaskes1[0];
        $kodefaskes3=explode('|',$this->request->getPost('kodefaskes2'));
        $kodefaskes2=$kodefaskes3[0];
        $kelas=$this->request->getPost('kelas');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_berlaku1=$this->request->getPost('tgl_berlaku');
        if ($tgl_berlaku1==''){
            $tgl_berlaku=NULL;
        } else {
            $tgl_berlaku=$tgl_berlaku1;
        }
        $tgl_input=date('Y-m-d');
        $inputby=$this->session->get('nama');
        if (empty($kdkeluarga)){
            return redirect()->to(base_url("trans/karyawan/index/"));
        }

        $info=array(
            'nik'=>$nik,
            'kdkeluarga'=>$kdkeluarga,
            'nama'=>strtoupper($nama),
            'kelamin'=>$kelamin,
            'kodenegara'=>$kodenegara,
            'kodeprov'=>$kodeprov,
            'kodekotakab'=>$kodekotakab,
            'tgl_lahir'=>$tgl_lahir,
            'kdjenjang_pendidikan'=>$kdjenjang_pendidikan,
            'pekerjaan'=>strtoupper($pekerjaan),
            'status_hidup'=>strtoupper($status_hidup),
            'status_tanggungan'=>strtoupper($status_tanggungan),
            'no_npwp'=>$no_npwp,
            'npwp_tgl'=>$npwp_tgl,
            'id_bpjs'=>$id_bpjs,
            'kode_bpjs'=>$kode_bpjs,
            'kodekomponen'=>$kodekomponen,
            'kodefaskes'=>$kodefaskes,
            'kodefaskes2'=>$kodefaskes2,
            'kelas'=>strtoupper($kelas),
            'keterangan'=>strtoupper($keterangan),
            'tgl_berlaku'=>$tgl_berlaku,
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_keluarga',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));


    }

    function add_riwayat_kesehatan(){
        $nik=$this->request->getPost('nik');
        $kdpenyakit=$this->request->getPost('kdpenyakit');
        $periode=$this->request->getPost('periode');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $info=array(
            'nik'=>$nik,
            'kdpenyakit'=>strtoupper($kdpenyakit),
            'periode'=>$periode,
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );
        $this->db->insert('sc_trx.riwayat_kesehatan',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }

    function edit_riwayat_kesehatan(){
        $nik=$this->request->getPost('nik');
        $kdpenyakit=$this->request->getPost('kdpenyakit');
        $periode=$this->request->getPost('periode');
        $keterangan=$this->request->getPost('keterangan');
        $inputby=$this->request->getPost('inputby');
        $no_urut=$this->request->getPost('no_urut');

        $info=array(
            'kdpenyakit'=>strtoupper($kdpenyakit),
            'periode'=>strtoupper($periode),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_kesehatan',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }

    function hps_riwayat_kesehatan($nik,$no_urut){
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_kesehatan');
        return redirect()->to(base_url("trans/karyawan/detail/$nik/del_succes"));
    }

    function add_riwayat_pengalaman(){

        $nik=$this->request->getPost('nik');
        $nmperusahaan=$this->request->getPost('nmperusahaan');
        $bidang_usaha=$this->request->getPost('bidang_usaha');
        $tahun_masuk=$this->request->getPost('tahun_masuk');
        $tahun_keluar=$this->request->getPost('tahun_keluar');
        $bagian=$this->request->getPost('bagian');
        $jabatan=$this->request->getPost('jabatan');
        $nmatasan=$this->request->getPost('nmatasan');
        $jbtatasan=$this->request->getPost('jbtatasan');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');

        $info=array(
            'nik'=>$nik,
            'nmperusahaan'=>strtoupper($nmperusahaan),
            'bidang_usaha'=>strtoupper($bidang_usaha),
            'bagian'=>strtoupper($bagian),
            'jabatan'=>strtoupper($jabatan),
            'nmatasan'=>strtoupper($nmatasan),
            'jbtatasan'=>strtoupper($jbtatasan),
            'tahun_masuk'=>$tahun_masuk,
            'tahun_keluar'=>$tahun_keluar,
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_pengalaman',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }

    function edit_riwayat_pengalaman(){
        $nik=$this->request->getPost('nik');
        $nmperusahaan=$this->request->getPost('nmperusahaan');
        $bidang_usaha=$this->request->getPost('bidang_usaha');
        $tahun_masuk=$this->request->getPost('tahun_masuk');
        $tahun_keluar=$this->request->getPost('tahun_keluar');
        $bagian=$this->request->getPost('bagian');
        $jabatan=$this->request->getPost('jabatan');
        $nmatasan=$this->request->getPost('nmatasan');
        $jbtatasan=$this->request->getPost('jbtatasan');
        $keterangan=$this->request->getPost('keterangan');
        $inputby=$this->request->getPost('inputby');
        $no_urut=$this->request->getPost('no_urut');

        $info=array(
            'nmperusahaan'=>strtoupper($nmperusahaan),
            'bidang_usaha'=>strtoupper($bidang_usaha),
            'bagian'=>strtoupper($bagian),
            'jabatan'=>strtoupper($jabatan),
            'nmatasan'=>strtoupper($nmatasan),
            'jbtatasan'=>strtoupper($jbtatasan),
            'tahun_masuk'=>$tahun_masuk,
            'tahun_keluar'=>$tahun_keluar,
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_pengalaman',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }

    function hps_riwayat_pengalaman($nik,$no_urut){
        $this->db->where('nik',$nik);
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_pengalaman');
        return redirect()->to(base_url("trans/karyawan/detail/$nik/del_succes"));
    }


    function add_riwayat_pendidikan_nf(){

        $nik=$this->request->getPost('nik');
        $kdkeahlian=$this->request->getPost('kdkeahlian');
        $nmkursus=$this->request->getPost('nmkursus');
        $nminstitusi=$this->request->getPost('nminstitusi');
        $tahun_masuk=str_replace("_","",$this->request->getPost('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->request->getPost('tahun_keluar'));
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');

        $info=array(
            'nik'=>$nik,
            'kdkeahlian'=>$kdkeahlian,
            'nmkursus'=>strtoupper($nmkursus),
            'nminstitusi'=>strtoupper($nminstitusi),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_pendidikan_nf',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }

    function edit_riwayat_pendidikan_nf(){

        $nik=$this->request->getPost('nik');
        $kdkeahlian=$this->request->getPost('kdkeahlian');
        $nmkursus=$this->request->getPost('nmkursus');
        $nminstitusi=$this->request->getPost('nminstitusi');
        $tahun_masuk=str_replace("_","",$this->request->getPost('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->request->getPost('tahun_keluar'));
        $keterangan=$this->request->getPost('keterangan');
        $inputby=$this->request->getPost('inputby');
        $no_urut=$this->request->getPost('no_urut');

        $info=array(
            'nmkursus'=>strtoupper($nmkursus),
            'kdkeahlian'=>strtoupper($kdkeahlian),
            'nminstitusi'=>strtoupper($nminstitusi),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );

        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_pendidikan_nf',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));
    }

    function hps_riwayat_pendidikan_nf($nik,$no_urut){
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_pendidikan_nf');
        return redirect()->to(base_url("trans/karyawan/detail/$nik/del_succes"));
    }

    function add_riwayat_pendidikan(){

        $nik=$this->request->getPost('nik');
        $kdpendidikan=$this->request->getPost('kdpendidikan');
        $nmsekolah=$this->request->getPost('nmsekolah');
        $jurusan=$this->request->getPost('jurusan');
        $program_studi=$this->request->getPost('program_studi');
        $kodeprov=$this->request->getPost('kodeprov');
        $kotakab=$this->request->getPost('kotakab');
        $tahun_masuk=str_replace("_","",$this->request->getPost('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->request->getPost('tahun_keluar'));
        $nilai=$this->request->getPost('nilai');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');

        $info=array(
            'nik'=>$nik,
            'kdpendidikan'=>$kdpendidikan,
            'nmsekolah'=>strtoupper($nmsekolah),
            'kotakab'=>strtoupper($kotakab),
            'jurusan'=>strtoupper($jurusan),
            'program_studi'=>strtoupper($program_studi),
            'nilai'=>strtoupper($nilai),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
        );

        $this->db->insert('sc_trx.riwayat_pendidikan',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }
    function edit_riwayat_pendidikan(){
        $nik=$this->request->getPost('nik');
        $kdpendidikan=$this->request->getPost('kdpendidikan');
        $nmsekolah=$this->request->getPost('nmsekolah');
        $jurusan=$this->request->getPost('jurusan');
        $program_studi=$this->request->getPost('program_studi');
        $kodeprov=$this->request->getPost('kodeprov');
        $kotakab=$this->request->getPost('kotakab');
        $tahun_masuk=str_replace("_","",$this->request->getPost('tahun_masuk'));
        $tahun_keluar=str_replace("_","",$this->request->getPost('tahun_keluar'));
        $nilai=$this->request->getPost('nilai');
        $keterangan=$this->request->getPost('keterangan');
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $no_urut=$this->request->getPost('no_urut');

        $info=array(
            'nmsekolah'=>strtoupper($nmsekolah),
            'kotakab'=>strtoupper($kotakab),
            'jurusan'=>strtoupper($jurusan),
            'program_studi'=>strtoupper($program_studi),
            'nilai'=>strtoupper($nilai),
            'tahun_keluar'=>strtoupper($tahun_keluar),
            'tahun_masuk'=>strtoupper($tahun_masuk),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
        );

        $this->db->where('no_urut',$no_urut);
        $this->db->update('sc_trx.riwayat_pendidikan',$info);
        return redirect()->to(base_url("trans/karyawan/detail/$nik/rep_succes"));

    }

    function hps_riwayat_pendidikan($nik,$no_urut){
        $this->db->where('no_urut',$no_urut);
        $this->db->delete('sc_trx.riwayat_pendidikan');
        return redirect()->to(base_url("trans/karyawan/detail/$nik/del_succes"));
    }



    public function list_resign_karyawan()
    {
        $list = $this->m_karyawan->get_t_resign_view();
        $data = array();
        $no = $_POST['start'];
        $nama=trim($this->session->get('nama'));
        foreach ($list as $person) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a class="btn btn-sm btn-default" href="#" title="Detail Karyawan"  onclick="detailNik('."'".trim($person->nik)."'".');"><i class="fa fa-bars"></i></a>
                        <a class="btn btn-sm btn-primary" href="#"  title="Ubah Karyawan" onclick="editNikResign('."'".trim($person->nik)."'".');"><i class="fa fa-gear"></i> </a>
						';
            $row[] = $person->nik;
            $row[] = $person->nmlengkap;
            /*
            if ($person->image<>'') {
                $row[] = 					//'test';
                    '<div class="pull-left image">
					<img height="100px" width="100px" alt="User Image" class="img-box" src="'.base_url('/assets/img/profile').'/'.trim($person->image).'">
				</div>';
            } else {
                $row[] =
                    '<div class="pull-left image">
					<img height="100px" width="100px" alt="User Image" src="'.base_url('/assets/img/user.png').'">
				</div>';
            }
            */
            $row[] = $person->nmdept;
            $row[] = $person->nmsubdept;
            $row[] = $person->nmjabatan;
            $row[] = $person->tglkeluarkerja;
            $row[] = $person->nmgroupgol;
            $row[] = $person->locaname;
            $row[] = $person->alasan_resign;
            //<a class="btn btn-sm btn-success" href="'.base_url('trans/mutprom/index').'/'.trim($person->nik).'" title="Detail"><i class="glyphicon glyphicon-pencil"></i> Mutasi</a>
            //add html for action

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_karyawan->t_resign_view_count_all(),
            "recordsFiltered" => $this->m_karyawan->t_resign_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    /* PENAMBAHAN ATRIBUT PADA MASTER KARYAWAN */
    public function detail_atribut_karyawan(){
        $docno = trim($this->request->getGet('docno'));
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.A.1'; $versirelease='I.T.A.1/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.A.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }
        }


        $data['title'] = "Detail Atribut Karyawan";
        $data['type']="DETAIL";
        $data['nama']=$nama;
        $data['docno'] =$docno;
        /* RIWAYAT PENDIDIKAN */
        $data['riwayat_pendidikan'] = $this->m_karyawan->q_riwayat_pendidikan(" and nik='$docno'")->getResult();
        $data['m_pendidikan'] = $this->m_karyawan->q_m_pendidikan(" ")->getResult();
        /* KARTU PENDUKUNG */
        $data['list_kartupendukung'] = $this->m_karyawan->q_kartupendukung(" and nik='$docno'")->getResult();
        $data['m_kartupendukung'] = $this->m_karyawan->q_m_kartupendukung(" ")->getResult();

        $data['title_abscut'] = 'Laporan Absensi Cuti/Sakit';
        $data['title_abstelat'] = 'Laporan Izin,Keterlambatan & Pulang Awal Individu';
        $data['title_covidtrack'] = 'Laporan Historis Izin Sakit & rKesehatan Karyawan';
        $data['list_abscut'] = $this->m_abscut->q_abscut(" and nik='$docno' and trim(idtypecuti) not in ('PA','DT','IZ')")->getResult();
        $data['list_abstel'] = $this->m_abscut->q_abscut(" and nik='$docno' and trim(idtypecuti) in ('IZ','PA','DT')")->getResult();
        $data['list_covidtrack'] = $this->m_karyawan->q_covidtrack(" and nik='$docno' and trim(idtypecuti) in ('VK1','VK2','IM','CP','SC')")->getResult();

        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);

        return $this->template->render('trans/karyawan/v_detail_atribut_karyawan',$data);
    }

    function save_pendidikanf(){
        $nama=trim($this->session->get('nama'));
        $nik = trim($this->request->getPost('pend_inp_nik'));
        $type = trim($this->request->getPost('pend_inp_type'));
        $kdpendidikan = trim($this->request->getPost('pend_inp_kdpendidikan'));
        $nmstudy = strtoupper($this->request->getPost('pend_inp_nmstudy'));
        $tahun_masuk = strtoupper($this->request->getPost('pend_inp_tahun_masuk'));
        $tahun_keluar = strtoupper($this->request->getPost('pend_inp_tahun_keluar'));
        $alamatpendidikan = strtoupper($this->request->getPost('pend_inp_alamatpendidikan'));
        $jurusan = strtoupper($this->request->getPost('pend_inp_jurusan'));
        $ipk = trim($this->request->getPost('pend_inp_ipk'));
        $description = strtoupper($this->request->getPost('pend_inp_description'));
        //$attachment = trim($this->request->getPost('pend_inp_attachment'));
        $builder = $this->db->table('sc_his.riwayat_pendidikan');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $path = "./assets/img/riwayat_pendidikan";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        $file = $this->request->getFile('pend_inp_attachment');
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'pend_inp_attachment' => [
                    'uploaded[pend_inp_attachment]',
                    'mime_in[pend_inp_attachment,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[pend_inp_attachment,2096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 250 pixel</br>
					* Tinggi Max 250 pixel</br>					
					');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = trim(trim($nik)).'_'.$file->getRandomName().'.png';
                $path = FCPATH.'assets/img/riwayat_pendidikan';
                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                    // ->convert(IMAGETYPE_PNG)
                    //->resize(250, 250, true, 'height')
                    ->save($path . '/' . $final_file_name);
                $gambar=$final_file_name;


                $insert_this = array(
                    'nik' => $nik,
                    'kdpendidikan' => $kdpendidikan,
                    'nmstudy' => $nmstudy,
                    'alamatpendidikan' => $alamatpendidikan,
                    'jurusan' => $jurusan,
                    'tahun_masuk' => $tahun_masuk,
                    'tahun_keluar' => $tahun_keluar,
                    'ipk' => $ipk,
                    'status' => 'UNACTIVE',
                    'description' => $description,
                    'attachment' => $gambar,
                    'inputby' => $nama,
                    'inputdate' => date('Y-m-d H:i:s'),

                );
                $builder->insert($insert_this);

                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','I.T.A.1');
                $builder_trxerror->delete();
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $nik,
                    'nomorakhir2' => '',
                    'modul' => 'I.T.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));

            }
        } else {
            $insert_this = array(
                'nik' => $nik,
                'kdpendidikan' => $kdpendidikan,
                'nmstudy' => $nmstudy,
                'alamatpendidikan' => $alamatpendidikan,
                'jurusan' => $jurusan,
                'tahun_masuk' => $tahun_masuk,
                'tahun_keluar' => $tahun_keluar,
                'ipk' => $ipk,
                'status' => 'UNACTIVE',
                'description' => $description,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),

            );
            $builder->insert($insert_this);
            //INSERT TRX ERROR
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.T.A.1');
            $builder_trxerror->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nik,
                'nomorakhir2' => '',
                'modul' => 'I.T.A.1',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));
        }

    }
    function save_pendidikanf_update(){
        $nama=trim($this->session->get('nama'));
        $id = trim($this->request->getPost('pend_upd_id'));
        $nik = trim($this->request->getPost('pend_upd_nik'));
        $type = trim($this->request->getPost('pend_upd_type'));
        $kdpendidikan = trim($this->request->getPost('pend_upd_kdpendidikan'));
        $nmstudy = strtoupper($this->request->getPost('pend_upd_nmstudy'));
        $tahun_masuk = strtoupper($this->request->getPost('pend_upd_tahun_masuk'));
        $tahun_keluar = strtoupper($this->request->getPost('pend_upd_tahun_keluar'));
        $alamatpendidikan = strtoupper($this->request->getPost('pend_upd_alamatpendidikan'));
        $jurusan = strtoupper($this->request->getPost('pend_upd_jurusan'));
        $ipk = trim($this->request->getPost('pend_upd_ipk'));
        $status = trim($this->request->getPost('pend_upd_status'));
        $description = strtoupper($this->request->getPost('pend_upd_description'));
        //$attachment = trim($this->request->getPost('pend_inp_attachment'));
        $builder = $this->db->table('sc_his.riwayat_pendidikan');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $path = "./assets/img/riwayat_pendidikan/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }


        $file = $this->request->getFile('pend_upd_attachment');
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'pend_inp_attachment' => [
                    'uploaded[pend_inp_attachment]',
                    'mime_in[pend_inp_attachment,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[pend_inp_attachment,2096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
					Format yang sesuai:</br>
					* Ukuran File yang di ijinkan max 2MB</br>
					* Lebar Max 250 pixel</br>
					* Tinggi Max 250 pixel</br>					
					');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = trim(trim($nik)).'_'.$file->getRandomName().'.png';
                $path = FCPATH.'assets/img/riwayat_pendidikan';
                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                    // ->convert(IMAGETYPE_PNG)
                    //->resize(250, 250, true, 'height')
                    ->save($path . '/' . $final_file_name);
                $gambar=$final_file_name;

                $insert_this = array(
                    'kdpendidikan' => $kdpendidikan,
                    'nmstudy' => $nmstudy,
                    'alamatpendidikan' => $alamatpendidikan,
                    'jurusan' => $jurusan,
                    'tahun_masuk' => $tahun_masuk,
                    'tahun_keluar' => $tahun_keluar,
                    'ipk' => $ipk,
                    'description' => $description,
                    'attachment' => $gambar,
                    'status' => $status,
                    'updateby' => $nama,
                    'updatedate' => date('Y-m-d H:i:s'),

                );
                $builder->where('id',$id);
                $builder->update($insert_this);

                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','I.T.A.1');
                $builder_trxerror->delete('sc_mst.trxerror');
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $nik,
                    'nomorakhir2' => '',
                    'modul' => 'I.T.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));

            }
        } else {
            $insert_this = array(
                'kdpendidikan' => $kdpendidikan,
                'nmstudy' => $nmstudy,
                'alamatpendidikan' => $alamatpendidikan,
                'jurusan' => $jurusan,
                'tahun_masuk' => $tahun_masuk,
                'tahun_keluar' => $tahun_keluar,
                'ipk' => $ipk,
                'description' => $description,
                'status' => $status,
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),
            );
            $builder->where('id',$id);
            $builder->update($insert_this);

            //INSERT TRX ERROR
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.T.A.1');
            $builder_trxerror->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nik,
                'nomorakhir2' => '',
                'modul' => 'I.T.A.1',
            );
            $builder_trxerror->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));
        }

    }
    function save_pendidikanf_delete(){
        $nama = trim($this->session->get('nama'));
        $nik = trim($this->request->getGet('nik'));
        $id = trim($this->request->getGet('id'));

        $dtl = $this->m_karyawan->q_riwayat_pendidikan(" and id='$id'")->getRowArray();
        $final_file_name = trim($dtl['attachment']);

        $builder = $this->db->table('sc_his.riwayat_pendidikan');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $path = FCPATH."assets/img/riwayat_pendidikan/";

        echo $path . $final_file_name;
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        if (!empty($final_file_name)) {
            unlink($path . $final_file_name);
        }

        $builder->where('id',$id);
        $builder->delete();


        //INSERT TRX ERROR
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.T.A.1');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nik,
            'nomorakhir2' => '',
            'modul' => 'I.T.A.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));
    }

    /* SAVE KARTU PENDIDIKAN */
    function save_kartupendukung(){
        $nama=trim($this->session->get('nama'));
        $nik = trim($this->request->getPost('kartupendukung_inp_nik'));
        $type = trim($this->request->getPost('kartupendukung_inp_type'));
        $kdkartu = strtoupper($this->request->getPost('kartupendukung_inp_kdkartu'));
        $idkartu = $this->request->getPost('kartupendukung_inp_idkartu');
        $description = strtoupper($this->request->getPost('kartupendukung_inp_description'));
        //$attachment = trim($this->request->getPost('pend_inp_attachment'));
        $builder = $this->db->table('sc_his.kartu_pendukung');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $path = "./assets/img/kartupendukung/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        $file = $this->request->getFile('kartupendukung_inp_attachment');
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'pend_inp_attachment' => [
                    'uploaded[kartupendukung_inp_attachment]',
                    'mime_in[kartupendukung_inp_attachment,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[kartupendukung_inp_attachment,2096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
                Format yang sesuai:</br>
                * Ukuran File yang di ijinkan max 2MB</br>
                * Lebar Max 250 pixel</br>
                * Tinggi Max 250 pixel</br>					
                ');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = trim(trim($nik)).'_'.$file->getRandomName().'.png';
                $path = FCPATH.'assets/img/kartupendukung';
                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                    // ->convert(IMAGETYPE_PNG)
                    //->resize(250, 250, true, 'height')
                    ->save($path . '/' . $final_file_name);
                $gambar=$final_file_name;

                $insert_this = array(
                    'nik' => $nik,
                    'kdkartu' => $kdkartu,
                    'idkartu' => $idkartu,
                    'status' => 'ACTIVE',
                    'description' => $description,
                    'attachment' => $gambar,
                    'inputby' => $nama,
                    'inputdate' => date('Y-m-d H:i:s'),

                );
                $builder->insert($insert_this);

                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','I.T.A.1');
                $builder_trxerror->delete();
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $nik,
                    'nomorakhir2' => '',
                    'modul' => 'I.T.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));

            }
        } else {
            $insert_this = array(
                'nik' => $nik,
                'kdkartu' => $kdkartu,
                'idkartu' => $idkartu,
                'status' => 'ACTIVE',
                'description' => $description,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),

            );
            $builder->insert($insert_this);
            //INSERT TRX ERROR
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.T.A.1');
            $builder_trxerror->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nik,
                'nomorakhir2' => '',
                'modul' => 'I.T.A.1',
            );
            $builder_trxerror->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));
        }

    }

    function save_kartupendukung_update(){
        $nama=trim($this->session->get('nama'));
        $nik = trim($this->request->getPost('kartupendukung_upd_nik'));
        $id = trim($this->request->getPost('kartupendukung_upd_id'));
        $type = trim($this->request->getPost('kartupendukung_upd_type'));
        $kdkartu = strtoupper($this->request->getPost('kartupendukung_upd_kdkartu'));
        $idkartu = $this->request->getPost('kartupendukung_upd_idkartu');
        $description = strtoupper($this->request->getPost('kartupendukung_upd_description'));
        //$attachment = trim($this->request->getPost('pend_inp_attachment'));

        $builder = $this->db->table('sc_his.kartu_pendukung');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $path = "./assets/img/kartupendukung/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        $file = $this->request->getFile('kartupendukung_upd_attachment');
        if (!empty($file->getName())) {
            //echo $file->getName();
            $validateImg = $this->validate([
                'pend_inp_attachment' => [
                    'uploaded[kartupendukung_inp_attachment]',
                    'mime_in[kartupendukung_inp_attachment,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[kartupendukung_inp_attachment,2096]',
                ]
            ]);

            if(!$validateImg){
                $getResult = array('status' => false, 'messages' => 'Gambar Tidak Sesuai</br>
                Format yang sesuai:</br>
                * Ukuran File yang di ijinkan max 2MB</br>
                * Lebar Max 250 pixel</br>
                * Tinggi Max 250 pixel</br>					
                ');
                echo json_encode($getResult);
                $gambar="";
            } else {
                //Image Resizing
                $final_file_name = trim(trim($nik)).'_'.$file->getRandomName().'.png';
                $path = FCPATH.'assets/img/kartupendukung';
                if(file_exists($path.$final_file_name)){
                    unlink($path.$final_file_name);
                }
                $this->image->withFile($file)
                    // ->convert(IMAGETYPE_PNG)
                    //->resize(250, 250, true, 'height')
                    ->save($path . '/' . $final_file_name);
                $gambar=$final_file_name;

                $insert_this = array(
                    'idkartu' => $idkartu,
                    'status' => 'ACTIVE',
                    'description' => $description,
                    'attachment' => $gambar,
                    'inputby' => $nama,
                    'inputdate' => date('Y-m-d H:i:s'),

                );
                $builder->where('id',$id);
                $builder->update($insert_this);

                //INSERT TRX ERROR
                $builder_trxerror->where('userid',$nama);
                $builder_trxerror->where('modul','I.T.A.1');
                $builder_trxerror->delete();
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 0,
                    'nomorakhir1' => $nik,
                    'nomorakhir2' => '',
                    'modul' => 'I.T.A.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));

            }
        } else {
            $insert_this = array(
                'idkartu' => $idkartu,
                'status' => 'ACTIVE',
                'description' => $description,
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),

            );
            $builder->where('id',$id);
            $builder->update($insert_this);
            //INSERT TRX ERROR
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.T.A.1');
            $builder_trxerror->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nik,
                'nomorakhir2' => '',
                'modul' => 'I.T.A.1',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));
        }

    }
    function save_kartupendukung_delete(){
        $nama = trim($this->session->get('nama'));
        $nik = trim($this->request->getGet('nik'));
        $id = trim($this->request->getGet('id'));

        $dtl = $this->m_karyawan->q_kartupendukung(" and id='$id'")->getRowArray();
        $final_file_name = trim($dtl['attachment']);
        $path = "./assets/img/kartupendukung/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        if (file_exists($path . $final_file_name)) {
            unlink($path . $final_file_name);
        }

        $builder = $this->db->table('sc_his.kartu_pendukung');
        $builder->where('id',$id);
        $builder->delete();


        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.T.A.1');
        $builder_trxerror->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nik,
            'nomorakhir2' => '',
            'modul' => 'I.T.A.1',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url("trans/karyawan/detail_atribut_karyawan/?docno=$nik"));
    }
}
