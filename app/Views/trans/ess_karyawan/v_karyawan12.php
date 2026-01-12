<?php 
/*
	@author : Junis
*/
?>
<script type="text/javascript">
	
    var save_method; //for save method string
    var table;
    $(document).ready(function() {
      table = $('#table').DataTable({ 
        
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
    });
	
	$(function() {
		$("#resign").dataTable();
		
        $('#fisikselector').change(function(){
            $('.fisiks').hide();
            $('#' + $(this).val()).show();
        });
	});	
	
	$(function() {
		//ktpseumurhidup
        $('#ktps').change(function(){
            $('.ktps').hide();
            $('#ktp' + $(this).val()).show();
            $('#modal_form').modal('show'); // show bootstrap modal
        });
	});	
	
	
	
	$('#cbxShowHide').click(function(){
			this.checked?$('#block').show(1000):$('#block').hide(1000);
		});
	
	
    function add_person()
    {
      save_method = 'add';
	  
	  $('#rootwizard').bootstrapWizard({onNext: function(tab, navigation, index) {
			if(index==1) {
				 $("#resign").dataTable();
				// Make sure we entered the name
				if($('.required').val()==true) {
					alert('Masukan Nik');
					$('#.required').focus();
					return false;
				}
			}
 
		}});	
	 
	  $('#tgl').datepicker();
	  $('#tglktp').datepicker();
	  $('#tgl2').datepicker();
	  $('#tglmasuk').datepicker();
	  $('#tglnpwp').datepicker();
	  $('#nikatasan').selectize();
	  $('#nikatasan2').selectize();
	  //$('#form')[0].reset(); // reset form on modals
      $('#modal_form').modal('show'); // show bootstrap modal
      $('.modal-title').text('Input Data Karyawan'); // Set Title to Bootstrap modal title
    }

    function edit_person(id)
    {
      save_method = 'update';
	  
	  $('#editform')[0].reset(); // reset form on modals

      //Ajax Load data from ajax
      $.ajax({
        url : "<?php echo site_url('trans/karyawan/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
           
			$('[name="nik"]').val(data.nik);
            $('[name="nmlengkap"]').val(data.nmlengkap);                        
            $('[name="callname"]').val(data.callname);                        
            $('[name="jk"]').val(data.jk);                        
            $('[name="neglahir"]').val(data.neglahir);                        
            $('[name="provlahir"]').val(data.provlahir);                        
            $('[name="kotalahir"]').val(data.kotalahir);                        
            
            // show bootstrap modal when complete loaded
			$('#modal_form').modal('hide');
			$('#edit_form').modal('show');
            $('.modal-title').text('Edit Data Karyawan'); // Set title to Bootstrap modal title
            
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
    }
	
	function detail_person(id)
    {      
	  
	  $('#detailform')[0].reset(); // reset form on modals

      //Ajax Load data from ajax
      $.ajax({
        url : "<?php echo site_url('trans/karyawan/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {           
			$('[name="nik"]').val(data.nik);
            $('[name="nmlengkap"]').val(data.nmlengkap);                        
            $('[name="callname"]').val(data.callname);                        
            $('[name="jk"]').val(data.jk);                        
            $('[name="neglahir"]').val(data.neglahir);                        
            $('[name="provlahir"]').val(data.provlahir);                        
            $('[name="kotalahir"]').val(data.kotalahir);   							
            $('[name="tgllahir"]').val(data.tgllahir);   											
            $('[name="kd_agama"]').val(data.kd_agama);   															
            $('[name="stswn"]').val(data.stswn);   															
            $('[name="stsfisik"]').val(data.stsfisik);   															
            $('[name="ketfisik"]').val(data.ketfisik);   																			
            $('[name="noktp"]').val(data.noktp);  
			/*
            //$('[name="tgl_ktp"]').val(data.tgl_ktp);   																			
            $('[name="ktp_seumurhdp"]').val(data.ktp_seumurhdp);   																							
            $('[name="ktpdikeluarakan"]').val(data.ktpdikeluarakan);   																							
            $('[name="tgldikeluarakan"]').val(data.tgldikeluarakan);   																											
            $('[name="stastus_pernikahan"]').val(data.stastus_pernikahan);   																															
            $('[name="gol_darah"]').val(data.gol_darah);   																															
            $('[name="negktp"]').val(data.negktp);   																																			
            $('[name="provktp"]').val(data.provktp);   																																											
            $('[name="kotaktp"]').val(data.kotaktp);   																																															
            $('[name="kecktp"]').val(data.kecktp);   																																															
            $('[name="kelktp"]').val(data.kelktp);   																																															
            $('[name="alamatktp"]').val(data.alamatktp);   																																																			
			$('[negtinggal"]').val(data.negtinggal);
			$('[provtinggal"]').val(data.provtinggal);
			$('[kotatinggal"]').val(data.kotatinggal);
			$('[kectinggal"]').val(data.kectinggal);
			$('[keltinggal"]').val(data.keltinggal);
			$('[alamattinggal"]').val(data.alamattinggal);
			$('[nohp1"]').val(data.nohp1);
			$('[nohp2"]').val(data.nohp2);
			$('[npwp"]').val(data.npwp);
			$('[tglnpwp"]').val(data.tglnpwp);
			$('[bag_dept"]').val(data.bag_dept);
			$('[subbag_dept"]').val(data.subbag_dept);
			$('[jabatan"]').val(data.jabatan);
			$('[lvl_jabatan"]').val(data.lvl_jabatan);
			$('[grade_golongan"]').val(data.grade_golongan);
			$('[nik_atasan"]').val(data.nik_atasan);
			$('[status_ptkp"]').val(data.status_ptkp);
			$('[besaranptkp"]').val(data.besaranptkp);
			$('[tglmasukkerja"]').val(data.tglmasukkerja);
							//'tglkeluarkerja"]').val(data.tglkeluarkerja);
			$('[masakerja"]').val(data.masakerja);
			$('[statuskepegawaian"]').val(data.statuskepegawaian);
			$('[grouppenggajian"]').val(data.grouppenggajian);
			$('[gajipokok"]').val(data.gajipokok);
			$('[gajibpjs"]').val(data.gajibpjs);
			$('[namabank"]').val(data.namabank);
			$('[namapemilikrekening"]').val(data.namapemilikrekening);
			$('[norek"]').val(data.norek);
							//'shift"]').val(data.shift);
			$('[idabsen"]').val(data.idabsen);
			$('[email"]').val(data.email);
							//'bolehcuti"]').val(data.bolehcuti);
							//'sisacuti"]').val(data.sisacuti);
			*/
            // show bootstrap modal when complete loaded
			$('#modal_form').modal('hide');
			$('#edit_form').modal('hide');
			$('#detail_form').modal('show');
            $('.modal-title').text('Detail Karyawan'); // Set title to Bootstrap modal title
            
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
      var url;
      if(save_method == 'add') 
      {
          url = "<?php echo site_url('trans/karyawan/ajax_add')?>";
		  //data = $('#form').serialize();
      }
      else
      {
        url = "<?php echo site_url('trans/karyawan/ajax_update')?>";
		//data = $('#editform').serialize();
      }

       // ajax adding data to database
          $.ajax({
            url : url,
            type: "POST",
            data: $('#form').serialize(),
            //data: data,
            dataType: "JSON",
            success: function(data)
            {
               //if success close modal and reload ajax table
               $('#modal_form').modal('hide');
               $('#edit_form').modal('hide');
               reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error Menambahkan / update data');
            }
        });
    }

    function delete_person(id)
    {
      if(confirm('Are you sure delete this data?'))
      {
        // ajax delete data to database
          $.ajax({
            url : "<?php echo site_url('trans/karyawan/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
               //if success reload ajax table
               $('#modal_form').modal('hide');
               reload_table();
			   alert('Hapus Data Sukses');
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

<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">					
		<li class="active"><a href="#tab_1" data-bs-toggle="tab"><b>DATA KARYAWAN AKTIF</b></a></li>
		<li><a href="#tab_2" data-bs-toggle="tab"><b>DATA KARYAWAN RESIGN</b></a></li>
	</ul>
</div>



<div class="tab-content">
	<div class="chart tab-pane active" id="tab_1" style="position: relative; height: 300px;" >

<div class="row">
	<div class="col-sm-12">										
		<div class="box">
			<div class="box-header">
				<div class="col-sm-12">		
					<!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Negara</a>-->
					<button class="btn btn-primary" onclick="add_person()" style="margin:10px; color:#ffffff;"><i class="glyphicon glyphicon-plus"></i> Input Data Karyawan</button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
				<table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
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
			</div><!-- /.box-body -->
		</div><!-- /.box -->								
	</div>
</div>


 <!-- Bootstrap modal -->
  <div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Form Kode Trxtype</h3>
      </div>
      <div class="modal-body form">
        <form action="<?php echo site_url('trans/karyawan/ajax_add'); ?>" method='post' id="form"> 
		<div class="form-horizontal">
			<div class="stepwizard ">
				<div class="stepwizard-row setup-panel">				
					<div class="stepwizard-step col-sm-1">
						<a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
						<p>Step 1</p>
					</div>
					<div class="stepwizard-step col-sm-1">
						<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
						<p>Step 2</p>
					</div>
						<div class="stepwizard-step col-sm-1">
						<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
						<p>Step 3</p>
					</div>				
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
					<p>Step 4</p>
				  </div>
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
					<p>Step 5</p>
				  </div>
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-6" type="button" class="btn btn-default btn-circle" disabled="disabled">6</a>
					<p>Step 6</p>
				  </div>
				  <div class="stepwizard-step col-sm-1">
					<a href="#step-7" type="button" class="btn btn-default btn-circle"  disabled="disabled">7</a>
					<p>Step 7</p>
				  </div>
				   <div class="stepwizard-step col-sm-1">
					<a href="#step-8" type="button" class="btn btn-default btn-circle"  disabled="disabled">8</a>
					<p>Step 8</p>
				  </div>
				</div>
			</div>
			<div class="row setup-content " id="step-1">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 1</h3>
					<div class="row">
						<div class="col-sm-6 ">
							<div class="box box-primary" >
								<div class="box-header">									
								</div>
								<div class="box-body">
									<!--div class="form-group">
									  <label class="control-label col-sm-3">NIK Karyawan</label>
									  <div class="col-sm-9">
										<input name="nik" id="nik" style="text-transform:uppercase;" maxlength="12" placeholder="Nomor Induk Otomatis" class="form-control" type="text" disabled >
									  </div>
									</div-->
									<div class="form-group">
									  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
									  <div class="col-sm-9">
										<input name="nmlengkap" style="text-transform:uppercase;" placeholder="Nama Lengkap" class="form-control" type="text" required>
									  </div>
									</div>
									<div class="form-group">
									  <label class="control-label col-sm-3">Nama Panggilan</label>
									  <div class="col-sm-9">
										<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
									  </div>
									</div>
									<div class="form-group">
									  <label class="control-label col-sm-3">Jenis Kelamin</label>
									  <div class="col-sm-9">
										<select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text" required>
											<option value="P">LAKI-LAKI</option>
											<option value="W">WANITA</option>
										</select>
									  </div>
									</div>
								</div>
							</div>
						</div>					
						<div class="col-sm-6 ">
							<div class="box box-primary" >
								<div class="box-header">									
								</div>
								<div class="box-body">
									<div class="form-group">
										<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>	
										<div class="col-sm-8">    
											<select type="text" name="neglahir" id='negara' class="form-control col-sm-12" required>										
												<?php foreach ($list_opt_neg as $lon){ ?>
												<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
												<?php };?>
											</select>
										</div>
									</div>
									<script type="text/javascript" charset="utf-8">
										$(document).ready(function(){
													$("#provinsi").change(function (){
														var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
														$('#kotakab').load(url);
														return false;
													})									
												});
									  $(function() {	
										$("#provinsi").chained("#negara");								
									  });
									</script>
									<div class="form-group">
										<label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>	
										<div class="col-sm-8">    
											<select type="text" name="provlahir" id='provinsi' class="form-control col-sm-12"  required="required">
												<option value="">-KOSONG-</option>
												<?php foreach ($list_opt_prov as $lop){ ?>
												<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
												<?php };?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>	
										<div class="col-sm-8">    
											<select type="text" name="kotalahir" id='kotakab' class="form-control col-sm-12" required>
												<option value="">-KOSONG-</option>
											</select>
										</div>
									</div>
									<div class="form-group">
									  <label class="control-label col-sm-3">Tanggal Lahir</label>
									  <div class="col-sm-9">
										<input name="tgllahir"   class="form-control" id="tgl" placeholder="Tanggal Lahir" data-date-format="dd-mm-yyyy" type="text" required>
									  </div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-3">Agama</label>	
										<div class="col-sm-8">    
											<select type="text" name="kd_agama" class="form-control col-sm-12" required>										
												<?php foreach ($list_opt_agama as $loa){ ?>
												<option value="<?php echo trim($loa->kdagama);?>" ><?php echo trim($loa->nmagama);?></option>																																																			
												<?php };?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>									
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-2">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 2</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">Keadaan Fisik</label>
					  <div class="col-sm-9">
						<select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							<option value="t">BAIK & SEHAT</option>
							<option value="f">CACAT FISIK</option>
						</select>
					  </div>
					</div>
					<div  class="form-group"  >
					  <div id="f" class="fisiks" style="display:none">
						  <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
						  <div class="col-sm-9">
							<textarea name="ketfisik" style="text-transform:uppercase;" placeholder="Deskripsikan Cacat fisik" class="form-control" ></textarea>
						  </div>
					  </div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				  
				</div>
			  </div>
			</div>			
			<div class="row setup-content" id="step-3">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 3</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">No KTP</label>
					  <div class="col-sm-9">
						<input name="noktp" style="text-transform:uppercase;" placeholder="No Ktp" class="form-control" type="text" maxlength="18" required>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP Dikeluaran di</label>
					  <div class="col-sm-9">
						<input name="ktpdikeluarkan" style="text-transform:uppercase;" placeholder="Kota KTP di keluarkan" class="form-control" type="text" maxlength="20">
					  </div>
					</div>							
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal KTP Dikeluaran</label>
					  <div class="col-sm-9">
						<input name="tgldikeluarkan" style="text-transform:uppercase;" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" id="tgl2" type="text" required>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP seumur hidup</label>
					  <div class="col-sm-9">								
						<select id="ktps" name="ktp_seumurhdp" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							<option value="t">IYA</option>
							<option value="f">TIDAK</option>							
						</select>
					  </div>
					</div>
					<div class="form-group">							  
						<div id="ktpf" class="ktps" style="display:none">
						  <label class="control-label col-sm-3">Tanggal Berlaku</label>
						  <div class="col-sm-9">							
								<input type="text" name="ktpberlaku" data-date-format="dd-mm-yyyy" id="tglktp" class="form-control" >							
						  </div>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Kewarganegaraan</label>
					  <div class="col-sm-9">								
						<select  name="stswn" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							<option value="t">Warga Negara Indonesia</option>
							<option value="f">Warga Negara Asing</option>
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Status Pernikahan</label>
					  <div class="col-sm-9">								
						<select name="stastus_pernikahan" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">																		
							<?php foreach ($list_opt_nikah as $lonikah){ ?>
							<option value="<?php echo trim($lonikah->kdnikah);?>" ><?php echo trim($lonikah->nmnikah);?></option>																																																			
							<?php };?>									
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Golongan Darah</label>
					  <div class="col-sm-9">								
						<select name="gol_darah" style="text-transform:uppercase;"  class="form-control" type="text">
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="AB">AB</option>
							<option value="O">O</option>
						</select>
					  </div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-4">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
					<h3> Step 4</h3>
						<div class="row">
							<div class="col-sm-6 ">
								<div class="box box-primary" >
									<div class="box-header">
										<h4>Sesuai Ktp</h4>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label class="control-label col-sm-3">NEGARA</label>	
											<div class="col-sm-8">    
												<select name="negktp" id='almnegara' class="form-control col-sm-12">										
													<?php foreach ($list_opt_neg as $lon){ ?>
													<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<script type="text/javascript" charset="utf-8">
										 $(document).ready(function(){
												$("#almprovinsi").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
													$('#almkotakab').load(url);
													return false;
												})
												
												$("#almkotakab").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kec');?>/"+$(this).val();
													$('#almkec').load(url);
													return false;
												})
												
												$("#almkec").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_des');?>/"+$(this).val();
													$('#almkeldesa').load(url);
													return false;
												})
											});
										  $(function() {	
											$("#almprovinsi").chained("#almnegara");										
										  });
										</script>
										<div class="form-group">
											<label class="control-label col-sm-3">Provinsi</label>	
											<div class="col-sm-8">    
												<select name="provktp" id='almprovinsi' class="form-control col-sm-12">
													<option value="">-KOSONG-</option>
													<?php foreach ($list_opt_prov as $lop){ ?>
													<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kota/Kabupaten</label>	
											<div class="col-sm-8">    
												<select name="kotaktp" id='almkotakab' class="form-control col-sm-12" >										
													<option value="">-Pilih provinsi Dahulu-</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kecamatan</label>	
											<div class="col-sm-8">   												
												<select name="kecktp" id='almkec' class="form-control col-sm-12" >										
													<option value="">-Pilih Kota/Kabupaten Dahulu-</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kelurahan/Desa</label>	
											<div class="col-sm-8">    
												<select name="kelktp" id='almkeldesa' class="form-control col-sm-12" >
													<option value="">-Pilih Kecamatan Dahulu-</option>
												</select>
											</div>
										</div>
										<div  class="form-group"  >							  
										  <label class="control-label col-sm-3">Alamat</label>
										  <div class="col-sm-9">
											<textarea name="alamatktp" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ></textarea>
										  </div>							  
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="box box-primary" >
									<div class="box-header">
										<h4>Sesuai Tempat Tinggal</h4>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label class="control-label col-sm-3">NEGARA</label>	
											<div class="col-sm-8">    
												<select name="negtinggal" id='almsnegara' class="form-control col-sm-12">										
													<?php foreach ($list_opt_neg as $lon){ ?>
													<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<script type="text/javascript" charset="utf-8">
											$(document).ready(function(){
												$("#almsprovinsi").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kab');?>/"+$(this).val();
													$('#almskotakab').load(url);
													return false;
												})
												
												$("#almskotakab").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_kec');?>/"+$(this).val();
													$('#almskec').load(url);
													return false;
												})
												
												$("#almskec").change(function (){
													var url = "<?php echo site_url('master/wilayah/add_ajax_des');?>/"+$(this).val();
													$('#almskeldesa').load(url);
													return false;
												})
											});
										  $(function() {	
											$("#almsprovinsi").chained("#almsnegara");			
										  });
										</script>
										<div class="form-group">
											<label class="control-label col-sm-3">Provinsi</label>	
											<div class="col-sm-8">    
												<select name="provtinggal" id='almsprovinsi' class="form-control col-sm-12" >
													<option value="">-Pilih Provinsi-</option>
													<?php foreach ($list_opt_prov as $lop){ ?>
													<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
													<?php };?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kota/Kabupaten</label>	
											<div class="col-sm-8">    
												<select name="kotatinggal" id='almskotakab' class="form-control col-sm-12" >
													<option value="">-Pilih Provinsi Dahulu-</option>												
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kecamatan</label>	
											<div class="col-sm-8">    
												<select name="kectinggal" id='almskec' class="form-control col-sm-12" >
													<option value="">-Pilih Kota/Kabupaten Dahulu-</option>										
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-sm-3">Kelurahan/Desa</label>	
											<div class="col-sm-8">    
												<select name="keltinggal" id='almskeldesa' class="form-control col-sm-12" >
													<option value="">-Pilih Kecamatan Dahulu-</option>											
												</select>
											</div>
										</div>						
										<div  class="form-group"  >							  
										  <label class="control-label col-sm-3">Alamat</label>
										  <div class="col-sm-9">
											<textarea name="alamattinggal" style="text-transform:uppercase;" placeholder="Alamat sesuai tempat tinggal" class="form-control" ></textarea>
										  </div>							  
										</div>
									</div>
							</div>
						</div>
				</div>
				</div>				
					<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					<button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>				
			  </div>
			</div>
			<div class="row setup-content" id="step-5">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 5</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP UTAMA</label>
					  <div class="col-sm-9">
						<input name="nohp1" style="text-transform:uppercase;" placeholder="Nomor Hp Utama" class="form-control" type="number" maxlength="13" required>
					  </div>
					</div>												
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP kedua</label>
					  <div class="col-sm-9">
						<input name="nohp2" style="text-transform:uppercase;" placeholder="Nomor Hp Lainnya" class="form-control" type="number" maxlength="13" >
					  </div>
					</div>											
					<div class="form-group">
					  <label class="control-label col-sm-3">Email</label>
					  <div class="col-sm-9">
						<input name="email" style="text" placeholder="Alamat email" class="form-control" type="email">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">NPWP</label>
					  <div class="col-sm-9">
						<input name="npwp" id="npwp" style="text-transform:uppercase;" placeholder="Nomor NPWP" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal NPWP</label>
					  <div class="col-sm-9">
						<input name="tglnpwp" style="text-transform:uppercase;" placeholder="Tanggal NPWP" id="tglnpwp" data-date-format="dd-mm-yyyy" class="form-control" type="text" >
					  </div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" id="lanjut" type="button">Next</button>
				  <script>
					$('#npwp').onclick(function(e){
						$('#npwp').each(function(){
						!$(this).val() || $(this).find('input[name="tglnpwp"]').prop('required',true).closest(".form-group").addClass("has-error");
						});
					});
				  </script>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-6">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 6</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Department</label>	
						<div class="col-sm-8">    
							<select name="bag_dept" id='dept' class="form-control col-sm-12" >										
								<?php foreach ($list_opt_dept as $lodept){ ?>
								<option value="<?php echo trim($lodept->kddept);?>" ><?php echo trim($lodept->nmdept);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Sub Department</label>	
						<div class="col-sm-8">    
							<select name="subbag_dept" id='subdept' class="form-control col-sm-12" >
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_subdept as $losdept){ ?>
								<option value="<?php echo trim($losdept->kdsubdept);?>" class="<?php echo trim($losdept->kddept);?>"><?php echo trim($losdept->nmsubdept);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
							  $(function() {
							$('#dept').selectize();
							//$('#jobgrade').selectize();
							$("#subdept").chained("#dept");
							//$('#subdept').selectize();
							$("#jabatan").chained("#dept");

							$("#jobgrade").chained("#jabatan");

							  //
							  //$('#jabatan').selectize();
							  });
					</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Jabatan</label>	
						<div class="col-sm-8">    
							<select name="jabatan" id='jabatan' class="form-control col-sm-12" >	
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_jabt as $lojab){ ?>
								<option value="<?php echo trim($lojab->kdjabatan);?>" class="<?php echo trim($lojab->kddept);?>"><?php echo trim($lojab->nmjabatan);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>				
					<div class="form-group">
						<label class="control-label col-sm-3">Level Jabatan</label>	
						<div class="col-sm-8">    
							<select name="lvl_jabatan" id='lvljabatan' class="form-control col-sm-12" >										
								<?php foreach ($list_opt_lvljabt as $loljab){ ?>
								<option value="<?php echo trim($loljab->kdlvl);?>" ><?php echo trim($loljab->nmlvljabatan);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>				
					<div class="form-group">
						<label class="control-label col-sm-3">Job Grade</label>
						<div class="col-sm-8"  >    
						<select name="grade_golongan" id='jobgrade' class="form-control col-sm-12" disabled readonly>
						<!--option value="">-KOSONG-</option-->
						<?php foreach ($list_chainjobgrade as $logjab){ ?>
						<!--input name="grade_golongan" id="jobgrade" class="<?php// echo trim($logjab->kdjabatan);?>" value="<?php// echo trim($logjab->kdgrade);?>" style="text-transform:uppercase;" placeholder="Job Grade" type="text" disabled readonly-->
						<option value="<?php echo trim($logjab->kdgrade);?>" class="<?php echo trim($logjab->kdjabatan);?>"><?php echo trim($logjab->nmgrade);?></option>
						<?php };?>
						</select>
						</div>
					</div>										
					<div class="form-group">
						<label class="control-label col-sm-3">Atasan</label>	
						<div class="col-sm-8">    
							<!--<select name="nik_atasan" id="nikatasan" class="form-control col-sm-12" required>	-->
							<select id="nikatasan" class="form-control" data-placeholder="Pilih Atasan" name="nik_atasan" required>
								<option value="">--Pilih Atasan--</option>
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Atasan Ke-2</label>	
						<div class="col-sm-8">    
							<!--<select name="nik_atasan" id="nikatasan" class="form-control col-sm-12" required>	-->
							<select id="nikatasan2" class="form-control" data-placeholder="Pilih Atasan" name="nik_atasan2" >
								<option value="">--Pilih Atasan--</option>
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">PTKP</label>	
						<div class="col-sm-8">    
							<select name="status_ptkp" class="form-control col-sm-12" >										
								<?php foreach ($list_opt_ptkp as $lptkp){ ?>
								<option value="<?php echo trim($lptkp->kodeptkp);?>" ><?php echo trim($lptkp->kodeptkp).' | '.trim($lptkp->besaranpertahun);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal Masuk</label>
					  <div class="col-sm-9">
						<input name="tglmasukkerja" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglmasuk" data-date-format="dd-mm-yyyy" class="form-control" type="text" required>
					  </div>
					</div>
				  <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
				  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-7">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 7</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Group Penggajian</label>	
						<div class="col-sm-8">    
							<select name="grouppenggajian" class="form-control col-sm-12">										
								<?php foreach ($list_opt_grp_gaji as $lgaji){ ?>
								<option value="<?php echo trim($lgaji->kdgroup_pg);?>" ><?php echo trim($lgaji->kdgroup_pg).' | '.trim($lgaji->nmgroup_pg);?></option>																																																			
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji Pokok</label>
					  <div class="col-sm-9">
						<input name="gajipokok" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" maxlength="12" type="number" required>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS</label>
					  <div class="col-sm-9">
						<input name="gajibpjs" style="text-transform:uppercase;" placeholder="Gaji BPJS" class="form-control" maxlength="12" type="number">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama BANK</label>
					  <div class="col-sm-9">
						<select name="namabank" id='dept' class="form-control col-sm-12" >										
							<?php foreach ($list_opt_bank as $lbank){ ?>
							<option value="<?php echo trim($lbank->kdbank);?>" ><?php echo trim($lbank->nmbank);?></option>																																																			
							<?php };?>
						</select>
						
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
					  <div class="col-sm-9">
						<input name="namapemilikrekening" style="text-transform:uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nomor Rekening</label>
					  <div class="col-sm-9">
						<input name="norek" style="text-transform:uppercase;" placeholder="Nomor Rekening" class="form-control" type="number">
					  </div>
					</div>
					<!--<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" style="text-transform:uppercase;" placeholder="Nomor ID Absensi" class="form-control" type="text" required>
					  </div>
					</div>-->
					<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					  <button class="btn btn-primary nextBtn btn-sm pull-right" type="button">Next</button>
					<!--</form>
					<!--<button class="btn btn-success btn-sm pull-right" id="btnSave" onclick="save()" type="submit">Submit</button>-->
					<!--<button class="btn btn-success btn-sm pull-right" type="submit">Submit</button>-->
				</div>
			  </div>
			</div>
			<div class="row setup-content" id="step-8">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 8</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" style="text-transform:uppercase;" placeholder="Nomor ID Absensi" class="form-control" type="text" required>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">ID Mesin</label>
					  <div class="col-sm-9">
					 <select type="text" class="form-control" name="idmesin" id="idmesin">
							<option   value="1"> 1</option>
							<option   value="2"> 2</option>
							<option   value="3"> 3</option>
							<option   value="4"> 4</option>
							<option   value="5"> 5</option>
							<option   value="6"> 6</option>
							<option   value="7"> 7</option>
					</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Card Number</label>
					  <div class="col-sm-9">
						<input name="cardnumber" style="text-transform:uppercase;" maxlength="12" placeholder="Card Number" class="form-control" type="text">
					  </div>
					</div>
					
					<button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Back</button>
					</form>
					<!--<button class="btn btn-success btn-sm pull-right" id="btnSave" onclick="save()" type="submit">Submit</button>-->
					<button class="btn btn-success btn-sm pull-right" type="submit">Submit</button>
				</div>
			  </div>
			</div>	
          </div>  
				
          </div>
          <div class="modal-footer">			           
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
		
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal --> 
  
 
  
  <!-- Edit modal -->
  <div class="modal fade" id="edit_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Edit Data</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="editform">
          <!--<input type="hidden" value="" name="id"/> -->  
			<div class="form-horizontal">
           <div class="form-group">
							  <label class="control-label col-sm-3">NIK Karyawan</label>
							  <div class="col-sm-9">
								<input name="nik" id="nik" style="text-transform:uppercase;" placeholder="Nomor Induk Karyawan" class="form-control" type="text" required>
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
							  <div class="col-sm-9">
								<input name="nmlengkap" style="text-transform:uppercase;" placeholder="Nama Lengkap" class="form-control" type="text" required>
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-sm-3">Nama Panggilan</label>
							  <div class="col-sm-9">
								<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text">
							  </div>
							</div>
							<div class="form-group">
							  <label class="control-label col-sm-3">Jenis Kelamin</label>
							  <div class="col-sm-9">
								<select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text" required>
									<option value="P">LAKI-LAKI</option>
									<option value="W">WANITA</option>
								</select>
							  </div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>	
								<div class="col-sm-8">    
									<select name="neglahir" id='enegara' class="form-control col-sm-12" required>										
										<?php foreach ($list_opt_neg as $lon){ ?>
										<option value="<?php echo trim($lon->kodenegara);?>"><?php echo trim($lon->namanegara);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<script type="text/javascript" charset="utf-8">
							  $(function() {	
								$("#eprovinsi").chained("#enegara");		
								$("#ekotakab").chained("#eprovinsi");		
							  });
							</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>	
								<div class="col-sm-8">    
									<select name="provlahir" id='eprovinsi' class="form-control col-sm-12" required >
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_prov as $lop){ ?>
										<option value="<?php echo trim($lop->kodeprov);?>" class="<?php echo trim($lop->kodenegara);?>"><?php echo trim($lop->namaprov);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>	
								<div class="col-sm-8">    
									<select name="kotalahir" id='ekotakab' class="form-control col-sm-12" required>
										<option value="">-KOSONG-</option>
										<?php foreach ($list_opt_kotakab as $lok){ ?>
										<option value="<?php echo trim($lok->kodekotakab);?>" class="<?php echo trim($lok->kodeprov);?>"><?php echo trim($lok->namakotakab);?></option>																																																			
										<?php };?>
									</select>
								</div>
							</div>						                                 
        </form>
          </div>
          </div>
          <div class="modal-footer">
            <button type="submit" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <!-- End Bootstrap modal -->
  	</div>
	
	
<!--- DATA KARYAWAN RESIGN -->	
<!--import department-->

<div class="tab-pane" id="tab_2" style="position: relative; height: 300px;" >

	<div class="box">
	<div class="col-lg-12">
	<!-- general form elements -->
	<div class="box box-primary">
	<div class="box-header">
	<h3 class="box-title">DATA KARYAWAN RESIGN</h3>
	</div><!-- /.box-header -->
	<!-- form start -->
			<div class="box-body table-responsive" style='overflow-x:scroll;'>
			<table id="resign" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
			<tr>
			<th>No.</th>
			<th>NIK</th>
			<th>Nama Lengkap</th>
			<th>Department</th>
			<th>Jabatan</th>
			<th>Tgl Resign</th>
			<th>Alamat</th>

			</tr>
			</thead>
			<tbody>
			<?php $no=0; foreach($list_resignkary as $ls): $no++; ?>
			<tr>
			<td><?php echo $no;?></td>
			<td><?php echo $ls->nik;?></td>
			<td><?php echo $ls->nmlengkap;?></td>
			<td><?php echo $ls->nmdept;?></td>
			<td><?php echo $ls->nmjabatan;?></td>
			<td><?php echo $ls->tglkeluarkerja;?></td> <!-- salah cok hahah-->
			<td><?php echo $ls->alamatktp;?></td> <!-- salah cok hahah-->

			</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
			</div><!-- /.box-body -->
	</div><!-- /.box -->
	</div>
	</div>
</div>	
