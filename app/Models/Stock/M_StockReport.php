<?php

namespace App\Models\Stock;

use CodeIgniter\Model;

class M_StockReport extends Model
{
    function q_stk_pergudang($param)
    {
        return $this->db->query("select a.*, b.nmlocation ,c.nmbarang,c.unit,d.idgroup,d.nmgroup from (
select 1 as urut, idlocation, batch,idbarang,null::timestamp without time zone as trxdate,'' as doctype,'' as docno,'' as docref,sum(0) as qty_in,sum(0) as qty_out,sum(qty_sld) as qty_sld from (
select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref,coalesce(a.qty_in,0.00) as qty_in,coalesce(a.qty_out,0.00) as qty_out,coalesce(a.qty_sld,0.00) as qty_sld from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,max(a.docref) as docref from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,max(a.docno) as docno from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,max(a.doctype) as doctype from sc_trx.stkblc a,
		(select idlocation,idarea,batch,idbarang,max(trxdate) as trxdate from sc_trx.stkblc 
		where idlocation is not null $param
		group by idlocation,idarea,batch,idbarang) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref) as x	
group by urut,idlocation,batch,idbarang) a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.mgroup d on c.idgroup=d.idgroup and d.grouptype='BRG'
where coalesce(a.qty_sld,0)>0
order by nmbarang,urut;
");
    }
//
//        function q_stk_pergudang_position($param){
//            return $this->db->query("select * from (
//select a.*,b.nmlocation,c.nmbarang,c.unit,d.nmarea,e.idgroup,e.nmgroup,b1.nmlocation as deflocation,d1.nmarea as defarea from sc_mst.stkgdw a
//left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
//left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
//left outer join sc_mst.marea d on a.idarea=d.idarea
//left outer join sc_mst.mgroup e on c.idgroup=e.idgroup and e.grouptype='BRG'
//left outer join sc_mst.mlocation b1 on c.deflocation=b1.idlocation
//left outer join sc_mst.marea d1 on c.defarea=d1.idarea) as x where idbarang is not null and coalesce(onhand,0)>0
//and coalesce(idgroup,'')!='' $param
//order by nmbarang,nmarea;
//");
//    }
    function q_stk_pergudang_position($param){
            return $this->db->query("select a.*,b.nmlocation,c.nmbarang,c.unit,d.nmarea,e.idgroup,e.nmgroup,b1.nmlocation as deflocation,d1.nmarea as defarea,a.qty_sld as onhand from (
select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref,coalesce(a.qty_in,0.00) as qty_in,coalesce(a.qty_out,0.00) as qty_out,coalesce(a.qty_sld,0.00) as qty_sld from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,max(a.docref) as docref from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,max(a.docno) as docno from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,max(a.doctype) as doctype from sc_trx.stkblc a,
		(select idlocation,idarea,batch,idbarang,max(trxdate) as trxdate from sc_trx.stkblc 
		where idlocation is not null $param
		group by idlocation,idarea,batch,idbarang) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref
) as a 
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.marea d on a.idarea=d.idarea
left outer join sc_mst.mgroup e on c.idgroup=e.idgroup and e.grouptype='BRG'
left outer join sc_mst.mlocation b1 on c.deflocation=b1.idlocation
left outer join sc_mst.marea d1 on c.defarea=d1.idarea
where coalesce(a.qty_sld,0)>0
");

    }

/*
FORMULASI SALDO AWAL

select 1 as urut, idlocation,idbarang,trxdate,'SA' as doctype,'',docref,'' as hist,'SALDO_AWAL' as description,balance-qty_out as qty_in,0 as qty_out,balance-qty_out AS balance from (
select idlocation,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum(qty_in-qty_out) over(partition by idlocation,idbarang order by idlocation,idbarang,trxdate,doctype,docno,docref) as balance
from sc_trx.stkblc ) as a
where idbarang is not null and idbarang='01040201000018' and to_char(trxdate,'yyyy-mm-dd') between '2022-02-03' and  '2022-02-03'

*/


