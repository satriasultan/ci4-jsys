<?php
/*
	@author : Fiky
*/
?>

<script type="text/javascript">
            $(function() {
                $("#table1").dataTable();
                $("#example2").dataTable();
                $("#example3").dataTable();
				$("#dateinput").datepicker();
				$("#dateinput1").datepicker();
				$("#dateinput2").datepicker();
				$("#dateinput3").datepicker();
				$("[data-mask]").inputmask();
            });

</script>
<legend><?php echo $title;?></legend>
<?php// echo $message;?>

<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">
					 <form action="<?php echo site_url('trans/jadwal_new/proses_inputjadwal_sama')?>" method="post">

							  </div>
							<div>&nbsp </div>
								  <div class="row">
								  <div class="form-body">
									  <!--<div class="form-group">
									  <label class="control-label col-md-3">Shift Tipe</label>
									  <div class="col-md-9">
												<select class="form-control input-sm" id="shift" name="shift" required>
													<option value="t">SHIFT</option>
													<option value="f">NON SHIFT</option>
												</select>
									  </div>
									</div>-->
									<div class="form-group input-sm">
									  <label class="control-label col-md-3">Kode Regu Terinput</label>
									  <div class="col-md-5">
												<select class="form-control input-sm" id="kdregu" name="kdregulama" required>
													<option value="">--Pilih Nama Regu--</option>
													<?php foreach ($list_regu as $ld){ ?>
													<option value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
													<?php } ?>
												</select>
									  </div>
									</div>
									<div class="form-group input-sm">
									  <label class="control-label col-md-3">Kode Regu Yang Akan Di input</label>
									  <div class="col-md-5">
												<select class="form-control input-sm" id="kdregu2" name="kdregubaru" required>
													<option value="">--Pilih Nama Regu--</option>
													<?php foreach ($list_regu as $ld){ ?>
													<option value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
													<?php } ?>
												</select>
									  </div>
									</div>
									<div class="form-group input-sm ">
										<label class="label-form col-sm-3">Bulan</label>
										<div class="col-sm-5">
											<select class="form-control input-sm" id="bln1" name='bln' required>

												<option value="01" <?php $tgl=date('m'); if($tgl=='01') echo "selected"; ?>>Januari</option>
												<option value="02" <?php $tgl=date('m'); if($tgl=='02') echo "selected"; ?>>Februari</option>
												<option value="03" <?php $tgl=date('m'); if($tgl=='03') echo "selected"; ?>>Maret</option>
												<option value="04" <?php $tgl=date('m'); if($tgl=='04') echo "selected"; ?>>April</option>
												<option value="05" <?php $tgl=date('m'); if($tgl=='05') echo "selected"; ?>>Mei</option>
												<option value="06" <?php $tgl=date('m'); if($tgl=='06') echo "selected"; ?>>Juni</option>
												<option value="07" <?php $tgl=date('m'); if($tgl=='07') echo "selected"; ?>>Juli</option>
												<option value="08" <?php $tgl=date('m'); if($tgl=='08') echo "selected"; ?>>Agustus</option>
												<option value="09" <?php $tgl=date('m'); if($tgl=='09') echo "selected"; ?>>September</option>
												<option value="10" <?php $tgl=date('m'); if($tgl=='10') echo "selected"; ?>>Oktober</option>
												<option value="11" <?php $tgl=date('m'); if($tgl=='11') echo "selected"; ?>>November</option>
												<option value="12" <?php $tgl=date('m'); if($tgl=='12') echo "selected"; ?>>Desember</option>
											</select>
										</div>
									</div>
									<div class="form-group input-sm ">
										<label class="label-form col-sm-3">Tahun</label>
										<div class="col-sm-5">
											<select class="form-control input-sm" id="thn1" name="thn" required>
												<option value='<?php $tgl=date('Y')+1; echo $tgl; ?>'><?php $tgl=date('Y')+1; echo $tgl; ?></option>
												<option selected value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
												<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
												<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>
											</select>
										</div>
									</div>
								  </div>



								<div class="col-sm-3">
									<button type="submit" class="btn btn-warning">NEXT STEP</button>
									<a href="<?php echo site_url("trans/jadwal_new/index")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
								</div>




							</form>

			</div><!-- /.box-header -->

		</div><!-- /.box -->
	</div>
</div>



 <script>

	//Date range picker
    $('#tgl').datepicker();
    $('#tgl2').datepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihkaryawan2').selectize();
	//$('#thn1').selectize();
	//$('#bln1').selectize();
	$('#kdregu').selectize();
	$('#kdregu2').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");
	$("#disb").chained("#city");

</script>
