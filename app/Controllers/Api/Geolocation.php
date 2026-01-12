<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Geolocation extends BaseController
{

    public function index()
    {
        echo json_encode(ARRAY('Geolocation' => 'True','Wilayah'=>'True')) ;
    }

    /* INI NEGARANYA */
    function add_negara($search=null){
        $dtlbranch=$this->m_global->q_branch()->row_array();
        $branch = $dtlbranch['branch'];
        $data = json_encode(
            array(
                'search' => $this->request->getPost('_search_'),
                'perpage' => $this->request->getPost('_perpage_'),
                'page' => $this->request->getPost('_page_'),
                'paramxnegara' => $this->request->getPost('_paramxnegara_'),
            ),JSON_PRETTY_PRINT
        );
        echo $this->fiky_wilayah->getNegara($data);
    }

    function add_prov($search=null){
        $dtlbranch=$this->m_global->q_branch()->row_array();
        $branch = $dtlbranch['branch'];
        $data = json_encode(
            array(
                'search' => $this->request->getPost('_search_'),
                'perpage' => $this->request->getPost('_perpage_'),
                'page' => $this->request->getPost('_page_'),
                'kodenegara' => $this->request->getPost('_kodenegara_'),
            ),JSON_PRETTY_PRINT
        );
        echo $this->fiky_wilayah->getProvince($data);
    }

    function add_kota($search=null){
        $dtlbranch=$this->m_global->q_branch()->row_array();
        $branch = $dtlbranch['branch'];
        $data = json_encode(
            array(
                'search' => $this->request->getPost('_search_'),
                'perpage' => $this->request->getPost('_perpage_'),
                'page' => $this->request->getPost('_page_'),
                'kodenegara' => $this->request->getPost('_kodenegara_'),
                'kodeprov' => $this->request->getPost('_kodeprov_'),
            ),JSON_PRETTY_PRINT
        );
        echo $this->fiky_wilayah->getKota($data);

    }

    function add_kec($search=null){
        $dtlbranch=$this->m_global->q_branch()->row_array();
        $branch = $dtlbranch['branch'];

        $data = json_encode(
            array(
                'search' => $this->request->getPost('_search_'),
                'perpage' => $this->request->getPost('_perpage_'),
                'page' => $this->request->getPost('_page_'),
                'kodenegara' => $this->request->getPost('_kodenegara_'),
                'kodeprov' => $this->request->getPost('_kodeprov_'),
                'kodekotakab' => $this->request->getPost('_kodekotakab_'),
            ),JSON_PRETTY_PRINT
        );
        echo $this->fiky_wilayah->getKecamatan($data);

    }

    function add_desa($search=null){
        $dtlbranch=$this->m_global->q_branch()->row_array();
        $branch = $dtlbranch['branch'];

        $data = json_encode(
            array(
                'search' => $this->request->getPost('_search_'),
                'perpage' => $this->request->getPost('_perpage_'),
                'page' => $this->request->getPost('_page_'),
                'kodenegara' => $this->request->getPost('_kodenegara_'),
                'kodeprov' => $this->request->getPost('_kodeprov_'),
                'kodekotakab' => $this->request->getPost('_kodekotakab_'),
                'kodekec' => $this->request->getPost('_kodekec_'),
            ),JSON_PRETTY_PRINT
        );
        echo $this->fiky_wilayah->getDesa($data);

    }

    /* NEW GEOLOCATION TO SELECT 2*/
    /* LIST DEPARTEMENT GLOBAL MODULE*/
    function list_negara(){
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
            $paramglobal1= " and kodenegara='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kodenegara='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $page = intval($page);
        $limit = $perpage * $page;

        $param=" and (kodenegara like '%$search%' $paramglobal1 $paramglobal2 ) or (namanegara like '%$search%' $paramglobal1 $paramglobal2 ) order by namanegara asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_negara($param)->getResult();
        $count = $this->m_global->q_negara($param)->getNumRows();
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


    function list_provinsi(){
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
            $paramglobal = " and trim(coalesce(kodenegara,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kodeprov='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kodeprov='$varGet'";
        } else {
            $paramglobal2= "";
        }
        $param=" and (kodeprov like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(namaprov) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by namaprov asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_provinsi($param)->getResult();
        $count = $this->m_global->q_provinsi($param)->getNumRows();
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

    function list_kota(){
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
            $paramglobal = " and trim(coalesce(kodeprov,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kodekotakab='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kodekotakab='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kodekotakab like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(namakotakab) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by namakotakab asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_kotakab($param)->getResult();
        $count = $this->m_global->q_kotakab($param)->getNumRows();
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


    function list_kecamatan(){
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
            $paramglobal = " and trim(coalesce(kodekotakab,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kodekec='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kodekec='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kodekec like '%$search%' $paramglobal $paramglobal1 $paramglobal2) or (upper(namakec) like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) order by namakec asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_kecamatan($param)->getResult();
        $count = $this->m_global->q_kecamatan($param)->getNumRows();
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

    function list_desa(){
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
            $paramglobal = " and trim(coalesce(kodekec,'')) ='$pg'";
        } else {
            $paramglobal = "";
        }

        $varGet = trim($this->request->getGet('var'));
        $varPost = trim($this->request->getPost('var'));
        if (!empty($varGet) or $varGet!=='') {
            $paramglobal1= " and kodekeldesa='$varGet'";
        } else {
            $paramglobal1= "";
        }
        if (!empty($varPost) or $varPost!=='') {
            $paramglobal2= " and kodekeldesa='$varGet'";
        } else {
            $paramglobal2= "";
        }

        $param=" and (kodekeldesa like '%$search%' $paramglobal $paramglobal1 $paramglobal2 ) or (upper(namakeldesa) like '%$search%' $paramglobal $paramglobal1 $paramglobal2  ) order by namakeldesa asc";
        //$param="";
        // $getResult = $this->m_skperingatan->q_mst_karyawan()->getResult();
        $getResult = $this->m_global->q_keldesa($param)->getResult();
        $count = $this->m_global->q_keldesa($param)->getNumRows();
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
