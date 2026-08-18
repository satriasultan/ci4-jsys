<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;

use function PHPUnit\Framework\isEmpty;

class PostSales extends BaseController
{

    //Sales Order External

    
    public function salesorderexternal()
    {
        $data['title']="Sales Order External";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.3'; $versirelease='I.S.B.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.3'";
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
        $dtl = $this->m_postsales->q_salesorderexternal_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntrySalesOrderExternal');
            $urlnext = base_url('sales/postsales/addSalesOrderExternal');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_list_salesorderexternal',$data);
    }

    function detailSalesOrderExternal()
    {
        /* Penambahan Squence */
        $data['title']="Detail Sales Order External";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/postsales/salesorderexternal'));
        }
        $kodemenu='I.S.B.3'; $versirelease='I.S.B.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.3'";
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
        $data['dtldata'] = $this->m_postsales->q_salesorderexternal_master($param)->getRowArray();
        return $this->template->render('sales/postsales/v_detail_salesorderexternal',$data);
    }

    function list_salesorderexternal(){
        $list = $this->m_postsales->get_t_front_salesorderexternal_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = '<div class="dropdown">
            //                 <button class="btn btn-primary btn-sm dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-bars"></i>
            //                     <span class="caret"></span></button>
            //                     <div class="dropdown-menu" role="menu">
            //                         <a style="background-color: #3badf6;"class="dropdown-item" href=' . "'" . base_url('sales/postsales/salesorderexternal/updateSalesOrderExternal') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Update This salesorderexternal : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-bars"></i> Update Sales Order External </a>
            //                         <a style="background-color: #00ff8e;" class="dropdown-item" href=' . "'" . base_url('sales/postsales/salesorderexternal/show_salesorderexternal') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Print This Data Detail : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-eye"></i> Print Sales Order External </a>
            //                         <a style="background-color: red;" class="dropdown-item" href=' . "'" . base_url('sales/postsales/salesorderexternal/deleteSalesOrderExternal') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Remove this salesorderexternal : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-trash"></i> Delete Sales Order External </a>                      
            //                     </div>
            //             </div>
            // ';
            $updateBtn = '<a class="dropdown-item bg-warning" 
                href="' . base_url('sales/postsales/updateSalesOrderExternal') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'Update This salesorderexternal : ' . trim($lm->docno) . '\')">
                <i class="fa fa-edit"></i> Update Sales Order External 
            </a>';

            $detailBtn = '<a style="background-color: #3badf6;" class="dropdown-item" 
                href="' . base_url('sales/postsales/detailSalesOrderExternal') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'View This Detail salesorderexternal : ' . trim($lm->docno) . '\')">
                <i class="fa fa-eye"></i> Detail Sales Order External 
            </a>';

            $printBtn = '<a style="background-color: #00ff8e;" class="dropdown-item" 
                            href="' . base_url('sales/postsales/show_salesorderexternal') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Print This Data Detail : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-print"></i> Print Sales Order External 
                        </a>';

            $deleteBtn = '<a class="dropdown-item bg-danger" 
                            href="' . base_url('sales/postsales/deleteSalesOrderExternal') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Cancel this salesorderexternal : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-trash"></i> Cancel Sales Order External 
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
            $row[] = $lm->pic;
            
            // $row[] = $lm->status_desc ?? $lm->status;
            $status = $lm->status_desc ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'FINAL':
                    $badgeClass = 'badge-success';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-info';
                    break;
                case 'CANCEL':
                    $badgeClass = 'badge-warning';
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
            "recordsTotal" => $this->m_postsales->t_front_salesorderexternal_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_salesorderexternal_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntrySalesOrderExternal()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_postsales->q_salesorderexternal_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/postsales/salesorderexternal'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.salesorderexternal');
        $builder_dtl = $this->db->table('sc_tmp.salesorderexternaldtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.salesorderexternal');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/postsales/salesorderexternal'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/salesorderexternal'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/salesorderexternal'));
        }

    }

    function addSalesOrderExternal()
    {
        /* Penambahan Squence */
        $data['title']="Input Sales Order External";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.3'; $versirelease='I.S.B.3/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.B.3'";
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
        $data['mst'] = $this->m_postsales->q_salesorderexternal_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_postsales->q_salesorderexternal_master_temp($param)->getRowArray();

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_add_salesorderexternal',$data);
    }

    function showing_cc($id){
        $data = $this->m_manufacture->q_t_CC(" and idchemicalcomposition='$id'")->getRow();
        echo json_encode($data);
    }

    function updateSalesOrderExternal()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_postsales->q_salesorderexternal_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.salesorderexternal');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/postsales/addSalesOrderExternal'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/postsales/salesorderexternal'));
        }
    }

    function saveSalesOrderExternal(){
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
        $enduser = strtoupper($this->request->getPost('enduser'));
        $trader = strtoupper($this->request->getPost('trader'));
        $pocust = strtoupper($this->request->getPost('pocust'));
        $exchangerate = strtoupper($this->request->getPost('exchangerate'));
        $currency = strtoupper($this->request->getPost('currency'));
        $address = strtoupper($this->request->getPost('address'));
        $pic = strtoupper($this->request->getPost('pic'));
        $description = strtoupper($this->request->getPost('desc'));

        // $dateout = strtoupper($this->request->getPost('dateout'));
        // $nopol = strtoupper($this->request->getPost('nopol'));
        // $isreturn = ($this->request->getPost('isreturn'));
        // $datereturn = strtoupper($this->request->getPost('datereturn'));
        // $tujuan = ($this->request->getPost('tujuan'));
        // $jenisbarang = ($this->request->getPost('jenisbarang'));
        // $baranglain = strtoupper($this->request->getPost('baranglain'));
        $countx = $this->m_postsales->q_salesorderexternal_master_temp(" and trim(inputby)='$nama'")->getNumRows();

        // if ($isreturn === 'kembali' && empty($datereturn)) {
        //     return redirect()->to(base_url('sales/postsales/addSalesOrderExternal'))
        //         ->with('error', 'Return date is required when selecting "Kembali".');
        // }
    
        // if ($jenisbarang === 'lainlain' && empty($baranglain)) {
        //     return redirect()->to(base_url('sales/postsales/addSalesOrderExternal'))
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
                'currency' => $currency,
                'exchangerate' => $exchangerate,
                'enduser' => $enduser,
                'trader' => $trader,
                'pocust' => $pocust,
                'phone' => $phone,
                'fax'=>$fax,
                'address'=>$address,
                'rolejob' => $rolejob,
                'pic' => $pic,
                // 'datereturn' => (!empty($datereturn) ? date('Y-m-d', strtotime($datereturn)) : null),
                // 'baranglain'=>$baranglain,
                // 'docdate' => date('Y-m-d'),
                'status' => 'E',
                'description' => $description,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.salesorderexternal');
            $builder->where('docno',$docno);
            $builder->insert($info);
            return redirect()->to(base_url('sales/postsales/addSalesOrderExternal'));
        } else {
            /*RETURN FAILED*/
            return redirect()->to(base_url('sales/postsales/addSalesOrderExternal'));
        }

    }

    function showing_salesorderexternaltrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_salesorderexternal_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_salesorderexternaltemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_salesorderexternal_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_salesorderexternal_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_postsales->q_salesorderexternal_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    public function insert_detail_salesorderexternal()
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
        $builder = $this->db->table('sc_tmp.salesorderexternaldtl'); // Sesuaikan dengan tabel Anda
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

    public function insertNewSalesOrderExternal()
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
        $builder = $this->db->table('sc_tmp.salesorderexternaldtl');
        $insert = $builder->insert($data_insert);
    
        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Sales Order External successfully created!',
                'docno' => $docno
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create Sales Order External. Please try again.'
            ]);
        }
    }
    
    


    public function update_detail_salesorderexternal()
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
                $grade = $update['grade'] ?? '';
                $size = $update['size'] ?? '';
                $cutlength = $update['cutlength'] ?? '';
                $specno = $update['specno'] ?? '';
                $ordernumbermsr = $update['ordernumbermsr'] ?? '';
                $etd = $update['etd'] ? date('Y-m-d',strtotime($update['etd'])) : '';
                
                $qty = $update['qty'] ?? 0;
                $price = $update['price'] ?? 0;
                $amount = $update['amount'] ?? 0;
                $totaldelivery = $update['totaldelivery'] ?? 0;
                $balanceorder = $update['balanceorder'] ?? 0;
                
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

                    'grade' => $grade,
                    'size' => $size,
                    'cutlength' => $cutlength,
                    'specno' => $specno,
                    'ordernumbermsr' => $ordernumbermsr,
                    'etd' => $etd,
                    'amount' => $amount,
                    'totaldelivery' => $totaldelivery,
                    'balanceorder' => $balanceorder,


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
                $builder = $this->db->table('sc_tmp.salesorderexternaldtl');
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
        $suppliers = $this->m_postsales->get_itemsales(); // Fungsi di model untuk ambil data supplier
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

    function list_t_salesorderexternal_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_salesorderexternal_dtl_temp_view($docno);
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
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '" selected>'
                    . htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') . 
                '</option>
            </select>';


             //grade
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="grade_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="grade_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->grade, ENT_QUOTES, 'UTF-8') . '" disabled >';
            
            //size
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="size_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="size_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->size, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //cutlength
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="cutlength_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="cutlength_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->cutlength, ENT_QUOTES, 'UTF-8') . '" disabled >';

             //qty
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 2, ',', '.').'" disabled  min="0">';
            
            //unit
            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
                </select>';

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

            //total amount
            // Price dengan Rp sejajar
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                <input class="form-control ratakanan jtsseparator" 
                style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="amount_'.$lm->idurut.'" 
                    name="amount_'.$lm->idurut.'" 
                    value="'.number_format($lm->amount, 2, ',', '.').'" 
                    disabled min="0">
                    </div>';

            //etd
            // $row[] = '<input class="form-control " style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="etd_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="etd_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->etd, ENT_QUOTES, 'UTF-8') . '" disabled >';
            $row[] = '<input type="text" class="form-control" style="margin:0px; background-color:#d6d5d5;width: 100%;" id="etd_'.$lm->idurut.'" name="etd_'.$lm->idurut.'" value="'.($lm->etd ? htmlspecialchars(date('d-m-Y', strtotime($lm->etd)), ENT_QUOTES, 'UTF-8') : '').'" disabled >';

            //ordernumbermsr
            $row[] = '<input class="form-control " maxlength="50"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="ordernumbermsr_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="ordernumbermsr_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->ordernumbermsr, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //description
            $row[] = '<input class="form-control "   style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->description, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //specno
            $row[] = '<input class="form-control " maxlength="150"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="specno_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="specno_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->specno, ENT_QUOTES, 'UTF-8') . '" disabled >';

              //totaldelivery
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="totaldelivery_'.$lm->idurut.'" name="totaldelivery_'.$lm->idurut.'" value="'.number_format($lm->totaldelivery, 2, ',', '.').'" disabled  min="0">';
            
              //balanceorder
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="balanceorder_'.$lm->idurut.'" name="balanceorder_'.$lm->idurut.'" value="'.number_format($lm->balanceorder, 2, ',', '.').'" disabled  min="0">';
            
            $row[] = '';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_salesorderexternal_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_salesorderexternal_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

        function list_t_salesorderexternal_dtltrx(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_salesorderexternal_dtl_view($docno);
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
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '" selected>'
                    . htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') . 
                '</option>
            </select>';


             //grade
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="grade_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="grade_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->grade, ENT_QUOTES, 'UTF-8') . '" disabled >';
            
            //size
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="size_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="size_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->size, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //cutlength
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="cutlength_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="cutlength_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->cutlength, ENT_QUOTES, 'UTF-8') . '" disabled >';

             //qty
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 2, ',', '.').'" disabled  min="0">';
            
            //unit
            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
                </select>';

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

            //total amount
            // Price dengan Rp sejajar
            $row[] = '
            <div style="display:flex; align-items:center;">
                <span style="margin-right:4px; font-size:12px;font-weight:bold">Rp</span>
                <input class="form-control ratakanan jtsseparator" 
                style="margin:0px; background-color:#d6d5d5; width:100%;" 
                    type="text"  
                    id="amount_'.$lm->idurut.'" 
                    name="amount_'.$lm->idurut.'" 
                    value="'.number_format($lm->amount, 2, ',', '.').'" 
                    disabled min="0">
                    </div>';

            //etd
            // $row[] = '<input class="form-control " style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="etd_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="etd_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->etd, ENT_QUOTES, 'UTF-8') . '" disabled >';
            $row[] = '<input type="text" class="form-control" style="margin:0px; background-color:#d6d5d5;width: 100%;" id="etd_'.$lm->idurut.'" name="etd_'.$lm->idurut.'" value="'.($lm->etd ? htmlspecialchars(date('d-m-Y', strtotime($lm->etd)), ENT_QUOTES, 'UTF-8') : '').'" disabled >';

            //ordernumbermsr
            $row[] = '<input class="form-control " maxlength="50"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="ordernumbermsr_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="ordernumbermsr_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->ordernumbermsr, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //description
            $row[] = '<input class="form-control "   style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="description_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->description, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //specno
            $row[] = '<input class="form-control " maxlength="150"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="specno_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="specno_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->specno, ENT_QUOTES, 'UTF-8') . '" disabled >';

              //totaldelivery
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="totaldelivery_'.$lm->idurut.'" name="totaldelivery_'.$lm->idurut.'" value="'.number_format($lm->totaldelivery, 2, ',', '.').'" disabled  min="0">';
            
              //balanceorder
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="balanceorder_'.$lm->idurut.'" name="balanceorder_'.$lm->idurut.'" value="'.number_format($lm->balanceorder, 2, ',', '.').'" disabled  min="0">';
            
            $row[] = '';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_salesorderexternal_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_salesorderexternal_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntrySalesOrderExternal(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '')";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_postsales->q_salesorderexternal_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_postsales->q_salesorderexternal_dtl_temp($paramdtl);
        $cek2 = $this->m_postsales->q_salesorderexternal_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.salesorderexternal');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.3');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.B.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addSalesOrderExternal'));
        } else {
            // Ambil dari request POST
            $grosssales         = strtoupper(trim($this->request->getPost('grosssales')));
            $downpayment         = strtoupper(trim($this->request->getPost('downpayment')));
            $netsales         = strtoupper(trim($this->request->getPost('netsales')));
            $taxbasis         = strtoupper(trim($this->request->getPost('taxbasis')));
            $vat         = strtoupper(trim($this->request->getPost('vat')));
            $pph22         = strtoupper(trim($this->request->getPost('pph22')));
            $ttlprice         = strtoupper(trim($this->request->getPost('ttlprice')));

            $trader         = strtoupper(trim($this->request->getPost('trader')));
            $enduser         = strtoupper(trim($this->request->getPost('enduser')));
            $cust         = strtoupper(trim($this->request->getPost('cust')));
            $currency         = strtoupper(trim($this->request->getPost('currency')));
            $exchangerate         = strtoupper(trim($this->request->getPost('exchangerate')));
            $remark         = strtoupper(trim($this->request->getPost('remark')));
            $paymentmethod         = strtoupper(trim($this->request->getPost('paymentmethod')));
            $phone = strtoupper($this->request->getPost('phone'));
            $fax = strtoupper($this->request->getPost('fax'));
            $address = strtoupper($this->request->getPost('address'));
            $pic = strtoupper($this->request->getPost('pic'));


            $brand       = strtoupper(trim($this->request->getPost('brand')));
            $size        = strtoupper(trim($this->request->getPost('size')));
            $qty         = strtoupper(trim($this->request->getPost('qty')));
            $pembayaran  = strtoupper(trim($this->request->getPost('pembayaran')));
            $pengiriman  = strtoupper(trim($this->request->getPost('pengiriman')));
            $expdateph   = trim($this->request->getPost('expdateph')); // format dd-mm-yyyy
            $ketentuan   = strtoupper(trim($this->request->getPost('ketentuan')));

            $desc         = strtoupper(trim($this->request->getPost('desc')));

            // Convert expdate ke format YYYY-MM-DD
            $expdate = null;
            if (!empty($expdateph)) {
                $expdate = date('Y-m-d', strtotime(str_replace('-', '/', $expdateph)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'grosssales'         => $grosssales,
                'downpayment'         => $downpayment,
                'netsales'         => $netsales,
                'taxbasis'         => $taxbasis,
                'vat'         =>    $vat,
                'pph22'         => $pph22,
                'ttlprice'         => $ttlprice,

                'trader'      => $trader,
                'enduser'      => $enduser,
                'cust'      => $cust,
                'currency'      => $currency,
                'exchangerate'      => $exchangerate,
                'remark'      => $remark,
                'paymentmethod'      => $paymentmethod,
                'phone'      => $phone,
                'fax'      => $fax,
                'address'      => $address,
                'pic'      => $pic,


                'description'       => $desc,



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
                $paramerror=" and userid='$nama' and modul='I.S.B.3'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                // return redirect()->to(base_url('/sales/postsales/salesorderexternal'));
                return $this->response->setJSON([
                    'status' => 'success',
                    'redirect' => base_url('/sales/postsales/salesorderexternal')
                ]);
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.S.B.3',
                );
                $builder_trxerror->insert($infotrxerror);
                // return redirect()->to(base_url('/sales/postsales/addSalesOrderExternal'));
                return $this->response->setJSON([
                    'status' => 'error',
                    'redirect' => base_url('/sales/postsales/addSalesOrderExternal')
                ]);
            }



        }

    }

    function deleteSalesOrderExternal(){
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $builder = $this->db->table('sc_trx.salesorderexternal');
                
        // Update status menjadi 'C'
        $iupdate = array('status' => 'C');
        $builder->where('docno', $docno);
        if ($builder->update($iupdate)) {
            // // Hapus data dari tabel salesorderexternal dan salesorderexternaldtl sesuai docno dan status 'C'
            // $builder->where('docno', $docno); 
            // $builder->where('status', 'C');
            // $builder->delete();
            
            // // Menghapus data dari salesorderexternaldtl
            // $this->db->table('sc_trx.salesorderexternaldtl')
            //         ->where('docno', $docno)
            //         ->where('inputby', $nama)
            //         ->delete();

            return redirect()->to(base_url('sales/postsales/salesorderexternal') . '?status=success&message=Successfully data Canceled');
        } else {
            return redirect()->to(base_url('sales/postsales/salesorderexternal') . '?status=error&message=No updates provided or operation failed');
        }

    }


    function deleteSalesOrderExternalDtl(){
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
        $builder = $this->db->table('sc_tmp.salesorderexternaldtl');
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

    function show_salesorderexternal(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.salesorderexternal');

       $builder = $builder
            ->where('docno', $docno)
            ->update([
                'status'=> 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s')
            ]);

        
        $enc_docno = $this->fiky_encryption->sealed($docno);
        
        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Report Sales Order External";

        //$datajson =  base_url("manufactur/production/api_salesorderexternal/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_salesorderexternal/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_salesorderexternal.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_salesorderexternal_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_salesorderexternal(){
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
        $datamst = $this->m_postsales->q_salesorderexternal_master($param);
        $datadtl = $this->m_postsales->q_salesorderexternal_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;        
        if ($detail) {

            $tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
        
            // // Tambahkan properti baru isPindah
            // $detail->isPindah = false; // Default value
            // if ($tujuan === 'pindah') {
            //     $detail->isPindah = true;
            // }

            //  // Tambahkan properti baru isPembuangan
            //  $detail->isPembuangan = false; // Default value
            //  if ($tujuan === 'pembuangan') {
            //      $detail->isPembuangan = true;
            //  }

            // // Tambahkan properti baru isPinjam
            // $detail->isPinjam = false; // Default value
            // if ($tujuan === 'pinjam') {
            //     $detail->isPinjam = true;
            // }

            // $isreturn = isset($detail->isreturn) ? trim($detail->isreturn) : '';
            //  // Tambahkan properti baru iskembali
            //  $detail->iskembali = false; // Default value
            //  if ($isreturn === 'kembali') {
            //      $detail->iskembali = true;
            //  }

            //  $detail->istidakkembali = false; // Default value
            //  if ($isreturn === 'tidak_kembali') {
            //      $detail->istidakkembali = true;
            //  }

            //  $jenisbarang = isset($detail->jenisbarang) ? trim($detail->jenisbarang) : '';
            //   // Tambahkan properti baru isAset
            //   $detail->isAset = false; // Default value
            //   if ($jenisbarang === 'aset') {
            //       $detail->isAset = true;
            //   }

            //   // Tambahkan properti baru isPersediaan
            //   $detail->isPersediaan = false; // Default value
            //   if ($jenisbarang === 'persediaan') {
            //       $detail->isPersediaan = true;
            //   }

            //   // Tambahkan properti baru isLainlain
            //   $detail->isLainlain = false; // Default value
            //   if ($jenisbarang === 'lainlain') {
            //       $detail->isLainlain = true;
            //   }
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


    public function getRolePOSOE()
    {
        $jobcode = trim($this->request->getGet('rolejob'));
        $codemenu = 'I.S.B.3';
        $logindate = trim($this->session->get('logindate')); // format: dd-mm-yyyy

        // Buat infix dari logindate
        $infix = '';
        if (!empty($logindate)) {
            $ts = strtotime($logindate);
            $infix = date('ym', $ts); // contoh: 2508
        }

        // Mapping prefix default
        $prefixMap = [  
            'JTS'  => 'JTS-SO',
            'MSMI' => 'MSMI-SO',
            'MSMJ' => 'MSM-SO'
        ];
        $prefix = isset($prefixMap[$jobcode]) ? $prefixMap[$jobcode] : '';

        if (empty($prefix) || empty($infix)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role/Job atau logindate tidak valid'
            ]);
        }

        // Ambil suffix terakhir dari docno di sc_trx.salesorderexternal
        $builder = $this->db->table('sc_trx.salesorderexternal');
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

    public function getRate()
    {
        $curr = $this->request->getGet('currcode'); // mis. "USD"
        $date = $this->request->getGet('docdate'); // "2025-08-06"

        $builder = $this->db->table('sc_mst.currency');
        $builder->select('id');
        $builder->where('currcode', $curr);
        $row = $builder->get()->getRow();

        if (!$row) {
            return null; // currency tidak ditemukan
        }

        $idcurr = $row->id;

        // $model = new ExchangeRateModel();
        $nilai = $this->m_postsales->getRate($idcurr, $date);

        return $this->response->setJSON([
            'currency' => $curr,
            'date' => $date,
            'nilai' => $nilai ?? 1, // fallback
        ]);
    }



    
    // =================================== SALES ORDER ===========================================


     public function salesorder()
    {
        $data['title']="Sales Order";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.1'; $versirelease='I.S.B.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.1'";
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
        $dtl = $this->m_postsales->q_salesorder_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntrySalesOrder');
            $urlnext = base_url('sales/postsales/addSalesOrder');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.S.B.1';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_list_salesorder',$data);
    }

    function detailSalesOrder()
    {
        /* Penambahan Squence */
        $data['title']="Detail Sales Order";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/postsales/salesorder'));
        }
        $kodemenu='I.S.B.1'; $versirelease='I.S.B.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.1'";
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
        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_postsales->q_salesorder_master($param)->getRowArray();
        return $this->template->render('sales/postsales/v_detail_salesorder',$data);
    }

    function list_salesorder(){
        $list = $this->m_postsales->get_t_front_salesorder_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.S.B.1';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $approveBtn  = '';
            $disapproveBtn  = '';
            $cancelBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateSalesOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Sales Order : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Sales Order 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailSalesOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Sales Order : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Sales Order 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_salesorder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Preview Sales Order : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Preview Sales Order 
                </a>';
            }

            if($canDelete){
                $cancelBtn =  '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-trash"></i> Cancel</a>';
            }


            if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                    $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }

            


            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                    $menuContent .= $printBtn;
                }

            } else if($status === 'CANCEL'){
                $menuContent .= $detailBtn;
            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canApprove)   $menuContent .= $approveBtn;
                if ($canApprove)   $menuContent .= $disapproveBtn;
                if ($canDelete)   $menuContent .= $cancelBtn;
            }

            // =========================
            // Final Dropdown (jangan tampil kalau kosong)
            // =========================
            if ($menuContent !== '') {

                $dropdownMenu = '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu">
                            ' . $menuContent . '
                        </div>
                    </div>';

            } else {

                // Tidak punya hak akses apapun
                $dropdownMenu = '';
            }

            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
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
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success ';
                    break;
                case 'CANCEL':
                    $badgeClass = 'badge-danger ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $row[] = $lm->kdcustomer;
            $row[] = $lm->nmcustomer;
            $row[] = $lm->alamatcustomer;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d/m/Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;
            
            $row[] = $lm->nmsalesman;
            $row[] = $lm->pocust;
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_front_salesorder_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_salesorder_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    
    function list_salesorder_apprv(){
        $list = $this->m_postsales->get_t_front_salesorder_apprv_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.S.B.1';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $approveBtn  = '';
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateSalesOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Sales Order : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Sales Order 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailSalesOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Sales Order : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Sales Order 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Sales Order : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Sales Order 
                </a>';
            }


            if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                    $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }


            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                    $menuContent .= $printBtn;
                }

            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canApprove)   $menuContent .= $approveBtn;
                if ($canApprove)   $menuContent .= $disapproveBtn;
            }

            // =========================
            // Final Dropdown (jangan tampil kalau kosong)
            // =========================
            if ($menuContent !== '') {

                $dropdownMenu = '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu">
                            ' . $menuContent . '
                        </div>
                    </div>';

            } else {

                // Tidak punya hak akses apapun
                $dropdownMenu = '';
            }

            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
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
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $row[] = $lm->kdcustomer;
            $row[] = $lm->nmcustomer;
            $row[] = $lm->alamatcustomer;
            $row[] = $lm->nmkota;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d/m/Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;
            
            $row[] = $lm->nmsalesman;
            $row[] = $lm->pocust;
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_front_salesorder_apprv_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_salesorder_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntrySalesOrder()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_postsales->q_salesorder_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/postsales/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.salesorder');
        $builder_dtl = $this->db->table('sc_tmp.salesorder_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/postsales/salesorder'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/salesorder'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/salesorder'));
        }

    }

    function addSalesOrder()
    {
        /* Penambahan Squence */
        $data['title']="Input Sales Order";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.1'; $versirelease='I.S.B.1/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.B.1'";
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
        $data['mst'] = $this->m_postsales->q_salesorder_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_postsales->q_salesorder_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_add_salesorder',$data);
    }


   public function getBranchInfoSalesOrder()
    {
        $idbranch = trim($this->request->getGet('idbranch'));

        $row = $this->db->table('sc_mst.branchjob')
            ->select('nmbranch')
            ->where('idbranch', $idbranch)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang tidak ditemukan'
            ]);
        }

        // mapping nmbranch → kode suffix
        $map = [
            'PT JATIM TAMAN STEEL MFG' => 'PT',
            'PLANT I'                 => 'PA',
            'PLANT II'                => 'PB',
        ];

        $kodeSuffix = $map[trim($row['nmbranch'])] ?? '';

        if ($kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mapping cabang belum diset'
            ]);
        }

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix
        ]);
    }

    public function getNextSuffixSalesOrder()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.salesorder')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }

    public function initSalesOrderHeader()
    {
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper($this->request->getPost('docno'));
        $docdate    = $this->request->getPost('docdate');
        $cabang     = $this->request->getPost('cabang');
        $pemohon    = strtoupper($this->request->getPost('pemohon'));
        // $estpakai   = $this->request->getPost('estpakai');
        // $keterangan = strtoupper($this->request->getPost('keterangan'));

        if (!$docno || !$docdate || !$cabang) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Header belum lengkap'
            ]);
        }

        $builder = $this->db->table('sc_tmp.salesorder');
        $exists = $builder->where('docno', $docno)->countAllResults();

        // HEADER SUDAH ADA → TIDAK PERLU RELOAD
        if ($exists > 0) {
            return $this->response->setJSON([
                'success' => true,
                'reload'  => false
            ]);
        }

        // HEADER BARU → INSERT
        $builder->insert([
            'docno'      => $docno,
            'docdate'    => $docdate,
            'cabang'     => $cabang,
            'pemohon'    => $pemohon,
            // 'estpakai'   => $estpakai,
            'status'     => 'E',
            // 'keterangan' => $keterangan,
            'inputby'    => $nama,
            'inputdate'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'reload'  => true   // ⬅ PENTING
        ]);
    }



    public function saveSalesOrderDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        // $docnopo = strtoupper(trim($this->request->getPost('docnopo')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya
        
        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No. Jurnal tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.salesorder');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST
        
        if ($exists == 0) {
            $isinclusive = strtoupper(trim(
                $this->request->getPost('isinclusive') 
                ?? $dataprocess->isinclusive 
                ?? 'NO'
            ));

            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $isopenprice = strtoupper(trim(
                $this->request->getPost('isopenprice') 
                ?? $dataprocess->isopenprice 
                ?? 'NO'
            ));

            $isopenprice = ($isopenprice === 'YES') ? 'YES' : 'NO';

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                'delivdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('delivdate')))),
                'jthtempo'     => $this->request->getPost('jthtempo'),
                'isinclusive'     => $isinclusive,
                'isopenprice'     => $isopenprice,
                
                'kdcustomer'    => strtoupper($this->request->getPost('kdcustomer')),
                'alamatcustomer'    => strtoupper($this->request->getPost('alamatcustomer')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                'idtax'    => strtoupper($this->request->getPost('idtax')),
                'kdsalesman'    => ($this->request->getPost('kdsalesman')),
                'gradecustomer'    => ($this->request->getPost('gradecustomer')),
                'currcode'    => strtoupper($this->request->getPost('currcode')),
                'kurs'    => ($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                'pocust'    => strtoupper($this->request->getPost('pocust')),
                'nodp'    => strtoupper($this->request->getPost('nodp')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.salesorder_dtl');
        // $insertCount = 0;
        $message = '';

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {
            $uniqueid = $this->request->getPost('uniqueid');
            // =====================================================
            // MODE EDIT - UPDATE DATA
            // =====================================================
            $qty         = $this->request->getPost('qty');
            // $qtybonus    = $this->request->getPost('qtybonus') ?: 0;
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            // $volitem   = $this->request->getPost('volitem') ?: 0;
            // $biaya   = $this->request->getPost('biaya') ?: 0;
            // $biaya2   = $this->request->getPost('biaya2') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $bomdesc = strtoupper($this->request->getPost('bomdesc'));
            $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang = strtoupper($this->request->getPost('idgudang'));
            $idspec = strtoupper($this->request->getPost('idspec'));


            // Ambil kurs dari header SO
            $soHeader = $builderHeader->select('kurs, idtax')->where('docno', $docno)->get()->getRowArray();
            $kurs = $soHeader['kurs'] ?? 0;
            $idtax = $soHeader['idtax'] ?? '';
            
            // Hitung nilaikonversi = nilai * kurs
            $nilaikonversi = $nilai * $kurs;
            
            // Hitung nilaipajak berdasarkan idtax
            $nilaipajak = 0;
            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                // Ambil detail tax dari sc_mst.tax_dtl
                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();
                
                $totalPersentase = 0;
                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $totalPersentase += $persentase;
                }
                
                // Hitung nilaipajak = nilai + (nilai * totalPersentase / 100)
                // $nilaipajak = $nilai + ($nilai * $totalPersentase / 100);
                $nilaipajak = $nilai * $totalPersentase / 100;

            } else {
                // Jika NON pajak, nilaipajak sama dengan nilai
                $nilaipajak = $nilai;
            }

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                // 'qtybonus'     => $qtybonus,
                'harga'        => $harga,
                'multidisc'    => $multidisc,
                'nilai'        => $nilai,
                'nilaikonversi' => $nilaikonversi,  // Tambahkan ini
                'nilaipajak'   => $nilaipajak,      // Tambahkan ini
                'kurs'          => strtoupper($this->request->getPost('kurs')),
                'idtax'         => strtoupper($this->request->getPost('idtax')),
                'currcode'      => strtoupper($this->request->getPost('currcode')),
                // 'volitem'      => $volitem,

                // 'biaya'      => $biaya,
                // 'biaya2'      => $biaya2,
                'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                'idspec'      => $idspec,

                'bomdesc' => $bomdesc,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);



            
            
            $message = 'Data berhasil diupdate';
            
        } else {

            // =====================================================
            // MODE ADD - INSERT DATA DARI PP
            // =====================================================
            $idbarang    = $this->request->getPost('idbarang');
            $nmbarang    = strtoupper($this->request->getPost('nmbarang'));
            $unit        = strtoupper($this->request->getPost('unit'));
            $qty         = $this->request->getPost('qty');
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $bomdesc = strtoupper($this->request->getPost('bomdesc'));
            $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang = strtoupper($this->request->getPost('idgudang'));
            $idspec = strtoupper($this->request->getPost('idspec'));
            
            
            // Ambil kurs dari header SO
            $soHeader = $builderHeader->select('kurs, idtax')->where('docno', $docno)->get()->getRowArray();
            $kurs = $soHeader['kurs'] ?? 0;
            $idtax = $soHeader['idtax'] ?? '';
            
            // Hitung nilaikonversi = nilai * kurs
            $nilaikonversi = $nilai * $kurs;
            
            // Hitung nilaipajak berdasarkan idtax
            $nilaipajak = 0;
            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                // Ambil detail tax dari sc_mst.tax_dtl
                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();
                
                $totalPersentase = 0;
                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $totalPersentase += $persentase;
                }
                
                // Hitung nilaipajak = nilai + (nilai * totalPersentase / 100)
                // $nilaipajak = $nilai + ($nilai * $totalPersentase / 100);
                $nilaipajak = $nilai * $totalPersentase / 100;

            } else {
                // Jika NON pajak, nilaipajak sama dengan nilai
                $nilaipajak = $nilai;
            }
            


            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $nmbarang 
            . '|' . $docno 
            . '|' . $nama
            . '|' . $inputdate;

            $uniqueid  = hash('sha256', $rawUnique);


            // 🔹 INSERT
            $builderDetail->insert([
                'docno'       => $docno,
                'idbarang'    => $idbarang,
                'nmbarang'    => $nmbarang,
                'unit'        => $unit,
                'qty'         => $qty,
                'multidisc'         => $multidisc,
                'harga'         => $harga,
                'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                'idspec'      => $idspec,
                'nilai'         => $nilai,
                'nilaipajak'         => $nilaipajak,
                'nilaikonversi'         => $nilaikonversi,
                'kurs'          => strtoupper($this->request->getPost('kurs')),
                'idtax'         => strtoupper($this->request->getPost('idtax')),
                'currcode'      => strtoupper($this->request->getPost('currcode')),
                'bomdesc' => $bomdesc,
                'status'      => 'F',
                'inputby'     => $nama,
                'inputdate'   => date('Y-m-d H:i:s'),
                'uniqueid'    => $uniqueid
            ]);
            
            $message = "item berhasil ditambahkan";
        }

        $salesorderHeader = $builderHeader->select('idtax')
        ->where('docno', $docno)
        ->where('inputby', $nama)
        ->get()->getRowArray();

        $idtax = $salesorderHeader['idtax'] ?? '';
        
        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.salesorder_dtl');
        $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();
        
        $dpp = $totalDpp['total_dpp'] ?? 0;
        
        // Hitung jumlah pajak berdasarkan idtax
        $jumlahPajak = 0;
        
        if (!empty($idtax) && trim($idtax) !== 'NON'  && $dpp > 0) {
            // Ambil detail tax dari sc_mst.tax_dtl
            $builderTaxDtl = $db->table('sc_mst.tax_dtl');
            $taxDetails = $builderTaxDtl->select('percentation')
                ->where('idtax', $idtax)
                ->get()
                ->getResultArray();
            
            foreach ($taxDetails as $tax) {
                $persentase = $tax['percentation'] ?? 0;
                $jumlahPajak += $dpp * ($persentase / 100);
            }
        }
        
        // Hitung total (DPP + Jumlah Pajak)
        $total = $dpp + $jumlahPajak;
        
        // Update header SalesOrder
        $builderHeader->where('docno', $docno)->where('inputby', $nama)->update([
            'dpp' => number_format($dpp, 2, '.', ''),
            'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
            'updateby' => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusSalesOrder()
    {
        $docno = $this->request->getPost('docno');
        $status = $this->request->getPost('status');
        if (!$docno || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_trx.salesorder');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function updateSalesOrder()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_postsales->q_salesorder_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.salesorder');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/postsales/addSalesOrder'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/postsales/salesorder'));
        }
    }

    function showing_salesordertrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_salesorder_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_salesordertemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_salesorder_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_salesorder_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_postsales->q_salesorder_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_salesorder_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.salesorder_dtl')
            ->where('idurut', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $row
        ]);
    }

    public function delete_salesorder_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.salesorder_dtl');
        $nama    = trim($this->session->get('nama'));

        $ids = $request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // ======================================
            // AMBIL DOCNO DARI DETAIL
            // ======================================
            $rows = $builder
                ->select('docno')
                ->whereIn('idurut', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // karena Anda yakin semua docno sama
            $docno = $rows[0]['docno'];

            // OPTIONAL VALIDATION (lebih aman)
            foreach ($rows as $r) {
                if ($r['docno'] !== $docno) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Docno tidak konsisten'
                    ]);
                }
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
            $builder
                ->whereIn('idurut', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // ======================================
            // RECALCULATE HEADER
            // ======================================
            $builderHeader = $db->table('sc_tmp.salesorder');

            $salesorderHeader = $builderHeader
                ->select('idtax')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $idtax = $salesorderHeader['idtax'] ?? '';

            $builderTotalDpp = $db->table('sc_tmp.salesorder_dtl');
            $totalDpp = $builderTotalDpp
                ->select('COALESCE(SUM(nilai), 0) as total_dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $dpp = $totalDpp['total_dpp'] ?? 0;

            $jumlahPajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {

                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $jumlahPajak += $dpp * ($persentase / 100);
                }
            }

            $total = $dpp + $jumlahPajak;

            $builderHeader
                ->where('docno', $docno)
                ->update([
                    'dpp'         => number_format($dpp, 2, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                    'total'       => number_format($total, 2, '.', ''),
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data PO Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function list_tmp_salesorder_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_salesorder_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            // $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '% </div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            $row[] = $lm->bomdesc;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_salesorder_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_salesorder_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_salesorder_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_salesorder_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            // $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            $row[] = $lm->bomdesc;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_salesorder_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_salesorder_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntrySalesOrder(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR harga = '0.00' OR harga = '0' OR nilai = '0.00' OR nilai = '0') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_postsales->q_salesorder_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_postsales->q_salesorder_dtl_temp($paramdtl);
        $cek2 = $this->m_postsales->q_salesorder_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.salesorder');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.1');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.B.1',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addSalesOrder'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            $delivdate   = trim($this->request->getPost('delivdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdcustomer   = trim($this->request->getPost('kdcustomer'));
            $alamatcustomer   = trim($this->request->getPost('alamatcustomer'));
            $gradecustomer   = trim($this->request->getPost('gradecustomer'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            // $keterangan   = trim($this->request->getPost('keterangan'));
            $currcode   = trim($this->request->getPost('currcode'));
            $salesman   = trim($this->request->getPost('kdsalesman'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = strtoupper(trim($this->request->getPost('keterangan')));
            $nodp   = trim($this->request->getPost('nodp'));
            $pocust   = trim($this->request->getPost('pocust'));
            $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';
            $isopenprice = $this->request->getPost('isopenprice') ? 'YES' : 'NO';


            
            // **BERSIHKAN FORMAT KURS**
            $kurs = trim($this->request->getPost('kurs'));
            $kurs_clean = 0;
            if (!empty($kurs)) {
                $kurs_clean = str_replace(',', '', $kurs);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }

             // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            $delivdateph = null;
            if (!empty($delivdate)) {
                $delivdateph = date('Y-m-d', strtotime(str_replace('-', '/', $delivdate)));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                'delivdate'       => $delivdateph,
                'jthtempo'       => $jthtempo,
                'kdcustomer'     => strtoupper($kdcustomer),
                'alamatcustomer' => strtoupper($alamatcustomer),
                'gradecustomer' => strtoupper($gradecustomer),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => $keterangan,
                'currcode'       => $currcode,
                'kdsalesman'       => $salesman,
                'kurs'           => $kurs_clean,
                'isinclusive'    => strtoupper($isinclusive),
                'isopenprice'    => strtoupper($isopenprice),
                'idtax'          => strtoupper($idtax),
                // 'keterangan'         => strtoupper($keterangan),
                'pocust'         => strtoupper($pocust),
                'nodp'         => strtoupper($nodp),
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.S.B.1'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/sales/postsales/salesorder'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.S.B.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addSalesOrder'));
            }



        }

    }


    function show_salesorder(){
         $module = "Sales Order";
        $table = "sc_trx.salesorder";
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.salesorder');

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

        $title = " Report Sales Order";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_salesorder/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_salesorder.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno);
    }

    function api_salesorder(){
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
        $datamst = $this->m_postsales->q_salesorder_master($param);
        $datadtl = $this->m_postsales->q_salesorder_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;        
        if ($detail) {

            $kdcustomer = trim($detail->kdcustomer);

            $customer = $this->db->query("
                SELECT nmcustomer 
                FROM sc_mst.customer 
                WHERE TRIM(kdcustomer) = ?
                LIMIT 1
            ", [$kdcustomer])->getRow();

            // 🔹 Set ke object detail
            $detail->nmcustomerdata = $customer->nmcustomer ?? '';
            $detail->namauser = $nama;

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





    // =================================== PENJUALAN ===========================================


     public function penjualan()
    {
        $data['title']="Penjualan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.2'; $versirelease='I.S.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.2'";
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
        $dtl = $this->m_postsales->q_penjualan_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntryPenjualan');
            $urlnext = base_url('sales/postsales/addPenjualan');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.S.B.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();        
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_list_penjualan',$data);
    }

    function detailPenjualan()
    {
        /* Penambahan Squence */
        $data['title']="Detail Penjualan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/postsales/penjualan'));
        }
        $kodemenu='I.S.B.2'; $versirelease='I.S.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.2'";
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
        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_postsales->q_penjualan_master($param)->getRowArray();
        return $this->template->render('sales/postsales/v_detail_penjualan',$data);
    }

    function list_penjualan(){
        $list = $this->m_postsales->get_t_front_penjualan_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.S.B.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';
        // $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $approveBtn  = '';
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updatePenjualan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Penjualan : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Penjualan 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailPenjualan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Penjualan : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Penjualan 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Penjualan : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Penjualan 
                </a>';
            }


            // if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
            //         $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
            //             <i class="fa fa-check-circle"></i> Approve</a>';
            // }

            // if (trim($status) == 'APPROVED') {
            //     $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
            //         <i class="fa fa-times-circle"></i> Disapprove</a>';
            // }


            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                    $menuContent .= $printBtn;
                }

            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                // if ($canApprove)   $menuContent .= $approveBtn;
                // if ($canApprove)   $menuContent .= $disapproveBtn;
            }

            // =========================
            // Final Dropdown (jangan tampil kalau kosong)
            // =========================
            if ($menuContent !== '') {

                $dropdownMenu = '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu">
                            ' . $menuContent . '
                        </div>
                    </div>';

            } else {

                // Tidak punya hak akses apapun
                $dropdownMenu = '';
            }

            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
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
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $row[] = $lm->kdcust;
            $row[] = $lm->nmcust;
            $row[] = $lm->alamatcust;
            $row[] = $lm->nmkota;
            // $row[] = $lm->kdcustdeliv;
            $row[] = $lm->nmcustdeliv;
            $row[] = $lm->alamatcustdeliv;
            $row[] = $lm->nmkotadeliv;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d/m/Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;
            
            $row[] = $lm->nmsalesman;
            // $row[] = $lm->pocust;
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_front_penjualan_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_penjualan_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    
    function list_penjualan_apprv(){
        $list = $this->m_postsales->get_t_front_penjualan_apprv_view();
        $data = array();
        $no = $_POST['start'];


        $kmenu = 'I.S.B.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $docno  = trim($lm->docno);
            $docnoHex = bin2hex($docno);

            
            $updateBtn = '';
            $detailBtn = '';
            $printBtn  = '';
            $approveBtn  = '';
            $disapproveBtn  = '';

            // =========================
            // Build button by access
            // =========================

            if ($canUpdate && $status != "REVISION/EDITING" && $status != "APPROVED") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updatePenjualan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This Penjualan : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update Penjualan 
                </a>';
            }

            if($canView){
                $detailBtn = 
                '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailPenjualan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail Penjualan : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail Penjualan 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print Penjualan : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print Penjualan 
                </a>';
            }


            if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
                    $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-check-circle"></i> Approve</a>';
            }

            if (trim($status) == 'APPROVED') {
                $disapproveBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToDisapproved(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-times-circle"></i> Disapprove</a>';
            }


            $menuContent = '';

            if ($status === 'CETAK/PRINT') {

                // hanya detail jika ada akses
                if ($canView) {
                    $menuContent .= $detailBtn;
                    $menuContent .= $printBtn;
                }

            } else {

                // selain status tersebut → tampilkan sesuai hak akses
                if ($canUpdate) $menuContent .= $updateBtn;
                if ($canPrint)  $menuContent .= $printBtn;
                if ($canView)   $menuContent .= $detailBtn;
                if ($canApprove)   $menuContent .= $approveBtn;
                if ($canApprove)   $menuContent .= $disapproveBtn;
            }

            // =========================
            // Final Dropdown (jangan tampil kalau kosong)
            // =========================
            if ($menuContent !== '') {

                $dropdownMenu = '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu">
                            ' . $menuContent . '
                        </div>
                    </div>';

            } else {

                // Tidak punya hak akses apapun
                $dropdownMenu = '';
            }

            $row[] = $no;
            $row[] = $dropdownMenu;

            $row[] = $lm->docno;
            $row[] = date(
                'd/m/Y',
                strtotime(trim($lm->docdate))
            );
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
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-success ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';

            $row[] = $lm->kdcust;
            $row[] = $lm->nmcust;
            $row[] = $lm->alamatcust;
            $row[] = $lm->nmkota;
            $row[] = $lm->kdcustdeliv;
            $row[] = $lm->nmcustdeliv;
            $row[] = $lm->alamatcustdeliv;
            $row[] = $lm->nmkotadeliv;
            $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            $docdate  = trim($lm->docdate);
            $jthtempo = (int) $lm->jthtempo;

            if (!empty($docdate)) {

                $date = new \DateTime(trim($lm->docdate));
                $date->modify("+{$jthtempo} days");

                $jatuhTempo = $date->format('d/m/Y');

            } else {
                $jatuhTempo = '';
            }

            $row[] = $jatuhTempo;
            
            $row[] = $lm->nmsalesman;
            // $row[] = $lm->pocust;
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;
            

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_front_penjualan_apprv_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_penjualan_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryPenjualan()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_postsales->q_penjualan_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/postsales/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.penjualan');
        $builder_dtl = $this->db->table('sc_tmp.penjualan_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/postsales/penjualan'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/penjualan'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/penjualan'));
        }

    }

    function addPenjualan()
    {
        /* Penambahan Squence */
        $data['title']="Input Penjualan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.2'; $versirelease='I.S.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.B.2'";
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
        $data['mst'] = $this->m_postsales->q_penjualan_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_postsales->q_penjualan_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_add_penjualan',$data);
    }


   public function getBranchInfoPenjualan()
    {
        $idbranch = trim($this->request->getGet('idbranch'));

        $row = $this->db->table('sc_mst.branchjob')
            ->select('nmbranch')
            ->where('idbranch', $idbranch)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cabang tidak ditemukan'
            ]);
        }

        // mapping nmbranch → kode suffix
        $map = [
            'PT JATIM TAMAN STEEL MFG' => 'PT',
            'PLANT I'                 => 'PA',
            'PLANT II'                => 'PB',
        ];

        $kodeSuffix = $map[trim($row['nmbranch'])] ?? '';

        if ($kodeSuffix === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mapping cabang belum diset'
            ]);
        }

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix
        ]);
    }

    public function getNextSuffixPenjualan()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.penjualan')
            ->select('docno')
            ->like('docno', $like, 'after')
            ->orderBy('docno', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($row) {
            $parts = explode('/', $row['docno']);
            $last  = substr($parts[2], 2); // ambil angka setelah PT/PA/PB
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return $this->response->setJSON([
            'success' => true,
            'suffix'  => $kodeSuffix . $next
        ]);
    }

    public function initPenjualanHeader()
    {
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper($this->request->getPost('docno'));
        $docdate    = $this->request->getPost('docdate');
        $cabang     = $this->request->getPost('cabang');
        $pemohon    = strtoupper($this->request->getPost('pemohon'));
        // $estpakai   = $this->request->getPost('estpakai');
        // $keterangan = strtoupper($this->request->getPost('keterangan'));

        if (!$docno || !$docdate || !$cabang) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Header belum lengkap'
            ]);
        }

        $builder = $this->db->table('sc_tmp.penjualan');
        $exists = $builder->where('docno', $docno)->countAllResults();

        // HEADER SUDAH ADA → TIDAK PERLU RELOAD
        if ($exists > 0) {
            return $this->response->setJSON([
                'success' => true,
                'reload'  => false
            ]);
        }

        // HEADER BARU → INSERT
        $builder->insert([
            'docno'      => $docno,
            'docdate'    => $docdate,
            'cabang'     => $cabang,
            'pemohon'    => $pemohon,
            // 'estpakai'   => $estpakai,
            'status'     => 'E',
            // 'keterangan' => $keterangan,
            'inputby'    => $nama,
            'inputdate'  => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'reload'  => true   // ⬅ PENTING
        ]);
    }



    public function savePenjualanDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $docnoso = strtoupper(trim($this->request->getPost('docnoso')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya
        
        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno || !$docnoso) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No. Jurnal tidak boleh kosong'
            ]);
        }

        $db = $this->db;
        $db->transStart();

        $builderSO = $db->table('sc_trx.salesorder');
        $soData = $builderSO
            ->select('currcode, kurs, idtax, isinclusive')
            ->where('docno', $docnoso)
            ->get()
            ->getRowArray();

        // Jika data SO tidak ditemukan, beri response error
        if (!$soData) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => "Data SO dengan nomor {$docnoso} tidak ditemukan"
            ]);
        }


        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.penjualan');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST
        
        if ($exists == 0) {
            $isinclusive = strtoupper(trim(
                $this->request->getPost('isinclusive') 
                ?? $dataprocess->isinclusive 
                ?? 'NO'
            ));

            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $isopenprice = strtoupper(trim(
                $this->request->getPost('isopenprice') 
                ?? $dataprocess->isopenprice 
                ?? 'NO'
            ));

            $isopenprice = ($isopenprice === 'YES') ? 'YES' : 'NO';

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                // 'delivdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('delivdate')))),
                'jthtempo'     => $this->request->getPost('jthtempo'),
                'isinclusive'     => $isinclusive,
                'isopenprice'     => $isopenprice,
                
                'kdcustomer'    => strtoupper($this->request->getPost('kdcustomer')),
                'alamatcustomer'    => strtoupper($this->request->getPost('alamatcustomer')),
                'kdcustomerdeliv'    => strtoupper($this->request->getPost('kdcustomerdeliv')),
                'alamatcustomerdeliv'    => strtoupper($this->request->getPost('alamatcustomerdeliv')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                'idtax'    => strtoupper($this->request->getPost('idtax')),
                'kdsalesman'    => ($this->request->getPost('kdsalesman')),
                'gradecustomer'    => ($this->request->getPost('gradecustomer')),
                'carabayar'    => strtoupper($this->request->getPost('carabayar')),
                // 'pocust'    => strtoupper($this->request->getPost('pocust')),
                'currcode'    => strtoupper($this->request->getPost('currcode')),
                'kurs'    => ($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                // 'pocust'    => strtoupper($this->request->getPost('pocust')),
                // 'nodp'    => strtoupper($this->request->getPost('nodp')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.penjualan_dtl');
        $insertCount = 0;
        $message = '';

        // CEK MODE: ADD atau EDIT
        if (!empty($idurut)) {            

            $uniqueid = $this->request->getPost('uniqueid');
            // =====================================================
            // MODE EDIT - UPDATE DATA
            // =====================================================
            $qty         = $this->request->getPost('qty');
            // $qtybonus    = $this->request->getPost('qtybonus') ?: 0;
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            // $volitem   = $this->request->getPost('volitem') ?: 0;
            // $biaya   = $this->request->getPost('biaya') ?: 0;
            // $biaya2   = $this->request->getPost('biaya2') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $description = strtoupper($this->request->getPost('description'));
            $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang = strtoupper($this->request->getPost('idgudang'));
            $idspec = strtoupper($this->request->getPost('idspec'));

            $nilai = $qty * $harga;
            $h = $db->table('sc_tmp.penjualan')
                ->select('kurs,idtax')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $kurs = $h['kurs'] ?? 1;
            $idtax = $h['idtax'] ?? 'NON';
            $nilaikonversi = $nilai * $kurs;
            $nilaipajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                $taxDetails = $db->table('sc_mst.tax_dtl')
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
                // $nilaipajak = $nilai + ($nilai * $totalPersen / 100);
                $nilaipajak = $nilai * $totalPersen / 100;
            }

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                // 'qtybonus'     => $qtybonus,
                'harga'        => $harga,
                'multidisc'    => $multidisc,
                'nilai'        => $nilai,
                'nilaikonversi' => $nilaikonversi,
                'nilaipajak' => $nilaipajak,
                'idtax' => $idtax,
                'kurs' => $kurs,
                'currcode' => $poData['currcode'] ?? '',
                // 'volitem'      => $volitem,

                // 'biaya'      => $biaya,
                // 'biaya2'      => $biaya2,
                'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                'idspec'      => $idspec,

                'description' => $description,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ]);



            
            
            $message = 'Data berhasil diupdate';
            
        } else {
            // =====================================================
            // MODE ADD - INSERT DATA DARI PP
            // =====================================================
            $soDetails = $db->query("
                SELECT 
                    docno,
                    idbarang,
                    uniqueid,
                    nmbarang,
                    unit,
                    qty,
                    idprincipal,
                    idgudang,
                    idspec,
                    nilaipajak,
                    nilaikonversi,
                    multidisc,
                    qtypenjualan,
                    harga,
                    currcode,
                    idtax,
                    kurs,
                    nilai
                FROM sc_trx.salesorder_dtl
                WHERE TRIM(docno) = ?
            ", [$docnoso])->getResult();

            if (empty($soDetails)) {
                $db->transRollback();   
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data SO tidak ditemukan'
                ]);
            }

            foreach ($soDetails as $row) {
                $sisaQty = $row->qty - ($row->qtypenjualan);
                if ($sisaQty <= 0) continue;

                $duplicate = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                $nilaiRow = $row->qty * $row->harga;
                $h = $db->table('sc_tmp.penjualan')
                    ->select('kurs,idtax')
                    ->where('docno', $docno)
                    ->get()
                    ->getRowArray();

                $kurs = $h['kurs'] ?? 1;
                $idtaxRow = $h['idtaxRow'] ?? 'NON';
                $nilaikonversiRow = $nilaiRow * $kurs;
                $nilaipajakRow = 0;

                if (!empty($idtaxRow) && trim($idtaxRow) !== 'NON' && $nilaiRow > 0) {
                    $taxDetails = $db->table('sc_mst.tax_dtl')
                        ->select('percentation')
                        ->where('idtax', $idtaxRow)
                        ->get()
                        ->getResultArray();

                    $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
                    // $nilaipajakRow = $nilai + ($nilai * $totalPersen / 100);
                    $nilaipajakRow = $nilaiRow * $totalPersen / 100;
                }

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnoso'       => $docnoso,
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $sisaQty,
                        'idprincipal'   => $row->idprincipal,
                        'idgudang'      => $row->idgudang,
                        'idspec'        => $row->idspec,
                        'harga'         => $row->harga, // Default 0 untuk new insert
                        'multidisc'     =>  $row->multidisc, // Default 0 untuk new insert
                        'nilaipajak'    => $nilaipajakRow,
                        'nilaikonversi' => $nilaikonversiRow,
                        'currcode'      => $row->currcode,
                        'kurs'          => $row->kurs,
                        'idtax'         => $row->idtax,
                        'nilai'         => $sisaQty * $row->harga, // Default 0 untuk new insert
                        // 'description' => $row->description,
                        'inputby'       => $nama,
                        'inputdate'     => date('Y-m-d H:i:s')
                    ]);

                    $insertCount++;
                }
            }
            
            $message = $insertCount > 0 
                        ? "$insertCount item berhasil ditambahkan"
                        : "Semua item sudah ada sebelumnya";
        }

        $penjualanHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
        $idtax = $penjualanHeader['idtax'] ?? '';
        
        // Hitung total DPP (sum nilai dari po_dtl)
        $builderTotalDpp = $db->table('sc_tmp.penjualan_dtl');
        $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->get()
            ->getRowArray();
        
        $dpp = $totalDpp['total_dpp'] ?? 0;
        
        // Hitung jumlah pajak berdasarkan idtax
        $jumlahPajak = 0;
        
        if (!empty($idtax) && trim($idtax) !== 'NON'  && $dpp > 0) {
            // Ambil detail tax dari sc_mst.tax_dtl
            $builderTaxDtl = $db->table('sc_mst.tax_dtl');
            $taxDetails = $builderTaxDtl->select('percentation')
                ->where('idtax', $idtax)
                ->get()
                ->getResultArray();
            
            foreach ($taxDetails as $tax) {
                $persentase = $tax['percentation'] ?? 0;
                $jumlahPajak += $dpp * ($persentase / 100);
            }
        }
        
        // Hitung total (DPP + Jumlah Pajak)
        $total = $dpp + $jumlahPajak;
        
        // Update header LPB
        $builderHeader->where('docno', $docno)->update([
            'dpp' => number_format($dpp, 2, '.', ''),
            'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
            'updateby' => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusPenjualan()
    {
        $docno = $this->request->getPost('docno');
        $status = $this->request->getPost('status');
        if (!$docno || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sc_trx.penjualan');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        if ($update) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function updatePenjualan()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_postsales->q_penjualan_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.penjualan');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/postsales/addPenjualan'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/postsales/penjualan'));
        }
    }

    function showing_penjualantrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_penjualan_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_penjualantemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_penjualan_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_penjualan_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_postsales->q_penjualan_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_penjualan_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.penjualan_dtl')
            ->where('idurut', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $row
        ]);
    }

    public function delete_penjualan_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.penjualan_dtl');
        $nama    = trim($this->session->get('nama'));

        $ids = $request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter ids tidak boleh kosong'
            ]);
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $db->transBegin();

        try {

            // ======================================
            // AMBIL DOCNO DARI DETAIL
            // ======================================
            $rows = $builder
                ->select('docno')
                ->whereIn('idurut', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // karena Anda yakin semua docno sama
            $docno = $rows[0]['docno'];

            // OPTIONAL VALIDATION (lebih aman)
            foreach ($rows as $r) {
                if ($r['docno'] !== $docno) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'status'  => false,
                        'message' => 'Docno tidak konsisten'
                    ]);
                }
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
            $builder
                ->whereIn('idurut', $ids)
                ->delete();

            if ($db->affectedRows() === 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // ======================================
            // RECALCULATE HEADER
            // ======================================
            $builderHeader = $db->table('sc_tmp.penjualan');

            $penjualanHeader = $builderHeader
                ->select('idtax')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $idtax = $penjualanHeader['idtax'] ?? '';

            $builderTotalDpp = $db->table('sc_tmp.penjualan_dtl');
            $totalDpp = $builderTotalDpp
                ->select('COALESCE(SUM(nilai), 0) as total_dpp')
                ->where('docno', $docno)
                ->get()
                ->getRowArray();

            $dpp = $totalDpp['total_dpp'] ?? 0;

            $jumlahPajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {

                $builderTaxDtl = $db->table('sc_mst.tax_dtl');
                $taxDetails = $builderTaxDtl
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                foreach ($taxDetails as $tax) {
                    $persentase = $tax['percentation'] ?? 0;
                    $jumlahPajak += $dpp * ($persentase / 100);
                }
            }

            $total = $dpp + $jumlahPajak;

            $builderHeader
                ->where('docno', $docno)
                ->update([
                    'dpp'         => number_format($dpp, 2, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 2, '.', ''),
                    'total'       => number_format($total, 2, '.', ''),
                    'updateby'    => $nama,
                    'updatedate'  => date('Y-m-d H:i:s')
                ]);

            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data PO Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function list_tmp_penjualan_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_penjualan_dtl_temp_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnosj;
            $row[] = $lm->docnoso;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '% </div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_penjualan_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_penjualan_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_penjualan_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_penjualan_dtl_view($docno);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $lm->docnosj;
            $row[] = $lm->docnoso;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 0, '.', ',') . '</div>';
            $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;   
            
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_penjualan_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_penjualan_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryPenjualan(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '' OR harga = '0.00' OR harga = '0' OR nilai = '0.00' OR nilai = '0') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_postsales->q_penjualan_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_postsales->q_penjualan_dtl_temp($paramdtl);
        $cek2 = $this->m_postsales->q_penjualan_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.penjualan');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.2');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.B.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addPenjualan'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdcustomer   = trim($this->request->getPost('kdcustomer'));
            $alamatcustomer   = trim($this->request->getPost('alamatcustomer'));
            $gradecustomer   = trim($this->request->getPost('gradecustomer'));
            $kdcustomerdeliv   = trim($this->request->getPost('kdcustomerdeliv'));
            $alamatcustomerdeliv   = trim($this->request->getPost('alamatcustomerdeliv'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            // $keterangan   = trim($this->request->getPost('keterangan'));
            $currcode   = trim($this->request->getPost('currcode'));
            $salesman   = trim($this->request->getPost('kdsalesman'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            $carabayar   = trim($this->request->getPost('carabayar'));
            // $pocust   = trim($this->request->getPost('pocust'));
            $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';
            $isopenprice = $this->request->getPost('isopenprice') ? 'YES' : 'NO';


            
            // **BERSIHKAN FORMAT KURS**
            $kurs = trim($this->request->getPost('kurs'));
            $kurs_clean = 0;
            if (!empty($kurs)) {
                $kurs_clean = str_replace(',', '', $kurs);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }

             // Convert expdate ke format YYYY-MM-DD
            $docdateph = null;
            if (!empty($docdate)) {
                $docdateph = date('Y-m-d', strtotime(str_replace('-', '/', $docdate)));
            }

            // $delivdateph = null;
            // if (!empty($delivdate)) {
            //     $delivdateph = date('Y-m-d', strtotime(str_replace('-', '/', $delivdate)));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                'docdate'        => $docdateph,
                // 'delivdate'       => $delivdateph,
                'jthtempo'       => $jthtempo,
                'kdcustomer'     => strtoupper($kdcustomer),
                'alamatcustomer' => strtoupper($alamatcustomer),
                'gradecustomer' => strtoupper($gradecustomer),
                'kdcustomerdeliv'     => strtoupper($kdcustomerdeliv),
                'alamatcustomerdeliv' => strtoupper($alamatcustomerdeliv),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                'currcode'       => $currcode,
                'kdsalesman'       => $salesman,
                'kurs'           => $kurs_clean,
                'isinclusive'    => strtoupper($isinclusive),
                'isopenprice'    => strtoupper($isopenprice),
                'idtax'          => strtoupper($idtax),
                // 'pocust'         => strtoupper($pocust),
                'carabayar'         => strtoupper($carabayar),
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.S.B.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                return redirect()->to(base_url('/sales/postsales/penjualan'));
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.S.B.2',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addPenjualan'));
            }



        }

    }


    function show_penjualan(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.penjualan');

       $builder = $builder
            ->where('docno', $docno)
            ->update([
                'status'=> 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s')
            ]);

        
        $enc_docno = $this->fiky_encryption->sealed($docno);
        
        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Report Void Permintaan Pembelian";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_penjualan/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/report_penjualan.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_penjualan(){
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
        $datamst = $this->m_postsales->q_penjualan_master($param);
        $datadtl = $this->m_postsales->q_penjualan_dtl($param);
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







    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL
    // SURAT ORDER INTERNAL

    
    public function soi()
    {
        $data['title']="Surat Order Internal";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.2'; $versirelease='I.S.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.2'";
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
        $dtl = $this->m_postsales->q_soi_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntrySOI');
            $urlnext = base_url('sales/postsales/addSOI');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_list_soi',$data);
    }

    function detailSOI()
    {
        /* Penambahan Squence */
        $data['title']="Detail Surat Order Internal";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/postsales/soi'));
        }
        $kodemenu='I.S.B.2'; $versirelease='I.S.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.2'";
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
        $data['dtldata'] = $this->m_postsales->q_soi_master($param)->getRowArray();
        return $this->template->render('sales/postsales/v_detail_soi',$data);
    }

    function list_soi(){
        $list = $this->m_postsales->get_t_front_soi_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = '<div class="dropdown">
            //                 <button class="btn btn-primary btn-sm dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-bars"></i>
            //                     <span class="caret"></span></button>
            //                     <div class="dropdown-menu" role="menu">
            //                         <a style="background-color: #3badf6;"class="dropdown-item" href=' . "'" . base_url('sales/postsales/soi/updateSOI') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Update This soi : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-bars"></i> Update Surat Order Internal </a>
            //                         <a style="background-color: #00ff8e;" class="dropdown-item" href=' . "'" . base_url('sales/postsales/soi/show_soi') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Print This Data Detail : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-eye"></i> Print Surat Order Internal </a>
            //                         <a style="background-color: red;" class="dropdown-item" href=' . "'" . base_url('sales/postsales/soi/deleteSOI') . '/' . '?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . "'" . ' onclick="return confirm(' . "'" . 'Remove this soi : ' . trim($lm->docno) . "'" . ')"><i class="fa fa-trash"></i> Delete Surat Order Internal </a>                      
            //                     </div>
            //             </div>
            // ';
            $updateBtn = '<a class="dropdown-item bg-warning" 
                href="' . base_url('sales/postsales/updateSOI') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'Update This soi : ' . trim($lm->docno) . '\')">
                <i class="fa fa-edit"></i> Update Surat Order Internal 
            </a>';

            $detailBtn = '<a style="background-color: #3badf6;" class="dropdown-item" 
                href="' . base_url('sales/postsales/detailSOI') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                onclick="return confirm(\'View This Detail soi : ' . trim($lm->docno) . '\')">
                <i class="fa fa-eye"></i> Detail Surat Order Internal 
            </a>';

            $printBtn = '<a style="background-color: #00ff8e;" class="dropdown-item" 
                            href="' . base_url('sales/postsales/show_soi') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Print This Data Detail : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-print"></i> Print Surat Order Internal 
                        </a>';

            $deleteBtn = '<a class="dropdown-item bg-danger" 
                            href="' . base_url('sales/postsales/deleteSOI') . '/?id=' . bin2hex(trim($lm->docno)) . '&docno=' . bin2hex(trim($lm->docno)) . '" 
                            onclick="return confirm(\'Cancel this soi : ' . trim($lm->docno) . '\')">
                            <i class="fa fa-trash"></i> Cancel Surat Order Internal 
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
            $row[] = $lm->po;
            $row[] = $lm->pocust;
            // $row[] = $lm->linksteelgrade;
            $row[] = $lm->revno;
            $row[] = $lm->description;
            // $row[] = $lm->pic;
            
            // $row[] = $lm->status_desc ?? $lm->status;
            $status = $lm->status_desc ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'FINAL':
                    $badgeClass = 'badge-success';
                    break;
                case 'CETAK/PRINT':
                    $badgeClass = 'badge-info';
                    break;
                case 'CANCEL':
                    $badgeClass = 'badge-warning';
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
            "recordsTotal" => $this->m_postsales->t_front_soi_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_soi_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function importSOIDetailFromPO()
    {
        $po = trim($this->request->getPost('po'));
        $cust = trim($this->request->getPost('cust'));
        $nama = trim($this->session->get('nama'));

        // 1. Validasi parameter PO
        if (empty($po)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter PO tidak boleh kosong.'
            ]);
        }

        $db = db_connect();

        // 2. Ambil data Sales Order External Detail berdasarkan PO
        $builderSO = $db->table('sc_trx.salesorderexternaldtl');
        $builderSO->where('docno', $po);
        $soDetail = $builderSO->get()->getResultArray();

        if (empty($soDetail)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data Sales Order External Detail tidak ditemukan untuk PO tersebut.'
            ]);
        }

        // 3. Ambil docno dari sc_tmp.soi berdasarkan user aktif
        $builderSOI = $db->table('sc_tmp.soi');
        $builderSOI->select('docno');
        $builderSOI->where('inputby', $nama);
        $soi = $builderSOI->get()->getRowArray();

        if (empty($soi)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data SOI sementara untuk user aktif tidak ditemukan.'
            ]);
        }

        $docnoSOI = $soi['docno'];

        // 4. UPDATE DATA PO di sc_tmp.soi
        $builderUpdateSOI = $db->table('sc_tmp.soi');
        $builderUpdateSOI->where('inputby', $nama); // WHERE condition sama
        $builderUpdateSOI->where('docno', $docnoSOI); // Tambahan condition untuk lebih aman
        
        $dataUpdateSOI = [
            'po' => $po, // Simpan PO ke kolom yang sesuai
            'cust' => $cust, // Simpan Cust ke kolom yang sesuai
            'updateby' => $nama,
            'updatedate' => date('Y-m-d H:i:s')
        ];

        $builderUpdateSOI->update($dataUpdateSOI);


        // 4. Siapkan data insert ke sc_tmp.soidtl
        $builderInsert = $db->table('sc_tmp.soidtl');

        foreach ($soDetail as $row) {
            $dataInsert = [
                'docno'         => $docnoSOI,
                'idbarang'      => $row['idbarang'] ?? null,
                'cust'          => $row['cust'] ?? null,
                'nmbarang'      => $row['nmbarang'] ?? null,
                'grade'         => $row['grade'] ?? null,
                'size'          => $row['size'] ?? null,
                'cutlength'     => $row['cutlength'] ?? null,
                'qty'           => $row['qty'] ?? 0,
                'unit'          => $row['unit'] ?? null,
                'usdmt'         => $row['usdmt'] ?? 0,
                'price'         => $row['price'] ?? 0,
                'exchange'      => $row['exchange'] ?? 0,
                'amount'        => $row['amount'] ?? 0,
                'etd'           => $row['etd'] ?? null,
                'ordernumbermsr'=> $row['ordernumbermsr'] ?? null,
                'specno'        => $row['specno'] ?? null,
                'totaldelivery' => $row['totaldelivery'] ?? 0,
                'balanceorder'  => $row['balanceorder'] ?? 0,
                'description'   => $row['description'] ?? null,
                'status'        => $row['status'] ?? 'E', // default draft
                'inputby'       => $nama,
                'inputdate'     => date('Y-m-d H:i:s'),
                'updateby'      => $nama,
                'updatedate'    => date('Y-m-d H:i:s'),
                'docnotmp'      => $row['docnotmp'] ?? null
            ];

            $builderInsert->insert($dataInsert);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data Sales Order External berhasil diimpor ke SOI Detail.',
            'inserted' => count($soDetail)
        ]);
    }


    public function clearTmpSOIDetail()
    {
        $db   = db_connect();
        $nama = trim($this->session->get('nama'));

        if (!$nama) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter inputby tidak ditemukan.'
            ]);
        }

        try {
            // ✅ 1. Hapus semua detail temporary SOI berdasarkan user
            $db->table('sc_tmp.soidtl')
                ->where('inputby', $nama)
                ->delete();

            // ✅ 2. Hapus value PO & CUST di header SOI user yang sama
            $db->table('sc_tmp.soi')
                ->where('inputby', $nama)
                ->update([
                    'po'   => null,
                    'cust' => null
                ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data temporary SOI detail, PO, dan CUST berhasil dikosongkan.'
            ]);

        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }



    function clearEntrySOI()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_postsales->q_soi_master_temp($param);
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/postsales/soi'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.soi');
        $builder_dtl = $this->db->table('sc_tmp.soidtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.soi');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/postsales/soi'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/soi'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
                // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                // echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/soi'));
        }

    }

    function addSOI()
    {
        /* Penambahan Squence */
        $data['title']="Input Surat Order Internal";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.2'; $versirelease='I.S.B.2/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.B.2'";
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
        $data['mst'] = $this->m_postsales->q_soi_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_postsales->q_soi_master_temp($param)->getRowArray();

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_add_soi',$data);
    }


    function updateSOI()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_postsales->q_soi_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.soi');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/postsales/addSOI'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/postsales/soi'));
        }
    }

    function saveSOI(){
        $nama = trim($this->session->get('nama'));
        $loccode = trim($this->session->get('loccode'));
        $docref = strtoupper($this->request->getPost('docref'));
        // $penerima = strtoupper($this->request->getPost('penerima'));
        $docno = strtoupper($this->request->getPost('docno'));
        $docdate = strtoupper($this->request->getPost('docdate'));
        // $cust = strtoupper($this->request->getPost('cust'));
        // $phone = strtoupper($this->request->getPost('phone'));
        // $fax = strtoupper($this->request->getPost('fax'));
        // $up = strtoupper($this->request->getPost('up'));
        $rolejob = strtoupper($this->request->getPost('rolejob'));
        // $enduser = strtoupper($this->request->getPost('enduser'));
        // $trader = strtoupper($this->request->getPost('trader'));
        // $pocust = strtoupper($this->request->getPost('pocust'));
        // $exchangerate = strtoupper($this->request->getPost('exchangerate'));
        // $currency = strtoupper($this->request->getPost('currency'));
        // $address = strtoupper($this->request->getPost('address'));
        // $pic = strtoupper($this->request->getPost('pic'));
        // $description = strtoupper($this->request->getPost('desc'));

        // $dateout = strtoupper($this->request->getPost('dateout'));
        // $nopol = strtoupper($this->request->getPost('nopol'));
        // $isreturn = ($this->request->getPost('isreturn'));
        // $datereturn = strtoupper($this->request->getPost('datereturn'));
        // $tujuan = ($this->request->getPost('tujuan'));
        // $jenisbarang = ($this->request->getPost('jenisbarang'));
        // $baranglain = strtoupper($this->request->getPost('baranglain'));
        $countx = $this->m_postsales->q_soi_master_temp(" and trim(inputby)='$nama'")->getNumRows();

        // if ($isreturn === 'kembali' && empty($datereturn)) {
        //     return redirect()->to(base_url('sales/postsales/addSOI'))
        //         ->with('error', 'Return date is required when selecting "Kembali".');
        // }
    
        // if ($jenisbarang === 'lainlain' && empty($baranglain)) {
        //     return redirect()->to(base_url('sales/postsales/addSOI'))
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
                // 'cust' => $cust,
                // 'currency' => $currency,
                // 'exchangerate' => $exchangerate,
                // 'enduser' => $enduser,
                // 'trader' => $trader,
                // 'pocust' => $pocust,
                // 'phone' => $phone,
                // 'fax'=>$fax,
                // 'address'=>$address,
                'rolejob' => $rolejob,
                // 'pic' => $pic,
                // 'datereturn' => (!empty($datereturn) ? date('Y-m-d', strtotime($datereturn)) : null),
                // 'baranglain'=>$baranglain,
                // 'docdate' => date('Y-m-d'),
                'status' => 'E',
                // 'description' => $description,
                'inputby' => $nama,
                'inputdate' => date('Y-m-d H:i:s'),
            );
            $builder= $this->db->table('sc_tmp.soi');
            $builder->where('docno',$docno);
            $builder->insert($info);
            return redirect()->to(base_url('sales/postsales/addSOI'));
        } else {
            /*RETURN FAILED*/
            return redirect()->to(base_url('sales/postsales/addSOI'));
        }

    }

    function showing_soitrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_soi_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_soitemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_soi_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_soi_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_postsales->q_soi_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }


    public function insert_detail_soi()
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
        $builder = $this->db->table('sc_tmp.soidtl'); // Sesuaikan dengan tabel Anda
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

    public function insertNewSOI()
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
        $builder = $this->db->table('sc_tmp.soidtl');
        $insert = $builder->insert($data_insert);
    
        // Cek apakah berhasil insert
        if ($insert) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Surat Order Internal successfully created!',
                'docno' => $docno
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to create Surat Order Internal. Please try again.'
            ]);
        }
    }
    
    


    public function update_detail_soi()
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
                $grade = $update['grade'] ?? '';
                $size = $update['size'] ?? '';
                $cutlength = $update['cutlength'] ?? '';
                $specno = $update['specno'] ?? '';
                $ordernumbermsr = $update['ordernumbermsr'] ?? '';
                $etd = $update['etd'] ? date('Y-m-d',strtotime($update['etd'])) : '';
                
                $qty = $update['qty'] ?? 0;
                $price = $update['price'] ?? 0;
                $amount = $update['amount'] ?? 0;
                $totaldelivery = $update['totaldelivery'] ?? 0;
                $balanceorder = $update['balanceorder'] ?? 0;
                
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

                    'grade' => $grade,
                    'size' => $size,
                    'cutlength' => $cutlength,
                    'specno' => $specno,
                    'ordernumbermsr' => $ordernumbermsr,
                    'etd' => $etd,
                    'amount' => $amount,
                    'totaldelivery' => $totaldelivery,
                    'balanceorder' => $balanceorder,


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
                $builder = $this->db->table('sc_tmp.soidtl');
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

    function list_t_soi_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_soi_dtl_temp_view($docno);
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
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '" selected>'
                    . htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') . 
                '</option>
            </select>';


             //grade
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="grade_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="grade_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->grade, ENT_QUOTES, 'UTF-8') . '" disabled >';
            
            //size
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="size_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="size_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->size, ENT_QUOTES, 'UTF-8') . '" disabled >';

            //cutlength
            $row[] = '<input class="form-control " maxlength="100"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="cutlength_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="cutlength_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->cutlength, ENT_QUOTES, 'UTF-8') . '" disabled >';

             //qty
            $row[] = '<input class="form-control ratakanan jtsseparator" style="margin:0px; background-color:#d6d5d5;width: 100%;" type="text" id="qty_'.$lm->idurut.'" name="qty_'.$lm->idurut.'" value="'.number_format($lm->qty, 2, ',', '.').'" disabled  min="0">';
            
            //unit
            $row[] = '<select disabled class="unit-dropdown" style="width: 100%; height: 20px!important; font-size: 12px; " data-id="' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '">
                <option value="" disabled>-- Choose --</option>
                <option value="' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($lm->unit, ENT_QUOTES, 'UTF-8') . '</option>
                </select>';

            //ordernumbermsr
            $row[] = '<input class="form-control " maxlength="50"  style="text-transform: uppercase;margin:0px; background-color:#d6d5d5;width: 100%" type="text" id="ordernumbermsr_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" name="ordernumbermsr_' . htmlspecialchars($lm->idurut, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($lm->ordernumbermsr, ENT_QUOTES, 'UTF-8') . '" disabled >';
            $row[] = '';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_postsales->t_soi_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_soi_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

        function list_t_soi_dtltrx(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_soi_dtl_view($docno);
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
                <option value="' . htmlspecialchars($lm->idbarang, ENT_QUOTES, 'UTF-8') . '" selected>'
                    . htmlspecialchars($lm->nmbarang, ENT_QUOTES, 'UTF-8') . 
                '</option>
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
            "recordsTotal" => $this->m_postsales->t_soi_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_soi_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntrySOI(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $param = " and coalesce(inputby,'')='$nama'";
        $paramdtl = " AND COALESCE(inputby, '') = '$nama' AND (COALESCE(unit, '') = ''  OR qty = '0.00' OR qty = '0' OR COALESCE(nmbarang, '') = '')";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_postsales->q_soi_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek = $this->m_postsales->q_soi_dtl_temp($paramdtl);
        $cek2 = $this->m_postsales->q_soi_dtl_temp($paramdtl2);


        $builder = $this->db->table('sc_tmp.soi');

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.2');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.B.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addSOI'));
        } else {
            // Ambil dari request POST
            $cust         = strtoupper(trim($this->request->getPost('cust')));
            $revno         = strtoupper(trim($this->request->getPost('revno')));
            $pocust         = strtoupper(trim($this->request->getPost('pocust')));
            $po       = strtoupper(trim($this->request->getPost('po')));
            $desc         = strtoupper(trim($this->request->getPost('desc')));


            // Update data header dulu sebelum set status F
            $updateHeader = [
                'cust'      => $cust,
                'pocust'      => $pocust,
                'po'      => $po,
                'revno'      => $revno,
                'description'       => $desc,
            ];

            $builder->where('inputby', $nama);
            $builder->update($updateHeader);

            $info = array(
                'status' => 'F'
            );
            $builder->where('inputby',$nama);
            if ($builder->update($info)) {
                $paramerror=" and userid='$nama' and modul='I.S.B.2'";
                $dtlerror=$this->m_trxerror->q_trxerror($paramerror)->getRowArray();
                $count_err=$this->m_trxerror->q_trxerror($paramerror)->getNumRows();

                // $docno = trim(bin2hex(trim($dtlerror['nomorakhir1'])));

                // return redirect()->to(base_url('/sales/postsales/soi'));
                return $this->response->setJSON([
                    'status' => 'success',
                    'redirect' => base_url('/sales/postsales/soi')
                ]);
            } else {
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek->getNumRows(),
                    'nomorakhir2' => $cek2->getNumRows(),
                    'modul' => 'I.S.B.2',
                );
                $builder_trxerror->insert($infotrxerror);
                // return redirect()->to(base_url('/sales/postsales/addSOI'));
                return $this->response->setJSON([
                    'status' => 'error',
                    'redirect' => base_url('/sales/postsales/addSOI')
                ]);
            }



        }

    }

    function deleteSOI(){
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $builder = $this->db->table('sc_trx.soi');
                
        // Update status menjadi 'C'
        $iupdate = array('status' => 'C');
        $builder->where('docno', $docno);
        if ($builder->update($iupdate)) {
            // // Hapus data dari tabel soi dan soidtl sesuai docno dan status 'C'
            // $builder->where('docno', $docno); 
            // $builder->where('status', 'C');
            // $builder->delete();
            
            // // Menghapus data dari soidtl
            // $this->db->table('sc_trx.soidtl')
            //         ->where('docno', $docno)
            //         ->where('inputby', $nama)
            //         ->delete();

            return redirect()->to(base_url('sales/postsales/soi') . '?status=success&message=Successfully data Canceled');
        } else {
            return redirect()->to(base_url('sales/postsales/soi') . '?status=error&message=No updates provided or operation failed');
        }

    }


    function deleteSOIDtl(){
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
        $builder = $this->db->table('sc_tmp.soidtl');
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

    function show_soi(){
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.soi');

       $builder = $builder
            ->where('docno', $docno)
            ->update([
                'status'=> 'P',
                'printby' => $nama,
                'printdate' => date('Y-m-d H:i:s')
            ]);

        
        $enc_docno = $this->fiky_encryption->sealed($docno);
        
        //$enc_docdate= $this->fiky_encryption->sealed($docdate);
        // $enc_idlocation = $this->fiky_encryption->sealed($idlocation);
        // $enc_idgroup = $this->fiky_encryption->sealed($idgroup);
        // $enc_formheader = $this->fiky_encryption->sealed($formheader);

        $title = " Report Surat Order Internal";

        //$datajson =  base_url("manufactur/production/api_soi/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_soi/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
            $datamrt =  base_url("assets/mrt/soi.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/soi_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama);
    }

    function api_soi(){
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

        $this->db->query("select sc_trx.pr_generate_pov_soidtl('$docno','$nama');");
        $databranch = $this->m_global->q_master_branch();
        $param=" and docno='$docno'";
        $datamst = $this->m_postsales->q_soi_master($param);
        $datadtl = $this->m_postsales->q_view_print_soi_dtl($param,$nama);
        // $datadtl = $this->m_postsales->q_soi_dtl($param);
        $tampungdtl = $datamst->getResult();
        $detail = $tampungdtl[0] ?? null;        
        if ($detail) {
            // $tujuan = isset($detail->tujuan) ? trim($detail->tujuan) : '';
            $detail->docno = trim($detail->docno);            
             // === Hitung TOTAL QTY dari datadtl ===
            $sumQty = 0;

            foreach ($datadtl->getResult() as $row) {
                // pastikan null jadi 0
                $sumQty += floatval($row->qty ?? 0);
            }

            // Tambahkan property baru
            $detail->ttlqty = $sumQty;   // atau $detail->sumqty
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


    public function getRolePOSOI()
    {
        $jobcode = trim($this->request->getGet('rolejob'));
        $codemenu = 'I.S.B.2';
        $logindate = trim($this->session->get('logindate')); // format: dd-mm-yyyy

        // Buat infix dari logindate
        $infix = '';
        if (!empty($logindate)) {
            $ts = strtotime($logindate);
            $infix = date('ym', $ts); // contoh: 2508
        }

        // Mapping prefix default
        $prefixMap = [  
            'JTS'  => 'JTS-SOI',
            'MSMI' => 'MSMI-SOI',
            'MSMJ' => 'MSM-SOI'
        ];
        $prefix = isset($prefixMap[$jobcode]) ? $prefixMap[$jobcode] : '';

        if (empty($prefix) || empty($infix)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role/Job atau logindate tidak valid'
            ]);
        }

        // Ambil suffix terakhir dari docno di sc_trx.soi
        $builder = $this->db->table('sc_trx.soi');
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

    public function getRateSOI()
    {
        $curr = $this->request->getGet('currcode'); // mis. "USD"
        $date = $this->request->getGet('docdate'); // "2025-08-06"

        $builder = $this->db->table('sc_mst.currency');
        $builder->select('id');
        $builder->where('currcode', $curr);
        $row = $builder->get()->getRow();

        if (!$row) {
            return null; // currency tidak ditemukan
        }

        $idcurr = $row->id;

        // $model = new ExchangeRateModel();
        $nilai = $this->m_postsales->getRate($idcurr, $date);

        return $this->response->setJSON([
            'currency' => $curr,
            'date' => $date,
            'nilai' => $nilai ?? 1, // fallback
        ]);
    }



}

