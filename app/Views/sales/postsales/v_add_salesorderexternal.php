<?php
/**
 * *
 *  * Created by PhpStorm.
 *  *  * User: FIKY-PC
 *  *  * Date: 4/29/19 1:34 PM
 *  *  * Last Modified: 12/18/16 10:51 AM.
 *  *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  *  Copyright© 2019 .All rights reserved.
 *  *
 *
 */

/*winacc
1. penomoran state, mengambil no terakhir, ketika disimpan melihat no terakhir
2. ketika ada penghapusan no terakhir(tetap yang utama) state terakhir harus disimpan .
3. ketika dihapus user bisa merubah dengan nomor yg dihapus sendiri
4. ketikan nomor sudah terbentuk,
*/
?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet"> -->

<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
    $(function() {
        $("#example1").dataTable();
        $("#dateinput").datepicker();
    });
</script>
<style>
    /* .table-condensed thead tr:nth-child(4),
    .table-condensed tbody {
    display: none
    } */

    .text-wrap{
        white-space:normal;
    }
    .width-90{  
        width:90px;
    }
    .width-150{
        width:150px;
    }
        .width-200{
        width:200px;
    }
    /* Untuk browser berbasis WebKit (Chrome, Safari, Edge terbaru) */
    .table-wrapper::-webkit-scrollbar {
        height: 6px; /* Lebar scrollbar horizontal */
        width: 6px;  /* Lebar scrollbar vertikal */
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #888; /* Warna scrollbar */
        border-radius: 10px; /* Ujung scrollbar melengkung */
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555; /* Warna scrollbar saat hover */
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1; /* Warna latar track scrollbar */
    }


    .section-block {
        background-color: #f9f9fc;
        border-left: 4px solid #007bff;
        /* border-bottom: 4px solid #007bff;  */
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                    box-shadow 0.4s ease, 
                    border-color 0.4s ease;
        
        transform: scale(1);
        will-change: transform;
    }

    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 8px;
    }

    .section-block:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .tooltip-inner {
        background-color: #333 !important;
        color: #fff !important;
        max-width: 250px; /* Default ukuran lebih kecil */
        padding: 10px;
        text-align: left;
        border-radius: 8px;
        font-size: 14px;
    }

    /* Khusus untuk tooltip "Jenis Object Pembelian" */
    #objcapex + label .tooltip-inner {
        max-width: 600px !important;
    }

    /* Tooltip default */
    .tooltip-small .tooltip-inner {
        max-width: 250px;
    }

    /* Tooltip besar hanya untuk "Jenis Object Pembelian" */
    .tooltip-large .tooltip-inner {
        max-width: 600px;
    }
    /* .select2-container, .selectize-input, .selectize-input input{
        min-height: 20px;
    }

    .select2-container {
        display: block;
        height: 20px!important;
        font-size: 14px;
        line-height: 26px;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 0px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        padding: 0px;
        height: 20px!important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 35px;
    }

    .select2-container--classic .select2-selection--single {
        height: 22px;
    }

    .select2-container--default .select2-selection--multiple {
    min-height: 20px !important;
    padding: 0 !important;
}
    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        height:20px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 18px;
    right: 6px;
    } */
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<?php echo $message; ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Informasi Umum Sales Order</h3>
    </div>
    <form  id="salesOrderForm">
        <div class="card-body">
            <div class="row align-items-start"> 
                <!-- Role/Job -->
                <div class="form-group col-md-2">
                    <label for="rolejob">Role/Job <span style="color:red">*</span></label>
                    <select name="rolejob" id="rolejob" class="form-control">
                        <option value="" selected disabled>- Choose Role/Job -</option>
                        <option value="JTS">JTS</option>
                        <option value="MSMI">MSM INDONESIA</option>
                        <option value="MSMJ">MSM JAPAN</option>
                    </select>
                </div>
                <div class="form-group col-md-10"></div>
                <!-- Docno Fields -->
            
                <div class="col-md-3 d-flex mt-4">
                    <div class="form-group me-1">
                        <input type="text" name="prefix" id="prefix" class="form-control" 
                            style="text-transform: uppercase;"
                            pattern="[A-Z0-9-]+"
                            title="Hanya huruf kapital, angka, dan strip">
                    </div>
                    <span class="px-2">/</span>
                    <div class="form-group mx-1">
                        <input type="text" name="infix" id="infix" class="form-control" readonly>
                    </div>
                    <span class="px-2">/</span>
                    <div class="form-group ms-1">
                        <input type="text" name="sufix" id="sufix" class="form-control" 
                            style="text-transform: uppercase;"
                            pattern="[A-Z0-9]+"
                            title="Hanya huruf kapital dan angka">
                    </div>
                </div>

                <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20" style="text-transform: uppercase;"  placeholder="No. Jurnal/Dokumen" readonly>
                <div class="form-group col-md-2">
                    <label for="docdate">Tanggal Dokumen/Periode</label>
                    <input type="text" class="form-control" id="docdate" name="docdate" data-date-format="dd-mm-yyyy"  placeholder="Tanggal Dokumen/Periode" style="text-transform: uppercase;"  required>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cust">Customer/Destination</label>
                        <select class="form-control" id="cust" name="cust"></select>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="address">Alamat Customer</label>
                    <textarea type="text" class="form-control" id="address"  name="address"  placeholder="Alamat Kantor" style="text-transform: uppercase;"  required readonly></textarea>
                </div>
                <div class="form-group col-md-2">
                    <label for="phone">No. Telp</label>
                    <input type="text" class="form-control" id="phone"  name="phone"  placeholder="No. Telepon" style="text-transform: uppercase;"  required readonly>
                </div>
                <div class="form-group col-md-2">
                    <label for="fax">Fax</label>
                    <input type="text" class="form-control" id="fax"  name="fax"  placeholder="Fax" style="text-transform: uppercase;"  required readonly>
                </div>
                <div class="form-group col-md-2">
                    <label for="pic">PIC</label>
                    <input type="text" class="form-control" id="pic"  name="pic"  placeholder="Kontak PIC" style="text-transform: uppercase;"  required readonly>
                </div>
                <div class="form-group col-md-5"></div>
                <div class="form-group  col-md-4">
                    <label for="trader">Trader</label>
                        <textarea name="trader" id="trader" class="form-control" rows="2" placeholder="Write Trader" style="text-transform: uppercase;" ></textarea>
                </div>
                <div class="form-group  col-md-4">
                    <label for="enduser">End User</label>
                        <textarea name="enduser" id="enduser" class="form-control" rows="2" placeholder="Write End User" style="text-transform: uppercase;" ></textarea>
                </div>
                <div class="form-group  col-md-4">
                    <label for="pocust">PO Customer</label>
                        <input type="text" class="form-control" id="pocust"  name="pocust"  placeholder="Write PO Customer" style="text-transform: uppercase;"  required>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select class="form-control" id="currency" name="currency"></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exchangerate">Exchange Rate</label>
                        <input type="text" class="form-control ratakanan" id="exchangerate"  name="exchangerate"  placeholder="Write Exchange"  required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="desc">Description</label>
                        <textarea type="text" class="form-control" id="desc"  name="desc"  placeholder="Write Description" style="text-transform: uppercase;"  required></textarea>
                    </div>
                </div>
            </div>
        </div>
        <?php if (empty($dtldata)) { ?>
            <div class="card-footer">
                <span class="float-left" style="color: red;">Klik button proses terlebih dahulu untuk melanjutkan pengisian item Sales Order</span>
                <button type="submit" onclick="submitSalesOrder()" class="btn btn-primary float-right"><i class="fa fa-repeat"></i> Proses</button>
            </div>
        <?php } ?>
    </form>