    function q_kstockgdg($param) {
        return $this->db->query("select a.*,to_char(a.trxdate,'dd-mm-yyyy') as trxdate1,b.nmlocation,c.nmbarang,c.unit,c.subunit,d.idgroup,d.nmgroup from (".
            "select 1 as urut, idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,balance from (".
            "select idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum(". "qty_in"."-"."qty_out".") over(partition by idlocation,idbarang order by idlocation,idbarang,trxdate,doctype,docno,docref) as balance ".
            "from sc_trx.stkblc ) as a
where idbarang is not null $param
union all
select 2 as urut, idlocation,batch,idbarang,max(trxdate) as trxdate,'GT' AS doctype,'' as docno,'' AS docref,'' AS hist,'GRAND TOTAL' as description,sum(qty_in) as qty_in,sum(qty_out) as qty_out, balance from (
select idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum("."qty_in"."-"."qty_out".") over(partition by idlocation,idbarang order by idlocation,idbarang) as balance
from sc_trx.stkblc ) as a
where idbarang is not null $param
group by idlocation,batch,idbarang,balance) as a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.mgroup d on c.idgroup=d.idgroup and d.grouptype='BRG'
order by a.idlocation,a.batch,a.idbarang,a.urut,a.trxdate,a.docno,a.docref,a.doctype ");
    }

    function q_kstockgdg_2($param,$paramgroup) {
        return $this->db->query("select a.*,to_char(a.trxdate,'dd-mm-yyyy') as trxdate1,b.nmlocation,c.nmbarang,c.unit,c.subunit,d.idgroup,d.nmgroup from (".
            "select 1 as urut, idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,balance from (".
            "select idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum(". "qty_in"."-"."qty_out".") over(partition by idlocation,idbarang order by idlocation,idbarang,trxdate,doctype,docno,docref) as balance ".
            "from sc_trx.stkblc ) as a
where idbarang is not null $param
union all
select 2 as urut, idlocation,batch,idbarang,max(trxdate) as trxdate,'GT' AS doctype,'' as docno,'' AS docref,'' AS hist,'GRAND TOTAL' as description,sum(qty_in) as qty_in,sum(qty_out) as qty_out, balance from (
select idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum("."qty_in"."-"."qty_out".") over(partition by idlocation,idbarang order by idlocation,idbarang) as balance
from sc_trx.stkblc ) as a
where idbarang is not null $param
group by idlocation,batch,idbarang,balance) as a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.mgroup d on c.idgroup=d.idgroup and d.grouptype='BRG'
$paramgroup
order by a.idlocation,a.batch,a.idbarang,a.urut,a.trxdate,a.docno,a.docref,a.doctype ");
    }

