<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class ValidatorPush extends BaseController
{
    function index(){
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }

    function validate_links(){
        //$data['title'] =  "Isi Title";
        //$this->load->view("leavesession/v_validator_mailer",$data);
        $this->load->view("leavesession/v_validator_mailer");
    }

    function repush_otomatis(){
        $this->db->cache_delete('validatorPush','repush_otomatis');

        $param = " and trim(coalesce(status,''))='A'";
        $loop = $this->m_mobileApprovals->q_push_approval_system($param)->result();

        $cek = $this->m_mobileApprovals->q_push_approval_system($param)->num_rows();
        if ($cek > 0){
            foreach ($loop as $lp) {
                $this->fiky_notification_push->onePushVapeApprovalHrms(trim($lp->docref),trim($lp->nik_atasan),trim($lp->docno));
            }
            echo json_encode(array('status' => true, 'messages' => 'Data Sukses Di Proses'));
        } else {
            echo json_encode(array('status' => false, 'messages' => 'Tidak Ada Data Di Proses'));
        }

    }


    function renderpdf($doc){
        header('Access-Control-Allow-Origin: *');
        $docno = trim($doc);
        $path = "./assets/files/lpb_print";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        $barcode = "./assets/files/imgbarcode/".$docno.'.png';

        $data['barcode'] =$barcode;
        $param_mst = " and docno='$docno'";
        $param_dtl = " and coalesce(docno,'')='$docno' and coalesce(status,'')='P'";

        $data['master'] = $this->m_bbm->q_trx_item_bbm_mst($param_mst)->getRowArray();
        //$data['detail'] = $this->m_bbm->q_trx_item_bbm_dtl($param_dtl)->getResult();
        $data['detail'] = $this->m_bbm->q_trx_item_bbm_dtl_printpdf($param_dtl)->getResult();
        $rowdtl = $this->m_bbm->q_trx_item_bbm_dtl_printpdf($param_dtl)->getNumRows();
        /* IF ELSE PAPER DISINI , PEMBACAAN BANYAKNYA DETAIL BELUM DI SETING*/

        $this->fiky_pdf->loadHtml(view('stock/bbm/v_renderpdf_lpb',$data));

        if ($rowdtl <= 10){
            $custompaper=array(0,0,300,1000);
        } else if ($rowdtl > 10 and $rowdtl <= 15){
            $custompaper=array(0,0,300,1407);
        } else {
            // (Optional) Setup the paper size and orientation
            $custompaper=array(0,0,300,2407);
        }

        $this->fiky_pdf->setPaper($custompaper, 'portrait');
        $this->fiky_pdf->set_option('isRemoteEnabled', true); // <-- object ini yang perlu kita tambahkan
        // Render the HTML as PDF
        $this->fiky_pdf->render();

        // Output the generated PDF to Browser Dowload
        //$this->fiky_pdf->stream();

        // Output on server put file
        $output = $this->fiky_pdf->output();

        file_put_contents($path.'/'.$docno.'.pdf', $output);
        //file_put_contents($path.'/'.$docno.'.pdf', $output, stream_context_create($arrContextOptions));

        //return redirect()->to(base_url('/stock/bbm/detail_stock_bbm?docno=42424d3232303630303032'));
        return view('stock/bbm/v_renderpdf_lpb',$data);
    }

    function renderpdf_pbl($doc){
        header('Access-Control-Allow-Origin: *');
        $docno = trim($doc);
        $path = "./assets/files/pbl_print";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }

        $barcode = "./assets/files/imgbarcode/".$docno.'.png';

        $data['barcode'] =$barcode;
        $param_mst = " and docno='$docno'";
        $param_dtl = " and docno='$docno'";

        $data['master'] = $this->m_bbk->q_trx_item_bbk_mst($param_mst)->getRowArray();
        $data['detail'] = $this->m_bbk->q_trx_item_bbk_dtl($param_dtl)->getResult();
        $rowdtl = $this->m_bbk->q_trx_item_bbk_dtl($param_dtl)->getNumRows();
        /* IF ELSE PAPER DISINI , PEMBACAAN BANYAKNYA DETAIL BELUM DI SETING*/

        $this->fiky_pdf->loadHtml(view('stock/bbm/v_renderpdf_pbl',$data));

        if ($rowdtl <= 10){
            $custompaper=array(0,0,300,800);
        } else {
            // (Optional) Setup the paper size and orientation
            $custompaper=array(0,0,300,1207);
        }

        $this->fiky_pdf->setPaper($custompaper, 'portrait');
        $this->fiky_pdf->set_option('isRemoteEnabled', true); // <-- object ini yang perlu kita tambahkan
        // Render the HTML as PDF
        $this->fiky_pdf->render();

        // Output the generated PDF to Browser Dowload
        //$this->fiky_pdf->stream();

        // Output on server put file
        $output = $this->fiky_pdf->output();

        file_put_contents($path.'/'.$docno.'.pdf', $output);
        //file_put_contents($path.'/'.$docno.'.pdf', $output, stream_context_create($arrContextOptions));

        //return redirect()->to(base_url('/stock/bbm/detail_stock_bbm?docno=42424d3232303630303032'));
        //return view('stock/bbm/v_renderpdf_lpb',$data);
    }
}
