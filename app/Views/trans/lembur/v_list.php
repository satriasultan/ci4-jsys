<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 3/29/21, 4:17 PM
 * Path Directory: v_list.php
 */

?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
                $("#example2").dataTable();
                $("#example3").dataTable();
				$("#dateinput").datepicker();
				$("#dateinput1").datepicker();
				$("#dateinput2").datepicker();
				$("#dateinput3").datepicker();
				$("[data-mask]").inputmask();
				$('#pilihkaryawan').selectize();
            });
</script>
 <script>
    /*$(document).ready(function() {
        function disableBack() { window.history.forward() }

        window.onload = disableBack();
        window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
    });*/
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
<h3><?php echo $title; ?></h3>
<?php echo $message;?>


<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="<?= $tab_active_1 ?>"><a href="#tab_1" data-bs-toggle="tab"><b>DETAIL</b></a></li>
        <li class="<?= $tab_active_2 ?>"><a href="#tab_2" data-bs-toggle="tab" style="background-color: #c7e9bb"><b>RESUME</b></a></li>
    </ul>
</div>
<div class="tab-content">
    <div class="tab-pane <?= $tab_active_1 ?>" id="tab_1" style="position: relative;" >
        <div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a>-->
                    <?php if (trim($dtl_akses['a_input'])==='t') { ?>
					<a href="<?php echo base_url("trans/lembur/input_new")?>"  class="btn btn-primary" style="margin:0px; color:#ffffff;"><i class="fa fa-plus"></i>  Input</a>
                    <?php } ?>
					<a href="#" data-bs-toggle="modal" data-bs-target="#filter" class="btn btn-default" style="margin:10px; color:#000000;"><i class="fa fa-filter"></i> Filter </a>
                    <?php if (trim($dtl_akses['a_report'])==='t') { ?>
                        <a href="#"  data-bs-toggle="modal" data-bs-target="#downloadXls"  class="btn btn-default" style="margin:0px; color:#000000;"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                        <a href="#"  data-bs-toggle="modal" data-bs-target="#recalculate"  class="btn btn-warning" style="margin:0px; color:#000000;"><i class="fa fa-file-excel-o"></i> Recalculate</a>
                    <?php } ?>
				</div>

			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="example1" class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>No.</th>
							<!--<th>NIK</th>
							<th>Nama Karyawan</th>-->

                            <th>Action</th>
							<th>Dokumen</th>
							<th>NIK</th>
							<th>Nama</th>
							<th>Department</th>
							<th>Section</th>
							<th>Group</th>
							<th>Plant</th>
							<th>Tanggal</th>
							<th>Jam (Mati)</th>
							<th>Jam (Hidup)</th>
							<th>Jenis</th>
							<th>Keterangan</th>
							<th>Status</th>
							<th>Inputby</th>

						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_lembur as $lu): $no++;?>
						<tr>
							<td width="2%"><?php echo $no;?></td>
                            <td width="10%">
                                <?php if (trim($lu->status)==='C' or trim($lu->status)==='D') { ?>
                                    <a class="btn btn-sm btn-default" href="#"  title="Detail Lembur" onclick="detailDocument('<?php echo trim($lu->nodok); ?>');"><i class="fa fa-bars"></i> </a>
                                <?php } else { ?>
                                    <a class="btn btn-sm btn-default" href="#"  title="Detail Lembur" onclick="detailDocument('<?php echo trim($lu->nodok); ?>');"><i class="fa fa-bars"></i> </a>
                                    <?php if (trim($dtl_akses['a_update'])==='t') { ?>
                                        <a class="btn btn-sm btn-primary" href="#"  title="Ubah Lembur" onclick="editDocument('<?php echo trim($lu->nodok); ?>');"><i class="fa fa-gear"></i> </a>
                                    <?php } ?>
                                    <?php if (trim($dtl_akses['a_delete'])==='t') { ?>
                                        <a  href="<?php $nik=trim($lu->nik); echo base_url("trans/lembur/hps_lembur").'/'.trim($lu->nodok) ?>" onclick="return confirm('Anda Yakin Membatalkan Data ini?')" class="btn btn-danger  btn-sm"  title="Cancel Lembur">
                                            <i class="fa fa-close"></i>
                                        </a>
                                    <?php }  ?>
                                <?php }  ?>
                            </td>
							<td><?php echo $lu->nodok;?></td>
							<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>
							<td><?php echo $lu->nmdept;?></td>
							<td><?php echo $lu->nmsubdept;?></td>
							<td><?php echo $lu->nmgroupgol;?></td>
							<td><?php echo $lu->locaname;?></td>
							<td width="5%"><?php echo $lu->tgl_kerja1;?></td>
							<td width="2%" class="ratakanan"><?php echo str_replace('.',',',$lu->durasijam) ;?></td>
							<td width="2%" class="ratakanan"><?php echo str_replace('.',',',$lu->durasihidup);?></td>
							<td><?php echo trim($lu->kdlembur);?></td>
							<td><?php echo $lu->keterangan;?></td>
							<td><?php echo $lu->nmstatus;?></td>
							<td><?php echo $lu->input_by;?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
    </div>
    <div class="tab-pane <?= $tab_active_2 ?>" id="tab_2" style="position: relative; height: 300px;" >
            <div class="row">
                <div class="col-sm-12">
                    <div class="box" style="background-color: #c7e9bb">
                        <div class="box-title" align="center">
                            <h2> Resume Per - Departmen Lembur Periode  <?= $tglresume1.' s/d '.$tglresume2?></h2>
                        </div>
                        <div class="box-header">
                            <div class="col-sm-12">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#filterResume" class="btn btn-default" style="margin:0px; color:#000000;"><i class="fa fa-filter"></i> Filter </a>
                                <?php if (trim($dtl_akses['a_report'])==='t') { ?>
                                    <a href="#"  data-bs-toggle="modal" data-bs-target="#downloadXlsResume"  class="btn btn-default" style="margin:0px; color:#000000;"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                                <?php } ?>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive" style='overflow-x:scroll;'>
                            <table id="tLemburResume" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Departement</th>
                                    <th>Jumlah Dokumen</th>
                                    <th>Total Jam</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no=0; foreach($list_lembur_resume as $lar): $no++;?>
                                    <tr>
                                        <td width="2%"><?php echo $no;?></td>
                                        <td><?php echo $lar->nmdept;?></td>
                                        <td class="ratakanan"><?php echo $lar->jml_lembur;?></td>
                                        <td class="ratakanan"><?php echo $lar->ttljam;?></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
        </div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Periode Lembur</h4>
      </div>
	  <form action="<?php echo base_url('trans/lembur')?>" method="post">
          <div class="modal-body">
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Tanggal Lembur</label>
                  <div class="col-sm-9">
                      <input type="text" name="daterangefilter" id="daterangefilter" class="form-control input-sm daterangefilter" required>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Plant</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="plantfilter" id="plantfilter" style="width: 100%">

                      </select>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Group</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="id_gol_filter" id="id_gol_filter" style="width: 100%">

                      </select>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Department</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" id="id_dept_filter" name="id_dept_filter" style="width: 100%">

                      </select>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Cost Center</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="costcenter_filter" id="costcenter_filter" style="width: 100%">

                      </select>
                  </div>
              </div>
              <?php /*
		<div class="form-group input-sm ">
			<label class="label-form col-sm-3">Status</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" name="status">
					<option value="">SEMUA</option>
					<option value="P">DISETUJUI</option>
					<option value="A">PERLU PERSETUJUAN</option>
					<option value="C">DIBATALKAN</option>
					<option value="D">DIHAPUS</option>
				</select>
			</div>
		</div>
		<div class="form-group input-sm ">
			<label class="label-form col-sm-3">NIK</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" id="pilihkaryawan" name="nik">
					<option value="">--ALL--</option>
					<?php foreach ($list_karyawan as $ld){ ?>
					<option value="<?php echo trim($ld->nik);?>"><?php echo $ld->nik.'|'.$ld->nmlengkap;?></option>
					<?php } ?>
				</select>
			</div>
		</div>
            */ ?>
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        <button type="submit1" class="btn btn-primary">Filter</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<!--Modal untuk Filter-->
<div class="modal fade" id="downloadXls" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download File Excel Lembur</h4>
            </div>
            <form action="<?php echo base_url('trans/lembur/downloadExcel')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal Lembur</label>
                        <div class="col-sm-9">
                            <input type="text" name="daterange" id="daterange" class="form-control input-sm daterange" required>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Plant</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="plant" id="plant" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Group</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="id_gol" id="id_gol" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Department</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" id="id_dept_download" name="id_dept_download" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Cost Center</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="costcenter_download" id="costcenter_download" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <?php /*
		<div class="form-group input-sm ">
			<label class="label-form col-sm-3">Status</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" name="status">
					<option value="">SEMUA</option>
					<option value="P">DISETUJUI</option>
					<option value="A">PERLU PERSETUJUAN</option>
					<option value="C">DIBATALKAN</option>
					<option value="D">DIHAPUS</option>
				</select>
			</div>
		</div>
		<div class="form-group input-sm ">
			<label class="label-form col-sm-3">NIK</label>
			<div class="col-sm-9">
				<select class="form-control input-sm" id="pilihkaryawan" name="nik">
					<option value="">--ALL--</option>
					<?php foreach ($list_karyawan as $ld){ ?>
					<option value="<?php echo trim($ld->nik);?>"><?php echo $ld->nik.'|'.$ld->nmlengkap;?></option>
					<?php } ?>
				</select>
			</div>
		</div>
            */ ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Modal untuk Recalculite-->
