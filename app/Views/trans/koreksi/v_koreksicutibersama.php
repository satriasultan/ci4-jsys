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
<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">	
				<a href="<?php echo site_url("trans/koreksi/inputkcb")?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a>
			</div>
		</div>
	</div>
	<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<th>NIK</th>
							<th>Nama Karyawan</th>
							<th>Nomer Dokumen</th>	
							<th>DOC REF</th>
							<th>TGL DOKUMEN</th>							
							<th>STATUS</th>										
							<th>TGL AWAL</th>
							<th>TGL AKHIR</th>
							<th>Jumlah Cuti</th>
							<th>KETERANGAN</th>										
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_kcb as $lu): $no++;?>
						<tr>										
							<td width="2%"><?php echo $no;?></td>
							<td><?php echo $lu->nik;?></td>	
							<td><?php echo $lu->nmlengkap;?></td>
							<td><?php echo $lu->nodok;?></td>
							<td><?php echo $lu->docref;?></td>
							<td><?php echo $lu->tgl_dok;?></td>
							<td><?php echo $lu->status;?></td>
							<td><?php echo $lu->tgl_awal;?></td>
							<td><?php echo $lu->tgl_akhir;?></td>
							<td><?php echo $lu->jumlahcuti;?></td>
							<td><?php echo $lu->keterangan;?></td>
							<td>
								<?php if (trim($lu->status)<>'P' and trim($lu->status)<>'F') {?>
								<a href="<?php echo base_url("trans/koreksi/viewotokcb/$lu->nodok/");?>"  class="btn btn-success">Save Final</a>
								<a href="<?php echo site_url("trans/koreksi/hps_kcb/$lu->nodok");?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-danger">Hapus</a>
								<?php } ?>
								<?php if (trim($lu->status)=='P') {
								 
								echo "Final"; } ?>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
		</div><!-- /.box-body -->	
	</div>
</div>
	