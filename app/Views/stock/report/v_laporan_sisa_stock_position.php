<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1>
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

<?php echo $message;?>

<div class="row">
    <!-- left column -->
    <div class="col-md-6">
        <!-- jquery validation -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title"> Laporan Analisa Sisa Stok Per Area Position </h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('stock/report/show_sisastockgdg_position')?>" method="post" id="formInputTransfers" enctype="multipart/form-data" role="form">
                <div class="card-body row">
                    <div class="form-group col-sm-12 ">
                        <label for="idlocation">Search Your Location (Gudang)</label>
                        <div class="input-group">
                            <select name="idlocation" id="idlocation" class="form-control" required >
                            </select>
                        </div>
                    </div>
                    <div class="row col-sm-12">
                        <div class="form-group col-sm-12 ">
                            <label for="docno">Date (Tanggal) </label>
                            <input type="text" name="docdate" class="form-control" id="docdate" placeholder="Pilih Tanggal" required>
                        </div>
                    </div>
                    <div class="row col-sm-12">
                        <label for="idlocation" >Search Area Position</label>
                        <div class="form-group col-sm-12 ">
                            <select name="idarea" id="idarea" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 ">
                        <label for="idbarang">Search Your Category</label>
                        <div class="input-group">
                            <select name="idgroup" id="idgroup" class="form-control" >
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 ">
                        <label for="idbarang">Search Your Items (Barang)</label>
                        <div class="input-group">
                            <select name="idbarang" id="idbarang" class="form-control" >
                            </select>
                        </div>
                    </div>
                    <div class="row col-sm-12">
                        <div class="form-group col-sm-12 ">
                            <button type="submit"  class="btn btn-primary float-right"><i class="fa fa-repeat"></i> Proses</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <!-- /.card -->
    </div>
</div>


<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/stock_report.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: false,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        });

        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>