<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Libraries\Fiky_encryption;


class Dashboard extends BaseController
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

        if (trim($this->session->get('roleid'))==='AD') {
            //return redirect()->to(base_url('/capital/administration/admin_transaction'));
            return redirect()->to(base_url('/dashboarduser'));
        }

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
        return $this->template->render("dashboard/v_index", $viewData);
        //echo 'MASUK DASHBOARD';
    }

    function list_jasapembayaran(){
        $list = $this->m_dashboard->get_reminder_jasa_pembayaran_view();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lm) {
            $no++;
            $row = array();
            //$row[] = $no;
            $row[] = $lm->nmsubgroup;
            $row[] = $lm->nmsupplier;
            $row[] = $lm->nmpembayaran;
            $row[] = $lm->duedate_next;
            $row[] = '<div class="ratakanan" style="font-weight: bold;">'.$lm->selisih.'</div>';
            //$row[] = '<div align="right">'.number_format($lm->nominal, 2,',','.').'</div>';
            //add html for action
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_dashboard->reminder_jasa_pembayaran_view_count_all(),
            "recordsFiltered" => $this->m_dashboard->reminder_jasa_pembayaran_view_count_filtered(),
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
