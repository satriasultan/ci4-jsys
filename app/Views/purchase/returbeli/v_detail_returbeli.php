<style>
    
    .section-block {
        background-color: #e8e8e8;
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
        /* transform: scale(1.02); */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #dfdfdf;
        opacity: 1;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(
            to right,
            #007bff 0%,
            #cfe2ff 30%,
            #e8e8e8 100%
        );
        margin: 24px 0;
        border: none;
    }


</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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

<?php echo $message;?>
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('purchase/trans/finalEntryReturBeli') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Retur Pembelian</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Retur Pembelian
                            </div>
                            <div class="row">

                                <!-- LEFT COLUMN -->
                                <div class="col-md-3">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job</label>
                                                <select name="cabang" id="cabang" class="form-control" required></select>
                                            </div>
                                        </div>

                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>No. Jurnal Penerimaan Pembelian</label>

                                                <div class="d-flex">
                                                    <input type="text"
                                                        name="prefix"
                                                        id="prefix"
                                                        class="form-control me-1"
                                                        maxlength="3"
                                                        style="text-transform: uppercase;"
                                                        pattern="[A-Z0-9]+">

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="infix"
                                                        id="infix"
                                                        class="form-control mx-1"
                                                        readonly>

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="sufix"
                                                        id="sufix"
                                                        class="form-control ms-1"
                                                        readonly>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20"     value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>
                                            
                                    </div>
                                        
                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="docdate">Tanggal</label>
                                                <input type="text"
                                                name="docdate"
                                                id="docdate"
                                                class="form-control"
                                                placeholder="Tanggal">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="senddate">Tanggal Pengiriman</label>
                                                <input type="text"
                                                    name="senddate"
                                                    id="senddate"
                                                    class="form-control"
                                                    placeholder="Tanggal Pengiriman">
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="jthtempo">Jatuh Tempo</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        name="jthtempo"
                                                        id="jthtempo"
                                                        class="form-control ratakanan jtsseparator"
                                                        placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">hari</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdsupplier">Supplier</label>
                                                <select name="kdsupplier" id="kdsupplier" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatsupplier">Alamat Supplier</label>
                                                <textarea name="alamatsupplier"
                                                    id="alamatsupplier"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Supplier"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6" style="margin-top: -20px;">
                                            <div class="form-group">
                                                <label for="biayavol">Biaya/Vol</label>
                                                <input name="biayavol"
                                                    id="biayavol"
                                                    class="form-control jtsseparator ratakanan"
                                                    type="text"
                                                    placeholder="Biaya/Vol"
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: -20px;">
                                            <div class="form-group">
                                                <label for="biayavol2">Biaya2/Vol</label>
                                                <input name="biayavol2"
                                                    id="biayavol2"
                                                    class="form-control jtsseparator ratakanan"
                                                    type="text"
                                                    placeholder="Biaya2/Vol"
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="currcode" class="form-label">Mata Uang</label>
                                                    <select name="currcode"
                                                            id="currcode"
                                                            class="form-select select2"
                                                            required>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="kurs" class="form-label">Nilai Tukar</label>
                                                    <input type="text"
                                                        name="kurs"
                                                        id="kurs"
                                                        class="form-control ratakanan jtsseparator"
                                                        placeholder="Nilai tukar akan muncul disini"
                                                        >
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatkirim">Alamat Kirim</label>
                                                <textarea name="alamatkirim"
                                                    id="alamatkirim"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Kirim"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div> -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="idtax">Pajak</label>
                                                <select name="idtax" id="idtax" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="margin-top: 30px;">
                                            <div class="form-group">
                                                <!-- Radio untuk Interngroup -->
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        name="isinclusive" 
                                                        id="isinclusive"
                                                        <?= isset($data['isinclusive']) && $data['isinclusive'] === 'YES' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="isinclusive">
                                                        Inclusive
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6" >
                                            <div class="form-group">
                                                <label for="nofaktur">No. Faktur</label>
                                                <input name="nofaktur"
                                                    id="nofaktur"
                                                    class="form-control"
                                                    type="text"
                                                    maxlength="30"
                                                    placeholder="No. Faktur"
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                        <div class="col-md-6" >
                                            <div class="form-group">
                                                <label for="nosj">No. SJ</label>
                                                <input name="nosj"
                                                    id="nosj"
                                                    class="form-control"
                                                    type="text"
                                                    maxlength="30"
                                                    placeholder="No. SJ"
                                                    style="text-transform:uppercase;">
                                            </div>
                                        </div> -->

                                    </div>
                                </div>

                            </div>

                        </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        Detail PO & Barang
                    </h3>

                    <!-- <div class="float-right d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-success btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Input Data"
                                onclick="btnInputDetail()">
                            <i class="fa fa-plus"></i>
                        </button>

                        <button type="button"
                                class="btn btn-warning btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Update Data"
                                onclick="btnUpdateDetail()">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button type="button"
                                class="btn btn-danger btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Hapus Data"
                                onclick="btnDeleteDetail()">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div> -->
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table id="tabreturbelidtl" class="table table-bordered table-striped" style="width:100%;" cellspacing="0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th>LPB</th>
                                            <th>ID Barang</th>
                                            <th>Nama Barang</th>
                                            <th>No. Capex</th>
                                            <!-- <th>Principal</th> -->
                                            <th>Gudang</th>
                                            <th>Spec</th>
                                            <th>Satuan</th>
                                            <th>Qty</th>
                                            <!-- <th>Bonus Qty</th> -->
                                            <th>Harga</th>
                                            <th>Multi Disc</th>
                                            <!-- <th>Vol/Item</th>
                                            <th>Biaya 1</th>
                                            <th>Biaya 2</th> -->
                                            <th>Nilai</th>
                                            <!-- <th>Keterangan PO</th>
                                            <th>Keterangan PP</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea name="keterangan"
                                    id="keterangan"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Keterangan"
                                    style="text-transform:uppercase;"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-2">
                                            <div class="row">
                                                <div class="col-md-6 text-end">
                                                    <label for="dpp" class="mt-2">DPP</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                        name="dpp"
                                                        id="dpp"
                                                        class="form-control jtsseparator ratakanan"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-2">
                                            <div class="row">
                                                <div class="col-md-6 text-end">
                                                    <label for="jumlahpajak" class="mt-2">Jumlah Pajak</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                        name="jumlahpajak"
                                                        id="jumlahpajak"
                                                        class="form-control jtsseparator ratakanan"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-3">
                                            <div class="row">
                                                <div class="col-md-6 text-end">
                                                    <label for="total" class="mt-2">Total</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text"
                                                        name="total"
                                                        id="total"
                                                        style="font-weight: bold;"
                                                        class="form-control jtsseparator ratakanan"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- <div class="col-md-12">
                                    <div class="form-group text-end">
                                        <button type="button" class="btn btn-lg btn-primary text-white" title="Down Payment">
                                            <i class="fa fa-money"></i> 
                                        </button>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="complain">Complain</label>
                                <input name="complain"
                                    id="complain"
                                    class="form-control"
                                    placeholder="Complain"
                                    style="text-transform:uppercase;">
                            </div>
                        </div>
                    </div>
                    <hr>

                </div>
                <div class="card-footer bg-light">
                    <a href="<?= base_url('purchase/trans/returbeli') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
                
            </div>
        </div>
    </form>
        <!-- /.card -->
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>




