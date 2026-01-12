<?php 
/*
	@author : Junis
*/
?>
<script type="text/javascript">

    var save_method; //for save method string
    var table;
    $(document).ready(function() {
      table = $('#table').DataTable();
    });

    function add_person()
    {
      save_method = 'add';
      $('#form')[0].reset(); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tarik Data Absen'); // Set Title to Bootstrap modal title
    }



  </script>
<legend><?php echo $title;?></legend>
<?php //echo $message;?>
<div id="message" >	
</div>
<div><?php //echo 'Total data: '.$ttldata['jumlah']; ?></div>
<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					<a href="<?php echo site_url('trans/absensi/filter_koreksi');?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Absen</a>
					<!--<button class="btn btn-primary" onclick="add_person()" style="margin:10px; color:#ffffff;"><i class="glyphicon glyphicon-plus"></i> Data Mesin Absen</button>-->
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>							
							<!--<th>Userid</th>
							<th>Badgenumberz</th>-->									
							<th>Nama</th>
							<th>NIK</th>
							<th>Tanggal</th>
							<th>Shift</th>
							<th>Jam Masuk</th>
							<th>Jam Pulang</th>
							<th>Keterangan</th>
							<th>Keterangan Cuti</th>
							<th>Keterangan Ijin</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=1; foreach ($list_absen as $la){ ?>
							<tr>																					
								<td><?php echo $la->nmlengkap;?></td>																
								<td><?php echo $la->nik;?></td>								
								<td><?php echo $la->tgl;?></td>								
								<td><?php echo $la->shiftke;?></td>								
								<td><?php echo $la->jam_masuk_absen;?></td>	
								<td><?php echo $la->jam_pulang_absen;?></td>
								<td><?php echo $la->ketsts;?></td>
								<td><?php echo $la->ketcuti;?></td>
								<td><?php echo $la->ketijin;?></td>
								<td>
								<a  href="<?php echo site_url("trans/absensi/show_edit/$la->id/$kddept/$tgl1/$tgl2")?>" onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-pencil"></i> Edit
								</a>
								<a  href="<?php echo site_url("trans/absensi/hapus_absensi/$la->id")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Delete
								</a>
							</td>	
							</tr>
						<?php }?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>



<form action="<?php echo site_url('trans/absensi/input_absensi')?>" method="post">
  <div class="modal fade" id="input" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
       
		<h3 class="modal-title">Input Data Absensi</h3>
      </div>
      <div class="modal-body form">
          <div class="row">
		  <div class="form-body">
		  
            <div class="form-group">
              <label class="control-label col-md-3">Nik Karyawan</label>
              <div class="col-md-9">
				 
                <input name="nik" placeholder="Nama Karyawan" class="form-control" value="<?php //echo trim($nik);?>" type="text" readonly>
              </div>
            </div>
			
			<div class="form-group">
              <label class="control-label col-md-3">Tanggal</label>
              <div class="col-md-9">
                <input name="tanggal1" id="tgl1" data-date-format="dd-mm-yyyy" class="form-control"  type="text">
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jadwal Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="kdjamkerja1" name="kdjamkerja" required>
							<option value="">--Pilih Jam Kerja--</option>
							<?php foreach ($list_jam as $ld){ ?>
							<option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
							<?php } ?>																																					
						</select>
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Masuk</label>
              <div class="col-md-9">
                <input name="jam_masuk" placeholder="Jam masuk" data-inputmask='"mask": "99:99:99"' data-mask="" class="form-control"  type="text">
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Pulang</label>
              <div class="col-md-9">
                <input name="jam_pulang" placeholder="Jam pulang" data-inputmask='"mask": "99:99:99"' data-mask="" class="form-control"  type="text">
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
  

 
 
 
 
 <script>

  

	
	//Date range picker
    $('#tgl').datepicker();
    $('#tgl1').datepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihdept').selectize();
	$("[data-mask]").inputmask();

</script>