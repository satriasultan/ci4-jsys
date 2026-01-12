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

?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
				$(".datePick").datepicker();

                // $(".tglrangetime").datetimepicker({
                //     format: 'DD-MM-YYYY H:mm',
                // }).on('dp.change', function(e) {
                //     // Revalidate the date field
                //     //console.log('DD-MM-YYYY' + 'X');
                //     $('#formBeritaAcara').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
                // });
                // $(".tglrangetime").daterangepicker({
                //     timePicker: true,
                //     singleDatePicker: true,
                //     showDropdowns: true,
                //     minYear: 1901,
                //     maxYear: parseInt(moment().format('YYYY'), 10),
                //     locale: {
                //         format: 'DD-MM-YYYY H:mm'
                //     }
                // });

                $(".dateRangePick").daterangepicker({
                    //singleDatePicker: true,
                    //showDropdowns: true
                },function () {
                    //console.log('TEST');
                    $('#formMutasiPromosi').bootstrapValidator('validateField', 'startdate');
                });
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
<!--?php echo $message;?-->


<div class="box">
    <div class="box-header">

    </div><!-- /.box-header -->
    <div class="box-body" >
        <div class="col-lg-12">
            <h3 ALIGN="center"> <?= $type ;?> MUTASI KARYAWAN <?php echo $docno ;?></h3>
        </div>
        <form action="<?php echo base_url('#')?>" method="post" id="formMutasiPromosi" enctype="multipart/form-data" role="form">
        <div class="col-lg-12">
            <input type="hidden" class="form-control" name="docno" value="<?php echo trim($docno) ;?>">
            <input type="hidden" class="form-control" name="type" value="<?php echo trim($type) ;?>">
                <div class="form-group">
                    <label class="label-form col-sm-2">NIK/NAMA MUTASI KARYAWAN</label>
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
            <div class="form-group">
                <label>Sub departmen/Section</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="department" placeholder="Sub Department/Section Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <!-- Date dd/mm/yyyy -->
            <div class="form-group">
                <label>Jabatan/Position</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="jabatan" placeholder="Jabatan/Position Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
<?php /*
            <div class="form-group">
                <label>Atasan 1</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="atasan1" placeholder="Atasan 1 Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <div class="form-group">
                <label>Atasan 2</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="atasan2" placeholder="Atasan 2 Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <div class="form-group">
                <label>Atasan 3</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="atasan3" placeholder="Atasan 3 Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
 */ ?>
            <div class="form-group">
                <label>Plant</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="plant" placeholder="Plant Area" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Contact</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="nohp1" placeholder="Contact & Ponsel" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Contact</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input type="text" class="form-control" name="email" placeholder="Email / Surel" disabled>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label>Evaluation / Masa Induksi</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12 select2" name="evaluation" id="evaluation" placeholder="Evaluation" >
                        <option value="YES" class="YES">YES</option>
                        <option value="NO" class="NO">NO</option>
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Tanggal Pengajuan s/d Tanggal Terhitung</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control dateRangePick" name="startdate" id="startdate" data-date-format="dd-mm-yyyy" placeholder="Tanggal Periode" >
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Pilih Jenis</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12" name="doctype" id="doctype" placeholder="Type" >
                        <option value="MUTASI" class="">MUTASI</option>
                    </select>
                </div>
                <!-- /.input group -->
            </div>
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
                <label>Deskripsi</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-pencil"></i>
                    </div>
                    <textarea class="form-control" name="description" id="description" placeholder="Deskripsi & Keterangan" style="text-transform:uppercase" ></textarea>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <!-- /.form group -->
            <div class="form-group">
                <label>Departemen Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12" name="newdept" id="newdept" placeholder="Departemen Baru" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Sub Departemen/Section Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12" name="newsubdept" id="newsubdept" placeholder="Sub Departemen Baru" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Jabatan/Postion Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12" name="newjabatan" id="newjabatan" placeholder="Jabatan Baru" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Level/Grade Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12" name="newlvljabatan" id="newlvljabatan" placeholder="Level/Grade Jabatan Baru" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Plant</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <select class="form-control col-sm-12" name="newplant" id="newplant" placeholder="Plant Baru" >
                    </select>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        </form>
    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="col-sm-12">
            <a href="<?php echo base_url('trans/mutpromot') ?>" class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" type="button"><i class="fa fa-arrow-left"></i> Kembali </a>
            <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="finishInput();"> Proses <i class="fa fa-arrow-right"></i></button>
        </div>
    </div>
</div><!-- /.box -->


<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/smutpromot.js') ?>"></script>
