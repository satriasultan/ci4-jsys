<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu']))).' (centang u/ pembatalan per barang)';?></h1>
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
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <form id="formLPBvoid">
                <div class="row">

                    <div class="form-group col-sm-6">
                        <input type="text" name="docnovoid" class="form-control" id="docnovoid" placeholder="Masukan Dokumen LPB Untuk Melakukan Void" required>
                    </div>


                    <div class="form-group col-sm-2 ">
                        <button type="button" id="btnloadbbm" onclick="load_item_bbm_onvoid()" class="btn btn-primary form-control float-right"><i class="fa fa-repeat"></i> Load Item</button>
                    </div>
                    <?php /*
                    <div class="form-group col-sm-2">
                        <button type="button" id="btnloadbbm" onclick="delete_list_bbm_onvoid()" class="btn btn-danger form-control float-right"><i class="fa fa-repeat"></i> Delete Uncheck</button>
                    </div> */ ?>
                    <div class="form-group col-sm-2">
                        <button type="button" id="btnloadbbm" onclick="delete_list_bbm_onvoid()" class="btn btn-danger form-control float-right"><i class="fa fa-repeat"></i> Clear</button>
                    </div>
                    <div class="form-group col-sm-2">
                        <button type="button" onclick="process_bbm_onvoid()"   class="btn btn-success form-control float-right"><i class="fa fa-close"></i> Process Void</button>
                    </div>

                </div>
                </form>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <form action="#" method="post" enctype="multipart/form-data" id="formTabmdtl" name="formTabmdtl">
                <table id="tabbmdtl" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="2%">Action</th>
                        <th>Document LPB</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Spesifikasi</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Default Area</th>
                        <th>Description LPB</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </form>
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
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
                <form action="#" id="formProcessVoid" method="post" enctype="multipart/form-data" role="form" >
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <input type="hidden" name="idlocationm"/>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Document</label>
                        <div class="col-md-4">
                            <input name="docno" placeholder="Doc ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-1">Reference</label>
                        <div class="col-md-5">
                            <input name="docref" placeholder="Rev ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Void Date</label>
                        <div class="col-md-10">
                            <input name="docdate" id="dateinputx" placeholder="Void Date" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Void Descriptio</label>
                        <div class="col-md-10">
                            <textarea name="description" id="description" class="form-control" rows="2" placeholder="Enter ..." ></textarea>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_bbm_onvoid()" class="btn btn-primary">Save</button>
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

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>