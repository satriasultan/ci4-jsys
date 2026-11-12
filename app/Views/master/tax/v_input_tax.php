<style>
    /* =========================================================
       v_input_tax.php — CORPORATE STYLE
       Referensi tampilan : master/currency/v_input_currency.php
       Catatan : hanya CSS & layout yang diubah.
                 name / id field / function JS tetap dipertahankan.
    ========================================================= */

    /* ---------- CORPORATE THEME VARIABLES ---------- */
    .corp-form-container {
        --corp-primary:       #1e3a5f;
        --corp-primary-hover: #152a45;
        --corp-primary-light: #e8eef5;
        --corp-text:          #1a202c;
        --corp-text-muted:    #64748b;
        --corp-border:        #cbd5e1;
        --corp-bg-light:      #f8fafc;
        --corp-bg-section:    #ffffff;
        --corp-bg-disabled:   #f1f5f9;
        --corp-success:       #16a34a;
        --corp-warning:       #f59e0b;
        --corp-danger:        #dc2626;
        --corp-radius:        6px;
        --corp-shadow:        0 1px 3px rgba(15, 23, 42, 0.06);

        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        color: var(--corp-text);
        font-size: 13.5px;
    }

    /* ---------- CARD ---------- */
    .corp-form-container .card {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        box-shadow: var(--corp-shadow);
        overflow: hidden;
        background: var(--corp-bg-section);
    }

    .corp-form-container .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(135deg, var(--corp-primary) 0%, var(--corp-primary-hover) 100%);
        color: #ffffff;
        padding: 13px 20px;
        border-bottom: none;
    }

    .corp-form-container .card-header::before,
    .corp-form-container .card-header::after {
        display: none !important;
    }

    .corp-form-container .card-header h3,
    .corp-form-container .card-header .card-title {
        float: none;
        margin: 0;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .corp-form-container .card-header h3 i {
        margin-right: 8px;
        font-size: 13px;
    }

    .corp-form-container .card-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .corp-form-container .card-body {
        padding: 20px;
        background: var(--corp-bg-light);
    }

    .corp-form-container .card-footer {
        background: var(--corp-bg-light) !important;
        border-top: 1px solid var(--corp-border) !important;
        padding: 12px 20px;
    }

    /* ---------- SECTION BLOCK ---------- */
    .corp-form-container .section-block {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 18px 20px;
        margin-bottom: 16px;
        box-shadow: var(--corp-shadow);
    }

    .corp-form-container .section-block:last-of-type {
        margin-bottom: 0;
    }

    .corp-form-container .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--corp-border);
        color: var(--corp-primary);
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    /* ---------- FORM CONTROLS ---------- */
    .corp-form-container .form-group {
        margin-bottom: 14px;
    }

    .corp-form-container label {
        display: block;
        margin-bottom: 5px;
        color: var(--corp-text-muted);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .corp-form-container .form-control,
    .corp-form-container select.form-control {
        height: 36px;
        padding: 6px 10px;
        font-size: 13px;
        color: var(--corp-text);
        background: #ffffff;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        box-shadow: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .corp-form-container .form-control:focus,
    .corp-form-container select.form-control:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .corp-form-container .form-control[readonly],
    .corp-form-container .form-control.bg-light {
        background: var(--corp-bg-disabled) !important;
        color: var(--corp-text);
    }

    .corp-form-container .form-control:disabled {
        background: var(--corp-bg-disabled);
        color: var(--corp-text-muted);
    }

    .corp-form-container hr {
        border-top: 1px solid var(--corp-border);
        margin: 12px 0 18px;
    }

    /* ---------- BUTTONS ---------- */
    .corp-form-container .btn {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 8px 16px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: none;
        transition: all 0.15s;
    }

    .corp-form-container .btn i {
        margin-right: 6px;
    }

    .corp-form-container .btn-sm {
        padding: 7px 13px;
        font-size: 11.5px;
    }

    .corp-form-container .btn-primary {
        background: var(--corp-primary);
        border-color: var(--corp-primary);
        color: #ffffff;
    }

    .corp-form-container .btn-primary:hover,
    .corp-form-container .btn-primary:focus {
        background: var(--corp-primary-hover);
        border-color: var(--corp-primary-hover);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(30, 58, 95, 0.25);
    }

    .corp-form-container .btn-success {
        background: var(--corp-success);
        border-color: var(--corp-success);
        color: #ffffff;
    }

    .corp-form-container .btn-success:hover,
    .corp-form-container .btn-success:focus {
        background: #128040;
        border-color: #128040;
        color: #ffffff;
    }

    .corp-form-container .btn-warning {
        background: var(--corp-warning);
        border-color: var(--corp-warning);
        color: #ffffff;
    }

    .corp-form-container .btn-warning:hover,
    .corp-form-container .btn-warning:focus {
        background: #d97706;
        border-color: #d97706;
        color: #ffffff;
    }

    .corp-form-container .btn-danger {
        background: var(--corp-danger);
        border-color: var(--corp-danger);
        color: #ffffff;
    }

    .corp-form-container .btn-danger:hover,
    .corp-form-container .btn-danger:focus {
        background: #b91c1c;
        border-color: #b91c1c;
        color: #ffffff;
    }

    .corp-form-container .btn-default,
    .corp-form-container .btn-secondary {
        background: #ffffff;
        border-color: var(--corp-border);
        color: var(--corp-text);
    }

    .corp-form-container .btn-default:hover,
    .corp-form-container .btn-secondary:hover {
        background: var(--corp-bg-disabled);
        border-color: var(--corp-text-muted);
        color: var(--corp-text);
    }

    /* ---------- TABLE GRID ---------- */
    .corp-form-container .table-wrapper {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        overflow-x: auto;
        background: #ffffff;
    }

    .corp-form-container #tblMasterPajakDetail {
        width: 100% !important;
        margin-bottom: 0;
        font-size: 12.5px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .corp-form-container #tblMasterPajakDetail thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        text-align: center;
        vertical-align: middle;
        padding: 10px 9px;
        border: none !important;
        white-space: nowrap;
    }

    .corp-form-container #tblMasterPajakDetail thead th:first-child {
        width: 34px;
    }

    .corp-form-container #tblMasterPajakDetail thead input[type="checkbox"] {
        width: 15px;
        height: 15px;
        margin: 0;
        vertical-align: middle;
        cursor: pointer;
        accent-color: var(--corp-primary);
    }

    .corp-form-container #tblMasterPajakDetail tbody td {
        padding: 7px 9px;
        vertical-align: middle;
        color: var(--corp-text);
        border: 1px solid var(--corp-border) !important;
        background: #ffffff;
        white-space: nowrap;
    }

    .corp-form-container #tblMasterPajakDetail tbody td:first-child {
        text-align: center;
    }

    .corp-form-container #tblMasterPajakDetail tbody input[type="checkbox"].row-check {
        width: 15px;
        height: 15px;
        margin: 0;
        cursor: pointer;
        accent-color: var(--corp-primary);
    }

    .corp-form-container #tblMasterPajakDetail tbody tr:nth-child(even) td {
        background: var(--corp-bg-light);
    }

    .corp-form-container #tblMasterPajakDetail tbody tr:hover td {
        background: var(--corp-primary-light);
    }

    .corp-form-container #tblMasterPajakDetail tbody tr.selected td {
        background: #dbe7f3 !important;
    }

    /* ---------- DATATABLES ---------- */
    .corp-form-container .dataTables_wrapper .dataTables_length,
    .corp-form-container .dataTables_wrapper .dataTables_info {
        font-size: 12px;
        color: var(--corp-text-muted);
    }

    .corp-form-container .dataTables_wrapper .dataTables_length select {
        height: 32px;
        padding: 4px 8px;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        font-size: 12.5px;
        background: #ffffff;
    }

    .corp-form-container .dataTables_wrapper .dataTables_filter input {
        height: 32px;
        padding: 4px 9px;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        font-size: 12.5px;
    }

    .corp-form-container .dataTables_wrapper .dt-buttons .btn {
        margin-right: 4px;
    }

    .corp-form-container .dataTables_wrapper .dataTables_processing {
        top: 0;
        background: #ffffff;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        font-size: 12.5px;
        color: var(--corp-text-muted);
    }

    /* ---------- MODAL ---------- */
    .corp-form-container.modal .modal-content {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }

    .corp-form-container.modal .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, var(--corp-primary) 0%, var(--corp-primary-hover) 100%);
        color: #ffffff;
        padding: 13px 20px;
        border-bottom: none;
    }

    .corp-form-container.modal .modal-title {
        margin: 0;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .corp-form-container.modal .modal-title i {
        margin-right: 8px;
        font-size: 13px;
    }

    .corp-form-container.modal .modal-header .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
        opacity: 0.85;
        box-shadow: none;
    }

    .corp-form-container.modal .modal-body {
        background: var(--corp-bg-light);
        padding: 20px;
    }

    .corp-form-container.modal .modal-body .form-group {
        margin-bottom: 14px;
    }

    .corp-form-container.modal .modal-footer {
        background: #ffffff !important;
        border-top: 1px solid var(--corp-border) !important;
        padding: 12px 20px;
        gap: 8px;
    }

    .corp-form-container.modal .modal-footer .btn i {
        margin-right: 6px;
    }

    /* ---------- SELECT2 ---------- */
    .corp-form-container .select2-container--default .select2-selection--single {
        height: 36px;
        padding: 4px 6px;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        background: #ffffff;
    }

    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    .corp-form-container .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
        font-size: 13px;
        color: var(--corp-text);
    }

    .corp-form-container .select2-dropdown {
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12) !important;
        z-index: 1070 !important;
    }

    .corp-form-container .select2-results__option {
        padding: 8px 11px !important;
        font-size: 13px !important;
    }

    .corp-form-container .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
    }

    .corp-form-container .select2-search--dropdown .select2-search__field {
        min-height: 34px;
        border: 1px solid var(--corp-border);
        border-radius: 3px;
        font-size: 13px;
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <?php if ($typeform === 'INPUT'): ?>
                        Input Master Tax
                    <?php elseif ($typeform === 'UPDATE'): ?>
                        Update Master Tax
                    <?php else: ?>
                        Detail Master Tax
                    <?php endif; ?>
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right:10px;padding-top:.7%">
                        Menu ID <?= $version; ?>
                    </div>
                    <input type="hidden" id="classmenu"
                           value="<?= str_replace('.', '_', $kodemenu) ?>">

                    <?php foreach ($y as $y1): ?>
                        <?php if (trim($y1->kodemenu) != trim($kodemenu)): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(trim($y1->linkmenu)); ?>">
                                    <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                    <?= trim($y1->namamenu); ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active">
                                <i class="fa <?= trim($y1->iconmenu); ?>"></i>
                                <?= trim($y1->namamenu); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= $message; ?>

<div class="row corp-form-container">
    <div class="col-md-12">

        <!-- ================= MASTER TAX ================= -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-percent"></i> Data Master Tax</h3>
                <div class="card-actions">
                    <button type="button" id="btnUpdate" class="btn btn-warning btn-sm"
                            title="Update Master" onclick="updateData()">
                        <i class="fa fa-edit"></i> Update
                    </button>
                    <button type="button" id="btnSave" class="btn btn-success btn-sm d-none"
                            title="Simpan Master" onclick="saveData()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button" id="btnCancel" class="btn btn-secondary btn-sm d-none"
                            title="Batalkan Perubahan" onclick="cancelUpdate()">
                        <i class="fa fa-undo"></i> Batal
                    </button>
                </div>
            </div>

            <div class="card-body">
                <form id="formInputTaxMst" method="post" autocomplete="off">
                    <input type="hidden" name="type" value="<?= $type ?>">
                    <input type="hidden" name="id" id="id_mst" value="<?= $id ?>">

                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-tag"></i> Informasi Master Tax
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="idtax_mst">Kode Pajak <span class="text-danger">*</span></label>
                                    <input type="text" name="idtax" id="idtax_mst"
                                           class="form-control" placeholder="KODE PAJAK"
                                           maxlength="20" style="text-transform:uppercase;" readonly>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nmtax_mst">Keterangan <span class="text-danger">*</span></label>
                                    <input type="text" name="nmtax" id="nmtax_mst"
                                           class="form-control" placeholder="NAMA TAX"
                                           maxlength="50" style="text-transform:uppercase;" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer">
                <a href="<?= base_url('master/data/tax') ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>


        <!-- ================= DETAIL TAX ================= -->
        <div class="card mt-3">
            <div class="card-header">
                <h3><i class="fa fa-list-alt"></i> Detail Master Tax</h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-success btn-sm"
                            title="Input Detail" onclick="btnInputDetail()">
                        <i class="fa fa-plus"></i> Input
                    </button>
                    <button type="button" class="btn btn-warning btn-sm"
                            title="Update Detail" onclick="btnUpdateDetail()">
                        <i class="fa fa-edit"></i> Update
                    </button>
                    <button type="button" class="btn btn-danger btn-sm"
                            title="Hapus Detail" onclick="btnDeleteDetail()">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-wrapper">
                    <table id="tblMasterPajakDetail" class="table">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>ID Tax</th>
                            <th>Jenis</th>
                            <th>Nama</th>
                            <th>Kode Pajak</th>
                            <th>PRK Masukan</th>
                            <th>PRK Keluaran</th>
                            <th>PRK FPJ Msk</th>
                            <th>PRK FPJ Klr</th>
                            <th>PRK FPJ Msk Rep</th>
                            <th>PRK FPJ Klr Rep</th>
                            <th>DPP Lain</th>
                            <th>DPP Pajak</th>
                            <th>% Pajak</th>
                            <th>Hold</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- ================= END FORM ================= -->

<!-- ================= MODAL TAX DETAIL ================= -->
<div class="modal fade corp-form-container" id="modalTaxDetail" tabindex="-1"
     aria-labelledby="modalTaxDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalTaxDetailLabel">
                    <i class="fa fa-plus"></i> Input Tax Detail
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- FORM -->
            <form id="formTaxDetail" autocomplete="off">
                <div class="modal-body">

                    <!-- hidden -->
                    <input type="hidden" name="iddtl" id="iddtl">
                    <input type="hidden" name="idtax" id="idtax_dtl">
                    <input type="hidden" name="status" id="status" value="P">
                    <input type="hidden" name="chold" id="chold" value="NO">

                    <!-- ROW 1 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="idgrouptax">Jenis <span class="text-danger">*</span></label>
                                <input type="text" name="idgrouptax" id="idgrouptax"
                                       class="form-control" placeholder="JENIS PAJAK"
                                       maxlength="50" style="text-transform:uppercase">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nmgrouptax">Nama</label>
                                <input type="text" name="nmgrouptax" id="nmgrouptax"
                                       class="form-control" placeholder="NAMA PAJAK"
                                       maxlength="50" style="text-transform:uppercase">
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kodepajak">Kode Pajak</label>
                                <input type="text" name="kodepajak" id="kodepajak"
                                       class="form-control" placeholder="KODE PAJAK"
                                       maxlength="20" style="text-transform:uppercase">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="percentation">% Pajak</label>
                                <input type="number" step="0.01" min="0" max="100"
                                       name="percentation" id="percentation"
                                       class="form-control" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- ROW 3 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prk_masukan">PRK Masukan</label>
                                <select name="prk_masukan" id="prk_masukan"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prk_keluaran">PRK Keluaran</label>
                                <select name="prk_keluaran" id="prk_keluaran"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prk_fpj_msk">PRK FPJ Masuk</label>
                                <select name="prk_fpj_msk" id="prk_fpj_msk"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prk_fpj_klr">PRK FPJ Keluar</label>
                                <select name="prk_fpj_klr" id="prk_fpj_klr"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 4 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prk_fpj_msk_rep">PRK FPJ Masuk Rep.</label>
                                <select name="prk_fpj_msk_rep" id="prk_fpj_msk_rep"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prk_fpj_klr_rep">PRK FPJ Keluar Rep.</label>
                                <select name="prk_fpj_klr_rep" id="prk_fpj_klr_rep"
                                        class="form-control select2" style="width:100%"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rumus_dpp_lain">DPP Lain</label>
                                <input type="text" name="rumus_dpp_lain" id="rumus_dpp_lain"
                                       class="form-control" maxlength="20"
                                       placeholder="RUMUS DPP LAIN">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rumus_pajak">DPP Pajak</label>
                                <input type="text" name="rumus_pajak" id="rumus_pajak"
                                       class="form-control" maxlength="20"
                                       placeholder="RUMUS PAJAK">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveTaxDetail()">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ================= END MODAL ================= -->

<script type="application/javascript" src="<?= base_url('assets/pagejs/master/tax/tax.js') ?>"></script>
<script>
    $(function () {
        // Bootstrap 5 tooltip (tanpa plugin jQuery)
        if (window.bootstrap && bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        }
    });
</script>

