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


namespace App\Models\Absenmesin;
use CodeIgniter\Model;


class M_Absenmesin extends Model
{


    var $table = 'USERINFO';
    var $column = array('USERID','Badgenumber','Name');
    var $order = array('USERID' => 'asc');
    private $dbdmk,$dbsby,$dbsmg,$dbjkt,$db2,$db3;

    public function __construct()
    {
        parent::__construct();
    }

    function show_user_lembur($tgl1,$tgl2){

        $this->db3 = $this->load->database('lembur', TRUE);
        return $this->db3->query("select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc
								");
    }

    function del_user_lembur($tgl1,$tgl2){
        return $this->db3->query("delete from CHECKINOUT
								where CHECKTIME between #$tgl1# and #$tgl2# 
								");
    }
    function ttldata_lembur($tgl1,$tgl2){
        return $this->db3->query("select count(*) as jumlah from (
select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc) as x							
								");
    }


    function q_trxabsen(){
        return $this->db->query("select * from sc_mst.trxtype where jenistrx in ('ABSEN','PRESENT') and kdtrx not in ('IJD','IJP','LMB') order by uraian ");
    }
    function q_cek_fingerid($badgenumber){
        return $this->db->query("select * from sc_mst.karyawan where idabsen='$badgenumber'");
    }


    function show_user_dmk($tgl1,$tgl2){
        $this->dbdmk = $this->load->database('NBIDMK', TRUE);
        return $this->dbdmk->query("select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc
								");
    }

    function show_user_smg($tgl1,$tgl2){
        $this->dbsmg = $this->load->database('NBISMG', TRUE);
        return $this->dbsmg->query("select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc
								");
    }

    function show_user_jkt($tgl1,$tgl2){

        $this->dbjkt = $this->load->database('NBIJKT', TRUE);
        return $this->dbjkt->query("select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc
								");
    }

    function show_user_sby($tgl1,$tgl2){
        $this->dbsby = $this->load->database('NBISBY', TRUE);
        return $this->dbsby->query("select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc
								");
    }
    /* ----------------  */
    function show_userinfo_smg($tgl1,$tgl2){
        $this->dbsmg = $this->load->database('NBISMG', TRUE);
        return $this->dbsmg->query("select top 10000 * from USERINFO order by USERID asc");
    }

    function show_userinfo_sby($tgl1,$tgl2){
        $this->dbsby = $this->load->database('NBISBY', TRUE);
        return $this->dbsby->query("select top 10000 * from USERINFO order by USERID asc");
    }
    function show_userinfo_jkt($tgl1,$tgl2){
        $this->dbjkt = $this->load->database('NBIJKT', TRUE);
        return $this->dbjkt->query("select top 10000 * from USERINFO order by USERID asc");
    }
    function show_userinfo_dmk($tgl1,$tgl2){
        $this->dbdmk = $this->load->database('NBIDMK', TRUE);
        return $this->dbdmk->query("select top 10000 * from USERINFO order by USERID asc");
    }
    /* ----------------  */

    function ttldata_dmk($tgl1,$tgl2){
        $this->dbdmk = $this->load->database('NBIDMK', TRUE);
        return $this->dbdmk->query("select count(*) as jumlah from (
select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc) as x								
								");
    }

    function ttldata_smg($tgl1,$tgl2){
        $this->dbsmg = $this->load->database('NBISMG', TRUE);
        return $this->dbsmg->query("select count(*) as jumlah from (
select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc) as x								
								");
    }

    function ttldata_sby($tgl1,$tgl2){
        $this->dbsby = $this->load->database('NBISBY', TRUE);
        return $this->dbsby->query("select count(*) as jumlah from (
select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc) as x								
								");
    }
    function ttldata_jkt($tgl1,$tgl2){
        $this->dbjkt = $this->load->database('NBIJKT', TRUE);
        return $this->dbjkt->query("select count(*) as jumlah from (
select a.USERID,a.CHECKTIME,b.Name,b.Badgenumber from checkinout a 
left join userinfo b on a.userid=b.userid
where checktime between #$tgl1# and #$tgl2#
group by a.USERID,a.CHECKTIME,b.Name,b.Badgenumber
order by a.checktime,b.name asc) as x								
								");
    }


    function simpan($info){
        $this->db->insert('sc_tmp.checkinout',$info);
    }

    function simpan_lembur($info){
        $this->db->insert('sc_tmp.checkinout_lembur',$info);
    }

    function simpan_userinfo($info){
        $this->db->insert('sc_tmp.mdb_userinfo',$info);
    }

    function cek_tmp_userinfo($param){
        return $this->db->query("select * from sc_tmp.mdb_userinfo where userid is not null $param");
    }

    public function get_by_id($id)
    {
        //$this->db2->from($this->table);
        //$this->db2->where('USERID',$id);
        //$query = $this->db2->get();
        $query = $this->db2->query('select * from USERINFO where USERID="'.$id.'"');
        return $query->row();
    }

    public function saves($data)
    {
        return $this->db2->insert($this->table, $data);
        //return $this->db2->insert_id();
    }

    public function updates($where, $data)
    {
        $this->db2->update($this->table, $data, $where);
        return $this->db2->affected_rows();
    }

    public function delete_by_id($id)
    {
        $this->db2->where('USERID', $id);
        $this->db2->delete($this->table);
    }

    function q_absensi_kor($tgl1,$tgl2,$nik){
        return $this->db->query("select b.nik,b.nmlengkap,to_char(a.checktime,'DD-MM-YYYY')as tanggal,to_char(a.checktime,'hh24:mi:ss')as jam,
								case 
								when a.editan='t' then 'YES'
								else 'NO' end as status_edit,
								a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik
								where a.checktime between '$tgl1' and '$tgl2' and b.nik='$nik'
								order by a.checktime asc,a.nama asc
								");

    }

    function q_absensi2($tgl1,$nik){
        return $this->db->query("select t7.nmlengkap,t4.nik,t4.tanggal,t4.jam,t4.ket,
								case 
								when jam>t5.jam_masuk and ket='MASUK' then 'TELAT' 
								when jam>t5.jam_pulang and ket='PULANG' then 'LOYALITAS'
								when jam>t5.jam_pulang_min and jam<t5.jam_pulang and ket='PULANG' then 'PULANG AWAL'   
								when jam<t5.jam_masuk and jam>jam_masuk_min and ket='MASUK' then 'ONTIME'   
								when jam>t5.jam_pulang and jam<jam_pulang_max and ket='PULANG' then 'ONTIME'   
								when jam>t5.jam_istirahat_min and jam<jam_istirahatselesai and ket='ISTIRAHAT' then 'ONTIME'   
								else 'TIDAK TERDETEKSI'
								end as ketper
								from 
								(select nik,tanggal,min(jam) as jam,ket
								from (select t1.nik,tanggal,jam
								,case when jam>jam_masuk_min and jam<jam_masuk_max then 'MASUK'
								when jam>jam_pulang_min and jam<jam_pulang_max then 'PULANG' 
								when jam>jam_masuk_max and jam>jam_istirahat and jam<jam_istirahatselesai then 'ISTIRAHAT'
								else 'TIDAK ADA KETERANGAN' 
								end as ket
								from
								(select * from(select b.nik,cast(to_char(a.checktime,'DD-MM-YYYY') as date)as tanggal,cast(to_char(a.checktime,'hh24:mi:ss') as time) as jam,
								case when a.editan='t' then 'YES' else 'NO' end as status_edit,
								a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik) as t6
								where t6.tanggal='$tgl1' and t6.nik='$nik'
								order by checktime asc,nama asc) as t1
								left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
								where a.nik='$nik' and a.tgl='$tgl1') as t2 on t1.nik=t2.nik and t1.tanggal=t2.tgl) as t3
								group by nik,tanggal,ket) as t4
								left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
								where a.nik='$nik' and a.tgl='$tgl1') as t5 on t4.nik=t5.nik and t4.tanggal=t5.tgl
								left outer join sc_mst.karyawan t7 on t4.nik=t7.nik");

    }

    function q_absensi($tgl1,$tgl2,$nik) {
        return $this->db->query("select t7.nmlengkap,t4.nik,t4.tanggal,t4.jam,t4.ket,
								case 
								when jam>t5.jam_masuk and ket='MASUK' then 'TELAT' 
								when jam>t5.jam_pulang and ket='PULANG' then 'LOYALITAS'
								when jam>t5.jam_pulang_min and jam<t5.jam_pulang and ket='PULANG' then 'PULANG AWAL'   
								when jam<t5.jam_masuk and jam>jam_masuk_min and ket='MASUK' then 'ONTIME'   
								when jam>t5.jam_pulang and jam<jam_pulang_max and ket='PULANG' then 'ONTIME'   
								when jam>t5.jam_istirahat_min and jam<jam_istirahatselesai and ket='ISTIRAHAT' then 'ONTIME'   
								else 'TIDAK TERDETEKSI'
								end as ketper
								from 
								(select distinct nik,tanggal,min(jam) as jam,ket
								from (select t1.nik,tanggal,jam
								,case when jam>jam_masuk_min and jam<jam_masuk_max then 'MASUK'
								when jam>jam_pulang_min and jam<jam_pulang_max then 'PULANG' 
								when jam>jam_masuk_max and jam>jam_istirahat and jam<jam_istirahatselesai then 'ISTIRAHAT'
								else 'TIDAK ADA KETERANGAN' 
								end as ket
								from
								(select * from(select b.nik,cast(to_char(a.checktime,'DD-MM-YYYY') as date)as tanggal,cast(to_char(a.checktime,'hh24:mi:ss') as time) as jam,
								case when a.editan='t' then 'YES' else 'NO' end as status_edit,
								a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik) as t6
								where t6.tanggal between '$tgl1' and '$tgl2' and t6.nik='$nik' 
								order by checktime asc,nama asc) as t1
									left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
									left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
									where a.nik='$nik ' and a.tgl between '$tgl1' and '$tgl2') as t2 on t1.nik=t2.nik and t1.tanggal=t2.tgl) as t3
									group by nik,tanggal,ket) as t4
								left outer join (select b.*,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.jam_kerja b on a.kdjamkerja=b.kdjam_kerja
								where a.nik='$nik ' and a.tgl between '$tgl1' and '$tgl2') as t5 on t4.nik=t5.nik and t4.tanggal=t5.tgl
								left outer join sc_mst.karyawan t7 on t4.nik=t7.nik
								order by t4.tanggal asc,t4.ket asc");

    }
    function q_karyawan(){
        return $this->db->query("select * from sc_mst.karyawan order by nmlengkap asc");

    }

    function q_regu(){
        return $this->db->query("select * from sc_mst.regu order by nmregu asc");
    }

    function q_department(){
        return $this->db->query("select * from sc_mst.departmen order by nmdept asc");
    }

    function q_regu_detail(){
        return $this->db->query("select distinct kdregu,tgl from sc_trx.jadwalkerja");
    }

    function q_jamkerja(){
        return $this->db->query("select * from sc_mst.jam_kerja");
    }

    function q_jadwalkerja($tgl){
        return $this->db->query("select distinct tgl,to_char(tgl,'DD') as tglnya,
								to_char(tgl,'MM') as bln,to_char(tgl,'YYYY') as tahun
								from sc_trx.jadwalkerja
								where to_char(tgl,'mmYYYY')='$tgl'
								order by tgl asc
								");
    }

    function q_dtljadwalkerja($tgl_kerja){
        return $this->db->query("select  id,kdregu,kodejamkerja,to_char(tgl,'DD-MM-YYYY') as tgl,b.jam_masuk,b.jam_pulang from sc_trx.jadwalkerja a 
								left outer join sc_mst.jam_kerja b on a.kodejamkerja=b.kdjam_kerja
								where tgl='$tgl_kerja'");
    }


    function q_dtljadwalkerja_regu($tgl_kerja,$kdregu){
        return $this->db->query("select to_char(tgl,'DD-MM-YYYY') as tglnya,to_char(tgl,'DDMMYYYY') as tglkode,b.nmlengkap,c.jam_masuk,c.jam_pulang,a.* from sc_trx.dtljadwalkerja a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								left outer join sc_mst.jam_kerja c on a.kdjamkerja=c.kdjam_kerja 

								where a.kdregu='$kdregu' and a.tgl='$tgl_kerja'");
    }

    function q_jadwalkerja_ns($tgl_kerja){
        return $this->db->query("select b.nmlengkap,a.* from sc_trx.jadwalkerja_ns a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where a.tgl='$tgl_kerja'");
    }

    function q_dtljadwalkerja_ns($tgl_kerja,$nik){
        return $this->db->query("select b.nmlengkap,a.* from sc_trx.jadwalkerja_ns a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where a.nik='$nik' and a.tgl='$tgl_kerja'");
    }

    function q_mesin(){
        return $this->db->query("select * from sc_mst.mesin");
    }

    function cek_exist($kdregu,$kdjamkerja,$tgl){
        return $this->db->query("select * from sc_trx.jadwalkerja a
								where kdregu='$kdregu' and kodejamkerja='$kdjamkerja' and tgl='$tgl'")->num_rows();
    }


    function cek_exist_detail($nik,$kdjamkerja,$tgl){
        return $this->db->query("select * from sc_trx.dtljadwalkerja a
								where nik='$nik' and kdjamkerja='$kdjamkerja' and tgl='$tgl'")->num_rows();
    }

    function cek_exist_ns($nik,$kdjamkerja,$tgl){
        return $this->db->query("select * from sc_trx.jadwalkerja_ns a
								where nik='$nik' and kodejamkerja='$kdjamkerja' and tgl='$tgl'")->num_rows();
    }

    function cek_shift($tgl_kerja,$shift){
        return $this->db->query("select * from sc_trx.jadwalkerja 
								where tgl='$tgl_kerja' and shift_tipe='$shift'")->num_rows();
    }

    function q_showcheckinout($tgl){
        return $this->db->query("select cast(to_char(checktime,'hh24:mi:ss')as time) as jam,c.kdjamkerja,* from 
								(select cast(to_char(a.checktime,'DD-MM-YYYY')as date) as tglchecktime,b.nik,a.* from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.nik
								where to_char(a.checktime,'DD-MM-YYYY')='$tgl' and b.nik in (select nik from sc_trx.dtljadwalkerja)) as t1 
								left outer join sc_trx.dtljadwalkerja c on c.nik=t1.nik and c.tgl=t1.tglchecktime
								left outer join sc_mst.jam_kerja d on d.kdjam_kerja=c.kdjamkerja");

    }


    function q_transready_old(){
        return $this->db->query("select cast((cast(tgl as date)- integer '1')||' '||to_char(jam_masuk_min,'hh24:mi:ss') as timestamp)as tgljadwal,
								cast(to_char(checktime,'hh24:mi:ss')as time) as jam,cast(to_char(checktime,'DD-MM-YYYY')as date) as tglcheck,* from sc_trx.transready 
								where to_char(checktime,'DD-MM-YYYY')='02-12-2015'	
								order by shiftke");

    }

    function q_loopingjadwal($tglawal,$tglakhir){

        return $this->db->query("select 
								a.nik,tgl,kdjamkerja,jam_masuk,jam_masuk_min,jam_pulang,jam_pulang_min,jam_pulang_max,kdharimasuk,kdharipulang,shiftke,
								case 
								when shiftke='3' and kdharimasuk='H-' then cast(to_char((cast(a.tgl as date)- integer '1'),'YYYY-MM-DD')||' '||to_char(b.jam_masuk_min,'HH24:MM:SS') as timestamp)	
								else cast(to_char((cast(a.tgl as date)),'YYYY-MM-DD')||' '||to_char(b.jam_masuk_min,'HH24:MM:SS') as timestamp) 
								end as tgl_min_masuk,
								case 
								when shiftke='2' and kdharipulang='H+' then cast(to_char((cast(a.tgl as date)+ integer '1'),'YYYY-MM-DD')||' '||to_char(b.jam_pulang_max,'HH24:MM:SS') as timestamp) 
								else cast(to_char((cast(a.tgl as date)),'YYYY-MM-DD')||' '||to_char(b.jam_pulang_max,'HH24:MM:SS') as timestamp)
								end as tgl_max_pulang
								from sc_trx.dtljadwalkerja a,sc_mst.jam_kerja b,sc_tmp.checkinout c, sc_mst.karyawan d
								where a.kdjamkerja=b.kdjam_kerja  and a.nik=d.nik and c.badgenumber=d.nik
								and to_char(tgl,'YYYY-MM-DD') between '$tglawal' and '$tglakhir' 
								group by a.nik,tgl,kdjamkerja,jam_masuk,jam_masuk_min,jam_pulang,jam_pulang_min,jam_pulang_max,kdharimasuk,kdharipulang,shiftke
								order by shiftke,tgl,nik
								");
    }


    //to_char(a.tgl,'YYYY-MM-DD')=to_char(c.checktime,'YYYY-MM-DD') and c.checktime is not null

    function q_caricheckinout($nik,$tgl1,$tgl2){

        return $this->db->query("select b.nik,min(checktime) as tgl_min,max(checktime) as tgl_max from sc_tmp.checkinout a,sc_mst.karyawan b
								where a.badgenumber=b.nik and  to_char(a.checktime,'YYYY-MM-DD hh24:mi:ss')>= '$tgl1' 
								and to_char(a.checktime,'YYYY-MM-DD hh24:mi:ss')<='$tgl2' and b.nik='$nik'
								
								group by b.nik");
    }

    function q_transready_koreksi($nik,$tgl1,$tgl2){
        return $this->db->query("select b.nmlengkap, 
										case 
										when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
										when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
										when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
										when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
										when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
										when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
										when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
										when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
										else 'TIDAK ABSEN MASUK/KELUAR'
										end as ketsts,
										case when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is not null then 'CUTI' 
										when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null then 'ALPHA'
										else 'TIDAK ADA DOKUMEN'
										end as ketcuti,
										case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is not null then 'IJIN' 
										when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is null then 'ALPHA'
										else 'TIDAK ADA DOKUMEN'
										end as ketijin,
										c.nodok as nodokcuti,d.nodok as nodokijin,
										a.* from sc_trx.transready a
										left outer join sc_mst.karyawan b on a.nik=b.nik
										left outer join sc_trx.cuti_karyawan c on a.nik=c.nik and a.tgl between c.tgl_mulai and c.tgl_selesai
										left outer join sc_trx.ijin_karyawan d on a.nik=d.nik and a.tgl=d.tgl_kerja 
										where a.nik='$nik' and a.tgl between '$tgl1' and '$tgl2'
										order by tgl asc
								");


    }

    function q_transready_koreksi_old($nik,$tgl1,$tgl2){
        return $this->db->query("select b.nmlengkap, 
												case 
												when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
												when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
												when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
												else 'TIDAK ABSEN MASUK/KELUAR'
												end as ketsts,
												a.* from sc_trx.transready a
												left outer join sc_mst.karyawan b on a.nik=b.nik
												where a.nik='$nik' and a.tgl between '$tgl1' and '$tgl2'
												order by tgl asc					
								");


    }

    function q_transready_koreksidept($kddept,$tgl1,$tgl2){
        return $this->db->query("select b.nmlengkap, 
												case 
												when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
												when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
												when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
												else 'TIDAK ABSEN MASUK/KELUAR'
												end as ketsts,
												case when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is not null then 'CUTI' 
												when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null and d.nodok is null and e.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketcuti,
												case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is not null then 'IJIN' 
												when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null and d.nodok is null and e.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketijin,
												case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is null and e.nodok is not null then 'DINAS' 
												when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null and d.nodok is null and e.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketdinas,
												c.nodok as nodokcuti,d.nodok as nodokijin,e.nodok as nodokdinas,c.keterangan as ketercuti,d.keterangan as keterijin,e.keperluan as keterdinas,
												a.* from sc_trx.transready a
												left outer join sc_mst.karyawan b on a.nik=b.nik
												left outer join sc_trx.cuti_karyawan c on a.nik=c.nik and a.tgl between c.tgl_mulai and c.tgl_selesai
												left outer join sc_trx.ijin_karyawan d on a.nik=d.nik and a.tgl=d.tgl_kerja
												left outer join sc_trx.dinas e on a.nik=e.nik and a.tgl between e.tgl_mulai and e.tgl_selesai	
												where b.bag_dept='$kddept' and b.statuskepegawaian<>'KO' and a.tgl between '$tgl1' and '$tgl2'
												order by tgl,nmlengkap asc						
								");


    }

    function q_show_edit($id){
        return $this->db->query("select b.nmlengkap, 
												case 
												when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
												when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
												when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
												when jam_masuk_absen is null and jam_pulang_absen is null then 'TIDAK MASUK KERJA'
												when shiftke='2' and jam_masuk=jam_pulang then 'TIDAK ABSEN MASUK/KELUAR'
												else 'TIDAK ABSEN MASUK/KELUAR'
												end as ketsts,
												case when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is not null then 'CUTI' 
												when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketcuti,
												case when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is not null then 'IJIN' 
												when c.nodok is null and jam_masuk_absen is null and jam_pulang_absen is null and d.nodok is null then 'ALPHA'
												else 'TIDAK ADA DOKUMEN'
												end as ketijin,
												c.nodok as nodokcuti,d.nodok as nodokijin,
												a.* from sc_trx.transready a
												left outer join sc_mst.karyawan b on a.nik=b.nik
												left outer join sc_trx.cuti_karyawan c on a.nik=c.nik and a.tgl between c.tgl_mulai and c.tgl_selesai
												left outer join sc_trx.ijin_karyawan d on a.nik=d.nik and a.tgl=d.tgl_kerja 
												where a.id='$id'
												order by tgl asc					
								");


    }


    /*function q_transready($nik,$tgl1,$tgl2,$ketsts){
        return $this->db->query("select * from (select b.nmlengkap,
                                            case
                                            when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
                                            when shiftke='3' and jam_masuk_absen>=jam_masuk_min  then 'TIDAK TERLAMBAT'
                                            when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
                                            when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
                                            when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
                                            when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk  then 'TIDAK TERLAMBAT'
                                            when jam_masuk_absen is null and jam_pulang_absen is null and c.nodok is null then 'TIDAK MASUK KERJA'
                                            when shiftke='2' and jam_masuk=jam_pulang  and c.nodok is null then 'TIDAK ABSEN MASUK/KELUAR'
                                            when c.nodok is not null and kdpokok='CT' then 'CUTI'
                                            when c.nodok is not null and kdpokok='IK' then 'CUTI KHUSUS'
                                            when c.nodok is not null and kdpokok='AL' then 'ALPHA'
                                            else 'TIDAK ABSEN MASUK/KELUAR'
                                            end as ketsts,
                                            a.*,c.kdpokok from sc_trx.transready a
                                            left outer join sc_mst.karyawan b on a.nik=b.nik
                                            left outer join sc_trx.listlinkjadwalcuti c on a.nik=c.nik and a.tgl=c.tgl
                                            where a.nik='$nik' and a.tgl between '$tgl1' and '$tgl2'
                                            order by tgl asc) as t1
                                    where ketsts $ketsts
                                ");


    }*/

    function q_transready($param,$tglawal,$tglakhir,$ketsts){
        return $this->db->query("select x.*,y.uraian as keterangan from (
								select a.nik,b.nmlengkap,a.tgl,a.kdjamkerja,a.kdregu,a.nodok,a.tgl_mulai,a.tgl_selesai,a.kdpokok,a.masuk,a.keluar,
									case when kdpokok='CB' OR kdpokok='CT' then 'CT'
									when kdpokok='CK' then 'CK'
									when kdpokok='DN' then 'DN'
									when kdpokok='AL' then 'AL'
									when a.masuk<c.jam_masuk and a.keluar>=c.jam_pulang then 'TE'
									when a.masuk>c.jam_masuk and a.keluar>=c.jam_pulang then 'TA'
									when a.masuk<c.jam_masuk and a.keluar<=c.jam_pulang then 'TD'
									when a.masuk>c.jam_masuk and a.keluar<=c.jam_pulang then 'TF'
									end as docref 
									from sc_trx.listlinkjadwalcuti a
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.jam_kerja c on a.kdpokok=c.kdjam_kerja
									where a.nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null $param) and ( to_char(a.tgl,'yyyy-mm-dd') between '$tglawal' and '$tglakhir')
								order by nmlengkap,tgl) as x left outer join sc_mst.trxtype y on x.docref=y.kdtrx and (y.jenistrx='ABSEN' OR y.jenistrx='PRESENT') where x.tgl is not null $ketsts order by nmlengkap
								");


    }
    function q_transready_regu($kdregu,$tglawal,$tglakhir,$ketsts){
        return $this->db->query("select x.*,y.uraian as keterangan from (
								select a.nik,b.nmlengkap,a.tgl,a.kdjamkerja,a.kdregu,a.nodok,a.tgl_mulai,a.tgl_selesai,a.kdpokok,a.masuk,a.keluar,
									case when kdpokok='CB' OR kdpokok='CT' then 'CT'
									when kdpokok='CK' then 'CK'
									when kdpokok='DN' then 'DN'
									when kdpokok='AL' then 'AL'
									when a.masuk<c.jam_masuk and a.keluar>=c.jam_pulang then 'TE'
									when a.masuk>c.jam_masuk and a.keluar>=c.jam_pulang then 'TA'
									when a.masuk<c.jam_masuk and a.keluar<=c.jam_pulang then 'TD'
									when a.masuk>c.jam_masuk and a.keluar<=c.jam_pulang then 'TF'
									end as docref 
									from sc_trx.listlinkjadwalcuti a
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.jam_kerja c on a.kdpokok=c.kdjam_kerja
									where a.nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null and kdregu='$kdregu') and ( to_char(a.tgl,'yyyy-mm-dd') between '$tglawal' and '$tglakhir')
								order by nmlengkap,tgl) as x left outer join sc_mst.trxtype y on x.docref=y.kdtrx and (y.jenistrx='ABSEN' OR y.jenistrx='PRESENT') where x.tgl is not null $ketsts order by nmlengkap								
								");


    }
    /*	function q_transready_regu($kdregu,$tgl1,$tgl2,$ketsts){
            return $this->db->query("select * from (
                                    select b.nmlengkap,
                                        case when shiftke='1' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk then 'TIDAK TERLAMBAT'
                                        when shiftke='3' and jam_masuk_absen>=jam_masuk_min then 'TIDAK TERLAMBAT'
                                        when shiftke='1' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
                                        when shiftke='2' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
                                        when shiftke='3' and jam_masuk_absen>jam_masuk then 'TERLAMBAT'
                                        when shiftke='2' and jam_masuk_absen>jam_masuk_min and jam_masuk_absen<=jam_masuk then 'TIDAK TERLAMBAT'
                                        when jam_masuk_absen is null and jam_pulang_absen is null  and d.nodok is null then 'TIDAK MASUK KERJA'
                                        when shiftke='2' and jam_masuk=jam_pulang  and d.nodok is null then 'TIDAK ABSEN MASUK/KELUAR'
                                        when d.nodok is not null and kdpokok='CT' then 'CUTI'
                                        when d.nodok is not null and kdpokok='IK' then 'CUTI KHUSUS'
                                        when d.nodok is not null and kdpokok='AL' then 'ALPHA'
                                        else 'TIDAK ABSEN MASUK/KELUAR' end as ketsts, a.*,d.kdpokok from sc_trx.transready a
                                        left outer join sc_mst.karyawan b on a.nik=b.nik
                                        left outer join sc_mst.regu_opr c on b.nik=c.nik
                                        left outer join sc_trx.listlinkjadwalcuti d on a.nik=d.nik and a.tgl=d.tgl
                                        where c.kdregu='$kdregu' and a.tgl between '$tgl1' and '$tgl2' order by tgl asc) as t1
                                        where ketsts $ketsts
                                    ");


        }*/

    function q_transready_dept($kddept,$tglawal,$tglakhir,$ketsts){
        return $this->db->query("select x.*,y.uraian as keterangan from (
								select a.nik,b.nmlengkap,a.tgl,a.kdjamkerja,a.kdregu,a.nodok,a.tgl_mulai,a.tgl_selesai,a.kdpokok,a.masuk,a.keluar,
									case when kdpokok='CB' OR kdpokok='CT' then 'CT'
									when kdpokok='CK' then 'CK'
									when kdpokok='DN' then 'DN'
									when kdpokok='AL' then 'AL'
									when a.masuk<c.jam_masuk and a.keluar>=c.jam_pulang then 'TE'
									when a.masuk>c.jam_masuk and a.keluar>=c.jam_pulang then 'TA'
									when a.masuk<c.jam_masuk and a.keluar<=c.jam_pulang then 'TD'
									when a.masuk>c.jam_masuk and a.keluar<=c.jam_pulang then 'TF'
									end as docref 
									from sc_trx.listlinkjadwalcuti a
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.jam_kerja c on a.kdpokok=c.kdjam_kerja
									where a.nik in (select nik from sc_mst.karyawan where tglkeluarkerja is null and bag_dept='$kddept') and ( to_char(a.tgl,'yyyy-mm-dd') between '$tglawal' and '$tglakhir')
								order by nmlengkap,tgl) as x left outer join sc_mst.trxtype y on x.docref=y.kdtrx and (y.jenistrx='ABSEN' OR y.jenistrx='PRESENT') where x.tgl is not null $ketsts order by nmlengkap
								");


    }

    function cek_absenexist($nik,$tgl,$kdjamkerja){

        return $this->db->query("select * from sc_trx.transready where nik='$nik' and tgl='$tgl' and kdjamkerja='$kdjamkerja'");
    }

    function tglakhir_ci($kdcabang){
        return $this->db->query("select to_char(max(checktime),'DD-MM-YYYY')as lastdate from sc_tmp.checkinout a
								left outer join sc_mst.karyawan b on a.badgenumber=b.idabsen
								where b.kdcabang='$kdcabang'");
    }

    function tglakhir_tr($kdcabang){
        return $this->db->query("select to_char(max(tgl),'DD-MM-YYYY')as lastdate from sc_trx.transready a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where b.kdcabang='$kdcabang'");

    }

    function q_prreportabsensi($bln,$thn,$user){
        return $this->db->query("select sc_trx.pr_reportabsen_user('$bln','$thn','$user')");
    }

    function excel_reportabsensi($bln,$thn){
        return $this->db->query("select a.*,b.nmlengkap,c.nmdept,d.nmjabatan,c1.nmsubdept from sc_trx.report_absen a 
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.departmen c on b.bag_dept=c.kddept
									left outer join sc_mst.jabatan d on b.jabatan=d.kdjabatan
									left outer join sc_mst.subdepartmen c1 on b.bag_dept=c1.kddept and b.subbag_dept=c1.kdsubdept
									where bulan='$bln' and tahun='$thn' order by b.nmlengkap asc");
    }


    function excel_reportabsensi_user($bln,$thn,$user){
        return $this->db->query("select a.*,b.nmlengkap,c.nmdept,d.nmjabatan,c1.nmsubdept from sc_trx.report_absen a 
									left outer join sc_mst.karyawan b on a.nik=b.nik
									left outer join sc_mst.departmen c on b.bag_dept=c.kddept
									left outer join sc_mst.jabatan d on b.jabatan=d.kdjabatan
									left outer join sc_mst.subdepartmen c1 on b.bag_dept=c1.kddept and b.subbag_dept=c1.kdsubdept
									where bulan='$bln' and tahun='$thn' and userid='$user' order by b.nmlengkap asc");
    }

    function q_kanwil(){
        return $this->db->query("select * from sc_mst.kantorwilayah where branch is not null order by desc_cabang");
    }

    function q_trxerror($paramtrxerror){
        return $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null $paramtrxerror");
    }


    function q_setup_attendance($param = null){
        return $this->db->query("select * from sc_mst.fingerprint where branch is not null $param  ORDER BY machinename asc ");
    }

    function q_show_checkinout($param = null,$limit = null){
        return $this->db->query("select * from (
                                    select a.userid,a.badgenumber,a.nama,a.checktime,a.inputan,a.editan,b.nik,b.nmlengkap,b.idabsen,b.kdcabang from sc_tmp.checkinout a
                                    left outer join sc_mst.karyawan b on a.badgenumber=b.idabsen) as x
                                    where nik is not null $param
                                    group by userid,badgenumber,nama,checktime,inputan,editan,nik,nmlengkap,idabsen,kdcabang $limit");
    }

    function q_set_connection($param){
        return $this->db->query("select * from sc_mst.fingerprint where branch is not null $param  ORDER BY machinename asc ");
    }

}
