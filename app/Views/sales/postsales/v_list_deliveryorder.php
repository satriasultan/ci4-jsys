<style>
    .badge-cetak{
        background-color: #b73fff;
    }
    /* Tab status Delivery Order (Semua / Draft / Final User / ...) */
    #doStatusTab .nav-link {
        font-size: 13px;
        padding: 6px 14px;
    }
    #doStatusTab .nav-link.active {
        font-weight: 600;
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
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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
<?php echo $showUnfinish; ?>
<div class="row">
	<div class="col-sm-12">
		<div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
                    </button>
                    <div class="dropdown-menu">
                        <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                            <a class="dropdown-item" href="<?= base_url('sales/postsales/addDeliveryOrder') ?>"
                            onclick="return guardPeriodeTutup(event)">
                            <i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <?php endif; ?>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter" href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a>
                        <a class="dropdown-item" href="#"  onclick="reload_tableDeliveryOrderTrx()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
            <ul class="nav nav-tabs" id="doStatusTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active" data-status="ALL" role="tab">Delivery Order</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-status="D" role="tab">Daftar Draft Delivery Order</button>
                </li>
               <!-- <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-status="F" role="tab">Final User</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-status="A" role="tab">Approved</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-status="P" role="tab">Cetak/Print</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-status="C" role="tab">Cancel</button>
                </li>-->
            </ul>

			<div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive"  style='overflow-x:auto;'>
                            <table id="tabledeliveryorderTrx" class="table table-bordered table-striped"  style="width:100%;" cellspacing="0">
                                <thead class="text-center">
                                    <tr>
                                        <th style="min-width:10px; text-align:center; vertical-align:middle;">No.</th>
                                        <th style="min-width:10px; text-align:center; vertical-align:middle;">Action</th>
                                        <th style="min-width:100px; text-align:center; vertical-align:middle;">Docno</th>
                                        <th style="min-width:100px; text-align:center; vertical-align:middle;">Tanggal</th>
                                        <th style="min-width:50px; text-align:center; vertical-align:middle;">Kode Customer</th>
                                        <th style="min-width:150px; text-align:center; vertical-align:middle;">Nama Customer</th>
                                        <th style="min-width:150px; text-align:center; vertical-align:middle;">Alamat Customer</th>
                                        <th style="min-width:100px; text-align:center; vertical-align:middle;">Kota Customer</th>
                                        <th style="min-width:150px; text-align:center; vertical-align:middle;">Deliver To</th>
                                        <th style="min-width:150px; text-align:center; vertical-align:middle;">Alamat Kirim</th>
                                        <th style="min-width:100px; text-align:center; vertical-align:middle;">Kota Kirim</th>
                                        <!-- <th style="min-width:100px; text-align:center; vertical-align:middle;">Tanggal Kirim</th> -->
                                        <th style="min-width:100px; text-align:center; vertical-align:middle;">Salesman</th>
                                        <!-- <th style="min-width:100px; text-align:center; vertical-align:middle;">No. PO Customer</th> -->
                                        <th style="min-width:200px; text-align:center; vertical-align:middle;">Keterangan</th>
                                        <th style="min-width:150px; text-align:center; vertical-align:middle;">Cost Center</th>
                                        <th style="min-width:80px; text-align:center; vertical-align:middle;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Item</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6" style="margin-bottom: 25px;">
                            <div class="form-group input-sm">
                                <label class="label-form col-sm-12">No. DO</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm" id="docno_filter" name="docno_filter" value="" style="text-transform:uppercase;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-bottom: 25px;">
                            <div class="form-group input-sm">
                                <label class="label-form col-sm-12">Customer (kode / nama)</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm" id="namacustomer" name="namacustomer" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-bottom: 25px;">
                            <div class="form-group input-sm ">
                                <label class="label-form col-sm-12">Tanggal Dokumen</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control input-sm tglrange" id="tglrange" name="tglrange" value="" data-date-format="dd-mm-yyyy" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group input-sm ">
                                <label class="label-form col-sm-12">Status</label>
                                <div class="col-sm-12">
                                    <select class="form-control input-sm" id="status_filter" name="status_filter">
                                        <option value="ALL">Semua Status</option>
                                        <option value="D">DRAFT</option>
                                        <option value="F">FINAL USER</option>
                                        <option value="A">APPROVED</option>
                                        <option value="P">CETAK/PRINT</option>
                                        <!-- <option value="PO">DITARIK PO</option> -->
                                        <option value="C">CANCELED</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 25px;">
                            <div class="form-group input-sm">
                                <label class="label-form col-sm-12">Item (kode / nama barang)</label>
                                <div class="col-sm-12">
                                    <select class="form-control input-sm" id="idbarang_filter" name="idbarang_filter" style="width:100%;">
                                    </select>
                                </div>
                            </div>
                        </div>
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


<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/postsales/deliveryorder/deliveryorder.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
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

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-M-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /* ===== Tab status Delivery Order =====
           Tab hanya mengubah nilai #status_filter lalu me-reload DataTable,
           jadi tidak perlu endpoint/filter baru di server. */
        function doSyncStatusTab() {
            var val = String($('#status_filter').val() || 'ALL').toUpperCase();
            var $tabs = $('#doStatusTab .nav-link');
            $tabs.removeClass('active');
            var $aktif = $tabs.filter(function () {
                return String($(this).data('status')).toUpperCase() === val;
            });
            if ($aktif.length === 0) {
                $aktif = $tabs.filter('[data-status="ALL"]');
            }
            $aktif.addClass('active');
        }

        $('#doStatusTab').on('click', '.nav-link', function () {
            $('#status_filter').val(String($(this).data('status') || 'ALL').toUpperCase());
            doSyncStatusTab();
            reload_tableDeliveryOrderTrx();
        });

        // Filter modal / reset dari deliveryorder.js tetap sinkron dengan tab.
        $('#btn-filter, #btn-reset').on('click', function () {
            doSyncStatusTab();
        });

        doSyncStatusTab();
    });

</script>