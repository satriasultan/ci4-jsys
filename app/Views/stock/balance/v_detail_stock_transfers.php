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
            <form  action="#" method="post" id="formInputTransfers" enctype="multipart/form-data" role="form">
                <div class="card-body row">
                    <div class="row col-sm-5">

                        <div class="form-group col-sm-12 ">
                            <label for="idlocation">Search/Scan Your Destination Location</label>
                            <div class="input-group">
                                <input type="text" name="idlocation" class="form-control" id="idlocation" value="<?php echo trim($dtlmst['nmlocationto']); ?>" placeholder="Document Input" disabled>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="docno">Document</label>
                            <input type="text" name="docno" class="form-control" id="docno" value="<?php echo trim($dtlmst['docno']); ?>" placeholder="Document Input" disabled>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="docdate">Transfer Date</label>
                            <input type="text" name="docdate" class="form-control" id="docdate" value="<?php echo trim($dtlmst['docdate1']); ?>" placeholder="Transfer Date" disabled>
                        </div>
                        <div class="form-group col-sm-12 ">
                            <label for="description">Reference ID</label>
                            <input type="text" name="docref" class="form-control" id="docref" placeholder="Reference ID" value="<?php echo trim($dtlmst['docref']); ?>" disabled>
                        </div>
                        <div class="form-group col-sm-12 ">
                            <label for="description">Description</label>
                            <input type="text" name="description" class="form-control" id="description" placeholder="Description" value="<?php echo trim($dtlmst['description']); ?>" disabled>
                        </div>
                    </div>
                    <div class="row col-sm-7" >
                        <div class="form-group col-sm-12">
                            <label for="idbarang">Search/Scan Your Item Barcode</label>
                            <div class="input-group">
                                <select name="idbarang" id="idbarang" class="form-control " disabled>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
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
                        </div>

                    </div><!-- /.card-body -->
                    <div class="row col-sm-12">
                        <div class="form-group col-sm-12 ">

                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        </div><!-- /.card-header -->
                        <div class="card-body table-responsive" style='overflow-x:scroll;'>
                            <table id="talocationtransfer" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th width="2%">Action</th>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Spesifikasi</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Gudang asal</th>
                                    <th>Gudang tujuan</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($listdtl as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td>BUTTON</td>
                                        <td><?php echo $lu->idbarang;?></td>
                                        <td><?php echo $lu->nmbarang;?></td>
                                        <td><?php echo $lu->spesifikasi;?></td>
                                        <td><?php echo $lu->onhandtrans;?></td>
                                        <td><?php echo $lu->unit;?></td>
                                        <td><?php echo $lu->nmlocationfrom;?></td>
                                        <td><?php echo $lu->nmlocationto;?></td>
                                        <td><?php echo $lu->description;?></td>
                                        <td><?php echo $lu->nmstatus;?></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('stock/balance/transfers') ?>" class="btn btn-default float-left"><i class="fa fa-arrow-left"></i>Back</a>

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
                <form action="#" id="formAddItemTransfer" method="post" enctype="multipart/form-data" role="form" >
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-group row">
                        <label class="control-label col-md-2">ITEM ID</label>
                        <div class="col-md-4">
                            <input type="hidden" name="idlocationfrom" >
                            <input type="hidden" name="idlocationto" >
                            <input name="idbarangmove" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-1">NAME</label>
                        <div class="col-md-5">
                            <input name="nmbarangmove" placeholder="Item ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-md-2">QTY ONHAND</label>
                        <div class="col-md-7">
                            <input name="qtyonhand" placeholder="Item qty onhand" class="form-control inform ratakanan" type="text"  style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unitonhand" placeholder="Item unit" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Area ID</label>
                        <div class="col-md-4">
                            <input name="idareato" placeholder="Area ID" class="form-control inform" type="hidden" style="text-transform:uppercase;">
                            <input name="idareafrom" placeholder="Area ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-1">Name</label>
                        <div class="col-md-5">
                            <input name="nmareato" placeholder="Area Name" class="form-control inform" type="hidden" style="text-transform:uppercase;">
                            <input name="nmareafrom" placeholder="Area Name" class="form-control inform" type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">QTY TRANSFERS</label>
                        <div class="col-md-7">
                            <input name="qtytransfers" placeholder="Item qty Transfers" class="form-control inform ratakanan" type="text" MAXLENGTH="20" style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unittransfers" placeholder="Item unit" class="form-control inform " type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">DESCRIPTION</label>
                        <div class="col-md-10">
                            <input name="descriptionm" id="descriptionm" placeholder="Description" class="form-control inform" type="text" >
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_dtltransfers()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!--script type="application/javascript" src="<?= base_url('assets/pagejs/stock/balance_transfers.js') ?>"></script-->
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