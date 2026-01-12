<?php
/**
 * Created by PhpStorm.
 * User: FIKY-PC
 * Date: 13/04/2019
 * Time: 10:26
 */
use App\Libraries\Fiky_encryption;
$this->fiky_encryption = new Fiky_encryption();
?>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#dateinput").datepicker();
        $('form').attr("autocomplete", "off");
        $(".focusnya").focus();
    });

</script>
<style>
    .ratakanan { text-align : right; }
</style>

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
<h3><?php echo $title; ?></h3>
<?php echo $message; ?>

<div class="box">
    <div class="box-header">
        <div class="col-sm-12">
            <h3> </h3>
        </div>
        <div class="dropdown pull-right">
            <button class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" id="menu1" type="button" data-bs-toggle="dropdown"><i class="fa fa-bars"></i> Menu Verifikasi
                <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" >
                <li role="presentation"><a role="menuitem" href="#"  tabindex="0" data-bs-toggle="modal" data-bs-target="#EditReguPerNik" ><i class="fa fa-gear"></i>Ubah Jadwal Per NIK</a></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-search"></i>Filter</a></li>
                <!--<li role="presentation"><a role="menuitem" tabindex="-1" data-bs-toggle="modal" data-bs-target="#download_ex"  href="#"><i class="fa fa-download"></i>Download XLS 2007</a></li>-->
                <li role="presentation" ><a role="menuitem" tabindex="-1" href="#" onclick="reload_table()"><i class="fa fa-refresh"></i> Reload </a></li>
            </ul>
        </div>

    </div><!-- /.box-header -->
    <div class="box-body table-responsive" style='overflow-x:scroll;'>
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th width="1%" >No.</th>
                <th width="8%">Action</th>
                <th>Dokumen</th>
                <th>Periode</th>
                <th>Nik</th>
                <th>Nama Lengkap</th>
                <th>Departemen</th>
                <th>Regu</th>
                <th>Status</th>
                <th>Input Oleh</th>
                <th>Tanggal</th>

            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