</div>  

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Detail Item Sales Order</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($dtldata) && isset($dtldata['status']) && trim($dtldata['status']) === 'E') { ?>
        <div class="row" id="updateSection">
            <!-- load item -->
            <div class="form-group col-md-2">
                <div class="input-group">
                        <!-- <button type="button" id="btn_insert_" onclick="insertdetailsikbsp()" class="btn btn-success form-control float-right"><i class="fa fa-plus"></i> Insert Detail</button> -->
                        <button type="button" id="btn_insert_" onclick="insertNewSalesOrder()" class="btn btn-success form-control btn-insert-new float-right"><i class="fa fa-plus"></i> Insert Detail Item</button>

                </div>
            </div>
            <!-- standart usage -->
            <div class="col-md-4">
                <div class="form-group float-left">
                    <div class="input-group">
                        <button type="button" id="btn_add_item" onclick="openAddNewItemModal()" 
                                class="btn btn-info btn-block">
                            <i class="fa fa-plus-square"></i> Add New Master Item
                        </button>
                    </div>
                    <small style="color: red; font-style: italic; display: block; margin-top: 5px;">
                        Apabila item yang mau disertakan belum ada di list, tambahkan terlebih dahulu melalui button ini
                    </small>
                </div>
            </div>

            <div class="col-sm-6 ">
                <!--<form id="formLoadLpbWin">-->
                <div class="form-group float-right">
                    <div class="input-group">
                            <button href="#" id="update_item" class="btn btn-primary float-left" style="margin-left:5px"><i class="fa fa-gear"></i> Update </button>
                            <button href="#" id="delete_item" class="btn btn-danger float-left" style="margin-left:5px"><i class="fa fa-trash"></i> Delete </button>

                    </div>
                </div>
                <!--</form>-->
            </div>
            <!-- <div class="form-group col-md-1 ">
                <div class="input-group">
                    <button type="button" id="btnload_mrp_dtl" onclick="load_mrp_dtl_outstanding()" class="btn btn-primary form-control float-right"><i class="fa fa-repeat"></i> Load MRP</button>
                </div>
            </div> -->
        </div>
        <?php } ?>

        <div class="row">
            <div class="col-sm-12">
                <label for="charge"></label>
            </div>
            <div class="col-sm-12">
                <form id="frm-example" action="#" method="POST">
                    <div class="table-wrapper" style="overflow-x: auto;">
                        <table id="t_salesorder_dtl" class="table table-bordered table-striped"  style="text-wrap:nowrap"  cellspacing="0" >
                            <thead>
                            <tr>
                                <td style="min-width: 20px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Act</td>
                                <td style="min-width: 350px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Nama Barang</td>
                                <td style="min-width: 100px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Grade</td>
                                <td style="min-width: 100px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Size Diameter</td>
                                <td style="min-width: 100px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Cut Length (MM)</td>
                                <td style="min-width: 100px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Qty (MT)</td>
                                <td style="min-width: 100px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Unit</td>
                                <td style="min-width: 140px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Harga (US/MT)</td>
                                <td style="min-width: 140px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Harga (Rp)/Kg</td>
                                <td style="min-width: 140px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Ex. Rate</td>
                                <td style="min-width: 180px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Total (Rp)</td>
                                <td style="min-width: 180px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">ETD</td>
                                <td style="min-width: 180px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Order Number (MSR)</td>
                                <td style="min-width: 200px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Remark</td>
                                <td style="min-width: 180px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Spec No.</td>
                                <td style="min-width: 140px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Total Delivery</td>
                                <td style="min-width: 140px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Balance Order (BO)</td>

                                <td style="min-width: 150px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Delivery List</td>
                                <!-- <td rowspan="2" style="width: 100px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">USD/MT</td>
                                <td rowspan="2" style="width: 200px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Description</td> -->
                            </tr>
                            <!-- <tr>
                                <td style="width: 75px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Weight</td>
                                <td style="width: 75px;font-weight: bolder; background-color: darkgrey;text-align: center; vertical-align: middle">Unit</td>
                            </tr> -->
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" rowspan="7" style="vertical-align:top; background:#f0f0f0; padding:10px;">
                                        <label for="remark" style="font-weight:bold;">Remarks</label>
                                        <textarea name="remark" id="remark" class="form-control" rows="8" 
                                                placeholder="Write Remarks"
                                                style="text-transform: uppercase; width:100%; margin-top:5px;"></textarea>
                                        <label for="paymentmethod" style="font-weight:bold;margin-top:25px">Payment Method</label>
                                        <textarea name="paymentmethod" id="paymentmethod" class="form-control" rows="4" 
                                                placeholder="Write Payment Method"
                                                style="text-transform: uppercase; width:100%; margin-top:5px;"></textarea>
                                    </td>
                                    <!-- <td colspan="9" rowspan="7" style="vertical-align:top; background:#f0f0f0; padding:10px;">
                                    </td> -->
                                    <td colspan="1" style="font-weight:bold;">Gross Sales</td>
                                    <td colspan="1">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="grosssales" name="grosssales"
                                                class="form-control ratakanan jtsseparator"
                                                value="0" disabled>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-weight:bold;">Down Payment</td>
                                    <td colspan="1">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="downpayment" name="downpayment"
                                                class="form-control ratakanan jtsseparator"
                                                value="0">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-weight:bold;">Net Sales</td>
                                    <td colspan="1">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="netsales" name="netsales"
                                                class="form-control ratakanan jtsseparator"
                                                value="0" disabled>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-weight:bold;">Tax Basis</td>
                                    <td colspan="1">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="taxbasis" name="taxbasis"
                                                class="form-control ratakanan jtsseparator"
                                                value="0" disabled>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-weight:bold;">V.A.T (12%)</td>
                                    <td colspan="1">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="vat" name="vat"
                                                class="form-control ratakanan jtsseparator"
                                                value="0" disabled>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" style="font-weight:bold;">PPh 22
                                        <i class="bi bi-question-circle text-primary tooltip-icon" data-bs-toggle="tooltip" data-html="true"
                                            data-tooltip-content="Tarif PPh 22 atas Penjualan Hasil Produksi Tertentu. <br>
                                            <b> Baja: 0.3% dari DPP PPN </b>">
                                        </i></td>
                                    <td colspan="1">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="pph22" name="pph22"
                                                class="form-control ratakanan jtsseparator"
                                                value="0">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1" class="bg-warning" style="font-weight:bold;">Total</td>
                                    <td colspan="1" class="bg-warning" style="">
                                        <div style="display:flex; align-items:center;">
                                        <span style="font-weight: bold; margin-right:5px;">Rp</span>
                                        <input type="text" id="ttlprice" name="ttlprice"
                                                class="form-control ratakanan jtsseparator"
                                                value="0" disabled
                                                style="font-weight:bold;">
                                        </div>
                                    </td class="bg-warning">
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('sales/postsales/clearEntrySalesOrder') ?>" 
            onclick="return confirm('This data will not save, are u sure?')" 
            class="btn btn-default float-left">
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <button type="submit"  onclick="finalEntrySalesOrder()" class="btn btn-success float-right">
            <i class="fa fa-arrow-right"></i> Submit Final Entry
        </button>
    </div>
