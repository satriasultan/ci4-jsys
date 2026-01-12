<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 8/29/21, 4:17 PM
 * Path Directory: v_ess_dashboard.php
 */

?>
<!--<style>-->
<!--    .fc-event {-->
<!---->
<!--    }-->
<!--    .fc-day-grid-event {-->
<!--        margin: 1px 2px 80px;-->
<!--        padding: 0 30px 0;-->
<!--        position: relative;-->
<!--        display: block;-->
<!--        font-size: 1.5em;-->
<!--        line-height: 1.3;-->
<!--        border-radius: 3px;-->
<!--        border: 1px solid #3a87ad;-->
<!--        font-weight: 500;-->
<!--    }-->
<!--</style>-->
<style>
    .fc-event, .fc-event:hover {
        color: #fff;
        font-size: 1em;
        font-weight: 700;
        text-align: center;

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
<!--h3><?php echo $title; ?></h3-->
<?php echo $message;?>
<style>
    .lbprofile{
        font-size: 1.3em;
        font-family: "Times New Roman";
    }
    .lbprofile2{
        font-size: 1em;
        font-family: "Times New Roman";
    }
    .lbprofile3{
        font-size: 4em;
        font-family: "Times New Roman";
        font-weight: bold;
    }
    .form-group {
        margin-bottom: 0px;
    }
    .small-box h3 {
        font-size: 50px;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }

    .small-box {
        margin-bottom: 0px;
    }
    .small-box > .small-box-footer {
        position: relative;
        text-align: center;
        padding: 10px 0;
        color: #fff;
        color: rgba(255, 255, 255, 0.8);
        display: block;
        z-index: 10;
        background: rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
</style>

<div class="row">
    <input type="hidden" name="niksession" value="<?php echo trim($niksession); ?>">
    <!-- /.col -->
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-body no-padding">
                <!-- THE CALENDAR -->
                <div id="calendar"></div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /. box -->
    </div>
    <!-- /.col -->
    <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">Event Absensi, <strong><?= $dtlkary['nmlengkap'] ?></strong></h4>
            </div>
            <div class="box-body">
                <!-- the events -->
                <div id="external-events">
                    <div class="external-event bg-green">Masuk</div>
                    <div class="external-event bg-blue">Pulang</div>
                    <div class="external-event bg-yellow">Izin</div>
                    <div class="external-event bg-orange">Terlambat</div>
                    <div class="external-event bg-purple">Sakit</div>
                    <div class="external-event bg-navy">Cuti</div>
                    <div class="external-event bg-red">Alpha/Mangkir</div>
                    <?php /*
                    <div class="checkbox">
                        <label for="drop-remove">
                            <input type="checkbox" id="drop-remove">
                            remove after drop
                        </label>
                    </div>
 */ ?>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /. box -->
        <form action="<?php echo site_url('#')?>" method="post" id="formAbsenCalendar" role="form">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4 class="box-title">Nik & Nama Karyawan</h4>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <select class="select2_kary form-control col-sm-12" name="nik" id="nik">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="col-sm-12">
                    <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="button" id="btnSave" onclick="lihatNik();"> Lihat <i class="fa fa-eye"></i></button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- Page specific script -->
<script type="text/javascript" src="<?= base_url('assets/pagejs/trans/ess_calendar.js') ?>"></script>

