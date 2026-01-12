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
                        <a class="dropdown-item" href="<?= base_url('form/pa/input_perangkatkeluar') ?>"><i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a>
                        <a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
			<div class="card-body table-responsive" style='overflow-x:scroll;' cellspacing="0" width="100%">
				<table id="tkeluar" class="table table-bordered table-striped" >
					<thead class="text-center">
						<tr>
                            <th style="min-width:30px; text-align:center; vertical-align:middle;">No.</th>
                            <th style="min-width:10px; text-align:center; vertical-align:middle;">Action</th>
                            <th style="min-width:100px; text-align:center; vertical-align:middle;">No. Dokumen</th>
                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Status</th>
                            <th style="min-width:190px; text-align:center; vertical-align:middle;">Nama Pemohon</th>
                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Departemen</th>
                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Jabatan</th>
                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Jenis Form</th>
                            <th style="min-width:80px; text-align:center; vertical-align:middle;">Tanggal Dokumen</th>
                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Jenis Perangkat</th>
                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Periode</th>
                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Periode Mulai</th>
                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Periode Akhir</th>

                            <th style="min-width:300px; text-align:center; vertical-align:middle;">Keperluan</th>
                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Tujuan (tempat)</th>
                            <th style="min-width:80px; text-align:center; vertical-align:middle;">Input By</th>
                            <th style="min-width:60px; text-align:center; vertical-align:middle;">Input Date</th>
                            <th style="min-width:80px; text-align:center; vertical-align:middle;">Print By</th>
                            <th style="min-width:60px; text-align:center; vertical-align:middle;">Print Date</th>
                            <th style="min-width:60px; text-align:center; vertical-align:middle;">Print Count</th>


						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>

<!-- <div class="modal fade" id="ketentuanModal" tabindex="-1" role="dialog" aria-labelledby="ketentuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="ketentuanModalLabel">Ketentuan Perangkat IT Keluar Perusahaan</h5>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" style="line-height: 1.6; font-size: 14px;">
            <ol>
            <li>Seluruh perangkat IT yang digunakan oleh karwayan harus mempunyai sticker identitas asset dari perusahaan karena sebagai point pemeriksaan security saat perangkat akan dibawa masuk / keluar perusahaan. Jika perangkat IT yang digunakan tidak memiliki Sticker id asset perusahaan maka pengguna perangkat segera menghubungi Departemen IT / IRGA agar segera mendapat sticker identitas asset untuk perangkat tersebut.</li>
            <li>Jika ditemukan ada unsur kesengajaan pengguna untuk tidak menempelkan sticker identitas asset pada perangkat IT yang digunakan, maka penggna dapat dikenai sanksi sesuai dengan peraturan yang berlaku.</li>
            <li>Penyalahgunaan perangkat diluar dari penugasan perusahaan akan menyebabkan pencabutan izin, tindakan disiplin atau sanksi sesuai peraturan yang berlaku.</li>
            </ol>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-bs-dismiss="modal">Saya Mengerti</button>
        </div>
        </div>
    </div>
</div> -->


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
                    <!-- <div class="form-group">
                        <label for="suppliername">Category Item</label>
                        <select name="idgroup" id="idgroup" class="form-control" placeholder="Pilih Category Item">
                        </select>
                    </div> -->
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


<script type="application/javascript" src="<?= base_url('assets/pagejs/form/perangkatkeluar.js') ?>"></script>
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