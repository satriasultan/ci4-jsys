<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\MWeb;
use Config\MyConfig; // Loading config class


class Relogin extends BaseController
{
    public function index()
    {
        //wajib per fungsi
        $myconfig = new MyConfig;
        
        //echo 'KINTIL'.$_SERVER['RECAPTCHAV2_SITEKEY'];
        $data['request'] = $this->request;
        $data['session'] = $this->session;

        $dr = $this->fiky_encryption->checkDirectLc();
        $data['ip'] = $this->fiky_encryption->getUserIP();
        if ($dr === '.') {
            return redirect()->to(base_url('/dashboard'));
        } else {
            return view('web/v_relogin',$data);
        }

    }

    public function endDate()
    {
        //wajib per fungsi
        $myconfig = new MyConfig;

        //echo 'KINTIL'.$_SERVER['RECAPTCHAV2_SITEKEY'];
        $data['request'] = $this->request;
        $data['session'] = $this->session;

        return view('web/v_screenSaver.php',$data);
    }
}
