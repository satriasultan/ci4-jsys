<?php
/**
 * *
 *  * Created by PhpStorm.
 *  *  * User: FIKY-PC
 *  *  * Date: 4/29/19 1:34 PM
 *  *  * Last Modified: 12/18/16 10:51 AM.
 *  *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  *  Copyright© 2019 .All rights reserved.
 *  *
 *
 */

?>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#dateinput").datepicker();
    });
</script>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
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
<?php echo $message; ?>
<div class="card">
    <div class="card-header">
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
            </button>
            <div class="dropdown-menu">
                <!-- <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter"  href="#" disabled="true"><i class="fa fa-filter"></i><?php echo '    Filter'; ?></a> -->
                <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                <a class="dropdown-item" href="#"  onclick="add_mlocation()"><i class="fa fa-plus" ></i><?php echo '     Input'; ?> </a>
                <?php endif;?>
                <a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
            </div>
        </div>
    </div><!-- /.card-header -->
    <div class="card-body table-responsive" style='overflow-x:scroll;'>
        <table id="tmlocation" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th width="1%" >No.</th>
                <th width="8%">Action</th>
                <th>Warehouse</th>
                <th>ID Area</th>
                <th>Area Name</th>
                <th>Barcode ID</th>
                <th>Hold</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.card-body -->
</div><!-- /.card -->

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input Data Lokasi</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="">Dasar Lokasi</label>
                                    <select name="idlocation" id="idlocation" class="form-control" style="text-transform:uppercase;" >
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="">ID Area</label>
                                    <input name="idarea" placeholder="ID Area" class="form-control inform" type="text" MAXLENGTH="30" style="text-transform:uppercase;" required>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="">Nama Area</label>
                                    <input name="nmarea" placeholder="Nama Area" class="form-control inform" type="text" style="text-transform:uppercase;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="">Hold</label>
                                    <select name="chold" class="form-control chold inform" style="text-transform:uppercase;" >
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="NO"> NO </option>
                                        <option value="YES">YES </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!--Modal untuk Filter-->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">FILTER TANGGAL DOKUMEN</h4>
            </div>
            <form id="form-filter" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">TANGGAL DOKUMEN</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control input-sm tglrange" id="tglrange" name="tglrange" value="" data-date-format="dd-mm-yyyy" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
                    <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/javascript" src="<?= base_url('assets/pagejs/master/mlocation_area.js') ?>"></script>
<script type="application/javascript">
    $(".tglrange").daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    });

    $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>






