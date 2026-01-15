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

<?php /*
<div class="row">
	<div class="col-sm-12">
		<a href="<?= base_url('master/item/input') ?>" class="btn btn-primary" style="margin:10px"><i class="fa fa-plus"></i>  Input Master Item  </a>
	</div>
</div> */ ?>
<div class="row">
	<div class="col-sm-12">
		<div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?= base_url('master/data/input_suppliers') ?>"><i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <!-- <a class="dropdown-item disabled" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a> -->
                        <a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
			<div class="card-body table-responsive m-t-40" style='overflow-x:scroll;' cellspacing="0" width="100%">
				<table id="tsuppliers" class="table table-bordered table-striped" >
					<thead class="text-center">
						<tr>
							<th style="min-width:30px; text-align:center; vertical-align:middle;">No.</th>
                            <th style="min-width:10px; text-align:center; vertical-align:middle;">Action</th>
                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Kode Supplier</th>
                            <th style="min-width:200px; text-align:center; vertical-align:middle;">Nama Supplier</th>
                            <th style="min-width:250px; text-align:center; vertical-align:middle;">Alamat</th>
                            <th style="min-width:80px; text-align:center; vertical-align:middle;">Telepon</th>
                            <th style="min-width:80px; text-align:center; vertical-align:middle;">Dibuat oleh</th>
                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Dibuat tanggal</th>
                            <th style="min-width:80px; text-align:center; vertical-align:middle;">Diubah oleh</th>
                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Diubah tanggal</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
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
                                        <option value="I">DRAFT USER</option>
                                        <option value="C">CLOSE</option>
                                        <option value="O">OPEN</option>
                                        <option value="R">BATAL</option>
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


<script type="application/javascript" src="<?= base_url('assets/pagejs/master/suppliers.js') ?>"></script>
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
    });

</script>