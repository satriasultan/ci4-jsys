<!-- v_input.php — Corporate Style -->

<style>
    /* =========================================================
       CORPORATE THEME VARIABLES
    ========================================================= */
    :root {
        --corp-primary:       #1e3a5f;
        --corp-primary-hover: #152a45;
        --corp-primary-light: #e8eef5;
        --corp-accent:        #2c5282;
        --corp-text:          #1a202c;
        --corp-text-muted:    #64748b;
        --corp-border:        #cbd5e1;
        --corp-bg-light:      #f8fafc;
        --corp-bg-section:    #ffffff;
        --corp-bg-disabled:   #f1f5f9;
        --corp-success:       #16a34a;
        --corp-danger:        #dc2626;
        --corp-radius:        6px;
        --corp-shadow:        0 1px 3px rgba(15, 23, 42, 0.06);
    }

    /* =========================================================
       FORM CONTAINER
    ========================================================= */
    .corp-form-container {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        color: var(--corp-text);
        font-size: 13.5px;
    }

    .corp-form-container .card {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        box-shadow: var(--corp-shadow);
        overflow: hidden;
        background: var(--corp-bg-section);
    }

    .corp-form-container .card-header {
        background: var(--corp-primary);
        color: #ffffff;
        padding: 14px 22px;
        border-bottom: none;
    }

    .corp-form-container .card-header h3 {
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.3px;
        margin: 0;
        text-transform: uppercase;
    }

    .corp-form-container .card-body {
        padding: 0;
        background: var(--corp-bg-light);
    }

    .corp-form-container .card-footer {
        background: var(--corp-bg-light);
        border-top: 1px solid var(--corp-border);
        padding: 14px 22px;
    }

    /* =========================================================
       TABS — Corporate flat design
    ========================================================= */
    .corp-form-container .nav-tabs {
        background: #ffffff;
        border-bottom: 2px solid var(--corp-border);
        padding: 0 22px;
        margin: 0;
    }

    .corp-form-container .nav-tabs .nav-item {
        margin-bottom: -2px;
    }

    .corp-form-container .nav-tabs .nav-link {
        color: var(--corp-text-muted);
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 14px 20px;
        margin-right: 4px;
        background: transparent;
        transition: all 0.15s ease;
    }

    .corp-form-container .nav-tabs .nav-link i {
        margin-right: 6px;
        font-size: 13px;
    }

    .corp-form-container .nav-tabs .nav-link:hover {
        color: var(--corp-primary);
        border-bottom-color: var(--corp-primary-light);
    }

    .corp-form-container .nav-tabs .nav-link.active {
        color: var(--corp-primary);
        border-bottom-color: var(--corp-primary);
        background: transparent;
        font-weight: 700;
    }

    /* =========================================================
       TAB CONTENT
    ========================================================= */
    .corp-form-container .tab-content {
        padding: 22px;
        background: var(--corp-bg-light);
    }

    /* =========================================================
       SECTION BLOCK
    ========================================================= */
    .corp-form-container .section-block {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 20px 22px;
        margin-bottom: 20px;
        box-shadow: var(--corp-shadow);
    }

    .corp-form-container .section-block:last-child {
        margin-bottom: 0;
    }

    .corp-form-container .section-header {
        font-size: 13px;
        font-weight: 700;
        color: var(--corp-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--corp-border);
        display: flex;
        align-items: center;
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
        font-size: 12px;
        font-weight: 600;
        color: var(--corp-text);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 6px;
        display: block;
    }

    .corp-form-container .form-control,
    .corp-form-container .form-select,
    .corp-form-container select.form-control {
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 13px;
        color: var(--corp-text);
        background-color: #ffffff;
        transition: border-color 0.15s, box-shadow 0.15s;
        height: auto;
        min-height: 36px;
    }

    .corp-form-container .form-control:focus,
    .corp-form-container .form-select:focus,
    .corp-form-container select.form-control:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .corp-form-container textarea.form-control {
        min-height: 76px;
        resize: vertical;
    }

    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly] {
        background-color: var(--corp-bg-disabled);
        color: var(--corp-text-muted);
        cursor: not-allowed;
        border-color: var(--corp-border);
    }

    /* Select2 corporate override */
    .corp-form-container .select2-container .select2-selection--single {
        height: 36px !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        background: #ffffff;
    }

    .corp-form-container .select2-container--focus .select2-selection--single,
    .corp-form-container .select2-container--open .select2-selection--single {
        border-color: var(--corp-primary) !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        color: var(--corp-text) !important;
        padding-left: 10px !important;
        font-size: 13px;
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }

    /* Checkbox */
    .corp-form-container .form-check {
        padding-left: 0;
        display: flex;
        align-items: center;
    }

    .corp-form-container .form-check-input {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        margin-top: 0;
        border: 1px solid var(--corp-border);
        cursor: pointer;
    }

    .corp-form-container .form-check-input:checked {
        background-color: var(--corp-primary);
        border-color: var(--corp-primary);
    }

    .corp-form-container .form-check-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--corp-text);
        text-transform: none;
        letter-spacing: 0;
        cursor: pointer;
        margin: 0;
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
        border-radius: 4px;
        border: 1px solid transparent;
        transition: all 0.15s;
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

    .corp-form-container .btn-default,
    .corp-form-container .btn-secondary {
        background: #ffffff;
        border-color: var(--corp-border);
        color: var(--corp-text);
    }

    .corp-form-container .btn-default:hover {
        background: var(--corp-bg-disabled);
        border-color: var(--corp-text-muted);
        color: var(--corp-text);
    }

    .corp-form-container .btn-block {
        width: 100%;
    }

    /* =========================================================
       GRID SATUAN TABLE
    ========================================================= */
    .corp-form-container #tableSatuan {
        margin-top: 16px;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        overflow: hidden;
        font-size: 13px;
    }

    .corp-form-container #tableSatuan thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 12px;
        border: none;
        text-align: left;
    }

    .corp-form-container #tableSatuan tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--corp-border);
        vertical-align: middle;
        color: var(--corp-text);
    }

    .corp-form-container #tableSatuan tbody tr:last-child td {
        border-bottom: none;
    }

    .corp-form-container #tableSatuan tbody tr:hover {
        background: var(--corp-bg-light);
    }

    .corp-form-container #tableSatuan .badge {
        font-size: 10.5px;
        font-weight: 600;
        padding: 5px 9px;
        border-radius: 3px;
        letter-spacing: 0.3px;
    }

    .corp-form-container #tableSatuan .bg-success {
        background: var(--corp-success) !important;
        color: #ffffff;
    }

    .corp-form-container #tableSatuan .bg-default {
        background: #e2e8f0 !important;
        color: var(--corp-text-muted);
    }

    /* =========================================================
       SMALL RESPONSIVE TWEAK
    ========================================================= */
    @media (max-width: 768px) {
        .corp-form-container .tab-content {
            padding: 14px;
        }
        .corp-form-container .section-block {
            padding: 16px 14px;
        }
        .corp-form-container .nav-tabs {
            padding: 0 12px;
        }
        .corp-form-container .nav-tabs .nav-link {
            padding: 12px 12px;
            font-size: 12px;
        }
    }
