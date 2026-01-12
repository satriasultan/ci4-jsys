<?php

$this->session = \Config\Services::session();
?>

<script type="text/javascript">
    $(function() {
        $("#example1").DataTable( {
            "scrollY": false,
            "scrollX": 2700
        } );
    });
</script>
<style>

</style>
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
<h3><?php echo $title; ?></h3>
<?php echo $message;?>

<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
		<a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal1" style="margin:10px"><i class="glyphicon glyphicon-plus"></i> INPUT</a>
	</div><!-- /.box-header -->
		<div class="box-body">
			<table id="example1" class="table table-bordered table-striped cell-border compact display"  style="width:100%" >
				<thead>
					<tr>
						<th>No.</th>
                        <th>Aksi</th>
                        <th>Hold</th>
						<th>Kode</th>
						<th>Jenis Jam </th>
						<th>Shift Ke</th>
						<th>Jam Masuk</th>
						<th>Jam Masuk Minimal</th>
						<th>Jam Masuk Maksimal</th>
						<th>Jam Istirahat </th>
						<th>Jam Istirahat Mulai Minimal</th>
						<th>Jam Istirahat Mulai Maksimal</th>
						<th>Jam Istirahat Selesai </th>
						<th>Jam Istirahat Selesai Minimal</th>
						<th>Jam Istirahat Selesai Maksimal</th>
						<th>Jam Pulang</th>
						<th>Jam Pulang Minimal</th>
						<th>Jam Pulang Maksimal</th>
						<th>Kd Hari Pulang</th>
						<th>Keterangan</th>


					</tr>
				</thead>
				<tbody>
				<?php $no=0; foreach($list_jam_kerja as $row): $no++;?>
			<tr>

				<td width="1"><?php echo $no;?></td>
                <td>
                    <a href="<?php echo base_url('master/jam_kerja/hps_jam_kerja').'/'.$row->kdjam_kerja;?>" OnClick="return confirm('Anda Yakin Hapus <?php echo trim($row->kdjam_kerja);?>?')" class="btn btn-danger btn-sm" title="Hapus Jam Kerja"><i class="fa  fa-trash-o"></i></a>
                    <a data-bs-toggle="modal" data-bs-target="#<?php echo trim($row->kdjam_kerja);?>" href="#" class="btn btn-primary btn-sm" title="Ubah/Non Aktif Jam Kerja"><i class="fa  fa-gear"></i></a>
                </td>
                <td><?php echo $row->chold;?></td>
				<td><?php echo $row->kdjam_kerja;?></td>
				<td><?php echo $row->nmjam_kerja;?></td>
				<td><?php echo $row->shiftke;?></td>
				<td><?php echo $row->jam_masuk;?></td>
				<td><?php echo $row->jam_masuk_min;?></td>
				<td><?php echo $row->jam_masuk_max;?></td>
				<td><?php echo $row->jam_istirahat;?></td>
				<td><?php echo $row->jam_istirahat_min;?></td>
				<td><?php echo $row->jam_istirahat_max;?></td>
				<td><?php echo $row->jam_istirahatselesai;?></td>
				<td><?php echo $row->jam_istirahatselesai_min;?></td>
				<td><?php echo $row->jam_istirahatselesai_max;?></td>
				<td><?php echo $row->jam_pulang;?></td>
				<td><?php echo $row->jam_pulang_min;?></td>
				<td><?php echo $row->jam_pulang_max;?></td>
				<td><?php echo $row->kdharipulang;?></td>
				<td><?php echo $row->keterangan;?></td>


			</tr>
			<?php endforeach;?>
				</tbody>
			</table>
		</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
<!-- Modal Input jam_kerja -->

<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="myModalLabel">INPUT MASTER JAM KERJA</h4>
	  </div>

		<div class="modal-body">
		<form role="form" action="<?php echo base_url('master/jam_kerja/add_jam_kerja');?>" method="post">
			<div class="row">
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Kode Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="kddept" name="kdjam_kerja"  class="form-control" style="text-transform:uppercase" maxlength="4" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Nama Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="nmjam_kerja"  maxlength="20" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Shift Ke</label>
				<div class="col-sm-12">
					<select class="form-control input-sm" name="shiftke" id="kdgrade">
						  <option  value="1" class="form-control">I</option>
						  <option  value="2" class="form-control">II</option>
						  <option  value="3" class="form-control">III</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_min" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_max" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Lintas Hari Pulang</label>
				<div class="col-sm-12">
					<select class="form-control input-sm" name="kdharipulang" id="kdharipulang">
						  <option value="" class="form-control">TIDAK</option>
						  <option value="H+" class="form-control">YA H+1</option>
						  <!--option  <?php if (trim($lk->shiftke=='3')) { echo 'selected';}?> value="3" class="form-control">III</option-->
					</select>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Tanggal Input</label>
				<div class="col-sm-12">

						<input type="text" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>

					<!-- /.input group -->
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Input By</label>
				<div class="col-sm-12">

						<input type="text" id="inputby" name="inputby"  value="<?php echo $this->session->get('nik');?>" class="form-control" readonly >

					<!-- /.input group -->
				</div>
			</div>
            <div class="form-group">
                <label class="col-sm-12">Hold</label>
                <div class="col-sm-12">
                    <select class="form-control input-sm" name="chold" id="chold">
                        <option  value="NO" class="form-control"> NO </option>
                        <option  value="YES" class="form-control"> YES </option>
                    </select>
                </div>
            </div>
			</div>
			</div>

			<div class="row">
			<div class="form-group">
				 <label class="col-sm-12">Keterangan</label>
				<div class="col-sm-12">

						<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"></textarea>

					<!-- /.input group -->
				</div>
			</div>

			</div>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<div class="col-lg-12">
						<button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
					   <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
					</div>
				</div>
			</div>
			</div>
		</form>
  </div>
