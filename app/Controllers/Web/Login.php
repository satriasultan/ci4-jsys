<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\MWeb;
use Config\MyConfig; // Loading config class


class Login extends BaseController
{
    public function index()
    {
        //wajib per fungsi
        $this->fiky_encryption->fill_mac();
        $myconfig = new MyConfig;

        //echo 'KINTIL'.$_SERVER['RECAPTCHAV2_SITEKEY'];
        $data['request'] = $this->request;
        $data['session'] = $this->session;

        $vals = array(
            'img_path'      => 'assets/captcha/',
            'img_url'       => base_url().'/assets/captcha/',
            'font_path'     => FCPATH.'assets/fonts/texb.ttf',
            'img_width'     => '250',
            'img_height'    => 55,
            'expiration'    => 7200,
            'word_length'   => 4,
            'font_size'     => 100,
            'pool'          => '0123456789',

            'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
            )
        );


        //$cap = create_captcha($vals);
        //$capword=md5(strtolower($cap['word'] ??= ''));
        //$this->session->set('keycode',$capword);
        //$data['captcha_img'] = $cap['image'];
        ////$this->session->markAsFlashdata('message');
        /// Session Expiration
        $this->db->query("
delete from ci4_sessions where id in (
select id from (
select *,EXTRACT(EPOCH FROM to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp - to_char(timestamp,'yyyy-mm-dd HH24:mi:ss')::timestamp) as msession
from ci4_sessions )as x
where msession > 7200
order by msession);");

        $this->fiky_encryption->checkDirectLc();
        if ($this->session->get('nama')) {

            if (trim($this->session->get('roleid'))==='ESS') {
                return redirect()->to(base_url('trans/ess_dashboard'));
            } else {
                return redirect()->to(base_url('/dashboard/menu'));
            }

        } else {
            $data['recaptha_sitekey'] = $myconfig->recaptha_sitekey;
            $data['recaptha_secret'] = $myconfig->recaptha_secret;
            return view('web/v_login',$data);
        }


    }

    public function proses()
    {
        $myconfig   = new MyConfig();
        $model_auth = new MWeb();

        // ==========================================
        // INPUT
        // ==========================================

        $username = strtoupper(
            trim((string) $this->request->getVar('username'))
        );

        $password = (string) $this->request->getVar('password');

        $logindate = trim(
            (string) $this->request->getVar('logindate')
        );

        $recaptchaResponse = trim(
            (string) $this->request->getVar('g-recaptcha-response')
        );

        $ip = $this->fiky_encryption->getUserIP();


        // ==========================================
        // VALIDASI INPUT
        // ==========================================

        if ($username === '' || $password === '' || $logindate === '') {

            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Username, Password dan Tanggal wajib diisi.',
                'result'   => 'error'
            ]);
        }


        // ==========================================
        // RECAPTCHA
        // ==========================================

        if ($recaptchaResponse === '') {

            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Silakan verifikasi reCAPTCHA terlebih dahulu.',
                'result'   => 'error'
            ]);
        }

        $secret = $myconfig->recaptha_secret;

        $credential = [
            'secret'   => $secret,
            'response' => $recaptchaResponse,
            'remoteip' => $ip
        ];

        $verify = curl_init();

        curl_setopt_array($verify, [
            CURLOPT_URL            => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($credential),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($verify);

        $curlError = curl_error($verify);

        curl_close($verify);


        // ==========================================
        // RECAPTCHA CURL ERROR
        // ==========================================

        if ($response === false || $response === '' || $curlError !== '') {

            log_message(
                'error',
                'reCAPTCHA CURL Error: ' . $curlError
            );

            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                'result'   => 'error'
            ]);
        }


        // ==========================================
        // RECAPTCHA RESPONSE
        // ==========================================

        $status = json_decode($response, true);

        if (!is_array($status) || empty($status['success'])) {

            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Verifikasi reCAPTCHA tidak valid.',
                'result'   => 'error'
            ]);
        }


        // ==========================================
        // CHECK USER
        // ==========================================

        $gu = $model_auth->cek_user_login(
            $username,
            $password,
            'NO'
        );

        if (!$gu) {

            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Username atau Password Salah.',
                'result'   => 'error'
            ]);
        }

        $dtl = $gu->getRowArray();


        // ==========================================
        // LOGIN FAILED
        // ==========================================

        if ($gu->getNumRows() !== 1 || empty($dtl)) {

            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Username atau Password Salah.',
                'result'   => 'error'
            ]);
        }


        // ==========================================
        // SESSION
        // ==========================================

        $newdata = [
            'count'     => $gu->getNumRows(),
            'user'      => trim($dtl['nik']),
            'nama'      => trim($dtl['username']),
            'lvl'       => trim($dtl['level_akses']),
            'nik'       => trim($dtl['nik']),
            'loccode'   => trim($dtl['loccode']),
            'kddept'    => trim($dtl['kddept']),
            'roleid'    => trim($dtl['roleid']),
            'site_lang' => trim($dtl['lang']),
            'logindate' => $logindate
        ];

        $this->session->set($newdata);


        // ==========================================
        // LOGIN LOG
        // ==========================================

        $blog = $this->db->table('sc_log.log_time');

        $blog->insert([
            'nik' => trim($dtl['username']),
            'tgl' => date('Y-m-d H:i:s'),
            'ip'  => $ip
        ]);


        // ==========================================
        // USER ONLINE
        // ==========================================

        $uonline = $this->db->table('sc_log.useronline');


        // Hapus login expired > 2 jam
        $this->db->query("
        DELETE FROM sc_log.useronline
        WHERE tgl <= NOW() - INTERVAL '2 hours'
    ");


        // Hapus user yang sedang login sebelumnya
        $uonline
            ->where('username', trim($dtl['username']))
            ->delete();


        // Insert user online
        $uonline->insert([
            'username'    => trim($dtl['username']),
            'tgl'         => date('Y-m-d H:i:s'),
            'ip'          => $ip,
            'statuslogin' => 'YES'
        ]);


        // ==========================================
        // UPDATE LAST LOGIN
        // ==========================================

        $upuser = $this->db->table('sc_mst.user');

        $upuser
            ->where('username', trim($dtl['username']))
            ->update([
                'lastlogin' => date('Y-m-d H:i:s')
            ]);


        // ==========================================
        // SCHEDULER CUTI
        // ==========================================

        $model_auth->schedular();


        // ==========================================
        // FLASH MESSAGE
        // ==========================================

        $this->session->setFlashdata(
            'message',
            'Form has been successfully submitted'
        );


        // ==========================================
        // REDIRECT
        // ==========================================

        $redirect = base_url('dashboard/menu');


        // ==========================================
        // LOGIN SUCCESS
        // ==========================================

        return $this->response->setJSON([
            'status'   => true,
            'messages' => 'Login Sukses',
            'result'   => 'ok',
            'redirect' => $redirect
        ]);
    }
}
