<style>
    
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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

<div class="row">
	<div class="col-sm-12">
		<div class="card">
            <div class="card-header">
            
            </div><!-- /.card-header -->
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-12">Tanggal Dokumen</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control input-sm tglrange" id="tglrange" name="tglrange" value="" data-date-format="dd-mm-yyyy" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
                        <button type="button" id="btn-filter" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
            <div id="wrapperTable" style="display:none;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="text-align:right; margin-bottom:10px;">
                                <button id="btn-print" class="btn btn-primary">
                                    Cetak Laporan
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive"  style='overflow-x:auto;'>
                                <table id="tablelapndkTrx" class="table table-bordered table-striped"  style="width:100%;" cellspacing="0">
                                    <thead class="text-center">
                                        <tr>
                                            <!-- <th style="min-width:10px; text-align:center; vertical-align:middle;">No.</th>
                                            <th style="min-width:10px; text-align:center; vertical-align:middle;">Action</th> -->
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">No. Jurnal</th>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Tanggal</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Kode</th>
                                            <th style="min-width:200px; text-align:center; vertical-align:middle;">Nama</th>
                                            <th style="min-width:250px; text-align:center; vertical-align:middle;">Keterangan</th>
                                            <!-- <th style="min-width:50px; text-align:center; vertical-align:middle;">Kota</th> -->
                                            <!-- <th style="min-width:150px; text-align:center; vertical-align:middle;">Jurnal</th> -->
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Debit</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Kredit</th>
                                            <!-- <th style="min-width:100px; text-align:center; vertical-align:middle;">Tanggal Kirim</th> -->
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Salesman</th>
                                            <!-- <th style="min-width:100px; text-align:center; vertical-align:middle;">DK</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Nilai</th>
                                            <th style="min-width:400px; text-align:center; vertical-align:middle;">Remark</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Cost Center</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" style="text-align:right;padding-right:50px;">GRAND TOTAL</th>
                                            <th id="total_debit"></th>
                                            <th id="total_kredit"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div>
		</div><!-- /.card -->
	</div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/arap/lapndk.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
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