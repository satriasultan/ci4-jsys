<?php
/*
	@author : junis \m/
*/
?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
                $("#example2").dataTable();
                $("#example4").dataTable();
                $("#nikatasan1").selectize();
                $("#nikatasan2").selectize();
				//datemask
				//$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
				//$("#datemaskinput").daterangepicker();
				$("#dateinput").datepicker();
				$("#tgl").datepicker();
				$(".tgl").datepicker();
				$("#tglktp").datepicker();
				$("#tgl2").datepicker();
				$("#tglmasuk").datepicker();
				$("#tglmasuk2").datepicker();
				$("#tglnpwp2").datepicker();
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
<?php echo $message;?>
</br>
<div class="row">
	<div class="col-sm-12">
	<form action="<?php echo site_url('trans/ess_karyawan/ajax_update'); ?>" method='post'>
		<div class="form-horizontal">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1" data-bs-toggle="tab">Profile Karyawan</a></li>
				<li><a href="#tab_2" data-bs-toggle="tab">Fisik</a></li>
				<li><a href="#tab_3" data-bs-toggle="tab">Data ID</a></li>
				<li><a href="#tab_4" data-bs-toggle="tab">Alamat</a></li>
				<li><a href="#tab_5" data-bs-toggle="tab">Kontak</a></li>
				<li><a href="#tab_6" data-bs-toggle="tab">Jabatan Pekerjaan</a></li>
				<li><a href="#tab_7" data-bs-toggle="tab">Salary</a></li>
				<li><a href="#tab_8" data-bs-toggle="tab">Absensi</a></li>
			</ul>
		</div>
        <div class="tab-content">
			<div class="tab-pane active" id="tab_1">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 1</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">NIK Karyawan</label>
					  <div class="col-sm-6">
						<input name="nik" id="nik" style="text-transform:uppercase;" value="<?php echo $dtl['nik'];?>" placeholder="Nomor Induk Karyawan" class="form-control" type="text" readonly >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Lengkap Karyawan</label>
					  <div class="col-sm-6">
						<input name="nmlengkap" style="text-transform:uppercase;" value="<?php echo $dtl['nmlengkap'];?>" placeholder="Nama Lengkap" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Panggilan</label>
					  <div class="col-sm-6">
						<input name="callname" style="text-transform:uppercase;" placeholder="Nama Panggilan" value="<?php echo $dtl['callname'];?>" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Jenis Kelamin</label>
					  <div class="col-sm-6">
						<select  name="jk" style="text-transform:uppercase;"  class="form-control" type="text" >
							<option value="L" <?php if (trim($dtl['jk'])=='L') { echo 'selected';}?>>LAKI-LAKI</option>
							<option value="P" <?php if (trim($dtl['jk'])=='P') { echo 'selected';}?>>PEREMPUAN</option>
						</select>
					  </div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Tanggal Lahir</label>
                        <div class="col-sm-6">
                            <input name="tgllahir" value="<?php echo date("d-m-Y",strtotime(trim($dtl['tgllahir'])));?>" style="text-transform:uppercase;" placeholder="Tanggal Lahir" id="tglahir" data-date-format="dd-mm-yyyy"  class="form-control tgl" type="text" >
                        </div>
                    </div>
					<div class="form-group">
						<label class="control-label col-sm-3">Tempat Lahir (Negara)</label>
						<div class="col-sm-6">
                            <select name="neglahir" class="form-control input-sm " placeholder="---KETIK NEGARA---" id="neglahir">
                                <option value="<?php echo trim($dtl['neglahir']);?>" class=""><?php echo trim($dtl['namanegara']);?></option>
                            </select>
						</div>
					</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#neglahir').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodenegara',
            labelField: 'namanegara',
            searchField: ['namanegara'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namanegara) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_negara'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _paramxnegara_: "  "
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        }).on('change click', function() {
            //console.log('provlahir >> on.change');
            //console.log('provlahir >> clear');
            $('#provlahir')[0].selectize.clearOptions();
        });
    });
</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Tempat Lahir (Provinsi)</label>
						<div class="col-sm-6">
                            <select name="provlahir" class="form-control input-sm " placeholder="---KETIK PROVINSI---" id="provlahir">
                                <option value="<?php echo trim($dtl['provlahir']);?>" class=""><?php echo trim($dtl['nmprovlahir']);?></option>
                            </select>
						</div>
					</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#provlahir').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodeprov',
            labelField: 'namaprov',
            searchField: ['namaprov'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namaprov) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_prov'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _kodenegara_: $('#neglahir').val()
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        }).on('change click', function() {
            //console.log('provlahir >> on.change');
            //console.log('kotalahir >> clear');
            $('#kotalahir')[0].selectize.clearOptions();
        });


    });
