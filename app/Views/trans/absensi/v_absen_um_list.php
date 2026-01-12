<?php 
/*
	@author : hanif_anak_metal \m/
*/
error_reporting(0);
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $('#example2').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bInfo": true,
                    "bSort": true,
                    "bAutoWidth": false
                });
            });
</script>
<legend>Daftar Absensi Tanggal <?php echo $tgl;?></legend>

<div class="row">
                        <div class="col-xs-12">                            
                            <div class="box">
								<div class="box-header">
								
									<form action="<?php echo site_url('trans/absensi/pdf');?>" name="form" role="form" method="post">		
										<input type="hidden" name='branch' value="<?php echo $branch;?>">
										<input type="hidden" name='tgl' value="<?php echo $tgl;?>">										
										<button type="submit" class="btn btn-primary" style="margin:10px">
										
										<i class="glyphicon glyphicon-file"></i> PDF</button>
										<a href="#" data-bs-toggle="modal" data-bs-target="#filter" class="btn btn-success" style="margin:10px; color:#ffffff;">FILTER</a>
									</form>
									
                                </div><!-- /.box-header -->
								
                                <div class="box-body table-responsive">
                                    <table id="example1" class="table table-bordered table-striped" >
                                        <thead>
											<tr>
												<th>No.</th>
												<th>Nama</th>
												<th>Departemen</th>
												<th>Jabatan</th>
												<th>Tanggal</th>
												<th>Checktime</th>																					
												<th>Keterangan</th>												
												<th>Uang Makan</th>												
											</tr>
										</thead>
                                        <tbody>
                                            <?php $no=0; foreach($list_um as $um){ $no++;?>
											<tr>
												<td><?php echo $no;?></td>
												<td><?php if ($um->badgenumber=='TOTAL') { echo '<b>TOTAL UANG MAKAN: '.$um->nmlengkap.'</b>';} 
														else if ($um->badgenumber=='GRAND TOTAL'){ echo '<b>GRAND TOTAL UANG MAKAN</b>';}
														else { echo $um->nmlengkap;}?></td>
												<td><?php echo $um->departement;?></td>
												<td><?php if (trim($um->badgenumber)<>'TOTAL' and trim($um->badgenumber)=='GRAND TOTAL') { echo '';}
													else if(trim($um->badgenumber)<>'TOTAL' and trim($um->badgenumber)<>'GRAND TOTAL'){ echo $um->deskripsi;}	
													?></td>
												<td><?php if (!empty($um->checkdate)) { echo $um->checkdate.', '.$um->hari;}?></td>
												<td><?php if  (!empty($um->checkin) or !empty($um->checkout)) { echo $um->checkin.'|';?><?php echo $um->checkout;}?></td>
												<td><?php echo $um->ket;?></td>												
												<td><?php echo $um->uangmakan;?></td>
											</tr>
											<?php }?>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
</div>
<!--Modal untuk Filter-->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filter Laporan Absensi</h4>
      </div>
	  <form action="<?php echo site_url('trans/absensi/list_um');?>" name="form" role="form" method="post">	
      <div class="modal-body">
        <div class="form-group input-sm ">		
			<label class="label-form col-sm-3">Wilayah</label>
				<div class="col-lg-9">    
					<select class='form-control' name="branch">
						<?php foreach ($fingerprintwil as $fpwil){?>
							<option value="<?php echo $fpwil->ipaddress;?>"><?php echo $fpwil->wilayah;?></option>
						<?php }?>
					</select>
				</div>
		</div>		
		<div class="form-group input-sm ">		
			<label class="label-form col-sm-3">Tanggal</label>
			<div class="col-lg-9">
			<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" id="tgl" name="tgl" data-date-format="dd-mm-yyyy" class="form-control pull-right">
				</div><!-- /.input group -->
			</div>	
		</div>

    
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit1" class="btn btn-success">Process</button>
      </div>
	  </form>
    </div>
  </div>
</div>


<script>
	//Date range picker
    $('#tgl').daterangepicker();
</script>