<?php 
/*
	@author : junis 10-12-2012\m/
*/
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $("#example2").dataTable();
                $("#example3").dataTable();                             
				$("#dateinput").datepicker();                               
				$("#dateinput1").datepicker(); 
				$("#dateinput2").datepicker(); 
				$("#dateinput3").datepicker(); 
				$("[data-mask]").inputmask();	
            });
</script>
 <script>
    $(document).ready(function() {
        function disableBack() { window.history.forward() }

        window.onload = disableBack();
        window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
    });
</script> 

<legend><?php echo $title;?></legend>
<?php echo $message;?>
<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a>-->
					<a href="<?php echo site_url("trans/lembur/karyawan")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a>
				</div>
				
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->
							
							<th>Nomer Dokumen</th>										
							<th>NIK</th>										
							<th>Nama Karyawan</th>										
							<th>Nama Department</th>										
							<th>Status</th>											
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_lembur as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<!--<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>-->
							<td><?php echo $lu->nodok;?></td>
							<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>
							<td><?php echo $lu->nmdept;?></td>
							<td><?php echo $lu->status1;?></td>
							<td>
								<a data-bs-toggle="modal" data-bs-target="#dtl<?php echo trim($lu->nodok);?>" href='#' class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Detail
								</a>
								<?php if (trim($lu->status)<>'C' and trim($lu->status)<>'P') {?>
								<a data-bs-toggle="modal" data-bs-target="#<?php echo trim($lu->nodok);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-edit"></i> Edit
								</a>
								<a  href="<?php $nik=trim($lu->nik); echo site_url("trans/lembur/hps_lembur/$nik/$lu->nodok")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Hapus
								</a>
								<?php } ?>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>


