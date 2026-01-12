<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> LPB (Laporan Penerimaan Barang)</h1>
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
<?php echo $showUnfinish; ?>
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
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <a href="<?= base_url('stock/bbm/input_bbm') ?>" class="btn btn-primary" style="margin:10px"><i class="fa fa-plus"></i>  Input LPB  </a>

                <a href="#" class="btn btn-default" style="margin:10px"  data-bs-toggle="modal" data-bs-target="#filter" ><i class="fa fa-filter"></i>  Filter  </a>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="tablebbmTrx" class="table table-bordered table-striped" >
                    <thead>
                    <tr>
                        <th width="1%">No.</th>
                        <th width="2%">Action</th>
                        <th>Document</th>
                        <th>Reference(PO)</th>
                        <th>Id Barang</th>
                        <th>Nama Barang</th>
                        <th>Spesifikasi</th>
                        <th>Kategori</th>
                        <th>QtyTerima</th>
                        <th>QtyKotor</th>
                        <th>Unit</th>
                        <th>TglTerima</th>
                        <th>Keterangan</th>
                        <th>Gudang</th>
                        <th>Area Drop</th>
                        <th>Jenis</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>InputBy</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggalpo">Tanggal Kedatangan</label>
                        <input type="text" class="form-control tglrange" id="tglrange"  name="tglrange" data-date-format="dd-mm-yyyy" required placeholder="Entry LPB Date" required>
                    </div>
                    <div class="form-group">
                        <label for="itembarang">Item Barang</label>
                        <select name="idbarang_filter" id="idbarang_filter" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suppliername">Nama Supplier</label>
                        <select name="namasupplier" id="namasupplier" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_filter">Status</label>
                        <select name="status_filter" id="status_filter" class="form-control" placeholder="Pilih Status Filter">
                            <option value=""> Semua Status</option>
                            <option value="A"> Outstanding</option>
                            <option value="S"> Outstanding Sebagian</option>
                            <option value="P"> Diterima Penuh</option>
                        </select>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-reset-tx" class="btn btn-default">Reset</button>
                    <button type="button" id="btn-filter-tx" class="btn btn-primary">Filter</button>

                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->






<script type="application/javascript" src="<?= base_url('assets/pagejs/stock/bbm.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-M-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>
<script type="application/javascript">
    $(".tglrange").daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $("#status_filter").select2();

    $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    });

    $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>