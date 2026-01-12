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
                return redirect()->to(base_url('/dashboard'));
            }

        } else {
            $data['recaptha_sitekey'] = $myconfig->recaptha_sitekey;
            $data['recaptha_secret'] = $myconfig->recaptha_secret;
            return view('web/v_login',$data);
        }


    }

    public function proses()
    {
        $myconfig = new MyConfig;
        $model_auth = new MWeb();
        $recaptchaResponse = trim($this->request->getVar('g-recaptcha-response'));
        $username = strtoupper($this->request->getVar('username'));
        $password = $this->request->getVar('password');
        $ip = $this->fiky_encryption->getUserIP();

        // form data
        $secret = $myconfig->recaptha_secret;

        $credential = array(
            'secret' => $secret,
            'response' => $recaptchaResponse
        );

        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($credential));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $status = json_decode($response, true);
        //echo $status['success'];
        $gu = $model_auth->cek_user_login($username,$password,$hold='NO');
        $dtl = $gu->getRowArray();

        //echo $gu->getNumRows();
        if ($gu->getNumRows() == 1){
            $newdata = [
                'count'  => $gu->getNumRows(),
                'user'  => trim($dtl['nik']),
                'nama' => trim($dtl['username']),
                'lvl' => trim($dtl['level_akses']),
                'nik' => trim($dtl['nik']),
                'loccode' => trim($dtl['loccode']),
                'kddept' => trim($dtl['kddept']),
                'roleid' => trim($dtl['roleid']),
                'site_lang' => trim($dtl['lang']),
            ];
            $this->session->set($newdata);
            $blog = $this->db->table('sc_log.log_time');
            $log_data=array(
                'nik' => trim($dtl['username']),
                'tgl'=>date("Y-m-d H:i:s"),
                'ip'=>$ip
            );
            $blog->insert($log_data);

            // FOR USER ONLINE
            $uonline = $this->db->table('sc_log.useronline');
            $uonline_self = $this->db->table('sc_log.useronline');
            //delete before login expiration 7200 second based app sessions
            $this->db->query("delete from sc_log.useronline
where (EXTRACT(EPOCH FROM (to_char(tgl,'yyyy-mm-dd HH24:mi:ss')::timestamp - to_char(now(),'yyyy-mm-dd HH24:mi:ss')::timestamp)) * -1) >= 7200;");
            //delete before login self
            $uonline_self->where('username',$username);
            $uonline_self->delete();


            $log_data_uonline=array(
                'username' => trim($dtl['username']),
                'tgl'=>date("Y-m-d H:i:s"),
                'ip'=>$ip,
                'statuslogin'=>'YES',
            );
            $uonline->insert($log_data_uonline);

            $upuser = $this->db->table('sc_mst.user');
            $upinfo = array('lastlogin' => date('Y-m-d H:i:s'));
            $upuser->where('username',trim($dtl['username']));
            $upuser->update($upinfo);


            /* schedular cuti */
            $model_auth->schedular();

            $output = array(
                'status' => 'success',
                'messages' => 'Login Sukses',
                'result' => 'ok',
                'fields' => array('username' => $username,
                    'password' => $password,
                    'status' => 'success'
                ),
            );
            echo json_encode($output,JSON_PRETTY_PRINT);
            $this->session->setFlashdata('message', 'Form has been successfully submitted');

            if (trim($dtl['roleid'])==='ESS') {
                return redirect()->to(base_url('trans/ess_dashboard'));
            } else {
                return redirect()->to(base_url('/dashboard'));
            }

        } else {
            $output = array(
                'status' => 'gagal',
                'messages' => 'Username atau Password Salah',
                'result' => 'error',
                'fields' => array('username' => $username,
                    'password' => $password,
                    'status' => 'success'
                ),
            );
            //echo json_encode($output,JSON_PRETTY_PRINT);
            $this->session->setFlashdata('message', 'Check inputan & Cek Verifikasi Wajib Tercentang');
            return redirect()->to(base_url('/'));
        }

    }
}
