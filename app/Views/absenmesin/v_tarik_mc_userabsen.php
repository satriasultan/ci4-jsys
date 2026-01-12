<script type="application/javascript">
    $(function () {
        $('#qintil').click(function () {
            $("#loadMe").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });

            var id = $('#id').val();
            var daterange = $('#daterange').val();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('daterange', daterange);

            $('#qintil').prop('disabled', true);
            $('#qintil').text('Memproses....!!');
            $.ajax({
                url: "<?php echo base_url('absenmesin/save_mc_userabsen')?>",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                type: 'POST',
                success: function (data) {
                    if (data.status) {
                        $("#loadMe").modal("hide");
                        alert('Data Sukses Di Proses, Lihat Presensi Dasar');
                    }
                    $('#qintil').prop('disabled', false);
                    $('#qintil').text('Proses');
                   // console.log(this.status);
                   // console.log(data);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $("#loadMe").modal("hide");
                    alert('DATA GAGAL/ KESALAHAN DATA');
                    //console.log("Failed To Loading Data");
                }
            });
        });
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
<h3><?php echo $title; ?></h3>
<?php echo $message; ?>
<div class="row">
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <form id="myform" method="post">
                        <div class="form-group">
                            <label class="label-form col-sm-3">Tanggal</label>
                            <div class="col-sm-9">
                                <input type="text" name="daterange" id="daterange" class="form-control input-sm daterangefilter" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="label-form col-sm-3">Pilih Mesin</label>
                            <div class="col-sm-9">
                                <select id="id" name="id" style="width: 100%">
                                    <?php foreach ($list_machine as $ld){ ?>
                                        <option value="<?php echo trim($ld->id);?>"><?php echo $ld->machinename.' || '.$ld->ipaddress;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <button type='button' id='qintil' class='btn btn-primary pull-right' > Proses </button>
                                </div>
                                <!--<div class="form-group">-->
                                <div class="col-lg-12">
                                    <div id = "pgbar" class=" progress progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                                </div>

                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="<?php echo base_url('absenmesin/set_mConnection') ?>" type='button' class='btn btn-success' > Cek Koneksi </a>


<script>
    //Date range picker
    $('#tgl').daterangepicker();
    $('#id').select2();
    $(".daterangefilter").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
    });
    //$(".daterangefilter").val('');

</script>