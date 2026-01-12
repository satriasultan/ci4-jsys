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

<legend><?php echo $title;?></legend>
<div class="row">
<div class="col-xs-6">
<?php echo $message;?>
    <div class="box">
        <div class="box-header">

        </div>
        <div class="box-body">
            <div class="form-horizontal">
                <form action="<?php echo base_url('trans/absensi/input_data');?>" name="form" role="form" method="post">
                    <div class="form-group">
                         <label class="col-lg-3">Tanggal Tarikan Terakhir</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="input" id="tglakhir" name="tglakhir"  class="form-control pull-right" readonly>
                            </div><!-- /.input group -->
                        </div>
                    </div>
                    <div class="form-group">
                         <label class="col-lg-3">Pilih Wilayah</label>
                        <div class="col-lg-9">
                            <select id="kdcabang" name="kdcabang" required>
                            <option value="">--Pilih Wilayah--</option>
                            <?php foreach ($list_kanwil as $ld){ ?>
                            <option value="<?php echo trim($ld->loccode);?>"><?php echo $ld->loccode.' || '.$ld->locaname;?></option>
                            <?php } ?>
                        </select>
                        </div>
                    </div>


        <script type="text/javascript" charset="utf-8">
        $(function() {
                    $('#kdcabang').change(function(){

                            var cabang=$(this).val();

                              $.ajax({
                                url : "<?php echo base_url('trans/absensi/ajax_tglakhir_tr')?>/" + $(this).val(),
                                type: "GET",
                                dataType: "JSON",
                                success: function(data)
                                {
                                   $('[name="tglakhir"]').val(data.lastdate);
                                    //alert('cok');
                                },
                                error: function (jqXHR, textStatus, errorThrown)
                                {
                                    alert('Error get data from ajax');
                                }
                            });


                        });

                    });

</script>

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
		
	

<script>

  

	
	//Date range picker
    $('#tgl').daterangepicker();
	$('#pilihkaryawan').selectize();
	    $('#kdcabang').selectize();
  

</script>