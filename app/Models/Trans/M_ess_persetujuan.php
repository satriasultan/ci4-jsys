<?php
namespace App\Models\Trans;

use CodeIgniter\Model;

class M_ess_persetujuan extends Model
{

    function list_karyawan(){
        return $this->db->query("select * from sc_mst.t_karyawan where coalesce(resign,'')='NO'");

    }

    function q_mst_libur($param)
    {
        return $this->db->query("select * from sc_mst.libur where id_libur is not null $param order by tgl_libur asc");
    }

    function q_trx_approvals_system($param)
    {
        return $this->db->query("select * from sc_trx.approvals_system where docno is not null $param");
    }

    function q_approval_system_karyawan($param)
    {
        return $this->db->query("select * from (
select a.*,b.*,c.tglawal,c.tglakhir,to_char(c.tglawal,'dd-mm-yyyy') as tglawal1,to_char(c.tglakhir,'dd-mm-yyyy') as tglakhir1 from sc_trx.approvals_system a
left outer join sc_mst.lv_m_karyawan b on a.docref=b.nik
left outer join sc_trx.abscut c on a.docno=c.docno
) as x
where nik is not null $param order by docno desc,docdate desc");
    }

}
