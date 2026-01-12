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

<ol class="breadcrumb">
    <div class="pull-right"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
    <?php foreach ($y as $y1) { ?>
        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
            <li><a href="<?php echo site_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
        <?php } else { ?>
            <li class="active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
        <?php } ?>
    <?php } ?>
</ol>

<legend><?php echo $title;?></legend>

<div class="row">
    <div class="col-sm-12">
        <a href="cutibalance" class="btn btn-info" style="margin:5px"> Kembali</a>
        <?php if ($role!=='ESS') { ?>
                <a href="<?php echo site_url("trans/cuti_karyawan/excel_blc_dtl/$tahun/$nik")?>"  class="btn btn-default" style="margin:10px;">Export Excel</a>
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
							<th>TANGGAL</th>
							<th>NO DOKUMEN</th>
							<!--th>TIPE DOKUMEN</th-->
							<th>IN CUTI</th>
							<th>OUT CUTI</th>
							<th>TOTAL CUTI</th>
							<th>AWAL</th>
							<th>AKHIR</th>
							<th>DESKRIPSI</th>
							<!--th>STATUS</th-->
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
							<!--td><?php echo $lb->doctype;?></td-->
							<td align='right'><b><?php echo $lb->in_cuti;?></b></td>
							<td align='right'><b><?php echo $lb->out_cuti;?></b></td>
							<td align='right'><b><?php echo $lb->sisacuti;?></b></td>
							<td align='right'><b><?php echo $lb->tgl_mulai;?></b></td>
							<td align='right'><b><?php echo $lb->tgl_selesai;?></b></td>
							<td align='right'><b><?php echo trim($lb->description);?></b></td>
							<!--td><?php echo $lb->status;?></td--->


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
			<form class="form-horizontal" action="<?php echo site_url('trans/cuti_karyawan/adjusment_cuti');?>" method="post">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Tutup</span></button>
				<h4 class="modal-title" id="myModalLabel">Penyesuaian/Ajustment Balance Cuti</h4>
			</div>
			<div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<div class="box box-danger">
						<div class="box-body">
							<div class="form-horizontal">

								<div class="form-group">
									<label class="col-sm-4">Kategori</label>
									<div class="col-sm-8">
										<select id="kategori" name="kategori" class="form-control" required>
											<option value="+">TAMBAH</option>
											<option value="-">KURANG</option>

										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4">Jumlah Cuti Yang Disisipkan</label>
								<div class="col-sm-8">
									<input type="number" id="jumlah" name="jumlah" class="form-control">
									<input type="hidden" id="nik" name="nik" value="<?php echo $nik;?>" class="form-control">
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


