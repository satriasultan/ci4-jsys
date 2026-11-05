<!-- =========================================================
     v_input_suppliers.php - Corporate Style
     Referensi tampilan : purchase/po/v_add_po.php (versi corporate)
     Catatan : perubahan pada tampilan (CSS & layout) dan
               penambahan field yang memang diminta untuk diedit.
               Nama field/id/fungsi yang sudah ada tidak diubah.
========================================================== -->
<style>
    /* =========================================================
       CORPORATE THEME VARIABLES
    ========================================================= */
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
        --corp-warning:       #ffc107;
        --corp-danger:        #dc2626;
        --corp-radius:        6px;
        --corp-shadow:        0 1px 3px rgba(15, 23, 42, 0.06);

        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        color: var(--corp-text);
        font-size: 13.5px;
    }

    /* =========================================================
       CARD
    ========================================================= */
    .corp-form-container .card {
        border: 1px solid var(--corp-border) !important;
        border-radius: var(--corp-radius) !important;
        box-shadow: var(--corp-shadow) !important;
        overflow: hidden;
        background: var(--corp-bg-section);
    }

    /* warna background header mengikuti global corporate header (layout) */
    .corp-form-container .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #ffffff;
        border-bottom: none;
    }

    .corp-form-container .card-header h3 {
        float: none;
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #ffffff;
    }

    .corp-form-container .card-header h3 i {
        margin-right: 8px;
        font-size: 14px;
    }

    .corp-form-container .card-header::before,
    .corp-form-container .card-header::after {
        display: none !important;
    }

    .corp-form-container .card-header .card-title {
        flex: 0 1 auto;
        margin: 0 auto 0 0;
    }

    .corp-form-container .card-body {
        padding: 22px;
        background: var(--corp-bg-light);
    }

    .corp-form-container .card-footer {
        background: var(--corp-bg-light) !important;
        border-top: 1px solid var(--corp-border) !important;
        padding: 14px 22px;
    }

    .corp-form-container .card-footer::after {
        content: "";
        display: block;
        clear: both;
    }

    /* =========================================================
       SECTION BLOCK
    ========================================================= */
    .corp-form-container .section-block {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 20px 22px;
        margin-bottom: 16px;
        box-shadow: var(--corp-shadow);
        transition: none;
    }

    .corp-form-container .section-block:last-of-type {
        margin-bottom: 0;
    }

    .corp-form-container .section-block:hover {
        box-shadow: var(--corp-shadow);
        border-color: var(--corp-border);
    }

    .corp-form-container .section-header {
        display: flex;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
        color: var(--corp-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--corp-border);
    }

    .corp-form-container .section-header i {
        margin-right: 8px;
        font-size: 14px;
        color: var(--corp-primary);
    }

    /* =========================================================
       FORM ELEMENTS
    ========================================================= */
    .corp-form-container .form-group {
        margin-bottom: 16px;
    }

    .corp-form-container label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--corp-text);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
    }

    .corp-form-container label span[style*="color: red"] {
        color: var(--corp-danger) !important;
        font-weight: 700;
    }

    .corp-form-container .form-control,
    .corp-form-container .form-select,
    .corp-form-container select.form-control {
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: var(--corp-text);
        background-color: #ffffff;
        height: auto;
        min-height: 40px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .corp-form-container .form-control:focus,
    .corp-form-container .form-select:focus,
    .corp-form-container select.form-control:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .corp-form-container textarea.form-control {
        min-height: 74px;
        resize: vertical;
        line-height: 1.45;
    }

    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly] {
        background-color: var(--corp-bg-disabled);
        color: #202020;
        opacity: 1;
        cursor: not-allowed;
        border-color: var(--corp-border);
    }

    /* select native (Hold / Status / PKP / Import-Local / Market / Provinsi / Kota) */
    .corp-form-container select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%2364748b' d='M8 11.5 2.8 6.3l1-1L8 9.5l4.2-4.2 1 1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px 14px;
        padding-right: 30px;
    }

    /* angka rata kanan (class global .ratakanan) */
    .corp-form-container .ratakanan,
    .corp-form-container .text-end {
        text-align: right;
    }

    /* keterangan kecil untuk field audit */
    .corp-form-container .audit-note {
        display: block;
        font-size: 11px;
        font-weight: 500;
        color: var(--corp-text-muted);
        text-transform: none;
        letter-spacing: 0;
        margin-top: 4px;
    }


    /* =========================================================
       SELECT2 (corporate override)
    ========================================================= */
    .corp-form-container .select2-container {
        width: 100% !important;
    }

    .corp-form-container .select2-container .select2-selection--single {
        height: 40px !important;
        min-height: 40px !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        background-color: #ffffff;
    }

    .corp-form-container .select2-container--focus .select2-selection--single,
    .corp-form-container .select2-container--open .select2-selection--single {
        border-color: var(--corp-primary) !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 11px !important;
        padding-right: 26px !important;
        font-size: 13.5px;
        color: var(--corp-text) !important;
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__placeholder {
        color: var(--corp-text-muted);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__clear {
        font-size: 16px;
        color: var(--corp-text-muted);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 6px;
    }

    .corp-form-container .select2-dropdown {
        border-color: var(--corp-border) !important;
        border-radius: 4px;
        font-size: 13.5px;
    }

    .corp-form-container .select2-results__option--highlighted,
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--corp-primary) !important;
        color: #ffffff !important;
    }

    .corp-form-container .select2-container--disabled .select2-selection--single {
        background-color: var(--corp-bg-disabled) !important;
        border-color: var(--corp-border) !important;
    }

    .corp-form-container .select2-container--disabled .select2-selection--single .select2-selection__rendered {
        color: #202020 !important;
    }

    /* =========================================================
       CHECKBOX & FORM CHECK
    ========================================================= */
    .corp-form-container input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: var(--corp-primary);
        cursor: pointer;
        vertical-align: middle;
    }

    .corp-form-container .form-check.corporate-check {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        min-height: 20px;
    }

    .corp-form-container .form-check.corporate-check:last-child {
        margin-bottom: 0;
    }

    .corp-form-container .form-check.corporate-check .form-check-input {
        margin: 0;
        width: 16px;
        height: 16px;
        border: 1px solid #9ca3af;
        accent-color: var(--corp-primary);
    }

    .corp-form-container .form-check.corporate-check .form-check-label {
        margin: 0;
        font-size: 12.5px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0.2px;
        color: #111827;
        cursor: pointer;
    }

    /* =========================================================
       BUTTONS
    ========================================================= */
    .corp-form-container .btn {
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 9px 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        box-shadow: none;
        transition: all 0.15s ease;
    }

    .corp-form-container .btn i {
        margin-right: 5px;
    }

    .corp-form-container .btn-lg {
        font-size: 12.5px;
        padding: 9px 20px;
    }

    .corp-form-container .btn-primary,
    .corp-form-container .btn-primary:focus {
        background-color: var(--corp-primary);
        border-color: var(--corp-primary);
        color: #ffffff;
    }

    .corp-form-container .btn-primary:hover {
        background-color: var(--corp-primary-hover);
        border-color: var(--corp-primary-hover);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(30, 58, 95, 0.25);
    }

    .corp-form-container .btn-default,
    .corp-form-container .btn-secondary {
        background-color: #ffffff;
        border-color: var(--corp-border);
        color: var(--corp-text);
    }

    .corp-form-container .btn-default:hover,
    .corp-form-container .btn-secondary:hover,
    .corp-form-container .btn-secondary:focus {
        background-color: var(--corp-bg-disabled);
        border-color: var(--corp-text-muted);
        color: var(--corp-text);
    }


    /* =========================================================
       PAGE HEADER / BREADCRUMB
    ========================================================= */
    .corp-form-container .content-header {
        padding: 4px 0 14px;
    }

    .corp-form-container .content-header h1 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.3px;
        color: var(--corp-primary);
        text-transform: uppercase;
    }

    .corp-form-container .content-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 12px;
    }

    .corp-form-container .content-header .breadcrumb-item,
    .corp-form-container .content-header .breadcrumb-item a {
        color: var(--corp-text-muted);
        font-size: 12px;
    }

    .corp-form-container .content-header .breadcrumb-item a:hover {
        color: var(--corp-primary);
        text-decoration: none;
    }

    .corp-form-container .content-header .breadcrumb-item.active {
        color: var(--corp-primary);
        font-weight: 600;
    }

    /* =========================================================
       ALERT (pesan proses dari controller)
    ========================================================= */
    .corp-form-container .alert {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        font-size: 13px;
        padding: 11px 16px;
    }

    .corp-form-container .alert-info {
        background-color: var(--corp-primary-light);
        border-color: #bcd0e6;
        color: var(--corp-primary);
    }

    .corp-form-container .alert-danger {
        background-color: #fdecec;
        border-color: #f3c2c2;
        color: var(--corp-danger);
    }

    /* =========================================================
       VALIDATION (bootstrapValidator)
    ========================================================= */
    .corp-form-container .is-invalid,
    .corp-form-container .form-control.is-invalid {
        border-color: var(--corp-danger) !important;
    }

    .corp-form-container .invalid-feedback,
    .corp-form-container .help-block {
        display: block;
        color: var(--corp-danger);
        font-size: 11.5px;
        margin-top: 4px;
    }

    .corp-form-container .has-error .form-control,
    .corp-form-container .has-danger .form-control {
        border-color: var(--corp-danger) !important;
        box-shadow: none;
    }

    .corp-form-container .has-success .form-control {
        border-color: var(--corp-success) !important;
        box-shadow: none;
    }

    /* =========================================================
       RESPONSIVE TWEAK
    ========================================================= */
    @media (max-width: 768px) {
        .corp-form-container .card-body {
            padding: 14px;
        }

        .corp-form-container .section-block {
            padding: 16px 14px;
        }

        .corp-form-container .card-header {
            flex-wrap: wrap;
        }
    }
