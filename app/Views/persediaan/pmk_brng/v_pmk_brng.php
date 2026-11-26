<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> Pemakaian Barang</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
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

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('periode_error')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa fa-lock"></i>
        <?= esc(session()->getFlashdata('periode_error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php echo $message;?>
<?php echo $showUnfinish; ?>
<style>
    .text-wrap{
        white-space:normal;
    }
    .width-90{
        width:90px;
    }
    .width-150{
        width:150px;
    }
    .width-200{
        width:200px;
    }

    /* ===============================
   TABLE HEADER STYLE - CORPORATE
================================ */

    #tPmkBrg {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    /* HEADER */
    #tPmkBrg thead th {
        background: linear-gradient(135deg, #1f2937, #374151);
        color: #ffffff;
        font-weight: 600;
        text-align: center;
        padding: 10px 8px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* Rounded corner atas */
    #tPmkBrg thead th:first-child {
        border-top-left-radius: 8px;
    }

    #tPmkBrg thead th:last-child {
        border-top-right-radius: 8px;
    }

    /* Zebra row */
    #tPmkBrg tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    /* Hover row */
    #tPmkBrg tbody tr:hover {
        background-color: #e9f2ff;
        transition: 0.2s ease-in-out;
    }

    /* Body cell dasar */
    #tPmkBrg tbody td {
        vertical-align: middle;
        padding: 9px 10px;
        border-color: #e5e7eb;
        color: #374151;
    }

    /* Center kolom: No, Action, Document, Cabang, Bagian, Docdate, User, Status */
    #tPmkBrg tbody td:nth-child(1),
    #tPmkBrg tbody td:nth-child(2),
    #tPmkBrg tbody td:nth-child(3),
    #tPmkBrg tbody td:nth-child(4),
    #tPmkBrg tbody td:nth-child(5),
    #tPmkBrg tbody td:nth-child(6),
    #tPmkBrg tbody td:nth-child(8),
    #tPmkBrg tbody td:nth-child(9) {
        text-align: center;
    }

    /* Description rata kiri + wrap */
    #tPmkBrg tbody td:nth-child(7) {
        text-align: left;
        white-space: normal;
        min-width: 180px;
    }

    /* STATUS BADGE */
    #tPmkBrg .badge-status {
        display: inline-block;
        min-width: 80px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        border-radius: 30px;
    }

    /* ===============================
       DATATABLE CONTROLS (bs4 integration)
    ================================ */
    .card-body .dataTables_wrapper { padding: 2px 4px 0; }

    .card-body .dataTables_wrapper .dt-buttons { margin-bottom: 12px; }

    .card-body .dataTables_wrapper .dt-buttons .dt-button.btn {
        background: #1f2937;
        border: none;
        color: #fff;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 14px;
        margin-right: 6px;
        box-shadow: none;
    }

    .card-body .dataTables_wrapper .dt-buttons .dt-button.btn:hover {
        background: #374151;
    }

    .card-body .dataTables_wrapper .dataTables_filter label {
        font-weight: 600;
        color: #374151;
        font-size: 13px;
    }

    .card-body .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 5px 10px;
        outline: none;
        transition: .2s ease-in-out;
    }

    .card-body .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #1f2937;
        box-shadow: 0 0 0 3px rgba(31,41,55,.12);
    }

    .card-body .dataTables_wrapper .dataTables_info {
        color: #6b7280;
        font-size: 13px;
        padding-top: 12px;
    }

    .card-body .dataTables_wrapper .dataTables_paginate {
        padding-top: 8px;
    }

    .card-body .dataTables_wrapper .dataTables_paginate .page-link {
        border: 1px solid #e5e7eb;
        color: #374151;
        border-radius: 8px !important;
        margin: 0 2px;
        padding: 5px 11px;
        font-size: 13px;
    }

    .card-body .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
        background: linear-gradient(135deg, #1f2937, #374151);
        border-color: #1f2937;
        color: #fff;
    }

    .card-body .dataTables_wrapper .dataTables_paginate .page-link:hover {
        background: #e9f2ff;
        color: #1f2937;
    }

    .card-body .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
        color: #adb5bd;
        background: #f8f9fa;
    }

