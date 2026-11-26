


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
								<a class="dropdown-item" href="#" onclick="add_tax()" ><i class="fa fa-plus"></i><?php echo '    Input Pajak Data'; ?></a>
								<a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
								<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter"  href="#" ><i class="fa fa-filter"></i><?php echo '    Filter'; ?></a>
							</div>
						</div>
						<!-- <a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px"><i class="fa fa-plus"></i>  Input Master Customer  </a> -->
					</div>
				</div>
			</div><!-- /.card-header -->
			<div class="card-body table-responsive" style='overflow-x:scroll;'>
				<table id="ttax" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th width="1%">No.</th>
                            <th width="8%">Action</th>
							<!-- <th>Pajak ID</th> -->
							<th>Kode Pajak </th>
                            <th>Nama Pajak </th>
							<th>Jenis Pajak</th>
                            <th>%</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>


<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input Master Pajak</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="typeform"/>
                    <input type="hidden" name="id"/>
                    <div class="form-body">
                        <input type="hidden" name="idtax" value="">
                        <div class="form-group">
                            <label class="control-label col-md-12">Kode Pajak</label>
                            <div class="col-md-12">
                                <input name="kodetax" placeholder="Kode Pajak" class="form-control inform" type="text" maxlength="10" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12">Jenis Pajak</label>
                            <div class="col-md-12">
                                <select name="jnstax" id="jnstax" class="form-control">
                                    <option value=""> Pilih Jenis Pajak </option>
                                    <option value="NON">NON</option>
                                    <option value="PPH">PPH</option>
                                    <option value="PPN">PPN</option>
                                </select>
                                <!-- <input name="jnstax" placeholder="Jenis Pajak" class="form-control inform" maxlength="20" type="text" > -->
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12">Nama Pajak</label>
                            <div class="col-md-12">
                                <input name="nmtax" placeholder="Nama Pajak" class="form-control inform" type="text" maxlength="30" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12">Persen (%)</label>
                            <div class="col-md-12">
                                <input name="persen" placeholder="Persen" class="form-control inform ratakanan jtsseparator" type="text" maxlength="5" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-12">Keterangan</label>
                            <div class="col-md-12">
                                <textarea name="description" placeholder="Keterangan" class="form-control inform" style="text-transform:uppercase;"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="submitTax()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/sales/pajak.js') ?>"></script>
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