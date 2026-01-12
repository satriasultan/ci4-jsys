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
<?php echo $message;?>

<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					 <form action="<?php echo site_url('trans/jadwal_new/simpan_oprdtljadwal')?>" method="post">						
								
							  </div>
							<div>&nbsp </div>
								  <div class="row">
								  <div class="form-body">
									<div class="form-group input-sm">
										 <label class="control-label col-md-3">NIK</label>
										<div class="col-md-5">
											<select class="form-control input-sm" name="nik" id="pilihkaryawan" required>
												  <option value="">--PILIH KARYAWAN--</option-->
												  <?php foreach($list_nik as $listkan){?>
												  <option value="<?php echo trim($listkan->nik);?>" ><?php echo $listkan->nmlengkap;?></option>	
												  <?php }?>
											</select>
										</div>
									</div>
									<script type="text/javascript" charset="utf-8">
										  $(function() {	
											$("#kdregu1").chained("#pilihkaryawan");		
											
										  });
									</script>
									<div class="form-group input-sm">
									  <label class="control-label col-md-3">Kode Regu Karyawan</label>
									  <div class="col-md-5">
												<select class="form-control input-sm" id="kdregu1" name="kdregu" required readonly>
													<?php foreach ($list_nik as $lr){ ?>
														<option value="<?php echo trim($lr->kdregu);?>" class="<?php echo trim($lr->nik);?>"><?php echo trim($lr->kdregu);?></option>
													<?php };?>																																				
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
												<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
												<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
												<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>					
											</select>
										</div>			
									</div>		
								  </div>
								  
								
								<div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 1</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl1" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 2</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl2" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 3</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl3" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 4</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl4" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 5</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm"  name="tgl5" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 6</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl6" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 7</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl7" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 8</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl8" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 9</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl9" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 10</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm"  name="tgl10" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 11</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl11" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 12</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl12" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 13</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl13" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 14</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl14" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 15</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl15" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 16</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl16" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 17</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl17" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 18</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl18" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 19</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl19" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 20</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl20" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 21</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl21" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 22</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl22" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 23</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl23" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 24</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl24" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 25</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl25" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 26</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl26" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 27</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl27" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 28</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl28" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 29</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl29" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 30</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl30" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div>
								<div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 31</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl31" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div>  
								<div class="col-sm-3">
									<button type="submit" class="btn btn-warning">SIMPAN</button>
									<a href="<?php echo site_url("trans/jadwal_new")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
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
	//$('#pilihkaryawan').selectize();
	//$('#pilihkaryawan2').selectize();
	//$('#thn1').selectize();
	//$('#bln1').selectize();
	$('#kdregu').selectize();
	$('#kdregu2').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");		
	$("#disb").chained("#city");	

</script>