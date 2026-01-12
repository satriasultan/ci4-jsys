<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 3/29/21, 4:17 PM
 * Path Directory: v_list.php
 */

?>


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
<h3><?php echo $title; ?></h3>
<?php echo $message;?>


<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">
					<a href="#" data-bs-toggle="modal" data-bs-target="#filter-checkinout" class="btn btn-default pull-right" style="margin:10px; color:#000000;"><i class="fa fa-filter"></i> Filter </a>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="tcheckinout" class="table table-bordered table-striped" >
					<thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th>ID Finger</th>
                        <th>ID Absen</th>
                        <th>Nama</th>
                        <th>Department</th>
                        <th>Section</th>
                        <th>Checktime</th>
                        <th>Type</th>
                        <th>StatusID</th>
                    </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>



<!--Modal untuk Filter-->
<div class="modal fade" id="filter-checkinout" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filter Data Absen Online</h4>
      </div>
        <form id="form-filter-checkinout" class="form-horizontal">
          <div class="modal-body">
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Tanggal</label>
                  <div class="col-sm-9">
                      <input type="text" name="daterangefilter" id="daterangefilter" class="form-control input-sm daterangefilter" required>
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
                  <label class="label-form col-sm-3">Pilih Nama / Nik</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" id="nik_filter" name="nik_filter" style="width: 100%">

                      </select>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">StatusID</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" id="statusid" name="statusid" style="width: 100%">
                        <option value="">ALL</option>
                        <option value="IDENTIFIED">IDENTIFIED</option>
                        <option value="UNIDENTIFIED">UNIDENTIFIED</option>
                      </select>
                  </div>
              </div>
              <?php /*
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">TYPE</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="ctype" id="ctype" style="width: 100%">
                          <option value=""> Pilih Jadwal </option>
                          <option value="PAGI">PAGI</option>
                          <option value="SORE">SORE</option>
                      </select>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Status</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="attendstatus" id="attendstatus" style="width: 100%">
                          <option value=""> Pilih Status </option>
                          <option value="0">BELUM SHARELOC</option>
                          <option value="1">SHARELOC</option>
                          <option value="2">SHARE ENDED</option>
                          <option value="3">ABSEN KANTOR</option>
                      </select>
                  </div>
              </div>
 */ ?>
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
      </form>
      <div class="modal-footer">
          <button type="button" id="btn-checkinout-filter" class="btn btn-primary">Filter</button>
          <button type="button" id="btn-checkinout-reset" class="btn btn-default">Reset</button>
      </div>

    </div>
  </div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade" id="downloadXls" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download Excel Lengkap</h4>
            </div>
            <form action="<?php echo site_url('trans/shareloc/downloadExcel')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal</label>
                        <div class="col-sm-9">
                            <input type="text" name="daterangeDownload" id="daterangeDownload" class="form-control input-sm daterangefilter" required>
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
                        <label class="label-form col-sm-3">Pilih Nama / Nik</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" id="nik_download" name="nik_download" style="width: 100%">

                            </select>
                        </div>
                    </div>

                    <?php /*
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">TYPE</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="ctype" id="ctype" style="width: 100%">
                          <option value=""> Pilih Jadwal </option>
                          <option value="PAGI">PAGI</option>
                          <option value="SORE">SORE</option>
                      </select>
                  </div>
              </div>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Status</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="attendstatus" id="attendstatus" style="width: 100%">
                          <option value=""> Pilih Status </option>
                          <option value="0">BELUM SHARELOC</option>
                          <option value="1">SHARELOC</option>
                          <option value="2">SHARE ENDED</option>
                          <option value="3">ABSEN KANTOR</option>
                      </select>
                  </div>
              </div>
 */ ?>
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

<!-- Bootstrap modal -->
<div class="modal fade " id="modal_show_image" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Tampilan Gambar</h3>
            </div>
            <div class="modal-body   col-md-8 col-md-offset-2">
                <img id="photo" name="photo" alt="Gambar disini."  width="500" height="500" >
                <?php /*<div className="image">
                    <img height="100px"
                     width="100px"
                     alt="User Image"
                     className="img-box"
                     src="'.$lm->imageblob.'">
                    </a>
                </div> */ ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/onlineabsen.js') ?>"></script>