</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Tempat Lahir (Kota/Kabupaten)</label>
						<div class="col-sm-6">
                            <select name="kotalahir" class="form-control input-sm " placeholder="---KETIK KOTA---" id="kotalahir">
                                <option value="<?php echo trim($dtl['kotalahir']);?>" class=""><?php echo trim($dtl['nmkotalahir']);?></option>
                            </select>
						</div>
					</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#kotalahir').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodekotakab',
            labelField: 'namakotakab',
            searchField: ['namakotakab'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakotakab) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_kota'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _kodenegara_: $('#neglahir').val(),
                        _kodeprov_: $('#provlahir').val()
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        });
    });
</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Agama</label>
						<div class="col-sm-6">
							<select name="kd_agama" class="form-control col-sm-12" >
								<?php foreach ($list_opt_agama as $loa){ ?>
								<option value="<?php echo trim($loa->kdagama);?>" <?php if (trim($dtl['kd_agama'])==trim($loa->kdagama)) { echo 'selected';}?> ><?php echo trim($loa->nmagama);?></option>
								<?php };?>
							</select>
						</div>
					</div>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_2">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 2</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">Keadaan Fisik</label>
					  <div class="col-sm-6">
						<select id="fisikselector" name="stsfisik" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<option value="t" <?php if (trim($dtl['stsfisik'])=='T') { echo 'selected';}?>>BAIK & SEHAT</option>
							<option value="f" <?php if (trim($dtl['stsfisik'])=='F') { echo 'selected';}?>>CACAT FISIK</option>
						</select>
					  </div>
					</div>
					<div  class="form-group"  >
					  	  <label class="control-label col-sm-3">Keterangan Jika Cacat</label>
						  <div class="col-sm-6">
							<textarea name="ketfisik" style="text-transform:uppercase;" value="<?php echo $dtl['ketfisik'];?>" placeholder="Deskripsikan Cacat fisik" class="form-control" ></textarea>
						  </div>
						</div>

				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_3">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 3</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">No KTP</label>
					  <div class="col-sm-9">
						<input name="noktp" style="text-transform:uppercase;" placeholder="No Ktp" value="<?php echo $dtl['noktp'];?>" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP Dikeluaran di</label>
					  <div class="col-sm-9">
						<input name="ktpdikeluarkan" style="text-transform:uppercase;" value="<?php echo $dtl['ktpdikeluarkan'];?>" placeholder="Kota KTP di keluarkan" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal KTP Dikeluaran</label>
					  <div class="col-sm-9">
						<input name="tgldikeluarkan" style="text-transform:uppercase;" value="<?php echo date("d-m-Y",strtotime(trim($dtl['tgldikeluarkan'])));?>" placeholder="Tanggal KTP Di keluarkan" data-date-format="dd-mm-yyyy" class="form-control" id="tgl2" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">KTP seumur hidup</label>
					  <div class="col-sm-9">
						<select id="ktpseumurhidup" name="ktp_seumurhdp" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<option value="f" <?php if (trim($dtl['ktp_seumurhdp'])=='F') { echo 'selected';}?>>TIDAK</option>
							<option value="t" <?php if (trim($dtl['ktp_seumurhdp'])=='T') { echo 'selected';}?>>IYA</option>
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">No Kartu Keluarga (KK)</label>
					  <div class="col-sm-9">
						<input name="nokk" style="text-transform:uppercase;" placeholder="NO KK" value="<?php echo $dtl['nokk'];?>" class="form-control" type="text">
					  </div>
					</div>
					<div class="form-group">
						<div id="f" class="ktpseumurhidupku" style="display:none">
						  <label class="control-label col-sm-3">Tanggal Berlaku</label>
						  <div class="col-sm-9">
							<textarea name="tglberlaku" id="tglktp"  style="text-transform:uppercase;" value="<?php echo $dtl['tglberlaku'];?>" data-date-format="dd-mm-yyyy" class="form-control" ></textarea>
						  </div>
						</div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Kewarganegaraan</label>
					  <div class="col-sm-9">
						<select  name="stswn" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<option value="t" <?php if (trim($dtl['stswn'])=='T') { echo 'selected';}?>>Warga Negara Indonesia</option>
							<option value="f" <?php if (trim($dtl['stswn'])=='F') { echo 'selected';}?>>Warga Negara Asing</option>
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Status Pernikahan</label>
					  <div class="col-sm-9">
						<select name="status_pernikahan" style="text-transform:uppercase;" placeholder="Nama Panggilan" class="form-control" type="text" >
							<?php foreach ($list_opt_nikah as $lonikah){ ?>
							<option value="<?php echo trim($lonikah->kdnikah);?>" <?php if (trim($dtl['status_pernikahan'])==trim($lonikah->kdnikah)) { echo 'selected';}?> ><?php echo trim($lonikah->nmnikah);?></option>
							<?php };?>
						</select>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Golongan Darah</label>
					  <div class="col-sm-9">
						<select name="gol_darah" style="text-transform:uppercase;"  class="form-control" type="text" >
							<option value="A" <?php if (trim($dtl['gol_darah'])=='A') { echo 'selected';}?>>A</option>
							<option value="B" <?php if (trim($dtl['gol_darah'])=='B') { echo 'selected';}?>>B</option>
							<option value="AB" <?php if (trim($dtl['gol_darah'])=='AB') { echo 'selected';}?>>AB</option>
							<option value="O" <?php if (trim($dtl['gol_darah'])=='O') { echo 'selected';}?>>O</option>
						</select>
					  </div>
					</div>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_4">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 4</h3>
				  <div class="form-group">
								<label class="control-label col-sm-3">NEGARA (sesuai KTP)</label>
								<div class="col-sm-8">
                                    <select name="negktp" class="form-control input-sm " placeholder="---KETIK NEGARA---" id="almnegara">
                                        <option value="<?php echo trim($dtl['negktp']);?>" class=""><?php echo trim($dtl['namanegara']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#almnegara').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodenegara',
            labelField: 'namanegara',
            searchField: ['namanegara'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namanegara) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_negara'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _paramxnegara_: "  "
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        }).on('change click', function() {
            //console.log('provlahir >> on.change');
            //console.log('provlahir >> clear');
            $('#almprovinsi')[0].selectize.clearOptions();
        });


    });
