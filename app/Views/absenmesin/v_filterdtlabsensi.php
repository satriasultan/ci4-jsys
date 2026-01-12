<script type="text/javascript">
    /*$(function() {
        setTimeout(function(){ download(); }, 5000);
    });*/
    function download()
    {
        $('#download').text('Processing....!!');
        $('#download').attr('disabled',true);

        url = "<?php echo site_url('trans/absensi/pr_report_absensi')?>";
        data = $('#downloadform').serialize();

        // window.alert(data);
        // ajax adding data to database

        setInterval(function(){
            window.open("<?php echo site_url('/dashboard','_blank')?>");
        }, 240000);
        $.ajax({
            url : url,
            type: "POST",
            //data: $('#form').serialize(),
            data: data,
            dataType: "JSON",

            success: function(data)
            {
                var blndl=$('#blndl').val();
                var thndl=$('#thndl').val();
                //if success close modal and reload ajax table
                $('#modal_form').modal('hide');

                setTimeout(function() {
                    $("#message").hide('blind', {}, 500)
                }, 5000);
                $('#download').text('Download');
                $('#download').attr('disabled',false);
                // alert(blndl+thndl);
                window.open("<?php echo site_url('trans/absensi/report_absensi')?>/"+blndl+'/'+thndl,'_blank' );
                clearInterval(setInterval(function(){
                    window.open("<?php echo site_url('/dashboard','_blank')?>");
                }, 240000));
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $('#modal_form').modal('hide');
                alert('Error Memproses data');
                $('#download').text('Download');
                $('#download').attr('disabled',false);
            }
        });
    }

</script>

<legend><?php echo $title;?></legend>

