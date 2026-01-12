<?php
/*
	@author : Junis
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

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-bs-toggle="tab"><b>DATA KARYAWAN CANDIDATE KARYAWAN OS</b></a></li>
        <!--<li><a href="#tab_2" data-bs-toggle="tab"><b>DATA KARYAWAN NON AKTIF</b></a></li>-->
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
                            <a href="<?php echo base_url("trans/candidateos/input")?>"  class="btn btn-default" style="margin:10px;" title="Input Karyawan Outsource"><i class="glyphicon glyphicon-plus"></i> Input Karyawan OS </a>
                        <?php } ?>
                        <?php /*
                            <a href="<?php echo base_url("trans/candidateos/excel_listkaryawan")?>"  class="btn btn-default" style="margin:10px;" title="Download Excel"><i class="glyphicon glyphicon-download"></i> Download </a>
                        */ ?>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive" >
                        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th width="1%">No.</th>
                                    <th width="2%">Action</th>
                                    <th>Dokumen</th>
                                    <th>Nama Lengkap</th>
                                    <th>Picture</th>
                                    <th>Department</th>
                                    <th>SubDepartment</th>
                                    <th>Tgl Masuk</th>
                                    <th>Group</th>
                                    <th>Plan</th>
                                    <th>Pegawai</th>
                                    <th>Status</th>
                                    <th>ApprovNik</th>
                                    <th>InputBy</th>
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

<!-- End Bootstrap modal -->
<script type="text/javascript">

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
                "url": "<?php echo base_url('trans/candidateos/ajax_list')?>",
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
                "url": "<?php echo base_url('trans/candidateos/list_resign_karyawan')?>",
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

</script>

<script type="text/javascript" src="<?= base_url('assets/pagejs/master/candidateos.js') ?>"></script>