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


namespace App\Models\Globals;
use CodeIgniter\Model;


class M_Gobals extends Model
{

    function getTrxerror($param = null)
    {
        $query = $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null $param");
        return $query;
    }
    function q_deltrxerror($param = null){
        $query = $this->db->query("delete from sc_mst.trxerror where userid is not null $param");
        return $query;
    }

    function ins_trxerror($userid,$errorcode,$nomorakhir1,$nomorakhir2,$modul){
        $this->db->where('userid',$userid);
        $this->db->where('modul',$modul);
        $this->db->delete('sc_mst.trxerror');
        /* error handling */
        $infotrxerror = array (
            'userid' => $userid,
            'errorcode' => $errorcode,
            'nomorakhir1' => $nomorakhir1,
            'nomorakhir2' => $nomorakhir2,
            'modul' => $modul,
        );
        $this->db->insert('sc_mst.trxerror',$infotrxerror);
        return $this->db->affected_rows();
    }

    function q_versidb($param = null){
        $query = $this->db->query("select * from sc_mst.version where kodemenu is not null and kodemenu='$param'");
        return $query;

    }

    /*GET BRANCH/COMPANY*/
    function q_branch(){
        $query = $this->db->query("select * from sc_mst.branch where coalesce(trim(cdefault),'')='YES'");
        return $query;
    }


    /* INITIALIZE BREADCRUMB */

    function q_menuprg($kodemenu){
        return $this->db->query("select * from (
                                    select a.*,b.namamenu as namaparent,c.namamenu as namaparentsub from sc_mst.menuprg a
                                    left outer join sc_mst.menuprg b on a.parentmenu=b.kodemenu
                                    left outer join sc_mst.menuprg c on a.parentsub=c.kodemenu) as x
                                    where kodemenu is not null and kodemenu='$kodemenu'");
    }