</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Provinsi (sesuai KTP)</label>
								<div class="col-sm-8">
									<select name="provktp" id='almprovinsi' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['provktp']);?>" class=""><?php echo trim($dtl['nmprovktp']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
        $(function() {
            var totalCount,
                page,
                perPage = 7;
            $('#almprovinsi').selectize({
                plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                valueField: 'kodeprov',
                labelField: 'namaprov',
                searchField: ['namaprov'],
                options: [],
                create: false,
                render: {
                    option: function(item, escape) {
                        return '' +
                            '<div class=\'row\'>' +
                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namaprov) + '</div>' +
                            '</div>' +
                            '';
                    }
                },
                load: function(query, callback) {
                    query = JSON.parse(query);
                    page = query.page || 1;

                    if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                        $.post(base('master/wilayah/add_prov'), {
                            _search_: query.search,
                            _perpage_: perPage,
                            _page_: page,
                            _kodenegara_: $('#almnegara').val(),
                        })
                            .done(function(json) {
                                console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                totalCount = json.totalcount;
                                callback(json.group);
                            })
                            .fail(function( jqxhr, textStatus, error ) {
                                callback();
                            });
                    } else {
                        callback();
                    }
                }
            }).on('change click', function() {
                //console.log('provlahir >> on.change');
                //console.log('kotalahir >> clear');
                $('#almkotakab')[0].selectize.clearOptions();
            });


        });
    </script>
							<div class="form-group">
								<label class="control-label col-sm-3">Kota/Kabupaten (sesuai KTP)</label>
								<div class="col-sm-8">
									<select name="kotaktp" id='almkotakab' class="form-control col-sm-12"  >
                                        <option value="<?php echo trim($dtl['kotaktp']);?>" class=""><?php echo trim($dtl['nmkotaktp']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#almkotakab').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodekotakab',
            labelField: 'namakotakab',
            searchField: ['namakotakab'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakotakab) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_kota'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _kodenegara_: $('#almnegara').val(),
                        _kodeprov_: $('#almprovinsi').val()
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        }).on('change click', function() {
            console.log('#almkec >> on.change');
            console.log('#almkec >> clear');
            $('#almkec')[0].selectize.clearOptions();
        });
    });
</script>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Kecamatan (sesuai KTP)</label>
                            <div class="col-sm-8">
                                    <select name="kecktp" id='almkec' class="form-control col-sm-12"  >
                                    <option value="<?php echo trim($dtl['kecktp']);?>" class=""><?php echo trim($dtl['nmkecktp']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#almkec').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodekec',
            labelField: 'namakec',
            searchField: ['namakec'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakec) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_kec'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _kodenegara_: $('#almnegara').val(),
                        _kodekotakab_: $('#almkotakab').val(),
                        _kodeprov_: $('#almprovinsi').val(),
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        }).on('change click', function() {
                console.log('#almkeldesa >> on.change');
                console.log('#almkeldesa >> clear');
            $('#almkeldesa')[0].selectize.clearOptions();
        });
    });
</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Kelurahan/Desa (sesuai KTP)</label>
								<div class="col-sm-8">
									<select name="kelktp" id='almkeldesa' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['kelktp']);?>" class=""><?php echo trim($dtl['nmdesaktp']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#almkeldesa').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodekeldesa',
            labelField: 'namakeldesa',
            searchField: ['namakeldesa'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakeldesa) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_desa'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _kodenegara_: $('#almnegara').val(),
                        _kodeprov_: $('#almprovinsi').val(),
                        _kodekotakab_: $('#almkotakab').val(),
                        _kodekec_: $('#almkec').val(),
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        });
    });