<!--Modal untuk Edit Nama Bpjs-->
<?php foreach ($list_lembur_dtl as $lb){?>
<div class="modal fade" id="<?php echo trim($lb->nodok); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Lembur Karyawan</h4>
      </div>
	  <form action="<?php echo site_url('trans/lembur/edit_lembur')?>" method="post">
<div class="modal-body">										
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label class="col-sm-4">No. Dokumen</label>	
								<div class="col-sm-8">    
									<input type="text" id="status" name="nodok"  value="<?php echo trim($lb->nodok); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">NIK</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nik"  value="<?php echo trim($lb->nik); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									<input type="hidden" id="status" name="status"  value="I" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Karyawan</label>	
								<div class="col-sm-8">    
									<input type="hidden" id="nik" name="kdlvl1"  value="<?php echo trim($lb->nmlvljabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									<input type="text" id="nik" name="kdlvl1"  value="<?php echo trim($lb->nmlengkap); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									<input type="hidden" id="nik" name="kdlvl"  value="<?php echo trim($lb->kdlvljabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>								
							<div class="form-group">
								<label class="col-sm-4">Department</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="department"  value="<?php echo trim($lb->nmdept); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Sub Department</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="subdepartment"  value="<?php echo trim($lb->nmsubdept); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							
							<!--<div class="form-group">
								<label class="col-sm-4">Level Jabatan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="kdlvl"  value="<?php echo trim($lb->nmlvljabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>-->	
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="jabatan"  value="<?php echo trim($lb->nmjabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">NIK Atasan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="atasan"  value="<?php echo trim($lb->nmatasan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
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
								<label class="col-sm-4">Tipe Lembur</label>	
									<div class="col-sm-8">
									<select class="form-control input-sm" name="kdlembur" id="kdgrade">
									  <?php foreach($list_lembur_edit as $listkan){?>
									  <option <?php if (trim($lb->kdlembur)==trim($listkan->kdlembur)) { echo 'selected';}?> value="<?php echo trim($listkan->kdlembur);?>" ><?php echo $listkan->tplembur;?></option>						  
									  <?php }?>
									</select>
									</div>
							</div>
							<script type="text/javascript">
								$(function() {                         
									$("#dateinput<?php echo trim($lb->nodok);?>").datepicker();                               
								});
							</script>	
							<div class="form-group">
								<label class="col-sm-4">Tanggal Kerja</label>	
								<div class="col-sm-8">    
									<input type="text" id="dateinput<?php echo trim($lb->nodok);?>" value="<?php echo trim($lb->tgl_kerja1); ?>" name="tgl_kerja" data-date-format="dd-mm-yyyy"  class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jam Awal</label>	
								<div class="col-sm-8">    
									<select type="text" class="form-control" name="jam_awal" onFocus="startCalc();" onBlur="stopCalc();" id="target2" required>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="00:00:00") { echo 'selected';}?> value="00:00">00:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="01:00:00") { echo 'selected';}?> value="01:00">01:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="02:00:00") { echo 'selected';}?> value="02:00">02:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="03:00:00") { echo 'selected';}?>  value="03:00">03:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="04:00:00") { echo 'selected';}?> value="04:00">04:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="05:00:00") { echo 'selected';}?> value="05:00">05:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="06:00:00") { echo 'selected';}?> value="06:00">06:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="07:00:00") { echo 'selected';}?> value="07:00">07:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="08:00:00") { echo 'selected';}?> value="08:00">08:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="09:00:00") { echo 'selected';}?> value="09:00">09:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="10:00:00") { echo 'selected';}?> value="10:00">10:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="11:00:00") { echo 'selected';}?> value="11:00">11:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="12:00:00") { echo 'selected';}?> value="12:00">12:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="13:00:00") { echo 'selected';}?> value="13:00">13:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="14:00:00") { echo 'selected';}?> value="14:00">14:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="15:00:00") { echo 'selected';}?> value="15:00">15:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="16:00:00") { echo 'selected';}?> value="16:00">16:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="17:00:00") { echo 'selected';}?> value="17:00">17:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="18:00:00") { echo 'selected';}?> value="18:00">18:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="19:00:00") { echo 'selected';}?> value="19:00">19:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="20:00:00") { echo 'selected';}?> value="20:00">20:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="21:00:00") { echo 'selected';}?> value="21:00">21:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="22:00:00") { echo 'selected';}?> value="22:00">22:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_mulai)=="23:00:00") { echo 'selected';}?> value="23:00">23:00</option>
								</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jam Selesai</label>	
								<div class="col-sm-8">    
								<select type="text" class="form-control" name="jam_selesai" onFocus="startCalc();" onBlur="stopCalc();" id="target1" required>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=='00:00:00') { echo 'selected';}?> value="00:00">00:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=='01:00:00') { echo 'selected';}?> value="01:00">01:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="02:00:00") { echo 'selected';}?> value="02:00">02:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="03:00:00") { echo 'selected';}?>  value="03:00">03:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="04:00:00") { echo 'selected';}?> value="04:00">04:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="05:00:00") { echo 'selected';}?> value="05:00">05:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="06:00:00") { echo 'selected';}?> value="06:00">06:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="07:00:00") { echo 'selected';}?> value="07:00">07:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="08:00:00") { echo 'selected';}?> value="08:00">08:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="09:00:00") { echo 'selected';}?> value="09:00">09:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="10:00:00") { echo 'selected';}?> value="10:00">10:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="11:00:00") { echo 'selected';}?> value="11:00">11:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="12:00:00") { echo 'selected';}?> value="12:00">12:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="13:00:00") { echo 'selected';}?> value="13:00">13:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="14:00:00") { echo 'selected';}?> value="14:00">14:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="15:00:00") { echo 'selected';}?> value="15:00">15:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="16:00:00") { echo 'selected';}?> value="16:00">16:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="17:00:00") { echo 'selected';}?> value="17:00">17:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="18:00:00") { echo 'selected';}?> value="18:00">18:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="19:00:00") { echo 'selected';}?> value="19:00">19:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="20:00:00") { echo 'selected';}?> value="20:00">20:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="21:00:00") { echo 'selected';}?> value="21:00">21:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="22:00:00") { echo 'selected';}?> value="22:00">22:00</option>
									<option type="text" <?php if (trim($lb->tgl_jam_selesai)=="23:00:00") { echo 'selected';}?> value="23:00">23:00</option>
								</select>		
								</div>
							</div>
							<!--<div class="form-group">
								<label class="col-sm-4">Durasi(jam)</label>	
								<div class="col-sm-8">    
									<input type="number" id="gaji" name="durasi" placeholder="0" value="<?php echo trim($lb->durasi); ?>"  class="form-control"  >
								</div>
							</div>-->
							<div class="form-group">
								<label class="col-sm-4">Tanggal Dokumen</label>	
								<div class="col-sm-8">    
									<input type="text" id="tgl1" name="tgl_dok"  value="<?php echo trim($lb->tgl_dok1);?>"class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Alasan Lembur</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="kdtrx" id="kdtrx">
									  <?php foreach($list_trxtype as $listkan){?>
									  <option <?php if (trim($lb->kdtrx)==trim($listkan->kdtrx)) { echo 'selected';}?> value="<?php echo trim($listkan->kdtrx);?>" ><?php echo $listkan->uraian;?></option>						  
									  <?php }?>
									</select>
								</div>
							</div>			
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>	
								<div class="col-sm-8">    
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control" ><?php echo trim($lb->keterangan);?></textarea>
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
	  </form>
	</div>  
	
    </div>
  </div>
</div>
<?php } ?>


