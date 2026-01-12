<?php

namespace App\Models\Capital;

use CodeIgniter\Model;

class M_capital_administration extends Model
{
    function q_karyawan($param){
        return $this->db->query("select *,trim(nik) as id from sc_mst.lv_m_karyawan_trans where nik is not null $param");
    }
    function q_departmen($param){
        return $this->db->query("select *,trim(kddept) as id,to_char(now(),'dd-mm-yyyy') as tglcetak from sc_mst.departmen where kddept is not null $param");
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
d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,z.uraian as nmstatus,round(a.estprice_kdlvl) as estprice_kdlvl1 from sc_trx.capital_permintaan a
left outer join sc_mst.lvljabatan b on a.kdlvl=b.kdlvl
left outer join sc_mst.departmen c on a.kddept=c.kddept
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='CIT'
) as x where id is not null $param ");
    }



    /* CAPITAL AUTHORIZATION LEVEL 1 */
    public function qauthorization($param){
        return $this->db->query("select * from sc_mst.capital_authorization where id is not null $param");
    }
    var $capital_pengajuan_level1_view = "(select a.*,b.nmlvljabatan,round(b.pricemin) as pricemin,round(b.pricemax) as pricemax,c.nmdept,
d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,z.uraian as nmstatus from sc_trx.capital_permintaan a
left outer join sc_mst.lvljabatan b on a.kdlvl=b.kdlvl
left outer join sc_mst.departmen c on a.kddept=c.kddept
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='CIT'
) as x";
    var $capital_pengajuan_level1_view_column = array('nmlengkap','nmdept','nmgroup','nmsubgroup','nmlvljabatan');
    var $capital_pengajuan_level1_view_order = array("id" => 'desc'); // default order
    private function _get_query_capital_pengajuan_level1()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();

        $builder = $this->db->table($this->capital_pengajuan_level1_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        if ($cek>0){
            $builder->where(array('kddept' => "$dept",'status'=>'A'));
        } else if ($roleid==='IT') {
            $builder->where(array('status'=>'A'));
        } else {
            $builder->where(array('kddept' => "$dept",'status'=>'A'));
        }

        $i = 0;
        foreach ($this->capital_pengajuan_level1_view_column as $item)
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

                if(count($this->capital_pengajuan_level1_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->capital_pengajuan_level1_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->capital_pengajuan_level1_view_order))
        {
            $order = $this->capital_pengajuan_level1_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_capital_pengajuan_level1_view(){
        $builder = $this->_get_query_capital_pengajuan_level1();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function capital_pengajuan_level1_view_count_filtered()
    {
        $builder = $this->_get_query_capital_pengajuan_level1();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function capital_pengajuan_level1_view_count_all()
    {
        $builder = $this->_get_query_capital_pengajuan_level1();
        return $builder->countAllResults();
    }
    public function get_capital_pengajuan_level1_view_by_id($id)
    {
        $builder = $this->_get_query_capital_pengajuan_level1();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }





    /* ALLL CAPITAL PENGAJUAN PROCESS IT */

    var $capital_pengajuan_all_view = "(select a.*,b.nmlvljabatan,round(b.pricemin) as pricemin,round(b.pricemax) as pricemax,c.nmdept,
d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,z.uraian as nmstatus from sc_trx.capital_permintaan a
left outer join sc_mst.lvljabatan b on a.kdlvl=b.kdlvl
left outer join sc_mst.departmen c on a.kddept=c.kddept
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='CIT'
) as x";
    var $capital_pengajuan_all_view_column = array('nmlengkap','nmdept','nmgroup','nmsubgroup','nmlvljabatan');
    var $capital_pengajuan_all_view_order = array("id" => 'desc'); // default order
    private function _get_query_capital_pengajuan_all()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        //$cek = $this->qauthorization(" and username='$nama' and level_1='YES'")->getNumRows();
        $builder = $this->db->table($this->capital_pengajuan_all_view);
        //$builder->where(array('grouptype ' => 'CIT'));

        if ($roleid==='IT') {
            //$builder->where(array('status'=>'B'));
        } else {
            $builder->where(array('kddept' => "$dept",'status'=>'B'));
        }

          if($this->request->getPost('status')) {
          $ket = trim($this->request->getPost('status'));
              $builder->where("status ='$ket'");
          }
          if($this->request->getPost('kddept')) {
              $ket = trim($this->request->getPost('kddept'));
              $builder->where("kddept ='$ket'");
          }
          if($this->request->getPost('idgroup')) {
              $ket = trim($this->request->getPost('idgroup'));
              $builder->where("idgroup ='$ket'");
          }
          if($this->request->getPost('idsubgroup')) {
              $ket = trim($this->request->getPost('idsubgroup'));
              $builder->where("idsubgroup ='$ket'");
          }


        $i = 0;
        foreach ($this->capital_pengajuan_all_view_column as $item)
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

                if(count($this->capital_pengajuan_all_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->capital_pengajuan_all_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->capital_pengajuan_all_view_order))
        {
            $order = $this->capital_pengajuan_all_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_capital_pengajuan_all_view(){
        $builder = $this->_get_query_capital_pengajuan_all();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function capital_pengajuan_all_view_count_filtered()
    {
        $builder = $this->_get_query_capital_pengajuan_all();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function capital_pengajuan_all_view_count_all()
    {
        $builder = $this->_get_query_capital_pengajuan_all();
        return $builder->countAllResults();
    }
    public function get_capital_pengajuan_all_view_by_id($id)
    {
        $builder = $this->_get_query_capital_pengajuan_all();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    /* JASA PEMBAYARAN */
    var $jasa_pembayaran_view = "(select a.*,d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,(duedate_next - CURRENT_DATE) AS selisih_hari from sc_mst.jasa_pembayaran a
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup) as x";
    var $jasa_pembayaran_view_column = array('nmpembayaran','nmsupplier','nmgroup','nmsubgroup');
    var $jasa_pembayaran_view_order = array("id" => 'desc'); // default order
    private function _get_query_jasa_pembayaran()
    {
        $this->session = \Config\Services::session();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $builder = $this->db->table($this->jasa_pembayaran_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        //$builder->where(array('status is not null'));

        $i = 0;
        foreach ($this->jasa_pembayaran_view_column as $item)
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

                if(count($this->jasa_pembayaran_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->jasa_pembayaran_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->jasa_pembayaran_view_order))
        {
            $order = $this->jasa_pembayaran_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_jasa_pembayaran_view(){
        $builder = $this->_get_query_jasa_pembayaran();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function jasa_pembayaran_view_count_filtered()
    {
        $builder = $this->_get_query_jasa_pembayaran();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function jasa_pembayaran_view_count_all()
    {
        $builder = $this->_get_query_jasa_pembayaran();
        return $builder->countAllResults();
    }
    public function get_jasa_pembayaran_view_by_id($id)
    {
        $builder = $this->_get_query_jasa_pembayaran();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    public function q_jasa_pembayaran($param){
        return $this->db->query("select * from (select a.*,d.nmgroup, d1.nmsubgroup,to_char(docdate,'dd-mm-yyyy') as docdate1,replace(coalesce(ttlnetto,0)::text,'.',',') as ttlnetto1 from sc_mst.jasa_pembayaran a
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup) as x where id is not null $param");
    }

    function q_jasa_pembayaran_detail($param){
        return $this->db->query("select * from (select aa.*,a.noassetit,a.nmpembayaran,a.nmpengguna,a.nmsupplier,a.description,d.nmgroup, d1.nmsubgroup,to_char(aa.duedate_in,'dd-mm-yyyy') as duedate_in1,to_char(aa.duedate_next,'dd-mm-yyyy') as duedate_next1,to_char(aa.invoice_date,'dd-mm-yyyy') as invoice_date1 from sc_trx.jasa_pembayaran_detail aa
left outer join sc_mst.jasa_pembayaran a on aa.idrev=a.id			   
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup) as x where id is not null $param
order by id desc");
    }



    /* JASA PEMBAYARAN DETAIL*/
    var $jasa_pembayaran_detail_view = "(select aa.*,a.noassetit,a.nmpembayaran,a.nmpengguna,a.nmsupplier,a.description,d.nmgroup, d1.nmsubgroup,to_char(aa.duedate_in,'dd-mm-yyyy') as duedate_in1,to_char(aa.duedate_next,'dd-mm-yyyy') as duedate_next1,to_char(aa.invoice_date,'dd-mm-yyyy') as invoice_date1,to_char(aa.docdate,'dd-mm-yyyy') as docdate1,to_char(aa.duedate_next,'dd-mm-yyyy') as duedate_next1 from sc_trx.jasa_pembayaran_detail aa
left outer join sc_mst.jasa_pembayaran a on aa.idrev=a.id			   
left outer join sc_mst.mgroup d on a.idgroup=d.idgroup
left outer join sc_mst.msubgroup d1 on a.idgroup=d1.idgroup and a.idsubgroup=d1.idsubgroup) as x";
    var $jasa_pembayaran_detail_view_column = array('nmpembayaran','nmsupplier','nmgroup','nmsubgroup');
    var $jasa_pembayaran_detail_view_order = array("id" => 'desc'); // default order
    private function _get_query_jasa_pembayaran_detail()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $roleid=trim($this->session->get('roleid'));
        $nama=trim($this->session->get('nama'));
        $dept=trim($this->session->get('kddept'));

        $builder = $this->db->table($this->jasa_pembayaran_detail_view);
        //$builder->where(array('grouptype ' => 'CIT'));
        //$builder->where(array('status is not null'));

        if($this->request->getPost('idrev_x')) {
            $idrev = trim($this->request->getPost('idrev_x'));
            $builder->where("idrev ='$idrev'");
        }
        $i = 0;
        foreach ($this->jasa_pembayaran_detail_view_column as $item)
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

                if(count($this->jasa_pembayaran_detail_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->jasa_pembayaran_detail_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->jasa_pembayaran_detail_view_order))
        {
            $order = $this->jasa_pembayaran_detail_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_jasa_pembayaran_detail_view(){
        $builder = $this->_get_query_jasa_pembayaran_detail();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function jasa_pembayaran_detail_view_count_filtered()
    {
        $builder = $this->_get_query_jasa_pembayaran_detail();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function jasa_pembayaran_detail_view_count_all()
    {
        $builder = $this->_get_query_jasa_pembayaran_detail();
        return $builder->countAllResults();
    }
    public function get_jasa_pembayaran_detail_view_by_id($id)
    {
        $builder = $this->_get_query_jasa_pembayaran_detail();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

}
