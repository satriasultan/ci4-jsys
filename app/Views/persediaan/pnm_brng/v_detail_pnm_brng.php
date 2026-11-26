<?php
/* =====================================================================
 * DETAIL PENERIMAAN BARANG (I.Q.A.6) - READ ONLY
 * Sumber : sc_trx.pnm_brng_mst (header) + sc_trx.pnm_brng_dtl (detail)
 * Layout & jurnal mengikuti modul LPB (purchase/trans/detailLPB)
 * ===================================================================== */

$hdr  = (isset($dtldata) && is_array($dtldata)) ? $dtldata : [];
$rows = (isset($dtldetail) && is_array($dtldetail)) ? $dtldetail : [];

$val = function ($key, $default = '') use ($hdr) {
    return isset($hdr[$key]) ? trim((string) $hdr[$key]) : $default;
};

$num = function ($v, $dec = 2) {
    return number_format((float) $v, $dec, '.', ',');
};

$grandTotal = 0.0;
$totalQty   = 0.0;

foreach ($rows as $r) {
    $grandTotal += (float) ($r['valsum'] ?? 0);
    $totalQty   += (float) ($r['qty'] ?? 0);
}

$statusMap = [
    'F' => 'badge-info',       // FINAL USER
    'O' => 'badge-primary',    // FINAL TRANSACTION
    'P' => 'badge-success',    // CETAK/PRINT
    'A' => 'badge-primary',    // APPROVED
    'D' => 'badge-danger',     // DISAPPROVED
    'E' => 'badge-warning',    // REVISION/EDITING
    'I' => 'badge-secondary',  // DRAFT
];

$statusCode  = strtoupper($val('status'));
$statusClass = $statusMap[$statusCode] ?? 'badge-secondary';
$statusText  = $val('nmstatus') !== '' ? $val('nmstatus') : $statusCode;

$docno        = $val('docno');
$cabangText   = $val('nmbranch') !== '' ? $val('nmbranch') : $val('cabang');
$bagianText   = $val('nmcostcenter') !== '' ? $val('nmcostcenter') : $val('idcostcenter');
$pemohonText  = $val('nmpemohon') !== '' ? $val('nmpemohon') : $val('pemohon');
?>
<link rel="stylesheet" href="<?= base_url('assets/css/corp-form.css') ?>" />
<style>
    /* =========================================================
       LAYOUT - mengikuti detail LPB
    ========================================================= */
    .section-block {
        background-color: #f7f9fb;
        border-left: 4px solid #007bff;
        padding: 18px 20px;
        border-radius: 6px;
        margin-bottom: 18px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .section-header {
        font-weight: bold;
        color: #007bff;
        margin-bottom: 16px;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 8px;
    }

    #pnmDetailForm label {
        font-weight: 600;
        font-size: 12.5px;
        color: #495057;
        margin-bottom: 4px;
    }

    #pnmDetailForm .form-control:disabled,
    #pnmDetailForm .form-control[readonly] {
        background-color: #eef1f4;
        opacity: 1;
        color: #212529;
        font-weight: 600;
    }

    /* =========================================================
       TABEL DETAIL
    ========================================================= */
    #tabpnmdtl {
        width: 100% !important;
        margin-bottom: 0;
        font-size: 13px;
    }

    #tabpnmdtl thead th {
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #tabpnmdtl tbody td {
        vertical-align: middle;
        padding: 7px 8px;
    }

    #tabpnmdtl tbody td.amount {
        text-align: right;
        font-family: Consolas, "Courier New", monospace;
        white-space: nowrap;
    }

    #tabpnmdtl tfoot td {
        font-weight: bold;
        background-color: #eef1e9 !important;
        border-top: 2px solid #46533a !important;
    }

    #tabpnmdtl tfoot td.amount {
        text-align: right;
        font-family: Consolas, "Courier New", monospace;
    }

    /* =========================================================
       LAPORAN JURNAL - CORPORATE TABLE  (SAMA DENGAN LPB)
    ========================================================= */
    #modalLaporanJurnal .modal-content {
        border-radius: 4px;
        border: 1px solid #d6dce3;
    }

    #modalLaporanJurnal .modal-header {
        padding: 12px 20px;
    }

    #modalLaporanJurnal .modal-title {
        font-size: 16px;
        color: #343a40;
    }

    #tableLaporanJurnal {
        border-collapse: collapse !important;
        white-space: nowrap !important;
    }

    #tableLaporanJurnal thead th {
        background: #46533a;
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        vertical-align: middle;
        white-space: nowrap !important;
        border-color: #59664c;
        padding: 9px 10px;
    }

    #tableLaporanJurnal tbody td {
        font-size: 12px;
        vertical-align: middle;
        white-space: nowrap !important;
        padding: 8px 10px;
    }

    #tableLaporanJurnal tbody tr:hover {
        background-color: #f1f4f6 !important;
    }

    #tableLaporanJurnal td.text-right,
    #tableLaporanJurnal td.text-end {
        font-family: Consolas, "Courier New", monospace;
        font-weight: 500;
    }

    #tableLaporanJurnal tbody tr.row-total td {
        background-color: #eef1e9 !important;
        font-weight: bold;
        border-top: 2px solid #46533a !important;
    }

    #modalLaporanJurnal .dataTables_wrapper {
        font-size: 12px;
    }

    #modalLaporanJurnal .dataTables_filter input {
        border: 1px solid #ced4da;
        border-radius: 3px;
        height: 30px;
        padding: 5px 10px;
    }

    #modalLaporanJurnal .dataTables_length select {
        border: 1px solid #ced4da;
        border-radius: 3px;
        height: 30px;
    }

    #modalLaporanJurnal .dataTables_scrollBody {
        border-bottom: 1px solid #dee2e6 !important;
    }
