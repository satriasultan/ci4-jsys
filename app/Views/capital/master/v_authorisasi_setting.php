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
                <h3 class="card-title">Authorisasi Level Settings</h3>
            </div>
            <div class="card-body">
                <div class="col-md-3">
                    <button type="button"  href="#"  onclick="add_authorization()" class="btn btn-sm btn-success form-control float-right"><i class="fa fa-plus"></i> Tambahkan User</button>
                </div>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="capital_authorization" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <!--th class="width-90">Action</th-->
                        <th>Username</th>
                        <th>Departement</th>
                        <th>Level 1</th>
                        <th>Level 2</th>
                        <th>Level 3</th>
                        <th>Level 4</th>
                        <th>Deskripsi</th>
                        <th>Hold</th>
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
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formAuthorization" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Pilih Username</label>
                            <div class="col-md-12">
                                <select name="username" id="username" class="form-control" style="text-transform:uppercase;" >
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Pilih Departmen</label>
                            <div class="col-md-12">
                                <select name="kddept" id="kddept" class="form-control" style="text-transform:uppercase;" >
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-12 control-label" style="font-size: 100%">HOLD MODUL</label>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level1" id="level1_yes" value="YES"  autocomplete="off">
                                        YES
                                    </label>
                                </div>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level1" id="level1_no" value="NO" autocomplete="off">
                                        NO
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-12 control-label" style="font-size: 100%">LEVEL 2</label>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level2" id="level2_yes" value="YES" autocomplete="off">
                                        YES
                                    </label>
                                </div>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level2" id="level2_no" value="NO" autocomplete="off">
                                        NO
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-12 control-label" style="font-size: 100%">LEVEL 3</label>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level3" id="level3_yes" value="YES" autocomplete="off">
                                        YES
                                    </label>
                                </div>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level3" id="level3_no" value="NO" autocomplete="off">
                                        NO
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-12 control-label" style="font-size: 100%">LEVEL 4</label>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level4" id="level4_yes" value="YES" autocomplete="off">
                                        YES
                                    </label>
                                </div>
                                <div class="radio col-sm-6">
                                    <label class="">
                                        <input type="radio" name="level4" id="level4_no" value="NO" autocomplete="off">
                                        NO
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Description</label>
                            <div class="col-md-12">
                                <input name="description" placeholder="Deskripsi" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hold</label>
                            <div class="col-md-12">
                                <select name="chold" class="form-control inform" style="text-transform:uppercase;" >
                                    <!--option value="">--Pilih Hold--</option-->
                                    <option value="NO"> NO </option>
                                    <option value="YES">YES </option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="saveAuthorization()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="application/javascript" src="<?= base_url('assets/pagejs/capital/masters/master_authorisasi.js') ?>"></script>
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