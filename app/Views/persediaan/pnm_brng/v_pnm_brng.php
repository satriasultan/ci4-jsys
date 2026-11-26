<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                <h1 class="m-0"> Penerimaan Barang </h1>
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
    /* =========================================================
       PNM BARANG (LIST) - SELARAS DENGAN HALAMAN LPB
       Referensi layout: purchase/trans/lpb (read-only)
    ========================================================= */

    /* Tabel utama: lebar penuh, tinggi baris rapi */
    #tpnmBrg {
        width: 100% !important;
        margin-bottom: 0;
        font-size: 13px;
    }

    /* Header kolom sejajar & rapi (LPB: text-center + vertical-align middle) */
    #tpnmBrg thead th {
        vertical-align: middle !important;
        white-space: nowrap;
        font-weight: 600;
    }

    #tpnmBrg tbody td {
        vertical-align: middle;
    }

    /* Tombol aksi (dropdown) selalu tepat di tengah kolom Action */
    #tpnmBrg tbody td .dropdown {
        display: flex;
        justify-content: center;
    }

    #tpnmBrg tbody td .dropdown > .btn {
        padding: 3px 9px;
        line-height: 1.2;
    }

    /* Badge status (bootstrap badge, sama seperti LPB) */
    #tpnmBrg .badge {
        padding: 5px 6px;
        font-weight: 600;
        letter-spacing: .2px;
    }

    /* Dropdown menu aksi per baris */
    #tpnmBrg .dropdown-menu {
        min-width: 250px;
    }

    #tpnmBrg .dropdown-menu .dropdown-item {
        font-size: 13px;
        white-space: normal;
    }

    /* Toolbar (search + buttons) & pagination DataTables - dom: Bfrtip */
    #tpnmBrg_wrapper .dataTables_filter label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    #tpnmBrg_wrapper .dataTables_filter input {
        height: 32px;
        padding: 4px 10px;
        margin-left: 6px;
        font-size: 12.5px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        outline: none;
    }

    #tpnmBrg_wrapper .dt-buttons .btn {
        font-size: 12px;
    }

    #tpnmBrg_wrapper .pagination {
        margin: 0;
    }

    /* =========================================================
       MODAL LAPORAN JURNAL TRANSAKSI (tampilan standar seperti LPB)
    ========================================================= */
    #modalLaporanJurnalPnm .pnm-journal-info {
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    #tableLaporanJurnalPnm {
        width: 100% !important;
        margin-bottom: 0;
        font-size: 12.5px;
    }

    #tableLaporanJurnalPnm thead th {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #tableLaporanJurnalPnm tbody td,
    #tableLaporanJurnalPnm tfoot td {
        vertical-align: middle;
    }

    #tableLaporanJurnalPnm tfoot td {
        font-weight: 700;
        background: #f4f6f9 !important;
        font-variant-numeric: tabular-nums;
    }

    #tableLaporanJurnalPnm .jnum,
    #tableLaporanJurnalPnm .jcoa {
        font-family: Consolas, 'Courier New', monospace;
    }

    #tableLaporanJurnalPnm .jcc,
    #tableLaporanJurnalPnm .jref {
        font-size: 11px;
        color: #6c757d;
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
                            <a class="dropdown-item" href="<?= base_url('persediaan/trans/add_pnm_brng_mst') ?>"
                            onclick="return guardPeriodeTutup(event)">
                            <i class="fa fa-plus"></i><?php echo '   Input'; ?> </a>
                        <?php endif; ?>
                        <!-- Filter (modal sudah tersedia, sama seperti struktur menu LPB) -->
                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filter" href="#">
                            <i class="fa fa-filter"></i><?php echo '    Filter'; ?> </a>
                        <a class="dropdown-item" href="#"  onclick="reloadpnmBrg()"><i class="fa fa-refresh"></i><?php echo '    Reload'; ?> </a>
                    </div>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" style='overflow-x:auto;'>
                            <table id="tpnmBrg" class="table table-bordered table-striped" style="width:100%;" cellspacing="0">
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
                        </div>
                    </div>
                </div>
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
     MODAL LAPORAN JURNAL TRANSAKSI PENERIMAAN BARANG
========================================================= -->
<div class="modal fade" id="modalLaporanJurnalPnm" tabindex="-1"
     aria-labelledby="modalLaporanJurnalPnmLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalLaporanJurnalPnmLabel">
                    <i class="fa fa-book"></i> Laporan Jurnal Transaksi Penerimaan Barang
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="pnm-journal-info">
                    No. Transaksi : <span class="pnm-journal-docno">-</span>
                </div>

                <div class="table-responsive">
                    <table id="tableLaporanJurnalPnm" class="table table-bordered table-striped">
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
/* cache-busting JS khusus halaman list */
$pnmListJsRel = 'assets/pagejs/persediaan/pnm_brng/pnm_brng_list.js';
$pnmListJsAbs = (defined('FCPATH') ? FCPATH : '') . $pnmListJsRel;
$pnmListJsVer = is_file($pnmListJsAbs) ? filemtime($pnmListJsAbs) : date('YmdHis');
?>



<script type="application/javascript" src="<?= base_url('assets/pagejs/persediaan/pnm_brng/pnm_brng.js') ?>"></script>
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

<!-- JS khusus halaman list (modal laporan jurnal) -->
<script type="application/javascript" src="<?= base_url($pnmListJsRel) ?>?v=<?= $pnmListJsVer ?>"></script>
