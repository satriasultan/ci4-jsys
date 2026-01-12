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
                <a href="<?php echo base_url('capital/administration/print_pengajuan_dept_get')?>" class="btn btn-info" style="margin:10px" target=”_blank”><i class="fa fa-print"></i> Cetak </a>
                <a href="<?php echo base_url('capital/administration/print_pengajuan_dept_get')?>" class="btn btn-info" style="margin:10px" target=”_blank”><i class="fa fa-print"></i> Filter </a>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <div class="form-body">
                    <div class="row">
                        <div class="row col-md-6">
                            <div class="form-group col-md-6">
                                <div>
                                    <label class="control-label col-md-12">Kebutuhan</label>
                                    <div class="col-md-12">
                                        <select name="kebutuhanjenis" id="kebutuhanjenis" class="form-control inform" style="text-transform:uppercase;" >
                                            <!--option value="">--Pilih Hold--</option-->
                                            <option value="BARU">BARU </option>
                                            <option value="REPLACEMENT"> REPLACEMENT </option>

                                        </select>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div>
                                    <label class="control-label col-md-12">Nomor Asset IT</label>
                                    <div class="col-md-12">
                                        <input name="noassetit" id="noassetit"  placeholder="Input Nomor Asset Jika Replacement" class="form-control inform" type="text" style="text-transform:uppercase;">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div>
                                    <label class="control-label col-md-12">Nama Asset Lama</label>
                                    <div class="col-md-12">
                                        <input name="nmasset" id="nmasset"  placeholder="Ketik Nama Assets" class="form-control inform" type="text" style="text-transform:uppercase;">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Periode Budget</label>
                                <div class="col-md-12">
                                    <input name="docdate" id="docdate" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <!-- Column -->
                            <div class="form-group col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Total Budget</h4>
                                        <div class="text-end"> <span class="text-muted">Todays Income</span>
                                            <h1 class="font-light"><sup><i class="ti-arrow-up text-success"></i></sup> $120000000</h1>
                                        </div>
                                        <span class="text-success">20%</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 20%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                            <!-- Column -->
                            <div class="form-group col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Realisasi</h4>
                                        <div class="text-end"> <span class="text-muted">Todays Income</span>
                                            <h1 class="font-light"><sup><i class="ti-arrow-up text-info"></i></sup> $5,00000000</h1>
                                        </div>
                                        <span class="text-info">30%</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 30%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Column -->
                            <div class="form-group col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Sisa Budget</h4>
                                        <div class="text-end"> <span class="text-muted">Todays Income</span>
                                            <h1 class="font-light"><sup><i class="ti-arrow-up text-info"></i></sup> $5,00000000</h1>
                                        </div>
                                        <span class="text-info">30%</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 30%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Row -->
                    <div class="row">
                    </div>
                    <!-- Row -->

                </div>
            </div><!-- /.card-body -->
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="capital_pengajuan" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="8%">Action</th>
                        <th>PIC</th>
                        <th>Departement</th>
                        <th>Tgl Pengajuan</th>
                        <th>Status</th>
                        <th>Kebutuhan</th>
                        <th>No Assets</th>
                        <th>Kategori</th>
                        <th>Sub Kategori</th>
                        <th>Spesifikasi</th>
                        <th>Level</th>
                        <th>Perkiraan Harga</th>
                        <th>Referensi</th>
                        <th>Deskripsi</th>
                        <th>Alasan Ditolak</th>

                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div>
    </div>
</div>

