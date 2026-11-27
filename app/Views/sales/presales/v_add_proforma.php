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
<div class="corp-form-container">

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
<?= $this->include('sales/postsales/_corp_theme') ?>

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
<?php
    /* =====================================================
     * AUTOFILL INPUT AWAL CABANG / JOB (referensi addDeliveryOrder)
     * Cabang mengikuti session user login.
     * Hanya PT JATIM TAMAN STEEL yang boleh memilih bebas.
     * ===================================================== */
    $sessionBranchId   = trim((string) session()->get('idbranchjob'));
    $sessionBranchName = strtoupper(trim((string) session()->get('nmbranchjob')));
    $sessionBranchKey  = preg_replace('/[^A-Z0-9]/', '', $sessionBranchName);

    $isPlantBranch      = strpos($sessionBranchKey, 'PLANT') !== false
                        || strpos($sessionBranchKey, 'PLAN1') !== false
                        || strpos($sessionBranchKey, 'PLAN2') !== false;

    $isHeadOfficeBranch = ($sessionBranchId === '01')
                        || strpos($sessionBranchKey, 'JATIMTAMANSTEEL') !== false;

    $canChooseBranch    = $isHeadOfficeBranch && !$isPlantBranch;
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Informasi Umum Proforma Invoice</h3>
    </div>
    <form  action="<?php echo base_url('sales/presales/saveProforma')?>" method="post" id="formsaveMstMRP" enctype="multipart/form-data" role="form">
    <div class="card-body">
        <div class="row align-items-start"> 
            <!-- Baris 1: Identitas dokumen -->
            <div class="form-group col-lg-2 col-md-4">
                <label for="jnsinvoice">Jenis Invoice <span style="color:red">*</span></label>
                <select name="jnsinvoice" id="jnsinvoice" class="form-control">
                    <option value="" selected disabled>- Choose Jenis -</option>
                    <option value="PROFORMA">PROFORMA</option>
                    <option value="INVOICE">INVOICE</option>
                    <!-- <option value="MSMJ">MSM JAPAN</option> -->
                </select>
            </div>
            <div class="form-group col-lg-2 col-md-4">
                <label for="rolejob">Role/Job <span style="color:red">*</span></label>
                <select name="rolejob" id="rolejob" class="form-control">
                    <option value="" selected disabled>- Choose Role/Job -</option>
                    <option value="JTS">JTS</option>
                    <option value="MSMI">MSM INDONESIA</option>
                    <option value="MSMJ">MSM JAPAN</option>
                </select>
            </div>
            <div class="form-group col-lg-2 col-md-4">
                <label for="cabang">Cabang / Job <span style="color:red">*</span></label>
                <select name="cabang" id="cabang" class="form-control" required
                    data-session-branch-id="<?= esc($sessionBranchId) ?>"
                    data-session-branch-name="<?= esc($sessionBranchName) ?>"
                    data-can-choose-branch="<?= $canChooseBranch ? '1' : '0' ?>"></select>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="prefix">No. Dokumen <span style="color:red">*</span></label>
                <div class="pf-docno-group">
                    <input type="text" name="prefix" id="prefix" class="form-control" placeholder="Prefix" readonly>
                    <span class="pf-docno-sep">/</span>
                    <input type="text" name="infix" id="infix" class="form-control" placeholder="Infix" maxlength="20" style="text-transform: uppercase;" pattern="[A-Za-z0-9-]+" onkeypress="return validateInfix(event)">
                    <span class="pf-docno-sep">/</span>
                    <input type="text" name="sufix" id="sufix" class="form-control" placeholder="Suffix" readonly>
                </div>
            </div>

            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20" style="text-transform: uppercase;"  placeholder="No. Jurnal/Dokumen" readonly>
            <div class="form-group col-lg-2 col-md-4">
                <label for="docdate">Tanggal Dokumen/Periode <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="docdate" name="docdate" data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy" autocomplete="off" style="text-transform: uppercase;" required>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="cust">Customer <span style="color:red">*</span></label>
                <select class="form-control" id="cust" name="cust"></select>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="address">Alamat Customer</label>
                <textarea type="text" class="form-control" id="address"  name="address" rows="2" placeholder="Alamat Kantor" style="text-transform: uppercase;"  required readonly></textarea>
            </div>
            <div class="form-group col-lg-2 col-md-3">
                <label for="phone">No. Telp</label>
                <input type="text" class="form-control" id="phone"  name="phone"  placeholder="No. Telepon" style="text-transform: uppercase;"  required readonly>
            </div>
            <div class="form-group col-lg-2 col-md-3">
                <label for="fax">Fax</label>
                <input type="text" class="form-control" id="fax"  name="fax"  placeholder="Fax" style="text-transform: uppercase;"  required readonly>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="up">PIC</label>
                <input type="text" class="form-control" id="up"  name="up"  placeholder="Kontak PIC" style="text-transform: uppercase;"  required readonly>
            </div>
            <div class="d-none">
                <div class="section-block" style="width:100%;">
                    <div class="section-header"><i class="fa fa-link"></i> Sumber PJO / Penjualan (lama)</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="docnopjo">No. PJO / Penjualan</label>
                                <select id="docnopjo" name="docnopjo" class="form-control"></select>
                                <small style="color:#64748b;font-size:11.5px;display:block;margin-top:5px;">
                                    Hanya PJO FINAL yang sudah memiliki jurnal SALESX POSTED.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jurnalno">No. Jurnal PJO</label>
                                <input type="text" id="jurnalno" name="jurnalno" class="form-control"
                                       placeholder="Otomatis dari PJO" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btnTarikPJO" class="btn btn-primary w-100"
                                        onclick="pullPenjualanProforma()" disabled>
                                    <i class="fa fa-download"></i> Tarik PJO
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="facrisk">For Account &amp; Risk Messers</label>
                    <textarea name="facrisk" id="facrisk" class="form-control" rows="2" placeholder="Write For Account &amp; Risk Messers" style="text-transform: uppercase;" ></textarea>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="shipper">Shipper</label>
                    <textarea name="shipper" id="shipper" class="form-control" rows="2" placeholder="Write Shipper" style="text-transform: uppercase;" ></textarea>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="consignee">Consignee</label>
                    <textarea name="consignee" id="consignee" class="form-control" rows="2" placeholder="Write Consignee" style="text-transform: uppercase;" ></textarea>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="shippingmark">Shipping Mark</label>
                    <textarea name="shippingmark" id="shippingmark" class="form-control" rows="2" placeholder="Write Shipping Mark" style="text-transform: uppercase;" ></textarea>
            </div>
            <div class="form-group col-lg-4 col-md-6">
                <label for="notifyparty">Notify Party</label>
                    <textarea name="notifyparty" id="notifyparty" class="form-control" rows="2" placeholder="Write Notify Party" style="text-transform: uppercase;" ></textarea>
            </div>

            <div class="col-12">
                <div class="pf-form-divider"><i class="fa fa-random"></i> Customer Destination / Trader / End User</div>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="customerdestination">Customer Destination</label>
                <select class="form-control" id="customerdestination" name="customerdestination"></select>
                <small style="color:#64748b;font-size:11px;">Pilih dari customer atau ketik manual.</small>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="customerdestination_addr">Alamat Customer Destination</label>
                <textarea name="customerdestination_addr" id="customerdestination_addr" class="form-control" rows="2" placeholder="Alamat Customer Destination" style="text-transform: uppercase;"></textarea>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="trader">Trader</label>
                <select class="form-control" id="trader" name="trader"></select>
                <small style="color:#64748b;font-size:11px;">Pilih dari customer atau ketik manual.</small>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="trader_addr">Alamat Trader</label>
                <textarea name="trader_addr" id="trader_addr" class="form-control" rows="2" placeholder="Alamat Trader" style="text-transform: uppercase;"></textarea>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="enduser">End User</label>
                <select class="form-control" id="enduser" name="enduser"></select>
                <small style="color:#64748b;font-size:11px;">Pilih dari customer atau ketik manual.</small>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="enduser_addr">Alamat End User</label>
                <textarea name="enduser_addr" id="enduser_addr" class="form-control" rows="2" placeholder="Alamat End User" style="text-transform: uppercase;"></textarea>
            </div>
            
            <!-- <div class="form-group  col-md-4">
                <label for="desc">Description</label>
                    <textarea name="desc" id="desc" class="form-control" rows="2" placeholder="Write description" style="text-transform: uppercase;" ></textarea>
            </div> -->
        </div>
    </div>
    <?php if (empty($dtldata)) { ?>
        <div class="card-footer">
            <span class="float-left" style="color: red;">Klik button proses terlebih dahulu untuk melanjutkan pengisian item Penawaran</span>
            <button type="submit"  class="btn btn-primary float-right"><i class="fa fa-repeat"></i> Proses</button>
        </div>
    <?php } ?>
    </form>
