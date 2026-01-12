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
                $(".tgl").datepicker();


            });
</script>


<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>
<legend><?php echo $title." Tahun ". $tahune;?></legend>
<!--?php echo $message;?-->
<div class="row">
    <div class="col-sm-12">
			<a href="#" data-bs-toggle="modal" data-bs-target="#filter" class="btn btn-primary" style="margin:0px; color:#ffffff;">FILTER</a>
                <?php if ($role!=='ESS') { ?>
			<a href="pr_hitungallcuti" class="btn btn-danger" style="margin:0px; color:#ffffff;"> HITUNG CUTI </a>
			<a href="<?php echo base_url("trans/abscut/excel_blc/$tahune")?>"  class="btn btn-default" style="margin:0px;">Export Excel</a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#rebalanceCuti" class="btn btn-primary" style="margin:0px; color:#ffffff;"><i class="fa fa-calculator">  CUTI TGL MASUK KERJA</i></a>
                <?php } ?>
    </div>
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
							<th>DEPARTEMENT</th>
							<th>SUB DEPART</th>
							<th>JABATAN</th>
							<th>LAST DATE</th>
							<!--th>TIPE DOKUMEN</th-->
							<!--th>IN CUTI</th>
							<th>OUT CUTI</th-->
							<th>SISA CUTI</th>
							<!--th>STATUS</th-->
							<th>ACTION</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($listblc as $lb): $no++;?>
						<tr>
							<td><?php echo $no;?></td>
							<td><?php echo $lb->nik;?></td>
							<td><?php echo $lb->nmlengkap;?></td>
							<td><?php echo $lb->nmdept;?></td>
							<td><?php echo $lb->nmsubdept;?></td>
							<td><?php echo $lb->nmjabatan;?></td>
							<td><?php echo $lb->tanggal;?></td>
							<!--td><?php echo $lb->doctype;?></td-->
							<!--td><?php echo $lb->in_cuti;?></td>
							<td><?php echo $lb->out_cuti;?></td-->
							<td align='RIGHT'><b><?php echo $lb->sisacuti;?></b></td>
							<!--td><?php echo $lb->status;?></td-->
							<td>
                                <form action="<?php echo base_url('/trans/abscut/cutibalancedtl');?>" method="post" >
                                    <input type="hidden" value="<?php echo trim($lb->nik);?>" name='kdkaryawan'>
                                    <input type="hidden" value="<?php echo $tahune;?>" name='tahunlek'>
                                    <button type='submit' class="btn btn-success">Detail</button>
								</form>
							</td>


						</tr>
						<?php endforeach;?>
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
        <h4 class="modal-title" id="myModalLabel">Periode Inquiry Cuti</h4>
      </div>
	  <form action="<?php echo base_url('trans/abscut/cutibalance')?>" method="post">
      <div class="modal-body">
		<div class="form-group input-sm ">
			<label class="label-form col-sm-3">Tahun</label>
			<div class="col-sm-9">
				<select id="tahun" Name='pilihtahun' required>
										<option value="">--Pilih Tahun--></option>
										<?php
										for ($ngantukjeh=2022; $ngantukjeh>2010; $ngantukjeh--)
										  {
											echo'<option value="'.$ngantukjeh.'">'.$ngantukjeh.'</option>';
										  }
										?>
				</select>
			</div>
		</div>
		<div class="form-group input-sm ">
			<label class="label-form col-sm-3">Pilih Department</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" id="dept" name="lsdept" >
				    <option value="">--Pilih Department--></option>
					<?php foreach ($listdepartmen as $db){?>
					<option value="<?php echo trim($db->kddept);?>"><?php echo $db->nmdept;?>
					</option>
					<?php }?>

				</select>

			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit1" class="btn btn-primary">Filter</button>
      </div>
	  </form>
    </div>
  </div>
</div>


<!--Modal untuk rebalanceCuti-->
<div class="modal fade" id="rebalanceCuti" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Penyesuaian Balance Cuti</h4>
            </div>
            <form action="<?php echo base_url('trans/abscut/rebalance_ultah')?>" method="post">
                <div class="modal-body">

                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Pilih Tanggal Ultah Karyawan</label>
                        <div class="col-sm-9">
                            <input type="text" id="tgldok" name="tgldok"  value="<?php echo date('d-m-Y');?>" class="form-control tgl" style="text-transform:uppercase"  data-date-format="dd-mm-yyyy" required>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit1" class="btn btn-primary">HitungCuti</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Hitung Cuti Karyawan-->
	<div class="modal fade baru"  role="dialog" >
	  <div class="modal-dialog modal-sm-12">
		<div class="modal-content">
			<form class="form-horizontal" action="<?php echo base_url('trans/abscut/cutibalance');?>" method="post">
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
										<select id="moduser" name="kdkaryawan" required>
											<option value="">--Pilih NIK || Nama Karyawan--></option>
											<?php foreach ($listkaryawan as $db){?>
											<option value="<?php echo trim($db->nik);?>"><?php echo str_pad($db->nik,50).' || '.str_pad($db->nmlengkap,50);?></option>
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