/* header */

    /* ===============================
   CORPORATE GREY CARD STYLE
================================ */

    .card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    /* Header Grey Gradient */
    .card-header {
        background: linear-gradient(135deg, #4b5563, #6b7280);
        border-bottom: none;
        padding: 12px 18px;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }

    /* Dropdown Button Grey */
    .card-header .btn-primary {
        background: #e5e7eb;
        color: #374151;
        border: none;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
    }

    .card-header .btn-primary:hover {
        background: #d1d5db;
        transform: translateY(-2px);
    }

    /* Dropdown Menu */
    .dropdown-menu {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        padding: 6px 0;
    }

    /* Dropdown Item */
    .dropdown-item {
        padding: 8px 16px;
        font-size: 14px;
        color: #374151;
        transition: 0.2s ease-in-out;
    }

    .dropdown-item:hover {
        background: #f3f4f6;
        color: #111827;
        padding-left: 20px;
    }

    /* Icon spacing */
    .dropdown-item i {
        width: 20px;
        color: #6b7280;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"><?php echo 'Menu'; ?>
                    </button>
                    <div class="dropdown-menu">
                        <?php if (isset($dtl_akses['a_input']) && trim($dtl_akses['a_input']) === 't'): ?>
                            <a class="dropdown-item" href="<?= base_url('persediaan/trans/add_pmk_brng_mst') ?>"
                                onclick="return guardPeriodeTutup(event)">
                                <i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <?php endif; ?>
                        <!-- <a class="dropdown-item disabled" data-bs-toggle="modal" data-bs-target="#filter"  href="#"><i class="fa fa-filter"></i><?php echo '   Filter'; ?></a> -->
                        <a class="dropdown-item" href="#"  onclick="reloadPmkBrg()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body table-responsive" style='overflow-x:scroll;'>
                <table id="tPmkBrg" class="table table-bordered table-striped" style="width:100%;" cellspacing="0">
                    <thead class="text-center">
                    <tr>
                        <th style="min-width:10px; text-align:center; vertical-align:middle;">No.</th>
                        <th style="min-width:10px; text-align:center; vertical-align:middle;">Action</th>
                        <th style="min-width:130px; text-align:center; vertical-align:middle;">Docno</th>
                        <th style="min-width:100px; text-align:center; vertical-align:middle;">Tanggal</th>
                        <th style="min-width:150px; text-align:center; vertical-align:middle;">Cabang</th>
                        <th style="min-width:170px; text-align:center; vertical-align:middle;">Bagian / Cost Center</th>
                        <th style="min-width:120px; text-align:center; vertical-align:middle;">Referensi</th>
                        <th style="min-width:140px; text-align:center; vertical-align:middle;">Total</th>
                        <th style="min-width:90px; text-align:center; vertical-align:middle;">Status</th>
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
<div class="modal fade" id="filter">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-filter">
                <div class="modal-header">
                    <h4 class="modal-title">Filtering Data</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggalpo">Tanggal Kedatangan</label>
                        <input type="text" class="form-control tglrange" id="tglrange"  name="tglrange" data-date-format="dd-mm-yyyy" required placeholder="Entry LPB Date" required>
                    </div>
                    <div class="form-group">
                        <label for="itembarang">Item Barang</label>
                        <select name="idbarang_filter" id="idbarang_filter" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suppliername">Nama Supplier</label>
                        <select name="namasupplier" id="namasupplier" class="form-control" placeholder="Pilih Item Barang">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_filter">Status</label>
                        <select name="status_filter" id="status_filter" class="form-control" placeholder="Pilih Status Filter">
                            <option value=""> Semua Status</option>
                            <option value="A"> Outstanding</option>
                            <option value="S"> Outstanding Sebagian</option>
                            <option value="P"> Diterima Penuh</option>
                        </select>

                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-reset-tx" class="btn btn-default">Reset</button>
                    <button type="button" id="btn-filter-tx" class="btn btn-primary">Filter</button>

                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->






<!-- =========================================================
     MODAL LAPORAN JURNAL TRANSAKSI PEMAKAIAN BARANG
========================================================= -->
<div class="modal fade" id="modalLaporanJurnalpmk" tabindex="-1"
     aria-labelledby="modalLaporanJurnalpmkLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalLaporanJurnalpmkLabel">
                    <i class="fa fa-book"></i> Laporan Jurnal Transaksi Pemakaian Barang
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="pmk-journal-info">
                    No. Transaksi : <span class="pmk-journal-docno">-</span>
                </div>

                <div class="table-responsive">
                    <table id="tableLaporanJurnalpmk" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th width="40">No</th>
                            <th width="95">Tanggal</th>
                            <th width="160">No. Jurnal</th>
                            <th>Keterangan / Akun</th>
                            <th class="text-end" width="130">Debet</th>
                            <th class="text-end" width="130">Kredit</th>
                            <th width="180">Referensi</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><b>TOTAL</b></td>
                            <td class="text-end" id="jurnalTotalDebet">0.00</td>
                            <td class="text-end" id="jurnalTotalKredit">0.00</td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>

        </div>
    </div>
</div>

<?php
/* cache-busting JS khusus halaman list PMK */
$pmkListJsRel = 'assets/pagejs/persediaan/pmk_brng/pmk_brng_list.js';
$pmkListJsAbs = (defined('FCPATH') ? FCPATH : '') . $pmkListJsRel;
$pmkListJsVer = is_file($pmkListJsAbs) ? filemtime($pmkListJsAbs) : date('YmdHis');
?>
<script type="application/javascript" src="<?= base_url('assets/pagejs/persediaan/pmk_brng/pmk_brng.js') ?>"></script>
<script type="application/javascript" src="<?= base_url($pmkListJsRel) ?>?v=<?= $pmkListJsVer ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $("#example2").dataTable();
        $("#example4").dataTable();
        //datemask
        //$("#datemaskinput").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //$("#datemaskinput").daterangepicker();
        //Date picker

        $('#dateinputx').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-M-YYYY'
            },
            cancelLabel: 'Clear',
        });
        $('#dateinputx').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-M-YYYY'));
        });

        $('#dateinputx').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>
<script type="application/javascript">
    $(".tglrange").daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $("#status_filter").select2();

    $(".tglrange").on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    });

    $(".tglrange").on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
</script>