</style>

<div class="corp-form-container">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo ucwords(strtolower(trim($title))); ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <div class="float-right" style="margin-right: 10px;vertical-align:middle;padding-top: 0.7%;"><i style="color:transparent;"><?php echo $t; ?></i> Menu ID <?php echo $version; ?></div>
                        <input type="hidden" id="classmenu" value="<?= str_replace('.', '_', $kodemenu) ?>" required>
                        <?php foreach ($y as $y1) { ?>
                            <?php if (trim($y1->kodemenu) != trim($kodemenu)) { ?>
                                <li class="breadcrumb-item"><a href="<?php echo base_url(trim($y1->linkmenu)); ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></a></li>
                            <?php } else { ?>
                                <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                            <?php } ?>
                        <?php } ?>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <?php echo $message; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-file-text-o"></i> Detail Penerimaan Barang</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body" id="pnmDetailForm">

                    <!-- ============ HEADER DOKUMEN ============ -->
                    <div class="section-block">
                        <div class="section-header">
                            <i class="fa fa-address-card"></i> Penerimaan Barang
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="docno">No. Transaksi</label>
                                    <input type="text" name="docno" id="docno" class="form-control"
                                           value="<?= esc($docno) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="docdate">Tanggal</label>
                                    <input type="text" name="docdate" id="docdate" class="form-control"
                                           value="<?= esc($val('docdate')) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="cabang">Cabang / Job</label>
                                    <input type="text" name="cabang" id="cabang" class="form-control"
                                           value="<?= esc($cabangText) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="idcostcenter">Bagian / Cost Center</label>
                                    <input type="text" name="idcostcenter" id="idcostcenter" class="form-control"
                                           value="<?= esc($bagianText) ?>" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="docref">Referensi</label>
                                    <input type="text" name="docref" id="docref" class="form-control"
                                           value="<?= esc($val('docref')) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="pemohon">Pemohon</label>
                                    <input type="text" name="pemohon" id="pemohon" class="form-control"
                                           value="<?= esc($pemohonText) ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label>Status</label>
                                    <div class="form-control" style="background-color:#eef1f4;">
                                        <span class="badge <?= $statusClass ?>" style="font-size:12px; padding:5px 8px;">
                                            <?= esc($statusText) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="totalHeader">Total</label>
                                    <input type="text" name="totalHeader" id="totalHeader"
                                           class="form-control text-end"
                                           value="<?= $num($grandTotal) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label for="description">Keterangan</label>
                                    <textarea name="description" id="description" class="form-control"
                                              rows="2" readonly><?= esc($val('description')) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============ END HEADER DOKUMEN ============ -->


                    <!-- ============ DETAIL BARANG ============ -->
                    <div class="card card-primary mt-3 mb-0">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-cubes"></i> Detail Barang
                            </h3>
                            <div class="float-right">
                                <span class="badge badge-light" style="font-size:12px;">
                                    <?= count($rows) ?> baris
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table id="tabpnmdtl" class="table table-bordered table-striped"
                                       style="width:100%;" cellspacing="0">
                                    <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="min-width:40px;">No</th>
                                        <th style="min-width:180px;">Gudang</th>
                                        <th style="min-width:140px;">ID Barang</th>
                                        <th style="min-width:260px;">Nama Barang</th>
                                        <th style="min-width:140px;">Batch / Spec</th>
                                        <th style="min-width:80px;">Satuan</th>
                                        <th class="text-end" style="min-width:110px;">Qty</th>
                                        <th class="text-end" style="min-width:120px;">Harga</th>
                                        <th class="text-end" style="min-width:150px;">Nilai</th>
                                        <th style="min-width:200px;">Keterangan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (count($rows) === 0): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                Tidak ada data detail untuk transaksi ini.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 0; ?>
                                        <?php foreach ($rows as $r): ?>
                                            <?php
                                            $no++;
                                            $gudang = trim((string) ($r['nmlocation'] ?? '')) !== ''
                                                ? trim((string) $r['nmlocation'])
                                                : trim((string) ($r['idlocation'] ?? ''));
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $no ?></td>
                                                <td><?= esc($gudang) ?></td>
                                                <td><?= esc(trim((string) ($r['idbarang'] ?? ''))) ?></td>
                                                <td><?= esc(trim((string) ($r['nmbarang'] ?? ''))) ?></td>
                                                <td><?= esc(trim((string) ($r['batch'] ?? ''))) ?></td>
                                                <td class="text-center"><?= esc(trim((string) ($r['unit'] ?? ''))) ?></td>
                                                <td class="amount"><?= $num($r['qty'] ?? 0) ?></td>
                                                <td class="amount"><?= $num($r['val'] ?? 0) ?></td>
                                                <td class="amount"><?= $num($r['valsum'] ?? 0) ?></td>
                                                <td><?= esc(trim((string) ($r['description'] ?? ''))) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end">GRAND TOTAL</td>
                                        <td class="amount"><?= $num($totalQty) ?></td>
                                        <td></td>
                                        <td class="amount"><?= $num($grandTotal) ?></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- ============ END DETAIL BARANG ============ -->

                </div>
                <!-- /.card-body -->

                <div class="card-footer bg-light">
                    <a href="<?= base_url('persediaan/trans/pnm_barang') ?>"
                       class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left mr-2"></i>
                        Kembali
                    </a>

                    <button type="button"
                            id="btnLaporanJurnal"
                            class="btn btn-primary btn-lg ms-2">
                        <i class="fa fa-book"></i> Laporan Jurnal Transaksi
                    </button>
                </div>
                <!-- /.card-footer -->

            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<!-- /.corp-form-container -->



