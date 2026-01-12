<script type="text/javascript">
            $(function() {
               $("#table").dataTable();
                $("#example1").dataTable();
                $("#pilihkaryawan").selectize();
				//datemask
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});                               
				//$("#datemaskinput").daterangepicker();                              
				$("#tgl").datepicker();                               
            });			
</script>

<legend><?php echo $title;?></legend>
<?php echo $message;?>
<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					<a href="<?php echo site_url("trans/jadwal")?>" class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
					<!--a href="<?php echo site_url("trans/lembur/karyawan")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a-->
					<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">INPUT</a>
				</div>
				
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<th>NIK</th>
							<th>Nama Karyawan</th>										
							<th>DEPARTMENT</th>	
							<th>KODE JAM KERJA</th>	
							<th>KODE REGU</th>	
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($opt_jamkerja as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>																							
							<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>
							<td><?php echo $lu->nmdept;?></td>
							<td><?php echo $lu->kdjamkerja;?></td>
							<td><?php echo $lu->kdregu;?></td>
							<td>
							<a href="<?php echo site_url("trans/jadwal/delete_dtljdwal")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-warning">Delete</a-->
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>

<!--Modal untuk Filter-->
<div class="modal fade" id="input" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">INPUT KARYAWAN REGU</h4>
      </div>
	  <form action="<?php echo site_url('trans/jadwal/input_opr')?>" method="post">
      <div class="modal-body modal-md">
        <div class="form-group input-sm ">		
			<label class="label-form col-sm-3">NIK</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" id="pilihkaryawan" name="nik">
					<option value="">--ALL--</option>
					<?php foreach ($list_karyawan as $ld){ ?>
					<option value="<?php echo trim($ld->nik);?>"><?php echo $ld->nik.'|'.$ld->nmlengkap;?></option>
					<?php } ?>	
				</select>	
			</div>			
		</div>
		<div class="form-group input-sm">
              <label class="label-form col-sm-3">Department</label>
              <div class="col-sm-9">
                <input name="depte" id="dept" value="<?php echo trim($opt_1['nmdept']);?>" class="form-control" type="text" disabled>					
			  </div>
        </div>
		<div class="form-group input-sm">
              <label class="label-form col-sm-3">Kode REGU</label>
              <div class="col-sm-9">
                <input name="kdregu" id="kdregu1" value="<?php echo trim($opt_1['kdregu']);?>" class="form-control" type="text" disabled>	
				<input type="hidden" name="kdregue"  value="<?php echo trim($opt_1['kdregu']);?>"class="form-control" readonly>				
			  </div>
        </div>
		<div class="form-group input-sm">
              <label class="label-form col-sm-3">Kode Jam Kerja</label>
              <div class="col-sm-9">
                <input name="kdjamkerja" id="kdjamkerja1" value="<?php echo trim($opt_1['kdjamkerja']);?>" class="form-control" type="text" disabled>	
				<input type="hidden" name="kdjamkerjae"  value="<?php echo trim($opt_1['kdjamkerja']);?>"class="form-control" readonly>					
			  </div>
        </div>
		<div class="form-group input-sm">
              <label class="label-form col-sm-3">Tanggal</label>
              <div class="col-md-9">
                <input name="tgl" id="tgl1" value="<?php echo trim($opt_1['tgl']);?>" class="form-control" type="text" required disabled>					
                <input type="hidden" name="tgle"  value="<?php echo trim($opt_1['tgl']);?>"class="form-control" readonly>
                <input type="hidden" name="id"  value="<?php echo trim($id);?>"class="form-control" readonly>
			  </div>
        </div>	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit1" class="btn btn-primary">INPUT</button>
      </div>
	  </form>
    </div>
  </div>
</div>

