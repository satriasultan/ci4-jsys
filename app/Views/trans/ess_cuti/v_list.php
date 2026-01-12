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
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" cldass="btn btn-primary" style="margin:10px; color:#ffffff;">Input</a>-->
                    <?php if (trim($dtl_akses['a_input'])==='t') { ?>
					<a href="<?php echo base_url("trans/ess_cuti/input_new")?>"  class="btn btn-primary" style="margin:0px; color:#ffffff;"><i class="fa fa-plus"></i>  Input</a>
                    <?php } ?>
					<a href="#" data-bs-toggle="modal" data-bs-target="#filter" class="btn btn-default" style="margin:10px; color:#000000;"><i class="fa fa-filter"></i> Filter </a>

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

							<th>NIK</th>
							<th>Nama</th>
							<th>Section</th>
							<th>Tanggal</th>
							<th>Durasi</th>
							<th>Jenis</th>
							<th>Keterangan</th>
							<th>Status</th>
                            <th>Dokumen</th>
							<th>InputBy</th>
                            <th>ApproveBy</th>
                            <th>ApproveDate</th>

						</tr>
					</thead>
					<tbody>
						<?php $no=0; foreach($list_abscut as $lu): $no++;?>
						<tr>
							<td width="2%"><?php echo $no;?></td>
                            <td width="5%">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle " style="margin:0px; color:#FFFFFF;" id="menu1" type="button" data-bs-toggle="dropdown" autocomplete="off" aria-expanded="false"><i class="fa fa-gear"></i>
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" style="background: #b3efff">


                                        <?php if (trim($lu->status)==='C' or trim($lu->status)==='D') { ?>
                                            <li role="presentation"><a role="menuitem" title="Detail" onclick="detailDocument('<?php echo trim($lu->docno); ?>');"><i class="fa fa-bars"></i> Detail </a></li>
                                        <?php } else { ?>

                                            <?php if (trim($lu->statusapprove)==='A' and trim($lu->nikatasan1)===$niksession) { ?>
                                                <li role="presentation"><a role="menuitem" title="Setujui" href="<?php $nik=trim($lu->nik); echo base_url("trans/ess_cuti/setujui_abscut/").trim($lu->docno) ?>" onclick="return confirm('Anda Yakin Menyetujui Dokumen Ini?')" title="Setujui"><i class="fa fa-check-circle-o"></i> Setujui </a></li>
                                            <?php } else if (trim($lu->statusapprove)==='A1' and trim($lu->nikatasan2)===$niksession) { ?>
                                                <li role="presentation"><a role="menuitem" title="Setujui" href="<?php $nik=trim($lu->nik); echo base_url("trans/ess_cuti/setujui_abscut/").trim($lu->docno) ?>" onclick="return confirm('Anda Yakin Menyetujui Dokumen Ini?')" title="Setujui"><i class="fa fa-check-circle-o"></i> Setujui </a></li>
                                            <?php } else if (trim($lu->statusapprove)==='A2' and trim($lu->nikatasan3)===$niksession) { ?>
                                                <li role="presentation"><a role="menuitem" title="Setujui" href="<?php $nik=trim($lu->nik); echo base_url("trans/ess_cuti/setujui_abscut/").trim($lu->docno) ?>" onclick="return confirm('Anda Yakin Menyetujui Dokumen Ini?')" title="Setujui"><i class="fa fa-check-circle-o"></i> Setujui </a></li>
                                            <?php }  ?>


                                            <li role="presentation"><a role="menuitem" title="Detail" onclick="detailDocument('<?php echo trim($lu->docno); ?>');"><i class="fa fa-bars"></i> Detail </a></li>
                                            <?php if (trim($lu->attachment)!=='') { ?>
                                            <li role="presentation"><a role="menuitem" title="Lampiran" href="<?php if(!empty($lu->attachment)) { echo base_url('/assets/files/abscut/imagetrans'.'/'.trim($lu->attachment)); } else { echo '#'; } ?>"  title="Detail Attachment" <?php if(!empty($lu->attachment)) { ?> target="_blank" <?php } ?>><i class="fa fa-eye"></i> Lampiran </a></li>
                                            <?php } ?>
                                            <?php if (trim($dtl_akses['a_update'])==='t') { ?>
                                                <?php /*<!--li role="presentation"><a role="menuitem" title="Ubah" onclick="editDocument('<?php echo trim($lu->docno); ?>');"><i class="fa fa-gear"></i> Ubah </a></li--> */ ?>
                                            <?php } ?>
                                            <?php if (trim($dtl_akses['a_delete'])==='t') { ?>
                                                <li role="presentation"><a role="menuitem" title="Batalkan" href="<?php $nik=trim($lu->nik); echo base_url("trans/ess_cuti/hps_abscut").'/'.trim($lu->docno) ?>" onclick="return confirm('Anda Yakin Membatalkan Data ini?')" title="Batalkan"><i class="fa fa-trash"></i> Batalkan </a></li>
                                            <?php }  ?>

                                        <?php }  ?>
                                    </ul>
                                </div>

                            </td>

							<td><?php echo $lu->nik;?></td>
							<td><?php echo $lu->nmlengkap;?></td>
							<td><?php echo $lu->nmsubdept;?></td>
							<td width="5%"><?php echo $lu->tglawal1;?></td>
							<td width="2%" class="ratakanan"><?php echo str_replace('.',',',$lu->durasimenit) ;?></td>
							<td><?php echo trim($lu->nmtypecuti);?></td>
							<td><?php echo $lu->description;?></td>

                            <?php if (trim($lu->statusapprove) === 'A') { ?>
                                <td><span class="label label-default"><?php echo $lu->nmstatusapprove;?></span></td>
                            <?php } else if (trim($lu->statusapprove) === 'A1') { ?>
                                <td><span class="label label-info"><?php echo $lu->nmstatusapprove;?></span></td>
                            <?php } else if (trim($lu->statusapprove) === 'A2') { ?>
                                <td><span class="label label-warning"><?php echo $lu->nmstatusapprove;?></span></td>
                            <?php } else if (trim($lu->statusapprove) === 'C') { ?>
                                <td><span class="label label-danger"><?php echo $lu->nmstatusapprove;?></span></td>
                            <?php } else if (trim($lu->statusapprove) === 'P') { ?>
                                <td><span class="label label-success"><?php echo $lu->nmstatusapprove;?></span></td>
                            <?php } ?>
                            <td><?php echo $lu->docno;?></td>
                            <td><?php if(empty($lu->nminputby)) { echo $lu->inputby; } else { echo $lu->nminputby; } ?></td>
                            <td><?php echo $lu->nmapproveby;?></td>
                            <td><?php if(empty($lu->approvedate)) { echo ''; } else { echo date('d-m-Y H:i:s',strtotime($lu->approvedate)); }?></td>
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
                        <h2> Resume Per - Karyawan Absensi Periode  <?= $tglresume1.' s/d '.$tglresume2?></h2>
                    </div>
                    <div class="box-header">
                        <div class="col-sm-12">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#filterResume" class="btn btn-default" style="margin:0px; color:#000000;"><i class="fa fa-filter"></i> Filter </a>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="tAbscutResume" class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Departement</th>
                                <th>Cuti</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Terlambat</th>
                                <th>Dispensasi</th>
                                <th>Alpha</th>
                                <th>Covid</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no=0; foreach($list_abscut_resume as $lar): $no++;?>
                                <tr>
                                    <td width="2%"><?php echo $no;?></td>
                                    <td><?php echo $lar->nmlengkap;?></td>
                                    <td><?php echo $lar->cuti;?></td>
                                    <td><?php echo $lar->izin;?></td>
                                    <td><?php echo $lar->sakit;?></td>
                                    <td><?php echo $lar->terlambat;?></td>
                                    <td><?php echo $lar->dispensasi;?></td>
                                    <td><?php echo $lar->alpha;?></td>
                                    <td><?php echo $lar->sakit_covid;?></td>

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
<div class="modal fade" id="filter" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Filter Pencarian</h4>
            </div>
            <form action="<?php echo base_url('trans/ess_cuti')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal</label>
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
                        <label class="label-form col-sm-3">Jenis Absensi</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="idtypecutifilter[]" id="idtypecutifilter" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Nik/ Nama Karyawan Filter</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="nikfilter[]" id="nikfilter" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Status Persetujuan</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="statusapprove" id="statusapprove" style="width: 100%" placeholder="Pilih Status Persetujuan">
                                <option value="">SEMUA</option>
                                <option value="A">PERLU PERSETUJUAN</option>
                                <option value="A1">PERLU PERSETUJUAN 2</option>
                                <option value="A2">PERLU PERSETUJUAN 3</option>
                                <option value="A3">PERLU FINALISASI</option>
                                <option value="P">FINAL/DISETUJUI</option>
                                <option value="C">DIBATALKAN</option>
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
<div class="modal fade" id="downloadXls" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download File Excel Absensi</h4>
            </div>
            <form action="<?php echo base_url('trans/ess_cuti/downloadExcel')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal</label>
                        <div class="col-sm-9">
                            <input type="text" name="daterange" id="daterange" class="form-control input-sm daterange" required>
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
                        <label class="label-form col-sm-3">Jenis Absensi</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="idtypecutidownload" id="idtypecutidownload" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Nik/Nama Karyawan</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="nikdownload" id="nikdownload" style="width: 100%">

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

<!-- Modal untuk Filter Resume -->
<div class="modal fade" id="filterResume" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Filter Pencarian</h4>
            </div>
            <form action="<?php echo base_url('trans/ess_cuti')?>" method="post">
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

<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/ess_cuti.js') ?>"></script>




