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
            <h3 ALIGN="center"> DETAIL MUTASI DAN PROMOSI KARYAWAN</h3>
        </div>
        <form action="<?php echo site_url('#')?>" method="post" id="formMutasiPromosi" enctype="multipart/form-data" role="form">
        <div class="col-lg-12">
                <input type="hidden" class="form-control" name="type" value="INPUT">
                <div class="form-group">
                    <label class="label-form col-sm-2">NIK MUTASI KARYAWAN</label>
                    <?php /*<input type="hidden" value="<?php if (isset($dtl['kdregu'])){ echo trim($dtl['kdregu']); } else { echo ''; } ?>" id="kdregu" name="kdregu" style="text-transform:uppercase" maxlength="200" class="form-control" readonly required />
                <input type="hidden" value="<?php echo $blnx;?>" id="bln1" name='bln' style="text-transform:uppercase" maxlength="200" class="form-control" readonly />
                <input type="hidden" value="<?php echo $thnx;?>" id="thn1" name="thn" style="text-transform:uppercase" maxlength="200" class="form-control" readonly /> */ ?>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-user-plus"></i>
                            </div>

                            <input type="text" class="form-control col-sm-12" name="nik" value="<?= trim($dtl['nmlengkap']) ?>" disabled>
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
                    <input value="<?= trim($dtl['nmdept']) ?>" type="text" class="form-control" name="department" placeholder="Department Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
            <!-- Date dd/mm/yyyy -->
            <div class="form-group">
                <label>Jabatan</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input value="<?= trim($dtl['nmjabatan']) ?>" type="text" class="form-control" name="jabatan" placeholder="Jabatan Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>

            <div class="form-group">
                <label>Atasan 1</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input value="<?= trim($dtl['nmatasan1']) ?>" type="text" class="form-control" name="atasan1" placeholder="Atasan 1 Karyawan" disabled>
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
                    <input value="<?= trim($dtl['nmatasan2']) ?>" type="text" class="form-control" name="atasan2" placeholder="Atasan 2 Karyawan" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input value="<?= trim($dtl['alamattinggal']) ?>" type="text" class="form-control" name="alamattinggal" placeholder="Alamat Domisili" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Contact</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input value="<?= trim($dtl['nohp1']) ?>" type="text" class="form-control" name="nohp1" placeholder="Contact & Ponsel" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Contact</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-circle-o"></i>
                    </div>
                    <input value="<?= trim($dtl['email']) ?>" type="text" class="form-control" name="email" placeholder="Email / Surel" disabled>
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label>Evaluation</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input value="<?= trim($dtl['evaluation']) ?>" type="text" class="form-control col-sm-12" name="evaluation" id="evaluation" placeholder="Evaluation" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Tanggal Awal s/d Tanggal Selesai</label>

                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input value="<?= trim($dtl['startdatex']) ?>" type="text" class="form-control col-sm-12" name="startdate" id="startdate" placeholder="tanggal" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Pilih Jenis</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input value="<?= trim($dtl['doctype']) ?>" type="text" class="form-control col-sm-12" name="doctype" id="doctype" placeholder="type" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Regu Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input value="<?= trim($dtl['kdregu']) ?>" type="text" class="form-control col-sm-12" name="kdregu" id="kdregu" placeholder="regu" disabled>

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
                    <textarea class="form-control" name="description" id="description" placeholder="Deskripsi & Keterangan" style="text-transform:uppercase" disabled><?= trim($dtl['description']) ?></textarea>
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
                    <input value="<?= trim($dtl['nmnewdept']) ?>" type="text" class="form-control col-sm-12" disabled>

                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Sub Departemen Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input value="<?= trim($dtl['nmnewsubdept']) ?>" type="text" class="form-control col-sm-12" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Jabatan Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input value="<?= trim($dtl['nmnewjabatan']) ?>" type="text" class="form-control col-sm-12" disabled>
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Level Jabatan Baru</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-user"></i>
                    </div>
                    <input value="<?= trim($dtl['nmnewlvljabatan']) ?>" type="text" class="form-control col-sm-12" disabled>
                </div>
                <!-- /.input group -->
            </div>

        </div>
        </form>
    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="col-sm-12">
            <a href="<?php echo site_url('trans/mutpromot') ?>" class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" type="button"><i class="fa fa-arrow-left"></i> Kembali </a>
        </div>
    </div>
</div><!-- /.box -->





























