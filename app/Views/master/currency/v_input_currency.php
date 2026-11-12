<style>
    /* =========================================================
       v_input_currency.php — CORPORATE STYLE
       Referensi tampilan : master/item/v_input.php &
                            master/supplier/v_input_suppliers.php
       Catatan : hanya CSS & layout yang diubah.
                 name / id / function / business logic tidak diubah.
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
        --corp-warning:       #ffc107;
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
        background: var(--corp-primary);
        color: #ffffff;
        padding: 14px 22px;
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
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .corp-form-container .card-header h3 i {
        margin-right: 8px;
        font-size: 14px;
    }

    .corp-form-container .card-body {
        padding: 0;
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

    /* ---------- TABS ---------- */
    .corp-form-container .nav-tabs {
        background: #ffffff;
        border-bottom: 2px solid var(--corp-border);
        padding: 0 22px;
        margin: 0;
    }

    .corp-form-container .nav-tabs .nav-item {
        margin-bottom: -2px;
    }

    .corp-form-container .nav-tabs .nav-title {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 18px 14px 0;
        margin-right: 12px;
        border-right: 1px solid var(--corp-border);
        color: var(--corp-primary);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        list-style: none;
    }

    .corp-form-container .nav-tabs .nav-title i {
        font-size: 13px;
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

    .corp-form-container .nav-tabs .nav-link.disabled {
        color: #b9c2ce;
        pointer-events: none;
    }

    /* ---------- TAB CONTENT ---------- */
    .corp-form-container .tab-content {
        padding: 22px;
        margin-top: 0;
        background: var(--corp-bg-light);
    }

    .corp-form-container .tab-pane > .card-body {
        padding: 0;
        background: transparent;
    }


    /* ---------- SECTION BLOCK ---------- */
    .corp-form-container .section-block {
        background: var(--corp-bg-section);
        border: 1px solid var(--corp-border);
        border-left: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        padding: 20px 22px;
        margin-bottom: 16px;
        box-shadow: var(--corp-shadow);
        transform: none;
        transition: none;
        will-change: auto;
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
        gap: 8px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--corp-border);
        color: var(--corp-primary);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .corp-form-container .section-header i {
        margin-right: 0;
        font-size: 13px;
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
    .corp-form-container .form-select,
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
    .corp-form-container .form-select:focus,
    .corp-form-container select.form-control:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .corp-form-container .form-control:disabled,
    .corp-form-container .form-control[readonly],
    .corp-form-container select.form-control:disabled {
        background: var(--corp-bg-disabled) !important;
        border-color: var(--corp-border);
        color: var(--corp-text-muted);
        opacity: 1;
    }

    .corp-form-container .ratakanan {
        text-align: right;
    }

    .corp-form-container .is-invalid {
        border-color: var(--corp-danger) !important;
    }

    .corp-form-container .invalid-feedback {
        display: block;
        color: var(--corp-danger);
        font-size: 11.5px;
    }

    /* ---------- SELECT2 (COA) ---------- */
    .corp-form-container .select2-container {
        width: 100% !important;
    }

    .corp-form-container .select2-container .select2-selection--single {
        height: 36px !important;
        border: 1px solid var(--corp-border) !important;
        border-radius: 4px !important;
        background: #ffffff !important;
    }

    .corp-form-container .select2-container--focus .select2-selection--single,
    .corp-form-container .select2-container--open .select2-selection--single {
        border-color: var(--corp-primary) !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none !important;
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        padding-left: 10px !important;
        padding-right: 26px !important;
        color: var(--corp-text) !important;
        font-size: 13px;
    }

    .corp-form-container .select2-container .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }

    .corp-form-container .select2-container--disabled .select2-selection--single {
        background: var(--corp-bg-disabled) !important;
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


    /* ---------- TABLE EXCHANGE RATE ---------- */
    .corp-form-container .table-wrapper {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        overflow-x: auto;
        background: #ffffff;
    }

    .corp-form-container #t_exchange {
        margin-bottom: 0;
        font-size: 13px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .corp-form-container #t_exchange thead td {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        text-align: center;
        vertical-align: middle;
        padding: 11px 12px;
        border: none !important;
    }

    .corp-form-container #t_exchange thead td:nth-child(2) {
        min-width: 200px;
    }

    .corp-form-container #t_exchange thead td:nth-child(3) {
        min-width: 140px;
    }

    .corp-form-container #t_exchange tbody td {
        padding: 8px 10px;
        vertical-align: middle;
        border-color: var(--corp-border);
        background: #ffffff;
    }

    .corp-form-container #t_exchange tbody tr:nth-child(even) td {
        background: var(--corp-bg-light);
    }

    .corp-form-container #t_exchange tbody tr:hover td {
        background: var(--corp-primary-light);
    }

    .corp-form-container #t_exchange input[type="text"] {
        width: 100%;
        margin: 0;
        padding: 6px 9px;
        font-size: 13px;
        color: var(--corp-text);
        text-align: left;
        background: var(--corp-bg-disabled);
        border: 1px solid transparent;
        border-radius: 4px;
        transition: all 0.15s ease;
    }

    .corp-form-container #t_exchange input[type="text"]:focus {
        border-color: var(--corp-primary);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .corp-form-container #t_exchange input[type="text"].jtsseparator,
    .corp-form-container #t_exchange input[type="text"].nilai-exchange {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .corp-form-container #t_exchange input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin: 0;
        cursor: pointer;
        accent-color: var(--corp-primary);
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

    .corp-form-container .dataTables_wrapper .dataTables_processing {
        top: 0;
        background: #ffffff;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
        font-size: 12.5px;
        color: var(--corp-text-muted);
    }

    /* ---------- BUTTONS ---------- */
    .corp-form-container .btn {
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 9px 18px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: none;
        transition: all 0.15s;
    }

    .corp-form-container .btn i {
        margin-right: 6px;
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

    .corp-form-container .btn-sm {
        padding: 6px 12px;
        font-size: 11.5px;
    }

    .corp-form-container .btn.btn-aksi {
        min-width: 180px;
    }

    .corp-form-container .input-group > .btn {
        border-radius: 4px;
    }

    .corp-form-container hr {
        border-top: 1px solid var(--corp-border);
        margin: 12px 0;
    }

    /* ---------- TOOLBAR EXCHANGE RATE ---------- */
    .corp-form-container .exchange-toolbar {
        margin-bottom: 14px;
    }

    .corp-form-container .exchange-toolbar .btn + .btn {
        margin-left: 8px;
    }

    .corp-form-container #t_exchange .exchange-date {
        display: inline-block;
        min-width: 150px;
        font-variant-numeric: tabular-nums;
    }

    .corp-form-container #t_exchange .exchange-nilai {
        display: inline-block;
        min-width: 110px;
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-weight: 600;
    }

    .corp-form-container #t_exchange .btn-edit-exchange {
        padding: 3px 10px;
        font-size: 12px;
        line-height: 1.4;
        border-radius: 4px;
    }

    /* ---------- MODAL POPUP EXCHANGE RATE ---------- */
    #modal_exchange .modal-content {
        border: 0;
        border-radius: 6px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.25);
    }

    #modal_exchange .modal-header {
        padding: 13px 18px;
        background: #1e3a5f;
        color: #ffffff;
        border-bottom: 0;
        border-radius: 6px 6px 0 0;
    }

    #modal_exchange .modal-title {
        font-size: 14.5px;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    #modal_exchange .modal-header .close {
        color: #ffffff;
        opacity: 0.85;
        font-size: 20px;
        line-height: 1;
        background: transparent;
        border: 0;
        padding: 0 2px;
    }

    #modal_exchange .modal-body {
        padding: 18px;
    }

    #modal_exchange .modal-footer {
        padding: 12px 18px;
        border-top: 1px solid #e2e8f0;
    }

    #modal_exchange .form-group label {
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
    }

    #modal_exchange .form-control {
        height: 36px;
        font-size: 13px;
        border: 1px solid #d5dde7;
        border-radius: 4px;
    }

    #modal_exchange .form-control:focus {
        border-color: #1e3a5f;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }

    #modal_exchange #md_nilai {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    #modal_exchange #md_exchange_error {
        display: none;
        margin-top: 4px;
        font-size: 12.5px;
        color: #b91c1c;
    }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <?php if ($typeform !== 'DETAIL' && $typeform !== 'UPDATE'): ?>
                    <h1 class="m-0"> Input Currency Data</h1>
                <?php elseif ($typeform === 'UPDATE'): ?>
                    <h1 class="m-0"> Update Currency Data</h1>
                <?php else: ?>
                    <h1 class="m-0"> Detail Currency Data</h1>
                <?php endif; ?>
                    
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php //echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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


