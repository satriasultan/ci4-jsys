<?php

namespace App\Controllers\Stock;

use App\Controllers\BaseController;

class Bbk extends BaseController
{
    function index(){
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.1'; $versirelease='I.D.E.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.E.1'";
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

        /* Item Transfer Master Check */
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_bbk->q_tmp_item_bbk_mst($param);

        if ($dtl->getNumRows()>0) {
            $title = "PERINGATAN !!!";
            $urlclear = base_url('stock/bbk/clearEntrybbk');
            $urlnext = base_url('stock/bbk/input_bbk');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbk/v_bbk',$data);
    }

    function list_bbk(){
        $list = $this->m_bbk->get_t_trx_bbk_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle btn-sm" style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                        <!--a class="dropdown-item" href='."'". base_url('stock/bbk/cancel_stock_bbk').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Batalkan Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Cancel </a-->
                        <a class="dropdown-item" href='."'". base_url('stock/bbk/detail_stock_bbk').'/'.'?docno='.bin2hex(trim($lm->docno))."'".' onclick="return confirm('."'".'Detail Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-bars"></i> Detail </a>
                    </div>
            </div>
             ';

            /*
            if (trim($lm->status)==='P') {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href='."'". base_url('stock/bbk/update_stock_bbk').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Update Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Update </a>
                      <a class="dropdown-item" href='."'". base_url('stock/bbk/cancel_stock_bbk').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Batalkan Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-gear"></i> Cancel </a>
                      <a class="dropdown-item" href='."'". base_url('stock/bbk/print_stock_bbk').'/'.'?docno='.trim($lm->docno)."'".' onclick="return confirm('."'".'Cetak Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-print"></i> Print </a>
                      <div class="dropdown-divider"></div>
                      
                      <a class="dropdown-item" href='."'". base_url('stock/bbk/detail_stock_bbk').'/'.'?docno='.bin2hex(trim($lm->docno))."'".' onclick="return confirm('."'".'Detail Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-bars"></i> Detail </a>
                    </div>
            </div>
             ';
            } else {
                $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href='."'". base_url('stock/bbk/detail_stock_bbk').'/'.'?docno='.bin2hex(trim($lm->docno))."'".' onclick="return confirm('."'".'Detail Data Ini : '.trim($lm->docno)."'".')"><i class="fa fa-bars"></i> Detail </a>
                    </div>
            </div>
             ';
            }
            */

            $row[] = $lm->docno;
            $row[] = $lm->docref;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->nmgroup;
            $row[] = $lm->onhandtrans;
            $row[] = $lm->unit;
            $row[] = $lm->lasttrxdate1;
            $row[] = $lm->nmlocation;
            $row[] = $lm->nmarea;
            $row[] = $lm->docjns;
            $row[] = $lm->idsection;
            $row[] = $lm->nmcostcenter;
            if ( in_array(trim($lm->status),array('C','O','D'))) {
                $row[] = '<span class="badge bg-danger" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            } else {
                $row[] = '<span class="badge bg-success" style="font-size: 100%">'.$lm->nmstatus.'</span>';
            }
            $row[] = $lm->inputby;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_bbk->t_trx_bbk_dtl_view_count_all(),
            "recordsFiltered" => $this->m_bbk->t_trx_bbk_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function input_bbk()
    {
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.1'; $versirelease='I.D.E.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.E.1'";
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
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_bbk->q_tmp_item_bbk_mst($param)->getNumRows();

        if (!empty($nama) and $dtl<=0) {
            $info = array (
                'docno' => $nama,
                'doctype' => 'OUT',
                'idlocation' => $loccode,
                'docdate' => date('Y-m-d'),
                'status' => 'I',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.item_bbk_mst');
            $builder->insert($info);
        } else if (!empty($nama) and $dtl>0) {
            //lanjutkan
        } else {
            return redirect()->to(base_url('stock/bbk'));
        }
        $data['typeform'] = 'INPUT';
        $data['dtldata'] = $this->m_bbk->q_tmp_item_bbk_mst($param)->getRowArray();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbk/v_input_stock_bbk',$data);
    }
    function update_stock_bbk()
    {
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $param = " and docno='$docno' and coalesce(holdby,'')!='$nama' and coalesce(holdrow,'')='YES'";
        $dtl = $this->m_bbk->q_trx_item_bbk_mst($param)->getNumRows();

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder = $this->db->table('sc_trx.item_bbk_mst');
        if ($dtl>0){
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.D.E.1');
            $this->db->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'I.D.E.1',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('stock/bbk'));
        } else {
            $info = array(

                'docnotmp' => $docno,
                'holdby' => $nama,
                'holdrow' => 'YES',
                'status' => 'E',
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),
            );
            $builder->where('docno',$docno);
            if ($builder->update( $info)) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk/input_bbk'));
            }
        }
    }
    function cancel_stock_bbk()
    {
        $docno = trim($this->request->getGet('docno'));
        $nama = trim($this->session->get('nama'));

        $param = " and docno='$docno' and coalesce(holdby,'')!='$nama' and coalesce(holdrow,'')='YES'";
        $dtl = $this->m_bbk->q_trx_item_bbk_mst($param)->getNumRows();

        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder = $this->db->table('sc_trx.item_bbk_mst');
        if ($dtl>0){
            $this->db->where('userid',$nama);
            $this->db->where('modul','I.D.E.1');
            $this->db->delete('sc_mst.trxerror');
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 1,
                'nomorakhir1' => $docno,
                'nomorakhir2' => '',
                'modul' => 'I.D.E.1',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('stock/bbk'));
        } else {
            $info = array(

                'docnotmp' => '',
                'holdby' => '',
                'holdrow' => '',
                'status' => 'C',
                'updateby' => $nama,
                'updatedate' => date('Y-m-d H:i:s'),
            );
            $builder->where('docno',$docno);
            if ($builder->update( $info)) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk'));
            }
        }
    }
    function detail_stock_bbk()
    {
        $docno = trim(hex2bin($this->request->getGet('docno')));
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.1'; $versirelease='I.D.E.1/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.E.1'";
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
        $param = " and coalesce(docno,'')='$docno'";
        $data['dtlmst'] = $this->m_bbk->q_trx_item_bbk_mst($param)->getRowArray();
        $data['listdtl'] = $this->m_bbk->q_trx_item_bbk_dtl($param)->getResult();


        $tempdir = "./assets/files/imgbarcode/";
        if (!is_dir($tempdir)) {
            mkdir($tempdir, 0777, TRUE);
        }
        //ambil logo
        $logopath=base_url()."/assets/img/logo-depan/jts-icon.png";

        //isi qrcode jika di scan
        //$codeContents = base_url()."/master/document/verified?var=".md5(md5($docno));
        $codeContents = $docno; //barcodenya

        //simpan file qrcode
        $this->fiky_qrcode->save_image($codeContents, $tempdir.$docno.'.png', QR_ECLEVEL_H, 10,4);
        // ambil file qrcode
        $QR =  $this->fiky_qrcode->get_qrcode($tempdir.$docno.'.png');
        // memulai menggambar logo dalam file qrcode
        $logo = $this->fiky_qrcode->createfromstring($logopath);
        $this->fiky_qrcode->proses($logo,$QR,$tempdir.$docno.'.png');

        /* MANUAL URL SOALYA TIDAK BISA PAKAI HTTPS */
        //$this->renderpdf($docno);
       //// file_get_contents("http://192.168.100.2/ims/api/validatorpush/renderpdf_pbl"."/".$docno);
       //// $data['urlx'] = 'http://192.168.100.2/ims/assets/files/pbl_print/'.$docno.'.pdf';

        $data['urlx'] = '';
        //file_get_contents("http://10.0.150.198/ci4-inventory/api/validatorpush/renderpdf_pbl"."/".$docno);
        //$data['urlx'] = 'http://10.0.150.198/ci4-inventory/assets/files/pbl_print/'.$docno.'.pdf';

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbk/v_detail_stock_bbk',$data);
    }
    function list_tmp_bbk_dtl(){
        $list = $this->m_bbk->get_t_tmp_bbk_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle btn-sm" style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="edit_bbk('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_bbk('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';

            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->onhand;
            if ($lm->onhandtrans > 0) {
                $row[] = '<input style="margin:0px; background-color:#ceedcf;" type="text" id="#" name="#" value='.$lm->onhandtrans.' disabled>';
            } else {
                $row[] = '<input type="text" id="#" name="#" value='.$lm->onhandtrans.' disabled>';
            }
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->description;
            $row[] = $lm->idbarang;
            //$row[] = $lm->nmstatus;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_bbk->t_tmp_bbk_dtl_view_count_all(),
            "recordsFiltered" => $this->m_bbk->t_tmp_bbk_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_bbk_mst(){
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$nama'";
        $data = $this->m_bbk->q_tmp_item_bbk_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_bbk_dtl($id){
        $nama=trim($this->session->get('nama'));
        $data = $this->m_bbk->q_tmp_item_bbk_dtl(" and docno='$nama' and id='$id'")->getRow();
        echo json_encode($data);
    }

    function showing_stkgdw($id){
        $data = $this->m_bbk->q_stkgdw(" and id='$id'")->getRow();
        echo json_encode($data);
    }


    function verifikasi_mst_stock_bbk(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $idlocation = $this->request->getPost('idlocation');
        $docdate = $this->request->getPost('docdate');
        $docref = strtoupper(trim($this->request->getPost('docref')));
        $idsection = strtoupper(trim($this->request->getPost('idsection')));
        $descsection = strtoupper(trim($this->request->getPost('descsection')));
        $description = strtoupper(trim($this->request->getPost('description')));
        $docjns = strtoupper(trim($this->request->getPost('docjns')));
        $copydesc = $this->request->getPost('copydesc');

        $dtl = $this->m_location->q_mlocation(" and idlocation='$idlocation'")->getRowArray();

        if (!empty($loccode)) {
            $info = array (
                'docref' => $docref,
                'idlocation' => $loccode,
                'idsection' => $idsection,
                'descsection' => $descsection,
                'docjns' => $docjns,
                'description' => $description,
                'docdate' => date('Y-m-d',strtotime($docdate)),
                'status' => 'E',
                'copydesc' => $copydesc ? 'TRUE' : 'FALSE',
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.item_bbk_mst');
            $builder->where('docno',$nama);
            $builder->update($info);
        }
        return redirect()->to(base_url('stock/bbk/input_bbk'));

    }
    function clearEntrybbk()
    {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_bbk->q_tmp_item_bbk_mst($param);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_bbk_mst');
        $builder_dtl = $this->db->table('sc_tmp.item_bbk_dtl');
        $buildertx = $this->db->table('sc_trx.item_bbk_mst');
        $builderdtltx = $this->db->table('sc_trx.item_bbk_dtl');
        //PROSES INPUT
        if ($status==='I') {
            $builder->where('docno', $nama);
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno', $nama);
                $builder_dtl->delete();

                $builder->where('docno', $nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else if ($status==='E')
        {   $builder->where('docno',$nama);
            if ($builder_dtl->update(array('status' => 'C'))) {
                $builder_dtl->where('docno',$nama);
                $builder_dtl->delete();

                $builder->where('docno',$nama);
                $builder->delete();

                $buildertx->where('docno',trim($dtl->getRowArray()['docnotmp']));
                $buildertx->update(array(
                    'status' => 'P',
                    'holdrow' => '',
                    'holdby' => '',
                ));
                $builderdtltx->where('docno',trim($dtl->getRowArray()['docnotmp']));
                $builderdtltx->update(array(
                    'status' => 'P',
                    'holdrow' => '',
                    'holdby' => '',
                ));
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno',$nama);
                $builder_dtl->delete();

                $builder->where('docno',$nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        }
    }

    function saveDetailbbk(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $id = trim($dataprocess->id);
            $type = trim($dataprocess->type);
            $idlocation = trim($dataprocess->idlocation);
            $idarea = trim($dataprocess->idarea);
            $idbarang = trim($dataprocess->idbarang);
            //$qtyonhand = trim($dataprocess->qtyonhand);
            $qtyonhandtrans = trim($dataprocess->qtyonhandtrans);
            $batch = trim($dataprocess->batch);
            $batch_parameter = str_replace("'","''",trim($dataprocess->batch));
            $unit = trim($dataprocess->unit);
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            $builder = $this->db->table('sc_tmp.item_bbk_dtl');
            // CHECK INPUT TYPE
            if ($type==='INPUT') {
                $paramlocation = " and idlocation='$loccode'";
                $parammst = " and docno='$nama'";
                $param = " and docno='$nama' and idbarang='$idbarang'";
                $paramitem = " and idbarang='$idbarang'";

                $dloc = $this->m_location->q_mlocation($paramlocation)->getRowArray();
                $dtlmst = $this->m_bbk->q_tmp_item_bbk_mst($parammst)->getRowArray();
                $check = $this->m_bbk->q_tmp_item_bbk_dtl($param)->getNumRows();

                $parambalance = " and trim(idlocation)='".trim($dloc['idlocation'])."' and trim(idarea)='".trim($dloc['idarea_default'])."' and trim(batch)='".$batch_parameter."' and trim(idbarang)='$idbarang'";
                $onhandcheck = $this->m_balance->q_stkgdw($parambalance)->getRowArray();

                $parambalance_realstock = " and trim(idlocation)='".$idlocation."' and trim(idarea)='".$idarea."' and trim(batch)='".$batch_parameter."' and trim(idbarang)='$idbarang'";
                $onhandcheck_realstock = $this->m_balance->q_stkgdw($parambalance_realstock)->getRowArray();
                //check data ganda
                if ($idbarang === '' or ((int)$qtyonhandtrans > (int)$onhandcheck_realstock['sisa'])) {
                    $getResult = array('status' => false, 'messages' => 'Oops ready in use, use edit or qty not acceptable');
                    echo json_encode($getResult);
                } else {
                    $info = array(
                        //'parentunit' => empty($parentunit) ? 0 : $parentunit ,
                        'id' => 0,
                        'docno' => $nama,
                        'doctype' => 'OUT',
                        'docref' => $dtlmst['docref'] ?? null,
                        'idlocation' => $idlocation,
                        'idarea' => $idarea,
                        'idbarang' => $idbarang,
                        'onhand' => $onhandcheck['sisa'] ?? 0,
                        'onhandtrans' => $qtyonhandtrans,
                        'unit' => $unit,
                        'subunit' => '',
                        'lasttrxdate' => date('Y-m-d H:i:s',strtotime($dtlmst['docdate'])),
                        'description' => $description,
                        'status' => 'I',
                        'inputby' => $inputby,
                        'inputdate' => $inputdate,
                        'idsection' => $dtlmst['idsection'] ?? null,
                        'descsection' => $dtlmst['descsection'] ?? null,
                        'docjns' => $dtlmst['docjns'] ?? null,
                        'batch' => $batch,
                    );
                    if ($builder->insert($info)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: '.$idbarang);
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='UPDATE') {
                $parambalance_realstock = " and idlocation='".$idlocation."' and idarea='".$idarea."' and idbarang='$idbarang'  and batch='$batch_parameter'";
                $onhandcheck_realstock = $this->m_balance->q_stkgdw($parambalance_realstock)->getRowArray();

                $paramdtl = " and docno='$nama' and idlocation='".$idlocation."' and idarea='".$idarea."' and idbarang='$idbarang'  and batch='$batch_parameter'";
                $dtltmp = $this->m_bbk->q_tmp_item_bbk_dtl($paramdtl)->getRowArray();

                if ($qtyonhandtrans<=0 or $idarea === '' or ((((int)$onhandcheck_realstock['sisa'] + (int)$dtltmp['onhandtrans']) - (int)$qtyonhandtrans) < 0) ) {
                    $getResult = array('status' => false, 'messages' => 'Oops ready in use, use edit or qty not acceptable'. (((int)$onhandcheck_realstock['sisa'] + (int)$dtltmp['onhandtrans']) - (int)$qtyonhandtrans));
                    echo json_encode($getResult);
                } else {
                    $infoupdate = array(
                        'onhandtrans' => $qtyonhandtrans,
                        'description' => $description,
                        'status' => 'E',
                    );
                    $builder->where('docno',$nama);
                    $builder->where('id',$id);
                    if ($builder->update($infoupdate)) {
                        $getResult = array('status' => true, 'messages' => 'Data processing'.' Code: ');
                        echo json_encode($getResult);
                    } else {
                        $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                        echo json_encode($getResult);
                    }
                }

            } else if ($type==='DELETE') {
                $iupdate = array(
                    'status' => 'C',
                );
                $builder->where('docno',$nama);
                $builder->where('id',$id);
                if ($builder->update($iupdate)) {
                    $builder->where('docno',$nama);
                    $builder->where('id',$id);
                    $builder->delete();

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

    function finalEntrybbk(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $parammst = " and coalesce(docno,'')='$nama'";
        $paramdtl = " and coalesce(docno,'')='$nama' and onhandtrans <= 0";
        $paramdtl2 = " and coalesce(docno,'')='$nama' ";
        $dtl = $this->m_bbk->q_tmp_item_bbk_mst($parammst);
        $cek = $this->m_bbk->q_tmp_item_bbk_dtl($paramdtl);
        $cek2 = $this->m_bbk->q_tmp_item_bbk_dtl($paramdtl2);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_bbk_mst');
        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.D.E.1');
        $builder_trxerror->delete();

        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.D.E.1',
            );
            $builder_trxerror->insert($infotrxerror);
            return redirect()->to(base_url('/stock/bbk/input_bbk'));
        } else {
            $info = array(
                'status' => 'F'
            );
            $builder->where('docno',$nama);
            $builder->update($info);


            $paramerror=" and userid='$nama' and modul='I.D.E.1'";
            $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
            $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

            $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

            return redirect()->to(base_url('/stock/bbk/detail_stock_bbk?docno='.$docno));

            /* return redirect()->to(base_url('/stock/bbk')); */

        }

    }

    function print_stock_bbk(){
        $nama = trim($this->session->get('nama'));
        if ($this->request->getGet('docno')){
            $enc_docno = $this->fiky_encryption->sealed($this->request->getGet('docno'));
        } else {
            $enc_docno = $this->fiky_encryption->sealed($this->request->getPost('docno'));
        }

        $docno=trim($this->fiky_encryption->unseal($enc_docno));

        $title = " Bukti Barang Keluar ";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_bbk->q_trx_item_bbk_mst($param);


        $datajson =  base_url("stock/bbk/api_print_stock_bbk/?enc_docno=$enc_docno") ;
        $datamrt =  base_url("assets/mrt/form_bbk.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_print_stock_bbk(){
        $nama = trim($this->session->get('nama'));
        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docno')));
        //$docno=trim($this->request->getGet('enc_docno'));


        $param=" and docno='$docno'";
        $databranch = $this->m_global->q_master_branch();
        $datamst = $this->m_bbk->q_trx_item_bbk_mst($param);
        $datadtl = $this->m_bbk->q_trx_item_bbk_dtl($param);

        $nfo = array(
            'status' => 'V'
        );
        $builderx = $this->db->table("sc_trx.item_bbk_mst");
        $builderx->where('docno',$docno);
        $builderx->update($nfo);
        header("Content-Type: text/json");
        return json_encode(
            array(
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }

    function list_balance_bbk(){
        $list = $this->m_balance->get_t_balance_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = '
                <a class="btn btn-md btn-primary" href="#"  title="Add Item" onclick="add_bbk('."'".trim($lm->id)."'".')"><i class="fa fa-plus"></i> </a>
             ';
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;

            $row[] = $lm->sisa;
            $row[] = $lm->unit;
            $row[] = $lm->nmarea;
            $row[] = $lm->nmgroup;
            $row[] = $lm->idbarang;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_balance->t_balance_view_count_all(),
            "recordsFiltered" => $this->m_balance->t_balance_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function reprint(){
        $docno = trim(hex2bin($this->request->getGet('docno')));
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.5'; $versirelease='I.D.E.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.E.1'";
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

        } else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        $param = " and coalesce(docno,'')='$docno'";
        $data['dtlmst'] = $this->m_bbk->q_trx_item_bbk_mst($param)->getRowArray();
        $data['listdtl'] = $this->m_bbk->q_trx_item_bbk_dtl($param)->getResult();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbk/v_reprint_bbk',$data);
    }

    // MENU VOID
    function void(){
        $docno = trim(hex2bin($this->request->getGet('docno')));
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.6'; $versirelease='I.D.E.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='VOID_ITEM'";
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
        /* Item Transfer Master Check */
        $param = " and coalesce(docno,'')='$nama' and docjns='VOID_BBK'";
        $dtl = $this->m_bbk->q_tmp_item_void_bbk_mst($param);

        if ($dtl->getNumRows()>0) {
            $title = "PERINGATAN !!!";
            $urlclear = base_url('stock/bbk/clearEntryVoidbbk');
            $urlnext = base_url('stock/bbk/void_stock_bbk');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }


        //auto insert unit
        $param = " and coalesce(docno,'')='$docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbk/v_void_bbk',$data);
    }

    function void_stock_bbk()
    {
        $docno = trim($this->request->getPost('docno'));
        $loccode=trim($this->session->get('loccode'));
        $paramx = " and idlocation='$loccode'";
        $dtllocation=$this->m_location->q_mlocation($paramx)->getRowArray();
        $data['title']=ucfirst("SEARCH STOCK: ".$dtllocation['idlocation'].' - '.$dtllocation['nmlocation']);
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.6'; $versirelease='I.D.E.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='VOID_ITEM'";
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
        $param = " and coalesce(docno,'')='$nama'";
        $param_bbk = " and coalesce(docno,'')='$docno' and trim(status) not in ('C','D','V','E','O')";
        $dtl = $this->m_bbk->q_tmp_item_void_bbk_mst($param)->getNumRows();
        $dtlbbk = $this->m_bbk->q_trx_item_bbk_mst($param_bbk)->getNumRows();

        if (!empty($nama) and $dtl<=0 and $dtlbbk>0) {
            $this->db->query("
         INSERT INTO sc_tmp.item_void_mst(
docno, docref, doctype, idlocation, docdate, description, status, inputby, inputdate, cancelby, canceldate, docnotmp, holdrow, holdby, updatedate, updateby, idsection, descsection, docjns) 
(select '$nama', docno, doctype, idlocation, docdate, description, 'I' as status, '$nama', to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp, cancelby, canceldate, docnotmp, holdrow, holdby, updatedate, updateby, '' as idsection, '' as descsection, 'VOID_BBK' as docjns
from sc_trx.item_bbk_mst where docno='$docno');

INSERT INTO sc_tmp.item_void_dtl(
docno, docref, doctype, idlocation, idarea, idbarang, onhand, onhandtrans, unit, lasttrxdate, subunit, subunittrans, description, status, inputby, inputdate, id, docnotmp, holdrow, holdby, updatedate, updateby, idsection, descsection, docjns,uniqueidobd,idpodtl,idrev,batch)
(select '$nama', docno, doctype, idlocation, idarea, idbarang, onhand, onhandtrans, unit, lasttrxdate, subunit, subunittrans, description, 'I', '$nama', to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp, id, docnotmp, holdrow, holdby, updatedate, updateby, '' as idsection, '' as descsection,'VOID_BBK' as docjns,
coalesce(uniqueidobd,''),coalesce(idpodtl,0),coalesce(idrev,0),coalesce(batch,'')
from sc_trx.item_bbk_dtl where docno='$docno');
         ");

        } else if (!empty($nama) and $dtl>0 ) {
            //lanjutkan
        } else {
            //INSERT TRX ERROR
            $builder_trxerror = $this->db->table('sc_mst.trxerror');
            $builder_trxerror->where('userid', $nama);
            $builder_trxerror->where('modul', 'VOID_ITEM');
            $builder_trxerror->delete();

            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 4,
                'nomorakhir1' => $docno,
                'nomorakhir2' => 0,
                'modul' => 'VOID_ITEM',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('stock/bbk/void'));
        }
        $data['typeform'] = 'INPUT';
        $data['dtldata'] = $this->m_bbk->q_tmp_item_void_bbk_mst($param)->getRowArray();
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('stock/bbk/v_input_void_stock_bbk',$data);
    }

    function loadItemVoidPbl(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            $docno = trim($dataprocess->docnovoid);
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');

            $cek_bbk = $this->m_bbk->q_trx_item_bbk_dtl(" and doc_idrev='$docno' and status='P'")->getNumRows();
            $countvoid = $this->m_bbk->q_tmp_item_void_bbk_mst(" and docno='$nama'")->getNumRows();
            $count = $this->m_bbk->q_trx_item_bbk_dtl(" and docno='$docno' and status='P'")->getNumRows();

            if ($count > 0 and $countvoid<=0 and $cek_bbk<=0) {
                // CHECK INPUT TYPE
                $this->db->query("
         INSERT INTO sc_tmp.item_void_mst(
docno, docref, doctype, idlocation, docdate, description, status, inputby, inputdate, cancelby, canceldate, docnotmp, holdrow, holdby, updatedate, updateby, idsection, descsection, docjns) 
(select '$nama', docno, doctype, idlocation, docdate, description, 'I' as status, '$nama', to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp, cancelby, canceldate, docnotmp, holdrow, holdby, updatedate, updateby, '' as idsection, '' as descsection, 'VOID_BBK' as docjns
from sc_trx.item_bbk_mst where docno='$docno');

INSERT INTO sc_tmp.item_void_dtl(
docno, docref, doctype, idlocation, idarea, idbarang, onhand, onhandtrans, unit, lasttrxdate, subunit, subunittrans, description, status, inputby, inputdate, id, docnotmp, holdrow, holdby, updatedate, updateby, idsection, descsection, docjns,uniqueidobd,idpodtl,idrev,batch)
(select '$nama', docno, doctype, idlocation, idarea, idbarang, onhand, onhandtrans, unit, lasttrxdate, subunit, subunittrans, description, 'I', '$nama', to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp, id, docnotmp, holdrow, holdby, updatedate, updateby, '' as idsection, '' as descsection,'VOID_BBK' as docjns,
coalesce(uniqueidobd,''),coalesce(idpodtl,0),coalesce(idrev,0),coalesce(batch,'')
from sc_trx.item_bbk_dtl where docno='$docno');
         ");
                $getResult = array('status' => true, 'messages' => 'Data sukses ter load');
                echo json_encode($getResult);

            } else {
                $getResult = array('status' => false, 'messages' => 'Dokumen Salah / Tidak Ada/ Sedang Terload/Dokumen Sudah Dikeluarkan');
                echo json_encode($getResult);
            }


        } else {
            $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($getResult);
        }
    }
    function clearEntryVoidbbk()
    {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(docno,'')='$nama'";
        $dtl = $this->m_bbk->q_tmp_item_bbk_mst($param);
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.item_void_mst');
        $builder_dtl = $this->db->table('sc_tmp.item_void_dtl');
        $buildertx = $this->db->table('sc_trx.item_void_mst');
        $builderdtltx = $this->db->table('sc_trx.item_void_dtl');
        //PROSES INPUT
        if ($status==='I') {
            $builder->where('docno', $nama);
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno', $nama);
                $builder_dtl->delete();

                $builder->where('docno', $nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk/void'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else if ($status==='E')
        {   $builder->where('docno',$nama);
            if ($builder_dtl->update(array('status' => 'C'))) {
                $builder_dtl->where('docno',$nama);
                $builder_dtl->delete();

                $builder->where('docno',$nama);
                $builder->delete();

                $buildertx->where('docno',trim($dtl->getRowArray()['docnotmp']));
                $buildertx->update(array(
                    'status' => 'P',
                    'holdrow' => '',
                    'holdby' => '',
                ));
                $builderdtltx->where('docno',trim($dtl->getRowArray()['docnotmp']));
                $builderdtltx->update(array(
                    'status' => 'P',
                    'holdrow' => '',
                    'holdby' => '',
                ));
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk/void'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            if ($builder->update(array('status' => 'C'))) {
                $builder_dtl->where('docno',$nama);
                $builder_dtl->delete();

                $builder->where('docno',$nama);
                $builder->delete();
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('stock/bbk/void'));
            } else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        }
    }

    function list_tmp_void_dtl(){
        $list = $this->m_bbk->get_t_tmp_void_bbk_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            //$row[] = $no;
            $row[] = ' <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkvoid[]" name="checkvoid[]" value="'.trim($lm->id).'" checked>
                        </div>';
            /*
            $row[] = '
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu" role="menu">
                      <a class="dropdown-item" href="#" onclick="edit_bbm('."'".trim($lm->id)."'".');"><i class="fa fa-gear"></i> Update</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="delete_bbm('."'".trim($lm->id)."'".');"><i class="fa fa-trash"></i> Delete </a>
                    </div>
            </div>
             ';*/
            $row[] = $lm->docref;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->batch;
            $row[] = $lm->onhandtrans;
            $row[] = $lm->unit;

            $row[] = $lm->nmarea;
            $row[] = $lm->description;
            $row[] = $lm->nmstatus;


            //$row[] = $lm->nmstatus;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_bbk->t_tmp_void_bbk_dtl_view_count_all(),
            "recordsFiltered" => $this->m_bbk->t_tmp_void_bbk_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_void_mst(){
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$nama'";
        $data = $this->m_bbk->q_tmp_item_void_bbk_mst($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo json_encode($output);
        //echo $this->fiky_encryption->jDatatable($output);
    }
    function showing_item_void_dtl($id){
        $data = $this->m_bbk->q_tmp_item_void_bbk_dtl(" and id='$id'")->getRow();
        echo json_encode($data);
    }

    function verifikasi_void_mst_stock_bbk(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $idlocation = $this->request->getPost('idlocation');
        $docdate = $this->request->getPost('docdate');
        $docref = strtoupper(trim($this->request->getPost('docref')));
        $docjns = strtoupper(trim($this->request->getPost('docjns')));
        $description = strtoupper(trim($this->request->getPost('description')));

        $dtl = $this->m_location->q_mlocation(" and idlocation='$idlocation'")->getRowArray();

        if (!empty($loccode)) {
            $info = array (
                'docref' => $docref,
                'idlocation' => $loccode,
                'description' => $description,
                'docdate' => date('Y-m-d',strtotime($docdate)),
                'docjns' => $docjns,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
                'status' => 'F',
            );
            $builder= $this->db->table('sc_tmp.item_void_mst');
            $builder->where('docno',$nama);
            $builder->update($info);

        }
        return redirect()->to(base_url('stock/bbk/void'));

    }

    function load_bbm_to_bbk(){
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key=='1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            //$id = trim($dataprocess->id);
            //$type = trim($dataprocess->type);
            $doc_idrev = trim($dataprocess->doc_idrev);
            $docnobbm = $doc_idrev;


            $nama = trim($this->session->get('nama'));
            //$loccode = trim($this->session->get('loccode'));
            $param = " and docno='$nama'";
            /* Load bbm mst */
            $dtl = $this->m_bbk->q_tmp_item_bbk_mst($param)->getRowArray();
            $cek = $this->m_bbk->q_tmp_item_bbk_dtl($param)->getNumRows();
            $docdate = date('Y-m-d',strtotime(trim($dtl['docdate'])));
            $docref = trim($dtl['docref']);
            $idsection = trim($dtl['idsection']);
            $descsection = trim($dtl['descsection']);
            $docjns = trim($dtl['docjns']);
            $inputdate = date('Y-m-d H:i:s');

            if ($cek>0) {

            }  else {
                $this->db->query("insert into sc_tmp.item_bbk_dtl
(docno, docref, doctype, idlocation, idarea, idbarang, onhand, onhandtrans, unit, lasttrxdate, subunit, subunittrans, description, status, inputby, inputdate, id, docnotmp, holdrow, holdby, updatedate, updateby, idsection, descsection, docjns,uniqueidobd,idpodtl,doc_idrev,idrev,batch)
(select '$nama' as docno, '$docref', 'OUT' as doctype, idlocation, idarea, idbarang,onhandtrans, 0 , unit, '$docdate'::date, subunit, subunittrans, description, 'I' as status, '$nama', '$inputdate'::timestamp, id, '', holdrow, holdby, updatedate, updateby, '$idsection', '$descsection','$docjns',uniqueidobd,idpodtl,docno,id,batch	
from sc_trx.item_bbm_dtl where docno='$docnobbm' and status='P');");
            }

            $result = array('status' => true, 'messages' => 'Sukses Di Proses');
            echo json_encode($result);
        }


        //return redirect()->to(base_url('stock/bbk/input_bbk'));

    }

    function deleteUnheckVoid(){
        $nama = trim($this->session->get('nama'));
        $lb = $this->request->getPost('checkvoid');

        if(empty($lb)){
            //INSERT TRX ERROR
            $trx_build = $this->db->table('sc_mst.trxerror');
            $trx_build->where('userid',$nama);
            $trx_build->where('modul','I.D.D.6');
            $trx_build->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.D.D.6',
            );
            $trx_build->insert($infotrxerror);
            $getResult = array('status' => true, 'messages' => 'Calculation Success By' . $nama);
            echo json_encode($getResult);
        }
        //ganti delete all
        $this->db->query("
delete from sc_tmp.item_void_mst where docno='$nama';
delete from sc_tmp.item_void_dtl where docno='$nama';");

        /*
        foreach($lb as $index => $temp){
            $idnya=trim($lb[$index]);
            $builder1 = $this->db->table('sc_tmp.item_void_dtl');
            $builder1->delete(array('id' => $idnya,'docno' => $nama));
        }*/
        //INSERT TRX ERROR

        $trx_build = $this->db->table('sc_mst.trxerror');
        $trx_build->where('userid',$nama);
        $trx_build->where('modul','I.D.D.6');
        $trx_build->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nama,
            'nomorakhir2' => '',
            'modul' => 'I.D.D.6',
        );
        $trx_build->insert($infotrxerror);
        $getResult = array('status' => true, 'messages' => 'Calculation Success By' . $nama);
        echo json_encode($getResult);
    }

    function progresVoid(){
        $nama = trim($this->session->get('nama'));
        $lb = $this->request->getPost('checkvoid');

        if(empty($lb)){
            //INSERT TRX ERROR
            $trx_build = $this->db->table('sc_mst.trxerror');
            $trx_build->where('userid',$nama);
            $trx_build->where('modul','I.D.D.6');
            $trx_build->delete();
            $infotrxerror = array (
                'userid' => $nama,
                'errorcode' => 0,
                'nomorakhir1' => $nama,
                'nomorakhir2' => '',
                'modul' => 'I.D.D.6',
            );
            $trx_build->insert($infotrxerror);
            $getResult = array('status' => true, 'messages' => 'Calculation Success By' . $nama);
            echo json_encode($getResult);
        }
        foreach($lb as $index => $temp){
            $idnya=trim($lb[$index]);
            $builder1 = $this->db->table('sc_tmp.item_void_dtl');
            //$builder1->delete(array('id' => $idnya,'docno' => $nama));
            $invo = array('status' => 'O');
            $builder1->where(array('id' => $idnya,'docno' => $nama));
            $builder1->update($invo);
        }
        //INSERT TRX ERROR

        $trx_build = $this->db->table('sc_mst.trxerror');
        $trx_build->where('userid',$nama);
        $trx_build->where('modul','I.D.D.6');
        $trx_build->delete();
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => $nama,
            'nomorakhir2' => '',
            'modul' => 'I.D.D.6',
        );
        $trx_build->insert($infotrxerror);
        $getResult = array('status' => true, 'messages' => 'Calculation Success By' . $nama);
        echo json_encode($getResult);
    }
    function saveTmpMstVoid()
    {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data->key == '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            $dataprocess = $data->body;
            //$id = trim($dataprocess->id)?? null;
            $type = trim($dataprocess->type);
            //$docno = trim($dataprocess->docno)?? null;
            $docref = trim($dataprocess->docref);
            $docdate = trim($dataprocess->docdate);
            $description = strtoupper(trim($dataprocess->description));
            $inputby = $nama;
            $inputdate = date('Y-m-d H:i:s');
            $builder = $this->db->table('sc_tmp.item_void_mst');
            // CHECK INPUT TYPE
            if ($type === 'UPDATE') {
                $info = array(
                    'docref' => $docref,
                    'docdate' => date('Y-m-d', strtotime($docdate)),
                    'description' => $description,
                    'inputby' => $inputby,
                    'inputdate' => $inputdate,
                    'status' => 'F',
                );
                $builder->where('docno', $nama);
                if ($builder->update($info)) {
                    $getResult = array('status' => true, 'messages' => 'Data processing' . ' Code: ');
                    echo json_encode($getResult);
                } else {
                    $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
                    echo json_encode($getResult);
                }
            } else {
                $getResult = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($getResult);
            }
        }
    }

    // winacc area
    function import_pbl(){
        $data['title']="Import Data PBL Dari Winacc ";
        //echo ;
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $loccode=trim($this->session->get('loccode'));
        $kodemenu='I.D.E.7'; $versirelease='I.D.E.7/RELEASE.401'; $releasedate=date('2019-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.D.E.7'";
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
        $kmenu='I.D.E.7';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses']=$this->m_role->detail_user_akses($role,$kmenu)->getRowArray();

        $parameter = " and inputby='$nama'";
        $data['list_detail']=$this->m_bbk->q_tmp_item_pbl_wacc_dtl($parameter)->getResult();
        $data['adaisi']=$this->m_bbk->q_tmp_item_pbl_wacc_dtl($parameter)->getNumRows();

        $paramerror=" and userid='$nama'";
        $this->m_global->q_deltrxerror($paramerror);
        return $this->template->render('stock/bbk/v_import_pbl_wacc',$data);

    }

    function proses_upload_pbl() {
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $this->db->query("delete from sc_tmp.item_pbl_wacc_dtl where inputby='$nama'");
        $dtlloc = $this->m_location->q_mlocation(" and idlocation='$loccode'")->getRowArray();

        $path = "./assets/files/pblwacc/";
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
                echo '<a href="'.base_url('/stock/bbk/import_pbl').'">BACK</a>';
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
                        'docno' => trim($rowData[0][4]),
                        'docref' => trim($rowData[0][8]),
                        'idlocation' => trim($dtlloc['idlocation']),
                        'idarea' => trim($dtlloc['idarea_default']),
                        'doctype' => 'OUT',
                        'docjns' => trim($rowData[0][7]),
                        'trxdate' => date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][3])))),
                        'idbarang' => trim($rowData[0][0]),
                        'onhandtranspo' => trim($rowData[0][6]),
                        'namabarangx' => trim($rowData[0][1]),
                        'unit' => trim($rowData[0][2]),
                        'description' => trim($rowData[0][5]),
                        'senddate' =>  date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][3])))),
                        'uniqueidobd' => trim($rowData[0][4]).trim($rowData[0][0]).date('Y-m-d',strtotime(str_replace('/','-',trim($rowData[0][3])))),
                        'inputby'=>$nama,
                        'inputdate'=>date("Y-m-d H:i:s"),
                        'status' => 'I',
                    );

//                    $pm =  "and trim(docref)='".trim($rowData[0][1])."' and trim(docno)='".trim($rowData[0][0])."' and trim(uniqueidobd)='".trim($rowData[0][5])."'";
//                    $cek = $this->m_bbm->q_tmp_item_lbm_wacc_dtl($pm)->getNumRows();
                    if(trim($rowData[0][0]) != '') {
                        $builder = $this->db->table("sc_tmp.item_pbl_wacc_dtl");
                        $builder->insert($data);
                    }
                    $no++;
                }
                return redirect()->to(base_url('/stock/bbk/import_pbl'));
            }
        }
    }

    function clear_tmp_pbl(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("delete from sc_tmp.item_pbl_wacc_dtl where inputby='$nama'");
        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.D.7');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.D.7',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/stock/bbk/import_pbl'));
    }

    function final_tmp_pbl(){
        $nama = trim($this->session->get('nama'));
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $this->db->query("update sc_tmp.item_pbl_wacc_dtl set status='F' where inputby='$nama'");

        $builder_trxerror->where('userid',$nama);
        $builder_trxerror->where('modul','I.D.D.7');
        $builder_trxerror->delete();
        /* error handling */
        $infotrxerror = array (
            'userid' => $nama,
            'errorcode' => 0,
            'nomorakhir1' => '',
            'nomorakhir2' => '',
            'modul' => 'I.D.D.7',
        );
        $builder_trxerror->insert($infotrxerror);
        return redirect()->to(base_url('/stock/bbk/import_pbl'));
    }


    public function list_pbl()
    {
        $data['title']="List Barang Masuk PBL Win ACC";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.D.E.8'; $versirelease='I.D.E.8/BETA.001'; $releasedate=date('2022-04-12 00:00:00');
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
        return $this->template->render('stock/bbk/v_list_pbl_wacc',$data);
    }

    function list_pbl_wacc_pagination(){
        $list = $this->m_bbk->get_t_trx_item_pbl_wacc_dtl_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;

            $row[] = '<div class="text-wrap width-90">'. $lm->docno .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->docref .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->trxdate1 .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->idbarang .'</div>';
            $row[] = '<div class="text-wrap width-90 ratakanan">'. $lm->onhandtranspo .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->nmbarang .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->unit .'</div>';
            $row[] = '<div class="text-wrap width-150">'. $lm->description .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->senddate1 .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->uniqueidobd .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->status .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->inputby .'</div>';
            $row[] = '<div class="text-wrap width-90">'. $lm->inputdate .'</div>';

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_bbk->t_trx_item_pbl_wacc_dtl_view_count_all(),
            "recordsFiltered" => $this->m_bbk->t_trx_item_pbl_wacc_dtl_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_balance_pbl_wacc(){
        $list = $this->m_bbm->get_t_item_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = '
                <a class="btn btn-md btn-primary" href="#"  title="Tambahkan Barang" onclick="add_bbm('."'".trim($lm->idbarang)."'".')"><i class="fa fa-plus"></i> </a>
             ';
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->unit;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_bbk->t_item_view_count_all(),
            "recordsFiltered" => $this->m_bbk->t_item_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }



    function test_print(){
        $nama = trim($this->session->get('nama'));
        if ($this->request->getGet('docno')){
            $enc_docno = $this->fiky_encryption->sealed($this->request->getGet('docno'));
        } else {
            $enc_docno = $this->fiky_encryption->sealed($this->request->getPost('docno'));
        }

        $docno=trim($this->fiky_encryption->unseal($enc_docno));

        $title = "Testing Print 58";

        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_bbk->q_trx_item_bbk_mst($param);


        $datajson =  base_url("stock/bbk/api_print_stock_bbk/?enc_docno=$enc_docno") ;
        $datamrt =  base_url("assets/mrt/print58.mrt") ;
        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }


}
