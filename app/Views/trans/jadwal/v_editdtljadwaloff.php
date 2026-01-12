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
				//$('#kdregu').prop('disabled',true);
				//$('#bln1').prop('disabled',true);
				//$('#thn1').prop('disabled',true);
            });
		
</script>
<legend><?php echo $title;?></legend>
<?php// echo $message;?>

<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					 <form action="<?php echo site_url('trans/jadwal_new/simpan_offjadwal')?>" method="post">						
								
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
												<input type="text" value="<?php echo trim($dtl['kdregu']);?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly></input>
												<!--select class="form-control input-sm" id="kdregu" name="kdregu" required readonly >
													<!--option value="">--Pilih Nama Regu--</option-->
													<!--?php foreach ($list_regu as $ld){ ?>
													<option value="<!--?php echo trim($dtl['kdregu']);?>"><!--?php echo trim($ld->kdregu).'||'.$ld->nmregu;?></option>
													<!--?php } ?>																																					
												</select-->
									  </div>
									</div>
									<div class="form-group input-sm ">		
										<label class="label-form col-sm-3">Bulan</label>
										<div class="col-sm-5">
										
										<input type="text" value="<?php echo $bln;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly></input>
											<!--select class="form-control input-sm" id="bln1" name='bln' required readonly >
												
												<option value="01" <?php $tgl=$bln; if($tgl=='01') echo "selected"; ?>>Januari</option>
												<option value="02" <?php $tgl=$bln; if($tgl=='02') echo "selected"; ?>>Februari</option>
												<option value="03" <?php $tgl=$bln; if($tgl=='03') echo "selected"; ?>>Maret</option>
												<option value="04" <?php $tgl=$bln; if($tgl=='04') echo "selected"; ?>>April</option>
												<option value="05" <?php $tgl=$bln; if($tgl=='05') echo "selected"; ?>>Mei</option>
												<option value="06" <?php $tgl=$bln; if($tgl=='06') echo "selected"; ?>>Juni</option>
												<option value="07" <?php $tgl=$bln; if($tgl=='07') echo "selected"; ?>>Juli</option>
												<option value="08" <?php $tgl=$bln; if($tgl=='08') echo "selected"; ?>>Agustus</option>
												<option value="09" <?php $tgl=$bln; if($tgl=='09') echo "selected"; ?>>September</option>
												<option value="10" <?php $tgl=$bln; if($tgl=='10') echo "selected"; ?>>Oktober</option>
												<option value="11" <?php $tgl=$bln; if($tgl=='11') echo "selected"; ?>>November</option>
												<option value="12" <?php $tgl=$bln; if($tgl=='12') echo "selected"; ?>>Desember</option>
											</select-->
										</div>			
									</div>
																
									<script type="text/javascript" charset="utf-8">
								
										$(function() {
					
														//$('#bln1').on('change', function(){
															
															var idbulan=$('#bln1').val();
															var idtahun=$('#thn1').val();
															var blnthn=(idtahun+'-'+idbulan);
															
															//var date = new Date();
															//var date = new Date("02 21, 2017");
															var date = new Date(idbulan+' 01, '+idtahun);
															var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
															var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
															var endDayofMonth = (lastDay.getDate());				
															var lastDayWithSlashes = (lastDay.getDate()) + '/' + (lastDay.getMonth() + 1) + '/' + lastDay.getFullYear();

															
															//for ()
																//if(idbulan=='02'){
																	if(endDayofMonth>=28){$('#tgl28').show();}
																		else{ $('#tgl28').hide();
																		$("[name='tgl28']").val("");
																		}								
																	if(endDayofMonth>=29){ $('#tgl29').show();}
																		else{ $('#tgl29').hide();
																		$("[name='tgl29']").val("");
																		}	
																	if(endDayofMonth>=30){ $('#tgl30').show();}
																		else{ $('#tgl30').hide();
																		$("[name='tgl30']").val("");
																		}	
																	if(endDayofMonth>=31){ $('#tgl31').show();}
																		else{ $('#tgl31').hide();
																		$("[name='tgl31']").val("");
																	}	
															
														
														//});
													});
																										
										</script>
									<div class="form-group input-sm ">		
										<label class="label-form col-sm-3">Tahun</label>
										<div class="col-sm-5">
										<input type="text" value="<?php echo $thn;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly></input>
											<!--select class="form-control input-sm" id="thn1" name="thn" required readonly >
												<option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
																
											</select-->
										</div>			
									</div>		
								  </div>
		
								
								<div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 1</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl1" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<!--option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option-->
												<option <?php if (trim($tgl1['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 2</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl2" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<!--option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option-->
												<option <?php if (trim($tgl2['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 3</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl3" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl3['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 4</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl4" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl4['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 5</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm"  name="tgl5" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl5['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 6</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl6" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl6['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 7</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl7" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl7['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 8</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl8" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl8['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 9</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl9" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl9['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 10</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm"  name="tgl10" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl10['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 11</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl11" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl11['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 12</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl12" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl12['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 13</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl13" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl13['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 14</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl14" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl14['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 15</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl15" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl15['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								  
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 16</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl16" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl16['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 17</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl17" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl17['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 18</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl18" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl18['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 19</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl19" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl19['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 20</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl20" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl20['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 21</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl21" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl21['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 22</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl22" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl22['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 23</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl23" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl23['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 24</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl24" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl24['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 25</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl25" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl25['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 26</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl26" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl26['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm">
								  <label class="label-form col-sm-3">Tgl 27</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl27" required>
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl27['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm tgl28" id="tgl28">
								  <label class="label-form col-sm-3">Tgl 28</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl28" >
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl28['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm tgl29" id="tgl29">
								  <label class="label-form col-sm-3">Tgl 29</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl29" >
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl29['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div><div class="form-group input-sm tgl30" id="tgl30">
								  <label class="label-form col-sm-3">Tgl 30</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl30" >
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl30['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?></option>
												<?php } ?>																																					
											</select>
								  </div>
								</div>
								<div class="form-group input-sm tgl31" id="tgl31">
								  <label class="label-form col-sm-3">Tgl 31</label>
								  <div class="col-sm-5">
											<select class="form-control input-sm" name="tgl31" >
												<option value="OFF">OFF</option>
												<?php foreach ($list_jamkerja as $ld){ ?>
												<option <?php if (trim($tgl31['kodejamkerja'])==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>" ><?php echo trim($ld->kdjam_kerja).' || '.$ld->nmjam_kerja;?> </option>
												<?php } ?>																																					
											</select>
								  </div>
								</div>  
								<div class="col-sm-3">
									<button type="submit" class="btn btn-warning">SIMPAN</button>
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
	//$('#kdregu').selectize();
	$('#kdregu2').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");		
	$("#disb").chained("#city");	

</script>