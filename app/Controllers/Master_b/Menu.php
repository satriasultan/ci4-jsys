<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Menu extends BaseController
{
    public function index()
    {
        $data['title']="MASTER DATA USER";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.4'; $versirelease='I.M.A.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']="";
        $data['list_menu_utama']=$this->m_menu->list_menu_utama()->getResult();
        $data['list_menu_sub']=$this->m_menu->list_menu_sub()->getResult();
        $data['list_menu_submenu']=$this->m_menu->list_menu_submenu()->getResult();
        $data['list_menu_opt_utama']=$this->m_menu->list_menu_opt_utama()->getResult();
        $data['list_menu_opt_sub']=$this->m_menu->list_menu_opt_sub()->getResult();
        $message=$this->session->getFlashdata('message');
        if (!empty($message)) {
            $data['message']="<div class='alert alert-info'> $message </div>";
        } else {
            $data['message']='';
        }
        return $this->template->render('master/menu/v_menu',$data);
        ////$this->template->display('master/menu/v_menu',$data);
    }

    function edit($pmenu)
    {

        if (empty($pmenu)){
            return redirect()->to(base_url("master/menu"));
        } else {
            $data['title']='EDIT DATA MENU';
            $dtlbranch=$this->m_global->q_branch()->getRowArray();
            $branch=$dtlbranch['branch'];
            /* CODE UNTUK VERSI*/
            $nama=trim($this->session->get('nama'));
            $kodemenu='I.M.A.4'; $versirelease='I.M.A.4/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
            $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
            $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
            $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
            $data['kodemenu']=$kodemenu; $data['version']=$versidb;
            /* END CODE UNTUK VERSI */
            if (!empty($message)) {
                $data['message']="<div class='alert alert-info'> $message </div>";
            } else {
                $data['message']='';
            }
            $data['dtl_menu']=$this->m_menu->dtl_menu(strtoupper($pmenu));
            $data['list_menu_opt_utama']=$this->m_menu->list_menu_opt_utama()->getResult();
            $data['list_menu_opt_sub']=$this->m_menu->list_menu_opt_sub()->getResult();
            //$this->template->display('master/menu/v_editmenu',$data);
            return $this->template->render('master/menu/v_editmenu',$data);
        }
    }

    function hps($kodemenu)
    {
        $cek_delete=$this->m_menu->cek_del($kodemenu);
        if ($cek_delete>0) {
            return redirect()->to(base_url("master/menu"));
        } else {
            $builder = $this->db->table('sc_mst.menuprg');
            $builder->where('kodemenu',$kodemenu);
            $builder->delete();
            return redirect()->to(base_url("master/menu"));
        }
    }

    function save()
    {
        $tipe=$this->request->getPost('tipe');
        $kodemenu=strtoupper(trim($this->request->getPost('kdmenu')));
        $namamenu=strtoupper($this->request->getPost('namamenu'));
        $parentmenu=strtoupper($this->request->getPost('parentmenu'));
        $parentsubmenu=strtoupper($this->request->getPost('parentsubmenu'));
        $urut=strtoupper($this->request->getPost('urut'));
        $child=strtoupper($this->request->getPost('childmenu'));
        $holdmenu=$this->request->getPost('holdmenu');
        $linkmenu=$this->request->getPost('linkmenu');
        $iconmenu=$this->request->getPost('iconmenu');
        $cek_menu=$this->m_menu->cek_menu($kodemenu)->getNumRows();
        if ($tipe=='input') {
            if ($cek_menu>0){
                $this->session->setFlashdata('message', 'Failed Data Is Avaiable !!!');
                return redirect()->to(base_url("master/menu"));
            } else {
                $this->session->setFlashdata('message', 'Success Input Data !!!');
                $builder = $this->db->table('sc_mst.menuprg');
                $info_request=array(
                    'kodemenu'=>$kodemenu,
                    'namamenu'=>$namamenu,
                    'parentmenu'=>$parentmenu,
                    'parentsub'=>$parentsubmenu,
                    'urut'=>$urut,
                    'child'=>$child,
                    'holdmenu'=>$holdmenu,
                    'linkmenu'=>$linkmenu,
                    'iconmenu'=>$iconmenu,
                );
                $builder->insert($info_request);
                return redirect()->to(base_url("master/menu"));
            }
        } else if ($tipe=='edit'){
            $builder = $this->db->table('sc_mst.menuprg');
            $info_edit1=array(
                'namamenu'=>$namamenu,
                'parentmenu'=>$parentmenu,
                'parentsub'=>$parentsubmenu,
                'holdmenu'=>$holdmenu,
                'linkmenu'=>$linkmenu,
                'iconmenu'=>$iconmenu,
                'urut'=>$urut,
            );
            $builder->where('kodemenu',$kodemenu);
            $builder->update($info_edit1);
            //redirect("master/menu/edit/$kodemenu/upsuccess");
            $this->session->setFlashdata('message', 'Success Update Data !!!');
            return redirect()->to(base_url("master/menu/edit".'/'.$kodemenu));
        } else {
            $this->session->setFlashdata('message', 'Failed Data !!!');
            return redirect()->to(base_url("master/menu"));
        }
    }


}
