<?php
/**
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 5/2/19 1:27 PM
 *  * Last Modified: 4/23/19 9:09 AM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2019 .All rights reserved.
 *
 */

?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable();
				$("#dateinput").datepicker();
                $('form').attr("autocomplete", "off");
                $(".focusnya").focus();
               // $(".selection").selectize();

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

<div class="box">
    <div class="box-header">
        <div class="col-sm-12">
            <h3> </h3>
        </div>
        <div class="col-sm-12">
            <button class="btn btn-success focusnya" onclick="add_map_jamkerja()"><i class="glyphicon glyphicon-plus"></i> Input </button>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive" style='overflow-x:scroll;'>
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th width="1%" >No.</th>
                <th>Kode Regu</th>
                <th>Jam Kerja</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Kode Mapping</th>
                <th>Hold</th>
                <th width="10%">Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Input Level Gaji</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="INPUT" name="type"/>
                    <div class="form-body">
                        <div class="form-group ilang">
                            <label class="control-label col-md-3">PILIH KODE REGU</label>
                            <div class="col-md-9">
                                <input type="hidden" name="id" placeholder="" class="form-control" type="text" style="text-transform:uppercase;">
                                <select name="kdregu" class="form-control inform c_hold del selection" style="text-transform:uppercase;">
                                    <option value="">--Pilih Regu--</option>
                                    <?php foreach($list_regu as $lg){?>
                                        <option value="<?php echo trim($lg->kdregu);?>"> <?php echo trim($lg->kdregu).' || '.trim($lg->nmregu);?> </option>
                                    <?php }?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">PILIH KODE JAM KERJA</label>
                            <div class="col-md-9">
                                <select name="kdjam_kerja" class="form-control inform del selection" style="text-transform:uppercase;">
                                    <option value="">--Pilih Kode Jam Kerja--</option>
                                    <?php foreach($list_jamkerja as $ljk){?>
                                        <option value="<?php echo trim($ljk->kdjam_kerja);?>"> <?php echo trim($ljk->kdjam_kerja).' || '.trim($ljk->nmjam_kerja);?> </option>
                                    <?php }?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">KODE MAPING BARU</label>
                            <div class="col-md-9">
                                <input onClick="this.select();" name="mapjam_kerja" placeholder="INPUT NAMA MAPPING" class="form-control c_hold del" type="text" MAXLENGTH="4" style="text-transform:uppercase;">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Hold</label>
                            <div class="col-md-9">
                                <select name="c_hold" class="form-control c_hold inform del" style="text-transform:uppercase;" >
                                    <!--option value="">--Pilih Hold--</option-->
                                    <option value="NO"> NO </option>
                                    <option value="YES">YES </option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


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
                "url": "<?php echo site_url('trans/jadwal_new/list_mapping_jam_kerja')?>",
                "type": "POST",
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

        //datepicker
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "top auto",
            todayBtn: true,
            todayHighlight: true,
        });

    });



    function add_map_jamkerja()
    {
        save_method = 'add';

        $('.ilang').prop("hidden", false);
        $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        }); // show bootstrap modal
        $('.modal-title').text('Input Mapping Jam Kerja'); // Set Title to Bootstrap modal title
        $('[name="type"]').val('INPUT');
        $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Simpan');
        $('[name="kdregu"]').prop("required", true);
        $('[name="kdjam_kerja"]').prop("required", true);
        $('.del').prop("disabled", false);
    }

    function ubah_map_jamkerja(id)
    {
        save_method = 'update';
        $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('.rmchild').remove();
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('trans/jadwal_new/show_edit_map_jadwal')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                var v_kdregu = (data.kdregu != null ? data.kdregu.trim() : "");
                var v_kdjam_kerja = (data.kdjam_kerja != null ? data.kdjam_kerja.trim() : "");

              /*  var selectElement  = $('[name="kdjam_kerja"]').eq(0);
                var selectize = selectElement.data('selectize');
                if (!!selectize) selectize.setValue(v_kdjam_kerja);*/

                $('.ilang').prop("hidden", true);
                $('.del').prop("disabled", false);
                $('[name="type"]').val('EDIT');
                $('[name="id"]').val(data.id);
                $('[name="kdregu"]').val(v_kdregu);
                $('[name="kdjam_kerja"]').val(v_kdjam_kerja);
                $('[name="mapjam_kerja"]').val(data.mapjam_kerja);
                $('#btnSave').removeClass("btn-danger").addClass("btn-primary").text('Ubah');
                //$('[name="dob"]').datepicker('update',data.dob);
                $('#modal_form').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                }); // show bootstrap modal
                $('.modal-title').text('Ubah Data Mapping'); // Set title to Bootstrap modal title
                //.removeClass("btn-primary").addClass("btn-danger"); // set button


            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function hapus_map_jamkerja(id)
    {
        save_method = 'delete';
        //$('#modal_form').removeData('bs.modal');
        $('.inform').removeAttr("disabled").removeAttr("readonly").removeAttr("text");
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('trans/jadwal_new/show_edit_map_jadwal')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {

                var v_kdregu = (data.kdregu != null ? data.kdregu.trim() : "");
                var v_kdjam_kerja = (data.kdjam_kerja != null ? data.kdjam_kerja.trim() : "");

                $('.ilang').prop("hidden", true);
                $('[name="type"]').val('DELETE');
                $('[name="id"]').val(data.id);
                $('[name="kdregu"]').val(v_kdregu);
                $('[name="kdjam_kerja"]').val(v_kdjam_kerja);
                $('[name="mapjam_kerja"]').val(data.mapjam_kerja);
                $('.del').prop("disabled", true);
                $('#btnSave').removeClass("btn-primary").addClass("btn-danger").text('Hapus');
                //$('[name="dob"]').datepicker('update',data.dob);
                $('#modal_form').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                }); // show bootstrap modal
                $('.modal-title').text('Hapus Data Mapping'); // Set title to Bootstrap modal title
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
      /*  var validator = $('#form').data('bootstrapValidator');
        validator.validate();
        if (validator.isValid()) {*/
            $('#btnSave').text('saving...'); //change button text
            $('#btnSave').attr('disabled', true); //set button disable
            var url;

            if (save_method == 'add') {
                url = "<?php echo site_url('trans/jadwal_new/save_mapping_jam_kerja')?>";
            } else if (save_method == 'update') {
                url = "<?php echo site_url('trans/jadwal_new/save_mapping_jam_kerja')?>";
            } else if (save_method == 'delete') {
                url = "<?php echo site_url('trans/jadwal_new/save_mapping_jam_kerja')?>";
            }

            // ajax adding data to database
            $.ajax({
                url: url,
                type: "POST",
                data: $('#form').serialize(),
                dataType: "JSON",
                success: function (data) {

                    if (data.status) //if success close modal and reload ajax table
                    {
                        $('#modal_form').modal('hide');
                        reload_table();
                    }

                    $('#btnSave').text('save'); //change button text
                    $('#btnSave').attr('disabled', false); //set button enable


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Gagal Menyimpan / Ubah data / data sudah ada');
                    $('#btnSave').text('save'); //change button text
                    $('#btnSave').attr('disabled', false); //set button enable

                }
            });
        /*}*/
    }


</script>