<!-- ================= MODAL TAX DETAIL ================= -->
<div class="modal fade" id="modalDetailReturBeli" tabindex="-1" role="dialog" aria-labelledby="modalDetailReturBeliLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailReturBeliLabel">
                    </i> Input Item Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formReturBeliDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="docno" id="docno">
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>LPB</label>
                                <select name="docnolpb" id="docnolpb"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailReturBeli').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>

                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveReturBeliDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalUpdateReturBeli" tabindex="-1" role="dialog" aria-labelledby="modalUpdateReturBeliLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalUpdateReturBeliLabel">
                    </i> Edit Item Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formReturBeliUpdate">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="uniqueid" id="uniqueid">
                    <input type="hidden" name="docno" id="docno">
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PO</label>
                                <input name="docnopomodal" id="docnopomodal"
                                        class="form-control"
                                        style="width:100%" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>ID Barang</label>
                                <input name="idbarang" id="idbarang"
                                        class="form-control"
                                        style="width:100%" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text"
                                        name="nmbarang"
                                        id="nmbarang"
                                        class="form-control"
                                        placeholder="Nama Barang"
                                        style="text-transform:uppercase" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Principal</label>
                                <select name="idprincipal" id="idprincipal"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gudang</label>
                                <input name="idgudang" id="idgudang"
                                        class="form-control"
                                        style="width:100%">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Spec</label>
                                <input name="idspec" id="idspec"
                                        class="form-control"
                                        style="width:100%">
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Satuan</label>
                                <input name="unit" id="unit"
                                        class="form-control select2"
                                        style="width:100%" readonly>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="text"
                                        name="qty"
                                        id="qty"
                                        class="form-control jtsseparator ratakanan"
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Bonus Quantity</label>
                                <input type="text"
                                        name="qtybonus"
                                        id="qtybonus"
                                        class="form-control jtsseparator ratakanan"
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="text"
                                        name="harga"
                                        id="harga"
                                        class="form-control jtsseparator ratakanan"
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Multi Disc (%)</label>
                                <input type="text"
                                        name="multidisc"
                                        id="multidisc"
                                        class="form-control jtsseparator ratakanan"
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Vol/Item</label>
                                <input type="text"
                                        name="volitem"
                                        id="volitem"
                                        class="form-control jtsseparator ratakanan"
                                        
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Biaya 1</label>
                                <input type="text"
                                        name="biaya"
                                        id="biaya"
                                        class="form-control jtsseparator ratakanan"
                                        
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Biaya 2</label>
                                <input type="text"
                                        name="biaya2"
                                        id="biaya2"
                                        class="form-control jtsseparator ratakanan"
                                        
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nilai</label>
                                <input type="text"
                                        name="nilai"
                                        id="nilai"
                                        class="form-control jtsseparator ratakanan"
                                        readonly
                                        placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Keterangan PO</label>
                                <textarea type="text"
                                        name="descriptionpo"
                                        rows="4"
                                        style="text-transform: uppercase;"
                                        id="descriptionpo"
                                        class="form-control" readonly></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Keterangan PP</label>
                                <textarea type="text"
                                        name="descriptionpp"
                                        rows="4"
                                        style="text-transform: uppercase;"
                                        id="descriptionpp"
                                        class="form-control" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalUpdateReturBeli').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>

                    <button type="button"
                            class="btn btn-primary"
                            onclick="saveReturBeliDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/purchase/returbeli_detail.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        var dtldata = <?= json_encode($dtldata ?? null) ?>;

        if (dtldata && dtldata.docno && dtldata.docno.trim() !== '') {
            $('#docno').val(dtldata.docno.trim()).prop('readonly', true);
        }


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



        $('#senddate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#senddate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'senddate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'senddate');
        });
        $('#senddate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        
        $('#periodemulai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodemulai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodemulai').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        
        $('#periodeakhir').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodeakhir').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodeakhir').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        

    });

</script>