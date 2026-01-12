<?php 
/*
	@author : junis 10-12-2012\m/
*/
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $("#kary").selectize();
                $("#moduser").selectize();
                $("#tahun").selectize();
               
				
            });					
</script>

<legend><?php echo $title;?></legend>

<div class="row">
		<a data-bs-toggle="modal" data-bs-target=".baru" class="btn btn-warning" style="margin:5px"><i class="glyphicon glyphicon-process"></i> Hitung Ulang Cuti</a>
			<a href="cutibalance" class="btn btn-info" style="margin:5px"> Kembali</a>
			<a href="<?php echo site_url("trans/cuti_karyawan/excel_blc_dtl/$tahun")?>"  class="btn btn-default" style="margin:10px;">Export Excel</a>			

	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<th>NIK</th>	
							<th>NAMA</th>	
							<th>TANGGAL</th>
							<th>NO DOKUMEN</th>
							<th>TIPE DOKUMEN</th>	
							<th>IN CUTI</th>					
							<th>OUT CUTI</th>					
							<th>TOTAL CUTI</th>					
							<th>STATUS</th>					
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($listblc as $lb): $no++;?>
						<tr>										
							<td><?php echo $no;?></td>																							
							<td><?php echo $lb->nik;?></td>
							<td><?php echo $lb->nmlengkap;?></td>
							<td><?php echo $lb->tanggal;?></td>
							<td><?php echo $lb->no_dokumen;?></td>
							<td><?php echo $lb->doctype;?></td>
							<td><?php echo $lb->in_cuti;?></td>
							<td><?php echo $lb->out_cuti;?></td>
							<td><?php echo $lb->sisacuti;?></td>
							<td><?php echo $lb->status;?></td>
							
							
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>


<!--Hitung Cuti Karyawan-->
	<div class="modal fade baru"  role="dialog" >
	  <div class="modal-dialog modal-sm-12">
		<div class="modal-content">
			<form class="form-horizontal" action="<?php echo site_url('trans/cuti_karyawan/cutibalancedtl');?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Tutup</span></button>
				<h4 class="modal-title" id="myModalLabel">Hitung Ulang Cuti Karyawan</h4>
			</div>
			<div class="modal-body">										
			<div class="row">
				<div class="col-lg-12">
					<div class="box box-danger">
						<div class="box-body">
							<div class="form-horizontal">								
								
								<div class="form-group">
									<label class="col-sm-4">PILIH NIK DAN Karyawan</label>
									<div class="col-sm-8">
										<select id="moduser" name="htgkry" required>
											<option value="">--Pilih NIK || Nama Karyawan--></option>
											<?php foreach ($listblc as $db){?>
											<option value="<?php echo trim($db->nik);?>"><?php echo str_pad($db->nik,50);?></option>
											<?php }?>
										</select>	
									</div>				
								</div>
							
							</div>
							</div>
						</div><!-- /.box-body -->													
					</div><!-- /.box --> 
				</div>			
			</div><!--row-->
			
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary" onclick="return confirm('Yakin Akan Di Process?')">Process</button>											
			</div>
			
			</form>
			</div>
		</div> 
	</div>