</div>
</div>
</div>
<!--Edit Department -->
<?php foreach ($list_jam_kerja as $lk){?>

<div class="modal fade" id="<?php echo trim($lk->kdjam_kerja);?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="myModalLabel">UBAH MASTER JAM KERJA </h4>
	  </div>

		<div class="modal-body">
		<form role="form" action="<?php echo base_url('master/jam_kerja/edit_jam_kerja');?>" method="post">
			<div class="row">
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Kode Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="kddept" name="kdjam_kerja"  class="form-control" value="<?php echo $lk->kdjam_kerja;?>" maxlength="3" readonly>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Nama Jam Kerja</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="nmjam_kerja" value="<?php echo $lk->nmjam_kerja;?>" maxlength="20" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Shift Ke</label>
				<div class="col-sm-12">
					<select class="form-control input-sm" name="shiftke" id="kdgrade">
						  <option  <?php if (trim($lk->shiftke=='1')) { echo 'selected';}?> value="1" class="form-control">I</option>
						  <option  <?php if (trim($lk->shiftke=='2')) { echo 'selected';}?> value="2" class="form-control">II</option>
						  <option  <?php if (trim($lk->shiftke=='3')) { echo 'selected';}?> value="3" class="form-control">III</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk" value="<?php echo $lk->jam_masuk;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_min" value="<?php echo $lk->jam_masuk_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Masuk Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_masuk_max" value="<?php echo $lk->jam_masuk_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat" value="<?php echo $lk->jam_istirahat;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_min" value="<?php echo $lk->jam_istirahat_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahat_max" value="<?php echo $lk->jam_istirahat_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai</label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai" value="<?php echo $lk->jam_istirahatselesai;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_min" value="<?php echo $lk->jam_istirahatselesai_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Istirahat Selesai Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_istirahatselesai_max" value="<?php echo $lk->jam_istirahatselesai_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang" value="<?php echo $lk->jam_pulang;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Minimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_min" value="<?php echo $lk->jam_pulang_min;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Jam Pulang Maksimal </label>
				<div class="col-sm-12">
					<input type="text" id="nmdept" name="jam_pulang_max" value="<?php echo $lk->jam_pulang_max;?>" data-inputmask='"mask": "99:99"' data-mask="" style="text-transform:uppercase" class="form-control" required>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Lintas Hari Pulang</label>
				<div class="col-sm-12">
					<select class="form-control input-sm" name="kdharipulang" id="kdharipulang">
						  <option <?php if (trim($lk->kdharipulang=='')) { echo 'selected';}?> value="" class="form-control">TIDAK</option>
						  <option <?php if (trim($lk->kdharipulang=='H+')) { echo 'selected';}?> value="H+" class="form-control">YA H+1</option>
						  <!--option  <?php if (trim($lk->shiftke=='3')) { echo 'selected';}?> value="3" class="form-control">III</option-->
					</select>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Tanggal Input</label>
				<div class="col-sm-12">

						<input type="text" id="tgl1" name="tgl"  value="<?php echo date('d-m-Y H:i:s');?>"class="form-control" readonly>

					<!-- /.input group -->
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-12">Input By</label>
				<div class="col-sm-12">

						<input type="text" id="inputby" name="inputby"  value="<?php echo $this->session->get('nik');?>" class="form-control" readonly >

					<!-- /.input group -->
				</div>
			</div>
            <div class="form-group">
                <label class="col-sm-12">Hold</label>
                <div class="col-sm-12">
                    <select class="form-control input-sm" name="chold" id="chold">
                        <option  <?php if (trim($lk->chold)==='NO') { echo 'selected';}?> value="NO" class="form-control"> NO </option>
                        <option  <?php if (trim($lk->chold)==='YES') { echo 'selected';}?> value="YES" class="form-control"> YES </option>
                    </select>
                </div>
            </div>
			</div>
			</div>

			<div class="row">
			<div class="form-group">
				 <label class="col-sm-12">Keterangan</label>
				<div class="col-sm-12">

						<textarea type="text" id="nmdept" name="keterangan"   style="text-transform:uppercase" class="form-control"><?php echo $lk->keterangan;?></textarea>

					<!-- /.input group -->
				</div>
			</div>
			</div>
			</div>
			<div class="modal-footer">
				<div class="form-group">
						<button type='submit' class='btn btn-primary' ><i class="fa fa-download"></i> Simpan </button>

				</div>
			</div>
		</div>
		</form>
  </div>
</div>
</div>
</div>
<?php }?>
<script>

  $("[data-mask]").inputmask();


	//Date range picker
    $('#tgl').datepicker();



</script>
