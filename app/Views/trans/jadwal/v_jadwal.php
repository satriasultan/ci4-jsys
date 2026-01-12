<div id="message" >
	
</div>
<div class="row">
	<div class="col-md-3">
		<div class="box box-primary">
			<div class="box-header">
				<button class="btn btn-primary" onclick="add_person()" style="margin:10px; color:#ffffff;"><i class="glyphicon glyphicon-plus"></i> Input Jadwal Kerja</button>				
				<button class="btn btn-primary" onclick="filter_ajax()" style="margin:10px; color:#ffffff;"><i class="glyphicon glyphicon-search"></i> Filter Data</button>				
			</div>
			<div class="box-body">
				<!-- the events -->
				 
			</div><!-- /.box-body -->
		</div><!-- /. box -->                            
	</div><!-- /.col -->
	<div class="col-md-9">
		<div class="box box-primary">                                
			<div class="box-body no-padding">
				<!-- THE CALENDAR -->
				<div id="calendar"></div>
			</div><!-- /.box-body -->
		</div><!-- /. box -->
	</div><!-- /.col -->
</div><!-- /.row --> 
					
	<script type="text/javascript">
            var save_method; //for save method string
			var table;
			$(document).ready(function() {				
				// Load data for the table's content from an Ajax source				
				$.ajax({
					url: "<?php echo site_url('trans/jadwal/ajax_list')?>",
					type: 'POST', // Send post data
					//data: 'type=fetch',
					async: false,
					success: function(s){
						json_events = s;
					}
				});
				//events: JSON.parse(json_events)				
				$('#calendar').fullCalendar({
				   events: JSON.parse(json_events),				
				eventClick: function(event, jsEvent, view) {
		    	console.log(event.id);		                   				  		 				  
					$('#modal_form').modal('hide');
					$('#regu').html(event.title);
					$('#tgl').html(event.start);
					$('#isi').html(event.isi);
					$('#idne').html(event.inputid);
					$('#urlhps').html(event.urlhps);
					document.getElementById("tgl").value = event.start;
					$('#tgl2').datepicker(); // tgl
					//$('#edit_form').modal('show');	
					$('#edit_form').modal();					
				}
				});	
			  
			});			
			
			function getFreshEvents(){
				$.ajax({
					url: "<?php echo site_url('trans/jadwal/ajax_list')?>",
					type: 'POST', // Send post data
					//data: 'type=fetch',
					async: false,
					success: function(s){
						freshevents = s;
					}
				});
				$('#calendar').fullCalendar('removeEvents');
				$('#calendar').fullCalendar('addEventSource', JSON.parse(freshevents));
			}
			
			
			function add_person()
			{
			  save_method = 'add';
			  $('#form')[0].reset(); // reset form on modals
			  $('#tgl').datepicker(); // tgl
			  $('#modal_form').modal('show'); // show bootstrap modal
			  $('.modal-title').text('Input Jadwal Kerja'); // Set Title to Bootstrap modal title
			}
			
			function filter_ajax()
			{
			  save_method = 'filter';
			  $('#form')[0].reset(); // reset form on modals
			  $('#tgl').datepicker(); // tgl
			  $('#modal_filter').modal('show'); // show bootstrap modal
			  $('.modal-title').text('Filter Data Jadwal Kerja'); // Set Title to Bootstrap modal title
			}									
			
			function save()
			{
			  var url;
			  if(save_method == 'add') 
			  {
				  url = "<?php echo site_url('trans/jadwal/ajax_add')?>";				  
			  }
			  else
			  {
				url = "<?php echo site_url('trans/jadwal/ajax_update')?>";				
			  }

			   // ajax adding data to database
				  $.ajax({
					url : url,
					type: "POST",
					data: $('#form').serialize(),					
					dataType: "JSON",
					success: function(data)
					{
					   if (data.status)//if success close modal and reload ajax table
					   {
						$('#modal_form').modal('hide');
						$('#edit_form').modal('hide');
						getFreshEvents();
						$('#calendar').fullCalendar('rerenderEvents' );
						$('#message').show();
						$("#message").html("<div class='alert alert-success alert-dismissable'><i class='fa fa-check'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b>Data Sukses Di simpan</b> </div>");
  						setTimeout(function() {
						$("#message").hide('blind', {}, 500)
						}, 5000);
					   }
					   else
					   {
						$('#modal_form').modal('hide');
						$('#message').show();
						$("#message").html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-check'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b>Data Sudah ada</b> </div>");
  						setTimeout(function() {
						$("#message").hide('blind', {}, 500)
						}, 5000);   
					   }
					   
					},

					error: function (jqXHR, textStatus, errorThrown)
					{						
						alert('Error adding / update data');
					}
				});
			}

			function delete_person(id)
			{
			  if(confirm('Anda Yakin Hapus Data Ini?'))
			  {
				// ajax delete data to database
				  $.ajax({
					url : "<?php echo site_url('trans/jadwal/ajax_delete')?>/"+id,
					type: "POST",
					dataType: "JSON",
					success: function(data)
					{
					   //if success reload ajax table
					   $('#modal_form').modal('hide');
					   $('#edit_form').modal('hide');
					   getFreshEvents();
						$('#message').show();
						$("#message").html("<div class='alert alert-warning alert-dismissable'><i class='fa fa-check'></i><button type='button' class='close' data-bs-dismiss='alert' aria-hidden='true'>×</button><b> Hapus Data Sukses</b></div>");
						setTimeout(function() {
						$("#message").hide('blind', {}, 500)
						}, 5000);
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert('Error adding / update data');						
					}
				});
				 
			  }
			}
			
			function edit_person(id)
			{
			  save_method = 'update';			  
			  $('#form')[0].reset(); // reset form on modals
			  //Ajax Load data from ajax
			  $.ajax({
				url : "<?php echo site_url('trans/jadwal/ajax_edit/')?>/" + id,
				type: "GET",
				dataType: "JSON",
				success: function(data)
				{				 
					//[{"tgl":"2016-01-18","shift_tipe":"T","nik":null,"kdregu":"A1","kodejamkerja":"IA","inputdate":"2016-01-18 14:01:20","id":"75"}]
					$('[name="id"]').val(data.id);
					$('[name="shiftkrj"]').val(data.shift_tipe);
					$('[name="tgl"]').val(data.tgl);                                    			
					$('[name="regu"]').val(data.kdregu);                                    			
					$('[name="kodejamkerja"]').val(data.kodejamkerja);                                    			
					// show bootstrap modal when complete loaded										
					$('#tgl').datepicker(); // tgl
					$('#modal_form').modal('show'); // show bootstrap modal				
					$('.modal-title').text('Edit Jadwal Kerja'); // Set title to Bootstrap modal title					
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('Error Loading Data..');			
				}
			});
			}
			
			function detail_person(id)
			{
			  save_method = 'update';			  
			  $('#form')[0].reset(); // reset form on modals
				$('#modal_detail').modal('show');
			  //Ajax Load data from ajax
			  $('#table').DataTable({         
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?php echo site_url('trans/karyawan/ajax_list')?>",
					"type": "POST"
				},

				//Set column definition initialisation properties.
				"columnDefs": [
				{ 
				  "targets": [ -1 ], //last column
				  "orderable": false, //set not orderable
				},
				],

			  });
			}
        </script>
		
		
