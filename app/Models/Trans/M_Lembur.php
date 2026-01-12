<?php
namespace App\Models\Trans;

use CodeIgniter\Model;

class M_Lembur extends Model
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


    function q_lembur($param){
        return $this->db->query("select * from (
                                    select trim(a.nodok) as nodok1,trim(a.nik) as nik1,a.nik,a.nodok,a.tgl_dok,a.kdlembur,a.tgl_kerja,a.tgl_jam_mulai,a.tgl_jam_selesai,a.durasi,a.status,a.kdtrx,a.keterangan,a.input_date,
a.approval_date,a.input_by,a.approval_by,a.delete_by,a.cancel_by,a.update_date,a.delete_date,a.cancel_date,a.update_by,a.durasi_istirahat,a.jenis_lembur,
a.durasijam,a.durasihidup,a.tjlembur,a.nominal,to_char(a.tgl_dok,'dd-mm-yyyy')as tgl_dok1,to_char(a.tgl_kerja,'dd-mm-yyyy')as tgl_kerja1,to_char(a.tgl_kerja,'dd/mm/yyyy')as tgl_kerja2,
                                    b.nmlengkap,b.nikatasan1,b.nikatasan2,b.nikatasan3,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.nmdivision,b.nmgroupgol,b.nmkepegawaian,
                                    b.id_dept,z.uraian as nmstatus,b.plant,b.locaname,b.costcenter,b.nmcostcenter,b.statuskepegawaian,coalesce(a.tjlembur,0) as tjlembur1,coalesce(a.nominal,0) as nominal1,
                                    b.id_gol     
                                    from sc_trx.lembur a
                                    left outer join sc_mst.lv_m_karyawan_trans b on a.nik=b.nik
                                    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='LEMBUR') as x
                                    where nodok is not null $param order by input_date desc
								");
    }



    function q_lembur_tmp($param){
        return $this->db->query("select * from (
                                    select a.nik,a.nodok,a.tgl_dok,a.kdlembur,a.tgl_kerja,a.tgl_jam_mulai,a.tgl_jam_selesai,a.durasi,a.status,a.kdtrx,a.keterangan,a.input_date,
a.approval_date,a.input_by,a.approval_by,a.delete_by,a.cancel_by,a.update_date,a.delete_date,a.cancel_date,a.update_by,a.durasi_istirahat,a.jenis_lembur,
a.durasijam,a.durasihidup,a.tjlembur,a.nominal,to_char(a.tgl_dok,'dd-mm-yyyy')as tgl_dok1,to_char(a.tgl_kerja,'dd-mm-yyyy')as tgl_kerja1,to_char(a.tgl_kerja,'dd/mm/yyyy')as tgl_kerja2,
                                    b.nmlengkap,b.nikatasan1,b.nikatasan2,b.nikatasan3,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.nmdivision,b.nmgroupgol,b.nmkepegawaian,
                                    b.id_dept,z.uraian as nmstatus,b.plant,b.locaname,b.costcenter,b.nmcostcenter,b.statuskepegawaian,coalesce(a.tjlembur,0) as tjlembur1,coalesce(a.nominal,0) as nominal1,
                                    b.id_gol                                           
                                    from sc_tmp.lembur a
                                    left outer join sc_mst.lv_m_karyawan_trans b on a.nik=b.nik
                                    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='LEMBUR') as x
                                    where nodok is not null $param order by nodok desc
								");
    }

    function q_cek_double_upload_lembur($param)
    {
        return $this->db->query("select nik,tgl_kerja,status from (
select nik,tgl_kerja,status from sc_trx.lembur
union all
select nik,tgl_kerja,status from sc_tmp.lembur) as x where nik is not null $param
group by nik,tgl_kerja,status");
    }

    function q_lembur_edit($nodok){
        return $this->db->query("select to_char(a.tgl_dok,'dd-mm-yyyy')as tgl_dok1,
								to_char(a.tgl_kerja,'dd-mm-yyyy')as tgl_kerja1,
								to_char(a.tgl_jam_selesai,'dd-mm-yyyy')as tgl_kerja2,
								cast(to_char(a.tgl_jam_mulai,'HH24:MI:SS')as time)as jam_awal,
								cast(to_char(a.tgl_jam_selesai,'HH24:MI:SS')as time)as jam_akhir,
								a.status, 
								case
								when a.status='P' then 'DISETUJUI'
								when a.status='C' then 'DIBATALKAN'
								when a.status='I' then 'INPUT'
								when a.status='A' then 'PERLU PERSETUJUAN'
								when a.status='D' then 'DIHAPUS'
								end as status1,
								a.*,b.nmlengkap,c.nmdept,d.nmsubdept,e.nmlvljabatan,f.nmjabatan,h.uraian,i.nmlengkap as nmatasan1,
								cast(cast(floor(durasi/60.) as integer)as character(12))|| ' Jam '||
								cast(cast((durasi-(floor(durasi/60.)*60)) as integer)as character(12))||' Menit' as jam	
								from sc_trx.lembur a 
								left outer join sc_mst.karyawan b on a.nik=b.nik
								left outer join sc_mst.departmen c on a.kddept=c.kddept
								left outer join sc_mst.subdepartmen d on a.kdsubdept=d.kdsubdept
								left outer join sc_mst.lvljabatan e on a.kdlvljabatan=e.kdlvl
								left outer join sc_mst.jabatan f on a.kdjabatan=f.kdjabatan
								left outer join sc_mst.trxtype h on a.kdtrx=h.kdtrx and trim(h.jenistrx)='ALASAN LEMBUR'
								left outer join sc_mst.karyawan i on a.nmatasan=i.nik
								where a.nodok='$nodok'
								order by a.nodok desc
								");
    }

    function q_lembur_dtl(){
        return $this->db->query("select to_char(a.tgl_dok,'dd-mm-yyyy')as tgl_dok1,
								to_char(a.tgl_kerja,'dd-mm-yyyy')as tgl_kerja1,
								a.status, 
								case
								when a.status='P' then 'DISETUJUI'
								when a.status='C' then 'DIBATALKAN'
								when a.status='I' then 'INPUT'
								when a.status='A' then 'PERLU PERSETUJUAN'
								when a.status='D' then 'DIHAPUS'
								end as status1,
								a.*,b.nmlengkap,c.nmdept,d.nmsubdept,e.nmlvljabatan,f.nmjabatan,h.uraian,i.nmlengkap as nmatasan1,
								cast(cast(floor(durasi/60.) as integer)as character(12))|| ' Jam '||
								cast(cast((durasi-(floor(durasi/60.)*60)) as integer)as character(12))||' Menit' as jam	
								from sc_trx.lembur a 
								left outer join sc_mst.karyawan b on a.nik=b.nik
								left outer join sc_mst.departmen c on a.kddept=c.kddept
								left outer join sc_mst.subdepartmen d on a.kdsubdept=d.kdsubdept
								left outer join sc_mst.lvljabatan e on a.kdlvljabatan=e.kdlvl
								left outer join sc_mst.jabatan f on a.kdjabatan=f.kdjabatan
								left outer join sc_mst.trxtype h on a.kdtrx=h.kdtrx and trim(h.jenistrx)='ALASAN LEMBUR'
								left outer join sc_mst.karyawan i on a.nmatasan=i.nik
								order by a.nodok desc
								");
    }

    function tr_cancel($nodok,$inputby,$tgl_input){
        return $this->db->query("update sc_trx.lembur set status='C',cancel_by='$inputby',cancel_date='$tgl_input' where nodok='$nodok'");
    }

    function tr_app($nodok,$inputby,$tgl_input){
        return $this->db->query("update sc_trx.lembur set status='F',approval_by='$inputby',approval_date='$tgl_input' where nodok='$nodok'");
    }

    function cek_dokumen($nodok){
        return $this->db->query("select * from sc_trx.lembur where nodok='$nodok' and status='P'");
    }

    function cek_dokumen2($nodok){
        return $this->db->query("select * from sc_trx.lembur where nodok='$nodok' and status='C'");
    }


    function cek_dokumen3($nodok){
        return $this->db->query("select * from sc_trx.lembur where nodok='$nodok' and status<>'D'");
    }


    function cek_position($nik){
        return $this->db->query("select * from sc_mst.karyawan where nik='$nik'");

    }

    function tgl_closing(){
        return $this->db->query("select cast(value1 as date) from sc_mst.option where kdoption='TGLCLS' and trim(nmoption)='TANGGAL CLOSING'");
    }

    function q_cekdouble($nik,$jam_awal1,$jam_selesai1){
        return $this->db->query("select * from sc_trx.lembur
                                    where nik='$nik' and (tgl_jam_mulai>='$jam_selesai1'::timestamp or tgl_jam_selesai>'$jam_awal1'::timestamp)
                                    and (tgl_jam_mulai<'$jam_selesai1'::timestamp or tgl_jam_selesai<='$jam_awal1') and status in ('A','P','I')");
    }

    function q_cekdouble2($nodok,$nik,$jam_awal1,$jam_selesai1){
        return $this->db->query("select * from sc_trx.lembur
                                    where nik='$nik' and (tgl_jam_mulai>='$jam_selesai1'::timestamp or tgl_jam_selesai>'$jam_awal1'::timestamp)
                                    and (tgl_jam_mulai<'$jam_selesai1'::timestamp or tgl_jam_selesai<='$jam_awal1') and status in ('A','P','I') and nodok != '$nodok'");
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

    function q_lembur_resume($param1,$param2)
    {
        return $this->db->query("select a.kddept,a.nmdept,sum(coalesce(b.jml_lembur,0)) as jml_lembur,sum(coalesce(b.ttljam,0)) as ttljam from sc_mst.departmen a
                                    left outer join 
                                    (select b.id_dept,count(a.*) as jml_lembur,sum(durasijam) as ttljam from sc_trx.lembur a
                                    left outer join sc_mst.t_karyawan b on a.nik=b.nik 
                                    where b.id_dept is not null and trim(coalesce(a.status,''))='P' $param2 
                                    group by b.id_dept) b on a.kddept=b.id_dept
                                    group by a.kddept,a.nmdept");
    }

}
