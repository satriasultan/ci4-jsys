<?php

namespace App\Models\Form;

use CodeIgniter\Model;

class M_Form extends Model
{

    /* PERANGKAT */
    var $perangkat_view = "(select a.*,z.uraian as nmstatus from sc_trx.formperangkat a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.1'
) as x";
    var $perangkat_view_column = array('docno', 'nmpemohon','docdate','jnsperangkat','tujuan','instansi');
    var $perangkat_view_order = array("inputdate" => 'desc'); // default order
    private function _get_query_t_perangkat()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->perangkat_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        $tglrange = $this->request->getPost('tglrange');
        if (!empty($tglrange)) {
            $dates = explode(' - ', $tglrange);
            if (count($dates) == 2) {
                $start = \DateTime::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end   = \DateTime::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                $builder->where("docdate BETWEEN '{$start}' AND '{$end}'");
            }
        }

        $status_filter = $this->request->getPost('status_filter');
        if ($status_filter !== 'ALL') {
            $builder->where('trim(x.status)', $status_filter);
        }


        if ($roleid==='IT') {
            //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }
        if ($roleid==='IT') {
            //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }

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


    public function q_trxperangkat($param){
        return $this->db->query("select * from 
(select a.*,z.uraian as nmstatus from sc_trx.formperangkat a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.1'
) as x where docno is not null $param ");
    }



    /* INTERNET */
    var $internet_view = "(select a.*,z.uraian as nmstatus, d.kddept, d.nmdept, j.kdlvl, j.nmlvljabatan from sc_trx.forminternet a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.2'
left outer join sc_mst.departmen d on a.departmen=d.kddept
left outer join sc_mst.lvljabatan j on a.jabatan=j.kdlvl
) as x";
    var $internet_view_column = array('docno','nmpemohon','docdate','jnsakses','keperluan','departmen');
    var $internet_view_order = array("inputdate" => 'desc'); // default order
    private function _get_query_t_internet()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->internet_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        $tglrange = $this->request->getPost('tglrange');
        if (!empty($tglrange)) {
            $dates = explode(' - ', $tglrange);
            if (count($dates) == 2) {
                $start = \DateTime::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end   = \DateTime::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                $builder->where("docdate BETWEEN '{$start}' AND '{$end}'");
            }
        }

        $status_filter = $this->request->getPost('status_filter');
        if ($status_filter !== 'ALL') {
            $builder->where('trim(x.status)', $status_filter);
        }
        // else{
        //     $builder->where('trim(x.status) !=', '');
        // }

        if ($roleid==='IT') {
            //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }

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
        return $this->db->query("select * from 
(select a.*,z.uraian as nmstatus from sc_trx.forminternet a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.2'
) as x where docno is not null $param ");
    }


    //PERANGKAT KELUAR
    var $perangkatkeluar_view = "(select a.*,z.uraian as nmstatus, d.kddept, d.nmdept, j.kdlvl, j.nmlvljabatan from sc_trx.formperangkatkeluar a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.3'
left outer join sc_mst.departmen d on a.departmen=d.kddept
left outer join sc_mst.lvljabatan j on a.jabatan=j.kdlvl
) as x";
    var $perangkatkeluar_view_column = array('docno', 'nmpemohon','docdate','jnsperangkat','keperluan','departmen');
    var $perangkatkeluar_view_order = array("inputdate" => 'desc'); // default order
    private function _get_query_t_perangkatkeluar()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->perangkatkeluar_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        $tglrange = $this->request->getPost('tglrange');
        if (!empty($tglrange)) {
            $dates = explode(' - ', $tglrange);
            if (count($dates) == 2) {
                $start = \DateTime::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end   = \DateTime::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                $builder->where("docdate BETWEEN '{$start}' AND '{$end}'");
            }
        }

        $status_filter = $this->request->getPost('status_filter');
        if ($status_filter !== 'ALL') {
            $builder->where('trim(x.status)', $status_filter);
        }

        if ($roleid==='IT') {
            //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }

        $i = 0;
        foreach ($this->perangkatkeluar_view_column as $item)
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

                if(count($this->perangkatkeluar_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->perangkatkeluar_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->perangkatkeluar_view_order))
        {
            $order = $this->perangkatkeluar_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_perangkatkeluar_view(){
        $builder = $this->_get_query_t_perangkatkeluar();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_perangkatkeluar_view_count_filtered()
    {
        $builder = $this->_get_query_t_perangkatkeluar();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_perangkatkeluar_view_count_all()
    {
        $builder = $this->_get_query_t_perangkatkeluar();
        return $builder->countAllResults();
    }
    public function get_t_perangkatkeluar_view_by_id($id)
    {
        $builder = $this->_get_query_t_perangkatkeluar();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_trxperangkatkeluar($param){
        return $this->db->query("select * from 
(select a.*,z.uraian as nmstatus from sc_trx.formperangkatkeluar a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.3'
) as x where docno is not null $param ");
    }



    //PERANGKAT KELUAR
    var $pengadaan_hwsw_view = "(select a.*,z.uraian as nmstatus from sc_trx.formrequest_it a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.10'
) as x";
    var $pengadaan_hwsw_view_column = array('docno','namapic','departemen','jnsbarang');
    var $pengadaan_hwsw_view_order = array("inputdate" => 'desc'); // default order
    private function _get_query_t_pengadaan_hwsw()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->pengadaan_hwsw_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        if ($roleid==='IT') {
            //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('inputby' => "$nama"));
        }

        $i = 0;
        foreach ($this->pengadaan_hwsw_view_column as $item)
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

                if(count($this->pengadaan_hwsw_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->pengadaan_hwsw_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->pengadaan_hwsw_view_order))
        {
            $order = $this->pengadaan_hwsw_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_pengadaan_hwsw_view(){
        $builder = $this->_get_query_t_pengadaan_hwsw();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_pengadaan_hwsw_view_count_filtered()
    {
        $builder = $this->_get_query_t_pengadaan_hwsw();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_pengadaan_hwsw_view_count_all()
    {
        $builder = $this->_get_query_t_pengadaan_hwsw();
        return $builder->countAllResults();
    }
    public function get_t_pengadaan_hwsw_view_by_id($id)
    {
        $builder = $this->_get_query_t_pengadaan_hwsw();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_trxpengadaan_hwsw($param){
        return $this->db->query("select * from 
(select a.*,z.uraian as nmstatus from sc_trx.formrequest_it a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.N.A.10'
) as x where docno is not null $param ");
    }


    /* PRINT LABEL CETAK UNTUK IT MANAGEMENT TEMPORARY */
    var $t_print_label_form_temporary = "sc_tmp.print_label_form_temporary";
    var $t_print_label_form_temporary_column = array('docno','cmodul','cgroup','cname');
    var $t_print_label_form_temporary_order = array("docno" => 'asc'); // default order
    private function _get_query_print_label_form_temporary()
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();

        $builder = $this->db->table($this->t_print_label_form_temporary);
        $i = 0;

        $nama = trim($this->session->get('nama'));
        $builder->where('inputby',$nama);
        foreach ($this->t_print_label_form_temporary_column as $item)
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

                if(count($this->t_print_label_form_temporary_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_print_label_form_temporary_order[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_print_label_form_temporary_order))
        {
            $order = $this->t_print_label_form_temporary_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_query_print_label_form_temporary(){
        $builder = $this->_get_query_print_label_form_temporary();
        ////$this->_get_query_t_unit();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function print_label_form_temporary_filtered()
    {
        $builder = $this->_get_query_print_label_form_temporary();
        ////$this->_get_query_t_unit();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function print_label_form_temporary_count_all()
    {
        $builder = $this->_get_query_print_label_form_temporary();
        return $builder->countAllResults();
    }

    function q_labeltemporary(){
        return $this->db->query("select * from sc_tmp.print_label_form_temporary");
    }



}