</style>
<div class="corp-form-container">

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
    <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-primary">
            <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-truck"></i> <?=  $typeTitle = ($type == 'INPUT') ? 'Input' : ($type == 'UPDATE' ? 'Edit' : 'Detail'); ?> Data Master Supplier</h3></div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  action="<?php echo base_url('#')?>" method="post" id="formInputSupplier" enctype="multipart/form-data" role="form">
                <div class="card-body">
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-address-card"></i> Identitas Supplier
                        </div>
                        <div class="row">

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="kdsupplier">Kode Supplier</label>
                                    <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                                    <input type="hidden" id="id" name="id" value="<?= $id ?>" autocomplete="off">
                                    <input type="text" name="kdsupplier" class="form-control" id="kdsupplier" <?= $type == 'UPDATE' ? 'disabled' : '' ?> maxlength="60" placeholder="Kode Supplier" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-6">
                                <div class="form-group">
                                    <label for="nmsupplier">Nama Supplier</label>
                                    <input type="text" name="nmsupplier" class="form-control" id="nmsupplier" maxlength="250" placeholder="Nama Supplier" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="chold">Hold</label>
                                    <select name="chold" id="chold" class="form-control inform" style="text-transform:uppercase;">
                                        <!--option value="">--Pilih Hold--</option-->
                                        <option value="NO"> NO </option>
                                        <option value="YES">YES </option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="F">AKTIF (F)</option>
                                        <option value="D">NON AKTIF (D)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="createdby">Created By</label>
                                    <input type="text" name="createdby" id="createdby" class="form-control" readonly>
                                    <span class="audit-note">Terisi otomatis saat data dibuat.</span>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="createddate">Created Date</label>
                                    <input type="text" name="createddate" id="createddate" class="form-control" readonly>
                                    <span class="audit-note">Otomatis dari sistem.</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-map-marker"></i> Kontak & Alamat
                        </div>
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" id="alamat" placeholder="Alamat" style="text-transform:uppercase;"></textarea>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="idprovinsi">Wilayah/Provinsi</label>
                                    <select name="idprovinsi" id="idprovinsi" class="form-control">
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="idkota">Kota</label>
                                    <select name="idkota" id="idkota" class="form-control">
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="phone">No. Telp</label>
                                    <input type="text" name="phone" class="form-control" id="phone" maxlength="20" placeholder="No. Telp" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="fax">Fax</label>
                                    <input type="text" name="fax" class="form-control" id="fax" maxlength="30" placeholder="Fax" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-0">
                                    <label for="email">Email</label>
                                    <input type="text" name="email" class="form-control" id="email" maxlength="150" placeholder="Email" style="text-transform:lowercase;">
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-percent"></i> Pajak & Keuangan
                        </div>
                        <div class="row">

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="npwp">NPWP</label>
                                    <input type="text" name="npwp" class="form-control" id="npwp" maxlength="50" placeholder="NPWP" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="npkp">NPKP</label>
                                    <input type="text" name="npkp" class="form-control" id="npkp" maxlength="50" placeholder="NPKP" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="pkp_non_pkp">PKP / Non PKP</label>
                                    <select name="pkp_non_pkp" id="pkp_non_pkp" class="form-control">
                                        <option value="">-- Pilih --</option>
                                        <option value="PPN">PPN</option>
                                        <option value="NON PPN">NON PPN</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="currency">Currency</label>
                                    <select name="currency" id="currency" class="form-control select2" style="width:100%">
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="import_local">Import / Local</label>
                                    <select name="import_local" id="import_local" class="form-control">
                                        <option value="">-- Pilih --</option>
                                        <option value="IMPORT">IMPORT</option>
                                        <option value="LOCAL">LOCAL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="plafon">Plafon</label>
                                    <input type="text"
                                           name="plafon"
                                           id="plafon"
                                           class="form-control ratakanan jtsseparator"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="jthtempo">Jatuh Tempo</label>
                                    <input type="text"
                                           name="jthtempo"
                                           id="jthtempo"
                                           class="form-control ratakanan jtsseparator"
                                           placeholder="0.00">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-users"></i> Pemasaran & PIC
                        </div>
                        <div class="row">

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="idmarket">Market</label>
                                    <select name="idmarket" id="idmarket" class="form-control" required>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="cp">Contact Person</label>
                                    <input type="text" name="cp" class="form-control" id="cp" maxlength="100" placeholder="Contact Person" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="jabatan">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control" id="jabatan" maxlength="50" placeholder="Jabatan" style="text-transform:uppercase;">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="d-block">Kategori</label>

                                    <!-- Interngroup -->
                                    <div class="form-check corporate-check">
                                        <input class="form-check-input" type="checkbox" name="interngroup" id="interngroup" value="1" <?= isset($data['interngroup']) && $data['interngroup'] == '1' ? 'checked' : '' ?> <?= $disabled ?>>
                                        <label class="form-check-label" for="interngroup">
                                            Interngroup
                                        </label>
                                    </div>

                                    <!-- Blacklist -->
                                    <div class="form-check corporate-check">
                                        <input class="form-check-input" type="checkbox" name="blacklist" id="blacklist" value="1" <?= isset($data['blacklist']) && $data['blacklist'] == '1' ? 'checked' : '' ?> <?= $disabled ?>>
                                        <label class="form-check-label" for="blacklist">
                                            Blacklist
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group mb-0">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Catatan/Informasi..." style="text-transform:uppercase;" <?= $disabled ?>></textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-laptop"></i> Informasi Supplier
                        </div>
                        <div class="row">                            
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-file-text"></i> Informasi Penggunaan
                        </div>
                        <div class="row">                            
                        </div>
                    </div>
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-exchange-alt"></i> Informasi Perizinan 
                        </div>
                        <div class="row">
                        </div>
                    </div> -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="<?= base_url('master/data/supplier') ?>" class="btn btn-default">Kembali</a>
                    <button id="btnSave" onclick="saveInputSupplier();" class="btn btn-primary float-right">Submit</button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
    <!--/.col (left) -->
    <!-- right column -->
    <div class="col-md-6">

    </div>
    <!--/.col (right) -->
</div>
</div><!-- /.corp-form-container -->





<script type="application/javascript" src="<?= base_url('assets/pagejs/master/supplier/suppliers.js') ?>"></script>
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
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#docdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('#docdate').on('cancel.daterangepicker', function(ev, picker) {
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