</style>


<!-- =========================================================
     HEADER
========================================================= -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px">
                        <i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?>
                    </div>
                    <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                    <?php foreach ($y as $y1) { ?>
                        <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>">
                                    <i class="fa <?php echo trim($y1->iconmenu); ?>"></i>
                                    <?php echo  trim($y1->namamenu); ?>
                                </a>
                            </li>
                        <?php } else { ?>
                            <li class="breadcrumb-item active">
                                <i class="fa <?php echo trim($y1->iconmenu); ?>"></i>
                                <?php echo trim($y1->namamenu); ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php echo $message;?>

<!-- =========================================================
     FORM CONTAINER
========================================================= -->
<div class="row corp-form-container">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fa fa-cube"></i>
                    <?php echo ($type == "DETAIL") ? 'Detail' : (($type == "EDIT") ? 'Edit' : 'Input'); ?>
                    Data Master Item
                </h3>
            </div>

            <form action="<?php echo base_url('#')?>" method="post" id="formInputItem" enctype="multipart/form-data" role="form">

                <div class="card-body">
                    <ul class="nav nav-tabs" id="customerTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab">
                                <i class="fa fa-cubes"></i> General
                            </a>
                        </li>
                        <li class="nav-item" id="tab-umum-wrapper">
                            <a class="nav-link" id="umum-tab" data-bs-toggle="tab" href="#umumtab" role="tab">
                                <i class="fa fa-info-circle"></i> Keterangan Umum
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="satuan-tab" data-bs-toggle="tab" href="#satuantab" role="tab">
                                <i class="fa fa-file-text"></i> Satuan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="account-tab" data-bs-toggle="tab" href="#accounttab" role="tab">
                                <i class="fa fa-book"></i> Account
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="customerTabContent">

                        <!-- ============================
                             TAB GENERAL
                        ============================= -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-cubes"></i> Informasi Barang
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="hidden" id="type" name="type" value="<?= $type ?>" autocomplete="off">
                                            <input type="hidden" id="id" name="id" value="<?= $id ?>" autocomplete="off">
                                            <label for="idbarang">Item ID</label>
                                            <input type="text" name="idbarang" class="form-control" id="idbarang" maxlength="20" placeholder="Item ID" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nmbarang">Item Name</label>
                                            <input type="text" name="nmbarang" class="form-control" id="nmbarang" maxlength="60" placeholder="Item Name" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="kdtax">Kode Tax</label>
                                            <input type="text" name="kdtax" class="form-control" id="kdtax" maxlength="60" placeholder="Kode Tax" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="originalno">Original No.</label>
                                            <input type="text" name="originalno" class="form-control" id="originalno" maxlength="60" placeholder="Original No." style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="description">Remark</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Enter Your Remark..." style="text-transform:uppercase;"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 30px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="discontinue" id="discontinue" value="YES">
                                            <label class="form-check-label" for="discontinue">Discontinue</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="margin-top: 30px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="issn" id="issn" value="YES">
                                            <label class="form-check-label" for="issn">With S/N</label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idgroup">Group</label>
                                            <select name="idgroup" id="idgroup" class="form-control" placeholder="Item Group" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idgolonganbarang">Golongan</label>
                                            <select name="idgolonganbarang" id="idgolonganbarang" class="form-control" placeholder="Golongan" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idjenisproduk">Jenis Produk</label>
                                            <select name="idjenisproduk" id="idjenisproduk" class="form-control" placeholder="Jenis Produk" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idkelompokbarang">Kelompok</label>
                                            <select name="idkelompokbarang" id="idkelompokbarang" class="form-control" placeholder="Kelompok Barang" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idprincipal">Principal</label>
                                            <select name="idprincipal" id="idprincipal" class="form-control" placeholder="Principal" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idbarcode">ID Barcode/SKU</label>
                                            <input type="text" name="idbarcode" class="form-control" id="idbarcode" placeholder="ID Barcode/SKU">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mfgdate">MFG Date</label>
                                            <input type="text" name="mfgdate" class="form-control" id="mfgdate" placeholder="MFG Date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expdate">Expired Date</label>
                                            <input type="text" name="expdate" class="form-control" id="expdate" placeholder="Expire Date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="maks_daystock">Max Save Life (Days)</label>
                                            <input type="text" name="maks_daystock" class="form-control jtsseparator ratakanan" id="maks_daystock" placeholder="Maks Save life (Days)">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="subunitenable">Sub Unit Enable</label>
                                            <select name="subunitenable" class="form-control" style="text-transform:uppercase;">
                                                <option value="NO">NO</option>
                                                <option value="YES">YES</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="setminstock">Set Minimum Stock</label>
                                            <select name="setminstock" class="form-control" style="text-transform:uppercase;">
                                                <option value="NO">NO</option>
                                                <option value="YES">YES</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="chold">Hold Item</label>
                                            <select name="chold" class="form-control" style="text-transform:uppercase;">
                                                <option value="NO">NO</option>
                                                <option value="YES">YES</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="minstock">Minimum Stock</label>
                                            <input type="text" name="minstock" class="form-control jtsseparator ratakanan" id="minstock" placeholder="Set Your Minimum Stock" maxlength="18">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================
                             TAB KETERANGAN UMUM
                        ============================= -->
                        <div class="tab-pane fade" id="umumtab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-info-circle"></i> Keterangan Umum
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="volume">Volume</label>
                                            <input type="text" name="volume" class="form-control jtsseparator ratakanan" id="volume" placeholder="Volume" maxlength="18">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="berat">Berat (Gram)</label>
                                            <input type="text" name="berat" class="form-control jtsseparator ratakanan" id="berat" placeholder="Berat" maxlength="18">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gw">Gross Weight</label>
                                            <input type="text" name="gw" class="form-control jtsseparator ratakanan" id="gw" placeholder="Gross Weight" maxlength="18">
                                        </div>
                                    </div>
                                </div>

                                <div class="section-header" style="margin-top: 20px;">
                                    <i class="fa fa-expand"></i> Size
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="psize">Panjang</label>
                                            <input type="text" name="psize" class="form-control jtsseparator ratakanan" placeholder="psize" maxlength="18">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lsize">Lebar</label>
                                            <input type="text" name="lsize" class="form-control jtsseparator ratakanan" placeholder="lsize" maxlength="18">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tsize">Tinggi</label>
                                            <input type="text" name="tsize" class="form-control jtsseparator ratakanan" placeholder="tsize" maxlength="18">
                                        </div>
                                    </div>
                                </div>

                                <div class="section-header" style="margin-top: 20px;">
                                    <i class="fa fa-map-marker"></i> Area
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="deflocation">Default Location</label>
                                            <select name="deflocation" id="deflocation" class="form-control" placeholder="Default Location" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="defarea">Default Area</label>
                                            <select name="defarea" id="defarea" class="form-control" placeholder="Default Area" style="text-transform:uppercase;"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lokasireff">Lokasi (Reff)</label>
                                            <input type="text" name="lokasireff" class="form-control" id="lokasireff" maxlength="60" placeholder="Lokasi (Reff)" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================
                             TAB SATUAN
                        ============================= -->
                        <div class="tab-pane fade" id="satuantab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-cube"></i> Satuan Utama
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="unit">Unit</label>
                                            <select name="unit" id="unit" class="form-control" style="text-transform:uppercase;"></select>

                                            <!-- 🆕 HIDDEN BACKUP — terisi otomatis saat EDIT/DETAIL -->
                                            <input type="hidden" name="unit_hidden" id="unit_hidden" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="subunit">Sub Unit (Satuan Kecil)</label>
                                            <select name="subunit" id="subunit" class="form-control" style="text-transform:uppercase;"></select>

                                            <!-- 🆕 HIDDEN BACKUP -->
                                            <input type="hidden" name="subunit_hidden" id="subunit_hidden" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="satuantax">Satuan Tax</label>
                                            <input type="text" name="satuantax" class="form-control" id="satuantax" placeholder="Satuan Tax" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-list"></i> Konversi Satuan
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="idunit">ID Unit Konversi <span class="text-danger">*</span></label>
                                            <input type="text" name="idunit" id="idunit" class="form-control" maxlength="10" placeholder="Masukkan Unit" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="idunit_tax">Satuan Tax</label>
                                            <input type="text" name="idunit_tax" id="idunit_tax" class="form-control" maxlength="10" placeholder="Satuan Tax" style="text-transform:uppercase;">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="basic_value">Basic Value</label>
                                            <input type="text" name="basic_value" id="basic_value" class="form-control jtsseparator ratakanan" value="1.00" placeholder="1.00">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="conv_value">Conversion Value</label>
                                            <input type="text" name="conv_value" id="conv_value" class="form-control jtsseparator ratakanan" value="1.00" placeholder="1.00">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="cdefault">Default</label>
                                            <select name="cdefault" id="cdefault" class="form-control">
                                                <option value="NO">NO</option>
                                                <option value="YES">YES</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="btnAddSatuan" title="Tambah Satuan">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- hidden ref: ganti nama agar tidak menimpa idbarang utama -->
                                <input type="hidden" name="idbarang_ref" id="idbarang_unit">

                                <div class="table-responsive mt-3">
                                    <table class="table" id="tableSatuan">
                                        <thead>
                                        <tr>
                                            <th style="width:50px;" class="text-center">No</th>
                                            <th>Unit</th>
                                            <th>Satuan Tax</th>
                                            <th class="text-end">Basic Value</th>
                                            <th class="text-end">Conversion Value</th>
                                            <th style="width:100px;" class="text-center">Default</th>
                                            <th style="width:100px;" class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- data masuk disini -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ============================
                             TAB ACCOUNT
                        ============================= -->
                        <div class="tab-pane fade" id="accounttab" role="tabpanel">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-book"></i> Account Mapping
                                </div>
                                <div class="row">
                                    <div class="col-md-3 field-ppersediaan">
                                        <div class="form-group">
                                            <label for="ppersediaan">Perkiraan Persediaan</label>
                                            <select name="ppersediaan" id="ppersediaan" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 field-psj">
                                        <div class="form-group">
                                            <label for="psj">Perkiraan SJ</label>
                                            <select name="psj" id="psj" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 field-salesakun">
                                        <div class="form-group">
                                            <label for="salesakun">Sales Akun</label>
                                            <select name="salesakun" id="salesakun" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 field-pcogs">
                                        <div class="form-group">
                                            <label for="pcogs">Perkiraan COGS</label>
                                            <select name="pcogs" id="pcogs" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 field-phpproduksi">
                                        <div class="form-group">
                                            <label for="phpproduksi">Perkiraan HP Produksi</label>
                                            <select name="phpproduksi" id="phpproduksi" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 field-pjasa">
                                        <div class="form-group">
                                            <label for="pjasa">Perkiraan Jasa</label>
                                            <select name="pjasa" id="pjasa" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 field-pwaste">
                                        <div class="form-group">
                                            <label for="pwaste">Perkiraan Waste</label>
                                            <select name="pwaste" id="pwaste" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.tab-content -->
                </div><!-- /.card-body -->

                <div class="card-footer">
                    <a href="<?= base_url('master/data/barang') ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button id="btnSave" type="button" onclick="saveInputItem();" class="btn btn-primary float-right">
                        <i class="fa fa-save"></i> Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     SCRIPT
========================================================= -->
<script type="application/javascript" src="<?= base_url('assets/pagejs/master/item.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $('#expdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear',
        });
        $('#expdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });
        $('#expdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#mfgdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: { format: 'DD-MM-YYYY' },
            cancelLabel: 'Clear',
        });
        $('#mfgdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });
        $('#mfgdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>