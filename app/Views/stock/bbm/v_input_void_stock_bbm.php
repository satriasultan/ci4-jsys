<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> Void Dokumen LPB</h1>
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

<?php echo $message;?>
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-default">
            <?php /*<div class="card-header">
                <h3 class="card-title"><small>Input your moving alocation stock position area</small></h3>
            </div>*/ ?>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('stock/bbm/verifikasi_void_mst_stock_bbm')?>" method="post" id="formInputTransfers" enctype="multipart/form-data" role="form">
                <div class="card-body row">
                    <div class="row col-sm-5">
                        <div class="form-group col-sm-12 ">
                            <label for="idlocation">Location</label>
                            <div class="input-group">
                                <select name="idlocation" id="idlocation" class="form-control" disabled >
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="docno">Document</label>
                            <input type="text" name="docno" class="form-control" id="docno" value="<?php echo trim($dtldata['docno']); ?>" placeholder="Document Input" readonly>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="docdate">Void Date</label>
                            <input type="text" name="docdate" class="form-control" id="docdate" placeholder="Transfer Date" required <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?>>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="description">Reference Void</label>
                            <input type="text" name="docref"  style="text-transform:uppercase;" class="form-control" id="docref" placeholder="Reference ID" <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?> required>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="description">Jenis</label>
                            <input type="text" name="docjns"  style="text-transform:uppercase;" maxlength="12" class="form-control" id="docjns" placeholder="Type Reference" <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?> required>
                        </div>
                        <div class="form-group col-sm-12 ">
                            <label for="description">Description</label>
                            <input type="text" name="description"  style="text-transform:uppercase;" class="form-control" id="description" placeholder="Description" <?php if (trim($dtldata['status']) !== 'I') { echo 'disabled'; } ?>>
                        </div>
                    </div>
                    <div class="row col-sm-7" >
                        <div class="form-group col-sm-12">
                            <label for="idbarang">Search/Scan Your Item Barcode</label>
                            <div class="input-group">
                                <select name="idbarang" id="idbarang" class="form-control " <?php if (trim($dtldata['status']) === 'I') { echo 'disabled'; } ?>>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <?php /*
                        <div class="col-sm-12 table-responsive" style='overflow-x:scroll;'>
                            <label for="tsearchitem">Choose your avaiable stock</label>
                            <table id="tsearchitem" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th width="2%">Action</th>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Area Name</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div> */ ?>
                    </div><!-- /.card-body -->
                    <div class="row col-sm-12">
                        <div class="form-group col-sm-12 ">
                            <?php if (trim($dtldata['status']) === 'I') {  ?>
                            <button type="submit"  class="btn btn-primary float-left" onclick="return confirm('Data yang tervoid tidak dapat dikembalikan, anda yakin?')" ><i class="fa fa-repeat"></i> Proses Void</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div><!-- /.card-header -->
                        <div class="card-body table-responsive" style='overflow-x:scroll;'>
                            <table id="tabbmdtl" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <?php /* <th width="2%">Action</th> */ ?>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>ID Area</th>
                                    <th>Default Area</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('stock/bbm/clearEntryVoidBbm') ?>" onclick="return confirm('Are you sure clear this entry?')" class="btn btn-default float-left"><i class="fa fa-arrow-left"></i>Back</a>
                    <?php /*
                    <a href="<?= base_url('stock/bbm/finalEntryVoidBbm') ?>" onclick="return confirm('Are you sure final this entry?')" class="btn btn-success float-right"><i class="fa fa-arrow-right"></i>Final</a>
                        */ ?>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>

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
                        <label class="control-label col-md-2">ITEM ID</label>
                        <div class="col-md-4">
                            <input name="idbarangnm" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-1">NAME</label>
                        <div class="col-md-5">
                            <input name="nmbarang" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idlocation" class="control-label col-md-2">DEFAULT AREA POSITION</label>
                        <div class="col-md-10">
                            <select name="idarea" id="idarea" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">QTY</label>
                        <div class="col-md-7">
                            <input name="onhandtrans" placeholder="Item qty" class="form-control inform ratakanan" type="text" MAXLENGTH="20" style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unit" placeholder="Item unit" class="form-control inform " type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">DESCRIPTION</label>
                        <div class="col-md-10">
                            <input name="descriptionm" id="descriptionm"  style="text-transform:uppercase;" placeholder="Description" class="form-control inform" type="text" >
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
<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/void_item.js') ?>"></script>
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