    function q_ktstockgdg_3($param,$paramgroup,$tglawal,$tglakhir,$paramlocation){
        return $this->db->query("select a.*,to_char(a.trxdate,'dd-mm-yyyy') as trxdate1,b.nmlocation,c.nmbarang,c.unit,c.subunit,d.idgroup,d.nmgroup from (
select 1::numeric as urut, idlocation,batch,idbarang,trxdate,'SA' AS doctype,'' AS docno,'' AS docref,'SALDO_AWAL' as hist,'SALDO_AWAL' as description,sum(qty_sld) as qty_in,0 as qty_out,sum(qty_sld) as balance from 
(select a.*, b.nmlocation ,c.nmbarang,c.unit,d.idgroup,d.nmgroup from (
select 1::numeric as urut, idlocation, batch,idbarang,null::timestamp without time zone as trxdate,'' as doctype,'' as docno,'' as docref,sum(0) as qty_in,sum(0) as qty_out,sum(qty_sld) as qty_sld from (
select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref,coalesce(a.qty_in,0.00) as qty_in,coalesce(a.qty_out,0.00) as qty_out,coalesce(a.qty_sld,0.00) as qty_sld from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,max(a.docref) as docref from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,max(a.docno) as docno from sc_trx.stkblc a,
		(select a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,max(a.doctype) as doctype from sc_trx.stkblc a,
		(select idlocation,idarea,batch,idbarang,max(trxdate) as trxdate from sc_trx.stkblc 
		where idlocation is not null $paramlocation and to_char(trxdate,'yyyy-mm-dd')< '$tglawal' and trim(idbarang)||trim(batch) in (
		select trim(idbarang)||trim(batch) from sc_trx.stkblc where idbarang is not null $param)
		group by idlocation,idarea,batch,idbarang) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref
		group by  a.idlocation,a.idarea,a.batch,a.idbarang,a.trxdate,a.doctype,a.docno,a.docref) as b
		where a.idlocation=b.idlocation and a.idarea=b.idarea and a.batch=b.batch and a.idbarang=b.idbarang and a.trxdate=b.trxdate and a.doctype=b.doctype and a.docno=b.docno and a.docref=b.docref) as x	
group by urut,idlocation,batch,idbarang) a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.mgroup d on c.idgroup=d.idgroup and d.grouptype='BRG'
order by nmbarang,urut) as x
GROUP BY idlocation,batch,idbarang,trxdate
union all	
".
            "select 2::numeric as urut, idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,balance from (".
            "select idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum(". "qty_in"."-"."qty_out".") over(partition by idlocation,batch,idbarang order by idlocation,idbarang,trxdate,doctype,docno,docref) as balance ".
            "from sc_trx.stkblc ) as a
where idbarang is not null $param
union all
select 3::numeric as urut, idlocation,batch,idbarang,max(trxdate) as trxdate,'GT' AS doctype,'' as docno,'' AS docref,'' AS hist,'GRAND TOTAL' as description,sum(qty_in) as qty_in,sum(qty_out) as qty_out, balance from (
select idlocation,batch,idbarang,trxdate,doctype,docno,docref,hist,description,qty_in,qty_out,sum("."qty_in"."-"."qty_out".") over(partition by idlocation,batch,idbarang order by idlocation,idbarang) as balance
from sc_trx.stkblc ) as a
where idbarang is not null $param
group by idlocation,batch,idbarang,balance) as a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.mgroup d on c.idgroup=d.idgroup and d.grouptype='BRG'
$paramgroup
order by a.idlocation,a.idbarang,a.batch,a.urut,a.trxdate,a.docno,a.docref,a.doctype");
    }

