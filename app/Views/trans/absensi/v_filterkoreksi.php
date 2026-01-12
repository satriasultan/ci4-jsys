<legend><?php echo $title;?></legend>
<?php echo $message; ?>
<?php if($akses['aksesupdate']=='t') { ?>
				<div class="row">
                    <div class="col-xs-6">
	
					<div class="box">
							<div class="box-header">
								<div class="col-xs-12">
									<h4>Filter Laporan Absensi</h4>
								</div>
							</div>
						
                            <div class="box-body">
								<div class="form-horizontal">
								
									<form action="<?php echo site_url('trans/absensi/koreksiabsensi');?>" name="form" role="form" method="post">										
										<!--area-->
										<div class="form-group">
											 <label class="col-lg-3">Nama Karyawan</label>
											<div class="col-lg-9">
												<select id="pilihkaryawan" name="karyawan" required>
												<option value="">--Pilih Karyawan--</option>
												<?php foreach ($list_karyawan as $ld){ ?>
												<option value="<?php echo trim($ld->nik);?>"><?php echo $ld->nik.'| '.$ld->nmlengkap;?></option>
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
													<input type="text" id="tgl" name="tgl"  class="form-control pull-right">
												</div><!-- /.input group -->
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
									<form action="<?php echo site_url('trans/absensi/koreksiabsensi_dept');?>" name="form" role="form" method="post">										
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
				</div>
<?php } else echo 'anda tidak diperkenankan mengakeses modul ini !!!!';?>		
				</div>
		
	

<script>

  

	
	//Date range picker
    $('#tgl').daterangepicker();
    $('#tgl3').daterangepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihdept').selectize();
  

</script>