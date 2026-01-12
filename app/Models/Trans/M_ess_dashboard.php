<?php
namespace App\Models\Trans;

use CodeIgniter\Model;

class M_ess_dashboard extends Model
{

    function list_karyawan(){
        return $this->db->query("select * from sc_mst.t_karyawan where coalesce(resign,'')='NO'");

    }

    function q_karyawan_base64($param){
        return $this->db->query("select *,encode(nik::bytea, 'hex') as id from sc_mst.lv_m_karyawan_trans where nik is not null $param");
    }

    function q_mst_libur($param)
    {
        return $this->db->query("select * from sc_mst.libur where id_libur is not null $param order by tgl_libur asc");
    }

    function q_transready_calendar($param)
    {
        return $this->db->query("select nik,tgl,kdjamkerja,kdregu,title,colourcode from (
select nik,tgl,kdjamkerja,kdregu,jam_masuk_absen::text as title,'#00a65a' as colourcode from sc_trx.transready where jam_masuk_absen is not null
union all
select nik,tgl,kdjamkerja,kdregu,jam_pulang_absen::text as title,'#0073b7' as colourcode from sc_trx.transready where jam_pulang_absen is not null
union all
select nik,tgl,kdjamkerja,kdregu,'TERLAMBAT'::text as title,'#ff851b' as colourcode from sc_trx.transready where coalesce(idgroupcuti,'')='DT'
union all
select nik,tgl,kdjamkerja,kdregu,'IZIN'::text as title,'#f39c12' as colourcode from sc_trx.transready where coalesce(idgroupcuti,'')='IZ'
union all
select nik,tgl,kdjamkerja,kdregu,'CUTI'::text as title,'#001f3f' as colourcode from sc_trx.transready where coalesce(idgroupcuti,'')='CT'
union all
select nik,tgl,kdjamkerja,kdregu,'SAKIT'::text as title,'#605ca8' as colourcode from sc_trx.transready where coalesce(idgroupcuti,'')='SK'
union all
select nik,tgl,kdjamkerja,kdregu,'ALPHA'::text as title,'#dd4b39' as colourcode from sc_trx.transready where coalesce(idgroupcuti,'')='AL'
) as x
where nik is not null $param order by tgl asc");
    }


}
