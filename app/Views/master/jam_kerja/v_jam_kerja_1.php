<link href="<?php echo base_url('assets/css/datepicker.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
            });
</script>
<legend><?php echo $title;?></legend>
<?php echo $message; ?>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
		<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal1" style="margin:10px"><i class="glyphicon glyphicon-plus"></i> INPUT</a>
	</div><!-- /.box-header -->	
		<div class="box-body" style="overflow:auto;width:auto;height:auto;">
			<table id="example1" class="table table-bordered table-striped" >
				<thead>
					<tr>																	
						<th>No.</th>
						<th>Kode Jam Kerja</th>
						<th>Nama Jam Kerja</th>
						<th>Shift Ke</th>
						<th>Jam Masuk</th>
						<th>Jam Masuk Minimal</th>
						<th>Jam Masuk Maksimal</th>
						<th>Jam Istirahat </th>
						<th>Jam Istirahat Mulai Minimal</th>
						<th>Jam Istirahat Mulai Maksimal</th>
						<th>Jam Istirahat Selesai </th>
						<th>Jam Istirahat Selesai Minimal</th>
						<th>Jam Istirahat Selesai Maksimal</th>
						<th>Jam Pulang</th>
						<th>Jam Pulang Minimal</th>
						<th>Jam Pulang Maksimal</th>
						<th>Keterangan</th>
						<th>Input Date</th>
						<th>Input By</th>	
						<th>Update Date</th>
						<th>Update By</th>
						<th>Aksi</th>						
					</tr>
				</thead>
				<tbody>
				<?php $no=0; foreach($list_jam_kerja as $row): $no++;?>
			<tr>
				
				<td width="1"><?php echo $no;?></td>
				<td><?php echo $row->kdjam_kerja;?></td>
				<td><?php echo $row->nmjam_kerja;?></td>
				<td><?php echo $row->shiftke;?></td>
				<td><?php echo $row->jam_masuk;?></td>
				<td><?php echo $row->jam_masuk_min;?></td>
				<td><?php echo $row->jam_masuk_max;?></td>
				<td><?php echo $row->jam_istirahat;?></td>
				<td><?php echo $row->jam_istirahat_min;?></td>
				<td><?php echo $row->jam_istirahat_max;?></td>
				<td><?php echo $row->jam_istirahatselesai;?></td>
				<td><?php echo $row->jam_istirahatselesai_min;?></td>
				<td><?php echo $row->jam_istirahatselesai_max;?></td>
				<td><?php echo $row->jam_pulang;?></td>
				<td><?php echo $row->jam_pulang_min;?></td>
				<td><?php echo $row->jam_pulang_max;?></td>
				<td><?php echo $row->keterangan;?></td>
				<td><?php echo $row->input_date;?></td>
				<td><?php echo $row->input_by;?></td>
				<td><?php echo $row->update_date;?></td>
				<td><?php echo $row->update_by;?></td>
				<td><a href="<?php echo site_url('master/jam_kerja/hps_jam_kerja').'/'.$row->kdjam_kerja;?>" OnClick="return confirm('Anda Yakin Hapus <?php echo trim($row->kdjam_kerja);?>?')"><i class="fa  fa-trash-o"></i> Hapus</a>|
					<a data-bs-toggle="modal" data-bs-target="#<?php echo trim($row->kdjam_kerja);?>" href="#" ><i class="fa  fa-edit"></i>Edit</a>
				</td>
			</tr>
			<?php endforeach;?>	
				</tbody>
			</table>
		</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
<!-- Modal Input jam_kerja -->

