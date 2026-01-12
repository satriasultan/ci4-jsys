<?php
namespace App\Models\Trans;

use CodeIgniter\Model;

class M_Abscut extends Model
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

        $builder = $this->db->table($this->t_mtypecuti_view);
        $i = 0;

        foreach ($this->t_mtypecuti_view_column as $item)
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

                if(count($this->t_mtypecuti_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_mtypecuti_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_mtypecuti_view_order))
        {
            $order = $this->t_mtypecuti_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }

        return $builder;

    }


    function get_t_mtypecuti_view(){
        $builder = $this->db->table($this->t_mtypecuti_view);
        $this->_get_query_t_mtypecuti();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_mtypecuti_view_count_filtered()
    {
        $builder = $this->db->table($this->t_mtypecuti_view);
        $this->_get_query_t_mtypecuti();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_mtypecuti_view_count_all()
    {
        $builder = $this->db->table($this->t_mtypecuti_view);
        return $builder->countAllResults();
    }
    public function get_t_mtypecuti_view_by_id($id)
    {

        $builder = $this->db->table($this->t_mtypecuti_view);
        $builder->where('idtypecuti',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function q_abscut_resume($param1,$param2)
    {
        return $this->db->query("
select a.kddept,a.nmdept,sum(coalesce(CUTI,0)) as cuti,sum(coalesce(IZIN,0)) as IZIN,sum(coalesce(SAKIT,0)) as SAKIT,sum(coalesce(DISPENSASI,0)) as DISPENSASI,sum(coalesce(ALPHA,0)) as ALPHA,sum(coalesce(TERLAMBAT,0)) as TERLAMBAT,sum(coalesce(SAKIT_COVID,0)) as SAKIT_COVID from sc_mst.departmen a left outer join (
select nmdept,id_dept,sum(CUTI) as cuti,sum(IZIN) as IZIN,sum(SAKIT) as SAKIT,sum(DISPENSASI) as DISPENSASI,sum(ALPHA) as ALPHA,sum(TERLAMBAT) as TERLAMBAT,sum(SAKIT_COVID) as SAKIT_COVID from (
select c.nmdept,c.id_dept,count(a.*) CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='CT' $param2
group by c.nmdept,c.id_dept
union all
select c.nmdept,c.id_dept,0 as CUTI,count(a.*) as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='IZ' $param2
group by c.nmdept,c.id_dept
union all
select c.nmdept,c.id_dept,0 as CUTI,0 as IZIN,count(a.*) as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='SK' $param2
group by c.nmdept,c.id_dept
union all
select c.nmdept,c.id_dept,0 as CUTI,0 as IZIN,0 as SAKIT,count(a.*) as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='DI' $param2
group by c.nmdept,c.id_dept
union all
select c.nmdept,c.id_dept,0 as CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,count(a.*) as ALPHA,0 as TERLAMBAT,0 as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='AL' $param2
group by c.nmdept,c.id_dept
union all
select c.nmdept,c.id_dept,0 as CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,count(a.*) as TERLAMBAT,0 as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='DT' $param2
group by c.nmdept,c.id_dept
union all
select c.nmdept,c.id_dept,0 as CUTI,0 as IZIN,0 as SAKIT,0 as DISPENSASI,0 as ALPHA,0 as TERLAMBAT,count(a.*) as SAKIT_COVID from sc_trx.abscut a
left outer join sc_mst.mtypecuti b on a.idtypecuti=b.idtypecuti
left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik
where a.status='P' and b.idgroup='SC' $param2
group by c.nmdept,c.id_dept ) as x group by nmdept,id_dept ) as b on a.kddept=b.id_dept
where kddept is not null $param1
group by a.nmdept,a.kddept");
    }


    function q_departmen(){
        return $this->db->query("select * from sc_mst.departmen");
    }

    function q_cutiblc($tahun,$dept,$niknya){
        return $this->db->query("
									select x.*,a.nmlengkap,a.id_dept,a.id_subdept,a.nmdept,a.nmsubdept,a.nmjabatan from sc_mst.lv_m_karyawan_trans a
										left outer join(
										select a.nik,a.tanggal,a.no_dokumen,a.in_cuti,a.out_cuti,a.sisacuti,a.doctype,a.status from sc_trx.cuti_blc a,
										(select a.nik,a.tanggal,a.no_dokumen,max(a.doctype) as doctype from sc_trx.cuti_blc a,
										(select a.nik,a.tanggal,max(a.no_dokumen) as no_dokumen from sc_trx.cuti_blc a,
										(select nik,max(tanggal) as tanggal from sc_trx.cuti_blc where to_char(tanggal,'yyyy')='$tahun'
										group by nik) as b
										where a.nik=b.nik and a.tanggal=b.tanggal
										group by a.nik,a.tanggal) b
										where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen
										group by a.nik,a.tanggal,a.no_dokumen) b
										where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen and a.doctype=b.doctype) x on a.nik=x.nik 
										where to_char(tanggal,'yyyy')='$tahun' and coalesce(upper(a.resign),'')!='YES' $dept $niknya
										/* trim(coalesce,'')<>'N'*/

								");
    }

    function q_cutiblcdtl($nik,$tahun){
        //return $this->db->query("select * from sc_trx.cuti_blc where nik='$nik' and  to_char(tanggal,'yyyy')='$tahun' order by tanggal asc");
        return $this->db->query("select x.nik,x.nmlengkap,x.tanggal,x.no_dokumen,x.in_cuti,x.out_cuti,x.sisacuti,x.doctype,x.status,y.tgl_mulai,y.tgl_selesai,x.description
from (
	select a.nik,a.nmlengkap,(select cast(trim ('$tahun')||'-01-01' as timestamp) - INTERVAL '1 DAY') as tanggal,'SALDO' as no_dokumen,
	coalesce(b.sisacuti,0) as in_cuti,0 as out_cuti,coalesce(b.sisacuti,0) as sisacuti,'SLD' as doctype,'SALDO LALU' as status,b.description
	from sc_mst.karyawan a
	left outer join (
		select a.nik,coalesce(sisacuti,0) as sisacuti,a.tanggal,a.description from sc_trx.cuti_blc a,
		(select a.nik,a.tanggal,a.no_dokumen,max(a.doctype) as doctype from sc_trx.cuti_blc a,
		(select a.nik,a.tanggal,max(a.no_dokumen) as no_dokumen from sc_trx.cuti_blc a,
		(select nik,max(tanggal) as tanggal from sc_trx.cuti_blc where to_char(tanggal,'YYYY')<'$tahun'
		group by nik) as b
		where a.nik=b.nik and a.tanggal=b.tanggal
		group by a.nik,a.tanggal) b
		where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen
		group by a.nik,a.tanggal,a.no_dokumen) b
		where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen and a.doctype=b.doctype) b
		 on a.nik=b.nik
	where a.nik='$nik'
	union all
	select a.nik,b.nmlengkap,a.tanggal,a.no_dokumen,a.in_cuti,a.out_cuti,a.sisacuti,a.doctype,a.status,a.description from sc_trx.cuti_blc a, sc_mst.t_karyawan b where a.nik=b.nik and to_char(a.tanggal,'YYYY')='$tahun'
	and a.nik='$nik' and coalesce(upper(b.resign),'NO')!='YES'
) as x
left outer join (
select nik,docno as nodok,tglawal as tgl_mulai,tglakhir as tgl_selesai from sc_trx.abscut 
union all
select a.nik,b.nodok,to_char(tgl_awal,'yyyy-mm-dd')::date as tgl_mulai,to_char(tgl_akhir,'yyyy-mm-dd')::date as tgl_selesai from sc_tmp.cuti_blc a 
left outer join sc_trx.cutibersama b on  a.no_dokumen=b.nodok ) as y on x.nik=y.nik and x.no_dokumen=y.nodok
where x.nik='$nik'
order by x.nik,x.tanggal desc,x.no_dokumen desc
");
    }

    function excel_cutiblcdtl($nik,$tahun){
        return $this->db->query("select x.nik,x.nmlengkap,x.tanggal,x.no_dokumen,x.in_cuti,x.out_cuti,x.sisacuti,x.doctype,x.status,y.tgl_mulai,y.tgl_selesai,x.description
from (
	select a.nik,a.nmlengkap,(select cast(trim ('$tahun')||'-01-01' as timestamp) - INTERVAL '1 DAY') as tanggal,'SALDO' as no_dokumen,
	coalesce(b.sisacuti,0) as in_cuti,0 as out_cuti,coalesce(b.sisacuti,0) as sisacuti,'SLD' as doctype,'SALDO LALU' as status,b.description
	from sc_mst.karyawan a
	left outer join (
		select a.nik,coalesce(sisacuti,0) as sisacuti,a.tanggal,a.description from sc_trx.cuti_blc a,
		(select a.nik,a.tanggal,a.no_dokumen,max(a.doctype) as doctype from sc_trx.cuti_blc a,
		(select a.nik,a.tanggal,max(a.no_dokumen) as no_dokumen from sc_trx.cuti_blc a,
		(select nik,max(tanggal) as tanggal from sc_trx.cuti_blc where to_char(tanggal,'YYYY')<'$tahun'
		group by nik) as b
		where a.nik=b.nik and a.tanggal=b.tanggal
		group by a.nik,a.tanggal) b
		where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen
		group by a.nik,a.tanggal,a.no_dokumen) b
		where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen and a.doctype=b.doctype) b
		 on a.nik=b.nik
	where a.nik='$nik'
	union all
	select a.nik,b.nmlengkap,a.tanggal,a.no_dokumen,a.in_cuti,a.out_cuti,a.sisacuti,a.doctype,a.status,a.description from sc_trx.cuti_blc a, sc_mst.t_karyawan b where a.nik=b.nik and to_char(a.tanggal,'YYYY')='$tahun'
	and a.nik='$nik' and coalesce(upper(b.resign),'NO')!='YES'
) as x
left outer join (
select nik,docno as nodok,tglawal as tgl_mulai,tglakhir as tgl_selesai from sc_trx.abscut 
union all
select a.nik,b.nodok,to_char(tgl_awal,'yyyy-mm-dd')::date as tgl_mulai,to_char(tgl_akhir,'yyyy-mm-dd')::date as tgl_selesai from sc_tmp.cuti_blc a 
left outer join sc_trx.cutibersama b on  a.no_dokumen=b.nodok ) as y on x.nik=y.nik and x.no_dokumen=y.nodok
where x.nik='$nik'
order by x.nik,x.tanggal desc,x.no_dokumen desc");
    }

    function q_proc_htg($nikht){
        return $this->db->query("select sc_trx.pr_rekap_cutiblc('$nikht')");
    }

    function q_hitungallcuti(){
        return $this->db->query("select sc_trx.pr_rekap_cutiblcall()");
    }

    function excel_cutiblc($tahun){
        return $this->db->query("
					            select x.*,a.nmlengkap,a.id_dept,a.id_subdept,a.nmdept,a.nmsubdept,a.nmjabatan from sc_mst.lv_m_karyawan_trans a
										left outer join(
										select a.nik,a.tanggal,a.no_dokumen,a.in_cuti,a.out_cuti,a.sisacuti,a.doctype,a.status from sc_trx.cuti_blc a,
										(select a.nik,a.tanggal,a.no_dokumen,max(a.doctype) as doctype from sc_trx.cuti_blc a,
										(select a.nik,a.tanggal,max(a.no_dokumen) as no_dokumen from sc_trx.cuti_blc a,
										(select nik,max(tanggal) as tanggal from sc_trx.cuti_blc where to_char(tanggal,'yyyy')='$tahun'
										group by nik) as b
										where a.nik=b.nik and a.tanggal=b.tanggal
										group by a.nik,a.tanggal) b
										where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen
										group by a.nik,a.tanggal,a.no_dokumen) b
										where a.nik=b.nik and a.tanggal=b.tanggal and a.no_dokumen=b.no_dokumen and a.doctype=b.doctype) x on a.nik=x.nik 
										where to_char(tanggal,'yyyy')='$tahun' and coalesce(upper(a.resign),'')!='YES'
										
								");
    }

}
