<script type="text/javascript">
function download()
    {
	  $('#download').text('Processing....!!');
	  $('#download').attr('disabled',true);

          url = "<?php echo site_url('trans/absensi/pr_report_absensi')?>";
		  data = $('#downloadform').serialize();

			// window.alert(data);
       // ajax adding data to database
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
<?php echo $message;?>
<legend><?php echo $title;?></legend>
<div class="row">
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

                                    <button onclick="download()" id='download' class='btn btn-success' ><i class="glyphicon glyphicon-search"></i> Download</button>
                        </div>
                </div>
            </div>
        </div>
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
	$('.sl').selectize();
  

</script>