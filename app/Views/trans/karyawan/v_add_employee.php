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
				$("#dateinput").datepicker();
            });
</script>
<style>
    .row {
        margin-right: 20px;
        margin-left: -15px;
    }
    .form-horizontal .form-group {
         margin-right: 0px;
         margin-left: 0px;
    }

</style>
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


<div class="box">
    <div class="box-header">
        <?php echo $message; ?>
    </div><!-- /.box-header -->
    <div class="box-body">
        <!-- SmartWizard html -->
        <div id="smartwizard">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#step-1" data-repo="jquery-smarttab">
                        <strong>Step 1</strong><br />Data identitas karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-2" data-repo="smartwizard">
                        <strong>SmartWizard</strong><br />repository details from GitHub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-3" data-repo="jquery-smartcart">
                        <strong>SmartCart</strong><br />repository details from GitHub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-4" data-repo="jquery-smartcart">
                        <strong>SmartCart</strong><br />repository details from GitHub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-5" data-repo="jquery-smartcart">
                        <strong>SmartCart</strong><br />repository details from GitHub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-6" data-repo="jquery-smartcart">
                        <strong>SmartCart</strong><br />repository details from GitHub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-7" data-repo="jquery-smartcart">
                        <strong>SmartCart</strong><br />repository details from GitHub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#step-8" data-repo="jquery-smartcart">
                        <strong>SmartCart</strong><br />repository details from GitHub
                    </a>
                </li>
            </ul>

            <div class="tab-content container">
                <form action="#" id="step-karyawan" class="form-horizontal">
                    <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                        <div class="row">
                            <h4> Step 1</h4>
                            <div class="col-md-5 ">
                                <div class="box box-primary" >
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Nama Lengkap</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <input type="text" class="form-control" name="nmlengkap" placeholder="Nama Lengkap">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <!-- /.form group -->
                                        <!-- Date dd/mm/yyyy -->
                                        <div class="form-group">
                                            <label>Nama Singkat/Panggilan</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <input type="text" class="form-control" name="nikname" placeholder="Nama Singkat/Panggilan">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <div class="form-group">
                                            <label>Jenis Kelamin</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text">
                                                    <option value="L">LAKI-LAKI</option>
                                                    <option value="P">PEREMPUAN</option>
                                                </select>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <div class="form-group">
                                            <label>Agama</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <select  name="agama" style="text-transform:uppercase;"  class="form-control" type="text">
                                                    <option value="ISL">ISLAM</option>
                                                    <option value="KR">KRISTEN</option>
                                                </select>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <!-- Date dd/mm/yyyy -->
                                        <div class="form-group">
                                            <label>Tanggal Lahir</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <input type="text" class="form-control" name="tgllahir" placeholder="Tanggal Lahir">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                        <!-- Date dd/mm/yyyy -->
                                        <div class="form-group">
                                            <label>Tempat Lahir</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <input type="text" class="form-control" name="nikname" placeholder="Tempat Lahir">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 ">
                                <div class="box box-primary" >
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Isinya Dis SIni</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle-o"></i>
                                                </div>
                                                <input type="text" class="form-control" name="nmlengkap" placeholder="Nama Lengkap">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                        <h3> Step 2</h3>
                        <div class="row">
                            <div class="col-sm-6 ">
                                <div class="box box-primary" >
                                    <div class="box-header">
                                    </div>
                                    <div class="box-body">

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Agama</label>
                                            <div class="col-sm-8">

                                                <select  name="kd_agama" class="form-control col-sm-12 plktp" required>
                                                    <option value="" >-- PILIH AGAMA --</option>
                                                    <?php foreach ($list_opt_agama as $loa){ ?>
                                                        <option value="<?php echo trim($loa->kdagama);?>" ><?php echo trim($loa->nmagama);?></option>
                                                    <?php };?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tanggal Lahir</label>
                                            <div class="col-sm-8">
                                                <input name="tgllahir"   class="form-control" id="tgl" placeholder="Tanggal Lahir" data-date-format="dd-mm-yyyy" type="text" required>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 ">
                                <div class="box box-primary" >
                                    <div class="box-header">
                                    </div>
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Keadaan Fisik</label>
                                            <div class="col-sm-8">
                                                <select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
                                                    <option value="t">BAIK & SEHAT</option>
                                                    <option value="f">CACAT FISIK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
                                            <div class="col-sm-8">
                                                <textarea name="ketfisik" style="text-transform:uppercase;" placeholder="Deskripsikan Cacat fisik" class="form-control" ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                    </div>
                    <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
                    </div>
                    <div id="step-5" class="tab-pane" role="tabpanel" aria-labelledby="step-5">
                    </div>
                    <div id="step-6" class="tab-pane" role="tabpanel" aria-labelledby="step-6">
                    </div>
                    <div id="step-7" class="tab-pane" role="tabpanel" aria-labelledby="step-7">
                    </div>
                    <div id="step-8" class="tab-pane" role="tabpanel" aria-labelledby="step-8">
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->




<script type="application/javascript" src="<?= base_url('assets/pagejs/master/karyawan.js') ?>"></script>