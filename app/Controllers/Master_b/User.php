<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class User extends BaseController
{
    public function index()
    {
        $data['title']="MASTER DATA USER";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.5'; $versirelease='I.M.A.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['message']="";
        $data['list_user']=$this->m_user->list_user()->getResult();
        $data['list_kary']=$this->m_user->list_karyawan()->getResult();
        $data['list_dept']=$this->m_user->list_departmen()->getResult();
        $data['list_lvljbt']=$this->m_user->list_lvljbt()->getResult();
        $data['list_gudang']=$this->m_user->q_lokasi_gudang()->getResult();
        $data['list_role']=$this->m_user->q_role()->getResult();
        $data['dtl_user']=$this->m_user->list_user()->getRowArray();
        $message=$this->session->getFlashdata('message');
        if (!empty($message)) {
            $data['message']="<div class='alert alert-info'> $message </div>";
        } else {
            $data['message']='';
        }
        return $this->template->render('master/user/v_user',$data);
    }

    public function editProfile($enc_nik,$enc_username)
    {
        $nik = trim($this->fiky_encryption->unseal($enc_nik));
        $username = trim($this->fiky_encryption->unseal($enc_username));

        if (empty($nik)){
            return redirect()->to(base_url("dashboard"));
        } else {
            $data['title']='UBAH PASSWORD USER';
            $message=$this->session->getFlashdata('message');
            if (!empty($message)) {
                $data['message']="<div class='alert alert-info'> $message </div>";
            } else {
                $data['message']='';
            }


            $data['dtl_user']=$this->m_user->dtl_user($nik,$username);
            return $this->template->render("master/user/v_editprofile", $data);
        }

    }

    function saveprofile()
    {
        $tipe=$this->request->getPost('tipe');
        $splituser=explode('|',$this->request->getPost('user'));
        $nik = trim($this->request->getPost('nik'));
        $nama = trim($this->request->getPost('username'));
        $password = md5(md5(md5(strtoupper($this->request->getVar('passwordweb')))));
        $password2 = md5(md5(md5(strtoupper($this->request->getVar('passwordweb2')))));
        $expdate=$this->request->getPost('expdate');
        $hold=$this->request->getPost('hold');
        $enc_nik = $this->fiky_encryption->sealed($nik);
        $enc_username = $this->fiky_encryption->sealed($nama);

        if ($tipe==='edit')
        {
            if ($password != $password2){
                $this->session->setFlashdata('message', 'Password Doesnot Match!!!');
                return redirect()->to(base_url("user/editprofile/$enc_nik/$enc_username"));
            } else {
                if (!empty($password) or $password!==''){
                    $info_edit2=array(
                        'passwordweb'=> $password,
                        'editdate' => date('d-m-Y'),
                        'editby' => trim($this->session->get('nama'))
                    );
                    $build =  $this->db->table('sc_mst.user');
                    $build->where('username',$nama);
                    $build->update($info_edit2);
                    //$builder->update();
                    $this->session->setFlashdata('message', 'Success Data Update');
                    return redirect()->to(base_url("user/editprofile/$enc_nik/$enc_username"));
                }
            }
        } else {
            return redirect()->to(base_url()("dashboard"));
        }

    }

    function list_user(){
        $list = $this->m_user->get_t_user_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $enc_iduser=$this->fiky_encryption->sealed(trim($lm->iduser));
            $enc_username=$this->fiky_encryption->sealed(trim($lm->username));
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
              <a class="btn btn-primary btn-sm btn-flat" href='."'". base_url('user/edit').'/'.''.$enc_iduser.'/'.$enc_username."'".'  title="Editing User" onclick="return confirm('."'".'Editing User ?  : '.trim($lm->username)."'".')"><i class="fa fa-gear"></i> </a>
              <a class="btn btn-danger btn-sm  btn-flat" href='."'". base_url('user/hps').'/'.''.$enc_iduser.'/'.$enc_username."'".'  title="Hapus Data User" onclick="return confirm('."'".'Detail Menu Programs ?  : '.trim($lm->username)."'".')"><i class="fa fa-trash"></i> </a>
              ';
            $row[] = $lm->username;
            $row[] = $lm->expdate1;
            $row[] = $lm->hold_id;
            $row[] = $lm->nmdept;
            $row[] = $lm->locaname;
            $row[] = $lm->rolename;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_user->t_user_view_count_all(),
            "recordsFiltered" => $this->m_user->t_user_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }
    
    function edit($enc_iduser,$enc_username){
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.A.5'; $versirelease='I.M.A.5/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $data['title']='EDIT DATA USER';
        $message=$this->session->getFlashdata('message');

        $iduser = trim($this->fiky_encryption->unseal($enc_iduser));
        $username = trim($this->fiky_encryption->unseal($enc_username));
        if (empty($iduser)){
            return redirect()->to(base_url()("user/"));
        } else {

            if (!empty($message)) {
                $data['message']="<div class='alert alert-info'> $message </div>";
            } else {
                $data['message']='';
            }
            $data['roleid'] = trim($this->session->get('roleid'));
            $data['dtl_user']=$this->m_user->dtl_user($iduser,$username);
            $data['list_lvljbt']=$this->m_user->list_lvljbt()->getResult();
            $data['list_gudang']=$this->m_user->q_lokasi_gudang()->getResult();
            $data['list_role']=$this->m_user->q_role()->getResult();
            $data['list_dept']=$this->m_user->list_departmen()->getResult();
            return $this->template->render('master/user/v_edituser',$data);
        }
    }

    function hps($enc_iduser,$enc_username){
        $iduser = trim($this->fiky_encryption->unseal($enc_iduser));
        $username = trim($this->fiky_encryption->unseal($enc_username));
        $builder = $this->db->table('sc_mst.user');
        $builder->where('iduser',$iduser);
        $builder->where('username',$username);
        $builder->delete();
        $this->session->setFlashdata('message', $username.' '.'Success Data Deleted');
        return redirect()->to(base_url('user'));
    }

    function save(){
        $tipe=$this->request->getPost('tipe');
        //$splituser=explode('|',$this->request->getPost('user'));
        //$nik=strtoupper(trim($splituser[0]));
        $iduser=$this->request->getPost('iduser');
        $username=strtoupper(trim($this->request->getPost('username')));
        $passcek=$this->request->getPost('passwordweb');
        $password=md5(md5(md5(strtoupper($this->request->getPost('passwordweb')))));
        $password2=md5(md5(md5(strtoupper($this->request->getPost('passwordweb2')))));
        $lvlid=strtoupper(trim($this->request->getPost('lvlid')));
        $lvlakses=strtoupper(trim($this->request->getPost('lvlakses')));
        $loccode=strtoupper(trim($this->request->getPost('loccode')));
        $kddept=strtoupper(trim($this->request->getPost('kddept')));

        $expdate=$this->request->getPost('expdate');
        $hold=$this->request->getPost('hold');
        $roleid=$this->request->getPost('roleid');
        $cek_user=$this->m_user->cek_user($username)->getNumRows();
        $builder = $this->db->table('sc_mst.user');
        if ($tipe==='input') {
            if ($cek_user>0 and ($password !== $password2)){
                $this->session->setFlashdata('message', 'Unable to process data is avaiable !!!');
                return redirect()->to(base_url('user'));
            } else {
                $info_input=array(
                    'branch'=>'JTS',
                    'nik' => '',
                    'username'=>$username,
                    'passwordweb'=>$password,
                    'level_id'=>$lvlid,
                    'level_akses'=>$lvlakses,
                    'expdate'=>date('Y-m-d',strtotime($expdate)),
                    'hold_id'=>$hold,
                    'image'=>'admin.jpg',
                    'kddept'=>$kddept,
                    'loccode'=>$loccode,
                    'roleid'=>$roleid,
                    'lang'=>'english',
                    'inputdate'=>date('Y-m-d'),
                    'inputby'=>$this->session->get('nama')
                );
                $builder->insert($info_input);

                $this->session->setFlashdata('message', 'Data Success Add !!!');
                return redirect()->to(base_url('user'));
            }
        } else if ($tipe==='edit'){
            if (empty($passcek) or $passcek===''){
                $info_edit1=array(
                    'expdate'=>date('Y-m-d',strtotime($expdate)),
                    'hold_id'=>$hold,
                    'level_id'=>$lvlid,
                    'level_akses'=>$lvlakses,
                    'kddept'=>$kddept,
                    'loccode'=>$loccode,
                    'roleid'=>$roleid,
                    'lang'=>'english',
                    'editdate'=>date('Y-m-d'),
                    'editby'=>$this->session->get('nama')
                );
                $builder->where('iduser',$iduser);
                $builder->where('username',$username);
                $builder->update($info_edit1);
                $this->session->setFlashdata('message', 'Data Success Update !!!');
                $enc_iduser = $this->fiky_encryption->sealed($iduser);
                $enc_username = $this->fiky_encryption->sealed($username);

                return redirect()->to(base_url("user/edit/$enc_iduser/$enc_username"));
            } else {
                $info_edit2=array(
                    'passwordweb'=>$password,
                    'expdate'=>date('Y-m-d',strtotime($expdate)),
                    'hold_id'=> $hold,
                    'level_id'=> $lvlid,
                    'level_akses'=> $lvlakses,
                    'kddept'=>$kddept,
                    'loccode'=> $loccode,
                    'roleid'=>$roleid,
                    'lang'=>'english',
                    'editdate'=> date('Y-m-d'),
                    'editby'=> trim($this->session->get('nik'))
                );
                $builder->where('iduser',$iduser);
                $builder->where('username',$username);
                $builder->update($info_edit2);

                $this->session->setFlashdata('message', 'Data Success Update !!!');
                $enc_iduser = $this->fiky_encryption->sealed($iduser);
                $enc_username = $this->fiky_encryption->sealed($username);
                return redirect()->to(base_url("user/edit/$enc_iduser/$enc_username"));
            }
        } else {
            $this->session->setFlashdata('message', 'Anda tidak berhak akses data ini !!!');
            return redirect()->to(base_url('user'));
        }


    }

}
