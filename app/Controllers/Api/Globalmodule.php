<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Globalmodule extends BaseController
{

    function index()
    {
        echo json_encode(ARRAY('cok' => 'ASUW','cik'=>'Jembut')) ;
    }

    function option_trxtype(){

        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (uraian like '%$search%' $paramglobal ) or ( kdtrx like '%$search%' $paramglobal ) order by uraian asc";
        $getResult = $this->m_global->q_trxtype($param)->getResult();
        $count = $this->m_global->q_trxtype($paramglobal)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function option_trxtype_by_id(){

        $id = trim($this->request->getGet('var'));
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = " and kdtrx='$id' and jenistrx='I.D.A.1_TYPE'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (uraian like '%$search%' $paramglobal ) or ( kdtrx like '%$search%' $paramglobal ) order by kdtrx asc";
        $getResult = $this->m_global->q_trxtype($param)->getResult();
        $count = $this->m_global->q_trxtype($paramglobal)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }
    function option_trxtype_ktg_by_id(){

        $id = trim($this->request->getGet('var'));
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = " and kdtrx='$id' and jenistrx='I.D.A.1_KTG'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (uraian like '%$search%' $paramglobal ) or ( kdtrx like '%$search%' $paramglobal ) order by kdtrx asc";
        $getResult = $this->m_global->q_trxtype($param)->getResult();
        $count = $this->m_global->q_trxtype($paramglobal)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function option_mcustomer(){

        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (custcode like '%$search%' $paramglobal ) or ( custname like '%$search%' $paramglobal ) order by custname asc";
        $getResult = $this->m_global->q_customer($param)->getResult();
        $count = $this->m_global->q_customer($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                'param' => $param,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function option_mcustomer_by_id(){
        $id = trim($this->request->getGet('var'));
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = " and custcode='$id'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (custcode like '%$search%' $paramglobal ) or ( custname like '%$search%' $paramglobal ) order by custname asc";
        $getResult = $this->m_global->q_customer($param)->getResult();
        $count = $this->m_global->q_customer($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                'param' => $param,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function option_idbu(){

        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kdcabang like '%$search%' $paramglobal ) or ( desc_cabang like '%$search%' $paramglobal ) order by desc_cabang asc";
        $getResult = $this->m_global->q_kantorwilayah($param)->getResult();
        $count = $this->m_global->q_kantorwilayah($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                'param' => $param,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }


     function list_branchjob(){

         $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idbranch='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idbranch='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idbranch like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmbranch like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by idbranch asc";
        //$param="";
        $getResult = $this->m_global->q_branchjob($param)->getResult();
        $count = $this->m_global->q_branchjob($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function option_idbu_by_id(){

        $id = trim($this->request->getGet('var'));
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = " and kdcabang='$id' ";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kdcabang like '%$search%' $paramglobal ) or ( desc_cabang like '%$search%' $paramglobal ) order by desc_cabang asc";
        $getResult = $this->m_global->q_kantorwilayah($param)->getResult();
        $count = $this->m_global->q_kantorwilayah($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                'param' => $param,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    /* LIST KARYAWAN */
    function list_karyawan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and nik='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and nik='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param="  and ( coalesce(resign,'')!='YES' and nmlengkap like '%$search%' $paramglobal1 $paramglobal2 ) or ( coalesce(statuskepegawaian,'')!='KO' and nik like '%$search%'  $paramglobal1 $paramglobal2 ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_lv_m_karyawan($param)->getResult();
        $count = $this->m_global->q_lv_m_karyawan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_karyawan_by_id(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getGet('var'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_param_global_');
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and ( nik like '%$search%' $paramglobal ) order by nmlengkap asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_lv_m_karyawan($param)->getResult();
        $count = $this->m_global->q_lv_m_karyawan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'param' => $param,
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    /* END LIST KARYAWAN */


    /* LIST DEPARTEMENT GLOBAL MODULE*/
    function list_departmen(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kddept='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kddept='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kddept like '%$search%' $paramglobal1 $paramglobal2 ) or (nmdept like '%$search%' $paramglobal1 $paramglobal2 ) order by nmdept asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_departmen($param)->getResult();
        $count = $this->m_global->q_departmen($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_departmen_nm(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and nmdept='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and nmdept='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kddept like '%$search%' $paramglobal1 $paramglobal2 ) or (nmdept like '%$search%' $paramglobal1 $paramglobal2 ) order by nmdept asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_departmen($param)->getResult();
        $count = $this->m_global->q_departmen($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }


    
    function list_lvljabatan_nm(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = $pg;
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and nmlvljabatan='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and nmlvljabatan='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdlvl like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmlvljabatan like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmlvljabatan asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_lvljabatan($param)->getResult();
        $count = $this->m_global->q_lvljabatan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }


    function list_subdepartmen(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kddept,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdsubdept='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdsubdept='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdsubdept like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmsubdept like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmsubdept asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_subdepartmen($param)->getResult();
        $count = $this->m_global->q_subdepartmen($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'getResultparam' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_subdepartmen_by_id(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getGet('var'));
        $page = intval($page);
        $limit = $perpage * $page;

        //if (!empty($pg) or $pg!=='') {
        $paramglobal = " and trim(coalesce(kdsubdept,'')) ='$pg'";
//        } else {
//            $paramglobal = "";
//        }

        $param=" and (kdsubdept like '%$search%' $paramglobal ) or (nmsubdept like '%$search%' $paramglobal ) order by nmsubdept asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_subdepartmen($param)->getResult();
        $count = $this->m_global->q_subdepartmen($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_jabatan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;
        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kdsubdept,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdjabatan='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdjabatan='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdjabatan like '%$search%' $paramglobal $paramglobal1 $paramglobal2  ) or (nmjabatan like '%$search%' $paramglobal $paramglobal1 $paramglobal2  ) order by nmjabatan asc";
        $getResult = $this->m_global->q_jabatan($param)->getResult();
        $count = $this->m_global->q_jabatan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                //'paramglobal' => $paramglobal,
                //'parameter' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_jabatan_by_id(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getGet('var'));
        $page = intval($page);
        $limit = $perpage * $page;
        //if (!empty($pg) or $pg!=='') {
        $paramglobal = " and trim(coalesce(kdjabatan,'')) ='$pg'";
//        } else {
//            $paramglobal = "";
//        }

        $param=" and (kdjabatan like '%$search%' $paramglobal ) or (nmjabatan like '%$search%' $paramglobal ) order by nmjabatan asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_jabatan($param)->getResult();
        $count = $this->m_global->q_jabatan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_lvljabatan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = $pg;
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdlvl='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdlvl='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdlvl like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmlvljabatan like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmlvljabatan asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_lvljabatan($param)->getResult();
        $count = $this->m_global->q_lvljabatan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_lvljabatan_by_id(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getGet('var'));
        $page = intval($page);
        $limit = $perpage * $page;
        $paramglobal= " and kdlvl='$pg'";
        $param=" and (kdlvl like '%$search%' $paramglobal ) or (nmlvljabatan like '%$search%' $paramglobal ) order by nmlvljabatan asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_lvljabatan($param)->getResult();
        $count = $this->m_global->q_lvljabatan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }
    /* END LIST DEPARTEMENT GLOBAL MODULE*/


    function list_regu(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getGet('var'));
        if (!empty($pg)) {
            $paramglobal = " and kdregu='$pg'";
        } else {
            $paramglobal = "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kdregu like '%$search%' $paramglobal ) or (nmregu like '%$search%' $paramglobal ) order by nmregu asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_regu($param)->getResult();
        $count = $this->m_global->q_regu($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }


    function golongan_darah()
    {
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and goldarah='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and goldarah='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (nmgoldarah like '%$search%' $paramglobal $paramglobal1 $paramglobal2) or ( goldarah like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmgoldarah asc";
        $getResult = $this->m_global->q_golongandarah($param)->getResult();
        $count = $this->m_global->q_golongandarah($paramglobal)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function jenis_kelamin()
    {
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idkelamin='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idkelamin='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (nmkelamin like '%$search%' $paramglobal $paramglobal1 $paramglobal2) or ( idkelamin like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmkelamin asc";
        $getResult = $this->m_global->q_kelamin($param)->getResult();
        $count = $this->m_global->q_kelamin($paramglobal)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function agama()
    {
        $nama = $this->session->get('nik');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdagama='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdagama='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (nmagama like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or ( kdagama like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmagama asc";
        $getResult = $this->m_global->q_agama($param)->getResult();
        $count = $this->m_global->q_agama($paramglobal)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'search' => $search,
                //'perpage' => $perpage,
                'items' => $getResult,
                //'limit' => $limit,
                'incomplete_getResults' => false,
                //'group' => $getResult,
                'paramglobal' => $paramglobal,
            ),
            JSON_PRETTY_PRINT
        );
    }

    //// division divisi
    ///
    function list_division(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and iddivision='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and iddivision='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (iddivision like '%$search%' $paramglobal $paramglobal1 $paramglobal2) or (nmdivision like '%$search%' $paramglobal  $paramglobal1 $paramglobal2) order by nmdivision asc";
        //$param="";
        $getResult = $this->m_global->q_division($param)->getResult();
        $count = $this->m_global->q_division($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }
    // cost center

    function list_costcenter(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idcostcenter='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idcostcenter='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idcostcenter like '%$search%' $paramglobal $paramglobal1 $paramglobal2) or (nmcostcenter like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmcostcenter asc";
        //$param="";
        $getResult = $this->m_global->q_costcenter($param)->getResult();
        $count = $this->m_global->q_costcenter($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }
    /// list golongan
    function list_golongan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idgroupgol='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idgroupgol='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idgroupgol like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmgroupgol like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmgroupgol asc";
        //$param="";
        $getResult = $this->m_global->q_groupgol($param)->getResult();
        $count = $this->m_global->q_groupgol($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    /// list warehouse/plant
    function list_plant(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and loccode='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and loccode='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (loccode like '%$search%' $paramglobal $paramglobal1 $paramglobal2) or (locaname like '%$search%'  $paramglobal $paramglobal1 $paramglobal2 ) order by locaname asc";
        //$param="";
        $getResult = $this->m_global->q_mgudang($param)->getResult();
        $count = $this->m_global->q_mgudang($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    /// list kepegawaian
    function list_kepegawaian(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdkepegawaian='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdkepegawaian='$varGet'";
        } else {
            $paramglobal2= "";
        }
        $param=" and (kdkepegawaian like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmkepegawaian like '%$search%' $paramglobal $paramglobal1 $paramglobal2  ) order by nmkepegawaian asc";
        //$param="";
        $getResult = $this->m_global->q_statuspegawai($param)->getResult();
        $count = $this->m_global->q_statuspegawai($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'parameter' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    /// list kepegawaian
    function list_ptkp(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kodeptkp='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kodeptkp='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kodeptkp like '%$search%' $paramglobal $paramglobal1 $paramglobal2) order by kodeptkp asc";
        //$param="";
        $getResult = $this->m_global->q_ptkp($param)->getResult();
        $count = $this->m_global->q_ptkp($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }


    /// list kepegawaian
    function list_pendidikan(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdpendidikan='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdpendidikan='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdpendidikan like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmpendidikan like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmpendidikan asc";
        //$param="";
        $getResult = $this->m_global->q_pendidikan($param)->getResult();
        $count = $this->m_global->q_pendidikan($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }

    /// list bank
    function list_bank(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $paramglobal = $this->request->getPost('_paramglobal_');
        $page = intval($page);
        $limit = $perpage * $page;
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdbank='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdbank='$varGet'";
        } else {
            $paramglobal2= "";
        }
        $param=" and (kdbank like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmbank like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmbank asc";
        //$param="";
        $getResult = $this->m_global->q_bank($param)->getResult();
        $count = $this->m_global->q_bank($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }


    /* WAREHOUSE START */
    function list_mlocation(){
        $branch = $this->session->get('branch');
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idlocation='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idlocation='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idlocation like '%$search%' $paramglobal1 $paramglobal2 ) or (nmlocation like '%$search%' $paramglobal1 $paramglobal2 ) order by nmlocation asc";
        $getResult = $this->m_global->q_mlocation($param)->getResult();
        $count = $this->m_global->q_mlocation($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );
    }


    function list_marea(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');

        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idlocation,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and trim(coalesce(idarea,''))='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and trim(coalesce(idarea,''))='$varPost'";
        } else {
            $paramglobal2= "";
        }


        $param=" and (idarea like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (nmarea like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmarea asc";
        $getResult = $this->m_global->q_marea($param)->getResult();
        $count = $this->m_global->q_marea($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'getResultparam' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }


    function list_mgroup(){
        $branch = $this->session->get('branch');
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idgroup='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idgroup='$varGet'";
        } else {
            $paramglobal2= "";
        }
        if (!empty($px)) {
            $parameterx = " and grouptype='$px'";
        } else {
            $parameterx = "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idgroup like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) or (nmgroup like '%$search%' $paramglobal1 $paramglobal2 $parameterx) order by nmgroup asc";
        $getResult = $this->m_global->q_mgroup($param)->getResult();
        $count = $this->m_global->q_mgroup($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }
    function list_msubgroup(){
        $branch = $this->session->get('branch');
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('_var_'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idsubgroup='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idgroup='$varPost'";
        } else {
            $paramglobal2= "";
        }
        if (!empty($px)) {
            $parameterx = " and grouptype='$px'";
        } else {
            $parameterx = "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idsubgroup like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) or (nmsubgroup like '%$search%' $paramglobal1 $paramglobal2 $parameterx) order by nmsubgroup asc";
        $getResult = $this->m_global->q_msubgroup($param)->getResult();
        $count = $this->m_global->q_msubgroup($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_unit(){
        $branch = $this->session->get('branch');
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idunit='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idunit='$varGet'";
        } else {
            $paramglobal2= "";
        }
        if (!empty($px)) {
            $parameterx = " and ctype='$px'";
        } else {
            $parameterx = "";
        }

        $parameterx = " and ctype='UNIT'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idunit like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) order by idunit asc";
        $getResult = $this->m_global->q_unit($param)->getResult();
        $count = $this->m_global->q_unit($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_subunit(){
        $branch = $this->session->get('branch');
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idunit='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and parentunit='$varGet'";
        } else {
            $paramglobal2= "";
        }
        if (!empty($px)) {
            $parameterx = " and parentunit='$px'";
        } else {
            $parameterx = "";
        }

        $parameterx = " and ctype='SUBUNIT'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idunit like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) order by idunit asc";
        $getResult = $this->m_global->q_unit($param)->getResult();
        $count = $this->m_global->q_unit($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_item(){
        $branch = trim($this->session->get('branch'));
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idbarang='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idbarang='$varPost'";
        } else {
            $paramglobal2= "";
        }

        $parameterx = " and coalesce(chold,'NO')!='YES'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (idbarang like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) or (nmbarang like '%$search%' $paramglobal1 $paramglobal2 $parameterx ) order by nmbarang asc";
        $getResult = $this->m_balance->q_item_param($param)->getResult();
        $count = $this->m_balance->q_item_param($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }

    function list_supplier(){
        $branch = trim($this->session->get('branch'));
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and nmsupplier='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and nmsupplier='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (nmsupplier like '%$search%' $paramglobal1 $paramglobal2 ) ";
        $getResult = $this->m_global->q_supplier($param)->getResult();
        $count = $this->m_global->q_supplier($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }


    function list_outstanding_po(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');

        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idlocation,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and trim(coalesce(idarea,''))='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and trim(coalesce(idarea,''))='$varPost'";
        } else {
            $paramglobal2= "";
        }


        $param=" and (docno like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) ";
        $getResult = $this->m_global->q_po_outstanding($param)->getResult();
        $count = $this->m_global->q_po_outstanding($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'getResultparam' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }
    /* WAREHOUSE END */
    /* BATCH PER ITEM & PERGUDANG */
    function list_batch_item(){
        $branch = trim($this->session->get('branch'));
        $loccode = trim($this->session->get('loccode'));
        $search = strtoupper(trim($this->request->getPost('_search_')));
        $perpage = $this->request->getPost('_perpage_');
        $px = trim($this->request->getPost('_parameterx_'));
        $perpage = intval($perpage);
        $page = $this->request->getPost('_page_');
        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and batch='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and batch='$varPost'";
        } else {
            $paramglobal2= "";
        }

        if (!empty($px) or $px!=='') {
            $paramitem= " and idbarang='$px'";
        } else {
            $paramitem= " and idbarang='$px'";
        }

        $parameterx = " and coalesce(idlocation,'')='$loccode'";
        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (batch like '%$search%' $paramitem $paramglobal1 $paramglobal2 $parameterx ) ";
        $getResult = $this->m_balance->q_stkgdw_batch_selecting($param)->getResult();
        $count = $this->m_balance->q_stkgdw_batch_selecting($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
                'param' => $param,
            ),
            JSON_PRETTY_PRINT
        );
    }
    /* BATCH PER ITEM & PERGUDANG */
    /* INSERT NEW BATCH */
    function add_newbatch(){

        $idbarang =  strtoupper(trim($this->request->getPost('idbarang')));
        $batch =  strtoupper(trim($this->request->getPost('batch')));
        $idlocation =  strtoupper(trim($this->session->get('loccode')));
        $parameter = " and idbarang='$idbarang' and batch='$batch' and idlocation='$idlocation'";
        $cekstkgdw = $this->m_balance->q_stkgdw_batch_selecting($parameter)->getNumRows();
        if (!empty($idbarang) and !empty($batch) and $cekstkgdw<=0) {
            $builder = $this->db->table('sc_mst.stkgdw');
            $info = array(
                'idbarang' => $idbarang,
                'batch' => $batch,
                'idlocation' => $idlocation,
                'idarea' => $idlocation.'.0000',
            );
            $builder->insert($info);

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'idbarang' => $idbarang,
                    'batch' => $batch,
                    'idlocation' => $idlocation,
                    'messages' => $batch.' Berhasil Ditambahkan',
                    'status' => true,
                ),
                JSON_PRETTY_PRINT
            );
        } else {
            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'idbarang' => $idbarang,
                    'batch' => $batch,
                    'idlocation' => $idlocation,
                    'messages' => 'Pilih Barang Terlebih Dahulu',
                    'status' => false,
                ),
                JSON_PRETTY_PRINT
            );
        }

    }
    /* INSERT NEW BATCH */


    
    function list_market(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idmarket,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idmarket='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idmarket='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idmarket like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmmarket) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmmarket asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_market($param)->getResult();
        $count = $this->m_global->q_market($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }


    
    function list_gradecust(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kdgradecust,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdgradecust='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdgradecust='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdgradecust like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmgradecust) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmgradecust asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_gradecust($param)->getResult();
        $count = $this->m_global->q_gradecust($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }


    
    function list_salesman(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kdsalesman,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdsalesman='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdsalesman='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdsalesman like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmsalesman) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmsalesman asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_salesman($param)->getResult();
        $count = $this->m_global->q_salesman($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }


    
    function list_kolektor(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kdkolektor,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdkolektor='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdkolektor='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdkolektor like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmkolektor) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by nmkolektor asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_kolektor($param)->getResult();
        $count = $this->m_global->q_kolektor($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }

    
    
    function list_coa(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idcoa,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idcoa='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idcoa='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idcoa like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmcoa) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by level asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_coa($param)->getResult();
        $count = $this->m_global->q_coa($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }



        function list_currency(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(currcode,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and currcode='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and currcode='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (currcode like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(currname) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by currcode asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_currency($param)->getResult();
        $count = $this->m_global->q_currency($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }


    

        function list_customer(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kdcustomer,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdcustomer='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdcustomer='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdcustomer like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmcustomer) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by kdcustomer asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_customer_new($param)->getResult();
        $count = $this->m_global->q_customer_new($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }



    
        function list_golonganbarang(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idgolonganbarang,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idgolonganbarang='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idgolonganbarang='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idgolonganbarang like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmgolonganbarang) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by idgolonganbarang asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_golonganbarang($param)->getResult();
        $count = $this->m_global->q_golonganbarang($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }



    
    function list_jenisproduk(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idjenisproduk,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idjenisproduk='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idjenisproduk='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idjenisproduk like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmjenisproduk) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by idjenisproduk asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_jenisproduk($param)->getResult();
        $count = $this->m_global->q_jenisproduk($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }



    
    
    function list_kelompokbarang(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idkelompokbarang,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idkelompokbarang='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idkelompokbarang='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idkelompokbarang like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmkelompokbarang) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by idkelompokbarang asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_kelompokbarang($param)->getResult();
        $count = $this->m_global->q_kelompokbarang($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }



    
    
    
    function list_principal(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(idprincipal,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and idprincipal='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and idprincipal='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (idprincipal like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmprincipal) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by idprincipal asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_principal($param)->getResult();
        $count = $this->m_global->q_principal($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }

    
    
    
    function list_supplier_new(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(kdsupplier,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kdsupplier='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kdsupplier='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kdsupplier like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(nmsupplier) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by kdsupplier asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_supplier_new($param)->getResult();
        $count = $this->m_global->q_supplier_new($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }



    
    
    function list_pp(){
        $branch = $this->session->get('branch');
        $idbu = $this->session->get('idbu');
        $param_c="";
        //$count = $this->m_instock->q_kdgroup_param($param_c)->getNumRows();
        $search = strtoupper($this->request->getPost('_search_'));
        $perpage = $this->request->getPost('_perpage_');
        $perpage = intval($perpage);
        //$perpage = $perpage < 1 ? $count : $perpage;
        $page = $this->request->getPost('_page_');
        $pg = trim($this->request->getPost('_paramglobal_'));
        $page = intval($page);
        $limit = $perpage * $page;

        if (!empty($pg) or $pg!=='') {
            $paramglobal = " and trim(coalesce(docno,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and docno='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and docno='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (docno like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(keterangan) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by docno asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_pp($param)->getResult();
        $count = $this->m_global->q_pp($param)->getNumRows();
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'total_count' => $count,
                'items' => $getResult,
                'incomplete_getResults' => false,
            ),
            JSON_PRETTY_PRINT
        );

    }
}
