<?php 
/*
	@author : Fiky
*/
?>

<legend><?php echo $title;?></legend>
<?php echo $message;?>

<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">					
		<li class="active"><a href="#tab_1" data-bs-toggle="tab"><b>DATA KARYAWAN AKTIF</b></a></li>
	</ul>
</div>



<div class="tab-content">
	<div class="chart tab-pane active" id="tab_1" style="position: relative; height: 300px;" >

<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Negara</a>-->
					<!--a href="<?php echo site_url("trans/karyawan/excel_listkaryawan")?>"  class="btn btn-default" style="margin:10px;">Export Excel</a--->
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>No.</th>
							<th>NIK</th>
							<th>Nama Lengkap</th>																								
							<th>Department</th>																								
							<th>Jabatan</th>																								
							<th>Tgl Masuk</th>
							<th>Kantor</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_self as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>						
							<td><?php echo trim($lu->nik);?></td>
							<td><?php echo $lu->nmlengkap;?></td>
							<td><?php echo $lu->nmdept;?></td>
							<td><?php echo $lu->nmjabatan;?></td>
							<td><?php echo $lu->tglmasukkerja;?></td>
							<td><?php echo $lu->kdcabang;?></td>
							<td>
								<a href="<?php echo site_url("trans/karyawan/detail_self/".trim($lu->nik)."");?>" onclick="return confirm('Profile Karyawan <?php echo 'NIK:  ',$lu->nik,'  Nama:   ',$lu->nmlengkap;?> ?')" class="btn btn-success">Detail Profile</a>
	
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>


 <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Form Kode Trxtype</h3>
      </div>
      <div class="modal-body form">
        <form action="<?php echo site_url('trans/karyawan/ajax_add'); ?>" method='post' id="form"> 
		<div class="form-horizontal">
			<div class="stepwizard ">
				<div class="stepwizard-row setup-panel">				
					<div class="stepwizard-step col-sm-1">
						<a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
						<p>Step 1</p>
					</div>
					<div class="stepwizard-step col-sm-1">
						<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
						<p>Step 2</p>
					</div>
						<div class="stepwizard-step col-sm-1">
						<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
						<p>Step 3</p>
					</div>				
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
					<p>Step 4</p>
				  </div>
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
					<p>Step 5</p>
				  </div>
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-6" type="button" class="btn btn-default btn-circle" disabled="disabled">6</a>
					<p>Step 6</p>
				  </div>
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-7" type="button" class="btn btn-default btn-circle"  disabled="disabled">7</a>
					<p>Step 7</p>
				  </div>
				   <div class="stepwizard-step col-sm-1">
					<a href="#step-8" type="button" class="btn btn-default btn-circle"  disabled="disabled">8</a>
					<p>Step 8</p>
				  </div>
				</div>
			</div>
			<div class="row setup-content " id="step-1">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 1</h3>
					<div class="row">
						<div class="col-sm-6 ">
							<div class="box box-primary" >
								<div class="box-header">									
								</div>
								<div class="box-body">
									<!--div class="form-group">
									  <label class="control-label col-sm-3">NIK Karyawan</label>
									  <div class="col-sm-9">
										<input name="nik" id="nik" style="text-transform:uppercase;" maxlength="12" placeholder="Nomor Induk Otomatis" class="form-control" type="text" disabled >
									  </div>
									</div-->
									<div class="form-group">
								  <label class="control-label col-sm-3">Pilih Dari Daftar KTP Pelamar</label>
									<div class="col-sm-9">
										<select  name="dfktp" id="dfktp" style="text-transform:uppercase;"  class="form-control" type="text" disabled >
											<option value="f">TIDAK</option>
											<option value="t">YA</option>
										</select>
									</div>
								</div>	
							<script type="text/javascript" charset="utf-8">
							$(function() {
											
											$('.ktp1').show();
											$('.ktp2').hide();
											$('.tglpel').hide();
											$('#noktp2').removeAttr('required');
											//$('#tgllam').removeAttr('required');
											//$('#tgllow').removeAttr('required');
											$('#dfktp').change(function(){
												
												var dfktp=$(this).val();
						
													if(dfktp=='t'){
														$('.ktp2').show();
														$('.tglpel').show();
														$('.ktp1').hide();
														$('#noktp2').prop('required',true);
														//$('#tgllow').prop('required',true);
														//$('#tgllam').prop('required',true);
														$('#noktp1').removeAttr('required');
														$('.plktp').prop('readOnly',true);
														//$('#nmlengkap').val('test');
													} else if(dfktp=='f'){
														$('.ktp2').hide();
														$('.tglpel').hide();
														$('.ktp1').show();
														$('#noktp1').prop('required',true);
													
														$('#noktp2').removeAttr('required');
															$('.plktp').prop('readOnly',false);
															
														//$('#tgllam').removeAttr('required');
														//$('#tgllow').removeAttr('required');
														document.getElementById("form").reset();
														
														
													}
											
											});
										
										
										$('#noktp2').change(function(){
												
												var ktp2=$(this).val();
										/*		var url = "<?php echo site_url('trans/karyawan/ajax_req_recruitment');?>/"+$(this).val();
														$('#kotakab').load(url);
												var nmlengkap=$('#nmktprec').val();
												$('#nmlengkap').val(nmlengkap); */
													      //Ajax Load data from ajax
												  $.ajax({
													url : "<?php echo site_url('trans/karyawan/ajax_req_recruitment')?>/" + $(this).val(),
													type: "GET",
													dataType: "JSON",
													success: function(data)
													{
													   
														$('[name="nmlengkap"]').val(data.nmlengkap);                        
														$('[name="jk"]').val(data.jk);                        
														$('[name="neglahir"]').val(data.neglahir);                        
														$('[name="tgllahir"]').val(data.tgllahir);                        
														                        
														//$('[name="kotalahir"]').val(data.kotalahir);                        
														//$('[name="provlahir"]').val(data.provtinggal);                        
														//$('#provinsi').val(data.provtinggal);                        
														//$('[name="kotalahir"]').val(data.kotatinggal);                        
													                       
														$('[name="kd_agama"]').val(data.kd_agama); 
														$('[name="alamattinggal"]').val(data.alamattinggal); 
														$('[name="nohp1"]').val(data.nohp1); 
														$('[name="nohp2"]').val(data.nohp2); 
														$('[name="email"]').val(data.email); 
														
														
														$('[name="status_pernikahan"]').val(data.status_pernikahan);
														                     
														
													},
													error: function (jqXHR, textStatus, errorThrown)
													{
														alert('Error get data from ajax');
													}
												});
												
												 $.ajax({
													url : "<?php echo site_url('trans/karyawan/ajax_cekktpkembar')?>/" + $(this).val(),
													type: "GET",
													dataType: "JSON",
													success: function(data)
													{
														if (data>=1){
															alert('PERINGATAN NO KTP : '+ktp2+ ' SUDAH PERNAH TERDAFTAR SEBAGAI KARYAWAN SILAHKAN CEK KEMBALI !!! (Click OK untuk Melanjutkan)'); 
														}

													},
													error: function (jqXHR, textStatus, errorThrown)
													{
														alert('Error get data from ajax');
													}
												});
												
											
											});
											
										});	

	</script>
								<div class="form-group ktp1">
								  <label class="control-label col-sm-3">No KTP</label>
								  <div class="col-sm-9">
									<input name="noktp" id="noktp1" style="text-transform:uppercase;" placeholder="No Ktp" class="form-control" type="text" maxlength="18" required>
								  </div>
								</div>
								<div class="form-group ktp2">
								  <label class="control-label col-sm-3">Pilih KTP Dari Pelamar</label>
									<div class="col-sm-9">
										<select  name="noktp" id="noktp2" style="text-transform:uppercase;"  class="form-control" type="text" required>
											<?php foreach ($calon_karyawan as $lon){ ?>
												<option value="<?php echo trim($lon->noktp);?>"><?php echo trim($lon->noktp).' || '.trim($lon->nmlengkap).' || '.trim($lon->kdposisi);?></option>
												
											<?php };?>
										</select>
									</div>
								</div>	

									
									<div class="form-group">
									  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
									  <div class="col-sm-9">
										<input name="nmlengkap" style="text-transform:uppercase;" placeholder="Nama Lengkap" class="form-control plktp" id="nmlengkap" type="text" required>
									  </div>
									</div>
									<div class="form-group">
									  <label class="control-label col-sm-3">Nama Panggilan</label>
									  <div class="col-sm-9">
										<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
									  </div>
									</div>
									<div class="form-group">
									  <label class="control-label col-sm-3">Jenis Kelamin</label>
									  <div class="col-sm-9">
										<select  name="jk" style="text-transform:uppercase;"  class="form-control plktp" type="text" required>
											<option value="L">LAKI-LAKI</option>
											<option value="P">PEREMPUAN</option>
										</select>
									  </div>
									</div>

								</div>
							</div>
						</div>					
						<div class="col-sm-6 ">
							<div class="box box-primary" >
								<div class="box-header">									
								</div>
								<div class="box-body">
									<div class="form-group">
										<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>	
										<div class="col-sm-8">    
											<select type="text" name="neglahir" id='negara' class="form-control col-sm-12" required>										
												<?php foreach ($list_opt_neg as $lon){ ?>
												<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
												<?php };?>
											</select>
										</div>
									</div>
									<script type="text/javascript" charset="utf-8">
										$(document).ready(function(){
													$("#provinsi").change(function (){
														var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
														$('#kotakab').load(url);
														return false;
													})									
												});
									  $(function() {	
										$("#provinsi").chained("#negara");								
									  });
									</script>
									<div class="form-group">
										<label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>	
										<div class="col-sm-8">    
											<select type="text" name="provlahir" id='provinsi' class="form-control col-sm-12"  required="required">
												<option value="">-KOSONG-</option>
												<?php foreach ($list_opt_prov as $lop){ ?>
												<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
												<?php };?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>	
										<div class="col-sm-8">    
											<select type="text" name="kotalahir" id='kotakab' class="form-control col-sm-12" required="required">
												<option value="">-KOSONG-</option>
											</select>
										</div>
									</div>
									<div class="form-group">
									  <label class="control-label col-sm-3">Tanggal Lahir</label>
									  <div class="col-sm-9">
										<input name="tgllahir"   class="form-control plktp" id="tgl" placeholder="Tanggal Lahir" data-date-format="dd-mm-yyyy" type="text">
									  </div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Agama</label>	
										<div class="col-sm-8">    
											<select  name="kd_agama" class="form-control col-sm-12 plktp">										
												<?php foreach ($list_opt_agama as $loa){ ?>
												<option value="<?php echo trim($loa->kdagama);?>" ><?php echo trim($loa->nmagama);?></option>																																																			
												<?php };?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>									
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-2">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 2</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">Keadaan Fisik</label>
					  <div class="col-sm-9">
						<select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							<option value="t">BAIK & SEHAT</option>
							<option value="f">CACAT FISIK</option>
						</select>
					  </div>
					</div>
					<div  class="form-group"  >
					  <div id="f" class="fisiks" style="display:none">
						  <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
						  <div class="col-sm-9">
							<textarea name="ketfisik" style="text-transform:uppercase;" placeholder="Deskripsikan Cacat fisik" class="form-control" ></textarea>
						  </div>
					  </div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Kantor Wilayah</label>
						<div class="col-sm-9">
							<select name="kdcabang" id='kdcabang' class="form-control col-sm-12" required>
								<?php foreach ($list_kanwil as $lf){ ?>
									<option value="<?php echo trim($lf->kdcabang);?>" ><?php echo trim($lf->desc_cabang);?></option>
								<?php };?>
							</select>

						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Branch ID</label>
						<div class="col-sm-9">
							<input name="branch" value="SBYNSA" class="form-control" type="input" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Tanggal Masuk</label>
						<div class="col-sm-9">
							<input name="tglmasukkerja" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglmasuk" data-date-format="dd-mm-yyyy" class="form-control" type="text" required="required">
						</div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				  
				</div>
			  </div>
			</div>			
			<div class="row setup-content" id="step-3">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 3</h3>
					<!--div class="form-group"> pindah depan untuk lookup karyawan
					  <label class="control-label col-sm-3">No KTP</label>
					  <div class="col-sm-9">
						<input name="noktp" style="text-transform:uppercase;" placeholder="No Ktp" class="form-control" type="text" maxlength="18" required>
					  </div>
					</div-->
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP Dikeluaran di</label>
					  <div class="col-sm-9">
						<input name="ktpdikeluarkan" style="text-transform:uppercase;" placeholder="Kota KTP di keluarkan" class="form-control" type="text" maxlength="20">
					  </div>
					</div>							
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal KTP Dikeluaran</label>
					  <div class="col-sm-9">
						<input name="tgldikeluarkan" style="text-transform:uppercase;" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" id="tgl2" type="text" >
					  </div>
					</div>
					<script type="text/javascript" charset="utf-8">
										$(function() {
						//ktpseumurhidup
						$('#ktps').change(function(){
							$('.ktps').show();
							$('#ktpz' + $(this).val()).hide();
							$('#modal_form').modal('show'); // show bootstrap modal
						});
					});	
					</script>
					
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP seumur hidup</label>
					  <div class="col-sm-9">								
						<select id="ktps" name="ktp_seumurhdp" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							<option value="f">TIDAK</option>
							<option value="t">IYA</option>
						</select>
					  </div>
					</div>
					<div class="form-group">							  
						<div id="ktpzt" class="ktps">
						  <label class="control-label col-sm-3">Tanggal Berlaku</label>
						  <div class="col-sm-9">							
								<input type="text" name="ktpberlaku" data-date-format="dd-mm-yyyy" id="tglktp" class="form-control" >							
						  </div>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Kewarganegaraan</label>
					  <div class="col-sm-9">								
						<select  name="stswn" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" required="required">
							<option value="t">Warga Negara Indonesia</option>
							<option value="f">Warga Negara Asing</option>
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Status Pernikahan</label>
					  <div class="col-sm-9">								
						<select name="status_pernikahan" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" required>
							<?php foreach ($list_opt_nikah as $lonikah){ ?>
							<option value="<?php echo trim($lonikah->kdnikah);?>" ><?php echo trim($lonikah->nmnikah);?></option>																																																			
							<?php };?>									
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Golongan Darah</label>
					  <div class="col-sm-9">								
						<select name="gol_darah" style="text-transform:uppercase;"  class="form-control" type="text" required="required">
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="AB">AB</option>
							<option value="O">O</option>
						</select>
					  </div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-4">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
					<h3> Step 4</h3>
						<div class="row">
							<div class="col-sm-6 ">
								<div class="box box-primary" >
									<div class="box-header">
										<h4>Sesuai Ktp</h4>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label class="control-label col-sm-3">NEGARA</label>	
											<div class="col-sm-8">    
												<select name="negktp" id='almnegara' class="form-control col-sm-12">										
													<?php foreach ($list_opt_neg as $lon){ ?>
													<option value="">--Pilih Negara---</option>																																																			
													<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<script type="text/javascript" charset="utf-8">
										 $(document).ready(function(){
												$("#almprovinsi").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
													$('#almkotakab').load(url);
													return false;
												})
												
												$("#almkotakab").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kec');?>/"+$(this).val();
													$('#almkec').load(url);
													return false;
												})
												
												$("#almkec").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_des');?>/"+$(this).val();
													$('#almkeldesa').load(url);
													return false;
												})
											});
										  $(function() {	
											$("#almprovinsi").chained("#almnegara");										
										  });
										</script>
										<div class="form-group">
											<label class="control-label col-sm-3">Provinsi</label>	
											<div class="col-sm-8">    
												<select name="provktp" id='almprovinsi' class="form-control col-sm-12">
													<option value="">-KOSONG-</option>
													<?php foreach ($list_opt_prov as $lop){ ?>
													<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kota/Kabupaten</label>	
											<div class="col-sm-8">    
												<select name="kotaktp" id='almkotakab' class="form-control col-sm-12" >										
													<option value="">-Pilih provinsi Dahulu-</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kecamatan</label>	
											<div class="col-sm-8">   												
												<select name="kecktp" id='almkec' class="form-control col-sm-12" >										
													<option value="">-Pilih Kota/Kabupaten Dahulu-</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kelurahan/Desa</label>	
											<div class="col-sm-8">    
												<select name="kelktp" id='almkeldesa' class="form-control col-sm-12" >
													<option value="">-Pilih Kecamatan Dahulu-</option>
												</select>
											</div>
										</div>
										<div  class="form-group"  >							  
										  <label class="control-label col-sm-3">Alamat</label>
										  <div class="col-sm-9">
											<textarea name="alamatktp" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ></textarea>
										  </div>							  
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="box box-primary" >
									<div class="box-header">
										<h4>Sesuai Tempat Tinggal</h4>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label class="control-label col-sm-3">NEGARA</label>	
											<div class="col-sm-8">    
												<select name="negtinggal" id='almsnegara' class="form-control col-sm-12">										
													<?php foreach ($list_opt_neg as $lon){ ?>
													<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<script type="text/javascript" charset="utf-8">
											$(document).ready(function(){
												$("#almsprovinsi").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
													$('#almskotakab').load(url);
													return false;
												})
												
												$("#almskotakab").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kec');?>/"+$(this).val();
													$('#almskec').load(url);
													return false;
												})
												
												$("#almskec").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_des');?>/"+$(this).val();
													$('#almskeldesa').load(url);
													return false;
												})
											});
										  $(function() {	
											$("#almsprovinsi").chained("#almsnegara");			
										  });
										</script>
										<div class="form-group">
											<label class="control-label col-sm-3">Provinsi</label>	
											<div class="col-sm-8">    
												<select name="provtinggal" id='almsprovinsi' class="form-control col-sm-12" >
													<option value="">-Pilih Provinsi-</option>
													<?php foreach ($list_opt_prov as $lop){ ?>
													<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kota/Kabupaten</label>	
											<div class="col-sm-8">    
												<select name="kotatinggal" id='almskotakab' class="form-control col-sm-12" >
													<option value="">-Pilih Provinsi Dahulu-</option>												
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kecamatan</label>	
											<div class="col-sm-8">    
												<select name="kectinggal" id='almskec' class="form-control col-sm-12" >
													<option value="">-Pilih Kota/Kabupaten Dahulu-</option>										
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kelurahan/Desa</label>	
											<div class="col-sm-8">    
												<select name="keltinggal" id='almskeldesa' class="form-control col-sm-12" >
													<option value="">-Pilih Kecamatan Dahulu-</option>											
												</select>
											</div>
										</div>						
										<div  class="form-group"  >							  
										  <label class="control-label col-sm-3">Alamat</label>
										  <div class="col-sm-9">
											<textarea name="alamattinggal" style="text-transform:uppercase;" placeholder="Alamat sesuai tempat tinggal" class="form-control plktp" ></textarea>
										  </div>							  
										</div>
									</div>
							</div>
						</div>
				</div>
				</div>				
					<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					<button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>				
			  </div>
			</div>
			<div class="row setup-content" id="step-5">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 5</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP UTAMA</label>
					  <div class="col-sm-9">
						<input name="nohp1" style="text-transform:uppercase;" placeholder="Nomor Hp Utama" class="form-control plktp" type="number" maxlength="13">
					  </div>
					</div>												
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP kedua</label>
					  <div class="col-sm-9">
						<input name="nohp2" style="text-transform:uppercase;" placeholder="Nomor Hp Lainnya" class="form-control plktp" type="number" maxlength="13">
					  </div>
					</div>											
					<div class="form-group">
					  <label class="control-label col-sm-3">Email</label>
					  <div class="col-sm-9">
						<input name="email" style="text" placeholder="Alamat email" class="form-control plktp" type="email plktp">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">NPWP</label>
					  <div class="col-sm-9">
						<input name="npwp" id="npwp" style="text-transform:uppercase;" placeholder="Nomor NPWP" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal NPWP</label>
					  <div class="col-sm-9">
						<input name="tglnpwp" style="text-transform:uppercase;" placeholder="Tanggal NPWP" id="tglnpwp" data-date-format="dd-mm-yyyy" class="form-control" type="text" >
					  </div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" id="lanjut" type="button">Next</button>
				  <script>/*
					$('#npwp').onclick(function(e){
						$('#npwp').each(function(){
						!$(this).val() || $(this).find('input[name="tglnpwp"]').prop('required',true).closest(".form-group").addClass("has-error");
						});
					});*/
				  </script>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-6">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 6</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Department</label>	
						<div class="col-sm-8">    
							<select name="bag_dept" id='dept' class="form-control col-sm-12" >										
								<?php foreach ($list_opt_dept as $lodept){ ?>
								<option value="<?php echo trim($lodept->kddept);?>" ><?php echo trim($lodept->nmdept);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Sub Department</label>	
						<div class="col-sm-8">    
							<select name="subbag_dept" id='subdept' class="form-control col-sm-12" >
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_subdept as $losdept){ ?>
								<option value="<?php echo trim($losdept->kdsubdept);?>" class="<?php echo trim($losdept->kddept);?>"><?php echo trim($losdept->nmsubdept);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
							  $(function() {
							$('#dept').selectize();
							//$('#jobgrade').selectize();
							$("#subdept").chained("#dept");
							//$('#subdept').selectize();
							$("#jabatan").chained("#dept");

							$("#jobgrade").chained("#jabatan");

							  //
							  //$('#jabatan').selectize();
							  });
					</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Jabatan</label>	
						<div class="col-sm-8">    
							<select name="jabatan" id='jabatan' class="form-control col-sm-12" >	
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_jabt as $lojab){ ?>
								<option value="<?php echo trim($lojab->kdjabatan);?>" class="<?php echo trim($lojab->kddept);?>"><?php echo trim($lojab->nmjabatan);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>				
					<div class="form-group">
						<label class="control-label col-sm-3">Level Jabatan</label>	
						<div class="col-sm-8">    
							<select name="lvl_jabatan" id='lvljabatan' class="form-control col-sm-12" >										
								<?php foreach ($list_opt_lvljabt as $loljab){ ?>
								<option value="<?php echo trim($loljab->kdlvl);?>" ><?php echo trim($loljab->nmlvljabatan);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>				

					<div class="form-group">
						<label class="control-label col-sm-3">Atasan</label>	
						<div class="col-sm-8">    
							<!--<select name="nik_atasan" id="nikatasan" class="form-control col-sm-12" required>	-->
							<select id="nikatasan" class="form-control" data-placeholder="Pilih Atasan" name="nik_atasan" required>
								<option value="">--Pilih Atasan--</option>
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Atasan Ke-2</label>	
						<div class="col-sm-8">    
							<!--<select name="nik_atasan" id="nikatasan" class="form-control col-sm-12" required>	-->
							<select id="nikatasan2" class="form-control" data-placeholder="Pilih Atasan" name="nik_atasan2" >
								<option value="">--Pilih Atasan--</option>
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">PTKP</label>	
						<div class="col-sm-8">    
							<select name="status_ptkp" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_ptkp as $lptkp){ ?>
								<option value="<?php echo trim($lptkp->kodeptkp);?>" ><?php echo trim($lptkp->kodeptkp).' | '.trim($lptkp->besaranpertahun);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>

				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-7">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 7</h3>

					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji Pokok</label>
					  <div class="col-sm-9">
						<input name="gajipokok" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" maxlength="12" type="number">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS KES</label>
					  <div class="col-sm-9">
						<input name="gajibpjs" style="text-transform:uppercase;" placeholder="Gaji BPJS Kesehatan" class="form-control" maxlength="12" type="number">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS NAKER</label>
					  <div class="col-sm-9">
						<input name="gajinaker" placeholder="Gaji BPJS NAKER" class="form-control" maxlength="12" type="number">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama BANK</label>
					  <div class="col-sm-9">
						<select name="namabank" id='dept' class="form-control col-sm-12" >										
							<?php foreach ($list_opt_bank as $lbank){ ?>
							<option value="<?php echo trim($lbank->kdbank);?>" ><?php echo trim($lbank->nmbank);?></option>																																																			
							<?php };?>
						</select>
						
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
					  <div class="col-sm-9">
						<input name="namapemilikrekening" style="text-transform:uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nomor Rekening</label>
					  <div class="col-sm-9">
						<input name="norek" style="text-transform:uppercase;" placeholder="Nomor Rekening" class="form-control" type="number">
					  </div>
					</div>
					<!--<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" style="text-transform:uppercase;" placeholder="Nomor ID Absensi" class="form-control" type="text" required>
					  </div>
					</div>-->
					<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
					<!--</form>
					<!--<button class="btn btn-success btn-sm pull-right" id="btnSave" onclick="save()" type="submit">Submit</button>-->
					<!--<button class="btn btn-success btn-sm pull-right" type="submit">Submit</button>-->
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-8">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 8</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" style="text-transform:uppercase;" placeholder="Nomor ID Absensi / Badgenumber" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">ID Mesin</label>
						<div class="col-sm-9">
							<select name="idmesin" id='idmesin' class="form-control col-sm-12" >
								<?php foreach ($list_finger as $lf){ ?>
									<option value="<?php echo trim($lf->fingerid);?>" ><?php echo trim($lf->wilayah).' || '.trim($lf->ipaddress);?></option>
								<?php };?>
							</select>

						</div>
					</div>

					<div class="form-group">
						 <label class="control-label col-sm-3">BORONG</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="borong" id="borong">
										<option   value="f"> TIDAK</option>
										<option   value="t"> YA</option>
								</select>
						</div>
					</div>

					<div class="form-group">
						 <label class="control-label col-sm-3">SHIFT</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="shift" id="shift">
										<option   value="f"> TIDAK</option>
										<option   value="t"> YA</option>
								</select>
						</div>
					</div>
					
										
					<div class="form-group">
						 <label class="control-label col-sm-3">LEMBUR</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="lembur" id="lembur">
										<option   value="f"> TIDAK</option>
										<option   value="t"> YA</option>
								</select>
						</div>
					</div>
					
					
					<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					</form>
					<!--<button class="btn btn-success btn-sm pull-right" id="btnSave" onclick="save()" type="submit">Submit</button>-->
					<button class="btn btn-success btn-sm pull-right" type="submit">Submit</button>
				</div>
			  </div>
			</div>	
          </div>  
				
          </div>
          <div class="modal-footer">			           
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
		
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal --> 
  
 
  
  <!-- Edit modal -->
  <div class="modal fade" id="edit_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Edit Data</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="editform">
          <!--<input type="hidden" value="" name="id"/> -->  
			<div class="form-horizontal">
           <div class="form-group">
							  <label class="control-label col-sm-3">NIK Karyawan</label>
							  <div class="col-sm-9">
								<input name="nik" id="nik" style="text-transform:uppercase;" placeholder="Nomor Induk Karyawan" class="form-control" type="text">
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
							  <div class="col-sm-9">
								<input name="nmlengkap" style="text-transform:uppercase;" placeholder="Nama Lengkap" class="form-control" type="text" required>
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-sm-3">Nama Panggilan</label>
							  <div class="col-sm-9">
								<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-sm-3">Jenis Kelamin</label>
							  <div class="col-sm-9">
								<select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text" required>
									<option value="P">LAKI-LAKI</option>
									<option value="W">WANITA</option>
								</select>
							  </div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>	
								<div class="col-sm-8">    
									<select name="neglahir" id='enegara' class="form-control col-sm-12" required>										
										<?php foreach ($list_opt_neg as $lon){ ?>
										<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#eprovinsi").chained("#enegara");		
								$("#ekotakab").chained("#eprovinsi");		
							  });
							</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>	
								<div class="col-sm-8">    
									<select name="provlahir" id='eprovinsi' class="form-control col-sm-12" required >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_prov as $lop){ ?>
										<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>	
								<div class="col-sm-8">    
									<select name="kotalahir" id='ekotakab' class="form-control col-sm-12" required>
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_kotakab as $lok){ ?>
										<option value="<?php echo trim($lok->kodekotakab);?>" class="<?php echo trim($lok->kodeprov);?>"><?php echo trim($lok->namakotakab);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>						                                 
        </form>
          </div>
          </div>
          <div class="modal-footer">
            <button type="submit" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->
  	</div>
	
	
<!--- DATA KARYAWAN RESIGN -->	
<!--import department-->

<div class="tab-pane" id="tab_2" style="position: relative; height: 300px;" >

	<div class="box">
	<div class="col-lg-12">
	<!-- general form elements -->
	<div class="box box-primary">
	<div class="box-header">
	<h3 class="box-title">DATA KARYAWAN RESIGN</h3>
	</div><!-- /.box-header -->
	<!-- form start -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
			<table id="resign" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
			<tr>
			<th>No.</th>
			<th>NIK</th>
			<th>Nama Lengkap</th>
			<th>Department</th>
			<th>Jabatan</th>
			<th>Tgl Resign</th>
			<th>Alamat</th>

			</tr>
			</thead>
			<tbody>
			<?php $no=0; foreach($list_resignkary as $ls): $no++; ?>
			<tr>
			<td><?php echo $no;?></td>
			<td><?php echo $ls->nik;?></td>
			<td><?php echo $ls->nmlengkap;?></td>
			<td><?php echo $ls->nmdept;?></td>
			<td><?php echo $ls->nmjabatan;?></td>
			<td width="8%"><?php echo $ls->tglkeluarkerja;?></td> <!-- salah cok hahah-->
			<td><?php echo $ls->alamatktp;?></td> <!-- salah cok hahah-->

			</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			</div><!-- /.box-body -->
	</div><!-- /.box -->
	</div>
	</div>
</div>	


<div class="tab-pane" id="tab_3" style="position: relative; height: 300px;" >

	<div class="box">
	<div class="col-lg-12">
	<!-- general form elements -->
	<div class="box box-primary">
	<div class="box-header">
	<h3 class="box-title">DATA KARYAWAN BORONG</h3>
						<?php if($akses['aksesdownload']=='t') { ?>
					<a href="<?php echo site_url("trans/karyawan/excel_karyborong")?>"  class="btn btn-default" style="margin:10px;">Export Excel</a>
					<?php } ?>
	</div><!-- /.box-header -->
	<!-- form start -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
			<table id="boronggrid" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
			<tr>
			<th>No.</th>
			<th>NIK</th>
			<th>Nama Lengkap</th>
			<th>Department</th>
			<th>Jabatan</th>
			<!--th>Tgl Resign</th-->
			<th>Alamat</th>

			</tr>
			</thead>
			<tbody>
			<?php $no=0; foreach($list_borong as $ls): $no++; ?>
			<tr>
			<td><?php echo $no;?></td>
			<td><?php echo $ls->nik;?></td>
			<td><?php echo $ls->nmlengkap;?></td>
			<td><?php echo $ls->nmdept;?></td>
			<td><?php echo $ls->nmjabatan;?></td>
			<!--td width="8%"><?php echo $ls->tglkeluarkerja;?></td> <!-- salah cok hahah-->
			<td><?php echo $ls->alamatktp;?></td> <!-- salah cok hahah-->

			</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			</div><!-- /.box-body -->
	</div><!-- /.box -->
	</div>
	</div>
</div>	