</script>

							<div  class="form-group"  >
							  <label class="control-label col-sm-3">Alamat (sesuai KTP)</label>
							  <div class="col-sm-9">
								<textarea name="alamatktp" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ><?php echo $dtl['alamatktp'];?></textarea>
							  </div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">NEGARA (Sesuai Tempat Tinggal)</label>
								<div class="col-sm-8">
									<select name="negtinggal" id='negtinggal' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['negtinggal']);?>" class=""><?php echo trim($dtl['namanegara']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
                        $(function() {
                            var totalCount,
                                page,
                                perPage = 7;
                            $('#negtinggal').selectize({
                                plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                valueField: 'kodenegara',
                                labelField: 'namanegara',
                                searchField: ['namanegara'],
                                options: [],
                                create: false,
                                render: {
                                    option: function(item, escape) {
                                        return '' +
                                            '<div class=\'row\'>' +
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namanegara) + '</div>' +
                                            '</div>' +
                                            '';
                                    }
                                },
                                load: function(query, callback) {
                                    query = JSON.parse(query);
                                    page = query.page || 1;

                                    if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                        $.post(base('master/wilayah/add_negara'), {
                                            _search_: query.search,
                                            _perpage_: perPage,
                                            _page_: page,
                                            _paramxnegara_: "  "
                                        })
                                            .done(function(json) {
                                                console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                                totalCount = json.totalcount;
                                                callback(json.group);
                                            })
                                            .fail(function( jqxhr, textStatus, error ) {
                                                callback();
                                            });
                                    } else {
                                        callback();
                                    }
                                }
                            }).on('change click', function() {
                                //console.log('provlahir >> on.change');
                                //console.log('provlahir >> clear');
                                $('#provtinggal')[0].selectize.clearOptions();
                            });


                        });
        </script>
							<div class="form-group">
								<label class="control-label col-sm-3">Provinsi (Sesuai Tempat Tinggal)</label>
								<div class="col-sm-8">
									<select name="provtinggal" id='provtinggal' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['provtinggal']);?>" class=""><?php echo trim($dtl['nmprovtinggal']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
                        $(function() {
                            var totalCount,
                                page,
                                perPage = 7;
                            $('#provtinggal').selectize({
                                plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                valueField: 'kodeprov',
                                labelField: 'namaprov',
                                searchField: ['namaprov'],
                                options: [],
                                create: false,
                                render: {
                                    option: function(item, escape) {
                                        return '' +
                                            '<div class=\'row\'>' +
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namaprov) + '</div>' +
                                            '</div>' +
                                            '';
                                    }
                                },
                                load: function(query, callback) {
                                    query = JSON.parse(query);
                                    page = query.page || 1;

                                    if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                        $.post(base('master/wilayah/add_prov'), {
                                            _search_: query.search,
                                            _perpage_: perPage,
                                            _page_: page,
                                            _kodenegara_: $('#almnegara').val(),
                                        })
                                            .done(function(json) {
                                                console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                                totalCount = json.totalcount;
                                                callback(json.group);
                                            })
                                            .fail(function( jqxhr, textStatus, error ) {
                                                callback();
                                            });
                                    } else {
                                        callback();
                                    }
                                }
                            }).on('change click', function() {
                                //console.log('provlahir >> on.change');
                                //console.log('kotalahir >> clear');
                                $('#kotatinggal')[0].selectize.clearOptions();
                            });


                        });
</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Kota/Kabupaten (Sesuai Tempat Tinggal)</label>
								<div class="col-sm-8">
									<select name="kotatinggal" id='kotatinggal' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['kotatinggal']);?>" class=""><?php echo trim($dtl['nmkotatinggal']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
            $(function() {
                var totalCount,
                    page,
                    perPage = 7;
                $('#kotatinggal').selectize({
                    plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                    valueField: 'kodekotakab',
                    labelField: 'namakotakab',
                    searchField: ['namakotakab'],
                    options: [],
                    create: false,
                    render: {
                        option: function(item, escape) {
                            return '' +
                                '<div class=\'row\'>' +
                                /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                                '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakotakab) + '</div>' +
                                '</div>' +
                                '';
                        }
                    },
                    load: function(query, callback) {
                        query = JSON.parse(query);
                        page = query.page || 1;

                        if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                            $.post(base('master/wilayah/add_kota'), {
                                _search_: query.search,
                                _perpage_: perPage,
                                _page_: page,
                                _kodenegara_: $('#almnegara').val(),
                                _kodeprov_: $('#almprovinsi').val()
                            })
                                .done(function(json) {
                                    console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                    totalCount = json.totalcount;
                                    callback(json.group);
                                })
                                .fail(function( jqxhr, textStatus, error ) {
                                    callback();
                                });
                        } else {
                            callback();
                        }
                    }
                }).on('change click', function() {
                    console.log('#kectinggal >> on.change');
                    console.log('#kectinggal >> clear');
                    $('#kectinggal')[0].selectize.clearOptions();
                });
            });
