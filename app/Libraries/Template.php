<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 2:48 PM
 *  * Last Modified: 7/7/20, 2:48 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */

namespace App\Libraries;

use App\Models\Web\M_Auth;
use App\Models\Web\M_Menuprg;
use App\Models\Web\MWeb;

#[\AllowDynamicProperties]
class Template
{
    function __construct(){
        $this->session = \Config\Services::session();
        $this->fiky_encryption = new Fiky_encryption();
        $this->default_style = new Default_style();
    }

    public function render($template, $data = [])
    {

        if (! $this->session->get('nama')) {
            return redirect()->to(base_url('/'));
        } else {
            $model_auth = new M_Auth();
            $model_menuprg = new M_Menuprg();
            $username = trim($this->session->get('nama'));
            $roleid = trim($this->session->get('roleid'));

            $tmp['_ini_stylenya']=$this->default_style->_getCss();
            $tmp['_ini_jsnya']=$this->default_style->_getJs();
            $tmp['_ini_keyaccess']=$this->default_style->_keyAccess('TEMPLATE_PAGE');
            $tmp['_ini_customnya']=$this->default_style->_getCustom();
            $menudata['list_menu_main']=$model_menuprg->list_menu_sidebar_main($roleid)->getResult();
            $menudata['list_menu_sub']=$model_menuprg->list_menu_sidebar_sub($roleid)->getResult();
            $menudata['list_menu_submenu']=$model_menuprg->list_menu_sidebar_submenu($roleid)->getResult();
            $menudata['user_menu']=$model_auth->getUser(" and username='$username'")->getRowArray();
            $menudata['userInfo'] = $model_auth->getUser(" and username='$username'")->getRowArray();

            $headerdata['user_menu']=$model_auth->getUser(" and username='$username'")->getRowArray();
            $headerdata['user_online']=$model_auth->user_online()->getResult();
            $headerdata['jumlah_online']=$model_auth->user_online()->getNumRows();
            $headerdata['list_login']=$model_auth->q_user_last_login()->getResult();


            $headerdata['userInfo'] = $model_auth->getUser(" and username='$username'")->getRowArray();


            $tmp['_content']=view($template,$data);
            $tmp['_sidebar']= view('template/mt_sidebar',$menudata);
            $tmp['_header']= view('template/mt_header',$headerdata);


            return view('template/mt_layout', $tmp);
            //return view($view, $data);



        }



    }
}
