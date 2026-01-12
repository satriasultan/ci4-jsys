<!-- Bootstrap modal -->
 <?php// foreach ($list_jadwal as $la) { ?>
 <form action="<?php echo base_url('trans/jadwal_new/proses_edit_detail')?>" method="post">

    <div class="modal-content">
      <div class="modal-header">
        <!--button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button-->

		<h3 class="modal-title">Edit Jadwal Kerja</h3>
		<h5 class="modal-title">Edit jadwal ini harus melalui persetujuan bagian personalia, silahkan info personalia setelah melakukan perubahan </h5>
      </div>
      <div class="modal-body form">
          <div class="row">
		  <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">REGU</label>
              <div class="col-md-9">
						<input name="kdregu" value="<?php echo $dtl_jadwal['kdregu'];?>"  class="form-control"  type="text" readonly>
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">NIK</label>
              <div class="col-md-9">
						<input name="nik" value="<?php echo $dtl_jadwal['nik'];?>"  class="form-control"  type="text" readonly>
              </div>
            </div>

			<div class="form-group">
              <label class="control-label col-md-3">Tanggal Kerja</label>
              <div class="col-md-9">
                <input name="tanggal" value="<?php echo $dtl_tgl;?>" id="tgl" data-date-format="dd-mm-yyyy" class="form-control"  type="text" readonly>
                <!--input name="id" value="<?php echo $dtl_jadwal['id'];?>"  class="form-control"  type="hidden"-->
              </div>
            </div>
			 <div class="form-group">
              <label class="control-label col-md-3">Kode Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="kdjamkerja1" name="kdjamkerja" required>
							<option value="OFF">OFF</option>
							<?php foreach ($list_jamkerja as $ld){ ?>
							<option <?php if (trim($dtl_jadwal['kdjamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.trim($ld->nmjam_kerja);?></option>
							<?php } ?>
						</select>
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="nmjamkerja1" name="nmjamkerja" readonly>
							<?php foreach ($list_jamkerja as $ld){ ?>
							<option class="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->jam_masuk.'-'.$ld->jam_pulang;?></option>
							<?php } ?>
						</select>
              </div>
            </div>
          </div>
          </div>
          </div>

          <div class="modal-footer">
			<div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?php echo base_url('trans/jadwal_new');?>"  class="btn btn-danger" >Cancel</a>
			</div>
          </div>
        </div><!-- /.modal-content -->

  <!-- End Bootstrap modal -->
	</form>

  <!-- Bootstrap modal -->
  <?php// } ?>

   <script>




	//Date range picker
   //$('#tgl').datepicker();
	$('#pilihkaryawan').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");

</script>