</script>
							<div class="form-group">
								<label class="control-label col-sm-3">Kecamatan (Sesuai Tempat Tinggal)</label>
								<div class="col-sm-8">
									<select name="kectinggal" id='kectinggal' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['kectinggal']);?>" class=""><?php echo trim($dtl['nmkectinggal']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
                        $(function() {
                            var totalCount,
                                page,
                                perPage = 7;
                            $('#kectinggal').selectize({
                                plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                valueField: 'kodekec',
                                labelField: 'namakec',
                                searchField: ['namakec'],
                                options: [],
                                create: false,
                                render: {
                                    option: function(item, escape) {
                                        return '' +
                                            '<div class=\'row\'>' +
                                            /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakec) + '</div>' +
                                            '</div>' +
                                            '';
                                    }
                                },
                                load: function(query, callback) {
                                    query = JSON.parse(query);
                                    page = query.page || 1;

                                    if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                        $.post(base('master/wilayah/add_kec'), {
                                            _search_: query.search,
                                            _perpage_: perPage,
                                            _page_: page,
                                            _kodenegara_: $('#almnegara').val(),
                                            _kodekotakab_: $('#almkotakab').val(),
                                            _kodeprov_: $('#almprovinsi').val(),
                                        })
                                            .done(function(json) {
                                                console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                                totalCount = json.totalcount;
                                                callback(json.group);
                                            })
                                            .fail(function( jqxhr, textStatus, error ) {
                                                callback();
                                            });
                                    } else {
                                        callback();
                                    }
                                }
                            }).on('change click', function() {
                                console.log('#keltinggal >> on.change');
                                console.log('#keltinggal >> clear');
                                $('#keltinggal')[0].selectize.clearOptions();
                            });
                        });
                    </script>
							<div class="form-group">
								<label class="control-label col-sm-3">Kelurahan/Desa (Sesuai Tempat Tinggal)</label>
								<div class="col-sm-8">
									<select name="keltinggal" id='keltinggal' class="form-control col-sm-12" >
                                        <option value="<?php echo trim($dtl['keltinggal']);?>" class=""><?php echo trim($dtl['nmdesatinggal']);?></option>
									</select>
								</div>
							</div>
<script type="text/javascript">
    $(function() {
        var totalCount,
            page,
            perPage = 7;
        $('#keltinggal').selectize({
            plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
            valueField: 'kodekeldesa',
            labelField: 'namakeldesa',
            searchField: ['namakeldesa'],
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    return '' +
                        '<div class=\'row\'>' +
                        /*  '<div class=\'col-xs-2 col-md-2 text-nowrap\'>' + escape(item.nodok) + '</div>' +*/
                        '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.namakeldesa) + '</div>' +
                        '</div>' +
                        '';
                }
            },
            load: function(query, callback) {
                query = JSON.parse(query);
                page = query.page || 1;

                if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                    $.post(base('master/wilayah/add_desa'), {
                        _search_: query.search,
                        _perpage_: perPage,
                        _page_: page,
                        _kodenegara_: $('#almnegara').val(),
                        _kodeprov_: $('#almprovinsi').val(),
                        _kodekotakab_: $('#almkotakab').val(),
                        _kodekec_: $('#almkec').val(),
                    })
                        .done(function(json) {
                            console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                            totalCount = json.totalcount;
                            callback(json.group);
                        })
                        .fail(function( jqxhr, textStatus, error ) {
                            callback();
                        });
                } else {
                    callback();
                }
            }
        });
    });
</script>
							<div  class="form-group"  >
							  <label class="control-label col-sm-3">Alamat (Sesuai Tempat Tinggal)</label>
							  <div class="col-sm-9">
								<textarea name="alamattinggal" style="text-transform:uppercase;" placeholder="Alamat sesuai dengan KTP" class="form-control" ><?php echo $dtl['alamattinggal'];?></textarea>
							  </div>
							</div>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_5">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 5</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP UTAMA</label>
					  <div class="col-sm-9">
						<input name="nohp1" value="<?php echo $dtl['nohp1'];?>" style="text-transform:uppercase;" placeholder="Nomor Handphone Utama" class="form-control" type="input">
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">NO HP kedua</label>
					  <div class="col-sm-9">
						<input name="nohp2" value="<?php echo $dtl['nohp2'];?>" style="text-transform:uppercase;" placeholder="Nomor Handphone Lainnya" class="form-control" type="input" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Email</label>
					  <div class="col-sm-9">
						<input name="email" value="<?php echo $dtl['email'];?>" style="text-transform:uppercase;" placeholder="Alamat email" class="form-control" type="email" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">NPWP</label>
					  <div class="col-sm-9">
						<input name="npwp" value="<?php echo $dtl['npwp'];?>" style="text-transform:uppercase;" placeholder="Nomor NPWP" class="form-control" type="input" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Tanggal NPWP</label>
					  <div class="col-sm-9">

						<input name="tglnpwp" style="text-transform:uppercase;" value="<?php echo $dtl['tglnpwp'];?>" data-date-format="dd-mm-yyyy" placeholder="Tanggal NPWP" class="form-control" id="tglnpwp2" type="text" >
					  </div>
					</div>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_6">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 6</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Department</label>
						<div class="col-sm-8">
							<select name="dept" id='dept' class="form-control col-sm-12">
								<?php foreach ($list_opt_dept as $lodept){ ?>
								<option value="<?php echo trim($lodept->kddept);?>" <?php if (trim($dtl['bag_dept'])==trim($lodept->kddept)) { echo 'selected';}?> ><?php echo trim($lodept->nmdept);?></option>
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Sub Department</label>
						<div class="col-sm-8" >
							<select name="subbag_dept" id='subdept' class="form-control col-sm-12" >
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_subdept as $losdept){ ?>
								<option value="<?php echo trim($losdept->kdsubdept);?>" <?php if (trim($dtl['subbag_dept'])==trim($losdept->kdsubdept)) { echo 'selected';}?> class="<?php echo trim($losdept->kddept);?>"><?php echo trim($losdept->nmsubdept);?></option>
								<?php };?>
							</select>
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
					  $(function() {
						$("#subdept").chained("#dept");
						$("#jabatan").chained("#subdept");
						///$("#jobgrade").chained("#lvljabatan");
					  });
					</script>
					<div class="form-group">
						<label class="control-label col-sm-3">Jabatan</label>
						<div class="col-sm-8">
							<select name="jabatan" id='jabatan' class="form-control col-sm-12" >
								<option value="">-KOSONG-</option>
								<?php foreach ($list_opt_jabt as $lojab){ ?>
								<option value="<?php echo trim($lojab->kdjabatan);?>" <?php if (trim($dtl['jabatan'])==trim($lojab->kdjabatan)) { echo 'selected';}?> class="<?php echo trim($lojab->kdsubdept);?>"><?php echo trim($lojab->nmjabatan);?></option>
								<?php };?>
							</select>
						</div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Level Jabatan</label>
                        <div class="col-sm-8">
                            <select name="lvl_jabatan" class="form-control input-sm " placeholder="---KETIK LEVEL JABATAN---" id="lvl_jabatan">
                                <option value="<?php echo trim($dtl['lvl_jabatan']);?>" class=""><?php echo trim($dtl['nmlvljabatan']);?></option>
                            </select>
                        </div>
                    </div>
