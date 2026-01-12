<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 3/17/20, 11:08 AM
 *  * Last Modified: 1/12/17, 5:40 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */

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

    function save()
    {
	  $('#btnSave').text('Memproses....!!');
	  $('#btnSave').attr('disabled',true);
      var url;
      if(save_method == 'add')
      {
          url = "<?php echo site_url('trans/absensi/tarik_data_mobile')?>";
		  data = $('#form').serialize();
      }

       // ajax adding data to database
          $.ajax({
            url : url,
            type: "POST",
            //data: $('#form').serialize(),
            data: data,
            dataType: "JSON",
            success: function(data)
            {
               //if success close modal and reload ajax table
               $('#modal_form').modal('hide');
               $('#edit_form').modal('hide');
               //reload_table();
			   $('#message').show();
			   $("#message").html("<div class='alert alert-success alert-dismissable'><i class='fa fa-check'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b>Data Sukses Di simpan</b> </div>");
  			   setTimeout(function() {
			   $("#message").hide('blind', {}, 500)
			   }, 5000);
			   $('#btnSave').text('Simpan');
			   $('#btnSave').attr('disabled',false);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $('#modal_form').modal('hide');
				alert('Error Memproses data');
				$('#btnSave').text('Simpan');
				$('#btnSave').attr('disabled',false);
            }
        });
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
<legend><?php echo $title;?></legend>
<?php echo $message;?>
<div id="message" >
</div>
<div><?php echo 'Total data: '.$ttldata; ?></div>
<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Negara</a>-->
					<a href="<?php echo site_url('trans/absensi/filter_absen_mobile');?>"  class="btn btn-default" style="margin:10px; color:#000000;">Kembali</a>
					<button class="btn btn-success" onclick="add_person()" style="margin:10px; color:#ffffff;"><i class="glyphicon glyphicon-plus"></i> Data Mesin Absen</button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Userid</th>
							<th>Badgenumber</th>
							<th>Nama</th>
							<th>CHECKTIME</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=1; foreach ($list_absen as $la){ ?>
							<tr>
								<td><?php echo $la->userid;?></td>
								<td><?php echo $la->idabsen;?></td>
								<td><?php echo $la->usersname;?></td>
								<td><?php echo $la->checktime;?></td>
							</tr>
						<?php }?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>


 <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Tarik Data</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" class="form-horizontal">
          <input type="hidden" name="tgl1" value="<?php echo $tgl1;?>" readonly />
          <input type="hidden" name="tgl2" value="<?php echo $tgl2;?>" readonly />
          <input type="hidden" name="kdcabang" value="<?php echo $kdcabang;?>"  readonly/>
          <input type="hidden" name="maplikasi" value="<?php echo $maplikasi;?>"  readonly/>
        </form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->

  <!-- Bootstrap modal -->
  <div class="modal fade" id="edit_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Form Kode kepegawaiantype</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="editform" class="form-horizontal">
          <!--<input type="hidden" value="" name="id"/> -->
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Kode Status Kepegawaian</label>
              <div class="col-md-9">
                <input name="kdkepegawaian" placeholder="Kode kepegawaiantype" class="form-control" type="text" readonly>
              </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Status Kepegawaian</label>
              <div class="col-md-9">
                <input name="nmkepegawaian" placeholder="Jenis kepegawaiantype" style="text-transform:uppercase;" class="form-control" type="text">
              </div>
            </div>
          </div>
        </form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->