</div>

<!-- <div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Informasi Pembayaran</h3>
    </div>
        <div class="card-body">
            <div class="row">
                <input type="hidden" name="grossales" id="grossales">
                <div class="form-group col-md-2">
                    <label for="bank">Bank</label>
                    <select class="form-control" id="bank" name="bank" 
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required>
                    </select>
                </div>

                
                <div class="form-group col-md-2">
                    <label for="nmbank">Nama Bank</label>
                    <input type="text" class="form-control" id="nmbank" name="nmbank" maxlength="100" placeholder="Nama Bank"
                    style="text-transform: uppercase;"
                    readonly required>
                </div>
                
                <div class="form-group col-md-4">
                    <label for="alamatbank">Alamat Bank</label>
                    <textarea class="form-control" id="alamatbank" name="alamatbank" placeholder="Alamat Bank"
                        style="text-transform: uppercase;"
                        readonly required></textarea>
                </div>

                <div class="form-group col-md-4">
                    <label for="accname">Nama Akun Bank</label>
                    <input class="form-control" id="accname" name="accname" placeholder="Nama Akun Bank"
                        style="text-transform: uppercase;"
                        readonly required>
                </div>

                <div class="form-group col-md-4">
                    <label for="accno">No. Akun</label>
                    <textarea name="accno" id="accno" class="form-control" rows="2" placeholder="No. Akun"
                        style="text-transform: uppercase;"
                        readonly> </textarea>
                </div>

                <div class="form-group col-md-4">
                    <label for="swiftcode">Swift Code</label>
                    <input class="form-control" id="swiftcode" name="swiftcode" placeholder="Swift Code"
                        style="text-transform: uppercase;"
                        readonly required>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="<?= base_url('sales/postsales/clearEntrySalesOrder') ?>" 
                onclick="return confirm('This data will not save, are u sure?')" 
                class="btn btn-default float-left">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <button type="submit"  onclick="finalEntrySalesOrder()" class="btn btn-success float-right">
                <i class="fa fa-arrow-right"></i> Submit Final Entry
            </button>
        </div>
