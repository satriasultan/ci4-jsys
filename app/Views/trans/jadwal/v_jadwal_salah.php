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
            <li><a href="<?php echo site_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
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
            <button class="btn btn-default dropdown-toggle " style="margin:0px; color:#000000;" id="menu1" type="button" data-bs-toggle="dropdown"><i class="fa fa-bars"></i> Menu Koreksi
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
                <th width="4%">Action</th>
                <th>Nik</th>
                <th>Nama</th>
                <th>Regu</th>
                <th>Jam Kerja</th>
                <th>Tgl</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Editan</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

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
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
                    <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Modal untuk Filter-->
<div class="modal fade" id="myModals" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="myModalLabel">Lihat Data Fingerprint Detail</h5>
            </div>
            <div class="modal-body">
                <!-- Content Data Disini -->


            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalsUpdate" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="myModalLabel">Ubah Data & Penjadwalan</h5>
            </div>
            <div class="modal-body">
                <!-- Content Data Disini -->


            </div>
            <div class="modal-footer">
            </div>
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
                "url": "<?php echo base_url('trans/jadwal_new/list_jadwal_salah')?>",
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


    // $('.ls-modal').on('click', function(e){
    //     e.preventDefault();
    //     var dataURL = $(this).attr('data-href');
    //     $('.modal-body').load(dataURL,function(){
    //         $('#myModals').modal({show:true});
    // });

    function see_fingerprint(id)
    {
            url = "load_modal_presensi" + "/" + id;
            $('.modal-body').load(url,function(){
                $('#myModals').modal({show:true});
            });

    }

    function update_jadwal(id)
    {
        url = "load_modal_update_presensi" + "/" + id;
        $('.modal-body').load(url,function(){
            $('#myModalsUpdate').modal({show:true});
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
            url : "<?php echo site_url('payroll/master/show_del_wilayah_salary/')?>/" + id,
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
                url = "<?php echo site_url('payroll/master/save_wilayah_salary')?>";
            }else if(save_method == 'update'){
                url = "<?php echo site_url('payroll/master/save_wilayah_salary')?>";
            }else if(save_method == 'delete') {
                url = "<?php echo site_url('payroll/master/save_wilayah_salary')?>";
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
