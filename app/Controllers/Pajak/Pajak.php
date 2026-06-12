<?php


namespace App\Controllers\Pajak;

use App\Controllers\BaseController;

class Pajak extends BaseController
{
    
    public function laporan()
    {
        $data['title']="Laporan Pajak";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.J.A.1'; $versirelease='I.J.A.1/BETA.001'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.J.A.1'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        // $param = " and coalesce(inputby,'')='$nama'";
        // $dtl = $this->m_purchase->q_pp_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        // if ($dtl->getNumRows()>0) {
        //     $title = "WARNING !!!";
        //     $urlclear = base_url('purchase/trans/clearEntryPP');
        //     $urlnext = base_url('purchase/trans/addPP');
        //     $body = " Entry not finished found....!!!";
        //     $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        // } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.J.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('pajak/v_list_lappajak',$data);
    }


    
    function list_lappajak(){
        $list = $this->m_pajak->get_t_front_pajak_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.J.A.1';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        // $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        // $canPrint = isset($datadtl['dtl_akses']['a_rendkrt']) && trim($datadtl['dtl_akses']['a_rendkrt']) === 't';
        // $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';
        // $total_debit = 0;
        // $total_kredit = 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();

            $row[] = $lm->docno;

            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;
            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d/m/Y');

            } else {
                $jatuhTempo = '';
            }
            $row[] = $jatuhTempo;

            $row[] = $lm->kdperusahaan;
            $row[] = $lm->nmperusahaan;
            $row[] = $lm->alamat;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->qty;
            $row[] = $lm->unit;

            $row[] = $lm->currcode;
            $row[] = '<div class="ratakanan">' . number_format($lm->kurs, 2, '.', ',') . '</div>';
            $row[] = $lm->idtax;
            $row[] = '<div class="ratakanan">' . number_format($lm->harga, 2, '.', ',') . '</div>';

            // nilai total bruto
            $row[] = '<div class="ratakanan">' . number_format($lm->nilai, 2, '.', ',') . '</div>';
                        
            // nilai pajak
            $row[] = '<div class="ratakanan">' . number_format($lm->nilaipajak, 2, '.', ',') . '</div>';

            // nilai konversi
            $row[] = '<div class="ratakanan">' . number_format($lm->nilaikonversi, 2, '.', ',') . '</div>';

            $row[] = $lm->nmsalesman;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_pajak->t_front_pajak_view_count_all(),
            "recordsFiltered" => $this->m_pajak->t_front_pajak_view_count_filtered(),
            "data" => $data
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


}