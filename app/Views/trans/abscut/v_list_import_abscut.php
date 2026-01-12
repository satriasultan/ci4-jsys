<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 3/29/21, 2:40 PM
 * Path Directory: v_list_import.php
 */

?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
                $("#example2").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
                $("#example3").dataTable({ "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] });
				$("#dateinput").datepicker();
				$("#dateinput1").datepicker();
				$("#dateinput2").datepicker();
				$("#dateinput3").datepicker();
				$("[data-mask]").inputmask();
				$('#pilihkaryawan').selectize();
            });
</script>
 <script>
    /*$(document).ready(function() {
        function disableBack() { window.history.forward() }

        window.onload = disableBack();
        window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
    });*/
</script>

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
<?php echo $message;?>

<div class="row">
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
            </div><!-- /.box-header -->
            <!-- form start -->
            <form action="<?php echo base_url('trans/abscut/proses_upload')?>" method="post" enctype="multipart/form-data" role="form">
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputFile">File Import Lembur</label>
                        <input type="file" id="import" name="import" required>
                        <p class="help-block">Data Harus Berextensi xls/x & Kolom Berformat Text (Excel).</p>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" required> Saya Bertanggung Jawab atas data yang saya Upload ke Sistem
                        </label>
                    </div>
                </div><!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" value="Import" name="save" class="btn btn-success"><i class="fa fa-sign-in"></i> Proses Excel </button>
                    <a href="<?= base_url('assets/files/abscut/example/abscut_template.xlsx') ?>" class="btn btn-default"><i class="fa fa-file-excel-o"></i> Download Template </a>
                    <?php if ($adaisi>0) { ?>
                        <a href="<?= base_url('trans/abscut/clear_tmp') ?>" class="btn btn-danger pull-right"><i class="fa fa-trash-o"></i> Clear Data </a>
                        <a href="<?= base_url('trans/abscut/final_data') ?>" class="btn btn-primary pull-right"><i class="fa fa-sign-in"></i> Final Data </a>
                    <?php } ?>
                </div>
            </form>
        </div><!-- /.box -->
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
			</div><!-- /.box-header -->
            <div class="box-body table-responsive" style='overflow-x:scroll;'>
                <table id="example1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Department</th>
                        <th>Section</th>
                        <th>Group</th>
                        <th>Plant</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Status</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($list_detail as $lu): $no++;?>
                        <tr>
                            <td width="2%"><?php echo $no;?></td>
                            <td><?php echo $lu->nik;?></td>
                            <td><?php echo $lu->nmlengkap;?></td>
                            <td><?php echo $lu->nmdept;?></td>
                            <td><?php echo $lu->nmsubdept;?></td>
                            <td><?php echo $lu->nmgroupgol;?></td>
                            <td><?php echo $lu->locaname;?></td>
                            <td width="5%"><?php echo $lu->tglawal1;?></td>
                            <td width="2%" class="ratakanan"><?php echo str_replace('.',',',$lu->durasimenit) ;?></td>
                            <td><?php echo trim($lu->nmtypecuti);?></td>
                            <td><?php echo $lu->description;?></td>
                            <td><?php echo $lu->nmstatus;?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>




