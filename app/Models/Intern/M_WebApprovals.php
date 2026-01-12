<?php

namespace App\Models\Intern;

use CodeIgniter\Model;

class M_WebApprovals extends Model
{
    var $t_trx_approvals_system_view = "( select 
                    coalesce(trim(docno       ::text),'') as docno         ,          
                    coalesce(trim(docdate     ::text),'') as docdate       ,          
                    coalesce(trim(docref      ::text),'') as docref        ,          
                    coalesce(trim(doctype     ::text),'') as doctype       ,          
                    coalesce(trim(doctypename ::text),'') as doctypename   ,          
                    coalesce(trim(kdgroup     ::text),'') as kdgroup       ,          
                    coalesce(trim(kdsubgroup  ::text),'') as kdsubgroup    ,          
                    coalesce(trim(stockcode   ::text),'') as stockcode     ,          
                    coalesce(trim(suppcode    ::text),'') as suppcode      ,          
                    coalesce(trim(subsuppcode ::text),'') as subsuppcode   ,          
                    coalesce(trim(suppname    ::text),'') as suppname      ,          
                    coalesce(trim(stockname   ::text),'') as stockname     ,          
                    coalesce(trim(ppny        ::text),'') as ppny          ,          
                    coalesce(trim(exppn       ::text),'') as exppn         ,          
                    coalesce(trim(ttldiskon   ::text),'') as ttldiskon     ,          
                    coalesce(trim(ttldpp      ::text),'') as ttldpp        ,          
                    coalesce(trim(ttlppn      ::text),'') as ttlppn        ,          
                    coalesce(trim(ttlppnbm    ::text),'') as ttlppnbm      ,          
                    coalesce(trim(ttlnetto    ::text),'') as ttlnetto      ,          
                    coalesce(trim(description ::text),'') as description   ,          
                    coalesce(trim(status      ::text),'') as status        ,          
                    coalesce(trim(asstatus    ::text),'') as asstatus      ,          
                    coalesce(trim(inputdate   ::text),'') as inputdate     ,          
                    coalesce(trim(inputby     ::text),'') as inputby       ,          
                    coalesce(trim(updatedate  ::text),'') as updatedate    ,          
                    coalesce(trim(updateby    ::text),'') as updateby      ,          
                    coalesce(trim(approvaldate::text),'') as approvaldate  ,          
                    coalesce(trim(approvalby  ::text),'') as approvalby    ,          
                    coalesce(trim(canceldate  ::text),'') as canceldate    ,          
                    coalesce(trim(cancelby    ::text),'') as cancelby      ,          
                    coalesce(trim(docnotmp    ::text),'') as docnotmp      ,          
                    coalesce(trim(tabelname   ::text),'') as tabelname     ,          
                    coalesce(trim(astabelname ::text),'') as astabelname   ,          
                    coalesce(trim(erptype     ::text),'') as erptype       ,          
                    coalesce(trim(branch      ::text),'') as branch,
                    coalesce(trim(to_char(docdate,'dd-mm-yyyy')::text),'') as docdate1,
                    case when status='A' then 'BUTUH PERSETUJUAN' else 'DITOLAK' end as nmstatus from sc_trx.approvals_system ) as x";
    var $t_trx_approvals_system_view_column = array('docno','docdate1','docref','doctypename','doctype','nmstatus');
    var $t_trx_approvals_system_view_order = array("docno" => 'desc',"docref" => 'asc'); // default order
    private function _get_query_t_trx_approvals_system()
    {
        /* end untuk filtering view */

        if($this->input->post('tglrange')) {
            $tgl=explode(' - ',$this->input->post('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $this->db->where("attenddate BETWEEN '$tgl1' AND '$tgl2'","", FALSE);
        }

        $this->db->from($this->t_trx_approvals_system_view);
        $i = 0;

        foreach ($this->t_trx_approvals_system_view_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $this->db->or_like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_trx_approvals_system_view_column) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $this->db->order_by($this->t_trx_approvals_system_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_approvals_system_view_order))
        {
            $order = $this->t_trx_approvals_system_view_order;
            foreach ($order as $key => $item){
                $this->db->order_by($key, $item);
            }
        }

    }


    function get_t_trx_approvals_system_view(){
        $this->_get_query_t_trx_approvals_system();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'],$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }


    function t_trx_approvals_system_view_count_filtered()
    {
        $this->_get_query_t_trx_approvals_system();
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function t_trx_approvals_system_view_count_all()
    {
        $this->db->from($this->t_trx_approvals_system_view);
        return $this->db->count_all_results();
    }
    public function get_t_trx_approvals_system_view_by_id($id)
    {
        $this->db->from($this->t_trx_approvals_system_view);
        $this->db->where('docno',$id);
        $query = $this->db->get();
        return $query->row();
    }

}
