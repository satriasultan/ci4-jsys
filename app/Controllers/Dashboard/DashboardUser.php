<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Libraries\Fiky_encryption;


class DashboardUser extends BaseController
{

    public function __construct()
    {
        //parent::__construct();
        //$this->session = \Config\Services::session();
        //$this->db = \Config\Database::connect('default');
        //$this->fiky_encryption = new Fiky_encryption();

    }

    public function index()
    {

        $viewData['title']="Home";
        $viewData['currentperiod'] = date("Y-m");

        // if (trim($this->session->get('roleid'))==='AD') {
        //     //return redirect()->to(base_url('/capital/administration/admin_transaction'));
        //     return redirect()->to(base_url('/form/pa'));
        // }

        $roleid = $this->session->get('roleid');
        $viewData['roleid'] = trim($this->session->get('roleid'));
        $viewData['akses']= $this->m_global->list_akses_role($roleid)->getRowArray();

        $day=4;
        $viewData['title_recent'] = 'Aktifitas '. $day .' Hari Terakhir (Recent Latest Employee Activity)';
        //$data['list_recent']=$this->m_stspeg->q_recent_employee_activities($param=4)->getResult();
        $viewData['formppcount'] = $this->m_dashboarduser->_get_query_formppcount();
        $viewData['formiicount'] = $this->m_dashboarduser->_get_query_formiicount();
        $viewData['formpkcount'] = $this->m_dashboarduser->_get_query_formpkcount();
        $viewData['totalformppcount'] = $this->m_dashboarduser->_get_query_totalformppcount();
        $viewData['totalformiicount'] = $this->m_dashboarduser->_get_query_totalformiicount();
        $viewData['totalformpkcount'] = $this->m_dashboarduser->_get_query_totalformpkcount();
        return $this->template->render("dashboard/v_index_user", $viewData);
        //echo 'MASUK DASHBOARD';
    }

