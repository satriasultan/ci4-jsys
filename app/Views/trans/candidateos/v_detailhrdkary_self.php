<?php 
/*
	@author : jancok polll \m/
*/
error_reporting(0);
?>
<legend><?php echo $title;?></legend>
<?php echo $message;?>
<?php echo validation_errors();?>
<div class="row">
	<div class="col-sm-12">
		
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">					
				<li class="active"><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_1" data-bs-toggle="tab">Profile Karyawan</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_2" data-bs-toggle="tab">MUTASI/PROMOSI</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_3" data-bs-toggle="tab">STATUS PEGAWAI</a></li>					
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_4" data-bs-toggle="tab">Riwayat Keluarga</a></li>					
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_5" data-bs-toggle="tab">Riwayat Kesehatan</a></li>					
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_6" data-bs-toggle="tab">Riwayat Kerja</a></li>					
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_7" data-bs-toggle="tab">Riwayat Pelatihan</a></li>
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_8" data-bs-toggle="tab">Riwayat Pendidikan</a></li>				
				<li><a href="#<?php echo str_replace('.','',trim($lp['nik']));?>tab_9" data-bs-toggle="tab">BPJS</a></li>				
			</ul>
		</div>		
		<div class="tab-content">
			<div class="chart tab-pane active" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_1" style="position: relative; height: 300px;" >				
				<div class="col-sm-6">
					<div class="box box-info col-sm-6">
						<div class="box-body" style="padding:5px;">
							<div class="form-group">
								<label for="inputnip" class="col-sm-4 control-label">NIK</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="nik" name="nik" value="<?php echo $lp['nik'];?>" readonly>
								</div>						
							</div>
							<div class="form-group">
								<label for="inputnama" class="col-sm-4 control-label">Nama Lengkap</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['nmlengkap'];?>" readonly>
								</div>					
							</div>							
							<div class="form-group">
								<label for="inputdept" class="col-sm-4 control-label">Departemen</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" value="<?php echo $lp['nmdept'];?>" readonly>
								</div>						
							</div>
							<div class="form-group">
								<label for="inputsub" class="col-sm-4 control-label">Divisi</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['nmsubdept'];?>" readonly>
								</div>
								
							</div>
							<div class="form-group">
								<label for="inputjab" class="col-sm-4 control-label">Jabatan</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="jabt" name="jabt" value="<?php echo $lp['nmjabatan'];?>" readonly>
								</div>								
							</div>			
							<div class="form-group">
								<label for="darah" class="col-sm-4 control-label">Level Jabatan</label>
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" value="<?php echo $lp['lvl_jabatan'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<!-- Wilayah di hidden
							<div class="form-group">
								<label for="inputwil" class="col-sm-4 control-label">Wilayah</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="wil" name="wil" value="<?php echo $lp['areaname'];?>" readonly>
								</div>						
							</div>
							-->
							<div class="form-group">
								<label for="inputjk" class="col-sm-4 control-label">Jenis Kelamin</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="jk" name="jk" value="<?php if ($lp['kdkelamin']=='B') { echo 'Pria';} else {echo 'Wanita';}?>" readonly>						  
								</div>						
							</div>
							<div class="form-group">
								<label for="inputkota" class="col-sm-4 control-label">Kota</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="kota" name="kota" value="<?php echo $lp['kotaktp'];?>" readonly>
								</div>								
							</div>
							<div class="form-group">
								<label for="inputalamat" class="col-sm-4 control-label">Alamat</label>
								<div class="col-sm-8">
								  <textarea class="form-control input-sm" rows="3" name="alamat" id="alamat" placeholder="alamat" readonly><?php echo $lp['alamatktp'];?></textarea>
								</div>								
							</div>
							<div class="form-group">
								<label for="inputalamat" class="col-sm-4 control-label">Alamat2</label>
								<div class="col-sm-8">
								  <textarea class="form-control input-sm" rows="3" name="alamat2" id="alamat2" placeholder="alamat sesuai KTP" readonly><?php echo $lp['alamattinggal'];?></textarea>
								</div>								
							</div>							
							<div class="form-group">
								<label for="inputatasan" class="col-sm-4 control-label">Atasan Ke-1</label>
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['atasan1'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
								<div class="form-group">
								<label for="inputatasan" class="col-sm-4 control-label">Atasan Ke-2</label>
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" id="nama" name="nama" value="<?php echo $lp['atasan2'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="inputttl" class="col-sm-4 control-label">Kota Lahir</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="kotalahir" name="kotalahir" value="<?php echo $lp['kotalahir'];?>" readonly>
								</div>									
							</div>
							<div class="form-group">
								<label for="inputttl" class="col-sm-4 control-label">Tanggal Lahir</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="tgl" name="tgl" value="<?php echo date('d-m-Y', strtotime($lp['tgllahir']));?>" data-date-format="dd-mm-yyyy" readonly>
								</div>
								
							</div>							
							<div class="form-group">
								<label for="inputstatusrmh" class="col-sm-4 control-label">Kantor</label>								
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" id="kdcabang" name="kdcabang" value="<?php echo $lp['kdcabang'];?>" readonly>
								</div>														
							</div>							
							<div class="form-group">
								<label for="inputstatusnikah" class="col-sm-4 control-label">Status Pernikahan</label>
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" id="status_pernikahan" name="status_pernikahan" value="<?php echo $lp['status_pernikahan'];?>" readonly>							  							
								</div>
								
							</div>
							<div class="form-group">
								<label for="inputtelp" class="col-sm-4 control-label">No. Telp</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="nohp1" name="nohp1" value="<?php echo $lp['nohp1'];?>" readonly>
								</div>						
							</div>
							<div class="form-group">
								<label for="inputtelp" class="col-sm-4 control-label">No. Telp 2</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="nohp2" name="nohp2" value="<?php echo $lp['nohp2'];?>" readonly>
								</div>						
							</div>
							<div class="form-group">
								<label for="inputtelp" class="col-sm-4 control-label">No. Telp 3</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="telp3" name="nohp3" value="<?php echo $lp['nohp3'];?>" readonly>
								</div>						
							</div>
							<div class="form-group">
								<label for="inputtelp" class="col-sm-4 control-label">NPWP</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="npwp" name="npwp" value="<?php echo $lp['npwp'];?>" readonly>
								</div>						
							</div>
							<div class="form-group">
								<label for="inputtelp" class="col-sm-4 control-label">No Rekening</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="norek" name="norek" value="<?php echo $lp['norek'];?>" readonly>
								</div>						
							</div>
						</div>
						<div class="box-footer">
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="box box-info col-sm-6">
						<div class="box-body" style="padding:5px;">
							<div class="form-group">
								<label for="agama" class="col-sm-4 control-label">Agama</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="agama" name="agama" value="<?php echo $lp['kdagama'];?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="inputmasuk" class="col-sm-4 control-label">Masuk Kerja</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="masuk" name="masuk" value="<?php echo date('d-m-Y', strtotime($lp['tglmasukkerja']));?>" data-date-format="dd-mm-yyyy" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="inputkeluar" class="col-sm-4 control-label">Keluar Kerja</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" <?php
									$keluar = strtotime($lp['tglkeluarkerja']);
									if (empty($lp['tglkeluarkerja'])){
										echo "placeholder='Masih Bekerja'";
									} else {
										$tanggalkeluar = date('d-m-Y',$keluar);
										if ($tangalkeluar=='01-01-1970'){
											echo "placeholder='Masih Bekerja'";
										} else {
											echo "value='$tanggalkeluar'";
											echo $lp['tglkeluarkerja'];
										}		
									}
									?> id="keluar" name="keluarkrj"  data-date-format="dd-mm-yyyy" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="wn" class="col-sm-4 control-label">Kewarganegaraan</label>
								<div class="col-sm-8">
									<?php
									 $kodewn=trim($lp['stswn']);
									 switch ($kodewn){
										case 'A': echo '<input type="text" class="form-control input-sm" id="wn" name="wn" value="WNI" readonly>'; break;
										case 'B': echo '<input type="text" class="form-control input-sm" id="wn" name="wn" value="WNA" readonly>'; break;
									 }
									?>								  
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="darah" class="col-sm-4 control-label">Gol. Darah</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="darah" name="darah" value="<?php echo $lp['goldarah'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="darah" class="col-sm-4 control-label">Masa Kerja</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" value="<?php
									$kta_awal = array('years','year','mons','mon','days','day');
									$kta_akhir = array('tahun','tahun','bulan','bulan','hari','hari');
									$pesan= str_replace($kta_awal,$kta_akhir,$lp['masakerja']);
								  echo $pesan;//$lp['masakerja'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="darah" class="col-sm-4 control-label">Id Absensi</label>
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" value="<?php echo $lp['idabsen'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>							
							<div class="form-group">
								<label for="darah" class="col-sm-4 control-label">Email</label>
								<div class="col-sm-8">
									<input type="text" class="form-control input-sm" value="<?php echo $lp['email'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
						</div>
						<div class="box-footer">
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="box box-info col-sm-6">
						<div class="box-body" style="padding:5px;">
							<div class="form-group">
								<label for="ktp" class="col-sm-4 control-label">No. KTP</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="ktp" name="ktp" value="<?php echo $lp['noktp'];?>" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tktp" class="col-sm-4 control-label">Dikeluarkan di</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="tktp" name="tktp" value="<?php echo $lp['kotaktp'];?>" readonly>
								</div>
									<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="berlaku" class="col-sm-4 control-label">Berlaku</label>
								<div class="col-sm-8">
								  <input type="text" class="form-control input-sm" id="berlaku" name="berlaku" value="<?php //echo date('d-m-Y', strtotime($lp['tglmulaiktp']));?> <?php echo date('d-m-Y', strtotime($lp['tgldikeluarkan']));?>" data-date-format="dd-mm-yyyy" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="berlaku" class="col-sm-4 control-label">Foto</label>
								<div class="col-sm-8">
									<img src="<?php if ($lp['image']<>'') { echo base_url('assets/img/profile/'.$lp['image']);} else { echo base_url('assets/img/user.png');} ;?>" width="100%" height="100%" alt="User Image">
								</div>
								<a href="#" type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target=".gantigambar">Ganti Foto</a>	
								<div class="col-sm-10"></div>
							</div><br/>
						</div>
						<div class="box-footer">
						</div>
					</div>
				</div>
				<!--BUTTON-->
					<div class="form-group">
						<div class="col-sm-9" style="margin-top: 10px">											
							<a href="<?php echo site_url('trans/karyawan/edit_self').'/'.$lp['nik'];?>" class="btn btn-primary" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin:10px">EDIT KONTAK PROFILE</a>
						</div>
						<div class="col-sm-10"></div>
					</div>
			<!--	</div><!-- ./col -->
				
			</div><!-- ./tab 1-->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_2">
				<div class="row">
					<div>	
					<!--a href="#" type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#inputmutasi">Input Mutasi</a-->						
					<br><br>
					</div>
				
				  <div class="box-body table-responsive" style='overflow-x:scroll;'>
					<table id="example1"  class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
						<tr>
							<th>No.</th>
							<th>Kode</th>
							<th>NIK</th>
							<th>Departement Lama</th>																								
							<th>Subdepartement Lama</th>																								
							<th>Jabatan Lama</th>																																															
							<th>Atasan Lama</th>
							<th>Departement Baru</th>																								
							<th>Subdepartement Baru</th>																								
							<th>Jabatan Baru</th>																								
							<!--th>Level Baru</th-->																								
							<th>Atasan Baru</th>
							<th>No Dok SK</th>
							<th>Tgl SK</th>
							<th>Tgl Memo</th>
							<th>Tgl Efektif</th>
							<th>Keterangan</th>							
							<th>Action</th>						
						</tr>
					</thead>					
					<tbody>
					
						<?php $no=1; foreach ($list_mutasi as $lm) {?>
						<tr>
							<td><?php echo $no; $no++;?></td>
							<td><?php echo $lm->nodokumen;?></td>
							<td><?php echo $lm->nik;?></td>
							<td><?php echo $lm->olddept;?></td>
							<td><?php echo $lm->oldsubdept;?></td>							
							<td><?php echo $lm->oldjabatan;?></td>							
							<!--td><?php echo $lm->oldlevel;?></td-->							
							<td><?php echo $lm->oldatasan;?></td>							
							<td><?php echo $lm->newdept;?></td>
							<td><?php echo $lm->newsubdept;?></td>							
							<td><?php echo $lm->newjabatan;?></td>							
							<!--td><?php echo $lm->newlevel;?></td-->							
							<td><?php echo $lm->newatasan;?></td>							
							<td><?php echo $lm->nodoksk;?></td>							
							<td><?php echo $lm->tglsk;?></td>							
							<td><?php echo $lm->tglmemo;?></td>							
							<td><?php echo $lm->tglefektif;?></td>							
							<td><?php echo $lm->ket;?></td>							
							<td>
							<?php if (trim($lm->status)=='I'){?>
								<a class="btn btn-sm btn-success" href="<?php echo site_url('trans/karyawan/approvemutasi/').'/'.trim($lm->nodokumen).'/'.trim($lm->nik);?>" title="Detail"><i class="glyphicon glyphicon-pencil"></i> Setujui</a>
								<a class="btn btn-sm btn-danger" href="<?php echo site_url('trans/karyawan/deletemutasi').'/'.trim($lm->nodokumen).'/'.trim($lm->nik);?>" title="Hapus" onclick="return_confirm('<?php echo trim($lm->nodokumen);?>')"><i class="glyphicon glyphicon-trash"></i> Batal/Hapus</a>
							<?php } else { echo 'DISETUJUI';} ?>
							
							</td>							
						</tr>
						<?php } ?>
					</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
											
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_4">
				<div class="row">
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>																				
								<td width="3%">No</td>												
								<!--td width="5%">Action</td-->												
								<td>Nama</td>												
								<td>Status Di keluarga</td>
								<td>Tanggungan</td>
								<td>Pekerjaan</td>								
							</tr>
						</thead>
						<tbody>
							<?php
							if(empty($list_keluarga))
								{
									echo "<tr><td colspan=\"6\">Data tidak tersedia</td></tr>";
								} else {
								$no=1;	
							foreach ($list_keluarga as $listkel){?>
							<tr>									
								<td><?php echo $no;?></td>
								<!--td><a href="#" data-bs-toggle="modal" data-bs-target=".keluarga<?php echo str_replace('.','',trim($listkel->nir)).trim($listkel->nomor);?>" data-placement="top" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
								|<a href="<?php echo site_url('hrd/hrd/hapus_keluarga/').'/'.trim($listkel->nir).'/'.$listkel->nomor;?>" data-bs-toggle="tooltip" data-placement="top" title="Hapus" onclick="return confirm('Hapus data Keluarga ini?')"><i class="glyphicon glyphicon-trash"></i></a></td-->								
								<td><?php echo $listkel->nama;?></td>
								<td><?php switch ($listkel->kdstklg) {
									case 'A': echo 'Bapak'; break;
									case 'B': echo 'Ibu';break;
									case 'C': echo 'Kakak';break;
									case 'D': echo 'Adik';break;
									case 'E': echo 'Anak';break;
									case 'F': echo 'Suami';break;
									case 'G': echo 'Istri';break;							
								};								
									?></td>
								<td><?php switch ($listkel->tanggungan) {
									case 'Y': echo 'Menjadi Tanggungan'; break;
									case 'N': echo 'Bukan Tanggungan'; break;
								}?>
								</td>								
								<td><?php echo $listkel->pekerjaan;?></td>												
							</tr>
							<?php $no++; }}?>
						</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
						<a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target=".keluarga">Input Keluarga</a>						
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>			
			<!--end of Keluarga-->
			
			<!--Status KErja STATUS KEPEGAWAIAN-->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_3">
				<div class="row">
					<div>	
					<!--a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#inputstskerja">Input Status Kerja</a-->						
					<br><br>
					</div>
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->
							<th>No Dokumen</th>
							<th>No SK</th>
							<th>Nama Status</th>							
							<th>Tanggal Mulai</th>							
							<th>Tanggal Berakhir</th>							
							<th>Keterangan</th>											
											
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_stspeg as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<td><?php echo $lu->nodok;?></td>
							<td><?php echo $lu->nosk;?></td>
							<td><?php echo $lu->nmkepegawaian;?></td>
							<td><?php echo $lu->tgl_mulai1;?></td>
							<td><?php echo $lu->tgl_selesai1;?></td>
							<td><?php echo $lu->keterangan;?></td>
							
							<!--td>
										
								<a data-bs-toggle="modal" data-bs-target="#<?php echo trim($lu->nodok);?>" href='#'  class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
								
								
								<a  href="<?php $nik=trim($lu->nik); echo site_url("trans/stspeg/hps_stspeg/$nik/$lu->nodok")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
								
							</td-->
						</tr>
						<?php endforeach;?>
					</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>
			<!--end off Statu KErja-->
			
			<!---View UPdate Junis 22-08-2015-->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_8">
				<div class="row">
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
							<th>No.</th>
							<th>Action</th>
							<th>Grade</th>
							<th>Nama Sekolah</th>
							<th>Kota</th>
							<th>Jurusan</th>
							<th>Tahun Masuk</th>
							<th>Tahun Lulus</th>
							<th>Nilai/IPK</th>
							<th>Keterangan</th>
						</tr>
						</thead>
						<tbody>
							<?php
							if(empty($list_pendidikan))
								{
									echo "<tr><td colspan=\"10\">Data tidak tersedia</td></tr>";
								} else {
								$no=1;	
							foreach ($list_pendidikan as $didik){?>
							<tr>									
								<td><?php echo $no;?></td>
								<!--td><a href="#" data-bs-toggle="modal" data-bs-target=".pendidikan<?php echo str_replace('.','',trim($didik->nip)).trim($didik->nomor);?>" data-placement="top" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
								|<a href="<?php echo site_url('hrd/hrd/hapus_pen/').'/'.trim($didik->nip).'/'.$didik->nomor;?>" data-bs-toggle="tooltip" data-placement="top" title="Hapus" onclick="return confirm('Hapus data pendidikan ini?')"><i class="glyphicon glyphicon-trash"></i></a></td-->								
								<td><?php echo $didik->gradepen;?></td>
								<td><?php echo $didik->nmsekolah;?></td>
								<td><?php echo $didik->kota;?></td>
								<td><?php echo $didik->jurusan;?></td>
								<td><?php echo $didik->periodeaw;?></td>
								<td><?php echo $didik->periodeak;?></td>
								<td><?php echo $didik->nilai;?></td>
								<td><?php echo $didik->keterangan;?></td>
							</tr>
							<?php $no++; }}?>
						</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<!--a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target=".pendidikan">Input Pendidikan</a-->						
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>
			<!--End View Pendidikan-->
			
			<!---TAB BPJS KARYAWAN -->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_9">
				<div class="row">
				<div class="form-group">
					<!--a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#inputbpjs">Input BPJS</a-->						
				<br><br>
				<div>										
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
						<tr>
							<th>No.</th>
							<th>Kode Bpjs</th>
							<th>Kode Komponen Bpjs</th>
							<th>Kode Faskes Utama</th>
							<th>Kode Faskes Tambahan</th>
							<th>Id Bpjs</th>
							<th>Kelas</th>
							<th>Tanggal Berlaku</th>
							<th>Keterangan</th>																	
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_bpjskaryawan as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<td><?php echo $lu->kode_bpjs;?></td>
							<td><?php echo $lu->namakomponen;?></td>
							<td><?php echo $lu->namafaskes;?></td>
							<td><?php echo $lu->namafaskes2;?></td>
							<td><?php echo $lu->id_bpjs;?></td>
							<td><?php echo $lu->uraian;?></td>
							<td><?php echo $lu->tgl_berlaku;?></td>
							<td><?php echo $lu->keterangan;?></td>
							<td>
							
								<!--a data-bs-toggle="modal" data-bs-target="#<?php echo trim($lu->id_bpjs);?>" href='#'  class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
							
							
								<a  href="<?php $nik=trim($lu->nik); echo  site_url("trans/karyawan/hps_bpjs/$nik/$lu->id_bpjs")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a-->
							
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>
			<!--End TAB BPJS-->
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			<!--Insert Pendidikan Febri 16-04-2015-->
			<div class="modal fade pendidikan" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Input Pendidikan</h4>
					</div>
					<div class="modal-body">					
						<div class="row">
						<form action="<?php echo site_url('hrd/hrd/simpan_pen');?>" method="post">
						  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">
						  <div class="col-sm-12">
									<div class="form-group">
										<label for="grade" class="col-sm-4 control-label">Grade Pendidikan</label>
										<div class="col-sm-6">
										  <select class='form-control input-sm' name="kddidik" id="kddidik">		
											<?php
												if(empty($qgrade))
												{
													echo "<tr><td colspan=\"10\">Data tidak tersedia</td></tr>";
												} else {
													foreach($qgrade as $grade)
												{
												?>
												<option value="<?php echo $grade->kdpendidikan; ?>"><?php echo $grade->desc_pendidikan; ?></option>
												<?php }} ?>
										  </select>
										</div>
										<div class="col-sm-10"></div>
									</div>
									<div class="form-group">
										<label for="inputsklah" class="col-sm-4 control-label">Nama Sekolah</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" id="sklah" name="sklah">
										</div>
										
									</div>
									<div class="form-group">
										<label for="inputkota" class="col-sm-4 control-label">Kota</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" id="kota" name="kota">
										</div>
										
									</div>
									<div class="form-group">
										<label for="inputjur" class="col-sm-4 control-label">Jurusan</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" id="jur" name="jur">
										</div>
										
									</div>
									<div class="form-group">
										<label for="inputtm" class="col-sm-4 control-label">Tahun Masuk</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" id="tm" name="tm">
										</div>
										
									</div>
									<div class="form-group">
										<label for="inputtl" class="col-sm-4 control-label">Tahun Lulus</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" id="tl" name="tl">
										</div>
										
									</div>
									<div class="form-group">
										<label for="inputnilai" class="col-sm-4 control-label">Nilai/IPK</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" name="nilai">
										</div>
										
									</div>
									<div class="form-group">
										<label for="inputket" class="col-sm-4 control-label">Keterangan</label>
										<div class="col-sm-8">
										  <input type="text" class="form-control input-sm" id="ket" name="ket">
										</div>
										
									</div>
						  </div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
						<button onclick="return confirm('Simpan data Pendidikan ini?')" type="submit" class="btn btn-primary">Simpan</button>
					</div>
					</form>
				</div>
			</div>
			</div>
			<!--End Insert Pendidikan-->
			
			<!--View & Edit Pendidikan Febri 16-04-2015-->
			<?php foreach ($list_pendidikan as $row){ ?>
			<div class="modal fade pendidikan<?php echo str_replace('.','',trim($row->nip)).trim($row->nomor);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="myModalLabel">Data Pendidikan</h4>
						</div>
						<div class="modal-body">
							<div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#pendidikan<?php echo str_replace('.','',trim($row->nip)).trim($row->nomor);?>tab_1" data-bs-toggle="tab">View Data</a></li>
									<li><a href="#pendidikan<?php echo str_replace('.','',trim($row->nip)).trim($row->nomor);?>tab_2" data-bs-toggle="tab">Edit Data</a></li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="pendidikan<?php echo str_replace('.','',trim($row->nip)).trim($row->nomor);?>tab_1" style="position: relative; height: 300px;">
										<div class="form-group">
										<label for="inputsklah" class="col-sm-4 control-label">Nama Sekolah</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="sklah" name="sklah" value="<?php echo $row->nmsekolah;?>" readonly>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputkota" class="col-sm-4 control-label">Kota</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="kota" name="kota" value="<?php echo $row->kota;?>" readonly>
											</div>
											<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputjur" class="col-sm-4 control-label">Jurusan</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="jur" name="jur" value="<?php echo trim($row->jurusan);?>" readonly>
											</div>
											<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputtm" class="col-sm-4 control-label">Tahun Masuk</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="tm" name="tm" value="<?php echo $row->periodeaw;?>" readonly>
											</div>
										<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputtl" class="col-sm-4 control-label">Tahun Lulus</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="tl" name="tl" value="<?php echo $row->periodeak;?>" readonly>
											</div>
										<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputnilai" class="col-sm-4 control-label">Nilai/IPK</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="nilai" name="nilai" value="<?php echo $row->nilai;?>" readonly>
											</div>
										<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputket" class="col-sm-4 control-label">Keterangan</label>
											<div class="col-sm-6">
											  <input type="text" class="form-control input-sm" id="ket" name="ket" value="<?php echo $row->keterangan;?>" readonly>
											</div>
										<div class="col-sm-10"></div>	
										</div>
									</div>
                                    <div class="chart tab-pane" id="pendidikan<?php echo str_replace('.','',trim($row->nip)).trim($row->nomor);?>tab_2" style="position: relative; height: 300px;">
										<form action="<?php echo site_url('hrd/hrd/edit_pen');?>" method="post">
										<input type="hidden" value="<?php echo $row->nip;?>" name="nip">
										<input type="hidden" value="<?php echo $row->nomor;?>" name="nomor">
										<div class="form-group">
											<label for="grade" class="col-sm-4 control-label">Grade Pendidikan</label>
											<div class="col-sm-6">
											  <select class='form-control input-sm' name="kddidik" id="kddidik">		
												<?php
													if(empty($qgrade))
													{
														echo "<tr><td colspan=\"10\">Data tidak tersedia</td></tr>";
													} else {
														foreach($qgrade as $grade)
													{
													?>
													<option value="<?php echo $grade->kdpendidikan; ?>" 
														<?php if (trim($row->kdpendidikan)==trim($grade->kdpendidikan)) { echo 'selected';} ?>
													><?php echo trim($grade->desc_pendidikan); ?></option>
													<?php }} ?>
											  </select>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputsklah" class="col-sm-4 control-label">Nama Sekolah</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="sklah" name="sklah" value="<?php echo $row->nmsekolah;?>" required>
												</div>
												<div class="col-sm-10"></div>
											</div>
										<div class="form-group">
											<label for="inputkota" class="col-sm-4 control-label">Kota</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="kota" name="kota" value="<?php echo $row->kota;?>" required>
												</div>
											<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputjur" class="col-sm-4 control-label">Jurusan</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="jur" name="jur" value="<?php echo trim($row->jurusan);?>" required>
												</div>
												<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputtm" class="col-sm-4 control-label">Tahun Masuk</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="tm" name="tm" value="<?php echo $row->periodeaw;?>" required>
												</div>
												<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputtl" class="col-sm-4 control-label">Tahun Lulus</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="tl" name="tl" value="<?php echo $row->periodeak;?>" required>
												</div>
												<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputnilai" class="col-sm-4 control-label">Nilai/IPK</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="nilai" name="nilai" value="<?php echo $row->nilai;?>" required>
												</div>
												<div class="col-sm-10"></div>	
										</div>
										<div class="form-group">
											<label for="inputket" class="col-sm-4 control-label">Keterangan</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="ket" name="ket" value="<?php echo $row->keterangan;?>">
												</div>
												<div class="col-sm-10"></div>	
										</div>
										<button type="submit" onclick="return confirm('Update data pendidikan ini?')" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i> Update</button>
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
			</div>
			<?php }?>
			<!--End View & Edit Pendidikan-->
			
			<!--Kesehatan-->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_5">
				<div class="row">
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>																				
								<td width="3%">No</td>												
																				
								<td width="10%">Tahun Sakit</td>												
								<td>Sakit</td>																
							</tr>
						</thead>
						<tbody>
							<?php
							if(empty($list_kesehatan))
								{
									echo "<tr><td colspan=\"6\">Data tidak tersedia</td></tr>";
								} else {
								$no=1;	
							foreach ($list_kesehatan as $listkes){?>
							<tr>									
								<td><?php echo $no;?></td>
								<!--td><a href="#" data-bs-toggle="modal" data-bs-target=".kesehatan<?php echo str_replace('.','',trim($listkes->nip)).trim($listkes->nomor);?>" data-placement="top" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
								|<a href="<?php echo site_url('hrd/hrd/hapus_kesehatan/').'/'.trim($listkes->nip).'/'.$listkes->nomor;?>" data-bs-toggle="tooltip" data-placement="top" title="Hapus" onclick="return confirm('Hapus data Riwayat Kesehatan ini?')"><i class="glyphicon glyphicon-trash"></i></a></td-->								
								<td><?php echo $listkes->thnsakit;?></td>
								<td><?php echo $listkes->desc_sakit;?></td>											
							</tr>
							<?php $no++; }}?>
						</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<!--a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target=".kesehatan">Input Kesehatan</a-->						
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>			
			<!--Riwayat Kerja-->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_6">
				<div class="row">
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>																				
								<td width="3%">No</td>												
																			
								<td width="5%">Tahun Masuk</td>												
								<td width="5%">Tahun Keluar</td>												
								<td>Nama Perusahaan</td>												
								<td>Alamat</td>												
								<td>Jabatan</td>												
								<td>Gaji</td>												
								<td>Keterangan</td>																
							</tr>
						</thead>
						<tbody>
							<?php
							if(empty($list_kerja))
								{
									echo "<tr><td colspan=\"8\">Data tidak tersedia</td></tr>";
								} else {
								$no=1;	
							foreach ($list_kerja as $listker){?>
							<tr>									
								<td><?php echo $no;?></td>
								<!--td><a href="#" data-bs-toggle="modal" data-bs-target=".ekerja<?php echo str_replace('.','',trim($listker->nir)).trim($listker->pglmke);?>" data-placement="top" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
								|<a href="<?php echo site_url('hrd/hrd/hapus_pglmkerja/').'/'.trim($listker->nir).'/'.$listker->pglmke;?>" data-bs-toggle="tooltip" data-placement="top" title="Hapus" onclick="return confirm('Hapus data Pengalaman Kerja ini?')"><i class="glyphicon glyphicon-trash"></i></a></td-->								
								<td><?php echo $listker->tahunmasuk;?></td>
								<td><?php echo $listker->tahunkeluar;?></td>											
								<td><?php echo $listker->nmperusahaan;?></td>											
								<td><?php echo $listker->alamat;?></td>											
								<td><?php echo $listker->jabatan;?></td>											
								<td><?php echo $listker->gaji;?></td>											
								<td><?php echo $listker->keterangan;?></td>											
							</tr>
							<?php $no++; }}?>
						</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<!--a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target=".inputkerja">Input Riwayat Kerja</a--->						
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>
			<!--Pelatihan-->
			<div class="tab-pane" id="<?php echo str_replace('.','',trim($lp['nik']));?>tab_7">
				<div class="row">
				  <div class="col-sm-12">
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>																				
								<td width="3%">No</td>												
								<td width="3%">Action</td>												
								<td width="10%">Tanggal Pelatihan</td>												
								<td width="10%">Lama Pelatihan</td>												
								<td width="15%">Nama Pelatihan</td>												
								<td width="15%">Tempat</td>																				
								<td width="15%">Trainer</td>																				
								<td>Keterangan</td>																
							</tr>
						</thead>
						<tbody>
							<?php
							if(empty($list_pelatihan))
								{
									echo "<tr><td colspan=\"8\">Data tidak tersedia</td></tr>";
								} else {
								$no=1;	
							foreach ($list_pelatihan as $listpel){?>
							<tr>									
								<td><?php echo $no;?></td>
								<td><a href="#" data-bs-toggle="modal" data-bs-target=".epelatihan<?php echo str_replace('.','',trim($listpel->nip)).trim($listpel->kdpelatihan);?>" data-placement="top" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
								|<a href="<?php echo site_url('hrd/hrd/hapus_pelatihan/').'/'.trim($listpel->nip).':'.trim($listpel->kdpelatihan);?>" data-bs-toggle="tooltip" data-placement="top" title="Hapus" onclick="return confirm('Hapus data Pelatihan ini?')"><i class="glyphicon glyphicon-trash"></i></a></td>								
								<td><?php echo $listpel->tglpelatihan;?></td>
								<td><?php echo $listpel->lamapelatihan;?></td>
								
								<td><?php echo $listpel->nmpelatihan;?></td>											
								<td><?php echo $listpel->tempatpelatihan;?></td>											
								<td><?php echo $listpel->trainer;?></td>											
								<td><?php echo $listpel->ketpelatihan;?></td>																			
							</tr>
							<?php $no++; }}?>
						</tbody>
					</table>
				  </div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<!--a href="#" type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target=".inputpelatihan">Input Pelatihan</a--->						
						<div class="col-sm-9" style="margin-top: 10px">											
						</div>
						<div class="col-sm-10"></div>
					</div>
					</div>
				</div>
			</div>
	</div>
	<div class="col-sm-12">
	<a href="<?php echo site_url("trans/karyawan/karyawan_self/");?>" class="btn btn-default">Kembali</a>
		<!--button type="button" class="btn btn-default" onclick="history.back();">Kembali</button-->
	</div>
</div>	




	
	<!--Modal View dan Edit Keluarga-->
		<?php foreach ($list_keluarga as $listkel){ ?>
				<div class="modal fade keluarga<?php echo str_replace('.','',trim($listkel->nir)).trim($listkel->nomor);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="myModalLabel">Data Keluarga</h4>
						</div>
						<div class="modal-body">
							<div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#keluarga<?php echo str_replace('.','',trim($listkel->nir)).trim($listkel->nomor);?>tab_1" data-bs-toggle="tab">View Data</a></li>
									<li><a href="#keluarga<?php echo str_replace('.','',trim($listkel->nir)).trim($listkel->nomor);?>tab_2" data-bs-toggle="tab">Edit Data</a></li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="keluarga<?php echo str_replace('.','',trim($listkel->nir)).trim($listkel->nomor);?>tab_1" style="position: relative; height: 300px;">
										<div class="form-group">
											<label for="tujuan" class="col-sm-4 control-label">Status Di Keluarga</label>
											<div class="col-sm-6">												
												<input type="text" value="<?php switch($listkel->kdstklg) {
														case 'A': echo 'Bapak'; break; 
														case 'B': echo 'Ibu'; break; 
														case 'C': echo 'Kakak'; break; 
														case 'D': echo 'Adik'; break; 
														case 'E': echo 'Anak'; break; 
														case 'F': echo 'Suami'; break; 
														case 'G': echo 'Istri'; break; 
													}?>" class="form-control input-sm" readonly>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="tujuan" class="col-sm-4 control-label">Nama</label>
											<div class="col-sm-6">									  
												<input type="text" class="form-control input-sm" name="nama" id="nama" value="<?php echo $listkel->nama;?>" readonly>									  
											</div>
											<div class="col-sm-10"></div>
										</div>		
										<div class="form-group">
											<label for="inputjk" class="col-sm-4 control-label">Jenis Kelamin</label>
											<div class="col-sm-6">
												<input type="text" class="form-control input-sm" name="nama" id="nama" value="<?php switch ($listkel->kdkelamin) { case 'B': echo 'PRIA'; break; case 'A': echo 'WANITA'; break;};?>" readonly>									  
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Tempat Lahir</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="tempat" name="tempatlhr" value="<?php echo $listkel->tempatlahir;?>" readonly>
												</div>
												<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Tanggal Lahir</label>
												<div class="col-sm-6">
												  <input type="date" class="form-control input-sm" id="tanggal" name="tanggallhr" value="<?php echo $listkel->tgllahir;?>" required data-date-format="dd-mm-yyyy" readonly>
												</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Pendidikan</label>
												<div class="col-sm-6">
													<input class="form-control input-sm" value="<?php switch ($listkel->kdpendidikan){
														case 'A': echo 'SD/MI'; break;														
														case 'B': echo 'SMP/MTS'; break;														
														case 'C': echo 'SMA/MA'; break;														
														case 'D': echo 'Diploma'; break;														
														case 'E': echo 'Sarjana'; break;														
														case 'F': echo 'Magister'; break;														
													};?>" readonly>												  
												</div>
												<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Pekerjaan</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="pekerjaan" name="pekerjaan" value="<?php echo $listkel->pekerjaan;?>" readonly>
												</div>
												<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="ketm" class="col-sm-4 control-label">Masih Hidup</label>
											<div class="col-sm-6">
												<input class="form-control input-sm" value="<?php switch ($listkel->sthidup){
														case 'A': echo 'Hidup'; break;														
														case 'B': echo 'Meninggal'; break;																												
													}?>" readonly>												  
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="ketm" class="col-sm-4 control-label">Tanggungan</label>
											<div class="col-sm-6">
												<input class="form-control input-sm" value="<?php switch ($listkel->sthidup){
														case 'Y': echo 'Menjadi Tanggungan'; break;														
														case 'N': echo 'Bukan Tanggungan'; break;																												
													}?>" readonly>												  
											</div>
											<div class="col-sm-10"></div>
										</div>
									</div>
                                    <div class="chart tab-pane" id="keluarga<?php echo str_replace('.','',trim($listkel->nir)).trim($listkel->nomor);?>tab_2" style="position: relative; height: 300px;">
										<form action="<?php echo site_url('hrd/hrd/edit_keluarga');?>" method="post">
										<input type="hidden" value="<?php echo $listkel->nir;?>" name="nip">
										<input type="hidden" value="<?php echo $listkel->nomor;?>" name="nourut">
										<div class="form-group">
											<label for="tujuan" class="col-sm-4 control-label">Status Di Keluarga</label>
											<div class="col-sm-6">
											  <select class='form-control input-sm' name="stskel" id="stskel">
												<?php switch ($listkel->kdstklg){												
													case 'A': echo '<option selected value="A">Bapak</option>';
															echo	'<option value="B">Ibu</option>';										
															echo	'<option value="C">Kakak</option>';										
															echo	'<option value="D">Adik</option>';										
															echo	'<option value="E">Anak</option>';										
															echo	'<option value="F">Suami</option>';										
															echo	'<option value="G">Istri</option>'; break;
													case 'B': echo '<option value="A">Bapak</option>';
															echo	'<option selected value="B">Ibu</option>';										
															echo	'<option value="C">Kakak</option>';										
															echo	'<option value="D">Adik</option>';										
															echo	'<option value="E">Anak</option>';										
															echo	'<option value="F">Suami</option>';										
															echo	'<option value="G">Istri</option>'; break;
													case 'C': echo '<option value="A">Bapak</option>';
															echo	'<option value="B">Ibu</option>';										
															echo	'<option selected value="C">Kakak</option>';										
															echo	'<option value="D">Adik</option>';										
															echo	'<option value="E">Anak</option>';										
															echo	'<option value="F">Suami</option>';										
															echo	'<option value="G">Istri</option>'; break;
													case 'D': echo '<option value="A">Bapak</option>';
															echo	'<option value="B">Ibu</option>';										
															echo	'<option value="C">Kakak</option>';										
															echo	'<option selected value="D">Adik</option>';										
															echo	'<option value="E">Anak</option>';										
															echo	'<option value="F">Suami</option>';										
															echo	'<option value="G">Istri</option>'; break;
													case 'E': echo '<option value="A">Bapak</option>';
															echo	'<option value="B">Ibu</option>';										
															echo	'<option value="C">Kakak</option>';										
															echo	'<option value="D">Adik</option>';										
															echo	'<option selected value="E">Anak</option>';										
															echo	'<option value="F">Suami</option>';										
															echo	'<option value="G">Istri</option>'; break;
													case 'F': echo '<option value="A">Bapak</option>';
															echo	'<option value="B">Ibu</option>';										
															echo	'<option value="C">Kakak</option>';										
															echo	'<option value="D">Adik</option>';										
															echo	'<option value="E">Anak</option>';										
															echo	'<option selected value="F">Suami</option>';										
															echo	'<option value="G">Istri</option>'; break;
													case 'G': echo '<option value="A">Bapak</option>';
															echo	'<option value="B">Ibu</option>';										
															echo	'<option value="C">Kakak</option>';										
															echo	'<option value="D">Adik</option>';										
															echo	'<option value="E">Anak</option>';										
															echo	'<option value="F">Suami</option>';										
															echo	'<option selected value="G">Istri</option>'; break;
													} ?>
																						
											  </select>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="tujuan" class="col-sm-4 control-label">Nama</label>
											<div class="col-sm-6">									  
												<input type="text" class="form-control input-sm" value="<?php echo $listkel->nama;?>" name="nama" id="nama">									  
											</div>
											<div class="col-sm-10"></div>
										</div>		
										<div class="form-group">
											<label for="inputjk" class="col-sm-4 control-label">Jenis Kelamin</label>
											<div class="col-sm-6">
												<select class="form-control input-sm" name="jk" id="jk" required>
													<?php if ($listkel->kdkelamin=='A') { 
														echo '<option value="B">Pria</option>';
														echo '<option selected value="A">Wanita</option>';
													} else {
														echo '<option selected value="B">Pria</option>';
														echo '<option value="A">Wanita</option>';
													}?>
												</select>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Tempat Lahir</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="tempat" value="<?php echo $listkel->tempatlahir; ?>" name="tempatlhr" required>
												</div>
												<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Tanggal Lahir</label>
												<div class="col-sm-6">
												  <input type="date" class="form-control input-sm" id="<?php echo 'kelu'.trim($listkel->nir).trim($listkel->nomor);?>" name="tanggallhr" value="<?php echo $listkel->tgllahir;?>" required data-date-format="dd-mm-yyyy" required>
												</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Pendidikan</label>
												<div class="col-sm-6">
												  <select class="form-control input-sm" id="pendidikan" name="pendidikan" required>
													<?php switch ($listkel->kdpendidikan){
														case 'A' : 	echo '<option selected value="A">SD/MI</option>';
																	echo '<option value="B">SMP/MTS</option>';
																	echo '<option value="C">SMA/MA</option>';
																	echo '<option value="D">Diploma</option>';
																	echo '<option value="E">Sarjana</option>';
																	echo '<option value="F">Magister</option>'; break;
														case 'B' : 	echo '<option value="A">SD/MI</option>';
																	echo '<option selected value="B">SMP/MTS</option>';
																	echo '<option value="C">SMA/MA</option>';
																	echo '<option value="D">Diploma</option>';
																	echo '<option value="E">Sarjana</option>';
																	echo '<option value="F">Magister</option>'; break;
														case 'C' : 	echo '<option value="A">SD/MI</option>';
																	echo '<option value="B">SMP/MTS</option>';
																	echo '<option selected value="C">SMA/MA</option>';
																	echo '<option value="D">Diploma</option>';
																	echo '<option value="E">Sarjana</option>';
																	echo '<option value="F">Magister</option>'; break;
														case 'D' : 	echo '<option value="A">SD/MI</option>';
																	echo '<option value="B">SMP/MTS</option>';
																	echo '<option value="C">SMA/MA</option>';
																	echo '<option selected value="D">Diploma</option>';
																	echo '<option value="E">Sarjana</option>';
																	echo '<option value="F">Magister</option>'; break;
														case 'E' : 	echo '<option value="A">SD/MI</option>';
																	echo '<option value="B">SMP/MTS</option>';
																	echo '<option value="C">SMA/MA</option>';
																	echo '<option value="D">Diploma</option>';
																	echo '<option selected value="E">Sarjana</option>';
																	echo '<option value="F">Magister</option>'; break;
														case 'F' : 	echo '<option value="A">SD/MI</option>';
																	echo '<option value="B">SMP/MTS</option>';
																	echo '<option value="C">SMA/MA</option>';
																	echo '<option value="D">Diploma</option>';
																	echo '<option value="E">Sarjana</option>';
																	echo '<option selected value="F">Magister</option>'; break;														
													}?>													
												  </select>
												</div>
												<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="inputttl" class="col-sm-4 control-label">Pekerjaan</label>
												<div class="col-sm-6">
												  <input type="text" class="form-control input-sm" id="pekerjaan" value="<?php echo $listkel->pekerjaan;?>" name="pekerjaan" required>
												</div>
												<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="ketm" class="col-sm-4 control-label">Masih Hidup</label>
											<div class="col-sm-6">
												<select class="form-control input-sm" name="stshdp">
												<?php if ($listkel->sthidup=='A') {
													echo '<option selected value="A">Hidup</option>';
													echo '<option value="B">Meninggal</option>';
												} else {
													echo '<option value="A">Hidup</option>';
													echo '<option selected value="B">Meninggal</option>';													
												}?>
													
												</select>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<div class="form-group">
											<label for="ketm" class="col-sm-4 control-label">Tanggungan</label>
											<div class="col-sm-6">
												<select class="form-control input-sm" name="tanggungan">
												<?php if ($listkel->tanggungan=='Y') {
													echo '<option selected value="Y">Ya</option>';
													echo '<option value="N">Tidak</option>';
												} else {
													echo '<option value="Y">Ya</option>';
													echo '<option selected value="N">Tidak</option>';													
												}?>
													
												</select>
											</div>
											<div class="col-sm-10"></div>
										</div>
										<button type="submit" onclick="return confirm('Simpan data Keluarga ini?')" class="btn btn-primary">Simpan</button>
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
			</div>
		<?php }?>
	<!--end of edit dan view keluarga-->
	
	
	<!--Edit dan View Kesehatan-->
	<?php foreach ($list_kesehatan as $listkes){?>
	<div class="modal fade kesehatan<?php echo str_replace('.','',trim($lp['nik'])).trim($listkes->nomor);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">EDIT Riwayat Kesehatan</h4>
			</div>
			<div class="modal-body">
				<div class="nav-tabs-custom">
					<!-- Tabs within a box -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#kesehatan<?php echo str_replace('.','',trim($lp['nik'])).trim($listkes->nomor);?>tab_1" data-bs-toggle="tab">View Data</a></li>
						<li><a href="#kesehatan<?php echo str_replace('.','',trim($lp['nik'])).trim($listkes->nomor);?>tab_2" data-bs-toggle="tab">Edit Data</a></li>
					</ul>
					<div class="tab-content no-padding">
						<!-- Morris chart - Sales -->
						<div class="chart tab-pane active" id="kesehatan<?php echo str_replace('.','',trim($lp['nik'])).trim($listkes->nomor);?>tab_1" style="position: relative; height: 100px;">																																									  																				
							  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">							  						
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Tahun</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo $listkes->thnsakit;?>" disabled>										
									</div>
									<div class="col-sm-10"></div>
								</div>						
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Sakit</label>
									<div class="col-sm-6">
									  <textarea class="form-control input-sm" id="sakit" name="sakit" disabled><?php echo $listkes->desc_sakit;?></textarea>
									</div>
									<div class="col-sm-10"></div>
								</div>							  													  								
						</div>
						<div class="chart tab-pane" id="kesehatan<?php echo str_replace('.','',trim($lp['nik'])).trim($listkes->nomor);?>tab_2" style="position: relative; height: 100px;">							
							<form action="<?php echo site_url('hrd/hrd/edit_kesehatan');?>" method="post">
							  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">							  							
							  <input type="hidden" name="nomor" value="<?php echo $listkes->nomor;?>">							  							
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Tahun</label>
									<div class="col-sm-6">
										<select class='form-control input-sm' name="tahun" id="tahun">											
											<option value='<?php echo $listkes->thnsakit; ?>' selected><?php echo $listkes->thnsakit; ?></option>
											<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
											<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
											<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>
											<option value='<?php $tgl=date('Y')-3; echo $tgl; ?>'><?php $tgl=date('Y')-3; echo $tgl; ?></option>
											<option value='<?php $tgl=date('Y')-4; echo $tgl; ?>'><?php $tgl=date('Y')-4; echo $tgl; ?></option>
											<option value='<?php $tgl=date('Y')-5; echo $tgl; ?>'><?php $tgl=date('Y')-5; echo $tgl; ?></option>
										</select>
									</div>
									<div class="col-sm-10"></div>
								</div>						
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Sakit</label>
									<div class="col-sm-6">
									  <textarea class="form-control input-sm" id="sakit" name="sakit"><?php echo $listkes->desc_sakit;?></textarea>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<button onclick="return confirm('Simpan data Riwayat Kesehatan ini?')" type="submit" class="btn btn-primary">Simpan</button>
							</form>
						</div>
					</div>
				</div>							
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
			</div>						
		</div>
	  </div>
	</div>
	<?php }?>
	<!--End Edit dan View Kesehatan-->	
	
	<!--Edit dan View Pengalaman Kerja-->
	<?php foreach ($list_kerja as $listker){?>
	<div class="modal fade ekerja<?php echo str_replace('.','',trim($lp['nik'])).trim($listker->pglmke);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">EDIT Riwayat Pengalaman Kerja</h4>
			</div>
			<div class="modal-body">
				<div class="nav-tabs-custom">
					<!-- Tabs within a box -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#kerjaku<?php echo str_replace('.','',trim($lp['nik'])).trim($listker->pglmke);?>tab_1" data-bs-toggle="tab">View Data</a></li>
						<li><a href="#kerjaku<?php echo str_replace('.','',trim($lp['nik'])).trim($listker->pglmke);?>tab_2" data-bs-toggle="tab">Edit Data</a></li>
					</ul>
					<div class="tab-content no-padding">
						<!-- Morris chart - Sales -->
						<div class="chart tab-pane active" id="kerjaku<?php echo str_replace('.','',trim($lp['nik'])).trim($listker->pglmke);?>tab_1" style="position: relative; height: 300px;">																																									  																				
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tahun Masuk</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo $listker->tahunmasuk?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tahun Keluar</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo $listker->tahunkeluar?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Nama Perusahaan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="perusahaan" value="<?php echo $listker->nmperusahaan;?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Alamat Perusahaan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="alamat" value="<?php echo $listker->alamat;?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Jabatan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="jabatan" value="<?php echo $listker->jabatan;?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Gaji</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="gaji" value="<?php echo $listker->gaji;?>" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
								<div class="col-sm-6">
								  <textarea class="form-control input-sm" id="sakit" name="keterangan" disabled><?php echo $listker->keterangan;?></textarea>
								</div>
								<div class="col-sm-10"></div>
							</div>
						</div>
						<div class="chart tab-pane" id="kerjaku<?php echo str_replace('.','',trim($lp['nik'])).trim($listker->pglmke);?>tab_2" style="position: relative; height: 300px;">														
							<form action="<?php echo site_url('hrd/hrd/edit_pglmkerja');?>" method="post">
							<input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">												
							<input type="hidden" name="nomor" value="<?php echo $listker->pglmke;?>">												
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tahun Masuk</label>
								<div class="col-sm-6">
									<!--<select class='form-control input-sm' name="tahunmasuk" id="tahun">										
										<option value='<?php echo $listker->tahunmasuk; ?>' selected><?php echo $listker->tahunmasuk; ?></option>
										<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
										<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
										<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>									
									</select>-->
									 <input type="text" class="form-control input-sm" id="tahun" value="<?php echo trim($listker->tahunmasuk);?>" placeholder="YYYY" name="tahunmasuk" data-inputmask='"mask": "9999"' data-mask="" required>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tahun Keluar</label>
								<div class="col-sm-6">
									<!--<select class='form-control input-sm' name="tahunkeluar" id="tahun">	
										<option value='<?php echo $listker->tahunkeluar; ?>' selected><?php echo $listker->tahunkeluar; ?></option>
										<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
										<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
										<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>									
									</select>-->
									 <input type="text" class="form-control input-sm" id="tahun" placeholder="YYYY" value="<?php echo trim($listker->tahunkeluar);?>" name="tahunkeluar" data-inputmask='"mask": "9999"' data-mask="" required>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Nama Perusahaan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="perusahaan" value="<?php echo $listker->nmperusahaan;?>" required>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Alamat Perusahaan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="alamat" maxlength="150" value="<?php echo trim($listker->alamat);?>" required>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Jabatan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="jabatan" value="<?php echo $listker->jabatan;?>" required>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Gaji</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" id="sakit" name="gaji" value="<?php echo $listker->gaji;?>" required>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
								<div class="col-sm-6">
								  <textarea class="form-control input-sm" id="sakit" name="keterangan"><?php echo $listker->keterangan;?></textarea>
								</div>
								<div class="col-sm-10"></div>
							</div>						  								
								<button onclick="return confirm('Simpan data Riwayat Pengalaman Pekerjaan ini?')" type="submit" class="btn btn-primary">Simpan</button>
							</form>
						</div>
					</div>
				</div>							
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
			</div>						
		</div>
	  </div>
	</div>
	<?php }?>
	<!--End Edit dan View Pengalaman Kerja-->
	
	<!--Edit dan View status kontrak Kerja-->	
	<?php foreach ($list_kontrak as $listkon){?>
	<div class="modal fade ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">EDIT Status Kontrak Kerja</h4>
			</div>
			<div class="modal-body">
				<div class="nav-tabs-custom">
					<!-- Tabs within a box -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_1" data-bs-toggle="tab">View Data</a></li>
						<li><a href="#ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_2" data-bs-toggle="tab">Edit Data</a></li>
					</ul>
					<div class="tab-content no-padding">
						<!-- Morris chart - Sales -->
						<div class="chart tab-pane active" id="ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_1" style="position: relative; height: 125px;">																																									  																																									  						
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tanggal Mulai</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" name="tglmulai" value="<?php
									$timestamp1 = strtotime($listkon->tanggal1);
									$tanggal1 = date('d-m-Y',$timestamp1);
									echo $tanggal1;
									?>" data-date-format="dd-mm-yyyy" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tanggal Selesai</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" name="tglakhir" value="<?php
									$timestamp2 = strtotime($listkon->tanggal2);
									$tanggal2 = date('d-m-Y',$timestamp2);
									echo $tanggal2;
									?>" data-date-format="dd-mm-yyyy" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>						
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Masa Kerja (Dalam Tahun)</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" value="<?php echo $listkon->masakerja;?>"name="masakerja" disabled>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Status Kerja</label>
								<div class="col-sm-6">
								  <select class="form-control input-sm" name="kdkontrak" disabled>
									<?php foreach ($list_kodekontrak as $likodekon){?>
										<option value="<?php echo $likodekon->kdkontrak;?>" <?php if ($likodekon->kdkontrak==$listkon->kdkontrak) {echo 'selected';}?>><?php echo $likodekon->desc_kontrak;?></option>
									<?php }?>
								  </select>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
									<label for="nosk" class="col-sm-4 control-label">No. SK (Jika Pegawai Tetap)</label>
									<div class="col-sm-6">
										<input value="<?php echo trim($listkon->no_sk);?>" type="text" class="form-control input-sm" name="no_sk" id="no_sk" disabled>
									</div>
									<div class="col-sm-10"></div>
								</div>
						</div>
						<div class="chart tab-pane" id="ekontrak<?php echo str_replace('.','',trim($lp['nik'])).trim($listkon->nomor);?>tab_2" style="position: relative; height: 125px;">							
							<form action="<?php echo site_url('hrd/hrd/edit_stskerja');?>" method="post">
							  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">							  
							  <input type="hidden" name="nomor" value="<?php echo $listkon->nomor;?>">							  
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Tanggal Mulai</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" name="tglmulai" value="<?php 
										$timestampb1 = strtotime($listkon->tanggal1);
										$tanggalb1 = date('d-m-Y',$timestampb1);
										echo $tanggalb1;
										?>" data-date-format="dd-mm-yyyy">
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Tanggal Selesai</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" name="tglakhir" value="<?php
										$timestampb2 = strtotime($listkon->tanggal2);
										$tanggalb2 = date('d-m-Y',$timestampb2);
										echo $tanggalb2;
										?>"  data-date-format="dd-mm-yyyy">
									</div>
									<div class="col-sm-10"></div>
								</div>						
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Masa Kerja (Dalam Tahun)</label>
									<div class="col-sm-6">
									  <input type="text" class="form-control input-sm" value="<?php echo $listkon->masakerja;?>" name="masakerja">
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Status Kerja</label>
									<div class="col-sm-6">
									  <select class="form-control input-sm" name="kdkontrak">
										<?php foreach ($list_kodekontrak as $likodekon){?>
											<option value="<?php echo $likodekon->kdkontrak;?>" <?php if ($likodekon->kdkontrak==$listkon->kdkontrak) {echo 'selected';}?>><?php echo $likodekon->desc_kontrak;?></option>
										<?php }?>
									  </select>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="nosk" class="col-sm-4 control-label">No. SK (Jika Pegawai Tetap)</label>
									<div class="col-sm-6">
										<input value="<?php echo trim($listkon->no_sk);?>" type="text" class="form-control input-sm" name="no_sk" id="no_sk">
									</div>
									<div class="col-sm-10"></div>
								</div>
									<button onclick="return confirm('Simpan data Status Kontrak Kerja ini?')" type="submit" class="btn btn-primary">Simpan</button>
								</form>	
						</div>
					</div>
				</div>							
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
			</div>						
		</div>
	  </div>
	</div>
	<?php }?>
	<!--end Edit dan View status kontrak Kerja-->
	
	<!--Edit dan View Pelatihan-->	
	<?php foreach ($list_pelatihan as $lipel){?>
	<div class="modal fade epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">EDIT Pelatihan</h4>
			</div>
			<div class="modal-body">
				<div class="nav-tabs-custom">
					<!-- Tabs within a box -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_1" data-bs-toggle="tab">View Data</a></li>
						<li><a href="#epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_2" data-bs-toggle="tab">Edit Data</a></li>
					</ul>
					<div class="tab-content no-padding">
						<!-- Morris chart - Sales -->
						<div class="chart tab-pane active" id="epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_1" style="position: relative; height: 250px;">
							<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Tanggal Pelatihan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control input-sm" name="tglpelatihan" value="<?php echo $lipel->tglpelatihan;?>" readonly>
							</div>
							<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Lama Pelatihan</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo trim($lipel->lamapelatihan);?>" maxlength="12" name="lamapelatihan" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>						
							<div class="form-group">
								<label class="col-sm-4 control-label">Nama Pelatihan</label>
								<div class="col-sm-6">
								  <input type="text" class="form-control input-sm" value="<?php echo $lipel->nmpelatihan;?>" name="nmpelatihan" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Tempat/Lokasi</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo $lipel->tempatpelatihan;?>" name="tempatpelatihan" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Trainer</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" value="<?php echo $lipel->trainer;?>" name="trainer" readonly>
								</div>
								<div class="col-sm-10"></div>
							</div>
							<div class="form-group">
								<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
								<div class="col-sm-6">
									<textarea class="form-control input-sm" name="ketpelatihan" readonly><?php echo $lipel->ketpelatihan;?></textarea>
								</div>
								<div class="col-sm-10"></div>
							</div>
						</div>
						<div class="chart tab-pane" id="epelatihan<?php echo str_replace('.','',trim($lp['nik'])).trim($lipel->kdpelatihan);?>tab_2" style="position: relative; height: 250px;">							
							<form action="<?php echo site_url('hrd/hrd/edit_pelatihan');?>" method="post">
							  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">							  
							  <input type="hidden" name="kdpelatihan" value="<?php echo trim($lipel->kdpelatihan);?>">							  
								<div class="form-group">
								<label for="tujuan" class="col-sm-4 control-label">Tanggal Pelatihan</label>
								<div class="col-sm-6">
									<input type="text" class="form-control input-sm" name="tglpelatihan" value="<?php echo $lipel->tglpelatihan;?>" id="tglpelatihan<?php echo trim($lipel->kdpelatihan);?>" data-date-format="dd-mm-yyyy">
								</div>
								<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Lama Pelatihan</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo trim($lipel->lamapelatihan);?>" maxlength="12" name="lamapelatihan" required>
									</div>
									<div class="col-sm-10"></div>
								</div>						
								<div class="form-group">
									<label class="col-sm-4 control-label">Nama Pelatihan</label>
									<div class="col-sm-6">
									  <input type="text" class="form-control input-sm" value="<?php echo $lipel->nmpelatihan;?>" name="nmpelatihan" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Tempat/Lokasi</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo $lipel->tempatpelatihan;?>" name="tempatpelatihan" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Trainer</label>
									<div class="col-sm-6">
										<input type="text" class="form-control input-sm" value="<?php echo trim($lipel->trainer);?>" maxlength="25" name="trainer" required>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
									<div class="col-sm-6">
										<textarea class="form-control input-sm" name="ketpelatihan"><?php echo $lipel->ketpelatihan;?></textarea>
									</div>
									<div class="col-sm-10"></div>
								</div>
									<button onclick="return confirm('Simpan perubahan Data Pelatihan ini?')" type="submit" class="btn btn-primary">Simpan</button>
								</form>	
						</div>
					</div>
				</div>							
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
			</div>						
		</div>
	  </div>
	</div>
	<?php }?>
	<!--end Edit dan View Pelatihan-->	
	
	
	<!--Inputan Modal Mutasi-->
	<!-- Bootstrap modal Input -->
  <div class="modal fade" id="inputmutasi" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Input Mutasi dan Promosi Baru</h3>
      </div>
      <div class="modal-body form">
        <form action="<?php echo site_url('trans/karyawan/simpanmutasi');?>" method="post" class="form-horizontal">
          <!--<input type="hidden" value="" name="id"/> -->
          <div class="form-body">
            <div class="form-group">
				<label class="control-label col-sm-3">Pilih NIK</label>	
				<div class="col-sm-8">    
					<select name="newnik" id='listkary1' class="form-control col-sm-12" >	
						<option value="">-Pilih Nik & Karyawan-</option>					
						<?php foreach ($list_karyawan as $ls){ ?>
						<option value="<?php echo trim($ls->nik);?>" ><?php echo trim($ls->nmlengkap).' || '. trim($ls->nik) ;?></option>																																																			
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Department</label>	
				<div class="col-sm-8">  			
						<select name="newkddept" id='dept' class="form-control col-sm-12" >	
						<option value="">-Pilih Departemen-</option>							
						<?php foreach ($list_opt_dept as $lodept){ ?>
						<option value="<?php echo trim($lodept->kddept);?>" ><?php echo trim($lodept->nmdept);?></option>																																																			
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Sub Department</label>	
				<div class="col-sm-8">    
					<select name="newkdsubdept" id='subdept' class="form-control col-sm-12" >
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
					<select name="newkdjabatan" id='jabatan' class="form-control col-sm-12" >	
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
					<select name="newkdlevel" id='lvljabatan' class="form-control col-sm-12" >										
					<option value="">-Level Jabatan-</option>
						<?php foreach ($list_opt_lvljabt as $loljab){ ?>
						<option value="<?php echo trim($loljab->kdlvl);?>" ><?php echo trim($loljab->nmlvljabatan);?></option>																																																			
						<?php };?>
					</select>
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-3">Atasan</label>	
				<div class="col-sm-8">    
					<select name="newnikatasan" class="form-control col-sm-12" required>
					<option value="">-Pilih NIK Atasan Utama-</option>					
						<?php foreach ($list_opt_atasan as $loan){ ?>
						<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nmlengkap);?></option>																																																			
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Atasan-2</label>	
				<div class="col-sm-8">    
					<select name="newnikatasan2" class="form-control col-sm-12" required>										
					<option value="">-Pilih NIK Atasan Kedua-</option>
						<?php foreach ($list_opt_atasan as $loan){ ?>
						<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nmlengkap);?></option>																																																			
						<?php };?>
					</select>
				</div>
			</div>
			<div class="form-group">
              <label class="control-label col-md-3">No SK</label>
              <div class="col-md-9">
                <input name="nodoksk" placeholder="Nomor Surat Keputusan" style="text-transform:uppercase;" class="form-control" type="text">
              </div>
            </div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Tanggal SK</label>
			  <div class="col-sm-9">
				<input name="tglsk" style="text-transform:uppercase;" placeholder="Tanggal Surat Keputusan" id="tglsk" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Tanggal Memo</label>
			  <div class="col-sm-9">
				<input name="tglmemo" style="text-transform:uppercase;" placeholder="Tanggal Memo Mutasi/Promosi" id="tglmemo" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Tanggal Efektif</label>
			  <div class="col-sm-9">
				<input name="tglefektif" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglefektif" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
			  </div>
			</div>
			<div class="form-group">
			  <label class="control-label col-sm-3">Keterangan</label>
			  <div class="col-sm-9">
				<textarea name="ket" style="text-transform:uppercase;" placeholder="Keterangan Mutasi / Promosi pegawai" id="tglmasuk" data-date-format="dd-mm-yyyy" class="form-control" type="text"></textarea>
			  </div>
			</div>
          </div>
        
          </div>
          <div class="modal-footer">
            <button  type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
		  </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal --> 
		
		<!--Input Keluarga-->
		<div class="modal fade keluarga" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="myModalLabel">Input Keluarga</h4>
						</div>
						<div class="modal-body">
							
							<div class="row">
							<form action="<?php echo site_url('hrd/hrd/input_keluarga');?>" method="post">
							<input type="hidden" name="nip" value="<?php echo trim($lp['nik']);?>">
							  <div class="col-sm-12">								
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Status Di Keluarga</label>
									<div class="col-sm-6">
									  <select class='form-control input-sm' name="stskel" id="stskel">		
										<option value="A">Bapak</option>										
										<option value="B">Ibu</option>										
										<option value="C">Kakak</option>										
										<option value="D">Adik</option>										
										<option value="E">Anak</option>										
										<option value="F">Suami</option>										
										<option value="G">Istri</option>										
									  </select>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="tujuan" class="col-sm-4 control-label">Nama</label>
									<div class="col-sm-6">									  
										<input type="text" class="form-control input-sm" name="nama" id="nama">									  
									</div>
									<div class="col-sm-10"></div>
								</div>		
								<div class="form-group">
									<label for="inputjk" class="col-sm-4 control-label">Jenis Kelamin</label>
									<div class="col-sm-6">
										<select class="form-control input-sm" name="jk" id="jk" required>
											<option value="">-Pilih Jenis Kelamin-</option>
											<option value="B">Pria</option>
											<option value='A'>Wanita</option>
										</select>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="inputttl" class="col-sm-4 control-label">Tempat Lahir</label>
										<div class="col-sm-6">
										  <input type="text" class="form-control input-sm" id="tempat" name="tempatlhr" required>
										</div>
										<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="inputttl" class="col-sm-4 control-label">Tanggal Lahir</label>
										<div class="col-sm-6">
										  <input type="text" class="form-control input-sm" id="inputkeluarga" name="tanggallhr" required data-date-format="dd-mm-yyyy">
										</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="inputttl" class="col-sm-4 control-label">Pendidikan</label>
										<div class="col-sm-6">
										  <select class="form-control input-sm" id="pendidikan" name="pendidikan" required>
											<option value="A">SD/MI</option>
											<option value="B">SMP/MTS</option>
											<option value="C">SMA/MA</option>
											<option value="D">Diploma</option>
											<option value="E">Sarjana</option>
											<option value="F">Magister</option>
										  </select>
										</div>
										<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="inputttl" class="col-sm-4 control-label">Pekerjaan</label>
										<div class="col-sm-6">
										  <input type="text" class="form-control input-sm" id="pekerjaan" name="pekerjaan" required>
										</div>
										<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Masih Hidup</label>
									<div class="col-sm-6">
										<select class="form-control input-sm" name="stshdp">
											<option value="A">Hidup</option>
											<option value="B">Meninggal</option>
										</select>
									</div>
									<div class="col-sm-10"></div>
								</div>
								<div class="form-group">
									<label for="ketm" class="col-sm-4 control-label">Tanggungan</label>
									<div class="col-sm-6">
										<select class="form-control input-sm" name="tanggungan">
											<option value="Y">Iya</option>
											<option value="N">Tidak</option>
										</select>
									</div>
									<div class="col-sm-10"></div>
								</div>
							  </div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
							<button onclick="return confirm('Simpan data Keluarga ini?')" type="submit" class="btn btn-primary">Simpan</button>
						</div>
						</form>
					</div>
				  </div>
		</div>
		
		<!--Input Kesehatan-->
		<div class="modal fade kesehatan<?php echo trim($row->list_nip);?>" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Input Kesehatan</h4>
				</div>
				<div class="modal-body">					
					<div class="row">
					<form action="<?php echo site_url('hrd/hrd/input_kesehatan');?>" method="post">
					  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">
					  <div class="col-sm-12">								
						<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Tahun</label>
							<div class="col-sm-6">
								<select class='form-control input-sm' name="tahun" id="tahun">										
									<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-3; echo $tgl; ?>'><?php $tgl=date('Y')-3; echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-4; echo $tgl; ?>'><?php $tgl=date('Y')-4; echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-5; echo $tgl; ?>'><?php $tgl=date('Y')-5; echo $tgl; ?></option>
								</select>
							</div>
							<div class="col-sm-10"></div>
						</div>						
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Sakit</label>
							<div class="col-sm-6">
							  <textarea class="form-control input-sm" id="sakit" name="sakit"></textarea>
							</div>
							<div class="col-sm-10"></div>
						</div>
					  </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
					<button onclick="return confirm('Simpan data Kesehatan ini?')" type="submit" class="btn btn-primary">Simpan</button>
				</div>
				</form>
			</div>
		  </div>
		</div>
		
	<!--input Riwayat Kerja-->
		<div class="modal fade inputkerja" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Input Riwayat Kerja</h4>
				</div>
				<div class="modal-body">					
					<div class="row">
					<form action="<?php echo site_url('hrd/hrd/input_pglmkerja');?>" method="post">
					  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">
					  <div class="col-sm-12">								
						<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Tahun Masuk</label>
							<div class="col-sm-6">
								<!--<select class='form-control input-sm' name="tahunmasuk" id="tahun">										
									<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>									
								</select>-->
								 <input type="text" class="form-control input-sm" id="tahun" placeholder="YYYY" name="tahunmasuk" data-inputmask='"mask": "9999"' data-mask="" required>
								
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Tahun Keluar</label>
							<div class="col-sm-6">
								<!--<select class='form-control input-sm' name="tahunkeluar" id="tahun">										
									<option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
									<option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>									
								</select>-->
								 <input type="text" class="form-control input-sm" id="tahun" placeholder="YYYY" name="tahunkeluar" data-inputmask='"mask": "9999"' data-mask="" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Nama Perusahaan</label>
							<div class="col-sm-6">
							  <input type="text" class="form-control input-sm" id="sakit" name="perusahaan" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
							<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Alamat Perusahaaan</label>
							<div class="col-sm-6">
							  <input type="text" class="form-control input-sm" id="sakit" name="alamat" maxlength="150" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Jabatan</label>
							<div class="col-sm-6">
							  <input type="text" class="form-control input-sm" id="sakit" name="jabatan" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Gaji</label>
							<div class="col-sm-6">
							  <input type="text" class="form-control input-sm" id="sakit" name="gaji" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
							<div class="col-sm-6">
							  <textarea class="form-control input-sm" id="sakit" name="keterangan"></textarea>
							</div>
							<div class="col-sm-10"></div>
						</div>
					  </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
					<button onclick="return confirm('Simpan data Riwayat Kerja ini?')" type="submit" class="btn btn-primary">Simpan</button>
				</div>
				</form>
			</div>
		  </div>
		</div>
	<!--end input Riwayat Kerja-->
	
<!--- INPUT STATUS KERJA ----->	
<div class="modal fade" id="inputstskerja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Status Kepegawaian <?php echo $nik.'|'.$list_lk['nmlengkap'];?></h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_stspeg')?>" method="post">
      <div class="modal-body">										
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label class="col-sm-4">NIK</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nik"  value="<?php echo $list_lk['nik']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Department</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nmdept"  value="<?php echo $list_lk['nmdept']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Sub Department</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nmsubdept"  value="<?php echo $list_lk['nmsubdept']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nmjabatan"  value="<?php echo $list_lk['nmjabatan']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nosk"  value="<?php echo $list_lk['nmjabatan']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Nama Atasan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nmatasan"  value="<?php echo $list_lk['nmatasan']; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>		
							
							
							
						</div>
					</div><!-- /.box-body -->													
				</div><!-- /.box -->													
			</div>	
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label class="col-sm-4">Nama Kepegawaian</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kdkepegawaian" id="kdkepegawaian1">
									<option value="">--Pilih Kepegawaian--></option>
									  <?php foreach($list_kepegawaian as $listkan){?>
									  <!--option value=""> Masukkan Opsi </option-->
									  <option value="<?php echo trim($listkan->kdkepegawaian);?>" ><?php echo $listkan->nmkepegawaian;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>	
							<script type="text/javascript" charset="utf-8">
							$(function() {
		
											$('#kdkepegawaian1').change(function(){
												
												var kdkepegawaian=$('#kdkepegawaian1').val();
						
													if(kdkepegawaian=='KT'){
														$('#tglselesai').hide();
															$('#tglmulai').show();
														$('#dateselesai').removeAttr('required');
														//$('#statptg1').prop('required',true);
													} else if(kdkepegawaian=='KK'){
														$('#tglmulai').show();	
															$('#datemulai').prop('required',true);														
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
																										
													} else if(kdkepegawaian=='HL'){
														$('#tglmulai').show();	
															$('#datemulai').prop('required',true);														
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
													}  else if(kdkepegawaian=='MG'){
														$('#tglmulai').show();	
															$('#datemulai').prop('required',true);														
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
													}	else if(kdkepegawaian=='KO'){
														$('#datemulai').removeAttr('required');	
														$('#tglmulai').hide();													
														$('#tglselesai').show();
															$('#dateselesai').prop('required',true);
														$('#bolehcuti').hide();		
													}
												
												
											});
										});	
							</script>
							<div id="tglmulai" class="tglmulaiKO" >
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai</label>	
								<div class="col-sm-8">    
									<input type="text" id="datemulai" name="tgl_mulai" data-date-format="dd-mm-yyyy"  class="form-control" required>
								</div>
							</div>
							</div>
							<div class="form-group">
							<div id="tglselesai" class="tglselesaiKT" >
								<label class="col-sm-4">Tanggal Selesai</label>	
								<div class="col-sm-8">    
									<input type="text" id="dateselesai" name="tgl_selesai" data-date-format="dd-mm-yyyy"  class="form-control" required>
								</div>
							</div>	
							</div>
							<div class="form-group">
								<label class="col-sm-4">No. SK</label>	
								<div class="col-sm-8">    
									<input type="text" id="noskstspeg" name="noskstspeg" class="form-control" style="text-transform:uppercase" maxlength="10">
								</div>
							</div>	
							<div class="form-group">
							<div id="bolehcuti" class="bolehcutiKO bolehcutiMG" >
								<label class="col-sm-4">Boleh Cuti</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="cuti" id="kdbahasa">
										<option  value="F" >TIDAK</option>
										<option  value="T" >YA</option>	
									</select>	
								</div>
							</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>	
								<div class="col-sm-8">    
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
								</div>
							</div>		
							
						</div>
					</div><!-- /.box-body -->													
				</div><!-- /.box --> 
			</div>
		</div>	
	</div>					
							
						
				
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
	<!--end input Status Kerja-->
	
	<!--input Pelatihan-->
	<div class="modal fade inputpelatihan" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Input Pelatihan</h4>
				</div>
				<div class="modal-body">					
					<div class="row">
					<form action="<?php echo site_url('hrd/hrd/input_pelatihan');?>" method="post">
					  <input type="hidden" name="nip" value="<?php echo $lp['nik'];?>">
					  <div class="col-sm-12">								
						<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Tanggal Pelatihan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control input-sm" name="tglpelatihan" id="tglpelatihan" data-date-format="dd-mm-yyyy">
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="tujuan" class="col-sm-4 control-label">Lama Pelatihan</label>
							<div class="col-sm-6">
								<input type="text" class="form-control input-sm" name="lamapelatihan" maxlength="12" required>
							</div>
							<div class="col-sm-10"></div>
						</div>						
						<div class="form-group">
							<label class="col-sm-4 control-label">Nama Pelatihan</label>
							<div class="col-sm-6">
							  <input type="text" class="form-control input-sm" name="nmpelatihan" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Tempat/Lokasi</label>
							<div class="col-sm-6">
								<input type="text" class="form-control input-sm" name="tempatpelatihan" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Trainer</label>
							<div class="col-sm-6">
								<input type="text" class="form-control input-sm" maxlength="25" name="trainer" required>
							</div>
							<div class="col-sm-10"></div>
						</div>
						<div class="form-group">
							<label for="ketm" class="col-sm-4 control-label">Keterangan</label>
							<div class="col-sm-6">
								<textarea class="form-control input-sm" name="ketpelatihan"></textarea>
							</div>
							<div class="col-sm-10"></div>
						</div>
					  </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
					<button onclick="return confirm('Simpan data Pelatihan ini?')" type="submit" class="btn btn-primary">Simpan</button>
				</div>
				</form>
			</div>
		  </div>
		</div>
	<!--end Pelatihan-->
	
	<!--Ganti Gambar-->
	<div class="modal fade gantigambar" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Ubah Foto</h4>
				</div>			
					<div class="row">
						<div class="col-md-12">
							<div class="box box-primary">
                                <!-- form start -->
                                <form class="form-horizontal" action="<?php echo site_url('trans/karyawan/up_foto');?>" method="post" enctype="multipart/form-data">						
                                    <div class="box-body">										
										<div class="col-md-12">
											<div class="form-group">												
												<img src="<?php if ($lp['image']<>'') { echo base_url('assets/img/profile/'.$lp['image']);} else { echo base_url('assets/img/user.png');} ;?>" width="100%" height="100%" alt="User Image" >                                            
											</div>											
											<div class="form-group">
												<label for="exampleInputFile">File input</label>											
												<input type="hidden" value="<?php echo $lp['nik'];?>" name="nik">
												<input type="file" id="exampleInputFile" name="gambar">
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
	<!--end Pelatihan-->
	
	
<!--Modal untuk Input Nama Bpjs-->
<div class="modal fade" id="inputbpjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Input Bpjs</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/add_bpjs')?>" method="post">
      <div class="modal-body">										
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#kodekomponen").chained("#kode_bpjs");		
								$("#cjabt").chained("#csubdept");	
								$("#kode_bpjs").selectize();	
								$("#kodekomponen").selectize();	
								$("#kodefaskes").selectize();	
								$("#kodefaskes2").selectize();	
								$("#tgl_bpjs").datepicker();	
								$("#tgl_bpjs2").datepicker();	
												
							  });
							</script>
							<div class="form-group">
								<label class="col-sm-4">NIK</label>	
								<div class="col-sm-8">    
									
									 <input type="text" id="nik" name="nik"  value="<?php echo $nik; ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Bpjs</label>	
								<div class="col-sm-8">
									<select class="form-control input-sm" name="kode_bpjs" id="kode_bpjs">
									  <?php foreach($list_bpjs as $listkan){?>
									  <option value="<?php echo trim($listkan->kode_bpjs)?>" ><?php echo $listkan->kode_bpjs.'|'.$listkan->nama_bpjs;?></option>						  
									  <?php }?>
									</select>									
								</div>
							</div>							
							<div class="form-group">
								<label class="col-sm-4">Kode Komponen Bpjs</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kodekomponen" id="kodekomponen">
									<option value="" ><?php echo '--PILIH PILIH KODE KOMPONEN BPJS NAKER--';?></option>
									  <?php foreach($list_bpjskomponen as $listkan){?>
									  	<option value="<?php echo trim($listkan->kodekomponen);?>" class="<?php echo trim($listkan->kode_bpjs);?>"><?php echo $listkan->kodekomponen.'|'.$listkan->namakomponen;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Id Bpjs</label>	
								<div class="col-sm-8">    
									<input type="text" id="kddept" name="id_bpjs" class="form-control" style="text-transform:uppercase" maxlength="15" required>
									
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Utama</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kodefaskes" id="kodefaskes">
									<option value="" ><?php echo '--PILIH FASKES UTAMA--';?></option>	
									  <?php foreach($list_faskes as $listkan){?>		  
									  <option value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Tambahan</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kodefaskes2" id="kodefaskes2">
									<option value="" ><?php echo '--PILIH FASKES KEDUA--';?></option>	
									  <?php foreach($list_faskes as $listkan){?>
									  <option value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kelas</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kelas" id="kelas">
									<option value="" ><?php echo '--PILIH KAMAR KELAS--';?></option>	
									  <?php foreach($list_kelas as $listkan){?>
									  <option value="<?php echo trim($listkan->kdtrx);?>" ><?php echo $listkan->uraian;?></option>						  
									  <?php }?>
									</select>
									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai Berlaku</label>	
								<div class="col-sm-8">    
									<input type="text" id="tgl_bpjs" name="tgl_berlaku"   data-date-format="dd-mm-yyyy" class="form-control" required>
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >	
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>	
								<div class="col-sm-8">    
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
								</div>
							</div>		
						</div>
					</div><!-- /.box-body -->													
				</div><!-- /.box --> 
			</div>					
		</div><!--row-->
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<!--Modal untuk Edit Bpjs Karyawan-->
<?php foreach ($list_bpjskaryawan as $lb){?>
<div class="modal fade" id="<?php echo trim($lb->id_bpjs); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Bpjs Karyawan</h4>
      </div>
	  <form action="<?php echo site_url('trans/karyawan/edit_bpjs')?>" method="post">
      <div class="modal-body">										
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4">NIK</label>	
								<div class="col-sm-8">    
									<input type="text" id="kddept" name="nik"  value="<?php echo trim($lb->nik);?>" class="form-control" style="text-transform:uppercase" maxlength="15" readonly>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-4">Kode Bpjs</label>	
								<div class="col-sm-8">
									<input type="text" id="nmdept" name="kode_bpjs" value="<?php echo $lb->kode_bpjs;?>"  style="text-transform:uppercase" class="form-control" readonly>								
								</div>
							</div>							
							<div class="form-group">
								<label class="col-sm-4">Kode Komponen Bpjs</label>	
								<div class="col-sm-8">    
									<input type="text" id="nmdept" name="kodekomponen" value="<?php echo $lb->kodekomponen;?>"  style="text-transform:uppercase" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Id Bpjs</label>	
								<div class="col-sm-8">    
									<input type="text" id="kddept" name="id_bpjs"  value="<?php echo $lb->id_bpjs;?>" class="form-control" style="text-transform:uppercase" maxlength="15" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Utama</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kodefaskes" id="kodefaskes">
									 <option value="" ><?php echo '--PILIH KODE FASKES UTAMA--';?></option>
									  <?php foreach($list_faskes as $listkan){?>
									   <option <?php if (trim($lb->kodefaskes)==trim($listkan->kodefaskes)) { echo 'selected';} ?> value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Kode Faskes Tambahan</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kodefaskes2" id="kodefaskes2">
									 <option value="" ><?php echo '--PILIH FASKES KEDUA--';?></option>
									  <?php foreach($list_faskes as $listkan){?>
									   <option <?php if (trim($lb->kodefaskes2)==trim($listkan->kodefaskes)) { echo 'selected';} ?> value="<?php echo trim($listkan->kodefaskes);?>" ><?php echo $listkan->kodefaskes.'|'.$listkan->namafaskes;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-4">Kelas</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kelas" id="kelas">
									<option value="" ><?php echo '--PILIH KAMAR KELAS--';?></option>									 
									 <?php foreach($list_kelas as $listkan){?>
									 		  <option <?php if (trim($lb->kelas)==trim($listkan->kdtrx)) {echo 'selected';}?> value="<?php echo trim($listkan->kdtrx);?>" ><?php echo $listkan->uraian;?></option>						  
									  <?php }?>
									</select>
									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Tanggal Mulai Berlaku</label>	
								<div class="col-sm-8">    
									<input type="text" id="tgl_bpjs2" name="tgl_berlaku"  value="<?php echo $lb->tgl_berlaku1;?>" data-date-format="dd-mm-yyyy" class="form-control">
									<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>	
								<div class="col-sm-8">    
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo $lb->keterangan;?></textarea>
								</div>
							</div>		
						</div>
					</div><!-- /.box-body -->													
				</div><!-- /.box --> 
			</div>					
		</div><!--row-->
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php } ?>	
	
	

</div>

<script>
	//Date picker
    $('#tanggal').datepicker();
    $('#tglmutasi').datepicker();
    $('#tglmemo').datepicker();
    $('#example1').dataTable();
	$("#listkary1").selectize();	
					$("#tglsk").datepicker();                                                            
				$("#tglefektif").datepicker();
					$("#datemulai").datepicker();                               
				$("#dateselesai").datepicker(); 
				$("#datemulai2").datepicker(); 
				$("#dateselesai2").datepicker(); 			


	<?php
	foreach ($list_mutasi as $emut){
		echo "$('#emut".trim($emut->nip).trim($emut->nomor)."').datepicker();";
	}
	foreach ($list_keluarga as $kelu){
		echo "$('#kelu".trim($kelu->nir).trim($kelu->nomor)."').datepicker();";
	}
	?>
    $('#inputkeluarga').datepicker();
	$('#masuk').datepicker();
	$('#stskrjmulai').datepicker();
	$('#stskrjakhir').datepicker();
	$('#tglm').datepicker();
	$('#tglpelatihan').datepicker();	
	<?php foreach ($list_pelatihan as $lipel){?>
	$('#tglpelatihan<?php echo trim($lipel->kdpelatihan);?>').datepicker();
	<?php }?>
	$('#keluar').datepicker();
	$('#berlaku').daterangepicker();
	$("[data-mask]").inputmask();

</script>