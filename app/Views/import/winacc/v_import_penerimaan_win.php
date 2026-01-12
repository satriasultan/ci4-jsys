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
            "deferRender":    false,
            "scrollX":        true,
            "scrollY":        false,
            "scrollCollapse": true,
            "ordering": false,
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
<style>
    .text-wrap{
        white-space:normal;
    }
    .width-90{
        width:90px;
    }
    .width-150{
        width:150px;
    }
    .width-200{
        width:200px;
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
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
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
            <form action="<?php echo base_url('import/winacc/proses_upload_penerimaan')?>" method="post" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputFile">File Import PENERIMAAN BRG (Winacc)</label>
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
                    <a href="<?= base_url('assets/files/Purchaseorder/example/import_penerimaan.xlsx') ?>" class="btn btn-default  btn-sm"><i class="fa fa-file-excel-o"></i> Template Import PENERIMAAN BRG</a>
                    <?php /* if ($adaisi>0) {
                        <a href="<?= base_url('stock/bbm/clear_tmp_lbm') ?>" class="btn btn-danger btn-sm float-right"><i class="fa fa-trash-o"></i> Clear Data </a>
                        <a href="<?= base_url('stock/bbm/final_data_lbm') ?>" class="btn btn-primary btn-sm  float-right"><i class="fa fa-sign-in"></i> Final Data </a>
                     } */ ?>
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
                        <th>tbl</th>
                        <th>date_</th>
                        <th>duedate</th>
                        <th>cur</th>
                        <th>kurs</th>
                        <th>sub</th>
                        <th>subname</th>
                        <th>slsm</th>
                        <th>slsmname</th>
                        <th>brg</th>
                        <th>brgname</th>
                        <th>qty</th>
                        <th>qtybonus</th>
                        <th>valbonus</th>
                        <th>valwithdiscval</th>
                        <th>discval</th>
                        <th>ppnval</th>
                        <th>job</th>
                        <th>jobname</th>
                        <th>unit</th>
                        <th>uniqueid</th>
                        <th>posisistock</th>
                        <th>nofaktur</th>
                        <th>biaya</th>
                        <th>biaya2</th>
                        <th>disc1</th>
                        <th>disc2</th>
                        <th>pcl</th>
                        <th>pclname</th>
                        <th>rem</th>
                        <th>npwp</th>
                        <th>nobatch</th>
                        <th>expired_date</th>
                        <th>kadar</th>
                        <th>date_lulus</th>
                        <th>penanggungjw</th>
                        <th>nomor</th>
                        <th>nomor_po</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=0; foreach($list_detail as $lu): $no++;?>
                        <tr>
                            <td><div class='text-wrap width-200'><?php echo $lu->tbl;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo date("d-m-Y",strtotime($lu->date_));?></div></td>
                            <td><div class='text-wrap width-200'><?php echo date("d-m-Y",strtotime($lu->duedate));?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->cur;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->kurs;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->sub;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->subname;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->slsm;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->slsmname;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->brg;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->brgname;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->qty;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->qtybonus;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->valbonus;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->valwithdiscval;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->discval;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->ppnval;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->job;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->jobname;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->unit;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->uniqueid;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->posisistock;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->nofaktur;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->biaya;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->biaya2;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->disc1;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->disc2;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->pcl;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->pclname;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->rem;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->npwp;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->nobatch;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->expired_date;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->kadar;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo date("d-m-Y",strtotime($lu->date_lulus));?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->penanggungjw;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->nomor;?></div></td>
                            <td><div class='text-wrap width-200'><?php echo $lu->nomor_po;?></div></td>

                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</div>




