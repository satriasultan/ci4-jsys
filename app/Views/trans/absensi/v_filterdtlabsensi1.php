<legend><?php echo $title;?></legend>

				<div class="row">
				<?php if($akses['aksesview']=='t') { ?>
                    <div class="col-xs-6">
						<div class="box">
							<div class="box-header">
								<div class="col-xs-12">
									<h4>Filter Laporan Absensi Karyawan</h4>
								</div>
							</div>
                            <div class="box-body">
								<div class="form-horizontal">
									<form action="<?php echo site_url('trans/absensi/detailabsensi');?>" name="form" role="form" method="post">										
										<!--area-->
										<div class="form-group">
											 <label class="col-lg-3">Nama Karyawan</label>
											<div class="col-lg-9">
												<select id="pilihkaryawan" name="karyawan" required>
												<option value="">--Pilih Karyawan--</option>
												<?php foreach ($list_karyawan as $ld){ ?>
												<option value="<?php echo trim($ld->nik);?>"><?php echo $ld->nmlengkap;?></option>
												<?php } ?>																																					
											</select>
											</div>
										</div>
										<div class="form-group">
											 <label class="col-lg-3">Tanggal</label>
											<div class="col-lg-9">
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" id="tgl" name="tgl"   class="form-control pull-right">
												</div><!-- /.input group -->
											</div>
										</div>
										<div class="form-group">
											 <label class="col-lg-3">Keterangan</label>
											<div class="col-lg-6">
												<select id="joss" name="ketsts" class="form-control input-group" >
												<option value="">--ALL--</option>
												<option value="TIDAK MASUK KERJA">TIDAK MASUK KERJA</option>
												<option value="TERLAMBAT">TERLAMBAT</option>																																				
											</select>
											</div>
										</div>
										
										<div class="form-group"> 
											<div class="col-lg-4">
												<button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
											   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				
				
				
                    <div class="col-xs-6">
						<div class="box">
							<div class="box-header">
								<div class="col-xs-12">
									<h4>Filter Laporan Absensi Regu</h4>
								</div>
							</div>
                            <div class="box-body">
								<div class="form-horizontal">
									<form action="<?php echo site_url('trans/absensi/detailabsensi_regu');?>" name="form" role="form" method="post">										
										<!--area-->
										<div class="form-group">
											 <label class="col-lg-3">Pilih Regu</label>
											<div class="col-lg-9">
												<select id="pilihregu" name="kdregu" required>
												<option value="">--Pilih Bagian--</option>
												<?php foreach ($list_regu as $ld){ ?>
												<option value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
												<?php } ?>																																					
											</select>
											</div>
										</div>
										<div class="form-group">
											 <label class="col-lg-3">Tanggal</label>
											<div class="col-lg-9">
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" id="tgl2" name="tgl"   class="form-control pull-right">
												</div><!-- /.input group -->
											</div>
										</div>
										<div class="form-group">
											 <label class="col-lg-3">Keterangan</label>
											<div class="col-lg-6">
												<select id="joss" name="ketsts" class="form-control input-group">
													<option value="">--ALL--</option>
													<option value="TIDAK MASUK KERJA">TIDAK MASUK KERJA</option>
													<option value="TERLAMBAT">TERLAMBAT</option>																																				
												</select>
											</div>
										</div>
										<div class="form-group"> 
											<div class="col-lg-4">
												<button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
											   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					
					<div class="col-xs-6">
						<div class="box">
							<div class="box-header">
								<div class="col-xs-12">
									<h4>Filter Laporan Absensi Bagian</h4>
								</div>
							</div>
                            <div class="box-body">
								<div class="form-horizontal">
									<form action="<?php echo site_url('trans/absensi/detailabsensi_dept');?>" name="form" role="form" method="post">										
										<!--area-->
										<div class="form-group">
											 <label class="col-lg-3">Pilih Bagian</label>
											<div class="col-lg-9">
												<select id="pilihdept" name="kddept" required>
												<option value="">--Pilih Bagian--</option>
												<?php foreach ($list_dept as $ld){ ?>
												<option value="<?php echo trim($ld->kddept);?>"><?php echo $ld->nmdept;?></option>
												<?php } ?>																																					
											</select>
											</div>
										</div>
										<div class="form-group">
											 <label class="col-lg-3">Tanggal</label>
											<div class="col-lg-9">
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" id="tgl3" name="tgl"   class="form-control pull-right">
												</div><!-- /.input group -->
											</div>
										</div>
										<div class="form-group">
											 <label class="col-lg-3">Keterangan</label>
											<div class="col-lg-6">
												<select id="joss" name="ketsts" class="form-control input-group">
													<option value="">--ALL--</option>
													<option value="TIDAK MASUK KERJA">TIDAK MASUK KERJA</option>
													<option value="TERLAMBAT">TERLAMBAT</option>																																				
												</select>
											</div>
										</div>
										<div class="form-group"> 
											<div class="col-lg-4">
												<button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
											   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				
		<?php } ?>	
		<!-------------------------- BATAS-------------->
		<?php if($akses['aksesdownload']=='t') { ?>
				<div class="col-xs-6">
						<div class="box">
							<div class="box-header">
								<div class="col-xs-12">
									<h4>DOWNLOAD XLS</h4>
								</div>
							</div>
                            <div class="box-body">
								<div class="form-horizontal">
									<form action="<?php echo site_url('trans/absensi/report_absensi');?>" name="form" role="form" method="post">										
										<!--area-->
										<div class="form-group">
											<label class="label-form col-sm-3">Bulan</label>
											<div class="col-sm-9">
												<select class="form-control input-sm" name='bln' required>
													
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
										<div class="form-group">
											<label class="label-form col-sm-3">Tahun</label>
											<div class="col-sm-9">
												<select class="form-control input-sm" name="thn" required>
													<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
													<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
													<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>					
												</select>
											</div>	
										</div>
										<!--div class="form-group">
											 <label class="col-lg-3">Keterangan</label>
											<div class="col-lg-6">
												<select id="joss" name="ketsts" class="form-control input-group">
													<option value="">--ALL--</option>
													<option value="TIDAK MASUK KERJA">TIDAK MASUK KERJA</option>
													<option value="TERLAMBAT">TERLAMBAT</option>																																				
												</select>
											</div>
										</div-->
										<div class="form-group"> 
											<div class="col-lg-4">
												<button type='submit' class='btn btn-success' ><i class="glyphicon glyphicon-search"></i> Download</button>
											   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
			</div>
		<?php } ?>


<script>

  

	
	//Date range picker
    $('#tgl').daterangepicker();
    $('#tgl2').daterangepicker();
    $('#tgl3').daterangepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihdept').selectize();
	$('#pilihregu').selectize();
  

</script>