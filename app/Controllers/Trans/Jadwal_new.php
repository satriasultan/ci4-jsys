<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Jadwal_new extends BaseController
{
    function index()
    {
        $kdregu='';
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.8'; $versirelease='I.T.B.8/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.8'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }
        if($count_err>0 and $errordesc<>''){
            if (trim($dtlerror['errorcode'])<1){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH/DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc.' '. $nomorakhir1 </div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $thn1=$this->request->getPost('thn');
        $bln1=$this->request->getPost('bln');
        if((!empty($bln1)) and (!empty($thn1))){
            $bln=$bln1;
            $thn=$thn1;
        } else if((!empty($blnuri)) and (!empty($thnuri))){
            $bln=$blnuri;
            $thn=$thnuri;
        } else{
            $bln=date('m');
            $thn=date('Y');
        }

        $kdregu1=$this->request->getPost('kdregu');
        if(empty($kdregu1)) {
            $kdregu=$kdregu;
        } else {
            $kdregu=$kdregu1;
        }



        $nik=$this->request->getPost('nik');

        $nama=trim($this->session->get('nama'));
        $username=trim($this->session->get('nama'));
        $kmenu='I.T.B.8';
        //ENTRY REGU OTOMATIS
        //$this->db->query("select sc_trx.pr_listdtljadwalkerja('$bln','$thn','$kdregu')");

        $q_cek_bagianses=$this->m_regu->q_karyawan_session($nama)->getRowArray();
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();
        $role_access_filter = $data['dtl_akses']['a_filter'];

        $id_dept=$q_cek_bagianses['id_dept'];



        if (empty($nik) and !empty ($kdregu)){
            if(trim($role_access_filter)==='f'){
                $pfilter=" and id_dept='$id_dept'";
                $id_dept="and b.id_dept='$id_dept'";
                $read="and a.kdregu='$kdregu' $id_dept";
                $pkdregu="'$kdregu'";

            }else{
                $id_dept="";
                $read="and a.kdregu='$kdregu' $id_dept";
                $pfilter="";
                $pkdregu="'$kdregu'";
            }
        } else if (!empty($nik) and empty ($kdregu)){
            if(trim($role_access_filter)==='t'){
                $pfilter=" and id_dept='$id_dept'";
                $id_dept="and b.id_dept='$id_dept'";
                $read="and a.nik='$nik' $id_dept";
                $pkdregu="(select max(kdregu)::character(25) from sc_trx.jadwalkerja 
			    where to_char(tgl,'yyyymm')='".$thn.$bln."')";

            }else{$id_dept="";
                $read="and a.nik='$nik' $id_dept";
                $pfilter="";
                $pkdregu="(select max(kdregu)::character(25) from sc_trx.jadwalkerja 
			    where to_char(tgl,'yyyymm')='".$thn.$bln."')";
            }
        } else if (!empty($nik) and !empty ($kdregu)){
            if(trim($role_access_filter)==='t'){
                $pfilter=" and id_dept='$id_dept'";
                $id_dept="and b.id_dept='$id_dept'";
                $read="and a.kdregu='$kdregu' and a.nik='$nik' $id_dept";
                $pkdregu="'$kdregu'";
            }else{$id_dept="";
                $read="and a.kdregu='$kdregu' and a.nik='$nik' $id_dept";}
            $pfilter="";
            $pkdregu="'$kdregu'";
        } else {
            if(trim($role_access_filter)==='t'){
                $pfilter=" and (id_dept='$id_dept' or id_dept in (select trim(kddept) from sc_mst.mapping_filter_jadwalkerja where username='$username' and nik='$nama'))";
                $id_dept="and (b.id_dept='$id_dept' or b.id_dept in (select trim(kddept) from sc_mst.mapping_filter_jadwalkerja where username='$username' and nik='$nama'))";
                $read=" $id_dept";
                $pkdregu="(select max(kdregu)::character(25) from sc_trx.jadwalkerja 
			    where to_char(tgl,'yyyymm')='".$thn.$bln."')";

            }else{
                $pfilter="";
                $paramread="and a.kdregu in (select max(kdregu)::character(25) from sc_trx.jadwalkerja 
			    where to_char(tgl,'yyyymm')='".$thn.$bln."')";
                //$read="$paramread";
                $read=" ";
                $pkdregu="(select max(kdregu)::character(25) from sc_trx.jadwalkerja 
			    where to_char(tgl,'yyyymm')='".$thn.$bln."')";

            }
        }
        if (empty($thn) and !empty($bln)){
            $tahun=date('Y'); $bulan=$bln;
        } else if (empty($bln) and !empty($thn)) {
            $tahun=$thn; $bulan=date('m');
        } else if (empty($bln) and empty($thn)) {
            $tahun=date('Y'); $bulan=date('m');
        } else {
            $tahun=trim($thn); $bulan=trim($bln);
        }




        $data['bulan']=$bulan;
        $data['tahun']=$tahun;
        $data['title']="JADWAL KERJA KARYAWAN";
        //$pfilter;
        //$this->db->query("select sc_trx.pr_listdtljadwalkerja('$bln','$thn',$pkdregu)");
        $this->db->query("select sc_trx.pr_listdtljadwalkerja('$bln','$thn')");

        //$data['oprblmjadwal']=$this->m_regu->q_cekdtlregu()->getNumRows();