</div> -->

<!-- <div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Informasi Tambahan</h3>
    </div>
    <form action="<?= base_url('sales/postsales/finalEntrySalesOrder') ?>" method="post" onsubmit="return confirm('Finish Entry?')">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="brand">Brand</label>
                    <input type="text" class="form-control" id="brand" name="brand" maxlength="50" placeholder="Brand"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required>
                </div>

                <div class="form-group col-md-4">
                    <label for="size">Ukuran</label>
                    <textarea class="form-control" id="size" name="size" placeholder="Ukuran"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required></textarea>
                </div>

                <div class="form-group col-md-2">
                    <label for="qty">Quantity</label>
                    <input type="text" class="form-control" id="qty" name="qty" maxlength="100" placeholder="Quantity"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required>
                </div>

                <div class="form-group col-md-4">
                    <label for="pembayaran">Pembayaran</label>
                    <textarea class="form-control" id="pembayaran" name="pembayaran" placeholder="Pembayaran"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required></textarea>
                </div>

                <div class="form-group col-md-4">
                    <label for="pengiriman">Pengiriman Barang</label>
                    <textarea name="pengiriman" id="pengiriman" class="form-control" rows="2" placeholder="Pengiriman Barang"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?>></textarea>
                </div>

                <div class="form-group col-md-2">
                    <label for="expdateph">Masa Berlaku Penawaran</label>
                    <input type="text" class="form-control" id="expdateph" name="expdateph" data-date-format="dd-mm-yyyy"
                        placeholder="Masa Berlaku Penawaran"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required>
                </div>

                <div class="form-group col-md-4">
                    <label for="ketentuan">Ketentuan berat</label>
                    <textarea name="ketentuan" id="ketentuan" class="form-control" rows="2" placeholder="Ketentuan Berat"
                        style="text-transform: uppercase;"
                        <?= (empty($dtldata)) ? 'readonly' : '' ?> required>  
                    </textarea>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="<?= base_url('sales/postsales/clearEntrySalesOrder') ?>" 
                onclick="return confirm('This data will not save, are u sure?')" 
                class="btn btn-default float-left">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <button type="submit" class="btn btn-success float-right">
                <i class="fa fa-arrow-right"></i> Submit Final Entry
            </button>
        </div>
    </form>