<script type="text/javascript">
                        $(function() {
                            var totalCount,
                                page,
                                perPage = 7;
                            $('#lvl_jabatan').selectize({
                                plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                valueField: 'kdlvl',
                                labelField: 'nmlvljabatan',
                                searchField: ['nmlvljabatan'],
                                options: [],
                                create: false,
                                render: {
                                    option: function(item, escape) {
                                        return '' +
                                            '<div class=\'row\'>' +
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.kdlvl) + '</div>' +
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmlvljabatan) + '</div>' +
                                            '</div>' +
                                            '';
                                    }
                                },
                                load: function(query, callback) {
                                    query = JSON.parse(query);
                                    page = query.page || 1;

                                    if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                        $.post(base('master/jabatan/req_lvljabatan'), {
                                            _search_: query.search,
                                            _perpage_: perPage,
                                            _page_: page,
                                            _paramlvljab_: ""
                                        })
                                            .done(function(json) {
                                                console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                                totalCount = json.totalcount;
                                                callback(json.group);
                                            })
                                            .fail(function( jqxhr, textStatus, error ) {
                                                callback();
                                            });
                                    } else {
                                        callback();
                                    }
                                }
                            }).on('change click', function() {
                                console.log('lvl_jabatan >> on.change');
                                console.log('grade_golongan >> clear');
                                $('#grade_golongan')[0].selectize.clearOptions();
                            });


                        });
                    </script>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Job Grade</label>
                        <div class="col-sm-8">
                            <select name="grade_golongan" class="form-control input-sm " placeholder="---KETIK JOB GRADE---" id="grade_golongan">
                                <option value="<?php echo trim($dtl['grade_golongan']);?>" class=""><?php echo trim($dtl['nmgrade']);?></option>
                            </select>
                        </div>
                    </div>
