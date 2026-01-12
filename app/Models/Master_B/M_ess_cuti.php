<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_ess_cuti extends Model
{
    function list_karyawan(){
        return $this->db->query("select * from sc_mst.t_karyawan where coalesce(resign,'')='NO'");

    }
    function list_karyawan_param($param){
        return $this->db->query("select * from sc_mst.t_karyawan where coalesce(resign,'')='NO' $param");

    }

    function list_lembur(){
        return $this->db->query("select tplembur from sc_mst.lembur 
								group by tplembur
								order by tplembur asc");

    }

    function list_trxtype(){
        return $this->db->query("select * from sc_mst.trxtype
								where trim(jenistrx)='ALASAN LEMBUR'
								order by kdtrx asc");

    }
    function list_karyawan_index($nik){
        return $this->db->query("select a.*,b.nmdept,c.nmsubdept,d.nmlvljabatan,e.nmjabatan,f.nmlengkap as nmatasan from sc_mst.karyawan a
								left outer join sc_mst.departmen b on a.bag_dept=b.kddept
								left outer join sc_mst.subdepartmen c on  a.bag_dept=c.kddept and a.subbag_dept=c.kdsubdept
								left outer join sc_mst.lvljabatan d on a.lvl_jabatan=d.kdlvl
								left outer join sc_mst.jabatan e on a.bag_dept=e.kddept and a.subbag_dept=e.kdsubdept and a.jabatan=e.kdjabatan
								left outer join sc_mst.karyawan f on a.nik_atasan=f.nik
								where a.nik='$nik'
								");
    }

    function q_mtypecuti($param){
        return $this->db->query("select *,trim(coalesce(idtypecuti,'')) as id from sc_mst.mtypecuti where idtypecuti is not null $param order by nmtypecuti asc");
    }
    function q_abscut($param){
        return $this->db->query("select * from (
                                    select trim(a.docno) as docno1,trim(a.nik) as nik1,a.*,to_char(a.docdate,'dd-mm-yyyy')as docdate1,to_char(a.tglawal,'dd-mm-yyyy')as tglawal1,to_char(a.tglakhir,'dd-mm-yyyy')as tglakhir1,to_char(a.tglawal,'dd/mm/yyyy')as tglawal2,
                                    b.nmlengkap,b.nikatasan1,b.nikatasan2,b.nikatasan3,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.nmdivision,b.nmgroupgol,b.nmkepegawaian,
                                    b.id_gol,z.uraian as nmstatus,z1.uraian as nmstatusapprove,b.id_dept,b.plant,b.locaname,b.costcenter,b.nmcostcenter,b.statuskepegawaian,c.nmtypecuti,trim(c.nmtypecuti) as nmtypecuti1,trim(a.inputby) as inputby1,
                                    x1.nmlengkap as nmapproveby,x2.nmlengkap as nminputby
                                    from sc_trx.abscut a
                                    left outer join sc_mst.lv_m_karyawan_trans b on a.nik=b.nik
                                    left outer join sc_mst.mtypecuti c on a.idtypecuti=c.idtypecuti
                                    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='LEMBUR'
                                    left outer join sc_mst.trxtype z1 on a.statusapprove=z1.kdtrx and z1.jenistrx='LEMBUR'
                                    left outer join sc_mst.t_karyawan x1 on a.approveby=x1.nik
                                    left outer join sc_mst.t_karyawan x2 on a.inputby=x2.nik
                                    ) as x
                                    where docno is not null $param order by docno desc
								");
    }

    function q_abscut_tmp($param){
        return $this->db->query("select * from (
                                    select a.*,to_char(a.docdate,'dd-mm-yyyy')as docdate1,to_char(a.tglawal,'dd-mm-yyyy')as tglawal1,to_char(a.tglakhir,'dd-mm-yyyy')as tglakhir1,to_char(a.tglawal,'dd/mm/yyyy')as tglawal2,
                                    b.nmlengkap,b.nikatasan1,b.nikatasan2,b.nikatasan3,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.nmdivision,b.nmgroupgol,b.nmkepegawaian,
                                    b.id_gol,z.uraian as nmstatus,b.id_dept,b.plant,b.locaname,b.costcenter,b.nmcostcenter,b.statuskepegawaian,c.nmtypecuti
                                    from sc_tmp.abscut a
                                    left outer join sc_mst.lv_m_karyawan_trans b on a.nik=b.nik
                                    left outer join sc_mst.mtypecuti c on a.idtypecuti=c.idtypecuti
                                    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='LEMBUR') as x
                                    where docno is not null $param order by docno desc
								");
    }



    function q_versidb($kodemenu){
        return $this->db->query("select * from sc_mst.version where kodemenu='$kodemenu'");
    }

    function q_trxerror($paramtrxerror){
        return $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null $paramtrxerror");
    }

    function q_deltrxerror($paramtrxerror){
        return $this->db->query("delete from sc_mst.trxerror where userid is not null $paramtrxerror");
    }

    //new

    function q_karyawan($param){
        return $this->db->query("select *,trim(nik) as id from sc_mst.lv_m_karyawan_trans where nik is not null $param");
    }

    function q_cek_tmplembur($param){
        return $this->db->query("select * from sc_tmp.lembur where nodok is not null $param");
    }

    function q_detail_gaji($param){
        return $this->db->query("select * from 
                                    (select a.*,b.nmlengkap,b.nikatasan1,b.nikatasan2,b.nikatasan3,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.nmdivision,b.nmgroupgol,b.nmkepegawaian,
                                    b.plant,b.locaname,b.costcenter,b.nmcostcenter,b.statuskepegawaian from sc_his.detail_gaji a
                                    left outer join sc_mst.lv_m_karyawan_trans b on a.nik=b.nik) as x 
                                    where nik not in (select nik from sc_mst.t_karyawan where coalesce(resign,'') = 'YES') $param
                                    order by nmlengkap");
    }

    /* MASTERING KRITERIA */
    var $t_mtypecuti_view = "sc_mst.mtypecuti";
    var $t_mtypecuti_view_column = array('idtypecuti','nmtypecuti','description','chold','inputby','inputdate');
    var $t_mtypecuti_view_order = array("nmtypecuti" => 'asc'); // default order
    private function _get_query_t_mtypecuti()
    {

        $this->db->from($this->t_mtypecuti_view);
        $i = 0;

        foreach ($this->t_mtypecuti_view_column as $item)
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

                if(count($this->t_mtypecuti_view_column) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $this->db->order_by($this->t_mtypecuti_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_mtypecuti_view_order))
        {
            $order = $this->t_mtypecuti_view_order;
            foreach ($order as $key => $item){
                $this->db->order_by($key, $item);
            }
        }

    }


    function get_t_mtypecuti_view(){
        $this->_get_query_t_mtypecuti();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'],$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }


    function t_mtypecuti_view_count_filtered()
    {
        $this->_get_query_t_mtypecuti();
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function t_mtypecuti_view_count_all()
    {
        $this->db->from($this->t_mtypecuti_view);
        return $this->db->count_all_results();
    }
    public function get_t_mtypecuti_view_by_id($id)
    {
        $this->db->from($this->t_mtypecuti_view);
        $this->db->where('idtypecuti',$id);
        $query = $this->db->get();
        return $query->row();
    }

    function q_abscut_nik_resume($param1,$param2)
    {
        return $this->db->query("select a.nik,a.nmlengkap,sum(coalesce(CUTI,0)) as cuti,sum(coalesce(IZIN,0)) as IZIN,sum(coalesce(SAKIT,0)) as SAKIT,sum(coalesce(DISPENSASI,0)) as DISPENSASI,sum(coalesce(ALPHA,0)) as ALPHA,sum(coalesce(TERLAMBAT,0)) as TERLAMBAT,sum(coalesce(SAKIT_COVID,0)) as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a left outer join (
select a.nik,a.nmlengkap,count(b.*) CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID 
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='CT' $param2
group by a.nik,a.nmlengkap
union all
select a.nik,a.nmlengkap,0 as CUTI,count(a.*) as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='IZ' $param2
group by a.nik,a.nmlengkap
union all
select a.nik,a.nmlengkap,0 as CUTI,0 as IZIN,count(a.*) as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='SK' $param2
group by a.nik,a.nmlengkap
union all
select a.nik,a.nmlengkap,0 as CUTI,0 as IZIN,0 as SAKIT,count(a.*) as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='DI' $param2
group by a.nik,a.nmlengkap
union all
select a.nik,a.nmlengkap,0 as CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,count(a.*) as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='AL' $param2
group by a.nik,a.nmlengkap
union all
select a.nik,a.nmlengkap,0 as CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,count(a.*) as TERLAMBAT,0 as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='DT' $param2
group by a.nik,a.nmlengkap
union all
select a.nik,a.nmlengkap,0 as CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,count(a.*) as SAKIT_COVID
from sc_mst.lv_m_karyawan_trans a 
left outer join sc_trx.abscut b on a.nik=b.nik
left outer join sc_mst.mtypecuti c on b.idtypecuti=c.idtypecuti
where b.status='P' and coalesce(a.resign,'')!='YES' and c.idgroup='SC' $param2
group by a.nik,a.nmlengkap

) as b on a.nik=b.nik
where a.nik in (select nik from sc_mst.t_karyawan where nik is not null $param1)
group by a.nik,a.nmlengkap  ");
    }

}
