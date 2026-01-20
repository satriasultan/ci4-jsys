<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_Customer extends Model
{

    /* PERANGKAT */
    var $customer_view = "(select a.* from sc_mst.customer a 
    where COALESCE(status,'') = 'F') as x";
    var $customer_view_column = array('kdcustomer', 'nmcustomer','alamat_kantor','phone','createdby','createddate','updateby','updatedate');
    var $customer_view_order = array("createddate" => 'desc'); // default order
    private function _get_query_t_customer()
    {
        // $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();
        $builder = $this->db->table($this->customer_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        // $tglrange = $this->request->getPost('tglrange');
        // if (!empty($tglrange)) {
        //     $dates = explode(' - ', $tglrange);
        //     if (count($dates) == 2) {
        //         $start = \DateTime::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
        //         $end   = \DateTime::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
        //         $builder->where("docdate BETWEEN '{$start}' AND '{$end}'");
        //     }
        // }

        // $status_filter = $this->request->getPost('status_filter');
        // if ($status_filter !== 'ALL') {
        //     $builder->where('trim(x.status)', $status_filter);
        // }

        $i = 0;
        foreach ($this->customer_view_column as $item)
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

                if(count($this->customer_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->customer_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->customer_view_order))
        {
            $order = $this->customer_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_customer_view(){
        $builder = $this->_get_query_t_customer();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_customer_view_count_filtered()
    {
        $builder = $this->_get_query_t_customer();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_customer_view_count_all()
    {
        $builder = $this->_get_query_t_customer();
        return $builder->countAllResults();
    }
    public function get_t_customer_view_by_id($id)
    {
        $builder = $this->_get_query_t_customer();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    public function q_mstcustomer($param){
        return $this->db->query("select * from 
(select a.* from sc_mst.customer a
) as x where id is not null $param ");
    }

}

?>