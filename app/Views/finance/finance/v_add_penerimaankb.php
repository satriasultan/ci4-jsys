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
<?php
    $isIT = isset($userinfo['rolename']) && trim($userinfo['rolename']) === 'IT';
    $disabled = $isIT ? '' : 'disabled';
?>
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('ka/finance/finalEntryPenerimaanKB') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Penerimaan Kas/Bank</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Penerimaan Kas/Bank
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
                                                <label>No. Bukti</label>

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
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="docdate">Tanggal</label>
                                                <input type="text"
                                                name="docdate"
                                                id="docdate"
                                                class="form-control"
                                                placeholder="Tanggal">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pp">PP</label>
                                                <input type="text"
                                                name="pp"
                                                id="pp"
                                                class="form-control"
                                                placeholder="PP">
                                            </div>
                                        </div>
                                    </div>
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
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdcustomer">Customer</label>
                                                <select name="kdcustomer" id="kdcustomer" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="alamatcustomer">Alamat Customer</label>
                                                <textarea name="alamatcustomer"
                                                    id="alamatcustomer"
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="Alamat Customer"
                                                    
                                                    style="text-transform:uppercase;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-md-12" >
                                                <div class="form-group">
                                                    <label for="keterangan">Keterangan</label>
                                                    <textarea name="keterangan"
                                                        id="keterangan"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Keterangan"
                                                        style="text-transform:uppercase;" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: -25px;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="prkkas">Perk. Kas/Bank</label>
                                                    <select name="prkkas" id="prkkas" class="form-control">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="dpp">Nilai</label>
                                                    <input type="text"
                                                            name="dpp"
                                                            id="dpp"
                                                            class="form-control jtsseparator ratakanan">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header clearfix">
                    <h3 class="card-title">
                        Detail Perkiraan
                    </h3>

                    <div class="float-right d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-success btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Input Data"
                                onclick="btnInputDetail()">
                            <i class="fa fa-plus"></i>
                        </button>

                        <!-- <button type="button"
                                class="btn btn-warning btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Update Data"
                                onclick="btnUpdateDetail()">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button type="button"
                                class="btn btn-danger btn-lg action-btn"
                                data-bs-toggle="tooltip"
                                title="Hapus Data"
                                onclick="btnDeleteDetail()">
                            <i class="fa fa-trash"></i>
                        </button> -->


                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table id="tabpenerimaankbdtl" class="table table-bordered table-striped" style="width:100%;" cellspacing="0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th width="100">Action</th>
                                            <th>No. Bukti</th>
                                            <th>Kode Perk.</th>
                                            <th>Nama Perkiraan</th>
                                            <th>Keterangan</th>
                                            <th>DK</th>
                                            <th>Status</th>
                                            <th>Cost/Profit Center</th>
                                            <th>Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="9" style="background-color: #ffe483;" class="text-right">
                                                TOTAL NILAI
                                            </th>
                                            <th id="total" class="text-right" style="font-weight: bold;">
                                                0
                                            </th>
                                        </tr>
                                </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-6">
                            
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="col-md-4" style="float: right;">
                                        <div class="form-group mb-2">
                                            <div class="row">
                                                <div class="col-md-4 text-end">
                                                    <label for="balance" class="mt-2">Balance</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text"
                                                        name="balance"
                                                        id="balance"
                                                        class="form-control jtsseparator ratakanan"
                                                        readonly>
                                                        <input type="hidden" id="balance_awal">
                                                        <input type="hidden" id="total_awal">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                </div>
                <div class="card-footer bg-light">
                    <a href="<?= base_url('ka/finance/penerimaankb') ?>"
                        class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <?php if ($typeform != 'DETAIL' && $dtldata != null): ?>
                        <button type="submit"
                                onclick="return confirm('Finish Entry?')"
                                class="btn btn-success btn-lg float-right">
                            <i class="fa fa-save"></i> Simpan Final Data
                        </button>
                    <?php endif; ?>
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




<div class="modal fade" id="modalDetailPenerimaanKB" tabindex="-1" role="dialog" aria-labelledby="modalDetailPenerimaanKBLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalDetailPenerimaanKBLabel">
                    </i> Input Perkiraan Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM -->
            <form id="formPenerimaanKBDetail">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="docno" id="docno">
                    <!-- <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO"> -->

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>No. Bukti</label>
                                <input type="text"
                                        name="nobukti"
                                        id="nobukti"
                                        class="form-control"
                                        placeholder="No. Bukti"
                                        style="text-transform:uppercase" readonly>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>ID Perkiraan</label>
                                <select name="idcoa" id="idcoa"
                                        class="form-control select2"
                                        style="width:100%"></select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Perkiraan</label>
                                <input type="text"
                                        name="nmcoa"
                                        id="nmcoa"
                                        class="form-control"
                                        placeholder="Nama Perkiraan"
                                        style="text-transform:uppercase" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dk">Debit/Kredit</label>
                                <select name="dk" id="dk" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="DEBIT">Debit</option>
                                    <option value="KREDIT">Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cost Center</label>
                                <input name="cabangdtl" id="cabangdtl"
                                        class="form-control select2"
                                        style="width:100%">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea type="text"
                                        name="remarks"
                                        rows="4"
                                        style="text-transform: uppercase;"
                                        id="remarks"
                                        class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nilai</label>
                                <input type="text"
                                        name="nilai"
                                        id="nilai"
                                        class="form-control jtsseparator ratakanan"
                                        placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">
                    
                    <button type="button"
                        class="btn btn-primary"
                        onclick="savePenerimaanKBDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="$('#modalDetailPenerimaanKB').modal('hide')">
                        <i class="fa fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/penerimaankb.js') ?>"></script>
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

        document.addEventListener('DOMContentLoaded', function() {

            document.addEventListener('keydown', function(e) {

                const activeTag = document.activeElement.tagName;

                if (
                    e.ctrlKey &&
                    e.shiftKey &&
                    e.code === 'KeyE' &&
                    !['INPUT','TEXTAREA','SELECT'].includes(activeTag)
                ) {
                    e.preventDefault();
                    console.log('Shortcut triggered');
                    btnUpdateDetail();
                }

            });

        });

    });

</script>