</div>  

<div class="card card-primary">
    <div class="card-header clearfix">
        <h3 class="card-title">
            <i class="fa fa-list-alt"></i> Detail Item Proforma Invoice
            <span class="badge pf-status-badge"><?= esc(strtoupper(trim((string) ($dtldata['status'] ?? ''))) === 'E' ? 'DRAFT ENTRY' : (strtoupper(trim((string) ($dtldata['status'] ?? ''))) === 'D' ? 'DRAFT' : (strtoupper(trim((string) ($dtldata['status'] ?? ''))) === 'F' ? 'FINAL' : '-'))) ?></span>
            <?php if (!empty($dtldata) && strtoupper(trim((string) ($dtldata['status'] ?? ''))) === 'E') { ?>
            <button type="button" class="btn btn-info pf-tarik-pjo" id="btnTarikPJOData"
                    title="Pilih Customer terlebih dahulu" disabled>
                <i class="fa fa-download"></i> Tarik Data Penjualan
            </button>
            <?php } ?>
        </h3>

        <?php if (!empty($dtldata) && strtoupper(trim((string) ($dtldata['status'] ?? ''))) === 'E') { ?>
        <div class="header-actions">
            <button type="button" class="btn btn-success btn-lg action-btn" id="pfBtnAdd"
                    title="Input Data (Ctrl + Q / Alt+N)"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-warning btn-lg action-btn" id="pfBtnUpdate"
                    title="Update Data (Ctrl + E)"><i class="fa fa-edit"></i></button>
            <button type="button" class="btn btn-danger btn-lg action-btn" id="pfBtnDelete"
                    title="Hapus Data (Ctrl + D)"><i class="fa fa-trash"></i></button>
            <button type="button" class="btn btn-info btn-lg action-btn" id="btn_add_item"
                    onclick="openAddNewItemModal()" title="Add New Master Item"><i class="fa fa-plus-square"></i></button>
        </div>
        <?php } ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" style="overflow-x:auto;">
                    <table id="t_proforma_dtl" class="table table-bordered table-striped sales-dtl-table" style="width:100%" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="30"><input type="checkbox" id="pfCheckAll"></th>
                                <th>Nama Barang</th>
                                <th width="110">Weight</th>
                                <th class="pf-col-unit" width="90">Unit</th>
                                <th width="150">Price</th>
                                <th width="160">Amount</th>
                                <th>Keterangan</th>
                                <th class="pf-col-aksi" width="76">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot id="proformaInlineFoot"></tfoot>
                    </table>
                <div class="pf-num-hint"><i class="fa fa-link"></i> Sumber PJO: <b><span id="pfSumberInfo">-</span></b></div>
                </div>
                <!-- Hint format angka & sumber data -->
                <div class="pf-num-hint">
                    <i class="fa fa-info-circle"></i>
                    Desimal gunakan titik (<b>.</b>), maksimal <b>4</b> angka di belakang koma.
                    Baris detail diisi inline: ketik <b>kode / nama barang</b> atau <b>No. Penjualan</b>
                    (memilih No. Penjualan akan menarik seluruh detail dokumen tersebut).
                    Untuk memilih per item / beberapa dokumen gunakan tombol <b>Tarik Data Penjualan</b>.
                    Mode <b>Multiple</b>: setiap dokumen Penjualan menjadi <b>satu baris</b> berisi No. Penjualan
                    beserta total nilainya (bukan rincian kode barang).
                </div>
                <div class="pf-num-hint pf-key-hint">
                    <i class="fa fa-bolt"></i>
                    Tambah item: <kbd>Alt</kbd> + <kbd>N</kbd> / <kbd>Ctrl</kbd> + <kbd>Q</kbd> atau tombol <b>+</b>.
                    Edit: centang baris lalu <kbd>Ctrl</kbd> + <kbd>E</kbd> atau klik pensil.
                    <kbd>Enter</kbd> menyimpan baris, <kbd>Esc</kbd> membatalkan.
                </div>
            </div>
        </div>

        <hr>
        <div class="row" style="margin-top:10px;">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="desc" style="font-weight:bold;">Remarks</label>
                    <textarea name="desc" id="desc" class="form-control" rows="6"
                              placeholder="Write description"
                              style="text-transform:uppercase;"><?= isset($dtldata['description']) ? esc(trim((string) $dtldata['description'])) : '' ?></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mb-1">
                    <div class="col-md-6 text-end"><label class="mt-2">Gross Sales</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="grosssales" name="grosssales" class="form-control ratakanan jaccseparator" value="0" disabled></div></div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-6 text-end"><label class="mt-2">Down Payment</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="downpayment" name="downpayment" class="form-control ratakanan jaccseparator" value="0"></div></div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-6 text-end"><label class="mt-2">Net Sales</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="netsales" name="netsales" class="form-control ratakanan jaccseparator" value="0" disabled></div></div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-6 text-end"><label class="mt-2">Tax Basis</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="taxbasis" name="taxbasis" class="form-control ratakanan jaccseparator" value="0" disabled></div></div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-6 text-end"><label class="mt-2">V.A.T (12%)</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="vat" name="vat" class="form-control ratakanan jaccseparator" value="0" disabled></div></div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-6 text-end"><label class="mt-2">PPh 22</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="pph22" name="pph22" class="form-control ratakanan jaccseparator" value="0" disabled></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-end"><label class="mt-2" style="font-weight:bold;">Total</label></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="text" id="ttlprice" name="ttlprice" class="form-control ratakanan jaccseparator" value="0" disabled style="font-weight:bold;"></div></div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div><div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Informasi Pembayaran</h3>
    </div>
    <!-- <form action="<?= base_url('sales/presales/finalEntryProforma') ?>" method="post" onsubmit="return confirm('Finish Entry?')"> -->
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

                <!-- <div class="form-group col-md-2">
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
                </div> -->
            </div>
        </div>
        <div class="card-footer">
            <a href="<?= base_url('sales/presales/clearEntryProforma') ?>" 
                onclick="return confirm('This data will not save, are u sure?')" 
                class="btn btn-default float-left">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <button type="button" class="btn btn-primary float-right me-2 btn-draft-entry" id="btnSimpanDraftProforma"
                    onclick="pfSaveDraftProforma()">
                <i class="fa fa-save"></i> Simpan Draft
            </button>

            <button type="button" class="btn btn-success float-right btn-final-entry"
                    onclick="pfSaveFinalProforma()">
                <i class="fa fa-arrow-right"></i> Submit Final Entry
            </button>
        </div>
    <!-- </form> -->