</div> -->


<!-- WC BY MODAL-->
<!-- Modal Add New Master Item -->
<div class="modal fade" id="modalAddItem" tabindex="-1" role="dialog" aria-labelledby="modalAddItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
        <!-- Header -->
        <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="modalAddItemLabel">
            <i class="fa fa-plus-square"></i> Add New Master Item
            </h5>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <!-- Body -->
        <!-- Body -->
        <div class="modal-body">
            <form id="formAddItem">
            <div class="row">
                
                <!-- Kolom 1 -->
                <div class="col-md-4">
                    <input type="hidden" id="type" name="type" value="INPUTSALES" autocomplete="off">
                    <div class="form-group">
                        <label for="idbarang">Item ID</label>
                        <input type="text" name="idbarang" class="form-control" id="idbarang" maxlength="20" placeholder="Item ID" style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                        <label for="nmbarang">Item Name</label>
                        <input type="text" name="nmbarang" class="form-control" id="nmbarang" maxlength="60" placeholder="Item Name" style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                        <label for="idgroup">Group</label>
                        <select name="idgroup" id="idgroup" class="form-control" style="text-transform:uppercase;"></select>
                    </div>
                    <div class="form-group">
                        <label for="subunitenable">Sub Unit Enable</label>
                        <select name="subunitenable" class="form-control" style="text-transform:uppercase;">
                        <option value="NO">NO</option>
                        <option value="YES">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select name="unit" id="unit" class="form-control" style="text-transform:uppercase;"></select>
                    </div>
                    <div class="form-group">
                        <label for="subunit">Sub Unit (Satuan Kecil)</label>
                        <select name="subunit" id="subunit" class="form-control" style="text-transform:uppercase;"></select>
                    </div>
                    </div>

                    <!-- Kolom 2 -->
                    <div class="col-md-4">
                    <div class="form-group">
                        <label for="idbarcode">ID Barcode/SKU</label>
                        <input type="text" name="idbarcode" class="form-control" id="idbarcode" placeholder="ID Barcode/SKU">
                    </div>
                    <div class="form-group">
                        <label for="mfgdate">MFG Date</label>
                        <input type="text" name="mfgdate" class="form-control" id="mfgdate" placeholder="MFG Date">
                    </div>
                    <div class="form-group">
                        <label for="expdate">Expired Date</label>
                        <input type="text" name="expdate" class="form-control" id="expdate" placeholder="Expire Date">
                    </div>
                    <div class="form-group">
                        <label for="maks_daystock">Max Save Life (Days)</label>
                        <input type="text" name="maks_daystock" class="form-control fikyseparator ratakanan" id="maks_daystock" placeholder="Maks Save life (Days)">
                    </div>
                    <div class="form-group">
                        <label for="deflocation">Default Location</label>
                        <select name="deflocation" id="deflocation" class="form-control" style="text-transform:uppercase;"></select>
                    </div>
                    <div class="form-group">
                        <label for="defarea">Default Area</label>
                        <select name="defarea" id="defarea" class="form-control" style="text-transform:uppercase;"></select>
                    </div>
                    </div>

                    <!-- Kolom 3 -->
                    <div class="col-md-4">
                    <div class="form-group">
                        <label for="setminstock">Set Minimum Stock</label>
                        <select name="setminstock" class="form-control" style="text-transform:uppercase;">
                        <option value="NO">NO</option>
                        <option value="YES">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="minstock">Minimum Stock (Set Minimum Will Be Appear In Report)</label>
                        <input type="text" name="minstock" class="form-control fikyseparator ratakanan" id="minstock" placeholder="Set Your Minimum Stock" maxlength="18">
                    </div>
                    <div class="form-group">
                        <label for="chold">Hold Item</label>
                        <select name="chold" class="form-control" style="text-transform:uppercase;">
                        <option value="NO">NO</option>
                        <option value="YES">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter Your Description..." style="text-transform:uppercase;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tipebarang">Tipe Barang</label>
                        <input type="text" name="tipebarang" value="SALES" class="form-control" id="tipebarang" placeholder="Tipe Barang" disabled maxlength="18">
                    </div>
                </div>

            </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-success" onclick="saveNewItem()">
            <i class="fa fa-save"></i> Save New Item
            </button>
        </div>

        </div>
    </div>
