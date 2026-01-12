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
                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter"><i class="fa fa-filter"></i><?php echo '    Filter'; ?></a>
                <a class="dropdown-item" href="#"  onclick="reloadTlistLbm()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
            </div>
        </div>
    </div><!-- /.card-header -->
    <div class="card-body table-responsive" style='overflow-x:scroll;'>
        <table id="tlistlbm_wacc" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>No.</th>
                <th><div class="text-wrap width-90"> Dokumen </div></th>
                <th><div class="text-wrap width-90"> PO Number</div></th>
                <th><div class="text-wrap width-90"> Date </div></th>
                <th><div class="text-wrap width-90"> ID Brg </div></th>
                <th><div class="text-wrap width-90"> Qty In(LNS) </div></th>
                <th><div class="text-wrap width-90"> Nama Barang </div></th>
                <th><div class="text-wrap width-90"> Unit </div></th>
                <th><div class="text-wrap width-150"> Description </div></th>
                <th><div class="text-wrap width-150"> Supplier </div></th>
                <th><div class="text-wrap width-90"> TglPakai </th></th>
                <th><div class="text-wrap width-90"> UNIQUEIDPD</div></th>
                <th><div class="text-wrap width-90"> Status </div></th>
                <th>Inputby</th>
                <th>Inputdate</th>
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
                <h4 class="modal-title" id="myModalLabel">Input</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">PO Number</label>
                            <div class="col-md-12">
                                <input name="docno" placeholder="Po Number" class="form-control inform" type="text" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">ID Barang</label>
                            <div class="col-md-12">
                                <input name="idbarang" placeholder="ID Barang" class="form-control inform" type="text" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nama Barang</label>
                            <div class="col-md-12">
                                <input name="nmbarang" placeholder="Nama Barang" class="form-control inform" type="text" >
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Qty PO</label>
                            <div class="col-md-12">
                                <input name="onhandpo" placeholder="Qty PO" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Qty Kedatangan</label>
                            <div class="col-md-12">
                                <input name="onhandtranspo" placeholder="Qty Kedatangan" class="form-control inform ratakanan" type="text" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Unit</label>
                            <div class="col-md-12">
                                <input name="unit" placeholder="Unit" class="form-control inform" type="text" style="text-transform:uppercase;" readonly>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Description</label>
                            <div class="col-md-12">
                                <textarea name="description" placeholder="Deskripsi" class="form-control inform" type="text" style="text-transform:uppercase;"></textarea>
                                <span class="help-block"></span>
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
<div class="modal fade" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggalpo">Tanggal LPB</label>
                        <input type="text" class="form-control tglrange" id="tglrange"  name="tglrange" data-date-format="dd-mm-yyyy" required placeholder="Tanggal Terima Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="itembarang">Item Barang</label>
                        <select name="idbarang_filter" id="idbarang_filter" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suppliername">Nama Supplier</label>
                        <select name="namasupplier" id="namasupplier" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_filter">Status PO</label>
                        <select name="status_filter" id="status_filter" class="form-control" placeholder="Pilih Status Filter">
                            <option value=""> Semua Status</option>
                        </select>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
                    <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>

                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/bbm.js') ?>"></script>
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