</div>

<!-- <div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Informasi Tambahan</h3>
    </div>
    <form action="<?= base_url('sales/presales/finalEntryProforma') ?>" method="post" onsubmit="return confirm('Finish Entry?')">
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
            <a href="<?= base_url('sales/presales/clearEntryProforma') ?>" 
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




<!--Modal untuk Filter-->
<div class="modal fade" id="insertsikbsp">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Insert Detail Proforma</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <label for="idunitinsert">Insert Unit</label>
                            <select name="idunitinsert" id="idunitinsert" class="form-control" required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="namabaranginsert">Insert Item Name</label>
                        <input type="text" maxlength="50" class="form-control" id="namabaranginsert" name="namabaranginsert" style="text-transform: uppercase;" placeholder="Insert Item Name" required>
                        <!-- <select name="idbarang_filter_spec" id="idbarang_filter_spec" class="form-control" required >
                        </select> -->
                    </div>
                    <div class="form-group col-md-12">
                        <label for="qtyinsert">Qty</label>
                        <input min="0" class="form-control" id="qtyinsert"  type="number" step="0.01" name="qtyinsert" placeholder="Insert Quantity" required>
                        <!-- <select name="idbarang_filter_spec" id="idbarang_filter_spec" class="form-control" required >
                        </select> -->
                    </div>
                    <div class="form-group col-md-12">
                        <label for="priceinsert">Price</label>
                        <input min="0" class="form-control jaccseparator ratakanan" id="priceinsert" type="text" name="priceinsert" placeholder="Price" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="descriptiondetail">Description</label>
                        <textarea name="descriptiondetail" id="descriptiondetail" style="text-transform: uppercase;" class="form-control" rows="2" placeholder="Write description" ></textarea>
                        <!-- <select name="idbarang_filter_spec" id="idbarang_filter_spec" class="form-control" required >
                        </select> -->
                    </div>

                </div>
                <div class="modal-footer ">
                    <button onclick="insert_detail_sikbsp()" class="btn btn-success float-right" style="margin-left:5px"><i class="fa fa-plus-circle"></i> Insert Detail Proforma </button>

                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- /.modal -->
