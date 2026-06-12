<style>
    
    .section-block {
        background-color: #e8e8e8;
        border-left: 4px solid #007bff;
        /* border-bottom: 4px solid #007bff;  */
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                    box-shadow 0.4s ease, 
                    border-color 0.4s ease;
        
        transform: scale(1);
        will-change: transform;
    }

    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 20px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 8px;
    }

    .section-block:hover {
        /* transform: scale(1.02); */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-left-color: #0056b3;
        border-bottom-color: #0056b3;
    }

    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }

    .form-control:disabled, .form-control[readonly] {
        background-color: #dfdfdf;
        opacity: 1;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(
            to right,
            #007bff 0%,
            #cfe2ff 30%,
            #e8e8e8 100%
        );
        margin: 24px 0;
        border: none;
    }


</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($title)));?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumt float-sm-right">
                    <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Versi: <?php echo $version; ?></div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumt-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                        <?php } else { ?>
                            <li class="breadcrumt-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<?php echo $message;?>
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('ka/finance/finalEntryUMT') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Uang Muka Titipan</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Uang Muka Titipan
                            </div>
                            <div class="row">

                                <!-- LEFT COLUMN -->
                                <div class="col-md-3">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="cabang">Cabang / Job</label>
                                                <select name="cabang" id="cabang" class="form-control" required></select>
                                            </div>
                                        </div>

                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>No. Jurnal Uang Muka Titipan</label>

                                                <div class="d-flex">
                                                    <input type="text"
                                                        name="prefix"
                                                        id="prefix"
                                                        class="form-control me-1"
                                                        maxlength="3"
                                                        style="text-transform: uppercase;"
                                                        pattern="[A-Z0-9]+">

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="infix"
                                                        id="infix"
                                                        class="form-control mx-1"
                                                        readonly>

                                                    <span class="px-2 align-self-center">/</span>

                                                    <input type="text"
                                                        name="sufix"
                                                        id="sufix"
                                                        class="form-control ms-1"
                                                        readonly>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20"     value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform: uppercase;" readonly>
                                            
                                    </div>
                                        
                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="docdate">Tanggal</label>
                                                <input type="text"
                                                name="docdate"
                                                id="docdate"
                                                class="form-control"
                                                placeholder="Tanggal">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="jthtempo">Jatuh Tempo</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        name="jthtempo"
                                                        id="jthtempo"
                                                        class="form-control ratakanan jtsseparator"
                                                        placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">hari</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="dk">Debit/Kredit</label>
                                                <select name="dk" id="dk" class="form-control inform" style="text-transform:uppercase;" >
                                                    <option value="" disabled>-- Pilih --</option>
                                                    <option value="DEBIT">DEBIT </option>
                                                    <option value="KREDIT"> KREDIT </option>
    
                                                </select>
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdsupplier">Supplier</label>
                                                <select name="kdsupplier" id="kdsupplier" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatsupplier">Alamat Supplier</label>
                                                <textarea name="alamatsupplier"
                                                    id="alamatsupplier"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Supplier"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: -25px;">
                                            <div class="form-group">
                                                <label for="prkarap">Prk. UM AR/AP</label>
                                                <select name="prkarap" id="prkarap" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: -25px;">
                                            <div class="form-group">
                                                <label for="prkkas">Prk. Kas/Bank</label>
                                                <select name="prkkas" id="prkkas" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="row align-items-end">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="currcode" class="form-label">Mata Uang</label>
                                                    <select name="currcode"
                                                            id="currcode"
                                                            class="form-select select2"
                                                            required>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="kurs" class="form-label">Nilai Tukar</label>
                                                    <input type="text"
                                                        name="kurs"
                                                        id="kurs"
                                                        class="form-control ratakanan jtsseparator"
                                                        placeholder="Nilai tukar akan muncul disini"
                                                        >
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatkirim">Alamat Kirim</label>
                                                <textarea name="alamatkirim"
                                                    id="alamatkirim"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Kirim"
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div> -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="idtax">Pajak</label>
                                                <select name="idtax" id="idtax" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="margin-top: 30px;">
                                            <div class="form-group">
                                                <!-- Radio untuk Interngroup -->
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        name="isinclusive" 
                                                        id="isinclusive"
                                                        <?= isset($data['isinclusive']) && $data['isinclusive'] === 'YES' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="isinclusive">
                                                        Inclusive
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        

                                    </div>
                                </div>

                            </div>
                            <hr>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan"
                                            id="keterangan"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Keterangan"
                                            style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="col-md-4" style="float: right;">
                                                <div class="form-group mb-2">
                                                    <div class="row">
                                                        <div class="col-md-6 text-end">
                                                            <label for="dpp" class="mt-2">DPP</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text"
                                                                name="dpp"
                                                                id="dpp"
                                                                class="form-control jtsseparator ratakanan"
                                                                >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 ">
                                            <div class="col-md-4" style="float: right;">
                                                <div class="form-group mb-2">
                                                    <div class="row">
                                                        <div class="col-md-6 text-end">
                                                            <label for="jumlahpajak" class="mt-2">Jumlah Pajak</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text"
                                                                name="jumlahpajak"
                                                                id="jumlahpajak"
                                                                class="form-control jtsseparator ratakanan"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 ">
                                            <div class="col-md-4" style="float: right;">
                                                <div class="form-group mb-3">
                                                    <div class="row">
                                                        <div class="col-md-6 text-end">
                                                            <label for="total" class="mt-2">Total</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text"
                                                                name="total"
                                                                id="total"
                                                                style="font-weight: bold;"
                                                                class="form-control jtsseparator ratakanan"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- <div class="col-md-12">
                                            <div class="form-group text-end">
                                                <button type="button" class="btn btn-lg btn-primary text-white" title="Down Payment">
                                                    <i class="fa fa-money"></i> 
                                                </button>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= base_url('ka/finance/umt') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    
                        <button type="submit"
                                onclick="return confirm('Finish Entry?')"
                                class="btn btn-success btn-lg float-right">
                            <i class="fa fa-save"></i> Simpan Final Data
                        </button>
                    
                </div>
            </div>
        </div>
    </form>
        <!-- /.card -->
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>






<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/umt.js') ?>"></script>
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
            locale: { format: 'YYYY-MM-DD' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            // jika butuh validasi bootstrapValidator:
            // $('#formInputTransfers').bootstrapValidator('updateStatus', 'docdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
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