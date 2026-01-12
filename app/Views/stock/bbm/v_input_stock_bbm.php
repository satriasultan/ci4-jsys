<style>
    .text-wrap{
        white-space:normal;
    }
    .width-200{
        width:200px;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> Input LPB (Laporan Penerimaan Barang)</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
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

<audio id="chatAudio" >
    <source src=
            "<?php echo base_url('assets/sound/beepscan.mp3'); ?>"
            type="audio/mpeg">
</audio>
<?php /*<button onclick="play()">Press Here!</button> */ ?>


<?php echo $message;?>
<div class="row">
    <input type="hidden" name="idlocation_tx" class="form-control" id="idlocation_tx" style="text-transform: uppercase" value="<?php echo trim($dtldata['idlocation']); ?>" placeholder="Document Input" readonly>
    <?php if (trim($dtldata['status']) === 'I') {  ?>
    <div class="col-md-6">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Step 1</h3>
            </div>
            <!-- /.card-header -->
            <form  action="<?php echo base_url('stock/bbm/verifikasi_mst_stock_bbm')?>" method="post" id="formInputTransfers" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <!-- text input -->
                            <div class="form-group">
                                <label for="idlocation">Gudang</label>
                                <div class="input-group">
                                    <select name="idlocation" id="idlocation" class="form-control" disabled >
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="docno">Document</label>
                                <input type="text" name="docno" class="form-control" id="docno" style="text-transform: uppercase" value="<?php echo trim($dtldata['docno']); ?>" placeholder="Document Input" readonly>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <!-- textarea -->
                            <div class="form-group">
                                <label for="docdate">Tanggal Kedatangan</label>
                                <input type="text" name="docdate" class="form-control" id="docdate" placeholder="Transfer Date" required <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?>>
                            </div>
                        </div>
                        <div class="col-sm-6 ">
                            <label for="description">Jenis</label>
                            <?php /*<input type="text" name="docjns"  style="text-transform:uppercase;" maxlength="12" class="form-control" id="docjns" placeholder="Type Reference" <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?> required>*/ ?>
                            <label for="docjns"></label><select name="docjns" id="docjns" class="form-control" placeholder="Type Reference" <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?>required>
                                <option value=""> Pilih Jenis Dokumen </option>
                                <option value="PO"> PO </option>
                                <option value="NON-PO"> NON-PO </option>
                                <option value="RTR"> RETUR </option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 ">
                            <label for="description">Supplier</label>
                            <label for="docjns"></label>
                            <select name="namasupplier" id="namasupplier" class="form-control" placeholder="Choose Your Supplier">
                            </select>
                        </div>
                        <div class="col-sm-6 ">
                            <label for="description"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group sreference">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="description">Copy Deskripsi Utama Ke Detail Barang</label>
                            <div class="custom-control custom-checkbox form-group">
                                <input class="custom-control-input" type="checkbox" id="copydesc" name="copydesc" >
                                <label for="copydesc" class="custom-control-label">Centang Jika Ingin Mengcopy</label>
                            </div>
                        </div>
                    </div>
                    <?php /*
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group sreference">
                                <label for="description">Reference ID/PO Number</label>
                                <input type="text" name="docref" class="form-control" id="docref1"  style="text-transform: uppercase" placeholder="Reference ID" <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?> required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="description">Reference ID/PO Number</label>
                                <select name="docref" id="docref2" class="form-control" >
                                </select>
                            </div>
                        </div>

                    </div>
 */ ?>
                    <div class="row">
                        <div class="col-sm-12 ">
                            <label for="description">Keterangan Utama</label>
                            <textarea  style="text-transform: uppercase" name="description" id="description" class="form-control" rows="3" placeholder="Enter ..." <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?>></textarea>
                            <!--input type="text" name="description" class="form-control" id="description" style="text-transform: uppercase" placeholder="Description" -->
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <?php if (trim($dtldata['status']) === 'I') {  ?>
                        <button type="submit"  class="btn btn-primary float-right"><i class="fa fa-repeat"></i> Proses</button>
                    <?php } ?>
                </div>
            </form>
            <!-- /.card-body -->
        </div>
    </div>
    <?php } ?>
    <?php if (trim($dtldata['docjns']) <> 'PO') {  ?>
    <div class="col-md-6">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Step 2</h3>
            </div>
            <!-- /.card-header -->
            <div class="card card-body">
                <div class="col-sm-12" >
                    <?php if (trim($dtldata['status']) === 'E') {  ?>
                    <div class="form-group col-sm-12">
                        <label for="idbarang">Search/Scan Your Item Barcode</label>
                        <div class="input-group">
                            <?php /*
                            <select name="idbarang" id="idbarang" class="form-control " <?php if (trim($dtldata['status']) === 'I') { echo 'disabled'; } ?>>
                            </select> */ ?>
                            <input type="search" id="searchitem" class="col-sm-10" placeholder="Scan search your item ID" <?php if (trim($dtldata['status']) === 'I') { echo 'disabled'; } ?>>
                            <input type="hidden" name="idarea_default" class="form-control" id="idarea_default" value="<?php echo trim($dtldata['idarea_default']); ?>" placeholder="Document Input" readonly>
                            <input type="hidden" name="desc_master" class="form-control" id="desc_master" value="<?php echo trim($dtldata['description']); ?>" placeholder="Document Input" readonly>
                            <span class="help-block"></span>
                            <button  onclick="open_scan();" class="btn btn-primary float-left form-control col-sm-10"><i class="fa fa-barcode"></i></button>
                        </div>

                    </div>

                    <?php } ?>
                    <div class="form-group col-sm-12">
                        <div class="table-responsive" style='overflow-x:scroll;'>
                            <label for="tsearchitem">Select your Item</label>
                            <table id="tsearchitem" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th width="2%">Action</th>
                                    <th>Item Name</th>
                                    <th>Unit</th>
                                    <th>Category</th>
                                    <th>Item ID</th>

                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div>
            <?php /* DISABLE SEMENTARA
            <div class="card card-footer">
                <?php if (trim($dtldata['status']) === 'E') {   ?>
                    <form action="<?php echo base_url('stock/bbk/load_bbm_to_bbk')?>" method="post" id="formdtlbbm" >
                        <div class="card-body row">
                            <div class="form-group col-sm-6">
                                <input type="text" name="docnobbm" class="form-control" id="docnobbm" style="text-transform: uppercase" placeholder="Dockumen BBM" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="input-group" >
                                    <button class="btn btn-primary float-left" onclick="return confirm('load From BBM?')"><i class="fa fa-repeat"></i>Load Item BBM</button>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
 <?php } ?> */ ?>
            <!-- /.card-body -->
        </div>
    </div>
    <?php } ?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Step 3 - Detail Item Input</h3>
            </div>
            <div class="col-sm-6">
            <?php if (trim($dtldata['docjns']) === 'PO') {  ?>
                <?php if (trim($dtldata['status']) === 'E') {  ?>
                    <div class="form-group col-sm-12">
                        <label for="description">Load Dari PO (Pastikan PO Sudah Di Import)</label>
                        <div class="input-group" >
                            <a href="<?= base_url('stock/bbm/load_po_to_bbm') ?>" onclick="return confirm('load from outstanding po entry?')" class="btn btn-sm btn-success"><i class="fa fa-arrow-right"></i>Load Item Dari PO</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="tabbmdtl" class="table table-bordered table-striped"  cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="2%">Action</th>
                        <th>Nama Barang</th>
                        <th>Id Barang</th>
                        <th>Spesifikasi</th>
                        <th>Qty PO</th>
                        <th>Qty Diterima</th>
                        <th>Qty Kotor</th>
                        <th>Unit</th>
                        <th>Keterangan</th>
                        <th>Area</th>
                        <th>Rev/PO</th>
                        <th>Jenis</th>
                        <th>Supplier</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="<?= base_url('stock/bbm/clearEntryBbm') ?>" onclick="return confirm('Are you sure clear this entry?')" class="btn btn-default float-left"><i class="fa fa-arrow-left"></i>Back</a>
                <a href="<?= base_url('stock/bbm/clearQty0') ?>" onclick="return confirm('Hps qty 0 fitur ini tidak menganggap barang tersebut datang, apakah ingin dilanjutkan?')" class="btn btn-danger"><i class="fa fa-trash"></i>Hps Qty 0</a>
                <?php if (trim($dtldata['status']) === 'E') {  ?>
                <a href="<?= base_url('stock/bbm/finalEntryBbm') ?>" onclick="return confirm('Apakah akan melakukan final penerimaan barang?')" class="btn btn-success float-right"><i class="fa fa-arrow-right"></i>Final</a>
                <?php } ?>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="open_scan" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Scan Your Rack / Item</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <script src="<?php echo base_url('assets/pagejs/customjs/fiky_qrscan.min.js') ?>"></script>
                <div id="qr-reader" style="width: 100%; height: 100%"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->








<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input Data Area</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="formBbmDetail" method="post" enctype="multipart/form-data" role="form" >
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <input type="hidden" name="idlocationm"/>
                    <div class="form-group row">
                        <label class="control-label col-md-2">ID Barang</label>
                        <div class="col-md-4">
                            <input name="idbarangnm" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-1">NAME</label>
                        <div class="col-md-5">
                            <input name="nmbarang" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idlocation" class="control-label col-md-2">Area Default</label>
                        <div class="col-md-10">
                            <select name="idarea" id="idarea" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Qty PO</label>
                        <div class="col-md-7">
                            <input name="onhand" placeholder="Item qty" class="form-control inform ratakanan" type="text" MAXLENGTH="20" style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unit" placeholder="Item unit" class="form-control inform " type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Qty Diterima</label>
                        <div class="col-md-4">
                            <input name="onhandtrans" placeholder="Qty Diterima" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-2">Qty Kotor</label>
                        <div class="col-md-4">
                            <input name="onhanddirty" placeholder="Qty Kotor" value="0" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Batch/Spesifikasi</label>
                        <div class="col-md-8">
                            <select name="batch" id="batch" class="form-control">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btnNew" onclick="new_spec()" class="btn btn-primary">New Spec</button>
                        </div>
                        <?php /*
                        <label class="control-label col-md-2">Expire Date</label>
                        <div class="col-md-4">
                            <input name="expireitem" placeholder="Expire Date" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                        </div> */ ?>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Keterangan</label>
                        <div class="col-md-10">
                            <textarea name="descriptionm" id="descriptionm" class="form-control" rows="3" placeholder="Enter ..." ></textarea>
                            <!--input name="descriptionm" id="descriptionm"  style="text-transform:uppercase;" placeholder="Description" class="form-control inform" type="text" --->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_dtlbbm()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/bbm.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>