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
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">
				</div>
			</div>	
   <form action="<?php echo site_url('trans/jadwal_new/gr_jadwal_perbulan')?>" method="post">
	<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table1" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>																
							<th>No.</th>
							<th>KDREGU</th>
							<th>BLN/THN</th>
							<th>KDOPTION</th>
							<!--th>EOSF INDEX</th>
							<th>EOSF</th>
							<th>EOR </th-->
							<th>1</th>
							<th>2</th>
							<th>3</th>
							<th>4</th>
							<th>5</th>
							<th>6</th>
							<th>7</th>
							<th>8</th>
							<th>9</th>
							<th>10</th>
							<th>11</th>
							<th>12</th>
							<th>13</th>
							<th>14</th>
							<th>15</th>
							<th>16</th>
							<th>17</th>
							<th>18</th>
							<th>19</th>
							<th>20</th>
							<th>21</th>
							<th>22</th>
							<th>23</th>
							<th>24</th>
							<th>25</th>
							<th>26</th>
							<th>27</th>
							<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=28){ ?><th>28</th><?php } ?> <!-- Fleksibel tanggal bro-->
							<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?><th>29</th><?php } ?> <!-- Fleksibel tanggal bro-->
							<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?><th>30</th><?php } ?> <!-- Fleksibel tanggal bro-->
						    <?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><th>31</th><?php } ?> <!-- Fleksibel tanggal bro-->
							

						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach ($list_template as $ls): $no++ ?>
							<tr>																													
								<td><?php echo $no;?></td>									
								<td><?php echo $ls->kdregu;?></td>								
								<td><?php echo $ls->bulan.'/'.$ls->tahun;?></td>															
								<td><?php echo $ls->kd_opt;?></td>															
								<!--td><?php echo $ls->eosf_index;?></td>															
								<td><?php echo $ls->eosf_val;?></td>															
								<td><?php echo $ls->eor_val;?></td-->															
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-01'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_1;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-02'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_2;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-03'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_3;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-04'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_4;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-05'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_5;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-06'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_6;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-07'; ?> href="<?php echo '#';?>"><?php echo $ls->m01_7;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-08'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_8;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-09'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_9;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-10'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_10;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-11'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_11;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-12'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_12;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-13'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_13;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-14'; ?> href="<?php echo '#';?>"><?php echo $ls->m02_14;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-15'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_15;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-16'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_16;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-17'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_17;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-18'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_18;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-19'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_19;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-20'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_20;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-21'; ?> href="<?php echo '#';?>"><?php echo $ls->m03_21;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-22'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_22;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-23'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_23;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-24'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_24;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-25'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_25;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-26'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_26;?></a></td>								
								<td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-27'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_27;?></a></td>								
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=28){ ?><td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-28'; ?> href="<?php echo '#';?>"><?php echo $ls->m04_28;?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->					
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?><td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-29'; ?> href="<?php echo '#';?>"><?php echo $ls->m05_29;?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->					
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?><td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-30'; ?> href="<?php echo '#';?>"><?php echo $ls->m05_30;?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->					
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><td><a <?php $kd_opt=trim($ls->kd_opt); $bln=trim($ls->bulan); $thn=trim($ls->tahun); $tgl=$thn.'-'.$bln.'-31'; ?> href="<?php echo '#';?>"><?php echo $ls->m05_31;?></a></td><?php } ?>			<!-- Fleksibel tanggal bro-->					
									
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div><!-- /.row --> 
 <div class="modal-footer">
      
		<div class="form-group">
				<input name="bln_akhir" value="<?php echo $bulan;?>"  class="form-control"  type="hidden">
				<input name="thn_akhir" value="<?php echo $tahun;?>"  class="form-control"  type="hidden">
				<input name="kdregu" value="<?php echo $kdregu;?>"  class="form-control"  type="hidden">
				<input name="type_gr" value="<?php echo $type_gr;?>"  class="form-control"  type="hidden">
		</div>		
		<a href="<?php echo site_url('trans/jadwal_new/index');?>"  class="btn btn-dismiss" >Cancel</a>
		<button type="submit"  class="btn btn-primary">SUBMIT</button>
		</form>
</div>
	 
					
