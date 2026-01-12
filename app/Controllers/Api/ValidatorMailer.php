<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class ValidatorMailer extends BaseController
{

    function index(){
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }

    function validate_links(){
        //$data['title'] =  "Isi Title";
        //$this->load->view("leavesession/v_validator_mailer",$data);
        $this->load->view("leavesession/v_validator_mailer");
    }

    function cli_mailtest(){
        $email = \Config\Services::email();

        $email->setFrom('noreply@jts.co.id', 'Test Mail Message');
        $email->setTo('fiky.ashariza@jts.co.id');
        $email->setCC('ikangurame3@gmail.com');
        $email->setBCC('');

        $email->setSubject('Email Test');
        $email->setMessage('Testing the email class.');

        if ($email->send()) {

            return json_encode(['status' => 'true','message' => 'mail success send']);
        } else {
            return json_encode(['status' => 'false','message' => 'error mail not sent']);
        }
    }
    function cli_mailoutbox_sent()
    {
        $loop_app = $this->db->query("
            select *,case 
when right(trim(mailto),1)=',' then left(trim(mailto),-1)
else trim(mailto) end as mailto2 from public.mail_outbox where coalesce(mailstatus,'NO_SENT') NOT IN ('SENT','FAILED') ORDER BY send_date ASC 
        ");
        $loop_appx = $loop_app->getResult();

        foreach ($loop_appx as $lr) {
            /* GENERATE FROM OUTBOX  */
            $this->email->setFrom('noreply@jts.co.id', 'IT System Reminder');
            $this->email->setTo(trim($lr->mailto));
            $this->email->setCC(trim($lr->mailcc));
            //$this->email->setBCC($bcc);
            $this->email->setSubject($lr->mailsubject);
            if (trim($lr->mailattach) !== ''){
                $this->email->attach($lr->mailattach);
            }
            $this->email->setMessage($lr->mailmessage);
            if ($this->email->send()) {
                $this->db->query("update mail_outbox set mailstatus='SENT', receive_date='".date('Y-m-d H:i:s')."' where
                                    docno='".$lr->docno."' and
                                    doctype='".$lr->doctype."' and
                                    erptype='".$lr->erptype."' and
                                    send_date='".$lr->send_date."' and
                                    mailto='".$lr->mailto."'
                                    
                ");
                return json_encode(['status' => 'true','message' => 'mail success send']);
                //break;
            } else {
                $this->db->query("update mail_outbox set mailstatus='FAILED' where
                                    docno='".$lr->docno."' and
                                    doctype='".$lr->doctype."' and
                                    erptype='".$lr->erptype."' and
                                    send_date='".$lr->send_date."' and
                                    mailto='".$lr->mailto."'
                                    
                ");
                return json_encode(['status' => 'false','message' => 'error mail not sent']);
            }
        }
    }

    function test_del_cache(){
        //$this->db->cache_delete_all();
        $uri = "application/cache2/master+menu";
        //$this->db->clear_path_cache($uri);
        $this->db->cache_delete('validatorMailer','test_del_cache');
        $this->db->cache_delete('master','menu');
        echo 'delete';
    }


    function capture_it_reminder(){
        $logoUrl = base_url('assets/img/logo-jts-putih.png');
        $namaApprover = 'SYSTEM';
        $emailTo      = 'it@jts.co.id';
        $roleidUser      = 'SA';
        $bagianUser      = 'IT';
        $inputdate = date('Y-m-d H:i:s');
        $msender = 'IT SYSTEM';
        $subject = 'PEMBAYARAN IT';
        // Ambil data pembayaran
        $datapembayaran = $this->db->query("
        SELECT 
            nmpembayaran,
            nmpengguna,
            renewal,
            ttlnetto,
            nmsupplier,
            description,
            (duedate_next - CURRENT_DATE) AS selisih_hari
        FROM sc_mst.jasa_pembayaran
        WHERE duedate_next <= (CURRENT_DATE + INTERVAL '25 days');
    ")->getResult();

        // Build table rows untuk pembayaran
        $pembayaranRows = '';
        $noPay = 1;

        foreach ($datapembayaran as $row) {
            $pembayaranRows .= '
            <tr>
                <td style="border:1px solid #ddd;">'.$noPay++.'</td>
                <td style="border:1px solid #ddd;">'.$row->nmpembayaran.'</td>
                <td style="border:1px solid #ddd;">'.$row->nmpengguna.'</td>
                <td style="border:1px solid #ddd;">'.$row->renewal.'</td>
                <td style="border:1px solid #ddd; text-align:right;">'.number_format($row->ttlnetto,0).'</td>
                <td style="border:1px solid #ddd;">'.$row->nmsupplier.'</td>
                <td style="border:1px solid #ddd;">'.$row->description.'</td>
                <td style="border:1px solid #ddd; text-align:center;">'.$row->selisih_hari.'</td>
            </tr>
        ';
        }



        // BODY EMAIL
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Reminder Pembayaran IT</title>
        </head>

        <body style="margin:0; padding:0; background-color:#f5f5f5; font-family:Arial, sans-serif;">

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff; max-width:800px; margin:auto; border:1px solid #ddd;">

            <!-- HEADER -->
            <tr style="background-color:#313131 !important; color:#ffffff;">
                <td style="padding:20px;">
                    <img src="' . $logoUrl . '" alt="Logo" style="height:40px;">
                </td>
            </tr>

            <!-- BODY -->
            <tr>
                <td style="padding:30px; font-size:14px; color:#333;">

                    <p>Yth. IT PT Jatim Taman Steel.Mfg,</p>
                    <p>Berikut adalah daftar pembayaran jasa IT yang jatuh tempo atau akan jatuh tempo dalam ≤ 25 hari:</p>

                    <!-- TABEL PEMBAYARAN -->
                    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-size:13px; margin-top:20px;">

                        <tr style="background:#eeeeee; font-weight:bold;">
                            <th style="border:1px solid #ddd;">No</th>
                            <th style="border:1px solid #ddd;">Nama Pembayaran</th>
                            <th style="border:1px solid #ddd;">Pengguna</th>
                            <th style="border:1px solid #ddd;">Renewal</th>
                            <th style="border:1px solid #ddd;">Total Netto</th>
                            <th style="border:1px solid #ddd;">Supplier</th>
                            <th style="border:1px solid #ddd;">Deskripsi</th>
                            <th style="border:1px solid #ddd;">Selisih Hari</th>
                        </tr>

                        ' . $pembayaranRows . '

                    </table>

                    <br><br>
                </td>
            </tr>

            <!-- FOOTER -->
            <tr>
                <td style="background-color:#C62828 !important; color:#ffffff !important; text-align:center; padding:15px; font-size:12px;">
                    Mohon jangan membalas pesan ini, ini adalah email otomatis dari sistem.<br>
                    &copy; 2025 PT Jatim Taman Steel. All rights reserved.
                </td>
            </tr>

        </table>

        </body>
        </html>
    ';


        $cekpem = $this->db->query("
        SELECT 
            nmpembayaran,
            nmpengguna,
            renewal,
            ttlnetto,
            nmsupplier,
            description,
            (duedate_next - CURRENT_DATE) AS selisih_hari
        FROM sc_mst.jasa_pembayaran
        WHERE duedate_next <= (CURRENT_DATE + INTERVAL '25 days');
    ")->getNumRows();

        if ($cekpem > 0) {
            // INSERT OUTBOX
            $this->db->query("
                INSERT INTO public.mail_outbox
                (docno, doctype, erptype, send_date, doctypename, mailto, mailsender, mailcc, mailbcc, 
                mailsubject, mailtype, mailmessage, mailattach, sentby, mailstatus, receive_date)
                VALUES
                ('RMDPAYMENT', 'ITMGMT', 'FORM', '$inputdate', 'SIKBSP', '$emailTo', '$msender', '', '', 
                '$subject', 'html', '$body', NULL, 'SYSTEM', 'NO_SENT', NULL);
            ");

            // === JSON RESPONSE SUCCESS ===
            echo json_encode([
                "status" => "success",
                "message" => "Data reminder berhasil dimasukkan ke mail_outbox."
            ]);
        } else {
            // === JSON RESPONSE SUCCESS ===
            echo json_encode([
                "status" => "failed",
                "message" => "Tidak ada data pembayaran."
            ]);
        }


    }


}