<!-- =========================================================
     MODAL LAPORAN JURNAL TRANSAKSI (STRUKTUR & STYLE SAMA LPB)
========================================================== -->
<div class="modal fade"
     id="modalLaporanJurnal"
     tabindex="-1"
     aria-labelledby="modalLaporanJurnalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h5 class="modal-title" id="modalLaporanJurnalLabel">
                    <i class="fa fa-book"></i>
                    Laporan Jurnal Transaksi Penerimaan Barang
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                <div class="table-responsive">

                    <table id="tableLaporanJurnal"
                           class="table table-bordered table-striped table-hover w-100">

                        <thead>

                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Doc No</th>
                            <th>Cost Center</th>
                            <th>COA</th>
                            <th>Nama Perkiraan</th>
                            <th class="text-end">Debet</th>
                            <th class="text-end">Kredit</th>
                        </tr>

                        </thead>

                        <tbody></tbody>

                    </table>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

<?php
/* cache-busting JS detail (modal laporan jurnal) */
$pnmDetailJsRel = 'assets/pagejs/persediaan/pnm_brng/pnm_brng_detail.js';
$pnmDetailJsAbs = (defined('FCPATH') ? FCPATH : '') . $pnmDetailJsRel;
$pnmDetailJsVer = is_file($pnmDetailJsAbs) ? filemtime($pnmDetailJsAbs) : date('YmdHis');
?>
<script type="application/javascript" src="<?= base_url($pnmDetailJsRel) ?>?v=<?= $pnmDetailJsVer ?>"></script>
