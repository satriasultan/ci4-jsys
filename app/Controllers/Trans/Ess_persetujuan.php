<?php

namespace App\Controllers\Trans;

use App\Controllers\BaseController;

class Ess_persetujuan extends BaseController
{
    function index(){
        $data['title']="Persetujuan Approvals";

        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.E.A.7'; $versirelease='I.E.A.7/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.E.A.7'";
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


        $data['nama']=$nama;
        /* END APPROVE ATASAN */

        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu='I.E.A.7';
        $role = trim($this->session->get('roleid'));
        $niksession=trim($this->session->get('nik'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $paramfilter = " and ((trim(nikatasan1)='$niksession' and asstatus='A') or (trim(nikatasan2)='$niksession'  and asstatus='A1') or (trim(nikatasan3)='$niksession' and asstatus='A2'))";
        $data['list_approval'] = $this->m_ess_persetujuan->q_approval_system_karyawan(" and asstatus not in ('P','C','D') ".$paramfilter)->getResult();
        $data['count_approval'] = $this->m_ess_persetujuan->q_approval_system_karyawan(" and asstatus not in ('P','C','D') ".$paramfilter)->getNumRows();
        $role_access_filter = $data['dtl_akses']['a_filter'];
        $dtlkary = $this->m_global->q_lv_m_karyawan(" and nik='$niksession'")->getRowArray();



        $data['dtlkary'] = $dtlkary;
        $data['niksession'] = $niksession;
        $paramerror=" and userid='$nama'";
        $dtlerror=$this->m_trxerror->q_deltrxerror($paramerror);;
        return $this->template->render('trans/ess_persetujuan/v_ess_persetujuan',$data);

    }

    function proses(){
        $nama = trim($this->session->get('nama'));
        $nik = trim($this->session->get('nik'));
        $lb = $this->request->getPost('centang');
        $id = $this->request->getPost('id');
        $bsubmit = $this->request->getPost('bsubmit');
        $builder = $this->db->table('sc_trx.approvals_system');
        $builder_trxerror = $this->db->table('sc_mst.trxerror');

        if(empty($lb)){
            return redirect()->to(base_url("trans/ess_persetujuan"));
        } else {
            foreach($lb as $index => $temp)
            {
                $docno=trim($lb[$index]);
                if ($bsubmit==='setujui') {
                    //$info[$index]['docno']=$docno;
                    $read = $this->m_ess_persetujuan->q_approval_system_karyawan(" and docno='$docno'")->getRowArray();
                    //$readNik = $this->m_ess_cuti->list_karyawan_param(" and nik='$nik'")->getRowArray();
                    //ATASAN 1 , ATASAN 2 & ATASAN 3

                    if (trim($read['nikatasan1']) !== '' and trim($read['nikatasan2']) !== '' and trim($read['nikatasan3']) !== '') {
                        if (trim($read['asstatus']) === 'A' and trim($read['nikatasan1']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'A1',
                            );
                        } else if (trim($read['asstatus']) === 'A1' and trim($read['nikatasan2']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'A2',
                            );
                        } else if (trim($read['asstatus']) === 'A2' and trim($read['nikatasan3']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'P',
                            );
                        }
                    } else if (trim($read['nikatasan1']) !== '' and trim($read['nikatasan2']) !== '' and trim($read['nikatasan3']) === '') {
                        if (trim($read['asstatus']) === 'A' and trim($read['nikatasan1']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'A1',
                            );
                        } else if (trim($read['asstatus']) === 'A1' and trim($read['nikatasan2']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'P',
                            );
                        } else if (trim($read['asstatus']) === 'A2' and trim($read['nikatasan3']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'P',
                            );
                        }
                    } else if (trim($read['nikatasan1']) !== '' and trim($read['nikatasan2']) === '' and trim($read['nikatasan3']) === '') {
                        if (trim($read['asstatus']) === 'A' and trim($read['nikatasan1']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'P',
                            );
                        } else if (trim($read['asstatus']) === 'A1' and trim($read['nikatasan2']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'P',
                            );
                        } else if (trim($read['asstatus']) === 'A2' and trim($read['nikatasan3']) === $nik) {
                            $info = array(
                                'approvaldate' => date('Y-m-d H:i:s'),
                                'approvalby' => trim($this->session->get('nama')),
                                'asstatus' => 'P',
                            );
                        }
                    }

                    //UPDATE DATA BATCH
                    $builder->where('docno',$docno);
                    if($builder->update($info)) {
                        $builder_trxerror->where('userid',$nama);
                        $builder_trxerror->where('modul','ABSCUT');
                        $builder_trxerror->delete();
                        $infotrxerror = array (
                            'userid' => $nama,
                            'errorcode' => 11,
                            'nomorakhir1' => $docno,
                            'nomorakhir2' => '',
                            'modul' => 'ABSCUT',
                        );
                        $builder->insert('sc_mst.trxerror',$infotrxerror);
                    }



                } else if ($bsubmit==='batalkan'){
                    $info = array(
                        'canceldate' => date('Y-m-d H:i:s'),
                        'cancelby' => trim($this->session->get('nama')),
                        'asstatus' => 'C',
                    );
                    //UPDATE DATA
                    $builder->where('docno',$docno);
                    if($builder->update($info)) {
                        $builder_trxerror->where('userid',$nama);
                        $builder_trxerror->where('modul','ABSCUT');
                        $builder_trxerror->delete();
                        $infotrxerror = array (
                            'userid' => $nama,
                            'errorcode' => 0,
                            'nomorakhir1' => $docno,
                            'nomorakhir2' => '',
                            'modul' => 'ABSCUT',
                        );
                        $builder_trxerror->insert($infotrxerror);
                    }

                }




            }


        }

        return redirect()->to(base_url("trans/ess_persetujuan"));

        /*$insert = $this->m_role->add_kdmnu_tmp($info);
        $enc_id = $this->fiky_encryption->enkript($id)*/;
        //return redirect()->to(base_url("master/role/access_permission/?id=".$enc_id);
    }


}