    function list_formpp(){
        $list = $this->m_dashboarduser->get_formpp_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<div class="text-bold">' . $lm->docno . '</div>';
            if ($lm->selisih_hari > 0) {
                // lewat dari hari ini → bg-danger
                $row[] = '<span class="badge w-100" style="font-size:medium;background-color:red;color:white;">H+' . $lm->selisih_hari . '</span>';
            } elseif ($lm->selisih_hari < 0) {
                // masih ada sisa hari → bg-warning
                $row[] = '<span class="badge w-100 bg-warning" style="font-size:medium">H' . $lm->selisih_hari . '</span>';
            } else {
                // tepat hari ini → oranye (misalnya pakai bg-warning-subtle / custom)
                $row[] = '<span class="badge w-100" style="font-size:medium;background-color:darkorange;color:white;">H0</span>';
            }
            $row[] = $lm->periodeakhir;
            $row[] = $lm->nmpemohon;
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'CLOSE':
                    $badgeClass = 'badge-danger';
                    break;
                case 'OPEN':
                    $badgeClass = 'badge-success';
                    break;
                case 'BATAL':
                    $badgeClass = 'badge-warning';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';
            $row[] = $lm->jnsperangkat;
            // $row[] = $lm->periodemulai;
           

            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_dashboarduser->formpp_view_count_all(),
            "recordsFiltered" => $this->m_dashboarduser->formpp_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_formii(){
        $list = $this->m_dashboarduser->get_formii_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<div class="text-bold">' . $lm->docno . '</div>';
            if ($lm->selisih_hari > 0) {
                // lewat dari hari ini → bg-danger
                $row[] = '<span class="badge w-100 "  style="font-size:medium;background-color:red;color:white;">H+' . $lm->selisih_hari . '</span>';
            } elseif ($lm->selisih_hari < 0) {
                // masih ada sisa hari → bg-warning
                $row[] = '<span class="badge w-100 bg-warning" style="font-size:medium">H' . $lm->selisih_hari . '</span>';
            } else {
                // tepat hari ini → oranye (misalnya pakai bg-warning-subtle / custom)
                $row[] = '<span class="badge w-100" style="font-size:medium;background-color:darkorange;color:white;">H0</span>';
            }
            $row[] = $lm->expdate;
            $row[] = $lm->nmpemohon;
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'TIDAK DIIZINKAN':
                    $badgeClass = 'badge-danger';
                    break;
                case 'DIIZINKAN':
                    $badgeClass = 'badge-success';
                    break;
                case 'BATAL':
                    $badgeClass = 'badge-warning';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';            
            // $row[] = $lm->duedate_next;

            $aksesList = [];

            if ($lm->aksesuser === true || $lm->aksesuser === 't') {
                $aksesList[] = 'User';
            }
            if ($lm->aksesinternet === true || $lm->aksesinternet === 't') {
                $aksesList[] = 'Internet';
            }
            if ($lm->aksesemail === true || $lm->aksesemail === 't') {
                $aksesList[] = 'Email';
            }
            if ($lm->aksesapp === true || $lm->aksesapp === 't') {
                $aksesList[] = 'Aplikasi';
            }
            if ($lm->aksesother === true || $lm->aksesother === 't') {
                $aksesList[] = 'Lain-lain';
            }

            $row[] = implode(', ', $aksesList);
            // $row[] = $lm->expdate;
            // $row[] = '<div style="min-height:40px; max-height:100px; overflow-y:auto;">'
            //     . htmlspecialchars($lm->keperluan, ENT_QUOTES, 'UTF-8') .
            //     '</div>';
            
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_dashboarduser->formii_view_count_all(),
            "recordsFiltered" => $this->m_dashboarduser->formii_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function list_formpk(){
        $list = $this->m_dashboarduser->get_formpk_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<div class="text-bold">' . $lm->docno . '</div>';
            if ($lm->selisih_hari > 0) {
                // lewat dari hari ini → bg-danger
                $row[] = '<span class="w-100 badge "   style="font-size:medium;background-color:red;color:white;">H+' . $lm->selisih_hari . '</span>';
            } elseif ($lm->selisih_hari < 0) {
                // masih ada sisa hari → bg-warning
                $row[] = '<span class="w-100 badge bg-warning" style="font-size:medium">H' . $lm->selisih_hari . '</span>';
            } else {
                // tepat hari ini → oranye (misalnya pakai bg-warning-subtle / custom)
                $row[] = '<span class="w-100 badge" style="background-color:orange;color:white;">H0</span>';
            }
            $row[] = $lm->periodeakhir;

            $row[] = $lm->nmpemohon;
            $status = $lm->nmstatus ?? $lm->status;
            $badgeClass = 'badge-secondary'; // Default

            switch (strtoupper($status)) {
                case 'DRAFT USER':
                    $badgeClass = 'badge-secondary';
                    break;
                case 'CLOSE':
                    $badgeClass = 'badge-danger';
                    break;
                case 'OPEN':
                    $badgeClass = 'badge-success';
                    break;
                case 'BATAL':
                    $badgeClass = 'badge-warning';
                    break;
                default:
                    $badgeClass = 'badge-primary'; // Default (primary) jika status tidak dikenali
                    break;
            }

            $row[] = '<div class="text-center"><span style="font-size:12px" class="badge ' . $badgeClass . ' w-100">' . htmlspecialchars($status) . '</span></div>';            
            $jnsform = strtoupper(trim($lm->jnsform));
            if ($jnsform === 'BARU') {
                $row[] = '<span class="w-100 badge bg-success">BARU</span>';
            } elseif ($jnsform === 'PERPANJANGAN') {
                $row[] = '<span class="w-100 badge bg-info text-dark">PERPANJANGAN</span>';
            } else {
                $row[] = '-';
            }
            $row[] = $lm->jnsperangkat;
            
            // $row[] = $lm->duedate_next;
            
            
            // $row[] = '<div class="ratakanan" style="font-weight: bold;">'.$lm->selisih.'</div>';
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_dashboarduser->formpk_view_count_all(),
            "recordsFiltered" => $this->m_dashboarduser->formpk_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }


    function list_minstock(){
        $list = $this->m_dashboard->get_minstock_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $lm->idbarang;
            $row[] = $lm->nmbarang;
            $row[] = '<div class="ratakanan" style="font-weight: bold;">'.$lm->minstock.'</div>';
            $row[] = '<div class="ratakanan" style="background-color: #ff7171;font-weight: bold;">' .$lm->sisa.'</div>';
            $row[] = $lm->unit;
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_dashboard->minstock_view_count_all(),
            "recordsFiltered" => $this->m_dashboard->minstock_view_count_filtered(),
            "data" => $data,
        );
        echo $this->fiky_encryption->jDatatable($output);
    }

    function minstock(){
        $viewData['title']="Home";
        $viewData['currentperiod'] = date("Y-m");
        return $this->template->render("dashboard/v_minstock", $viewData);
    }

    function demoCoordinate(){
        return $this->template->render('dashboard/coordinat');
        //$this->load->view('dashboard/coordinat');
    }

    function capture(){
        $this->load->view('dashboard/capture');
    }

    function api_summary_pembelian(){
        $sum_po = $this->m_dashboard->list_po("and trim(status) not in ('V','O')")->getNumRows();
        $sum_ots_po = $this->m_dashboard->list_po("and trim(status) in ('A','S')")->getNumRows();
        $sum_finish_po = $this->m_dashboard->list_po("and trim(status) in ('P')")->getNumRows();
        $sum_void = $this->m_dashboard->list_po("and trim(status) in ('V','O')")->getNumRows();

        $ini = array(
            'sum_po' =>$sum_po,
            'sum_ots_po' =>$sum_ots_po,
            'sum_finish_po' =>$sum_finish_po,
            'sum_void' =>$sum_void,

        );
        echo json_encode($ini,JSON_PRETTY_PRINT);
    }

    function api_transaction_tahunan(){
        $dt = $this->m_dashboard->q_dashboard_transaction_global("and trim(idtypecuti) in ('DT')",'2022')->getResult();
        $iz = $this->m_dashboard->q_dashboard_transaction_global("and trim(idtypecuti) in ('IZ')",'2022')->getResult();
        $sk = $this->m_dashboard->q_dashboard_transaction_global("and trim(idtypecuti) in ('SK')",'2022')->getResult();
        $ct = $this->m_dashboard->q_dashboard_transaction_global("and trim(idtypecuti) in ('CT')",'2022')->getResult();
        foreach ($dt as $dt1) {
            $hasildt[] = $dt1;
        }
        foreach($hasildt as $hdt) {
            $picdt[] = $hdt->pic;
            $bulandt[] = $hdt->bulan;
            $idtypedt[] = $hdt->durasijam;
        }
//
        foreach ($iz as $iz1) {
            $hasiliz[] = $iz1;
        }
        foreach($hasiliz as $hiz) {
            $piciz[] = $hiz->pic;
            $bulaniz[] = $hiz->bulan;
            $idtypeiz[] = $hiz->durasijam;
        }
//
        foreach ($ct as $ct1) {
            $hasilct[] = $ct1;
        }
        foreach($hasilct as $hct) {
            $picct[] = $hct->pic;
            $bulanct[] = $hct->bulan;
            $idtypect[] = $hct->durasijam;
        }
        //
        foreach ($sk as $sk1) {
            $hasilsk[] = $sk1;
        }
        foreach($hasilsk as $hsk) {
            $picsk[] = $hsk->pic;
            $bulansk[] = $hsk->bulan;
            $idtypesk[] = $hsk->durasijam;
        }
        $ini = array(
            'bulan' => array('Januari','February','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'),
            'dt' =>'Outstanding',
            'sk' =>'Masuk',
            'iz' =>'Keluar',
            'ct' =>'Transfer',
            'terlambat' =>$idtypedt,
            'izin' =>$idtypeiz,
            'cuti' =>$idtypect,
            'sakit' =>$idtypesk,

        );

        echo json_encode($ini,JSON_PRETTY_PRINT);

    }

    function logout(){
        $username = trim($this->session->get('nama'));
        $uonline = $this->db->table('sc_log.useronline');
        $uonline->where('username',$username);
        $uonline->delete();
        $this->session->destroy();
        return redirect()->to(base_url('/dashboard'));
    }


}
