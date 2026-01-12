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

                        <div class="form-group col-sm-6 ">
                            <label for="idlocation">Location</label>
                            <div class="input-group">
                                <input type="text" name="docno" class="form-control" id="docno" style="text-transform: uppercase" value="<?php echo trim($dtlmst['nmlocation']); ?>" placeholder="Lokasi Gudang" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="docno">Document</label>
                            <input type="text" name="docno" class="form-control" id="docno" style="text-transform: uppercase" value="<?php echo trim($dtlmst['docno']); ?>" placeholder="Document Input" readonly>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="docdate">Tanggal Bon/Pemakaian</label>
                            <input type="text" name="docdate" class="form-control" id="docdate" value="<?php echo trim($dtlmst['docdate1']) ?>" placeholder="Transfer Date" required <?php if (trim($dtlmst['status']) !== 'I') { echo 'disabled'; } ?>>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="description">Kode Bagian</label>
                            <input type="text" name="docjns"  style="text-transform:uppercase;"  value="<?php echo trim($dtlmst['idsection']) ?>" class="form-control" id="docjns" placeholder="Type Reference" disabled>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="description">Reference ID/SPPB Number (Optional)</label>
                            <input type="text" name="docref" class="form-control" id="docref" value="<?php echo trim($dtlmst['docref']) ?>" style="text-transform: uppercase" placeholder="Reference ID" <?php if (trim($dtlmst['status']) !== 'I') { echo 'disabled'; } ?>>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <label for="description">Jenis</label>
                            <input type="text" name="docjns"  style="text-transform:uppercase;"  value="<?php echo trim($dtlmst['docjns']) ?>" class="form-control" id="docjns" placeholder="Type Reference" disabled>
                        </div>
                        <div class="col-sm-12 ">
                            <label for="description">Description</label>
                            <textarea name="description" id="description"  class="form-control" rows="3" placeholder="Enter ..." disabled><?php echo trim($dtlmst['description']) ?></textarea>
                            <!--input type="text" name="description" class="form-control" id="description" style="text-transform: uppercase" placeholder="Description" -->
                        </div>
                    </div>
                    <div class="row col-sm-7" >

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
                            <?php if (trim($dtlmst['status']) === 'I') {  ?>
                            <button type="submit"  class="btn btn-primary float-left"><i class="fa fa-repeat"></i> Proses</button>
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
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Spesifikasi</th>
                                    <th>Kategori</th>
                                    <th>Stok Terakhir</th>
                                    <th>Qty Pakai</th>
                                    <th>Unit</th>
                                    <th>ID Area</th>
                                    <th>Default Area</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($listdtl as $lu): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lu->idbarang;?></td>
                                        <td><?php echo $lu->nmbarang;?></td>
                                        <td><?php echo $lu->batch;?></td>
                                        <td><?php echo $lu->nmgroup;?></td>
                                        <td><?php echo $lu->onhand;?></td>
                                        <td><?php echo $lu->onhandtrans;?></td>
                                        <td><?php echo $lu->unit;?></td>
                                        <td><?php echo $lu->idarea;?></td>
                                        <td><?php echo $lu->nmarea;?></td>
                                        <td><?php echo $lu->description;?></td>

                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('stock/bbk') ?>" class="btn btn-default float-left"><i class="fa fa-arrow-left"></i>Back</a>


                    <a href="#" onclick="return sendUrlToPrint('<?php echo $urlx ?>');" class="btn btn-danger float-right"><i class="fa fa-print"></i>Cetak Print</a>
                    <!--a href="my.bluetoothprint.scheme://http://192.168.7.104/btprint/response.php">localhost print</a--->
                </div>
                <!-- /.card-body -->
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
                            <input name="descriptionm" id="descriptionm" placeholder="Description" class="form-control inform" type="text" >
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


<!--script-- type="application/javascript" src="<?= base_url('assets/pagejs/stock/bbm.js') ?>"></script-->

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


<script>
    function sendUrlToPrint(url){
        var  beforeUrl = 'intent:';
        var  afterUrl = '#Intent;';
        // Intent call with component
        afterUrl += 'component=ru.a402d.rawbtprinter.activity.PrintDownloadActivity;'
        afterUrl += 'package=ru.a402d.rawbtprinter;end;';
        document.location=beforeUrl+encodeURI(url)+afterUrl;
        return false;
    }
    // jQuery: set onclick hook for css class print-file
    $(document).ready(function(){

        $('.print-file').click(function () {
            return sendUrlToPrint($(this).attr('href'));
        });
    });


    // or direct
    /*
    <a href="#" onclick="return sendUrlToPrint('http(s)://yousite.com/test.txt');">.txt</a>";
*/

</script>