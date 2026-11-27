<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Sales\DeliveryOrderModel;

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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */

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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


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

            if ($canUpdate && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateSalesOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Sales Order : ' . $docno . '\')">
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

            if ($canPrint && (trim($status) == 'APPROVED' || trim($status) == 'CETAK/PRINT')) {
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_salesorder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Preview Sales Order : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Preview Sales Order 
                </a>';
            }

            if ($canDelete && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
                $cancelBtn =  '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                        <i class="fa fa-undo"></i> Batalkan Sales Order</a>';
            }


            // if (trim($status) !== 'APPROVED' && trim($status) !== 'REVISION/EDITING') {
            //         $approveBtn = '<a class="dropdown-item bg-success" href="#" onclick="setToApproved(\'' . trim($lm->docno) . '\');">
            //             <i class="fa fa-check-circle"></i> Approve</a>';
            // }

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
                case 'DIPROSES DO':
                    $badgeClass = 'badge-cetak ';
                    break;
                case 'DIPROSES PENJUALAN':
                    $badgeClass = 'badge-cetak ';
                    break;
                case 'CANCEL':
                    $badgeClass = 'badge-danger ';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }


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
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_front_salesorder_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_salesorder_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_salesorder_apprv(){
        $list = $this->m_postsales->get_t_front_salesorder_apprv_view();
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


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

            if ($canUpdate && $status = "FINAL USER") {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateSalesOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Sales Order : ' . $docno . '\')">
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

            if ($canPrint && (trim($status) == 'APPROVED' || trim($status) == 'CETAK/PRINT')) {
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Sales Order : ' . $docno . '\')">
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
                // if ($canUpdate) $menuContent .= $updateBtn;
                // if ($canPrint)  $menuContent .= $printBtn;
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
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('sales/postsales/salesorder'));
        }
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

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('sales/postsales/salesorder'));
        }
        // =================================

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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays

        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['salesorder']) ?? '';
        $currcode = trim($konfigurasiUmum[0]['currcode']) ?? '';
        $idtax = trim($konfigurasiUmum[0]['idtax']) ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'        => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode' => $currcode,
            'idtax' => $idtax,

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

    /* =========================================================
     * CABANG / JOB mengikuti session user login.
     * - User "01 JATIM TAMAN STEEL" boleh memakai cabang pilihan.
     * - User PLAN 1 / PLAN 2 (PLANT I / PLANT II) dipaksa memakai
     *   cabang session, walaupun request dimanipulasi.
     * ========================================================= */
    private function salesOrderResolveBranch($cabang)
    {
        $posted     = trim((string) $cabang);
        $sessionId  = trim((string) $this->session->get('idbranchjob'));
        $sessionKey = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim((string) $this->session->get('nmbranchjob'))));

        if ($sessionId === '' || $sessionKey === '') {
            return $posted;
        }

        $isPlantBranch      = strpos($sessionKey, 'PLANT') !== false
            || strpos($sessionKey, 'PLAN1') !== false
            || strpos($sessionKey, 'PLAN2') !== false;

        $isHeadOfficeBranch = ($sessionId === '01')
            || strpos($sessionKey, 'JATIMTAMANSTEEL') !== false;

        if (!$isHeadOfficeBranch || $isPlantBranch) {
            return $sessionId;
        }

        return ($posted !== '') ? $posted : $sessionId;
    }

    public function initSalesOrderHeader()
    {
        $nama = trim($this->session->get('nama'));

        $docno      = strtoupper($this->request->getPost('docno'));
        $docdate    = $this->request->getPost('docdate');
        $cabang     = $this->salesOrderResolveBranch($this->request->getPost('cabang'));
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

            /* Open price mengikuti grade customer (sc_mst.gradecust.isopenprice) */
            $gradecustomer = $this->soClip(strtoupper(trim((string) $this->request->getPost('gradecustomer'))), 20);
            $isopenprice   = $this->m_postsales->getGradeOpenPrice($gradecustomer) ? 'YES' : 'NO';

            try {
                $builderHeader->insert([
                    'docno'     => $this->soClip($docno, 30),
                    'cabang'     => $this->salesOrderResolveBranch($this->request->getPost('cabang')),
                    'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                    'delivdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('delivdate')))),
                    'jthtempo'     => $this->soToNumber($this->request->getPost('jthtempo')),
                    'isinclusive'     => $isinclusive,
                    'isopenprice'     => $isopenprice,

                    'kdcustomer'    => $this->soClip(strtoupper((string) $this->request->getPost('kdcustomer')), 20),
                    'nmcustomer'    => $this->soClip(strtoupper((string) $this->request->getPost('nmcustomer')), 100),
                    'alamatcustomer'    => strtoupper((string) $this->request->getPost('alamatcustomer')),
                    // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                    'idtax'    => $this->soClip(strtoupper((string) $this->request->getPost('idtax')), 20),
                    'kdsalesman'    => $this->soClip($this->request->getPost('kdsalesman'), 20),
                    'gradecustomer'    => $gradecustomer,
                    'currcode'    => $this->soClip(strtoupper((string) $this->request->getPost('currcode')), 10),
                    'kurs'    => $this->soToNumber($this->request->getPost('kurs')),
                    'keterangan'    => strtoupper((string) $this->request->getPost('keterangan')),
                    'pocust'    => $this->soClip(strtoupper((string) $this->request->getPost('pocust')), 50),
                    'nodp'    => $this->soClip(strtoupper((string) $this->request->getPost('nodp')), 50),
                    'status'    => 'E',
                    'inputby'   => $this->soClip($nama, 50),
                    'inputdate' => date('Y-m-d H:i:s')
                ]);
            } catch (\Throwable $e) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan header: ' . $e->getMessage()
                ]);
            }

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
            $soHeader = $builderHeader->select('kurs, idtax')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()->getRowArray();
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

            $updateDetail = [
                'qty'          => $qty,
                // 'qtybonus'     => $qtybonus,
                'harga'        => $harga,
                'multidisc'    => $multidisc,
                'nilai'        => $nilai,
                'nilaikonversi' => $nilaikonversi,  // Tambahkan ini
                'nilaipajak'   => $nilaipajak,      // Tambahkan ini
                'kurs'          => strtoupper((string) $this->request->getPost('kurs')),
                'idtax'         => strtoupper((string) $this->request->getPost('idtax')),
                'currcode'      => strtoupper((string) $this->request->getPost('currcode')),
                // 'volitem'      => $volitem,

                // 'biaya'      => $biaya,
                // 'biaya2'      => $biaya2,
                'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                'idspec'      => $idspec,

                'bomdesc' => $bomdesc,
                'updateby'     => $nama,
                'updatedate'   => date('Y-m-d H:i:s')
            ];

            // ETD / ETA hanya diperbarui bila dikirim form (modal update tidak memilikinya)
            if ($this->request->getPost('etd') !== null) {
                $updateDetail['etd'] = $this->soNormalizeDate($this->request->getPost('etd'));
            }

            if ($this->request->getPost('eta') !== null) {
                $updateDetail['eta'] = $this->soNormalizeDate($this->request->getPost('eta'));
            }

            try {
                $builderDetail
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('uniqueid', $uniqueid)
                    ->update($updateDetail);
            } catch (\Throwable $e) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah detail: ' . $e->getMessage()
                ]);
            }





            $message = 'Data berhasil diupdate';

        } else {

            // =====================================================
            // MODE ADD - INSERT DATA DARI PP
            // =====================================================
            $idbarang    = $this->soClip($this->request->getPost('idbarang'), 30);
            $nmbarang    = $this->soClip(strtoupper((string) $this->request->getPost('nmbarang')), 150);
            $unit        = $this->soClip(strtoupper((string) $this->request->getPost('unit')), 20);
            $qty         = $this->request->getPost('qty');
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $bomdesc = strtoupper($this->request->getPost('bomdesc'));
            $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang = strtoupper($this->request->getPost('idgudang'));
            $idspec = strtoupper($this->request->getPost('idspec'));


            // Ambil kurs dari header SO
            $soHeader = $builderHeader->select('kurs, idtax')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()->getRowArray();
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



            /* Urutan tampilan detail (idurut_view) = MAX + 1 per dokumen */
            $maxViewRow = $db->table('sc_tmp.salesorder_dtl')
                ->select('COALESCE(MAX(idurut_view), 0) AS max_view')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            $idurutView = (int) ($maxViewRow['max_view'] ?? 0) + 1;

            $inputdate = date('Y-m-d H:i:s');
            $rawUnique = $nmbarang
                . '|' . $docno
                . '|' . $nama
                . '|' . $inputdate;

            /* uniqueid bertipe varchar(50), sedangkan hash sha256 = 64 karakter.
               Wajib dipotong, kalau tidak INSERT gagal ("value too long for
               character varying(50)") dan seluruh transaksi ikut batal. */
            $uniqueid  = substr(hash('sha256', $rawUnique), 0, 50);


            // 🔹 INSERT (nilai string dibatasi panjang kolom agar aman di PostgreSQL)
            try {
                $saved = $builderDetail->insert([
                    'docno'       => $this->soClip($docno, 30),
                    'idbarang'    => $this->soClip($idbarang, 30),
                    'nmbarang'    => $this->soClip($nmbarang, 150),
                    'unit'        => $this->soClip($unit, 20),
                    'qty'         => $qty,
                    'multidisc'         => $multidisc,
                    'harga'         => $harga,
                    'idprincipal'      => $this->soClip($idprincipal, 20),
                    'idgudang'      => $this->soClip($idgudang, 20),
                    'idspec'      => $this->soClip($idspec, 50),
                    'etd'         => $this->soNormalizeDate($this->request->getPost('etd')),
                    'eta'         => $this->soNormalizeDate($this->request->getPost('eta')),
                    'nilai'         => $nilai,
                    'nilaipajak'         => $nilaipajak,
                    'nilaikonversi'         => $nilaikonversi,
                    'kurs'          => $this->soToNumber($this->request->getPost('kurs')),
                    'idtax'         => $this->soClip(strtoupper((string) $this->request->getPost('idtax')), 20),
                    'currcode'      => $this->soClip(strtoupper((string) $this->request->getPost('currcode')), 10),
                    'bomdesc' => $bomdesc,
                    'idurut_view' => $idurutView,
                    'status'      => 'F',
                    'inputby'     => $this->soClip($nama, 50),
                    'inputdate'   => date('Y-m-d H:i:s'),
                    'uniqueid'    => $uniqueid
                ]);
            } catch (\Throwable $e) {
                /* Rollback wajib: kalau transaksi PostgreSQL dibiarkan gagal,
                   query berikutnya (termasuk write session) ikut error dan
                   halaman membalas 500. */
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan detail: ' . $e->getMessage()
                ]);
            }

            if ($saved === false) {
                $db->transRollback();

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan detail, periksa kembali data yang diinput.'
                ]);
            }

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
            ->where('inputby', $nama)
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
            'dpp' => number_format($dpp, 4, '.', ''),
            'jumlahpajak' => number_format($jumlahPajak, 4, '.', ''),
            'total' => number_format($total, 4, '.', ''),
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


    public function recalculate_tax_temp()
    {
        $db   = db_connect();
        $nama = trim($this->session->get('nama'));
        $idtax = strtoupper(trim($this->request->getPost('idtax')));

        // Cek apakah sc_tmp.salesorder (header temp) ada
        $builderHeader = $db->table('sc_tmp.salesorder');
        $header = $builderHeader->where('inputby', $nama)->get()->getRowArray();

        if (empty($header)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Header temp belum ada, skip update detail.'
            ]);
        }

        // Cek apakah sc_tmp.salesorder_dtl ada data
        $builderDetail = $db->table('sc_tmp.salesorder_dtl');
        $countDetail = $builderDetail->where('inputby', $nama)->countAllResults(false);

        if ($countDetail <= 0) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Detail temp belum ada, skip update.'
            ]);
        }

        // Ambil total persentase pajak dari sc_mst.tax_dtl
        $totalPersentase = 0;
        if (!empty($idtax) && $idtax !== 'NON') {
            $taxDetails = $db->table('sc_mst.tax_dtl')
                ->select('percentation')
                ->where('idtax', $idtax)
                ->get()
                ->getResultArray();

            foreach ($taxDetails as $tax) {
                $totalPersentase += (float) ($tax['percentation'] ?? 0);
            }
        }

        // Ambil semua detail milik user
        $details = $builderDetail
            ->select('uniqueid, nilai')
            ->where('inputby', $nama)
            ->get()
            ->getResultArray();

        // Update per baris
        foreach ($details as $row) {
            $nilai = (float) $row['nilai'];

            if ($totalPersentase > 0 && $nilai > 0) {
                $nilaipajak = $nilai * $totalPersentase / 100;
            } else {
                // NON pajak → nilaipajak = 0 (atau = nilai, tergantung bisnis)
                $nilaipajak = 0;
            }

            $builderDetail
                ->where('uniqueid', $row['uniqueid'])
                ->where('inputby', $nama)
                ->update([
                    'idtax'      => $idtax,
                    'nilaipajak' => $nilaipajak,
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s'),
                ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => "Update $countDetail baris berhasil.",
            'count'   => $countDetail,
            'percent' => $totalPersentase,
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

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // A / R / C (1 huruf)
                'I.S',             // kode module dari menuprg
                'I.S.B.1'          // kode menu untuk SO
            );

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

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('sales/postsales/salesorder'));
        }

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
        $param = " and docno='$docno' and inputby='$nama'";
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
            ->where('uniqueid', $id)
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
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
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
            // CEK HEADER MASIH DRAFT (belum final)
            // ======================================
            $headerRow = $db->table('sc_tmp.salesorder')
                ->select('status')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if (!$headerRow || strtoupper(trim($headerRow['status'])) === 'F') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah final, tidak dapat diubah'
                ]);
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
            $builder
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->delete();

            $sisa = $db->table('sc_tmp.salesorder_dtl')
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->countAllResults();

            if ($sisa > 0) {
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
                    'dpp'         => number_format($dpp, 4, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 4, '.', ''),
                    'total'       => number_format($total, 4, '.', ''),
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

    /* =====================================================
     * OPEN PRICE MENGIKUTI GRADE CUSTOMER
     * ===================================================== */

    /* Info grade customer + flag open price (dipakai saat pilih customer) */
    public function getCustomerGradeInfo()
    {
        $nama = trim((string) $this->session->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session login tidak ditemukan'
            ]);
        }

        $kdcustomer = trim((string) $this->request->getGet('kdcustomer'));
        $grade      = strtoupper(trim((string) $this->request->getGet('grade')));

        if ($grade === '' && $kdcustomer !== '') {
            $grade = strtoupper($this->m_postsales->getCustomerGrade($kdcustomer));
        }

        return $this->response->setJSON([
            'success'     => true,
            'grade'       => $grade,
            'isopenprice' => $this->m_postsales->getGradeOpenPrice($grade) ? 'YES' : 'NO'
        ]);
    }

    /* Ubah grade pada draft (temp) + simpan flag open price hasil derivasi grade */
    public function changeGradeTemp()
    {
        $nama = trim((string) $this->session->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session login tidak ditemukan'
            ]);
        }

        $docno = strtoupper(trim((string) $this->request->getPost('docno')));
        $grade = strtoupper(trim((string) $this->request->getPost('grade')));

        if ($grade === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction Grade wajib diisi'
            ]);
        }

        $isopenprice = $this->m_postsales->getGradeOpenPrice($grade) ? 'YES' : 'NO';
        $saved       = false;

        if ($docno !== '') {
            $exists = $this->db->table('sc_tmp.salesorder')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->countAllResults();

            if ($exists > 0) {
                $this->db->transStart();

                $this->db->table('sc_tmp.salesorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update([
                        'gradecustomer' => $grade,
                        'isopenprice'   => $isopenprice,
                        'updateby'      => $nama,
                        'updatedate'    => date('Y-m-d H:i:s')
                    ]);

                $this->db->transComplete();

                $saved = true;
            }
        }

        return $this->response->setJSON([
            'success'     => true,
            'saved'       => $saved,
            'grade'       => $grade,
            'isopenprice' => $isopenprice,
            'message'     => 'Transaction Grade berhasil diubah'
        ]);
    }

    /* Pindahkan urutan detail draft memakai idurut_view (tombol ▲ / ▼) */
    public function moveDetailOrder()
    {
        $nama = trim((string) $this->session->get('nama'));

        if ($nama === '') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Session login tidak ditemukan'
            ]);
        }

        $uniqueid  = trim((string) $this->request->getPost('uniqueid'));
        $direction = strtolower(trim((string) $this->request->getPost('direction')));

        if ($uniqueid === '' || ! in_array($direction, ['up', 'down'], true)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Parameter urutan tidak valid'
            ]);
        }

        $rowDetail = $this->db->table('sc_tmp.salesorder_dtl')
            ->select('docno')
            ->where('uniqueid', $uniqueid)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if (! $rowDetail) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data detail tidak ditemukan'
            ]);
        }

        $docno = trim((string) $rowDetail['docno']);

        $rowHeader = $this->db->table('sc_tmp.salesorder')
            ->select('status')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        if ($rowHeader && strtoupper(trim((string) $rowHeader['status'])) === 'F') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Dokumen sudah final, urutan tidak dapat diubah'
            ]);
        }

        $this->db->transBegin();

        try {
            $orderRow = $this->m_postsales->getSalesOrderDetailOrder($docno, $nama);

            $ids = [];
            foreach ($orderRow as $item) {
                $ids[] = (string) $item['uniqueid'];
            }

            $index = array_search($uniqueid, $ids, true);

            if ($index === false) {
                $this->db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data detail tidak ditemukan'
                ]);
            }

            $target = ($direction === 'up') ? $index - 1 : $index + 1;

            if ($target < 0 || $target >= count($ids)) {
                $this->db->transRollback();

                return $this->response->setJSON([
                    'status'  => true,
                    'message' => 'Urutan sudah berada di batas'
                ]);
            }

            $swap         = $ids[$index];
            $ids[$index]  = $ids[$target];
            $ids[$target] = $swap;

            /* Tulis ulang idurut_view 1..N agar urutan konsisten dan tanpa nilai kosong */
            $seq = 1;
            foreach ($ids as $id) {
                $this->db->table('sc_tmp.salesorder_dtl')
                    ->where('uniqueid', $id)
                    ->where('inputby', $nama)
                    ->update(['idurut_view' => $seq]);

                $seq++;
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Urutan detail berhasil diubah'
            ]);

        } catch (\Throwable $e) {

            $this->db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /* Format angka grid detail: maksimal 4 desimal, tanpa nol di belakang koma
       (5.0000 -> 5, 5.5000 -> 5.5). Desimal memakai titik, ribuan memakai koma. */
    private function soFormatGridNumber($value, $decimals = 4)
    {
        $text = number_format((float) $value, $decimals, '.', ',');

        if (strpos($text, '.') !== false) {
            $text = rtrim(rtrim($text, '0'), '.');
        }

        return ($text === '' || $text === '-0') ? '0' : $text;
    }

    /* Normalisasi tanggal dari form: input date (Y-m-d) atau teks (d-m-Y / d/m/Y) */
    private function soNormalizeDate($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (['Y-m-d', 'd-m-Y', 'd/m/Y'] as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date instanceof \DateTime && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($value);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    /* Potong nilai string sesuai panjang kolom PostgreSQL (varchar).
       Mencegah error "value too long for character varying(n)". */
    private function soClip($value, int $length)
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;

        return (mb_strlen($value) > $length) ? mb_substr($value, 0, $length) : $value;
    }

    /* Konversi input POST ke angka; string kosong / bukan angka menjadi default
       (mencegah error "invalid input syntax for type numeric" di PostgreSQL). */
    private function soToNumber($value, $default = 0)
    {
        $value = trim((string) $value);

        if ($value === '' || !is_numeric($value)) {
            return $default;
        }

        return $value + 0;
    }

    function list_tmp_salesorder_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_salesorder_dtl_temp_view($docno);
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->uniqueid;
            //item
            // $row[] = $lm->docnopo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->qty) . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->harga) . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. $this->soFormatGridNumber($lm->nilai) . '</div>';
            $row[] = '<div class="text-center">'. ($lm->etd ? date('d-m-Y', strtotime($lm->etd)) : '') . '</div>';
            $row[] = '<div class="text-center">'. ($lm->eta ? date('d-m-Y', strtotime($lm->eta)) : '') . '</div>';
            $row[] = '<div class="text-center">'. trim((string) $lm->idgudang) . '</div>';
            $row[] = $lm->bomdesc;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->qty) . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->harga) . '</div>';
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->multidisc) . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. $this->soFormatGridNumber($lm->nilai) . '</div>';
            $row[] = $lm->bomdesc;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;

        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_salesorder_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_salesorder_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntrySalesOrder(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $builder = $this->db->table('sc_tmp.salesorder');

        // Ambil header draft milik user (bind via Query Builder, hindari interpolasi SQL)
        $headerRow = $builder
            ->where('inputby', $nama)
            ->orderBy('inputdate', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $status = $headerRow ? trim($headerRow['status']) : '';
        $docno  = $headerRow ? trim($headerRow['docno']) : '';

        // Detail tidak valid (unit kosong / qty 0 / nmbarang kosong / harga 0 / nilai 0)
        $cek = $this->db->table('sc_tmp.salesorder_dtl')
            ->where('inputby', $nama)
            ->groupStart()
            ->where('unit', '')
            ->orWhere('qty', '0.00')
            ->orWhere('qty', '0')
            ->orWhere('nmbarang', '')
            ->orWhere('harga', '0.00')
            ->orWhere('harga', '0')
            ->orWhere('nilai', '0.00')
            ->orWhere('nilai', '0')
            ->groupEnd()
            ->countAllResults();

        // Total detail
        $cek2 = $this->db->table('sc_tmp.salesorder_dtl')
            ->where('inputby', $nama)
            ->countAllResults();

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.1');
        $builder_trxerror->delete();


        if (($status==='E' and $cek > 0) or ($cek2 <= 0))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek,
                'nomorakhir2' => $cek2,
                'modul' => 'I.S.B.1',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addSalesOrder'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            // $docdate   = trim($this->request->getPost('docdate'));
            $delivdate   = trim($this->request->getPost('delivdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            // $kdcustomer   = trim($this->request->getPost('kdcustomer'));
            // $nmcustomer   = trim($this->request->getPost('nmcustomer'));
            $alamatcustomer   = trim($this->request->getPost('alamatcustomer'));
            $gradecustomer   = trim($this->request->getPost('gradecustomer'));
            if ($gradecustomer === '' && $headerRow) {
                $gradecustomer = trim((string) ($headerRow['gradecustomer'] ?? ''));
            }
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            // $keterangan   = trim($this->request->getPost('keterangan'));
            $currcode   = trim($this->request->getPost('currcode'));
            $salesman   = trim($this->request->getPost('kdsalesman'));

            $dpp         = $this->request->getPost('dpp');
            $jumlahpajak = $this->request->getPost('jumlahpajak');
            $total       = $this->request->getPost('total');

            // Konversi: hapus koma ribuan, pertahankan titik desimal
            $dpp_clean         = str_replace(',', '', $dpp);
            $jumlahpajak_clean = str_replace(',', '', $jumlahpajak);
            $total_clean       = str_replace(',', '', $total);

            // Pastikan numeric (opsional tapi disarankan)
            $dpp_clean         = floatval($dpp_clean);
            $jumlahpajak_clean = floatval($jumlahpajak_clean);
            $total_clean       = floatval($total_clean);
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = strtoupper(trim($this->request->getPost('keterangan')));
            $nodp   = trim($this->request->getPost('nodp'));
            $pocust   = trim($this->request->getPost('pocust'));
            $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';
            /* Open price mengikuti grade customer (sc_mst.gradecust.isopenprice) */
            $isopenprice = $this->m_postsales->getGradeOpenPrice($gradecustomer) ? 'YES' : 'NO';



            // **BERSIHKAN FORMAT KURS**
            $kurs = trim($this->request->getPost('kurs'));
            $kurs_clean = 0;
            if (!empty($kurs)) {
                $kurs_clean = str_replace(',', '', $kurs);
                // $kurs_clean = str_replace('.', '.', $kurs_clean);
                // $kurs_clean = floatval($kurs_clean);
            }

            // Convert expdate ke format YYYY-MM-DD
            // $docdateph = null;
            // if (!empty($docdate)) {
            //     $docdateph = date('Y-m-d', strtotime($docdate));
            // }

            $delivdateph = null;
            if (!empty($delivdate)) {
                $delivdateph = date('Y-m-d', strtotime($delivdate));
            }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'docdate'        => $docdateph,
                'delivdate'       => $delivdateph,
                'jthtempo'       => $jthtempo,
                // 'kdcustomer'     => strtoupper($kdcustomer),
                // 'nmcustomer'     => strtoupper($nmcustomer),
                'alamatcustomer' => strtoupper($alamatcustomer),
                'gradecustomer' => strtoupper($gradecustomer),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'dpp'     => $dpp_clean,
                'jumlahpajak'     => $jumlahpajak_clean,
                'total'     => $total_clean,

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

            try {
                $this->db->transStart();

                // Update header (scoped ke pemilik + docno)
                $this->db->table('sc_tmp.salesorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update($updateHeader);

                // FINALIZE: CAS status E -> F (trigger tmp->trx berjalan di sini)
                $this->db->table('sc_tmp.salesorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update(['status' => 'F']);

                $errDb = $this->sales_errorDb();

                // read-back: trigger sukses akan menghapus baris tmp
                $sisa = $this->db->table('sc_tmp.salesorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->countAllResults();

                if ($errDb !== '' || $sisa > 0) {
                    $this->db->transRollback();

                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 3,
                        'nomorakhir1' => $cek,
                        'nomorakhir2' => $cek2,
                        'modul' => 'I.S.B.1',
                    );
                    $builder_trxerror->insert($infotrxerror);
                    return redirect()->to(base_url('/sales/postsales/addSalesOrder'));
                }

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek,
                    'nomorakhir2' => $cek2,
                    'modul' => 'I.S.B.1',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addSalesOrder'));
            }

            return redirect()->to(base_url('/sales/postsales/salesorder'));



        }

    }


    function show_salesorder(){
        $module = 'I.S';
        $menu = 'I.S.B.1';
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('sales/postsales/salesorder'));
        }
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

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
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




    // =================================== DELIVERY ORDER ===========================================


    public function deliveryorder()
    {
        $data['title']="Delivery Order";
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
        $dtl = $this->m_postsales->q_deliveryorder_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntryDeliveryOrder');
            $urlnext = base_url('sales/postsales/addDeliveryOrder');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.S.B.2';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_list_deliveryorder',$data);
    }

    function detailDeliveryOrder()
    {
        /* Penambahan Squence */
        $data['title']="Detail DeliveryOrder";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/postsales/deliveryorder'));
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
        $data['dtldata'] = $this->m_postsales->q_deliveryorder_master($param)->getRowArray();
        return $this->template->render('sales/postsales/v_detail_deliveryorder',$data);
    }

    function list_deliveryorder(){
        $list = $this->m_postsales->get_t_front_deliveryorder_view();
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


        $kmenu = 'I.S.B.2';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';
        $canDelete = isset($datadtl['dtl_akses']['a_delete']) && trim($datadtl['dtl_akses']['a_delete']) === 't';

        foreach ($list as $lm) {
            $no++;
            $row = array();

            $status = strtoupper(trim($lm->status_desc));
            $statusRaw = strtoupper(trim((string) ($lm->status ?? '')));
            // Status 'D' = DRAFT (alur trigger DO); master trxtype memetakan 'D' ke DISAPPROVED.
            if ($statusRaw === 'D') {
                $status = 'DRAFT';
            }

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

            if ($canUpdate && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ){
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateDeliveryOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update DeliveryOrder 
                </a>';
            }
            // Draft (status 'D') bisa dilanjutkan: revisi draft memindahkan
            // dokumen kembali ke sc_tmp dengan status 'E'.
            if ($updateBtn === '' && $canUpdate && trim($lm->inputby) == $nama
                && empty($lm->printby) && empty($lm->printdate)
                && trim($status) == 'DRAFT'
            ) {
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateDeliveryOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Lanjutkan Draft DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Lanjutkan Draft 
                </a>';
            }



            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailDeliveryOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail DeliveryOrder 
                </a>';
            }

            if ($canPrint && (trim($status) == 'FINAL USER' || trim($status) == 'CETAK/PRINT')) {
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_deliveryorder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print DeliveryOrder 
                </a>';
            }

            if ($canDelete && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
                $cancelBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-undo"></i> Batalkan Delivery Order</a>';
            }
            // Draft (status 'D') boleh dibatalkan tanpa revert stok & qtydo SO.
            if ($cancelBtn === '' && $canDelete && trim($lm->inputby) == $nama
                && empty($lm->printby) && empty($lm->printdate)
                && trim($status) == 'DRAFT'
            ) {
                $cancelBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-undo"></i> Batalkan Delivery Order</a>';
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
                if ($canDelete)   $menuContent .= $cancelBtn;
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
            if ($statusRaw === 'D') {
                $status = 'DRAFT';
            }

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
                case 'SJ PARTIAL':
                    $badgeClass = 'badge-primary';
                    break;
                case 'SJ FULL':
                    $badgeClass = 'badge-cetak';
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

            

            $row[] = $lm->kdcust;
            $row[] = $lm->nmcust;
            $row[] = $lm->alamatcust;
            $row[] = $lm->nmkota;
            // $row[] = $lm->kdcustdeliv;
            $row[] = $lm->nmcustdeliv;
            $row[] = $lm->alamatcustdeliv;
            $row[] = $lm->nmkotadeliv;
            // $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            // $docdate  = trim($lm->docdate);
            // $jthtempo = (int) $lm->jthtempo;

            // if (!empty($docdate)) {

            //     $date = new \DateTime(trim($lm->docdate));
            //     $date->modify("+{$jthtempo} days");

            //     $jatuhTempo = $date->format('d/m/Y');

            // } else {
            //     $jatuhTempo = '';
            // }

            // $row[] = $jatuhTempo;

            $row[] = $lm->nmsalesman;
            // $row[] = $lm->pocust;
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_front_deliveryorder_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_deliveryorder_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_deliveryorder_apprv(){
        $list = $this->m_postsales->get_t_front_deliveryorder_apprv_view();
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


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
                    href="' . base_url('sales/postsales/updateDeliveryOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update DeliveryOrder 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailDeliveryOrder') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail DeliveryOrder 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print DeliveryOrder : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print DeliveryOrder 
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

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_front_deliveryorder_apprv_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_deliveryorder_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntryDeliveryOrder()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_postsales->q_deliveryorder_master_temp($param);
        $row = $dtl->getRowArray();
        if(empty($row)){
            return redirect()->to(base_url('sales/postsales/deliveryorder'));
        }
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/postsales/pp'));
        // }
        $status   = trim($row['status']);
        /* docnotmp diisi trigger saat revisi (sc_trx status D/F -> E) =
           docno asli dokumen di sc_trx.deliveryorder */
        $docnotmp = trim((string) ($row['docnotmp'] ?? ''));
        $builder = $this->db->table('sc_tmp.deliveryorder');
        $builder_dtl = $this->db->table('sc_tmp.deliveryorder_dtl');

        if ($status==='I') {
            $builder->where('inputby',$nama);
            $builder->delete();
            // detail temporary ikut dibersihkan (tidak menyisakan baris yatim)
            $builder_dtl->where('inputby',$nama);
            $builder_dtl->delete();
            return redirect()->to(base_url('sales/postsales/deliveryorder'));
        } else if ($status==='E') {
            /* Satu transaksi: pembatalan entry temporary dan pengembalian status
               dokumen di sc_trx harus berhasil bersama-sama. */
            $this->db->transBegin();
            try {
                // Batal input di tmp (E -> C). Trigger sc_tmp.tr_deliveryorder_finalize()
                // yang menghapus header + detail temporary.
                $builder->where('inputby',$nama);
                $builder->update(array('status' => 'C'));

                /* Detail ditemukan / dokumen sudah ada di sc_trx: status DO di sc_trx
                   WAJIB kembali ke 'D' (draft). Saat Edit, sc_trx berubah D/F -> 'E'
                   dan datanya disalin ke sc_tmp (docnotmp = docno asli); kalau entry
                   dibatalkan (clear entry) status 'E' tertinggal sehingga DO tidak
                   tampil di tab Draft maupun tab status lain dan tidak bisa
                   dilanjutkan. */
                if ($docnotmp !== '') {
                    $hasDtl = $this->db->query(
                        "select 1 as ada from sc_trx.deliveryorder_dtl where trim(docno) = ? limit 1",
                        [$docnotmp]
                    )->getRowArray();

                    if (!empty($hasDtl)) {
                        $builderTrx = $this->db->table('sc_trx.deliveryorder');
                        $builderTrx->where('trim(docno)', $docnotmp);
                        $builderTrx->where("trim(status) = 'E'");
                        $builderTrx->update(array(
                            'status'     => 'D',
                            'updateby'   => $nama,
                            'updatedate' => date('Y-m-d H:i:s'),
                        ));
                    }
                }

                $this->db->transCommit();
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return redirect()->to(base_url('sales/postsales/deliveryorder'));
            }

            $result = array('status' => true, 'messages' => 'Sukses Di Proses');
            echo json_encode($result);
            return redirect()->to(base_url('sales/postsales/deliveryorder'));
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('sales/postsales/deliveryorder'));
        }

    }

    function addDeliveryOrder()
    {
        /* Penambahan Squence */
        $data['title']="Input Delivery Order";
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


        // Pesan dari validasi simpan final (mis. stok tidak mencukupi)
        $doFlashError = session()->getFlashdata('do_error');
        if (!empty($doFlashError)) {
            $data['message'] .= "<div class='alert alert-warning'>" . esc($doFlashError) . "</div>";
        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_postsales->q_deliveryorder_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_postsales->q_deliveryorder_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_add_deliveryorder_inline',$data);
    }


    public function getBranchInfoDeliveryOrder()
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays

        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['deliveryorder']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
    }

    public function getNextSuffixDeliveryOrder()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.deliveryorder')
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

    public function initDeliveryOrderHeader()
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

        $builder = $this->db->table('sc_tmp.deliveryorder');
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



    public function saveDeliveryOrderDetail()
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
        $builderHeader = $db->table('sc_tmp.deliveryorder');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST

        if ($exists == 0) {

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                // 'delivdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('delivdate')))),
                // 'jthtempo'     => $this->request->getPost('jthtempo'),
                // 'isinclusive'     => $isinclusive,
                // 'isopenprice'     => $isopenprice,

                'kdcustomer'    => strtoupper($this->request->getPost('kdcustomer')),
                'alamatcustomer'    => strtoupper($this->request->getPost('alamatcustomer')),
                'kdcustomerdeliv'    => strtoupper($this->request->getPost('kdcustomerdeliv')),
                'alamatcustomerdeliv'    => strtoupper($this->request->getPost('alamatcustomerdeliv')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                // 'idtax'    => strtoupper($this->request->getPost('idtax')),
                'kdsalesman'    => ($this->request->getPost('kdsalesman')),
                // 'gradecustomer'    => ($this->request->getPost('gradecustomer')),
                // 'carabayar'    => strtoupper($this->request->getPost('carabayar')),
                // 'pocust'    => strtoupper($this->request->getPost('pocust')),
                // 'currcode'    => strtoupper($this->request->getPost('currcode')),
                // 'kurs'    => ($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                // 'pocust'    => strtoupper($this->request->getPost('pocust')),
                // 'nodp'    => strtoupper($this->request->getPost('nodp')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.deliveryorder_dtl');
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
            // $volitem   = $this->request->getPost('volitem') ?: 0;
            // $biaya   = $this->request->getPost('biaya') ?: 0;
            // $biaya2   = $this->request->getPost('biaya2') ?: 0;
            $idgudang = strtoupper($this->request->getPost('idgudang'));

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                // 'qtybonus'     => $qtybonus,
                // 'harga'        => $harga,
                // 'multidisc'    => $multidisc,
                // 'nilai'        => $nilai,
                // 'nilaikonversi' => $nilaikonversi,
                // 'nilaipajak' => $nilaipajak,
                // 'idtax' => $idtax,
                // 'kurs' => $kurs,
                // 'currcode' => $poData['currcode'] ?? '',
                // 'volitem'      => $volitem,

                // 'biaya'      => $biaya,
                // 'biaya2'      => $biaya2,
                // 'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                // 'idspec'      => $idspec,

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
                    qtypenjualan,
                    idgudang,
                    qtydo
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
                $qtyDOSaatIni = 0;

                $existingDO = $db->query("
                    SELECT qty 
                    FROM sc_tmp.deliveryorder_dtl 
                    WHERE
                    uniqueid = ?
                ", [$row->uniqueid])->getRow();

                if ($existingDO) {
                    $qtyDOSaatIni = $existingDO->qty;
                }

                $sisaQty = $row->qty - ($row->qtypenjualan + $row->qtydo - $qtyDOSaatIni);
                if ($sisaQty <= 0) continue;

                $duplicate = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnoso'       => $docnoso,
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $sisaQty,
                        // 'idprincipal'   => $row->idprincipal,
                        'idgudang'      => $row->idgudang,
                        // 'idspec'        => $row->idspec,
                        // 'harga'         => $row->harga, // Default 0 untuk new insert
                        // 'multidisc'     =>  $row->multidisc, // Default 0 untuk new insert
                        // 'nilaipajak'    => $nilaipajakRow,
                        // 'nilaikonversi' => $nilaikonversiRow,
                        // 'currcode'      => $row->currcode,
                        // 'kurs'          => $row->kurs,
                        // 'idtax'         => $row->idtax,
                        // 'nilai'         => $sisaQty * $row->harga, // Default 0 untuk new insert
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


        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusDeliveryOrder()
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
        $builder = $db->table('sc_trx.deliveryorder');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // A / R / C (1 huruf)
                'I.S',             // kode module dari menuprg
                'I.S.B.2'          // kode menu untuk PENJUALAN
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function updateDeliveryOrder()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_postsales->q_deliveryorder_master($param)->getRowArray();
        $status = strtoupper(trim((string) ($dtl['status'] ?? '')));

        // D = Draft  : revisi draft (D -> E) tanpa revert qtydo / stok
        // F = Final  : revisi final (F -> E) dengan revert qtydo / stok
        // P = Printed: sama seperti final
        if (in_array($status, ['D', 'F', 'P'], true)) {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.deliveryorder');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/postsales/addDeliveryOrder'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/postsales/deliveryorder'));
        }
    }

    function showing_deliveryordertrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_deliveryorder_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_deliveryordertemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_deliveryorder_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_deliveryorder_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_postsales->q_deliveryorder_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_deliveryorder_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.deliveryorder_dtl')
            ->where('uniqueid', $id)
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

    public function delete_deliveryorder_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.deliveryorder_dtl');
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
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // ======================================
            // CEK HEADER MASIH DRAFT (belum final)
            // ======================================
            $docno = $rows[0]['docno'];

            $headerRow = $db->table('sc_tmp.deliveryorder')
                ->select('status')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if (!$headerRow || strtoupper(trim($headerRow['status'])) === 'F') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah final, tidak dapat diubah'
                ]);
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
            $builder
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->delete();

            $sisa = $db->table('sc_tmp.deliveryorder_dtl')
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->countAllResults();

            if ($sisa > 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data DO Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function list_tmp_deliveryorder_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_deliveryorder_dtl_temp_view($docno);
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->uniqueid;
            //item
            // $row[] = $lm->docnosj;
            $row[] = $lm->docnoso;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            // $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            // $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 4, '.', ',') . '% </div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
            // $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_deliveryorder_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_deliveryorder_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_deliveryorder_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_deliveryorder_dtl_view($docno);
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            // $row[] = $lm->docnosj;
            $row[] = $lm->docnoso;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            // $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            // $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
            // $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;

        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_deliveryorder_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_deliveryorder_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    public function finalEntryDeliveryOrder_SJ()
    {
        $nama = trim($this->session->get('nama'));

        /*
        ==========================
        1. VALIDASI DATA DO (SAMA seperti finalEntryDeliveryOrder)
        ==========================
        */
        $param     = " and coalesce(inputby,'')='$nama'";
        $paramdtl  = " AND COALESCE(inputby, '') = '$nama' 
                    AND (COALESCE(unit, '') = ''  
                    OR qty = '0.00' 
                    OR qty = '0' 
                    OR COALESCE(nmbarang, '') = '') ";
        $paramdtl2 = " and coalesce(inputby,'')='$nama'";

        $header = $this->m_postsales->q_deliveryorder_master_temp($param);
        $status = trim($header->getRowArray()['status']);
        $cek    = $this->m_postsales->q_deliveryorder_dtl_temp($paramdtl);
        $cek2   = $this->m_postsales->q_deliveryorder_dtl_temp($paramdtl2);

        $builder = $this->db->table('sc_tmp.deliveryorder');

        // Bersihkan trxerror
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.2');
        $builder_trxerror->delete();

        if (($status === 'E' && $cek->getNumRows() > 0) || ($cek2->getNumRows() <= 0)) {
            $builder_trxerror->insert([
                'userid'      => $nama,
                'errorcode'   => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul'       => 'I.S.B.2',
            ]);
            return redirect()->to(base_url('/sales/postsales/addDeliveryOrder'));
        }

        /*
        ==========================
        2. AMBIL POST DATA & UPDATE HEADER TMP (SAMA)
        ==========================
        */
        $kdcustomer          = trim($this->request->getPost('kdcustomer'));
        $alamatcustomer      = trim($this->request->getPost('alamatcustomer'));
        $kdcustomerdeliv     = trim($this->request->getPost('kdcustomerdeliv'));
        $alamatcustomerdeliv = trim($this->request->getPost('alamatcustomerdeliv'));
        $salesman            = trim($this->request->getPost('kdsalesman'));
        $keterangan          = trim($this->request->getPost('keterangan'));

        $updateHeader = [
            'kdcustomer'          => strtoupper($kdcustomer),
            'alamatcustomer'      => strtoupper($alamatcustomer),
            'kdcustomerdeliv'     => strtoupper($kdcustomerdeliv),
            'alamatcustomerdeliv' => strtoupper($alamatcustomerdeliv),
            'keterangan'          => strtoupper($keterangan),
            'kdsalesman'          => $salesman,
        ];

        $builder->where('inputby', $nama);
        $builder->update($updateHeader);

        /*
        ==========================
        3. FINALIZE DO (E → F) — SAMA
        Trigger tr_deliveryorder_finalize jalan:
        - insert ke sc_trx.deliveryorder + dtl
        - update qtydo di salesorder_dtl
        - insert logtrans
        - delete tmp DO
        ==========================
        */
        $builder->where('inputby', $nama);
        $ok = $builder->update(['status' => 'F']);

        if (!$ok) {
            $builder_trxerror->insert([
                'userid'      => $nama,
                'errorcode'   => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul'       => 'I.S.B.2',
            ]);
            return redirect()->to(base_url('/sales/postsales/addDeliveryOrder'));
        }

        /*
        ==========================
        4. AMBIL DOCNO DO DARI sc_log.logtrans
        (yang barusan diinsert oleh trigger)
        ==========================
        */
        $log = $this->db->table('sc_log.logtrans')
            ->where('inputby', $nama)
            ->where('modul', 'PENJUALAN')
            ->where('menu', 'DELIVERY ORDER')
            ->orderBy('inputdate', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$log) {
            // Tidak ada log → tidak bisa lanjut, redirect ke halaman DO
            return redirect()->to(base_url('/sales/postsales/deliveryorder'));
        }

        $docnoDO = trim($log['docno']);

        /*
        ==========================
        5. AMBIL HEADER DO DARI sc_trx.deliveryorder
        ==========================
        */
        $headerDO = $this->db->table('sc_trx.deliveryorder')
            ->where('docno', $docnoDO)
            ->get()
            ->getRowArray();

        if (!$headerDO) {
            return redirect()->to(base_url('/sales/postsales/deliveryorder'));
        }

        /*
        ==========================
        6. VALIDASI: SEMUA qtysj = qty?
        Kalau semua sudah sama → tidak perlu SJ baru
        ==========================
        */
        $totalBelumSJ = $this->db->table('sc_trx.deliveryorder_dtl')
            ->where('docno', $docnoDO)
            ->where('COALESCE(qtysj, 0) < qty', null, false)
            ->countAllResults();

        if ($totalBelumSJ === 0) {
            // Semua sudah di-SJ → langsung redirect ke addSuratJalan
            return redirect()->to(base_url('/sales/postsales/addSuratJalan'));
        }

        /*
        ==========================
        7. CLEAR TMP SJ MILIK USER INI
        (harus ada 1 docno per user)
        ==========================
        */
        $this->db->table('sc_tmp.suratjalan_dtl')
            ->where('inputby', $nama)
            ->delete();

        $this->db->table('sc_tmp.suratjalan')
            ->where('inputby', $nama)
            ->delete();

        /*
        ==========================
        8. AMBIL KONFIGURASI UMUM (prefix suratjalan)
        ==========================
        */
        $konfig = $this->db->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getRowArray();

        $prefix = trim($konfig['suratjalan'] ?? '');

        if ($prefix === '') {
            throw new \RuntimeException('Prefix suratjalan belum dikonfigurasi.');
        }

        /*
        ==========================
        9. TENTUKAN kodeSuffix DARI CABANG DO
        ==========================
        */
        $idbranch = trim($headerDO['cabang']);

        $branch = $this->db->table('sc_mst.branchjob')
            ->select('nmbranch')
            ->where('idbranch', $idbranch)
            ->get()
            ->getRowArray();

        if (!$branch) {
            throw new \RuntimeException('Cabang DO tidak ditemukan di branchjob.');
        }

        $map = [
            'PT JATIM TAMAN STEEL MFG' => 'PT',
            'PLANT I'                  => 'PA',
            'PLANT II'                 => 'PB',
        ];

        $kodeSuffix = $map[trim($branch['nmbranch'])] ?? '';

        if ($kodeSuffix === '') {
            throw new \RuntimeException('Mapping cabang belum diset: ' . $branch['nmbranch']);
        }

        /*
        ==========================
        10. INFIX DARI logindate
        ==========================
        */
        $logindate = $this->session->get('logindate');
        $infix     = date('ym', strtotime($logindate));

        /*
        ==========================
        11. GENERATE DOCNO SJ
        ==========================
        */
        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $lastSJ = $this->db->query("
            SELECT docno FROM sc_trx.suratjalan 
            WHERE docno LIKE ? 
            UNION ALL
            SELECT docno FROM sc_tmp.suratjalan 
            WHERE docno LIKE ? 
            ORDER BY docno DESC LIMIT 1
        ", [$like . '%', $like . '%'])->getRowArray();

        if ($lastSJ) {
            $parts = explode('/', trim($lastSJ['docno']));
            $last  = substr($parts[2], strlen($kodeSuffix));
            $next  = str_pad(((int)$last) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        $docnoSJ = $prefix . '/' . $infix . '/' . $kodeSuffix . $next;

        /*
        ==========================
        12. INSERT HEADER SJ KE TMP (status E)
        ==========================
        */
        $this->db->table('sc_tmp.suratjalan')->insert([
            'docno'               => $docnoSJ,
            'docdate'             => $headerDO['docdate'],
            'cabang'              => $headerDO['cabang'],
            'pemohon'             => $headerDO['pemohon'],
            'kdcustomer'          => $headerDO['kdcustomer'],
            'nmcustomer'          => $headerDO['nmcustomer'],
            'alamatcustomer'      => $headerDO['alamatcustomer'],
            'gradecustomer'       => $headerDO['gradecustomer'],
            'kdcustomerdeliv'     => $headerDO['kdcustomerdeliv'],
            'nmcustomerdeliv'     => $headerDO['nmcustomerdeliv'],
            'alamatcustomerdeliv' => $headerDO['alamatcustomerdeliv'],
            'kdsalesman'          => $headerDO['kdsalesman'],
            'jthtempo'            => $headerDO['jthtempo'],
            'idtax'               => $headerDO['idtax'],
            'isinclusive'         => $headerDO['isinclusive'],
            'isopenprice'         => $headerDO['isopenprice'],
            'currcode'            => $headerDO['currcode'],
            'kurs'                => $headerDO['kurs'],
            'dpp'                 => $headerDO['dpp'],
            'jumlahpajak'         => $headerDO['jumlahpajak'],
            'total'               => $headerDO['total'],
            'carabayar'           => $headerDO['carabayar'],
            'keterangan'          => $headerDO['keterangan'],
            'status'              => 'E',
            'inputby'             => $nama,
            'inputdate'           => date('Y-m-d H:i:s'),
        ]);

        /*
        ==========================
        13. INSERT DETAIL SJ KE TMP
            docnodo = docnoDO
            qty = qtydo - qtysj (sisa)
        ==========================
        */
        $details = $this->db->table('sc_trx.deliveryorder_dtl')
            ->where('docno', $docnoDO)
            ->where('COALESCE(qtysj, 0) < qty', null, false)
            ->get()
            ->getResultArray();

        $detailSJ = [];
        foreach ($details as $d) {
            $sisaQty = (float)$d['qty'] - (float)($d['qtysj'] ?? 0);

            if ($sisaQty <= 0) continue;

            $detailSJ[] = [
                'docno'          => $docnoSJ,
                // 'docnoso'        => $d['docnoso'],
                'docnodo'        => $docnoDO,          // ← dari logtrans
                'uniqueid'       => $d['uniqueid'],
                'idbarang'       => $d['idbarang'],
                'nmbarang'       => $d['nmbarang'],
                // 'idprincipal'    => $d['idprincipal'],
                'idgudang'       => $d['idgudang'],
                // 'idspec'         => $d['idspec'],
                'unit'           => $d['unit'],
                'qty'            => $sisaQty,
                // 'harga'          => $d['harga'],
                // 'multidisc'      => $d['multidisc'],
                // 'nilai'          => $d['nilai'],
                // 'idtax'          => $d['idtax'],
                // 'currcode'       => $d['currcode'],
                // 'kurs'           => $d['kurs'],
                // 'nilaikonversi'  => $d['nilaikonversi'],
                // 'nilaipajak'     => $d['nilaipajak'],
                // 'bomdesc'        => $d['bomdesc'],
                // 'description'    => $d['description'],
                'status'         => 'E',
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($detailSJ)) {
            $this->db->table('sc_tmp.suratjalan_dtl')->insertBatch($detailSJ);
        }

        /*
        ==========================
        14. REDIRECT KE addSuratJalan
            (JANGAN finalize SJ — biarkan E di tmp)
        ==========================
        */
        return redirect()->to(base_url('/sales/postsales/addSuratJalan'));
    }


    function finalEntryDeliveryOrder(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $builder = $this->db->table('sc_tmp.deliveryorder');

        // Bila form mengirim docno, finalize hanya untuk dokumen tersebut.
        $docnoPost = strtoupper(trim((string) $this->request->getPost('docno')));
        if ($docnoPost !== '') {
            $builder->where('docno', $docnoPost);
        }

        // Ambil header draft milik user (bind via Query Builder, hindari interpolasi SQL)
        $headerRow = $builder
            ->where('inputby', $nama)
            ->orderBy('inputdate', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $status = $headerRow ? trim($headerRow['status']) : '';
        $docno  = $headerRow ? trim($headerRow['docno']) : '';

        // Detail tidak valid (unit kosong / qty 0 / nmbarang kosong)
        $cek = $this->db->table('sc_tmp.deliveryorder_dtl')
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->groupStart()
            ->where('unit', '')
            ->orWhere('qty', '0.00')
            ->orWhere('qty', '0')
            ->orWhere('nmbarang', '')
            ->groupEnd()
            ->countAllResults();

        // Total detail
        $cek2 = $this->db->table('sc_tmp.deliveryorder_dtl')
            ->where('inputby', $nama)
            ->where('docno', $docno)
            ->countAllResults();

        // ------------------------------------------------------------------
        // Validasi stok akhir (gudang + idbarang + spec).
        // Stok KOSONG (0 / minus) => HANYA boleh disimpan sebagai draft.
        // ------------------------------------------------------------------
        $doModel    = new DeliveryOrderModel();
        $stokKurang = [];
        if ($docno !== '') {
            $rowsStok = $this->db->table('sc_tmp.deliveryorder_dtl')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getResultArray();

            // Stok posisi tanggal dokumen DO (sc_trx.stkblc s/d docdate header).
            $docdateStok = $doModel->normalDate($headerRow['docdate'] ?? '');
            $stokInfo    = $doModel->stokForRows($rowsStok, $docdateStok);

            foreach ($rowsStok as $dRow) {
                $info = $stokInfo[(int) ($dRow['idurut'] ?? 0)] ?? null;
                $stok = $info ? (float) $info['stokakhir'] : 0.0;
                $qty  = $doModel->num($dRow['qty'] ?? 0);

                if ($stok <= 0) {
                    $stokKurang[] = trim((string) ($dRow['idbarang'] ?? ''))
                        . ' / ' . trim((string) ($dRow['idgudang'] ?? ''))
                        . ' / ' . (trim((string) ($dRow['idspec'] ?? '')) !== '' ? trim((string) $dRow['idspec']) : '-')
                        . ' : stok ' . number_format($stok, 4, '.', ',')
                        . ', butuh ' . number_format($qty, 4, '.', ',');
                }
            }
        }

        if (!empty($stokKurang)) {
            return redirect()
                ->to(base_url('/sales/postsales/addDeliveryOrder'))
                ->with('do_error', 'Stok kosong sehingga tidak bisa Simpan Final: '
                    . implode('; ', array_slice($stokKurang, 0, 5))
                    . '. Simpan sebagai DRAFT atau perbaiki gudang / spec.');
        }

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.2');
        $builder_trxerror->delete();


        if (($status==='E' and $cek > 0) or ($cek2 <= 0))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek,
                'nomorakhir2' => $cek2,
                'modul' => 'I.S.B.2',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addDeliveryOrder'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            // $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            // $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdcustomer   = trim($this->request->getPost('kdcustomer'));
            $alamatcustomer   = trim($this->request->getPost('alamatcustomer'));
            // $gradecustomer   = trim($this->request->getPost('gradecustomer'));
            $kdcustomerdeliv   = trim($this->request->getPost('kdcustomerdeliv'));
            $alamatcustomerdeliv   = trim($this->request->getPost('alamatcustomerdeliv'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            // $keterangan   = trim($this->request->getPost('keterangan'));
            // $currcode   = trim($this->request->getPost('currcode'));
            $salesman   = trim($this->request->getPost('kdsalesman'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            // $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            // $carabayar   = trim($this->request->getPost('carabayar'));
            // $pocust   = trim($this->request->getPost('pocust'));
            // $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';
            // $isopenprice = $this->request->getPost('isopenprice') ? 'YES' : 'NO';



            // **BERSIHKAN FORMAT KURS**
            // $kurs = trim($this->request->getPost('kurs'));
            // $kurs_clean = 0;
            // if (!empty($kurs)) {
            //     $kurs_clean = str_replace(',', '', $kurs);
            //     // $kurs_clean = str_replace('.', '.', $kurs_clean);
            //     // $kurs_clean = floatval($kurs_clean);
            // }

            // Convert expdate ke format YYYY-MM-DD
            // $docdateph = null;
            // if (!empty($docdate)) {
            //     $docdateph = date('Y-m-d', strtotime($docdate));
            // }

            // $delivdateph = null;
            // if (!empty($delivdate)) {
            //     $delivdateph = date('Y-m-d', strtotime($delivdate));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'docdate'        => $docdateph,
                // 'delivdate'       => $delivdateph,
                // 'jthtempo'       => $jthtempo,
                'kdcustomer'     => strtoupper($kdcustomer),
                'alamatcustomer' => strtoupper($alamatcustomer),
                // 'gradecustomer' => strtoupper($gradecustomer),
                'kdcustomerdeliv'     => strtoupper($kdcustomerdeliv),
                'alamatcustomerdeliv' => strtoupper($alamatcustomerdeliv),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                // 'currcode'       => $currcode,
                'kdsalesman'       => $salesman,
                // 'kurs'           => $kurs_clean,
                // 'isinclusive'    => strtoupper($isinclusive),
                // 'isopenprice'    => strtoupper($isopenprice),
                // 'idtax'          => strtoupper($idtax),
                // 'pocust'         => strtoupper($pocust),
                // 'carabayar'         => strtoupper($carabayar),
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            try {
                $this->db->transStart();

                // Update header (scoped ke pemilik + docno)
                $this->db->table('sc_tmp.deliveryorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update($updateHeader);

                // FINALIZE: CAS status E -> F (trigger tmp->trx berjalan di sini)
                $this->db->table('sc_tmp.deliveryorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update(['status' => 'F']);

                $errDb = $this->sales_errorDb();

                // read-back: trigger sukses akan menghapus baris tmp
                $sisa = $this->db->table('sc_tmp.deliveryorder')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->countAllResults();

                if ($errDb !== '' || $sisa > 0) {
                    $this->db->transRollback();

                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 3,
                        'nomorakhir1' => $cek,
                        'nomorakhir2' => $cek2,
                        'modul' => 'I.S.B.2',
                    );
                    $builder_trxerror->insert($infotrxerror);
                    return redirect()->to(base_url('/sales/postsales/addDeliveryOrder'));
                }

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek,
                    'nomorakhir2' => $cek2,
                    'modul' => 'I.S.B.2',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addDeliveryOrder'));
            }

            return redirect()->to(base_url('/sales/postsales/deliveryorder'));



        }

    }


    function show_deliveryorder(){
        $module = 'I.S';
        $menu = 'I.S.B.2';
        $table = "sc_trx.deliveryorder";
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.deliveryorder');

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

        $title = " Report Delivery Order";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_deliveryorder/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_deliveryorder.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_deliveryorder(){
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
        $datamst = $this->m_postsales->q_deliveryorder_master($param);
        $datadtl = $this->m_postsales->q_deliveryorder_dtl($param);
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










    // =================================== SURAT JALAN ===========================================


    public function suratjalan()
    {
        $data['title']="Surat Jalan";
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
        $dtl = $this->m_postsales->q_suratjalan_master_temp($param);
        $logindate = trim($this->session->get('logindate'));

        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntrySuratJalan');
            $urlnext = base_url('sales/postsales/addSuratJalan');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.S.B.3';
        $role = trim($this->session->get('roleid'));
        $data['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        //auto insert unit
        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_list_suratjalan',$data);
    }

    function detailSuratJalan()
    {
        /* Penambahan Squence */
        $data['title']="Detail SuratJalan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));

        $docno = $this->request->getGet('docno');
        if (empty($docno)) {
            return redirect()->to(base_url('sales/postsales/suratjalan'));
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
        $data['typeform'] = 'DETAIL';
        $data['userlogin'] = $nama;
        $data['docnoParam'] = $decoded_docno;
        $data['dtldata'] = $this->m_postsales->q_suratjalan_master($param)->getRowArray();
        return $this->template->render('sales/postsales/v_detail_suratjalan',$data);
    }

    function list_suratjalan(){
        $list = $this->m_postsales->get_t_front_suratjalan_view();
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


        $kmenu = 'I.S.B.3';
        $nama=trim($this->session->get('nama'));
        $role=trim($this->session->get('roleid'));

        $datadtl['dtl_akses'] = $this->m_role->detail_user_akses($role, $kmenu)->getRowArray();
        $dataanu['userinfo'] = $this->m_user->getUser(" and username='$nama'")->getRowArray();

        $canUpdate = isset($datadtl['dtl_akses']['a_update']) && trim($datadtl['dtl_akses']['a_update']) === 't';
        $canPrint = isset($datadtl['dtl_akses']['a_report']) && trim($datadtl['dtl_akses']['a_report']) === 't';
        $canView = isset($datadtl['dtl_akses']['a_view']) && trim($datadtl['dtl_akses']['a_view']) === 't';
        // $canApprove = isset($datadtl['dtl_akses']['a_approve1']) && trim($datadtl['dtl_akses']['a_approve1']) === 't';
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

            if ($canUpdate && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ){
                $updateBtn = '
                <a class="dropdown-item bg-warning" 
                    href="' . base_url('sales/postsales/updateSuratJalan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This SuratJalan : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update SuratJalan 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailSuratJalan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail SuratJalan : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail SuratJalan 
                </a>';
            }

            if ($canPrint && (trim($status) == 'FINAL USER' || trim($status) == 'CETAK/PRINT')) {
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_suratjalan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print SuratJalan : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print SuratJalan 
                </a>';
            }

            if ($canDelete && trim($lm->inputby) == $nama && empty($lm->printby) &&
                empty($lm->printdate) &&
                trim($status) == 'FINAL USER'
            ) {
                $cancelBtn = '<a class="dropdown-item bg-danger" href="#" onclick="setToCancel(\'' . trim($lm->docno) . '\');">
                    <i class="fa fa-undo"></i> Batalkan Surat Jalan</a>';
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
                if ($canDelete)   $menuContent .= $cancelBtn;
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

            

            $row[] = $lm->kdcust;
            $row[] = $lm->nmcust;
            $row[] = $lm->alamatcust;
            $row[] = $lm->nmkota;
            // $row[] = $lm->kdcustdeliv;
            $row[] = $lm->nmcustdeliv;
            $row[] = $lm->alamatcustdeliv;
            $row[] = $lm->nmkotadeliv;
            // $row[] = $lm->currcode;
            // $row[] = date(
            //     'd/m/Y',
            //     strtotime(trim($lm->senddate))
            // );
            // $docdate  = trim($lm->docdate);
            // $jthtempo = (int) $lm->jthtempo;

            // if (!empty($docdate)) {

            //     $date = new \DateTime(trim($lm->docdate));
            //     $date->modify("+{$jthtempo} days");

            //     $jatuhTempo = $date->format('d/m/Y');

            // } else {
            //     $jatuhTempo = '';
            // }

            // $row[] = $jatuhTempo;

            $row[] = $lm->nmsalesman;
            // $row[] = $lm->pocust;
            $row[] = $lm->keterangan;

            $row[] = $lm->nmbranch;

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_front_suratjalan_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_suratjalan_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_suratjalan_apprv(){
        $list = $this->m_postsales->get_t_front_suratjalan_apprv_view();
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


        $kmenu = 'I.S.B.3';
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
                    href="' . base_url('sales/postsales/updateSuratJalan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Update This SuratJalan : ' . $docno . '\')">
                    <i class="fa fa-edit"></i> Update SuratJalan 
                </a>';
            }

            if($canView){
                $detailBtn =
                    '<a class="dropdown-item" 
                    style="background-color:#3badf6;" 
                    href="' . base_url('sales/postsales/detailSuratJalan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'View Detail SuratJalan : ' . $docno . '\')">
                    <i class="fa fa-eye"></i> Detail SuratJalan 
                </a>';
            }

            if($canPrint){
                $printBtn = '
                <a class="dropdown-item" 
                    style="background-color:#00ff8e;" 
                    href="' . base_url('sales/postsales/show_po') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return confirm(\'Print SuratJalan : ' . $docno . '\')">
                    <i class="fa fa-print"></i> Print SuratJalan 
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

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_front_suratjalan_apprv_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_suratjalan_apprv_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function clearEntrySuratJalan()
    {
        $nama=trim($this->session->get('nama'));
        $param = " and coalesce(inputby,'')='$nama'";
        $dtl = $this->m_postsales->q_suratjalan_master_temp($param);
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('sales/postsales/suratjalan'));
        }
        // if(isEmpty($dtl->getRowArray()['status'])){
        //     return redirect()->to(base_url('sales/postsales/pp'));
        // }
        $status = trim($dtl->getRowArray()['status']);
        $builder = $this->db->table('sc_tmp.suratjalan');
        $builder_dtl = $this->db->table('sc_tmp.suratjalan_dtl');

        if ($status==='I') {
            // $builder= $this->db->table('sc_tmp.standart_usage_mst');
            $builder->where('inputby',$nama);
            $builder->delete();
            // $builderDtl= $this->db->table('sc_tmp.pp');
            // $builderDtl->where('inputby',$nama);
            // $builderDtl->delete();
            return redirect()->to(base_url('sales/postsales/suratjalan'));
        } else if ($status==='E') {
            $builder->where('inputby',$nama);
            if ($builder->update(array('status' => 'C'))) {
                $result = array('status' => true, 'messages' => 'Sukses Di Proses');
                echo json_encode($result);
                return redirect()->to(base_url('sales/postsales/suratjalan'));
            }
            else {
                $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
                echo json_encode($result);
            }
        } else {
            // $result = array('status' => false, 'messages' => 'Data Gagal Di Proses Ada Kesalahan Data');
            // echo json_encode($result);
            return redirect()->to(base_url('sales/postsales/suratjalan'));
        }

    }

    function addSuratJalan()
    {
        /* Penambahan Squence */
        $data['title']="Input Surat Jalan";
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
        $data['mst'] = $this->m_postsales->q_suratjalan_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        $data['typeform'] = 'INPUT';
        $data['userlogin'] = $nama;
        $param = " and trim(inputby)='$nama'";
        $data['dtldata'] = $this->m_postsales->q_suratjalan_master_temp($param)->getRowArray();
        $logindate  = trim($this->session->get('logindate'));
        $ts    = strtotime($logindate);

        $pterror = " and userid='$nama'";
        $this->m_trxerror->q_deltrxerror($pterror);
        return $this->template->render('sales/postsales/v_add_suratjalan',$data);
    }


    public function getBranchInfoSuratJalan()
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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays

        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['suratjalan']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
    }

    public function getNextSuffixSuratJalan()
    {
        $prefix      = trim($this->request->getGet('prefix'));
        $infix       = trim($this->request->getGet('infix'));
        $kodeSuffix  = trim($this->request->getGet('kode_suffix'));

        $like = $prefix . '/' . $infix . '/' . $kodeSuffix;

        $row = $this->db->table('sc_trx.suratjalan')
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

    public function initSuratJalanHeader()
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

        $builder = $this->db->table('sc_tmp.suratjalan');
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



    public function saveSuratJalanDetail()
    {
        $nama   = trim($this->session->get('nama'));
        $docno  = strtoupper(trim($this->request->getPost('docno')));
        $docnodo = strtoupper(trim($this->request->getPost('docnodo')));
        $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya

        // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
        // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

        if (!$docno || !$docnodo) {
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
            ->where('docno', $docnodo)
            ->get()
            ->getRowArray();

        // Jika data SO tidak ditemukan, beri response error
        if (!$soData) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => "Data SO dengan nomor {$docnodo} tidak ditemukan"
            ]);
        }


        // =====================================================
        // CEK / INSERT HEADER
        // =====================================================
        $builderHeader = $db->table('sc_tmp.suratjalan');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;
        // Untuk pengambilan data dari POST

        if ($exists == 0) {

            $builderHeader->insert([
                'docno'     => $docno,
                'cabang'     => $this->request->getPost('cabang'),
                'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                // 'delivdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('delivdate')))),
                // 'jthtempo'     => $this->request->getPost('jthtempo'),
                // 'isinclusive'     => $isinclusive,
                // 'isopenprice'     => $isopenprice,

                'kdcustomer'    => strtoupper($this->request->getPost('kdcustomer')),
                'alamatcustomer'    => strtoupper($this->request->getPost('alamatcustomer')),
                'kdcustomerdeliv'    => strtoupper($this->request->getPost('kdcustomerdeliv')),
                'alamatcustomerdeliv'    => strtoupper($this->request->getPost('alamatcustomerdeliv')),
                // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
                // 'idtax'    => strtoupper($this->request->getPost('idtax')),
                'kdsalesman'    => ($this->request->getPost('kdsalesman')),
                // 'gradecustomer'    => ($this->request->getPost('gradecustomer')),
                // 'carabayar'    => strtoupper($this->request->getPost('carabayar')),
                // 'pocust'    => strtoupper($this->request->getPost('pocust')),
                // 'currcode'    => strtoupper($this->request->getPost('currcode')),
                // 'kurs'    => ($this->request->getPost('kurs')),
                'keterangan'    => strtoupper($this->request->getPost('keterangan')),
                // 'pocust'    => strtoupper($this->request->getPost('pocust')),
                // 'nodp'    => strtoupper($this->request->getPost('nodp')),
                'status'    => 'E',
                'inputby'   => $nama,
                'inputdate' => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.suratjalan_dtl');
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
            // $volitem   = $this->request->getPost('volitem') ?: 0;
            // $biaya   = $this->request->getPost('biaya') ?: 0;
            // $biaya2   = $this->request->getPost('biaya2') ?: 0;
            $idgudang = strtoupper($this->request->getPost('idgudang'));

            $builderDetail->where('uniqueid', $uniqueid)->update([
                'qty'          => $qty,
                // 'qtybonus'     => $qtybonus,
                // 'harga'        => $harga,
                // 'multidisc'    => $multidisc,
                // 'nilai'        => $nilai,
                // 'nilaikonversi' => $nilaikonversi,
                // 'nilaipajak' => $nilaipajak,
                // 'idtax' => $idtax,
                // 'kurs' => $kurs,
                // 'currcode' => $poData['currcode'] ?? '',
                // 'volitem'      => $volitem,

                // 'biaya'      => $biaya,
                // 'biaya2'      => $biaya2,
                // 'idprincipal'      => $idprincipal,
                'idgudang'      => $idgudang,
                // 'idspec'      => $idspec,

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
                    qtysj,
                    idgudang,
                    qtydo
                FROM sc_trx.salesorder_dtl
                WHERE TRIM(docno) = ?
            ", [$docnodo])->getResult();

            if (empty($soDetails)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data SO tidak ditemukan'
                ]);
            }

            foreach ($soDetails as $row) {
                $qtySJSaatIni = 0;

                $existingSJ = $db->query("
                    SELECT qty 
                    FROM sc_tmp.suratjalan_dtl 
                    WHERE
                    uniqueid = ?
                ", [$row->uniqueid])->getRow();

                if ($existingSJ) {
                    $qtySJSaatIni = $existingSJ->qty;
                }

                $sisaQty = $row->qty - ($row->qtysj - $qtySJSaatIni);
                if ($sisaQty <= 0) continue;

                $duplicate = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->countAllResults();

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnodo'       => $docnodo,
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $sisaQty,
                        // 'idprincipal'   => $row->idprincipal,
                        'idgudang'      => $row->idgudang,
                        // 'idspec'        => $row->idspec,
                        // 'harga'         => $row->harga, // Default 0 untuk new insert
                        // 'multidisc'     =>  $row->multidisc, // Default 0 untuk new insert
                        // 'nilaipajak'    => $nilaipajakRow,
                        // 'nilaikonversi' => $nilaikonversiRow,
                        // 'currcode'      => $row->currcode,
                        // 'kurs'          => $row->kurs,
                        // 'idtax'         => $row->idtax,
                        // 'nilai'         => $sisaQty * $row->harga, // Default 0 untuk new insert
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


        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'reload'  => $reload,
            'message' => $message
        ]);
    }


    public function updateStatusSuratJalan()
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
        $builder = $db->table('sc_trx.suratjalan');
        $builder->where('docno', $docno);
        /*tambahan sultan*/
        $info = array('status' => $status);
        $update = $builder->update($info);

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // A / R / C (1 huruf)
                'I.S',             // kode module dari menuprg
                'I.S.B.3'          // kode menu untuk PENJUALAN
            );

            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update status']);
        }
    }



    function updateSuratJalan()
    {
        $nama = trim($this->session->get('nama'));
        $docno = hex2bin($this->request->getGet('id'));
        $param = " and coalesce(docno,'')='$docno'";
        $dtl = $this->m_postsales->q_suratjalan_master($param)->getRowArray();
        $status = trim($dtl['status']);

        if ($status === 'F' || $status === 'P') {
            // Update hanya status di tabel sc_trx.standart_usage_mst
            $info = array(
                'status' => 'E',
            );
            $builder = $this->db->table('sc_trx.suratjalan');
            $builder->where('trim(docno)', $docno);
            $builder->update($info);

            // Redirect ke halaman addStdUsage
            return redirect()->to(base_url('sales/postsales/addSuratJalan'));
        } else {
            // Jika status bukan 'F', redirect ke halaman mrpgroup
            return redirect()->to(base_url('sales/postsales/suratjalan'));
        }
    }

    function showing_suratjalantrx(){
        $nama=trim($this->session->get('nama'));
        $docno = trim($this->request->getGet('docno')); // Ambil parameter docno dari Ajax

        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_suratjalan_master($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_suratjalantemp(){
        $docno = trim($this->request->getGet('docno')); // ambil dari GET
        $nama=trim($this->session->get('nama'));
        $param = " and docno='$docno'";
        $data = $this->m_postsales->q_suratjalan_master_temp($param);
        $output = array(
            'status' => true,
            'total_count' => $data->getNumRows(),
            'items' => $data->getResult(),
            'incomplete_getResults' => false,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function showing_suratjalan_dtl($id){
        $nama = trim($this->session->get('nama'));
        $data = $this->m_postsales->q_suratjalan_dtl_temp(" and docno='$nama' and idurut='$id'")->getRow();
        echo json_encode($data);
    }



    public function get_suratjalan_detail()
    {
        $id = $this->request->getGet('id');

        $row = $this->db->table('sc_tmp.suratjalan_dtl')
            ->where('uniqueid', $id)
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

    public function delete_suratjalan_detail()
    {
        $request = service('request');
        $db      = \Config\Database::connect();
        $builder = $db->table('sc_tmp.suratjalan_dtl');
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
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            // ======================================
            // CEK HEADER MASIH DRAFT (belum final)
            // ======================================
            $docno = $rows[0]['docno'];

            $headerRow = $db->table('sc_tmp.suratjalan')
                ->select('status')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if (!$headerRow || strtoupper(trim($headerRow['status'])) === 'F') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah final, tidak dapat diubah'
                ]);
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
            $builder
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->delete();

            $sisa = $db->table('sc_tmp.suratjalan_dtl')
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->countAllResults();

            if ($sisa > 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }


            $db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data DO Detail berhasil dihapus'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function list_tmp_suratjalan_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_suratjalan_dtl_temp_view($docno);
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->uniqueid;
            //item
            // $row[] = $lm->docnosj;
            $row[] = $lm->docnodo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            // $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            // $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 4, '.', ',') . '% </div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
            // $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_suratjalan_dtl_temp_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_suratjalan_dtl_temp_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_trx_suratjalan_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_suratjalan_dtl_view($docno);
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            // $row[] = $lm->docnosj;
            $row[] = $lm->docnodo;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            // $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            // $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
            // $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;

        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_suratjalan_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_suratjalan_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntrySuratJalan(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $builder = $this->db->table('sc_tmp.suratjalan');

        // Ambil header draft milik user (bind via Query Builder, hindari interpolasi SQL)
        $headerRow = $builder
            ->where('inputby', $nama)
            ->orderBy('inputdate', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $status = $headerRow ? trim($headerRow['status']) : '';
        $docno  = $headerRow ? trim($headerRow['docno']) : '';

        // Detail tidak valid (unit kosong / qty 0 / nmbarang kosong)
        $cek = $this->db->table('sc_tmp.suratjalan_dtl')
            ->where('inputby', $nama)
            ->groupStart()
            ->where('unit', '')
            ->orWhere('qty', '0.00')
            ->orWhere('qty', '0')
            ->orWhere('nmbarang', '')
            ->groupEnd()
            ->countAllResults();

        // Total detail
        $cek2 = $this->db->table('sc_tmp.suratjalan_dtl')
            ->where('inputby', $nama)
            ->countAllResults();

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.3');
        $builder_trxerror->delete();


        if (($status==='E' and $cek > 0) or ($cek2 <= 0))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek,
                'nomorakhir2' => $cek2,
                'modul' => 'I.S.B.3',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addSuratJalan'));
        } else {
            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            // $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            // $jthtempo   = trim($this->request->getPost('jthtempo'));
            $kdcustomer   = trim($this->request->getPost('kdcustomer'));
            $alamatcustomer   = trim($this->request->getPost('alamatcustomer'));
            // $gradecustomer   = trim($this->request->getPost('gradecustomer'));
            $kdcustomerdeliv   = trim($this->request->getPost('kdcustomerdeliv'));
            $alamatcustomerdeliv   = trim($this->request->getPost('alamatcustomerdeliv'));
            // $alamatkirim   = trim($this->request->getPost('alamatkirim'));
            // $keterangan   = trim($this->request->getPost('keterangan'));
            // $currcode   = trim($this->request->getPost('currcode'));
            $salesman   = trim($this->request->getPost('kdsalesman'));
            // $kurs   = trim($this->request->getPost('kurs'));
            // $isinclusive   = trim($this->request->getPost('isinclusive'));
            // $idtax   = trim($this->request->getPost('idtax'));
            $keterangan   = trim($this->request->getPost('keterangan'));
            // $carabayar   = trim($this->request->getPost('carabayar'));
            // $pocust   = trim($this->request->getPost('pocust'));
            // $isinclusive = $this->request->getPost('isinclusive') ? 'YES' : 'NO';
            // $isopenprice = $this->request->getPost('isopenprice') ? 'YES' : 'NO';



            // **BERSIHKAN FORMAT KURS**
            // $kurs = trim($this->request->getPost('kurs'));
            // $kurs_clean = 0;
            // if (!empty($kurs)) {
            //     $kurs_clean = str_replace(',', '', $kurs);
            //     // $kurs_clean = str_replace('.', '.', $kurs_clean);
            //     // $kurs_clean = floatval($kurs_clean);
            // }

            // Convert expdate ke format YYYY-MM-DD
            // $docdateph = null;
            // if (!empty($docdate)) {
            //     $docdateph = date('Y-m-d', strtotime($docdate));
            // }

            // $delivdateph = null;
            // if (!empty($delivdate)) {
            //     $delivdateph = date('Y-m-d', strtotime($delivdate));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'docdate'        => $docdateph,
                // 'delivdate'       => $delivdateph,
                // 'jthtempo'       => $jthtempo,
                'kdcustomer'     => strtoupper($kdcustomer),
                'alamatcustomer' => strtoupper($alamatcustomer),
                // 'gradecustomer' => strtoupper($gradecustomer),
                'kdcustomerdeliv'     => strtoupper($kdcustomerdeliv),
                'alamatcustomerdeliv' => strtoupper($alamatcustomerdeliv),
                // 'alamatkirim'    => strtoupper($alamatkirim),
                'keterangan'     => strtoupper($keterangan),
                // 'currcode'       => $currcode,
                'kdsalesman'       => $salesman,
                // 'kurs'           => $kurs_clean,
                // 'isinclusive'    => strtoupper($isinclusive),
                // 'isopenprice'    => strtoupper($isopenprice),
                // 'idtax'          => strtoupper($idtax),
                // 'pocust'         => strtoupper($pocust),
                // 'carabayar'         => strtoupper($carabayar),
                // 'pemohon'       => $pemohon (jika masih diperlukan nanti bisa ditambahkan)
            ];

            try {
                $this->db->transStart();

                // Update header (scoped ke pemilik + docno)
                $this->db->table('sc_tmp.suratjalan')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update($updateHeader);

                // FINALIZE: CAS status E -> F (trigger tmp->trx berjalan di sini)
                $this->db->table('sc_tmp.suratjalan')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update(['status' => 'F']);

                $errDb = $this->sales_errorDb();

                // read-back: trigger sukses akan menghapus baris tmp
                $sisa = $this->db->table('sc_tmp.suratjalan')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->countAllResults();

                if ($errDb !== '' || $sisa > 0) {
                    $this->db->transRollback();

                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 3,
                        'nomorakhir1' => $cek,
                        'nomorakhir2' => $cek2,
                        'modul' => 'I.S.B.3',
                    );
                    $builder_trxerror->insert($infotrxerror);
                    return redirect()->to(base_url('/sales/postsales/addSuratJalan'));
                }

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek,
                    'nomorakhir2' => $cek2,
                    'modul' => 'I.S.B.3',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addSuratJalan'));
            }

            return redirect()->to(base_url('/sales/postsales/suratjalan'));



        }

    }


    function show_suratjalan(){
        $module = 'I.S';
        $menu = 'I.S.B.3';
        $table = "sc_trx.suratjalan";
        $nama = trim($this->session->get('nama'));
        $docno = $this->request->getGet('docno');  // Mengambil 'docno' dari URL
        //$docdate = $this->request->getPost('docdate');
        // $idlocation = $this->request->getPost('idlocation');
        // $idgroup = $this->request->getPost('idgroup');
        // $formheader = $this->request->getPost('formheader');
        $nama = trim($this->session->get('nama'));
        // $docno = hex2bin($this->request->getGet('docno'));
        $docno = hex2bin($docno);
        $builder = $this->db->table('sc_trx.suratjalan');

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

        $title = " Report Surat Jalan";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_suratjalan/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_suratjalan.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
    }

    function api_suratjalan(){
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
        $datamst = $this->m_postsales->q_suratjalan_master($param);
        $datadtl = $this->m_postsales->q_suratjalan_dtl($param);
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



    // =================================== PENJUALAN ===========================================


    public function penjualan()
    {
        $data['title']="Penjualan";
        $dtlbranch=$this->m_global->q_branch()->getRowArray();
        $branch=$dtlbranch['branch'];
        /* CODE UNTUK VERSI*/
        $nama=trim($this->session->get('nama'));
        $kodemenu='I.S.B.4'; $versirelease='I.S.B.4/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.4'";
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
        $periode   = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        $data['periodeTutup'] = false;
        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            $data['periodeTutup'] = true;
            $data['periodeInfo']  = [
                'periode'    => trim($dtlPeriode['periode']),
                'keterangan' => trim($dtlPeriode['keterangan']),
            ];
        }
        /* ===================================================== */


        if ($dtl->getNumRows()>0) {
            $title = "WARNING !!!";
            $urlclear = base_url('sales/postsales/clearEntryPenjualan');
            $urlnext = base_url('sales/postsales/addPenjualan');
            $body = " Entry not finished found....!!!";
            $data['showUnfinish'] = $this->m_trxerror->unfinish($nama, $urlclear, $urlnext, $title, $body);
        } else { $data['showUnfinish'] = '' ; }

        $kmenu = 'I.S.B.4';
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
        $kodemenu='I.S.B.4'; $versirelease='I.S.B.4/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.4'";
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


        $kmenu = 'I.S.B.4';
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
                    onclick="return guardPeriodeTutup(event) && confirm(\'Update This Penjualan : ' . $docno . '\')">
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
                    href="' . base_url('sales/postsales/show_penjualan') . '/?id=' . $docnoHex . '&docno=' . $docnoHex . '" 
                    onclick="return guardPeriodeTutup(event) && confirm(\'Print Penjualan : ' . $docno . '\')">
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
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_front_penjualan_view_count_all(),
            "recordsFiltered" => $this->m_postsales->t_front_penjualan_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_penjualan_apprv(){
        $list = $this->m_postsales->get_t_front_penjualan_apprv_view();
        $data = array();
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;


        $kmenu = 'I.S.B.4';
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
            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';


            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        if(empty($dtl->getRowArray())){
            return redirect()->to(base_url('sales/postsales/penjualan'));
        }
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
        $kodemenu='I.S.B.4'; $versirelease='I.S.B.4/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.B.4'";
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


        $flashError = session()->getFlashdata('penjualan_error');
        if (!empty($flashError)) {
            $data['message'] = "<div class='alert alert-danger'>" . esc($flashError) . "</div>" . ($data['message'] ?? '');
        }

        $param = " and trim(inputby)='$nama'";
        $data['mst'] = $this->m_postsales->q_penjualan_master_temp($param)->getRowArray();
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('sales/postsales/penjualan'));
        }
        // =================================

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

        $konfigurasiUmum = $this->db
            ->table('sc_mst.konfigurasi_umum')
            ->get()
            ->getResultArray(); // Ini mengembalikan array of objects/arrays

        // Karena hanya 1 row, ambil index ke-0
        $prefix = trim($konfigurasiUmum[0]['penjualan']) ?? '';
        $currcode = $konfigurasiUmum[0]['currcode'] ?? '';
        $idtax = $konfigurasiUmum[0]['idtax'] ?? '';

        $logindate = $this->session->get('logindate'); // dd-mm-yyyy
        $infix = date('ym', strtotime($logindate));

        return $this->response->setJSON([
            'success'      => true,
            'kode_suffix'  => $kodeSuffix,
            'infix'        => $infix,
            'logindate'     => $logindate,
            'prefix'        => $prefix,
            'konfigurasi_umum' => $konfigurasiUmum,
            'currcode'      => $currcode,
            'idtax'         => $idtax,
        ]);
    }

    /**
     * Daftar SO untuk menu PENJUALAN.
     *
     * Berbeda dari api/globalmodule/list_so yang memfilter dari potongan suffix
     * docno (2 huruf setelah slash terakhir), method ini memfilter berdasarkan
     * kolom sc_trx.salesorder.cabang (idbranch). Dengan begitu SO yang suffix-nya
     * diubah menjadi angka (mis. SOF/2611/000001) tetap bisa ditarik selama
     * cabangnya sama dengan cabang header Penjualan.
     */
    public function list_so_penjualan()
    {
        $search   = strtoupper(trim((string) $this->request->getPost('_search_')));
        $branchId = trim((string) $this->request->getPost('_branchid_'));
        $cust     = trim((string) $this->request->getPost('_paramglobalcust_'));
        $kodeSO   = trim((string) $this->request->getPost('_paramglobal_'));

        // Escape kutip tunggal (pola query q_so() memakai string parameter).
        $searchEsc = str_replace("'", "''", $search);
        $branchEsc = str_replace("'", "''", $branchId);
        $custEsc   = str_replace("'", "''", $cust);
        $kodeEsc   = str_replace("'", "''", $kodeSO);

        $param = " and (so.docno ilike '%{$searchEsc}%'"
               . " or upper(coalesce(so.keterangan,'')) like '%{$searchEsc}%')";

        if ($branchEsc !== '') {
            // Filter utama: cabang SO harus sama dengan cabang header Penjualan.
            $param .= " and trim(so.cabang) = '{$branchEsc}'";
        } elseif ($kodeEsc !== '') {
            // Fallback kompatibilitas bila cabang belum terpilih.
            $param .= " and substring(trim(so.docno) from '.*/([A-Z]{2})') = '{$kodeEsc}'";
        }

        if ($custEsc !== '') {
            $param .= " and trim(so.kdcustomer) = '{$custEsc}'";
        }

        $param .= " order by so.docno asc";

        $items = $this->m_global->q_so($param)->getResult();
        $count = count($items);

        return $this->response->setJSON([
            'total_count'            => $count,
            'items'                  => $items,
            'incomplete_getResults'  => false,
        ]);
    }

    /**
     * Lookup stok (read-only) untuk detail item Penjualan.
     * Sumber sama dengan validasi final: sc_trx.stkblc.
     */
    public function stock_penjualan_item()
    {
        $idbarang = trim((string) $this->request->getPost('idbarang'));
        $idgudang = trim((string) $this->request->getPost('idgudang'));
        $idspec   = trim((string) $this->request->getPost('idspec'));

        return $this->response->setJSON([
            'total' => $this->sales_stock_saldo($idbarang, $idgudang, $idspec),
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



    // public function savePenjualanDetail()
    // {
    //     $nama   = trim($this->session->get('nama'));
    //     $docno  = strtoupper(trim($this->request->getPost('docno')));
    //     $docnoso = strtoupper(trim($this->request->getPost('docnoso')));
    //     $docnosj = strtoupper(trim($this->request->getPost('docnosj')));

    //     $idurut = $this->request->getPost('idurut'); // HAPUS strtoupper, biarkan apa adanya

    //     // Tambahkan mode untuk membedakan add/edit dengan lebih jelas
    //     // $mode = $this->request->getPost('mode'); // 'add' atau 'edit'

    //     if (!$docno) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'No. Jurnal tidak boleh kosong'
    //         ]);
    //     }

    //     // Harus salah satu: SO atau SJ (XOR)
    //     if (($docnoso && $docnosj) || (!$docnoso && !$docnosj)) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Pilih salah satu: No. SO atau No. SJ (tidak boleh keduanya / kosong keduanya)'
    //         ]);
    //     }

    //     $isFromSJ = !empty($docnosj);



    //     $db = $this->db;
    //     $db->transStart();

    //     $builderSO = $db->table('sc_trx.salesorder');
    //     $soData = $builderSO
    //         ->select('currcode, kurs, idtax, isinclusive')
    //         ->where('docno', $docnoso)
    //         ->get()
    //         ->getRowArray();

    //     // Jika data SO tidak ditemukan, beri response error
    //     if (!$soData) {
    //         $db->transRollback();
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => "Data SO dengan nomor {$docnoso} tidak ditemukan"
    //         ]);
    //     }


    //     // =====================================================
    //     // CEK / INSERT HEADER
    //     // =====================================================
    //     $builderHeader = $db->table('sc_tmp.penjualan');

    //     $exists = $builderHeader
    //         ->where('docno', $docno)
    //         ->where('inputby', $nama)
    //         ->countAllResults();

    //     $reload = false;
    //     // Untuk pengambilan data dari POST

    //     if ($exists == 0) {
    //         $isinclusive = strtoupper(trim(
    //             $this->request->getPost('isinclusive')
    //             ?? $dataprocess->isinclusive
    //             ?? 'NO'
    //         ));

    //         $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

    //         $isopenprice = strtoupper(trim(
    //             $this->request->getPost('isopenprice')
    //             ?? $dataprocess->isopenprice
    //             ?? 'NO'
    //         ));

    //         $isopenprice = ($isopenprice === 'YES') ? 'YES' : 'NO';

    //         $builderHeader->insert([
    //             'docno'     => $docno,
    //             'cabang'     => $this->request->getPost('cabang'),
    //             'docdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
    //             // 'delivdate'   => date('Y-m-d', strtotime(trim($this->request->getPost('delivdate')))),
    //             'jthtempo'     => $this->request->getPost('jthtempo'),
    //             'isinclusive'     => $isinclusive,
    //             'isopenprice'     => $isopenprice,

    //             'kdcustomer'    => strtoupper($this->request->getPost('kdcustomer')),
    //             'alamatcustomer'    => strtoupper($this->request->getPost('alamatcustomer')),
    //             'kdcustomerdeliv'    => strtoupper($this->request->getPost('kdcustomerdeliv')),
    //             'alamatcustomerdeliv'    => strtoupper($this->request->getPost('alamatcustomerdeliv')),
    //             // 'alamatkirim'    => strtoupper($this->request->getPost('alamatkirim')),
    //             'idtax'    => strtoupper($this->request->getPost('idtax')),
    //             'kdsalesman'    => ($this->request->getPost('kdsalesman')),
    //             'gradecustomer'    => ($this->request->getPost('gradecustomer')),
    //             'carabayar'    => strtoupper($this->request->getPost('carabayar')),
    //             // 'pocust'    => strtoupper($this->request->getPost('pocust')),
    //             'currcode'    => strtoupper($this->request->getPost('currcode')),
    //             'kurs'    => ($this->request->getPost('kurs')),
    //             'keterangan'    => strtoupper($this->request->getPost('keterangan')),
    //             // 'pocust'    => strtoupper($this->request->getPost('pocust')),
    //             // 'nodp'    => strtoupper($this->request->getPost('nodp')),
    //             'status'    => 'E',
    //             'inputby'   => $nama,
    //             'inputdate' => date('Y-m-d H:i:s')
    //         ]);

    //         $reload = true;
    //     }

    //     $builderDetail = $db->table('sc_tmp.penjualan_dtl');
    //     $insertCount = 0;
    //     $message = '';

    //     // CEK MODE: ADD atau EDIT
    //     if (!empty($idurut)) {

    //         $uniqueid = $this->request->getPost('uniqueid');
    //         // =====================================================
    //         // MODE EDIT - UPDATE DATA
    //         // =====================================================
    //         $qty         = $this->request->getPost('qty');
    //         // $qtybonus    = $this->request->getPost('qtybonus') ?: 0;
    //         $harga       = $this->request->getPost('harga') ?: 0;
    //         $multidisc   = $this->request->getPost('multidisc') ?: 0;
    //         // $volitem   = $this->request->getPost('volitem') ?: 0;
    //         // $biaya   = $this->request->getPost('biaya') ?: 0;
    //         // $biaya2   = $this->request->getPost('biaya2') ?: 0;
    //         $nilai       = $this->request->getPost('nilai') ?: 0;
    //         $description = strtoupper($this->request->getPost('description'));
    //         $idprincipal = strtoupper($this->request->getPost('idprincipal'));
    //         $idgudang = strtoupper($this->request->getPost('idgudang'));
    //         $idspec = strtoupper($this->request->getPost('idspec'));

    //         $nilai = $qty * $harga;
    //         $h = $db->table('sc_tmp.penjualan')
    //             ->select('kurs,idtax')
    //             ->where('docno', $docno)
    //             ->get()
    //             ->getRowArray();

    //         $kurs = $h['kurs'] ?? 1;
    //         $idtax = $h['idtax'] ?? 'NON';
    //         $nilaikonversi = $nilai * $kurs;
    //         $nilaipajak = 0;

    //         if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
    //             $taxDetails = $db->table('sc_mst.tax_dtl')
    //                 ->select('percentation')
    //                 ->where('idtax', $idtax)
    //                 ->get()
    //                 ->getResultArray();

    //             $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
    //             // $nilaipajak = $nilai + ($nilai * $totalPersen / 100);
    //             $nilaipajak = $nilai * $totalPersen / 100;
    //         }

    //         $builderDetail->where('uniqueid', $uniqueid)->update([
    //             'qty'          => $qty,
    //             // 'qtybonus'     => $qtybonus,
    //             'harga'        => $harga,
    //             'multidisc'    => $multidisc,
    //             'nilai'        => $nilai,
    //             'nilaikonversi' => $nilaikonversi,
    //             'nilaipajak' => $nilaipajak,
    //             'idtax' => $idtax,
    //             'kurs' => $kurs,
    //             'currcode' => $poData['currcode'] ?? '',
    //             // 'volitem'      => $volitem,

    //             // 'biaya'      => $biaya,
    //             // 'biaya2'      => $biaya2,
    //             'idprincipal'      => $idprincipal,
    //             'idgudang'      => $idgudang,
    //             'idspec'      => $idspec,

    //             'description' => $description,
    //             'updateby'     => $nama,
    //             'updatedate'   => date('Y-m-d H:i:s')
    //         ]);





    //         $message = 'Data berhasil diupdate';

    //     } else {
    //         // =====================================================
    //         // MODE ADD - INSERT DATA DARI PP
    //         // =====================================================
    //         $soDetails = $db->query("
    //             SELECT
    //                 docno,
    //                 idbarang,
    //                 uniqueid,
    //                 nmbarang,
    //                 unit,
    //                 qty,
    //                 idprincipal,
    //                 idgudang,
    //                 idspec,
    //                 nilaipajak,
    //                 nilaikonversi,
    //                 multidisc,
    //                 qtypenjualan,
    //                 harga,
    //                 currcode,
    //                 idtax,
    //                 kurs,
    //                 nilai
    //             FROM sc_trx.salesorder_dtl
    //             WHERE TRIM(docno) = ?
    //         ", [$docnoso])->getResult();

    //         if (empty($soDetails)) {
    //             $db->transRollback();
    //             return $this->response->setJSON([
    //                 'success' => false,
    //                 'message' => 'Data SO tidak ditemukan'
    //             ]);
    //         }

    //         foreach ($soDetails as $row) {
    //             $sisaQty = $row->qty - ($row->qtypenjualan);
    //             if ($sisaQty <= 0) continue;

    //             $duplicate = $builderDetail
    //                 ->where('docno', $docno)
    //                 ->where('uniqueid', $row->uniqueid)
    //                 ->countAllResults();

    //             $nilaiRow = $row->qty * $row->harga;
    //             $h = $db->table('sc_tmp.penjualan')
    //                 ->select('kurs,idtax')
    //                 ->where('docno', $docno)
    //                 ->get()
    //                 ->getRowArray();

    //             $kurs = $h['kurs'] ?? 1;
    //             $idtaxRow = $h['idtaxRow'] ?? 'NON';
    //             $nilaikonversiRow = $nilaiRow * $kurs;
    //             $nilaipajakRow = 0;

    //             if (!empty($idtaxRow) && trim($idtaxRow) !== 'NON' && $nilaiRow > 0) {
    //                 $taxDetails = $db->table('sc_mst.tax_dtl')
    //                     ->select('percentation')
    //                     ->where('idtax', $idtaxRow)
    //                     ->get()
    //                     ->getResultArray();

    //                 $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
    //                 // $nilaipajakRow = $nilai + ($nilai * $totalPersen / 100);
    //                 $nilaipajakRow = $nilaiRow * $totalPersen / 100;
    //             }

    //             if ($duplicate == 0) {
    //                 $builderDetail->insert([
    //                     'docno'         => $docno,
    //                     'docnoso'       => $docnoso,
    //                     'idbarang'      => $row->idbarang,
    //                     'uniqueid'      => $row->uniqueid,
    //                     'nmbarang'      => $row->nmbarang,
    //                     'unit'          => $row->unit,
    //                     'qty'           => $sisaQty,
    //                     'idprincipal'   => $row->idprincipal,
    //                     'idgudang'      => $row->idgudang,
    //                     'idspec'        => $row->idspec,
    //                     'harga'         => $row->harga, // Default 0 untuk new insert
    //                     'multidisc'     =>  $row->multidisc, // Default 0 untuk new insert
    //                     'nilaipajak'    => $nilaipajakRow,
    //                     'nilaikonversi' => $nilaikonversiRow,
    //                     'currcode'      => $row->currcode,
    //                     'kurs'          => $row->kurs,
    //                     'idtax'         => $row->idtax,
    //                     'nilai'         => $sisaQty * $row->harga, // Default 0 untuk new insert
    //                     // 'description' => $row->description,
    //                     'inputby'       => $nama,
    //                     'inputdate'     => date('Y-m-d H:i:s')
    //                 ]);

    //                 $insertCount++;
    //             }
    //         }

    //         $message = $insertCount > 0
    //                     ? "$insertCount item berhasil ditambahkan"
    //                     : "Semua item sudah ada sebelumnya";
    //     }

    //     $penjualanHeader = $builderHeader->select('idtax')->where('docno', $docno)->get()->getRowArray();
    //     $idtax = $penjualanHeader['idtax'] ?? '';

    //     // Hitung total DPP (sum nilai dari po_dtl)
    //     $builderTotalDpp = $db->table('sc_tmp.penjualan_dtl');
    //     $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
    //         ->where('docno', $docno)
    //         ->get()
    //         ->getRowArray();

    //     $dpp = $totalDpp['total_dpp'] ?? 0;

    //     // Hitung jumlah pajak berdasarkan idtax
    //     $jumlahPajak = 0;

    //     if (!empty($idtax) && trim($idtax) !== 'NON'  && $dpp > 0) {
    //         // Ambil detail tax dari sc_mst.tax_dtl
    //         $builderTaxDtl = $db->table('sc_mst.tax_dtl');
    //         $taxDetails = $builderTaxDtl->select('percentation')
    //             ->where('idtax', $idtax)
    //             ->get()
    //             ->getResultArray();

    //         foreach ($taxDetails as $tax) {
    //             $persentase = $tax['percentation'] ?? 0;
    //             $jumlahPajak += $dpp * ($persentase / 100);
    //         }
    //     }

    //     // Hitung total (DPP + Jumlah Pajak)
    //     $total = $dpp + $jumlahPajak;

    //     // Update header LPB
    //     $builderHeader->where('docno', $docno)->update([
    //         'dpp' => number_format($dpp, 4, '.', ''),
    //         'jumlahpajak' => number_format($jumlahPajak, 4, '.', ''),
    //         'total' => number_format($total, 4, '.', ''),
    //         'updateby' => $nama,
    //         'updatedate' => date('Y-m-d H:i:s')
    //     ]);

    //     $db->transComplete();

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'reload'  => $reload,
    //         'message' => $message
    //     ]);
    // }

    /* =====================================================================
     * TARIK DO -> PENJUALAN (khusus halaman sales/postsales/addPenjualan)
     * ---------------------------------------------------------------------
     * pjListDO       : daftar DO yang sudah POSTED (status P) dan masih memiliki sisa qty
     * pjListDODetail : detail DO (parent-child pada modal Tarik DO)
     * pjPullDO       : tarik detail DO terpilih ke sc_tmp.penjualan_dtl
     * ===================================================================== */

    public function pjListDO()
    {
        $kdcustomer = strtoupper(trim((string) $this->request->getPost('kdcustomer')));
        $search     = trim((string) $this->request->getPost('search'));

        $sql = "SELECT a.docno,
                       a.docdate,
                       a.kdcustomer,
                       a.nmcustomer,
                       COALESCE(a.total, 0) AS total,
                       a.status,
                       COALESCE(a.currcode, '') AS currcode,
                       COALESCE(a.kurs, 0) AS kurs
                  FROM sc_trx.deliveryorder a
                 WHERE COALESCE(TRIM(a.docno), '') <> ''
                   AND UPPER(TRIM(a.status)) = 'P'
                   AND (? = '' OR TRIM(a.kdcustomer) = ?)
                   AND (? = '' OR a.docno ILIKE ? OR a.nmcustomer ILIKE ?)
                   AND EXISTS (
                        SELECT 1
                          FROM sc_trx.deliveryorder_dtl d
                         WHERE TRIM(d.docno) = TRIM(a.docno)
                           AND (COALESCE(d.qty, 0) - COALESCE(d.qtysj, 0)) > 0
                   )
                 ORDER BY a.docdate DESC, a.docno DESC
                 LIMIT 50";

        $rows = $this->db->query($sql, [
            $kdcustomer, $kdcustomer,
            $search, '%' . $search . '%', '%' . $search . '%',
        ])->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'rows'    => $rows,
        ]);
    }

    public function pjListDODetail()
    {
        $nama    = trim($this->session->get('nama'));
        $docnodo = strtoupper(trim((string) $this->request->getPost('docnodo')));
        $docno   = strtoupper(trim((string) $this->request->getPost('docno')));

        if ($docnodo === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor DO tidak valid',
                'rows'    => [],
            ]);
        }

        $rows = $this->db->query("
            SELECT idurut,
                   TRIM(uniqueid) AS uniqueid,
                   TRIM(COALESCE(uniqueidso, '')) AS uniqueidso,
                   idbarang,
                   nmbarang,
                   COALESCE(idspec, '') AS idspec,
                   COALESCE(unit, '') AS unit,
                   COALESCE(qty, 0) AS qty,
                   COALESCE(qtysj, 0) AS qtysj,
                   COALESCE(harga, 0) AS harga,
                   COALESCE(multidisc, 0) AS multidisc,
                   COALESCE(idprincipal, '') AS idprincipal,
                   COALESCE(idgudang, '') AS idgudang,
                   COALESCE(description, '') AS description
              FROM sc_trx.deliveryorder_dtl
             WHERE TRIM(docno) = ?
             ORDER BY idurut ASC
        ", [$docnodo])->getResultArray();

        // Qty yang sudah masuk draft penjualan user ini (per baris DO)
        $pulled  = [];
        $tmpRows = $this->db->query("
            SELECT TRIM(COALESCE(uniqueiddo, '')) AS uid,
                   COALESCE(SUM(qty), 0) AS qty
              FROM sc_tmp.penjualan_dtl
             WHERE inputby = ?
               AND COALESCE(TRIM(uniqueiddo), '') <> ''
               AND (? = '' OR TRIM(docno) = ?)
             GROUP BY 1
        ", [$nama, $docno, $docno])->getResultArray();

        foreach ($tmpRows as $tmp) {
            $uid = trim((string) ($tmp['uid'] ?? ''));
            if ($uid !== '') {
                $pulled[$uid] = (float) ($tmp['qty'] ?? 0);
            }
        }

        // Qty yang sudah masuk dokumen penjualan FINAL untuk DO ini
        $trxRows = $this->db->query("
            SELECT TRIM(COALESCE(uniqueiddo, '')) AS uid,
                   COALESCE(SUM(qty), 0) AS qty
              FROM sc_trx.penjualan_dtl
             WHERE COALESCE(TRIM(uniqueiddo), '') <> ''
               AND TRIM(COALESCE(docnodo, '')) = ?
             GROUP BY 1
        ", [$docnodo])->getResultArray();

        foreach ($trxRows as $trx) {
            $uid = trim((string) ($trx['uid'] ?? ''));
            if ($uid !== '') {
                $pulled[$uid] = (float) ($pulled[$uid] ?? 0) + (float) ($trx['qty'] ?? 0);
            }
        }

        foreach ($rows as &$row) {
            $uid     = trim((string) ($row['uniqueid'] ?? ''));
            $dipakai = isset($pulled[$uid]) ? (float) $pulled[$uid] : 0;

            $row['qtydo']   = (float) ($row['qty'] ?? 0);
            $row['qtysisa'] = max(0, $row['qtydo'] - (float) ($row['qtysj'] ?? 0) - $dipakai);
            $row['sudah']   = ($dipakai > 0) ? 1 : 0;
        }
        unset($row);

        return $this->response->setJSON([
            'success' => true,
            'rows'    => $rows,
        ]);
    }


    public function pjPullDO()
    {
        $nama    = trim($this->session->get('nama'));
        $docno   = strtoupper(trim((string) $this->request->getPost('docno')));
        $docnodo = strtoupper(trim((string) $this->request->getPost('docnodo')));

        // Detail DO terpilih (uniqueid baris DO)
        $ids = $this->request->getPost('ids');
        if (is_string($ids)) {
            $ids     = trim($ids);
            $decoded = ($ids !== '' && $ids[0] === '[') ? json_decode($ids, true) : null;
            $ids     = is_array($decoded) ? $decoded : ($ids === '' ? [] : explode(',', $ids));
        }
        $ids = is_array($ids) ? $ids : [];

        $picks = [];
        foreach ($ids as $v) {
            $v = trim((string) $v);
            if ($v !== '') {
                $picks[$v] = $v;
            }
        }

        if ($docno === '' || $docnodo === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor jurnal / DO tidak valid',
            ]);
        }

        if (empty($picks)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Centang minimal satu detail DO yang akan ditarik',
            ]);
        }

        $db  = $this->db;
        $doH = $db->table('sc_trx.deliveryorder')->where('docno', $docnodo)->get()->getRowArray();

        if (!$doH) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Data DO dengan nomor {$docnodo} tidak ditemukan",
            ]);
        }

        /* Hanya DO yang sudah POSTED (status P) yang boleh ditarik ke Penjualan.
           Modal Tarik DO juga hanya menampilkan DO berstatus P (pjListDO). */
        if (strtoupper(trim((string) ($doH['status'] ?? ''))) !== 'P') {
            return $this->response->setJSON([
                'success' => false,
                'message' => "DO {$docnodo} belum berstatus POSTED (P), tidak dapat ditarik",
            ]);
        }

        $db->transStart();

        // =====================================================
        // HEADER (pola sama dengan savePenjualanDetail)
        // =====================================================
        $builderHeader = $db->table('sc_tmp.penjualan');

        $exists = $builderHeader
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->countAllResults();

        $reload = false;

        /* Referensi SO / DO asal tarikan (ikut header DO) */
        $docnosoDo = strtoupper(trim((string) ($doH['docnoso'] ?? '')));

        if ($exists == 0) {
            $isinclusive = strtoupper(trim($this->request->getPost('isinclusive') ?? 'NO'));
            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $isopenprice = strtoupper(trim($this->request->getPost('isopenprice') ?? 'NO'));
            $isopenprice = ($isopenprice === 'YES') ? 'YES' : 'NO';

            $docdatePost = trim((string) $this->request->getPost('docdate'));
            $docdate     = ($docdatePost !== '') ? date('Y-m-d', strtotime($docdatePost)) : date('Y-m-d');

            $kdcustomer = strtoupper(trim((string) $this->request->getPost('kdcustomer')));
            if ($kdcustomer === '') {
                $kdcustomer = strtoupper(trim((string) ($doH['kdcustomer'] ?? '')));
            }

            $alamatcustomer = trim((string) $this->request->getPost('alamatcustomer'));
            if ($alamatcustomer === '') {
                $alamatcustomer = trim((string) ($doH['alamatcustomer'] ?? ''));
            }

            $kdcustomerdeliv = strtoupper(trim((string) $this->request->getPost('kdcustomerdeliv')));
            if ($kdcustomerdeliv === '') {
                $kdcustomerdeliv = strtoupper(trim((string) ($doH['kdcustomerdeliv'] ?? $doH['kdcustomer'] ?? '')));
            }

            $alamatcustomerdeliv = trim((string) $this->request->getPost('alamatcustomerdeliv'));
            if ($alamatcustomerdeliv === '') {
                $alamatcustomerdeliv = trim((string) ($doH['alamatcustomerdeliv'] ?? $doH['alamatcustomer'] ?? ''));
            }

            $idtax = strtoupper(trim((string) $this->request->getPost('idtax')));
            if ($idtax === '') {
                $idtax = strtoupper(trim((string) ($doH['idtax'] ?? '')));
            }

            $currcode = strtoupper(trim((string) $this->request->getPost('currcode')));
            if ($currcode === '') {
                $currcode = strtoupper(trim((string) ($doH['currcode'] ?? '')));
            }

            $kurs = $this->request->getPost('kurs');
            if (trim((string) $kurs) === '' || (float) $kurs <= 0) {
                $kurs = $doH['kurs'] ?? 1;
            }

            /* =================================================
             * INFO & KETERANGAN HEADER DO IKUT DIMASUKKAN
             * Nilai dari form dipakai bila ada, selain itu
             * ambil dari header sc_trx.deliveryorder.
             * ================================================= */
            $pemohon = trim((string) $this->request->getPost('pemohon'));
            if ($pemohon === '') {
                $pemohon = trim((string) ($doH['pemohon'] ?? ''));
            }

            $nmcustomer = trim((string) $this->request->getPost('nmcustomer'));
            if ($nmcustomer === '') {
                $nmcustomer = trim((string) ($doH['nmcustomer'] ?? ''));
            }

            $nmcustomerdeliv = trim((string) $this->request->getPost('nmcustomerdeliv'));
            if ($nmcustomerdeliv === '') {
                $nmcustomerdeliv = trim((string) ($doH['nmcustomerdeliv'] ?? ''));
                if ($nmcustomerdeliv === '') {
                    $nmcustomerdeliv = $nmcustomer;
                }
            }

            $jthtempo = trim((string) $this->request->getPost('jthtempo'));
            if ($jthtempo === '') {
                $jthtempo = trim((string) ($doH['jthtempo'] ?? ''));
            }

            $kdsalesman = strtoupper(trim((string) $this->request->getPost('kdsalesman')));
            if ($kdsalesman === '') {
                $kdsalesman = strtoupper(trim((string) ($doH['kdsalesman'] ?? '')));
            }

            $gradecustomer = strtoupper(trim((string) $this->request->getPost('gradecustomer')));
            if ($gradecustomer === '') {
                $gradecustomer = strtoupper(trim((string) ($doH['gradecustomer'] ?? '')));
            }

            $carabayar = strtoupper(trim((string) $this->request->getPost('carabayar')));
            if ($carabayar === '') {
                $carabayar = strtoupper(trim((string) ($doH['carabayar'] ?? '')));
            }

            $keterangan = strtoupper(trim((string) $this->request->getPost('keterangan')));
            if ($keterangan === '') {
                $keterangan = strtoupper(trim((string) ($doH['keterangan'] ?? '')));
            }

            $builderHeader->insert([
                'docno'               => $docno,
                'docnoso'             => ($docnosoDo !== '') ? $docnosoDo : null,
                'docnodo'             => $docnodo,
                'cabang'              => $this->request->getPost('cabang'),
                'docdate'             => $docdate,
                'pemohon'             => $pemohon,
                'jthtempo'            => ($jthtempo !== '') ? $jthtempo : null,
                'isinclusive'         => $isinclusive,
                'isopenprice'         => $isopenprice,
                'kdcustomer'          => $kdcustomer,
                'nmcustomer'          => $nmcustomer,
                'alamatcustomer'      => strtoupper($alamatcustomer),
                'kdcustomerdeliv'     => $kdcustomerdeliv,
                'nmcustomerdeliv'     => $nmcustomerdeliv,
                'alamatcustomerdeliv' => strtoupper($alamatcustomerdeliv),
                'idtax'               => $idtax,
                'kdsalesman'          => $kdsalesman,
                'gradecustomer'       => $gradecustomer,
                'carabayar'           => $carabayar,
                'currcode'            => $currcode,
                'kurs'                => $kurs,
                'keterangan'          => $keterangan,
                'status'              => 'E',
                'inputby'             => $nama,
                'inputdate'           => date('Y-m-d H:i:s'),
            ]);

            $reload = true;
        } else {
            /* Header sudah ada (mis. baris diinput lebih dulu): lengkapi
               informasi & keterangan yang masih kosong dari header DO. */
            $headNow = $builderHeader
                ->select('pemohon, nmcustomer, nmcustomerdeliv, keterangan, carabayar, kdsalesman, gradecustomer, jthtempo, docnoso, docnodo')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if ($headNow) {
                $doInfo = [
                    'pemohon'         => (string) ($doH['pemohon'] ?? ''),
                    'nmcustomer'      => (string) ($doH['nmcustomer'] ?? ''),
                    'nmcustomerdeliv' => ((string) ($doH['nmcustomerdeliv'] ?? '') !== '')
                        ? (string) $doH['nmcustomerdeliv']
                        : (string) ($doH['nmcustomer'] ?? ''),
                    'keterangan'      => (string) ($doH['keterangan'] ?? ''),
                    'carabayar'       => (string) ($doH['carabayar'] ?? ''),
                    'kdsalesman'      => (string) ($doH['kdsalesman'] ?? ''),
                    'gradecustomer'   => (string) ($doH['gradecustomer'] ?? ''),
                    'jthtempo'        => (string) ($doH['jthtempo'] ?? ''),
                    'docnoso'         => $docnosoDo,
                    'docnodo'         => $docnodo,
                ];

                $fill = [];
                foreach ($doInfo as $col => $val) {
                    if (trim((string) ($headNow[$col] ?? '')) === '' && trim($val) !== '') {
                        $fill[$col] = $val;
                    }
                }

                if (!empty($fill)) {
                    $fill['updateby']   = $nama;
                    $fill['updatedate'] = date('Y-m-d H:i:s');

                    $db->table('sc_tmp.penjualan')
                        ->where('docno', $docno)
                        ->where('inputby', $nama)
                        ->update($fill);
                }
            }
        }

        // =====================================================
        // DETAIL DO -> sc_tmp.penjualan_dtl
        // =====================================================
        $builderDetail = $db->table('sc_tmp.penjualan_dtl');

        $ph     = implode(',', array_fill(0, count($picks), '?'));
        $params = array_merge([$docnodo], array_values($picks));

        $rows = $db->query("
            SELECT idurut,
                   TRIM(uniqueid) AS uniqueid,
                   TRIM(COALESCE(uniqueidso, '')) AS uniqueidso,
                   idbarang,
                   nmbarang,
                   COALESCE(idspec, '') AS idspec,
                   COALESCE(unit, '') AS unit,
                   COALESCE(qty, 0) AS qty,
                   COALESCE(qtysj, 0) AS qtysj,
                   COALESCE(harga, 0) AS harga,
                   COALESCE(multidisc, 0) AS multidisc,
                   COALESCE(idprincipal, '') AS idprincipal,
                   COALESCE(idgudang, '') AS idgudang,
                   COALESCE(description, '') AS description
              FROM sc_trx.deliveryorder_dtl
             WHERE TRIM(docno) = ?
               AND TRIM(uniqueid) IN ({$ph})
             ORDER BY idurut ASC
        ", $params)->getResultArray();

        if (empty($rows)) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Detail DO yang dipilih tidak ditemukan',
            ]);
        }

        /* =====================================================
           ETD / ETA baris DO diambil dari SO asal
           (sc_trx.salesorder_dtl), dicocokkan dengan docno SO pada
           header DO + uniqueidso baris DO.
           ===================================================== */
        $soEtdMap = [];

        $soUidList = [];
        foreach ($rows as $row) {
            $uidSo = trim((string) ($row['uniqueidso'] ?? ''));
            if ($uidSo !== '') {
                $soUidList[$uidSo] = $uidSo;
            }
        }
        $soUidList = array_values($soUidList);

        if (!empty($soUidList)) {
            $phSo = implode(',', array_fill(0, count($soUidList), '?'));

            // Kunci utama: docno SO (header DO) + uniqueid baris SO
            if ($docnosoDo !== '') {
                $soRows = $db->query("
                    SELECT TRIM(uniqueid) AS uid, etd, eta
                      FROM sc_trx.salesorder_dtl
                     WHERE TRIM(docno) = ?
                       AND TRIM(uniqueid) IN ({$phSo})
                ", array_merge([$docnosoDo], $soUidList))->getResultArray();

                foreach ($soRows as $soRow) {
                    $soEtdMap[trim((string) $soRow['uid'])] = [
                        'etd' => $soRow['etd'] ?? null,
                        'eta' => $soRow['eta'] ?? null,
                    ];
                }
            }

            // Cadangan: cocokkan uniqueid saja (bila docnoso header kosong/beda)
            $soMissing = array_values(array_diff($soUidList, array_keys($soEtdMap)));
            if (!empty($soMissing)) {
                $phMiss = implode(',', array_fill(0, count($soMissing), '?'));

                $soRows = $db->query("
                    SELECT TRIM(uniqueid) AS uid, etd, eta
                      FROM sc_trx.salesorder_dtl
                     WHERE TRIM(uniqueid) IN ({$phMiss})
                ", $soMissing)->getResultArray();

                foreach ($soRows as $soRow) {
                    $soEtdMap[trim((string) $soRow['uid'])] = [
                        'etd' => $soRow['etd'] ?? null,
                        'eta' => $soRow['eta'] ?? null,
                    ];
                }
            }
        }

        $head = $db->table('sc_tmp.penjualan')
            ->select('kurs, idtax, currcode')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        $kursHeader = (float) ($head['kurs'] ?? 1);
        if ($kursHeader <= 0) {
            $kursHeader = 1;
        }

        $idtaxHeader = trim((string) ($head['idtax'] ?? 'NON'));
        $currHeader  = trim((string) ($head['currcode'] ?? ''));

        $taxPercent = 0;
        if ($idtaxHeader !== '' && strtoupper($idtaxHeader) !== 'NON') {
            $taxRows = $db->table('sc_mst.tax_dtl')
                ->select('percentation')
                ->where('idtax', $idtaxHeader)
                ->get()
                ->getResultArray();

            $taxPercent = array_sum(array_column($taxRows, 'percentation'));
        }

        $inserted  = 0;

        foreach ($rows as $row) {
            $uid = trim((string) ($row['uniqueid'] ?? ''));
            if ($uid === '') {
                continue;
            }

            $uidSo  = trim((string) ($row['uniqueidso'] ?? ''));
            $etdRow = $soEtdMap[$uidSo]['etd'] ?? null;
            $etaRow = $soEtdMap[$uidSo]['eta'] ?? null;

            // Sudah ada di dokumen ini?
            $dup = $db->query("
                SELECT COUNT(*) AS c
                  FROM sc_tmp.penjualan_dtl
                 WHERE docno = ?
                   AND inputby = ?
                   AND TRIM(COALESCE(uniqueiddo, '')) = ?
            ", [$docno, $nama, $uid])->getRowArray();

            if ((int) ($dup['c'] ?? 0) > 0) {
                /* Baris sudah ditarik sebelumnya: lengkapi ETD/ETA yang kosong
                   dari SO asal agar tidak tertinggal null. */
                if ($etdRow !== null || $etaRow !== null) {
                    $db->query("
                        UPDATE sc_tmp.penjualan_dtl
                           SET etd = COALESCE(etd, ?),
                               eta = COALESCE(eta, ?)
                         WHERE docno = ?
                           AND inputby = ?
                           AND TRIM(COALESCE(uniqueiddo, '')) = ?
                    ", [$etdRow, $etaRow, $docno, $nama, $uid]);
                }

                continue;
            }

            // Qty yang sudah dipakai draft lain milik user ini
            $lain = $db->query("
                SELECT COALESCE(SUM(qty), 0) AS qty
                  FROM sc_tmp.penjualan_dtl
                 WHERE inputby = ?
                   AND TRIM(COALESCE(uniqueiddo, '')) = ?
                   AND docno <> ?
            ", [$nama, $uid, $docno])->getRowArray();

            $sisa = (float) ($row['qty'] ?? 0)
                  - (float) ($row['qtysj'] ?? 0)
                  - (float) ($lain['qty'] ?? 0);

            // Kurangi qty yang sudah masuk dokumen penjualan FINAL untuk DO ini
            $trxFinal = $db->query("
                SELECT COALESCE(SUM(qty), 0) AS qty
                  FROM sc_trx.penjualan_dtl
                 WHERE TRIM(COALESCE(uniqueiddo, '')) = ?
                   AND TRIM(COALESCE(docnodo, '')) = ?
            ", [$uid, $docnodo])->getRowArray();

            $sisa -= (float) ($trxFinal['qty'] ?? 0);

            if ($sisa <= 0) {
                continue;
            }

            $harga = (float) ($row['harga'] ?? 0);
            $disc  = (float) ($row['multidisc'] ?? 0);

            $idspec = trim((string) ($row['idspec'] ?? ''));
            if (strtoupper($idspec) === 'NULL') {
                $idspec = '';
            }

            $nilai         = $sisa * $harga;
            $nilaikonversi = $nilai * $kursHeader;
            $nilaipajak    = ($taxPercent > 0) ? ($nilai * $taxPercent / 100) : 0;

            $builderDetail->insert([
                'docno'          => $docno,
                'docnoso'        => $docnosoDo,
                'docnodo'        => $docnodo,
                'idurutdo'       => (int) ($row['idurut'] ?? 0),
                'uniqueiddo'     => $uid,
                'uniqueidso'     => trim((string) ($row['uniqueidso'] ?? '')),
                'uniqueid'       => (trim((string) ($row['uniqueidso'] ?? '')) !== '')
                                        ? trim((string) $row['uniqueidso'])
                                        : $uid,
                'idbarang'       => strtoupper(trim((string) ($row['idbarang'] ?? ''))),
                'nmbarang'       => strtoupper(trim((string) ($row['nmbarang'] ?? ''))),
                'idprincipal'    => strtoupper(trim((string) ($row['idprincipal'] ?? ''))),
                'idgudang'       => strtoupper(trim((string) ($row['idgudang'] ?? ''))),
                'idspec'         => $idspec,
                'unit'           => strtoupper(trim((string) ($row['unit'] ?? ''))),
                'qtyref'         => (float) ($row['qty'] ?? 0),
                'hargasatuanref' => $harga,
                'qty'            => $sisa,
                'harga'          => $harga,
                'hargasatuan'    => $harga,
                'multidisc'      => $disc,
                'nilai'          => $nilai,
                'idtax'          => $idtaxHeader,
                'currcode'       => $currHeader,
                'kurs'           => $kursHeader,
                'nilaikonversi'  => $nilaikonversi,
                'nilaipajak'     => $nilaipajak,
                'description'    => strtoupper(trim((string) ($row['description'] ?? ''))),
                'etd'            => $etdRow,
                'eta'            => $etaRow,
                'inputby'        => $nama,
                'inputdate'      => date('Y-m-d H:i:s'),
            ]);

            $inserted++;
        }

        // =====================================================
        // HITUNG TOTAL HEADER
        // =====================================================
        $penjualanHeader = $builderHeader->select('idtax')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()->getRowArray();

        $idtaxHead = trim((string) ($penjualanHeader['idtax'] ?? ''));

        $totalDpp = $db->table('sc_tmp.penjualan_dtl')
            ->select('COALESCE(SUM(nilai), 0) AS total_dpp')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        $dpp         = (float) ($totalDpp['total_dpp'] ?? 0);
        $jumlahPajak = 0;

        if ($idtaxHead !== '' && strtoupper($idtaxHead) !== 'NON' && $dpp > 0) {
            $taxRows = $db->table('sc_mst.tax_dtl')
                ->select('percentation')
                ->where('idtax', $idtaxHead)
                ->get()
                ->getResultArray();

            foreach ($taxRows as $tax) {
                $jumlahPajak += $dpp * (((float) ($tax['percentation'] ?? 0)) / 100);
            }
        }

        $total = $dpp + $jumlahPajak;

        $builderHeader->where('inputby', $nama)
            ->where('docno', $docno)
            ->update([
                'dpp'         => number_format($dpp, 4, '.', ''),
                'jumlahpajak' => number_format($jumlahPajak, 4, '.', ''),
                'total'       => number_format($total, 4, '.', ''),
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s'),
            ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menarik DO, transaksi dibatalkan',
            ]);
        }

        return $this->response->setJSON([
            'success'  => true,
            'reload'   => $reload,
            'inserted' => $inserted,
            'message'  => $inserted > 0
                ? $inserted . ' detail DO berhasil ditarik'
                : 'Tidak ada detail baru (sudah ada di dokumen ini)',
        ]);
    }

    public function savePenjualanDetail()
    {
        $nama    = trim($this->session->get('nama'));
        $docno   = strtoupper(trim($this->request->getPost('docno')));
        $docnoso = strtoupper(trim($this->request->getPost('docnoso')));
        $docnosj = strtoupper(trim($this->request->getPost('docnosj')));
        $idurut  = $this->request->getPost('idurut');

        if (!$docno) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No. Jurnal tidak boleh kosong'
            ]);
        }

        // >>> XOR SO / SJ
        if (($docnoso && $docnosj) || (!$docnoso && !$docnosj)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih salah satu: No. SO atau No. SJ (tidak boleh keduanya / kosong keduanya)'
            ]);
        }

        $isFromSJ = !empty($docnosj);

        $db = $this->db;
        $db->transStart();

        // =====================================================
        // VALIDASI SUMBER (SO atau SJ)
        // =====================================================
        if ($isFromSJ) {
            $sjCek = $db->table('sc_trx.suratjalan')
                ->select('docno')
                ->where('docno', $docnosj)
                ->get()
                ->getRowArray();

            if (!$sjCek) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Data SJ dengan nomor {$docnosj} tidak ditemukan"
                ]);
            }

            // Ambil SO dari trace SJ -> DO -> SO
            $soDariSj = $db->query("
                SELECT DISTINCT d.docnoso
                FROM sc_trx.suratjalan_dtl sj
                JOIN sc_trx.deliveryorder_dtl d ON d.docno = sj.docnodo
                WHERE TRIM(sj.docno) = ?
                LIMIT 1
            ", [$docnosj])->getRowArray();

            $docnoso = $soDariSj['docnoso'] ?? '';
        }

        // Ambil header SO (untuk SO maupun SJ, ujungnya tetap SO)
        $soData = $db->table('sc_trx.salesorder')
            ->select('currcode, kurs, idtax, isinclusive')
            ->where('docno', $docnoso)
            ->get()
            ->getRowArray();

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

        if ($exists == 0) {
            $isinclusive = strtoupper(trim($this->request->getPost('isinclusive') ?? 'NO'));
            $isinclusive = ($isinclusive === 'YES') ? 'YES' : 'NO';

            $isopenprice = strtoupper(trim($this->request->getPost('isopenprice') ?? 'NO'));
            $isopenprice = ($isopenprice === 'YES') ? 'YES' : 'NO';

            $builderHeader->insert([
                'docno'                 => $docno,
                'cabang'                => $this->request->getPost('cabang'),
                'docdate'               => date('Y-m-d', strtotime(trim($this->request->getPost('docdate')))),
                'jthtempo'              => $this->request->getPost('jthtempo'),
                'isinclusive'           => $isinclusive,
                'isopenprice'           => $isopenprice,
                'kdcustomer'            => strtoupper($this->request->getPost('kdcustomer')),
                'alamatcustomer'        => strtoupper($this->request->getPost('alamatcustomer')),
                'kdcustomerdeliv'       => strtoupper($this->request->getPost('kdcustomerdeliv')),
                'alamatcustomerdeliv'   => strtoupper($this->request->getPost('alamatcustomerdeliv')),
                'idtax'                 => strtoupper($this->request->getPost('idtax')),
                'kdsalesman'            => $this->request->getPost('kdsalesman'),
                'gradecustomer'         => $this->request->getPost('gradecustomer'),
                'carabayar'             => strtoupper($this->request->getPost('carabayar')),
                'currcode'              => strtoupper($this->request->getPost('currcode')),
                'kurs'                  => $this->request->getPost('kurs'),
                'keterangan'            => strtoupper($this->request->getPost('keterangan')),
                'status'                => 'E',
                'inputby'               => $nama,
                'inputdate'             => date('Y-m-d H:i:s')
            ]);

            $reload = true;
        }

        $builderDetail = $db->table('sc_tmp.penjualan_dtl');
        $insertCount = 0;
        $message = '';

        // =====================================================
        // MODE EDIT
        // =====================================================
        if (!empty($idurut)) {

            $uniqueid = $this->request->getPost('uniqueid');

            $qty         = $this->request->getPost('qty');
            $harga       = $this->request->getPost('harga') ?: 0;
            $multidisc   = $this->request->getPost('multidisc') ?: 0;
            $nilai       = $this->request->getPost('nilai') ?: 0;
            $description = strtoupper($this->request->getPost('description'));
            $idprincipal = strtoupper($this->request->getPost('idprincipal'));
            $idgudang    = strtoupper($this->request->getPost('idgudang'));

            // >>> FIX: normalisasi idspec (inline, tanpa helper)
            $idspec = strtoupper(trim($this->request->getPost('idspec') ?? ''));
            if ($idspec === '' || $idspec === 'NULL') {
                $idspec = '';
            }

            $nilai = $qty * $harga;

            $h = $db->table('sc_tmp.penjualan')
                ->select('kurs, idtax, currcode')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            $kurs     = $h['kurs'] ?? 1;
            $idtax    = $h['idtax'] ?? 'NON';
            $currcode = $h['currcode'] ?? '';

            $nilaikonversi = $nilai * $kurs;
            $nilaipajak = 0;

            if (!empty($idtax) && trim($idtax) !== 'NON' && $nilai > 0) {
                $taxDetails = $db->table('sc_mst.tax_dtl')
                    ->select('percentation')
                    ->where('idtax', $idtax)
                    ->get()
                    ->getResultArray();

                $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
                $nilaipajak = $nilai * $totalPersen / 100;
            }

            // ===== VALIDASI STOK (mode edit) =====
            $rowCek = $db->table('sc_tmp.penjualan_dtl')
                ->select('idbarang, nmbarang')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)
                ->get()
                ->getRowArray();

            $idbarangCek = trim((string) ($rowCek['idbarang'] ?? ''));
            $nmbarangCek = trim((string) ($rowCek['nmbarang'] ?? ''));

            if ($idbarangCek === '') {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Barang pada detail tidak ditemukan. Silakan muat ulang data.'
                ]);
            }

            $qtyOtherRow = $db->query("
                SELECT COALESCE(SUM(qty),0) AS qty
                FROM sc_tmp.penjualan_dtl
                WHERE docno = ?
                AND inputby = ?
                AND TRIM(idbarang) = ?
                AND TRIM(idgudang) = ?
                AND COALESCE(NULLIF(TRIM(idspec), ''), '') = ?
                AND TRIM(uniqueid) <> ?
            ", [$docno, $nama, $idbarangCek, $idgudang, $idspec, (string) $uniqueid])->getRowArray();

            $stokSaldo = $this->sales_stock_saldo($idbarangCek, $idgudang, $idspec);
            $qtyTotal  = (float) ($qtyOtherRow['qty'] ?? 0) + (float) $qty;

            if ($idgudang === '') {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gudang untuk barang ' . $idbarangCek . ' belum dipilih.'
                ]);
            }

            if ($qtyTotal > $stokSaldo) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk ' . $idbarangCek
                        . ($nmbarangCek !== '' ? ' (' . $nmbarangCek . ')' : '')
                        . ' di gudang ' . $idgudang
                        . ($idspec !== '' ? ', batch ' . $idspec : '')
                        . '. Tersedia: ' . $this->soFormatGridNumber($stokSaldo)
                        . ', dibutuhkan: ' . $this->soFormatGridNumber($qtyTotal) . '.'
                ]);
            }
            // ===== END VALIDASI STOK =====
            $builderDetail
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('uniqueid', $uniqueid)->update([
                    'qty'           => $qty,
                    'harga'         => $harga,
                    'multidisc'     => $multidisc,
                    'nilai'         => $nilai,
                    'nilaikonversi' => $nilaikonversi,
                    'nilaipajak'    => $nilaipajak,
                    'idtax'         => $idtax,
                    'kurs'          => $kurs,
                    'currcode'      => $currcode,
                    'idprincipal'   => $idprincipal,
                    'idgudang'      => $idgudang,
                    'idspec'        => $idspec,
                    'description'   => $description,
                    'updateby'      => $nama,
                    'updatedate'    => date('Y-m-d H:i:s')
                ]);

            $message = 'Data berhasil diupdate';

        } else {
            // =====================================================
            // MODE ADD
            // =====================================================

            if ($isFromSJ) {
                // >>> FIX: idspec dinormalisasi di SQL
                $soDetails = $db->query("
                    SELECT
                        sj.docno,
                        sj.idbarang,
                        sj.uniqueid,
                        sj.nmbarang,
                        sj.unit,
                        sj.qty,
                        sj.idgudang,
                        COALESCE(so.idprincipal, '')   AS idprincipal,
                        CASE
                            WHEN so.idspec IS NULL OR UPPER(TRIM(so.idspec)) = 'NULL' THEN ''
                            ELSE TRIM(so.idspec)
                        END                            AS idspec,
                        COALESCE(so.harga, 0)          AS harga,
                        COALESCE(so.multidisc, 0)      AS multidisc,
                        COALESCE(so.currcode, 'IDR')   AS currcode,
                        COALESCE(so.idtax, 'NON')      AS idtax,
                        COALESCE(so.kurs, 1)           AS kurs,
                        COALESCE(so.nilaipajak, 0)     AS nilaipajak,
                        COALESCE(so.nilaikonversi, 0)  AS nilaikonversi,
                        sj.qtypenjualan,
                        COALESCE(so.nilai, 0)          AS nilai
                    FROM sc_trx.suratjalan_dtl sj
                    LEFT JOIN sc_trx.salesorder_dtl so
                        ON so.uniqueid = sj.uniqueid
                    WHERE TRIM(sj.docno) = ?
                ", [$docnosj])->getResult();
            } else {
                // >>> FIX: idspec dinormalisasi di SQL
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
                        CASE
                            WHEN idspec IS NULL OR UPPER(TRIM(idspec)) = 'NULL' THEN ''
                            ELSE TRIM(idspec)
                        END             AS idspec,
                        nilaipajak,
                        nilaikonversi,
                        multidisc,
                        qtypenjualan,
                        qtydo,
                        harga,
                        currcode,
                        idtax,
                        kurs,
                        nilai
                    FROM sc_trx.salesorder_dtl
                    WHERE TRIM(docno) = ?
                ", [$docnoso])->getResult();
            }

            if (empty($soDetails)) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $isFromSJ ? 'Data SJ tidak ditemukan' : 'Data SO tidak ditemukan'
                ]);
            }

            foreach ($soDetails as $row) {

                if ($isFromSJ) {
                    $qtyPenjualanSaatIni = 0;

                    $existingPenjualan = $db->query("
                        SELECT COALESCE(SUM(qty), 0) AS qty
                        FROM sc_tmp.penjualan_dtl
                        WHERE uniqueid = ?
                        AND docno <> ?
                    ", [$row->uniqueid, $docno])->getRow();

                    if ($existingPenjualan) {
                        $qtyPenjualanSaatIni = $existingPenjualan->qty;
                    }

                    $sisaQty = $row->qty - ($row->qtypenjualan - $qtyPenjualanSaatIni);

                } else {
                    $qtyDOSaatIni = 0;

                    $existingDO = $db->query("
                        SELECT COALESCE(SUM(qty), 0) AS qty
                        FROM sc_tmp.deliveryorder_dtl
                        WHERE uniqueid = ?
                    ", [$row->uniqueid])->getRow();

                    if ($existingDO) {
                        $qtyDOSaatIni = $existingDO->qty;
                    }

                    $sisaQty = $row->qty - ($row->qtypenjualan + $row->qtydo - $qtyDOSaatIni);
                }

                if ($sisaQty <= 0) continue;

                $dupBuilder = $builderDetail
                    ->where('docno', $docno)
                    ->where('uniqueid', $row->uniqueid)
                    ->where('inputby', $nama);

                /* sc_tmp.penjualan_dtl tidak memiliki kolom docnosj. Baris yang
                   berasal dari SJ disimpan dengan docnoso NULL, baris dari SO
                   memakai nomor SO. */
                if ($isFromSJ) {
                    $dupBuilder->where('docnoso', null);
                } else {
                    $dupBuilder->where('docnoso', $docnoso);
                }

                $duplicate = $dupBuilder->countAllResults();

                $nilaiRow = $sisaQty * $row->harga;

                $h = $db->table('sc_tmp.penjualan')
                    ->select('kurs, idtax')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->get()
                    ->getRowArray();

                $kurs = $h['kurs'] ?? 1;
                $idtaxRow = $h['idtax'] ?? 'NON';

                $nilaikonversiRow = $nilaiRow * $kurs;
                $nilaipajakRow = 0;

                if (!empty($idtaxRow) && trim($idtaxRow) !== 'NON' && $nilaiRow > 0) {
                    $taxDetails = $db->table('sc_mst.tax_dtl')
                        ->select('percentation')
                        ->where('idtax', $idtaxRow)
                        ->get()
                        ->getResultArray();

                    $totalPersen = array_sum(array_column($taxDetails, 'percentation'));
                    $nilaipajakRow = $nilaiRow * $totalPersen / 100;
                }

                if ($duplicate == 0) {
                    $builderDetail->insert([
                        'docno'         => $docno,
                        'docnoso'       => $isFromSJ ? null : $docnoso,
                        // kolom docnosj tidak ada pada sc_tmp.penjualan_dtl
                        'idbarang'      => $row->idbarang,
                        'uniqueid'      => $row->uniqueid,
                        'nmbarang'      => $row->nmbarang,
                        'unit'          => $row->unit,
                        'qty'           => $sisaQty,
                        'idprincipal'   => $row->idprincipal,
                        'idgudang'      => $row->idgudang,
                        'idspec'        => $row->idspec,   // sudah bersih dari query
                        'harga'         => $row->harga,
                        'multidisc'     => $row->multidisc,
                        'nilaipajak'    => $nilaipajakRow,
                        'nilaikonversi' => $nilaikonversiRow,
                        'currcode'      => $row->currcode,
                        'kurs'          => $row->kurs,
                        'idtax'         => $row->idtax,
                        'nilai'         => $sisaQty * $row->harga,
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

        // =====================================================
        // HITUNG TOTAL
        // =====================================================
        $penjualanHeader = $builderHeader->select('idtax')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()->getRowArray();
        $idtax = $penjualanHeader['idtax'] ?? '';

        $builderTotalDpp = $db->table('sc_tmp.penjualan_dtl');
        $totalDpp = $builderTotalDpp->select('COALESCE(SUM(nilai), 0) as total_dpp')
            ->where('docno', $docno)
            ->where('inputby', $nama)
            ->get()
            ->getRowArray();

        $dpp = $totalDpp['total_dpp'] ?? 0;
        $jumlahPajak = 0;

        if (!empty($idtax) && trim($idtax) !== 'NON' && $dpp > 0) {
            $taxDetails = $db->table('sc_mst.tax_dtl')
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

        $builderHeader->where('inputby', $nama)
            ->where('docno', $docno)->update([
                'dpp'         => number_format($dpp, 4, '.', ''),
                'jumlahpajak' => number_format($jumlahPajak, 4, '.', ''),
                'total'       => number_format($total, 4, '.', ''),
                'updateby'    => $nama,
                'updatedate'  => date('Y-m-d H:i:s')
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

        $action = '';
        switch ($status) {
            case 'A': $action = 'A'; break;  // APPROVED → 1 huruf
            case 'F': $action = 'R'; break;  // REJECT → 1 huruf (R)
            case 'C': $action = 'C'; break;  // CANCEL → 1 huruf
            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
        }

        if ($update) {
            // ===============================
            // LOG MENGGUNAKAN FUNGSI HELPER
            // ===============================
            $this->m_global->insertlogtrans(
                $docno,
                $action,           // A / R / C (1 huruf)
                'I.S',             // kode module dari menuprg
                'I.S.B.1'          // kode menu untuk PENJUALAN
            );

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

        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('sales/postsales/penjualan'));
        }
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
        $param = " and docno='$docno' and inputby='$nama'";
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
            ->where('uniqueid', $id)
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
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
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
            // CEK HEADER MASIH DRAFT (belum final)
            // ======================================
            $headerRow = $db->table('sc_tmp.penjualan')
                ->select('status')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->get()
                ->getRowArray();

            if (!$headerRow || strtoupper(trim($headerRow['status'])) === 'F') {
                $db->transRollback();
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Dokumen sudah final, tidak dapat diubah'
                ]);
            }

            // ======================================
            // DELETE DETAIL
            // ======================================
            $builder
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->delete();

            $sisa = $db->table('sc_tmp.penjualan_dtl')
                ->where('inputby', $nama)
                ->whereIn('uniqueid', $ids)
                ->countAllResults();

            if ($sisa > 0) {
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
                    'dpp'         => number_format($dpp, 4, '.', ''),
                    'jumlahpajak' => number_format($jumlahPajak, 4, '.', ''),
                    'total'       => number_format($total, 4, '.', ''),
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

    /* Nomor referensi baris detail Penjualan (kolom "No. SJ / DO").
       sc_tmp.penjualan_dtl / sc_trx.penjualan_dtl pada DB yang dipakai aplikasi
       tidak memiliki kolom docnosj; baris hasil "Tarik DO" menyimpan nomor DO
       pada docnodo. Karena itu pembacaan harus aman terhadap property yang
       tidak ada supaya server-side DataTables tidak error (Ajax error). */
    private function penjualan_dtl_ref_doc($row)
    {
        foreach (array('docnosj', 'docnodo') as $col) {
            if (property_exists($row, $col)) {
                $val = trim((string) $row->$col);
                if ($val !== '') {
                    return $val;
                }
            }
        }

        return '';
    }

    function list_tmp_penjualan_dtl(){
        $docno = trim($this->request->getPost('docno')); // ambil dari POST
        $list = $this->m_postsales->get_t_penjualan_dtl_temp_view($docno);
        $data = array();
        $stockCache = [];
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->uniqueid;
            //item
            $row[] = $this->penjualan_dtl_ref_doc($lm);
            $row[] = $lm->docnoso;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->qty) . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->harga) . '</div>';
            $row[] = '<div class="ratakanan">'. $this->soFormatGridNumber($lm->multidisc) . '% </div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. $this->soFormatGridNumber($lm->nilai) . '</div>';
            $row[] = $lm->description;
            $idbarangRow = trim((string) $lm->idbarang);
            $idgudangRow = trim((string) $lm->idgudang);
            $idspecRow   = trim((string) ($lm->idspec ?? ''));
            if ($idspecRow === '' || strtoupper($idspecRow) === 'NULL') {
                $idspecRow = '';
            }

            $stockKey = $idbarangRow . '|' . $idgudangRow . '|' . $idspecRow;
            if (!isset($stockCache[$stockKey])) {
                $stockCache[$stockKey] = $this->sales_stock_saldo($idbarangRow, $idgudangRow, $idspecRow);
            }

            $stokRow     = (float) $stockCache[$stockKey];
            $qtyRow      = (float) $lm->qty;
            $stokRowText = $this->soFormatGridNumber($stokRow);

            if ($idgudangRow === '') {
                $row[] = '<div class="ratakanan text-danger" title="Gudang belum dipilih">- <i class="fa fa-exclamation-triangle"></i></div>';
            } elseif ($qtyRow > $stokRow) {
                $row[] = '<div class="ratakanan text-danger" title="Qty melebihi stok gudang (tersedia ' . $stokRowText . ')">'
                       . $stokRowText . ' <i class="fa fa-exclamation-triangle"></i></div>';
            } else {
                $row[] = '<div class="ratakanan">' . $stokRowText . '</div>';
            }
            // $row[] = $lm->descriptionpp;
            /* Kolom Aksi (index 15) dirender di sisi client oleh penjualan_inline.js
               (tombol update / hapus baris). Server tetap wajib mengirim satu nilai
               placeholder agar jumlah kolom data sama dengan jumlah <th> pada
               v_add_penjualan.php (16 kolom); jika tidak, DataTables memunculkan
               "Requested unknown parameter '15' for row 0, column 15". */
            $row[] = '';
            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
        foreach ($list as $lm) {
            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $lm->idurut;
            //item
            $row[] = $this->penjualan_dtl_ref_doc($lm);
            $row[] = $lm->docnoso;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = $lm->idprincipal;
            $row[] = $lm->idgudang;
            $row[] = $lm->idspec;
            $row[] = $lm->unit;
            $row[] = '<div class="ratakanan">'. number_format($lm->qty, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->qtybonus, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->harga, 4, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan">'. number_format($lm->multidisc, 4, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->volitem, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya, 0, '.', ',') . '</div>';
            // $row[] = '<div class="ratakanan">'. number_format($lm->biaya2, 0, '.', ',') . '</div>';
            $row[] = '<div class="ratakanan text-bold">'. number_format($lm->nilai, 4, '.', ',') . '</div>';
            $row[] = $lm->description;
            // $row[] = $lm->descriptionpp;
            $data[] = $row;

        }

        $output = array(
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            "recordsTotal" => $this->m_postsales->t_penjualan_dtl_view_count_all($docno),
            "recordsFiltered" => $this->m_postsales->t_penjualan_dtl_view_count_filtered($docno),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function finalEntryPenjualan(){
        $nama = trim($this->session->get('nama'));
        // $loccode = trim($this->session->get('loccode'));
        $builder = $this->db->table('sc_tmp.penjualan');

        // Ambil header draft milik user (bind via Query Builder, hindari interpolasi SQL)
        $headerRow = $builder
            ->where('inputby', $nama)
            ->orderBy('inputdate', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $status = $headerRow ? trim($headerRow['status']) : '';
        $docno  = $headerRow ? trim($headerRow['docno']) : '';

        // Detail tidak valid (unit kosong / qty 0 / nmbarang kosong / harga 0 / nilai 0)
        $cek = $this->db->table('sc_tmp.penjualan_dtl')
            ->where('inputby', $nama)
            ->groupStart()
            ->where('unit', '')
            ->orWhere('qty', '0.00')
            ->orWhere('qty', '0')
            ->orWhere('nmbarang', '')
            ->orWhere('harga', '0.00')
            ->orWhere('harga', '0')
            ->orWhere('nilai', '0.00')
            ->orWhere('nilai', '0')
            ->groupEnd()
            ->countAllResults();

        // Total detail
        $cek2 = $this->db->table('sc_tmp.penjualan_dtl')
            ->where('inputby', $nama)
            ->countAllResults();

        //INSERT TRX ERROR
        $builder_trxerror = $this->db->table('sc_mst.trxerror');
        $builder_trxerror->where('userid', $nama);
        $builder_trxerror->where('modul', 'I.S.B.4');
        $builder_trxerror->delete();


        if (($status==='E' and $cek > 0) or ($cek2 <= 0))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek,
                'nomorakhir2' => $cek2,
                'modul' => 'I.S.B.4',
            );
            $builder_trxerror->insert($infotrxerror);

            return redirect()->to(base_url('/sales/postsales/addPenjualan'));
        } else {
            // ======================================
            // CEK STOK (kurang stok) sebelum final
            // ======================================
            $pesanStok = $this->sales_cek_stok_penjualan($docno, $nama);
            if ($pesanStok !== '') {
                session()->setFlashdata('penjualan_error', $pesanStok);
                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek,
                    'nomorakhir2' => $cek2,
                    'modul' => 'I.S.B.4',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addPenjualan'));
            }

            // Ambil dari request POST
            // $pemohon = strtoupper(trim($this->request->getPost('pemohon')));
            // $docdate   = trim($this->request->getPost('docdate'));
            // $senddate   = trim($this->request->getPost('senddate'));
            $jthtempo   = trim($this->request->getPost('jthtempo'));
            // $kdcustomer   = trim($this->request->getPost('kdcustomer'));
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
            // $docdateph = null;
            // if (!empty($docdate)) {
            //     $docdateph = date('Y-m-d', strtotime($docdate));
            // }

            // $delivdateph = null;
            // if (!empty($delivdate)) {
            //     $delivdateph = date('Y-m-d', strtotime($delivdate));
            // }

            // Update data header dulu sebelum set status F
            $updateHeader = [
                // 'docdate'        => $docdateph,
                // 'delivdate'       => $delivdateph,
                'jthtempo'       => $jthtempo,
                // 'kdcustomer'     => strtoupper($kdcustomer),
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

            /* Jangan menimpa informasi/keterangan header draft (mis. hasil
               tarik DO) dengan nilai kosong dari form. */
            foreach ([
                'jthtempo',
                'alamatcustomer',
                'gradecustomer',
                'kdcustomerdeliv',
                'alamatcustomerdeliv',
                'keterangan',
                'currcode',
                'kdsalesman',
                'idtax',
                'carabayar',
            ] as $col) {
                if (trim((string) ($updateHeader[$col] ?? '')) === '') {
                    $updateHeader[$col] = $headerRow[$col] ?? $updateHeader[$col];
                }
            }

            if ((float) ($updateHeader['kurs'] ?? 0) <= 0) {
                $updateHeader['kurs'] = $headerRow['kurs'] ?? $updateHeader['kurs'];
            }

            try {
                $this->db->transStart();

                // Update header (scoped ke pemilik + docno)
                $this->db->table('sc_tmp.penjualan')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->update($updateHeader);

                // FINALIZE: CAS status E -> F (trigger tmp->trx berjalan di sini)
                $this->db->table('sc_tmp.penjualan')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->where('status', 'E')
                    ->update(['status' => 'F']);

                $errDb = $this->sales_errorDb();

                // read-back: trigger sukses akan menghapus baris tmp
                $sisa = $this->db->table('sc_tmp.penjualan')
                    ->where('docno', $docno)
                    ->where('inputby', $nama)
                    ->countAllResults();

                if ($errDb !== '' || $sisa > 0) {
                    $this->db->transRollback();

                    $infotrxerror = array(
                        'userid' => $nama,
                        'errorcode' => 3,
                        'nomorakhir1' => $cek,
                        'nomorakhir2' => $cek2,
                        'modul' => 'I.S.B.4',
                    );
                    $builder_trxerror->insert($infotrxerror);
                    return redirect()->to(base_url('/sales/postsales/addPenjualan'));
                }

                $this->db->transComplete();
            } catch (\Throwable $e) {
                $this->db->transRollback();

                $infotrxerror = array(
                    'userid' => $nama,
                    'errorcode' => 3,
                    'nomorakhir1' => $cek,
                    'nomorakhir2' => $cek2,
                    'modul' => 'I.S.B.4',
                );
                $builder_trxerror->insert($infotrxerror);
                return redirect()->to(base_url('/sales/postsales/addPenjualan'));
            }

            return redirect()->to(base_url('/sales/postsales/penjualan'));



        }

    }


    function show_penjualan(){
        $module = 'I.S';
        $menu = 'I.S.B.4';
        $table = "sc_trx.penjualan";
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
        $logindate = trim($this->session->get('logindate'));

        /* ====== GUARD PERIODE TUTUP ====== */
        $periode = date('ym', strtotime($logindate));
        $dtlPeriode = $this->m_postsales->q_cek_periode($periode)->getRowArray();

        if ($dtlPeriode && strtoupper(trim($dtlPeriode['flagproses'])) === 'TUTUP') {
            // Set pesan error ke session flash, lalu redirect ke list
            session()->setFlashdata('periode_error',
                'Periode ' . $periode . ' sudah TUTUP. Tidak dapat melakukan input.'
            );
            return redirect()->to(base_url('sales/postsales/penjualan'));
        }
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

        $title = " Report Penjualan";

        //$datajson =  base_url("manufactur/production/api_pp/?enc_idbarang=$enc_idbarang&enc_docdate=$enc_docdate&enc_idlocation=$enc_idlocation&enc_idgroup=$enc_idgroup") ;
        $datajson =  base_url("sales/postsales/api_penjualan/?enc_docno=$enc_docno") ;

        // if($formheader==="HEADER"){
        $datamrt =  base_url("assets/mrt/report_penjualan.mrt") ;
        // } else {
        //     $datamrt =  base_url("assets/mrt/report_pp_non_header.mrt") ;
        // }

        return $this->fiky_report->render($datajson,$datamrt,$title,$nama,$module,$table,$docno,$menu);
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



    public function laporan_jurnal_transaksi_pjo()
    {
        try {

            // =================================================
            // GET DOCNO
            // =================================================
            $docno = trim($this->request->getPost('docno'));

            // =================================================
            // VALIDASI
            // =================================================
            if ($docno === '') {
                return $this->response->setJSON([
                    'status'   => false,
                    'messages' => 'Doc No Penjualan tidak ditemukan',
                    'data'     => []
                ]);
            }

            // =================================================
            // PARAMETER QUERY
            // =================================================
            $params = "AND TRIM(jh.docno) = " . $this->db->escape($docno);

            // =================================================
            // GET DATA
            // =================================================
            $query = $this->m_postsales->q_laporan_jurnal_transaksi($params);

            $data = $query->getResultArray();

            // =================================================
            // RESPONSE
            // =================================================
            return $this->response->setJSON([
                'status'   => true,
                'messages' => 'Data berhasil diambil',
                'data'     => $data
            ]);

        } catch (\Throwable $e) {

            log_message('error', 'laporan_jurnal_transaksi_pjo: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'   => false,
                'messages' => $e->getMessage(),
                'data'     => []
            ]);

        }
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
        $kodemenu='I.S.B.5'; $versirelease='I.S.B.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.5'";
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
        $kodemenu='I.S.B.5'; $versirelease='I.S.B.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */

        $paramerror=" and userid='$nama' and modul='I.S.B.5'";
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $kodemenu='I.S.B.5'; $versirelease='I.S.B.5/01'; $releasedate=date('2025-04-12 00:00:00');
        $versidb=$this->fiky_version->version($kodemenu,$versirelease,$releasedate,$nama);
        $x=$this->fiky_menu->menus($kodemenu,$versirelease,$releasedate);
        $data['x'] = $x['rows']; $data['y'] = $x['res']; $data['t'] = $x['xn'];
        $data['kodemenu']=$kodemenu; $data['version']=$versidb;
        $data['nama']=$nama; $data['version']=$versidb;
        /* END CODE UNTUK VERSI */


        $paramerror=" and userid='$nama' and modul='I.S.B.5'";
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $no = isset($_POST['start']) ? (int) $_POST['start'] : 0;
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
            "draw" => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
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
        $builder_trxerror->where('modul', 'I.S.B.5');
        $builder_trxerror->delete();


        if (($status==='E' and $cek->getNumRows() > 0) or ($cek2->getNumRows() <= '0'))
        {
            $infotrxerror = array(
                'userid' => $nama,
                'errorcode' => 3,
                'nomorakhir1' => $cek->getNumRows(),
                'nomorakhir2' => $cek2->getNumRows(),
                'modul' => 'I.S.B.5',
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
                $paramerror=" and userid='$nama' and modul='I.S.B.5'";
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
                    'modul' => 'I.S.B.5',
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
        $codemenu = 'I.S.B.5';
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




    /* ================================================================
       HELPER PENJUALAN (pola sama seperti helper modul PRODUCTION)
       ================================================================ */

    /* Flash message (tipe + teks). */
    private function sales_msg($type, $text)
    {
        return ['type' => $type, 'text' => $text];
    }

    /* Render flash menjadi alert (kelas sama seperti $message existing). */
    private function sales_msg_html($flash)
    {
        if (empty($flash) || empty($flash['text'])) {
            return '';
        }

        $cls = 'alert-info';
        if ($flash['type'] === 'success') {
            $cls = 'alert-success';
        } elseif ($flash['type'] === 'error') {
            $cls = 'alert-danger';
        } elseif ($flash['type'] === 'warning') {
            $cls = 'alert-warning';
        }

        return "<div class='alert " . $cls . "'>" . $flash['text'] . "</div>";
    }

    /* Verifikasi hasil update lewat READ-BACK.
       Menggantikan $this->db->affectedRows() yang tidak aman pada driver
       Postgre CI4 ini (bila query gagal, resultID=false sehingga
       pg_affected_rows() melempar TypeError dan error asli tertutup). */
    private function sales_hitungStatus($table, $docno, $inputby, $status)
    {
        $builder = $this->db->table($table)->where('status', $status);

        if ($docno !== null && $docno !== '') {
            $builder->where('docno', trim($docno));
        }

        if ($inputby !== null && $inputby !== '') {
            $builder->where('inputby', $inputby);
        }

        return $builder->countAllResults();
    }

    /* Ambil pesan error terakhir dari koneksi Postgre.
       WAJIB dipanggil SEGERA setelah query yang gagal (sebelum query lain),
       karena setelah transaksi abort, query berikutnya menutupi error asli. */
    private function sales_errorDb()
    {
        try {
            if (!empty($this->db->connID)) {
                return trim((string) pg_last_error($this->db->connID));
            }
        } catch (\Throwable $x) {
            return '';
        }

        return '';
    }

    /* Terjemahkan error database menjadi pesan yang ramah untuk user. */
    private function sales_pesan_db($e)
    {
        $pesan = $e->getMessage();
        $db   = $this->sales_errorDb();

        if ($db !== '') {
            $pesan = $db;
        }

        return $pesan;
    }

    /* Saldo stok gudang (read-only) untuk item + gudang + batch.
       Sumber: sc_trx.stkblc (sama dengan posting/validasi final). */
    private function sales_stock_saldo($idbarang, $idgudang, $idspec = '')
    {
        $idbarang = trim((string) $idbarang);
        $idgudang = trim((string) $idgudang);
        $idspec   = trim((string) $idspec);

        if ($idspec === '' || strtoupper($idspec) === 'NULL') {
            $idspec = '';
        }

        if ($idbarang === '' || $idgudang === '') {
            return 0.0;
        }

        $row = $this->db->query("
            SELECT (COALESCE(SUM(qty_in),0) - COALESCE(SUM(qty_out),0)) AS saldo
            FROM sc_trx.stkblc
            WHERE TRIM(idbarang) = ?
            AND TRIM(warehouse) = ?
            AND COALESCE(NULLIF(TRIM(batch), ''), '') = ?
        ", [$idbarang, $idgudang, $idspec])->getRowArray();

        return (float) ($row['saldo'] ?? 0);
    }

    /* Cek kecukupan stok untuk draft penjualan (cegah kurang stok).
       Return : '' bila cukup, selain itu pesan error. */
    private function sales_cek_stok_penjualan($docno, $inputby)
    {
        $rows = $this->db->table('sc_tmp.penjualan_dtl')
            ->where('docno', $docno)
            ->where('inputby', $inputby)
            ->get()
            ->getResultArray();

        foreach ($rows as $r) {
            $idbarang = trim((string)($r['idbarang'] ?? ''));
            $idgudang = trim((string)($r['idgudang'] ?? ''));
            $idspec   = trim((string)($r['idspec'] ?? ''));
            $qty      = (float)($r['qty'] ?? 0);

            // Normalisasi: null / '' / 'NULL' / 'null' → ''
            if ($idspec === '' || strtoupper($idspec) === 'NULL') {
                $idspec = '';
            }

            if ($idbarang === '' || $qty <= 0) {
                continue;
            }

            $saldo = $this->db->query("
                SELECT (COALESCE(SUM(qty_in),0) - COALESCE(SUM(qty_out),0)) AS saldo
                FROM sc_trx.stkblc
                WHERE TRIM(idbarang) = ?
                AND TRIM(warehouse) = ?
                AND COALESCE(NULLIF(TRIM(batch), ''), '') = ?
            ", [$idbarang, $idgudang, $idspec])->getRowArray();

            $sisa = (float)($saldo['saldo'] ?? 0);

            if ($qty > $sisa) {
                return 'Stok tidak mencukupi untuk barang ' . $idbarang
                    . ' (gudang ' . $idgudang . '). Tersedia: ' . $sisa
                    . ', dibutuhkan: ' . $qty;
            }
        }

        return '';
    }

    /* ==================================================================
     * DELIVERY ORDER – INLINE ENTRY (addDeliveryOrder)
     * Endpoint khusus halaman add/edit DO. Tidak menyentuh menu lain.
     * ================================================================== */

    private function doModel()
    {
        return new DeliveryOrderModel();
    }

    public function doLoadDeliveryOrder()
    {
        $docno = strtoupper(trim((string) ($this->request->getGet('docno') ?? $this->request->getPost('docno'))));
        if ($docno === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor DO kosong']);
        }
        $model = $this->doModel();
        $nama  = trim((string) $this->session->get('nama'));
        $rows  = $model->getDetail($docno);
        $docdate = $model->getDocDate($docno, $nama);

        return $this->response->setJSON([
            'success' => true,
            'header'  => $model->getHeader($docno),
            'rows'    => $rows,
            'docdate' => $docdate,
            // Stok akhir sc_trx.stkblc posisi tanggal DO (bukan nilai tersimpan).
            'stok'    => $model->stokForRows($rows, $docdate),
        ]);
    }

    public function doSaveDeliveryOrderHeader()
    {
        $nama   = trim($this->session->get('nama'));
        $model  = $this->doModel();
        $header = $model->saveHeader($this->request->getPost(), $nama);

        if (!$header) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor DO wajib diisi']);
        }

        $header = $model->recalcTotals(trim((string) $header['docno']));

        return $this->response->setJSON([
            'success' => true,
            'header'  => $header,
            'message' => 'Header Delivery Order tersimpan',
        ]);
    }

    public function doSaveDeliveryOrderRow()
    {
        $nama     = trim($this->session->get('nama'));
        $model    = $this->doModel();
        $post     = $this->request->getPost();

        $docno    = strtoupper(trim((string) ($post['docno'] ?? '')));
        $idbarang = strtoupper(trim((string) ($post['idbarang'] ?? '')));
        $idgudang = strtoupper(trim((string) ($post['idgudang'] ?? '')));
        $unit     = strtoupper(trim((string) ($post['unit'] ?? '')));
        $qty      = $model->num($post['qty'] ?? 0);

        if ($docno === '' || $idbarang === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Item DO wajib dipilih']);
        }
        if ($idgudang === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Gudang wajib dipilih']);
        }
        if ($unit === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Satuan wajib dipilih']);
        }
        if ($qty <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Qty harus lebih besar dari 0']);
        }

        $row = $model->saveDetail($post, $nama);
        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan item DO']);
        }

        return $this->response->setJSON([
            'success' => true,
            'row'     => $row,
            'header'  => $model->getHeader($docno),
            'message' => 'Item DO tersimpan',
        ]);
    }

    /**
     * Simpan Draft = dokumen resmi DRAFT (status 'D').
     *
     * Alurnya mengikuti trigger sc_tmp.tr_deliveryorder_finalize():
     *   E -> D  : header + detail dipindah dari sc_tmp ke sc_trx dengan
     *             status 'D' TANPA update qtydo SO dan TANPA potong stok.
     * Stok kosong/kurang TIDAK memblokir draft — hanya dilaporkan sebagai
     * peringatan (baris dengan stok kosong tetap hanya boleh Simpan Draft).
     */
    public function doSaveDeliveryOrderDraft()
    {
        $nama  = trim($this->session->get('nama'));
        $model = $this->doModel();
        $post  = $this->request->getPost();

        $docno = strtoupper(trim((string) ($post['docno'] ?? '')));
        if ($docno === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor DO wajib diisi']);
        }
        if (trim((string) ($post['kdcustomer'] ?? '')) === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer wajib dipilih']);
        }

        $header = $model->saveHeader($post, $nama);
        if (!$header) {
            return $this->response->setJSON(['success' => false, 'message' => 'Header DO gagal disimpan']);
        }

        $rows = $model->getDetail($docno);
        if (empty($rows)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Detail DO masih kosong']);
        }

        // Validasi baris sama seperti simpan final.
        foreach ($rows as $r) {
            if (
                trim((string) ($r['unit'] ?? '')) === ''
                || $model->num($r['qty'] ?? 0) <= 0
                || trim((string) ($r['nmbarang'] ?? '')) === ''
            ) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Masih ada baris dengan satuan / qty / nama barang kosong',
                ]);
            }
        }

        $header = $model->recalcTotals($docno);

        // Peringatan stok posisi tanggal DO (sc_trx.stkblc s/d docdate).
        $docdate  = $model->normalDate($header['docdate'] ?? '');
        $stokInfo = $model->stokForRows($rows, $docdate);

        $stokKurang = [];
        foreach ($rows as $r) {
            $info = $stokInfo[(int) ($r['idurut'] ?? 0)] ?? null;
            if (!$info || empty($info['kurang'])) {
                continue;
            }
            $stokKurang[] = trim((string) $r['idbarang'])
                . ' / ' . trim((string) $r['idgudang'])
                . ' / ' . (trim((string) $r['idspec']) !== '' ? trim((string) $r['idspec']) : '-')
                . ' : stok ' . $this->soFormatGridNumber($info['stokakhir'])
                . ', butuh ' . $this->soFormatGridNumber($info['qty']);
        }

        try {
            $this->db->transBegin();

            // Penanda untuk membaca nomor final (trigger bisa menaikkan nomor
            // bila nomor sudah dipakai dokumen lain di sc_trx).
            $mxRow  = $this->db->query("SELECT COALESCE(MAX(idurut), 0) AS mx FROM sc_trx.deliveryorder")->getRowArray();
            $idMax  = (int) ($mxRow['mx'] ?? 0);

            // CAS E -> D: trigger memindahkan header+detail ke sc_trx (status 'D').
            $this->db->table('sc_tmp.deliveryorder')
                ->where('docno', $docno)
                ->where('inputby', $nama)
                ->where('status', 'E')
                ->update([
                    'status'     => 'D',
                    'updateby'   => $nama,
                    'updatedate' => date('Y-m-d H:i:s'),
                ]);

            if ($this->db->affectedRows() <= 0) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Draft DO tidak bisa disimpan (header temporary tidak ditemukan / sudah diproses).',
                ]);
            }

            $saved = $this->db->query(
                "SELECT TRIM(docno) AS docno
                   FROM sc_trx.deliveryorder
                  WHERE idurut > ?
                    AND status = 'D'
                    AND TRIM(inputby) = ?
                  ORDER BY idurut DESC
                  LIMIT 1",
                [$idMax, $nama]
            )->getRowArray();

            $newDocno = trim((string) ($saved['docno'] ?? ''));
            if ($newDocno === '') {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Draft DO gagal diproses oleh database (trigger draft tidak berjalan).',
                ]);
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Draft DO gagal disimpan: ' . $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'success'     => true,
            'docno'       => $newDocno,
            'status'      => 'D',
            'redirect'    => base_url('sales/postsales/deliveryorder'),
            'stok_kurang' => $stokKurang,
            'message'     => 'Draft Delivery Order tersimpan',
        ]);
    }

    public function doDeleteDeliveryOrderRow()
    {
        $model  = $this->doModel();
        $docno  = strtoupper(trim((string) $this->request->getPost('docno')));
        $idurut = (int) $this->request->getPost('idurut');

        if ($docno === '' || $idurut <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter hapus tidak valid']);
        }

        $model->deleteDetail($docno, $idurut);

        return $this->response->setJSON([
            'success' => true,
            'header'  => $model->getHeader($docno),
            'message' => 'Item DO dihapus',
        ]);
    }

    public function doSwapDeliveryOrderRow()
    {
        $model     = $this->doModel();
        $docno     = strtoupper(trim((string) $this->request->getPost('docno')));
        $idurutView = (int) $this->request->getPost('idurut_view');
        $direction = strtolower(trim((string) $this->request->getPost('direction')));

        if ($docno === '' || $idurutView <= 0 || !in_array($direction, ['up', 'down'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter urutan tidak valid']);
        }

        $ok = $model->swapView($docno, $idurutView, $direction);

        return $this->response->setJSON([
            'success' => $ok,
            'rows'    => $model->getDetail($docno),
            'message' => $ok ? '' : 'Baris sudah berada di posisi teratas/terbawah',
        ]);
    }

    public function doListSO()
    {
        $model = $this->doModel();
        $rows  = $model->listSO(
            (string) $this->request->getPost('kdcustomer'),
            (string) $this->request->getPost('search')
        );

        return $this->response->setJSON(['success' => true, 'rows' => $rows]);
    }

    /**
     * Detail SO untuk modal Tarik SO (parent-child, bisa pilih sebagian baris).
     */
    public function doListSODetail()
    {
        $model   = $this->doModel();
        $docnoso = strtoupper(trim((string) $this->request->getPost('docnoso')));
        $docno   = strtoupper(trim((string) $this->request->getPost('docno')));

        if ($docnoso === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor SO tidak valid',
                'rows'    => [],
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'rows'    => $model->listSODetail($docnoso, $docno),
        ]);
    }

    public function doPullSO()
    {
        $nama    = trim($this->session->get('nama'));
        $docno   = strtoupper(trim((string) $this->request->getPost('docno')));
        $docnoso = strtoupper(trim((string) $this->request->getPost('docnoso')));

        // Detail SO terpilih (uniqueid). Kosong = tarik semua item SO.
        $ids = $this->request->getPost('ids');
        if (is_string($ids)) {
            $ids = trim($ids);
            $decoded = ($ids !== '' && $ids[0] === '[') ? json_decode($ids, true) : null;
            $ids = is_array($decoded) ? $decoded : ($ids === '' ? [] : explode(',', $ids));
        }
        $ids = is_array($ids) ? $ids : [];

        $picks = [];
        foreach ($ids as $v) {
            $v = trim((string) $v);
            if ($v !== '') {
                $picks[$v] = $v;
            }
        }

        $model = $this->doModel();
        $res   = $model->pullSO($docno, $docnoso, $nama, array_values($picks));

        if (!empty($res['success'])) {
            $res['rows'] = $model->getDetail($docno);
        }

        return $this->response->setJSON($res);
    }

    public function doStokDeliveryOrder()
    {
        $model    = $this->doModel();
        $nama     = trim((string) $this->session->get('nama'));
        $docno    = strtoupper(trim((string) ($this->request->getPost('docno') ?? '')));
        $idbarang = strtoupper(trim((string) $this->request->getPost('idbarang')));
        $idgudang = strtoupper(trim((string) $this->request->getPost('idgudang')));
        $idspec   = trim((string) $this->request->getPost('idspec'));
        $qty      = $model->num($this->request->getPost('qty') ?? 0);

        // Batas stok = tanggal dokumen DO (header sc_tmp milik user).
        $docdate = $model->normalDate($this->request->getPost('docdate') ?? '');
        if ($docdate === '') {
            $docdate = $model->getDocDate($docno, $nama);
        }

        $stok = $model->stokAkhir($idbarang, $idgudang, $idspec, $docdate);

        return $this->response->setJSON([
            'success'   => true,
            'stok'      => $stok,
            'docdate'   => $docdate,
            'kosong'    => ($stok <= 0),
            'kurang'    => (($stok <= 0) || ($qty > $stok)),
        ]);
    }

    /**
     * Refresh stok akhir seluruh baris inline (satu query agregat).
     * Dipakai saat grid detail dimuat ulang / tanggal DO berubah.
     */
    public function doStokDeliveryOrderRows()
    {
        $model = $this->doModel();
        $nama  = trim((string) $this->session->get('nama'));
        $docno = strtoupper(trim((string) ($this->request->getPost('docno') ?? '')));

        if ($docno === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor DO kosong',
                'rows'    => [],
            ]);
        }

        $docdate = $model->normalDate($this->request->getPost('docdate') ?? '');
        if ($docdate === '') {
            $docdate = $model->getDocDate($docno, $nama);
        }

        $rows = $model->getDetail($docno);

        return $this->response->setJSON([
            'success' => true,
            'docdate' => $docdate,
            'rows'    => $model->stokForRows($rows, $docdate),
        ]);
    }

    /**
     * Kurs otomatis dari sc_mst.exchangerate berdasarkan docdate:
     * rate terakhir pada / sebelum docdate, fallback rate terakhir.
     */
    public function doKursDeliveryOrder()
    {
        $model    = $this->doModel();
        $currcode = strtoupper(trim((string) ($this->request->getPost('currcode') ?? '')));
        $docdate  = trim((string) ($this->request->getPost('docdate') ?? ''));

        return $this->response->setJSON([
            'success' => true,
            'kurs'    => $model->kursByDocdate($currcode, $docdate),
        ]);
    }



}

