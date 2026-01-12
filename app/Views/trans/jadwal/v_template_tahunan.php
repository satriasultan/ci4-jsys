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
	<a href="<?php echo site_url('trans/jadwal_new/index');?>"  class="btn btn-primary" >Kembali</a>
		<div class="box">
			<div class="box-header">
			
				<div class="col-sm-12">
					
				</div>
			</div>	
   <!--form action="<?php echo site_url('trans/jadwal_new/gr_jadwal_perbulan')?>" method="post"-->
	<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table1" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>																
							<th>No.</th>
							<th>KDREGU</th>
							<th>BLN/THN</th>
							<th>KDOPTION</th>
							<th>START INDEX</th>
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
							<th>END INDEX</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach ($list_template as $ls): $no++ ?>
							<tr>			
							<?php
								
	
								?>
								<td><?php echo $no;?></td>									
								<td><?php echo $ls->kdregu;?></td>								
								<td><?php echo $ls->bulan.'/'.$ls->tahun;?></td>															
								<td><?php echo $ls->kd_opt;?></td>															
								<td><?php echo $ls->noindex_start;?></td>															
								<td><?php if(trim($ls->m01_1)=='3' or trim($ls->m01_1)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_1)."</b></div>";
								} else if (trim($ls->m01_1)=='2' or trim($ls->m01_1)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_1)."</b></div>";
								} else if (trim($ls->m01_1)=='1' or trim($ls->m01_1)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_1)."</b></div>";
								} elseif (trim($ls->m01_1)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_1)."</b></div>";
								} else {
									echo ($ls->m01_1);
								}	
								?></td>								
								<td><?php if(trim($ls->m01_2)=='3' or trim($ls->m01_2)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_2)."</b></div>";
								} else if (trim($ls->m01_2)=='2' or trim($ls->m01_2)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_2)."</b></div>";
								} else if (trim($ls->m01_2)=='1' or trim($ls->m01_2)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_2)."</b></div>";
								} elseif (trim($ls->m01_2)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_2)."</b></div>";
								}else {
									echo ($ls->m01_2);
								}	?></td>								
								<td><?php if(trim($ls->m01_3)=='3' or trim($ls->m01_3)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_3)."</b></div>";
								} else if (trim($ls->m01_3)=='2' or trim($ls->m01_3)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_3)."</b></div>";
								} else if (trim($ls->m01_3)=='1' or trim($ls->m01_3)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_3)."</b></div>";
								} elseif (trim($ls->m01_3)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_3)."</b></div>";
								}else {
									echo ($ls->m01_3);
								}	?></td>								
								<td><?php 							if(trim($ls->m01_4)=='3' or trim($ls->m01_4)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_4)."</b></div>";
								} else if (trim($ls->m01_4)=='2' or trim($ls->m01_4)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_4)."</b></div>";
								} else if (trim($ls->m01_4)=='1' or trim($ls->m01_4)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_4)."</b></div>";
								} elseif (trim($ls->m01_4)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_4)."</b></div>";
								}else {
									echo ($ls->m01_4);
								}	?></td>								
								<td><?php							if(trim($ls->m01_5)=='3' or trim($ls->m01_5)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_5)."</b></div>";
								} else if (trim($ls->m01_5)=='2' or trim($ls->m01_5)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_5)."</b></div>";
								} else if (trim($ls->m01_5)=='1' or trim($ls->m01_5)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_5)."</b></div>";
								} elseif (trim($ls->m01_5)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_5)."</b></div>";
								}else {
									echo ($ls->m01_5);
								}	?></td>								
								<td><?php 							if(trim($ls->m01_6)=='3' or trim($ls->m01_6)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_6)."</b></div>";
								} else if (trim($ls->m01_6)=='2' or trim($ls->m01_6)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_6)."</b></div>";
								} else if (trim($ls->m01_6)=='1' or trim($ls->m01_6)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_6)."</b></div>";
								} elseif (trim($ls->m01_6)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_6)."</b></div>";
								}else {
									echo ($ls->m01_6);
								}	?></td>								
								<td><?php 							if(trim($ls->m01_7)=='3' or trim($ls->m01_7)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m01_7)."</b></div>";
								} else if (trim($ls->m01_7)=='2' or trim($ls->m01_7)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m01_7)."</b></div>";
								} else if (trim($ls->m01_7)=='1' or trim($ls->m01_7)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m01_7)."</b></div>";
								} elseif (trim($ls->m01_7)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m01_7)."</b></div>";
								}else {
									echo ($ls->m01_7);
								}	?></td>								
								<td><?php							if(trim($ls->m02_8)=='3' or trim($ls->m02_8)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_8)."</b></div>";
								} else if (trim($ls->m02_8)=='2' or trim($ls->m02_8)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_8)."</b></div>";
								} else if (trim($ls->m02_8)=='1' or trim($ls->m02_8)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_8)."</b></div>";
								} elseif (trim($ls->m02_8)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_8)."</b></div>";
								}else {
									echo ($ls->m02_8);
								}	?></td>								
								<td><?php							if(trim($ls->m02_9)=='3' or trim($ls->m02_9)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_9)."</b></div>";
								} else if (trim($ls->m02_9)=='2' or trim($ls->m02_9)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_9)."</b></div>";
								} else if (trim($ls->m02_9)=='1' or trim($ls->m02_9)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_9)."</b></div>";
								} elseif (trim($ls->m02_9)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_9)."</b></div>";
								}else {
									echo ($ls->m02_9);
								}	?></td>								
								<td><?php							if(trim($ls->m02_10)=='3' or trim($ls->m02_10)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_10)."</b></div>";
								} else if (trim($ls->m02_10)=='2' or trim($ls->m02_10)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_10)."</b></div>";
								} else if (trim($ls->m02_10)=='1' or trim($ls->m02_10)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_10)."</b></div>";
								} elseif (trim($ls->m02_10)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_10)."</b></div>";
								}else {
									echo ($ls->m02_10);
								}	?></td>								
								<td><?php							if(trim($ls->m02_11)=='3' or trim($ls->m02_11)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_11)."</b></div>";
								} else if (trim($ls->m02_11)=='2' or trim($ls->m02_11)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_11)."</b></div>";
								} else if (trim($ls->m02_11)=='1' or trim($ls->m02_11)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_11)."</b></div>";
								} elseif (trim($ls->m02_11)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_11)."</b></div>";
								}else {
									echo ($ls->m02_11);
								}	?></td>								
								<td><?php							if(trim($ls->m02_12)=='3' or trim($ls->m02_12)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_12)."</b></div>";
								} else if (trim($ls->m02_12)=='2' or trim($ls->m02_12)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_12)."</b></div>";
								} else if (trim($ls->m02_12)=='1' or trim($ls->m02_12)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_12)."</b></div>";
								} elseif (trim($ls->m02_12)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_12)."</b></div>";
								}else {
									echo ($ls->m02_12);
								}	?></td>								
								<td><?php							if(trim($ls->m02_13)=='3' or trim($ls->m02_13)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_13)."</b></div>";
								} else if (trim($ls->m02_13)=='2' or trim($ls->m02_13)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_13)."</b></div>";
								} else if (trim($ls->m02_13)=='1' or trim($ls->m02_13)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_13)."</b></div>";
								} elseif (trim($ls->m02_13)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_13)."</b></div>";
								}else {
									echo ($ls->m02_13);
								}	?></td>								
								<td><?php							if(trim($ls->m02_14)=='3' or trim($ls->m02_14)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m02_14)."</b></div>";
								} else if (trim($ls->m02_14)=='2' or trim($ls->m02_14)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m02_14)."</b></div>";
								} else if (trim($ls->m02_14)=='1' or trim($ls->m02_14)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m02_14)."</b></div>";
								} elseif (trim($ls->m02_14)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m02_14)."</b></div>";
								}else {
									echo ($ls->m02_14);
								}	?></td>								
								<td><?php							if(trim($ls->m03_15)=='3' or trim($ls->m03_15)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_15)."</b></div>";
								} else if (trim($ls->m03_15)=='2' or trim($ls->m03_15)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_15)."</b></div>";
								} else if (trim($ls->m03_15)=='1' or trim($ls->m03_15)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_15)."</b></div>";
								} elseif (trim($ls->m03_15)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_15)."</b></div>";
								}else {
									echo ($ls->m03_15);
								}	?></td>								
								<td><?php							if(trim($ls->m03_16)=='3' or trim($ls->m03_16)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_16)."</b></div>";
								} else if (trim($ls->m03_16)=='2' or trim($ls->m03_16)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_16)."</b></div>";
								} else if (trim($ls->m03_16)=='1' or trim($ls->m03_16)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_16)."</b></div>";
								} elseif (trim($ls->m03_16)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_16)."</b></div>";
								}else {
									echo ($ls->m03_16);
								}	?></td>								
								<td><?php							if(trim($ls->m03_17)=='3' or trim($ls->m03_17)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_17)."</b></div>";
								} else if (trim($ls->m03_17)=='2' or trim($ls->m03_17)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_17)."</b></div>";
								} else if (trim($ls->m03_17)=='1' or trim($ls->m03_17)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_17)."</b></div>";
								} elseif (trim($ls->m03_17)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_17)."</b></div>";
								}else {
									echo ($ls->m03_17);
								}	?></td>								
								<td><?php							if(trim($ls->m03_18)=='3' or trim($ls->m03_18)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_18)."</b></div>";
								} else if (trim($ls->m03_18)=='2' or trim($ls->m03_18)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_18)."</b></div>";
								} else if (trim($ls->m03_18)=='1' or trim($ls->m03_18)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_18)."</b></div>";
								} elseif (trim($ls->m03_18)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_18)."</b></div>";
								}else {
									echo ($ls->m03_18);
								}	?></td>								
								<td><?php							if(trim($ls->m03_19)=='3' or trim($ls->m03_19)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_19)."</b></div>";
								} else if (trim($ls->m03_19)=='2' or trim($ls->m03_19)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_19)."</b></div>";
								} else if (trim($ls->m03_19)=='1' or trim($ls->m03_19)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_19)."</b></div>";
								} elseif (trim($ls->m03_19)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_19)."</b></div>";
								}else {
									echo ($ls->m03_19);
								}	?></td>								
								<td><?php							if(trim($ls->m03_20)=='3' or trim($ls->m03_20)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_20)."</b></div>";
								} else if (trim($ls->m03_20)=='2' or trim($ls->m03_20)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_20)."</b></div>";
								} else if (trim($ls->m03_20)=='1' or trim($ls->m03_20)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_20)."</b></div>";
								} elseif (trim($ls->m03_20)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_20)."</b></div>";
								}else {
									echo ($ls->m03_20);
								}	?></td>								
								<td><?php							if(trim($ls->m03_21)=='3' or trim($ls->m03_21)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m03_21)."</b></div>";
								} else if (trim($ls->m03_21)=='2' or trim($ls->m03_21)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m03_21)."</b></div>";
								} else if (trim($ls->m03_21)=='1' or trim($ls->m03_21)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m03_21)."</b></div>";
								} elseif (trim($ls->m03_21)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m03_21)."</b></div>";
								}else {
									echo ($ls->m03_21);
								}	?></td>								
								<td><?php							if(trim($ls->m04_22)=='3' or trim($ls->m04_22)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_22)."</b></div>";
								} else if (trim($ls->m04_22)=='2' or trim($ls->m04_22)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_22)."</b></div>";
								} else if (trim($ls->m04_22)=='1' or trim($ls->m04_22)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_22)."</b></div>";
								} elseif (trim($ls->m04_22)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_22)."</b></div>";
								}else {
									echo ($ls->m04_22);
								}	?></td>								
								<td><?php							if(trim($ls->m04_23)=='3' or trim($ls->m04_23)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_23)."</b></div>";
								} else if (trim($ls->m04_23)=='2' or trim($ls->m04_23)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_23)."</b></div>";
								} else if (trim($ls->m04_23)=='1' or trim($ls->m04_23)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_23)."</b></div>";
								} elseif (trim($ls->m04_23)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_23)."</b></div>";
								}else {
									echo ($ls->m04_23);
								}	?></td>								
								<td><?php							if(trim($ls->m04_24)=='3' or trim($ls->m04_24)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_24)."</b></div>";
								} else if (trim($ls->m04_24)=='2' or trim($ls->m04_24)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_24)."</b></div>";
								} else if (trim($ls->m04_24)=='1' or trim($ls->m04_24)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_24)."</b></div>";
								} elseif (trim($ls->m04_24)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_24)."</b></div>";
								}else {
									echo ($ls->m04_24);
								}	?></td>								
								<td><?php							if(trim($ls->m04_25)=='3' or trim($ls->m04_25)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_25)."</b></div>";
								} else if (trim($ls->m04_25)=='2' or trim($ls->m04_25)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_25)."</b></div>";
								} else if (trim($ls->m04_25)=='1' or trim($ls->m04_25)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_25)."</b></div>";
								} elseif (trim($ls->m04_25)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_25)."</b></div>";
								}else {
									echo ($ls->m04_25);
								}	?></td>								
								<td><?php							if(trim($ls->m04_26)=='3' or trim($ls->m04_26)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_26)."</b></div>";
								} else if (trim($ls->m04_26)=='2' or trim($ls->m04_26)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_26)."</b></div>";
								} else if (trim($ls->m04_26)=='1' or trim($ls->m04_26)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_26)."</b></div>";
								} elseif (trim($ls->m04_26)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_26)."</b></div>";
								}else {
									echo ($ls->m04_26);
								}	?></td>								
								<td><?php							if(trim($ls->m04_27)=='3' or trim($ls->m04_27)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_27)."</b></div>";
								} else if (trim($ls->m04_27)=='2' or trim($ls->m04_27)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_27)."</b></div>";
								} else if (trim($ls->m04_27)=='1' or trim($ls->m04_27)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_27)."</b></div>";
								} elseif (trim($ls->m04_27)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_27)."</b></div>";
								}else {
									echo ($ls->m04_27);
								}	?></td>								
					
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=29){ ?><td> <?php							if(trim($ls->m04_28)=='3' or trim($ls->m04_28)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m04_28)."</b></div>";
								} else if (trim($ls->m04_28)=='2' or trim($ls->m04_28)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m04_28)."</b></div>";
								} else if (trim($ls->m04_28)=='1' or trim($ls->m04_28)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m04_28)."</b></div>";
								} elseif (trim($ls->m04_28)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m04_28)."</b></div>";
								}else {
									echo ($ls->m04_28);
								}	?></td><?php } ?>			<!-- Fleksibel tanggal bro-->					
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=30){ ?><td> <?php							if(trim($ls->m05_29)=='3' or trim($ls->m05_29)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m05_29)."</b></div>";
								} else if (trim($ls->m05_29)=='2' or trim($ls->m05_29)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m05_29)."</b></div>";
								} else if (trim($ls->m05_29)=='1' or trim($ls->m05_29)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m05_29)."</b></div>";
								} elseif (trim($ls->m05_29)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m05_29)."</b></div>";
								}else {
									echo ($ls->m05_29);
								}	?></td><?php } ?>			<!-- Fleksibel tanggal bro-->					
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><td><?php							if(trim($ls->m05_30)=='3' or trim($ls->m05_30)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m05_30)."</b></div>";
								} else if (trim($ls->m05_30)=='2' or trim($ls->m05_30)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m05_30)."</b></div>";
								} else if (trim($ls->m05_30)=='1' or trim($ls->m05_30)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m05_30)."</b></div>";
								} elseif (trim($ls->m05_30)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m05_30)."</b></div>";
								}else {
									echo ($ls->m05_30);
								}	?></td><?php } ?>			<!-- Fleksibel tanggal bro-->	
								<?php $a_date=$tahun.'-'.$bulan;$cekdate=date("t",strtotime($a_date)); if($cekdate>=31){ ?><td><?php							if(trim($ls->m05_31)=='3' or trim($ls->m05_31)=='3*'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#0404B4'><b>".($ls->m05_31)."</b></div>";
								} else if (trim($ls->m05_31)=='2' or trim($ls->m05_31)=='2*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#088A08'><b>".($ls->m05_31)."</b></div>";
								} else if (trim($ls->m05_31)=='1' or trim($ls->m05_31)=='1*') {
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#B40431'><b>".($ls->m05_31)."</b></div>";
								} elseif (trim($ls->m05_31)=='OFF'){
									echo "<div style ='font:15px/15px Arial,tahoma,sans-serif;color:#ff0000'><b>".($ls->m05_31)."</b></div>";
								}else {
									echo ($ls->m05_31);
								}	?></td><?php } ?>			<!-- Fleksibel tanggal bro-->									
								<td><?php echo $ls->noindex_end;?></td>																
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div><!-- /.row --> 
 <div class="modal-footer">
      
		<!--div class="form-group">
				<input name="bln_akhir" value="<?php echo $bulan;?>"  class="form-control"  type="hidden">
				<input name="thn_akhir" value="<?php echo $tahun;?>"  class="form-control"  type="hidden">
				<input name="kdregu" value="<?php echo $kdregu;?>"  class="form-control"  type="hidden">
				<input name="type_gr" value="<?php echo $type_gr;?>"  class="form-control"  type="hidden">
		</div>		
		<a href="<?php echo site_url('trans/jadwal_new/index');?>"  class="btn btn-dismiss" >Cancel</a>
		<button type="submit"  class="btn btn-primary">SUBMIT</button>
		
		</form-->
	
</div>
	 
					