    function q_lbm($param) {
        return $this->db->query("select * from (
select a.*,to_char(lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,b.nmlocation,c.nmarea,d.nmbarang,e.idgroup,e.nmgroup, 
f.val,f.valrc,round(f.valrc*a.onhandtrans) valnoppn,round(round(f.val/f.onhandpo)*a.onhandtrans) as vterima
	from sc_trx.item_bbm_dtl a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.marea c on a.idarea=c.idarea
left outer join sc_mst.mbarang d on a.idbarang=d.idbarang
left outer join sc_mst.mgroup e on d.idgroup=e.idgroup
left outer join sc_trx.item_po_dtl f on f.docno=a.docref and f.id=a.idpodtl
where a.docno in (select docno from sc_trx.item_bbm_mst where status in ('P','V','O')) and trim(coalesce(a.status,''))='P') as x where docno is not null 
$param
order by docno desc,lasttrxdate desc");
    }

    function q_lbk($param) {
        return $this->db->query("select * from (
select a.*,to_char(lasttrxdate,'dd-mm-yyyy') as lasttrxdate1,b.nmlocation,c.nmarea,d.nmbarang,e.nmcostcenter from sc_trx.item_bbk_dtl a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.marea c on a.idarea=c.idarea
left outer join sc_mst.mbarang d on a.idbarang=d.idbarang
left outer join sc_mst.costcenter e on a.idsection=e.idcostcenter
where a.docno in (select docno from sc_trx.item_bbk_mst where status in ('P','V','O'))  and trim(coalesce(a.status,''))='P') as x where docno is not null $param
order by docno,lasttrxdate");
    }

    function q_po_peritem($param){
        return $this->db->query("select * from (
select 1 as urut, docno,to_char(trxdate,'dd-mm-yyyy') as trxdate,idbarang,nmbarang,coalesce(onhandpo,0) as onhandpo,coalesce(onhandtranspo,0) as onhandtranspo,coalesce(onhanddirty,0) as onhanddirty,coalesce(sisapo,0) as sisapo,unit,description from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus,
(coalesce(onhandpo,0)- coalesce(onhandtranspo,0)) as sisapo, coalesce(onhanddirty,0) as kotor
from sc_trx.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x where docno is not null $param
union all
select 2 as urut, 'TTL' docno,null as trxdate,idbarang,nmbarang,sum(coalesce(onhandpo,0)) as onhandpo,sum(coalesce(onhandtranspo,0)) as onhandtranspo,sum(coalesce(onhanddirty,0)) as onhanddirty,sum(coalesce(sisapo,0)) as sisapo,''  as unit,'SUBTOTAL' from (select a.*,b.nmbarang, case when b.nmbarang is null then 'UNIDENTIFIED'
else 'IDENTIFIED' end as nmbarang_ident,to_char(trxdate,'dd-mm-yyyy') as trxdate1,to_char(senddate,'dd-mm-yyyy') as senddate1,z.uraian as nmstatus,
(coalesce(onhandpo,0)- coalesce(onhandtranspo,0)) as sisapo, coalesce(onhanddirty,0) as kotor
from sc_trx.item_po_dtl a 
left outer join sc_mst.mbarang b on trim(a.idbarang)=trim(b.idbarang)
left outer join sc_mst.trxtype z on trim(a.status)=trim(z.kdtrx) and trim(z.jenistrx)='I.D.B.2'
) as x where docno is not null $param
group by idbarang,nmbarang ) as x
order by idbarang,nmbarang,docno,urut");
    }

    /* STOCK OPNAME DARI MODUL STOCK OPNAME */
    function q_stock_opname($param){
        return $this->db->query("select * from (select a.*,b1.nmlocation,b2.nmarea,b2.description as nmarea_posisi,b3.nmbarang,z.uraian as nmstatus,to_char(lasttrxdate,'dd-mm-yyyy') as lasttrxdate1  from sc_trx.item_stock_opname a
left outer join sc_mst.mlocation b1 on a.idlocation=b1.idlocation
left outer join sc_mst.marea b2 on a.idarea=b2.idarea
left outer join sc_mst.mbarang b3 on a.idbarang=b3.idbarang
left outer join sc_mst.trxtype z on a.status=z.kdtrx and z.jenistrx='I.D.C.3') as x where docno is not null $param order by lasttrxdate desc");
    }

    function q_stock_opname_checklist_print($param){
        return $this->db->query("select * from (
select a.*,b.nmlocation,c.nmbarang,c.unit,d.nmarea,e.idgroup,e.nmgroup,b1.nmlocation as deflocation,d1.nmarea as defarea from sc_mst.stkgdw a
left outer join sc_mst.mlocation b on a.idlocation=b.idlocation
left outer join sc_mst.mbarang c on a.idbarang=c.idbarang
left outer join sc_mst.marea d on a.idarea=d.idarea
left outer join sc_mst.mgroup e on c.idgroup=e.idgroup and e.grouptype='BRG'
left outer join sc_mst.mlocation b1 on c.deflocation=b1.idlocation
left outer join sc_mst.marea d1 on c.defarea=d1.idarea) as x where idbarang is not null and coalesce(onhand,0)>0
and coalesce(idgroup,'')!='' 
and trim(idlocation)||trim(idarea)||trim(batch)||trim(idbarang) in (
select trim(idlocation)||trim(idarea)||trim(batch)||trim(idbarang) from sc_trx.stkblc 
where idlocation is not null $param
group by idlocation,idarea,batch,idbarang)
order by nmlocation,nmbarang,nmarea;

");
    }
}
