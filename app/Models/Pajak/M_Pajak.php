<?php

namespace App\Models\Pajak;

use CodeIgniter\Model;

class M_Pajak extends Model
{
    
    // var $t_front_pajak_view = "(select a.*,
    //     c.alamat as alamatsplr,
    //     c.nmsupplier as nmsplr,
    //     d.namakotakab AS nmkota,
    //     b.nmbranch as nmbranch,
    //     m.nmsalesman as nmsalesman,
    //     c.nmsupplier
    // from sc_trx.ndk a 
    // left outer join sc_mst.branchjob b on a.cabang=b.idbranch
    // left outer join sc_mst.mstsupplier c on a.kdsupplier=c.kdsupplier
    // left outer join sc_mst.salesman m on a.kdsalesman=m.kdsalesman
    // left outer join sc_mst.kotakab d on c.idkota=d.kodekotakab) as x";
    var $t_front_pajak_view_column = array('docno','docdate','currcode','keterangan');
    var $t_front_pajak_view_order = array('docdate' => 'desc'); // default order
    private function _get_query_front_pajak()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $type = $this->request->getPost('typefilter');

        if ($type == 'PEMBELIAN') {

            $this->t_front_pajak_view = "(

                SELECT 
                    a.docno,
                    l.docdate,
                    l.jthtempo,
                    l.kdsupplier as kdperusahaan,
                    s.nmsupplier as nmperusahaan,
                    l.alamatsupplier as alamat,
                    a.idbarang,
                    a.nmbarang,
                    a.descriptionpo as keterangan,
                    a.nilai,
                    a.harga,
                    a.nilaikonversi,
                    a.nilaipajak,
                    a.currcode,
                    a.kurs,
                    a.idtax,
                    a.qty,
                    a.unit,
                    '' as nmsalesman

                FROM sc_trx.lpb_dtl a
                left join sc_trx.lpb l on l.docno = a.docno
                left join sc_mst.mstsupplier s on s.kdsupplier = l.kdsupplier

            ) as x";

        } else {

            $this->t_front_pajak_view = "(

                SELECT 
                    a.docno,
                    p.docdate,
                    p.jthtempo,
                    p.kdcustomer as kdperusahaan,
                    c.nmcustomer as nmperusahaan,
                    p.alamatcustomer as alamat,
                    a.idbarang,
                    a.nmbarang,
                    a.description as keterangan,
                    a.nilaikonversi,
                    a.nilaipajak,
                    a.currcode,
                    a.kurs,
                    a.idtax,
                    a.qty,
                    a.unit,
                    a.nilai,
                    a.harga,
                    '' as nmsalesman

                FROM sc_trx.penjualan_dtl a
                left join sc_trx.penjualan p on p.docno = a.docno
                left join sc_mst.customer c on c.kdcustomer = p.kdcustomer

            ) as x";

        }
       
        $loccode=trim($this->session->get('loccode'));
        $nama=trim($this->session->get('nama'));

        $builder = $this->db->table($this->t_front_pajak_view);
        // $builder->join(
        //     "(SELECT DISTINCT ON (kdtrx) kdtrx, uraian 
        //     FROM sc_mst.trxtype 
        //     WHERE jenistrx = 'I.P.A.2' 
        //     ORDER BY kdtrx, uraian DESC) AS trx", 
        //     "COALESCE(x.status, '') = COALESCE(trx.kdtrx, '')", 
        //     "left"
        // );
        $builder->select("x.*");
        
        $tglrange = $this->request->getPost('tglrange');
        if (!empty($tglrange)) {
            $dates = explode(' - ', $tglrange);

            if (count($dates) == 2) {

                $start = \DateTime::createFromFormat(
                    'd-m-Y',
                    trim($dates[0])
                )->format('Y-m-d');

                $end = \DateTime::createFromFormat(
                    'd-m-Y',
                    trim($dates[1])
                )->format('Y-m-d');

                $builder->where('docdate >=', $start . ' 00:00:00');
                $builder->where('docdate <=', $end . ' 23:59:59');
            }
        }

        
        // $builder->where('inputby', $nama);

        $i = 0;

        //$builder->where("docno = '$nama'");
        foreach ($this->t_front_pajak_view_column as $mrpgroup)
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $builder->groupStart(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $builder->like("upper(cast(" . strtoupper($mrpgroup) . " as varchar))", strtoupper($_POST['search']['value']));
                }
                else
                {
                    $builder->orLike("upper(cast(" . strtoupper($mrpgroup) . " as varchar))", strtoupper($_POST['search']['value']));
                }

                if(count($this->t_front_pajak_view_column) - 1 == $i) //last loop
                    $builder->groupEnd(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            if ($_POST['order']['0']['column']!= 0){ //diset klo post column 0
                $builder->orderBy($this->t_front_pajak_view_column[$_POST['order']['0']['column']-1], $_POST['order']['0']['dir']);
            }
        }
        else if(isset($this->t_front_pajak_view_order))
        {
            $order = $this->t_front_pajak_view_order;
            foreach ($order as $key => $mrpgroup){
                $builder->orderBy($key, $mrpgroup);
            }
        }
        return $builder;
    }


    function get_t_front_pajak_view(){
        $builder = $this->_get_query_front_pajak();
        ////$this->_get_query_t_mstd_usage();
        if($_POST['length'] != -1)
            $builder->limit($_POST['length'],$_POST['start']);
        $query = $builder->get();
        return $query->getResult();
    }


    function t_front_pajak_view_count_filtered()
    {
        $builder = $this->_get_query_front_pajak();
        ////$this->_get_query_t_ndk();
        $query = $builder->get();
        return $query->getNumRows();
    }
    public function t_front_pajak_view_count_all()
    {
        $builder = $this->_get_query_front_pajak();
        return $builder->countAllResults();
    }
    public function get_t_front_pajak_view_by_id($id)
    {
        $builder = $this->_get_query_front_pajak();
        $builder->where('idmrpgroup',$id);
        $query = $builder->get();
        return $query->getRow();
    }

}