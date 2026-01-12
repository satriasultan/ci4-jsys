
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> Detail LPB Penerimaan Barang</h1>
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
        <div class="col-md-6">
            <div class="card card-green">
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
                                    <label for="idlocation">Location</label>
                                    <div class="input-group">
                                        <input type="text" name="idlocation" class="form-control" id="idlocation" style="text-transform: uppercase" value="<?php echo trim($dtlmst['nmlocation']); ?>" placeholder="Location" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="docno">Document</label>
                                    <input type="text" name="docno" class="form-control" id="docno" style="text-transform: uppercase" value="<?php echo trim($dtlmst['docno']); ?>" placeholder="Document Input" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- textarea -->
                                <div class="form-group">
                                    <label for="docdate">Date</label>
                                    <input type="text" name="docdate" class="form-control" id="docdate" value="<?php echo trim($dtlmst['docdate1']); ?>"  placeholder="Transaction Date" disabled="disabled">
                                </div>
                            </div>
                            <div class="col-sm-6 ">
                                <label for="description">Jenis</label>
                                <input type="text" name="docjns"  style="text-transform:uppercase;" maxlength="12" class="form-control" id="docjns" value="<?php echo trim($dtlmst['docjns']); ?>"  disabled="disabled">
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="description">Reference ID/PO Number</label>
                                    <input type="text" name="docref" class="form-control" id="docref"  style="text-transform: uppercase" value="<?php echo trim($dtlmst['docref']); ?>" placeholder="Reference ID" disabled="disabled">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 ">
                                <label for="description">Description</label>
                                <input type="text" name="description" class="form-control" id="description" style="text-transform: uppercase" placeholder="Description" value="<?php echo trim($dtlmst['description']); ?>" disabled="disabled">
                            </div>
                        </div>


                    </div>

                </form>
                <!-- /.card-body -->
            </div>
        </div>
</div>
<style>
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
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-green">
            <div class="card-header">
                <h3 class="card-title">Step 3 - Detail Item Input</h3>

            </div>

            <!-- /.card-header -->
            <div class="card-body" style='overflow-x:scroll;'>
                <table id="example1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
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
                    <?php $no=0; foreach($listdtl as $lar): $no++;?>
                        <tr>
                            <td width="2%"><?php echo $no;?></td>
                            <td><?php echo $lar->nmbarang;?></td>
                            <td><?php echo $lar->idbarang;?></td>
                            <td><?php echo $lar->batch;?></td>
                            <td><?php echo $lar->onhand;?></td>
                            <td><?php echo $lar->onhandtrans;?></td>
                            <td><?php echo $lar->onhanddirty;?></td>
                            <td><?php echo $lar->unit;?></td>
                            <td><?php echo $lar->description;?></td>
                            <td><?php echo $lar->nmarea;?></td>
                            <td><?php echo $lar->docref;?></td>
                            <td><?php echo $lar->docjns;?></td>

                            <td><?php echo $lar->nmsupplier;?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="<?= base_url('stock/bbm') ?>" class="btn btn-default float-left"><i class="fa fa-arrow-left"></i>Back</a>
                <?php /*<a href="#" onclick="return sendUrlToPrint('<?php echo base_url('assets/files/lpb_print/'.trim($dtlmst['docno']).'.pdf') ?>');" class="btn btn-danger float-right"><i class="fa fa-print"></i>_Cetak_X2_pdf</a>
 <a href="my.bluetoothprint.scheme://<?= base_url('stock/bbm/cetak_bt_bbm').'/?var='.trim($dtlmst['docno']);  ?>" onclick="return confirm('Are You Wanna Print...?')" class="btn btn-success float-right"><i class="fa fa-print"></i>_Cetak</a>
 */ ?>
                <a href="#" onclick="return sendUrlToPrint('<?php echo $urlx ?>');" class="btn btn-danger float-right"><i class="fa fa-print"></i>Cetak Print</a>
                <!--a href="my.bluetoothprint.scheme://http://192.168.7.104/btprint/response.php">localhost print</a--->
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
                        <label class="control-label col-md-2">Qty PO</label>
                        <div class="col-md-7">
                            <input name="onhand" placeholder="Item qty" class="form-control inform ratakanan" type="text" MAXLENGTH="20" style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-3">
                            <input name="unit" placeholder="Item unit" class="form-control inform " type="text" style="text-transform:uppercase;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Qty Bersih</label>
                        <div class="col-md-4">
                            <input name="onhandtrans" placeholder="Qty Bersih" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                        </div>
                        <label class="control-label col-md-2">Qty Kotor</label>
                        <div class="col-md-4">
                            <input name="onhanddirty" placeholder="Qty Kotor" value="0" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
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

<script type="text/javascript">
    $(function() {
        //calling untuk render pdf non https
/*
        console.log($("#docno").val() + 'KUONTOL');

        window.open();
        var dd = $('#docno').val();
        $.ajax({
            //url:"http://192.168.100.2/ims/stock/bbm/renderpdf"+ '/' + dd,
            url:"http://192.168.100.2/ims/api/validatorpush/renderpdf"+ '/' + dd,
            type: "GET",
            crossDomain: true,
            success: function () {
                //$("#comment-" + deleteID).slideUp("fast");
                console.log('Rendering PDF' + dd);
            }
        });

        fopen('http://www.example.com/filename.html');
*/



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