<!--Modal Edit Untuk Pemilihan Nik Karyawan-->
<div class="modal fade" id="EditReguPerNik" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Jadwal Per Regu Dengan Pemilihan NIK Karyawan</h4>
            </div>
            <form action="<?php echo base_url('trans/jadwal_new/edit_verifikasi_mutasi_jadwalpernik')?>" method="post">
                <div class="modal-body">
                    <!--div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Pilih REGU</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm pilihrg" id="kdregu2" name="kdregu2">
                                <option value="">--Pilih Nama Regu--</option>
                                <!?php foreach ($list_regu as $ld){ ?>
                                    <option value="<!?php echo trim($ld->kdregu);?>"><!?php echo $ld->nmregu;?></option>
                                <!?php } ?>
                            </select>
                        </div>
                    </div-->
                    <div class="form-group input-sm ">

                        <label class="label-form col-sm-3">Pilih Bulan</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name='bln' required>

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
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Pilih Tahun</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="thn" required>
                                <option value='<?php $tgl=date('Y')+1; echo $tgl; ?>'><?php $tgl=date('Y')+1; echo $tgl; ?></option>
                                <option selected value='<?php $tgl=date('Y'); echo $tgl; ?>'><?php $tgl=date('Y'); echo $tgl; ?></option>
                                <option value='<?php $tgl=date('Y')-1; echo $tgl; ?>'><?php $tgl=date('Y')-1; echo $tgl; ?></option>
                                <option value='<?php $tgl=date('Y')-2; echo $tgl; ?>'><?php $tgl=date('Y')-2; echo $tgl; ?></option>

                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit1" class="btn btn-primary">NEXT STEP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Modal untuk Filter-->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">FILTER VERIFIKASI JADWAL</h4>
            </div>
            <form id="form-filter" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tanggal Pengajuan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control input-sm tglrange" id="tglrange" name="tglrange" value="" data-date-format="dd-mm-yyyy" placeholder="Pilih Tanggal">
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Status Dokumen</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name='status' id="status">
                                <option value=""> --PILIH STATUS--</option>
                                <option value="A">BUTUH PERSETUJUAN</option>
                                <option value="P">FINAL/PRINT</option>
                                <option value="C">BATAL DOKUMEN</option>
                            </select>
                        </div>
                    </div>
                    <!--<div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Tahun</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="thn" required>
                                <option value='<?php /*$tgl=date('Y'); echo $tgl; */?>'><?php /*$tgl=date('Y'); echo $tgl; */?></option>
                                <option value='<?php /*$tgl=date('Y')-1; echo $tgl; */?>'><?php /*$tgl=date('Y')-1; echo $tgl; */?></option>
                                <option value='<?php /*$tgl=date('Y')-2; echo $tgl; */?>'><?php /*$tgl=date('Y')-2; echo $tgl; */?></option>
                                <option value='<?php /*$tgl=date('Y')+1; echo $tgl; */?>'><?php /*$tgl=date('Y')+1; echo $tgl; */?></option>
                            </select>
                        </div>
                    </div>-->
                    <!--<div class="form-group input-sm ">
                        <label class="label-form col-sm-3">KARYAWAN</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm selet" id="nik" name="nik">
                                <option value="">--PILIH KARYAWAN--</option>
                                <?php /*foreach ($list_karyawan as $ld){ */?>
                                    <option value="<?php /*echo trim($ld->nik);*/?>"><?php /*echo trim($ld->nik).'||'.$ld->nmlengkap;*/?></option>
                                <?php /*} */?>
                            </select>
                        </div>
                    </div>-->
                    <!--<div class="form-group input-sm ">
                        <label class="label-form col-sm-3">REGU</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm  selet" id="kdregu" name="kdregu">
                                <option value="">--Pilih Nama Regu--</option>
                                <?php /*foreach ($list_regu as $ld){ */?>
                                    <option value="<?php /*echo trim($ld->kdregu);*/?>"><?php /*echo $ld->nmregu;*/?></option>
                                <?php /*} */?>
                            </select>
                        </div>
                    </div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
                    <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    var save_method; //for save method string
    var table;
    $(document).ready(function() {

        $('form').on('focus', 'input[type=number]', function (e) {
            $(this).on('mousewheel.disableScroll', function (e) {
                e.preventDefault()
            })
        })


        //datatables
        table = $('#table').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language": {
                <?php echo $this->fiky_encryption->constant('datatable_language'); ?>
            },

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('trans/jadwal_new/list_verifikasi_jadwal_kerja')?>",
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#tglrange').val();
                    data.status = $('#status').val();
                },
                "dataFilter": function(data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json); // return JSON string
                }
            },

            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            ],

        });

        $('#btn-filter').click(function(){ //button filter event click
            table.ajax.reload();  //just reload table
            $('#filter').modal('hide');
        });
        $('#btn-reset').click(function(){ //button reset event click
            $('#form-filter')[0].reset();
            table.ajax.reload();  //just reload table
            $('#filter').modal('hide');
        });

        //datepicker
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

        $(".tglrange").daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        });

        $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    });



    function add_wilayah()
    {
        save_method = 'add';
        var validator = $('#form').data('bootstrapValidator');
        validator.resetForm();
        $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('.eek').show(); // clear error string
        $('#modal_form').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        }); // show bootstrap modal
        $('.modal-title').text('Input Mapping Filter User'); // Set Title to Bootstrap modal title
        $('[name="type"]').val('INPUT');
        $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
        $('[name="kdwilayah"]').focus();
    }

    function ubah_wilayah(id)
    {
        save_method = 'update';
        $('.eek').hide(); // clear error string
        var validator = $('#form').data('bootstrapValidator');
        validator.resetForm();
        $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('payroll/master/show_edit_wilayah_salary/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                /*variable untuk kondisi khusus table*/
                var v_golongan = (data.golongan != null ? data.golongan.trim() : "");
                var v_nominal = (data.nominal != null ? Math.round(data.nominal.replace(',','.')) : "0");
                var v_chold = (data.c_hold != null ? data.c_hold.trim() : "");

                $('[name="type"]').val('EDIT');
                $('[name="kdwilayah"]').val(data.kdwilayah).prop("disabled", true);
                $('[name="kdwilayahnominal"]').val(data.kdwilayahnominal).prop("readonly", true);
                $('[name="nmwilayahnominal"]').val(data.nmwilayahnominal);
                $('[name="golongan"]').val(v_golongan);
                $('[name="nominal"]').val(v_nominal);
                $('[name="c_hold"]').val(v_chold);
                //$('[name="dob"]').datepicker('update',data.dob);
                $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Ubah');
                $('#modal_form').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                }); // show bootstrap modal
                $('.modal-title').text('Ubah Data Wilayah'); // Set title to Bootstrap modal title
                $('[name="kdwilayah"]').focus();

                //console.log(data.golongan);
                //console.log(v_nominal);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function hapus_wilayah(id)
    {
        save_method = 'delete';
        $('.eek').hide(); // clear error string
        var validator = $('#form').data('bootstrapValidator');
        validator.resetForm();
        //$('#modal_form').removeData('bs.modal');
        $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo base_url('payroll/master/show_del_wilayah_salary/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                /*variable untuk kondisi khusus table*/
                var v_golongan = (data.golongan != null ? data.golongan.trim() : "");
                var v_nominal = (data.nominal != null ? Math.round(data.nominal.replace(',','.')) : "0");
                var v_chold = (data.c_hold != null ? data.c_hold.trim() : "");

                $('[name="type"]').val('DELETE');
                $('[name="kdwilayah"]').val(data.kdwilayah).prop("readonly", true);
                $('[name="kdwilayahnominal"]').val(data.kdwilayahnominal).prop("readonly", true);
                $('[name="nmwilayahnominal"]').val(data.nmwilayahnominal).prop("readonly", true);
                $('[name="golongan"]').val(v_golongan);
                $('[name="nominal"]').val(v_nominal);
                $('[name="c_hold"]').val(v_chold);
                $('.c_hold').prop("disabled", true);
                $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Hapus');
                //$('[name="dob"]').datepicker('update',data.dob);
                $('#modal_form').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                }); // show bootstrap modal
                $('.modal-title').text('Hapus Data Wilayah'); // Set title to Bootstrap modal title
                $('[name="kdwilayah"]').focus();
                //.removeClass("btn-primary").addClass("btn-danger"); // set button

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
        var validator = $('#form').data('bootstrapValidator');
        validator.validate();
        if (validator.isValid())
        {
            $('#btnSave').text('saving...'); //change button text
            $('#btnSave').attr('disabled',true); //set button disable
            var url;

            if(save_method == 'add') {
                url = "<?php echo base_url('payroll/master/save_wilayah_salary')?>";
            }else if(save_method == 'update'){
                url = "<?php echo base_url('payroll/master/save_wilayah_salary')?>";
            }else if(save_method == 'delete') {
                url = "<?php echo base_url('payroll/master/save_wilayah_salary')?>";
            }

            // ajax adding data to database
            $.ajax({
                url : url,
                type: "POST",
                data: $('#form').serialize(),
                dataType: "JSON",
                success: function(data)
                {

                    if(data.status) //if success close modal and reload ajax table
                    {
                        $('#modal_form').modal('hide');
                        reload_table();
                    }

                    $('#btnSave').text('save'); //change button text
                    $('#btnSave').attr('disabled',false); //set button enable


                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Gagal Menyimpan / Ubah data / data sudah ada');
                    $('#btnSave').text('save'); //change button text
                    $('#btnSave').attr('disabled',false); //set button enable

                }
            });
        }

    }


</script>
