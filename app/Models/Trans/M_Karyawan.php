<?php

namespace App\Models\Trans;

use CodeIgniter\Model;
$this->request = \Config\Services::request();

class M_Karyawan extends Model
{

    function list_karyawan(){
        return $this->db->query("select *,' '||id_ktp||' ' as noktp,' '||nokk||' ' as idkk,' '||id_npwp||' ' as nonpwp,' '||id_rekening||' ' as norekening,
        ' '||nohp1||' ' as hp1,' '||nohp2||' ' as hp2,' '||nik||' ' as nik1 from sc_mst.lv_m_karyawan where nik is not null and coalesce(resign,'') !='YES' order by nmlengkap asc");
    }

    function list_karyawan_param2($param){
        return $this->db->query("select *,' '||id_ktp||' ' as noktp,' '||nokk||' ' as idkk,' '||id_npwp||' ' as nonpwp,' '||id_rekening||' ' as norekening,
                                 ' '||nohp1||' ' as hp1,' '||nohp2||' ' as hp2,''''||nik as nik1,
                                date_part('year',age(now(), tgllahir::timestamp))::text as umurkaryawan,age(now()::date, tglmasukkerja::date)::text as lama_bekerja,column1 as pernahcovid
                                from sc_mst.lv_m_karyawan where nik is not null and left(nik,1) <> 'A' $param order by nmlengkap asc");
    }
    function list_karyawan_param($param){
        return $this->db->query("select * from sc_mst.lv_m_karyawan where nik is not null $param ");
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


    function cek_exist($nik){
        return $this->db->query("select * from sc_mst.karyawan where nik='$nik'")->getNumRows();
    }

    var $table = 'sc_tmp.karyawan';
    var $tablemst = 'sc_mst.lv_m_karyawan_trans';
    var $column = array('nik','nmlengkap','cimage','nmdept','nmsubdept','nmjabatan','tglmasukkerja','plant');
    var $order = array('nmlengkap' => 'asc');


    private function _get_datatables_query()
    {
        $this->request = \Config\Services::request();
        $builder = $this->db->table($this->tablemst);
        if($this->request->getPost('tglrange')) {
            $tgl=explode(' - ',$this->request->getPost('tglrange'));
            $tgl1= date('Y-m-d',strtotime($tgl[0]));
            $tgl2= date('Y-m-d',strtotime($tgl[1]));
            $builder->where("tglmasukkerja BETWEEN '$tgl1' AND '$tgl2'");
        }
        $builder->where(array('resign <>' => 'YES'));
        $builder->where(array('left(nik,1) <>' => 'A'));
        if($this->request->getPost('statuskepegawaianfilter')) {
            $status = trim($this->request->getPost('statuskepegawaianfilter'));
            $builder->where(array('statuskepegawaian =' => $status));
        }

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
        return $this->db->query("select * from sc_mst.lv_m_karyawan where nik='$id'");
    }

    public function get_by_id($id)
    {
        return $this->db->query("select * from  sc_mst.lv_m_karyawan where nik='$id'");
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
    var $t_resign_view = "sc_mst.lv_m_karyawan_trans";
    var $t_resign_view_column = array('nik','nmlengkap','nmdept','nmsubdept','nmjabatan','tglkeluarkerja','nmgroupgol','locaname','alasan_resign');
    var $t_resign_view_order = array("tglkeluarkerja" => 'desc'); // default order
    private function _get_query_t_resign()
    {

        $builder = $this->db->table($this->t_resign_view);
        $builder->where('resign','YES');
        $i = 0;

        foreach ($this->t_resign_view_column as $item)
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

                if(count($this->t_resign_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_resign_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_resign_view_order))
        {
            $order = $this->t_resign_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }

        return $builder;

    }


    function get_t_resign_view(){
        $builder = $this->_get_query_t_resign();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_resign_view_count_filtered()
    {
        $builder = $this->_get_query_t_resign();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_resign_view_count_all()
    {
        $builder = $this->_get_query_t_resign();
        return $builder->countAllResults();
    }
    public function get_t_resign_view_by_id($id)
    {
        $builder = $this->_get_query_t_resign();
        $builder->where('nik',$id);
        $query = $builder->get();
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
    function q_covidtrack($param)
    {
        return $this->db->query("select * from (
                                    select trim(a.docno) as docno1,trim(a.nik) as nik1,a.*,to_char(a.docdate,'dd-mm-yyyy')as docdate1,to_char(a.tglawal,'dd-mm-yyyy')as tglawal1,to_char(a.tglakhir,'dd-mm-yyyy')as tglakhir1,to_char(a.tglawal,'dd/mm/yyyy')as tglawal2,
                                    b.nmlengkap,b.nikatasan1,b.nikatasan2,b.nikatasan3,b.nmdept,b.nmsubdept,b.nmjabatan,b.nmgrade,b.nmdivision,b.nmgroupgol,b.nmkepegawaian,
                                    b.id_gol,z.uraian as nmstatus,z1.uraian as nmstatusapprove,b.id_dept,b.plant,b.locaname,b.costcenter,b.nmcostcenter,b.statuskepegawaian,c.nmtypecuti,trim(c.nmtypecuti) as nmtypecuti1,trim(a.inputby) as inputby1
                                    from sc_trx.abscut a
                                    left outer join sc_mst.lv_m_karyawan_trans b on a.nik=b.nik
                                    left outer join sc_mst.mtypecuti c on a.idtypecuti=c.idtypecuti
                                    left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='LEMBUR'
                                    left outer join sc_mst.trxtype z1 on a.statusapprove=z1.kdtrx and z1.jenistrx='LEMBUR') as x
                                    where docno is not null $param order by tglawal desc");
    }


}
