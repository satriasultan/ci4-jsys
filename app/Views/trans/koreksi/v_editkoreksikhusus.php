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
				$("#nik1").selectize();	
            });
		
</script>

<legend><?php echo $title;?></legend>


<form action="<?php echo site_url('trans/koreksi/save_editckk')?>" method="post">
<div class="modal-body">										
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-danger">
					<div class="box-body">
						<div class="form-horizontal">							
						
							<div class="form-group">
								<label class="col-sm-4">INPUT NIK KARYAWAN</label>	
								<div class="col-sm-8">    
									<select class="form-control input-sm" name="nik" id="nik1">
									<!--option value="">--Cari Nama Karyawan--</option-->
									<?php foreach ($list_karyawan as $lk) { ?>
									<option value="<?php echo trim($lk->nik); if(trim($dtl['nik'])==trim($lk->nik));?>"><?php echo trim($lk->nmlengkap).' || '.trim($lk->nik); ?></option>
									
									<?php } ?>
									</select>
								</div>
							</div>
							
							<!--div class="form-group">
								<label class="col-sm-4">DOKUMEN CUTI BERSAMA</label>	
								<div class="col-sm-8"> 
									<select class="form-control input-sm" name="docref" id="docref1">
									<option value="">--Cari Nomor Dokumen--</option>
									<?php foreach ($listcb as $lcb) { ?>
									<option value="<?php echo trim($lcb->nodok);?>" class="<?php echo trim($lcb->nodok);?>"><?php echo trim($lcb->nodok);?></option>
									<?php } ?>
									</select>
									
								</div>
							</div-->	
							<!--div class="form-group">
								 <label class="col-sm-4">Input Dokumen Koreksi Cuti</label>
								<div class="col-sm-8">
								
										<input type="text" id="docref1" name="docref"  class="form-control">

								</div>
							</div--->

							<script type="text/javascript">
								$(function() {                         
									$("#tglmulai").datepicker();                               
									//$("#tglselesai").datepicker(); 
																	
								});
								
							</script>
					<script type="text/javascript" charset="utf-8">
							/*  $(function() {
						
							$("#tgl_awal1").chained("#docref1");
							$("#tgl_akhir1").chained("#docref1");
							$("#jumlahcuti1").chained("#docref1");
						
						
							  });*/
					</script>
							<div class="form-group">
								 <label class="col-sm-4">Tanggal Koreksi Cuti</label>
								<div class="col-sm-8">
									
										<input value="<?php echo $dtl['tgl_dok2']?>" type="text" id="tgl_awal" name="tgl_dok" data-date-format="dd-mm-yyyy"  class="form-control" required>
									
									<!-- /.input group -->	
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Jumlah Cuti</label>	
								<div class="col-sm-8">    
									<!--input type="text" id="tglmulai" name="tgl_awal" data-date-format="dd-mm-yyyy"  class="form-control" required disabled-->
									
									<input value="<?php echo $dtl['jumlahcuti'];?>" type="number" name="jumlahcuti" placeholder="0"   class="form-control" required >
								</div>	
							</div>
							<div class="form-group">
								 <label class="col-sm-4">Tanggal Input</label>
								<div class="col-sm-8">
									
										<input type="text" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>
									
									<!-- /.input group -->
								</div>
							</div>
							<div class="form-group">
								 <label class="col-sm-4">Input By</label>
								<div class="col-sm-8">
								
										<input type="text" id="inputby" name="inputby"  value="<?php echo $this->session->userdata('nik');?>" class="form-control" readonly >

									<!-- /.input group -->
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Keterangan</label>	
								<div class="col-sm-8">
									<input textarea="textarea" value="<?php echo ($dtl['keterangan']);?>" type="text" id="keterangan1" name="keterangan"   class="form-control"></input>
									<!--input type="hidden" name="tgl_awal" id="tgl_awal1"  value="<?php echo date('d-m-Y 00:00:00'); ?>"class="form-control" readonly-->
									<input type="hidden" name="tgl_akhir" id="tgl_akhir1" value="<?php echo trim($dtl['tgl_awal']); ?>" class="form-control" readonly>
									<input type="hidden" id="docref1" name="docref" value=""class="form-control" readonly>
									<input type="hidden" id="doctype1" name="doctype" value="X" class="form-control" readonly>
									<input value="<?php echo ($dtl['nodok']);?>" type="hidden" name="nodoksmt" class="form-control" readonly>
								</div>
							</div>		
						</div>
					</div>													
				</div>
			</div>
		</div>
	</div>	
	<div> 
        <a href="<?php echo site_url('trans/koreksi');?>" type="button" class="btn btn-default"/> Kembali</a>
        <button type="submit"  class="btn btn-primary">SIMPAN</button>
    </div>
</form>