</div>



<!-- /.modal -->
<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/postsales/add_salesorder.js') ?>"></script>
<script type="application/javascript">

    document.querySelectorAll(".tooltip-icon").forEach(function (tooltipIcon) {
            var content = tooltipIcon.getAttribute("data-tooltip-content");

            if (content) {
                new bootstrap.Tooltip(tooltipIcon, {
                    title: content,
                    sanitize: false, // Mengizinkan HTML di dalam tooltip
                    placement: "right",
                    customClass: tooltipIcon.closest('.col-sm-3')?.querySelector('#objcapex') ? 'tooltip-large' : 'tooltip-small'
                });
            }
        });

    var dtldata = <?= json_encode($dtldata ?? null) ?>;

    if (dtldata && dtldata.docno && dtldata.docno.trim() !== '') {
        $('#docno').val(dtldata.docno.trim()).prop('readonly', true);
    }

    $(".startdate").daterangepicker({
        timePicker: true,
        timePicker24Hour: true,
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        locale: {
            format: 'DD-MM-YYYY HH:mm'
        }
    }, function(start, end, label) {
        //kosong
    });

    $(".enddate").daterangepicker({
        timePicker: true,
        timePicker24Hour: true,
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        locale: {
            format: 'DD-MM-YYYY HH:mm'
        }
    }, function(start, end, label) {
        //kosong
    });

  // 1) Inisialisasi daterangepicker ONCE
    $('#docdate').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        locale: { format: 'YYYY-MM-DD' },
        cancelLabel: 'Clear'
    });

    // handler apply/cancel
    $('#docdate').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        // jika butuh validasi bootstrapValidator:
        // $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
    });
    $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    $('#expdateph').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate: moment(), // <--- ini untuk disable backdate
        locale: {
            format: 'YYYY-MM-DD'
        },
        cancelLabel: 'Clear',
    });
    $('#expdateph').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        $('#formInputTransfers').bootstrapValidator('updateStatus', 'expdateph', 'NOT_VALIDATED').bootstrapValidator('validateField', 'expdateph');
    });
    $('#expdateph').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    $('#datereturn').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        },
        cancelLabel: 'Clear',
    });
    $('#datereturn').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        $('#formInputTransfers').bootstrapValidator('updateStatus', 'datereturn', 'NOT_VALIDATED').bootstrapValidator('validateField', 'datereturn');
    });
    $('#datereturn').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $('#periode').datepicker({
        format: "yyyy/mm",  // Format bulan dan tahun
        startView: "months",  // Memulai tampilan dari bulan
        minViewMode: "months", // Menampilkan hanya bulan dan tahun
        autoclose: true, // Menutup kalender setelah memilih tanggal
    });


    $('#expdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
    $('#expdate').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
    });

    $('#expdate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    $('#mfgdate').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD-MM-YYYY'
        },
        cancelLabel: 'Clear',
    });
    $('#mfgdate').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
    });

    $('#mfgdate').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    document.querySelectorAll('#prefix, #infix, #sufix').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\//g, '');
        });
    });

    function updateDocno() {
        let prefix = $("#prefix").val() || "";
        let infix  = $("#infix").val() || "";
        let sufix  = $("#sufix").val() || "";

        let fullDocno = prefix + "/" + infix + "/" + sufix;
        $("#docno").val(fullDocno);
    }

    // Jalankan ketika salah satu field berubah
    $("#prefix, #infix, #sufix").on("input change", function() {
        updateDocno();
    });

        // Auto uppercase untuk prefix dan sufix
    document.getElementById('prefix').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
    });

    document.getElementById('sufix').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
    });

    $('#t_salesorder_dtl').on('draw.dt', function () {
            // Temukan semua input yang ID-nya diawali dengan "etd_"
            $('[id^="etd_"]').each(function () {
                const $input = $(this);

                // Pastikan tidak menginisialisasi 2x
                if ($input.data('daterangepicker')) return;

                $input.daterangepicker({
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: 'DD-MM-YYYY'
                    },
                    cancelLabel: 'Clear'
                });

                $input.on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD-MM-YYYY'));
                });

                $input.on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            });
        });



</script>






