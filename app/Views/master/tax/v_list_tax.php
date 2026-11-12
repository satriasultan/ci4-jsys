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

<style>
    /* =========================================================
       v_list_tax.php - CORPORATE STYLE
       Referensi tampilan : master/currency/v_input_currency.php
       Catatan : hanya CSS & layout yang diubah.
                 id / class / function JS tetap dipertahankan.
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
        --corp-warning:       #f59e0b;
        --corp-danger:        #dc2626;
        --corp-radius:        6px;
        --corp-shadow:        0 1px 3px rgba(15, 23, 42, 0.06);

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

    .corp-form-container .card-header-title {
        margin: 0;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .corp-form-container .card-header-title i {
        margin-right: 8px;
        font-size: 13px;
    }

    .corp-form-container .card-tools {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .corp-form-container .card-body {
        padding: 20px;
        background: var(--corp-bg-light);
        overflow-x: auto;
    }

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

    .corp-form-container .dropdown-menu {
        border: 1px solid var(--corp-border);
        border-radius: var(--corp-radius);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
        padding: 6px;
        font-size: 12.5px;
    }

    .corp-form-container .dropdown-item {
        border-radius: 4px;
        padding: 8px 12px;
        font-weight: 600;
        color: var(--corp-text);
    }

    .corp-form-container .dropdown-item i {
        margin-right: 8px;
        color: var(--corp-primary);
    }

    .corp-form-container .dropdown-item:hover {
        background: var(--corp-primary-light);
        color: var(--corp-primary);
    }

    /* ---------- TABLE ---------- */
    .corp-form-container #t_tax {
        width: 100% !important;
        margin-bottom: 0;
        font-size: 12.5px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .corp-form-container #t_tax thead th {
        background: var(--corp-primary) !important;
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        text-align: center;
        vertical-align: middle;
        padding: 11px 10px;
        border: none !important;
        white-space: nowrap;
    }

    .corp-form-container #t_tax tbody td {
        padding: 8px 10px;
        vertical-align: middle;
        color: var(--corp-text);
        border: 1px solid var(--corp-border) !important;
        background: #ffffff;
    }

    .corp-form-container #t_tax tbody tr:nth-child(even) td {
        background: var(--corp-bg-light);
    }

    .corp-form-container #t_tax tbody tr:hover td {
        background: var(--corp-primary-light);
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

    .corp-form-container .dataTables_wrapper .dt-buttons {
        margin-bottom: 8px;
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

    .corp-form-container.modal .modal-body {
        background: var(--corp-bg-light);
        padding: 20px;
    }

    .corp-form-container.modal .modal-footer {
        background: #ffffff !important;
        border-top: 1px solid var(--corp-border) !important;
        padding: 12px 20px;
    }

    .corp-form-container.modal label {
        display: block;
        margin-bottom: 5px;
        color: var(--corp-text-muted);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .corp-form-container.modal .form-control {
        height: 36px;
        padding: 6px 10px;
        font-size: 13px;
        color: var(--corp-text);
        background: #ffffff;
        border: 1px solid var(--corp-border);
        border-radius: 4px;
    }

    .corp-form-container.modal .form-control:focus {
        border-color: var(--corp-primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }
</style>


<div class="row corp-form-container">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-header-title"><i class="fa fa-percent"></i> Data Master Tax</h3>
                <div class="card-tools">
                    <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                        <a class="btn btn-success btn-sm" href="<?= base_url('master/data/input_tax') ?>">
                            <i class="fa fa-plus"></i> Input
                        </a>
                    <?php endif; ?>
                    <div class="btn-group">
                        <button type="button"
                                class="btn btn-primary btn-sm dropdown-toggle dropdown-icon"
                                data-bs-toggle="dropdown">
                            <i class="fa fa-bars"></i> Menu
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                                <a class="dropdown-item" href="<?= base_url('master/data/input_tax') ?>">
                                    <i class="fa fa-plus"></i> Input
                                </a>
                            <?php endif; ?>
                            <a class="dropdown-item" href="#" onclick="reload_table()">
                                <i class="fa fa-refresh"></i> Reload
                            </a>
                        </div>
                    </div>
                </div>
            </div><!-- /.card-header -->

            <div class="card-body">
                <table id="t_tax" class="table">
                    <thead class="text-center">
                        <tr>
                            <th style="min-width:40px;">No.</th>
                            <th style="min-width:60px;">Action</th>
                            <th style="min-width:110px;">ID Tax</th>
                            <th style="min-width:220px;">Nama Tax</th>
                            <th style="min-width:90px;">Hold</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</div>


<!--Modal untuk Filter-->
<div class="modal fade corp-form-container" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Tax</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-form">Tanggal Dokumen</label>
                                <input type="text" class="form-control input-sm tglrange" id="tglrange"
                                       name="tglrange" value="" data-date-format="dd-mm-yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-form">Status</label>
                                <select class="form-control input-sm" id="status_filter" name="status_filter">
                                    <option value="ALL">Semua Status</option>
                                    <option value="I">DRAFT USER</option>
                                    <option value="C">CLOSE</option>
                                    <option value="O">OPEN</option>
                                    <option value="R">BATAL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-reset" class="btn btn-default">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                    <button type="button" id="btn-filter" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<script type="application/javascript" src="<?= base_url('assets/pagejs/master/tax/tax.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        // Bootstrap 5 tooltip (tanpa plugin jQuery)
        if (window.bootstrap && bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        }

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
    });
</script>

