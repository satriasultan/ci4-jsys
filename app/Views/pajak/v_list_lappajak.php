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
                    <div class="col-md-12" style="margin-bottom: 25px;">
                        <div class="form-group input-sm ">
                            <label for="tujuan" class="col-sm-12">Tipe Transaksi</label>
                            <div class="col-sm-3">
                                <select class='form-control input-sm' name="typefilter" id="typefilter">		
                                    <option value="PEMBELIAN">Pembelian</option>
                                    <option value="PENJUALAN">Penjualan</option>										
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 25px;">
                        <div class="form-group input-sm ">
                            <label class="label-form col-sm-12">Tanggal Dokumen</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-sm tglrange" id="tglrange" name="tglrange" value="" data-date-format="dd-mm-yyyy" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="btn-reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" id="btn-filter" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
            <div id="wrapperTable" style="display:none;">
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="table-responsive"  style='overflow-x:auto;'>
                                <table id="tablelappajakTrx" class="table table-bordered table-striped"  style="width:100%;" cellspacing="0">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">No. Jurnal</th>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Tanggal</th>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Tanggal Jatuh Tempo</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Kode Perusahaan</th>
                                            <th style="min-width:200px; text-align:center; vertical-align:middle;">Nama Perusahaan</th>
                                            <th style="min-width:200px; text-align:center; vertical-align:middle;">Alamat Perusahaan</th>
                                            <th style="min-width:70px; text-align:center; vertical-align:middle;">ID Barang</th>
                                            <th style="min-width:150px; text-align:center; vertical-align:middle;">Nama Barang</th>
                                            <th style="min-width:70px; text-align:center; vertical-align:middle;">Quantity</th>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Unit</th>
                                            
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Currency</th>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Kurs</th>
                                            <th style="min-width:50px; text-align:center; vertical-align:middle;">Pajak</th>

                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Harga Satuan</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Total Bruto</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Total Pajak</th>
                                            <th style="min-width:100px; text-align:center; vertical-align:middle;">Total Konversi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div><!-- /.card -->
	</div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/pajak/lappajak.js') ?>"></script>
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