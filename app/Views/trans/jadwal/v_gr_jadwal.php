<?php $this->load->model(array('m_jadwalnew','master/m_regu'));?>
<script>
 $(function() {
                $("#table1").dataTable();
                $("#table2").dataTable();
                $("#example2").dataTable();     
                $("#example3").dataTable();     
				$("[data-mask]").inputmask();	
            });
</script>
<legend><?php echo $title;?></legend>
<div class="row">
   <form action="<?php echo site_url('trans/jadwal_new/gr_jadwal_perbulan')?>" method="post">
	<div class="col-sm-6 ">
	<legend><?php echo $title1;?></legend>
		<div>                                
			<table id="table1" class="table table-striped " cellspacing="0" width="100%" border="4">
					<thead>
						<tr>	
							<th>No</th>
							<th width="10%">WEEK ON</th>
							<th width="20%"><div style ='font:11px/21px Arial,tahoma,sans-serif;color:#ff0000'> MINGGU</div></th>
							<th width="20%">SENIN</th>
							<th width="20%">SELASA</th>
							<th width="20%">RABU</th>
							<th width="20%">KAMIS</th>
							<th width="20%">JUMAT</th>
							<th width="20%">SABTU</th>
							
						</tr>
					</thead>
					<tbody>
						<?php $no=1;foreach ($list_tgljadwalrev as $cj){?>
							<tr>																													
								<td ><?php echo $no;?></td>	
								<td><?php echo $cj->week;?></td>	
								<td><?php echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->minggu))."</div>";?></td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->senin)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->senin))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->senin))."</div>";
											} ?>
								</td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->selasa)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->selasa))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->selasa))."</div>";
											} ?></td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->rabu)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->rabu))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->rabu))."</div>";
											} ?></td>								
								<td><?php 	
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->kamis)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->kamis))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->kamis))."</div>";
											} ?></td>								
								<td><?php 
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->jumat)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->jumat))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->jumat))."</div>";
											} ?></td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->sabtu)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->sabtu))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->sabtu))."</div>";
											} ?>
								</td>
			
							</tr>
						<?php $no++; }?>
					</tbody>
			</table>
		</div><!-- /. box -->
	</div><!-- /.col -->
	<div class="col-sm-6 ">
	<legend><?php echo $title2;?></legend>
		<div>                                
			<table id="table2" class="table table-striped " cellspacing="0" width="100%" border="4">
					<thead>
						<tr>	
							<th>No</th>
							<th width="10%">WEEK ON</th>
							<th width="20%"><div style ='font:11px/21px Arial,tahoma,sans-serif;color:#ff0000'> MINGGU</div></th>
							<th width="20%">SENIN</th>
							<th width="20%">SELASA</th>
							<th width="20%">RABU</th>
							<th width="20%">KAMIS</th>
							<th width="20%">JUMAT</th>
							<th width="20%">SABTU</th>
							
						</tr>
					</thead>
					<tbody>
						<?php $no=1;foreach ($list_tgljadwal as $cj){?>
							
							<tr>																													
								<td ><?php echo $no;?></td>	
								<td><?php echo $cj->week;?></td>
								<td><?php echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->minggu))."</div>";?></td>										
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->senin)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->senin))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->senin))."</div>";
											} ?>
								</td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->selasa)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->selasa))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->selasa))."</div>";
											} ?></td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->rabu)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->rabu))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->rabu))."</div>";
											} ?></td>								
								<td><?php 	
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->kamis)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->kamis))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->kamis))."</div>";
											} ?></td>								
								<td><?php 
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->jumat)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->jumat))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->jumat))."</div>";
											} ?></td>								
								<td><?php
								$cek_l=$this->m_jadwalnew->q_hari_libur($cj->sabtu)->num_rows();
								if($cek_l>0){
									echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#ff0000'>".date("d", strtotime($cj->sabtu))."</div>";
											} else {echo "<div style ='font:30px/21px Arial,tahoma,sans-serif;color:#000000'>".date("d", strtotime($cj->sabtu))."</div>";
											} ?>
								</td>
							</tr>
						<?php $no++; }?>
					</tbody>
			</table>
		</div><!-- /. box -->
	</div><!-- /.col -->
</div><!-- /.row --> 
 <div class="modal-footer">
      
		<div class="form-group">
				<input name="bln_akhir" value="<?php echo $bulan_akhir;?>"  class="form-control"  type="hidden">
				<input name="thn_akhir" value="<?php echo $tahun_akhir;?>"  class="form-control"  type="hidden">
				<input name="kdregu" value="<?php echo $kdregu;?>"  class="form-control"  type="hidden">
		</div>		
		<a href="<?php echo site_url('trans/jadwal_new/index');?>"  class="btn btn-dismiss" >Cancel</a>
		<button type="submit"  class="btn btn-primary">SUBMIT</button>
		</form>
</div>
	 
					
