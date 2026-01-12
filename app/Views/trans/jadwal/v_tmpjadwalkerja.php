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
<legend><?php echo $title.' REGU : '.$kdregu.'|| BULAN : '.$bln.'|| TAHUN : '.$thn;?></legend>
<?php //echo $message;?>
<!--div id="message" >	
</div-->
<div>	
<a href="<?php echo base_url("trans/jadwal_new/clear_tmpjadwal")?>" onclick="return confirm('Anda Yakin  Menghapus Jadwal Ini ? ')" class="btn btn-primary" style="margin:10px; color:#ffffff;">Clear</a>
<a href="<?php echo base_url('trans/jadwal_new/savefinal_tmpjadwal/'.$bln.'/'.$thn)?>"  onclick="return confirm('Anda Yakin  Memproses Data Ini ?')" class="btn btn-danger" style="margin:10px; color:#ffffff;">Final Submit</a>
<!--a href="#" data-bs-toggle="modal" data-bs-target="#filter" class="btn btn-primary" style="margin:10px; color:#ffffff;">FILTER</a-->
</div>
<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
				
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table1" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>																
							<th>No.</th>
							<!--th>KODE REGU</th-->
							<th>TANGGAL</th>
							<th>KODE SHIFT</th>
							<!--th>INPUT BY</th>
							<th>INPUT DATE</th--->
							<th width="14%">ACTION</th>
							
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach ($list_tmpjadwal as $ls): $no++ ?>
							<tr>																													
								<td><?php echo $no;?></td>	
								<!--td><?php echo $ls->kdregu;?></td-->								
								<td><?php echo $ls->tgl;?></td>								
								<td><?php echo $ls->kodejamkerja;?></td>								
								<!--td><?php echo $ls->inputby;?></td>								
								<td><?php echo $ls->inputdate;?></td-->											
						
								<td><a href="#" data-bs-toggle="modal" data-bs-target="#dtl<?php echo trim($ls->tgl);?>" class="btn btn-success"> Edit</a>
								<a <?php $kdregu=trim($ls->kdregu);?> href="<?php echo base_url("trans/jadwal_new/delete_tmpjadwal/$kdregu/$ls->tgl/$ls->kodejamkerja")?>" onclick="return confirm('Anda Yakin Hapus 1 Row Ini..??')" class="btn btn-danger"> Hapus </a>
								
								</td>											
							
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>

 <!-- Bootstrap modal Edit Jadwal -->
 <?php foreach ($list_tmpjadwal as $ls){?>
 <form action="<?php echo base_url('trans/jadwal_new/edit_tmpjadwal')?>" method="post">
  <div class="modal fade" id="dtl<?php echo trim($ls->tgl); ?>" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
		<h3 class="modal-title">Edit Jadwal Kerja</h3>
      </div>
      <div class="modal-body form">
    
             
			<div class="form-group-sm-12">
              <label class="control-label col-md-3">Kode Regu</label>
              <div class="col-sm-9">
					
					<input name="kdregu" value="<?php echo trim($ls->kdregu);?>" class="form-control"  type="input" readonly>
              </div>
            </div>
		 
			<div class="form-group-sm-12">
              <label class="control-label col-md-3">Tanggal Jadwal Kerja</label>
              <div class="col-sm-9">
                <input name="tgl" value="<?php echo trim($ls->tgl);?>" class="form-control"  type="input" readonly>
              </div>
            </div>
			 <div class="form-group-sm">
              <label class="control-label col-md-3">Jadwal Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm"  name="kdjamkerja" required>
							<!--option value="<?php echo trim($ls->kodejamkerja);?>"><?php echo trim($ls->kodejamkerja);?></option-->
							<?php foreach ($list_jamkerja as $ld){ ?>
							<!--option value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->kdjam_kerja.' || '.$ld->nmjam_kerja;?></option-->
							<option <?php if (trim($ld->kdjam_kerja)==trim($ls->kodejamkerja)) { echo 'selected';}?> value="<?php echo trim($ld->kdjam_kerja);?>" ><?php echo $ld->kdjam_kerja.' || '.$ld->nmjam_kerja;?></option>						  
							<?php } ?>																																					
						</select>
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
  <?php } ?>
  <!-- Bootstrap modal -->
  

 
 <script>
 
	//Date range picker
  //  $('#tgl').datepicker();
    $('#tgl2').datepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihkaryawan2').selectize();
	//$('#kdregu').selectize();
	$('#kdregu2').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");		
	$("#disb").chained("#city");	

</script>