<script type="text/javascript">
                        $(function() {
                            var totalCount,
                                page,
                                perPage = 7;
                            $('#grade_golongan').selectize({
                                plugins: ['hide-arrow', 'selectable-placeholder', 'infinite-scroll'],
                                valueField: 'kdgrade',
                                labelField: 'nmgrade',
                                searchField: ['nmgrade'],
                                options: [],
                                create: false,
                                render: {
                                    option: function(item, escape) {
                                        return '' +
                                            '<div class=\'row\'>' +
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.kdgrade) + '</div>' +
                                            '<div class=\'col-xs-5 col-md-5 text-nowrap\'>' + escape(item.nmgrade) + '</div>' +
                                            '</div>' +
                                            '';
                                    }
                                },
                                load: function(query, callback) {
                                    query = JSON.parse(query);
                                    page = query.page || 1;

                                    if( ! totalCount || totalCount > ( (page - 1) * perPage) ){
                                        $.post(base('master/jabatan/req_jobgrade'), {
                                            _search_: query.search,
                                            _perpage_: perPage,
                                            _page_: page,
                                            _lvl_jabatan_: $('#lvl_jabatan').val()
                                        })
                                            .done(function(json) {
                                                console.log('JSON Data: ' + JSON.stringify(json, null, '\t'));
                                                totalCount = json.totalcount;
                                                callback(json.group);
                                            })
                                            .fail(function( jqxhr, textStatus, error ) {
                                                callback();
                                            });
                                    } else {
                                        callback();
                                    }
                                }
                            }).on('change click', function() {
                                console.log('grade_golongan >> on.change');
                                console.log('kdlvlgp >> clear');
                                $('#kdlvlgp')[0].selectize.clearOptions();
                            });


                        });
                    </script>

					<div class="form-group">
						<label class="control-label col-sm-3">Atasan</label>
						<div class="col-sm-8">
							<select name="nik_atasan" id="nikatasan1" class="form-control col-sm-12" >
								<option value="-">---------------PILIH KARYAWAN ATASAN -------------</option>
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" <?php if (trim($dtl['nik_atasan'])==trim($loan->nik)) { echo 'selected';}?> ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>
								<?php };?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Atasan ke-2</label>
						<div class="col-sm-8">
							<select name="nik_atasan2" id="nikatasan2" class="form-control col-sm-12" >
							<option value="-">---------------PILIH KARYAWAN ATASAN -------------</option>
								<?php foreach ($list_opt_atasan as $loan){ ?>
								<option value="<?php echo trim($loan->nik);?>" <?php if (trim($dtl['nik_atasan2'])==trim($loan->nik)) { echo 'selected';}?> ><?php echo trim($loan->nik).'|'.trim($loan->nmlengkap);?></option>
								<?php };?>
							</select>
						</div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Kantor Wilayah</label>
                        <div class="col-sm-6">
                            <select name="kdcabang" id='kanwil' class="form-control col-sm-12" required>
                                <?php foreach ($list_kanwil as $lf){ ?>
                                    <!--option value="<?php echo trim($lf->kdcabang);?>" ><?php echo trim($lf->desc_cabang);?></option-->
                                    <option value="<?php echo trim($lf->kdcabang);?>" <?php if (trim($dtl['kdcabang'])==trim($lf->kdcabang)) { echo 'selected';}?> ><?php echo trim($lf->desc_cabang);?></option>
                                <?php };?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Tanggal Masuk</label>
                        <div class="col-sm-6">
                            <input name="tglmasukkerja" value="<?php echo date("d-m-Y",strtotime(trim($dtl['tglmasukkerja'])));?>" style="text-transform:uppercase;" placeholder="Tanggal Masuk Karyawan" id="tglmasuk2" data-date-format="dd-mm-yyyy"  class="form-control tgl" type="text" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Branch ID</label>
                        <div class="col-sm-6">
                            <input name="branch" value="<?php echo $dtl['branch'];?>" class="form-control" type="input" readonly>
                        </div>
                    </div>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_7">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 7</h3>
					<div class="form-group">
						<label class="control-label col-sm-3">Group Penggajian</label>
						<div class="col-sm-8">
							<select name="grouppenggajian" class="form-control col-sm-12" >
								<?php foreach ($list_opt_grp_gaji as $lgaji){ ?>
								<option value="<?php echo trim($lgaji->kdgroup_pg);?>" <?php if (trim($dtl['grouppenggajian'])==trim($lgaji->kdgroup_pg)) { echo 'selected';}?>><?php echo trim($lgaji->kdgroup_pg).' | '.trim($lgaji->nmgroup_pg);?></option>
								<?php };?>
							</select>
						</div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">PTKP</label>
                        <div class="col-sm-8">
                            <select name="status_ptkp" class="form-control col-sm-12" >
                                <?php foreach ($list_opt_ptkp as $lptkp){ ?>
                                    <option value="<?php echo trim($lptkp->kodeptkp);?>" <?php if (trim($dtl['status_ptkp'])==trim($lptkp->kodeptkp)) { echo 'selected';}?> ><?php echo trim($lptkp->kodeptkp).' | '.trim($lptkp->besaranpertahun);?></option>
                                <?php };?>
                            </select>
                        </div>
                    </div>
					<?php /*<!--
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji Pokok</label>
					  <div class="col-sm-9">
						<input name="gajipokok" value="<?php echo $dtl['gajipokok'];?>" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" type="text" readonly>
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS KES</label>
					  <div class="col-sm-9">
						<input name="gajibpjs" value="<?php echo $dtl['gajibpjs'];?>" style="text-transform:uppercase;" placeholder="Gaji BPJS KES" class="form-control" type="text" readonly>
					  </div>
					</div>

					<div class="form-group">
					  <label class="control-label col-sm-3">Gaji BPJS NAKER</label>
					  <div class="col-sm-9">
						<input name="gajinaker" value="<?php echo $dtl['gajinaker'];?>" style="text-transform:uppercase;" placeholder="Gaji BPJS NAKER" class="form-control" type="text" readonly>
					  </div>
					</div>
					-->*/ ?>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama BANK</label>
					  <div class="col-sm-9">
						<select name="namabank" id='dept' class="form-control col-sm-12" >
							<?php foreach ($list_opt_bank as $lbank){ ?>
							<option value="<?php echo trim($lbank->kdbank);?>" <?php if (trim($dtl['namabank'])==trim($lbank->kdbank)) { echo 'selected';}?> ><?php echo trim($lbank->nmbank);?></option>
							<?php };?>
						</select>
					  </div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Gaji Pokok</label>
                        <div class="col-sm-9">
                            <input name="gajipokok" value="0" style="text-transform:uppercase;" placeholder="Gaji Pokok" class="form-control" type="text" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Gaji BPJS KES</label>
                        <div class="col-sm-9">
                            <input name="gajibpjs" value="0" style="text-transform:uppercase;" placeholder="Gaji BPJS KES" class="form-control" type="text" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Gaji BPJS NAKER</label>
                        <div class="col-sm-9">
                            <input name="gajinaker" value="0" style="text-transform:uppercase;" placeholder="Gaji BPJS NAKER" class="form-control" type="text" readonly>
                        </div>
                    </div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nama Pemilik Rekening</label>
					  <div class="col-sm-9">
						<input name="namapemilikrekening" value="<?php echo $dtl['namapemilikrekening'];?>" style="text-transform:uppercase;" placeholder="Nama Pemilik Rekening" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
					  <label class="control-label col-sm-3">Nomor Rekening</label>
					  <div class="col-sm-9">
						<input name="norek" value="<?php echo trim($dtl['norek']);?>" style="text-transform:uppercase;" placeholder="Nomor Rekening" class="form-control" type="text" >
					  </div>
					</div>
					<!--<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" value="<?php echo $dtl['idabsen'];?>" style="text-transform:uppercase;" placeholder="Nomor Induk Karyawan" class="form-control" type="text" >
					  </div>
					</div>-->
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_8">
			  <div class="col-sm-12 ">
				<div class="col-sm-12">
				  <h3> Step 8</h3>
					<div class="form-group">
					  <label class="control-label col-sm-3">ID Absensi</label>
					  <div class="col-sm-9">
						<input name="idabsen" style="text-transform:uppercase;" value="<?php echo trim($dtl['idabsen']);?>" placeholder="Nomor ID Absensi" class="form-control" type="text" >
					  </div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">ID Mesin</label>
						<div class="col-sm-9">
							<select name="idmesin" id='idmesin' class="form-control col-sm-12" disabled>
								<?php foreach ($list_finger as $lf){ ?>
									<option <?php if(trim($dtl['idmesin'])==trim($lf->fingerid))?> value="<?php echo trim($lf->fingerid);?>" ><?php echo trim($lf->wilayah).' || '.trim($lf->ipaddress);?></option>
								<?php };?>
							</select>
						</div>
					</div>

					<div class="form-group">
						 <label class="control-label col-sm-3">Borong</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="borong" id="borong">
								 <option  <?php if(trim($dtl['tjborong'])=='') { echo 'selected';} ?> value="f">--PILIH BORONG--</option>
								 <option  <?php if(trim($dtl['tjborong'])=='t') { echo 'selected';} ?> value="t"> YA</option>
								 <option  <?php if(trim($dtl['tjborong'])=='f') { echo 'selected';} ?> value="f"> TIDAK</option>

								</select>
						</div>
					</div>

					<div class="form-group">
						 <label class="control-label col-sm-3">Shift</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="shift" id="shift">
								 <option  <?php if(trim($dtl['tjshift'])=='') { echo 'selected';} ?> value="f">--PILIH SHIFT--</option>
								 <option  <?php if(trim($dtl['tjshift'])=='t') { echo 'selected';} ?> value="t"> YA</option>
								 <option  <?php if(trim($dtl['tjshift'])=='f') { echo 'selected';} ?> value="f"> TIDAK</option>

								</select>
						</div>
					</div>

					<div class="form-group">
						 <label class="control-label col-sm-3">Lembur</label>
						<div class="col-sm-9">
								 <select type="text" class="form-control" name="lembur" id="lembur">
								 <option  <?php if(trim($dtl['tjlembur'])=='') { echo 'selected';} ?> value="f">--PILIH LEMBUR--</option>
								 <option  <?php if(trim($dtl['tjlembur'])=='t') { echo 'selected';} ?> value="t"> YA</option>
								 <option  <?php if(trim($dtl['tjlembur'])=='f') { echo 'selected';} ?> value="f"> TIDAK</option>

								</select>
						</div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Mobile Device ID</label>
                        <div class="col-sm-9">
                            <input name="deviceid" value="<?php echo trim($dtl['deviceid']);?>" placeholder="Mobile Device ID" class="form-control" type="text" >
                        </div>
                    </div>

				</div>
			  </div>
			</div>
        </div>
        </div>
	</div>
<div class="col-sm-12">
	<a href="<?php echo site_url('trans/ess_karyawan');?>" class="btn btn-primary" style="margin:10px">Kembali</a>
	<button type="submit" class="btn btn-primary" onclick="return confirm('Anda Yakin Ubah Data ini?')" style="margin:10px">Simpan Data</button>
</div>
</form>
</div>
