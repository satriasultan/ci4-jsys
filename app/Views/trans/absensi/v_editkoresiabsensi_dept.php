  <!-- Bootstrap modal -->
  
 
  <form action="<?php echo site_url('trans/absensi/edit_absensi_dept')?>" method="post">
 
        <script type="text/javascript">
								$(function() {                         
									$("#dateinput").datepicker();                               
								});
		</script>	
		<h3 class="modal-title">Edit Data Absensi</h3>
      
      <div class="modal-body form">
          <div class="row">
		  <div class="form-body">
		  
            <div class="form-group">
              <label class="control-label col-md-3">Nama Karyawan</label>
              <div class="col-md-9">
				 <input type="hidden" value="<?php echo $ld['id'];?>" name="id"/> 
                <input name="namakaryawan" placeholder="Nama Karyawan" class="form-control" value="<?php echo trim($ld['nmlengkap']);?>" type="text" readonly>
				<input name="kddept"  class="form-control" value="<?php echo trim($kddept);?>" type="hidden" readonly>
				<input name="tgl1"  class="form-control" value="<?php echo trim($tgl1);?>" type="hidden" readonly>
				<input name="tgl2"  class="form-control" value="<?php echo trim($tgl2);?>" type="hidden" readonly>
			  </div>
            </div>
			
			<div class="form-group">
              <label class="control-label col-md-3">Tanggal</label>
              <div class="col-md-9">
                <input name="tanggal" id="dateinput" data-date-format="dd-mm-yyyy" class="form-control" value="<?php echo trim($ld['tgl']);?>" type="text">
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Masuk</label>
              <div class="col-md-9">
                <input name="jam_masuk" placeholder="Jam masuk" data-inputmask='"mask": "99:99:99"' data-mask="" class="form-control" value="<?php echo trim($ld['jam_masuk_absen']);?>" type="text">
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Pulang</label>
              <div class="col-md-9">
                <input name="jam_pulang" placeholder="Jam pulang" data-inputmask='"mask": "99:99:99"' data-mask="" class="form-control" value="<?php echo trim($ld['jam_pulang_absen']);?>" type="text">
              </div>
            </div>		
          </div>
        
		
          </div>
          </div>
		  
          <div class="modal-footer">
			<div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?php echo site_url("trans/absensi/lihat_koreksi/$kddept/$tgl1/$tgl2");?>" class="btn btn-danger" >Cancel</a>
			</div>
          </div>
       
	</form>
 
 
 
 
 
 <script>

  

	
	//Date range picker
    $('#tgl').datepicker();
    $('#tgl1').datepicker();
	$('#pilihkaryawan').selectize();
	$('#pilihdept').selectize();
	$("[data-mask]").inputmask();

</script>