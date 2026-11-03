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
        $model_auth = new MWeb();

        // ==========================================
        // INPUT
        // ==========================================

        $username = strtoupper(trim((string) $this->request->getVar('username')));
        $password = (string) $this->request->getVar('password');
        $logindate = trim((string) $this->request->getVar('logindate'));

        $ip = $this->request->getIPAddress();

        // ==========================================
        // RATE LIMITING
        // Maksimal 10 request / IP / 60 detik
        // ==========================================

        $throttler = service('throttler');
        $ipKey = 'login-ip-' . hash('sha256', $ip);

        if ($throttler->check($ipKey, 10, 60) === false) {
            return $this->response
                ->setStatusCode(429)
                ->setJSON([
                    'status'   => false,
                    'messages' => 'Terlalu banyak percobaan login. Silakan coba lagi nanti.',
                    'result'   => 'error'
                ]);
        }

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
        // CHECK USER
        // ==========================================

        $gu = $model_auth->cek_user_login(
            $username,
            $password,
            'NO'
        );

        if (!$gu || $gu->getNumRows() !== 1) {
            return $this->response->setJSON([
                'status'   => false,
                'messages' => 'Username atau Password Salah.',
                'result'   => 'error'
            ]);
        }

        $dtl = $gu->getRowArray();

        if (empty($dtl)) {
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
            'count'        => $gu->getNumRows(),
            'user'         => trim($dtl['nik'] ?? ''),
            'nama'         => trim($dtl['username'] ?? ''),
            'lvl'          => trim($dtl['level_akses'] ?? ''),
            'nik'          => trim($dtl['nik'] ?? ''),
            'loccode'      => trim($dtl['loccode'] ?? ''),
            'kddept'       => trim($dtl['kddept'] ?? ''),
            'roleid'       => trim($dtl['roleid'] ?? ''),
            'site_lang'    => trim($dtl['lang'] ?? ''),
            'logindate'    => $logindate,

            // USER PROFILE
            'nmlengkap'    => trim($dtl['nmlengkap'] ?? ''),
            'nickname'     => trim($dtl['nickname'] ?? ''),
            'idbranchjob'  => trim($dtl['idbranchjob'] ?? ''),
            'nmbranchjob'  => trim($dtl['nmbranchjob'] ?? '')
        ];

        $this->session->regenerate(true);
        $this->session->set($newdata);

        // ==========================================
        // LOGIN TRANSACTION LOG
        // ==========================================

        $this->m_global->insertlogtrans(
            trim($dtl['username']),
            'L',
            'AUTH',
            'LOGIN'
        );

        // ==========================================
        // LOGIN LOG
        // ==========================================

        $this->db->table('sc_log.log_time')->insert([
            'nik' => trim($dtl['username']),
            'tgl' => date('Y-m-d H:i:s'),
            'ip'  => $ip
        ]);

        // ==========================================
        // USER ONLINE
        // ==========================================

        // Hapus login expired lebih dari 2 jam
        $this->db->query("
        DELETE FROM sc_log.useronline
        WHERE tgl <= NOW() - INTERVAL '2 hours'
    ");

        // Hapus sesi online user sebelumnya
        $this->db->table('sc_log.useronline')
            ->where('username', trim($dtl['username']))
            ->delete();

        // Insert user online
        $this->db->table('sc_log.useronline')->insert([
            'username'    => trim($dtl['username']),
            'tgl'         => date('Y-m-d H:i:s'),
            'ip'          => $ip,
            'statuslogin' => 'YES'
        ]);

        // ==========================================
        // UPDATE LAST LOGIN
        // ==========================================

        $this->db->table('sc_mst.user')
            ->where('username', trim($dtl['username']))
            ->update([
                'lastlogin' => date('Y-m-d H:i:s')
            ]);

        // ==========================================
        // SCHEDULER CUTI
        // ==========================================

        //// belum dipakai $model_auth->schedular();

        // ==========================================
        // FLASH MESSAGE
        // ==========================================

        $this->session->setFlashdata(
            'message',
            'Form has been successfully submitted'
        );

        // ==========================================
        // LOGIN SUCCESS
        // ==========================================

        return $this->response->setJSON([
            'status'   => true,
            'messages' => 'Login Sukses',
            'result'   => 'ok',
            'redirect' => base_url('dashboard/menu')
        ]);
    }
}