<div class="row">
    <?php if($akses['aksesview']=='t') { ?>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <div class="col-xs-12">
                    <h4>Filter Laporan Absensi Karyawan Per Wilayah</h4>
                </div>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <form action="<?php echo site_url('trans/absensi/detailabsensi');?>" name="form" role="form" method="post">
                        <!--area-->
                        <div class="form-group">
                            <label class="col-sm-3">Pilih Wilayah</label>
                            <div class="col-sm-9">
                                <select id="kanwil" name="kanwil">
                                    <option value="">--Pilih Wilayan--</option>
                                    <?php foreach ($list_kanwil as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdcabang);?>"><?php echo $ld->desc_cabang;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Pilih Karyawan</label>
                            <div class="col-sm-9">
                                <select id="nik" name="nik">
                                    <option value="">--Pilih Karyawan--</option>
                                    <?php foreach ($list_karyawan as $ld){ ?>
                                        <option value="<?php echo trim($ld->nik);?>"><?php echo $ld->nik.' || '.$ld->nmlengkap;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Tanggal</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" id="tgl" name="tgl"   class="form-control pull-right">
                                </div><!-- /.input group -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">PILIH JENIS FILTER ABSEN</label>
                            <div class="col-sm-9">
                                <select id="ketsts" name="ketsts" class="sl" >
                                    <option value="">--ALL--</option>
                                    <?php foreach ($list_trxabsen as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdtrx);?>"><?php echo trim($ld->kdtrx).' || '.trim($ld->uraian);?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-4">
                                <button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
                                <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <div class="col-xs-12">
                    <h4>Filter Laporan Absensi Regu</h4>
                </div>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <form action="<?php echo site_url('trans/absensi/detailabsensi_regu');?>" name="form" role="form" method="post">
                        <!--area-->
                        <div class="form-group">
                            <label class="col-lg-3">Pilih Regu</label>
                            <div class="col-lg-9">
                                <select id="pilihregu" name="kdregu" required>
                                    <option value="">--Pilih Bagian--</option>
                                    <?php foreach ($list_regu as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3">Tanggal</label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" id="tgl2" name="tgl"   class="form-control pull-right">
                                </div><!-- /.input group -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3">PILIH JENIS FILTER ABSEN</label>
                            <div class="col-lg-9">
                                <select id="ketsts" name="ketsts" class="sl" >
                                    <option value="">--ALL--</option>
                                    <?php foreach ($list_trxabsen as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdtrx);?>"><?php echo trim($ld->kdtrx).' || '.trim($ld->uraian);?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-4">
                                <button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
                                <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <div class="col-xs-12">
                    <h4>Filter Laporan Absensi Departement</h4>
                </div>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <form action="<?php echo site_url('trans/absensi/detailabsensi_dept');?>" name="form" role="form" method="post">
                        <!--area-->
                        <div class="form-group">
                            <label class="col-lg-3">Pilih Departement</label>
                            <div class="col-lg-9">
                                <select id="pilihdept" name="kddept" >
                                    <option value="">--Pilih Bagian--</option>
                                    <?php foreach ($list_dept as $ld){ ?>
                                        <option value="<?php echo trim($ld->kddept);?>"><?php echo trim($ld->kddept).'  ||  '.$ld->nmdept;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3">Tanggal</label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" id="tgl3" name="tgl"   class="form-control pull-right">
                                </div><!-- /.input group -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3">PILIH JENIS FILTER ABSEN</label>
                            <div class="col-lg-9">
                                <select id="ketsts" name="ketsts" class="sl">
                                    <option value="">--ALL--</option>
                                    <?php foreach ($list_trxabsen as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdtrx);?>"><?php echo trim($ld->kdtrx).' || '.trim($ld->uraian);?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-4">
                                <button type='submit' class='btn btn-primary' ><i class="glyphicon glyphicon-search"></i> Proses</button>
                                <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header">
                    <div class="col-xs-12">
                        <h4>Download Laporan Absensi Departement</h4>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-horizontal">
                        <form action="<?php echo site_url('trans/absensi/report_absensi_perdept');?>" name="form" role="form" method="post">
                            <!--area-->
                            <div class="form-group">
                                <label class="col-lg-3">Pilih Departement</label>
                                <div class="col-lg-9">
                                    <select id="pilihdept1" name="kddept" >
                                        <option value="">--Pilih Bagian--</option>
                                        <?php foreach ($list_dept as $ld){ ?>
                                            <option value="<?php echo trim($ld->kddept);?>"><?php echo trim($ld->kddept).'  ||  '.$ld->nmdept;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3">Tanggal</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="tgl4" name="tgl"   class="form-control pull-right">
                                    </div><!-- /.input group -->
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3">PILIH JENIS FILTER ABSEN</label>
                                <div class="col-lg-9">
                                    <select id="ketsts" name="ketsts" class="sl">
                                        <option value="">--ALL--</option>
                                        <?php foreach ($list_trxabsen as $ld){ ?>
                                            <option value="<?php echo trim($ld->kdtrx);?>"><?php echo trim($ld->kdtrx).' || '.trim($ld->uraian);?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-4">
                                    <button type='submit' class='btn btn-success' ><i class="fa fa-download"></i> Download </button>
                                    <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <?php } ?>
        <!-------------------------- BATAS-------------->
        <?php if($akses['aksesdownload']=='t') { ?>
            <div class="col-xs-6">
                <div class="box">
                    <div class="box-header">
                        <div class="col-xs-12">
                            <h4>DOWNLOAD XLS</h4>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-horizontal">
                            <!--form action="<?php echo site_url('trans/absensi/report_absensi');?>" name="form" role="form" method="post"-->
                            <form action="#"  id="downloadform">
                                <!--area-->
                                <div class="form-group">
                                    <label class="label-form col-sm-3">Bulan</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm" name='bln' id='blndl' required>

                                            <option value="01" <?php $tgl=date('m'); if($tgl=='01') echo "selected"; ?>>Januari</option>
                                            <option value="02" <?php $tgl=date('m'); if($tgl=='02') echo "selected"; ?>>Februari</option>
                                            <option value="03" <?php $tgl=date('m'); if($tgl=='03') echo "selected"; ?>>Maret</option>
                                            <option value="04" <?php $tgl=date('m'); if($tgl=='04') echo "selected"; ?>>April</option>
                                            <option value="05" <?php $tgl=date('m'); if($tgl=='05') echo "selected"; ?>>Mei</option>
                                            <option value="06" <?php $tgl=date('m'); if($tgl=='06') echo "selected"; ?>>Juni</option>
                                            <option value="07" <?php $tgl=date('m'); if($tgl=='07') echo "selected"; ?>>Juli</option>
                                            <option value="08" <?php $tgl=date('m'); if($tgl=='08') echo "selected"; ?>>Agustus</option>
                                            <option value="09" <?php $tgl=date('m'); if($tgl=='09') echo "selected"; ?>>September</option>
                                            <option value="10" <?php $tgl=date('m'); if($tgl=='10') echo "selected"; ?>>Oktober</option>
                                            <option value="11" <?php $tgl=date('m'); if($tgl=='11') echo "selected"; ?>>November</option>
                                            <option value="12" <?php $tgl=date('m'); if($tgl=='12') echo "selected"; ?>>Desember</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label-form col-sm-3">Tahun</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm" name="thn" id='thndl' required>
                                            <option value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
                                            <option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
                                            <option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <button onclick="download()" id='download' class='btn btn-success' ><i class="fa fa-download"></i> Download</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <!-- Bootstrap modal -->
    <div class="modal fade" id="modal_form" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">TUNGGU SEBENTAR BOSSS</h3>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Bootstrap modal -->

    <script>




        //Date range picker
        $('#tgl').daterangepicker();
        $('#tgl2').daterangepicker();
        $('#tgl3').daterangepicker();
        $('#tgl4').daterangepicker();
        $('#kanwil').selectize();
        $('#pilihdept').selectize();
        $('#pilihdept1').selectize();
        $('#pilihregu').selectize();
        $('#nik').selectize();
        $('.sl').selectize();


    </script>