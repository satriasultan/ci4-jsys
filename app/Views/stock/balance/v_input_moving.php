<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1>
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

                <div class="card-body row">
                    <div class="col-md-8 offset-md-2">
                        <div class="input-group">
                            <input type="search" id="searchitem" class="form-control form-control-md" placeholder="Scan search your item barcode or rack/area" >
                            <input type="hidden" name="idlocationuser" value="<?= $loccode ?>">
                            <span class="help-block"></span>
                            <button  onclick="open_scan();" class="btn btn-md btn-primary float-left"><i class="fa fa-barcode"></i></button>
                        </div>
                        <?php /*
                        <div class="input-group">
                            <input type="search" id="searchitem" class="form-control form-control-lg" placeholder="Scan or type area id/item id to search the location">
                            <div class="input-group-append">
                                <button class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div> */ ?>
                    </div>
                </div>
                <div class="card-body row table-responsive" style='overflow-x:scroll;'>
                    <table id="tsearchitem" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="2%">Action</th>
                        <th>Nama Barang</th>
                        <th>Spesifikasi</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Area</th>
                        <th>Category</th>
                        <th>Item ID</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div><!-- /.card-body -->
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('stock/balance/moving') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i>Kembali</a>
                   <?php /*
                    <a href="<?= base_url('master/item') ?>" class="btn btn-default"> Clear Input</a>
                    <button id="btnSave" onclick="saveInputItem();" class="btn btn-primary float-right">Submit</button>
                    */ ?>
                </div>

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
                <form action="#" id="form" method="post" enctype="multipart/form-data" role="form" >
                    <input type="hidden" value="UPDATE" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="control-label col-md-2">ID Barang</label>
                        <div class="col-md-10">

                            <input type="hidden" name="idlocationold" >
                            <input type="hidden" name="idareaold" >
                            <input name="idbarangmove" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Nama Barang</label>
                        <div class="col-md-10">
                            <input name="nmbarangmove" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Spesifikasi</label>
                        <div class="col-md-6">
                            <input type="text" name="batch" class="form-control" id="batch" placeholder="Spesifikasi">
                        </div>
                        <label class="control-label col-md-1">Tanggal</label>
                        <div class="col-md-3">
                            <input type="text" name="trxdate" class="form-control" id="trxdate" placeholder="Tanggal Pindah">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Qty</label>
                        <div class="col-md-7">
                            <input name="qty" placeholder="Item qty" class="form-control inform ratakanan" type="text"  style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unit" placeholder="Item unit" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Area Lama</label>
                        <div class="col-md-10">
                            <input name="nmarea" placeholder="AREA NAME" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Qty Pindah</label>
                        <div class="col-md-7">
                            <input name="qtymove" placeholder="Item qty" class="form-control inform ratakanan" type="text" MAXLENGTH="20" style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unitmove" placeholder="Item unit" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Area Pindah</label>
                        <div class="col-md-10">
                            <select name="idareamove" id="idareamove" class="form-control"  >
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Keterangan</label>
                        <div class="col-md-10">
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter ..." ></textarea>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="saveMovingStock()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Bootstrap modal scan -->
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


<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/balance_moving.js') ?>"></script>

<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
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
        $('#trxdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#trxdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#form').bootstrapValidator('updateStatus', 'trxdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'trxdate');
        });

        $('#trxdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>