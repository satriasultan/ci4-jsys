<?php 
/*
	@author : hanif_anak_metal \m/
*/
?>
<script>
            $(function() {
                $("#example1").dataTable();
                $("#example2").dataTable();
                $("#example4").dataTable();
				//datemask
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});                               
				//$("#datemaskinput").daterangepicker();                              
				$("#dateinput").datepicker();                               
            });
			//form validation

</script>
<legend><?php echo $title;?></legend>
<?php echo $message;?>

</br>
<form action="<?php echo base_url('user/saveprofile')?>" method="post">
<div class="row">
	<div class="col-sm-8">
		<div class="box box-danger">
			<div class="box-body">
				<div class="form-horizontal">							
					<div class="form-group">
						<label class="col-sm-4">NIK | USERNAME </label>
						<div class="col-sm-8">
							<input type="hidden" class="form-control input-sm" value="edit" id="tipe" name="tipe" required>

							<input type="hidden" class="form-control input-sm" value="<?php echo trim($dtl_user['nik']); ?>" id="nik" name="nik" required>
							<input type="hidden" class="form-control input-sm" value="<?php echo trim($dtl_user['username']); ?>" id="username" name="username" required>

							<input type="input" class="form-control input-sm" value="<?php echo trim($dtl_user['nik']).' | '.trim($dtl_user['username']);?>" name="user" readonly>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4">PASSWORD</label>	
						<div class="col-sm-8">    
							<input type="password" class="form-control input-sm" id="password1" name="passwordweb" title="Masukkan Password Baru Anda(Harus Memiliki Huruf Besar,Kecil & Simbol)" placeholder="Masukkan Password Baru Anda (Harus Memiliki Huruf Besar,Kecil & Simbol)" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4">ULANG PASSWORD</label>	
						<div class="col-sm-8">    
							<input type="password" id="password2" class="form-control input-sm" name="passwordweb2" title="Masukan Ulang Password Sama dengan sebelumnya(Harus Memiliki Huruf Besar,Kecil & Simbol)" placeholder="Masukkan Ulang Password Baru Anda (Harus Memiliki Huruf Besar,Kecil & Simbol)"  required>
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->													
		</div><!-- /.box --> 
	</div>					
</div>
<div class="row">
	<div class="col-sm-6">		
		<a href="<?php echo base_url('dashboard');?>" class="btn btn-danger" style="margin:10px">Close</a>
		<button type="submit" onclick="return confirm('Anda Yakin Password Akan Dirubah?')" class="btn btn-primary" style="margin:10px">Ubah Password</button>
	</div>
	<div class="col-sm-6">		
		
	</div>
</div>
</form>

