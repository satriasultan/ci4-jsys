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
        <div class="card card-dark">
            <div class="card-header">
                <a href="#" class="btn btn-primary" onclick="add_pengajuan_it()" style="margin:10px"><i class="fa fa-plus"></i>  Tambah  </a>
                <!--<a href="<?php /*echo base_url('capital/administration/print_pengajuan_dept_get')*/?>" class="btn btn-info" style="margin:10px" target=”_blank”><i class="fa fa-print"></i> Cetak </a>-->
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="jasa_pembayaran" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">ID</th>
                        <th width="8%">AKSI(Input)</th>
                        <th>NAMA PEMBAYARAN</th>
                        <th>JENIS PEMBAYARAN</th>
                        <th>PLANT</th>
                        <th>SUPPLIER</th>
                        <th>RENEWAL</th>
                        <th>TGL BAYAR</th>
                        <th>REMINDER</th>
                        <th>BIAYA</th>
                        <th>STATUS</th>
                        <!--<th>UPDATE</th>-->
                        <th>KETERANGAN</th>
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
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formPengajuan" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Kategori</label>
                                <div class="col-md-12">
                                    <select name="idgroup" id="idgroup" class="form-control inform" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Sub Kategori</label>
                                <div class="col-md-12">
                                    <select name="idsubgroup" id="idsubgroup" class="form-control inform" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php /*<div class="form-group col-md-6">
                                <label class="control-label col-md-12">Kebutuhan/Jenis Jasa</label>
                                <div class="col-md-12">
                                    <select name="kebutuhanjenis" id="kebutuhanjenis" class="form-control inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="INTERNET">INTERNET </option>
                                        <option value="LISENSI"> LISENSI </option>
                                        <option value="PERANGKAT"> PERANGKAT </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>*/ ?>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Nomor Asset IT</label>
                                <div class="col-md-12">
                                    <input name="noassetit" id="noassetit"  placeholder="Input Nomor Asset Jika Ada" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Nama dan Jenis Pembayaran</label>
                                <div class="col-md-12">
                                    <input name="nmpembayaran" placeholder="Input Nama Pembayaran Kamu" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Plant/Pengguna</label>
                                <div class="col-md-12">
                                    <input name="nmpengguna" id="nmpengguna"  placeholder="Nama Pengguna" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kddept_filter">Renewal</label>
                                <select name="renewal" id="renewal" class="form-control inform" style="text-transform:uppercase;" >
                                    <!--option value="">--Pilih Hold--</option-->
                                    <option value="BULANAN"> BULANAN </option>
                                    <option value="HARIAN">HARIAN </option>
                                    <option value="TAHUNAN"> TAHUNAN </option>
                                    <option value="5TAHUNAN"> 5TAHUNAN </option>
                                </select>
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
                                <label class="control-label col-md-12">Tanggal Jatuh Tempo Awal</label>
                                <div class="col-md-12">
                                    <input name="duedate_in" id="duedate_in" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Tanggal Jatuh Tempo Selanjutnya</label>
                                <div class="col-md-12">
                                    <input name="duedate_next" id="duedate_next" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-md-12">Nama Supplier</label>
                            <div class="col-md-12">
                                <input name="nmsupplier" placeholder="Nama Supplier " class="form-control inform" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
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

<script type="application/javascript" src="<?= base_url('assets/pagejs/capital/administration/jasa_pembayaran.js') ?>"></script>
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

        $('#duedate_in').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#duedate_in').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#duedate_in').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#duedate_next').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#duedate_next').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#duedate_next').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });

</script>