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

<div class="row">
	<div class="col-sm-4">
		<div class="card">
            <div class="card-header">
            </div><!-- /.card-header -->
			<div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="periode">Periode</label>
                            <input type="text"
                                name="periode"
                                id="periode"
                                class="form-control"
                                value="<?= $periode ?>"
                                disabled>
                                <?php if ($btnLabel == 'Open Period'): ?>
                                    <small style="color:red; font-style:italic;">
                                        Periode ini telah ditutup...!!!
                                    </small>
                                <?php endif; ?>
                        </div>
                        <button type="submit"
                                onclick="closePeriod(this)"
                                data-label="<?= $btnLabel ?>"
                                class="btn btn-primary btn-lg float-right">
                            <i class="fa fa-save"></i> <?= $btnLabel ?>
                        </button>
                    </div>
                </div>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Daftar Block/Unblock Periode</h3>
			</div><!-- /.card-header -->
			<div class="card-body table-responsive">
				<table id="tbl_bup" class="table table-bordered table-striped" style="width:100%;">
					<thead class="text-center">
						<tr>
							<th style="width:50px; text-align:center; vertical-align:middle;">No.</th>
							<th style="min-width:100px; text-align:center; vertical-align:middle;">Periode</th>
							<th style="min-width:100px; text-align:center; vertical-align:middle;">Status</th>
							<th style="min-width:200px; text-align:center; vertical-align:middle;">Keterangan</th>
							<th style="min-width:120px; text-align:center; vertical-align:middle;">Input By</th>
							<th style="min-width:150px; text-align:center; vertical-align:middle;">Input Date</th>
							<th style="min-width:120px; text-align:center; vertical-align:middle;">Update By</th>
							<th style="min-width:150px; text-align:center; vertical-align:middle;">Update Date</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; foreach ($list as $row): ?>
							<?php
								$flag = strtoupper(trim($row['flagproses'] ?? ''));
								$badge = ($flag === 'TUTUP') ? 'badge-danger' : (($flag === 'OPEN') ? 'badge-success' : 'badge-secondary');
							?>
							<tr>
								<td class="text-center"><?= $no++ ?></td>
								<td class="text-center"><?= htmlspecialchars(trim($row['periode'] ?? '')) ?></td>
								<td class="text-center"><span class="badge <?= $badge ?>"><?= $flag ?></span></td>
								<td><?= htmlspecialchars(trim($row['keterangan'] ?? '')) ?></td>
								<td class="text-center"><?= htmlspecialchars(trim($row['inputby'] ?? '')) ?></td>
								<td class="text-center"><?= !empty($row['inputdate']) ? date('d-m-Y H:i', strtotime($row['inputdate'])) : '' ?></td>
								<td class="text-center"><?= htmlspecialchars(trim($row['updateby'] ?? '')) ?></td>
								<td class="text-center"><?= !empty($row['updatedate']) ? date('d-m-Y H:i', strtotime($row['updatedate'])) : '' ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>





<script type="application/javascript" src="<?= base_url('assets/pagejs/tools/bup.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#tbl_bup").dataTable({
            "order": [[ 1, "desc" ]],
            "pageLength": 25
        });
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        $('#periode').datepicker({
            format: "yymm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

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