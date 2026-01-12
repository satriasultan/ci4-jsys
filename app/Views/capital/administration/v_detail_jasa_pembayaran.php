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
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
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
    <div class="col-md-12">
        <div class="card card-indigo">
            <div class="card-header">
                <a href="<?php echo base_url('capital/administration/jasa_pembayaran')?>" class="btn btn-lg btn-dark" style="margin:10px"><i class="fa fa-arrow-left"></i> Kembali </a>
                <a href="#" class="btn btn-lg btn-primary" onclick="add_pembayaran_detail(<?php echo $id ?>)" style="margin:10px"><i class="fa fa-plus"></i>  Tambah Pembayaran</a>
                <div class="row float-right">
                    <h2 class="col-sm-12"> <?php echo $mst['nmpembayaran']?></h2>
                    <h3 class="col-sm-12"> <?php echo $mst['nmsupplier']?></h3>
                    <input type="hidden" name="idrev_x" value="<?php echo trim($id) ;?>"/>
                </div>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="jasa_pembayaran" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">ID</th>
                        <th width="1%">Action</th>
                        <th>NAMA PEMBAYARAN</th>
                        <th>JENIS PEMBAYARAN</th>
                        <th>SUPPLIER</th>
                        <th>ASSET NO</th>
                        <th>INV NO</th>
                        <th>FTAX NO</th>
                        <th>RENEWAL</th>
                        <th>T.TERIMA INV</th>
                        <th>TGL SETOR</th>
                        <th>PPN</th>
                        <th>JNSPPN</th>
                        <th>TOTAL</th>
                        <th>STATUS</th>
                        <th>JTEMPO SELANJUTNYA</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div>
    </div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade" id="filter_cetak">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter-cetak" method="post"  action="<?php echo base_url('capital/administration/print_pengajuan_dept')?>" >
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data dan Cetak</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kddept_filter">Pilih Department</label>
                        <select name="kddept_filter" id="kddept_filter" class="form-control" placeholder="Pilih Department">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_filter">Pilih Status</label>
                        <select name="status_filter" id="status_filter" class="form-control" placeholder="Pilih Status">
                            <option value="I">INPUT ADMIN</option>
                            <option value="T">DITOLAK</option>
                            <option value="C">DIBATALKAN USER</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idgroup_filter">Pilih Kategori</label>
                        <select name="idgroup_filter" id="idgroup_filter" class="form-control" placeholder="Pilih Kategori">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idsubgroup_filter">Pilih Sub Kategori</label>
                        <select name="idsubgroup_filter" id="idsubgroup_filter" class="form-control" placeholder="Pilih Sub Kategori">
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" id="btn-filter-tx" class="btn btn-primary"><i class="fa fa-print"></i> Cetak</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--Modal untuk Input Menu Utama-->
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #00aced;">
                <h4 class="modal-title" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formPengajuan" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <input type="hidden" name="idrev"/>
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Nama Pembayaran</label>
                                <div class="col-md-12">
                                    <input name="nmpembayaran" id="nmpembayaran" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kddept_filter">Supplier</label>
                                <input name="nmsupplier" id="nmsupplier" class="form-control inform" type="text" style="text-transform:uppercase;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="kddept_filter">Tanggal JTempo Terakhir</label>
                                <div class="col-md-12">
                                    <input name="duedate_next" id="duedate_next" class="form-control inform" type="text" style="text-transform:uppercase;" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Tanggal Pembayaran Selanjutnya</label>
                                <div class="col-md-12">
                                    <input name="duedate_renewal" id="duedate_renewal" class="form-control inform" type="text" style="text-transform:uppercase;" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Tanggal Terima Nota/Invoice</label>
                                <div class="col-md-12">
                                    <input name="invoice_date" id="invoice_date" class="form-control inform" type="text" data-date-format="dd-mm-yyyy" >
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Tanggal Setor Tagihan</label>
                                <div class="col-md-12">
                                    <input name="docdate" id="docdate" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Nomor Invoice</label>
                                <div class="col-md-12">
                                    <input name="idinvoice" placeholder="Ketik nomor invoice" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Nomor Faktur Pajak</label>
                                <div class="col-md-12">
                                    <input name="idftax" placeholder="Ketik nomor faktur pajak" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label for="kddept_filter">PPN</label>
                                <select name="ppn" id="ppn" class="form-control inform" style="text-transform:uppercase;" >
                                    <!--option value="">--Pilih Hold--</option-->
                                    <option value="YES">PPN </option>
                                    <option value="NO"> NONPPN </option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="kddept_filter">INCLUDE/EXCLUDE</label>
                                <select name="jnsppn" id="jnsppn" class="form-control inform" style="text-transform:uppercase;" >
                                    <!--option value="">--Pilih Hold--</option-->
                                    <option value="INCLUDE">INCLUDE </option>
                                    <option value="EXCLUDE">EXCLUDE </option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Harga/Biaya</label>
                                <div class="col-md-12">
                                    <input name="ttlnetto" placeholder="Ketik harga pembayaran" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="control-label col-md-12">Status Pembayaran</label>
                                <div class="col-md-12">
                                    <select name="status" id="status" class="form-control inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="SETOR KASIR">SETOR KASIR </option>
                                        <option value="TERBAYAR">TERBAYAR </option>
                                        <option value="HOLD">HOLD </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kddept_filter">Renewal</label>
                                <input name="renewal" id="renewal" class="form-control inform" type="text" style="text-transform:uppercase;">
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12">Keterangan</label>
                            <div class="col-md-12">
                                <textarea  style="text-transform: uppercase"  name="description" id="description" class="form-control inform" rows="4" placeholder="Tulis keterangan ..." ></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="savePengajuan()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/capital/administration/jasa_pembayaran_detail.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        $('#duedate_renewal').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#duedate_renewal').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formPengajuan').bootstrapValidator('updateStatus', 'duedate_renewal', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#duedate_renewal').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /**/
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
            $('#formPengajuan').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        $('#invoice_date').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#invoice_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formPengajuan').bootstrapValidator('updateStatus', 'invoice_date', 'NOT_VALIDATED').bootstrapValidator('validateField', 'invoice_date');
        });
        $('#invoice_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });

</script>