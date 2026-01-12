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


class M_Dashboard extends Model
{

    /* JASA PEMBAYARAN */
    var $reminder_jasa_pembayaran_view = "(select a.*,d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,
AGE (to_char(now(),'yyyy-mm-dd')::date, duedate_next) as umur,
(to_char(now(),'yyyy-mm-dd')::date - duedate_next)*-1 as selisih
from sc_mst.jasa_pembayaran a
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup) as x";
    var $reminder_jasa_pembayaran_view_column = array('nmpembayaran','nmsupplier','nmgroup','nmsubgroup');
    var $reminder_jasa_pembayaran_view_order = array("id" => 'desc'); // default order
    private function _get_query_reminder_jasa_pembayaran()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $builder = $this->db->table($this->reminder_jasa_pembayaran_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        $builder->where('selisih <= 25');
        $builder->where("trim(status) != 'CLOSE'");

        $i = 0;
        foreach ($this->reminder_jasa_pembayaran_view_column as $item)
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

                if(count($this->reminder_jasa_pembayaran_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->reminder_jasa_pembayaran_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->reminder_jasa_pembayaran_view_order))
        {
            $order = $this->reminder_jasa_pembayaran_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_reminder_jasa_pembayaran_view(){
        $builder = $this->_get_query_reminder_jasa_pembayaran();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function reminder_jasa_pembayaran_view_count_filtered()
    {
        $builder = $this->_get_query_reminder_jasa_pembayaran();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function reminder_jasa_pembayaran_view_count_all()
    {
        $builder = $this->_get_query_reminder_jasa_pembayaran();
        return $builder->countAllResults();
    }
    public function get_reminder_jasa_pembayaran_view_by_id($id)
    {
        $builder = $this->_get_query_reminder_jasa_pembayaran();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* var $minstock_view = "(select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,b.setminstock,b.minstock,
c.nmlocation from (select idlocation,idbarang,sum(onhand) as onhand,sum(allocated) as allocated,sum(tmpalloca) as tmpalloca,unit from sc_mst.stkgdw group by idlocation,idbarang,unit) as a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation) as x"; query asal*/

    var $minstock_view = "sc_mst.v_stkgdw";
    var $minstock_view_column = array('idbarang','nmbarang');
    var $minstock_view_order = array("idbarang" => 'asc'); // default order
    private function _get_query_minstock()
    {
        $builder = $this->db->table($this->minstock_view);
        $i = 0;

        $builder->where("sisa <= minstock");
        $builder->where("setminstock = 'YES'");
        $builder->where("chold != 'YES'");
        foreach ($this->minstock_view_column as $item)
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

                if(count($this->minstock_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->minstock_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->minstock_view_order))
        {
            $order = $this->minstock_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_minstock_view(){
        $builder = $this->_get_query_minstock();
        ////$this->_get_query_minstock();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function minstock_view_count_filtered()
    {
        $builder = $this->_get_query_minstock();
        ////$this->_get_query_minstock();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function minstock_view_count_all()
    {
        $builder = $this->_get_query_minstock();
        return $builder->countAllResults();
    }
    public function get_minstock_view_by_id($id)
    {
        $builder = $this->_get_query_minstock();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_minstock($param){
        return $this->db->query("select * from 
(select a.*,round(coalesce(a.onhand,0)-(coalesce(a.allocated,0)+coalesce(a.tmpalloca,0)),2) as sisa,b.nmbarang,b.setminstock,b.minstock,
c.nmlocation,d.nmarea from sc_mst.stkgdw a
left outer join sc_mst.mbarang b on a.idbarang=b.idbarang
left outer join sc_mst.mlocation c on a.idlocation=c.idlocation
left outer join sc_mst.marea d on a.idarea=d.idarea) as x where idbarang is not null $param");
    }

}
