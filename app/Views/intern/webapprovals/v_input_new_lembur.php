<?php
/**
 * *
 *  * Created by PhpStorm.
 *  *  * User: FIKY-PC
 *  *  * Date: 4/29/19 1:34 PM
 *  *  * Last Modified: 12/18/16 10:51 AM.
 *  *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  *  Copyright© 2019 .All rights reserved.
 *  *
 *
 */

/**
 * Created by PhpStorm.
 * User: FIKY-PC
 * Date: 13/04/2019
 * Time: 10:26
 */
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
				$(".datePick").datepicker();

                $(".tglrangetime").datetimepicker({
                    format: 'DD-MM-YYYY H:mm',
                }).on('dp.change', function(e) {
                    // Revalidate the date field
                    //console.log('DD-MM-YYYY' + 'X');
                    $('#formBeritaAcara').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
                });
                /*$(".tglrangetime").daterangepicker({
                    timePicker: true,
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: 1901,
                    maxYear: parseInt(moment().format('YYYY'), 10),
                    locale: {
                        format: 'DD-MM-YYYY H:mm'
                    }
                });*/
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
<!--?php echo $message;?-->


<div class="box">
    <div class="box-header">

    </div><!-- /.box-header -->
    <div class="box-body" >
        <div class="col-lg-12">
            <h3 ALIGN="center"> <?php echo $type ;?> LEMBUR KARYAWAN <?php echo $docno ;?></h3>
        </div>
        <form action="<?php echo site_url('#')?>" method="post" id="formInputLembur" role="form">
        <div class="col-lg-12">
                <input type="hidden" class="form-control" name="docno" value="<?php echo $docno ;?>">
                <input type="hidden" class="form-control" name="type" value="<?php echo $type ;?>">
                <div class="form-group">
                    <label class="label-form col-sm-2">PIC LEMBUR</label>
                    <?php /*<input type="hidden" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { echo ''; } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                <input type="hidden" value="<?php echo $blnx;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                <input type="hidden" value="<?php echo $thnx;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly /> */ ?>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <select class="select2_kary form-control col-sm-12" name="nik" id="nik">
                            </select>
                        </div>
                    </div>
                    <!--<div class="col-md-1">
                        <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> Load </button>
                    </div>-->
                </div>

        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label>Nik Karyawan</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="nikv" placeholder="Nik Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- Date dd/mm/yyyy -->
            <div class="form-group">
                <label>Departement</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="department" placeholder="Department Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <!-- Date dd/mm/yyyy -->
            <div class="form-group">
                <label>Section</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="nmsubdept" placeholder="Section" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Cost Center</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="costcenter" placeholder="Cost Center" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Group/Golongan</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="nmgroupgol" placeholder="Group" disabled>
                    <input type="hidden" class="form-control" name="id_gol" placeholder="GroupId" readonly>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Plant</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="plant" placeholder="Lokasi Plant" disabled>
                </div>
                <!-- /.input group -->
            </div>


        </div>
        <div class="col-lg-4">
            <?php /*
            <div class="form-group">
                <label>Tanggal Awal s/d Tanggal Selesai</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control dateRangePick zz" name="startdate" id="startdate" data-date-format="dd-mm-yyyy" placeholder="Tanggal Periode" >
                </div>
                <!-- /.input group -->
            </div>
 */ ?>
            <?php /*
            <div class="form-group">
                <label>Tanggal Akhir</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datePick zz" name="enddate" id="enddate" data-date-format="dd-mm-yyyy" placeholder="Tanggal Akhir" >
                </div>
                <!-- /.input group -->
            </div> */ ?>
            <div class="form-group">
                <label>Tanggal Kerja</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control" name="tgl_kerja" id="tgl_kerja" placeholder="Tanggal Kerja" >
                </div>
                <!-- /.input group -->
            </div>

            <div class="form-group">
                <label>Masukkan Jam Lembur</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control" name="durasijam" id="durasijam" placeholder="Input Durasi Jam Gunakan Koma(,) Untuk Pecahan" maxlength="6">

                </div>
                <!-- /.input group -->
            </div>

        </div>
        <div class="col-lg-4">
            <!-- /.form group -->
            <div class="form-group">
                <label>Tipe Lembur</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input type="hidden" class="form-control" name="jenis_lembur" id="jenis_lembur" value="D2" PLACEHOLDER="non durasi absen">
                    <select class="form-control col-sm-12 zz select2" name="kdlembur" id="kdlembur" placeholder="Pilih Tipe Lembur" >
                        <option value="BIASA"> BIASA/REGULER </option>
                        <option value="LIBUR"> HARI LIBUR </option>
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-bars"></i>
                    </div>
                    <textarea type="text" class="form-control input-sm" id="keterangan" name="keterangan" style="text-transform: uppercase" required></textarea>
                </div>
                <!-- /.input group -->
            </div>


        </div>
        </form>
    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="col-sm-12">
            <a href="<?php echo site_url('trans/lembur') ?>" class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" type="button"><i class="fa fa-arrow-left"></i> Kembali </a>
            <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="finishInput();"> Proses <i class="fa fa-arrow-right"></i></button>
        </div>
    </div>
</div><!-- /.box -->



<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/lembur.js') ?>"></script>
