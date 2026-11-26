<!-- =========================================================
     v_add_jup.php - Corporate Style
     Referensi tampilan : finance/finance/v_add_penerimaankb.php
     Catatan : perubahan hanya pada font, warna, spacing, dan
               kerapian layout. Nama field, id, dan fungsi
               JavaScript tidak diubah.
     ========================================================= -->
<?php
/* Cabang default mengikuti session user yang login (lihat jup_branch_autofill.js).
   User tetap boleh memilih cabang / job lain pada halaman addJUP. */
$sessionBranchId   = trim((string) session()->get('idbranchjob'));
$sessionBranchName = strtoupper(trim((string) session()->get('nmbranchjob')));
?>
<link rel="stylesheet" href="<?= base_url('assets/css/corp-form.css') ?>">
<style>
    /* Suplemen khusus Jurnal Umum Perkiraan (JUP): tabel detail & separator angka
       mengikuti tema corporate yang sama (referensi v_add_penerimaankb.php). */
    .corp-form-container #jupGrid thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border-color: var(--corp-border) !important;
        padding: 9px 10px !important;
        vertical-align: middle;
    }

    .corp-form-container #jupGrid tbody td {
        padding: 4px 6px !important;
        border-color: var(--corp-border) !important;
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #jupGrid tbody tr.jup-row:hover {
        background-color: var(--corp-primary-light) !important;
    }

    /* ---- Grid inline detail JUP ---- */
    .corp-form-container #jupGrid .jup-cell-input,
    .corp-form-container #jupGrid .jup-cell-select {
        font-size: 13px;
        height: 32px;
        padding: 0.25rem 0.5rem;
    }

    .corp-form-container #jupGrid .jup-col-no,
    .corp-form-container #jupGrid .jup-col-aksi {
        text-align: center;
        white-space: nowrap;
    }

    .corp-form-container #jupGrid .jup-cell-ellipsis input {
        text-overflow: ellipsis;
    }

    .corp-form-container #jupGrid .jup-cell-nilai input {
        text-align: right;
    }

    .corp-form-container #jupGrid tbody tr.jup-row-selected {
        background-color: var(--corp-primary-light) !important;
        box-shadow: inset 3px 0 0 var(--corp-primary);
    }

    .corp-form-container #jupGrid tbody tr.jup-row-invalid td {
        background-color: #fdecec !important;
    }

    .corp-form-container #jupGrid .jup-invalid {
        border-color: var(--corp-danger) !important;
    }

    .corp-form-container #jupGrid tr.jup-empty-row td {
        text-align: center;
        color: #6b7280;
        padding: 18px 12px !important;
        cursor: pointer;
        background-color: #fbfcfe;
    }

    .corp-form-container #jupGrid .jup-btn {
        border: 0;
        background: transparent;
        color: #6b7280;
        padding: 2px 6px;
        border-radius: 4px;
        line-height: 1;
    }

    .corp-form-container #jupGrid .jup-btn:hover {
        background-color: #e5e7eb;
        color: #111827;
    }

    .corp-form-container #jupGrid .jup-btn-row-remove:hover {
        color: var(--corp-danger);
    }

    .corp-form-container #jupGrid.jup-busy {
        opacity: 0.55;
        pointer-events: none;
    }

    .corp-form-container .jup-saving-overlay {
        position: absolute;
        inset: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.72);
        border-radius: 6px;
    }

    .corp-form-container .jup-saving-overlay.d-none {
        display: none !important;
    }

    .corp-form-container .jup-saving-box {
        background-color: #ffffff;
        border: 1px solid var(--corp-border);
        border-radius: 8px;
        padding: 10px 18px;
        font-weight: 600;
        color: #374151;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .corp-form-container .jup-grid-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed var(--corp-border);
    }

    .corp-form-container .jup-totals {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
    }

    .corp-form-container .jup-total-item {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        min-width: 130px;
    }

    .corp-form-container .jup-total-item span {
        font-size: 11.5px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        color: #6b7280;
    }

    .corp-form-container .jup-total-item strong {
        font-size: 14.5px;
        color: #111827;
    }

    .corp-form-container .jup-balance-state {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.4px;
        padding: 5px 12px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .corp-form-container .jup-badge-ok {
        background-color: #dcfce7;
        color: #15803d;
    }

    .corp-form-container .jup-badge-notok {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .corp-form-container .jup-ok {
        color: #15803d !important;
    }

    .corp-form-container .jup-not-ok {
        color: var(--corp-danger) !important;
    }

    .corp-form-container .jup-selection-info {
        font-size: 12px;
        color: #6b7280;
    }

    .corp-form-container .jup-grid-hint {
        display: block;
        margin-top: 10px;
        font-size: 11.5px;
        color: #6b7280;
    }

    .corp-form-container .form-control.jtsseparator,
    .corp-form-container .form-control.ratakanan {
        text-align: right;
    }

    /* No. Jurnal JUP: prefix / infix / sufix sejajar & proporsional */
    .corp-form-container #prefix {
        min-width: 80px;
        flex: 0 0 80px;
    }

    .corp-form-container #infix {
        min-width: 70px;
        flex: 0 0 70px;
        background-color: var(--corp-bg-disabled);
    }

    .corp-form-container #sufix {
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
        <form action="<?= base_url('ka/accounting/finalEntryJUP') ?>" method="post" id="formPO">
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-file-text-o"></i> <?=  $typeTitle = ($typeform == 'INPUT') ? 'Input' : ($typeform == 'UPDATE' ? 'Edit' : 'Detail'); ?> Jurnal Umum Perkiraan</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-address-card"></i> Jurnal Umum Perkiraan (JUP)
                            </div>

                            <!-- ROW 1 : Informasi dokumen -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cabang">Cabang / Job</label>
                                        <select name="cabang" id="cabang" class="form-control" required
                                            data-session-branch-id="<?= esc($sessionBranchId) ?>"
                                            data-session-branch-name="<?= esc($sessionBranchName) ?>"></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="docdate">Tanggal</label>
                                        <input type="text" name="docdate" id="docdate" class="form-control" placeholder="Tanggal">
                                    </div>
                                </div>
                            </div>

                            <!-- ROW 2 : No. Jurnal & keterangan -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. Jurnal JUP</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" name="prefix" id="prefix" class="form-control me-1" maxlength="3" style="text-transform: uppercase;" pattern="[A-Z0-9]+">
                                            <span class="px-2 align-self-center">/</span>
                                            <input type="text" name="infix" id="infix" class="form-control mx-1" readonly>
                                            <span class="px-2 align-self-center">/</span>
                                            <input type="text" name="sufix" id="sufix" class="form-control ms-1" readonly>
                                        </div>
                                        <input type="hidden" name="docno" id="docno" value="<?= isset($dtldata['docno']) ? esc(trim($dtldata['docno'])) : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Keterangan" style="text-transform:uppercase;"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- DETAIL PERKIRAAN (grid inline) -->
                <div class="card mt-3 card-primary">
                    <div class="card-header d-flex align-items-center flex-wrap gap-2">
                        <h3 class="card-title mb-0 me-3">
                            <i class="fa fa-book"></i> Detail Perkiraan
                        </h3>

                        <div class="ms-auto d-flex align-items-center flex-wrap gap-2">
                            <span class="jup-selection-info" id="jupSelectedInfo">0 dari 0 baris dipilih</span>

                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    id="btnAddJUPDetail"
                                    title="Tambah baris (Alt+N)">
                                <i class="fa fa-plus me-1"></i> Tambah Baris
                            </button>

                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    id="btnDeleteJUPDetail"
                                    title="Hapus baris terpilih (Ctrl+Delete)"
                                    disabled>
                                <i class="fa fa-trash me-1"></i> Hapus Terpilih
                            </button>
                        </div>
                    </div>

                    <div class="card-body position-relative">
                        <div id="jupSavingOverlay" class="jup-saving-overlay d-none">
                            <div class="jup-saving-box">
                                <i class="fa fa-spinner fa-spin me-2"></i> Menyimpan detail JUP...
                            </div>
                        </div>

                        <div class="table-responsive" style="overflow-x: auto;">
                            <table id="jupGrid" class="table table-bordered table-sm jup-grid mb-0" width="100%">
                                <thead>
                                    <tr>
                                        <th width="34" class="text-center">
                                            <input type="checkbox" id="jupCheckAll" title="Pilih semua baris">
                                        </th>
                                        <th width="18%">Kode Perk.</th>
                                        <th>Nama Perkiraan</th>
                                        <th width="70">DK</th>
                                        <th width="20%">Cost/Profit Center</th>
                                        <th>Keterangan</th>
                                        <th width="150" class="text-end">Nilai</th>
                                        <th width="76" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="jupGridBody"></tbody>
                            </table>
                        </div>

                        <div class="jup-grid-footer">
                            <div class="jup-balance-state jup-badge-notok" id="jupBalanceState">
                                <i class="fa fa-exclamation-triangle"></i> SELISIH 0.00
                            </div>

                            <div class="jup-totals">
                                <div class="jup-total-item">
                                    <span>Total Debit</span>
                                    <strong id="jupTotalDebit">0.00</strong>
                                </div>
                                <div class="jup-total-item">
                                    <span>Total Kredit</span>
                                    <strong id="jupTotalKredit">0.00</strong>
                                </div>
                                <div class="jup-total-item">
                                    <span>Balance</span>
                                    <strong id="jupBalance" class="jup-ok">0.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- tetap dipertahankan: dibaca validasi form pada jup.js -->
                        <input type="hidden" name="balance" id="balance" value="0.00">

                        <small class="jup-grid-hint">
                            <i class="fa fa-keyboard me-1"></i>
                            Enter = simpan baris &amp; pindah kolom, panah atas/bawah = pindah baris,
                            Alt+N = tambah baris, Esc = batalkan perubahan baris,
                            Ctrl+Delete = hapus baris terpilih.
                        </small>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('ka/accounting/jup') ?>"
                               class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                            <?php if ($typeform != 'DETAIL'): ?>
                                <button type="submit"
                                        id="btnSimpanFinalJUP"
                                        class="btn btn-success">
                                    <i class="fa fa-save me-1"></i> Simpan Final Data
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </form>
        <!--/.col (left) -->
    </div>
</div><!-- /.corp-form-container -->



<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/jup.js') ?>"></script>
<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/jup_add.js') ?>"></script>
<script type="application/javascript" src="<?= base_url('assets/pagejs/finance/jup_branch_autofill.js') ?>"></script>
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
