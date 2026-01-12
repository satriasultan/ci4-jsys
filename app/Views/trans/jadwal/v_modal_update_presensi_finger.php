<div class="box-header">
    <h4><?php echo 'Tanggal Presensi Mesin Finger : '.date('d-m-Y',strtotime($dtl['tgl'])); ?> </h4>
    <h4><?php echo $dtlk['nmlengkap'].' / '.$dtlk['nmdept'].' / '.$dtlk['nmsubdept'].' / '.$dtlk['nmjabatan']; ?> </h4>
</div>
<div class="box">
    <div class="box-header">
    </div><!-- /.box-header -->
    <form action="<?php echo base_url('trans/jadwal_new/saveUpdatePresensi')?>" method="post"  id="formInputJam" role="form">
    <div class="box-body" >
        <div class="col-lg-12">
            <h3 ALIGN="center"> <?php echo 'UBAH DATA PRESENSI / JADWAL JAM KERJA' ;?></h3>
        </div>

            <div class="col-lg-12">
                <input type="hidden" class="form-control" name="type" value="<?php echo 'UPDATE' ;?>">
                <input type="hidden" class="form-control" name="id" value="<?php echo $dtl['id']; ?>">
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Pengelompokan Regu</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-circle-o"></i>
                        </div>
                        <input type="text" value="<?php echo $dtl['kdregu']; ?>" class="form-control" name="kdregu" placeholder="Kode Regu" disabled>
                    </div>
                    <!-- /.input group -->
                </div>
                <!-- Date dd/mm/yyyy -->
                <div class="form-group">
                    <label>Jam kerja & Kode Jam Kerja Asal</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-circle-o"></i>
                        </div>
                        <select class="form-control input-sm" name="kdjamkerja" id="kdjamkerja">
                            <?php foreach($jam_kerja as $ls){?>
                                <option <?php if (trim($dtl['kdjamkerja'])==trim($ls->kdjam_kerja)) { echo 'selected'; } ?> value="<?php echo trim($ls->kdjam_kerja);?>" ><?php echo trim($ls->kdjam_kerja).' '.$ls->jam_masuk.' '.$ls->jam_pulang;?></option>
                            <?php }?>
                        </select>
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-lg-6">

                <div class="form-group">
                    <label>Jam Masuk Absen</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input <?php if (empty($dtl['jam_masuk_absen'])) { ?> style="background-color: #db3737;" <?php } ?> type="text" class="form-control clockpicker" name="jam_masuk_absen" id="jam_masuk_absen" value="<?php echo trim($dtl['jam_masuk_absen1']);?>" placeholder="Jam Masuk Absen" >
                    </div>
                    <!-- /.input group -->
                </div>
                <div class="form-group">
                    <label>Jam Pulang Absen</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input <?php if (empty($dtl['jam_pulang_absen'])) { ?> style="background-color: #db3737;" <?php } ?>type="text" class="form-control clockpicker" name="jam_pulang_absen" id="jam_pulang_absen" value="<?php echo trim($dtl['jam_pulang_absen1']);?>" placeholder="Jam Pulang Absen" >
                    </div>
                    <!-- /.input group -->
                </div>
            </div>

    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="col-sm-12">
            <button class="btn btn-success dropdown-toggle pull-right" style="margin:0px; color:#ffffff;" type="submit" id="btnSave"> Proses <i class="fa fa-arrow-right"></i></button>
        </div>
    </div>
    </form>
</div><!-- /.box -->

<script type="text/javascript">
    $(document).ready(function() {
        $('.clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true
        });
    });


</script>