<!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Form Kode kepegawaiantype</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" class="form-horizontal">
          <input type="hidden" value="" name="id"/>
          <div class="form-body">
			<!--
            <div class="form-group">
              <label class="control-label col-md-3">Shift</label>
              <div class="col-md-9">
                <select name="shiftkrj" placeholder="Kode kepegawaiantype" style="text-transform:uppercase;" class="form-control" type="text">
					<option value="T">IYA</option>
					<option value="F">TIDAK</option>
				</select>
              </div>
            </div>
			-->
			<div class="form-group">
              <label class="control-label col-md-3">Regu</label>
              <div class="col-md-9">
                <select name="regu" placeholder="Jenis kepegawaiantype" style="text-transform:uppercase;" class="form-control" type="text">
					<?php foreach ($opt_regu as $or){?>
						<option value="<?php echo trim($or->kdregu);?>"><?php echo $or->nmregu;?></option>
					<?php } ?>
				</select>
			  </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Jam Kerja</label>
              <div class="col-md-9">
                <select name="kodejamkerja" placeholder="Jenis kepegawaiantype" style="text-transform:uppercase;" class="form-control" type="text">
					<?php foreach ($opt_jamkerja as $ojk){?>
					<option value="<?php echo trim($ojk->kdjam_kerja);?>"><?php echo '<b>'.$ojk->nmjam_kerja.'</b>|Masuk: '.$ojk->jam_masuk.' |Rehat: '.$ojk->jam_istirahat.' |Pulang: '.$ojk->jam_pulang;?></option>
					<?php } ?>
				</select>
			  </div>
            </div>
			<div class="form-group">
              <label class="control-label col-md-3">Tanggal</label>
              <div class="col-md-9">
                <input name="tgl" id="tgl" style="text-transform:uppercase;" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>					
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
  
  <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_filter" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Filter</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" class="form-horizontal">
          <input type="hidden" value="" name="id"/>
          <div class="form-body">			
			<div class="form-group">
              <label class="control-label col-md-3">Regu</label>
              <div class="col-md-9">
                <select name="regu" placeholder="Jenis kepegawaiantype" style="text-transform:uppercase;" class="form-control" type="text">
					<?php foreach ($opt_regu as $or){?>
						<option value="<?php echo trim($or->kdregu);?>"><?php echo $or->nmregu;?></option>
					<?php } ?>
				</select>
			  </div>
            </div>					                       
          </div>
        </form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Cari</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal --> 
  
  <div id="edit_form" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="regu" class="modal-title"></h4>
            </div>
            <div id="modalBody" class="modal-body">
				<div id='isi'></div>
			</div>
            <div class="modal-footer">                          
                <div id="urlhps"></div>				                
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap modal -->
  <div class="modal fade" id="modal_detail" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">List Detail Karyawan</h3>
      </div>
      <div class="box-body table-responsive" style='overflow-x:scroll;'>
		<div class="row">
			<div class="col-md-12">
				<table id="table" class="table table-striped table-bordered" cellspacing="0" width="50%" style="margin:5px; padding:5;">
					<thead>
						<tr>
							<th>No.</th>
							<th>NIK</th>
							<th>Nama Lengkap</th>																								
							<th>Department</th>																								
							<th>Jabatan</th>																								
							<th>Tgl Masuk</th>																								
							<th>Action</th>						
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>			
			</div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal --> 