<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
require FCPATH . 'vendor/autoload.php';
use TADPHP\TAD;
use TADPHP\TADFactory;

class ValidatorAbsensi extends BaseController
{

    function index(){
        //echo 'QONTOL';
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }

    function clearMC_Checkinout()
    {
        $this->db->query("delete from sc_tmp.mc_checkinout");
        echo json_encode(array("status" => TRUE));
    }

    // OTOMATIS FINGER TARIK
    function getFinger(){
        //read data finger with id =
        $id = 1;
        $param = " and id='$id'";
        $set = $this->m_absenMesin->q_setup_attendance($param)->getRowArray();
        $tglawal =  date('Y-m-d',strtotime(date('Y-m-d') . "-2 days"));
        $tglakhir = date('Y-m-d');
        if (trim($set['read_method'])==='RATE_TAD')
        {
            $options = [
                'ip' => trim($set['ipaddress']),
                'com_key' => trim($set['com_key']),
                //'com_key' => 111825,
                'soap_port' => trim($set['port']),
            ];
            $tad = new TADFactory($options);
            $con = $tad->get_instance();
            if ($con->is_alive()) {
                $att_logs = $con->get_att_log();
                // Now, you want filter the resulset to get att logs between '2014-01-10' and '2014-03-20'.
                $filtered_att_logs = $att_logs->filter_by_date(
                    ['start' => $tglawal,'end' => $tglakhir]
                );
                $data = $filtered_att_logs->to_array();
                //$data = $con->get_att_log()->to_array();
                //$data = $con->get_user_info()->to_array();
                foreach ($data["Row"] as $key => $log) {
                    $useratt = array(
                        'userid' => $log['PIN'],
                        'badgenumber' => $log['PIN'],
                        'status' => ($log['Status']==='1') ? 'OUT' : 'IN',
                        'checkinout' => $log['DateTime'],
                    );
                    //tarik manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$log['PIN']."' and checkinout='".$log['DateTime']."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }

                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        } else {
            $getConString = $this->fikyattd->connectAttd($set['ipaddress'], $set['port']);
            //new $getConString;
            $zk = $this->fikyattd;

            if ($zk->connect()) {
                $attendance = $zk->getAttendance();
                while( list($idx, $attendancedata) = each($attendance) ) {

                    $useratt = array (
                        'userid' => $idx,
                        'badgenumber' => $attendancedata[0],
                        'status' => $attendancedata[1],
                        'checkinout' => date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))
                    );
                    //tarik delete manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$attendancedata[0]."' and checkinout='".date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }

                $zk->getTime();
                $zk->enableDevice();
                $zk->disconnect();
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }
    }

    function getFinger2(){
        //read data finger with id =
        $id = 2;
        $param = " and id='$id'";
        $set = $this->m_absenMesin->q_setup_attendance($param)->getRowArray();
        $tglawal =  date('Y-m-d',strtotime(date('Y-m-d') . "-2 days"));
        $tglakhir = date('Y-m-d');
        if (trim($set['read_method'])==='RATE_TAD')
        {
            $options = [
                'ip' => trim($set['ipaddress']),
                'com_key' => trim($set['com_key']),
                //'com_key' => 111825,
                'soap_port' => trim($set['port']),
            ];
            $tad = new TADFactory($options);
            $con = $tad->get_instance();
            if ($con->is_alive()) {
                $att_logs = $con->get_att_log();
                // Now, you want filter the resulset to get att logs between '2014-01-10' and '2014-03-20'.
                $filtered_att_logs = $att_logs->filter_by_date(
                    ['start' => $tglawal,'end' => $tglakhir]
                );
                $data = $filtered_att_logs->to_array();
                //$data = $con->get_att_log()->to_array();
                //$data = $con->get_user_info()->to_array();
                foreach ($data["Row"] as $key => $log) {
                    $useratt = array(
                        'userid' => $log['PIN'],
                        'badgenumber' => $log['PIN'],
                        'status' => ($log['Status']==='1') ? 'OUT' : 'IN',
                        'checkinout' => $log['DateTime'],
                    );
                    //tarik manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$log['PIN']."' and checkinout='".$log['DateTime']."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }

                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        } else {
            $getConString = $this->fikyattd->connectAttd($set['ipaddress'], $set['port']);
            //new $getConString;
            $zk = $this->fikyattd;

            if ($zk->connect()) {
                $attendance = $zk->getAttendance();
                while( list($idx, $attendancedata) = each($attendance) ) {

                    $useratt = array (
                        'userid' => $idx,
                        'badgenumber' => $attendancedata[0],
                        'status' => $attendancedata[1],
                        'checkinout' => date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))
                    );
                    //tarik delete manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$attendancedata[0]."' and checkinout='".date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }

                $zk->getTime();
                $zk->enableDevice();
                $zk->disconnect();
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }
    }

    function getFinger3(){
        //read data finger with id =
        $id = 3;
        $param = " and id='$id'";
        $set = $this->m_absenMesin->q_setup_attendance($param)->getRowArray();
        $tglawal =  date('Y-m-d',strtotime(date('Y-m-d') . "-2 days"));
        $tglakhir = date('Y-m-d');
        if (trim($set['read_method'])==='RATE_TAD')
        {
            $options = [
                'ip' => trim($set['ipaddress']),
                'com_key' => trim($set['com_key']),
                //'com_key' => 111825,
                'soap_port' => trim($set['port']),
            ];
            $tad = new TADFactory($options);
            $con = $tad->get_instance();
            if ($con->is_alive()) {
                $att_logs = $con->get_att_log();
                // Now, you want filter the resulset to get att logs between '2014-01-10' and '2014-03-20'.
                $filtered_att_logs = $att_logs->filter_by_date(
                    ['start' => $tglawal,'end' => $tglakhir]
                );
                $data = $filtered_att_logs->to_array();
                //$data = $con->get_att_log()->to_array();
                //$data = $con->get_user_info()->to_array();
                foreach ($data["Row"] as $key => $log) {
                    $useratt = array(
                        'userid' => $log['PIN'],
                        'badgenumber' => $log['PIN'],
                        'status' => ($log['Status']==='1') ? 'OUT' : 'IN',
                        'checkinout' => $log['DateTime'],
                    );
                    //tarik manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$log['PIN']."' and checkinout='".$log['DateTime']."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }

                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        } else {
            $getConString = $this->fikyattd->connectAttd($set['ipaddress'], $set['port']);
            //new $getConString;
            $zk = $this->fikyattd;

            if ($zk->connect()) {
                $attendance = $zk->getAttendance();
                while( list($idx, $attendancedata) = each($attendance) ) {

                    $useratt = array (
                        'userid' => $idx,
                        'badgenumber' => $attendancedata[0],
                        'status' => $attendancedata[1],
                        'checkinout' => date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))
                    );
                    //tarik delete manual
                    $this->db->query("delete from sc_tmp.mc_checkinout where badgenumber='".$attendancedata[0]."' and checkinout='".date("d-m-Y", strtotime($attendancedata[3])).' '.date("H:i:s", strtotime($attendancedata[3]))."'");
                    $builder = $this->db->table('sc_tmp.mc_checkinout');
                    $builder->insert($useratt);
                }
                $zk->getTime();
                $zk->enableDevice();
                $zk->disconnect();
                echo json_encode(array("status" => TRUE));
            } else {
                echo json_encode(array("status" => FALSE));
            }
        }
    }
    //OTOMATIS TRANSREADY TARIK
    function getTransready(){
        $tglawal =  date('Y-m-d',strtotime(date('Y-m-d') . "-2 days"));
        $tglakhir = date('Y-m-d');
        $this->db->query("select sc_tmp.pr_generate_transready('$tglawal', '$tglakhir')");
        echo json_encode(array("status" => TRUE));
    }

    //TARIK DB FINGER DARI TABEL MESIN
    //triger mc disabled diganti prosedur

    function getDBfromMachine(){
        $tglawal =  date('Y-m-d',strtotime(date('Y-m-d') . "-2 days"));
        $tglakhir = date('Y-m-d');
        $this->db->query("select sc_tmp.pr_mc_checkinout('$tglawal', '$tglakhir')");
        echo json_encode(array("status" => TRUE));
    }


}
