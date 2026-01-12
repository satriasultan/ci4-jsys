<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_regu extends Model
{
    function q_regu(){
        return $this->db->query("select b.nmmesin,a.* from sc_mst.regu a 
								left outer join sc_mst.mesin b on a.kdmesin=b.kdmesin
								order by a.kdregu asc");
    }
    function q_karyawan_session($nama){
        return $this->db->query("select * from sc_mst.t_karyawan 
                                where nik in (select nik from sc_mst.user where username='$nama')");
    }

    function q_cekregu($kdregu){
        return $this->db->query("select * from sc_mst.regu where trim(kdregu)='$kdregu'");
    }

    function q_regu_opr(){
        return $this->db->query("select a.*,b.nmregu,c.nik,c.nmlengkap,d.nmdept from sc_mst.regu_opr a
								left outer join sc_mst.regu b on a.kdregu=b.kdregu
								left outer join sc_mst.karyawan c on a.nik=c.nik
								left outer join sc_mst.departmen d on c.bag_dept=d.kddept
								order by a.kdregu asc");
    }

    function q_regu_oprv($parameter){
        return $this->db->query("select * from (
                                    select a.*,b.nmregu,c.nmlengkap,c.id_dept,c.nmdept,c.nmsubdept,c.nmjabatan from sc_mst.regu_opr a
                                    left outer join sc_mst.regu b on a.kdregu=b.kdregu
                                    left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik) as x
                                    where nik is not null $parameter
                                    order by kdregu asc,input_date desc");
    }

    function q_regu_opr_edit($id){
        return $this->db->query("select * from (
                                    select a.*,to_char(a.tglefektif,'dd-mm-yyyy') as tglefektif1,b.nmregu,c.nmlengkap,c.id_dept,c.nmdept,c.nmsubdept,c.nmjabatan from sc_mst.regu_opr a
                                    left outer join sc_mst.regu b on a.kdregu=b.kdregu
                                    left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik) as x
                                    where nik is not null and no_urut='$id'
                                    order by kdregu asc ,nmlengkap asc");
    }

    function q_regu_opr_filter($parameter){
        return $this->db->query("select * from (
                                    select a.*,b.nmregu,c.nmlengkap,c.id_dept,c.nmdept,c.nmsubdept,c.nmjabatan from sc_mst.regu_opr a
                                    left outer join sc_mst.regu b on a.kdregu=b.kdregu
                                    left outer join sc_mst.lv_m_karyawan c on a.nik=c.nik) as x
                                    where nik is not null $parameter
                                    order by kdregu asc ,nmlengkap asc");
    }

    function q_regu_filterold(){
        return $this->db->query("select * from sc_mst.regu 
								where kdregu in (select kdregu from sc_mst.regu_opr)");
    }

    function q_regu_filter(){
        return $this->db->query("select * from sc_mst.regu 
								");
    }

    function q_list_mesin(){
        return $this->db->query("select * from sc_mst.mesin");
    }

    function q_cekregu_opr($kdregu,$nik){
        return $this->db->query("select * from sc_mst.regu_opr where trim(kdregu)='$kdregu' and nik='$nik'");
    }
    function q_list_nik(){
        return $this->db->query("select * from sc_mst.lv_m_karyawan_trans
                                    where nik not in
                                (select distinct(nik) from sc_mst.regu_opr) and trim(coalesce(resign,''))='NO' order by nmlengkap asc");
    }

    function q_list_warna(){
        return $this->db->query("select * from sc_mst.warna");
    }

    function q_cek_del_regu($kdregu){
        return $this->db->query("select * from sc_trx.jadwalkerja where kdregu='$kdregu'");

    }

    function q_cekdtlregu(){
        return $this->db->query("select a.*,b.nmlengkap from sc_mst.regu_opr a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where (a.nik not in (select nik from sc_trx.dtljadwalkerja))");
    }
    function q_cektmpregu($nama){
        return $this->db->query("select a.*,b.nmlengkap from sc_tmp.regu_opr a
								left outer join sc_mst.karyawan b on a.nik=b.nik
								where (a.nik not in (select nik from sc_trx.dtljadwalkerja)) and input_by='$nama'");
    }


    function q_cekdtlreguopr($nik){
        return $this->db->query("select * from sc_mst.regu_opr where (nik not in (select nik from sc_trx.dtljadwalkerja)) and nik='$nik'");
    }
    function q_list_setup(){
        return $this->db->query("select * from sc_mst.setup_grjadwal order by kd_opt");
    }

    function q_list_setupv2(){
        return $this->db->query("select * from sc_tmp.template_jadwalmst order by kdtemplate");
    }
    /* REGU OPTION */


    function q_mst_regu_option($param){
        return $this->db->query("select a.*,b.nmregu,c.nik,c.nmlengkap,d.nmdept from sc_mst.regu_opr a
									left outer join sc_mst.regu b on a.kdregu=b.kdregu
									left outer join sc_mst.karyawan c on a.nik=c.nik and coalesce(c.statuskepegawaian,'')!='KO'
									left outer join sc_mst.departmen d on c.bag_dept=d.kddept
									left outer join sc_mst.subdepartmen e on c.bag_dept=e.kddept and c.subbag_dept=e.kdsubdept
									left outer join sc_mst.jabatan f on c.bag_dept=f.kddept and c.subbag_dept=f.kdsubdept and c.jabatan=f.kdjabatan
									where a.kdregu is not null and a.nik not in (select trim(nik) from sc_tmp.regu_opr_option) $param
									order by c.nmlengkap asc");
    }

    function q_tmp_regu_option($param){
        return $this->db->query("select * from (
											select a.*,b.nmregu,c.nik,c.nmlengkap,d.nmdept,1::int as urut from sc_tmp.regu_opr_option a
												left outer join sc_mst.regu b on a.kdregu=b.kdregu
												left outer join sc_mst.karyawan c on a.nik=c.nik and coalesce(c.statuskepegawaian,'')!='KO'
												left outer join sc_mst.departmen d on c.bag_dept=d.kddept
												left outer join sc_mst.subdepartmen e on c.bag_dept=e.kddept and c.subbag_dept=e.kdsubdept
												left outer join sc_mst.jabatan f on c.bag_dept=f.kddept and c.subbag_dept=f.kdsubdept and c.jabatan=f.kdjabatan
												where a.kdregu is not null $param
											union all
											select a.*,b.nmregu,c.nik,c.nmlengkap,d.nmdept,2::int as urut from sc_mst.regu_opr a
												left outer join sc_mst.regu b on a.kdregu=b.kdregu
												left outer join sc_mst.karyawan c on a.nik=c.nik and coalesce(c.statuskepegawaian,'')!='KO'
												left outer join sc_mst.departmen d on c.bag_dept=d.kddept
												left outer join sc_mst.subdepartmen e on c.bag_dept=e.kddept and c.subbag_dept=e.kdsubdept
												left outer join sc_mst.jabatan f on c.bag_dept=f.kddept and c.subbag_dept=f.kdsubdept and c.jabatan=f.kdjabatan
												where a.kdregu is not null $param ) as x 
											where urut is not null												
											order by nmlengkap asc");
    }

    function q_cek_tmp_regu_option($param){
        return $this->db->query("select a.*,b.nmregu,c.nik,c.nmlengkap,d.nmdept,1::int as urut from sc_tmp.regu_opr_option a
												left outer join sc_mst.regu b on a.kdregu=b.kdregu
												left outer join sc_mst.karyawan c on a.nik=c.nik and coalesce(c.statuskepegawaian,'')!='KO'
												left outer join sc_mst.departmen d on c.bag_dept=d.kddept
												left outer join sc_mst.subdepartmen e on c.bag_dept=e.kddept and c.subbag_dept=e.kdsubdept
												left outer join sc_mst.jabatan f on c.bag_dept=f.kddept and c.subbag_dept=f.kdsubdept and c.jabatan=f.kdjabatan
												where a.kdregu is not null $param order by c.nmlengkap asc");
    }


    function add_regu_opr_tmp($data = array()){
        $insert = $this->db->insert_batch('sc_tmp.regu_opr_option ',$data);
        return $insert?true:false;
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

    function q_confirmasi_regu($param){
        return $this->db->query("select * from (
                                    select a.*,b.nik as nik_old,b.kdregu as kdregu_old, b.tglefektif as tglefektif_old,c.nmlengkap,c1.nmdept,b1.nmregu as nmregu_old,b2.nmregu as nmregu  from sc_tmp.regu_opr_option a 
                                    left outer join sc_mst.regu_opr b on a.nik=b.nik 
                                    left outer join sc_mst.karyawan c on a.nik=c.nik
                                    left outer join sc_mst.departmen c1 on c.bag_dept = c1.kddept 
                                    left outer join sc_mst.regu b1 on b.kdregu=b1.kdregu
                                    left outer join sc_mst.regu b2 on a.kdregu=b2.kdregu
                                    )as x  $param");
    }

    function q_history_mutasi_regu($param){
        /* buat view*/
        return $this->db->query("select * from (
                                    select a.*,b.nmlengkap,c1.nmdept,b1.nmregu as nmregu_old,b2.nmregu as nmregu_new from sc_his.regu_history_mutasi a 
                                    left outer join sc_mst.karyawan b on a.nik=b.nik
                                    left outer join sc_mst.departmen c1 on b.bag_dept = c1.kddept 
                                    left outer join sc_mst.regu b1 on a.kdregu_old=b1.kdregu
                                    left outer join sc_mst.regu b2 on a.kdregu_new=b2.kdregu) as x where nik is not null $param");
    }


    /* START MASTER MAPPING JADWAL JAM KERJA */
    var $t_history_mutasi_regu = "sc_his.v_regu_history_mutasi";
    var $t_history_mutasi_regu_column = array('nik','nmlengkap','nmdept','input_date','nmregu_old','tglefektif_old','nmregu_new','tglefektif_new'); //set column field database for datatable
    var $t_history_mutasi_regu_order = array("input_date" => 'desc'); // default order
    function _get_t_history_mutasi_regu_query()
    {
        if($this->input->post('nik')) { $this->db->like('nik', $this->input->post('nik')); }
        //if($this->input->post('kdregu')) { $this->db->like('kdregu', $this->input->post('kdregu')); }

        $this->db->from($this->t_history_mutasi_regu);
        $i = 0;

        foreach ($this->t_history_mutasi_regu_column as $item)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    // $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like("upper(cast(" . strtoupper($item) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->column_search) - 1 == $i); //last loop
                //$this->db->group_end(); //close bracket
            }
            //$x_column[$i] = $item;
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $this->db->order_by($this->t_history_mutasi_regu_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_history_mutasi_regu_order))
        {
            $order = $this->t_history_mutasi_regu_order;
            foreach ($order as $key => $item){
                $this->db->order_by($key, $item);
            }
        }
    }
    function get_t_history_mutasi_regu()
    {
        $this->_get_t_history_mutasi_regu_query();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    function t_history_mutasi_regu_count_filtered()
    {
        $this->_get_t_history_mutasi_regu_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function t_history_mutasi_regu_count_all()
    {
        $this->db->from($this->t_history_mutasi_regu);
        return $this->db->count_all_results();
    }
    public function get_t_history_mutasi_regu_by_id($id)
    {
        $this->db->from($this->t_history_mutasi_regu);
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    function cek_fill_regu($param){
        return $this->db->query("select * from sc_tmp.regu_opr_option where nodok is not null $param");
    }

    function list_karyawan($param){
        return $this->db->query("select * from sc_mst.karyawan where coalesce(statuskepegawaian,'')!='KO' and nik is not null $param
        order by nmlengkap asc");
    }

    function q_regu_opr_tmp($parameter_im){
        return $this->db->query("select * from (
select a.*,to_char(a.tglefektif,'dd-mm-yyyy') as tglefektif1,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,z.uraian as nmstatus from sc_im.regu_opr a
left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
left outer join sc_mst.trxtype z on a.status=z.kdtrx and jenistrx='ABSCUT') as x
where nik is not null $parameter_im");
    }

    function q_regu_opr_mst($parameter_mst){
        return $this->db->query("select * from (
select a.*,to_char(a.tglefektif,'dd-mm-yyyy') as tglefektif1,a1.nmregu,b.nmlengkap,b.nmdept,b.nmsubdept,b.nmjabatan,z.uraian as nmstatus from sc_mst.regu_opr a
left outer join sc_mst.regu a1 on a.kdregu=a1.kdregu    
left outer join sc_mst.lv_m_karyawan b on a.nik=b.nik
left outer join sc_mst.trxtype z on a.status=z.kdtrx and jenistrx='ABSCUT') as x
where nik is not null $parameter_mst");
    }
}
