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

<legend><?php echo $title;?></legend>
<?php echo $message;?>

			<div class="box">
					<div class="box-header">
						<div class="col-sm-12">	
						<a href="<?php echo site_url("trans/koreksi/inputkoreksikhusus")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a>
						
					</div>
				</div>
			</div>

<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">					
		<li class="active"><a href="#tab_1" data-bs-toggle="tab"><b>Input Koreksi Cuti Kondisi Khusus</b></a></li>
		<li><a href="#tab_2" data-bs-toggle="tab"><b>Lihat Final Koreksi Cuti Kondisi Khusus</b></a></li>
	</ul>
</div>



<div class="tab-content">
	<div class="chart tab-pane active" id="tab_1" style="position: relative; height: 300px;" >

		<div class="row">
			<div class="col-sm-12">										
				
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
						<table id="example1" class="table table-bordered table-striped" >
							<thead>
								<tr>
									<th>No.</th>
									<th>NIK</th>
									<th>Nama Karyawan</th>
									<th>Nomer Dokumen</th>	
									<th>TGL DOKUMEN</th>							
									<th>STATUS</th>										
									<th>TGL KOREKSI</th>
									<!--th>TGL AKHIR</th-->
									<th>Jumlah Cuti</th>
									<th>KETERANGAN</th>										
									<th>Action</th>						
								</tr>
							</thead>
							<tbody>
								<?php $no=0; foreach($list_kkstmp as $lu): $no++;?>
								<tr>										
									<td width="2%"><?php echo $no;?></td>
									<td><?php echo $lu->nik;?></td>	
									<td><?php echo $lu->nmlengkap;?></td>
									<td><a href="<?php echo site_url("trans/koreksi/edit_koreksick/".trim($lu->nodok)."/".trim($lu->nik)."/".$lu->jumlahcuti."");?>"><?php echo $lu->nodok;?></a></td>		
									<!--td><?php echo $lu->docref;?></td-->
									<td><?php echo $lu->tgl_dok;?></td>
									<td><?php echo $lu->status;?></td>
									<td><?php echo $lu->tgl_awal;?></td>
									<!--td><?php echo $lu->tgl_akhir;?></td-->
									<td><?php echo $lu->jumlahcuti;?></td>
									<td><?php echo $lu->keterangan;?></td>
									<td>
										<?php if (trim($lu->status)<>'P' and trim($lu->status)<>'F') {?>
										<a href="<?php echo site_url("trans/koreksi/save_finalkck/".trim($lu->nodok)."/".trim($lu->nik)."/".$lu->tgl_dok."/".$lu->jumlahcuti."");?>"  onclick="return confirm('Anda Yakin Save Final (PERINGATAN!!! DATA TIDAK BISA DI KOREKSI)?')"  class="btn btn-success">Save Final</a>
										<a href="<?php echo site_url("trans/koreksi/hps_kck/".trim($lu->nodok)."/".trim($lu->nik)."/".$lu->tgl_dok."/".$lu->jumlahcuti."");?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-danger">Hapus</a>
										<?php } ?>
										
									</td>
								</tr>
								<?php endforeach;?>
							</tbody>
						</table>
				</div><!-- /.box-body -->	
			</div>
		</div>
</div>	
<div class="tab-pane" id="tab_2" style="position: relative; height: 300px;" >

		<div class="row">
			<div class="col-sm-12">										
				
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
						<table id="example2" class="table table-bordered table-striped" >
							<thead>
								<tr>
									<th>No.</th>
									<th>NIK</th>
									<th>Nama Karyawan</th>
									<th>Nomer Dokumen</th>	
									<th>TGL DOKUMEN</th>							
									<th>STATUS</th>										
									<th>TGL KOREKSI</th>
									<!--th>TGL AKHIR</th-->
									<th>Jumlah Cuti</th>
									<th>KETERANGAN</th>										
									<!--th>Action</th-->						
								</tr>
							</thead>
							<tbody>
								<?php $no=0; foreach($list_kksfinal as $lu): $no++;?>
								<tr>										
									<td width="2%"><?php echo $no;?></td>
									<td><?php echo $lu->nik;?></td>	
									<td><?php echo $lu->nmlengkap;?></td>
									<td><a href="#"><?php echo $lu->nodok;?></a></td>		
									<!--td><?php echo $lu->docref;?></td-->
									<td><?php echo $lu->tgl_dok;?></td>
									<td><?php echo $lu->status;?></td>
									<td><?php echo $lu->tgl_awal;?></td>
									<!--td><?php echo $lu->tgl_akhir;?></td-->
									<td><?php echo $lu->jumlahcuti;?></td>
									<td><?php echo $lu->keterangan;?></td>
									<!--td>
										<?php if (trim($lu->status)<>'P' and trim($lu->status)<>'F') {?>
										<a href="<?php echo base_url("trans/koreksi/viewfinalkck/$lu->nodok/");?>"  class="btn btn-success">Save Final</a>
										<a href="<?php echo site_url("trans/koreksi/hps_kck/$lu->nodok");?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-danger">Hapus</a>
										<?php } ?>
										<?php if (trim($lu->status)=='P' and trim($lu->tglhgscuti)>date('m-d')) { ?>
										 <a href="<?php echo site_url("trans/koreksi/hapuskck/$lu->nodok");?>" onclick="return confirm('Anda Yakin Hapus Data ini Telah Di FINAL??')" class="btn btn-danger">Hapus</a>
										<?php } ?>
									</td-->
								</tr>
								<?php endforeach;?>
							</tbody>
						</table>
				</div><!-- /.box-body -->	
			</div>
		</div>
</div>