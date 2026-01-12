<?php 
/*
	@author : junis \m/
*/
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $("#example2").dataTable();
                $("#example4").dataTable();
                $("#nikatasan1").selectize();
                $("#nikatasan2").selectize();
				//datemask
				
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});                               
				//$("#datemaskinput").daterangepicker();                              
				$("#dateinput").datepicker();                               
				$("#tgl").datepicker();                               
				$("#tglktp").datepicker();                               
				$("#tgl2").datepicker();                               
				$("#tglmasuk").datepicker();                               
				$("#tglmasuk2").datepicker();                               
				$("#tglnpwp2").datepicker();                               
            });			
</script>
<legend><?php echo $title;?></legend>
<?php echo $message;?>
</br>
<div class="row">
	<div class="col-sm-12">
	<form action="<?php echo site_url('trans/karyawan/ajax_update'); ?>" method='post' id="form"> 
<div class="form-horizontal">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">					
				<li class="active"><a href="#tab_1" data-bs-toggle="tab">Profile Karyawan</a></li>
				<li><a href="#tab_2" data-bs-toggle="tab">Fisik</a></li>
				<li><a href="#tab_3" data-bs-toggle="tab">Data ID</a></li>
				<li><a href="#tab_4" data-bs-toggle="tab">Alamat</a></li>
				<li><a href="#tab_5" data-bs-toggle="tab">Kontak</a></li>
				<li><a href="#tab_6" data-bs-toggle="tab">Jabatan Pekerjaan</a></li>
				<li><a href="#tab_7" data-bs-toggle="tab">Sallary</a></li>
				<li><a href="#tab_8" data-bs-toggle="tab">Absensi</a></li>
			</ul>
		</div>
		<div class="tab-content">			
			<div class="tab-pane active" id="tab_1">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 1</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">NIK Karyawan</label>
					  <div class="col-sm-9">
						<input name="nik" id="nik" style="text-transform:uppercase;" value="<?php echo $dtl['nik'];?>" placeholder="Nomor Induk Karyawan" class="form-control" type="text" readonly >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
					  <div class="col-sm-9">
						<input name="nmlengkap" style="text-transform:uppercase;" value="<?php echo $dtl['nmlengkap'];?>" placeholder="Nama Lengkap" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Panggilan</label>
					  <div class="col-sm-9">
						<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" value="<?php echo $dtl['callname'];?>" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Jenis Kelamin</label>
					  <div class="col-sm-9">
						<select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text" >
							<option value="L" <?php if (trim($dtl['jk'])=='L') { echo 'selected';}?>>LAKI-LAKI</option>
							<option value="P" <?php if (trim($dtl['jk'])=='P') { echo 'selected';}?>>PEREMPUAN</option>
						</select>
					  </div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>	
						<div class="col-sm-8">    
							<select name="neglahir" id='negara' class="form-control col-sm-12" >										
								<?php foreach ($list_opt_neg as $lon){ ?>
								<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
					  $(function() {	
						$("#provinsi").chained("#negara");		
						$("#kotakab").chained("#provinsi");		
					  });
					</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>	
						<div class="col-sm-8">    
							<select name="provlahir" id='provinsi' class="form-control col-sm-12" >
								<option value="" >-KOSONG-</option>
								<?php foreach ($list_opt_prov as $lop){ ?>
								<option value="<?php echo trim($lop->kodeprov);?>" <?php if (trim($dtl['provlahir'])==trim($lop->kodeprov)) { echo 'selected';}?> class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>	
						<div class="col-sm-8">    
							<select name="kotalahir" id='kotakab' class="form-control col-sm-12" >
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_kotakab as $lok){ ?>
								<option value="<?php echo trim($lok->kodekotakab);?>" <?php if (trim($dtl['kotalahir'])==trim($lok->kodekotakab)) { echo 'selected';}?> class="<?php echo trim($lok->kodeprov);?>"><?php echo trim($lok->namakotakab);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal Lahir</label>
					  <div class="col-sm-9">
						<input name="tgllahir" style="text-transform:uppercase;" value="<?php echo $dtl['tgllahir'];?>" data-date-format="dd-mm-yyyy" placeholder="Tanggal Lahir" class="form-control" id="tgl" type="text" >
					  </div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Agama</label>	
						<div class="col-sm-8">    
							<select name="kd_agama" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_agama as $loa){ ?>
								<option value="<?php echo trim($loa->kdagama);?>" <?php if (trim($dtl['kdagama'])==trim($loa->kdagama)) { echo 'selected';}?> ><?php echo trim($loa->nmagama);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>				  
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_2">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 2</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">Keadaan Fisik</label>
					  <div class="col-sm-9">
						<select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<option value="t" <?php if (trim($dtl['stsfisik'])=='T') { echo 'selected';}?>>BAIK & SEHAT</option>
							<option value="f" <?php if (trim($dtl['stsfisik'])=='F') { echo 'selected';}?>>CACAT FISIK</option>
						</select>
					  </div>
					</div>
					<div  class="form-group"  >
					  <div id="f" class="fisiks" style="display:none">
						  <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
						  <div class="col-sm-9">
							<textarea name="ketfisik" style="text-transform:uppercase;" value="<?php echo $dtl['ketfisik'];?>" placeholder="Deskripsikan Cacat fisik" class="form-control" ></textarea>
						  </div>
					  </div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Kantor Wilayah</label>
						<div class="col-sm-9">
							<select name="kdcabang" id='kanwil' class="form-control col-sm-12" required>
								<?php foreach ($list_kanwil as $lf){ ?>
									<!--option value="<?php echo trim($lf->kdcabang);?>" ><?php echo trim($lf->desc_cabang);?></option-->
									<option value="<?php echo trim($lf->kdcabang);?>" <?php if (trim($dtl['kdcabang'])==trim($lf->kdcabang)) { echo 'selected';}?> ><?php echo trim($lf->desc_cabang);?></option>
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
							<input name="tglmasukkerja" value="<?php echo $dtl['tglmasukkerja'];?>" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglmasuk2" data-date-format="dd-mm-yyyy"  class="form-control" type="text" >
						</div>
					</div>
				</div>
			  </div>
			</div>			
			<div class="tab-pane" id="tab_3">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 3</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">No KTP</label>
					  <div class="col-sm-9">
						<input name="noktp" style="text-transform:uppercase;" placeholder="No Ktp" value="<?php echo $dtl['noktp'];?>" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP Dikeluaran di</label>
					  <div class="col-sm-9">
						<input name="ktpdikeluarkan" style="text-transform:uppercase;" value="<?php echo $dtl['ktpdikeluarkan'];?>" placeholder="Kota KTP di keluarkan" class="form-control" type="text" >
					  </div>
					</div>							
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal KTP Dikeluaran</label>
					  <div class="col-sm-9">
						<input name="tgldikeluarkan" style="text-transform:uppercase;" value="<?php echo $dtl['tgldikeluarkan'];?>" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" id="tgl2" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP seumur hidup</label>
					  <div class="col-sm-9">								
						<select id="ktpseumurhidup" name="ktp_seumurhdp" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<option value="f" <?php if (trim($dtl['ktp_seumurhdp'])=='F') { echo 'selected';}?>>TIDAK</option>
							<option value="t" <?php if (trim($dtl['ktp_seumurhdp'])=='T') { echo 'selected';}?>>IYA</option>
						</select>
					  </div>
					</div>
					<div class="form-group">							  
						<div id="f" class="ktpseumurhidupku" style="display:none">
						  <label class="control-label col-sm-3">Tanggal Berlaku</label>
						  <div class="col-sm-9">
							<textarea name="tglberlaku" id="tglktp"  style="text-transform:uppercase;" value="<?php echo $dtl['tglberlaku'];?>" data-date-format="dd-mm-yyyy" class="form-control" ></textarea>
						  </div>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Kewarganegaraan</label>
					  <div class="col-sm-9">								
						<select  name="stswn" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<option value="t" <?php if (trim($dtl['stswn'])=='T') { echo 'selected';}?>>Warga Negara Indonesia</option>
							<option value="f" <?php if (trim($dtl['stswn'])=='F') { echo 'selected';}?>>Warga Negara Asing</option>
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Status Pernikahan</label>
					  <div class="col-sm-9">								
						<select name="status_pernikahan" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<?php foreach ($list_opt_nikah as $lonikah){ ?>
							<option value="<?php echo trim($lonikah->kdnikah);?>" <?php if (trim($dtl['status_pernikahan'])==trim($lonikah->kdnikah)) { echo 'selected';}?> ><?php echo trim($lonikah->nmnikah);?></option>
							<?php };?>									
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Golongan Darah</label>
					  <div class="col-sm-9">								
						<select name="gol_darah" style="text-transform:uppercase;"  class="form-control" type="text" >
							<option value="A" <?php if (trim($dtl['goldarah'])=='A') { echo 'selected';}?>>A</option>
							<option value="B" <?php if (trim($dtl['goldarah'])=='B') { echo 'selected';}?>>B</option>
							<option value="AB" <?php if (trim($dtl['goldarah'])=='AB') { echo 'selected';}?>>AB</option>
							<option value="O" <?php if (trim($dtl['goldarah'])=='O') { echo 'selected';}?>>O</option>
						</select>
					  </div>
					</div>				  
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_4">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 4</h3>
				  <div class="form-group">
								<label class="control-label col-sm-3">NEGARA (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="negktp" id='almnegara' class="form-control col-sm-12" >										
										<?php foreach ($list_opt_neg as $lon){ ?>
										<option value="<?php echo trim($lon->kodenegara);?>" <?php if (trim($dtl['negktp'])==trim($lon->kodenegara)) { echo 'selected';}?>><?php echo trim($lon->namanegara);?></option>																																																			
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
								<label class="control-label col-sm-3">Provinsi (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="provktp" id='almprovinsi' class="form-control col-sm-12" >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_prov as $lop){ ?>
										<option value="<?php echo trim($lop->kodeprov);?>" <?php if (trim($dtl['provktp'])==trim($lop->kodeprov)) { echo 'selected';}?> class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kota/Kabupaten (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="kotaktp" id='almkotakab' class="form-control col-sm-12"  >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_kotakab as $lok){ ?>
										<option value="<?php echo trim($lok->kodekotakab);?>" <?php if (trim($dtl['kotaktp'])==trim($lok->kodekotakab)) { echo 'selected';}?> class="<?php echo trim($lok->kodeprov);?>"><?php echo trim($lok->namakotakab);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kecamatan (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="kecktp" id='almkec' class="form-control col-sm-12"  >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_kec as $lokec){ ?>
										<option value="<?php echo trim($lokec->kodekec);?>" <?php if (trim($dtl['kecktp'])==trim($lokec->kodekec)) { echo 'selected';}?> class="<?php echo trim($lokec->kodekotakab);?>"><?php echo trim($lokec->namakec);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kelurahan/Desa (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="kelktp" id='almkeldesa' class="form-control col-sm-12" >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_keldesa as $lokeldesa){ ?>
										<option value="<?php echo trim($lokeldesa->kodekeldesa);?>" class="<?php echo trim($lokeldesa->kodekeldesa);?>"><?php echo trim($lokeldesa->namakeldesa);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div  class="form-group"  >							  
							  <label class="control-label col-sm-3">Alamat (sesuai KTP)</label>
							  <div class="col-sm-9">
								<textarea name="alamatktp" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ><?php echo $dtl['alamatktp'];?></textarea>
							  </div>							  
							</div>							
							<div class="form-group">
								<label class="control-label col-sm-3">NEGARA (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="negtinggal" id='almsnegara' class="form-control col-sm-12" >										
										<?php foreach ($list_opt_neg as $lon){ ?>
										<option value="<?php echo trim($lon->kodenegara);?>" <?php if (trim($dtl['negtinggal'])==trim($lon->kodenegara)) { echo 'selected';}?>><?php echo trim($lon->namanegara);?></option>																																																			
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
								<label class="control-label col-sm-3">Provinsi (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="provtinggal" id='almsprovinsi' class="form-control col-sm-12" >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_prov as $lop){ ?>
										<option value="<?php echo trim($lop->kodeprov);?>" <?php if (trim($dtl['provtinggal'])==trim($lop->kodeprov)) { echo 'selected';}?> class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kota/Kabupaten (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="kotatinggal" id='almskotakab' class="form-control col-sm-12" >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_kotakab as $lok){ ?>
										<option value="<?php echo trim($lok->kodekotakab);?>" <?php if (trim($dtl['kotatinggal'])==trim($lok->kodekotakab)) { echo 'selected';}?> class="<?php echo trim($lok->kodeprov);?>"><?php echo trim($lok->namakotakab);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kecamatan (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="kectinggal" id='almskec' class="form-control col-sm-12" >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_kec as $lokec){ ?>
										<option value="<?php echo trim($lokec->kodekec);?>" <?php if (trim($dtl['kectinggal'])==trim($lokec->kodekec)) { echo 'selected';}?> class="<?php echo trim($lokec->kodekotakab);?>"><?php echo trim($lokec->namakec);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kelurahan/Desa (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="keltinggal" id='almskeldesa' class="form-control col-sm-12" >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_keldesa as $lokeldesa){ ?>
										<option value="<?php echo trim($lokeldesa->kodekeldesa);?>" class="<?php echo trim($lokeldesa->kodekec);?>"><?php echo trim($lokeldesa->namakeldesa);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>						
							<div  class="form-group"  >							  
							  <label class="control-label col-sm-3">Alamat (Sesuai Tempat Tinggal)</label>
							  <div class="col-sm-9">
								<textarea name="alamattinggal" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ><?php echo $dtl['alamattinggal'];?></textarea>
							  </div>							  
							</div>				  
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_5">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 5</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP UTAMA</label>
					  <div class="col-sm-9">
						<input name="nohp1" value="<?php echo $dtl['nohp1'];?>" style="text-transform:uppercase;" placeholder="Nomor Handphone Utama" class="form-control" type="input">
					  </div>
					</div>												
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP kedua</label>
					  <div class="col-sm-9">
						<input name="nohp2" value="<?php echo $dtl['nohp2'];?>" style="text-transform:uppercase;" placeholder="Nomor Handphone Lainnya" class="form-control" type="input" >
					  </div>
					</div>											
					<div class="form-group">
					  <label class="control-label col-sm-3">Email</label>
					  <div class="col-sm-9">
						<input name="email" value="<?php echo $dtl['email'];?>" style="text-transform:uppercase;" placeholder="Alamat email" class="form-control" type="email" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">NPWP</label>
					  <div class="col-sm-9">
						<input name="npwp" value="<?php echo $dtl['npwp'];?>" style="text-transform:uppercase;" placeholder="Nomor NPWP" class="form-control" type="input" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal NPWP</label>
					  <div class="col-sm-9">
				
						<input name="tglnpwp" style="text-transform:uppercase;" value="<?php echo $dtl['tglnpwp'];?>" data-date-format="dd-mm-yyyy" placeholder="Tanggal NPWP" class="form-control" id="tglnpwp2" type="text" >
					  </div>
					</div>				  
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_6">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 6</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Department</label>	
						<div class="col-sm-8">    
							<select name="dept" id='dept' class="form-control col-sm-12">										
								<?php foreach ($list_opt_dept as $lodept){ ?>
								<option value="<?php echo trim($lodept->kddept);?>" <?php if (trim($dtl['bag_dept'])==trim($lodept->kddept)) { echo 'selected';}?> ><?php echo trim($lodept->nmdept);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Sub Department</label>	
						<div class="col-sm-8" >    
							<select name="subbag_dept" id='subdept' class="form-control col-sm-12" >
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_subdept as $losdept){ ?>
								<option value="<?php echo trim($losdept->kdsubdept);?>" <?php if (trim($dtl['subbag_dept'])==trim($losdept->kdsubdept)) { echo 'selected';}?> class="<?php echo trim($losdept->kddept);?>"><?php echo trim($losdept->nmsubdept);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
					  $(function() {	
						$("#subdept").chained("#dept");										
						$("#jabatan").chained("#subdept");																		
						$("#jobgrade").chained("#lvljabatan");																		
					  });
					</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Jabatan</label>	
						<div class="col-sm-8">    
							<select name="jabatan" id='jabatan' class="form-control col-sm-12" >	
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_jabt as $lojab){ ?>
								<option value="<?php echo trim($lojab->kdjabatan);?>" <?php if (trim($dtl['jabatan'])==trim($lojab->kdjabatan)) { echo 'selected';}?> class="<?php echo trim($lojab->kdsubdept);?>"><?php echo trim($lojab->nmjabatan);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Level Jabatan</label>	
						<div class="col-sm-8">    
							<select name="lvl_jabatan" id='lvljabatan' class="form-control col-sm-12" >										
								<?php foreach ($list_opt_lvljabt as $loljab){ ?>
								<option value="<?php echo trim($loljab->kdlvl);?>" <?php if (trim($dtl['lvl_jabatan'])==trim($loljab->kdlvl)) { echo 'selected';}?> ><?php echo trim($loljab->kdlvl).' || '.trim($loljab->nmlvljabatan);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Atasan</label>	
						<div class="col-sm-8">    
							<select name="nik_atasan" id="nikatasan1" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" <?php if (trim($dtl['nik_atasan'])==trim($loan->nik)) { echo 'selected';}?> ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Atasan ke-2</label>	
						<div class="col-sm-8">    
							<select name="nik_atasan2" id="nikatasan2" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" <?php if (trim($dtl['nik_atasan2'])==trim($loan->nik)) { echo 'selected';}?> ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">PTKP</label>	
						<div class="col-sm-8">    
							<select name="status_ptkp" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_ptkp as $lptkp){ ?>
								<option value="<?php echo trim($lptkp->kodeptkp);?>" <?php if (trim($dtl['status_ptkp'])==trim($lptkp->kodeptkp)) { echo 'selected';}?> ><?php echo trim($lptkp->kodeptkp).' | '.trim($lptkp->besaranpertahun);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>

				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_7">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 7</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Group Penggajian</label>	
						<div class="col-sm-8">    
							<select name="grouppenggajian" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_grp_gaji as $lgaji){ ?>
								<option value="<?php echo trim($lgaji->kdgroup_pg);?>" <?php if (trim($dtl['grouppenggajian'])==trim($lgaji->kdgroup_pg)) { echo 'selected';}?>><?php echo trim($lgaji->kdgroup_pg).' | '.trim($lgaji->nmgroup_pg);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji Pokok</label>
					  <div class="col-sm-9">
						<input name="gajipokok" value="<?php echo $dtl['gajipokok'];?>" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" type="text" readonly>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS KES</label>
					  <div class="col-sm-9">
						<input name="gajibpjs" value="<?php echo $dtl['gajibpjs'];?>" style="text-transform:uppercase;" placeholder="Gaji BPJS KES" class="form-control" type="text" readonly>
					  </div>
					</div>
					
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS NAKER</label>
					  <div class="col-sm-9">
						<input name="gajinaker" value="<?php echo $dtl['gajinaker'];?>" style="text-transform:uppercase;" placeholder="Gaji BPJS NAKER" class="form-control" type="text" readonly>
					  </div>
					</div>
				
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama BANK</label>
					  <div class="col-sm-9">
						<select name="namabank" id='dept' class="form-control col-sm-12" >										
							<?php foreach ($list_opt_bank as $lbank){ ?>
							<option value="<?php echo trim($lbank->kdbank);?>" <?php if (trim($dtl['namabank'])==trim($lbank->kdbank)) { echo 'selected';}?> ><?php echo trim($lbank->nmbank);?></option>																																																			
							<?php };?>
						</select>
						
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
					  <div class="col-sm-9">
						<input name="namapemilikrekening" value="<?php echo $dtl['namapemilikrekening'];?>" style="text-transform:uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nomor Rekening</label>
					  <div class="col-sm-9">
						<input name="norek" value="<?php echo $dtl['norek'];?>" style="text-transform:uppercase;" placeholder="Nomor Rekening" class="form-control" type="text" >
					  </div>
					</div>
					<!--<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" value="<?php echo $dtl['idabsen'];?>" style="text-transform:uppercase;" placeholder="Nomor Induk Karyawan" class="form-control" type="text" >
					  </div>
					</div>-->
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_8">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 8</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" style="text-transform:uppercase;" value="<?php echo trim($dtl['idabsen']);?>" placeholder="Nomor ID Absensi" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">ID Mesin</label>
						<div class="col-sm-9">
							<select name="idmesin" id='idmesin' class="form-control col-sm-12" disabled>
								<?php foreach ($list_finger as $lf){ ?>
									<option <?php if(trim($dtl['idmesin'])==trim($lf->fingerid))?> value="<?php echo trim($lf->fingerid);?>" ><?php echo trim($lf->wilayah).' || '.trim($lf->ipaddress);?></option>
								<?php };?>
							</select>
						</div>
					</div>

					<div class="form-group">
						 <label class="control-label col-sm-3">Borong</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="borong" id="borong">
								 <option  <?php if(trim($dtl['tjborong'])=='') { echo 'selected';} ?> value="f">--PILIH BORONG--</option> 
								 <option  <?php if(trim($dtl['tjborong'])=='t') { echo 'selected';} ?> value="t"> YA</option>
								 <option  <?php if(trim($dtl['tjborong'])=='f') { echo 'selected';} ?> value="f"> TIDAK</option>
								 
								</select>
						</div>
					</div>
					
					<div class="form-group">
						 <label class="control-label col-sm-3">Shift</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="shift" id="shift">
								 <option  <?php if(trim($dtl['tjshift'])=='') { echo 'selected';} ?> value="f">--PILIH SHIFT--</option> 
								 <option  <?php if(trim($dtl['tjshift'])=='t') { echo 'selected';} ?> value="t"> YA</option>
								 <option  <?php if(trim($dtl['tjshift'])=='f') { echo 'selected';} ?> value="f"> TIDAK</option>
								 
								</select>
						</div>
					</div>
					
					<div class="form-group">
						 <label class="control-label col-sm-3">Lembur</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="lembur" id="lembur">
								 <option  <?php if(trim($dtl['tjlembur'])=='') { echo 'selected';} ?> value="f">--PILIH LEMBUR--</option> 
								 <option  <?php if(trim($dtl['tjlembur'])=='t') { echo 'selected';} ?> value="t"> YA</option>
								 <option  <?php if(trim($dtl['tjlembur'])=='f') { echo 'selected';} ?> value="f"> TIDAK</option>
								 
								</select>
						</div>
					</div>
					<!--<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					
					<!--<button class="btn btn-success btn-sm pull-right" id="btnSave" onclick="save()" type="submit">Submit</button>-->
					<!--<button class="btn btn-success btn-sm pull-right" type="submit">Submit</button>-->
				</div>
			  </div>
			</div>	
          </div>  
		  </div>
<div class="row">
	<div class="col-sm-6">		
		<a href="<?php echo site_url('trans/karyawan');?>" class="btn btn-primary" style="margin:10px">Kembali</a>
		<button type="submit" class="btn btn-primary" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin:10px">Simpan Data</button>
	</div>	
	<div class="col-sm-6">		
		
	</div>
</div>
</form>

