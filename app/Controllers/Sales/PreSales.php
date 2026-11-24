<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;

use function PHPUnit\Framework\isEmpty;

class PreSales extends BaseController
{

    public function taskmanagement()
    {
        $data['title']="Task Management";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.A.1'; $versirelease='I.S.A.1/01'; $releasedate=date('2024-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.A.1'";
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
        $pterror = " and userid='$nama'";
        //BAWA INI PENTING SEKALI DI SETIAP MENU
        $kmenu = 'I.S.A.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/presales/v_taskmanagement',$data);
    }

    function list_task()
    {
        $kmenu = 'I.S.A.1';
        $role = trim($this->session->get('roleid'));
        $nama = trim($this->session->get('nama'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();
        $bagian = trim($this->session->get('bagian'));

        $list = $this->m_sales->get_t_Task_view($nama, $role, $bagian);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $dropdown = '<div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" id="menu1_' . $no . '" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bars"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="menu1_' . $no . '" role="menu">';
            $dropdown .= '
                    <a class="dropdown-item bg-warning" href="#" onclick="kanban.editTask(\'' . trim($lm->id) . '\')">
                        <i class="fa fa-edit"></i> Edit Task
                    </a>
                    <a class="dropdown-item bg-danger" href="#" onclick="kanban.deleteTask(\'' . trim($lm->id) . '\')">
                        <i class="fa fa-trash"></i> Delete Task
                    </a>
                </div>
                        </div>';
            $row[] =  $dropdown ;
            $row[] = '<b> ' .$lm->id . '</b>';
            $row[] = $lm->title;
            $row[] = $lm->description;
            // $row[] = $lm->kddept;
            $priority = strtolower(trim($lm->priority)); // 'low', 'medium', 'high'
            $priorityLabel = ucfirst($priority); // untuk tampilkan huruf kapital: 'Low', 'Medium', dll

            // Gunakan class sesuai Kanban styling (misal pakai CSS .priority-low, .priority-high, dll)
            $row[] = '
            <span class="w-100 priority-badge priority-' . $priority . '">
                ' . $priorityLabel . '
            </span>';
            $row[] = $lm->assignee;
            $row[] = $lm->reportedto;
            $progress = (int)$lm->progress;

            // Optional: CSS class berdasarkan nilai
            $progressBarClass = 'bg-success';
            if ($progress < 25) {
                $progressBarClass = 'bg-danger';
            } elseif ($progress < 50) {
                $progressBarClass = 'bg-warning';
            } elseif($progress < 100) {
                $progressBarClass = 'bg-info';
            }

            $row[] = '
            <div class="progress-container">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar ' . $progressBarClass . '" role="progressbar"
                        style="width: ' . $progress . '%;" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">
                        ' . $progress . '%
                    </div>
                </div>
            </div>';

            $status = strtolower(trim($lm->status));
            $statusLabel = ucfirst($status);

            $badgeClass = 'secondary'; // default

            switch ($status) {
                case 'inprogress':
                    $badgeClass = 'warning';
                    break;
                case 'review':
                    $badgeClass = 'primary';
                    break;
                case 'done':
                    $badgeClass = 'success';
                    break;
                case 'todo':
                default:
                    $badgeClass = 'secondary';
                    break;
            }

            $row[] = '<span class="w-100 badge bg-' . $badgeClass . ' status-badge">' . $statusLabel . '</span>';
            $row[] = $lm->inputby;
            $row[] = $lm->inputdate;
            $row[] = $lm->updateby;
            $row[] = $lm->updatedate;

            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_sales->t_Task_view_count_all($nama, $role, $bagian),
            "recordsFiltered" => $this->m_sales->t_Task_view_count_filtered($nama, $role, $bagian),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    public function list_task_board()
    {
        if ($this->request->isAJAX()) {
            $kmenu = 'I.S.A.1';
            $role = trim($this->session->get('roleid'));
            $nama = trim($this->session->get('nama'));
            $db = \Config\Database::connect();

            $userinfo = $this->m_user->getUser(" and username='$nama'")->getRowArray();
            $bagian = trim($this->session->get('bagian'));

            $list = $this->m_sales->get_list_task_board($nama, $role, $bagian);

            $tasks = [];
            foreach ($list as $lm) {
                // 🔹 Ambil semua subtask berdasarkan task_id
                $subtasks = $db->table('sc_trx.tasksales_detail')
                    ->where('task_id', $lm->id)
                    ->orderBy('id', 'asc')
                    ->get()
                    ->getResultArray();

                $tasks[] = [
                    'id'          => $lm->id,
                    'title'       => $lm->title,
                    'description' => $lm->description,
                    'assignee'    => $lm->assignee,
                    'priority'    => $lm->priority,
                    'progress'    => (int)$lm->progress,
                    'status'      => strtolower(trim($lm->status)),
                    'createdAt'   => $lm->inputdate,
                    'reportedto'  => $lm->reportedto,
                    'subtasks'    => $subtasks // langsung array hasil query
                ];
            }


            return $this->response->setJSON([
                'status' => true,
                'tasks' => $tasks
            ]);
        }

        return $this->response->setStatusCode(405);
    }

    public function addTask()
    {
        $json = $this->request->getJSON(true);
        $db = \Config\Database::connect();
        $nama = trim($this->session->get('nama'));
        $db->transStart();

        $data = [
            'title'       => $json['title'] ?? '',
            'description' => $json['description'] ?? '',
            'assignee'    => $json['assignee'] ?? '',
            'priority'    => $json['priority'] ?? '',
            'progress'    => $json['progress'] ?? 0,
            'status'      => $json['status'] ?? 'todo',
            'inputby'     => $nama,
            'reportedto'  => $json['reporter'] ?? null
        ];

        $db->table('sc_trx.tasksales')->insert($data);

        // Ambil kembali id yang baru saja dibuat
        $task = $db->table('sc_trx.tasksales')
            ->select('id')
            ->where('inputby', $nama)
            ->orderBy('inputdate', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        $taskCode = $task->id ?? null;

        if (!empty($json['subtasks'])) {
            $detailBuilder = $db->table('sc_trx.tasksales_detail');
            foreach ($json['subtasks'] as $sub) {
                $detailBuilder->insert([
                    'task_id'   => $taskCode,
                    'sub_title' => $sub['sub_title'],
                    'is_done'   => $sub['is_done'] ?? false,
                    'inputby'   => $nama
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus()) {
            return $this->response->setJSON([
                'success' => true,
                'id' => $taskCode
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save task or subtasks'
            ]);
        }
    }


    public function updateTask()
    {
        $db = \Config\Database::connect();
        $nama = trim($this->session->get('nama'));
        $json = $this->request->getJSON(true); // ambil JSON sebagai array

        $id         = $json['id'] ?? null;
        $title      = $json['title'] ?? '';
        $description= $json['description'] ?? '';
        $assignee   = $json['assignee'] ?? '';
        $priority   = $json['priority'] ?? '';
        $progress   = $json['progress'] ?? 0;
        $reportedto = $json['reporter'] ?? '';
        $subtasks   = $json['subtasks'] ?? [];

        if (!$id || !$title) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID atau judul task tidak valid.'
            ]);
        }

        $db->transBegin();

        try {
            // 1️⃣ Update task utama
            $db->table('sc_trx.tasksales')
                ->where('id', $id)
                ->update([
                    'title'       => $title,
                    'description' => $description,
                    'assignee'    => $assignee,
                    'priority'    => $priority,
                    'progress'    => $progress,
                    'reportedto'  => $reportedto,
                    'updatedate'  => date('Y-m-d H:i:s'),
                    'updateby'    => $nama
                ]);

            // 2️⃣ Hapus subtask lama
            $db->table('sc_trx.tasksales_detail')->where('task_id', $id)->delete();

            // 3️⃣ Insert ulang subtask baru
            foreach ($subtasks as $st) {
                $db->table('sc_trx.tasksales_detail')->insert([
                    'task_id'   => $id,
                    'sub_title' => $st['sub_title'],
                    'is_done'   => $st['is_done'] ? 't' : 'f',
                    'inputby'   => $nama
                ]);
            }

            $db->transCommit();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Task berhasil diperbarui'
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }






    //PENAWARAN HARGA


    public function offering()
    {
        $data['title']="Penawaran Harga";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.A.2'; $versirelease='I.S.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_presales->q_offering_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/presales/clearEntryOffering');
            $urlnext = base_url('sales/presales/addOffering');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/presales/v_list_offering',$data);
    }

    function detailOffering()
    {
        /* Penambahan Squence */
        $data['title']="Detail Penawaran Harga";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/presales/offering'));
        }
        $kodemenu='I.S.A.2'; $versirelease='I.S.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc){
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_presales->q_offering_master($param)->getRowArray();
        return $this->template->render('sales/presales/v_detail_offering',$data);
    }

    function list_offering(){
        $list = $this->m_presales->get_t_front_offering_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = '<div class="dropdown">
            //                 <button class="btn btn-primary btn-sm dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-bars"></i>
            //                     <span class="caret"></span></button>
            //                     <div class="dropdown-menu" role="menu">
            //                         <a style="background-color: #3badf6;"class="dropdown-item" href=' . "'" . base_url('sales/presales/offering/updateOffering') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Update This offering : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-bars"></i> Update Penawaran Harga </a>
            //                         <a style="background-color: #00ff8e;" class="dropdown-item" href=' . "'" . base_url('sales/presales/offering/show_offering') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Print This Data Detail : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-eye"></i> Print Penawaran Harga </a>
            //                         <a style="background-color: red;" class="dropdown-item" href=' . "'" . base_url('sales/presales/offering/deleteOffering') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Remove this offering : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-trash"></i> Delete Penawaran Harga </a>                      
            //                     </div>
            //             </div>
            // ';
            $updateBtn = '<a class="dropdown-item bg-warning" 
                href="' . base_url('sales/presales/updateOffering') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'Update This offering : ' . trim($lm->docno) . '\')">
                <i class="fa fa-edit"></i> Update Penawaran Harga 
            </a>';

            $detailBtn = '<a style="background-color: #3badf6;" class="dropdown-item" 
                href="' . base_url('sales/presales/detailOffering') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'View This Detail offering : ' . trim($lm->docno) . '\')">
                <i class="fa fa-eye"></i> Detail Penawaran Harga 
            </a>';

            $printBtn = '<a style="background-color: #00ff8e;" class="dropdown-item" 
                            href="' . base_url('sales/presales/show_offering') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Print This Data Detail : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-print"></i> Print Penawaran Harga 
                        </a>';

            $deleteBtn = '<a class="dropdown-item bg-danger" 
                            href="' . base_url('sales/presales/deleteOffering') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Cancel this offering : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-trash"></i> Cancel Penawaran Harga 
                        </a>';

            $dropdownMenu = '<div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" 
                                    id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false">
                                    <i class="fa fa-bars"></i><span class="caret"></span>
                                </button>
                                <div class="dropdown-menu" role="menu">';

            if (strtoupper($lm->status_desc) !== 'CETAK/PRINT' && strtoupper($lm->status_desc) !== 'CANCEL') {
                // Jika status CETAK/PRINT atau CANCEL, tampilkan semua tombol
                $dropdownMenu = '<div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" 
                                        id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false">
                                        <i class="fa fa-bars"></i><span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">' .
                    $updateBtn . $printBtn . $deleteBtn . $detailBtn . '</div>
                                </div>';
            } else {
                // Jika bukan CETAK/PRINT atau CANCEL, hanya tampilkan tombol Detail
                $dropdownMenu = '<div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" 
                                        id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false">
                                        <i class="fa fa-bars"></i><span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">' . $detailBtn . '</div>
                                </div>';
            }



            // $dropdownMenu .= $deleteBtn . '</div></div>';

            $row[] = $dropdownMenu;
            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = '<span style="font-weight:bold" >' . $lm->rolejob . '</span>';
            $row[] = $lm->nmcust;
            $row[] = $lm->address;
            // $row[] = $lm->linksteelgrade;
            $row[] = $lm->phone;
            $row[] = $lm->fax;
            $row[] = $lm->up;

            // $row[] = $lm->status_desc ?? $lm->status;
            $status = $lm->status_desc ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'REVISION/EDITING':
                    $badgeClass = 'badge-warning';
                    break;
                case 'FINAL USER':
                    $badgeClass = 'badge-info';
                    break;
                case 'DITARIK PO':
                    $badgeClass = 'badge-success';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-cetak';
                    break;
                case 'CANCELED':
                    $badgeClass = 'badge-danger ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<span style="font-size:12px" class="badge ' . $badgeClass . '">' . htmlspecialchars($status) . '</span>';
            $row[] = $lm->inputby;
            $row[] = !empty($lm->inputdate) ? date('d-m-Y H:i:s', strtotime($lm->inputdate)) : null;

            $row[] = $lm->printby;
            $row[] = !empty($lm->printdate) ? date('d-m-Y H:i:s', strtotime($lm->printdate)) : null;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_presales->t_front_offering_view_count_all(),
            "recordsFiltered" => $this->m_presales->t_front_offering_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryOffering()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_presales->q_offering_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/presales/offering'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.offering');
        $builder_dtl = $this->db->table('sc_tmp.offeringdtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.offering');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/presales/offering'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/presales/offering'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('sales/presales/offering'));
        }

    }

    function addOffering()
    {
        /* Penambahan Squence */
        $data['title']="Input Penawaran Harga";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.A.2'; $versirelease='I.S.A.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.A.2'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_presales->q_offering_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_presales->q_offering_master_temp($param)->getRowArray();

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/presales/v_add_offering',$data);
    }

    function showing_cc($id){
        $data = $this->m_manufacture->q_t_CC(" and idchemicalcomposition='$id'")->getRow();
        echo json_encode($data);
    }

    function updateOffering()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_presales->q_offering_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.offering');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/presales/addOffering'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/presales/offering'));
        }
    }

    function saveOffering(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $docref = strtoupper($this->request->getPost('docref'));
        // $penerima = strtoupper($this->request->getPost('penerima'));
        $docno = strtoupper($this->request->getPost('docno'));
        $docdate = strtoupper($this->request->getPost('docdate'));
        $cust = strtoupper($this->request->getPost('cust'));
        $phone = strtoupper($this->request->getPost('phone'));
        $fax = strtoupper($this->request->getPost('fax'));
        $up = strtoupper($this->request->getPost('up'));
        $rolejob = strtoupper($this->request->getPost('rolejob'));
        $address = strtoupper($this->request->getPost('address'));
        $description = strtoupper($this->request->getPost('desc'));

        // $dateout = strtoupper($this->request->getPost('dateout'));
        // $nopol = strtoupper($this->request->getPost('nopol'));
        // $isreturn = ($this->request->getPost('isreturn'));
        // $datereturn = strtoupper($this->request->getPost('datereturn'));
        // $tujuan = ($this->request->getPost('tujuan'));
        // $jenisbarang = ($this->request->getPost('jenisbarang'));
        // $baranglain = strtoupper($this->request->getPost('baranglain'));
        $countx = $this->m_presales->q_offering_master_temp(" and trim(inputby)='$nama'")->getNumRows();

        // if ($isreturn === 'kembali' && empty($datereturn)) {
        //     return redirect()->to(base_url('sales/presales/addOffering'))
        //         ->with('error', 'Return date is required when selecting "Kembali".');
        // }

        // if ($jenisbarang === 'lainlain' && empty($baranglain)) {
        //     return redirect()->to(base_url('sales/presales/addOffering'))
        //         ->with('error', 'Other goods description is required when selecting "Lain-lain".');
        // }


        if (empty($countx)) {
            $info = array (
                'docno' => $docno,
                // 'penerima' => $penerima,
                // 'doctype' => $doctype,
                // 'docdate' => date('Y-m-d'),
                'docdate' => $docdate,
                // 'dateout' => $dateout,
                'cust' => $cust,
                'phone' => $phone,
                'fax'=>$fax,
                'up'=>$up,
                'address'=>$address,
                'rolejob' => $rolejob,
                // 'datereturn' => (!empty($datereturn) ? date('Y-m-d', strtotime($datereturn)) : null),
                // 'baranglain'=>$baranglain,
                // 'docdate' => date('Y-m-d'),
                'status' => 'E',
                'description' => $description,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.offering');
            $builder->where('docno',$docno);
            $builder->insert($info);
            return redirect()->to(base_url('sales/presales/addOffering'));
        } else {
            /*RETURN FAILED*/
            return redirect()->to(base_url('sales/presales/addOffering'));
        }

    }

    function showing_offeringtrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_presales->q_offering_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_offeringtemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_presales->q_offering_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_offering_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_presales->q_offering_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    public function insert_detail_offering()
    {
        // Ambil data dari session
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        // Ambil body request dalam bentuk JSON
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        // Validasi apakah request memiliki key yang benar
        if (!isset($data->key) || $data->key !== '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid Request Key!'
            ]);
        }

        // Ambil data dari body JSON
        $dataprocess = $data->body;
        $idunit = isset($dataprocess->idunit) ? trim($dataprocess->idunit) : null;
        $namabarang = isset($dataprocess->namabarang) ? trim($dataprocess->namabarang) : null;
        $qty = isset($dataprocess->qty) ? trim($dataprocess->qty) : null;
        $description = isset($dataprocess->description) ? trim($dataprocess->description) : null;

        // Validasi data tidak boleh kosong
        if (empty($idunit) || empty($namabarang) || empty($qty) || empty($description)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please fill all required fields (Unit, Item Name, Qty, Description).'
            ]);
        }

        // Data untuk disimpan ke database
        $data_insert = [
            'docno' => $inputby,
            'unit' => $idunit,
            'namabarang' => $namabarang,
            'qty' => $qty,
            'description' => $description,
            'inputby' => $inputby,
            'inputdate' => $inputdate,
            'status' => 'I'
        ];

        // Insert ke database
        $builder = $this->db->table('sc_tmp.offeringdtl'); // Sesuaikan dengan tabel Anda
        $insert = $builder->insert($data_insert);

        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Detail successfully inserted!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to insert data. Please try again.'
            ]);
        }
    }

    public function insertNewOffering()
    {
        // Ambil data dari session
        $nama = trim($this->session->get('nama'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        // Ambil body request dalam bentuk JSON
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        // Validasi apakah request memiliki key yang benar
        if (!isset($data->key) || $data->key !== '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid Request Key!'
            ]);
        }

        // Ambil docno dari body
        $docno = isset($data->body->docno) ? trim($data->body->docno) : '';

        if ($docno === '') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'docno is required!'
            ]);
        }
        // Data untuk disimpan ke database
        $data_insert = [
            'docno' => $docno,
            'inputby' => $inputby,
            'inputdate' => $inputdate,
            // 'exchange' => 
            'status' => 'I' // Status awal Insert
        ];

        // Insert ke database
        $builder = $this->db->table('sc_tmp.offeringdtl');
        $insert = $builder->insert($data_insert);

        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Penawaran Harga successfully created!',
                'docno' => $docno
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create Penawaran Harga. Please try again.'
            ]);
        }
    }




    public function update_detail_offering()
    {
        $nama = trim($this->session->get('nama')); // Mengambil docno dari session
        $request_body = file_get_contents('php://input');
        $data = $this->request->getJSON(); // Mengambil data JSON langsung
        $updateby = $nama;
        $updatedate = date('Y-m-d H:i:s');

        // Ambil data POST (dari frontend)
        $updates = $this->request->getPost('updates');
        $masterData = $this->request->getPost('masterData'); // Ambil data master dari POST


        $response = [];

        if (!empty($updates)) {
            foreach ($updates as $update) {
                $idurut = $update['idurut'] ?? null;
                $idbarang = trim($update['idbarang']) ?? '';
                $nmbarang = trim($update['nmbarang']) ?? '';
                $idunit = $update['idunit'] ?? '';
                $qty = $update['qty'] ?? 0;
                $price = $update['price'] ?? 0;
                $usdmt = $update['usdmt'] ?? 0;
                $exchange = $update['exchange'] ?? 0;

                $description = strtoupper($update['description']) ?? '';

                if (empty($idurut)) {
                    continue; // Skip jika idurut kosong
                }

                // Data yang akan diupdate
                $infoupdate = [
                    'idbarang' => $idbarang,
                    'nmbarang' => $nmbarang,
                    'unit' => $idunit,
                    'qty' => $qty,
                    'price' => $price,
                    'exchange' => $exchange,
                    'usdmt' => $usdmt,

                    'description' => $description,
                    'status' => 'F',
                    'updateby' => $updateby,
                    'updatedate' => $updatedate,
                ];

                // Update berdasarkan idurut dan docno = nama
                $builder = $this->db->table('sc_tmp.offeringdtl');
                $builder->where('idurut', $idurut);
                $builder->where('inputby', $nama);
                $builder->where('docno', trim($masterData['docno']));

                if ($builder->update($infoupdate)) {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => true,
                        'message' => 'Update successful'
                    ];
                } else {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => false,
                        'message' => 'Update failed'
                    ];
                }
            }

            // Berikan respons ke frontend
            echo json_encode([
                'status' => 'success',
                'message' => 'Data updated successfully',
                'results' => $response
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No updates provided'
            ]);
        }
    }


    public function get_itemsales()
    {
        $suppliers = $this->m_presales->get_itemsales(); // Fungsi di model untuk ambil data supplier
        // Format data yang dikembalikan dalam bentuk yang sesuai dengan Select2
        $result = [];
        foreach ($suppliers as $supplier) {
            $result[] = [
                'idsupplier' => $supplier->idsupplier,
                'nmsupplier' => $supplier->nmsupplier,
                'text' => $supplier->idsupplier . ' <=> ' . $supplier->nmsupplier,
            ];
        }

        echo json_encode(['items' => $result]);
    }

    function list_t_offering_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_presales->get_t_offering_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '<select disabled class="idbarang-dropdown" style="width: 100%; height: 20px!important; font-size: 12px;" data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') .
                ' <i class="fa fa-circle text-success" style="font-size:8px;"></i> ' .
                htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') .
                '</option>
            </select>';


            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
            <option value="" disabled>-- Choose --</option>
            <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
            </select>';
            //qty
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 2, ',', '.').'" disabled  min="0">';

            //price
            // Price dengan Rp sejajar
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                <input class="form-control ratakanan jtsseparator" 
                    style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="price_'.$lm->idurut.'" 
                    name="price_'.$lm->idurut.'" 
                    value="'.number_format($lm->price, 2, ',', '.').'" 
                    disabled min="0">
            </div>';

            //exchange
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                    <input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" 
                    type="text"  
                    id="exchange_'.$lm->idurut.'" 
                    name="exchange_'.$lm->idurut.'" 
                    value="'.number_format($lm->exchange, 2, ',', '.').'" 
                    disabled  min="0">
            </div>';
            //usdmt
            // USDMT dengan $
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">$</span>
                <input class="form-control ratakanan jtsseparator" 
                    style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="usdmt_'.$lm->idurut.'" 
                    name="usdmt_'.$lm->idurut.'" 
                    value="'.number_format($lm->usdmt, 2, ',', '.').'" 
                    disabled min="0">
            </div>';

            //description
            $row[] = '<input class="form-control "   style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->description, ENT_QUOTES, 'UTF-8') . '" disabled >';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_presales->t_offering_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_presales->t_offering_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_t_offering_dtltrx(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_presales->get_t_offering_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '<select disabled class="idbarang-dropdown" style="width: 100%; height: 20px!important; font-size: 12px;" data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') .
                ' <i class="fa fa-circle text-success" style="font-size:8px;"></i> ' .
                htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') .
                '</option>
            </select>';


            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
            <option value="" disabled>-- Choose --</option>
            <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
            </select>';
            //qty
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 2, ',', '.').'" disabled  min="0">';

            //price
            // Price dengan Rp sejajar
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                <input class="form-control ratakanan jtsseparator" 
                    style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="price_'.$lm->idurut.'" 
                    name="price_'.$lm->idurut.'" 
                    value="'.number_format($lm->price, 2, ',', '.').'" 
                    disabled min="0">
            </div>';

            //exchange
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                    <input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" 
                    type="text"  
                    id="exchange_'.$lm->idurut.'" 
                    name="exchange_'.$lm->idurut.'" 
                    value="'.number_format($lm->exchange, 2, ',', '.').'" 
                    disabled  min="0">
            </div>';
            //usdmt
            // USDMT dengan $
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">$</span>
                <input class="form-control ratakanan jtsseparator" 
                    style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="usdmt_'.$lm->idurut.'" 
                    name="usdmt_'.$lm->idurut.'" 
                    value="'.number_format($lm->usdmt, 2, ',', '.').'" 
                    disabled min="0">
            </div>';

            //description
            $row[] = '<input class="form-control "   style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->description, ENT_QUOTES, 'UTF-8') . '" disabled >';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_presales->t_offering_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_presales->t_offering_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryOffering(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '')";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_presales->q_offering_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_presales->q_offering_dtl_temp($paramdtl);
        $cek2 = $this->m_presales->q_offering_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.offering');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.A.2');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.A.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/presales/addOffering'));
        } else {
            // Ambil dari request POST
            $brand       = strtoupper(trim($this->request->getPost('brand')));
            $size        = strtoupper(trim($this->request->getPost('size')));
            $qty         = strtoupper(trim($this->request->getPost('qty')));
            $pembayaran  = strtoupper(trim($this->request->getPost('pembayaran')));
            $pengiriman  = strtoupper(trim($this->request->getPost('pengiriman')));
            $expdateph   = trim($this->request->getPost('expdateph')); // format dd-mm-yyyy
            $ketentuan   = strtoupper(trim($this->request->getPost('ketentuan')));

            // Convert expdate ke format YYYY-MM-DD
            $expdate = null;
            if (!empty($expdateph)) {
                $expdate = date('Y-m-d', strtotime(str_replace('-', '/', $expdateph)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'brand'      => $brand,
                'size'       => $size,
                'qty'        => $qty,
                'pembayaran' => $pembayaran,
                'pengiriman' => $pengiriman,
                'expdate'    => $expdate,
                'ketentuan'  => $ketentuan
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.S.A.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/sales/presales/offering'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.S.A.2',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/presales/addOffering'));
            }



        }

    }

    function deleteOffering(){
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $builder = $this->db->table('sc_trx.offering');

        // Update status menjadi 'C'
        $iupdate = array('status' => 'C');
        $builder->where('docno', $docno);
        if ($builder->update($iupdate)) {
            // // Hapus data dari tabel offering dan offeringdtl sesuai docno dan status 'C'
            // $builder->where('docno', $docno); 
            // $builder->where('status', 'C');
            // $builder->delete();

            // // Menghapus data dari offeringdtl
            // $this->db->table('sc_trx.offeringdtl')
            //         ->where('docno', $docno)
            //         ->where('inputby', $nama)
            //         ->delete();

            return redirect()->to(base_url('sales/presales/offering') . '?status=success&message=Successfully data Canceled');
        } else {
            return redirect()->to(base_url('sales/presales/offering') . '?status=error&message=No updates provided or operation failed');
        }

    }


    function deleteOfferingDtl(){
        $id = $this->request->getPost('id'); // Ambil array ID
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getPost('docno'); // Ambil docno yang dikirim dari AJAX

        if (empty($id) || empty($docno)) {
            echo json_encode(['status' => false, 'messages' => 'Missing Parameters']);
            return;
        }


        // $idurut = $id[0];
        $iupdate = array(
            'status' => 'C',
        );
        $builder = $this->db->table('sc_tmp.offeringdtl');
        $builder->where('inputby',$nama);
        $builder->where('docno',$docno);
        $builder->whereIn('idurut',$id);

        if ($builder->update($iupdate)) {
            $builder->where('docno',$docno);
            $builder->where('inputby',$nama);
            $builder->whereIn('idurut',$id);
            $builder->where('status','C');

            $builder->delete();

            $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: ');
            echo json_encode($getResult);
        } else {
            $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
            echo json_encode($getResult);
        }

    }

    function show_offering(){
        $module = 'I.S';
        $menu = 'I.S.A.3';
        $table = 'sc_trx.offering';
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.offering');

        //    $builder = $builder
        //         ->where('docno', $docno)
        //         ->update([
        //             'status'=> 'P',
        //             'printby' => $nama,
        //             'printdate' => date('Y-m-d H:i:s')
        //         ]);


        $enc_docno = $this->fiky_encryption->sealed($docno);

        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Report Penawaran Harga";

        //$datajson =  base_url("manufactur/production/api_offering/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/presales/api_offering/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_offering.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_offering_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_offering(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docno')));
        //$docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        // $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        // $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

        // $ddate = explode(' - ',$docdate);
        // $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        // $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($docno) or $docno==='') {
            $param_brg = "";
        } else {
            $param_brg = " and docno='$docno'";
        }

        // //idgroup
        // if (!empty($idgroup)) {
        //     $param_group=" and idgroup='$idgroup'";
        // } else {  $param_group=""; }


        $databranch = $this->m_global->q_master_branch();
        $param=" and docno='$docno'";
        $datamst = $this->m_presales->q_offering_master($param);
        $datadtl = $this->m_presales->q_offering_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;
        if ($detail) {

            $tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';

            // Tambahkan properti baru isPindah
            $detail->isPindah = false; // Default value
            if ($tujuan === 'pindah') {
                $detail->isPindah = true;
            }

            // Tambahkan properti baru isPembuangan
            $detail->isPembuangan = false; // Default value
            if ($tujuan === 'pembuangan') {
                $detail->isPembuangan = true;
            }

            // Tambahkan properti baru isPinjam
            $detail->isPinjam = false; // Default value
            if ($tujuan === 'pinjam') {
                $detail->isPinjam = true;
            }

            $isreturn = isset($detail->isreturn) ? trim($detail->isreturn) : '';
            // Tambahkan properti baru iskembali
            $detail->iskembali = false; // Default value
            if ($isreturn === 'kembali') {
                $detail->iskembali = true;
            }

            $detail->istidakkembali = false; // Default value
            if ($isreturn === 'tidak_kembali') {
                $detail->istidakkembali = true;
            }

            $jenisbarang = isset($detail->jenisbarang) ? trim($detail->jenisbarang) : '';
            // Tambahkan properti baru isAset
            $detail->isAset = false; // Default value
            if ($jenisbarang === 'aset') {
                $detail->isAset = true;
            }

            // Tambahkan properti baru isPersediaan
            $detail->isPersediaan = false; // Default value
            if ($jenisbarang === 'persediaan') {
                $detail->isPersediaan = true;
            }

            // Tambahkan properti baru isLainlain
            $detail->isLainlain = false; // Default value
            if ($jenisbarang === 'lainlain') {
                $detail->isLainlain = true;
            }
        }

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    //'date1' => date('d-m-Y',strtotime($tgl1)),
                    //'date2' => date('d-m-Y',strtotime($tgl2)),
                    'date1' => date('d-m-Y'),
                    'date2' => date('d-m-Y'),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,
                    'param' => $param,

                ]
                ),
                'branch' => $databranch->getResult(),
                'master' => $datamst->getResult(),
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    public function getRolePO()
    {
        $jobcode = trim($this->request->getGet('rolejob'));
        $codemenu = 'I.S.A.2';
        $logindate = trim($this->session->get('logindate')); // format: dd-mm-yyyy

        // Buat infix dari logindate
        $infix = '';
        if (!empty($logindate)) {
            $ts = strtotime($logindate);
            $infix = date('ym', $ts); // contoh: 2508
        }

        // Mapping prefix default
        $prefixMap = [
            'JTS'  => 'JTS-PH',
            'MSMI' => 'MSMI-PH',
            'MSMJ' => 'MSM-PH'
        ];
        $prefix = isset($prefixMap[$jobcode]) ? $prefixMap[$jobcode] : '';

        if (empty($prefix) || empty($infix)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role/Job atau logindate tidak valid'
            ]);
        }

        // Ambil suffix terakhir dari docno di sc_trx.offering
        $builder = $this->db->table('sc_trx.offering');
        $builder->select("docno");
        $builder->like('docno', $prefix . '/' . $infix . '/', 'after');
        $builder->orderBy('docno', 'DESC');
        $builder->limit(1);
        $row = $builder->get()->getRowArray();

        if (!empty($row['docno'])) {
            $parts = explode('/', $row['docno']);
            $lastSuffix = isset($parts[2]) ? (int)$parts[2] : 0;
            $newSuffix = str_pad($lastSuffix + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newSuffix = '000001';
        }

        return $this->response->setJSON([
            'success' => true,
            'prefix'  => $prefix,
            'infix'   => $infix,
            'suffix'  => $newSuffix
        ]);
    }


    /*



    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE

    SECTION PROFORMA INVOICE


        */



    public function proforma()
    {
        $data['title']="Proforma Invoice & Invoicing";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.A.3'; $versirelease='I.S.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.A.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }
        /* Item Entry Master Check */
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_presales->q_proforma_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/presales/clearEntryProforma');
            $urlnext = base_url('sales/presales/addProforma');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/presales/v_list_proforma',$data);
    }

    function detailProforma()
    {
        /* Penambahan Squence */
        $data['title']="Detail Proforma Invoice";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/presales/performainvoice'));
        }
        $kodemenu='I.S.A.3'; $versirelease='I.S.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.A.3'";
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

        $decoded_docno = hex2bin($docno); // Decode docno yang dikirim dalam bentuk hex
        $param = " and coalesce(docno,'') = '$decoded_docno'";
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_presales->q_proforma_master($param)->getRowArray();
        return $this->template->render('sales/presales/v_detail_proforma',$data);
    }

    function list_proforma(){
        $nama = trim((string) $this->session->get('nama'));
        $role = trim((string) $this->session->get('roleid'));
        $akses = $this->m_role->detail_user_akses($role, 'I.S.A.3')->getRowArray();
        $canUpdate = trim((string) ($akses['a_update'] ?? '')) === 't';

        $list = $this->m_presales->get_t_front_proforma_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $canModify = $canUpdate && trim((string) $lm->inputby) === $nama;
            // $row[] = '<div class="dropdown">
            //                 <button class="btn btn-primary btn-sm dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-bars"></i>
            //                     <span class="caret"></span></button>
            //                     <div class="dropdown-menu" role="menu">
            //                         <a style="background-color: #3badf6;"class="dropdown-item" href=' . "'" . base_url('sales/presales/performainvoice/updateProforma') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Update This proforma : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-bars"></i> Update Penawaran Harga </a>
            //                         <a style="background-color: #00ff8e;" class="dropdown-item" href=' . "'" . base_url('sales/presales/performainvoice/show_proforma') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Print This Data Detail : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-eye"></i> Print Penawaran Harga </a>
            //                         <a style="background-color: red;" class="dropdown-item" href=' . "'" . base_url('sales/presales/performainvoice/deleteProforma') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Remove this proforma : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-trash"></i> Delete Penawaran Harga </a>                      
            //                     </div>
            //             </div>
            // ';
            $updateBtn = '<a class="dropdown-item bg-warning" 
                href="' . base_url('sales/presales/updateProforma') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'Update This proforma : ' . trim($lm->docno) . '\')">
                <i class="fa fa-edit"></i> Update Proforma Invoice 
            </a>';

            $detailBtn = '<a style="background-color: #3badf6;" class="dropdown-item" 
                href="' . base_url('sales/presales/detailProforma') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'View This Detail proforma : ' . trim($lm->docno) . '\')">
                <i class="fa fa-eye"></i> Detail Proforma Invoice 
            </a>';

            $printBtn = '<a style="background-color: #00ff8e;" class="dropdown-item" 
                            href="' . base_url('sales/presales/show_proforma') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Print This Data Detail : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-print"></i> Print Proforma Invoice 
                        </a>';

            $deleteBtn = '<a class="dropdown-item bg-danger" 
                            href="' . base_url('sales/presales/deleteProforma') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Cancel this proforma : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-trash"></i> Cancel Proforma Invoice 
                        </a>';

            $dropdownMenu = '<div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" 
                                    id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false">
                                    <i class="fa fa-bars"></i><span class="caret"></span>
                                </button>
                                <div class="dropdown-menu" role="menu">';

            $statusDesc = strtoupper(trim((string) ($lm->status_desc ?? $lm->status ?? '')));

            if (!in_array($statusDesc, ['CETAK/PRINT', 'CANCEL', 'CANCELED'], true)) {
                $actionButtons = $detailBtn . $printBtn;
                if ($canModify) {
                    $actionButtons = $updateBtn . $printBtn . $deleteBtn . $detailBtn;
                }

                $dropdownMenu = '<div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" 
                                        id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false">
                                        <i class="fa fa-bars"></i><span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">' .
                    $actionButtons . '</div>
                                </div>';
            } else {
                // Status CETAK/PRINT atau CANCEL: hanya Detail.
                $dropdownMenu = '<div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" style="margin:0px; color:#FFFFFF;" 
                                        id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false">
                                        <i class="fa fa-bars"></i><span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">' . $detailBtn . '</div>
                                </div>';
            }



            // $dropdownMenu .= $deleteBtn . '</div></div>';

            $row[] = $dropdownMenu;
            $row[] = $lm->docno;
            $row[] = $lm->docdate;
            $row[] = '<span style="font-weight:bold">' . $lm->rolejob . '</span>';
            $row[] = $lm->jnsinvoice;
            $row[] = $lm->nmcust;
            $row[] = $lm->address;
            // $row[] = $lm->linksteelgrade;
            $row[] = $lm->phone;
            $row[] = $lm->fax;
            // $row[] = $lm->up;

            // $row[] = $lm->status_desc ?? $lm->status;
            $status = $lm->status_desc ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'REVISION/EDITING':
                    $badgeClass = 'badge-warning';
                    break;
                case 'FINAL USER':
                    $badgeClass = 'badge-info';
                    break;
                case 'DITARIK PO':
                    $badgeClass = 'badge-success';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-cetak';
                    break;
                case 'CANCELED':
                    $badgeClass = 'badge-danger ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<span style="font-size:12px" class="badge ' . $badgeClass . '">' . htmlspecialchars($status) . '</span>';
            $row[] = $lm->inputby;
            $row[] = !empty($lm->inputdate) ? date('d-m-Y H:i:s', strtotime($lm->inputdate)) : null;

            $row[] = $lm->printby;
            $row[] = !empty($lm->printdate) ? date('d-m-Y H:i:s', strtotime($lm->printdate)) : null;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_presales->t_front_proforma_view_count_all(),
            "recordsFiltered" => $this->m_presales->t_front_proforma_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryProforma()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_presales->q_proforma_master_temp($param);
        // if(isEmpty($dtl->getRowArray())){
        //     return redirect()->to(base_url('sales/presales/offering'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.proforma');
        $builder_dtl = $this->db->table('sc_tmp.proformadtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.proforma');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/presales/performainvoice'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/presales/performainvoice'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            echo json_encode($result);
        }

    }

    function addProforma()
    {
        /* Penambahan Squence */
        $data['title']="Input Proforma Invoice";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.A.3'; $versirelease='I.S.A.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.A.3'";
        $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
        $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();
        if(isset($dtlerror['description'])) { $errordesc=trim($dtlerror['description']); } else { $errordesc='';  }
        if(isset($dtlerror['nomorakhir1'])) { $nomorakhir1=trim($dtlerror['nomorakhir1']); } else { $nomorakhir1='';  }
        if(isset($dtlerror['errorcode'])) { $errorcode=trim($dtlerror['errorcode']); } else { $errorcode='';  }

        if($count_err>0 and $errordesc<>''){
            if ($dtlerror['errorcode']==0){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="<div class='alert alert-info'>$errordesc</div>";
            }

        }else {
            if ($errorcode=='0'){
                $data['message']="<div class='alert alert-info'>DATA SUCCESSFULLY PROCESSED $nomorakhir1 </div>";
            } else {
                $data['message']="";
            }

        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_presales->q_proforma_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_presales->q_proforma_master_temp($param)->getRowArray();

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/presales/v_add_proforma',$data);
    }

    // function showing_cc($id){
    //     $data = $this->m_manufacture->q_t_CC(" and idchemicalcomposition='$id'")->getRow();
    //     echo json_encode($data);
    // }

    function updateProforma()
    {
        $nama = trim($this->session->get('nama'));
        $docno = @hex2bin((string) $this->request->getGet('id'));

        if ($docno === false || $docno === '') {
            return redirect()->to(base_url('sales/presales/performainvoice'))
                ->with('proforma_error', 'Nomor dokumen tidak valid.');
        }

        [$aksesOk, $aksesMsg, $dtl] = $this->proforma_akses_update($docno);
        if (!$aksesOk) {
            return redirect()->to(base_url('sales/presales/performainvoice'))
                ->with('proforma_error', $aksesMsg);
        }

        $status = trim((string) ($dtl['status'] ?? ''));

        if ($status === 'F' || $status === 'P') {
            $builder = $this->db->table('sc_trx.proforma');
            $builder->where('trim(docno)', $docno);
            $builder->update(['status' => 'E']);

            return redirect()->to(base_url('sales/presales/addProforma'));
        }

        return redirect()->to(base_url('sales/presales/performainvoice'))
            ->with('proforma_error', 'Status dokumen tidak dapat diedit.');
    }

    function saveProforma(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $docref = strtoupper($this->request->getPost('docref'));
        // $penerima = strtoupper($this->request->getPost('penerima'));
        $docno = strtoupper($this->request->getPost('docno'));
        $docdate = strtoupper($this->request->getPost('docdate'));
        $cust = strtoupper($this->request->getPost('cust'));
        $phone = strtoupper($this->request->getPost('phone'));
        $fax = strtoupper($this->request->getPost('fax'));
        $up = strtoupper($this->request->getPost('up'));
        $rolejob = strtoupper($this->request->getPost('rolejob'));
        $address = strtoupper($this->request->getPost('address'));
        $description = strtoupper($this->request->getPost('desc'));

        $pono = strtoupper($this->request->getPost('pono'));
        $podate = strtoupper($this->request->getPost('podate'));
        $jnsinvoice = strtoupper($this->request->getPost('jnsinvoice'));
        $facrisk = strtoupper($this->request->getPost('facrisk'));
        $shipper = strtoupper($this->request->getPost('shipper'));
        $consignee = strtoupper($this->request->getPost('consignee'));
        $shippingmark = strtoupper($this->request->getPost('shippingmark'));
        $notifyparty = strtoupper($this->request->getPost('notifyparty'));
        // $paymentmethod = strtoupper($this->request->getPost('paymentmethod'));
        // $bank = strtoupper($this->request->getPost('bank'));


        // $dateout = strtoupper($this->request->getPost('dateout'));
        // $nopol = strtoupper($this->request->getPost('nopol'));
        // $isreturn = ($this->request->getPost('isreturn'));
        // $datereturn = strtoupper($this->request->getPost('datereturn'));
        // $tujuan = ($this->request->getPost('tujuan'));
        // $jenisbarang = ($this->request->getPost('jenisbarang'));
        // $baranglain = strtoupper($this->request->getPost('baranglain'));
        $countx = $this->m_presales->q_proforma_master_temp(" and trim(inputby)='$nama'")->getNumRows();

        // if ($isreturn === 'kembali' && empty($datereturn)) {
        //     return redirect()->to(base_url('sales/presales/addProforma'))
        //         ->with('error', 'Return date is required when selecting "Kembali".');
        // }

        // if ($jenisbarang === 'lainlain' && empty($baranglain)) {
        //     return redirect()->to(base_url('sales/presales/addProforma'))
        //         ->with('error', 'Other goods description is required when selecting "Lain-lain".');
        // }


        if (empty($countx)) {
            $custRow = $this->m_presales->q_customer_proforma($cust);
            $info = array (
                'docno' => $docno,
                // 'penerima' => $penerima,
                // 'doctype' => $doctype,
                // 'docdate' => date('Y-m-d'),
                'docdate' => $docdate,
                // 'dateout' => $dateout,
                'cust' => $cust,
                'nmcustomer' => trim((string) ($custRow['nmcustomer'] ?? '')),

                'pono' => $pono,
                'podate' => $podate,
                'jnsinvoice' => $jnsinvoice,
                'facrisk' => $facrisk,
                'shipper' => $shipper,
                'consignee' => $consignee,
                'shippingmark' => $shippingmark,
                'notifyparty' => $notifyparty,
                // 'paymentmethod' => $paymentmethod,
                // 'bank' => $bank,

                'phone' => ($phone !== '' ? $phone : trim((string) ($custRow['phone'] ?? ''))),
                'fax'=>$fax,
                // 'up'=>$up,
                'address'=>$address,
                'rolejob' => $rolejob,
                // 'datereturn' => (!empty($datereturn) ? date('Y-m-d', strtotime($datereturn)) : null),
                // 'baranglain'=>$baranglain,
                // 'docdate' => date('Y-m-d'),
                'status' => 'E',
                'description' => $description,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.proforma');
            $builder->where('docno',$docno);
            $builder->insert($info);
            return redirect()->to(base_url('sales/presales/addProforma'));
        } else {
            /*RETURN FAILED*/
            return redirect()->to(base_url('sales/presales/addProforma'));
        }

    }

    function showing_proformatrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_presales->q_proforma_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_proformatemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_presales->q_proforma_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_proforma_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_presales->q_proforma_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    public function insert_detail_proforma()
    {
        if (!$this->proforma_can_update()) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Anda tidak memiliki hak update untuk menu ini.'
            ]);
        }

        $nama = trim($this->session->get('nama'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if (!isset($data->key) || $data->key !== '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid Request Key!'
            ]);
        }

        $dataprocess = $data->body ?? null;
        $docno       = trim((string) ($dataprocess->docno ?? ''));
        $idunit      = trim((string) ($dataprocess->idunit ?? ''));
        $namabarang  = trim((string) ($dataprocess->namabarang ?? ''));
        $qty         = trim((string) ($dataprocess->qty ?? ''));
        $price       = trim((string) ($dataprocess->price ?? ''));
        $description = trim((string) ($dataprocess->description ?? ''));

        $qtyVal   = (float) str_replace(',', '', $qty);
        $priceVal = (float) str_replace(',', '', $price);
        $amountVal = round($qtyVal * $priceVal, 4);

        if ($docno === '' || $idunit === '' || $namabarang === '' || $qty === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Lengkapi No. Jurnal/Dokumen, Unit, Nama Barang, dan Qty.'
            ]);
        }

        $header = $this->db->table('sc_tmp.proforma')
            ->where('docno', $docno)
            ->where('inputby', $inputby)
            ->get()
            ->getRowArray();

        if (!$header || trim((string) ($header['status'] ?? '')) !== 'E') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Header proforma belum ada atau tidak dalam status edit.'
            ]);
        }

        $insert = $this->db->table('sc_tmp.proformadtl')->insert([
            'docno'       => $docno,
            'idbarang'    => null,
            'nmbarang'    => $namabarang,
            'unit'        => $idunit,
            'qty'         => $qtyVal,
            'price'       => $priceVal,
            'usdmt'       => 0,
            'exchange'    => 1,
            'amount'      => $amountVal,
            'description' => $description,
            'status'      => 'E',
            'inputby'     => $inputby,
            'inputdate'   => $inputdate,
        ]);

        return $this->response->setJSON([
            'status'  => (bool) $insert,
            'message' => $insert ? 'Detail berhasil disimpan.' : 'Detail gagal disimpan.'
        ]);
    }

    public function insertNewProforma()
    {
        // Ambil data dari session
        $nama = trim($this->session->get('nama'));
        $inputby = $nama;
        $inputdate = date('Y-m-d H:i:s');
        // Ambil body request dalam bentuk JSON
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        // Validasi apakah request memiliki key yang benar
        if (!isset($data->key) || $data->key !== '1203jD0j120dkjjKODNOoimdi)D(J)Jmjid0sjd0ijme09wjei0kjisdjfDSojiodksOjO') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid Request Key!'
            ]);
        }

        // Ambil docno dari body
        $docno = isset($data->body->docno) ? trim($data->body->docno) : '';

        if ($docno === '') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'docno is required!'
            ]);
        }
        // Data untuk disimpan ke database
        $data_insert = [
            'docno' => $docno,
            'inputby' => $inputby,
            'inputdate' => $inputdate,
            // 'exchange' => 
            'status' => 'I' // Status awal Insert
        ];

        // Insert ke database
        $builder = $this->db->table('sc_tmp.proformadtl');
        $insert = $builder->insert($data_insert);

        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Proforma Invoice successfully created!',
                'docno' => $docno
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create Proforma Invoice. Please try again.'
            ]);
        }
    }




    public function update_detail_proforma()
    {
        if (!$this->proforma_can_update()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki hak update untuk menu ini.'
            ]);
        }

        $nama = trim($this->session->get('nama')); // Mengambil docno dari session
        $request_body = file_get_contents('php://input');
        $data = $this->request->getJSON(); // Mengambil data JSON langsung
        $updateby = $nama;
        $updatedate = date('Y-m-d H:i:s');

        // Ambil data POST (dari frontend)
        $updates = $this->request->getPost('updates');
        $masterData = $this->request->getPost('masterData'); // Ambil data master dari POST


        $response = [];

        if (!empty($updates)) {
            foreach ($updates as $update) {
                $idurut = $update['idurut'] ?? null;
                $idbarang = trim($update['idbarang']) ?? '';
                $nmbarang = trim($update['nmbarang']) ?? '';
                $idunit = $update['idunit'] ?? '';
                $qty = $update['qty'] ?? 0;
                $price = $update['price'] ?? 0;
                $amount = $update['amount'] ?? 0;
                // $exchange = $update['exchange'] ?? 0;

                // $description = strtoupper($update['description']) ?? '';

                if (empty($idurut)) {
                    continue; // Skip jika idurut kosong
                }

                // Data yang akan diupdate
                $infoupdate = [
                    'idbarang' => $idbarang,
                    'nmbarang' => $nmbarang,
                    'unit' => $idunit,
                    'qty' => $qty,
                    'price' => $price,
                    'amount' => $amount,
                    // 'usdmt' => $usdmt,

                    // 'description' => $description,
                    'status' => 'F',
                    'updateby' => $updateby,
                    'updatedate' => $updatedate,
                ];

                // Update berdasarkan idurut dan docno = nama
                $builder = $this->db->table('sc_tmp.proformadtl');
                $builder->where('idurut', $idurut);
                $builder->where('inputby', $nama);
                $builder->where('docno', trim($masterData['docno']));

                if ($builder->update($infoupdate)) {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => true,
                        'message' => 'Update successful'
                    ];
                } else {
                    $response[] = [
                        'idurut' => $idurut,
                        'status' => false,
                        'message' => 'Update failed'
                    ];
                }
            }

            // Berikan respons ke frontend
            echo json_encode([
                'status' => 'success',
                'message' => 'Data updated successfully',
                'results' => $response
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No updates provided'
            ]);
        }
    }


    // public function get_itemsales()
    // {
    //     $suppliers = $this->m_presales->get_itemsales(); // Fungsi di model untuk ambil data supplier
    //     // Format data yang dikembalikan dalam bentuk yang sesuai dengan Select2
    //     $result = [];
    //     foreach ($suppliers as $supplier) {
    //         $result[] = [
    //             'idsupplier' => $supplier->idsupplier,
    //             'nmsupplier' => $supplier->nmsupplier,
    //             'text' => $supplier->idsupplier . ' <=> ' . $supplier->nmsupplier,
    //         ];
    //     }

    //     echo json_encode(['items' => $result]);
    // }

    function list_t_proforma_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_presales->get_t_proforma_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '<select disabled class="idbarang-dropdown" style="width: 100%; height: 20px!important; font-size: 12px;" data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') .
                ' <i class="fa fa-circle text-success" style="font-size:8px;"></i> ' .
                htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') .
                '</option>
            </select>';


            $row[] = '<input class="form-control ratakanan jaccseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 4, '.', ',').'" disabled  min="0">';
            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
            <option value="" disabled>-- Choose --</option>
            <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
            </select>';
            //qty

            //price
            // Price dengan Rp sejajar
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                <input class="form-control ratakanan jaccseparator" 
                    style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="price_'.$lm->idurut.'" 
                    name="price_'.$lm->idurut.'" 
                    value="'.number_format($lm->price, 4, '.', ',').'" 
                    disabled min="0">
            </div>';

            //exchange
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                    <input class="form-control ratakanan jaccseparator amount" style="margin:0px; background-color:#d6d5d5;width: 100%;" 
                    type="text"  
                    id="amount_'.$lm->idurut.'" 
                    name="amount_'.$lm->idurut.'" 
                    value="'.number_format($lm->amount, 4, '.', ',').'" 
                    disabled  min="0">
            </div>';
            //usdmt
            // USDMT dengan $
            // $row[] = '
            // <div style="display:flex; align-items:center;">
            //     <span style="margin-right:4px; font-size:12px;font-weight:bold">$</span>
            //     <input class="form-control ratakanan jaccseparator" 
            //         style="margin:0px; background-color:#d6d5d5; width:100%;" 
            //         type="text"  
            //         id="usdmt_'.$lm->idurut.'" 
            //         name="usdmt_'.$lm->idurut.'" 
            //         value="'.number_format($lm->usdmt, 2, ',', '.').'" 
            //         disabled min="0">
            // </div>';    

            //description
            $row[] = '<input class="form-control "   style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->description, ENT_QUOTES, 'UTF-8') . '" disabled >';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_presales->t_proforma_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_presales->t_proforma_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_t_proforma_dtltrx(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_presales->get_t_proforma_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = '<select disabled class="idbarang-dropdown" style="width: 100%; height: 20px!important; font-size: 12px;" data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') .
                ' <i class="fa fa-circle text-success" style="font-size:8px;"></i> ' .
                htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') .
                '</option>
            </select>';


            $row[] = '<input class="form-control ratakanan jaccseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 4, '.', ',').'" disabled  min="0">';
            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
            <option value="" disabled>-- Choose --</option>
            <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
            </select>';
            //qty

            //price
            // Price dengan Rp sejajar
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                <input class="form-control ratakanan jaccseparator" 
                    style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="price_'.$lm->idurut.'" 
                    name="price_'.$lm->idurut.'" 
                    value="'.number_format($lm->price, 4, '.', ',').'" 
                    disabled min="0">
            </div>';

            //exchange
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                    <input class="form-control ratakanan jaccseparator amount" style="margin:0px; background-color:#d6d5d5;width: 100%;" 
                    type="text"  
                    id="amount_'.$lm->idurut.'" 
                    name="amount_'.$lm->idurut.'" 
                    value="'.number_format($lm->amount, 4, '.', ',').'" 
                    disabled  min="0">
            </div>';
            //usdmt
            // USDMT dengan $
            // $row[] = '
            // <div style="display:flex; align-items:center;">
            //     <span style="margin-right:4px; font-size:12px;font-weight:bold">$</span>
            //     <input class="form-control ratakanan jaccseparator" 
            //         style="margin:0px; background-color:#d6d5d5; width:100%;" 
            //         type="text"  
            //         id="usdmt_'.$lm->idurut.'" 
            //         name="usdmt_'.$lm->idurut.'" 
            //         value="'.number_format($lm->usdmt, 2, ',', '.').'" 
            //         disabled min="0">
            // </div>';    

            //description
            $row[] = '<input class="form-control "   style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->description, ENT_QUOTES, 'UTF-8') . '" disabled >';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_presales->t_proforma_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_presales->t_proforma_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryProforma(){
        if (!$this->proforma_can_update()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki hak update untuk menu ini.',
                'redirect' => base_url('/sales/presales/performainvoice'),
            ]);
        }

        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '')";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_presales->q_proforma_master_temp($param);
        $headerRow = $header->getRowArray();
        $status = trim((string) ($headerRow['status'] ?? ''));
        $proformaDocno = trim((string) ($headerRow['docno'] ?? ''));
        $jnsinvoice = strtoupper(trim((string) ($headerRow['jnsinvoice'] ?? '')));
        $docnopjo = trim((string) ($headerRow['docnopjo'] ?? ''));

        $cek = $this->m_presales->q_proforma_dtl_temp($paramdtl);
        $cek2 = $this->m_presales->q_proforma_dtl_temp($paramdtl2);

        if ($status !== 'E' || $proformaDocno === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Header proforma tidak ditemukan atau tidak dalam status edit (E).',
                'redirect' => base_url('/sales/presales/addProforma'),
            ]);
        }

        if ($jnsinvoice === 'INVOICE') {
            if ($docnopjo === '') {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'INVOICE wajib menarik PJO yang sudah memiliki jurnal POSTED.',
                    'redirect' => base_url('/sales/presales/addProforma'),
                ]);
            }

            $pjoCheck = $this->m_presales->q_penjualan_proforma_header($docnopjo);
            if (!$pjoCheck) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'PJO ' . $docnopjo . ' tidak valid atau jurnal SALESX belum POSTED.',
                    'redirect' => base_url('/sales/presales/addProforma'),
                ]);
            }
        }

        $detailRows = $this->db->table('sc_tmp.proformadtl')
            ->where('docno', $proformaDocno)
            ->where('inputby', $nama)
            ->get()
            ->getResultArray();

        $totals = $this->proforma_hitung_totals(
            $headerRow,
            $detailRows,
            $docnopjo !== ''
        );


        $builder = $this->db->table('sc_tmp.proforma');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.A.3');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.A.3',
            );
            $builder_trxerror->insert($infotrxerror);

            // return redirect()->to(base_url('/sales/presales/addProforma'));
            return $this->response->setJSON([
                'status' => 'error',
                'redirect' => base_url('/sales/presales/addProforma')
            ]);
        } else {
            // Ambil dari request POST
            // $brand       = strtoupper(trim($this->request->getPost('brand')));
            // $size        = strtoupper(trim($this->request->getPost('size')));
            // $qty         = strtoupper(trim($this->request->getPost('qty')));
            // $pembayaran  = strtoupper(trim($this->request->getPost('pembayaran')));
            // $pengiriman  = strtoupper(trim($this->request->getPost('pengiriman')));
            // $expdateph   = trim($this->request->getPost('expdateph')); // format dd-mm-yyyy
            // $ketentuan   = strtoupper(trim($this->request->getPost('ketentuan')));

            $downpayment = str_replace(',', '', strtoupper(trim($this->request->getPost('downpayment'))));
            $pph22       = str_replace(',', '', strtoupper(trim($this->request->getPost('pph22'))));

            $totals = $this->proforma_hitung_totals(
                array_merge($headerRow, [
                    'downpayment' => (float) $downpayment,
                    'pph22'       => (float) $pph22,
                ]),
                $detailRows,
                $docnopjo !== ''
            );


            $bank         = strtoupper(trim($this->request->getPost('bank')));
            $nmbank         = strtoupper(trim($this->request->getPost('nmbank')));
            $alamatbank         = strtoupper(trim($this->request->getPost('alamatbank')));
            $accname         = strtoupper(trim($this->request->getPost('accname')));
            $accno         = strtoupper(trim($this->request->getPost('accno')));
            $swiftcode         = strtoupper(trim($this->request->getPost('swiftcode')));
            $desc         = strtoupper(trim($this->request->getPost('desc')));


            // Convert expdate ke format YYYY-MM-DD
            // $expdate = null;
            // if (!empty($expdateph)) {
            //     $expdate = date('Y-m-d', strtotime(str_replace('-', '/', $expdateph)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'brand'      => $brand,
                // 'size'       => $size,
                // 'qty'        => $qty,
                // 'pembayaran' => $pembayaran,
                // 'pengiriman' => $pengiriman,
                // 'expdate'    => $expdate,
                // 'ketentuan'  => $ketentuan,

                'grosssales'  => $totals['grosssales'],
                'downpayment' => (float) $downpayment,
                'netsales'    => $totals['netsales'],
                'taxbasis'    => $totals['taxbasis'],
                'vat'         => $totals['vat'],
                'pph22'       => $totals['pph22'],
                'ttlprice'    => $totals['ttlprice'],
                'docnotmp'    => $proformaDocno,

                'description'       => $desc,

                'bank'         => $bank,
                'nmbank'      => $nmbank,
                'alamatbank'      => $alamatbank,
                'accno'      => $accno,
                'accname'      => $accname,
                'swiftcode'      => $swiftcode,




            ];

            $builder->where('docno', $proformaDocno);
            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('docno', $proformaDocno);
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.S.A.3'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                // return redirect()->to(base_url('/sales/presales/performainvoice'));
                return $this->response->setJSON([
                    'status'      => 'success',
                    'message'     => 'Proforma/Invoice berhasil difinalkan.',
                    'docno'       => $proformaDocno,
                    'preview_url' => $this->proforma_preview_url($proformaDocno),
                    'redirect'    => base_url('/sales/presales/performainvoice')
                ]);
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.S.A.3',
                );
                $builder_trxerror->insert($infotrxerror);
                // return redirect()->to(base_url('/sales/presales/addProforma'));
                return $this->response->setJSON([
                    'status' => 'error',
                    'redirect' => base_url('/sales/presales/addProforma')
                ]);
            }



        }

    }

    function deleteProforma(){
        $docno = @hex2bin((string) $this->request->getGet('id'));

        if ($docno === false || $docno === '') {
            return redirect()->to(base_url('sales/presales/performainvoice'))
                ->with('proforma_error', 'Nomor dokumen tidak valid.');
        }

        [$aksesOk, $aksesMsg] = $this->proforma_akses_update($docno);
        if (!$aksesOk) {
            return redirect()->to(base_url('sales/presales/performainvoice'))
                ->with('proforma_error', $aksesMsg);
        }

        $builder = $this->db->table('sc_trx.proforma');
        $builder->where('docno', $docno);
        $builder->update(['status' => 'C']);

        return redirect()->to(base_url('sales/presales/performainvoice'))
            ->with('proforma_success', 'Dokumen proforma berhasil dibatalkan.');
    }


    function deleteProformaDtl(){
        $id = $this->request->getPost('id'); // Ambil array ID
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getPost('docno'); // Ambil docno yang dikirim dari AJAX

        if (empty($id) || empty($docno)) {
            echo json_encode(['status' => false, 'messages' => 'Missing Parameters']);
            return;
        }

        if (!$this->proforma_can_update()) {
            echo json_encode(['status' => false, 'messages' => 'Anda tidak memiliki hak update.']);
            return;
        }

        $header = $this->db->table('sc_tmp.proforma')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if (!$header || trim((string) ($header['status'] ?? '')) !== 'E') {
            echo json_encode(['status' => false, 'messages' => 'Dokumen bukan milik Anda atau tidak dalam status edit.']);
            return;
        }


        // $idurut = $id[0];
        $iupdate = array(
            'status' => 'C',
        );
        $builder = $this->db->table('sc_tmp.proformadtl');
        $builder->where('inputby',$nama);
        $builder->where('docno',$docno);
        $builder->whereIn('idurut',$id);

        if ($builder->update($iupdate)) {
            $builder->where('docno',$docno);
            $builder->where('inputby',$nama);
            $builder->whereIn('idurut',$id);
            $builder->where('status','C');

            $builder->delete();

            $getResult = array('status' => true, 'messages' => 'Data Deleting'.' Code: ');
            echo json_encode($getResult);
        } else {
            $getResult = array('status' => false, 'messages' => 'Data Fail, Try Again');
            echo json_encode($getResult);
        }

    }

    /**
     * Daftar PJO/Penjualan untuk dropdown Proforma/Invoice.
     * Hanya PJO status F + jurnal SALESX POSTED.
     */
    public function list_penjualan_proforma()
    {
        $branch = trim((string) $this->session->get('idbranchjob'));
        if ($branch === '') {
            $branch = trim((string) $this->session->get('loccode'));
        }

        $customer = trim((string) ($this->request->getPost('_customer_')
            ?? $this->request->getPost('cust')
            ?? ''));
        $search   = trim((string) $this->request->getPost('_search_'));

        $items = $this->m_presales->q_list_penjualan_proforma($branch, $customer, $search);

        return $this->response->setJSON([
            'total_count'           => count($items),
            'items'                 => $items,
            'incomplete_getResults' => false,
        ]);
    }

    /**
     * Tarik header + detail PJO ke header/detail proforma temporary.
     */
    public function pull_penjualan_proforma()
    {
        if (!$this->proforma_can_update()) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Anda tidak memiliki hak update untuk menu ini.',
            ]);
        }

        $nama     = trim((string) $this->session->get('nama'));
        $docno    = strtoupper(trim((string) $this->request->getPost('docno')));
        $docnopjo = strtoupper(trim((string) $this->request->getPost('docnopjo')));

        if ($docno === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'No. Jurnal/Dokumen proforma belum terisi.',
            ]);
        }

        if ($docnopjo === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Pilih No. PJO/Penjualan terlebih dahulu.',
            ]);
        }

        $header = $this->db->table('sc_tmp.proforma')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if (!$header) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Header proforma belum tersimpan. Klik Proses terlebih dahulu.',
            ]);
        }

        if (trim((string) $header['status']) !== 'E') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Proforma tidak dalam status edit (E).',
            ]);
        }

        $pjo = $this->m_presales->q_penjualan_proforma_header($docnopjo);

        if (!$pjo) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'PJO tidak ditemukan, belum FINAL, atau jurnal SALESX belum POSTED.',
            ]);
        }

        $jnsinvoice = strtoupper(trim((string) ($header['jnsinvoice'] ?? 'PROFORMA')));
        $custHeader = strtoupper(trim((string) ($header['cust'] ?? '')));
        $custPjo    = strtoupper(trim((string) ($pjo['kdcustomer'] ?? '')));

        if ($custHeader !== '' && $custPjo !== '' && $custHeader !== $custPjo) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Customer proforma tidak sama dengan customer PJO.',
            ]);
        }

        if ($jnsinvoice === 'INVOICE') {
            $used = $this->m_presales->q_proforma_by_pjo($docnopjo, 'INVOICE');
            if ($used && strtoupper(trim((string) $used['docno'])) !== $docno) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'PJO ' . $docnopjo . ' sudah dipakai INVOICE aktif: ' . trim((string) $used['docno']),
                ]);
            }
        }

        $rows = $this->m_presales->q_penjualan_proforma_dtl($docnopjo);
        if (empty($rows)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'PJO tidak memiliki detail item.',
            ]);
        }

        $details = [];
        foreach ($rows as $row) {
            $details[] = [
                'idurutpjo'     => (int) $row->idurut,
                'idbarang'      => trim((string) $row->idbarang),
                'nmbarang'      => trim((string) $row->nmbarang),
                'unit'          => trim((string) $row->unit),
                'qty'           => (float) $row->qty,
                'price'         => (float) $row->harga,
                'amount'        => (float) $row->nilai,
                'discount'      => (float) $row->multidisc,
                'nilaikonversi' => (float) $row->nilaikonversi,
                'nilaipajak'    => (float) $row->nilaipajak,
                'idtax'         => trim((string) $row->idtax),
                'idgudang'      => trim((string) $row->idgudang),
                'idspec'        => (strtoupper(trim((string) $row->idspec)) === 'NULL'
                    ? ''
                    : trim((string) $row->idspec)),
                'description'   => (string) ($row->description ?? ''),
            ];
        }

        $headerForCalc = array_merge($header, [
            'dpp'         => (float) $pjo['dpp'],
            'jumlahpajak' => (float) $pjo['jumlahpajak'],
            'downpayment' => (float) ($header['downpayment'] ?? 0),
            'pph22'       => (float) ($header['pph22'] ?? 0),
        ]);
        $totals = $this->proforma_hitung_totals($headerForCalc, $details, true);

        $db = $this->db;
        $db->transStart();

        $db->table('sc_tmp.proforma')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->update([
                'docnopjo'    => $docnopjo,
                'jurnalno'    => trim((string) $pjo['jurnalno']),
                'cust'        => $custPjo,
                'nmcustomer'  => trim((string) $pjo['nmcustomer']),
                'address'     => (string) ($pjo['address'] ?? ''),
                'phone'       => (string) ($pjo['phone'] ?? ''),
                'fax'         => (string) ($pjo['fax'] ?? ''),
                'pono'        => $docnopjo,
                'podate'      => (string) ($pjo['docdate'] ?? ''),
                'idtax'       => trim((string) ($pjo['idtax'] ?? '')),
                'isinclusive' => trim((string) ($pjo['isinclusive'] ?? '')),
                'currcode'    => trim((string) ($pjo['currcode'] ?? '')),
                'kurs'        => (float) ($pjo['kurs'] ?? 1),
                'dpp'         => (float) $pjo['dpp'],
                'jumlahpajak' => (float) $pjo['jumlahpajak'],
                'total'       => (float) $pjo['total'],
                'grosssales'  => $totals['grosssales'],
                'netsales'    => $totals['netsales'],
                'taxbasis'    => $totals['taxbasis'],
                'vat'         => $totals['vat'],
                'pph22'       => $totals['pph22'],
                'ttlprice'    => $totals['ttlprice'],
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s'),
            ]);

        $db->table('sc_tmp.proformadtl')
            ->where('docno', $docno)
            ->delete();

        foreach ($details as $detail) {
            $db->table('sc_tmp.proformadtl')->insert([
                'docno'         => $docno,
                'idbarang'      => $detail['idbarang'],
                'nmbarang'      => $detail['nmbarang'],
                'unit'          => $detail['unit'],
                'qty'           => $detail['qty'],
                'price'         => $detail['price'],
                'usdmt'         => 0,
                'exchange'      => 1,
                'amount'        => $detail['amount'],
                'description'   => $detail['description'],
                'status'        => 'E',
                'inputby'       => $nama,
                'inputdate'     => date('Y-m-d H:i:s'),
                'docnopjo'      => $docnopjo,
                'idurutpjo'     => $detail['idurutpjo'],
                'idtax'         => $detail['idtax'],
                'idgudang'      => $detail['idgudang'],
                'idspec'        => $detail['idspec'],
                'nilaikonversi' => $detail['nilaikonversi'],
                'nilaipajak'    => $detail['nilaipajak'],
                'discount'      => $detail['discount'],
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menarik PJO. Periksa data lalu coba lagi.',
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'PJO ' . $docnopjo . ' berhasil ditarik.',
            'header'  => [
                'docno'      => $docno,
                'docnopjo'   => $docnopjo,
                'jurnalno'   => trim((string) $pjo['jurnalno']),
                'cust'       => $custPjo,
                'nmcustomer' => trim((string) $pjo['nmcustomer']),
                'address'    => (string) ($pjo['address'] ?? ''),
                'phone'      => (string) ($pjo['phone'] ?? ''),
                'fax'        => (string) ($pjo['fax'] ?? ''),
                'pono'       => $docnopjo,
                'podate'     => (string) ($pjo['docdate'] ?? ''),
            ],
            'totals'  => $totals,
            'details' => $details,
        ]);
    }

    private function proforma_hitung_totals(array $header, array $details, bool $fromPjo): array
    {
        $gross = 0.0;
        foreach ($details as $detail) {
            $row = is_array($detail) ? $detail : (array) $detail;
            $gross += (float) ($row['amount'] ?? 0);
        }

        $downpayment = (float) ($header['downpayment'] ?? 0);
        $pph22       = (float) ($header['pph22'] ?? 0);

        if ($fromPjo) {
            $dpp  = (float) ($header['dpp'] ?? 0);
            $ppn  = (float) ($header['jumlahpajak'] ?? 0);
            $gross = $dpp > 0 ? $dpp : $gross;
            $net   = $gross - $downpayment;
            $tax   = $dpp;
            $vat   = $ppn;
            $total = $net + $vat + $pph22;
        } else {
            $net   = $gross - $downpayment;
            $tax   = (11 / 12) * $net;
            $vat   = $tax * 0.12;
            $pph22 = $net * 0.003;
            $total = $net + $vat + $pph22;
        }

        return [
            'grosssales' => round($gross, 4),
            'netsales'   => round($net, 4),
            'taxbasis'   => round($tax, 4),
            'vat'        => round($vat, 4),
            'pph22'      => round($pph22, 4),
            'ttlprice'   => round($total, 4),
        ];
    }

    private function proforma_preview_url(string $docno): string
    {
        return base_url('sales/presales/show_proforma/?docno=' . bin2hex($docno));
    }

    private function proforma_can_update(): bool
    {
        $role = trim((string) $this->session->get('roleid'));
        $akses = $this->m_role->detail_user_akses($role, 'I.S.A.3')->getRowArray();
        return trim((string) ($akses['a_update'] ?? '')) === 't';
    }

    private function proforma_akses_update(string $docno): array
    {
        $nama = trim((string) $this->session->get('nama'));
        $role = trim((string) $this->session->get('roleid'));

        $header = $this->db->table('sc_trx.proforma')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        if (!$header) {
            return [false, 'Dokumen proforma tidak ditemukan.', null];
        }

        $akses = $this->m_role->detail_user_akses($role, 'I.S.A.3')->getRowArray();
        $canUpdate = trim((string) ($akses['a_update'] ?? '')) === 't';

        if (!$canUpdate) {
            return [false, 'Anda tidak memiliki hak update untuk menu ini.', $header];
        }

        if (trim((string) ($header['inputby'] ?? '')) !== $nama) {
            return [false, 'Dokumen ini bukan milik Anda.', $header];
        }

        return [true, '', $header];
    }

    function show_proforma(){

        $module = 'I.S';
        $menu   = 'I.S.A.3';
        $table  = 'sc_trx.proforma';
        $nama   = trim($this->session->get('nama'));
        $docno  = $this->request->getGet('docno');

        if (empty($docno)) {
            return redirect()->to(base_url('sales/presales/performainvoice'));
        }

        $docno = @hex2bin($docno);
        if ($docno === false || $docno === '') {
            return redirect()->to(base_url('sales/presales/performainvoice'));
        }

        $headerProforma = $this->db->table('sc_trx.proforma')
            ->select('jnsinvoice')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();

        if (!$headerProforma) {
            return redirect()->to(base_url('sales/presales/performainvoice'));
        }

        $jnsinvoice = strtoupper(trim((string) ($headerProforma['jnsinvoice'] ?? '')));
        $builder = $this->db->table('sc_trx.proforma');

        //    $builder = $builder
        //         ->where('docno', $docno)
        //         ->update([
        //             'status'=> 'P',
        //             'printby' => $nama,
        //             'printdate' => date('Y-m-d H:i:s')
        //         ]);


        $enc_docno = $this->fiky_encryption->sealed($docno);

        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = $jnsinvoice === 'INVOICE' ? " Report Invoice" : " Report Proforma Invoice";

        //$datajson =  base_url("manufactur/production/api_proforma/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/presales/api_proforma/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/invoice.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_proforma_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_proforma(){
        $nama = trim($this->session->get('nama'));

        $dtlbranch = $this->m_global->q_master_branch()->getRowArray();
        $branch = strtoupper(trim($dtlbranch['branch']));
        $docno=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docno')));
        //$docdate=trim($this->fiky_encryption->unseal($this->request->getGet('enc_docdate')));
        // $idlocation=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idlocation')));
        // $idgroup=trim($this->fiky_encryption->unseal($this->request->getGet('enc_idgroup')));
        //$docno=trim($this->request->getGet('enc_docno'));

        // $ddate = explode(' - ',$docdate);
        // $tgl1 = date('Y-m-d',strtotime($ddate[0]));
        // $tgl2 = date('Y-m-d',strtotime($ddate[1]));

        if (empty($docno) or $docno==='') {
            $param_brg = "";
        } else {
            $param_brg = " and docno='$docno'";
        }

        // //idgroup
        // if (!empty($idgroup)) {
        //     $param_group=" and idgroup='$idgroup'";
        // } else {  $param_group=""; }

        $this->db->query("select sc_trx.pr_generate_pov_proforma_dtl(?, ?)", [$docno, $nama]);
        $databranch = $this->m_global->q_master_branch();
        $param=" and docno='$docno'";
        $datamst = $this->m_presales->q_proforma_master($param);
        $datadtl = $this->m_presales->q_view_print_proforma_dtl($param,$nama);
        // $datadtl = $this->m_presales->q_proforma_dtl($param);
        $masterRows = $datamst->getResult();
        $detail = $masterRows[0] ?? null;
        if ($detail) {

            $tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
            $detail->docno = trim($detail->docno);

            $detail->jnsinvoice = trim($detail->jnsinvoice) == 'PROFORMA' ? 'PROFORMA INVOICE' : 'INVOICE';

            $custRow = $this->m_presales->q_customer_proforma(trim((string) $detail->cust));
            $nmcust  = trim((string) ($custRow['nmcustomer'] ?? ''));
            foreach ($masterRows as $m) {
                $m->nmcustomer = $nmcust;
                $m->jnsinvoice = $detail->jnsinvoice;
            }
        }

        header("Content-Type: text/json");
        return json_encode(
            array(

                'info' => array([
                    //'date1' => date('d-m-Y',strtotime($tgl1)),
                    //'date2' => date('d-m-Y',strtotime($tgl2)),
                    'date1' => date('d-m-Y'),
                    'date2' => date('d-m-Y'),
                    'datenow' => date('d-m-Y'),
                    'userid' => $nama,
                    'param' => $param,

                ]
                ),
                'branch' => $databranch->getResult(),
                'master' => $masterRows,
                'detail' => $datadtl->getResult(),
            ), JSON_PRETTY_PRINT);
    }


    public function getRolePOProforma()
    {
        $jobcode    = trim($this->request->getGet('rolejob'));
        $jnsinvoice = strtoupper(trim($this->request->getGet('jnsinvoice')));
        $logindate  = trim($this->session->get('logindate'));

        if (empty($jobcode) || empty($jnsinvoice) || empty($logindate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'RoleJob, jnsinvoice, atau logindate tidak valid'
            ]);
        }

        // --- 1. Mapping CODE berdasarkan jenis invoice
        $codeMap = [
            'PROFORMA' => 'PROF',
            'INVOICE'  => 'INVC'
        ];
        $code = isset($codeMap[$jnsinvoice]) ? $codeMap[$jnsinvoice] : $jnsinvoice;

        // --- 2. Ambil bulan & tahun dari logindate
        $ts    = strtotime($logindate);
        $month = (int)date('m', $ts);
        $year  = date('Y', $ts);

        // Konversi bulan ke romawi
        $romawi = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];
        $bulanRomawi = $romawi[$month];

        // --- 3. Ambil SEMUA sequence yang sudah ada
        $pattern = $jobcode . '/' . $code . '/%/' . $bulanRomawi . '/' . $year;

        $builder = $this->db->table('sc_trx.proforma');
        $builder->select("docno");
        $builder->like('docno', $pattern, 'after');
        $builder->orderBy('docno', 'ASC');

        $result = $builder->get()->getResultArray();

        $newSeq = '0001'; // Default

        if (!empty($result)) {
            $existingSequences = [];

            // Ekstrak sequence number dari setiap docno
            foreach ($result as $row) {
                $parts = explode('/', $row['docno']);
                if (isset($parts[2])) {
                    $existingSequences[] = (int)$parts[2];
                }
            }

            // --- LOGIKA BARU: Cari gap ATAU lanjutkan dari max ---
            $maxSeq = max($existingSequences);
            $newSeq = $maxSeq + 1; // Default: lanjut dari max

            // Cari gap dari 1 sampai maxSeq
            for ($i = 1; $i <= $maxSeq; $i++) {
                if (!in_array($i, $existingSequences)) {
                    $newSeq = $i; // Ketemu gap, gunakan angka ini
                    break;
                }
            }

            $newSeq = str_pad($newSeq, 4, '0', STR_PAD_LEFT);
        }

        // --- 4. Buat docno baru
        $docno = $jobcode . '/' . $code . '/' . $newSeq . '/' . $bulanRomawi . '/' . $year;

        return $this->response->setJSON([
            'success' => true,
            'docno'   => $docno,
            'prefix' => $jobcode . '/'. $code,
            'infix'  => $newSeq,
            'suffix'  => $bulanRomawi . '/' . $year
        ]);
    }





}