<!--Modal untuk Detail Bpjs Karyawan-->
<?php foreach ($list_lembur_dtl as $lb){?>
<div class="modal fade" id="dtl<?php echo trim($lb->nodok); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Detail lembur Karyawan</h4>
      </div>
	  <form action="<?php echo site_url('trans/lembur/approval')?>" method="post">
<div class="modal-body">										
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">							
							<div class="form-group">
								<label class="col-sm-4">No. Dokumen</label>	
								<div class="col-sm-8">    
									<input type="text" id="status" name="nodok"  value="<?php echo trim($lb->nodok); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">NIK</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="nik"  value="<?php echo trim($lb->nik); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									<input type="hidden" id="status" name="status"  value="A" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Nama Karyawan</label>	
								<div class="col-sm-8">    
									<input type="hidden" id="nik" name="kdlvl1"  value="<?php echo trim($lb->nmlvljabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									<input type="text" id="nik" name="kdlvl1"  value="<?php echo trim($lb->nmlengkap); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
									<input type="hidden" id="nik" name="kdlvl"  value="<?php echo trim($lb->kdlvljabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>			
							<div class="form-group">
								<label class="col-sm-4">Department</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="department"  value="<?php echo trim($lb->nmdept); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							<div class="form-group">
								<label class="col-sm-4">Sub Department</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="subdepartment"  value="<?php echo trim($lb->nmsubdept); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>	
							
							<!--<div class="form-group">
								<label class="col-sm-4">Level Jabatan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="kdlvl"  value="<?php echo trim($lb->nmlvljabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>-->	
							<div class="form-group">
								<label class="col-sm-4">Jabatan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="jabatan"  value="<?php echo trim($lb->nmjabatan); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">NIK Atasan</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="atasan"  value="<?php echo trim($lb->nmatasan1); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
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
								<label class="col-sm-4">Tipe Lembur</label>	
								<div class="col-sm-8">    
									<input type="text" id="nik" name="kdlembur"  value="<?php echo trim($lb->tplembur); ?>" class="form-control" style="text-transform:uppercase" maxlength="40" readonly>
								</div>
							</div>		
							<div class="form-group">
								<label class="col-sm-4">Tanggal Kerja</label>	
								<div class="col-sm-8">    
									<input type="text" id="dateinput" value="<?php echo trim($lb->tgl_kerja1); ?>" name="tgl_kerja" data-date-format="dd-mm-yyyy"  class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jam Awal</label>	
								<div class="col-sm-8">    
									<input type="text" id="gaji" name="jam_awal" value="<?php echo trim($lb->tgl_jam_mulai); ?>" placeholder="HH:MM" data-inputmask='"mask": "99:99"' data-mask="" class="form-control" readonly >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jam Selesai</label>	
								<div class="col-sm-8">    
									<input type="text" id="gaji" name="jam_selesai" value="<?php echo trim($lb->tgl_jam_selesai); ?>" placeholder="HH:MM" data-inputmask='"mask": "99:99"' data-mask="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Durasi(jam)</label>	
								<div class="col-sm-8">    
									<input type="number" id="gaji" name="durasi" placeholder="0" value="<?php echo trim($lb->durasi); ?>"  class="form-control" readonly >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Alasan Lembur</label>	
								<div class="col-sm-8">    
									<input type="text" id="tgl1" name="kdtrx"  value="<?php echo trim($lb->uraian);?>"class="form-control" readonly>
								</div>
							</div>		
							<div class="form-group">
								<label class="col-sm-4">Tanggal Dokumen</label>	
								<div class="col-sm-8">    
									<input type="text" id="tgl1" name="tgl_dok"  value="<?php echo trim($lb->tgl_dok1);?>"class="form-control" readonly>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>	
								<div class="col-sm-8">    
									<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control" readonly><?php echo trim($lb->keterangan);?></textarea>
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
	<?php if (trim($lb->status)=='A'){ ?>
	
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
         <button type="submit"  class="btn btn-primary">APPROVAL</button>  
	  </form>
	</div>  
	<div class="modal-footer">
		<form action="<?php echo site_url('trans/lembur/cancel');?>" method="post">
			<input type="hidden" value="<?php echo trim($lb->nodok);?>" name="nodok">
			<input type="hidden" value="<?php echo trim($lb->nik);?>" name="nik">
			<input type="hidden" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
			<input type="hidden" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly>
			<button type="submit" class="btn btn-primary" OnClick="return confirm('Anda Yakin, Membatalkan <?php echo $lb->nodok;?>?')">Cancel</button>
		</form>
		</div> 
		

	
	<?php } ?>
    </div>
  </div>
</div>
<?php } ?>
