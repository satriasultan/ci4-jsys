<?php
namespace App\Models\Trans;

use CodeIgniter\Model;

class M_Koreksi extends Model
{
    function list_karyawan(){
        return $this->db->query("select a.*,b.nmdept from sc_mst.karyawan a
								left outer join sc_mst.departmen b on a.bag_dept=b.kddept where coalesce(upper(a.status),'')!='KO' 
								order by nmlengkap asc");

    }

    function list_kcb(){
        return $this->db->query("select a.*,b.nmlengkap from sc_trx.koreksicb a 
								 left outer join sc_mst.karyawan b on a.nik=b.nik where a.doctype='Y'");
    }

    function list_kks(){
        return $this->db->query("select a.*,b.nmlengkap,b.nmlengkap,b.tglmasukkerja,to_char(cast(to_char(b.tglmasukkerja,to_char(input_date,'yyyy')||'-mm-dd')as date)+interval '2 months','yyyy-mm-dd') as tglhgscuti ,to_char(a.tgl_dok,'yyyy-mm-dd') as tgl_dok2
								from sc_trx.koreksicb a 
								left outer join sc_mst.karyawan b on a.nik=b.nik where a.doctype='X' and a.status='P'
							    ");
    }
    function list_kkstmp(){
        return $this->db->query("select a.*,b.nmlengkap,b.nmlengkap,b.tglmasukkerja,to_char(cast(to_char(b.tglmasukkerja,to_char(input_date,'yyyy')||'-mm-dd')as date)+interval '2 months','yyyy-mm-dd') as tglhgscuti,to_char(a.tgl_dok,'yyyy-mm-dd') as tgl_dok2 
								from sc_tmp.koreksicb a 
								left outer join sc_mst.karyawan b on a.nik=b.nik where a.doctype='X' and a.status='I'
							    ");
    }

    function cek_kks($nodok,$nik,$jumlahcuti){
        return $this->db->query("select a.*,b.nmlengkap,b.tglmasukkerja,to_char(cast(to_char(b.tglmasukkerja,to_char(input_date,'yyyy')||'-mm-dd')as date)+interval '2 months','yyyy-mm-dd') as tglhgscuti,to_char(a.tgl_dok,'dd-mm-yyyy') as tgl_dok2  from sc_tmp.koreksicb a 
								 left outer join sc_mst.karyawan b on a.nik=b.nik where a.doctype='X' and a.nodok='$nodok' and a.nik='$nik' and jumlahcuti='$jumlahcuti'");
    }


    function cek_kcb($nodok,$nik,$jumlahcuti){
        return $this->db->query("select a.*,b.nmlengkap from sc_tmp.koreksicb a
								left outer join sc_mst.karyawan b on a.nik=b.nik where a.nodok='$nodok' and a.nik='$nik' and a.jumlahcuti='$jumlahcuti'");
    }

    function list_cb(){
        return $this->db->query("select * from sc_trx.cutibersama where status='P'");
    }

    function q_koreksikhusus($tahun,$dept){
        return $this->db->query("
									select x.*,a.nmlengkap,a.bag_dept,a.subbag_dept from sc_mst.karyawan a
										join(
										select a.nik,a.tanggal,a.no_dokumen,a.in_cuti,a.out_cuti,a.sisacuti,a.doctype,a.status from sc_trx.cuti_lalu a,
										(select a.nik,a.tanggal,a.no_dokumen,max(a.doctype) as doctype from sc_trx.cuti_lalu a,
										(select a.nik,a.tanggal,max(a.no_dokumen) as no_dokumen from sc_trx.cuti_lalu a,
										(select nik,max(tanggal) as tanggal from sc_trx.cuti_lalu where to_char(tanggal,'yyyy')='$tahun'
										group by nik) as b
										where a.nik=b.nik and a.tanggal=b.tanggal
										group by a.nik,a.tanggal) b
										where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen
										group by a.nik,a.tanggal,a.no_dokumen) b
										where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen and a.doctype=b.doctype) x
										on a.nik=x.nik where to_char(tanggal,'yyyy')='$tahun' and bag_dept $dept and coalesce(upper(a.status),'')!='KO'
										/* trim(coalesce,'')<>'N'*/

								");
    }

    function q_departmen(){
        return $this->db->query("select * from sc_mst.departmen");
    }

    function q_koreksicuti_tmp_mst($param)
    {
        return $this->db->query("select *,to_char(docdate,'dd-mm-yyyy') as docdate1 from sc_tmp.koreksi_cuti_mst where docno is not null $param");
    }

    function q_koreksicuti_tmp_dtl($param)
    {
        return $this->db->query("select * from sc_tmp.koreksi_cuti_dtl where docno is not null $param");
    }

    function q_koreksicuti_trx_mst($param)
    {
        return $this->db->query("select *,to_char(docdate,'dd-mm-yyyy') as docdate1 from sc_trx.koreksi_cuti_mst where docno is not null $param");
    }

    function q_koreksicuti_trx_dtl($param)
    {
        return $this->db->query("select * from sc_trx.koreksi_cuti_dtl where docno is not null $param");
    }
    /* START KOREKSI CUTI TRX */
    var $koreksi_cuti_mst_view = "(select *,case when trim(doctype)='PLUS' then '+ PENAMBAHAN' 
when trim(doctype)='MIN' then '- PENGURANGAN' else '' end as doctype1, to_char(docdate,'dd-mm-yyyy') as docdate1,z.uraian as nmstatus 
from sc_trx.koreksi_cuti_mst a
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.T.C.5') as x";
    var $koreksi_cuti_mst_view_column = array('docno','docref','doctype','docname','status','qty','countdtl','description','inputby');
    var $koreksi_cuti_mst_view_order = array("docno" => 'desc',"docdate" => 'desc'); // default order
    private function _get_query_koreksi_cuti_mst()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $builder = $this->db->table($this->koreksi_cuti_mst_view);

        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("docdate BETWEEN '$tgl1' AND '$tgl2'");
        }
        $nama = trim($this->session->get('nama'));
        //// $this->db->where(array('docno =' => $nama));

        $i = 0;

        foreach ($this->koreksi_cuti_mst_view_column as $item)
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

                if(count($this->koreksi_cuti_mst_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->koreksi_cuti_mst_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->koreksi_cuti_mst_view_order))
        {
            $order = $this->koreksi_cuti_mst_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_koreksi_cuti_mst_view(){
        $builder = $this->_get_query_koreksi_cuti_mst();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function koreksi_cuti_mst_view_count_filtered()
    {
        $builder = $this->_get_query_koreksi_cuti_mst();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function koreksi_cuti_mst_view_count_all()
    {
        $builder = $this->_get_query_koreksi_cuti_mst();
        return $builder->countAllResults();
    }
    public function get_koreksi_cuti_mst_view_by_id($id)
    {
        $builder = $this->_get_query_koreksi_cuti_mst();
        $builder->where('docno',$id);
        $query = $builder->get();
        return $query->getRow();
    }
    /* END KOREKSI CUTI  TRX */

    /* NIK DETAIL TEMPORARY */
    var $t_tmp_koreksi_cuti_dtl_view = "(select a.*,case when trim(a.doctype)='PLUS' then '+ PENAMBAHAN' 
    when trim(a.doctype)='MIN' then '- PENGURANGAN' else '' end as doctype1,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.locaname,b.plant from sc_tmp.koreksi_cuti_dtl a
left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik) as x";
    var $t_tmp_koreksi_cuti_dtl_view_column = array('docno','docref','doctype','docname','docdate','qty','status','description','nik','nmlengkap','nmdept','nmsubdept','nmjabatan','locaname');
    var $t_tmp_koreksi_cuti_dtl_view_order = array("docno" => 'asc',"id" => 'asc'); // default order
    private function _get_query_t_tmp_koreksi_cuti_dtl()
    {

        $builder = $this->db->table($this->t_tmp_koreksi_cuti_dtl_view);
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $nama = trim($this->session->get('nama'));
        $builder->where(array('docno =' => $nama));

        $i = 0;

        foreach ($this->t_tmp_koreksi_cuti_dtl_view_column as $item)
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

                if(count($this->t_tmp_koreksi_cuti_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_tmp_koreksi_cuti_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_tmp_koreksi_cuti_dtl_view_order))
        {
            $order = $this->t_tmp_koreksi_cuti_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }

        return $builder;
    }


    function get_t_tmp_koreksi_cuti_dtl_view(){
        $builder = $this->_get_query_t_tmp_koreksi_cuti_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_tmp_koreksi_cuti_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_tmp_koreksi_cuti_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_tmp_koreksi_cuti_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_tmp_koreksi_cuti_dtl();
        return $builder->countAllResults();
    }
    public function get_t_tmp_itemtrans_dtl_view_by_id($id)
    {
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();

        $nama = trim($this->session->get('nama'));
        $builder = $this->db->table($this->t_tmp_koreksi_cuti_dtl_view);
        $builder->where('docno',$nama);
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }


    /* TRX KOREKSI */
    var $t_trx_koreksi_cuti_dtl_view = "(select a.*,case when trim(a.doctype)='PLUS' then '+ PENAMBAHAN' 
    when trim(a.doctype)='MIN' then '- PENGURANGAN' else '' end as doctype1,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,b.locaname,b.plant from sc_trx.koreksi_cuti_dtl a
left outer join sc_mst.lv_m_karyawan b
on a.nik=b.nik) as x";
    var $t_trx_koreksi_cuti_dtl_view_column = array('docno','docref','doctype','docname','docdate','qty','status','description','nik','nmlengkap','nmdept','nmsubdept','nmjabatan','locaname');
    var $t_trx_koreksi_cuti_dtl_view_order = array("docno" => 'asc',"id" => 'asc'); // default order
    private function _get_query_t_trx_koreksi_cuti_dtl()
    {

        $this->request = \Config\Services::request();
        $builder = $this->db->table($this->t_trx_koreksi_cuti_dtl_view);
        $nama = trim($this->request->getPost('docno'));
        $builder->where(array('docno =' => $nama));


        $i = 0;

        foreach ($this->t_trx_koreksi_cuti_dtl_view_column as $item)
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

                if(count($this->t_trx_koreksi_cuti_dtl_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_trx_koreksi_cuti_dtl_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_trx_koreksi_cuti_dtl_view_order))
        {
            $order = $this->t_trx_koreksi_cuti_dtl_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_trx_koreksi_cuti_dtl_view(){
        $builder = $this->_get_query_t_trx_koreksi_cuti_dtl();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    function t_trx_koreksi_cuti_dtl_view_count_filtered()
    {
        $builder = $this->_get_query_t_trx_koreksi_cuti_dtl();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_trx_koreksi_cuti_dtl_view_count_all()
    {
        $builder = $this->_get_query_t_trx_koreksi_cuti_dtl();
        return $builder->countAllResults();
    }
    /* TRX KOREKSI */

}
