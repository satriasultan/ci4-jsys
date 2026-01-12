<?php
/*
	@author : fiky
*/

use App\Libraries\Fiky_encryption;

$this->fiky_encryption = new Fiky_encryption();
?>

<style>
    .btn-circle.btn-lg {
        width: 50px;
        height: 50px;
        padding: 10px 16px;
        font-size: 18px;
        line-height: 1.33;
        border-radius: 25px;
    }
    .btn-circle.btn-xl {
        width: 70px;
        height: 70px;
        padding: 10px 16px;
        font-size: 24px;
        line-height: 1.33;
        border-radius: 35px;
    }
    .modal-dialog-fl {
        width: 96%;
        height: 90%;
        padding: 1%;
        position: static;
    }


</style>
<!-- Content Header (Page header) -->
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
<?php echo $message;?>




<?php if($akses['a_view']==='t') { ?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-bs-toggle="tab"><b>DATA KARYAWAN AKTIF</b></a></li>
        <li><a href="#tab_2" data-bs-toggle="tab"><b>DATA KARYAWAN NON AKTIF</b></a></li>
    </ul>
</div>


<div class="tab-content">
	<div class="chart tab-pane active" id="tab_1" style="position: relative;" >
        <div class="box">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <!--<h3 class="box-title">DATA KARYAWAN AKTIF</h3>-->
                        <!--<a href="#" data-bs-toggle="modal" data-bs-target="#input" class="btn btn-primary" style="margin:10px; color:#ffffff;">Input Negara</a>-->
                        <?php if($akses['a_input']==='t') { ?>
                            <?php /*<button class="btn btn-primary" onclick="add_person()" style="margin:10px; color:#ffffff;" title="Input Data Karyawan"><i class="glyphicon glyphicon-plus"></i></button>*/ ?>
                            <a href="<?php echo base_url("trans/karyawan/input")?>"  class="btn btn-default" style="margin:10px;" title="Download Excel"><i class="glyphicon glyphicon-plus"></i> Input Karyawan </a>
                        <?php } ?>
                        <a href="#"  data-bs-toggle="modal" data-bs-target="#filter"  class="btn btn-default" style="margin:10px; color:#000000;"><i class="fa fa-filter"></i> Filter Data </a>
                        <?php if($akses['a_report']==='t') { ?>
                            <?php /* <a href="<?php echo base_url("trans/karyawan/excel_listkaryawan")?>"  class="btn btn-default" style="margin:10px;" title="Download Excel"><i class="glyphicon glyphicon-download"></i> Download </a> */ ?>
                            <a href="#"  data-bs-toggle="modal" data-bs-target="#downloadXls"  class="btn btn-default" style="margin:10px; color:#000000;"><i class="fa fa-file-excel-o"></i> Download Excel</a>
                        <?php } ?>

                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive" >
                        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th width="8%">Action</th>
                                    <th>NIK</th>
                                    <th>Nama Lengkap</th>
                                    <!--th>Picture</th--->
                                    <th>Department</th>
                                    <th>SubDepartment</th>
                                    <th>Jabatan/Section</th>
                                    <th>Tgl Masuk</th>
                                    <th>Group</th>
                                    <th>Plan</th>

                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
        </div>
    </div>
<!--- DATA KARYAWAN RESIGN -->
    <div class="tab-pane" id="tab_2" style="position: relative; height: 300px;" >
        <div class="box">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">DATA KARYAWAN RESIGN/NON AKTIF</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body table-responsive" style='overflow-x:scroll;'>
                        <table id="tableResign" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th width="1%">No.</th>
                                <th width="8%">Action</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <?php /*<th>Picture</th>*/ ?>
                                <th>Department</th>
                                <th>SubDepartment</th>
                                <th>Jabatan/Section</th>
                                <th>Tgl Nonaktif</th>
                                <th>Group</th>
                                <th>Plan</th>
                                <th>Alasan</th>

                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
        </div>
    </div>
</div>
<!-- Bootstrap modal -->

<!--Modal untuk Filter-->
<div class="modal fade" id="downloadXls" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download File Excel Data Karyawan</h4>
            </div>
            <form action="<?php echo base_url('trans/karyawan/excel_listkaryawan')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Plant</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="plant" id="plant" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Vaksin Covid 19</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="vaksin_filter" id="vaksin_filter" style="width: 100%">
                                <option value=""> ALL </option>
                                <option value="YES"> SUDAH VAKSIN </option>
                                <option value="NO"> BELUM VAKSIN </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Kota KTP</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm"  id="id_kotaktp" name="id_kotaktp"  style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Kota Domisili</label>
                            <div class="col-sm-9">
                            <select class="form-control input-sm" id="id_kotadom" name="id_kotadom"  style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Status Non Aktif / Resign</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="resign_filter" id="resign_filter" style="width: 100%">
                                <option value="ALL"> ALL </option>
                                <option value="YES"> RESIGN </option>
                                <option value="NO"> KARYAWAN AKTIF </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Status Kepegawaian</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" id="statuskepegawaian" name="statuskepegawaian_download"  style="width: 100%">
                            </select>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Bootstrap modal -->
<!-- Start Filter -->


<!--Modal untuk Filter-->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Filter Data Karyawan</h4>
      </div>
        <form id="form-filter" class="form-horizontal">
          <div class="modal-body">
          <?php /*
               <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Status Non Aktif / Resign</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" name="resign_filter" id="resign_filter" style="width: 100%">
                          <option value="ALL"> ALL </option>
                          <option value="YES"> RESIGN </option>
                          <option value="NO"> KARYAWAN AKTIF </option>
                      </select>
                  </div>
              </div>
              */ ?>
              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Tanggal Masuk Karyawan</label>
                  <div class="col-sm-9">
                      <input type="text" name="daterangefilter" id="daterangefilter" class="form-control input-sm daterangefilter" required>
                  </div>
              </div>

              <div class="form-group input-sm ">
                  <label class="label-form col-sm-3">Status Kepegawaian</label>
                  <div class="col-sm-9">
                      <select class="form-control input-sm" id="statuskepegawaianfilter" name="statuskepegawaianfilter"  style="width: 100%">
                      </select>
                  </div>
              </div>

          </div>
      </form>
      <div class="modal-footer">
          <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
          <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
      </div>

    </div>
  </div>
</div>
<!-- End Filter -->

<?php } else { ?>
<div class="box">
    <!-- general form elements -->
    <div class="box box-primary">
        <div class="box-header">
            <?php if($akses['a_report']==='t') { ?>
                <a href="#"  data-bs-toggle="modal" data-bs-target="#downloadXlsSH"  class="btn btn-default" style="margin:10px; color:#000000;"><i class="fa fa-file-excel-o"></i> Download Excel</a>
            <?php } ?>
        </div><!-- /.box-header -->
    </div>
</div>
<?php } ?>
<!--Modal untuk Filter-->
<div class="modal fade" id="downloadXlsSH" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download File Excel Data Karyawan</h4>
            </div>
            <form action="<?php echo base_url('trans/karyawan/excel_listkaryawan_sh')?>" method="post">
                <div class="modal-body">
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Plant</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="plant" id="plant" style="width: 100%">

                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Vaksin Covid 19</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="vaksin_filter" id="vaksin_filter" style="width: 100%">
                                <option value=""> ALL </option>
                                <option value="YES"> SUDAH VAKSIN </option>
                                <option value="NO"> BELUM VAKSIN </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Kota KTP</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm"  id="id_kotaktp" name="id_kotaktp"  style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                            <label class="label-form col-sm-3">Kota Domisili</label>
                            <div class="col-sm-9">
                            <select class="form-control input-sm" id="id_kotadom" name="id_kotadom"  style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="form-group input-sm ">
                        <label class="label-form col-sm-3">Status Non Aktif / Resign</label>
                        <div class="col-sm-9">
                            <select class="form-control input-sm" name="resign_filter" id="resign_filter" style="width: 100%">
                                <option value=""> ALL </option>
                                <option value="YES"> RESIGN </option>
                                <option value="NO"> KARYAWAN AKTIF </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Bootstrap modal -->
