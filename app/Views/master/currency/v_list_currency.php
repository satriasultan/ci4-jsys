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
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
                    </button>
                    <div class="dropdown-menu">
                        <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                            <a class="dropdown-item" href="<?= base_url('master/data/input_currency') ?>">
                                <i class="fa fa-plus"></i> Input
                            </a>
                        <?php endif; ?>
                        <!-- <a class="dropdown-item disabled" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a> -->
                        <a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
                <a class="btn btn-warning btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#modal_exchange_setting"
                    title="Pengaturan pengambilan exchange rate otomatis">
                    <i class="fa fa-cog"></i> Pengaturan Kurs Otomatis
                </a>
                <span class="badge bg-secondary" id="exchange_mode_badge" style="margin-left:5px;vertical-align:middle;color:#000 !important;font-weight:600;">MANUAL</span>
            </div><!-- /.card-header -->
			<div class="card-body table-responsive m-t-40" style='overflow-x:scroll;' cellspacing="0" width="100%">
				<table id="tcurrency" class="table table-bordered table-striped" >
					<thead class="text-center">
						<tr>
							<th style="max-width:30px; text-align:center; vertical-align:middle;">No.</th>
                            <th style="max-width:10px; text-align:center; vertical-align:middle;">Action</th>
                            <th style="max-width:100px; text-align:center; vertical-align:middle;">Kode Currency</th>
                            <th style="max-width:200px; text-align:center; vertical-align:middle;">Nama Currency</th>
                            <!-- <th style="max-width:250px; text-align:center; vertical-align:middle;">Alamat</th>
                            <th style="max-width:80px; text-align:center; vertical-align:middle;">Telepon</th> -->
                            <th style="max-width:80px; text-align:center; vertical-align:middle;">Dibuat oleh</th>
                            <th style="max-width:100px; text-align:center; vertical-align:middle;">Dibuat tanggal</th>
                            <th style="max-width:80px; text-align:center; vertical-align:middle;">Diubah oleh</th>
                            <th style="max-width:100px; text-align:center; vertical-align:middle;">Diubah tanggal</th>
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

<!-- Modal Pengaturan Exchange Rate Otomatis -->
<div class="modal fade" id="modal_exchange_setting" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-cog"></i> Pengaturan Exchange Rate Otomatis</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_exchange_setting">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mode Pengambilan Kurs</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="exr_mode_auto">
                                    <label class="form-check-label" for="exr_mode_auto" id="exr_mode_label">MANUAL</label>
                                </div>
                                <small class="text-muted">
                                    OTOMATIS : kurs diambil dari API dan disimpan ke tabel exchange rate.
                                    MANUAL : kurs hanya diinput manual oleh user.
                                </small>
                                <input type="hidden" id="exr_mode" name="mode" value="manual">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Base Currency (mata uang tujuan)</label>
                                <input type="text" class="form-control" id="exr_base_currency" name="base_currency"
                                    value="IDR" maxlength="5" placeholder="IDR">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>API URL Template</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="exr_api_url" name="api_url"
                                value="https://api.frankfurter.dev/v2/rate/{FROM}/{TO}"
                                placeholder="https://api.frankfurter.dev/v2/rate/{FROM}/{TO}">
                            <button type="button" class="btn btn-outline-secondary" id="exr_btn_preset"
                                title="Gunakan template Frankfurter">Frankfurter</button>
                        </div>
                        <small class="text-muted">
                            Gunakan placeholder <code>{FROM}</code> untuk kode currency asal dan <code>{TO}</code> untuk base currency.
                            Contoh : <code>https://api.frankfurter.dev/v2/rate/{FROM}/{TO}</code>.
                            URL statis seperti <code>.../rate/USD/IDR</code> akan otomatis diubah menjadi template.
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Mapping Kode Currency (opsional)</label>
                        <textarea class="form-control" id="exr_code_alias" name="code_alias" rows="2"
                            placeholder="YEN=JPY"></textarea>
                        <small class="text-muted">
                            Format <code>KODE_DB=KODE_API</code> dipisahkan koma atau baris baru.
                            Default : <code>YEN=JPY</code> (API Frankfurter memakai JPY).
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Test API</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="exr_test_code" value="USD" maxlength="5"
                                placeholder="USD">
                            <button type="button" class="btn btn-info" id="exr_btn_test">
                                <i class="fa fa-flask"></i> Test API
                            </button>
                            <button type="button" class="btn btn-success" id="exr_btn_sync">
                                <i class="fa fa-refresh"></i> Sync Semua Currency
                            </button>
                        </div>
                        <div class="mt-2" id="exr_test_result"></div>
                    </div>
                    <div id="exr_sync_result" class="mt-2"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="exr_btn_save">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.modal -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/master/currency/currency.js') ?>"></script>
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