<div id="form-container" name="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">
</div>
<div id="form-status" name="<?php echo $status; ?>" data-typeform="<?php echo $status; ?>">
</div>

<?php echo $message;?>

<div class="row corp-form-container">
    <div class="col-md-12">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header">
                <h3><i class="fa fa-usd"></i> Data Currency</h3>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                    <li class="nav-title"><i class="fa fa-money"></i> Currency</li>
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-two-home-tab" data-bs-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Exchange Rate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-two-profile-tab" data-bs-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" <?= ($typeform == 'INPUT') ? 'disabled' : '' ?> aria-selected="false">Perkiraan Pembelian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" <?= ($typeform == 'INPUT') ? 'disabled' : '' ?> aria-selected="false">Perkiraan Penjualan</a>
                    </li>
                </ul>
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-usd"></i> Informasi Umum Currency
                            </div>
                            <div class="row">
                                <input type="hidden" name="typeform" value="<?php echo $typeform; ?>" data-typeform="<?php echo $typeform; ?>">

                                <input type="hidden" name="idcurrency" id="idcurrency" value="<?= ($typeform == 'UPDATE' || $typeform == 'DETAIL') ? $currency['id'] : '' ?>">
                                
                                <div class="col-md-3">
                                    <label>Currency Code</label>
                                    <input type="text" name="currcode" maxlength="3" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> style="text-transform: uppercase;" required></input>
                                </div>
                                <div class="col-md-9">
                                    <label>Currency Name</label>
                                    <input type="text" name="currname" maxlength="50" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> style="text-transform: uppercase;" required>
                                </div>
                                <?php if(($typeform == 'INPUT')) {?>
                                <div class="col-md-12"><hr></div>
                                <div class="col-md-12">
                                    <button id="btnSave" onclick="submitCurrency();" class="btn btn-primary float-right">Save Master</button>
                                </div>
                                <?php }?>
                            </div>
                        </div>

                        <div class="section-block">
                            <div class="section-header">
                                <i class="fa fa-exchange"></i> Exchange Rate
                            </div>
                            <?php if($typeform == 'UPDATE'){?>
                            <div class="row exchange-toolbar">
                                <div class="col-sm-6">
                                    <button type="button" id="btn_insert_" onclick="insertNewExchange()" class="btn btn-success"><i class="fa fa-plus"></i> Insert Exchange Rate</button>
                                </div>
                                <div class="col-sm-6">
                                    <div class="float-right">
                                        <button type="button" id="delete_exchange" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="frm-examplee" action="#" method="POST">
                                        <div class="table-wrapper" style="overflow-x: auto;">
                                            <table id="t_exchange" class="table table-bordered table-striped"  style="text-wrap:nowrap; text-align: center;"  cellspacing="0" width="100%">
                                                <thead>
                                                <tr>
                                                    <td>Act</td>
                                                    <td>Exchange Date</td>
                                                    <td>Exchange Rate</td>
                                                    <td>Action</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="card-body">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-cart-arrow-down"></i> Perkiraan Pembelian
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Hutang</label>
                                            <select name="phutang" id="phutang" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Uang Muka</label>
                                            <select name="pum" id="pum" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Bonus</label>
                                            <select name="pbonus" id="pbonus" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Hutang Antar Cabang</label>
                                            <select name="hutangac" id="hutangac" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Hutang Biaya 1</label>
                                            <select name="hutangbiaya1" id="hutangbiaya1" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Hutang Biaya 1</label>
                                            <select name="hutangbiaya2" id="hutangbiaya2" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                        <div class="card-body">
                            <div class="section-block">
                                <div class="section-header">
                                    <i class="fa fa-bar-chart"></i> Perkiraan Penjualan
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Piutang</label>
                                            <select name="ppiutang" id="ppiutang" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Uang Muka</label>
                                            <select name="pumjual" id="pumjual" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Pendapatan</label>
                                            <select name="ppendapatan" id="ppendapatan" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Retur</label>
                                            <select name="pretur" id="pretur" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Disc</label>
                                            <select name="pdisc" id="pdisc" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Bonus</label>
                                            <select name="pbonusjual" id="pbonusjual" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Tunai</label>
                                            <select name="ptunai" id="ptunai" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Piutang Antar Cabang</label>
                                            <select name="piutangac" id="piutangac" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Pendapatan Antar Cabang</label>
                                            <select name="pendapatanac" id="pendapatanac" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Perkiraan Pendapatan Service</label>
                                            <select name="pps" id="pps" class="form-control" <?= ($typeform == 'DETAIL' ? 'disabled' : '') ?> required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= $back_url; ?>" class="btn btn-default float-left">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <?php if ($typeform == 'UPDATE'): ?>
                        <button id="btnSave" onclick="saveFinalCurrency();" class="btn btn-primary btn-aksi float-right">Save Final Currency</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- </form> -->
<!-- Bootstrap modal -->
<!-- ===================== MODAL POPUP EXCHANGE RATE ===================== -->
<div class="modal fade" id="modal_exchange" tabindex="-1" role="dialog" aria-labelledby="modal_exchange_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_exchange_label"><i class="fa fa-exchange"></i> Exchange Rate</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="md_exchange_type" value="INSERT">
                <input type="hidden" id="md_exchange_id" value="">
                <div class="form-group">
                    <label for="md_exchangedate">Exchange Date</label>
                    <input type="text" id="md_exchangedate" name="md_exchangedate" class="form-control" autocomplete="off" placeholder="DD-MM-YYYY HH:mm:ss">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="md_nilai">Exchange Rate (gunakan titik untuk pemisah desimal)</label>
                    <input type="text" id="md_nilai" name="md_nilai" inputmode="decimal" class="form-control nilai-exchange" autocomplete="off" placeholder="0.0000">
                </div>
                <div id="md_exchange_error"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn btn-primary" id="md_exchange_save" onclick="saveExchangeRate();"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>




<script type="application/javascript" src="<?= base_url('assets/pagejs/master/currency/currency.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".sidebar-mini").addClass( "sidebar-collapse" );
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker
        // $('#exchangedate').daterangepicker({
        //     autoUpdateInput: false,
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     locale: {
        //         format: 'DD-MM-YYYY'
        //     },
        //     cancelLabel: 'Clear',
        // });
        // $('#exchangedate').on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('DD-MM-YYYY'));
        //     $('#formSubmitCapex').bootstrapValidator('updateStatus', 'exchangedate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'exchangedate');
        // });
        // $('#exchangedate').on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        // });
        /* DATE TIME PICKER kolom exchange date :
           - #md_exchangedate  => input di modal popup Exchange Rate (Insert / Update)
           - [id^="exchangedate_"] => input lama (bila masih dipakai)
           format DD-MM-YYYY HH:mm:ss, sehingga bisa memilih tanggal + jam. */
        function initExchangeDateTimePicker() {
            // Temukan input exchange date (modal popup & input lama)
            $('[id^="exchangedate_"], #md_exchangedate').each(function () {
                const $input = $(this);

                // Pastikan tidak menginisialisasi 2x
                if ($input.data('daterangepicker')) return;

                $input.daterangepicker({
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    showDropdowns: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    timePickerSeconds: true,
                    timePickerIncrement: 1,
                    locale: {
                        format: 'DD-MM-YYYY HH:mm:ss'
                    },
                    cancelLabel: 'Clear'
                });

                $input.on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD-MM-YYYY HH:mm:ss'));
                });

                $input.on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            });
        }

        $('#t_exchange').on('draw.dt', function () {
            initExchangeDateTimePicker();
        });

        // inisialisasi awal, karena DataTable sudah di-draw oleh currency.js
        initExchangeDateTimePicker();



        /* ESTIMASI DATE */
        $('#estdate').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#estdate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            $('#formInputTransfers').bootstrapValidator('updateStatus', 'estdate', 'NOT_VALIDATED').bootstrapValidator('validateField', 'docdate');
        });
        $('#estdate').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        document.querySelectorAll(".tooltip-icon").forEach(function (tooltipIcon) {
            var content = tooltipIcon.getAttribute("data-tooltip-content");

            if (content) {
            new bootstrap.Tooltip(tooltipIcon, {
                title: content,
                sanitize: false, // Mengizinkan HTML di dalam tooltip
                placement: "right",
                customClass: tooltipIcon.closest('.col-sm-3')?.querySelector('#objcapex') ? 'tooltip-large' : 'tooltip-small'
            });
            }
        });
    });

</script>
