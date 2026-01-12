<?php
/*
 * Author: Fiky Ashariza
 * Create Date: 3/29/21, 2:40 PM
 * Path Directory: v_list_import.php
 */

?>
<script type="text/javascript">
            $(function() {
                $("#example1").dataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "deferRender":    true,
                    "scrollX":        true,
                    "scrollY":        false,
                    "scrollCollapse": true,
                    "scroller":       true});
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
<style>
    .text-wrap{
        white-space:normal;
    }
    .width-200{
        width:100px;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<?php echo $message; ?>
<div class="row">
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="card">
            <form action="<?php echo base_url('stock/bbk/proses_upload_pbl')?>" method="post" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputFile">File Import PBL</label>
                        <input type="file" id="import" name="import" required>
                        <p class="help-block">Data Harus Berextensi xls/x & Kolom Berformat Text (Excel).</p>
                    </div>
                    <div class="checkcard">
                        <label>
                            <input type="checkbox" required> Saya Bertanggung Jawab atas data yang saya Upload ke Sistem
                        </label>
                    </div>
                </div><!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" value="Import" name="save" class="btn btn-sm btn-success"><i class="fa fa-sign-in"></i> Proses Excel </button>
                    <a href="<?= base_url('assets/files/item/example/contoh_import_pbl.xlsx') ?>" class="btn btn-default  btn-sm"><i class="fa fa-file-excel-o"></i> Template Pbl(PBR) </a>
                    <a href="<?= base_url('assets/files/item/example/contoh_import_pbl_retur.xlsx') ?>" class="btn btn-warning  btn-sm"><i class="fa fa-file-excel-o"></i> Template Pbl Retur(TRB) </a>
                    <?php if ($adaisi>0) { ?>
                        <a href="<?= base_url('stock/bbk/clear_tmp_pbl') ?>" class="btn btn-danger btn-sm float-right"><i class="fa fa-trash-o"></i> Clear Data </a>
                        <a href="<?= base_url('stock/bbk/final_tmp_pbl') ?>" class="btn btn-primary btn-sm  float-right"><i class="fa fa-sign-in"></i> Final Data </a>
                    <?php } ?>
                </div>
            </form>
        </div><!-- /.card -->
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-header">
			</div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="example1" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th>Brg</th>
                        <th>Nama Brg</th>
                        <th>Unit</th>
                        <th>Date</th>
                        <th>Jurnal</th>
                        <th>Rem</th>
                        <th>QtyK</th>
                        <th>SubJurnal</th>
                        <th>Batch</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($list_detail as $lu): $no++;?>
                        <tr>
                            <td><?php echo $no;?></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->idbarang;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->namabarangx;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->unit;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo date("d-m-Y",strtotime($lu->trxdate));?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->docno;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->description;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->onhandtranspo;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->docjns;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->docref;?></div></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
		</div><!-- /.card -->
	</div>
</div>




