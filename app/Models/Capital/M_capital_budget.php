<?php

namespace App\Models\Capital;

use CodeIgniter\Model;

class M_capital_budget extends Model
{
    function q_karyawan($param){
        return $this->db->query("select *,trim(nik) as id from sc_mst.lv_m_karyawan_trans where nik is not null $param");
    }
    function q_departmen($param){
        return $this->db->query("select *,trim(kddept) as id from sc_mst.departmen where kddept is not null $param");
    }

    /* CAPITAL AUTHORIZATION */
    var $capital_pengajuan_view = "(select a.*,b.nmlvljabatan,round(b.pricemin) as pricemin,round(b.pricemax) as pricemax,c.nmdept,
d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,z.uraian as nmstatus from sc_trx.capital_permintaan a
left outer join sc_mst.lvljabatan b on a.kdlvl=b.kdlvl
left outer join sc_mst.departmen c on a.kddept=c.kddept
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='CIT'
) as x";
    var $capital_pengajuan_view_column = array('nmlengkap','nmdept','nmgroup','nmsubgroup','nmlvljabatan');
    var $capital_pengajuan_view_order = array("id" => 'desc'); // default order
    private function _get_query_capital_pengajuan()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();


        $builder = $this->db->table($this->capital_pengajuan_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        if ($roleid==='IT') {
                //$builder->where(array('status is not null'));
        } else {
            $builder->where(array('kddept' => "$dept"));
        }

        $i = 0;
        foreach ($this->capital_pengajuan_view_column as $item)
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

                if(count($this->capital_pengajuan_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->capital_pengajuan_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->capital_pengajuan_view_order))
        {
            $order = $this->capital_pengajuan_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_capital_pengajuan_view(){
        $builder = $this->_get_query_capital_pengajuan();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function capital_pengajuan_view_count_filtered()
    {
        $builder = $this->_get_query_capital_pengajuan();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function capital_pengajuan_view_count_all()
    {
        $builder = $this->_get_query_capital_pengajuan();
        return $builder->countAllResults();
    }
    public function get_capital_pengajuan_view_by_id($id)
    {
        $builder = $this->_get_query_capital_pengajuan();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_capital_pengajuan($param){
        return $this->db->query("select * from 
(select a.*,b.nmlvljabatan,round(b.pricemin) as pricemin,round(b.pricemax) as pricemax,c.nmdept,
d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,z.uraian as nmstatus from sc_trx.capital_permintaan a
left outer join sc_mst.lvljabatan b on a.kdlvl=b.kdlvl
left outer join sc_mst.departmen c on a.kddept=c.kddept
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='CIT'
) as x where id is not null $param ");
    }








}
