<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class M_User extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'musers';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];


    function dtl_user($iduser,$username){
        return $this->db->query("select *,to_char(expdate,'dd-mm-yyyy') as exdate from sc_mst.user where iduser='$iduser' and username='$username'")->getRowArray();
    }

    function list_user(){
        return $this->db->query("select a.*,b.locaname from sc_mst.user a left outer join sc_mst.mgudang b on a.loccode=b.loccode order by username asc");
    }

    function list_karyawan(){
        return $this->db->query("select * from sc_mst.t_karyawan where resign='NO' order by nmlengkap");
    }

    function list_lvljbt(){
        return $this->db->query("select * from sc_mst.lvljabatan order by kdlvl asc");
    }

    function q_lokasi_gudang(){
        return $this->db->query("select * from sc_mst.mlocation where coalesce(chold,'')!='YES' order by nmlocation");
    }
    function q_role(){
        return $this->db->query("select * from sc_mst.role where coalesce(chold,'')!='YES' order by rolename");
    }



    var $t_user_view = "(select a.*,to_char(a.inputdate,'dd-mm-yyyy hh24:mi:ss') as inputdate1,to_char(a.expdate,'dd-mm-yyyy hh24:mi:ss') as expdate1,b.nmlocation as locaname,c.rolename from sc_mst.user a
                        left outer join sc_mst.mlocation b on a.loccode=b.idlocation
                        left outer join sc_mst.role c on a.roleid=c.roleid
                        ) as x";
    var $t_user_view_column = array('nik','username','expdate1','hold_id','locaname');
    var $t_user_view_order = array("username" => 'asc'); // default order
    private function _get_query_t_user()
    {

        $builder = $this->db->table($this->t_user_view);
       // $builder->from('my_table');
        //$this->db->from($this->t_user_view);
        $i = 0;

        foreach ($this->t_user_view_column as $item)
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

                if(count($this->t_user_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_user_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_user_view_order))
        {
            $order = $this->t_user_view_order;
            foreach ($order as $key => $item){
                $builder->orderBy($key, $item);
            }
        }
        return $builder;
    }


    function get_t_user_view(){
        $builder = $this->_get_query_t_user();
        ////$this->_get_query_t_user();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_user_view_count_filtered()
    {
        $builder = $this->_get_query_t_user();
        ////$this->_get_query_t_user();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_user_view_count_all()
    {
        $builder = $this->_get_query_t_user();
        return $builder->countAllResults();
    }
    public function get_t_user_view_by_id($id)
    {
        $builder = $this->_get_query_t_user();
        $builder->where('id',$id);
        $query = $builder->get();
        return $query->getRow();
    }

    function cek_user($nama){
        return $this->db->query("select * from sc_mst.user where username='$nama'");
    }

    function getUser($param){
        $query = $this->db->query("select a.*,b.rolename from sc_mst.user a
        left outer join sc_mst.role b on a.roleid=b.roleid
        where username is not null $param ");
        return $query;
    }

}
