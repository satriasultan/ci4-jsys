<!-- =========================================================
     v_add_penerimaankb.php - Corporate Style
     Referensi tampilan : persediaan/pnm_brng/v_add_pnm_brg.php
     Catatan : perubahan hanya pada font, warna, spacing, dan
               kerapian layout. Nama field, id, dan fungsi
               JavaScript tidak diubah.
     ========================================================= -->
<link rel="stylesheet" href="<?= base_url('assets/css/corp-form.css') ?>">
<style>
    /* Suplemen khusus Penerimaan Kas/Bank: tabel detail & separator angka
       mengikuti tema corporate yang sama. */
    .corp-form-container #tabpenerimaankbdtl thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border-color: var(--corp-border) !important;
        padding: 10px 12px !important;
        vertical-align: middle;
    }

    .corp-form-container #tabpenerimaankbdtl tbody td {
        padding: 9px 12px !important;
        border-color: var(--corp-border) !important;
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #tabpenerimaankbdtl tbody tr:hover {
        background-color: var(--corp-primary-light) !important;
    }

    .corp-form-container .allocation-empty {
        color: var(--corp-text-muted);
        font-weight: 600;
        text-align: center;
    }

    .corp-form-container .form-control.jtsseparator,
    .corp-form-container .form-control.ratakanan {
        text-align: right;
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
<div class="corp-form-container">
<div class="row">
    <!-- left column -->
    <form action="<?= base_url('ka/finance/finalEntryPenerimaanKB') ?>" method="post" id="formPO">
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-file-text-o"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Penerimaan Kas/Bank</h3>
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
                                        <div class="col-md-12">
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
                                                        name="suffix"
                                                        id="suffix"
                                                        class="form-control ms-1"
                                                        maxlength="6">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="docno" class="form-control col-sm-12" id="docno" maxlength="20"     value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>" style="text-transform:uppercase;" readonly>
                                            <input type="hidden" name="idurut_header" id="idurut_header" value="<?= isset($dtldata['idurut']) ? esc(trim((string) $dtldata['idurut'])) : '' ?>">
                                            
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
                                                <label for="pp">Referensi</label>
                                                <input type="text"
                                                name="pp"
                                                id="pp"
                                                class="form-control"
                                                placeholder="Referensi" style="text-transform:uppercase;" >
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
                                <!-- CUSTOMER / PERK. KAS-BANK / NILAI -->
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="kdcustomer">Customer</label>
                                                <select name="kdcustomer" id="kdcustomer" class="form-control select2" required></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="prkkas">Perk. Kas/Bank</label>
                                                <select name="prkkas" id="prkkas" class="form-control" required>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="total">Nilai</label>
                                                <input type="text"
                                                        name="total"
                                                        id="total"
                                                        class="form-control jtsseparator ratakanan">
                                                <!-- nilai diterima juga dikirim sebagai dpp -->
                                                <input type="hidden" name="dpp" id="dpp">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
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
                                        <div class="col-md-12">
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
                                </div>

                            </div>

                        </div>
                </div>
            </div>

            <div class="card mt-3 card-primary">
                <div class="card-header d-flex align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0 me-3">
                        Detail Perkiraan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <!-- TARIK PIUTANG CUSTOMER -->
                            <div class="d-flex justify-content-between align-items-center mb-2 w-100">

                                <!-- KIRI : TARIK PIUTANG -->
                                <button type="button"
                                        class="btn btn-primary"
                                        data-bs-toggle="tooltip"
                                        title="Tarik sisa piutang customer dari dokumen penjualan (status F)"
                                        onclick="btnTarikPenerimaanKB()">
                                    <i class="fas fa-search"></i> Tarik Piutang Customer
                                </button>

                                <!-- KANAN : ACTION DETAIL -->
                                <div class="d-flex align-items-center gap-1 detail-action-toolbar">

                                    <!-- TAMBAH -->
                                    <button type="button"
                                            class="btn btn-success btn-sm btn-detail-action"
                                            id="btnAddDetail"
                                            data-bs-toggle="tooltip"
                                            title="Tambah Detail"
                                            aria-label="Tambah Detail"
                                            onclick="btnInputDetail()">
                                        <i class="fa fa-plus"></i>
                                    </button>

                                    <!-- EDIT -->
                                    <button type="button"
                                            class="btn btn-warning btn-sm btn-detail-action"
                                            id="btnEditDetail"
                                            data-bs-toggle="tooltip"
                                            title="Edit Detail"
                                            aria-label="Edit Detail"
                                            onclick="editSelectedPenerimaanKBDetail()"
                                            disabled>
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <!-- DELETE -->
                                    <button type="button"
                                            class="btn btn-danger btn-sm btn-detail-action"
                                            id="btnDeleteDetail"
                                            data-bs-toggle="tooltip"
                                            title="Hapus Detail"
                                            aria-label="Hapus Detail"
                                            onclick="deleteSelectedPenerimaanKBDetail()"
                                            disabled>
                                        <i class="fa fa-trash"></i>
                                    </button>

                                </div>

                            </div>
                        </div>
                        <div class="allocation-box">
                            <div class="table-responsive">
                                <table id="tabpenerimaankbdtl"
                                       class="table table-bordered table-hover table-sm mb-0"
                                       width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="checkAll">
                                            </th>
                                            <th>No. Bukti</th>
                                            <th>Perkiraan</th>
                                            <th>Keterangan</th>
                                            <th class="text-center">DK</th>
                                            <th>Cost/Profit Center</th>
                                            <th class="text-end">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyPenerimaanKBDetail">
                                        <tr>
                                            <td colspan="7" class="allocation-empty">
                                                <i class="fa fa-box-open text-muted"></i>
                                                Belum ada piutang yang dialokasikan.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- TOTAL ALOKASI NILAI -->
                            <div class="d-flex justify-content-end align-items-center border-top bg-light px-1 py-2">
                                <label for="totalalokasi"
                                       class="form-label mb-0 me-2 fw-semibold">
                                    Total Alokasi
                                </label>

                                <input type="text"
                                       name="totalalokasi"
                                       id="totalalokasi"
                                       class="form-control form-control-sm text-end"
                                       style="max-width: 220px; font-size: 17px; font-weight: 700;"
                                       value="0"
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <!-- BALANCE disembunyikan:
                         finalisasi hanya butuh #total = #totalalokasi -->
                    <input type="hidden" name="balance" id="balance">
                    <input type="hidden" id="balance_awal">
                    <input type="hidden" id="total_awal">
                </div>

                <!-- FOOTER -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('ka/finance/penerimaankb') ?>" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Kembali
                        </a>

                        <?php if ($typeform != 'DETAIL'): ?>
                            <button type="submit"
                                    id="btnFinalEntryPenerimaanKB"
                                    class="btn btn-success"
                                    onclick="return confirm('Finish Entry?')">
                                <i class="fa fa-save me-1"></i>
                                Simpan Final Entry Kas Bank
                            </button>
                        <?php endif; ?>
                    </div>
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




<!-- =========================================================
     MODAL ADD / EDIT DETAIL PENERIMAAN KAS/BANK
========================================================= -->
<div class="modal fade" id="modalDetailPenerimaanKB" tabindex="-1" aria-labelledby="modalDetailPenerimaanKBLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <!-- HEADER MODAL -->
            <div class="modal-header bg-primary text-white">

                <div>
                    <h5 class="modal-title mb-0" id="modalDetailPenerimaanKBLabel">
                        Detail Penerimaan Kas/Bank
                    </h5>

                    <small class="opacity-75">
                        Input detail transaksi
                    </small>
                </div>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <!-- FORM DETAIL -->
            <form id="formPenerimaanKBDetail" autocomplete="off">
                <div class="modal-body">

                    <!-- HIDDEN DETAIL -->
                    <input type="hidden" name="idurut" id="idurut">
                    <input type="hidden" name="uniqueid" id="uniqueid">
                    <input type="hidden" name="docno" id="docno_detail">
                    <input type="hidden" name="nilai_ref" id="nilai_ref">
                    <input type="hidden" name="jurnalref" id="jurnalref" value="YES">
                    <input type="hidden" name="nmcoa" id="nmcoa">

                    <!-- HEADER REFERENCE -->
                    <div class="row g-3 mb-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Customer</label>
                            <input type="text" id="detail_customer" class="form-control bg-light" readonly placeholder="Customer">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Jurnal</label>
                            <input type="text" id="detail_docno_display" class="form-control bg-light" readonly placeholder="No. Jurnal">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ID Header</label>
                            <input type="text" id="detail_idurut_display" class="form-control bg-light" readonly placeholder="ID Header">
                        </div>

                    </div>

                    <hr class="my-3">

                    <!-- ROW 1 -->
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label for="nobukti" class="form-label fw-semibold">No. Bukti</label>
                            <input type="text"
                                   name="nobukti"
                                   id="nobukti"
                                   class="form-control"
                                   maxlength="50"
                                   placeholder="Nomor Bukti"
                                   style="text-transform:uppercase;">
                        </div>

                        <div class="col-md-8">
                            <label for="idcoa" class="form-label fw-semibold">Perkiraan</label>
                            <select name="idcoa"
                                    id="idcoa"
                                    class="form-select"
                                    style="width:100%;">
                                <option value="">-- Pilih Perkiraan --</option>
                            </select>
                        </div>

                    </div>

                    <!-- ROW 2 -->
                    <div class="row g-3 mt-1">

                        <div class="col-md-2">
                            <label for="dk" class="form-label fw-semibold">DK</label>
                            <select name="dk" id="dk" class="form-select">
                                <option value="D">D</option>
                                <option value="K">K</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="cabangdtl" class="form-label fw-semibold">Cost / Profit Center</label>
                            <input type="text"
                                   name="cabangdtl"
                                   id="cabangdtl"
                                   class="form-control"
                                   placeholder="Cost / Profit Center"
                                   style="text-transform:uppercase;">
                        </div>

                        <div class="col-md-6">
                            <label for="remarks" class="form-label fw-semibold">Keterangan</label>
                            <textarea name="remarks"
                                      id="remarks"
                                      class="form-control"
                                      rows="2"
                                      maxlength="500"
                                      placeholder="Keterangan detail..."
                                      style="text-transform:uppercase;"></textarea>
                        </div>

                    </div>

                    <!-- ROW 3 -->
                    <div class="row g-3 mt-1">

                        <div class="col-md-5 ms-auto">
                            <label for="nilai" class="form-label fw-semibold">Nilai</label>
                            <div class="input-group">
                                <span class="input-group-text currency-symbol" id="detail_currency_symbol">IDR</span>
                                <input type="text"
                                       name="nilai"
                                       id="nilai"
                                       class="form-control text-end fw-bold jtsseparator ratakanan"
                                       value="0"
                                       placeholder="0"
                                       inputmode="decimal">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light">

                    <button type="button"
                            class="btn btn-primary"
                            id="btnSavePenerimaanKBDetail"
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



</div><!-- /.corp-form-container -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/penerimaankb.js') ?>?v=20261111b"></script>
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

        // Tampilkan tanggal dokumen dalam format dd-mm-yyyy
        if (dtldata && dtldata.docdate && String(dtldata.docdate).trim() !== '') {
            $('#docdate').val(penerimaanKBDateDisplay(dtldata.docdate));
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
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
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
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear'
        });

        // handler apply/cancel
        $('#senddate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
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