<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 7/7/20, 9:42 AM
 *  * Last Modified: 7/7/20, 9:42 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */


namespace App\Models\Dashboard;
use CodeIgniter\Model;


class M_DashboardUser extends Model
{

    /* FORM PP */
    var $formpp_view = "(select a.*,
                                z.uraian as nmstatus,
                                (CURRENT_DATE - a.periodeakhir) as selisih_hari
                        from sc_trx.formperangkat a
                        left outer join sc_mst.trxtype z 
                        on a.status=z.kdtrx and z.jenistrx='I.N.A.1'
    ) as x";
    var $formpp_view_column = array('nmpemohon','docdate','jnsperangkat','tujuan','instansi');
    var $formpp_view_order = array("docno" => 'desc'); // default order
    private function _get_query_formpp()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $builder = $this->db->table($this->formpp_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // $builder->where('selisih <= 25');
        // $builder->where("trim(status) != 'CLOSE'");
        // Hitung H+7 dari hari ini
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));

        // Tambahkan filter periodeakhir <= hari_ini + 7
        $builder->where('periodeakhir <=', $todayPlus7);
        $builder->whereIn('trim(status)', ['I', 'O']);

        $i = 0;
        foreach ($this->formpp_view_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->formpp_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->formpp_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->formpp_view_order))
        {
            $order = $this->formpp_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_formpp_view(){
        $builder = $this->_get_query_formpp();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function formpp_view_count_filtered()
    {
        $builder = $this->_get_query_formpp();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function formpp_view_count_all()
    {
        $builder = $this->_get_query_formpp();
        return $builder->countAllResults();
    }
    public function get_formpp_view_by_id($id)
    {
        $builder = $this->_get_query_formpp();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }







     /* FORM II */
    var $formii_view = "(select a.*,
                                z.uraian as nmstatus,
                                (CURRENT_DATE - a.expdate) as selisih_hari 
                        from sc_trx.forminternet a
                        left outer join sc_mst.trxtype z 
                        on a.status=z.kdtrx and z.jenistrx='I.N.A.2'
    ) as x";
    var $formii_view_column = array('nmpemohon','docdate','jnsakses','keperluan','departmen');
    var $formii_view_order = array("docno" => 'desc'); // default order
    private function _get_query_formii()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $builder = $this->db->table($this->formii_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // $builder->where('selisih <= 25');
        // $builder->where("trim(status) != 'CLOSE'");
        // Hitung H+7 dari hari ini
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));

        // Tambahkan filter periodeakhir <= hari_ini + 7
        $builder->where('expdate <=', $todayPlus7);
        $builder->whereIn('trim(status)', ['I', 'A']);

        $i = 0;
        foreach ($this->formii_view_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->formii_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->formii_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->formii_view_order))
        {
            $order = $this->formii_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_formii_view(){
        $builder = $this->_get_query_formii();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function formii_view_count_filtered()
    {
        $builder = $this->_get_query_formii();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function formii_view_count_all()
    {
        $builder = $this->_get_query_formii();
        return $builder->countAllResults();
    }
    public function get_formii_view_by_id($id)
    {
        $builder = $this->_get_query_formii();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }






     /* FORM PK */
    var $formpk_view = "(select a.*,
                                z.uraian as nmstatus,
                                (CURRENT_DATE - a.periodeakhir) as selisih_hari
                        from sc_trx.formperangkatkeluar a
                        left outer join sc_mst.trxtype z 
                        on a.status=z.kdtrx and z.jenistrx='I.N.A.3'
    ) as x";


    var $formpk_view_column = array('nmpemohon','docdate','jnsperangkat','keperluan','departmen');
    var $formpk_view_order = array("docno" => 'desc'); // default order
    private function _get_query_formpk()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $builder = $this->db->table($this->formpk_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // $builder->where('selisih <= 25');
        // $builder->where("trim(status) != 'CLOSE'");
        // Hitung H+7 dari hari ini
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));

        // Tambahkan filter periodeakhir <= hari_ini + 7
        $builder->where('periodeakhir <=', $todayPlus7);
        $builder->whereIn('trim(status)', ['I', 'O']);

        $i = 0;
        foreach ($this->formpk_view_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->formpk_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->formpk_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->formpk_view_order))
        {
            $order = $this->formpk_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_formpk_view(){
        $builder = $this->_get_query_formpk();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function formpk_view_count_filtered()
    {
        $builder = $this->_get_query_formpk();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function formpk_view_count_all()
    {
        $builder = $this->_get_query_formpk();
        return $builder->countAllResults();
    }
    public function get_formpk_view_by_id($id)
    {
        $builder = $this->_get_query_formpk();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    var $formppcount_view = "(select a.*,
                                z.uraian as nmstatus,
                                (CURRENT_DATE - a.periodeakhir) as selisih_hari
                        from sc_trx.formperangkat a
                        left outer join sc_mst.trxtype z 
                        on a.status=z.kdtrx and z.jenistrx='I.N.A.1'
    ) as x";

    var $formiicount_view = "(select a.*,
                                z.uraian as nmstatus,
                                (CURRENT_DATE - a.expdate) as selisih_hari 
                        from sc_trx.forminternet a
                        left outer join sc_mst.trxtype z 
                        on a.status=z.kdtrx and z.jenistrx='I.N.A.2'
    ) as x";
    
    var $formpkcount_view = "(select a.*,
                                z.uraian as nmstatus,
                                (CURRENT_DATE - a.periodeakhir) as selisih_hari
                        from sc_trx.formperangkatkeluar a
                        left outer join sc_mst.trxtype z 
                        on a.status=z.kdtrx and z.jenistrx='I.N.A.3'
    ) as x";



    function _get_query_formppcount()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->formppcount_view);
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // untuk Form Perangkat Pakai periodeakhir
        $builder->where('periodeakhir <=', $todayPlus7);
        $builder->whereIn('trim(status)', ['I', 'O']);

        return $builder->countAllResults();
    }

    function _get_query_formiicount()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));
        
        $builder = $this->db->table($this->formiicount_view);
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // untuk Form Internet Pakai expdate
        $builder->where('expdate <=', $todayPlus7);
        $builder->whereIn('trim(status)', ['I', 'A']);

        return $builder->countAllResults();
    }

    function _get_query_formpkcount()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->formpkcount_view);
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // untuk Form Perangkat Keluar Pakai periodeakhir
        $builder->where('periodeakhir <=', $todayPlus7);
        $builder->whereIn('trim(status)', ['I', 'O']);

        return $builder->countAllResults();
    }


    /* TOTAL */

    function _get_query_totalformppcount()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->formppcount_view);
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // untuk Form Perangkat Pakai periodeakhir
        // $builder->where('periodeakhir <=', $todayPlus7);
        // $builder->whereIn('trim(status)', ['I', 'O']);

        return $builder->countAllResults();
    }

    function _get_query_totalformiicount()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));
        
        $builder = $this->db->table($this->formiicount_view);
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // untuk Form Internet Pakai expdate
        // $builder->where('expdate <=', $todayPlus7);
        // $builder->whereIn('trim(status)', ['I', 'A']);

        return $builder->countAllResults();
    }

    function _get_query_totalformpkcount()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $todayPlus7 = date('Y-m-d', strtotime('+7 days'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->formpkcount_view);
        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        // untuk Form Perangkat Keluar Pakai periodeakhir
        // $builder->where('periodeakhir <=', $todayPlus7);
        // $builder->whereIn('trim(status)', ['I', 'O']);

        return $builder->countAllResults();
    }

}
