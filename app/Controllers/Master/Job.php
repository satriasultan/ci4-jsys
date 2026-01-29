<?php 

namespace App\Controllers\Master;

use App\Controllers\BaseController;

class Job extends BaseController
{
    public function job()
    {
        $data['title']="LIST JOB";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.M.B.6'; $versirelease='I.M.B.6/BETA.001'; $releasedate=date('2020-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.M.B.6'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUKSES DIPROSES $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        //auto insert unit
        // $this->m_job->q_autoinsert_unit();
        $roleid=trim($this->session->get('roleid'));
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('master/job/v_list_job',$data);
    }

    public function js_vtree_job_query()
    {
        $rows  = $this->m_job->getTreeData();

        $tree = [];

        foreach ($rows as $r) {
            $tree[] = [
                'id'   => $r->id,
                'pid'  => $r->pid,
                'name' => trim($r->id) . ' | ' . trim($r->name),
                'open' => ($r->pid === '0'), // auto open root
                // opsional
                // 'chkDisabled' => ($r->isDetail === 'false')
            ];
        }

        return $this->response->setJSON($tree);
    }

    public function get_job_detail()
    {
        $idbranch = $this->request->getPost('idbranch');
        
        $data['job'] = $this->m_job->getById($idbranch);
        
        $roleid = trim($this->session->get('roleid'));
        $kodemenu = 'I.M.B.6';
        $perm = $this->m_role->detail_user_akses($roleid, $kodemenu)->getRowArray();


        // fallback jika tidak ada record
        $data['perm'] = [
            'input'  => isset($perm['a_input'])  && trim($perm['a_input'])  === 't',
            'update' => isset($perm['a_update']) && trim($perm['a_update']) === 't',
            'delete' => isset($perm['a_delete']) && trim($perm['a_delete']) === 't',
        ];


        return view('master/job/job_form', $data);
    }


    public function saveJob()
    {
        $type  = $this->request->getPost('type');
        $idbranch = $this->request->getPost('idbranch');
        $nama  = trim($this->session->get('nama'));

        $builder = $this->db->table('sc_mst.branchjob');

        // if ($type === 'INPUT') {

        //     $idbranch = trim((string) $idbranch);

        //     $exists = $builder
        //     ->where("TRIM(idbranch) =", $idbranch)
        //     ->where("JobLESCE(TRIM(status), 'F') = 'F'", null, false)
        //     ->countAllResults();

        //     if ($exists > 0) {
        //         return $this->response->setJSON([
        //             'status'  => 'error',
        //             'message' => 'ID Job sudah ada'
        //         ]);
        //     }

        //     $data = [
        //         'idbranch'        => $idbranch,
        //         'nmbranch'        => strtoupper($this->request->getPost('nmbranch')),
        //         'keterangan'       => strtoupper($this->request->getPost('keterangan')),
        //         'induk'        => $this->request->getPost('induk'),
        //         'level'        => $this->request->getPost('level'),
        //         'penghubung'     => $this->request->getPost('penghubung'),
        //         'penghubung'     => strtoupper($this->request->getPost('penghubung')),
        //         'idcustomer'        => strtoupper($this->request->getPost('idcustomer')),
        //         'alamatcust'        => strtoupper($this->request->getPost('alamatcust')),
        //         'status'       => 'F',
        //         'createdby'    => $nama,
        //         'createddate'  => date('Y-m-d H:i:s')
        //     ];

        //     $builder->insert($data);
        // }

        if ($type === 'EDIT') {

            $data = [
                'nmbranch'        => strtoupper($this->request->getPost('nmbranch')),
                'keterangan'       => strtoupper($this->request->getPost('keterangan')),
                // 'induk'        => $this->request->getPost('induk'),
                // 'level'        => $this->request->getPost('level'),
                'penghubung'     => strtoupper($this->request->getPost('penghubung')),
                'idcustomer'        => strtoupper($this->request->getPost('idcustomer')),
                'alamatcust'        => strtoupper($this->request->getPost('alamatcust')),
                'manager'     => strtoupper($this->request->getPost('manager')),
                'datestart'     => date('Y-m-d',strtotime($this->request->getPost('datestart'))),
                'dateend'     => date('Y-m-d',strtotime($this->request->getPost('dateend'))),
                'persentase'     => ($this->request->getPost('persentase')),
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
            ];

            $builder->where('idbranch', $idbranch)->update($data);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data Job berhasil disimpan'
        ]);
    }


    public function delete_job()
    {
        $idbranch = $this->request->getPost('idbranch');
        $nama  = trim($this->session->get('nama'));

        if (!$idbranch) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ID Job tidak valid'
            ]);
        }

        // Cek apakah Job memiliki child (untuk keamanan)
        $builder = $this->db->table('sc_mst.job');
        
        // Cek apakah ada child records
        $hasChild = $builder->where('induk', $idbranch)->countAllResults();
        
        if ($hasChild > 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Job tidak dapat dihapus karena masih memiliki child Job'
            ]);
        }

        // Hard delete menggunakan delete()
        $deleted = $builder->where('idbranch', $idbranch)->delete();

        if ($deleted) {
            // Optional: Log aktivitas
            // $this->logActivity($nama, 'DELETE_Job', "Menghapus Job: $idbranch");
            
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Job berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus Job'
            ]);
        }
    }



}
?>