<style type="text/css">
    .pf-docno-group { display:flex; align-items:center; gap:6px; }
    .pf-docno-group .form-control { text-align:center; font-weight:600; }
    .pf-docno-group #prefix, .pf-docno-group #sufix { flex:1.15; background-color:#f8f9fa; }
    .pf-docno-group #infix { flex:1; }
    .pf-docno-sep { color:#64748b; font-weight:700; }
    .pf-header-note { margin:0 0 10px 0; font-size:11.5px; color:#64748b; }
    .pf-form-divider { margin:6px 0 10px 0; padding:4px 10px; font-size:12px; font-weight:600; color:#1e3a5f; background:#eef4ff; border-left:3px solid #0d6efd; border-radius:3px; }
    .corp-form-container .card-header h3.card-title .pf-tarik-pjo { margin-left:12px; padding:6px 16px; font-size:14px; font-weight:600; line-height:1.35; border-radius:4px; }
    .pf-status-badge { margin-left:10px; font-size:11px; vertical-align:middle; background:#0d6efd; color:#fff; }
    .pf-sumber-info { margin-left:10px; font-size:12px; font-weight:600; color:#0d6efd; }
    #t_proforma_dtl tfoot tr.pf-inline-row td { padding:4px 3px; vertical-align:middle; background-color:#fff8e1; }
    #t_proforma_dtl tfoot tr.pf-inline-row .form-control-sm { font-size:11px; padding:2px 4px; min-width:62px; height:28px!important; min-height:28px!important; }
    #t_proforma_dtl tfoot tr.pf-inline-row .select2-container { min-width:120px; }
    #t_proforma_dtl tfoot tr.pf-inline-row .select2-container .select2-selection--single { height:28px!important; min-height:28px!important; }
    #t_proforma_dtl tfoot tr.pf-inline-row .select2-container .select2-selection--single .select2-selection__rendered { padding-left:5px!important; padding-right:18px!important; line-height:26px!important; font-size:11px!important; }
    #t_proforma_dtl thead th.pf-col-unit, #t_proforma_dtl tbody td.pf-col-unit-cell { width:90px!important; min-width:90px!important; }
    #t_proforma_dtl thead th.pf-col-aksi, #t_proforma_dtl tbody td.pf-col-aksi-cell, #t_proforma_dtl tfoot td.pf-col-aksi { width:76px!important; min-width:76px!important; text-align:center!important; white-space:nowrap!important; padding-left:4px!important; padding-right:4px!important; }
    #t_proforma_dtl tfoot td.pf-col-no { width:30px!important; min-width:30px!important; text-align:center!important; padding:2px!important; }
    #t_proforma_dtl tfoot tr.pf-inline-row .pf-inline-btn, #t_proforma_dtl tbody .pf-row-btn { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; margin:0 1px; padding:0; font-size:12px; line-height:1; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    #t_proforma_dtl tfoot tr.pf-inline-row .pf-inline-btn:hover, #t_proforma_dtl tbody .pf-row-btn:hover { transform:translateY(-1px); box-shadow:0 2px 5px rgba(15,23,42,.22); }
    #t_proforma_dtl tfoot .pf-inline-save { background:#16a34a; }
    #t_proforma_dtl tfoot .pf-inline-cancel { background:#64748b; }
    #t_proforma_dtl tbody .pf-row-btn-edit { background:#d97706; }
    #t_proforma_dtl tbody .pf-row-btn-delete { background:#dc2626; }
    #t_proforma_dtl tbody td.dataTables_empty { padding:16px 10px!important; font-size:12.5px!important; font-weight:600!important; color:#64748b!important; text-align:center; cursor:pointer; }
    #t_proforma_dtl.pf-has-draft tbody td.dataTables_empty { display:none!important; }
    .pf-num-hint { margin-top:6px; font-size:11.5px; line-height:1.5; color:#64748b; }
    .pf-num-hint i { margin-right:4px; color:#1e3a5f; }
    .pf-key-hint { margin-top:4px; padding:5px 8px; background:#f1f5f9; border-left:3px solid #0284c7; border-radius:4px; color:#475569; }
    .pf-key-hint kbd { display:inline-block; min-width:16px; padding:1px 5px; font-size:11px; font-weight:700; color:#1e3a5f; background:#fff; border:1px solid #bfdbfe; border-bottom-width:2px; border-radius:4px; }
    #modalTarikPJOData .pjo-pick-wrap { max-height:56vh; overflow:auto; border:1px solid #dee2e6; border-radius:4px; }
    #modalTarikPJOData .pjo-pick-table { font-size:11.5px; margin-bottom:0; }
    #modalTarikPJOData .pjo-pick-table > thead > tr > th { padding:3px 6px; font-size:11px; font-weight:600; white-space:nowrap; background-color:#0d6efd; color:#fff; position:sticky; top:0; z-index:2; }
    #modalTarikPJOData .pjo-pick-table > tbody > tr > td { padding:2px 6px; vertical-align:middle; }
    #modalTarikPJOData .pjo-pick-col-no { font-family:Consolas, monospace; }
    #modalTarikPJOData .pjo-pick-dtl { font-size:11px; background-color:#fff; margin-bottom:2px; }
    #modalTarikPJOData .pjo-pick-dtl > thead > tr > th { padding:2px 5px; font-size:10.5px; background-color:#eef2f8; color:#33475b; white-space:nowrap; }
    #modalTarikPJOData .pjo-pick-dtl > tbody > tr > td { padding:1px 5px; vertical-align:middle; }
    #modalTarikPJOData .pjo-pick-dtl tr.pjo-pick-dtl-done > td { color:#a3aab5; background-color:#f7f7f7; }
    #modalTarikPJOData .pjo-pick-item { font-family:Consolas, monospace; color:#0b5ed7; }
    #modalTarikPJOData .pjo-pick-note { margin-top:4px; font-size:11px; color:#6c757d; }
    #modalTarikPJOData .pjo-filter-note { margin-bottom:6px; padding:5px 9px; font-size:11.5px; color:#1e3a5f; background:#eef4ff; border-left:3px solid #0d6efd; border-radius:3px; }
    #modalTarikPJOData .pjo-filter-note.pf-warn { color:#7a4b00; background:#fff6e5; border-left-color:#f59e0b; }
    #modalTarikPJOData .pjo-item-head { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin-bottom:8px; }
    #modalTarikPJOData .pjo-item-title { font-size:13px; font-weight:600; color:#0f172a; }
    #modalTarikPJOData .pjo-item-title b { color:#0d6efd; font-family:Consolas, monospace; }
    #modalTarikPJOData .pjo-item-cust { font-size:11.5px; color:#475569; }
    #modalTarikPJOData tr.pjo-doc-row { cursor:pointer; }
    #modalTarikPJOData tr.pjo-doc-row:hover > td { background-color:#eef5ff; }
    #modalTarikPJOData tr.pjo-doc-row.pjo-doc-active > td { background-color:#e3efff; }
    #modalTarikPJOData tr.pjo-doc-row.pjo-doc-locked > td { background-color:#f7f7f7; color:#9aa4b2; }
    #modalTarikPJOData .pjo-doc-picked { font-size:11px; font-weight:600; color:#16a34a; white-space:nowrap; }
    #modalTarikPJOData .pjo-doc-picked.pf-none { color:#94a3b8; font-weight:400; }
    #modalTarikPJOData .pjo-doc-open { color:#0d6efd; white-space:nowrap; font-size:11px; }
    #modalTarikPJOData td.pjo-doc-check-cell { text-align:center; }
    #modalPilihModePJO .pf-mode-btn { width:100%; padding:16px; font-size:14px; font-weight:600; }
</style>
<!-- ================= MODAL PILIH MODE TARIK ================= -->
<div class="modal fade" id="modalPilihModePJO" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="fa fa-download"></i> Tarik Data Penjualan</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" style="font-size:13px;">Pilih mode penarikan data:</p>
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary pf-mode-btn" data-mode="single">
                            <i class="fa fa-file-text-o"></i><br>Single<br><small>Satu dokumen penjualan</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success pf-mode-btn" data-mode="multi">
                            <i class="fa fa-files-o"></i><br>Multiple<br><small>Beberapa dokumen, customer<br></small>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL TARIK DATA PENJUALAN (Single / Multi) ================= -->
<div class="modal fade" id="modalTarikPJOData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">
                    <i class="fa fa-download"></i> <span id="pjoModalTitle">Tarik Data Penjualan</span>
                    <span id="pfModeLabel" class="badge bg-light text-dark ms-2">SINGLE</span>
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- TAMPILAN 1 : DAFTAR DOKUMEN PENJUALAN -->
                <div id="pjoListView">
                    <div class="row g-1 mb-2 align-items-center">
                        <div class="col-8"><input type="text" id="pjoSearch" class="form-control form-control-sm" placeholder="Cari No. Penjualan / Customer..."></div>
                        <div class="col-4 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnSearchPJO"><i class="fa fa-search"></i> Cari</button>
                        </div>
                    </div>
                    <div class="pjo-filter-note" id="pjoFilterNote"></div>
                    <div class="pjo-pick-wrap">
                        <table class="table table-sm table-bordered pjo-pick-table mb-0" id="tblPJOList">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="38" class="text-center">Pilih</th>
                                    <th>No. Penjualan</th>
                                    <th width="88">Tanggal</th>
                                    <th>Customer</th>
                                    <th width="118" class="text-end">Total</th>
                                    <th width="96">No. Jurnal</th>
                                    <th width="120" class="text-center">Detail Item</th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="7" class="text-center text-muted py-2">Memuat data...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="pjo-pick-note" id="pjoPickNote">
                        Klik baris Penjualan untuk membuka <b>detail item</b>, lalu centang item yang akan ditarik.
                    </div>
                </div>

                <!-- TAMPILAN 2 : DETAIL ITEM SATU DOKUMEN PENJUALAN -->
                <div id="pjoItemView" style="display:none;">
                    <div class="pjo-item-head">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnBackPJO"><i class="fa fa-arrow-left"></i> Kembali ke Daftar</button>
                        <span class="pjo-item-title">Detail Item Penjualan <b id="pjoItemDocno">-</b></span>
                        <span class="pjo-item-cust" id="pjoItemCust"></span>
                    </div>
                    <div class="pjo-pick-wrap">
                        <table class="table table-sm table-bordered pjo-pick-dtl mb-0" id="tblPJOItemList">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="34" class="text-center"><input type="checkbox" id="pjoItemChkAll" title="Centang semua item"></th>
                                    <th>Item</th>
                                    <th width="110">Spec</th>
                                    <th width="46">Sat</th>
                                    <th width="74" class="text-end">Qty</th>
                                    <th width="100" class="text-end">Harga</th>
                                    <th width="112" class="text-end">Nilai</th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="7" class="text-center text-muted py-2">Memuat detail...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="pjo-pick-note" id="pjoItemNote"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" id="btnResetPJO"><i class="fa fa-eraser"></i> Reset Pilihan</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
                <button type="button" class="btn btn-success btn-lg" id="btnPullPJOData"><i class="fa fa-download"></i> Tarik ke Proforma (<span id="pjoPickCount">0</span>)</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/presales/add_proforma.js') ?>"></script><?php
$pfJsRel = 'assets/pagejs/sales/presales/proforma_inline.js';
$pfJsAbs = (defined('FCPATH') ? FCPATH : '') . $pfJsRel;
$pfJsVer = is_file($pfJsAbs) ? filemtime($pfJsAbs) : date('YmdHis');
?>
<script type="text/javascript">
    window.PF_ADD = {
        existingDocno: '<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno']), 'js') : '' ?>',
        status: '<?= esc(strtoupper(trim((string) ($dtldata['status'] ?? ''))), 'js') ?>'
    };
</script>
<?php
$pfBranchJsRel = 'assets/pagejs/sales/postsales/sales_branch_autofill.js';
$pfBranchJsAbs = (defined('FCPATH') ? FCPATH : '') . $pfBranchJsRel;
$pfBranchJsVer = is_file($pfBranchJsAbs) ? filemtime($pfBranchJsAbs) : date('YmdHis');
?>
<script type="application/javascript" src="<?= base_url($pfBranchJsRel) ?>?v=<?= $pfBranchJsVer ?>"></script><script type="application/javascript" src="<?= base_url($pfJsRel) ?>?v=<?= $pfJsVer ?>"></script>
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


    function validateInfix(event) {
        const char = String.fromCharCode(event.which);
        // Hanya allow: huruf (A-Z, a-z), angka (0-9), dan strip (-)
        if (!/^[A-Za-z0-9-]$/.test(char)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // Additional validation on input event to remove any special characters that might paste
    document.getElementById('infix').addEventListener('input', function(e) {
        // Hapus semua karakter kecuali: A-Z, a-z, 0-9, dan -
        this.value = this.value.replace(/[^A-Za-z0-9-]/g, '');
    });

    // Validation on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const infixValue = document.getElementById('infix').value;
        if (!/^[A-Za-z0-9-]+$/.test(infixValue)) {
            e.preventDefault();
            alert('Hanya huruf, angka, dan tanda strip (-) yang diperbolehkan untuk bagian infix');
            document.getElementById('infix').focus();
        }
    });

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
        locale: { format: 'DD-MM-YYYY' },
        cancelLabel: 'Clear'
    });

    // handler apply/cancel
    $('#docdate').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
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
        if (typeof toggleTarikPjo === 'function') toggleTarikPjo();
    }

    // Jalankan ketika salah satu field berubah
    $("#prefix, #infix, #sufix").on("input change", function() {
        updateDocno();
    });


</script>




</div>