//        echo $read;
//        echo '</br>';
//        echo trim($q_akses_ses['aksesfilter']);
        $data['list_jadwal']=$this->m_jadwalnew->q_jadwalkerja($bulan,$tahun,$read)->getResult();

        //$data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();
        $data['list_regu']=$this->m_jadwalnew->q_regu_param($pfilter = '')->getResult();
        //$data['list_reguinjadwal']=$this->m_jadwalnew->q_regu_injadwal()->getResult();
        $data['list_reguinjadwal']=$this->m_jadwalnew->q_regu_param($pfilter)->getResult();
        //$data['list_karyawan']=$this->m_jadwalnew->q_karyawan()->getResult();
        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan_opr_param($pfilter)->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        //$data['cekdate']=
        /* DEL TRXERROR */
        $this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/jadwal/v_jadwalnew',$data);

    }

    function ajax_cekjadwalinput($thn,$bln,$kdregu){

        $data = $this->m_jadwalnew->q_offjadwalkerja($thn,$bln,$kdregu)->getNumRows();
        echo json_encode($data);
        //echo 'tae';
    }
    function view_jadwalsebulan(){
        $userid=$this->session->get('nama');
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.8'; $versirelease='I.T.B.8/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.8'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }
        if($count_err>0 and $errordesc<>''){
            if (trim($dtlerror['errorcode'])<1){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH/DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc.' '. $nomorakhir1 </div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }



        $cektmp=$this->m_jadwalnew->q_tmpjadwalid($userid)->getNumRows();
        $isivar=$this->m_jadwalnew->q_tmpjadwalid($userid)->getRowArray();
        if($cektmp>0){
            $kdregu=trim($isivar['kdregu']);
            $tgl=$isivar['tgl'];
            list($year, $month, $day) = explode('-', $tgl);
            $bln=$month;
            $thn=$year;
            return redirect()->to(base_url("trans/jadwal_new/view_tmpjadwal/$kdregu/$bln/$thn"));
        }

        $data['title']="JADWAL KERJA BULANAN";

        $data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();
        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        return $this->template->render('trans/jadwal/v_inputjadwalsebulan',$data);
    }

    function input_jadwalsebulan(){
        $kdregu=$this->request->getPost('kdregu');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $tgl1=$this->request->getPost('tgl1');
        $tgl2=$this->request->getPost('tgl2');
        $tgl3=$this->request->getPost('tgl3');
        $tgl4=$this->request->getPost('tgl4');
        $tgl5=$this->request->getPost('tgl5');
        $tgl6=$this->request->getPost('tgl6');
        $tgl7=$this->request->getPost('tgl7');
        $tgl8=$this->request->getPost('tgl8');
        $tgl9=$this->request->getPost('tgl9');
        $tgl10=$this->request->getPost('tgl10');
        $tgl11=$this->request->getPost('tgl11');
        $tgl12=$this->request->getPost('tgl12');
        $tgl13=$this->request->getPost('tgl13');
        $tgl14=$this->request->getPost('tgl14');
        $tgl15=$this->request->getPost('tgl15');
        $tgl16=$this->request->getPost('tgl16');
        $tgl17=$this->request->getPost('tgl17');
        $tgl18=$this->request->getPost('tgl18');
        $tgl19=$this->request->getPost('tgl19');
        $tgl20=$this->request->getPost('tgl20');
        $tgl21=$this->request->getPost('tgl21');
        $tgl22=$this->request->getPost('tgl22');
        $tgl23=$this->request->getPost('tgl23');
        $tgl24=$this->request->getPost('tgl24');
        $tgl25=$this->request->getPost('tgl25');
        $tgl26=$this->request->getPost('tgl26');
        $tgl27=$this->request->getPost('tgl27');
        $tgl28=$this->request->getPost('tgl28');
        $tgl29=$this->request->getPost('tgl29');
        $tgl30=$this->request->getPost('tgl30');
        $tgl31=$this->request->getPost('tgl31');

        //$cektmp=$this->m_jadwalnew->q_tmpjadwal()->getNumRows();
        $userid=trim($this->session->get('nama'));
        $isivar=$this->m_jadwalnew->q_tmpjadwal()->getRowArray();
        $tgl=$isivar['tgl'];
        list($year, $month, $day) = explode('-', $tgl);
        $blntmp=$month;
        $thntmp=$year;
        $usid=trim($isivar['inputby']);
        $kdregutmp=trim($isivar['kdregu']);

        if($bln===$blntmp and $thn===$thntmp and $kdregu===$kdregutmp){

            $usid=trim($isivar['inputby']);
            return redirect()->to(base_url("trans/jadwal_new//fail_input/$bln/$thn/$kdregu/$usid"));
        }
        $builder = $this->db->table('sc_tmp.jadwalkerja');


        if ($tgl1<>'OFF') {
            $tgl=trim("$thn-$bln-01");
            $kdjamkerja=$tgl1;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }
        if ($tgl2<>'OFF') {
            $tgl=trim("$thn-$bln-02");
            $kdjamkerja=$tgl2;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        } if ($tgl3<>'OFF') {
            $tgl=date("$thn-$bln-03");
            $kdjamkerja=$tgl3;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }  if ($tgl4<>'OFF') {
            $tgl=date("$thn-$bln-04");
            $kdjamkerja=$tgl4;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }  if ($tgl5<>'OFF') {
            $tgl=date("$thn-$bln-05");
            $kdjamkerja=$tgl5;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl6<>'OFF') {
            $tgl=date("$thn-$bln-06");
            $kdjamkerja=$tgl6;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl7<>'OFF') {
            $tgl=date("$thn-$bln-07");
            $kdjamkerja=$tgl7;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl8<>'OFF') {
            $tgl=date("$thn-$bln-08");
            $kdjamkerja=$tgl8;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl9<>'OFF') {
            $tgl=date("$thn-$bln-09");
            $kdjamkerja=$tgl9;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl10<>'OFF') {
            $tgl=date("$thn-$bln-10");
            $kdjamkerja=$tgl10;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl11<>'OFF') {
            $tgl=date("$thn-$bln-11");
            $kdjamkerja=$tgl11;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl12<>'OFF') {
            $tgl=date("$thn-$bln-12");
            $kdjamkerja=$tgl12;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl13<>'OFF') {
            $tgl=date("$thn-$bln-13");
            $kdjamkerja=$tgl13;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl14<>'OFF') {
            $tgl=date("$thn-$bln-14");
            $kdjamkerja=$tgl14;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl15<>'OFF') {
            $tgl=date("$thn-$bln-15");
            $kdjamkerja=$tgl15;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl16<>'OFF') {
            $tgl=date("$thn-$bln-16");
            $kdjamkerja=$tgl16;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl17<>'OFF') {
            $tgl=date("$thn-$bln-17");
            $kdjamkerja=$tgl17;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl18<>'OFF') {
            $tgl=date("$thn-$bln-18");
            $kdjamkerja=$tgl18;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl19<>'OFF') {
            $tgl=date("$thn-$bln-19");
            $kdjamkerja=$tgl19;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl20<>'OFF') {
            $tgl=date("$thn-$bln-20");
            $kdjamkerja=$tgl20;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl21<>'OFF') {
            $tgl=date("$thn-$bln-21");
            $kdjamkerja=$tgl21;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl22<>'OFF') {
            $tgl=date("$thn-$bln-22");
            $kdjamkerja=$tgl22;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl23<>'OFF') {
            $tgl=date("$thn-$bln-23");
            $kdjamkerja=$tgl23;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl24<>'OFF') {
            $tgl=date("$thn-$bln-24");
            $kdjamkerja=$tgl24;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl25<>'OFF') {
            $tgl=date("$thn-$bln-25");
            $kdjamkerja=$tgl25;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl26<>'OFF') {
            $tgl=date("$thn-$bln-26");
            $kdjamkerja=$tgl26;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl27<>'OFF') {
            $tgl=date("$thn-$bln-27");
            $kdjamkerja=$tgl27;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl28<>'OFF') {
            $tgl=date("$thn-$bln-28");
            $kdjamkerja=$tgl28;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl29<>'OFF') {
            $tgl=date("$thn-$bln-29");
            $kdjamkerja=$tgl29;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl30<>'OFF') {
            $tgl=date("$thn-$bln-30");
            $kdjamkerja=$tgl30;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }   if ($tgl31<>'OFF') {
            $tgl=date("$thn-$bln-31");
            $kdjamkerja=$tgl31;
            $info = array(
                'kdregu' => strtoupper($kdregu),
                'kodejamkerja' => strtoupper($kdjamkerja),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => trim($this->session->get('nama')),
                'tgl' => $tgl,
                'status'=> 'I'
            );
            $builder->insert($info);

        }
        return redirect()->to(base_url("trans/jadwal_new/view_tmpjadwal/$kdregu/$bln/$thn"));

    }

    function view_tmpjadwal(){
        $kdregu=$this->uri->getSegment(4);
        $bln=$this->uri->getSegment(5);
        $thn=$this->uri->getSegment(6);
        $userid=$this->session->get('nama');

        $data['title']='PROSES PENJADWALAN ';
        $data['kdregu']=$kdregu;
        $data['bln']=$bln;
        $data['thn']=$thn;

        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        $data['list_tmpjadwal']=$this->m_jadwalnew->q_tmpjadwalid($userid)->getResult();
        return $this->template->render('trans/jadwal/v_tmpjadwalkerja',$data);

    }

    function edit_tmpjadwal(){
        $kdregu=$this->request->getPost('kdregu');
        $tgl=$this->request->getPost('tgl');
        list($year, $month, $day) = explode('-', $tgl);
        $bln=$month;
        $thn=$year;
        $kodejamkerja=$this->request->getPost('kdjamkerja');
        $data = array(
            'kodejamkerja' => $kodejamkerja
        );

        $this->db->where('kdregu', $kdregu);
        $this->db->where('tgl', $tgl);
        $this->db->update('sc_tmp.jadwalkerja', $data);


        return redirect()->to(base_url("trans/jadwal_new/view_tmpjadwal/$kdregu/$bln/$thn"));
    }

    function savefinal_tmpjadwal(){
        $builder = $this->db->table('sc_tmp.jadwalkerja');
        $tglbulan=$this->uri->getSegment(4);
        $tgltahun=$this->uri->getSegment(5);
        $userid=$this->session->get('nama');

        $status='F';
        $data = array(
            'status' => $status
        );
        $builder->where('inputby',$userid);
        $builder->update($data);
        $this->db->query("select sc_trx.pr_listdtljadwalkerja('$tglbulan','$tgltahun')");
        return redirect()->to(base_url("trans/jadwal_new"));
    }

    function clear_tmpjadwal(){
        $userid=$this->session->get('nama');
        $this->db->query("delete from sc_tmp.jadwalkerja where inputby='$userid'");
        return redirect()->to(base_url("trans/jadwal_new/view_jadwalsebulan"));
    }

    function delete_tmpjadwal(){
        $kdregu=trim($this->uri->getSegment(4));
        $tgl=trim($this->uri->getSegment(5));
        $kodejamkerja=trim($this->uri->getSegment(6));
        $userid=$this->session->get('nama');
        list($year, $month, $day) = explode('-', $tgl);
        $bln=$month;
        $thn=$year;
        //echo $kdregu, $tgl, $kodejamkerja;
        $this->db->where('kdregu', $kdregu);
        $this->db->where('tgl', $tgl);
        $this->db->where('kodejamkerja', $kodejamkerja);
        $this->db->where('inputby', $userid);
        $this->db->delete('sc_tmp.jadwalkerja');
        return redirect()->to(base_url("trans/jadwal_new/view_tmpjadwal/$kdregu/$bln/$thn"));
    }

    function edit_jadwal(){
        $id=$this->request->getPost('id');
        $kdregu=$this->request->getPost('kdregu');
        //$shift=$this->request->getPost('shift');
        $tgl=$this->request->getPost('tanggal');
        $kdjamkerja=$this->request->getPost('kdjamkerja');
        $cek=$this->m_absensi->cek_exist($kdregu,$kdjamkerja,$tgl);
        $info = array(
            //'shift_tipe' => $this->request->getPost('shiftkrj'),
            'kdregu' => strtoupper($kdregu),
            'kodejamkerja' => strtoupper($kdjamkerja),
            'tgl' => $tgl,
            //'shift_tipe' => $shift,
            'updatedate' => date('d-m-Y H:i:s'),
            'updateby' => $this->session->get('nama'),
        );
        /*if ($cek>0) {
          echo "DATA SUDAH ADA";
        } else {
            $this->db->insert('sc_trx.jadwalkerja',$info);
            return redirect()->to(base_url('trans/jadwal_new/');
        }*/
        $this->db->where('id',$id);
        $this->db->update('sc_trx.jadwalkerja',$info);
        return redirect()->to(base_url('trans/jadwal_new/'));

    }



    function edit_jadwal_detail(){
        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu');
        $kdregu_old=$this->request->getPost('kdregu_old');
        $kdmesin=$this->request->getPost('kdmesin');
        //$shift=$this->request->getPost('shift');
        $tgl=$this->request->getPost('tanggal');
        $tgl_old=$this->request->getPost('tanggal_old');
        $id=$this->request->getPost('id');
        $kdjamkerja=$this->request->getPost('kdjamkerja');
        $cek=$this->m_absensi->cek_exist($kdregu,$kdjamkerja,$tgl);
        $info = array(
            //'shift_tipe' => $this->request->getPost('shiftkrj'),
            'kdregu' => strtoupper($kdregu),
            //'kdmesin' => strtoupper($kdmesin),
            'nik' => strtoupper($nik),
            'kdjamkerja' => strtoupper($kdjamkerja),
            'tgl' => $tgl,
            //'shift_tipe' => $shift,
            'updatedate' => date('d-m-Y H:i:s'),
            'updateby' => $this->session->get('nama'),
        );
        if ($cek>0) {
            //echo "BOLEH DI INSERT";
            $this->db->where('nik',$nik);
            $this->db->where('tgl',$tgl_old);
            $this->db->where('id',$id);
            $this->db->update('sc_trx.dtljadwalkerja',$info);
            return redirect()->to(base_url("trans/jadwal_new/detail_regu/$tgl_old/$kdregu_old"));
        } else {
            echo "DATA BELUM ADA";
        }


    }


    function delete_jadwal($id){
        $this->db->where('id',$id);
        $this->db->delete('sc_trx.jadwalkerja');
        return redirect()->to(base_url('trans/jadwal_new/'));

    }

    function delete_jadwal_detail($id,$tgl_kerja,$kdregu){
        $this->db->where('id',$id);
        $this->db->delete('sc_trx.dtljadwalkerja');
        return redirect()->to(base_url("trans/jadwal_new/detail_regu/$tgl_kerja/$kdregu"));

    }

    function edit_detail($nik,$tgl){
        //echo $nik.'||'.$tgl;
        $data['title']='EDIT JADWAL KERJA';
        $cek=$this->m_jadwalnew->q_dtljadwalkerja_nik($nik,$tgl)->getNumRows();
        if ($cek>0){
            $data['dtl_jadwal']=$this->m_jadwalnew->q_dtljadwalkerja_nik($nik,$tgl)->getRowArray();
            $data['dtl_tgl']=$tgl;
            $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
            return $this->template->render('trans/jadwal/v_editdtljadwalnew',$data);
        } else {
            $bulan=date("m",strtotime($tgl));
            $data['dtl_jadwal']=$this->m_jadwalnew->q_vsebulanjadwal($nik,$bulan)->getRowArray();
            $data['dtl_tgl']=$tgl;
            $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
            return $this->template->render('trans/jadwal/v_editdtljadwalnew',$data);
            //return redirect()->to(base_url('trans/jadwal_new//no_data');
        }
    }

    function proses_edit_detail(){
        $nama = trim($this->session->get('nama'));
        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu');
        //$kdregu_old=$this->request->getPost('kdregu_old');
        //$kdmesin=$this->request->getPost('kdmesin');
        //$shift=$this->request->getPost('shift');
        $tgl=$this->request->getPost('tanggal');
        $tgl_old=$this->request->getPost('tanggal_old');
        $id=$this->request->getPost('id');
        $kdjamkerja=trim($this->request->getPost('kdjamkerja'));
        $kdmesin =  $this->request->getPost('kdmesin');
        $cekdtl=$this->m_jadwalnew->cek_exist_detail2($nik,$kdregu,$tgl);
        $builderx = $this->db->table('sc_tmp.mutasi_jadwalkerja_pernik ');

        $split_tgl = explode('-',$tgl);
        $bln = $split_tgl[1];
        $thn = $split_tgl[0];
        $day = $split_tgl[2];
        /* BLOKING EDIT */
        $param_ent = " and nik = '$nik'";
        $inf_mkaryawan = $this->m_jadwalnew->q_master_karyawan($param_ent)->getRowArray();
        if ($inf_mkaryawan['tglmasukkerja'] <= $tgl) {
            /*Load Temporary*/
            $fill = array (
                'nodok' => $nama,
                'nik' => '',
                'kdregu' => '',
                'periode' => $thn.$bln,
                'status' => 'I',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builderx->insert($fill);
            $this->m_jadwalnew->load_tmp_nik($nik,$bln,$thn);

            if($kdjamkerja==='OFF') {
                /* $builderx->where('nik',$nik);
                 $builderx->where('kdregu',$kdregu);
                 $builderx->where('tgl',$tgl);
                 $this->db->delete('sc_trx.dtljadwalkerja');*/
                $favcolor = $day;
                switch ($favcolor) {
                    case "01":
                        $info = array(
                            'newtgl1' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                        $builderx->update( $info);
                        break;
                    case "02":
                        $info = array(
                            'newtgl2' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                        $builderx->update( $info);
                        break;
                    case "03":
                        $info = array(
                            'newtgl3' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "04":
                        $info = array(
                            'newtgl4' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "05":
                        $info = array(
                            'newtgl5' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "06":
                        $info = array(
                            'newtgl6' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "07":
                        $info = array(
                            'newtgl7' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "08":
                        $info = array(
                            'newtgl8' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "09":
                        $info = array(
                            'newtgl9' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "10":
                        $info = array(
                            'newtgl10' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "11":
                        $info = array(
                            'newtgl11' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "12":
                        $info = array(
                            'newtgl12' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "13":
                        $info = array(
                            'newtgl13' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "14":
                        $info = array(
                            'newtgl14' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "15":
                        $info = array(
                            'newtgl15' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "16":
                        $info = array(
                            'newtgl16' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "17":
                        $info = array(
                            'newtgl17' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "18":
                        $info = array(
                            'newtgl18' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "19":
                        $info = array(
                            'newtgl19' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "20":
                        $info = array(
                            'newtgl20' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "21":
                        $info = array(
                            'newtgl21' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "22":
                        $info = array(
                            'newtgl22' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "23":
                        $info = array(
                            'newtgl23' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "24":
                        $info = array(
                            'newtgl24' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "25":
                        $info = array(
                            'newtgl25' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "26":
                        $info = array(
                            'newtgl26' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "27":
                        $info = array(
                            'newtgl27' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "28":
                        $info = array(
                            'newtgl28' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "29":
                        $info = array(
                            'newtgl29' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "30":
                        $info = array(
                            'newtgl30' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "31":
                        $info = array(
                            'newtgl31' => 'OFF',
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    default:
                        //echo "Your favorite color is neither red, blue, nor green!";
                }

                //} else if($cekdtl==='0') {
                /* $info = array(

                     'kdregu' => strtoupper(trim($kdregu)),
                     'kdmesin' => strtoupper($kdmesin),
                     'nik' =>trim($nik),
                     'kdjamkerja' => strtoupper($kdjamkerja),
                     'tgl' => $tgl,
                     'updatedate' => date('d-m-Y H:i:s'),
                     'updateby' => $this->session->get('nama'),
                 );
                 $builderx->where('nik',$nik);
                 $builderx->where('kdregu',$kdregu);
                 $this->db->insert('sc_trx.dtljadwalkerja',$info);*/
            } else {
                $favcolor = $day;
                switch ($favcolor) {
                    case "01":
                        $info = array(
                            'newtgl1' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "02":
                        $info = array(
                            'newtgl2' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "03":
                        $info = array(
                            'newtgl3' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "04":
                        $info = array(
                            'newtgl4' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "05":
                        $info = array(
                            'newtgl5' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "06":
                        $info = array(
                            'newtgl6' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "07":
                        $info = array(
                            'newtgl7' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "08":
                        $info = array(
                            'newtgl8' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "09":
                        $info = array(
                            'newtgl9' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "10":
                        $info = array(
                            'newtgl10' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "11":
                        $info = array(
                            'newtgl11' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "12":
                        $info = array(
                            'newtgl12' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "13":
                        $info = array(
                            'newtgl13' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "14":
                        $info = array(
                            'newtgl14' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "15":
                        $info = array(
                            'newtgl15' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "16":
                        $info = array(
                            'newtgl16' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "17":
                        $info = array(
                            'newtgl17' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "18":
                        $info = array(
                            'newtgl18' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "19":
                        $info = array(
                            'newtgl19' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "20":
                        $info = array(
                            'newtgl20' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "21":
                        $info = array(
                            'newtgl21' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "22":
                        $info = array(
                            'newtgl22' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "23":
                        $info = array(
                            'newtgl23' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "24":
                        $info = array(
                            'newtgl24' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "25":
                        $info = array(
                            'newtgl25' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "26":
                        $info = array(
                            'newtgl26' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "27":
                        $info = array(
                            'newtgl27' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "28":
                        $info = array(
                            'newtgl28' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "29":
                        $info = array(
                            'newtgl29' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "30":
                        $info = array(
                            'newtgl30' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    case "31":
                        $info = array(
                            'newtgl31' => $kdjamkerja,
                        );
                        $builderx->where('nodok', $nama);
                        $builderx->where('nik', $nik);
                        //$builderx->where('kdregu', $kdregu);
                       $builderx->update( $info);
                        break;
                    default:
                        //echo "Your favorite color is neither red, blue, nor green!";
                }
                /*$info = array(

                    'kdmesin' => strtoupper($kdmesin),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                );
                $builderx->where('nik',$nik);
                $builderx->where('kdregu',$kdregu);
                $builderx->where('tgl',$tgl);
                $this->db->update('sc_trx.dtljadwalkerja',$info);*/
            }

            $info_final = array(
                'status' => 'A',
            );
            $builderx->where('nodok',$nama);
            $this->db->update('sc_tmp.mutasi_jadwalkerja_pernik',$info_final);
            return redirect()->to(base_url("trans/jadwal_new//"));
        } else {
            return redirect()->to(base_url("trans/jadwal_new//no_edit_tglmasuk"));
        }


    }

    function opr_belumjadwal(){
        $data['title']="NIK REGU YANG BELUM MEMILIKI JADWAL";
        if($this->uri->getSegment(4)==="failed"){
            $nik=$this->uri->getSegment(5);
            $kdregu=$this->uri->getSegment(6);
            $data['message']="<div class='alert alert-danger'>Nik: <b>$nik</b> Regu: <b>$kdregu</b> Tidak berhasil disimpan</div>";
        }
        else if($this->uri->getSegment(4)==="sukses"){
            $nik=$this->uri->getSegment(5);
            $kdregu=$this->uri->getSegment(6);
            $data['message']="<div class='alert alert-success'>Nik: <b>$nik</b> Regu: <b>$kdregu</b> Berhasil dijadwalkan</div>";
        } else {
            $data['message']="";
        }

        $data['list_nik']=$this->m_regu->q_cekdtlregu()->getResult();
        $data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        return $this->template->render('trans/jadwal/v_inputoprbelum.php',$data);

    }


    function simpan_oprdtljadwal(){
        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $tgl1=$this->request->getPost('tgl1');
        $tgl2=$this->request->getPost('tgl2');
        $tgl3=$this->request->getPost('tgl3');
        $tgl4=$this->request->getPost('tgl4');
        $tgl5=$this->request->getPost('tgl5');
        $tgl6=$this->request->getPost('tgl6');
        $tgl7=$this->request->getPost('tgl7');
        $tgl8=$this->request->getPost('tgl8');
        $tgl9=$this->request->getPost('tgl9');
        $tgl10=$this->request->getPost('tgl10');
        $tgl11=$this->request->getPost('tgl11');
        $tgl12=$this->request->getPost('tgl12');
        $tgl13=$this->request->getPost('tgl13');
        $tgl14=$this->request->getPost('tgl14');
        $tgl15=$this->request->getPost('tgl15');
        $tgl16=$this->request->getPost('tgl16');
        $tgl17=$this->request->getPost('tgl17');
        $tgl18=$this->request->getPost('tgl18');
        $tgl19=$this->request->getPost('tgl19');
        $tgl20=$this->request->getPost('tgl20');
        $tgl21=$this->request->getPost('tgl21');
        $tgl22=$this->request->getPost('tgl22');
        $tgl23=$this->request->getPost('tgl23');
        $tgl24=$this->request->getPost('tgl24');
        $tgl25=$this->request->getPost('tgl25');
        $tgl26=$this->request->getPost('tgl26');
        $tgl27=$this->request->getPost('tgl27');
        $tgl28=$this->request->getPost('tgl28');
        $tgl29=$this->request->getPost('tgl29');
        $tgl30=$this->request->getPost('tgl30');
        $tgl31=$this->request->getPost('tgl31');


        if ($tgl1<>'OFF') {
            $tgl=trim("$thn-$bln-01");
            $kdjamkerja=$tgl1;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }
        if ($tgl2<>'OFF') {
            $tgl=trim("$thn-$bln-02");
            $kdjamkerja=$tgl2;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        } if ($tgl3<>'OFF') {
            $tgl=date("$thn-$bln-03");
            $kdjamkerja=$tgl3;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }  if ($tgl4<>'OFF') {
            $tgl=date("$thn-$bln-04");
            $kdjamkerja=$tgl4;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }  if ($tgl5<>'OFF') {
            $tgl=date("$thn-$bln-05");
            $kdjamkerja=$tgl5;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl6<>'OFF') {
            $tgl=date("$thn-$bln-06");
            $kdjamkerja=$tgl6;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl7<>'OFF') {
            $tgl=date("$thn-$bln-07");
            $kdjamkerja=$tgl7;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl8<>'OFF') {
            $tgl=date("$thn-$bln-08");
            $kdjamkerja=$tgl8;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl9<>'OFF') {
            $tgl=date("$thn-$bln-09");
            $kdjamkerja=$tgl9;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl10<>'OFF') {
            $tgl=date("$thn-$bln-10");
            $kdjamkerja=$tgl10;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl11<>'OFF') {
            $tgl=date("$thn-$bln-11");
            $kdjamkerja=$tgl11;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl12<>'OFF') {
            $tgl=date("$thn-$bln-12");
            $kdjamkerja=$tgl12;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl13<>'OFF') {
            $tgl=date("$thn-$bln-13");
            $kdjamkerja=$tgl13;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl14<>'OFF') {
            $tgl=date("$thn-$bln-14");
            $kdjamkerja=$tgl14;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl15<>'OFF') {
            $tgl=date("$thn-$bln-15");
            $kdjamkerja=$tgl15;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl16<>'OFF') {
            $tgl=date("$thn-$bln-16");
            $kdjamkerja=$tgl16;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl17<>'OFF') {
            $tgl=date("$thn-$bln-17");
            $kdjamkerja=$tgl17;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl18<>'OFF') {
            $tgl=date("$thn-$bln-18");
            $kdjamkerja=$tgl18;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl19<>'OFF') {
            $tgl=date("$thn-$bln-19");
            $kdjamkerja=$tgl19;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl20<>'OFF') {
            $tgl=date("$thn-$bln-20");
            $kdjamkerja=$tgl20;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl21<>'OFF') {
            $tgl=date("$thn-$bln-21");
            $kdjamkerja=$tgl21;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl22<>'OFF') {
            $tgl=date("$thn-$bln-22");
            $kdjamkerja=$tgl22;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl23<>'OFF') {
            $tgl=date("$thn-$bln-23");
            $kdjamkerja=$tgl23;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl24<>'OFF') {
            $tgl=date("$thn-$bln-24");
            $kdjamkerja=$tgl24;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl25<>'OFF') {
            $tgl=date("$thn-$bln-25");
            $kdjamkerja=$tgl25;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl26<>'OFF') {
            $tgl=date("$thn-$bln-26");
            $kdjamkerja=$tgl26;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl27<>'OFF') {
            $tgl=date("$thn-$bln-27");
            $kdjamkerja=$tgl27;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl28<>'OFF') {
            $tgl=date("$thn-$bln-28");
            $kdjamkerja=$tgl28;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl29<>'OFF') {
            $tgl=date("$thn-$bln-29");
            $kdjamkerja=$tgl29;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl30<>'OFF') {
            $tgl=date("$thn-$bln-30");
            $kdjamkerja=$tgl30;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }   if ($tgl31<>'OFF') {
            $tgl=date("$thn-$bln-31");
            $kdjamkerja=$tgl31;
            $info = array(
                'nik' => $nik,
                'tgl' => $tgl,
                'kdjamkerja' => strtoupper($kdjamkerja),
                'kdregu' => strtoupper($kdregu),
                'inputdate' => date('d-m-Y H:i:s'),
                'inputby' => $this->session->get('nama')
            );
            $this->db->insert('sc_trx.dtljadwalkerja',$info);

        }
        $this->db->where('nik',$nik);
        $this->db->delete('sc_tmp.regu_opr');
        return redirect()->to(base_url("trans/jadwal_new/opr_belumjadwal/sukses/$nik/$kdregu"));

    }


    function inputjadwal_sama(){
        $data['title']='PROSES PENJADWALAN ';
        $data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();
        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        return $this->template->render('trans/jadwal/v_inputjadwalsama',$data);

    }
    function proses_inputjadwal_sama(){
        $userid=$this->session->get('nama');
        $kdregubaru=$this->request->getPost('kdregubaru');
        $kdregulama=$this->request->getPost('kdregulama');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $periode=$thn.'-'.$bln;
        $cek=$this->m_jadwalnew->q_reguadajadwal($kdregubaru,$periode)->getNumRows();
        if ($cek>0) {
            return redirect()->to(base_url("trans/jadwal_new//failed_sama/$bln/$kdregubaru"));

        } else {
            $this->db->query("delete from sc_tmp.jadwalkerja where kdregu='$kdregubaru' and to_char(tgl,'YYYY-MM')='$periode'");
            $this->db->query("insert into sc_tmp.jadwalkerja(kdregu,kodejamkerja,inputdate,inputby,tgl,status)
								select '$kdregubaru' as kdregu,kodejamkerja,inputdate,'$userid' as inputby,tgl,'I' as status from sc_trx.jadwalkerja
								where kdregu='$kdregulama' and to_char(tgl,'YYYY-MM')='$periode'");
            return redirect()->to(base_url("trans/jadwal_new/view_tmpjadwal/$kdregubaru/$bln/$thn"));
        }
    }

    function edit_jadwaloff(){
        $data['title']='EDIT JADWAL KERJA PER REGU';
        $kdregu=$this->request->getPost('kdregu2');
        $bln=$this->request->getPost('bln');
        $data['bln']=$bln;
        $thn=$this->request->getPost('thn');
        $data['thn']=$thn;
        $data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();
        //$data['list_karyawan']=$this->m_jadwalnew->q_karyawan()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        $dtl_jk=$this->m_jadwalnew->q_offjadwalkerja($thn,$bln,$kdregu)->getResult();
        $data['dtl']=$this->m_jadwalnew->q_offjadwalkerja($thn,$bln,$kdregu)->getRowArray();
        if(empty($data['dtl']['kdregu'])){
            return redirect()->to(base_url("trans/jadwal_new//no_data"));
        }
        $j=1;for($i=31;$i>=$j;$j++){
            $data["tgl$j"]=array('kodejamkerja' => null);
        }

        foreach($dtl_jk as $jk){
            if(($jk->tgl)===("$thn-$bln-01")){
                $tgl="$thn-$bln-01";
                $data['tgl1']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-02")){
                $tgl="$thn-$bln-02";
                $data['tgl2']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-03")){
                $tgl="$thn-$bln-03";
                $data['tgl3']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-04")){
                $tgl="$thn-$bln-04";
                $data['tgl4']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-05")){
                $tgl="$thn-$bln-05";
                $data['tgl5']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-06")){
                $tgl="$thn-$bln-06";
                $data['tgl6']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-07")){
                $tgl="$thn-$bln-07";
                $data['tgl7']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-08")){
                $tgl="$thn-$bln-08";
                $data['tgl8']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-09")){
                $tgl="$thn-$bln-09";
                $data['tgl9']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-10")){
                $tgl="$thn-$bln-10";
                $data['tgl10']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-11")){
                $tgl="$thn-$bln-11";
                $data['tgl11']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-12")){
                $tgl="$thn-$bln-12";
                $data['tgl12']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-13")){
                $tgl="$thn-$bln-13";
                $data['tgl13']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-14")){
                $tgl="$thn-$bln-14";
                $data['tgl14']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-15")){
                $tgl="$thn-$bln-15";
                $data['tgl15']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-16")){
                $tgl="$thn-$bln-16";
                $data['tgl16']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-17")){
                $tgl="$thn-$bln-17";
                $data['tgl17']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-18")){
                $tgl="$thn-$bln-18";
                $data['tgl18']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-19")){
                $tgl="$thn-$bln-19";
                $data['tgl19']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-20")){
                $tgl="$thn-$bln-20";
                $data['tgl20']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-21")){
                $tgl="$thn-$bln-21";
                $data['tgl21']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-22")){
                $tgl="$thn-$bln-22";
                $data['tgl22']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-23")){
                $tgl="$thn-$bln-23";
                $data['tgl23']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-24")){
                $tgl="$thn-$bln-24";
                $data['tgl24']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-25")){
                $tgl="$thn-$bln-25";
                $data['tgl25']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-26")){
                $tgl="$thn-$bln-26";
                $data['tgl26']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-27")){
                $tgl="$thn-$bln-27";
                $data['tgl27']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-28")){
                $tgl="$thn-$bln-28";
                $data['tgl28']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-29")){
                $tgl="$thn-$bln-29";
                $data['tgl29']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-30")){
                $tgl="$thn-$bln-30";
                $data['tgl30']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();
            }
            if(($jk->tgl)===("$thn-$bln-31")){
                $tgl="$thn-$bln-31";
                $data['tgl31']=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getRowArray();

            }

        }
        return $this->template->render('trans/jadwal/v_editdtljadwaloff',$data);
    }

    function edit_jadwalpernik(){
        $data['title']='EDIT JADWAL KERJA REGU PERNIK';
        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu2');
        $bln=$this->request->getPost('bln');
        $data['bln']=$bln;
        $thn=$this->request->getPost('thn');
        $data['thn']=$thn;
        //$data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();
        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan_ondtljadwalkerja($thn,$bln)->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        /*   $dtlrow=$this->m_jadwalnew->q_show_dtljadwalkerja_nik($thn,$bln,$nik)->getNumRows();
           if($dtlrow<1){
               return redirect()->to(base_url("trans/jadwal_new//no_data_2");
           } */
        $dtl_jk=$this->m_jadwalnew->q_show_dtljadwalkerja_nik($thn,$bln,$nik)->getResult();
        $data['dtl']=$this->m_jadwalnew->q_dtlreguopr($nik)->getRowArray();
        /*       if(empty($data['dtl']['kdregu'])){
                   return redirect()->to(base_url("trans/jadwal_new//no_data");
               } */
        $j=1;for($i=31;$i>=$j;$j++){
            $data["tgl$j"]=array('kodejamkerja' => null);
        }

        foreach($dtl_jk as $jk){

            if(($jk->tgl)===("$thn-$bln-01")){
                $tgl="$thn-$bln-01";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl1']=trim($cucu['kdjamkerja']); } else { $data['tgl1']=''; };

            }
            if(($jk->tgl)===("$thn-$bln-02")){
                $tgl="$thn-$bln-02";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl2']=trim($cucu['kdjamkerja']); } else { $data['tgl2']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-03")){
                $tgl="$thn-$bln-03";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl3']=trim($cucu['kdjamkerja']); } else { $data['tgl3']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-04")){
                $tgl="$thn-$bln-04";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl4']=trim($cucu['kdjamkerja']); } else { $data['tgl4']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-05")){
                $tgl="$thn-$bln-05";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl5']=trim($cucu['kdjamkerja']); } else { $data['tgl5']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-06")){
                $tgl="$thn-$bln-06";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl6']=trim($cucu['kdjamkerja']); } else { $data['tgl6']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-07")){
                $tgl="$thn-$bln-07";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl7']=trim($cucu['kdjamkerja']); } else { $data['tgl7']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-08")){
                $tgl="$thn-$bln-08";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl8']=trim($cucu['kdjamkerja']); } else { $data['tgl8']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-09")){
                $tgl="$thn-$bln-09";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl9']=trim($cucu['kdjamkerja']); } else { $data['tgl9']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-10")){
                $tgl="$thn-$bln-10";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl10']=trim($cucu['kdjamkerja']); } else { $data['tgl10']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-11")){
                $tgl="$thn-$bln-11";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl11']=trim($cucu['kdjamkerja']); } else { $data['tgl11']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-12")){
                $tgl="$thn-$bln-12";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl12']=trim($cucu['kdjamkerja']); } else { $data['tgl12']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-13")){
                $tgl="$thn-$bln-13";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl13']=trim($cucu['kdjamkerja']); } else { $data['tgl13']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-14")){
                $tgl="$thn-$bln-14";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl14']=trim($cucu['kdjamkerja']); } else { $data['tgl14']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-15")){
                $tgl="$thn-$bln-15";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl15']=trim($cucu['kdjamkerja']); } else { $data['tgl15']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-16")){
                $tgl="$thn-$bln-16";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl16']=trim($cucu['kdjamkerja']); } else { $data['tgl16']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-17")){
                $tgl="$thn-$bln-17";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl17']=trim($cucu['kdjamkerja']); } else { $data['tgl17']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-18")){
                $tgl="$thn-$bln-18";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl18']=trim($cucu['kdjamkerja']); } else { $data['tgl18']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-19")){
                $tgl="$thn-$bln-19";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl19']=trim($cucu['kdjamkerja']); } else { $data['tgl19']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-20")){
                $tgl="$thn-$bln-20";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl20']=trim($cucu['kdjamkerja']); } else { $data['tgl20']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-21")){
                $tgl="$thn-$bln-21";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl21']=trim($cucu['kdjamkerja']); } else { $data['tgl21']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-22")){
                $tgl="$thn-$bln-22";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl22']=trim($cucu['kdjamkerja']); } else { $data['tgl22']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-23")){
                $tgl="$thn-$bln-23";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl23']=trim($cucu['kdjamkerja']); } else { $data['tgl23']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-24")){
                $tgl="$thn-$bln-24";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl24']=trim($cucu['kdjamkerja']); } else { $data['tgl24']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-25")){
                $tgl="$thn-$bln-25";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl25']=trim($cucu['kdjamkerja']); } else { $data['tgl25']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-26")){
                $tgl="$thn-$bln-26";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl26']=trim($cucu['kdjamkerja']); } else { $data['tgl26']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-27")){
                $tgl="$thn-$bln-27";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl27']=trim($cucu['kdjamkerja']); } else { $data['tgl27']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-28")){
                $tgl="$thn-$bln-28";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl28']=trim($cucu['kdjamkerja']); } else { $data['tgl28']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-29")){
                $tgl="$thn-$bln-29";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl29']=trim($cucu['kdjamkerja']); } else { $data['tgl29']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-30")){
                $tgl="$thn-$bln-30";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl30']=trim($cucu['kdjamkerja']); } else { $data['tgl30']=''; };
            }
            if(($jk->tgl)===("$thn-$bln-31")){
                $tgl="$thn-$bln-31";
                $cucu=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getRowArray();
                if (isset($cucu['kdjamkerja'])){ $data['tgl31']=trim($cucu['kdjamkerja']); } else { $data['tgl31']=''; };

            }

        }
        return $this->template->render('trans/jadwal/v_editdtljadwalpernik',$data);
    }

    function simpan_offjadwal(){
        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $tgl1=$this->request->getPost('tgl1');
        $tgl2=$this->request->getPost('tgl2');
        $tgl3=$this->request->getPost('tgl3');
        $tgl4=$this->request->getPost('tgl4');
        $tgl5=$this->request->getPost('tgl5');
        $tgl6=$this->request->getPost('tgl6');
        $tgl7=$this->request->getPost('tgl7');
        $tgl8=$this->request->getPost('tgl8');
        $tgl9=$this->request->getPost('tgl9');
        $tgl10=$this->request->getPost('tgl10');
        $tgl11=$this->request->getPost('tgl11');
        $tgl12=$this->request->getPost('tgl12');
        $tgl13=$this->request->getPost('tgl13');
        $tgl14=$this->request->getPost('tgl14');
        $tgl15=$this->request->getPost('tgl15');
        $tgl16=$this->request->getPost('tgl16');
        $tgl17=$this->request->getPost('tgl17');
        $tgl18=$this->request->getPost('tgl18');
        $tgl19=$this->request->getPost('tgl19');
        $tgl20=$this->request->getPost('tgl20');
        $tgl21=$this->request->getPost('tgl21');
        $tgl22=$this->request->getPost('tgl22');
        $tgl23=$this->request->getPost('tgl23');
        $tgl24=$this->request->getPost('tgl24');
        $tgl25=$this->request->getPost('tgl25');
        $tgl26=$this->request->getPost('tgl26');
        $tgl27=$this->request->getPost('tgl27');
        $tgl28=$this->request->getPost('tgl28');
        $tgl29=$this->request->getPost('tgl29');
        $tgl30=$this->request->getPost('tgl30');
        $tgl31=$this->request->getPost('tgl31');


        if ($tgl1<>'OFF' and !empty($tgl1)) { /* tgl1 */
            $tgl=trim("$thn-$bln-01");
            $kdjamkerja=$tgl1;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }

        } else if($tgl1==='OFF'  and !empty($tgl1)){

            $tgl=trim("$thn-$bln-01");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl2<>'OFF' and !empty($tgl2)) { /* tgl2 */
            $tgl=trim("$thn-$bln-02");
            $kdjamkerja=$tgl2;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl2==='OFF' and !empty($tgl2)){

            $tgl=trim("$thn-$bln-02");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl3<>'OFF') { /* tgl3 */
            $tgl=trim("$thn-$bln-03");
            $kdjamkerja=$tgl3;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl3==='OFF'){

            $tgl=trim("$thn-$bln-03");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl4<>'OFF') { /* tgl4 */
            $tgl=trim("$thn-$bln-04");
            $kdjamkerja=$tgl4;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl4==='OFF'){

            $tgl=trim("$thn-$bln-04");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl5<>'OFF') { /* tgl5 */
            $tgl=trim("$thn-$bln-05");
            $kdjamkerja=$tgl5;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl5==='OFF'){

            $tgl=trim("$thn-$bln-05");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl6<>'OFF') { /* tgl6 */
            $tgl=trim("$thn-$bln-06");
            $kdjamkerja=$tgl6;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl6==='OFF'){

            $tgl=trim("$thn-$bln-06");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl7<>'OFF') { /* tgl7 */
            $tgl=trim("$thn-$bln-07");
            $kdjamkerja=$tgl7;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl7==='OFF'){

            $tgl=trim("$thn-$bln-07");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl8<>'OFF') { /* tgl8 */
            $tgl=trim("$thn-$bln-08");
            $kdjamkerja=$tgl8;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl8==='OFF'){

            $tgl=trim("$thn-$bln-08");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl9<>'OFF') { /* tgl9 */
            $tgl=trim("$thn-$bln-09");
            $kdjamkerja=$tgl9;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl9==='OFF'){

            $tgl=trim("$thn-$bln-09");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl10<>'OFF') { /* tgl10 */
            $tgl=trim("$thn-$bln-10");
            $kdjamkerja=$tgl10;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl10==='OFF'){

            $tgl=trim("$thn-$bln-10");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl11<>'OFF') { /* tgl11 */
            $tgl=trim("$thn-$bln-11");
            $kdjamkerja=$tgl11;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl11==='OFF'){

            $tgl=trim("$thn-$bln-11");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl12<>'OFF') { /* tgl12 */
            $tgl=trim("$thn-$bln-12");
            $kdjamkerja=$tgl12;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl12==='OFF'){

            $tgl=trim("$thn-$bln-12");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl13<>'OFF') { /* tgl13 */
            $tgl=trim("$thn-$bln-13");
            $kdjamkerja=$tgl13;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl13==='OFF'){

            $tgl=trim("$thn-$bln-13");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl14<>'OFF') { /* tgl14 */
            $tgl=trim("$thn-$bln-14");
            $kdjamkerja=$tgl14;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl14==='OFF'){

            $tgl=trim("$thn-$bln-14");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl15<>'OFF') { /* tgl15 */
            $tgl=trim("$thn-$bln-15");
            $kdjamkerja=$tgl15;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl15==='OFF'){

            $tgl=trim("$thn-$bln-15");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl16<>'OFF') { /* tgl16 */
            $tgl=trim("$thn-$bln-16");
            $kdjamkerja=$tgl16;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl16==='OFF'){

            $tgl=trim("$thn-$bln-16");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl17<>'OFF') { /* tgl17 */
            $tgl=trim("$thn-$bln-17");
            $kdjamkerja=$tgl17;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl17==='OFF'){

            $tgl=trim("$thn-$bln-17");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl18<>'OFF') { /* tgl18 */
            $tgl=trim("$thn-$bln-18");
            $kdjamkerja=$tgl18;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl18==='OFF'){

            $tgl=trim("$thn-$bln-18");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl19<>'OFF') { /* tgl19 */
            $tgl=trim("$thn-$bln-19");
            $kdjamkerja=$tgl19;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl19==='OFF'){

            $tgl=trim("$thn-$bln-19");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl20<>'OFF') { /* tgl20 */
            $tgl=trim("$thn-$bln-20");
            $kdjamkerja=$tgl20;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl20==='OFF'){

            $tgl=trim("$thn-$bln-20");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl21<>'OFF') { /* tgl21 */
            $tgl=trim("$thn-$bln-21");
            $kdjamkerja=$tgl21;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl21==='OFF'){

            $tgl=trim("$thn-$bln-21");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl22<>'OFF') { /* tgl22 */
            $tgl=trim("$thn-$bln-22");
            $kdjamkerja=$tgl22;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl22==='OFF'){

            $tgl=trim("$thn-$bln-22");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl23<>'OFF') { /* tgl23 */
            $tgl=trim("$thn-$bln-23");
            $kdjamkerja=$tgl23;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl23==='OFF'){

            $tgl=trim("$thn-$bln-23");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl24<>'OFF') { /* tgl24 */
            $tgl=trim("$thn-$bln-24");
            $kdjamkerja=$tgl24;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl24==='OFF'){

            $tgl=trim("$thn-$bln-24");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl25<>'OFF') { /* tgl25 */
            $tgl=trim("$thn-$bln-25");
            $kdjamkerja=$tgl25;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl25==='OFF'){

            $tgl=trim("$thn-$bln-25");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl26<>'OFF') { /* tgl26 */
            $tgl=trim("$thn-$bln-26");
            $kdjamkerja=$tgl26;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl26==='OFF'){

            $tgl=trim("$thn-$bln-26");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl27<>'OFF') { /* tgl27 */
            $tgl=trim("$thn-$bln-27");
            $kdjamkerja=$tgl27;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl27==='OFF'){

            $tgl=trim("$thn-$bln-27");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl28<>'OFF' and !empty($tgl28)) { /* tgl28 */
            $tgl=trim("$thn-$bln-28");
            $kdjamkerja=$tgl28;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl28==='OFF' and !empty($tgl28)){

            $tgl=trim("$thn-$bln-28");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl29<>'OFF' and !empty($tgl29)) { /* tgl29 */
            $tgl=trim("$thn-$bln-29");
            $kdjamkerja=$tgl29;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl29==='OFF' and !empty($tgl29)){

            $tgl=trim("$thn-$bln-29");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl30<>'OFF' and !empty($tgl30)) { /* tgl30 */
            $tgl=trim("$thn-$bln-30");
            $kdjamkerja=$tgl30;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.jadwalkerja',$info);
            } else{
                $info = array(
                    'kdregu' => strtoupper($kdregu),
                    'kodejamkerja' => strtoupper($kdjamkerja),
                    'inputdate' => date('d-m-Y H:i:s'),
                    'inputby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.jadwalkerja',$info);
            }
        } else if($tgl30==='OFF' and !empty($tgl30)){

            $tgl=trim("$thn-$bln-30");
            $this->db->where('tgl',$tgl);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.jadwalkerja');

        }
        if ($tgl31<>'OFF' and !empty($tgl31)) { /* tgl31 */
            $tgl=trim("$thn-$bln-31");
            $kdjamkerja=$tgl31;
            $cek_j=$this->m_jadwalnew->q_offjk_row($tgl,$kdregu)->getNumRows();
            $a_date = $tgl;
            $endday=date("Y-m-t", strtotime($tgl));
            if($endday<=$tgl){
                if(($cek_j)>0){
                    $info = array(
                        'kodejamkerja' => strtoupper($kdjamkerja),
                        'updatedate' => date('d-m-Y H:i:s'),
                        'updateby' => $this->session->get('nama')
                    );
                    $this->db->where('tgl',$tgl);
                    $this->db->where('kdregu',$kdregu);
                    $this->db->update('sc_trx.jadwalkerja',$info);
                } else{
                    $info = array(
                        'kdregu' => strtoupper($kdregu),
                        'kodejamkerja' => strtoupper($kdjamkerja),
                        'inputdate' => date('d-m-Y H:i:s'),
                        'inputby' => $this->session->get('nama'),
                        'tgl' => $tgl
                    );
                    $this->db->insert('sc_trx.jadwalkerja',$info);
                }
            }
        } else if($tgl31==='OFF' and !empty($tgl31)){

            $tgl=trim("$thn-$bln-31");
            $endday=date("Y-m-t", strtotime($tgl));
            if($endday<=$tgl){
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->delete('sc_trx.jadwalkerja');
            }
        }

        //return redirect()->to(base_url("trans/jadwal_new//edit_sukses/$kdregu/$bln/$thn");
        return redirect()->to(base_url("trans/jadwal_new//edit_sukses"));

    }

    function simpan_edit_dtljadwalkerja(){
        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $tgl1=$this->request->getPost('tgl1');
        $tgl2=$this->request->getPost('tgl2');
        $tgl3=$this->request->getPost('tgl3');
        $tgl4=$this->request->getPost('tgl4');
        $tgl5=$this->request->getPost('tgl5');
        $tgl6=$this->request->getPost('tgl6');
        $tgl7=$this->request->getPost('tgl7');
        $tgl8=$this->request->getPost('tgl8');
        $tgl9=$this->request->getPost('tgl9');
        $tgl10=$this->request->getPost('tgl10');
        $tgl11=$this->request->getPost('tgl11');
        $tgl12=$this->request->getPost('tgl12');
        $tgl13=$this->request->getPost('tgl13');
        $tgl14=$this->request->getPost('tgl14');
        $tgl15=$this->request->getPost('tgl15');
        $tgl16=$this->request->getPost('tgl16');
        $tgl17=$this->request->getPost('tgl17');
        $tgl18=$this->request->getPost('tgl18');
        $tgl19=$this->request->getPost('tgl19');
        $tgl20=$this->request->getPost('tgl20');
        $tgl21=$this->request->getPost('tgl21');
        $tgl22=$this->request->getPost('tgl22');
        $tgl23=$this->request->getPost('tgl23');
        $tgl24=$this->request->getPost('tgl24');
        $tgl25=$this->request->getPost('tgl25');
        $tgl26=$this->request->getPost('tgl26');
        $tgl27=$this->request->getPost('tgl27');
        $tgl28=$this->request->getPost('tgl28');
        $tgl29=$this->request->getPost('tgl29');
        $tgl30=$this->request->getPost('tgl30');
        $tgl31=$this->request->getPost('tgl31');


        /* BLOKING EDIT */
        $param_ent = " and nik = '$nik'";
        $inf_mkaryawan = $this->m_jadwalnew->q_master_karyawan($param_ent)->getRowArray();

        if ($tgl1<>'OFF' and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-01"))) { /* tgl1 */
            $tgl=trim("$thn-$bln-01");
            $kdjamkerja=$tgl1;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl1==='OFF' ){

            $tgl=trim("$thn-$bln-01");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl2<>'OFF' and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-02"))) { /* tgl2 */
            $tgl=trim("$thn-$bln-02");
            $kdjamkerja=$tgl2;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl2==='OFF'){

            $tgl=trim("$thn-$bln-02");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl3<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-03"))) { /* tgl3 */
            $tgl=trim("$thn-$bln-03");
            $kdjamkerja=$tgl3;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl3==='OFF'){

            $tgl=trim("$thn-$bln-03");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl4<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-04"))) { /* tgl4 */
            $tgl=trim("$thn-$bln-04");
            $kdjamkerja=$tgl4;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl4==='OFF'){

            $tgl=trim("$thn-$bln-04");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl5<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-05"))) { /* tgl5 */
            $tgl=trim("$thn-$bln-05");
            $kdjamkerja=$tgl5;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl5==='OFF'){

            $tgl=trim("$thn-$bln-05");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl6<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-06"))) { /* tgl6 */
            $tgl=trim("$thn-$bln-06");
            $kdjamkerja=$tgl6;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl6==='OFF'){

            $tgl=trim("$thn-$bln-06");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl7<>'OFF' and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-07"))) { /* tgl7 */
            $tgl=trim("$thn-$bln-07");
            $kdjamkerja=$tgl7;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl7==='OFF'){

            $tgl=trim("$thn-$bln-07");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl8<>'OFF' and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-08"))) { /* tgl8 */
            $tgl=trim("$thn-$bln-08");
            $kdjamkerja=$tgl8;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl8==='OFF'){

            $tgl=trim("$thn-$bln-08");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl9<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-09"))) { /* tgl9 */
            $tgl=trim("$thn-$bln-09");
            $kdjamkerja=$tgl9;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl9==='OFF'){

            $tgl=trim("$thn-$bln-09");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl10<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-10"))) { /* tgl10 */
            $tgl=trim("$thn-$bln-10");
            $kdjamkerja=$tgl10;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl10==='OFF'){

            $tgl=trim("$thn-$bln-10");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl11<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-11"))) { /* tgl11 */
            $tgl=trim("$thn-$bln-11");
            $kdjamkerja=$tgl11;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl11==='OFF'){

            $tgl=trim("$thn-$bln-11");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl12<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-12"))) { /* tgl12 */
            $tgl=trim("$thn-$bln-12");
            $kdjamkerja=$tgl12;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl12==='OFF'){

            $tgl=trim("$thn-$bln-12");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl13<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-13"))) { /* tgl13 */
            $tgl=trim("$thn-$bln-13");
            $kdjamkerja=$tgl13;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl13==='OFF'){

            $tgl=trim("$thn-$bln-13");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl14<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-14"))) { /* tgl14 */
            $tgl=trim("$thn-$bln-14");
            $kdjamkerja=$tgl14;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl14==='OFF'){

            $tgl=trim("$thn-$bln-14");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl15<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-15"))) { /* tgl15 */
            $tgl=trim("$thn-$bln-15");
            $kdjamkerja=$tgl15;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl15==='OFF'){

            $tgl=trim("$thn-$bln-15");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl16<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-16"))) { /* tgl16 */
            $tgl=trim("$thn-$bln-16");
            $kdjamkerja=$tgl16;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl16==='OFF'){

            $tgl=trim("$thn-$bln-16");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl17<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-17"))) { /* tgl17 */
            $tgl=trim("$thn-$bln-17");
            $kdjamkerja=$tgl17;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl17==='OFF'){

            $tgl=trim("$thn-$bln-17");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl18<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-18"))) { /* tgl18 */
            $tgl=trim("$thn-$bln-18");
            $kdjamkerja=$tgl18;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl18==='OFF'){

            $tgl=trim("$thn-$bln-18");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl19<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-19"))) { /* tgl19 */
            $tgl=trim("$thn-$bln-19");
            $kdjamkerja=$tgl19;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl19==='OFF'){

            $tgl=trim("$thn-$bln-19");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl20<>'OFF'   and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-20"))) { /* tgl20 */
            $tgl=trim("$thn-$bln-20");
            $kdjamkerja=$tgl20;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl20==='OFF'){

            $tgl=trim("$thn-$bln-20");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl21<>'OFF'    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-21"))) { /* tgl21 */
            $tgl=trim("$thn-$bln-21");
            $kdjamkerja=$tgl21;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl21==='OFF'){

            $tgl=trim("$thn-$bln-21");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl22<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-22"))) { /* tgl22 */
            $tgl=trim("$thn-$bln-22");
            $kdjamkerja=$tgl22;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl22==='OFF'){

            $tgl=trim("$thn-$bln-22");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl23<>'OFF'    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-23"))) { /* tgl23 */
            $tgl=trim("$thn-$bln-23");
            $kdjamkerja=$tgl23;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl23==='OFF'){

            $tgl=trim("$thn-$bln-23");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl24<>'OFF'    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-24"))) { /* tgl24 */
            $tgl=trim("$thn-$bln-24");
            $kdjamkerja=$tgl24;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl24==='OFF'){

            $tgl=trim("$thn-$bln-24");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl25<>'OFF'  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-25"))) { /* tgl25 */
            $tgl=trim("$thn-$bln-25");
            $kdjamkerja=$tgl25;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl25==='OFF'){

            $tgl=trim("$thn-$bln-25");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl26<>'OFF'    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-26"))) { /* tgl26 */
            $tgl=trim("$thn-$bln-26");
            $kdjamkerja=$tgl26;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl26==='OFF'){

            $tgl=trim("$thn-$bln-26");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl27<>'OFF'    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-27"))) { /* tgl27 */
            $tgl=trim("$thn-$bln-27");
            $kdjamkerja=$tgl27;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl27==='OFF'){

            $tgl=trim("$thn-$bln-27");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl28<>'OFF' and $tgl28 != ''  and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-28"))) { /* tgl28 */
            $tgl=trim("$thn-$bln-28");
            $kdjamkerja=$tgl28;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl28==='OFF'){

            $tgl=trim("$thn-$bln-28");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl29<>'OFF' and $tgl29 != ''    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-29"))) { /* tgl29 */
            $tgl=trim("$thn-$bln-29");
            $kdjamkerja=$tgl29;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl29==='OFF'){

            $tgl=trim("$thn-$bln-29");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl30<>'OFF' and $tgl30 != ''    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-30"))) { /* tgl30 */
            $tgl=trim("$thn-$bln-30");
            $kdjamkerja=$tgl30;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl30==='OFF') {

            $tgl=trim("$thn-$bln-30");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }
        if ($tgl31<>'OFF' and $tgl31 != ''    and ( $inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-31"))) { /* tgl31 */
            $tgl=trim("$thn-$bln-31");
            $kdjamkerja=$tgl31;
            $cek_j=$this->m_jadwalnew->q_show_dtljadwalkerja_nik_row($tgl,$nik)->getNumRows();
            if(($cek_j)>0){
                $info = array(
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama')
                );
                $this->db->where('nik',$nik);
                $this->db->where('tgl',$tgl);
                $this->db->where('kdregu',$kdregu);
                $this->db->update('sc_trx.dtljadwalkerja',$info);
            } else{
                $info = array(
                    'nik' => strtoupper($nik),
                    'kdregu' => strtoupper($kdregu),
                    'kdjamkerja' => strtoupper($kdjamkerja),
                    'updatedate' => date('d-m-Y H:i:s'),
                    'updateby' => $this->session->get('nama'),
                    'tgl' => $tgl
                );
                $this->db->insert('sc_trx.dtljadwalkerja',$info);
            }

        } else if($tgl31==='OFF'){

            $tgl=trim("$thn-$bln-31");
            $this->db->where('tgl',$tgl);
            $this->db->where('nik',$nik);
            $this->db->where('kdregu',$kdregu);
            $this->db->delete('sc_trx.dtljadwalkerja');
            //echo 'off',$tgl,$kdregu;
        }

        return redirect()->to(base_url("trans/jadwal_new//"));

    }


    public function excel_dtljadwalall(){

        $typedl=$this->request->getPost('typedl');
        $thn=$this->request->getPost('thn');
        $bln=$this->request->getPost('bln');
        $kdregu=$this->request->getPost('kdregu');

        if($typedl==='BS'){
            $datane=$this->m_jadwalnew->q_excel_jadwalregu($thn,$bln);
            $this->excel_generator->set_query($datane);
            $this->excel_generator->set_header(array('KODE REGU','KODE JAM KERJA','TANGGAL KERJA'
            ));
            $this->excel_generator->set_column(array('kdregu','kodejamkerja','tgl'
            ));
            $this->excel_generator->set_width(array(20,20,20
            ));
            $this->excel_generator->exportTo2007("JADWALKERJA_REGU");}
        else if($typedl==='BP'){
            $this->db->query("select sc_trx.pr_exceljadwalregu('$bln','$thn')");
            $datane=$this->m_jadwalnew->q_excel_jadwalregusamping($thn,$bln);
            $this->excel_generator->set_query($datane);
            $this->excel_generator->set_header(array('NAMA REGU','BULAN','TAHUN','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15',
                '16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'
            ));
            $this->excel_generator->set_column(array('kdregu','bulan','tahun','tgl1','tgl2','tgl3','tgl4','tgl5','tgl6','tgl7','tgl8','tgl9','tgl10','tgl11','tgl12','tgl13','tgl14','tgl15',
                'tgl16','tgl17','tgl18','tgl19','tgl20','tgl21','tgl22','tgl23','tgl24','tgl25','tgl26','tgl27','tgl28','tgl29','tgl30','tgl31'
            ));
            $this->excel_generator->set_width(array(8,8,8,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,
                4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4
            ));
            $this->excel_generator->exportTo2007("JADWALKERJA_REGU $bln $thn");}

        else if($typedl==='DL'){
            $this->db->query("select sc_trx.pr_listdtljadwalkerja('$bln','$thn')");
            $datane=$this->m_jadwalnew->q_excel_dtljadwalregu($thn,$bln);
            $this->excel_generator->set_query($datane);
            $this->excel_generator->set_header(array('NIK','NAMA LENGKAP','KODE REGU','BULAN','TAHUN',
                '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15',
                '16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'
            ));
            $this->excel_generator->set_column(array('nik','nmlengkap','kdregu','bulan','tahun',
                'tgl1','tgl2','tgl3','tgl4','tgl5','tgl6','tgl7','tgl8','tgl9','tgl10','tgl11','tgl12','tgl13','tgl14','tgl15',
                'tgl16','tgl17','tgl18','tgl19','tgl20','tgl21','tgl22','tgl23','tgl24','tgl25','tgl26','tgl27','tgl28','tgl29','tgl30','tgl31'
            ));
            $this->excel_generator->set_width(array(20,20,20,4,8,5,
                4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,
                4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4
            ));
            $this->excel_generator->exportTo2007("JADWALKERJA_DTL");

        }
        else{
            $this->db->query("select sc_trx.pr_listdtljadwalkerja('$bln','$thn')");
            $datane=$this->m_jadwalnew->q_excel_jadwal_untukprd($thn,$bln);
            $this->excel_generator->set_query($datane);
            $this->excel_generator->set_header(array('NIK','NAMA LENGKAP','KODE REGU','BULAN','TAHUN',
                '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15',
                '16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'
            ));
            $this->excel_generator->set_column(array('nik','nmlengkap','kdregu','bulan','tahun',
                'tgl1','tgl2','tgl3','tgl4','tgl5','tgl6','tgl7','tgl8','tgl9','tgl10','tgl11','tgl12','tgl13','tgl14','tgl15',
                'tgl16','tgl17','tgl18','tgl19','tgl20','tgl21','tgl22','tgl23','tgl24','tgl25','tgl26','tgl27','tgl28','tgl29','tgl30','tgl31'
            ));
            $this->excel_generator->set_width(array(20,20,20,4,8,5,
                4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,
                4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4
            ));
            $this->excel_generator->exportTo2007("JADWALKERJA_DTLUPRD");

        }


    }

    function v_gr_jadwal(){

        $kdregu=$this->request->getPost('kdregu');
        $bulan_awal=$this->request->getPost('bln_awal');
        $tahun_awal=$this->request->getPost('thn_awal');
        $bulan_akhir=$this->request->getPost('bln_akhir');
        $tahun_akhir=$this->request->getPost('thn_akhir');
        $type_gr=trim($this->request->getPost('type_gr'));
        if($type_gr==='gr_copying'){
            $this->db->query("select sc_trx.pr_gr_jadwalkerja('$bulan_awal','$tahun_awal','$bulan_akhir','$tahun_akhir','$kdregu')");
            $data['title']="GENERATE JADWAL KERJA ANTAR BULAN";
            $data['title1']="BULAN REFERENSI $kdregu  BULAN:$bulan_awal TAHUN:$tahun_awal";
            $data['title2']="BULAN GENERATE JADWAL $kdregu  BULAN:$bulan_akhir TAHUN:$tahun_akhir";
            $data['kdregu']=$kdregu; $data['tahun_akhir']=$tahun_akhir; $data['bulan_akhir']=$bulan_akhir;
            $data['list_tgljadwal']=$this->m_jadwalnew->q_calendar_jad($kdregu,$bulan_akhir,$tahun_akhir)->getResult();
            $data['list_tgljadwalrev']=$this->m_jadwalnew->q_calendar_jad_rev($kdregu,$bulan_awal,$tahun_awal)->getResult();
            return $this->template->render('trans/jadwal/v_gr_jadwal',$data);
        }
        else if($type_gr==='gr_template'){
            $cek_master_template=$this->m_jadwalnew->cek_master_template($kdregu,$bulan_akhir,$tahun_akhir)->getNumRows();
            $cek_jadwal_1moon=$this->m_jadwalnew->cek_jadwal_1moon($kdregu,$bulan_akhir,$tahun_akhir)->getNumRows();
            //if($cek_master_template<>0){
            //	return redirect()->to(base_url("trans/jadwal_new//template_sama/$bulan_akhir/$tahun_akhir/$kdregu");
            if($cek_jadwal_1moon<>0){
                return redirect()->to(base_url("trans/jadwal_new//jadwal_sama/$bulan_akhir/$tahun_akhir/$kdregu"));
            }
            else{
                $this->db->query("delete from sc_tmp.template_jadwal where kdregu='$kdregu' and bulan='$bulan_akhir' and tahun='$tahun_akhir'"); //delete query
                //$this->db->query("delete from sc_tmp.template_jadwal where kdregu='$kdregu' and bulan='$bulan_awal' and tahun='$tahun_awal'"); //delete query
                $this->db->query("select sc_tmp.pr_gr_setupjadwal('$bulan_akhir','$tahun_akhir', '$kdregu')");
                $data['title']="GENERATE JADWAL VIA TEMPLATE SETUP";
                $data['kdregu']=$kdregu; $data['tahun']=$tahun_akhir; $data['bulan']=$bulan_akhir;
                $data['type_gr']=$type_gr;
                $data['list_template']=$this->m_jadwalnew->q_template_jadwal($kdregu,$bulan_akhir,$tahun_akhir)->getResult();
                return $this->template->render('trans/jadwal/v_gr_jadwal_template',$data);
            }
        }
        else if($type_gr==='gr_templatev2'){
            $cek_master_templatev2=$this->m_jadwalnew->cek_master_templatev2($kdregu,$bulan_akhir,$tahun_akhir)->getNumRows();
            $cek_jadwal_1moon=$this->m_jadwalnew->cek_jadwal_1moon($kdregu,$bulan_akhir,$tahun_akhir)->getNumRows();
            //if($cek_master_template<>0){
            //	return redirect()->to(base_url("trans/jadwal_new//template_sama/$bulan_akhir/$tahun_akhir/$kdregu");
            if($cek_jadwal_1moon<>0){
                return redirect()->to(base_url("trans/jadwal_new//jadwal_sama/$bulan_akhir/$tahun_akhir/$kdregu"));
            }
            else{
                $this->db->query("delete from sc_tmp.template_jadwal_v2 where kdregu='$kdregu' and bulan='$bulan_akhir' and tahun='$tahun_akhir'"); //delete query
                //$this->db->query("delete from sc_tmp.template_jadwal where kdregu='$kdregu' and bulan='$bulan_awal' and tahun='$tahun_awal'"); //delete query
                $this->db->query("select sc_tmp.gr_v2_template('$bulan_akhir','$tahun_akhir', '$kdregu')");
                $data['title']="GENERATE JADWAL VIA TEMPLATE SETUP V2";
                $data['kdregu']=$kdregu; $data['tahun']=$tahun_akhir; $data['bulan']=$bulan_akhir;
                $data['type_gr']=$type_gr;
                $data['list_template']=$this->m_jadwalnew->q_template_jadwalv2($kdregu,$bulan_akhir,$tahun_akhir)->getResult();
                return $this->template->render('trans/jadwal/v_gr_jadwal_template_v2',$data);
            }
        }
    }

    function gr_jadwal_perbulan(){
        $kdregu=$this->request->getPost('kdregu');
        $bulan_akhir=$this->request->getPost('bln_akhir');
        $tahun_akhir=$this->request->getPost('thn_akhir');
        $type_gr=trim($this->request->getPost('type_gr'));
        $inputdate=date('d-m-Y H:i:s');
        $inputby=$this->session->get('nama');
        if($type_gr==='gr_copying'){

            $this->db->query("select sc_trx.pr_gr_insertjadwal('$bulan_akhir','$tahun_akhir','$kdregu')");
            return redirect()->to(base_url("trans/jadwal_new//gr_succes/$bulan_akhir/$tahun_akhir/$kdregu"));
        }
        else if($type_gr==='gr_template'){
            $this->db->query("update sc_tmp.template_jadwal set status='F' where kdregu='$kdregu' and bulan='$bulan_akhir' and tahun='$tahun_akhir'");
            $this->db->query("select sc_tmp.pr_template_jadwalkerja_ins('$bulan_akhir','$tahun_akhir','$kdregu','$inputdate','$inputby')");
            return redirect()->to(base_url("trans/jadwal_new//gr_succes/$bulan_akhir/$tahun_akhir/$kdregu"));
        } else if($type_gr==='gr_templatev2'){
            $this->db->query("update sc_tmp.template_jadwal_v2 set status='F' where kdregu='$kdregu' and bulan='$bulan_akhir' and tahun='$tahun_akhir'");
            $this->db->query("select sc_tmp.pr_template_jadwal_v2('$bulan_akhir','$tahun_akhir','$kdregu','$inputdate','$inputby')");
            return redirect()->to(base_url("trans/jadwal_new//gr_succes/$bulan_akhir/$tahun_akhir/$kdregu"));
        }

    }

    function reset_jadwal_bulanan(){
        $kdregu=$this->request->getPost('kdregu');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $tgl=trim($thn.$bln);
        $tglnow=date('Ym');

        //if($tgl>$tglnow){
        $this->db->query("delete from sc_trx.jadwalkerja where kdregu='$kdregu' and to_char(tgl,'yyyymm')>='$tgl';");
        $this->db->query("delete from sc_trx.dtljadwalkerja where kdregu='$kdregu' and to_char(tgl,'yyyymm')>='$tgl';");
        $this->db->query("delete from sc_mst.template_jadwal_v2 where kdregu='$kdregu' and bulan>='$bln' and tahun='$thn';");
        return redirect()->to(base_url("trans/jadwal_new//delete_success"));
        //}else{
        //return redirect()->to(base_url("trans/jadwal_new//rs_failed/$bln/$thn/$kdregu");
        //}

    }

    function v_template(){
        $kdregu=$this->request->getPost('kdregu');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $data['title']="TEMPLATE TAHUN $thn";
        $data['kdregu']=$kdregu; $data['tahun']=$thn; $data['bulan']=$bln;
        $data['list_template']=$this->m_jadwalnew->q_template_tahunan($kdregu,$thn)->getResult();
        return $this->template->render('trans/jadwal/v_template_tahunan',$data);
    }



    /*  NEW MENU FROM PENJADWALAN KARYAWAN I.A*/
    function import_jadwal(){
        $data['title']="IMPORT DATA JADWAL KERJA ";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.22'; $versirelease='I.T.B.22/RELEASE.401'; $releasedate=date('2019-08-21 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['list_regu']=$this->m_jadwalnew->q_regu_notin_map()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        /* END CODE UNTUK VERSI */
        $paramerror=" and userid='$nama' and modul='I.T.B.18'";
        $dtlerror=$this->m_jadwalnew->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_jadwalnew->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        $data['list_tmp_jadwalkerja_import']=$this->m_jadwalnew->q_tmp_jadwalkerja_import(" and inputby='$nama'")->getResult();
        $data['adaisi']=$this->m_jadwalnew->q_tmp_jadwalkerja_import(" and inputby='$nama'")->getRowArray();
        return $this->template->render('trans/jadwal/v_import_jadwal',$data);
        $this->m_jadwalnew->q_deltrxerror($paramerror);
    }

    function clear_tmp_jadwal_per_regu(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("delete from sc_tmp.jadwalkerja_import where inputby='$nama'");
        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.B.18');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.18',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/jadwal_new/import_jadwal'));
    }

    function final_data_jadwal_per_regu(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("update sc_tmp.jadwalkerja_import set status='F' where inputby='$nama'");
        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.B.18');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.18',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        //trigger ke 2 jadwal kerja
        //$nama = trim($this->session->get('nama'));
        //$this->db->query("update sc_tmp.jadwalkerja set status='F' where inputby='$nama'");
        //return redirect()->to(base_url('trans/jadwal_new/trans/jadwal_new');
        return redirect()->to(base_url('trans/jadwal_new/import_jadwal'));
    }

    function mapping_jam_kerja(){
        $data['title']='MAPPING JAM KERJA KARYAWAN';
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.19'; $versirelease='I.T.B.19/RELEASE.401'; $releasedate=date('2019-08-21 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['list_regu']=$this->m_jadwalnew->q_regu_notin_map()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        /* END CODE UNTUK VERSI */
        return $this->template->render('trans/jadwal/v_mapping_jam_kerja',$data);
    }

    function list_mapping_jam_kerja()
    {
        $list = $this->m_jadwalnew->get_t_jadwalkerja_jamkerja_mapping();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $id=$this->fiky_encryption->sealed(trim($lm->id));
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = trim($lm->nmregu);
            $row[] = trim($lm->nmjam_kerja);
            $row[] = trim($lm->jam_masuk);
            $row[] = trim($lm->jam_pulang);
            $row[] = trim($lm->mapjam_kerja);
            $row[] = trim($lm->chold);
            //add html for action
            $row[] = '
            <a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Ubah Data Mapping Jadwal Jam Kerja" onclick="ubah_map_jamkerja('."'".$id."'".')"><i class="fa fa-gear"></i> </a>
            <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus Data Mapping Jadwal Jam Kerja" onclick="hapus_map_jamkerja('."'".$id."'".')"><i class="fa fa-trash-o"></i> </a>
            ';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_jadwalnew->t_jadwalkerja_jamkerja_mapping_count_all(),
            "recordsFiltered" => $this->m_jadwalnew->t_jadwalkerja_jamkerja_mapping_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
        //echo json_encode($output);
    }

    function save_mapping_jam_kerja()
    {
        $nama = trim($this->session->get('nama'));
        $type = $this->request->getPost('type');
        $id = $this->request->getPost('id');
        $kdregu = strtoupper($this->request->getPost('kdregu'));
        $kdjam_kerja = strtoupper($this->request->getPost('kdjam_kerja'));
        $mapjam_kerja = strtoupper($this->request->getPost('mapjam_kerja'));
        $c_hold = $this->request->getPost('c_hold');
        $inputby = $nama;
        $inputdate = date('d-m-y H:i:s');
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch = trim($dtlbranch['branch']);
        if ($type==='INPUT') {
            $data = array(
                'kdregu' => $kdregu,
                'kdjam_kerja' => $kdjam_kerja,
                'mapjam_kerja' => $mapjam_kerja,
                'chold' => $c_hold,
                'status' => 'I',
                'inputby' => $inputby,
                'inputdate' => $inputdate
            );
            $this->m_jadwalnew->simpan_t_jadwalkerja_jamkerja_mapping($data);
            echo json_encode(array("status" => TRUE));
        } else if ($type==='EDIT') {
            $data = array(
                'kdjam_kerja' => $kdjam_kerja,
                'mapjam_kerja' => $mapjam_kerja,
                'chold' => $c_hold,
                'inputby' => $inputby,
                'inputdate' => $inputdate

            );

            $this->m_jadwalnew->ubah_t_jadwalkerja_jamkerja_mapping(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        } else if($type==='DELETE'){
            $this->m_jadwalnew->delete_t_jadwalkerja_jamkerja_mapping($id);
            echo json_encode(array("status" => TRUE));
        }

    }

    function show_edit_map_jadwal()
    {
        $enc_id=$this->uri->getSegment(4);
        $id=$this->fiky_encryption->unseal(trim($enc_id));
        $data = $this->m_jadwalnew->get_t_jadwalkerja_jamkerja_mapping_by_id($id);
        echo json_encode($data);
    }



    function proses_xls_jadwal() {
        $nama = trim($this->session->get('nama'));
        $this->db->where('nodok',$nama);
        $this->db->delete('sc_tmp.jadwalkerja_import');
        $path = "./assets/files/jadwalkerja/jadwalregu/data/";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        if ($this->request->getPost('save')) {
            $fileName = $_FILES['import']['name'];
            unlink($path.$fileName);

            $config['upload_path'] = $path;
            $config['file_name'] = $fileName;
            $config['allowed_types'] = 'xls|xlsx';
            $config['max_size']		= 10000;
            $this->upload->initialize($config);

            if(! $this->upload->do_upload('import') )
                $this->upload->display_errors();

            $inputFileName = $path.trim($fileName);


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

            if ((trim($JUDUL[0][0])<>'Kode Regu')){

                $this->db->where('userid',$nama);
                $this->db->where('modul','I.T.B.18');
                $this->db->delete('sc_mst.trxerror');
                $infotrxerror = array (
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => '',
                    'nomorakhir2' => '',
                    'modul' => 'I.T.B.18',
                );
                $this->db->insert('sc_mst.trxerror',$infotrxerror);
                return redirect()->to(base_url('trans/jadwal_new/import_jadwal'));
            }

            $no=1;
            for ($row = 2; $row <= $highestRow; $row++){  				//  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);
                //  Insert row data array into your database of choice here
                $data = array(
                    'nodok'=>$nama,
                    'kdregu'=>strtoupper(trim($rowData[0][0])),
                    'bulan'=>substr(strtoupper(trim($rowData[0][1])),4,2),
                    'tahun'=>substr(strtoupper(trim($rowData[0][1])),0,4),
                    'tgl1'=>strtoupper(trim($rowData[0][2])),
                    'tgl2'=>strtoupper(trim($rowData[0][3])),
                    'tgl3'=>strtoupper(trim($rowData[0][4])),
                    'tgl4'=>strtoupper(trim($rowData[0][5])),
                    'tgl5'=>strtoupper(trim($rowData[0][6])),
                    'tgl6'=>strtoupper(trim($rowData[0][7])),
                    'tgl7'=>strtoupper(trim($rowData[0][8])),
                    'tgl8'=>strtoupper(trim($rowData[0][9])),
                    'tgl9'=>strtoupper(trim($rowData[0][10])),
                    'tgl10'=>strtoupper(trim($rowData[0][11])),
                    'tgl11'=>strtoupper(trim($rowData[0][12])),
                    'tgl12'=>strtoupper(trim($rowData[0][13])),
                    'tgl13'=>strtoupper(trim($rowData[0][14])),
                    'tgl14'=>strtoupper(trim($rowData[0][15])),
                    'tgl15'=>strtoupper(trim($rowData[0][16])),
                    'tgl16'=>strtoupper(trim($rowData[0][17])),
                    'tgl17'=>strtoupper(trim($rowData[0][18])),
                    'tgl18'=>strtoupper(trim($rowData[0][19])),
                    'tgl19'=>strtoupper(trim($rowData[0][20])),
                    'tgl20'=>strtoupper(trim($rowData[0][21])),
                    'tgl21'=>strtoupper(trim($rowData[0][22])),
                    'tgl22'=>strtoupper(trim($rowData[0][23])),
                    'tgl23'=>strtoupper(trim($rowData[0][24])),
                    'tgl24'=>strtoupper(trim($rowData[0][25])),
                    'tgl25'=>strtoupper(trim($rowData[0][26])),
                    'tgl26'=>strtoupper(trim($rowData[0][27])),
                    'tgl27'=>strtoupper(trim($rowData[0][28])),
                    'tgl28'=>strtoupper(trim($rowData[0][29])),
                    'tgl29'=>strtoupper(trim($rowData[0][30])),
                    'tgl30'=>strtoupper(trim($rowData[0][31])),
                    'tgl31'=>strtoupper(trim($rowData[0][32])),
                    'status'=>'I',
                    'inputby'=>$nama,
                    'inputdate'=>date("Y-m-d H:i:s")
                );
                if (!empty($rowData[0][1])) {
                    $this->db->insert("sc_tmp.jadwalkerja_import",$data);
                    $this->db->where('userid',$nama);
                    $this->db->where('modul','I.T.B.18');
                    $this->db->delete('sc_mst.trxerror');

                    $infotrxerror = array (
                        'userid' => $nama,
                        'errorcode' => 0,
                        'nomorakhir1' => '',
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.18',
                    );
                    $this->db->insert('sc_mst.trxerror',$infotrxerror);
                }
                $no++;
            }

            return redirect()->to(base_url('trans/jadwal_new/import_jadwal'));
            //return redirect()->to(base_url('trans/jadwal_new/confirm_upload');
        }
    }

    function confirm_upload(){
        $data['title']='MAPPING JAM KERJA KARYAWAN';
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.18'; $versirelease='I.T.B.18/RELEASE.401'; $releasedate=date('2019-08-21 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.18'";
        $dtlerror=$this->m_jadwalnew->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_jadwalnew->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $data['list_import_data'] = $this->m_jadwalnew->q_tmp_jadwalkerja_import()->getResult();
        return $this->template->render('trans/jadwal/v_confirm_upload',$data);
        $this->m_jadwalnew->q_deltrxerror($paramerror);
    }

    function reload_mapping_jam_import(){
        $nama= $this->session->get('nama');
        $info = array(
            'status' => 'U',
        );
        $this->db->where('nodok',$nama);
        $this->db->update("sc_tmp.jadwalkerja_import",$info);

        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.B.18');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.18',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/jadwal_new/confirm_upload'));
    }

    function clear_mapping_jam_import(){
        $nama= $this->session->get('nama');
        $this->db->where('nodok',$nama);
        $this->db->delete("sc_tmp.jadwalkerja_import");

        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.B.18');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.18',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('trans/jadwal_new/import_jadwal'));
    }

    function proses_mapping_jam_import(){
        $nama=$this->session->get('nama');
        $cek = $this->m_jadwalnew->q_tglcek_unmap()->getNumRows();

        if ($cek>0) {
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.T.B.18');
            $this->db->delete('sc_mst.trxerror');
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 2,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.18',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url("trans/jadwal_new/confirm_upload"));
        } else {
            $nama= $this->session->get('nama');
            /* DELETE NODOK TMP JADWAL MAPPING */
            $this->db->where('inputby',$nama);
            $this->db->delete("sc_tmp.jadwalkerja");
            /* 2 */
            $info = array(
                'status' => 'F',
            );
            $this->db->where('nodok',$nama);
            $this->db->update("sc_tmp.jadwalkerja_import",$info);
            /* 3
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.T.B.18');
            $this->db->delete('sc_mst.trxerror');
            /* error handling
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.18',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            */

            $infoZ = array ( 'status' => 'F');
            $this->db->where('inputby',$nama);
            $this->db->update("sc_tmp.jadwalkerja",$infoZ);


            $this->db->where('nodok',$nama);
            $this->db->delete("sc_tmp.jadwalkerja_import");
            return redirect()->to(base_url('trans/jadwal_new//input_succes'));

        }


    }


    /* START MAPPING USER VIEW */
    function mapping_view_user(){
        //echo "test";
        $nama=$this->session->get('nama');
        $data['title']="Master Jam Kerja";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.24'; $versirelease='I.T.B.24/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.24'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        $param = " and coalesce(statuskepegawaian,'') != 'KO' ";
        $data['list_user'] =  $this->m_jadwalnew->q_read_user_karyawan($param)->getResult();
        $data['list_departmen'] =  $this->m_jadwalnew->q_read_departmen()->getResult();
        return $this->template->render('trans/jadwal/v_mapping_user_view_filter',$data);

        /*DEL*/
        $this->m_trxerror->q_deltrxerror($paramerror);
    }

    function list_mapping_view_user()
    {
        $list = $this->m_jadwalnew->get_t_mapping_view_user();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lm->nik;
            $row[] = $lm->username;
            $row[] = $lm->nmdept;
            $row[] = $lm->chold;
            //add html for action
            $row[] = '
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus Mapping User Filter" onclick="hapus_wilayah('."'".trim($lm->id)."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_jadwalnew->t_mapping_view_user_count_all(),
            "recordsFiltered" => $this->m_jadwalnew->t_mapping_view_user_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }
    function save_mapping_view_user()
    {
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch = trim($dtlbranch['branch']);
        $nama = trim($this->session->get('nama'));
        $type = $this->request->getPost('type');
        $username = trim($this->request->getPost('username'));
        $kddept = strtoupper($this->request->getPost('kddept'));
        $chold = strtoupper($this->request->getPost('chold'));
        $id = strtoupper($this->request->getPost('id'));
        $inputby = $nama;
        $inputdate = date('d-m-y H:i:s');
        $param = " and username='$username'";
        $dtl_mstuser = $this->m_jadwalnew->q_mstuser($param)->getRowArray();
        if($type==='INPUT'){
            $data = array(
                'nik' => trim($dtl_mstuser['nik']),
                'username' => $username,
                'kddept' => $kddept,
                'chold' => $chold,
                'inputby' => $inputby,
                'inputdate' => $inputdate,
            );
            $insert = $this->m_jadwalnew->simpan_t_mapping_view_user($data);
            echo json_encode(array("status" => TRUE));
        } else if ($type==='EDIT') {
            $data = array(
                'nik' => trim($dtl_mstuser['nik']),
                'username' => $username,
                'kddept' => $kddept,
                'chold' => $chold,
                'inputby' => $inputby,
                'inputdate' => $inputdate,
            );
            $this->m_jadwalnew->ubah_t_mapping_view_user(array('id' => $id), $data);
            echo json_encode(array("status" => TRUE));
        } else if($type==='DELETE'){
            $this->m_jadwalnew->hapus_t_mapping_view_user($id);
            echo json_encode(array("status" => TRUE));
        }


    }
    function show_edit_mapping_view_user($id)
    {
        $data = $this->m_master->get_t_mapping_view_user_by_id($id);
        echo json_encode($data);
    }
    function show_del_mapping_view_user($id)
    {
        $data = $this->m_jadwalnew->get_t_mapping_view_user_by_id($id);
        echo json_encode($data);
    }




    /* END MAPPING USER VIEW */

    /* START VERIFIKASI PERUBAHAN */
    function verifikasi_perubahan(){
        //echo "test";
        $nama=$this->session->get('nama');
        $data['title']="Verifikasi Perubahan Jadwal Kerja";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.25'; $versirelease='I.T.B.25/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.25'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        $paramtmpx = " and nodok='$nama'";
        $dtlrow=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();
        if ($dtlrow>0){
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.T.B.25');
            $builder_trxerror->delete();
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 8,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik'));
        }

        $param = " and coalesce(resign,'NO') != 'YES' ";
        $data['list_user'] =  $this->m_jadwalnew->q_read_user_karyawan($param)->getResult();
        $data['list_departmen'] =  $this->m_jadwalnew->q_read_departmen()->getResult();
        $this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/jadwal/v_verifikasi_jadwal_kerja',$data);
        /* DEL TRXERROR */

    }

    function list_verifikasi_jadwal_kerja()
    {
        $nama = $this->session->get('nama');
        $list = $this->m_jadwalnew->get_t_mutasi_jadwal_nik_user();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $enc_nodok = $this->fiky_encryption->sealed($lm->nodok);
            $no++;
            $row = array();
            $row[] = $no;
            //add html for action
            if (trim($lm->status)==='A') {

                    $row[] = '
                  <a class="btn btn-sm btn-primary" href="'.base_url("/trans/jadwal_new/ubah_verifikasi_mutasi_jadwalpernik/$enc_nodok").'" title="Ubah Data Pindah Jadwal"><i class="fa fa-gear"></i> </a>
				  <a class="btn btn-sm btn-danger" href="'.base_url("/trans/jadwal_new/batal_verifikasi_mutasi_jadwalpernik/$enc_nodok").'"  onclick="return confirm(\'Dokumen akan dilakukan pembatalan, anda yakin??\')" title="Batal Perubahan Jadwal Nik"><i class="fa fa-close"></i> </a>
				  <a class="btn btn-sm btn-default" href="'.base_url("/trans/jadwal_new/detail_verifikasi_mutasi_jadwalpernik/$enc_nodok").'" title="Detail Data Pindah Jadwal"><i class="fa fa-bars"></i> </a>';
                

            } else {
                $row[] = '
				  <a class="btn btn-sm btn-default" href="'.base_url("/trans/jadwal_new/detail_verifikasi_mutasi_jadwalpernik/$enc_nodok").'" title="Detail Mutasi Regu"><i class="fa fa-bars"></i> </a>';
            }

            $row[] = $lm->nodok;
            $row[] = $lm->periode;
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->nmdept;
            $row[] = $lm->kdregu;
            $row[] = $lm->nmstatus;
            $row[] = $lm->inputnameby;
            $row[] = date('d-m-Y H:i:s',strtotime(trim($lm->inputdate)));


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_jadwalnew->t_mutasi_jadwal_nik_count_all(),
            "recordsFiltered" => $this->m_jadwalnew->t_mutasi_jadwal_nik_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function edit_verifikasi_mutasi_jadwalpernik(){
        $data['title']='EDIT JADWAL KERJA REGU PERNIK';

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.25'; $versirelease='I.T.B.25/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.25'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu2');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $nama = trim($this->session->get('nama'));
        //$data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();

        $paramtmpx = " and nodok='$nama'";
        //$dtl_jk=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getResult();

        $dtlrow=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();
        $data['list_jadwal']=$this->m_jadwalnew->q_showgrid_mutasi($paramtmpx)->getResult();



        if($dtlrow>0){
            /**/
        } else {
            $fill = array (
                'nodok' => $nama,
                'nik' => '',
                'kdregu' => '',
                'periode' => $thn.$bln,
                'status' => 'I',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('sc_tmp.mutasi_jadwalkerja_pernik ',$fill);
        }
        $cucu=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getRowArray();
        $thn = substr($cucu['periode'],0,4);
        $bln = substr($cucu['periode'],4,2);
        $data['thnx']=$thn;
        $data['blnx']=$bln;
        $data['tahun']=$thn;
        $data['bulan']=$bln;
        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan_ondtljadwalkerja($thn,$bln)->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        $data['dtl']=$this->m_jadwalnew->q_dtlreguopr(trim($cucu['nik']))->getRowArray();
        $data['dtlcucu']=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getRowArray();

        if (isset($cucu['nik'])){ $data['nik']=trim($cucu['nik']); } else { $data['nik']=''; };
        if (isset($cucu['kdregu'])){ $data['kdregu']=trim($cucu['kdregu']); } else { $data['kdregu']=''; };
        if (isset($cucu['newtgl1'])){ $data['tgl1']=trim($cucu['newtgl1']); } else { $data['tgl1']=''; };
        if (isset($cucu['newtgl2'])){ $data['tgl2']=trim($cucu['newtgl2']); } else { $data['tgl2']=''; };
        if (isset($cucu['newtgl3'])){ $data['tgl3']=trim($cucu['newtgl3']); } else { $data['tgl3']=''; };
        if (isset($cucu['newtgl4'])){ $data['tgl4']=trim($cucu['newtgl4']); } else { $data['tgl4']=''; };
        if (isset($cucu['newtgl5'])){ $data['tgl5']=trim($cucu['newtgl5']); } else { $data['tgl5']=''; };
        if (isset($cucu['newtgl6'])){ $data['tgl6']=trim($cucu['newtgl6']); } else { $data['tgl6']=''; };
        if (isset($cucu['newtgl7'])){ $data['tgl7']=trim($cucu['newtgl7']); } else { $data['tgl7']=''; };
        if (isset($cucu['newtgl8'])){ $data['tgl8']=trim($cucu['newtgl8']); } else { $data['tgl8']=''; };
        if (isset($cucu['newtgl9'])){ $data['tgl9']=trim($cucu['newtgl9']); } else { $data['tgl9']=''; };
        if (isset($cucu['newtgl10'])){ $data['tgl10']=trim($cucu['newtgl10']); } else { $data['tgl10']=''; };
        if (isset($cucu['newtgl11'])){ $data['tgl11']=trim($cucu['newtgl11']); } else { $data['tgl11']=''; };
        if (isset($cucu['newtgl12'])){ $data['tgl12']=trim($cucu['newtgl12']); } else { $data['tgl12']=''; };
        if (isset($cucu['newtgl13'])){ $data['tgl13']=trim($cucu['newtgl13']); } else { $data['tgl13']=''; };
        if (isset($cucu['newtgl14'])){ $data['tgl14']=trim($cucu['newtgl14']); } else { $data['tgl14']=''; };
        if (isset($cucu['newtgl15'])){ $data['tgl15']=trim($cucu['newtgl15']); } else { $data['tgl15']=''; };
        if (isset($cucu['newtgl16'])){ $data['tgl16']=trim($cucu['newtgl16']); } else { $data['tgl16']=''; };
        if (isset($cucu['newtgl17'])){ $data['tgl17']=trim($cucu['newtgl17']); } else { $data['tgl17']=''; };
        if (isset($cucu['newtgl18'])){ $data['tgl18']=trim($cucu['newtgl18']); } else { $data['tgl18']=''; };
        if (isset($cucu['newtgl19'])){ $data['tgl19']=trim($cucu['newtgl19']); } else { $data['tgl19']=''; };
        if (isset($cucu['newtgl20'])){ $data['tgl20']=trim($cucu['newtgl20']); } else { $data['tgl20']=''; };
        if (isset($cucu['newtgl21'])){ $data['tgl21']=trim($cucu['newtgl21']); } else { $data['tgl21']=''; };
        if (isset($cucu['newtgl22'])){ $data['tgl22']=trim($cucu['newtgl22']); } else { $data['tgl22']=''; };
        if (isset($cucu['newtgl23'])){ $data['tgl23']=trim($cucu['newtgl23']); } else { $data['tgl23']=''; };
        if (isset($cucu['newtgl24'])){ $data['tgl24']=trim($cucu['newtgl24']); } else { $data['tgl24']=''; };
        if (isset($cucu['newtgl25'])){ $data['tgl25']=trim($cucu['newtgl25']); } else { $data['tgl25']=''; };
        if (isset($cucu['newtgl26'])){ $data['tgl26']=trim($cucu['newtgl26']); } else { $data['tgl26']=''; };
        if (isset($cucu['newtgl27'])){ $data['tgl27']=trim($cucu['newtgl27']); } else { $data['tgl27']=''; };
        if (isset($cucu['newtgl28'])){ $data['tgl28']=trim($cucu['newtgl28']); } else { $data['tgl28']=''; };
        if (isset($cucu['newtgl29'])){ $data['tgl29']=trim($cucu['newtgl29']); } else { $data['tgl29']=''; };
        if (isset($cucu['newtgl30'])){ $data['tgl30']=trim($cucu['newtgl30']); } else { $data['tgl30']=''; };
        if (isset($cucu['newtgl31'])){ $data['tgl31']=trim($cucu['newtgl31']); } else { $data['tgl31']=''; };

        /* DEL TRXERROR */
        $this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/jadwal/v_edit_verifikasi_mutasi_jadwalpernik',$data);

    }

    function clear_verifikasi_mutasi_jadwalpernik(){
        $nama = trim($this->session->get('nama'));
        $builder = $this->db->table('sc_tmp.mutasi_jadwalkerja_pernik');
        $builder->where('nodok' , $nama);
        $builder->delete('');

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.T.B.25');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 11,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.25',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url("trans/jadwal_new/verifikasi_perubahan"));
    }

    function load_verifikasi_mutasi_jadwalpernik(){
        ECHO $nama = trim($this->session->get('nama'));
        ECHO $nik = trim($this->request->getPost('nik'));
        ECHO $bln = trim($this->request->getPost('bln'));
        ECHO $thn = trim($this->request->getPost('thn'));

        /* tarik dari nik */
        $this->m_jadwalnew->load_tmp_nik($nik,$bln,$thn);

        $this->db->where('userid',$nama);
        $this->db->where('modul','I.T.B.25');
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.25',
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url("trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik"));
    }

    function simpan_verifikasi_mutasi_jadwalpernik()
    {
        $nama = trim($this->session->get('nama'));
        $nik = trim($this->request->getPost('nik'));
        $kdregu = trim($this->request->getPost('kdregu'));
        $bln = $this->request->getPost('bln');
        $thn = $this->request->getPost('thn');
        $tgl1 = $this->request->getPost('tgl1');
        $tgl2 = $this->request->getPost('tgl2');
        $tgl3 = $this->request->getPost('tgl3');
        $tgl4 = $this->request->getPost('tgl4');
        $tgl5 = $this->request->getPost('tgl5');
        $tgl6 = $this->request->getPost('tgl6');
        $tgl7 = $this->request->getPost('tgl7');
        $tgl8 = $this->request->getPost('tgl8');
        $tgl9 = $this->request->getPost('tgl9');
        $tgl10 = $this->request->getPost('tgl10');
        $tgl11 = $this->request->getPost('tgl11');
        $tgl12 = $this->request->getPost('tgl12');
        $tgl13 = $this->request->getPost('tgl13');
        $tgl14 = $this->request->getPost('tgl14');
        $tgl15 = $this->request->getPost('tgl15');
        $tgl16 = $this->request->getPost('tgl16');
        $tgl17 = $this->request->getPost('tgl17');
        $tgl18 = $this->request->getPost('tgl18');
        $tgl19 = $this->request->getPost('tgl19');
        $tgl20 = $this->request->getPost('tgl20');
        $tgl21 = $this->request->getPost('tgl21');
        $tgl22 = $this->request->getPost('tgl22');
        $tgl23 = $this->request->getPost('tgl23');
        $tgl24 = $this->request->getPost('tgl24');
        $tgl25 = $this->request->getPost('tgl25');
        $tgl26 = $this->request->getPost('tgl26');
        $tgl27 = $this->request->getPost('tgl27');
        $tgl28 = $this->request->getPost('tgl28');
        $tgl29 = $this->request->getPost('tgl29');
        $tgl30 = $this->request->getPost('tgl30');
        $tgl31 = $this->request->getPost('tgl31');


        /* BLOKING EDIT */
        $param_ent = " and nik = '$nik'";
        $inf_mkaryawan = $this->m_jadwalnew->q_master_karyawan($param_ent)->getRowArray();

        if (empty($kdregu)) {
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.T.B.25');
            $this->db->delete('sc_mst.trxerror');
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 9,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url("trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik"));
        } else {
            /* EKSEKUSI */
            if ($tgl1 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-01"))) { /* tgl1 */
                $tgl = trim("$thn-$bln-01");
                $kdjamkerja = $tgl1;

                $info = array(
                    'newtgl1' => $tgl1,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl1 === 'OFF') {

                $info = array(
                    'newtgl1' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 2*/
            if ($tgl2 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-02"))) { /* tgl2 */
                $tgl = trim("$thn-$bln-02");
                $kdjamkerja = $tgl2;

                $info = array(
                    'newtgl2' => $tgl2,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl2 === 'OFF') {

                $info = array(
                    'newtgl2' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 3*/
            if ($tgl3 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-03"))) { /* tgl3 */
                $tgl = trim("$thn-$bln-03");
                $kdjamkerja = $tgl3;

                $info = array(
                    'newtgl3' => $tgl3,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl1 === 'OFF') {

                $info = array(
                    'newtgl3' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 4*/
            if ($tgl4 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-04"))) { /* tgl4 */
                $tgl = trim("$thn-$bln-04");
                $kdjamkerja = $tgl4;

                $info = array(
                    'newtgl4' => $tgl4,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl4 === 'OFF') {

                $info = array(
                    'newtgl4' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 5*/
            if ($tgl5 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-05"))) { /* tgl5 */
                $tgl = trim("$thn-$bln-05");
                $kdjamkerja = $tgl5;

                $info = array(
                    'newtgl5' => $tgl5,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl5 === 'OFF') {

                $info = array(
                    'newtgl5' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 6*/
            if ($tgl6 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-06"))) { /* tgl6 */
                $tgl = trim("$thn-$bln-06");
                $kdjamkerja = $tgl6;

                $info = array(
                    'newtgl6' => $tgl6,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl6 === 'OFF') {

                $info = array(
                    'newtgl6' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 7*/
            if ($tgl7 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-07"))) { /* tgl7 */
                $tgl = trim("$thn-$bln-07");
                $kdjamkerja = $tgl7;

                $info = array(
                    'newtgl7' => $tgl7,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl7 === 'OFF') {

                $info = array(
                    'newtgl7' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 8*/
            if ($tgl8 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-08"))) { /* tgl8 */
                $tgl = trim("$thn-$bln-08");
                $kdjamkerja = $tgl1;

                $info = array(
                    'newtgl8' => $tgl8,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl8 === 'OFF') {

                $info = array(
                    'newtgl8' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 9*/
            if ($tgl9 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-09"))) { /* tgl9 */
                $tgl = trim("$thn-$bln-09");
                $kdjamkerja = $tgl9;

                $info = array(
                    'newtgl9' => $tgl9,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl9=== 'OFF') {

                $info = array(
                    'newtgl9' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 10*/
            if ($tgl10 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-10"))) { /* tgl10 */
                $tgl = trim("$thn-$bln-10");
                $kdjamkerja = $tgl1;

                $info = array(
                    'newtgl10' => $tgl10,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl10 === 'OFF') {

                $info = array(
                    'newtgl10' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 11*/
            if ($tgl11 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-11"))) { /* tgl11 */
                $tgl = trim("$thn-$bln-11");
                $kdjamkerja = $tgl11;

                $info = array(
                    'newtgl11' => $tgl11,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl11 === 'OFF') {

                $info = array(
                    'newtgl11' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 12*/
            if ($tgl12 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-12"))) { /* tgl12 */
                $tgl = trim("$thn-$bln-12");
                $kdjamkerja = $tgl12;

                $info = array(
                    'newtgl12' => $tgl12,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl12 === 'OFF') {

                $info = array(
                    'newtgl12' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 13*/
            if ($tgl13 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-13"))) { /* tgl13 */
                $tgl = trim("$thn-$bln-13");
                $kdjamkerja = $tgl13;

                $info = array(
                    'newtgl13' => $tgl13,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl13 === 'OFF') {

                $info = array(
                    'newtgl13' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 14*/
            if ($tgl14 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-14"))) { /* tgl14 */
                $tgl = trim("$thn-$bln-14");
                $kdjamkerja = $tgl14;

                $info = array(
                    'newtgl14' => $tgl14,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl14 === 'OFF') {

                $info = array(
                    'newtgl14' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 15*/
            if ($tgl15 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-15"))) { /* tgl15*/
                $tgl = trim("$thn-$bln-15");
                $kdjamkerja = $tgl15;

                $info = array(
                    'newtgl15' => $tgl15,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl15 === 'OFF') {

                $info = array(
                    'newtgl15' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 16*/
            if ($tgl16 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-16"))) { /* tgl16 */
                $tgl = trim("$thn-$bln-16");
                $kdjamkerja = $tgl16;

                $info = array(
                    'newtgl16' => $tgl16,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl16 === 'OFF') {

                $info = array(
                    'newtgl16' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 17*/
            if ($tgl17 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-17"))) { /* tgl17 */
                $tgl = trim("$thn-$bln-17");
                $kdjamkerja = $tgl17;

                $info = array(
                    'newtgl17' => $tgl17,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl17 === 'OFF') {

                $info = array(
                    'newtgl17' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 18*/
            if ($tgl18 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-18"))) { /* tgl18 */
                $tgl = trim("$thn-$bln-18");
                $kdjamkerja = $tgl18;

                $info = array(
                    'newtgl18' => $tgl18,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl18 === 'OFF') {

                $info = array(
                    'newtgl18' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 19*/
            if ($tgl19 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-19"))) { /* tgl19 */
                $tgl = trim("$thn-$bln-19");
                $kdjamkerja = $tgl19;

                $info = array(
                    'newtgl19' => $tgl19,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl19 === 'OFF') {

                $info = array(
                    'newtgl19' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 20*/
            if ($tgl20 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-20"))) { /* tgl20 */
                $tgl = trim("$thn-$bln-20");
                $kdjamkerja = $tgl20;

                $info = array(
                    'newtgl20' => $tgl20,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl20 === 'OFF') {

                $info = array(
                    'newtgl20' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }/*TGL 21*/
            if ($tgl21 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-21"))) { /* tgl1 */
                $tgl = trim("$thn-$bln-21");
                $kdjamkerja = $tgl21;

                $info = array(
                    'newtgl21' => $tgl21,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl21 === 'OFF') {

                $info = array(
                    'newtgl21' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 22*/
            if ($tgl22 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-22"))) { /* tgl22 */
                $tgl = trim("$thn-$bln-22");
                $kdjamkerja = $tgl22;

                $info = array(
                    'newtgl22' => $tgl22,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl22 === 'OFF') {

                $info = array(
                    'newtgl22' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 23*/
            if ($tgl23 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-23"))) { /* tgl23 */
                $tgl = trim("$thn-$bln-23");
                $kdjamkerja = $tgl23;

                $info = array(
                    'newtgl23' => $tgl23,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl23 === 'OFF') {

                $info = array(
                    'newtgl23' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 24*/
            if ($tgl24 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-24"))) { /* tgl24 */
                $tgl = trim("$thn-$bln-24");
                $kdjamkerja = $tgl24;

                $info = array(
                    'newtgl24' => $tgl24,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl24 === 'OFF') {

                $info = array(
                    'newtgl24' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 25*/
            if ($tgl25 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-25"))) { /* tgl25 */
                $tgl = trim("$thn-$bln-25");
                $kdjamkerja = $tgl25;

                $info = array(
                    'newtgl25' => $tgl25,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl25 === 'OFF') {

                $info = array(
                    'newtgl25' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 26*/
            if ($tgl26 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-26"))) { /* tgl26 */
                $tgl = trim("$thn-$bln-26");
                $kdjamkerja = $tgl26;

                $info = array(
                    'newtgl26' => $tgl26,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl26 === 'OFF') {

                $info = array(
                    'newtgl26' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 27*/
            if ($tgl27 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-27"))) { /* tgl27 */
                $tgl = trim("$thn-$bln-27");
                $kdjamkerja = $tgl27;

                $info = array(
                    'newtgl27' => $tgl27,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl27 === 'OFF') {

                $info = array(
                    'newtgl27' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 28*/
            if ($tgl28 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-28"))) { /* tgl28 */
                $tgl = trim("$thn-$bln-28");
                $kdjamkerja = $tgl28;

                $info = array(
                    'newtgl28' => $tgl28,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl28 === 'OFF') {

                $info = array(
                    'newtgl28' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 29*/
            if ($tgl29 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-29"))) { /* tgl29 */
                $tgl = trim("$thn-$bln-29");
                $kdjamkerja = $tgl29;

                $info = array(
                    'newtgl29' => $tgl29,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl29 === 'OFF') {

                $info = array(
                    'newtgl29' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 30*/
            if ($tgl30 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-30"))) { /* tgl30 */
                $tgl = trim("$thn-$bln-30");
                $kdjamkerja = $tgl30;

                $info = array(
                    'newtgl30' => $tgl30,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl30 === 'OFF') {

                $info = array(
                    'newtgl30' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }
            /*TGL 31*/
            if ($tgl1 !== 'OFF' and ($inf_mkaryawan['tglmasukkerja'] <= trim("$thn-$bln-31"))) { /* tgl31 */
                $tgl = trim("$thn-$bln-31");
                $kdjamkerja = $tgl31;

                $info = array(
                    'newtgl31' => $tgl31,
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            } else if ($tgl31 === 'OFF') {

                $info = array(
                    'newtgl31' => 'OFF',
                );
                $this->db->where('nodok', $nama);
                $this->db->where('nik', $nik);
                //$this->db->where('kdregu', $kdregu);
               $builderx->update( $info);

            }


            $this->db->where('userid',$nama);
            $this->db->where('modul','I.T.B.25');
            $this->db->delete('sc_mst.trxerror');
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url("trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik"));
        }


    }
    function finalisasi_verifikasi_mutasi_jadwalpernik(){
        $nama = trim($this->session->get('nama'));
        $paramtmpx = " and nodok='$nama'";
        $dtlrow=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();
        if ($dtlrow<0) {
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid',$nama);
            $builder_trxerror->where('modul','I.T.B.25');
            $builder_trxerror->delete();
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 4,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik'));
        } else {
            $builderx = $this->db->table('sc_tmp.mutasi_jadwalkerja_pernik');
            $info_final = array(
                'status' => 'A',
            );
            $builderx->where('nodok',$nama);
            $builderx->update($info_final);
            return redirect()->to(base_url('trans/jadwal_new/verifikasi_perubahan'));
        }
    }

    function ubah_verifikasi_mutasi_jadwalpernik(){
        $nodok = $this->fiky_encryption->unseal($this->uri->getSegment(4));
        $nama = trim($this->session->get('nama'));
        $inputdate = date('Y-m-d H:i:s');

        /* CEK USER LAIN MELAKUKAN EKSEKUSI DOKUMEN SAMA */
        $paramtmpx = " and nodoktmp='$nodok' and nodok!='$nama'";
        $dtlrow=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();

        if ($dtlrow>0){
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 12,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url('trans/jadwal_new/verifikasi_perubahan'));
        } else {
            $info_final = array(
                'updateby' => $nama,
                'updatedate' => $inputdate,
                'status' => 'E',
            );
            $this->db->where('nodok',$nodok);
            $this->db->update('sc_trx.mutasi_jadwalkerja_pernik',$info_final);
            return redirect()->to(base_url('trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik'));
        }


    }

    function persetujuan_verifikasi_mutasi_jadwalpernik(){
        $nodok = $this->fiky_encryption->unseal($this->uri->getSegment(4));
        $nama = trim($this->session->get('nama'));
        $inputdate = date('Y-m-d H:i:s');

        /* CEK USER LAIN MELAKUKAN EKSEKUSI DOKUMEN SAMA */
        $paramtmpx = " and nodok='$nodok' and status!='A'";
        $dtlrow=$this->m_jadwalnew->q_show_trx_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();

        if ($dtlrow>0){
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 4,
                'nomorakhir1' => $nodok,
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url('trans/jadwal_new/verifikasi_perubahan'));
        } else {
            $info_final = array(
                'approveby' => $nama,
                'approvedate' => $inputdate,
                'status' => 'P',
            );
            $this->db->where('nodok',$nodok);
            $this->db->update('sc_trx.mutasi_jadwalkerja_pernik',$info_final);
            return redirect()->to(base_url('trans/jadwal_new/verifikasi_perubahan'));
        }

    }

    function batal_verifikasi_mutasi_jadwalpernik(){

        $nodok = $this->fiky_encryption->unseal($this->uri->getSegment(4));
        $nama = trim($this->session->get('nama'));
        $inputdate = date('Y-m-d H:i:s');

        /* CEK USER LAIN MELAKUKAN EKSEKUSI DOKUMEN SAMA */
        $paramtmpx = " and nodok='$nodok' and status!='A'";
        $dtlrow=$this->m_jadwalnew->q_show_trx_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();

        if ($dtlrow>0){
            /* error handling */
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 4,
                'nomorakhir1' => $nodok,
                'nomorakhir2' => '',
                'modul' => 'I.T.B.25',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url('trans/jadwal_new/verifikasi_perubahan'));
        } else {
            $info_final = array(
                'cancelby' => $nama,
                'canceldate' => $inputdate,
                'status' => 'C',
            );
            $this->db->where('nodok',$nodok);
            $this->db->update('sc_trx.mutasi_jadwalkerja_pernik',$info_final);
            return redirect()->to(base_url('trans/jadwal_new/verifikasi_perubahan'));
        }
    }

    function detail_verifikasi_mutasi_jadwalpernik(){
        $data['title']='DETAIL JADWAL KERJA REGU PERNIK';

        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.T.B.25'; $versirelease='I.T.B.25/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.T.B.25'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $nik=$this->request->getPost('nik');
        $kdregu=$this->request->getPost('kdregu2');
        $bln=$this->request->getPost('bln');
        $thn=$this->request->getPost('thn');
        $nodok = $this->fiky_encryption->unseal($this->uri->getSegment(4));
        $nama = trim($this->session->get('nama'));
        //$data['list_regu']=$this->m_jadwalnew->q_regu()->getResult();

        $paramtmpx = " and nodok='$nodok'";
        //$dtl_jk=$this->m_jadwalnew->q_show_tmp_mutasi_dtljadwalkerja_nik($paramtmpx)->getResult();

        $dtlrow=$this->m_jadwalnew->q_show_trx_mutasi_dtljadwalkerja_nik($paramtmpx)->getNumRows();
        $data['list_jadwal']=$this->m_jadwalnew->q_showgrid_trx_mutasi($paramtmpx)->getResult();


        $cucu=$this->m_jadwalnew->q_show_trx_mutasi_dtljadwalkerja_nik($paramtmpx)->getRowArray();
        $thn = substr($cucu['periode'],0,4);
        $bln = substr($cucu['periode'],4,2);
        $data['thnx']=$thn;
        $data['blnx']=$bln;
        $data['tahun']=$thn;
        $data['bulan']=$bln;
        $data['list_karyawan']=$this->m_jadwalnew->q_karyawan_ondtljadwalkerja($thn,$bln)->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        $data['dtl']=$this->m_jadwalnew->q_dtlreguopr(trim($cucu['nik']))->getRowArray();
        $data['dtlcucu']=$this->m_jadwalnew->q_show_trx_mutasi_dtljadwalkerja_nik($paramtmpx)->getRowArray();

        if (isset($cucu['nik'])){ $data['nik']=trim($cucu['nik']); } else { $data['nik']=''; };
        if (isset($cucu['kdregu'])){ $data['kdregu']=trim($cucu['kdregu']); } else { $data['kdregu']=''; };
        if (isset($cucu['newtgl1'])){ $data['tgl1']=trim($cucu['newtgl1']); } else { $data['tgl1']=''; };
        if (isset($cucu['newtgl2'])){ $data['tgl2']=trim($cucu['newtgl2']); } else { $data['tgl2']=''; };
        if (isset($cucu['newtgl3'])){ $data['tgl3']=trim($cucu['newtgl3']); } else { $data['tgl3']=''; };
        if (isset($cucu['newtgl4'])){ $data['tgl4']=trim($cucu['newtgl4']); } else { $data['tgl4']=''; };
        if (isset($cucu['newtgl5'])){ $data['tgl5']=trim($cucu['newtgl5']); } else { $data['tgl5']=''; };
        if (isset($cucu['newtgl6'])){ $data['tgl6']=trim($cucu['newtgl6']); } else { $data['tgl6']=''; };
        if (isset($cucu['newtgl7'])){ $data['tgl7']=trim($cucu['newtgl7']); } else { $data['tgl7']=''; };
        if (isset($cucu['newtgl8'])){ $data['tgl8']=trim($cucu['newtgl8']); } else { $data['tgl8']=''; };
        if (isset($cucu['newtgl9'])){ $data['tgl9']=trim($cucu['newtgl9']); } else { $data['tgl9']=''; };
        if (isset($cucu['newtgl10'])){ $data['tgl10']=trim($cucu['newtgl10']); } else { $data['tgl10']=''; };
        if (isset($cucu['newtgl11'])){ $data['tgl11']=trim($cucu['newtgl11']); } else { $data['tgl11']=''; };
        if (isset($cucu['newtgl12'])){ $data['tgl12']=trim($cucu['newtgl12']); } else { $data['tgl12']=''; };
        if (isset($cucu['newtgl13'])){ $data['tgl13']=trim($cucu['newtgl13']); } else { $data['tgl13']=''; };
        if (isset($cucu['newtgl14'])){ $data['tgl14']=trim($cucu['newtgl14']); } else { $data['tgl14']=''; };
        if (isset($cucu['newtgl15'])){ $data['tgl15']=trim($cucu['newtgl15']); } else { $data['tgl15']=''; };
        if (isset($cucu['newtgl16'])){ $data['tgl16']=trim($cucu['newtgl16']); } else { $data['tgl16']=''; };
        if (isset($cucu['newtgl17'])){ $data['tgl17']=trim($cucu['newtgl17']); } else { $data['tgl17']=''; };
        if (isset($cucu['newtgl18'])){ $data['tgl18']=trim($cucu['newtgl18']); } else { $data['tgl18']=''; };
        if (isset($cucu['newtgl19'])){ $data['tgl19']=trim($cucu['newtgl19']); } else { $data['tgl19']=''; };
        if (isset($cucu['newtgl20'])){ $data['tgl20']=trim($cucu['newtgl20']); } else { $data['tgl20']=''; };
        if (isset($cucu['newtgl21'])){ $data['tgl21']=trim($cucu['newtgl21']); } else { $data['tgl21']=''; };
        if (isset($cucu['newtgl22'])){ $data['tgl22']=trim($cucu['newtgl22']); } else { $data['tgl22']=''; };
        if (isset($cucu['newtgl23'])){ $data['tgl23']=trim($cucu['newtgl23']); } else { $data['tgl23']=''; };
        if (isset($cucu['newtgl24'])){ $data['tgl24']=trim($cucu['newtgl24']); } else { $data['tgl24']=''; };
        if (isset($cucu['newtgl25'])){ $data['tgl25']=trim($cucu['newtgl25']); } else { $data['tgl25']=''; };
        if (isset($cucu['newtgl26'])){ $data['tgl26']=trim($cucu['newtgl26']); } else { $data['tgl26']=''; };
        if (isset($cucu['newtgl27'])){ $data['tgl27']=trim($cucu['newtgl27']); } else { $data['tgl27']=''; };
        if (isset($cucu['newtgl28'])){ $data['tgl28']=trim($cucu['newtgl28']); } else { $data['tgl28']=''; };
        if (isset($cucu['newtgl29'])){ $data['tgl29']=trim($cucu['newtgl29']); } else { $data['tgl29']=''; };
        if (isset($cucu['newtgl30'])){ $data['tgl30']=trim($cucu['newtgl30']); } else { $data['tgl30']=''; };
        if (isset($cucu['newtgl31'])){ $data['tgl31']=trim($cucu['newtgl31']); } else { $data['tgl31']=''; };


        return $this->template->render('trans/jadwal/v_detail_verifikasi_mutasi_jadwalpernik',$data);
        /* DEL TRXERROR */
        $this->m_trxerror->q_deltrxerror($paramerror);
    }
    /* END VERIFIKASI PERUBAHAN */


    /* START JADWAL SALAH OTOMATIS PADA SAAT TARIK ABSENSI */
    function jadwal_salah(){
        //echo "test";
        $nama=trim($this->session->get('nama'));
        $data['title']="Verifikasi Jadwal Kerja Salah/ Tidak Terbaca / Koreksi Data Absensi";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.B.1'; $versirelease='I.A.B.1/RELEASE.401'; $releasedate=date('2020-06-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.A.B.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        /* DEL TRXERROR */
        $this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('trans/jadwal/v_jadwal_salah',$data);

    }

    function list_jadwal_salah()
    {

        $list = $this->m_jadwalnew->get_t_jadwalsalah();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            //add html for action
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">
                    <li role="presentation" style="background-color: #a760f3;"><a style="margin:0px; color:#FFFFFF;" role="menuitem" href="javascript:void(0);"  onclick="see_fingerprint(' ."'".trim($lm->id)."'".');"><i class="fa fa-eye"></i> Lihat Presensi Mesin </a></li>
                    <li role="presentation" style="background-color: #00a7d0;"><a style="margin:0px; color:#FFFFFF;" role="menuitem" href="javascript:void(0);"  onclick="update_jadwal('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Ubah Presensi & Jadwal </a></li>
                </ul>
            </div>
             ';
            $row[] = $lm->nik;
            $row[] = $lm->nmlengkap;
            $row[] = $lm->kdregu;
            $row[] = $lm->kdjamkerja.'  '.$lm->jam_kerjax;
            $row[] = $lm->tgl1;
            if (empty($lm->jam_masuk_absen)) {
                $row[] = '<span style="background-color: #db3737;">'.$lm->jam_masuk_absen.'</span>';
            } else {
                $row[] = $lm->jam_masuk_absen;
            }

            $row[] = $lm->jam_pulang_absen;
            $row[] = $lm->editan1;
            //$row[] = date('d-m-Y H:i:s',strtotime(trim($lm->inputdate)));
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_jadwalnew->t_jadwalsalah_count_all(),
            "recordsFiltered" => $this->m_jadwalnew->t_jadwalsalah_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);

    }

    function load_modal_presensi($var)
    {
        $data['test'] = 'NAMA LENGKAP'.$var;
        $dtl = $this->m_jadwalnew->q_transready_id($var)->getRowArray();
        $data['dtl'] = $dtl;
        $param = " and badgenumber='".trim($dtl['nik'])."' and to_char(checktime,'yyyy-mm-dd')='".$dtl['tgl']."'";
        $data['list_checktime'] =$this->m_jadwalnew->q_tmp_checkinout($param)->getResult();
        $paramk = " and nik='".trim($dtl['nik'])."'";
        $data['dtlk'] =$this->m_jadwalnew->q_karyawan_param($paramk)->getRowArray();
        return view('trans/jadwal/v_modal_presensi_finger',$data);
    }

    function load_modal_update_presensi($var)
    {
        $data['test'] = 'NAMA LENGKAP'.$var;
        $dtl = $this->m_jadwalnew->q_transready_id($var)->getRowArray();
        $data['dtl'] = $dtl;
        $param = " and badgenumber='".trim($dtl['nik'])."' and to_char(checktime,'yyyy-mm-dd')='".$dtl['tgl']."'";
        $data['list_checktime'] =$this->m_jadwalnew->q_tmp_checkinout($param)->getResult();
        $paramk = " and nik='".trim($dtl['nik'])."'";
        $data['dtlk'] =$this->m_jadwalnew->q_karyawan_param($paramk)->getRowArray();
        $data['jam_kerja'] =$this->m_jadwalnew->q_jamkerja()->getResult();
        echo view('trans/jadwal/v_modal_update_presensi_finger',$data);
    }

    function saveUpdatePresensi(){
        $nama = trim($this->session->get('nama'));
        $id = trim($this->request->getPost('id'));
        $type = $this->request->getPost('type');
        $kdregu = $this->request->getPost('kdregu');
        $kdjamkerja = $this->request->getPost('kdjamkerja');
        $jam_masuk_absen = $this->request->getPost('jam_masuk_absen');
        $jam_pulang_absen = $this->request->getPost('jam_pulang_absen');
        $builder = $this->db->table('sc_trx.transready');
        $info = array(
            'kdjamkerja' => $kdjamkerja,
            'jam_masuk_absen' => !empty($jam_masuk_absen) ? $jam_masuk_absen : NULL,
            'jam_pulang_absen' => !empty($jam_pulang_absen) ? $jam_pulang_absen : NULL,
        );
        $builder->where('id',$id);
        $builder->update($info);
        return redirect()->to(base_url('trans/jadwal_new/jadwal_salah'));
    }


    /* IMPORT JADWAL KERJA PER NIK */
    function import_jadwal_pernik(){
        $data['title']="IMPORT DATA JADWAL KERJA PER KARYAWAN ";
        $this->db->query("
--insert into sc_mst.regu
--(kdregu,nmregu)
--values
--('GR','GLOBAL REGU');
insert into sc_mst.regu_opr
(kdregu,nik,status,tglefektif)
(select 'GR',nik,'P',tglmasukkerja from sc_mst.t_karyawan 
where coalesce(resign,'NO') != 'YES' and nik not in (select nik from sc_mst.regu_opr));");

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.A.B.2'; $versirelease='I.A.B.2/RELEASE.401'; $releasedate=date('2019-08-21 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['list_regu']=$this->m_jadwalnew->q_regu_notin_map()->getResult();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        /* END CODE UNTUK VERSI */
        $paramerror=" and userid='$nama' and modul='I.T.B.18'";
        $dtlerror=$this->m_jadwalnew->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_jadwalnew->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']===0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode==='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DISIMPAN/DIUBAH $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        $data['list_tmp_jadwalkerja_import']=$this->m_jadwalnew->q_tmp_jadwalkerja_pernik_import(" and inputby='$nama'",$nama)->getResult();
        $data['adaisi']=$this->m_jadwalnew->q_tmp_jadwalkerja_pernik_import(" and inputby='$nama'",$nama)->getRowArray();
        $data['list_jamkerja']=$this->m_jadwalnew->q_jamkerja()->getResult();
        $this->m_jadwalnew->q_deltrxerror($paramerror);
        return $this->template->render('trans/jadwal/v_import_jadwal_pernik',$data);

    }

    function proses_xls_jadwal_nik() {
        $nama = trim($this->session->get('nama'));
        $builder = $this->db->table('sc_tmp.jadwalkerja_nik_import');

        $builder->where('nodok',$nama);
        $builder->delete();
        $path = "./assets/files/jadwalkerja/jadwalnik/data/";


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
                echo '<a href="'.base_url('/trans/jadwal_new/import_jadwal_pernik').'">BACK</a>';
            } else {
                $inputFileName = $path . trim($fileName);
                //  Read your Excel workbook
                try {
                    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                    $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                } catch (Exception $e) {
                    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
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

                if ((trim($JUDUL[0][0]) <> 'Nik')) {

                    $builder_trxerror = $this->db->table('sc_mst.trxerror');
                    $builder_trxerror->where('userid', $nama);
                    $builder_trxerror->where('modul', 'I.T.B.18');
                    $builder_trxerror->delete();
                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 3,
                        'nomorakhir1' => '',
                        'nomorakhir2' => '',
                        'modul' => 'I.T.B.18',
                    );
                    $builder_trxerror->insert('sc_mst.trxerror', $infotrxerror);
                    return redirect()->to(base_url('trans/jadwal_new/import_jadwal_pernik'));
                }

                $no = 1;
                for ($row = 2; $row <= $highestRow; $row++) {                //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                        NULL,
                        TRUE,
                        FALSE);
                    //  Insert row data array into your database of choice here
                    $data = array(
                        'nodok' => $nama,
                        'nik' => strtoupper(trim($rowData[0][0])),
                        'bulan' => substr(strtoupper(trim($rowData[0][2])), 4, 2),
                        'tahun' => substr(strtoupper(trim($rowData[0][2])), 0, 4),
                        'tgl1' => strtoupper(trim($rowData[0][3])),
                        'tgl2' => strtoupper(trim($rowData[0][4])),
                        'tgl3' => strtoupper(trim($rowData[0][5])),
                        'tgl4' => strtoupper(trim($rowData[0][6])),
                        'tgl5' => strtoupper(trim($rowData[0][7])),
                        'tgl6' => strtoupper(trim($rowData[0][8])),
                        'tgl7' => strtoupper(trim($rowData[0][9])),
                        'tgl8' => strtoupper(trim($rowData[0][10])),
                        'tgl9' => strtoupper(trim($rowData[0][11])),
                        'tgl10' => strtoupper(trim($rowData[0][12])),
                        'tgl11' => strtoupper(trim($rowData[0][13])),
                        'tgl12' => strtoupper(trim($rowData[0][14])),
                        'tgl13' => strtoupper(trim($rowData[0][15])),
                        'tgl14' => strtoupper(trim($rowData[0][16])),
                        'tgl15' => strtoupper(trim($rowData[0][17])),
                        'tgl16' => strtoupper(trim($rowData[0][18])),
                        'tgl17' => strtoupper(trim($rowData[0][19])),
                        'tgl18' => strtoupper(trim($rowData[0][20])),
                        'tgl19' => strtoupper(trim($rowData[0][21])),
                        'tgl20' => strtoupper(trim($rowData[0][22])),
                        'tgl21' => strtoupper(trim($rowData[0][23])),
                        'tgl22' => strtoupper(trim($rowData[0][24])),
                        'tgl23' => strtoupper(trim($rowData[0][25])),
                        'tgl24' => strtoupper(trim($rowData[0][26])),
                        'tgl25' => strtoupper(trim($rowData[0][27])),
                        'tgl26' => strtoupper(trim($rowData[0][28])),
                        'tgl27' => strtoupper(trim($rowData[0][29])),
                        'tgl28' => strtoupper(trim($rowData[0][30])),
                        'tgl29' => strtoupper(trim($rowData[0][31])),
                        'tgl30' => strtoupper(trim($rowData[0][32])),
                        'tgl31' => strtoupper(trim($rowData[0][33])),
                        'status' => 'I',
                        'inputby' => $nama,
                        'inputdate' => date("Y-m-d H:i:s")
                    );
                    if (!empty($rowData[0][1])) {
                        $builderx = $this->db->table('sc_tmp.jadwalkerja_nik_import');
                        $builderx->insert($data);
                        $builder_trxerror =  $this->db->table('sc_mst.trxerror');
                        $builder_trxerror->where('userid', $nama);
                        $builder_trxerror->where('modul', 'I.T.B.18');
                        $builder_trxerror->delete();

                        $infotrxerror = array(
                            'userid' => $nama,
                            'errorcode' => 0,
                            'nomorakhir1' => '',
                            'nomorakhir2' => '',
                            'modul' => 'I.T.B.18',
                        );
                        $builder_trxerror->insert($infotrxerror);
                    }
                    $no++;
                }
                return redirect()->to(base_url('trans/jadwal_new/import_jadwal_pernik'));
            }
        }
    }

    function clear_tmp_jadwal_per_nik(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("delete from sc_tmp.jadwalkerja_nik_import where inputby='$nama'");
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.T.B.18');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.18',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('trans/jadwal_new/import_jadwal_pernik'));
    }

    function final_data_jadwal_per_nik(){
        $nama = trim($this->session->get('nama'));
        $this->db->query("update sc_tmp.jadwalkerja_nik_import set status='F' where inputby='$nama'");
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.T.B.18');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.T.B.18',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('trans/jadwal_new/import_jadwal_pernik'));
    }
}
