<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\MWeb;
use Config\MyConfig; // Loading config class


class Profile extends BaseController
{
    public function index()
    {
        //wajib per fungsi
        $myconfig = new MyConfig;
        
        //echo 'KINTIL'.$_SERVER['RECAPTCHAV2_SITEKEY'];
        $data['request'] = $this->request;
        $data['session'] = $this->session;
        $message=$this->session->getFlashdata('message');
        if (!empty($message)) {
            $data['message']="<div class='alert alert-info'> $message </div>";
        } else {
            $data['message']='';
        }
        $nama = trim($this->session->get('nama'));
        $dr = $this->fiky_encryption->checkDirectLc();
        $data['ip'] = $this->fiky_encryption->getUserIP();
        $data['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();
        return $this->template->render('web/v_profile',$data);

    }

    function saveprofile()
    {
        $tipe=$this->request->getPost('tipe');
        $splituser=explode('|',$this->request->getPost('user'));
        $nik = trim($this->request->getPost('nik'));
        $nama = trim($this->request->getPost('username'));
        $password = md5(md5(md5(strtoupper(trim($this->request->getVar('passwordweb'))))));
        $password2 = md5(md5(md5(strtoupper(trim($this->request->getVar('passwordweb2'))))));
        $expdate=$this->request->getPost('expdate');
        $hold=$this->request->getPost('hold');
        $enc_nik = $this->fiky_encryption->sealed($nik);
        $enc_username = $this->fiky_encryption->sealed($nama);

        if ($tipe==='edit')
        {
            if ($password != $password2){
                    $this->session->setFlashdata('message', 'Password Doesnot Match!!!');
                return redirect()->to(base_url("/profile"));
            } else {
                if (!empty($password) or $password!==''){
                    $info_edit2=array(
                        'passwordweb'=>$password,
                        'editdate'=>date('Y-m-d H:i:s'),
                        'editby'=>$this->session->get('nama')
                    );
                    $build =  $this->db->table('sc_mst.user');
                    $build->where('username',$nama);
                    $build->update($info_edit2);
                    //$builder->update();
                    $this->session->setFlashdata('message', 'Data user update, please logout & login again with your new password!!');
                    return redirect()->to(base_url("/profile"));
                }
            }
        } else {
            return redirect()->to(base_url("/profile"));
        }

    }
}
