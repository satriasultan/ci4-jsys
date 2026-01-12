<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Jam_kerja extends BaseController
{
    function index(){
        //echo "test";
        $nama=$this->session->get('nik');
        $data['title']="Master Jam Kerja";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nik'));
        $kodemenu='I.M.F.4'; $versirelease='I.M.F.4/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */




        $paramerror=" and userid='$nama' and modul='I.M.F.4'";
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

        $data['list_jam_kerja']=$this->m_jam_kerja->q_jam_kerja()->getResult();
        //$data['message']="List SMS Masuk";
        $this->m_trxerror->q_deltrxerror($paramerror);
        return $this->template->render('master/jam_kerja/v_jam_kerja',$data);

    }
    function add_jam_kerja(){
        $kdjam_kerja=trim(strtoupper(str_replace(" ","",$this->request->getPost('kdjam_kerja'))));
        $nmjam_kerja=$this->request->getPost('nmjam_kerja');
        $shiftke=$this->request->getPost('shiftke');
        $jam_masuk=$this->request->getPost('jam_masuk');
        $jam_masuk_min=$this->request->getPost('jam_masuk_min');
        $jam_masuk_max=$this->request->getPost('jam_masuk_max');
        $jam_istirahat=$this->request->getPost('jam_istirahat');
        $jam_istirahat_min=$this->request->getPost('jam_istirahat_min');
        $jam_istirahat_max=$this->request->getPost('jam_istirahat_max');
        $jam_istirahatselesai=$this->request->getPost('jam_istirahatselesai');
        $jam_istirahatselesai_min=$this->request->getPost('jam_istirahatselesai_min');
        $jam_istirahatselesai_max=$this->request->getPost('jam_istirahatselesai_max');
        $jam_pulang=$this->request->getPost('jam_pulang');
        $jam_pulang_min=$this->request->getPost('jam_pulang_min');
        $jam_pulang_max=$this->request->getPost('jam_pulang_max');
        $kdharipulang=$this->request->getPost('kdharipulang');
        $keterangan=$this->request->getPost('keterangan');
        //$kdsubdept=$this->request->getPost('kdsubdept');
        //$subdept=explode('|',$this->request->getPost('kdsubdept'));
        //$sub=$subdept[1];
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $chold=$this->request->getPost('chold');
        $nama = trim($this->session->get('nik'));

        //echo $sub;
        $info=array(
            'kdjam_kerja'=>$kdjam_kerja,
            'nmjam_kerja'=>strtoupper($nmjam_kerja),
            'shiftke'=>$shiftke,
            'jam_masuk'=>$jam_masuk,
            'jam_masuk_min'=>$jam_masuk_min,
            'jam_masuk_max'=>$jam_masuk_max,
            'jam_istirahat'=>$jam_istirahat,
            'jam_istirahat_min'=>$jam_istirahat_min,
            'jam_istirahat_max'=>$jam_istirahat_max,
            'jam_istirahatselesai'=>$jam_istirahatselesai,
            'jam_istirahatselesai_min'=>$jam_istirahatselesai_min,
            'jam_istirahatselesai_max'=>$jam_istirahatselesai_max,
            'jam_pulang'=>$jam_pulang,
            'jam_pulang_min'=>$jam_pulang_min,
            'jam_pulang_max'=>$jam_pulang_max,
            'keterangan'=>strtoupper($keterangan),
            'kdharipulang'=>strtoupper($kdharipulang),
            'input_date'=>$tgl_input,
            'input_by'=>strtoupper($inputby),
            'chold'=>strtoupper($chold),
        );
        //echo $shiftke;

        //$this->db->where('custcode',$kode);
        $cek=$this->m_jam_kerja->q_cekjam_kerja($kdjam_kerja)->getNumRows();
        if ($cek>0){
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.M.F.4');
            $this->db->delete('sc_mst.trxerror');

            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.M.F.4',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);

            return redirect()->to(base_url('master/jam_kerja'));
        } else {
            $this->db->insert('sc_mst.jam_kerja',$info);
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.M.F.4');
            $this->db->delete('sc_mst.trxerror');

            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => '',
                'nomorakhir2' => '',
                'modul' => 'I.M.F.4',
            );
            $this->db->insert('sc_mst.trxerror',$infotrxerror);
            return redirect()->to(base_url('master/jam_kerja'));
        }
        //echo $inputby;
    }

    function edit_jam_kerja(){
        $kdjam_kerja=trim(strtoupper(str_replace(" ","",$this->request->getPost('kdjam_kerja'))));
        $nmjam_kerja=$this->request->getPost('nmjam_kerja');
        $shiftke=$this->request->getPost('shiftke');
        $jam_masuk=$this->request->getPost('jam_masuk');
        $jam_masuk_min=$this->request->getPost('jam_masuk_min');
        $jam_masuk_max=$this->request->getPost('jam_masuk_max');
        $jam_istirahat=$this->request->getPost('jam_istirahat');
        $jam_istirahat_min=$this->request->getPost('jam_istirahat_min');
        $jam_istirahat_max=$this->request->getPost('jam_istirahat_max');
        $jam_istirahatselesai=$this->request->getPost('jam_istirahatselesai');
        $jam_istirahatselesai_min=$this->request->getPost('jam_istirahatselesai_min');
        $jam_istirahatselesai_max=$this->request->getPost('jam_istirahatselesai_max');
        $jam_pulang=$this->request->getPost('jam_pulang');
        $jam_pulang_min=$this->request->getPost('jam_pulang_min');
        $jam_pulang_max=$this->request->getPost('jam_pulang_max');
        $keterangan=$this->request->getPost('keterangan');
        $kdharipulang=$this->request->getPost('kdharipulang');
        //$kdsubdept=$this->request->getPost('kdsubdept');
        //$subdept=explode('|',$this->request->getPost('kdsubdept'));
        //$sub=$subdept[1];
        $tgl_input=$this->request->getPost('tgl');
        $inputby=$this->request->getPost('inputby');
        $chold=$this->request->getPost('chold');
        $nama = trim($this->session->get('nik'));

        $builder = $this->db->table('sc_mst.jam_kerja');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        //echo $sub;
        $info=array(
            'nmjam_kerja'=>strtoupper($nmjam_kerja),
            'shiftke'=>$shiftke,
            'jam_masuk'=>$jam_masuk,
            'jam_masuk_min'=>$jam_masuk_min,
            'jam_masuk_max'=>$jam_masuk_max,
            'jam_istirahat'=>$jam_istirahat,
            'jam_istirahat_min'=>$jam_istirahat_min,
            'jam_istirahat_max'=>$jam_istirahat_max,
            'jam_istirahatselesai'=>$jam_istirahatselesai,
            'jam_istirahatselesai_min'=>$jam_istirahatselesai_min,
            'jam_istirahatselesai_max'=>$jam_istirahatselesai_max,
            'jam_pulang'=>$jam_pulang,
            'jam_pulang_min'=>$jam_pulang_min,
            'jam_pulang_max'=>$jam_pulang_max,
            'kdharipulang'=>strtoupper($kdharipulang),
            'keterangan'=>strtoupper($keterangan),
            'update_date'=>$tgl_input,
            'update_by'=>strtoupper($inputby),
            'chold'=>strtoupper($chold),
        );


        $builder->where('kdjam_kerja',$kdjam_kerja);
        $builder->update($info);

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.F.4');
        $builder_trxerror->delete();

        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.M.F.4',
        );
        $builder_trxerror->insert('sc_mst.trxerror',$infotrxerror);
        return redirect()->to(base_url('master/jam_kerja'));

    }

    function hps_jam_kerja($kdjam_kerja){
        $nama = trim($this->session->get('nik'));
        $builder = $this->db->table('sc_mst.jam_kerja');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');


        $builder->where('kdjam_kerja',$kdjam_kerja);
        $builder->delete();

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.M.F.4');
        $builder_trxerror->delete();

        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.M.F.4',
        );
        $builder_trxerror->insert($infotrxerror);

        return redirect()->to(base_url('master/jam_kerja/index/del_succes'));
    }



}
