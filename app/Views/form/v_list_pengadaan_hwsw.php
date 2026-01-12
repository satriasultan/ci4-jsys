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
                        <a class="dropdown-item" href="<?= base_url('form/pa/input_pengadaan_hwsw') ?>"><i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a>
                        <a class="dropdown-item" href="#"  onclick="reload_table()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
			<div class="card-body table-responsive" style='overflow-x:scroll;'  cellspacing="0" width="100%">
				<table id="treq" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th width="1%">No.</th>
                            <th style="width:1%; text-align:center; vertical-align:middle;">Action</th>
							<th style="width:10%; text-align:center; vertical-align:middle;">No. Dokumen</th>
							<th style="width:7%; text-align:center; vertical-align:middle;">Status</th>
							<th style="width:15%; text-align:center; vertical-align:middle;">Nama PIC</th>
							<th style="width:5%; text-align:center; vertical-align:middle;">Departemen</th>
                            <th style="width:8%; text-align:center; vertical-align:middle;">Nama Barang</th>
                            <th style="width:7%; text-align:center; vertical-align:middle;">Jenis Barang</th>
							<th style="width:30%; text-align:center; vertical-align:middle;">Keperluan</th>
							<th style="width:8%; text-align:center; vertical-align:middle;">Spesifikasi</th>
							<th style="width:10%; text-align:center; vertical-align:middle;">Inputdate</th>

						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>


<!-- Modal Detail Permintaan -->
<div class="modal fade" id="modalDetailReq" tabindex="-1" aria-labelledby="modalDetailReqLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailReqLabel">Detail Permintaan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loader -->
                <div id="detailLoader" class="text-center my-3 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Mengambil data...</p>
                </div>

                <!-- Isi detail -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Nama/PIC <span style="color:red">*</span></label>
                            <input type="text" name="namapic" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Departemen <span style="color:red">*</span></label>
                            <input type="text" name="departemen" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Nama Barang <span style="color:red">*</span></label>
                            <input type="text" name="nmbarang" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Jumlah Barang <span style="color:red">*</span></label>
                            <input type="text" name="jumlah" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Estimasi Harga/Unit</label>
                            <input type="text" name="estimasi" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Jenis Barang <span style="color:red">*</span></label>
                            <input type="text" name="jnsbarang" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label>Tujuan Pengadaan <span style="color:red">*</span></label>
                            <textarea name="tujuan" class="form-control" rows="3" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Frekuensi Pemakaian <span style="color:red">*</span></label>
                            <input type="text" name="frekuensi" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Jenis Pengadaan <span style="color:red">*</span></label>
                            <input type="text" name="jnspengadaan" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Klasifikasi</label>
                            <input type="text" name="klasifikasi" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label>Spesifikasi Jenis Barang <span style="color:red">*</span></label>
                            <textarea name="spesifikasi" class="form-control" rows="3" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>Lampiran</label>
                            <input type="text" name="lampiran" class="form-control" disabled>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Ketentuan Akses Internet -->
<!-- <div class="modal fade" id="ketentuanAksesModal" tabindex="-1" aria-labelledby="ketentuanAksesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3">
        <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="ketentuanAksesLabel">Ketentuan Akses Internet</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="line-height: 1.6; font-size: 14px;">
            <ol>
            <li>PIC Akun menyetujui dan mematuhi kebijakan keamanan informasi, kebijakan keamanan sistem / aplikasi, dan prosedur terkait.</li>
            <li>PIC Akun dilarang mengalihkan dan/atau meminjamkan hak akses kepada orang lain.</li>
            <li>Akun hanya boleh digunakan di Perangkat Perusahaan yang terdaftar atas seizin Atasan PIC Akun dan Manager IT.</li>
            <li>PIC Akun dilarang menyalahgunakan akses untuk kepentingan selain penugasan yang telah ditetapkan perusahaan.</li>
            <li>PIC Akun harus memberitahukan kepada atasan dan IT apabila ada perubahan hak akses (Resign, Mutasi, dll).</li>
            <li>Pelanggaran terhadap ketentuan akses dan prosedur terkait akan menyebabkan pencabutan hak akses, tindakan disiplin, atau sanksi sesuai peraturan yang berlaku.</li>
            </ol>
            <p class="mt-3" ><em style="color: red;">"Saya menyetujui dan bersedia mematuhi ketentuan ini, saya akan menggunakan hak akses sesuai dengan tugas dan pekerjaan yang diberikan oleh perusahaan dan saya akan melaporkan setiap masalah atau insiden keamanan informasi yang saya ketahui kepada atasan."</em></p>
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
                    <div class="form-group">
                        <label for="suppliername">Category Item</label>
                        <select name="idgroup" id="idgroup" class="form-control" placeholder="Pilih Category Item">
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


<script type="application/javascript" src="<?= base_url('assets/pagejs/form/request.js') ?>"></script>
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