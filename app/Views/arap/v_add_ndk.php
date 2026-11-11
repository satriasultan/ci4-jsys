<link rel="stylesheet" href="<?= base_url('assets/css/corp-form.css') ?>">
<style>
    /* =========================================================
       SUPLEMEN CORPORATE - INPUT DEBIT/KREDIT (SALDO AWAL)
       Melengkapi corp-form.css + merapikan kolom form.
    ========================================================= */
    .corp-form-container .card-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #152a45 100%) !important;
        border-bottom: none !important;
    }

    .corp-form-container .card-header .card-title {
        text-transform: uppercase;
    }

    /* Input angka rata kanan (dipakai .jtsseparator / .ratakanan) */
    .corp-form-container .form-control.jtsseparator,
    .corp-form-container .form-control.ratakanan {
        text-align: right;
    }

    /* No. Bukti: prefix / infix / suffix sejajar & proporsional */
    .corp-form-container #prefix {
        min-width: 80px;
        flex: 0 0 80px;
    }

    .corp-form-container #infix {
        min-width: 70px;
        flex: 0 0 70px;
        background-color: var(--corp-bg-disabled);
    }

    .corp-form-container #suffix {
        min-width: 110px;
        flex: 0 0 110px;
    }

    .corp-form-container .is-invalid {
        border-color: var(--corp-danger) !important;
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

<?php echo $message;?>
<div class="corp-form-container">
    <div class="row">
        <!-- left column -->
        <form action="<?= base_url('arap/transaksi/finalEntryNDK') ?>" method="post" id="formPO">
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-file-text-o"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Saldo Awal Hutang / Piutang</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Saldo Awal Hutang Piutang
                            </div>

                            <!-- ROW 1 : Informasi dokumen -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cabang">Cabang / Job</label>
                                        <select name="cabang" id="cabang" class="form-control" required></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="docdate">Tanggal</label>
                                        <input type="text" name="docdate" id="docdate" required class="form-control" placeholder="Tanggal">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dk">Debit/Kredit</label>
                                        <select name="dk" id="dk" class="form-control" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="D">D</option>
                                            <option value="K">K</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="jthtempo">Jatuh Tempo</label>
                                        <div class="input-group">
                                            <input type="text" name="jthtempo" id="jthtempo" class="form-control ratakanan jtsseparator" placeholder="0" maxlength="2" inputmode="numeric" pattern="[0-9]{1,2}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ROW 2 : No. Bukti & mata uang -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. Bukti</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" name="prefix" id="prefix" class="form-control me-1" maxlength="3" style="text-transform: uppercase;" pattern="[A-Za-z0-9]{1,3}" title="Prefix hanya boleh huruf dan angka, maksimal 3 karakter">
                                            <span class="px-2 align-self-center">/</span>
                                            <input type="text" name="infix" id="infix" class="form-control mx-1" readonly>
                                            <span class="px-2 align-self-center">/</span>
                                            <input type="text" name="suffix" id="suffix" class="form-control ms-1" maxlength="6" pattern="^[A-Za-z0-9]{0,2}[0-9]{4}$" title="Maksimal 6 karakter. 4 karakter terakhir wajib angka." style="text-transform: uppercase;">
                                        </div>
                                        <input type="hidden" name="docno" id="docno" value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="currcode">Mata Uang</label>
                                        <select name="currcode" id="currcode" class="form-control select2" required></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="kurs">Nilai Tukar</label>
                                        <input type="text" name="kurs" id="kurs" class="form-control ratakanan jtsseparator" placeholder="Nilai tukar akan muncul disini">
                                    </div>
                                </div>
                            </div>

                            <!-- ROW 3 : Pihak / kontak -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="kdsalesman">Salesman</label>
                                        <select name="kdsalesman" id="kdsalesman" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="kdsupplier">Kepada</label>
                                        <select name="kdsupplier" id="kdsupplier" class="form-control select2" required></select>
                                        <input type="hidden" name="nmsupplier" id="nmsupplier">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="alamatsupplier">Alamat</label>
                                        <textarea name="alamatsupplier" id="alamatsupplier" class="form-control" rows="2" placeholder="Alamat" style="text-transform:uppercase;" readonly></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- ROW 4 : Perkiraan & pajak -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="perkiraanarap">Perkiraan</label>
                                        <select name="perkiraanarap" id="perkiraanarap" class="form-control select2" style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="perkiraanlawan">Perkiraan Lawan</label>
                                        <select name="perkiraanlawan" id="perkiraanlawan" class="form-control select2" style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="idtax">Pajak</label>
                                        <select name="idtax" id="idtax" class="form-control select2" required></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Inclusive</label>
                                        <div class="form-check corporate-check">
                                            <input class="form-check-input" type="checkbox" name="isinclusive" id="isinclusive" <?= isset($data['isinclusive']) && $data['isinclusive'] === 'YES' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="isinclusive">Inclusive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ROW 5 : Nilai & keterangan -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dpp">Nilai</label>
                                        <input name="dpp" id="dpp" type="text" class="form-control jtsseparator ratakanan" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="total">Total</label>
                                        <input name="total" id="total" type="text" maxlength="30" placeholder="0.00" readonly class="form-control jtsseparator ratakanan">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Keterangan" style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- AKSI -->
                <div class="card mt-3">
                    <div class="card-footer">
                        <a href="<?= base_url('arap/transaksi/ndk') ?>" class="btn btn-default btn-lg">
                            <i class="fa fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <?php if ($typeform != 'DETAIL'): ?>
                            <button type="submit" onclick="return confirm('Finish Entry?')" class="btn btn-success btn-lg float-right">
                                <i class="fa fa-save"></i> Simpan Final Data
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
        <!--/.col (left) -->
    </div>
</div><!-- /.corp-form-container -->


<script type="application/javascript" src="<?= base_url('assets/pagejs/arap/ndk.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        var dtldata = <?= json_encode($dtldata ?? null) ?>;

        if (dtldata && dtldata.docno && dtldata.docno.trim() !== '') {
            $('#docno').val(dtldata.docno.trim()).prop('readonly', true);
        }


        $('#docdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            const dateStr = picker.startDate.format('DD-MM-YYYY');
            $(this).val(dateStr);

            // Update estpakai berdasarkan docdate baru
            const prefix = $('#prefix').val() || 'JI';
            setupEstpakai(prefix);
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        $('#hpdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#hpdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'hpdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'hpdate');
        });
        $('#hpdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });



        $('#senddate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#senddate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'senddate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'senddate');
        });
        $('#senddate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });


        
        $('#periodemulai').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodemulai').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodemulai').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        
        $('#periodeakhir').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#periodeakhir').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#periodeakhir').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        

    });

</script>