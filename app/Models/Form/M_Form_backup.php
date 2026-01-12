<?php

namespace App\Models\Form;

use CodeIgniter\Model;

class M_Form_backup extends Model
{

    /* CAPITAL AUTHORIZATION */
    var $perangkat_view = "(select a.*,z.uraian as nmstatus from sc_trx.formperangkat a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.1'
) as x";
    var $perangkat_view_column = array('nmpemohon','docdate','jnsperangkat','tujuan','instansi');
    var $perangkat_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_perangkat()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->perangkat_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        // if ($roleid==='IT') {
        //         //$builder->where(array('status is not null'));
        // } else {
        //     $builder->where(array('kddept' => "$dept"));
        // }

        $i = 0;
        foreach ($this->perangkat_view_column as $item)
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

                if(count($this->perangkat_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->perangkat_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->perangkat_view_order))
        {
            $order = $this->perangkat_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_perangkat_view(){
        $builder = $this->_get_query_t_perangkat();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_perangkat_view_count_filtered()
    {
        $builder = $this->_get_query_t_perangkat();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_perangkat_view_count_all()
    {
        $builder = $this->_get_query_t_perangkat();
        return $builder->countAllResults();
    }
    public function get_t_perangkat_view_by_id($id)
    {
        $builder = $this->_get_query_t_perangkat();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }



     /* CAPITAL AUTHORIZATION */
    var $internet_view = "(select a.*,z.uraian as nmstatus from sc_trx.forminternet a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.2'
) as x";
    var $internet_view_column = array('nmpemohon','docdate','jnsakses','keperluan','departmen');
    var $internet_view_order = array("docno" => 'desc'); // default order
    private function _get_query_t_internet()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->internet_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        // if ($roleid==='IT') {
        //         //$builder->where(array('status is not null'));
        // } else {
        //     $builder->where(array('kddept' => "$dept"));
        // }

        $i = 0;
        foreach ($this->internet_view_column as $item)
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

                if(count($this->internet_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->internet_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->internet_view_order))
        {
            $order = $this->internet_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_internet_view(){
        $builder = $this->_get_query_t_internet();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_internet_view_count_filtered()
    {
        $builder = $this->_get_query_t_internet();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_internet_view_count_all()
    {
        $builder = $this->_get_query_t_internet();
        return $builder->countAllResults();
    }
    public function get_t_internet_view_by_id($id)
    {
        $builder = $this->_get_query_t_internet();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_trxinternet($param){
        return $this->db->query("select *,trim(linkdefault)||trim(docnoencrypt) as barcodelink2 from 
(select a.*,z.uraian as nmstatus from sc_trx.forminternet a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.2'
) as x where docno is not null $param ");
    }



}