    function q_menuprg_ext($kodemenu)
    {
        return $this->db->query("select * from (
                                    select * from sc_mst.menuprg where trim(kodemenu) in (select trim(parentmenu) from sc_mst.menuprg where kodemenu='$kodemenu')
                                    union all
                                    select * from sc_mst.menuprg where trim(kodemenu) in (select trim(parentsub) from sc_mst.menuprg where kodemenu='$kodemenu')
                                    union all
                                    select * from sc_mst.menuprg where trim(kodemenu) in (select trim(kodemenu) from sc_mst.menuprg where kodemenu='$kodemenu')
                                    ) as x where kodemenu is not null order by trim(child) desc
        ");
    }
    function insert_version($kodemenu){
        $query = $this->db->query("insert into sc_mst.version
                                    (kodemenu)
                                values
                                    ('$kodemenu');");
        return $query;
    }

    function q_update_version($kodemenu,$data){
        $builder = $this->db->table("sc_mst.version");
        $builder->where('kodemenu',$kodemenu);
        return $builder->update($data);
    }

    function list_aksespermenu($nama,$kmenu){
        return $this->db->query(" select a.kodemenu,a.linkmenu,b.* from sc_mst.menuprg a join sc_mst.akses b on a.kodemenu=b.kodemenu 
								where b.nik='$nama' and a.kodemenu='$kmenu'");
    }


    /* ROLE POIN */
    function list_akses_role($roleid){
        return $this->db->query("select a.kodemenu,a.linkmenu,b.* from sc_mst.menuprg a join sc_mst.role_permissions b on a.kodemenu=b.kodemenu 
								where b.roleid='$roleid'");
    }

    /* END ROLE POIN */


    function q_trxerror($paramtrxerror){
        return $this->db->query("select * from (
								select a.*,b.description from sc_mst.trxerror a
								left outer join sc_mst.errordesc b on a.modul=b.modul and a.errorcode=b.errorcode) as x
								where userid is not null $paramtrxerror");
    }
    function q_trxtype($paramtrxtype){
        return $this->db->query("select coalesce(kdtrx,'') as kdtrx, coalesce (jenistrx,'') as jenistrx,coalesce(uraian,'') as uraian,trim(kdtrx) as id from sc_mst.trxtype where kdtrx is not null $paramtrxtype");
    }
    function q_trxstatus($paramtrxstatus){
        return $this->db->query("select coalesce(kdtrx,'') as kdtrx, coalesce (jenistrx,'') as jenistrx,coalesce(uraian,'') as uraian,trim(kdtrx) as id from sc_mst.trxstatus where kdtrx is not null $paramtrxstatus");
    }
    function q_master_branch(){
        return $this->db->query("select 
								coalesce(branch    ,'')::text as branch      ,
								coalesce(branchname,'')::text as branchname  ,
								coalesce(address   ,'')::text as address     ,
								coalesce(phone1    ,'')::text as phone1      ,
								coalesce(phone2    ,'')::text as phone2      ,
								coalesce(fax       ,'')::text as fax from sc_mst.branch where coalesce(cdefault,'')='YES'");
    }

    function q_customer($param){
        return $this->db->query("select *,coalesce(custcode,'') as id from sc_mst.customer where coalesce(custcode,'')!='' $param ");
    }

    function q_lv_m_karyawan($param){
        return $this->db->query("select *,coalesce(nik,'') as id from sc_mst.lv_m_karyawan where coalesce(statuskepegawaian,'')!='KO' $param ");
    }

    function q_kantorwilayah($param){
        return $this->db->query("select *,coalesce(kdcabang,'') as id from sc_mst.kantorwilayah where coalesce(kdcabang,'')!='' $param ");
    }

    function q_departmen($param){
        return $this->db->query("select *,coalesce(trim(kddept),'') as id from sc_mst.departmen where coalesce(trim(kddept),'')!='' $param ");
    }

    function q_subdepartmen($param){
        return $this->db->query("select *,coalesce(trim(kdsubdept),'') as id from sc_mst.subdepartmen where coalesce(trim(kdsubdept),'')!='' $param ");
    }
    function q_jabatan($param){
        return $this->db->query("select *,coalesce(trim(kdjabatan),'') as id from sc_mst.jabatan where coalesce(trim(kdjabatan),'')!=''  $param ");
    }
    function q_lvljabatan($param){
        return $this->db->query("select *,coalesce(trim(kdlvl),'') as id from sc_mst.lvljabatan where coalesce(trim(kdlvl),'')!=''  $param ");
    }
    function q_regu($param){
        return $this->db->query("select *,coalesce(trim(kdregu),'') as id from sc_mst.regu where coalesce(trim(kdregu),'')!=''  $param ");
    }
    function q_kelamin($param){
        return $this->db->query("select *,idkelamin as id from sc_mst.kelamin where idkelamin is not null $param");
    }
    function q_agama($param){
        return $this->db->query("select *,trim(kdagama) as id from sc_mst.agama where nmagama is not null $param");
    }
    function q_golongandarah($param){
        return $this->db->query("select *,trim(goldarah) as id from sc_mst.golongan_darah where nmgoldarah is not null $param");
    }
    function q_division($param){
        return $this->db->query("select *,trim(iddivision) as id from sc_mst.division where nmdivision is not null $param");
    }
    function q_costcenter($param){
        return $this->db->query("select *,trim(idcostcenter) as id from sc_mst.costcenter where nmcostcenter is not null $param");
    }
    function q_groupgol($param){
        return $this->db->query("select *,trim(idgroupgol) as id from sc_mst.groupgol where nmgroupgol is not null $param");
    }
    function q_mgudang($param){
        return $this->db->query("select *,trim(loccode) as id from sc_mst.mgudang where locaname is not null $param");
    }
    function q_statuspegawai($param){
        return $this->db->query("select *,trim(kdkepegawaian) as id from sc_mst.status_kepegawaian where nmkepegawaian is not null $param");
    }
    function q_ptkp($param){
        return $this->db->query("select *,trim(kodeptkp) as id from sc_mst.ptkp where kodeptkp is not null $param");
    }
    function q_pendidikan($param){
        return $this->db->query("select *,trim(kdpendidikan) as id from sc_mst.pendidikan where kdpendidikan is not null $param");
    }
    function q_bank($param){
        return $this->db->query("select *,trim(kdbank) as id from sc_mst.bank where kdbank is not null $param");
    }


    /* For Geolocation */
    function q_negara($param){
        return $this->db->query("select *,trim(kodenegara) as id from sc_mst.negara where namanegara is not null $param");
    }

    function q_provinsi($param){
        return $this->db->query("select *,trim(kodeprov) as id from sc_mst.provinsi where namaprov is not null $param");
    }

    function q_kotakab($param){
        return $this->db->query("select *,trim(kodekotakab) as id from sc_mst.kotakab where namakotakab is not null $param");
    }

    function q_kecamatan($param){
        return $this->db->query("select *,trim(kodekec) as id from sc_mst.kec where namakec is not null $param");
    }

    function q_keldesa($param){
        return $this->db->query("select *,trim(kodekeldesa) as id from sc_mst.keldesa where namakeldesa is not null $param");
    }

    function q_user_check(){

        $this->session = \Config\Services::session();
        $nik=trim($this->session->get('nik'));
        $username=$this->session->get('nama');
        return $this->db->query("select * from sc_mst.user where nik='$nik' and username='$username'");
    }

    function list_aksesperdep(){
        $this->session = \Config\Services::session();
        $nik=trim($this->session->get('nik'));
        return $this->db->query(" select * from sc_mst.karyawan where nik='$nik' and bag_dept='SDM' and subbag_dept in ('SDM05','SDM01')");
    }

    // WAREHOUSE AREA
    function q_mlocation($param){
        return $this->db->query("select idlocation,nmlocation,chold,description,idcomplex,idbarcode,inputdate,inputby,updatedate,updateby,coalesce(trim(idlocation),'') as id from sc_mst.mlocation where coalesce(trim(idlocation),'')!='' $param ");
    }

    function q_marea($param){
        return $this->db->query("select idlocation,idarea,nmarea,chold,status,description,idcomplex,idbarcode,idtype,inputdate,inputby,updatedate,updateby ,coalesce(trim(idarea),'') as id from sc_mst.marea where coalesce(trim(idarea),'')!='' $param ");
    }

    function q_mgroup($param){
        return $this->db->query("select idgroup,nmgroup,grouptype,chold,description,inputdate,inputby,updatedate,updateby,coalesce(trim(idgroup),'') as id from sc_mst.mgroup where coalesce(trim(idgroup),'')!='' $param ");
    }
    function q_msubgroup($param){
        return $this->db->query("select idsubgroup,nmsubgroup,grouptype,grouphold,keterangan,inputdate,inputby,updatedate,updateby,coalesce(trim(idsubgroup),'') as id from sc_mst.msubgroup where coalesce(trim(idsubgroup),'')!='' $param ");
    }

    function q_unit($param){
        return $this->db->query("select idunit,parentunit,ctype,conversion_value,chold,description,coalesce(trim(idunit),'') as id from sc_mst.unit where idunit is not null $param");
    }

    function q_supplier($param){
        return $this->db->query("select trim(nmsupplier) as id, nmsupplier from sc_mst.msupplier where coalesce(trim(chold),'NO')!='YES' $param order by nmsupplier asc");
    }

    function q_po_outstanding($param){
        return $this->db->query("select docno, docno as id from sc_trx.item_po_dtl
where docno is not null and status in ('A','S') $param
group by docno order by docno asc");
    }

    function q_username($param){
        return $this->db->query("select * from (select *,username as id from sc_mst.user) as x where username is not null $param");
    }


    function q_market($param){
        return $this->db->query("select *, trim(idmarket) as id from sc_mst.market where coalesce(trim(idmarket),'')!='' $param ");
    }

    
    function q_gradecust($param){
        return $this->db->query("select *, trim(kdgradecust) as id from sc_mst.gradecust where coalesce(trim(kdgradecust),'')!='' $param ");
    }

    
    function q_salesman($param){
        return $this->db->query("select a.*, k.namakotakab ,trim(a.kdsalesman) as id from sc_mst.salesman a left outer join sc_mst.kotakab k on k.kodekotakab=a.idkota  where coalesce(trim(kdsalesman),'')!='' $param ");
    }
    
    function q_kolektor($param){
        return $this->db->query("select *, trim(kdkolektor) as id from sc_mst.kolektor where coalesce(trim(kdkolektor),'')!='' $param ");
    }

    
    function q_coa($param){
        return $this->db->query("select *, trim(idcoa) as id from sc_mst.coa where coalesce(trim(idcoa),'')!='' $param ");
    }

    function q_currency($param){
        return $this->db->query("select *, trim(currcode) as id from sc_mst.currency where coalesce(trim(currcode),'')!='' $param ");
    }

    function q_customer_new($param){
        return $this->db->query("select *, trim(kdcustomer) as id from sc_mst.customer where coalesce(trim(kdcustomer),'')!='' $param ");
    }

}
