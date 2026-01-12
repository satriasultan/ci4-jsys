<?php 
/*
	@author : Junis
*/
?>
<script type="text/javascript">

    var save_method; //for save method string
    var table;
    $(document).ready(function() {
      table = $('#table').DataTable();
    });

    function add_person()
    {
      save_method = 'add';
      $('#form')[0].reset(); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
      $('.modal-title').text('Tarik Data Absen'); // Set Title to Bootstrap modal title
    }

    function edit_person(id)
    {
      save_method = 'update';
	  
	  $('#editform')[0].reset(); // reset form on modals

      //Ajax Load data from ajax
      $.ajax({
        url : "<?php echo site_url('trans/absensi/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
           
			$('[name="kdkepegawaian"]').val(data.kdkepegawaian);
            $('[name="nmkepegawaian"]').val(data.nmkepegawaian);                                    			
            // show bootstrap modal when complete loaded
			$('#modal_form').modal('hide');
			$('#edit_form').modal('show');
            $('.modal-title').text('Edit Status Kepegawaian'); // Set title to Bootstrap modal title
            
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');			
        }
    });
    }

    function reload_table()
    {
      table.ajax.reload(null,false); //reload datatable ajax 
    }

   

    function delete_person(id)
    {
      if(confirm('Are you sure delete this data?'))
      {
        // ajax delete data to database
          $.ajax({
            url : "<?php echo site_url('trans/absensi/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
               //if success reload ajax table
               $('#modal_form').modal('hide');
               reload_table();
				$("#message").html("<div class='alert alert-warning alert-dismissable'><i class='fa fa-check'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b> Hapus Data Sukses</b></div>");
			},
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
				
            }
        });
         
      }
    }

  </script>
<legend><?php echo $title.' Tanggal '.$tgl_kerja;?></legend>
<?php //echo $message;?>
<div id="message" >	
</div>
<div><?php //echo 'Total data: '.$ttldata['jumlah']; ?></div>
<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					<a href="<?php echo site_url('trans/jadwal_new/index');?>"  class="btn btn-primary" style="margin:10px; color:#ffffff;">Kembali</a>
					<!--<button href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Jadwal</button>-->
					<!--<button class="btn btn-primary" onclick="add_person()" style="margin:10px; color:#ffffff;"><i class="glyphicon glyphicon-plus"></i> Data Mesin Absen</button>-->
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>																
							<!--<th>Shift/Non Shift</th>-->
							<th>Nama Regu</th>
							<th>Jam Masuk</th>
							<th>Jam Pulang</th>
							<th>Tanggal Kerja</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=1; foreach ($list_jadwal as $la){ ?>
							<tr>																												
								<!--<td><?php echo $la->shift_tipe;?></td>-->								
								<td><a href="<?php echo site_url("trans/jadwal_new/detail_regu/$la->tgl/$la->kdregu");?>"><?php echo $la->kdregu;?></a></td>								
								<td><?php echo $la->jam_masuk;?></td>								
								<td><?php echo $la->jam_pulang;?></td>								
								<td><?php echo $la->tgl;?></td>								
								<td>
								<a  data-bs-toggle="modal" data-bs-target="#<?php echo trim($la->id);?>" href='#' onclick="return confirm('Anda Yakin Edit Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-pencil"></i> Edit
								</a>
								<a  href="<?php echo site_url("trans/jadwal_new/delete_jadwal/$la->id")?>" onclick="return confirm('Anda Yakin Hapus Data ini?')" class="btn btn-default  btn-sm">
									<i class="fa fa-trash-o"></i> Delete
								</a>
							</td>	
							</tr>
						<?php }?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>

 <!-- Bootstrap modal -->
 <?php foreach ($list_jadwal as $la) { ?>
 <form action="<?php echo site_url('trans/jadwal_new/edit_jadwal')?>" method="post">
  <div class="modal fade" id="<?php echo $la->id;?>" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
		<h3 class="modal-title">Edit Jadwal Kerja</h3>
      </div>
      <div class="modal-body form">
          <div class="row">
		  <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Nama Regu</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="kdregu" name="kdregu" required>
							<?php foreach ($list_regu as $ld){ ?>
							<option <?php if (trim($la->kdregu)==trim($ld->kdregu)) { echo 'selected';} ?> value="<?php echo trim($ld->kdregu);?>"><?php echo $ld->nmregu;?></option>
							<?php } ?>																																					
						</select>
              </div>
            </div>
		 
			<div class="form-group">
              <label class="control-label col-md-3">Tanggal Kerja</label>
              <div class="col-md-9">
                <input name="tanggal" value="<?php echo $la->tgl;?>" id="tgl" data-date-format="dd-mm-yyyy" class="form-control"  type="text">
                <input name="id" value="<?php echo $la->id;?>"  class="form-control"  type="hidden">
              </div>
            </div>
			 <div class="form-group">
              <label class="control-label col-md-3">Kode Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="kdjamkerja1" name="kdjamkerja" required>
							<?php foreach ($list_jamkerja as $ld){ ?>
							<option <?php if (trim($la->kodejamkerja)==trim($ld->kdjam_kerja)) { echo 'selected';} ?> value="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->nmjam_kerja;?></option>
							<?php } ?>																																					
						</select>
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Kerja</label>
              <div class="col-md-9">
						<select class="form-control input-sm" id="nmjamkerja1" name="nmjamkerja" readonly>
							<?php foreach ($list_jamkerja as $ld){ ?>
							<option class="<?php echo trim($ld->kdjam_kerja);?>"><?php echo $ld->jam_masuk.'-'.$ld->jam_pulang;?></option>
							<?php } ?>																																					
						</select>
              </div>
            </div>		
          </div>
          </div>
          </div>
		  
          <div class="modal-footer">
			<div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
			</div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->
	</form>
  
  <!-- Bootstrap modal -->
  <?php } ?>
 
 
 <script>

  

	
	//Date range picker
    $('#tgl').datepicker();
	$('#pilihkaryawan').selectize();
	$("[data-mask]").inputmask();
	$("#nmjamkerja1").chained("#kdjamkerja1");	

</script>