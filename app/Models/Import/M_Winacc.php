<?php

namespace App\Models\Import;

use CodeIgniter\Model;

class M_Winacc extends Model
{
    function q_im_lap_pp_win($param){
        $query = $this->db->query("select * from (
select 
trim(pp) as pp,
trim(jurnal) as jurnal,
trim(date_) as date_,
trim(username) as username,
trim(brg) as brg,
trim(qty) as qty,
trim(lns) as lns,
trim(gdg) as gdg,
trim(nama_user) as nama_user,
trim(nama_barang) as nama_barang,
trim(unit) as unit,
trim(spec) as spec,
trim(job) as job,
trim(rem_pp) as rem_pp,
trim(rem_pd) as rem_pd,
trim(date_pakai) as date_pakai,
trim(uniqueidpd) as uniqueidpd,
trim(status) as status,
trim(inputby) as inputby,
inputdate as inputdate,
id as id
from sc_im.lap_pp_win) as x where pp is not null $param order by date_::date desc");
        return $query;
    }

    function q_im_lap_po_win($param){
        $query = $this->db->query("select * from (
select 
trim(obl) as obl,
trim(jurnal) as jurnal,
trim(date_) as date_,
trim(sub) as sub,
trim(brg) as brg,
trim(gdgobl) as gdgobl,
trim(qty) as qty,
trim(lns) as lns,
trim(val) as val,
trim(job) as job,
trim(unit) as unit,
trim(uniqueidobd) as uniqueidobd,
trim(nama_supplier) as nama_supplier,
trim(nama_barang) as nama_barang,
trim(jobname) as jobname,
trim(priceobl) as priceobl,
trim(rempd) as rempd,
trim(tglkirim) as tglkirim,
trim(status) as status,
trim(inputby) as inputby,
inputdate as inputdate,
id as id
from sc_im.lap_po_win) as x where obl is not null $param order by date_::date desc");
        return $query;
    }

    function q_im_lap_penerimaan_win($param){
        $query = $this->db->query("select * from (
select 
trim(tbl) as tbl,
trim(date_) as date_,
trim(duedate) as duedate,
trim(cur) as cur,
trim(kurs) as kurs,
trim(sub) as sub,
trim(subname) as subname,
trim(slsm) as slsm,
trim(slsmname) as slsmname,
trim(brg) as brg,
trim(brgname) as brgname,
trim(qty) as qty,
trim(qtybonus) as qtybonus,
trim(valbonus) as valbonus,
trim(valwithdiscval) as valwithdiscval,
trim(discval) as discval,
trim(ppnval) as ppnval,
trim(job) as job,
trim(jobname) as jobname,
trim(unit) as unit,
trim(uniqueid) as uniqueid,
trim(posisistock) as posisistock,
trim(nofaktur) as nofaktur,
trim(biaya) as biaya,
trim(biaya2) as biaya2,
trim(disc1) as disc1,
trim(disc2) as disc2,
trim(pcl) as pcl,
trim(pclname) as pclname,
trim(rem) as rem,
trim(npwp) as npwp,
trim(nobatch) as nobatch,
trim(expired_date) as expired_date,
trim(kadar) as kadar,
trim(date_lulus) as date_lulus,
trim(penanggungjw) as penanggungjw,
trim(nomor) as nomor,
trim(nomor_po) as nomor_po,
trim(status) as status,
trim(inputby) as inputby,
inputdate as inputdate,
id as id
from sc_im.lap_penerimaan_win) as x where tbl is not null $param order by date_::date desc");
        return $query;
    }
}
