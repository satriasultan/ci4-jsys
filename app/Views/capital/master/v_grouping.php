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
    <div class="col-md-6">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Kategori</h3>
            </div>
            <div class="card-body">
                <div class="col-md-5">
                    <button type="button"  href="#"  onclick="add_mgroup()" class="btn btn-sm btn-success form-control float-right"><i class="fa fa-plus"></i> Input Kategori</button>
                </div>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="tmgroup" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th class="width-90">Action</th>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Description</th>
                        <th>Hold</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->

        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Sub Kategori</h3>
            </div>
            <div class="card-body">
                <div class="col-md-5">
                    <button type="button"  href="#"  onclick="add_msubgroup()" class="btn btn-sm btn-success form-control float-right"><i class="fa fa-plus"></i> Input Sub Kategori</button>
                </div>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="tmsubgroup" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="8%">Action</th>
                        <th>ID Sub</th>
                        <th>Sub Kategori</th>
                        <th>Kategori</th>
                        <th>Description</th>
                        <th>Hold</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->

        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Jenis</h3>
            </div>
            <div class="card-body">
                <div class="col-md-3">
                    <button type="button"  href="#"  onclick="add_mtype()" class="btn btn-sm btn-success form-control float-right"><i class="fa fa-plus"></i> Input Jenis</button>
                </div>
            </div>
            <div class="card-body " style='overflow-x:scroll;'>
                <table id="tmtype" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="8%">Action</th>
                        <th>ID Sub</th>
                        <th>Sub Kategori</th>
                        <th>Kategori</th>
                        <th>Description</th>
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
                <form action="#" id="formMgroup" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="typeMgroup"/>
                    <input type="hidden" name="idMgroup"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">ID</label>
                            <div class="col-md-12">
                                <input name="idgroupMgroup" placeholder="Group ID" class="form-control inform" type="text" MAXLENGTH="6" style="text-transform:uppercase;" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Group Name</label>
                            <div class="col-md-12">
                                <input name="nmgroupMgroup" placeholder="Group Name" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <input name="grouptypeMgroup" placeholder="Group Type" value="CIT" class="form-control inform" type="hidden" style="text-transform:uppercase;" >
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Description</label>
                            <div class="col-md-12">
                                <input name="descriptionMgroup" placeholder="Deskripsi" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hold</label>
                            <div class="col-md-12">
                                <select name="choldMgroup" class="form-control chold inform" style="text-transform:uppercase;" >
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
                <button type="button" id="btnSave" onclick="saveMgroup()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- MODAL SUB GROUP-->
<div class="modal fade" id="modal_form_msubgroup" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title-msubgroup" id="myModalLabelMsubgroup">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formMsubgroup" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="typeMsubgroup"/>
                    <input type="hidden" name="idMsubgroup"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Kategori</label>
                            <div class="col-md-12">
                                <select name="idgroupMsubgroup" id="idgroupMsubgroup" class="form-control idgroup" style="text-transform:uppercase;" >
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">ID Sub Kategori</label>
                            <div class="col-md-12">
                                <input name="idsubgroupMsubgroup" placeholder="Sub Kategori ID" class="form-control inform" type="text" MAXLENGTH="6" style="text-transform:uppercase;" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Sub Kategori</label>
                            <div class="col-md-12">
                                <input name="nmsubgroupMsubgroup" placeholder="Sub Kategori Name" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <input name="grouptypeMsubgroup" placeholder="Group Type" value="CIT" class="form-control inform" type="hidden" style="text-transform:uppercase;" >
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Deskripsi</label>
                            <div class="col-md-12">
                                <input name="descriptionMsubgroup" placeholder="Deskripsi" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hold</label>
                            <div class="col-md-12">
                                <select name="choldMsubgroup" class="form-control chold inform" style="text-transform:uppercase;" >
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
                <button type="button" id="btnSaveMsubgroup" onclick="saveMsubgroup()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- MODAL MTYPE-->
<div class="modal fade" id="modal_form_mtype" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title-mtype" id="myModalLabelMtype">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="formMtype" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="typeMtype"/>
                    <input type="hidden" name="idMtype"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Kategori</label>
                            <div class="col-md-12">
                                <select name="idgroupMtype" id="idgroupMtype" class="form-control idgroup" style="text-transform:uppercase;" >
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">ID Sub Kategori</label>
                            <div class="col-md-12">
                                <select name="idsubgroupMtype" id="idsubgroupMtype" class="form-control " style="text-transform:uppercase;" >
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">ID Type</label>
                            <div class="col-md-12">
                                <input name="idtype" placeholder="ID Type" class="form-control inform" type="text" MAXLENGTH="6" style="text-transform:uppercase;" required>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Type</label>
                            <div class="col-md-12">
                                <input name="nmtype" placeholder="Nama Type" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <input name="grouptypeMtype" placeholder="Group Type" value="CIT" class="form-control inform" type="hidden" style="text-transform:uppercase;" >
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Deskripsi</label>
                            <div class="col-md-12">
                                <input name="descriptionMtype" placeholder="Deskripsi" class="form-control inform" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hold</label>
                            <div class="col-md-12">
                                <select name="choldMtype" class="form-control chold inform" style="text-transform:uppercase;" >
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
                <button type="button" id="btnSaveMtype" onclick="saveMtype()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<script type="application/javascript" src="<?= base_url('assets/pagejs/capital/masters/grouping.js') ?>"></script>
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