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
				//datemask
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});                               
				//$("#datemaskinput").daterangepicker();                              
				$("#dateinput").datepicker();                               
            });			
</script>
<legend><?php echo $title;?></legend>
<?php echo $message;?>
</br>
<div class="row">
	<div class="col-sm-12">
		</div>
<div class="form-horizontal" id="detailku">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">					
				<li class="active"><a href="#tab_1" data-bs-toggle="tab">Profile Karyawan</a></li>
				<li><a href="#tab_2" data-bs-toggle="tab">Kontak</a></li>
				<li><a href="#tab_3" data-bs-toggle="tab">Alamat</a></li>
				<li><a href="#tab_4" data-bs-toggle="tab">Jabatan</a></li>
				<li><a href="#tab_5" data-bs-toggle="tab">Riwayat Keluarga</a></li>
				<li><a href="#tab_6" data-bs-toggle="tab">Riwayat Kesehatan</a></li>
				<li><a href="#tab_7" data-bs-toggle="tab">Riwayat Pengalaman Kerja</a></li>
				<li><a href="#tab_8" data-bs-toggle="tab">Riwayat Pendidikan</a></li>
			</ul>
		</div>
		<div class="tab-content">			
			<div class="tab-pane active" id="tab_1">
				<div class="row">
					<div class="col-sm-6">
						<div class="box box-info col-sm-6">
							<div class="box-body" style="padding:5px;">
								<div class="form-group">
								  <label class="control-label col-sm-3">NIK Karyawan</label>
								  <div class="col-sm-9">
									<input name="nik" id="nik" style="text-transform:uppercase;" value="<?php echo $dtl['nik'];?>" placeholder="Nomor Induk Karyawan" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
								  <div class="col-sm-9">
									<input name="nmlengkap" style="text-transform:uppercase;" value="<?php echo $dtl['nmlengkap'];?>" placeholder="Nama Lengkap" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Nama Panggilan</label>
								  <div class="col-sm-9">
									<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" value="<?php echo $dtl['callname'];?>" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Jenis Kelamin</label>
								  <div class="col-sm-9">
									<select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text" disabled>
										<option value="P" <?php if (trim($dtl['jk'])=='P') { echo 'selected';}?>>LAKI-LAKI</option>
										<option value="W" <?php if (trim($dtl['jk'])=='W') { echo 'selected';}?>>PEREMPUAN</option>
									</select>
								  </div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>	
									<div class="col-sm-8">    
										<select name="neglahir" id='negara' class="form-control col-sm-12" disabled>										
											<option value="" ><?php echo $dtl['neglahir'];?></option>
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
										<select name="provlahir" id='provinsi' class="form-control col-sm-12" disabled>
											<option value="" ><?php echo $dtl['provlahir'];?></option>											
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>	
									<div class="col-sm-8">    
										<select name="kotalahir" id='kotakab' class="form-control col-sm-12" disabled>
											<option value="" ><?php echo $dtl['kotalahir'];?></option>
										</select>
									</div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Tanggal Lahir</label>
								  <div class="col-sm-9">
									<input name="tgllahir" style="text-transform:uppercase;" value="<?php echo $dtl['tgllahir'];?>" data-date-format="dd-mm-yyyy" placeholder="Tanggal Lahir" class="form-control" id="tgl" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Agama</label>	
									<div class="col-sm-8">    
										<select name="kd_agama" class="form-control col-sm-12" disabled>										
											<?php foreach ($list_opt_agama as $loa){ ?>
											<option value="<?php echo trim($loa->kdagama);?>" <?php if (trim($dtl['kdagama'])==trim($loa->kdagama)) { echo 'selected';}?> ><?php echo trim($loa->nmagama);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Status Pernikahan</label>
								  <div class="col-sm-9">								
									<select name="stastus_pernikahan" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" disabled>																		
										<?php foreach ($list_opt_nikah as $lonikah){ ?>
										<option value="<?php echo trim($lonikah->kdnikah);?>" <?php if (trim($dtl['status_pernikahan'])==trim($lonikah->kdnikah)) { echo 'selected';}?> ><?php echo trim($lonikah->nmnikah);?></option>
										<?php };?>									
									</select>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Kewarganegaraan</label>
								  <div class="col-sm-9">								
									<select  name="stswn" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" disabled>
										<option value="t" <?php if (trim($dtl['stswn'])=='T') { echo 'selected';}?>>Warga Negara Indonesia</option>
										<option value="f" <?php if (trim($dtl['stswn'])=='F') { echo 'selected';}?>>Warga Negara Asing</option>
									</select>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Golongan Darah</label>
								  <div class="col-sm-9">								
									<select name="gol_darah" style="text-transform:uppercase;"  class="form-control" type="text" disabled>
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
					<div class="col-sm-6">
						<div class="box box-info col-sm-6">																					
							<div class="box-body" style="padding:5px;">								
								<div class="form-group">								
									<div id="gbr">
										<img id="gbr1" src="<?php if ($dtl['image']<>'') { echo base_url('assets/img/profile/'.$dtl['image']);} else { echo base_url('assets/img/user.png');} ;?>" width="240px" height="320%" alt="User Image">
									</div>
									<a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target=".gantigambar">Ganti Foto</a>
								</div>	
								<div class="form-group">
								  <label class="control-label col-sm-3">No KTP</label>
								  <div class="col-sm-9">
									<input name="noktp" style="text-transform:uppercase;" placeholder="No Ktp" value="<?php echo $dtl['noktp'];?>" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">KTP Dikeluaran di</label>
								  <div class="col-sm-9">
									<input name="ktpdikeluarkan" style="text-transform:uppercase;" value="<?php echo $dtl['ktpdikeluarkan'];?>" placeholder="Kota KTP di keluarkan" class="form-control" type="text" disabled>
								  </div>
								</div>							
								<div class="form-group">
								  <label class="control-label col-sm-3">Tanggal KTP Dikeluaran</label>
								  <div class="col-sm-9">
									<input name="tgldikeluarkan" style="text-transform:uppercase;" value="<?php echo $dtl['tgldikeluarkan'];?>" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" id="tgl2" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">KTP seumur hidup</label>
								  <div class="col-sm-9">								
									<select id="ktpseumurhidup" name="ktp_seumurhdp" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" disabled>
										<option value="f" <?php if (trim($dtl['ktp_seumurhdp'])=='F') { echo 'selected';}?>>TIDAK</option>
										<option value="t" <?php if (trim($dtl['ktp_seumurhdp'])=='T') { echo 'selected';}?>>IYA</option>
									</select>
								  </div>
								</div>
								<div class="form-group">							  
									<div id="f" class="ktpseumurhidupku" style="display:none">
									  <label class="control-label col-sm-3">Tanggal Berlaku</label>
									  <div class="col-sm-9">
										<textarea name="tglberlaku" id="tglktp"  style="text-transform:uppercase;" value="<?php echo $dtl['tglberlaku'];?>" data-date-format="dd-mm-yyyy" class="form-control" disabled></textarea>
									  </div>
									</div>
								</div>
							</div>
						</div>
					</div>					
				</div>
			</div>
			<div class="tab-pane" id="tab_2">
				<div class="row">					
					<div class="col-sm-6">
						<div class="box box-info col-sm-6">
							<div class="box-body" style="padding:5px;">	
								<h5> Contact Person</h5>
								<div class="form-group">
								  <label class="control-label col-sm-3">NO HP UTAMA</label>
								  <div class="col-sm-9">
									<input name="nohp1" value="<?php echo $dtl['nohp1'];?>" style="text-transform:uppercase;" placeholder="Nomor Handphone Utama" class="form-control" type="text" disabled>
								  </div>
								</div>												
								<div class="form-group">
								  <label class="control-label col-sm-3">NO HP kedua</label>
								  <div class="col-sm-9">
									<input name="nohp2" value="<?php echo $dtl['nohp2'];?>" style="text-transform:uppercase;" placeholder="Nomor Handphone Lainnya" class="form-control" type="text" disabled>
								  </div>
								</div>											
								<div class="form-group">
								  <label class="control-label col-sm-3">Email</label>
								  <div class="col-sm-9">
									<input name="email" value="<?php echo $dtl['email'];?>" style="text-transform:uppercase;" placeholder="Alamat email" class="form-control" type="email" disabled>
								  </div>
								</div>
								<h5> Nomor Pokok Wajib Pajak</h5>
								<div class="form-group">
								  <label class="control-label col-sm-3">NPWP</label>
								  <div class="col-sm-9">
									<input name="npwp" value="<?php echo $dtl['npwp'];?>" style="text-transform:uppercase;" placeholder="Nomor NPWP" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Tanggal NPWP</label>
								  <div class="col-sm-9">
									<input name="tglnpwp" value="<?php echo $dtl['tglnpwp'];?>" style="text-transform:uppercase;" placeholder="Tanggal NPWP" id="tglnpwp" data-date-format="dd-mm-yyyy" class="form-control" type="text" disabled>
								  </div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="box box-info col-sm-6">
							<div class="box-body" style="padding:5px;">	
								<h5>Keadaan Fisik Pegawai</h5>
								<div class="form-group">
								  <label class="control-label col-sm-3">Keadaan Fisik</label>
								  <div class="col-sm-9">
									<select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" disabled>
										<option value="t" <?php if (trim($dtl['stsfisik'])=='T') { echo 'selected';}?>>BAIK & SEHAT</option>
										<option value="f" <?php if (trim($dtl['stsfisik'])=='F') { echo 'selected';}?>>CACAT FISIK</option>
									</select>
								  </div>
								</div>
								<?php if (trim($dtl['stsfisik'])=='T') { ?>
								<div  class="form-group"  >
								  <div id="f" class="fisiks" >
									  <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
									  <div class="col-sm-9">
										<textarea name="ketfisik" style="text-transform:uppercase;" value="<?php echo $dtl['ketfisik'];?>" placeholder="Deskripsikan Cacat fisik" class="form-control" disabled></textarea>
									  </div>
								  </div>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>			
			<div class="tab-pane" id="tab_3">
			  <div class="row">
				<div class="col-sm-6">
					<div class="box box-info col-sm-6">
						<div class="box-body" style="padding:5px;">	
							<h5>Alamat Sesuai dengan KTP</h5>
							<div class="form-group">
								<label class="control-label col-sm-3">NEGARA (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="negktp" id='almnegara' class="form-control col-sm-12" disabled>																				
										<option value="" ><?php echo trim($dtl['negktp']);?></option>																																																													
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#almprovinsi").chained("#almnegara");		
								$("#almkotakab").chained("#almprovinsi");		
								$("#almkec").chained("#almkotakab");		
								$("#almkeldesa").chained("#almkec");		
							  });
							</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Provinsi (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="provktp" id='almprovinsi' class="form-control col-sm-12" disabled>
										<option value="" ><?php echo trim($dtl['provktp']);?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kota/Kabupaten (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="kotaktp" id='almkotakab' class="form-control col-sm-12"  disabled>
										<option value="" ><?php echo trim($dtl['kotaktp']);?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kecamatan (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="kecktp" id='almkec' class="form-control col-sm-12"  disabled>
										<option value="" ><?php echo trim($dtl['kecktp']);?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kelurahan/Desa (sesuai KTP)</label>	
								<div class="col-sm-8">    
									<select name="kelktp" id='almkeldesa' class="form-control col-sm-12" disabled>
										<option value="" ><?php echo trim($dtl['kelktp']);?></option>
									</select>
								</div>
							</div>
							<div  class="form-group"  >							  
							  <label class="control-label col-sm-3">Alamat (sesuai KTP)</label>
							  <div class="col-sm-9">
								<textarea name="alamatktp" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" disabled><?php echo $dtl['alamatktp'];?></textarea>
							  </div>							  
							</div>														
						</div>														
					</div>
				</div>
				<div class="col-sm-6">
					<div class="box box-info col-sm-6">
						<div class="box-body" style="padding:5px;">
							<h5>Alamat Sesuai tempat tinggal sekarang</h5>
							<div class="form-group">
								<label class="control-label col-sm-3">NEGARA (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="negtinggal" id='almsnegara' class="form-control col-sm-12" disabled>										
										<option value="" ><?php echo trim($dtl['negtinggal']);?></option>
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#almsprovinsi").chained("#almsnegara");		
								$("#almskotakab").chained("#almsprovinsi");		
								$("#almskec").chained("#almskotakab");		
								$("#almskseldesa").chained("#almskec");		
							  });
							</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Provinsi (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="provtinggal" id='almsprovinsi' class="form-control col-sm-12" disabled>
										<option value="" ><?php echo trim($dtl['provtinggal']);?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kota/Kabupaten (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="kotatinggal" id='almskotakab' class="form-control col-sm-12" disabled>
										<option value="" ><?php echo trim($dtl['keltinggal']);?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kecamatan (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="kectinggal" id='almskec' class="form-control col-sm-12" disabled>
										<option value="" ><?php echo trim($dtl['kectinggal']);?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Kelurahan/Desa (Sesuai Tempat Tinggal)</label>	
								<div class="col-sm-8">    
									<select name="keltinggal" id='almskeldesa' class="form-control col-sm-12" disabled>
										<option value="" ><?php echo trim($dtl['keltinggal']);?></option>
									</select>
								</div>
							</div>						
							<div  class="form-group"  >							  
							  <label class="control-label col-sm-3">Alamat (Sesuai Tempat Tinggal)</label>
							  <div class="col-sm-9">
								<textarea name="alamattinggal" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" disabled><?php echo $dtl['alamattinggal'];?></textarea>
							  </div>							  
							</div>
						</div>
					</div>
				</div>				
			  </div>
			</div>
			<div class="tab-pane" id="tab_4">			  
				<div class="row">
					<div class="col-sm-6">
						<div class="box box-info col-sm-6">
							<div class="box-body" style="padding:5px;">
								<div class="form-group">
									<label class="control-label col-sm-3">Department</label>	
									<div class="col-sm-8">    
										<select name="bag_dept" id='dept' class="form-control col-sm-12" disabled>										
											<?php foreach ($list_opt_dept as $lodept){ ?>
											<option value="<?php echo trim($lodept->kddept);?>" <?php if (trim($dtl['bag_dept'])==trim($lodept->kddept)) { echo 'selected';}?> ><?php echo trim($lodept->nmdept);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Sub Department</label>	
									<div class="col-sm-8">    
										<select name="subbag_dept" id='subdept' class="form-control col-sm-12"  disabled readonly>
											<!--<option value="">-KOSONG-</option>-->
											<?php foreach ($list_opt_subdept as $losdept){ ?>
											<option value="<?php echo trim($losdept->kdsubdept);?>" <?php if (trim($dtl['subbag_dept'])==trim($losdept->kdsubdept)) { echo 'selected';}?> class="<?php echo trim($losdept->kddept);?>"><?php echo trim($losdept->nmsubdept);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<script type="text/javascript" charset="utf-8">
								  $(function() {	
									$("#subdept1").chained("#dept1");																			
									$("#jabatan1").chained("#subdept1");																		
									$("#jobgrade1").chained("#lvljabatan1");																		
								  });
								</script>
								<div class="form-group">
									<label class="control-label col-sm-3">Jabatan</label>	
									<div class="col-sm-8">    
										<select name="jabatan" id='jabatan' class="form-control col-sm-12" disabled readonly>	
											<!--<option value="">-KOSONG-</option>-->
											<?php foreach ($list_opt_jabt as $lojab){ ?>
											<option value="<?php echo trim($lojab->kdjabatan);?>" <?php if (trim($dtl['jabatan'])==trim($lojab->kdjabatan)) { echo 'selected';}?> class="<?php echo trim($lojab->kdsubdept);?>"><?php echo trim($lojab->nmjabatan);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Golongan</label>	
									<div class="col-sm-8">    
										<select name="lvl_jabatan" id='lvljabatan' class="form-control col-sm-12" disabled>										
											<?php foreach ($list_opt_lvljabt as $loljab){ ?>
											<option value="<?php echo trim($loljab->kdlvl);?>" <?php if (trim($dtl['lvl_jabatan'])==trim($loljab->kdlvl)) { echo 'selected';}?> ><?php echo trim($loljab->nmlvljabatan);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Job Grade</label>	
									<div class="col-sm-8">    
										<select name="grade_golongan" id='jobgrade' class="form-control col-sm-12" readonly disabled>
											<!--<option value="">-KOSONG-</option>-->
											<?php foreach ($list_opt_goljabt as $logjab){ ?>
											<option value="<?php echo trim($logjab->kdgrade);?>" <?php if (trim($dtl['grade_golongan'])==trim($logjab->kdgrade)) { echo 'selected';}?> class="<?php echo trim($logjab->kdlvl);?>"><?php echo trim($logjab->nmgrade);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Atasan</label>	
									<div class="col-sm-8">    
										<select name="nik_atasan" class="form-control col-sm-12" disabled readonly>										
											<?php foreach ($list_opt_atasan as $loan){ ?>
											<option value="<?php echo trim($loan->nik);?>" <?php if (trim($dtl['nik_atasan'])==trim($loan->nik)) { echo 'selected';}?> ><?php echo trim($loan->nmlengkap);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">Atasan ke-2</label>	
									<div class="col-sm-8">    
										<select name="nik_atasan2" class="form-control col-sm-12" disabled readonly>										
											<?php foreach ($list_opt_atasan as $loan){ ?>
											<option value="<?php echo trim($loan->nik);?>" <?php if (trim($dtl['nik_atasan2'])==trim($loan->nik)) { echo 'selected';}?> ><?php echo trim($loan->nmlengkap);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">PTKP</label>	
									<div class="col-sm-8">    
										<select name="status_ptkp" class="form-control col-sm-12" disabled>										
											<?php foreach ($list_opt_ptkp as $lptkp){ ?>
											<option value="<?php echo trim($lptkp->kodeptkp);?>" <?php if (trim($dtl['status_ptkp'])==trim($lptkp->kodeptkp)) { echo 'selected';}?> ><?php echo trim($lptkp->kodeptkp).' | '.trim($lptkp->besaranpertahun);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Tanggal Masuk</label>
								  <div class="col-sm-9">
									<input name="tglmasukkerja" value="<?php echo $dtl['tglmasukkerja'];?>" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglmasuk" data-date-format="dd-mm-yyyy" class="form-control" type="text" disabled>
								  </div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="box box-info col-sm-6">
							<div class="box-body" style="padding:5px;">
								<div class="form-group">
									<label class="control-label col-sm-3">Group Penggajian</label>	
									<div class="col-sm-8">    
										<select name="grouppenggajian" class="form-control col-sm-12" disabled>										
											<?php foreach ($list_opt_grp_gaji as $lgaji){ ?>
											<option value="<?php echo trim($lgaji->kdgroup_pg);?>" <?php if (trim($dtl['grouppenggajian'])==trim($lgaji->kdgroup_pg)) { echo 'selected';}?>><?php echo trim($lgaji->kdgroup_pg).' | '.trim($lgaji->nmgroup_pg);?></option>																																																			
											<?php };?>
										</select>
									</div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Gaji Pokok</label>
								  <div class="col-sm-9">
									<input name="gajipokok" value="<?php echo $dtl['gajipokok'];?>" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Gaji BPJS</label>
								  <div class="col-sm-9">
									<input name="gajibpjs" value="<?php echo $dtl['gajibpjs'];?>" style="text-transform:uppercase;" placeholder="Gaji BPJS" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Nama BANK</label>
								  <div class="col-sm-9">
									<select name="namabank" id='dept' class="form-control col-sm-12" disabled>										
										<?php foreach ($list_opt_bank as $lbank){ ?>
										<option value="<?php echo trim($lbank->kdbank);?>" <?php if (trim($dtl['namabank'])==trim($lbank->kdbank)) { echo 'selected';}?> ><?php echo trim($lbank->nmbank);?></option>																																																			
										<?php };?>
									</select>
									
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
								  <div class="col-sm-9">
									<input name="namapemilikrekening" value="<?php echo $dtl['namapemilikrekening'];?>" style="text-transform:uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">Nomor Rekening</label>
								  <div class="col-sm-9">
									<input name="norek" value="<?php echo $dtl['norek'];?>" style="text-transform:uppercase;" placeholder="Nomor Rekening" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-sm-3">ID Absensi/Badgenumber</label>
								  <div class="col-sm-9">
									<input name="idabsen" value="<?php echo $dtl['idabsen'];?>" style="text-transform:uppercase;" placeholder="Nomor Induk Karyawan" class="form-control" type="text" disabled>
								  </div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-3">ID Mesin</label>
									<div class="col-sm-9">
										<input name="idmesin" value="<?php echo $dtl['idmesin'];?>" style="text-transform:uppercase;" placeholder="ID Mesin Finger" class="form-control" type="text" disabled>
									</div>
								</div>
							<div class="form-group">
							 <label class="control-label col-sm-3">BORONG</label>
								 <div class="col-sm-9">
									<input name="borong" value="<?php echo $dtl['tjborong1'];?>" style="text-transform:uppercase;" class="form-control" type="text" disabled>
								  </div>
							</div>
							<div class="form-group">
							 <label class="control-label col-sm-3">SHIFT</label>
								 <div class="col-sm-9">
									<input name="shift" value="<?php echo $dtl['tjshift1'];?>" style="text-transform:uppercase;" class="form-control" type="text" disabled>
								  </div>
							</div>
							<div class="form-group">
							 <label class="control-label col-sm-3">LEMBUR</label>
								 <div class="col-sm-9">
									<input name="lembur" value="<?php echo $dtl['tjlembur1'];?>" style="text-transform:uppercase;" class="form-control" type="text" disabled>
								  </div>
							</div>
							
							</div>
						</div>
					</div>
				</div>			  
			</div>
			<div class="tab-pane" id="tab_5">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  	<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->
							<th>Nama Keluarga</th>
							<th>Jenis Kelamin</th>
							<th>Status Di Keluarga</th>
							<th>Tempat Lahir</th>
							<th>Tanggal Lahir</th>
							<th>Jenjang Pendidikan</th>
							<th>Status Tanggungan</th>																
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_riwayat_keluarga as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<!--<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>-->
							<td><?php echo $lu->nama;?></td>
							<td><?php echo $lu->kelamin;?></td>
							<td><?php echo $lu->nmkeluarga;?></td>
							<td><?php echo $lu->namakotakab;?></td>
							<td><?php echo $lu->tgl_lahir;?></td>
							<td><?php echo $lu->nmjenjang_pendidikan;?></td>
							<td><?php echo $lu->status_tanggungan1;?></td>
							
							
							<td>
								<a href='<?php  $nik=trim($lu->nik); echo site_url("trans/riwayat_keluarga/detail/$nik/$lu->no_urut")?>' class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Detail
								</a>
								<a href='<?php  $nik=trim($lu->nik); echo site_url("trans/riwayat_keluarga/edit/$nik/$lu->no_urut")?>' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
								<a  href="<?php $nik=trim($lu->nik); echo site_url("trans/riwayat_keluarga/hps_riwayat_keluarga/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->			  
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_6">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
					<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example2" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->
							<th>Nama penyakit</th>
							<th>Periode</th>							
							<th>Keterangan</th>											
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_riwayat_kesehatan as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<!--<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>-->
							<td><?php echo $lu->nmpenyakit;?></td>
							<td><?php echo $lu->periode;?></td>
							<td><?php echo $lu->keterangan;?></td>
							
							<td>
								<!--<a href='<?php  $nik=trim($lu->nik); echo site_url("trans/riwayat_kesehatan/detail/$nik/$lu->no_urut")?>' class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Detail
								</a>-->
								<a data-bs-toggle="modal" data-bs-target="#<?php echo trim($lu->no_urut);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
								<a  href="<?php $nik=trim($lu->nik); echo site_url("trans/riwayat_kesehatan/hps_riwayat_kesehatan/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->				  
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_7">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
					<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->
							<th>Nama Perusahaan</th>
							<th>Bagian</th>
							<th>Jabatan</th>
							<th>Bulan/Tahun Masuk</th>
							<th>Bulan/Tahun Keluar</th>																						
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_riwayat_pengalaman as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<!--<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>-->
							<td><?php echo $lu->nmperusahaan;?></td>
							<td><?php echo $lu->bagian;?></td>
							<td><?php echo $lu->jabatan;?></td>
							<td><?php echo $lu->tahun_masuk1;?></td>
							<td><?php echo $lu->tahun_keluar1;?></td>
							<td>
								<a data-bs-toggle="modal" data-bs-target="#dtl<?php echo trim($lu->no_urut);?>" href='#' class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Detail
								</a>
								<a data-bs-toggle="modal" data-bs-target="#<?php echo trim($lu->no_urut);?>" href='#' class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
								<a  href="<?php $nik=trim($lu->nik); echo site_url("trans/riwayat_pengalaman/hps_riwayat_pengalaman/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
					
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_8">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->
							<th>Nama Pendidikan</th>
							<th>Nama Sekolah</th>
							<th>Kota/Kab</th>
							<th>Jurusan</th>
							<th>Program Studi</th>
							<th>Tahun Masuk</th>
							<th>Tahun Keluar</th>											
							<th>Keterangan</th>											
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_riwayat_pendidikan as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<!--<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>-->
							<td><?php echo $lu->nmpendidikan;?></td>
							<td><?php echo $lu->nmsekolah;?></td>
							<td><?php echo $lu->kotakab;?></td>
							<td><?php echo $lu->jurusan;?></td>
							<td><?php echo $lu->program_studi;?></td>
							<td><?php echo $lu->tahun_masuk;?></td>
							<td><?php echo $lu->tahun_keluar;?></td>
							<td><?php echo $lu->keterangan;?></td>
							
							<td>
								<!--<a href='<?php  $nik=trim($lu->nik); echo site_url("trans/riwayat_pendidikan/detail/$nik/$lu->no_urut")?>' class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Detail
								</a>-->
								<a data-bs-toggle="modal" data-bs-target="#<?php echo trim($lu->no_urut);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
								<a  href="<?php $nik=trim($lu->nik); echo site_url("trans/riwayat_pendidikan/hps_riwayat_pendidikan/$nik/$lu->no_urut")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
					
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_9">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 9</h3>
					
				</div>
			  </div>
			</div>				
          </div>  
		  </div>

<div class="row">
	<div class="col-sm-6">		
		<a href="<?php echo site_url('trans/karyawan');?>" class="btn btn-primary" style="margin:10px">Kembali</a>
		<a href="<?php echo site_url('trans/karyawan/edit').'/'.$dtl['nik'];?>" class="btn btn-primary" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin:10px">Ubah Data</a>
	</div>
	<div class="col-sm-6">		
		
	</div>
</div>
</div>

<!--Ganti Gambar-->
	<div class="modal fade gantigambar" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Ubah Foto</h4>
				</div>			
					<div class="row">
						<div class="col-md-12">
							<div class="box box-primary">							
                                <form class="form-horizontal" action="<?php echo site_url('trans/karyawan/up_foto');?>" method="post" enctype="multipart/form-data">						
                                    <div class="box-body">										
										<div class="col-md-12">
											<div class="form-group">												
												<!--<img src="<?php if ($dtl['image']<>'') { echo base_url('assets/img/profile/'.$dtl['image']);} else { echo base_url('assets/img/user.png');} ;?>" width="100%" height="100%" alt="User Image" >-->
											</div>
											<div id="message"></div>
											<div class="form-group">																					
												<input type="hidden" value="<?php echo $dtl['nik'];?>" name="nik">
												<p align="center"><input align="center" type="file" id="userfile" name="gambar" value="<?php echo $dtl['image'];?>"> </p>
												<p class="help-block">Upload file jpg.</p>
												<button onclick="return confirm('Ubah Foto ini?')" type="submit" class="btn btn-primary">Simpan</button>
											</div>											
										</div>										
                                    </div><!-- /.box-body -->
                                </form>
                            </div>
						</div>
					</div>				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
					
				</div>				
			</div>
		  </div>
		</div>
		<?php $x=$dtl['nik'];?>
		<script type="text/javascript">
			$(function() {
			  $('#userfile').picEdit({
				formSubmitted: function(ajax){
				  $('#message').html(ajax.response);
				  //$('#gbr').html(ajax.response);
				  $("#gbr").load('<?php echo site_url("trans/karyawan/detail/$x");?> #gbr1');
				  $('.modal .fade .gantigambar').hide();
				}
			 });
			});
		</script>