<!--Modal untuk Input Menu Utama-->
<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formPengajuanBudget" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="row">
                            <div class="form-group col-md-1">
                                <label class="control-label col-md-12">Kebutuhan</label>
                                <div class="col-md-12">
                                    <select name="kebutuhanjenis" id="kebutuhanjenis" class="form-control inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="BARU">BARU </option>
                                        <option value="REPLACEMENT"> REPLACEMENT </option>

                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nomor Asset IT</label>
                                <div class="col-md-12">
                                    <input name="noasset" id="noasset"  placeholder="Input Nomor Asset Jika Replacement" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Nama Asset Lama</label>
                                <div class="col-md-12">
                                    <input name="nmasset" id="nmasset"  placeholder="Ketik Nama Assets" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">User Pemakai/Penanggung Jawab</label>
                                <div class="col-md-12">
                                    <select name="nik" id="nik" class="form-control select2_kary" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <!--div class="form-group col-md-3">
                                <label class="control-label col-md-12">User Pengguna/PIC</label>
                                <div class="col-md-12">
                                    <input name="nmlengkap" id="nmlengkap"  placeholder="Mohon Input User Pengguna/ PIC" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div-->
                            <div class="form-group col-md-3">
                                <label for="kddept">Pilih Department</label>
                                <select name="kddept" id="kddept" class="form-control inform" placeholder="Pilih Department">
                                </select>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Kategori</label>
                                <div class="col-md-12">
                                    <select name="idgroup" id="idgroup" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Sub Kategori</label>
                                <div class="col-md-12">
                                    <select name="idsubgroup" id="idsubgroup" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Level</label>
                                <div class="col-md-12">
                                    <select name="kdlvl" id="kdlvl" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Perkiraan Harga</label>
                                <div class="col-md-12">
                                    <input name="estprice_kdlvl" placeholder="Ketik perkiraan harga" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Budget/Unbudget</label>
                                <div class="col-md-12">
                                    <select name="budgeting" id="budgeting" class="form-control inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="BUDGET">BUDGET </option>
                                        <!--option value="UNBUDGET"> UNBUDGET </option-->
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Nilai Budget</label>
                                <div class="col-md-12">
                                    <input name="vbudgeting" id="vbudgeting"  placeholder="Ketik Nilai Budget Yang Diajukan" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Tanggal Pengajuan</label>
                                <div class="col-md-12">
                                    <input name="docdate" id="docdate" class="form-control" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Tanggal Rencana Realisasi</label>
                                <div class="col-md-12">
                                    <input name="daterealization" id="daterealization" class="form-control" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Cost Center</label>
                                <div class="col-md-12">
                                    <select name="idcostcenter" id="idcostcenter" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Spesifikasi</label>
                                <div class="col-md-12">
                                    <input name="spec" placeholder="Ketik spesifikasi sesuai ketentuan" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Referensi/link</label>
                                <div class="col-md-12">
                                    <input name="estvendor" placeholder="Referensi bisa nama vendor/ contact / Link toko online/ " class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <?php /*
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nama Pembanding Vendor 1</label>
                                <div class="col-md-12">
                                    <input name="vendor_ref1" placeholder="Ketik nama vendor 1" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Harga Perolehan Vendor 1</label>
                                <div class="col-md-12">
                                    <input name="pricev_ref1" id="pricev_ref1"  placeholder="Tulis Harga Vendor 1" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nama Pembanding Vendor 2</label>
                                <div class="col-md-12">
                                    <input name="vendor_ref2" id="vendor_ref2" placeholder="Ketik nama vendor 2" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Harga Perolehan Vendor 2</label>
                                <div class="col-md-12">
                                    <input name="pricev_ref2" id="pricev_ref2"  placeholder="Tulis Harga Vendor 2" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Nama Pembanding Vendor 3</label>
                                <div class="col-md-12">
                                    <input name="vendor_ref3" id="vendor_ref3" placeholder="Ketik nama vendor 3" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="control-label col-md-12">Harga Perolehan Vendor 3</label>
                                <div class="col-md-12">
                                    <input name="pricev_ref3"  id="pricev_ref3" placeholder="Tulis Harga Vendor 3" class="form-control inform ratakanan fikyseparator" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
 */ ?>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Jenis Budget</label>
                                <div class="col-md-12">
                                    <select name="btype" id="btype" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Sub Jenis Budget</label>
                                <div class="col-md-12">
                                    <select name="bsubtype" id="bsubtype" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Account Kategori</label>
                                <div class="col-md-12">
                                    <select name="acctype" id="acctype" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-12">Document ID</label>
                                <div class="col-md-12">
                                    <input name="docno" id="docno" placeholder="Document ID" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label col-md-12">Keterangan</label>
                                <div class="col-md-12">
                                    <textarea  style="text-transform: uppercase"  name="description" id="description" class="form-control inform" rows="4" placeholder="Tulis keterangan ..." ></textarea>
                                    <span class="help-block"></span>
                                </div>
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

<script type="application/javascript" src="<?= base_url('assets/pagejs/capital/budget/budgeting.js') ?>"></script>
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
            singleDatePicker: false,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY - DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });

</script>