<!-- Start Filter -->





<script type="text/javascript">
    $(".daterangefilter").daterangepicker({
        //singleDatePicker: true,
        //showDropdowns: true
        //autoUpdateInput: false,
        locale: {
          cancelLabel: 'Clear'
        }
    });
    $("#daterangefilter").val('');

//     $('.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
//       $(this).val(picker.startDate.format('dd-mm-yyyy') + ' - ' + picker.endDate.format('dd-mm-yyyy'));
//     });
//
//     $('.daterangepicker').on('cancel.daterangepicker', function(ev, picker) {
//       $(this).val('');
//     });


    var save_method; //for save method string
    var table;
    var tableResign;
    $(document).ready(function() {
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
                "url": "<?php echo base_url('trans/karyawan/ajax_list')?>",
                "type": "POST",
                "data": function(data) {
                    data.tglrange = $('#daterangefilter').val();
                    data.statuskepegawaianfilter = $('#statuskepegawaianfilter').val();
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


        // DATA TABEL RESIGN KARYAWAN
        //datatables
        tableResign = $('#tableResign').DataTable({

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "language": {
                <?php echo $this->fiky_encryption->constant('datatable_language'); ?>
            },

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo base_url('trans/karyawan/list_resign_karyawan')?>",
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

    });

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax
    }


// ranah filter
    $('#btn-filter').click(function(){ //button filter event click
        var table = $('#table');
        table.DataTable().ajax.reload(); //reload datatable ajax
        $('#filter').modal('hide');
    });
    $('#btn-reset').click(function(){ //button reset event click
        $('#form-filter')[0].reset();
        var table = $('#table');
        table.DataTable().ajax.reload(); //reload datatable ajax
        $('#filter').modal('hide');
    });

</script>

<script type="text/javascript" src="<?= base_url('assets/pagejs/master/karyawan.js') ?>"></script>