<div class="modal fade" id="recalculate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Kalkulasi Ulang Nilai Lembur</h4>
            </div>
            <form action="<?php echo base_url('trans/lembur/recalculate')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal Kalkulasi</label>
                        <div class="col-sm-9">
                            <input type="text" name="daterangerecalculate" id="daterangerecalculate" class="form-control input-sm" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal untuk Filter Resume -->
<div class="modal fade" id="filterResume" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Filter Pencarian</h4>
            </div>
            <form action="<?php echo base_url('trans/lembur')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal</label>
                        <div class="col-sm-9">
                            <input type="text" name="daterangefilterResume" id="daterangefilterResume" class="form-control input-sm daterangefilter" required>
                            <input type="hidden" name="tab" id="tab" value="2">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit1" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal untuk Download Resume -->
<div class="modal fade" id="downloadXlsResume" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download</h4>
            </div>
            <form action="<?php echo base_url('trans/lembur/downloadResumeExcel')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal</label>
                        <div class="col-sm-9">
                            <input type="text" name="daterangeExcelResume" id="daterangeExcelResume" class="form-control input-sm daterangefilter" required>
                            <input type="hidden" name="tab" id="tab" value="2">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit1" class="btn btn-primary">Download Xlsx</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/lembur.js') ?>"></script>




