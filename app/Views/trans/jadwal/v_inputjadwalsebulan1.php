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
					 <form action="<?php echo site_url('trans/jadwal_new/input_jadwalsebulan')?>" method="post">						
								
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
									  <label class="control-label col-md-3">Kode Regu Karyawan</label>
									  <div class="col-md-5">
												<select class="form-control input-sm" id="kdregu" name="kdregu" required>
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
									<button type="submit" class="btn btn-warning">NEXT STEP</button>
									<a href="<?php echo site_url("trans/jadwal_new/index")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
									</div>
									
								 
									
							
							</form>
					
			</div><!-- /.box-header -->
		
		</div><!-- /.box -->								
	</div>
</div>

 <!-- Bootstrap modal -->
 <form action="<?php echo site_url('trans/jadwal_new/input_jadwal')?>" method="post">
  <div class="modal fade" id="input" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
		<h3 class="modal-title">Input Jadwal Kerja</h3>
      </div>
      <div class="modal-body form">
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
			<div class="form-group">
              <label class="control-label col-md-3">Nama Regu</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="kdregu" name="kdregu" required>
							<option value="">--Pilih Nama Regu--</option>
							<?php foreach ($list_regu as $ld){ ?>
							<option value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
							<?php } ?>																																					
						</select>
              </div>
            </div>
		 
			<div class="form-group">
              <label class="control-label col-md-3">Tanggal Jadwal Kerja</label>
              <div class="col-md-9">
                <input name="tanggal" id="tgl" data-date-format="dd-mm-yyyy" class="form-control"  type="text">
              </div>
            </div>
			 <div class="form-group">
              <label class="control-label col-md-3">Jadwal Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="kdjamkerja1" name="kdjamkerja" required>
							<option value="">--Pilih Jam Kerja--</option>
							<?php foreach ($list_jamkerja as $ld){ ?>
							<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
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
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
			</div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->
	</form>
  
  <!-- Bootstrap modal -->
  

  
  
  
  <!-- Bootstrap modal -->
 
 <!--Modal untuk Filter-->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filter Jadwal Per Bulan</h4>
      </div>
	  <form action="<?php echo site_url('trans/jadwal_new/index')?>" method="post">
      <div class="modal-body">
        <div class="form-group input-sm ">		
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
		<div class="form-group input-sm ">		
			<label class="label-form col-sm-3">Tahun</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" name="thn" required>
					<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
					<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
					<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>					
				</select>
			</div>			
		</div>
		<div class="form-group input-sm ">		
			<label class="label-form col-sm-3">KARYAWAN</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" id="pilihkaryawan2" name="nik">
							<option value="">--PILIH KARYAWAN--</option>
							<?php foreach ($list_karyawan as $ld){ ?>
							<option value="<?php echo trim($ld->nik);?>"><?php echo trim($ld->nik).'||'.$ld->nmlengkap;?></option>
							<?php } ?>																																					
						</select>
			</div>			
		</div>
		<div class="form-group input-sm ">		
			<label class="label-form col-sm-3">REGU</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" id="kdregu2" name="kdregu">
							<option value="">--Pilih Nama Regu--</option>
							<?php foreach ($list_regu as $ld){ ?>
							<option value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
							<?php } ?>																																					
						</select>
			</div>			
		</div>
      </div>
      <div class="modal-footer">
		  <div class="col-sm-12">
			<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
			<button type="submit1" class="btn btn-primary">Filter</button>
		  </div>
		</div>
	  </form>
    </div>
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