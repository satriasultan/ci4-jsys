<?php
namespace App\Models\Report;

use CodeIgniter\Model;

class M_Manpower extends Model
{

    function q_manpower($param){
        return $this->db->query("
select urutl,nmdept, nmsubdept, nmjabatan,
SUM(PRESDIR) AS PRESDIR,
SUM(VP) AS VP,
SUM(DIR) AS DIR,
SUM(GM) AS GM,
SUM(MNG) AS MNG,
SUM(AM) AS AM,
SUM(SPV) AS SPV,
SUM(STAFF) AS STAFF,
SUM(OPR) AS OPR,
SUM(PKWT_MNG) AS PKWT_MNG,
SUM(PKWT_AM) AS PKWT_AM,
SUM(PKWT_SPV) AS PKWT_SPV,
SUM(PKWT_STAFF) AS PKWT_STAFF,
SUM(PKWT_OPR) AS PKWT_OPR,
SUM(TTL_OS) AS TTL_OS,
(SUM(PRESDIR) +
SUM(VP)  +
SUM(DIR)  +
SUM(GM)  +
SUM(MNG)  +
SUM(AM)  +
SUM(SPV)  +
SUM(STAFF)  +
SUM(OPR)  +
SUM(PKWT_MNG)  +
SUM(PKWT_AM)  +
SUM(PKWT_SPV)  +
SUM(PKWT_STAFF)  +
SUM(PKWT_OPR)  +
SUM(TTL_OS) ) AS GRANDTOTAL FROM (
		
select 1 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
count(*) AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='1A' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 2 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
count(*) AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='1B' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 3 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
count(*) AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='1C' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 4 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
count(*) AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='1D' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan



UNION ALL

select 5 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
count(*) AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2A' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 6 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
count(*) AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2B' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 7 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
count(*) AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2C' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 8 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
count(*) AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2D' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 9 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
count(*) AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2E' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan

UNION ALL

select 10 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
count(*) AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2A' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan

UNION ALL

select 11 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
count(*) AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2B' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan

UNION ALL

select 12 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
count(*) AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2C' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan


UNION ALL

select 13 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
count(*) AS PKWT_STAFF,
0 AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2D' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan

UNION ALL

select 14 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
count(*) AS PKWT_OPR,
0 AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2E' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
group by nmdept,nmsubdept,nmjabatan

UNION ALL

select 15 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
0 AS PRESDIR,
0 AS VP,
0 AS DIR,
0 AS GM,
0 AS MNG,
0 AS AM,
0 AS SPV,
0 AS STAFF,
0 AS OPR,
0 AS PKWT_MNG,
0 AS PKWT_AM,
0 AS PKWT_SPV,
0 AS PKWT_STAFF,
0 AS PKWT_OPR,
count(*) AS TTL_OS,
0 AS GRANDTOTAL
from sc_mst.lv_m_karyawan where trim(id_grade)='2F' and coalesce(trim(resign),'NO')='NO'
group by nmdept,nmsubdept,nmjabatan



UNION ALL


select 15 as urut,2 as urutl,nmdept,'TOTAL'::TEXT as nmsubdept,'' as nmjabatan,
SUM(PRESDIR) AS PRESDIR,
SUM(VP) AS VP,
SUM(DIR) AS DIR,
SUM(GM) AS GM,
SUM(MNG) AS MNG,
SUM(AM) AS AM,
SUM(SPV) AS SPV,
SUM(STAFF) AS STAFF,
SUM(OPR) AS OPR,
SUM(PKWT_MNG) AS PKWT_MNG,
SUM(PKWT_AM) AS PKWT_AM,
SUM(PKWT_SPV) AS PKWT_SPV,
SUM(PKWT_STAFF) AS PKWT_STAFF,
SUM(PKWT_OPR) AS PKWT_OPR,
SUM(TTL_OS) AS TTL_OS,
		SUM(GRANDTOTAL) AS GRANDTOTAL FROM (
		select 1 as urut,1 as urutl,nmdept,'' as nmsubdept,'' as nmjabatan,
		count(*) AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1A' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 2 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		count(*) AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1B' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 3 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		count(*) AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1C' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 4 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		count(*) AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1D' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan



		UNION ALL

		select 5 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		count(*) AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2A' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 6 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		count(*) AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2B' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 7 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		count(*) AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2C' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 8 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		count(*) AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2D' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 9 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		count(*) AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2E' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 10 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		count(*) AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2A' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 11 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		count(*) AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2B' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 12 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		count(*) AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2C' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 13 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		count(*) AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2D' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 14 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		count(*) AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2E' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 15 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		count(*) AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2F' and coalesce(trim(resign),'NO')='NO'
		group by nmdept,nmsubdept,nmjabatan ) as x GROUP BY nmdept
		
		
		
UNION ALL
		
select 15 as urut,3 as urutl,'Z GRANDTOTAL A'::TEXT nmdept,'TOTAL'::TEXT as nmsubdept,'' as nmjabatan,
SUM(PRESDIR) AS PRESDIR,
SUM(VP) AS VP,
SUM(DIR) AS DIR,
SUM(GM) AS GM,
SUM(MNG) AS MNG,
SUM(AM) AS AM,
SUM(SPV) AS SPV,
SUM(STAFF) AS STAFF,
SUM(OPR) AS OPR,
SUM(PKWT_MNG) AS PKWT_MNG,
SUM(PKWT_AM) AS PKWT_AM,
SUM(PKWT_SPV) AS PKWT_SPV,
SUM(PKWT_STAFF) AS PKWT_STAFF,
SUM(PKWT_OPR) AS PKWT_OPR,
SUM(TTL_OS) AS TTL_OS,
		SUM(GRANDTOTAL) AS GRANDTOTAL FROM (
		select 1 as urut,1 as urutl,nmdept,'' as nmsubdept,'' as nmjabatan,
		count(*) AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1A' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 2 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		count(*) AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1B' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 3 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		count(*) AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1C' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 4 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		count(*) AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='1D' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan



		UNION ALL

		select 5 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		count(*) AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2A' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 6 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		count(*) AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2B' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 7 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		count(*) AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2C' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 8 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		count(*) AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2D' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 9 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		count(*) AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2E' and trim(coalesce(statuskepegawaian,''))='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 10 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		count(*) AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2A' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 11 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		count(*) AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2B' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 12 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		count(*) AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2C' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan


		UNION ALL

		select 13 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		count(*) AS PKWT_STAFF,
		0 AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2D' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 14 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		count(*) AS PKWT_OPR,
		0 AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2E' and trim(coalesce(statuskepegawaian,''))!='TT' and trim(coalesce(resign,'NO'))='NO'
		group by nmdept,nmsubdept,nmjabatan

		UNION ALL

		select 15 as urut,1 as urutl,nmdept,nmsubdept,nmjabatan,
		0 AS PRESDIR,
		0 AS VP,
		0 AS DIR,
		0 AS GM,
		0 AS MNG,
		0 AS AM,
		0 AS SPV,
		0 AS STAFF,
		0 AS OPR,
		0 AS PKWT_MNG,
		0 AS PKWT_AM,
		0 AS PKWT_SPV,
		0 AS PKWT_STAFF,
		0 AS PKWT_OPR,
		count(*) AS TTL_OS,
		0 AS GRANDTOTAL
		from sc_mst.lv_m_karyawan where trim(id_grade)='2F' and coalesce(trim(resign),'NO')='NO'
		group by nmdept,nmsubdept,nmjabatan ) as x 
		
		
		
		
		) x2 GROUP BY nmdept,urutl,nmsubdept,nmjabatan
order by nmdept,urutl,nmsubdept,nmjabatan");

    }





}
