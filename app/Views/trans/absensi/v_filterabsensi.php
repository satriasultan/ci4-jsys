<legend><?php echo $title;?></legend>
<?php echo $message;?>
				<div class="row">
                    <div class="col-xs-6">
						<div class="box">
							<div class="box-header">
								
							</div>
                            <div class="box-body">
								<div class="form-horizontal">
									<form action="<?php echo site_url('trans/absensi/index');?>" name="form" role="form" method="post">										
										<!--area-->
										<div class="form-group">
											 <label class="col-lg-3">Tanggal Tarikan Terakhir</label>
											<div class="col-lg-9">
												<div class="input-group">
													<input type="input" id="tglakhir" name="tglakhir"  class="form-control pull-right" readonly>
												</div><!-- /.input group -->
											</div>
										</div>	
										<div class="form-group">
											 <label class="col-lg-3">Pilih Wilayah</label>
											<div class="col-lg-9">
												<select id="kdcabang" name="kdcabang" required>
												<option value="">--Pilih Wilayah--</option>
												<?php foreach ($list_kanwil as $ld){ ?>
												<option value="<?php echo trim($ld->kdcabang);?>"><?php echo $ld->kdcabang.' || '.$ld->desc_cabang;?></option>
												<?php } ?>																																					
											</select>
											</div>
										</div>
										
										
							<script type="text/javascript" charset="utf-8">
							$(function() {																				
										$('#kdcabang').change(function(){
												
												var cabang=$(this).val();
										
												  $.ajax({
													url : "<?php echo site_url('trans/absensi/ajax_tglakhir_ci')?>/" + $(this).val(),
													type: "GET",
													dataType: "JSON",
													success: function(data)
													{
													   $('[name="tglakhir"]').val(data.lastdate);                        
														//alert('cok');
													},
													error: function (jqXHR, textStatus, errorThrown)
													{
														alert('Error get data from ajax');
													}
												});
				
											
											});
											
										});	

	</script>

										
										<?php if($akses['aksesconvert']=='t'){?>
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
											<?php } else { echo 'anda tidak diperkenankan mengakses modul ini!!!!';} ?> 
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
		
	

<script>

  

	
	//Date range picker
    $('#tgl').daterangepicker();
    $('#kdcabang').selectize();

  

</script>