<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="myModalLabel">INPUT MASTER JAM KERJA</h4>
	  </div>
	  
		<div class="modal-body">
		<form role="form" action="<?php echo site_url('master/jam_kerja/add_jam_kerja');?>" method="post">
			<div class="row">
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Kode Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="kddept" name="kdjam_kerja"  class="form-control" style="text-transform:uppercase" maxlength="4" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Nama Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="nmjam_kerja"  maxlength="20" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Shift Ke</label>
				<div class="col-sm-12">
					<select class="form-control input-sm" name="shiftke" id="kdgrade">
						  <option  value="1" class="form-control">I</option>						  
						  <option  value="2" class="form-control">II</option>
						  <option  value="3" class="form-control">III</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Tanggal Input</label>
				<div class="col-sm-12">
					
						<input type="text" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
					
					<!-- /.input group -->
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Input By</label>
				<div class="col-sm-12">
				
						<input type="text" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >

					<!-- /.input group -->
				</div>
			</div>
			</div>
			</div>
			
			<div class="row">
			<div class="form-group">
				 <label class="col-sm-12">Keterangan</label>
				<div class="col-sm-12">

						<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>
					
					<!-- /.input group -->
				</div>
			</div>
			
			</div>
			</div>
			<div class="modal-footer">
				<div class="form-group"> 
					<div class="col-lg-12">
						<button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
					   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
					</div>
				</div>
			</div>
			</div>
		</form>
  </div>
</div>
</div>  
</div>
<!--Edit Department -->
<?php foreach ($list_jam_kerja as $lk){?>

<div class="modal fade" id="<?php echo trim($lk->kdjam_kerja);?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="myModalLabel">Edit MASTER JAM KERJA </h4>
	  </div>
	  
		<div class="modal-body">
		<form role="form" action="<?php echo site_url('master/jam_kerja/edit_jam_kerja');?>" method="post">
			<div class="row">
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Kode Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="kddept" name="kdjam_kerja"  class="form-control" value="<?php echo $lk->kdjam_kerja;?>" maxlength="3" readonly>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Nama Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="nmjam_kerja" value="<?php echo $lk->nmjam_kerja;?>" maxlength="20" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Shift Ke</label>
				<div class="col-sm-12">
					<select class="form-control input-sm" name="shiftke" id="kdgrade">
						  <option  <?php if (trim($lk->shiftke=='1')) { echo 'selected';}?> value="1" class="form-control">I</option>						  
						  <option  <?php if (trim($lk->shiftke=='2')) { echo 'selected';}?> value="2" class="form-control">II</option>
						  <option  <?php if (trim($lk->shiftke=='3')) { echo 'selected';}?> value="3" class="form-control">III</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk" value="<?php echo $lk->jam_masuk;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_min" value="<?php echo $lk->jam_masuk_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_max" value="<?php echo $lk->jam_masuk_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat" value="<?php echo $lk->jam_istirahat;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_min" value="<?php echo $lk->jam_istirahat_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_max" value="<?php echo $lk->jam_istirahat_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai" value="<?php echo $lk->jam_istirahatselesai;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_min" value="<?php echo $lk->jam_istirahatselesai_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_max" value="<?php echo $lk->jam_istirahatselesai_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang" value="<?php echo $lk->jam_pulang;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_min" value="<?php echo $lk->jam_pulang_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_max" value="<?php echo $lk->jam_pulang_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Tanggal Input</label>
				<div class="col-sm-12">
					
						<input type="text" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
					
					<!-- /.input group -->
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Input By</label>
				<div class="col-sm-12">
				
						<input type="text" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >

					<!-- /.input group -->
				</div>
			</div>
			</div>
			</div>
			
			<div class="row">
			<div class="form-group">
				 <label class="col-sm-12">Keterangan</label>
				<div class="col-sm-12">

						<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo $lk->keterangan;?></textarea>
					
					<!-- /.input group -->
				</div>
			</div>
			</div>
			</div>
			<div class="modal-footer">
				<div class="form-group"> 
					<div class="col-lg-12">
						<button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
					   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
					</div>
				</div>
			</div>
		</div>
		</form>
  </div>
</div>
</div> 
</div> 						
<?php }?>
<script>

  $("[data-mask]").inputmask();
 
	
	//Date range picker
    $('#tgl').datepicker();

  

</script>