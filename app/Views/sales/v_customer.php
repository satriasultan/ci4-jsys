


<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1>
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

<?php echo $message;?>


<div class="row mt-2">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-sm-12">
						<div class="btn-group">
							<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="<?= base_url('sales/master/input_customer') ?>" ><i class="fa fa-plus"></i><?php echo '    Input Customer Data'; ?></a>
								<a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
								<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter"  href="#" ><i class="fa fa-filter"></i><?php echo '    Filter'; ?></a>
							</div>
						</div>
						<!-- <a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px"><i class="fa fa-plus"></i>  Input Master Customer  </a> -->
					</div>
				</div>
			</div><!-- /.card-header -->
			<div class="card-body table-responsive" style='overflow-x:scroll;'>
				<table id="tcust" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th width="1%">No.</th>
                            <th width="8%">Action</th>
							<th>Customer ID</th>
							<th>Customer Name</th>
                            <th>Kode Customer (From Sales)</th>
							<th>Country</th>
							<th>Customer Address</th>
							<th>Contact</th>
							<th>Email</th>
							<th>Telepon</th>							
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>





<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/customer.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

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