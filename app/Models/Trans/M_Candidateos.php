<?php

namespace App\Models\Trans;

use CodeIgniter\Model;
$this->request = \Config\Services::request();

class M_Candidateos extends Model
{

    var $table = 'sc_tmp.karyawan';
    var $tablemst = 'sc_mst.lv_m_karyawan_candidate';
    var $column = array('nik','nmlengkap','cimage','nmdept','nmsubdept','nmjabatan','tglmasukkerja','plant');
    var $order = array('nik' => 'desc');
    
    private function _get_datatables_query()
    {

        //$this->db->cache_on();
        $this->request = \Config\Services::request();
        $builder = $this->db->table($this->tablemst);

        if (! empty($this->request->getPost('status'))) {
            $stats = trim($this->request->getPost('status'));
            $builder->where(array('status =' => $stats));
        }
        $builder->where(array('resign <>' => 'YES'));

        $i = 0;
        foreach ($this->column as $item)
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

                if(count($this->column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }
        if(isset($_POST['order']))
        {
            if ($_POST['order']['0']['column']!=0){ //diset klo post column 0
                $builder->orderBy($this->column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $builder->orderBy(key($order), $order[key($order)]);
        }
        return $builder;

    }

    function list_karyawan(){
        return $this->db->query("select *,' '||id_ktp||' ' as noktp,' '||nokk||' ' as idkk,' '||id_npwp||' ' as nonpwp,' '||id_rekening||' ' as norekening,
        ' '||nohp1||' ' as hp1,' '||nohp2||' ' as hp2,' '||nik||' ' as nik1 from sc_mst.lv_m_karyawan_candidate where nik is not null and coalesce(resign,'') !='YES' order by nmlengkap asc");
    }

    function list_karyawan_param($param){
        return $this->db->query("select * from sc_mst.lv_m_karyawan_candidate where nik is not null $param ");
    }
    function list_karyawan_resign(){
        return $this->db->query("select a.*,a.nik,a.nmlengkap,b.nmjabatan,c.nmdept,d.nmsubdept,e.nmlvljabatan,f.nmgrade,g1.nmagama,g2.namanegara,g3.namaprov as nmprovlahir,g4.namakotakab as nmkotalahir,
                                        h1.namaprov as nmprovktp,
                                        h2.namakotakab as nmkotaktp,
                                        h3.namakec as nmkecktp,
                                        h4.namakeldesa as nmdesaktp,
                                        i1.namaprov as nmprovtinggal,
                                        i2.namakotakab as nmkotatinggal,
                                        i3.namakec as nmkectinggal,
                                        i4.namakeldesa as nmdesatinggal
                                        from sc_mst.karyawan a
                                            left outer join sc_mst.departmen c on a.bag_dept=c.kddept  
                                            left outer join sc_mst.subdepartmen d on a.subbag_dept=d.kdsubdept and d.kddept=c.kddept
                                            left outer join sc_mst.jabatan b on a.subbag_dept=d.kdsubdept and d.kddept=b.kddept and a.jabatan=b.kdjabatan 
                                            left outer join sc_mst.lvljabatan e on a.lvl_jabatan=e.kdlvl
                                            left outer join sc_mst.jobgrade f on a.grade_golongan=f.kdgrade
                                    
                                            left outer join sc_mst.agama g1 on a.kd_agama=g1.kdagama
                                            left outer join sc_mst.negara g2 on a.neglahir=g2.kodenegara
                                            left outer join sc_mst.provinsi g3 on a.provlahir=g3.kodeprov
                                            left outer join sc_mst.kotakab g4 on a.kotalahir=g4.kodekotakab and g4.kodeprov=g3.kodeprov
                                            left outer join sc_mst.negara g5 on a.negktp=g5.kodenegara
                                            
                                            left outer join sc_mst.provinsi h1 on a.provktp=h1.kodeprov
                                            left outer join sc_mst.kotakab h2 on a.kotaktp=h2.kodekotakab and h2.kodeprov=h1.kodeprov
                                            left outer join sc_mst.kec h3 on a.kecktp=h3.kodekec
                                            left outer join sc_mst.keldesa h4 on a.kelktp=h4.kodekeldesa
                                    
                                            left outer join sc_mst.provinsi i1 on a.provtinggal=i1.kodeprov 
                                            left outer join sc_mst.kotakab i2 on a.kotatinggal=i2.kodekotakab and i2.kodeprov=i1.kodeprov
                                            left outer join sc_mst.kec i3 on a.kectinggal=i3.kodekec
                                            left outer join sc_mst.keldesa i4 on a.keltinggal=i4.kodekeldesa
                                            where coalesce(a.statuskepegawaian,'')='KO'");
    }

    function list_karyresgn(){
        return $this->db->query("select a.*,a.nik,a.nmlengkap,b.nmjabatan,c.nmdept from sc_mst.karyawan a
		left outer join sc_mst.jabatan b on a.jabatan=b.kdjabatan and a.subbag_dept=b.kdsubdept
		left outer join sc_mst.departmen c on a.bag_dept=c.kddept where coalesce(a.statuskepegawaian,'')='KO' order by a.tglkeluarkerja desc");
    }

    function q_finger(){
        return $this->db->query("select * from sc_mst.fingerprint order by kodecabang asc");
    }

    function q_kanwil(){
        return $this->db->query("select * from sc_mst.kantorwilayah order by desc_cabang asc");
    }

    function list_karyborong(){
        return $this->db->query("select a.*,a.nik,a.nmlengkap,b.nmjabatan,c.nmdept,0 as closegaji from sc_mst.karyawan a
		left outer join sc_mst.jabatan b on a.jabatan=b.kdjabatan
		left outer join sc_mst.departmen c on a.bag_dept=c.kddept where a.tjborong='t' and a.statuskepegawaian<>'KO'");
    }


    function get_datatables()
    {
        $builder = $this->_get_datatables_query();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }

    function count_filtered()
    {
        $builder = $this->_get_datatables_query();
        $query = $builder->get();
        return $query->getNumRows();
    }

    public function count_all()
    {
        $builder = $this->_get_datatables_query();
        return $builder->countAllResults();
    }

    public function get_dtl_id($id)
    {
        return $this->db->query("select * from sc_mst.lv_m_karyawan_candidate where nik='$id'");
    }

    public function get_by_id($id)
    {
        return $this->db->query("select * from  sc_mst.lv_m_karyawan_candidate where nik='$id'");
    }

    public function saves($data)
    {
        return $this->db->insert('sc_tmp.karyawan', $data);
        //return $this->db->insert_id();
    }

    public function save_foto($nip,$info)
    {
        $this->db->where('nik',$nip);
        $this->db->update('sc_mst.karyawan', $info);
    }

    public function updates($where, $data)
    {
        $this->db->update('sc_mst.karyawan', $data, $where);
        return $this->db->affected_getRows();
    }

    public function delete_by_id($id)
    {
        //$this->db->where('nik', $id);
        return $this->db->query("update sc_mst.karyawan set status='D' where nik='$id'");
    }

    function list_ptkp(){
        return $this->db->query("select * from sc_mst.status_nikah order by kdnikah");
    }

    function q_besaranptkp($status_ptkp){
        return $this->db->query("select cast(besaranpertahun as numeric(18,0)) from sc_mst.ptkp where kodeptkp='$status_ptkp'");

    }

    function q_regu(){
        return $this->db->query("select * from sc_mst.regu order by nmregu");

    }
    function q_wilayah_nominal($param){
        return $this->db->query("select * from sc_mst.m_wilayah_nominal where c_hold='NO' $param");
    }

    /* TABLE RESIGN KARYAWAN */
    var $t_resign_view = "sc_mst.lv_m_karyawan_candidate";
    var $t_resign_view_column = array('nik','nmlengkap','nmdept','nmsubdept','nmjabatan','tglkeluarkerja','nmgroupgol','locaname','alasan_resing');
    var $t_resign_view_order = array("tglkeluarkerja" => 'desc'); // default order
    private function _get_query_t_resign()
    {

        $this->db->where('resign','YES');
        $this->db->from($this->t_resign_view);
        $i = 0;

        foreach ($this->t_resign_view_column as $item)
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

                if(count($this->t_resign_view_column) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $this->db->order_by($this->t_resign_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_resign_view_order))
        {
            $order = $this->t_resign_view_order;
            foreach ($order as $key => $item){
                $this->db->order_by($key, $item);
            }
        }

    }


    function get_t_resign_view(){
        $this->_get_query_t_resign();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'],$_POST['start']);
        $query = $this->db->get();
        return $query->getResult();
    }


    function t_resign_view_count_filtered()
    {
        $this->_get_query_t_resign();
        $query = $this->db->get();
        return $query->getNumRows();
    }
    public function t_resign_view_count_all()
    {
        $this->db->from($this->t_resign_view);
        return $this->db->countAllResults();
    }
    public function get_t_resign_view_by_id($id)
    {
        $this->db->from($this->t_resign_view);
        $this->db->where('nik',$id);
        $query = $this->db->get();
        return $query->getRow();
    }


    /* RIWAYAT RIWAYAT ATRIBUTE */
    function q_riwayat_pendidikan($param){
        return $this->db->query("select * from (
                select a.*,b.nmpendidikan from sc_his.riwayat_pendidikan a
                left outer join sc_mst.pendidikan b on a.kdpendidikan=b.kdpendidikan) as x 
                where nik is not null $param order by id");
    }
    function q_m_pendidikan($param){
        return $this->db->query("select * from sc_mst.pendidikan where kdpendidikan is not null $param order by nmpendidikan desc");
    }

    /* KARTU PENDUKUNG ATRIBUTE */
    function q_kartupendukung($param){
        return $this->db->query("select * from (
                select a.*,b.nmkartu from sc_his.kartu_pendukung a
                left outer join sc_mst.kartu_pendukung b on a.kdkartu=b.kdkartu) as x 
                where nik is not null $param order by id");
    }
    function q_m_kartupendukung($param){
        return $this->db->query("select * from sc_mst.kartu_pendukung where kdkartu is not null $param order by nmkartu asc");
    }


    function q_ceknik($param){
        return $this->db->query("select nik from (
                                 select trim(nik) as nik from sc_mst.t_karyawan
                                 union all
                                 select trim(nikupdated) as nik from sc_mst.t_karyawan_candidate where status not in ('C','D')) as x
                                 where nik is not null $param
                                 group by nik");
    }


}
