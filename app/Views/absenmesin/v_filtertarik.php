<legend><?php echo $title;?></legend>

<div class="row">
    <div class="col-xs-6">
        <?php echo $message;?>
        <div class="box">
            <div class="box-header">

            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <form action="<?php echo site_url('trans/absensi/input_data');?>" id="iniform" name="form" role="form" method="post">
                        <div class="form-group">
                            <label class="col-lg-3">Tanggal Tarikan Terakhir</label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="input" id="tglakhir" name="tglakhir"  class="form-control pull-right fr" readonly>
                                </div><!-- /.input group -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3">Pilih Wilayah</label>
                            <div class="col-lg-9">
                                <select id="kdcabang" name="kdcabang" required>
                                    <option value="">--Pilih Wilayah--</option>
                                    <?php foreach ($list_kanwil as $ld){ ?>
                                        <option value="<?php echo trim($ld->kdcabang);?>"><?php echo $ld->kdcabang.' || '.$ld->desc_cabang;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                        <script type="text/javascript" charset="utf-8">
                            $(function() {
                                $(':input[type="submit"]').prop('disabled', true);
                                $('#kdcabang').change(function(){
                                    $(':input[type="submit"]').prop('disabled', true);
                                    $(':input[type="submit"]').text('Memproses....!!');
                                    var cabang=$(this).val();

                                    $.ajax({
                                        url : "<?php echo site_url('trans/absensi/ajax_tglakhir_tr')?>/" + cabang,
                                        type: "GET",
                                        dataType: "JSON",
                                        success: function(data)
                                        {
                                            $('[name="tglakhir"]').val(data.lastdate);
                                            $(':input[type="submit"]').prop('disabled', false);
                                            $(':input[type="submit"]').text('Proses');
                                        },
                                        error: function (jqXHR, textStatus, errorThrown)
                                        {
                                            alert('Error get data from ajax');
                                        }
                                    });


                                });

                                $("#iniform").submit(function(e){
                                    $('.fr').prop('disabled', true);
                                    $(':input[type="submit"]').text('Memproses....!!');
                                    $(':input[type="submit"]').submit();
                                    e.preventDefault();
                                });

                            });



                        </script>
                        <?php if($akses['aksesview']=='t') { ?>
                            <div class="form-group">
                                <label class="col-lg-3">Tanggal</label>
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="tgl" name="tgl"  data-date-format="yyyy-mm-dd" class="form-control pull-right">
                                    </div><!-- /.input group -->
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-4">
                                    <button type='submit'  id='qintil' class='btn btn-primary fr' > Proses </button>
                                    <!-- <button id="tampilkan" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tampilkan</button>-->
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>




    //Date range picker
    $('#tgl').daterangepicker();
    $('#pilihkaryawan').selectize();
    $('#kdcabang').selectize();


</script>